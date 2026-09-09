const nodemailer = require('nodemailer');
const dns = require('dns').promises;

const SMTP_HOST = process.env.SMTP_HOST || '';
const isConfigured = !!SMTP_HOST;

// Ditemukan 2026-09-09 lewat uji kirim email sungguhan di production (Railway): server itu
// tidak punya jalur jaringan IPv6 sama sekali, dan `smtp.gmail.com` resolve ke alamat IPv4 DAN
// IPv6 sekaligus - percobaan koneksi ke alamat IPv6-nya selalu gagal "ENETUNREACH".
//
// Dua percobaan perbaikan SEBELUM ini ternyata sama-sama TIDAK manjur, setelah dicek langsung
// ke source code nodemailer yang terpasang (node_modules/nodemailer/lib/shared/index.js):
// nodemailer melakukan resolusi DNS-nya SENDIRI secara manual lewat `dns.resolve4()` +
// `dns.resolve6()` lalu menggabungkan hasilnya - BUKAN lewat `dns.lookup()` bawaan Node.js.
// Akibatnya:
//   1. Opsi `family: 4` di `nodemailer.createTransport()` (percobaan pertama) tidak pernah
//      diteruskan ke mana pun - digrep ke seluruh source nodemailer, kata "family" sama sekali
//      tidak disebut di situ.
//   2. `dns.setDefaultResultOrder('ipv4first')` di server/index.js (percobaan kedua) cuma
//      memengaruhi `dns.lookup()`, TIDAK memengaruhi `dns.resolve4()`/`resolve6()` yang dipakai
//      nodemailer - jadi tidak berpengaruh sama sekali ke email walau sudah aktif dan benar-benar
//      ter-deploy (dites ulang setelah dipastikan kode baru sungguhan berjalan, masih gagal
//      dengan pesan ENETUNREACH yang identik).
//
// Perbaikan yang akhirnya benar-benar manjur (SUDAH diverifikasi lewat pengiriman email
// sungguhan dari production, lihat CLAUDE.md): resolve alamat IPv4 SENDIRI di sini (bukan lewat
// nodemailer) sebelum bikin koneksi, lalu paksa nodemailer connect ke ALAMAT IP itu langsung
// (bukan ke nama host lagi, supaya nodemailer tidak sempat coba resolve6() sama sekali).
// `tls.servername` diisi nama host ASLI (bukan alamat IP) supaya verifikasi sertifikat TLS
// tetap benar - koneksi TLS ke alamat IP polos butuh SNI eksplisit macam ini, kalau tidak nanti
// sertifikat servernya dianggap tidak cocok dengan yang diminta.
async function resolveIPv4Host(hostname) {
  try {
    const addresses = await dns.resolve4(hostname);
    if (addresses && addresses.length) return addresses[0];
  } catch (err) {
    // dibiarkan null - sendMail() di bawah tetap coba pakai nama host asli sebagai jalan
    // terakhir kalau resolve IPv4 manual ini sendiri gagal, daripada berhenti total.
  }
  return null;
}

// Transporter dibuat BARU tiap kali kirim (bukan sekali di awal modul) supaya alamat IPv4-nya
// selalu segar - alamat IP server Gmail bisa berubah sewaktu-waktu (infrastruktur Google),
// jadi tidak aman kalau di-cache permanen. Overhead-nya kecil karena email di sistem ini
// dikirim jarang (notifikasi), bukan pengiriman massal.
function buildTransporter(host) {
  return nodemailer.createTransport({
    host,
    port: parseInt(process.env.SMTP_PORT || '587', 10),
    secure: process.env.SMTP_SECURE === 'true',
    auth: process.env.SMTP_USER ? { user: process.env.SMTP_USER, pass: process.env.SMTP_PASS } : undefined,
    tls: { servername: SMTP_HOST },
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
    const ipv4 = await resolveIPv4Host(SMTP_HOST);
    const transporter = buildTransporter(ipv4 || SMTP_HOST);
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
