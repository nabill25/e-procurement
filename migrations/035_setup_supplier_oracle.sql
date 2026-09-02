-- Migrasi 035: Setup Supplier Oracle
--
-- Menerapkan alur "Request Setup Supplier Oracle" dari project terpisah yang ditunjukkan
-- pengguna (folder root project setup-supplier-request/, sistem CodeIgniter 4 + Keycloak SSO
-- berdiri sendiri). BUKAN kredensial untuk fitur "Integrasi Oracle" yang sudah ada (itu soal
-- sinkronisasi data RKA/PR lewat SFTP) - ini modul BARU yang beda konsep: alur permintaan
-- SETUP supplier baru di Oracle EBS, berbasis tiket/kanban, dari staf Operating Unit sampai
-- tim support Oracle benar-benar mengerjakannya di Oracle EBS.
--
-- Alur: Pengaju (submit) -> Verifikator (baca & teruskan) -> Tim Support Oracle - Dispatcher
-- (terima, dispatch ke pelaksana atau ambil sendiri) -> Tim Support Oracle - Pelaksana
-- (kerjakan di Oracle EBS, upload bukti selesai).
--
-- Field form persis mengikuti rancangan aslinya (18 field wajib + 2 field tambahan opsional),
-- status pakai snake_case huruf kecil konsisten dengan konvensi sistem baru ini (rancangan
-- asli pakai UPPERCASE). 4 role baru ditambahkan mengikuti pola "10 role tambahan" yang sudah
-- established (migrations/014, 032, 033).
--
-- Aman dijalankan berulang kali (IF NOT EXISTS / ON CONFLICT DO NOTHING).

INSERT INTO role_definitions (role_key, label, description) VALUES
  ('pengaju_oracle',      'Pengaju Setup Supplier',        'Staf Operating Unit yang mengajukan permintaan setup supplier baru di Oracle EBS'),
  ('verifikator_oracle',  'Verifikator Setup Supplier',    'Memvalidasi permintaan setup supplier sebelum diteruskan ke tim Oracle'),
  ('dispatcher_oracle',   'Dispatcher Setup Supplier',     'Tim support Oracle - membagi tugas setup supplier ke pelaksana'),
  ('pelaksana_oracle',    'Pelaksana Setup Supplier',      'Tim support Oracle - mengerjakan setup supplier langsung di Oracle EBS')
ON CONFLICT (role_key) DO NOTHING;

CREATE TABLE IF NOT EXISTS oracle_supplier_requests (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  kode varchar(30) NOT NULL UNIQUE, -- format RSS-<tahun>-<5 digit>, dibuat aplikasi saat insert

  -- Field wajib (diisi Pengaju)
  operating_unit varchar(255) NOT NULL,
  nama_supplier varchar(255) NOT NULL,
  alamat_kantor text,
  no_telp varchar(50),
  nama_kontak varchar(255),
  jabatan varchar(100),
  no_pkp varchar(100),
  no_nib varchar(100),
  tgl_nib date,
  domisili varchar(255),
  npwp varchar(50),
  alamat_npwp text,
  nama_bank varchar(255),
  cabang_bank varchar(255),
  nama_rekening varchar(255),
  nomor_rekening varchar(100),
  mata_uang varchar(10) DEFAULT 'IDR',

  -- Field tambahan opsional (diisi Pengaju)
  nama_paket_rup varchar(255),
  kode_rup varchar(100),

  -- Status & state machine (lihat oracle_supplier_request_logs untuk riwayat lengkap)
  --   diajukan -> diverifikasi -> diteruskan -> didispatch -> dikerjakan -> selesai
  status varchar(20) NOT NULL DEFAULT 'diajukan',

  -- Diisi Verifikator (opsional)
  catatan_verifikator text,
  aktivasi_dari date,
  aktivasi_sampai date,

  -- Diisi Tim Support Oracle
  assigned_to uuid REFERENCES users(id), -- pelaksana yang ditugaskan (dispatch ke orang lain atau ambil sendiri)
  bukti_screenshot varchar(500), -- wajib diisi Pelaksana saat menandai selesai

  -- Jejak waktu tiap tahap (dipakai hitung "usia status" di kanban)
  created_by uuid NOT NULL REFERENCES users(id),
  submitted_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  verified_at timestamp,
  verified_by uuid REFERENCES users(id),
  forwarded_at timestamp,
  dispatched_at timestamp,
  dispatched_by uuid REFERENCES users(id),
  started_at timestamp,
  completed_at timestamp,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_oracle_supplier_status ON oracle_supplier_requests(status);
CREATE INDEX IF NOT EXISTS idx_oracle_supplier_created_by ON oracle_supplier_requests(created_by);
CREATE INDEX IF NOT EXISTS idx_oracle_supplier_assigned_to ON oracle_supplier_requests(assigned_to);

CREATE TABLE IF NOT EXISTS oracle_supplier_request_logs (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  request_id uuid NOT NULL REFERENCES oracle_supplier_requests(id) ON DELETE CASCADE,
  status varchar(20) NOT NULL,
  changed_by uuid REFERENCES users(id),
  changed_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  catatan text
);
CREATE INDEX IF NOT EXISTS idx_oracle_supplier_logs_request ON oracle_supplier_request_logs(request_id, changed_at ASC);

-- Menu baru + hak akses (admin selalu ikut ditambahkan eksplisit karena getDefaultAllowedMenus
-- di Sidebar.jsx cuma jaring pengaman kalau API gagal - akses sungguhan datang dari sini)
INSERT INTO menu_items (menu_key, label, icon, order_index, is_active)
SELECT 'oracle_supplier_setup', 'Setup Supplier Oracle', 'Landmark', 18, true
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE menu_key = 'oracle_supplier_setup');

INSERT INTO menu_role_access (menu_id, role)
SELECT id, r.role FROM menu_items, (VALUES
  ('admin'), ('pengaju_oracle'), ('verifikator_oracle'), ('dispatcher_oracle'), ('pelaksana_oracle')
) AS r(role)
WHERE menu_items.menu_key = 'oracle_supplier_setup'
ON CONFLICT DO NOTHING;
