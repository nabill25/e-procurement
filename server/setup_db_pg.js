const { pool } = require('./db');
const bcrypt = require('bcrypt');

async function setup() {
  console.log('Membuat tabel dan dummy data PostgreSQL...');
  try {
    await pool.query(`
      CREATE TABLE IF NOT EXISTS users (
        id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
        username VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        role VARCHAR(50) NOT NULL,
        role_label VARCHAR(100),
        status VARCHAR(20) DEFAULT 'aktif',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );

      CREATE TABLE IF NOT EXISTS vendors (
        id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
        user_id UUID REFERENCES users(id) ON DELETE CASCADE,
        company_name VARCHAR(255) NOT NULL,
        npwp VARCHAR(50) UNIQUE,
        company_type VARCHAR(100),
        city VARCHAR(100),
        performance_score DECIMAL(3,2) DEFAULT 0,
        status VARCHAR(50) DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );

      CREATE TABLE IF NOT EXISTS procurement_requests (
        id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
        request_number VARCHAR(100) UNIQUE NOT NULL,
        title VARCHAR(255) NOT NULL,
        unit_kerja VARCHAR(100),
        category VARCHAR(100),
        estimated_value DECIMAL(15,2),
        budget_source VARCHAR(50),
        fiscal_year INT,
        status VARCHAR(50) DEFAULT 'draft',
        requester_id UUID REFERENCES users(id) ON DELETE SET NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );

      CREATE TABLE IF NOT EXISTS tenders (
        id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
        procurement_request_id UUID REFERENCES procurement_requests(id) ON DELETE SET NULL,
        tender_number VARCHAR(100) UNIQUE NOT NULL,
        title VARCHAR(255) NOT NULL,
        category VARCHAR(100),
        method VARCHAR(50) DEFAULT 'tender',
        status VARCHAR(50) DEFAULT 'draft',
        pagu_anggaran DECIMAL(15,2),
        hps DECIMAL(15,2),
        ppk_id UUID REFERENCES users(id) ON DELETE SET NULL,
        pokja_lead_id UUID REFERENCES users(id) ON DELETE SET NULL,
        work_location VARCHAR(255),
        description TEXT,
        submission_deadline TIMESTAMP,
        winner_announcement TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );

      CREATE TABLE IF NOT EXISTS tender_participants (
        id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
        tender_id UUID REFERENCES tenders(id) ON DELETE CASCADE,
        vendor_id UUID REFERENCES users(id) ON DELETE CASCADE,
        status VARCHAR(50) DEFAULT 'registered',
        bid_price DECIMAL(15,2),
        registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(tender_id, vendor_id)
      );

      CREATE TABLE IF NOT EXISTS audit_logs (
        id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
        user_id UUID REFERENCES users(id) ON DELETE SET NULL,
        action VARCHAR(255) NOT NULL,
        entity_type VARCHAR(100),
        entity_id UUID,
        description VARCHAR(500),
        ip_address VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );
    `);

    console.log('Tabel berhasil dibuat.');

    // Cek apakah user admin sudah ada
    const res = await pool.query("SELECT id FROM users WHERE username = 'admin'");
    if (res.rows.length === 0) {
      console.log('Seeding dummy data...');
      
      const pAdmin = await bcrypt.hash('admin123', 10);
      const pPpk = await bcrypt.hash('ppk123', 10);
      const pPokja = await bcrypt.hash('pokja123', 10);
      const pVendor = await bcrypt.hash('vendor123', 10);
      
      const adminId = (await pool.query(`
        INSERT INTO users (username, password, full_name, role, role_label) 
        VALUES ('admin', $1, 'Super Admin DPBJ', 'admin', 'Super Administrator') 
        RETURNING id
      `, [pAdmin])).rows[0].id;

      const ppkId = (await pool.query(`
        INSERT INTO users (username, password, full_name, role, role_label) 
        VALUES ('ppk', $1, 'Dr. Budi Santoso', 'ppk', 'Pejabat Pembuat Komitmen') 
        RETURNING id
      `, [pPpk])).rows[0].id;

      const pokjaId = (await pool.query(`
        INSERT INTO users (username, password, full_name, role, role_label) 
        VALUES ('pokja', $1, 'Tim Pokja Pemilihan 1', 'pokja', 'Pokja Pemilihan') 
        RETURNING id
      `, [pPokja])).rows[0].id;

      const vendorId = (await pool.query(`
        INSERT INTO users (username, password, full_name, role, role_label) 
        VALUES ('vendor', $1, 'PT Jaya Abadi', 'vendor', 'Penyedia/Vendor') 
        RETURNING id
      `, [pVendor])).rows[0].id;

      await pool.query(`
        INSERT INTO vendors (user_id, company_name, npwp, company_type, city, status)
        VALUES ($1, 'PT Jaya Abadi', '01.234.567.8-901.000', 'Barang', 'Jakarta Selatan', 'terverifikasi')
      `, [vendorId]);

      // Seed Pengajuan
      const reqId = (await pool.query(`
        INSERT INTO procurement_requests (request_number, title, unit_kerja, category, estimated_value, budget_source, fiscal_year, status, requester_id)
        VALUES ('PENGAJUAN/2025/090', 'Pengadaan PC Desktop Lab Komputer', 'Fasilkom UI', 'Barang', 250000000.00, 'DIPA', 2025, 'disetujui', $1)
        RETURNING id
      `, [adminId])).rows[0].id;

      // Seed Tender
      await pool.query(`
        INSERT INTO tenders (procurement_request_id, tender_number, title, category, method, status, pagu_anggaran, submission_deadline, ppk_id, pokja_lead_id)
        VALUES ($1, 'TENDER/2025/090', 'Pengadaan PC Desktop Lab Komputer', 'Barang', 'tender', 'draft', 250000000.00, NOW() + INTERVAL '5 days', $2, $3)
      `, [reqId, ppkId, pokjaId]);

      console.log('Dummy data berhasil ditambahkan!');
    }
  } catch(e) {
    console.error(e);
  } finally {
    process.exit();
  }
}

setup();
