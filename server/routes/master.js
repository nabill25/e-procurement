const express = require('express');
const router  = express.Router();
const { pool } = require('../db');
const { createUpload, handleUploadError } = require('../lib/upload');
const { sendMail } = require('../lib/mailer');
const { requireAuth, requireRole } = require('../lib/authMiddleware');

// Seluruh isi modul Data Master butuh login (GET dipakai role manapun untuk isi dropdown
// form seperti Analisa Kebutuhan/Pasar; operasi tulis POST/PUT/PATCH/DELETE dibatasi admin
// saja lewat requireAdmin di tiap route-nya).
router.use(requireAuth);
const requireAdmin = requireRole('admin');

// ── Konfigurasi Multer ──
const upload = createUpload('master');

// Kategori data master yang valid (mengikuti tabel referensi di eProc lama)
const VALID_CATEGORIES = ['bank', 'mata_uang', 'negara', 'satuan', 'incoterm', 'payment_method', 'analisa_kebutuhan', 'analisa_pasar', 'rekanan_tipe', 'sertifikat_jenis', 'jenis_belanja', 'analisa_kategori', 'ijin_usaha', 'pendidikan', 'paket_jenis', 'metode_lelang', 'metode_kualifikasi', 'metode_evaluasi', 'direktorat'];

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

