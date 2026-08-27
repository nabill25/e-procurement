# Panduan Operasional - DPBJ UI E-Procurement

Dokumen ini berisi prosedur praktis untuk menjaga sistem tetap aman dan bisa dipulihkan kalau terjadi masalah. Ditulis dengan bahasa sederhana supaya bisa diikuti siapa saja, bukan cuma yang paham teknis.

## 0. Status SMTP (email) saat ini (2026-08-27)

Kredensial SMTP (Gmail App Password, akun `mrnbil2507@gmail.com`) sudah diisi ke `server/.env` dan kodenya sudah teruji benar (`server/lib/mailer.js`, ditambahkan `family: 4` supaya memaksa pakai IPv4 karena beberapa jaringan tidak punya rute IPv6 ke Gmail).

**Tapi email belum bisa dikirim dari komputer development ini**, bukan karena kredensial salah, melainkan **jaringan komputer ini memblokir semua port pengiriman email keluar** (dicoba dan dikonfirmasi timeout di ketiga port standar: 25, 465, 587). Ini pola umum di banyak jaringan rumah/kantor/ISP untuk mencegah spam - server yang memblokir bukan Gmail atau kode aplikasi, tapi jaringan lokal tempat komputer ini terhubung.

**Kemungkinan besar ini akan berfungsi normal begitu di-deploy ke layanan cloud** (Vercel, Railway, Render, dst) karena layanan-layanan itu pada umumnya tidak memblokir port SMTP keluar seperti jaringan rumah/kantor biasa. Yang perlu dilakukan setelah deploy:
1. Pastikan variabel `SMTP_HOST`, `SMTP_PORT`, `SMTP_USER`, `SMTP_PASS`, `SMTP_FROM` diisi di pengaturan environment variables layanan hosting (bukan cuma di file `.env` lokal, itu tidak ikut ter-deploy).
2. Uji kirim 1 email sungguhan dari lingkungan production untuk konfirmasi (bisa lewat fitur "Undangan Klarifikasi" di tab Kontrak, atau langsung panggil endpoint yang memicu email).
3. Kalau ternyata TETAP gagal walau sudah di production (jarang terjadi tapi mungkin), errornya akan tercatat di log server - biasanya berarti perlu ganti ke SMTP resmi institusi UI (bukan Gmail) atau layanan email transaksional (SendGrid, Mailgun, dst yang punya API khusus, bukan lewat port SMTP biasa).

## 1. Backup Database

### Backup otomatis dari Supabase (sudah berjalan, tidak perlu diatur)

Supabase (penyedia database cloud yang dipakai project ini) sudah otomatis membuat backup harian untuk semua project, termasuk yang gratis. Untuk paket berbayar, tersedia juga "Point-in-Time Recovery" yang bisa mengembalikan database ke kondisi di menit tertentu (bukan cuma per hari).

