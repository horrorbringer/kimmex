import puppeteer from 'puppeteer';

(async () => {
  const browser = await puppeteer.launch();
  const page = await browser.newPage();
  
  await page.setViewport({ width: 1920, height: 1080 });
  await page.goto('https://layout-cms-km.vercel.app/design-z/about', { waitUntil: 'networkidle0' });
  
  const content = await page.evaluate(() => document.body.outerHTML);
  console.log(content);
  await browser.close();
})();
