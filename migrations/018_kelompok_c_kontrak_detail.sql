-- Migrasi 018: Kelompok C - Contracting/Kontrak Detail
--
-- Ini kelompok kerja paling besar sejauh ini. Sesuai metodologi yang sudah terbukti, semua fitur
-- di bawah ditelusuri dari controller PHP asli yang aktif dipakai:
-- - eproc/application/controllers/contracting_json.php (4756 baris, ~65 fungsi - controller utama
--   yang benar-benar menyimpan data, dipanggil dari form eproc/application/views/kontrak/
--   contracting_detail.php)
-- - eproc/application/controllers/sppjb_json.php
-- - eproc/application/controllers/contracting_notifikasi_json.php
-- CATATAN METODOLOGI: eproc/application/controllers/contracting_rekanan_json.php (2716 baris) dan
-- contractingfile_json.php, contracting_penyedia_json.php, contracting_search_json.php SEMUANYA
-- terkonfirmasi READ-ONLY (cuma query listing DataTables, nol operasi insert/update), jadi TIDAK
-- dipakai sebagai acuan skema di migrasi ini - field-field yang mereka baca sudah tercakup dari
-- field yang ditulis oleh contracting_json.php.
--
-- Sistem lama mengorganisir kontrak lewat 4 tahap (field CONTRACTINGPROSESID di tabel asli):
-- 0/1/2 = Persiapan (SPPBJ -> SPMK -> SPK/PKS), 3 = Pengendalian, 4/5 = Penyelesaian (BAST), 6 = Selesai.
-- Sistem baru TIDAK meniru pemisahan 4 tahap ini secara rigid (kolom `status` yang sudah ada di
-- `contracts` sudah cukup untuk itu, ditambah kolom baru `stage` untuk kategorisasi kasar), tapi
-- SEMUA dokumen/data di tiap tahap tetap diikuti field-per-fieldnya persis dari kode asli.
--
-- Isi kelompok C:
-- 1. Perluasan tabel `contracts` (jadi "SPPBJ + SPK/PKS" dalam satu baris, field dari fungsi
--    addSppbj/addSPKPKS/addLegal/addBASTHasil/tunjuk_pic*/approveKontrak/penilaianPengguna)
-- 2. `contract_spmk` - Surat Perintah Mulai Kerja (dari addSpmk)
-- 3. `contract_jaminan` - Jaminan Pelaksanaan (dari addfilejaminanAll)
-- 4. `contract_jaminan_pemeliharaan` - Jaminan Pemeliharaan/garansi purna kontrak (dari addJampel)
-- 5. `contract_sla` - Service Level Agreement (dari addSLA), khusus kontrak jenis layanan
-- 6. `contract_materials` + `contract_surat_pesanan` + `contract_surat_pesanan_items` - material dan
--    surat pesanan untuk kontrak payung (dari addMaterial, addSuratPesanan)
-- 7. `contract_addendum` - perubahan kontrak dengan 2 tahap approval (dari addAddendum)
-- 8. `contract_notes` - catatan bebas internal/penyedia (dari addCatatan/addCatatanPenyedia)
-- 9. `contract_reminders` - notifikasi/pengingat (dari contracting_notifikasi_json)
-- 10. `contract_documents` - dokumen tambahan selain SPK/BAST (dari addfile/addfileMulti)
-- 11. `contract_status_changes` - kolom flag CR_PERUBAHAN/CR_KAHAR/CR_BERAKHIR/CR_PEMUTUSAN/
--     CR_KESEMPATAN/CR_DENDA (dari addPerubahanKontrak dkk, semuanya pola sama: flag+alasan+file)
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS / ADD COLUMN IF NOT EXISTS).

