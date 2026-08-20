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

class rekanan_pengurus_json extends CI_Controller {

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

   function data_administrasi_pengurus_perusahaan_ubah()
   {
		/* INCLUDE FILE */
		$this->load->model("Rekanan");
		$this->load->model("RekananPengurus");
		$this->load->library("FileHandler");
		$file = new FileHandler();

		/* create objects */
		$rekanan_pengurus = new RekananPengurus();
		$rekanan = new Rekanan();
		//$reqId = $userLogin->userRekanan;

		/* VARIABLE */
		$reqId = $this->ID;

		$reqPengurusID = $this->input->post("reqPengurusID");
		$reqSubmit	 = $this->input->post("reqSubmit");

		$reqNama	 = $this->input->post("reqNama");
		$reqNomorKTP = $this->input->post("reqNomorKTP");
		$reqJabatan	 = $this->input->post("reqJabatan");
		$reqTipe 	 = $this->input->post("reqTipe");
		$reqMode 	 = $this->input->post("reqMode");
		$reqLinkFileTempNama 	= $_POST["reqLinkFileTempNama"];
		$reqLinkFileTemp 		= $_POST["reqLinkFileTemp"];
		$reqLinkFileTempTipe 	= $_POST["reqLinkFileTempTipe"];
		$reqLinkFileTempUkuran 	= $_POST["reqLinkFileTempUkuran"];
		$reqLinkFile = $_FILES['reqLinkFile'];
		$reqLinkFile2 = $_FILES['reqLinkFile2'];
		$reqLinkFile2TempNama 	= $_POST["reqLinkFile2TempNama"];
		$FILE_DIR = "uploads/pengurus/";

		$reqKewarganegaraan 	 = $this->input->post("reqKewarganegaraan");
		$reqJenisKelamin 	 = $this->input->post("reqJenisKelamin");
		$reqAlamatKTP 	 = $this->input->post("reqAlamatKTP");
		$reqDomisili 	 = $this->input->post("reqDomisili");
		$reqNPWP 	 = $this->input->post("reqNPWP");
		$reqNegara 	 = $this->input->post("reqNegara");
		$reqNoHPDirektur 	 = $this->input->post("reqNoHPDirektur");

		$reqStatus = 1;

		//echo "dssdasfa";exit;
		if($reqMode=='insert')
		{
			$rekanan_pengurus->setField("REKANAN_ID",$reqId);
			$rekanan_pengurus->setField("NAMA",$reqNama);
			$rekanan_pengurus->setField("KTP",$reqNomorKTP);
			$rekanan_pengurus->setField("JABATAN",$reqJabatan);
			$rekanan_pengurus->setField("TIPE",$reqTipe);
			$rekanan_pengurus->setField("STATUS",$reqStatus);
			$rekanan_pengurus->setField('CREATED_BY', $this->USER_LOGIN_ID);

			$rekanan_pengurus->setField("KEWARGANEGARAAN",$reqKewarganegaraan);
			$rekanan_pengurus->setField("JENIS_KELAMIN",$reqJenisKelamin);
			$rekanan_pengurus->setField("ALAMAT_KTP",$reqAlamatKTP);
			$rekanan_pengurus->setField("DOMISILI",$reqDomisili);
			$rekanan_pengurus->setField("NPWP",$reqNPWP);
			$rekanan_pengurus->setField("NEGARA",$reqNegara);
			$rekanan_pengurus->setField("NOMOR_HP_DIREKTUR",$reqNoHPDirektur);

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
				$insertLinkFile =  $reqLinkFileTemp;
				$insertLinkFilesExe =  $reqLinkFileTempTipe;
				$insertLinkFilesSize = $reqLinkFileTempUkuran;
				$insertLinkFileNama = $reqLinkFileTempNama;
			}
			$rekanan_pengurus->setField("PATH_FILE", $insertLinkFile);
			$rekanan_pengurus->setField("TIPE_FILE", $insertLinkFilesExe);
			$rekanan_pengurus->setField("UKURAN", ValToNullDB($insertLinkFilesSize));
			$rekanan_pengurus->setField("NAMA_FILE", $insertLinkFileNama);

			$renameFile2 = "NPWP_".md5(date("dmYHis").$reqLinkFile2['name'].$this->ID).".".getExtension($reqLinkFile2['name']);
			if($file->uploadToDir('reqLinkFile2', $FILE_DIR, $renameFile2))
			{
				$insertLinkFile2 =  $renameFile2;
			}
			else
			{
				$insertLinkFile2 =  $reqLinkFile2TempNama;
			}
			$rekanan_pengurus->setField("PATH_FILE2", $insertLinkFile2);

			//if($rekanan->update()){}
			// if($rekanan_pengurus->insertnofile())
			if($rekanan_pengurus->insert())
			{
				echo "Data Berhasil Disimpan";
			}
			else
			{
				echo "Data Gagal Tersimpan";
			}
		}
		else
		{
			$rekanan_pengurus->setField("REKANAN_PENGURUS_ID",$reqPengurusID);
			$rekanan_pengurus->setField("REKANAN_ID",$this->ID);
			$rekanan_pengurus->setField("NAMA",$reqNama);
			$rekanan_pengurus->setField("KTP",$reqNomorKTP);
			$rekanan_pengurus->setField("JABATAN",$reqJabatan);
			$rekanan_pengurus->setField("TIPE",$reqTipe);
			$rekanan_pengurus->setField("STATUS",$reqStatus);
			$rekanan_pengurus->setField('CREATED_BY', $this->USER_LOGIN_ID);

			$rekanan_pengurus->setField("KEWARGANEGARAAN",$reqKewarganegaraan);
			$rekanan_pengurus->setField("JENIS_KELAMIN",$reqJenisKelamin);
			$rekanan_pengurus->setField("ALAMAT_KTP",$reqAlamatKTP);
			$rekanan_pengurus->setField("DOMISILI",$reqDomisili);
			$rekanan_pengurus->setField("NPWP",$reqNPWP);
			$rekanan_pengurus->setField("NEGARA",$reqNegara);
			$rekanan_pengurus->setField("NOMOR_HP_DIREKTUR",$reqNoHPDirektur);

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
				$insertLinkFile =  $reqLinkFileTemp;
				$insertLinkFilesExe =  $reqLinkFileTempTipe;
				$insertLinkFilesSize = $reqLinkFileTempUkuran;
				$insertLinkFileNama = $reqLinkFileTempNama;
			}
			$rekanan_pengurus->setField("PATH_FILE", $insertLinkFile);
			$rekanan_pengurus->setField("TIPE_FILE", $insertLinkFilesExe);
			$rekanan_pengurus->setField("UKURAN", ValToNullDB($insertLinkFilesSize));
			$rekanan_pengurus->setField("NAMA_FILE", $insertLinkFileNama);

