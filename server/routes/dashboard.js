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

module.exports = router;
