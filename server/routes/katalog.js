const express = require('express');
const router = express.Router();
const { pool } = require('../db');

// ── GET /api/katalog ──
// Mengambil semua item katalog, opsional search & filter vendor
router.get('/', async (req, res) => {
  try {
    const { search, vendor_id, limit = 50 } = req.query;
    let sql = `
      SELECT k.*, v.company_name 
      FROM katalog_items k
      JOIN vendors v ON k.vendor_id = v.user_id
      WHERE 1=1
    `;
    const params = [];
    let idx = 1;

    if (search) {
      sql += ` AND k.item_name ILIKE $${idx++}`;
      params.push(`%${search}%`);
    }
    if (vendor_id) {
      sql += ` AND k.vendor_id = $${idx++}`;
      params.push(vendor_id);
    }
    sql += ` ORDER BY k.created_at DESC LIMIT $${idx}`;
    params.push(parseInt(limit));

    const result = await pool.query(sql, params);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/katalog/:id ──
// Mengambil detail item
router.get('/:id', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT k.*, v.company_name 
      FROM katalog_items k
      JOIN vendors v ON k.vendor_id = v.user_id
      WHERE k.id = $1
    `, [req.params.id]);

    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Item tidak ditemukan.' });
    res.json({ success: true, data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/katalog ──
// Tambah item katalog (oleh vendor)
router.post('/', async (req, res) => {
  try {
    const { vendor_id, item_name, description, price, unit, image_url } = req.body;
    
    if (!vendor_id || !item_name || !price) {
      return res.status(400).json({ success: false, message: 'vendor_id, item_name, price wajib diisi.' });
    }

    const result = await pool.query(`
      INSERT INTO katalog_items (vendor_id, item_name, description, price, unit, image_url)
      VALUES ($1, $2, $3, $4, $5, $6)
      RETURNING *
    `, [vendor_id, item_name, description, price, unit || 'Pcs', image_url]);

    res.status(201).json({ success: true, message: 'Item berhasil ditambahkan ke katalog', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── DELETE /api/katalog/:id ──
// Hapus item dari katalog (oleh vendor yang bersangkutan)
router.delete('/:id', async (req, res) => {
  try {
    const result = await pool.query('DELETE FROM katalog_items WHERE id = $1 RETURNING id', [req.params.id]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Item tidak ditemukan.' });
    res.json({ success: true, message: 'Item berhasil dihapus dari katalog.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
