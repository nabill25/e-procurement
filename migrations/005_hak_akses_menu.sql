-- Migrasi 005: Manajemen Hak Akses Menu
--
-- Mengikuti konsep tbl_m_menu + tbl_m_menu_akses di sistem eProc lama (menu tersimpan di
-- database, hak akses per tipe user juga di database, bukan di-hardcode di kode program).
--
-- Disederhanakan dari versi lama: sistem baru ini belum punya pengecekan hak akses per aksi
-- (create/edit/delete) di halaman manapun, jadi cukup diatur level "boleh lihat menu ini atau
-- tidak" per role, sesuai dengan yang benar-benar dipakai sistem baru sekarang (dulu diatur
-- lewat kode di src/components/layout/Sidebar.jsx, sekarang dipindah ke database).
--
-- menu_items = daftar menu yang ada di sidebar.
-- menu_role_access = menu mana yang boleh dilihat role apa.
--
-- Data awal diisi PERSIS SAMA dengan aturan yang sebelumnya di-hardcode di Sidebar.jsx,
-- supaya tidak ada perubahan tampilan menu untuk siapapun saat migrasi ini pertama kali jalan.
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS + ON CONFLICT DO NOTHING).

CREATE TABLE IF NOT EXISTS menu_items (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  menu_key varchar(50) NOT NULL UNIQUE,
  label varchar(100) NOT NULL,
  icon varchar(50),
  order_index integer NOT NULL DEFAULT 0,
  is_active boolean NOT NULL DEFAULT true,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS menu_role_access (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  menu_id uuid NOT NULL REFERENCES menu_items(id) ON DELETE CASCADE,
  role varchar(30) NOT NULL,
  UNIQUE(menu_id, role)
);

INSERT INTO menu_items (menu_key, label, icon, order_index) VALUES
  ('dashboard',      'Dashboard',                 'LayoutDashboard', 1),
  ('pengajuan',      'Pengajuan',                  'FileText',        2),
  ('tender',         'Paket Pengadaan',            'Briefcase',       3),
  ('katalog',        'E-Purchasing',               'Sparkles',        4),
  ('purchasing',     'Purchase Orders',            'FileText',        5),
  ('vendor',         'Manajemen Vendor',           'Building2',       6),
  ('blacklist',      'Daftar Hitam',                'AlertTriangle',   7),
  ('vendor_profile', 'Profil & Kualifikasi',       'ShieldCheck',     8),
  ('audit',          'Audit & Dokumen',            'ShieldCheck',     9),
  ('master_data',    'Data Master',                'Database',        10),
  ('menu_access',    'Hak Akses Menu',             'Lock',            11)
ON CONFLICT (menu_key) DO NOTHING;

-- Hak akses awal, disalin persis dari logika lama di Sidebar.jsx:
-- admin bisa lihat semua menu di atas.
INSERT INTO menu_role_access (menu_id, role)
SELECT id, 'admin' FROM menu_items
ON CONFLICT (menu_id, role) DO NOTHING;

-- ppk: dashboard, pengajuan, tender, katalog, purchasing
INSERT INTO menu_role_access (menu_id, role)
SELECT id, 'ppk' FROM menu_items WHERE menu_key IN ('dashboard','pengajuan','tender','katalog','purchasing')
ON CONFLICT (menu_id, role) DO NOTHING;

-- pokja: dashboard, tender, vendor, blacklist
INSERT INTO menu_role_access (menu_id, role)
SELECT id, 'pokja' FROM menu_items WHERE menu_key IN ('dashboard','tender','vendor','blacklist')
ON CONFLICT (menu_id, role) DO NOTHING;

-- vendor: dashboard, tender, blacklist, vendor_profile, katalog, purchasing
INSERT INTO menu_role_access (menu_id, role)
SELECT id, 'vendor' FROM menu_items WHERE menu_key IN ('dashboard','tender','blacklist','vendor_profile','katalog','purchasing')
ON CONFLICT (menu_id, role) DO NOTHING;
