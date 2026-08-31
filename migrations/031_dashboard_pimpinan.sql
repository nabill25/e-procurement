-- Migrasi 031: Dashboard Pimpinan
-- Padanan halaman executive_report.php di sistem lama (rekap portofolio pengadaan lintas
-- tahap RUP->Tender->Kontrak + metrik efisiensi HPS vs nilai kontrak), lihat endpoint baru
-- GET /api/dashboard/executive-summary dan GET /api/dashboard/efficiency.
-- Tidak ada tabel baru - murni query rekap dari data yang sudah ada (procurement_requests,
-- tenders, contracts). Cuma perlu daftarkan menu barunya ke sistem Hak Akses Menu.

INSERT INTO menu_items (menu_key, label, icon, order_index)
VALUES ('executive_dashboard', 'Dashboard Pimpinan', 'LayoutGrid', 1)
ON CONFLICT (menu_key) DO NOTHING;

INSERT INTO menu_role_access (menu_id, role)
SELECT id, role FROM menu_items, (VALUES ('admin'), ('ppk')) AS r(role)
WHERE menu_key = 'executive_dashboard'
ON CONFLICT DO NOTHING;
