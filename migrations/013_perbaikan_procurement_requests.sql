-- Migrasi 013: Perbaikan kolom procurement_requests yang belum pernah ada
--
-- Ditemukan waktu testing modul RUP/Analisa: endpoint POST /api/pengajuan sudah dari awal
-- mengacu ke kolom budget_code, description, technical_spec, quantity, unit_of_measure, dan
-- needed_by_date di query INSERT-nya (dan form pengajuan di frontend juga sudah punya field
-- ini), tapi kolom-kolomnya ternyata tidak pernah benar-benar dibuat di database. Akibatnya
-- pengajuan baru yang diisi lengkap akan selalu gagal ("column ... does not exist").
--
-- Ini BUKAN bagian dari modul RUP/Analisa yang sedang dikerjakan, tapi bug lama yang baru
-- ketahuan waktu testing menyeluruh, jadi sekalian diperbaiki di sini.
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS).

ALTER TABLE procurement_requests ADD COLUMN IF NOT EXISTS budget_code varchar(50);
ALTER TABLE procurement_requests ADD COLUMN IF NOT EXISTS description text;
ALTER TABLE procurement_requests ADD COLUMN IF NOT EXISTS technical_spec text;
ALTER TABLE procurement_requests ADD COLUMN IF NOT EXISTS quantity integer;
ALTER TABLE procurement_requests ADD COLUMN IF NOT EXISTS unit_of_measure varchar(30);
ALTER TABLE procurement_requests ADD COLUMN IF NOT EXISTS needed_by_date date;
