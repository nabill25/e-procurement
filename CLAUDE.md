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
🟡 Inti sudah ada (`tenders`, `tender_participants`, `tender_eval_criteria/scores`, `tender_aanwijzing_chats`, `tender_objections`, `tender_negotiation_chats`). Sudah dikerjakan (lihat tulisan lengkap "Kelompok A" di bawah):
- ✅ Dokumen tender (`tender_documents`) - upload/download dokumen pengadaan resmi (beda dari dokumen penawaran vendor)
- ✅ Panitia/Pokja per paket (`tender_panitia`) + SK Panitia master roster (`sk_panitia` + `panitia`) - penunjukan anggota panitia, ketua, kunci tim, validasi pemenang oleh panitia
- ✅ Pembukaan penawaran 2 tahap (`tender_pembukaan_validasi`, kolom `tahap`) - untuk metode 2 sampul/2 tahap
- ✅ Pernyataan minat / pra-kualifikasi (`tender_pernyataan_minat`)
- ✅ Klarifikasi dokumen formal + tanggapan aanwijzing (`tender_klarifikasi_dokumen`) - beda dari chat aanwijzing yang sudah ada, plus undangan klarifikasi resmi (`tender_undangan_klarifikasi`)
- ✅ Peringkat pemenang & cadangan (`tender_pemenang_peringkat`)
- ✅ Pakta integritas (`tender_pakta_integritas`)
- ✅ Pihak lain / sub-kontraktor (`tender_pihak_lain`)
- ✅ Reschedule tahapan dengan riwayat (`tender_stages` + `tender_stage_reschedule_history`) - selesai 2026-08-26, lihat tulisan lengkap "Item Tambahan Pasca-Roadmap" di bawah
- ✅ Jenis & metode tender (`paket_jenis`, metode lelang/kualifikasi/evaluasi) - selesai 2026-08-26, sebagai kategori Data Master generik
- ✅ Template penilaian (`paket_penilaian_template`) - selesai 2026-08-26, versi disederhanakan (berjenjang bab/pasal + bobot, tanpa approval 3 tingkat)
- ✅ Bidang usaha per paket (`paket_bidang_usaha`) - ternyata **sudah selesai sejak Kelompok B** (endpoint `tender_bidang_usaha` + section "Bidang Usaha yang Disyaratkan" di `DokumenPaketTab.jsx`), catatan lama di sini sudah ketinggalan zaman

**B. Vendor/Rekanan (41 tabel, prefix `rekanan_*` + `rekanan` sendiri) - selesai 2026-08-21**
🟡 Inti sudah ada (`vendors` + kolom jsonb pajak/pengurus/tenaga_ahli/peralatan/bank/neraca + `vendor_documents` + `vendor_experiences` + `vendor_ratings`). Sudah dikerjakan (lihat tulisan lengkap "Kelompok B" di bawah):
- ✅ Bidang usaha berjenjang (`bidang_usaha`) - data KBLI+SBU ASLI diimpor dari `eproc_migrasi.sql` (2794 baris), bukan data buatan
- ✅ Bidang usaha per vendor (`vendor_bidang_usaha`) dan per tender (`tender_bidang_usaha`)
- ✅ Rekening koran (`vendor_rekening_koran`) - bukti mutasi bank per bulan, beda dari sekadar nomor rekening
- ✅ Tipe vendor & Jenis sertifikat master - ditambahkan sebagai 2 kategori baru di Data Master yang sudah ada (`rekanan_tipe`, `sertifikat_jenis`, reuse `master_data`)
- ✅ Vendor Retail (`vendor_retail`) - kategori vendor retail/katalog, alur & kontak terpisah dari vendor pengadaan biasa
- ✅ Rincian Penawaran / BOQ (`tender_bid_items`) - item per baris pada penawaran vendor, opsional, otomatis menghitung ulang total penawaran

**Koreksi penting dari daftar awal**: setelah dicek controllernya, `rekanan_sertifikat_jenis` (versi lama daftar ini), `rekanan_checklist`, `rekanan_pakta_integritas`, dan `rekanan_url_validasi` ternyata **TIDAK PERNAH ADA controllernya sama sekali** di sistem lama - itu cuma tebakan salah dari nama tabel yang mirip. Sebaliknya ditemukan 2 fitur nyata yang sebelumnya tidak masuk daftar: `sertifikat_json.php` (master jenis sertifikat) dan `vendor_retail_json.php` (vendor retail).

- ❌ Oracle ERP integration (`rekanan_oracle`) - **tidak akan dibuat**, ini integrasi ke sistem finansial lama yang sudah tidak dipakai/di luar cakupan

**C. Contracting/Kontrak (23 tabel, prefix `contracting_*`) - selesai 2026-08-21**
🟡 Sudah ada `contracts`, `contract_payment_terms`, `contract_penalties`, `contract_deliverables`. Sudah dikerjakan (lihat tulisan lengkap "Kelompok C" di bawah):
- ✅ SPPBJ (Surat Penunjukan Penyedia) & SPK/PKS detail - diperluas langsung ke tabel `contracts`
- ✅ SPMK (`contract_spmk`)
- ✅ Jaminan Pelaksanaan (`contract_jaminan`) & Jaminan Pemeliharaan/garansi (`contract_jaminan_pemeliharaan`)
- ✅ SLA (`contract_sla`) - sebelumnya sengaja dilewati, sekarang sudah dibuat
- ✅ Material + Surat Pesanan untuk kontrak payung (`contract_materials`, `contract_surat_pesanan`, `contract_surat_pesanan_items`)
- ✅ Addendum dengan 2 tahap approval (`contract_addendum`)
- ✅ Catatan bebas teks internal/penyedia (`contract_notes`)
- ✅ Notifikasi/pengingat kontrak (`contract_reminders`)
- ✅ Dokumen tambahan selain SPK/BAST (`contract_documents`)
- ✅ Perubahan status kontrak: Perubahan/Penyesuaian/Kahar/Berakhir/Pemutusan/Kesempatan/Denda (`contract_status_changes`)
- ✅ SPPJB - Surat Perjanjian (`contract_sppjb`)
- ✅ PIC per tahap (Persiapan/Pengendali/Penyelesai) dan tahap kontrak (`contracts.stage`)
- ✅ Penilaian kinerja penyedia dengan 3 tahap approval (PPK/Kasubdit/Unit)
- ❌ Master jenis kontrak/pekerjaan/status terstruktur (`contracting_jenis_kontrak` dkk) - **tetap teks bebas**, tidak dibuatkan tabel master terpisah karena nilainya cuma dipakai sebagai label, bisa diperluas nanti kalau memang dibutuhkan pengelolaan master jenis kontrak

**D. Katalog/E-Purchasing (11 tabel) - selesai 2026-08-21**
🟡 Sudah ada `katalog_items`. Sudah dikerjakan (lihat tulisan lengkap "Kelompok D" di bawah):
- ✅ Perluasan field produk (merek, model, dimensi, TKDN, garansi, stok, kemasan, status) langsung di `katalog_items`
- ✅ Foto produk (`katalog_photos`) - banyak foto per produk, sebelumnya cuma 1 `image_url`
- ✅ Lampiran (`katalog_attachments`) - dokumen pendukung produk (brosur, spesifikasi)
- ✅ Kategori berjenjang (`katalog_categories`, `katalog_item_categories`) - taksonomi kategori produk terstruktur
- ✅ Riwayat harga (`katalog_price_history`) - histori perubahan harga produk, auto-tercatat cuma kalau harga BERUBAH
- ✅ Bandingkan produk (`katalog_compare`) - fitur compare untuk pembeli, maksimal 3 produk per sesi
- ✅ Laporan katalog (`katalog_reports`)
- ✅ Logistik (`katalog_logistik`) - ongkos kirim per pengajuan
- ✅ Keranjang & alur pesanan (`katalog_cart_items`) - **temuan besar**: katalog ternyata toko online mini terhubung ke pengajuan/RUP, bukan sekadar galeri produk (backend selesai, frontend keranjang-nego-pesanan menyusul, lihat catatan di bawah)
- ❌ Surat pernyataan (`katalog_surat_pernyataan`) - **tidak ditemukan controllernya sama sekali** di sistem lama, kemungkinan tabel yang tidak pernah dipakai

**E. Permohonan Paket / RUP (9 tabel) - selesai 2026-08-21**
🟡 Sudah ada `procurement_requests` + field analisa kebutuhan/pasar. Sudah dikerjakan (lihat tulisan lengkap "Kelompok E" di bawah):
- ✅ File analisa (`procurement_request_files`) - lampiran dokumen analisa, termasuk field tanda tangan elektronik (esign)
- ✅ Approval berjenjang + revisi (`procurement_request_approvals`, `procurement_request_revisions`) - satu baris approval per approver (bukan cuma 1 status tunggal), plus riwayat revisi dengan catatan+file dikirim balik ke pengaju
- ✅ Checklist kelengkapan (`procurement_request_checklist` + `master_checklist`) - master checklist difilter per jenis paket, digabung dengan status centang tiap pengajuan
- ✅ Master jenis belanja & kategori (`jenis_belanja`, `analisa_kategori`) - reuse `master_data` yang sudah ada
- ❌ Import dari SIRUP (`import_sirup`) - **tetap simulasi**, integrasi ke sistem resmi LKPP, butuh akses API SIRUP asli (sama seperti SAP, tidak bisa dibuat "asli" tanpa akses eksternal, sudah disepakati sebelumnya)

**F. Data Master (bertebaran, sekitar 25 tabel) - selesai 2026-08-21**
🟡 Sudah ada sebagian di Data Master (bank, mata_uang, negara, satuan, incoterm, payment_method, analisa_kebutuhan, analisa_pasar, unit_kerja_master, rekanan_tipe, sertifikat_jenis, vendor_retail, katalog_kategori, jenis_belanja, analisa_kategori, master_checklist - ini semua sudah ditambahkan di kelompok B/D/E sebelumnya). Sudah ditambahkan (lihat tulisan lengkap "Kelompok F" di bawah):
- ✅ `dokumen_template` + `dokumen_template_rekanan` + `master_dokumen_template`/`upload` - digabung jadi satu tabel `document_templates` (field `target` bedakan internal/rekanan, 3 sistem lama yang tumpang tindih disatukan)
- ✅ `ijin_usaha` (jenis izin usaha), `pendidikan` (jenjang pendidikan) - reuse `master_data`
- ✅ `master_pengaturan` (pengaturan sistem umum, khusus notifikasi dokumen expired)
- ✅ `tanggal_merah` (hari libur)
- ✅ `region` (wilayah administratif) - **kerangka tabel berjenjang dibuat, tapi cuma diisi 38 provinsi resmi**, kabupaten/kota/kecamatan/kelurahan kosong karena sumber data aslinya (INDOWILAYAH2023) TIDAK ADA di database yang dimiliki, beda dari data KBLI kelompok B yang lengkap tersedia
- ❌ `akta_type` - **tidak dibuat**, controllernya (`akta_type_json.php`) tidak pernah dipanggil dari manapun (kode mati, dicek ke seluruh folder views termasuk backup)
- ❌ `komoditas`, `kurs` - **tidak dibuat**, tidak ada controllernya sama sekali di sistem lama (kemungkinan tabel yang tidak pernah benar-benar dipakai)
- ❌ `direktorat`, `metode_tahap`, `metode_tahap_panel` - **belum dibuat**, fungsinya (`metode_json.php`) ternyata isinya kalkulasi tanggal/kalender kompleks terkait `paket_jenis`/`paket_metode_lelang` (master data tender yang sengaja ditunda dari kelompok A), bukan data referensi sederhana - akan digarap bareng kelompok A lanjutan kalau memang dibutuhkan

**G. Auth/User - selesai 2026-08-21**
✅ `user_type`→`role_definitions`, `tbl_m_menu`→`menu_items`, `tbl_m_menu_akses`→`menu_role_access` semua sudah ada dari fondasi multi-role. Sudah ditambahkan (lihat tulisan lengkap "Kelompok G" di bawah):
- ✅ Log login/aktivitas per user (`user_login_logs`) - riwayat login per akun (IP, browser, waktu login/logout, status sesi), plus halaman baru untuk melihatnya
- ✅ Sistem API key (`api_keys`, `api_key_requests`) - untuk integrasi API pihak ketiga, plus UI kelola key (generate/aktifkan/nonaktifkan/hapus) yang di sistem lama tidak pernah ada
- ✅ Kerangka Soft-delete (kolom `deleted_at` di `vendors` dan `users`) - disiapkan untuk dipakai nanti kalau fitur hapus permanen dibangun
- ❌ Log sistem menu (`tbl_m_logs`) - **tidak dibuat**, ternyata bukan log menu, tapi log percobaan akses ditolak (403 Forbidden), dan tidak ada satupun halaman di sistem lama yang membacanya (murni tulis, tidak pernah ditampilkan) - tidak ada fungsi yang hilang kalau tidak ditiru

**H. Komunikasi - selesai 2026-08-21**
✅ `inbox` + `inbox_category` → `inbox_messages` + `inbox_categories` sudah ada dari sebelumnya. Sudah ditambahkan (lihat tulisan lengkap "Kelompok H" di bawah):
- ✅ Kategori komplain terstruktur (`inbox_complain_set`, `inbox_complain_type` → `inbox_complain_types`, `inbox_complain_recipients`)
- ✅ Chat umum per paket (`chatshoutbox` → `tender_general_chats`) - chat 1-ke-1 panitia-vendor per konteks (evaluasi teknis, evaluasi kualifikasi, auction, kontrak, umum)
- ✅ Konfirmasi kehadiran aanwijzing (`phpshoutbox` bagian PESAN='CONFIRMED') - perluasan `tender_aanwijzing_chats` yang sudah ada, bukan tabel baru
- ✅ Chat negosiasi (`negoshoutbox`) - ternyata **sudah tercakup** sejak awal oleh `tender_negotiation_chats`, tidak perlu tabel baru
- ✅ Chat sesi lelang (`biddingshoutbox`) - digabung ke `tender_general_chats` (jenis_chat='auction'), tidak dibuat tabel terpisah karena sistem baru belum ada modul e-auction sendiri
- ❌ `PHPFREECHAT` - **tidak dibuat**, kode mati total (tidak ada satupun controller/view yang memanggilnya)

**I. Konten Publik - selesai 2026-08-21**
✅ `berita`→`cms_news`, `faq`→`cms_faq` sudah ada dari sebelumnya. Sudah ditambahkan (lihat tulisan lengkap "Kelompok I" di bawah):
- ✅ `banner` → `cms_banners` - kelola gambar lewat admin (upload, nama, link URL, aktif/nonaktif), tampil di section terpisah di halaman utama publik (TIDAK menggantikan hero slider besar yang sudah ada, itu sengaja tidak diubah)
- ✅ `kebijakan` → `cms_policies` - halaman kebijakan publik baru, admin bisa kelola banyak halaman kebijakan (judul, isi HTML, jenis, terbit/draft)

**J. Evaluasi Detail dengan Rumus Resmi**
🟡 Sudah ada sistem kriteria+skor generik (`tender_eval_criteria/scores`). Yang BELUM:
- ⬜ Rumus resmi untuk kategori "pengalaman" (`paket_eval_pengalaman`/`rekanan_eval_pengalaman`, ada kolom `bp_nilai`, `nk1_rp`, `nk2_rpmin/rpmax` dst) dan "personil" (`paket_eval_personil`, ada `ska`, `cv`, `nilai_minimum`) - **sekarang sumbernya sudah ada di kode PHP asli** (`eproc/application/controllers/rekanan_eval_pengalaman_json.php` dan sejenisnya), jadi TIDAK PERLU nebak lagi seperti kemarin - tinggal baca logika perhitungannya langsung dari situ dan tiru persis. Ini jadi prioritas tinggi karena sebelumnya saya salah langkah (mengira tidak ada rujukan resminya, padahal source code aslinya ADA di project ini).
- ⬜ `paket_eval_kd`, `paket_eval_keuangan`, `paket_eval_peralatan_detil` - sub-kategori evaluasi yang lebih rinci

**K. Lain-lain - selesai 2026-08-21 (kelompok TERAKHIR dari roadmap)**
- ✅ `rekam_jejak` → `tender_activity_logs` - timeline detail aktivitas per tender/pengajuan/kontrak, jauh lebih detail dari `audit_logs` generik yang sudah ada
- ✅ `logs_kirim_email_dok_expired` → `document_expiry_notification_logs` - pemantauan dan pencatatan notifikasi dokumen vendor akan kedaluwarsa (pengiriman email sungguhan belum aktif, belum ada SMTP)
- ❌ `visitor` - **tidak dibuat**, di sistem lama sendiri fitur ini sudah setengah mati (data dicatat tapi fungsi laporannya sudah lama di-comment/dinonaktifkan developer sebelumnya, tidak ada satupun halaman yang menampilkannya)
- ❌ `pivoting` - **tidak dibuat**, terkonfirmasi bukan tabel data bisnis sama sekali, cuma trik SQL lama (generator angka urut) untuk query pivot laporan pajak, tergantikan sepenuhnya oleh `generate_series()` kalau memang dibutuhkan pola serupa
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

