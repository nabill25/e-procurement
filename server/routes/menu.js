const express = require('express');
const router  = express.Router();
const { pool } = require('../db');
const { requireRole } = require('../lib/authMiddleware');
const requireAdmin = requireRole('admin');

// ── GET /api/menu/access-matrix — Semua menu beserta role yang boleh melihatnya (Admin) ──
// Didefinisikan sebelum /:role supaya path "access-matrix" tidak ketiban rute role.
router.get('/access-matrix', requireAdmin, async (req, res) => {
  try {
    const menus = await pool.query('SELECT * FROM menu_items WHERE is_active = true ORDER BY order_index ASC');
    const access = await pool.query('SELECT menu_id, role FROM menu_role_access');

    const data = menus.rows.map(menu => ({
      ...menu,
      roles: access.rows.filter(a => a.menu_id === menu.id).map(a => a.role),
    }));

    res.json({ success: true, data });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── PUT /api/menu/:menuId/access — Ubah daftar role yang boleh lihat satu menu (Admin) ──
router.put('/:menuId/access', requireAdmin, async (req, res) => {
  try {
    const { roles } = req.body; // contoh: ["admin", "ppk"]
    if (!Array.isArray(roles)) {
      return res.status(400).json({ success: false, message: 'roles harus berupa array.' });
    }

    const client = await pool.connect();
    try {
      await client.query('BEGIN');
      await client.query('DELETE FROM menu_role_access WHERE menu_id = $1', [req.params.menuId]);
      for (const role of roles) {
        await client.query('INSERT INTO menu_role_access (menu_id, role) VALUES ($1, $2)', [req.params.menuId, role]);
      }
      await client.query('COMMIT');
    } catch (e) {
      await client.query('ROLLBACK');
      throw e;
    } finally {
      client.release();
    }

    await pool.query(
      `INSERT INTO audit_logs (action, entity_type, description, is_success) VALUES ('UPDATE', 'MenuAccess', $1, true)`,
      [`Hak akses menu diubah untuk menu ID ${req.params.menuId}: [${roles.join(', ')}]`]
    );

    res.json({ success: true, message: 'Hak akses menu berhasil diperbarui.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/menu/:role — Daftar menu yang boleh dilihat satu role (dipakai Sidebar) ──
router.get('/:role', async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT m.menu_key, m.label, m.icon, m.order_index
      FROM menu_items m
      JOIN menu_role_access a ON a.menu_id = m.id
      WHERE a.role = $1 AND m.is_active = true
      ORDER BY m.order_index ASC
    `, [req.params.role]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
