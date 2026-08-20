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


class rekanan_ijin_usaha_json extends CI_Controller {

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

	function reload_ijin_usaha()
	{
		$reqIjin = $this->input->get("reqIjin");

		$this->load->library("kauth");  $userLogin = new kauth();

		$this->load->model("RekananIjinUsaha");
		$rekanan_ijin_usaha = new RekananIjinUsaha();
		$rekanan_ijin_usaha->selectByParams(array("REKANAN_ID" => $userLogin->userRekanan, "IJIN_USAHA_ID"=>$reqIjin));
		$rekanan_ijin_usaha->firstRow();


		$tempNomor= $rekanan_ijin_usaha->getField("NO_IJIN");
		$tempTanggal= dateToPageCheck($rekanan_ijin_usaha->getField("TANGGAL"));
		$tempNotaris= $rekanan_ijin_usaha->getField("INSTANSI");

		$i = 0;
		$met[$i]['NOMOR'] = $tempNomor;
		$met[$i]['TANGGAL'] = $tempTanggal;
		$met[$i]['INSTANSI'] = $tempNotaris;
		echo json_encode($met);
	}

	function data_administrasi_ijin_usaha_ubah()
	{
		/* INCLUDE FILE */
		$this->load->model("Rekanan");
		$this->load->model("RekananIjinUsaha");
		$this->load->model("RekananBidangUsaha");
		$this->load->library("FileHandler");
		$file = new FileHandler();

		/* create objects */
		$rekanan_ijin_usaha = new RekananIjinUsaha();
		$rekanan_bidang_usaha = new RekananBidangUsaha();
		$rekanan = new Rekanan();

		/* VARIABLE */
		$submitSimpan	= $this->input->post("submitSimpan");
		$reqBatal	= $this->input->post("reqBatal");
		$reqNomorIjin = $this->input->post('reqNomorIjin');
		$reqTanggalIjin = $this->input->post('reqTanggalIjin');
		$reqTanggalBerakhir = $this->input->post('reqTanggalBerakhir');
		$reqInstansiPemberiIjin = $this->input->post('reqInstansiPemberiIjin');
		$reqId = $this->input->post('reqId');
		$reqBidangUsahaId = $this->input->post("reqBidangUsahaId");
		$jsonBidangUsaha = isset($_POST['jsonBidangUsaha']) ? $_POST['jsonBidangUsaha'] : '';
		$dataBidangUsaha = json_decode($jsonBidangUsaha, true);


		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqLinkFileTemp = $this->input->post("reqLinkFileTemp");
		$reqLinkFileTempTipe = $this->input->post("reqLinkFileTempTipe");
		$reqLinkFileTempUkuran = $this->input->post("reqLinkFileTempUkuran");
		$reqLinkFileTempNama = $this->input->post("reqLinkFileTempNama");

		$reqIjinUsahaId = $this->input->post('reqIjinUsahaId');
		$reqIjinUsaha	= $this->input->post("reqIjinUsaha");
		$reqMode	= $this->input->post("reqMode");

		// Update 17.12.2025
		$reqPKKPR	= $this->input->post("reqPKKPR");
		$reqTanggaPKKPR	= $this->input->post("reqTanggaPKKPR");
		$reqTanggaPKKPRBerakhir	= $this->input->post("reqTanggaPKKPRBerakhir");
		$reqLinkFile2= $_FILES['reqLinkFile2'];
		$reqLinkFile2Temp = $this->input->post("reqLinkFile2Temp");

		$FILE_DIR = "uploads/ijin_usaha/";

		$rekanan_ijin_usaha->setField("NO_IJIN", $reqNomorIjin);
		$rekanan_ijin_usaha->setField("TANGGAL", dateToDBCheck($reqTanggalIjin));
		$rekanan_ijin_usaha->setField("TANGGAL_BERAKHIR", dateToDBCheck($reqTanggalBerakhir));
		$rekanan_ijin_usaha->setField("INSTANSI", $reqInstansiPemberiIjin);
		$rekanan_ijin_usaha->setField("REKANAN_ID", $this->ID);
		$rekanan_ijin_usaha->setField("PKKPR", $reqPKKPR);
		$rekanan_ijin_usaha->setField("TANGGAL_PKKPR", dateToDBCheck($reqTanggaPKKPR));
		$rekanan_ijin_usaha->setField("TANGGAL_PKKPR_BERAKHIR", dateToDBCheck($reqTanggaPKKPRBerakhir));

		$renameFile = "NIB_".md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
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

		$rekanan_ijin_usaha->setField("UKURAN", $insertLinkFilesSize);
		$rekanan_ijin_usaha->setField("TIPE", $insertLinkFilesExe);
		$rekanan_ijin_usaha->setField("NAMA_FILE", $insertLinkFileNama);
		$rekanan_ijin_usaha->setField("PATH_FILE", $insertLinkFile);

		$renameFile2 = "PKKPR_".md5(date("dmYHis").$reqLinkFile2['name'].$this->ID).".".getExtension($reqLinkFile2['name']);
		if($file->uploadToDir('reqLinkFile2', $FILE_DIR, $renameFile2))
		{
			$insertLinkFilesSize2 = $file->uploadedSize;
			$insertLinkFilesExe2 =  $file->uploadedExtension;
			$insertLinkFile2 =  $renameFile2;
			$insertLinkFileNama2 = $reqLinkFile2['name'];
		}
		else
		{
			$insertLinkFilesSize2 = $reqLinkFile2TempUkuran;
			$insertLinkFilesExe2 =  $reqLinkFile2TempTipe;
			$insertLinkFile2 =  $reqLinkFile2Temp;
			$insertLinkFileNama2 = $reqLinkFile2TempNama;
		}
		$rekanan_ijin_usaha->setField("PATH_FILE2", $insertLinkFile2);

		$rekanan_ijin_usaha->setField("IJIN_USAHA_ID", $reqIjinUsaha);
		$rekanan_ijin_usaha->setField('CREATED_BY', $this->USER_LOGIN_ID);

		if($reqMode=="update")		$rekanan_ijin_usaha->update_onedha();
		else					$rekanan_ijin_usaha->insert_onedha();

		$rekanan_bidang_usaha->setField("REKANAN_ID", $this->ID);
		$rekanan_bidang_usaha->delete(" AND IJIN_USAHA_ID = '".$reqIjinUsaha."'");

		foreach ($dataBidangUsaha as $row) {
		    // proses simpan ke database di sini...
				$rekanan_bidang_usaha_insert = new RekananBidangUsaha();
				$rekanan_bidang_usaha_insert->setField("REKANAN_ID", $this->ID);
				$rekanan_bidang_usaha_insert->setField("BIDANG_USAHA_ID", $row['Value']);
				$rekanan_bidang_usaha_insert->setField("IJIN_USAHA_ID", $reqIjinUsaha);
				$rekanan_bidang_usaha_insert->setField('CREATED_BY', $this->USER_LOGIN_ID);
				$rekanan_bidang_usaha_insert->insert_ijin_usaha();
				unset($rekanan_bidang_usaha_insert);
		}

		// if (count($reqBidangUsahaId) > 0) {
		// 	for($i=0;$i<count($reqBidangUsahaId);$i++)
		// 	{
		// 		$rekanan_bidang_usaha_insert = new RekananBidangUsaha();
		// 		$rekanan_bidang_usaha_insert->setField("REKANAN_ID", $this->ID);
		// 		$rekanan_bidang_usaha_insert->setField("BIDANG_USAHA_ID", $reqBidangUsahaId[$i]);
		// 		$rekanan_bidang_usaha_insert->setField("IJIN_USAHA_ID", $reqIjinUsaha);
		// 		$rekanan_bidang_usaha_insert->setField('CREATED_BY', $this->USER_LOGIN_ID);
		// 		$rekanan_bidang_usaha_insert->insert_ijin_usaha();
		// 		unset($rekanan_bidang_usaha_insert);
		// 	}
		// }

		echo "Data berhasil disimpan.";
	}

