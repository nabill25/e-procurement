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
- **Blacklist** (selesai 2026-08-20): list publik + tambah manual - `server/routes/blacklist.js`, tabel `blacklist` (`migrations/002_tabel_blacklist.sql`), halaman `src/pages/Blacklist.jsx`. Aksi "Blokir Vendor" di halaman Vendor (`vendors.js` PATCH `/:id/block`) sekarang otomatis bikin entri di tabel `blacklist` juga, jadi dua fitur ini sudah tersambung.
- **Negosiasi** (selesai 2026-08-20): chat tawar-menawar harga antara Pokja/PPK dan vendor pemenang, plus tombol sepakati/gagalkan oleh Pokja/PPK - endpoint baru di `server/routes/tenders.js` (`GET/POST /:id/negotiation/:vendorId`, `POST /:id/negotiation/:vendorId/finalize`), kolom baru `negotiated_price`/`negotiation_status` di tabel `tender_participants`, tabel baru `tender_negotiation_chats` (`migrations/003_modul_negosiasi.sql`), komponen `src/components/modals/NegotiationTab.jsx` disambungkan sebagai tab baru "Negosiasi" di `DetailTenderModal.jsx` (muncul begitu tender sudah masuk tahap "pemenang" ke atas, ambil vendor pemenang otomatis dari data peserta).
- **Data Master** (selesai 2026-08-20): halaman admin baru untuk kelola data referensi (Bank, Mata Uang, Negara, Satuan, Incoterm, Metode Pembayaran, Unit Kerja) - `server/routes/master.js`, tabel `master_data` (kategori disimpan di kolom `category`, satu tabel untuk semua kategori sederhana yang bentuknya sama) dan `unit_kerja_master` (tabel sendiri karena datanya lebih detail) di `migrations/004_data_master.sql`, halaman `src/pages/DataMaster.jsx`, menu baru "Data Master" di sidebar (khusus role admin).
- **Manajemen Hak Akses Menu** (selesai 2026-08-20): menu sidebar sekarang diatur dari database (tabel `menu_items` + `menu_role_access`, `migrations/005_hak_akses_menu.sql`), bisa diatur admin lewat halaman baru `src/pages/MenuAccess.jsx` (menu "Hak Akses Menu" di sidebar). **Penting soal keamanan navigasi**: `src/components/layout/Sidebar.jsx` punya jaring pengaman (`getDefaultAllowedMenus`) yang berisi aturan bawaan persis seperti logika lama yang di-hardcode. Kalau server/database tidak bisa dihubungi, sidebar otomatis pakai aturan bawaan ini, jadi navigasi TIDAK PERNAH kosong/rusak. Data dari API cuma dipakai kalau berhasil didapat.

- **Pengaduan / Pusat Pesan** (selesai 2026-08-20): form "Kontak Kami" yang sebelumnya cuma tampilan (pesan hilang begitu saja saat dikirim) sekarang benar-benar tersimpan - `server/routes/inbox.js`, tabel `inbox_messages` + `inbox_categories` (`migrations/006_modul_pengaduan.sql`), halaman admin baru `src/pages/Inbox.jsx` ("Pusat Pesan" di sidebar) untuk baca dan balas pesan. `src/pages/KontakKami.jsx` sudah disambungkan ke `POST /api/inbox`.

- **Konten/CMS - Berita & FAQ** (selesai 2026-08-20): `server/routes/cms.js`, tabel `cms_news` + `cms_faq` (`migrations/007_konten_cms.sql`), halaman admin baru `src/pages/ContentManagement.jsx` ("Kelola Konten" di sidebar), tampil otomatis di halaman utama publik (`PublicLandingPage.jsx`, komponen `NewsAndFaqSection`, section ini otomatis sembunyi kalau belum ada berita/FAQ yang dipublikasikan). **Sengaja tidak termasuk** "Banner" (carousel hero yang sudah ada di halaman utama sengaja tidak diubah, sudah bagus dan jalan baik pakai data sendiri) dan "Kebijakan" (belum dibuat, bisa ditambahkan lagi kalau memang dibutuhkan).

- **Validasi QR** (selesai 2026-08-20): sistem lama sebenarnya **tidak pernah menyelesaikan** fitur ini (controller-nya kosong, tidak ada tampilannya sama sekali), jadi dibangun berdasarkan struktur tabel `qr_validasi` yang ada (maksud aslinya: cek keaslian dokumen resmi lewat kode QR). `server/routes/qr.js`, tabel `qr_validations` (`migrations/008_validasi_qr.sql`). Ditambahkan dependency baru **`qrcode`** (npm package) di `server/package.json` untuk bikin gambar QR asli, sebelumnya tidak ada. Tombol "Buat Kode QR" ada di tab Kontrak pada detail tender (`ContractTab.jsx`, khusus role admin/PPK setelah kontrak dibuat). Halaman publik baru `src/pages/QrVerify.jsx` ("Cek Dokumen" di menu publik) untuk masukkan kode manual, ATAU otomatis kebuka & langsung tercek kalau link `http://localhost:5173/verify/KODE` dibuka langsung (misal dari hasil pindai kamera HP) - ini pakai pengecekan `window.location.pathname` sederhana di `AppContext.jsx`, BUKAN library routing (react-router), supaya tidak mengubah cara kerja navigasi yang sudah ada. **Catatan untuk nanti kalau sudah waktunya deploy sungguhan**: server hosting production perlu diatur supaya path seperti `/verify/KODE` tetap mengarah ke `index.html` (fallback SPA), kalau tidak nanti muncul halaman 404 dari server saat link itu dibuka langsung (bukan dari dalam aplikasi). Sekarang di `npm run dev` ini otomatis jalan karena Vite dev server memang begitu perilakunya.
- Ketemu juga bug lama yang tidak berhubungan dengan tugas ini: `ContractTab.jsx` sebelumnya pakai ikon `CheckCircle2` tanpa di-import, jadi bisa bikin halaman Kontrak error kalau dokumen SPK/BAST sudah pernah diunggah. Sudah ikut diperbaiki.

Semua 7 modul yang disepakati di awal (Blacklist, Negosiasi, Data Master, Hak Akses Menu, Pengaduan, Konten/CMS, Validasi QR) **sudah selesai** per 2026-08-20. Setiap modul sudah dites sendiri (bukan cuma nulis kode) sebelum dilaporkan selesai: lewat API langsung (curl) untuk backend, dan lewat permintaan ke dev server Vite untuk memastikan file frontend tidak error kompilasi.

