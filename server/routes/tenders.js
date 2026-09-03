const express = require('express');
const router  = express.Router();
const { pool } = require('../db');
const { createUpload, handleUploadError } = require('../lib/upload');
const { logActivity } = require('../lib/activityLog');
const { sendMail } = require('../lib/mailer');
const { requireAuth, optionalAuth } = require('../lib/authMiddleware');

// ── Konfigurasi Multer ──
const upload = createUpload('tenders');

// ── GET /api/tenders — Daftar semua tender dengan filter (publik: HPS cuma ditampilkan
// kalau pemanggil sudah login, supaya tidak jadi patokan vendor sebelum tender ditutup) ──
router.get('/', optionalAuth, async (req, res) => {
  try {
    const { status, method, search, page = 1, limit = 20 } = req.query;
    const offset = (parseInt(page) - 1) * parseInt(limit);

    let sql = `
      SELECT
        t.id, t.tender_number, t.title, t.method, t.category,
        t.pagu_anggaran, ${req.user ? 't.hps' : 'NULL AS hps'}, t.status,
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

// ── GET /api/tenders/public-stats — Angka ringkas untuk strip statistik di portal publik
// (landing page). Sengaja cuma hitungan (bukan nilai rupiah/HPS) supaya aman ditampilkan
// tanpa login. Ditaruh SEBELUM route GET /:id (pelajaran route-ordering dari kelompok A). ──
router.get('/public-stats', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT
        COUNT(*) FILTER (WHERE status IN ('pengumuman','pendaftaran','penawaran','evaluasi','pemenang','masa_sanggah')) AS tender_aktif,
        COUNT(*) FILTER (WHERE status = 'kontrak') AS kontrak_berjalan,
        COUNT(*) FILTER (WHERE created_at >= date_trunc('year', CURRENT_DATE)) AS total_tahun_ini
      FROM tenders
    `);
    const row = result.rows[0];
    res.json({
      success: true,
      data: {
        tender_aktif: parseInt(row.tender_aktif, 10),
        kontrak_berjalan: parseInt(row.kontrak_berjalan, 10),
        total_tahun_ini: parseInt(row.total_tahun_ini, 10),
      },
    });
  } catch (err) {
    console.error('[GET /tenders/public-stats]', err);
    res.status(500).json({ success: false, message: 'Gagal mengambil statistik.' });
  }
});

