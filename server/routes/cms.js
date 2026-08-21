const express = require('express');
const router  = express.Router();
const { pool } = require('../db');
const multer  = require('multer');
const path    = require('path');
const fs      = require('fs');

// ── Konfigurasi Multer (gambar banner) ──
const storage = multer.diskStorage({
  destination: function (req, file, cb) {
    const dir = path.join(__dirname, '..', 'uploads');
    if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
    cb(null, dir);
  },
  filename: function (req, file, cb) {
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
    cb(null, 'banner-' + uniqueSuffix + path.extname(file.originalname));
  }
});
const upload = multer({ storage: storage });

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

// ── BANNER (carousel halaman utama) ──────────────────────────────────────────
// Meniru struktur dasar tabel BANNER eProc lama (nama+gambar), ditambah link_url dan
// is_active yang tidak ada di sistem lama tapi disepakati ditambahkan sebagai perbaikan.

// GET /api/cms/banners — Publik, hanya yang aktif
router.get('/banners', async (req, res) => {
  try {
    const result = await pool.query(
      'SELECT * FROM cms_banners WHERE is_active = true ORDER BY created_at DESC'
    );
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// GET /api/cms/banners/all — Admin, semua banner termasuk yang nonaktif
router.get('/banners/all', async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM cms_banners ORDER BY created_at DESC');
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// POST /api/cms/banners — Admin, tambah banner baru (upload gambar)
router.post('/banners', upload.single('gambar'), async (req, res) => {
  try {
    const { nama, link_url, created_by } = req.body;
    if (!nama || !req.file) {
      return res.status(400).json({ success: false, message: 'Nama dan gambar banner wajib diisi.' });
    }
    const gambar_path = `/uploads/${req.file.filename}`;

    const result = await pool.query(`
      INSERT INTO cms_banners (nama, gambar_path, link_url, created_by)
      VALUES ($1, $2, $3, $4)
      RETURNING *
    `, [nama, gambar_path, link_url || null, created_by || null]);

    res.status(201).json({ success: true, message: 'Banner berhasil ditambahkan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// PATCH /api/cms/banners/:id/toggle — Admin, aktifkan/nonaktifkan tanpa hapus
router.patch('/banners/:id/toggle', async (req, res) => {
  try {
    const result = await pool.query(
      'UPDATE cms_banners SET is_active = NOT is_active WHERE id = $1 RETURNING *',
      [req.params.id]
    );
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Banner tidak ditemukan.' });
    res.json({ success: true, message: 'Status banner berhasil diubah.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// DELETE /api/cms/banners/:id — Admin, hapus banner permanen
router.delete('/banners/:id', async (req, res) => {
  try {
    const result = await pool.query('DELETE FROM cms_banners WHERE id = $1 RETURNING id', [req.params.id]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Banner tidak ditemukan.' });
    res.json({ success: true, message: 'Banner berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── KEBIJAKAN ─────────────────────────────────────────────────────────────────
// Tabel KEBIJAKAN di eProc lama tidak pernah punya controller (kode mati total), tapi
// skemanya (title, text, jenis) dipakai sebagai acuan karena memang disiapkan di database
// asli. Kolom "jenis" mendukung multi-halaman kebijakan (Kebijakan Privasi, Syarat &
// Ketentuan, dst), bukan cuma satu halaman tunggal.

// GET /api/cms/policies — Publik, hanya yang dipublikasikan
router.get('/policies', async (req, res) => {
  try {
    const result = await pool.query(
      'SELECT * FROM cms_policies WHERE is_published = true ORDER BY created_at ASC'
    );
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// GET /api/cms/policies/all — Admin, semua kebijakan termasuk draft
router.get('/policies/all', async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM cms_policies ORDER BY created_at ASC');
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// POST /api/cms/policies — Admin, tambah kebijakan baru
router.post('/policies', async (req, res) => {
  try {
    const { title, content, jenis, is_published, created_by } = req.body;
    if (!title || !content) return res.status(400).json({ success: false, message: 'title dan content wajib diisi.' });

    const result = await pool.query(`
      INSERT INTO cms_policies (title, content, jenis, is_published, created_by)
      VALUES ($1, $2, COALESCE($3, 'umum'), COALESCE($4, true), $5)
      RETURNING *
    `, [title, content, jenis, is_published, created_by || null]);

    res.status(201).json({ success: true, message: 'Kebijakan berhasil ditambahkan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// PUT /api/cms/policies/:id — Admin, ubah kebijakan
router.put('/policies/:id', async (req, res) => {
  try {
    const { title, content, jenis, is_published } = req.body;
    const result = await pool.query(`
      UPDATE cms_policies
      SET title = COALESCE($1, title), content = COALESCE($2, content),
          jenis = COALESCE($3, jenis), is_published = COALESCE($4, is_published),
          updated_at = CURRENT_TIMESTAMP
      WHERE id = $5
      RETURNING *
    `, [title || null, content || null, jenis || null, is_published, req.params.id]);

    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Kebijakan tidak ditemukan.' });
    res.json({ success: true, message: 'Kebijakan berhasil diperbarui.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// DELETE /api/cms/policies/:id — Admin, hapus kebijakan
router.delete('/policies/:id', async (req, res) => {
  try {
    const result = await pool.query('DELETE FROM cms_policies WHERE id = $1 RETURNING id', [req.params.id]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Kebijakan tidak ditemukan.' });
    res.json({ success: true, message: 'Kebijakan berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
