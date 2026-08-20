<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */
/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

define('KODE_REKANAN', 'RK-');
define('FILENAME_PENAWARAN', 'ENC-UI_');
define('PASS_END_PENAWARAN', 'dp137uiklog');
define('SYSTEM_NAME_WEB', 'Universitas Indonesia');
define('SYSTEM_NAME_URL', 'http://10.39.28.110');
define('SYSTEM_URL_EMAIL', 'http://10.39.28.110');
define('SYSTEM_NAME', 'Sistem Pengadaan Barang Jasa');
define('SYSTEM_NAME_PT', 'DPBJ Universitas Indonesia');
define('SYSTEM_ALAMAT_PT', 'Jalan Jend. Sudirman Kav. 32-36, Jakarta 11080');
define('SYSTEM_EMAIL', 'admin.eproc@namapt.co.id');
define('SYSTEM_TLP', 'Phone. +62 888 8888 888');
define('SYSTEM_FAX', '-');
define('SYSTEM_TLP_FAX', '');
define('SYSTEM_EMAIL_VMS', 'admin.eproc@namapt.co.id');
define('SYSTEM_LOGO', 'logo.png');
define('SYSTEM_LOGO_CETAK', 'logo.png'); // logo-cetak-copy.jpg
define('SYSTEM_LOGO_URL', 'http://10.39.28.110/images/logo.png');
define('SYSTEM_LOGO_URL_WHITE', 'http://10.39.28.110/images/logo.png');
define('SYSTEM_LOADING', 'http://10.39.28.110/images/page-loader-old.gif');
define('SYSTEM_SAH', 'DPBJ Universitas Indonesia menyatakan dokumen ini SAH dan dikeluarkan oleh sistem Sistem Pengadaan Barang/Jasa Universitas Indonesia.');
define('SYSTEM_SAH_EN', 'DPBJ Universitas Indonesia states that this document is valid and published by Sistem Pengadaan Barang/Jasa Universitas Indonesia system.');
// define('PASSWORD_DEFAULT', 'ikn19-eproc19');
define('LOKASI_KLARIFIKASI', 'Kantor Logistik Perumda DPBJ Universitas Indonesia');
define('LABEL_PENYEDIA', 'Penyedia');
define('LABEL_COPY_RIGHT', 'Version 3.1.0');
define('LABEL_COPY_RIGHT_YEAR', '2025 - '.date('Y'));
define('NOTIF_PENYEDIA_TERVERIFIKASI', '<div class="alert alert-danger">
              								<button type="button" class="close" data-dismiss="alert">&times;</button>
              								<b><u>Data sudah terverifikasi jika ada perubahan, silahkan hubungi verifikator/validator. </u></b>
              							</div>');
define('MESSAGE_NOUSER', 'بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيم,<br><br>Ana catat IP antum karena sudah mencoba masuk tanpa ijin. <br><br> لَا حَوْلَ وَلَا قُوَّةَ إِلَّا بِاللهِ العَلِيِّ العَظِيْمِ <br> ');

// For SEO Tag
define('META_DESC', 'Sistem Pengadaan Barang/Jasa Universitas Indonesia Kebon Tech merupakan aplikasi Pengadaan Elektronik yang memberikan fasilitas dan informasi terkait dengan proses Pengadaan barang dan jasa di Kebon Tech');
// End For SEO Tag
define('URL_API_DOWNLOAD_FILE_PR', 'https://planning.ui.ac.id/rest/purchasing.php?method=download_attachment&document_id=');
define('URL_API_AGNES', '');

// Button
define('LABEL_HPS', 'Harga Perkiraan');
define('BTN_TAMBAH', '<i class="fa fa-plus"></i> Tambah');
define('BTN_SIMPAN', '<i class="fa fa-check-square-o"></i> Simpan');
define('BTN_KEMBALI', '<i class="fa fa-arrow-left"></i> Kembali');
define('BTN_HAPUS', '<i class="fa fa-trash"></i> Hapus');
define('BTN_LANJUT', 'Lanjut <i class="fa fa-arrow-right"></i>');
define('BTN_PRINT', '<i class="fa fa-print"></i> Cetak ');
define('BTN_PRINT_BA', '<i class="fa fa-print"></i> Cetak Berita Acara ');
define('BTN_RESCHEDULE', '<i class="fa fa-calendar"></i> Reschedule');
define('BTN_UPLOAD', '<i class="fa fa-upload"></i> Upload');
define('BTN_PUBLISH', '<i class="fa fa-send"></i> Publish');
define('BTN_VALIDASI', '<i class="fa fa-check-square-o"></i> Validasi');
define('BTN_REFRESH', '<i class="fa fa-refresh"></i> Refresh');
define('BTN_UBAH', '<i class="fa fa-check-square-o"></i> Ubah');
define('BTN_KIRIM', '<i class="fa fa-send"></i> Kirim');
define('BTN_LIHAT', '<span class="fa fa-eye"></span> Lihat');
define('ICON_EDIT', '<i class="fa fa-pencil-square-o" id="fa-circle" aria-hidden="true"></i>');
define('ICON_DELETE', '<i class="fa fa-trash-o" id="fa-circle" aria-hidden="true"></i>');
define('ICON_DOWNLOAD', '<i class="icon-cloud-download" style="font-size:1.3em; font-weight:bold" id="fa-circle" aria-hidden="true"></i>');
define('UPLOAD_PDF_2MB', '<small>(Format file .pdf & Maksimal ukuran file 5MB) </small>');
define('UPLOAD_PDF_ZIP_2MB', '<small>(Format file .pdf .zip & Maksimal ukuran file 5MB)</small>');
define('UPLOAD_PDF_ZIP_3MB', '<small>(Format file .pdf .zip & Maksimal ukuran file 5MB)</small>');
define('UPLOAD_PDF_ZIP_10MB', '<small>(Format file .pdf .zip & Maksimal ukuran file 10MB)</small>');
define('UPLOAD_XLS_XLSX_PDF_2MB','<small>Format file .xls .xlsx .pdf & Maksimal ukuran file 5MB </small>');
define('UPLOAD_PDF_ZIP_10MB','<small> <br>Format file .pdf .zip & Maksimal ukuran file 10MB </small>');
define('UPLOAD_PDF_ZIP_DOC_10MB','<small> <br>Format file .pdf .zip .docx & Maksimal ukuran file 10MB </small>');
define('CLASS_BTN_DANGER','btn round btn-min-width box-shadow-1 btn-danger text-white');
define('CLASS_BTN_PRIMARY','btn round btn-min-width box-shadow-1 btn-primary text-white');
define('CLASS_BTN_SUCCESS','btn round btn-min-width box-shadow-1 btn-success text-white');
define('CLASS_BTN_INFO','btn round btn-min-width box-shadow-1 btn-info text-white');
define('CLASS_BTN_DARK','btn round btn-min-width box-shadow-1 btn-dark text-white');
define('CLASS_BTN_SECONDARY','btn round btn-min-width box-shadow-1 btn-secondary text-white');
define('CLASS_BTN_WARNING','btn round btn-min-width box-shadow-1 btn-warning text-white');
// setting static jadwal DPBJ Universitas Indonesia
// 0= , 1:
// jenis_tahap  Metode
// 	1			tender	1 file
// 	2			tender	2 file
// 	3			tender terbatas, kompetisi	1 file
// 	4			penunjukan langsung	1 file
// 	5			pengadaan langsung	1 file
// 	6			Tender Prakualifikasi	1 File
// 	7			Tender Prakualifikasi	2 file
// 	8			tender cepat	1 file

// Tender Kualifikasi
define('DOKUMEN_KUALIFIKASI', array(0,0,0,0,0,0,2,2,0)); // Download Dokumen Kualifikasi [OK]
// define('AANWIJZING_KUALIFIKASI', array(0,0,0,0,0,0,3,3,0)); // Aanwijzing Kualifikasi Online
define('UPLOAD_DOKUMEN_KUALIFIKASI', array(0,0,0,0,0,0,4,4,0)); // Upload Dokumen Kualifikasi [OK]
define('EVALUASI_KUALIFIKASI_PRA', array(0,0,0,0,0,0,5,5,0)); // Evaluasi Kualifikasi Prakualifikasi [OK]
define('PEMBUKTIAN_KUALIFIKASI', array(0,0,0,0,0,0,6,6,0)); // Evaluasi Kualifikasi Prakualifikasi [OK]
define('PEMBUKAAN_AUCTION_2FILE_KUALIFIKASI', array(0,0,9,0,0,0,0,16,0)); // Pembukaan Penawaran
define('PEMBUKAAN_AUCTION_SAMPUL1_KUALIFIKASI', array(0,0,9,0,0,0,0,12,0)); // Pembukaan Penawaran 1
define('PEMBUKAAN_AUCTION_SAMPUL2_KUALIFIKASI', array(0,0,9,0,0,0,0,16,0)); // Pembukaan Penawaran 2
define('PENGUMUMAN_KUALIFIKASI', array(0,0,0,0,0,0,7,7,0)); // Evaluasi Kualifikasi Prakualifikasi [OK]
define('SANGGAH_KUALIFIKASI', array(0,0,0,0,0,0,8,8,0)); // Evaluasi Kualifikasi Prakualifikasi [OK]
// End Tender Kualifikasi

define('PENDAFTARAN', array(0,2,2,0,0,0,2,2,2)); // Pendaftaran [OK]
define('DOKUMEN_LELANG', array(0,2,2,1,1,1,9,9,2,)); // Download Dokumen Lelang [-]
define('EVALUASI_KUALIFIKASI', array(0,0,0,0,0,0,0,0,0)); // Evaluasi Kualifikasi Prakualifikasi [-]
define('EVALUASI_KUALIFIKASI1', array(0,0,0,0,0,0,0,0,0)); // Evaluasi Kualifikasi Prakualifikasi [-]
define('AANWIJZING', array(0,3,3,2,2,2,10,10,0)); // Aanwijzing Online [OK]
define('AANWIJZING_KUALIFIKASI', array(0,0,0,0,0,0,3,3,0)); // Aanwijzing Kualifikasi Online [OK]
define('DOKUMEN_PENAWARAN', array(0,4,4,3,3,3,11,11,3)); // Upload Dokumen Penawaran  [OK]
define('DOKUMEN_PENAWARAN1', array(0,4,4,3,3,3,11,11,3)); // Upload Dokumen Penawaran [OK]
define('UPLOAD_PASSWORD_PENAWARAN', array(0,4,4,3,3,3,11,11,3)); // Upload Password Dokumen Penawaran [OK]
define('UPLOAD_PASSWORD_PENAWARAN_SAMPUL2', array(0,0,7,0,0,0,0,14,0)); // Upload Password 2 File [OK]
define('PEMBUKAAN_AUCTION', array(0,5,5,4,4,4,12,12,4)); // Pembukaan Penawaran 1 [OK]
define('PEMBUKAAN_AUCTION_2FILE', array(0,0,8,0,0,0)); // Gak dipake 19-08-2023 Pembukaan Penawaran 2 [-]
define('PEMBUKAAN_AUCTION_SAMPUL2', array(0,0,8,0,0,0,0,15,0)); // Pembukaan Penawaran 2 [OK]
define('EVALUASI_PANAWARAN', array(0,6,6,5,5,5,13,13,5)); // Evaluasi Penawaran File 1 [OK]
define('EVALUASI_PANAWARAN_2FILE', array(0,0,9,0,0,0,0,16,0)); // Evaluasi Penawaran File 2 [OK]
define('EVALUASI_PANAWARAN_SAMPUL2', array(0,0,9,0,0,0)); // gak dipake Evaluasi Penawaran [-]
define('NEGOSIASI', array(0,7,10,6,6,6,14,17,6)); // Negosiasi [OK]
define('PENETAPAN_PENYEDIA', array(0,8,11,7,7,7,15,18,7)); // Penetapan Penyedia / Pemenang [OK]
define('PENGUMUMAN_PEMENANG', array(0,8,11,7,7,7,15,18,7)); // Pengumuman Pemenang [OK]
define('MASA_SANGGAH', array(0,9,12,8,0,0,16,19,0)); // Negosiasi
define('PERINGKAT', array(0,7,10,5,0,0,14,17,6)); // Pemberitahuan Peringkat

/* End of file constants.php */
/* Location: ./application/config/constants.php */
