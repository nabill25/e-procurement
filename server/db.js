const { Pool } = require('pg');
// Path eksplisit ke server/.env - lihat catatan lengkap di server/index.js soal kenapa ini wajib.
require('dotenv').config({ path: require('path').join(__dirname, '.env') });

let poolConfig = {};

// Jika user memberikan URL Supabase secara penuh
if (process.env.SUPABASE_DB_URL) {
  poolConfig = {
    connectionString: process.env.SUPABASE_DB_URL,
    max: 10,
    idleTimeoutMillis: 30000,
  };
} else if (process.env.SUPABASE_DB_PASSWORD) {
  // Format Supabase fallback
  const dbUrl = `postgresql://postgres.xdzdzcbzlobvezyzgxbl:${process.env.SUPABASE_DB_PASSWORD}@aws-0-ap-southeast-1.pooler.supabase.com:6543/postgres`;
  poolConfig = {
    connectionString: dbUrl,
    max: 10,
    idleTimeoutMillis: 30000,
  };
} else {
  poolConfig = {
    host:            process.env.DB_HOST     || '127.0.0.1',
    port:            parseInt(process.env.DB_PORT) || 5432,
    database:        process.env.DB_NAME     || 'dpbj_ui',
    user:            process.env.DB_USER     || 'postgres',
    max: 10,
    idleTimeoutMillis: 30000,
  };
  if (process.env.DB_PASS) {
    poolConfig.password = process.env.DB_PASS;
  }
}

const pool = new Pool(poolConfig);

// ── Test koneksi saat startup ──
async function testConnection() {
  try {
    const conn = await pool.connect();
    console.log('✅ Database PostgreSQL terhubung:', process.env.DB_NAME);
    conn.release();
  } catch (err) {
    console.error('❌ Gagal koneksi ke PostgreSQL:', err.message);
    if (process.env.SUPABASE_DB_PASSWORD) {
      console.error('   Pastikan password Supabase yang dimasukkan di .env (SUPABASE_DB_PASSWORD) benar.');
    } else {
      console.error('   Pastikan server PostgreSQL sudah berjalan dan database dpbj_ui sudah dibuat.');
    }
    process.exit(1);  // Hentikan server jika DB tidak bisa konek
  }
}

module.exports = { pool, testConnection };
