# Catatan Keamanan - DPBJ UI E-Procurement

Dokumen ini bukan pengganti audit keamanan resmi/independen (penetration testing sungguhan butuh pihak ketiga dengan alat dan izin khusus). Ini adalah tinjauan mandiri (self-review) yang sistematis mengikuti kategori risiko umum OWASP Top 10, dilakukan lewat pembacaan kode langsung, bukan simulasi serangan otomatis. Cocok sebagai langkah awal, bukan langkah terakhir, sebelum sistem ini dipakai untuk data sungguhan dalam skala besar.

## Ringkasan status per kategori

| Kategori | Status | Catatan |
|---|---|---|
| Kontrol akses (siapa boleh apa) | Sudah dibenahi | Lihat detail di bawah |
| Kegagalan kriptografi (password, token) | Sudah dibenahi | bcrypt untuk password, JWT dengan secret acak |
| Injeksi (SQL, dll) | Aman | Parameterized query konsisten dipakai |
| Desain tidak aman | Sebagian dibenahi | Lihat catatan validasi kepemilikan data |
| Kesalahan konfigurasi keamanan | Sudah dibenahi | Helmet, CORS, rate limit terpasang |
| Komponen rentan/usang | 1 celah risiko rendah tersisa | Vite dev-server, tidak berlaku di production |
| Kegagalan identifikasi & autentikasi | Sudah dibenahi | Rate limit brute-force, captcha, password minimum |
| Kegagalan integritas software/data | Belum ada | Tidak ada checksum/signing untuk deploy - wajar untuk skala project ini |
| Kegagalan logging & monitoring | Sebagian ada | Log aktivitas ada di database, belum ada alerting real-time |
| Server-Side Request Forgery (SSRF) | Tidak berlaku | Tidak ada fitur yang mengambil URL dari input pengguna untuk diakses server |
| Ketahanan terhadap beban (denial of service) | Sudah diuji dan diperbaiki | Lihat `OPERASIONAL.md` bagian Uji Beban - rate limiter yang tadinya terlalu ketat (bisa memblokir pemakaian wajar) ditemukan dan diperbaiki lewat uji beban sungguhan |

## 1. Kontrol akses

**Ditemukan dan diperbaiki**: hampir seluruh API (sekitar 280 dari 295 endpoint) sebelumnya bisa diakses siapa saja tanpa login sama sekali. Sudah ditutup seluruhnya - detail lengkap ada di CLAUDE.md bagian "Menuju 100% Fungsional".

**Yang sudah diverifikasi bekerja:**
- Endpoint privat menolak permintaan tanpa token (401)
- Endpoint admin-only menolak role selain admin (403)
- Vendor tidak bisa mengakses/mengubah data vendor lain (`ownVendorDataOnly` di `vendors.js`, pengecekan serupa di `tenders.js`)
- Vendor tidak bisa melihat harga penawaran vendor lain (endpoint peserta tender dipisah jadi versi "semua peserta" untuk staf internal dan versi "punya saya sendiri" untuk vendor)
- Field approval kontrak (misalnya `approve_ppk`) sekarang dicek terhadap role pengirim, tidak bisa diisi sembarang role yang login

**Yang BELUM sepenuhnya tertutup** (dicatat sebagai keterbatasan yang diketahui, bukan diabaikan):
- Beberapa endpoint approval berjenjang untuk role yang belum punya UI sendiri (Kasubdit, Unit - bagian dari 10 role yang baru fondasinya dibangun) untuk sementara dibatasi ke admin, bukan role aslinya, karena role itu memang belum bisa login sungguhan di sistem baru ini.
- Belum ada pengujian menyeluruh terhadap kemungkinan IDOR (Insecure Direct Object Reference) di SEMUA endpoint yang menerima ID dari parameter URL - yang sudah dicek secara spesifik cuma titik-titik berisiko tinggi (profil vendor, dokumen, rekening bank, peserta tender). Endpoint lain yang menerima `:id` (kontrak, evaluasi, dst) mengasumsikan siapa saja yang login dan tahu ID-nya boleh akses - ini wajar untuk staf internal (admin/ppk/pokja saling percaya dalam alur kerja yang sama), tapi belum divalidasi eksplisit satu-satu.

## 2. Password dan token

- Password disimpan dengan `bcrypt` (hash satu arah, standar industri), bukan teks biasa maupun enkripsi yang bisa dibalik.
- Password minimal 8 karakter dicek saat registrasi (`server/routes/auth.js`).
- Sesi login pakai JWT (JSON Web Token) dengan masa berlaku 8 jam, ditandatangani pakai kunci rahasia acak 96 karakter (sebelumnya nilai contoh dari template yang sama untuk semua orang yang pernah clone kode ini - sudah diganti).
- **Catatan penting untuk deploy production**: `JWT_SECRET` yang dipakai sekarang HANYA untuk development. Sebelum deploy sungguhan, WAJIB generate nilai baru khusus production (lihat `OPERASIONAL.md` bagian deploy) dan jangan pernah pakai nilai yang sama antara development dan production.

## 3. Injeksi SQL

Dicek: seluruh query database di `server/routes/*.js` memakai parameterized query (`pool.query('... WHERE id = $1', [id])`), bukan menggabungkan string secara langsung. Ini pola yang mencegah SQL injection secara otomatis oleh library `pg`. Tidak ditemukan pola penggabungan string mentah untuk nilai dari pengguna di manapun.

Pengecualian yang disengaja dan aman: beberapa tempat menyisipkan NAMA TABEL secara dinamis (bukan nilai data) misalnya di `server/lib/upload.js` dan modul Data Master (`master.js`, `VALID_CATEGORIES` whitelist) - ini aman karena nama tabelnya dibatasi ke daftar tetap yang sudah dikodekan (whitelist), bukan diterima langsung dari input pengguna tanpa filter.

