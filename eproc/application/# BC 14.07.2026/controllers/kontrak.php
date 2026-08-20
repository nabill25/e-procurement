<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

include_once("functions/image.func.php");
include_once("functions/string.func.php");

class Kontrak extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");
		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			//redirect('main');
		}
		// $this->db->query("alter session set nls_date_format='YYYY-MM-DD'");
		// $this->db->query("alter session set nls_numeric_characters='.,'");

		if (isset($this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID)) {
			$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;

			$this->USER_LOGIN_ID =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
			$this->USER_LOGIN =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN;
			$this->USER_NAMA =  $this->kauth->getInstance()->getIdentity()->USER_NAMA;
			$this->USER_TYPE_ID =  $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID;
			$this->USER_TYPE =  $this->kauth->getInstance()->getIdentity()->USER_TYPE;
			$this->REKANAN_ID =  $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
			$this->REKANAN_TIPE_ID =  $this->kauth->getInstance()->getIdentity()->REKANAN_TIPE_ID;
			$this->UNIT_KERJA_ID =  $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID;
			$this->LEVEL_PERENCANA =  $this->kauth->getInstance()->getIdentity()->LEVEL_PERENCANA;
			$this->LEVEL_PEMBELI =  $this->kauth->getInstance()->getIdentity()->LEVEL_PEMBELI;
			$this->LEVEL_KONTRAK =  $this->kauth->getInstance()->getIdentity()->LEVEL_KONTRAK;
			$this->PENUNJUK_PIC =  $this->kauth->getInstance()->getIdentity()->PENUNJUK_PIC;
			$this->LEVEL_PENGGUNA =  $this->kauth->getInstance()->getIdentity()->LEVEL_PENGGUNA;
			$this->KASI_PENGGUNA =  $this->kauth->getInstance()->getIdentity()->KASI_PENGGUNA;
			$this->NIP =  $this->kauth->getInstance()->getIdentity()->NIP;
			$this->LOGIN_TIME = $this->kauth->getInstance()->getIdentity()->LOGIN_TIME;
			$this->LOGIN_DATE = $this->kauth->getInstance()->getIdentity()->LOGIN_DATE;
			$this->REKANAN = $this->kauth->getInstance()->getIdentity()->REKANAN;
			$this->REKANAN_KODE = $this->kauth->getInstance()->getIdentity()->REKANAN_KODE;
			$this->REKANAN_PKP = $this->kauth->getInstance()->getIdentity()->REKANAN_PKP;
			$this->REKANAN_NPWP = $this->kauth->getInstance()->getIdentity()->REKANAN_NPWP;
			$this->REKANAN_STATUS_PERUSAHAAN = isset($this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN) ? $this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN : '';
			$this->REKANAN_STATUS_VALIDASI = isset($this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI) ? $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI : '';
			$this->USER_STATUS = $this->kauth->getInstance()->getIdentity()->USER_STATUS;
			$this->REKANAN_EMAIL = $this->kauth->getInstance()->getIdentity()->REKANAN_EMAIL;
			$_SESSION["lang"] = null;
		} else {
			$this->ID = null;
			$this->USER_LOGIN_ID =  null;
			$this->USER_LOGIN =  null;
			$this->USER_NAMA =  null;
			$this->USER_TYPE_ID =  null;
			$this->USER_TYPE =  null;
			$this->REKANAN_ID =  null;
			$this->REKANAN_TIPE_ID =  null;
			$this->UNIT_KERJA_ID =  null;
			$this->LEVEL_PERENCANA =  null;
			$this->LEVEL_PEMBELI =  null;
			$this->LEVEL_KONTRAK =  null;
			$this->PENUNJUK_PIC =  null;
			$this->LEVEL_PENGGUNA =  null;
			$this->KASI_PENGGUNA =  null;
			$this->NIP =  null;
			$this->LOGIN_TIME = null;
			$this->LOGIN_DATE = null;
			$this->REKANAN = null;
			$this->REKANAN_KODE = null;
			$this->REKANAN_PKP = null;
			$this->REKANAN_NPWP = null;
			$this->REKANAN_STATUS_PERUSAHAAN = null;
			$this->REKANAN_STATUS_VALIDASI = null;
			$this->USER_STATUS = null;
			$this->REKANAN_EMAIL = null;
			$_SESSION["lang"] = null;
		}

		/* BLOCK AKSES EVALUASI SELAIN PANITIA */
		if(stristr($this->uri->segment(3, ""), "evaluasi") ||
		   stristr($this->uri->segment(3, ""), "paket_lelang_tambah"))
		{
			// if($this->USER_TYPE_ID == "3" || $this->USER_TYPE_ID == "7")
			if($this->USER_TYPE_ID == "3" || $this->USER_TYPE_ID == "7" || $this->USER_TYPE_ID == "9" || $this->USER_TYPE_ID == "11") // ikn tambah buat 7=fronliner 11:pejabat pengadaan
			{}
			else
				// redirect(base_url().'main');
				redirect(base_url());
		}

		/* BLOCK AKSES MASTER SELAIN ADMINISTRATOR */
		if(stristr($this->uri->segment(3, ""), "master"))
		{
			if($this->USER_TYPE_ID == "1")
			{}
			else
				redirect(base_url());
		}

		/* BLOCK AKSES MASTER SELAIN REKANAN */
		if(stristr($this->uri->segment(3, ""), "pembukaan_auction_rekanan") ||
		   stristr($this->uri->segment(3, ""), "dokumen_lelang_rekanan") ||
		   stristr($this->uri->segment(3, ""), "data_kualifikasi") ||
		   stristr($this->uri->segment(3, ""), "data_kualifikasi") ||
		   $this->uri->segment(3, "") == "negosiasi_rekanan" ||
		   $this->uri->segment(3, "") == "auction_rekanan" ||
		   stristr($this->uri->segment(3, ""), "dokumen_penawaran_")
		   )
		{
			if($this->USER_TYPE_ID == "6")
			{}
			elseif(($this->USER_TYPE_ID == "10" || $this->USER_TYPE_ID == "9") && stristr($this->uri->segment(3, ""), "dokumen_lelang_rekanan"))
			{}
			else
				redirect(base_url());

		}


		/* BLOCK AKSES POPUP DATA REKANAN SELAIN ADMIN DAN VALIDATOR */
		if(stristr($this->uri->segment(3, ""), "daftar_rekanan"))
		{
			if($this->USER_TYPE_ID == "1" || $this->USER_TYPE_ID == "2")
			{}
			else
				redirect(base_url());
		}

		/* BLOCK AKSES VALIDASI DATA REKANAN SELAIN ADMIN DAN VALIDATOR */
		if(stristr($this->uri->segment(3, ""), "validasi"))
		{
			// if($this->USER_TYPE_ID == "2")
			if($this->USER_TYPE_ID == "1" || $this->USER_TYPE_ID == "2") // ikn 20200406
			{}
			else
				redirect(base_url());
		}

	}

	public function index()
	{
		// echo "string"; die();
		$getTahun = isset($_GET['tahun']) ? $_GET['tahun'] : 'all';
		$this->session->set_userdata('setTahunKontrak',$getTahun);

		$getProses = $_GET['reqProses'] ?: $this->session->userdata('setProsesKontrak');
		$this->session->set_userdata('setProsesKontrak',$getProses);
		// echo $this->session->userdata('setProsesKontrak');

		$pg = $this->uri->segment(3, "home");

		$reqParse1 = $this->uri->segment(4, "");
		$reqParse2 = $this->uri->segment(5, "");
		$reqParse3 = $this->uri->segment(6, "");
		$reqParse4 = $this->uri->segment(7, "");
		$reqParse5 = $this->uri->segment(5, "");
		$reqId = $this->input->get("reqId");
		// echo $reqId; die();
		// echo $arrJudul[$max]; die();

		// Meta Tag Convension
		$metaTitle 		= SYSTEM_NAME.' '.SYSTEM_NAME_PT;
		$metaDesc 		= META_DESC;
		$metaAuthor 	= SYSTEM_NAME.' '.SYSTEM_NAME_PT;
		// OG Meta
		if ($pg == 'katalog' || $pg == 'katalog_detail') {
			include_once("functions/string.func.php");
			include_once("functions/date.func.php");
			include_once("functions/default.func.php");

			$name = httpFilterRequest("name") ? httpFilterRequest("name") : '-';
			$subKaetgoriLabel = httpFilterRequest("kategori") ? httpFilterRequest("kategori") : '-';

			if ($subKaetgoriLabel != '-') {
				$urlShare = SYSTEM_NAME_URL.'/main/index/katalog?kategori='.$subKaetgoriLabel;
				$nameDesc = ucwords(str_replace("-", " ", $subKaetgoriLabel));
			} else {
				$urlShare = SYSTEM_NAME_URL.'/main/index/katalog?name='.$name;
				$nameDesc = ucwords(str_replace("-", " ", $name));
			}

			$metaOGSitename	= SYSTEM_NAME.' '.SYSTEM_NAME_PT;
			$metaOGUrl 		= $urlShare;
			$metaOGType 	= 'website';
			$metaOGTitle 	= 'Katalog '.$nameDesc.' | '.SYSTEM_NAME.' | '.SYSTEM_NAME_PT;
			$metaOGDesc 	= 'Katalog '.SYSTEM_NAME_PT.' merupakan aplikasi informasi terkait katalog guna mendukung proses Pengadaan barang dan jasa di '.SYSTEM_NAME_PT;
			$metaOGImage 	= SYSTEM_LOGO_URL;
		} else {
			$metaOGSitename	= SYSTEM_NAME.' '.SYSTEM_NAME_PT;
			$metaOGUrl 		= SYSTEM_NAME_URL;
			$metaOGType 	= 'website';
			$metaOGTitle 	= SYSTEM_NAME.' '.SYSTEM_NAME_PT;
			$metaOGDesc 	= META_DESC;
			$metaOGImage 	= SYSTEM_LOGO_URL;
		}
		// End Meta Tag Convension

		$view = array(
			'pg' => $pg,
			'reqParse1' => $reqParse1,
			'reqParse2'	=> $reqParse2,
			'reqParse3'	=> $reqParse3,
			'reqParse4'	=> $reqParse4,
			'reqParse5'	=> $reqParse5
		);

		$breadcrumb = $this->libbreadcrumb->breadikn($pg,$reqId);

		// kalau page not found arahkan ke 404
		if (!is_file(APPPATH.'views/kontrak/' . $pg . EXT)) { redirect(base_url().'main/index/404'); }

		// echo $breadcrumb; die();
		$data = array(
			'metaTitle' 	=> $metaTitle,
			'metaDesc' 		=> $metaDesc,
			'metaAuthor' 	=> $metaAuthor,
			'metaOGSitename' => $metaOGSitename,
			'metaOGUrl' 	=> $metaOGUrl,
			'metaOGType' 	=> $metaOGType,
			'metaOGTitle' 	=> $metaOGTitle,
			'metaOGDesc' 	=> $metaOGDesc,
			'metaOGImage' 	=> $metaOGImage,
			'breadcrumb' 	=> $breadcrumb,
			'content' 		=> $this->load->view("kontrak/".$pg,$view,TRUE),
			'pg' 			=> $pg,
			'reqParse1' => $reqParse1,
			'reqParse2'	=> $reqParse2,
			'reqParse3'	=> $reqParse3,
			'reqParse4'	=> $reqParse4,
			'reqParse5'	=> $reqParse5
		);
		// echo "<pre>"; print_r($data); die();
		$this->load->view('main/index', $data);
	}

	public function admin()
	{
		redirect(base_url());
	}

	public function loadUrl()
	{

		$reqFolder = $this->uri->segment(3, "");
		$reqFilename = $this->uri->segment(4, "");
		$reqParse1 = $this->uri->segment(5, "");
		$reqParse2 = $this->uri->segment(6, "");
		$reqParse3 = $this->uri->segment(7, "");
		$reqParse4 = $this->uri->segment(8, "");
		$reqParse5 = $this->uri->segment(9, "");
		$data = array(
			'reqParse1' => urldecode($reqParse1),
			'reqParse2' => urldecode($reqParse2),
			'reqParse3' => urldecode($reqParse3),
			'reqParse4' => urldecode($reqParse4),
			'reqParse5' => urldecode($reqParse5)
		);
		if($reqFolder == "main")
			$this->session->set_userdata('currentUrl', $reqFilename);

		$this->load->view($reqFolder.'/'.$reqFilename, $data);
	}

}
