-- Migrasi 037: Checklist Verifikasi Kelengkapan Berkas Vendor
--
-- Padanan REKANAN_CHECKLIST di eProc lama (dikonfirmasi AKTIF dipakai, bukan kode mati seperti
-- yang sempat salah dicatat di CLAUDE.md sebelumnya - fungsi updateChecklist()/updateChecklist2()
-- di rekanan_json.php dipanggil dari 18 halaman aktif views/main/daftar_rekanan_*.php, dan data
-- aslinya berisi catatan verifikator sungguhan). Di sistem lama: 1 baris per vendor, 19 kolom
-- boolean (npwp, nib, akta, pengurus, saham, sbu, rekening_koran, neraca, pkp, spt_tahunan,
-- pph, ppn, tenaga_ahli, pengalaman, peralatan, teknis_lain, pakta, cv, ktp) + 18 kolom catatan
-- pasangannya, di-checkbox satu-satu ("Ya, Lengkap" + catatan) per bagian dari 18 halaman
-- verifikasi berbeda.
--
-- Di sistem baru, disederhanakan mengikuti cara sistem baru mengelompokkan data kualifikasi
-- vendor (10 tab di halaman Profil & Kualifikasi, bukan 19 field terpisah seperti sistem
-- lama) - satu baris per bagian per vendor (bukan 1 baris lebar per vendor), supaya gampang
-- ditambah/dikurangi kalau tab berubah nanti, dan konsisten dengan pola
-- procurement_request_checklist yang sudah ada.

CREATE TABLE IF NOT EXISTS vendor_qualification_checklist (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  vendor_id uuid NOT NULL REFERENCES users(id), -- users.id, konsisten dengan vendor_documents/qualifications
  section varchar(50) NOT NULL, -- legalitas, pengalaman, pajak, pengurus, tenaga_ahli, peralatan, bank, neraca, bidang_usaha, rekening_koran
  is_complete boolean NOT NULL DEFAULT false,
  catatan text,
  checked_by uuid REFERENCES users(id),
  checked_at timestamp,
  UNIQUE (vendor_id, section)
);

CREATE INDEX IF NOT EXISTS idx_vendor_checklist_vendor ON vendor_qualification_checklist(vendor_id);