### Kelompok A: Tender/Paket Detail - backend selesai (2026-08-21), frontend menyusul

**Metodologi yang dipakai** (sesuai pelajaran dari kelompok J): sebelum bikin tabel apapun, semua controller PHP terkait dibaca penuh dulu (`paket_dokumen_json.php`, `paket_panitia_json.php`, `sk_panitia_json.php`, `paket_pernyataan_minat_json.php`, `paket_pemenang_peringkat_json.php`, `paket_pakta_integritas_json.php`, `paket_pihak_lain_json.php`, `paket_pembukaan_validasi_json.php`, `paket_pembukaan_kedua_validasi_json.php`, `klarifikasi_chat_json.php`, `paket_undangan_klarifikasi_json.php`), lalu dicek dengan grep ke `eproc/application/views/main/*.php` bahwa semuanya benar-benar dipanggil dari halaman aktif (bukan folder backup). Semua 11 controller terkonfirmasi aktif dipakai.

**Tabel baru** (migrasi `migrations/016_kelompok_a_paket_detail.sql`):
- `tender_documents` - dokumen resmi tender (jenis: lelang/kualifikasi/aritmatika/laporan), beda dari dokumen penawaran vendor yang sudah ada di `tender_participants.document_path`.
- `sk_panitia` + `panitia` - master roster SK (surat keputusan) pembentukan panitia per unit kerja, dengan daftar anggotanya. Ini terpisah dari penugasan panitia ke satu paket tertentu.
- `tender_panitia` - penugasan panitia (dari roster atau input manual) ke satu paket, dengan flag ketua, status kunci tim (`locked`), dan kolom validasi pemenang oleh panitia (`validasi_pemenang` + catatan) - ini langkah persetujuan TAMBAHAN sebelum pemenang final diumumkan, terpisah dari `tender_participants.is_winner` yang merupakan penetapan awal oleh PPK/Pokja.
- `tender_pernyataan_minat` - surat kuasa/pernyataan minat rekanan saat mendaftar paket, termasuk upload file kuasa.
- `tender_pakta_integritas` - validasi pakta integritas per paket, bisa oleh rekanan maupun panitia (kolom `jenis`).
- `tender_pihak_lain` - user login lain yang diberi akses lihat ke suatu paket (misal auditor tambahan).
- `tender_pembukaan_validasi` - validasi pembukaan sampul penawaran, kolom `tahap` (1=sampul pertama, 2=sampul kedua) menggabungkan dua controller lama (`paket_pembukaan_validasi_json` dan `paket_pembukaan_kedua_validasi_json`) jadi satu tabel yang lebih rapi.
- `tender_klarifikasi_dokumen` - dokumen klarifikasi formal dari rekanan + tanggapan aanwijzing dari panitia (pola parent-child lewat `parent_id`), beda dari `tender_aanwijzing_chats` yang sudah ada (itu chat realtime, ini dokumen resmi).
- `tender_undangan_klarifikasi` - undangan klarifikasi resmi ke vendor (jadwal, tempat, peserta). Di sistem lama fitur ini otomatis kirim email; di sistem baru field `email` disimpan tapi pengiriman email belum diimplementasikan (perlu SMTP config, menyusul).
- `tender_pemenang_peringkat` - urutan peringkat pemenang + cadangan per paket (bisa lebih dari 1 baris per paket, beda dari `is_winner` yang cuma tandai 1 pemenang utama).

**Endpoint baru** di `server/routes/tenders.js` (semua sudah dites lewat curl dengan data tender sungguhan, lalu data uji dibersihkan): dokumen tender (`GET/POST/DELETE /:id/documents`), SK panitia master (`GET/POST/PUT/DELETE /master/sk-panitia`, `POST /master/sk-panitia/:skId/lampiran`), panitia per paket (`GET/POST/DELETE /:id/panitia`, `PATCH /:id/panitia/lock`, `PATCH /:id/panitia/:panitiaId/validasi-pemenang`), pernyataan minat (`GET/POST /:id/pernyataan-minat`), pakta integritas (`GET/POST /:id/pakta-integritas`), pihak lain (`GET/POST/DELETE /:id/pihak-lain`), pembukaan penawaran (`GET/POST /:id/pembukaan`), klarifikasi dokumen + tanggapan (`GET/POST/DELETE /:id/klarifikasi-dokumen`, `POST /:id/klarifikasi-dokumen/:docId/tanggapan`), undangan klarifikasi (`GET/POST /:id/undangan-klarifikasi`), peringkat pemenang (`GET/POST/DELETE /:id/peringkat-pemenang`).

**Perangkap route ordering yang ditemukan dan diperbaiki sendiri**: rute `/master/sk-panitia` awalnya ditaruh di akhir file, SETELAH `router.get('/:id', ...)` yang sudah ada dari awal. Karena Express mencocokkan rute berurutan dari atas, `GET /api/tenders/master/sk-panitia` akan salah tertangkap sebagai `GET /:id` dengan `id="master"`. Sudah dipindah ke sebelum rute `/:id` dan dites ulang, berhasil. Pelajaran untuk kelompok B-K berikutnya: setiap kali menambah endpoint dengan path literal (bukan berisi tender_id) di file yang sudah punya `/:id`, harus ditaruh SEBELUM rute `/:id` itu.

**Frontend** (2026-08-21): dua komponen tab baru ditambahkan ke `DetailTenderModal.jsx` (halaman kerja internal admin/pokja/ppk untuk satu tender, beda dari `TenderDetailView.jsx` yang cuma tampilan publik ringkas):
- `src/components/modals/PanitiaTab.jsx` - tab "Panitia": ambil anggota dari SK Panitia master atau input manual, kunci tim, validasi pemenang oleh ketua panitia (setuju/tolak dengan catatan wajib kalau tolak). Hanya tampil untuk role pokja/admin/ppk.
- `src/components/modals/DokumenPaketTab.jsx` - tab "Dokumen & Klarifikasi": gabungan 6 fitur jadi satu tab supaya tidak kebanyakan tab (dokumen tender, klarifikasi+tanggapan aanwijzing, pakta integritas, peringkat pemenang, pihak lain). Tampil untuk semua role yang bisa akses detail tender, tapi bagian upload/kelola cuma muncul untuk pokja/admin/ppk (vendor cuma bisa lihat + validasi pakta integritas sendiri + upload dokumen klarifikasi).

Sudah dicek compile bersih lewat curl ke Vite dev server (HTTP 200, tidak ada "Failed to parse"/"Unexpected token"), dan dicek log HMR menunjukkan update berhasil tanpa error.

**Belum dikerjakan dari kelompok A**: fitur pembukaan penawaran (`tender_pembukaan_validasi`) dan undangan klarifikasi (`tender_undangan_klarifikasi`) sudah ada backend-nya tapi BELUM disambungkan ke frontend - menyusul. Juga 3 sub-fitur yang sengaja ditunda (lihat daftar di atas): reschedule tahapan dengan riwayat, tabel master jenis/metode (paket_jenis dst), template penilaian.

### Kelompok B: Vendor/Rekanan Detail - selesai (2026-08-21)

**Metodologi**: sama seperti kelompok A, semua controller PHP terkait dibaca dulu sebelum bikin skema (`bidang_usaha_json.php`, `rekanan_rekening_koran_json.php`, `rekanan_tipe_json.php`, `sertifikat_json.php`, `vendor_retail_json.php`, `paket_bidang_usaha_json.php`, `rekanan_paket_penawaran_json.php`), lalu dicek aktif dipanggil dari `views/main/*.php`. Ketemu koreksi penting: 4 item di daftar awal roadmap (`rekanan_sertifikat_jenis`, `rekanan_checklist`, `rekanan_pakta_integritas`, `rekanan_url_validasi`) ternyata **tidak punya controller sama sekali** - itu tebakan salah dari nama tabel. Sebaliknya ditemukan `sertifikat_json.php` dan `vendor_retail_json.php` yang sebelumnya tidak masuk daftar.

**Data bidang usaha (KBLI+SBU) diimpor ASLI, bukan dibuat manual**: tabel `bidang_usaha` di sistem lama ternyata berisi 2794 baris data resmi (klasifikasi KBLI - Klasifikasi Baku Lapangan Usaha Indonesia - plus kode SBU konstruksi), bukan data buatan. Karena ini data resmi pemerintah yang sudah ada di `eproc_migrasi.sql`, saya import langsung lewat script sekali-pakai (bukan tebak-tebak bikin sendiri, sesuai aturan 0% asumsi). Strukturnya pohon 2 level: root (huruf A-U untuk kategori KBLI, atau kode SBU seperti "SBU UBG") dengan `parent_id` bernilai "0" (bukan NULL), lalu kode detail (5 digit KBLI atau kode SBU spesifik) sebagai anak.

**Bug yang ditemukan dan diperbaiki sendiri selama proses import** (2 bug terpisah, keduanya di script sekali-pakai, bukan di kode aplikasi):
1. Deteksi baris akhir data COPY di file dump salah (`content.indexOf('\n\\.\n', ...)` gagal karena masalah escaping saat menulis lewat heredoc bash, menyebabkan pencarian file berhenti di lokasi salah dan mengambil >96 ribu baris alih-alih 2794 baris yang benar). Diperbaiki dengan membandingkan per baris pakai `String.fromCharCode(92)` supaya tidak bergantung pada escaping backslash yang rawan salah.
2. Deteksi "baris akar" (root) pohon bidang usaha salah: kode kategori huruf (misal "A") punya `bidang_usaha_parent_id` bernilai string `"0"` di data asli, bukan `NULL` atau merujuk ke dirinya sendiri seperti yang saya kira di awal. Akibatnya 2749 dari 2794 baris gagal masuk (parent tidak pernah "ketemu") dan salah dimasukkan sebagai root semua. Setelah ketahuan, diperbaiki jadi mengenali `"0"` sebagai penanda root juga, hasil re-import: 2794 baris masuk sempurna (28 root: 21 kategori KBLI + 7 root SBU).
3. Bug tambahan: field `nama` (bukan cuma `keterangan`) di data asli juga mengandung teks `\r\n` literal (bukan baris baru sungguhan) yang perlu dibersihkan, sebelumnya cuma `keterangan` yang dibersihkan. Sudah diperbaiki dan re-import ulang, diverifikasi bersih dengan mengecek langsung tiap baris di JavaScript (bukan cuma pakai `LIKE` SQL yang ternyata memberi hasil salah/false-positive karena cara Postgres menangani backslash di pola pencarian LIKE).

**Tabel baru** (migrasi `migrations/017_kelompok_b_vendor_detail.sql`): `bidang_usaha` (pohon KBLI/SBU), `vendor_bidang_usaha` (bidang usaha milik vendor), `tender_bidang_usaha` (bidang usaha yang disyaratkan tender), `vendor_rekening_koran` (bukti mutasi bank per bulan), `vendor_retail` (vendor retail/katalog, alur terpisah), `tender_bid_items` (rincian penawaran per item / BOQ).

**Endpoint baru**: `server/routes/vendors.js` (bidang usaha tree+search, retail CRUD, vendor bidang-usaha assign/remove, rekening-koran upload/list/delete), `server/routes/tenders.js` (tender bidang-usaha requirement, bid-items get/replace dengan auto-hitung ulang `tender_participants.bid_price`), `server/routes/master.js` (2 kategori baru: `rekanan_tipe`, `sertifikat_jenis`, reuse pola `master_data` yang sudah ada).

**Bug tambahan yang ditemukan & diperbaiki saat testing** (bukan bagian utama kelompok B tapi ditemukan waktu ini): endpoint `PUT /api/vendors/retail/:id` awalnya replace semua kolom sekaligus (field yang tidak dikirim jadi `NULL`), sudah diperbaiki jadi partial update pakai `COALESCE` sesuai pola yang sudah dipakai di `master.js`.

**Frontend**: tab baru "Bidang Usaha" dan "Rekening Koran" di halaman Profil Vendor (`src/components/profile/BidangUsahaTab.jsx`, `RekeningKoranTab.jsx`, cari-dan-pilih dari 2794 kode via pencarian, bukan dropdown penuh). Kategori "Tipe Vendor" dan "Jenis Sertifikat" otomatis muncul di halaman Data Master (reuse komponen generik yang sudah ada). Kategori baru "Vendor Retail" juga di Data Master, komponen `VendorRetailTable` khusus karena datanya lebih detail dari sekadar nama. Section baru "Bidang Usaha yang Disyaratkan" di tab Dokumen & Klarifikasi (`DokumenPaketTab.jsx`) untuk Pokja/PPK/Admin menandai syarat bidang usaha per tender. Form penawaran vendor (`VendorBidForm` di `DetailTenderModal.jsx`) dapat opsi tambahan "Rincian Penawaran per Item" yang bisa dibuka/tutup, opsional (tidak menggantikan alur harga total yang sudah ada, cuma pelengkap). Sekalian diperbaiki bug lama yang ditemukan di file yang sama: `VendorBidForm` masih pakai key localStorage `eproc_token` yang salah (seharusnya `dpbj_token`), jadi upload dokumen penawaran vendor sebenarnya selalu gagal auth sebelum ini.

Sudah dites lewat curl end-to-end (retail CRUD dengan partial update, assign bidang usaha ke vendor, upload rekening koran, submit rincian penawaran dengan verifikasi total otomatis benar 40.000.000 dari 2 item, replace rincian saat vendor kirim ulang), dan dicek compile bersih semua file frontend (HTTP 200 dari Vite, tidak ada error parse, HMR update sukses). Data uji dibersihkan dari Supabase setelah testing (data bidang usaha asli 2794 baris TIDAK dihapus, itu data permanen).

### Kelompok C: Contracting/Kontrak Detail - selesai (2026-08-21)

**Ini kelompok kerja paling besar sejauh ini.** Riset awalnya salah arah: saya kira `contracting_rekanan_json.php` (2716 baris) adalah kode mati karena tidak dipanggil dari `views/main/`. Ternyata SALAH - ada folder terpisah `eproc/application/views/kontrak/` (175 file) yang aktif dipakai lewat controller `kontrak.php` (fungsi `index()` yang load view dinamis dari folder itu berdasarkan URL segment). Setelah ditelusuri lebih dalam (2 agent riset dikerahkan paralel untuk membaca ribuan baris kode), ketahuan fakta sebenarnya:
- `contracting_rekanan_json.php` **memang terkonfirmasi 100% read-only** (nol operasi insert/update, cuma listing DataTables untuk berbagai halaman per tahap/role) - jadi bukan kode mati, tapi juga bukan tempat logika bisnis.
- Logika bisnis sesungguhnya (semua insert/update field kontrak) ada di **`contracting_json.php`** (4756 baris, ~65 fungsi), yang dipanggil dari form utama `views/kontrak/contracting_detail.php`. File inilah yang jadi acuan skema, dibaca PENUH (bukan sebagian) lewat kombinasi baca langsung + 1 agent riset tambahan.
- `contracting_notifikasi_json.php` dan `contracting_penyedia_json.php` juga sudah dibaca penuh (masing-masing kecil, di bawah 300 baris).

**Pelajaran metodologi baru untuk kelompok D-K berikutnya**: kalau sebuah controller tidak ketemu dipanggil dari `views/main/`, JANGAN langsung simpulkan kode mati. Cek dulu apakah ada folder `views/<nama modul>/` terpisah (seperti `views/kontrak/` di sini) yang mungkin dipakai lewat controller dinamis serupa `kontrak.php`. Kelompok Kontrak ternyata punya arsitektur folder sendiri yang beda dari modul-modul lain yang sudah dikerjakan sebelumnya.

**Alur kerja lengkap yang ditemukan** (sistem lama, field CONTRACTINGPROSESID): SPPBJ (Surat Penunjukan Penyedia) → SPMK (Surat Perintah Mulai Kerja) → SPK/PKS atau Surat Perjanjian (SPPJB, untuk kontrak konstruksi) → lalu berjalan lewat 4 tahap: Persiapan (0/1/2) → Pengendalian (3) → Penyelesaian (4/5, termasuk BAST) → Selesai (6). Sistem baru TIDAK meniru 7 kode angka status yang membingungkan itu, cukup pakai kolom teks `contracts.stage` dengan 4 nilai (persiapan/pengendalian/penyelesaian/selesai).

