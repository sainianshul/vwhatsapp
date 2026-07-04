import axios from 'axios';
import * as cheerio from 'cheerio';
import cors from 'cors';
import 'dotenv/config';
import express from 'express';
import { existsSync } from 'node:fs';
import morgan from 'morgan';
import puppeteer from 'puppeteer-core';

const app = express();
const port = process.env.PORT || 4000;
const host = process.env.HOST || '0.0.0.0';
const facebookCookie = normalizeCookie(process.env.FACEBOOK_COOKIE || '');
const chromePath = process.env.CHROME_PATH || findChromePath();

app.use(cors());
app.use(express.json());
app.use(morgan('dev'));

const facebookClient = axios.create({
  timeout: 20000,
  maxRedirects: 5,
  validateStatus: () => true,
  headers: {
    'accept-language': 'en-US,en;q=0.9',
    'user-agent': 'Mozilla/5.0',
    ...(facebookCookie ? { cookie: facebookCookie } : {}),
  },
});

app.get('/api/health', (_req, res) => {
  res.json({
    ok: true,
    service: 'aifacebook-server',
    facebookCookieConfigured: Boolean(facebookCookie),
  });
});

app.get('/api/search', async (req, res) => {
  const query = String(req.query.q || '').trim();

  if (!query) {
    return res.status(400).json({ message: 'Search query required' });
  }

  try {
    const encodedQuery = encodeURIComponent(query);
    const sourceUrl = `https://www.facebook.com/search/top/?q=${encodedQuery}`;
    const searchUrls = [
      `https://m.facebook.com/search/top/?q=${encodedQuery}`,
      `https://mbasic.facebook.com/search/top/?q=${encodedQuery}`,
      `https://www.facebook.com/public/${encodeURIComponent(query.replace(/\s+/g, '-'))}`,
      sourceUrl,
    ];
    const browserResult =
      facebookCookie && chromePath ? await scrapeSearchWithBrowser(query, sourceUrl).catch(() => null) : null;
    const { items, fetchedUrl } =
      browserResult?.items?.length > 0 ? browserResult : await searchAcrossSources(searchUrls);

    res.json({
      query,
      sourceUrl,
      fetchedUrl,
      items,
      facebookCookieConfigured: Boolean(facebookCookie),
    });
  } catch (error) {
    res.status(502).json({
      message:
        'Facebook se data fetch nahi ho paya. Public page blocked, login required, ya network issue ho sakta hai.',
      detail: error.message,
    });
  }
});

app.get('/api/profile/posts', async (req, res) => {
  const rawUrl = String(req.query.url || '').trim();
  const profileUrl = normalizeFacebookUrl(rawUrl);

  if (!profileUrl) {
    return res.status(400).json({ message: 'Valid facebook.com profile url required' });
  }

  try {
    const browserPosts =
      facebookCookie && chromePath ? await scrapeProfilePostsWithBrowser(profileUrl).catch(() => null) : null;
    const posts = browserPosts?.length
      ? browserPosts
      : parseProfilePosts(await fetchHtml(toMobileFacebookUrl(profileUrl)), profileUrl);

    res.json({
      profileUrl,
      posts,
      facebookCookieConfigured: Boolean(facebookCookie),
    });
  } catch (error) {
    res.status(502).json({
      message:
        'Profile posts fetch nahi ho payi. Facebook aksar posts ke liye login/session require karta hai.',
      detail: error.message,
    });
  }
});

app.post('/api/analyze-posts', (req, res) => {
  const profileName = String(req.body?.profileName || 'this profile').trim();
  const tone = normalizeTone(req.body?.tone);
  const posts = Array.isArray(req.body?.posts) ? req.body.posts : [];

  const analyses = posts.map((post, index) => {
    const text = cleanText(post?.text || '');
    const postUrl = String(post?.url || '');
    const postId = String(post?.postId || '') || extractPostId(postUrl);
    return analyzePost({
      id: postId || postUrl || `post-${index + 1}`,
      profileName,
      text,
      timeText: cleanText(post?.timeText || ''),
      url: postUrl,
      authorName: cleanText(post?.authorName || profileName),
      imageUrl: String(post?.imageUrl || ''),
      tone,
    });
  });

  res.json({ analyses });
});


