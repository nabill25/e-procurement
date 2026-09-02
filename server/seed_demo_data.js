// ═══════════════════════════════════════════════════════════════════════════
// seed_demo_data.js - Isi seluruh modul sistem dengan data contoh yang saling
// terhubung, supaya alur proses bisnis & alur logika aplikasi bisa benar-benar
// dilihat/dicoba di browser (bukan cuma kerangka kosong).
//
// BEDA dari skrip migrasi/testing sekali-pakai yang biasa dipakai project ini:
// data yang dibuat skrip ini SENGAJA DIBIARKAN (tidak dibersihkan otomatis),
// karena memang itu yang diminta - untuk dilihat-lihat. Jalankan lewat:
//   npm run seed:demo   (dari folder root)
// Aman dijalankan berkali-kali (memakai kode/nomor otomatis dari aplikasi,
// jadi tiap run akan menambah data baru, bukan menimpa - kalau mau reset
// total, hapus manual dulu lewat halaman masing-masing atau minta dibuatkan
// skrip pembersih terpisah).
//
// Backend (npm run server, port 3001) HARUS sudah menyala sebelum skrip ini
// dijalankan - skrip ini murni memanggil API sungguhan (bukan INSERT SQL
// langsung) untuk sebagian besar alur bisnis, supaya validasi & logika
// otomatis (hitung ulang total, activity log, dst) ikut jalan persis seperti
// kalau dipakai manusia lewat form. Untuk data referensi murni (master data,
// dst) dipakai INSERT SQL langsung karena lebih cepat dan tidak ada logika
// bisnis yang perlu diuji di situ.
// ═══════════════════════════════════════════════════════════════════════════

require('dotenv').config({ path: require('path').join(__dirname, '.env') });
const { Pool } = require('pg');

const pool = new Pool({ connectionString: process.env.SUPABASE_DB_URL, ssl: { rejectUnauthorized: false } });
const BASE = 'http://localhost:3001/api';
const DEMO_PASSWORD = 'Demo2026!';

// ── Helper HTTP ──────────────────────────────────────────────────────────────
// Retry sekali kalau gagal di LEVEL JARINGAN (fetch gagal total, bukan respons HTTP error
// biasa) - ditemukan lewat percobaan pertama skrip ini kadang kena hiccup transient saat
// banyak request beruntun ke server lokal. Tidak retry untuk error HTTP 4xx/5xx sungguhan
// (itu error aplikasi yang nyata, bukan masalah jaringan, retry cuma akan mengulang error yang sama).
async function api(method, path, { token, body, form } = {}, _attempt = 1) {
  const headers = {};
  if (token) headers.Authorization = `Bearer ${token}`;
  let payload;
  if (form) {
    payload = form;
  } else if (body !== undefined) {
    headers['Content-Type'] = 'application/json';
    payload = JSON.stringify(body);
  }
  let res;
  try {
    res = await fetch(`${BASE}${path}`, { method, headers, body: payload });
  } catch (netErr) {
    if (_attempt < 3) {
      await new Promise(r => setTimeout(r, 500 * _attempt));
      return api(method, path, { token, body, form }, _attempt + 1);
    }
    throw new Error(`${method} ${path} -> gagal terhubung ke server: ${netErr.message}`);
  }
  let json = null;
  try { json = await res.json(); } catch { /* respons bukan JSON (mis. file export) */ }
  if (!res.ok) {
    throw new Error(`${method} ${path} -> HTTP ${res.status}: ${json?.message || res.statusText}`);
  }
  return json;
}
const get   = (path, token)          => api('GET', path, { token });
const post  = (path, body, token)    => api('POST', path, { token, body });
const patch = (path, body, token)    => api('PATCH', path, { token, body });
const put   = (path, body, token)    => api('PUT', path, { token, body });
const del   = (path, token)          => api('DELETE', path, { token });
const postForm  = (path, form, token)  => api('POST', path, { token, form });
const patchForm = (path, form, token)  => api('PATCH', path, { token, form });

function form(fields = {}, files = {}) {
  const fd = new FormData();
  for (const [k, v] of Object.entries(fields)) {
    if (v === undefined || v === null) continue;
    fd.append(k, String(v));
  }
  for (const [field, { blob, filename }] of Object.entries(files)) {
    fd.append(field, blob, filename);
  }
  return fd;
}

const TINY_PNG_BASE64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';
function docBlob(text) {
  return new Blob([text || 'Dokumen contoh data demo sistem e-Procurement DPBJ UI.\n'], { type: 'application/pdf' });
}
function imgBlob() {
  return new Blob([Buffer.from(TINY_PNG_BASE64, 'base64')], { type: 'image/png' });
}
async function loginAs(username, password = DEMO_PASSWORD) {
  const res = await post('/auth/login', { username, password });
  return res.token;
}

async function phase(label, fn, ctx) {
  process.stdout.write(`\n▶ ${label} ... `);
  try {
    await fn(ctx);
    console.log('OK');
  } catch (err) {
    console.log('GAGAL:', err.message);
  }
}

// ═══════════════════════════════════════════════════════════════════════════
// FASE 1: Bersihkan 1 baris data uji lama yang ketinggalan (dicatat di
// CLAUDE.md sebagai belum dibersihkan dari sesi audit sebelumnya)
// ═══════════════════════════════════════════════════════════════════════════
async function cleanupStray() {
  await pool.query(`DELETE FROM unit_kerja_master WHERE nama ILIKE 'Unit Audit Test%'`);
}

// ═══════════════════════════════════════════════════════════════════════════
// FASE 2: Login admin
// ═══════════════════════════════════════════════════════════════════════════
async function loginAdmin(ctx) {
  ctx.adminToken = await loginAs('admin@ui.ac.id', 'UIAdmin2026!');
  ctx.ppkToken   = await loginAs('ppk@ui.ac.id', 'UIPPK2026!');
  ctx.pokjaToken = await loginAs('pokja@ui.ac.id', 'UIPokja2026!');
  const me = await get('/auth/me', ctx.adminToken);
  ctx.adminId = me.user.id;
  const mePpk = await get('/auth/me', ctx.ppkToken);
  ctx.ppkId = mePpk.user.id;
  const mePokja = await get('/auth/me', ctx.pokjaToken);
  ctx.pokjaId = mePokja.user.id;
}

// ═══════════════════════════════════════════════════════════════════════════
// FASE 3: Data Master - lengkapi kategori yang masih kosong + data pendukung
// ═══════════════════════════════════════════════════════════════════════════
async function seedMasterData(ctx) {
  const cat = async (category, items) => {
    for (const it of items) {
      await pool.query(
        `INSERT INTO master_data (category, kode, nama) VALUES ($1,$2,$3)
         ON CONFLICT DO NOTHING`,
        [category, it.kode || null, it.nama]
      ).catch(() => {}); // kalau tabel tidak punya unique constraint, cukup skip error duplikat manual
    }
  };
  // Cek dulu supaya tidak dobel kalau sudah pernah di-seed run sebelumnya
  const existing = async (category) => {
    const r = await pool.query('SELECT nama FROM master_data WHERE category=$1', [category]);
    return new Set(r.rows.map(x => x.nama));
  };
  const seedIfEmpty = async (category, items) => {
    const have = await existing(category);
    const toInsert = items.filter(it => !have.has(it.nama));
    for (const it of toInsert) {
      await pool.query('INSERT INTO master_data (category, kode, nama) VALUES ($1,$2,$3)', [category, it.kode || null, it.nama]);
    }
  };

  await seedIfEmpty('bank', [
    { kode: 'BCA', nama: 'Bank Central Asia (BCA)' },
    { kode: 'BNI', nama: 'Bank Negara Indonesia (BNI)' },
    { kode: 'BRI', nama: 'Bank Rakyat Indonesia (BRI)' },
    { kode: 'MANDIRI', nama: 'Bank Mandiri' },
    { kode: 'BSI', nama: 'Bank Syariah Indonesia (BSI)' },
  ]);
  await seedIfEmpty('mata_uang', [
    { kode: 'IDR', nama: 'Rupiah Indonesia (IDR)' },
    { kode: 'USD', nama: 'Dolar Amerika Serikat (USD)' },
    { kode: 'EUR', nama: 'Euro (EUR)' },
  ]);
  await seedIfEmpty('negara', [
    { kode: 'ID', nama: 'Indonesia' },
    { kode: 'SG', nama: 'Singapura' },
    { kode: 'MY', nama: 'Malaysia' },
    { kode: 'JP', nama: 'Jepang' },
  ]);
  await seedIfEmpty('satuan', [
    { kode: 'PCS', nama: 'Pcs' }, { kode: 'UNIT', nama: 'Unit' }, { kode: 'SET', nama: 'Set' },
    { kode: 'PAKET', nama: 'Paket' }, { kode: 'M2', nama: 'Meter Persegi' }, { kode: 'BLN', nama: 'Bulan' },
  ]);
  await seedIfEmpty('incoterm', [
    { kode: 'EXW', nama: 'EXW - Ex Works' }, { kode: 'FOB', nama: 'FOB - Free on Board' }, { kode: 'DDP', nama: 'DDP - Delivered Duty Paid' },
  ]);
  await seedIfEmpty('payment_method', [
    { nama: 'Transfer Bank' }, { nama: 'Termin Bertahap' }, { nama: 'Lunas di Muka' },
  ]);
  await seedIfEmpty('rekanan_tipe', [
    { nama: 'Perusahaan Perorangan' }, { nama: 'CV' }, { nama: 'PT' }, { nama: 'Koperasi' }, { nama: 'BUMN/BUMD' },
  ]);
  await seedIfEmpty('sertifikat_jenis', [
    { nama: 'ISO 9001' }, { nama: 'ISO 14001' }, { nama: 'SBU Konstruksi' }, { nama: 'K3 Konstruksi' },
  ]);
  await seedIfEmpty('jenis_belanja', [
    { nama: 'Belanja Modal' }, { nama: 'Belanja Barang' }, { nama: 'Belanja Jasa' },
  ]);
  await seedIfEmpty('analisa_kategori', [
    { nama: 'Kategori Strategis' }, { nama: 'Kategori Rutin' }, { nama: 'Kategori Mendesak' },
  ]);
  await seedIfEmpty('ijin_usaha', [
    { nama: 'SIUP' }, { nama: 'NIB' }, { nama: 'IUJK' },
  ]);
  await seedIfEmpty('pendidikan', [
    { nama: 'SMA/SMK' }, { nama: 'D3' }, { nama: 'S1' }, { nama: 'S2' },
  ]);
  await seedIfEmpty('direktorat', [
    { nama: 'Direktorat Pengadaan Barang dan Jasa' }, { nama: 'Direktorat Fasilitas' }, { nama: 'Direktorat Keuangan' },
  ]);

  // Unit kerja (tabel sendiri)
  const unitExisting = await pool.query('SELECT nama FROM unit_kerja_master');
  const unitHave = new Set(unitExisting.rows.map(r => r.nama));
  const units = [
    { kode: 'FT', nama: 'Fakultas Teknik' },
    { kode: 'FEB', nama: 'Fakultas Ekonomi dan Bisnis' },
    { kode: 'FK', nama: 'Fakultas Kedokteran' },
    { kode: 'FASILUM', nama: 'Direktorat Fasilitas Umum' },
    { kode: 'DPBJ', nama: 'Direktorat Pengadaan Barang dan Jasa' },
  ];
  for (const u of units) {
    if (unitHave.has(u.nama)) continue;
    await pool.query('INSERT INTO unit_kerja_master (kode, nama, alamat, telepon, email) VALUES ($1,$2,$3,$4,$5)',
      [u.kode, u.nama, `Kampus UI Depok, Gedung ${u.kode}`, '021-7867222', `${u.kode.toLowerCase()}@ui.ac.id`]);
  }
  const unitRow = await pool.query(`SELECT id, nama FROM unit_kerja_master WHERE nama = 'Fakultas Teknik' LIMIT 1`);
  ctx.unitFT = unitRow.rows[0]?.nama || 'Fakultas Teknik';

  // Katalog kategori
  const katExisting = await pool.query('SELECT nama FROM katalog_categories');
  if (katExisting.rows.length === 0) {
    const parents = [
      { nama: 'Elektronik & Komputer', kode: 'ELK' },
      { nama: 'Alat Tulis Kantor', kode: 'ATK' },
      { nama: 'Material Bangunan', kode: 'MTB' },
    ];
    for (const p of parents) {
      await pool.query('INSERT INTO katalog_categories (nama, kode) VALUES ($1,$2)', [p.nama, p.kode]);
    }
  }
  const katRows = await pool.query('SELECT id, nama FROM katalog_categories');
  ctx.katCategories = katRows.rows;

  // Template dokumen
  const dtExisting = await pool.query('SELECT id FROM document_templates LIMIT 1');
  if (!dtExisting.rows.length) {
    await pool.query(`INSERT INTO document_templates (target, nama, keterangan) VALUES
      ('internal', 'Template Berita Acara Evaluasi', 'Format standar BAHP untuk Pokja'),
      ('rekanan', 'Template Surat Pernyataan Minat', 'Format surat pernyataan minat mengikuti tender')`);
  }

  // Hari libur nasional 2026 (contoh sebagian)
  const holExisting = await pool.query('SELECT id FROM holidays LIMIT 1');
  if (!holExisting.rows.length) {
    await pool.query(`INSERT INTO holidays (tanggal, keterangan) VALUES
      ('2026-01-01', 'Tahun Baru Masehi'),
      ('2026-08-17', 'Hari Kemerdekaan RI'),
      ('2026-12-25', 'Hari Raya Natal')`);
  }

  // Master checklist (untuk fitur RUP)
  const chkExisting = await pool.query('SELECT id FROM master_checklist LIMIT 1');
  if (!chkExisting.rows.length) {
    await pool.query(`INSERT INTO master_checklist (nama, wajib) VALUES
      ('Kerangka Acuan Kerja (KAK)', true),
      ('Rencana Anggaran Biaya (RAB)', true),
      ('Spesifikasi Teknis', true),
      ('Dokumentasi Survei Harga Pasar', false)`);
  }

  // Template penilaian kinerja (berjenjang bab/pasal)
  const pkExisting = await pool.query('SELECT id FROM penilaian_kinerja_templates LIMIT 1');
  if (!pkExisting.rows.length) {
    const bab1 = await pool.query(`INSERT INTO penilaian_kinerja_templates (kode, nama, bobot_persen) VALUES ('I', 'Kualitas Pekerjaan', 40) RETURNING id`);
    const bab2 = await pool.query(`INSERT INTO penilaian_kinerja_templates (kode, nama, bobot_persen) VALUES ('II', 'Ketepatan Waktu', 30) RETURNING id`);
    const bab3 = await pool.query(`INSERT INTO penilaian_kinerja_templates (kode, nama, bobot_persen) VALUES ('III', 'Komunikasi & Responsivitas', 30) RETURNING id`);
    await pool.query(`INSERT INTO penilaian_kinerja_templates (parent_id, kode, nama, skor_maksimal) VALUES
      ($1, 'I.1', 'Kesesuaian spesifikasi teknis', 100),
      ($2, 'II.1', 'Ketepatan jadwal penyelesaian', 100),
      ($3, 'III.1', 'Kecepatan menanggapi komunikasi', 100)`,
      [bab1.rows[0].id, bab2.rows[0].id, bab3.rows[0].id]);
  }

  // Inbox complain recipient (kalau belum ada)
  const rcpt = await pool.query('SELECT id FROM inbox_complain_recipients LIMIT 1');
  if (!rcpt.rows.length) {
    await pool.query(`INSERT INTO inbox_complain_recipients (email, keterangan) VALUES ('pengaduan@dpbj.ui.ac.id', 'Tim Pengaduan DPBJ')`);
  }
}