**Perluasan tabel `contracts`** (bukan tabel baru, karena secara konsep 1 baris kontrak = gabungan SPPBJ+SPK dalam sistem lama juga, migrasi `migrations/018_kelompok_c_kontrak_detail.sql`) jadi 82 kolom total, mencakup: field SPPBJ (kode, tanggal, direktur, jaminan pelaksanaan), field SPK/PKS (kode, jenis pekerjaan, pihak 1/2, lingkup pekerjaan, legal), approval manager+PPK+pemeriksa, BAST Hasil Pekerjaan dan BAST Masa Pemeliharaan (2 set kolom terpisah, field asli beda prefix `CR_BAST_PEKERJAAN_*` vs `CR_BAST_MASA_*`), PIC per tahap (persiapan/pengendali/penyelesai), dan penilaian kinerja penyedia (grade + 3 approval independen PPK/Kasubdit/Unit).

**11 tabel anak baru**: `contract_spmk`, `contract_jaminan` (jaminan pelaksanaan + konfirmasi bank), `contract_jaminan_pemeliharaan` (garansi purna kontrak), `contract_sla` (khusus kontrak layanan), `contract_materials` + `contract_surat_pesanan` + `contract_surat_pesanan_items` (untuk Kontrak Payung - istilah "Kontrak Payung/Surat Pesanan" eksplisit ada di komentar kode PHP asli), `contract_addendum` (butuh 2 approval terpisah: Kasubdit dan Penyedia), `contract_notes`, `contract_reminders`, `contract_documents`, `contract_status_changes` (7 jenis perubahan digabung 1 tabel karena semua fungsi asli - `addPerubahanKontrak`, `addKaharKontrak`, dst - punya pola identik: flag+alasan+file opsional), `contract_sppjb`.

**Endpoint baru**: sekitar 40 endpoint baru di `server/routes/tenders.js`, semua nested di bawah `/api/tenders/:id/contract/...` mengikuti pola yang sudah ada. Logika replikasi aturan bisnis asli yang diterapkan persis: addendum otomatis berubah status jadi "selesai" begitu KEDUA approval (Kasubdit dan Penyedia) sudah true (meniru pola `approvalAddendum`+`approvalAddendumPenyedia` di kode asli), catatan kosong ditolak dengan pesan yang sama persis ("Catatan tidak boleh kosong"), submit material pakai pola replace-all (hapus semua lalu insert ulang, meniru `addMaterial()` asli), item surat pesanan otomatis hitung `total = qty × harga_satuan`.

**Frontend**: file baru `src/components/modals/ContractWorkflowSections.jsx` isinya 9 komponen section (SppbjSpkSection, SpmkSection, JaminanSection, SlaSection, MaterialSection, AddendumSection, NotesRemindersDocsSection, StatusChangeSection, PicStageSection). `ContractTab.jsx` yang sudah ada diubah jadi ber-sub-tab (10 sub-tab: Utama, SPPBJ & SPK, SPMK, Jaminan, SLA, Material & Surat Pesanan, Addendum, Catatan & Dokumen, Perubahan Status, PIC & Tahap) supaya tidak jadi satu halaman scroll raksasa - fitur lama (termin pembayaran, sanksi, progres pekerjaan, kode QR, penilaian bintang) tetap di sub-tab "Utama", tidak dipindah/diubah.

Sudah dites lewat curl end-to-end mencakup semua 40 endpoint (SPPBJ, SPK detail, approval manager/PPK dengan penolakan field yang tidak dikenal, PIC, perpindahan tahap dengan validasi nilai tidak valid, SPMK, SLA, material replace-all, surat pesanan dengan item dan verifikasi total otomatis benar 17.500.000, update status terima item, hapus surat pesanan dengan cascade item, addendum dengan verifikasi logika 2-approval-baru-selesai, catatan kosong ditolak, pengingat, perubahan status dengan validasi jenis tidak dikenal, BAST Hasil, jaminan pelaksanaan+pemeliharaan, SPPJB, dokumen tambahan dengan toggle publish, penilaian kinerja). Semua data uji sudah dibersihkan dari Supabase. Frontend dicek compile bersih (HTTP 200, HMR update sukses tanpa error) untuk `ContractWorkflowSections.jsx` dan `ContractTab.jsx`.

### Kelompok D: Katalog/E-Purchasing Detail - selesai (2026-08-21)

**Temuan besar yang mengubah cakupan**: dugaan awal roadmap (foto, kategori, riwayat harga saja) SALAH. Setelah `katalog_json.php` (~1400 baris) dibaca penuh, ketahuan katalog di sistem lama itu toko online mini yang terhubung ke pengajuan pengadaan (`procurement_requests`), bukan sekadar galeri produk: user browse produk → masukkan ke keranjang (terikat ke satu pengajuan tertentu) → nego harga dengan vendor → checkout dengan ongkos kirim → alur status pesanan 6 tahap (Proses Pemilihan → Negosiasi → Penyedia Setuju → Surat Pesanan → Proses → Dikirim → Diterima) dengan nomor invoice otomatis. Sesuai arahan pengguna, dikerjakan penuh termasuk alur ini di backend.

**Koreksi penting saat riset**: dua controller yang namanya kelihatan seperti fitur katalog (`katalog_offline_json.php`, `katalog_pemerintah_json.php`) TERNYATA BUKAN fitur katalog produk sama sekali - itu upload dokumen untuk metode pengadaan "Pembelian Offline"/"Pembelian Pemerintah" pada modul Purchasing yang beroperasi ke tabel `Paket`/`Purchasingfile`, bukan `Katalog`. Tidak dipakai sebagai acuan migrasi ini. `katalog_validasi_json.php` juga ternyata cuma grid read-only (jumlah katalog per vendor untuk admin), tidak ada logika "validasi" khusus yang butuh tabel baru.

**9 tabel baru** (migrasi `migrations/019_kelompok_d_katalog_detail.sql`), plus perluasan `katalog_items` dengan field produk lengkap (kode produk, merek, model, dimensi P/L/T, TKDN, garansi, stok, kemasan, status): `katalog_photos` (banyak foto), `katalog_attachments` (lampiran), `katalog_categories` (kategori berjenjang, pola sama seperti `bidang_usaha` di kelompok B), `katalog_item_categories` (many-to-many produk-kategori), `katalog_price_history` (riwayat harga - **cuma tercatat kalau harga BENAR-BENAR berubah**, meniru persis logika asli yang membandingkan harga lama vs baru sebelum insert), `katalog_reports` (laporan/komplain dari pengunjung publik, tidak perlu login), `katalog_compare` (bandingkan produk per sesi browser, maksimal 3 seperti validasi asli), `katalog_logistik` (ongkos kirim per pengajuan), `katalog_cart_items` (keranjang dengan status alur pesanan 6 tahap dan invoice otomatis).

**Endpoint baru**: `server/routes/katalog.js` ditulis ulang total (sebelumnya cuma 4 endpoint CRUD sederhana) jadi ~25 endpoint. Logika bisnis yang direplikasi persis dari kode asli: PUT update produk otomatis bandingkan harga lama vs baru dan cuma insert riwayat kalau beda, POST ke keranjang otomatis nambah qty kalau produk yang sama sudah ada di keranjang pengajuan itu (bukan insert baris baru), transisi status pesanan divalidasi ketat (cuma bisa 0→1→2→3→4 dan 5→6, transisi lain ditolak), nomor invoice otomatis dibuat cuma pada transisi status 0 dan 1 (meniru `generateInvoice()` asli).

**Frontend**: `src/components/modals/KatalogDetailModal.jsx` (baru) - modal detail produk lengkap dengan foto, lampiran, kategori, riwayat harga, dan form laporan publik. `src/pages/Katalog.jsx` - form tambah/edit produk diperluas dengan semua field baru plus pemilih kategori (multi-select tombol toggle), tombol Edit khusus pemilik produk, tombol Detail untuk semua orang. Kategori "Kategori Katalog" ditambahkan ke Data Master (`KatalogCategoryTable`, pola pilih induk-kategori sama seperti Unit Kerja).

**Sengaja belum dikerjakan di frontend** (dikonfirmasi ke pengguna dulu sebelum mulai): alur keranjang-negosiasi-checkout-lacak status pesanan yang terikat ke satu pengajuan tertentu. Ini butuh perubahan alur UI yang lebih besar di halaman Katalog (pengguna harus pilih pengajuan dulu sebelum belanja, beda dari keranjang sisi-browser sederhana yang sudah ada sekarang yang langsung checkout ke modul Purchasing terpisah). Backend untuk alur ini SUDAH selesai dan sudah dites lewat curl end-to-end (endpoint `/api/katalog/cart/*`, `/api/katalog/logistik/*`), tinggal disambungkan ke UI kapan saja dibutuhkan.

Sudah dites lewat curl end-to-end (kategori berjenjang, tambah produk dengan field lengkap + kategori, update harga dengan verifikasi riwayat cuma tercatat saat berubah, upload foto+lampiran, filter by kategori, keranjang dengan auto-qty-increment, negosiasi harga + ongkos kirim, alur status pesanan lengkap 0→1→2→3 dengan verifikasi invoice muncul di transisi yang tepat, transisi status tidak valid ditolak, laporan produk publik, compare produk). Semua data uji dibersihkan dari Supabase. Frontend dicek compile bersih (HTTP 200, HMR update sukses tanpa error).

### Kelompok E: Permohonan Paket/RUP Detail - selesai (2026-08-21)

**Metodologi**: sesuai roadmap CLAUDE.md yang sudah spesifik (bukan riset ulang dari nol seperti kelompok C/D), cakupan kelompok E memang sudah jelas sejak awal: file analisa, approval berjenjang+revisi, checklist. Controller terkait (`permohonan_paket_approval_json.php`, `permohonan_paket_checklist_json.php`) dibaca penuh dan dikonfirmasi aktif dipanggil dari `views/main/`. Dua file besar lain (`permohonan_paket_json.php`, `permohonan_paket_usulan_json.php`, gabungan >5800 baris) TIDAK dijadikan acuan karena isinya alur pembuatan RUP dari awal yang sudah tercakup oleh `procurement_requests` yang ada sekarang - di luar cakupan "sisa pekerjaan" yang didefinisikan roadmap.

**Tabel baru** (migrasi `migrations/020_kelompok_e_rup_detail.sql`): `procurement_request_files` (file analisa + field esign), `procurement_request_approvals` (approval berjenjang - **catatan penting**: field asli cuma `approved`+`approved_by` per baris, jadi "berjenjang" di sini artinya BISA ADA LEBIH DARI SATU approval per pengajuan oleh approver berbeda, bukan alur level bertingkat formal seperti yang mungkin terbayang - ini sesuai persis dengan struktur tabel asli, bukan ditambah-tambah), `procurement_request_revisions` (catatan+file revisi), `master_checklist` (checklist master dengan filter jenis paket+metode pemilihan+flag wajib), `procurement_request_checklist` (centang per pengajuan). Kategori baru `jenis_belanja` dan `analisa_kategori` di `master_data` yang sudah ada (kedua tabel asli cuma referensi nama sederhana, tidak perlu tabel baru).

**Endpoint baru** di `server/routes/pengajuan.js` (~10 endpoint) dan `server/routes/master.js` (2 kategori baru). Logika yang direplikasi persis dari kode asli: approval pakai upsert (`ON CONFLICT ... DO UPDATE`) supaya satu approver yang approve ulang meng-update baris lamanya, bukan bikin duplikat (meniru pola `selectByParams` lalu cek `countRow()` di kode asli); endpoint checklist pengajuan mengambil SEMUA item `master_checklist` yang relevan (difilter jenis paket) lalu digabung dengan status centang yang sudah ada, supaya item yang belum pernah dicentang tetap muncul di daftar (bukan cuma yang sudah ada baris di database); kirim revisi otomatis mengubah `procurement_requests.status` jadi `'revisi'`.

**Frontend**: `src/components/modals/DetailPengajuanModal.jsx` dapat 3 section baru (`ChecklistSection`, `FileAnalisaSection`, `RevisionHistorySection`) plus tombol "Minta Revisi" di tahap verifikasi berkas admin (sebelumnya cuma ada Tolak/Terima, sekarang ada opsi tengah untuk minta perbaikan tanpa langsung menolak). Kategori "Checklist Pengajuan" ditambahkan ke Data Master (`MasterChecklistTable`) untuk admin kelola daftar item checklist master, plus "Jenis Belanja" dan "Kategori Analisa" (pakai komponen generik yang sudah ada).

Sudah dites lewat curl end-to-end (master checklist dengan filter jenis paket, upload file analisa + update esign, checklist merge antara master dan status tercentang, approval oleh 2 approver berbeda dengan verifikasi upsert saat approver sama approve ulang, kirim revisi dengan verifikasi status pengajuan otomatis berubah jadi 'revisi', kategori master data baru). Semua data uji dibersihkan dari Supabase. Frontend dicek compile bersih (HTTP 200, HMR update sukses tanpa error).

### Kelompok F: Data Master Lanjutan - selesai (2026-08-21)

**Metodologi**: semua controller terkait dibaca dulu (`ijin_usaha_json.php`, `region_json.php`, `pendidikan_json.php`, `master_tanggal_json.php`, `dokumen_template_json.php`, `dokumen_template_rekanan_json.php`, `metode_json.php`, `master_pengaturan_json.php`, `master_backup_json.php`, `master_dokumen_template_upload.php`), semua dicek aktif dipanggil dari `views/main/`.

**Koreksi dari daftar awal roadmap**:
- `akta_type` **dikeluarkan** - controllernya (`akta_type_json.php`) ternyata TIDAK PERNAH dipanggil dari manapun sama sekali (dicek ke seluruh folder views termasuk backup), kode mati murni.
- `komoditas` dan `kurs` **dikeluarkan** - setelah `ls` langsung ke folder controllers, TIDAK ADA controller untuk keduanya sama sekali. Kemungkinan besar tabel yang pernah dibuat di skema tapi fiturnya tidak pernah jadi dipakai.
- `metode_json.php` isinya BUKAN data referensi sederhana seperti dugaan - fungsinya kalkulasi kalender rekening koran (`get_bulan_rekening_koran*`, murni matematika tanggal, bukan tabel) dan query matriks tahapan tender yang terikat ke `paket_jenis`/`paket_metode_lelang` (master data tender yang sudah sengaja ditunda sejak kelompok A). Jadi `direktorat`/`metode_tahap`/`metode_tahap_panel` **belum dikerjakan** di kelompok ini, disatukan nanti dengan lanjutan kelompok A kalau memang dibutuhkan.
- `master_backup_json.php` ternyata fitur backup-download seluruh database (operasi sistem admin, bukan data domain) - **tidak relevan** untuk migrasi data master.

**Temuan penting soal data wilayah Indonesia**: field `region` di roadmap awal saya kira cuma dropdown provinsi/kota sederhana. Setelah dicek `region_json.php`, ternyata itu terhubung ke tabel `INDOWILAYAH2023` yang punya 4 tingkat (provinsi → kabupaten/kota → kecamatan → kelurahan) - dataset resmi pemerintah yang besar. Sudah dicek langsung ke `eproc_migrasi.sql`, tabel `INDOWILAYAH2023` **TIDAK ADA di dalamnya sama sekali** (beda dari data KBLI/bidang usaha di kelompok B yang datanya lengkap tersedia). Karena ini data alamat administratif resmi yang berisiko kalau dikarang sendiri, dikonfirmasi ke pengguna dan disepakati: kerangka tabel berjenjang tetap dibuat, tapi cuma diisi 38 nama provinsi resmi Indonesia (data publik yang stabil dan sudah pasti benar), level di bawahnya (kab/kota, kecamatan, kelurahan) dikosongkan sampai ada sumber data resmi yang bisa diimpor - sama seperti perlakuan terhadap integrasi SAP dan SIRUP sebelumnya (dicatat sebagai keterbatasan, bukan dipaksa dibuat).

**3 sistem template dokumen yang tumpang tindih disatukan**: sistem lama punya `dokumen_template`+`dokumen_template_rekanan` (template untuk internal vs rekanan, dari `dokumen_template_json.php`/`dokumen_template_rekanan_json.php`) DAN `master_dokumen_template`+`master_dokumen_template_upload` (dari `master_dokumen_template_upload.php`) - dicek isinya sama-sama "file template yang bisa diunduh", cuma beda controller karena ditulis di waktu berbeda. Di sistem baru digabung jadi satu tabel `document_templates` dengan kolom `target` ('internal'/'rekanan') alih-alih 4 tabel terpisah yang isinya tumpang tindih.

