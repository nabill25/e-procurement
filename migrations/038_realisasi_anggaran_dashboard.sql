-- Migrasi 038: tambahkan "anggaran terserap" ke view v_dashboard_stats
--
-- Ditemukan 2026-09-03: widget "Realisasi Anggaran TA 2025" di halaman Dashboard
-- (src/components/dashboard/MetricCards.jsx) dari awal SELALU menampilkan "Terserap: Rp 0"
-- dan "0% terserap" apapun isi datanya. Bukan soal kurang data - field yang dibaca frontend
-- (total_budget_used) TIDAK PERNAH dikembalikan oleh backend sama sekali, jadi selalu bernilai
-- 0. total_budget_this_year yang sudah ada (SUM pagu_anggaran tender tahun berjalan) itu
-- representasi "anggaran yang direncanakan/dianggarkan", sedangkan "terserap" seharusnya
-- representasi anggaran yang SUDAH benar-benar terpakai jadi kontrak (committed spend).
--
-- Diambil dari SUM(contracts.contract_value) untuk kontrak berstatus aktif/selesai, dibatasi
-- ke tender yang dibuat di tahun berjalan juga (supaya pembilang & penyebutnya konsisten
-- scope tahun yang sama dengan total_budget_this_year).

-- Catatan: kolom baru (total_budget_used) HARUS ditaruh paling akhir di daftar SELECT.
-- CREATE OR REPLACE VIEW di Postgres cuma boleh menambah kolom baru di posisi akhir,
-- tidak boleh menyisipkan di tengah (dianggap "mengganti nama" kolom yang sudah ada
-- setelahnya) - sempat dicoba taruh di tengah dan ditolak Postgres, ini versi yang benar.
CREATE OR REPLACE VIEW v_dashboard_stats AS
SELECT
  (SELECT COUNT(*) FROM tenders WHERE status IN ('pengumuman','pendaftaran','penawaran','evaluasi','pemenang')) AS active_tenders,
  (SELECT COUNT(*) FROM vendors WHERE status = 'terverifikasi') AS verified_vendors,
  (SELECT COUNT(*) FROM contracts WHERE status = 'selesai') AS completed_contracts,
  (SELECT COALESCE(SUM(pagu_anggaran), 0) FROM tenders WHERE EXTRACT(YEAR FROM created_at) = EXTRACT(YEAR FROM CURRENT_DATE)) AS total_budget_this_year,
  (SELECT COUNT(*) FROM procurement_requests WHERE status = 'proses_review') AS pending_reviews,
  (SELECT COALESCE(SUM(c.contract_value), 0)
     FROM contracts c
     JOIN tenders t ON t.id = c.tender_id
     WHERE c.status IN ('aktif', 'selesai')
       AND EXTRACT(YEAR FROM t.created_at) = EXTRACT(YEAR FROM CURRENT_DATE)) AS total_budget_used;