// ── SK PANITIA (master roster, terpisah dari data master.js karena spesifik ke tender workflow) ──
// Ditaruh sebelum route GET /:id supaya "master" tidak ketangkap sebagai :id.
router.get('/master/sk-panitia', requireAuth, async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM sk_panitia ORDER BY created_at DESC');
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.get('/master/sk-panitia/:skId', requireAuth, async (req, res) => {
  try {
    const sk = await pool.query('SELECT * FROM sk_panitia WHERE id = $1', [req.params.skId]);
    if (!sk.rows.length) return res.status(404).json({ success: false, message: 'SK Panitia tidak ditemukan.' });
    const members = await pool.query('SELECT * FROM panitia WHERE sk_panitia_id = $1 ORDER BY is_ketua DESC, nama ASC', [req.params.skId]);
    res.json({ success: true, data: { ...sk.rows[0], members: members.rows } });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/master/sk-panitia', requireAuth, async (req, res) => {
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

router.put('/master/sk-panitia/:skId', requireAuth, async (req, res) => {
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

router.delete('/master/sk-panitia/:skId', requireAuth, async (req, res) => {
  try {
    await pool.query('DELETE FROM sk_panitia WHERE id = $1', [req.params.skId]);
    res.json({ success: true, message: 'SK Panitia berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/master/sk-panitia/:skId/lampiran', requireAuth, upload.single('file'), async (req, res) => {
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

// ── GET /api/tenders/:id — Detail satu tender (publik: HPS cuma ditampilkan kalau
// pemanggil sudah login, supaya tidak jadi patokan vendor sebelum tender ditutup) ──
router.get('/:id', optionalAuth, async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT t.id, t.tender_number, t.title, t.method, t.category, t.description,
             t.pagu_anggaran, ${req.user ? 't.hps' : 'NULL AS hps'}, t.status, t.work_location,
             t.submission_deadline, t.winner_announcement, t.created_at,
             t.ppk_id, t.pokja_lead_id, t.procurement_request_id,
             u_ppk.full_name AS ppk_name, u_pokja.full_name AS pokja_lead_name
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

// ── Seluruh endpoint di bawah ini WAJIB login (proses internal pengadaan: dokumen, panitia,
// evaluasi, negosiasi, kontrak, dst). Cuma GET / dan GET /:id di atas yang publik. ──
router.use(requireAuth);

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

// Catatan: endpoint PATCH /:id/status (lama) sudah dihapus dari sini - isinya dulu masih
// pakai vocabulary status peninggalan mock data lama (proses_review/tender_buka/selesai dst,
// sama seperti bug STATUS_OPTIONS di TenderTable.jsx yang sudah diperbaiki), sudah lama
// tergantikan penuh oleh /:id/stage di bawah ini (vocabulary yang benar, dipakai frontend),
// dan dikonfirmasi (grep ke seluruh src/) tidak ada satupun kode yang masih memanggilnya.

// ── PATCH /api/tenders/:id/stage — Update tahapan tender (Oleh Pokja) ──
router.patch('/:id/stage', async (req, res) => {
  try {
    const { status, user_id } = req.body; // status baru
    // Catatan: 'masa_sanggah' dan 'kontrak' sengaja ditambahkan ke daftar ini supaya konsisten
    // dengan procurementPhases.js (7 tahap) dan tab "Kontrak & BAST" yang mensyaratkan status
    // 'kontrak' (getTenderPhaseIndex >= 6) - sebelumnya kedua status ini ditolak di sini padahal
    // sudah didefinisikan di tempat lain, membuat tab Kontrak tidak pernah bisa dicapai lewat UI.
    const allowed = ['draft','pengumuman','pendaftaran','penawaran','evaluasi','pemenang','masa_sanggah','kontrak','selesai','dibatalkan'];
    if (!allowed.includes(status)) {
      return res.status(400).json({ success: false, message: 'Tahapan tender tidak valid.' });
    }
    await pool.query('UPDATE tenders SET status = $1 WHERE id = $2', [status, req.params.id]);

    logActivity({
      tenderId: req.params.id, posisi: `Tahapan tender diubah ke: ${status}`,
      flow: 'tender', userId: user_id, ip: req.ip,
    });

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
    if (req.user.role === 'vendor' && String(vendor_id) !== String(req.user.id)) {
      return res.status(403).json({ success: false, message: 'Anda cuma bisa mendaftarkan akun vendor Anda sendiri.' });
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

// ── GET /api/tenders/:id/participants — Lihat Peserta Tender (Oleh Pokja/PPK/Admin, melihat
// SEMUA peserta termasuk harga penawaran satu sama lain - makanya dibatasi role internal saja).
// Vendor pakai endpoint terpisah di bawah (/participants/me) yang cuma kembalikan baris
// miliknya sendiri, supaya tidak bisa mengintip harga penawaran kompetitor. ──
router.get('/:id/participants', async (req, res) => {
  try {
    if (req.user.role === 'vendor') {
      return res.status(403).json({ success: false, message: 'Gunakan endpoint /participants/me untuk melihat status keikutsertaan Anda.' });
    }
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

// ── GET /api/tenders/:id/participants/me — Status keikutsertaan vendor yang login sendiri
// (dipakai vendor untuk tahu apakah dia sudah terdaftar/jadi pemenang di tender ini, tanpa
// bisa mengintip data peserta lain seperti harga penawaran kompetitor) ──
router.get('/:id/participants/me', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT tp.*, v.company_name, v.company_type, v.city
      FROM tender_participants tp
      JOIN vendors v ON tp.vendor_id = v.user_id
      WHERE tp.tender_id = $1 AND tp.vendor_id = $2
    `, [req.params.id, req.user.id]);
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
    if (req.user.role === 'vendor' && String(vendor_id) !== String(req.user.id)) {
      return res.status(403).json({ success: false, message: 'Anda cuma bisa mengirim penawaran atas nama akun vendor Anda sendiri.' });
    }

    const document_path = req.file ? `/uploads/${req.file.filename}` : null;

    // Cek status tender
    const tender = await pool.query('SELECT status, title FROM tenders WHERE id = $1', [tenderId]);
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

    // Email konfirmasi ke vendor - meniru email/dokumen_penawaran_upload.php di sistem lama
    const vendorInfo = await pool.query(
      `SELECT u.email, v.company_name FROM users u JOIN vendors v ON v.user_id = u.id WHERE u.id = $1`,
      [vendor_id]
    );
    if (vendorInfo.rows.length && vendorInfo.rows[0].email) {
      sendMail({
        to: vendorInfo.rows[0].email,
        subject: `Konfirmasi Penawaran Diterima: ${tender.rows[0].title || tenderId}`,
        html: `
          <p>Yth. ${vendorInfo.rows[0].company_name},</p>
          <p>Penawaran Anda untuk paket pengadaan <strong>${tender.rows[0].title || ''}</strong> telah <strong>berhasil kami terima</strong> dengan nilai penawaran Rp ${Number(bid_price).toLocaleString('id-ID')}.</p>
          <p>Anda dapat memantau perkembangan proses evaluasi lewat menu "Paket Pengadaan" pada akun Anda.</p>
          <p>Terima kasih.<br/>Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia</p>
        `,
      }).catch(err => console.error('[BID CONFIRM MAIL]', err));
    }

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
    const { vendor_id, user_id } = req.body;

    // Reset status pemenang untuk peserta lain
    await pool.query(`UPDATE tender_participants SET is_winner = false WHERE tender_id = $1`, [req.params.id]);

    // Set pemenang baru
    await pool.query(`
      UPDATE tender_participants
      SET is_winner = true, status = 'winner'
      WHERE tender_id = $1 AND vendor_id = $2
    `, [req.params.id, vendor_id]);

    const winnerName = await pool.query('SELECT full_name FROM users WHERE id = $1', [vendor_id]);
    logActivity({
      tenderId: req.params.id, posisi: 'Penetapan Pemenang Tender',
      keterangan: `Pemenang ditetapkan: ${winnerName.rows[0]?.full_name || vendor_id}`,
      flow: 'tender', userId: user_id, ip: req.ip,
    });

    res.json({ success: true, message: 'Pemenang tender berhasil ditetapkan.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── TAHAPAN TENDER + RESCHEDULE (padanan PAKET_TAHAP + PAKET_TAHAP_RESCHEDULE eProc lama) ──
const STAGE_KEYS = ['pengumuman', 'pendaftaran', 'penawaran', 'evaluasi', 'pemenang', 'masa_sanggah', 'kontrak'];

// GET /api/tenders/:id/stages — daftar tahapan + tanggalnya (auto-buat baris kosong kalau belum ada)
router.get('/:id/stages', async (req, res) => {
  try {
    const existing = await pool.query('SELECT * FROM tender_stages WHERE tender_id = $1', [req.params.id]);
    const existingKeys = existing.rows.map(s => s.stage_key);
    const missing = STAGE_KEYS.filter(k => !existingKeys.includes(k));

    for (const key of missing) {
      await pool.query(
        `INSERT INTO tender_stages (tender_id, stage_key) VALUES ($1, $2) ON CONFLICT (tender_id, stage_key) DO NOTHING`,
        [req.params.id, key]
      );
    }

    const result = await pool.query('SELECT * FROM tender_stages WHERE tender_id = $1', [req.params.id]);
    const sorted = result.rows.sort((a, b) => STAGE_KEYS.indexOf(a.stage_key) - STAGE_KEYS.indexOf(b.stage_key));
    res.json({ success: true, data: sorted });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// POST /api/tenders/:id/stages/:stageKey/reschedule — ubah tanggal satu tahap, catat riwayat
router.post('/:id/stages/:stageKey/reschedule', async (req, res) => {
  try {
    const { start_date, end_date, alasan, user_id } = req.body;
    if (!STAGE_KEYS.includes(req.params.stageKey)) {
      return res.status(400).json({ success: false, message: 'Tahapan tidak dikenal.' });
    }
    if (!start_date && !end_date) {
      return res.status(400).json({ success: false, message: 'Tanggal baru wajib diisi.' });
    }

    let stage = await pool.query(
      'SELECT * FROM tender_stages WHERE tender_id = $1 AND stage_key = $2',
      [req.params.id, req.params.stageKey]
    );
    if (!stage.rows.length) {
      stage = await pool.query(
        `INSERT INTO tender_stages (tender_id, stage_key) VALUES ($1, $2) RETURNING *`,
        [req.params.id, req.params.stageKey]
      );
    }
    const old = stage.rows[0];

    await pool.query(
      `INSERT INTO tender_stage_reschedule_history
        (tender_stage_id, old_start_date, old_end_date, new_start_date, new_end_date, alasan, created_by)
       VALUES ($1, $2, $3, $4, $5, $6, $7)`,
      [old.id, old.start_date, old.end_date, start_date || old.start_date, end_date || old.end_date, alasan || null, user_id || null]
    );

    const updated = await pool.query(
      `UPDATE tender_stages
       SET start_date = COALESCE($1, start_date), end_date = COALESCE($2, end_date),
           reschedule_count = reschedule_count + 1, updated_at = CURRENT_TIMESTAMP
       WHERE id = $3 RETURNING *`,
      [start_date || null, end_date || null, old.id]
    );

    logActivity({
      tenderId: req.params.id, posisi: `Reschedule Tahapan: ${req.params.stageKey}`,
      keterangan: alasan, flow: 'tender', userId: user_id, ip: req.ip,
    });

    // Email pemberitahuan ke seluruh vendor peserta - meniru email/reschedule_jadwal.php
    (async () => {
      try {
        const tenderInfo = await pool.query('SELECT title FROM tenders WHERE id = $1', [req.params.id]);
        const vendors = await pool.query(`
          SELECT u.email, v.company_name FROM tender_participants tp
          JOIN users u ON u.id = tp.vendor_id JOIN vendors v ON v.user_id = u.id
          WHERE tp.tender_id = $1 AND u.email IS NOT NULL
        `, [req.params.id]);
        const title = tenderInfo.rows[0]?.title || '';
        const fmt = (d) => d ? new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-';
        for (const v of vendors.rows) {
          sendMail({
            to: v.email,
            subject: `Perubahan Jadwal Tahapan ${req.params.stageKey}: ${title}`,
            html: `
              <p>Yth. ${v.company_name},</p>
              <p>Jadwal tahapan <strong>${req.params.stageKey}</strong> pada paket pengadaan <strong>${title}</strong> telah diubah menjadi:</p>
              <ul>
                <li>Tanggal mulai: ${fmt(updated.rows[0].start_date)}</li>
                <li>Tanggal selesai: ${fmt(updated.rows[0].end_date)}</li>
              </ul>
              ${alasan ? `<p>Alasan perubahan: ${alasan}</p>` : ''}
              <p>Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia</p>
            `,
          }).catch(err => console.error('[RESCHEDULE MAIL]', err));
        }
      } catch (mailErr) {
        console.error('[RESCHEDULE MAIL BATCH]', mailErr);
      }
    })();

    res.json({ success: true, message: 'Tahapan berhasil dijadwalkan ulang.', data: updated.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// GET /api/tenders/:id/stages/:stageKey/reschedule-history — riwayat reschedule satu tahap
router.get('/:id/stages/:stageKey/reschedule-history', async (req, res) => {
  try {
    const stage = await pool.query(
      'SELECT id FROM tender_stages WHERE tender_id = $1 AND stage_key = $2',
      [req.params.id, req.params.stageKey]
    );
    if (!stage.rows.length) return res.json({ success: true, data: [] });

    const result = await pool.query(`
      SELECT h.*, u.full_name AS user_name
      FROM tender_stage_reschedule_history h
      LEFT JOIN users u ON h.created_by = u.id
      WHERE h.tender_stage_id = $1
      ORDER BY h.created_at DESC
    `, [stage.rows[0].id]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/tenders/:id/activity-log — Timeline rekam jejak tender (padanan REKAM_JEJAK eProc lama) ──
router.get('/:id/activity-log', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT a.*, u.full_name AS user_name
      FROM tender_activity_logs a
      LEFT JOIN users u ON a.user_id = u.id
      WHERE a.tender_id = $1
      ORDER BY a.created_at ASC
    `, [req.params.id]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/tenders/:id/negotiation/:vendorId — Riwayat negosiasi harga dengan vendor pemenang ──
const UUID_RE = /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i;
router.get('/:id/negotiation/:vendorId', async (req, res) => {
  try {
    if (!UUID_RE.test(req.params.vendorId)) {
      return res.status(400).json({ success: false, message: 'ID vendor tidak valid.' });
    }
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

    // Tandai negosiasi sudah berlangsung (kalau masih 'belum') - dan kalau memang barusan
    // berubah (rowCount > 0) DAN pemicunya panitia (bukan vendor sendiri yang mulai chat),
    // kirim email undangan negosiasi ke vendor, meniru email/negosiasi_paket.php /
    // undangan_negosiasi_chat.php di sistem lama.
    const transition = await pool.query(`
      UPDATE tender_participants
      SET negotiation_status = 'berlangsung'
      WHERE tender_id = $1 AND vendor_id = $2 AND negotiation_status = 'belum'
      RETURNING tender_id
    `, [req.params.id, req.params.vendorId]);

    if (transition.rows.length) {
      const sender = await pool.query('SELECT role FROM users WHERE id = $1', [user_id]);
      if (sender.rows.length && sender.rows[0].role !== 'vendor') {
        const info = await pool.query(`
          SELECT t.title, u.email, v.company_name
          FROM tenders t, users u JOIN vendors v ON v.user_id = u.id
          WHERE t.id = $1 AND u.id = $2
        `, [req.params.id, req.params.vendorId]);
        if (info.rows.length && info.rows[0].email) {
          sendMail({
            to: info.rows[0].email,
            subject: `Undangan Negosiasi: ${info.rows[0].title}`,
            html: `
              <p>Yth. ${info.rows[0].company_name},</p>
              <p>Anda diundang untuk melakukan negosiasi harga terkait paket pengadaan <strong>${info.rows[0].title}</strong> dengan Pokja Pemilihan.</p>
              <p>Silakan masuk ke akun Anda dan buka tab "Negosiasi" pada detail paket untuk menanggapi.</p>
              <p>Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia</p>
            `,
          }).catch(err => console.error('[NEGOTIATION INVITE MAIL]', err));
        }
      }
    }

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

    // Cek dulu apakah ini pesan PERTAMA dari panitia (bukan vendor) di paket ini - kalau ya,
    // ini dianggap "sesi aanwijzing dibuka" dan dikirim email ke seluruh vendor peserta, meniru
    // email/aanwijzing_publish.php di sistem lama. Dicek SEBELUM insert supaya baris yang baru
    // saja dibuat tidak ikut terhitung "sudah pernah ada".
    const senderRole = await pool.query('SELECT role FROM users WHERE id = $1', [user_id]);
    const isFirstStaffMessage = senderRole.rows.length && senderRole.rows[0].role !== 'vendor'
      ? (await pool.query(`
          SELECT 1 FROM tender_aanwijzing_chats c JOIN users u ON c.user_id = u.id
          WHERE c.tender_id = $1 AND u.role != 'vendor' LIMIT 1
        `, [req.params.id])).rows.length === 0
      : false;

    const result = await pool.query(`
      INSERT INTO tender_aanwijzing_chats (tender_id, user_id, message)
      VALUES ($1, $2, $3)
      RETURNING *
    `, [req.params.id, user_id, message]);

    if (isFirstStaffMessage) {
      const tenderInfo = await pool.query('SELECT title FROM tenders WHERE id = $1', [req.params.id]);
      const vendors = await pool.query(`
        SELECT u.email, v.company_name FROM tender_participants tp
        JOIN users u ON u.id = tp.vendor_id JOIN vendors v ON v.user_id = u.id
        WHERE tp.tender_id = $1 AND u.email IS NOT NULL
      `, [req.params.id]);
      const title = tenderInfo.rows[0]?.title || '';
      for (const v of vendors.rows) {
        sendMail({
          to: v.email,
          subject: `Sesi Aanwijzing Dibuka: ${title}`,
          html: `
            <p>Yth. ${v.company_name},</p>
            <p>Sesi rapat penjelasan (aanwijzing) untuk paket pengadaan <strong>${title}</strong> telah dibuka oleh Pokja Pemilihan.</p>
            <p>Silakan masuk ke akun Anda dan buka tab "Aanwijzing" pada detail paket untuk mengikuti tanya jawab.</p>
            <p>Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia</p>
          `,
        }).catch(err => console.error('[AANWIJZING PUBLISH MAIL]', err));
      }
    }

    res.json({ success: true, data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/tenders/:id/aanwijzing/confirmations — Daftar vendor yang konfirmasi hadir ──
// Meniru PESAN='CONFIRMED' di PHPSHOUTBOX eProc lama
router.get('/:id/aanwijzing/confirmations', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT a.id, a.user_id, a.created_at, u.full_name AS user_name, v.company_name
      FROM tender_aanwijzing_chats a
      JOIN users u ON a.user_id = u.id
      LEFT JOIN vendors v ON v.user_id = a.user_id
      WHERE a.tender_id = $1 AND a.is_confirmation = true
      ORDER BY a.created_at ASC
    `, [req.params.id]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/tenders/:id/aanwijzing/confirm — Vendor konfirmasi hadir sesi aanwijzing ──
router.post('/:id/aanwijzing/confirm', async (req, res) => {
  try {
    const { user_id } = req.body;
    if (!user_id) return res.status(400).json({ success: false, message: 'user_id diperlukan.' });

    const existing = await pool.query(
      `SELECT id FROM tender_aanwijzing_chats WHERE tender_id = $1 AND user_id = $2 AND is_confirmation = true`,
      [req.params.id, user_id]
    );
    if (existing.rows.length) {
      return res.status(409).json({ success: false, message: 'Anda sudah konfirmasi hadir sebelumnya.' });
    }

    const result = await pool.query(`
      INSERT INTO tender_aanwijzing_chats (tender_id, user_id, message, is_confirmation)
      VALUES ($1, $2, 'Konfirmasi hadir sesi aanwijzing', true)
      RETURNING *
    `, [req.params.id, user_id]);

    res.status(201).json({ success: true, message: 'Kehadiran berhasil dikonfirmasi.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── CHAT UMUM PER PAKET (padanan CHATSHOUTBOX eProc lama) ──
// Dipakai untuk chat 1-ke-1 panitia<->vendor di berbagai konteks (jenis_chat), TERPISAH dari
// chat aanwijzing (broadcast satu ruang) dan chat negosiasi (sudah ada endpoint sendiri).

// GET /api/tenders/:id/general-chat/:vendorId?jenis=umum
router.get('/:id/general-chat/:vendorId', async (req, res) => {
  try {
    const jenis = req.query.jenis || 'umum';

    // Tandai pesan yang bukan dari pengguna yang sedang membuka jadi terbaca DULU,
    // supaya hasil SELECT setelahnya sudah mencerminkan status is_read yang terbaru
    if (req.query.reader_id) {
      await pool.query(
        `UPDATE tender_general_chats SET is_read = true
         WHERE tender_id = $1 AND vendor_id = $2 AND jenis_chat = $3 AND user_id != $4 AND is_read = false`,
        [req.params.id, req.params.vendorId, jenis, req.query.reader_id]
      );
    }

    const result = await pool.query(`
      SELECT c.*, u.full_name AS user_name, u.role
      FROM tender_general_chats c
      JOIN users u ON c.user_id = u.id
      WHERE c.tender_id = $1 AND c.vendor_id = $2 AND c.jenis_chat = $3
      ORDER BY c.created_at ASC
    `, [req.params.id, req.params.vendorId, jenis]);

    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// POST /api/tenders/:id/general-chat/:vendorId
router.post('/:id/general-chat/:vendorId', async (req, res) => {
  try {
    const { user_id, message, jenis_chat, file_path } = req.body;
    if (!user_id || !message) {
      return res.status(400).json({ success: false, message: 'user_id dan message diperlukan.' });
    }
    const result = await pool.query(`
      INSERT INTO tender_general_chats (tender_id, vendor_id, user_id, jenis_chat, message, file_path)
      VALUES ($1, $2, $3, $4, $5, $6)
      RETURNING *
    `, [req.params.id, req.params.vendorId, user_id, jenis_chat || 'umum', message, file_path || null]);

    res.status(201).json({ success: true, data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// GET /api/tenders/:id/general-chat-unread/:userId — badge notifikasi (jumlah pesan belum dibaca lintas vendor)
router.get('/:id/general-chat-unread/:userId', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT vendor_id, jenis_chat, COUNT(*) AS unread
      FROM tender_general_chats
      WHERE tender_id = $1 AND is_read = false AND user_id != $2
      GROUP BY vendor_id, jenis_chat
    `, [req.params.id, req.params.userId]);
    res.json({ success: true, data: result.rows });
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
    if (req.user.role === 'vendor' && String(vendor_id) !== String(req.user.id)) {
      return res.status(403).json({ success: false, message: 'Anda cuma bisa mengirim sanggahan atas nama akun vendor Anda sendiri.' });
    }

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
    const { vendor_id, contract_number, contract_date, contract_value, status, user_id } = req.body;
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

    logActivity({
      tenderId: req.params.id, posisi: existing.rows.length ? 'Perubahan Kontrak' : 'Input Kontrak',
      keterangan: `Nomor kontrak: ${contract_number}`, flow: 'kontrak', userId: user_id, ip: req.ip,
    });

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

// ── PENILAIAN KINERJA PENYEDIA (padanan PAKET_PENILAIAN_REKANAN eProc lama, versi
// disederhanakan: skor langsung per kriteria template, tanpa approval berjenjang) ──

router.get('/:id/contract/penilaian-kinerja', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const result = await pool.query(`
      SELECT pk.*, t.nama AS kriteria_nama, t.kode AS kriteria_kode, t.bobot_persen, t.skor_maksimal, u.full_name AS scored_by_name
      FROM contract_penilaian_kinerja pk
      JOIN penilaian_kinerja_templates t ON pk.template_id = t.id
      LEFT JOIN users u ON pk.scored_by = u.id
      WHERE pk.contract_id = $1
      ORDER BY t.kode ASC, t.nama ASC
    `, [contractId]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/contract/penilaian-kinerja', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });

    const { template_id, skor, catatan, scored_by } = req.body;
    if (!template_id || skor == null) return res.status(400).json({ success: false, message: 'template_id dan skor wajib diisi.' });

    const result = await pool.query(`
      INSERT INTO contract_penilaian_kinerja (contract_id, template_id, skor, catatan, scored_by)
      VALUES ($1, $2, $3, $4, $5)
      ON CONFLICT (contract_id, template_id) DO UPDATE SET
        skor = EXCLUDED.skor, catatan = EXCLUDED.catatan, scored_by = EXCLUDED.scored_by
      RETURNING *
    `, [contractId, template_id, skor, catatan || null, scored_by || null]);

    res.json({ success: true, message: 'Skor penilaian berhasil disimpan.', data: result.rows[0] });
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

// ══════════════════════════════════════════════════════════════════════════
// KELOMPOK C - Contracting/Kontrak Detail
// (SPPBJ, SPMK, SPK/PKS, SPPJB, jaminan, SLA, material/surat pesanan, addendum,
// catatan, pengingat, dokumen tambahan, perubahan status, PIC, penilaian)
// ══════════════════════════════════════════════════════════════════════════

// ── SPPBJ (Surat Penunjukan Penyedia Barang/Jasa) & SPK/PKS - field di tabel contracts ──
router.patch('/:id/contract/sppbj', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const b = req.body;
    const result = await pool.query(`
      UPDATE contracts SET
        sppbj_code = $1, sppbj_date = $2, sppbj_nilai = $3, sppbj_direktur_nama = $4, sppbj_direktur_jabatan = $5,
        sppbj_direktur_alamat = $6, sppbj_direktur_kota = $7, sppbj_pejabat_berwenang = $8, sppbj_pejabat_nip = $9,
        sppbj_pejabat_jabatan = $10, sppbj_pelaksanaan_dari = $11, sppbj_pelaksanaan_sampai = $12, sppbj_ppn = $13,
        sppbj_jaminan_pelaksana = $14, sppbj_jaminan_persen = $15, sppbj_jaminan_nilai = $16,
        sppbj_jaminan_jangka_dari = $17, sppbj_jaminan_jangka_sampai = $18, sppbj_jaminan_maksimal_penyerahan = $19,
        is_non_sppbj = $20, updated_at = CURRENT_TIMESTAMP
      WHERE id = $21 RETURNING *
    `, [b.sppbj_code || null, b.sppbj_date || null, b.sppbj_nilai || null, b.sppbj_direktur_nama || null, b.sppbj_direktur_jabatan || null,
        b.sppbj_direktur_alamat || null, b.sppbj_direktur_kota || null, b.sppbj_pejabat_berwenang || null, b.sppbj_pejabat_nip || null,
        b.sppbj_pejabat_jabatan || null, b.sppbj_pelaksanaan_dari || null, b.sppbj_pelaksanaan_sampai || null, b.sppbj_ppn || null,
        b.sppbj_jaminan_pelaksana || null, b.sppbj_jaminan_persen || null, b.sppbj_jaminan_nilai || null,
        b.sppbj_jaminan_jangka_dari || null, b.sppbj_jaminan_jangka_sampai || null, b.sppbj_jaminan_maksimal_penyerahan || null,
        !!b.is_non_sppbj, contractId]);
    res.json({ success: true, message: 'SPPBJ berhasil disimpan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.patch('/:id/contract/spk-detail', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const b = req.body;
    const result = await pool.query(`
      UPDATE contracts SET
        spk_code = $1, metode_pembayaran = $2, jenis_pengadaan = $3, jenis_pekerjaan = $4, jenis_kontrak = $5,
        waktu_pelaksanaan_dari = $6, waktu_pelaksanaan_sampai = $7, pihak1_nama = $8, pihak1_jabatan = $9,
        pihak2_nama = $10, pihak2_jabatan = $11, lingkup_pekerjaan = $12, legal_nomor_pks = $13, legal_tanggal = $14,
        legal_nomor_rekanan = $15, purchase_order_number = $16, penyelesaian_kontrak_awal = $17,
        penyelesaian_kontrak_akhir = $18, masa_garansi = $19, masa_garansi_periode = $20, nama_kegiatan = $21,
        dokumen_jenis = $22, updated_at = CURRENT_TIMESTAMP
      WHERE id = $23 RETURNING *
    `, [b.spk_code || null, b.metode_pembayaran || null, b.jenis_pengadaan || null, b.jenis_pekerjaan || null, b.jenis_kontrak || null,
        b.waktu_pelaksanaan_dari || null, b.waktu_pelaksanaan_sampai || null, b.pihak1_nama || null, b.pihak1_jabatan || null,
        b.pihak2_nama || null, b.pihak2_jabatan || null, b.lingkup_pekerjaan || null, b.legal_nomor_pks || null, b.legal_tanggal || null,
        b.legal_nomor_rekanan || null, b.purchase_order_number || null, b.penyelesaian_kontrak_awal || null,
        b.penyelesaian_kontrak_akhir || null, b.masa_garansi || null, b.masa_garansi_periode || null, b.nama_kegiatan || null,
        b.dokumen_jenis || 'spk', contractId]);
    res.json({ success: true, message: 'Detail SPK/PKS berhasil disimpan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// field-ke-role: siapa yang boleh mengisi approval field tertentu (admin selalu boleh, sebagai
// jaring pengaman kalau alur normal terhambat)
const CONTRACT_APPROVAL_ROLES = { approve_manager: ['admin'], approve_ppk: ['ppk', 'admin'] };
router.patch('/:id/contract/approval', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const { field, value } = req.body;
    const ALLOWED = ['approve_manager', 'approve_ppk'];
    if (!ALLOWED.includes(field)) return res.status(400).json({ success: false, message: `field harus salah satu dari: ${ALLOWED.join(', ')}` });
    if (!CONTRACT_APPROVAL_ROLES[field].includes(req.user.role)) {
      return res.status(403).json({ success: false, message: 'Anda tidak memiliki akses untuk mengisi persetujuan ini.' });
    }
    const result = await pool.query(`UPDATE contracts SET ${field} = $1, updated_at = CURRENT_TIMESTAMP WHERE id = $2 RETURNING *`, [!!value, contractId]);
    res.json({ success: true, message: 'Persetujuan berhasil disimpan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.patch('/:id/contract/pemeriksa', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const { pemeriksa_nama, pemeriksa_jabatan } = req.body;
    const result = await pool.query(`
      UPDATE contracts SET pemeriksa_nama = $1, pemeriksa_jabatan = $2, pemeriksa_approval = true, updated_at = CURRENT_TIMESTAMP
      WHERE id = $3 RETURNING *
    `, [pemeriksa_nama || null, pemeriksa_jabatan || null, contractId]);
    res.json({ success: true, message: 'Approval pemeriksa berhasil disimpan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── PIC per tahap & tahap (stage) kontrak ──
router.patch('/:id/contract/pic', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const { tahap, user_id, pengawas_unit_kerja } = req.body;
    const COLUMN_MAP = { persiapan: 'pic_persiapan_id', pengendali: 'pic_pengendali_id', penyelesai: 'pic_penyelesai_id' };
    const col = COLUMN_MAP[tahap];
    if (!col) return res.status(400).json({ success: false, message: `tahap harus salah satu dari: ${Object.keys(COLUMN_MAP).join(', ')}` });
    const result = await pool.query(`
      UPDATE contracts SET ${col} = $1, pengawas_unit_kerja = COALESCE($2, pengawas_unit_kerja), updated_at = CURRENT_TIMESTAMP
      WHERE id = $3 RETURNING *
    `, [user_id || null, pengawas_unit_kerja || null, contractId]);
    res.json({ success: true, message: 'PIC berhasil ditunjuk.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.patch('/:id/contract/stage', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const { stage } = req.body;
    if (!['persiapan', 'pengendalian', 'penyelesaian', 'selesai'].includes(stage)) {
      return res.status(400).json({ success: false, message: 'stage tidak valid.' });
    }
    // Ikut samakan contracts.status begitu tahap mencapai 'selesai' (ditemukan 2026-09-03:
    // sebelumnya kolom stage dan status berjalan sendiri-sendiri - stage bisa 'selesai' tapi
    // status tetap 'aktif' selamanya karena tidak ada kode manapun yang pernah menuliskan
    // status='selesai'. Akibatnya kartu "Kontrak Selesai" di dashboard SELALU 0 walau kontrak
    // sungguhan sudah tuntas). Kalau tahap mundur lagi dari 'selesai' ke tahap sebelumnya,
    // status ikut balik 'aktif' - tapi status lain (mis. 'draft') sengaja tidak disentuh.
    const result = await pool.query(`
      UPDATE contracts SET stage = $1::varchar, status = CASE
        WHEN $1::varchar = 'selesai' THEN 'selesai'
        WHEN status = 'selesai' THEN 'aktif'
        ELSE status
      END, updated_at = CURRENT_TIMESTAMP
      WHERE id = $2 RETURNING *
    `, [stage, contractId]);
    res.json({ success: true, message: 'Tahap kontrak berhasil diperbarui.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── BAST (Berita Acara Serah Terima): Hasil Pekerjaan & Masa Pemeliharaan ──
router.patch('/:id/contract/bast-hasil', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const b = req.body;
    const result = await pool.query(`
      UPDATE contracts SET
        bast_pekerjaan_nomor = $1, bast_pekerjaan_tanggal = $2, bast_pekerjaan_nama_penyedia = $3,
        bast_pekerjaan_jabatan_penyedia = $4, bast_pekerjaan_nama_penerima = $5, bast_pekerjaan_jabatan_penerima = $6,
        bast_pekerjaan_status = $7, updated_at = CURRENT_TIMESTAMP
      WHERE id = $8 RETURNING *
    `, [b.nomor || null, b.tanggal || null, b.nama_penyedia || null, b.jabatan_penyedia || null,
        b.nama_penerima || null, b.jabatan_penerima || null, b.status || null, contractId]);
    res.json({ success: true, message: 'BAST Hasil Pekerjaan berhasil disimpan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.patch('/:id/contract/bast-masa', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const b = req.body;
    const result = await pool.query(`
      UPDATE contracts SET
        bast_masa_nomor = $1, bast_masa_tanggal = $2, bast_masa_nama_penyedia = $3,
        bast_masa_jabatan_penyedia = $4, bast_masa_nama_penerima = $5, bast_masa_jabatan_penerima = $6,
        bast_masa_status = $7, updated_at = CURRENT_TIMESTAMP
      WHERE id = $8 RETURNING *
    `, [b.nomor || null, b.tanggal || null, b.nama_penyedia || null, b.jabatan_penyedia || null,
        b.nama_penerima || null, b.jabatan_penerima || null, b.status || null, contractId]);
    res.json({ success: true, message: 'BAST Masa Pemeliharaan berhasil disimpan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── Penilaian kinerja penyedia (3 tahap approval independen) ──
router.patch('/:id/contract/penilaian', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const { grade, total_skor } = req.body;
    const result = await pool.query(`
      UPDATE contracts SET penilaian_grade = COALESCE($1, penilaian_grade), penilaian_total_skor = COALESCE($2, penilaian_total_skor), updated_at = CURRENT_TIMESTAMP
      WHERE id = $3 RETURNING *
    `, [grade || null, total_skor || null, contractId]);
    res.json({ success: true, data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// Kasubdit dan Unit belum punya role/login sendiri di sistem baru (baru fondasi role, belum
// dikembangkan UI-nya), jadi field itu untuk sementara cuma bisa diisi admin sebagai penjaga.
const PENILAIAN_APPROVAL_ROLES = {
  penilaian_approval_ppk: ['ppk', 'admin'],
  penilaian_approval_kasubdit: ['admin'],
  penilaian_approval_unit: ['admin'],
};
router.patch('/:id/contract/penilaian/approval', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const { field, value } = req.body;
    const ALLOWED = ['penilaian_approval_ppk', 'penilaian_approval_kasubdit', 'penilaian_approval_unit'];
    if (!ALLOWED.includes(field)) return res.status(400).json({ success: false, message: `field harus salah satu dari: ${ALLOWED.join(', ')}` });
    if (!PENILAIAN_APPROVAL_ROLES[field].includes(req.user.role)) {
      return res.status(403).json({ success: false, message: 'Anda tidak memiliki akses untuk mengisi persetujuan ini.' });
    }
    const result = await pool.query(`UPDATE contracts SET ${field} = $1, updated_at = CURRENT_TIMESTAMP WHERE id = $2 RETURNING *`, [!!value, contractId]);
    res.json({ success: true, message: 'Persetujuan penilaian berhasil disimpan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── SPMK (Surat Perintah Mulai Kerja) ──
router.get('/:id/contract/spmk', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.json({ success: true, data: [] });
    const result = await pool.query('SELECT * FROM contract_spmk WHERE contract_id = $1 ORDER BY created_at DESC', [contractId]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/contract/spmk', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const { nomor, spmk_dari, spmk_sampai, keterangan, created_by } = req.body;
    if (!nomor) return res.status(400).json({ success: false, message: 'nomor diperlukan.' });
    const result = await pool.query(`
      INSERT INTO contract_spmk (contract_id, nomor, spmk_dari, spmk_sampai, keterangan, created_by)
      VALUES ($1, $2, $3, $4, $5, $6) RETURNING *
    `, [contractId, nomor, spmk_dari || null, spmk_sampai || null, keterangan || null, created_by || null]);
    res.status(201).json({ success: true, message: 'SPMK berhasil disimpan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── Jaminan Pelaksanaan ──
router.get('/:id/contract/jaminan', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.json({ success: true, data: [] });
    const result = await pool.query('SELECT * FROM contract_jaminan WHERE contract_id = $1 ORDER BY created_at DESC', [contractId]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/contract/jaminan', upload.fields([{ name: 'file_jaminan' }, { name: 'file_konfirmasi' }]), async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const { nomor, tanggal_jaminan, tanggal_konfirmasi_kebank, tanggal_konfirmasi_oleh_bank, status_konfirmasi, created_by } = req.body;
    const fileJaminan = req.files?.file_jaminan ? `/uploads/${req.files.file_jaminan[0].filename}` : null;
    const fileKonfirmasi = req.files?.file_konfirmasi ? `/uploads/${req.files.file_konfirmasi[0].filename}` : null;
    const result = await pool.query(`
      INSERT INTO contract_jaminan (contract_id, nomor, tanggal_jaminan, file_jaminan, tanggal_konfirmasi_kebank, tanggal_konfirmasi_oleh_bank, status_konfirmasi, file_konfirmasi, created_by)
      VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9) RETURNING *
    `, [contractId, nomor || null, tanggal_jaminan || null, fileJaminan, tanggal_konfirmasi_kebank || null, tanggal_konfirmasi_oleh_bank || null, status_konfirmasi || null, fileKonfirmasi, created_by || null]);
    res.status(201).json({ success: true, message: 'Jaminan pelaksanaan berhasil disimpan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.patch('/:id/contract/jaminan/:jaminanId/konfirmasi', async (req, res) => {
  try {
    const { status_konfirmasi } = req.body;
    const result = await pool.query(`
      UPDATE contract_jaminan SET status_konfirmasi = $1, tanggal_konfirmasi_oleh_bank = CURRENT_DATE WHERE id = $2 RETURNING *
    `, [status_konfirmasi, req.params.jaminanId]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Data jaminan tidak ditemukan.' });
    res.json({ success: true, data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── Jaminan Pemeliharaan (garansi purna kontrak) ──
router.get('/:id/contract/jaminan-pemeliharaan', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.json({ success: true, data: [] });
    const result = await pool.query('SELECT * FROM contract_jaminan_pemeliharaan WHERE contract_id = $1 ORDER BY created_at DESC', [contractId]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/contract/jaminan-pemeliharaan', upload.single('file_jaminan'), async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const { nomor, nilai, masa, tanggal_mulai, tanggal_akhir, created_by } = req.body;
    const filePath = req.file ? `/uploads/${req.file.filename}` : null;
    const result = await pool.query(`
      INSERT INTO contract_jaminan_pemeliharaan (contract_id, nomor, nilai, masa, tanggal_mulai, tanggal_akhir, file_jaminan, created_by)
      VALUES ($1, $2, $3, $4, $5, $6, $7, $8) RETURNING *
    `, [contractId, nomor || null, nilai || null, masa || null, tanggal_mulai || null, tanggal_akhir || null, filePath, created_by || null]);
    res.status(201).json({ success: true, message: 'Jaminan pemeliharaan berhasil disimpan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── SLA (Service Level Agreement) ──
router.get('/:id/contract/sla', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.json({ success: true, data: [] });
    const result = await pool.query('SELECT * FROM contract_sla WHERE contract_id = $1 ORDER BY created_at ASC', [contractId]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/contract/sla', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const { availability, waktu, denda, biaya_maintenance, nilai_denda, created_by } = req.body;
    const result = await pool.query(`
      INSERT INTO contract_sla (contract_id, availability, waktu, denda, biaya_maintenance, nilai_denda, created_by)
      VALUES ($1, $2, $3, $4, $5, $6, $7) RETURNING *
    `, [contractId, availability || null, waktu || null, denda || null, biaya_maintenance || null, nilai_denda || null, created_by || null]);
    res.status(201).json({ success: true, message: 'SLA berhasil disimpan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/:id/contract/sla/:slaId', async (req, res) => {
  try {
    await pool.query('DELETE FROM contract_sla WHERE id = $1', [req.params.slaId]);
    res.json({ success: true, message: 'SLA berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── Material (khusus Kontrak Payung) ──
router.get('/:id/contract/materials', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.json({ success: true, data: [] });
    const result = await pool.query('SELECT * FROM contract_materials WHERE contract_id = $1 ORDER BY created_at ASC', [contractId]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// Replace-all pattern (mengikuti addMaterial() asli: hapus semua material lama, insert ulang).
router.post('/:id/contract/materials', async (req, res) => {
  const client = await pool.connect();
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const { materials, created_by } = req.body;
    if (!Array.isArray(materials) || !materials.length) {
      return res.status(400).json({ success: false, message: 'Daftar material diperlukan.' });
    }
    await client.query('BEGIN');
    await client.query('DELETE FROM contract_materials WHERE contract_id = $1', [contractId]);
    for (const m of materials) {
      await client.query(`
        INSERT INTO contract_materials (contract_id, nama, qty, satuan, harga_satuan, sifat, keterangan, created_by)
        VALUES ($1, $2, $3, $4, $5, $6, $7, $8)
      `, [contractId, m.nama, m.qty || null, m.satuan || null, m.harga_satuan || null, m.sifat || null, m.keterangan || null, created_by || null]);
    }
    await client.query('COMMIT');
    res.json({ success: true, message: 'Daftar material berhasil disimpan.' });
  } catch (err) {
    await client.query('ROLLBACK');
    res.status(500).json({ success: false, message: err.message });
  } finally {
    client.release();
  }
});

router.delete('/:id/contract/materials/:materialId', async (req, res) => {
  try {
    await pool.query('DELETE FROM contract_materials WHERE id = $1', [req.params.materialId]);
    res.json({ success: true, message: 'Material berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── Surat Pesanan (dokumen turunan Kontrak Payung) ──
router.get('/:id/contract/surat-pesanan', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.json({ success: true, data: [] });
    const headers = await pool.query('SELECT * FROM contract_surat_pesanan WHERE contract_id = $1 ORDER BY created_at DESC', [contractId]);
    const withItems = await Promise.all(headers.rows.map(async h => {
      const items = await pool.query('SELECT * FROM contract_surat_pesanan_items WHERE surat_pesanan_id = $1 ORDER BY created_at ASC', [h.id]);
      return { ...h, items: items.rows };
    }));
    res.json({ success: true, data: withItems });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/contract/surat-pesanan', async (req, res) => {
  const client = await pool.connect();
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const { nomor_surat, tanggal, items, created_by } = req.body;
    if (!nomor_surat || !Array.isArray(items) || !items.length) {
      return res.status(400).json({ success: false, message: 'nomor_surat dan minimal satu item diperlukan.' });
    }
    await client.query('BEGIN');
    const header = await client.query(`
      INSERT INTO contract_surat_pesanan (contract_id, nomor_surat, tanggal, created_by) VALUES ($1, $2, $3, $4) RETURNING *
    `, [contractId, nomor_surat, tanggal || null, created_by || null]);
    for (const it of items) {
      const qty = Number(it.qty) || 0;
      const hargaSatuan = Number(it.harga_satuan) || 0;
      await client.query(`
        INSERT INTO contract_surat_pesanan_items (surat_pesanan_id, material_id, nama, harga_satuan, qty, satuan, sifat, total, keterangan, created_by)
        VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)
      `, [header.rows[0].id, it.material_id || null, it.nama, hargaSatuan, qty, it.satuan || null, it.sifat || null, qty * hargaSatuan, it.keterangan || null, created_by || null]);
    }
    await client.query('COMMIT');
    res.status(201).json({ success: true, message: 'Surat pesanan berhasil dibuat.', data: header.rows[0] });
  } catch (err) {
    await client.query('ROLLBACK');
    res.status(500).json({ success: false, message: err.message });
  } finally {
    client.release();
  }
});

router.patch('/:id/contract/surat-pesanan/items/:itemId', async (req, res) => {
  try {
    const { status_terima, status_keterangan, tanggal_terima, presentase } = req.body;
    const result = await pool.query(`
      UPDATE contract_surat_pesanan_items SET
        status_terima = COALESCE($1, status_terima), status_keterangan = COALESCE($2, status_keterangan),
        tanggal_terima = COALESCE($3, tanggal_terima), presentase = COALESCE($4, presentase)
      WHERE id = $5 RETURNING *
    `, [status_terima || null, status_keterangan || null, tanggal_terima || null, presentase || null, req.params.itemId]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Item surat pesanan tidak ditemukan.' });
    res.json({ success: true, message: 'Status penerimaan berhasil diperbarui.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/:id/contract/surat-pesanan/:suratPesananId', async (req, res) => {
  const client = await pool.connect();
  try {
    await client.query('BEGIN');
    await client.query('DELETE FROM contract_surat_pesanan_items WHERE surat_pesanan_id = $1', [req.params.suratPesananId]);
    await client.query('DELETE FROM contract_surat_pesanan WHERE id = $1', [req.params.suratPesananId]);
    await client.query('COMMIT');
    res.json({ success: true, message: 'Surat pesanan berhasil dihapus.' });
  } catch (err) {
    await client.query('ROLLBACK');
    res.status(500).json({ success: false, message: err.message });
  } finally {
    client.release();
  }
});

// ── Addendum (2 tahap approval: Kasubdit dan Penyedia) ──
router.get('/:id/contract/addendum', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.json({ success: true, data: [] });
    const result = await pool.query('SELECT * FROM contract_addendum WHERE contract_id = $1 ORDER BY created_at DESC', [contractId]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/contract/addendum', upload.fields([{ name: 'file_persetujuan' }, { name: 'file_addendum' }]), async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const b = req.body;
    const filePersetujuan = req.files?.file_persetujuan ? `/uploads/${req.files.file_persetujuan[0].filename}` : null;
    const fileAddendum = req.files?.file_addendum ? `/uploads/${req.files.file_addendum[0].filename}` : null;
    const result = await pool.query(`
      INSERT INTO contract_addendum
        (contract_id, nomor, addendum_ke, jenis, tanggal, tanggal_kontrak_dari, tanggal_kontrak_sampai,
         tanggal_penyelesaian_awal, tanggal_penyelesaian_akhir, file_persetujuan, file_addendum, keterangan, nilai_baru, created_by)
      VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14) RETURNING *
    `, [contractId, b.nomor || null, b.addendum_ke || null, b.jenis || null, b.tanggal || null, b.tanggal_kontrak_dari || null,
        b.tanggal_kontrak_sampai || null, b.tanggal_penyelesaian_awal || null, b.tanggal_penyelesaian_akhir || null,
        filePersetujuan, fileAddendum, b.keterangan || null, b.nilai_baru || null, b.created_by || null]);
    res.status(201).json({ success: true, message: 'Addendum berhasil diajukan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// Kasubdit belum punya role/login sendiri (baru fondasi role), sementara diisi admin sebagai
// penjaga. Penyedia (vendor) mengisi persetujuannya sendiri.
const ADDENDUM_APPROVAL_ROLES = { approved_kasubdit: ['admin'], approved_penyedia: ['vendor', 'admin'] };
router.patch('/:id/contract/addendum/:addendumId/approval', async (req, res) => {
  try {
    const { field, value } = req.body;
    const ALLOWED = ['approved_kasubdit', 'approved_penyedia'];
    if (!ALLOWED.includes(field)) return res.status(400).json({ success: false, message: `field harus salah satu dari: ${ALLOWED.join(', ')}` });
    if (!ADDENDUM_APPROVAL_ROLES[field].includes(req.user.role)) {
      return res.status(403).json({ success: false, message: 'Anda tidak memiliki akses untuk mengisi persetujuan ini.' });
    }
    const result = await pool.query(`UPDATE contract_addendum SET ${field} = $1 WHERE id = $2 RETURNING *`, [!!value, req.params.addendumId]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Addendum tidak ditemukan.' });

    const row = result.rows[0];
    if (row.approved_kasubdit && row.approved_penyedia && row.status !== 'selesai') {
      await pool.query(`UPDATE contract_addendum SET status = 'selesai' WHERE id = $1`, [row.id]);
      row.status = 'selesai';
    }
    res.json({ success: true, message: 'Persetujuan addendum berhasil disimpan.', data: row });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/:id/contract/addendum/:addendumId', async (req, res) => {
  try {
    await pool.query('DELETE FROM contract_addendum WHERE id = $1', [req.params.addendumId]);
    res.json({ success: true, message: 'Addendum berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── Catatan (internal / versi penyedia) ──
router.get('/:id/contract/notes', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.json({ success: true, data: [] });
    const { jenis } = req.query;
    const params = [contractId];
    let sql = 'SELECT n.*, u.full_name AS created_by_name FROM contract_notes n LEFT JOIN users u ON n.created_by = u.id WHERE n.contract_id = $1';
    if (jenis) { sql += ' AND n.jenis = $2'; params.push(jenis); }
    sql += ' ORDER BY n.created_at DESC';
    const result = await pool.query(sql, params);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/contract/notes', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const { jenis, pesan, created_by } = req.body;
    if (!pesan || !pesan.trim()) return res.status(400).json({ success: false, message: 'Catatan tidak boleh kosong.' });
    const result = await pool.query(`
      INSERT INTO contract_notes (contract_id, jenis, pesan, created_by) VALUES ($1, $2, $3, $4) RETURNING *
    `, [contractId, jenis || 'internal', pesan, created_by || null]);
    res.status(201).json({ success: true, data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── Notifikasi/Pengingat kontrak ──
router.get('/:id/contract/reminders', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.json({ success: true, data: [] });
    const result = await pool.query('SELECT * FROM contract_reminders WHERE contract_id = $1 ORDER BY tanggal_dari DESC', [contractId]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/contract/reminders', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const { judul, tanggal_dari, tanggal_sampai, created_by } = req.body;
    if (!judul) return res.status(400).json({ success: false, message: 'judul diperlukan.' });
    const result = await pool.query(`
      INSERT INTO contract_reminders (contract_id, judul, tanggal_dari, tanggal_sampai, created_by)
      VALUES ($1, $2, $3, $4, $5) RETURNING *
    `, [contractId, judul, tanggal_dari || null, tanggal_sampai || null, created_by || null]);
    res.status(201).json({ success: true, message: 'Pengingat berhasil disimpan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/:id/contract/reminders/:reminderId', async (req, res) => {
  try {
    await pool.query('DELETE FROM contract_reminders WHERE id = $1', [req.params.reminderId]);
    res.json({ success: true, message: 'Pengingat berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── Dokumen tambahan (selain SPK/BAST utama) ──
router.get('/:id/contract/documents', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.json({ success: true, data: [] });
    const result = await pool.query('SELECT * FROM contract_documents WHERE contract_id = $1 ORDER BY created_at DESC', [contractId]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/contract/documents', upload.single('file'), async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    if (!req.file) return res.status(400).json({ success: false, message: 'File diperlukan.' });
    const { nama, jenis, keterangan, created_by } = req.body;
    const result = await pool.query(`
      INSERT INTO contract_documents (contract_id, nama, file_path, file_size, jenis, keterangan, created_by)
      VALUES ($1, $2, $3, $4, $5, $6, $7) RETURNING *
    `, [contractId, nama || req.file.originalname, `/uploads/${req.file.filename}`, req.file.size, jenis || null, keterangan || null, created_by || null]);
    res.status(201).json({ success: true, message: 'Dokumen berhasil diunggah.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.patch('/:id/contract/documents/:docId/publish', async (req, res) => {
  try {
    const { publish } = req.body;
    const result = await pool.query(`UPDATE contract_documents SET publish_ke_penyedia = $1 WHERE id = $2 RETURNING *`, [!!publish, req.params.docId]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Dokumen tidak ditemukan.' });
    res.json({ success: true, message: publish ? 'Dokumen berhasil dipublish ke penyedia.' : 'Dokumen berhasil di-unpublish.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/:id/contract/documents/:docId', async (req, res) => {
  try {
    await pool.query('DELETE FROM contract_documents WHERE id = $1', [req.params.docId]);
    res.json({ success: true, message: 'Dokumen berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── Perubahan Status Kontrak (Perubahan/Penyesuaian/Kahar/Berakhir/Pemutusan/Kesempatan/Denda) ──
const STATUS_CHANGE_TYPES = ['perubahan', 'penyesuaian', 'kahar', 'berakhir', 'pemutusan', 'kesempatan', 'denda'];

router.get('/:id/contract/status-changes', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.json({ success: true, data: [] });
    const result = await pool.query('SELECT * FROM contract_status_changes WHERE contract_id = $1 ORDER BY created_at DESC', [contractId]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/contract/status-changes', upload.single('file'), async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const { jenis, alasan, created_by } = req.body;
    if (!STATUS_CHANGE_TYPES.includes(jenis)) {
      return res.status(400).json({ success: false, message: `jenis harus salah satu dari: ${STATUS_CHANGE_TYPES.join(', ')}` });
    }
    const filePath = req.file ? `/uploads/${req.file.filename}` : null;
    const result = await pool.query(`
      INSERT INTO contract_status_changes (contract_id, jenis, alasan, file_path, created_by)
      VALUES ($1, $2, $3, $4, $5) RETURNING *
    `, [contractId, jenis, alasan || null, filePath, created_by || null]);
    res.status(201).json({ success: true, message: 'Perubahan status kontrak berhasil dicatat.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── SPPJB (Surat Perjanjian, varian dokumen kontrak) ──
router.get('/:id/contract/sppjb', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.json({ success: true, data: null });
    const result = await pool.query('SELECT * FROM contract_sppjb WHERE contract_id = $1 ORDER BY created_at DESC LIMIT 1', [contractId]);
    res.json({ success: true, data: result.rows[0] || null });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/contract/sppjb', async (req, res) => {
  try {
    const contractId = await getContractId(req.params.id);
    if (!contractId) return res.status(404).json({ success: false, message: 'Kontrak belum dibuat untuk tender ini.' });
    const b = req.body;
    const result = await pool.query(`
      INSERT INTO contract_sppjb
        (contract_id, kode, tanggal, nama_dirut, alamat_dirut, kota_dirut, ppn, persen_jaminan, tmt_jaminan,
         jangka_waktu, jangka_waktu_jaminan, penanda_tangan, penanda_tangan_jabatan, created_by)
      VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14) RETURNING *
    `, [contractId, b.kode || null, b.tanggal || null, b.nama_dirut || null, b.alamat_dirut || null, b.kota_dirut || null,
        b.ppn || null, b.persen_jaminan || null, b.tmt_jaminan || null, b.jangka_waktu || null, b.jangka_waktu_jaminan || null,
        b.penanda_tangan || null, b.penanda_tangan_jabatan || null, b.created_by || null]);
    res.status(201).json({ success: true, message: 'SPPJB berhasil disimpan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── RUMUS EVALUASI RESMI (Personil, Peralatan, Sertifikat) ──────────────────
// Meniru persis fungsi hitungPersonil()/hitungPeralatan()/hitungSertifikat() di
// eproc/lib/eproc/allfunc.js (kode yang benar-benar dipakai sistem produksi lama).
// Logikanya ada di server/lib/evalFormula.js supaya bisa dipakai bersama dengan
// endpoint cetak rekapitulasi evaluasi kualifikasi (server/routes/print.js).
const { FORMULA_CATEGORIES, round2, calcCriteriaRatio } = require('../lib/evalFormula');

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

    let mailResult = { sent: false, reason: 'no_email' };
    try {
      const info = await pool.query(
        `SELECT t.title, u.full_name AS vendor_name, u.email AS vendor_email
         FROM tenders t, users u WHERE t.id = $1 AND u.id = $2`,
        [req.params.id, vendor_id]
      );
      if (info.rows.length && info.rows[0].vendor_email) {
        const { title, vendor_name, vendor_email } = info.rows[0];
        const tglStr = new Date(tanggal_undangan).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        mailResult = await sendMail({
          to: vendor_email,
          subject: `Undangan Klarifikasi: ${title}`,
          html: `
            <p>Yth. ${vendor_name},</p>
            <p>Anda diundang untuk hadir dalam klarifikasi terkait paket pengadaan <strong>${title}</strong>.</p>
            <ul>
              <li>Tanggal: ${tglStr}${jam ? ' pukul ' + jam : ''}</li>
              ${tempat ? `<li>Tempat: ${tempat}</li>` : ''}
              ${pelaksanaan ? `<li>Pelaksanaan: ${pelaksanaan}</li>` : ''}
              ${peserta ? `<li>Peserta yang diharapkan hadir: ${peserta}</li>` : ''}
            </ul>
            ${keterangan ? `<p>Keterangan: ${keterangan}</p>` : ''}
            <p>Mohon kehadirannya tepat waktu. Terima kasih.<br/>Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia</p>
          `,
        });
      }
    } catch (mailErr) {
      console.error('[UNDANGAN KLARIFIKASI MAIL]', mailErr);
    }

    const message = mailResult.sent
      ? 'Undangan klarifikasi berhasil disimpan dan email terkirim ke vendor.'
      : 'Undangan klarifikasi berhasil disimpan' + (mailResult.reason === 'smtp_not_configured' ? ' (email tidak terkirim, SMTP belum dikonfigurasi).' : mailResult.reason === 'no_email' ? ' (vendor tidak punya email terdaftar, email tidak dikirim).' : '.');

    res.json({ success: true, message, email_sent: mailResult.sent, data: result.rows[0] });
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

// ══════════════════════════════════════════════════════════════════════════
// KELOMPOK B - Vendor/Rekanan Detail (bagian yang bersinggungan dengan tender)
// ══════════════════════════════════════════════════════════════════════════

// ── BIDANG USAHA YANG DISYARATKAN TENDER ──
router.get('/:id/bidang-usaha', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT tb.id, tb.bidang_usaha_id, b.kode, b.nama
      FROM tender_bidang_usaha tb
      JOIN bidang_usaha b ON tb.bidang_usaha_id = b.id
      WHERE tb.tender_id = $1
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
      INSERT INTO tender_bidang_usaha (tender_id, bidang_usaha_id) VALUES ($1, $2)
      ON CONFLICT (tender_id, bidang_usaha_id) DO NOTHING RETURNING *
    `, [req.params.id, bidang_usaha_id]);
    res.status(201).json({ success: true, data: result.rows[0] || null });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/:id/bidang-usaha/:linkId', async (req, res) => {
  try {
    await pool.query('DELETE FROM tender_bidang_usaha WHERE id = $1 AND tender_id = $2', [req.params.linkId, req.params.id]);
    res.json({ success: true, message: 'Syarat bidang usaha berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── RINCIAN PENAWARAN / BOQ (item per baris pada penawaran vendor) ──
router.get('/:id/participants/:vendorId/bid-items', async (req, res) => {
  try {
    const participant = await pool.query(
      'SELECT id FROM tender_participants WHERE tender_id = $1 AND vendor_id = $2',
      [req.params.id, req.params.vendorId]
    );
    if (!participant.rows.length) return res.json({ success: true, data: [] });
    const result = await pool.query(
      'SELECT * FROM tender_bid_items WHERE tender_participant_id = $1 ORDER BY created_at ASC',
      [participant.rows[0].id]
    );
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// Vendor kirim rincian item penawaran sekaligus (replace semua item lama), lalu bid_price
// di tender_participants otomatis dihitung ulang dari jumlah semua item supaya tetap konsisten.
router.post('/:id/participants/:vendorId/bid-items', async (req, res) => {
  const client = await pool.connect();
  try {
    const { items } = req.body;
    if (!Array.isArray(items) || items.length === 0) {
      return res.status(400).json({ success: false, message: 'Daftar item diperlukan.' });
    }
    const participant = await client.query(
      'SELECT id FROM tender_participants WHERE tender_id = $1 AND vendor_id = $2',
      [req.params.id, req.params.vendorId]
    );
    if (!participant.rows.length) {
      return res.status(404).json({ success: false, message: 'Vendor belum terdaftar di tender ini.' });
    }
    const participantId = participant.rows[0].id;

    await client.query('BEGIN');
    await client.query('DELETE FROM tender_bid_items WHERE tender_participant_id = $1', [participantId]);

    let total = 0;
    for (const item of items) {
      const quantity = Number(item.quantity) || 0;
      const unitPrice = Number(item.unit_price) || 0;
      const subtotal = quantity * unitPrice;
      total += subtotal;
      await client.query(`
        INSERT INTO tender_bid_items (tender_participant_id, item_name, quantity, unit_price, subtotal, delivery_date, notes)
        VALUES ($1, $2, $3, $4, $5, $6, $7)
      `, [participantId, item.item_name, quantity, unitPrice, subtotal, item.delivery_date || null, item.notes || null]);
    }

    await client.query(
      `UPDATE tender_participants SET bid_price = $1, status = 'bidded' WHERE id = $2`,
      [total, participantId]
    );

    await client.query('COMMIT');
    res.json({ success: true, message: 'Rincian penawaran berhasil disimpan.', data: { total } });
  } catch (err) {
    await client.query('ROLLBACK');
    res.status(500).json({ success: false, message: err.message });
  } finally {
    client.release();
  }
});

module.exports = router;
