<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

include_once("functions/string.func.php");
include_once("functions/date.func.php");

class paket_tahap_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'"); 
		
		if (!$this->kauth->getInstance()->hasIdentity()) { }       
		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;   
	}	
	
	function add() 
	{
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("Metode");
		$this->load->model("Paket");
		
		$metode = new Metode();
		// echo "<pre>";
		// print_r($this->input->post()); die();
		$reqId = $this->input->post("reqId");
		$reqExistData = $this->input->post("reqExistData");
		$submitSimpan = $this->input->post("submitSimpan");
		$submitReschedule = $this->input->post("submitReschedule"); // update untuk reschedule atau bukan
		$rescheduleKe = $this->input->post("rescheduleKe")+1;
		$reqTahapanLelang = $_POST["reqTahapanLelang"];
		$reqHadir = isset($_POST["reqHadir"]) ? $_POST["reqHadir"] : array(0);
		$reqTampil = isset($_POST["reqTampil"]) ? $_POST["reqTampil"] : array(0);
		$reqTanggalMulai = $_POST["reqTanggalMulai"];
		$reqJamMulai = $_POST["reqJamMulai"];
		$reqMenitMulai = $_POST["reqMenitMulai"];
		$reqTanggalSelesai = $_POST["reqTanggalSelesai"]; 
		$reqJamSelesai = $_POST["reqJamSelesai"];
		$reqMenitSelesai = $_POST["reqMenitSelesai"];
		$reqTanggalChash = $_POST["reqTanggalChash"]; 
		$reqTanggalPublish = $this->input->post("reqTanggalPublish");
		$reqJamPublish = $this->input->post("reqJamPublish");
		$reqMenitPublish = $this->input->post("reqMenitPublish");
		$reqKembali = $this->input->post("back"); // set kembali 1:tetep dihalaman jadwal, 0:input paket
		
		$paketInfo->getPaket($reqId);
		$reqMetodeLelangId = $paketInfo->metode_lelang_id;
		
		if($submitSimpan == "Simpan")
		{ 
			for($i=1; $i<=count($reqTahapanLelang);$i++)
			{
				$jam_awal = '';
				$jam_akhir = '';
				$setHadir = isset($reqHadir[$i]) ? (int)$reqHadir[$i] : 0;
				$setTampil = isset($reqTampil[$i]) ? (int)$reqTampil[$i] : 0;
				$setClash = isset($reqTanggalChash[$i]) ? str_replace(' ','',$reqTanggalChash[$i]) : '0';
				
				$metode_insert = new Metode();
				$metode_insert->setField("PAKET_ID", $reqId);
				$metode_insert->setField("NAMA", $reqTahapanLelang[$i]);
				$metode_insert->setField("HADIR", $setHadir);
				$metode_insert->setField("TAMPILKAN", $setTampil);
				$metode_insert->setField("CEK_TANGGAL_CLASH", $setClash);
				
				if($reqTanggalMulai[$i] == "")
					$tanggal_awal = "NULL";
				elseif($reqJamMulai[$i] == "")		
					$tanggal_awal = "TO_TIMESTAMP('".$reqTanggalMulai[$i]."', 'DD-MM-YYYY')";
				else
				{
					$tanggal_awal = "TO_TIMESTAMP('".$reqTanggalMulai[$i]." ".$reqJamMulai[$i].":".$reqMenitMulai[$i]."', 'DD-MM-YYYY HH24:MI')";
					$jam_awal = $reqJamMulai[$i].":".$reqMenitMulai[$i];
				}
				
				$metode_insert->setField("TANGGAL_AWAL", $tanggal_awal);
		
				if($reqTanggalSelesai[$i] == "")
					$tanggal_akhir = "NULL";
				elseif($reqJamSelesai[$i] == "")		
					$tanggal_akhir = "TO_TIMESTAMP('".$reqTanggalSelesai[$i]." 23:59:59', 'DD-MM-YYYY HH24:MI:SS')";
				else
				{
					$tanggal_akhir = "TO_TIMESTAMP('".$reqTanggalSelesai[$i]." ".$reqJamSelesai[$i].":".$reqMenitSelesai[$i]."', 'DD-MM-YYYY HH24:MI')";
					$jam_akhir = $reqJamSelesai[$i].":".$reqMenitSelesai[$i];
				}
				
				$metode_insert->setField("TANGGAL_AKHIR", $tanggal_akhir);
				$metode_insert->setField("JAM_AWAL", $jam_awal);
				$metode_insert->setField("JAM_AKHIR", $jam_akhir);
				$metode_insert->setField("URUT", $i);
				$metode_insert->setField("CREATED_BY", $this->USER_LOGIN_ID);
				$metode_insert->insert();
				unset($metode_insert);		
			}

			// Insert Rekam Jejak
	        $this->load->library("librekamjejak"); 
	        $this->librekamjejak->insertRJ('10','',$reqId,'null','10'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
	        // End Insert Rekam Jejak

			if ($reqKembali == '1') {
				$urlback = "main/index/paket_lelang_tambah_jadwal/?reqId=".$reqId."&back=1";
				if (strpos($urlback, 'amp;') !== false) {
				    $urlback = str_replace('amp;', '', $urlback);
				}
				echo $urlback;
			} else {
				// 1:Tender, 2:Pengadaan Langsung, 3:Tender Terbatas, 5:Penunjukan Langsung, 7:Tender Cepat, 8:Kompetisi
				// if($reqMetodeLelangId == 2 || $reqMetodeLelangId == 4 || $reqMetodeLelangId == 5)
				if($reqMetodeLelangId == 2 || $reqMetodeLelangId == 3 || $reqMetodeLelangId == 5 || $reqMetodeLelangId == 8)
				{
					// echo "main/index/paket_lelang_tambah_rekanan/?reqId=".$reqId;
					echo "main/index/paket_lelang_tambah_jadwal/?reqId=".$reqId;
				}
				else
				{
					// echo "main/index/paket_lelang_tambah_syarat/?reqId=".$reqId;
					echo "main/index/paket_lelang_tambah_jadwal/?reqId=".$reqId;
				}		
			}
			
		}
		elseif($submitSimpan == "Update")
		{ 
			// backupdate jadwal sebelum di hapus ke table paket_reschedule
			$this->load->model("PaketTahap");
			$paket_reschedule_backup_get = new PaketTahap();
  			$paket_reschedule_backup_get->selectByParams(array('PAKET_ID'=>$reqId));
  			while ($paket_reschedule_backup_get->nextRow())
            {
            	if ($paket_reschedule_backup_get->getField('TANGGAL_AWAL') == "") {
            		$tglAwal = "NULL";
            	} else {
					$tglAwal = "TO_TIMESTAMP('".$paket_reschedule_backup_get->getField('TANGGAL_AWAL')."', 'YYYY-MM-DD HH24:MI:SS')";
            	}

            	if ($paket_reschedule_backup_get->getField('TANGGAL_AKHIR2') == "") {
            		$tglAkhir = "NULL";
            	} else {
					$tglAkhir = "TO_TIMESTAMP('".$paket_reschedule_backup_get->getField('TANGGAL_AKHIR2')."', 'YYYY-MM-DD HH24:MI:SS')";
            	}

				$paket_tahap_reschedule = new Paket();
				$paket_tahap_reschedule->setField("PAKET_ID", $reqId);
				$paket_tahap_reschedule->setField("PAKET_TAHAP_ID", $paket_reschedule_backup_get->getField('PAKET_TAHAP_ID'));
				$paket_tahap_reschedule->setField("NAMA", $paket_reschedule_backup_get->getField('NAMA'));
				$paket_tahap_reschedule->setField("URUT", $paket_reschedule_backup_get->getField('URUT'));
				$paket_tahap_reschedule->setField("HADIR", $paket_reschedule_backup_get->getField('HADIR'));
				$paket_tahap_reschedule->setField("TAMPILKAN", $paket_reschedule_backup_get->getField('TAMPILKAN'));
				$paket_tahap_reschedule->setField("TANGGAL_AWAL", $tglAwal);
				$paket_tahap_reschedule->setField("TANGGAL_AKHIR", $tglAkhir);
				$paket_tahap_reschedule->setField("JAM_AWAL", $paket_reschedule_backup_get->getField('JAM_AWAL'));
				$paket_tahap_reschedule->setField("JAM_AKHIR", $paket_reschedule_backup_get->getField('JAM_AKHIR'));
				$paket_tahap_reschedule->setField("RESCHEDULE_KE", $rescheduleKe);
				$paket_tahap_reschedule->setField("CREATED_BY", $this->USER_LOGIN_ID);
				$paket_tahap_reschedule->tahapReschedule();
            }
			unset($paket_reschedule_backup_get);	
			// end of backupdate jadwal sebelum di hapus ke table paket_reschedule

			$metode->setField("PAKET_ID", $reqId);
			$metode->delete();
			
			for($i=1; $i<=count($reqTahapanLelang);$i++)
			{
				$jam_awal = '';
				$jam_akhir = '';
				$setHadir = isset($reqHadir[$i]) ? (int)$reqHadir[$i] : 0;
				$setTampil = isset($reqTampil[$i]) ? (int)$reqTampil[$i] : 0;
				$setClash = isset($reqTanggalChash[$i]) ? str_replace(' ','',$reqTanggalChash[$i]) : '0';

				$metode_insert = new Metode();
				$metode_insert->setField("PAKET_ID", $reqId);
				$metode_insert->setField("NAMA", $reqTahapanLelang[$i]);
				$metode_insert->setField("HADIR", $setHadir);
				$metode_insert->setField("TAMPILKAN", $setTampil);
				$metode_insert->setField("CEK_TANGGAL_CLASH", $setClash);
				
				if($reqTanggalMulai[$i] == "")
					$tanggal_awal = "NULL";
				elseif($reqJamMulai[$i] == "")		
					$tanggal_awal = "TO_TIMESTAMP('".$reqTanggalMulai[$i]."', 'DD-MM-YYYY')";
				else
				{
					$tanggal_awal = "TO_TIMESTAMP('".$reqTanggalMulai[$i]." ".$reqJamMulai[$i].":".$reqMenitMulai[$i]."', 'DD-MM-YYYY HH24:MI')";
					$jam_awal = $reqJamMulai[$i].":".$reqMenitMulai[$i];
				}
				
				$metode_insert->setField("TANGGAL_AWAL", $tanggal_awal);
		
				if($reqTanggalSelesai[$i] == "")
					$tanggal_akhir = "NULL";
				elseif($reqJamSelesai[$i] == "")		
					$tanggal_akhir = "TO_TIMESTAMP('".$reqTanggalSelesai[$i]." 23:59:59', 'DD-MM-YYYY HH24:MI:SS')";
				else
				{
					$tanggal_akhir = "TO_TIMESTAMP('".$reqTanggalSelesai[$i]." ".$reqJamSelesai[$i].":".$reqMenitSelesai[$i]."', 'DD-MM-YYYY HH24:MI')";
					$jam_akhir = $reqJamSelesai[$i].":".$reqMenitSelesai[$i];
				}
				
				$metode_insert->setField("TANGGAL_AKHIR", $tanggal_akhir);
				$metode_insert->setField("JAM_AWAL", $jam_awal);
				$metode_insert->setField("JAM_AKHIR", $jam_akhir);
				$metode_insert->setField("URUT", $i);
				$metode_insert->setField("CREATED_BY", $this->USER_LOGIN_ID);
				$metode_insert->insert();
				unset($metode_insert);	
		
			}	

			// Insert Rekam Jejak
	        $this->load->library("librekamjejak"); 
	        $this->librekamjejak->insertRJ('11','',$reqId,'null','11'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
	        // End Insert Rekam Jejak	

			// update jika reschedule
			if ($submitReschedule == '1') { 
				$paket_reschedule = new Paket();
				$paket_reschedule->setField("PAKET_ID", $reqId);
				$paket_reschedule->setField("RESCHEDULE_KE", $rescheduleKe);
				$paket_reschedule->updateRescheduleKe();
			}
			// end of update jika reschedule

			if ($reqKembali == '1') {
				$urlback = "main/index/paket_lelang_tambah_jadwal/?reqId=".$reqId."&back=1";
				if (strpos($urlback, 'amp;') !== false) {
				    $urlback = str_replace('amp;', '', $urlback);
				}
				echo $urlback;
			} else {
				// 1:Tender, 2:Pengadaan Langsung, 5:Penunjukan Langsung, 7:Tender Cepat
				if($reqMetodeLelangId == 2 || $reqMetodeLelangId == 3 || $reqMetodeLelangId == 5 || $reqMetodeLelangId == 8)
				{
					// echo "main/index/paket_lelang_tambah_rekanan/?reqId=".$reqId;
					echo "main/index/paket_lelang_tambah_jadwal/?reqId=".$reqId;
				}
				else
				{
					// echo "main/index/paket_lelang_tambah_syarat/?reqId=".$reqId;
					echo "main/index/paket_lelang_tambah_jadwal/?reqId=".$reqId;
				}		
			}
						
		}

	}
	
	function reschedule()
	{

		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("Metode");
		$this->load->model("Paket");
		$this->load->model("PaketReschedule");
		
		$metode = new Metode();
		$paket_reschedule = new PaketReschedule();
		
		
		$reqId = $this->input->post("reqId");
		$reqExistData = $this->input->post("reqExistData");
		$submitSimpan = $this->input->post("submitSimpan");
		$reqTahapanLelang = $_POST["reqTahapanLelang"];
		$reqHadir = $_POST["reqHadir"];
		$reqTampil = $_POST["reqTampil"];
		$reqTanggalMulai = $_POST["reqTanggalMulai"];
		$reqJamMulai = $_POST["reqJamMulai"];
		$reqAktivitas = $_POST["reqAktivitas"];
		$reqMenitMulai = $_POST["reqMenitMulai"];
		$reqTanggalSelesai = $_POST["reqTanggalSelesai"];
		$reqJamSelesai = $_POST["reqJamSelesai"];
		$reqMenitSelesai = $_POST["reqMenitSelesai"];
		$reqPaketTahapId = $_POST["reqPaketTahapId"];
		$reqTanggalPublish = $this->input->post("reqTanggalPublish");
		$reqJamPublish = $this->input->post("reqJamPublish");
		$reqMenitPublish = $this->input->post("reqMenitPublish");
		
		$paketInfo->getPaket($reqId);
		$reqMetodeLelangId = $paketInfo->metode_lelang_id;

		$rescheduleKe = (int)$paket_reschedule->getRescheduleKe(array("A.PAKET_ID" => $reqId)) + 1;
		
		//echo "sdsad";exit;
		if($submitSimpan == "Reschedule")
		{
			
			for($i=1; $i<=count($reqTahapanLelang);$i++)
			{
				if($reqTanggalMulai[$i] == "")
				{}
				else
				{
					$jam_awal = '';
					$jam_akhir = '';
					
					$metode_insert = new Metode();
					$metode_insert->setField("PAKET_ID", $reqId);
					$metode_insert->setField("NAMA", $reqTahapanLelang[$i]);
					$metode_insert->setField("HADIR", (int)$reqHadir[$i]);
					$metode_insert->setField("TAMPILKAN", (int)$reqTampil[$i]);
					
					if($reqTanggalMulai[$i] == "")
						$tanggal_awal = "NULL";
					elseif($reqJamMulai[$i] == "")		
						$tanggal_awal = "TO_TIMESTAMP('".$reqTanggalMulai[$i]."', 'DD-MM-YYYY')";
					else
					{
						// $tanggal_awal = "TO_TIMESTAMP('".$reqTanggalMulai[$i]." ".ValToNullMenit($reqJamMulai[$i]).":".ValToNullMenit($reqMenitMulai[$i])."', 'DD-MM-YYYY HH24:MI')";
						$tanggal_awal = "TO_TIMESTAMP('".$reqTanggalMulai[$i]." ".$reqJamMulai[$i].":".$reqMenitMulai[$i]."', 'DD-MM-YYYY HH24:MI')";
						$jam_awal = $reqJamMulai[$i].":".$reqMenitMulai[$i];
					}
					
					$metode_insert->setField("TANGGAL_AWAL", $tanggal_awal);
			
					if($reqTanggalSelesai[$i] == "")
						$tanggal_akhir = "NULL";
					elseif($reqJamSelesai[$i] == "")		
						$tanggal_akhir = "TO_TIMESTAMP('".$reqTanggalSelesai[$i]." 23:59:59', 'DD-MM-YYYY HH24:MI:SS')";
					else
					{
						// $tanggal_akhir = "TO_TIMESTAMP('".$reqTanggalSelesai[$i]." ".ValToNullMenit($reqJamSelesai[$i]).":".ValToNullMenit($reqMenitSelesai[$i])."', 'DD-MM-YYYY HH24:MI')";
						$tanggal_akhir = "TO_TIMESTAMP('".$reqTanggalSelesai[$i]." ".$reqJamSelesai[$i].":".$reqMenitSelesai[$i]."', 'DD-MM-YYYY HH24:MI')";
						$jam_akhir = $reqJamSelesai[$i].":".$reqMenitSelesai[$i];
					}
					
					$metode_insert->setField("TANGGAL_AKHIR", $tanggal_akhir);
					$metode_insert->setField("JAM_AWAL", $jam_awal);
					$metode_insert->setField("JAM_AKHIR", $jam_akhir);
					$metode_insert->setField("PAKET_TAHAP_ID", $reqPaketTahapId[$i]);
					$metode_insert->setField("URUT", $i);
					$metode_insert->setField("RESCHEDULE_KE", $rescheduleKe);
					if($metode_insert->reschedule())
					{
						$metode_update = new Metode();
						$metode_update->setField("TANGGAL_AWAL", $tanggal_awal);
						$metode_update->setField("TANGGAL_AKHIR", $tanggal_akhir);
						$metode_update->setField("JAM_AWAL", $jam_awal);
						$metode_update->setField("JAM_AKHIR", $jam_akhir);
						$metode_update->setField("PAKET_TAHAP_ID", $reqPaketTahapId[$i]);
						$metode_update->updateRescheduleJadwal();
						unset($metode_update);	
					}
					unset($metode_insert);	
				}
			}
			
			echo "Data berhasil direschedule.";
		}
		
	}
	
	function kirim_reschedule()
	{

		$this->load->model("PaketRekanan");
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->library("KMail");

		$paket_rekanan = new PaketRekanan();
		$reqId = $this->input->get("reqId");
		
		$paket_rekanan->selectByParams(array("A.PAKET_ID" => $reqId, "A.LULUS_PENDAFTARAN" => "1"));
		
		$gagal_kirim = "";
		while($paket_rekanan->nextRow())
		{
			$to 				 = $paket_rekanan->getField('EMAIL');
			$reqNamaPerusahaan   = $paket_rekanan->getField("FULL_NAMA_REKANAN");
			
			$Ccs = array($_SESSION["ses_CabangEmail"]);
			$cbg = str_replace(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR,'',dirname(__FILE__));
			$mail = new KMail($cbg);
			$mail->Subject = 'Pengumuman Reschedule Jadwal - '.SYSTEM_NAME.' '.SYSTEM_NAME_PT;
			$mail->AddAddress($to , 'Perusahaan '.$reqNamaPerusahaan);
				
			$body = file_get_contents(base_url()."main/loadUrl/email/reschedule_jadwal/".$reqId);
			$mail->MsgHTML($body);
			//$mail->MsgHTML($message);
							
			if(!$mail->Send())
			{
				if($gagal_kirim == "")
					$gagal_kirim = $reqNamaPerusahaan;
				else
					$gagal_kirim .= ",".$reqNamaPerusahaan;
			}
			else
			{	
			}
			
			unset($mail);
		}
		
		if($gagal_kirim == "")
			echo "1";
		else
			echo "Gagal mengirim email ke perusahaan ".$gagal_kirim.".";
						
			
	}	

	function reschedule_alasan()
	{
		$this->load->model("Paket");
		$paket_reschedule = new Paket();

		$reqId = $this->input->post("reqId");
		$reqKe = $this->input->post("reqKe");
		$reqAlasan = $_POST["reqAlasan"];

		$paket_reschedule->setField("PAKET_ID", $reqId);
		$paket_reschedule->setField("ALASAN", $reqAlasan);

		switch ($reqKe) {
			case '0':
				$simpan = $paket_reschedule->updateRescheduleAlasan1();
				break;
			case '1':
				$simpan = $paket_reschedule->updateRescheduleAlasan2();
				break;
			case '2':
				$simpan = $paket_reschedule->updateRescheduleAlasan3();
				break;
			case '3':
				$simpan = $paket_reschedule->updateRescheduleAlasan4();
				break;
			case '4':
				$simpan = $paket_reschedule->updateRescheduleAlasan5();
				break;
			case '5':
				$simpan = $paket_reschedule->updateRescheduleAlasan6();
				break; 
			case '6':
				$simpan = $paket_reschedule->updateRescheduleAlasan7();
				break; 
			case '7':
				$simpan = $paket_reschedule->updateRescheduleAlasan8();
				break; 
			case '8':
				$simpan = $paket_reschedule->updateRescheduleAlasan9();
				break; 
			case '9':
				$simpan = $paket_reschedule->updateRescheduleAlasan10();
				break; 
			
			default:
				# code...
				break;
		}
		unset($paket_reschedule);	

		if ($simpan) {
			echo "Alasan berhasil disimpan, silahkan reschedule jadwal";
		} else {
			echo "Alasan gagal disimpan, silahkan ulangi kembali";
		}

	}
	
}
?>
