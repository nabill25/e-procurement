-- Migrasi 029: Reset Password
-- Menambahkan mekanisme lupa password/reset password lewat email, yang sebelumnya sama
-- sekali tidak ada di sistem baru (tombol "Lupa Password?" di LoginModal cuma dekoratif,
-- tidak ada handler). Sistem lama punya alur ini (email/lupa_password.php, endpoint
-- reset_password di main/index.php).
--
-- Token disimpan sebagai hash SHA-256 (bukan token mentah) supaya kalau database bocor,
-- token yang ada di email orang tidak bisa dipakai langsung - pola umum untuk reset token.

ALTER TABLE users ADD COLUMN IF NOT EXISTS reset_token VARCHAR(64);
ALTER TABLE users ADD COLUMN IF NOT EXISTS reset_token_expiry TIMESTAMP;

CREATE INDEX IF NOT EXISTS idx_users_reset_token ON users(reset_token) WHERE reset_token IS NOT NULL;
