const { pool } = require('./db');
async function run() {
  try {
    await pool.query(`
      CREATE TABLE IF NOT EXISTS vendor_documents (
        id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
        vendor_id UUID REFERENCES users(id) ON DELETE CASCADE,
        doc_type VARCHAR(50),
        doc_number VARCHAR(100),
        issue_date DATE,
        expiry_date DATE,
        file_path VARCHAR(255),
        status VARCHAR(50) DEFAULT 'verified',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );
    `);
    await pool.query(`
      CREATE TABLE IF NOT EXISTS vendor_experiences (
        id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
        vendor_id UUID REFERENCES users(id) ON DELETE CASCADE,
        project_name VARCHAR(255),
        client_name VARCHAR(255),
        contract_value DECIMAL(15,2),
        start_date DATE,
        end_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );
    `);
    console.log('Tabel Kualifikasi Vendor berhasil dibuat');
  } catch(e) {
    console.error(e);
  } finally {
    process.exit();
  }
}
run();