Cara mengecek/menggunakan: masuk ke dashboard Supabase (https://supabase.com/dashboard) → pilih project ini → menu "Database" → "Backups". Kalau perlu pulihkan data dari backup Supabase, lakukan dari situ langsung.

### Backup manual tambahan (lapis kedua, sudah disiapkan)

Selain backup otomatis Supabase, ada skrip di project ini untuk membuat salinan data secara manual kapan saja, disimpan sebagai file di komputer lokal.

**Membuat backup:**
```
npm run db:backup
```
File hasil backup akan tersimpan di folder `backups/` dengan nama yang mengandung tanggal dan jam (misalnya `backup_2026-08-27T08-11-48-771Z.sql`). Folder ini sengaja tidak ikut disimpan ke git (ada di `.gitignore`) karena isinya data asli, bukan kode program.

**Kapan sebaiknya backup manual dijalankan:**
- Sebelum menjalankan migrasi database baru (perubahan struktur tabel)
- Sebelum melakukan perubahan besar pada data (misalnya hapus massal, migrasi data)
- Secara berkala (disarankan minimal 1x seminggu kalau sistem sudah dipakai sungguhan)

**Memulihkan data dari backup manual:**
```
npm run db:restore backups/nama_file_backup.sql
```
Perhatian: proses ini menambahkan data dari file backup ke database yang sedang aktif (dicek dari `SUPABASE_DB_URL` di file `.env`). Data yang sudah ada di database tidak akan ditimpa/dihapus (pakai `ON CONFLICT DO NOTHING`), jadi aman dijalankan berulang tanpa takut duplikat. Tapi tetap pastikan dulu database mana yang sedang aktif sebelum menjalankan restore, supaya tidak salah sasaran.

## 2. Kalau Terjadi Masalah (Disaster Recovery)

### Skenario: Data hilang/rusak karena kesalahan aplikasi atau manusia

1. Jangan panik, jangan langsung menjalankan operasi lain ke database yang bermasalah.
2. Cek dulu apakah datanya masih ada tapi cuma tidak tampil (kemungkinan bug di kode), atau memang sudah terhapus dari database.
3. Kalau memang terhapus, pulihkan dari Supabase Backups (poin 1 di atas) sesuai waktu kejadian, atau dari backup manual terakhir yang tersedia.
4. Setelah data pulih, baru cari tahu penyebabnya supaya tidak terulang.

### Skenario: Server backend (Node/Express) berhenti merespons atau error terus-menerus

1. Cek proses yang sedang berjalan: `netstat -ano | grep ":3001"` (Windows/Git Bash) untuk tahu apakah prosesnya masih hidup.
2. Kalau masih hidup tapi tidak merespons dengan benar, matikan dan jalankan ulang: cari PID dari langkah di atas, lalu `taskkill //PID <nomor> //F`, kemudian `npm run server` lagi.
3. Cek log yang muncul saat startup untuk tahu penyebab error (biasanya soal koneksi database atau port yang bentrok).
4. Kalau setelah dijalankan ulang errornya sama persis, kemungkinan besar ada bug di kode yang baru saja diubah - pertimbangkan untuk kembalikan ke versi sebelumnya (`git log` untuk lihat riwayat, `git checkout <commit_lama> -- <file>` untuk file tertentu).

### Skenario: Database Supabase tidak bisa diakses sama sekali

1. Cek status Supabase di https://status.supabase.com/ untuk tahu apakah ini masalah dari pihak Supabase (bukan masalah di sisi kita).
2. Cek apakah kredensial di `.env` (`SUPABASE_DB_URL`) masih benar - kadang password perlu direset dari dashboard Supabase kalau sudah lama tidak dipakai atau ada kebijakan keamanan yang berubah.
3. Kalau memang masalah dari Supabase dan berkepanjangan, sistem sementara tidak bisa dipakai sampai Supabase pulih (project ini belum punya failover ke database cadangan).

## 3. Deploy ke Produksi (Frontend di Vercel, Backend di Railway)

**Kenapa dipisah begini**: Vercel didesain untuk aplikasi frontend (atau fungsi serverless kecil), bukan server Node/Express yang jalan terus-menerus seperti `server/index.js` di project ini (dia memakai `app.listen()`, menyala terus menunggu request, bukan pola serverless). Railway (dan platform sejenis seperti Render) memang didesain untuk server Node biasa. Database tetap di Supabase seperti sekarang, tidak berubah.

Konfigurasi yang sudah disiapkan di project ini (tidak perlu dibuat manual):
- `vercel.json` (root) - beritahu Vercel cara build frontend, plus aturan redirect supaya semua URL (termasuk `/verify/KODE` untuk verifikasi QR) tetap memuat halaman aplikasi, bukan 404.
- `railway.json` (root) - beritahu Railway cara build dan menjalankan backend dari folder `server/`.
- `server/package.json` sudah punya script `start` yang dibutuhkan Railway.

### Langkah A: Deploy backend ke Railway (kerjakan ini DULUAN)

1. Buka https://railway.app, login/daftar (bisa pakai akun GitHub yang sama).
2. Klik "New Project" → "Deploy from GitHub repo" → pilih repo `nabill25/e-procurement`.
3. Railway akan otomatis mendeteksi `railway.json` dan mulai build. Kalau diminta pilih root directory, pastikan tetap di root repo (bukan `server/`) karena `railway.json` sudah mengatur `cd server` sendiri di dalam perintahnya.
4. Buka tab "Variables" di project Railway itu, isi SEMUA variabel berikut (nilai yang sama seperti di `server/.env` lokal Anda, KECUALI yang ditandai harus beda):
   - `SUPABASE_DB_URL` - sama seperti di lokal (koneksi ke database Supabase yang sama)
   - `JWT_SECRET` - **WAJIB nilai BARU yang berbeda dari development**, jangan pernah pakai yang sama. Generate lewat: `node -e "console.log(require('crypto').randomBytes(48).toString('hex'))"`
   - `JWT_EXPIRES_IN` = `8h`
   - `NODE_ENV` = `production` (ini mengaktifkan pembatasan CORS dan rate-limit yang lebih ketat, sesuai yang sudah disiapkan di kode)
   - `FRONTEND_URL` - isi setelah Langkah B selesai dan Anda tahu alamat Vercel-nya (misal `https://e-procurement-xxxx.vercel.app`)
   - `SMTP_HOST` = `smtp.gmail.com`, `SMTP_PORT` = `587`, `SMTP_SECURE` = `false`, `SMTP_USER`, `SMTP_PASS` (App Password), `SMTP_FROM` - sama seperti di lokal. Railway kemungkinan besar TIDAK memblokir port SMTP seperti jaringan lokal tadi, jadi email seharusnya benar-benar terkirim dari sini.
5. Setelah deploy selesai, Railway kasih Anda satu alamat (misal `https://e-procurement-production.up.railway.app`). Catat alamat ini - itu alamat backend Anda.
6. Buka `https://alamat-railway-anda.up.railway.app/api` di browser, harus muncul teks `{"message":"DPBJ UI E-Procurement API is running!"}`. Kalau muncul itu, backend sudah jalan.

### Langkah B: Deploy frontend ke Vercel

1. Buka https://vercel.com, login/daftar (bisa pakai akun GitHub yang sama).
2. Klik "Add New" → "Project" → pilih repo `nabill25/e-procurement` dari GitHub.
3. Vercel akan otomatis mendeteksi `vercel.json` dan mengisi pengaturan build. Biarkan default (Framework Preset: Vite).
4. Sebelum klik Deploy, buka bagian "Environment Variables", tambahkan:
   - `VITE_API_BASE` = `https://alamat-railway-anda.up.railway.app/api` (pakai alamat dari Langkah A poin 5, JANGAN lupa akhiran `/api`)
5. Klik Deploy. Tunggu beberapa menit.
6. Setelah selesai, Vercel kasih alamat (misal `https://e-procurement-xxxx.vercel.app`). Ini alamat aplikasi yang akan dibuka pengguna.
7. **Kembali ke Railway** (Langkah A poin 4), isi `FRONTEND_URL` dengan alamat Vercel ini, lalu redeploy backend supaya CORS mengizinkan alamat frontend yang benar.

### Sebelum deploy pertama kali (jangan lewatkan)

1. **Jalankan seluruh file migrasi** (`migrations/001_....sql` sampai yang terbaru, berurutan) ke database Supabase yang dipakai production, KALAU itu database yang berbeda dari yang dipakai development. Kalau memakai Supabase project yang sama seperti development sekarang, migrasinya sudah semua terpasang, tidak perlu diulang.
2. **Jalankan test suite** (`npm run test:e2e`) dan pastikan semuanya lulus sebelum deploy.
3. **Backup database** (bagian 1 di atas) sebelum deploy pertama kali, sebagai jaring pengaman.

### Setelah deploy

1. Buka alamat Vercel, coba login dengan salah satu akun sungguhan, pastikan tidak ada error dan data benar-benar muncul (bukan cuma halaman kosong).
2. Cek log Railway (dashboard Railway → project → tab "Deployments" → "View Logs") dan log Vercel (dashboard Vercel → project → tab "Logs") untuk memastikan tidak ada error yang muncul di production tapi tidak muncul waktu development.
3. Coba fitur yang memicu email (misalnya Undangan Klarifikasi di tab Kontrak) untuk konfirmasi SMTP benar-benar berfungsi dari Railway (yang kemungkinan besar tidak memblokir port SMTP seperti jaringan lokal development).
4. Beri tahu pengguna/staf yang akan memakai supaya mereka juga ikut mengecek dan melaporkan kalau ada yang aneh.

### Biaya

Railway dan Vercel keduanya punya paket gratis yang cukup untuk skala percobaan/demo (Railway: sekitar $5 kredit gratis per bulan untuk akun baru, cukup untuk 1 backend kecil yang tidak terus-menerus sibuk; Vercel: gratis penuh untuk project pribadi/kecil). Kalau nanti dipakai institusi dalam skala besar dengan trafik tinggi, pertimbangkan paket berbayar atau pindah ke infrastruktur resmi UI.

## 4. Monitoring (Pemantauan) Produksi

Saat ini project belum terhubung ke layanan monitoring khusus (seperti Sentry untuk pelacakan error, atau Uptime Robot untuk cek apakah server masih hidup). Untuk sistem yang benar-benar dipakai sehari-hari oleh banyak orang, disarankan menambahkan salah satu dari ini sebelum deploy produksi sungguhan:

- **Pelacakan error otomatis** (contoh: Sentry, gratis untuk skala kecil) - supaya kalau ada error di production, langsung ada notifikasi tanpa harus menunggu laporan dari pengguna.
- **Cek ketersediaan server** (contoh: UptimeRobot, gratis) - mengecek server secara berkala dari luar, kirim notifikasi (email/WhatsApp) kalau server tidak bisa diakses.
- **Log terpusat** - saat ini log cuma muncul di terminal tempat `npm run server` dijalankan, akan hilang kalau terminal ditutup. Untuk production, sebaiknya log disimpan ke file atau layanan log terpisah supaya bisa ditelusuri kalau ada masalah di masa lalu.

Ini belum dikerjakan di sesi ini karena butuh mendaftar akun di layanan pihak ketiga (keputusan yang sebaiknya melibatkan Anda/atasan, bukan diputuskan sepihak).

## 5. Uji Beban (Load Testing)

Sudah dilakukan uji beban dasar memakai `autocannon` (tool load-testing HTTP berbasis Node, terpasang sebagai dev dependency) untuk membuktikan server bisa menangani banyak pengguna bersamaan tanpa macet atau error.

**Cara menjalankan sendiri kapan saja:**
```
npm run load-test
```
(backend harus sudah jalan di `localhost:3001` sebelum menjalankan ini)

**Hasil terakhir** (simulasi ~15 pengguna aktif bersamaan dari 1 alamat IP kantor): server menangani permintaan dengan latency rata-rata 3-7 milidetik, nol error, nol timeout, throughput mencapai ribuan request/detik untuk endpoint sederhana. Login (yang melibatkan hashing password bcrypt, lebih berat secara komputasi) tetap di bawah 100 milidetik rata-rata bahkan dengan puluhan request bersamaan.

**Temuan penting dari load test ini**: percobaan awal dengan beban jauh lebih tinggi (20 koneksi tanpa batas jumlah request, berjalan 10 detik penuh) menghasilkan puluhan ribu request dalam hitungan detik - jauh melebihi rate limiter umum yang tadinya diatur 1000 request/15 menit per IP. Ini mengungkap batas itu sebenarnya terlalu ketat untuk skenario wajar sekalipun (kantor dengan banyak staf aktif bersamaan dari 1 alamat IP, atau navigasi SPA normal yang melakukan banyak fetch API per menit). Sudah diperbaiki: batas umum dinaikkan ke 600 request **per menit** (bukan per 15 menit) per IP - jauh lebih longgar untuk pemakaian wajar, tapi tetap jadi jaring pengaman terhadap lonjakan trafik ekstrem.

**Catatan jujur soal batasan uji ini**: ini uji beban dari komputer lokal ke server lokal (localhost), bukan simulasi trafik sungguhan lewat internet dengan latensi jaringan nyata, dan bukan simulasi ratusan/ribuan pengguna sungguhan sekaligus. Untuk keyakinan penuh sebelum dipakai institusi skala besar, sebaiknya diuji juga di lingkungan yang lebih mendekati production (server yang benar-benar akan dipakai, database Supabase yang sama, dari luar jaringan lokal).
