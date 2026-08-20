-- Migrasi 010: Kualifikasi Vendor (SIKaP) - lengkapi data yang masih kurang
--
-- Setelah dicek, halaman Profil & Kualifikasi Vendor ternyata sudah cukup lengkap:
-- dokumen legalitas (akta, NIB, NPWP, dst) sudah bisa diunggah lewat tab Dokumen
-- (tabel vendor_documents), begitu juga pajak, tenaga ahli, peralatan, dan pengurus
-- (kolom jsonb di tabel vendors, dipakai tab-tab di SikapTabs.jsx).
--
-- Yang benar-benar belum ada: data rekening bank dan neraca keuangan. Ditambahkan
-- mengikuti pola yang sama seperti kolom jsonb yang sudah ada (pajak, tenaga_ahli, dst),
-- supaya konsisten dengan cara kerja yang sudah ada, bukan bikin sistem baru yang beda sendiri.
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS).

ALTER TABLE vendors ADD COLUMN IF NOT EXISTS bank jsonb;
ALTER TABLE vendors ADD COLUMN IF NOT EXISTS neraca jsonb;
