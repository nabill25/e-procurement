const express = require('express');
const router = express.Router();
const { pool } = require('../db');
const { requireAuth, requireRole } = require('../lib/authMiddleware');
const { createUpload, handleUploadError } = require('../lib/upload');
const oracle = require('../lib/oracleIntegration');

const requireAdmin = requireRole('admin');
router.use(requireAuth, requireAdmin); // seluruh modul integrasi Oracle khusus Super Admin, sama seperti sistem lama

// persist: false - file Excel RKA/PR di sini cuma dibaca sekali untuk diparsing isinya,
// tidak perlu disimpan permanen ke Supabase Storage (beda dari upload dokumen di modul lain).
const upload = createUpload('integration', { persist: false });

async function writeLog({ jenis, arah, file_name, status, catatan, jumlah_baris, created_by }) {
  await pool.query(
    `INSERT INTO integration_logs (jenis, arah, file_name, status, catatan, jumlah_baris, created_by)
     VALUES ($1, $2, $3, $4, $5, $6, $7)`,
    [jenis, arah, file_name || null, status, catatan || null, jumlah_baris ?? null, created_by || null]
  );
}

// ── GET /api/integration/status — konfigurasi SFTP aktif atau belum ──
router.get('/status', (req, res) => {
  res.json({ success: true, data: { sftp_configured: oracle.isConfigured } });
});