Modul lanjutan yang BELUM dikerjakan (bukan bagian dari 7 modul awal, disebutkan waktu bahas modul Konten/CMS, baru dikerjakan kalau memang diminta): Banner (kelola gambar carousel halaman utama) dan Kebijakan (halaman kebijakan publik).

## Tahap kedua: memperdalam modul yang sudah ada tapi masih lebih simpel dari sistem lama

Setelah 7 modul di atas selesai, pengguna bertanya apakah SEMUA modul eproc sudah diterapkan. Jawabannya: kerangka utamanya sudah lengkap dan bisa dipakai dari awal sampai akhir, tapi beberapa bagian masih jauh lebih simpel dibanding sistem lama yang sudah bertahun-tahun dikembangkan. Pengguna minta diperdalam juga, urutan yang disepakati:
1. Evaluasi Tender - **selesai 2026-08-20**
2. Kualifikasi Vendor (SIKaP) - **selesai 2026-08-20**
3. Kontrak (termin pembayaran, sanksi, progres pekerjaan) - **selesai 2026-08-20**
4. RUP/Permohonan Paket (analisa kebutuhan & pasar) - **selesai 2026-08-20**
5. Integrasi SAP - **sengaja tetap simulasi**, karena butuh akses/kredensial SAP asli milik UI yang tidak dimiliki, bukan sesuatu yang bisa dibuat "asli" tanpa itu

**Tahap kedua ini sudah selesai semua** (4 dari 4 yang dikerjakan, 1 sengaja tetap simulasi karena keterbatasan akses eksternal, sudah dijelaskan ke pengguna).

### Kualifikasi Vendor (SIKaP) - lengkapi data yang kurang (selesai 2026-08-20)

Sebelum mengerjakan ini, saya cek dulu halaman Profil & Kualifikasi Vendor yang sudah ada (`src/pages/VendorProfile.jsx`), ternyata sudah lebih lengkap dari dugaan awal:
- Dokumen legalitas (Akta, NIB, NPWP, SKT, SPT) sudah bisa diunggah lewat tab "Legalitas" (tabel `vendor_documents`)
- Pajak, Tenaga Ahli, Peralatan, dan Pengurus sudah ada tab masing-masing (kolom jsonb di tabel `vendors`, lewat komponen reusable `GenericArrayTab` di `src/components/profile/SikapTabs.jsx`)

Yang benar-benar belum ada: **data rekening bank** dan **neraca keuangan**. Ditambahkan dengan cara yang sama persis seperti yang sudah ada (kolom jsonb baru `bank` dan `neraca` di tabel `vendors`, `migrations/010_kualifikasi_vendor_detail.sql`), plus 2 tab baru "Bank" dan "Neraca" di halaman Profil Vendor. Juga ditambahkan 2 pilihan jenis dokumen baru yang belum ada di dropdown ("Sertifikat" dan "Ijin Usaha"), memakai tabel `vendor_documents` yang sudah ada (tidak perlu tabel baru).

**Catatan teknis penting yang ditemukan waktu testing** (bukan bug, tapi gampang salah kalau lupa): endpoint `POST /api/vendors/:id/documents` itu `:id`-nya adalah **users.id** punya vendor (bukan vendors.id / id baris di tabel vendors), karena `vendor_documents.vendor_id` foreign key ke tabel `users`, bukan ke tabel `vendors`. Ini konsisten dengan pola di seluruh aplikasi (vendor_id di tender_participants, katalog_items, dst juga selalu berarti users.id), jadi bukan hal baru, cuma dicatat di sini supaya tidak bingung lagi kalau testing manual.

Sudah dites lewat API: simpan data bank, simpan data neraca, cek data kebaca lagi lewat endpoint qualifications, upload dokumen dengan jenis baru - semua berhasil.

### Kontrak - termin pembayaran, sanksi, progres pekerjaan (selesai 2026-08-20)

Sebelumnya di tab Kontrak cuma bisa unggah SPK dan BAST saja. Sekarang ditambah 3 hal, mengikuti tabel `contracting_payment`, `contracting_sanksi`, `contracting_deliverable` di sistem lama:
- **Termin Pembayaran**: tabel `contract_payment_terms`, bisa tambah termin (nama, nilai, persentase progres), tandai sudah dibayar.
- **Sanksi Keterlambatan**: tabel `contract_penalties`, catat hari terlambat, tarif denda, nilai denda.
- **Progres Pekerjaan**: tabel `contract_deliverables`, tambah item pekerjaan/deliverable, update persentase progres, otomatis tercatat tanggal selesai kalau progres 100%.

Semua di `migrations/011_kontrak_detail.sql`. Endpoint baru di `server/routes/tenders.js` (nested di bawah `/api/tenders/:id/contract/...`, konsisten dengan pola endpoint lain). Komponen frontend baru `src/components/modals/ContractDetailSections.jsx` (3 komponen: `PaymentTermsSection`, `PenaltiesSection`, `DeliverablesSection`), disambungkan ke `ContractTab.jsx`, muncul begitu kontrak sudah dibuat.

**Sengaja tidak termasuk "SLA"** (contracting_sla di sistem lama) karena itu spesifik untuk kontrak jenis layanan/maintenance saja, lingkupnya lebih sempit dibanding 3 hal di atas yang berlaku untuk hampir semua jenis kontrak. Bisa ditambahkan nanti kalau memang dibutuhkan.

Sudah dites lewat API dari ujung ke ujung: buat kontrak → tambah termin → tandai dibayar → catat sanksi → tambah item progres → update progres jadi 100% (otomatis status "selesai" dan tanggal terima terisi). Semua berhasil.

### RUP/Permohonan Paket - Analisa Kebutuhan & Pasar (selesai 2026-08-20)

Mengikuti tabel `analisa_kebutuhan`, `analisa_pasar`, dan `permohonan_paket_analisa` di sistem lama. Jenis analisa kebutuhan/pasar (cuma daftar nama) ditambahkan sebagai 2 kategori baru di **Data Master** yang sudah ada (`analisa_kebutuhan`, `analisa_pasar` - bukan tabel baru, reuse `master_data`), diisi 3 pilihan awal masing-masing yang lazim dipakai dalam analisa pengadaan pemerintah, admin bisa tambah/ubah lewat halaman Data Master. Field analisa yang sesungguhnya (komoditas, analisa kebutuhan dipilih, analisa pasar dipilih, identifikasi risiko, keterangan risiko) ditambahkan langsung ke tabel `procurement_requests` karena sifatnya satu-ke-satu per pengajuan.

