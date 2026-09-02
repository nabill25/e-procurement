const express = require('express');
const router  = express.Router();
const { pool } = require('../db');
const { createUpload, handleUploadError } = require('../lib/upload');
const { requireRole } = require('../lib/authMiddleware');
const { sendMail } = require('../lib/mailer');
const requireAdmin = requireRole('admin');
// Padanan role "APPROVAL VMS" di sistem lama (menu "Daftar Rekanan Approval") - staf yang
// tugasnya khusus meninjau dan menyetujui/menolak status kualifikasi vendor, tanpa akses admin
// penuh ke seluruh sistem.
const requireVendorApproval = requireRole('admin', 'approval_vms');
// Padanan role "ADMIN VMS" di sistem lama (menu "Hapus Data Vendor").
const requireVendorDelete = requireRole('admin', 'admin_vms');

// Kalau yang login adalah vendor, pastikan dia cuma mengelola datanya sendiri (:id di path
// selalu berarti users.id milik vendor tersebut). Admin/PPK/Pokja tetap boleh bertindak atas
// nama vendor manapun untuk keperluan administratif.
function ownVendorDataOnly(req, res, next) {
  if (req.user.role === 'vendor' && String(req.params.id) !== String(req.user.id)) {
    return res.status(403).json({ success: false, message: 'Anda cuma bisa mengelola data akun Anda sendiri.' });
  }
  next();
}

// ── Konfigurasi Multer ──
const upload = createUpload('vendors');

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

