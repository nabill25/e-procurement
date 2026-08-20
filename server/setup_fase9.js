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
    console.log('Connecting to database for Phase 9 Setup (VMS SIKaP)...');
    await pool.connect();
    
    console.log('Adding JSONB columns to vendors table...');
    await pool.query(`
      ALTER TABLE vendors 
      ADD COLUMN IF NOT EXISTS pajak JSONB DEFAULT '[]'::jsonb,
      ADD COLUMN IF NOT EXISTS tenaga_ahli JSONB DEFAULT '[]'::jsonb,
      ADD COLUMN IF NOT EXISTS peralatan JSONB DEFAULT '[]'::jsonb,
      ADD COLUMN IF NOT EXISTS pengurus JSONB DEFAULT '[]'::jsonb;
    `);
    
    console.log('Phase 9 Setup completed successfully!');
  } catch (err) {
    console.error('Error in Phase 9 setup:', err);
  } finally {
    await pool.end();
  }
}

setup();