Semua di `migrations/012_rup_analisa.sql`. Form pengajuan (`NewProcurementModal.jsx`) dapat step baru "Analisa Kebutuhan & Pasar" (step ke-4 dari 5, sebelum step Dokumen). Ringkasannya juga muncul di `DetailPengajuanModal.jsx` untuk direview PPK/admin, termasuk tanda peringatan kalau ada risiko yang teridentifikasi.

**Sengaja tidak termasuk**: "Jenis Belanja" dan "Kategori Permohonan" (tabel referensi terpisah tapi di luar cakupan "analisa kebutuhan & pasar" yang diminta), "Matrix Status" (itu tabel konfigurasi alur kerja admin, bukan data analisa), dan "Checklist" (butuh tabel `master_checklist` yang belum ada, cakupan terpisah).

**Bug lama yang ditemukan dan ikut diperbaiki** (bukan bagian dari modul ini, tapi ditemukan waktu testing menyeluruh): endpoint `POST /api/pengajuan` sudah dari awal mengacu ke kolom `budget_code`, `description`, `technical_spec`, `quantity`, `unit_of_measure`, `needed_by_date` di query-nya (dan form frontend-nya juga sudah punya field ini sejak awal), tapi kolom-kolom itu **tidak pernah benar-benar dibuat di database**. Jadi kalau ada yang mengisi pengajuan dengan field-field itu terisi, akan selalu gagal. Sudah diperbaiki lewat `migrations/013_perbaikan_procurement_requests.sql`.

Sudah dites lewat API: buat pengajuan lengkap dengan semua field (termasuk yang tadinya bikin error) sampai berhasil tersimpan dan terbaca lagi dengan benar.

### Evaluasi Tender - detail per kategori (selesai 2026-08-20)

Sebelumnya sistem baru cuma punya 1 skor gabungan (`technical_score` di `tender_participants`). Sekarang ditambah sistem evaluasi per kategori yang lebih detail, mengikuti pola sistem lama (tabel `paket_eval_*` dan `rekanan_eval_*`), lewat tabel baru `tender_eval_criteria` (kriteria per tender per kategori: administrasi, teknis, harga, kualifikasi, personil, peralatan, sertifikat lain, pengalaman, syarat pendaftaran) dan `tender_eval_scores` (skor per vendor per kriteria) di `migrations/009_evaluasi_detail.sql`.

**Catatan penting soal batasan yang disengaja**: beberapa kategori di sistem lama (terutama "pengalaman" dan "personil") punya rumus penilaian yang sangat spesifik, sepertinya mengikuti aturan resmi pengadaan pemerintah (LKPP) - ada kolom-kolom seperti `bp_nilai`, `nk1_rp`, `nk2_rpmin/rpmax` di tabel `paket_eval_pengalaman` yang jelas mengikuti rumus resmi tertentu. **Rumus itu TIDAK ditiru** karena tidak ada dokumen resmi acuannya di project ini, dan saya tidak mau menebak-nebak rumus yang berkaitan dengan kepatuhan pengadaan (bisa berakibat serius kalau salah). Sebagai gantinya, semua kategori evaluasi memakai cara yang sama: Pokja bikin daftar kriteria, lalu nilai tiap vendor secara manual per kriteria (skor + catatan + memenuhi syarat/tidak). Kalau nanti pengguna punya dokumen resmi rumusnya, tabel ini sudah siap dipakai, tinggal ditambahkan logika hitung otomatisnya.

Cara pakai: di detail tender, tab "Peserta & Penawaran", ada tombol baru "Evaluasi Detail" per vendor (muncul saat tender di tahap evaluasi/pemenang) yang membuka `EvaluationDetailModal.jsx`. Ini terpisah dan TIDAK menggantikan alur skor/LULUS/GUGUR yang sudah ada sebelumnya (yang menentukan pemenang) - evaluasi detail ini sifatnya pendukung/pencatatan detail saja.

Sudah dites lewat API: tambah kriteria, simpan skor, update skor (replace bukan duplikat), hapus kriteria - semua berhasil.

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

## Cara kerja mulai 2026-08-20: testing dilakukan sendiri, bukan minta pengguna coba dulu

Pengguna minta supaya setiap modul selesai dibangun, **saya (Claude) yang testing dan pastikan jalan tanpa error dulu** (lewat curl/API, alur lengkap end-to-end), bukan menyuruh pengguna coba manual duluan. Baru setelah saya yakin semuanya jalan, laporkan ke pengguna dan persilakan mereka coba juga kalau mau. Ini berlaku untuk semua modul berikutnya.

Soal migrasi database: pengguna **tidak perlu** menambahkan apapun manual di Supabase. Setiap file migrasi baru langsung dijalankan sendiri lewat script node ke Supabase (karena ini database development, bukan production asli). Kalau nanti sudah waktunya ke production sungguhan, baru file migrasi diserahkan ke atasan untuk dijalankan duluan.

## Folder migrations/

Semua perubahan struktur database (tabel/kolom/view baru) ditulis sebagai file `.sql` bernomor urut di folder `migrations/` (contoh: `migrations/001_lengkapi_skema_awal.sql`). Untuk database Supabase yang dipakai development ini, migrasi dijalankan langsung (karena ini bukan production asli, aksesnya memang untuk development). File migrasi tetap dibuat dan disimpan supaya ada jejak historis dan supaya polanya siap dipakai kalau nanti memang harus diserahkan ke orang lain untuk dijalankan di server lain.

## Status terakhir: sudah jalan normal (2026-08-20)

Backend (`npm run server`, port 3001) dan frontend (`npm run dev`, port 5173) sudah dicoba dijalankan bersamaan dan berhasil, backend konek ke Supabase, halaman utama berhasil tampil.

Sempat ditemukan skema database Supabase belum lengkap dibanding yang dibutuhkan kode backend (sudah begitu dari sebelum sesi ini, bukan sesuatu yang baru rusak), plus beberapa bug penamaan kolom di kode. Semua sudah diperbaiki:

- `migrations/001_lengkapi_skema_awal.sql` sudah dibuat dan dijalankan ke Supabase: menambahkan kolom yang kurang di tabel `vendors` (email, phone, province, nib, contact_person, qualification_class, blacklisted, verified_by, verified_at), `users` (unit_kerja), `tender_participants` (document_path, technical_score, evaluation_notes, is_evaluated, is_winner), `audit_logs` (is_success), membuat view `v_dashboard_stats`, dan menyamakan data status vendor yang tadinya `'verified'` jadi `'terverifikasi'` (istilah yang dipakai konsisten di kode).
- Bug kode yang diperbaiki: `server/routes/auth.js` (kolom `password_hash`/`is_active` yang salah, seharusnya `password`/`status`, bikin fitur daftar akun baru gagal total sebelumnya), `server/routes/purchasing.js` (kolom `u.name` seharusnya `u.full_name`), `server/routes/tenders.js` (query daftar tender salah join ke tabel `bids` yang tidak ada, seharusnya hitung dari `tender_participants`; query sanggahan dan kontrak salah ambil `company_name` dari tabel `users`, seharusnya dari tabel `vendors`).

Semua 8 endpoint utama sudah dites ulang satu per satu dan jalan normal: `/api/dashboard`, `/api/tenders`, `/api/vendors`, `/api/purchasing`, `/api/katalog`, `/api/audit`, `/api/pengajuan`, `/api/auth` (login, register, me).

**Akun untuk testing manual di browser** (dibuat lewat `server/seed_real_users.js`, sudah ada di database Supabase):
| Role | Username | Password |
|---|---|---|
| Admin | admin@ui.ac.id | UIAdmin2026! |
| PPK | ppk@ui.ac.id | UIPPK2026! |
| Pokja | pokja@ui.ac.id | UIPokja2026! |
| Vendor | vendor@gmail.com | UIVendor2026! |

Catatan: data di database Supabase saat ini masih sangat sedikit (cuma 1 vendor, belum ada tender/pengajuan/katalog), jadi kebanyakan halaman list akan terlihat kosong sampai ada yang mengisi data lewat form di aplikasi atau lewat seed script tambahan.

## Perbaikan dan temuan tambahan (2026-08-20/21)

Setelah semua modul di atas selesai, pengguna mencoba lewat browser sungguhan dan menemukan beberapa hal:

### Bug: halaman Profil & Kualifikasi Vendor layar putih kalau diakses Admin

Penyebab: menu "Profil & Kualifikasi" (`vendor_profile`) itu khusus untuk role Vendor kelola profil perusahaan sendiri, tapi Admin ikut bisa lihat menu ini (warisan dari logika lama yang di-hardcode). Saat Admin klik, halaman minta data vendor dengan ID akun Admin, yang jelas tidak ada (Admin bukan vendor), dan komponen `SikapTabs.jsx` (Pajak, Pengurus, Tenaga Ahli, Peralatan, Bank, Neraca) langsung crash karena akses `vendor.pajak` dkk padahal `vendor` masih `null` - tidak ada penanganan kondisi ini jadi React langsung blank.

Sudah diperbaiki:
- `src/pages/VendorProfile.jsx` sekarang menampilkan pesan yang jelas ("Data Vendor Tidak Ditemukan") kalau akun yang login bukan vendor, bukan crash.
- Semua field di `SikapTabs.jsx` dikasih optional chaining (`vendor?.pajak` dst) sebagai lapis pengaman tambahan.
- Menu `vendor_profile` sudah dicabut dari akses Admin (baik di database lewat Hak Akses Menu, maupun di aturan bawaan `getDefaultAllowedMenus` di `Sidebar.jsx`). Sekarang cuma role `vendor` yang lihat menu ini.

### Menu "Portal Publik" dibatasi hanya untuk Admin

Sebelumnya semua role bisa lihat tautan ini di sidebar. Pengguna minta dibatasi, sekarang cuma Admin yang lihat (`src/components/layout/Sidebar.jsx`).

### Tombol Login diubah jadi lonjong warna navy

`src/components/modals/LoginModal.jsx`: tombol Login sekarang berlatar navy penuh (bukan cuma garis tepi), bentuknya tetap lonjong (`rounded-full`).

## Tahap ketiga: Struktur Role Lengkap + Multi-Role (fondasi sebelum pengembangan UI/UX) - selesai 2026-08-21

Pengguna bertanya apakah role di sistem baru sudah sama dengan sistem lama, dan menyebut soal "menu multi role" yang muncul saat login di sistem lama. Setelah diriset langsung ke kode PHP sistem lama, ternyata ini temuan besar:

**Sistem lama punya 14 role aktif** (dicek dari data asli tabel `user_type` di `eproc_migrasi.sql`, kolom `aktif='1'`): ADMINISTRATOR, ADMIN VMS, POKJA, ADMINISTRATOR APPROVAL, PENYEDIA, MANAGER PENGADAAN, PENGGUNA, AUDIT, PELAKSANA PENGADAAN, PENGELOLA KONTRAK, APPROVAL VMS, KASUBDIT KONTRAK, PERENCANAAN, PPK. (Ada 10 role lagi yang `aktif='0'`/nonaktif, tidak dimasukkan.)

**Mekanisme "multi role"**: satu akun (`user_login`) bisa terdaftar dengan LEBIH DARI SATU role sekaligus, disimpan di tabel `USER_LOGIN_MULTI` (kode PHP: `eproc/application/models/userloginmulti.php`, view popup-nya: `eproc/application/views/main/user_login_multi.php`). Kalau akun itu punya lebih dari satu role, muncul popup untuk pilih role mana yang mau dipakai untuk sesi itu. Fungsi `excSplitRole()` di `eproc/application/controllers/users_base_json.php` (baris ~1572) yang menjalankan pergantian: menyalin role yang dipilih ke akun utama (`user_login.USER_TYPE_ID`), mencatat riwayatnya di `USER_LOGIN_MULTI_REKAM`, lalu sesi di-refresh (`reloadlocalAuthenticate`). Beberapa role juga punya jenjang (level): Perencanaan punya Staff/Kasi/Kasubdit, Pejabat Pengadaan punya Staff/Kasi, Pengelola Kontrak punya tahap Persiapan/Pengendalian/Penyelesaian dengan level Staff/Kasi.

Sistem baru sebelumnya cuma punya **4 role tetap** (admin/ppk/pokja/vendor), satu akun cuma bisa satu role, tidak ada mekanisme ganti role. Pengguna minta ini dibangun sebagai fondasi SEBELUM mulai pengembangan UI/UX semua role.

### Yang sudah dibangun