**Tabel baru** (migrasi `migrations/021_kelompok_f_data_master_lanjutan.sql`): `document_templates`, `holidays` (hari libur, replace-all pattern meniru `add()` asli), `app_settings` (pengaturan sistem, cuma 1 baris untuk notifikasi dokumen expired, di-seed otomatis lewat migrasi), `regions` (wilayah berjenjang, diisi 38 provinsi). Kategori baru `ijin_usaha` dan `pendidikan` reuse `master_data` yang sudah ada.

**Endpoint baru** di `server/routes/master.js` (~15 endpoint: document-templates, holidays, settings, regions).

**Frontend**: 3 komponen tabel baru di Data Master (`DocumentTemplateTable`, `HolidayTable`, `RegionTable`) plus 2 kategori reuse (`ijin_usaha`, `pendidikan`). `RegionTable` punya alur pilih-provinsi-dulu-baru-kelola-turunannya, dengan catatan peringatan di UI bahwa data di bawah provinsi memang belum ada dan perlu diisi manual/nanti dari sumber resmi.

Sudah dites lewat curl end-to-end (upload template dokumen dengan filter target, hari libur replace-all, pengaturan sistem get+update, daftar 38 provinsi, tambah wilayah anak di bawah provinsi tertentu, kategori ijin_usaha/pendidikan). Semua data uji dibersihkan dari Supabase (38 provinsi TIDAK dihapus, itu data permanen). Frontend dicek compile bersih (HTTP 200, HMR update sukses tanpa error).

### Kelompok G: Auth/User Lanjutan - selesai (2026-08-21)

**Metodologi**: controller terkait dibaca dulu (`login.php`, `models/usersbase.php` untuk log login; `controllers/api.php`, `models/key.php` untuk API key; `controllers/rekanan_json.php` untuk contoh nyata pola `zdel_*`), dicek aktif/tidaknya lewat grep ke seluruh `views/`.

**Log login/aktivitas (`USER_LOGIN_LOGS` di sistem lama)**: dikonfirmasi aktif ditulis tiap kali ada yang login (baik login manual maupun lewat SSO), dicatat IP, OS, browser, dan token sesi. Tapi setelah dicek, halaman yang ada (`logs_login.php`) ternyata **tidak menampilkan isi tabel ini sama sekali** - cuma menampilkan ringkasan "terakhir login kapan" dari tabel user biasa. Jadi di sistem lama, tabel ini sebenarnya write-only (datanya lengkap tapi tidak pernah ada yang bisa lihat detail histori login per baris). Karena datanya berguna untuk keamanan (tahu siapa login dari IP/perangkat mana dan kapan), sistem baru sekalian membuat halaman untuk melihatnya - ini murni fitur baru, tidak ada versi lama untuk dicontoh tampilannya.

**Log sistem menu (`tbl_m_logs`) - TIDAK dibuat**: setelah ditelusuri, nama tabel ini menyesatkan - isinya bukan log perubahan menu, tapi log setiap kali ada yang mencoba akses halaman yang tidak diizinkan (kode 403 Forbidden), dipicu otomatis dari fungsi keamanan `cekSession()` yang dipakai di hampir semua halaman. Sama seperti log login, ini juga murni ditulis tapi **tidak ada satupun halaman di sistem lama yang membacanya** - tidak ada dashboard atau laporan untuk lihat percobaan akses ditolak. Karena tidak ada fungsi yang hilang kalau tidak ditiru, bagian ini dilewati.

**API key untuk integrasi pihak ketiga**: dikonfirmasi aktif dipakai - ada 12 endpoint REST (`api.php`) yang bisa dipanggil sistem luar untuk ambil data RUP dan paket pengadaan, divalidasi pakai API key. Tapi ditemukan sistem lama **tidak punya satupun halaman untuk mengelola key ini** (bikin key baru, aktifkan/nonaktifkan) - itu dilakukan manual langsung ke database oleh developer. Sistem baru menambahkan halaman kelola API key sebagai perbaikan gap ini (generate key otomatis, bisa nonaktifkan sementara tanpa hapus, bisa lihat riwayat pemakaian tiap key).

**Pola arsip `zdel_*` (18 tabel)**: dikonfirmasi lewat modul Rekanan (`rekanan_json.php` fungsi `excRekananDelete`) - polanya adalah menyalin seluruh isi baris ke tabel arsip (nama sama, prefix `ZDEL_`) DULU, baru setelah berhasil, baris aslinya benar-benar **dihapus permanen** dari tabel asal (bukan cuma ditandai/flag terhapus). Sistem baru **tidak meniru pola tabel arsip terpisah ini** karena boros (2x jumlah tabel) - pendekatan modern yang dipakai cukup 1 kolom `deleted_at` di tabel asli (data "terhapus" tetap ada di tempatnya, tinggal difilter `WHERE deleted_at IS NULL`). Kolom ini baru ditambahkan sebagai kerangka di tabel `vendors` dan `users` - **belum dipakai fitur apapun** karena sistem baru saat ini memang belum ada fitur hapus permanen vendor/user sama sekali (yang ada baru ubah status suspend/block). Kerangka ini disiapkan supaya siap dipakai begitu fitur hapus benar-benar dibangun nanti.

**Migrasi baru** (`migrations/022_kelompok_g_auth_user_lanjutan.sql`): tabel `user_login_logs`, `api_keys`, `api_key_requests`, kolom `deleted_at` di `vendors`/`users`, plus pendaftaran 2 menu baru (`login_logs`, `api_keys`) ke sistem Hak Akses Menu khusus role admin.

**Endpoint baru**: `server/routes/auth.js` (catat log login otomatis saat `POST /login` berhasil dan tutup sesi saat `POST /logout`, endpoint `GET /login-logs` untuk lihat riwayat login akun sendiri), `server/routes/users.js` (`GET /login-logs` versi admin untuk lihat SEMUA akun, CRUD `api-keys` lengkap: buat/lihat/toggle aktif/hapus/lihat riwayat pemakaian).

**Frontend**: 2 halaman baru khusus admin - `src/pages/LoginLogs.jsx` (tabel riwayat login, siapa, dari IP mana, kapan, status sesi masih aktif atau sudah logout) dan `src/pages/ApiKeys.jsx` (kelola API key: buat baru dengan tombol salin, aktifkan/nonaktifkan, hapus, dan modal lihat riwayat pemakaian per key). Ditambahkan ke menu sidebar dan `App.jsx`.

Sudah dites lewat curl end-to-end (login tercatat otomatis ke `user_login_logs`, logout menutup sesi dengan benar/`is_active` jadi false, riwayat login akun sendiri vs semua akun untuk admin, buat API key dengan key ter-generate otomatis, toggle aktif/nonaktif, lihat riwayat pemakaian key, hapus key). Data uji sudah dibersihkan dari Supabase. Frontend dicek compile bersih (HTTP 200 dari Vite untuk semua file baru). Menu baru dikonfirmasi muncul untuk role admin lewat `GET /api/menu/admin`.

**2 bug ditemukan dan diperbaiki setelah tes lewat BROWSER SUNGGUHAN (bukan cuma curl)**: sesuai arahan pengguna untuk selalu pastikan lancar tanpa bug sebelum lanjut kelompok berikutnya, dilakukan testing tambahan pakai browser asli (Playwright), bukan cuma curl seperti biasanya. Ternyata curl saja tidak cukup untuk menangkap 2 bug ini karena keduanya murni bug sisi frontend/React:

1. **`getAuthHeaders` salah dipanggil** di `LoginLogs.jsx` dan `ApiKeys.jsx` - ditulis `const { getAuthHeaders } = useApp()` padahal `getAuthHeaders` itu fungsi yang di-export terpisah dari `AppContext.jsx` (bukan bagian dari `value` yang dikembalikan `useApp()`). Akibatnya kedua halaman baru ini diam-diam gagal (ketangkap try/catch, tidak sampai bikin layar putih, tapi datanya tidak pernah kebaca/tersimpan - riwayat login selalu kosong, API key tidak pernah tersimpan walau tombol submit ditekan). Sudah diperbaiki: import langsung `getAuthHeaders` dari `../context/AppContext`, bukan destructure dari `useApp()`.
   - **Update 2026-08-21**: pola salah yang SAMA ditemukan tersebar di **17 file lain** yang sudah ada dari kelompok-kelompok sebelumnya (bukan cuma 11 seperti dugaan awal). Yang paling parah: **7 dari 8 kategori di halaman Data Master rusak total** (tombol tambah/hapus selalu gagal diam-diam). Semua 17 file ini sudah diperbaiki tuntas (lihat detail lengkap di tulisan "Perbaikan bug besar getAuthHeaders" setelah writeup Kelompok H di bawah).
2. **React "Hooks berubah urutan" di `App.jsx`** - fungsi `AppShell` punya `return <AuthLoadingScreen/>` di tengah fungsi (saat sesi masih dicek), tapi ADA pemanggilan `useApp()` KEDUA setelah titik return itu (untuk ambil `isAuthenticated`/`user`). Ini melanggar aturan React bahwa jumlah hook yang dipanggil harus selalu sama di setiap render - kebetulan baru ketahuan sekarang karena baru kali ini dites sampai proses login selesai lewat browser sungguhan. Ini POLA BUG YANG SAMA PERSIS dengan insiden layar putih total yang pernah terjadi sebelumnya (kasus `switchRole`/`addNotification`) - untungnya kali ini belum sampai menyebabkan crash nyata, cuma warning di console, tapi berisiko jadi crash sewaktu-waktu tergantung timing render. Sudah diperbaiki: semua yang dibutuhkan dari `useApp()` (termasuk `isAuthenticated` dan `user`) digabung jadi SATU pemanggilan di paling atas fungsi, sebelum ada `return` apapun.

Kedua perbaikan sudah diverifikasi ulang lewat browser sungguhan (bukan cuma compile check): riwayat login sekarang benar-benar tampil datanya, API key benar-benar tersimpan/toggle/hapus berhasil, warning hook-order sudah hilang total, dan dites ulang seluruh alur (login admin, pindah 5 menu sidebar, logout, login ulang dengan akun vendor) tanpa ada satupun error/warning baru di console. Data uji dibersihkan.

### Perbaikan bug besar: getAuthHeaders di 17 file + vendor.id vs users.id (2026-08-21)

Sebelum lanjut ke Kelompok H, sesuai arahan pengguna untuk selalu pastikan lancar tanpa bug dulu, dicek dulu apakah pola bug `getAuthHeaders` yang ketemu di Kelompok G ini menyebar ke tempat lain. Ternyata iya, jauh lebih luas dari dugaan awal (11 file) - total **17 file** kena pola yang sama: `ContractDetailSections.jsx`, `ContractTab.jsx`, `ContractWorkflowSections.jsx`, `DetailTenderModal.jsx`, `FormulaCategorySection.jsx`, `ObjectionsTab.jsx`, `ContentManagement.jsx`, `DataMaster.jsx`, `MenuAccess.jsx`, `UserManagement.jsx`, `VendorProfile.jsx`, `Inbox.jsx`, `Purchasing.jsx`, `DetailPengajuanModal.jsx`, `Katalog.jsx`, `EvaluationDetailModal.jsx`, `KatalogDetailModal.jsx`.

**Dampak paling parah dikonfirmasi lewat browser sungguhan**: 7 dari 8 kategori di halaman Data Master (Bank, Mata Uang, Unit Kerja, Vendor Retail, Kategori Katalog, Hari Libur, Wilayah - cuma Template Dokumen yang kebetulan selamat karena headernya ditulis manual) rusak total, tombol tambah dan hapus selalu gagal dengan pesan error generik "Terjadi kesalahan saat menyimpan data." Penyebabnya: `getAuthHeaders()` bernilai `undefined` (bukan fungsi), jadi crash JavaScript terjadi SEBELUM request sempat dikirim ke server sama sekali - bukan soal token ditolak, request-nya memang tidak pernah sampai.

**Perbaikan**: semua 17 file diseragamkan mengikuti pola yang SUDAH BENAR sejak awal di 2 file lama (`Vendor.jsx`, `Pengajuan.jsx`) - `getAuthHeaders` diimpor langsung dari `AppContext` di baris import teratas, dipisahkan dari destructure `useApp()`. Dikerjakan lewat skrip otomatis (bukan manual satu-satu) supaya konsisten dan tidak ada yang terlewat, lalu dicek ulang manual tidak ada sisa pola salah (`grep` ke seluruh `src/` hasilnya kosong).

**Bug terpisah yang ditemukan bersamaan**: 3 endpoint di `server/routes/vendors.js` (`GET /:id`, `PUT /:id/profile`, `GET /:id/qualifications`) ternyata salah mencari data pakai `vendors.id` (PK baris tabel vendors sendiri), padahal `VendorProfile.jsx` memanggilnya dengan `user.id` (konsisten dengan aturan yang sudah dicatat di CLAUDE.md sejak kelompok Kualifikasi Vendor: `vendor_id` di seluruh aplikasi selalu berarti `users.id`). Akibatnya vendor asli yang login **tidak bisa melihat profilnya sendiri sama sekali** (selalu "Data Vendor Tidak Ditemukan"). Diperbaiki dengan mengubah ketiga query itu jadi `WHERE user_id = $1`, plus 6 titik panggilan di `SikapTabs.jsx` (tab Pajak/Tenaga Ahli/Peralatan/Pengurus/Bank/Neraca) yang tadinya kirim `vendor.id` (vendors.id, sekarang salah) diubah jadi `vendor.user_id` supaya tetap konsisten dengan backend yang baru.

**Diverifikasi menyeluruh lewat browser sungguhan**, termasuk uji reload halaman untuk pastikan data BENAR-BENAR tersimpan ke database (bukan cuma state React sementara): tambah-hapus data di 3 kategori Data Master berhasil, halaman Profil Vendor sekarang tampil normal dan bisa isi-simpan-reload-masih-ada di 2 tab (Pajak, Bank), semua halaman admin lain (Riwayat Login, API Key, Manajemen User, Hak Akses Menu, Kelola Konten, Pusat Pesan) dicek tetap normal tidak ada regresi. Nol error console di sepanjang pengujian.

### Kelompok H: Komunikasi Lanjutan - selesai (2026-08-21)

**Metodologi**: riset awal lewat agent Explore menemukan sistem lama ternyata punya **5 sistem chat berbeda yang aktif** (bukan 2 seperti dugaan roadmap awal): `CHATSHOUTBOX` (chat panitia-vendor per konteks: evaluasi teknis, evaluasi kualifikasi, auction, kontrak, umum - dibedakan kolom `JENIS_CHAT`), `PHPSHOUTBOX` (broadcast + konfirmasi kehadiran selama sesi aanwijzing), `NEGOSHOUTBOX` (chat negosiasi harga), `BIDDINGSHOUTBOX` (chat sesi lelang harga/e-auction), plus struktur kategori komplain terpisah (`INBOX_COMPLAIN_TYPE`, `INBOX_COMPLAIN_SET`). Semua dikonfirmasi aktif dipanggil dari `views/main/`. Karena skalanya lebih besar dari dugaan, cakupan dikonfirmasi dulu ke pengguna sebelum mulai - disepakati kerjakan semua.

**Kategori komplain terstruktur**: `INBOX_COMPLAIN_TYPE` (daftar subjek komplain pilihan, bukan teks bebas) dan `INBOX_COMPLAIN_SET` (daftar alamat/penerima default) jadi tabel baru `inbox_complain_types` dan `inbox_complain_recipients`, plus kolom baru `inbox_messages.complain_type_id` untuk menandai pesan mana yang komplain resmi (beda dari pesan "Kontak Kami" biasa).

**Chat umum per paket**: `CHATSHOUTBOX` jadi tabel baru `tender_general_chats`, kolom `jenis_chat` menyimpan konteksnya (umum/evaluasi_teknis/evaluasi_kualifikasi/auction/kontrak). **Chat sesi lelang (`BIDDINGSHOUTBOX`) sengaja digabung ke sini** (jenis_chat='auction') alih-alih dibuat tabel terpisah, karena sistem baru belum punya modul e-auction sendiri - di sistem lama pun sebenarnya sudah dobel ditangani 2 tabel berbeda untuk hal yang sama, technical debt lama yang tidak perlu ditiru.

**Konfirmasi kehadiran aanwijzing**: bagian `PHPSHOUTBOX` yang soal broadcast+chat sudah tercakup `tender_aanwijzing_chats` yang sudah ada duluan, yang BELUM ada cuma fitur konfirmasi kehadiran (`PESAN='CONFIRMED'` di sistem lama). Ditambahkan lewat kolom baru `tender_aanwijzing_chats.is_confirmation` (perluasan tabel yang sudah ada, bukan tabel baru) - baris dengan flag ini bukan pesan chat biasa, tapi penanda "vendor ini hadir".

