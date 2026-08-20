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
    cb(null, req.params.id + '-' + uniqueSuffix + path.extname(file.originalname));
  }
});
const upload = multer({ storage: storage });

// ── GET /api/tenders — Daftar semua tender dengan filter ──
router.get('/', async (req, res) => {
  try {
    const { status, method, search, page = 1, limit = 20 } = req.query;
    const offset = (parseInt(page) - 1) * parseInt(limit);

    let sql = `
      SELECT
        t.id, t.tender_number, t.title, t.method, t.category,
        t.pagu_anggaran, t.hps, t.status,
        t.submission_deadline, t.winner_announcement,
        t.work_location, t.created_at,
        u_ppk.full_name   AS ppk_name,
        u_pokja.full_name AS pokja_lead_name,
        (SELECT COUNT(*) FROM tender_participants tp
          WHERE tp.tender_id = t.id AND tp.bid_price IS NOT NULL) AS bid_count
      FROM tenders t
      LEFT JOIN users u_ppk   ON t.ppk_id       = u_ppk.id
      LEFT JOIN users u_pokja ON t.pokja_lead_id = u_pokja.id
      WHERE 1=1
    `;
    const params = [];
    let paramIndex = 1;

    if (status) { sql += ` AND t.status = $${paramIndex++}`;        params.push(status); }
    if (method) { sql += ` AND t.method = $${paramIndex++}`;        params.push(method); }
    if (search) { sql += ` AND (t.title ILIKE $${paramIndex++} OR t.tender_number ILIKE $${paramIndex++})`;
                  params.push(`%${search}%`, `%${search}%`); }

    sql += ` ORDER BY t.created_at DESC LIMIT $${paramIndex++} OFFSET $${paramIndex++}`;
    params.push(parseInt(limit), offset);

    const result = await pool.query(sql, params);
    const rows = result.rows;

    // Hitung total untuk pagination
    let countSql = `SELECT COUNT(DISTINCT t.id) AS total FROM tenders t WHERE 1=1`;
    const countParams = [];
    let countParamIndex = 1;
    if (status) { countSql += ` AND t.status = $${countParamIndex++}`; countParams.push(status); }
    if (method) { countSql += ` AND t.method = $${countParamIndex++}`; countParams.push(method); }
    if (search) { countSql += ` AND (t.title ILIKE $${countParamIndex++} OR t.tender_number ILIKE $${countParamIndex++})`;
                  countParams.push(`%${search}%`, `%${search}%`); }

    const countResult = await pool.query(countSql, countParams);
    const total = countResult.rows[0].total;

    res.json({
      success: true,
      data: rows,
      pagination: { total, page: parseInt(page), limit: parseInt(limit), pages: Math.ceil(total / limit) }
    });
  } catch (err) {
    console.error('[GET /tenders]', err);
    res.status(500).json({ success: false, message: 'Gagal mengambil data tender.' });
  }
});

