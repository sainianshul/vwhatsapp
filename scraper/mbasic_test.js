const axios = require('axios');
const cheerio = require('cheerio');
const fs = require('fs');

async function testMbasic(url, cookiesJsonStr) {
    try {
        const cookiesArr = JSON.parse(cookiesJsonStr);
        // format cookies for axios: "name=value; name=value"
        const cookieHeader = cookiesArr.map(c => `${c.name}=${c.value}`).join('; ');
        
        let mbasicUrl = url.replace('www.facebook.com', 'mbasic.facebook.com');
        if (!mbasicUrl.includes('mbasic')) {
             mbasicUrl = 'https://mbasic.facebook.com/' + mbasicUrl.split('/').pop();
        }

        console.log('Fetching:', mbasicUrl);
        const res = await axios.get(mbasicUrl, {
            headers: {
                'User-Agent': 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Mobile Safari/537.36',
                'Cookie': cookieHeader,
                'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8'
            }
        });

        const html = res.data;
        const $ = cheerio.load(html);
        
        const posts = [];
        
        // In mbasic, posts are usually in articles or divs with specific structural patterns
        // Let's just find all story links to see if we're authenticated
        const title = $('title').text();
        console.log('Page Title:', title);
        
        const loginLink = $('a[href*="/login/"]');
        if (loginLink.length > 0) {
             console.log('Got redirected to login! Cookies might be invalid for mbasic without proper headers.');
        }

        // Just write the HTML to a file to inspect it
        fs.writeFileSync('mbasic_dump.html', html);
        console.log('Dumped HTML to mbasic_dump.html');
        
    } catch (e) {
        console.error(e.message);
    }
}

// Pass a profile URL and the cookies JSON string
const url = process.argv[2];
const cookies = process.argv[3];
if(url && cookies) testMbasic(url, cookies);
