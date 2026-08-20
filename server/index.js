require('dotenv').config();
const express = require('express');
const cors = require('cors');
const { testConnection } = require('./db');

const app = express();
const PORT = process.env.PORT || 3001;

// ── Middleware ──
app.use(cors({ origin: true, credentials: true }));
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
const path = require('path');
app.use('/uploads', express.static(path.join(__dirname, 'uploads')));

// ── Test Database Connection ──
testConnection();

// ── Routes ──
const { router: authRouter } = require('./routes/auth');
app.use('/api/auth',      authRouter);
app.use('/api/tenders',   require('./routes/tenders'));
app.use('/api/vendors',   require('./routes/vendors'));
app.use('/api/pengajuan', require('./routes/pengajuan'));
app.use('/api/audit',     require('./routes/audit'));
app.use('/api/dashboard', require('./routes/dashboard'));
app.use('/api/katalog',   require('./routes/katalog'));
app.use('/api/purchasing', require('./routes/purchasing'));
app.use('/api/blacklist', require('./routes/blacklist'));
app.use('/api/master', require('./routes/master'));
app.use('/api/menu', require('./routes/menu'));
app.use('/api/inbox', require('./routes/inbox'));
app.use('/api/cms', require('./routes/cms'));

// ── Default Route / Health Check ──
app.get('/api', (req, res) => {
  res.json({ message: 'DPBJ UI E-Procurement API is running!' });
});

// ── Handle 404 ──
app.use((req, res) => {
  res.status(404).json({ success: false, message: 'Endpoint tidak ditemukan.' });
});

// ── Global Error Handler ──
app.use((err, req, res, next) => {
  console.error('[SERVER ERROR]', err.stack);
  res.status(500).json({ success: false, message: 'Terjadi kesalahan internal server.' });
});

app.listen(PORT, () => {
  console.log(`🚀 Server backend berjalan di http://localhost:${PORT}`);
});
