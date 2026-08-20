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

// ── SK PANITIA (master roster, terpisah dari data master.js karena spesifik ke tender workflow) ──
// Ditaruh sebelum route GET /:id supaya "master" tidak ketangkap sebagai :id.
router.get('/master/sk-panitia', async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM sk_panitia ORDER BY created_at DESC');
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.get('/master/sk-panitia/:skId', async (req, res) => {
  try {
    const sk = await pool.query('SELECT * FROM sk_panitia WHERE id = $1', [req.params.skId]);
    if (!sk.rows.length) return res.status(404).json({ success: false, message: 'SK Panitia tidak ditemukan.' });
    const members = await pool.query('SELECT * FROM panitia WHERE sk_panitia_id = $1 ORDER BY is_ketua DESC, nama ASC', [req.params.skId]);
    res.json({ success: true, data: { ...sk.rows[0], members: members.rows } });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/master/sk-panitia', async (req, res) => {
  const client = await pool.connect();
  try {
    const { unit_kerja, nomor_sk, tanggal_sk, pejabat_penetap, pejabat_penetap_nip, tanggal_mulai, tanggal_akhir, status, members } = req.body;
    if (!unit_kerja) return res.status(400).json({ success: false, message: 'unit_kerja diperlukan.' });
    await client.query('BEGIN');
    const sk = await client.query(`
      INSERT INTO sk_panitia (unit_kerja, nomor_sk, tanggal_sk, pejabat_penetap, pejabat_penetap_nip, tanggal_mulai, tanggal_akhir, status)
      VALUES ($1, $2, $3, $4, $5, $6, $7, $8) RETURNING *
    `, [unit_kerja, nomor_sk || null, tanggal_sk || null, pejabat_penetap || null, pejabat_penetap_nip || null, tanggal_mulai || null, tanggal_akhir || null, status !== false]);
    if (Array.isArray(members)) {
      for (const m of members) {
        if (!m.nama) continue;
        await client.query(`
          INSERT INTO panitia (sk_panitia_id, nip, nama, jabatan, is_ketua)
          VALUES ($1, $2, $3, $4, $5)
        `, [sk.rows[0].id, m.nip || null, m.nama, m.jabatan || null, !!m.is_ketua]);
      }
    }
    await client.query('COMMIT');
    res.json({ success: true, data: sk.rows[0] });
  } catch (err) {
    await client.query('ROLLBACK');
    res.status(500).json({ success: false, message: err.message });
  } finally {
    client.release();
  }
});

router.put('/master/sk-panitia/:skId', async (req, res) => {
  const client = await pool.connect();
  try {
    const { unit_kerja, nomor_sk, tanggal_sk, pejabat_penetap, pejabat_penetap_nip, tanggal_mulai, tanggal_akhir, status, members } = req.body;
    await client.query('BEGIN');
    const sk = await client.query(`
      UPDATE sk_panitia SET unit_kerja=$1, nomor_sk=$2, tanggal_sk=$3, pejabat_penetap=$4, pejabat_penetap_nip=$5,
        tanggal_mulai=$6, tanggal_akhir=$7, status=$8, updated_at=CURRENT_TIMESTAMP
      WHERE id = $9 RETURNING *
    `, [unit_kerja, nomor_sk || null, tanggal_sk || null, pejabat_penetap || null, pejabat_penetap_nip || null, tanggal_mulai || null, tanggal_akhir || null, status !== false, req.params.skId]);
    if (Array.isArray(members)) {
      await client.query('DELETE FROM panitia WHERE sk_panitia_id = $1', [req.params.skId]);
      for (const m of members) {
        if (!m.nama) continue;
        await client.query(`
          INSERT INTO panitia (sk_panitia_id, nip, nama, jabatan, is_ketua)
          VALUES ($1, $2, $3, $4, $5)
        `, [req.params.skId, m.nip || null, m.nama, m.jabatan || null, !!m.is_ketua]);
      }
    }
    await client.query('COMMIT');
    res.json({ success: true, data: sk.rows[0] });
  } catch (err) {
    await client.query('ROLLBACK');
    res.status(500).json({ success: false, message: err.message });
  } finally {
    client.release();
  }
});

router.delete('/master/sk-panitia/:skId', async (req, res) => {
  try {
    await pool.query('DELETE FROM sk_panitia WHERE id = $1', [req.params.skId]);
    res.json({ success: true, message: 'SK Panitia berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/master/sk-panitia/:skId/lampiran', upload.single('file'), async (req, res) => {
  try {
    if (!req.file) return res.status(400).json({ success: false, message: 'File diperlukan.' });
    const result = await pool.query(`
      UPDATE sk_panitia SET file_sk = $1, file_path = $2, updated_at = CURRENT_TIMESTAMP WHERE id = $3 RETURNING *
    `, [req.file.originalname, req.file.filename, req.params.skId]);
    res.json({ success: true, data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
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

// ── GET /api/tenders/:id/negotiation/:vendorId — Riwayat negosiasi harga dengan vendor pemenang ──
router.get('/:id/negotiation/:vendorId', async (req, res) => {
  try {
    const participant = await pool.query(`
      SELECT bid_price, negotiated_price, negotiation_status
      FROM tender_participants
      WHERE tender_id = $1 AND vendor_id = $2
    `, [req.params.id, req.params.vendorId]);

    if (!participant.rows.length) {
      return res.status(404).json({ success: false, message: 'Peserta tender tidak ditemukan.' });
    }

    const chats = await pool.query(`
      SELECT c.*, u.full_name AS user_name, u.role
      FROM tender_negotiation_chats c
      JOIN users u ON c.user_id = u.id
      WHERE c.tender_id = $1 AND c.vendor_id = $2
      ORDER BY c.created_at ASC
    `, [req.params.id, req.params.vendorId]);

    res.json({
      success: true,
      data: {
        bid_price: participant.rows[0].bid_price,
        negotiated_price: participant.rows[0].negotiated_price,
        negotiation_status: participant.rows[0].negotiation_status,
        chats: chats.rows,
      }
    });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/tenders/:id/negotiation/:vendorId — Kirim pesan/tawaran negosiasi ──
router.post('/:id/negotiation/:vendorId', async (req, res) => {
  try {
    const { user_id, message, offered_price } = req.body;

    if (!user_id || !message) {
      return res.status(400).json({ success: false, message: 'user_id dan message diperlukan.' });
    }

    const result = await pool.query(`
      INSERT INTO tender_negotiation_chats (tender_id, vendor_id, user_id, message, offered_price)
      VALUES ($1, $2, $3, $4, $5)
      RETURNING *
    `, [req.params.id, req.params.vendorId, user_id, message, offered_price || null]);

    // Tandai negosiasi sudah berlangsung (kalau masih 'belum')
    await pool.query(`
      UPDATE tender_participants
      SET negotiation_status = 'berlangsung'
      WHERE tender_id = $1 AND vendor_id = $2 AND negotiation_status = 'belum'
    `, [req.params.id, req.params.vendorId]);

    res.json({ success: true, data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/tenders/:id/negotiation/:vendorId/finalize — Sepakati atau gagalkan negosiasi (PPK/Pokja) ──
router.post('/:id/negotiation/:vendorId/finalize', async (req, res) => {
  try {
    const { agreed, final_price } = req.body;

    if (agreed && !final_price) {
      return res.status(400).json({ success: false, message: 'final_price wajib diisi jika negosiasi disepakati.' });
    }

    await pool.query(`
      UPDATE tender_participants
      SET negotiation_status = $1, negotiated_price = $2
      WHERE tender_id = $3 AND vendor_id = $4
    `, [agreed ? 'sepakat' : 'gagal', agreed ? final_price : null, req.params.id, req.params.vendorId]);

    await pool.query(
      `INSERT INTO audit_logs (action, entity_type, description, is_success) VALUES ('UPDATE', 'Negosiasi', $1, true)`,
      [`Negosiasi tender ${req.params.id} dengan vendor ${req.params.vendorId}: ${agreed ? 'disepakati senilai Rp ' + final_price : 'gagal/tidak disepakati'}`]
    );

    res.json({ success: true, message: agreed ? 'Negosiasi berhasil disepakati.' : 'Negosiasi ditandai gagal.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/tenders/:id/eval-criteria — Daftar kriteria evaluasi tender (dikelompokkan kategori) ──
router.get('/:id/eval-criteria', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT * FROM tender_eval_criteria WHERE tender_id = $1 ORDER BY category ASC, order_index ASC, created_at ASC
    `, [req.params.id]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/tenders/:id/eval-criteria — Pokja tambah kriteria evaluasi baru ──
router.post('/:id/eval-criteria', async (req, res) => {
  try {
    const { category, name, is_mandatory, weight, required_count } = req.body;
    if (!category || !name) return res.status(400).json({ success: false, message: 'category dan name wajib diisi.' });

    const result = await pool.query(`
      INSERT INTO tender_eval_criteria (tender_id, category, name, is_mandatory, weight, required_count)
      VALUES ($1, $2, $3, COALESCE($4, true), $5, $6)
      RETURNING *
    `, [req.params.id, category, name, is_mandatory, weight || null, required_count || null]);

    res.status(201).json({ success: true, message: 'Kriteria evaluasi berhasil ditambahkan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── DELETE /api/tenders/:id/eval-criteria/:criteriaId — Hapus kriteria evaluasi ──
router.delete('/:id/eval-criteria/:criteriaId', async (req, res) => {
  try {
    const result = await pool.query(
      'DELETE FROM tender_eval_criteria WHERE id = $1 AND tender_id = $2 RETURNING id',
      [req.params.criteriaId, req.params.id]
    );
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Kriteria tidak ditemukan.' });
    res.json({ success: true, message: 'Kriteria evaluasi berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/tenders/:id/eval-scores/:vendorId — Skor evaluasi detail satu vendor ──
router.get('/:id/eval-scores/:vendorId', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT c.id AS criteria_id, c.category, c.name, c.is_mandatory, c.weight,
             s.meets_requirement, s.score, s.notes
      FROM tender_eval_criteria c
      LEFT JOIN tender_eval_scores s ON s.criteria_id = c.id AND s.vendor_id = $2
      WHERE c.tender_id = $1
      ORDER BY c.category ASC, c.order_index ASC, c.created_at ASC
    `, [req.params.id, req.params.vendorId]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/tenders/:id/eval-scores — Pokja simpan/ubah skor satu kriteria untuk satu vendor ──
router.post('/:id/eval-scores', async (req, res) => {
  try {
    const { criteria_id, vendor_id, meets_requirement, score, notes, scored_by } = req.body;
    if (!criteria_id || !vendor_id) {
      return res.status(400).json({ success: false, message: 'criteria_id dan vendor_id wajib diisi.' });
    }

    const result = await pool.query(`
      INSERT INTO tender_eval_scores (criteria_id, vendor_id, meets_requirement, score, notes, scored_by)
      VALUES ($1, $2, $3, $4, $5, $6)
      ON CONFLICT (criteria_id, vendor_id)
      DO UPDATE SET meets_requirement = EXCLUDED.meets_requirement, score = EXCLUDED.score,
                     notes = EXCLUDED.notes, scored_by = EXCLUDED.scored_by
      RETURNING *
    `, [criteria_id, vendor_id, meets_requirement, score || null, notes || null, scored_by || null]);

    res.json({ success: true, message: 'Skor berhasil disimpan.', data: result.rows[0] });
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

// ── Helper: ambil contract_id dari tender_id ──
async function getContractId(tenderId) {
  const result = await pool.query('SELECT id FROM contracts WHERE tender_id = $1', [tenderId]);
  return result.rows.length ? result.rows[0].id : null;
}

// ── TERMIN PEMBAYARAN ──────────────────────────────────────────────────────

router.get('/:id/contract/payment-terms', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const result = await pool.query('SELECT * FROM contract_payment_terms WHERE contract_id = $1 ORDER BY created_at ASC', [contractId]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/contract/payment-terms', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });

    const { term_name, amount, progress_percent, notes } = req.body;
    if (!term_name || !amount) return res.status(400).json({ success: false, message: 'term_name dan amount wajib diisi.' });

    const result = await pool.query(`
      INSERT INTO contract_payment_terms (contract_id, term_name, amount, progress_percent, notes)
      VALUES ($1, $2, $3, $4, $5) RETURNING *
    `, [contractId, term_name, amount, progress_percent || null, notes || null]);

    res.status(201).json({ success: true, message: 'Termin pembayaran berhasil ditambahkan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.patch('/:id/contract/payment-terms/:termId', upload.single('bapp'), async (req, res) => {
  try {
    const { status, payment_date, notes } = req.body;
    const bapp_file_path = req.file ? `/uploads/${req.file.filename}` : null;

    const result = await pool.query(`
      UPDATE contract_payment_terms
      SET status = COALESCE($1, status), payment_date = COALESCE($2, payment_date),
          notes = COALESCE($3, notes), bapp_file_path = COALESCE($4, bapp_file_path)
      WHERE id = $5 RETURNING *
    `, [status || null, payment_date || null, notes || null, bapp_file_path, req.params.termId]);

    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Termin tidak ditemukan.' });
    res.json({ success: true, message: 'Termin pembayaran berhasil diperbarui.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── SANKSI / DENDA KETERLAMBATAN ─────────────────────────────────────────────

router.get('/:id/contract/penalties', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const result = await pool.query('SELECT * FROM contract_penalties WHERE contract_id = $1 ORDER BY created_at DESC', [contractId]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/contract/penalties', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });

    const { days_late, penalty_rate, work_value, penalty_amount, notes } = req.body;
    if (!days_late) return res.status(400).json({ success: false, message: 'days_late wajib diisi.' });

    const result = await pool.query(`
      INSERT INTO contract_penalties (contract_id, days_late, penalty_rate, work_value, penalty_amount, notes)
      VALUES ($1, $2, $3, $4, $5, $6) RETURNING *
    `, [contractId, days_late, penalty_rate || null, work_value || null, penalty_amount || null, notes || null]);

    res.status(201).json({ success: true, message: 'Sanksi keterlambatan berhasil dicatat.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── PROGRES PEKERJAAN (DELIVERABLE) ──────────────────────────────────────────

router.get('/:id/contract/deliverables', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const result = await pool.query('SELECT * FROM contract_deliverables WHERE contract_id = $1 ORDER BY created_at ASC', [contractId]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/contract/deliverables', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });

    const { scope, deliverable_name, target_date, notes } = req.body;
    if (!deliverable_name) return res.status(400).json({ success: false, message: 'deliverable_name wajib diisi.' });

    const result = await pool.query(`
      INSERT INTO contract_deliverables (contract_id, scope, deliverable_name, target_date, notes)
      VALUES ($1, $2, $3, $4, $5) RETURNING *
    `, [contractId, scope || null, deliverable_name, target_date || null, notes || null]);

    res.status(201).json({ success: true, message: 'Item progres pekerjaan berhasil ditambahkan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.patch('/:id/contract/deliverables/:deliverableId', upload.single('document'), async (req, res) => {
  try {
    const { progress_percent, status, notes } = req.body;
    const file_path = req.file ? `/uploads/${req.file.filename}` : null;
    const received_date = status === 'selesai' ? new Date() : null;

    const result = await pool.query(`
      UPDATE contract_deliverables
      SET progress_percent = COALESCE($1, progress_percent), status = COALESCE($2, status),
          notes = COALESCE($3, notes), file_path = COALESCE($4, file_path),
          received_date = COALESCE($5, received_date)
      WHERE id = $6 RETURNING *
    `, [progress_percent || null, status || null, notes || null, file_path, received_date, req.params.deliverableId]);

    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Item progres tidak ditemukan.' });
    res.json({ success: true, message: 'Progres pekerjaan berhasil diperbarui.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── RUMUS EVALUASI RESMI (Personil, Peralatan, Sertifikat) ──────────────────
// Meniru persis fungsi hitungPersonil()/hitungPeralatan()/hitungSertifikat() di
// eproc/lib/eproc/allfunc.js (kode yang benar-benar dipakai sistem produksi lama).

const FORMULA_CATEGORIES = ['personil', 'peralatan', 'sertifikat_lain'];

// Nilai kesesuaian efektif: S=100, TS=0, selain itu pakai nilai manual (tapi kalau manual
// diisi persis 0 atau 100, dipaksa jadi 50 - meniru validasi di allfunc.js).
function resolveSuitabilityValue(suitability, manualValue) {
  if (suitability === 'S') return 100;
  if (suitability === 'TS') return 0;
  const v = Number(manualValue);
  if (isNaN(v)) return 0;
  if (v === 0 || v === 100) return 50;
  return v;
}

function round2(n) {
  return Math.round(n * 100) / 100;
}

// Hitung rasio (0-1) untuk SATU kriteria (mis. satu peran personil / satu jenis alat) berdasarkan
// item-item yang diajukan vendor untuk kriteria itu.
function calcCriteriaRatio(category, criteria, items) {
  const values = items.map(it => resolveSuitabilityValue(it.suitability, it.suitability_value));

  if (category === 'personil') {
    const requiredCount = Number(criteria.required_count) || 0;
    const filledCount = items.length;
    const totalKebutuhan = requiredCount * 100;
    const totalNilai = values.reduce((a, b) => a + b, 0);
    if (totalKebutuhan === 0) return 0;
    if (requiredCount > filledCount) return totalNilai / totalKebutuhan;
    return totalKebutuhan <= totalNilai ? 1 : totalNilai / totalKebutuhan;
  }

  if (category === 'peralatan') {
    const totalNilai = items.reduce((sum, it, i) => {
      const ownership = it.ownership_factor != null ? Number(it.ownership_factor) : 100;
      return sum + (values[i] * ownership) / 100;
    }, 0);
    return totalNilai >= 100 ? 1 : totalNilai / 100;
  }

  // sertifikat_lain
  const totalNilai = values.reduce((a, b) => a + b, 0);
  return totalNilai >= 100 ? 1 : totalNilai / 100;
}

// ── GET /api/tenders/:id/eval-category-config — Nilai maksimal per kategori (personil/peralatan/sertifikat) ──
router.get('/:id/eval-category-config', async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM tender_eval_category_config WHERE tender_id = $1', [req.params.id]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/tenders/:id/eval-category-config — Set nilai maksimal satu kategori (Pokja) ──
router.post('/:id/eval-category-config', async (req, res) => {
  try {
    const { category, max_score } = req.body;
    if (!category || max_score === undefined) {
      return res.status(400).json({ success: false, message: 'category dan max_score wajib diisi.' });
    }
    if (!FORMULA_CATEGORIES.includes(category)) {
      return res.status(400).json({ success: false, message: `Kategori ini tidak pakai rumus otomatis. Pilihan: ${FORMULA_CATEGORIES.join(', ')}.` });
    }

    const result = await pool.query(`
      INSERT INTO tender_eval_category_config (tender_id, category, max_score)
      VALUES ($1, $2, $3)
      ON CONFLICT (tender_id, category) DO UPDATE SET max_score = EXCLUDED.max_score
      RETURNING *
    `, [req.params.id, category, max_score]);

    res.json({ success: true, message: 'Nilai maksimal kategori berhasil disimpan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/tenders/:id/eval-score-items/:vendorId — Semua item (personil/alat/sertifikat) milik satu vendor ──
router.get('/:id/eval-score-items/:vendorId', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT si.*, c.category, c.name AS criteria_name, c.weight, c.required_count
      FROM tender_eval_score_items si
      JOIN tender_eval_criteria c ON c.id = si.criteria_id
      WHERE c.tender_id = $1 AND si.vendor_id = $2
      ORDER BY c.category ASC, c.order_index ASC, si.created_at ASC
    `, [req.params.id, req.params.vendorId]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/tenders/:id/eval-score-items — Tambah satu item (1 personil/1 alat/1 sertifikat) ──
router.post('/:id/eval-score-items', async (req, res) => {
  try {
    const { criteria_id, vendor_id, item_name, suitability, suitability_value, ownership_factor } = req.body;
    if (!criteria_id || !vendor_id || !item_name) {
      return res.status(400).json({ success: false, message: 'criteria_id, vendor_id, dan item_name wajib diisi.' });
    }

    const result = await pool.query(`
      INSERT INTO tender_eval_score_items (criteria_id, vendor_id, item_name, suitability, suitability_value, ownership_factor)
      VALUES ($1, $2, $3, $4, $5, $6) RETURNING *
    `, [criteria_id, vendor_id, item_name, suitability || null, suitability_value ?? null, ownership_factor ?? null]);

    res.status(201).json({ success: true, message: 'Item berhasil ditambahkan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── DELETE /api/tenders/:id/eval-score-items/:itemId — Hapus satu item ──
router.delete('/:id/eval-score-items/:itemId', async (req, res) => {
  try {
    const result = await pool.query('DELETE FROM tender_eval_score_items WHERE id = $1 RETURNING id', [req.params.itemId]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Item tidak ditemukan.' });
    res.json({ success: true, message: 'Item berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/tenders/:id/eval-formula-score/:vendorId/:category — Hitung nilai akhir kategori (rumus resmi) ──
router.get('/:id/eval-formula-score/:vendorId/:category', async (req, res) => {
  try {
    const { id: tenderId, vendorId, category } = req.params;
    if (!FORMULA_CATEGORIES.includes(category)) {
      return res.status(400).json({ success: false, message: `Kategori ini tidak pakai rumus otomatis. Pilihan: ${FORMULA_CATEGORIES.join(', ')}.` });
    }

    const criteriaResult = await pool.query(
      'SELECT * FROM tender_eval_criteria WHERE tender_id = $1 AND category = $2 ORDER BY order_index ASC, created_at ASC',
      [tenderId, category]
    );
    const itemsResult = await pool.query(`
      SELECT si.* FROM tender_eval_score_items si
      JOIN tender_eval_criteria c ON c.id = si.criteria_id
      WHERE c.tender_id = $1 AND c.category = $2 AND si.vendor_id = $3
    `, [tenderId, category, vendorId]);
    const configResult = await pool.query(
      'SELECT max_score FROM tender_eval_category_config WHERE tender_id = $1 AND category = $2',
      [tenderId, category]
    );

    const maxScore = configResult.rows.length ? Number(configResult.rows[0].max_score) : 100;

    const breakdown = criteriaResult.rows.map(criteria => {
      const items = itemsResult.rows.filter(it => it.criteria_id === criteria.id);
      const ratio = calcCriteriaRatio(category, criteria, items);
      const weight = Number(criteria.weight) || 0;
      const contribution = round2(weight * ratio);
      return { criteria_id: criteria.id, criteria_name: criteria.name, weight, item_count: items.length, ratio: round2(ratio), contribution };
    });

    const totalProsentase = Math.min(100, round2(breakdown.reduce((sum, b) => sum + b.contribution, 0)));
    const finalScore = round2((maxScore * totalProsentase) / 100);

    res.json({
      success: true,
      data: { category, max_score: maxScore, breakdown, total_prosentase: totalProsentase, final_score: finalScore },
    });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ══════════════════════════════════════════════════════════════════════════
// KELOMPOK A - Detail Paket/Tender
// (dokumen tender, panitia, SK panitia, pernyataan minat, pakta integritas,
// pihak lain, pembukaan penawaran, klarifikasi, peringkat pemenang)
// ══════════════════════════════════════════════════════════════════════════

// ── DOKUMEN TENDER ──
router.get('/:id/documents', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT d.*, u.full_name AS uploaded_by_name
      FROM tender_documents d
      LEFT JOIN users u ON d.uploaded_by = u.id
      WHERE d.tender_id = $1
      ORDER BY d.created_at DESC
    `, [req.params.id]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/documents', upload.single('file'), async (req, res) => {
  try {
    const { document_type, name, notes, uploaded_by } = req.body;
    if (!document_type || !req.file) {
      return res.status(400).json({ success: false, message: 'document_type dan file diperlukan.' });
    }
    const result = await pool.query(`
      INSERT INTO tender_documents (tender_id, document_type, name, file_path, file_size, notes, uploaded_by)
      VALUES ($1, $2, $3, $4, $5, $6, $7) RETURNING *
    `, [req.params.id, document_type, name || req.file.originalname, req.file.filename, req.file.size, notes || null, uploaded_by || null]);
    res.json({ success: true, data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/:id/documents/:docId', async (req, res) => {
  try {
    await pool.query('DELETE FROM tender_documents WHERE id = $1 AND tender_id = $2', [req.params.docId, req.params.id]);
    res.json({ success: true, message: 'Dokumen berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── PANITIA PER PAKET ──
router.get('/:id/panitia', async (req, res) => {
  try {
    const result = await pool.query(
      'SELECT * FROM tender_panitia WHERE tender_id = $1 ORDER BY is_ketua DESC, nama ASC',
      [req.params.id]
    );
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// Penugasan panitia ke paket, biasanya diambil massal dari roster SK panitia (bisa juga input manual).
router.post('/:id/panitia', async (req, res) => {
  const client = await pool.connect();
  try {
    const { members, created_by } = req.body;
    if (!Array.isArray(members) || members.length === 0) {
      return res.status(400).json({ success: false, message: 'Daftar anggota panitia diperlukan.' });
    }
    await client.query('BEGIN');
    const lockCheck = await client.query('SELECT locked FROM tender_panitia WHERE tender_id = $1 LIMIT 1', [req.params.id]);
    if (lockCheck.rows.length && lockCheck.rows[0].locked) {
      await client.query('ROLLBACK');
      return res.status(400).json({ success: false, message: 'Tim panitia sudah dikunci, tidak bisa diubah.' });
    }
    await client.query('DELETE FROM tender_panitia WHERE tender_id = $1', [req.params.id]);
    for (const m of members) {
      await client.query(`
        INSERT INTO tender_panitia (tender_id, nip, nama, jabatan, is_ketua, created_by)
        VALUES ($1, $2, $3, $4, $5, $6)
      `, [req.params.id, m.nip || null, m.nama, m.jabatan || null, !!m.is_ketua, created_by || null]);
    }
    await client.query('COMMIT');
    res.json({ success: true, message: 'Panitia berhasil ditugaskan.' });
  } catch (err) {
    await client.query('ROLLBACK');
    res.status(500).json({ success: false, message: err.message });
  } finally {
    client.release();
  }
});

router.delete('/:id/panitia/:panitiaId', async (req, res) => {
  try {
    await pool.query('DELETE FROM tender_panitia WHERE id = $1 AND tender_id = $2', [req.params.panitiaId, req.params.id]);
    res.json({ success: true, message: 'Anggota panitia berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.patch('/:id/panitia/lock', async (req, res) => {
  try {
    await pool.query('UPDATE tender_panitia SET locked = true WHERE tender_id = $1', [req.params.id]);
    res.json({ success: true, message: 'Tim panitia berhasil dikunci.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// Validasi pemenang oleh panitia (approve/reject), langkah tambahan sebelum pemenang final diumumkan.
router.patch('/:id/panitia/:panitiaId/validasi-pemenang', async (req, res) => {
  try {
    const { validasi, catatan } = req.body;
    if (!['setuju', 'tolak'].includes(validasi)) {
      return res.status(400).json({ success: false, message: 'validasi harus setuju atau tolak.' });
    }
    if (validasi === 'tolak' && !catatan) {
      return res.status(400).json({ success: false, message: 'Catatan wajib diisi jika menolak.' });
    }
    const result = await pool.query(`
      UPDATE tender_panitia SET validasi_pemenang = $1, validasi_pemenang_catatan = $2
      WHERE id = $3 AND tender_id = $4 RETURNING *
    `, [validasi, catatan || null, req.params.panitiaId, req.params.id]);
    res.json({ success: true, data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── PERNYATAAN MINAT ──
router.get('/:id/pernyataan-minat/:vendorId', async (req, res) => {
  try {
    const result = await pool.query(
      'SELECT * FROM tender_pernyataan_minat WHERE tender_id = $1 AND vendor_id = $2',
      [req.params.id, req.params.vendorId]
    );
    res.json({ success: true, data: result.rows[0] || null });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/pernyataan-minat', upload.single('penerima_kuasa_file'), async (req, res) => {
  try {
    const { vendor_id, nama, jabatan, alamat, telepon, email, penerima_kuasa, penerima_kuasa_jabatan, penerima_kuasa_ktp } = req.body;
    if (!vendor_id) return res.status(400).json({ success: false, message: 'vendor_id diperlukan.' });
    const result = await pool.query(`
      INSERT INTO tender_pernyataan_minat
        (tender_id, vendor_id, nama, jabatan, alamat, telepon, email, penerima_kuasa, penerima_kuasa_jabatan, penerima_kuasa_ktp, penerima_kuasa_file)
      VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11) RETURNING *
    `, [req.params.id, vendor_id, nama || null, jabatan || null, alamat || null, telepon || null, email || null,
        penerima_kuasa || null, penerima_kuasa_jabatan || null, penerima_kuasa_ktp || null, req.file ? req.file.filename : null]);
    res.json({ success: true, data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── PAKTA INTEGRITAS ──
router.get('/:id/pakta-integritas', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT p.*, u.full_name AS user_name
      FROM tender_pakta_integritas p
      JOIN users u ON p.user_id = u.id
      WHERE p.tender_id = $1
      ORDER BY p.created_at DESC
    `, [req.params.id]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/pakta-integritas', async (req, res) => {
  try {
    const { user_id, kode, jenis, created_by } = req.body;
    if (!user_id) return res.status(400).json({ success: false, message: 'user_id diperlukan.' });
    const result = await pool.query(`
      INSERT INTO tender_pakta_integritas (tender_id, user_id, kode, jenis, created_by)
      VALUES ($1, $2, $3, $4, $5)
      ON CONFLICT (tender_id, user_id, jenis) DO UPDATE SET kode = EXCLUDED.kode, created_at = CURRENT_TIMESTAMP
      RETURNING *
    `, [req.params.id, user_id, kode || null, jenis || 'REKANAN', created_by || null]);
    res.json({ success: true, message: 'Validasi pakta integritas berhasil.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── PIHAK LAIN ──
router.get('/:id/pihak-lain', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT pl.*, u.full_name, u.email, u.role_label
      FROM tender_pihak_lain pl
      JOIN users u ON pl.user_id = u.id
      WHERE pl.tender_id = $1 AND pl.status = true
      ORDER BY u.full_name ASC
    `, [req.params.id]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/pihak-lain', async (req, res) => {
  try {
    const { user_id } = req.body;
    if (!user_id) return res.status(400).json({ success: false, message: 'user_id diperlukan.' });
    const result = await pool.query(`
      INSERT INTO tender_pihak_lain (tender_id, user_id, status)
      VALUES ($1, $2, true)
      ON CONFLICT (tender_id, user_id) DO UPDATE SET status = true
      RETURNING *
    `, [req.params.id, user_id]);
    res.json({ success: true, data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/:id/pihak-lain/:userId', async (req, res) => {
  try {
    await pool.query('DELETE FROM tender_pihak_lain WHERE tender_id = $1 AND user_id = $2', [req.params.id, req.params.userId]);
    res.json({ success: true, message: 'Pihak lain berhasil dihapus dari paket ini.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── PEMBUKAAN PENAWARAN (sampul 1 dan sampul 2) ──
router.get('/:id/pembukaan/:tahap', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT pv.*, u.full_name AS user_name
      FROM tender_pembukaan_validasi pv
      JOIN users u ON pv.user_id = u.id
      WHERE pv.tender_id = $1 AND pv.tahap = $2
      ORDER BY pv.created_at ASC
    `, [req.params.id, req.params.tahap]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/pembukaan', async (req, res) => {
  try {
    const { user_id, kode, jenis, tahap } = req.body;
    if (!user_id) return res.status(400).json({ success: false, message: 'user_id diperlukan.' });
    const result = await pool.query(`
      INSERT INTO tender_pembukaan_validasi (tender_id, user_id, kode, jenis, tahap)
      VALUES ($1, $2, $3, $4, $5) RETURNING *
    `, [req.params.id, user_id, kode || null, jenis || null, tahap || 1]);
    res.json({ success: true, message: 'Validasi pembukaan berhasil.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── KLARIFIKASI DOKUMEN (dokumen formal, terpisah dari chat aanwijzing) ──
router.get('/:id/klarifikasi-dokumen', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT k.*, u.full_name AS vendor_name
      FROM tender_klarifikasi_dokumen k
      LEFT JOIN users u ON k.vendor_id = u.id
      WHERE k.tender_id = $1
      ORDER BY k.created_at ASC
    `, [req.params.id]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// Vendor upload dokumen klarifikasi.
router.post('/:id/klarifikasi-dokumen', upload.single('file'), async (req, res) => {
  try {
    const { vendor_id, nama, notes, created_by } = req.body;
    if (!req.file) return res.status(400).json({ success: false, message: 'File diperlukan.' });
    const result = await pool.query(`
      INSERT INTO tender_klarifikasi_dokumen (tender_id, nama, file_path, file_size, notes, vendor_id, created_by)
      VALUES ($1, $2, $3, $4, $5, $6, $7) RETURNING *
    `, [req.params.id, nama || 'Dokumen Klarifikasi', req.file.filename, req.file.size, notes || null, vendor_id || null, created_by || null]);
    res.json({ success: true, message: 'Dokumen klarifikasi berhasil disimpan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// Panitia balas dengan dokumen tanggapan aanwijzing (parent_id = dokumen yang ditanggapi).
router.post('/:id/klarifikasi-dokumen/:docId/tanggapan', upload.single('file'), async (req, res) => {
  try {
    const { notes, created_by } = req.body;
    if (!req.file) return res.status(400).json({ success: false, message: 'File diperlukan.' });
    const result = await pool.query(`
      INSERT INTO tender_klarifikasi_dokumen (tender_id, nama, file_path, file_size, notes, parent_id, created_by)
      VALUES ($1, $2, $3, $4, $5, $6, $7) RETURNING *
    `, [req.params.id, 'Dokumen Tanggapan Aanwijzing', req.file.filename, req.file.size, notes || null, req.params.docId, created_by || null]);
    res.json({ success: true, message: 'Tanggapan berhasil disimpan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/:id/klarifikasi-dokumen/:docId', async (req, res) => {
  try {
    await pool.query('DELETE FROM tender_klarifikasi_dokumen WHERE id = $1 AND tender_id = $2', [req.params.docId, req.params.id]);
    res.json({ success: true, message: 'Dokumen klarifikasi berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── UNDANGAN KLARIFIKASI (jadwal pertemuan resmi ke vendor) ──
router.get('/:id/undangan-klarifikasi', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT uk.*, u.full_name AS vendor_name, u.email AS vendor_email
      FROM tender_undangan_klarifikasi uk
      JOIN users u ON uk.vendor_id = u.id
      WHERE uk.tender_id = $1
      ORDER BY uk.tanggal_undangan DESC
    `, [req.params.id]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/undangan-klarifikasi', async (req, res) => {
  try {
    const { vendor_id, tanggal_undangan, jam, peserta, pelaksanaan, tempat, keterangan, created_by } = req.body;
    if (!vendor_id || !tanggal_undangan) {
      return res.status(400).json({ success: false, message: 'vendor_id dan tanggal_undangan diperlukan.' });
    }
    const result = await pool.query(`
      INSERT INTO tender_undangan_klarifikasi (tender_id, vendor_id, tanggal_undangan, jam, peserta, pelaksanaan, tempat, keterangan, created_by)
      VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9) RETURNING *
    `, [req.params.id, vendor_id, tanggal_undangan, jam || null, peserta || null, pelaksanaan || null, tempat || null, keterangan || null, created_by || null]);
    res.json({ success: true, message: 'Undangan klarifikasi berhasil disimpan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── PERINGKAT PEMENANG (urutan pemenang utama + cadangan) ──
router.get('/:id/peringkat-pemenang', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT pp.*, u.full_name AS vendor_name
      FROM tender_pemenang_peringkat pp
      JOIN users u ON pp.vendor_id = u.id
      WHERE pp.tender_id = $1
      ORDER BY pp.peringkat ASC
    `, [req.params.id]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/peringkat-pemenang', async (req, res) => {
  try {
    const { vendor_id, peringkat, keterangan, created_by } = req.body;
    if (!vendor_id || !peringkat) {
      return res.status(400).json({ success: false, message: 'vendor_id dan peringkat diperlukan.' });
    }
    const result = await pool.query(`
      INSERT INTO tender_pemenang_peringkat (tender_id, vendor_id, peringkat, keterangan, created_by)
      VALUES ($1, $2, $3, $4, $5)
      ON CONFLICT (tender_id, peringkat) DO UPDATE SET vendor_id = EXCLUDED.vendor_id, keterangan = EXCLUDED.keterangan
      RETURNING *
    `, [req.params.id, vendor_id, peringkat, keterangan || null, created_by || null]);
    res.json({ success: true, data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/:id/peringkat-pemenang/:rankId', async (req, res) => {
  try {
    await pool.query('DELETE FROM tender_pemenang_peringkat WHERE id = $1 AND tender_id = $2', [req.params.rankId, req.params.id]);
    res.json({ success: true, message: 'Data peringkat berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
