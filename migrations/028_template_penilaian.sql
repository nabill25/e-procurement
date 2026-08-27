-- Migrasi 028: Template Penilaian Kinerja Penyedia (versi disederhanakan)
--
-- Item yang sengaja ditunda dari Kelompok A (paket_penilaian_template), sekarang dikerjakan
-- dalam bentuk yang disederhanakan atas persetujuan pengguna. Berdasarkan riset ke
-- eproc/application/models/paketpenilaian.php: sistem lama punya struktur PAKET_PENILAIAN_TEMPLATE
-- berjenjang (parent_id, bab/pasal kriteria penilaian, field NILAI=skor maksimal dan
-- PRESENTASI=bobot persen), PLUS approval 3 tingkat (Unit/Kasubdit/PPK) dan perhitungan
-- grading otomatis (A/B/C/D/E) lewat VIEW SQL agregasi.
--
-- YANG DITIRU: struktur template berjenjang (bab/pasal dengan bobot dan skor maksimal) -
-- ini yang jadi pembeda utama dari vendor_ratings yang sudah ada (cuma skor 1-5 + catatan
-- bebas, tanpa struktur kriteria).
--
-- YANG SENGAJA TIDAK DITIRU (disederhanakan atas kesepakatan): approval 3 tingkat
-- (Unit/Kasubdit/PPK) dan VIEW agregasi otomatis grading A-E. PPK cukup input skor per
-- kriteria langsung (mirip pola tender_eval_scores yang sudah ada di modul Evaluasi), total
-- skor dihitung on-the-fly di kode aplikasi (bukan VIEW database), tanpa alur approval
-- berjenjang. Ini konsisten dengan pola "sederhanakan yang berlebihan" yang sudah dipakai di
-- kelompok-kelompok migrasi sebelumnya.
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS).

-- Master kriteria penilaian, berjenjang (bab -> pasal), bisa dipakai ulang untuk semua kontrak
CREATE TABLE IF NOT EXISTS penilaian_kinerja_templates (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  parent_id uuid REFERENCES penilaian_kinerja_templates(id),
  kode varchar(20),
  nama varchar(255) NOT NULL,
  bobot_persen numeric(5,2), -- persentase bobot kriteria ini (cuma diisi di level pasal/leaf)
  skor_maksimal integer, -- skor maksimal untuk kriteria ini (cuma diisi di level pasal/leaf)
  catatan text,
  is_active boolean NOT NULL DEFAULT true,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_penilaian_template_parent ON penilaian_kinerja_templates(parent_id);

-- Transaksi: skor penilaian kinerja vendor untuk satu kontrak, per kriteria template
CREATE TABLE IF NOT EXISTS contract_penilaian_kinerja (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  contract_id uuid NOT NULL REFERENCES contracts(id),
  template_id uuid NOT NULL REFERENCES penilaian_kinerja_templates(id),
  skor integer NOT NULL,
  catatan text,
  scored_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(contract_id, template_id)
);
CREATE INDEX IF NOT EXISTS idx_penilaian_kinerja_contract ON contract_penilaian_kinerja(contract_id);