// ═══════════════════════════════════════════════════════════════════════════
// FASE 4: Akun staf untuk 14 role tambahan (supaya bisa login & lihat tiap
// sudut pandang role) + 2 akun Pokja/PPK tambahan untuk variasi penanggung
// jawab tender.
// ═══════════════════════════════════════════════════════════════════════════
const STAFF_ROLES = [
  { role_key: 'admin_vms', full_name: 'Rania Kusuma (Admin VMS)', username: 'admin_vms_demo' },
  { role_key: 'administrator_approval', full_name: 'Doni Prasetyo (Administrator Approval)', username: 'administrator_approval_demo' },
  { role_key: 'approval_vms', full_name: 'Siti Nur Aini (Approval VMS)', username: 'approval_vms_demo' },
  { role_key: 'audit', full_name: 'Bagus Wirawan (Audit)', username: 'audit_demo' },
  { role_key: 'dispatcher_oracle', full_name: 'Fajar Setiadi (Dispatcher Oracle)', username: 'dispatcher_oracle_demo' },
  { role_key: 'kasubdit_kontrak', full_name: 'Herlina Wati (Kasubdit Kontrak)', username: 'kasubdit_kontrak_demo' },
  { role_key: 'manager_pengadaan', full_name: 'Arief Hidayat (Manager Pengadaan)', username: 'manager_pengadaan_demo' },
  { role_key: 'pelaksana_oracle', full_name: 'Yoga Pratama (Pelaksana Oracle)', username: 'pelaksana_oracle_demo' },
  { role_key: 'pelaksana_pengadaan', full_name: 'Nadia Rahmawati (Pelaksana Pengadaan)', username: 'pelaksana_pengadaan_demo' },
  { role_key: 'pengaju_oracle', full_name: 'Wahyu Nugroho (Pengaju Oracle)', username: 'pengaju_oracle_demo' },
  { role_key: 'pengelola_kontrak', full_name: 'Citra Dewi (Pengelola Kontrak)', username: 'pengelola_kontrak_demo' },
  { role_key: 'pengguna', full_name: 'Budi Santoso (Pengguna)', username: 'pengguna_demo' },
  { role_key: 'perencanaan', full_name: 'Maya Anggraini (Perencanaan)', username: 'perencanaan_demo' },
  { role_key: 'verifikator_oracle', full_name: 'Reza Firmansyah (Verifikator Oracle)', username: 'verifikator_oracle_demo' },
];

async function seedStaffAccounts(ctx) {
  ctx.staff = {};
  const existingUsers = await pool.query('SELECT username, id FROM users');
  const usernameMap = new Map(existingUsers.rows.map(r => [r.username, r.id]));

  for (const s of STAFF_ROLES) {
    let userId = usernameMap.get(s.username);
    if (!userId) {
      const created = await post('/users', {
        username: s.username, password: DEMO_PASSWORD, full_name: s.full_name,
        email: `${s.username}@ui.ac.id`, role_key: s.role_key,
      }, ctx.adminToken);
      userId = created.data.id;
    }
    const token = await loginAs(s.username);
    const me = await get('/auth/me', token);
    ctx.staff[s.role_key] = { id: me.user.id, username: s.username, token, full_name: s.full_name };
  }
}

// ═══════════════════════════════════════════════════════════════════════════
// FASE 5: Vendor baru dengan berbagai status + kualifikasi lengkap
// ═══════════════════════════════════════════════════════════════════════════
async function registerVendorIfMissing(company_name, npwp, email, username, company_type) {
  const existing = await pool.query('SELECT u.id AS user_id FROM users u WHERE u.username = $1', [username]);
  if (existing.rows.length) return existing.rows[0].user_id;
  await post('/auth/register', { company_name, npwp, email, password: DEMO_PASSWORD, username, company_type });
  const r = await pool.query('SELECT id FROM users WHERE username = $1', [username]);
  return r.rows[0].id;
}

async function seedVendors(ctx) {
  ctx.vendors = {};

  // 1. PT Mitra Konstruksi Nusantara - akan jadi pemenang tender utama (hero)
  const mkUserId = await registerVendorIfMissing(
    'PT Mitra Konstruksi Nusantara', '11.234.567.8-901.000', 'kontak@mitrakonstruksi.co.id',
    'mitra_konstruksi', 'PT'
  );
  ctx.vendors.mitraKonstruksi = { userId: mkUserId, name: 'PT Mitra Konstruksi Nusantara', username: 'mitra_konstruksi' };

  // 2. CV Sinar Abadi Teknik - peserta kalah, ajukan sanggah
  const saUserId = await registerVendorIfMissing(
    'CV Sinar Abadi Teknik', '02.345.678.9-012.000', 'kontak@sinarabadi.co.id',
    'sinar_abadi', 'CV'
  );
  ctx.vendors.sinarAbadi = { userId: saUserId, name: 'CV Sinar Abadi Teknik', username: 'sinar_abadi' };

  // 3. PT Cipta Data Solusi - masih pending verifikasi
  const cdUserId = await registerVendorIfMissing(
    'PT Cipta Data Solusi', '03.456.789.0-123.000', 'halo@ciptadata.co.id',
    'cipta_data', 'PT'
  );
  ctx.vendors.ciptaData = { userId: cdUserId, name: 'PT Cipta Data Solusi', username: 'cipta_data' };

  // 4. UD Berkah Jaya Material - ditangguhkan, ada tindak lanjut terbuka
  const bjUserId = await registerVendorIfMissing(
    'UD Berkah Jaya Material', '04.567.890.1-234.000', 'berkahjaya@gmail.com',
    'berkah_jaya', 'Perusahaan Perorangan'
  );
  ctx.vendors.berkahJaya = { userId: bjUserId, name: 'UD Berkah Jaya Material', username: 'berkah_jaya' };

  // 5. PT Global Sukses Abadi - akan diblokir/blacklist
  const gsUserId = await registerVendorIfMissing(
    'PT Global Sukses Abadi', '05.678.901.2-345.000', 'info@globalsukses.co.id',
    'global_sukses', 'PT'
  );
  ctx.vendors.globalSukses = { userId: gsUserId, name: 'PT Global Sukses Abadi', username: 'global_sukses' };

  // Ambil vendors.id (PK tabel vendors) untuk tiap vendor - dibutuhkan endpoint verify/suspend/block
  for (const key of Object.keys(ctx.vendors)) {
    const v = ctx.vendors[key];
    const r = await pool.query('SELECT id FROM vendors WHERE user_id = $1', [v.userId]);
    v.vendorId = r.rows[0].id;
    v.token = await loginAs(v.username);
  }

  // ── Lengkapi kualifikasi PT Mitra Konstruksi Nusantara (paling lengkap, dia pemenang) ──
  const mk = ctx.vendors.mitraKonstruksi;
  await put(`/vendors/${mk.userId}/profile`, {
    pajak: [{ jenis: 'SPT Tahunan 2025', nomor: 'SPT-2025-001', tanggal: '2025-03-20' }],
    tenaga_ahli: [
      { nama: 'Ir. Bambang Kurniawan', jabatan: 'Site Manager', pendidikan: 'S1 Teknik Sipil', pengalaman_tahun: 12 },
      { nama: 'Dewi Anjani, S.T.', jabatan: 'Pengawas Lapangan', pendidikan: 'S1 Teknik Arsitektur', pengalaman_tahun: 8 },
    ],
    peralatan: [
      { nama: 'Excavator Mini', jumlah: 2, kepemilikan: 'Milik Sendiri' },
      { nama: 'Concrete Mixer', jumlah: 3, kepemilikan: 'Sewa' },
    ],
    pengurus: [
      { nama: 'H. Suryanto Wijaya', jabatan: 'Direktur Utama', ktp: '3175010101800001' },
    ],
    bank: [{ nama_bank: 'Bank Mandiri', nomor_rekening: '1270012345678', atas_nama: 'PT Mitra Konstruksi Nusantara' }],
    neraca: [{ tahun: 2025, aset: 4500000000, kewajiban: 1200000000, modal: 3300000000 }],
  }, mk.token);
  await postForm(`/vendors/${mk.userId}/documents`,
    form({ doc_type: 'Akta Pendirian', doc_number: 'AKTA-021/2015', issue_date: '2015-04-10' }, { document: { blob: docBlob(), filename: 'akta-pendirian.pdf' } }),
    mk.token);
  await postForm(`/vendors/${mk.userId}/documents`,
    form({ doc_type: 'NIB', doc_number: 'NIB-8812002731', issue_date: '2019-06-01', expiry_date: '2026-10-15' }, { document: { blob: docBlob(), filename: 'nib.pdf' } }),
    mk.token);
  await post(`/vendors/${mk.userId}/experiences`, {
    project_name: 'Renovasi Gedung Rektorat Universitas Trisakti', client_name: 'Universitas Trisakti',
    contract_value: 2800000000, start_date: '2023-02-01', end_date: '2023-11-30',
  }, mk.token);
  await post(`/vendors/${mk.userId}/experiences`, {
    project_name: 'Pembangunan Laboratorium Terpadu Politeknik Negeri Jakarta', client_name: 'Politeknik Negeri Jakarta',
    contract_value: 1950000000, start_date: '2022-03-01', end_date: '2022-12-15',
  }, mk.token);
  // Bidang usaha konstruksi
  const buKonstruksi = await pool.query(`SELECT id FROM bidang_usaha WHERE nama ILIKE '%Bangunan Gedung%' AND parent_id IS NOT NULL LIMIT 1`);
  if (buKonstruksi.rows.length) {
    await post(`/vendors/${mk.userId}/bidang-usaha`, { bidang_usaha_id: buKonstruksi.rows[0].id }, mk.token);
    ctx.buKonstruksiId = buKonstruksi.rows[0].id;
  }
  await postForm(`/vendors/${mk.userId}/rekening-koran`,
    form({ nomor_rekening: '1270012345678', nama_bank: 'Bank Mandiri', bulan: 12, tahun: 2025, nilai: 850000000 }, { file: { blob: docBlob(), filename: 'rekening-koran-des2025.pdf' } }),
    mk.token);
  await patch(`/vendors/${mk.vendorId}/verify`, { verified_by: ctx.adminId }, ctx.adminToken);

  // ── CV Sinar Abadi Teknik - verifikasi juga (biar bisa ikut tender), kualifikasi lebih sederhana ──
  const sa = ctx.vendors.sinarAbadi;
  await put(`/vendors/${sa.userId}/profile`, {
    tenaga_ahli: [{ nama: 'Rudi Hartanto', jabatan: 'Pelaksana', pendidikan: 'D3 Teknik Sipil', pengalaman_tahun: 5 }],
    peralatan: [{ nama: 'Dump Truck', jumlah: 1, kepemilikan: 'Sewa' }],
    bank: [{ nama_bank: 'BCA', nomor_rekening: '0981234567', atas_nama: 'CV Sinar Abadi Teknik' }],
  }, sa.token);
  await postForm(`/vendors/${sa.userId}/documents`,
    form({ doc_type: 'NIB', doc_number: 'NIB-7723004512', issue_date: '2020-01-15' }, { document: { blob: docBlob(), filename: 'nib.pdf' } }),
    sa.token);
  await patch(`/vendors/${sa.vendorId}/verify`, { verified_by: ctx.adminId }, ctx.adminToken);

  // ── PT Cipta Data Solusi - dibiarkan pending (belum diverifikasi), tapi sudah isi sebagian dokumen ──
  const cd = ctx.vendors.ciptaData;
  await postForm(`/vendors/${cd.userId}/documents`,
    form({ doc_type: 'NIB', doc_number: 'NIB-8891002233', issue_date: '2024-05-20' }, { document: { blob: docBlob(), filename: 'nib.pdf' } }),
    cd.token);

  // ── UD Berkah Jaya Material - ditangguhkan + ada catatan tindak lanjut terbuka ──
  const bj = ctx.vendors.berkahJaya;
  await patch(`/vendors/${bj.vendorId}/suspend`, { reason: 'Dokumen legalitas belum lengkap, menunggu kelengkapan NPWP terbaru.' }, ctx.adminToken);

  // ── PT Global Sukses Abadi - verifikasi dulu baru diblokir (supaya masuk daftar hitam dengan riwayat wajar) ──
  const gs = ctx.vendors.globalSukses;
  await patch(`/vendors/${gs.vendorId}/verify`, { verified_by: ctx.adminId }, ctx.adminToken);
  await patch(`/vendors/${gs.vendorId}/block`, { reason: 'Wanprestasi pada kontrak sebelumnya, tidak menyelesaikan pekerjaan sesuai jadwal.' }, ctx.adminToken);

  // ── Vendor Retail ──
  const retailExisting = await pool.query('SELECT id FROM vendor_retail LIMIT 1');
  if (!retailExisting.rows.length) {
    await post('/vendors/retail', {
      tipe: 'Toko ATK', nama: 'Toko ATK Sejahtera', npwp: '06.789.012.3-456.000',
      telepon: '021-8899001', kota: 'Depok', kontak_person: 'Ibu Ratna', kontak_person_hp: '081234567890',
      alamat: 'Jl. Margonda Raya No. 100, Depok',
    }, ctx.adminToken);
  }

  // Pastikan vendor lama "PT Vendor Contoh" (sudah terverifikasi sejak awal) ikut dipakai
  const vc = await pool.query(`SELECT v.id AS vendor_id, v.user_id FROM vendors v JOIN users u ON u.id=v.user_id WHERE u.username='vendor@gmail.com' OR u.email='vendor@gmail.com' LIMIT 1`);
  if (vc.rows.length) {
    ctx.vendors.vendorContoh = { userId: vc.rows[0].user_id, vendorId: vc.rows[0].vendor_id, name: 'PT Vendor Contoh', token: await loginAs('vendor@gmail.com', 'UIVendor2026!') };
  }
}

