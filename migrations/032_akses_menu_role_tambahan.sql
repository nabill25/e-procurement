-- Migrasi 032: buka akses menu untuk sebagian dari 10 role tambahan (fondasi multi-role sudah
-- ada sejak migrations/014), ke modul yang SUDAH ADA di sistem baru dan fungsinya cocok dengan
-- tugas role itu di sistem lama (dicek langsung ke data tbl_m_menu di eproc_migrasi.sql, bukan
-- tebakan). Role yang butuh fitur baru (Admin VMS sebagian, Audit versi detail, Administrator
-- Approval, Perencanaan) belum dikerjakan di migrasi ini, menyusul.
--
-- Pemetaan (role lama -> menu lama -> menu baru):
--   PENGGUNA           -> "Permohonan Paket Tambah" dkk       -> pengajuan (submit RUP, sudah bisa dilakukan siapa saja yang login)
--   MANAGER PENGADAAN  -> "Dashboard Manager" (1 menu doang)  -> dashboard + executive_dashboard (rekap portofolio level manajerial)
--   PELAKSANA PENGADAAN-> Katalog & pembelian langsung        -> katalog + purchasing
--   PENGELOLA KONTRAK  -> 57 menu, semua soal tahapan kontrak -> tender (untuk buka tab Kontrak & BAST di detail tender)
--   KASUBDIT KONTRAK   -> 0 menu sendiri, nempel di kontrak, tugasnya approval/pemeriksa -> tender (tab Kontrak, hak edit sama seperti Pengelola Kontrak)
--   APPROVAL VMS       -> "Daftar Rekanan Approval"           -> vendor (dengan hak verifikasi/blokir/tangguhkan, lihat perubahan kode di vendors.js)
--   AUDIT              -> "Contracting Audit Dokumen" dkk     -> audit (versi generik yang sudah ada, belum 100% sama, dicatat sebagai keterbatasan di CLAUDE.md)

INSERT INTO menu_role_access (menu_id, role)
SELECT id, r.role
FROM menu_items, (VALUES
  ('pengguna', 'pengajuan'),
  ('manager_pengadaan', 'dashboard'),
  ('manager_pengadaan', 'executive_dashboard'),
  ('pelaksana_pengadaan', 'katalog'),
  ('pelaksana_pengadaan', 'purchasing'),
  ('pengelola_kontrak', 'tender'),
  ('kasubdit_kontrak', 'tender'),
  ('approval_vms', 'vendor'),
  ('audit', 'audit')
) AS r(role, menu_key)
WHERE menu_items.menu_key = r.menu_key
ON CONFLICT DO NOTHING;
