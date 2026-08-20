const express = require('express');
const router = express.Router();
const { pool } = require('../db');

// ── GET /api/purchasing ──
// Mendapatkan daftar order berdasarkan buyer atau vendor
router.get('/', async (req, res) => {
  try {
    const { buyer_id, vendor_id } = req.query;
    let sql = `
      SELECT p.*, v.company_name AS vendor_name, u.name AS buyer_name
      FROM purchasing_orders p
      LEFT JOIN vendors v ON p.vendor_id = v.user_id
      LEFT JOIN users u ON p.buyer_id = u.id
      WHERE 1=1
    `;
    const params = [];
    let idx = 1;

    if (buyer_id) {
      sql += ` AND p.buyer_id = $${idx++}`;
      params.push(buyer_id);
    }
    if (vendor_id) {
      sql += ` AND p.vendor_id = $${idx++}`;
      params.push(vendor_id);
    }
    sql += ` ORDER BY p.created_at DESC`;

    const result = await pool.query(sql, params);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/purchasing/:id ──
// Mengambil detail order dan itemnya
router.get('/:id', async (req, res) => {
  try {
    const orderResult = await pool.query(`
      SELECT p.*, v.company_name AS vendor_name, u.name AS buyer_name
      FROM purchasing_orders p
      LEFT JOIN vendors v ON p.vendor_id = v.user_id
      LEFT JOIN users u ON p.buyer_id = u.id
      WHERE p.id = $1
    `, [req.params.id]);

    if (!orderResult.rows.length) return res.status(404).json({ success: false, message: 'Order tidak ditemukan.' });
    
    const itemsResult = await pool.query(`
      SELECT po.*, k.item_name, k.unit, k.image_url
      FROM purchasing_order_items po
      JOIN katalog_items k ON po.item_id = k.id
      WHERE po.order_id = $1
    `, [req.params.id]);

    res.json({ 
      success: true, 
      data: {
        ...orderResult.rows[0],
        items: itemsResult.rows
      }
    });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/purchasing ──
// Membuat pesanan e-purchasing baru
router.post('/', async (req, res) => {
  const client = await pool.connect();
  try {
    const { buyer_id, vendor_id, total_amount, delivery_address, notes, items } = req.body;
    
    if (!buyer_id || !vendor_id || !items || items.length === 0) {
      return res.status(400).json({ success: false, message: 'Data pesanan tidak lengkap.' });
    }

    await client.query('BEGIN');

    const orderRes = await client.query(`
      INSERT INTO purchasing_orders (buyer_id, vendor_id, total_amount, delivery_address, notes)
      VALUES ($1, $2, $3, $4, $5)
      RETURNING *
    `, [buyer_id, vendor_id, total_amount, delivery_address, notes]);

    const orderId = orderRes.rows[0].id;

    for (const item of items) {
      await client.query(`
        INSERT INTO purchasing_order_items (order_id, item_id, quantity, price_at_purchase)
        VALUES ($1, $2, $3, $4)
      `, [orderId, item.id, item.quantity, item.price]);
    }

    // Log the action
    const buyerRes = await client.query('SELECT name FROM users WHERE id = $1', [buyer_id]);
    const buyerName = buyerRes.rows.length ? buyerRes.rows[0].name : 'PPK';
    await client.query(`
      INSERT INTO audit_logs (action, entity_type, description, is_success) 
      VALUES ('CREATE', 'Purchasing', $1, true)
    `, [`Pesanan E-Purchasing baru dibuat oleh ${buyerName} senilai Rp ${total_amount}`]);

    await client.query('COMMIT');
    res.status(201).json({ success: true, message: 'Pesanan berhasil dibuat.', data: orderRes.rows[0] });
  } catch (err) {
    await client.query('ROLLBACK');
    res.status(500).json({ success: false, message: err.message });
  } finally {
    client.release();
  }
});

// ── PATCH /api/purchasing/:id/status ──
// Vendor update status order (approve, complete, reject)
router.patch('/:id/status', async (req, res) => {
  try {
    const { status } = req.body;
    const orderRes = await pool.query('UPDATE purchasing_orders SET status = $1, updated_at = CURRENT_TIMESTAMP WHERE id = $2 RETURNING *', [status, req.params.id]);
    
    if (!orderRes.rows.length) return res.status(404).json({ success: false, message: 'Order tidak ditemukan.' });
    
    await pool.query(`
      INSERT INTO audit_logs (action, entity_type, description, is_success) 
      VALUES ('UPDATE', 'Purchasing', $1, true)
    `, [`Status E-Purchasing Order ID ${req.params.id} diubah ke ${status}`]);

    res.json({ success: true, message: `Status order berhasil diubah menjadi ${status}` });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
