const express = require('express');
const router  = express.Router();
const { pool } = require('../db');
const { createUpload, handleUploadError } = require('../lib/upload');
const { requireAuth, requireRole } = require('../lib/authMiddleware');

// ── Konfigurasi Multer (upload file SK) ──
const upload = createUpload('blacklist');

// ── GET /api/blacklist — Daftar perusahaan yang di-blacklist (publik) ──
router.get('/', async (req, res) => {
  try {
    const { search, page = 1, limit = 50 } = req.query;
    const offset = (parseInt(page) - 1) * parseInt(limit);

    let sql = `SELECT * FROM blacklist WHERE 1=1`;
    const params = [];
    let idx = 1;

    if (search) {
      sql += ` AND (company_name ILIKE $${idx} OR npwp ILIKE $${idx})`;
      params.push(`%${search}%`);
      idx++;
    }

    sql += ` ORDER BY start_date DESC LIMIT $${idx++} OFFSET $${idx++}`;
    params.push(parseInt(limit), offset);

    const result = await pool.query(sql, params);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/blacklist/:id — Detail satu entri blacklist ──
router.get('/:id', async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM blacklist WHERE id = $1', [req.params.id]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Data tidak ditemukan.' });
    res.json({ success: true, data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/blacklist — Tambah entri blacklist manual (Admin) ──
router.post('/', requireAuth, requireRole('admin'), upload.single('sk_file'), async (req, res) => {
  try {
    const { vendor_id, company_name, npwp, address, city, start_date, end_date, sk_number, reason, created_by } = req.body;

    if (!company_name || !reason) {
      return res.status(400).json({ success: false, message: 'company_name dan reason wajib diisi.' });
    }

    const sk_file_path = req.file ? `/uploads/${req.file.filename}` : null;

    const result = await pool.query(`
      INSERT INTO blacklist (vendor_id, company_name, npwp, address, city, start_date, end_date, sk_number, sk_file_path, reason, created_by)
      VALUES ($1, $2, $3, $4, $5, COALESCE($6, CURRENT_DATE), $7, $8, $9, $10, $11)
      RETURNING *
    `, [vendor_id || null, company_name, npwp || null, address || null, city || null,
        start_date || null, end_date || null, sk_number || null, sk_file_path, reason, created_by || null]);

    await pool.query(
      `INSERT INTO audit_logs (action, entity_type, description, is_success) VALUES ('CREATE', 'Blacklist', $1, true)`,
      [`Perusahaan ditambahkan ke Daftar Hitam: ${company_name}`]
    );

    res.status(201).json({ success: true, message: 'Berhasil ditambahkan ke Daftar Hitam.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