router.post('/retail', requireAdmin, async (req, res) => {
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

router.put('/retail/:retailId', requireAdmin, async (req, res) => {
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

router.delete('/retail/:retailId', requireAdmin, async (req, res) => {
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
      SELECT v.id, v.user_id, v.company_name, v.npwp, v.nib, v.company_type, v.city, v.province, v.email, v.phone, v.status, v.blacklisted, v.qualification_class, v.created_at,
             u.rating_avg, u.rating_count
      FROM vendors v
      LEFT JOIN users u ON v.user_id = u.id
      WHERE v.deleted_at IS NULL
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

// ── Antrian vendor yang perlu diingatkan (tindak lanjut kelengkapan dokumen) — path statis,
// WAJIB ditaruh sebelum GET /:id supaya tidak salah tertangkap sebagai id="followup-reminder-queue" ──
router.get('/followup-reminder-queue', requireVendorApproval, async (req, res) => {
  try {
    const hariJeda = parseInt(req.query.hari) || HARI_JEDA_REMINDER_TL;
    const maksReminder = parseInt(req.query.maks) || MAKS_REMINDER_TL;
    const result = await pool.query(`
      SELECT v.id AS vendor_id, v.company_name, v.email,
             t.catatan, t.created_at AS sejak,
             EXTRACT(DAY FROM (NOW() - t.created_at))::int AS hari_diam,
             (SELECT COUNT(*) FROM vendor_followups x WHERE x.vendor_id = v.id AND x.jenis = 'reminder') AS jumlah_reminder
      FROM vendors v
      JOIN LATERAL (
        SELECT status, catatan, created_at FROM vendor_followups
        WHERE vendor_id = v.id ORDER BY created_at DESC, id DESC LIMIT 1
      ) t ON true
      WHERE t.status = 'perlu_dilengkapi'
        AND t.created_at < NOW() - ($1 || ' days')::interval
        AND v.email IS NOT NULL AND v.email <> ''
        AND (SELECT COUNT(*) FROM vendor_followups x WHERE x.vendor_id = v.id AND x.jenis = 'reminder') < $2
      ORDER BY t.created_at ASC
    `, [hariJeda, maksReminder]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/vendors/:id — :id di sini adalah users.id (konsisten dengan seluruh aplikasi,
// lihat vendor_documents.vendor_id, tender_participants.vendor_id, dst yang semuanya FK ke users) ──
router.get('/:id', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT v.*, u.rating_avg, u.rating_count
      FROM vendors v
      LEFT JOIN users u ON v.user_id = u.id
      WHERE v.user_id = $1 AND v.deleted_at IS NULL
    `, [req.params.id]);
    const rows = result.rows;
    if (!rows.length) return res.status(404).json({ success: false, message: 'Vendor tidak ditemukan.' });
    res.json({ success: true, data: rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── PUT /api/vendors/:id/profile ──
router.put('/:id/profile', ownVendorDataOnly, async (req, res) => {
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
    const sql = `UPDATE vendors SET ${updates.join(', ')} WHERE user_id = $${idx} RETURNING *`;
    
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
router.patch('/:id/verify', requireVendorApproval, async (req, res) => {
  try {
    const { verified_by } = req.body;
    const result = await pool.query('SELECT company_name, user_id FROM vendors WHERE id = $1', [req.params.id]);
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

    // Email pemberitahuan lulus verifikasi - meniru email/validasi_konfirmasi.php di sistem lama
    const userEmail = await pool.query('SELECT email, full_name FROM users WHERE id = $1', [result.rows[0].user_id]);
    if (userEmail.rows.length && userEmail.rows[0].email) {
      sendMail({
        to: userEmail.rows[0].email,
        subject: 'Verifikasi Akun Berhasil - Sistem e-Procurement DPBJ Universitas Indonesia',
        html: `
          <p>Yth. ${result.rows[0].company_name},</p>
          <p>Selamat, akun perusahaan Anda telah <strong>berhasil diverifikasi</strong> oleh Admin Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia.</p>
          <p>Anda sekarang dapat mendaftar dan mengikuti tender pengadaan yang dipublikasikan di sistem.</p>
          <p>Terima kasih.<br/>Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia</p>
        `,
      }).catch(err => console.error('[VERIFY MAIL]', err));
    }

    res.json({ success: true, message: 'Vendor berhasil diverifikasi.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/vendors/:id/status — Update status vendor (general) ──
// Mengikuti eProc: mapping status_validasi ke berbagai status
router.post('/:id/status', requireVendorApproval, async (req, res) => {
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
router.patch('/:id/suspend', requireVendorApproval, async (req, res) => {
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
router.patch('/:id/block', requireVendorApproval, async (req, res) => {
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

// ── DELETE /api/vendors/:id — Hapus data vendor (soft-delete) ──
// Padanan menu "Hapus Data Vendor" (daftar_rekanan_delete) khusus Admin VMS di sistem lama.
// Pakai kolom deleted_at (kerangkanya sudah disiapkan sejak Kelompok G, belum pernah dipakai
// fitur apapun sampai sekarang) - BUKAN hapus baris dari database, supaya riwayat transaksi
// vendor itu (tender yang pernah diikuti, kontrak, dst) tetap utuh dan tidak menabrak foreign
// key. Akun login vendor itu ikut dinonaktifkan (users.deleted_at) supaya tidak bisa login lagi.
router.delete('/:id', requireVendorDelete, async (req, res) => {
  try {
    const result = await pool.query('SELECT user_id, company_name FROM vendors WHERE id = $1 AND deleted_at IS NULL', [req.params.id]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Vendor tidak ditemukan (atau sudah dihapus sebelumnya).' });
    const vendor = result.rows[0];

    await pool.query('UPDATE vendors SET deleted_at = CURRENT_TIMESTAMP WHERE id = $1', [req.params.id]);
    await pool.query('UPDATE users SET deleted_at = CURRENT_TIMESTAMP WHERE id = $1', [vendor.user_id]);

    await pool.query(
      `INSERT INTO audit_logs (action, entity_type, description, is_success) VALUES ('DELETE', 'Vendor', $1, true)`,
      [`Vendor dihapus: ${vendor.company_name}`]
    );

    res.json({ success: true, message: 'Vendor berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/vendors/:id/qualifications — Ambil dokumen & pengalaman ──
router.get('/:id/qualifications', ownVendorDataOnly, async (req, res) => {
  try {
    const docsResult = await pool.query('SELECT * FROM vendor_documents WHERE vendor_id = $1 ORDER BY created_at DESC', [req.params.id]);
    const expResult = await pool.query('SELECT * FROM vendor_experiences WHERE vendor_id = $1 ORDER BY start_date DESC', [req.params.id]);
    const sikapResult = await pool.query('SELECT pajak, tenaga_ahli, peralatan, pengurus, bank, neraca FROM vendors WHERE user_id = $1', [req.params.id]);

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

// ── TINDAK LANJUT KELENGKAPAN DOKUMEN PENYEDIA ──
// Padanan fitur "Pencatatan Tindak Lanjut Kelengkapan Dokumen Penyedia" yang dirancang untuk
// sistem lama (draft di folder root project 2026-09-01_pencatatan-tindak-lanjut-verifikasi-
// penyedia/, belum pernah dipasang di eproc production). Melacak bolak-balik (tektok) antara
// verifikator dan penyedia saat melengkapi dokumen registrasi: verifikator kirim catatan minta
// lengkapi -> penyedia konfirmasi sudah lengkap -> verifikator cek ulang -> tandai terverifikasi
// atau ulang dari awal. :id di sini adalah vendors.id (konsisten dengan endpoint verify/status/
// suspend/block di atas), BUKAN users.id seperti kebanyakan endpoint lain di file ini.
//
// Perbedaan sengaja dari rancangan aslinya (disesuaikan dengan sistem baru):
// - Status/jenis/pihak pakai huruf kecil snake_case, konsisten dengan vocabulary status vendor
//   yang sudah ada (pending/terverifikasi/ditangguhkan/diblokir), bukan UPPERCASE.
// - Tidak butuh konstanta "email fallback verifikator" seperti rancangan lama - tabel users di
//   sistem baru sudah punya kolom email asli untuk semua akun (beda dari sistem lama yang
//   username stafnya belum tentu berupa email), jadi email tujuan "penyedia sudah melengkapi"
//   diambil langsung dari akun verifikator yang terakhir menangani. FOLLOWUP_EMAIL_FALLBACK
//   (env var opsional) cuma jaga-jaga kalau baris tersebut somehow tidak ketemu.
// - Rancangan lama pakai cron OS (crontab) yang panggil endpoint HTTP terjadwal. Sistem baru ini
//   tidak punya cron OS aktif, jadi "pengingat otomatis" diganti jadi tombol yang dipicu manual
//   admin/approval_vms dari daftar antrian (GET /followup-reminder-queue di bawah), sama seperti
//   pola yang sudah dipakai fitur "Dokumen Kedaluwarsa" (lihat server/routes/master.js). Saklar
//   on/off-nya reuse app_settings yang sudah ada (kunci 'reminder_tindak_lanjut_vendor').
const HARI_JEDA_REMINDER_TL = 7;
const MAKS_REMINDER_TL = 3;

// Ringkasan + riwayat tindak lanjut untuk 1 vendor. Bisa dibaca verifikator (admin/pokja/
// admin_vms/approval_vms, semuanya yang bisa membuka modal Verifikasi Vendor) ATAU vendor yang
// bersangkutan sendiri (dicek lewat vendors.user_id, bukan string compare sederhana seperti
// ownVendorDataOnly karena :id di sini vendors.id, bukan users.id).
router.get('/:id/followup', async (req, res) => {
  try {
    const vendorRow = await pool.query('SELECT id, user_id FROM vendors WHERE id = $1', [req.params.id]);
    if (!vendorRow.rows.length) return res.status(404).json({ success: false, message: 'Vendor tidak ditemukan.' });

    const isOwner = req.user.role === 'vendor' && vendorRow.rows[0].user_id === req.user.id;
    const isStaff = ['admin', 'pokja', 'admin_vms', 'approval_vms'].includes(req.user.role);
    if (!isOwner && !isStaff) {
      return res.status(403).json({ success: false, message: 'Anda tidak berhak melihat data ini.' });
    }

    const timeline = await pool.query(`
      SELECT f.*, u.full_name AS created_by_name
      FROM vendor_followups f
      LEFT JOIN users u ON f.created_by = u.id
      WHERE f.vendor_id = $1
      ORDER BY f.created_at ASC, f.id ASC
    `, [req.params.id]);

    const rows = timeline.rows;
    const last = rows.length ? rows[rows.length - 1] : null;
    const followUpCount = rows.filter(r => r.jenis === 'permintaan' || r.jenis === 'reminder').length;

    res.json({
      success: true,
      data: {
        ada: !!last,
        status: last ? last.status : null,
        catatan_terakhir: last ? last.catatan : null,
        sejak: last ? last.created_at : null,
        follow_up_count: followUpCount,
        timeline: rows,
      },
    });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// Verifikator kirim catatan minta penyedia melengkapi berkas. Status jadi perlu_dilengkapi,
// email otomatis ke penyedia (kalau SMTP dikonfigurasi, kalau tidak tetap tercatat).
router.post('/:id/followup/request', requireVendorApproval, async (req, res) => {
  try {
    const catatan = (req.body.catatan || '').trim();
    if (!catatan) return res.status(400).json({ success: false, message: 'Catatan wajib diisi.' });

    const vendorRow = await pool.query('SELECT company_name, email FROM vendors WHERE id = $1', [req.params.id]);
    if (!vendorRow.rows.length) return res.status(404).json({ success: false, message: 'Vendor tidak ditemukan.' });
    const vendor = vendorRow.rows[0];

    let emailTerkirim = false;
    if (vendor.email) {
      const mailResult = await sendMail({
        to: vendor.email,
        subject: 'Permintaan Kelengkapan Dokumen - Sistem e-Procurement DPBJ Universitas Indonesia',
        html: `
          <p>Yth. ${vendor.company_name},</p>
          <p>Verifikator kami sudah memeriksa dokumen registrasi perusahaan Anda dan menemukan ada yang perlu dilengkapi:</p>
          <blockquote style="border-left:3px solid #c0392b; margin:12px 0; padding:8px 14px; background:#fbeceb; font-style:italic;">${catatan.replace(/</g, '&lt;')}</blockquote>
          <p>Mohon segera lengkapi lewat halaman Profil &amp; Kualifikasi pada akun Anda, lalu klik tombol <strong>"Sudah Saya Lengkapi"</strong> supaya kami bisa memeriksa ulang.</p>
          <p>Terima kasih.<br/>Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia</p>
        `,
      });
      emailTerkirim = mailResult.sent;
    }

    await pool.query(`
      INSERT INTO vendor_followups (vendor_id, status, jenis, catatan, pihak, created_by, email_tujuan, email_terkirim, email_terkirim_at)
      VALUES ($1, 'perlu_dilengkapi', 'permintaan', $2, 'verifikator', $3, $4, $5, $6)
    `, [req.params.id, catatan, req.user.id, vendor.email || null, emailTerkirim, emailTerkirim ? new Date() : null]);

    res.json({
      success: true,
      message: emailTerkirim
        ? 'Catatan tersimpan. Email pemberitahuan terkirim ke penyedia.'
        : 'Catatan tersimpan. Email ke penyedia belum terkirim (lihat riwayat), status tetap tercatat.',
    });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// Verifikator menyatakan dokumen penyedia sudah lengkap/oke - menutup satu siklus tindak
// lanjut. TIDAK menyentuh kolom vendors.status, itu tetap lewat tombol Verifikasi/Blokir/
// Tangguhkan yang sudah ada (sama seperti rancangan aslinya).
router.post('/:id/followup/complete', requireVendorApproval, async (req, res) => {
  try {
    const vendorRow = await pool.query('SELECT id FROM vendors WHERE id = $1', [req.params.id]);
    if (!vendorRow.rows.length) return res.status(404).json({ success: false, message: 'Vendor tidak ditemukan.' });

    await pool.query(`
      INSERT INTO vendor_followups (vendor_id, status, jenis, catatan, pihak, created_by)
      VALUES ($1, 'terverifikasi', 'selesai', $2, 'verifikator', $3)
    `, [req.params.id, (req.body.catatan || '').trim() || null, req.user.id]);

    res.json({ success: true, message: 'Dokumen ditandai terverifikasi.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// Penyedia konfirmasi sudah melengkapi berkas sesuai catatan verifikator. Cuma boleh untuk
// vendor miliknya sendiri (dicegah konfirmasi atas nama perusahaan lain), admin boleh override.
router.post('/:id/followup/confirm', async (req, res) => {
  try {
    const vendorRow = await pool.query('SELECT id, user_id, company_name FROM vendors WHERE id = $1', [req.params.id]);
    if (!vendorRow.rows.length) return res.status(404).json({ success: false, message: 'Vendor tidak ditemukan.' });
    const vendor = vendorRow.rows[0];

    const isOwner = req.user.role === 'vendor' && vendor.user_id === req.user.id;
    if (!isOwner && req.user.role !== 'admin') {
      return res.status(403).json({ success: false, message: 'Anda hanya bisa konfirmasi untuk perusahaan sendiri.' });
    }

    const catatan = (req.body.catatan || '').trim() || null;

    // Cari verifikator yang paling terakhir mengirim permintaan/selesai untuk vendor ini,
    // kirim pemberitahuan ke email akun verifikator itu (bukan broadcast ke semua verifikator).
    const lastVerif = await pool.query(`
      SELECT u.email, u.full_name FROM vendor_followups f
      JOIN users u ON u.id = f.created_by
      WHERE f.vendor_id = $1 AND f.jenis IN ('permintaan', 'selesai')
      ORDER BY f.created_at DESC, f.id DESC LIMIT 1
    `, [req.params.id]);

    const tujuanEmail = lastVerif.rows[0]?.email || process.env.FOLLOWUP_EMAIL_FALLBACK || null;
    const tujuanNama = lastVerif.rows[0]?.full_name || 'Tim Verifikasi Penyedia';

    let emailTerkirim = false;
    if (tujuanEmail) {
      const mailResult = await sendMail({
        to: tujuanEmail,
        subject: `Penyedia Sudah Melengkapi Dokumen - ${vendor.company_name}`,
        html: `
          <p>Yth. ${tujuanNama},</p>
          <p>Penyedia <strong>${vendor.company_name}</strong> sudah mengkonfirmasi kelengkapan dokumen sesuai catatan yang diberikan sebelumnya.</p>
          ${catatan ? `<blockquote style="border-left:3px solid #27ae60; margin:12px 0; padding:8px 14px; background:#eafaf0; font-style:italic;">${catatan.replace(/</g, '&lt;')}</blockquote>` : ''}
          <p>Mohon diperiksa ulang lewat halaman Manajemen Penyedia.</p>
          <p>Terima kasih.<br/>Sistem e-Procurement DPBJ Universitas Indonesia</p>
        `,
      });
      emailTerkirim = mailResult.sent;
    }

    await pool.query(`
      INSERT INTO vendor_followups (vendor_id, status, jenis, catatan, pihak, created_by, email_tujuan, email_terkirim, email_terkirim_at)
      VALUES ($1, 'sudah_dilengkapi', 'konfirmasi', $2, 'penyedia', $3, $4, $5, $6)
    `, [req.params.id, catatan, req.user.id, tujuanEmail, emailTerkirim, emailTerkirim ? new Date() : null]);

    res.json({
      success: true,
      message: emailTerkirim
        ? 'Konfirmasi tersimpan. Verifikator sudah diberi tahu lewat email untuk memeriksa ulang.'
        : 'Konfirmasi tersimpan. Verifikator akan memeriksa ulang dokumen Anda.',
    });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// Kirim satu pengingat manual ke vendor yang masih perlu_dilengkapi (dipicu admin/approval_vms
// dari daftar antrian, padanan cron pengingat di rancangan asli - lihat komentar di atas soal
// kenapa manual bukan cron OS).
router.post('/:id/followup/remind', requireVendorApproval, async (req, res) => {
  try {
    const vendorRow = await pool.query('SELECT company_name, email FROM vendors WHERE id = $1', [req.params.id]);
    if (!vendorRow.rows.length) return res.status(404).json({ success: false, message: 'Vendor tidak ditemukan.' });
    const vendor = vendorRow.rows[0];

    let emailTerkirim = false;
    if (vendor.email) {
      const mailResult = await sendMail({
        to: vendor.email,
        subject: 'Pengingat: Kelengkapan Dokumen Registrasi Anda Masih Ditunggu',
        html: `
          <p>Yth. ${vendor.company_name},</p>
          <p>Ini pengingat bahwa dokumen registrasi perusahaan Anda masih menunggu kelengkapan sesuai catatan verifikator sebelumnya. Mohon segera dilengkapi supaya proses verifikasi bisa dilanjutkan.</p>
          <p>Terima kasih.<br/>Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia</p>
        `,
      });
      emailTerkirim = mailResult.sent;
    }

    await pool.query(`
      INSERT INTO vendor_followups (vendor_id, status, jenis, catatan, pihak, created_by, email_tujuan, email_terkirim, email_terkirim_at)
      VALUES ($1, 'perlu_dilengkapi', 'reminder', 'Pengingat kelengkapan dokumen.', 'sistem', $2, $3, $4, $5)
    `, [req.params.id, req.user.id, vendor.email || null, emailTerkirim, emailTerkirim ? new Date() : null]);

    res.json({ success: true, message: emailTerkirim ? 'Pengingat terkirim ke penyedia.' : 'Pengingat dicatat, email belum terkirim.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/vendors/:id/documents — Upload dokumen legalitas ──
router.post('/:id/documents', ownVendorDataOnly, upload.single('document'), async (req, res) => {
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
router.post('/:id/experiences', ownVendorDataOnly, async (req, res) => {
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
router.get('/:id/bidang-usaha', ownVendorDataOnly, async (req, res) => {
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

router.post('/:id/bidang-usaha', ownVendorDataOnly, async (req, res) => {
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

router.delete('/:id/bidang-usaha/:linkId', ownVendorDataOnly, async (req, res) => {
  try {
    await pool.query('DELETE FROM vendor_bidang_usaha WHERE id = $1 AND vendor_id = $2', [req.params.linkId, req.params.id]);
    res.json({ success: true, message: 'Bidang usaha berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── REKENING KORAN (bukti mutasi bank per bulan, syarat kualifikasi keuangan) ──
router.get('/:id/rekening-koran', ownVendorDataOnly, async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM vendor_rekening_koran WHERE vendor_id = $1 ORDER BY tahun DESC, bulan DESC', [req.params.id]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/:id/rekening-koran', ownVendorDataOnly, upload.single('file'), async (req, res) => {
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

router.delete('/:id/rekening-koran/:rkId', ownVendorDataOnly, async (req, res) => {
  try {
    const result = await pool.query('DELETE FROM vendor_rekening_koran WHERE id = $1 AND vendor_id = $2 RETURNING id', [req.params.rkId, req.params.id]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Data tidak ditemukan.' });
    res.json({ success: true, message: 'Rekening koran berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
