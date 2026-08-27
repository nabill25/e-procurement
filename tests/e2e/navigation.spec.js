const { test, expect } = require('@playwright/test');
const { loginAs, collectConsoleErrors, dismissRoleSwitcherIfPresent } = require('./helpers');

// Regression test utama: klik semua item sidebar untuk tiap role, pastikan tidak ada error
// console atau response HTTP gagal (401/403/500). Ini jaring pengaman paling penting di project
// ini - proteksi API yang salah pasang (terlalu ketat atau terlalu longgar untuk suatu role)
// akan langsung ketahuan di sini tanpa perlu klik manual satu-satu.
test.describe('Navigasi sidebar per role', () => {
  for (const role of ['admin', 'ppk', 'pokja', 'vendor']) {
    test(`semua menu sidebar untuk role ${role} bisa dibuka tanpa error`, async ({ page }) => {
      const errors = collectConsoleErrors(page);
      const failedRequests = [];
      page.on('response', (res) => {
        if (res.status() >= 400 && res.status() !== 404) failedRequests.push(`${res.status()} ${res.url()}`);
      });

      await loginAs(page, role);

      const items = page.locator('aside button, aside a');
      const count = await items.count();
      const clickedLabels = [];
      for (let i = 0; i < count; i++) {
        await dismissRoleSwitcherIfPresent(page);
        const el = items.nth(i);
        const text = (await el.textContent({ timeout: 3000 }).catch(() => '') || '').trim();
        if (!text) continue;
        clickedLabels.push(text);
        await el.click({ timeout: 3000 }).catch(() => {});
        await page.waitForTimeout(400);
        await dismissRoleSwitcherIfPresent(page);
      }

      expect(clickedLabels.length, 'Sidebar harus punya minimal 1 menu yang bisa diklik').toBeGreaterThan(0);
      expect(errors, `Console error saat navigasi role ${role}: ${errors.join(' | ')}`).toEqual([]);
      expect(failedRequests, `Request gagal (bukan 404) saat navigasi role ${role}: ${failedRequests.join(' | ')}`).toEqual([]);
    });
  }
});
