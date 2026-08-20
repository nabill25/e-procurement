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
            needed_by_date, requester_id } = req.body;

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
         status, kak_path, rab_path, nota_dinas_path)
      VALUES (gen_random_uuid(), $1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, 'diajukan', $15, $16, $17)
    `, [request_number, title, unit_kerja, category||null, estimated_value,
        budget_source||null, budget_code||null, year, description||null,
        technical_spec||null, quantity||null, unit_of_measure||null,
        needed_by_date||null, validRequesterId, kakPath, rabPath, notaPath]);

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
    await pool.query("UPDATE procurement_requests SET status = 'diajukan' WHERE id = $1 AND status = 'draft'", [id]);
    res.json({ success: true, message: 'Pengajuan disubmit.' });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

// ── POST /api/pengajuan/:id/review — Tahap 1 Admin Review Dokumen ──
router.post('/:id/review', async (req, res) => {
  try {
    const { id } = req.params;
    const { admin_notes, is_docs_complete } = req.body;

    const result = await pool.query('SELECT * FROM procurement_requests WHERE id = $1', [id]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Not found' });
    const p = result.rows[0];

    if (p.status !== 'diajukan') {
      return res.status(400).json({ success: false, message: 'Pengajuan belum diajukan atau sudah diproses.' });
    }

    if (!is_docs_complete) {
      // Jika dokumen belum lengkap, bisa ditolak atau dikembalikan
      await pool.query("UPDATE procurement_requests SET status = 'ditolak', admin_notes = $1 WHERE id = $2", [admin_notes, id]);
      return res.json({ success: true, message: 'Pengajuan ditolak karena dokumen tidak lengkap.' });
    }

    await pool.query("UPDATE procurement_requests SET status = 'proses_review', admin_notes = $1, is_docs_complete = $2 WHERE id = $3", [admin_notes, is_docs_complete, id]);
    
    await pool.query(`INSERT INTO audit_logs (action, entity_type, description, is_success) VALUES ('UPDATE', 'Pengajuan', $1, true)`, [`Admin mereview dokumen pengajuan: ${p.title}`]);

    res.json({ success: true, message: 'Dokumen lengkap. Pengajuan masuk ke tahap Review Akhir.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/pengajuan/:id/approve — Tahap 2 Admin ACC pengajuan ──
router.post('/:id/approve', async (req, res) => {
  try {
    const { id } = req.params;
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

module.exports = router;
