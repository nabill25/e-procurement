-- Migrasi 003: Modul Negosiasi
--
-- Menambahkan kemampuan negosiasi harga antara Pokja/PPK dengan vendor pemenang tender,
-- mengikuti alur sistem eProc lama (tabel paket_negosiasi + negoshoutbox), disederhanakan
-- dan disesuaikan dengan gaya skema tabel yang sudah ada di sistem baru ini.
--
-- Negosiasi disimpan per (tender, vendor): harga hasil negosiasi disimpan langsung di
-- tender_participants (mengikuti pola technical_score/is_winner yang sudah ada di sana),
-- sedangkan riwayat percakapan negosiasi disimpan di tabel tender_negotiation_chats
-- (mengikuti pola tender_aanwijzing_chats yang sudah ada).
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS).

ALTER TABLE tender_participants ADD COLUMN IF NOT EXISTS negotiated_price numeric;
ALTER TABLE tender_participants ADD COLUMN IF NOT EXISTS negotiation_status varchar(20) NOT NULL DEFAULT 'belum';
-- negotiation_status: 'belum' (belum mulai) / 'berlangsung' / 'sepakat' / 'gagal'

CREATE TABLE IF NOT EXISTS tender_negotiation_chats (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  tender_id uuid NOT NULL REFERENCES tenders(id),
  vendor_id uuid NOT NULL REFERENCES users(id),
  user_id uuid NOT NULL REFERENCES users(id),
  message text NOT NULL,
  offered_price numeric,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
