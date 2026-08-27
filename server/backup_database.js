// Backup manual seluruh database ke file SQL (INSERT statements), tanpa perlu pg_dump
// (tool itu tidak terpasang di komputer ini). Cocok dipakai kapan saja sebagai lapis tambahan
// di luar backup otomatis Supabase sendiri.
//
// Cara pakai: node server/backup_database.js
// Hasilnya disimpan ke folder backups/ (dibuat otomatis), nama file pakai timestamp supaya
// tidak menimpa backup lama. File .sql yang dihasilkan bisa dijalankan langsung ke database
// kosong (lewat script restore_database.js) untuk memulihkan data.
require('dotenv').config();
const fs = require('fs');
const path = require('path');
const { pool } = require('./db');

const BACKUP_DIR = path.join(__dirname, '..', 'backups');

function escapeValue(val) {
  if (val === null || val === undefined) return 'NULL';
  if (typeof val === 'number') return val;
  if (typeof val === 'boolean') return val ? 'TRUE' : 'FALSE';
  if (val instanceof Date) return `'${val.toISOString()}'`;
  if (Buffer.isBuffer(val)) return `'\\x${val.toString('hex')}'`;
  if (typeof val === 'object') return `'${JSON.stringify(val).replace(/'/g, "''")}'::jsonb`;
  return `'${String(val).replace(/'/g, "''")}'`;
}

async function backup() {
  if (!fs.existsSync(BACKUP_DIR)) fs.mkdirSync(BACKUP_DIR, { recursive: true });

  const stamp = new Date().toISOString().replace(/[:.]/g, '-');
  const outFile = path.join(BACKUP_DIR, `backup_${stamp}.sql`);
  const stream = fs.createWriteStream(outFile, { encoding: 'utf-8' });

  stream.write(`-- Backup database DPBJ UI E-Procurement\n-- Dibuat: ${new Date().toISOString()}\n-- Cara restore: node server/restore_database.js <nama_file_ini>\n\n`);
  stream.write('SET session_replication_role = replica;\n\n'); // matikan sementara pengecekan FK supaya urutan insert tidak masalah

  const tablesResult = await pool.query(`
    SELECT tablename FROM pg_tables WHERE schemaname = 'public' ORDER BY tablename
  `);
  const tables = tablesResult.rows.map(r => r.tablename);

  let totalRows = 0;
  for (const table of tables) {
    const dataResult = await pool.query(`SELECT * FROM "${table}"`);
    if (!dataResult.rows.length) continue;

    const columns = Object.keys(dataResult.rows[0]);
    stream.write(`-- Tabel: ${table} (${dataResult.rows.length} baris)\n`);
    for (const row of dataResult.rows) {
      const values = columns.map(c => escapeValue(row[c])).join(', ');
      stream.write(`INSERT INTO "${table}" ("${columns.join('", "')}") VALUES (${values}) ON CONFLICT DO NOTHING;\n`);
    }
    stream.write('\n');
    totalRows += dataResult.rows.length;
    console.log(`  ${table}: ${dataResult.rows.length} baris`);
  }

  stream.write('SET session_replication_role = DEFAULT;\n');
  stream.end();

  await new Promise((resolve) => stream.on('finish', resolve));

  const stats = fs.statSync(outFile);
  console.log(`\nBackup selesai: ${outFile}`);
  console.log(`Total ${tables.length} tabel, ${totalRows} baris, ukuran file ${(stats.size / 1024 / 1024).toFixed(2)} MB`);
  process.exit(0);
}

backup().catch((err) => {
  console.error('Backup gagal:', err.message);
  process.exit(1);
});
