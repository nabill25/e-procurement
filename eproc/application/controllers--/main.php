<?php
date_default_timezone_set('UTC');

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */
include_once("functions/image.func.php");
include_once("functions/string.func.php");

class Main extends CI_Controller {

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
			$this->KODE_SA =  $this->kauth->getInstance()->getIdentity()->KODE_SA;
			$this->KODE_DPSJ =  $this->kauth->getInstance()->getIdentity()->KODE_DPSJ;
			$this->USER_TYPE =  $this->kauth->getInstance()->getIdentity()->USER_TYPE;
			$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;
			$this->USER_JABATAN_PANITIA =  $this->kauth->getInstance()->getIdentity()->USER_JABATAN_PANITIA;
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
			$this->VALIDATOR_UNIT = $this->kauth->getInstance()->getIdentity()->VALIDATOR_UNIT;
			$this->APPROVAL_UNIT = $this->kauth->getInstance()->getIdentity()->APPROVAL_UNIT;
			$this->REKANAN = $this->kauth->getInstance()->getIdentity()->REKANAN;
			$this->REKANAN_KODE = $this->kauth->getInstance()->getIdentity()->REKANAN_KODE;
			$this->REKANAN_PKP = $this->kauth->getInstance()->getIdentity()->REKANAN_PKP;
			$this->REKANAN_NPWP = $this->kauth->getInstance()->getIdentity()->REKANAN_NPWP;
			$this->REKANAN_STATUS_PERUSAHAAN = isset($this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN) ? $this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN : '';
			$this->REKANAN_STATUS_VALIDASI = isset($this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI) ? $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI : '';
			$this->USER_STATUS = $this->kauth->getInstance()->getIdentity()->USER_STATUS;
			$this->REKANAN_EMAIL = $this->kauth->getInstance()->getIdentity()->REKANAN_EMAIL;
			$this->DEPARTMENT = $this->kauth->getInstance()->getIdentity()->DEPARTMENT;
			$_SESSION["lang"] = null;
		} else {
			$this->ID = null;
			$this->USER_LOGIN_ID =  null;
			$this->USER_LOGIN =  null;
			$this->USER_NAMA =  null;
			$this->USER_TYPE_ID =  null;
			$this->KODE_SA =  null;
			$this->KODE_DPSJ =  null;
			$this->USER_TYPE =  null;
			$this->LEGAL =  null;
			$this->USER_JABATAN_PANITIA =  null;
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
			$this->DEPARTMENT = null;
			$_SESSION["lang"] = null;
		}

		/* BLOCK AKSES EVALUASI SELAIN PANITIA */
		if(stristr($this->uri->segment(3, ""), "evaluasi") ||
		   stristr($this->uri->segment(3, ""), "paket_lelang_tambah"))
		{
			// if($this->USER_TYPE_ID == "3" || $this->USER_TYPE_ID == "7")
			if($this->USER_TYPE_ID == "3" || $this->USER_TYPE_ID == "7" || $this->USER_TYPE_ID == "9" || $this->USER_TYPE_ID == "11") // ikn tambah buat 7=Kepala Pengadaan 11:pejabat pengadaan
			{}
			else
				redirect(base_url());
		}

		/* BLOCK AKSES MASTER SELAIN ADMINISTRATOR KECUALI MASTER_BLACKLIST UNTUK ADMIN VMS */
		if($this->uri->segment(3, "") == "master_blacklist")
		{
			if($this->USER_TYPE_ID == "2")
			{}
			else
				redirect(base_url());
		} else if(stristr($this->uri->segment(3, ""), "master"))
		{
			if($this->USER_TYPE_ID == "1" || $this->USER_TYPE_ID == "4") // 1:Administrator, 2:Administrator Approval
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
			if($this->USER_TYPE_ID == "1" || $this->USER_TYPE_ID == "2" || $this->USER_TYPE_ID == "11" || $this->USER_TYPE_ID == "18"|| $this->USER_TYPE_ID == "19" || $this->USER_TYPE_ID == "26") // 1:admin, 2. admin vms, 18:approval vms, 19:Rekomendasi VMS, 26:Senior Manager, 11:Pejabat Pengedaan
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
		$pg = $this->uri->segment(3, "home");
		$reqParse1 = $this->uri->segment(4, "");
		$reqParse2 = $this->uri->segment(5, "");
		$reqParse3 = $this->uri->segment(6, "");
		$reqParse4 = $this->uri->segment(7, "");
		$reqParse5 = $this->uri->segment(5, "");
		// $reqId = $this->input->get("reqId");
		$reqId = $this->input->get("eid") ? $this->input->get("eid") : $this->input->get("reqId");
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
		if (!is_file(APPPATH.'views/main/' . $pg . EXT)) { redirect(base_url().'main/index/404'); }

		// Pisahkan core load view yang belum login
		if (isset($this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID)) {
			// echo '<div class="col-md-12 alert alert-primary">Login</div>';
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
				'content' 		=> $this->load->view("main/".$pg,$view,TRUE),
				'pg' 			=> $pg,
				'reqParse1' => $reqParse1,
				'reqParse2'	=> $reqParse2,
				'reqParse3'	=> $reqParse3,
				'reqParse4'	=> $reqParse4,
				'reqParse5'	=> $reqParse5,
				// 'captcha' => $this->recaptcha->getWidget(), // menampilkan recaptcha
	            // 'script_captcha' => $this->recaptcha->getScriptTag(), // javascript recaptcha ditaruh di head
			);

			// echo "<pre>"; print_r($data); die();
			$this->load->view('main/index', $data);
		} else {
			// echo '<div class="col-md-12 alert alert-danger">No Login</div>';
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
				'content' 		=> $this->load->view("main/".$pg,$view,TRUE),
				'pg' 			=> $pg,
				'reqParse1' => $reqParse1,
				'reqParse2'	=> $reqParse2,
				'reqParse3'	=> $reqParse3,
				'reqParse4'	=> $reqParse4,
				'reqParse5'	=> $reqParse5,
				// 'captcha' => $this->recaptcha->getWidget(), // menampilkan recaptcha
	            // 'script_captcha' => $this->recaptcha->getScriptTag(), // javascript recaptcha ditaruh di head
			);

			$this->load->view('main/index_nologin', $data);
		}

	}

	public function admin()
	{
		redirect(base_url());
	}

	public function getNotif()
	{
		sleep(0);
		// 1	ADMIN / SUPERADMIN
		// 2	ADMINISTRATOR VMS
		// 3	PANITIA / PELAKSANA PENGADAAN
		// 4	ADMIN APPROVAL / APPROVAL SUPER ADMIN
		// 6	PENYEDIA
		// 7	KEPALA PENGADAAN / MANAGER PENGADAAN
		// 9	PENGGUNA
		// 10	AUDIT
		// 11	PELAKSANA PEMBELI / PURCHASER
		// 12	PENGELOLA KONTRAK
		// 13	PPHP
		// 14	TENAGA TEKNIS
		// 15	KONSULTAN PENGAWAS
		// 16	ADM. KONTRAK
		// 17	ADMIN RENCANA PENGADAAN
		// 18	APPROVAL VMS
		// 19	APPROVAL PENYELIA
		// 20	PEMERIKSA KONTRAK
		// 21	UNIT / INSTALASI
		// 22	VALIDATOR UNIT
		// 23	APPROVAL UNIT
		// 24	ADMIN RUP JENJANG
		// 25	DIREKSI
		// 26	SENIOR MANAGER

		switch ($this->USER_TYPE_ID) {
			case '2': // ADMIN VMS
	 			$data = $this->libnotification->notifAdminVMS();
				break;
			case '3': // POKJA
	 			$data = $this->libnotification->notifPokja();
				break;
			case '6': // PENYEDIA
	 			$data = $this->libnotification->notifRekanan();
				break;
			case '7': // MANAGER PENGADAAN / KPP
	 			$data = $this->libnotification->notifManagerPengadaan();
				break;
			case '9': // PENGGUNA
	 			$data = $this->libnotification->notifPengguna();
				break;
			case '12': // PENGELOLA KONTRAK
			case '20': // KASUBDIT KONTRAK
	 			$data = $this->libnotification->notifKontrak();
				break;
			case '18': // APPROVAL VMS
	 			$data = $this->libnotification->notifApprovalVMS();
				break;
			case '19': // APPROVAL PENYELIA
	 			$data = $this->libnotification->notifPenyeliaVMS();
				break; 
			case '28': // PPK
	 			$data = $this->libnotification->notifKontrak();
				break; 

			default:
				$data = array('data' => '<span class="dropdown-item">. : : Tidak ada pesan : : . </span>', 'count' => '' );
				break;
		}

	 echo json_encode(array('data' => $data['data'], 'count' => $data['count']));

	}

	public function testemail($email=null)
	{
		$this->load->library("KMail");

		if ($email) {
					echo "masuk ".$email;
		$cbg = str_replace(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR,'',dirname(__FILE__));
		$mail = new KMail($cbg);
		$mail->Subject  =  'Test Kirim Email';
		$mail->AddAddress($email , 'PT ABC');
		$text = 'Kirim email berhasil';

		// $body = $this->get_content(base_url()."main/loadUrl/mail/registrasi_rekanan/154");
		// $fp = fsockopen('ssl://'.$host, $prt , $errno , $errstr , 4);
		$body = $text;
			$mail->MsgHTML($body);

			if(!$mail->Send())
			{
				echo "Mailer Error: " . $mail->ErrorInfo; die();
			}
			else
			{
				echo 'Message has been sent.';
			}
		} else {
			echo "email kosong";
		}
	}

	public function curlt($url){
	$ch = curl_init();

	    curl_setopt($ch, CURLOPT_URL, $url);

	    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

	    $content = curl_exec ($ch);
	if (curl_errno($ch)) {
	    $error_msg = curl_error($ch);
	}

	// echo $error_msg;
	    curl_close ($ch);

	return $content;
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

	public function loadUrlKontrak()
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
		if($reqFolder == "kontrak")
			$this->session->set_userdata('currentUrl', $reqFilename);

		$this->load->view($reqFolder.'/'.$reqFilename, $data);
	}

	function testcas() {
	  // $this->load->library('cas');
	  // $this->cas->force_auth();
	  // $user = $this->cas->user();
		// echo "<pre>";
		// var_dump($user);
	  // echo "Hello, $user->userlogin!";
		//
		// $username = phpCAS::getUser();
	  // $attributes = phpCAS::getAttributes();
	  // print_r($attributes);
	}

}
