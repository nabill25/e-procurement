-- Migrasi 027: Jenis Tender, Metode (Lelang/Kualifikasi/Evaluasi), Direktorat
--
-- Item yang sengaja ditunda dari Kelompok A (paket_jenis, paket_metode_lelang,
-- paket_metode_kualifikasi, paket_metode_evaluasi) dan Kelompok F (direktorat), sekarang
-- dikerjakan. Berdasarkan riset ke eproc/application/controllers/paket_jenis_json.php,
-- models/paketmetodelelang.php, models/paketmetodekualifikasi.php,
-- models/paketmetodeevaluasi.php, models/direktorat.php - semuanya terkonfirmasi tabel
-- referensi SEDERHANA (id+nama, direktorat tambah kode+keterangan), cocok reuse master_data
-- generik yang sudah ada, tidak perlu tabel baru.
--
-- CATATAN PENTING soal cakupan: paket_metode_lelang/kualifikasi/evaluasi di sistem lama
-- sebenarnya DIPAKAI lewat query matriks berjenjang (model Metode, join ke tabel METODE dan
-- MATRIX_EVALUASI) untuk dropdown bertingkat saat membuat paket tender - itu TIDAK ditiru di
-- sini (sudah diputuskan dilewati sejak Kelompok A/F, terkait metode_tahap/metode_tahap_panel
-- yang jauh lebih kompleks, murni kalkulasi kalender & matriks, bukan data referensi). Yang
-- dibangun di sini CUMA daftar referensinya saja (untuk dikelola admin), bukan logika
-- dropdown bertingkat itu.
--
-- Item yang DIKELUARKAN dari daftar awal karena ternyata bukan data referensi sederhana:
-- paket_kriteria_eval (transaksional 1:1 per paket, bukan master data), evaluasi_jenis
-- (nama salah kutip, controllernya ternyata melayani AKTA_TYPE, bukan konsep terpisah).
--
-- Aman dijalankan berulang kali (pakai ON CONFLICT DO NOTHING, tidak ada constraint unique
-- eksplisit di master_data jadi dicek manual pakai WHERE NOT EXISTS).

INSERT INTO master_data (category, nama)
SELECT 'paket_jenis', n FROM unnest(ARRAY[
  'Barang', 'Pekerjaan Konstruksi', 'Jasa Konsultansi', 'Jasa Lainnya'
]) AS n
WHERE NOT EXISTS (SELECT 1 FROM master_data m WHERE m.category = 'paket_jenis' AND m.nama = n);

INSERT INTO master_data (category, nama)
SELECT 'metode_lelang', n FROM unnest(ARRAY[
  'Tender Umum', 'Tender Terbatas', 'Seleksi Umum', 'Penunjukan Langsung', 'Pengadaan Langsung', 'E-Purchasing'
]) AS n
WHERE NOT EXISTS (SELECT 1 FROM master_data m WHERE m.category = 'metode_lelang' AND m.nama = n);

INSERT INTO master_data (category, nama)
SELECT 'metode_kualifikasi', n FROM unnest(ARRAY[
  'Prakualifikasi', 'Pascakualifikasi'
]) AS n
WHERE NOT EXISTS (SELECT 1 FROM master_data m WHERE m.category = 'metode_kualifikasi' AND m.nama = n);

INSERT INTO master_data (category, nama)
SELECT 'metode_evaluasi', n FROM unnest(ARRAY[
  'Sistem Nilai', 'Sistem Harga Terendah', 'Sistem Kualitas dan Biaya', 'Sistem Kualitas', 'Sistem Pagu Anggaran'
]) AS n
WHERE NOT EXISTS (SELECT 1 FROM master_data m WHERE m.category = 'metode_evaluasi' AND m.nama = n);