// ====================== DIRECT COMMENT POSTING ======================
app.post('/api/post-comment', async (req, res) => {
  const { postUrl, postId, commentText } = req.body;

  if (!postUrl || !commentText?.trim()) {
    return res.status(400).json({
      success: false,
      message: 'postUrl aur commentText dono chahiye'
    });
  }

  if (!facebookCookie) {
    return res.status(400).json({
      success: false,
      message: 'Facebook cookie set nahi hai. Pehle cookie set karo.'
    });
  }

  try {
    const result = await postCommentWithBrowser(postUrl, commentText.trim());

    res.json({
      success: true,
      message: 'Comment successfully posted!',
      postUrl,
      comment: commentText,
      ...result
    });
  } catch (error) {
    console.error('Comment post error:', error);
    res.status(502).json({
      success: false,
      message: 'Comment post nahi ho paya. Session expired ya Facebook blocked kar diya.',
      detail: error.message
    });
  }
});

async function postCommentWithBrowser(postUrl, commentText) {
  return withFacebookPage(async (page) => {
    console.log('Opening post:', postUrl);

    await page.goto(postUrl, { waitUntil: 'networkidle2', timeout: 60000 });
    await waitForFacebookContent(page);
    await new Promise(r => setTimeout(r, 5000));

    await page.screenshot({ path: 'debug-before.png' });

    const commentBoxFound = await page.evaluate(async () => {
      // Scroll to bottom
      window.scrollTo(0, document.body.scrollHeight - 300);
      await new Promise(r => setTimeout(r, 2500));

      // Step 1: Click on Comment icon (chat bubble), if Facebook has not opened the box already.
      const commentIcon = Array.from(document.querySelectorAll('div[role="button"]')).find(el => {
        const aria = el.getAttribute('aria-label') || '';
        return aria.toLowerCase().includes('comment') || aria.toLowerCase().includes('reply');
      });

      if (commentIcon) {
        commentIcon.click();
        await new Promise(r => setTimeout(r, 3000));
      }

      // Step 2: Find comment box
      const textboxes = Array.from(document.querySelectorAll('div[role="textbox"][contenteditable="true"], textarea'));
      const commentBox = textboxes.find(el => {
        const aria = (el.getAttribute('aria-label') || '').toLowerCase();
        const placeholder = (el.getAttribute('aria-placeholder') || el.getAttribute('placeholder') || '').toLowerCase();
        const text = `${aria} ${placeholder}`;
        return text.includes('comment') || text.includes('reply') || text.includes('\u0915\u092e\u0947\u0902\u091f');
      }) || textboxes.at(-1);

      if (!commentBox) return false;

      // Real focus/click is important because Facebook's React editor ignores plain textContent changes.
      commentBox.scrollIntoView({ block: 'center', inline: 'center' });
      commentBox.focus();
      commentBox.click();
      return true;
    });

    if (!commentBoxFound) throw new Error('Comment box not found');

    // Step 3: Type like a real user so the send button becomes active.
    await page.keyboard.type(commentText, { delay: 20 });
    await new Promise(r => setTimeout(r, 1500));

    const beforePostCount = await page.evaluate((text) => {
      const isVisible = (el) => {
        const rect = el.getBoundingClientRect();
        const style = window.getComputedStyle(el);
        return rect.width > 0 && rect.height > 0 && style.visibility !== 'hidden' && style.display !== 'none';
      };

      const textboxes = Array.from(document.querySelectorAll('div[role="textbox"][contenteditable="true"], textarea'));
      const activeTextbox = textboxes.find(el => (el.innerText || el.value || '').includes(text)) ||
        document.activeElement?.closest('div[role="textbox"][contenteditable="true"], textarea') ||
        textboxes.at(-1);
      const composer = activeTextbox?.closest('[role="presentation"], form') || activeTextbox?.parentElement;

      return Array.from(document.querySelectorAll('div, span')).filter(el => {
        if (!isVisible(el)) return false;
        if (activeTextbox && (el === activeTextbox || activeTextbox.contains(el) || el.contains(activeTextbox))) return false;
        if (composer && (el === composer || composer.contains(el) || el.contains(composer))) return false;
        const elementText = (el.innerText || el.textContent || '').trim();
        if (!elementText.includes(text)) return false;
        return !Array.from(el.children).some(child => (child.innerText || child.textContent || '').includes(text));
      }).length;
    }, commentText);

    const clickResult = await page.evaluate(async (text) => {
      const isVisible = (el) => {
        const rect = el.getBoundingClientRect();
        const style = window.getComputedStyle(el);
        return rect.width > 0 && rect.height > 0 && style.visibility !== 'hidden' && style.display !== 'none';
      };

      const textboxes = Array.from(document.querySelectorAll('div[role="textbox"][contenteditable="true"], textarea'));
      const activeTextbox = textboxes.find(el => (el.innerText || el.value || '').includes(text)) ||
        document.activeElement?.closest('div[role="textbox"][contenteditable="true"], textarea') ||
        textboxes.at(-1);

      if (!activeTextbox) return { success: false, reason: 'Comment box lost after typing' };

      const findComposer = (textbox) => {
        let node = textbox.parentElement;
        while (node && node !== document.body) {
          const buttons = Array.from(node.querySelectorAll('div[role="button"], button')).filter(isVisible);
          const rect = node.getBoundingClientRect();
          if (buttons.length >= 2 && rect.width > 250) return { node, buttons };
          node = node.parentElement;
        }
        return { node: document.body, buttons: Array.from(document.querySelectorAll('div[role="button"], button')).filter(isVisible) };
      };

      const { node: composer, buttons } = findComposer(activeTextbox);
      activeTextbox.setAttribute('data-ai-comment-editor', 'true');
      composer.setAttribute('data-ai-comment-composer', 'true');

      // Step 4: Click Send/Post icon. Facebook often renders it as an unlabeled SVG button.
      const labelledButton = buttons.find(el => {
        const label = `${el.getAttribute('aria-label') || ''} ${el.textContent || ''}`.toLowerCase();
        return label.includes('send') || label.includes('\u092d\u0947\u091c');
      });

      if (labelledButton) {
        labelledButton.click();
        return { success: true };
      }

      const textboxRect = activeTextbox.getBoundingClientRect();
      const composerRect = composer.getBoundingClientRect();
      const iconButtons = buttons.filter(el => {
        const rect = el.getBoundingClientRect();
        const label = `${el.getAttribute('aria-label') || ''} ${el.textContent || ''}`.toLowerCase();
        const hasIcon = Boolean(el.querySelector('svg, img, i'));
        const isCompact = rect.width <= 60 && rect.height <= 60;
        const disabled = el.getAttribute('aria-disabled') === 'true' || el.hasAttribute('disabled');
        const isComposerButton = rect.left >= composerRect.left && rect.right <= composerRect.right &&
          rect.top >= composerRect.top && rect.bottom <= composerRect.bottom;
        const isRightOfTextbox = rect.left > textboxRect.left + textboxRect.width * 0.55;
        const isNearTextboxBottom = Math.abs(rect.bottom - textboxRect.bottom) < 40;
        const isAttachment = ['emoji', 'gif', 'photo', 'sticker', 'avatar', 'camera'].some(word => label.includes(word));
        return hasIcon && isCompact && !disabled && isComposerButton && isRightOfTextbox && isNearTextboxBottom && !isAttachment;
      });

      const iconButton = iconButtons.sort((a, b) => {
        const aRect = a.getBoundingClientRect();
        const bRect = b.getBoundingClientRect();
        const aScore = (composerRect.right - aRect.right) + (composerRect.bottom - aRect.bottom);
        const bScore = (composerRect.right - bRect.right) + (composerRect.bottom - bRect.bottom);
        return aScore - bScore;
      })[0];

      if (iconButton) {
        iconButton.click();
        return { success: true };
      }

      return { success: false, reason: 'Send icon not found' };
    }, commentText);

    const verifyPosted = async () => page.evaluate(({ text, beforeCount }) => {
      const isVisible = (el) => {
        const rect = el.getBoundingClientRect();
        const style = window.getComputedStyle(el);
        return rect.width > 0 && rect.height > 0 && style.visibility !== 'hidden' && style.display !== 'none';
      };

      const editor = document.querySelector('[data-ai-comment-editor="true"]');
      const composer = document.querySelector('[data-ai-comment-composer="true"]');
      const editorText = editor ? (editor.innerText || editor.value || '') : '';
      const editorStillHasText = editorText.includes(text);

      const postedCount = Array.from(document.querySelectorAll('div, span')).filter(el => {
        if (!isVisible(el)) return false;
        if (editor && (el === editor || editor.contains(el) || el.contains(editor))) return false;
        if (composer && (el === composer || composer.contains(el) || el.contains(composer))) return false;
        const elementText = (el.innerText || el.textContent || '').trim();
        if (!elementText.includes(text)) return false;
        return !Array.from(el.children).some(child => (child.innerText || child.textContent || '').includes(text));
      }).length;

      return postedCount > beforeCount && !editorStillHasText;
    }, { text: commentText, beforeCount: beforePostCount });

    await new Promise(r => setTimeout(r, 5000));
    let posted = await verifyPosted();

    if (!posted) {
      await page.keyboard.down('Control');
      await page.keyboard.press('Enter');
      await page.keyboard.up('Control');
      await new Promise(r => setTimeout(r, 2500));
      posted = await verifyPosted();
    }

    if (!posted) {
      await page.keyboard.press('Enter');
      await new Promise(r => setTimeout(r, 5000));
      posted = await verifyPosted();
    }

    if (!posted) {
      await page.screenshot({ path: 'debug-after.png' });
      throw new Error(clickResult.reason || 'Comment was typed but not submitted');
    }

    return { status: 'posted', message: 'Comment posted on Facebook!' };
  });
}
/*async function postCommentWithBrowser(postUrl, commentText) {
  return withFacebookPage(async (page) => {
    await page.goto(postUrl, {
      waitUntil: 'networkidle2',
      timeout: 45000
    });

    await waitForFacebookContent(page);

    // Click on comment box
    const commentBoxSelector = [
      'div[role="textbox"][contenteditable="true"]',
      'textarea[placeholder*="Write a comment"]',
      'div[aria-label*="Write a comment"]'
    ].join(', ');

    await page.waitForSelector(commentBoxSelector, { timeout: 15000 }).catch(() => null);

    // Focus and type comment
    await page.evaluate((text) => {
      const boxes = document.querySelectorAll('div[role="textbox"], textarea');
      for (const box of boxes) {
        if (box.getAttribute('contenteditable') === 'true' ||
            box.getAttribute('placeholder')?.toLowerCase().includes('comment')) {
          box.focus();
          box.innerText = text;
          box.dispatchEvent(new Event('input', { bubbles: true }));
          box.dispatchEvent(new Event('change', { bubbles: true }));
          return;
        }
      }
    }, commentText);

    await new Promise(r => setTimeout(r, 1500));

    // Click Post button
    const postButtonSelector = 'div[role="button"][aria-label*="Comment"], div[role="button"][aria-label*="Post"]';
    await page.click(postButtonSelector).catch(() => null);

    await new Promise(r => setTimeout(r, 3000)); // Wait for post to register

    return { status: 'posted' };
  });
}*/




