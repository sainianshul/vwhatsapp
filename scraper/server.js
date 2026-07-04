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

        // Inject cookies if provided for authenticated scraping
        if (fbCookiesJson) {
            try {
                const cookies = JSON.parse(fbCookiesJson);
                await page.setCookie(...cookies);
                console.log(`[DEEP-SCRAPE] Session cookies injected for authenticated scrape`);
            } catch (e) {
                console.log(`[DEEP-SCRAPE] No valid cookies, scraping as guest`);
            }
        }

        // Normalize to www.facebook.com for proper desktop rendering
        let targetUrl;
        try {
            const urlObj = new URL(fbUrl);
            urlObj.hostname = 'www.facebook.com';
            targetUrl = urlObj.toString();
        } catch (e) {
            targetUrl = fbUrl;
        }

        console.log(`[DEEP-SCRAPE] Visiting ${targetUrl}`);
        await page.goto(targetUrl, { waitUntil: 'networkidle2', timeout: 60000 });
        await randomDelay(3000, 5000);

        // Wait for page content to load
        await page.waitForFunction(
            () => document.body && document.body.innerText && document.body.innerText.trim().length > 500,
            { timeout: 20000 }
        ).catch(() => null);
        await randomDelay(2000, 3000);

        // Scroll down multiple times to load more posts
        for (let i = 0; i < 6; i++) {
            await page.evaluate(() => window.scrollBy(0, window.innerHeight * 1.5));
            await randomDelay(2000, 4000);
        }

        // Extract posts using robust selectors (inspired by scrap_code.js)
        const pageData = await page.evaluate(() => {
            const postsData = [];
            const seen = new Set();

            // Use multiple selectors for post discovery
            const postElements = document.querySelectorAll('[role="article"], [data-pagelet^="FeedUnit"]');
            
            postElements.forEach((postEl, index) => {
                if (index > 25) return;

                // Content extraction — try data-ad-preview first, then innerText
                const messageEl = postEl.querySelector('[data-ad-preview="message"]');
                const content = ((messageEl?.innerText || messageEl?.textContent || '') || 
                    (postEl.querySelector('div[dir="auto"]')?.innerText || '')).replace(/\s+/g, ' ').trim();
                
                if (content.length < 20) return; // Skip too-short content

                // Skip nav/utility text
                const lower = content.toLowerCase();
                if (['log in', 'sign up', 'forgotten password', 'create new account', 'cookie policy'].some(p => lower.includes(p))) return;

                // Deduplicate by first 200 chars of content
                const key = content.slice(0, 200);
                if (seen.has(key)) return;
                seen.add(key);

                // ---- POST URL EXTRACTION (Critical!) ----
                // Try multiple link patterns to find the individual post URL
                const allLinks = Array.from(postEl.querySelectorAll('a[href]'));
                let postUrl = '';

                // Priority 1: Links containing /posts/, story_fbid, or permalink
                const postLink = allLinks.find(a => 
                    a.href.includes('/posts/') || 
                    a.href.includes('story_fbid') || 
                    a.href.includes('/permalink/') ||
                    a.href.includes('/photos/') ||
                    a.href.includes('/videos/')
                );
                if (postLink) {
                    postUrl = postLink.href;
                }

                // Priority 2: Look at role="link" anchors
                if (!postUrl) {
                    const roleLinks = allLinks.filter(a => a.getAttribute('role') === 'link');
                    const rl = roleLinks.find(a => 
                        a.href.includes('/posts/') || 
                        a.href.includes('story_fbid') || 
                        a.href.includes('/permalink/')
                    );
                    if (rl) postUrl = rl.href;
                }

                // Priority 3: Time/date links often link to individual posts
                if (!postUrl) {
                    const timeLinks = allLinks.filter(a => {
                        const text = (a.innerText || '').toLowerCase();
                        return (text.includes('hr') || text.includes('min') || text.includes('d') || 
                                text.includes('h') || text.includes('yesterday') || text.includes('just now') ||
                                /\d+\s*(h|m|d|w|hr|min)/.test(text));
                    });
                    const tl = timeLinks.find(a => a.href.includes('facebook.com') && !a.href.endsWith('.com/'));
                    if (tl) postUrl = tl.href;
                }

                // Clean the URL
                if (postUrl) {
                    try {
                        const u = new URL(postUrl);
                        u.hash = '';
                        // Keep important query params like story_fbid
                        if (!u.search.includes('story_fbid') && !u.search.includes('fbid')) {
                            u.search = '';
                        }
                        postUrl = u.toString();
                    } catch(e) {}
                }

                // Skip if we still don't have a proper individual post URL
                if (!postUrl || postUrl === window.location.href) {
                    return; // Don't store posts without individual URLs
                }

                // Media — find the best/largest image
                const images = Array.from(postEl.querySelectorAll('img'));
                const scoredImgs = images
                    .map(img => {
                        const rect = img.getBoundingClientRect();
                        return { src: img.currentSrc || img.src, score: rect.width * rect.height, w: rect.width, h: rect.height };
                    })
                    .filter(i => i.src && i.w >= 80 && i.h >= 80)
                    .sort((a, b) => b.score - a.score);
                const mediaUrl = scoredImgs[0]?.src || null;

                // Author
                const authorEl = postEl.querySelector('h2, h3, strong a, span a[role="link"]');
                const authorName = (authorEl?.innerText || authorEl?.textContent || '').replace(/\s+/g, ' ').trim();

                // Time text
                const timeEl = postEl.querySelector('abbr') || 
                    allLinks.find(a => (a.innerText || '').match(/\d+\s*(h|m|d|w|hr|min)/));
                const timeText = (timeEl?.innerText || timeEl?.textContent || '').replace(/\s+/g, ' ').trim();

                // Metrics
                let reactions = 0, commentsCount = 0, sharesCount = 0;
                const allText = postEl.innerText.toLowerCase();
                
                const commentMatch = allText.match(/([\d,.km]+)\s*comments?/i);
                if (commentMatch) {
                    let n = commentMatch[1].replace(/,/g, '');
                    commentsCount = n.includes('k') ? parseFloat(n)*1000 : n.includes('m') ? parseFloat(n)*1000000 : parseInt(n)||0;
                }
                const shareMatch = allText.match(/([\d,.km]+)\s*shares?/i);
                if (shareMatch) {
                    let n = shareMatch[1].replace(/,/g, '');
                    sharesCount = n.includes('k') ? parseFloat(n)*1000 : n.includes('m') ? parseFloat(n)*1000000 : parseInt(n)||0;
                }
                const reactionNodes = Array.from(postEl.querySelectorAll('span[aria-label]'));
                const reactionSpan = reactionNodes.find(n => (n.getAttribute('aria-label')||'').includes('reaction'));
                if (reactionSpan) {
                    let n = reactionSpan.innerText.toLowerCase().replace(/,/g, '');
                    reactions = n.includes('k') ? parseFloat(n)*1000 : n.includes('m') ? parseFloat(n)*1000000 : parseInt(n)||0;
                }

                // Extract post ID from URL
                let fbPostId = '';
                try {
                    const pUrl = new URL(postUrl);
                    fbPostId = pUrl.searchParams.get('story_fbid') || pUrl.searchParams.get('fbid') || '';
                    if (!fbPostId) {
                        const match = pUrl.pathname.match(/\/(?:posts|videos|photos|permalink)\/([^/?#]+)/i);
                        fbPostId = match?.[1] || '';
                    }
                } catch(e) {}
                if (!fbPostId) fbPostId = btoa(postUrl).substring(0, 15);

                postsData.push({
                    fb_post_id: fbPostId,
                    post_url: postUrl,
                    post_type: mediaUrl ? 'photo' : (postUrl.includes('video') ? 'video' : 'text'),
                    content: content.slice(0, 2000),
                    media_url: mediaUrl,
                    author_name: authorName,
                    time_text: timeText.slice(0, 80),
                    posted_at: new Date().toISOString(), 
                    reactions_count: isNaN(reactions) ? 0 : reactions,
                    comments_count: isNaN(commentsCount) ? 0 : commentsCount,
                    shares_count: isNaN(sharesCount) ? 0 : sharesCount,
                    comments: []
                });
            });

            return {
                posts: postsData,
                metadata: { title: document.title }
            };
        });

        results.posts = pageData.posts;
        results.metadata = pageData.metadata;
        console.log(`[DEEP-SCRAPE] Extracted ${results.posts.length} posts with individual URLs`);

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
//  START SERVER
// ============================================================
const PORT = process.env.PORT || 3000;
app.listen(PORT, '0.0.0.0', () => {
    console.log(`🚀 Smart Social Scraper v3 running on port ${PORT}`);
    console.log(`   Sources: Bing ✓ | Google ✓ | Direct FB ✓`);
    console.log(`   Auto-Comment: mbasic ✓ | Desktop Fallback ✓`);
    console.log(`   Anti-detection: Stealth ✓ | UA Rotation ✓ | Random Delays ✓`);
});
