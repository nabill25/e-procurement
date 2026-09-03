const express = require('express');
const router  = express.Router();
const { pool } = require('../db');
const { requireRole } = require('../lib/authMiddleware');
const { createUpload, handleUploadError } = require('../lib/upload');

// Modul "Setup Supplier Oracle" - padanan alur tiket dari project terpisah yang ditunjukkan
// pengguna (setup-supplier-request/, sistem CodeIgniter 4 berdiri sendiri). BUKAN bagian dari
// fitur "Integrasi Oracle" yang sudah ada (server/routes/integration.js, itu soal sinkronisasi
// RKA/PR lewat SFTP) - ini modul terpisah: alur permintaan SETUP supplier baru di Oracle EBS,
// dari staf Operating Unit sampai tim support Oracle mengerjakannya.
//
// Alur: Pengaju (submit) -> Verifikator (verifikasi & teruskan) -> Dispatcher (dispatch ke
// pelaksana atau ambil sendiri) -> Pelaksana (kerjakan di Oracle EBS, upload bukti selesai).
//
// Penyederhanaan sengaja dari rancangan asli: rancangan asli mencatat verified_at OTOMATIS
// begitu Verifikator membuka halaman detail (efek samping dari GET), lalu form catatan+
// tanggal aktivasi dikirim terpisah untuk "teruskan". Di sini digabung jadi SATU aksi eksplisit
// (POST .../verify-and-forward) supaya tidak ada operasi GET yang mengubah data (anti-pattern
// REST) - hasil akhirnya sama (status diverifikasi lalu diteruskan sekaligus tercatat).

const requireOracleTeam = requireRole('admin', 'verifikator_oracle', 'dispatcher_oracle', 'pelaksana_oracle');
const upload = createUpload('oracle-supplier');

const VALID_STATUS = ['diajukan', 'diverifikasi', 'diteruskan', 'didispatch', 'dikerjakan', 'selesai'];

async function catatLog(client, requestId, status, changedBy, catatan) {
  await client.query(
    `INSERT INTO oracle_supplier_request_logs (request_id, status, changed_by, catatan) VALUES ($1, $2, $3, $4)`,
    [requestId, status, changedBy || null, catatan || null]
  );
}

async function generateKode() {
  const tahun = new Date().getFullYear();
  const count = await pool.query(
    `SELECT COUNT(*)::int AS jml FROM oracle_supplier_requests WHERE kode LIKE $1`,
    [`RSS-${tahun}-%`]
  );
  const urut = String(count.rows[0].jml + 1).padStart(5, '0');
  return `RSS-${tahun}-${urut}`;
}

