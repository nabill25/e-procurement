<?php

include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

class syarat_daftar_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'"); 
		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;   
		
		$this->USER_LOGIN_ID =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
		$this->USER_LOGIN =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN;
		$this->USER_NAMA =  $this->kauth->getInstance()->getIdentity()->USER_NAMA;
		$this->USER_TYPE_ID =  $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID;
		$this->REKANAN_ID =  $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
		$this->UNIT_KERJA_ID =  $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID;
		$this->NIP =  $this->kauth->getInstance()->getIdentity()->NIP;
		$this->LOGIN_TIME = $this->kauth->getInstance()->getIdentity()->LOGIN_TIME;
		$this->LOGIN_DATE = $this->kauth->getInstance()->getIdentity()->LOGIN_DATE;
		$this->REKANAN = $this->kauth->getInstance()->getIdentity()->NAMA;
		$this->REKANAN_KODE = $this->kauth->getInstance()->getIdentity()->KODE;
		$this->REKANAN_PKP = $this->kauth->getInstance()->getIdentity()->PKP;
		$this->REKANAN_NPWP = $this->kauth->getInstance()->getIdentity()->NPWP;
		$this->REKANAN_STATUS_PERUSAHAAN = $this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN;
		$this->REKANAN_STATUS_VALIDASI = $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI;
				
	}
	
	function reloadAkta()
	{

		$this->load->model("RekananAkta");
		$rekanan_akta = new RekananAkta();
		// set syarat untuk landasan hukum
		$rekanan_akta->selectByParams(array("REKANAN_ID"=>$this->ID, "AKTA_TYPE_ID"=>1),-1,-1);
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
	
	function reloadIjinUsaha()
	{

		$reqIjin = $this->input->get("reqIjin");
		
		$this->load->model("RekananIjinUsaha");
		$rekanan_ijin_usaha = new RekananIjinUsaha();
		$rekanan_ijin_usaha->selectByParams(array("REKANAN_ID" => $this->ID, "IJIN_USAHA_ID"=>$reqIjin));
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

	function reloadKualifikasi()
	{

		$this->load->model("Rekanan");
		$rekanan = new Rekanan();
		$rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
		$rekanan->firstRow();
		
		$tempKualifikasiNama = $rekanan->getField("REKANAN_KUALIFIKASI");
		
		$i = 0;
		$met[$i]['KUALIFIKASI'] = $tempKualifikasiNama;
		echo json_encode($met);			
		
	}
	
	function reloadRekeningKoran()
	{
		
		$reqPaketId = $this->input->get("reqPaketId");

		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("RekananRekeningKoran");


		$paketInfo->getPaket($reqPaketId);		
		$arrSyaratBulanRekeningKoran = explode(", ",$paketInfo->syarat_rekening_koran_bulan);

		$rekanan_rekening_koran = new RekananRekeningKoran();
		$rekanan_rekening_koran->selectByParams(array("K.REKANAN_ID" => $this->ID),-1,-1, " AND CONCAT(BULAN,TAHUN) IN (".getValueArrayMonth($arrSyaratBulanRekeningKoran).") ");
		$i = 0;
		while($rekanan_rekening_koran->nextRow())
		{
		  $met[$i]['PERIODE'] = getNamePeriode($rekanan_rekening_koran->getField("PERIODE"));
		  $met[$i]['NAMA'] = $rekanan_rekening_koran->getField("NAMA");
		  $met[$i]['NILAI'] = numberToIna($rekanan_rekening_koran->getField("NILAI"));
		  $i++;
		}		
		echo json_encode($met);		
			
	}

	function reloadSPT()
	{
		
		$reqPaketId = $this->input->get("reqPaketId");

		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("RekananPajak");

		$paketInfo->getPaket($reqPaketId);		
		
		$rekanan_spt = new RekananPajak();
		$rekanan_spt->selectByParams(array("REKANAN_ID"=>$this->ID, 'TAHUN'=>$paketInfo->syarat_keuangan_spt_tahun, "TIPE"=>1), -1, -1, "", "");
		$rekanan_spt->firstRow();

		$i = 0;
		
		$met[$i]['TAHUN'] = $rekanan_spt->getField("TAHUN");
		$met[$i]['TANGGAL'] = dateToPage($rekanan_spt->getField("TANGGAL"));
		$met[$i]['NOMOR'] = $rekanan_spt->getField("NOMOR");
		
		echo json_encode($met);		
			
	}	

	function reloadNeraca()
	{
		
		$reqPaketId = $this->input->get("reqPaketId");

		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("RekananNeraca");

		$paketInfo->getPaket($reqPaketId);		
		
		$rekanan_neraca = new RekananNeraca();
		$rekanan_neraca->selectByParams(array("REKANAN_ID" => $this->ID),-1,-1, " AND TAHUN IN (".str_replace("/", ",",$paketInfo->syarat_neraca_tahun).") ");

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
	
	function reloadPajak()
	{
		
		$reqPaketId = $this->input->get("reqPaketId");
		$reqTipe = $this->input->get("reqTipe");

		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("RekananPajak");

		$paketInfo->getPaket($reqPaketId);		

		
		if($reqTipe == 2)
			$arrSyaratBulan = explode(", ",$paketInfo->syarat_keuangan_bulan_pph);
		else
			$arrSyaratBulan = explode(", ",$paketInfo->syarat_keuangan_bulan_ppn);		
			
		$rekanan_pajak = new RekananPajak();
		$rekanan_pajak->selectByParams(array("REKANAN_ID"=>$this->ID, "TIPE" => $reqTipe),-1,-1, " AND BULAN || TAHUN IN (".getValueArrayMonth($arrSyaratBulan).") ");
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

	function reloadPKP()
	{

		$this->load->model("Rekanan");
		$rekanan = new Rekanan();
		$rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
		$rekanan->firstRow();

		$tempNoSurat_PKP = $rekanan->getField("PKP");
		$tempTanggal_PKP = dateToPageCheck($rekanan->getField("PKP_TANGGAL"));
		$tempJabatan_PKP = $rekanan->getField("NPWP");
		
		$i = 0;
		$met[$i]['PKP'] = $tempNoSurat_PKP;
		$met[$i]['PKP_TANGGAL'] = $tempTanggal_PKP;
		$met[$i]['NPWP'] = $tempJabatan_PKP;
		echo json_encode($met);			
		
	}
				
	
}
?>
