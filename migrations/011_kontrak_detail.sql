-- Migrasi 011: Kontrak Detail (Termin Pembayaran, Sanksi, Progres Pekerjaan)
--
-- Mengikuti tabel contracting_payment, contracting_sanksi, dan contracting_deliverable
-- di sistem eProc lama. Sistem baru sebelumnya cuma bisa unggah SPK dan BAST, belum ada
-- pelacakan termin pembayaran, sanksi keterlambatan, atau progres pekerjaan bertahap.
--
-- Sengaja TIDAK termasuk "SLA" (contracting_sla) karena itu spesifik untuk kontrak jenis
-- layanan/maintenance saja, lingkupnya lebih sempit dibanding 3 hal di atas yang berlaku
-- untuk hampir semua jenis kontrak.
--
-- Aman dijalankan berulang kali (pakai IF NOT EXISTS).

CREATE TABLE IF NOT EXISTS contract_payment_terms (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  contract_id uuid NOT NULL REFERENCES contracts(id) ON DELETE CASCADE,
  term_name varchar(100) NOT NULL,
  amount numeric NOT NULL,
  progress_percent numeric,
  status varchar(20) NOT NULL DEFAULT 'belum_dibayar',
  payment_date date,
  bapp_file_path varchar(255),
  notes text,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
-- status: 'belum_dibayar' / 'diajukan' / 'dibayar'

CREATE TABLE IF NOT EXISTS contract_penalties (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  contract_id uuid NOT NULL REFERENCES contracts(id) ON DELETE CASCADE,
  days_late integer NOT NULL,
  penalty_rate varchar(50),
  work_value numeric,
  penalty_amount numeric,
  status varchar(20) NOT NULL DEFAULT 'aktif',
  notes text,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
-- status: 'aktif' / 'lunas' / 'dibatalkan'

CREATE TABLE IF NOT EXISTS contract_deliverables (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  contract_id uuid NOT NULL REFERENCES contracts(id) ON DELETE CASCADE,
  scope varchar(255),
  deliverable_name varchar(255) NOT NULL,
  progress_percent numeric NOT NULL DEFAULT 0,
  status varchar(20) NOT NULL DEFAULT 'proses',
  target_date date,
  received_date date,
  file_path varchar(255),
  notes text,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
-- status: 'proses' / 'selesai' / 'terlambat'