// ── Daftar pelaksana (buat dropdown dispatch) - statis, taruh sebelum /:id ──
router.get('/pelaksana', requireRole('admin', 'dispatcher_oracle'), async (req, res) => {
  try {
    const result = await pool.query(
      `SELECT id, full_name, email FROM users WHERE role = 'pelaksana_oracle' ORDER BY full_name ASC`
    );
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET / — daftar request. Pengaju cuma lihat milik sendiri, role lain (tim Oracle/admin)
// lihat semua supaya bisa dikelompokkan jadi papan kanban di sisi klien per status. ──
router.get('/', async (req, res) => {
  try {
    const isOracleTeam = ['admin', 'verifikator_oracle', 'dispatcher_oracle', 'pelaksana_oracle'].includes(req.user.role);
    const params = [];
    let where = '1=1';
    if (!isOracleTeam) {
      params.push(req.user.id);
      where = `r.created_by = $${params.length}`;
    }
    const result = await pool.query(`
      SELECT r.*, u1.full_name AS created_by_name, u2.full_name AS assigned_to_name
      FROM oracle_supplier_requests r
      LEFT JOIN users u1 ON r.created_by = u1.id
      LEFT JOIN users u2 ON r.assigned_to = u2.id
      WHERE ${where}
      ORDER BY r.submitted_at DESC
    `, params);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /:id — detail + riwayat status ──
router.get('/:id', async (req, res) => {
  try {
    const reqRow = await pool.query(`
      SELECT r.*, u1.full_name AS created_by_name, u2.full_name AS assigned_to_name, u3.full_name AS verified_by_name
      FROM oracle_supplier_requests r
      LEFT JOIN users u1 ON r.created_by = u1.id
      LEFT JOIN users u2 ON r.assigned_to = u2.id
      LEFT JOIN users u3 ON r.verified_by = u3.id
      WHERE r.id = $1
    `, [req.params.id]);
    if (!reqRow.rows.length) return res.status(404).json({ success: false, message: 'Permintaan tidak ditemukan.' });

    const isOracleTeam = ['admin', 'verifikator_oracle', 'dispatcher_oracle', 'pelaksana_oracle'].includes(req.user.role);
    if (!isOracleTeam && reqRow.rows[0].created_by !== req.user.id) {
      return res.status(403).json({ success: false, message: 'Anda tidak berhak melihat permintaan ini.' });
    }

    const logs = await pool.query(`
      SELECT l.*, u.full_name AS changed_by_name
      FROM oracle_supplier_request_logs l LEFT JOIN users u ON l.changed_by = u.id
      WHERE l.request_id = $1 ORDER BY l.changed_at ASC
    `, [req.params.id]);

    res.json({ success: true, data: { ...reqRow.rows[0], logs: logs.rows } });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST / — Pengaju ajukan permintaan baru ──
router.post('/', requireRole('admin', 'pengaju_oracle'), async (req, res) => {
  const client = await pool.connect();
  try {
    const b = req.body;
    if (!b.operating_unit || !b.nama_supplier) {
      return res.status(400).json({ success: false, message: 'Operating Unit dan Nama Supplier wajib diisi.' });
    }
    const kode = await generateKode();

    await client.query('BEGIN');
    const result = await client.query(`
      INSERT INTO oracle_supplier_requests (
        kode, operating_unit, nama_supplier, alamat_kantor, no_telp, nama_kontak, jabatan,
        no_pkp, no_nib, tgl_nib, domisili, npwp, alamat_npwp, nama_bank, cabang_bank,
        nama_rekening, nomor_rekening, mata_uang, nama_paket_rup, kode_rup, created_by
      ) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19,$20,$21)
      RETURNING *
    `, [
      kode, b.operating_unit, b.nama_supplier, b.alamat_kantor || null, b.no_telp || null,
      b.nama_kontak || null, b.jabatan || null, b.no_pkp || null, b.no_nib || null,
      b.tgl_nib || null, b.domisili || null, b.npwp || null, b.alamat_npwp || null,
      b.nama_bank || null, b.cabang_bank || null, b.nama_rekening || null, b.nomor_rekening || null,
      b.mata_uang || 'IDR', b.nama_paket_rup || null, b.kode_rup || null, req.user.id,
    ]);
    await catatLog(client, result.rows[0].id, 'diajukan', req.user.id, 'Permintaan diajukan.');
    await client.query('COMMIT');

    res.status(201).json({ success: true, message: `Permintaan ${kode} berhasil diajukan.`, data: result.rows[0] });
  } catch (err) {
    await client.query('ROLLBACK');
    res.status(500).json({ success: false, message: err.message });
  } finally {
    client.release();
  }
});

// ── POST /:id/verify-and-forward — Verifikator validasi + teruskan ke tim Oracle sekaligus ──
router.post('/:id/verify-and-forward', requireRole('admin', 'verifikator_oracle'), async (req, res) => {
  const client = await pool.connect();
  try {
    const cur = await pool.query('SELECT status FROM oracle_supplier_requests WHERE id = $1', [req.params.id]);
    if (!cur.rows.length) return res.status(404).json({ success: false, message: 'Permintaan tidak ditemukan.' });
    if (cur.rows[0].status !== 'diajukan') {
      return res.status(400).json({ success: false, message: `Permintaan ini sudah berstatus "${cur.rows[0].status}", tidak bisa diverifikasi ulang.` });
    }

    const { catatan_verifikator, aktivasi_dari, aktivasi_sampai } = req.body;
    await client.query('BEGIN');
    const result = await client.query(`
      UPDATE oracle_supplier_requests SET
        status = 'diteruskan', verified_at = CURRENT_TIMESTAMP, verified_by = $1,
        forwarded_at = CURRENT_TIMESTAMP, catatan_verifikator = $2,
        aktivasi_dari = $3, aktivasi_sampai = $4
      WHERE id = $5 RETURNING *
    `, [req.user.id, catatan_verifikator || null, aktivasi_dari || null, aktivasi_sampai || null, req.params.id]);
    await catatLog(client, req.params.id, 'diteruskan', req.user.id, catatan_verifikator || 'Diverifikasi dan diteruskan ke Tim Support Oracle.');
    await client.query('COMMIT');

    res.json({ success: true, message: 'Permintaan diverifikasi dan diteruskan ke Tim Support Oracle.', data: result.rows[0] });
  } catch (err) {
    await client.query('ROLLBACK');
    res.status(500).json({ success: false, message: err.message });
  } finally {
    client.release();
  }
});

// ── POST /:id/dispatch — Dispatcher pilih pelaksana (atau diri sendiri) ──
router.post('/:id/dispatch', requireRole('admin', 'dispatcher_oracle'), async (req, res) => {
  const client = await pool.connect();
  try {
    const cur = await pool.query('SELECT status FROM oracle_supplier_requests WHERE id = $1', [req.params.id]);
    if (!cur.rows.length) return res.status(404).json({ success: false, message: 'Permintaan tidak ditemukan.' });
    if (cur.rows[0].status !== 'diteruskan') {
      return res.status(400).json({ success: false, message: `Permintaan ini berstatus "${cur.rows[0].status}", belum bisa di-dispatch.` });
    }

    const assignedTo = req.body.assigned_to || req.user.id; // kosong = dispatcher ambil sendiri
    await client.query('BEGIN');
    const result = await client.query(`
      UPDATE oracle_supplier_requests SET
        status = 'didispatch', dispatched_at = CURRENT_TIMESTAMP, dispatched_by = $1, assigned_to = $2
      WHERE id = $3 RETURNING *
    `, [req.user.id, assignedTo, req.params.id]);
    await catatLog(client, req.params.id, 'didispatch', req.user.id, 'Ditugaskan ke tim pelaksana.');
    await client.query('COMMIT');

    res.json({ success: true, message: 'Permintaan berhasil di-dispatch.', data: result.rows[0] });
  } catch (err) {
    await client.query('ROLLBACK');
    res.status(500).json({ success: false, message: err.message });
  } finally {
    client.release();
  }
});

// ── POST /:id/start — Pelaksana buka/mulai kerjakan ──
router.post('/:id/start', requireOracleTeam, async (req, res) => {
  const client = await pool.connect();
  try {
    const cur = await pool.query('SELECT status, assigned_to FROM oracle_supplier_requests WHERE id = $1', [req.params.id]);
    if (!cur.rows.length) return res.status(404).json({ success: false, message: 'Permintaan tidak ditemukan.' });
    if (cur.rows[0].status !== 'didispatch') {
      return res.status(400).json({ success: false, message: `Permintaan ini berstatus "${cur.rows[0].status}", belum bisa dimulai.` });
    }
    if (req.user.role === 'pelaksana_oracle' && cur.rows[0].assigned_to !== req.user.id) {
      return res.status(403).json({ success: false, message: 'Permintaan ini bukan ditugaskan untuk Anda.' });
    }

    await client.query('BEGIN');
    const result = await client.query(
      `UPDATE oracle_supplier_requests SET status = 'dikerjakan', started_at = CURRENT_TIMESTAMP WHERE id = $1 RETURNING *`,
      [req.params.id]
    );
    await catatLog(client, req.params.id, 'dikerjakan', req.user.id, 'Mulai dikerjakan di Oracle EBS.');
    await client.query('COMMIT');

    res.json({ success: true, message: 'Permintaan ditandai mulai dikerjakan.', data: result.rows[0] });
  } catch (err) {
    await client.query('ROLLBACK');
    res.status(500).json({ success: false, message: err.message });
  } finally {
    client.release();
  }
});

// ── POST /:id/complete — Pelaksana selesaikan + wajib upload bukti screenshot ──
router.post('/:id/complete', requireOracleTeam, upload.single('bukti'), handleUploadError, async (req, res) => {
  const client = await pool.connect();
  try {
    const cur = await pool.query('SELECT status, assigned_to FROM oracle_supplier_requests WHERE id = $1', [req.params.id]);
    if (!cur.rows.length) return res.status(404).json({ success: false, message: 'Permintaan tidak ditemukan.' });
    if (cur.rows[0].status !== 'dikerjakan') {
      return res.status(400).json({ success: false, message: `Permintaan ini berstatus "${cur.rows[0].status}", belum bisa diselesaikan.` });
    }
    if (req.user.role === 'pelaksana_oracle' && cur.rows[0].assigned_to !== req.user.id) {
      return res.status(403).json({ success: false, message: 'Permintaan ini bukan ditugaskan untuk Anda.' });
    }
    if (!req.file) {
      return res.status(400).json({ success: false, message: 'Bukti screenshot wajib diunggah untuk menandai selesai.' });
    }

    const buktiPath = req.file.filename;
    await client.query('BEGIN');
    const result = await client.query(
      `UPDATE oracle_supplier_requests SET status = 'selesai', completed_at = CURRENT_TIMESTAMP, bukti_screenshot = $1 WHERE id = $2 RETURNING *`,
      [buktiPath, req.params.id]
    );
    await catatLog(client, req.params.id, 'selesai', req.user.id, 'Selesai dikerjakan, bukti terlampir.');
    await client.query('COMMIT');

    res.json({ success: true, message: 'Permintaan ditandai selesai.', data: result.rows[0] });
  } catch (err) {
    await client.query('ROLLBACK');
    res.status(500).json({ success: false, message: err.message });
  } finally {
    client.release();
  }
});

module.exports = router;
