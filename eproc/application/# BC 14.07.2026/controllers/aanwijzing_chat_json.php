<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

include_once("functions/string.func.php");
include_once("functions/date.func.php");

class aanwijzing_chat_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			// trow to unauthenticated page!
			redirect('Login');
		}

		/* GLOBAL VARIABLE */
		$this->USER_LOGIN_ID = $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
		$this->REKANAN_KODE = $this->kauth->getInstance()->getIdentity()->REKANAN_KODE;
	}

	function dokumen_aanwijzing_rekanan()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketAanwijzing");
		$this->load->model("PaketTahap");
		$this->load->library("FileHandler");

		$paket_tahap = new PaketTahap();
		$paket_tahap_metode = new PaketTahap();
		$paket_aanwijzing = new PaketAanwijzing();
		$file = new FileHandler();
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqBayar= 1;
		$reqDokumenId = $this->input->post('reqDokumenId');
		$submitSimpan= $this->input->post('submitSimpan');

		$FILE_DIR = "uploads/aanwijzing/";

		$arrAanwijzing = AANWIJZING;
		
		$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
		$aktif_aanwitzing = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrAanwijzing[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
		$aktif_aanwitzing2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrAanwijzing[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
		if($aktif_aanwitzing > 0 && $aktif_aanwitzing2 < 1 )
		{
		  $cekAktif = 1;
		} else {
		  $cekAktif = 0;
		}

		if ($cekAktif == '0') {
			echo "Aanwijzing belum di mulai atau sudah selesai";
		} else {
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
			$paket_aanwijzing->setField("PAKET_ID", $reqId);
			$paket_aanwijzing->setField("KETERANGAN", $reqKeterangan);
			$paket_aanwijzing->setField("REKANAN_USER_ID", $this->ID);
			$paket_aanwijzing->setField("REKANAN_KODE", $this->REKANAN_KODE);
			$paket_aanwijzing->setField("STATUS", (int)$reqBayar);
			$paket_aanwijzing->setField("PARENT_ID", 0);
			$paket_aanwijzing->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$paket_aanwijzing->insertRekanan();

			echo "Data berhasil disimpan";
		}
	}

	function dokumen_aanwijzing_tanggapan()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketAanwijzing");
		$this->load->library("FileHandler");

		$paket_aanwijzing = new PaketAanwijzing();
		$file = new FileHandler();
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqBayar= 1;
		$reqDokumenId = $this->input->post('reqPaketDokumenId'); 
		$reqRekananUserId= $this->input->post('reqRekananUserId');
		$submitSimpan= $this->input->post('submitSimpan');

		$FILE_DIR = "uploads/aanwijzing/";

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

		$paket_aanwijzing->setField("PAKET_ID", $reqId);
		$paket_aanwijzing->setField("NAMA", 'Dokumen aanwijzing');
		$paket_aanwijzing->setField("UKURAN", $insertLinkFilesSize);
		$paket_aanwijzing->setField("TIPE", $insertLinkFilesExe);
		$paket_aanwijzing->setField("PATH_FILE", $insertLinkFile); 
		$paket_aanwijzing->setField("KETERANGAN", $reqKeterangan);
		$paket_aanwijzing->setField("REKANAN_USER_ID", $reqRekananUserId);
		$paket_aanwijzing->setField("STATUS", (int)$reqBayar);
		$paket_aanwijzing->setField("PARENT_ID", (int)$reqDokumenId);
		$paket_aanwijzing->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
		$paket_aanwijzing->setField('CREATED_BY', $this->USER_LOGIN_ID);

		if ($insertLinkFilesSize) {
			$paket_aanwijzing->insert();
		} else {
			$paket_aanwijzing->insertNoFile();
			// $paket_aanwijzing->insert();
		}

		echo "Data berhasil disimpan";
	}

	function dokumen_aanwijzing_tanggapan_edit()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketAanwijzing");
		$this->load->library("FileHandler");

		$paket_aanwijzing = new PaketAanwijzing();
		$file = new FileHandler();
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqBayar= 1;
		$reqPaketAanwijzingId= $this->input->post('reqPaketAanwijzingId');
		$submitSimpan= $this->input->post('submitSimpan');

		$FILE_DIR = "uploads/aanwijzing/";

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

		$paket_aanwijzing->setField("PAKET_ID", $reqId);
		$paket_aanwijzing->setField("NAMA", 'Dokumen aanwijzing');
		$paket_aanwijzing->setField("UKURAN", $insertLinkFilesSize);
		$paket_aanwijzing->setField("TIPE", $insertLinkFilesExe);
		$paket_aanwijzing->setField("PATH_FILE", $insertLinkFile); 
		$paket_aanwijzing->setField("KETERANGAN", $reqKeterangan);
		$paket_aanwijzing->setField("REKANAN_USER_ID", $reqRekananUserId);
		$paket_aanwijzing->setField("STATUS", (int)$reqBayar);
		$paket_aanwijzing->setField("PARENT_ID", (int)$reqDokumenId);
		$paket_aanwijzing->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
		$paket_aanwijzing->setField("PAKET_AANWIJZING_ID", $reqPaketAanwijzingId);
		$paket_aanwijzing->setField('CREATED_BY', $this->USER_LOGIN_ID);

		if ($insertLinkFilesSize) {
			$paket_aanwijzing->updateTanggapan();
		} else {
			$paket_aanwijzing->updateTanggapanNoFile();
			// $paket_aanwijzing->insert();
		}

		echo "Data berhasil disimpan";
	}

	function dokumen_aanwijzing_kualifikasi_rekanan()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketAanwijzing");
		$this->load->model("PaketTahap");
		$this->load->library("FileHandler");

		$paket_tahap = new PaketTahap();
		$paket_tahap_metode = new PaketTahap();
		$paket_aanwijzing = new PaketAanwijzing();
		$file = new FileHandler();
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqBayar= 1;
		$reqDokumenId = $this->input->post('reqDokumenId');
		$submitSimpan= $this->input->post('submitSimpan');
		$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);


		$FILE_DIR = "uploads/aanwijzing/";
		$arrAanwijzing = AANWIJZING_KUALIFIKASI;

		$aktif_aanwitzing = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrAanwijzing[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
		$aktif_aanwitzing2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrAanwijzing[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
		if($aktif_aanwitzing > 0 && $aktif_aanwitzing2 < 1 )
		{
		  $cekAktif = 1;
		} else {
		  $cekAktif = 0;
		}

		if ($cekAktif == '0') {
			echo "Aanwijzing belum di mulai atau sudah selesai";
		} else {
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
			$paket_aanwijzing->setField("PAKET_ID", $reqId);
			$paket_aanwijzing->setField("KETERANGAN", $reqKeterangan);
			$paket_aanwijzing->setField("REKANAN_USER_ID", $this->ID);
			$paket_aanwijzing->setField("REKANAN_KODE", $this->REKANAN_KODE);
			$paket_aanwijzing->setField("STATUS", (int)$reqBayar);
			$paket_aanwijzing->setField("PARENT_ID", 0);
			$paket_aanwijzing->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$paket_aanwijzing->insertKualifikasiRekanan();

			echo "Data berhasil disimpan";
		} 
	}

	function dokumen_aanwijzing_kualifikasi_tanggapan()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketAanwijzing");
		$this->load->library("FileHandler");

		$paket_aanwijzing = new PaketAanwijzing();
		$file = new FileHandler();
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqBayar= 1;
		$reqDokumenId = $this->input->post('reqPaketDokumenId'); 
		$reqRekananUserId= $this->input->post('reqRekananUserId');
		$submitSimpan= $this->input->post('submitSimpan');

		$FILE_DIR = "uploads/aanwijzing/";

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

		$paket_aanwijzing->setField("PAKET_ID", $reqId);
		$paket_aanwijzing->setField("NAMA", 'Dokumen aanwijzing');
		$paket_aanwijzing->setField("UKURAN", $insertLinkFilesSize);
		$paket_aanwijzing->setField("TIPE", $insertLinkFilesExe);
		$paket_aanwijzing->setField("PATH_FILE", $insertLinkFile); 
		$paket_aanwijzing->setField("KETERANGAN", $reqKeterangan);
		$paket_aanwijzing->setField("REKANAN_USER_ID", $reqRekananUserId);
		$paket_aanwijzing->setField("STATUS", (int)$reqBayar);
		$paket_aanwijzing->setField("PARENT_ID", (int)$reqDokumenId);
		$paket_aanwijzing->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
		$paket_aanwijzing->setField('CREATED_BY', $this->USER_LOGIN_ID);

		if ($insertLinkFilesSize) {
			$paket_aanwijzing->insertKualifikasi();
		} else {
			$paket_aanwijzing->insertKualifikasiNoFile();
			// $paket_aanwijzing->insert();
		}

		echo "Data berhasil disimpan";
	}

	// belum di pakai
	function dokumen_aanwijzing_ba()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketAanwijzing");
		$this->load->library("FileHandler");

		$paket_aanwijzing = new PaketAanwijzing();
		$file = new FileHandler();
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqBayar= 11;
		$reqDokumenId = $this->input->post('reqPaketDokumenId'); 
		$reqRekananUserId= $this->input->post('reqRekananUserId');
		$submitSimpan= $this->input->post('submitSimpan');

		$FILE_DIR = "uploads/aanwijzing/";

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

		$paket_aanwijzing->setField("PAKET_ID", $reqId);
		$paket_aanwijzing->setField("NAMA", 'Dokumen aanwijzing');
		$paket_aanwijzing->setField("UKURAN", $insertLinkFilesSize);
		$paket_aanwijzing->setField("TIPE", $insertLinkFilesExe);
		$paket_aanwijzing->setField("PATH_FILE", $insertLinkFile); 
		$paket_aanwijzing->setField("KETERANGAN", $reqKeterangan);
		$paket_aanwijzing->setField("REKANAN_USER_ID", $reqRekananUserId);
		$paket_aanwijzing->setField("STATUS", (int)$reqBayar);
		$paket_aanwijzing->setField("PARENT_ID", (int)$reqDokumenId);
		$paket_aanwijzing->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);

		if ($insertLinkFilesSize) {
			$paket_aanwijzing->insert();
		} else {
			$paket_aanwijzing->insertNoFile();
			// $paket_aanwijzing->insert();
		}

		echo "Data berhasil disimpan";
	}

	function delete()
	{
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketAanwijzing");

		$paket_aanwijzing = new PaketAanwijzing();

		$reqId = $this->input->get("reqId");

		$paket_aanwijzing->setField("PAKET_AANWIJZING_ID", $reqId);
		if($paket_aanwijzing->delete())
		{

		}

	}

	function deletekualifikasi()
	{
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketAanwijzing");

		$paket_aanwijzing = new PaketAanwijzing();

		$reqId = $this->input->get("reqId");

		$paket_aanwijzing->setField("PAKET_AANWIJZING_KUALIFIKASI_ID", $reqId);
		if($paket_aanwijzing->deleteKualifikasi())
		{

		}

	}

	function deletekualifikasi2()
	{
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketAanwijzing");

		$paket_aanwijzing = new PaketAanwijzing();

		$reqId = $this->input->get("reqId");

		$paket_aanwijzing->setField("PAKET_AANWIJZING_KUALIFIKASI_ID", $reqId);
		if($paket_aanwijzing->deleteKualifikasiParentChild())
		{
			$paket_aanwijzing->deletekualifikasi();
		}

	}

	function addAddendum() // Done
	{
		$this->load->model("Aanwijzingaddendum");
		// echo "<pre>"; print_r($this->input->post()); die();
		$reqid	= $this->input->post('reqId');
		$topic 			= $_POST["topic"];
		$topicsemula	= $_POST["topicsemula"];
		$topicmenjadi	= $_POST["topicmenjadi"];

		// hapus data semua dulu kemudian insert
		$aan2	= new Aanwijzingaddendum();
		$aan2->setField("PAKET_ID", $reqid);
		$aan2->delAll();
      	unset($aan2);

		for($i=0; $i<count($topic);$i++)
        {
			$aanWi2	= new Aanwijzingaddendum();
			$aanWi2->setField("PAKET_ID", $reqid);
			$aanWi2->setField("TOPIC", $topic[$i]);
			$aanWi2->setField("TOPIC_SEMULA", $topicsemula[$i]);
			$aanWi2->setField("TOPIC_MENJADI", $topicmenjadi[$i]);
			$aanWi2->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$insert = $aanWi2->insertAanwijzing();
          	unset($aanWi2);
        }

		if ($insert) { echo "Data berhasil disimpan.";
		} else { echo "Data gagal disimpan, silahkan coba kembali."; }
	}

}
?>
