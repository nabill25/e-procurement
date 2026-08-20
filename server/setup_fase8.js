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
    console.log('Connecting to database for Phase 8 Setup (Aanwijzing)...');
    await pool.connect();
    
    // Create tender_aanwijzing_chats table
    console.log('Creating table tender_aanwijzing_chats...');
    await pool.query(`
      CREATE TABLE IF NOT EXISTS tender_aanwijzing_chats (
        id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
        tender_id UUID REFERENCES tenders(id) ON DELETE CASCADE,
        user_id UUID REFERENCES users(id) ON DELETE CASCADE,
        message TEXT NOT NULL,
        created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
      );
    `);
    
    console.log('Phase 8 Setup completed successfully!');
  } catch (err) {
    console.error('Error in Phase 8 setup:', err);
  } finally {
    await pool.end();
  }
}

setup();
