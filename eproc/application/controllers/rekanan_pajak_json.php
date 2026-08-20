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

class rekanan_pajak_json extends CI_Controller {

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
	
	function data_administrasi_keuangan_pajak_tambah()
	{
		/* INCLUDE FILE */
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("Rekanan");
		$this->load->model("RekananPajak");
		$this->load->library("FileHandler"); 
		
		/* create objects */
		$rekanan = new Rekanan();
		$rekanan_pph	= new RekananPajak(); // tipe 2
		$rekanan_ppn	= new RekananPajak(); // tipe 3
		$rekanan_tahun_select = new RekananPajak();
		$rekanan_tahun_selectGet = new RekananPajak();
		
		$reqId = $this->ID;
		
		$reqSubmit= $this->input->post("reqSubmit");
		$reqTahunPajak= $this->input->post('reqTahunPajak');
		$reqBulanPPH = $_POST["reqBulanPPH"];
		$reqNomorPPH = $_POST["reqNomorPPH"];
		$reqTanggalPPH = $_POST["reqTanggalPPH"];
		$reqLinkFilePPH= $_FILES['reqLinkFilePPH'];
		$reqLinkFilePPHTemp = $_POST["reqLinkFilePPHTemp"];
		$reqLinkFilePPHTempNama = $_POST["reqLinkFilePPHTempNama"];
		
		$reqBulanPPN = $_POST["reqBulanPPN"];
		$reqNomorPPN = $_POST["reqNomorPPN"];
		$reqTanggalPPN = $_POST["reqTanggalPPN"];
		$reqLinkFilePPN= $_FILES['reqLinkFilePPN'];
		$reqLinkFilePPNTemp = $_POST["reqLinkFilePPNTemp"];
		$reqLinkFilePPNTempNama = $_POST["reqLinkFilePPNTempNama"];
		
		$FILE_DIR_KUALIFIKASI = "uploads/ppn_pph/";
		// Input PPH
		// $i=0;
		// while($i <= 11)
		// {
		// 	if($reqNomorPPH[$i] == "")
		// 	{}
		// 	else
		// 	{			
		// 		if($bulan_gagal_pph == "")			
		// 			$bulan_gagal_pph = getNameMonth($reqBulanPPH[$i]);
		// 		else
		// 			$bulan_gagal_pph .= ", ".getNameMonth($reqBulanPPH[$i]);
		// 	}
		// 	$pph_loop = new RekananPajak();
		// 	$pph_loop->setField('NOMOR', $reqNomorPPH[$i]);
		// 	$pph_loop->setField('TANGGAL', dateToDBCheck($reqTanggalPPH[$i]));
		// 	$pph_loop->setField('BULAN', $reqBulanPPH[$i]);
		// 	$pph_loop->setField('TIPE', '2');
		// 	$pph_loop->setField('REKANAN_ID', $this->ID);
		// 	$pph_loop->setField('TAHUN', $reqTahunPajak);
			
		// 	$file = new FileHandler();
			
		// 	$insertLinkFile = "";
		// 	$renameFile = md5(date("dmYHis").$reqLinkFilePPH['name'][$i].$this->ID).".".getExtension($reqLinkFilePPH['name'][$i]);
		// 	if($file->uploadToDirArray('reqLinkFilePPH', $FILE_DIR_KUALIFIKASI, $renameFile, $i))
		// 	{
		// 		$insertLinkFile =  $renameFile;
		// 		$insertLinkFileNama =  $reqLinkFilePPH['name'][$i];
		// 	}
		// 	else
		// 	{
		// 		$insertLinkFile =  $reqLinkFilePPHTemp[$i];
		// 		$insertLinkFileNama =  $reqLinkFilePPHTempNama[$i];
		// 	}
		// 	unset($file);
			
		// 	$pph_loop->setField("PATH_FILE", $insertLinkFile);
		// 	$pph_loop->setField("NAMA_FILE", $insertLinkFileNama);
						
		// 	$ppn_save = new RekananPajak();
		// 	$allRecord = $ppn_save->getCountByParams(array("TIPE"=>2, "BULAN"=>$reqBulanPPH[$i], "REKANAN_ID"=>$this->ID, "TAHUN"=>$reqTahunPajak));
		// 	$ppn_save->selectByParams(array("TIPE"=>2, "BULAN"=>$reqBulanPPH[$i], "REKANAN_ID"=>$this->ID, "TAHUN"=>$reqTahunPajak));
		// 	$ppn_save->firstRow();
			
		// 	$tmp = $ppn_save->getField("REKANAN_PAJAK_ID");
		// 	unset($ppn_save);
			
		// 	$pph_loop->setField('REKANAN_PAJAK_ID', $tmp);
		// 	$pph_loop->setField('CREATED_BY', $this->USER_LOGIN_ID);
			
		// 	if($allRecord > 0){
		// 		if($pph_loop->update())
		// 		{
		// 			$alertMsg .= "Data berhasil diupdate";
		// 		}
		// 		else
		// 		{
		// 			$alertMsg .= "Update failed : ".$pph_loop->query;
		// 		}
		// 	}else{
		// 		if($pph_loop->insert())
		// 		{
		// 			$alertMsg .= "Data berhasil diupdate";
		// 		}
		// 		else
		// 		{
		// 			$alertMsg .= "Update failed : ".$pph_loop->query;
		// 		}
		// 	unset($pph_loop);
		// 	}
			
		// 	$i++;
		// }
		
		// Input PPN
		$i=0;
		while($i <= 11)
		{
			if($reqNomorPPN[$i] == "")
			{}
			else
			{			
				if($bulan_gagal_ppn == "")			
					$bulan_gagal_ppn = getNameMonth($reqBulanPPN[$i]);
				else
					$bulan_gagal_ppn .= ", ".getNameMonth($reqBulanPPN[$i]);
			}
			$FILE_DIR_KUALIFIKASI = "uploads/ppn_pph/";
			$ppn_loop = new RekananPajak();
			$ppn_loop->setField('NOMOR', $reqNomorPPN[$i]);
			$ppn_loop->setField('TANGGAL', dateToDBCheck($reqTanggalPPN[$i]));
			$ppn_loop->setField('BULAN', $reqBulanPPN[$i]);
			$ppn_loop->setField('TIPE', '3');
			$ppn_loop->setField('REKANAN_ID', $this->ID);
			$ppn_loop->setField('TAHUN', $reqTahunPajak);
			
			$file = new FileHandler();
			$insertLinkFile = "";
			$renameFile = md5(date("dmYHis").$reqLinkFilePPN['name'][$i].$this->ID).".".getExtension($reqLinkFilePPN['name'][$i]);
			if($file->uploadToDirArray('reqLinkFilePPN', $FILE_DIR_KUALIFIKASI, $renameFile, $i))
			{
				$insertLinkFile =  $renameFile;
				$insertLinkFileNama =  $reqLinkFilePPN['name'][$i];
			}
			else
			{
				$insertLinkFile =  $reqLinkFilePPNTemp[$i];
				$insertLinkFileNama =  $reqLinkFilePPNTempNama[$i];
			}
			
			unset($file);
			
			$ppn_loop->setField("PATH_FILE", $insertLinkFile);
			$ppn_loop->setField("NAMA_FILE", $insertLinkFileNama);
						
			$ppn_save = new RekananPajak();
			$allRecord = $ppn_save->getCountByParams(array("TIPE"=>3, "BULAN"=>$reqBulanPPN[$i], "REKANAN_ID"=>$this->ID, "TAHUN"=>$reqTahunPajak));
			$ppn_save->selectByParams(array("TIPE"=>3, "BULAN"=>$reqBulanPPN[$i], "REKANAN_ID"=>$this->ID, "TAHUN"=>$reqTahunPajak));
			$ppn_save->firstRow();
			$tmp = $ppn_save->getField("REKANAN_PAJAK_ID");
			unset($ppn_save);
			
			$ppn_loop->setField('REKANAN_PAJAK_ID', $tmp);
			$ppn_loop->setField('CREATED_BY', $this->USER_LOGIN_ID);
			
			if($allRecord > 0){
				if($ppn_loop->update())
				{
					echo "Data berhasil diupdate";
				}
				else
				{
					echo "Data Gagal Tersimpan";
				}
			}else{
				if($ppn_loop->insert())
				{
					echo "Data berhasil diupdate";
				}
				else
				{
					echo "Data Gagal Tersimpan";
				}
				unset($ppn_loop);
			}
			$i++;
		}	
			
		if($bulan_gagal_pph == "" && $bulan_gagal_ppn == "")
		{
			echo "Data berhasil di Simpan";
		}
		else
		{
			if($bulan_gagal_pph)
				$pesan_pph = "PPh bulan ".$bulan_gagal_pph;
				
			if($bulan_gagal_ppn)
				$pesan_ppn = "PPN bulan ".$bulan_gagal_ppn;
			echo "Data Gagal Tersimpan";
		}
		
	}


