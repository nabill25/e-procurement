-- Migrasi 006: Modul Pengaduan / Pusat Pesan
--
-- Mengikuti konsep tabel "inbox" + "inbox_category" di sistem eProc lama: pesan/pengaduan
-- dari publik atau vendor masuk ke satu kotak masuk, admin bisa baca dan balas.
--
-- Halaman "Kontak Kami" yang sudah ada di web baru ini (src/pages/KontakKami.jsx) sebelumnya
-- cuma tampilan doang, pesan yang dikirim tidak benar-benar tersimpan kemana-mana. Migrasi ini
-- menyediakan tempat penyimpanannya.
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS + ON CONFLICT DO NOTHING).

CREATE TABLE IF NOT EXISTS inbox_categories (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  name varchar(100) NOT NULL UNIQUE,
  is_active boolean NOT NULL DEFAULT true
);

CREATE TABLE IF NOT EXISTS inbox_messages (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  category_id uuid REFERENCES inbox_categories(id),
  subject varchar(255) NOT NULL,
  content text NOT NULL,
  attachment_path varchar(255),
  sender_id uuid REFERENCES users(id),
  sender_name varchar(150) NOT NULL,
  sender_email varchar(150) NOT NULL,
  sender_phone varchar(50),
  status varchar(20) NOT NULL DEFAULT 'belum_dibaca',
  parent_id uuid REFERENCES inbox_messages(id),
  read_by uuid REFERENCES users(id),
  read_at timestamp,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
-- status: 'belum_dibaca' / 'dibaca' / 'dibalas'

INSERT INTO inbox_categories (name) VALUES
  ('Pengaduan Vendor'),
  ('Pertanyaan Umum'),
  ('Bantuan Teknis'),
  ('Lainnya')
ON CONFLICT (name) DO NOTHING;

-- Daftarkan sebagai menu baru di sistem hak akses menu (migrations/005), khusus role admin,
-- mengikuti pola yang sama dengan menu Data Master dan Hak Akses Menu.
INSERT INTO menu_items (menu_key, label, icon, order_index) VALUES
  ('inbox', 'Pusat Pesan', 'Inbox', 12)
ON CONFLICT (menu_key) DO NOTHING;

INSERT INTO menu_role_access (menu_id, role)
SELECT id, 'admin' FROM menu_items WHERE menu_key = 'inbox'
ON CONFLICT (menu_id, role) DO NOTHING;
