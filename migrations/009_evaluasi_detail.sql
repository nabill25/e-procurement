-- Migrasi 009: Evaluasi Tender Detail (per kategori)
--
-- Sistem lama punya evaluasi yang dipecah jadi banyak kategori terpisah: administrasi,
-- teknis, harga, kualifikasi, personil, peralatan, sertifikat lain, pengalaman, dan syarat
-- pendaftaran (lihat tabel paket_eval_* dan rekanan_eval_* di eproc_migrasi.sql). Sistem baru
-- sebelumnya cuma punya 1 skor gabungan (kolom technical_score di tender_participants).
--
-- Catatan penting: beberapa kategori di sistem lama (terutama "pengalaman" dan "personil")
-- punya rumus penilaian yang sangat spesifik mengikuti aturan pengadaan pemerintah (misalnya
-- tabel paket_eval_pengalaman punya kolom bp_nilai, nk1_rp, nk2_rpmin, nk2_rpmax, dst yang
-- sepertinya mengikuti rumus resmi LKPP). Rumus itu TIDAK ditiru di sini karena saya tidak
-- punya dokumen resmi acuannya dan tidak mau menebak-nebak rumus yang berkaitan dengan
-- kepatuhan pengadaan. Sebagai gantinya, semua kategori memakai cara yang sama: Pokja bikin
-- daftar kriteria per kategori untuk tiap tender, lalu nilai tiap vendor secara manual per
-- kriteria (skor + catatan + memenuhi syarat atau tidak). Kalau nanti rumus resminya
-- didapatkan, tabel ini masih bisa dipakai, tinggal ditambahkan logika hitung otomatisnya.
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS).

CREATE TABLE IF NOT EXISTS tender_eval_criteria (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  tender_id uuid NOT NULL REFERENCES tenders(id),
  category varchar(30) NOT NULL,
  name varchar(255) NOT NULL,
  is_mandatory boolean NOT NULL DEFAULT true,
  weight numeric,
  order_index integer NOT NULL DEFAULT 0,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
-- category: 'administrasi' / 'teknis' / 'harga' / 'kualifikasi' / 'personil' / 'peralatan' /
--           'sertifikat_lain' / 'pengalaman' / 'syarat_daftar'

CREATE TABLE IF NOT EXISTS tender_eval_scores (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  criteria_id uuid NOT NULL REFERENCES tender_eval_criteria(id) ON DELETE CASCADE,
  vendor_id uuid NOT NULL REFERENCES users(id),
  meets_requirement boolean,
  score numeric,
  notes text,
  scored_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(criteria_id, vendor_id)
);