	function data_administrasi_keuangan_pajak_syarat()
	{
		/* INCLUDE FILE */
		$this->load->model("Rekanan");
		$this->load->model("RekananPajak");
		$this->load->library("FileHandler"); 
		
		/* create objects */
		$rekanan = new Rekanan();
		$rekanan_pph	= new RekananPajak(); // tipe 2
		$rekanan_ppn	= new RekananPajak(); // tipe 3
		
		$reqId = $this->ID;
		
		$reqSubmit = $this->input->post("reqSubmit");
		$reqTahunPajak= $this->input->post('reqTahunPajak');
		$reqSyaratPPH= $this->input->post('reqSyaratPPH');
		$reqSyaratPPN= $this->input->post('reqSyaratPPN');
		$reqTipe = $this->input->post('reqTipe');
		
		
		$reqBulanPPH = $_POST["reqBulanPPH"];
		$reqNomorPPH = $_POST["reqNomorPPH"];
		$reqTanggalPPH = $_POST["reqTanggalPPH"];
		$reqLinkFilePPH= $_FILES['reqLinkFilePPH'];
		$reqLinkFilePPHTemp = $_POST["reqLinkFilePPHTemp"];
		$reqLinkFilePPHTempNama = $_POST["reqLinkFilePPHTempNama"];
		
		$reqBulanPPN = $_POST["reqBulanPPN"];
		$reqNomorPPN = $_POST["reqNomorPPN"];
		$reqTanggalPPN = $_POST["reqTanggalPPN"];
		$reqLinkFilePPN= $_FILES['reqLinkFilePPN'];
		$reqLinkFilePPNTemp = $_POST["reqLinkFilePPNTemp"];
		$reqLinkFilePPNTempNama = $_POST["reqLinkFilePPNTempNama"];
				
		$FILE_DIR_KUALIFIKASI = "uploads/ppn_pph/";
		$i=0;
		while($i <= 2)
		{
			if($reqNomorPPH[$i] == "")
			{}
			else
			{			

				$reqBulan = (int)substr($reqBulanPPH[$i], 0, 2);
				$reqTahun = substr($reqBulanPPH[$i], 2, 4);

				$pph_loop = new RekananPajak();
				$pph_loop->setField('NOMOR', $reqNomorPPH[$i]);
				$pph_loop->setField('TANGGAL', dateToDBCheck($reqTanggalPPH[$i]));
				$pph_loop->setField('BULAN', $reqBulan);
				$pph_loop->setField('TIPE', '2');
				$pph_loop->setField('REKANAN_ID', $this->ID);
				$pph_loop->setField('TAHUN', $reqTahun);
				
				$file = new FileHandler();
				
				$insertLinkFile = "";
				$renameFile = md5(date("dmYHis").$reqLinkFilePPH['name'][$i].$this->ID).".".getExtension($reqLinkFilePPH['name'][$i]);
				if($file->uploadToDirArray('reqLinkFilePPH', $FILE_DIR_KUALIFIKASI, $renameFile, $i))
				{
					$insertLinkFile =  $renameFile;
					$insertLinkFileNama =  $reqLinkFilePPH['name'][$i];
				}
				else
				{
					$insertLinkFile =  $reqLinkFilePPHTemp[$i];
					$insertLinkFileNama =  $reqLinkFilePPHTempNama[$i];
				}
				unset($file);
				
				$pph_loop->setField("PATH_FILE", $insertLinkFile);
				$pph_loop->setField("NAMA_FILE", $insertLinkFileNama);
				
				$ppn_save = new RekananPajak();
				$allRecord = $ppn_save->getCountByParams(array("TIPE"=>2, "BULAN"=>$reqBulan, "REKANAN_ID"=>$this->ID, "TAHUN"=>$reqTahun));
				$ppn_save->selectByParams(array("TIPE"=>2, "BULAN"=>$reqBulan, "REKANAN_ID"=>$this->ID, "TAHUN"=>$reqTahun));
				$ppn_save->firstRow();
				
				$tmp = $ppn_save->getField("REKANAN_PAJAK_ID");
				unset($ppn_save);
				
				$pph_loop->setField('REKANAN_PAJAK_ID', $tmp);
				
				if($allRecord > 0)
					$pph_loop->update();
				else
					$pph_loop->insert();

				unset($pph_loop);

			}
			$i++;
		}
		
		$i=0;
		while($i <= 2)
		{
			if($reqNomorPPN[$i] == "")
			{}
			else
			{			
				$FILE_DIR_KUALIFIKASI = "uploads/ppn_pph/";
				$ppn_loop = new RekananPajak();
				
				$reqBulan = (int)substr($reqBulanPPN[$i], 0, 2);
				$reqTahun = substr($reqBulanPPN[$i], 2, 4);

				$ppn_loop->setField('NOMOR', $reqNomorPPN[$i]);
				$ppn_loop->setField('TANGGAL', dateToDBCheck($reqTanggalPPN[$i]));
				$ppn_loop->setField('BULAN', $reqBulan);
				$ppn_loop->setField('TIPE', '3');
				$ppn_loop->setField('REKANAN_ID', $this->ID);
				$ppn_loop->setField('TAHUN', $reqTahun);
				
				$file = new FileHandler();
				
				$insertLinkFile = "";
				$renameFile = md5(date("dmYHis").$reqLinkFilePPN['name'][$i].$this->ID).".".getExtension($reqLinkFilePPN['name'][$i]);
				if($file->uploadToDirArray('reqLinkFilePPN', $FILE_DIR_KUALIFIKASI, $renameFile, $i))
				{
					$insertLinkFile =  $renameFile;
					$insertLinkFileNama =  $reqLinkFilePPN['name'][$i];
				}
				else
				{
					$insertLinkFile =  $reqLinkFilePPNTemp[$i];
					$insertLinkFileNama =  $reqLinkFilePPNTempNama[$i];
				}
				
				unset($file);
				
				$ppn_loop->setField("PATH_FILE", $insertLinkFile);
				$ppn_loop->setField("NAMA_FILE", $insertLinkFileNama);
				
							
				$ppn_save = new RekananPajak();
				$allRecord = $ppn_save->getCountByParams(array("TIPE"=>3, "BULAN"=>$reqBulan, "REKANAN_ID"=>$this->ID, "TAHUN"=>$reqTahun));
				$ppn_save->selectByParams(array("TIPE"=>3, "BULAN"=>$reqBulan, "REKANAN_ID"=>$this->ID, "TAHUN"=>$reqTahun));
				$ppn_save->firstRow();
				$tmp = $ppn_save->getField("REKANAN_PAJAK_ID");
				unset($ppn_save);
				
				$ppn_loop->setField('REKANAN_PAJAK_ID', $tmp);
				
				if($allRecord > 0)
					$ppn_loop->update();
				else
					$ppn_loop->insert();
					
				unset($ppn_loop);
				
			}
			$i++;
		}	
		
		$rekanan_pajak_validasi = new RekananPajak();
		
		if($reqTipe == "2")
			$jumlahData = $rekanan_pajak_validasi->getCountByParams(array("TIPE"=>2, "REKANAN_ID"=>$this->ID), " AND NOMOR IS NOT NULL AND BULAN || TAHUN IN (".$reqSyaratPPH.") ");
		else
			$jumlahData = $rekanan_pajak_validasi->getCountByParams(array("TIPE"=>3, "REKANAN_ID"=>$this->ID), " AND NOMOR IS NOT NULL AND BULAN || TAHUN IN (".$reqSyaratPPN.") ");
		
		if($jumlahData == 3)
			echo "1";
		else
			echo "0";
		
	}		
	
