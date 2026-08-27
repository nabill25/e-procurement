const express = require('express');
const router  = express.Router();
const { pool } = require('../db');
const { createUpload, handleUploadError } = require('../lib/upload');
const { requireAuth, requireRole } = require('../lib/authMiddleware');
const { sendMail } = require('../lib/mailer');
const adminOnly = [requireAuth, requireRole('admin')];

// ── Konfigurasi Multer (lampiran pesan) ──
const upload = createUpload('inbox');

// ── GET /api/inbox/categories — Daftar kategori pengaduan ──
router.get('/categories', async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM inbox_categories WHERE is_active = true ORDER BY name ASC');
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/inbox — Daftar pesan masuk (Admin) ──
router.get('/', adminOnly, async (req, res) => {
  try {
    const { status } = req.query;
    let sql = `
      SELECT m.*, c.name AS category_name
      FROM inbox_messages m
      LEFT JOIN inbox_categories c ON m.category_id = c.id
      WHERE m.parent_id IS NULL
    `;
    const params = [];
    if (status) {
      sql += ` AND m.status = $1`;
      params.push(status);
    }
    sql += ` ORDER BY m.created_at DESC`;

    const result = await pool.query(sql, params);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/inbox/complain-types — Daftar subjek komplain terstruktur (dropdown publik) ──
// Didefinisikan sebelum /:id supaya path literal "meta" tidak ketiban rute /:id
router.get('/meta/complain-types', async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM inbox_complain_types WHERE is_active = true ORDER BY name ASC');
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── CRUD Kategori Komplain & Penerima Default (Admin) ──
router.post('/meta/complain-types', adminOnly, async (req, res) => {
  try {
    const { name, description } = req.body;
    if (!name) return res.status(400).json({ success: false, message: 'Nama subjek komplain wajib diisi.' });
    const result = await pool.query(
      'INSERT INTO inbox_complain_types (name, description) VALUES ($1, $2) RETURNING *',
      [name, description || null]
    );
    res.status(201).json({ success: true, message: 'Subjek komplain berhasil ditambahkan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/meta/complain-types/:id', adminOnly, async (req, res) => {
  try {
    await pool.query('UPDATE inbox_complain_types SET is_active = false WHERE id = $1', [req.params.id]);
    res.json({ success: true, message: 'Subjek komplain berhasil dinonaktifkan.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.get('/meta/complain-recipients', adminOnly, async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM inbox_complain_recipients WHERE is_active = true ORDER BY email ASC');
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/meta/complain-recipients', adminOnly, async (req, res) => {
  try {
    const { email, keterangan } = req.body;
    if (!email) return res.status(400).json({ success: false, message: 'Email penerima wajib diisi.' });
    const result = await pool.query(
      'INSERT INTO inbox_complain_recipients (email, keterangan) VALUES ($1, $2) RETURNING *',
      [email, keterangan || null]
    );
    res.status(201).json({ success: true, message: 'Penerima komplain berhasil ditambahkan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/meta/complain-recipients/:id', adminOnly, async (req, res) => {
  try {
    await pool.query('UPDATE inbox_complain_recipients SET is_active = false WHERE id = $1', [req.params.id]);
    res.json({ success: true, message: 'Penerima komplain berhasil dinonaktifkan.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/inbox/:id — Detail pesan + balasannya ──
router.get('/:id', adminOnly, async (req, res) => {
  try {
    const msg = await pool.query(`
      SELECT m.*, c.name AS category_name
      FROM inbox_messages m
      LEFT JOIN inbox_categories c ON m.category_id = c.id
      WHERE m.id = $1
    `, [req.params.id]);

    if (!msg.rows.length) return res.status(404).json({ success: false, message: 'Pesan tidak ditemukan.' });

    const replies = await pool.query(`
      SELECT m.*, u.full_name AS replied_by_name
      FROM inbox_messages m
      LEFT JOIN users u ON m.sender_id = u.id
      WHERE m.parent_id = $1
      ORDER BY m.created_at ASC
    `, [req.params.id]);

    res.json({ success: true, data: { ...msg.rows[0], replies: replies.rows } });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/inbox — Kirim pesan/pengaduan baru (Publik, dari form Kontak Kami) ──
// complain_type_id opsional: kalau diisi, berarti pesan ini komplain terstruktur (subjek
// dipilih dari daftar inbox_complain_types), mengikuti pola INBOX_COMPLAIN_TYPE di eProc lama
router.post('/', upload.single('attachment'), async (req, res) => {
  try {
    const { category_id, subject, content, sender_name, sender_email, sender_phone, complain_type_id } = req.body;

    if (!subject || !content || !sender_name || !sender_email) {
      return res.status(400).json({ success: false, message: 'Nama, email, subyek, dan pesan wajib diisi.' });
    }

    const attachment_path = req.file ? `/uploads/${req.file.filename}` : null;

    const result = await pool.query(`
      INSERT INTO inbox_messages (category_id, subject, content, attachment_path, sender_name, sender_email, sender_phone, complain_type_id)
      VALUES ($1, $2, $3, $4, $5, $6, $7, $8)
      RETURNING *
    `, [category_id || null, subject, content, attachment_path, sender_name, sender_email, sender_phone || null, complain_type_id || null]);

    res.status(201).json({ success: true, message: 'Pesan berhasil dikirim. Terima kasih, kami akan merespons segera.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── PATCH /api/inbox/:id/read — Tandai pesan sudah dibaca (Admin) ──
router.patch('/:id/read', adminOnly, async (req, res) => {
  try {
    const { read_by } = req.body;
    const result = await pool.query(`
      UPDATE inbox_messages
      SET status = 'dibaca', read_by = $1, read_at = CURRENT_TIMESTAMP
      WHERE id = $2 AND status = 'belum_dibaca'
      RETURNING *
    `, [read_by || null, req.params.id]);

    res.json({ success: true, message: 'Pesan ditandai sudah dibaca.', data: result.rows[0] || null });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/inbox/:id/reply — Balas pesan (Admin) ──
router.post('/:id/reply', adminOnly, async (req, res) => {
  try {
    const { content, replied_by } = req.body;
    if (!content) return res.status(400).json({ success: false, message: 'Isi balasan wajib diisi.' });

    const original = await pool.query('SELECT subject, sender_name, sender_email FROM inbox_messages WHERE id = $1', [req.params.id]);
    if (!original.rows.length) return res.status(404).json({ success: false, message: 'Pesan tidak ditemukan.' });

    const reply = await pool.query(`
      INSERT INTO inbox_messages (subject, content, sender_id, sender_name, sender_email, parent_id, status)
      VALUES ('Re: Balasan', $1, $2, 'DPBJ UI', 'noreply@dpbj.ui.ac.id', $3, 'dibaca')
      RETURNING *
    `, [content, replied_by || null, req.params.id]);

    await pool.query(`UPDATE inbox_messages SET status = 'dibalas' WHERE id = $1`, [req.params.id]);

    let mailResult = { sent: false, reason: 'no_email' };
    const { sender_name, sender_email, subject } = original.rows[0];
    if (sender_email) {
      mailResult = await sendMail({
        to: sender_email,
        subject: `Re: ${subject}`,
        html: `
          <p>Yth. ${sender_name},</p>
          <p>Terima kasih telah menghubungi kami. Berikut balasan atas pesan Anda:</p>
          <blockquote style="border-left:3px solid #ccc;padding-left:10px;color:#555;">${content}</blockquote>
          <p>Terima kasih.<br/>Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia</p>
        `,
      });
    }

    const message = mailResult.sent
      ? 'Balasan berhasil dikirim dan email diteruskan ke pengirim.'
      : 'Balasan berhasil disimpan' + (mailResult.reason === 'smtp_not_configured' ? ' (email tidak terkirim, SMTP belum dikonfigurasi).' : '.');

    res.status(201).json({ success: true, message, email_sent: mailResult.sent, data: reply.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
