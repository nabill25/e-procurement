const { pool } = require('./db');

async function run() {
  try {
    console.log('Menjalankan migrasi Fase 4: Pengajuan Berdokumen & Multi-level...');
    
    await pool.query(`
      ALTER TABLE procurement_requests 
      ADD COLUMN IF NOT EXISTS kak_path VARCHAR(255),
      ADD COLUMN IF NOT EXISTS rab_path VARCHAR(255),
      ADD COLUMN IF NOT EXISTS nota_dinas_path VARCHAR(255),
      ADD COLUMN IF NOT EXISTS admin_notes TEXT,
      ADD COLUMN IF NOT EXISTS is_docs_complete BOOLEAN DEFAULT FALSE;
    `);

    // Reset status existing requests to 'draft' or 'diajukan' if necessary for testing
    // Not modifying existing data to avoid confusion.
    
    console.log('Kolom berhasil ditambahkan ke procurement_requests!');
  } catch (err) {
    console.error('Gagal:', err);
  } finally {
    process.exit(0);
  }
}

run();