-- 1. PERLUASAN TABEL CONTRACTS
-- Field SPPBJ (dari addSppbj): kode, tanggal, direktur, alamat, jabatan, jaminan pelaksanaan.
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS sppbj_code varchar(100);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS sppbj_date date;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS sppbj_nilai numeric;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS sppbj_direktur_nama varchar(255);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS sppbj_direktur_jabatan varchar(255);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS sppbj_direktur_alamat text;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS sppbj_direktur_kota varchar(100);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS sppbj_pejabat_berwenang varchar(255);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS sppbj_pejabat_nip varchar(50);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS sppbj_pejabat_jabatan varchar(255);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS sppbj_pelaksanaan_dari date;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS sppbj_pelaksanaan_sampai date;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS sppbj_ppn numeric;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS sppbj_jaminan_pelaksana varchar(10);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS sppbj_jaminan_persen numeric;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS sppbj_jaminan_nilai numeric;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS sppbj_jaminan_jangka_dari date;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS sppbj_jaminan_jangka_sampai date;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS sppbj_jaminan_maksimal_penyerahan date;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS is_non_sppbj boolean NOT NULL DEFAULT false;
-- is_non_sppbj: eproc lama punya addSppbjNon() untuk kontrak yang lewati tahap SPPBJ formal
-- (langsung dari data pemenang), dipakai untuk paket kecil/pengadaan langsung.

-- Field SPK/PKS (dari addSPKPKS, versi lebih lengkap dari addSPK):
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS spk_code varchar(100);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS metode_pembayaran varchar(100);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS jenis_pengadaan varchar(50);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS jenis_pekerjaan varchar(50);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS jenis_kontrak varchar(100);
-- jenis_kontrak: teks bebas mengikuti CONTRACTINGJENISKONTRAKID, tidak dibuatkan tabel master
-- terpisah dulu karena nilainya cuma dipakai sebagai label, bisa diperluas jadi FK nanti kalau
-- memang dibutuhkan pengelolaan master jenis kontrak.
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS waktu_pelaksanaan_dari date;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS waktu_pelaksanaan_sampai date;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS pihak1_nama varchar(255);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS pihak1_jabatan varchar(255);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS pihak2_nama varchar(255);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS pihak2_jabatan varchar(255);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS lingkup_pekerjaan text;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS legal_nomor_pks varchar(100);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS legal_tanggal date;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS legal_nomor_rekanan varchar(100);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS purchase_order_number varchar(100);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS penyelesaian_kontrak_awal date;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS penyelesaian_kontrak_akhir date;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS masa_garansi integer;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS masa_garansi_periode varchar(20);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS nama_kegiatan varchar(255);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS dokumen_jenis varchar(20) DEFAULT 'spk';
-- dokumen_jenis: 'spk', 'surat_perjanjian' (mengikuti comboJenisContrack asli: '0'=SPK, '1'=Surat
-- Perjanjian). Kalau 'surat_perjanjian', field detail tambahannya ada di tabel contract_sppjb.

-- Field pemeriksa/approval (dari approveKontrak, approve_ppk, approve_manager):
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS approve_manager boolean;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS approve_ppk boolean;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS pemeriksa_nama varchar(255);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS pemeriksa_jabatan varchar(255);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS pemeriksa_approval boolean;

-- Field BAST Hasil Pekerjaan (dari addBASTHasil, prefix asli CR_BAST_PEKERJAAN_*):
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS bast_pekerjaan_nomor varchar(100);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS bast_pekerjaan_tanggal date;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS bast_pekerjaan_nama_penyedia varchar(255);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS bast_pekerjaan_jabatan_penyedia varchar(255);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS bast_pekerjaan_nama_penerima varchar(255);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS bast_pekerjaan_jabatan_penerima varchar(255);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS bast_pekerjaan_status varchar(30);
-- Field BAST Masa Pemeliharaan (dari addBASTPemeliharaan, prefix asli CR_BAST_MASA_*):
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS bast_masa_nomor varchar(100);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS bast_masa_tanggal date;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS bast_masa_nama_penyedia varchar(255);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS bast_masa_jabatan_penyedia varchar(255);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS bast_masa_nama_penerima varchar(255);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS bast_masa_jabatan_penerima varchar(255);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS bast_masa_status varchar(30);

