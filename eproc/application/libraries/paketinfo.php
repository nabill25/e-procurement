<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
 
require_once 'kloader.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Kauth
 *
 * @author user
 */
class paketinfo{
	var $id;
	var $uuid;
	var $nama;
	var $metode_lelang_id;
	var $metode_lelang_nama;
	var $metode_kualifikasi;
	var $metode_kualifikasi_id;
	var $metode_evaluasi;
	var $metode_evaluasi_id;
	var $jenis;
	var $jenis_id;
	var $kualifikasi;
	var $kualifikasi_id;
	var $nilai;
	var $nilai_owner_estimate;
	var $penawaran_harga_maksimal;
	var $tanggal;
	// var $passing_grade;	
	var $lokasi;
	var $permohonan_paket_id;
	
	var $syarat_teknis_tenaga_ahli;
	var $syarat_teknis_peralatan;
	var $syarat_teknis_sertifikat;
	var $syarat_rekening_koran;
	var $syarat_rekening_koran_bulan;
	var $syarat_keuangan_spt_tahun;
	var $syarat_keuangan_spt;
	var $syarat_keuangan_info_spt;
	var $syarat_keuangan_ppn;
	var $syarat_keuangan_pph;
	var $syarat_keuangan_pkp;
	var $syarat_neraca;
	var $syarat_neraca_tahun;
	var $syarat_sbu;
	
	var $syarat_admin_klasifikasi;
	
	var $syarat_keuangan_bulan_ppn;
	var $syarat_keuangan_bulan_pph;
	
	var $syarat_ijin_siujk;
	var $syarat_ijin_siui;
	var $syarat_ijin_lain;
	var $syarat_ijin_siup;
	var $syarat_adm_kualifikasi_info;
	var $tanggal_tahap;
	var $rekanan_id_pemenang;
	var $nilai_negosiasi;
	var $tanggal_pengumuman_pemenang;
	var $rekanan_id_penilaian;
	var $user_login_id;
	var $user_login;
	var $unit_kerja;
	var $unit_kerja_id;
	var $publish_paket;
	var $reschedule_ke; 
	var $reschedule_1; 
	var $reschedule_2; 
	var $reschedule_3; 
	var $reschedule_4; 
	var $reschedule_5; 
	var $reschedule_6; 
	var $reschedule_7; 
	var $reschedule_8; 
	var $reschedule_9; 
	var $reschedule_10; 
	var $publish_paket_tanggal;
	var $publish_ba_penawaran;
	var $publish_ba_negosiasi;
	var $publish_ba_kualifikasi;
	var $publish_ba_penawaran_tanggal;
	var $pr_group_number;
	var $jenis_pengadaan;
	var $mata_uang;
	var $sistem_sampul;
	var $publish_ba_sampul1;
	var $publish_ba_sampul2;
	var $publish_ba_penawaran_sampul2;
	var $bahasa;
	var $bidding_menit;
	var $bidding_menit_tambahan;
	var $bidding_mulai;
	var $bidding;
	var $bobot_teknis;
	var $bobot_harga;
	var $passing_grade;
	var $multi_pemenang;
	var $nip;
	var $publish_eval_kualifikasi;
	var $kode_pr;
	var $kode_rup;
	var $multi_bidang_usaha;
	
    /******************** CONSTRUCTOR **************************************/

    function __construct(){
	  // parent::__construct();
		$this->emptyProps();
	}

   //  function paketinfo(){
	
		 // $this->emptyProps();
   //  }