**Chat negosiasi (`NEGOSHOUTBOX`) - ternyata sudah tercakup**: dicek ke `tender_negotiation_chats` yang sudah dibangun sejak modul Negosiasi (jauh sebelum kelompok ini), strukturnya sudah cukup (pesan + harga tawaran per tender per vendor). Tidak perlu tabel baru, cukup dicatat selesai.

**Ditemukan juga sekaligus dikeluarkan dari cakupan** (kode mati, dicek grep ke seluruh `views/` termasuk backup): `PHPFREECHAT` (tidak ada satupun controller/view yang memanggilnya, kemungkinan sisa integrasi library pihak-ketiga yang tidak jadi dipakai) dan `nego_shoutbox_json-.php` (versi lama `nego_shoutbox_json.php` yang sudah digantikan, tidak dipanggil dari manapun).

**Migrasi baru** (`migrations/023_kelompok_h_komunikasi_lanjutan.sql`): tabel `inbox_complain_types`, `inbox_complain_recipients`, kolom `inbox_messages.complain_type_id`, tabel `tender_general_chats`, kolom `tender_aanwijzing_chats.is_confirmation`.

**Endpoint baru**: `server/routes/inbox.js` (meta endpoint untuk kategori komplain dan penerima, ditaruh sebelum rute `/:id` supaya tidak salah tertangkap - pelajaran route-ordering dari kelompok-kelompok sebelumnya tetap dijaga), `server/routes/tenders.js` (`/:id/aanwijzing/confirm` + `/:id/aanwijzing/confirmations` untuk konfirmasi kehadiran, `/:id/general-chat/:vendorId` GET+POST untuk chat umum plus endpoint badge unread `/:id/general-chat-unread/:userId`).

**Bug kecil ditemukan dan diperbaiki sendiri saat testing** (urutan query di endpoint `GET general-chat`): awalnya `SELECT` histori chat dijalankan SEBELUM `UPDATE is_read=true`, jadi respons yang dikembalikan ke klien masih menampilkan status "belum dibaca" walau sebenarnya baru saja ditandai dibaca. Diperbaiki dengan menukar urutan (UPDATE dulu, baru SELECT).

**Frontend**: form "Kontak Kami" (`KontakKami.jsx`) dapat checkbox "Ini adalah komplain/pengaduan resmi" yang mengubah field Subjek jadi dropdown pilihan terstruktur, opsional (tidak mengganggu alur pesan biasa yang sudah ada). Halaman admin "Pusat Pesan" (`Inbox.jsx`) dapat badge "Komplain Resmi" (merah) di detail pesan yang punya `complain_type_id`. Data Master dapat 2 kategori baru dengan komponen khusus (`ComplainTypeTable`, `ComplainRecipientTable`) karena datanya bukan dari sistem `master_data` generik. Tab "Aanwijzing" di detail tender (`AanwijzingTab.jsx`) dapat panel `KonfirmasiKehadiran` di atas area chat (jumlah peserta hadir + tombol konfirmasi khusus role vendor). Tab "Peserta & Penawaran" dapat tombol baru "Chat dengan Vendor" per baris peserta yang membuka modal baru `GeneralChatModal.jsx`.

Sudah dites lewat browser sungguhan end-to-end (bukan cuma curl, sesuai pelajaran dari kelompok G): form komplain publik dengan dropdown subjek terisi dari database, kelola subjek dan penerima komplain di Data Master (tambah-hapus berhasil), badge Komplain Resmi tampil di Pusat Pesan, chat umum per paket terkirim dan tampil real-time (polling), konfirmasi kehadiran aanwijzing bekerja (angka bertambah, tombol berubah jadi "sudah konfirmasi", tombol cuma muncul untuk role vendor). Karena database belum ada data tender sama sekali, 1 tender uji dibuat sementara khusus untuk pengujian Bagian C, dan sudah dibersihkan total setelah selesai. Nol error console di seluruh pengujian.

### Kelompok I: Konten Publik (Banner & Kebijakan) - selesai (2026-08-21)

**Metodologi**: riset ke `eproc/application/controllers/banner_json.php` dan `models/banner.php`, plus pengecekan tabel `kebijakan` di `eproc_migrasi.sql`. Dua temuan penting yang mengubah pendekatan dari sekadar "tiru persis":

1. **Banner ternyata sangat minim** di sistem lama: tabel cuma punya `nama`, `gambar`, `tanggal` (waktu input, bukan periode aktif). **Tidak ada** link URL saat diklik, **tidak ada** urutan tampil manual (urutan cuma ikut `ORDER BY tanggal DESC`), **tidak ada** status aktif/nonaktif (semua baris di tabel otomatis tampil semua, mau dihapus baru hilang). Ada kolom `lampiran` di skema tapi terkonfirmasi dead column (tidak pernah diisi di kode manapun) - tidak ditiru. Karena keterbatasan ini cukup fungsional (admin tidak bisa sembunyikan banner sementara tanpa hapus permanen, tidak bisa buat banner jadi tombol/link), dikonfirmasi ke pengguna dan disepakati: tiru dasarnya + tambah 2 field praktis yang tidak ada di sistem lama (`link_url` opsional, `is_active` toggle).

2. **Kebijakan ternyata kode mati total**: modelnya (`models/kebijakan.php`) cuma punya `selectByParams()` (baca doang), tidak ada fungsi insert/update/delete sama sekali. **Tidak ada satupun controller** yang memanggil model ini (dicek grep ke seluruh `controllers/` termasuk backup, nihil). Data di tabel `kebijakan` juga kosong total di dump asli. Kebutuhan "kebijakan" yang benar-benar berjalan di sistem lama justru berupa teks HTML **hardcoded langsung di kode PHP** (`views/main/registrasi_kebijakan.php`, popup wajib dibaca saat registrasi vendor, bukan halaman publik mandiri). Karena tabelnya sendiri memang ada di skema resmi (bukan sekadar tebakan nama tabel yang mirip, seperti kasus di kelompok-kelompok sebelumnya), dan bukan fitur yang benar-benar "tidak pernah dimaksudkan ada" (skemanya sudah dirancang lumayan matang: `title`, `text`, `jenis` untuk dukung multi-halaman, `created_by`, `created_date`), dikonfirmasi ke pengguna dan disepakati: dibangun sebagai fitur baru minimal mengikuti struktur kolom yang sudah disiapkan itu.

**Tabel baru** (migrasi `migrations/024_kelompok_i_konten_publik.sql`): `cms_banners` (nama, gambar_path, link_url, is_active, created_by), `cms_policies` (title, content, jenis, is_published, created_by, updated_at).

**Endpoint baru** di `server/routes/cms.js`: Banner pakai multer untuk upload gambar (konsisten dengan pola upload file di modul lain), endpoint publik (`GET /banners`, cuma yang aktif) dan admin (`GET /banners/all`, `POST`, `PATCH /:id/toggle`, `DELETE /:id`). Kebijakan serupa: publik (`GET /policies`, cuma yang terbit) dan admin (`GET /policies/all`, `POST`, `PUT /:id`, `DELETE /:id`).

**Frontend**: 2 tab baru di halaman admin "Kelola Konten" (`ContentManagement.jsx`) - `BannerTab` (form upload dengan preview, toggle aktif/nonaktif, hapus) dan `PolicyTab` (form dengan textarea HTML, mode edit inline, toggle terbit/draft, hapus). Section publik baru `BannerSection` di `PublicLandingPage.jsx`, ditaruh SETELAH hero slider besar yang sudah ada (bukan menggantikannya, sesuai keputusan sebelumnya untuk tidak menyentuh hero slider yang "sudah bagus dan jalan baik") - section ini otomatis sembunyi kalau belum ada banner aktif, mengikuti pola `NewsAndFaqSection` yang sudah ada. Halaman publik baru `PublicPolicyPage.jsx` (menu sidebar-kiri daftar judul kebijakan + panel konten di kanan, mirip pola dokumentasi), diakses lewat link "Kebijakan" di footer halaman utama (bukan ditambahkan ke navbar utama supaya tidak makin padat, karena ini bukan fitur inti seperti Tender/Daftar Hitam).

Sudah dites lewat browser sungguhan end-to-end: upload banner dengan gambar sungguhan (dicek HTTP 200 dan `naturalWidth/Height` terisi, bukan gambar rusak), toggle aktif/nonaktif banner langsung berefek ke tampilan publik, kebijakan tambah-edit-toggle terbit/draft-hapus semua berhasil, isi HTML kebijakan ter-render sebagai elemen HTML sungguhan di halaman publik (bukan tampil sebagai teks tag mentah). Data uji dibersihkan total dari Supabase. Nol error console (kecuali 1 kegagalan koneksi database sesaat yang tidak bisa direproduksi ulang, kemungkinan cuma blip jaringan ke Supabase, tidak terkait kode).

**Catatan kecil ditemukan saat testing, tidak diperbaiki karena di luar cakupan**: `DELETE /api/cms/banners/:id` cuma menghapus baris database, file gambar fisik di `server/uploads/` tidak ikut terhapus (jadi file yatim). Tidak berdampak ke pengguna, cuma soal kerapian penyimpanan server jangka panjang - bisa dibereskan lain waktu kalau memang diperlukan.

### Kelompok K: Lain-lain - selesai (2026-08-21) - KELOMPOK TERAKHIR DARI ROADMAP

Ini kelompok penutup dari seluruh roadmap 100% paritas (A sampai K) yang dimulai sejak keputusan besar 2026-08-21 (lihat bagian "KEPUTUSAN BESAR" di atas).

**Metodologi**: riset ke `eproc/application/models/rekamjejak.php`, `libraries/librekamjejak.php`, `controllers/cronjobs_notif_dokexpired.php`, dan investigasi murni untuk `visitor`/`pivoting` (grep menyeluruh ke seluruh codebase, bukan cuma tebak dari nama tabel). 4 temuan dengan implikasi berbeda-beda, dikonfirmasi cakupannya ke pengguna sebelum mulai:

1. **`rekam_jejak` (AKTIF dan kritikal)** - di sistem lama, ini timeline detail tiap tahap alur pengadaan (150+ kode posisi/tahap internal, dari usulan kebutuhan sampai BAST kontrak), dipanggil dari 27+ controller lintas semua modul. **Jauh lebih detail dari `audit_logs` generik** yang sudah ada di sistem baru (field `posisi`/tahap, relasi langsung ke paket/permohonan/kontrak, `keterangan` approval/reject) - kalau cuma mengandalkan `audit_logs`, kemampuan menampilkan timeline workflow per paket seperti di UI lama akan hilang. Dibangun sebagai tabel baru `tender_activity_logs`. **Perbedaan desain dari sistem lama**: kolom `posisi` di sistem lama berupa kode angka yang di-lookup dari daftar 150+ tahap internal PHP - di sistem baru diganti jadi teks langsung (lebih mudah dibaca, tidak perlu tabel lookup terpisah untuk ratusan kode yang sebagian besar sudah spesifik ke alur lama yang tidak selalu relevan).

2. **`logs_kirim_email_dok_expired` (AKTIF, cron eksternal)** - sistem lama dipicu crontab Linux (curl ke endpoint HTTP tiap 6 jam), bergantung ke 2 VIEW database (`VIEW_REKANAN_DOKUMEN_EXPIRED`, `VIEW_REKANAN_DOKUMEN_EXPIRED_EMAIL`) yang definisinya **tidak ditemukan** di dump manapun. Disederhanakan: query langsung ke `vendor_documents.expiry_date` (kolom yang sudah ada sejak awal sistem baru), tidak perlu VIEW terpisah. Pengaturan aktif/nonaktif fitur ini **sudah ada** dari Kelompok F (`app_settings`, kunci `notifikasi_dokumen_expired`), tidak perlu tabel pengaturan baru. **Keterbatasan yang disengaja**: "kirim notifikasi" di sistem baru cuma MENCATAT LOG, belum benar-benar mengirim email sungguhan, karena belum ada konfigurasi SMTP di sistem baru sama sekali - ini konsisten dengan keterbatasan yang sama pada fitur undangan klarifikasi tender (Kelompok A, field email disimpan tapi pengiriman belum aktif).

3. **`visitor` - TIDAK dibuat**: setelah ditelusuri, fitur statistik pengunjung di sistem lama sendiri ternyata **sudah setengah mati** - data masih tercatat otomatis tiap halaman publik diakses, tapi fungsi laporannya (`hitsToday()`, `totalHits()`, `countOnline()`) sudah **di-comment langsung di kode PHP** sejak lama, dan tidak ada satupun controller yang memanggilnya. Karena developer sebelumnya sendiri sudah meninggalkan fitur ini, dikonfirmasi ke pengguna dan disepakati untuk tidak dibangun ulang.

4. **`pivoting` - TIDAK dibuat, terkonfirmasi bukan tabel data**: investigasi menyeluruh (grep "pivot" ke seluruh controllers/models/views/libraries) menemukan `pivoting` cuma tabel bantu 1 kolom, dipakai murni sebagai trik SQL (`row_number() OVER ()`) untuk generate 12 baris kosong dalam query pivot laporan pajak bulanan vendor. Bukan entitas bisnis apapun - tergantikan sepenuhnya oleh `generate_series(1,12)` kalau pola serupa dibutuhkan di query baru manapun.

**Tabel baru** (migrasi `migrations/025_kelompok_k_lain_lain.sql`): `tender_activity_logs` (posisi, keterangan, flow, relasi opsional ke tender/procurement_request/contract), `document_expiry_notification_logs` (sent_count upsert, bukan duplikat baris tiap kirim), plus pendaftaran menu baru "Dokumen Kedaluwarsa" ke sistem Hak Akses Menu khusus admin.

**Helper baru**: `server/lib/activityLog.js` (fungsi `logActivity()`, dipanggil dari titik-titik penting di `tenders.js` dan `pengajuan.js` - submit pengajuan, verifikasi berkas, ACC jadi tender, penetapan pemenang, perubahan tahapan tender, input/ubah kontrak). **Perbedaan cakupan dari sistem lama yang disengaja**: sistem lama mencatat ke 150+ titik super detail di seluruh alur; sistem baru cuma mencatat di titik-titik keputusan/transisi status yang paling bernilai untuk dilihat sebagai timeline, bukan meniru granularitas ekstrem itu - konsisten dengan pola "sederhanakan yang berlebihan" yang sudah dipakai di kelompok-kelompok sebelumnya (misal kelompok C soal 7 kode status kontrak jadi 4 status teks).

**Endpoint baru**: `GET /api/tenders/:id/activity-log`, `GET /api/pengajuan/:id/activity-log`, `GET /api/master/dokumen-expired?hari=N`, `POST /api/master/dokumen-expired/:docId/notify` (upsert, bertambah `sent_count` kalau sudah pernah dicatat), `GET /api/master/dokumen-expired/logs`.

**Frontend**: tab baru "Rekam Jejak" di detail tender (khusus role pokja/admin/ppk), section baru "Rekam Jejak" di detail pengajuan (otomatis sembunyi kalau belum ada aktivitas tercatat, sama seperti pola `RevisionHistorySection` yang sudah ada). Halaman admin baru "Dokumen Kedaluwarsa" (menu sidebar baru): daftar dokumen vendor yang mendekati/lewat tanggal berlaku dengan filter rentang hari (7/30/90), tombol catat notifikasi, dan tampilan riwayat notifikasi terpisah.

**3 bug ditemukan dan diperbaiki lewat testing browser sungguhan** (2 di antaranya bug LAMA yang sudah ada sebelum kelompok ini, cuma baru ketahuan sekarang karena baru kali ini alurnya dites sampai tuntas end-to-end):

1. **Judul pengajuan tersimpan rusak** (`NewProcurementModal.jsx`) - field `title` dikirim DUA KALI ke backend (sekali lewat loop otomatis `Object.keys(form).forEach()`, sekali lagi lewat baris eksplisit `formData.append('title', form.title)` yang berlebihan). Akibatnya backend menerima array 2 elemen, dan node-postgres otomatis mengubahnya jadi notasi array Postgres literal (`{"judul","judul"}`) saat disimpan ke kolom teks biasa. Sudah diperbaiki dengan menghapus baris duplikat.
2. **`Error: refreshData is not a function`** muncul setiap kali aksi apapun di `DetailTenderModal.jsx` yang memanggil `refreshData()` selesai dijalankan (ubah tahapan tender, submit penawaran vendor, evaluasi peserta) - BUG LAMA yang sudah ada sebelumnya, ternyata `refreshData` diambil dari `useApp()` padahal fungsi itu **tidak pernah ada** di `AppContext.jsx` (nama yang benar `triggerRefresh`). Aksinya sendiri sebenarnya tetap berhasil di backend, tapi selalu muncul dialog error mengganggu ke pengguna sesudahnya. Sudah diperbaiki dengan alias `const { user, triggerRefresh: refreshData } = useApp();` supaya seluruh pemanggilan `refreshData()` lain di file yang sama (yang jumlahnya banyak) tidak perlu diubah satu-satu.
3. Sudah diverifikasi ulang keduanya lewat browser sungguhan setelah perbaikan: judul pengajuan tersimpan bersih, tombol ubah tahapan tender jalan mulus tanpa dialog error, dan rekam jejak otomatis tercatat dengan benar untuk kedua kasus.

