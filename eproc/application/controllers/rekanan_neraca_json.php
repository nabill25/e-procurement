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

class rekanan_neraca_json extends CI_Controller {

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

	function data_administrasi_keuangan_neraca_tambah()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Rekanan");
		$this->load->model("RekananNeraca");
		$this->load->library("FileHandler");
		$file = new FileHandler();
		$file2 = new FileHandler();

		/* create objects */
		$rekanan = new Rekanan();
		$rekanan_neraca	= new RekananNeraca();

		$reqId = $this->ID;
		$reqTahunNeraca= $this->input->post("reqTahunNeraca");
		$reqTahun= $this->input->post("reqTahun");
		$reqKekayaanBersih= $this->input->post("reqKekayaanBersih");
		$reqAuditor= $this->input->post("reqAuditor");
		$reqNomor= $this->input->post("reqNomor");
		$reqTanggal= $this->input->post("reqTanggal");
		$reqKesimpulan= $this->input->post("reqKesimpulan");
		$reqSubmit= $this->input->post("reqSubmit");
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqLinkFileTemp = $this->input->post("reqLinkFileTemp");
		$reqLinkFileTempTipe = $this->input->post("reqLinkFileTempTipe");
		$reqLinkFileTempUkuran = $this->input->post("reqLinkFileTempUkuran");
		$reqLinkFileTempNama = $this->input->post("reqLinkFileTempNama");

		$tempModalNeraca = dotToNo($reqKekayaanBersih);
		$tempAuditNamaNeraca = $reqAuditor;
		$tempAuditNomorNeraca = $reqNomor;
		$tempAuditTanggalNeraca = dateToPageCheck($reqTanggal);
		$tempAuditKeteranganNeraca = $reqKesimpulan;


		$reqLinkFile2= $_FILES['reqLinkFile2'];
		$reqLinkFileTemp2 = $this->input->post("reqLinkFileTemp2");
		$reqLinkFileTempTipe2 = $this->input->post("reqLinkFileTempTipe2");
		$reqLinkFileTempUkuran2 = $this->input->post("reqLinkFileTempUkuran2");
		$reqLinkFileTempNama2 = $this->input->post("reqLinkFileTempNama2");


		$FILE_DIR = "uploads/neraca_keuangan/";

		$rekanan_neraca->setField('REKANAN_ID', $this->ID);
		$rekanan_neraca->setField('TAHUN', $reqTahunNeraca);
		$rekanan_neraca->setField('MODAL', dotToNo($reqKekayaanBersih));
		$rekanan_neraca->setField('AUDIT_NAMA', $reqAuditor);
		$rekanan_neraca->setField('AUDIT_NOMOR', $reqNomor);
		$rekanan_neraca->setField('AKTIVA', "NULL");
		$rekanan_neraca->setField('PASIVA', "NULL");
		$rekanan_neraca->setField('AUDIT_TANGGAL', dateToDBCheck($reqTanggal));
		$rekanan_neraca->setField('AUDIT_KESIMPULAN', $reqKesimpulan);

		$neraca_save = new RekananNeraca();
		$allRecord = $neraca_save->getCountByParams(array("REKANAN_ID"=>$this->ID, "TAHUN"=>$reqTahunNeraca));
		$neraca_save->selectByParams(array("REKANAN_ID"=>$this->ID, "TAHUN"=>$reqTahunNeraca));
		$neraca_save->firstRow();
		$tmp = $neraca_save->getField("REKANAN_NERACA_ID");

		unset($neraca_save);

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

		/* UPLOAD FILE 2 */
		$renameFile2 = md5(date("dmYHis").$reqLinkFile2['name'].$this->ID).".".getExtension($reqLinkFile2['name']);
		if($file2->uploadToDir('reqLinkFile2', $FILE_DIR, $renameFile2))
		{
			$insertLinkFilesSize2 = $file2->uploadedSize;
			$insertLinkFilesExe2 =  $file2->uploadedExtension;
			$insertLinkFile2 =  $renameFile2;
			$insertLinkFileNama2 =  $reqLinkFile2['name'];
		}
		else
		{
			$insertLinkFilesSize2 = $reqLinkFileTempUkuran2;
			$insertLinkFilesExe2 =  $reqLinkFileTempTipe2;
			$insertLinkFile2 =  $reqLinkFileTemp2;
			$insertLinkFileNama2 =  $reqLinkFileTempNama2;
		}
		/* END UPLOAD FILE */

		$rekanan_neraca->setField("UKURAN", $insertLinkFilesSize);
		$rekanan_neraca->setField("TIPE", $insertLinkFilesExe);
		$rekanan_neraca->setField("PATH_FILE", $insertLinkFile);
		$rekanan_neraca->setField("NAMA_FILE", $insertLinkFileNama);

		$rekanan_neraca->setField("UKURAN2", $insertLinkFilesSize2);
		$rekanan_neraca->setField("TIPE2", $insertLinkFilesExe2);
		$rekanan_neraca->setField("PATH_FILE2", $insertLinkFile2);
		$rekanan_neraca->setField("NAMA_FILE2", $insertLinkFileNama2);

