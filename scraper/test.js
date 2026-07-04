const puppeteer = require('puppeteer-extra');
const StealthPlugin = require('puppeteer-extra-plugin-stealth');
puppeteer.use(StealthPlugin());

(async () => {
    const browser = await puppeteer.launch({
        headless: 'new',
        args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage', '--single-process', '--no-zygote'],
        executablePath: '/usr/bin/chromium',
    });
    
    const page = await browser.newPage();
    await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36');
    
    console.log('Visiting www.facebook.com/narendramodi ...');
    await page.goto('https://www.facebook.com/narendramodi', { waitUntil: 'networkidle2', timeout: 30000 });
    
    console.log('TITLE:', await page.title());
    console.log('URL:', page.url());
    
    const text = await page.evaluate(() => document.body.innerText.substring(0, 1200));
    console.log('TEXT:', text);
    
    const desc = await page.evaluate(() => {
        const m = document.querySelector('meta[name="description"]');
        return m ? m.content : 'NO META DESC';
    });
    console.log('META_DESC:', desc);
    
    await browser.close();
})()