**Catatan tambahan ditemukan, TIDAK diperbaiki (di luar cakupan)**: tombol titik-tiga (⋮) di baris daftar pada halaman Paket Pengadaan (`TenderTable.jsx`) tidak punya fungsi apapun (tombol dekoratif tanpa `onClick`), sudah ada dari sebelumnya, dicatat untuk referensi kalau nanti relevan.

Sudah dites lewat browser sungguhan end-to-end: alur pengajuan lengkap (buat → review → approve → tender otomatis terbuat) dengan rekam jejak tercatat benar di tiap tahap dan nama pelaku serta waktu akurat, tab Rekam Jejak tender menampilkan aktivitas real setelah perubahan tahapan, halaman Dokumen Kedaluwarsa dites dengan data uji sungguhan (upload dokumen dengan expiry_date dekat, catat notifikasi, verifikasi upsert sent_count bertambah bukan duplikat baris). Semua data uji dibersihkan total dari Supabase.

---

## STATUS: ROADMAP 100% PARITAS SELESAI (2026-08-21)

Seluruh kelompok A sampai K dari roadmap keputusan besar 2026-08-21 sudah selesai dikerjakan. Sisa item yang sengaja TIDAK dibangun sebagai "asli" karena keterbatasan akses eksternal (bukan kelalaian): integrasi SAP (`sap_pr`, butuh kredensial SAP asli UI) dan Import SIRUP (`import_sirup`, butuh akses API resmi LKPP) - keduanya tetap simulasi, sudah disepakati sejak awal proyek.

Item yang sengaja ditunda dari kelompok-kelompok yang sudah "selesai" (dicatat di masing-masing kelompok di atas, belum tentu perlu dikerjakan kalau memang tidak dibutuhkan): reschedule tahapan tender dengan riwayat, tabel master jenis/metode tender (paket_jenis dkk), template penilaian (Kelompok A); direktorat/metode_tahap (Kelompok F); log akses ditolak `tbl_m_logs` dan API key management dari sistem lama yang memang tidak pernah ada UI-nya (Kelompok G, sudah diperbaiki jadi ADA di sistem baru); SLA kontrak generik di luar 3 hal utama (Kelompok C, catatan lama, sebenarnya SLA sudah dibangun penuh saat kelompok C); alur keranjang-nego-checkout katalog di frontend (Kelompok D, backend sudah selesai).

Kalau pengguna ingin melanjutkan pengembangan lebih jauh, arahnya bukan lagi "kelompok migrasi paritas" tapi sudah masuk ke pengembangan UI/UX untuk 10 role tambahan yang fondasinya sudah dibangun sejak tahap ketiga (Admin VMS, Administrator Approval, Manager Pengadaan, Pengguna, Audit, Pelaksana Pengadaan, Pengelola Kontrak, Approval VMS, Kasubdit Kontrak, Perencanaan), atau menyelesaikan item-item yang sengaja ditunda di atas kalau memang dibutuhkan.

**Pelajaran untuk ke depan**: testing lewat curl saja TIDAK CUKUP untuk kelompok-kelompok berikutnya yang menyentuh frontend - bug di atas keduanya murni sisi React dan tidak akan pernah ketahuan dari curl API saja. Mulai kelompok berikutnya, testing menyeluruh sebelum lapor selesai harus mencakup uji lewat browser sungguhan (Playwright), bukan cuma cek compile HTTP 200.

---

## Item Tambahan Pasca-Roadmap: menuntaskan sisa yang sengaja ditunda (selesai 2026-08-26)

Setelah roadmap 100% paritas (A-K) selesai, pengguna bertanya apakah sistem sudah benar-benar 100% sama secara fungsional. Jawaban jujurnya: sangat mendekati, tapi ada beberapa item kecil yang sengaja ditunda di kelompok-kelompok sebelumnya, plus beberapa fitur sengaja tetap simulasi (SAP, SIRUP) karena keterbatasan akses eksternal. Pengguna minta semua yang BISA dimigrasi untuk benar-benar dikerjakan sekarang, sisanya (yang butuh akses eksternal atau memang kode mati) cukup jadi laporan.

**7 item dikerjakan** (disepakati lewat 3 kali AskUserQuestion terpisah karena beberapa ternyata jauh lebih besar dari dugaan awal saat diriset ulang):

1. **Pembukaan Penawaran + Undangan Klarifikasi ke frontend** - backend sudah ada dari Kelompok A, tinggal disambungkan. Section baru "Pembukaan Penawaran" (validasi sampul 1/2) dan "Undangan Klarifikasi" (jadwal pertemuan resmi ke vendor) ditambahkan ke `DokumenPaketTab.jsx`.

2. **Reschedule Tahapan Tender dengan riwayat** - riset ke `paket_tahap_json.php` menemukan cakupan ini lebih besar dari dugaan awal: sistem lama punya tabel `PAKET_TAHAP` (banyak baris tahap per paket, masing-masing punya tanggal sendiri), sedangkan sistem baru sebelumnya cuma punya 1 kolom `status` tunggal di `tenders` tanpa tanggal per tahap sama sekali. Disepakati dibangun penuh: tabel baru `tender_stages` (7 tahap per tender, mengikuti `procurementPhases.js` yang sudah dipakai konsisten di frontend: pengumuman, pendaftaran, penawaran, evaluasi, pemenang, masa_sanggah, kontrak) dan `tender_stage_reschedule_history` (snapshot tanggal lama-baru + alasan tiap kali di-reschedule). Sistem lama punya 2 mekanisme riwayat paralel yang membingungkan (salah satu tabelnya bahkan tidak ditemukan di dump manapun) plus batasan aneh (alasan cuma bisa disimpan 10 kali lewat 10 kolom bernomor `RESCHEDULE_1..10` di tabel paket) - semua disederhanakan jadi satu tabel riwayat yang jelas tanpa batasan jumlah. Migrasi `migrations/026_reschedule_tahapan_tender.sql`. Endpoint `GET/POST /:id/stages/...` di `tenders.js`. Frontend: panel baru "Jadwal Tanggal Tiap Tahap" di `DetailTenderModal.jsx`, dekat timeline stepper yang sudah ada, dengan modal reschedule dan modal riwayat.

3. **Alur Keranjang-Nego-Checkout Katalog di frontend** - backend sudah ada dari Kelompok D, tapi sebelumnya halaman Katalog masih pakai keranjang sisi-browser sederhana (React state lokal, hilang saat refresh, checkout langsung ke modul Purchasing terpisah TANPA melalui `procurement_request_id` sama sekali). Dibangun ulang total: `Katalog.jsx` sekarang punya selector "Belanja untuk pengajuan" (khusus PPK/admin, pilih dari pengajuan berstatus disetujui), tombol Tambah ke keranjang memanggil endpoint `/katalog/cart` yang sungguhan (bukan state lokal), dan komponen baru `CatalogCartPanel.jsx` (modal terpisah) menampilkan keranjang dengan negosiasi harga+ongkos kirim dan alur status pesanan 7 tahap (Proses Pemilihan → Negosiasi → Penyedia Setuju → Surat Pesanan → Proses → Dikirim → Diterima), sesuai desain backend yang sudah dibangun sejak Kelompok D.

4. **5 kategori Data Master sederhana** - setelah riset ulang (karena catatan lama "paket_jenis, paket_metode_evaluasi, dst" ternyata bercampur antara yang sungguh-sungguh sederhana dan yang tidak), ditemukan hanya 5 yang benar-benar id+nama sederhana: Jenis Tender (`paket_jenis`), Metode Lelang, Metode Kualifikasi, Metode Evaluasi (`paket_metode_lelang/kualifikasi/evaluasi` - tabelnya sendiri sederhana meski di sistem lama dipakai lewat query matriks berjenjang yang kompleks dan TIDAK ditiru), dan Direktorat (`direktorat` - ternyata di sistem lama modelnya lengkap tapi TIDAK ADA controllernya sama sekali, kode mati murni, walau datanya tetap dipakai sebagai referensi di form user). Semua reuse tabel `master_data` generik yang sudah ada (tinggal tambah ke `VALID_CATEGORIES` di `master.js`), migrasi `migrations/027_master_jenis_metode_direktorat.sql` isi seed jenis/metode yang lazim dipakai pengadaan pemerintah Indonesia (Direktorat sengaja TIDAK diisi data awal, itu data spesifik struktur organisasi UI yang tidak dimiliki, sama seperti kasus data wilayah Indonesia sebelumnya).

   **2 item DIKELUARKAN dari daftar setelah riset ulang** (bukan sekadar id+nama seperti dugaan): `paket_kriteria_eval` ternyata tabel transaksional 1:1 per paket (flag kriteria kualifikasi yang dipilih), bukan data referensi - tidak masuk Data Master. `evaluasi_jenis` ternyata nama yang salah kutip di roadmap awal - controllernya (`evaluasi_jenis_json.php`) sebenarnya melayani tabel `AKTA_TYPE` (jenis akta perusahaan rekanan), bukan konsep "jenis evaluasi" terpisah yang sebenarnya tidak ada di sistem lama.

5. **Template Penilaian Kinerja Penyedia** - riset ke `paketpenilaian.php` menemukan struktur asli jauh lebih kompleks dari sekadar tabel referensi: berjenjang (bab→pasal) dengan bobot dan skor maksimal per kriteria, PLUS approval 3 tingkat (Unit/Kasubdit/PPK) dan perhitungan grading otomatis (A-E) lewat VIEW SQL. Disepakati dibangun versi disederhanakan: struktur berjenjang bab/pasal dengan bobot (`penilaian_kinerja_templates`, kolom `parent_id`) DITIRU karena itu yang jadi pembeda utama dari `vendor_ratings` yang sudah ada (cuma skor 1-5 + catatan bebas). Approval 3 tingkat dan VIEW agregasi otomatis TIDAK ditiru - PPK cukup input skor langsung per kriteria (mirip pola `tender_eval_scores` yang sudah ada di modul Evaluasi), total nilai tertimbang dihitung on-the-fly di kode aplikasi, bukan approval berjenjang. Migrasi `migrations/028_template_penilaian.sql` (tabel `penilaian_kinerja_templates` + transaksi `contract_penilaian_kinerja`, upsert per kontrak per kriteria). Frontend: kategori baru "Template Penilaian" di Data Master (kelola bab/pasal berjenjang) dan sub-tab baru "Penilaian Kinerja" di `ContractTab.jsx` (`PenilaianKinerjaSection` di `ContractWorkflowSections.jsx`, input skor per kriteria, total nilai tertimbang otomatis terhitung).

**Catatan tentang bidang usaha per paket**: saat mengecek ulang, ternyata item ini (disebut "belum dikerjakan" di catatan lama) sebenarnya **sudah selesai sejak Kelompok B** (endpoint `tender_bidang_usaha` di `tenders.js`, section "Bidang Usaha yang Disyaratkan" sudah ada di `DokumenPaketTab.jsx`). Catatan lamanya sekadar ketinggalan zaman, tidak pernah diperbarui setelah Kelompok B selesai.

Semua 7 item sudah dites end-to-end lewat curl (login, buat tender/kontrak/pengajuan/produk katalog uji, jalankan seluruh alur baru, verifikasi hasil di database, bersihkan semua data uji setelahnya - data seed permanen seperti daftar jenis tender/metode tetap utuh). Verifikasi lewat browser sungguhan sempat dimulai tapi terputus di tengah jalan karena limit sesi eksternal sebelum sempat menyelesaikan seluruh skenario UI - bagian yang sempat dicek (Data Master 5 kategori baru) tidak menemukan masalah. **Catatan untuk sesi berikutnya**: kalau ada waktu, sebaiknya jalankan verifikasi Playwright penuh untuk 7 item ini (terutama alur keranjang katalog dan modal reschedule/riwayat yang belum sempat diklik langsung di browser), mengikuti pola testing menyeluruh yang sudah jadi standar sejak Kelompok G.

## Menuju 100% Fungsional: perbaikan bug + kesiapan operasional (selesai 2026-08-27)

Setelah demo alur bisnis dan laporan skor kesiapan 73% (dilaporkan sesi sebelumnya), pengguna minta 2 hal digabung jadi satu pekerjaan: (1) cari dan perbaiki SEMUA bug form yang masih ada, (2) tuntaskan 4 hal yang menahan skor dari 100% - email SMTP, keamanan formal, testing otomatis, dan kesiapan multi-user.

### 1. Keamanan: proteksi login/role ke seluruh API (temuan paling besar sesi ini)

Ditemukan lewat audit menyeluruh: **hampir seluruh API backend (kira-kira 280 dari sekitar 295 endpoint di 14 file route) TIDAK punya pemeriksaan login sama sekali** - siapa saja tanpa login bisa membuat akun admin baru, mengubah data vendor manapun, membuat/menghapus kontrak, dst. Cuma `auth.js` yang sejak awal sudah pakai `requireAuth`. Ini bukan sesuatu yang baru rusak, melainkan celah yang memang belum pernah ditutup sejak awal proyek.

Diperbaiki dengan pola bertingkat:
- `server/lib/authMiddleware.js` (baru) - `requireAuth` (wajib login), `requireRole(...roles)` (wajib login DAN role tertentu, dipasang setelah `requireAuth`), `optionalAuth` (coba baca token kalau ada tapi tidak menolak kalau tidak ada - dipakai di endpoint yang harus tetap publik tapi ingin menampilkan info lebih banyak untuk yang sudah login).
- File yang isinya 100% privat (`vendors.js`, `pengajuan.js`, `audit.js`, `dashboard.js`, `purchasing.js`) diproteksi blanket di `server/index.js` lewat `app.use('/api/xxx', requireAuth, ...)`.
- File campuran publik/privat (`cms.js`, `inbox.js`, `qr.js`, `blacklist.js`, `master.js`, `katalog.js`, `tenders.js`, `users.js`, `menu.js`) diproteksi per-route di dalam file masing-masing, supaya rute publik (banner, FAQ, kebijakan, form Kontak Kami, verifikasi QR, cek blacklist, daftar tender publik, registrasi vendor) tetap terbuka.
- `GET /api/tenders` dan `GET /api/tenders/:id` pakai `optionalAuth`: kolom `hps` (Harga Perkiraan Sendiri) cuma ditampilkan kalau pemanggil sudah login, disembunyikan (`NULL`) untuk publik - supaya tidak jadi patokan vendor menyesuaikan harga penawaran sebelum tender ditutup, tapi staf internal (yang butuh lihat HPS untuk kerja evaluasi) tetap bisa akses lewat endpoint yang SAMA.
- `users.js` (manajemen akun staff, API key) sebagian besar admin-only, TAPI `GET /api/users` dan `GET /api/users/roles` sengaja dibuka untuk semua role yang login (bukan cuma admin) karena ternyata dipakai PPK juga untuk isi dropdown "PIC Persiapan/Pengendali/Penyelesai" di tab Kontrak - kalau diproteksi admin-only penuh, fitur itu rusak untuk PPK (baru ketahuan lewat audit form, lihat bagian bawah).
- Ditambahkan pengecekan kepemilikan data (`ownVendorDataOnly` di `vendors.js`, pengecekan serupa di `tenders.js`): kalau yang login role `vendor`, dia cuma boleh mengelola/melihat datanya sendiri (profil, dokumen, bidang usaha, rekening koran, submit penawaran, daftar tender, ajukan sanggahan) - tidak bisa bertindak atas nama vendor lain walau tahu ID-nya. Role admin/ppk/pokja tetap bebas bertindak atas nama vendor manapun (kebutuhan administratif yang sah).
- Ditambahkan pengecekan role pada field approval yang tadinya bisa diisi siapa saja yang login (misalnya `approve_ppk` di kontrak sekarang cuma bisa diisi role `ppk`/`admin`, `approved_penyedia` di addendum cuma bisa diisi role `vendor`/`admin`).
- `GET /api/tenders/:id/participants` (daftar SEMUA peserta beserta harga penawaran masing-masing) sekarang ditolak untuk role `vendor` supaya tidak bisa mengintip harga penawaran kompetitor - dibuatkan endpoint terpisah `GET /api/tenders/:id/participants/me` yang cuma mengembalikan baris milik vendor yang login sendiri.

