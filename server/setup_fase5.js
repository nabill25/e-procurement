const { pool } = require('./db');

async function run() {
  try {
    console.log('Menjalankan migrasi Fase 5: Masa Sanggah & Kontrak...');
    
    await pool.query(`
      CREATE TABLE IF NOT EXISTS tender_objections (
        id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
        tender_id UUID REFERENCES tenders(id) ON DELETE CASCADE,
        vendor_id UUID REFERENCES users(id) ON DELETE CASCADE,
        objection_text TEXT NOT NULL,
        attachment_path VARCHAR(255),
        response_text TEXT,
        response_attachment_path VARCHAR(255),
        status VARCHAR(50) DEFAULT 'submitted', -- 'submitted', 'responded'
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );

      CREATE TABLE IF NOT EXISTS contracts (
        id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
        tender_id UUID REFERENCES tenders(id) ON DELETE CASCADE UNIQUE,
        vendor_id UUID REFERENCES users(id) ON DELETE CASCADE,
        contract_number VARCHAR(100) NOT NULL,
        contract_date DATE,
        contract_value NUMERIC(15, 2),
        spk_path VARCHAR(255),
        bast_path VARCHAR(255),
        status VARCHAR(50) DEFAULT 'draft', -- 'draft', 'signed', 'completed'
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );
    `);

    console.log('Tabel tender_objections dan contracts berhasil dibuat!');
  } catch (err) {
    console.error('Gagal:', err);
  } finally {
    process.exit(0);
  }
}

run();
