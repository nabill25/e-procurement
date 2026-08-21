-- Migrasi 023: Kelompok H - Komunikasi Lanjutan
--
-- Berdasarkan riset ke eproc/application/controllers/inbox_rfi_json.php, chat_json.php,
-- phpshoutbox_json.php, shoutbox_json.php, nego_shoutbox_json.php, bidding_shoutbox_json.php,
-- dan modelnya masing-masing. Ditemukan sistem lama punya 5 sistem chat/pesan berbeda yang
-- SEMUA aktif (bukan cuma 2 seperti dugaan roadmap awal):
--
-- 1. INBOX_COMPLAIN_SET + INBOX_COMPLAIN_TYPE - struktur subjek/kategori komplain yang lebih
--    rinci dari sekadar inbox_categories yang sudah ada. INBOX_COMPLAIN_TYPE = daftar subjek
--    pilihan (dropdown) khusus form komplain, INBOX_COMPLAIN_SET = daftar alamat/penerima
--    default untuk komplain masuk.
--
-- 2. CHATSHOUTBOX - chat 1-ke-1 panitia<->vendor per paket, dibedakan lewat kolom JENIS_CHAT
--    (evaluasi teknis, evaluasi kualifikasi, auction/lelang, chat umum/kontrak). Ini BUKAN
--    sama dengan tender_aanwijzing_chats yang sudah ada, dan belum ada padanannya di sistem
--    baru sama sekali - dibuat sebagai tabel baru tender_general_chats.
--
-- 3. PHPSHOUTBOX - chat broadcast+konfirmasi kehadiran selama sesi aanwijzing. Sistem baru
--    sudah punya tender_aanwijzing_chats (chat dasar), tapi BELUM ada fitur konfirmasi
--    kehadiran (PESAN='CONFIRMED' di sistem lama) dan window waktu aktif aanwijzing.
--    Diperluas lewat kolom baru di tabel yang sudah ada, bukan tabel baru.
--
-- 4. NEGOSHOUTBOX (chat negosiasi harga) - SUDAH TERCAKUP oleh tender_negotiation_chats
--    yang sudah ada sejak migrasi 003. Tidak perlu tabel baru, cukup dicatat sebagai selesai.
--
-- 5. BIDDINGSHOUTBOX (chat saat sesi lelang harga/e-auction) - sistem baru BELUM punya modul
--    e-auction/lelang harga sama sekali (beda dari "penawaran harga" biasa yang sudah ada).
--    Karena chat ini scope-nya SPESIFIK untuk sesi auction yang belum ada wadahnya, dipakaikan
--    JENIS_CHAT='auction' di tabel tender_general_chats yang sama dengan CHATSHOUTBOX (di
--    sistem lama pun auction sebenarnya sudah dobel ditangani CHATSHOUTBOX JENIS_CHAT=2 DAN
--    BIDDINGSHOUTBOX terpisah - kemungkinan besar technical debt lama, tidak perlu ditiru
--    dobel di sistem baru).
--
-- Dikeluarkan dari migrasi ini (kode mati, dicek grep ke seluruh views/ termasuk backup):
-- PHPFREECHAT (tidak ada satupun controller/view yang memanggilnya), nego_shoutbox_json-.php
-- (versi lama nego_shoutbox_json.php yang sudah digantikan, tidak dipanggil dari manapun).
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS).

-- 1. KATEGORI KOMPLAIN TERSTRUKTUR
CREATE TABLE IF NOT EXISTS inbox_complain_types (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  name varchar(255) NOT NULL,
  description text,
  is_active boolean NOT NULL DEFAULT true,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS inbox_complain_recipients (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  email varchar(255) NOT NULL,
  keterangan varchar(255),
  is_active boolean NOT NULL DEFAULT true,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Tandai pesan yang termasuk komplain terstruktur (beda dari pesan Kontak Kami biasa)
ALTER TABLE inbox_messages ADD COLUMN IF NOT EXISTS complain_type_id uuid REFERENCES inbox_complain_types(id);

INSERT INTO inbox_complain_types (name, description) VALUES
  ('Keterlambatan Proses Tender', 'Komplain terkait proses tender yang berjalan lebih lambat dari jadwal'),
  ('Ketidaksesuaian Evaluasi', 'Komplain terkait hasil evaluasi penawaran/kualifikasi yang dirasa tidak sesuai'),
  ('Kendala Teknis Sistem', 'Komplain terkait error atau kendala teknis penggunaan sistem'),
  ('Lainnya', 'Komplain di luar kategori di atas')
ON CONFLICT DO NOTHING;

-- 2. CHAT UMUM PER PAKET (padanan CHATSHOUTBOX, dipakai juga untuk chat sesi auction)
CREATE TABLE IF NOT EXISTS tender_general_chats (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  tender_id uuid NOT NULL REFERENCES tenders(id),
  vendor_id uuid NOT NULL REFERENCES users(id),
  user_id uuid NOT NULL REFERENCES users(id),
  jenis_chat varchar(30) NOT NULL DEFAULT 'umum', -- 'umum' | 'evaluasi_teknis' | 'evaluasi_kualifikasi' | 'auction' | 'kontrak'
  message text NOT NULL,
  file_path varchar(255),
  is_read boolean NOT NULL DEFAULT false,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_general_chats_tender_vendor ON tender_general_chats(tender_id, vendor_id, jenis_chat);

-- 3. KONFIRMASI KEHADIRAN AANWIJZING (perluasan tender_aanwijzing_chats yang sudah ada)
ALTER TABLE tender_aanwijzing_chats ADD COLUMN IF NOT EXISTS is_confirmation boolean NOT NULL DEFAULT false;
-- is_confirmation = true berarti baris ini bukan pesan chat biasa, tapi penanda "vendor ini
-- konfirmasi hadir di sesi aanwijzing" (meniru PESAN='CONFIRMED' di PHPSHOUTBOX asli)