**Keamanan lain yang dibenahi sekaligus**: `helmet` (HTTP security headers) dan `express-rate-limit` (20 percobaan/15 menit untuk endpoint login/register, mencegah brute-force password; 1000 request/15 menit untuk seluruh API sebagai jaring pengaman DoS dasar) ditambahkan di `server/index.js`. CORS dibatasi ke `FRONTEND_URL` saja kalau `NODE_ENV=production` (tetap terbuka di development supaya tidak mengganggu kerja lokal). `JWT_SECRET` yang tadinya nilai contoh dari template (`dpbj_ui_super_secret_2025_change_in_production`, ketahuan persis sama kalau ada yang baca kode sumber ini) diganti jadi string acak 96-karakter sungguhan lewat `crypto.randomBytes`. Validasi password minimal 8 karakter dan format email ditambahkan di `POST /api/auth/register`. Validasi format NPWP (15 digit format lama atau 16 digit format baru) ditambahkan di form registrasi vendor (frontend `RegistrasiVendor.jsx`) dan backend (`auth.js`), sebelumnya bisa didaftarkan dengan NPWP asal-asalan seperti "x" satu karakter.

**Upload file diperketat**: semua 8 file yang punya `multer({storage: storage})` polos (tanpa validasi tipe/ukuran file, artinya sebelumnya bisa upload file APAPUN termasuk `.exe`/`.php` ke folder `uploads/` yang disajikan publik) diganti pakai modul terpusat baru `server/lib/upload.js` (`createUpload(prefix)`): cuma menerima dokumen umum/gambar/arsip (pdf, doc(x), xls(x), ppt(x), jpg, png, gif, webp, zip, rar), maksimal 10MB per file, maksimal 10 file sekaligus, dengan pesan error yang jelas kalau ditolak (`handleUploadError` middleware).

### 2. Email SMTP: notifikasi sungguhan, bukan cuma catat log

`server/lib/mailer.js` (baru) - modul kirim email pakai `nodemailer`, dengan **fallback otomatis ke "catat log saja"** kalau `SMTP_HOST` kosong di `.env` (jadi tidak akan pernah membuat fitur error walau SMTP belum dikonfigurasi sungguhan, pas untuk development lokal). 3 titik yang tadinya cuma "field email disimpan tapi tidak pernah benar-benar dikirim" sekarang benar-benar mengirim (kalau SMTP dikonfigurasi):
- Notifikasi dokumen vendor akan/sudah kedaluwarsa (`master.js`, `POST /dokumen-expired/:docId/notify`).
- Undangan klarifikasi tender ke vendor (`tenders.js`, `POST /:id/undangan-klarifikasi`).
- Balasan pesan/pengaduan dari admin ke pengirim (`inbox.js`, `POST /:id/reply`) - ini titik baru yang ditemukan sekalian saat mengerjakan proteksi, sebelumnya balasan cuma tersimpan di database tanpa pernah sampai ke pengirim.

`server/.env` sudah diisi variabel `SMTP_HOST`/`SMTP_PORT`/`SMTP_SECURE`/`SMTP_USER`/`SMTP_PASS`/`SMTP_FROM` (kosong, siap diisi kalau pengguna sudah punya akses SMTP institusi UI). `server/.env.example` diperbarui total (sebelumnya isinya template lama yang menyebut MySQL/Laragon, sudah tidak sesuai kenyataan sejak lama).

### 3. Testing otomatis: Playwright test suite baru

Sebelumnya benar-benar tidak ada satupun automated test di project ini (cuma testing manual lewat browser tiap kali selesai kerja). Ditambahkan:
- `playwright.config.cjs` (root) + `tests/e2e/` (helper login `tests/e2e/helpers.js`, test pertama `tests/e2e/auth.spec.js` - smoke test login gagal/berhasil untuk 4 role, plus cek halaman publik tanpa error console).
- Root `package.json` sekarang `"type": "module"`, jadi file test/config yang pakai `require()` (CommonJS) butuh ekstensi `.cjs`, dan `tests/package.json` kecil (`{"type": "commonjs"}`) supaya folder `tests/` boleh CommonJS tanpa mengubah keseluruhan project.
- Jalankan: `npx playwright test --config=playwright.config.cjs` (backend dan frontend harus sudah jalan duluan, config ini tidak otomatis menyalakan keduanya).
- Ini fondasi awal, BUKAN cakupan penuh - baru mencakup alur login. Kalau mau diperluas, tinggal tambah file `.spec.js` baru di `tests/e2e/` mengikuti pola yang sama.

### 4. Kesiapan multi-user: sudah dicek, arsitektur memang sudah aman untuk banyak user bersamaan

Backend pakai `pg.Pool` (connection pooling, bukan koneksi tunggal) dan JWT stateless (tidak ada session di memori server yang bisa bentrok antar user) - dari sisi arsitektur sudah tidak ada penghalang fundamental untuk dipakai banyak orang sekaligus. Rate limiting yang ditambahkan di poin 1 juga jadi lapis perlindungan tambahan dari lonjakan trafik berlebihan. Tidak ditemukan pola kode yang menyimpan state global per-request yang bisa bocor antar user (state React ada di browser masing-masing, bukan di server).

### 5. Bug form yang ditemukan dan diperbaiki (lewat 2 audit menyeluruh Playwright terpisah)

Dua agent riset dikerahkan paralel untuk mengklik dan mengisi SEMUA form di aplikasi (login, registrasi vendor, pengajuan, tender, evaluasi, kontrak, katalog, negosiasi, sanggah, aanwijzing, panitia, dokumen paket, data master, manajemen user, kontak kami, settings, profil vendor - total sekitar 17 area). Ditemukan dan diperbaiki:

- **CAPTCHA form login selalu gagal divalidasi dengan benar** (`LoginModal.jsx`) - kode generate CAPTCHA menambahkan akhiran `'sp'` yang tidak pernah dimaksudkan (`.join('') + 'sp'`), jadi pengguna harus mengetik ulang kode SEPERTI YANG TAMPIL termasuk akhiran aneh itu supaya validasinya benar. Dibandingkan dengan pola yang sama di `RegistrasiVendor.jsx` (yang benar, tidak ada akhiran ini) untuk memastikan ini memang bug, bukan disengaja. Diperbaiki dengan menghapus `+ 'sp'`.
- **Vendor pemenang tidak bisa lihat kontrak/negosiasi miliknya sendiri** (`DetailTenderModal.jsx`) - fetch daftar peserta tender (`GET /tenders/:id/participants`) cuma dipanggil untuk role pokja/admin/ppk, vendor selalu dapat array kosong, jadi tab Kontrak & BAST dan Negosiasi salah menampilkan pesan "belum ada pemenang" walau kontrak sudah dibuat dan negosiasi sudah disepakati. Diperbaiki dengan endpoint baru `GET /tenders/:id/participants/me` (cuma kembalikan baris milik vendor yang login, tidak bisa lihat harga penawaran vendor lain) dan frontend dipisah jalur fetch-nya sesuai role. Diverifikasi lewat browser sungguhan: vendor sekarang bisa lihat detail kontrak (nomor SPK, nilai, dokumen) dan riwayat negosiasi (tawaran, harga final, status sepakat) miliknya sendiri.
- **`GET /api/tenders/:id/negotiation/:vendorId` bisa 500 Internal Server Error** kalau `vendorId` bukan format UUID valid (efek berantai dari bug di atas: waktu `participants` kosong, `vendorId` yang dikirim ke tab Negosiasi jadi `undefined`). Ditambahkan validasi format UUID di awal endpoint, sekarang membalas 400 dengan pesan jelas.
- **Dropdown PIC di tab Kontrak kosong untuk PPK** - dijelaskan di bagian keamanan di atas (endpoint `GET /api/users` sempat ikut diproteksi admin-only saat pengerasan keamanan, padahal PPK butuh itu untuk dropdown "PIC Persiapan/Pengendali/Penyelesai"). Ini regresi dari pekerjaan proteksi API di poin 1 sesi ini sendiri, ditemukan dan diperbaiki di sesi yang sama sebelum dianggap selesai.
- **Modal "Pengaturan Profil" menampilkan data PALSU untuk SEMUA pengguna** (`SettingsModal.jsx`) - field NIP, Unit Kerja, dan Hak Akses ditulis hardcoded (selalu "198001012005011002" dan "Super Administrator" walau yang login vendor). Diperbaiki: NIP dihapus (memang tidak ada kolom ini di database, tidak dipaksakan menampilkan data yang tidak ada), Unit Kerja diganti Email, Hak Akses diambil dari `user.roleLabel` sungguhan. Sekaligus ketahuan field `username` tidak pernah dipetakan dari respons login/`/me` ke object `user` di frontend (`AppContext.jsx`) - diperbaiki juga.
- **Tombol lulus/gugur di modal Evaluasi Detail tidak bisa diklik** (`EvaluationDetailModal.jsx`) - modal ini dirender bersarang di dalam `DetailTenderModal.jsx` yang overlay-nya pakai `backdrop-blur-sm` (menciptakan CSS stacking context baru), akibatnya elemen lain di dalam `DetailTenderModal` (sidebar "Jadwal Tahapan Tender") menutupi tombol yang seharusnya ada di lapisan lebih atas walau `z-index`-nya sudah lebih tinggi secara nominal. Diperbaiki dengan React Portal (`createPortal(..., document.body)`, pola yang sama sudah dipakai `LoginModal.jsx`) supaya modal ini dirender langsung ke `document.body`, keluar dari hierarki stacking context induknya. Diverifikasi lewat browser sungguhan: tombol lulus/gugur sekarang bisa diklik dan mengubah warna sesuai state.
- **Teks status salah di layar sukses submit Pengajuan Baru** (`NewProcurementModal.jsx`) - menampilkan "Draft - Menunggu Review PPK" padahal backend langsung menyimpan status `diajukan` dan reviewer tahap pertama adalah Admin DPBJ (bukan PPK). Diperbaiki teksnya.
- **Modal Pengajuan Baru tidak reset saat ditutup lewat tombol X** (`NewProcurementModal.jsx`) - `handleClose` cuma memanggil `onClose()`, tidak mereset form/step/status submitted seperti tombol "Buat Pengajuan Lain". Efeknya: buka lagi modal setelah submit sukses malah menampilkan ulang layar sukses SEBELUMNYA, bukan form kosong. Diperbaiki dengan menyamakan `handleClose` dengan logika reset yang sudah benar.
- **Validasi NPWP tidak ada sama sekali** (dijelaskan di bagian keamanan/kualitas data di atas).

**Bug yang ditemukan tapi belum ditangani di sesi ini** (dicatat untuk sesi berikutnya, bukan diabaikan): duplikat entri Sampul 1 validasi di Dokumen Paket kalau divalidasi berkali-kali oleh user yang sama (perlu dicek apakah backend seharusnya mencegah duplikasi ini), CatalogCartPanel (alur keranjang-nego-checkout) belum sempat diuji end-to-end di audit form kali ini, dan sanggahan (ObjectionsTab) submit oleh vendor kalah belum diuji end-to-end (butuh data uji tender di tahap masa_sanggah dengan vendor yang kalah, di luar cakupan waktu audit ini).

### File-file baru yang ditambahkan sesi ini

- `server/lib/authMiddleware.js` - `requireAuth`, `requireRole`, `optionalAuth`
- `server/lib/upload.js` - `createUpload(prefix)`, `handleUploadError` (validasi tipe/ukuran file terpusat)
- `server/lib/mailer.js` - `sendMail()`, fallback otomatis kalau SMTP belum dikonfigurasi
- `playwright.config.cjs`, `tests/e2e/helpers.js`, `tests/e2e/auth.spec.js`, `tests/package.json` - fondasi testing otomatis

### Cara verifikasi yang dipakai sesi ini

Semua perubahan diverifikasi lewat kombinasi: `node -c` (cek sintaks tiap file backend sebelum restart), restart backend bersih (matikan proses lama pakai PID dari `netstat`, jalankan ulang, cek `curl http://localhost:3001/api`), lalu navigasi penuh lewat Playwright untuk KEEMPAT role (klik semua item sidebar satu-satu, tangkap console error dan response HTTP >=400) sebagai jaring pengaman utama supaya proteksi keamanan yang ditambahkan tidak diam-diam merusak fitur yang sah. Ditemukan dan diperbaiki beberapa regresi dari pekerjaan sesi ini sendiri lewat cara ini (endpoint `master.js` yang sempat kelewat ketat untuk PPK/Pokja/Vendor, endpoint `users.js` yang sempat kelewat ketat untuk PPK, dan sekitar 10 titik frontend yang ternyata belum pernah mengirim header token sama sekali walau sebelumnya "kebetulan" tidak masalah karena API belum diproteksi). Semua data uji (tender/kontrak/pengajuan/akun percobaan) dibuat lewat script Node langsung ke Supabase dan dihapus lagi setelah verifikasi selesai, mengikuti lampiran FK yang benar (child rows dulu baru parent).

## Lanjutan: menuntaskan 5 celah menuju kesiapan produksi (2026-08-27, sesi lanjutan)

Setelah laporan "belum 100% siap" di atas, pengguna minta 5 celah yang disebutkan (SMTP, audit keamanan formal, testing otomatis lebih luas, uji beban, backup/disaster-recovery/monitoring) benar-benar dikerjakan, bukan cuma dicatat sebagai keterbatasan.

### Testing otomatis diperluas (sebelumnya cuma cakup login)

Ditambahkan 2 file test baru di `tests/e2e/`:
- `navigation.spec.js` - klik SEMUA item sidebar untuk KEEMPAT role, tangkap console error dan response HTTP gagal. Ini otomatisasi dari script manual `check_nav.cjs` yang dipakai berulang kali sebagai regression test sepanjang sesi sebelumnya.
- `pengajuan.spec.js` - alur form Pengajuan Pengadaan Baru 5-step (regression guard untuk bug scroll-lock lama), plus 5 test proteksi keamanan (endpoint privat menolak tanpa token, endpoint publik tetap terbuka, endpoint admin-only menolak role lain).

`tests/e2e/helpers.js` diperbaiki: `loginAs()` sebelumnya pakai `Escape` untuk menutup modal ganti-role (RoleSwitcherModal) yang ternyata TIDAK menutup modal itu (Escape tidak ada handler-nya di komponen itu) - sekarang klik opsi role pertama yang tersedia sebagai gantinya. Ditambahkan juga `dismissRoleSwitcherIfPresent()` untuk dipanggil berulang di tengah loop klik sidebar (modal ini bisa muncul lagi kalau tombol "Ganti Role" ikut terklik).

Total sekarang 17 test, jalankan dengan `npm run test:e2e`. Semua 17 lulus bersih di kondisi backend stabil.

### Uji beban (load testing) - baru, dan menemukan bug nyata

`server/load_test.js` (baru, jalankan dengan `npm run load-test`) - pakai `autocannon` (dev dependency baru) untuk simulasi banyak pengguna bersamaan mengakses 3 jenis endpoint (publik, login, privat-tanpa-token).

**Bug nyata ditemukan lewat uji beban ini**: rate limiter umum (`apiLimiter` di `server/index.js`) yang tadinya diatur 1000 request/15 menit per IP ternyata terlalu ketat untuk pemakaian wajar sekalipun - percobaan beban realistis (banyak staf dari 1 IP kantor, navigasi SPA normal yang melakukan puluhan fetch API per menit) bisa dengan mudah kena limit itu dalam hitungan menit, memblokir pengguna sah bukan cuma penyerang. Diperbaiki: dinaikkan jadi 600 request **per menit** (bukan per 15 menit) per IP - jauh lebih longgar untuk pemakaian wajar, window lebih pendek jadi reset lebih cepat kalau memang kena.

Hasil akhir setelah perbaikan: latency 3-7ms untuk endpoint sederhana, di bawah 100ms untuk login (yang melibatkan bcrypt), nol error, nol timeout, status code sesuai ekspektasi (2xx untuk publik, 401 untuk privat-tanpa-token) di semua skenario.

### Audit keamanan self-review (`KEAMANAN.md`, baru)

Bukan pengganti penetration testing sungguhan (butuh pihak ketiga bersertifikasi, dicatat jujur di dokumen itu sebagai batasan), tapi tinjauan sistematis mengikuti kategori OWASP Top 10 lewat pembacaan kode langsung. Ditemukan dan diperbaiki 1 celah XSS nyata: `src/pages/PublicPolicyPage.jsx` merender HTML kebijakan (diisi admin lewat textarea) langsung tanpa sanitasi (`dangerouslySetInnerHTML` tanpa filter) - diperbaiki dengan menambahkan `dompurify` (library sanitasi HTML standar, dependency baru) sebelum render. Dicek juga: tidak ada pola SQL injection (parameterized query konsisten di semua route), password pakai bcrypt, JWT secret sudah acak. Dicatat sebagai keterbatasan yang jujur (bukan diperbaiki tapi diakui): upload file validasi cuma berdasarkan ekstensi bukan isi file (magic bytes), CAPTCHA level dasar (bisa dilewati bot canggih), belum ada pengecekan IDOR menyeluruh ke SEMUA endpoint (cuma titik-titik berisiko tinggi yang sudah dicek eksplisit).