	function data_administrasi_ijin_usaha_hapus()
	{
		/* INCLUDE FILE */
		$this->load->model("Rekanan");
		$this->load->model("RekananIjinUsaha");
		$this->load->model("RekananBidangUsaha");
		$this->load->library("FileHandler");
		$file = new FileHandler();

		/* create objects */
		$rekanan_ijin_usaha = new RekananIjinUsaha();
		$rekanan_bidang_usaha = new RekananBidangUsaha();
		$rekanan = new Rekanan();

		$reqIjinUsahaId = $this->input->get('reqIjinUsahaId');
		$reqIjinUsaha	= $this->input->get("reqIjinUsaha");
		$reqMode	= $this->input->post("reqMode");

		$rekanan_ijin_usaha->setField("REKANAN_ID", $this->ID);
		$rekanan_ijin_usaha->deleteIjin(" AND IJIN_USAHA_ID = '".$reqIjinUsaha."'");

		$rekanan_bidang_usaha->setField("REKANAN_ID", $this->ID);
		$rekanan_bidang_usaha->delete(" AND IJIN_USAHA_ID = '".$reqIjinUsaha."'");

		// echo "Data berhasil disimpan.";
		redirect(base_url('/main/index/data_administrasi_ijin_usaha'), 'refresh');
	}

