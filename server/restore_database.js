// Restore database dari file backup yang dibuat backup_database.js.
// PERINGATAN: ini menjalankan INSERT ke database yang sedang dikonfigurasi (lihat server/.env,
// SUPABASE_DB_URL). Jalankan dengan hati-hati, terutama kalau ini database yang sedang dipakai -
// data yang sudah ada TIDAK ditimpa (pakai ON CONFLICT DO NOTHING di file backup), tapi tetap
// pastikan Anda tahu database mana yang sedang aktif sebelum menjalankan ini.
//
// Cara pakai: node server/restore_database.js backups/backup_2026-08-27T10-00-00-000Z.sql
require('dotenv').config();
const fs = require('fs');
const path = require('path');
const { pool } = require('./db');

const fileArg = process.argv[2];
if (!fileArg) {
  console.error('Cara pakai: node server/restore_database.js <path_file_backup.sql>');
  process.exit(1);
}

const filePath = path.isAbsolute(fileArg) ? fileArg : path.join(__dirname, '..', fileArg);
if (!fs.existsSync(filePath)) {
  console.error('File tidak ditemukan:', filePath);
  process.exit(1);
}

async function restore() {
  const sql = fs.readFileSync(filePath, 'utf-8');
  console.log(`Menjalankan restore dari: ${filePath}`);
  console.log(`Ukuran file: ${(sql.length / 1024 / 1024).toFixed(2)} MB\n`);

  const client = await pool.connect();
  try {
    await client.query('BEGIN');
    await client.query(sql);
    await client.query('COMMIT');
    console.log('Restore berhasil.');
  } catch (err) {
    await client.query('ROLLBACK');
    console.error('Restore gagal, semua perubahan dibatalkan:', err.message);
    process.exit(1);
  } finally {
    client.release();
  }
  process.exit(0);
}

restore();
