// Skrip migrasi SEKALI JALAN: pindahkan file yang masih ada di server/uploads/ (harddisk lokal
// komputer ini) ke Supabase Storage, lalu perbarui baris database yang mereferensikannya supaya
// menunjuk ke URL Storage yang baru (bukan lagi path lokal /uploads/xxx yang cuma ada di
// komputer ini, tidak pernah sampai ke Railway).
//
// Kenapa ini perlu: sebelum migrasi ini, SEMUA file yang diupload lewat "npm run dev" lokal
// (termasuk seluruh data demo dari server/seed_demo_data.js) cuma tersimpan di disk lokal.
// Baris di database (Supabase, dipakai bersama lokal & Railway) sudah lama menyimpan referensi
// ke file-file itu, tapi filenya sendiri tidak pernah ada di Railway. Skrip ini menutup gap itu
// untuk data yang SUDAH ADA (file baru yang diupload setelah migrasi kode selesai otomatis
// lewat Supabase Storage, tidak butuh skrip ini lagi).
//
// Cara pakai: node server/migrate_uploads_to_storage.js
// (jalankan dari folder manapun, path di dalam skrip ini pakai __dirname jadi tetap benar)
//
// AMAN dijalankan berkali-kali (idempotent): baris yang isinya sudah berupa URL https:// (baik
// karena sudah pernah dimigrasikan, atau memang upload baru pasca-migrasi kode) dilewati.

const fs = require('fs');
const path = require('path');
const { pool } = require('./db');
const { uploadBuffer, isConfigured } = require('./lib/storage');

const UPLOADS_DIR = path.join(__dirname, 'uploads');

// Daftar (tabel, kolom) yang benar-benar menyimpan path file hasil upload - dikonfirmasi satu
// per satu langsung dari kode route (bukan tebakan dari nama kolom), lewat query
// information_schema.columns lalu dicek baris kodenya. Kolom yang KELIHATANNYA soal file tapi
// TERNYATA BUKAN upload sungguhan sengaja tidak dimasukkan: cms_banners.link_url dan
// cms_news.image_url (diketik manual oleh admin, bukan hasil upload), katalog_items.image_url
// (sama, field URL manual, terpisah dari katalog_photos yang memang upload),
// integration_rka_budget.import_file & integration_pr_import.import_file & integration_logs.
// file_name (cuma label nama file untuk ditampilkan, bukan path yang bisa diunduh),
// tender_general_chats.file_path (diisi dari req.body, bukan endpoint upload),
// procurement_request_files.esign_path_file (kolom ada di skema tapi tidak pernah ditulis
// kode manapun, selalu kosong).
const TARGETS = [
  ['blacklist', 'sk_file_path'],
  ['cms_banners', 'gambar_path'],
  ['contract_addendum', 'file_addendum'],
  ['contract_addendum', 'file_persetujuan'],
  ['contract_deliverables', 'file_path'],
  ['contract_documents', 'file_path'],
  ['contract_jaminan', 'file_jaminan'],
  ['contract_jaminan', 'file_konfirmasi'],
  ['contract_jaminan_pemeliharaan', 'file_jaminan'],
  ['contract_payment_terms', 'bapp_file_path'],
  ['contract_status_changes', 'file_path'],
  ['contracts', 'bast_path'],
  ['contracts', 'spk_path'],
  ['document_templates', 'file_path'],
  ['inbox_messages', 'attachment_path'],
  ['katalog_attachments', 'file_path'],
  ['katalog_photos', 'file_path'],
  ['procurement_request_files', 'file_path'],
  ['procurement_request_revisions', 'file_path'],
  ['procurement_requests', 'kak_path'],
  ['procurement_requests', 'nota_dinas_path'],
  ['procurement_requests', 'rab_path'],
  ['sk_panitia', 'file_path'],
  ['tender_documents', 'file_path'],
  ['tender_klarifikasi_dokumen', 'file_path'],
  ['tender_objections', 'attachment_path'],
  ['tender_objections', 'response_attachment_path'],
  ['tender_participants', 'document_path'],
  ['tender_pernyataan_minat', 'penerima_kuasa_file'],
  ['vendor_documents', 'file_path'],
  ['vendor_rekening_koran', 'file_path'],
];

const MIME_MAP = {
  '.pdf': 'application/pdf',
  '.doc': 'application/msword',
  '.docx': 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
  '.xls': 'application/vnd.ms-excel',
  '.xlsx': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
  '.ppt': 'application/vnd.ms-powerpoint',
  '.pptx': 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
  '.jpg': 'image/jpeg', '.jpeg': 'image/jpeg', '.png': 'image/png',
  '.gif': 'image/gif', '.webp': 'image/webp',
  '.zip': 'application/zip', '.rar': 'application/vnd.rar',
};

function extractLocalFilename(value) {
  // Nilai lama bisa berupa "/uploads/xxx.pdf" ATAU cuma "xxx.pdf" (dua konvensi lama yang
  // sekarang sudah disatukan di kode baru, tapi baris data lama masih pakai keduanya).
  if (!value) return null;
  return path.basename(value);
}

async function run() {
  if (!isConfigured()) {
    console.error('❌ SUPABASE_URL / SUPABASE_SERVICE_ROLE_KEY belum diisi di server/.env - tidak bisa migrasi.');
    process.exit(1);
  }

  let totalRows = 0, migrated = 0, missingLocal = 0, alreadyUrl = 0, errors = 0;
  const missingList = [];

  for (const [table, column] of TARGETS) {
    const result = await pool.query(`SELECT id, "${column}" AS val FROM ${table} WHERE "${column}" IS NOT NULL AND "${column}" <> ''`);
    for (const row of result.rows) {
      totalRows++;
      const val = row.val;
      if (/^https?:\/\//i.test(val)) { alreadyUrl++; continue; }

      const filename = extractLocalFilename(val);
      const localPath = path.join(UPLOADS_DIR, filename);
      if (!fs.existsSync(localPath)) {
        missingLocal++;
        missingList.push(`${table}.${column} (id=${row.id}): ${val}`);
        continue;
      }

      try {
        const buffer = fs.readFileSync(localPath);
        const ext = path.extname(filename).toLowerCase();
        const mimetype = MIME_MAP[ext] || 'application/octet-stream';
        const storageName = `migrated/${table}/${filename}`;
        const publicUrl = await uploadBuffer(buffer, storageName, mimetype);
        await pool.query(`UPDATE ${table} SET "${column}" = $1 WHERE id = $2`, [publicUrl, row.id]);
        migrated++;
        console.log(`✅ ${table}.${column} id=${row.id}: ${filename} -> ${publicUrl}`);
      } catch (err) {
        errors++;
        console.error(`❌ Gagal migrasi ${table}.${column} id=${row.id} (${filename}): ${err.message}`);
      }
    }
  }

  console.log('\n── Ringkasan Migrasi ──');
  console.log(`Total baris dicek     : ${totalRows}`);
  console.log(`Sudah URL (dilewati)  : ${alreadyUrl}`);
  console.log(`Berhasil dimigrasi    : ${migrated}`);
  console.log(`File lokal tidak ada  : ${missingLocal}`);
  console.log(`Gagal (error)         : ${errors}`);
  if (missingList.length) {
    console.log('\nBaris yang file lokalnya tidak ditemukan (dibiarkan apa adanya, kemungkinan data uji lama yang filenya sudah terhapus):');
    missingList.forEach((m) => console.log('  - ' + m));
  }

  await pool.end();
  process.exit(errors > 0 ? 1 : 0);
}

run().catch((err) => { console.error('FATAL:', err); process.exit(1); });
