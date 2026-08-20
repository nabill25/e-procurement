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

class rekanan_rekening_koran_json extends CI_Controller {

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

	function data_keuangan_rekening_koran_ubah()
	{
		/* INCLUDE FILE */
		$this->load->model("Rekanan");
		$this->load->model("RekananRekeningKoran");
		$this->load->model("MataUang");
		$this->load->model("Bank");
		$this->load->library("FileHandler");
		$file = new FileHandler();

		/* create objects */
		$rekanan = new Rekanan();
		$rekanan_koran = new RekananRekeningKoran();
		$mata_uang = new MataUang();
		$bank = new Bank();

		$reqNoRekening= $this->input->post("reqNoRekening");
		$reqNamaBank= $this->input->post("reqNamaBank");
		$reqBulan= $this->input->post("reqBulan");
		$reqAuditor= $this->input->post("reqAuditor");
		$reqMataUang= $this->input->post("reqMataUang");
		$reqNilai= $this->input->post("reqNilai");
		$reqId= $this->input->post("reqId");
		$reqSubmit= $this->input->post("reqSubmit");
		$reqRekeningKoranId= $this->input->post("reqRekeningKoranId");
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqLinkFileTemp = $this->input->post("reqLinkFileTemp");
		$reqLinkFileTempTipe = $this->input->post("reqLinkFileTempTipe");
		$reqLinkFileTempUkuran = $this->input->post("reqLinkFileTempUkuran");
		$reqMode = $this->input->post("reqMode");
		$reqBankId = $this->input->post("reqBankId");
		$reqLinkFileTempNama = $this->input->post("reqLinkFileTempNama");

		$reqId = $this->ID;

		$FILE_DIR = "uploads/rekening_koran/";

		// $rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
		// $rekanan->firstRow();

		if($reqMode=='insert')
		{
			$bank_insert = new Bank();
			$bank_insert->selectByParams(array("BANK_ID" => $reqBankId));
			$bank_insert->firstRow();
			//echo $bank_insert->query;exit;
			$rekanan_koran->setField('REKANAN_ID', $this->ID);
			$rekanan_koran->setField('NOMOR', $reqNoRekening);
			$rekanan_koran->setField('NAMA', $bank_insert->getField("NAMA"));
			$rekanan_koran->setField('BULAN', $reqBulan);
			$rekanan_koran->setField('TAHUN', $reqAuditor);
			$rekanan_koran->setField('MATA_UANG_ID', $reqMataUang);
			$rekanan_koran->setField('BANK_ID', $reqBankId);
			$rekanan_koran->setField('NILAI', dotToNo($reqNilai));

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
			$rekanan_koran->setField("UKURAN", $insertLinkFilesSize);
			$rekanan_koran->setField("TIPE", $insertLinkFilesExe);
			$rekanan_koran->setField("PATH_FILE", $insertLinkFile);
			$rekanan_koran->setField("NAMA_FILE", $insertLinkFileNama);
			$rekanan_koran->setField('CREATED_BY', $this->USER_LOGIN_ID);

			if($rekanan_koran->insert())
			{
				echo "Data berhasil di Simpan";
			}
			else
			{
				echo "Data Gagal Tersimpan";
			}
		}
		else
		{
			$bank_insert = new Bank();
			$bank_insert->selectByParams(array("BANK_ID" => $reqBankId));
			$bank_insert->firstRow();

			$rekanan_koran->setField('REKANAN_REKENING_KORAN_ID', $reqRekeningKoranId);
			$rekanan_koran->setField('NOMOR', $reqNoRekening);
			$rekanan_koran->setField('NAMA', $bank_insert->getField("NAMA"));
			$rekanan_koran->setField('BULAN', $reqBulan);
			$rekanan_koran->setField('TAHUN', $reqAuditor);
			$rekanan_koran->setField('MATA_UANG_ID', $reqMataUang);
			$rekanan_koran->setField('BANK_ID', $reqBankId);
			$rekanan_koran->setField('NILAI', dotToNo($reqNilai));
			$rekanan_koran->setField("REKANAN_ID", $this->ID);

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
			$rekanan_koran->setField("UKURAN", $insertLinkFilesSize);
			$rekanan_koran->setField("TIPE", $insertLinkFilesExe);
			$rekanan_koran->setField("PATH_FILE", $insertLinkFile);
			$rekanan_koran->setField("NAMA_FILE", $insertLinkFileNama);
			$rekanan_koran->setField('CREATED_BY', $this->USER_LOGIN_ID);

			if($rekanan_koran->update())
			{
				echo "Data Berhasil di update";
			}
			else
			{
				echo "Data Gagal Tersimpan";
			}
		}

	}