async function fetchHtml(url) {
  const response = await facebookClient.get(url);
  if (response.status < 200 || response.status >= 300) {
    throw new Error(`HTTP ${response.status} from ${url}`);
  }
  if (typeof response.data !== 'string') {
    throw new Error('Unexpected non-html response');
  }
  return response.data;
}

async function fetchFirstHtml(urls) {
  const errors = [];

  for (const url of urls) {
    try {
      return { html: await fetchHtml(url), url };
    } catch (error) {
      errors.push(error.message);
    }
  }

  throw new Error(errors.join(' | '));
}

async function searchAcrossSources(urls) {
  const errors = [];
  let lastFetchedUrl = null;

  for (const url of urls) {
    try {
      const html = await fetchHtml(url);
      lastFetchedUrl = url;
      const items = parseSearchResults(html);
      if (items.length > 0) {
        return { items, fetchedUrl: url };
      }
    } catch (error) {
      errors.push(error.message);
    }
  }

  if (lastFetchedUrl) return { items: [], fetchedUrl: lastFetchedUrl };
  throw new Error(errors.join(' | '));
}

async function scrapeSearchWithBrowser(query, sourceUrl) {
  return withFacebookPage(async (page) => {
    await page.goto(sourceUrl, { waitUntil: 'networkidle2', timeout: 45000 });
    await waitForFacebookContent(page);
    await autoScroll(page);

    const rawItems = await page.evaluate(() => {
      const anchors = Array.from(document.querySelectorAll('a[href]'));
      const items = anchors.map((anchor) => {
        const rect = anchor.getBoundingClientRect();
        const root = anchor.closest('[role="article"], div, li');
        const img = root?.querySelector('img');
        return {
          text: (anchor.innerText || anchor.textContent || '').replace(/\s+/g, ' ').trim(),
          href: anchor.href,
          imageUrl: bestImageFrom(root) || img?.src || null,
          top: rect.top,
          height: rect.height,
        };
      });

      function bestImageFrom(root) {
        const images = Array.from(root?.querySelectorAll('img') || []);
        const scored = images
          .map((img) => {
            const rect = img.getBoundingClientRect();
            return {
              src: img.currentSrc || img.src,
              score: rect.width * rect.height,
              width: rect.width,
              height: rect.height,
            };
          })
          .filter((item) => item.src && item.width >= 40 && item.height >= 40)
          .sort((a, b) => b.score - a.score);
        return scored[0]?.src || null;
      }

      return items;
    });

    const seen = new Set();
    const lowerQuery = query.toLowerCase();
    const items = [];

    for (const item of rawItems) {
      const profileUrl = canonicalProfileUrl(item.href);
      if (!profileUrl || seen.has(profileUrl) || isFacebookUtilityUrl(profileUrl)) continue;
      if (!looksLikeSearchResult(item.text, lowerQuery)) continue;

      seen.add(profileUrl);
      items.push({
        name: item.text.slice(0, 90),
        url: profileUrl,
        snippet: profileUrl,
        imageUrl: normalizeImageUrl(item.imageUrl),
      });
    }

    const limitedItems = items.slice(0, 15);
    for (const item of limitedItems.slice(0, 10)) {
      if (!item.imageUrl) {
        item.imageUrl = await resolveProfileImage(page, item.url);
      }
    }

    return { items: limitedItems, fetchedUrl: page.url() };
  });
}

