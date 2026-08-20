const express = require('express');
const router  = express.Router();
const { pool } = require('../db');
const QRCode  = require('qrcode');

// Kode acak 10 karakter (huruf besar + angka, tanpa karakter yang gampang ketuker seperti 0/O, 1/I)
function generateCode() {
  const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
  let code = '';
  for (let i = 0; i < 10; i++) code += chars[Math.floor(Math.random() * chars.length)];
  return code;
}

// ── POST /api/qr/generate — Buat kode QR baru untuk satu dokumen (Admin/PPK/Pokja) ──
router.post('/generate', async (req, res) => {
  try {
    const { source_type, tender_id, vendor_id, info, created_by } = req.body;
    if (!source_type) return res.status(400).json({ success: false, message: 'source_type wajib diisi.' });

    let qr_code, exists = true, attempts = 0;
    while (exists && attempts < 5) {
      qr_code = generateCode();
      const check = await pool.query('SELECT id FROM qr_validations WHERE qr_code = $1', [qr_code]);
      exists = check.rows.length > 0;
      attempts++;
    }
    if (exists) return res.status(500).json({ success: false, message: 'Gagal membuat kode unik, coba lagi.' });

    const result = await pool.query(`
      INSERT INTO qr_validations (qr_code, source_type, tender_id, vendor_id, info, created_by)
      VALUES ($1, $2, $3, $4, $5, $6)
      RETURNING *
    `, [qr_code, source_type, tender_id || null, vendor_id || null, info || null, created_by || null]);

    const frontendUrl = process.env.FRONTEND_URL || 'http://localhost:5173';
    const verifyUrl = `${frontendUrl}/verify/${qr_code}`;
    const qrImageDataUrl = await QRCode.toDataURL(verifyUrl, { width: 300 });

    res.status(201).json({
      success: true,
      message: 'Kode QR berhasil dibuat.',
      data: { ...result.rows[0], verify_url: verifyUrl, qr_image: qrImageDataUrl },
    });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/qr/verify/:code — Cek keaslian dokumen (Publik) ──
router.get('/verify/:code', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT q.*, t.title AS tender_title, t.tender_number, v.company_name AS vendor_name
      FROM qr_validations q
      LEFT JOIN tenders t ON q.tender_id = t.id
      LEFT JOIN vendors v ON q.vendor_id = v.user_id
      WHERE q.qr_code = $1
    `, [req.params.code.toUpperCase()]);

    if (!result.rows.length) {
      return res.status(404).json({ success: false, valid: false, message: 'Kode tidak ditemukan. Dokumen ini tidak dapat diverifikasi keasliannya.' });
    }

    res.json({ success: true, valid: true, data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
