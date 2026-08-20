const { pool } = require('./db');

async function test() {
  try {
    const res = await pool.query('SELECT NOW()');
    console.log('Connected to Supabase! Time:', res.rows[0].now);
    
    const fs = require('fs');
    const path = require('path');
    const sql = fs.readFileSync(path.join(__dirname, '..', 'supabase_clean.sql'), 'utf-8');
    
    console.log('Executing clean SQL on Supabase...');
    await pool.query(sql);
    console.log('Migration to Supabase completed successfully!');
  } catch (err) {
    console.error('Error:', err);
  } finally {
    pool.end();
  }
}

test();
