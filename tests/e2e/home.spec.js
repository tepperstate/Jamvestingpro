const { test, expect } = require('@playwright/test');

test('homepage loads correctly', async ({ page }) => {
  await page.goto('/');

  // Check if the page title exists and is not empty.
  const title = await page.title();
  expect(title).not.toBe('');

  // Example check: expect some kind of navigation or hero section to exist
  // We don't know the exact DOM yet, but we expect the body to be visible.
  await expect(page.locator('body')).toBeVisible();
});