	function data_administrasi_sbu_ubah()
	{
		/* INCLUDE FILE */
		$this->load->library("kauth");
		$userLogin = new kauth();
		$this->load->model("Rekanan");
		$this->load->model("RekananIjinUsaha");
		$this->load->model("RekananBidangUsaha");
		$this->load->library("FileHandler");
		$file = new FileHandler();

		/* create objects */
		$rekanan_ijin_usaha = new RekananIjinUsaha();
		$rekanan_bidang_usaha = new RekananBidangUsaha();
		$rekanan = new Rekanan();

		/* VARIABLE */
		$submitSimpan	= $this->input->post("submitSimpan");
		$reqBatal	= $this->input->post("reqBatal");
		$reqNomorIjin = $this->input->post('reqNomorIjin');
		$reqTanggalIjin = $this->input->post('reqTanggalIjin');
		$reqTanggalBerakhir = $this->input->post('reqTanggalBerakhir');
		$reqInstansiPemberiIjin = $this->input->post('reqInstansiPemberiIjin');
		$reqId = $this->input->post('reqId');
		$reqBidangUsahaId = $this->input->post("reqBidangUsahaId");
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqLinkFileTemp = $this->input->post("reqLinkFileTemp");
		$reqLinkFileTempTipe = $this->input->post("reqLinkFileTempTipe");
		$reqLinkFileTempUkuran = $this->input->post("reqLinkFileTempUkuran");
		$reqLinkFileTempNama = $this->input->post("reqLinkFileTempNama");

		$reqIjinUsahaId = $this->input->post('reqIjinUsahaId');
		$reqTipe	= $this->input->post("reqTipe");
		$reqMode	= $this->input->post("reqMode");

		$reqId = $this->ID;

		$rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
		$rekanan->firstRow();

		$FILE_DIR = "uploads/ijin_usaha/";

		$rekanan_ijin_usaha->setField("NO_IJIN", $reqNomorIjin);
		$rekanan_ijin_usaha->setField("TANGGAL", dateToDBCheck($reqTanggalIjin));
		$rekanan_ijin_usaha->setField("TANGGAL_BERAKHIR", dateToDBCheck($reqTanggalBerakhir));
		$rekanan_ijin_usaha->setField("INSTANSI", $reqInstansiPemberiIjin);
		$rekanan_ijin_usaha->setField("REKANAN_ID", $this->ID);
		$rekanan_ijin_usaha->setField("REKANAN_IJIN_USAHA_ID", $reqIjinUsahaId);

		/* UPLOAD FILE */
		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFilesSize = $file->uploadedSize;
			$insertLinkFilesExe =  $file->uploadedExtension;
			$insertLinkFile =  $renameFile;
			$insertLinkFileNama =  $reqLinkFile['name'];
		}
		else
		{
			$insertLinkFilesSize = $reqLinkFileTempUkuran;
			$insertLinkFilesExe =  $reqLinkFileTempTipe;
			$insertLinkFile =  $reqLinkFileTemp;
			$insertLinkFileNama = $reqLinkFileTempNama;
		}
		/* END UPLOAD FILE */
		$rekanan_ijin_usaha->setField("UKURAN", $insertLinkFilesSize);
		$rekanan_ijin_usaha->setField("TIPE", $insertLinkFilesExe);
		$rekanan_ijin_usaha->setField("PATH_FILE", $insertLinkFile);
		$rekanan_ijin_usaha->setField("NAMA_FILE", $insertLinkFileNama);
		$rekanan_ijin_usaha->setField("IJIN_USAHA_ID", 99);

