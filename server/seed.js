const { pool } = require('./db');

async function seed() {
  console.log('Starting seed...');
  const conn = await pool.getConnection();
  try {
    await conn.beginTransaction();

    // 1. Seed Vendors
    console.log('Seeding Vendors...');
    const vendors = [
      { id: 'v1', company_name: 'PT Jaya Abadi', npwp: '01.234.567.8-901.000', company_type: 'Barang', city: 'Jakarta Selatan', performance_score: 4.5, status: 'terverifikasi' },
      { id: 'v2', company_name: 'CV Makmur Sejahtera', npwp: '02.345.678.9-012.000', company_type: 'Jasa Konsultansi', city: 'Depok', performance_score: 4.8, status: 'terverifikasi' },
      { id: 'v3', company_name: 'PT Teknologi Inovasi', npwp: '03.456.789.0-123.000', company_type: 'Jasa Lainnya', city: 'Bandung', performance_score: 3.5, status: 'pending' },
      { id: 'v4', company_name: 'Firma Hukum & Partner', npwp: '04.567.890.1-234.000', company_type: 'Jasa Konsultansi', city: 'Jakarta Pusat', performance_score: 0, status: 'pending' },
      { id: 'v5', company_name: 'PT Bangun Konstruksi', npwp: '05.678.901.2-345.000', company_type: 'Pekerjaan Konstruksi', city: 'Bekasi', performance_score: 2.1, status: 'ditangguhkan' },
    ];

    for (const v of vendors) {
      await conn.query(`
        INSERT IGNORE INTO vendors (id, company_name, npwp, company_type, city, performance_score, status)
        VALUES (UUID(), ?, ?, ?, ?, ?, ?)
      `, [v.company_name, v.npwp, v.company_type, v.city, v.performance_score, v.status]);
    }

    // 2. We need an existing user as requester for requests
    const [users] = await conn.query('SELECT id FROM users LIMIT 1');
    const adminId = users.length > 0 ? users[0].id : null;

    if (adminId) {
      // 3. Seed Pengajuan
      console.log('Seeding Pengajuan & Tenders...');
      const reqId1 = 'req-dummy-1';
      await conn.query(`
        INSERT IGNORE INTO procurement_requests (id, request_number, title, unit_kerja, category, estimated_value, budget_source, fiscal_year, status, requester_id)
        VALUES (?, 'PENGAJUAN/2025/090', 'Pengadaan PC Desktop Lab Komputer', 'Fasilkom UI', 'Barang', 250000000.00, 'DIPA', 2025, 'disetujui', ?)
      `, [reqId1, adminId]);

      const reqId2 = 'req-dummy-2';
      await conn.query(`
        INSERT IGNORE INTO procurement_requests (id, request_number, title, unit_kerja, category, estimated_value, budget_source, fiscal_year, status, requester_id)
        VALUES (?, 'PENGAJUAN/2025/091', 'Jasa Pemeliharaan AC Rektorat', 'Direktorat Fasilitas', 'Jasa Lainnya', 75000000.00, 'DIPA', 2025, 'proses_review', ?)
      `, [reqId2, adminId]);

      // 4. Seed Tenders
      await conn.query(`
        INSERT IGNORE INTO tenders (id, procurement_request_id, tender_number, title, category, method, status, pagu_anggaran, submission_deadline)
        VALUES (UUID(), ?, 'TENDER/2025/090', 'Pengadaan PC Desktop Lab Komputer', 'Barang', 'tender', 'tender_buka', 250000000.00, DATE_ADD(NOW(), INTERVAL 5 DAY))
      `, [reqId1]);
    }

    await conn.commit();
    console.log('Seed completed successfully!');
  } catch (err) {
    await conn.rollback();
    console.error('Seed error:', err);
  } finally {
    conn.release();
    process.exit();
  }
}

seed();
