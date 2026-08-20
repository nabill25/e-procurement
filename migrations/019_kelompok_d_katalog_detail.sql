-- Migrasi 019: Kelompok D - Katalog/E-Purchasing Detail
--
-- Dugaan awal (dari roadmap) katalog cuma perlu "foto, kategori, riwayat harga" - ternyata SALAH,
-- setelah dibaca kode aslinya (katalog_json.php, ~1400 baris) ketahuan katalog adalah toko online
-- mini yang terhubung ke pengajuan pengadaan (bukan sekadar galeri produk):
-- browse produk -> masukkan keranjang (terikat ke satu paket/pengajuan) -> negosiasi harga dengan
-- vendor -> checkout dengan ongkos kirim -> alur status pesanan 6 tahap -> nomor invoice otomatis.
--
-- Semua controller sudah dicek aktif dipanggil dari eproc/application/views/main/*.php:
-- katalog_json.php (utama), katalog_foto_json.php, katalog_kategori_json.php,
-- katalog_lampiran_json.php, katalog_laporan_json.php.
-- katalog_offline_json.php dan katalog_pemerintah_json.php TERNYATA BUKAN fitur katalog produk -
-- itu upload dokumen untuk metode pengadaan "Pembelian Offline"/"Pembelian Pemerintah" pada modul
-- Purchasing yang sudah ada (operasi ke tabel Paket/Purchasingfile, bukan Katalog), jadi TIDAK
-- relevan untuk migrasi ini. katalog_validasi_json.php cuma grid read-only jumlah katalog per
-- vendor untuk admin, tidak ada logika "validasi" khusus, tidak butuh tabel baru.
--
-- Isi kelompok D:
-- 1. Perluasan tabel katalog_items - field produk lengkap dari fungsi add() (dimensi, TKDN,
--    garansi, stok, kemasan, status, keterangan tambahan)
-- 2. katalog_photos - banyak foto per produk (dari katalog_foto_json)
-- 3. katalog_attachments - lampiran dokumen produk (dari katalog_lampiran_json)
-- 4. katalog_categories - kategori berjenjang (pola sama seperti bidang_usaha)
-- 5. katalog_item_categories - many-to-many produk <-> kategori (dari KatalogKategoriRekanan)
-- 6. katalog_price_history - riwayat harga, auto-tercatat cuma kalau harga BERUBAH (meniru
--    persis logika di fungsi add(): if (reqHarga == reqHargaold) maka TIDAK dicatat)
-- 7. katalog_reports - laporan/komplain produk dari pengunjung publik (dari addLaporan)
-- 8. katalog_compare - daftar produk yang dibandingkan per sesi browser (dari compare())
-- 9. katalog_cart_items - keranjang belanja per paket/pengajuan (dari cart/cartupdate), dengan
--    status alur pesanan 6 tahap dan invoice otomatis (dari statusupdate/generateInvoice)
-- 10. katalog_logistik - ongkos kirim per paket (dari Kataloglogistik)
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS).

-- 1. PERLUASAN TABEL KATALOG_ITEMS
ALTER TABLE katalog_items ADD COLUMN IF NOT EXISTS item_code varchar(100);
ALTER TABLE katalog_items ADD COLUMN IF NOT EXISTS brand varchar(255);
ALTER TABLE katalog_items ADD COLUMN IF NOT EXISTS model_type varchar(255);
ALTER TABLE katalog_items ADD COLUMN IF NOT EXISTS diameter numeric;
ALTER TABLE katalog_items ADD COLUMN IF NOT EXISTS panjang numeric;
ALTER TABLE katalog_items ADD COLUMN IF NOT EXISTS lebar numeric;
ALTER TABLE katalog_items ADD COLUMN IF NOT EXISTS tinggi numeric;
ALTER TABLE katalog_items ADD COLUMN IF NOT EXISTS unit_pengukuran varchar(20);
ALTER TABLE katalog_items ADD COLUMN IF NOT EXISTS tkdn_persen numeric;
ALTER TABLE katalog_items ADD COLUMN IF NOT EXISTS berlaku_sampai date;
ALTER TABLE katalog_items ADD COLUMN IF NOT EXISTS jenis_produk varchar(100);
ALTER TABLE katalog_items ADD COLUMN IF NOT EXISTS lama_garansi integer;
ALTER TABLE katalog_items ADD COLUMN IF NOT EXISTS lama_garansi_satuan varchar(20);
ALTER TABLE katalog_items ADD COLUMN IF NOT EXISTS jumlah_stock integer DEFAULT 0;
ALTER TABLE katalog_items ADD COLUMN IF NOT EXISTS jumlah_stock_ready varchar(50);
ALTER TABLE katalog_items ADD COLUMN IF NOT EXISTS kemasan varchar(100);
ALTER TABLE katalog_items ADD COLUMN IF NOT EXISTS status varchar(20) NOT NULL DEFAULT 'aktif';
ALTER TABLE katalog_items ADD COLUMN IF NOT EXISTS keterangan_tambahan text;

-- 2. FOTO PRODUK (banyak foto per produk)
CREATE TABLE IF NOT EXISTS katalog_photos (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  katalog_id uuid NOT NULL REFERENCES katalog_items(id) ON DELETE CASCADE,
  file_path varchar(500) NOT NULL,
  file_size integer,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 3. LAMPIRAN PRODUK (dokumen pendukung: brosur, spesifikasi, dst)
CREATE TABLE IF NOT EXISTS katalog_attachments (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  katalog_id uuid NOT NULL REFERENCES katalog_items(id) ON DELETE CASCADE,
  nama varchar(255),
  file_path varchar(500) NOT NULL,
  file_size integer,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 4. KATEGORI PRODUK (berjenjang, pola sama seperti bidang_usaha di kelompok B)
CREATE TABLE IF NOT EXISTS katalog_categories (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  parent_id uuid REFERENCES katalog_categories(id),
  kode varchar(30),
  nama varchar(255) NOT NULL,
  is_active boolean NOT NULL DEFAULT true,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_katalog_categories_parent ON katalog_categories(parent_id);

-- 5. KATEGORI PER PRODUK (many-to-many)
CREATE TABLE IF NOT EXISTS katalog_item_categories (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  katalog_id uuid NOT NULL REFERENCES katalog_items(id) ON DELETE CASCADE,
  category_id uuid NOT NULL REFERENCES katalog_categories(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(katalog_id, category_id)
);

-- 6. RIWAYAT HARGA (cuma tercatat kalau harga benar-benar berubah, meniru logika asli)
CREATE TABLE IF NOT EXISTS katalog_price_history (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  katalog_id uuid NOT NULL REFERENCES katalog_items(id) ON DELETE CASCADE,
  harga_lama numeric,
  harga_baru numeric,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 7. LAPORAN/KOMPLAIN PRODUK (dari pengunjung publik, tidak perlu login)
CREATE TABLE IF NOT EXISTS katalog_reports (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  katalog_id uuid NOT NULL REFERENCES katalog_items(id) ON DELETE CASCADE,
  nama varchar(255),
  email varchar(255),
  telepon varchar(50),
  alasan text NOT NULL,
  jenis_laporan varchar(100),
  status varchar(20) NOT NULL DEFAULT 'baru',
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 8. BANDINGKAN PRODUK (per sesi browser, maksimal 3 produk seperti validasi asli)
CREATE TABLE IF NOT EXISTS katalog_compare (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  katalog_id uuid NOT NULL REFERENCES katalog_items(id) ON DELETE CASCADE,
  session_id varchar(100) NOT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(katalog_id, session_id)
);

-- 9. LOGISTIK / ONGKOS KIRIM PER PENGAJUAN
CREATE TABLE IF NOT EXISTS katalog_logistik (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  procurement_request_id uuid NOT NULL REFERENCES procurement_requests(id),
  ongkos_kirim numeric NOT NULL DEFAULT 0,
  updated_by uuid REFERENCES users(id),
  updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(procurement_request_id)
);

-- 10. KERANJANG BELANJA (terikat ke satu pengajuan/permohonan paket, dengan alur status pesanan)
-- Status meniru persis nilai asli dari statusupdate() di katalog_json.php:
-- 0=Proses Pemilihan, 1=Negosiasi, 2=Penyedia Setuju, 3=Surat Pesanan, 4=Proses, 5=Dikirim, 6=Diterima
CREATE TABLE IF NOT EXISTS katalog_cart_items (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  procurement_request_id uuid NOT NULL REFERENCES procurement_requests(id),
  katalog_id uuid NOT NULL REFERENCES katalog_items(id),
  vendor_id uuid REFERENCES users(id),
  nama_produk varchar(255),
  merek varchar(255),
  model_type varchar(255),
  harga numeric NOT NULL,
  harga_nego numeric,
  qty integer NOT NULL DEFAULT 1,
  status integer NOT NULL DEFAULT 0,
  status_awal integer,
  no_invoice varchar(100),
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp
);
CREATE INDEX IF NOT EXISTS idx_katalog_cart_procurement ON katalog_cart_items(procurement_request_id);