	function data_keuangan_rekening_koran_syarat()
	{
		/* INCLUDE FILE */
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("Rekanan");
		$this->load->model("RekananRekeningKoran");
		$this->load->model("MataUang");
		$this->load->model("Bank");
		$this->load->model("Paket");
		$this->load->library("FileHandler");
		$file = new FileHandler();

		/* create objects */
		$rekanan = new Rekanan();
		$rekanan_koran = new RekananRekeningKoran();
		$mata_uang = new MataUang();
		$bank = new Bank();

		$reqNoRekening= $this->input->post("reqNoRekening");
		$reqNamaBank= $this->input->post("reqNamaBank");
		$reqBulan= $this->input->post("reqBulan");
		$reqAuditor= $this->input->post("reqAuditor");
		$reqMataUang= $this->input->post("reqMataUang");
		$reqNilai= $this->input->post("reqNilai");
		$reqId= $this->input->post("reqId");
		$reqPaketId= $this->input->post("reqPaketId");
		$reqSubmit= $this->input->post("reqSubmit");
		$reqRekeningKoranId= $this->input->post("reqRekeningKoranId");
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqLinkFileTemp = $this->input->post("reqLinkFileTemp");
		$reqLinkFileTempTipe = $this->input->post("reqLinkFileTempTipe");
		$reqLinkFileTempUkuran = $this->input->post("reqLinkFileTempUkuran");
		$reqMode = $this->input->post("reqMode");
		$reqBankId = $this->input->post("reqBankId");

		$reqId = $this->ID;

		$FILE_DIR = "uploads/rekening_koran/";

		// $rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
		// $rekanan->firstRow();

		if($reqMode=='insert')
		{

			$bank_insert = new Bank();
			$bank_insert->selectByParams(array("BANK_ID" => $reqBankId));
			$bank_insert->firstRow();

			$rekanan_koran->setField('REKANAN_ID', $this->ID);
			$rekanan_koran->setField('NOMOR', $reqNoRekening);
			$rekanan_koran->setField('NAMA', $bank_insert->getField("NAMA"));
			$rekanan_koran->setField('BULAN', $reqBulan);
			$rekanan_koran->setField('TAHUN', $reqAuditor);
			$rekanan_koran->setField('MATA_UANG_ID', $reqMataUang);
			$rekanan_koran->setField('BANK_ID', $reqBankId);
			$rekanan_koran->setField('NILAI', dotToNo($reqNilai));
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
			$rekanan_koran->setField("UKURAN", $insertLinkFilesSize);
			$rekanan_koran->setField("TIPE", $insertLinkFilesExe);
			$rekanan_koran->setField("PATH_FILE", $insertLinkFile);
			$rekanan_koran->setField("NAMA_FILE", $insertLinkFileNama);
			$rekanan_koran->insert();
		}
		else
		{

			$bank_insert = new Bank();
			$bank_insert->selectByParams(array("BANK_ID" => $reqBankId));
			$bank_insert->firstRow();

			$rekanan_koran->setField('REKANAN_REKENING_KORAN_ID', $reqRekeningKoranId);
			$rekanan_koran->setField('NOMOR', $reqNoRekening);
			$rekanan_koran->setField('NAMA', $bank_insert->getField("NAMA"));
			$rekanan_koran->setField('BULAN', $reqBulan);
			$rekanan_koran->setField('TAHUN', $reqAuditor);
			$rekanan_koran->setField('MATA_UANG_ID', $reqMataUang);
			$rekanan_koran->setField('BANK_ID', $reqNamaBank);
			$rekanan_koran->setField('NILAI', dotToNo($reqNilai));
			$rekanan_koran->setField("REKANAN_ID",$this->ID);

			$rekanan_koran->setField("UKURAN", $insertLinkFilesSize);
			$rekanan_koran->setField("TIPE", $insertLinkFilesExe);
			$rekanan_koran->setField("PATH_FILE", $insertLinkFile);
			$rekanan_koran->update();
		}

		/* HITUNG DATA REKENING KORAN */

		$paketInfo->getPaket($reqPaketId);

		$paket_rekening_koran = new Paket();
		$arrSyaratBulan = explode(", ",$paketInfo->syarat_rekening_koran_bulan);
		$rekening_koran = $paket_rekening_koran->getPaketRekeningKoran($this->ID, getValueArrayMonth($arrSyaratBulan));
		if($rekening_koran == 3){
			echo "1";
		}
		else
			echo "0";

	}

