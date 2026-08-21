const express = require('express');
const router  = express.Router();
const { pool } = require('../db');
const multer  = require('multer');
const path    = require('path');
const fs      = require('fs');
const { logActivity } = require('../lib/activityLog');

// ── Konfigurasi Multer ──
const storage = multer.diskStorage({
  destination: function (req, file, cb) {
    const dir = path.join(__dirname, '..', 'uploads');
    if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
    cb(null, dir);
  },
  filename: function (req, file, cb) {
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
    cb(null, 'pengajuan-' + file.fieldname + '-' + uniqueSuffix + path.extname(file.originalname));
  }
});
const upload = multer({ storage: storage });

// ── POST /api/pengajuan/sap-sync (Simulasi SAP ERP) ──
router.post('/sap-sync', async (req, res) => {
  const client = await pool.connect();
  try {
    const { requester_id } = req.body;
    
    // Generate mock SAP PR
    const sapPrNumber = 'SAP-PR-' + Math.floor(Math.random() * 1000000);
    const estimatedValue = Math.floor(Math.random() * 1000) * 1000000;
    
    await client.query('BEGIN');
    
    const year = new Date().getFullYear();
    const countResult = await client.query('SELECT COUNT(*) AS cnt FROM procurement_requests WHERE fiscal_year = $1', [year]);
    const cnt = parseInt(countResult.rows[0].cnt);
    const nextNum = (cnt + 1).toString().padStart(3, '0');
    const request_number = `REQ-${year}-${nextNum}`;
    
    const result = await client.query(`
      INSERT INTO procurement_requests (
        request_number, title, unit_kerja, category, estimated_value, status, fiscal_year, is_from_sap, sap_pr_number, requester_id
      ) VALUES (
        $1, $2, $3, $4, $5, 'proses_review', $6, true, $7, $8
      ) RETURNING *
    `, [request_number, `[SAP] Pengadaan Peralatan IT Otomatis ${sapPrNumber}`, 'Direktorat Fasilitas', 'Barang', estimatedValue, year, sapPrNumber, requester_id]);

    await client.query(`INSERT INTO audit_logs (action, entity_type, description, is_success) VALUES ('CREATE', 'Pengajuan', $1, true)`, [`Sinkronisasi otomatis PR dari SAP: ${sapPrNumber}`]);
    
    await client.query('COMMIT');
    res.status(201).json({ success: true, message: 'Data Purchase Request dari SAP berhasil ditarik.', data: result.rows[0] });
  } catch (err) {
    await client.query('ROLLBACK');
    res.status(500).json({ success: false, message: err.message });
  } finally {
    client.release();
  }
});