// ── TEMPLATE DOKUMEN (internal & rekanan digabung, field "target" membedakan) ──
router.get('/document-templates', async (req, res) => {
  try {
    const { target } = req.query;
    let sql = 'SELECT * FROM document_templates';
    const params = [];
    if (target) { sql += ' WHERE target = $1'; params.push(target); }
    sql += ' ORDER BY created_at DESC';
    const result = await pool.query(sql, params);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/document-templates', requireAdmin, upload.single('file'), async (req, res) => {
  try {
    const { nama, keterangan, target, created_by } = req.body;
    if (!nama) return res.status(400).json({ success: false, message: 'nama wajib diisi.' });
    const result = await pool.query(`
      INSERT INTO document_templates (target, nama, keterangan, file_path, file_size, file_type, created_by)
      VALUES ($1, $2, $3, $4, $5, $6, $7) RETURNING *
    `, [target || 'internal', nama, keterangan || null,
        req.file ? `/uploads/${req.file.filename}` : null, req.file ? req.file.size : null, req.file ? req.file.mimetype : null,
        created_by || null]);
    res.status(201).json({ success: true, message: 'Template dokumen berhasil ditambahkan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/document-templates/:templateId', requireAdmin, async (req, res) => {
  try {
    const result = await pool.query('DELETE FROM document_templates WHERE id = $1 RETURNING id', [req.params.templateId]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Template tidak ditemukan.' });
    res.json({ success: true, message: 'Template dokumen berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── HARI LIBUR / TANGGAL MERAH (replace-all pattern, meniru add() asli) ──
router.get('/holidays', async (req, res) => {
  try {
    const { year } = req.query;
    let sql = 'SELECT * FROM holidays';
    const params = [];
    if (year) { sql += ` WHERE EXTRACT(YEAR FROM tanggal) = $1`; params.push(year); }
    sql += ' ORDER BY tanggal ASC';
    const result = await pool.query(sql, params);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/holidays', requireAdmin, async (req, res) => {
  const client = await pool.connect();
  try {
    const { holidays, created_by } = req.body;
    if (!Array.isArray(holidays)) return res.status(400).json({ success: false, message: 'holidays harus berupa array.' });
    await client.query('BEGIN');
    for (const h of holidays) {
      if (!h.tanggal) continue;
      await client.query(`
        INSERT INTO holidays (tanggal, keterangan, created_by) VALUES ($1, $2, $3)
      `, [h.tanggal, h.keterangan || null, created_by || null]);
    }
    await client.query('COMMIT');
    res.status(201).json({ success: true, message: 'Data hari libur berhasil disimpan.' });
  } catch (err) {
    await client.query('ROLLBACK');
    res.status(500).json({ success: false, message: err.message });
  } finally {
    client.release();
  }
});

router.delete('/holidays/:holidayId', requireAdmin, async (req, res) => {
  try {
    await pool.query('DELETE FROM holidays WHERE id = $1', [req.params.holidayId]);
    res.json({ success: true, message: 'Hari libur berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── PENGATURAN SISTEM ──
router.get('/settings/:kunci', async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM app_settings WHERE kunci = $1', [req.params.kunci]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Pengaturan tidak ditemukan.' });
    res.json({ success: true, data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.patch('/settings/:kunci', requireAdmin, async (req, res) => {
  try {
    const { aktif, url, keterangan, updated_by } = req.body;
    const result = await pool.query(`
      UPDATE app_settings SET
        aktif = COALESCE($1, aktif), url = COALESCE($2, url), keterangan = COALESCE($3, keterangan),
        updated_by = $4, updated_at = CURRENT_TIMESTAMP
      WHERE kunci = $5 RETURNING *
    `, [aktif, url, keterangan, updated_by || null, req.params.kunci]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Pengaturan tidak ditemukan.' });
    res.json({ success: true, message: 'Pengaturan berhasil disimpan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── DOKUMEN VENDOR AKAN KEDALUWARSA (padanan cronjobs_notif_dokexpired.php eProc lama) ──
// Sistem lama pakai cron eksternal (crontab) yang panggil endpoint HTTP tiap 6 jam, dan
// bergantung ke 2 VIEW database yang definisinya tidak ditemukan. Sistem baru menyederhanakan:
// query langsung ke vendor_documents.expiry_date (kolom yang sudah ada sejak awal), dan
// "kirim notifikasi" di sini HANYA mencatat log (belum benar-benar mengirim email, karena
// belum ada konfigurasi SMTP di sistem baru) - konsisten dengan keterbatasan yang sama pada
// fitur undangan klarifikasi tender (field email disimpan tapi pengiriman belum jalan).

// GET /api/master/dokumen-expired?hari=30 — daftar dokumen vendor yang akan/sudah kedaluwarsa
router.get('/dokumen-expired', requireAdmin, async (req, res) => {
  try {
    const hari = parseInt(req.query.hari) || 30;
    const result = await pool.query(`
      SELECT d.*, u.full_name AS vendor_name, u.email AS vendor_email,
             (d.expiry_date - CURRENT_DATE) AS sisa_hari
      FROM vendor_documents d
      JOIN users u ON d.vendor_id = u.id
      WHERE d.expiry_date IS NOT NULL
        AND d.expiry_date <= CURRENT_DATE + ($1 || ' days')::interval
      ORDER BY d.expiry_date ASC
    `, [hari]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// POST /api/master/dokumen-expired/:docId/notify — kirim email notifikasi + catat log (upsert)
router.post('/dokumen-expired/:docId/notify', requireAdmin, async (req, res) => {
  try {
    const doc = await pool.query(
      `SELECT d.vendor_id, d.doc_type, d.expiry_date, u.full_name AS vendor_name, u.email AS vendor_email
       FROM vendor_documents d JOIN users u ON d.vendor_id = u.id WHERE d.id = $1`,
      [req.params.docId]
    );
    if (!doc.rows.length) return res.status(404).json({ success: false, message: 'Dokumen tidak ditemukan.' });
    const docInfo = doc.rows[0];

    let mailResult = { sent: false, reason: 'no_email' };
    if (docInfo.vendor_email) {
      const expiryStr = docInfo.expiry_date ? new Date(docInfo.expiry_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-';
      mailResult = await sendMail({
        to: docInfo.vendor_email,
        subject: `Pemberitahuan: Dokumen ${docInfo.doc_type} Anda Akan/Sudah Kedaluwarsa`,
        html: `
          <p>Yth. ${docInfo.vendor_name},</p>
          <p>Dokumen <strong>${docInfo.doc_type}</strong> yang Anda unggah di sistem e-Procurement DPBJ UI akan/sudah melewati tanggal berlaku pada <strong>${expiryStr}</strong>.</p>
          <p>Mohon segera perbarui dokumen tersebut melalui halaman Profil &amp; Kualifikasi Vendor untuk menghindari kendala pada proses pengadaan yang sedang/akan Anda ikuti.</p>
          <p>Terima kasih.<br/>Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia</p>
        `,
      });
    }

    const existing = await pool.query(
      'SELECT id, sent_count FROM document_expiry_notification_logs WHERE vendor_document_id = $1',
      [req.params.docId]
    );

    let result;
    if (existing.rows.length) {
      result = await pool.query(
        `UPDATE document_expiry_notification_logs
         SET sent_count = sent_count + 1, last_sent_at = CURRENT_TIMESTAMP
         WHERE id = $1 RETURNING *`,
        [existing.rows[0].id]
      );
    } else {
      result = await pool.query(
        `INSERT INTO document_expiry_notification_logs (vendor_document_id, vendor_id)
         VALUES ($1, $2) RETURNING *`,
        [req.params.docId, docInfo.vendor_id]
      );
    }

    const message = mailResult.sent
      ? 'Notifikasi berhasil dikirim ke email vendor dan dicatat.'
      : (mailResult.reason === 'smtp_not_configured'
          ? 'Notifikasi dicatat (email tidak terkirim karena SMTP belum dikonfigurasi di server).'
          : (mailResult.reason === 'no_email'
              ? 'Notifikasi dicatat (vendor tidak punya alamat email terdaftar, email tidak dikirim).'
              : 'Notifikasi dicatat, namun pengiriman email gagal: ' + (mailResult.error || '')));

    res.json({ success: true, message, email_sent: mailResult.sent, data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// GET /api/master/dokumen-expired/logs — riwayat notifikasi yang sudah dicatat
router.get('/dokumen-expired/logs', requireAdmin, async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT l.*, u.full_name AS vendor_name, d.doc_type
      FROM document_expiry_notification_logs l
      JOIN users u ON l.vendor_id = u.id
      LEFT JOIN vendor_documents d ON l.vendor_document_id = d.id
      ORDER BY l.last_sent_at DESC
    `);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── WILAYAH ADMINISTRATIF (berjenjang) ──
router.get('/regions', async (req, res) => {
  try {
    const { level, parent_id } = req.query;
    let sql = 'SELECT * FROM regions WHERE 1=1';
    const params = [];
    if (level) { sql += ` AND level = $${params.length + 1}`; params.push(level); }
    if (parent_id) { sql += ` AND parent_id = $${params.length + 1}`; params.push(parent_id); }
    else if (level && level !== 'provinsi') { sql += ' AND parent_id IS NULL'; } // belum ada data di bawah provinsi
    sql += ' ORDER BY nama ASC';
    const result = await pool.query(sql, params);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/regions', requireAdmin, async (req, res) => {
  try {
    const { level, nama, parent_id } = req.body;
    if (!level || !nama) return res.status(400).json({ success: false, message: 'level dan nama wajib diisi.' });
    const result = await pool.query(`
      INSERT INTO regions (level, nama, parent_id) VALUES ($1, $2, $3) RETURNING *
    `, [level, nama, parent_id || null]);
    res.status(201).json({ success: true, message: 'Wilayah berhasil ditambahkan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/regions/:regionId', requireAdmin, async (req, res) => {
  try {
    const result = await pool.query('DELETE FROM regions WHERE id = $1 RETURNING id', [req.params.regionId]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Wilayah tidak ditemukan.' });
    res.json({ success: true, message: 'Wilayah berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
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

router.post('/unit-kerja', requireAdmin, async (req, res) => {
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

router.delete('/unit-kerja/:id', requireAdmin, async (req, res) => {
  try {
    const result = await pool.query('DELETE FROM unit_kerja_master WHERE id = $1 RETURNING id', [req.params.id]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Data tidak ditemukan.' });
    res.json({ success: true, message: 'Unit kerja berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── TEMPLATE PENILAIAN KINERJA PENYEDIA (padanan PAKET_PENILAIAN_TEMPLATE eProc lama,
// versi disederhanakan tanpa approval berjenjang) ──
router.get('/penilaian-templates', async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM penilaian_kinerja_templates WHERE is_active = true ORDER BY kode ASC, nama ASC');
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/penilaian-templates', requireAdmin, async (req, res) => {
  try {
    const { parent_id, kode, nama, bobot_persen, skor_maksimal, catatan } = req.body;
    if (!nama) return res.status(400).json({ success: false, message: 'nama wajib diisi.' });
    const result = await pool.query(`
      INSERT INTO penilaian_kinerja_templates (parent_id, kode, nama, bobot_persen, skor_maksimal, catatan)
      VALUES ($1, $2, $3, $4, $5, $6) RETURNING *
    `, [parent_id || null, kode || null, nama, bobot_persen || null, skor_maksimal || null, catatan || null]);
    res.status(201).json({ success: true, message: 'Kriteria penilaian berhasil ditambahkan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/penilaian-templates/:id', requireAdmin, async (req, res) => {
  try {
    await pool.query('UPDATE penilaian_kinerja_templates SET is_active = false WHERE id = $1', [req.params.id]);
    res.json({ success: true, message: 'Kriteria penilaian berhasil dinonaktifkan.' });
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
router.post('/:category', requireAdmin, checkCategory, async (req, res) => {
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
router.put('/:category/:id', requireAdmin, checkCategory, async (req, res) => {
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
router.delete('/:category/:id', requireAdmin, checkCategory, async (req, res) => {
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
