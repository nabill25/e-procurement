const { Pool } = require('pg');
require('dotenv').config();

const pool = new Pool({
  user: 'postgres',
  host: 'localhost',
  database: 'dpbj_ui',
  password: 'nabil2507',
  port: 5432,
});

async function setup() {
  try {
    console.log('Connecting to database for Phase 11 Setup (SAP ERP Integration)...');
    await pool.connect();
    
    console.log('Adding SAP columns to procurement_requests table...');
    await pool.query(`
      ALTER TABLE procurement_requests 
      ADD COLUMN IF NOT EXISTS is_from_sap BOOLEAN DEFAULT false,
      ADD COLUMN IF NOT EXISTS sap_pr_number VARCHAR(100) UNIQUE;
    `);
    
    console.log('Phase 11 Setup completed successfully!');
  } catch (err) {
    console.error('Error in Phase 11 setup:', err);
  } finally {
    await pool.end();
  }
}

setup();
