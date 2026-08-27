// Konfigurasi Playwright untuk automated testing sistem DPBJ UI e-Procurement.
// Menjalankan test butuh backend (npm run server, port 3001) dan frontend (npm run dev,
// port 5173) sudah aktif secara terpisah - config ini TIDAK otomatis menyalakan keduanya,
// supaya tidak bentrok dengan proses dev yang mungkin sedang berjalan.
// Jalankan test: npx playwright test
const { defineConfig, devices } = require('@playwright/test');

module.exports = defineConfig({
  testDir: './tests/e2e',
  timeout: 30 * 1000,
  expect: { timeout: 8000 },
  fullyParallel: false, // test berbagi data di database yang sama, jalankan berurutan
  retries: 0,
  workers: 1,
  reporter: [['list'], ['html', { open: 'never', outputFolder: 'tests/e2e/report' }]],
  use: {
    baseURL: 'http://localhost:5173',
    trace: 'retain-on-failure',
    screenshot: 'only-on-failure',
    actionTimeout: 8000,
  },
  projects: [
    { name: 'chromium', use: { ...devices['Desktop Chrome'] } },
  ],
});
