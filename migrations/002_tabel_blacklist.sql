-- Migrasi 002: Tabel Daftar Hitam (Blacklist)
--
-- Menambahkan tabel untuk modul Blacklist, mengikuti struktur data yang sama dengan sistem
-- eProc lama (tabel "blacklist" di eproc/eproc_migrasi.sql), disesuaikan dengan gaya penamaan
-- dan tipe kolom (uuid) yang dipakai tabel-tabel lain di sistem baru ini.
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS).

CREATE TABLE IF NOT EXISTS blacklist (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  vendor_id uuid REFERENCES users(id),
  company_name varchar(150) NOT NULL,
  npwp varchar(30),
  address text,
  city varchar(100),
  start_date date NOT NULL DEFAULT CURRENT_DATE,
  end_date date,
  sk_number varchar(50),
  sk_file_path varchar(255),
  reason text,
  status varchar(20) NOT NULL DEFAULT 'aktif',
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