## 4. XSS (Cross-Site Scripting)

**Ditemukan dan diperbaiki**: `src/pages/PublicPolicyPage.jsx` merender HTML kebijakan (diisi admin lewat textarea) langsung ke halaman tanpa disaring. Kalau akun admin diretas (atau suatu saat role lain diberi akses tulis ke field ini), penyerang bisa menyisipkan script berbahaya yang akan berjalan di browser semua pengunjung halaman Kebijakan publik. Diperbaiki dengan menambahkan `dompurify` (library sanitasi HTML standar) sebelum konten dirender.

Bagian lain aplikasi yang menampilkan teks dari pengguna (nama vendor, judul tender, isi pesan, dst) aman karena React secara otomatis meng-escape teks biasa (`{variabel}`), cuma `dangerouslySetInnerHTML` yang perlu perhatian khusus - sudah dicek, cuma ada 1 titik itu di seluruh codebase.

## 5. Upload file

**Ditemukan dan diperbaiki**: semua 8 titik upload file sebelumnya menerima file APAPUN tanpa validasi tipe atau ukuran (bisa upload `.exe`, `.php`, dst ke folder yang disajikan publik lewat `/uploads`). Diperbaiki lewat `server/lib/upload.js`: whitelist ekstensi (dokumen umum, gambar, arsip), batas 10MB per file.

**Catatan**: validasi saat ini berdasarkan ekstensi file, bukan pemeriksaan isi file (magic bytes). Secara teori, file berbahaya bisa diberi nama ulang dengan ekstensi yang diizinkan (misalnya `virus.exe` diganti nama jadi `dokumen.pdf`). Untuk sistem yang menangani dokumen dari publik (termasuk registrasi vendor, upload dokumen tender), pertimbangkan menambah pemeriksaan isi file yang lebih dalam (magic byte checking) sebagai lapis tambahan sebelum production sungguhan, terutama karena folder `uploads/` disajikan langsung tanpa melalui pemrosesan apapun.

## 6. Rate limiting (mencegah brute-force dan penyalahgunaan)

- Endpoint login/register: 20 percobaan per 15 menit per alamat IP (di production; dilonggarkan ke 200 di development supaya tidak mengganggu kerja/testing normal).
- Seluruh API: 1000 permintaan per 15 menit per alamat IP, sebagai jaring pengaman dasar terhadap serangan otomatis skala besar.
- CAPTCHA sederhana (kode acak yang harus diketik ulang) sudah ada di form login dan registrasi, walau ini level dasar (bisa dilewati oleh bot yang cukup canggih) - untuk perlindungan lebih kuat, pertimbangkan layanan CAPTCHA pihak ketiga (misalnya Google reCAPTCHA) sebelum production sungguhan kalau sistem akan diakses publik luas.

## 7. Dependency (paket pihak ketiga) yang dipakai

Dicek lewat `npm audit` di kedua bagian (frontend root dan `server/`):
- **Backend (`server/`): 0 kerentanan.**
- **Frontend (root): 1 kerentanan tingkat sedang** di `esbuild`/`vite` (dev server saja, tidak aktif saat build production `npm run build` atau saat aplikasi sudah di-deploy). Perbaikannya butuh upgrade Vite dari versi 5 ke 8 (lompatan 3 versi besar, berisiko tinggi merusak build kalau dipaksakan tanpa pengujian menyeluruh). **Diputuskan untuk ditunda** (dikonfirmasi ke pengguna), dicatat di sini supaya tidak terlupakan - tinjau ulang kalau nanti ada waktu khusus untuk migrasi Vite dengan pengujian penuh.
- **`autocannon`** (dev dependency baru, dipakai untuk `npm run load-test`) membawa 1 kerentanan moderat di paket `uuid` versi lama (celah buffer bounds check). Ini murni tool baris-perintah untuk uji beban manual, tidak pernah ikut masuk ke bundle production maupun berjalan di server produksi - risikonya bisa diterima. Sudah dicek, tidak ada versi `autocannon` lebih baru yang memperbaiki ini (8.0.0 adalah versi terbaru yang tersedia).

## 8. Yang TIDAK tercakup tinjauan ini (batasan jujur)

Ini bukan daftar lengkap segala kemungkinan celah keamanan. Yang tidak dicek/tidak bisa dicek lewat cara ini:
- **Serangan lewat jaringan sungguhan** (packet sniffing, man-in-the-middle) - butuh environment jaringan sungguhan untuk diuji, tidak bisa lewat pembacaan kode.
- **Serangan terhadap infrastruktur Supabase/Vercel sendiri** - itu tanggung jawab penyedia layanan tersebut, bukan kode aplikasi ini.
- **Social engineering** (menipu pengguna/admin langsung) - di luar cakupan tinjauan kode.
- **Pengujian beban/DoS sungguhan** - rate limiter sudah terpasang tapi belum pernah diuji dengan traffic tinggi sungguhan.
- **Uji otomatis terhadap SEMUA endpoint satu-satu** (fuzzing) - yang dicek adalah pola dan titik-titik berisiko tinggi yang teridentifikasi lewat pembacaan kode dan audit form manual, bukan pengujian mekanis ke semua endpoint.

**Rekomendasi jujur**: kalau sistem ini akan menangani data sungguhan dalam skala institusi (bukan cuma demo), sebaiknya tetap dilakukan penetration testing oleh pihak ketiga bersertifikasi sebelum dianggap benar-benar siap produksi, terutama karena sistem ini menyimpan data finansial (nilai kontrak, HPS) dan data pribadi (dokumen vendor, rekening bank). Tinjauan di dokumen ini adalah langkah awal yang jujur dan sistematis, bukan pengganti proses formal itu.
