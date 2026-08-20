const express = require('express');
const router  = express.Router();
const { pool } = require('../db');

// ── BERITA / PENGUMUMAN ──────────────────────────────────────────────────────

// GET /api/cms/news — Publik, hanya yang sudah dipublikasikan
router.get('/news', async (req, res) => {
  try {
    const result = await pool.query(
      'SELECT * FROM cms_news WHERE is_published = true ORDER BY created_at DESC'
    );
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// GET /api/cms/news/all — Admin, semua berita termasuk yang belum dipublikasikan
router.get('/news/all', async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM cms_news ORDER BY created_at DESC');
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// POST /api/cms/news — Admin, tambah berita baru
router.post('/news', async (req, res) => {
  try {
    const { title, content, image_url, created_by, is_published } = req.body;
    if (!title || !content) return res.status(400).json({ success: false, message: 'title dan content wajib diisi.' });

    const result = await pool.query(`
      INSERT INTO cms_news (title, content, image_url, created_by, is_published)
      VALUES ($1, $2, $3, $4, COALESCE($5, true))
      RETURNING *
    `, [title, content, image_url || null, created_by || null, is_published]);

    res.status(201).json({ success: true, message: 'Berita berhasil ditambahkan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// DELETE /api/cms/news/:id — Admin, hapus berita
router.delete('/news/:id', async (req, res) => {
  try {
    const result = await pool.query('DELETE FROM cms_news WHERE id = $1 RETURNING id', [req.params.id]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Berita tidak ditemukan.' });
    res.json({ success: true, message: 'Berita berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── FAQ ───────────────────────────────────────────────────────────────────────

// GET /api/cms/faq — Publik, hanya yang sudah dipublikasikan
router.get('/faq', async (req, res) => {
  try {
    const result = await pool.query(
      'SELECT * FROM cms_faq WHERE is_published = true ORDER BY order_index ASC, created_at ASC'
    );
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// GET /api/cms/faq/all — Admin, semua FAQ
router.get('/faq/all', async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM cms_faq ORDER BY order_index ASC, created_at ASC');
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// POST /api/cms/faq — Admin, tambah FAQ baru
router.post('/faq', async (req, res) => {
  try {
    const { question, answer, order_index } = req.body;
    if (!question || !answer) return res.status(400).json({ success: false, message: 'question dan answer wajib diisi.' });

    const result = await pool.query(`
      INSERT INTO cms_faq (question, answer, order_index)
      VALUES ($1, $2, COALESCE($3, 0))
      RETURNING *
    `, [question, answer, order_index]);

    res.status(201).json({ success: true, message: 'FAQ berhasil ditambahkan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// DELETE /api/cms/faq/:id — Admin, hapus FAQ
router.delete('/faq/:id', async (req, res) => {
  try {
    const result = await pool.query('DELETE FROM cms_faq WHERE id = $1 RETURNING id', [req.params.id]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'FAQ tidak ditemukan.' });
    res.json({ success: true, message: 'FAQ berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
