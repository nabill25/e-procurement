# DPBJ UI E-Procurement - Catatan Project

Dokumen ini adalah ingatan permanen soal project ini. Baca ini di awal setiap sesi kerja supaya tidak perlu dijelaskan ulang dari nol.

## Siapa yang kerja di sini

Pengguna baru bergabung di perusahaan ini (Direktorat Pengadaan Barang dan Jasa, Universitas Indonesia) dan mendapat tugas mengembangkan, memperbaiki, dan meregenerasi sistem e-procurement. Anggap pengguna sebagai orang awam soal istilah teknis - jelaskan dengan bahasa manusia yang sederhana, jangan pakai jargon tanpa penjelasan.

## Kondisi nyata di lapangan (penting!)

- Sistem lama **masih dipakai di production sekarang juga**. Kita **tidak punya akses** ke server production itu.
- Semua pekerjaan hanya dilakukan di komputer lokal (lokal development), bisa pakai Docker (sudah terinstall di mesin ini, versi 29.7.2, tapi Docker Desktop kadang belum menyala - cek dulu dengan `docker info` sebelum pakai).
- Cara "deliver" hasil kerja ke atasan: **kirim selisih/diff** dari perubahan kode. Kalau ada perubahan struktur database (tabel baru, kolom baru, dll), buat **file migrasi terpisah**, lalu atasan yang akan menjalankan migrasi itu di production dulu, baru kode baru ditimpakan.
- Karena itu, **versioning pakai git secara lokal itu wajib** supaya setiap perubahan tercatat rapi dan gampang diambil selisihnya kapan saja.

## Aturan kerja yang wajib diikuti (permintaan eksplisit dari pengguna)

1. **0% asumsi.** Sebelum bertindak atau menjawab, cari tahu dulu faktanya (baca file, cek struktur, cek isi database, dll). Jangan menebak.
2. **Bahasa manusiawi**, gampang dimengerti orang awam. Hindari jargon tanpa penjelasan.
3. **Jangan pernah pakai tanda em dash (—)** di manapun, di teks jawaban maupun di file yang ditulis. Kalau perlu jeda kalimat, pakai koma, titik, atau kata penghubung biasa.
4. **Commit message**: singkat, padat, jelas, ditulis untuk dibaca manusia (bukan gaya AI). **Jangan pernah** menambahkan baris "Co-Authored-By" di commit message untuk project ini.
5. Commit git hanya dibuat kalau memang diminta atau memang bagian dari alur kerja yang sudah disepakati (misalnya commit checkpoint setelah suatu pekerjaan selesai).

## Identitas git untuk project ini

Repo sudah di-`git init` secara lokal (tidak ada remote, tidak di-push kemana-mana kecuali diminta). Identitas commit di-set **khusus untuk repo ini saja** (bukan config git global di komputer):
- `user.name` = `Nabil`
- `user.email` = `raslinabil25@gmail.com` (ini email yang sudah tersimpan di git config global komputer ini sebelumnya)

## Arsitektur project: dua sistem dalam satu folder

### 1. Sistem LAMA (referensi) - folder `eproc/`

Ini adalah source code aplikasi yang **sedang berjalan di production**. Kita punya source code-nya sebagai referensi, tapi **bukan** akses ke server production yang sebenarnya.

- Framework: **CodeIgniter 3** (PHP), folder aktifnya adalah `eproc/application/` (bukan `eproc/app/` atau `eproc/application-`/`application--` yang adalah folder backup/percobaan lama, jangan bingung dengan itu).
- Ada juga `eproc/app/` yang isinya adalah **percobaan migrasi ke CodeIgniter 4 yang sepertinya ditinggalkan** (cuma ada beberapa controller: API, Home, TestDB, dan 3 model saja). Jangan dianggap sebagai sumber kebenaran, itu cuma eksperimen lama yang belum selesai.
- Database asli: **PostgreSQL** (bukan MySQL, walaupun ada docker-compose yang menyebut MySQL, itu untuk skenario development CI3 yang berbeda dan tidak konsisten dengan `eproc/.env` yang sebenarnya pakai driver Postgre).
- Ada integrasi CAS SSO ke `sso.ui.ac.id` (lihat `eproc/vmstools/.env`) dan referensi host `dev-eproc.ui.ac.id`.
- **218 tabel database**, terbagi jadi kurang lebih 18 modul besar (daftar lengkap di bagian "Peta modul sistem lama" di bawah).
- **184 file controller** di `eproc/application/controllers/` (banyak yang polanya `*_json.php`, itu adalah endpoint AJAX/JSON yang menyuplai data ke tabel/grid di halaman, bukan halaman utuh).

### 2. Sistem BARU (yang sedang dikembangkan) - folder root: `src/`, `server/`, `public/`

Ini yang sedang dibangun untuk **menggantikan** sistem lama, dengan kerangka lebih modern.