-- Field PIC dan tahap (dari tunjuk_pic, tunjuk_pic_pengendali, tunjuk_pic_penyelesaian):
-- PIC = Person In Charge, satu kontrak eProc lama punya PIC berbeda per tahap (mengikuti alur
-- Persiapan->Pengendalian->Penyelesaian).
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS pic_persiapan_id uuid REFERENCES users(id);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS pic_pengendali_id uuid REFERENCES users(id);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS pic_penyelesai_id uuid REFERENCES users(id);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS pengawas_unit_kerja varchar(255);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS stage varchar(20) NOT NULL DEFAULT 'persiapan';
-- stage: 'persiapan' | 'pengendalian' | 'penyelesaian' | 'selesai' - kategorisasi kasar tahap
-- kontrak, dipakai untuk filter tampilan (mirip CONTRACTINGPROSESID di sistem lama tapi
-- disederhanakan jadi 4 nilai teks, bukan 7 kode angka 0-6 yang membingungkan).

-- Field penilaian kinerja penyedia (dari approvalPPK/approvalKasubdit/approvalPICUnit,
-- penilaianPengguna - 3 tahap approval independen untuk penilaian pasca-kontrak):
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS penilaian_grade varchar(5);
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS penilaian_total_skor numeric;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS penilaian_approval_ppk boolean;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS penilaian_approval_kasubdit boolean;
ALTER TABLE contracts ADD COLUMN IF NOT EXISTS penilaian_approval_unit boolean;

