const { pool } = require('./db');

async function run() {
  try {
    console.log('Menjalankan migrasi Fase 6: Penilaian Kinerja & Sanksi Vendor...');
    
    // 1. Tambah kolom rating_avg dan rating_count ke users jika belum ada
    await pool.query(`
      ALTER TABLE users
      ADD COLUMN IF NOT EXISTS rating_avg NUMERIC(3, 2) DEFAULT 0.00,
      ADD COLUMN IF NOT EXISTS rating_count INT DEFAULT 0;
    `);

    // 2. Buat tabel vendor_ratings
    await pool.query(`
      CREATE TABLE IF NOT EXISTS vendor_ratings (
        id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
        vendor_id UUID REFERENCES users(id) ON DELETE CASCADE,
        tender_id UUID REFERENCES tenders(id) ON DELETE CASCADE,
        ppk_id UUID REFERENCES users(id) ON DELETE CASCADE,
        rating_score INT CHECK (rating_score >= 1 AND rating_score <= 5) NOT NULL,
        review_notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(vendor_id, tender_id)
      );
    `);

    // 3. Update tabel users kolom status untuk mock users jika diperlukan
    // (mock data sudah dibuat, kita pastikan struktur enumnya)
    // Di PostgreSQL, jika status VARCHAR, kita biarkan saja.

    console.log('Migrasi Fase 6 berhasil!');
  } catch (err) {
    console.error('Gagal migrasi:', err);
  } finally {
    process.exit(0);
  }
}

run();
