<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once("functions/image.func.php");
include_once("functions/string.func.php");

class Integration extends CI_Controller {

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
		if(stristr($this->uri->segment(3, ""), "integration"))
		{
			if($this->USER_TYPE_ID == "1") // ikn tambah buat 1=Super Admin
			{}
			else
				// redirect(base_url().'main');
				redirect(base_url());
		}


	}

	public function index()
	{
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

		$metaOGSitename	= SYSTEM_NAME.' '.SYSTEM_NAME_PT;
		$metaOGUrl 		= SYSTEM_NAME_URL;
		$metaOGType 	= 'website';
		$metaOGTitle 	= SYSTEM_NAME.' '.SYSTEM_NAME_PT;
		$metaOGDesc 	= META_DESC;
		$metaOGImage 	= SYSTEM_LOGO_URL;

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
		if (!is_file(APPPATH.'views/integration/' . $pg . EXT)) { redirect(base_url().'main/index/404'); }

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
			'content' 		=> $this->load->view("integration/".$pg,$view,TRUE),
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