// ═══════════════════════════════════════════════════════════════════════════
// FASE 6: Pengajuan / RUP - beberapa pengajuan di status berbeda-beda
// ═══════════════════════════════════════════════════════════════════════════
async function findPengajuanByTitle(title) {
  const r = await pool.query('SELECT id, request_number FROM procurement_requests WHERE title = $1 ORDER BY created_at DESC LIMIT 1', [title]);
  return r.rows[0] ? { id: r.rows[0].id, number: r.rows[0].request_number } : null;
}

async function seedPengajuan(ctx) {
  ctx.pengajuan = {};

  // Idempotensi per-item (bukan all-or-nothing) - kalau skrip sempat gagal di tengah jalan
  // pada run sebelumnya, run berikutnya cuma melengkapi yang belum ada, bukan mengulang dari
  // nol (request_number auto-increment, jangan sampai numpuk duplikat).
  ctx.pengajuan.renovasiLab = await findPengajuanByTitle('Renovasi Gedung Laboratorium Fakultas Teknik Tahap II');
  if (!ctx.pengajuan.renovasiLab) {
    // 1. Disetujui -> jadi cikal-bakal Tender A (hero)
    const p1 = await post('/pengajuan', {
      title: 'Renovasi Gedung Laboratorium Fakultas Teknik Tahap II',
      unit_kerja: 'Fakultas Teknik', category: 'Konstruksi', estimated_value: 2650000000,
      budget_source: 'BOPTN', description: 'Renovasi ruang laboratorium riset material dan struktur, termasuk perbaikan instalasi listrik dan sanitasi.',
      technical_spec: 'Sesuai KAK terlampir, luas area 850 m2.', quantity: 1, unit_of_measure: 'Paket',
      needed_by_date: '2026-12-31', komoditas: 'Jasa Konstruksi',
      analisa_kebutuhan: 'Kondisi gedung eksisting sudah tidak layak untuk riset material modern.',
      analisa_pasar: 'Tersedia cukup banyak penyedia jasa konstruksi berpengalaman di area Depok/Jakarta.',
      risiko_teridentifikasi: true, risiko_keterangan: 'Potensi keterlambatan akibat cuaca pada musim hujan.',
    }, ctx.ppkToken);
    const p1Row = await pool.query('SELECT id FROM procurement_requests WHERE request_number = $1', [p1.request_number]);
    ctx.pengajuan.renovasiLab = { id: p1Row.rows[0].id, number: p1.request_number };
    await post(`/pengajuan/${ctx.pengajuan.renovasiLab.id}/review`, { admin_notes: 'Dokumen lengkap.', is_docs_complete: true, user_id: ctx.adminId }, ctx.adminToken);
    await post(`/pengajuan/${ctx.pengajuan.renovasiLab.id}/approve`, { user_id: ctx.adminId }, ctx.adminToken);
    const checklist1 = await get(`/pengajuan/${ctx.pengajuan.renovasiLab.id}/checklist`, ctx.adminToken);
    for (const item of checklist1.data) {
      await post(`/pengajuan/${ctx.pengajuan.renovasiLab.id}/checklist`, { master_checklist_id: item.id, approved: true, notes: 'Sudah diverifikasi.', created_by: ctx.adminId }, ctx.adminToken);
    }
    await post(`/pengajuan/${ctx.pengajuan.renovasiLab.id}/approvals`, { approved: true, approved_by: ctx.ppkId, created_by: ctx.ppkId }, ctx.ppkToken);
    await post(`/pengajuan/${ctx.pengajuan.renovasiLab.id}/approvals`, { approved: true, approved_by: ctx.adminId, created_by: ctx.adminId }, ctx.adminToken);
    if (ctx.staff.perencanaan) {
      await post(`/pengajuan/${ctx.pengajuan.renovasiLab.id}/approvals`, { approved: true, approved_by: ctx.staff.perencanaan.id, created_by: ctx.staff.perencanaan.id }, ctx.staff.perencanaan.token);
    }
    await postForm(`/pengajuan/${ctx.pengajuan.renovasiLab.id}/files`,
      form({ judul: 'Kerangka Acuan Kerja (KAK)', created_by: ctx.ppkId }, { file: { blob: docBlob('Kerangka Acuan Kerja - Renovasi Gedung Lab FT.'), filename: 'kak-renovasi-lab.pdf' } }),
      ctx.ppkToken);
  }

  // 2. Disetujui -> jadi cikal-bakal Tender B
  ctx.pengajuan.peralatanLab = await findPengajuanByTitle('Pengadaan Peralatan Laboratorium Kimia');
  if (!ctx.pengajuan.peralatanLab) {
    const p2 = await post('/pengajuan', {
      title: 'Pengadaan Peralatan Laboratorium Kimia',
      unit_kerja: 'Fakultas Teknik', category: 'Barang', estimated_value: 980000000,
      budget_source: 'PNBP', description: 'Pengadaan peralatan gelas laboratorium dan instrumen analisis kimia dasar.',
      needed_by_date: '2026-11-30',
    }, ctx.ppkToken);
    const p2Row = await pool.query('SELECT id FROM procurement_requests WHERE request_number = $1', [p2.request_number]);
    ctx.pengajuan.peralatanLab = { id: p2Row.rows[0].id, number: p2.request_number };
    await post(`/pengajuan/${ctx.pengajuan.peralatanLab.id}/review`, { admin_notes: 'Lengkap.', is_docs_complete: true, user_id: ctx.adminId }, ctx.adminToken);
    await post(`/pengajuan/${ctx.pengajuan.peralatanLab.id}/approve`, { user_id: ctx.adminId }, ctx.adminToken);
  }

  // 3. Disetujui, nilai kecil -> dipakai untuk alur Katalog/keranjang (bukan tender)
  ctx.pengajuan.katering = await findPengajuanByTitle('Pengadaan Katering Acara Wisuda Fakultas');
  if (!ctx.pengajuan.katering) {
    const p3 = await post('/pengajuan', {
      title: 'Pengadaan Katering Acara Wisuda Fakultas',
      unit_kerja: 'Fakultas Ekonomi dan Bisnis', category: 'Jasa', estimated_value: 45000000,
      budget_source: 'BOPTN', description: 'Konsumsi untuk acara wisuda 300 orang.',
    }, ctx.ppkToken);
    const p3Row = await pool.query('SELECT id FROM procurement_requests WHERE request_number = $1', [p3.request_number]);
    ctx.pengajuan.katering = { id: p3Row.rows[0].id, number: p3.request_number };
    await post(`/pengajuan/${ctx.pengajuan.katering.id}/review`, { admin_notes: 'Lengkap.', is_docs_complete: true, user_id: ctx.adminId }, ctx.adminToken);
    await post(`/pengajuan/${ctx.pengajuan.katering.id}/approve`, { user_id: ctx.adminId }, ctx.adminToken);
  }

  // 4. Masih diajukan, menunggu review admin
  ctx.pengajuan.studiKelayakan = await findPengajuanByTitle('Pengadaan Jasa Konsultan Studi Kelayakan Gedung Serbaguna');
  if (!ctx.pengajuan.studiKelayakan) {
    const p4 = await post('/pengajuan', {
      title: 'Pengadaan Jasa Konsultan Studi Kelayakan Gedung Serbaguna',
      unit_kerja: 'Direktorat Fasilitas Umum', category: 'Jasa Konsultansi', estimated_value: 350000000,
      budget_source: 'BOPTN', description: 'Studi kelayakan pembangunan gedung serbaguna baru.',
    }, ctx.ppkToken);
    ctx.pengajuan.studiKelayakan = { number: p4.request_number };
  }

  // 5. Diminta revisi
  ctx.pengajuan.atk = await findPengajuanByTitle('Pengadaan Alat Tulis Kantor Direktorat');
  if (!ctx.pengajuan.atk) {
    const p5 = await post('/pengajuan', {
      title: 'Pengadaan Alat Tulis Kantor Direktorat',
      unit_kerja: 'Direktorat Pengadaan Barang dan Jasa', category: 'Barang', estimated_value: 25000000,
      budget_source: 'BOPTN', description: 'ATK rutin tahunan.',
    }, ctx.ppkToken);
    const p5Row = await pool.query('SELECT id FROM procurement_requests WHERE request_number = $1', [p5.request_number]);
    ctx.pengajuan.atk = { id: p5Row.rows[0].id, number: p5.request_number };
    await post(`/pengajuan/${ctx.pengajuan.atk.id}/revisions`, { catatan: 'Mohon lampirkan rincian RAB per item, bukan cuma nilai total.', created_by: ctx.adminId }, ctx.adminToken);
  }

  // 6. Ditolak (dokumen tidak lengkap)
  ctx.pengajuan.sisAkademik = await findPengajuanByTitle('Pengadaan Sistem Informasi Akademik Tambahan');
  if (ctx.pengajuan.sisAkademik) return;
  const p6 = await post('/pengajuan', {
    title: 'Pengadaan Sistem Informasi Akademik Tambahan',
    unit_kerja: 'Fakultas Kedokteran', category: 'Jasa IT', estimated_value: 620000000,
    budget_source: 'PNBP', description: 'Modul tambahan sistem akademik.',
  }, ctx.ppkToken);
  const p6Row = await pool.query('SELECT id FROM procurement_requests WHERE request_number = $1', [p6.request_number]);
  ctx.pengajuan.sisAkademik = { id: p6Row.rows[0].id, number: p6.request_number };
  await post(`/pengajuan/${ctx.pengajuan.sisAkademik.id}/review`, { admin_notes: 'KAK tidak dilampirkan, mohon lengkapi dan ajukan ulang.', is_docs_complete: false, user_id: ctx.adminId }, ctx.adminToken);

  // Simulasi SAP sekali saja (fitur "Integrasi SAP", tetap simulasi)
  await post('/pengajuan/sap-sync', { requester_id: ctx.ppkId }, ctx.adminToken).catch(() => {});
}

// ═══════════════════════════════════════════════════════════════════════════
// FASE 7: Tender B (bidding pertengahan jalan), C (baru diumumkan), D (dibatalkan)
// ═══════════════════════════════════════════════════════════════════════════
async function findDraftTenderByTitle(title) {
  const r = await pool.query('SELECT id, tender_number FROM tenders WHERE title = $1 ORDER BY created_at DESC LIMIT 1', [title]);
  return r.rows[0];
}

