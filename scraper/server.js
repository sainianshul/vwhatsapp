/**
 * ============================================================
 *  SMART SOCIAL MEDIA SCRAPER - Microservice v2
 * ============================================================
 *  Anti-detection features:
 *  - Puppeteer Stealth Plugin (bypasses headless detection)
 *  - User-Agent rotation (8 real browser UAs)
 *  - Random delays between every request (3-8 sec)
 *  - Random viewport sizes
 *  - Real browser headers
 *  - Uses mbasic.facebook.com (simpler HTML, less detection)
 *  - Rate limiting (max 1 concurrent search)
 *  - Multi-source search: Bing + Direct Facebook
 * ============================================================
 */

const express = require('express');
const puppeteer = require('puppeteer-extra');
const StealthPlugin = require('puppeteer-extra-plugin-stealth');

puppeteer.use(StealthPlugin());

const app = express();
app.use(express.json());

// ============================================================
//  ANTI-DETECTION: User Agent Pool
// ============================================================
const USER_AGENTS = [
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
    'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
    'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:127.0) Gecko/20100101 Firefox/127.0',
    'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:127.0) Gecko/20100101 Firefox/127.0',
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36 Edg/126.0.0.0',
    'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Safari/605.1.15',
    'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:127.0) Gecko/20100101 Firefox/127.0',
];

function getRandomUA() {
    return USER_AGENTS[Math.floor(Math.random() * USER_AGENTS.length)];
}

function randomDelay(minMs = 3000, maxMs = 8000) {
    const delay = Math.floor(Math.random() * (maxMs - minMs)) + minMs;
    console.log(`  [Delay] Waiting ${(delay / 1000).toFixed(1)}s...`);
    return new Promise(resolve => setTimeout(resolve, delay));
}

function randomViewport() {
    const screens = [
        { width: 1366, height: 768 },
        { width: 1440, height: 900 },
        { width: 1536, height: 864 },
        { width: 1920, height: 1080 },
        { width: 1280, height: 800 },
    ];
    return screens[Math.floor(Math.random() * screens.length)];
}

// ============================================================
//  RATE LIMITING
// ============================================================
let isSearching = false;

// ============================================================
//  BROWSER MANAGEMENT
// ============================================================
async function createBrowser() {
    return puppeteer.launch({
        headless: 'new',
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--disable-gpu',
            '--disable-web-security',
            '--single-process',
            '--no-zygote',
        ],
        executablePath: process.env.PUPPETEER_EXECUTABLE_PATH || undefined,
    });
}

async function createPage(browser) {
    const page = await browser.newPage();
    const vp = randomViewport();
    await page.setViewport(vp);
    await page.setUserAgent(getRandomUA());
    await page.setExtraHTTPHeaders({
        'Accept-Language': 'en-US,en;q=0.9',
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
    });
    return page;
}

