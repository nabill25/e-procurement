// Penyimpanan file permanen lewat Supabase Storage.
//
// Kenapa ini dibuat: sebelumnya file yang diupload (dokumen vendor, dokumen tender, gambar
// banner, dst) disimpan di folder server/uploads/ (harddisk milik server itu sendiri lewat
// multer.diskStorage). Itu menimbulkan masalah nyata: komputer lokal dan server Railway
// punya "harddisk" masing-masing yang terpisah, jadi file yang diupload dari satu sisi tidak
// pernah muncul di sisi lain. Railway sendiri juga tidak punya penyimpanan permanen sama
// sekali (dicek lewat `railway volume list` = kosong), jadi walau ada yang upload langsung
// di situs online, filenya akan hilang lagi tiap kali server di-deploy ulang.
//
// Solusinya: pindah ke Supabase Storage, penyimpanan file bawaan Supabase (bukan lagi
// harddisk server). Karena project ini sudah pakai Supabase untuk database, sekarang dipakai
// juga untuk simpan file - satu tempat yang sama-sama bisa diakses dari lokal maupun Railway,
// dan permanen (tidak hilang saat redeploy).
//
// Kenapa pakai @supabase/storage-js langsung, bukan @supabase/supabase-js yang lebih umum
// dipakai: paket supabase-js otomatis menyalakan modul Realtime, yang di Node.js versi 20 ke
// bawah (versi yang terpasang di komputer development ini) langsung CRASH saat start karena
// butuh WebSocket bawaan Node 22+. Project ini cuma butuh fitur Storage-nya saja, jadi dipakai
// @supabase/storage-js (paket inti yang dipakai supabase-js di baliknya untuk fitur storage),
// yang tidak menyentuh modul Realtime sama sekali - aman di Node versi berapapun.
//
// Butuh 2 variabel baru di .env yang BEDA dari SUPABASE_DB_URL yang sudah ada (itu password
// database, ini kunci API terpisah untuk fitur Storage):
//   SUPABASE_URL               - URL project Supabase, contoh: https://xxxxx.supabase.co
//   SUPABASE_SERVICE_ROLE_KEY  - kunci rahasia dari Supabase Dashboard > Settings > API,
//                                 bagian "service_role" (BUKAN kunci "anon"/"public"). Kunci
//                                 ini setara password, jangan pernah dikomit ke git atau
//                                 ditempel di tempat yang bisa ter-share.

const { StorageClient } = require('@supabase/storage-js');

const SUPABASE_URL = process.env.SUPABASE_URL || '';
const SUPABASE_SERVICE_ROLE_KEY = process.env.SUPABASE_SERVICE_ROLE_KEY || '';
const BUCKET = process.env.SUPABASE_STORAGE_BUCKET || 'uploads';

let client = null;
if (SUPABASE_URL && SUPABASE_SERVICE_ROLE_KEY) {
  client = new StorageClient(`${SUPABASE_URL.replace(/\/$/, '')}/storage/v1`, {
    apikey: SUPABASE_SERVICE_ROLE_KEY,
    Authorization: `Bearer ${SUPABASE_SERVICE_ROLE_KEY}`,
  });
} else {
  // Sengaja tidak melempar error di sini (supaya server tetap bisa nyala untuk fitur lain
  // yang tidak butuh upload file) - baru gagal di titik upload sungguhan dipakai.
  console.warn('[storage] SUPABASE_URL / SUPABASE_SERVICE_ROLE_KEY belum diisi di .env - fitur upload file tidak akan berfungsi sampai ini diisi.');
}

function isConfigured() {
  return !!client;
}

let bucketReadyPromise = null;
// Pastikan bucket penyimpanan sudah ada, bikin otomatis kalau belum (sekali saja per proses nyala).
function ensureBucket() {
  if (!bucketReadyPromise) {
    bucketReadyPromise = (async () => {
      const { data: buckets, error } = await client.listBuckets();
      if (error) throw new Error(`Gagal cek bucket Supabase Storage: ${error.message}`);
      const exists = (buckets || []).some((b) => b.name === BUCKET || b.id === BUCKET);
      if (!exists) {
        const { error: createErr } = await client.createBucket(BUCKET, {
          public: true,
          fileSizeLimit: 10 * 1024 * 1024, // samakan dengan MAX_FILE_SIZE di upload.js
        });
        if (createErr && !/already exists/i.test(createErr.message || '')) {
          throw new Error(`Gagal membuat bucket Supabase Storage: ${createErr.message}`);
        }
      }
    })();
  }
  return bucketReadyPromise;
}

// Upload 1 file (buffer di memori) ke Supabase Storage, kembalikan URL publik lengkap
// yang siap dipakai langsung sebagai src gambar / href unduhan tanpa perlu digabung apapun lagi.
async function uploadBuffer(buffer, storageName, mimetype) {
  if (!client) {
    throw new Error('Supabase Storage belum dikonfigurasi (SUPABASE_URL / SUPABASE_SERVICE_ROLE_KEY kosong di .env).');
  }
  await ensureBucket();
  const { error } = await client.from(BUCKET).upload(storageName, buffer, {
    contentType: mimetype || 'application/octet-stream',
    upsert: false,
  });
  if (error) throw new Error(`Gagal upload file ke Supabase Storage: ${error.message}`);
  const { data } = client.from(BUCKET).getPublicUrl(storageName);
  return data.publicUrl;
}

// Hapus file dari Storage lewat path relatifnya di dalam bucket (bukan URL publik lengkap).
// Dipakai best-effort saat baris database yang mereferensikannya dihapus - gagal hapus file
// fisik tidak menggagalkan operasi hapus data (konsisten dengan perilaku lama yang juga tidak
// pernah menghapus file fisik saat baris dihapus).
async function deleteByStoragePath(storageName) {
  if (!client || !storageName) return;
  await client.from(BUCKET).remove([storageName]).catch(() => {});
}

module.exports = { uploadBuffer, deleteByStoragePath, isConfigured, BUCKET };
