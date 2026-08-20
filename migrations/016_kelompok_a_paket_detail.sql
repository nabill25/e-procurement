-- Migrasi 016: Kelompok A - Detail Paket/Tender
--
-- Ini bagian pertama dari roadmap 100% paritas (kelompok A sampai K). Fokus kelompok A:
-- fitur-fitur detail di dalam satu paket/tender yang di sistem lama tersebar di banyak
-- controller (paket_dokumen_json, paket_panitia_json, sk_panitia_json, dst).
--
-- Semua fitur di bawah sudah dicek aktif dipakai (controllernya dipanggil dari halaman
-- di eproc/application/views/main/, bukan folder backup), lewat grep ke folder views/main.
--
-- Isi kelompok A:
-- 1. Dokumen tender (paket_dokumen) - upload dokumen resmi paket: dokumen lelang, dokumen
--    kualifikasi, dokumen aritmatika (BA koreksi aritmatika), laporan paket.
-- 2. Panitia/Pokja per paket (paket_panitia) - penugasan anggota panitia ke satu paket,
--    termasuk ketua, dan validasi pemenang oleh panitia (approve/reject pemenang).
-- 3. SK Panitia (sk_panitia + panitia) - SK (surat keputusan) pembentukan panitia per unit
--    kerja, dengan daftar anggota panitia di dalamnya. ini "master roster", terpisah dari
--    penugasan panitia ke paket tertentu (poin 2).
-- 4. Pernyataan minat (paket_pernyataan_minat) - surat kuasa/pernyataan minat rekanan saat
--    mendaftar ke suatu paket.
-- 5. Pakta integritas (paket_pakta_integritas) - validasi pakta integritas oleh rekanan
--    maupun panitia untuk satu paket.
-- 6. Pihak lain (paket_pihak_lain) - daftar user login lain yang diberi akses ke suatu paket
--    (misal auditor/pengawas tambahan).
-- 7. Pembukaan penawaran (paket_pembukaan_validasi + paket_pembukaan_kedua_validasi) - proses
--    validasi pembukaan sampul 1 dan sampul 2 (dua tahap, dipakai untuk lelang metode 2 sampul).
-- 8. Klarifikasi & tanggapan aanwijzing (klarifikasi_chat + paket_undangan_klarifikasi) -
--    dokumen klarifikasi dari rekanan, tanggapan aanwijzing dari panitia, dan undangan
--    klarifikasi resmi (email + jadwal pertemuan).
-- 9. Peringkat pemenang (paket_pemenang_peringkat) - daftar peringkat vendor pemenang dan
--    cadangan untuk satu paket (beda dari tender_participants.is_winner yang cuma tandai
--    1 pemenang, ini bisa simpan urutan peringkat 1/2/3 dst termasuk cadangan).
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS).

