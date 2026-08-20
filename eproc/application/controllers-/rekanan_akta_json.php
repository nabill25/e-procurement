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

class rekanan_akta_json extends CI_Controller {

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
	
	function reload_akta() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("RekananAkta");
		$rekanan_akta = new RekananAkta();
		// set syarat untuk landasan hukum
		$rekanan_akta->selectByParams(array("REKANAN_ID"=>$userLogin->userRekanan, "AKTA_TYPE_ID"=>1),-1,-1);
		$rekanan_akta->firstRow();
		
		$tempNomor= $rekanan_akta->getField("NOMOR");
		$tempTanggal= dateToPageCheck($rekanan_akta->getField("TANGGAL"));
		$tempNotaris= $rekanan_akta->getField("NOTARIS");		
		
		$i = 0;
		$met[$i]['NOMOR'] = $tempNomor;
		$met[$i]['TANGGAL'] = $tempTanggal;
		$met[$i]['NOTARIS'] = $tempNotaris;
		echo json_encode($met);		
	}
	
	function data_administrasi_landasan_hukum_ubah()
	{
		/* INCLUDE FILE */
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("Rekanan");
		$this->load->model("RekananAkta");
		$this->load->library("FileHandler");
		$file = new FileHandler();
		
		/* create objects */
		$rekanan_akta = new RekananAkta();
		$rekanan = new Rekanan();
		
		$reqId				= $this->input->post("reqId");
		$reqAktaType 		= $this->input->post("reqAktaType");
		$reqNamaNotaris		= $this->input->post("reqNamaNotaris");
		$reqTanggal			= $this->input->post("reqTanggal");
		$reqNomorAkta		= $this->input->post("reqNomorAkta");
		$reqNomorKemenkumham = $this->input->post("reqNomorKemenkumham");
		$reqRekananAktaId	= $this->input->post('reqRekananAktaId');
		$reqMode			= $this->input->post('reqMode');
		$reqLinkFile		= $_FILES['reqLinkFile'];
		$reqLinkFileTemp 	= $this->input->post("reqLinkFileTemp");
		$reqLinkFileTempTipe = $this->input->post("reqLinkFileTempTipe");
		$reqLinkFileTempUkuran = $this->input->post("reqLinkFileTempUkuran");
		$reqLinkFileTempNama = $this->input->post("reqLinkFileTempNama"); 
		
		$FILE_DIR = "uploads/landasan_hukum/";
		if($reqMode == 'update')
		{
			
			if($reqAktaType == 3)
			{
				$rekanan_akta->setField("REKANAN_ID",$this->ID);	
				$rekanan_akta->setField("SURAT_KUASA_NOTARIS",$reqNamaNotaris);
				$rekanan_akta->setField("SURAT_KUASA",$reqNomorAkta);
				$rekanan_akta->setField("SURAT_KUASA_TANGGAL",dateToDBCheck($reqTanggal));
				$rekanan_akta->update_rekanan();
			}
			else
			{
			
				/* UPLOAD FILE */	
				$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
				if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
				{
					$insertLinkFilesSize = $file->uploadedSize;
					$insertLinkFilesExe =  $file->uploadedExtension;
					$insertLinkFile =  $renameFile;
					$insertLinkFileNama = $reqLinkFile['name'];
				}
				else
				{
					$insertLinkFilesSize = $reqLinkFileTempUkuran;
					$insertLinkFilesExe =  $reqLinkFileTempTipe;
					$insertLinkFile =  $reqLinkFileTemp;
					$insertLinkFileNama = $reqLinkFileTempNama;
				}
				/* END UPLOAD FILE */			
				
				$rekanan_akta->setField("REKANAN_ID",$this->ID);	
				$rekanan_akta->setField("REKANAN_AKTA_ID",$reqRekananAktaId);
				$rekanan_akta->setField("NOTARIS",$reqNamaNotaris);
				$rekanan_akta->setField("NOMOR",$reqNomorAkta);
				$rekanan_akta->setField("NOMOR_KEMENKUMHAM",$reqNomorKemenkumham);
				$rekanan_akta->setField("TANGGAL",dateToDBCheck($reqTanggal));
				$rekanan_akta->setField("UKURAN", $insertLinkFilesSize);
				$rekanan_akta->setField("TIPE", $insertLinkFilesExe);
				$rekanan_akta->setField("PATH_FILE", $insertLinkFile);
				$rekanan_akta->setField("NAMA_FILE", $insertLinkFileNama);	
				$rekanan_akta->setField('CREATED_BY', $this->USER_LOGIN_ID);	
				
				// if($rekanan_akta->update_landasan())
				// {
				// 	echo "Data berhasil diupdate";
				// }
				// else
				// {
					$rekanan_akta->setField("REKANAN_ID", $this->ID);
					$rekanan_akta->setField("AKTA_TYPE_ID", $reqAktaType);
					$rekanan_akta->setField('STATUS', 1);
					$rekanan_akta->insert();
					echo "Data berhasil disimpan";
				// }
			}
		}
	}
	
	function registrasi()
	{
		/* INCLUDE FILE */
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("Rekanan");
		$this->load->model("RekananAkta");
		$this->load->model("RekananSertifikat");
		$this->load->library("FileHandler");
		$file = new FileHandler();
		
		/* create objects */
		$rekanan_akta = new RekananAkta();
		$rekanan_sertifikat = new RekananSertifikat();
		$rekanan = new Rekanan();
		
		$reqId				= $this->input->post("reqId");
		$reqRekananId		= $this->input->post("reqRekananId");
		$reqAktaType 		= $this->input->post("reqAktaType");
		$reqNamaNotaris		= $this->input->post("reqNamaNotaris");
		$reqTanggal			= $this->input->post("reqTanggal");
		$reqNomorAkta			= $this->input->post("reqNomorAkta");
		$reqRekananAktaId	= $this->input->post('reqRekananAktaId');
		$reqMode			= $this->input->post('reqMode');
		$reqLinkFile		= $_FILES['reqLinkFile'];
		$reqLinkFileTemp 	= $this->input->post("reqLinkFileTemp");
		$reqLinkFileTempTipe = $this->input->post("reqLinkFileTempTipe");
		$reqLinkFileTempUkuran = $this->input->post("reqLinkFileTempUkuran");
		$reqLinkFileTempNama = $this->input->post("reqLinkFileTempNama"); 
		
		$reqNomorAktaPerubahan				= $this->input->post("reqNomorAktaPerubahan");
		$reqTanggalPerubahan				= $this->input->post("reqTanggalPerubahan");
		$reqNamaNotarisPerubahan			= $this->input->post("reqNamaNotarisPerubahan");
		$reqLinkFilePerubahan				= $_FILES['reqLinkFilePerubahan'];
		$reqLinkFilePerubahanTemp 			= $this->input->post("reqLinkFilePerubahanTemp");
		$reqLinkFilePerubahanTempTipe 		= $this->input->post("reqLinkFilePerubahanTempTipe");
		$reqLinkFilePerubahanTempUkuran 	= $this->input->post("reqLinkFilePerubahanTempUkuran");
		$reqLinkFilePerubahanTempNama 		= $this->input->post("reqLinkFilePerubahanTempNama"); 
		$reqRekananAktaIdPerubahan			= $this->input->post('reqRekananAktaIdPerubahan');
		
		$reqNomorPengesahan 				= $this->input->post("reqNomorPengesahan"); 
		$reqTanggalPengesahan 				= $this->input->post("reqTanggalPengesahan"); 
		$reqTanggalBerlakuPengesahan 		= $this->input->post("reqTanggalBerlakuPengesahan");
		$reqLinkFilePengesahan				= $_FILES['reqLinkFilePengesahan'];
		$reqLinkFilePengesahanTemp 			= $this->input->post("reqLinkFilePengesahanTemp");
		$reqLinkFilePengesahanTempTipe 		= $this->input->post("reqLinkFilePengesahanTempTipe");
		$reqLinkFilePengesahanTempUkuran 	= $this->input->post("reqLinkFilePengesahanTempUkuran");
		$reqLinkFilePengesahanTempNama 		= $this->input->post("reqLinkFilePengesahanTempNama");  
		$reqPengesahanSertifikatId			= $this->input->post("reqPengesahanSertifikatId");
		
		$reqNomorDomisili 				= $this->input->post("reqNomorDomisili"); 
		$reqTanggalDomisili 				= $this->input->post("reqTanggalDomisili"); 
		$reqTanggalBerlakuDomisili 		= $this->input->post("reqTanggalBerlakuDomisili");
		$reqLinkFileDomisili				= $_FILES['reqLinkFileDomisili'];
		$reqLinkFileDomisiliTemp 			= $this->input->post("reqLinkFileDomisiliTemp");
		$reqLinkFileDomisiliTempTipe 		= $this->input->post("reqLinkFileDomisiliTempTipe");
		$reqLinkFileDomisiliTempUkuran 	= $this->input->post("reqLinkFileDomisiliTempUkuran");
		$reqLinkFileDomisiliTempNama 		= $this->input->post("reqLinkFileDomisiliTempNama");  
		$reqDomisiliId						= $this->input->post("reqDomisiliId"); 
		
		$reqNomorTandaDaftar 				= $this->input->post("reqNomorTandaDaftar"); 
		$reqTanggalTandaDaftar 				= $this->input->post("reqTanggalTandaDaftar"); 
		$reqTanggalBerlakuTandaDaftar 		= $this->input->post("reqTanggalBerlakuTandaDaftar");
		$reqLinkFileTandaDaftar				= $_FILES['reqLinkFileTandaDaftar'];
		$reqLinkFileTandaDaftarTemp 			= $this->input->post("reqLinkFileTandaDaftarTemp");
		$reqLinkFileTandaDaftarTempTipe 		= $this->input->post("reqLinkFileTandaDaftarTempTipe");
		$reqLinkFileTandaDaftarTempUkuran 	= $this->input->post("reqLinkFileTandaDaftarTempUkuran");
		$reqLinkFileTandaDaftarTempNama 		= $this->input->post("reqLinkFileTandaDaftarTempNama");  
		$reqTandaDaftarId						= $this->input->post("reqTandaDaftarId"); 
		
		$FILE_DIR = "uploads/landasan_hukum/";
		
		if($reqRekananAktaId== '')
		{
			/* AKTA PENDIRIAN */
			$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
			if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
			{
				$insertLinkFilesSize = $file->uploadedSize;
				$insertLinkFilesExe =  $file->uploadedExtension;
				$insertLinkFile =  $renameFile;
				$insertLinkFileNama = $reqLinkFile['name'];
			}
			else
			{
				$insertLinkFilesSize = $reqLinkFileTempUkuran;
				$insertLinkFilesExe =  $reqLinkFileTempTipe;
				$insertLinkFile =  $reqLinkFileTemp;
				$insertLinkFileNama = $reqLinkFileTempNama;
			}
			/* END UPLOAD FILE */			
			
			$rekanan_akta->setField("REKANAN_ID", $this->ID);	
			$rekanan_akta->setField("REKANAN_AKTA_ID",$reqRekananAktaId);
			$rekanan_akta->setField("NOTARIS",setQuote($reqNamaNotaris),1);
			$rekanan_akta->setField("NOMOR",$reqNomorAkta);
			$rekanan_akta->setField("TANGGAL",dateToDBCheck($reqTanggal));
			$rekanan_akta->setField("UKURAN", $insertLinkFilesSize);
			$rekanan_akta->setField("TIPE", $insertLinkFilesExe);
			$rekanan_akta->setField("PATH_FILE", $insertLinkFile);
			$rekanan_akta->setField("NAMA_FILE", $insertLinkFileNama);
			$rekanan_akta->setField("AKTA_TYPE_ID", 1);
			$rekanan_akta->setField('STATUS', 1);
			$rekanan_akta->insert();
		}
		else
		{
			/* AKTA PENDIRIAN */
			$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
			if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
			{
				$insertLinkFilesSize = $file->uploadedSize;
				$insertLinkFilesExe =  $file->uploadedExtension;
				$insertLinkFile =  $renameFile;
				$insertLinkFileNama = $reqLinkFile['name'];
			}
			else
			{
				$insertLinkFilesSize = $reqLinkFileTempUkuran;
				$insertLinkFilesExe =  $reqLinkFileTempTipe;
				$insertLinkFile =  $reqLinkFileTemp;
				$insertLinkFileNama = $reqLinkFileTempNama;
			}
			/* END UPLOAD FILE */			
			
			$rekanan_akta->setField("REKANAN_ID", $this->ID);	
			$rekanan_akta->setField("REKANAN_AKTA_ID",$reqRekananAktaId);
			$rekanan_akta->setField("NOTARIS",setQuote($reqNamaNotaris),1);
			$rekanan_akta->setField("NOMOR",$reqNomorAkta);
			$rekanan_akta->setField("TANGGAL",dateToDBCheck($reqTanggal));
			$rekanan_akta->setField("UKURAN", $insertLinkFilesSize);
			$rekanan_akta->setField("TIPE", $insertLinkFilesExe);
			$rekanan_akta->setField("PATH_FILE", $insertLinkFile);
			$rekanan_akta->setField("NAMA_FILE", $insertLinkFileNama);
			$rekanan_akta->setField("AKTA_TYPE_ID", 1);
			$rekanan_akta->setField('STATUS', 1);
			$rekanan_akta->update_landasan();
		}

		if($reqRekananAktaIdPerubahan== '')
		{
			/* AKTA PERUBAHAN */
			$renameFile = md5(date("dmYHis").$reqLinkFilePerubahan['name'].$this->ID).".".getExtension($reqLinkFilePerubahan['name']);
			if($file->uploadToDir('reqLinkFilePerubahan', $FILE_DIR, $renameFile))
			{
				$insertLinkFilesPerubahanSize = $file->uploadedSize;
				$insertLinkFilesPerubahanExe =  $file->uploadedExtension;
				$insertLinkFilePerubahan =  $renameFile;
				$insertLinkFilePerubahanNama = $reqLinkFilePerubahan['name'];
			}
			else
			{
				$insertLinkFilesPerubahanSize = $reqLinkFilePerubahanTempUkuran;
				$insertLinkFilesPerubahanExe =  $reqLinkFilePerubahanTempTipe;
				$insertLinkFilePerubahan =  $reqLinkFilePerubahanTemp;
				$insertLinkFilePerubahanNama = $reqLinkFilePerubahanTempNama;
			}
			/* END UPLOAD FILE */			
			
			$rekanan_akta->setField("REKANAN_ID", $this->ID);	
			$rekanan_akta->setField("REKANAN_AKTA_ID",$reqRekananAktaIdPerubahan);
			$rekanan_akta->setField("NOTARIS",setQuote($reqNamaNotarisPerubahan),1);
			$rekanan_akta->setField("NOMOR",$reqNomorAktaPerubahan);
			$rekanan_akta->setField("TANGGAL",dateToDBCheck($reqTanggalPerubahan));
			$rekanan_akta->setField("UKURAN", valToNull($insertLinkFilesPerubahanSize));
			$rekanan_akta->setField("TIPE", $insertLinkFilesPerubahanExe);
			$rekanan_akta->setField("PATH_FILE", $insertLinkFilePerubahan);
			$rekanan_akta->setField("NAMA_FILE", $insertLinkFilePerubahanNama);
			$rekanan_akta->setField("AKTA_TYPE_ID", 2);
			$rekanan_akta->setField('STATUS', 1);
			$rekanan_akta->insert();
			
		}
		else
		{
			/* AKTA PERUBAHAN */
			$renameFile = md5(date("dmYHis").$reqLinkFilePerubahan['name'].$this->ID).".".getExtension($reqLinkFilePerubahan['name']);
			if($file->uploadToDir('reqLinkFilePerubahan', $FILE_DIR, $renameFile))
			{
				$insertLinkFilesPerubahanSize = $file->uploadedSize;
				$insertLinkFilesPerubahanExe =  $file->uploadedExtension;
				$insertLinkFilePerubahan =  $renameFile;
				$insertLinkFilePerubahanNama = $reqLinkFilePerubahan['name'];
			}
			else
			{
				$insertLinkFilesPerubahanSize = $reqLinkFilePerubahanTempUkuran;
				$insertLinkFilesPerubahanExe =  $reqLinkFilePerubahanTempTipe;
				$insertLinkFilePerubahan =  $reqLinkFilePerubahanTemp;
				$insertLinkFilePerubahanNama = $reqLinkFilePerubahanTempNama;
			}
			/* END UPLOAD FILE */			
			
			$rekanan_akta->setField("REKANAN_ID", $this->ID);	
			$rekanan_akta->setField("REKANAN_AKTA_ID",$reqRekananAktaIdPerubahan);
			$rekanan_akta->setField("NOTARIS",setQuote($reqNamaNotarisPerubahan),1);
			$rekanan_akta->setField("NOMOR",$reqNomorAktaPerubahan);
			$rekanan_akta->setField("TANGGAL",dateToDBCheck($reqTanggalPerubahan));
			$rekanan_akta->setField("UKURAN", valToNull($insertLinkFilesPerubahanSize));
			$rekanan_akta->setField("TIPE", $insertLinkFilesPerubahanExe);
			$rekanan_akta->setField("PATH_FILE", $insertLinkFilePerubahan);
			$rekanan_akta->setField("NAMA_FILE", $insertLinkFilePerubahanNama);
			$rekanan_akta->setField("AKTA_TYPE_ID", 2);
			$rekanan_akta->setField('STATUS', 1);
			$rekanan_akta->update_landasan();
		}
		
				
		if($reqPengesahanSertifikatId == '')
		{
			$rekanan_sertifikat->setField("REKANAN_ID", $this->ID);	
			$rekanan_sertifikat->setField("REKANAN_SERTIFIKAT_ID",$reqPengesahanSertifikatId);
			$rekanan_sertifikat->setField("NOMOR",$reqNomorPengesahan);
			$rekanan_sertifikat->setField("NAMA", "Pengesahan Badan Usaha");
			$rekanan_sertifikat->setField("SERTIFIKAT_TIPE", "PENGESAHAN_BADAN_USAHA");
			$rekanan_sertifikat->setField("TANGGAL",dateToDBCheck($reqTanggalPengesahan));
			$rekanan_sertifikat->setField("BERLAKU",dateToDBCheck($reqTanggalBerlakuPengesahan));
			
			$renameFile = md5(date("dmYHis").$reqLinkFilePengesahan['name'].$this->ID).".".getExtension($reqLinkFilePengesahan['name']);
			if($file->uploadToDir('reqLinkFilePengesahan', $FILE_DIR, $renameFile))
			{
				$insertLinkFilesPengesahanSize = $file->uploadedSize;
				$insertLinkFilesPengesahanExe =  $file->uploadedExtension;
				$insertLinkFilePengesahan =  $renameFile;
				$insertLinkFilePengesahanNama = $reqLinkFilePengesahan['name'];
			}
			else
			{
				$insertLinkFilesPengesahanSize = $reqLinkFilePengesahanTempUkuran;
				$insertLinkFilesPengesahanExe =  $reqLinkFilePengesahanTempTipe;
				$insertLinkFilePengesahan =  $reqLinkFilePengesahanTemp;
				$insertLinkFilePengesahanNama = $reqLinkFilePengesahanTempNama;
			}
			/* END UPLOAD FILE */		
			$rekanan_sertifikat->setField("UKURAN", $insertLinkFilesPengesahanSize);
			$rekanan_sertifikat->setField("TIPE", $insertLinkFilesPengesahanExe);
			$rekanan_sertifikat->setField("PATH_FILE", $insertLinkFilePengesahan);
			$rekanan_sertifikat->setField("NAMA_FILE", $insertLinkFilePengesahanNama);
			$rekanan_sertifikat->insert();
		}
		else
		{
			
			$rekanan_sertifikat->setField("REKANAN_ID", $this->ID);	
			$rekanan_sertifikat->setField("REKANAN_SERTIFIKAT_ID",$reqPengesahanSertifikatId);
			$rekanan_sertifikat->setField("NOMOR",$reqNomorPengesahan);
			$rekanan_sertifikat->setField("NAMA", "Pengesahan Badan Usaha");
			$rekanan_sertifikat->setField("SERTIFIKAT_TIPE", "PENGESAHAN_BADAN_USAHA");
			$rekanan_sertifikat->setField("TANGGAL",dateToDBCheck($reqTanggalPengesahan));
			$rekanan_sertifikat->setField("BERLAKU",dateToDBCheck($reqTanggalBerlakuPengesahan));
			
			$renameFile = md5(date("dmYHis").$reqLinkFilePengesahan['name'].$this->ID).".".getExtension($reqLinkFilePengesahan['name']);
			if($file->uploadToDir('reqLinkFilePengesahan', $FILE_DIR, $renameFile))
			{
				$insertLinkFilesPengesahanSize = $file->uploadedSize;
				$insertLinkFilesPengesahanExe =  $file->uploadedExtension;
				$insertLinkFilePengesahan =  $renameFile;
				$insertLinkFilePengesahanNama = $reqLinkFilePengesahan['name'];
			}
			else
			{
				$insertLinkFilesPengesahanSize = $reqLinkFilePengesahanTempUkuran;
				$insertLinkFilesPengesahanExe =  $reqLinkFilePengesahanTempTipe;
				$insertLinkFilePengesahan =  $reqLinkFilePengesahanTemp;
				$insertLinkFilePengesahanNama = $reqLinkFilePengesahanTempNama;
			}
			/* END UPLOAD FILE */	
			$rekanan_sertifikat->setField("UKURAN", $insertLinkFilesPengesahanSize);
			$rekanan_sertifikat->setField("TIPE", $insertLinkFilesPengesahanExe);
			$rekanan_sertifikat->setField("PATH_FILE", $insertLinkFilePengesahan);
			$rekanan_sertifikat->setField("NAMA_FILE", $insertLinkFilePengesahanNama);
			$rekanan_sertifikat->update();
		}
		
		if($reqDomisiliId == '')
		{
			$rekanan_sertifikat->setField("REKANAN_ID", $this->ID);	
			$rekanan_sertifikat->setField("REKANAN_SERTIFIKAT_ID",$reqDomisiliId);
			$rekanan_sertifikat->setField("NOMOR",$reqNomorDomisili);
			$rekanan_sertifikat->setField("NAMA", "Surat Domisili");
			$rekanan_sertifikat->setField("SERTIFIKAT_TIPE", "SURAT_DOMISILI");
			$rekanan_sertifikat->setField("TANGGAL",dateToDBCheck($reqTanggalDomisili));
			$rekanan_sertifikat->setField("BERLAKU",dateToDBCheck($reqTanggalBerlakuDomisili));
			
			$renameFile = md5(date("dmYHis").$reqLinkFileDomisili['name'].$this->ID).".".getExtension($reqLinkFileDomisili['name']);
			if($file->uploadToDir('reqLinkFileDomisili', $FILE_DIR, $renameFile))
			{
				$insertLinkFilesDomisiliSize = $file->uploadedSize;
				$insertLinkFilesDomisiliExe =  $file->uploadedExtension;
				$insertLinkFileDomisili =  $renameFile;
				$insertLinkFileDomisiliNama = $reqLinkFileDomisili['name'];
			}
			else
			{
				$insertLinkFilesDomisiliSize = $reqLinkFileDomisiliTempUkuran;
				$insertLinkFilesDomisiliExe =  $reqLinkFileDomisiliTempTipe;
				$insertLinkFileDomisili =  $reqLinkFileDomisiliTemp;
				$insertLinkFileDomisiliNama = $reqLinkFileDomisiliTempNama;
			}
			/* END UPLOAD FILE */		
			$rekanan_sertifikat->setField("UKURAN", $insertLinkFilesDomisiliSize);
			$rekanan_sertifikat->setField("TIPE", $insertLinkFilesDomisiliExe);
			$rekanan_sertifikat->setField("PATH_FILE", $insertLinkFileDomisili);
			$rekanan_sertifikat->setField("NAMA_FILE", $insertLinkFileDomisiliNama);
			$rekanan_sertifikat->insert();
		}
		else
		{
			$rekanan_sertifikat->setField("REKANAN_ID", $this->ID);	
			$rekanan_sertifikat->setField("REKANAN_SERTIFIKAT_ID",$reqDomisiliId);
			$rekanan_sertifikat->setField("NOMOR",$reqNomorDomisili);
			$rekanan_sertifikat->setField("NAMA", "Surat Domisili");
			$rekanan_sertifikat->setField("SERTIFIKAT_TIPE", "SURAT_DOMISILI");
			$rekanan_sertifikat->setField("TANGGAL",dateToDBCheck($reqTanggalDomisili));
			$rekanan_sertifikat->setField("BERLAKU",dateToDBCheck($reqTanggalBerlakuDomisili));
			
			$renameFile = md5(date("dmYHis").$reqLinkFileDomisili['name'].$this->ID).".".getExtension($reqLinkFileDomisili['name']);
			if($file->uploadToDir('reqLinkFileDomisili', $FILE_DIR, $renameFile))
			{
				$insertLinkFilesDomisiliSize = $file->uploadedSize;
				$insertLinkFilesDomisiliExe =  $file->uploadedExtension;
				$insertLinkFileDomisili =  $renameFile;
				$insertLinkFileDomisiliNama = $reqLinkFileDomisili['name'];
			}
			else
			{
				$insertLinkFilesDomisiliSize = $reqLinkFileDomisiliTempUkuran;
				$insertLinkFilesDomisiliExe =  $reqLinkFileDomisiliTempTipe;
				$insertLinkFileDomisili =  $reqLinkFileDomisiliTemp;
				$insertLinkFileDomisiliNama = $reqLinkFileDomisiliTempNama;
			}
			/* END UPLOAD FILE */		
			$rekanan_sertifikat->setField("UKURAN", $insertLinkFilesDomisiliSize);
			$rekanan_sertifikat->setField("TIPE", $insertLinkFilesDomisiliExe);
			$rekanan_sertifikat->setField("PATH_FILE", $insertLinkFileDomisili);
			$rekanan_sertifikat->setField("NAMA_FILE", $insertLinkFileDomisiliNama);
			$rekanan_sertifikat->update();
		}
		
		if($reqTandaDaftarId == '')
		{
			$rekanan_sertifikat->setField("REKANAN_ID", $this->ID);	
			$rekanan_sertifikat->setField("REKANAN_SERTIFIKAT_ID",$reqTandaDaftarId);
			$rekanan_sertifikat->setField("NOMOR",$reqNomorTandaDaftar);
			$rekanan_sertifikat->setField("NAMA", "Tanda Daftar Perusahaan");
			$rekanan_sertifikat->setField("SERTIFIKAT_TIPE", "TANDA_DAFTAR_PERUSAHAAN");
			$rekanan_sertifikat->setField("TANGGAL",dateToDBCheck($reqTanggalTandaDaftar));
			$rekanan_sertifikat->setField("BERLAKU",dateToDBCheck($reqTanggalBerlakuTandaDaftar));
			
			$renameFile = md5(date("dmYHis").$reqLinkFileTandaDaftar['name'].$this->ID).".".getExtension($reqLinkFileTandaDaftar['name']);
			if($file->uploadToDir('reqLinkFileTandaDaftar', $FILE_DIR, $renameFile))
			{
				$insertLinkFilesTandaDaftarSize = $file->uploadedSize;
				$insertLinkFilesTandaDaftarExe =  $file->uploadedExtension;
				$insertLinkFileTandaDaftar =  $renameFile;
				$insertLinkFileTandaDaftarNama = $reqLinkFileTandaDaftar['name'];
			}
			else
			{
				$insertLinkFilesTandaDaftarSize = $reqLinkFileTandaDaftarTempUkuran;
				$insertLinkFilesTandaDaftarExe =  $reqLinkFileTandaDaftarTempTipe;
				$insertLinkFileTandaDaftar =  $reqLinkFileTandaDaftarTemp;
				$insertLinkFileTandaDaftarNama = $reqLinkFileTandaDaftarTempNama;
			}
			/* END UPLOAD FILE */		
			$rekanan_sertifikat->setField("UKURAN", $insertLinkFilesTandaDaftarSize);
			$rekanan_sertifikat->setField("TIPE", $insertLinkFilesTandaDaftarExe);
			$rekanan_sertifikat->setField("PATH_FILE", $insertLinkFileTandaDaftar);
			$rekanan_sertifikat->setField("NAMA_FILE", $insertLinkFileTandaDaftarNama);
			$rekanan_sertifikat->insert();
		}
		else
		{
			$rekanan_sertifikat->setField("REKANAN_ID", $this->ID);	
			$rekanan_sertifikat->setField("REKANAN_SERTIFIKAT_ID",$reqTandaDaftarId);
			$rekanan_sertifikat->setField("NOMOR",$reqNomorTandaDaftar);
			$rekanan_sertifikat->setField("NAMA", "Tanda Daftar Perusahaan");
			$rekanan_sertifikat->setField("SERTIFIKAT_TIPE", "TANDA_DAFTAR_PERUSAHAAN");
			$rekanan_sertifikat->setField("TANGGAL",dateToDBCheck($reqTanggalTandaDaftar));
			$rekanan_sertifikat->setField("BERLAKU",dateToDBCheck($reqTanggalBerlakuTandaDaftar));
			
			$renameFile = md5(date("dmYHis").$reqLinkFileTandaDaftar['name'].$this->ID).".".getExtension($reqLinkFileTandaDaftar['name']);
			if($file->uploadToDir('reqLinkFileTandaDaftar', $FILE_DIR, $renameFile))
			{
				$insertLinkFilesTandaDaftarSize = $file->uploadedSize;
				$insertLinkFilesTandaDaftarExe =  $file->uploadedExtension;
				$insertLinkFileTandaDaftar =  $renameFile;
				$insertLinkFileTandaDaftarNama = $reqLinkFileTandaDaftar['name'];
			}
			else
			{
				$insertLinkFilesTandaDaftarSize = $reqLinkFileTandaDaftarTempUkuran;
				$insertLinkFilesTandaDaftarExe =  $reqLinkFileTandaDaftarTempTipe;
				$insertLinkFileTandaDaftar =  $reqLinkFileTandaDaftarTemp;
				$insertLinkFileTandaDaftarNama = $reqLinkFileTandaDaftarTempNama;
			}
			/* END UPLOAD FILE */		
			$rekanan_sertifikat->setField("UKURAN", $insertLinkFilesTandaDaftarSize);
			$rekanan_sertifikat->setField("TIPE", $insertLinkFilesTandaDaftarExe);
			$rekanan_sertifikat->setField("PATH_FILE", $insertLinkFileTandaDaftar);
			$rekanan_sertifikat->setField("NAMA_FILE", $insertLinkFileTandaDaftarNama);
			$rekanan_sertifikat->update();
		}
		echo "Data berhasil disimpan";	
	}	
}
?>
