-- Migrasi 025: Kelompok K - Lain-lain (bagian terakhir dari roadmap 100% paritas)
--
-- Berdasarkan riset ke eproc/application/models/rekamjejak.php, libraries/librekamjejak.php,
-- dan controllers/cronjobs_notif_dokexpired.php.
--
-- Dikerjakan (disepakati dengan pengguna): rekam_jejak dan logs_kirim_email_dok_expired.
-- DILEWATI: visitor (fitur statistik pengunjung yang di sistem lama sendiri sudah setengah
-- mati - datanya masih tercatat tapi fungsi laporannya sudah lama di-comment/dinonaktifkan
-- developer sebelumnya, tidak ada satupun halaman yang menampilkannya). TIDAK PERLU dimigrasi
-- sama sekali: pivoting (bukan tabel data bisnis, cuma trik SQL lama untuk generate angka urut
-- di query pivot laporan pajak, tergantikan sepenuhnya oleh generate_series() kalau memang
-- suatu saat dibutuhkan pola serupa).
--
-- 1. REKAM_JEJAK -> tender_activity_logs: timeline detail tiap tahap alur pengadaan (usulan,
--    evaluasi, negosiasi, kontrak dst), JAUH LEBIH DETAIL dari audit_logs generik yang sudah
--    ada (field posisi/tahap, relasi langsung ke paket/kontrak, keterangan approval/reject).
--    Kolom "posisi" di sistem lama berupa KODE ANGKA yang di-lookup dari daftar 150+ tahap
--    internal PHP - di sistem baru diganti jadi TEKS LANGSUNG (lebih mudah dibaca, tidak perlu
--    tabel lookup terpisah untuk 150+ kode yang sebagian besar spesifik ke alur lama).
--
-- 2. LOGS_KIRIM_EMAIL_DOK_EXPIRED -> document_expiry_notification_logs: log setiap kali email
--    peringatan dokumen expired dikirim ke vendor. Sistem lama pakai 2 VIEW database
--    (VIEW_REKANAN_DOKUMEN_EXPIRED, VIEW_REKANAN_DOKUMEN_EXPIRED_EMAIL) yang definisinya tidak
--    ditemukan di dump manapun - TIDAK ditiru, digantikan query langsung ke vendor_documents
--    (kolom expiry_date sudah ada sejak awal) yang lebih sederhana dan tidak butuh view
--    terpisah. Pengaturan aktif/nonaktif fitur ini SUDAH ADA dari kelompok F (app_settings,
--    kunci 'notifikasi_dokumen_expired'), tidak perlu tabel pengaturan baru.
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS).

CREATE TABLE IF NOT EXISTS tender_activity_logs (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  tender_id uuid REFERENCES tenders(id),
  procurement_request_id uuid REFERENCES procurement_requests(id),
  contract_id uuid REFERENCES contracts(id),
  posisi varchar(255) NOT NULL, -- nama tahap/aktivitas, teks langsung (bukan kode angka)
  keterangan text,
  flow varchar(100), -- kelompok alur: 'permohonan' | 'tender' | 'evaluasi' | 'negosiasi' | 'kontrak'
  user_id uuid REFERENCES users(id),
  ip_address varchar(100),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_activity_logs_tender ON tender_activity_logs(tender_id);
CREATE INDEX IF NOT EXISTS idx_activity_logs_request ON tender_activity_logs(procurement_request_id);
CREATE INDEX IF NOT EXISTS idx_activity_logs_contract ON tender_activity_logs(contract_id);

CREATE TABLE IF NOT EXISTS document_expiry_notification_logs (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  vendor_document_id uuid REFERENCES vendor_documents(id),
  vendor_id uuid REFERENCES users(id),
  sent_count integer NOT NULL DEFAULT 1, -- berapa kali sudah dikirim (meniru KIRIM_KE di sistem lama)
  last_sent_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_expiry_notif_vendor ON document_expiry_notification_logs(vendor_id);

-- Daftarkan menu baru "Dokumen Kedaluwarsa" ke sistem Hak Akses Menu, khusus admin
INSERT INTO menu_items (menu_key, label, icon, order_index, is_active)
SELECT 'document_expiry', 'Dokumen Kedaluwarsa', 'AlertTriangle', 17, true
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE menu_key = 'document_expiry');

INSERT INTO menu_role_access (menu_id, role)
SELECT id, 'admin' FROM menu_items WHERE menu_key = 'document_expiry'
ON CONFLICT DO NOTHING;