// ── GET /api/integration/logs — riwayat aktivitas integrasi ──
router.get('/logs', async (req, res) => {
  try {
    const { jenis, page = 1, limit = 30 } = req.query;
    const offset = (parseInt(page) - 1) * parseInt(limit);
    const params = [];
    let where = 'WHERE 1=1';
    if (jenis) { params.push(jenis); where += ` AND l.jenis = $${params.length}`; }
    params.push(parseInt(limit), offset);
    const result = await pool.query(`
      SELECT l.*, u.full_name AS created_by_name
      FROM integration_logs l LEFT JOIN users u ON l.created_by = u.id
      ${where} ORDER BY l.created_at DESC LIMIT $${params.length - 1} OFFSET $${params.length}
    `, params);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── RKA ──
router.get('/rka', async (req, res) => {
  try {
    const { page = 1, limit = 30, tahun } = req.query;
    const offset = (parseInt(page) - 1) * parseInt(limit);
    const params = [];
    let where = 'WHERE 1=1';
    if (tahun) { params.push(parseInt(tahun)); where += ` AND start_date_year = $${params.length}`; }
    params.push(parseInt(limit), offset);
    const result = await pool.query(`
      SELECT id, rka_key, start_date_year, segment1, segment1_desc, segment2, segment2_desc, budget_amt, remain_amt, import_file, imported_at
      FROM integration_rka_budget ${where} ORDER BY imported_at DESC LIMIT $${params.length - 1} OFFSET $${params.length}
    `, params);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.get('/rka/remote-list', async (req, res) => {
  const result = await oracle.listRemoteFiles('rka');
  res.json(result);
});

router.post('/rka/upload', upload.single('file'), handleUploadError, async (req, res) => {
  if (!req.file) return res.status(400).json({ success: false, message: 'File Excel diperlukan.' });
  try {
    const rows = await oracle.parseRkaExcel(req.file.buffer);
    if (!rows.length) {
      await writeLog({ jenis: 'rka_import', arah: 'masuk', file_name: req.file.originalname, status: 'gagal', catatan: 'Tidak ada baris data yang terbaca dari file.', created_by: req.user.id });
      return res.status(400).json({ success: false, message: 'Tidak ada baris data yang terbaca dari file. Pastikan format kolom sesuai (RKA KEY, START DATE YEAR, SEGMENT1, dst).' });
    }
    for (const r of rows) {
      await pool.query(`
        INSERT INTO integration_rka_budget (rka_key, start_date_year, segment1, segment1_desc, segment2, segment2_desc, budget_amt, remain_amt, raw_data, import_file, imported_by)
        VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11)
      `, [r.rka_key, r.start_date_year, r.segment1, r.segment1_desc, r.segment2, r.segment2_desc, r.budget_amt, r.remain_amt, JSON.stringify(r.raw_data), req.file.originalname, req.user.id]);
    }
    await writeLog({ jenis: 'rka_import', arah: 'masuk', file_name: req.file.originalname, status: 'sukses', jumlah_baris: rows.length, created_by: req.user.id });
    res.json({ success: true, message: `${rows.length} baris data RKA berhasil diimpor.`, jumlah_baris: rows.length });
  } catch (err) {
    await writeLog({ jenis: 'rka_import', arah: 'masuk', file_name: req.file.originalname, status: 'gagal', catatan: err.message, created_by: req.user.id }).catch(() => {});
    res.status(500).json({ success: false, message: `Gagal membaca file: ${err.message}` });
  }
});

// ── PR (Purchase Requisition) ──
router.get('/pr', async (req, res) => {
  try {
    const { page = 1, limit = 30 } = req.query;
    const offset = (parseInt(page) - 1) * parseInt(limit);
    const result = await pool.query(`
      SELECT id, requisition_number, description, bu_name, document_status, pr_type, metode_pengadaan, jenis_anggaran, nomor_rup, subdivisi, lines, import_file, imported_at
      FROM integration_pr_import ORDER BY imported_at DESC LIMIT $1 OFFSET $2
    `, [parseInt(limit), offset]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.get('/pr/remote-list', async (req, res) => {
  const result = await oracle.listRemoteFiles('pr');
  res.json(result);
});

router.post('/pr/upload', upload.single('file'), handleUploadError, async (req, res) => {
  if (!req.file) return res.status(400).json({ success: false, message: 'File Excel diperlukan.' });
  try {
    const rows = await oracle.parsePrExcel(req.file.buffer);
    if (!rows.length) {
      await writeLog({ jenis: 'pr_import', arah: 'masuk', file_name: req.file.originalname, status: 'gagal', catatan: 'Tidak ada baris data yang terbaca (kolom REQUISITION_NUMBER wajib ada).', created_by: req.user.id });
      return res.status(400).json({ success: false, message: 'Tidak ada baris data yang terbaca. Pastikan kolom REQUISITION_NUMBER terisi.' });
    }
    for (const r of rows) {
      await pool.query(`
        INSERT INTO integration_pr_import (requisition_number, description, bu_name, document_status, pr_type, metode_pengadaan, jenis_anggaran, nomor_rup, subdivisi, lines, import_file, imported_by)
        VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12)
      `, [r.requisition_number, r.description, r.bu_name, r.document_status, r.pr_type, r.metode_pengadaan, r.jenis_anggaran, r.nomor_rup, r.subdivisi, JSON.stringify(r.lines), req.file.originalname, req.user.id]);
    }
    await writeLog({ jenis: 'pr_import', arah: 'masuk', file_name: req.file.originalname, status: 'sukses', jumlah_baris: rows.length, created_by: req.user.id });
    res.json({ success: true, message: `${rows.length} PR berhasil diimpor.`, jumlah_baris: rows.length });
  } catch (err) {
    await writeLog({ jenis: 'pr_import', arah: 'masuk', file_name: req.file.originalname, status: 'gagal', catatan: err.message, created_by: req.user.id }).catch(() => {});
    res.status(500).json({ success: false, message: `Gagal membaca file: ${err.message}` });
  }
});

// ── Export Supplier & PO (keluar ke Oracle) - benar-benar hasilkan file Excel, tidak perlu
//     koneksi SFTP sama sekali, admin bisa unduh lalu kirimkan sendiri ke tim Oracle ──
router.get('/supplier/export', async (req, res) => {
  try {
    const vendors = await pool.query(`
      SELECT company_name, npwp, email, phone, province, city, qualification_class, status
      FROM vendors WHERE status = 'terverifikasi' ORDER BY company_name ASC
    `);
    const buffer = await oracle.generateSupplierExcel(vendors.rows);
    await writeLog({ jenis: 'supplier_export', arah: 'keluar', file_name: 'supplier_export.xlsx', status: 'sukses', jumlah_baris: vendors.rows.length, created_by: req.user.id });
    res.setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    res.setHeader('Content-Disposition', 'attachment; filename="supplier_export.xlsx"');
    res.send(Buffer.from(buffer));
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.get('/po/export', async (req, res) => {
  try {
    const contracts = await pool.query(`
      SELECT c.contract_number, t.title, v.company_name, c.contract_value, c.contract_date, c.status
      FROM contracts c
      JOIN tenders t ON c.tender_id = t.id
      JOIN vendors v ON c.vendor_id = v.user_id
      WHERE c.status IN ('aktif', 'selesai')
      ORDER BY c.contract_date DESC
    `);
    const buffer = await oracle.generatePoExcel(contracts.rows);
    await writeLog({ jenis: 'po_export', arah: 'keluar', file_name: 'po_export.xlsx', status: 'sukses', jumlah_baris: contracts.rows.length, created_by: req.user.id });
    res.setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    res.setHeader('Content-Disposition', 'attachment; filename="po_export.xlsx"');
    res.send(Buffer.from(buffer));
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