			$renameFile2 = "NPWP_".md5(date("dmYHis").$reqLinkFile2['name'].$this->ID).".".getExtension($reqLinkFile2['name']);
			if($file->uploadToDir('reqLinkFile2', $FILE_DIR, $renameFile2))
			{
				$insertLinkFile2 =  $renameFile2;
			}
			else
			{
				$insertLinkFile2 =  $reqLinkFile2TempNama;
			}
			$rekanan_pengurus->setField("PATH_FILE2", $insertLinkFile2);

			// if($rekanan_pengurus->updatenofile())
			if($rekanan_pengurus->update())
			{
				echo "Data Berhasil Diupdate";
			}
			else
			{
				echo "Data Gagal Tersimpan";
			}

		}

   }

   function registrasi()
   {
		/* INCLUDE FILE */
		$this->load->model("Rekanan");
		$this->load->model("RekananPengurus");
		$this->load->library("FileHandler");
		$file = new FileHandler();

		/* create objects */
		$rekanan_pengurus = new RekananPengurus();
		$rekanan_pengurus_direksi = new RekananPengurus();
		$rekanan = new Rekanan();

		/* VARIABLE */
		$reqId = $this->input->post("reqId");
		$reqRekananId = $this->input->post("reqRekananId");
		$reqPengurusID = $this->input->post("reqPengurusID");
		$reqSubmit	 = $this->input->post("reqSubmit");

		$reqKomisarisNama	 = $this->input->post("reqKomisarisNama");
		$reqDireksiNama	 = $this->input->post("reqDireksiNama");
		$reqKomisarisKTP = $this->input->post("reqKomisarisKTP");
		$reqDireksiKTP = $this->input->post("reqDireksiKTP");
		$reqKomisarisJabatan	 = $this->input->post("reqKomisarisJabatan");
		$reqDireksiJabatan	 = $this->input->post("reqDireksiJabatan");
		$reqTipe 	 = $this->input->post("reqTipe");
		$reqMode 	 = $this->input->post("reqMode");
		$reqRekananPengurusId 	 = $this->input->post("reqRekananPengurusId");
		$reqLinkFile			= $_FILES['reqLinkFile'];
		$reqLinkFileDireksi			= $_FILES['reqLinkFileDireksi'];

		$reqLinkFileTemp 		= $_POST["reqLinkFileTemp"];
		$reqLinkFileTempTipe 	= $_POST["reqLinkFileTempTipe"];
		$reqLinkFileTempUkuran 	= $_POST["reqLinkFileTempUkuran"];
		$reqLinkFileTempNama 	= $_POST["reqLinkFileTempNama"];

		$reqLinkFileDireksiTemp 		= $_POST["reqLinkFileDireksiTemp"];
		$reqLinkFileDireksiTempTipe 	= $_POST["reqLinkFileDireksiTempTipe"];
		$reqLinkFileDireksiTempUkuran 	= $_POST["reqLinkFileDireksiTempUkuran"];
		$reqLinkFileDireksiTempNama 	= $_POST["reqLinkFileDireksiTempNama"];
		$reqStatus = 1;
		$FILE_DIR = "uploads/pemimpin_perusahaan/";

		//print_r ($reqKomisarisNama);
		//print_r ($reqDireksiNama);
		//exit;
		$rekanan_pengurus->setField("REKANAN_ID", $this->ID);
		$rekanan_pengurus->delete_komisaris();
		for($i=0; $i<count($reqKomisarisNama);$i++)
		{
			if($reqKomisarisNama[$i] == "")
			{}
			else
			{
				$rekanan_pengurus = new RekananPengurus();
				$rekanan_pengurus->setField("REKANAN_ID", $this->ID);
				$rekanan_pengurus->setField("NAMA",$reqKomisarisNama[$i]);
				$rekanan_pengurus->setField("KTP",$reqKomisarisKTP[$i]);
				$rekanan_pengurus->setField("JABATAN",$reqKomisarisJabatan[$i]);
				$rekanan_pengurus->setField("TIPE",1);
				$rekanan_pengurus->setField("STATUS",1);
				$renameFile = md5(date("dmYHis").$reqLinkFile['name'][$i].$this->ID).".".getExtension($reqLinkFile['name'][$i]);
				if($file->uploadToDirArray('reqLinkFile', $FILE_DIR, $renameFile, $i))
				{
					$insertLinkFilesSize = $file->uploadedSize;
					$insertLinkFilesExe =  $file->uploadedExtension;
					$insertLinkFile =  $renameFile;
					$insertLinkFileNama = $reqLinkFile['name'][$i];
				}
				else
				{
					$insertLinkFile =  $reqLinkFileTemp[$i];
					$insertLinkFilesExe =  $reqLinkFileTempTipe[$i];
					$insertLinkFilesSize = $reqLinkFileTempUkuran[$i];
					$insertLinkFileNama = $reqLinkFileTempNama[$i];
				}
				$rekanan_pengurus->setField("PATH_FILE", $insertLinkFile);
				$rekanan_pengurus->setField("TIPE_FILE", $insertLinkFilesExe);
				$rekanan_pengurus->setField("UKURAN", ValToNullDB($insertLinkFilesSize));
				$rekanan_pengurus->setField("NAMA_FILE", $insertLinkFileNama);
				$rekanan_pengurus->insert();
			}
			unset($rekanan_pengurus);
		}

		for($i=0; $i<count($reqDireksiNama);$i++)
		{
			if($reqDireksiNama[$i] == "")
			{}
			else
			{
				$rekanan_pengurus_direksi = new RekananPengurus();
				$rekanan_pengurus_direksi->setField("REKANAN_ID", $this->ID);
				$rekanan_pengurus_direksi->setField("NAMA",$reqDireksiNama[$i]);
				$rekanan_pengurus_direksi->setField("KTP",$reqDireksiKTP[$i]);
				$rekanan_pengurus_direksi->setField("JABATAN",$reqDireksiJabatan[$i]);
				$rekanan_pengurus_direksi->setField("TIPE",2);
				$rekanan_pengurus_direksi->setField("STATUS",1);
				$renameFile = md5(date("dmYHis").$reqLinkFileDireksi['name'][$i].$this->ID).".".getExtension($reqLinkFileDireksi['name'][$i]);
				if($file->uploadToDirArray('reqLinkFileDireksi', $FILE_DIR, $renameFile, $i))
				{
					$insertLinkFilesDireksiSize = $file->uploadedSize;
					$insertLinkFilesDireksiExe =  $file->uploadedExtension;
					$insertLinkFileDireksi =  $renameFile;
					$insertLinkFileDireksiNama = $reqLinkFileDireksi['name'][$i];
				}
				else
				{
					$insertLinkFileDireksi =  $reqLinkFileDireksiTemp[$i];
					$insertLinkFilesDireksiExe =  $reqLinkFileDireksiTempTipe[$i];
					$insertLinkFilesDireksiSize = $reqLinkFileDireksiTempUkuran[$i];
					$insertLinkFileDireksiNama = $reqLinkFileDireksiTempNama[$i];
				}
				$rekanan_pengurus_direksi->setField("PATH_FILE", $insertLinkFileDireksi);
				$rekanan_pengurus_direksi->setField("TIPE_FILE", $insertLinkFilesDireksiExe);
				$rekanan_pengurus_direksi->setField("UKURAN", ValToNullDB($insertLinkFilesDireksiSize));
				$rekanan_pengurus_direksi->setField("NAMA_FILE", $insertLinkFileDireksiNama);
				$rekanan_pengurus_direksi->insert();
				//echo $rekanan_pengurus_direksi->query;exit;
			}
			unset($rekanan_pengurus_direksi);
		}
		echo "Data berhasil disimpan";
			/*if($rekanan_pengurus->insert())
			{
				echo "Data Berhasil Diupdate";
			}
			else
			{
				echo "Data Berhasil Diupdate";
			}*/
   }

   function delete()
   {
		$this->load->model("RekananPengurus");

		$reqId = $this->input->get("reqId");

		$rekanan_pengurus = new RekananPengurus();
		$rekanan_pengurus->setField("REKANAN_ID",  $this->ID);
		$rekanan_pengurus->setField("REKANAN_PENGURUS_ID",  $reqId);
		$rekanan_pengurus->delete();

   }

}
?>
