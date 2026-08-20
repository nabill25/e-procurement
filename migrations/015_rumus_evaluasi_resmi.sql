-- Migrasi 015: Rumus Evaluasi Resmi untuk Personil, Peralatan, Sertifikat
--
-- Kemarin (migrations/009) saya sengaja bikin evaluasi manual semua karena mengira rumus resmi
-- kategori "pengalaman" tidak ada dokumennya. Itu KELIRU. Setelah ditelusuri langsung ke kode
-- JavaScript asli yang benar-benar dipakai sistem produksi (eproc/lib/eproc/allfunc.js), ternyata:
--
-- - Kategori PENGALAMAN: dicek ulang, ternyata halaman aktifnya (eproc/application/views/main/
--   evaluasi_kualifikasi_pengalaman.php) MEMANG cuma input manual, tidak ada rumus otomatis yang
--   benar-benar dipakai (ada fungsi hitungBidangPekerjaan() di allfunc.js yang kelihatannya rumus
--   lama/kompleks, tapi setelah dicek TIDAK DIPANGGIL dari halaman manapun yang aktif - jadi kode
--   mati/tidak terpakai). Jadi cara manual yang sudah saya buat sebelumnya SUDAH BENAR untuk
--   kategori ini, tidak perlu diubah.
--
-- - Kategori PERSONIL, PERALATAN, SERTIFIKAT: ternyata BENAR-BENAR punya rumus otomatis yang aktif
--   dipakai (fungsi hitungPersonil(), hitungPeralatan(), hitungSertifikat() di allfunc.js, dipanggil
--   dari eproc/application/views/main/evaluasi_kualifikasi_personil.php dst). Rumus ini yang
--   diimplementasikan di migrasi ini.
--
-- Rumus umum (sama pola untuk ketiganya, beda di detail):
-- 1. Tiap item (1 personil/1 alat/1 sertifikat) dinilai "Kesesuaian": S=100, TS=0, atau manual
--    (kalau manual diisi persis 0 atau 100, dipaksa jadi 50 - supaya tidak "curang" mengklaim
--    sempurna/nol tanpa pilih S/TS secara eksplisit).
-- 2. PERALATAN saja: nilai kesesuaian dikali dulu dengan faktor kepemilikan (100=milik sendiri,
--    kurang dari itu kalau sewa dst) dibagi 100.
-- 3. PERSONIL saja: kalau jumlah orang yang diajukan KURANG dari yang dibutuhkan, nilai dibagi
--    (jumlah_dibutuhkan x 100) - jadi didilusi/dikurangi proporsional. Kalau sudah cukup/lebih,
--    nilai di-cap maksimal 1.0 (100%) begitu totalnya mencapai jumlah_dibutuhkan x 100.
-- 4. PERALATAN & SERTIFIKAT: jumlah nilai kesesuaian semua item, dibagi 100, di-cap maksimal 1.0.
-- 5. Rasio tadi (0-1) dikali dengan bobot (%) kategori itu dalam grup evaluasinya (mis. "Site
--    Manager" = 40% dari total Personil) → jadi nilai kontribusi kategori itu.
-- 6. Jumlah semua nilai kontribusi kategori (dalam grup yang sama, mis. semua peran personil)
--    dikali nilai maksimal grup (mis. Personil = 20 poin dari 100 total evaluasi) dibagi 100
--    → jadi nilai akhir grup itu untuk vendor tersebut.
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS).

ALTER TABLE tender_eval_criteria ADD COLUMN IF NOT EXISTS required_count integer;
-- required_count: khusus kategori 'personil', jumlah orang yang dibutuhkan untuk peran ini.

CREATE TABLE IF NOT EXISTS tender_eval_category_config (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  tender_id uuid NOT NULL REFERENCES tenders(id),
  category varchar(30) NOT NULL,
  max_score numeric NOT NULL DEFAULT 100,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(tender_id, category)
);
-- max_score: nilai maksimal untuk keseluruhan kategori ini (mis. Personil = 20 poin dari 100
-- total evaluasi). Cuma relevan untuk kategori yang punya rumus otomatis (personil/peralatan/
-- sertifikat_lain), diisi manual oleh Pokja saat menyiapkan tender.

CREATE TABLE IF NOT EXISTS tender_eval_score_items (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  criteria_id uuid NOT NULL REFERENCES tender_eval_criteria(id) ON DELETE CASCADE,
  vendor_id uuid NOT NULL REFERENCES users(id),
  item_name varchar(255) NOT NULL,
  suitability varchar(3),
  suitability_value numeric,
  ownership_factor numeric,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
-- Satu baris = satu personil/satu unit alat/satu sertifikat yang diajukan vendor untuk satu
-- kriteria (mis. 1 baris per calon "Site Manager" yang diajukan). suitability: 'S' / 'R' / 'TS'.
-- ownership_factor: cuma dipakai kategori peralatan (0-100, milik sendiri/sewa dst), NULL untuk
-- kategori lain.
