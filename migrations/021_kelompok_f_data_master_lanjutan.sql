-- Migrasi 021: Kelompok F - Data Master Lanjutan
--
-- Sisa kategori Data Master dari roadmap CLAUDE.md: ijin_usaha, dokumen_template, pendidikan,
-- region (wilayah), tanggal_merah, master_pengaturan. `akta_type` DIKELUARKAN dari daftar karena
-- setelah dicek, controllernya (akta_type_json.php) TERNYATA TIDAK PERNAH dipanggil dari
-- manapun (bukan cuma views/main, sudah dicek seluruh folder views/) - kode mati, bukan fitur
-- aktif. `komoditas` dan `kurs` juga TIDAK ADA controllernya sama sekali di sistem lama (dicek
-- lewat ls langsung ke folder controllers), jadi kemungkinan besar tabel yang tidak pernah
-- benar-benar dipakai, tidak dibuatkan migrasi.
--
-- Semua controller yang jadi acuan sudah dicek aktif dipanggil dari eproc/application/views/main/:
-- ijin_usaha_json.php, dokumen_template_json.php, dokumen_template_rekanan_json.php,
-- pendidikan_json.php, region_json.php, master_tanggal_json.php, master_pengaturan_json.php,
-- master_dokumen_template_upload.php.
--
-- CATATAN PENTING soal data wilayah Indonesia: tabel sumber aslinya (INDOWILAYAH2023) TIDAK ADA
-- di eproc_migrasi.sql (dicek langsung, tidak ketemu satupun baris "CREATE TABLE
-- public.indowilayah2023" atau sejenisnya) - beda dengan bidang usaha KBLI kemarin yang datanya
-- lengkap tersedia. Karena ini data alamat administratif resmi yang butuh akurasi, TIDAK dikarang
-- sendiri. Sesuai arahan pengguna: kerangka tabel dibuat, cuma diisi 38 nama provinsi resmi
-- Indonesia (data publik yang stabil), sedangkan kabupaten/kota/kecamatan/kelurahan dikosongkan
-- dulu sampai ada sumber data resmi yang bisa diimpor.
--
-- Isi kelompok F:
-- 1. document_templates - gabungan dokumen_template + dokumen_template_rekanan (field TARGET
--    membedakan internal/rekanan) + master_dokumen_template/master_dokumen_template_upload
--    (konsepnya sama: file template yang bisa diunduh, jadi digabung jadi satu tabel alih-alih
--    3 tabel terpisah yang tumpang tindih).
-- 2. holidays - hari libur/tanggal merah (dari master_tanggal_json, replace-all pattern).
-- 3. app_settings - pengaturan sistem sederhana (dari master_pengaturan_json, cuma 1 baris
--    on/off + keterangan, dipakai untuk notifikasi dokumen akan kedaluwarsa).
-- 4. regions - wilayah administratif berjenjang (provinsi/kab-kota/kecamatan/kelurahan),
--    kerangka tabel dibuat, HANYA diisi 38 provinsi resmi, level di bawahnya kosong.
-- 5. Kategori baru di master_data yang sudah ada: 'ijin_usaha', 'pendidikan' (keduanya tabel
--    referensi sederhana id+nama, cukup reuse master_data, tidak perlu tabel baru).
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS).

-- 1. TEMPLATE DOKUMEN (gabungan 3 sistem template yang tumpang tindih di sistem lama)
CREATE TABLE IF NOT EXISTS document_templates (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  target varchar(20) NOT NULL DEFAULT 'internal', -- 'internal' atau 'rekanan'
  nama varchar(255) NOT NULL,
  keterangan text,
  file_path varchar(500),
  file_size numeric,
  file_type varchar(100),
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 2. HARI LIBUR / TANGGAL MERAH
CREATE TABLE IF NOT EXISTS holidays (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  tanggal date NOT NULL,
  keterangan varchar(255),
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 3. PENGATURAN SISTEM (dipakai untuk notifikasi dokumen akan kedaluwarsa)
CREATE TABLE IF NOT EXISTS app_settings (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  kunci varchar(100) NOT NULL UNIQUE,
  url varchar(255),
  aktif boolean NOT NULL DEFAULT false,
  keterangan text,
  updated_by uuid REFERENCES users(id),
  updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO app_settings (kunci, aktif, keterangan)
VALUES ('notifikasi_dokumen_expired', false, 'Notifikasi otomatis untuk dokumen vendor yang akan kedaluwarsa')
ON CONFLICT (kunci) DO NOTHING;

-- 4. WILAYAH ADMINISTRATIF (berjenjang: provinsi -> kab/kota -> kecamatan -> kelurahan)
CREATE TABLE IF NOT EXISTS regions (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  parent_id uuid REFERENCES regions(id),
  level varchar(20) NOT NULL, -- 'provinsi' | 'kabkota' | 'kecamatan' | 'kelurahan'
  nama varchar(255) NOT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_regions_parent ON regions(parent_id);
CREATE INDEX IF NOT EXISTS idx_regions_level ON regions(level);

INSERT INTO regions (level, nama)
SELECT 'provinsi', p FROM unnest(ARRAY[
  'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Kepulauan Riau', 'Jambi',
  'Sumatera Selatan', 'Kepulauan Bangka Belitung', 'Bengkulu', 'Lampung', 'DKI Jakarta', 'Jawa Barat',
  'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur', 'Banten', 'Bali', 'Nusa Tenggara Barat',
  'Nusa Tenggara Timur', 'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara',
  'Sulawesi Utara', 'Gorontalo', 'Sulawesi Tengah', 'Sulawesi Barat', 'Sulawesi Selatan', 'Sulawesi Tenggara',
  'Maluku', 'Maluku Utara', 'Papua', 'Papua Barat', 'Papua Barat Daya', 'Papua Tengah',
  'Papua Pegunungan', 'Papua Selatan'
]) AS p
WHERE NOT EXISTS (SELECT 1 FROM regions r WHERE r.level = 'provinsi' AND r.nama = p);
