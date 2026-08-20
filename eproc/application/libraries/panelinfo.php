<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
 
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Kauth
 *
 * @author user
 */
  class panelinfo{
	var $id;
	var $nama;
	var $kualifikasi;
	var $kualifikasi_id;
	var $tanggal;
	var $passing_grade;	

	var $syarat_teknis_tenaga_ahli;
	var $syarat_teknis_peralatan;
	var $syarat_teknis_sertifikat;
	var $syarat_rekening_koran;
	var $syarat_rekening_koran_bulan;
	var $syarat_keuangan_spt;
	var $syarat_keuangan_info_spt;
	var $syarat_keuangan_ppn;
	var $syarat_keuangan_pph;
	var $syarat_keuangan_pkp;
	var $syarat_neraca;
	var $syarat_sbu;	
	var $syarat_admin_klasifikasi;
	var $syarat_keuangan_bulan_ppn;
	var $syarat_keuangan_bulan_pph;	
	var $syarat_ijin_siujk;
	var $syarat_ijin_siui;
	var $syarat_ijin_lain;
	var $syarat_adm_kualifikasi_info;
	var $syarat_pengalaman_tahun;
	var $tanggal_tahap;
	var $user_login_id;
	var $unit_kerja;
	
    /******************** CONSTRUCTOR **************************************/
    function panelinfo(){
	
		 $this->emptyProps();
    }

    /******************** METHODS ************************************/
    /** Empty the properties **/
    function emptyProps(){
		$this->id = "";
		$this->nama = "";
		$this->kualifikasi = "";
		$this->kualifikasi_id = "";
		$this->tanggal = "";	
		$this->passing_grade = "";				
		
		$this->syarat_teknis_tenaga_ahli = "";
		$this->syarat_teknis_peralatan = "";
		$this->syarat_teknis_sertifikat = "";
		$this->syarat_rekening_koran = "";
		$this->syarat_rekening_koran_bulan = "";
		$this->syarat_keuangan_spt = "";
		$this->syarat_keuangan_info_spt = "";
		$this->syarat_keuangan_ppn = "";
		$this->syarat_keuangan_pph = "";
		$this->syarat_keuangan_pkp = "";
		$this->syarat_neraca = "";
		$this->syarat_sbu = "";
		$this->syarat_admin_klasifikasi = "";
		
		$this->syarat_keuangan_bulan_ppn= "";
		$this->syarat_keuangan_bulan_pph= "";
		
		$this->syarat_ijin_siujk= "";
		$this->syarat_ijin_siui= "";
		$this->syarat_ijin_lain= "";
		$this->syarat_adm_kualifikasi_info = "";
		$this->tanggal_tahap = "";
		$this->user_login_id = "";
		$this->unit_kerja = "";
		$this->syarat_pengalaman_tahun = "";
		
    }
		
    
    /** Verify user login. True when login is valid**/
    function getPanel($panel_id){			
		$usr = new Panel();
		$usr->selectById($panel_id);
		if ($usr->firstRow()) {
			          
			$this->id = $usr->getField("PANEL_ID");
			$this->nama = $usr->getField("NAMA");
			$this->kualifikasi = $usr->getField("REKANAN_KUALIFIKASI");
			$this->kualifikasi_id = $usr->getField("REKANAN_KUALIFIKASI_ID");
			$this->tanggal = $usr->getField("TANGGAL");	
			$this->passing_grade = $usr->getField("PASS_GRADE");	
			
			$this->syarat_teknis_tenaga_ahli = $usr->getField("SYARAT_TEKNIS_TENAGA_AHLI");
			$this->syarat_teknis_peralatan = $usr->getField("SYARAT_TEKNIS_PERALATAN");
			$this->syarat_teknis_sertifikat = $usr->getField("SYARAT_TEKNIS_SERTIFIKAT");
			$this->syarat_rekening_koran = $usr->getField("SYARAT_REKENING_KORAN");
			$this->syarat_rekening_koran_bulan = $usr->getField("SYARAT_REKENING_KORAN_BULAN");
			$this->syarat_keuangan_spt = $usr->getField("SYARAT_KEUANGAN_SPT");
			$this->syarat_keuangan_ppn = $usr->getField("SYARAT_KEUANGAN_PPN");
			$this->syarat_keuangan_pph = $usr->getField("SYARAT_KEUANGAN_PPH");
			$this->syarat_keuangan_pkp = $usr->getField("SYARAT_KEUANGAN_PKP");
			$this->syarat_neraca = $usr->getField("SYARAT_NERACA");
			$this->syarat_sbu = $usr->getField("SYARAT_SBU");						
			$this->syarat_admin_klasifikasi = $usr->getField("SYARAT_ADM_KUALIFIKASI");			
			$this->syarat_keuangan_bulan_pph= $usr->getField("SYARAT_KEUANGAN_PPH_BULAN");
			$this->syarat_keuangan_bulan_ppn= $usr->getField("SYARAT_KEUANGAN_PPN_BULAN");			
			$this->syarat_keuangan_info_spt= $usr->getField("SYARAT_TEKNIS_SERTIFIKAT_INFO");			
			$this->syarat_ijin_siujk= $usr->getField("SYARAT_IJIN_SIUJK");
			$this->syarat_ijin_siui= $usr->getField("SYARAT_IJIN_SIUI");
			$this->syarat_ijin_lain= $usr->getField("SYARAT_IJIN_LAIN");
			$this->syarat_adm_kualifikasi_info= $usr->getField("SYARAT_ADM_KUALIFIKASI_INFO");
			$this->tanggal_tahap = $usr->getField("TANGGAL_TAHAP");
			$this->unit_kerja = $usr->getField("UNIT_KERJA");
			$this->user_login_id = $usr->getField("USER_LOGIN_ID");
			$this->syarat_pengalaman_tahun = $usr->getField("SYARAT_PENGALAMAN_TAHUN");
			
		}
		
		$this->query = $usr->query;
		
		unset($usr);
    }


    /** Verify user login. True when login is valid**/
    function getPanelEncrypt($panel_id, $rekanan_id){			
		$usr = new Panel();
		$usr->selectByIdEncrypt($panel_id, $rekanan_id);
		if ($usr->firstRow()) {
			          
			$this->id = $usr->getField("PANEL_ID");
			$this->nama = $usr->getField("NAMA");
			$this->kualifikasi = $usr->getField("REKANAN_KUALIFIKASI");
			$this->kualifikasi_id = $usr->getField("REKANAN_KUALIFIKASI_ID");
			$this->tanggal = $usr->getField("TANGGAL");	
			$this->passing_grade = $usr->getField("PASS_GRADE");	
			
			$this->syarat_teknis_tenaga_ahli = $usr->getField("SYARAT_TEKNIS_TENAGA_AHLI");
			$this->syarat_teknis_peralatan = $usr->getField("SYARAT_TEKNIS_PERALATAN");
			$this->syarat_teknis_sertifikat = $usr->getField("SYARAT_TEKNIS_SERTIFIKAT");
			$this->syarat_rekening_koran = $usr->getField("SYARAT_REKENING_KORAN");
			$this->syarat_rekening_koran_bulan = $usr->getField("SYARAT_REKENING_KORAN_BULAN");
			$this->syarat_keuangan_spt = $usr->getField("SYARAT_KEUANGAN_SPT");
			$this->syarat_keuangan_ppn = $usr->getField("SYARAT_KEUANGAN_PPN");
			$this->syarat_keuangan_pph = $usr->getField("SYARAT_KEUANGAN_PPH");
			$this->syarat_keuangan_pkp = $usr->getField("SYARAT_KEUANGAN_PKP");
			$this->syarat_neraca = $usr->getField("SYARAT_NERACA");
			$this->syarat_sbu = $usr->getField("SYARAT_SBU");						
			$this->syarat_admin_klasifikasi = $usr->getField("SYARAT_ADM_KUALIFIKASI");			
			$this->syarat_keuangan_bulan_pph= $usr->getField("SYARAT_KEUANGAN_PPH_BULAN");
			$this->syarat_keuangan_bulan_ppn= $usr->getField("SYARAT_KEUANGAN_PPN_BULAN");			
			$this->syarat_keuangan_info_spt= $usr->getField("SYARAT_TEKNIS_SERTIFIKAT_INFO");			
			$this->syarat_ijin_siujk= $usr->getField("SYARAT_IJIN_SIUJK");
			$this->syarat_ijin_siui= $usr->getField("SYARAT_IJIN_SIUI");
			$this->syarat_ijin_lain= $usr->getField("SYARAT_IJIN_LAIN");
			$this->syarat_adm_kualifikasi_info= $usr->getField("SYARAT_ADM_KUALIFIKASI_INFO");
			$this->tanggal_tahap = $usr->getField("TANGGAL_TAHAP");
			$this->unit_kerja = $usr->getField("UNIT_KERJA");
			$this->user_login_id = $usr->getField("USER_LOGIN_ID");
			$this->syarat_pengalaman_tahun = $usr->getField("SYARAT_PENGALAMAN_TAHUN");
			
		}
		
		$this->query = $usr->query;
		
		unset($usr);
    }
			   
}
	
  /***** INSTANTIATE THE GLOBAL OBJECT */
  $panelInfo = new PanelInfo();

?>