**Database** (`migrations/014_struktur_role_lengkap.sql`):
- `role_definitions` - daftar semua role yang dikenal sistem, sudah diisi 14 role aktif dari sistem lama + 4 role yang sudah dipakai sistem baru (totalnya 14 baris karena admin/ppk/pokja/vendor sudah termasuk yang 14 itu).
- `user_roles` - role apa saja yang dimiliki satu akun (bisa lebih dari satu baris per akun), ada kolom `level` untuk role yang punya jenjang, dan `is_primary` untuk role utama.
- `user_role_switch_history` - riwayat tiap kali akun ganti role aktif (setara `USER_LOGIN_MULTI_REKAM`).
- **Penting**: kolom `users.role` TETAP dipakai sebagai "role yang sedang aktif sekarang", supaya SEMUA kode yang sudah ada di seluruh aplikasi (pengecekan `user.role === 'admin'` dst, ada di puluhan tempat) tetap jalan tanpa perlu ditulis ulang. Yang baru murni soal kemampuan satu akun punya banyak role dan bisa berpindah di antaranya.
- Data user yang sudah ada otomatis dipindahkan jadi baris `user_roles` (role utama masing-masing), jadi tidak ada yang hilang.
- Role baru (10 role di luar admin/ppk/pokja/vendor) diberi akses default ke menu Dashboard saja dulu lewat sistem Hak Akses Menu yang sudah ada - **ini sengaja minimal, bukan tebakan hak akses detail**, karena halaman/fitur khusus untuk role-role ini belum dibangun. Admin bisa atur lebih lanjut lewat halaman "Hak Akses Menu" begitu pengguna mulai kembangkan UI/UX untuk role-role tersebut.

**Backend** (`server/routes/auth.js`, `server/routes/users.js` baru):
- Login (`POST /api/auth/login`) sekarang juga mengembalikan `available_roles` (daftar role yang dimiliki akun itu).
- `GET /api/auth/my-roles` - daftar role akun yang sedang login.
- `POST /api/auth/switch-role` - ganti role aktif (mengikuti alur `excSplitRole` di atas: validasi role itu memang dimiliki akun, update `users.role`, catat riwayat, terbitkan token JWT baru).
- `server/routes/users.js` (baru, mount di `/api/users`): manajemen akun staff internal (bukan vendor) khusus Admin - lihat daftar user + role masing-masing, buat akun staff baru, tambah/cabut role dari suatu akun (tidak bisa cabut role terakhir, minimal harus ada 1).

**Frontend**:
- `src/context/AppContext.jsx`: state `availableRoles`, `showRoleSwitcher`, fungsi `switchRole()`. Kalau akun yang login punya lebih dari satu role, otomatis tawarkan modal pilih role begitu selesai login (mengikuti perilaku popup di sistem lama).
- `src/components/modals/RoleSwitcherModal.jsx` (baru): modal pilih/ganti role aktif.
- `src/components/layout/Sidebar.jsx`: tombol "Ganti Role" muncul di menu Sistem, cuma kalau akun itu punya lebih dari 1 role.
- `src/pages/UserManagement.jsx` (baru, menu "Manajemen User" khusus Admin): kelola akun staff internal dan role gandanya - ini PERLU dibangun karena sebelumnya tidak ada satupun cara membuat akun staff baru lewat UI (cuma bisa lewat seed script), jadi jadi prasyarat supaya fitur multi-role ini benar-benar bisa dipakai.

Sudah dites lewat API dari ujung ke ujung: login akun lama (harus tetap normal, tidak terganggu) → buat akun staff baru dengan role utama → tambah role kedua → login dengan akun itu (dapat 2 available_roles) → ganti role aktif (token baru langsung reflect role baru) → coba ganti ke role yang tidak dimiliki (ditolak dengan benar) → cabut satu role → coba cabut role terakhir (ditolak, minimal 1 role) → cek riwayat pergantian tersimpan. Semua berhasil, data uji coba sudah dibersihkan.

**Yang BELUM dikerjakan (di luar cakupan "fondasi", ini memang bagian pengembangan UI/UX yang akan dikerjakan pengguna sendiri)**: halaman/fitur khusus untuk 10 role baru (Admin VMS, Administrator Approval, Manager Pengadaan, Pengguna, Audit, Pelaksana Pengadaan, Pengelola Kontrak, Approval VMS, Kasubdit Kontrak, Perencanaan). Fondasinya (role terdaftar, bisa di-assign, bisa dipilih/diganti, bisa diatur menu-nya) sudah siap, tinggal dikembangkan tampilan dan fitur detail per role sesuai kebutuhan.

### Bug fatal: layar putih total gara-gara urutan kode (ditemukan & diperbaiki 2026-08-21)

Setelah kerjaan multi-role di atas selesai dan dilaporkan, pengguna coba di browser dan **seluruh aplikasi jadi layar putih kosong total** (bukan cuma satu halaman). Penyebabnya murni salah saya sendiri: di `src/context/AppContext.jsx`, fungsi `switchRole` didefinisikan SEBELUM `addNotification`, padahal `switchRole` menaruh `addNotification` di array dependency `useCallback(fn, [addNotification])`. Array dependency itu dievaluasi LANGSUNG saat render (beda dengan isi function body yang baru jalan belakangan), jadi begitu baris kode `switchRole` dieksekusi, `addNotification` belum ada nilainya sama sekali (kena "temporal dead zone" di JavaScript) → `ReferenceError` → seluruh pohon komponen React crash tanpa error boundary → layar putih.

**Perbaikan**: pindahkan deklarasi `switchRole` ke SETELAH `addNotification` didefinisikan (urutan baru: `addNotification` → `markAllAsRead` → `switchRole` → `addRequest`). Sudah dicek ulang baris per baris urutan definisi semua fungsi di file itu, dan dicek juga `RoleSwitcherModal.jsx` serta `Sidebar.jsx` (file lain yang ikut diubah di kerjaan multi-role) - keduanya aman, tidak ada masalah urutan serupa.

**Pelajaran penting untuk ke depan**: kalau nambah fungsi baru di `AppContext.jsx` yang levelnya top-level di dalam komponen (bukan di dalam function lain), dan fungsi itu butuh referensi fungsi lain sebagai dependency array `useCallback`/`useEffect`/`useMemo`, PASTIKAN fungsi yang direferensikan itu sudah dideklarasikan LEBIH DULU di atasnya secara urutan baris kode. Ini jenis bug yang **tidak ketahuan dari sekadar cek kompilasi Vite** (Vite/esbuild cuma cek sintaks, bukan urutan eksekusi runtime), jadi harus dites beneran di browser buat ketahuan.

