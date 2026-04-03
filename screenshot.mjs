import puppeteer from 'puppeteer';

(async () => {
  const browser = await puppeteer.launch();
  const page = await browser.newPage();
  await page.setViewport({ width: 1920, height: 1080 });
  
  await page.goto('http://127.0.0.1:8000/about', { waitUntil: 'networkidle0' });
  
  // scroll down
  await page.evaluate(() => window.scrollBy(0, window.innerHeight));
  await new Promise(r => setTimeout(r, 1000));
  await page.evaluate(() => window.scrollBy(0, window.innerHeight));
  await new Promise(r => setTimeout(r, 1000));
  await page.evaluate(() => window.scrollBy(0, window.innerHeight));
  await new Promise(r => setTimeout(r, 1000));
  
  await page.screenshot({ path: '/Users/vanny/.gemini/antigravity/brain/b266209e-748a-4e1c-b607-eae7621a6c20/artifacts/milestone_preview.png', fullPage: true });
  
  console.log("Screenshot saved.");
  await browser.close();
})();
