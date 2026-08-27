-- Migrasi 026: Reschedule Tahapan Tender dengan Riwayat
--
-- Item ini sebelumnya sengaja ditunda sejak Kelompok A (lihat CLAUDE.md), sekarang dikerjakan
-- penuh atas permintaan pengguna. Berdasarkan riset ke eproc/application/controllers/
-- paket_tahap_json.php: sistem lama punya tabel PAKET_TAHAP (banyak baris tahap per paket,
-- masing-masing punya tanggal_awal/tanggal_akhir/jam sendiri, misal "Pendaftaran",
-- "Pemasukan Penawaran", dst), sedangkan sistem baru sebelumnya cuma punya 1 kolom status
-- tunggal (tenders.status) tanpa tanggal per tahap sama sekali.
--
-- 1. tender_stages -> padanan PAKET_TAHAP: satu baris per tahapan per tender, dengan tanggal
--    mulai/selesai sendiri. Nama tahap mengikuti 7 fase yang SUDAH ADA dan dipakai konsisten
--    di frontend (src/data/procurementPhases.js): pengumuman, pendaftaran, penawaran, evaluasi,
--    pemenang, masa_sanggah, kontrak. Tidak dibuat sistem "jenis metode lelang" bercabang
--    seperti paket_jenis/paket_metode_lelang di sistem lama (itu memang sengaja ditunda
--    terpisah, di luar cakupan reschedule) - 7 fase ini berlaku sama untuk semua tender.
--
-- 2. tender_stage_reschedule_history -> padanan PAKET_TAHAP_RESCHEDULE + PAKET_RESCHEDULE
--    (sistem lama punya 2 mekanisme riwayat paralel yang membingungkan - salah satunya
--    bahkan tabelnya tidak ditemukan di dump manapun, kemungkinan bug/tabel hilang di sistem
--    lama sendiri). Disederhanakan jadi SATU tabel riwayat yang jelas: snapshot tanggal lama
--    dan tanggal baru tiap kali satu tahap di-reschedule, plus alasan (sistem lama menyimpan
--    alasan di 10 kolom bernomor RESCHEDULE_1..10 di tabel paket - batasan desain lama yang
--    TIDAK ditiru, di sistem baru alasan cukup 1 baris per histori, tidak terbatas 10 kali).
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS).

CREATE TABLE IF NOT EXISTS tender_stages (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  tender_id uuid NOT NULL REFERENCES tenders(id),
  stage_key varchar(50) NOT NULL, -- 'pengumuman' | 'pendaftaran' | 'penawaran' | 'evaluasi' | 'pemenang' | 'masa_sanggah' | 'kontrak'
  start_date timestamp,
  end_date timestamp,
  reschedule_count integer NOT NULL DEFAULT 0,
  updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(tender_id, stage_key)
);
CREATE INDEX IF NOT EXISTS idx_tender_stages_tender ON tender_stages(tender_id);

CREATE TABLE IF NOT EXISTS tender_stage_reschedule_history (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  tender_stage_id uuid NOT NULL REFERENCES tender_stages(id),
  old_start_date timestamp,
  old_end_date timestamp,
  new_start_date timestamp,
  new_end_date timestamp,
  alasan text,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_reschedule_history_stage ON tender_stage_reschedule_history(tender_stage_id);
