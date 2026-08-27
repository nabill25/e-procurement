// Uji beban sederhana: simulasi banyak pengguna mengakses API bersamaan, untuk membuktikan
// server (connection pooling pg.Pool + JWT stateless) bisa menangani beban wajar tanpa error
// atau macet. Ini BUKAN pengganti uji beban skala besar sungguhan, cuma bukti awal bahwa
// arsitekturnya tidak punya penghalang fundamental untuk dipakai banyak orang bersamaan.
//
// Cara pakai: node server/load_test.js
// Backend harus sudah jalan di localhost:3001 sebelum menjalankan ini.
const autocannon = require('autocannon');

const BASE_URL = 'http://localhost:3001';

function run(opts) {
  return new Promise((resolve, reject) => {
    const instance = autocannon(opts, (err, result) => {
      if (err) return reject(err);
      resolve(result);
    });
    autocannon.track(instance, { renderProgressBar: false });
  });
}

function printSummary(label, result) {
  console.log(`\n=== ${label} ===`);
  console.log(`Total request: ${result.requests.total}`);
  console.log(`Request/detik (rata-rata): ${result.requests.average}`);
  console.log(`Latency rata-rata: ${result.latency.average} ms`);
  console.log(`Latency p99 (99% request selesai dalam waktu ini): ${result.latency.p99} ms`);
  console.log(`Error: ${result.errors}, Timeout: ${result.timeouts}`);
  console.log(`Status 2xx: ${result['2xx']}, Status non-2xx: ${result.non2xx}`);
}

async function main() {
  // Skala beban realistis: simulasi ~15 pengguna aktif bersamaan dari 1 kantor (1 alamat IP),
  // masing-masing melakukan permintaan berulang selama 8 detik - jauh di bawah batas rate
  // limiter (600 request/menit per IP) supaya hasil test ini benar-benar mengukur kesehatan
  // server, bukan sekadar memicu rate limiter (yang memang harus terpicu kalau beban SANGAT
  // ekstrem, itu perilaku yang benar, bukan bug).
  console.log('Uji beban 1: endpoint publik GET /api/tenders - 15 koneksi bersamaan selama 8 detik');
  const test1 = await run({
    url: `${BASE_URL}/api/tenders`,
    connections: 15,
    duration: 8,
    amount: 300, // batasi total request supaya tidak memicu rate limiter (600/menit)
  });
  printSummary('GET /api/tenders (publik, 15 pengguna bersamaan)', test1);

  console.log('\nUji beban 2: endpoint login - 10 koneksi bersamaan (di bawah rate limit auth development 200/15menit)');
  const test2 = await run({
    url: `${BASE_URL}/api/auth/login`,
    connections: 10,
    duration: 8,
    amount: 150,
    method: 'POST',
    headers: { 'content-type': 'application/json' },
    body: JSON.stringify({ username: 'ini-sengaja-salah', password: 'salah' }),
  });
  printSummary('POST /api/auth/login (validasi ditolak tapi server harus tetap responsif)', test2);

  console.log('\nUji beban 3: endpoint privat GET /api/dashboard TANPA token - 15 koneksi bersamaan (harus konsisten 401, bukan macet)');
  const test3 = await run({
    url: `${BASE_URL}/api/dashboard`,
    connections: 15,
    duration: 8,
    amount: 300,
  });
  printSummary('GET /api/dashboard tanpa token (harus konsisten 401)', test3);

  console.log('\n=== Kesimpulan ===');
  const test1Ok = test1.errors === 0 && test1.timeouts === 0 && test1['2xx'] === test1.requests.total;
  const test2Ok = test2.errors === 0 && test2.timeouts === 0;
  const test3Ok = test3.errors === 0 && test3.timeouts === 0 && test3.non2xx === test3.requests.total;
  const allHealthy = test1Ok && test2Ok && test3Ok;
  console.log(allHealthy
    ? 'Server tetap sehat (0 error, 0 timeout, status code sesuai ekspektasi) di ketiga skenario uji beban ini.'
    : 'ADA MASALAH: hasil tidak sesuai ekspektasi, perlu diselidiki lebih lanjut sebelum anggap siap menangani banyak pengguna.');
  if (!test1Ok) console.log(`  - Uji beban 1 (GET /api/tenders) tidak semua 2xx: ${test1['2xx']}/${test1.requests.total}`);
  if (!test3Ok) console.log(`  - Uji beban 3 (GET /api/dashboard tanpa token) tidak semua non-2xx: ${test3.non2xx}/${test3.requests.total}`);
}

main().catch((err) => {
  console.error('Uji beban gagal dijalankan:', err.message);
  process.exit(1);
});