// ── GET /api/pengajuan ──
router.get('/', async (req, res) => {
  try {
    const { status, unit_kerja, page = 1, limit = 20 } = req.query;
    const offset = (parseInt(page) - 1) * parseInt(limit);
    let sql = `
      SELECT pr.*, u.full_name AS requester_name
      FROM procurement_requests pr
      LEFT JOIN users u ON pr.requester_id = u.id
      WHERE 1=1
    `;
    const params = [];
    let paramIndex = 1;
    if (status)    { sql += ` AND pr.status = $${paramIndex++}`;     params.push(status); }
    if (unit_kerja){ sql += ` AND pr.unit_kerja = $${paramIndex++}`; params.push(unit_kerja); }
    sql += ` ORDER BY pr.created_at DESC LIMIT $${paramIndex++} OFFSET $${paramIndex++}`;
    params.push(parseInt(limit), offset);
    const result = await pool.query(sql, params);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/pengajuan — Buat pengajuan baru (Fase 4 - FormData) ──
router.post('/', upload.fields([{name:'kak', maxCount:1}, {name:'rab', maxCount:1}, {name:'nota', maxCount:1}]), async (req, res) => {
  try {
    const { title, unit_kerja, category, estimated_value,
            budget_source, budget_code, fiscal_year,
            description, technical_spec, quantity, unit_of_measure,
            needed_by_date, requester_id,
            komoditas, analisa_kebutuhan, analisa_pasar,
            risiko_teridentifikasi, risiko_keterangan } = req.body;

    if (!title || !unit_kerja || !estimated_value) {
      return res.status(400).json({ success: false, message: 'title, unit_kerja, estimated_value wajib diisi.' });
    }

    const year = fiscal_year || new Date().getFullYear();
    const countResult = await pool.query('SELECT COUNT(*) AS cnt FROM procurement_requests WHERE fiscal_year = $1', [year]);
    const cnt = parseInt(countResult.rows[0].cnt);
    const request_number = `PENGAJUAN/${year}/${String(cnt + 1).padStart(3, '0')}`;

    const usersResult = await pool.query('SELECT id FROM users LIMIT 1');
    const validRequesterId = usersResult.rows.length > 0 ? usersResult.rows[0].id : null;

    let kakPath = null, rabPath = null, notaPath = null;
    if (req.files) {
      if (req.files.kak) kakPath = '/uploads/' + req.files.kak[0].filename;
      if (req.files.rab) rabPath = '/uploads/' + req.files.rab[0].filename;
      if (req.files.nota) notaPath = '/uploads/' + req.files.nota[0].filename;
    }

    // Langsung set status ke diajukan (melewati draft)
    await pool.query(`
      INSERT INTO procurement_requests
        (id, request_number, title, unit_kerja, category, estimated_value,
         budget_source, budget_code, fiscal_year, description,
         technical_spec, quantity, unit_of_measure, needed_by_date, requester_id,
         status, kak_path, rab_path, nota_dinas_path,
         komoditas, analisa_kebutuhan, analisa_pasar, risiko_teridentifikasi, risiko_keterangan)
      VALUES (gen_random_uuid(), $1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, 'diajukan', $15, $16, $17, $18, $19, $20, $21, $22)
    `, [request_number, title, unit_kerja, category||null, estimated_value,
        budget_source||null, budget_code||null, year, description||null,
        technical_spec||null, quantity||null, unit_of_measure||null,
        needed_by_date||null, validRequesterId, kakPath, rabPath, notaPath,
        komoditas||null, analisa_kebutuhan||null, analisa_pasar||null,
        risiko_teridentifikasi === 'true' || risiko_teridentifikasi === true,
        risiko_keterangan||null]);

    await pool.query(`INSERT INTO audit_logs (action, entity_type, description, is_success) VALUES ('CREATE', 'Pengajuan', $1, true)`, [`Pengajuan baru diajukan: ${title}`]);

    res.status(201).json({ success: true, message: 'Pengajuan berhasil diajukan dengan dokumen pendukung.', request_number });
  } catch (err) {
    console.error('Error creating pengajuan:', err);
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/pengajuan/:id/submit — Legacy support (draft → diajukan) ──
router.post('/:id/submit', async (req, res) => {
  try {
    const { id } = req.params;
    const { user_id } = req.body;
    await pool.query("UPDATE procurement_requests SET status = 'diajukan' WHERE id = $1 AND status = 'draft'", [id]);
    logActivity({ procurementRequestId: id, posisi: 'Pengajuan Disubmit', flow: 'permohonan', userId: user_id, ip: req.ip });
    res.json({ success: true, message: 'Pengajuan disubmit.' });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

// ── POST /api/pengajuan/:id/review — Tahap 1 Admin Review Dokumen ──
router.post('/:id/review', async (req, res) => {
  try {
    const { id } = req.params;
    const { admin_notes, is_docs_complete, user_id } = req.body;

    const result = await pool.query('SELECT * FROM procurement_requests WHERE id = $1', [id]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Not found' });
    const p = result.rows[0];

    if (p.status !== 'diajukan') {
      return res.status(400).json({ success: false, message: 'Pengajuan belum diajukan atau sudah diproses.' });
    }

    if (!is_docs_complete) {
      // Jika dokumen belum lengkap, bisa ditolak atau dikembalikan
      await pool.query("UPDATE procurement_requests SET status = 'ditolak', admin_notes = $1 WHERE id = $2", [admin_notes, id]);
      logActivity({ procurementRequestId: id, posisi: 'Verifikasi Berkas Ditolak', keterangan: admin_notes, flow: 'permohonan', userId: user_id, ip: req.ip });
      return res.json({ success: true, message: 'Pengajuan ditolak karena dokumen tidak lengkap.' });
    }

    await pool.query("UPDATE procurement_requests SET status = 'proses_review', admin_notes = $1, is_docs_complete = $2 WHERE id = $3", [admin_notes, is_docs_complete, id]);

    await pool.query(`INSERT INTO audit_logs (action, entity_type, description, is_success) VALUES ('UPDATE', 'Pengajuan', $1, true)`, [`Admin mereview dokumen pengajuan: ${p.title}`]);
    logActivity({ procurementRequestId: id, posisi: 'Verifikasi Berkas Lengkap', flow: 'permohonan', userId: user_id, ip: req.ip });

    res.json({ success: true, message: 'Dokumen lengkap. Pengajuan masuk ke tahap Review Akhir.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/pengajuan/:id/approve — Tahap 2 Admin ACC pengajuan ──
router.post('/:id/approve', async (req, res) => {
  try {
    const { id } = req.params;
    const { user_id } = req.body;
    const result = await pool.query('SELECT * FROM procurement_requests WHERE id = $1', [id]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Pengajuan tidak ditemukan.' });
    const p = result.rows[0];
    
    // Bisa approve dari draft, diajukan, atau proses_review
    if (['disetujui', 'ditolak', 'dibatalkan'].includes(p.status)) {
      return res.status(400).json({ success: false, message: 'Pengajuan sudah disetujui sebelumnya.' });
    }

    const conn = await pool.connect();
    await conn.query('BEGIN');
    try {
      await conn.query("UPDATE procurement_requests SET status = 'disetujui' WHERE id = $1", [id]);
      const tenderNumber = `TENDER/${p.fiscal_year}/${p.request_number.split('/').pop()}`;
      await conn.query(`
        INSERT INTO tenders 
          (id, procurement_request_id, tender_number, title, category, 
           method, status, pagu_anggaran, submission_deadline)
        VALUES (gen_random_uuid(), $1, $2, $3, $4, 'tender', 'draft', $5, CURRENT_TIMESTAMP + INTERVAL '14 days')
      `, [p.id, tenderNumber, p.title, p.category, p.estimated_value]);
      
      await conn.query(`INSERT INTO audit_logs (action, entity_type, description, is_success) VALUES ('UPDATE', 'Pengajuan', $1, true)`, [`Pengajuan di-ACC menjadi Tender: ${p.title}`]);

      await conn.query('COMMIT');

      logActivity({ procurementRequestId: id, posisi: 'Pengajuan Disetujui (ACC)', keterangan: `Paket tender ${tenderNumber} dibuat otomatis`, flow: 'permohonan', userId: user_id, ip: req.ip });

      res.json({ success: true, message: 'Pengajuan berhasil di-ACC dan Paket Tender Draft telah dibuat.' });
    } catch (e) {
      await conn.query('ROLLBACK');
      throw e;
    } finally {
      conn.release();
    }
  } catch (err) {
    console.error('Error approving pengajuan:', err);
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/pengajuan/:id/reject — Tolak pengajuan ──
router.post('/:id/reject', async (req, res) => {
  try {
    const { id } = req.params;
    const { reason } = req.body;
    await pool.query("UPDATE procurement_requests SET status = 'ditolak', admin_notes = $1 WHERE id = $2", [reason, id]);
    res.json({ success: true, message: 'Pengajuan berhasil ditolak.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ══════════════════════════════════════════════════════════════════════════
// KELOMPOK E - Permohonan Paket/RUP Detail
// (file analisa, approval berjenjang, revisi, checklist kelengkapan)
// ══════════════════════════════════════════════════════════════════════════

// ── MASTER CHECKLIST (ditaruh sebelum /:id supaya tidak ketiban rute pengajuan) ──
router.get('/master/checklist', async (req, res) => {
  try {
    const { paket_jenis, metode_pemilihan } = req.query;
    let sql = 'SELECT * FROM master_checklist WHERE is_active = true';
    const params = [];
    if (paket_jenis) { sql += ` AND (paket_jenis IS NULL OR paket_jenis = $${params.length + 1})`; params.push(paket_jenis); }
    if (metode_pemilihan) { sql += ` AND (metode_pemilihan IS NULL OR metode_pemilihan = $${params.length + 1})`; params.push(metode_pemilihan); }
    sql += ' ORDER BY nama ASC';
    const result = await pool.query(sql, params);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/master/checklist', async (req, res) => {
  try {
    const { nama, paket_jenis, metode_pemilihan, wajib, created_by } = req.body;
    if (!nama) return res.status(400).json({ success: false, message: 'nama wajib diisi.' });
    const result = await pool.query(`
      INSERT INTO master_checklist (nama, paket_jenis, metode_pemilihan, wajib, created_by)
      VALUES ($1, $2, $3, $4, $5) RETURNING *
    `, [nama, paket_jenis || null, metode_pemilihan || null, !!wajib, created_by || null]);
    res.status(201).json({ success: true, message: 'Item checklist berhasil ditambahkan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/master/checklist/:checklistId', async (req, res) => {
  try {
    await pool.query('DELETE FROM master_checklist WHERE id = $1', [req.params.checklistId]);
    res.json({ success: true, message: 'Item checklist berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── FILE ANALISA ──
router.get('/:id/files', async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM procurement_request_files WHERE procurement_request_id = $1 ORDER BY created_at DESC', [req.params.id]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/files', upload.single('file'), async (req, res) => {
  try {
    if (!req.file) return res.status(400).json({ success: false, message: 'File diperlukan.' });
    const { judul, created_by } = req.body;
    const result = await pool.query(`
      INSERT INTO procurement_request_files (procurement_request_id, judul, file_path, file_type, file_size, created_by)
      VALUES ($1, $2, $3, $4, $5, $6) RETURNING *
    `, [req.params.id, judul || req.file.originalname, `/uploads/${req.file.filename}`, req.file.mimetype, req.file.size, created_by || null]);
    res.status(201).json({ success: true, message: 'File analisa berhasil diunggah.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.patch('/:id/files/:fileId/esign', async (req, res) => {
  try {
    const { esign_nomor_surat, esign_status } = req.body;
    const result = await pool.query(`
      UPDATE procurement_request_files SET esign_nomor_surat = $1, esign_status = $2 WHERE id = $3 RETURNING *
    `, [esign_nomor_surat || null, esign_status || null, req.params.fileId]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'File tidak ditemukan.' });
    res.json({ success: true, data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/:id/files/:fileId', async (req, res) => {
  try {
    await pool.query('DELETE FROM procurement_request_files WHERE id = $1 AND procurement_request_id = $2', [req.params.fileId, req.params.id]);
    res.json({ success: true, message: 'File berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── APPROVAL BERJENJANG ──
router.get('/:id/approvals', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT a.*, u.full_name AS approved_by_name
      FROM procurement_request_approvals a
      LEFT JOIN users u ON a.approved_by = u.id
      WHERE a.procurement_request_id = $1
      ORDER BY a.created_at ASC
    `, [req.params.id]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// Satu baris per approver (mengikuti alur asli: cek dulu apakah approver ini sudah pernah approve,
// kalau belum insert baru, kalau sudah update baris yang ada).
router.post('/:id/approvals', async (req, res) => {
  try {
    const { approved, approved_by, created_by } = req.body;
    if (!approved_by) return res.status(400).json({ success: false, message: 'approved_by diperlukan.' });
    const result = await pool.query(`
      INSERT INTO procurement_request_approvals (procurement_request_id, approved, approved_by, created_by)
      VALUES ($1, $2, $3, $4)
      ON CONFLICT (procurement_request_id, approved_by) DO UPDATE SET approved = EXCLUDED.approved
      RETURNING *
    `, [req.params.id, !!approved, approved_by, created_by || approved_by]);
    res.json({ success: true, message: 'Data berhasil disimpan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── RIWAYAT REVISI ──
router.get('/:id/revisions', async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM procurement_request_revisions WHERE procurement_request_id = $1 ORDER BY created_at DESC', [req.params.id]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/revisions', upload.single('file'), async (req, res) => {
  try {
    const { catatan, created_by } = req.body;
    const filePath = req.file ? `/uploads/${req.file.filename}` : null;
    const result = await pool.query(`
      INSERT INTO procurement_request_revisions (procurement_request_id, catatan, file_path, created_by)
      VALUES ($1, $2, $3, $4) RETURNING *
    `, [req.params.id, catatan || null, filePath, created_by || null]);
    await pool.query("UPDATE procurement_requests SET status = 'revisi' WHERE id = $1", [req.params.id]);
    res.status(201).json({ success: true, message: 'Catatan revisi berhasil dikirim.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── CHECKLIST KELENGKAPAN PER PENGAJUAN ──
router.get('/:id/checklist', async (req, res) => {
  try {
    const { paket_jenis, metode_pemilihan } = req.query;
    let masterSql = 'SELECT * FROM master_checklist WHERE is_active = true';
    const params = [];
    if (paket_jenis) { masterSql += ` AND (paket_jenis IS NULL OR paket_jenis = $${params.length + 1})`; params.push(paket_jenis); }
    if (metode_pemilihan) { masterSql += ` AND (metode_pemilihan IS NULL OR metode_pemilihan = $${params.length + 1})`; params.push(metode_pemilihan); }
    masterSql += ' ORDER BY nama ASC';
    const master = await pool.query(masterSql, params);
    const existing = await pool.query('SELECT * FROM procurement_request_checklist WHERE procurement_request_id = $1', [req.params.id]);

    const merged = master.rows.map(m => {
      const found = existing.rows.find(e => e.master_checklist_id === m.id);
      return { ...m, checklist_item_id: found?.id || null, approved: found?.approved || false, notes: found?.notes || null };
    });
    res.json({ success: true, data: merged });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/checklist', async (req, res) => {
  try {
    const { master_checklist_id, approved, notes, created_by } = req.body;
    if (!master_checklist_id) return res.status(400).json({ success: false, message: 'master_checklist_id diperlukan.' });
    const result = await pool.query(`
      INSERT INTO procurement_request_checklist (procurement_request_id, master_checklist_id, approved, notes, created_by)
      VALUES ($1, $2, $3, $4, $5)
      ON CONFLICT (procurement_request_id, master_checklist_id) DO UPDATE SET approved = EXCLUDED.approved, notes = EXCLUDED.notes, updated_at = CURRENT_TIMESTAMP
      RETURNING *
    `, [req.params.id, master_checklist_id, !!approved, notes || null, created_by || null]);
    res.json({ success: true, message: 'Checklist berhasil disimpan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/pengajuan/:id/activity-log — Timeline rekam jejak pengajuan (padanan REKAM_JEJAK eProc lama) ──
router.get('/:id/activity-log', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT a.*, u.full_name AS user_name
      FROM tender_activity_logs a
      LEFT JOIN users u ON a.user_id = u.id
      WHERE a.procurement_request_id = $1
      ORDER BY a.created_at ASC
    `, [req.params.id]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