async function seedOtherTenders(ctx) {
  ctx.tenders = {};

  // ── Tender B: dari pengajuan Peralatan Lab, sudah di tahap penawaran ──
  const tB = await findDraftTenderByTitle('Pengadaan Peralatan Laboratorium Kimia');
  if (tB) {
    ctx.tenders.peralatanLab = { id: tB.id, number: tB.tender_number };
    await api('PATCH', `/tenders/${tB.id}/stage`, { token: ctx.pokjaToken, body: { status: 'pengumuman', user_id: ctx.pokjaId } });
    await api('PATCH', `/tenders/${tB.id}/stage`, { token: ctx.pokjaToken, body: { status: 'pendaftaran', user_id: ctx.pokjaId } });
    await post(`/tenders/${tB.id}/register`, { vendor_id: ctx.vendors.sinarAbadi.userId }, ctx.vendors.sinarAbadi.token);
    await post(`/tenders/${tB.id}/register`, { vendor_id: ctx.vendors.mitraKonstruksi.userId }, ctx.vendors.mitraKonstruksi.token);
    await api('PATCH', `/tenders/${tB.id}/stage`, { token: ctx.pokjaToken, body: { status: 'penawaran', user_id: ctx.pokjaId } });
    await postForm(`/tenders/${tB.id}/bids`, form({ vendor_id: ctx.vendors.sinarAbadi.userId, bid_price: 945000000 }, { document: { blob: docBlob('Dokumen penawaran CV Sinar Abadi Teknik.'), filename: 'penawaran-sinar-abadi.pdf' } }), ctx.vendors.sinarAbadi.token);
    // Chat aanwijzing supaya tab itu juga ada isinya
    await post(`/tenders/${tB.id}/aanwijzing`, { user_id: ctx.pokjaId, message: 'Selamat datang di sesi aanwijzing paket ini. Silakan ajukan pertanyaan.' }, ctx.pokjaToken);
    await post(`/tenders/${tB.id}/aanwijzing`, { user_id: ctx.vendors.sinarAbadi.userId, message: 'Apakah spesifikasi mikroskop boleh setara merk lain?' }, ctx.vendors.sinarAbadi.token);
    await post(`/tenders/${tB.id}/aanwijzing`, { user_id: ctx.pokjaId, message: 'Boleh, selama spesifikasi teknis minimal terpenuhi dan dilampirkan brosur.' }, ctx.pokjaToken);
  }

  // ── Tender C: baru diumumkan, belum ada pendaftar (demo halaman publik) ──
  const existingC = await pool.query(`SELECT id FROM tenders WHERE title = 'Pengadaan Jasa Keamanan dan Kebersihan Kampus'`);
  if (!existingC.rows.length) {
    const c = await post('/tenders', {
      title: 'Pengadaan Jasa Keamanan dan Kebersihan Kampus',
      method: 'tender', pagu_anggaran: 1850000000, hps: 1790000000,
      ppk_id: ctx.ppkId, pokja_lead_id: ctx.pokjaId, category: 'Jasa Lainnya',
      description: 'Jasa keamanan dan kebersihan seluruh area kampus untuk periode 1 tahun.',
      work_location: 'Kampus UI Depok',
    }, ctx.pokjaToken);
    const cRow = await pool.query('SELECT id FROM tenders WHERE tender_number = $1', [c.tender_number]);
    ctx.tenders.keamananKebersihan = { id: cRow.rows[0].id, number: c.tender_number };
    await api('PATCH', `/tenders/${cRow.rows[0].id}/stage`, { token: ctx.pokjaToken, body: { status: 'pengumuman', user_id: ctx.pokjaId } });
  } else {
    ctx.tenders.keamananKebersihan = { id: existingC.rows[0].id };
  }

  // ── Tender D: dibatalkan (variasi status) ──
  const existingD = await pool.query(`SELECT id FROM tenders WHERE title = 'Pengadaan Kendaraan Operasional Direktorat'`);
  if (!existingD.rows.length) {
    const d = await post('/tenders', {
      title: 'Pengadaan Kendaraan Operasional Direktorat',
      method: 'tender', pagu_anggaran: 750000000, hps: 720000000,
      ppk_id: ctx.ppkId, pokja_lead_id: ctx.pokjaId, category: 'Barang',
      description: 'Pengadaan 2 unit kendaraan operasional.', work_location: 'Kampus UI Depok',
    }, ctx.pokjaToken);
    const dRow = await pool.query('SELECT id FROM tenders WHERE tender_number = $1', [d.tender_number]);
    ctx.tenders.kendaraan = { id: dRow.rows[0].id };
    await api('PATCH', `/tenders/${dRow.rows[0].id}/stage`, { token: ctx.pokjaToken, body: { status: 'pengumuman', user_id: ctx.pokjaId } });
    await api('PATCH', `/tenders/${dRow.rows[0].id}/stage`, { token: ctx.pokjaToken, body: { status: 'dibatalkan', user_id: ctx.pokjaId } });
  }
}

// ═══════════════════════════════════════════════════════════════════════════
// FASE 8: Tender A (hero) - alur PENUH dari pengumuman sampai kontrak selesai,
// menyentuh hampir semua sub-fitur yang ada.
// ═══════════════════════════════════════════════════════════════════════════
async function step(label, fn) {
  try { await fn(); } catch (err) { console.log(`\n   ⚠ ${label}: ${err.message}`); }
}

