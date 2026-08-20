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

class paket_dokumen_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'"); 
		
		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			// trow to unauthenticated page!
			//redirect('Login');
		}       
		
		/* GLOBAL VARIABLE */
		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;   
	}	
	
	function dokumen_pengadaan_upload_rekanan() 
	{
		$this->load->model("PaketDokumen");
		$this->load->library("kauth");  $userLogin = new kauth(); 
		
		$paket_dokumen = new PaketDokumen();
		$file = new FileHandler();
		
		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqDokumenKe = $this->input->post('reqDokumenKe');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqJenisDokumen= $this->input->post('reqJenisDokumen'); 
		$reqLinkFile= $_FILES['reqLinkFile'.$reqDokumenKe];
		$reqBayar= $this->input->post('reqBayar');
		$reqToken= $this->input->post('reqToken');
		$reqDokumenId = $this->input->post('reqDokumenId');
		$submitSimpan= $this->input->post('submitSimpan');
		$FILE_DIR = "uploads/penawaran/";
		
		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$userLogin->userRekanan).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile'.$reqDokumenKe, $FILE_DIR, $renameFile))
		{
			$paket_dokumen->setField("REKANAN_USER_ID", $userLogin->userRekanan);
			$paket_dokumen->setField("PAKET_ID", $reqId);
			$paket_dokumen->setField("NAMA", $reqNamaDokumen);
			$paket_dokumen->setField("UKURAN", $file->uploadedSize);
			$paket_dokumen->setField("TIPE", $file->uploadedExtension);
			$paket_dokumen->setField("PATH_FILE", $file->uploadedFileName);
			$paket_dokumen->setField("JENIS_DOKUMEN", $reqJenisDokumen);
			$paket_dokumen->setField("KETERANGAN", $reqKeterangan);
			$paket_dokumen->setField("FILE_PASSWORD", $reqToken);
			$paket_dokumen->setField("PARENT_ID", 0);
			$paket_dokumen->insert();
			echo "Dokumen berhasil diupload.";
		}
		else
			echo "Dokumen gagal diupload.";
	}
	
	function dokumen_lelang() 
	{
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
		$submitSimpan= $this->input->post('submitSimpan');
		
		$FILE_DIR = "uploads/lelang/";
			
		$paket_dokumen->setField("PAKET_ID", $reqId);
		$paket_dokumen->setField("NAMA", $reqNamaDokumen);
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
		$paket_dokumen->setField("JENIS_DOKUMEN", "LELANG");
		$paket_dokumen->setField("KETERANGAN", $reqKeterangan);
		$paket_dokumen->setField("REKANAN_USER_ID", "NULL");
		$paket_dokumen->setField("STATUS", (int)$reqBayar);
		$paket_dokumen->setField("PARENT_ID", 0);
		$paket_dokumen->setField('CREATED_BY', $this->USER_LOGIN_ID);
		$paket_dokumen->insert(); 

		// Insert Rekam Jejak
        $this->load->library("librekamjejak"); 
        $this->librekamjejak->insertRJ('12',$reqNamaDokumen,$reqId,'null','12'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
        // End Insert Rekam Jejak
		
		echo "Data berhasil disimpan";
	}

	function dokumen_kualifikasi() 
	{
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
		$submitSimpan= $this->input->post('submitSimpan');
		
		$FILE_DIR = "uploads/lelang/";
			
		$paket_dokumen->setField("PAKET_ID", $reqId);
		$paket_dokumen->setField("NAMA", $reqNamaDokumen);
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
		$paket_dokumen->setField("JENIS_DOKUMEN", "KUALIFIKASI");
		$paket_dokumen->setField("KETERANGAN", $reqKeterangan);
		$paket_dokumen->setField("REKANAN_USER_ID", "NULL");
		$paket_dokumen->setField("STATUS", (int)$reqBayar);
		$paket_dokumen->setField("PARENT_ID", 0);
		$paket_dokumen->setField('CREATED_BY', $this->USER_LOGIN_ID);
		$paket_dokumen->insert(); 

		// Insert Rekam Jejak
        $this->load->library("librekamjejak"); 
        $this->librekamjejak->insertRJ('12',$reqNamaDokumen,$reqId,'null','12'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
        // End Insert Rekam Jejak
		
		echo "Data berhasil disimpan";
	}
	
	function paket_lelang_dokumen_aritmatika() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("Paket");
		$this->load->model("PaketDokumen");
		$this->load->model("PaketPenawaran");
		$this->load->library("FileHandler");
		include_once("functions/string.func.php");
		include_once("functions/date.func.php");
		include_once("functions/default.func.php");
		
		$paket_dokumen = new PaketDokumen();
		$paket_dokumen_peserta = new PaketDokumen();
		$paket_nilai = new Paket();
		$paket_penawaran = new PaketPenawaran();
		$file = new FileHandler();
		$file_child = new FileHandler();
		
		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqOE= $this->input->post('reqOE');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqDokumenId = $this->input->post('reqDokumenId');
		$submitSimpan= $this->input->post('submitSimpan');
		$reqLinkFileTemp= $this->input->post('reqLinkFileTemp');
		$reqLinkFileTempTipe= $this->input->post('reqLinkFileTempTipe');
		$reqLinkFileTempUkuran= $this->input->post('reqLinkFileTempUkuran');
		
		
		$FILE_DIR = "uploads/aritmatika/";
			
		if($submitSimpan == "Simpan")
		{		
			$paket_dokumen->setField("PAKET_DOKUMEN_ID", $reqDokumenId);
			if($paket_dokumen->delete())
			{}
			$nama_file = $renameFile;
			$paket_dokumen->setField("PAKET_ID", $reqId);
			$paket_dokumen->setField("NAMA", $reqNamaDokumen);
			
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
			
			$paket_dokumen->setField("JENIS_DOKUMEN", "ARITMATIKA");
			$paket_dokumen->setField("KETERANGAN", $reqKeterangan);
			$paket_dokumen->setField("STATUS", 0);
			$paket_dokumen->setField("REKANAN_USER_ID", "NULL");
			$paket_dokumen->setField("PARENT_ID", 0);
			$paket_dokumen->insert();
			$paket_nilai->setField("PAKET_ID", $reqId);
			$paket_nilai->setField("NILAI_OWNER_ESTIMATE", dotToNo($reqOE));
			$paket_nilai->updateNilaiOwner();
					
			echo "Data berhasil di Simpan";
		}
	}
	
	function upload_validasi()
	{
		ini_set('memory_limit', '-1'); 		
		
		$this->load->model("PaketDokumen");
		$this->load->model("PaketRekanan");
		$this->load->model("PaketTahap");
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		
		$verifyToken = md5('unique' . $_POST['timestamp']);
		if ($_POST['token'] == $verifyToken) 
		{}
		else
			exit;
		
		$paket_rekanan = new PaketRekanan();
		$paket_dokumen = new PaketDokumen();
		$paket_tahap = new PaketTahap();
		$paket_tahap_metode = new PaketTahap();
		$file = new FileHandler();
		
		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqDokumenKe = $this->input->post('reqDokumenKe');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqJenisDokumen= $this->input->post('reqJenisDokumen'); 
		$reqLinkFile= $_FILES['Filedata'];
		$reqBayar= $this->input->post('reqBayar');
		$reqDokumenId = $this->input->post('reqDokumenId');
		$submitSimpan= $this->input->post('submitSimpan');
		$reqPengirim = $this->input->post('reqPengirim');
		
		$FILE_DIR = "uploads/penawaran/";
		
		$paketInfo->getPaket($reqId);
		$paket_rekanan->selectByParamsPaketLelang(array("PAKET_ID" => $reqId, "REKANAN_ID" => $reqPengirim));
		$paket_rekanan->firstRow();		
		
		/* CEK 1 SAMPUL ATAU 2 SAMPUL */
		if($paketInfo->sistem_sampul == "2")
		{
			if($reqJenisDokumen == "PENAWARAN_HARGA")
				$reqToken= md5("2_".$paketInfo->publish_ba_penawaran.$paketInfo->pr_group_number.$paket_rekanan->getField("TANGGAL_DAFTAR_ENKRIPSI").$paket_rekanan->getField("KODE_REKANAN").$reqId.$paketInfo->user_login_id);			
			else
				$reqToken= md5("1_".$paketInfo->publish_ba_penawaran.$paketInfo->pr_group_number.$paket_rekanan->getField("TANGGAL_DAFTAR_ENKRIPSI").$paket_rekanan->getField("KODE_REKANAN").$reqId.$paketInfo->user_login_id);
		}
		else	
			$reqToken= md5($paketInfo->publish_ba_penawaran.$paketInfo->pr_group_number.$paket_rekanan->getField("TANGGAL_DAFTAR_ENKRIPSI").$paket_rekanan->getField("KODE_REKANAN").$reqId.$paketInfo->user_login_id);

		/* VALIDASI WAKTU HABIS */
		$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
		//$arrDokumenPenawaran 	 	 = array(0, 11, 6,  11, 6,  10, 6,  11, 11);
		
		$arrDokumenPenawaran 	 	 = array(0, 11, 6,  11, 6,  10, 6,  11, 11, 0, 0, 11, 6,  11, 6);
		$aktif_dok_penawaran1 = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrDokumenPenawaran[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1, "HADIR" => 0));
		
		if($aktif_dok_penawaran1 == 0)
		{
			echo "Waktu pemasukan / upload penawaran telah berakhir. Dokumen penawaran gagal diupload.";	
			return;
		}
		
		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$reqPengirim).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('Filedata', $FILE_DIR, $renameFile))
		{
			//password for the pdf file
			$password = $reqToken;
			//name of the original file (unprotected)
			$origFile = $FILE_DIR.$renameFile;
			//name of the destination file (password protected and printing rights removed)
			$destFile = $FILE_DIR."enc".$renameFile;
			//encrypt the book and create the protected file
			if(pdfEncrypt($origFile, $password, $destFile ))	
			{
				/**/
				unlink($origFile);
							
				$paket_dokumen->setField("REKANAN_USER_ID", $reqPengirim);
				$paket_dokumen->setField("PAKET_ID", $reqId);
				$paket_dokumen->setField("NAMA", $reqNamaDokumen);
				$paket_dokumen->setField("UKURAN", $file->uploadedSize);
				$paket_dokumen->setField("TIPE", $file->uploadedExtension);
				$paket_dokumen->setField("PATH_FILE", "enc".$renameFile);
				$paket_dokumen->setField("JENIS_DOKUMEN", $reqJenisDokumen);
				$paket_dokumen->setField("KETERANGAN", $reqLinkFile['name']);
				$paket_dokumen->setField("PARENT_ID", 0);
				$paket_dokumen->insert();
				echo "berhasil diupload.";

			}
			else
			{
				unlink($origFile);
				echo "gagal diupload.";
			}
		}
	}
	
	function upload()
	{
		$this->load->model("PaketDokumen");
		$this->load->library("kauth");  $userLogin = new kauth(); 
		
		$paket_dokumen = new PaketDokumen();
		$file = new FileHandler();
		
		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqDokumenKe = $this->input->post('reqDokumenKe');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqJenisDokumen= $this->input->post('reqJenisDokumen'); 
		$reqLinkFile= $_FILES['reqLinkFile'.$reqDokumenKe];
		$reqBayar= $this->input->post('reqBayar');
		$reqDokumenId = $this->input->post('reqDokumenId');
		$submitSimpan= $this->input->post('submitSimpan');
		$FILE_DIR = "uploads/penawaran/";
		$reqToken= md5($userLogin->userRekanan.$reqId);
		
		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$userLogin->userRekanan).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile'.$reqDokumenKe, $FILE_DIR, $renameFile))
		{
			//password for the pdf file
			$password = $reqToken;
			//name of the original file (unprotected)
			$origFile = $FILE_DIR.$renameFile;
			//name of the destination file (password protected and printing rights removed)
			$destFile = $FILE_DIR."enc".$renameFile;
			//encrypt the book and create the protected file
			if(pdfEncrypt($origFile, $password, $destFile ))	
			{
				/**/
				unlink($origFile);
							
				$paket_dokumen->setField("REKANAN_USER_ID", $userLogin->userRekanan);
				$paket_dokumen->setField("PAKET_ID", $reqId);
				$paket_dokumen->setField("NAMA", $reqNamaDokumen);
				$paket_dokumen->setField("UKURAN", $file->uploadedSize);
				$paket_dokumen->setField("TIPE", $file->uploadedExtension);
				$paket_dokumen->setField("PATH_FILE", "enc".$renameFile);
				$paket_dokumen->setField("JENIS_DOKUMEN", $reqJenisDokumen);
				$paket_dokumen->setField("KETERANGAN", $reqLinkFile['name']);
				$paket_dokumen->setField("PARENT_ID", 0);
				$paket_dokumen->insert();
				
				echo "Dokumen berhasil diupload.";

			}
			else
				echo "Dokumen gagal diupload.";
		}
	}
	
	function upload_evaluasi()
	{
		$this->load->model("PaketDokumen");
		$this->load->library("kauth");  $userLogin = new kauth(); 
		
		$paket_dokumen = new PaketDokumen();
		$file = new FileHandler();
		
		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqDokumenKe = $this->input->post('reqDokumenKe');
		$reqRekananId = $this->input->post('reqRekananId');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqJenisDokumen= $this->input->post('reqJenisDokumen'); 
		$reqLinkFile= $_FILES['reqLinkFile'.$reqRekananId];
		$reqBayar= $this->input->post('reqBayar');
		$reqDokumenId = $this->input->post('reqDokumenId');
		$submitSimpan= $this->input->post('submitSimpan');
		$FILE_DIR = "uploads/penawaran/";
		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$userLogin->UID).".".getExtension($reqLinkFile['name']);
		;
		if($file->uploadToDir('reqLinkFile'.$reqRekananId, $FILE_DIR, $renameFile))
		{
							
			$paket_dokumen->setField("REKANAN_USER_ID", $reqRekananId);
			$paket_dokumen->setField("PAKET_ID", $reqId);
			$paket_dokumen->setField("NAMA", $reqNamaDokumen);
			$paket_dokumen->setField("UKURAN", $file->uploadedSize);
			$paket_dokumen->setField("TIPE", $file->uploadedExtension);
			$paket_dokumen->setField("PATH_FILE", $renameFile);
			$paket_dokumen->setField("JENIS_DOKUMEN", $reqJenisDokumen);
			$paket_dokumen->setField("KETERANGAN", $reqLinkFile['name']);
			$paket_dokumen->setField("PARENT_ID", 0);
			$paket_dokumen->deleteByJenisPaketAdmin();
			$paket_dokumen->insert();
			
			echo "Dokumen berhasil diupload.";

		}
	}
	
	function upload_surat_penawaran()
	{
		$this->load->model("PaketDokumen");
		$this->load->library("kauth");  $userLogin = new kauth(); 
		
		$paket_dokumen = new PaketDokumen();
		$file = new FileHandler();
		
		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqDokumenKe = $this->input->post('reqDokumenKe');
		$reqRekananId = $this->input->post('reqRekananId');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqJenisDokumen= $this->input->post('reqJenisDokumen'); 
		$reqLinkFile= $_FILES['reqLinkFile'.$reqRekananId];
		$reqBayar= $this->input->post('reqBayar');
		$reqDokumenId = $this->input->post('reqDokumenId');
		$submitSimpan= $this->input->post('submitSimpan');
		$FILE_DIR = "uploads/penawaran/";
		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$userLogin->UID).".".getExtension($reqLinkFile['name']);
		;
		if($file->uploadToDir('reqLinkFile'.$reqRekananId, $FILE_DIR, $renameFile))
		{
							
			$paket_dokumen->setField("REKANAN_USER_ID", $reqRekananId);
			$paket_dokumen->setField("PAKET_ID", $reqId);
			$paket_dokumen->setField("NAMA", $reqNamaDokumen);
			$paket_dokumen->setField("UKURAN", $file->uploadedSize);
			$paket_dokumen->setField("TIPE", $file->uploadedExtension);
			$paket_dokumen->setField("PATH_FILE", $renameFile);
			$paket_dokumen->setField("JENIS_DOKUMEN", $reqJenisDokumen);
			$paket_dokumen->setField("KETERANGAN", $reqLinkFile['name']);
			$paket_dokumen->setField("PARENT_ID", 0);
			$paket_dokumen->deleteByJenisPaket();
			$paket_dokumen->insert();
			
			echo "Dokumen berhasil diupload.";

		}
	}

	function dokumen_laporan() 
	{
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
		$submitSimpan= $this->input->post('submitSimpan');
		
		$FILE_DIR = "uploads/lelang/";
			
		$paket_dokumen->setField("PAKET_ID", $reqId);
		$paket_dokumen->setField("NAMA", $reqNamaDokumen);
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
		$paket_dokumen->setField("JENIS_DOKUMEN", "LAPORAN_PAKET");
		$paket_dokumen->setField("KETERANGAN", $reqKeterangan);
		$paket_dokumen->setField("REKANAN_USER_ID", "NULL");
		$paket_dokumen->setField("STATUS", (int)$reqBayar);
		$paket_dokumen->setField("PARENT_ID", 0);
		$paket_dokumen->insert(); 

		// Insert Rekam Jejak
        $this->load->library("librekamjejak"); 
        $this->librekamjejak->insertRJ('27',$reqNamaDokumen,$reqId,'null','27'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
        // End Insert Rekam Jejak
		
		echo "Data berhasil disimpan";
	}
	
	function delete()
	{
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketDokumen");
		
		$paket_dokumen = new PaketDokumen();
		
		$reqId = $this->input->get("reqId");

		$paket_dokumen->setField("PAKET_DOKUMEN_ID", $reqId);
		if($paket_dokumen->delete())
		{
			
		}		
		
	}
	
}
?>
