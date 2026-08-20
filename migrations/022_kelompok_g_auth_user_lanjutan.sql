-- Migrasi 022: Kelompok G - Auth/User Lanjutan
--
-- Berdasarkan riset ke eproc/application/controllers/login.php, models/usersbase.php,
-- controllers/api.php, models/key.php, dan controllers/rekanan_json.php (pola zdel_*).
--
-- 1. user_login_logs - riwayat login per akun (dari tabel asli USER_LOGIN_LOGS).
--    Di sistem lama datanya cuma DITULIS (IP, OS, browser, waktu, status sesi), tidak pernah
--    ada halaman yang menampilkan histori detailnya (cuma ringkasan "terakhir login kapan").
--    Di sistem baru sekalian dibuatkan halaman untuk melihat histori ini karena datanya
--    berguna untuk keamanan (audit siapa login dari mana), UI-nya memang baru (tidak ada
--    versi lama untuk dicontoh).
--
-- 2. api_keys + api_key_requests - dari tabel asli KEY + KEY_REQUEST, dipakai controllers/api.php
--    untuk integrasi sistem pihak ketiga (ambil data RUP/paket lewat REST API). Di sistem lama
--    TIDAK ADA UI untuk kelola key (generate/aktifkan/nonaktifkan) - itu dilakukan manual
--    langsung ke database. Sistem baru menambahkan UI kelola key sebagai perbaikan gap ini.
--
-- 3. Kolom deleted_at di vendors dan users - meniru TUJUAN dari pola 18 tabel zdel_* (arsipkan
--    data sebelum betul-betul hilang), tapi TIDAK meniru caranya persis (sistem lama menyalin
--    ke tabel arsip terpisah per tabel, lalu hard-delete). Pendekatan modern yang lebih simpel:
--    soft-delete pakai satu kolom timestamp, data asli tetap di tempatnya (tidak perlu tabel
--    duplikat). Catatan: ini baru KERANGKA, belum ada fitur hapus vendor/user yang memakainya -
--    sistem baru sekarang belum py fitur hapus permanen sama sekali (cuma ubah status), jadi
--    kolom ini disiapkan untuk dipakai nanti kalau fitur hapus memang dibangun.
--
-- Dilewati (tidak dibuat migrasinya): tbl_m_logs ("log sistem menu") - setelah dicek, ternyata
-- bukan log menu, tapi log percobaan akses ditolak (403), dan TIDAK ADA satupun halaman yang
-- membacanya di sistem lama (murni tulis, tidak pernah dibaca/berguna). Tidak ada fungsi yang
-- hilang kalau ini tidak ditiru.
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS).

-- 1. LOG LOGIN PER AKUN
CREATE TABLE IF NOT EXISTS user_login_logs (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id uuid REFERENCES users(id),
  username varchar(150),
  ip_address varchar(100),
  user_agent text,
  is_active boolean NOT NULL DEFAULT true, -- sesi ini masih aktif atau sudah logout
  login_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  logout_at timestamp
);
CREATE INDEX IF NOT EXISTS idx_login_logs_user ON user_login_logs(user_id);

-- 2. API KEY UNTUK INTEGRASI PIHAK KETIGA
CREATE TABLE IF NOT EXISTS api_keys (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  api_key varchar(255) NOT NULL UNIQUE,
  client_name varchar(255) NOT NULL,
  is_active boolean NOT NULL DEFAULT true,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS api_key_requests (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  api_key_id uuid REFERENCES api_keys(id),
  api_key varchar(255),
  client_name varchar(255),
  endpoint varchar(500),
  ip_address varchar(100),
  user_agent text,
  is_valid_key boolean NOT NULL DEFAULT true,
  requested_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_api_key_requests_key ON api_key_requests(api_key_id);

-- 3. KERANGKA SOFT-DELETE (belum dipakai fitur manapun, disiapkan untuk nanti)
ALTER TABLE vendors ADD COLUMN IF NOT EXISTS deleted_at timestamp;
ALTER TABLE users   ADD COLUMN IF NOT EXISTS deleted_at timestamp;

-- 4. Daftarkan 2 menu baru (Riwayat Login, API Key) ke sistem Hak Akses Menu, khusus admin
INSERT INTO menu_items (menu_key, label, icon, order_index, is_active)
SELECT 'login_logs', 'Riwayat Login', 'History', 15, true
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE menu_key = 'login_logs');

INSERT INTO menu_items (menu_key, label, icon, order_index, is_active)
SELECT 'api_keys', 'API Key', 'KeyRound', 16, true
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE menu_key = 'api_keys');

INSERT INTO menu_role_access (menu_id, role)
SELECT id, 'admin' FROM menu_items WHERE menu_key IN ('login_logs', 'api_keys')
ON CONFLICT DO NOTHING;