	function delete()
	{
		/* INCLUDE FILE */
		$this->load->model("Rekanan");
		$this->load->model("RekananRekeningKoran");
		$this->load->library("FileHandler");

		$reqId = $this->input->get("reqId");

		/* create objects */
		$rekanan = new Rekanan();
		$rekanan_tahun_select = new RekananRekeningKoran();
		$rekanan_tahun = new RekananRekeningKoran();
		$rekanan_koran = new RekananRekeningKoran();
		$rekanan_tahun_selectGet = new RekananRekeningKoran();
		$file = new FileHandler();

		$rekanan_koran->setField("REKANAN_REKENING_KORAN_ID", $reqId);
		$rekanan_koran->setField("REKANAN_ID", $this->ID);
		$FILE_DIR = "uploads/rekening_koran/";

		$rekanan_pengalaman_file = new RekananRekeningKoran();
		$rekanan_pengalaman_file->selectByParams(array("REKANAN_REKENING_KORAN_ID"=>$reqId), -1, -1," ");
		$rekanan_pengalaman_file->firstRow();
		$varSource = $FILE_DIR.$rekanan_pengalaman_file->getField('PATH_FILE');
		$fileSource = $rekanan_pengalaman_file->getField('PATH_FILE');

		if($rekanan_koran->delete())
		{
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

	function registrasi()
	{
		$this->load->library("kauth");
		$userLogin = new kauth();
		$this->load->library("FileHandler");

		$this->load->model("Rekanan");
		$this->load->model("Bank");
		$this->load->model("RekananRekeningKoran");

		/* create objects */
		$rekanan = new Rekanan();
		$rekanan_koran 	= new RekananRekeningKoran();

		$reqNoRekening= $this->input->post("reqNoRekening");
		$reqNamaBank= $this->input->post("reqNamaBank");
		$reqBulan= $this->input->post("reqBulan");
		$reqAuditor= $this->input->post("reqAuditor");
		$reqMataUang= $this->input->post("reqMataUang");
		$reqNilai= $this->input->post("reqNilai");
		$reqId= $this->input->post("reqId");
		$reqSubmit= $this->input->post("reqSubmit");
		$reqRekeningKoranId= $this->input->post("reqRekeningKoranId");
		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqLinkFileTemp = $this->input->post("reqLinkFileTemp");
		$reqLinkFileTempTipe = $this->input->post("reqLinkFileTempTipe");
		$reqLinkFileTempUkuran = $this->input->post("reqLinkFileTempUkuran");
		$reqMode = $this->input->post("reqMode");
		$reqBankId = $this->input->post("reqBankId");
		$reqPeriode = $this->input->post("reqPeriode");
		$reqLinkFileTempNama = $this->input->post("reqLinkFileTempNama");
		$FILE_DIR = "uploads/rekening_koran/";
		$rekanan_koran->setField("REKANAN_ID", $this->ID);

		// $reqRekananRekeningKoranId = $this->input->post("reqRekananRekeningKoranId");
		// if (count($reqRekananRekeningKoranId) > 0) {
		// 	for ($j=0; $j < count($reqRekananRekeningKoranId) ; $j++) {
		// 		$rekanan_koran->setField("REKANAN_REKENING_KORAN_ID", $reqRekananRekeningKoranId[$j]);
		// 		$rekanan_koran->delete();
		// 	}
		// }
		for($i=0; $i<count($reqNoRekening);$i++)
		{
			if($reqNoRekening[$i] == "")
			{}
			else
			{
				$bank_insert = new Bank();
				$bank_insert->selectByParams(array("BANK_ID" => $reqBankId[$i]));
				$bank_insert->firstRow();
				//echo $bank_insert->query;exit;
				//echo $reqPeriode[$i];
				$reqTahun = substr($reqPeriode[$i], 2, 4);
				$reqBulan = substr($reqPeriode[$i], 0, 2);
				$rekanan_koran 	= new RekananRekeningKoran();
				$rekanan_koran->setField('REKANAN_ID', $this->ID);
				$rekanan_koran->setField('NOMOR', $reqNoRekening[$i]);
				$rekanan_koran->setField('NAMA', $bank_insert->getField("NAMA"));
				$rekanan_koran->setField('BULAN', (int)$reqBulan);
				$rekanan_koran->setField('TAHUN', $reqTahun);
				$rekanan_koran->setField('MATA_UANG_ID', 1);
				$rekanan_koran->setField('BANK_ID', $reqBankId[$i]);
				$rekanan_koran->setField('NILAI', dotToNo($reqNilai[$i]));
				$file = new FileHandler();
				$renameFile = md5(date("dmYHis").$reqLinkFile['name'][$i].$this->ID).".".getExtension($reqLinkFile['name'][$i]);
				if($file->uploadToDirArray('reqLinkFile', $FILE_DIR, $renameFile, $i))
				{
					$insertLinkFilesSize = $file->uploadedSize;
					$insertLinkFilesExe =  $file->uploadedExtension;
					$insertLinkFile =  $renameFile;
					$insertLinkFileNama =  $reqLinkFile['name'][$i];
				}
				else
				{
					$insertLinkFilesSize = $reqLinkFileTempUkuran[$i];
					$insertLinkFilesExe =  $reqLinkFileTempTipe[$i];
					$insertLinkFile =  $reqLinkFileTemp[$i];
					$insertLinkFileNama =  $reqLinkFileTempNama[$i];
				}
				$rekanan_koran->setField("UKURAN", $insertLinkFilesSize);
				$rekanan_koran->setField("TIPE", $insertLinkFilesExe);
				$rekanan_koran->setField("PATH_FILE", $insertLinkFile);
				$rekanan_koran->setField("NAMA_FILE", $insertLinkFileNama);
				$rekanan_koran->insert();
			}
			unset($rekanan_koran);
			unset($bank_insert);
			unset($file);
		}
		echo "Data berhasil disimpan";
	}

}
?>