async function resolveProfileImage(page, profileUrl) {
  try {
    const previousUrl = page.url();
    await page.goto(profileUrl, { waitUntil: 'domcontentloaded', timeout: 12000 });
    await new Promise((resolve) => setTimeout(resolve, 1200));
    const imageUrl = await page.evaluate(() => {
      const images = Array.from(document.querySelectorAll('image[href], img'));
      const scored = images
        .map((image) => {
          const rect = image.getBoundingClientRect();
          const src = image.getAttribute('href') || image.currentSrc || image.src;
          return {
            src,
            score: rect.width * rect.height,
            width: rect.width,
            height: rect.height,
          };
        })
        .filter((item) => item.src && item.width >= 40 && item.height >= 40)
        .sort((a, b) => b.score - a.score);
      return scored[0]?.src || null;
    });
    await page.goto(previousUrl, { waitUntil: 'domcontentloaded', timeout: 8000 }).catch(() => null);
    return normalizeImageUrl(imageUrl);
  } catch {
    return null;
  }
}

async function scrapeProfilePostsWithBrowser(profileUrl) {
  return withFacebookPage(async (page) => {
    await page.goto(profileUrl, { waitUntil: 'networkidle2', timeout: 45000 });
    await waitForFacebookContent(page);

    const rawPosts = await page.evaluate(() => {
      const nodes = Array.from(document.querySelectorAll('[role="article"], [data-pagelet^="FeedUnit"]'));
      return nodes.map((node) => {
        const message = node.querySelector('[data-ad-preview="message"]');
        const text = ((message?.innerText || message?.textContent || node.innerText || node.textContent) ?? '')
          .replace(/\s+/g, ' ')
          .trim();
        const link = node.querySelector('a[href*="/posts/"], a[href*="story_fbid"], a[href*="permalink"]');
        const time = node.querySelector('a[href*="/posts/"], a[href*="story_fbid"], abbr');
        const author = node.querySelector('h2, h3, strong a, span a[role="link"]');
        const image = bestImageFrom(node);
        return {
          text,
          url: link?.href || location.href,
          timeText: (time?.innerText || time?.textContent || '').replace(/\s+/g, ' ').trim(),
          authorName: (author?.innerText || author?.textContent || '').replace(/\s+/g, ' ').trim(),
          imageUrl: image,
        };
      });

      function bestImageFrom(root) {
        const images = Array.from(root?.querySelectorAll('img') || []);
        const scored = images
          .map((img) => {
            const rect = img.getBoundingClientRect();
            return {
              src: img.currentSrc || img.src,
              score: rect.width * rect.height,
              width: rect.width,
              height: rect.height,
            };
          })
          .filter((item) => item.src && item.width >= 80 && item.height >= 80)
          .sort((a, b) => b.score - a.score);
        return scored[0]?.src || null;
      }
    });

    const seen = new Set();
    const posts = [];

    for (const rawPost of rawPosts) {
      const text = cleanText(rawPost.text);
      if (text.length < 30 || text.length > 2200) continue;
      if (looksLikeNavigation(text)) continue;
      if (rawPost.url?.includes('comment_id=')) continue;
      if (looksLikeComment(text)) continue;

      const key = text.slice(0, 220);
      if (seen.has(key)) continue;
      seen.add(key);

      posts.push({
        postId: extractPostId(rawPost.url) || `post-${posts.length + 1}`,
        authorName: rawPost.authorName || '',
        text,
        timeText: rawPost.timeText?.slice(0, 80) || '',
        url: facebookAbsoluteUrl(rawPost.url) || profileUrl,
        imageUrl: normalizeImageUrl(rawPost.imageUrl),
      });
    }

    return posts.slice(0, 30);
  });
}

