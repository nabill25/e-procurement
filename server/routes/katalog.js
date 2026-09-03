const express = require('express');
const router = express.Router();
const { pool } = require('../db');
const { createUpload, handleUploadError } = require('../lib/upload');
const { requireAuth } = require('../lib/authMiddleware');

// ── Konfigurasi Multer ──
const upload = createUpload('katalog');

// ── KATEGORI (berjenjang, ditaruh sebelum /:id supaya tidak ketiban rute item) ──
router.get('/categories/tree', requireAuth, async (req, res) => {
  try {
    const { search } = req.query;
    let sql = 'SELECT * FROM katalog_categories';
    const params = [];
    if (search) { sql += ' WHERE nama ILIKE $1'; params.push(`%${search}%`); }
    sql += ' ORDER BY nama ASC';
    const result = await pool.query(sql, params);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/categories', requireAuth, async (req, res) => {
  try {
    const { nama, kode, parent_id } = req.body;
    if (!nama) return res.status(400).json({ success: false, message: 'nama wajib diisi.' });
    const result = await pool.query(`
      INSERT INTO katalog_categories (nama, kode, parent_id) VALUES ($1, $2, $3) RETURNING *
    `, [nama, kode || null, parent_id || null]);
    res.status(201).json({ success: true, message: 'Kategori berhasil ditambahkan.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/categories/:categoryId', requireAuth, async (req, res) => {
  try {
    await pool.query('DELETE FROM katalog_categories WHERE id = $1', [req.params.categoryId]);
    res.json({ success: true, message: 'Kategori berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── LAPORAN/KOMPLAIN (ditaruh sebelum /:id) - cuma POST /reports yang publik ──
router.get('/reports', requireAuth, async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT r.*, k.item_name AS nama_produk
      FROM katalog_reports r JOIN katalog_items k ON r.katalog_id = k.id
      ORDER BY r.created_at DESC
    `);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/reports', async (req, res) => {
  try {
    const { katalog_id, nama, email, telepon, alasan, jenis_laporan } = req.body;
    if (!katalog_id || !alasan) return res.status(400).json({ success: false, message: 'katalog_id dan alasan wajib diisi.' });
    const result = await pool.query(`
      INSERT INTO katalog_reports (katalog_id, nama, email, telepon, alasan, jenis_laporan)
      VALUES ($1, $2, $3, $4, $5, $6) RETURNING *
    `, [katalog_id, nama || null, email || null, telepon || null, alasan, jenis_laporan || null]);
    res.status(201).json({ success: true, message: 'Laporan berhasil dikirim.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.patch('/reports/:reportId', requireAuth, async (req, res) => {
  try {
    const { status } = req.body;
    const result = await pool.query('UPDATE katalog_reports SET status = $1 WHERE id = $2 RETURNING *', [status, req.params.reportId]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Laporan tidak ditemukan.' });
    res.json({ success: true, data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── KERANJANG & PESANAN (per pengajuan/procurement_request, ditaruh sebelum /:id) ──
const CART_STATUS_LABELS = {
  0: 'Proses Pemilihan', 1: 'Negosiasi', 2: 'Penyedia Setuju',
  3: 'Surat Pesanan', 4: 'Proses', 5: 'Dikirim', 6: 'Diterima',
};

function generateInvoice(procurementRequestId) {
  const seed = 'abcdefghijklmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ123456789';
  let rand = '';
  for (let i = 0; i < 4; i++) rand += seed[Math.floor(Math.random() * seed.length)];
  const date = new Date().toISOString().slice(2, 10).replace(/-/g, '');
  return `INV-PR/${date}/${rand}${String(procurementRequestId).slice(0, 8)}`;
}

router.get('/cart/:procurementRequestId', requireAuth, async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT c.*, v.company_name AS vendor_name
      FROM katalog_cart_items c LEFT JOIN vendors v ON c.vendor_id = v.user_id
      WHERE c.procurement_request_id = $1 ORDER BY c.created_at ASC
    `, [req.params.procurementRequestId]);
    res.json({ success: true, data: result.rows.map(r => ({ ...r, status_label: CART_STATUS_LABELS[r.status] })) });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/cart', requireAuth, async (req, res) => {
  try {
    const { procurement_request_id, katalog_id, created_by } = req.body;
    if (!procurement_request_id || !katalog_id) {
      return res.status(400).json({ success: false, message: 'procurement_request_id dan katalog_id diperlukan.' });
    }
    const katalog = await pool.query('SELECT * FROM katalog_items WHERE id = $1', [katalog_id]);
    if (!katalog.rows.length) return res.status(404).json({ success: false, message: 'Produk tidak ditemukan.' });
    const item = katalog.rows[0];

    const existing = await pool.query(
      'SELECT * FROM katalog_cart_items WHERE procurement_request_id = $1 AND katalog_id = $2',
      [procurement_request_id, katalog_id]
    );
    if (existing.rows.length) {
      const result = await pool.query(
        'UPDATE katalog_cart_items SET qty = qty + 1, updated_at = CURRENT_TIMESTAMP WHERE id = $1 RETURNING *',
        [existing.rows[0].id]
      );
      return res.json({ success: true, message: `Katalog ${item.item_name} berhasil ditambah.`, data: result.rows[0] });
    }
    const result = await pool.query(`
      INSERT INTO katalog_cart_items (procurement_request_id, katalog_id, vendor_id, nama_produk, merek, model_type, harga, qty, status, created_by)
      VALUES ($1, $2, $3, $4, $5, $6, $7, 1, 0, $8) RETURNING *
    `, [procurement_request_id, katalog_id, item.vendor_id, item.item_name, item.brand || null, item.model_type || null, item.price, created_by || null]);
    res.status(201).json({ success: true, message: `Katalog ${item.item_name} berhasil ditambah.`, data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.patch('/cart/:cartItemId/qty', requireAuth, async (req, res) => {
  try {
    const { qty } = req.body;
    if (!qty || qty < 1) return res.status(400).json({ success: false, message: 'qty harus lebih dari 0.' });
    const result = await pool.query(
      'UPDATE katalog_cart_items SET qty = $1, updated_at = CURRENT_TIMESTAMP WHERE id = $2 RETURNING *',
      [qty, req.params.cartItemId]
    );
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Item keranjang tidak ditemukan.' });
    res.json({ success: true, data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/cart/:cartItemId', requireAuth, async (req, res) => {
  try {
    await pool.query('DELETE FROM katalog_cart_items WHERE id = $1', [req.params.cartItemId]);
    res.json({ success: true, message: 'Item berhasil dihapus dari keranjang.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// Kirim harga negosiasi ke vendor + update ongkos kirim sekaligus (mengikuti cartupdateNego() asli).
router.post('/cart/negotiate', requireAuth, async (req, res) => {
  const client = await pool.connect();
  try {
    const { procurement_request_id, ongkos_kirim, items, updated_by } = req.body;
    if (!procurement_request_id || !Array.isArray(items)) {
      return res.status(400).json({ success: false, message: 'procurement_request_id dan items diperlukan.' });
    }
    await client.query('BEGIN');
    if (ongkos_kirim != null) {
      await client.query(`
        INSERT INTO katalog_logistik (procurement_request_id, ongkos_kirim, updated_by)
        VALUES ($1, $2, $3)
        ON CONFLICT (procurement_request_id) DO UPDATE SET ongkos_kirim = EXCLUDED.ongkos_kirim, updated_by = EXCLUDED.updated_by, updated_at = CURRENT_TIMESTAMP
      `, [procurement_request_id, ongkos_kirim, updated_by || null]);
    }
    for (const it of items) {
      await client.query(
        'UPDATE katalog_cart_items SET harga_nego = $1, updated_at = CURRENT_TIMESTAMP WHERE id = $2',
        [it.harga_nego, it.cart_item_id]
      );
    }
    await client.query('COMMIT');
    res.json({ success: true, message: 'Harga negosiasi berhasil dikirim ke Penyedia.' });
  } catch (err) {
    await client.query('ROLLBACK');
    res.status(500).json({ success: false, message: err.message });
  } finally {
    client.release();
  }
});

// Alur status pesanan (mengikuti statusupdate() asli persis, termasuk transisi status & invoice).
router.patch('/cart/:cartItemId/status', requireAuth, async (req, res) => {
  try {
    const { status } = req.body;
    const STATUS_TRANSITIONS = { '10': '0', '0': '1', '1': '2', '2': '3', '3': '4', '5': '6' };
    const nextStatus = STATUS_TRANSITIONS[String(status)];
    if (nextStatus === undefined) return res.status(400).json({ success: false, message: 'Transisi status tidak valid.' });

    const cartItem = await pool.query('SELECT * FROM katalog_cart_items WHERE id = $1', [req.params.cartItemId]);
    if (!cartItem.rows.length) return res.status(404).json({ success: false, message: 'Item keranjang tidak ditemukan.' });

    let noInvoice = cartItem.rows[0].no_invoice;
    if (status === '0' || status === '1' || status == 0 || status == 1) {
      noInvoice = generateInvoice(cartItem.rows[0].procurement_request_id);
    }

    const result = await pool.query(`
      UPDATE katalog_cart_items SET status = $1, status_awal = $2, no_invoice = COALESCE($3, no_invoice), updated_at = CURRENT_TIMESTAMP
      WHERE id = $4 RETURNING *
    `, [nextStatus, status, noInvoice, req.params.cartItemId]);
    res.json({ success: true, message: CART_STATUS_LABELS[nextStatus] || 'Status berhasil diperbarui.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.get('/logistik/:procurementRequestId', requireAuth, async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM katalog_logistik WHERE procurement_request_id = $1', [req.params.procurementRequestId]);
    res.json({ success: true, data: result.rows[0] || null });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── COMPARE (per sesi browser) ──
router.get('/compare/:sessionId', requireAuth, async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT c.*, k.item_name, k.price, k.brand, k.image_url
      FROM katalog_compare c JOIN katalog_items k ON c.katalog_id = k.id
      WHERE c.session_id = $1
    `, [req.params.sessionId]);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.post('/compare', requireAuth, async (req, res) => {
  try {
    const { katalog_id, session_id } = req.body;
    if (!katalog_id || !session_id) return res.status(400).json({ success: false, message: 'katalog_id dan session_id diperlukan.' });
    const count = await pool.query('SELECT COUNT(*) FROM katalog_compare WHERE session_id = $1', [session_id]);
    if (parseInt(count.rows[0].count) >= 3) {
      return res.status(400).json({ success: false, message: `Bandingkan sudah ${count.rows[0].count} produk, maksimal 3.` });
    }
    const result = await pool.query(`
      INSERT INTO katalog_compare (katalog_id, session_id) VALUES ($1, $2)
      ON CONFLICT (katalog_id, session_id) DO NOTHING RETURNING *
    `, [katalog_id, session_id]);
    res.status(201).json({ success: true, data: result.rows[0] || null });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/compare', requireAuth, async (req, res) => {
  try {
    const { katalog_id, session_id } = req.body;
    await pool.query('DELETE FROM katalog_compare WHERE katalog_id = $1 AND session_id = $2', [katalog_id, session_id]);
    res.json({ success: true, message: 'Produk berhasil dihapus dari perbandingan.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/katalog ──
router.get('/', requireAuth, async (req, res) => {
  try {
    const { search, vendor_id, category_id, limit = 50 } = req.query;
    let sql = `
      SELECT k.*, v.company_name
      FROM katalog_items k
      JOIN vendors v ON k.vendor_id = v.user_id
      WHERE 1=1
    `;
    const params = [];
    let idx = 1;

    if (search) { sql += ` AND k.item_name ILIKE $${idx++}`; params.push(`%${search}%`); }
    if (vendor_id) { sql += ` AND k.vendor_id = $${idx++}`; params.push(vendor_id); }
    if (category_id) {
      sql += ` AND EXISTS (SELECT 1 FROM katalog_item_categories ic WHERE ic.katalog_id = k.id AND ic.category_id = $${idx++})`;
      params.push(category_id);
    }
    sql += ` ORDER BY k.created_at DESC LIMIT $${idx}`;
    params.push(parseInt(limit));

    const result = await pool.query(sql, params);
    res.json({ success: true, data: result.rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET /api/katalog/:id ──
router.get('/:id', requireAuth, async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT k.*, v.company_name
      FROM katalog_items k
      JOIN vendors v ON k.vendor_id = v.user_id
      WHERE k.id = $1
    `, [req.params.id]);

    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Item tidak ditemukan.' });

    const [photos, attachments, categories, priceHistory] = await Promise.all([
      pool.query('SELECT * FROM katalog_photos WHERE katalog_id = $1 ORDER BY created_at ASC', [req.params.id]),
      pool.query('SELECT * FROM katalog_attachments WHERE katalog_id = $1 ORDER BY created_at ASC', [req.params.id]),
      pool.query(`
        SELECT c.* FROM katalog_categories c
        JOIN katalog_item_categories ic ON ic.category_id = c.id
        WHERE ic.katalog_id = $1
      `, [req.params.id]),
      pool.query('SELECT * FROM katalog_price_history WHERE katalog_id = $1 ORDER BY created_at DESC', [req.params.id]),
    ]);

    res.json({
      success: true,
      data: { ...result.rows[0], photos: photos.rows, attachments: attachments.rows, categories: categories.rows, price_history: priceHistory.rows },
    });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST /api/katalog — Tambah item katalog (oleh vendor) ──
router.post('/', requireAuth, async (req, res) => {
  const client = await pool.connect();
  try {
    const {
      vendor_id, item_name, description, price, unit, image_url,
      item_code, brand, model_type, diameter, panjang, lebar, tinggi, unit_pengukuran,
      tkdn_persen, berlaku_sampai, jenis_produk, lama_garansi, lama_garansi_satuan,
      jumlah_stock, jumlah_stock_ready, kemasan, status, keterangan_tambahan, category_ids,
    } = req.body;

    if (!vendor_id || !item_name || !price) {
      return res.status(400).json({ success: false, message: 'vendor_id, item_name, price wajib diisi.' });
    }

    await client.query('BEGIN');
    const result = await client.query(`
      INSERT INTO katalog_items
        (vendor_id, item_name, description, price, unit, image_url, item_code, brand, model_type,
         diameter, panjang, lebar, tinggi, unit_pengukuran, tkdn_persen, berlaku_sampai, jenis_produk,
         lama_garansi, lama_garansi_satuan, jumlah_stock, jumlah_stock_ready, kemasan, status, keterangan_tambahan)
      VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19,$20,$21,$22,$23,$24)
      RETURNING *
    `, [vendor_id, item_name, description || null, price, unit || 'Pcs', image_url || null, item_code || null, brand || null,
        model_type || null, diameter || 0, panjang || 0, lebar || 0, tinggi || 0, unit_pengukuran || null, tkdn_persen || null,
        berlaku_sampai || null, jenis_produk || null, lama_garansi || null, lama_garansi_satuan || null, jumlah_stock || 0,
        jumlah_stock_ready || null, kemasan || null, status || 'aktif', keterangan_tambahan || null]);

    const item = result.rows[0];
    if (Array.isArray(category_ids)) {
      for (const catId of category_ids) {
        await client.query('INSERT INTO katalog_item_categories (katalog_id, category_id) VALUES ($1, $2) ON CONFLICT DO NOTHING', [item.id, catId]);
      }
    }
    await client.query('COMMIT');
    res.status(201).json({ success: true, message: 'Item berhasil ditambahkan ke katalog', data: item });
  } catch (err) {
    await client.query('ROLLBACK');
    res.status(500).json({ success: false, message: err.message });
  } finally {
    client.release();
  }
});

// ── PUT /api/katalog/:id — Ubah item (auto-catat riwayat harga kalau harga berubah) ──
router.put('/:id', requireAuth, async (req, res) => {
  const client = await pool.connect();
  try {
    const {
      item_name, description, price, unit, image_url,
      item_code, brand, model_type, diameter, panjang, lebar, tinggi, unit_pengukuran,
      tkdn_persen, berlaku_sampai, jenis_produk, lama_garansi, lama_garansi_satuan,
      jumlah_stock, jumlah_stock_ready, kemasan, status, keterangan_tambahan, category_ids, created_by,
    } = req.body;

    const existing = await client.query('SELECT price FROM katalog_items WHERE id = $1', [req.params.id]);
    if (!existing.rows.length) return res.status(404).json({ success: false, message: 'Item tidak ditemukan.' });
    const oldPrice = Number(existing.rows[0].price);

    await client.query('BEGIN');
    const result = await client.query(`
      UPDATE katalog_items SET
        item_name = COALESCE($1, item_name), description = COALESCE($2, description), price = COALESCE($3, price),
        unit = COALESCE($4, unit), image_url = COALESCE($5, image_url), item_code = COALESCE($6, item_code),
        brand = COALESCE($7, brand), model_type = COALESCE($8, model_type), diameter = COALESCE($9, diameter),
        panjang = COALESCE($10, panjang), lebar = COALESCE($11, lebar), tinggi = COALESCE($12, tinggi),
        unit_pengukuran = COALESCE($13, unit_pengukuran), tkdn_persen = COALESCE($14, tkdn_persen),
        berlaku_sampai = COALESCE($15, berlaku_sampai), jenis_produk = COALESCE($16, jenis_produk),
        lama_garansi = COALESCE($17, lama_garansi), lama_garansi_satuan = COALESCE($18, lama_garansi_satuan),
        jumlah_stock = COALESCE($19, jumlah_stock), jumlah_stock_ready = COALESCE($20, jumlah_stock_ready),
        kemasan = COALESCE($21, kemasan), status = COALESCE($22, status), keterangan_tambahan = COALESCE($23, keterangan_tambahan),
        updated_at = CURRENT_TIMESTAMP
      WHERE id = $24 RETURNING *
    `, [item_name, description, price, unit, image_url, item_code, brand, model_type, diameter, panjang, lebar, tinggi,
        unit_pengukuran, tkdn_persen, berlaku_sampai, jenis_produk, lama_garansi, lama_garansi_satuan, jumlah_stock,
        jumlah_stock_ready, kemasan, status, keterangan_tambahan, req.params.id]);

    if (price != null && Number(price) !== oldPrice) {
      await client.query(`
        INSERT INTO katalog_price_history (katalog_id, harga_lama, harga_baru, created_by) VALUES ($1, $2, $3, $4)
      `, [req.params.id, oldPrice, price, created_by || null]);
    }

    if (Array.isArray(category_ids)) {
      await client.query('DELETE FROM katalog_item_categories WHERE katalog_id = $1', [req.params.id]);
      for (const catId of category_ids) {
        await client.query('INSERT INTO katalog_item_categories (katalog_id, category_id) VALUES ($1, $2) ON CONFLICT DO NOTHING', [req.params.id, catId]);
      }
    }
    await client.query('COMMIT');
    res.json({ success: true, message: 'Item berhasil diperbarui.', data: result.rows[0] });
  } catch (err) {
    await client.query('ROLLBACK');
    res.status(500).json({ success: false, message: err.message });
  } finally {
    client.release();
  }
});

// ── DELETE /api/katalog/:id ──
router.delete('/:id', requireAuth, async (req, res) => {
  try {
    const result = await pool.query('DELETE FROM katalog_items WHERE id = $1 RETURNING id', [req.params.id]);
    if (!result.rows.length) return res.status(404).json({ success: false, message: 'Item tidak ditemukan.' });
    res.json({ success: true, message: 'Item berhasil dihapus dari katalog.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── FOTO PRODUK ──
router.post('/:id/photos', requireAuth, upload.single('file'), async (req, res) => {
  try {
    if (!req.file) return res.status(400).json({ success: false, message: 'File diperlukan.' });
    const { created_by } = req.body;
    const result = await pool.query(`
      INSERT INTO katalog_photos (katalog_id, file_path, file_size, created_by) VALUES ($1, $2, $3, $4) RETURNING *
    `, [req.params.id, req.file.filename, req.file.size, created_by || null]);
    res.status(201).json({ success: true, message: 'Foto berhasil diunggah.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/:id/photos/:photoId', requireAuth, async (req, res) => {
  try {
    await pool.query('DELETE FROM katalog_photos WHERE id = $1 AND katalog_id = $2', [req.params.photoId, req.params.id]);
    res.json({ success: true, message: 'Foto berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── LAMPIRAN PRODUK ──
router.post('/:id/attachments', requireAuth, upload.single('file'), async (req, res) => {
  try {
    if (!req.file) return res.status(400).json({ success: false, message: 'File diperlukan.' });
    const { nama, created_by } = req.body;
    const result = await pool.query(`
      INSERT INTO katalog_attachments (katalog_id, nama, file_path, file_size, created_by) VALUES ($1, $2, $3, $4, $5) RETURNING *
    `, [req.params.id, nama || req.file.originalname, req.file.filename, req.file.size, created_by || null]);
    res.status(201).json({ success: true, message: 'Lampiran berhasil diunggah.', data: result.rows[0] });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

router.delete('/:id/attachments/:attachmentId', requireAuth, async (req, res) => {
  try {
    await pool.query('DELETE FROM katalog_attachments WHERE id = $1 AND katalog_id = $2', [req.params.attachmentId, req.params.id]);
    res.json({ success: true, message: 'Lampiran berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