    /******************** METHODS ************************************/
    /** Empty the properties **/
    function emptyProps(){
		$this->id = "";
		$this->uuid = "";
		$this->nama = "";
		$this->metode_lelang_id = "";
		$this->metode_lelang_nama = "";
		$this->metode_kualifikasi = "";
		$this->metode_kualifikasi_id = "";
		$this->metode_evaluasi = "";
		$this->metode_evaluasi_id = "";
		$this->jenis = "";
		$this->jenis_id = "";
		$this->kualifikasi = "";
		$this->kualifikasi_id = "";
		$this->nilai = "";			
		$this->nilai_owner_estimate = "";					
		$this->penawaran_harga_maksimal = "";					
		$this->tanggal = "";	
		$this->passing_grade = "";				
		$this->lokasi = "";
		$this->permohonan_paket_id = "";
		
		$this->syarat_teknis_tenaga_ahli = "";
		$this->syarat_teknis_peralatan = "";
		$this->syarat_teknis_sertifikat = "";
		$this->syarat_rekening_koran = "";
		$this->syarat_rekening_koran_bulan = "";
		$this->syarat_keuangan_spt = "";
		$this->syarat_keuangan_spt_tahun = "";
		
		$this->syarat_keuangan_info_spt = "";
		$this->syarat_keuangan_ppn = "";
		$this->syarat_keuangan_pph = "";
		$this->syarat_keuangan_pkp = "";
		$this->syarat_neraca = "";
		$this->syarat_neraca_tahun = "";		
		$this->syarat_sbu = "";
		$this->syarat_admin_klasifikasi = "";
		
		$this->syarat_keuangan_bulan_ppn= "";
		$this->syarat_keuangan_bulan_pph= "";
		
		$this->syarat_ijin_siujk= "";
		$this->syarat_ijin_siui= "";
		$this->syarat_ijin_lain= "";
		$this->syarat_ijin_siup= "";
		$this->syarat_adm_kualifikasi_info = "";
		$this->tanggal_tahap = "";
		$this->rekanan_id_pemenang = "";
		$this->nilai_negosiasi = "";
		$this->tanggal_pengumuman_pemenang = "";
		$this->rekanan_id_penilaian = "";
		$this->user_login_id = "";
		$this->user_login = "";
		$this->unit_kerja = "";
		$this->unit_kerja_id = "";
		$this->publish_paket = "";
		$this->reschedule_ke = ""; 
		$this->reschedule_1 = ""; 
		$this->reschedule_2 = ""; 
		$this->reschedule_3 = ""; 
		$this->reschedule_4 = ""; 
		$this->reschedule_5 = ""; 
		$this->reschedule_6 = ""; 
		$this->reschedule_7 = ""; 
		$this->reschedule_8 = ""; 
		$this->reschedule_9 = ""; 
		$this->reschedule_10 = ""; 
		$this->publish_paket_tanggal = "";
		$this->publish_ba_penawaran = "";
		$this->publish_ba_negosiasi = "";
		$this->publish_ba_kualifikasi = "";
		$this->publish_ba_penawaran_tanggal = "";
		$this->pr_group_number = "";
		$this->jenis_pengadaan = "";
		$this->mata_uang = "";
		$this->sistem_sampul = "";
		$this->publish_ba_sampul1 = "";
		$this->publish_ba_sampul2 = "";
		$this->publish_ba_penawaran_sampul2 = "";
		$this->bahasa = "";
		$this->bidding_menit = "";
		$this->bidding_menit_tambahan = "";
		$this->bidding_mulai = "";
		$this->bidding = "";
		$this->bobot_teknis = "";
		$this->bobot_harga = "";
		$this->passing_grade = "";
		$this->multi_pemenang = "";
		$this->nip = "";
		$this->publish_eval_kualifikasi = "";
		$this->kode_pr = "";
		$this->kode_rup = "";
		$this->alasan_batal = "";
		$this->multi_bidang_usaha = "";
		
    }
		
    
    /** Verify user login. True when login is valid**/
    function getPaket($paket_id){			
		$CI =& get_instance();
		$CI->load->model("Paket");	
		
		$usr = new Paket();
		$usr->selectById($paket_id);
		
		if ($usr->firstRow()) {
			          
			$this->id = $usr->getField("PAKET_ID");
			$this->uuid = $usr->getField("PAKET_UUID");
			$this->nama = $usr->getField("NAMA");
			$this->metode_lelang_id = $usr->getField("PAKET_METODE_LELANG_ID");
			$this->metode_lelang_nama = $usr->getField("PAKET_METODE_LELANG");
			$this->metode_kualifikasi = $usr->getField("PAKET_METODE_KUALIFIKASI");
			$this->metode_kualifikasi_id = $usr->getField("PAKET_METODE_KUALIFIKASI_ID");
			$this->metode_evaluasi = $usr->getField("PAKET_METODE_EVALUASI");
			$this->metode_evaluasi_id = $usr->getField("PAKET_METODE_EVALUASI_ID");
			$this->jenis = $usr->getField("PAKET_JENIS");
			$this->jenis_id = $usr->getField("PAKET_JENIS_ID");
			$this->kualifikasi = $usr->getField("REKANAN_KUALIFIKASI");
			$this->kualifikasi_id = $usr->getField("REKANAN_KUALIFIKASI_ID");
			$this->nilai = $usr->getField("NILAI");	
			$this->nilai_owner_estimate = $usr->getField("NILAI_OWNER_ESTIMATE");	
			$this->penawaran_harga_maksimal = $usr->getField("PENAWARAN_HARGA_MAKSIMAL");	
			$this->tanggal = $usr->getField("TANGGAL");	
			$this->passing_grade = $usr->getField("PASS_GRADE");	
			$this->lokasi = $usr->getField("LOKASI");
			$this->permohonan_paket_id = $usr->getField("PERMOHONAN_PAKET_ID");
			
			$this->syarat_teknis_tenaga_ahli = $usr->getField("SYARAT_TEKNIS_TENAGA_AHLI");
			$this->syarat_teknis_peralatan = $usr->getField("SYARAT_TEKNIS_PERALATAN");
			$this->syarat_teknis_sertifikat = $usr->getField("SYARAT_TEKNIS_SERTIFIKAT");
			$this->syarat_rekening_koran = $usr->getField("SYARAT_REKENING_KORAN");
			$this->syarat_rekening_koran_bulan = $usr->getField("SYARAT_REKENING_KORAN_BULAN");
			$this->syarat_keuangan_spt = $usr->getField("SYARAT_KEUANGAN_SPT");
			$this->syarat_keuangan_spt_tahun = $usr->getField("SYARAT_KEUANGAN_SPT_TAHUN");
			
			$this->syarat_keuangan_ppn = $usr->getField("SYARAT_KEUANGAN_PPN");
			$this->syarat_keuangan_pph = $usr->getField("SYARAT_KEUANGAN_PPH");
			$this->syarat_keuangan_pkp = $usr->getField("SYARAT_KEUANGAN_PKP");
			$this->syarat_neraca = $usr->getField("SYARAT_NERACA");
			$this->syarat_neraca_tahun = $usr->getField("SYARAT_NERACA_TAHUN");
			$this->syarat_sbu = $usr->getField("SYARAT_SBU");
						
			$this->syarat_admin_klasifikasi = $usr->getField("SYARAT_ADM_KUALIFIKASI");
			
			$this->syarat_keuangan_bulan_pph= $usr->getField("SYARAT_KEUANGAN_PPH_BULAN");
			$this->syarat_keuangan_bulan_ppn= $usr->getField("SYARAT_KEUANGAN_PPN_BULAN");
			
			$this->syarat_keuangan_info_spt= $usr->getField("SYARAT_TEKNIS_SERTIFIKAT_INFO");
			
			$this->syarat_ijin_siujk= $usr->getField("SYARAT_IJIN_SIUJK");
			$this->syarat_ijin_siui= $usr->getField("SYARAT_IJIN_SIUI");
			$this->syarat_ijin_lain= $usr->getField("SYARAT_IJIN_LAIN");
			$this->syarat_ijin_siup= $usr->getField("SYARAT_IJIN_SIUP");
			$this->syarat_adm_kualifikasi_info= $usr->getField("REKANAN_KUALIFIKASI");
			$this->tanggal_tahap = $usr->getField("TANGGAL_TAHAP");
			$this->tanggal_pemasukan = $usr->getField("TANGGAL_PEMASUKAN"); //aim: INC0002723
			$this->rekanan_id_pemenang = $usr->getField("REKANAN_ID_PEMENANG");
			$this->nilai_negosiasi = $usr->getField("NILAI_NEGOSIASI");
			$this->tanggal_pengumuman_pemenang = $usr->getField("TANGGAL_PENGUMUMAN_PEMENANG");
			$this->rekanan_id_penilaian = $usr->getField("REKANAN_ID_PENILAIAN");
			$this->unit_kerja = $usr->getField("UNIT_KERJA");		
			$this->unit_kerja_id = $usr->getField("UNIT_KERJA_ID");		
			$this->jenis_pengadaan = $usr->getField("JENIS_PENGADAAN");		
			$this->publish_paket = $usr->getField("PUBLISH_PAKET");		
			$this->reschedule_ke = $usr->getField("RESCHEDULE_KE");		
			$this->reschedule_1 = $usr->getField("RESCHEDULE_1");		
			$this->reschedule_2 = $usr->getField("RESCHEDULE_2");		
			$this->reschedule_3 = $usr->getField("RESCHEDULE_3");		
			$this->reschedule_4 = $usr->getField("RESCHEDULE_4");		
			$this->reschedule_5 = $usr->getField("RESCHEDULE_5");		
			$this->reschedule_6 = $usr->getField("RESCHEDULE_6");		
			$this->reschedule_7 = $usr->getField("RESCHEDULE_7");		
			$this->reschedule_8 = $usr->getField("RESCHEDULE_8");		
			$this->reschedule_9 = $usr->getField("RESCHEDULE_9");		
			$this->reschedule_10 = $usr->getField("RESCHEDULE_10");		
			$this->publish_paket_tanggal = $usr->getField("PUBLISH_PAKET_TANGGAL");		
			$this->publish_ba_penawaran = $usr->getField("PUBLISH_BA_PENAWARAN");	
			$this->publish_ba_negosiasi = $usr->getField("PUBLISH_BA_NEGOSIASI");	
			$this->publish_ba_kualifikasi = $usr->getField("PUBLISH_BA_KUALIFIKASI");		
			$this->publish_ba_penawaran_tanggal = $usr->getField("PUBLISH_BA_PENAWARAN_TANGGAL");
			$this->pr_group_number = $usr->getField("PR_GROUP_NUMBER");
			$this->sistem_sampul = $usr->getField("SISTEM_SAMPUL");
			$this->publish_ba_sampul1 = $usr->getField("PUBLISH_BA_EVALSAMPUL1");
			$this->publish_ba_sampul2 = $usr->getField("PUBLISH_BA_EVALSAMPUL2");
			$this->publish_ba_penawaran_sampul2 = $usr->getField("PUBLISH_BA_PENAWARAN2");
			$this->bahasa = $usr->getField("BAHASA");
			$this->mata_uang = $usr->getField("NILAI_MATA_UANG");		
				
			$this->user_login_id = $usr->getField("USER_LOGIN_ID");
			$this->user_login = $usr->getField("USER_LOGIN");

			$this->bidding_menit = $usr->getField("BIDDING_MENIT");
			$this->bidding_menit_tambahan = $usr->getField("BIDDING_MENIT_TAMBAHAN");
			$this->bidding_mulai = $usr->getField("BIDDING_MULAI");	
			$this->bidding = $usr->getField("BIDDING");	
			$this->bobot_teknis = $usr->getField("BOBOT_TEKNIS");	
			$this->bobot_harga = $usr->getField("BOBOT_HARGA");	
			$this->passing_grade = $usr->getField("PASSING_GRADE");	
			$this->multi_pemenang = $usr->getField("MULTI_PEMENANG");	
			$this->nip = $usr->getField("NIP_PEMBUAT");	
			$this->publish_eval_kualifikasi = $usr->getField("PUBLISH_EVALKUALIFIKASI");	
			$this->kode_pr = $usr->getField("KODE_PR");	
			$this->kode_rup = $usr->getField("KODE_RUP");	
			$this->alasan_batal = $usr->getField("ALASAN");	
			$this->alasan_gagal = $usr->getField("ALASAN_ULANG");	
			$this->multi_bidang_usaha = $usr->getField("MULTI_BIDANG_USAHA");	
		}
		
		$this->query = $usr->query;
		
		unset($usr);
    }
		   
}
	
  /***** INSTANTIATE THE GLOBAL OBJECT */
  $paketInfo = new paketinfo();

?>
