# Pencatatan Tindak Lanjut Kelengkapan Dokumen Penyedia

Status: **draft untuk demo dan diskusi. Sudah diuji jalan lewat browser di komputer lokal, belum dipasang di server production.**

Paket ini menggantikan draft awal tanggal yang sama yang dulu isinya masih
tebakan kasar. Sekarang sudah disesuaikan dengan arahan Kasubdit dan sudah
diuji end-to-end lewat halaman verifikasi penyedia yang asli (bukan cuma cek
sintaks).

## Isi paket

| Berkas | Untuk apa |
|---|---|
| `alur-logika.pdf` / `alur-logika.html` | Penjelasan visual alur fitur, untuk ditunjukkan ke atasan. |
| `sql/buat_tabel_rekanan_tindak_lanjut.sql` | Migrasi database (1 tabel baru + 1 baris saklar). Dikirim terpisah dari kode. |
| `perubahan-file-lama.diff` | Selisih perubahan untuk 4 berkas yang sudah ada. |
| `source_code/` | Salinan lengkap 11 berkas (7 baru + 4 yang berubah). Struktur foldernya sama dengan project, tinggal ditimpa. |

## Apa yang dikerjakan fitur ini

Melacak proses bolak-balik (tektok) antara verifikator (checker VMS,
`USER_TYPE_ID = 2`) dan penyedia saat melengkapi dokumen registrasi:

1. Verifikator buka halaman verifikasi penyedia, lihat dokumen mana yang kurang
   (pakai checklist yang **sudah ada**). Muncul panel baru **"Tindak Lanjut
   Kelengkapan Dokumen"** (bisa dibuka/tutup). Verifikator tulis catatan,
   klik **"Kirim Catatan & Email ke Penyedia"**.
2. Sistem simpan riwayat, status jadi **PERLU DILENGKAPI**, kirim email otomatis
   ke penyedia.
3. Penyedia login, di halaman **Konfirmasi Pendaftaran** muncul panel berisi
   catatan verifikator + riwayat. Penyedia lengkapi dokumen, klik
   **"Sudah Saya Lengkapi"**.
4. Sistem simpan, status jadi **SUDAH DILENGKAPI**, kirim email otomatis ke
   verifikator yang menangani.
5. Verifikator cek ulang. Kalau oke, klik **"Tandai Dokumen Sudah Lengkap"**
   (status **TERVERIFIKASI**), lalu lanjut ke tombol "KIRIM KE APPROVAL VMS" /
   "VALIDASI & MINTA REKOMENDASI" yang sudah ada seperti biasa. Kalau masih
   kurang, ulang dari langkah 1.
6. (Opsional, ada saklar on/off, default mati) Cron harian mengirim email
   pengingat ke penyedia yang diam lebih dari 7 hari, maksimal 3 kali.

Di panel verifikator kelihatan: status sekarang, sudah berapa kali diingatkan,
sejak kapan status tidak berubah. Ini menjawab pertanyaan "masih ditunggu
penyedia atau sudah kita follow up".

## Di mana panelnya muncul

Panel verifikator dipasang di **dua tempat** karena eproc punya dua jalan ke
layar verifikasi penyedia, dua-duanya submit ke endpoint yang sama:

- **`daftar_rekanan_rekomendasi.php`** : layar checklist di dalam modal
  "Lihat Detil / Verifikasi" (dibuka dari menu Manajemen Penyedia -> Daftar
  Penyedia). Ini yang paling sering dipakai tim.
- **`validasi_rekanan.php`** : halaman dari menu "Validasi Penyedia" (cari
  penyedia dulu baru masuk).

Panelnya **tampil langsung di halaman** (bagian yang bisa dibuka/tutup), bukan
popup, supaya aman dipakai di dalam modal/iframe eproc.

Panel penyedia dipasang di **`konfirmasi_pendaftaran.php`**, cuma muncul kalau
memang ada permintaan kelengkapan yang belum dijawab.

## Urutan penerapan ke server (WAJIB urut)

1. **Jalankan `sql/buat_tabel_rekanan_tindak_lanjut.sql`** di database production.
   Isinya: tabel `REKANAN_TINDAK_LANJUT` + 3 indeks + 1 foreign key ke `REKANAN`,
   dan 1 baris di `MASTER_PENGATURAN` sebagai saklar cron (default `n` = mati).
   Aman dijalankan berulang, tidak menghapus apa pun, tidak menyentuh tabel lama.
2. **Timpa kode** pakai isi `source_code/` (atau terapkan `perubahan-file-lama.diff`
   untuk 4 berkas lama).
3. **Isi konstanta `EMAIL_TINDAK_LANJUT_FALLBACK`** di `application/config/constants.php`
   dengan alamat email tim verifikasi penyedia yang benar (lihat catatan di bawah).
