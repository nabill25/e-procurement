// Path env di-set eksplisit ke server/.env (bukan .env di folder tempat proses dijalankan).
// Tanpa ini, dotenv baca .env dari current working directory - kalau server dinyalakan lewat
// `npm run server` dari folder root (cara resmi yang didokumentasikan), yang kebaca malah
// .env di ROOT (cuma berisi SUPABASE_DB_URL), bukan server/.env yang berisi JWT_SECRET/SMTP dst.
require('dotenv').config({ path: require('path').join(__dirname, '.env') });
const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const rateLimit = require('express-rate-limit');
const { testConnection } = require('./db');
const { handleUploadError } = require('./lib/upload');
const { requireAuth, requireRole } = require('./lib/authMiddleware');

const app = express();
const PORT = process.env.PORT || 3001;
const IS_PROD = process.env.NODE_ENV === 'production';

// ── Keamanan: HTTP security headers ──
// crossOriginResourcePolicy dilonggarkan supaya file di /uploads (gambar produk katalog,
// dokumen tender dst) tetap bisa diakses langsung oleh frontend yang beda origin (port 5173).
app.use(helmet({ crossOriginResourcePolicy: { policy: 'cross-origin' } }));

// ── CORS: di production cuma terima FRONTEND_URL, di development tetap terbuka
// supaya tidak menghalangi kerja lokal (beda port/tunnel) ──
app.use(cors({
  origin: IS_PROD ? (process.env.FRONTEND_URL || true) : true,
  credentials: true,
}));
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
const path = require('path');
app.use('/uploads', express.static(path.join(__dirname, 'uploads')));

// ── Rate limiting: cegah brute-force di endpoint login/register ──
// 20 percobaan per 15 menit per IP di production - cukup longgar untuk pemakaian wajar
// (termasuk kalau beberapa staff berbagi IP kantor yang sama), tapi menghentikan percobaan
// tebak password otomatis. Di development/testing dilonggarkan jadi 200 supaya tidak
// menghalangi kerja normal (testing manual berulang, automated test suite yang login berkali-kali).
const authLimiter = rateLimit({
  windowMs: 15 * 60 * 1000,
  max: IS_PROD ? 20 : 200,
  standardHeaders: true,
  legacyHeaders: false,
  message: { success: false, message: 'Terlalu banyak percobaan. Silakan coba lagi dalam beberapa menit.' },
});

// Rate limit umum untuk seluruh API - jaring pengaman DoS dasar, BUKAN untuk membatasi
// pemakaian wajar. Dihitung per menit (bukan per 15 menit) supaya reset cepat kalau kena,
// dan angkanya cukup tinggi untuk menampung kantor yang berbagi 1 alamat IP dengan banyak staf
// aktif bersamaan (SPA seperti ini wajar melakukan puluhan fetch API per menit per orang saat
// navigasi normal - dashboard, sidebar, notifikasi, dst).
const apiLimiter = rateLimit({
  windowMs: 60 * 1000,
  max: 600,
  standardHeaders: true,
  legacyHeaders: false,
  message: { success: false, message: 'Terlalu banyak permintaan. Silakan coba lagi sebentar lagi.' },
});
app.use('/api', apiLimiter);

// ── Test Database Connection ──
testConnection();

// ── Routes ──
// Catatan soal proteksi requireAuth/requireRole di bawah ini: dipetakan lewat audit menyeluruh
// (Agustus 2026) ke seluruh 14 file route + seluruh pemanggil frontend, supaya proteksi tidak
// memutus fitur publik yang memang harus terbuka (landing page, verifikasi QR, form kontak,
// registrasi vendor, cek blacklist publik). File yang isinya CAMPURAN publik/privat (cms, inbox,
// qr, blacklist, katalog, master, tenders) diproteksi per-route di dalam file masing-masing,
// bukan di sini, supaya rute publiknya tetap terbuka.
const { router: authRouter } = require('./routes/auth');
app.use('/api/auth/login',    authLimiter);
app.use('/api/auth/register', authLimiter);
app.use('/api/auth',      authRouter);
app.use('/api/tenders',   require('./routes/tenders'));   // proteksi per-route di dalam file (2 route publik: GET /, GET /:id)
app.use('/api/vendors',   requireAuth, require('./routes/vendors'));   // 100% privat
app.use('/api/pengajuan', requireAuth, require('./routes/pengajuan')); // 100% privat, alur internal RUP
app.use('/api/audit',     requireAuth, require('./routes/audit'));     // 100% privat
app.use('/api/dashboard', requireAuth, require('./routes/dashboard')); // 100% privat
app.use('/api/katalog',   require('./routes/katalog'));   // proteksi per-route di dalam file (POST /reports publik)
app.use('/api/purchasing', requireAuth, require('./routes/purchasing')); // 100% privat
app.use('/api/blacklist', require('./routes/blacklist')); // proteksi per-route di dalam file (GET publik untuk transparansi)
app.use('/api/master', require('./routes/master'));       // proteksi per-route di dalam file
app.use('/api/menu', requireAuth, require('./routes/menu'));   // 100% privat (dipanggil setelah login)
app.use('/api/inbox', require('./routes/inbox'));         // proteksi per-route di dalam file (form Kontak Kami publik)
app.use('/api/cms', require('./routes/cms'));             // proteksi per-route di dalam file (konten publik)
app.use('/api/qr', require('./routes/qr'));               // proteksi per-route di dalam file (verifikasi QR publik)
app.use('/api/users', requireAuth, require('./routes/users')); // proteksi per-route di dalam file (GET / dan GET /roles dipakai PPK juga untuk dropdown staff)
app.use('/api/print', require('./routes/print'));         // 100% privat (requireAuth dipasang di dalam file sendiri)
app.use('/api/integration', require('./routes/integration')); // 100% privat, admin-only (requireRole dipasang di dalam file sendiri)

// ── Default Route / Health Check ──
app.get('/api', (req, res) => {
  res.json({ message: 'DPBJ UI E-Procurement API is running!' });
});

// ── Handle 404 ──
app.use((req, res) => {
  res.status(404).json({ success: false, message: 'Endpoint tidak ditemukan.' });
});

// ── Error handler khusus upload file (tipe/ukuran tidak valid) ──
app.use(handleUploadError);

// ── Global Error Handler ──
app.use((err, req, res, next) => {
  console.error('[SERVER ERROR]', err.stack);
  res.status(500).json({ success: false, message: 'Terjadi kesalahan internal server.' });
});

app.listen(PORT, () => {
  console.log(`🚀 Server backend berjalan di http://localhost:${PORT}`);
});
