const nodemailer = require('nodemailer');

const SMTP_HOST = process.env.SMTP_HOST || '';
const isConfigured = !!SMTP_HOST;

let transporter = null;
if (isConfigured) {
  transporter = nodemailer.createTransport({
    host: SMTP_HOST,
    port: parseInt(process.env.SMTP_PORT || '587', 10),
    secure: process.env.SMTP_SECURE === 'true',
    auth: process.env.SMTP_USER ? { user: process.env.SMTP_USER, pass: process.env.SMTP_PASS } : undefined,
    // Paksa IPv4 - beberapa jaringan lokal tidak punya rute IPv6 ke server SMTP (gagal dengan
    // ENETUNREACH ke alamat IPv6), padahal IPv4 tetap bisa dijangkau normal.
    family: 4,
  });
}

// Kirim email kalau SMTP sudah dikonfigurasi (SMTP_HOST terisi di .env).
// Kalau belum, cuma catat ke console dan kembalikan status "skipped" - TIDAK melempar error,
// supaya fitur yang memanggil ini (notifikasi dokumen kedaluwarsa, undangan klarifikasi, dst)
// tetap jalan normal dan datanya tetap tercatat di database walau email sungguhan belum terkirim.
async function sendMail({ to, subject, html, text }) {
  if (!isConfigured) {
    console.log(`[MAILER] SMTP belum dikonfigurasi, email tidak dikirim (cuma log). Tujuan: ${to} | Subjek: ${subject}`);
    return { sent: false, reason: 'smtp_not_configured' };
  }

  try {
    await transporter.sendMail({
      from: process.env.SMTP_FROM || 'DPBJ UI E-Procurement <no-reply@ui.ac.id>',
      to,
      subject,
      html,
      text: text || undefined,
    });
    return { sent: true };
  } catch (err) {
    console.error('[MAILER] Gagal mengirim email:', err.message);
    return { sent: false, reason: 'send_failed', error: err.message };
  }
}

module.exports = { sendMail, isConfigured };