async function seedHeroTender(ctx) {
  const t = await findDraftTenderByTitle('Renovasi Gedung Laboratorium Fakultas Teknik Tahap II');
  if (!t) throw new Error('Tender hero belum ada (pengajuan belum ter-ACC?)');
  const id = t.id;
  ctx.tenders.hero = { id, number: t.tender_number };
  const stg = (status) => api('PATCH', `/tenders/${id}/stage`, { token: ctx.pokjaToken, body: { status, user_id: ctx.pokjaId } });

  const mk = ctx.vendors.mitraKonstruksi; // pemenang
  const sa = ctx.vendors.sinarAbadi;      // kalah, sanggah

  // ── Tahap: Pengumuman ──
  await step('umumkan tender', () => stg('pengumuman'));
  await step('unggah dokumen tender', () => postForm(`/tenders/${id}/documents`,
    form({ document_type: 'lelang', name: 'Dokumen Pemilihan', notes: 'Dokumen lengkap syarat & ketentuan tender.', uploaded_by: ctx.pokjaId },
      { file: { blob: docBlob('Dokumen Pemilihan - Renovasi Gedung Lab FT.'), filename: 'dokumen-pemilihan.pdf' } }), ctx.pokjaToken));
  await step('syarat bidang usaha', async () => {
    if (ctx.buKonstruksiId) await post(`/tenders/${id}/bidang-usaha`, { bidang_usaha_id: ctx.buKonstruksiId }, ctx.pokjaToken);
  });
  await step('tugaskan panitia', () => post(`/tenders/${id}/panitia`, {
    members: [
      { nip: '198005152005011001', nama: 'Ir. Slamet Riyadi, M.T.', jabatan: 'Ketua Panitia', is_ketua: true },
      { nip: '198512202010012002', nama: 'Yuni Kartika, S.T.', jabatan: 'Anggota', is_ketua: false },
      { nip: '199001102015031003', nama: 'Fadli Ramadhan, S.T.', jabatan: 'Anggota', is_ketua: false },
    ],
    created_by: ctx.pokjaId,
  }, ctx.pokjaToken));

  // ── Tahap: Pendaftaran ──
  await step('buka pendaftaran', () => stg('pendaftaran'));
  await step('pernyataan minat mitra konstruksi', () => postForm(`/tenders/${id}/pernyataan-minat`,
    form({ vendor_id: mk.userId, nama: 'H. Suryanto Wijaya', jabatan: 'Direktur Utama', alamat: 'Jl. Kebon Jeruk Raya No. 15, Jakarta Barat', telepon: '021-5678901', email: 'kontak@mitrakonstruksi.co.id' }), mk.token));
  await step('pernyataan minat sinar abadi', () => postForm(`/tenders/${id}/pernyataan-minat`,
    form({ vendor_id: sa.userId, nama: 'Agus Salim', jabatan: 'Direktur', alamat: 'Jl. Cinere Raya No. 8, Depok', telepon: '021-7654321', email: 'kontak@sinarabadi.co.id' }), sa.token));
  await step('daftar mitra konstruksi', () => post(`/tenders/${id}/register`, { vendor_id: mk.userId }, mk.token));
  await step('daftar sinar abadi', () => post(`/tenders/${id}/register`, { vendor_id: sa.userId }, sa.token));
  await step('pakta integritas mitra konstruksi', () => post(`/tenders/${id}/pakta-integritas`, { user_id: mk.userId, kode: 'PI-' + Date.now(), jenis: 'REKANAN', created_by: mk.userId }, mk.token));
  await step('pakta integritas sinar abadi', () => post(`/tenders/${id}/pakta-integritas`, { user_id: sa.userId, kode: 'PI-' + (Date.now() + 1), jenis: 'REKANAN', created_by: sa.userId }, sa.token));
  await step('pakta integritas panitia (pokja)', () => post(`/tenders/${id}/pakta-integritas`, { user_id: ctx.pokjaId, kode: 'PI-PANITIA-' + Date.now(), jenis: 'PANITIA', created_by: ctx.pokjaId }, ctx.pokjaToken));
  await step('tambah pihak lain (auditor internal)', async () => {
    if (ctx.staff.audit) await post(`/tenders/${id}/pihak-lain`, { user_id: ctx.staff.audit.id }, ctx.pokjaToken);
  });
  await step('sesi aanwijzing dibuka', () => post(`/tenders/${id}/aanwijzing`, { user_id: ctx.pokjaId, message: 'Selamat datang di sesi aanwijzing paket Renovasi Gedung Lab FT Tahap II. Silakan ajukan pertanyaan sampai batas waktu yang ditentukan.' }, ctx.pokjaToken));
  await step('pertanyaan vendor', () => post(`/tenders/${id}/aanwijzing`, { user_id: mk.userId, message: 'Apakah pekerjaan instalasi listrik termasuk penggantian panel utama?' }, mk.token));
  await step('jawaban panitia', () => post(`/tenders/${id}/aanwijzing`, { user_id: ctx.pokjaId, message: 'Ya, termasuk penggantian panel utama sesuai spesifikasi pada KAK bagian 3.2.' }, ctx.pokjaToken));
  await step('konfirmasi hadir mitra konstruksi', () => post(`/tenders/${id}/aanwijzing/confirm`, { user_id: mk.userId }, mk.token));
  await step('konfirmasi hadir sinar abadi', () => post(`/tenders/${id}/aanwijzing/confirm`, { user_id: sa.userId }, sa.token));
  await step('undangan klarifikasi ke mitra konstruksi', () => post(`/tenders/${id}/undangan-klarifikasi`, {
    vendor_id: mk.userId, tanggal_undangan: '2026-10-05', jam: '10:00', peserta: 'Direktur Utama / Kuasa Direksi',
    pelaksanaan: 'Tatap muka di ruang rapat DPBJ', tempat: 'Gedung DPBJ UI Lt. 2', keterangan: 'Membawa dokumen asli untuk klarifikasi kualifikasi.', created_by: ctx.pokjaId,
  }, ctx.pokjaToken));
  await step('unggah dokumen klarifikasi vendor', () => postForm(`/tenders/${id}/klarifikasi-dokumen`,
    form({ vendor_id: mk.userId, nama: 'Klarifikasi Kualifikasi Perusahaan', notes: 'Dokumen tambahan sesuai permintaan panitia.', created_by: mk.userId },
      { file: { blob: docBlob('Dokumen klarifikasi kualifikasi PT Mitra Konstruksi Nusantara.'), filename: 'klarifikasi-mitra.pdf' } }), mk.token));

  // ── Tahap: Penawaran ──
  await step('buka tahap penawaran', () => stg('penawaran'));
  await step('validasi pembukaan sampul 1', () => post(`/tenders/${id}/pembukaan`, { user_id: ctx.pokjaId, kode: 'SAH', jenis: 'administrasi', tahap: 1 }, ctx.pokjaToken));
  await step('rincian penawaran (BOQ) mitra konstruksi', () => post(`/tenders/${id}/participants/${mk.userId}/bid-items`, {
    items: [
      { item_name: 'Pekerjaan Bongkar & Persiapan', quantity: 1, unit_price: 180000000, delivery_date: '2026-11-15', notes: 'Termasuk pembersihan lokasi' },
      { item_name: 'Pekerjaan Struktur & Dinding', quantity: 1, unit_price: 950000000, delivery_date: '2026-12-10' },
      { item_name: 'Pekerjaan Instalasi Listrik & Sanitasi', quantity: 1, unit_price: 520000000, delivery_date: '2026-12-20' },
      { item_name: 'Pekerjaan Finishing & Interior', quantity: 1, unit_price: 890000000, delivery_date: '2027-01-15' },
    ],
  }, mk.token));
  await step('penawaran sinar abadi', () => postForm(`/tenders/${id}/bids`, form({ vendor_id: sa.userId, bid_price: 2590000000 },
    { document: { blob: docBlob('Dokumen penawaran CV Sinar Abadi Teknik.'), filename: 'penawaran-sinar-abadi.pdf' } }), sa.token));
  await step('validasi pembukaan sampul 2', () => post(`/tenders/${id}/pembukaan`, { user_id: ctx.pokjaId, kode: 'SAH', jenis: 'harga', tahap: 2 }, ctx.pokjaToken));
  await step('jadwalkan ulang tahap evaluasi', () => post(`/tenders/${id}/stages/evaluasi/reschedule`, {
    start_date: '2026-10-20', end_date: '2026-10-27', alasan: 'Menyesuaikan ketersediaan jadwal panitia evaluasi.', user_id: ctx.pokjaId,
  }, ctx.pokjaToken));

  // ── Tahap: Evaluasi ──
  await step('buka tahap evaluasi', () => stg('evaluasi'));
  const critIds = {};
  const addCrit = async (category, name, extra = {}) => {
    const r = await post(`/tenders/${id}/eval-criteria`, { category, name, is_mandatory: true, weight: extra.weight ?? 100, required_count: extra.required_count }, ctx.pokjaToken);
    critIds[`${category}:${name}`] = r.data.id;
    return r.data.id;
  };
  await step('kriteria evaluasi administrasi', async () => {
    await addCrit('administrasi', 'Kelengkapan Legalitas Perusahaan');
    await addCrit('administrasi', 'Kelengkapan Dokumen Penawaran');
  });
  await step('kriteria evaluasi teknis', async () => {
    await addCrit('teknis', 'Kesesuaian Metode Pelaksanaan');
    await addCrit('teknis', 'Jadwal Pelaksanaan Pekerjaan');
  });
  await step('kriteria evaluasi harga', () => addCrit('harga', 'Kewajaran Harga Penawaran'));
  await step('kriteria evaluasi personil (rumus resmi)', () => addCrit('personil', 'Tenaga Ahli Site Manager', { required_count: 1 }));
  await step('kriteria evaluasi peralatan (rumus resmi)', () => addCrit('peralatan', 'Excavator Mini', { required_count: 1 }));
  await step('kriteria evaluasi sertifikat lain (rumus resmi)', () => addCrit('sertifikat_lain', 'Sertifikat ISO 9001', { required_count: 1 }));

  const scoreManual = async (critKey, vendorId, score, meets, notes) => {
    const criteria_id = critIds[critKey];
    if (!criteria_id) return;
    await post(`/tenders/${id}/eval-scores`, { criteria_id, vendor_id: vendorId, meets_requirement: meets, score, notes, scored_by: ctx.pokjaId }, ctx.pokjaToken);
  };
  await step('skor administrasi', async () => {
    await scoreManual('administrasi:Kelengkapan Legalitas Perusahaan', mk.userId, 100, true, 'Lengkap dan sah.');
    await scoreManual('administrasi:Kelengkapan Legalitas Perusahaan', sa.userId, 90, true, 'Lengkap, ada 1 dokumen kadaluarsa mendekati batas.');
    await scoreManual('administrasi:Kelengkapan Dokumen Penawaran', mk.userId, 100, true, 'Lengkap.');
    await scoreManual('administrasi:Kelengkapan Dokumen Penawaran', sa.userId, 95, true, 'Lengkap.');
  });
  await step('skor teknis', async () => {
    await scoreManual('teknis:Kesesuaian Metode Pelaksanaan', mk.userId, 92, true, 'Metode pelaksanaan realistis dan detail.');
    await scoreManual('teknis:Kesesuaian Metode Pelaksanaan', sa.userId, 75, true, 'Metode cukup umum, kurang rinci pada tahap struktur.');
    await scoreManual('teknis:Jadwal Pelaksanaan Pekerjaan', mk.userId, 90, true, 'Jadwal realistis.');
    await scoreManual('teknis:Jadwal Pelaksanaan Pekerjaan', sa.userId, 80, true, 'Jadwal agak ketat pada tahap akhir.');
  });
  await step('skor harga', async () => {
    await scoreManual('harga:Kewajaran Harga Penawaran', mk.userId, 95, true, 'Harga wajar dan kompetitif.');
    await scoreManual('harga:Kewajaran Harga Penawaran', sa.userId, 85, true, 'Harga wajar.');
  });
  await step('config kategori personil', () => post(`/tenders/${id}/eval-category-config`, { category: 'personil', max_score: 100 }, ctx.pokjaToken));
  await step('config kategori peralatan', () => post(`/tenders/${id}/eval-category-config`, { category: 'peralatan', max_score: 100 }, ctx.pokjaToken));
  await step('config kategori sertifikat', () => post(`/tenders/${id}/eval-category-config`, { category: 'sertifikat_lain', max_score: 100 }, ctx.pokjaToken));
  await step('item personil mitra konstruksi', () => post(`/tenders/${id}/eval-score-items`, { criteria_id: critIds['personil:Tenaga Ahli Site Manager'], vendor_id: mk.userId, item_name: 'Ir. Bambang Kurniawan', suitability: 'S' }, ctx.pokjaToken));
  await step('item personil sinar abadi', () => post(`/tenders/${id}/eval-score-items`, { criteria_id: critIds['personil:Tenaga Ahli Site Manager'], vendor_id: sa.userId, item_name: 'Rudi Hartanto', suitability: 'TS' }, ctx.pokjaToken));
  await step('item peralatan mitra konstruksi', () => post(`/tenders/${id}/eval-score-items`, { criteria_id: critIds['peralatan:Excavator Mini'], vendor_id: mk.userId, item_name: 'Excavator Mini (Milik Sendiri)', suitability: 'S', ownership_factor: 100 }, ctx.pokjaToken));
  await step('item peralatan sinar abadi', () => post(`/tenders/${id}/eval-score-items`, { criteria_id: critIds['peralatan:Excavator Mini'], vendor_id: sa.userId, item_name: 'Dump Truck (Sewa)', suitability: 'S', ownership_factor: 60 }, ctx.pokjaToken));
  await step('item sertifikat mitra konstruksi', () => post(`/tenders/${id}/eval-score-items`, { criteria_id: critIds['sertifikat_lain:Sertifikat ISO 9001'], vendor_id: mk.userId, item_name: 'ISO 9001:2015', suitability: 'S' }, ctx.pokjaToken));
  await step('item sertifikat sinar abadi', () => post(`/tenders/${id}/eval-score-items`, { criteria_id: critIds['sertifikat_lain:Sertifikat ISO 9001'], vendor_id: sa.userId, item_name: 'Tidak ada', suitability: 'TS' }, ctx.pokjaToken));
  await step('evaluasi ringkas mitra konstruksi (lulus)', () => api('PATCH', `/tenders/${id}/participants/${mk.userId}/evaluate`, { token: ctx.pokjaToken, body: { technical_score: 93, evaluation_notes: 'Unggul di seluruh aspek teknis dan kualifikasi.', is_passed: true } }));
  await step('evaluasi ringkas sinar abadi (lulus, kalah)', () => api('PATCH', `/tenders/${id}/participants/${sa.userId}/evaluate`, { token: ctx.pokjaToken, body: { technical_score: 78, evaluation_notes: 'Memenuhi syarat, namun skor teknis lebih rendah.', is_passed: true } }));

  // ── Penetapan Pemenang ──
  await step('tetapkan pemenang', () => post(`/tenders/${id}/winner`, { vendor_id: mk.userId, user_id: ctx.pokjaId }, ctx.pokjaToken));
  await step('peringkat pemenang utama', () => post(`/tenders/${id}/peringkat-pemenang`, { vendor_id: mk.userId, peringkat: 1, keterangan: 'Pemenang utama', created_by: ctx.pokjaId }, ctx.pokjaToken));
  await step('peringkat cadangan', () => post(`/tenders/${id}/peringkat-pemenang`, { vendor_id: sa.userId, peringkat: 2, keterangan: 'Cadangan pemenang', created_by: ctx.pokjaId }, ctx.pokjaToken));
  await step('kunci tim panitia', () => api('PATCH', `/tenders/${id}/panitia/lock`, { token: ctx.pokjaToken }));
  await step('validasi pemenang oleh ketua panitia', async () => {
    const list = await get(`/tenders/${id}/panitia`, ctx.pokjaToken);
    const ketua = list.data.find(p => p.is_ketua);
    if (ketua) await api('PATCH', `/tenders/${id}/panitia/${ketua.id}/validasi-pemenang`, { token: ctx.pokjaToken, body: { validasi: 'setuju', catatan: 'Sudah sesuai prosedur evaluasi.' } });
  });
  await step('rating vendor pemenang', () => post(`/vendors/${mk.userId}/rating`, { tender_id: id, ppk_id: ctx.ppkId, rating_score: 5, review_notes: 'Rekam jejak sangat baik pada tender-tender sebelumnya.' }, ctx.ppkToken));

  // ── Negosiasi ──
  await step('buka negosiasi', () => stg('pemenang'));
  await step('chat negosiasi 1', () => post(`/tenders/${id}/negotiation/${mk.userId}`, { user_id: ctx.pokjaId, message: 'Mohon dapat diberikan penawaran harga terbaik mengingat pagu anggaran terbatas.', offered_price: 2500000000 }, ctx.pokjaToken));
  await step('chat negosiasi 2', () => post(`/tenders/${id}/negotiation/${mk.userId}`, { user_id: mk.userId, message: 'Baik, kami dapat menyesuaikan menjadi Rp 2.520.000.000 dengan lingkup pekerjaan tetap sama.', offered_price: 2520000000 }, mk.token));
  await step('sepakati negosiasi', () => post(`/tenders/${id}/negotiation/${mk.userId}/finalize`, { agreed: true, final_price: 2520000000 }, ctx.pokjaToken));
  ctx.heroFinalPrice = 2520000000;

  // ── Masa Sanggah ──
  await step('buka masa sanggah', () => stg('masa_sanggah'));
  const objRes = await (async () => {
    let r; await step('sanggahan vendor kalah', async () => {
      r = await postForm(`/tenders/${id}/objections`, form({ vendor_id: sa.userId, objection_text: 'Kami keberatan dengan hasil evaluasi teknis karena metode pelaksanaan kami sudah sesuai standar industri.' },
        { attachment: { blob: docBlob('Lampiran sanggahan CV Sinar Abadi Teknik.'), filename: 'sanggahan-sinar-abadi.pdf' } }), sa.token);
    });
    return r;
  })();
  await step('balas sanggahan', async () => {
    const list = await get(`/tenders/${id}/objections`, ctx.pokjaToken);
    const obj = list.data[0];
    if (obj) await postForm(`/tenders/${id}/objections/${obj.id}/reply`, form({ response_text: 'Sanggahan telah kami tinjau. Hasil evaluasi tetap sesuai karena perbedaan skor teknis dan pengalaman proyek sejenis.' },
      { response_attachment: { blob: docBlob('Jawaban sanggahan dari Pokja.'), filename: 'jawaban-sanggahan.pdf' } }), ctx.pokjaToken);
  });

  // ── Kontrak ──
  await step('mulai tahap kontrak', () => stg('kontrak'));
  await step('buat kontrak (SPK & BAST)', () => postForm(`/tenders/${id}/contract`, form({
    vendor_id: mk.userId, contract_number: `SPK/${t.tender_number.split('/').slice(1).join('/')}`, contract_date: '2026-10-15',
    contract_value: ctx.heroFinalPrice, status: 'aktif', user_id: ctx.ppkId,
  }, {
    spk: { blob: docBlob('Surat Perjanjian Kerja - Renovasi Gedung Lab FT.'), filename: 'spk.pdf' },
    bast: { blob: docBlob('Berita Acara Serah Terima.'), filename: 'bast.pdf' },
  }), ctx.ppkToken));

  await step('detail SPK/PKS', () => api('PATCH', `/tenders/${id}/contract/spk-detail`, { token: ctx.ppkToken, body: {
    spk_code: `SPK/${t.tender_number.split('/').slice(1).join('/')}`, metode_pembayaran: 'Termin Bertahap', jenis_pengadaan: 'Konstruksi',
    jenis_pekerjaan: 'Renovasi Gedung', jenis_kontrak: 'Lumsum', waktu_pelaksanaan_dari: '2026-10-20', waktu_pelaksanaan_sampai: '2027-02-20',
    pihak1_nama: 'Dr. Ir. Herman Wijaya, M.T.', pihak1_jabatan: 'Pejabat Pembuat Komitmen', pihak2_nama: 'H. Suryanto Wijaya', pihak2_jabatan: 'Direktur Utama',
    lingkup_pekerjaan: 'Renovasi total ruang laboratorium riset material dan struktur seluas 850 m2.',
    legal_nomor_pks: `PKS/${t.tender_number.split('/').slice(1).join('/')}`, legal_tanggal: '2026-10-15',
    penyelesaian_kontrak_awal: '2026-10-20', penyelesaian_kontrak_akhir: '2027-02-20', masa_garansi: 6, masa_garansi_periode: 'bulan',
    nama_kegiatan: 'Renovasi Gedung Laboratorium Fakultas Teknik Tahap II', dokumen_jenis: 'spk',
  } }));
  await step('SPPBJ', () => api('PATCH', `/tenders/${id}/contract/sppbj`, { token: ctx.ppkToken, body: {
    sppbj_code: `SPPBJ/${t.tender_number.split('/').slice(1).join('/')}`, sppbj_date: '2026-10-10', sppbj_nilai: ctx.heroFinalPrice,
    sppbj_direktur_nama: 'H. Suryanto Wijaya', sppbj_direktur_jabatan: 'Direktur Utama', sppbj_direktur_alamat: 'Jl. Kebon Jeruk Raya No. 15, Jakarta Barat', sppbj_direktur_kota: 'Jakarta Barat',
    sppbj_pejabat_berwenang: 'Dr. Ir. Herman Wijaya, M.T.', sppbj_pejabat_jabatan: 'PPK', sppbj_pelaksanaan_dari: '2026-10-20', sppbj_pelaksanaan_sampai: '2027-02-20',
    sppbj_jaminan_pelaksana: 'Ya', sppbj_jaminan_persen: 5, sppbj_jaminan_nilai: Math.round(ctx.heroFinalPrice * 0.05),
    sppbj_jaminan_jangka_dari: '2026-10-20', sppbj_jaminan_jangka_sampai: '2027-03-20',
  } }));
  await step('approval manager', () => api('PATCH', `/tenders/${id}/contract/approval`, { token: ctx.adminToken, body: { field: 'approve_manager', value: true } }));
  await step('approval PPK', () => api('PATCH', `/tenders/${id}/contract/approval`, { token: ctx.ppkToken, body: { field: 'approve_ppk', value: true } }));
  await step('approval pemeriksa', () => api('PATCH', `/tenders/${id}/contract/pemeriksa`, { token: ctx.ppkToken, body: { pemeriksa_nama: 'Ir. Slamet Riyadi, M.T.', pemeriksa_jabatan: 'Ketua Panitia' } }));
  await step('PIC persiapan', async () => { if (ctx.staff.pengelola_kontrak) await api('PATCH', `/tenders/${id}/contract/pic`, { token: ctx.ppkToken, body: { tahap: 'persiapan', user_id: ctx.staff.pengelola_kontrak.id, pengawas_unit_kerja: 'Direktorat Fasilitas Umum' } }); });
  await step('PIC pengendali', async () => { if (ctx.staff.pengelola_kontrak) await api('PATCH', `/tenders/${id}/contract/pic`, { token: ctx.ppkToken, body: { tahap: 'pengendali', user_id: ctx.staff.pengelola_kontrak.id } }); });
  await step('PIC penyelesai', async () => { if (ctx.staff.kasubdit_kontrak) await api('PATCH', `/tenders/${id}/contract/pic`, { token: ctx.ppkToken, body: { tahap: 'penyelesai', user_id: ctx.staff.kasubdit_kontrak.id } }); });
  await step('tahap kontrak: persiapan', () => api('PATCH', `/tenders/${id}/contract/stage`, { token: ctx.ppkToken, body: { stage: 'persiapan' } }));

  await step('SPMK', () => post(`/tenders/${id}/contract/spmk`, { nomor: `SPMK/${t.tender_number.split('/').slice(1).join('/')}`, spmk_dari: '2026-10-20', spmk_sampai: '2027-02-20', keterangan: 'Pekerjaan dapat dimulai sesuai jadwal.', created_by: ctx.ppkId }, ctx.ppkToken));
  await step('jaminan pelaksanaan', async () => {
    const r = await postForm(`/tenders/${id}/contract/jaminan`, form({
      nomor: `JAMPEL/${Date.now()}`, tanggal_jaminan: '2026-10-12', tanggal_konfirmasi_kebank: '2026-10-13', status_konfirmasi: 'menunggu', created_by: ctx.ppkId,
    }, { file_jaminan: { blob: docBlob('Bank Garansi Jaminan Pelaksanaan.'), filename: 'jaminan-pelaksanaan.pdf' } }), ctx.ppkToken);
    if (r?.data?.id) await api('PATCH', `/tenders/${id}/contract/jaminan/${r.data.id}/konfirmasi`, { token: ctx.ppkToken, body: { status_konfirmasi: 'sesuai' } });
  });
  await step('jaminan pemeliharaan', () => postForm(`/tenders/${id}/contract/jaminan-pemeliharaan`, form({
    nomor: `JAMPEL-M/${Date.now()}`, nilai: Math.round(ctx.heroFinalPrice * 0.05), masa: 6, tanggal_mulai: '2027-02-20', tanggal_akhir: '2027-08-20', created_by: ctx.ppkId,
  }, { file_jaminan: { blob: docBlob('Bank Garansi Jaminan Pemeliharaan.'), filename: 'jaminan-pemeliharaan.pdf' } }), ctx.ppkToken));
  await step('SLA (contoh, kontrak dengan unsur layanan)', () => post(`/tenders/${id}/contract/sla`, { availability: '99%', waktu: 'Respons maksimal 1x24 jam', denda: '1 permil per hari', biaya_maintenance: 15000000, nilai_denda: 500000, created_by: ctx.ppkId }, ctx.ppkToken));
  await step('daftar material (kontrak payung)', () => post(`/tenders/${id}/contract/materials`, { materials: [
    { nama: 'Besi Beton Ulir 12mm', qty: 500, satuan: 'Batang', harga_satuan: 85000, sifat: 'Utama' },
    { nama: 'Semen Portland 50kg', qty: 300, satuan: 'Sak', harga_satuan: 68000, sifat: 'Utama' },
  ], created_by: ctx.ppkId }, ctx.ppkToken));
  await step('surat pesanan', async () => {
    const r = await post(`/tenders/${id}/contract/surat-pesanan`, { nomor_surat: `SP/${Date.now()}`, tanggal: '2026-10-22', items: [
      { nama: 'Besi Beton Ulir 12mm', harga_satuan: 85000, qty: 500, satuan: 'Batang', sifat: 'Utama', keterangan: 'Tahap 1' },
    ], created_by: ctx.ppkId }, ctx.ppkToken);
    if (r?.data?.id) {
      const detail = await get(`/tenders/${id}/contract/surat-pesanan`, ctx.ppkToken);
      const sp = detail.data.find(x => x.id === r.data.id);
      const item = sp?.items?.[0];
      if (item) await api('PATCH', `/tenders/${id}/contract/surat-pesanan/items/${item.id}`, { token: ctx.ppkToken, body: { status_terima: 'diterima', status_keterangan: 'Barang sudah diterima sesuai jumlah.', tanggal_terima: '2026-10-25', presentase: 100 } });
    }
  });
  await step('addendum kontrak', async () => {
    const r = await postForm(`/tenders/${id}/contract/addendum`, form({
      nomor: `ADD-01/${Date.now()}`, addendum_ke: 1, jenis: 'Perpanjangan Waktu', tanggal: '2026-12-01',
      tanggal_penyelesaian_awal: '2027-02-20', tanggal_penyelesaian_akhir: '2027-03-10',
      keterangan: 'Perpanjangan waktu 18 hari akibat kondisi cuaca ekstrem.', created_by: ctx.ppkId,
    }, { file_addendum: { blob: docBlob('Dokumen Addendum 1.'), filename: 'addendum-1.pdf' } }), ctx.ppkToken);
    if (r?.data?.id) {
      await api('PATCH', `/tenders/${id}/contract/addendum/${r.data.id}/approval`, { token: ctx.adminToken, body: { field: 'approved_kasubdit', value: true } });
      await api('PATCH', `/tenders/${id}/contract/addendum/${r.data.id}/approval`, { token: mk.token, body: { field: 'approved_penyedia', value: true } });
    }
  });
  await step('catatan internal', () => post(`/tenders/${id}/contract/notes`, { jenis: 'internal', pesan: 'Progres pekerjaan dipantau mingguan, sejauh ini sesuai rencana.', created_by: ctx.ppkId }, ctx.ppkToken));
  await step('catatan penyedia', () => post(`/tenders/${id}/contract/notes`, { jenis: 'penyedia', pesan: 'Mohon percepatan pencairan termin 1 untuk kelancaran pengadaan material.', created_by: mk.userId }, mk.token));
  await step('pengingat kontrak', () => post(`/tenders/${id}/contract/reminders`, { judul: 'Reminder evaluasi progres bulanan', tanggal_dari: '2026-11-01', tanggal_sampai: '2026-11-05', created_by: ctx.ppkId }, ctx.ppkToken));
  await step('dokumen tambahan kontrak', async () => {
    const r = await postForm(`/tenders/${id}/contract/documents`, form({ nama: 'Foto Progres Minggu ke-4', jenis: 'dokumentasi', keterangan: 'Dokumentasi visual progres pekerjaan.', created_by: ctx.ppkId },
      { file: { blob: docBlob('Dokumentasi progres pekerjaan.'), filename: 'dokumentasi-progres.pdf' } }), ctx.ppkToken);
    if (r?.data?.id) await api('PATCH', `/tenders/${id}/contract/documents/${r.data.id}/publish`, { token: ctx.ppkToken, body: { publish: true } });
  });
  await step('perubahan status kontrak', () => postForm(`/tenders/${id}/contract/status-changes`, form({ jenis: 'penyesuaian', alasan: 'Penyesuaian jadwal akibat perubahan cuaca, sesuai addendum 1.', created_by: ctx.ppkId }), ctx.ppkToken));
  await step('SPPJB', () => post(`/tenders/${id}/contract/sppjb`, {
    kode: `SPPJB/${Date.now()}`, tanggal: '2026-10-15', nama_dirut: 'H. Suryanto Wijaya', alamat_dirut: 'Jl. Kebon Jeruk Raya No. 15, Jakarta Barat', kota_dirut: 'Jakarta Barat',
    ppn: 11, persen_jaminan: 5, tmt_jaminan: '2026-10-20', jangka_waktu: '4 bulan', jangka_waktu_jaminan: '5 bulan',
    penanda_tangan: 'Dr. Ir. Herman Wijaya, M.T.', penanda_tangan_jabatan: 'PPK', created_by: ctx.ppkId,
  }, ctx.ppkToken));

  await step('termin pembayaran 1', async () => {
    const r = await post(`/tenders/${id}/contract/payment-terms`, { term_name: 'Termin 1 (Uang Muka)', amount: Math.round(ctx.heroFinalPrice * 0.2), progress_percent: 0, notes: 'Dibayarkan di awal pelaksanaan.' }, ctx.ppkToken);
    if (r?.data?.id) await api('PATCH', `/tenders/${id}/contract/payment-terms/${r.data.id}`, { token: ctx.ppkToken, body: { status: 'dibayar', payment_date: '2026-10-22', notes: 'Sudah dibayar via transfer bank.' } });
  });
  await step('termin pembayaran 2', () => post(`/tenders/${id}/contract/payment-terms`, { term_name: 'Termin 2 (Progres 50%)', amount: Math.round(ctx.heroFinalPrice * 0.4), progress_percent: 50, notes: 'Dibayarkan setelah progres mencapai 50%.' }, ctx.ppkToken));
  await step('termin pembayaran 3', () => post(`/tenders/${id}/contract/payment-terms`, { term_name: 'Termin 3 (Pelunasan)', amount: Math.round(ctx.heroFinalPrice * 0.4), progress_percent: 100, notes: 'Dibayarkan setelah serah terima.' }, ctx.ppkToken));
  await step('sanksi keterlambatan (contoh kecil)', () => post(`/tenders/${id}/contract/penalties`, { days_late: 3, penalty_rate: 0.1, work_value: ctx.heroFinalPrice, penalty_amount: Math.round(ctx.heroFinalPrice * 0.001 * 3), notes: 'Keterlambatan submit laporan mingguan, bukan keterlambatan pekerjaan utama.' }, ctx.ppkToken));

  const deliverables = [
    { scope: 'Pekerjaan Struktur', deliverable_name: 'Pembongkaran & Persiapan Lahan', target_date: '2026-11-01' },
    { scope: 'Pekerjaan Struktur', deliverable_name: 'Pekerjaan Dinding & Struktur', target_date: '2026-12-15' },
    { scope: 'Pekerjaan MEP', deliverable_name: 'Instalasi Listrik & Sanitasi', target_date: '2027-01-10' },
    { scope: 'Pekerjaan Akhir', deliverable_name: 'Finishing & Interior', target_date: '2027-02-15' },
  ];
  const delivIds = [];
  await step('daftar item progres pekerjaan', async () => {
    for (const d of deliverables) {
      const r = await post(`/tenders/${id}/contract/deliverables`, d, ctx.ppkToken);
      if (r?.data?.id) delivIds.push(r.data.id);
    }
  });
  await step('update progres item 1 (selesai 100%)', async () => {
    if (delivIds[0]) await api('PATCH', `/tenders/${id}/contract/deliverables/${delivIds[0]}`, { token: ctx.ppkToken, body: { progress_percent: 100, status: 'selesai', notes: 'Selesai sesuai jadwal.' } });
  });
  await step('update progres item 2 (65%)', async () => {
    if (delivIds[1]) await api('PATCH', `/tenders/${id}/contract/deliverables/${delivIds[1]}`, { token: ctx.ppkToken, body: { progress_percent: 65, status: 'berjalan', notes: 'Sedang berjalan, sesuai jadwal.' } });
  });

  await step('tahap kontrak: pengendalian', () => api('PATCH', `/tenders/${id}/contract/stage`, { token: ctx.ppkToken, body: { stage: 'pengendalian' } }));
  await step('BAST Hasil Pekerjaan', () => api('PATCH', `/tenders/${id}/contract/bast-hasil`, { token: ctx.ppkToken, body: {
    nomor: `BAST-1/${Date.now()}`, tanggal: '2027-02-22', nama_penyedia: 'H. Suryanto Wijaya', jabatan_penyedia: 'Direktur Utama',
    nama_penerima: 'Dr. Ir. Herman Wijaya, M.T.', jabatan_penerima: 'PPK', status: 'diterima',
  } }));
  await step('tahap kontrak: penyelesaian', () => api('PATCH', `/tenders/${id}/contract/stage`, { token: ctx.ppkToken, body: { stage: 'penyelesaian' } }));
  await step('BAST Masa Pemeliharaan', () => api('PATCH', `/tenders/${id}/contract/bast-masa`, { token: ctx.ppkToken, body: {
    nomor: `BAST-2/${Date.now()}`, tanggal: '2027-08-25', nama_penyedia: 'H. Suryanto Wijaya', jabatan_penyedia: 'Direktur Utama',
    nama_penerima: 'Dr. Ir. Herman Wijaya, M.T.', jabatan_penerima: 'PPK', status: 'diterima',
  } }));

  await step('penilaian kinerja: skor per kriteria', async () => {
    const templates = await get('/master/penilaian-templates', ctx.ppkToken);
    const children = templates.data.filter(t => t.parent_id);
    const skors = [95, 90, 92];
    for (let i = 0; i < children.length; i++) {
      await post(`/tenders/${id}/contract/penilaian-kinerja`, { template_id: children[i].id, skor: skors[i] || 90, catatan: 'Sesuai pengamatan lapangan.', scored_by: ctx.ppkId }, ctx.ppkToken);
    }
  });
  await step('penilaian kinerja: grade & total', () => api('PATCH', `/tenders/${id}/contract/penilaian`, { token: ctx.ppkToken, body: { grade: 'A', total_skor: 92 } }));
  await step('penilaian kinerja: approval PPK', () => api('PATCH', `/tenders/${id}/contract/penilaian/approval`, { token: ctx.ppkToken, body: { field: 'penilaian_approval_ppk', value: true } }));
  await step('penilaian kinerja: approval Kasubdit', () => api('PATCH', `/tenders/${id}/contract/penilaian/approval`, { token: ctx.adminToken, body: { field: 'penilaian_approval_kasubdit', value: true } }));
  await step('penilaian kinerja: approval Unit', () => api('PATCH', `/tenders/${id}/contract/penilaian/approval`, { token: ctx.adminToken, body: { field: 'penilaian_approval_unit', value: true } }));

  await step('tahap kontrak: selesai', () => api('PATCH', `/tenders/${id}/contract/stage`, { token: ctx.ppkToken, body: { stage: 'selesai' } }));
  await step('selesaikan tender', () => stg('selesai'));
  await step('buat kode QR validasi kontrak', () => post('/qr/generate', { source_type: 'kontrak', tender_id: id, vendor_id: mk.userId, info: `Kontrak ${t.tender_number}`, created_by: ctx.ppkId }, ctx.ppkToken));
  await step('chat umum panitia-vendor', () => post(`/tenders/${id}/general-chat/${mk.userId}`, { user_id: ctx.ppkId, message: 'Terima kasih atas kerja samanya, pekerjaan selesai dengan hasil sangat baik.', jenis_chat: 'kontrak' }, ctx.ppkToken));
}

