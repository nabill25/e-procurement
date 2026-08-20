-- Migrasi 020: Kelompok E - Permohonan Paket/RUP Detail
--
-- Sisa dari roadmap kelompok E (sesuai CLAUDE.md): file analisa, approval berjenjang dengan
-- riwayat revisi, checklist kelengkapan, master jenis belanja & kategori analisa. Import SIRUP
-- tetap simulasi (butuh akses API resmi LKPP yang tidak dimiliki), sudah disepakati sebelumnya.
--
-- Semua controller sudah dicek aktif dipanggil dari eproc/application/views/main/*.php:
-- permohonan_paket_approval_json.php, permohonan_paket_checklist_json.php.
--
-- Isi kelompok E:
-- 1. procurement_request_files - lampiran dokumen analisa (dari permohonan_paket_analisa_file),
--    termasuk field tanda tangan elektronik (esign) yang ada di tabel asli.
-- 2. procurement_request_approvals - approval berjenjang, satu baris per approver (dari
--    permohonan_paket_approval, field ASLI cuma approved+approved_by, jadi approval berjenjang
--    di sini artinya BISA ADA LEBIH DARI SATU baris approval per pengajuan/oleh approver
--    berbeda, bukan alur approval "level" bertingkat formal - itu sesuai kode asli).
-- 3. procurement_request_revisions - riwayat revisi (dari permohonan_paket_approval_revisi),
--    dengan catatan + file, dikirim balik ke pengaju kalau dokumen dianggap kurang lengkap.
-- 4. procurement_request_checklist - checklist kelengkapan per pengajuan (dari
--    permohonan_paket_checklist), sumbernya dari master_checklist yang difilter per jenis
--    paket/metode pemilihan.
-- 5. master_checklist - daftar checklist master, kategori baru di Data Master (reuse pattern
--    yang sudah ada tapi tabel sendiri karena field lebih detail dari master_data biasa:
--    ada filter paket_jenis + metode_pemilihan + wajib).
-- 6. Kategori baru di master_data yang sudah ada: 'jenis_belanja', 'analisa_kategori' (dari
--    permohonan_paket_analisa_jenis_belanja, permohonan_paket_analisa_kategori - keduanya tabel
--    referensi sederhana, cukup reuse master_data, tidak perlu tabel baru).
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS).

-- 1. FILE ANALISA (lampiran dokumen analisa kebutuhan/pasar, termasuk info tanda tangan elektronik)
CREATE TABLE IF NOT EXISTS procurement_request_files (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  procurement_request_id uuid NOT NULL REFERENCES procurement_requests(id) ON DELETE CASCADE,
  judul varchar(100),
  file_path varchar(500) NOT NULL,
  file_type varchar(100),
  file_size numeric,
  esign_nomor_surat varchar(255),
  esign_status varchar(100),
  esign_path_file varchar(500),
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_pr_files_request ON procurement_request_files(procurement_request_id);

-- 2. APPROVAL BERJENJANG (satu baris per approver, mengikuti field asli persis)
CREATE TABLE IF NOT EXISTS procurement_request_approvals (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  procurement_request_id uuid NOT NULL REFERENCES procurement_requests(id) ON DELETE CASCADE,
  approved boolean,
  approved_by uuid REFERENCES users(id),
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(procurement_request_id, approved_by)
);

-- 3. RIWAYAT REVISI (catatan + file, dikirim balik ke pengaju)
CREATE TABLE IF NOT EXISTS procurement_request_revisions (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  procurement_request_id uuid NOT NULL REFERENCES procurement_requests(id) ON DELETE CASCADE,
  catatan text,
  file_path varchar(500),
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 4. MASTER CHECKLIST (difilter per jenis paket + metode pemilihan, ada flag wajib)
CREATE TABLE IF NOT EXISTS master_checklist (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  nama varchar(255) NOT NULL,
  paket_jenis varchar(50),
  metode_pemilihan varchar(50),
  wajib boolean NOT NULL DEFAULT false,
  is_active boolean NOT NULL DEFAULT true,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 5. CHECKLIST PER PENGAJUAN (centang/uncentang per item master_checklist)
CREATE TABLE IF NOT EXISTS procurement_request_checklist (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  procurement_request_id uuid NOT NULL REFERENCES procurement_requests(id) ON DELETE CASCADE,
  master_checklist_id uuid NOT NULL REFERENCES master_checklist(id),
  approved boolean NOT NULL DEFAULT false,
  notes text,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp,
  UNIQUE(procurement_request_id, master_checklist_id)
);
