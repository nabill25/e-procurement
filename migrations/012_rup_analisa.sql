-- Migrasi 012: RUP/Permohonan Paket - Analisa Kebutuhan & Pasar
--
-- Mengikuti tabel analisa_kebutuhan, analisa_pasar, dan permohonan_paket_analisa di sistem
-- eProc lama. Sistem lama menyimpan jenis analisa kebutuhan/pasar sebagai tabel referensi
-- terpisah (isinya cuma daftar nama) - jadi ditambahkan sebagai kategori baru di tabel
-- master_data yang sudah ada (migrations/004_data_master.sql), bukan bikin tabel referensi
-- baru. Field analisa yang sesungguhnya (komoditas, jenis analisa dipilih, identifikasi
-- risiko) ditambahkan langsung ke tabel procurement_requests karena sifatnya satu-ke-satu
-- per pengajuan, sama seperti field lain yang sudah ada di situ (technical_spec, quantity, dst).
--
-- Sengaja TIDAK termasuk "Jenis Belanja", "Kategori Permohonan" (permohonan_paket_analisa_
-- jenis_belanja/kategori), "Matrix Status" (permohonan_paket_analisa_matrix, ini tabel
-- konfigurasi alur kerja, bukan data analisa), dan "Checklist" (permohonan_paket_checklist,
-- butuh tabel master_checklist yang belum ada) - di luar cakupan "analisa kebutuhan & pasar"
-- yang diminta.
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS / WHERE NOT EXISTS).

ALTER TABLE procurement_requests ADD COLUMN IF NOT EXISTS komoditas varchar(150);
ALTER TABLE procurement_requests ADD COLUMN IF NOT EXISTS analisa_kebutuhan varchar(150);
ALTER TABLE procurement_requests ADD COLUMN IF NOT EXISTS analisa_pasar varchar(150);
ALTER TABLE procurement_requests ADD COLUMN IF NOT EXISTS risiko_teridentifikasi boolean NOT NULL DEFAULT false;
ALTER TABLE procurement_requests ADD COLUMN IF NOT EXISTS risiko_keterangan text;

-- Data awal kategori "analisa_kebutuhan" dan "analisa_pasar" di Data Master, mengikuti
-- istilah umum yang dipakai dalam analisa kebutuhan & pasar pengadaan pemerintah.
-- Admin bisa tambah/ubah/hapus lagi lewat halaman Data Master.
INSERT INTO master_data (category, nama)
SELECT 'analisa_kebutuhan', v.nama FROM (VALUES
  ('Kebutuhan Rutin/Operasional'),
  ('Kebutuhan Pengembangan/Investasi'),
  ('Kebutuhan Mendesak/Darurat')
) AS v(nama)
WHERE NOT EXISTS (SELECT 1 FROM master_data m WHERE m.category = 'analisa_kebutuhan' AND m.nama = v.nama);

INSERT INTO master_data (category, nama)
SELECT 'analisa_pasar', v.nama FROM (VALUES
  ('Tersedia Banyak Penyedia (Pasar Kompetitif)'),
  ('Penyedia Terbatas (Pasar Oligopoli)'),
  ('Penyedia Tunggal (Monopoli/Spesifik)')
) AS v(nama)
WHERE NOT EXISTS (SELECT 1 FROM master_data m WHERE m.category = 'analisa_pasar' AND m.nama = v.nama);