**Catatan tambahan**: waktu debug ini juga ketahuan ada 2 proses `npm run dev` yang nyala bersamaan tanpa sengaja (satu di port 5173, satu lagi "numpuk" ke port 5174 karena 5173 sudah dipakai). Ini bikin bingung karena pengguna buka tab yang ternyata nyambung ke proses yang beda dari yang sedang saya kelola/tes. Sudah dibereskan (kedua proses lama dimatikan, dijalankan ulang 1 instance bersih di port 5173). Ke depan, sebelum bilang "sudah saya restart dan tes", selalu cek dulu proses mana saja yang benar-benar dengar di port 5173/3001 (`netstat -ano | grep LISTENING`), jangan asumsikan cuma ada 1 instance.

## KEPUTUSAN BESAR (2026-08-21): target berubah jadi 100% paritas dengan sistem lama

Setelah laporan kompleksitas (32 tabel/97 endpoint vs 218 tabel/184 controller sistem lama) dan gap struktur role, pengguna memberi arahan tegas:

> "Kita tidak bisa membuat sistem baru yang beda dari sistem lama yang sudah berjalan di kantor. Kita cuma pindahkan sistem yang sudah ada ke teknologi lebih modern (bukan CodeIgniter 2/PHP 7.3-7.4 lagi), supaya pengembangan ke depan lebih mudah. Target: 100% sama secara fungsionalitas dan kompleksitas dengan eProc lama. Tidak masalah walau makan waktu lama."

Jadi ini **BUKAN** proyek "bikin sistem baru yang lebih simpel", ini proyek **migrasi 1:1 ke stack modern**. Sistem lama (`eproc/`) adalah sumber kebenaran mutlak untuk semua keputusan fitur - kalau ada di sistem lama, harus ada juga di sistem baru, sedetail apapun itu. Ini mengubah cara kerja ke depan: sebelumnya saya cuma menutup gap yang "kelihatan jelas", sekarang harus benar-benar menelusuri semua 218 tabel dan 184 controller satu per satu.

### Peta lengkap 218 tabel sistem lama vs status sistem baru (per 2026-08-21)

Legenda: ✅ selesai/dekat selesai · 🟡 ada tapi belum lengkap · ⬜ belum ada sama sekali

**A. Tender/Paket (49 tabel, prefix `paket_*` + `paket` sendiri)**
🟡 Inti sudah ada (`tenders`, `tender_participants`, `tender_eval_criteria/scores`, `tender_aanwijzing_chats`, `tender_objections`, `tender_negotiation_chats`). Yang BELUM ada sebagai fitur tersendiri:
- ⬜ Dokumen tender (`paket_dokumen`, `paket_dokumen_backup`, `paket_dokumen_download`) - upload/download dokumen pengadaan resmi (beda dari dokumen penawaran vendor)
- ⬜ Panitia/Pokja per paket (`paket_panitia`, `paket_panitia_backup`, `paket_panitia_sk`, `panitia`, `sk_panitia`) - penunjukan anggota panitia + SK
- ⬜ Pembukaan penawaran 2 tahap (`paket_pembukaan_validasi`, `paket_pembukaankedua_validasi`) - untuk metode 2 sampul/2 tahap
- ⬜ Pernyataan minat / pra-kualifikasi (`paket_pernyataan_minat`)
- ⬜ Klarifikasi (`paket_klarifikasi`) - beda dari aanwijzing
- ⬜ Reschedule tahapan dengan riwayat (`paket_tahap_reschedule`) - sekarang cuma ubah status, belum ada riwayat reschedule
- ⬜ Peringkat pemenang & cadangan (`paket_pemenang_peringkat`)
- ⬜ Template & jenis master (`paket_jenis`, `paket_metode_evaluasi`, `paket_metode_kualifikasi`, `paket_metode_lelang`, `paket_kriteria_eval`, `evaluasi_jenis`, `matrix_evaluasi`)
- ⬜ Pakta integritas (`paket_pakta_integritas`)
- ⬜ Pihak lain / sub-kontraktor (`paket_pihak_lain`)
- ⬜ Template penilaian (`paket_penilaian_template`) - beda dari `vendor_ratings` yang sudah ada
- 🟡 Bidang usaha per paket (`paket_bidang_usaha`) - butuh master `bidang_usaha` dulu

**B. Vendor/Rekanan (41 tabel, prefix `rekanan_*` + `rekanan` sendiri)**
🟡 Inti sudah ada (`vendors` + kolom jsonb pajak/pengurus/tenaga_ahli/peralatan/bank/neraca + `vendor_documents` + `vendor_experiences` + `vendor_ratings`). Yang BELUM ada sebagai fitur/tabel tersendiri:
- ⬜ Rekening koran (`rekanan_rekening_koran`) - bukti mutasi bank, beda dari sekadar nomor rekening
- ⬜ Sertifikat dengan jenis terstruktur (`rekanan_sertifikat_jenis`) - sekarang cuma `doc_type='sertifikat'` generik
- ⬜ Bidang usaha per vendor (`rekanan_bidang_usaha`) - butuh master `bidang_usaha`
- ⬜ Tipe vendor master (`rekanan_tipe`) - Penyedia Barang/Jasa Konsultansi/Konstruksi/dst sebagai master data terstruktur
- ⬜ Retail (`rekanan_retail`) - kategori vendor retail/katalog
- ⬜ Checklist kualifikasi (`rekanan_checklist`) - butuh `master_checklist` dulu
- ⬜ Pakta integritas vendor (`rekanan_pakta_integritas`)
- ⬜ Validasi URL/whitelist (`rekanan_url_validasi`, `rekanan_url_validasi_allow`) - prioritas rendah, teknis lama (kemungkinan terkait pembatasan IP/domain akses)
- ❌ Oracle ERP integration (`rekanan_oracle`) - **tidak akan dibuat**, ini integrasi ke sistem finansial lama yang sudah tidak dipakai/di luar cakupan
- 🟡 Password per paket (`rekanan_paket_penawaran`, `paket_rekanan_password`) - mekanisme keamanan submisi penawaran lama, perlu dicek relevansinya di sistem modern (biasanya sudah digantikan HTTPS + auth token)