// ============================================================
//  HELPER: Extract clean Facebook URLs from text/links
// ============================================================
function extractFbUrls(urls) {
    const cleanUrls = new Set();
    for (let url of urls) {
        // Clean URL
        url = url.replace(/&amp;/g, '&');
        
        // Extract facebook.com URL from redirect URLs
        const fbMatch = url.match(/(https?:\/\/(?:www\.|m\.|mbasic\.|web\.)?facebook\.com\/[^\s&"'<>]+)/i);
        if (fbMatch) {
            let fbUrl = fbMatch[1].split('?')[0].split('#')[0]; // Strip params
            fbUrl = fbUrl.replace(/\/+$/, ''); // Strip trailing slash
            
            // Skip non-page URLs
            if (/\/(posts|photos|videos|stories|events|reels|watch|groups|marketplace|gaming|help)\//i.test(fbUrl)) continue;
            if (fbUrl === 'https://www.facebook.com' || fbUrl === 'https://facebook.com') continue;
            
            // Normalize to www
            fbUrl = fbUrl.replace('m.facebook.com', 'www.facebook.com')
                         .replace('mbasic.facebook.com', 'www.facebook.com')
                         .replace('web.facebook.com', 'www.facebook.com');
            
            cleanUrls.add(fbUrl);
        }
    }
    return [...cleanUrls];
}

// ============================================================
//  SEARCH SOURCE 1: Bing
// ============================================================
async function searchBing(page, query) {
    console.log(`  [Bing] Searching...`);
    const searchUrl = `https://www.bing.com/search?q=${encodeURIComponent(query + ' facebook page')}&count=20`;
    
    try {
        await page.goto(searchUrl, { waitUntil: 'domcontentloaded', timeout: 30000 });
        await randomDelay(2000, 4000);

        const results = await page.evaluate(() => {
            const urls = [];
            // Bing result links
            document.querySelectorAll('#b_results .b_algo a, #b_results a').forEach(a => {
                const href = a.href || '';
                if (href.includes('facebook.com')) {
                    urls.push(href);
                }
            });
            // Also check cite elements for display URLs
            document.querySelectorAll('cite').forEach(cite => {
                const text = cite.textContent || '';
                if (text.includes('facebook.com')) {
                    urls.push('https://' + text.split(' ')[0]);
                }
            });
            return urls;
        });

        console.log(`  [Bing] Found ${results.length} raw Facebook links`);
        return results;
    } catch (e) {
        console.log(`  [Bing] Failed: ${e.message}`);
        return [];
    }
}

// ============================================================
//  SEARCH SOURCE 2: Google
// ============================================================
async function searchGoogle(page, query) {
    console.log(`  [Google] Searching...`);
    const searchUrl = `https://www.google.com/search?q=${encodeURIComponent(query + ' site:facebook.com')}&num=15`;
    
    try {
        await page.setUserAgent(getRandomUA());
        await page.goto(searchUrl, { waitUntil: 'domcontentloaded', timeout: 30000 });
        await randomDelay(2000, 4000);

        const results = await page.evaluate(() => {
            const urls = [];
            // Google result links
            document.querySelectorAll('a[href]').forEach(a => {
                const href = a.href || '';
                if (href.includes('facebook.com') && !href.includes('google.com')) {
                    urls.push(href);
                }
            });
            // Check cite elements  
            document.querySelectorAll('cite').forEach(cite => {
                const text = cite.textContent || '';
                if (text.includes('facebook.com')) {
                    let url = text.replace(/\s.*/, '');
                    if (!url.startsWith('http')) url = 'https://' + url;
                    urls.push(url);
                }
            });
            return urls;
        });

        console.log(`  [Google] Found ${results.length} raw Facebook links`);
        return results;
    } catch (e) {
        console.log(`  [Google] Failed: ${e.message}`);
        return [];
    }
}

// ============================================================
//  SEARCH SOURCE 3: Direct known Facebook URL patterns
// ============================================================
function generateDirectFbUrls(query) {
    // Generate likely Facebook page URLs from the person name
    const slug = query.toLowerCase().replace(/[^a-z0-9\s]/g, '').replace(/\s+/g, '');
    const slugDot = query.toLowerCase().replace(/[^a-z0-9\s]/g, '').replace(/\s+/g, '.');
    const slugDash = query.toLowerCase().replace(/[^a-z0-9\s]/g, '').replace(/\s+/g, '-');
    
    return [
        `https://www.facebook.com/${slug}`,
        `https://www.facebook.com/${slugDot}`,
        `https://www.facebook.com/${slugDash}`,
    ];
}

// ============================================================
//  PHASE 2: Visit Facebook pages and extract details
// ============================================================
async function extractPageDetails(page, fbUrl) {
    // Use www.facebook.com (NOT mbasic — it requires login now)
    const wwwUrl = fbUrl
        .replace('mbasic.facebook.com', 'www.facebook.com')
        .replace('m.facebook.com', 'www.facebook.com')
        .replace('web.facebook.com', 'www.facebook.com');
    
    try {
        await page.setUserAgent(getRandomUA());
        await page.goto(wwwUrl, { waitUntil: 'networkidle2', timeout: 30000 });
        await randomDelay(1500, 3000);

        const pageData = await page.evaluate((origUrl) => {
            const title = document.title || '';
            const pageName = title
                .replace(/\s*\|\s*Facebook/i, '')
                .replace(/\s*-\s*Facebook/i, '')
                .replace(/\s*–\s*Facebook/i, '')
                .trim();

            // Skip login/error pages
            if (!pageName || pageName === 'Facebook' || pageName.includes('Log in') || pageName.includes('log in')) {
                return null;
            }

            const bodyText = document.body ? document.body.innerText : '';

            // Extract followers count (e.g. "58M followers" or "1.2K followers")
            const followMatch = bodyText.match(/([\d,.]+[KMB]?)\s*followers/i);
            const followers = followMatch ? followMatch[1].trim() : null;

            // Extract likes count if followers not found
            const likesMatch = !followers ? bodyText.match(/([\d,]+)\s*likes/i) : null;
            const likes = likesMatch ? likesMatch[1].trim() : null;

            // Meta description (has best info on FB pages)
            const metaDesc = document.querySelector('meta[name="description"]');
            const description = metaDesc ? metaDesc.content : '';

            // Extract profile picture from og:image meta tag
            const ogImage = document.querySelector('meta[property="og:image"]');
            const profilePic = ogImage ? ogImage.content : null;

            // Detect account type from body text
            let accountType = 'Page';
            const lowerBody = bodyText.toLowerCase();
            if (lowerBody.includes('politician') || lowerBody.includes('political')) accountType = 'Politician';
            else if (lowerBody.includes('public figure')) accountType = 'Public Figure';
            else if (lowerBody.includes('community')) accountType = 'Community Page';
            else if (lowerBody.includes('government')) accountType = 'Government';
            else if (lowerBody.includes('news') || lowerBody.includes('media')) accountType = 'News/Media';
            else if (lowerBody.includes('fan page') || lowerBody.includes('interest')) accountType = 'Fan Page';

            return {
                name: pageName,
                url: origUrl,
                description: description.substring(0, 300),
                followers: followers || likes || null,
                profilePic: profilePic,
                accountType: accountType,
            };
        }, wwwUrl);

        return pageData;
    } catch (e) {
        console.log(`    [SKIP] ${wwwUrl}: ${e.message}`);
        return null;
    }
}

// ============================================================
//  MAIN SEARCH FUNCTION
// ============================================================
async function searchFacebook(query) {
    const browser = await createBrowser();
    const results = [];

    try {
        console.log(`\n[SEARCH] ========================================`);
        console.log(`[SEARCH] Query: "${query}"`);

        // ----- PHASE 1: Collect Facebook URLs from multiple sources -----
        console.log(`[SEARCH] Phase 1: Collecting Facebook URLs...`);
        
        const page = await createPage(browser);
        let allRawUrls = [];

        // Source 1: Bing search
        const bingUrls = await searchBing(page, query);
        allRawUrls = allRawUrls.concat(bingUrls);

        // Source 2: Google search (if Bing gave few results)
        if (allRawUrls.length < 5) {
            await randomDelay(2000, 4000);
            const googleUrls = await searchGoogle(page, query);
            allRawUrls = allRawUrls.concat(googleUrls);
        }

        // Source 3: Direct URL guesses
        const directUrls = generateDirectFbUrls(query);
        allRawUrls = allRawUrls.concat(directUrls);

        // Clean and deduplicate
        const cleanUrls = extractFbUrls(allRawUrls);
        console.log(`[SEARCH] Total unique Facebook URLs found: ${cleanUrls.length}`);
        cleanUrls.forEach(u => console.log(`    ${u}`));

        // ----- PHASE 2: Visit each Facebook page for details -----
        console.log(`[SEARCH] Phase 2: Extracting page details...`);

        for (const fbUrl of cleanUrls.slice(0, 12)) {
            console.log(`  [Visit] ${fbUrl}`);
            await randomDelay(3000, 6000);
            
            const pageData = await extractPageDetails(page, fbUrl);
            
            if (pageData) {
                results.push(pageData);
                console.log(`    [OK] ${pageData.name} (${pageData.accountType}) - ${pageData.followers || 'N/A'} followers`);
            }
        }

        await page.close();

        console.log(`[SEARCH] ========================================`);
        console.log(`[SEARCH] Done. Returning ${results.length} results.\n`);

    } catch (e) {
        console.error(`[FATAL] Search failed: ${e.message}`);
        throw e;
    } finally {
        await browser.close();
    }

    return results;
}

// ============================================================
//  DEEP SCRAPE LOGIC — Cookie-Powered for Best Results
// ============================================================
async function deepScrapeFacebook(fbUrl, fbCookiesJson) {
    const browser = await createBrowser();
    const results = { posts: [], metadata: {} };

    try {
        const page = await createPage(browser);
        await page.setViewport({ width: 1366, height: 900 });
        await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36');

        if (fbCookiesJson) {
            try {
                const cookies = JSON.parse(fbCookiesJson);
                await page.setCookie(...cookies);
                console.log(`[DEEP-SCRAPE] Session cookies injected`);
            } catch (e) {
                console.log(`[DEEP-SCRAPE] No valid cookies, scraping as guest`);
            }
        }

        // Normalize to mbasic.facebook.com for stable pagination
        let targetUrl;
        try {
            const urlObj = new URL(fbUrl);
            urlObj.hostname = 'mbasic.facebook.com';
            urlObj.searchParams.set('v', 'timeline'); // Force timeline view
            targetUrl = urlObj.toString();
        } catch (e) {
            targetUrl = fbUrl;
        }

        console.log(`[DEEP-SCRAPE] Visiting ${targetUrl}`);
        await page.goto(targetUrl, { waitUntil: 'networkidle2', timeout: 60000 });
        await randomDelay(2000, 3000);

        results.metadata.title = await page.title();
        const seenKeys = new Set();
        
        // DUMP HTML FOR DEBUGGING
        const html = await page.content();
        const fs = require('fs');
        fs.writeFileSync('mbasic_dump.html', html);
        console.log(`[DEEP-SCRAPE] Dumped HTML to mbasic_dump.html`);
        
        // Loop to paginate (fetch more posts) up to 7 pages
        for (let pageNum = 0; pageNum < 7; pageNum++) {
            console.log(`[DEEP-SCRAPE] Extracting page ${pageNum + 1}...`);
            
            const newPosts = await page.evaluate(() => {
                const posts = [];
                // Look for elements that could be posts (usually links containing 'story_fbid' or 'Full Story')
                const links = Array.from(document.querySelectorAll('a[href]'));
                const storyLinks = links.filter(a => 
                    (a.innerText.trim() === 'Full Story' || a.href.includes('story_fbid=') || a.href.includes('/story.php?')) &&
                    !a.href.includes('/login/') && !a.href.includes('composer') && !a.href.includes('reaction')
                );

                storyLinks.forEach(link => {
                    let container = link;
                    // Go up a few levels to capture the whole post container
                    for (let j = 0; j < 5; j++) {
                        if (container.parentElement && container.parentElement.tagName !== 'BODY') {
                            container = container.parentElement;
                            // Assume we found the container if it has substantial text
                            if (container.innerText.length > 50) break;
                        }
                    }

                    const content = container.innerText.trim();
                    if (content.length < 20) return; // Skip non-posts
                    if (content.includes('Forgot Password') || content.includes('Create New Account')) return;

                    let postUrl = link.href;
                    // Convert mbasic url back to www url for the user interface
                    postUrl = postUrl.replace('mbasic.facebook.com', 'www.facebook.com');

                    // Clean URL
                    try {
                        const u = new URL(postUrl);
                        u.hash = '';
                        postUrl = u.toString();
                    } catch(e) {}

                    // Extract image if any
                    const images = Array.from(container.querySelectorAll('img'));
                    // Find the largest image (ignore tiny icons)
                    const scoredImgs = images
                        .map(img => {
                            const rect = img.getBoundingClientRect();
                            return { src: img.currentSrc || img.src, score: rect.width * rect.height, w: rect.width, h: rect.height };
                        })
                        .filter(i => i.src && i.w > 40 && i.h > 40 && !i.src.includes('static'))
                        .sort((a, b) => b.score - a.score);
                    const mediaUrl = scoredImgs[0]?.src || null;

                    // Extract author (usually the first link)
                    const authorLink = container.querySelector('a');
                    const authorName = authorLink ? authorLink.innerText.trim() : '';

                    // Simple metrics parsing
                    let reactions = 0, commentsCount = 0;
                    const allText = content.toLowerCase();
                    const commentMatch = allText.match(/([\d,]+)\s*comments?/i);
                    if (commentMatch) commentsCount = parseInt(commentMatch[1].replace(/,/g, '')) || 0;
                    
                    posts.push({
                        content: content,
                        post_url: postUrl,
                        media_url: mediaUrl,
                        author_name: authorName,
                        reactions_count: reactions,
                        comments_count: commentsCount
                    });
                });
                return posts;
            });

            // Process and deduplicate
            let addedThisPage = 0;
            for (const p of newPosts) {
                // Use a short snippet as a deduplication key
                const key = p.content.slice(0, 100);
                if (!seenKeys.has(key)) {
                    seenKeys.add(key);
                    addedThisPage++;
                    
                    let fbPostId = '';
                    try {
                        const u = new URL(p.post_url);
                        fbPostId = u.searchParams.get('story_fbid') || u.searchParams.get('fbid') || '';
                        if (!fbPostId) {
                            const m = u.pathname.match(/\/(?:posts|videos|photos|permalink|story.php)\/([^/?#]+)/i);
                            fbPostId = m ? m[1] : '';
                        }
                    } catch(e){}
                    if (!fbPostId) fbPostId = btoa(p.post_url).substring(0, 15);

                    results.posts.push({
                        fb_post_id: fbPostId,
                        post_url: p.post_url,
                        post_type: p.media_url ? 'photo' : 'text',
                        content: p.content.slice(0, 2000),
                        media_url: p.media_url,
                        author_name: p.author_name,
                        time_text: 'Recent',
                        posted_at: new Date().toISOString(),
                        reactions_count: p.reactions_count,
                        comments_count: p.comments_count,
                        shares_count: 0,
                        comments: []
                    });
                }
            }

            console.log(`[DEEP-SCRAPE] Found ${addedThisPage} new posts. Total unique posts: ${results.posts.length}`);
            if (results.posts.length >= 60) break; // Stop if we have enough posts

            // Find the "Show more" link to go to the next page
            const nextUrl = await page.evaluate(() => {
                const aTags = Array.from(document.querySelectorAll('a'));
                const nextBtn = aTags.find(a => {
                    const t = a.innerText.toLowerCase();
                    return t.includes('show more') || t === 'more' || (a.href && a.href.includes('cursor='));
                });
                return nextBtn ? nextBtn.href : null;
            });

            if (nextUrl) {
                console.log(`[DEEP-SCRAPE] Moving to next page...`);
                await page.goto(nextUrl, { waitUntil: 'networkidle2', timeout: 30000 });
                await randomDelay(2000, 4000);
            } else {
                console.log(`[DEEP-SCRAPE] No more pages found.`);
                break;
            }
        }

    } catch (e) {
        console.error(`[DEEP-SCRAPE] Failed: ${e.message}`);
        throw e;
    } finally {
        await browser.close();
    }

    return results;
}

// ============================================================
//  API ENDPOINTS
// ============================================================
app.get('/health', (req, res) => {
    res.json({ status: 'ok', service: 'social-scraper-v2', busy: isSearching });
});

app.post('/api/search', async (req, res) => {
    if (isSearching) {
        return res.status(429).json({
            success: false,
            error: 'A search is already in progress. Please wait.',
        });
    }

    const { query } = req.body;
    if (!query || query.trim().length < 2) {
        return res.status(400).json({ success: false, error: 'Query too short' });
    }

    isSearching = true;
    console.log(`\n🔍 API Request: Search for "${query}"`);

    try {
        const results = await searchFacebook(query.trim());
        res.json({ success: true, query: query.trim(), results });
    } catch (error) {
        console.error(`❌ API Error: ${error.message}`);
        res.status(500).json({ success: false, error: error.message });
    } finally {
        isSearching = false;
    }
});

app.post('/api/deep-scrape', async (req, res) => {
    const { url, fb_cookies } = req.body;
    if (!url || !url.includes('facebook.com')) {
        return res.status(400).json({ success: false, error: 'Invalid Facebook URL' });
    }

    console.log(`\n🔍 API Request: Deep Scrape for "${url}"`);

    try {
        const results = await deepScrapeFacebook(url, fb_cookies || null);
        res.json({ success: true, url, data: results });
    } catch (error) {
        console.error(`❌ API Error: ${error.message}`);
        res.status(500).json({ success: false, error: error.message });
    }
});


// ============================================================
//  AUTO-COMMENT LOGIC (USING COOKIES + www.facebook.com)
//  Approach: Use www.facebook.com desktop site (like scrap_code.js)
//  for commenting. If that fails, fallback to mbasic.
// ============================================================
async function postFacebookComment(postUrl, commentText, fbCookiesJson) {
    const browser = await createBrowser();
    
    try {
        const page = await createPage(browser);
        await page.setViewport({ width: 1366, height: 900 });
        await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36');
        
        console.log(`[AUTO-COMMENT] Injecting session cookies...`);
        
        let cookies;
        try {
            cookies = JSON.parse(fbCookiesJson);
            await page.setCookie(...cookies);
        } catch (e) {
            throw new Error('Invalid JSON format for cookies. Please re-export and save them.');
        }
        
        // STRATEGY 1: Try mbasic.facebook.com first (simpler, more reliable form)
        console.log(`[AUTO-COMMENT] Strategy 1: Trying mbasic.facebook.com...`);
        let mbasicUrl;
        try {
            const urlObj = new URL(postUrl);
            urlObj.hostname = 'mbasic.facebook.com';
            mbasicUrl = urlObj.toString();
        } catch (e) {
            mbasicUrl = postUrl;
        }
        
        await page.goto(mbasicUrl, { waitUntil: 'networkidle2', timeout: 30000 });
        await randomDelay(2000, 4000);
        
        // Check if redirected to login
        const currentUrl = page.url();
        if (currentUrl.includes('/login') || currentUrl.includes('login.php')) {
            throw new Error('Session cookies have expired or are invalid. Please update the cookies for this bot account.');
        }
        
        const commentBoxStr = 'textarea[name="comment_text"], input[name="comment_text"]';
        let commentBox = await page.$(commentBoxStr);
        
        if (commentBox) {
            // mbasic works! Type and submit
            console.log(`[AUTO-COMMENT] Found mbasic comment box, typing...`);
            await page.type(commentBoxStr, commentText, { delay: Math.floor(Math.random() * 100) + 100 });
            await randomDelay(1500, 3000);
            
            console.log(`[AUTO-COMMENT] Submitting via mbasic...`);
            const submitBtn = await page.$('input[value="Comment"], input[value="Post"], button[value="Comment"], input[type="submit"]');
            if (submitBtn) {
                await Promise.all([
                    page.waitForNavigation({ waitUntil: 'networkidle2', timeout: 30000 }).catch(() => null),
                    submitBtn.click()
                ]);
                console.log(`[AUTO-COMMENT] ✅ Comment posted via mbasic!`);
                return true;
            }
        }
        
        // STRATEGY 2: Try www.facebook.com desktop approach (like scrap_code.js)
        console.log(`[AUTO-COMMENT] Strategy 2: Trying www.facebook.com desktop...`);
        
        let wwwUrl;
        try {
            const urlObj = new URL(postUrl);
            urlObj.hostname = 'www.facebook.com';
            wwwUrl = urlObj.toString();
        } catch (e) {
            wwwUrl = postUrl;
        }

        // Re-inject cookies for www domain
        await page.setCookie(...cookies);
        await page.goto(wwwUrl, { waitUntil: 'networkidle2', timeout: 60000 });
        
        // Wait for FB content to load
        await page.waitForFunction(
            () => document.body && document.body.innerText && document.body.innerText.trim().length > 500,
            { timeout: 20000 }
        ).catch(() => null);
        await randomDelay(3000, 5000);

        // Scroll down to make comment section visible
        await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight - 300));
        await randomDelay(2000, 3000);

        // Step 1: Click on comment icon to open comment box
        const commentIconClicked = await page.evaluate(async () => {
            const commentIcon = Array.from(document.querySelectorAll('div[role="button"]')).find(el => {
                const aria = el.getAttribute('aria-label') || '';
                return aria.toLowerCase().includes('comment') || aria.toLowerCase().includes('reply');
            });
            if (commentIcon) {
                commentIcon.click();
                await new Promise(r => setTimeout(r, 3000));
                return true;
            }
            return false;
        });
        
        if (commentIconClicked) {
            console.log(`[AUTO-COMMENT] Clicked comment icon`);
        }
        await randomDelay(2000, 3000);

        // Step 2: Find the comment textbox
        const commentBoxFound = await page.evaluate(async () => {
            const textboxes = Array.from(document.querySelectorAll('div[role="textbox"][contenteditable="true"], textarea'));
            const commentBox = textboxes.find(el => {
                const aria = (el.getAttribute('aria-label') || '').toLowerCase();
                const placeholder = (el.getAttribute('aria-placeholder') || el.getAttribute('placeholder') || '').toLowerCase();
                const text = `${aria} ${placeholder}`;
                return text.includes('comment') || text.includes('reply') || text.includes('write');
            }) || textboxes.at(-1);

            if (!commentBox) return false;
            
            commentBox.scrollIntoView({ block: 'center' });
            commentBox.focus();
            commentBox.click();
            return true;
        });

        if (!commentBoxFound) {
            throw new Error('Comment box not found on both mbasic and desktop. The post might be private, comments disabled, or cookies expired.');
        }

        // Step 3: Type the comment
        console.log(`[AUTO-COMMENT] Found desktop comment box, typing...`);
        await page.keyboard.type(commentText, { delay: 20 });
        await randomDelay(1500, 3000);

        // Step 4: Submit — try Enter, then Ctrl+Enter, then click send button
        console.log(`[AUTO-COMMENT] Submitting via desktop...`);
        
        // Try pressing Enter first
        await page.keyboard.press('Enter');
        await randomDelay(3000, 5000);

        // Verify if comment was posted
        const posted = await page.evaluate((text) => {
            const textboxes = Array.from(document.querySelectorAll('div[role="textbox"][contenteditable="true"], textarea'));
            // If the textbox is now empty, the comment was likely submitted
            const activeBox = textboxes.find(el => {
                const aria = (el.getAttribute('aria-label') || '').toLowerCase();
                const placeholder = (el.getAttribute('aria-placeholder') || el.getAttribute('placeholder') || '').toLowerCase();
                return aria.includes('comment') || placeholder.includes('comment');
            }) || textboxes.at(-1);
            
            if (!activeBox) return true; // Box gone = submitted
            const boxText = (activeBox.innerText || activeBox.value || '').trim();
            return !boxText.includes(text); // If text is gone, it was submitted
        }, commentText);

        if (posted) {
            console.log(`[AUTO-COMMENT] ✅ Comment posted via desktop!`);
            return true;
        }

        // Try Ctrl+Enter as fallback
        await page.keyboard.down('Control');
        await page.keyboard.press('Enter');
        await page.keyboard.up('Control');
        await randomDelay(3000, 5000);

        console.log(`[AUTO-COMMENT] ✅ Comment submitted (desktop fallback)!`);
        return true;
        
    } catch (e) {
        console.error(`[AUTO-COMMENT] ❌ Failed: ${e.message}`);
        throw e;
    } finally {
        await browser.close();
    }
}

// ============================================================
//  COMMENT API ENDPOINT
// ============================================================
app.post('/api/post-comment', async (req, res) => {
    const { post_url, comment_text, fb_cookies } = req.body;
    
    if (!post_url || !comment_text || !fb_cookies) {
        return res.status(400).json({ success: false, error: 'Missing required fields (post_url, comment_text, fb_cookies)' });
    }

    try {
        await postFacebookComment(post_url, comment_text, fb_cookies);
        res.json({ success: true, message: 'Comment posted successfully' });
    } catch (error) {
        res.status(500).json({ success: false, error: error.message });
    }
});

// ============================================================
//  AUTO-LIKE LOGIC (USING COOKIES + mbasic.facebook.com)
// ============================================================
async function likeFacebookPost(postUrl, fbCookiesJson) {
    const browser = await createBrowser();
    
    try {
        const page = await createPage(browser);
        await page.setViewport({ width: 1366, height: 900 });
        await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36');
        
        console.log(`[AUTO-LIKE] Injecting session cookies...`);
        
        let cookies;
        try {
            cookies = JSON.parse(fbCookiesJson);
            await page.setCookie(...cookies);
        } catch (e) {
            throw new Error('Invalid JSON format for cookies. Please re-export and save them.');
        }
        
        // STRATEGY 1: Use mbasic for liking, it's the fastest and most reliable
        console.log(`[AUTO-LIKE] Trying mbasic.facebook.com...`);
        let mbasicUrl;
        try {
            const urlObj = new URL(postUrl);
            urlObj.hostname = 'mbasic.facebook.com';
            mbasicUrl = urlObj.toString();
        } catch (e) {
            mbasicUrl = postUrl;
        }
        
        await page.goto(mbasicUrl, { waitUntil: 'networkidle2', timeout: 30000 });
        await randomDelay(2000, 4000);
        
        const currentUrl = page.url();
        if (currentUrl.includes('/login') || currentUrl.includes('login.php')) {
            throw new Error('Session cookies have expired or are invalid. Please update the cookies for this bot account.');
        }

        // Check if already liked (the link text would say "Unlike")
        const isLiked = await page.evaluate(() => {
            const links = Array.from(document.querySelectorAll('a'));
            return links.some(a => a.innerText.trim() === 'Unlike');
        });

        if (isLiked) {
            console.log(`[AUTO-LIKE] Post is already liked!`);
            return true;
        }

        // Find the Like link (either by text 'Like' or href containing 'like.php')
        const likeSuccess = await page.evaluate(async () => {
            const links = Array.from(document.querySelectorAll('a'));
            const likeLink = links.find(a => {
                const txt = a.innerText.trim();
                const href = a.href || '';
                return txt === 'Like' || href.includes('/a/like.php');
            });
            
            if (likeLink) {
                likeLink.click();
                return true;
            }
            return false;
        });
        
        if (likeSuccess) {
            console.log(`[AUTO-LIKE] Clicked Like link!`);
            await page.waitForNavigation({ waitUntil: 'networkidle2', timeout: 10000 }).catch(() => null);
            console.log(`[AUTO-LIKE] ✅ Post liked successfully via mbasic!`);
            return true;
        }

        // STRATEGY 2: Desktop fallback
        console.log(`[AUTO-LIKE] Strategy 2: Trying www.facebook.com desktop...`);
        let wwwUrl;
        try {
            const urlObj = new URL(postUrl);
            urlObj.hostname = 'www.facebook.com';
            wwwUrl = urlObj.toString();
        } catch (e) {
            wwwUrl = postUrl;
        }

        await page.goto(wwwUrl, { waitUntil: 'networkidle2', timeout: 60000 });
        await randomDelay(3000, 5000);

        // Click on Like icon
        const desktopLikeSuccess = await page.evaluate(async () => {
            const likeBtn = Array.from(document.querySelectorAll('div[role="button"]')).find(el => {
                const aria = (el.getAttribute('aria-label') || '').toLowerCase();
                // Facebook desktop like button aria label is often just "Like" or "Like reaction"
                return aria === 'like' || aria === 'like reaction' || aria === 'remove like';
            });
            if (likeBtn) {
                const aria = (likeBtn.getAttribute('aria-label') || '').toLowerCase();
                if (aria === 'remove like') return 'already_liked'; // Already liked
                
                likeBtn.click();
                return true;
            }
            return false;
        });

        if (desktopLikeSuccess === 'already_liked') {
            console.log(`[AUTO-LIKE] Post is already liked (Desktop)!`);
            return true;
        }

        if (desktopLikeSuccess) {
            console.log(`[AUTO-LIKE] ✅ Post liked successfully via desktop!`);
            await randomDelay(2000, 3000);
            return true;
        }
        
        throw new Error('Like button not found. The post might be private, restricted, or cookies expired.');
        
    } catch (e) {
        console.error(`[AUTO-LIKE] ❌ Failed: ${e.message}`);
        throw e;
    } finally {
        await browser.close();
    }
}

// ============================================================
//  LIKE API ENDPOINT
// ============================================================
app.post('/api/like-post', async (req, res) => {
    const { post_url, fb_cookies } = req.body;
    
    if (!post_url || !fb_cookies) {
        return res.status(400).json({ success: false, error: 'Missing required fields (post_url, fb_cookies)' });
    }

    try {
        await likeFacebookPost(post_url, fb_cookies);
        res.json({ success: true, message: 'Post liked successfully' });
    } catch (error) {
        res.status(500).json({ success: false, error: error.message });
    }
});

// ============================================================
//  BOT HEALTH CHECK API
// ============================================================
app.post('/api/health-check', async (req, res) => {
    const { platform, fb_cookies } = req.body;
    
    if (!fb_cookies) {
        return res.status(400).json({ success: false, error: 'Missing required fields (fb_cookies)' });
    }

    try {
        const result = await checkFacebookHealth(fb_cookies);
        res.json({ success: true, status: result.status, message: result.message });
    } catch (error) {
        res.status(500).json({ success: false, error: error.message });
    }
});

async function checkFacebookHealth(fbCookiesJson) {
    const browser = await createBrowser();
    try {
        const page = await createPage(browser);
        await page.setViewport({ width: 1366, height: 900 });
        await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36');
        
        let cookies;
        try {
            cookies = JSON.parse(fbCookiesJson);
            await page.setCookie(...cookies);
        } catch (e) {
            throw new Error('Invalid JSON format for cookies.');
        }
        
        // Use mbasic to check login status
        await page.goto('https://mbasic.facebook.com/', { waitUntil: 'networkidle2', timeout: 30000 });
        
        const currentUrl = page.url();
        const contentText = await page.evaluate(() => document.body.innerText.toLowerCase());
        
        if (currentUrl.includes('/login') || currentUrl.includes('login.php')) {
            return { status: 'expired', message: 'Cookies expired or invalid — redirected to login page.' };
        }
        
        if (contentText.includes('account restricted') || contentText.includes('checkpoint') || currentUrl.includes('checkpoint')) {
            return { status: 'restricted', message: 'Account is restricted, locked, or at a checkpoint.' };
        }
        
        return { status: 'active', message: 'Account is active and cookies are valid.' };
    } finally {
        if (browser) {
            await browser.close().catch(e => console.error("Error closing browser:", e));
        }
    }
}

// ============================================================
//  GRAPHQL INTERCEPT SCRAPER — Apify-Level Data Quality
//  Strategy: Load FB page with cookies, intercept internal
//  GraphQL API responses, extract clean structured post data.
// ============================================================

/**
 * Recursively search a deeply nested object for post-like nodes.
 * Facebook's GraphQL responses are deeply nested and structure varies.
 * We look for objects that have 'creation_time' (unique to posts/stories).
 */
function findPostNodes(obj, depth = 0, results = []) {
    if (depth > 25 || !obj || typeof obj !== 'object') return results;

    // Check if this object looks like a post
    if (obj.creation_time && typeof obj.creation_time === 'number') {
        results.push(obj);
        return results; // Don't search deeper in this branch
    }

    // Recurse into arrays and objects
    if (Array.isArray(obj)) {
        for (const item of obj) {
            findPostNodes(item, depth + 1, results);
        }
    } else {
        for (const key of Object.keys(obj)) {
            findPostNodes(obj[key], depth + 1, results);
        }
    }

    return results;
}

/**
 * Extract clean post data from a raw GraphQL post node.
 * Handles multiple Facebook GraphQL schema variations.
 */
function extractPostFromNode(node) {
    try {
        // --- Post ID ---
        const postId = node.post_id || node.id || node.story_id || null;

        // --- Post Text/Message ---
        let message = '';
        if (node.message && node.message.text) {
            message = node.message.text;
        } else if (node.comet_sections?.content?.story?.message?.text) {
            message = node.comet_sections.content.story.message.text;
        } else if (typeof node.message === 'string') {
            message = node.message;
        }

        // --- Creation Time ---
        const creationTime = node.creation_time 
            ? new Date(node.creation_time * 1000).toISOString() 
            : null;

        // --- URL ---
        let postUrl = node.url || node.permalink_url || '';
        if (node.comet_sections?.context_layout?.story?.url) {
            postUrl = node.comet_sections.context_layout.story.url;
        }
        // Ensure it's a full URL
        if (postUrl && !postUrl.startsWith('http')) {
            postUrl = 'https://www.facebook.com' + postUrl;
        }

        // --- Reactions ---
        let reactions = 0;
        if (node.feedback?.reactors?.count !== undefined) {
            reactions = node.feedback.reactors.count;
        } else if (node.feedback?.reaction_count?.count !== undefined) {
            reactions = node.feedback.reaction_count.count;
        } else if (node.feedback?.i18n_reaction_count) {
            const r = node.feedback.i18n_reaction_count.replace(/[^0-9]/g, '');
            reactions = parseInt(r) || 0;
        } else if (node.reaction_count?.count !== undefined) {
            reactions = node.reaction_count.count;
        }

        // --- Comments Count ---
        let commentsCount = 0;
        if (node.feedback?.comment_count?.total_count !== undefined) {
            commentsCount = node.feedback.comment_count.total_count;
        } else if (node.feedback?.total_comment_count !== undefined) {
            commentsCount = node.feedback.total_comment_count;
        } else if (node.comment_count?.total_count !== undefined) {
            commentsCount = node.comment_count.total_count;
        }

        // --- Shares Count ---
        let sharesCount = 0;
        if (node.feedback?.share_count?.count !== undefined) {
            sharesCount = node.feedback.share_count.count;
        } else if (node.feedback?.reshare_count?.count !== undefined) {
            sharesCount = node.feedback.reshare_count.count;
        } else if (node.share_count?.count !== undefined) {
            sharesCount = node.share_count.count;
        }

        // --- Media/Images ---
        let mediaUrl = null;
        let postType = 'text';

        // Search for image in attachments
        const attachments = node.attachments || 
                           node.comet_sections?.content?.story?.attachments || 
                           [];
        
        if (Array.isArray(attachments)) {
            for (const att of attachments) {
                // Check for image
                const imgUri = att?.media?.image?.uri || 
                              att?.media?.photo?.image?.uri ||
                              att?.media?.large_share_image?.uri ||
                              att?.styles?.attachment?.media?.photo?.image?.uri;
                if (imgUri) {
                    mediaUrl = imgUri;
                    postType = 'photo';
                    break;
                }
                // Check for video
                if (att?.media?.__typename === 'Video' || att?.media?.is_playable) {
                    postType = 'video';
                    mediaUrl = att?.media?.playable_url || att?.media?.image?.uri || null;
                    break;
                }
            }
        }

        // Fallback: search entire node JSON for scontent CDN URLs
        if (!mediaUrl) {
            const nodeStr = JSON.stringify(node);
            const cdnMatch = nodeStr.match(/(https?:\\\/\\\/scontent[^"\\]+)/);
            if (cdnMatch) {
                mediaUrl = cdnMatch[1].replace(/\\\//g, '/');
                if (postType === 'text') postType = 'photo';
            }
        }

        // --- Author ---
        let authorName = '';
        if (node.comet_sections?.context_layout?.story?.comet_sections?.actor_photo?.story?.actors?.[0]?.name) {
            authorName = node.comet_sections.context_layout.story.comet_sections.actor_photo.story.actors[0].name;
        } else if (node.actors?.[0]?.name) {
            authorName = node.actors[0].name;
        } else if (node.owner?.name) {
            authorName = node.owner.name;
        }

        // Skip if no useful data
        if (!message && !mediaUrl && !postUrl) return null;

        return {
            post_id: postId,
            message: message.substring(0, 2000),
            created_time: creationTime,
            post_url: postUrl,
            post_type: postType,
            media_url: mediaUrl,
            author_name: authorName,
            reactions_count: reactions,
            comments_count: commentsCount,
            shares_count: sharesCount,
        };
    } catch (e) {
        return null;
    }
}

/**
 * Main GraphQL intercept scraper function.
 * Loads a Facebook profile with cookies, scrolls to trigger GraphQL calls,
 * and captures the clean JSON responses.
 */
async function graphqlScrapeFacebook(fbUrl, fbCookiesJson, maxScrolls = 6) {
    const browser = await createBrowser();
    const capturedResponses = [];
    const allRawData = [];

    try {
        const page = await createPage(browser);
        await page.setViewport({ width: 1366, height: 900 });
        await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36');

        // 1. Inject cookies (REQUIRED for this method)
        if (!fbCookiesJson) {
            throw new Error('Cookies are REQUIRED for GraphQL scraping. Please provide bot account cookies.');
        }

        let cookies;
        try {
            cookies = JSON.parse(fbCookiesJson);
            await page.setCookie(...cookies);
            console.log(`[GQL-SCRAPE] ✅ Session cookies injected (${cookies.length} cookies)`);
        } catch (e) {
            throw new Error('Invalid cookies JSON format. Please re-export cookies.');
        }

        // 2. Set up GraphQL response interceptor BEFORE navigating
        console.log(`[GQL-SCRAPE] Setting up GraphQL response interceptor...`);
        
        page.on('response', async (response) => {
            const url = response.url();
            
            // Capture Facebook's internal GraphQL API calls
            if (url.includes('/api/graphql/') || url.includes('graphql')) {
                try {
                    const contentType = response.headers()['content-type'] || '';
                    if (!contentType.includes('json') && !contentType.includes('text')) return;
                    
                    const text = await response.text();
                    if (text.length < 500) return; // Skip tiny responses (not post data)
                    
                    // Facebook sometimes returns multiple JSON objects separated by newlines
                    const jsonParts = text.split('\n').filter(line => line.trim().startsWith('{'));
                    
                    for (const part of jsonParts) {
                        try {
                            const json = JSON.parse(part);
                            capturedResponses.push({
                                url: url.substring(0, 120),
                                size: part.length,
                                timestamp: new Date().toISOString(),
                            });
                            allRawData.push(json);
                        } catch (e) {
                            // Not valid JSON, skip
                        }
                    }
                } catch (e) {
                    // Response body might not be available (e.g., redirects)
                }
            }
        });

        // 3. Navigate to the Facebook profile
        let targetUrl = fbUrl
            .replace('mbasic.facebook.com', 'www.facebook.com')
            .replace('m.facebook.com', 'www.facebook.com')
            .replace('web.facebook.com', 'www.facebook.com');

        console.log(`[GQL-SCRAPE] Navigating to: ${targetUrl}`);
        await page.goto(targetUrl, { waitUntil: 'networkidle2', timeout: 60000 });
        await randomDelay(3000, 5000);

        // Check if redirected to login
        const currentUrl = page.url();
        if (currentUrl.includes('/login') || currentUrl.includes('login.php')) {
            throw new Error('Cookies expired or invalid — redirected to login page. Please update bot account cookies.');
        }

        console.log(`[GQL-SCRAPE] Page loaded. Current URL: ${currentUrl}`);
        console.log(`[GQL-SCRAPE] GraphQL responses captured so far: ${capturedResponses.length}`);

        // 4. Scroll down to trigger more GraphQL post-loading calls
        for (let i = 0; i < maxScrolls; i++) {
            console.log(`[GQL-SCRAPE] Scroll ${i + 1}/${maxScrolls}...`);
            
            await page.evaluate(() => {
                window.scrollTo(0, document.body.scrollHeight);
            });
            
            // Wait for new content to load (GraphQL responses)
            await randomDelay(2500, 4000);
            
            console.log(`[GQL-SCRAPE]   Captured responses so far: ${allRawData.length}`);
        }

        // 5. Small final wait to catch any remaining responses
        await randomDelay(2000, 3000);

        console.log(`[GQL-SCRAPE] ========================================`);
        console.log(`[GQL-SCRAPE] Total GraphQL responses captured: ${allRawData.length}`);

        // 6. Parse posts from all captured GraphQL data
        const allPostNodes = [];
        for (const rawJson of allRawData) {
            const postNodes = findPostNodes(rawJson);
            allPostNodes.push(...postNodes);
        }

        console.log(`[GQL-SCRAPE] Raw post nodes found: ${allPostNodes.length}`);

        // 7. Extract and deduplicate posts
        const seenIds = new Set();
        const seenTexts = new Set();
        const parsedPosts = [];

        for (const node of allPostNodes) {
            const post = extractPostFromNode(node);
            if (!post) continue;

            // Deduplicate by post_id or message text
            const dedupeKey = post.post_id || post.message?.substring(0, 80) || post.post_url;
            if (seenIds.has(dedupeKey)) continue;
            if (post.message && seenTexts.has(post.message.substring(0, 80))) continue;
            
            seenIds.add(dedupeKey);
            if (post.message) seenTexts.add(post.message.substring(0, 80));
            
            parsedPosts.push(post);
        }

        // Sort by date (newest first)
        parsedPosts.sort((a, b) => {
            const dateA = a.created_time ? new Date(a.created_time).getTime() : 0;
            const dateB = b.created_time ? new Date(b.created_time).getTime() : 0;
            return dateB - dateA;
        });

        console.log(`[GQL-SCRAPE] ✅ Unique posts extracted: ${parsedPosts.length}`);
        console.log(`[GQL-SCRAPE] ========================================`);

        return {
            success: true,
            stats: {
                graphql_responses_captured: allRawData.length,
                raw_post_nodes_found: allPostNodes.length,
                unique_posts_extracted: parsedPosts.length,
                scrolls_performed: maxScrolls,
                scraped_at: new Date().toISOString(),
            },
            posts: parsedPosts,
            // Send a sample of raw data (first 3 responses, truncated) for debugging
            debug_raw_responses: allRawData.slice(0, 3).map((r, i) => ({
                response_index: i,
                size_bytes: JSON.stringify(r).length,
                keys: Object.keys(r),
                // Truncated preview — user can see the structure
                preview: JSON.stringify(r).substring(0, 2000) + '...[truncated]',
            })),
            captured_endpoints: capturedResponses,
        };

    } catch (e) {
        console.error(`[GQL-SCRAPE] ❌ Failed: ${e.message}`);
        return {
            success: false,
            error: e.message,
            stats: {
                graphql_responses_captured: allRawData.length,
                raw_post_nodes_found: 0,
                unique_posts_extracted: 0,
            },
            posts: [],
            debug_raw_responses: allRawData.slice(0, 2).map((r, i) => ({
                response_index: i,
                size_bytes: JSON.stringify(r).length,
                keys: Object.keys(r),
                preview: JSON.stringify(r).substring(0, 2000) + '...[truncated]',
            })),
            captured_endpoints: capturedResponses,
        };
    } finally {
        await browser.close();
    }
}

// ============================================================
//  GRAPHQL SCRAPE API ENDPOINT
// ============================================================
app.post('/api/graphql-scrape', async (req, res) => {
    const { url, fb_cookies, max_scrolls } = req.body;

    if (!url || !url.includes('facebook.com')) {
        return res.status(400).json({ success: false, error: 'Invalid Facebook URL' });
    }
    if (!fb_cookies) {
        return res.status(400).json({ success: false, error: 'Cookies are required for GraphQL scraping' });
    }

    console.log(`\n🔬 API Request: GraphQL Scrape for "${url}"`);

    try {
        const results = await graphqlScrapeFacebook(url, fb_cookies, max_scrolls || 6);
        res.json(results);
    } catch (error) {
        console.error(`❌ API Error: ${error.message}`);
        res.status(500).json({ success: false, error: error.message });
    }
});


// ============================================================
//  GRAPHQL SEARCH — Search Facebook accounts by name
//  Uses Facebook's internal search with cookies to get:
//  - Exact page/profile names
//  - HD profile pictures
//  - Follower counts
//  - Categories (Politician, Brand, etc.)
//  - Verified badges
// ============================================================

/**
 * Extract search result entries from a captured GraphQL JSON blob.
 * Facebook's search response structure varies wildly, so we search recursively
 * for nodes that look like page/profile entries AND also scan for
 * search-specific result containers.
 */
function extractSearchResults(jsonData) {
    const results = [];
    const seen = new Set();

    /**
     * Try to extract a profile picture URL from any object (deep scan)
     */
    function findProfilePic(obj) {
        if (!obj || typeof obj !== 'object') return null;
        
        // Direct paths (most common in Facebook GraphQL)
        const directPaths = [
            obj?.profile_picture?.uri,
            obj?.profilePicLarge?.uri,
            obj?.profilePicMedium?.uri,
            obj?.profile_pic_url,
            obj?.pic_large,
            obj?.pic,
            obj?.profile_picture?.url,
            obj?.avatar?.uri,
            obj?.profile_photo?.image?.uri,
            obj?.image?.uri,
            obj?.picture?.uri,
            obj?.photo?.image?.uri,
        ];
        for (const p of directPaths) {
            if (p && typeof p === 'string' && (p.includes('scontent') || p.includes('fbcdn') || p.startsWith('http'))) {
                return p;
            }
        }

        // Search one level deeper in nested keys
        for (const key of ['profile_picture', 'profilePicLarge', 'profilePicMedium', 'picture', 'avatar', 'image', 'photo', 'profile_photo']) {
            if (obj[key] && typeof obj[key] === 'object') {
                const uri = obj[key].uri || obj[key].url || obj[key].src;
                if (uri && typeof uri === 'string') return uri;
                // Check one more level
                if (obj[key].image && obj[key].image.uri) return obj[key].image.uri;
            }
        }
        
        return null;
    }

    /**
     * Try to extract follower/like count from object
     */
    function findFollowers(obj) {
        if (!obj || typeof obj !== 'object') return '';

        // Direct count fields
        if (obj.followers_count !== undefined && obj.followers_count !== null) return formatCount(obj.followers_count);
        if (obj.follower_count !== undefined && obj.follower_count !== null) return formatCount(obj.follower_count);
        
        // Nested count objects
        if (obj.page_likers?.count !== undefined) return formatCount(obj.page_likers.count);
        if (obj.followers?.count !== undefined) return formatCount(obj.followers.count);
        if (obj.fan_count !== undefined) return formatCount(obj.fan_count);
        if (obj.like_count !== undefined) return formatCount(obj.like_count);
        if (obj.friend_count !== undefined) return formatCount(obj.friend_count) + ' friends';

        // Text-based (already formatted by FB)
        if (obj.page_like_count_text) return obj.page_like_count_text;
        if (obj.followers_count_text) return obj.followers_count_text;
        if (obj.social_context?.text) return obj.social_context.text;
        
        // Check secondary text / subtitle which often contains "X followers" or "X likes"
        const subtitle = obj.subtitle_text || obj.secondary_title || obj.page_like_count_string || '';
        if (typeof subtitle === 'string' && (subtitle.includes('follower') || subtitle.includes('like') || subtitle.includes('friend'))) {
            return subtitle;
        }

        return '';
    }

    /**
     * Try to extract category from object
     */
    function findCategory(obj) {
        if (!obj || typeof obj !== 'object') return '';
        return obj.category_name || obj.category_type || obj.page_category || 
               obj.short_name || obj.category || obj.page_categories?.[0]?.name || '';
    }

    /**
     * Try to extract description/bio from object
     */
    function findDescription(obj) {
        if (!obj || typeof obj !== 'object') return '';
        return obj.about || obj.bio || obj.blurb || obj.intro_text || obj.description || '';
    }

    function dig(obj, depth = 0) {
        if (!obj || typeof obj !== 'object' || depth > 25) return;

        // Pattern 1: Node with __typename = Page/User/Group + name + url
        if (obj.name && typeof obj.name === 'string' && obj.__typename) {
            const type = obj.__typename;
            if (['Page', 'User', 'Group', 'Profile', 'CanonicalEntity', 'PageItem'].includes(type)) {
                const url = obj.url || obj.uri || obj.profile_url || '';
                const key = url || obj.id || obj.name;
                if (!seen.has(key) && (url || obj.id)) {
                    seen.add(key);
                    results.push({
                        name: obj.name,
                        url: url ? (url.startsWith('http') ? url : 'https://www.facebook.com' + url) : '',
                        profilePic: findProfilePic(obj),
                        accountType: type.toLowerCase() === 'canonicalentity' ? 'page' : type.toLowerCase(),
                        followers: findFollowers(obj),
                        verified: obj.is_verified || obj.is_blue_verified || obj.verified || false,
                        category: findCategory(obj),
                        description: findDescription(obj),
                        fb_id: obj.id || null,
                    });
                }
            }
        }

        // Pattern 2: Search result containers (edges/nodes with nested entity)
        if (obj.node && typeof obj.node === 'object') {
            // Check for search result node containing a "entity" or "result" field
            const entity = obj.node.entity || obj.node.open_graph_object || obj.node;
            if (entity && entity.name && entity.__typename) {
                dig(entity, depth); // Let pattern 1 handle it
            }
        }

        // Pattern 3: rendering_style containers with title and image
        if (obj.title?.text && obj.image?.uri && !obj.__typename) {
            const url = obj.url || obj.uri || '';
            const key = url || obj.title.text;
            if (!seen.has(key) && url && url.includes('facebook.com')) {
                seen.add(key);
                results.push({
                    name: obj.title.text,
                    url: url.startsWith('http') ? url : 'https://www.facebook.com' + url,
                    profilePic: obj.image.uri,
                    accountType: 'page',
                    followers: obj.subtitle?.text || '',
                    verified: false,
                    category: '',
                    description: obj.subtitle?.text || '',
                    fb_id: null,
                });
            }
        }

        // Recurse
        if (Array.isArray(obj)) {
            for (const item of obj) dig(item, depth + 1);
        } else {
            for (const key of Object.keys(obj)) {
                if (typeof obj[key] === 'object') dig(obj[key], depth + 1);
            }
        }
    }

    dig(jsonData);
    return results;
}

/**
 * Format a number into human-readable count (e.g., 1.2M, 45K)
 */
function formatCount(num) {
    if (!num || isNaN(num)) return '';
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
    return num.toString();
}

/**
 * GraphQL-based Facebook Search
 * Navigates to facebook.com/search with cookies, intercepts GraphQL responses
 * to extract rich search results (name, pic, followers, category, verified)
 */
async function graphqlSearchFacebook(query, fbCookiesJson) {
    const browser = await createBrowser();
    const capturedResults = [];
    
    try {
        const page = await createPage(browser);

        // 1. Inject cookies
        let cookies = [];
        try {
            cookies = typeof fbCookiesJson === 'string' ? JSON.parse(fbCookiesJson) : fbCookiesJson;
        } catch (e) {
            throw new Error('Invalid cookie format. Please provide valid JSON cookies.');
        }

        if (!cookies || !Array.isArray(cookies) || cookies.length === 0) {
            throw new Error('Cookies are REQUIRED for GraphQL search. Please provide bot account cookies.');
        }

        const puppeteerCookies = cookies.map(c => ({
            name: c.name,
            value: c.value,
            domain: c.domain || '.facebook.com',
            path: c.path || '/',
            httpOnly: c.httpOnly !== undefined ? c.httpOnly : true,
            secure: c.secure !== undefined ? c.secure : true,
        }));
        await page.setCookie(...puppeteerCookies);
        console.log(`[GQL-SEARCH] Injected ${puppeteerCookies.length} cookies.`);

        // 2. Set up GraphQL response interceptor
        const capturedGraphqlData = [];

        page.on('response', async (response) => {
            try {
                const url = response.url();
                if ((url.includes('/api/graphql/') || url.includes('graphql')) && response.status() === 200) {
                    const contentType = response.headers()['content-type'] || '';
                    if (contentType.includes('json') || contentType.includes('text')) {
                        const text = await response.text();
                        if (text.length > 500) {
                            // Facebook sometimes returns multiple JSON objects concatenated
                            const jsonParts = text.split('\n').filter(line => line.trim().startsWith('{'));
                            for (const part of jsonParts) {
                                try {
                                    const parsed = JSON.parse(part);
                                    capturedGraphqlData.push(parsed);
                                    // Extract results immediately
                                    const newResults = extractSearchResults(parsed);
                                    capturedResults.push(...newResults);
                                } catch (pe) { /* skip malformed JSON */ }
                            }
                        }
                    }
                }
            } catch (e) { /* response already consumed or navigation changed */ }
        });

        // 3. Navigate to Facebook Search — People & Pages
        const searchUrl = `https://www.facebook.com/search/pages/?q=${encodeURIComponent(query)}`;
        console.log(`[GQL-SEARCH] Navigating to: ${searchUrl}`);
        
        await page.goto(searchUrl, { waitUntil: 'networkidle2', timeout: 45000 });
        console.log(`[GQL-SEARCH] Page loaded. Waiting for search results...`);

        // 4. Wait for results to load + scroll once for more results
        await new Promise(r => setTimeout(r, 4000));

        // Also try "All" results tab for broader coverage
        try {
            const allSearchUrl = `https://www.facebook.com/search/top/?q=${encodeURIComponent(query)}`;
            await page.goto(allSearchUrl, { waitUntil: 'networkidle2', timeout: 30000 });
            await new Promise(r => setTimeout(r, 3000));
        } catch (e) {
            console.log(`[GQL-SEARCH] All-results tab failed, continuing with pages results.`);
        }

        // 5. Scroll down once to trigger more results loading
        await page.evaluate(() => {
            window.scrollTo(0, document.body.scrollHeight);
        });
        await new Promise(r => setTimeout(r, 3000));

        console.log(`[GQL-SEARCH] Captured ${capturedGraphqlData.length} GraphQL responses.`);
        console.log(`[GQL-SEARCH] Extracted ${capturedResults.length} raw results.`);

        // 6. Also extract from page HTML — profile pics + names from rendered DOM
        //    This catches results even when GraphQL extraction misses them
        const domResults = await page.evaluate(() => {
            const results = [];
            const seenHrefs = new Set();

            // Strategy: Find all search result containers
            // Facebook renders search results inside [role="article"] or specific data attributes
            const articles = document.querySelectorAll('[role="article"], [data-visualcompletion="ignore-dynamic"]');
            
            articles.forEach(article => {
                // Find the main profile link
                const link = article.querySelector('a[href*="facebook.com/"][role="presentation"], a[href*="facebook.com/"][aria-hidden]');
                if (!link) return;

                const href = link.href || '';
                if (seenHrefs.has(href)) return;
                if (href.includes('/search/') || href.includes('/hashtag/') || 
                    href.includes('/help') || href.includes('/policies') ||
                    href.includes('/settings') || href.includes('login')) return;

                seenHrefs.add(href);

                // Get name — usually in a bold span or heading
                let name = '';
                const nameEl = article.querySelector('span[dir="auto"] > span') || article.querySelector('a span[dir="auto"]');
                if (nameEl) name = nameEl.textContent?.trim() || '';
                if (!name || name.length < 2 || name.length > 100) return;

                // Get profile pic from img tags
                let imgSrc = null;
                const imgs = article.querySelectorAll('img');
                for (const img of imgs) {
                    const src = img.src || '';
                    if ((src.includes('scontent') || src.includes('fbcdn')) && !src.includes('emoji')) {
                        imgSrc = src;
                        break;
                    }
                }
                // Also check SVG image elements (Facebook sometimes renders pics in SVG)
                if (!imgSrc) {
                    const svgImg = article.querySelector('image[href*="scontent"], image[xlink\\:href*="scontent"]');
                    if (svgImg) imgSrc = svgImg.getAttribute('href') || svgImg.getAttribute('xlink:href');
                }

                // Get subtitle text (followers, category etc)
                let subtitle = '';
                const spans = article.querySelectorAll('span');
                for (const span of spans) {
                    const text = span.textContent?.trim() || '';
                    if (text !== name && text.length > 3 && text.length < 200) {
                        if (text.includes('follower') || text.includes('like') || text.includes('friend') || text.includes('·')) {
                            subtitle = text;
                            break;
                        }
                    }
                }

                // Check for verified badge (blue tick icon)
                const verified = !!article.querySelector('[data-testid="blue-badge"], svg[aria-label*="Verified"]');

                results.push({
                    name: name,
                    url: href,
                    profilePic: imgSrc,
                    subtitle: subtitle,
                    verified: verified,
                });
            });

            // Fallback: simple link scanning if articles didn't yield results
            if (results.length === 0) {
                const links = document.querySelectorAll('a[href*="facebook.com/"]');
                links.forEach(link => {
                    const href = link.href || '';
                    if (seenHrefs.has(href)) return;
                    if (href.includes('/search/') || href.includes('/hashtag/') || 
                        href.includes('/help') || href.includes('/policies') ||
                        href.includes('/settings') || href.includes('/notifications') ||
                        href.includes('login') || href.includes('signup')) return;
                    
                    const text = (link.textContent || '').trim();
                    if (text && text.length > 1 && text.length < 100) {
                        seenHrefs.add(href);
                        const container = link.closest('[role="article"]') || link.parentElement?.parentElement;
                        let imgSrc = null;
                        if (container) {
                            const img = container.querySelector('img[src*="scontent"], img[src*="fbcdn"]');
                            if (img) imgSrc = img.src;
                        }
                        results.push({ name: text, url: href, profilePic: imgSrc, subtitle: '', verified: false });
                    }
                });
            }

            return results;
        });

        console.log(`[GQL-SEARCH] DOM scraping found: ${domResults.length} entries.`);

        await page.close();

        // 7. Merge, deduplicate, and rank results
        const allResults = [...capturedResults];
        const seenUrls = new Set(allResults.map(r => normalizeUrl(r.url)));

        // Add DOM results that aren't duplicates — also try to enrich existing results
        for (const dr of domResults) {
            const normUrl = normalizeUrl(dr.url);
            
            // Check if this DOM result enriches an existing GraphQL result
            const existing = allResults.find(r => normalizeUrl(r.url) === normUrl);
            if (existing) {
                // Enrich with DOM data if GraphQL data was missing
                if (!existing.profilePic && dr.profilePic) existing.profilePic = dr.profilePic;
                if (!existing.followers && dr.subtitle) existing.followers = dr.subtitle;
                if (!existing.verified && dr.verified) existing.verified = true;
                continue;
            }

            if (!seenUrls.has(normUrl) && normUrl) {
                seenUrls.add(normUrl);
                allResults.push({
                    name: dr.name,
                    url: dr.url,
                    profilePic: dr.profilePic || `https://ui-avatars.com/api/?name=${encodeURIComponent(dr.name)}&background=random&size=200`,
                    accountType: 'page',
                    followers: dr.subtitle || '',
                    verified: dr.verified || false,
                    category: '',
                    description: dr.subtitle || '',
                    fb_id: null,
                });
            }
        }

        // Remove results with very short names or obvious non-profile pages
        const filtered = allResults.filter(r => {
            if (!r.name || r.name.length < 2) return false;
            if (!r.url) return false;
            if (r.url.includes('/search/') || r.url.includes('/hashtag/')) return false;
            if (r.name.toLowerCase().includes('log in') || r.name.toLowerCase().includes('sign up')) return false;
            if (r.name.toLowerCase() === 'facebook' || r.name.toLowerCase() === 'meta') return false;
            return true;
        });

        // Deduplicate by normalized URL (keep richest entry)
        const finalMap = new Map();
        for (const r of filtered) {
            const key = normalizeUrl(r.url);
            if (!finalMap.has(key)) {
                // Add fallback profile pic if missing
                if (!r.profilePic) {
                    r.profilePic = `https://ui-avatars.com/api/?name=${encodeURIComponent(r.name)}&background=random&size=200&bold=true`;
                }
                finalMap.set(key, r);
            } else {
                // Merge — keep richer data
                const existing = finalMap.get(key);
                if (!existing.profilePic && r.profilePic) existing.profilePic = r.profilePic;
                if (!existing.followers && r.followers) existing.followers = r.followers;
                if (!existing.category && r.category) existing.category = r.category;
                if (!existing.description && r.description) existing.description = r.description;
                if (!existing.verified && r.verified) existing.verified = r.verified;
                if (!existing.fb_id && r.fb_id) existing.fb_id = r.fb_id;
            }
        }

        const finalResults = Array.from(finalMap.values()).slice(0, 20);
        console.log(`[GQL-SEARCH] Final results: ${finalResults.length}`);

        return {
            success: true,
            query: query,
            method: 'graphql_intercept',
            results: finalResults,
            stats: {
                graphql_responses: capturedGraphqlData.length,
                raw_results: capturedResults.length,
                dom_results: domResults.length,
                final_results: finalResults.length,
            },
        };

    } catch (e) {
        console.error(`[GQL-SEARCH] FATAL: ${e.message}`);
        throw e;
    } finally {
        await browser.close();
    }
}

/**
 * Normalize a Facebook URL for deduplication
 */
function normalizeUrl(url) {
    if (!url) return '';
    try {
        const u = new URL(url);
        let path = u.pathname.replace(/\/+$/, '').toLowerCase();
        // Remove common suffixes
        path = path.replace(/\/(about|photos|videos|posts|reviews|events|community)$/, '');
        return u.hostname + path;
    } catch (e) {
        return url.toLowerCase().replace(/\/+$/, '');
    }
}

// ============================================================
//  GRAPHQL SEARCH API ENDPOINT
// ============================================================
app.post('/api/graphql-search', async (req, res) => {
    const { query, fb_cookies } = req.body;

    if (!query || query.trim().length < 2) {
        return res.status(400).json({ success: false, error: 'Query too short (min 2 chars)' });
    }
    if (!fb_cookies) {
        // Fall back to the old Bing/Google search (no cookies needed)
        console.log(`\n🔍 GraphQL search requested but no cookies — falling back to Bing/Google search.`);
        if (isSearching) {
            return res.status(429).json({ success: false, error: 'A search is already in progress.' });
        }
        isSearching = true;
        try {
            const results = await searchFacebook(query.trim());
            res.json({ success: true, query: query.trim(), method: 'bing_google_fallback', results });
        } catch (error) {
            res.status(500).json({ success: false, error: error.message });
        } finally {
            isSearching = false;
        }
        return;
    }

    console.log(`\n🔬 API Request: GraphQL Search for "${query}"`);

    try {
        const results = await graphqlSearchFacebook(query.trim(), fb_cookies);
        res.json(results);
    } catch (error) {
        console.error(`❌ GraphQL Search Error: ${error.message}`);
        res.status(500).json({ success: false, error: error.message });
    }
});


// ============================================================
//  START SERVER
// ============================================================
const PORT = process.env.PORT || 3000;
app.listen(PORT, '0.0.0.0', () => {
    console.log(`🚀 Smart Social Scraper v5 running on port ${PORT}`);
    console.log(`   Sources: Bing ✓ | Google ✓ | Direct FB ✓`);
    console.log(`   Scraping: mbasic ✓ | GraphQL Intercept ✓`);
    console.log(`   Search: Bing/Google ✓ | GraphQL Search ✓`);
    console.log(`   Auto-Comment: mbasic ✓ | Desktop Fallback ✓`);
    console.log(`   Anti-detection: Stealth ✓ | UA Rotation ✓ | Random Delays ✓`);
});