// ═══════════════════════════════════════════════════════════════════════════
// FASE 9: Katalog - beberapa produk lengkap (foto, lampiran, kategori, riwayat
// harga) + alur keranjang-nego-checkout terikat ke pengajuan Katering.
// ═══════════════════════════════════════════════════════════════════════════
async function seedKatalog(ctx) {
  ctx.katalog = {};
  const vc = ctx.vendors.vendorContoh;
  const mk = ctx.vendors.mitraKonstruksi;
  if (!vc) return;

  const katElektronik = ctx.katCategories.find(c => c.nama.includes('Elektronik'));
  const katAtk = ctx.katCategories.find(c => c.nama.includes('Tulis'));

  const p1 = await post('/katalog', {
    vendor_id: vc.userId, item_name: 'Laptop Office ProBook 14"', description: 'Laptop untuk kebutuhan perkantoran, RAM 16GB, SSD 512GB.',
    price: 9500000, unit: 'Unit', item_code: 'LPT-001', brand: 'ProBook', model_type: 'PB14-G5',
    tkdn_persen: 25, jenis_produk: 'Barang', lama_garansi: 2, lama_garansi_satuan: 'Tahun', jumlah_stock: 50, jumlah_stock_ready: 20,
    status: 'aktif', keterangan_tambahan: 'Tersedia paket bundling dengan tas laptop.',
    category_ids: katElektronik ? [katElektronik.id] : [],
  }, vc.token);
  ctx.katalog.laptop = p1.data;
  await postForm(`/katalog/${p1.data.id}/photos`, form({ created_by: vc.userId }, { file: { blob: imgBlob(), filename: 'laptop-1.png' } }), vc.token);
  await postForm(`/katalog/${p1.data.id}/attachments`, form({ nama: 'Brosur Spesifikasi', created_by: vc.userId }, { file: { blob: docBlob('Brosur spesifikasi laptop.'), filename: 'brosur-laptop.pdf' } }), vc.token);
  // Perubahan harga -> otomatis tercatat riwayat
  await put(`/katalog/${p1.data.id}`, { price: 9250000, created_by: vc.userId }, vc.token);

  const p2 = await post('/katalog', {
    vendor_id: vc.userId, item_name: 'Kertas HVS A4 80gr (1 Rim)', description: 'Kertas HVS kualitas premium untuk kebutuhan cetak dokumen.',
    price: 55000, unit: 'Rim', item_code: 'ATK-014', brand: 'PaperPrime', jenis_produk: 'Barang', jumlah_stock: 500, jumlah_stock_ready: 500, status: 'aktif',
    category_ids: katAtk ? [katAtk.id] : [],
  }, vc.token);
  ctx.katalog.kertas = p2.data;

  const p3 = await post('/katalog', {
    vendor_id: mk.userId, item_name: 'Paket Renovasi Ruang Kecil (Jasa)', description: 'Jasa renovasi ruang kerja skala kecil, termasuk material dasar.',
    price: 35000000, unit: 'Paket', jenis_produk: 'Jasa', status: 'aktif',
  }, mk.token);
  ctx.katalog.renovasiKecil = p3.data;

  // Laporan produk publik
  await post('/katalog/reports', { katalog_id: p1.data.id, nama: 'Calon Pembeli', email: 'calon@ui.ac.id', alasan: 'Harga di foto berbeda dengan harga sistem.', jenis_laporan: 'harga' });

  // Bandingkan produk (per sesi browser)
  await post('/katalog/compare', { katalog_id: p1.data.id, session_id: 'demo-session-001' }, ctx.ppkToken).catch(() => {});
  await post('/katalog/compare', { katalog_id: p2.data.id, session_id: 'demo-session-001' }, ctx.ppkToken).catch(() => {});

  // ── Alur keranjang terikat ke pengajuan Katering ──
  if (ctx.pengajuan?.katering?.id) {
    const prId = ctx.pengajuan.katering.id;
    const c1 = await post('/katalog/cart', { procurement_request_id: prId, katalog_id: p2.data.id, created_by: ctx.ppkId }, ctx.ppkToken);
    await post('/katalog/cart', { procurement_request_id: prId, katalog_id: p2.data.id, created_by: ctx.ppkId }, ctx.ppkToken); // qty nambah otomatis
    await api('PATCH', `/katalog/cart/${c1.data.id}/qty`, { token: ctx.ppkToken, body: { qty: 10 } });
    await post('/katalog/cart/negotiate', { procurement_request_id: prId, ongkos_kirim: 150000, items: [{ cart_item_id: c1.data.id, harga_nego: 52000 }], updated_by: vc.userId }, vc.token);
    // Alur status: 0 (Proses Pemilihan) -> 1 (Negosiasi) -> 2 (Penyedia Setuju)
    await api('PATCH', `/katalog/cart/${c1.data.id}/status`, { token: ctx.ppkToken, body: { status: '0' } }); // masuk status 1 (negosiasi)
    await api('PATCH', `/katalog/cart/${c1.data.id}/status`, { token: vc.token, body: { status: '1' } });     // penyedia setuju
  }
}