-- 1. DOKUMEN TENDER
-- Dokumen resmi yang diupload panitia/PPK untuk suatu paket (bukan dokumen penawaran vendor,
-- itu sudah ada di tender_participants.document_path). document_type pakai teks bebas supaya
-- fleksibel mengikuti pola lama (JENIS_DOKUMEN di eproc), nilai yang dipakai antara lain:
-- 'lelang' (dokumen lelang/RKS), 'kualifikasi' (dokumen kualifikasi), 'aritmatika' (BA koreksi
-- aritmatika), 'laporan' (laporan paket).
CREATE TABLE IF NOT EXISTS tender_documents (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  tender_id uuid NOT NULL REFERENCES tenders(id),
  document_type varchar(30) NOT NULL,
  name varchar(255) NOT NULL,
  file_path varchar(500) NOT NULL,
  file_size integer,
  notes text,
  uploaded_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_tender_documents_tender ON tender_documents(tender_id);

-- 2. SK PANITIA (master roster per unit kerja) dan anggotanya
CREATE TABLE IF NOT EXISTS sk_panitia (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  unit_kerja varchar(255) NOT NULL,
  nomor_sk varchar(100),
  tanggal_sk date,
  pejabat_penetap varchar(255),
  pejabat_penetap_nip varchar(50),
  tanggal_mulai date,
  tanggal_akhir date,
  status boolean NOT NULL DEFAULT true,
  file_sk varchar(255),
  file_path varchar(500),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp
);
-- panitia: anggota di dalam satu SK panitia (roster master, belum ditugaskan ke paket manapun).
CREATE TABLE IF NOT EXISTS panitia (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  sk_panitia_id uuid NOT NULL REFERENCES sk_panitia(id) ON DELETE CASCADE,
  nip varchar(50),
  nama varchar(255) NOT NULL,
  jabatan varchar(255),
  is_ketua boolean NOT NULL DEFAULT false,
  status boolean NOT NULL DEFAULT true,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_panitia_sk ON panitia(sk_panitia_id);

-- 3. PANITIA PER PAKET (penugasan panitia dari roster ke satu paket tertentu)
CREATE TABLE IF NOT EXISTS tender_panitia (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  tender_id uuid NOT NULL REFERENCES tenders(id),
  nip varchar(50),
  nama varchar(255) NOT NULL,
  jabatan varchar(255),
  is_ketua boolean NOT NULL DEFAULT false,
  status boolean NOT NULL DEFAULT true,
  -- validasi pemenang oleh panitia: langkah persetujuan tambahan sebelum pemenang final
  -- diumumkan, terpisah dari tender_participants.is_winner (itu penetapan awal oleh PPK/Pokja).
  validasi_pemenang varchar(20),
  validasi_pemenang_catatan text,
  locked boolean NOT NULL DEFAULT false,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_tender_panitia_tender ON tender_panitia(tender_id);

-- 4. PERNYATAAN MINAT (surat kuasa/pernyataan minat rekanan saat mendaftar paket)
CREATE TABLE IF NOT EXISTS tender_pernyataan_minat (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  tender_id uuid NOT NULL REFERENCES tenders(id),
  vendor_id uuid NOT NULL REFERENCES users(id),
  nama varchar(255),
  jabatan varchar(255),
  alamat text,
  telepon varchar(50),
  email varchar(255),
  penerima_kuasa varchar(255),
  penerima_kuasa_jabatan varchar(255),
  penerima_kuasa_ktp varchar(50),
  penerima_kuasa_file varchar(500),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_pernyataan_minat_tender ON tender_pernyataan_minat(tender_id);

-- 5. PAKTA INTEGRITAS (validasi pakta integritas per paket, oleh rekanan atau panitia)
CREATE TABLE IF NOT EXISTS tender_pakta_integritas (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  tender_id uuid NOT NULL REFERENCES tenders(id),
  user_id uuid NOT NULL REFERENCES users(id),
  kode varchar(100),
  jenis varchar(20) NOT NULL DEFAULT 'REKANAN', -- 'REKANAN' atau 'PANITIA'
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(tender_id, user_id, jenis)
);

-- 6. PIHAK LAIN (user login lain yang diberi akses lihat ke suatu paket)
CREATE TABLE IF NOT EXISTS tender_pihak_lain (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  tender_id uuid NOT NULL REFERENCES tenders(id),
  user_id uuid NOT NULL REFERENCES users(id),
  status boolean NOT NULL DEFAULT true,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(tender_id, user_id)
);

-- 7. PEMBUKAAN PENAWARAN (validasi pembukaan sampul 1 dan sampul 2)
-- jenis: 'ADMINISTRASI', 'TEKNIS', 'HARGA' dst mengikuti kode dokumen yang dibuka.
CREATE TABLE IF NOT EXISTS tender_pembukaan_validasi (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  tender_id uuid NOT NULL REFERENCES tenders(id),
  user_id uuid NOT NULL REFERENCES users(id),
  kode varchar(100),
  jenis varchar(50),
  tahap integer NOT NULL DEFAULT 1, -- 1 = sampul pertama, 2 = sampul kedua
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_pembukaan_validasi_tender ON tender_pembukaan_validasi(tender_id);

-- 8. KLARIFIKASI (dokumen klarifikasi dari rekanan + tanggapan aanwijzing dari panitia)
-- terpisah dari tender_aanwijzing_chats (itu chat/tanya-jawab realtime saat sesi aanwijzing).
-- Ini dokumen formal: rekanan upload dokumen klarifikasi, panitia balas dengan dokumen
-- tanggapan (parent_id menunjuk ke dokumen yang ditanggapi).
CREATE TABLE IF NOT EXISTS tender_klarifikasi_dokumen (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  tender_id uuid NOT NULL REFERENCES tenders(id),
  nama varchar(255),
  file_path varchar(500),
  file_size integer,
  notes text,
  vendor_id uuid REFERENCES users(id), -- null kalau ini tanggapan dari panitia
  parent_id uuid REFERENCES tender_klarifikasi_dokumen(id),
  status integer NOT NULL DEFAULT 1,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_klarifikasi_dokumen_tender ON tender_klarifikasi_dokumen(tender_id);

-- undangan klarifikasi resmi (jadwal pertemuan + email undangan) ke satu vendor tertentu
CREATE TABLE IF NOT EXISTS tender_undangan_klarifikasi (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  tender_id uuid NOT NULL REFERENCES tenders(id),
  vendor_id uuid NOT NULL REFERENCES users(id),
  tanggal_undangan date,
  jam varchar(20),
  peserta text,
  pelaksanaan varchar(50), -- misal 'Daring' / 'Luring'
  tempat text,
  keterangan text,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_undangan_klarifikasi_tender ON tender_undangan_klarifikasi(tender_id);

-- 9. PERINGKAT PEMENANG (urutan peringkat vendor pemenang + cadangan)
CREATE TABLE IF NOT EXISTS tender_pemenang_peringkat (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  tender_id uuid NOT NULL REFERENCES tenders(id),
  vendor_id uuid NOT NULL REFERENCES users(id),
  peringkat integer NOT NULL, -- 1 = pemenang utama, 2 = cadangan 1, dst
  keterangan text,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(tender_id, peringkat)
);
