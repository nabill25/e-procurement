-- Migrasi 033: gelombang kedua akses menu untuk 3 role tambahan sisa (Admin VMS, Administrator
-- Approval, Perencanaan). Sesuai temuan riset ke data tbl_m_menu sistem lama (lihat CLAUDE.md):
--   ADMIN VMS             -> blacklist (sudah ada), vendor (hapus vendor + cetak SKT, baru
--                            ditambahkan di sesi ini), inbox (baca/balas pesan, versi belum
--                            selengkapnya sistem lama yang bisa broadcast survei/komplain)
--   ADMINISTRATOR APPROVAL-> user_management (ubah status aktif/nonaktif akun staf, baru
--                            ditambahkan di sesi ini - endpoint PATCH /api/users/:id/status)
--   PERENCANAAN           -> pengajuan (approval terpisah lewat mekanisme multi-approver yang
--                            sudah ada di backend sejak Kelompok E, UI baru ditambahkan di sesi
--                            ini - ApprovalPerencanaanSection di DetailPengajuanModal.jsx)

INSERT INTO menu_role_access (menu_id, role)
SELECT id, r.role
FROM menu_items, (VALUES
  ('admin_vms', 'blacklist'),
  ('admin_vms', 'vendor'),
  ('admin_vms', 'inbox'),
  ('administrator_approval', 'user_management'),
  ('perencanaan', 'pengajuan')
) AS r(role, menu_key)
WHERE menu_items.menu_key = r.menu_key
ON CONFLICT DO NOTHING;
