const express = require('express');
const router  = express.Router();
const { pool } = require('../db');

// ── GET /api/dashboard ──
router.get('/', async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM v_dashboard_stats');
    const rows = result.rows;
    if (!rows.length) {
      return res.json({
        success: true,
        data: {
          active_tenders: 0,
          verified_vendors: 0,
          completed_contracts: 0,
          total_budget_this_year: 0,
          pending_reviews: 0,
        }
      });
    }
    res.json({ success: true, data: rows[0] });
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
      // Vendor dengan skor kinerja tertinggi (untuk leaderboard kecil)
      pool.query(`
        SELECT company_name, performance_score, qualification_class
        FROM vendors
        WHERE status = 'terverifikasi' AND performance_score IS NOT NULL
        ORDER BY performance_score DESC
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

module.exports = router;
