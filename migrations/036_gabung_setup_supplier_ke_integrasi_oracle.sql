-- Migrasi 036: Gabungkan menu "Setup Supplier Oracle" ke dalam "Integrasi Oracle"
--
-- Migrasi 035 sempat membuat "Setup Supplier Oracle" sebagai menu tersendiri di sidebar.
-- Setelah dikonfirmasi ulang ke pengguna: "Setup Supplier" itu memang fitur yang ada DI DALAM
-- Oracle EBS sendiri (tim support Oracle yang mengerjakannya langsung di sana) - jadi tiket
-- permintaannya lebih pas jadi TAB di dalam halaman "Integrasi Oracle" yang sudah ada
-- (menu_key integration_oracle), bukan menu terpisah. Tabel data (oracle_supplier_requests dkk)
-- dan 4 role baru dari migrasi 035 TIDAK berubah - cuma menu & hak aksesnya yang digabung.
--
-- Aman dijalankan berulang kali.

-- Pindahkan hak akses 4 role Tim Support Oracle dari menu lama ke menu "integration_oracle"
INSERT INTO menu_role_access (menu_id, role)
SELECT id, r.role FROM menu_items, (VALUES
  ('pengaju_oracle'), ('verifikator_oracle'), ('dispatcher_oracle'), ('pelaksana_oracle')
) AS r(role)
WHERE menu_items.menu_key = 'integration_oracle'
ON CONFLICT DO NOTHING;

-- Hapus menu lama "Setup Supplier Oracle" beserta hak aksesnya (sudah tidak dipakai frontend -
-- kontennya sekarang jadi tab "Setup Supplier" di dalam Integration.jsx)
DELETE FROM menu_role_access
WHERE menu_id IN (SELECT id FROM menu_items WHERE menu_key = 'oracle_supplier_setup');

DELETE FROM menu_items WHERE menu_key = 'oracle_supplier_setup';