async function withFacebookPage(task) {
  const browser = await puppeteer.launch({
    executablePath: chromePath,
    headless: false,
    args: [
      '--no-sandbox',
      '--disable-setuid-sandbox',
      '--disable-notifications',
      '--disable-dev-shm-usage',
      '--lang=en-US,en',
      '--window-size=1366,900',   // ← Bigger size
      '--start-maximized',
    ],
  });

  try {
    const page = await browser.newPage();
    await page.setViewport({ width: 1366, height: 900, deviceScaleFactor: 1 });
    await page.setUserAgent(
      'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 ' +
        '(KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
    );
    await page.setCookie(...cookieToPuppeteerCookies(facebookCookie));
    return await task(page);
  } finally {
    await browser.close();
  }
}

async function waitForFacebookContent(page) {
  await page
    .waitForFunction(
      () => document.body && document.body.innerText && document.body.innerText.trim().length > 500,
      { timeout: 20000 },
    )
    .catch(() => null);
  await new Promise((resolve) => setTimeout(resolve, 2500));
}

async function autoScroll(page) {
  for (let index = 0; index < 3; index += 1) {
    await page.evaluate(() => window.scrollBy(0, window.innerHeight * 0.85));
    await new Promise((resolve) => setTimeout(resolve, 1400));
  }
}

