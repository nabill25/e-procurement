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

class rekanan_saham_json extends CI_Controller {

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

	function data_administrasi_keuangan_saham_ubah()
	{
		$this->load->library("kauth");
		$userLogin = new kauth();

		$this->load->model("Rekanan");
		$this->load->model("RekananSaham");
		$this->load->library("FileHandler");
		$file = new FileHandler();

		/* create objects */
		$rekanan = new Rekanan();
		$rekanan_saham 	= new RekananSaham();

		$reqSahamId= $this->input->post('reqSahamId');
		$reqKepemilikan= $this->input->post('reqKepemilikan');
		$reqPemegangSaham= $this->input->post('reqPemegangSaham');
		$reqNomorKTP= $this->input->post('reqNomorKTP');
		$reqNomorNPWP= $this->input->post('reqNomorNPWP');
		$reqAlamat= $this->input->post('reqAlamat');
		$reqPersentase= $this->input->post('reqPersentase');
		$reqId= $this->input->post('reqId');
		$reqSubmit= $this->input->post('reqSubmit');
		$reqMode= $this->input->post('reqMode');
		$reqLinkFileTempNama 	= $_POST["reqLinkFileTempNama"];
		$reqLinkFileTemp 		= $_POST["reqLinkFileTemp"];
		$reqLinkFileTempTipe 	= $_POST["reqLinkFileTempTipe"];
		$reqLinkFileTempUkuran 	= $_POST["reqLinkFileTempUkuran"];
		$reqLinkFileTempNama 	= $_POST["reqLinkFileTempNama"];
		$reqLinkFile = $_FILES['reqLinkFile'];
		$FILE_DIR = "uploads/kepemilikan_saham/";

		$reqJenisKelamin= $this->input->post('reqJenisKelamin');
		$reqKewarganegaraan= $this->input->post('reqKewarganegaraan');
		$reqNegara= $this->input->post('reqNegara');
		$reqNominalSaham= $this->input->post('reqNominalSaham');

		$reqId = $this->ID;

		$rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
		$rekanan->firstRow();

		if($reqMode=='insert')
		{
			$rekanan_saham->setField("REKANAN_ID",$this->ID);
			$rekanan_saham->setField("KEPEMILIKAN",$reqKepemilikan);
			$rekanan_saham->setField("NAMA",$reqPemegangSaham);
			$rekanan_saham->setField("KTP",$reqNomorKTP);
			$rekanan_saham->setField("NPWP",$reqNomorNPWP);
			$rekanan_saham->setField("ALAMAT",$reqAlamat);
			$rekanan_saham->setField("JUMLAH_SAHAM",$reqPersentase);
			$rekanan_saham->setField("STATUS",'1');
			$rekanan_saham->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$rekanan_saham->setField("KEWARGANEGARAAN",$reqKewarganegaraan);
			$rekanan_saham->setField("JENIS_KELAMIN",$reqJenisKelamin);
			$rekanan_saham->setField("NEGARA",$reqNegara);
			$rekanan_saham->setField("NOMINAL_SAHAM",dotToNo($reqNominalSaham));

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
			$rekanan_saham->setField("PATH_FILE", $insertLinkFile);
			$rekanan_saham->setField("TIPE_FILE", $insertLinkFilesExe);
			$rekanan_saham->setField("UKURAN", ValToNullDB($insertLinkFilesSize));
			$rekanan_saham->setField("NAMA_FILE", $insertLinkFileNama);

			if($rekanan_saham->insert2())
			{
				echo "Data berhasil disimpan";
			}
			else
			{
				echo "Data Gagal Tersimpan";
			}

		}
		else
		{
			$rekanan_saham->setField("REKANAN_SAHAM_ID",$reqSahamId);
			$rekanan_saham->setField("KEPEMILIKAN",$reqKepemilikan);
			$rekanan_saham->setField("NAMA",$reqPemegangSaham);
			$rekanan_saham->setField("KTP",$reqNomorKTP);
			$rekanan_saham->setField("NPWP",$reqNomorNPWP);
			$rekanan_saham->setField("ALAMAT",$reqAlamat);
			$rekanan_saham->setField("JUMLAH_SAHAM",$reqPersentase);
			$rekanan_saham->setField("REKANAN_ID",$this->ID);
			$rekanan_saham->setField('CREATED_BY', $this->USER_LOGIN_ID);
			$rekanan_saham->setField("KEWARGANEGARAAN",$reqKewarganegaraan);
			$rekanan_saham->setField("JENIS_KELAMIN",$reqJenisKelamin);
			$rekanan_saham->setField("NEGARA",$reqNegara);
			$rekanan_saham->setField("NOMINAL_SAHAM",dotToNo($reqNominalSaham));

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
			$rekanan_saham->setField("PATH_FILE", $insertLinkFile);
			$rekanan_saham->setField("TIPE_FILE", $insertLinkFilesExe);
			$rekanan_saham->setField("UKURAN", ValToNullDB($insertLinkFilesSize));
			$rekanan_saham->setField("NAMA_FILE", $insertLinkFileNama);

			if($rekanan_saham->update2())
			{
				echo "Data berhasil diupdate";

			}
			else
			{
				echo "Data Gagal Tersimpan";
			}
		}
	}

	function registrasi()
	{
		$this->load->library("kauth");
		$userLogin = new kauth();

		$this->load->model("Rekanan");
		$this->load->model("RekananSaham");

		/* create objects */
		$rekanan = new Rekanan();
		$rekanan_saham 	= new RekananSaham();

		$reqSahamId= $this->input->post('reqSahamId');
		$reqPemegangSaham= $this->input->post('reqPemegangSaham');
		$reqNomorKTP= $this->input->post('reqNomorKTP');
		$reqAlamat= $this->input->post('reqAlamat');
		$reqPersentase= $this->input->post('reqPersentase');
		$reqId= $this->input->post('reqId');
		$reqSubmit= $this->input->post('reqSubmit');
		$reqMode= $this->input->post('reqMode');
		$reqRekananId= $this->input->post('reqRekananId');

		$reqId = $this->ID;

		$rekanan_saham->setField("REKANAN_ID", $this->ID);
		$rekanan_saham->delete_kepemilikan_saham();
		for($i=0; $i<count($reqPemegangSaham);$i++)
		{
			if($reqPemegangSaham[$i] == "")
			{}
			else
			{
				$rekanan_saham 	= new RekananSaham();
				$rekanan_saham->setField("REKANAN_ID",$this->ID);
				$rekanan_saham->setField("NAMA",$reqPemegangSaham[$i]);
				$rekanan_saham->setField("KTP",$reqNomorKTP[$i]);
				$rekanan_saham->setField("ALAMAT",$reqAlamat[$i]);
				$rekanan_saham->setField("JUMLAH_SAHAM",$reqPersentase[$i]);
				$rekanan_saham->setField("STATUS",'1');
				$rekanan_saham->insert();
				unset($rekanan_saham);
			}
		}
		echo "Data berhasil disimpan";
	}

	function delete()
	{
		$this->load->library("kauth");
		$userLogin = new kauth();

		$this->load->model("Rekanan");
		$this->load->model("RekananSaham");

		$reqId = $this->input->get("reqId");
		/* create objects */
		$rekanan = new Rekanan();
		$rekanan_saham 	= new RekananSaham();

		$rekanan_saham->setField("REKANAN_SAHAM_ID", $reqId);
		$rekanan_saham->setField("REKANAN_ID", $this->ID);

		if($rekanan_saham->delete())
		{
			echo "Data berhasil di hapus";
		}
		else
		{
			echo "Data gagal di hapus";
		}
	}
}
?>