// ═══════════════════════════════════════════════════════════════════════════
// FASE 10: Purchasing (pembelian langsung, tanpa tender)
// ═══════════════════════════════════════════════════════════════════════════
async function seedPurchasing(ctx) {
  const vc = ctx.vendors.vendorContoh;
  if (!vc || !ctx.katalog?.kertas) return;
  const items = [{ id: ctx.katalog.kertas.id, quantity: 20, price: ctx.katalog.kertas.price }];
  const total = items.reduce((s, i) => s + i.quantity * i.price, 0);
  await post('/purchasing', {
    buyer_id: ctx.ppkId, vendor_id: vc.userId, total_amount: total,
    delivery_address: 'Gedung Direktorat PBJ, Kampus UI Depok', notes: 'Kebutuhan ATK rutin bulanan.', items,
  }, ctx.ppkToken);
}

// ═══════════════════════════════════════════════════════════════════════════
// FASE 11: Blacklist manual (di luar yang otomatis dari blokir vendor)
// ═══════════════════════════════════════════════════════════════════════════
async function seedBlacklist(ctx) {
  const existing = await pool.query(`SELECT id FROM blacklist WHERE company_name = 'PT Karya Mundur Sejahtera'`);
  if (existing.rows.length) return;
  await postForm('/blacklist', form({
    company_name: 'PT Karya Mundur Sejahtera', npwp: '09.111.222.3-444.000', address: 'Jl. Raya Bogor No. 50', city: 'Bogor',
    start_date: '2026-06-01', end_date: '2027-06-01', sk_number: 'SK-BLACKLIST/002/2026',
    reason: 'Terbukti melakukan persekongkolan tender berdasarkan hasil audit internal.', created_by: ctx.adminId,
  }, { sk_file: { blob: docBlob('Surat Keputusan Daftar Hitam.'), filename: 'sk-blacklist-002.pdf' } }), ctx.adminToken);
}

// ═══════════════════════════════════════════════════════════════════════════
// FASE 12: Pusat Pesan (Inbox) - kontak biasa + komplain terstruktur + balasan
// ═══════════════════════════════════════════════════════════════════════════
async function seedInbox(ctx) {
  const cats = await get('/inbox/categories');
  const catId = cats.data[0]?.id;
  const m1 = await postForm('/inbox', form({
    category_id: catId, subject: 'Pertanyaan Jadwal Tender', content: 'Selamat siang, saya ingin menanyakan jadwal pengumuman pemenang untuk tender jasa keamanan. Terima kasih.',
    sender_name: 'Ahmad Fauzan', sender_email: 'ahmad.fauzan@example.com', sender_phone: '081298765432',
  }));
  if (m1?.data?.id) {
    await api('PATCH', `/inbox/${m1.data.id}/read`, { token: ctx.adminToken, body: { read_by: ctx.adminId } });
    await post(`/inbox/${m1.data.id}/reply`, { content: 'Selamat siang, jadwal pengumuman pemenang dapat dilihat pada halaman detail tender terkait. Terima kasih.', replied_by: ctx.adminId }, ctx.adminToken);
  }

  const complainTypes = await get('/inbox/meta/complain-types');
  const complainTypeId = complainTypes.data[0]?.id;
  await postForm('/inbox', form({
    complain_type_id: complainTypeId, subject: 'Komplain Resmi', content: 'Kami merasa proses evaluasi salah satu paket tender kurang transparan, mohon ditinjau ulang.',
    sender_name: 'PT Cipta Data Solusi', sender_email: 'halo@ciptadata.co.id', sender_phone: '021-4455667',
  }));
}

