const { pool } = require('./db');
const bcrypt = require('bcrypt');

async function seedRealUsers() {
  console.log('Menjalankan seeder akun resmi...');
  try {
    // 1. Kosongkan data pengguna lama (hati-hati, cascade delete akan menghapus data tender/vendor)
    // Karena ini environment baru di Supabase, hapus semua users agar bersih.
    await pool.query('ALTER TABLE users ADD COLUMN IF NOT EXISTS email VARCHAR(255);');
    await pool.query('DELETE FROM users');

    const users = [
      {
        username: 'admin@ui.ac.id',
        password: 'UIAdmin2026!',
        full_name: 'Administrator Pusat',
        role: 'admin',
        role_label: 'Administrator',
        email: 'admin@ui.ac.id'
      },
      {
        username: 'ppk@ui.ac.id',
        password: 'UIPPK2026!',
        full_name: 'Pejabat Pembuat Komitmen',
        role: 'ppk',
        role_label: 'Pejabat Pembuat Komitmen',
        email: 'ppk@ui.ac.id'
      },
      {
        username: 'pokja@ui.ac.id',
        password: 'UIPokja2026!',
        full_name: 'Kelompok Kerja ULP',
        role: 'pokja',
        role_label: 'Pokja Pemilihan',
        email: 'pokja@ui.ac.id'
      },
      {
        username: 'vendor@gmail.com',
        password: 'UIVendor2026!',
        full_name: 'PT Vendor Contoh',
        role: 'vendor',
        role_label: 'Vendor / Penyedia',
        email: 'vendor@gmail.com'
      }
    ];

    for (const u of users) {
      const hash = await bcrypt.hash(u.password, 10);
      const res = await pool.query(
        `INSERT INTO users (username, password, full_name, role, role_label, email, status)
         VALUES ($1, $2, $3, $4, $5, $6, 'aktif') RETURNING id`,
        [u.username, hash, u.full_name, u.role, u.role_label, u.email]
      );
      
      const userId = res.rows[0].id;
      
      // Jika vendor, masukkan juga ke tabel vendors
      if (u.role === 'vendor') {
        await pool.query(
          `INSERT INTO vendors (user_id, company_name, npwp, company_type, city, status)
           VALUES ($1, $2, $3, $4, $5, $6)`,
          [userId, u.full_name, '01.234.567.8-901.000', 'PT', 'Jakarta', 'verified']
        );
      }
      
      console.log(`✅ User ${u.username} berhasil dibuat.`);
    }

    console.log('Selesai membuat 4 akun resmi.');
  } catch (err) {
    console.error('❌ Gagal menyuntikkan user:', err);
  } finally {
    pool.end();
  }
}

seedRealUsers();
