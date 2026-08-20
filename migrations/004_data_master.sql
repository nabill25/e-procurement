-- Migrasi 004: Data Master
--
-- Menambahkan data referensi (dropdown) yang dipakai di berbagai form: bank, mata uang,
-- negara, satuan, incoterm, metode pembayaran. Mengikuti tabel-tabel terpisah di sistem
-- eProc lama (bank, mata_uang, negara, satuan, incoterm, payment_method), tapi digabung
-- jadi satu tabel "master_data" dengan kolom "category" karena bentuk datanya sama persis
-- (cuma kode + nama). Kolom "extra" (jsonb) dipakai untuk field tambahan yang cuma dipakai
-- kategori tertentu, misalnya "benua" untuk negara.
--
-- Unit Kerja dibuatkan tabel sendiri (unit_kerja) karena datanya lebih detail (alamat,
-- telepon, email), sekaligus meningkatkan users.unit_kerja dan procurement_requests.unit_kerja
-- yang sebelumnya cuma teks bebas menjadi punya sumber data resmi.
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS).

CREATE TABLE IF NOT EXISTS master_data (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  category varchar(30) NOT NULL,
  kode varchar(20),
  nama varchar(255) NOT NULL,
  extra jsonb,
  is_active boolean NOT NULL DEFAULT true,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS unit_kerja_master (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  kode varchar(20),
  nama varchar(150) NOT NULL,
  alamat text,
  telepon varchar(50),
  email varchar(100),
  is_active boolean NOT NULL DEFAULT true,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