4. (Opsional) Aktifkan pengingat otomatis: `UPDATE MASTER_PENGATURAN SET AKTIF='y'
   WHERE URL='cronjobs_reminder_kelengkapan';` lalu daftarkan cron harian ke
   `http://<url-buyer>/cronjobs_reminder_kelengkapan/sendMail`.
5. Tidak perlu restart server.

## Berkas baru (tidak menimpa apa pun)

```
application/models/rekanantindaklanjut.php                model tabel baru
application/libraries/libtindaklanjut.php                 logika inti + kirim email + render panel/timeline
application/controllers/rekanan_tindak_lanjut_json.php    endpoint AJAX (verifikator & penyedia)
application/controllers/cronjobs_reminder_kelengkapan.php cron pengingat otomatis
application/views/email/rekanan_perlu_lengkapi.php        email ke penyedia
application/views/email/rekanan_sudah_lengkap.php         email ke verifikator
application/views/email/rekanan_reminder_lengkapi.php     email pengingat (cron)
```

## Berkas lama yang berubah (semua perubahan kecil, additive)

- `application/config/constants.php` : tambah 1 konstanta email (+5 baris).
- `application/views/main/daftar_rekanan_rekomendasi.php` : panggil panel verifikator (+9 baris).
- `application/views/main/validasi_rekanan.php` : panggil panel verifikator di 2 cabang form (+43/-14 baris).
- `application/views/main/konfirmasi_pendaftaran.php` : panggil panel penyedia (+8 baris).

Logika lama di keempat berkas itu tidak disentuh.

## Sudah diuji (di komputer lokal, database salinan production `eproc_migrasi`)

- Semua berkas PHP lolos `php -l`.
- Migrasi SQL dijalankan ke database: tabel/indeks/FK terbentuk, aman diulang.
- **Alur lengkap lewat browser + Docker, lewat layar yang asli**: verifikator
  buka modal "Lihat Detil / Verifikasi" -> panel muncul -> kirim catatan ->
  penyedia buka Konfirmasi Pendaftaran -> panel + catatan muncul -> penyedia
  konfirmasi -> verifikator tandai selesai -> riwayat menampilkan siklus penuh
  (Perlu Dilengkapi -> Sudah Dilengkapi -> Terverifikasi) -> panel penyedia
  hilang setelah selesai. Login pakai `devlogin` (fitur login lokal yang ada).
- **Keamanan**: penyedia tidak bisa konfirmasi atas nama perusahaan lain (ditolak).
- **Cron**: saklar OFF tidak kirim apa pun; saklar ON kirim ke penyedia yang
  diam > 7 hari, tidak dobel kirim, berhenti setelah 3 kali.
- Pengiriman email **gagal dari komputer lokal** (SMTP UI tidak bisa diakses
  dari luar jaringan). Ini **tidak menghentikan alur**: catatan dan status
  tetap tersimpan, di riwayat ditandai "email gagal terkirim". Perlu diuji
  ulang di server yang bisa akses SMTP.

## Yang masih perlu keputusan atasan (jangan dipasang sebelum ini jelas)

1. **Alamat email tujuan "penyedia sudah melengkapi".** Di database tidak ada
   kolom email untuk akun verifikator internal, dan tidak ada akun verifikator
   yang username-nya berupa email. Jadi notifikasi diarahkan ke **satu inbox tim**
   lewat konstanta `EMAIL_TINDAK_LANJUT_FALLBACK`. **Perlu dipastikan alamat
   email tim mana yang dipakai.** Kalau dikosongkan, notifikasi ke verifikator
   tidak terkirim (tapi catatan penyedia tetap tersimpan dan kelihatan).
2. **Scope pekerjaan.** Ini penambahan alur/logika, bukan sekadar perbaikan
   tampilan. Sebaiknya ada konfirmasi tertulis dari atasan bahwa ini memang
   diminta sebagai pekerjaan tambahan.
3. **Perlu tidaknya kolom/filter status tindak lanjut di halaman daftar
   Validasi Rekanan** (untuk pantauan atasan). Belum dibuat supaya perubahan
   minim, tapi gampang ditambahkan.
4. Angka **jeda 7 hari** dan **maksimal 3 pengingat** untuk cron, apakah pas.

## Catatan keamanan

Berkas `application/libraries/KMail.php` (bukan bagian dari paket ini, sudah ada
sejak awal) menyimpan **user dan password SMTP dalam teks biasa** dan **ikut
masuk git**. Sebaiknya dipindah ke konfigurasi terpisah yang di-`.gitignore`,
seperti yang sudah dilakukan untuk `database.php`. Di luar scope paket ini, tapi
perlu dicatat.