function parseSearchResults(html) {
  const $ = cheerio.load(html);
  const seen = new Set();
  const results = [];

  $('a').each((_index, element) => {
    const anchor = $(element);
    const href = anchor.attr('href') || '';
    const text = cleanText(anchor.text());
    const absoluteUrl = facebookAbsoluteUrl(href);
    const profileUrl = canonicalProfileUrl(absoluteUrl);

    if (!profileUrl || !text || text.length < 2 || seen.has(profileUrl)) return;
    if (isFacebookUtilityUrl(profileUrl)) return;

    const containerText = cleanText(anchor.closest('td, div, li').text());
    seen.add(profileUrl);
    results.push({
      name: text.slice(0, 90),
      url: profileUrl,
      snippet: containerText.replace(text, '').trim().slice(0, 180),
      imageUrl: findNearbyImage($, anchor),
    });
  });

  return results.slice(0, 25);
}

function parseProfilePosts(html, profileUrl) {
  const $ = cheerio.load(html);
  const posts = [];
  const seen = new Set();

  $('div, article').each((_index, element) => {
    const block = $(element);
    const text = cleanText(block.text());

    if (text.length < 35 || text.length > 1800) return;
    if (looksLikeNavigation(text)) return;

    const key = text.slice(0, 180);
    if (seen.has(key)) return;
    seen.add(key);

    const link = block.find('a[href*="story.php"], a[href*="/posts/"], a[href*="permalink"]').first();
    const timeText = cleanText(
      block.find('abbr, a[href*="story.php"], a[href*="/posts/"], a[href*="permalink"]').first().text(),
    );

    posts.push({
      text,
      timeText: timeText.length > 80 ? '' : timeText,
      url: facebookAbsoluteUrl(link.attr('href') || '') || profileUrl,
    });
  });

  return posts.slice(0, 30);
}

function findNearbyImage($, anchor) {
  const image = anchor.closest('td, div, li').find('img').first();
  const src = image.attr('src');
  if (!src) return null;
  return src.startsWith('//') ? `https:${src}` : src;
}

function facebookAbsoluteUrl(href) {
  if (!href) return null;

  try {
    const url = new URL(href, 'https://mbasic.facebook.com');
    url.hash = '';
    return url.toString();
  } catch {
    return null;
  }
}

