const multer = require('multer');
const path = require('path');
const { uploadBuffer } = require('./storage');

// Ekstensi yang diizinkan untuk seluruh aplikasi: dokumen umum + gambar.
// Sengaja tidak mengizinkan file executable/script (.exe, .php, .js, .html, dst)
// supaya bucket penyimpanan tidak bisa dipakai untuk menaruh file berbahaya.
const ALLOWED_EXTENSIONS = new Set([
  '.pdf', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx',
  '.jpg', '.jpeg', '.png', '.gif', '.webp',
  '.zip', '.rar',
]);

const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB per file

function fileFilter(req, file, cb) {
  const ext = path.extname(file.originalname).toLowerCase();
  if (!ALLOWED_EXTENSIONS.has(ext)) {
    return cb(new Error(`Tipe file "${ext || '(tanpa ekstensi)'}" tidak diizinkan. Hanya dokumen (PDF/Word/Excel), gambar, atau arsip zip/rar.`));
  }
  cb(null, true);
}

// File ditampung di memori dulu (bukan ditulis ke disk server) - selanjutnya middleware
// makeStorageUploader() yang mengambil buffer-nya dan mengunggahnya ke Supabase Storage.
function makeMulter() {
  return multer({
    storage: multer.memoryStorage(),
    fileFilter,
    limits: { fileSize: MAX_FILE_SIZE, files: 10 },
  });
}

function storageNameFor(prefix, req, file) {
  const idPart = req.params && req.params.id ? `${req.params.id}-` : '';
  const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1e9);
  const ext = path.extname(file.originalname).toLowerCase();
  return `${prefix}/${idPart}${uniqueSuffix}${ext}`;
}

// Middleware yang jalan SETELAH multer selesai parsing: ambil buffer file dari
// req.file / req.files, unggah ke Supabase Storage, lalu timpa `.filename` supaya isinya
// jadi URL publik lengkap yang siap dipakai langsung. Ini disengaja supaya SELURUH route yang
// sudah ada (yang membaca req.file.filename / req.files.x[0].filename) tetap jalan tanpa
// perlu diubah logikanya - cukup nilainya sekarang sudah berupa URL utuh, bukan lagi nama
// file lokal.
function makeStorageUploader(prefix) {
  return async function uploadToStorageMw(req, res, next) {
    try {
      const files = [];
      if (req.file) files.push(req.file);
      if (req.files) {
        if (Array.isArray(req.files)) files.push(...req.files);
        else Object.values(req.files).forEach((arr) => files.push(...arr));
      }
      for (const f of files) {
        const storageName = storageNameFor(prefix, req, f);
        const publicUrl = await uploadBuffer(f.buffer, storageName, f.mimetype);
        f.filename = publicUrl;
        f.storagePath = storageName;
      }
      next();
    } catch (err) {
      next(err);
    }
  };
}

// Dipakai sebagai pengganti `multer({ storage })` polos di semua route.
// prefix: nama pendek modulnya (misal 'vendor', 'tender', 'katalog') untuk penamaan file.
// persist: false dipakai untuk upload yang cuma dibaca sekali lalu tidak perlu disimpan
// permanen (misal file Excel Integrasi Oracle yang cuma diparsing isinya) - lewat opsi ini
// filenya TIDAK diunggah ke Storage, cukup tersedia sebagai req.file.buffer di memori.
function createUpload(prefix, opts = {}) {
  const persist = opts.persist !== false;
  const m = makeMulter();
  const storageMw = persist ? makeStorageUploader(prefix) : (req, res, next) => next();
  return {
    single: (field) => [m.single(field), storageMw],
    array: (field, max) => [m.array(field, max), storageMw],
    fields: (fieldsSpec) => [m.fields(fieldsSpec), storageMw],
  };
}

// Middleware untuk menerjemahkan error multer (tipe file / ukuran) jadi respons JSON yang jelas,
// dipasang setelah route yang pakai upload.single()/upload.array() dst.
function handleUploadError(err, req, res, next) {
  if (err instanceof multer.MulterError) {
    if (err.code === 'LIMIT_FILE_SIZE') {
      return res.status(400).json({ success: false, message: 'Ukuran file terlalu besar. Maksimal 10MB per file.' });
    }
    return res.status(400).json({ success: false, message: 'Gagal mengunggah file: ' + err.message });
  }
  if (err && err.message && (err.message.includes('tidak diizinkan') || err.message.includes('Supabase Storage'))) {
    return res.status(400).json({ success: false, message: err.message });
  }
  next(err);
}

module.exports = { createUpload, handleUploadError, MAX_FILE_SIZE, ALLOWED_EXTENSIONS };
