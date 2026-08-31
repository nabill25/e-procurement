-- Migrasi 030: Integrasi Oracle ERP (RKA, Purchase Requisition, Supplier, PO)
--
-- Mengikuti temuan audit paritas: sistem lama sinkron data anggaran (RKA) dan permintaan
-- pembelian (PR) MASUK dari Oracle ERP kampus, dan kirim data vendor (Supplier) serta
-- Purchase Order (PO) KELUAR ke Oracle. Mekanisme aslinya (dibaca langsung dari
-- eproc/application/libraries/libintegrationoracle.php dan models/integrate.php): file Excel
-- (.xlsx) "dititipkan" tim Oracle/keuangan ke folder di server SFTP, lalu Admin mengimpornya
-- lewat sistem. Field per baris di sini SENGAJA disederhanakan dari struktur asli Oracle EBS
-- yang sangat rinci (7 segmen COA, PJC Project/Task, dst) - cuma menyimpan field yang benar-benar
-- berguna untuk ditampilkan/dicari, sisanya (kalau ada) tersimpan sebagai jsonb "raw_data" supaya
-- tidak ada data yang hilang kalau suatu saat dibutuhkan detail lengkapnya.
--
-- PENTING: kredensial SFTP TIDAK disimpan di tabel manapun (mengikuti pola SMTP_* di server/.env),
-- cuma lewat environment variable ORACLE_SFTP_*, kosong secara default. Lihat server/lib/oracleIntegration.js.

-- Data RKA (Rencana Kerja Anggaran) yang diimpor dari Oracle - padanan tabel INTEGRATION_IMPORT_RKA_BUDGET
CREATE TABLE IF NOT EXISTS integration_rka_budget (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  rka_key VARCHAR(100),
  start_date_year INTEGER,
  segment1 VARCHAR(50),
  segment1_desc VARCHAR(255),
  segment2 VARCHAR(50),
  segment2_desc VARCHAR(255),
  budget_amt NUMERIC(18,2),
  remain_amt NUMERIC(18,2),
  raw_data JSONB,
  import_file VARCHAR(255),
  imported_by UUID REFERENCES users(id),
  imported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Data Purchase Requisition yang diimpor dari Oracle - padanan gabungan INTEGRATION_IMPORT_PR_HEADER
-- + _LINE (baris item disimpan sebagai jsonb array, bukan tabel terpisah - detail distribusi
-- anggaran per baris/COA tidak disimpan granular karena tidak dipakai fitur manapun di sistem baru)
CREATE TABLE IF NOT EXISTS integration_pr_import (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  requisition_number VARCHAR(100),
  description TEXT,
  bu_name VARCHAR(255),
  document_status VARCHAR(50),
  pr_type VARCHAR(50),
  metode_pengadaan VARCHAR(100),
  jenis_anggaran VARCHAR(100),
  nomor_rup VARCHAR(100),
  subdivisi VARCHAR(255),
  lines JSONB,
  import_file VARCHAR(255),
  imported_by UUID REFERENCES users(id),
  imported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Log setiap aktivitas integrasi (masuk maupun keluar) - padanan INTEGRATION_ORACLE_LOGS,
-- disatukan untuk kedua arah karena polanya sama (jenis + file + status + catatan)
CREATE TABLE IF NOT EXISTS integration_logs (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  jenis VARCHAR(30) NOT NULL CHECK (jenis IN ('rka_import','pr_import','supplier_export','po_export')),
  arah VARCHAR(10) NOT NULL CHECK (arah IN ('masuk','keluar')),
  file_name VARCHAR(255),
  status VARCHAR(20) NOT NULL DEFAULT 'sukses' CHECK (status IN ('sukses','gagal')),
  catatan TEXT,
  jumlah_baris INTEGER,
  created_by UUID REFERENCES users(id),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_integration_logs_jenis ON integration_logs(jenis);
CREATE INDEX IF NOT EXISTS idx_integration_rka_year ON integration_rka_budget(start_date_year);

-- Daftarkan menu baru ke sistem Hak Akses Menu (khusus admin), mengikuti pola menu lain
INSERT INTO menu_items (menu_key, label, icon, order_index)
VALUES ('integration_oracle', 'Integrasi Oracle', 'RefreshCw', 21)
ON CONFLICT (menu_key) DO NOTHING;

INSERT INTO menu_role_access (menu_id, role)
SELECT id, 'admin' FROM menu_items WHERE menu_key = 'integration_oracle'
ON CONFLICT DO NOTHING;
