const express = require('express');
const router  = express.Router();
const bcrypt  = require('bcrypt');
const jwt     = require('jsonwebtoken');
const { pool } = require('../db');

const JWT_SECRET  = process.env.JWT_SECRET  || 'dpbj_ui_super_secret_2025_change_in_production';
const JWT_EXPIRES = process.env.JWT_EXPIRES_IN || '8h';

// ── Middleware: verifikasi JWT ────────────────────────────────────────────────
function requireAuth(req, res, next) {
  const authHeader = req.headers['authorization'];
  const token = authHeader && authHeader.split(' ')[1]; // Bearer <token>
  if (!token) {
    return res.status(401).json({ success: false, message: 'Token tidak ditemukan. Silakan login.' });
  }
  try {
    const decoded = jwt.verify(token, JWT_SECRET);
    req.user = decoded;
    next();
  } catch (err) {
    return res.status(401).json({ success: false, message: 'Token tidak valid atau sudah kadaluarsa.' });
  }
}

// ── POST /api/auth/login ──────────────────────────────────────────────────────
// Mengikuti alur Auth.php eProc: validasi username/password → set session/token
router.post('/login', async (req, res) => {
  try {
    const { username, password } = req.body;

    if (!username || !password) {
      return res.status(400).json({ success: false, message: 'Username dan password wajib diisi.' });
    }

    // Cari user berdasarkan username atau email — mengikuti eProc (user_login field)
    const result = await pool.query(
      `SELECT id, username, full_name, email, password AS password_hash, role, status, role_label
       FROM users
       WHERE (username = $1 OR email = $1)
       LIMIT 1`,
      [username]
    );

    if (!result.rows.length) {
      return res.status(401).json({ success: false, message: 'Username atau password salah.' });
    }

    const user = result.rows[0];

    // Cek status aktif — mengikuti eProc (user_aktif !== '1')
    if (user.status !== 'aktif') {
      return res.status(401).json({ success: false, message: 'Akun Anda belum aktif atau telah dinonaktifkan.' });
    }

    // Verifikasi password — mengikuti eProc (password_verify PHP = bcrypt)
    let passwordValid = false;
    if (user.password_hash) {
      try {
        passwordValid = await bcrypt.compare(password, user.password_hash);
      } catch {
        // Abaikan jika bcrypt gagal, password tetap invalid
      }
    }

    if (!passwordValid) {
      return res.status(401).json({ success: false, message: 'Username atau password salah.' });
    }

    // Buat JWT — menyimpan info user seperti eProc session
    const payload = {
      id:        user.id,
      username:  user.username,
      nama:      user.full_name,
      role:      user.role || 'ppk',
      role_label:user.role_label || '',
      email:     user.email || '',
    };
    const token = jwt.sign(payload, JWT_SECRET, { expiresIn: JWT_EXPIRES });

    // Log aktivitas login
    try {
      await pool.query(
        `INSERT INTO audit_logs (action, entity_type, description, user_id, is_success)
         VALUES ('LOGIN', 'User', $1, $2, true)`,
        [`Login berhasil: ${user.username}`, user.id]
      );
    } catch (_) { /* audit log tidak blok login */ }

    return res.json({
      success: true,
      message: 'Login berhasil.',
      token,
      user: payload,
    });

  } catch (err) {
    console.error('[AUTH LOGIN]', err);
    res.status(500).json({ success: false, message: 'Terjadi kesalahan server saat login.' });
  }
});

// ── POST /api/auth/register ───────────────────────────────────────────────────
// Mendaftar akun vendor baru
router.post('/register', async (req, res) => {
  try {
    const { company_name, npwp, email, password, username, company_type } = req.body;
    
    if (!company_name || !npwp || !email || !password || !username) {
      return res.status(400).json({ success: false, message: 'Semua kolom wajib diisi.' });
    }

    // 1. Cek apakah email, username atau npwp sudah digunakan
    const checkDuplicate = await pool.query(`
      SELECT username FROM users WHERE email = $1 OR username = $2
      UNION
      SELECT npwp FROM vendors WHERE npwp = $3
    `, [email, username, npwp]);

    if (checkDuplicate.rows.length > 0) {
      return res.status(409).json({ success: false, message: 'Email, Username, atau NPWP sudah terdaftar.' });
    }

    // 2. Hash password
    const saltRounds = 10;
    const password_hash = await bcrypt.hash(password, saltRounds);

    // Mulai transaksi
    await pool.query('BEGIN');

    // 3. Insert ke tabel users
    const userResult = await pool.query(`
      INSERT INTO users (username, password, full_name, email, role, role_label, status)
      VALUES ($1, $2, $3, $4, 'vendor', 'Vendor / Penyedia', 'aktif')
      RETURNING id
    `, [username, password_hash, company_name, email]);
    
    const userId = userResult.rows[0].id;

    // 4. Insert ke tabel vendors dengan status 'pending'
    await pool.query(`
      INSERT INTO vendors (user_id, company_name, npwp, email, company_type, status)
      VALUES ($1, $2, $3, $4, $5, 'pending')
    `, [userId, company_name, npwp, email, company_type]);

    // Commit transaksi
    await pool.query('COMMIT');

    // Catat log
    await pool.query(
      `INSERT INTO audit_logs (action, entity_type, description, user_id, is_success) VALUES ('CREATE', 'Vendor', $1, $2, true)`,
      [`Pendaftaran mandiri vendor: ${company_name}`, userId]
    );

    res.status(201).json({ success: true, message: 'Pendaftaran berhasil. Silakan login untuk melanjutkan verifikasi akun.' });
  } catch (err) {
    await pool.query('ROLLBACK');
    console.error('[AUTH REGISTER]', err);
    res.status(500).json({ success: false, message: 'Terjadi kesalahan server saat mendaftar.' });
  }
});

// ── GET /api/auth/me ──────────────────────────────────────────────────────────
// Verifikasi token yang tersimpan di client — seperti eProc Auth.php::me()
router.get('/me', requireAuth, async (req, res) => {
  try {
    // Ambil data terbaru dari database
    const result = await pool.query(
      'SELECT id, username, full_name, email, role, unit_kerja, status FROM users WHERE id = $1',
      [req.user.id]
    );

    if (!result.rows.length) {
      return res.status(401).json({ success: false, message: 'User tidak ditemukan.' });
    }

    const user = result.rows[0];
    if (user.status !== 'aktif') {
      return res.status(401).json({ success: false, message: 'Akun tidak aktif.' });
    }

    return res.json({
      success: true,
      user: {
        id:       user.id,
        username: user.username,
        nama:     user.full_name,
        role:     user.role || 'ppk',
        unit:     user.unit_kerja || '',
        email:    user.email || '',
      },
    });
  } catch (err) {
    console.error('[AUTH ME]', err);
    res.status(500).json({ success: false, message: 'Gagal memverifikasi sesi.' });
  }
});

// ── POST /api/auth/logout ─────────────────────────────────────────────────────
// Stateless JWT: client hapus token. Server catat log — seperti eProc Auth.php::logout()
router.post('/logout', requireAuth, async (req, res) => {
  try {
    await pool.query(
      `INSERT INTO audit_logs (action, entity_type, description, user_id, is_success)
       VALUES ('LOGIN', 'User', $1, $2, true)`,
      [`Logout: ${req.user.username}`, req.user.id]
    );
  } catch (_) { /* tidak blok logout */ }

  return res.json({ success: true, message: 'Logout berhasil.' });
});

module.exports = { router, requireAuth };