	function get_PPH()
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("RekananPajak");
		$rekanan_pph	= new RekananPajak(); // tipe 2
		
		$reqId = httpFilterGet("reqId");
		$reqTahun = httpFilterGet("reqTahun");
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		
		$rekanan_pph->selectByParams(array("TIPE"=>2, "REKANAN_ID"=>$reqId, "TAHUN" => $reqTahun), -1, -1);
		$met = array();
		$i=0;
		
		$arrBulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
		 
		while($rekanan_pph->nextRow()){
			$met[$i]['BULAN'] = $arrBulan[$rekanan_pph->getField('BULAN')];
			$met[$i]['NOMOR'] = $rekanan_pph->getField('NOMOR');
			$met[$i]['TANGGAL'] = getFormattedDate($rekanan_pph->getField('TANGGAL'));
			$i++;
		}
		echo json_encode($met);
	}
	
	function get_PPN()
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("RekananPajak");
		$rekanan_ppn	= new RekananPajak(); // tipe 3
		
		$reqId = httpFilterGet("reqId");
		$reqTahun = httpFilterGet("reqTahun");
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		
		$rekanan_ppn->selectByParams(array("TIPE"=>3, "REKANAN_ID"=>$reqId, "TAHUN" => $reqTahun), -1, -1);
		$met = array();
		$i=0;
		
		$arrBulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
		 
		while($rekanan_ppn->nextRow()){
			$met[$i]['BULAN'] = $arrBulan[$rekanan_ppn->getField('BULAN')];
			$met[$i]['NOMOR'] = $rekanan_ppn->getField('NOMOR');
			$met[$i]['TANGGAL'] = getFormattedDate($rekanan_ppn->getField('TANGGAL'));
			$i++;
		}
		echo json_encode($met);
	}
	
	function reload_SPT()
	{
		$reqPaketId = $this->input->get("reqPaketId");

		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("RekananPajak");

		$paketInfo->getPaket($reqPaketId);		
		
		$rekanan_spt = new RekananPajak();
		$rekanan_spt->selectByParams(array("REKANAN_ID"=>$userLogin->userRekanan, 'TAHUN'=>$paketInfo->syarat_keuangan_spt_tahun, "TIPE"=>1), -1, -1, "", "");
		$rekanan_spt->firstRow();

		$i = 0;
		
		$met[$i]['TAHUN'] = $rekanan_spt->getField("TAHUN");
		$met[$i]['TANGGAL'] = dateToPage($rekanan_spt->getField("TANGGAL"));
		$met[$i]['NOMOR'] = $rekanan_spt->getField("NOMOR");
		
		echo json_encode($met);	
	}
	
	function reload_pajak()
	{
		$reqPaketId = $this->input->get("reqPaketId");
		$reqTipe = $this->input->get("reqTipe");

		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("RekananPajak");

		$paketInfo->getPaket($reqPaketId);		

		
		if($reqTipe == 2)
			$arrSyaratBulan = explode(", ",$paketInfo->syarat_keuangan_bulan_pph);
		else
			$arrSyaratBulan = explode(", ",$paketInfo->syarat_keuangan_bulan_ppn);		
			
		$rekanan_pajak = new RekananPajak();
		$rekanan_pajak->selectByParams(array("REKANAN_ID"=>$userLogin->userRekanan, "TIPE" => $reqTipe),-1,-1, " AND BULAN || TAHUN IN (".getValueArrayMonth($arrSyaratBulan).") ");
		$i = 0;

		while($rekanan_pajak->nextRow())
		{
			$met[$i]['PERIODE'] = getNamePeriode($rekanan_pajak->getField("PERIODE"));
			$met[$i]['NOMOR']   = $rekanan_pajak->getField("NOMOR");
			$met[$i]['TANGGAL'] = dateToPage($rekanan_pajak->getField("TANGGAL"));
			$i++;
		}	
		
		echo json_encode($met);		
	}
	
	function data_administrasi_keuangan_spt_ubah()
	{
		/* INCLUDE FILE */
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("Rekanan");
		$this->load->model("RekananPajak");
		$this->load->library("FileHandler");
		$file = new FileHandler();
		
		/* create objects */
		$rekanan = new Rekanan();
		$rekanan_spt	= new RekananPajak(); // tipe 1
		
		$reqId = $this->ID;
		$reqTahun= $this->input->post("reqTahun");
		$reqTahunPajak_last = $this->input->post("reqTahunPajak_last");
		$reqNomor= $this->input->post("reqNomor");
		$reqTanggal= $this->input->post("reqTanggal");
		$reqRekananPajakId= $this->input->post("reqRekananPajakId");
		$reqSubmit= $this->input->post("reqSubmit");
		$reqMode= $this->input->post("reqMode");
		$reqLinkFileTempNama 	= $_POST["reqLinkFileTempNama"];
		$reqLinkFileTemp 		= $_POST["reqLinkFileTemp"];
		$reqLinkFileTempTipe 	= $_POST["reqLinkFileTempTipe"];
		$reqLinkFileTempUkuran 	= $_POST["reqLinkFileTempUkuran"];
		$reqLinkFileTempNama 	= $_POST["reqLinkFileTempNama"];
		$reqLinkFile = $_FILES['reqLinkFile'];
		$FILE_DIR = "uploads/spt/";
		
		//$rekanan_tahun = new RekananPajak();
		//if($reqRekananPajakId)
			//$record = $rekanan_tahun->getCountByParams(array("REKANAN_ID"=>$this->ID, "TIPE"=>1, "TAHUN"=>$reqTahun, 'NOT TAHUN' => $reqTahunPajak_last));
		//else
			//$record = $rekanan_tahun->getCountByParams(array("REKANAN_ID"=>$this->ID, "TIPE"=>1, "TAHUN"=>$reqTahun));
		
		//echo '--'.$record.'--';
		
		if($reqMode=='insert')
		{
			$rekanan_spt->setField('TAHUN', $reqTahun);
			$rekanan_spt->setField('BULAN', '0');
			$rekanan_spt->setField('NOMOR', $reqNomor);
			$rekanan_spt->setField('TANGGAL', dateToDBCheck($reqTanggal));
			$rekanan_spt->setField('REKANAN_ID', $this->ID);
			$rekanan_spt->setField('TIPE', '1');
			$rekanan_spt->setField('CREATED_BY', $this->USER_LOGIN_ID);

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
			$rekanan_spt->setField("PATH_FILE", $insertLinkFile);
			$rekanan_spt->setField("NAMA_FILE", $insertLinkFileNama);

			if($rekanan_spt->insert())
			{
				echo "Data Berhasil di Simpan";
			}
			else
			{
				echo "Data Gagal Tersimpan";
			}
		}
		else
		{
			$rekanan_spt->setField('TAHUN', $reqTahun);
			$rekanan_spt->setField('BULAN', '0');
			$rekanan_spt->setField('NOMOR', $reqNomor);
			$rekanan_spt->setField('REKANAN_PAJAK_ID', $reqRekananPajakId);
			$rekanan_spt->setField('TANGGAL', dateToDBCheck($reqTanggal));
			$rekanan_spt->setField('REKANAN_ID', $this->ID);
			$rekanan_spt->setField('TIPE', '1');
			$rekanan_spt->setField('CREATED_BY', $this->USER_LOGIN_ID);

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
			$rekanan_spt->setField("PATH_FILE", $insertLinkFile);
			$rekanan_spt->setField("NAMA_FILE", $insertLinkFileNama);

			if($rekanan_spt->update())
			{
				echo "Data Berhasil di Update";
			}
			else
			{
				echo "Data Gagal Tersimpan";
			}
		 }
	  }

	function data_administrasi_keuangan_spt_syarat()
	{
		/* INCLUDE FILE */
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("Rekanan");
		$this->load->model("RekananPajak");
		
		/* create objects */
		$rekanan = new Rekanan();
		$rekanan_spt	= new RekananPajak(); 
		
		$reqId = $this->ID;
		$reqTahun= $this->input->post("reqTahun");
		$reqTahunPajak_last = $this->input->post("reqTahunPajak_last");
		$reqNomor= $this->input->post("reqNomor");
		$reqTanggal= $this->input->post("reqTanggal");
		$reqRekananPajakId= $this->input->post("reqRekananPajakId");
		$reqSubmit= $this->input->post("reqSubmit");
		$reqMode= $this->input->post("reqMode");
		$reqTahunSPT = $this->input->post("reqTahunSPT");
		
		if($reqMode=='insert')
		{
			$rekanan_spt->setField('TAHUN', $reqTahun);
			$rekanan_spt->setField('BULAN', '0');
			$rekanan_spt->setField('NOMOR', $reqNomor);
			$rekanan_spt->setField('TANGGAL', dateToDBCheck($reqTanggal));
			$rekanan_spt->setField('REKANAN_ID', $this->ID);
			$rekanan_spt->setField('TIPE', '1');
			if($rekanan_spt->insert())
			{
				if($reqTahunSPT == $reqTahun)
					echo "1";
				else
					echo "0";				
			}
			else
			{
				echo "Data Gagal di Simpan";
			}
		}
		else
		{
			$rekanan_spt->setField('TAHUN', $reqTahun);
			$rekanan_spt->setField('BULAN', '0');
			$rekanan_spt->setField('NOMOR', $reqNomor);
			$rekanan_spt->setField('REKANAN_PAJAK_ID', $reqRekananPajakId);
			$rekanan_spt->setField('TANGGAL', dateToDBCheck($reqTanggal));
			$rekanan_spt->setField('REKANAN_ID', $this->ID);
			$rekanan_spt->setField('TIPE', '1');
			if($rekanan_spt->update())
			{
				if($reqTahunSPT == $reqTahun)
					echo "1";
				else
					echo "0";			
			}
			else
			{
				echo "Data Gagal di Update";
			}
		 }
	  }
	  	  
	  function delete_spt()
	  {
		  $this->load->model("RekananPajak");
		  $rekanan_spt	= new RekananPajak();
		  
		  $reqId= $this->input->get("reqId");
		  	
		  $rekanan_spt->setField('REKANAN_PAJAK_ID', $reqId);
		  $rekanan_spt->setField("REKANAN_ID", $this->ID);
			
		  if($rekanan_spt->delete())
		  {
			echo "Data berhasil didelete";
		  }
		  else
		  {
			echo "Data gagal didelete";
		  }
	  }
	  
  	
}
?>
