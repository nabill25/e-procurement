-- Migrasi 034: Pencatatan Tindak Lanjut Kelengkapan Dokumen Penyedia
--
-- Menerapkan ke sistem baru sebuah fitur yang sebelumnya dirancang untuk sistem lama (eproc)
-- tapi belum pernah dipasang di production sana. Rancangan aslinya ada di folder root project
-- 2026-09-01_pencatatan-tindak-lanjut-verifikasi-penyedia/ (BACA-DULU.md, source_code/, sql/).
--
-- Fitur ini melacak bolak-balik (tektok) antara verifikator (admin/approval_vms) dan penyedia
-- saat melengkapi dokumen registrasi: verifikator minta kelengkapan -> penyedia konfirmasi ->
-- verifikator cek ulang -> tandai terverifikasi (atau ulang dari awal kalau masih kurang).
--
-- TIDAK mengubah tabel vendors yang sudah ada (murni tambah tabel baru, aman/additive). Kolom
-- vendors.status tetap dipakai apa adanya (pending/terverifikasi/ditangguhkan/diblokir), tabel
-- baru ini cuma lapisan pelacakan komunikasi di atasnya - persis seperti rancangan aslinya.
--
-- Perbedaan sengaja dari rancangan asli (disesuaikan konvensi sistem baru, lihat komentar
-- lengkap di server/routes/vendors.js bagian "TINDAK LANJUT KELENGKAPAN DOKUMEN PENYEDIA"):
-- status/jenis/pihak pakai snake_case huruf kecil (bukan UPPERCASE), PK uuid (bukan integer
-- manual getNextId), tidak perlu konstanta "email fallback verifikator" karena users.email di
-- sistem baru sudah selalu ada, dan "cron pengingat" diganti tombol manual admin (sistem baru
-- tidak punya cron OS aktif) - saklar on/off-nya reuse app_settings yang sudah ada.
--
-- Aman dijalankan berkali-kali (IF NOT EXISTS + ON CONFLICT DO NOTHING).

CREATE TABLE IF NOT EXISTS vendor_followups (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  vendor_id uuid NOT NULL REFERENCES vendors(id) ON DELETE CASCADE,

  -- Status berkas SETELAH kejadian ini:
  --   perlu_dilengkapi   verifikator minta kelengkapan (bola di penyedia)
  --   sudah_dilengkapi   penyedia sudah konfirmasi lengkap (bola di verifikator)
  --   terverifikasi      verifikator menyatakan dokumen sudah lengkap/oke
  status varchar(20) NOT NULL,

  -- Jenis kejadian yang menghasilkan baris ini:
  --   permintaan  verifikator kirim catatan minta lengkapi
  --   konfirmasi  penyedia klik "sudah saya lengkapi"
  --   reminder    pengingat (dipicu manual admin/approval_vms dari antrian, lihat vendors.js)
  --   selesai     verifikator menandai terverifikasi
  jenis varchar(20) NOT NULL,

  catatan text,

  -- Pihak yang menimbulkan baris ini: verifikator / penyedia / sistem
  pihak varchar(20) NOT NULL,

  created_by uuid REFERENCES users(id), -- NULL kalau memang tidak ada pelaku (jarang terjadi)
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  -- Jejak email notifikasi untuk baris ini (audit + tahu kalau perlu dikirim ulang manual)
  email_tujuan varchar(255),
  email_terkirim boolean NOT NULL DEFAULT false,
  email_terkirim_at timestamp
);

CREATE INDEX IF NOT EXISTS idx_vendor_followups_vendor ON vendor_followups(vendor_id);
CREATE INDEX IF NOT EXISTS idx_vendor_followups_status ON vendor_followups(status);
CREATE INDEX IF NOT EXISTS idx_vendor_followups_vendor_tgl ON vendor_followups(vendor_id, created_at DESC);

COMMENT ON TABLE vendor_followups IS 'Riwayat tektok verifikator-penyedia soal kelengkapan dokumen registrasi vendor';

-- Saklar on/off untuk pengingat kelengkapan dokumen (reuse app_settings yang sudah ada dari
-- Kelompok F, pola sama seperti kunci 'notifikasi_dokumen_expired'). Default mati.
INSERT INTO app_settings (kunci, aktif, keterangan)
VALUES ('reminder_tindak_lanjut_vendor', false, 'Pengingat kelengkapan dokumen ke vendor yang belum menanggapi permintaan verifikator (dipicu manual oleh admin/approval_vms dari daftar antrian, sistem ini tidak punya cron otomatis)')
ON CONFLICT (kunci) DO NOTHING;
