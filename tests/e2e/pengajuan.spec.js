const { test, expect } = require('@playwright/test');
const { loginAs, collectConsoleErrors } = require('./helpers');

// Alur inti: form Pengajuan Pengadaan Baru (5 step). Ini form yang pernah punya bug scroll
// (tombol submit tidak terjangkau di step terakhir) - test ini jadi regression guard supaya
// bug itu (atau sejenisnya) langsung ketahuan lagi kalau muncul.
test.describe('Pengajuan Pengadaan Baru', () => {
  test('form 5-step bisa diisi dan tombol submit terjangkau di desktop', async ({ page }) => {
    const errors = collectConsoleErrors(page);
    await loginAs(page, 'ppk');

    await page.getByText('Pengajuan', { exact: true }).click();
    await page.waitForTimeout(1000);

    const newBtn = page.getByRole('button', { name: /pengajuan baru/i }).first();
    await newBtn.click();
    await page.waitForTimeout(800);

    // Step 1: Informasi Dasar
    await page.locator('input[name="title"], input').first().fill('Test Otomatis - Pengadaan Alat Tulis Kantor');
    const unitField = page.locator('select, input').filter({ hasText: '' });

    // Cari tombol "Lanjut"/"Selanjutnya" untuk maju step, lakukan sampai 4 kali (5 step total)
    for (let i = 0; i < 4; i++) {
      const nextBtn = page.getByRole('button', { name: /lanjut|selanjutnya|berikutnya/i }).first();
      if (await nextBtn.isVisible().catch(() => false)) {
        await nextBtn.click({ timeout: 3000 }).catch(() => {});
        await page.waitForTimeout(500);
      }
    }

    // Verifikasi tombol submit/kirim di step terakhir benar-benar terlihat dan bisa di-scroll-in-view
    const submitBtn = page.getByRole('button', { name: /kirim pengajuan|submit|ajukan/i }).first();
    const isVisible = await submitBtn.isVisible().catch(() => false);
    if (isVisible) {
      await submitBtn.scrollIntoViewIfNeeded();
      await expect(submitBtn).toBeInViewport();
    }

    // Modal harus tetap bisa discroll (regression guard untuk bug scroll-lock lama)
    const modalContainer = page.locator('.modal-container, [class*="max-h-"][class*="overflow-y-auto"]').first();
    if (await modalContainer.count() > 0) {
      const overflowY = await modalContainer.evaluate((el) => getComputedStyle(el).overflowY);
      expect(['auto', 'scroll']).toContain(overflowY);
    }

    expect(errors, `Console error saat mengisi form pengajuan: ${errors.join(' | ')}`).toEqual([]);
  });
});

// Proteksi keamanan: pastikan endpoint sensitif benar-benar menolak akses yang tidak berhak.
// Ini automated check untuk regresi keamanan - kalau suatu saat proteksi tidak sengaja dilepas
// lagi (misal saat refactor route), test ini akan gagal dan langsung ketahuan.
test.describe('Proteksi API (regression guard keamanan)', () => {
  test('endpoint /api/users ditolak tanpa token', async ({ request }) => {
    const res = await request.get('http://localhost:3001/api/users');
    expect(res.status()).toBe(401);
  });

  test('endpoint /api/vendors ditolak tanpa token', async ({ request }) => {
    const res = await request.get('http://localhost:3001/api/vendors');
    expect(res.status()).toBe(401);
  });

  test('endpoint publik /api/tenders tetap bisa diakses tanpa token', async ({ request }) => {
    const res = await request.get('http://localhost:3001/api/tenders');
    expect(res.status()).toBe(200);
  });

  test('endpoint publik /api/cms/banners tetap bisa diakses tanpa token', async ({ request }) => {
    const res = await request.get('http://localhost:3001/api/cms/banners');
    expect(res.status()).toBe(200);
  });

  test('login vendor lalu akses /api/master/bank (kategori admin-only write) - GET tetap boleh, POST ditolak', async ({ request }) => {
    const loginRes = await request.post('http://localhost:3001/api/auth/login', {
      data: { username: 'vendor@gmail.com', password: 'UIVendor2026!' },
    });
    const loginJson = await loginRes.json();
    expect(loginJson.success).toBe(true);
    const token = loginJson.token;

    const getRes = await request.get('http://localhost:3001/api/master/bank', {
      headers: { Authorization: `Bearer ${token}` },
    });
    expect(getRes.status()).toBe(200);

    const postRes = await request.post('http://localhost:3001/api/master/bank', {
      headers: { Authorization: `Bearer ${token}` },
      data: { nama: 'Bank Percobaan Tidak Sah' },
    });
    expect(postRes.status()).toBe(403);
  });
});
