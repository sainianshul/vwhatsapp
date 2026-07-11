const puppeteer = require('puppeteer');
const fs = require('fs');

async function run() {
    const browser = await puppeteer.launch({
        args: ['--no-sandbox']
    });
    const page = await browser.newPage();
    const cookies = JSON.parse(process.argv[2]);
    await page.setCookie(...cookies);
    
    await page.goto('https://mbasic.facebook.com/narendramodi?v=timeline', { waitUntil: 'networkidle2' });
    
    const html = await page.content();
    fs.writeFileSync('mbasic_dump.html', html);
    
    await browser.close();
}

run();
