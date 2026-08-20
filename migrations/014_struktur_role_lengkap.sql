-- Migrasi 014: Struktur Role Lengkap + Multi-Role (Fondasi sebelum pengembangan UI/UX)
--
-- Temuan dari riset kode sistem eProc lama: satu akun login BISA punya lebih dari satu role
-- sekaligus (tabel USER_LOGIN_MULTI di kode PHP-nya, model Userloginmulti.php), dan saat
-- login user bisa MEMILIH role mana yang mau dipakai untuk sesi itu (fungsi excSplitRole di
-- users_base_json.php: menyalin role yang dipilih ke akun utama, lalu sesi di-refresh).
-- Ada juga riwayat pergantian role (USER_LOGIN_MULTI_REKAM).
--
-- Sistem lama juga punya 14 role aktif (dicek langsung dari data tabel user_type di
-- eproc_migrasi.sql), bukan cuma 4 (admin/ppk/pokja/vendor) seperti sistem baru sekarang.
--
-- Migrasi ini membangun fondasi yang setara:
-- - role_definitions: daftar semua role yang dikenal sistem (termasuk 14 role dari sistem lama)
-- - user_roles: role apa saja yang boleh dipakai satu akun (bisa lebih dari satu)
-- - user_role_switch_history: catatan setiap kali user ganti role aktif
--
-- Kolom users.role TETAP dipakai sebagai "role yang sedang aktif sekarang" (sama seperti
-- sebelumnya), supaya SEMUA kode yang sudah ada (pengecekan user.role === 'admin' dst di
-- banyak halaman) tetap jalan tanpa perlu ditulis ulang. Yang baru adalah kemampuan satu
-- akun punya lebih dari satu role yang bisa dipilih/diganti.
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS / ON CONFLICT DO NOTHING).

CREATE TABLE IF NOT EXISTS role_definitions (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  role_key varchar(30) NOT NULL UNIQUE,
  label varchar(100) NOT NULL,
  description text,
  is_active boolean NOT NULL DEFAULT true,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS user_roles (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id uuid NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  role_key varchar(30) NOT NULL REFERENCES role_definitions(role_key),
  level varchar(30),
  is_primary boolean NOT NULL DEFAULT false,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(user_id, role_key)
);
-- level: dipakai role tertentu yang punya jenjang di sistem lama, misal 'staff' / 'kasi' / 'kasubdit'
-- untuk role perencanaan, atau 'staff' / 'kasi' untuk pejabat pengadaan. Boleh kosong untuk role
-- yang tidak punya jenjang.

CREATE TABLE IF NOT EXISTS user_role_switch_history (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id uuid NOT NULL REFERENCES users(id),
  role_old varchar(30),
  role_new varchar(30) NOT NULL,
  switched_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 14 role aktif dari sistem lama (data asli tabel user_type, WHERE aktif='1'), plus 4 role
-- yang sudah dipakai sistem baru sekarang (nama key disesuaikan gaya lowercase_underscore
-- yang sudah dipakai di kolom users.role).
INSERT INTO role_definitions (role_key, label, description) VALUES
  ('admin',                   'Administrator',            'Superuser sistem, akses penuh'),
  ('ppk',                     'PPK',                       'Pejabat Pembuat Komitmen'),
  ('pokja',                   'Pokja',                     'Kelompok Kerja Pemilihan'),
  ('vendor',                  'Penyedia',                  'Vendor / Penyedia Barang Jasa'),
  ('admin_vms',               'Admin VMS',                 'Administrator Vendor Management System'),
  ('administrator_approval',  'Administrator Approval',    'Approval tingkat administrator'),
  ('manager_pengadaan',       'Manager Pengadaan',         'Manajer pengadaan barang/jasa'),
  ('pengguna',                'Pengguna',                  'Pengaju kebutuhan / permohonan pengadaan'),
  ('audit',                   'Audit',                     'Auditor internal'),
  ('pelaksana_pengadaan',     'Pelaksana Pengadaan',       'Pelaksana proses pengadaan'),
  ('pengelola_kontrak',       'Pengelola Kontrak',         'Pengelola administrasi kontrak'),
  ('approval_vms',            'Approval VMS',              'Approval pendaftaran/verifikasi vendor'),
  ('kasubdit_kontrak',        'Kasubdit Kontrak',          'Pemeriksa kontrak tingkat kasubdit'),
  ('perencanaan',             'Perencanaan',               'Perencanaan pengadaan (RUP, analisa kebutuhan)')
ON CONFLICT (role_key) DO NOTHING;

-- Pindahkan role yang sudah dipakai user yang ada sekarang jadi baris resmi di user_roles
-- (ditandai sebagai role utama/primary masing-masing), supaya data lama otomatis konsisten
-- dengan struktur baru ini tanpa kehilangan apapun.
INSERT INTO user_roles (user_id, role_key, is_primary)
SELECT u.id, u.role, true
FROM users u
WHERE u.role IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM user_roles ur WHERE ur.user_id = u.id AND ur.role_key = u.role);

-- Daftarkan halaman "Manajemen User" sebagai menu baru khusus Admin, mengikuti pola
-- migrations/005 (sistem hak akses menu).
INSERT INTO menu_items (menu_key, label, icon, order_index) VALUES
  ('user_management', 'Manajemen User', 'Users2', 14)
ON CONFLICT (menu_key) DO NOTHING;

INSERT INTO menu_role_access (menu_id, role)
SELECT id, 'admin' FROM menu_items WHERE menu_key = 'user_management'
ON CONFLICT (menu_id, role) DO NOTHING;

-- Role baru (di luar admin/ppk/pokja/vendor yang sudah ada halamannya masing-masing) diberi
-- akses default ke Dashboard saja dulu. Ini sengaja minimal, bukan tebakan hak akses detail -
-- admin bisa atur lebih lanjut lewat halaman "Hak Akses Menu" begitu halaman/fitur khusus
-- untuk role-role ini mulai dikembangkan.
INSERT INTO menu_role_access (menu_id, role)
SELECT m.id, r.role_key
FROM menu_items m, role_definitions r
WHERE m.menu_key = 'dashboard'
  AND r.role_key NOT IN ('admin', 'ppk', 'pokja', 'vendor')
ON CONFLICT (menu_id, role) DO NOTHING;