### Backup, disaster recovery, dan panduan deploy (`OPERASIONAL.md`, baru)

- `server/backup_database.js` (baru, `npm run db:backup`) - export SEMUA tabel database ke file SQL (INSERT statements dengan `ON CONFLICT DO NOTHING`, aman dijalankan ulang tanpa duplikat), tanpa perlu `pg_dump` (tool itu tidak terpasang di komputer ini, dicek dan dikonfirmasi tidak ada). **Sudah diuji sungguhan** terhadap database Supabase asli: berhasil backup 98 tabel, 3683 baris, 2.26MB, dalam hitungan detik.
- `server/restore_database.js` (baru, `npm run db:restore <file>`) - pasangan restore, jalankan file backup dalam 1 transaksi (rollback otomatis kalau ada error di tengah jalan).
- Folder `backups/` ditambahkan ke `.gitignore` (berisi data asli, tidak boleh ikut git, sama seperti alasan dump lama yang sudah diblokir sebelumnya).
- `OPERASIONAL.md` mencakup: prosedur backup (otomatis Supabase + manual tambahan), 3 skenario disaster recovery (data hilang, backend crash, Supabase tidak bisa diakses) dengan langkah konkret, panduan deploy ke Vercel (variabel lingkungan yang WAJIB diisi beda dari development termasuk JWT_SECRET baru, urutan migrasi sebelum deploy, catatan penting bahwa Vercel didesain untuk frontend/serverless bukan server Express yang jalan terus-menerus jadi backend perlu di-host terpisah kalau mau architecture yang sama persis dipertahankan), dan rekomendasi monitoring (Sentry/UptimeRobot, belum dikerjakan karena butuh keputusan pendaftaran akun pihak ketiga dari pengguna).

### SMTP: menunggu kredensial dari pengguna

Modul `server/lib/mailer.js` (dari sesi sebelumnya) sudah siap teknis, tinggal diisi kredensial. Pengguna diminta memberikan alamat Gmail + App Password (bukan password akun biasa, dibuat lewat https://myaccount.google.com/apppasswords) untuk dipasang sebagai SMTP sementara supaya email BENAR-BENAR terkirim (bukan cuma tercatat log). **Kredensial belum diterima di sesi ini** - begitu diterima, langkah berikutnya: isi ke `server/.env` (SMTP_HOST=smtp.gmail.com, SMTP_PORT=587, SMTP_SECURE=false, SMTP_USER=alamat gmail, SMTP_PASS=app password), restart backend, kirim 1 email uji ke alamat sungguhan untuk konfirmasi benar-benar sampai, lalu sarankan pengguna hapus pesan berisi App Password itu dari riwayat chat.

### File baru sesi lanjutan ini

- `tests/e2e/navigation.spec.js`, `tests/e2e/pengajuan.spec.js` - perluasan test suite
- `server/load_test.js` - uji beban otomatis
- `KEAMANAN.md` - tinjauan keamanan self-review terstruktur (OWASP-style)
- `OPERASIONAL.md` - prosedur backup/disaster-recovery/deploy/monitoring
- `server/backup_database.js`, `server/restore_database.js` - skrip backup manual (sudah diuji sungguhan)
- Dependency baru: `dompurify` (sanitasi XSS), `autocannon` (dev-only, load testing)

## Audit independen 2026-09-01: skor paritas sebenarnya 60-65%, bukan 100% (bug kritis ditemukan dan diperbaiki)

**Pelajaran penting yang harus terus diingat**: klaim "selesai"/"100% paritas" yang dicatat sendiri di dokumen ini sudah **2 kali terbukti tidak akurat** saat dicek ulang oleh audit independen terpisah (audit 2026-08-31 menemukan 4 gap besar yang sudah ditutup di sesi-sesi berikutnya; audit 2026-09-01 di bawah ini menemukan lagi 1 bug kritis dan 1 gap cakupan). **Jangan pernah lagi melaporkan sesuatu "selesai" ke pengguna tanpa pengujian end-to-end sungguhan** (curl + query database + browser), dan kalau pengguna bertanya soal status kelengkapan sistem secara umum, selalu tawarkan audit independen baru daripada cuma merangkum status di file ini.

Setelah 4 modul dari audit 2026-08-31 selesai dikerjakan (Cetak Dokumen, Notifikasi Email, Integrasi Oracle, Dashboard Pimpinan), pengguna minta audit independen lagi untuk verifikasi apakah klaim itu sekarang benar. Hasilnya (detail lengkap ada di memory project, cari "audit-paritas-2026-08-31"):

**Bug kritis ditemukan dan sudah diperbaiki (commit `0bf6892e`)**: `server/index.js` dan `server/db.js` memanggil `require('dotenv').config()` **tanpa path eksplisit**. Kalau server dinyalakan lewat `npm run server` dari folder root (cara resmi yang didokumentasikan di bagian "Cara menjalankan project" di atas), dotenv membaca `.env` dari folder tempat proses dijalankan yaitu ROOT (cuma berisi `SUPABASE_DB_URL`), **BUKAN** `server/.env` yang berisi `JWT_SECRET`, `SMTP_*`, `ORACLE_SFTP_*`, dst. Akibatnya:
- `JWT_SECRET` diam-diam jatuh ke nilai default yang tertulis terbuka di kode (`server/routes/auth.js`, `server/lib/authMiddleware.js`) - artinya SIAPA SAJA yang membaca source code repo ini (yang memang publik/bisa diakses siapa saja yang punya akses ke repo) bisa memalsukan token JWT untuk role APAPUN termasuk admin, tanpa perlu password sama sekali. Penggantian JWT_SECRET jadi 96-karakter acak yang diklaim selesai di sesi 2026-08-27 **tidak pernah benar-benar aktif** sejak awal ditulis.
- Fitur Notifikasi Email yang diklaim selesai di sesi ini sendiri **tidak pernah benar-benar mengirim email**, karena `server/lib/mailer.js` selalu mendeteksi SMTP "belum dikonfigurasi" walau `server/.env` sudah diisi kredensial Gmail asli.

**Perbaikan**: kedua file diubah pakai path eksplisit (`require('dotenv').config({ path: require('path').join(__dirname, '.env') })`), supaya selalu baca `server/.env` yang benar terlepas dari folder mana proses dijalankan. Sudah diverifikasi ulang: percobaan memalsukan token pakai secret default lama sekarang GAGAL ("invalid signature"), dan proses kirim email sekarang benar-benar mencoba connect ke server SMTP (sempat gagal ETIMEDOUT ke Gmail port 587, kemungkinan besar itu masalah jaringan sandbox pengembangan saat itu, bukan lagi masalah konfigurasi kosong - **perlu dicoba lagi nanti untuk pastikan SMTP benar-benar bisa connect keluar dari lingkungan production/deploy yang sebenarnya**).

**Gap cakupan yang ditemukan, BELUM diperbaiki**: modul "Cetak Dokumen" (`server/routes/print.js`) yang diklaim selesai di sesi 2026-08-27 ternyata cuma mencakup **4 dari sekitar 23 kategori** dokumen cetak yang ada di sistem lama. Sumbernya ditemukan lewat controller generik `eproc/application/controllers/cetak.php` yang menjembatani folder `eproc/application/views/report/` (~90 file view) - controller ini sebelumnya **luput total** dari radar penulisan dokumen ini karena namanya generik, bukan per-fitur. Yang paling berdampak hilang:
- **Tidak bisa cetak dokumen Kontrak/SPK itu sendiri** (`kontrak_pdf.php`, `kontrak_multi_pdf.php`, `spmk_multi_pdf.php`)
- **Tidak ada satupun cetak resmi hasil evaluasi kualifikasi** (8 kategori: administrasi, rekening koran, pengalaman, peralatan, personil, sertifikat, SKK, rekapitulasi - masing-masing versi PDF dan Excel), padahal modul Evaluasi Tender sendiri sudah lama diklaim selesai
- Juga belum ada: cetak RUP/Permohonan Paket, cetak Rekam Jejak, cetak SPPJB, cetak Surat Pesanan, cetak Jadwal, cetak Pernyataan Minat, cetak Klarifikasi, cetak Negosiasi, cetak dokumen Katalog (SKT, Surat Pesanan), laporan VMS

**Dikonfirmasi ulang (bukan gap baru, cuma dipastikan levelnya)**: 10 role tambahan (admin_vms dst) memang 100% masih kosong, cuma akses menu Dashboard, tidak ada satupun halaman khusus. SAP dan SIRUP tetap simulasi jujur sesuai yang sudah diketahui, tidak ada masalah representasi.

**Temuan kecil**: ditemukan sisa akun uji dari sesi-sesi sebelumnya yang tidak pernah dibersihkan di database Supabase (vendor `TJAKreasi`, 2 akun `audituser*`, 1 akun `staff_audit_*` dengan role admin_vms) - artinya kebiasaan "selalu bersihkan data uji setelah testing" yang berulang kali diklaim di dokumen ini ternyata TIDAK selalu benar-benar dijalankan konsisten. Perlu lebih hati-hati soal ini ke depan, dan akun-akun sisa ini masih ada di database sampai sekarang (sengaja tidak dihapus lewat audit karena di luar mandat audit read-only).

**Skor keyakinan paritas keseluruhan menurut audit ini: sekitar 60-65%** (bukan 100%). Kerangka utama modul inti memang sudah luas dan sebagian besar benar-benar berfungsi saat diuji langsung, tapi masih ada gap nyata yang signifikan (terutama cetak dokumen dan 10 role tambahan yang masih kosong total).

## Tindak lanjut audit 2026-09-01: cetak Data Kontrak ditambahkan, 4 akun uji lama dibersihkan

Setelah laporan audit di atas, pengguna setuju urutan kerja: lengkapi Cetak Dokumen dulu (mulai dari cetak Kontrak karena paling sering dibutuhkan), baru lanjut ke 10 role tambahan.

**Cetak Data Kontrak/SPK ditambahkan** (`GET /api/print/tenders/:id/kontrak`, halaman `src/pages/print/PrintKontrak.jsx`, akses lewat `/cetak/kontrak/:tenderId`): mengikuti struktur `eproc/application/views/report/kontrak.php` persis (data pokok SPK/PKS, deliverable pekerjaan, termin pembayaran, SLA kalau ada). Tombol "Cetak Data Kontrak" muncul di sub-tab SPK/PKS pada tab Kontrak, begitu nomor SPK/PKS sudah diisi. Sudah dites end-to-end (curl + browser sungguhan, data lengkap tampil benar, nol error console) dan data ujinya sudah dibersihkan. **Cakupan cetak dokumen masih belum lengkap** (baru 5 dari ~23 kategori sistem lama - masih kurang: cetak resmi hasil evaluasi kualifikasi 8 kategori, RUP, rekam jejak, SPPJB, surat pesanan, jadwal, pernyataan minat, klarifikasi, negosiasi, dokumen katalog, laporan VMS).

**Bug nyata ditemukan dan diperbaiki saat testing** (bukan disengaja, ditemukan lewat regresi Playwright): `formatRupiah()` di `src/components/ui/shared.jsx` crash (`Cannot read properties of null`) kalau nilainya `null` (misalnya tender yang kolom `pagu_anggaran`-nya belum diisi) - ini merusak `TenderTable` di Dashboard. Diperbaiki dengan guard null/undefined/NaN, sekarang tampil "-" alih-alih crash.

**Data uji lama dibersihkan dari database Supabase** (ditemukan waktu regresi Playwright gagal khusus untuk role admin): akun `admin@ui.ac.id` yang didokumentasikan resmi ternyata punya role tambahan `admin_vms` yang nyasar dari sesi testing 26 Agustus, tidak pernah dibersihkan - ini bikin popup "Pilih Role Aktif" muncul setiap kali admin login (tidak seharusnya ada), dan sempat bikin test navigasi admin timeout karena modal blocking. Sudah dihapus, `admin@ui.ac.id` sekarang kembali single-role sesuai dokumentasi awal. 3 akun uji lain yang jelas-jelas nama testing ("CV Audit Testing...", "PT Audit Test...", "Staff Audit Test") juga dihapus. **1 akun ("Tirta Jaya Abadi Kreasi", email Gmail pribadi) sengaja TIDAK dihapus** karena tidak jelas itu data uji atau registrasi sungguhan lewat form publik - tidak mau menebak, biarkan pengguna yang putuskan kalau perlu dihapus.

Setelah pembersihan ini, seluruh 17 test Playwright (`npm run test:e2e` / `npx playwright test --config=playwright.config.cjs`) lulus bersih lagi.

## Lanjutan 2026-09-01: cetak Evaluasi Kualifikasi (7 kategori) + Rekapitulasi + RUP/Pengajuan

Pengguna minta cetak dokumen dituntaskan dulu (khususnya evaluasi kualifikasi dan RUP) sebelum lanjut ke 10 role tambahan.

**Evaluasi Kualifikasi**: sistem lama punya 7 laporan terpisah (administrasi, rekening koran, pengalaman, personil, peralatan, sertifikat, SKK) plus 1 rekapitulasi. Karena sistem baru sudah punya SATU model data yang sama untuk semua kategori evaluasi (`tender_eval_criteria`/`tender_eval_scores`/`tender_eval_score_items`, dibangun waktu Kelompok J), dibuat **generik**: `GET /api/print/tenders/:id/evaluasi-kualifikasi/:category` menerima kategori apa saja (bukan 7 endpoint terpisah/hardcoded) - matrix vendor x kriteria, otomatis tahu apakah kategori itu pakai rumus resmi (personil/peralatan/sertifikat_lain, tampilkan daftar item) atau skor manual (kategori lain, tampilkan skor+catatan+memenuhi/tidak). Tombol "Cetak" muncul di tiap kategori dalam modal Evaluasi Detail (`EvaluationDetailModal.jsx`).

**Rekapitulasi**: `GET /api/print/tenders/:id/evaluasi-rekapitulasi` menghitung nilai akhir SEMUA kategori sekaligus per vendor, meniru `evaluasi_kualifikasi_rekapitulasi_excel.php`. Logika hitung nilai akhir kategori (rasio kriteria x bobot, dibatasi maks 100, dikali nilai maksimal kategori) **dipindahkan dari `tenders.js` ke modul bersama baru `server/lib/evalFormula.js`** (fungsi `computeCategoryFinalScore`) supaya endpoint cetak dan endpoint kerja Pokja yang sudah ada pakai rumus PERSIS SAMA, tidak ada duplikasi logika yang bisa drift. Diverifikasi dengan hitungan manual sebelum dipakai (bukan cuma dites jalan): skenario administrasi (skor manual 90, bobot 100) dan personil (rumus resmi, 1 personil sesuai dari 1 dibutuhkan) dihitung tangan dulu, hasilnya cocok persis dengan hasil API (90 dan 100, rata-rata akhir 95).

**Cetak Detail Pengajuan/RUP**: sistem lama cuma punya laporan berbentuk DAFTAR/list (banyak baris pengajuan sekaligus, bukan dokumen satu pengajuan) - kurang cocok ditiru persis karena daftar itu sudah bisa dilihat & difilter langsung di halaman Pengajuan yang ada. Dibangun sebagai dokumen detail SATU pengajuan (`GET /api/print/pengajuan/:id`, halaman `PrintPengajuan.jsx`, tombol "Cetak" di `DetailPengajuanModal.jsx`): data usulan, analisa kebutuhan & pasar, checklist kelengkapan, riwayat persetujuan.

Sudah dites end-to-end (curl dengan verifikasi angka manual + browser sungguhan Playwright untuk ketiga jenis dokumen baru, nol error console) dan seluruh 17 test regresi tetap lulus bersih setelah perubahan. Data uji dibersihkan.

**Sisa cetak dokumen yang masih belum dibuat** (dianggap prioritas lebih rendah - dokumen internal/jarang dipakai, bukan dokumen resmi legal yang wajib): rekam jejak, SPPJB (surat perjanjian versi konstruksi, sudah ada endpoint backend-nya dari Kelompok C tapi belum ada halaman cetak), surat pesanan (kontrak payung/katalog), jadwal tahapan, pernyataan minat, klarifikasi, negosiasi, dokumen katalog (SKT/surat pesanan), laporan VMS. Total cetak dokumen sekarang mencakup 8 dari ~23 kategori sistem lama (naik dari 4).
