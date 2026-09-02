-- =====================================================================
-- Fitur: Pencatatan Tindak Lanjut Kelengkapan Dokumen Penyedia
-- Tanggal rancangan: 2026-09-01
-- =====================================================================
--
-- Tabel ini mencatat bolak-balik (tektok) antara verifikator (checker VMS,
-- USER_TYPE_ID = 2) dan penyedia selama proses verifikasi dokumen registrasi:
-- verifikator minta kelengkapan berkas, penyedia melengkapi lalu konfirmasi,
-- verifikator cek ulang, begitu terus sampai dinyatakan terverifikasi.
--
-- Setiap baris = 1 kejadian. Status "sekarang" suatu berkas = kolom STATUS
-- pada baris paling baru untuk REKANAN_ID tersebut.
--
-- TIDAK mengubah tabel REKANAN yang sudah ada (murni tambah tabel baru, jadi
-- aman/additive). Kolom STATUS_VALIDASI di REKANAN tetap dipakai apa adanya
-- (0=Belum, 10=Tolak, 3=Rekomendator, 4=Validator, 1=Valid), tabel ini cuma
-- lapisan pelacakan komunikasi di atasnya.
--
-- Cara apply (jalankan SEBELUM kode PHP dipasang, sesuai aturan proyek):
--   psql -U <user> -d eproc_migrasi -f buat_tabel_rekanan_tindak_lanjut.sql
--
-- Aman dijalankan berkali-kali (IF NOT EXISTS + guard WHERE NOT EXISTS).

-- ---------------------------------------------------------------------
-- 1. Tabel utama: riwayat tindak lanjut
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS REKANAN_TINDAK_LANJUT (
    ID                    INTEGER PRIMARY KEY,
    REKANAN_ID            INTEGER NOT NULL REFERENCES REKANAN (REKANAN_ID),

    -- Status berkas SETELAH kejadian ini:
    --   PERLU_DILENGKAPI  verifikator minta kelengkapan (bola di penyedia)
    --   SUDAH_DILENGKAPI  penyedia sudah konfirmasi lengkap (bola di verifikator)
    --   TERVERIFIKASI     verifikator menyatakan dokumen sudah lengkap/oke
    STATUS                VARCHAR(20) NOT NULL,

    -- Jenis kejadian yang menghasilkan baris ini:
    --   PERMINTAAN  verifikator kirim catatan minta lengkapi
    --   KONFIRMASI  penyedia klik "sudah saya lengkapi"
    --   REMINDER    email pengingat otomatis dari sistem (cron)
    --   SELESAI     verifikator menandai terverifikasi
    JENIS                 VARCHAR(20) NOT NULL,

    CATATAN               TEXT,

    -- Pihak yang menimbulkan baris ini: VERIFIKATOR / PENYEDIA / SISTEM
    PIHAK                 VARCHAR(20) NOT NULL,

    CREATED_BY            INTEGER,        -- USER_LOGIN_ID (NULL kalau PIHAK = SISTEM)
    CREATED_DATE          TIMESTAMP NOT NULL DEFAULT NOW(),

    -- Jejak email notifikasi untuk baris ini (buat audit + kalau perlu kirim ulang)
    EMAIL_TUJUAN          VARCHAR(255),
    EMAIL_TERKIRIM        BOOLEAN NOT NULL DEFAULT FALSE,
    EMAIL_TERKIRIM_DATE   TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_rekanan_tindak_lanjut_rekanan_id
    ON REKANAN_TINDAK_LANJUT (REKANAN_ID);

CREATE INDEX IF NOT EXISTS idx_rekanan_tindak_lanjut_status
    ON REKANAN_TINDAK_LANJUT (STATUS);

CREATE INDEX IF NOT EXISTS idx_rekanan_tindak_lanjut_rekanan_tgl
    ON REKANAN_TINDAK_LANJUT (REKANAN_ID, CREATED_DATE DESC);

COMMENT ON TABLE REKANAN_TINDAK_LANJUT IS 'Riwayat tektok verifikator-penyedia soal kelengkapan dokumen registrasi';

-- ---------------------------------------------------------------------
-- 2. Saklar on/off untuk email pengingat otomatis (cron)
--    Meniru pola MASTER_PENGATURAN yang sudah dipakai fitur
--    "notif dokumen expired" (cronjobs_notif_dokexpired).
--    Baris ini dibaca oleh cronjobs_reminder_kelengkapan.
--    Default 'n' (mati) supaya tidak langsung jalan sebelum disetujui.
-- ---------------------------------------------------------------------
INSERT INTO MASTER_PENGATURAN (ID, URL, AKTIF, KETERANGAN, CREATED_DATE)
SELECT
    (SELECT COALESCE(MAX(ID), 0) + 1 FROM MASTER_PENGATURAN),
    'cronjobs_reminder_kelengkapan',
    'n',
    'Saklar email pengingat otomatis ke penyedia yang belum melengkapi dokumen. Isi y untuk mengaktifkan.',
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM MASTER_PENGATURAN WHERE URL = 'cronjobs_reminder_kelengkapan'
);