**C. Contracting/Kontrak (23 tabel, prefix `contracting_*`)**
🟡 Sudah ada `contracts`, `contract_payment_terms`, `contract_penalties`, `contract_deliverables`. Yang BELUM:
- ⬜ SLA (`contracting_sla`) - sebelumnya sengaja dilewati, sekarang harus dibuat juga untuk 100%
- ⬜ Surat Pesanan + material (`contracting_surat_pesanan`, `contracting_surat_pesanan_material`) - untuk kontrak jenis pengadaan barang bertahap
- ⬜ SPMK & proses kontrak bertahap (`contracting_proses`, `contracting_rekanan_proses1/4/5`, `contracting_rekanan_proses1_spmk`) - alur kerja detail kontrak (persiapan → pelaksanaan → serah terima)
- ⬜ Notifikasi kontrak (`contracting_notifikasi`) - pengingat otomatis (misal kontrak akan berakhir)
- ⬜ Catatan/monitoring teks bebas (`contracting_catatan`, `contracting_text_monitoring`)
- ⬜ File kontrak tambahan (`contracting_file`) - beda dari SPK/BAST, lampiran pendukung lain
- ⬜ Material yang dipakai (`contracting_material`)
- ⬜ SPPJB - Surat Perjanjian Pemborongan Jasa/Barang (`sppjb`) - varian dokumen kontrak konstruksi
- 🟡 Master jenis kontrak/pekerjaan/status (`contracting_jenis_kontrak`, `contracting_jenis_pekerjaan`, `contracting_status_kontrak`, `contracting_matrix`, `contracting_matrix_ori`)

**D. Katalog/E-Purchasing (11 tabel)**
🟡 Sudah ada `katalog_items`. Yang BELUM:
- ⬜ Foto produk (`katalog_foto`) - sekarang cuma 1 `image_url`, seharusnya banyak foto per produk
- ⬜ Lampiran (`katalog_lampiran`) - dokumen pendukung produk (brosur, spesifikasi)
- ⬜ Kategori (`katalog_kategori`, `katalog_kategori_rekanan`) - taksonomi kategori produk terstruktur
- ⬜ Riwayat harga (`katalog_riwayat_harga`) - histori perubahan harga produk
- ⬜ Bandingkan produk (`katalog_compare`) - fitur compare untuk pembeli
- ⬜ Laporan katalog (`katalog_laporan`)
- ⬜ Logistik (`katalog_logistik`) - info pengiriman
- ⬜ Surat pernyataan (`katalog_surat_pernyataan`)

**E. Permohonan Paket / RUP (9 tabel)**
🟡 Sudah ada `procurement_requests` + field analisa kebutuhan/pasar. Yang BELUM:
- ⬜ File analisa (`permohonan_paket_analisa_file`) - lampiran dokumen analisa
- ⬜ Approval berjenjang + revisi (`permohonan_paket_approval`, `permohonan_paket_approval_revisi`) - sekarang cuma 1 tahap approve/reject, seharusnya berjenjang dengan riwayat revisi
- ⬜ Checklist kelengkapan (`permohonan_paket_checklist`) - butuh `master_checklist`
- 🟡 Master jenis belanja & kategori (`permohonan_paket_analisa_jenis_belanja`, `permohonan_paket_analisa_kategori`)
- ⬜ Import dari SIRUP (`import_sirup`) - integrasi ke sistem resmi LKPP, butuh akses API SIRUP asli (sama seperti SAP, tidak bisa dibuat "asli" tanpa akses eksternal)

**F. Data Master (bertebaran, sekitar 25 tabel)**
🟡 Sudah ada sebagian di Data Master (bank, mata_uang, negara, satuan, incoterm, payment_method, analisa_kebutuhan, analisa_pasar, unit_kerja_master). Yang BELUM ditambahkan sebagai kategori Data Master:
- ⬜ `akta_type`, `bidang_usaha`, `direktorat`, `dokumen_template` + `dokumen_template_rekanan`, `ijin_usaha` (jenis izin), `komoditas`, `kurs`, `master_checklist`, `master_dokumen_template` + `upload`, `master_pengaturan` (pengaturan sistem umum), `metode` + `metode_tahap` + `metode_tahap_panel`, `pendidikan`, `region`, `tanggal_merah` (hari libur)

**G. Auth/User (sudah dikerjakan sebagian besar di fondasi multi-role)**
✅ `user_type`→`role_definitions`, `tbl_m_menu`→`menu_items`, `tbl_m_menu_akses`→`menu_role_access` semua sudah ada. Yang BELUM:
- ⬜ Log login/aktivitas per user (`user_login_logs`) - beda dari `audit_logs` umum, ini spesifik histori login per akun
- ⬜ Log sistem menu (`tbl_m_logs`)
- ⬜ Sistem API key (`key`, `key_request`) - untuk integrasi API pihak ketiga
- 🟡 Soft-delete/arsip (18 tabel `zdel_*`) - pola sistem lama: data yang "dihapus" dipindah ke tabel arsip terpisah, bukan hard delete. Sistem baru sekarang belum ada fitur hapus vendor/user sama sekali (cuma ubah status), jadi ini baru relevan kalau fitur hapus dibuat nanti - pendekatan modernnya cukup pakai kolom `deleted_at`, tidak perlu tabel duplikat terpisah seperti sistem lama.

**H. Komunikasi (sebagian sudah ada)**
✅ `inbox` + `inbox_category` → `inbox_messages` + `inbox_categories` sudah ada. Yang BELUM:
- ⬜ Kategori komplain terstruktur (`inbox_complain_set`, `inbox_complain_type`)
- ⬜ Chat umum per paket (`chatshoutbox`) - beda dari aanwijzing/negosiasi yang sudah ada, ini sepertinya chat umum/diskusi
- ⬜ Chat PHP shoutbox lama (`phpshoutbox`) - kemungkinan legacy/duplikat `chatshoutbox`, perlu dicek relevansinya

**I. Konten Publik**
✅ `berita`→`cms_news`, `faq`→`cms_faq` sudah ada. Yang BELUM (sebelumnya sengaja dilewati, sekarang harus dibuat untuk 100%):
- ⬜ `banner` - kelola gambar carousel halaman utama lewat admin (sekarang hardcode di kode)
- ⬜ `kebijakan` - halaman kebijakan publik

**J. Evaluasi Detail dengan Rumus Resmi**
🟡 Sudah ada sistem kriteria+skor generik (`tender_eval_criteria/scores`). Yang BELUM:
- ⬜ Rumus resmi untuk kategori "pengalaman" (`paket_eval_pengalaman`/`rekanan_eval_pengalaman`, ada kolom `bp_nilai`, `nk1_rp`, `nk2_rpmin/rpmax` dst) dan "personil" (`paket_eval_personil`, ada `ska`, `cv`, `nilai_minimum`) - **sekarang sumbernya sudah ada di kode PHP asli** (`eproc/application/controllers/rekanan_eval_pengalaman_json.php` dan sejenisnya), jadi TIDAK PERLU nebak lagi seperti kemarin - tinggal baca logika perhitungannya langsung dari situ dan tiru persis. Ini jadi prioritas tinggi karena sebelumnya saya salah langkah (mengira tidak ada rujukan resminya, padahal source code aslinya ADA di project ini).
- ⬜ `paket_eval_kd`, `paket_eval_keuangan`, `paket_eval_peralatan_detil` - sub-kategori evaluasi yang lebih rinci

