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

class rekanan_pengalaman_json extends CI_Controller {

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

	function get_data_pengalaman()
	{
		$this->load->model("RekananPengalaman");
		$rekanan_pengalaman = new RekananPengalaman();

		$reqSearch = $this->input->get("reqSearch");

		$met = array();
		$i=0;

		$rekanan_pengalaman->selectByParams(array("REKANAN_ID" => $this->ID), -1, -1, "AND (UPPER(A.NAMA) LIKE '%".strtoupper($reqSearch)."%')");
		while($rekanan_pengalaman->nextRow())
		{
			$met[$i]['id'] = $rekanan_pengalaman->getField('REKANAN_PENGALAMAN_ID');
			$met[$i]['text'] = $rekanan_pengalaman->getField('NAMA');
			$met[$i]['NAMA'] = $rekanan_pengalaman->getField('NAMA');
			$met[$i]['PENGALAMAN_BIDANG'] = $rekanan_pengalaman->getField('PENGALAMAN_BIDANG');
			$met[$i]['LOKASI'] = $rekanan_pengalaman->getField('LOKASI');
			$i++;
		}

		echo json_encode($met);
	}

	function get_data_pengalaman_kualifikasi()
	{
		$this->load->model("RekananPengalaman");
		$rekanan_pengalaman = new RekananPengalaman();

		$reqId = $this->input->get("reqId");

		$met = array();
		$i=0;

		$rekanan_pengalaman->selectByParamsPengalamanKualifikasi($this->ID, $reqId);
		while($rekanan_pengalaman->nextRow())
		{
			$met[$i]['id'] = $rekanan_pengalaman->getField('REKANAN_PENGALAMAN_ID');
			$met[$i]['text'] = $rekanan_pengalaman->getField('NAMA');
			$met[$i]['NAMA'] = $rekanan_pengalaman->getField('NAMA');
			$met[$i]['LOKASI'] = $rekanan_pengalaman->getField('LOKASI');
			$met[$i]['REKANAN_PENGALAMAN_ID'] = $rekanan_pengalaman->getField('REKANAN_PENGALAMAN_ID');
			$met[$i]['BIDANG_USAHA'] = $rekanan_pengalaman->getField('BIDANG_USAHA');
			$met[$i]['KONTRAK_NILAI'] = currencyToPage($rekanan_pengalaman->getField("KONTRAK_NILAI"));
			$met[$i]['JO'] = $rekanan_pengalaman->getField('JO');
			$i++;
		}

		echo json_encode($met);
	}

	function add_progress()
	{
		/* INCLUDE FILE */
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Rekanan");
		$this->load->model("BidangUsaha");
		$this->load->model("RekananPengalaman");
		$this->load->model("RekananPengalamanBidang");

		/* create objects */
		$rekanan = new Rekanan();
		$rekanan_pengalaman	= new RekananPengalaman(); // tipe 0
		$file = new FileHandler();

		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin())
		{
			$userLogin->retrieveUserInfo();
		}

		$reqId= $this->input->post('reqId');
		$reqBidangUsahaId = $_POST["reqBidangUsahaId"];
		$reqNama= $this->input->post('reqNama');
		$reqLokasi= $this->input->post('reqLokasi');
		$reqTgsNama= $this->input->post('reqTgsNama');
		$reqTgsAlamat= $this->input->post('reqTgsAlamat');
		$reqKontrakNo= $this->input->post('reqKontrakNo');
		$reqTanggal= $this->input->post('reqTanggal');
		$reqKontrakNilai= $this->input->post('reqKontrakNilai');
		$reqJOpersen= $this->input->post('reqJOpersen');
		$reqJOket= $this->input->post('reqJOket');
		$reqStatus= $this->input->post('reqStatus');
		$reqSelesaiBA= $this->input->post('reqSelesaiBA');
		$reqProgress= $this->input->post('reqProgress');
		$reqProgressTanggal= $this->input->post('reqProgressTanggal');
		$reqSubmit= $this->input->post('reqSubmit');
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqLinkFileTemp = $this->input->post("reqLinkFileTemp");
		$reqLinkFileTempTipe = $this->input->post("reqLinkFileTempTipe");
		$reqLinkFileTempUkuran = $this->input->post("reqLinkFileTempUkuran");

		$FILE_DIR = "uploads/pengalaman/";

		if($reqSubmit == 'Batal'){
			//header("Location: main/?pg=data_administrasi_umum");
			echo '<script language="javascript">';
			echo "document.location='main/?pg=data_teknis_pengalaman'";
			echo '</script>';
			exit;
		}

		$nextId = 0;

		$cek_file = formatTextToDb($file->getFileName('reqLinkFile'));

