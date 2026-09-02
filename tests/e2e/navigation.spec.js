const { test, expect } = require('@playwright/test');
const { loginAs, collectConsoleErrors, dismissRoleSwitcherIfPresent } = require('./helpers');

// Regression test utama: klik semua item sidebar untuk tiap role, pastikan tidak ada error
// console atau response HTTP gagal (401/403/500). Ini jaring pengaman paling penting di project
// ini - proteksi API yang salah pasang (terlalu ketat atau terlalu longgar untuk suatu role)
// akan langsung ketahuan di sini tanpa perlu klik manual satu-satu.
test.describe('Navigasi sidebar per role', () => {
  for (const role of ['admin', 'ppk', 'pokja', 'vendor']) {
    test(`semua menu sidebar untuk role ${role} bisa dibuka tanpa error`, async ({ page }) => {
      // Waktu tes ini murni sebanding dengan JUMLAH menu yang dimiliki role itu (klik satu-satu,
      // tiap klik nunggu render+API sungguhan) - role admin sekarang punya 21 menu dan makin
      // bertambah tiap modul baru ditambahkan, jadi timeout default 30s sudah beberapa kali mepet/
      // gagal padahal aplikasinya sendiri normal (dikonfirmasi lewat pengecekan waktu render per
      // halaman terisolasi, tidak ada halaman yang benar-benar lambat). Dinaikkan ke 60s supaya
      // tidak perlu dinaikkan lagi tiap kali ada 1-2 menu baru ditambahkan ke sidebar admin.
      test.setTimeout(60000);
      const errors = collectConsoleErrors(page);
      const failedRequests = [];
      page.on('response', (res) => {
        if (res.status() >= 400 && res.status() !== 404) failedRequests.push(`${res.status()} ${res.url()}`);
      });

      await loginAs(page, role);

      // Tunggu sidebar benar-benar selesai render (menu diambil dari API GET /api/menu/:role,
      // butuh waktu setelah login) sebelum mulai menghitung/mengklik item, supaya tidak flaky.
      // Sebelumnya pakai waitFor(...).catch(()=>{}) sekali saja - kalau render lebih lambat dari
      // timeout (pernah kejadian di lingkungan yang lambat), catch menelan errornya diam-diam dan
      // items.count() langsung dipanggil sesudahnya walau sidebar belum benar-benar terisi,
      // count() sendiri TIDAK auto-retry jadi hasilnya 0 dan seluruh loop di bawah tidak pernah
      // jalan. Diganti jadi polling expect() yang otomatis coba ulang sampai benar-benar > 0.
      const items = page.locator('aside button, aside a');
      await expect.poll(async () => items.count(), { timeout: 15000, message: 'Sidebar belum terisi menu apapun' }).toBeGreaterThan(0);
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
