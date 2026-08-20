-- Migrasi 008: Validasi QR
--
-- Mengikuti tabel "qr_validasi" di sistem eProc lama. Catatan penting: di sistem lama,
-- fitur ini ternyata TIDAK PERNAH selesai dibangun (controller-nya cuma kerangka kosong,
-- view-nya tidak ada sama sekali di source code). Jadi modul ini dibangun berdasarkan
-- struktur tabel yang sudah ada (mengindikasikan maksud aslinya: cek keaslian dokumen
-- pengadaan lewat kode QR), bukan menyalin fitur yang sudah jadi.
--
-- Fungsinya: dokumen pengadaan (pengumuman tender, hasil evaluasi, dst) diberi kode unik.
-- Siapapun bisa memindai kode QR di dokumen itu atau memasukkan kodenya manual di halaman
-- publik untuk memastikan dokumen itu asli dan belum diubah-ubah.
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS).

CREATE TABLE IF NOT EXISTS qr_validations (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  qr_code varchar(20) NOT NULL UNIQUE,
  source_type varchar(50) NOT NULL,
  tender_id uuid REFERENCES tenders(id),
  vendor_id uuid REFERENCES users(id),
  info text,
  created_by uuid REFERENCES users(id),
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
-- source_type: contoh 'pengumuman_tender' / 'hasil_evaluasi' / 'kontrak' / 'lainnya'
