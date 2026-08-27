const { test, expect } = require('@playwright/test');
const { loginAs, collectConsoleErrors } = require('./helpers');

test.describe('Autentikasi', () => {
  test('halaman utama publik tampil tanpa error console', async ({ page }) => {
    const errors = collectConsoleErrors(page);
    await page.goto('/');
    await expect(page.locator('body')).toBeVisible();
    await page.waitForTimeout(1000);
    expect(errors, `Console error ditemukan: ${errors.join(' | ')}`).toEqual([]);
  });

  test('login gagal dengan password salah menampilkan pesan error', async ({ page }) => {
    await page.goto('/');
    await page.getByRole('button', { name: /^masuk$|^login$/i }).first().click();
    await page.getByPlaceholder('Username').fill('admin@ui.ac.id');
    await page.locator('input[type="password"]').fill('password-salah-sengaja');
    const captchaText = await page.locator('span.font-black.text-2xl').first().textContent();
    await page.getByPlaceholder('ketik kode').fill((captchaText || '').trim());
    await page.locator('form button[type="submit"]').click();
    await expect(page.getByText(/username atau password salah/i)).toBeVisible({ timeout: 5000 });
  });

  test('login gagal dengan kode captcha salah', async ({ page }) => {
    await page.goto('/');
    await page.getByRole('button', { name: /^masuk$|^login$/i }).first().click();
    await page.getByPlaceholder('Username').fill('admin@ui.ac.id');
    await page.locator('input[type="password"]').fill('UIAdmin2026!');
    await page.getByPlaceholder('ketik kode').fill('salahkode');
    await page.locator('form button[type="submit"]').click();
    await expect(page.getByText(/kode keamanan salah/i)).toBeVisible({ timeout: 3000 });
  });

  for (const role of ['admin', 'ppk', 'pokja', 'vendor']) {
    test(`login berhasil sebagai ${role} dan tanpa error console`, async ({ page }) => {
      const errors = collectConsoleErrors(page);
      await loginAs(page, role);
      // Setelah login berhasil, sidebar/topbar aplikasi utama harus muncul (bukan lagi landing page publik)
      await expect(page.locator('body')).toBeVisible();
      await page.waitForTimeout(1000);
      expect(errors, `Console error saat login sebagai ${role}: ${errors.join(' | ')}`).toEqual([]);
    });
  }
});