// ── GET /api/tenders/:id — Detail satu tender ──
router.get('/:id', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT t.*, u_ppk.full_name AS ppk_name, u_pokja.full_name AS pokja_lead_name
      FROM tenders t
      LEFT JOIN users u_ppk   ON t.ppk_id       = u_ppk.id
      LEFT JOIN users u_pokja ON t.pokja_lead_id = u_pokja.id
      WHERE t.id = $1
    `, [req.params.id]);
    const rows = result.rows;

    if (!rows.length) return res.status(404).json({ success: false, message: 'Tender tidak ditemukan.' });
    res.json({ success: true, data: rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/tenders — Buat tender baru ──
router.post('/', async (req, res) => {
  try {
    const { title, method, pagu_anggaran, hps, ppk_id, pokja_lead_id,
            category, description, work_location } = req.body;

    if (!title || !pagu_anggaran) {
      return res.status(400).json({ success: false, message: 'title dan pagu_anggaran wajib diisi.' });
    }

    // Generate nomor tender otomatis: TENDER/YYYY/NNN
    const year = new Date().getFullYear();
    const countResult = await pool.query(
      'SELECT COUNT(*) AS cnt FROM tenders WHERE EXTRACT(YEAR FROM created_at) = $1', [year]
    );
    const cnt = parseInt(countResult.rows[0].cnt);
    const tender_number = `TENDER/${year}/${String(cnt + 1).padStart(3, '0')}`;

    const result = await pool.query(`
      INSERT INTO tenders
        (id, tender_number, title, method, pagu_anggaran, hps,
         ppk_id, pokja_lead_id, category, description, work_location)
      VALUES (gen_random_uuid(), $1, $2, $3, $4, $5, $6, $7, $8, $9, $10)
      RETURNING *
    `, [tender_number, title, method || 'tender', pagu_anggaran, hps || null,
        ppk_id || null, pokja_lead_id || null, category || null,
        description || null, work_location || null]);

    res.status(201).json({ success: true, message: 'Tender berhasil dibuat.', tender_number });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── PATCH /api/tenders/:id/status — Update status saja ──
router.patch('/:id/status', async (req, res) => {
  try {
    const { status } = req.body;
    const allowed = ['draft','proses_review','disetujui','ditolak','tender_buka','evaluasi','selesai','dibatalkan'];
    if (!allowed.includes(status)) {
      return res.status(400).json({ success: false, message: 'Status tidak valid.' });
    }
    await pool.query('UPDATE tenders SET status = $1 WHERE id = $2', [status, req.params.id]);
    res.json({ success: true, message: `Status tender diubah ke: ${status}` });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── PATCH /api/tenders/:id/stage — Update tahapan tender (Oleh Pokja) ──
router.patch('/:id/stage', async (req, res) => {
  try {
    const { status } = req.body; // status baru
    const allowed = ['draft','pengumuman','pendaftaran','penawaran','evaluasi','pemenang','selesai','dibatalkan'];
    if (!allowed.includes(status)) {
      return res.status(400).json({ success: false, message: 'Tahapan tender tidak valid.' });
    }
    await pool.query('UPDATE tenders SET status = $1 WHERE id = $2', [status, req.params.id]);
    res.json({ success: true, message: `Tahapan tender berhasil diubah ke: ${status}` });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/tenders/:id/register — Pendaftaran Vendor ke Tender ──
router.post('/:id/register', async (req, res) => {
  try {
    const tenderId = req.params.id;
    const { vendor_id } = req.body;

    if (!vendor_id) {
      return res.status(400).json({ success: false, message: 'vendor_id diperlukan.' });
    }

    // Cek apakah tender sedang dalam tahap pendaftaran
    const tender = await pool.query('SELECT status FROM tenders WHERE id = $1', [tenderId]);
    if (tender.rows.length === 0) return res.status(404).json({ success: false, message: 'Tender tidak ditemukan.' });
    if (tender.rows[0].status !== 'pendaftaran') {
      return res.status(400).json({ success: false, message: 'Pendaftaran untuk tender ini belum/sudah ditutup.' });
    }

    // Cek apakah vendor diblokir
    const vendorCheck = await pool.query('SELECT blacklisted, status FROM vendors WHERE user_id = $1', [vendor_id]);
    if (vendorCheck.rows.length > 0) {
      if (vendorCheck.rows[0].blacklisted || vendorCheck.rows[0].status === 'diblokir') {
        return res.status(403).json({ success: false, message: 'Akun Anda sedang diblokir/blacklist. Tidak dapat mengikuti tender baru.' });
      }
    }

    // Insert ke tender_participants
    await pool.query(`
      INSERT INTO tender_participants (tender_id, vendor_id, status)
      VALUES ($1, $2, 'registered')
      ON CONFLICT (tender_id, vendor_id) DO NOTHING
    `, [tenderId, vendor_id]);

    res.json({ success: true, message: 'Berhasil mendaftar ke tender ini.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/tenders/:id/participants — Lihat Peserta Tender (Oleh Pokja) ──
router.get('/:id/participants', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT tp.*, v.company_name, v.company_type, v.city
      FROM tender_participants tp
      JOIN vendors v ON tp.vendor_id = v.user_id
      WHERE tp.tender_id = $1
    `, [req.params.id]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/tenders/:id/bids — Vendor mengirim penawaran & dokumen ──
router.post('/:id/bids', upload.single('document'), async (req, res) => {
  try {
    const tenderId = req.params.id;
    const { vendor_id, bid_price } = req.body;
    
    if (!vendor_id || !bid_price) {
      return res.status(400).json({ success: false, message: 'vendor_id dan bid_price diperlukan.' });
    }

    const document_path = req.file ? `/uploads/${req.file.filename}` : null;

    // Cek status tender
    const tender = await pool.query('SELECT status FROM tenders WHERE id = $1', [tenderId]);
    if (tender.rows.length === 0) return res.status(404).json({ success: false, message: 'Tender tidak ditemukan.' });
    if (tender.rows[0].status !== 'penawaran') {
      return res.status(400).json({ success: false, message: 'Tender tidak dalam tahap penawaran.' });
    }

    // Cek apakah vendor diblokir
    const vendorCheck = await pool.query('SELECT blacklisted, status FROM vendors WHERE user_id = $1', [vendor_id]);
    if (vendorCheck.rows.length > 0) {
      if (vendorCheck.rows[0].blacklisted || vendorCheck.rows[0].status === 'diblokir') {
        return res.status(403).json({ success: false, message: 'Akun Anda sedang diblokir/blacklist. Tidak dapat mengirim penawaran.' });
      }
    }

    // Update participants
    await pool.query(`
      UPDATE tender_participants 
      SET bid_price = $1, document_path = $2, status = 'bidded'
      WHERE tender_id = $3 AND vendor_id = $4
    `, [bid_price, document_path, tenderId, vendor_id]);

    res.json({ success: true, message: 'Penawaran berhasil dikirim.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── PATCH /api/tenders/:id/participants/:vendorId/evaluate — Evaluasi (Pokja) ──
router.patch('/:id/participants/:vendorId/evaluate', async (req, res) => {
  try {
    const { technical_score, evaluation_notes, is_passed } = req.body;
    
    await pool.query(`
      UPDATE tender_participants 
      SET technical_score = $1, evaluation_notes = $2, 
          is_evaluated = true, status = $3
      WHERE tender_id = $4 AND vendor_id = $5
    `, [technical_score, evaluation_notes, is_passed ? 'passed' : 'failed', req.params.id, req.params.vendorId]);

    res.json({ success: true, message: 'Hasil evaluasi berhasil disimpan.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/tenders/:id/winner — Penetapan Pemenang ──
router.post('/:id/winner', async (req, res) => {
  try {
    const { vendor_id } = req.body;

    // Reset status pemenang untuk peserta lain
    await pool.query(`UPDATE tender_participants SET is_winner = false WHERE tender_id = $1`, [req.params.id]);
    
    // Set pemenang baru
    await pool.query(`
      UPDATE tender_participants 
      SET is_winner = true, status = 'winner' 
      WHERE tender_id = $1 AND vendor_id = $2
    `, [req.params.id, vendor_id]);

    res.json({ success: true, message: 'Pemenang tender berhasil ditetapkan.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/tenders/:id/aanwijzing — Chat Aanwijzing ──
router.get('/:id/aanwijzing', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT a.*, u.full_name AS user_name, u.role
      FROM tender_aanwijzing_chats a
      JOIN users u ON a.user_id = u.id
      WHERE a.tender_id = $1
      ORDER BY a.created_at ASC
    `, [req.params.id]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/tenders/:id/aanwijzing — Kirim Chat Aanwijzing ──
router.post('/:id/aanwijzing', async (req, res) => {
  try {
    const { user_id, message } = req.body;
    
    if (!user_id || !message) {
      return res.status(400).json({ success: false, message: 'user_id dan message diperlukan.' });
    }

    const result = await pool.query(`
      INSERT INTO tender_aanwijzing_chats (tender_id, user_id, message)
      VALUES ($1, $2, $3)
      RETURNING *
    `, [req.params.id, user_id, message]);

    res.json({ success: true, data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/tenders/:id/objections — Daftar sanggahan ──
router.get('/:id/objections', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT o.*, u.full_name AS vendor_name, v.company_name
      FROM tender_objections o
      LEFT JOIN users u   ON o.vendor_id = u.id
      LEFT JOIN vendors v ON o.vendor_id = v.user_id
      WHERE o.tender_id = $1
      ORDER BY o.created_at DESC
    `, [req.params.id]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/tenders/:id/objections — Submit Sanggahan (Vendor) ──
router.post('/:id/objections', upload.single('attachment'), async (req, res) => {
  try {
    const { vendor_id, objection_text } = req.body;
    if (!vendor_id || !objection_text) return res.status(400).json({ success: false, message: 'vendor_id dan objection_text wajib.' });

    const attachmentPath = req.file ? `/uploads/${req.file.filename}` : null;

    await pool.query(`
      INSERT INTO tender_objections (tender_id, vendor_id, objection_text, attachment_path)
      VALUES ($1, $2, $3, $4)
    `, [req.params.id, vendor_id, objection_text, attachmentPath]);

    res.json({ success: true, message: 'Sanggahan berhasil dikirim.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/tenders/:id/objections/:objId/reply — Balas Sanggahan (Pokja) ──
router.post('/:id/objections/:objId/reply', upload.single('response_attachment'), async (req, res) => {
  try {
    const { response_text } = req.body;
    if (!response_text) return res.status(400).json({ success: false, message: 'response_text wajib.' });

    const responseAttachmentPath = req.file ? `/uploads/${req.file.filename}` : null;

    await pool.query(`
      UPDATE tender_objections 
      SET response_text = $1, response_attachment_path = $2, status = 'responded', updated_at = CURRENT_TIMESTAMP
      WHERE id = $3 AND tender_id = $4
    `, [response_text, responseAttachmentPath, req.params.objId, req.params.id]);

    res.json({ success: true, message: 'Balasan sanggahan berhasil dikirim.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/tenders/:id/contract — Ambil detail kontrak ──
router.get('/:id/contract', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT c.*, v.company_name AS vendor_name
      FROM contracts c
      LEFT JOIN vendors v ON c.vendor_id = v.user_id
      WHERE c.tender_id = $1
    `, [req.params.id]);
    res.json({ success: true, data: result.rows.length ? result.rows[0] : null });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/tenders/:id/contract — Unggah SPK & BAST (PPK) ──
router.post('/:id/contract', upload.fields([{ name: 'spk' }, { name: 'bast' }]), async (req, res) => {
  try {
    const { vendor_id, contract_number, contract_date, contract_value, status } = req.body;
    if (!vendor_id || !contract_number || !contract_value) {
      return res.status(400).json({ success: false, message: 'Data kontrak belum lengkap.' });
    }

    const spkPath = req.files?.spk ? `/uploads/${req.files.spk[0].filename}` : null;
    const bastPath = req.files?.bast ? `/uploads/${req.files.bast[0].filename}` : null;

    const existing = await pool.query(`SELECT id, spk_path, bast_path FROM contracts WHERE tender_id = $1`, [req.params.id]);

    const finalSpkPath = spkPath || (existing.rows.length ? existing.rows[0].spk_path : null);
    const finalBastPath = bastPath || (existing.rows.length ? existing.rows[0].bast_path : null);

    if (existing.rows.length > 0) {
      await pool.query(`
        UPDATE contracts 
        SET contract_number = $1, contract_date = $2, contract_value = $3, spk_path = $4, bast_path = $5, status = $6, updated_at = CURRENT_TIMESTAMP
        WHERE tender_id = $7
      `, [contract_number, contract_date || null, contract_value, finalSpkPath, finalBastPath, status || 'draft', req.params.id]);
    } else {
      await pool.query(`
        INSERT INTO contracts (tender_id, vendor_id, contract_number, contract_date, contract_value, spk_path, bast_path, status)
        VALUES ($1, $2, $3, $4, $5, $6, $7, $8)
      `, [req.params.id, vendor_id, contract_number, contract_date || null, contract_value, finalSpkPath, finalBastPath, status || 'draft']);
    }

    res.json({ success: true, message: 'Dokumen kontrak berhasil disimpan.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});
module.exports = router;
