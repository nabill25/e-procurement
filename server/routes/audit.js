const express = require('express');
const router  = express.Router();
const { pool } = require('../db');

// ── GET /api/audit ──
router.get('/', async (req, res) => {
  try {
    const { action, user_id, page = 1, limit = 50 } = req.query;
    const offset = (parseInt(page) - 1) * parseInt(limit);
    
    let sql = `
      SELECT a.*, u.full_name AS user_name 
      FROM audit_logs a
      LEFT JOIN users u ON a.user_id = u.id
      WHERE 1=1
    `;
    const params = [];
    let paramIndex = 1;
    
    if (action)  { sql += ` AND a.action = $${paramIndex++}`;  params.push(action); }
    if (user_id) { sql += ` AND a.user_id = $${paramIndex++}`; params.push(user_id); }
    
    sql += ` ORDER BY a.created_at DESC LIMIT $${paramIndex++} OFFSET $${paramIndex++}`;
    params.push(parseInt(limit), offset);
    
    const result = await pool.query(sql, params);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
