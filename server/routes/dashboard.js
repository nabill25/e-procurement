const express = require('express');
const router  = express.Router();
const { pool } = require('../db');
const { requireRole } = require('../lib/authMiddleware');

// Khusus admin/PPK - rekap portofolio & efisiensi anggaran lintas organisasi tidak untuk
// ditampilkan ke vendor/pokja biasa (req.user sudah pasti terisi, requireAuth dipasang di
// index.js untuk seluruh /api/dashboard/*).
// manager_pengadaan ditambahkan: padanan role "MANAGER PENGADAAN" di sistem lama yang menu
// SATU-SATUNYA memang "Dashboard Manager" (dashboardhead) - persis dashboard rekap ini.
const requireLeadership = requireRole('admin', 'ppk', 'manager_pengadaan');

// ── GET /api/dashboard ──
// Catatan (ditemukan 2026-09-03): v_dashboard_stats cuma punya 5 kolom, tapi PokjaView di
// frontend (Dashboard.jsx) butuh 2 field lagi yang TIDAK PERNAH ada di view ini
// (total_tenders, dan field "Tender Selesai" yang sebelumnya salah dipetakan ke
// completed_contracts - itu sebenarnya field "Kontrak Selesai (BAST)" untuk AdminPPKView,
// beda makna). Ditambahkan di sini (bukan ubah struktur view) supaya tidak perlu migrasi baru.
router.get('/', async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM v_dashboard_stats');
    const base = result.rows[0] || {
      active_tenders: 0, verified_vendors: 0, completed_contracts: 0,
      total_budget_this_year: 0, pending_reviews: 0,
    };

    const extra = await pool.query(`
      SELECT
        (SELECT COUNT(*)::int FROM tenders WHERE EXTRACT(YEAR FROM created_at) = EXTRACT(YEAR FROM CURRENT_DATE)) AS total_tenders,
        (SELECT COUNT(*)::int FROM tenders WHERE status = 'selesai') AS completed_tenders
    `);
    const data = { ...base, ...extra.rows[0] };

    // Vendor yang login: tambahkan angka partisipasinya sendiri + status verifikasi (dipakai
    // VendorView di Dashboard.jsx - sebelumnya kartu jumlah tender hardcoded angka 0, dan teks
    // status verifikasi selalu bilang "terverifikasi" apapun status sungguhannya, tidak pernah
    // baca data asli. user dari useApp()/JWT tidak menyimpan status vendor, jadi diambil di sini).
    if (req.user?.role === 'vendor') {
      const joined = await pool.query(
        `SELECT COUNT(*)::int AS jumlah FROM tender_participants WHERE vendor_id = $1`,
        [req.user.id]
      );
      data.vendor_tenders_joined = joined.rows[0].jumlah;
      const statusRow = await pool.query('SELECT status FROM vendors WHERE user_id = $1', [req.user.id]);
      data.vendor_status = statusRow.rows[0]?.status || null;
    }

    res.json({ success: true, data });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/dashboard/analytics ──
// Data terstruktur untuk chart/grafik dashboard (beda dari /api/dashboard yang cuma angka
// ringkasan tunggal). Dipisah endpoint sendiri supaya tidak mengubah kontrak data lama yang
// sudah dipakai MetricCards.jsx.
router.get('/analytics', async (req, res) => {
  try {
    const [byStatus, monthlyTrend, byCategory, topVendors] = await Promise.all([
      // Sebaran tender per tahapan (untuk donut/bar chart status)
      pool.query(`
        SELECT status, COUNT(*)::int AS count
        FROM tenders
        GROUP BY status
        ORDER BY count DESC
      `),
      // Tren jumlah pengajuan per bulan, 6 bulan terakhir (untuk line/area chart)
      pool.query(`
        SELECT
          TO_CHAR(date_trunc('month', created_at), 'YYYY-MM') AS month,
          COUNT(*)::int AS count
        FROM procurement_requests
        WHERE created_at >= date_trunc('month', CURRENT_DATE) - INTERVAL '5 months'
        GROUP BY date_trunc('month', created_at)
        ORDER BY date_trunc('month', created_at)
      `),
      // Sebaran tender per kategori (untuk bar chart horizontal)
      pool.query(`
        SELECT COALESCE(category, 'Lainnya') AS category, COUNT(*)::int AS count
        FROM tenders
        GROUP BY category
        ORDER BY count DESC
        LIMIT 6
      `),
      // Vendor dengan skor kinerja tertinggi (untuk leaderboard kecil). Catatan (ditemukan
      // 2026-09-03): sebelumnya baca vendors.performance_score, kolom yang TIDAK PERNAH
      // ditulis di manapun di seluruh aplikasi (selalu 0.00) - rating asli tersimpan di
      // users.rating_avg/rating_count (diisi lewat POST /api/vendors/:id/rating, ditampilkan
      // juga di halaman Manajemen Vendor). Diperbaiki supaya baca sumber yang sungguhan.
      pool.query(`
        SELECT v.company_name, u.rating_avg AS performance_score, v.qualification_class
        FROM vendors v
        JOIN users u ON u.id = v.user_id
        WHERE v.status = 'terverifikasi' AND u.rating_count > 0
        ORDER BY u.rating_avg DESC
        LIMIT 5
      `),
    ]);

    res.json({
      success: true,
      data: {
        by_status: byStatus.rows,
        monthly_trend: monthlyTrend.rows,
        by_category: byCategory.rows,
        top_vendors: topVendors.rows.map(v => ({ ...v, rating: v.performance_score })),
      },
    });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/dashboard/executive-summary — Ringkasan portofolio pengadaan untuk pimpinan ──
// Padanan halaman executive_report.php di sistem lama (khusus role "kasi"/perencana): rekap
// lintas-tahap per paket (RUP -> HPS -> Kontrak) dalam satu tabel, supaya pimpinan bisa
// meninjau progres portofolio pengadaan tanpa perlu buka detail satu-satu.
router.get('/executive-summary', requireLeadership, async (req, res) => {
  try {
    const { page = 1, limit = 25, tahun, unit_kerja } = req.query;
    const offset = (parseInt(page) - 1) * parseInt(limit);
    const params = [];
    let where = 'WHERE 1=1';
    if (tahun) { params.push(parseInt(tahun)); where += ` AND EXTRACT(YEAR FROM pr.created_at) = $${params.length}`; }
    if (unit_kerja) { params.push(unit_kerja); where += ` AND pr.unit_kerja = $${params.length}`; }
    params.push(parseInt(limit), offset);

    const result = await pool.query(`
      SELECT
        pr.id AS request_id, pr.request_number, pr.title, pr.unit_kerja,
        pr.estimated_value AS pagu_rup, pr.status AS status_rup, pr.created_at AS tanggal_pengajuan,
        t.id AS tender_id, t.tender_number, t.hps, t.status AS status_tender,
        c.id AS contract_id, c.contract_number, c.contract_value, c.status AS status_kontrak
      FROM procurement_requests pr
      LEFT JOIN tenders t ON t.procurement_request_id = pr.id
      LEFT JOIN contracts c ON c.tender_id = t.id
      ${where}
      ORDER BY pr.created_at DESC
      LIMIT $${params.length - 1} OFFSET $${params.length}
    `, params);

    // params.slice(...) buang 2 elemen terakhir (limit, offset) - placeholder $1/$2 di `where`
    // sudah pas dengan urutan tahun/unit_kerja karena keduanya ditambah SEBELUM limit & offset.
    const countResult = await pool.query(`SELECT COUNT(*)::int AS total FROM procurement_requests pr ${where}`, params.slice(0, params.length - 2));

    res.json({ success: true, data: result.rows, pagination: { total: countResult.rows[0].total, page: parseInt(page), limit: parseInt(limit) } });
  } catch (err) {
    console.error('[GET /dashboard/executive-summary]', err);
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/dashboard/efficiency — Metrik efisiensi pengadaan (HPS vs nilai kontrak final) ──
// Padanan setHPSVal()/setHPSValPembelian() di dashboard_json.php sistem lama: selisih HPS
// dikurangi nilai kontrak final, per unit kerja, sebagai indikator kinerja pengadaan.
router.get('/efficiency', requireLeadership, async (req, res) => {
  try {
    const { tahun } = req.query;
    const params = [];
    let where = "WHERE t.hps IS NOT NULL AND c.contract_value IS NOT NULL AND c.status IN ('aktif','selesai')";
    if (tahun) { params.push(parseInt(tahun)); where += ` AND EXTRACT(YEAR FROM c.contract_date) = $${params.length}`; }

    const byUnit = await pool.query(`
      SELECT
        COALESCE(pr.unit_kerja, 'Tidak diketahui') AS unit_kerja,
        COUNT(*)::int AS jumlah_paket,
        SUM(t.hps)::numeric AS total_hps,
        SUM(c.contract_value)::numeric AS total_kontrak,
        SUM(t.hps - c.contract_value)::numeric AS total_efisiensi
      FROM contracts c
      JOIN tenders t ON c.tender_id = t.id
      LEFT JOIN procurement_requests pr ON t.procurement_request_id = pr.id
      ${where}
      GROUP BY pr.unit_kerja
      ORDER BY total_efisiensi DESC NULLS LAST
    `, params);

    const overall = await pool.query(`
      SELECT
        COUNT(*)::int AS jumlah_paket,
        SUM(t.hps)::numeric AS total_hps,
        SUM(c.contract_value)::numeric AS total_kontrak,
        SUM(t.hps - c.contract_value)::numeric AS total_efisiensi
      FROM contracts c JOIN tenders t ON c.tender_id = t.id
      ${where}
    `, params);

    res.json({ success: true, data: { overall: overall.rows[0], by_unit: byUnit.rows } });
  } catch (err) {
    console.error('[GET /dashboard/efficiency]', err);
    res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