		$rekanan_neraca->setField('REKANAN_NERACA_ID', $tmp);
		$rekanan_neraca->setField('CREATED_BY', $this->USER_LOGIN_ID);
		if($allRecord > 0)
		{
			if($rekanan_neraca->update())
			{
				echo "Data berhasil di Update";
			}
			else
			{
				echo "Data Gagal Tersimpan";
			}
		}
		else
		{
			if($rekanan_neraca->insert())
			{
				echo "Data berhasil di Simpan";
			}
			else
			{
				echo "Data Gagal Tersimpan";
			}
	  }
    }

	function data_administrasi_keuangan_neraca_syarat()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Rekanan");
		$this->load->model("RekananNeraca");
		$this->load->library("FileHandler");
		$file = new FileHandler();

		/* create objects */
		$rekanan = new Rekanan();
		$rekanan_neraca	= new RekananNeraca();

		$reqId = $this->ID;
		$reqTahunNeraca= $this->input->post("reqTahunNeraca");
		$reqTahunNeracaSyarat= $this->input->post("reqTahunNeracaSyarat");
		$reqTahun= $this->input->post("reqTahun");
		$reqKekayaanBersih= $this->input->post("reqKekayaanBersih");
		$reqAuditor= $this->input->post("reqAuditor");
		$reqNomor= $this->input->post("reqNomor");
		$reqTanggal= $this->input->post("reqTanggal");
		$reqKesimpulan= $this->input->post("reqKesimpulan");
		$reqSubmit= $this->input->post("reqSubmit");
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqLinkFileTemp = $this->input->post("reqLinkFileTemp");
		$reqLinkFileTempTipe = $this->input->post("reqLinkFileTempTipe");
		$reqLinkFileTempUkuran = $this->input->post("reqLinkFileTempUkuran");
		$reqLinkFileTempNama = $this->input->post("reqLinkFileTempNama");


		$FILE_DIR = "uploads/neraca_keuangan/";

		$rekanan_neraca->setField('REKANAN_ID', $this->ID);
		$rekanan_neraca->setField('TAHUN', $reqTahunNeraca);
		$rekanan_neraca->setField('MODAL', dotToNo($reqKekayaanBersih));
		$rekanan_neraca->setField('AUDIT_NAMA', $reqAuditor);
		$rekanan_neraca->setField('AUDIT_NOMOR', $reqNomor);
		$rekanan_neraca->setField('AUDIT_TANGGAL', dateToDBCheck($reqTanggal));
		$rekanan_neraca->setField('AUDIT_KESIMPULAN', $reqKesimpulan);

		$neraca_save = new RekananNeraca();
		$allRecord = $neraca_save->getCountByParams(array("REKANAN_ID"=>$this->ID, "TAHUN"=>$reqTahunNeraca));
		$neraca_save->selectByParams(array("REKANAN_ID"=>$this->ID, "TAHUN"=>$reqTahunNeraca));
		$neraca_save->firstRow();
		$tmp = $neraca_save->getField("REKANAN_NERACA_ID");

		unset($neraca_save);

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

		$rekanan_neraca->setField("UKURAN", $insertLinkFilesSize);
		$rekanan_neraca->setField("TIPE", $insertLinkFilesExe);
		$rekanan_neraca->setField("PATH_FILE", $insertLinkFile);
		$rekanan_neraca->setField("NAMA_FILE", $insertLinkFileNama);
		$rekanan_neraca->setField("AKTIVA", "NULL");
		$rekanan_neraca->setField("PASIVA", "NULL");

		$rekanan_neraca->setField('REKANAN_NERACA_ID', $tmp);

		$arrTahunSyarat = explode("/", $reqTahunNeracaSyarat);
		$tahunSyarat1 = $arrTahunSyarat[0];
		$tahunSyarat2 = $arrTahunSyarat[1];

		if($allRecord > 0)
		{

			if($rekanan_neraca->update())
			{
				if($reqTahunNeraca == $tahunSyarat1 || $reqTahunNeraca == $tahunSyarat2)
					echo "1";
			}
			else
			{
				echo "Data gagal di Update";
			}
		}
		else
		{
			if($rekanan_neraca->insert())
			{

				if($reqTahunNeraca == $tahunSyarat1 || $reqTahunNeraca == $tahunSyarat2)
					echo "1";
			}
			else
			{	echo "Data gagal di Simpan";
			}
	  }
   }

   function reload_neraca()
   {
	   $reqPaketId = $this->input->get("reqPaketId");

		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("RekananNeraca");

		$paketInfo->getPaket($reqPaketId);

		$rekanan_neraca = new RekananNeraca();
		$rekanan_neraca->selectByParams(array("REKANAN_ID" => $userLogin->userRekanan),-1,-1, " AND TAHUN IN (".str_replace("/", ",",$paketInfo->syarat_neraca_tahun).") ");

		$i = 0;

		while($rekanan_neraca->nextRow())
		{
			$met[$i]['NOMOR'] = $rekanan_neraca->getField("AUDIT_NOMOR");
			$met[$i]['TANGGAL'] = dateToPage($rekanan_neraca->getField("AUDIT_TANGGAL"));
			$met[$i]['MODAL'] = numberToIna($rekanan_neraca->getField("MODAL"));
			$i++;
		}

		echo json_encode($met);
   }

}
?>
