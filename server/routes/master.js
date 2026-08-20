const express = require('express');
const router  = express.Router();
const { pool } = require('../db');

// Kategori data master yang valid (mengikuti tabel referensi di eProc lama)
const VALID_CATEGORIES = ['bank', 'mata_uang', 'negara', 'satuan', 'incoterm', 'payment_method'];

function checkCategory(req, res, next) {
  if (!VALID_CATEGORIES.includes(req.params.category)) {
    return res.status(400).json({ success: false, message: `Kategori tidak dikenal. Pilihan: ${VALID_CATEGORIES.join(', ')}.` });
  }
  next();
}

// ── GET /api/master/categories — Daftar kategori yang tersedia ──
router.get('/categories', (req, res) => {
  res.json({ success: true, data: VALID_CATEGORIES });
});

// ── Unit Kerja (tabel terpisah karena datanya lebih detail: alamat, telepon, email) ──
// Didefinisikan sebelum /:category supaya path "unit-kerja" tidak ketiban rute kategori.

router.get('/unit-kerja', async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM unit_kerja_master ORDER BY nama ASC');
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/unit-kerja', async (req, res) => {
  try {
    const { kode, nama, alamat, telepon, email } = req.body;
    if (!nama) return res.status(400).json({ success: false, message: 'nama wajib diisi.' });

    const result = await pool.query(`
      INSERT INTO unit_kerja_master (kode, nama, alamat, telepon, email)
      VALUES ($1, $2, $3, $4, $5)
      RETURNING *
    `, [kode || null, nama, alamat || null, telepon || null, email || null]);

    res.status(201).json({ success: true, message: 'Unit kerja berhasil ditambahkan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/unit-kerja/:id', async (req, res) => {
  try {
    const result = await pool.query('DELETE FROM unit_kerja_master WHERE id = $1 RETURNING id', [req.params.id]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Data tidak ditemukan.' });
    res.json({ success: true, message: 'Unit kerja berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/master/:category — List data referensi ──
router.get('/:category', checkCategory, async (req, res) => {
  try {
    const result = await pool.query(
      'SELECT * FROM master_data WHERE category = $1 ORDER BY nama ASC',
      [req.params.category]
    );
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/master/:category — Tambah data referensi ──
router.post('/:category', checkCategory, async (req, res) => {
  try {
    const { kode, nama, extra } = req.body;
    if (!nama) return res.status(400).json({ success: false, message: 'nama wajib diisi.' });

    const result = await pool.query(`
      INSERT INTO master_data (category, kode, nama, extra)
      VALUES ($1, $2, $3, $4)
      RETURNING *
    `, [req.params.category, kode || null, nama, extra ? JSON.stringify(extra) : null]);

    res.status(201).json({ success: true, message: 'Data berhasil ditambahkan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── PUT /api/master/:category/:id — Ubah data referensi ──
router.put('/:category/:id', checkCategory, async (req, res) => {
  try {
    const { kode, nama, extra, is_active } = req.body;
    const result = await pool.query(`
      UPDATE master_data
      SET kode = COALESCE($1, kode),
          nama = COALESCE($2, nama),
          extra = COALESCE($3, extra),
          is_active = COALESCE($4, is_active)
      WHERE id = $5 AND category = $6
      RETURNING *
    `, [kode, nama, extra ? JSON.stringify(extra) : null, is_active, req.params.id, req.params.category]);

    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Data tidak ditemukan.' });
    res.json({ success: true, message: 'Data berhasil diperbarui.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── DELETE /api/master/:category/:id — Hapus data referensi ──
router.delete('/:category/:id', checkCategory, async (req, res) => {
  try {
    const result = await pool.query(
      'DELETE FROM master_data WHERE id = $1 AND category = $2 RETURNING id',
      [req.params.id, req.params.category]
    );
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Data tidak ditemukan.' });
    res.json({ success: true, message: 'Data berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
