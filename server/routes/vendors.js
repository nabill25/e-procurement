const express = require('express');
const router  = express.Router();
const { pool } = require('../db');
const multer  = require('multer');
const path    = require('path');
const fs      = require('fs');

// ── Konfigurasi Multer ──
const storage = multer.diskStorage({
  destination: function (req, file, cb) {
    const dir = path.join(__dirname, '..', 'uploads');
    if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
    cb(null, dir);
  },
  filename: function (req, file, cb) {
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
    cb(null, 'vendor-' + req.params.id + '-' + uniqueSuffix + path.extname(file.originalname));
  }
});
const upload = multer({ storage: storage });

// ── Status yang valid (mengikuti mapping status rekanan eProc) ──
// eProc: status_validasi 1=pending, 2=terverifikasi, 3=ditangguhkan, 4=diblokir
const VALID_STATUSES = ['pending', 'terverifikasi', 'ditangguhkan', 'diblokir'];

// ── BIDANG USAHA (klasifikasi berjenjang, ditaruh sebelum /:id supaya tidak ketiban rute vendor) ──
router.get('/bidang-usaha/tree', async (req, res) => {
  try {
    const { search } = req.query;
    let sql = 'SELECT * FROM bidang_usaha';
    const params = [];
    if (search) {
      sql += ' WHERE nama ILIKE $1 OR kode ILIKE $1';
      params.push(`%${search}%`);
    }
    sql += ' ORDER BY nama ASC';
    const result = await pool.query(sql, params);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── VENDOR RETAIL (kategori vendor retail/katalog, terpisah dari vendor pengadaan biasa) ──
router.get('/retail', async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM vendor_retail ORDER BY nama ASC');
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/retail', async (req, res) => {
  try {
    const { tipe, nama, npwp, telepon_kode, telepon, whatsapp, tanggal_daftar, kota, region, kontak_person, kontak_person_hp, alamat, created_by } = req.body;
    if (!nama) return res.status(400).json({ success: false, message: 'nama wajib diisi.' });
    const result = await pool.query(`
      INSERT INTO vendor_retail (tipe, nama, npwp, telepon_kode, telepon, whatsapp, tanggal_daftar, kota, region, kontak_person, kontak_person_hp, alamat, created_by)
      VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13) RETURNING *
    `, [tipe || null, nama, npwp || null, telepon_kode || null, telepon || null, whatsapp || null, tanggal_daftar || null, kota || null, region || null, kontak_person || null, kontak_person_hp || null, alamat || null, created_by || null]);
    res.status(201).json({ success: true, message: 'Vendor retail berhasil ditambahkan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.put('/retail/:retailId', async (req, res) => {
  try {
    const { tipe, nama, npwp, telepon_kode, telepon, whatsapp, tanggal_daftar, kota, region, kontak_person, kontak_person_hp, alamat } = req.body;
    const result = await pool.query(`
      UPDATE vendor_retail SET
        tipe = COALESCE($1, tipe), nama = COALESCE($2, nama), npwp = COALESCE($3, npwp),
        telepon_kode = COALESCE($4, telepon_kode), telepon = COALESCE($5, telepon), whatsapp = COALESCE($6, whatsapp),
        tanggal_daftar = COALESCE($7, tanggal_daftar), kota = COALESCE($8, kota), region = COALESCE($9, region),
        kontak_person = COALESCE($10, kontak_person), kontak_person_hp = COALESCE($11, kontak_person_hp), alamat = COALESCE($12, alamat)
      WHERE id = $13 RETURNING *
    `, [tipe, nama, npwp, telepon_kode, telepon, whatsapp, tanggal_daftar || null, kota, region, kontak_person, kontak_person_hp, alamat, req.params.retailId]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Data tidak ditemukan.' });
    res.json({ success: true, message: 'Vendor retail berhasil diperbarui.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/retail/:retailId', async (req, res) => {
  try {
    const result = await pool.query('DELETE FROM vendor_retail WHERE id = $1 RETURNING id', [req.params.retailId]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Data tidak ditemukan.' });
    res.json({ success: true, message: 'Vendor retail berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/vendors ──
router.get('/', async (req, res) => {
  try {
    const { status, search, page = 1, limit = 20 } = req.query;
    const offset = (parseInt(page) - 1) * parseInt(limit);
    let sql = `
      SELECT v.id, v.company_name, v.npwp, v.nib, v.company_type, v.city, v.province, v.email, v.phone, v.status, v.blacklisted, v.qualification_class, v.created_at, 
             u.rating_avg, u.rating_count 
      FROM vendors v
      LEFT JOIN users u ON v.user_id = u.id
      WHERE 1=1
    `;
    const params = [];
    let paramIndex = 1;
    if (status) { sql += ` AND v.status = $${paramIndex++}`;                                   params.push(status); }
    if (search) { sql += ` AND (v.company_name ILIKE $${paramIndex++} OR v.npwp ILIKE $${paramIndex++})`;
                  params.push(`%${search}%`, `%${search}%`); }
    sql += ` ORDER BY v.created_at DESC LIMIT $${paramIndex++} OFFSET $${paramIndex++}`;
    params.push(parseInt(limit), offset);
    const result = await pool.query(sql, params);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/vendors/:id ──
router.get('/:id', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT v.*, u.rating_avg, u.rating_count
      FROM vendors v
      LEFT JOIN users u ON v.user_id = u.id
      WHERE v.id = $1
    `, [req.params.id]);
    const rows = result.rows;
    if (!rows.length) return res.status(404).json({ success: false, message: 'Vendor tidak ditemukan.' });
    res.json({ success: true, data: rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── PUT /api/vendors/:id/profile ──
router.put('/:id/profile', async (req, res) => {
  try {
    const { pajak, tenaga_ahli, peralatan, pengurus, bank, neraca } = req.body;

    // Bangun set query dinamis jika data dikirim
    const updates = [];
    const values = [];
    let idx = 1;

    if (pajak !== undefined) {
      updates.push(`pajak = $${idx++}`);
      values.push(JSON.stringify(pajak));
    }
    if (tenaga_ahli !== undefined) {
      updates.push(`tenaga_ahli = $${idx++}`);
      values.push(JSON.stringify(tenaga_ahli));
    }
    if (peralatan !== undefined) {
      updates.push(`peralatan = $${idx++}`);
      values.push(JSON.stringify(peralatan));
    }
    if (pengurus !== undefined) {
      updates.push(`pengurus = $${idx++}`);
      values.push(JSON.stringify(pengurus));
    }
    if (bank !== undefined) {
      updates.push(`bank = $${idx++}`);
      values.push(JSON.stringify(bank));
    }
    if (neraca !== undefined) {
      updates.push(`neraca = $${idx++}`);
      values.push(JSON.stringify(neraca));
    }

    if (updates.length === 0) {
      return res.status(400).json({ success: false, message: 'Tidak ada data untuk diperbarui.' });
    }

    values.push(req.params.id);
    const sql = `UPDATE vendors SET ${updates.join(', ')} WHERE id = $${idx} RETURNING *`;
    
    const result = await pool.query(sql, values);
    if (!result.rows.length) {
      return res.status(404).json({ success: false, message: 'Vendor tidak ditemukan.' });
    }

    res.json({ success: true, message: 'Profil vendor berhasil diperbarui.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/vendors — Daftarkan vendor baru ──
router.post('/', async (req, res) => {
  try {
    const { company_name, npwp, email, company_type, city, province, phone, contact_person } = req.body;
    if (!company_name || !npwp || !email) {
      return res.status(400).json({ success: false, message: 'company_name, npwp, dan email wajib diisi.' });
    }
    await pool.query(`
      INSERT INTO vendors (id, company_name, npwp, email, company_type, city, province, phone, contact_person)
      VALUES (gen_random_uuid(), $1, $2, $3, $4, $5, $6, $7, $8)
    `, [company_name, npwp, email, company_type||null, city||null, province||null, phone||null, contact_person||null]);

    // Catat audit log
    await pool.query(
      `INSERT INTO audit_logs (action, entity_type, description, is_success) VALUES ('CREATE', 'Vendor', $1, true)`,
      [`Vendor baru didaftarkan: ${company_name}`]
    );

    res.status(201).json({ success: true, message: 'Vendor berhasil didaftarkan.' });
  } catch (err) {
    if (err.code === '23505') {
      return res.status(409).json({ success: false, message: 'NPWP atau email sudah terdaftar.' });
    }
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── PATCH /api/vendors/:id/verify — Verifikasi vendor (status → terverifikasi) ──
// Mengikuti eProc: status_validasi = 2
router.patch('/:id/verify', async (req, res) => {
  try {
    const { verified_by } = req.body;
    const result = await pool.query('SELECT company_name FROM vendors WHERE id = $1', [req.params.id]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Vendor tidak ditemukan.' });

    await pool.query(
      "UPDATE vendors SET status='terverifikasi', verified_by=$1, verified_at=CURRENT_TIMESTAMP WHERE id=$2",
      [verified_by || null, req.params.id]
    );

    // Audit log
    await pool.query(
      `INSERT INTO audit_logs (action, entity_type, description, is_success) VALUES ('UPDATE', 'Vendor', $1, true)`,
      [`Vendor diverifikasi: ${result.rows[0].company_name}`]
    );

    res.json({ success: true, message: 'Vendor berhasil diverifikasi.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/vendors/:id/status — Update status vendor (general) ──
// Mengikuti eProc: mapping status_validasi ke berbagai status
router.post('/:id/status', async (req, res) => {
  try {
    const { status, reason } = req.body;

    if (!VALID_STATUSES.includes(status)) {
      return res.status(400).json({
        success: false,
        message: `Status tidak valid. Pilihan: ${VALID_STATUSES.join(', ')}.`
      });
    }

    const vendorResult = await pool.query('SELECT company_name FROM vendors WHERE id = $1', [req.params.id]);
    if (!vendorResult.rows.length) {
      return res.status(404).json({ success: false, message: 'Vendor tidak ditemukan.' });
    }

    const companyName = vendorResult.rows[0].company_name;
    await pool.query('UPDATE vendors SET status = $1 WHERE id = $2', [status, req.params.id]);

    // Audit log
    const desc = reason
      ? `Status vendor ${companyName} diubah ke "${status}". Alasan: ${reason}`
      : `Status vendor ${companyName} diubah ke "${status}"`;
    await pool.query(
      `INSERT INTO audit_logs (action, entity_type, description, is_success) VALUES ('UPDATE', 'Vendor', $1, true)`,
      [desc]
    );

    res.json({ success: true, message: `Status vendor berhasil diubah ke: ${status}` });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── PATCH /api/vendors/:id/suspend — Tangguhkan vendor ──
// Mengikuti eProc: status_validasi = 3 (ditangguhkan)
router.patch('/:id/suspend', async (req, res) => {
  try {
    const { reason } = req.body;
    const result = await pool.query('SELECT company_name FROM vendors WHERE id = $1', [req.params.id]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Vendor tidak ditemukan.' });

    await pool.query("UPDATE vendors SET status='ditangguhkan' WHERE id=$1", [req.params.id]);

    await pool.query(
      `INSERT INTO audit_logs (action, entity_type, description, is_success) VALUES ('UPDATE', 'Vendor', $1, true)`,
      [`Vendor ditangguhkan: ${result.rows[0].company_name}${reason ? '. Alasan: ' + reason : ''}`]
    );

    res.json({ success: true, message: 'Vendor berhasil ditangguhkan.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── PATCH /api/vendors/:id/block — Blokir vendor (masuk blacklist) ──
// Mengikuti eProc: status_validasi = 4 (diblokir)
router.patch('/:id/block', async (req, res) => {
  try {
    const { reason } = req.body;
    const result = await pool.query('SELECT user_id, company_name, npwp, city FROM vendors WHERE id = $1', [req.params.id]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Vendor tidak ditemukan.' });
    const vendor = result.rows[0];

    await pool.query("UPDATE vendors SET status='diblokir', blacklisted=true WHERE id=$1", [req.params.id]);

    await pool.query(`
      INSERT INTO blacklist (vendor_id, company_name, npwp, city, reason)
      VALUES ($1, $2, $3, $4, $5)
    `, [vendor.user_id, vendor.company_name, vendor.npwp, vendor.city, reason || 'Tidak ada alasan yang dicantumkan.']);

    await pool.query(
      `INSERT INTO audit_logs (action, entity_type, description, is_success) VALUES ('UPDATE', 'Vendor', $1, true)`,
      [`Vendor diblokir/blacklist: ${vendor.company_name}${reason ? '. Alasan: ' + reason : ''}`]
    );

    res.json({ success: true, message: 'Vendor berhasil diblokir dan ditambahkan ke daftar hitam.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/vendors/:id/qualifications — Ambil dokumen & pengalaman ──
router.get('/:id/qualifications', async (req, res) => {
  try {
    const docsResult = await pool.query('SELECT * FROM vendor_documents WHERE vendor_id = $1 ORDER BY created_at DESC', [req.params.id]);
    const expResult = await pool.query('SELECT * FROM vendor_experiences WHERE vendor_id = $1 ORDER BY start_date DESC', [req.params.id]);
    const sikapResult = await pool.query('SELECT pajak, tenaga_ahli, peralatan, pengurus, bank, neraca FROM vendors WHERE id = $1', [req.params.id]);

    const sikap = sikapResult.rows[0] || {};

    res.json({
      success: true,
      data: {
        documents: docsResult.rows,
        experiences: expResult.rows,
        pajak: sikap.pajak || [],
        tenaga_ahli: sikap.tenaga_ahli || [],
        peralatan: sikap.peralatan || [],
        pengurus: sikap.pengurus || [],
        bank: sikap.bank || [],
        neraca: sikap.neraca || []
      }
    });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/vendors/:id/documents — Upload dokumen legalitas ──
router.post('/:id/documents', upload.single('document'), async (req, res) => {
  try {
    const { doc_type, doc_number, issue_date, expiry_date } = req.body;
    
    if (!doc_type || !doc_number) {
      return res.status(400).json({ success: false, message: 'doc_type dan doc_number wajib diisi.' });
    }

    const file_path = req.file ? `/uploads/${req.file.filename}` : null;
    if (!file_path) {
      return res.status(400).json({ success: false, message: 'File dokumen wajib diunggah.' });
    }

    await pool.query(`
      INSERT INTO vendor_documents (vendor_id, doc_type, doc_number, issue_date, expiry_date, file_path, status)
      VALUES ($1, $2, $3, $4, $5, $6, 'verified')
    `, [req.params.id, doc_type, doc_number, issue_date || null, expiry_date || null, file_path]);

    res.status(201).json({ success: true, message: 'Dokumen berhasil diunggah dan terverifikasi otomatis.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/vendors/:id/experiences — Tambah pengalaman kerja ──
router.post('/:id/experiences', async (req, res) => {
  try {
    const { project_name, client_name, contract_value, start_date, end_date } = req.body;
    
    if (!project_name || !client_name || !contract_value) {
      return res.status(400).json({ success: false, message: 'project_name, client_name, dan contract_value wajib diisi.' });
    }

    await pool.query(`
      INSERT INTO vendor_experiences (vendor_id, project_name, client_name, contract_value, start_date, end_date)
      VALUES ($1, $2, $3, $4, $5, $6)
    `, [req.params.id, project_name, client_name, contract_value, start_date || null, end_date || null]);

    res.status(201).json({ success: true, message: 'Pengalaman kerja berhasil ditambahkan.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/vendors/:id/rating/:tenderId — Cek apakah vendor sudah di-rating di tender tertentu ──
router.get('/:id/rating/:tenderId', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT * FROM vendor_ratings 
      WHERE vendor_id = $1 AND tender_id = $2
    `, [req.params.id, req.params.tenderId]);
    
    if (result.rows.length > 0) {
      res.json({ success: true, data: result.rows[0] });
    } else {
      res.json({ success: true, data: null });
    }
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/vendors/:id/rating — Berikan rating (PPK) ──
router.post('/:id/rating', async (req, res) => {
  try {
    const { tender_id, ppk_id, rating_score, review_notes } = req.body;
    
    if (!tender_id || !ppk_id || !rating_score) {
      return res.status(400).json({ success: false, message: 'tender_id, ppk_id, dan rating_score wajib diisi.' });
    }

    // Insert rating
    await pool.query(`
      INSERT INTO vendor_ratings (vendor_id, tender_id, ppk_id, rating_score, review_notes)
      VALUES ($1, $2, $3, $4, $5)
      ON CONFLICT (vendor_id, tender_id) 
      DO UPDATE SET rating_score = EXCLUDED.rating_score, review_notes = EXCLUDED.review_notes, created_at = CURRENT_TIMESTAMP
    `, [req.params.id, tender_id, ppk_id, rating_score, review_notes]);

    // Hitung rata-rata baru
    const avgResult = await pool.query(`
      SELECT AVG(rating_score) as avg, COUNT(*) as count 
      FROM vendor_ratings WHERE vendor_id = $1
    `, [req.params.id]);

    const newAvg = avgResult.rows[0].avg || 0;
    const newCount = avgResult.rows[0].count || 0;

    // Update tabel users
    await pool.query(`
      UPDATE users 
      SET rating_avg = $1, rating_count = $2 
      WHERE id = $3
    `, [newAvg, newCount, req.params.id]);

    res.json({ success: true, message: 'Rating berhasil disimpan.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── BIDANG USAHA PER VENDOR ──
router.get('/:id/bidang-usaha', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT vb.id, vb.bidang_usaha_id, b.kode, b.nama, b.parent_id
      FROM vendor_bidang_usaha vb
      JOIN bidang_usaha b ON vb.bidang_usaha_id = b.id
      WHERE vb.vendor_id = $1
      ORDER BY b.nama ASC
    `, [req.params.id]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/bidang-usaha', async (req, res) => {
  try {
    const { bidang_usaha_id } = req.body;
    if (!bidang_usaha_id) return res.status(400).json({ success: false, message: 'bidang_usaha_id diperlukan.' });
    const result = await pool.query(`
      INSERT INTO vendor_bidang_usaha (vendor_id, bidang_usaha_id) VALUES ($1, $2)
      ON CONFLICT (vendor_id, bidang_usaha_id) DO NOTHING RETURNING *
    `, [req.params.id, bidang_usaha_id]);
    res.status(201).json({ success: true, message: 'Bidang usaha berhasil ditambahkan.', data: result.rows[0] || null });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/:id/bidang-usaha/:linkId', async (req, res) => {
  try {
    await pool.query('DELETE FROM vendor_bidang_usaha WHERE id = $1 AND vendor_id = $2', [req.params.linkId, req.params.id]);
    res.json({ success: true, message: 'Bidang usaha berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── REKENING KORAN (bukti mutasi bank per bulan, syarat kualifikasi keuangan) ──
router.get('/:id/rekening-koran', async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM vendor_rekening_koran WHERE vendor_id = $1 ORDER BY tahun DESC, bulan DESC', [req.params.id]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/rekening-koran', upload.single('file'), async (req, res) => {
  try {
    const { nomor_rekening, nama_bank, bulan, tahun, nilai, mata_uang } = req.body;
    if (!nomor_rekening || !bulan || !tahun) {
      return res.status(400).json({ success: false, message: 'nomor_rekening, bulan, dan tahun wajib diisi.' });
    }
    const result = await pool.query(`
      INSERT INTO vendor_rekening_koran (vendor_id, nomor_rekening, nama_bank, bulan, tahun, nilai, mata_uang, file_path, file_size)
      VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9) RETURNING *
    `, [req.params.id, nomor_rekening, nama_bank || null, Number(bulan), Number(tahun), nilai ? Number(nilai) : null, mata_uang || 'IDR', req.file ? req.file.filename : null, req.file ? req.file.size : null]);
    res.status(201).json({ success: true, message: 'Rekening koran berhasil disimpan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/:id/rekening-koran/:rkId', async (req, res) => {
  try {
    const result = await pool.query('DELETE FROM vendor_rekening_koran WHERE id = $1 AND vendor_id = $2 RETURNING id', [req.params.rkId, req.params.id]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Data tidak ditemukan.' });
    res.json({ success: true, message: 'Rekening koran berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
