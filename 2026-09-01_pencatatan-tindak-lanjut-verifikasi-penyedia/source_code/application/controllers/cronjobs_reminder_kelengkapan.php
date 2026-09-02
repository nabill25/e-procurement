<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 * Cron: kirim email pengingat OTOMATIS ke penyedia yang statusnya masih
 * "PERLU_DILENGKAPI" dan sudah diam beberapa hari. Meniru pola
 * cronjobs_notif_dokexpired.php (saklar on/off lewat MASTER_PENGATURAN,
 * log ke file + tabel).
 *
 * URL cron   : http://<url-buyer>/cronjobs_reminder_kelengkapan/sendMail
 * Contoh crontab (1x sehari jam 7 pagi):
 *   0 7 * * * curl -s "http://10.4.2.161/cronjobs_reminder_kelengkapan/sendMail" > /dev/null 2>&1
 *
 * Saklar: baris MASTER_PENGATURAN dengan URL='cronjobs_reminder_kelengkapan',
 *         kolom AKTIF harus 'y' supaya email benar-benar dikirim.
 */
class Cronjobs_reminder_kelengkapan extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->title_api = 'eprocui';
	}

	public function sendMail()
	{
		$this->load->model("Masterpengaturan");
		$this->load->model("Rekanantindaklanjut");

		// 1. Cek saklar
		$pengaturan = new Masterpengaturan();
		$pengaturan->selectByParams(array('URL' => 'cronjobs_reminder_kelengkapan'));
		$aktif = $pengaturan->firstRow() ? $pengaturan->getField("AKTIF") : 'n';

		if ($aktif != 'y') {
			echo "Pengingat kelengkapan: saklar OFF, tidak ada email dikirim.";
			return;
		}

		// 2. Ambil antrian
		$this->load->library("libtindaklanjut");
		$lib = new libtindaklanjut();

		$antrian = new Rekanantindaklanjut();
		$antrian->selectAntrianReminder(libtindaklanjut::HARI_JEDA_REMINDER, libtindaklanjut::MAKS_REMINDER);

		$jml = 0;
		$log = '';
		while ($antrian->nextRow()) {
			$rekananId = $antrian->getField("REKANAN_ID");
			$res = $lib->kirimReminderOtomatis($rekananId);
			$jml++;

			$baris = "REKANAN_ID:" . $rekananId
				. " ### NAMA:" . $antrian->getField("NAMA")
				. " ### DIAM(hari):" . $antrian->getField("HARI_DIAM")
				. " ### TERSIMPAN:" . ($res['ok'] ? 'YA' : 'TIDAK')
				. " ### EMAIL_TERKIRIM:" . ($res['email'] ? 'YA' : 'TIDAK')
				. " ### TIME:" . date('Y-m-d H:i:s');
			$log .= $baris . "\n";
			echo $baris . "<br>";
		}

		// 3. Log ke file (folder logs/notif sudah dipakai cron dokexpired)
		$filepath = 'logs/notif/logs_reminder_kelengkapan.txt';
		$handle = @fopen($filepath, "a+");
		if ($handle) {
			fwrite($handle, "===== " . date('Y-m-d H:i:s') . " (" . $jml . " penyedia diproses) =====\n" . $log . "\n");
			fclose($handle);
		}

		echo "<br>Selesai. " . $jml . " penyedia diproses.";
	}

	public function testCron()
	{
		$filepath = 'logs/notif/logs_reminder_kelengkapan_test.txt';
		$handle = @fopen($filepath, "a+");
		if ($handle) {
			fwrite($handle, "TEST ### DATE:" . date('Y-m-d H:i:s') . "\n");
			fclose($handle);
		}
		echo "ok " . date('Y-m-d H:i:s');
	}
}
