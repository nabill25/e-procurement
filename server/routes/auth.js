const express = require('express');
const router  = express.Router();
const bcrypt  = require('bcrypt');
const jwt     = require('jsonwebtoken');
const crypto  = require('crypto');
const { pool } = require('../db');
const { sendMail } = require('../lib/mailer');

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
       WHERE (username = $1 OR email = $1) AND deleted_at IS NULL
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

    // Catat riwayat login (IP, browser, waktu) - mengikuti USER_LOGIN_LOGS di eProc lama
    try {
      const ip = req.headers['x-forwarded-for'] || req.socket.remoteAddress || '';
      const userAgent = req.headers['user-agent'] || '';
      await pool.query(
        `INSERT INTO user_login_logs (user_id, username, ip_address, user_agent)
         VALUES ($1, $2, $3, $4)`,
        [user.id, user.username, ip, userAgent]
      );
    } catch (_) { /* tidak blok login */ }

    // Ambil semua role yang dimiliki akun ini (mengikuti konsep USER_LOGIN_MULTI di eProc lama:
    // satu akun bisa punya lebih dari satu role, dan bisa memilih/ganti role aktif)
    let availableRoles = [];
    try {
      const rolesResult = await pool.query(`
        SELECT ur.role_key, rd.label, ur.level, ur.is_primary
        FROM user_roles ur
        JOIN role_definitions rd ON rd.role_key = ur.role_key
        WHERE ur.user_id = $1
        ORDER BY ur.is_primary DESC, rd.label ASC
      `, [user.id]);
      availableRoles = rolesResult.rows;
    } catch (_) { /* tabel role multi belum ada / gagal ambil, tidak blok login */ }

    return res.json({
      success: true,
      message: 'Login berhasil.',
      token,
      user: payload,
      available_roles: availableRoles,
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

    if (password.length < 8) {
      return res.status(400).json({ success: false, message: 'Password minimal 8 karakter.' });
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
      return res.status(400).json({ success: false, message: 'Format email tidak valid.' });
    }

    const npwpDigits = npwp.replace(/\D/g, '');
    if (npwpDigits.length !== 15 && npwpDigits.length !== 16) {
      return res.status(400).json({ success: false, message: 'NPWP harus berupa 15 digit (format lama) atau 16 digit (format baru).' });
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

    // Email selamat datang - meniru email/registrasi_rekanan.php di sistem lama (berisi ringkasan
    // data pendaftaran). Tidak memblokir respons kalau gagal terkirim (SMTP belum dikonfigurasi dsb).
    sendMail({
      to: email,
      subject: 'Registrasi - Sistem e-Procurement DPBJ Universitas Indonesia',
      html: `
        <p>Yth. ${company_name},</p>
        <p>Anda telah berhasil melakukan registrasi pada Sistem e-Procurement Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia.</p>
        <table style="border-collapse:collapse">
          <tr><td style="padding:4px 8px;border:1px solid #eee">Nama Perusahaan</td><td style="padding:4px 8px;border:1px solid #eee">${company_name}</td></tr>
          <tr><td style="padding:4px 8px;border:1px solid #eee">Username</td><td style="padding:4px 8px;border:1px solid #eee">${username}</td></tr>
          <tr><td style="padding:4px 8px;border:1px solid #eee">Email Terdaftar</td><td style="padding:4px 8px;border:1px solid #eee">${email}</td></tr>
        </table>
        <p>Langkah selanjutnya: lengkapi dokumen legalitas dan data kualifikasi perusahaan Anda lewat menu "Profil & Kualifikasi", lalu tunggu proses verifikasi oleh Admin DPBJ sebelum dapat mengikuti tender.</p>
        <p>Terima kasih telah mendaftar.<br/>Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia</p>
      `,
    }).catch(err => console.error('[REGISTER MAIL]', err));

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

// ── GET /api/auth/my-roles ──────────────────────────────────────────────────────
// Daftar role yang dimiliki akun yang sedang login (untuk tombol "Ganti Role")
router.get('/my-roles', requireAuth, async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT ur.role_key, rd.label, ur.level, ur.is_primary
      FROM user_roles ur
      JOIN role_definitions rd ON rd.role_key = ur.role_key
      WHERE ur.user_id = $1
      ORDER BY ur.is_primary DESC, rd.label ASC
    `, [req.user.id]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/auth/switch-role ────────────────────────────────────────────────
// Ganti role aktif untuk akun yang sedang login — mengikuti excSplitRole() di eProc lama:
// role yang dipilih disalin jadi role aktif di akun utama, dicatat riwayatnya, lalu token baru
// diterbitkan supaya seluruh aplikasi langsung memakai role baru itu (tanpa perlu logout).
router.post('/switch-role', requireAuth, async (req, res) => {
  try {
    const { role_key } = req.body;
    if (!role_key) return res.status(400).json({ success: false, message: 'role_key wajib diisi.' });

    // Pastikan role ini memang dimiliki akun ini
    const owned = await pool.query('SELECT 1 FROM user_roles WHERE user_id = $1 AND role_key = $2', [req.user.id, role_key]);
    if (!owned.rows.length) {
      return res.status(403).json({ success: false, message: 'Anda tidak memiliki role tersebut.' });
    }

    const roleInfo = await pool.query('SELECT label FROM role_definitions WHERE role_key = $1', [role_key]);
    const roleLabel = roleInfo.rows.length ? roleInfo.rows[0].label : role_key;

    const oldRole = req.user.role;

    await pool.query('UPDATE users SET role = $1, role_label = $2 WHERE id = $3', [role_key, roleLabel, req.user.id]);
    await pool.query(
      'INSERT INTO user_role_switch_history (user_id, role_old, role_new) VALUES ($1, $2, $3)',
      [req.user.id, oldRole, role_key]
    );
    await pool.query(
      `INSERT INTO audit_logs (action, entity_type, description, user_id, is_success) VALUES ('UPDATE', 'User', $1, $2, true)`,
      [`Ganti role aktif dari ${oldRole} ke ${role_key}`, req.user.id]
    );

    const result = await pool.query('SELECT id, username, full_name, email FROM users WHERE id = $1', [req.user.id]);
    const user = result.rows[0];

    const payload = {
      id: user.id, username: user.username, nama: user.full_name,
      role: role_key, role_label: roleLabel, email: user.email || '',
    };
    const token = jwt.sign(payload, JWT_SECRET, { expiresIn: JWT_EXPIRES });

    res.json({ success: true, message: `Role aktif berhasil diganti ke ${roleLabel}.`, token, user: payload });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
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

  // Tutup sesi login yang masih tercatat aktif untuk akun ini
  try {
    await pool.query(
      `UPDATE user_login_logs SET is_active = false, logout_at = CURRENT_TIMESTAMP
       WHERE user_id = $1 AND is_active = true`,
      [req.user.id]
    );
  } catch (_) { /* tidak blok logout */ }

  return res.json({ success: true, message: 'Logout berhasil.' });
});

// ── GET /api/auth/login-logs — Riwayat login akun sendiri ──────────────────────
router.get('/login-logs', requireAuth, async (req, res) => {
  try {
    const result = await pool.query(
      `SELECT id, ip_address, user_agent, is_active, login_at, logout_at
       FROM user_login_logs WHERE user_id = $1 ORDER BY login_at DESC LIMIT 50`,
      [req.user.id]
    );
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/auth/forgot-password — Minta link reset password ────────────────
// Meniru alur email/lupa_password.php di sistem lama, tapi token disimpan sebagai hash
// (bukan token mentah) dan wajib kadaluarsa (1 jam), lebih aman dari pola lama yang pakai
// md5(id."IKUN") - nilai statis yang bisa ditebak ulang kapan saja.
// Selalu balas sukses generik (tidak bocorkan apakah email terdaftar) untuk cegah enumerasi akun.
router.post('/forgot-password', async (req, res) => {
  try {
    const { email } = req.body;
    if (!email) return res.status(400).json({ success: false, message: 'Email diperlukan.' });

    const user = await pool.query('SELECT id, full_name, email FROM users WHERE email = $1 AND status != $2', [email, 'nonaktif']);
    const generic = { success: true, message: 'Kalau email tersebut terdaftar, tautan reset password sudah kami kirimkan.' };

    if (!user.rows.length) return res.json(generic);

    const rawToken = crypto.randomBytes(32).toString('hex');
    const tokenHash = crypto.createHash('sha256').update(rawToken).digest('hex');
    const expiry = new Date(Date.now() + 60 * 60 * 1000); // 1 jam

    await pool.query('UPDATE users SET reset_token = $1, reset_token_expiry = $2 WHERE id = $3', [tokenHash, expiry, user.rows[0].id]);

    const frontendUrl = process.env.FRONTEND_URL || 'http://localhost:5173';
    const resetLink = `${frontendUrl}/reset-password/${rawToken}`;

    sendMail({
      to: user.rows[0].email,
      subject: 'Reset Password - Sistem e-Procurement DPBJ Universitas Indonesia',
      html: `
        <p>Yth. ${user.rows[0].full_name},</p>
        <p>Anda telah melakukan permintaan perubahan password pada Sistem e-Procurement DPBJ Universitas Indonesia.</p>
        <p>Silakan klik tautan berikut untuk membuat password baru (berlaku 1 jam):</p>
        <p><a href="${resetLink}">${resetLink}</a></p>
        <p>Kalau Anda tidak merasa meminta ini, abaikan saja email ini - password Anda tidak akan berubah.</p>
        <p>Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia</p>
      `,
    }).catch(err => console.error('[FORGOT-PASSWORD MAIL]', err));

    res.json(generic);
  } catch (err) {
    console.error('[AUTH FORGOT-PASSWORD]', err);
    res.status(500).json({ success: false, message: 'Terjadi kesalahan server.' });
  }
});

// ── POST /api/auth/reset-password — Set password baru pakai token dari email ──
router.post('/reset-password', async (req, res) => {
  try {
    const { token, password } = req.body;
    if (!token || !password) return res.status(400).json({ success: false, message: 'Token dan password baru diperlukan.' });
    if (password.length < 8) return res.status(400).json({ success: false, message: 'Password minimal 8 karakter.' });

    const tokenHash = crypto.createHash('sha256').update(token).digest('hex');
    const user = await pool.query(
      'SELECT id FROM users WHERE reset_token = $1 AND reset_token_expiry > NOW()',
      [tokenHash]
    );
    if (!user.rows.length) {
      return res.status(400).json({ success: false, message: 'Tautan reset password tidak valid atau sudah kadaluarsa. Silakan minta tautan baru.' });
    }

    const password_hash = await bcrypt.hash(password, 10);
    await pool.query(
      'UPDATE users SET password = $1, reset_token = NULL, reset_token_expiry = NULL WHERE id = $2',
      [password_hash, user.rows[0].id]
    );

    await pool.query(
      `INSERT INTO audit_logs (action, entity_type, description, user_id, is_success) VALUES ('UPDATE', 'User', 'Reset password berhasil', $1, true)`,
      [user.rows[0].id]
    );

    res.json({ success: true, message: 'Password berhasil diubah. Silakan login dengan password baru Anda.' });
  } catch (err) {
    console.error('[AUTH RESET-PASSWORD]', err);
    res.status(500).json({ success: false, message: 'Terjadi kesalahan server.' });
  }
});

module.exports = { router, requireAuth };
