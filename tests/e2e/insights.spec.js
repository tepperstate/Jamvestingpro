const { test, expect } = require('@playwright/test');

test.describe('Insights Page', () => {
  test('insights page loads successfully', async ({ page }) => {
    await page.goto('/insights');
    const status = await page.evaluate(() => document.readyState);
    expect(status).toBe('complete');
    await expect(page).toHaveURL(/.*\/insights/);
  });
});
