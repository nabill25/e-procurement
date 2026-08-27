// Helper bersama untuk semua test e2e - login lewat UI (bukan lewat API langsung) supaya
// test ini juga otomatis memvalidasi form login itu sendiri setiap kali dijalankan.
const ACCOUNTS = {
  admin: { username: 'admin@ui.ac.id', password: 'UIAdmin2026!' },
  ppk: { username: 'ppk@ui.ac.id', password: 'UIPPK2026!' },
  pokja: { username: 'pokja@ui.ac.id', password: 'UIPokja2026!' },
  vendor: { username: 'vendor@gmail.com', password: 'UIVendor2026!' },
};

async function loginAs(page, role) {
  const acc = ACCOUNTS[role];
  if (!acc) throw new Error(`Role tidak dikenal: ${role}`);

  await page.goto('/');
  const loginTrigger = page.getByRole('button', { name: /^masuk$|^login$/i }).first();
  if (await loginTrigger.isVisible().catch(() => false)) {
    await loginTrigger.click();
  }

  await page.getByPlaceholder('Username').fill(acc.username);
  await page.locator('input[type="password"]').fill(acc.password);

  // CAPTCHA: baca kode yang ditampilkan lalu ketik ulang persis (case-insensitive di validasi)
  const captchaText = await page.locator('span.font-black.text-2xl').first().textContent();
  await page.getByPlaceholder('ketik kode').fill((captchaText || '').trim());

  await page.locator('form button[type="submit"]').click();

  // Tunggu sampai sidebar/dashboard muncul (tanda login berhasil), atau modal ganti-role muncul.
  // Modal ganti-role (untuk akun multi-role) tidak menutup lewat Escape, jadi klik opsi role
  // pertama yang tersedia supaya modal tertutup dan role utama tetap aktif.
  await page.waitForTimeout(1500);
  const roleSwitcher = page.getByText(/pilih role aktif/i);
  if (await roleSwitcher.isVisible().catch(() => false)) {
    const firstOption = page.locator('button', { hasText: /./ }).filter({ has: page.locator('p.font-semibold') }).first();
    await firstOption.click({ timeout: 3000 }).catch(() => {});
    await page.waitForTimeout(500);
  }
}

async function dismissRoleSwitcherIfPresent(page) {
  const roleSwitcher = page.getByText(/pilih role aktif/i);
  if (await roleSwitcher.isVisible().catch(() => false)) {
    const firstOption = page.locator('button', { hasText: /./ }).filter({ has: page.locator('p.font-semibold') }).first();
    await firstOption.click({ timeout: 3000 }).catch(() => {});
    await page.waitForTimeout(500);
  }
}

function collectConsoleErrors(page) {
  const errors = [];
  page.on('console', (msg) => {
    if (msg.type() === 'error') errors.push(msg.text());
  });
  page.on('pageerror', (err) => {
    errors.push(err.message);
  });
  return errors;
}

module.exports = { ACCOUNTS, loginAs, collectConsoleErrors, dismissRoleSwitcherIfPresent };
