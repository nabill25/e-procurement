-- Migrasi 001: Melengkapi skema database yang masih bolong
--
-- Latar belakang: setelah sistem baru dicoba dijalankan dan dites lewat API satu per satu,
-- ditemukan beberapa kolom, satu tabel referensi, dan satu view yang dipakai oleh kode backend
-- (folder server/routes) tapi belum ada di database. Migrasi ini menambahkan semua yang kurang
-- itu, tanpa mengubah atau menghapus data yang sudah ada.
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS / CREATE OR REPLACE).

-- ── Tabel vendors: field profil perusahaan yang belum ada ──
ALTER TABLE vendors ADD COLUMN IF NOT EXISTS email varchar(150);
ALTER TABLE vendors ADD COLUMN IF NOT EXISTS phone varchar(50);
ALTER TABLE vendors ADD COLUMN IF NOT EXISTS province varchar(100);
ALTER TABLE vendors ADD COLUMN IF NOT EXISTS nib varchar(50);
ALTER TABLE vendors ADD COLUMN IF NOT EXISTS contact_person varchar(150);
ALTER TABLE vendors ADD COLUMN IF NOT EXISTS qualification_class varchar(50);
ALTER TABLE vendors ADD COLUMN IF NOT EXISTS blacklisted boolean NOT NULL DEFAULT false;
ALTER TABLE vendors ADD COLUMN IF NOT EXISTS verified_by uuid;
ALTER TABLE vendors ADD COLUMN IF NOT EXISTS verified_at timestamp;

-- ── Tabel users: field yang dipakai untuk profil PPK/Pokja/Admin ──
ALTER TABLE users ADD COLUMN IF NOT EXISTS unit_kerja varchar(150);

-- ── Tabel tender_participants: field proses penawaran & evaluasi ──
ALTER TABLE tender_participants ADD COLUMN IF NOT EXISTS document_path varchar(255);
ALTER TABLE tender_participants ADD COLUMN IF NOT EXISTS technical_score numeric;
ALTER TABLE tender_participants ADD COLUMN IF NOT EXISTS evaluation_notes text;
ALTER TABLE tender_participants ADD COLUMN IF NOT EXISTS is_evaluated boolean NOT NULL DEFAULT false;
ALTER TABLE tender_participants ADD COLUMN IF NOT EXISTS is_winner boolean NOT NULL DEFAULT false;

-- ── Tabel audit_logs: penanda berhasil/gagalnya suatu aksi ──
ALTER TABLE audit_logs ADD COLUMN IF NOT EXISTS is_success boolean NOT NULL DEFAULT true;

-- ── Perbaikan data: samakan istilah status vendor ──
-- Data yang sudah ada (dibuat lewat server/seed_real_users.js) memakai kata 'verified' (Inggris),
-- padahal seluruh kode (server/routes/vendors.js) memakai istilah Indonesia 'terverifikasi'.
-- Disamakan supaya vendor ini kehitung benar di filter status dan di Dashboard.
UPDATE vendors SET status = 'terverifikasi' WHERE status = 'verified';

-- ── View v_dashboard_stats: ringkasan angka untuk halaman Dashboard ──
-- Catatan asumsi (karena belum ada definisi resmi sebelumnya di kode):
--   - "tender aktif"  = tender yang sudah lewat draft tapi belum selesai/dibatalkan
--   - "kontrak selesai" = kontrak dengan status 'selesai'
--   - "anggaran tahun ini" = total pagu_anggaran tender yang dibuat tahun berjalan
--   - "menunggu review" = pengajuan dengan status 'proses_review'
-- Kalau nanti kebutuhan tampilannya beda, tinggal ganti definisi view ini saja, tidak perlu ubah kode.
CREATE OR REPLACE VIEW v_dashboard_stats AS
SELECT
  (SELECT COUNT(*) FROM tenders WHERE status IN ('pengumuman','pendaftaran','penawaran','evaluasi','pemenang')) AS active_tenders,
  (SELECT COUNT(*) FROM vendors WHERE status = 'terverifikasi') AS verified_vendors,
  (SELECT COUNT(*) FROM contracts WHERE status = 'selesai') AS completed_contracts,
  (SELECT COALESCE(SUM(pagu_anggaran), 0) FROM tenders WHERE EXTRACT(YEAR FROM created_at) = EXTRACT(YEAR FROM CURRENT_DATE)) AS total_budget_this_year,
  (SELECT COUNT(*) FROM procurement_requests WHERE status = 'proses_review') AS pending_reviews;