function canonicalProfileUrl(urlValue) {
  if (!urlValue) return null;

  try {
    const url = new URL(urlValue);
    if (!isFacebookHost(url.hostname)) return null;

    url.hostname = 'www.facebook.com';
    url.protocol = 'https:';
    url.hash = '';

    const next = url.searchParams.get('next') || url.searchParams.get('u');
    if (next) return canonicalProfileUrl(next);

    const id = url.searchParams.get('id');
    if (url.pathname.includes('/profile.php') && id) {
      return `https://www.facebook.com/profile.php?id=${encodeURIComponent(id)}`;
    }

    url.search = '';
    return url.toString().replace(/\/$/, '');
  } catch {
    return null;
  }
}

function normalizeFacebookUrl(rawUrl) {
  const canonical = canonicalProfileUrl(rawUrl);
  if (!canonical) return null;
  if (isFacebookUtilityUrl(canonical)) return null;
  return canonical;
}

function toMobileFacebookUrl(urlValue) {
  const url = new URL(urlValue);
  url.hostname = 'mbasic.facebook.com';
  return url.toString();
}

function isFacebookHost(hostname) {
  return ['facebook.com', 'www.facebook.com', 'm.facebook.com', 'mbasic.facebook.com'].includes(hostname);
}

function isFacebookUtilityUrl(urlValue) {
  try {
    const path = new URL(urlValue).pathname.toLowerCase();
    if (path === '/' || path === '') return true;
    return [
      '/reg',
      '/login',
      '/recover',
      '/help',
      '/privacy',
      '/policies',
      '/settings',
      '/messages',
      '/notifications',
      '/friends',
      '/search',
      '/groups',
      '/watch',
      '/marketplace',
      '/public',
      '/lite',
      '/ad_campaign',
      '/pages/create',
      '/careers',
      '/allactivity',
    ].some((blocked) => path === blocked || path.startsWith(`${blocked}/`));
  } catch {
    return true;
  }
}

function looksLikeNavigation(text) {
  const lower = text.toLowerCase();
  const blockedPhrases = [
    'log in',
    'forgotten password',
    'create new account',
    'not now',
    'facebook lite',
    'facebook is not available on this browser',
    'get one of the browsers below',
    'terms privacy',
    'cookie policy',
  ];

  return blockedPhrases.some((phrase) => lower.includes(phrase));
}

function looksLikeSearchResult(text, lowerQuery) {
  const value = cleanText(text);
  const lower = value.toLowerCase();
  if (value.length < 3 || value.length > 120) return false;
  if (/^(home|profile|menu|pages|groups|friends|notifications|messages)$/i.test(value)) return false;
  if (/^(होम पेज|प्रोफ़ाइल|प्रोफाइल|मेनू|पेज|ग्रुप|दोस्त|संदेश)/.test(value)) return false;
  if (looksLikeNavigation(value)) return false;

  const queryWords = lowerQuery
    .split(/\s+/)
    .map((word) => word.trim())
    .filter((word) => word.length >= 3);

  return queryWords.length === 0 || queryWords.some((word) => lower.includes(word));
}

function looksLikeComment(text) {
  const lower = text.toLowerCase();
  return [
    'reply',
    'write a comment',
    'लाइक करें जवाब दें',
    'कमेंट करें',
  ].some((phrase) => lower.includes(phrase));
}

function analyzePost(post) {
  const lower = post.text.toLowerCase();
  const positiveWords = [
    'happy',
    'great',
    'nice',
    'beautiful',
    'extraordinary',
    'congratulations',
    'खुश',
    'सुंदर',
    'बधाई',
    'अच्छ',
  ];
  const questionWords = ['?', 'क्यों', 'कैसे', 'क्या', 'where', 'why', 'how', 'what'];
  const travelWords = ['gate', 'india gate', 'travel', 'यात्रा', 'घूम', 'मंदिर', 'place'];

  const isPositive = positiveWords.some((word) => lower.includes(word));
  const isQuestion = questionWords.some((word) => lower.includes(word));
  const isTravel = travelWords.some((word) => lower.includes(word));

  const category = isTravel ? 'travel' : isQuestion ? 'question' : isPositive ? 'positive' : 'general';
  const reaction = isPositive || isTravel ? 'like' : 'care';
  const title = buildPostTitle(post.text);
  const comment = buildSuggestedComment({
    category,
    title,
    profileName: post.profileName,
    tone: post.tone,
  });

  return {
    postId: post.id,
    postUrl: post.url,
    postTitle: title,
    authorName: post.authorName,
    imageUrl: post.imageUrl,
    category,
    tone: post.tone,
    reaction,
    confidence: estimateConfidence(post.text),
    suggestedComment: comment,
    status: 'ready',
    serverMessage: '✅ Ready for direct posting!',
    timeText: post.timeText,
  };
}