-- 2. SPMK (Surat Perintah Mulai Kerja) - dari addSpmk
CREATE TABLE IF NOT EXISTS contract_spmk (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  contract_id uuid NOT NULL REFERENCES contracts(id),
  nomor varchar(100),
  spmk_dari date,
  spmk_sampai date,
  keterangan text,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 3. JAMINAN PELAKSANAAN - dari addfilejaminanAll (jaminan + konfirmasi bank, 2 file terpisah)
CREATE TABLE IF NOT EXISTS contract_jaminan (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  contract_id uuid NOT NULL REFERENCES contracts(id),
  nomor varchar(100),
  tanggal_jaminan date,
  file_jaminan varchar(500),
  tanggal_konfirmasi_kebank date,
  tanggal_konfirmasi_oleh_bank date,
  status_konfirmasi varchar(30),
  file_konfirmasi varchar(500),
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 4. JAMINAN PEMELIHARAAN (Jampel) - dari addJampel, garansi purna kontrak
CREATE TABLE IF NOT EXISTS contract_jaminan_pemeliharaan (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  contract_id uuid NOT NULL REFERENCES contracts(id),
  nomor varchar(100),
  nilai numeric,
  masa integer,
  tanggal_mulai date,
  tanggal_akhir date,
  file_jaminan varchar(500),
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 5. SLA (Service Level Agreement) - dari addSLA, khusus kontrak jenis layanan/maintenance
CREATE TABLE IF NOT EXISTS contract_sla (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  contract_id uuid NOT NULL REFERENCES contracts(id),
  availability varchar(50),
  waktu varchar(100),
  denda varchar(100),
  biaya_maintenance numeric,
  nilai_denda numeric,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 6. MATERIAL & SURAT PESANAN (khusus Kontrak Payung, dari addMaterial/addSuratPesanan)
-- Material = daftar barang/jasa yang tersedia dalam kontrak payung (pagu per item).
CREATE TABLE IF NOT EXISTS contract_materials (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  contract_id uuid NOT NULL REFERENCES contracts(id),
  nama varchar(255) NOT NULL,
  qty numeric,
  satuan varchar(50),
  harga_satuan numeric,
  sifat varchar(20),
  keterangan text,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
-- Surat Pesanan = dokumen turunan dari kontrak payung untuk memesan sebagian material.
CREATE TABLE IF NOT EXISTS contract_surat_pesanan (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  contract_id uuid NOT NULL REFERENCES contracts(id),
  nomor_surat varchar(100),
  tanggal date,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS contract_surat_pesanan_items (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  surat_pesanan_id uuid NOT NULL REFERENCES contract_surat_pesanan(id) ON DELETE CASCADE,
  material_id uuid REFERENCES contract_materials(id),
  nama varchar(255) NOT NULL,
  harga_satuan numeric,
  qty numeric,
  satuan varchar(50),
  sifat varchar(20),
  total numeric,
  keterangan text,
  status_terima varchar(30),
  status_keterangan text,
  tanggal_terima date,
  presentase numeric,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 7. ADDENDUM - dari addAddendum, butuh 2 approval terpisah (Kasubdit dan Penyedia)
CREATE TABLE IF NOT EXISTS contract_addendum (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  contract_id uuid NOT NULL REFERENCES contracts(id),
  nomor varchar(100),
  addendum_ke integer,
  jenis varchar(255),
  tanggal date,
  tanggal_kontrak_dari date,
  tanggal_kontrak_sampai date,
  tanggal_penyelesaian_awal date,
  tanggal_penyelesaian_akhir date,
  file_persetujuan varchar(500),
  file_addendum varchar(500),
  keterangan text,
  nilai_baru numeric,
  approved_kasubdit boolean,
  approved_penyedia boolean,
  status varchar(20) NOT NULL DEFAULT 'draft',
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 8. CATATAN (bebas teks, internal atau versi penyedia) - dari addCatatan/addCatatanPenyedia
CREATE TABLE IF NOT EXISTS contract_notes (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  contract_id uuid NOT NULL REFERENCES contracts(id),
  jenis varchar(20) NOT NULL DEFAULT 'internal', -- 'internal' atau 'penyedia'
  pesan text NOT NULL,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 9. NOTIFIKASI/PENGINGAT - dari contracting_notifikasi_json (mengikuti field asli persis,
-- termasuk bahwa TANGGAL_NOTIFIKASI_SAMPAI di kode asli di-comment-out/tidak pernah kesimpan)
CREATE TABLE IF NOT EXISTS contract_reminders (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  contract_id uuid NOT NULL REFERENCES contracts(id),
  judul varchar(255) NOT NULL,
  tanggal_dari date,
  tanggal_sampai date,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 10. DOKUMEN TAMBAHAN (selain SPK/BAST yang sudah ada di kolom spk_path/bast_path) - dari addfile
CREATE TABLE IF NOT EXISTS contract_documents (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  contract_id uuid NOT NULL REFERENCES contracts(id),
  nama varchar(255) NOT NULL,
  file_path varchar(500) NOT NULL,
  file_size integer,
  jenis varchar(50),
  keterangan text,
  publish_ke_penyedia boolean NOT NULL DEFAULT false,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 11. PERUBAHAN STATUS KONTRAK (Perubahan/Penyesuaian/Kahar/Berakhir/Pemutusan/Kesempatan/Denda)
-- Semua fungsi addPerubahanKontrak dkk di kode asli punya pola identik: flag + alasan + file
-- opsional, jadi digabung jadi satu tabel dengan kolom "jenis" alih-alih 7 tabel terpisah.
CREATE TABLE IF NOT EXISTS contract_status_changes (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  contract_id uuid NOT NULL REFERENCES contracts(id),
  jenis varchar(20) NOT NULL,
  -- jenis: 'perubahan' | 'penyesuaian' | 'kahar' | 'berakhir' | 'pemutusan' | 'kesempatan' | 'denda'
  alasan text,
  file_path varchar(500),
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 12. SPPJB (Surat Perjanjian) - varian dokumen kontrak, dari sppjb_json.php
-- Field mengikuti field asli PERSIS termasuk sinyal bug (di kode PHP asli, mode update salah
-- pakai key field SPPBJ_ID padahal primary key aslinya SPPJB_ID - di skema baru ini dipakai
-- 1 primary key konsisten "id" seperti semua tabel lain, jadi bug penamaan itu otomatis tidak
-- relevan lagi di sistem baru, tapi dicatat di sini sebagai jejak sejarah).
CREATE TABLE IF NOT EXISTS contract_sppjb (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  contract_id uuid NOT NULL REFERENCES contracts(id),
  kode varchar(100),
  tanggal date,
  nama_dirut varchar(255),
  alamat_dirut text,
  kota_dirut varchar(100),
  ppn numeric,
  persen_jaminan numeric,
  tmt_jaminan date,
  jangka_waktu varchar(100),
  jangka_waktu_jaminan varchar(100),
  penanda_tangan varchar(255),
  penanda_tangan_jabatan varchar(255),
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
