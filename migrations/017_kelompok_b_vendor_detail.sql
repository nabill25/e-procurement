-- Migrasi 017: Kelompok B - Vendor/Rekanan Detail
--
-- Sesuai metodologi yang sudah terbukti (cek controller PHP aktif dulu, bukan cuma nama tabel),
-- dari daftar awal di CLAUDE.md ternyata beberapa item ("rekanan_sertifikat_jenis", "rekanan_checklist",
-- "rekanan_pakta_integritas", "rekanan_url_validasi") TIDAK ADA controllernya sama sekali di sistem
-- lama - itu cuma tebakan dari nama tabel yang salah. Sebaliknya, ditemukan 2 fitur nyata yang
-- sebelumnya tidak masuk daftar: "sertifikat_json.php" (master jenis sertifikat, dipanggil dari
-- master_sertifikat_jenis.php) dan "vendor_retail_json.php" (kategori vendor retail terpisah,
-- dipanggil dari master_vendor_retail.php). Semua controller di bawah sudah dicek aktif dipanggil
-- dari eproc/application/views/main/*.php (bukan folder backup).
--
-- Isi kelompok B:
-- 1. Bidang Usaha (bidang_usaha_json.php) - klasifikasi bidang usaha berjenjang (pohon), dipakai
--    sebagai syarat kualifikasi vendor untuk ikut tender tertentu.
-- 2. Bidang Usaha per Vendor (rekanan_bidang_usaha, lewat halaman registrasi/profil) - bidang usaha
--    mana saja yang dimiliki satu vendor.
-- 3. Rekening Koran (rekanan_rekening_koran_json.php) - bukti mutasi rekening bank per bulan,
--    dipakai sebagai syarat kualifikasi keuangan (beda dari data "bank" yang sudah ada di kolom
--    jsonb vendors.bank, itu cuma nomor rekening saja, ini bukti mutasinya).
-- 4. Tipe Vendor & Jenis Sertifikat master (rekanan_tipe_json.php, sertifikat_json.php) - data
--    referensi sederhana, disatukan ke tabel master_data yang sudah ada (kategori baru:
--    'rekanan_tipe' dan 'sertifikat_jenis').
-- 5. Vendor Retail (vendor_retail_json.php) - kategori vendor retail/katalog, punya data kontak
--    sendiri terpisah dari tabel vendors utama (di sistem lama ini memang tabel & alur terpisah,
--    dipakai untuk vendor yang hanya jualan produk katalog, bukan ikut tender).
-- 6. Rincian Penawaran / BOQ (rekanan_paket_penawaran_json.php) - item per baris pada penawaran
--    vendor (nama item, quantity, harga satuan, jumlah), bukan cuma satu angka total seperti
--    tender_participants.bid_price yang sudah ada. Dipakai untuk tender yang butuh rincian harga
--    per item, bukan cuma harga borongan.
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS).

-- 1. BIDANG USAHA (klasifikasi berjenjang / pohon, lewat bidang_usaha_parent_id)
CREATE TABLE IF NOT EXISTS bidang_usaha (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  parent_id uuid REFERENCES bidang_usaha(id),
  kode varchar(30),
  nama varchar(255) NOT NULL,
  keterangan text,
  is_active boolean NOT NULL DEFAULT true,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_bidang_usaha_parent ON bidang_usaha(parent_id);

-- 2. BIDANG USAHA PER VENDOR (many-to-many, satu vendor bisa punya banyak bidang usaha)
CREATE TABLE IF NOT EXISTS vendor_bidang_usaha (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  vendor_id uuid NOT NULL REFERENCES users(id),
  bidang_usaha_id uuid NOT NULL REFERENCES bidang_usaha(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(vendor_id, bidang_usaha_id)
);

-- Tender juga bisa mensyaratkan bidang usaha tertentu (paket_bidang_usaha di sistem lama).
CREATE TABLE IF NOT EXISTS tender_bidang_usaha (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  tender_id uuid NOT NULL REFERENCES tenders(id),
  bidang_usaha_id uuid NOT NULL REFERENCES bidang_usaha(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(tender_id, bidang_usaha_id)
);

-- 3. REKENING KORAN (bukti mutasi bank per bulan, syarat kualifikasi keuangan)
CREATE TABLE IF NOT EXISTS vendor_rekening_koran (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  vendor_id uuid NOT NULL REFERENCES users(id),
  nomor_rekening varchar(100),
  nama_bank varchar(100),
  bulan integer,
  tahun integer,
  nilai numeric,
  mata_uang varchar(10) DEFAULT 'IDR',
  file_path varchar(500),
  file_size integer,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_rekening_koran_vendor ON vendor_rekening_koran(vendor_id);

-- 5. VENDOR RETAIL (kategori vendor retail/katalog, alur & data kontak terpisah dari vendor pengadaan)
CREATE TABLE IF NOT EXISTS vendor_retail (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  tipe varchar(100),
  nama varchar(255) NOT NULL,
  npwp varchar(40),
  telepon_kode varchar(5),
  telepon varchar(20),
  whatsapp varchar(30),
  tanggal_daftar date,
  kota varchar(100),
  region varchar(100),
  kontak_person varchar(255),
  kontak_person_hp varchar(255),
  alamat text,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 6. RINCIAN PENAWARAN / BOQ (item per baris pada penawaran vendor untuk satu tender)
CREATE TABLE IF NOT EXISTS tender_bid_items (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  tender_participant_id uuid NOT NULL REFERENCES tender_participants(id) ON DELETE CASCADE,
  item_name varchar(255) NOT NULL,
  quantity numeric NOT NULL DEFAULT 0,
  unit_price numeric NOT NULL DEFAULT 0,
  subtotal numeric NOT NULL DEFAULT 0,
  delivery_date date,
  notes text,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_tender_bid_items_participant ON tender_bid_items(tender_participant_id);
