const multer = require('multer');
const path = require('path');
const fs = require('fs');

// Ekstensi yang diizinkan untuk seluruh aplikasi: dokumen umum + gambar.
// Sengaja tidak mengizinkan file executable/script (.exe, .php, .js, .html, dst)
// supaya folder uploads/ (yang disajikan statis lewat /uploads) tidak bisa dipakai
// untuk menaruh file berbahaya.
const ALLOWED_EXTENSIONS = new Set([
  '.pdf', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx',
  '.jpg', '.jpeg', '.png', '.gif', '.webp',
  '.zip', '.rar',
]);

const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB per file

function makeStorage(prefix) {
  return multer.diskStorage({
    destination: function (req, file, cb) {
      const dir = path.join(__dirname, '..', 'uploads');
      if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
      cb(null, dir);
    },
    filename: function (req, file, cb) {
      const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
      const idPart = req.params && req.params.id ? req.params.id + '-' : '';
      cb(null, `${prefix}-${idPart}${uniqueSuffix}${path.extname(file.originalname).toLowerCase()}`);
    },
  });
}

function fileFilter(req, file, cb) {
  const ext = path.extname(file.originalname).toLowerCase();
  if (!ALLOWED_EXTENSIONS.has(ext)) {
    return cb(new Error(`Tipe file "${ext || '(tanpa ekstensi)'}" tidak diizinkan. Hanya dokumen (PDF/Word/Excel), gambar, atau arsip zip/rar.`));
  }
  cb(null, true);
}

// Dipakai sebagai pengganti `multer({ storage })` polos di semua route.
// prefix: nama pendek modulnya (misal 'vendor', 'tender', 'katalog') untuk penamaan file.
function createUpload(prefix) {
  return multer({
    storage: makeStorage(prefix),
    fileFilter,
    limits: { fileSize: MAX_FILE_SIZE, files: 10 },
  });
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
  if (err && err.message && err.message.includes('tidak diizinkan')) {
    return res.status(400).json({ success: false, message: err.message });
  }
  next(err);
}

module.exports = { createUpload, handleUploadError, MAX_FILE_SIZE, ALLOWED_EXTENSIONS };
