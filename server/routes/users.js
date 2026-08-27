const crypto  = require('crypto');
const express = require('express');
const router  = express.Router();
const bcrypt  = require('bcrypt');
const { pool } = require('../db');
const { requireRole } = require('../lib/authMiddleware');
const requireAdmin = requireRole('admin');

// ── GET /api/users/roles — Daftar semua role yang dikenal sistem (dipakai juga PPK untuk
// dropdown, misal isi PIC tahap kontrak - bukan cuma admin) ──
router.get('/roles', async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM role_definitions WHERE is_active = true ORDER BY label ASC');
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/users — Daftar user internal (bukan vendor), beserta role yang dimiliki.
// Dipakai halaman admin Manajemen User, TAPI juga dipakai PPK untuk isi dropdown PIC di
// tab Kontrak (ContractWorkflowSections.jsx) - jadi cukup login, tidak admin-only. ──
router.get('/', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT u.id, u.username, u.full_name, u.email, u.role AS active_role, u.role_label, u.status, u.created_at
      FROM users u
      WHERE u.role != 'vendor'
      ORDER BY u.full_name ASC
    `);

    const roles = await pool.query(`
      SELECT ur.user_id, ur.role_key, rd.label, ur.level, ur.is_primary
      FROM user_roles ur
      JOIN role_definitions rd ON rd.role_key = ur.role_key
    `);

    const data = result.rows.map(u => ({
      ...u,
      roles: roles.rows.filter(r => r.user_id === u.id),
    }));

    res.json({ success: true, data });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/users — Buat akun staff internal baru (Admin) ──
router.post('/', requireAdmin, async (req, res) => {
  try {
    const { username, password, full_name, email, role_key } = req.body;
    if (!username || !password || !full_name || !role_key) {
      return res.status(400).json({ success: false, message: 'username, password, full_name, dan role_key wajib diisi.' });
    }

    const roleInfo = await pool.query('SELECT label FROM role_definitions WHERE role_key = $1', [role_key]);
    if (!roleInfo.rows.length) return res.status(400).json({ success: false, message: 'Role tidak dikenal.' });

    const password_hash = await bcrypt.hash(password, 10);

    const client = await pool.connect();
    try {
      await client.query('BEGIN');
      const userResult = await client.query(`
        INSERT INTO users (username, password, full_name, email, role, role_label, status)
        VALUES ($1, $2, $3, $4, $5, $6, 'aktif')
        RETURNING id
      `, [username, password_hash, full_name, email || null, role_key, roleInfo.rows[0].label]);

      const userId = userResult.rows[0].id;

      await client.query(`
        INSERT INTO user_roles (user_id, role_key, is_primary) VALUES ($1, $2, true)
      `, [userId, role_key]);

      await client.query('COMMIT');

      await pool.query(
        `INSERT INTO audit_logs (action, entity_type, description, is_success) VALUES ('CREATE', 'User', $1, true)`,
        [`Akun staff baru dibuat: ${full_name} (${role_key})`]
      );

      res.status(201).json({ success: true, message: 'Akun staff berhasil dibuat.', data: { id: userId } });
    } catch (e) {
      await client.query('ROLLBACK');
      if (e.code === '23505') return res.status(409).json({ success: false, message: 'Username atau email sudah dipakai.' });
      throw e;
    } finally {
      client.release();
    }
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/users/:id/roles — Tambah role tambahan untuk satu user (Admin) ──
router.post('/:id/roles', requireAdmin, async (req, res) => {
  try {
    const { role_key, level } = req.body;
    if (!role_key) return res.status(400).json({ success: false, message: 'role_key wajib diisi.' });

    const roleInfo = await pool.query('SELECT label FROM role_definitions WHERE role_key = $1', [role_key]);
    if (!roleInfo.rows.length) return res.status(400).json({ success: false, message: 'Role tidak dikenal.' });

    await pool.query(`
      INSERT INTO user_roles (user_id, role_key, level, is_primary)
      VALUES ($1, $2, $3, false)
      ON CONFLICT (user_id, role_key) DO UPDATE SET level = EXCLUDED.level
    `, [req.params.id, role_key, level || null]);

    res.status(201).json({ success: true, message: `Role ${roleInfo.rows[0].label} berhasil ditambahkan ke akun ini.` });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── DELETE /api/users/:id/roles/:roleKey — Cabut satu role dari user (Admin) ──
// Tidak boleh menghapus role kalau itu satu-satunya role yang tersisa.
router.delete('/:id/roles/:roleKey', requireAdmin, async (req, res) => {
  try {
    const countResult = await pool.query('SELECT COUNT(*) AS cnt FROM user_roles WHERE user_id = $1', [req.params.id]);
    if (parseInt(countResult.rows[0].cnt) <= 1) {
      return res.status(400).json({ success: false, message: 'Tidak bisa menghapus role terakhir. Akun harus punya minimal 1 role.' });
    }

    const result = await pool.query(
      'DELETE FROM user_roles WHERE user_id = $1 AND role_key = $2 RETURNING id',
      [req.params.id, req.params.roleKey]
    );
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Role tidak ditemukan pada akun ini.' });

    res.json({ success: true, message: 'Role berhasil dicabut dari akun ini.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/users/login-logs — Riwayat login SEMUA akun (Admin) ───────────────
router.get('/login-logs', requireAdmin, async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT l.id, l.user_id, l.username, l.ip_address, l.user_agent, l.is_active, l.login_at, l.logout_at,
             u.full_name, u.role_label
      FROM user_login_logs l
      LEFT JOIN users u ON u.id = l.user_id
      ORDER BY l.login_at DESC LIMIT 200
    `);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── API KEY: integrasi pihak ketiga (Admin) ─────────────────────────────────────
// Di sistem lama pengelolaan key dilakukan manual langsung ke database (tidak ada UI).
// Sistem baru menambahkan UI ini sebagai perbaikan, mengikuti struktur tabel KEY asli.
router.get('/api-keys', requireAdmin, async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM api_keys ORDER BY created_at DESC');
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/api-keys', requireAdmin, async (req, res) => {
  try {
    const { client_name } = req.body;
    if (!client_name) return res.status(400).json({ success: false, message: 'Nama klien wajib diisi.' });

    const apiKey = crypto.randomBytes(24).toString('hex');
    const result = await pool.query(
      `INSERT INTO api_keys (api_key, client_name, created_by) VALUES ($1, $2, $3) RETURNING *`,
      [apiKey, client_name, req.body.created_by || null]
    );
    res.status(201).json({ success: true, message: 'API key berhasil dibuat.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.patch('/api-keys/:id/toggle', requireAdmin, async (req, res) => {
  try {
    const result = await pool.query(
      `UPDATE api_keys SET is_active = NOT is_active WHERE id = $1 RETURNING *`,
      [req.params.id]
    );
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Key tidak ditemukan.' });
    res.json({ success: true, message: 'Status key berhasil diubah.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/api-keys/:id', requireAdmin, async (req, res) => {
  try {
    await pool.query('DELETE FROM api_keys WHERE id = $1', [req.params.id]);
    res.json({ success: true, message: 'API key berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.get('/api-keys/:id/requests', requireAdmin, async (req, res) => {
  try {
    const result = await pool.query(
      `SELECT * FROM api_key_requests WHERE api_key_id = $1 ORDER BY requested_at DESC LIMIT 100`,
      [req.params.id]
    );
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