// ═══════════════════════════════════════════════════════════════════════════
// FASE 13: Konten Publik (CMS) - banner, berita, FAQ, kebijakan
// ═══════════════════════════════════════════════════════════════════════════
async function seedCms(ctx) {
  const news = await get('/cms/news/all', ctx.adminToken);
  if (!news.data.length) {
    await post('/cms/news', { title: 'Pembukaan Tender Renovasi Gedung Laboratorium', content: 'Direktorat Pengadaan Barang dan Jasa UI membuka tender renovasi gedung laboratorium Fakultas Teknik. Simak informasi lengkapnya di halaman Daftar Tender.', is_published: true, created_by: ctx.adminId }, ctx.adminToken);
    await post('/cms/news', { title: 'Sosialisasi Sistem e-Procurement Baru', content: 'DPBJ UI mengadakan sosialisasi penggunaan sistem e-Procurement baru bagi seluruh unit kerja dan penyedia terdaftar.', is_published: true, created_by: ctx.adminId }, ctx.adminToken);
  }
  const faq = await get('/cms/faq/all', ctx.adminToken);
  if (!faq.data.length) {
    await post('/cms/faq', { question: 'Bagaimana cara mendaftar sebagai penyedia?', answer: 'Klik menu Registrasi Vendor pada halaman utama, lalu lengkapi data perusahaan dan dokumen legalitas.', order_index: 1 }, ctx.adminToken);
    await post('/cms/faq', { question: 'Berapa lama proses verifikasi akun penyedia?', answer: 'Proses verifikasi umumnya memakan waktu 3-5 hari kerja setelah seluruh dokumen dinyatakan lengkap.', order_index: 2 }, ctx.adminToken);
  }
  const banners = await get('/cms/banners/all', ctx.adminToken);
  if (!banners.data.length) {
    await postForm('/cms/banners', form({ nama: 'Selamat Datang di e-Procurement DPBJ UI', link_url: '/tender', created_by: ctx.adminId }, { gambar: { blob: imgBlob(), filename: 'banner-1.png' } }), ctx.adminToken);
  }
  const policies = await get('/cms/policies/all', ctx.adminToken);
  if (!policies.data.length) {
    await post('/cms/policies', { title: 'Kebijakan Privasi', content: '<p>Data pribadi yang dikumpulkan hanya digunakan untuk keperluan proses pengadaan di lingkungan Universitas Indonesia.</p>', jenis: 'privasi', is_published: true, created_by: ctx.adminId }, ctx.adminToken);
  }
}

// ═══════════════════════════════════════════════════════════════════════════
// FASE 14: Integrasi Oracle - contoh data RKA/PR yang "sudah diimpor" + log
// (langsung SQL, meniru hasil impor .xlsx tanpa perlu file .xlsx sungguhan)
// ═══════════════════════════════════════════════════════════════════════════
async function seedOracleIntegration(ctx) {
  const existing = await pool.query('SELECT id FROM integration_rka_budget LIMIT 1');
  if (!existing.rows.length) {
    await pool.query(`INSERT INTO integration_rka_budget (rka_key, start_date_year, segment1, segment1_desc, segment2, segment2_desc, budget_amt, remain_amt, import_file, imported_by) VALUES
      ('RKA-2026-001', 2026, '5211', 'Belanja Barang Operasional', '01', 'Fakultas Teknik', 3500000000, 1200000000, 'rka_2026_export.xlsx', $1),
      ('RKA-2026-002', 2026, '5311', 'Belanja Modal Gedung', '01', 'Fakultas Teknik', 5000000000, 2650000000, 'rka_2026_export.xlsx', $1)
    `, [ctx.adminId]);
    await pool.query(`INSERT INTO integration_logs (jenis, arah, file_name, status, jumlah_baris, created_by) VALUES ('rka_import', 'masuk', 'rka_2026_export.xlsx', 'sukses', 2, $1)`, [ctx.adminId]);
  }
  const existingPr = await pool.query('SELECT id FROM integration_pr_import LIMIT 1');
  if (!existingPr.rows.length) {
    await pool.query(`INSERT INTO integration_pr_import (requisition_number, description, bu_name, document_status, pr_type, nomor_rup, lines, import_file, imported_by) VALUES
      ('PR-2026-00045', 'Pengadaan Peralatan Laboratorium Kimia', 'Fakultas Teknik', 'Approved', 'Barang', 'RUP-2026-0231', $1, 'pr_2026_export.xlsx', $2)
    `, [JSON.stringify([{ item: 'Gelas Ukur Set', qty: 20 }, { item: 'pH Meter Digital', qty: 5 }]), ctx.adminId]);
    await pool.query(`INSERT INTO integration_logs (jenis, arah, file_name, status, jumlah_baris, created_by) VALUES ('pr_import', 'masuk', 'pr_2026_export.xlsx', 'sukses', 1, $1)`, [ctx.adminId]);
  }
  await pool.query(`INSERT INTO integration_logs (jenis, arah, file_name, status, jumlah_baris, created_by) VALUES ('supplier_export', 'keluar', 'supplier_export.xlsx', 'sukses', 6, $1)`, [ctx.adminId]);
}

// ═══════════════════════════════════════════════════════════════════════════
// FASE 15: Setup Supplier Oracle - tiket di berbagai tahap (diajukan sampai selesai)
// ═══════════════════════════════════════════════════════════════════════════
async function seedOracleSupplier(ctx) {
  const pengaju = ctx.staff.pengaju_oracle, verif = ctx.staff.verifikator_oracle;
  const dispatcher = ctx.staff.dispatcher_oracle, pelaksana = ctx.staff.pelaksana_oracle;
  if (!pengaju || !verif || !dispatcher || !pelaksana) return;

  const baseData = (nama, ou) => ({
    operating_unit: ou, nama_supplier: nama, alamat_kantor: 'Jl. Contoh Raya No. 1', no_telp: '021-1234567',
    nama_kontak: 'Kontak Person', jabatan: 'Manajer', no_pkp: 'PKP-' + Math.floor(Math.random() * 9999),
    no_nib: 'NIB-' + Math.floor(Math.random() * 999999), domisili: 'Jakarta', npwp: '07.123.456.7-890.000',
    alamat_npwp: 'Jl. Contoh Raya No. 1', nama_bank: 'Bank Mandiri', cabang_bank: 'Jakarta Pusat',
    nama_rekening: nama, nomor_rekening: '1234567890', mata_uang: 'IDR',
  });

  // Tiket 1: baru diajukan (belum diverifikasi)
  await post('/oracle-supplier', baseData('CV Fajar Elektronik', 'Fakultas Teknik'), pengaju.token);
  // Tiket 2: sudah diverifikasi & diteruskan
  const t2 = await post('/oracle-supplier', baseData('PT Anugerah Percetakan', 'Fakultas Ekonomi dan Bisnis'), pengaju.token);
  const t2Id = await pool.query('SELECT id FROM oracle_supplier_requests ORDER BY created_at DESC LIMIT 1');
  await post(`/oracle-supplier/${t2Id.rows[0].id}/verify-and-forward`, { catatan_verifikator: 'Data lengkap, silakan diproses.' }, verif.token);
  // Tiket 3: sudah di-dispatch
  const t3 = await post('/oracle-supplier', baseData('UD Sumber Rejeki', 'Fakultas Kedokteran'), pengaju.token);
  const t3Id = (await pool.query('SELECT id FROM oracle_supplier_requests ORDER BY created_at DESC LIMIT 1')).rows[0].id;
  await post(`/oracle-supplier/${t3Id}/verify-and-forward`, { catatan_verifikator: 'Data lengkap.' }, verif.token);
  await post(`/oracle-supplier/${t3Id}/dispatch`, { assigned_to: pelaksana.id }, dispatcher.token);
  // Tiket 4: sedang dikerjakan
  const t4 = await post('/oracle-supplier', baseData('PT Cahaya Logistik', 'Direktorat Fasilitas Umum'), pengaju.token);
  const t4Id = (await pool.query('SELECT id FROM oracle_supplier_requests ORDER BY created_at DESC LIMIT 1')).rows[0].id;
  await post(`/oracle-supplier/${t4Id}/verify-and-forward`, { catatan_verifikator: 'Data lengkap.' }, verif.token);
  await post(`/oracle-supplier/${t4Id}/dispatch`, { assigned_to: pelaksana.id }, dispatcher.token);
  await post(`/oracle-supplier/${t4Id}/start`, {}, pelaksana.token);
  // Tiket 5: selesai (dengan bukti)
  const t5 = await post('/oracle-supplier', baseData('CV Mitra Elektrik Jaya', 'Direktorat Pengadaan Barang dan Jasa'), pengaju.token);
  const t5Id = (await pool.query('SELECT id FROM oracle_supplier_requests ORDER BY created_at DESC LIMIT 1')).rows[0].id;
  await post(`/oracle-supplier/${t5Id}/verify-and-forward`, { catatan_verifikator: 'Data lengkap.' }, verif.token);
  await post(`/oracle-supplier/${t5Id}/dispatch`, {}, dispatcher.token); // dispatcher ambil sendiri
  await post(`/oracle-supplier/${t5Id}/start`, {}, dispatcher.token);
  await postForm(`/oracle-supplier/${t5Id}/complete`, form({}, { bukti: { blob: imgBlob(), filename: 'bukti-selesai.png' } }), dispatcher.token);
}

// ═══════════════════════════════════════════════════════════════════════════
// FASE 16: Tindak Lanjut Kelengkapan Dokumen Vendor
// ═══════════════════════════════════════════════════════════════════════════
async function seedVendorFollowup(ctx) {
  const bj = ctx.vendors.berkahJaya; // ditangguhkan, cocok untuk followup terbuka
  if (!bj) return;
  await post(`/vendors/${bj.vendorId}/followup/request`, { catatan: 'Mohon lengkapi salinan NPWP terbaru dan Akta Perubahan Terakhir perusahaan Anda.' }, ctx.adminToken);

  const gs = ctx.vendors.globalSukses;
  if (gs) {
    // siklus lengkap: minta -> konfirmasi -> selesai (walau vendor ini sekarang diblokir, riwayatnya tetap relevan sebagai contoh siklus penuh)
    await post(`/vendors/${gs.vendorId}/followup/request`, { catatan: 'Mohon lampirkan SPT Tahunan 2025.' }, ctx.adminToken);
    await post(`/vendors/${gs.vendorId}/followup/confirm`, { catatan: 'Sudah kami lampirkan di menu Profil & Kualifikasi.' }, gs.token);
    await post(`/vendors/${gs.vendorId}/followup/complete`, { catatan: 'Dokumen sudah sesuai, terima kasih.' }, ctx.adminToken);
  }
}

// ═══════════════════════════════════════════════════════════════════════════
// FASE 17: API Key demo (integrasi pihak ketiga)
// ═══════════════════════════════════════════════════════════════════════════
async function seedApiKey(ctx) {
  const existing = await get('/users/api-keys', ctx.adminToken);
  if (existing.data?.length) return;
  await post('/users/api-keys', { client_name: 'Integrasi Portal SIRUP (Contoh)', created_by: ctx.adminId }, ctx.adminToken).catch(() => {});
}

// ═══════════════════════════════════════════════════════════════════════════
// RUNNER
// ═══════════════════════════════════════════════════════════════════════════
async function main() {
  console.log('═══════════════════════════════════════════════════════════');
  console.log(' Seed Demo Data - DPBJ UI E-Procurement');
  console.log('═══════════════════════════════════════════════════════════');
  const ctx = {};
  await phase('1. Bersihkan sisa data lama', cleanupStray, ctx);
  await phase('2. Login akun dasar', loginAdmin, ctx);
  await phase('3. Data master referensi', seedMasterData, ctx);
  await phase('4. Akun staf 14 role tambahan', seedStaffAccounts, ctx);
  await phase('5. Vendor baru + kualifikasi', seedVendors, ctx);
  await phase('6. Pengajuan / RUP', seedPengajuan, ctx);
  await phase('7. Tender B/C/D (variasi tahap)', seedOtherTenders, ctx);
  await phase('8. Tender A - alur penuh (hero)', seedHeroTender, ctx);
  await phase('9. Katalog + alur keranjang', seedKatalog, ctx);
  await phase('10. Purchasing langsung', seedPurchasing, ctx);
  await phase('11. Daftar Hitam manual', seedBlacklist, ctx);
  await phase('12. Pusat Pesan (Inbox)', seedInbox, ctx);
  await phase('13. Konten Publik (CMS)', seedCms, ctx);
  await phase('14. Integrasi Oracle (contoh RKA/PR)', seedOracleIntegration, ctx);
  await phase('15. Setup Supplier Oracle', seedOracleSupplier, ctx);
  await phase('16. Tindak Lanjut Vendor', seedVendorFollowup, ctx);
  await phase('17. API Key demo', seedApiKey, ctx);

  console.log('\n═══════════════════════════════════════════════════════════');
  console.log(' SELESAI');
  console.log('═══════════════════════════════════════════════════════════');
  console.log('\nAkun staf demo (password semua: ' + DEMO_PASSWORD + '):');
  for (const s of STAFF_ROLES) console.log(`  - ${s.username} (${s.role_key})`);
  console.log('\nAkun vendor demo (password semua: ' + DEMO_PASSWORD + '):');
  console.log('  - mitra_konstruksi (terverifikasi, pemenang tender hero)');
  console.log('  - sinar_abadi (terverifikasi, kalah tender, mengajukan sanggah)');
  console.log('  - cipta_data (masih pending verifikasi)');
  console.log('  - berkah_jaya (ditangguhkan, ada tindak lanjut terbuka)');
  console.log('  - global_sukses (diblokir & masuk daftar hitam)');
  if (ctx.tenders?.hero) console.log(`\nTender contoh alur lengkap: ${ctx.tenders.hero.number}`);

  await pool.end();
}

if (require.main === module) {
  main().catch(err => { console.error('FATAL:', err); process.exit(1); });
}

module.exports = { pool, api, get, post, patch, put, del, postForm, patchForm, form, docBlob, imgBlob, loginAs, phase,
  cleanupStray, loginAdmin, seedMasterData, seedStaffAccounts, seedVendors, seedPengajuan, seedOtherTenders, seedHeroTender,
  seedKatalog, seedPurchasing, seedBlacklist, seedInbox, seedCms, seedOracleIntegration, seedOracleSupplier,
  seedVendorFollowup, seedApiKey, findDraftTenderByTitle, main };
