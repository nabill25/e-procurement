const jwt = require('jsonwebtoken');

const JWT_SECRET = process.env.JWT_SECRET || 'dpbj_ui_super_secret_2025_change_in_production';

// Sama persis dengan requireAuth di routes/auth.js (diduplikasi di sini supaya bisa dipasang
// di server/index.js tanpa import siklik ke auth.js). Kalau logikanya perlu diubah, ubah di
// kedua tempat, atau pertimbangkan auth.js meng-import dari sini nanti.
function requireAuth(req, res, next) {
  const authHeader = req.headers['authorization'];
  const token = authHeader && authHeader.split(' ')[1];
  if (!token) {
    return res.status(401).json({ success: false, message: 'Token tidak ditemukan. Silakan login.' });
  }
  try {
    const decoded = jwt.verify(token, JWT_SECRET);
    req.user = decoded;
    next();
  } catch (err) {
    return res.status(401).json({ success: false, message: 'Token tidak valid atau sudah kadaluarsa.' });
  }
}

// Dipasang SETELAH requireAuth. Menolak akses kalau role user yang login tidak ada
// dalam daftar role yang diizinkan untuk endpoint tersebut.
function requireRole(...roles) {
  return (req, res, next) => {
    if (!req.user || !roles.includes(req.user.role)) {
      return res.status(403).json({ success: false, message: 'Anda tidak memiliki akses untuk melakukan aksi ini.' });
    }
    next();
  };
}

// Dipakai untuk endpoint yang HARUS tetap publik tapi ingin menampilkan lebih banyak data
// kalau pemanggilnya ternyata sudah login (misal: HPS tender ditampilkan ke staf internal
// yang login, disembunyikan dari publik). Tidak pernah menolak request - kalau token
// tidak ada/tidak valid, req.user tetap undefined dan request lanjut sebagai anonim.
function optionalAuth(req, res, next) {
  const authHeader = req.headers['authorization'];
  const token = authHeader && authHeader.split(' ')[1];
  if (token) {
    try {
      req.user = jwt.verify(token, JWT_SECRET);
    } catch (err) { /* token tidak valid, anggap saja anonim */ }
  }
  next();
}

module.exports = { requireAuth, requireRole, optionalAuth };