		if($reqSubmit == 'Submit')
		{
			$cek = formatTextToDb($file->getFileName('reqLinkFile'));
			if($cek != "")
			{
				$allowed = array(".pdf");	$status_allowed='';
				foreach ($allowed as $file_cek)
				{
					if(!preg_match("/$file_cek\$/i", $_FILES['reqLinkFile']['name']))
					{
						$status_allowed = 'tidak_boleh';
					}
				}
			}

			if($cek_file == ''){
				echo '<script language="javascript">';
				echo "$.jGrowl('Lengkapi file terlebih dahulu, Data gagal disimpan');";
				echo '</script>';
			}elseif($status_allowed == 'tidak_boleh'){
				echo '<script language="javascript">';
				echo "$.jGrowl('File upload harus pdf');";
				echo '</script>';
			}else{
				$rekanan_pengalaman->setField("REKANAN_ID",$userLogin->userRekanan);
				$rekanan_pengalaman->setField("NAMA",$reqNama);
				$rekanan_pengalaman->setField("LOKASI",$reqLokasi);
				$rekanan_pengalaman->setField("PEMBERI_TUGAS",$reqTgsNama);
				$rekanan_pengalaman->setField("PEMBERI_TUGAS_ALAMAT",$reqTgsAlamat);
				$rekanan_pengalaman->setField("KONTRAK_NOMOR",$reqKontrakNo);
				$rekanan_pengalaman->setField("KONTRAK_NILAI",dotToNo($reqKontrakNilai));
				$rekanan_pengalaman->setField("KONTRAK_KETERANGAN",$reqJOket);
				$rekanan_pengalaman->setField("KONTRAK_STATUS",$reqStatus);
				$rekanan_pengalaman->setField("KONTRAK_JO",$reqJOpersen);
				$rekanan_pengalaman->setField("PROGRESS",$reqProgress);

				$rekanan_pengalaman->setField("KONTRAK_TANGGAL",dateToDBCheck($reqTanggal));
				$rekanan_pengalaman->setField("PROGRESS_TANGGAL",dateToDBCheck($reqProgressTanggal));
				$rekanan_pengalaman->setField("BA_TANGGAL",dateToDBCheck($reqSelesaiBA));

				$cek = formatTextToDb($file->getFileName('reqLinkFile'));
				if($cek != "")
				{
					$renameFile = $rekanan_pengalaman->getNextId("REKANAN_PENGALAMAN_ID","REKANAN_PENGALAMAN").formatTextToDb($file->getFileName('reqLinkFile'));
					$varSource=$FILE_DIR.$reqLinkFileTemp;

					if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
					{
						if($reqLinkFileTemp != ''){
							if($file->delete($varSource)){}
						}
						$insertLinkFile = $file->uploadedFileName;
						$insertLinkFilesSize = $file->uploadedSize;
						$insertLinkFilesExe = $file->uploadedExtension;
					}
				}else{
					$insertLinkFile = $reqLinkFileTemp;
					$insertLinkFilesSize = 'NULL';
					$insertLinkFilesExe = $reqLinkFileTempTipe;
				}

				$rekanan_pengalaman->setField("UKURAN", $insertLinkFilesSize);
				$rekanan_pengalaman->setField("TIPE", $insertLinkFilesExe);
				$rekanan_pengalaman->setField("PATH_FILE", $insertLinkFile);

				if($rekanan_pengalaman->insert())
				{
					$idPengalaman = $rekanan_pengalaman->id;

					for($i=0;$i<count($reqBidangUsahaId);$i++)
					{
						$rekanan_bidang_usaha_insert = new RekananPengalamanBidang();
						$rekanan_bidang_usaha_insert->setField("REKANAN_PENGALAMAN_ID", $idPengalaman);
						$rekanan_bidang_usaha_insert->setField("BIDANG_USAHA_ID", $reqBidangUsahaId[$i]);
						$rekanan_bidang_usaha_insert->insert();
						unset($rekanan_bidang_usaha_insert);
					}

					echo '<script language="javascript">';
					echo "document.location='main/?pg=data_teknis_pengalaman'";
					echo '</script>';

					echo '<script language="javascript">';
					echo "$.jGrowl('Data berhasil disimpan');";
					//echo 'window.close();';
					echo '</script>';

					/*echo '<script language="javascript">';
					echo "alert('Data berhasil disimpan')";
					echo '</script>';*/
					$alertMsg .= "Data berhasil diupdate";
				}
				else
				{
					$alertMsg .= "Update failed : ".$rekanan_pengalaman->query;
				}
			}
		}
   }

   function data_teknis_pengalaman_progress_ubah()
   {
		/* INCLUDE FILE */
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Rekanan");
		$this->load->model("RekananPengalaman");
		$this->load->model("RekananPengalamanBidang");
		$this->load->library("FileHandler");
		$file = new FileHandler();

		/* create objects */
		$rekanan = new Rekanan();
		$rekanan_pengalaman	= new RekananPengalaman(); // tipe 0

		$reqPengalamanId= $this->input->post('reqPengalamanId');
		$reqId= $this->input->post('reqId');
		$reqBidangUsahaId = $_POST["reqBidangUsahaId"];
		$reqNama= $this->input->post('reqNama');
		$reqLokasi= $this->input->post('reqLokasi');
		$reqTgsNama= $this->input->post('reqTgsNama');
		$reqTgsAlamat= $this->input->post('reqTgsAlamat');
		$reqKontrakNo= $this->input->post('reqKontrakNo');
		$reqTanggal= $this->input->post('reqTanggal');
		$reqKontrakNilai= $this->input->post('reqKontrakNilai');
		$reqJOpersen= $this->input->post('reqJOpersen');
		$reqJOket= $this->input->post('reqJOket');
		$reqStatus= $this->input->post('reqStatus');
		$reqSelesaiBA= $this->input->post('reqSelesaiBA');
		$reqProgress= $this->input->post('reqProgress');
		$reqProgressTanggal= $this->input->post('reqProgressTanggal');
		$reqSubmit= $this->input->post('reqSubmit');
		$file_list = $_POST["file_list"];
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqLinkFileTemp = $this->input->post("reqLinkFileTemp");
		$reqLinkFileTempTipe = $this->input->post("reqLinkFileTempTipe");
		$reqLinkFileTempUkuran = $this->input->post("reqLinkFileTempUkuran");
		$reqLinkFileTempNama = $this->input->post("reqLinkFileTempNama");
		$reqMode = $this->input->post("reqMode");

		$FILE_DIR = "uploads/pengalaman/";

		if($reqMode =='insert')
		{
			$rekanan_pengalaman->setField("REKANAN_ID",$this->ID);
			$rekanan_pengalaman->setField("NAMA",$reqNama);
			$rekanan_pengalaman->setField("LOKASI",$reqLokasi);
			$rekanan_pengalaman->setField("PEMBERI_TUGAS",$reqTgsNama);
			$rekanan_pengalaman->setField("PEMBERI_TUGAS_ALAMAT",$reqTgsAlamat);
			$rekanan_pengalaman->setField("KONTRAK_NOMOR",$reqKontrakNo);
			$rekanan_pengalaman->setField("KONTRAK_NILAI",dotToNo($reqKontrakNilai));
			$rekanan_pengalaman->setField("KONTRAK_KETERANGAN",$reqJOket);
			$rekanan_pengalaman->setField("KONTRAK_STATUS",$reqStatus);
			$rekanan_pengalaman->setField("KONTRAK_JO",valToNull($reqJOpersen));
			$rekanan_pengalaman->setField("PROGRESS",$reqProgress);

			$rekanan_pengalaman->setField("KONTRAK_TANGGAL",dateToDBCheck($reqTanggal));
			$rekanan_pengalaman->setField("PROGRESS_TANGGAL",dateToDBCheck($reqProgressTanggal));
			$rekanan_pengalaman->setField("BA_TANGGAL",dateToDBCheck($reqSelesaiBA));
			$rekanan_pengalaman->setField("UKURAN_BA", 'NULL');
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
			$rekanan_pengalaman->setField("UKURAN", $insertLinkFilesSize);
			$rekanan_pengalaman->setField("TIPE", $insertLinkFilesExe);
			$rekanan_pengalaman->setField("PATH_FILE", $insertLinkFile);
			$rekanan_pengalaman->setField("NAMA_FILE", $insertLinkFileNama);
			$rekanan_pengalaman->setField('CREATED_BY', $this->USER_LOGIN_ID);

			if($rekanan_pengalaman->insert())
			{
				$idPengalaman = $rekanan_pengalaman->id;
				// $idPengalaman = $reqPengalamanId;

				for($i=0;$i<count($reqBidangUsahaId);$i++)
				{
					$rekanan_bidang_usaha_insert = new RekananPengalamanBidang();
					$rekanan_bidang_usaha_insert->setField("REKANAN_PENGALAMAN_ID", $idPengalaman);
					$rekanan_bidang_usaha_insert->setField("BIDANG_USAHA_ID", $reqBidangUsahaId[$i]);
					$rekanan_bidang_usaha_insert->setField('CREATED_BY', $this->USER_LOGIN_ID);
					$rekanan_bidang_usaha_insert->insert();
					unset($rekanan_bidang_usaha_insert);
				}

				echo "Data Berhasil di Simpan";
			}
			else
			{
				echo "Data Gagal di Simpan";
			}
		}
		else
		{
			$rekanan_pengalaman->setField("REKANAN_PENGALAMAN_ID",$reqPengalamanId);
			$rekanan_pengalaman->setField("NAMA",$reqNama);
			$rekanan_pengalaman->setField("LOKASI",$reqLokasi);
			$rekanan_pengalaman->setField("PEMBERI_TUGAS",$reqTgsNama);
			$rekanan_pengalaman->setField("PEMBERI_TUGAS_ALAMAT",$reqTgsAlamat);
			$rekanan_pengalaman->setField("KONTRAK_NOMOR",$reqKontrakNo);
			$rekanan_pengalaman->setField("KONTRAK_NILAI",dotToNo($reqKontrakNilai));
			$rekanan_pengalaman->setField("KONTRAK_KETERANGAN",$reqJOket);
			$rekanan_pengalaman->setField("KONTRAK_STATUS",$reqStatus);
			$rekanan_pengalaman->setField("KONTRAK_JO",valToNull($reqJOpersen));
			$rekanan_pengalaman->setField("PROGRESS",$reqProgress);

			$rekanan_pengalaman->setField("KONTRAK_TANGGAL",dateToDBCheck($reqTanggal));
			$rekanan_pengalaman->setField("PROGRESS_TANGGAL",dateToDBCheck($reqProgressTanggal));
			$rekanan_pengalaman->setField("BA_TANGGAL",dateToDBCheck($reqSelesaiBA));
			$rekanan_pengalaman->setField("UKURAN_BA", 'NULL');
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
			$rekanan_pengalaman->setField("UKURAN", $insertLinkFilesSize);
			$rekanan_pengalaman->setField("TIPE", $insertLinkFilesExe);
			$rekanan_pengalaman->setField("PATH_FILE", $insertLinkFile);
			$rekanan_pengalaman->setField("NAMA_FILE", $insertLinkFileNama);
			$rekanan_pengalaman->setField("REKANAN_ID",$this->ID);
			$rekanan_pengalaman->setField('CREATED_BY', $this->USER_LOGIN_ID);

			if($rekanan_pengalaman->update())
			{
				$paketBidangUsaha = new RekananPengalamanBidang();
				$paketBidangUsaha->setField('REKANAN_PENGALAMAN_ID', $reqPengalamanId);
				$paketBidangUsaha->delete_parent();
				unset($paketBidangUsaha);
				// $idPengalaman = $rekanan_pengalaman->id;
				$idPengalaman = $reqPengalamanId;

				for($i=0;$i<count($reqBidangUsahaId);$i++)
				{
					$rekanan_bidang_usaha_insert = new RekananPengalamanBidang();
					$rekanan_bidang_usaha_insert->setField("REKANAN_PENGALAMAN_ID", $reqPengalamanId);
					$rekanan_bidang_usaha_insert->setField("BIDANG_USAHA_ID", $reqBidangUsahaId[$i]);
					$rekanan_bidang_usaha_insert->setField('CREATED_BY', $this->USER_LOGIN_ID);
					$rekanan_bidang_usaha_insert->insert();
					unset($rekanan_bidang_usaha_insert);
				}
				echo "Data berhasil di Update";
			}
			else
			{
				echo "Data gagal di Update";
			}
		}
   }

   function data_teknis_pengalaman_selesai_tambah()
   {
	   /* INCLUDE FILE */
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Rekanan");
		$this->load->model("BidangUsaha");
		$this->load->model("RekananPengalaman");
		$this->load->model("RekananPengalamanBidang");

		/* create objects */
		$rekanan = new Rekanan();
		$rekanan_pengalaman	= new RekananPengalaman(); // tipe 0
		$file = new FileHandler();
		$file_ba = new FileHandler();

		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin())
		{
			$userLogin->retrieveUserInfo();
		}

		$reqId= $this->input->post('reqId');
		$reqBidangUsahaId = $_POST["reqBidangUsahaId"];
		//echo $reqBidangUsahaId.'-asdasdasd-';
		$reqNama= $this->input->post('reqNama');
		$reqLokasi= $this->input->post('reqLokasi');
		$reqTgsNama= $this->input->post('reqTgsNama');
		$reqTgsAlamat= $this->input->post('reqTgsAlamat');
		$reqKontrakNo= $this->input->post('reqKontrakNo');
		$reqTanggal= $this->input->post('reqTanggal');
		$reqKontrakNilai= $this->input->post('reqKontrakNilai');
		$reqJOpersen= $this->input->post('reqJOpersen');
		$reqJOket= $this->input->post('reqJOket');
		$reqStatus= $this->input->post('reqStatus');
		$reqSelesaiBA= $this->input->post('reqSelesaiBA');
		$reqProgress= $this->input->post('reqProgress');
		$reqProgressTanggal= $this->input->post('reqProgressTanggal');
		$reqSubmit= $this->input->post('reqSubmit');
		$file_list = $_POST["file_list"];
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqLinkFileTemp = $this->input->post("reqLinkFileTemp");
		$reqLinkFileTempTipe = $this->input->post("reqLinkFileTempTipe");
		$reqLinkFileTempUkuran = $this->input->post("reqLinkFileTempUkuran");
		$reqLinkFileBA= $_FILES['reqLinkFileBA'];
		$reqLinkFileBATemp = $this->input->post("reqLinkFileBATemp");
		$reqLinkFileBATempTipe = $this->input->post("reqLinkFileBATempTipe");
		$reqLinkFileBATempUkuran = $this->input->post("reqLinkFileBATempUkuran");

		$FILE_DIR = "uploads/pengalaman/";

		if($reqSubmit == 'Batal'){
			//header("Location: main/?pg=data_administrasi_umum");
			echo '<script language="javascript">';
			echo "document.location='main/?pg=data_teknis_pengalaman'";
			echo '</script>';
			exit;
		}

		$nextId = 0;
		if($reqSubmit == 'Submit'){
			$cek = formatTextToDb($file->getFileName('reqLinkFile'));
			$cek2 = formatTextToDb($file_ba->getFileName('reqLinkFileBA'));
			if($cek != "")
			{
				$allowed = array(".pdf");	$status_allowed='';
				foreach ($allowed as $file_cek)
				{
					if(!preg_match("/$file_cek\$/i", $_FILES['reqLinkFile']['name']))
					{
						$status_allowed = 'tidak_boleh';
					}
				}
			}
			if($reqStatus == 2)
				$cek2 = "1";
			else
			{
				if($cek2 != "")
				{
					$allowed = array(".pdf");	$status_allowed='';
					foreach ($allowed as $file_cek)
					{
						if(!preg_match("/$file_cek\$/i", $_FILES['reqLinkFileBA']['name']))
						{
							$status_allowed = 'tidak_boleh';
						}
					}
				}
			}

			if($hasil_cek_file == '' && $cek == ''){
				echo '<script language="javascript">';
				echo "$.jGrowl('Lengkapi file terlebih dahulu, Data gagal disimpan');";
				echo '</script>';
			}elseif($cek2 == ''){
				echo '<script language="javascript">';
				echo "$.jGrowl('Lengkapi file terlebih dahulu, Data gagal disimpan');";
				echo '</script>';
			}elseif($status_allowed == 'tidak_boleh'){
				echo '<script language="javascript">';
				echo "$.jGrowl('File upload harus pdf');";
				echo '</script>';
			}else{
				$rekanan_pengalaman->setField("REKANAN_ID",$userLogin->userRekanan);
				$rekanan_pengalaman->setField("NAMA",$reqNama);
				$rekanan_pengalaman->setField("LOKASI",$reqLokasi);
				$rekanan_pengalaman->setField("PEMBERI_TUGAS",$reqTgsNama);
				$rekanan_pengalaman->setField("PEMBERI_TUGAS_ALAMAT",$reqTgsAlamat);
				$rekanan_pengalaman->setField("KONTRAK_NOMOR",$reqKontrakNo);
				$rekanan_pengalaman->setField("KONTRAK_NILAI",dotToNo($reqKontrakNilai));
				$rekanan_pengalaman->setField("KONTRAK_KETERANGAN",$reqJOket);
				$rekanan_pengalaman->setField("KONTRAK_STATUS",$reqStatus);
				$rekanan_pengalaman->setField("KONTRAK_JO",$reqJOpersen);
				$rekanan_pengalaman->setField("PROGRESS",$reqProgress);

				$rekanan_pengalaman->setField("KONTRAK_TANGGAL",dateToDBCheck($reqTanggal));
				$rekanan_pengalaman->setField("PROGRESS_TANGGAL",dateToDBCheck($reqProgressTanggal));
				$rekanan_pengalaman->setField("BA_TANGGAL",dateToDBCheck($reqSelesaiBA));

				$reqPengalamanId = $rekanan_pengalaman->getNextId("REKANAN_PENGALAMAN_ID","REKANAN_PENGALAMAN");

				$cek = formatTextToDb($file->getFileName('reqLinkFile'));
				if($cek != "")
				{
					$renameFile = $reqPengalamanId.formatTextToDb($file->getFileName('reqLinkFile'));
					$varSource=$FILE_DIR.$reqLinkFileTemp;

					if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
					{
						if($reqLinkFileTemp != ''){
							if($file->delete($varSource)){}
						}
						$insertLinkFile = $file->uploadedFileName;
						$insertLinkFilesSize = $file->uploadedSize;
						$insertLinkFilesExe = $file->uploadedExtension;
					}
				}else{
					$insertLinkFile = $reqLinkFileTemp;
					$insertLinkFilesSize = 'NULL';
					$insertLinkFilesExe = $reqLinkFileTempTipe;
				}

				$cek_ba = formatTextToDb($file_ba->getFileName('reqLinkFileBA'));
				if($cek_ba != "")
				{
					$renameFile = $reqPengalamanId."BA".formatTextToDb($file_ba->getFileName('reqLinkFileBA'));
					$varSource=$FILE_DIR.$reqLinkFileBATemp;
					if($file_ba->uploadToDir('reqLinkFileBA', $FILE_DIR, $renameFile))
					{
						if($reqLinkFileBATemp != ''){
							if($file_ba->delete($varSource)){}
						}
						$insertLinkFileBA = $file_ba->uploadedFileName;
						$insertLinkFilesSizeBA = $file_ba->uploadedSize;
						$insertLinkFilesExeBA = $file_ba->uploadedExtension;
					}
				}else{
					$insertLinkFileBA = $reqLinkFileBATemp;
					if($reqLinkFileBATempUkuran)
						$insertLinkFilesSizeBA = $reqLinkFileBATempUkuran;
					else
						$insertLinkFilesSizeBA = 'NULL';
					$insertLinkFilesExeBA = $reqLinkFileBATempTipe;
				}

				$rekanan_pengalaman->setField("UKURAN_BA", $insertLinkFilesSizeBA);
				$rekanan_pengalaman->setField("TIPE_BA", $insertLinkFilesExeBA);
				$rekanan_pengalaman->setField("PATH_FILE_BA", $insertLinkFileBA);

				$rekanan_pengalaman->setField("UKURAN", $insertLinkFilesSize);
				$rekanan_pengalaman->setField("TIPE", $insertLinkFilesExe);
				$rekanan_pengalaman->setField("PATH_FILE", $insertLinkFile);
				$rekanan_pengalaman->setField('CREATED_BY', $this->USER_LOGIN_ID);

				//echo 'asd';print_r($reqBidangUsahaId);
				if($rekanan_pengalaman->insert())
				{
					$idPengalaman = $rekanan_pengalaman->id;

					for($i=0;$i<count($reqBidangUsahaId);$i++)
					{
						$rekanan_bidang_usaha_insert = new RekananPengalamanBidang();
						$rekanan_bidang_usaha_insert->setField("REKANAN_PENGALAMAN_ID", $idPengalaman);
						$rekanan_bidang_usaha_insert->setField("BIDANG_USAHA_ID", $reqBidangUsahaId[$i]);
						$rekanan_bidang_usaha_insert->insert();
						unset($rekanan_bidang_usaha_insert);
					}

					echo '<script language="javascript">';
					echo "document.location='main/?pg=data_teknis_pengalaman'";
					echo '</script>';

					echo '<script language="javascript">';
					echo "$.jGrowl('Data berhasil disimpan');";
					//echo 'window.close();';
					echo '</script>';

					/*echo '<script language="javascript">';
					echo "alert('Data berhasil disimpan')";
					echo '</script>';*/
					$alertMsg .= "Data berhasil diupdate";
				}
				else
				{
					$alertMsg .= "Update failed : ".$rekanan_pengalaman->query;
				}
				//echo $rekanan_pengalaman->query;
			}
		}
   }

   function data_teknis_pengalaman_selesai_ubah()
   {
	   /* INCLUDE FILE */
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Rekanan");
		$this->load->model("RekananPengalaman");
		$this->load->model("RekananPengalamanBidang");
		$this->load->library("FileHandler");
		$file = new FileHandler();

		/* create objects */
		$rekanan = new Rekanan();
		$rekanan_pengalaman				= new RekananPengalaman(); // tipe 0

		$reqPengalamanId= $this->input->post('reqPengalamanId');
		$reqId= $this->input->post('reqId');
		//$reqBidangUsahaId = $_POST["reqBidangUsahaId"];
		$reqBidangUsahaId = $this->input->post("reqBidangUsahaId");
		$reqNama= $this->input->post('reqNama');
		$reqLokasi= $this->input->post('reqLokasi');
		$reqTgsNama= $this->input->post('reqTgsNama');
		$reqTgsAlamat= $this->input->post('reqTgsAlamat');
		$reqKontrakNo= $this->input->post('reqKontrakNo');
		$reqTanggal= $this->input->post('reqTanggal');
		$reqKontrakNilai= $this->input->post('reqKontrakNilai');
		$reqJOpersen= $this->input->post('reqJOpersen') ? $this->input->post('reqJOpersen') : '0';
		$reqJOket= $this->input->post('reqJOket');
		$reqStatus= $this->input->post('reqStatus');
		$reqSelesaiBA= $this->input->post('reqSelesaiBA');
		$reqProgress= $this->input->post('reqProgress');
		$reqProgressTanggal= $this->input->post('reqProgressTanggal');

		$reqLinkFileBA= $_FILES['reqLinkFileBA'];
		$reqLinkFileBATemp = $this->input->post("reqLinkFileBATemp");
		$reqLinkFileBATempTipe = $this->input->post("reqLinkFileBATempTipe");
		$reqLinkFileBATempUkuran = $this->input->post("reqLinkFileBATempUkuran") ?: 0;
		$reqLinkFileTempBANama = $this->input->post("reqLinkFileTempBANama");

		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqLinkFileTemp = $this->input->post("reqLinkFileTemp");
		$reqLinkFileTempTipe = $this->input->post("reqLinkFileTempTipe");
		$reqLinkFileTempUkuran = $this->input->post("reqLinkFileTempUkuran");
		$reqLinkFileTempNama = $this->input->post("reqLinkFileTempNama");

		$reqSubmit= $this->input->post('reqSubmit');
		$reqMode= $this->input->post('reqMode');
		
		$file_list = $_POST["file_list"];

		$FILE_DIR = "uploads/pengalaman/";

		if($reqMode == 'insert')
		{
			$rekanan_pengalaman->setField("REKANAN_ID",$this->ID);
			$rekanan_pengalaman->setField("NAMA",$reqNama);
			$rekanan_pengalaman->setField("LOKASI",$reqLokasi);
			$rekanan_pengalaman->setField("PEMBERI_TUGAS",$reqTgsNama);
			$rekanan_pengalaman->setField("PEMBERI_TUGAS_ALAMAT",$reqTgsAlamat);
			$rekanan_pengalaman->setField("KONTRAK_NOMOR",$reqKontrakNo);
			$rekanan_pengalaman->setField("KONTRAK_NILAI",dotToNo($reqKontrakNilai));
			$rekanan_pengalaman->setField("KONTRAK_KETERANGAN",$reqJOket);
			$rekanan_pengalaman->setField("KONTRAK_STATUS",$reqStatus);
			$rekanan_pengalaman->setField("KONTRAK_JO",$reqJOpersen);
			$rekanan_pengalaman->setField("PROGRESS",valToNull($reqProgress));

			$rekanan_pengalaman->setField("KONTRAK_TANGGAL",dateToDBCheck($reqTanggal));
			$rekanan_pengalaman->setField("PROGRESS_TANGGAL",dateToDBCheck($reqProgressTanggal));
			$rekanan_pengalaman->setField("BA_TANGGAL",dateToDBCheck($reqSelesaiBA));

			$reqPengalamanId = $rekanan_pengalaman->getNextId("REKANAN_PENGALAMAN_ID","REKANAN_PENGALAMAN");
			/* UPLOAD FILE */
			$renameFile = md5(date("dmYHis").$reqLinkFileBA['name'].$this->ID).".".getExtension($reqLinkFileBA['name']);
			if($file->uploadToDir('reqLinkFileBA', $FILE_DIR, $renameFile))
			{
				$insertLinkFilesSizeBA = $file->uploadedSize;
				$insertLinkFilesExeBA =  $file->uploadedExtension;
				$insertLinkFileBA =  $renameFile;
				$insertLinkFileBANama =  $reqLinkFileBA['name'];
			}
			else
			{
				$insertLinkFilesSizeBA = $reqLinkFileBATempUkuran;
				$insertLinkFilesExeBA =  $reqLinkFileBATempTipe;
				$insertLinkFileBA =  $reqLinkFileBATemp;
				$insertLinkFileBANama =  $reqLinkFileTempBANama;
			}
			/* END UPLOAD FILE */
			$rekanan_pengalaman->setField("UKURAN_BA", $insertLinkFilesSizeBA);
			$rekanan_pengalaman->setField("TIPE_BA", $insertLinkFilesExeBA);
			$rekanan_pengalaman->setField("PATH_FILE_BA", $insertLinkFileBA);
			$rekanan_pengalaman->setField("NAMA_FILE_BA", $insertLinkFileBANama);

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
				$insertLinkFileNama =  $reqLinkFileTempNama;
			}
			/* END UPLOAD FILE */
			$rekanan_pengalaman->setField("UKURAN", $insertLinkFilesSize);
			$rekanan_pengalaman->setField("TIPE", $insertLinkFilesExe);
			$rekanan_pengalaman->setField("PATH_FILE", $insertLinkFile);
			$rekanan_pengalaman->setField("NAMA_FILE", $insertLinkFileNama);
			$rekanan_pengalaman->setField('CREATED_BY', $this->USER_LOGIN_ID);

			if($rekanan_pengalaman->insert())
			{
				$idPengalaman = $rekanan_pengalaman->id;

				// if (count($reqBidangUsahaId) > 0) {
					for($i=0;$i<count($reqBidangUsahaId);$i++)
					{
						$rekanan_bidang_usaha_insert = new RekananPengalamanBidang();
						$rekanan_bidang_usaha_insert->setField("REKANAN_PENGALAMAN_ID", $idPengalaman);
						$rekanan_bidang_usaha_insert->setField("BIDANG_USAHA_ID", $reqBidangUsahaId[$i]);
						$rekanan_bidang_usaha_insert->setField('CREATED_BY', $this->USER_LOGIN_ID);
						$rekanan_bidang_usaha_insert->insert();
						unset($rekanan_bidang_usaha_insert);
					}
				// }

				echo "Data Berhasil disimpan";
			}
			else
			{
				echo "Data Gagal Tersimpan";
			}
				//echo $rekanan_pengalaman->query;
		}
		else
		{
				$rekanan_pengalaman->setField("REKANAN_PENGALAMAN_ID",$reqPengalamanId);
				$rekanan_pengalaman->setField("NAMA",$reqNama);
				$rekanan_pengalaman->setField("LOKASI",$reqLokasi);
				$rekanan_pengalaman->setField("PEMBERI_TUGAS",$reqTgsNama);
				$rekanan_pengalaman->setField("PEMBERI_TUGAS_ALAMAT",$reqTgsAlamat);
				$rekanan_pengalaman->setField("KONTRAK_NOMOR",$reqKontrakNo);
				$rekanan_pengalaman->setField("KONTRAK_NILAI",dotToNo($reqKontrakNilai));
				$rekanan_pengalaman->setField("KONTRAK_KETERANGAN",$reqJOket);
				$rekanan_pengalaman->setField("KONTRAK_STATUS",$reqStatus);
				$rekanan_pengalaman->setField("KONTRAK_JO",valToNull($reqJOpersen));
				$rekanan_pengalaman->setField("PROGRESS",valToNull($reqProgress));
				$rekanan_pengalaman->setField("KONTRAK_TANGGAL",dateToDBCheck($reqTanggal));
				$rekanan_pengalaman->setField("PROGRESS_TANGGAL",dateToDBCheck($reqProgressTanggal));
				$rekanan_pengalaman->setField("BA_TANGGAL",dateToDBCheck($reqSelesaiBA));

				$rekanan_pengalaman->setField("REKANAN_ID",$this->ID);
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
					$insertLinkFileNama =  $reqLinkFileTempNama;
				}
				/* END UPLOAD FILE */
				$rekanan_pengalaman->setField("UKURAN", $insertLinkFilesSize);
				$rekanan_pengalaman->setField("TIPE", $insertLinkFilesExe);
				$rekanan_pengalaman->setField("PATH_FILE", $insertLinkFile);
				$rekanan_pengalaman->setField("NAMA_FILE", $insertLinkFileNama);

				/* UPLOAD FILE */
				$renameFile = md5(date("dmYHis").$reqLinkFileBA['name'].$this->ID).".".getExtension($reqLinkFileBA['name']);
				if($file->uploadToDir('reqLinkFileBA', $FILE_DIR, $renameFile))
				{
					$insertLinkFilesSizeBA = $file->uploadedSize;
					$insertLinkFilesExeBA =  $file->uploadedExtension;
					$insertLinkFileBA =  $renameFile;
					$insertLinkFileBANama =  $reqLinkFileBA['name'];
				}
				else
				{
					$insertLinkFilesSizeBA = $reqLinkFileBATempUkuran ?: 0;
					$insertLinkFilesExeBA =  $reqLinkFileBATempTipe ?: '';
					$insertLinkFileBA =  $reqLinkFileBATemp ?: '';
					$insertLinkFileBANama =  $reqLinkFileTempBANama ?: '';
				}
				/* END UPLOAD FILE */
				$rekanan_pengalaman->setField("UKURAN_BA", $insertLinkFilesSizeBA);
				$rekanan_pengalaman->setField("TIPE_BA", $insertLinkFilesExeBA);
				$rekanan_pengalaman->setField("PATH_FILE_BA", $insertLinkFileBA);
				$rekanan_pengalaman->setField("NAMA_FILE_BA", $insertLinkFileBANama);
				$rekanan_pengalaman->setField('CREATED_BY', $this->USER_LOGIN_ID);

				if($rekanan_pengalaman->update())
				{
					$paketBidangUsaha = new RekananPengalamanBidang();
					$paketBidangUsaha->setField('REKANAN_PENGALAMAN_ID', $reqPengalamanId);
					$paketBidangUsaha->delete_parent();
					unset($paketBidangUsaha);
					//$idPengalaman = $rekanan_pengalaman->id;

					// if (count($reqBidangUsahaId) > 0) {
						for($i=0;$i<count($reqBidangUsahaId);$i++)
						{
							$rekanan_bidang_usaha_insert = new RekananPengalamanBidang();
							$rekanan_bidang_usaha_insert->setField("REKANAN_PENGALAMAN_ID", $reqPengalamanId);
							$rekanan_bidang_usaha_insert->setField("BIDANG_USAHA_ID", $reqBidangUsahaId[$i]);
							$rekanan_bidang_usaha_insert->setField('CREATED_BY', $this->USER_LOGIN_ID);
							$rekanan_bidang_usaha_insert->insert();
							unset($rekanan_bidang_usaha_insert);
						}
					// }

					echo "Data berhasil di Update..";
				}
				else
				{
					echo "Data Gagal Tersimpan";
				}
			}
	   }

	   function delete()
   	   {
			$this->load->model("Rekanan");
			$this->load->model("RekananPengalaman");
			$this->load->library("FileHandler");


			 $reqId= $this->input->get("reqId");
			/* create objects */
			$rekanan = new Rekanan();
			$rekanan_pengalaman				= new RekananPengalaman(); // tipe 0
			$rekanan_pengalaman_progress	= new RekananPengalaman(); // tipe 0
			$file = new FileHandler();

			$FILE_DIR = "uploads/pengalaman/";

			$rekanan_pengalaman->setField("REKANAN_PENGALAMAN_ID", $reqId);
			$rekanan_pengalaman->setField('REKANAN_ID', $this->ID);

			$rekanan_pengalaman_file = new RekananPengalaman();
			$rekanan_pengalaman_file->selectByParams(array("REKANAN_PENGALAMAN_ID"=>$reqId), -1, -1," ");
			$rekanan_pengalaman_file->firstRow();
			$varSource = $FILE_DIR.$rekanan_pengalaman_file->getField('PATH_FILE');
			$fileSource = $rekanan_pengalaman_file->getField('PATH_FILE');
			if($rekanan_pengalaman->delete())
			{
				$rekanan_pengalaman_bidang = new RekananPengalaman();
				$rekanan_pengalaman_bidang->setField("REKANAN_PENGALAMAN_ID", $reqId);
				$rekanan_pengalaman_bidang->delete_bidang();

				if($fileSource != ''){
					if($file->delete($varSource)){}
				}
					echo "Data telah dihapus";
			}
			else
			{
				echo "Data gagal dihapus";
			}
	   }

	   function paket_evaluasi_lihat_pengalaman()
	   {
			/* INCLUDE FILE */
			$this->load->model("RekananPengalaman");
			$this->load->model("Rekanan");

			/* create objects */
			$rekanan_pengalaman					= new RekananPengalaman(); // tipe 0
			$rekanan_pengalaman_progress		= new RekananPengalaman(); // tipe 0
			$rekanan_get_nama = new Rekanan();

			/* VARIABLE */
			$reqId = $this->input->post("reqId");
			$reqPengalamanId  = $_POST["reqPengalamanId"];
			$reqKontrakNilai = $_POST["reqKontrakNilai"];
			$reqKontrakNilaiTemp = $_POST["reqKontrakNilaiTemp"];
			$submitSimpan = $this->input->post("submitSimpan");

			if($submitSimpan == "Simpan")
			{
				$total_nilai = 0;
				for($i=0;$i<count($reqPengalamanId);$i++)
				{
					/* UPDATE REKANAN PENGALAMAN */
					if($reqKontrakNilai[$i] == $reqKontrakNilaiTemp[$i])
					{}
					else
					{
						$rekanan_pengalaman_ubah = new RekananPengalaman();
						$rekanan_pengalaman_ubah->setField("KONTRAK_NILAI", dotToNo($reqKontrakNilai[$i]));
						$rekanan_pengalaman_ubah->setField("KONTRAK_NILAI_SEBELUMNYA", dotToNo($reqKontrakNilaiTemp[$i]));
						//$rekanan_pengalaman_ubah->setField("UPDATED_BY", $userLogin->nama);
						$rekanan_pengalaman_ubah->setField("REKANAN_PENGALAMAN_ID", $reqPengalamanId[$i]);
						$rekanan_pengalaman_ubah->updateNilai();
					}
					unset($rekanan_pengalaman_ubah);
				}
				echo "Data berhasil disimpan";
			}
	  }

}
?>