		if($reqMode == 'update')
		{
			$rekanan_ijin_usaha->update_onedhav2();
		}
		else
		{
			$rekanan_ijin_usaha->insert_onedha();
			$reqIjinUsahaId = $rekanan_ijin_usaha->id;
		}

			$rekanan_bidang_usaha->setField("REKANAN_ID", $this->ID);
			$rekanan_bidang_usaha->delete(" AND IJIN_USAHA_ID = '99'");
			// $rekanan_bidang_usaha->delete(" AND REKANAN_BIDANG_USAHA_INFO_ID = '".$reqIjinUsahaId."'");

			for($i=0;$i<count($reqBidangUsahaId);$i++)
			{
				$rekanan_bidang_usaha_insert = new RekananBidangUsaha();
				$rekanan_bidang_usaha_insert->setField("REKANAN_ID", $this->ID);
				$rekanan_bidang_usaha_insert->setField("BIDANG_USAHA_ID", $reqBidangUsahaId[$i]);
				$rekanan_bidang_usaha_insert->setField("IJIN_USAHA_ID", 99);
				$rekanan_bidang_usaha_insert->setField("REKANAN_BIDANG_USAHA_INFO_ID", $reqIjinUsahaId);
				$rekanan_bidang_usaha_insert->insert_sbu();
				unset($rekanan_bidang_usaha_insert);
			}
			echo "Data Berhasil di Simpan";
	}

	function data_administrasi_ijin_usaha_sbu_hapus()
	{
		/* INCLUDE FILE */
		$this->load->model("Rekanan");
		$this->load->model("RekananIjinUsaha");
		$this->load->model("RekananBidangUsaha");
		$this->load->library("FileHandler");
		$file = new FileHandler();

		/* create objects */
		$rekanan_ijin_usaha = new RekananIjinUsaha();
		$rekanan_bidang_usaha = new RekananBidangUsaha();
		$rekanan = new Rekanan();

		$reqIjinUsahaId = $this->input->get('reqIjinUsahaId');
		$reqIjinUsaha	= $this->input->get("reqIjinUsaha");
		$reqMode	= $this->input->post("reqMode");

		$rekanan_ijin_usaha->setField("REKANAN_ID", $this->ID);
		$rekanan_ijin_usaha->deleteIjin(" AND IJIN_USAHA_ID = '".$reqIjinUsaha."'");

		$rekanan_bidang_usaha->setField("REKANAN_ID", $this->ID);
		$rekanan_bidang_usaha->delete(" AND IJIN_USAHA_ID = '".$reqIjinUsaha."'");

		// echo "Data berhasil disimpan.";
		redirect(base_url('/main/index/data_administrasi_sbu'), 'refresh');
	}

	function registrasi()
	{
		/* INCLUDE FILE */
		$this->load->model("Rekanan");
		$this->load->model("RekananIjinUsaha");
		$this->load->model("RekananBidangUsaha");
		$this->load->library("FileHandler");
		$file = new FileHandler();

		/* create objects */
		$rekanan_ijin_usaha = new RekananIjinUsaha();
		$rekanan_bidang_usaha = new RekananBidangUsaha();
		$rekanan = new Rekanan();

		/* VARIABLE */
		$submitSimpan	= $this->input->post("submitSimpan");
		$reqBatal	= $this->input->post("reqBatal");
		$reqNomorIjin = $this->input->post('reqNomorIjin');
		$reqTanggalIjin = $this->input->post('reqTanggalIjin');
		$reqTanggalBerakhir = $this->input->post('reqTanggalBerakhir');
		$reqInstansiPemberiIjin = $this->input->post('reqInstansiPemberiIjin');
		$reqId = $this->input->post('reqId');
		$reqRekananId = $this->input->post("reqRekananId");
		$reqIjinUsahaTemp = $this->input->post("reqIjinUsahaTemp");
		$reqBidangUsahaId = $this->input->post("reqBidangUsahaId");

		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqLinkFileTemp = $this->input->post("reqLinkFileTemp");
		$reqLinkFileTempTipe = $this->input->post("reqLinkFileTempTipe");
		$reqLinkFileTempUkuran = $this->input->post("reqLinkFileTempUkuran");
		$reqLinkFileTempNama = $this->input->post("reqLinkFileTempNama");

		$reqIjinUsahaId = $this->input->post('reqIjinUsahaId');
		$reqIjinUsaha	= $this->input->post("reqIjinUsaha");
		$reqMode	= $this->input->post("reqMode");
		$FILE_DIR = "uploads/ijin_usaha/";
		$rekanan_ijin_usaha->setField("NO_IJIN", $reqNomorIjin);
		$rekanan_ijin_usaha->setField("TANGGAL", dateToDBCheck($reqTanggalIjin));
		$rekanan_ijin_usaha->setField("TANGGAL_BERAKHIR", dateToDBCheck($reqTanggalBerakhir));
		$rekanan_ijin_usaha->setField("INSTANSI", $reqInstansiPemberiIjin);
		$rekanan_ijin_usaha->setField("REKANAN_ID", $this->ID);

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
		$rekanan_ijin_usaha->setField("UKURAN", $insertLinkFilesSize);
		$rekanan_ijin_usaha->setField("TIPE", $insertLinkFilesExe);
		$rekanan_ijin_usaha->setField("NAMA_FILE", $insertLinkFileNama);
		$rekanan_ijin_usaha->setField("PATH_FILE", $insertLinkFile);
		$rekanan_ijin_usaha->setField("IJIN_USAHA_ID", $reqIjinUsaha);


		if($reqMode=="update")
		{
			$rekanan_ijin_usaha->setField("IJIN_USAHA_ID_TEMP", $reqIjinUsahaTemp);
			$rekanan_ijin_usaha->update_registrasi();
		}else
		{
			$rekanan_ijin_usaha->insert_onedha();
		}

		$rekanan_bidang_usaha->setField("REKANAN_ID", $this->ID);
		$rekanan_bidang_usaha->setField("IJIN_USAHA_ID", $reqIjinUsaha);
		$rekanan_bidang_usaha->delete_bidang_usaha_registrasi();
		//echo $rekanan_bidang_usaha->query;exit;

		for($i=0;$i<count($reqBidangUsahaId);$i++)
		{
			if($reqBidangUsahaId[$i] == "")
			{}
			else
			{
				$rekanan_bidang_usaha_insert = new RekananBidangUsaha();
				$rekanan_bidang_usaha_insert->setField("REKANAN_ID", $this->ID);
				$rekanan_bidang_usaha_insert->setField("BIDANG_USAHA_ID", $reqBidangUsahaId[$i]);
				$rekanan_bidang_usaha_insert->setField("IJIN_USAHA_ID",  $reqIjinUsaha);
				$rekanan_bidang_usaha_insert->setField("REKANAN_BIDANG_USAHA_INFO_ID", "NULL");
				$rekanan_bidang_usaha_insert->insert_ijin_usaha();
				unset($rekanan_bidang_usaha_insert);
			}
		}
		echo "Data berhasil disimpan.";
	}

	function registrasi_sbu()
	{
		/* INCLUDE FILE */
		$this->load->library("kauth");
		$userLogin = new kauth();
		$this->load->model("Rekanan");
		$this->load->model("RekananIjinUsaha");
		$this->load->model("RekananBidangUsaha");
		$this->load->library("FileHandler");
		$file = new FileHandler();

		/* create objects */
		$rekanan_ijin_usaha = new RekananIjinUsaha();
		$rekanan_bidang_usaha = new RekananBidangUsaha();
		$rekanan = new Rekanan();

		/* VARIABLE */
		$submitSimpan	= $this->input->post("submitSimpan");
		$reqBatal	= $this->input->post("reqBatal");
		$reqNomorIjin = $this->input->post('reqNomorIjin');
		$reqTanggalIjin = $this->input->post('reqTanggalIjin');
		$reqTanggalBerakhir = $this->input->post('reqTanggalBerakhir');
		$reqInstansiPemberiIjin = $this->input->post('reqInstansiPemberiIjin');
		$reqNamaPemegang = $this->input->post('reqNamaPemegang');
		$reqId = $this->input->post('reqId');
		$reqBidangUsahaId = $this->input->post("reqBidangUsahaId");
		$jsonBidangUsaha = isset($_POST['jsonBidangUsaha']) ? $_POST['jsonBidangUsaha'] : '';
		$dataBidangUsaha = json_decode($jsonBidangUsaha, true);

		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqLinkFileTemp = $this->input->post("reqLinkFileTemp");
		$reqLinkFileTempTipe = $this->input->post("reqLinkFileTempTipe");
		$reqLinkFileTempUkuran = $this->input->post("reqLinkFileTempUkuran");
		$reqLinkFileTempNama = $this->input->post("reqLinkFileTempNama");

		$reqIjinUsahaId = $this->input->post('reqIjinUsahaId');
		$reqTipe		= $this->input->post("reqTipe");
		$reqMode		= $this->input->post("reqMode");
		$reqRekananId	= $this->input->post("reqRekananId");

		$FILE_DIR = "uploads/ijin_usaha/";

		$rekanan_ijin_usaha->setField("NO_IJIN", $reqNomorIjin);
		$rekanan_ijin_usaha->setField("TANGGAL", dateToDBCheck($reqTanggalIjin));
		$rekanan_ijin_usaha->setField("TANGGAL_BERAKHIR", dateToDBCheck($reqTanggalBerakhir));
		$rekanan_ijin_usaha->setField("INSTANSI", $reqInstansiPemberiIjin);
		$rekanan_ijin_usaha->setField("REKANAN_ID", $this->ID);
		$rekanan_ijin_usaha->setField("REKANAN_IJIN_USAHA_ID", $reqIjinUsahaId);
		// $rekanan_ijin_usaha->setField("NAMA_PEMEGANG", $reqNamaPemegang);
		$rekanan_ijin_usaha->setField('CREATED_BY', $this->USER_LOGIN_ID);

		/* UPLOAD FILE */
		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$insertLinkFilesSize = $file->uploadedSize;
			$insertLinkFilesExe =  $file->uploadedExtension;
			$insertLinkFile =  $renameFile;
			$insertLinkFileNama =  $reqLinkFile['name'];
		}
		else
		{
			$insertLinkFilesSize = $reqLinkFileTempUkuran;
			$insertLinkFilesExe =  $reqLinkFileTempTipe;
			$insertLinkFile =  $reqLinkFileTemp;
			$insertLinkFileNama = $reqLinkFileTempNama;
		}
		/* END UPLOAD FILE */
		$rekanan_ijin_usaha->setField("UKURAN", $insertLinkFilesSize);
		$rekanan_ijin_usaha->setField("TIPE", $insertLinkFilesExe);
		$rekanan_ijin_usaha->setField("PATH_FILE", $insertLinkFile);
		$rekanan_ijin_usaha->setField("NAMA_FILE", $insertLinkFileNama);
		$rekanan_ijin_usaha->setField("IJIN_USAHA_ID", 99);

		if($reqMode == 'update')
		{
			$rekanan_ijin_usaha->update_onedhav2();
		}
		else
		{
			$rekanan_ijin_usaha->insert_onedha2();
			$reqIjinUsahaId = $rekanan_ijin_usaha->id;
		}

			$rekanan_bidang_usaha->setField("REKANAN_ID", $this->ID);
			$rekanan_bidang_usaha->delete(" AND IJIN_USAHA_ID = 99");

			foreach ($dataBidangUsaha as $row) {
				$rekanan_bidang_usaha_insert = new RekananBidangUsaha();
				$rekanan_bidang_usaha_insert->setField("REKANAN_ID", $this->ID);
				$rekanan_bidang_usaha_insert->setField("BIDANG_USAHA_ID", $row['Value']);
				$rekanan_bidang_usaha_insert->setField("IJIN_USAHA_ID", 99);
				//$rekanan_bidang_usaha_insert->setField("REKANAN_BIDANG_USAHA_INFO_ID", $reqIjinUsahaId);
				$rekanan_bidang_usaha_insert->setField("REKANAN_BIDANG_USAHA_INFO_ID", "NULL");
				$rekanan_bidang_usaha_insert->setField('CREATED_BY', $this->USER_LOGIN_ID);
				$rekanan_bidang_usaha_insert->insert_sbu();
				unset($rekanan_bidang_usaha_insert);
			}
			echo "Data Berhasil di Simpan";
		}
}
?>
