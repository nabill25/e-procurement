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
include_once("functions/default.func.php");

class paket_rekanan_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		if (!$this->kauth->getInstance()->hasIdentity())
		{
		}

		$this->ID = isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID)?$this->kauth->getInstance()->getIdentity()->REKANAN_ID:'';

		$this->USER_LOGIN_ID =  isset($this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID)?$this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID:'';
		$this->USER_LOGIN =  isset($this->kauth->getInstance()->getIdentity()->USER_LOGIN)?$this->kauth->getInstance()->getIdentity()->USER_LOGIN:'';
		$this->USER_NAMA =  isset($this->kauth->getInstance()->getIdentity()->USER_NAMA)?$this->kauth->getInstance()->getIdentity()->USER_NAMA:'';
		$this->USER_TYPE_ID =  isset($this->kauth->getInstance()->getIdentity()->USER_TYPE_ID)?$this->kauth->getInstance()->getIdentity()->USER_TYPE_ID:'';
		$this->REKANAN_ID =  isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID)?$this->kauth->getInstance()->getIdentity()->REKANAN_ID:'';
		$this->UNIT_KERJA_ID =  isset($this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID)?$this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID:'';
		$this->NIP =  isset($this->kauth->getInstance()->getIdentity()->NIP)?$this->kauth->getInstance()->getIdentity()->NIP:'';
		$this->LOGIN_TIME = isset($this->kauth->getInstance()->getIdentity()->LOGIN_TIME)?$this->kauth->getInstance()->getIdentity()->LOGIN_TIME:'';
		$this->LOGIN_DATE = isset($this->kauth->getInstance()->getIdentity()->LOGIN_DATE)?$this->kauth->getInstance()->getIdentity()->LOGIN_DATE:'';
		$this->REKANAN = isset($this->kauth->getInstance()->getIdentity()->REKANAN)?$this->kauth->getInstance()->getIdentity()->REKANAN:'';
		$this->REKANAN_KODE = isset($this->kauth->getInstance()->getIdentity()->REKANAN_KODE)?$this->kauth->getInstance()->getIdentity()->REKANAN_KODE:'';
		$this->REKANAN_PKP = isset($this->kauth->getInstance()->getIdentity()->REKANAN_PKP)?$this->kauth->getInstance()->getIdentity()->REKANAN_PKP:'';
		$this->REKANAN_NPWP = isset($this->kauth->getInstance()->getIdentity()->REKANAN_NPWP)?$this->kauth->getInstance()->getIdentity()->REKANAN_NPWP:'';
		$this->REKANAN_STATUS_PERUSAHAAN = isset($this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN)?$this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN:'';
		$this->REKANAN_STATUS_VALIDASI = isset($this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI)?$this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI:'';
	}

	function undang_pemilihan()
	{

		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketRekanan");

		$paket_rekanan = new PaketRekanan();
		/* VARIABLES */
		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqNama = $this->input->post("reqNama");
		$submitSimpan = $this->input->post("submitSimpan");
		$reqRekananId = $_POST["reqRekananId"] ?: 0;


		$reqBulan = date("m");
		$reqTahun = date("Y");

		if($submitSimpan == "Simpan")
		{
			if ($reqRekananId) {
				$reqRekananId = $reqRekananId;
			} else {
				$reqRekananId = array();
			}
			// echo "string"; die();
			$data_tdk_delete='';
			for($i=0; $i<count($reqRekananId);$i++)
			{
				$paket_rekanan_check = new PaketRekanan();

				if($data_tdk_delete == '')	$data_tdk_delete .= "'".$reqRekananId[$i]."'";
				else						$data_tdk_delete .= ", '".$reqRekananId[$i]."'";

				$paket_rekanan_check = new PaketRekanan();
				$check = $paket_rekanan_check->getCountByParams(array("PAKET_ID" => $reqId, "REKANAN_ID" => $reqRekananId[$i]));
				if($check == 0)
				{
					$paket_rekanan_check->setField("PAKET_ID", $reqId);
					$paket_rekanan_check->setField("REKANAN_ID", $reqRekananId[$i]);
					$paket_rekanan_check->setField("DI_EMAIL", 0);
					$paket_rekanan_check->setField("LULUS_PENDAFTARAN", 1);
					$paket_rekanan_check->setField("LULUS_KUALIFIKASI", 1);
					$paket_rekanan_check->setField("KODE_REKANAN", date("ymd"));
					$paket_rekanan_check->insertUndang2();
				}
				unset($paket_rekanan_check);
			}
			// echo $data_tdk_delete.'-';
			if ($data_tdk_delete == '') {
				$paket_rekanan->setField("PAKET_ID", $reqId);
				$paket_rekanan->setField("REKANAN_ID", $data_tdk_delete);
				// $paket_rekanan->deleteByPaketNew();
				$paket_rekanan->deleteByPaket();
			} else {
				$paket_rekanan->setField("PAKET_ID", $reqId);
				$paket_rekanan->setField("REKANAN_ID", $data_tdk_delete);
				$paket_rekanan->deleteByPaketNew();
			}

			echo "Data berhasil disimpan.";
		}

	}

	function undang_pemilihan_email()
	{

		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketRekanan");
		$this->load->library("KMail");

		$paket_rekanan = new PaketRekanan();

		/* VARIABLES */
		$reqId = $this->input->get("reqId");

		$paketInfo->getPaket($reqId);
		$paket_rekanan_kirim = new PaketRekanan();
		// $paket_rekanan_kirim->selectByParamsEmail(array("PAKET_ID" => $reqId, "DI_EMAIL" => 0));
		$paket_rekanan_kirim->selectByParamsEmail(array("PAKET_ID" => $reqId));

		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
      	$paketInfo->getPaket($reqId);
		$reqNama = $paketInfo->nama;

		$Ccs = array('support'=>"");
		//$cbg = str_replace(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR,'',dirname(__FILE__));

		$kirim = 1;
		while($paket_rekanan_kirim->nextRow())
		{
			$mail = new KMail($cbg);
			$mail->Subject  =  translate("Undangan Pengadaan - ".$reqNama, "Tender Invitation");
			$mail->AddAddress($paket_rekanan_kirim->getField("EMAIL") , 'Perusahaan '.$paket_rekanan_kirim->getField("NAMA"));
			foreach($Ccs as $key => $val){
				$mail->AddCC($val , $key);
			}
			//$mail->MsgHTML($message);

			if($paketInfo->bahasa == "EN")
				$link_email = "undangan_paket_en";
			else
				$link_email = "undangan_paket";

			$body = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/email/".$link_email."/".$reqId."/".$paket_rekanan_kirim->getField("PAKET_REKANAN_ID"));
			$mail->MsgHTML($body);
			if(!$mail->Send())
			{
				$kirim = 0;
			}
			else
			{
				$paket_rekanan_update = new PaketRekanan();
				$paket_rekanan_update->setField("FIELD", "DI_EMAIL");
				$paket_rekanan_update->setField("FIELD_VALUE", 1);
				$paket_rekanan_update->setField("PAKET_REKANAN_ID", $paket_rekanan_kirim->getField("PAKET_REKANAN_ID"));
				$paket_rekanan_update->update();
				unset($paket_rekanan_update);
			}

			unset($body);
		}

		// Insert Rekam Jejak
        $this->load->library("librekamjejak");
        $this->librekamjejak->insertRJ('29','',$reqId,'null','29'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
        // End Insert Rekam Jejak

		if($kirim == 0)
			echo "Ada email yang gagal Terkirim, Silahkan Ulangi!";
		else
			echo "Email telah terkirim.";

	}

	function pengumuman_pemenang()
	{
		// echo "string"; die();
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketDokumen");
		$this->load->library("FileHandler");

		$paket_dokumen = new PaketDokumen();
		$file = new FileHandler();
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqBayar= $this->input->post('reqBayar');
		$reqDokumenId = $this->input->post('reqDokumenId');
		$reqPaketDokumenId = $this->input->post('reqPaketDokumenId');
		$submitSimpan= $this->input->post('submitSimpan');

		$FILE_DIR = "uploads/pemenang/";

		$paket_dokumen->setField("PAKET_ID", $reqId);
		$paket_dokumen->setField("NAMA", "Pengumuman Pemenang");
		/* UPLOAD FILE */
		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFilesSize = $file->uploadedSize;
			$insertLinkFilesExe =  $file->uploadedExtension;
			$insertLinkFile =  $renameFile;
		}
		else
		{
			$insertLinkFilesSize = $reqLinkFileTempUkuran;
			$insertLinkFilesExe =  $reqLinkFileTempTipe;
			$insertLinkFile =  $reqLinkFileTemp;
		}
		/* END UPLOAD FILE */
		$paket_dokumen->setField("UKURAN", $insertLinkFilesSize);
		$paket_dokumen->setField("TIPE", $insertLinkFilesExe);
		$paket_dokumen->setField("PATH_FILE", $insertLinkFile);
		$paket_dokumen->setField("JENIS_DOKUMEN", "PENGUMUMAN_PEMENANG");
		$paket_dokumen->setField("KETERANGAN", $reqKeterangan);
		$paket_dokumen->setField("REKANAN_USER_ID", "NULL");
		$paket_dokumen->setField("STATUS", (int)$reqBayar);
		$paket_dokumen->setField("PAKET_DOKUMEN_ID", $reqPaketDokumenId);
		if($reqMode =='insert')
		{
			$paket_dokumen->insert2();
		}
		else
		{
			$paket_dokumen->updateDokumenPemenang();
		}
		echo "Data berhasil disimpan";

	}

	function pengumuman_prakualifikasi()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketDokumen");
		$this->load->library("FileHandler");

		$paket_dokumen = new PaketDokumen();
		$file = new FileHandler();

		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqBayar= $this->input->post('reqBayar');
		$reqDokumenId = $this->input->post('reqDokumenId');
		$reqPaketDokumenId = $this->input->post('reqPaketDokumenId');
		$submitSimpan= $this->input->post('submitSimpan');

		$FILE_DIR = "uploads/pengumuman_prakualifikasi/";

		$paket_dokumen->setField("PAKET_ID", $reqId);
		$paket_dokumen->setField("NAMA", "Pengumuman Pra");
		/* UPLOAD FILE */
		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFilesSize = $file->uploadedSize;
			$insertLinkFilesExe =  $file->uploadedExtension;
			$insertLinkFile =  $renameFile;
		}
		else
		{
			$insertLinkFilesSize = $reqLinkFileTempUkuran;
			$insertLinkFilesExe =  $reqLinkFileTempTipe;
			$insertLinkFile =  $reqLinkFileTemp;
		}
		/* END UPLOAD FILE */
		$paket_dokumen->setField("UKURAN", $insertLinkFilesSize);
		$paket_dokumen->setField("TIPE", $insertLinkFilesExe);
		$paket_dokumen->setField("PATH_FILE", $insertLinkFile);
		$paket_dokumen->setField("JENIS_DOKUMEN", "PENGUMUMAN_PRA");
		$paket_dokumen->setField("KETERANGAN", $reqKeterangan);
		$paket_dokumen->setField("REKANAN_USER_ID", "NULL");
		$paket_dokumen->setField("STATUS", (int)$reqBayar);
		$paket_dokumen->setField("PAKET_DOKUMEN_ID", $reqPaketDokumenId);
		if($reqMode =='insert')
		{
			$paket_dokumen->insert2();
		}
		else
		{
			$paket_dokumen->updateDokumenPemenang();
		}
		echo "Data berhasil disimpan";

	}


	function verifikasi_peserta_lelang()
	{
		$this->load->model("PaketRekanan");

		$paket_rekanan = new PaketRekanan();

		$reqId = $this->input->post("reqId");
		$submitSimpan =  $this->input->post("submitSimpan");

		$reqLulusPendaftaran = $_POST["reqLulusPendaftaran"];
		$reqLulusKeterangan = $_POST["reqLulusKeterangan"];
		$reqPaketRekananId = $_POST["reqPaketRekananId"];

		if($submitSimpan == 'Simpan'){
			for($i=0;$i<count($reqPaketRekananId);$i++)
			{
				$paket_rekanan_insert = new PaketRekanan();
				$paket_rekanan_insert->setField("FIELD1", "LULUS_PENDAFTARAN");
				$paket_rekanan_insert->setField("FIELD1_VALUE", $reqLulusPendaftaran[$i]);
				$paket_rekanan_insert->setField("FIELD2", "LULUS_PENDAFTARAN_KETERANGAN");
				$paket_rekanan_insert->setField("FIELD2_VALUE", $reqLulusKeterangan[$i]);
				$paket_rekanan_insert->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$i]);
				$paket_rekanan_insert->updateTwoField();
				unset($paket_rekanan_insert);
			}
			echo "Data berhasil disimpan.";
		}

	}

	function verifikasi_peserta_kualifikasi()
	{
		$this->load->model("PaketRekanan");

		$paket_rekanan = new PaketRekanan();

		$reqId = $this->input->post("reqId");
		$submitSimpan =  $this->input->post("submitSimpan");

		$reqLulusPendaftaran = $_POST["reqLulusPendaftaran"];
		$reqLulusKeterangan = $_POST["reqLulusKeterangan"];
		$reqPaketRekananId = $_POST["reqPaketRekananId"];

		// echo "<pre>"; print_r($_POST); die;

		if($submitSimpan == 'Simpan'){
			for($i=0;$i<count($reqPaketRekananId);$i++)
			{
				$paket_rekanan_insert = new PaketRekanan();
				$paket_rekanan_insert->setField("FIELD1", "LULUS_PENDAFTARAN");
				$paket_rekanan_insert->setField("FIELD1_VALUE", $reqLulusPendaftaran[$i]);
				$paket_rekanan_insert->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$i]);
				$paket_rekanan_insert->updateOneField();
				unset($paket_rekanan_insert);
			}
			echo "Data berhasil disimpan.";
		}

	}

	function email_seluruh_peserta_lelang()
	{

		$this->load->model("PaketRekanan");
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->library("KMail");

		$paket_rekanan = new PaketRekanan();
		$reqId = $this->input->get("reqId");

		$gagal_kirim = "";

		$paketInfo->getPaket($reqId);

		// $paket_rekanan->selectByParams(array("A.PAKET_ID" => $reqId, "A.DI_EMAIL" => "0"));
		// ikn 20190914
		$paket_rekanan->selectByParams(array("A.PAKET_ID" => $reqId));

		while($paket_rekanan->nextRow())
		{
			$reqLulusPendaftaran = $paket_rekanan->getField("LULUS_PENDAFTARAN");
			$reqLulusKeterangan  = $paket_rekanan->getField("LULUS_PENDAFTARAN_KETERANGAN");
			$reqPaketRekananId   = $paket_rekanan->getField("PAKET_REKANAN_ID");
			$reqNamaPerusahaan   = $paket_rekanan->getField("FULL_NAMA_REKANAN");
            $to 				 = $paket_rekanan->getField('EMAIL');

			if($reqLulusPendaftaran == "")
			{
			}
			else
			{
				// $Ccs = array($_SESSION["ses_CabangEmail"]);
				$cbg = str_replace(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR,'',dirname(__FILE__));
				$mail = new KMail($cbg);
				$mail->Subject = 'Pengumuman pendaftaran - '.SYSTEM_NAME.' - '.SYSTEM_NAME_PT;;
				$mail->AddAddress($to , 'Perusahaan '.$reqNamaPerusahaan);
				/*foreach($Ccs as $key => $val){
					$mail->AddBCC($val , $key);
				}*/

				if($paketInfo->bahasa == "EN")
					$link_email = "lulus_pendaftaran_en";
				else
					$link_email = "lulus_pendaftaran";

				$body = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/email/".$link_email."/".$reqId."/".$reqPaketRekananId);
				$mail->MsgHTML($body);
				//$mail->MsgHTML($message);

				if(!$mail->Send())
				{
					$gagal_kirim .= ", ".$reqNamaPerusahaan;
				}
				else
				{
					$paket_rekanan_update = new PaketRekanan();
					$paket_rekanan_update->setField("FIELD", "DI_EMAIL");
					$paket_rekanan_update->setField("FIELD_VALUE", 2);
					$paket_rekanan_update->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
					$paket_rekanan_update->update();
					unset($paket_rekanan_update);
				}

				unset($body);
			}
		}

		if($gagal_kirim == "")
			echo "Email berhasil dikirim.";
		else
			echo "Terdapat email yang gagal dikirim yaitu".$gagal_kirim;

	}

	function email_peserta_lelang()
	{

		$this->load->model("PaketRekanan");
		$this->load->library("paketinfo");
		$paketInfo = new paketinfo();
		$this->load->library("KMail");

		$paket_rekanan = new PaketRekanan();
		$reqId = $this->input->get("reqId");
		$reqPaketId = $this->input->get("reqPaketId");

		$gagal_kirim = "";

		$paketInfo->getPaket($reqId);

		$paket_rekanan->selectByParams(array("A.PAKET_REKANAN_ID" => $reqPaketId));
		$paket_rekanan->firstRow();

		$reqLulusPendaftaran = $paket_rekanan->getField("LULUS_PENDAFTARAN");
		$reqLulusKeterangan = $paket_rekanan->getField("LULUS_PENDAFTARAN_KETERANGAN");
		$reqPaketRekananId = $paket_rekanan->getField("PAKET_REKANAN_ID");
		$reqNamaPerusahaan = $paket_rekanan->getField("FULL_NAMA_REKANAN");
        $to 				 = $paket_rekanan->getField('EMAIL');

		if($reqLulusPendaftaran == "")
		{
			echo "Status pendaftaran belum ditentukan.";
			return;
		}
		else
		{
			$Ccs = array($_SESSION["ses_CabangEmail"]);
			$cbg = str_replace(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR,'',dirname(__FILE__));
			$mail = new KMail($cbg);
			$mail->Subject = 'Pengumuman pendaftaran'.' - '.SYSTEM_NAME.' - '.SYSTEM_NAME_PT;
			$mail->AddAddress($to , 'Perusahaan '.$reqNamaPerusahaan);
			/*foreach($Ccs as $key => $val){
				$mail->AddBCC($val , $key);
			}*/

			if($paketInfo->bahasa == "EN")
				$link_email = "lulus_pendaftaran_en";
			else
				$link_email = "lulus_pendaftaran";
				// echo base_url()."main/loadUrl/email/".$link_email."/".$reqId."/".$reqPaketId; die();
				// $body = file_get_contents(base_url()."main/loadUrl/email/".$link_email."/".$reqId."/".$reqPaketRekananId);
				// Update ikun 20180809
			$body = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/email/".$link_email."/".$reqId."/".$reqPaketId);
			// End Update ikun 20180809
			// echo $body; die();
			$mail->MsgHTML($body);
			//$mail->MsgHTML($message);

			if(!$mail->Send())
			{
				$gagal_kirim .= $reqNamaPerusahaan;
			}
			else
			{
				$paket_rekanan_update = new PaketRekanan();
				$paket_rekanan_update->setField("FIELD", "DI_EMAIL");
				$paket_rekanan_update->setField("FIELD_VALUE", 2);
				$paket_rekanan_update->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
				$paket_rekanan_update->update();
				unset($paket_rekanan_update);
			}

			unset($body);
		}

		if($gagal_kirim == "")
			echo "Email berhasil dikirim.";
		else
			echo "Email gagal dikirim.";

	}


	function set_evaluasi_kualifikasi_administrasi()
	{

		$this->load->model("PaketRekanan");
		$paket_rekanan = new PaketRekanan();

		$reqId = $this->input->get("reqId");

		$paket_rekanan->setField("FIELD", "LULUS_ADMINISTRASI");
		$paket_rekanan->setField("FIELD_VALUE", "(SELECT CASE WHEN COALESCE(LULUS_ADMINISTRASI, 0) = 0 THEN 1 ELSE 0 END LULUS_ADMINISTRASI FROM PAKET_REKANAN X WHERE X.PAKET_REKANAN_ID = A.PAKET_REKANAN_ID)");
		$paket_rekanan->setField("PAKET_REKANAN_ID", $reqId);
		if($paket_rekanan->update())
			echo "Data berhasil disimpan.";

	}

	function set_evaluasi_kualifikasi_rekapitulasi()
	{

		$this->load->model("PaketRekanan");
		$paket_rekanan = new PaketRekanan();
		// echo "<pre>"; print_r($this->input->post()); die();

		$submitSimpan = $this->input->post("submitSimpan");
		$reqPaketRekananId = $_POST["reqPaketRekananId"];
		$reqCatatan = $_POST["reqCatatan"];
		// $reqLulus = $_POST["reqLulus"];
		// $reqKeterangan = $_POST["reqKeterangan"];

		if($submitSimpan == "Simpan")
		{
			for($i=0;$i<count($reqPaketRekananId);$i++)
			{
				$status = $_POST["reqStatus$reqPaketRekananId[$i]"];
				$paket_rekanan_insert = new PaketRekanan();
				$paket_rekanan_insert->setField("FIELD1", "LULUS_KUALIFIKASI");
				$paket_rekanan_insert->setField("FIELD1_VALUE", $status);
				$paket_rekanan_insert->setField("FIELD2", "LULUS_KUALIFIKASI_KETERANGAN");
				$paket_rekanan_insert->setField("FIELD2_VALUE", $reqCatatan[$i]);
				$paket_rekanan_insert->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$i]);
				$paket_rekanan_insert->updateTwoField();
				unset($paket_rekanan_insert);
			}
			echo 'Data berhasil di simpan.';
		}

	}

	function kirim_penawaran()
	{

		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketRekanan");
		$this->load->model("PaketDokumen");
		$this->load->model("Rekanan");
		$this->load->model("PaketTahap");
		include_once("functions/string.func.php");
		include_once("functions/date.func.php");
		include_once("functions/default.func.php");

		$paket_dokumen = new PaketDokumen();
		$paket_rekanan = new PaketRekanan();
		$paket_tahap = new PaketTahap();
		$paket_tahap_metode = new PaketTahap();
		$rekanan = new Rekanan();

		$reqId = $this->input->post("reqId");
		$reqToken = $this->input->post("reqToken");
		$submitSimpan= $this->input->post('submitSimpan');

		$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);

		$arrDokumenPenawaran            = DOKUMEN_PENAWARAN; // ikn

		$aktif_dok_penawaran1 = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrDokumenPenawaran[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1, "HADIR" => 0));
		if($aktif_dok_penawaran1 == 0) { // waktu nya habis
			echo "1--Waktu upload / update dokumen penawaran telah usai.";
		} else {
			if($submitSimpan == "Simpan")
			{
				$checked = 1;
				if(trim($reqToken) == "")
				{
					$rekanan->selectByParamsSimple(array("A.REKANAN_ID" => $this->ID));
					$rekanan->firstRow();

					echo "1--Isi terlebih dahulu kode dari email yang telah di kirim ke ".$rekanan->getField("EMAIL").".";
					return;
				}
				else
				{
					$paket_rekanan->selectByParamsSimple(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => $this->ID));
					$paket_rekanan->firstRow();
					$reqPaketRekananId = $paket_rekanan->getField("PAKET_REKANAN_ID");
					$reqTokenVerifikasi = $paket_rekanan->getField("KIRIM_PENAWARAN_KODE");
					if($reqToken == $reqTokenVerifikasi || $reqToken == "00000")
					{
						$paket_rekanan->setField("FIELD", "KIRIM_PENAWARAN");
						$paket_rekanan->setField("FIELD_VALUE", "1");
						$paket_rekanan->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
						if($paket_rekanan->update())
						{
							echo "0--Penawaran berhasil di kirim.";
						}
					}
					else
					{
						echo "1--Kode yang anda masukkan tidak sesuai dengan email yang telah di kirim.";
					}
				}

			}

			$paketInfo->getPaket($reqId);
			$reqNama = $paketInfo->nama;
		}
	}

	function nilai_penawaran()
	{
		$this->load->model("PaketRekanan");

		$paket_rekanan = new PaketRekanan();

		$reqId = $this->input->post("reqId");
		$submitSimpan = $this->input->post("submitSimpan");
		$reqDataPenawaranHarga = $_POST["reqDataPenawaranHarga"];
		$reqPaketRekananId = $_POST["reqPaketRekananId"];

		if($submitSimpan == "Simpan")
		{
			for($i=0; $i<count($reqPaketRekananId);$i++)
			{
				$paket_rekanan_insert = new PaketRekanan();
				$paket_rekanan_insert->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$i]);
				$paket_rekanan_insert->setField("FIELD", "NILAI_PENAWARAN");
				$paket_rekanan_insert->setField("FIELD_VALUE", coalesce(dotToNo($reqDataPenawaranHarga[$i]), "NULL"));
				$paket_rekanan_insert->update();
				unset($paket_rekanan_insert);
			}

			echo 'Data berhasil di simpan.';
		}
	}


	function kelengkapan_sampul1()
	{
		$this->load->model("PaketRekanan");

		$paket_rekanan = new PaketRekanan();

		$reqId = $this->input->post("reqId");
		$reqNilaiEstimate = $this->input->post("reqNilaiEstimate");
		$reqKelengkapanDokumen  = $_POST["reqKelengkapanDokumen"];
		$reqKelengkapanDokumenAlasan  = $_POST["reqKelengkapanDokumenAlasan"];
		$reqPaketRekananId = $_POST["reqPaketRekananId"];
		$submitSimpan = $this->input->post("submitSimpan");

		if($submitSimpan == "Simpan")
		{
			for($i=0; $i<count($reqKelengkapanDokumen);$i++)
			{
				$paket_rekanan_insert = new PaketRekanan();
				$paket_rekanan_insert->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$i]);
				$paket_rekanan_insert->setField("FIELD1", "KIRIM_PENAWARAN_LENGKAP");
				$paket_rekanan_insert->setField("FIELD1_VALUE", $reqKelengkapanDokumen[$i]);
				$paket_rekanan_insert->setField("FIELD2", "KIRIM_PENAWARAN_ALASAN");
				$paket_rekanan_insert->setField("FIELD2_VALUE", $reqKelengkapanDokumenAlasan[$i]);
				$paket_rekanan_insert->updateTwoField();
				unset($paket_rekanan_insert);
			}

			echo 'Data berhasil di simpan';
		}
	}

	function evaluasi_penawaran_rekapitulasi()
	{

		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		include_once("functions/string.func.php");
		include_once("functions/default.func.php");
		include_once("functions/date.func.php");
		$this->load->model("Paket");
		$this->load->model("PaketRekanan");
		$this->load->model("RekananEvaluasiTeknisTawar");
		$this->load->model("RekananEvaluasiAdminTawar");
		$this->load->model("RekananEvaluasiHargaTawar");
		$this->load->model("MatrixEvaluasi");

		$paket_rekanan = new PaketRekanan();
		$paket_rekanan_nilai = new PaketRekanan();
		$matrix_evaluasi = new MatrixEvaluasi();

		$reqId = $this->input->post("reqId");
		$submitSimpan = $this->input->post("submitSimpan");
		$reqUndangan = $this->input->post("reqUndangan");
		$reqEvaluasiPenilaian = $_POST["reqEvaluasiPenilaian"];
		$reqPaketRekananUrutId = $_POST["reqPaketRekananUrutId"];
		$reqUrutan = $_POST["reqUrutan"];
		$reqEvaluasiPenilaianKeterangan = $_POST["reqEvaluasiPenilaianKeterangan"];
		$reqPaketRekananId = $_POST["reqPaketRekananId"];
		$reqPaketRekananUrutArray =unserialize(stripslashes($_POST['reqPaketRekananUrutArray']));
		if($submitSimpan == "Simpan")
		{
			if (count($reqPaketRekananId) > 0) {
				for($i=0;$i<count($reqPaketRekananId);$i++)
				{
					$paket_rekanan_insert = new PaketRekanan();
					$paket_rekanan_insert->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$i]);
					$paket_rekanan_insert->setField("FIELD", "LULUS_PENAWARAN");
					$paket_rekanan_insert->setField("FIELD_VALUE", $reqEvaluasiPenilaian[$i]);
					$paket_rekanan_insert->update();
					unset($paket_rekanan_insert);
				}
			}
			if (count($reqPaketRekananUrutId) > 0) {
				for($i=0;$i<count($reqPaketRekananUrutId);$i++)
				{
					if($reqPaketRekananUrutId[$i]) {
						$paket_rekanan_insert = new PaketRekanan();
						$paket_rekanan_insert->setField("PAKET_REKANAN_ID", $reqPaketRekananUrutId[$i]);
						$paket_rekanan_insert->setField("FIELD", "LULUS_PENAWARAN_URUT");
						$paket_rekanan_insert->setField("FIELD_VALUE", $reqUrutan[$i]);
						$paket_rekanan_insert->update();
						unset($paket_rekanan_insert);

						$paket_rekanan_insert = new PaketRekanan();
						$paket_rekanan_insert->setField("PAKET_REKANAN_ID", $reqPaketRekananUrutId[$i]);
						$paket_rekanan_insert->setField("FIELD", "LULUS_PENAWARAN_KETERANGAN");
						$paket_rekanan_insert->setField("FIELD_VALUE", "'".$reqEvaluasiPenilaianKeterangan[$i]."'");
						$paket_rekanan_insert->update();
						unset($paket_rekanan_insert);
					}
				}
			}

			if ($reqUndangan) {
				$paket = new Paket();
				$paket->setField("FIELD", "REKANAN_ID_PEMENANG");
				$paket->setField("FIELD_VALUE", $reqUndangan);
				$paket->setField("PAKET_ID", $reqId);
				$paket->updateByField();
			}

			echo 'Data berhasil di simpan';

		}


	}


	function evaluasi_penawaran_sampul1()
	{

		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketRekanan");

		$paket_rekanan = new PaketRekanan();
		$paket_rekanan_nilai = new PaketRekanan();
		$reqId = $this->input->post("reqId");
		$submitSimpan = $this->input->post("submitSimpan");
		$reqUndangan = $this->input->post("reqUndangan");
		$reqEvaluasiPenilaian = $_POST["reqEvaluasiPenilaian"];
		$reqPaketRekananUrutId = $_POST["reqPaketRekananUrutId"];
		$reqUrutan = $_POST["reqUrutan"];
		$reqEvaluasiPenilaianKeterangan = $_POST["reqEvaluasiPenilaianKeterangan"];
		$reqPaketRekananId = $_POST["reqPaketRekananId"];

		if($submitSimpan == "Simpan")
		{
			for($i=0;$i<count($reqPaketRekananId);$i++)
			{
				$paket_rekanan_insert = new PaketRekanan();
				$paket_rekanan_insert->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$i]);
				$paket_rekanan_insert->setField("FIELD", "LULUS_PENAWARAN_SAMPUL1");
				$paket_rekanan_insert->setField("FIELD_VALUE", $reqEvaluasiPenilaian[$i]);
				$paket_rekanan_insert->update();
				unset($paket_rekanan_insert);
			}

			echo 'Data berhasil di simpan';
		}

	}

	function evaluasi_penawaran_aritmatika()
	{
		include_once("functions/string.func.php");
		include_once("functions/default.func.php");
		include_once("functions/date.func.php");
		$this->load->model("PaketDokumen");
		$this->load->model("PaketRekanan");
		$this->load->model("RekananPaketPenawaran");
		$this->load->library("FileHandler");
		//include_once("WEB-INF/classes/utils/FileHandler.php");
		// echo "<pre>"; print_r($this->input->post()); die();
		$FILE_DIR = "uploads/aritmatika/";

		$paket_rekanan = new PaketRekanan();
		$paket_dokumen = new PaketDokumen();
		$rekanan_paket_penawaran = new RekananPaketPenawaran();
		$file = new FileHandler();

		$reqId = $this->input->get("reqId");

		$submitSimpan = $this->input->post("submitSimpan");
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqRekananId = $_POST['reqRekananId'];
		$reqPaketRekananId = $_POST['reqPaketRekananId'];
		$reqPaketPenawaranId = $_POST['reqPaketPenawaranId'];
		$reqQuantity = $_POST['reqQuantity'];
		$reqPenawaranKoreksi = $_POST['reqPenawaranKoreksi'];
		$reqUraian = $_POST["reqUraian"];
		$reqPenawaranSebelumnya = $_POST["reqPenawaranSebelumnya"];

		if($submitSimpan == "Simpan")
		{
			for($i=0;$i<count($reqPaketRekananId);$i++)
			{
				$paket_rekanan = new PaketRekanan();

				$paket_rekanan->setField("FIELD1", "NILAI_PENAWARAN");
				$paket_rekanan->setField("FIELD1_VALUE", dotToNo($reqPenawaranKoreksi[$i]));
				$paket_rekanan->setField("FIELD2", "NILAI_PENAWARAN_SEBELUMNYA");
				$paket_rekanan->setField("FIELD2_VALUE", dotToNo($reqPenawaranSebelumnya[$i]));
				$paket_rekanan->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$i]);
				$paket_rekanan->updateTwoField();

				unset($paket_rekanan);

			}

			for($i=0;$i<count($reqPaketPenawaranId);$i++)
			{

				if($reqQuantity[$i] == "0")
				{}
				else
				{

					for($j=0;$j<count($reqPaketRekananId);$j++)
					{
						$rekanan_paket_penawaran = new RekananPaketPenawaran();

						$rekanan_paket_penawaran->setField("UNIT_PRICE_KOREKSI", dotToNo($this->input->post("reqUnitPriceKoreksi".$reqPaketRekananId[$j]."-".$reqPaketPenawaranId[$i])));
						$rekanan_paket_penawaran->setField("JUMLAH_KOREKSI", dotToNo($this->input->post("reqJumlahKoreksi".$reqPaketRekananId[$j]."-".$reqPaketPenawaranId[$i])));
						$rekanan_paket_penawaran->setField("PAKET_REKANAN_ID", $reqPaketRekananId[$j]);
						$rekanan_paket_penawaran->setField("PAKET_PENAWARAN_ID", $reqPaketPenawaranId[$i]);
						$rekanan_paket_penawaran->updateKoreksi();

						unset($rekanan_paket_penawaran);
					}
				}
			}

			echo "Data berhasil disimpan";
		}
	}

}
?>