- Frontend: **React 18 + Vite** di folder `src/`. Jalan di port **5173**.
- Backend: **Node.js + Express** di folder `server/`. Jalan di port **3001**. Routing API di `server/routes/*.js`.
- Database: **PostgreSQL**, lewat library `pg`. Konfigurasi koneksi ada di `server/db.js`.
- Styling: Tailwind CSS.
- State management: React Context (`src/context/AppContext.jsx`), autentikasi pakai JWT disimpan di localStorage (`dpbj_token`).
- Frontend punya **fallback demo login** kalau backend tidak bisa dihubungi (lihat `AppContext.jsx` fungsi `login`), supaya tetap bisa dicoba tanpa backend.

## Database: pilihan yang sudah disepakati

Pengguna memilih pakai **Supabase (PostgreSQL cloud)** untuk development lokal, BUKAN PostgreSQL lokal di Docker. Kredensialnya sudah ada di dua tempat:
- `.env` (root)
- `server/.env` (sudah disamakan, ditambahkan `SUPABASE_DB_URL` supaya server selalu connect ke Supabase ini, tidak peduli dijalankan dari folder mana)

Urutan prioritas koneksi di `server/db.js`: `SUPABASE_DB_URL` > `SUPABASE_DB_PASSWORD` (format fallback) > variabel `DB_HOST`/`DB_USER`/dst (lokal, dipakai sebagai cadangan kalau Supabase kosong).

**PENTING soal keamanan:** file `.env` berisi password asli, jangan pernah dikomit ke git (sudah diblokir lewat `.gitignore`). Jangan tempel isi file `.env` di percakapan atau di file lain yang bisa ter-share.

## Peta modul sistem lama (dari 218 nama tabel di database)

Ini daftar modul besar sistem lama, dipakai sebagai acuan "apa saja yang perlu ada" kalau membandingkan dengan sistem baru:

1. **Vendor/Rekanan** - registrasi, kualifikasi, akta, saham, pengurus, neraca, pajak, sertifikat, tenaga ahli, pengalaman, bank, checklist
2. **Paket/Tender** - data paket, jenis, metode lelang, tahapan, panitia, dokumen, peserta, penawaran, pemenang
3. **Aanwijzing** - rapat penjelasan sebelum tender (chat, hasil, kualifikasi, validasi)
4. **Evaluasi** - modul paling detail, puluhan sub-tabel (evaluasi administrasi, teknis, harga, kualifikasi, personil, peralatan, sertifikat, dst), dari sisi panitia maupun sisi rekanan
5. **Negosiasi** - negosiasi harga/item, shoutbox negosiasi
6. **Sanggah/Objections** - keberatan peserta tender
7. **Contracting/Kontrak** - SPK, deliverable, pembayaran, sanksi, SLA, jenis kontrak, status kontrak
8. **Purchasing** - pemenang purchasing, file purchasing
9. **Katalog (e-catalog)** - produk katalog, kategori, foto, lampiran, riwayat harga, laporan
10. **Permohonan Paket / RUP** - pengajuan kebutuhan, analisa kebutuhan & pasar, approval, checklist, import dari SIRUP (Sistem Informasi Rencana Umum Pengadaan, punya LKPP)
11. **Integrasi SAP** - sinkronisasi purchase requisition dari SAP
12. **Blacklist** - vendor yang di-blacklist beserta filenya
13. **Chat/Pengaduan** - shoutbox, inbox, kategori komplain
14. **Data Master** - bank, mata uang, negara, satuan, incoterm, metode, direktorat, unit kerja, hari libur, dst
15. **User & Hak Akses Menu** - login, tipe user, struktur menu dan hak akses per menu (`tbl_m_menu`, `tbl_m_menu_akses`)
16. **Audit/Log** - log aktivitas, jejak rekam, log pengiriman email
17. **Konten/CMS** - banner, berita, FAQ, kebijakan, kontak
18. **Validasi QR** - validasi dokumen lewat QR code

## Status kelengkapan sistem baru dibanding sistem lama (per 2026-08-20)

Sudah ada API dan halaman untuk (walaupun kedalaman fiturnya kemungkinan masih lebih sederhana dari sistem lama):
- Auth (login, register, me, logout) - `server/routes/auth.js`
- Vendor: list, detail, update profil, verifikasi, suspend, block, kualifikasi, upload dokumen, pengalaman, rating - `server/routes/vendors.js`, halaman `src/pages/Vendor.jsx`, `VendorProfile.jsx`, `RegistrasiVendor.jsx`
- Tender: list, detail, buat, ubah status/tahap, registrasi peserta, upload penawaran, evaluasi peserta, tentukan pemenang, aanwijzing, sanggah/objections, kontrak - `server/routes/tenders.js`, halaman `src/pages/Tender.jsx`
- Katalog: CRUD - `server/routes/katalog.js`, halaman `src/pages/Katalog.jsx`
- Purchasing: list, detail, buat, ubah status - `server/routes/purchasing.js`, halaman `src/pages/Purchasing.jsx`
- Pengajuan: sync SAP, buat, submit, review, approve, reject - `server/routes/pengajuan.js`, halaman `src/pages/Pengajuan.jsx`
- Dashboard, Audit log - `server/routes/dashboard.js`, `audit.js`