**K. Lain-lain**
- ⬜ `visitor` - statistik pengunjung halaman publik
- ⬜ `rekam_jejak` - jejak audit lebih detail dari `audit_logs` yang sudah ada (perlu dicek bedanya)
- ⬜ `logs_kirim_email_dok_expired` - notifikasi email otomatis dokumen akan kedaluwarsa
- ⬜ `pivoting` - perlu diselidiki fungsinya, nama tabel tidak cukup jelas
- ❌ `sap_pr` (integrasi SAP) - **tetap simulasi**, butuh akses SAP asli milik UI
- ❌ `import_sirup` - **tetap belum bisa "asli"**, butuh akses API SIRUP resmi LKPP

### Rencana kerja ke depan

Karena skalanya sangat besar, dikerjakan bertahap per kelompok (A sampai K di atas), setiap fitur/tabel yang dikerjakan tetap mengikuti proses yang sama seperti sebelumnya: cek dulu logika PHP asli di `eproc/application/controllers/` (bukan cuma struktur tabelnya) supaya tidak menebak-nebak aturan bisnis, baru bikin migrasi + endpoint + halaman, lalu dites sendiri lewat API dan cek kompilasi sebelum lapor selesai. Urutan pengerjaan kelompok dikonfirmasi ke pengguna sebelum mulai setiap kelompok baru (kecuali diminta jalan terus tanpa henti).

**Prioritas yang disepakati** (urutan pengerjaan): mulai dari kelompok J (Rumus Evaluasi Resmi) dulu, baru lanjut A sampai K berurutan, satu-satu, dikabari tiap kelompok selesai.

### Kelompok J: Rumus Evaluasi Resmi (selesai 2026-08-21)

**Temuan penting yang meluruskan kesalahan sebelumnya**: kemarin (migrations/009) saya kira rumus resmi kategori "pengalaman" tidak ada dokumennya jadi saya bikin manual semua. Setelah ditelusuri LANGSUNG ke kode JavaScript asli yang benar-benar dipakai sistem produksi (`eproc/lib/eproc/allfunc.js`, dicocokkan dengan view mana saja yang benar-benar memanggilnya di `eproc/application/views/main/` - folder aktif, bukan backup), ketahuan faktanya:

- **Kategori Pengalaman**: halaman aktifnya (`evaluasi_kualifikasi_pengalaman.php`) memang cuma input manual, TIDAK ADA rumus otomatis yang benar-benar dipakai. Ada fungsi `hitungBidangPekerjaan()` di `allfunc.js` yang kelihatannya rumus lama & kompleks (pakai field BP_NILAI, NK1/NK2/NK3, STBU dari tabel `paket_eval_pengalaman`), tapi sudah dicek ke SELURUH folder views (termasuk backup) dan fungsi itu **tidak dipanggil dari manapun** - kode mati/ditinggalkan. Jadi cara manual yang sudah dibangun sebelumnya **sudah benar, tidak perlu diubah**.
- **Kategori Personil, Peralatan, Sertifikat Lain**: BENAR-BENAR punya rumus otomatis yang aktif dipakai (`hitungPersonil()`, `hitungPeralatan()`, `hitungSertifikat()` di `allfunc.js`, dipanggil dari `evaluasi_kualifikasi_personil.php`, `_peralatan.php`, `_sertifikat.php` di folder aktif). Rumus ini sudah diimplementasikan persis di `migrations/015_rumus_evaluasi_resmi.sql` + endpoint baru di `server/routes/tenders.js`.

**Pelajaran metodologi penting untuk kelompok kerja berikutnya (A-K)**: struktur tabel database TIDAK CUKUP untuk tahu apakah suatu fitur/rumus benar-benar dipakai. Harus dicek juga: (1) apakah ada view yang memanggilnya, dan (2) apakah view itu ada di folder AKTIF (`views/main/`, bukan `views-/`, `views--/`, atau folder `# BC ...` yang merupakan backup/percobaan lama). Kalau cuma tabel ada tapi tidak ada satupun view aktif yang pakai, kemungkinan besar itu fitur yang sudah ditinggalkan, tidak perlu ditiru.

**Rumus yang diimplementasikan** (detail lengkap ada di komentar `migrations/015_rumus_evaluasi_resmi.sql`):
- Tiap item (1 personil/1 alat/1 sertifikat yang diajukan vendor) dinilai "Kesesuaian": S(Sesuai)=100, TS(Tidak Sesuai)=0, atau manual (dipaksa jadi 50 kalau diisi persis 0/100 tanpa pilih S/TS eksplisit - meniru validasi asli).
- Peralatan dikalikan faktor kepemilikan (100=milik sendiri, kurang dari itu kalau sewa dst).
- Personil: kalau jumlah yang diajukan kurang dari kebutuhan, nilai didilusi proporsional; kalau cukup/lebih, di-cap maksimal 100%.
- Peralatan & Sertifikat: jumlah nilai kesesuaian dibagi 100, di-cap maksimal 100%.
- Rasio itu dikali bobot kriteria dalam kategorinya → nilai kontribusi. Dijumlah semua kriteria dalam kategori yang sama → nilai kali nilai maksimal kategori dibagi 100 → nilai akhir kategori untuk vendor itu.

Tabel baru: `tender_eval_category_config` (nilai maksimal per kategori per tender), `tender_eval_score_items` (item individual per kriteria per vendor), kolom baru `tender_eval_criteria.required_count` (khusus personil). Endpoint baru: `eval-category-config`, `eval-score-items`, `eval-formula-score/:vendorId/:category` (mesin hitungnya, fungsi `calcCriteriaRatio()` di `server/routes/tenders.js`).

**Sudah diverifikasi dengan hitungan manual sebelum dipakai** (bukan cuma dites jalan, tapi dicek angkanya benar): 3 skenario dihitung tangan dulu (personil dengan kekurangan orang, peralatan sewa, sertifikat lengkap), lalu dibandingkan hasil API-nya - ketiganya cocok persis dengan hitungan manual.

Frontend: `src/components/modals/FormulaCategorySection.jsx` (baru), disambungkan ke `EvaluationDetailModal.jsx` - kategori Personil/Peralatan/Sertifikat Lain otomatis tampil pakai UI item-based (tambah item + rumus otomatis), kategori lain tetap pakai skor manual seperti sebelumnya.
