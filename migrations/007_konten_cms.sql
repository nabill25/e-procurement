-- Migrasi 007: Konten / CMS (Berita & FAQ)
--
-- Mengikuti tabel "berita" dan "faq" di sistem eProc lama, supaya admin bisa mengelola
-- pengumuman/berita dan FAQ dari halaman admin, ditampilkan di halaman utama publik.
--
-- Lingkup sengaja dibatasi ke Berita dan FAQ dulu (paling jelas kegunaannya dan paling aman
-- ditambahkan tanpa mengubah bagian yang sudah jalan baik, seperti carousel banner di halaman
-- utama yang sudah bagus dan sudah pakai data sendiri). Modul "Kebijakan" dan "Banner" belum
-- dibuat, bisa ditambahkan lagi nanti kalau memang dibutuhkan.
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS).

CREATE TABLE IF NOT EXISTS cms_news (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  title varchar(255) NOT NULL,
  content text NOT NULL,
  image_url varchar(255),
  is_published boolean NOT NULL DEFAULT true,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cms_faq (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  question text NOT NULL,
  answer text NOT NULL,
  order_index integer NOT NULL DEFAULT 0,
  is_published boolean NOT NULL DEFAULT true,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Daftarkan sebagai menu baru khusus admin, mengikuti pola migrations/005.
INSERT INTO menu_items (menu_key, label, icon, order_index) VALUES
  ('content_management', 'Kelola Konten', 'Newspaper', 13)
ON CONFLICT (menu_key) DO NOTHING;

INSERT INTO menu_role_access (menu_id, role)
SELECT id, 'admin' FROM menu_items WHERE menu_key = 'content_management'
ON CONFLICT (menu_id, role) DO NOTHING;
