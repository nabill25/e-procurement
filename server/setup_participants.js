const { pool } = require('./db');

async function setup() {
  try {
    await pool.query(`
      CREATE TABLE IF NOT EXISTS tender_participants (
        id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
        tender_id UUID REFERENCES tenders(id) ON DELETE CASCADE,
        vendor_id UUID REFERENCES users(id) ON DELETE CASCADE,
        status VARCHAR(50) DEFAULT 'registered',
        registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(tender_id, vendor_id)
      );
    `);
    console.log('Tabel tender_participants siap.');
  } catch(e) {
    console.error(e);
  } finally {
    process.exit(0);
  }
}
setup();
