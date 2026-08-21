-- Migrasi 024: Kelompok I - Konten Publik (Banner & Kebijakan)
--
-- Berdasarkan riset ke eproc/application/controllers/banner_json.php dan models/banner.php,
-- serta pengecekan tabel kebijakan di eproc_migrasi.sql.
--
-- BANNER: sistem lama ternyata sangat minim (cuma nama+gambar+tanggal input, TIDAK ADA link
-- URL saat diklik, TIDAK ADA urutan tampil manual, TIDAK ADA status aktif/nonaktif - semua
-- baris di tabel otomatis tampil, urutan ikut ORDER BY tanggal input DESC). Sesuai arahan
-- pengguna, ditiru dasarnya TAPI ditambah 2 field praktis yang tidak ada di sistem lama:
-- link_url (opsional, tujuan saat banner diklik) dan is_active (toggle tanpa hapus permanen).
-- Kolom "lampiran" di tabel asli TIDAK ditiru karena terkonfirmasi dead column (tidak pernah
-- diisi/dipakai di kode PHP manapun).
--
-- KEBIJAKAN: terkonfirmasi KODE MATI TOTAL di sistem lama (model ada tapi cuma selectByParams,
-- tidak ada insert/update/delete; tidak ada controller; data kosong). TAPI tabelnya memang ada
-- di skema asli (title, text, jenis, created_by, created_date), jadi bukan sekadar tebakan -
-- sesuai arahan pengguna, dibangun sebagai fitur baru minimal mengikuti struktur kolom yang
-- sudah disiapkan itu. Kolom "jenis" dipertahankan untuk dukung multi-halaman kebijakan
-- (misal "Kebijakan Privasi", "Syarat & Ketentuan", dst), bukan cuma 1 halaman tunggal.
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS).

CREATE TABLE IF NOT EXISTS cms_banners (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  nama varchar(255) NOT NULL,
  gambar_path varchar(500) NOT NULL,
  link_url varchar(500),
  is_active boolean NOT NULL DEFAULT true,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cms_policies (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  title varchar(255) NOT NULL,
  content text NOT NULL,
  jenis varchar(100) NOT NULL DEFAULT 'umum',
  is_published boolean NOT NULL DEFAULT true,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