Yang **belum ada sama sekali** di sistem baru (dicek dari daftar route yang benar-benar ada, bukan tebakan):
- Modul **Negosiasi**
- **Blacklist**: halaman frontend-nya sudah ada (`src/pages/Blacklist.jsx`) tapi **tidak ada route backend-nya**, jadi masih pakai data dummy/mock
- **Data Master** (bank, mata uang, negara, satuan, dst): belum ada CRUD di backend
- **Manajemen hak akses berbasis menu**: sistem baru masih pakai role tetap (admin/ppk/pokja/vendor) yang di-hardcode, belum ada sistem menu dinamis seperti `tbl_m_menu`/`tbl_m_menu_akses` di sistem lama
- **Chat/shoutbox dan sistem pengaduan (inbox complain)**
- **Konten/CMS** (berita, FAQ, kebijakan): kemungkinan besar masih statis di frontend, belum ada backend-nya
- **Validasi QR**

Catatan: menyamakan SEMUA modul di atas 1:1 adalah pekerjaan besar (skala berbulan-bulan, bukan sekali kerja). Sebelum mengerjakan modul besar berikutnya, **cek dulu prioritas dengan pengguna**, jangan langsung membangun semuanya sekaligus tanpa konfirmasi.

## Data sensitif yang HARUS dijaga

Ditemukan beberapa hal sensitif di dalam project ini. Aturan mainnya:

1. **Jangan pernah komit file `.env` apapun ke git** (root, `server/`, `eproc/`, `eproc/vmstools/` semua punya file `.env` berisi kredensial asli). Sudah diblokir di `.gitignore`.
2. **File dump database berikut TIDAK dikomit ke git** karena berisi data asli (email vendor sungguhan, dll) atau formatnya belum bisa dipastikan aman:
   - `eproc/eproc_migrasi.sql` (dipastikan berisi data asli, ada alamat email vendor sungguhan)
   - `eproc/dump-eproc_migrasi-*.sql` dan `database/dump-eproc_migrasi-*.sql` (formatnya ternyata dump biner PostgreSQL custom format, bukan teks SQL biasa meskipun ekstensinya `.sql`, isinya belum bisa dipastikan aman karena tidak ada tool `pg_restore` di komputer ini untuk mengeceknya dengan benar)
   - `eproc/vmstools/eproc_migrasi_*.tgz` (arsip backup database, ~6MB)
3. File `supabase_migration_full.sql`, `supabase_clean.sql`, `supabase_migration.sql`, `database/schema_mysql.sql` **aman** dan boleh dikomit, sudah dicek isinya cuma struktur tabel (schema), bukan data asli.
4. Password Supabase asli ada di `.env` (root) dan `server/.env`: `Maianabil041123.` (akun pribadi, kemungkinan milik developer sebelumnya bernama Nabil).
5. Password database dev UI asli ada di `eproc/vmstools/.env`: `3p1R4@fZ5!k` (untuk server `dev-eproc.ui.ac.id`). Ini kredensial institusi asli, bukan cuma data lokal.

## Cara menjalankan project secara lokal

Backend dan frontend dijalankan terpisah (bukan lewat Docker untuk saat ini, karena sudah sepakat pakai Supabase cloud sebagai database, jadi tidak perlu container database lokal):

```
# Terminal 1: backend (dari folder root, supaya .env yang kepakai konsisten)
npm run server
# Backend jalan di http://localhost:3001

# Terminal 2: frontend
npm run dev
# Frontend jalan di http://localhost:5173
```

Frontend sudah otomatis memanggil backend di `http://localhost:3001/api` (lihat `API_BASE` di `src/context/AppContext.jsx`, ini nilai yang di-hardcode, bukan dari env variable).

Docker **tersedia** di komputer ini (`docker --version` = 29.7.2, `docker compose` = v5.4.0) tapi Docker Desktop perlu dinyalakan manual dulu sebelum dipakai, cek dengan `docker info`. Docker compose yang ada di `eproc/` (`docker-compose.yml` dan `docker-compose.ci4.yml`) itu untuk menjalankan **sistem lama** (PHP), bukan untuk sistem baru. Sistem baru saat ini tidak punya docker-compose sendiri kecuali diminta untuk dibuatkan.

## Testing

Pengguna memilih testing **manual lewat browser** untuk saat ini (klik-klik langsung), belum pakai automated testing tools seperti Playwright. Kalau nanti diminta testing otomatis, baru disiapkan.