function extractPostId(urlValue) {
  try {
    const url = new URL(urlValue);
    const storyId = url.searchParams.get('story_fbid') || url.searchParams.get('fbid');
    if (storyId) return storyId;
    const match = url.pathname.match(/\/(?:posts|videos|photos)\/([^/?#]+)/i);
    if (match?.[1]) return match[1];
    const permalink = url.pathname.match(/\/permalink\/([^/?#]+)/i);
    if (permalink?.[1]) return permalink[1];
    return null;
  } catch {
    return null;
  }
}

function buildPostTitle(text) {
  const cleaned = cleanText(text);
  if (!cleaned) return 'Public post';
  const firstSentence = cleaned.split(/[.!?।]/).find(Boolean) || cleaned;
  return firstSentence.slice(0, 90);
}

function normalizeTone(value) {
  return String(value || '').toLowerCase() === 'negative' ? 'negative' : 'positive';
}

function buildSuggestedComment({ category, title, tone }) {
  if (tone === 'negative') {
    if (category === 'travel') {
      return `Photo achhi hai, lekin ${title} ke baare me thoda aur context hota to post aur clear lagti.`;
    }
    if (category === 'question') {
      return 'Point samajh aaya, lekin details thodi kam hain. Thoda aur explain karenge to better rahega.';
    }
    if (category === 'positive') {
      return 'Post positive hai, bas caption thoda aur meaningful hota to impact aur strong hota.';
    }
    return 'Post theek hai, lekin thodi aur detail hoti to zyada engaging lagti.';
  }

  if (category === 'travel') {
    return `Bahut badhiya post. ${title} dekhkar kaafi positive aur inspiring feel aaya.`;
  }
  if (category === 'question') {
    return 'Bahut interesting point hai. Iske baare me aur details share kariye.';
  }
  if (category === 'positive') {
    return 'Bahut badhiya post. Positive vibe aur achhi energy ke liye thanks.';
  }
  return 'Bahut achhi post hai. Share karne ke liye thanks.';
}

function estimateConfidence(text) {
  const length = cleanText(text).length;
  if (length > 120) return 0.86;
  if (length > 50) return 0.72;
  return 0.58;
}

function normalizeImageUrl(urlValue) {
  if (!urlValue) return null;
  const value = String(urlValue);
  if (value.startsWith('http://') || value.startsWith('https://')) return value;
  if (value.startsWith('//')) return `https:${value}`;
  return null;
}

function cleanText(value) {
  return String(value || '')
    .replace(/\s+/g, ' ')
    .replace(/\u00a0/g, ' ')
    .trim();
}

function normalizeCookie(cookie) {
  return cookie
    .split(';')
    .map((part) => part.trim())
    .filter(Boolean)
    .join('; ');
}

function cookieToPuppeteerCookies(cookie) {
  return normalizeCookie(cookie)
    .split(';')
    .map((part) => part.trim())
    .filter(Boolean)
    .map((part) => {
      const separatorIndex = part.indexOf('=');
      const name = separatorIndex === -1 ? part : part.slice(0, separatorIndex);
      const value = separatorIndex === -1 ? '' : part.slice(separatorIndex + 1);
      return {
        name,
        value,
        domain: '.facebook.com',
        path: '/',
        secure: true,
        httpOnly: false,
      };
    });
}

function findChromePath() {
  const candidates = [
    'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
    'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
    `${process.env.LOCALAPPDATA || ''}\\Google\\Chrome\\Application\\chrome.exe`,
    'C:\\Program Files\\Microsoft\\Edge\\Application\\msedge.exe',
    'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
  ];

  return candidates.find((candidate) => {
    return Boolean(candidate) && existsSync(candidate);
  });
}

app.listen(port, host, () => {
  console.log(`AI Facebook server running at http://${host}:${port}`);
  console.log(`Phone URL: http://192.168.1.2:${port}`);
});
