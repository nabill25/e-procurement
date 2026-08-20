const express = require('express');
const router  = express.Router();
const bcrypt  = require('bcrypt');
const { pool } = require('../db');

// ── GET /api/users/roles — Daftar semua role yang dikenal sistem ──
router.get('/roles', async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM role_definitions WHERE is_active = true ORDER BY label ASC');
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/users — Daftar user internal (bukan vendor), beserta role yang dimiliki ──
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
router.post('/', async (req, res) => {
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
router.post('/:id/roles', async (req, res) => {
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
router.delete('/:id/roles/:roleKey', async (req, res) => {
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

module.exports = router;
