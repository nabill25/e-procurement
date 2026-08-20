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

class login extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");
		/* GLOBAL VARIABLE */
		// $this->db->query("alter session set nls_date_format='DD-MM-YYYY'");
	}

	public function index()
	{

		$pg = $this->uri->segment(3, "home");
		$reqParse1 = $this->uri->segment(4, "");
		$reqParse2 = $this->uri->segment(5, "");
		$reqParse3 = $this->uri->segment(6, "");
		$reqParse4 = $this->uri->segment(7, "");
		$reqParse5 = $this->uri->segment(5, "");

		$view = array(
			'pg' => $pg,
			'reqParse1' => $reqParse1,
			'reqParse2'	=> $reqParse2,
			'reqParse3'	=> $reqParse3,
			'reqParse4'	=> $reqParse4,
			'reqParse5'	=> $reqParse5
		);

		$data = array(
			'content' => $this->load->view("main/".$pg,$view,TRUE),
			'pg' => $pg,
			'reqParse1' => $reqParse1,
			'reqParse2'	=> $reqParse2,
			'reqParse3'	=> $reqParse3,
			'reqParse4'	=> $reqParse4,
			'reqParse5'	=> $reqParse5
		);

		$this->load->view('main/index', $data);
	}

	public function action()
	{
		$this->load->library("crfs_protect"); $csrf = new crfs_protect('_crfs_rr_login');

		// 1	SUPER ADMIN
		// 2	ADMIN VMS
		// 3	PELAKSANA PENGADAAN / PANITIA
		// 4 	APPROVAL SUPER ADMIN
		// 6	PENYEDIA
		// 7	KEPALA PENGADAAN / MANAGER PENGADAAN
		// 9	PENGGUNA
		// 10	AUDIT
		// 11	PELAKSANA PEMBELI / PEJABAT PENGADAAN
		// 12	PENGELOLA KONTRAK
		// 13	PPHP
		// 14 	TENAGA TEKNIS
		// 15 	KONSULTAN PENGAWAS
		// 16 	ADM. KONTRAK
		// 17	ADMIN RUP / ADMIN RENCANA PENGADAAN
		// 18	APPROVAL VMS
		// 19	APPROVAL PENYELIA / REKOMENDASI VMS
		// 20	PEMERIKSA KONTRAK
		// 21	UNIT / INSTALASI
		// 22 	VALIDATOR UNIT
		// 23 	APPROVAL UNIT
		// 24 	ADMIN RUP JENJANG
		// 25	DIREKSI
		// 26	SENIOR MANAGER

		$reqUser = $this->input->post("reqUser");
		$reqPasswd = $this->input->post("reqPasswd");
		// Captcha google
		$recaptcha = $this->input->post('g-recaptcha-response');
  		$response = $this->recaptcha->verifyResponse($recaptcha);
		$token = md5(session_id().date('his').rand().$this->input->post("reqUser"));
		$this->session->set_userdata('token',$token);

		if($reqUser == "")
		{
			$reqUser = $this->input->get("reqUser");
			$reqPasswd = $this->input->get("reqPasswd");
		}
		// Captcha pakai angka
		// if (!$csrf->isTokenValid($_POST['_crfs_rr_login']))
		// 	redirect(base_url().'main/index');
			// exit();
		if(!empty($reqUser) AND !empty($reqPasswd))
		{
			// if ($response['success'] == '1') {
				if($this->kauth->localAuthenticate($reqUser,$reqPasswd,$token))
				{
					$this->load->model("UsersBase");
					$users_base = new UsersBase();
					$users_base->setField("USER_LOGIN_ID", $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID);
					$users_base->updateLasLogin();

					// insert to user_login_logs
					$userslogin_logs = new UsersBase();
	           		$server = implode("||",$_SERVER);
					$users_base->setField("LOGS_IP", $this->getIp());
					$users_base->setField("LOGS_OS", $this->getOS());
					$users_base->setField("LOGS_BROWSER", $this->getBrowser());
					$users_base->setField("LOGS_INFOSERVER", $server);
					$users_base->setField("USER_LOGIN_ID", $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID);
					$users_base->setField("USER_LOGIN", $reqUser);
					$users_base->setField("TOKEN", $token);
					$users_base->setField("AKTIF", '1');
					$users_base->insertLoginLogs();

					// attempt 0 lagi
					$updateAttempt = new UsersBase();
					$updateAttempt->setField("CREATED_BY", $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID);
					$updateAttempt->setField("USER_LOGIN_ID", $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID);
					$updateAttempt->setField("ATTEMPT", 0);
					$updateAttempt->update_attempt();


					if($this->kauth->getInstance()->getIdentity()->USER_TYPE_ID == "6") // penyedia
					{
						if($this->kauth->getInstance()->getIdentity()->USER_STATUS == "0") { // belum konfirmasi
							if ($this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI == '1') { // user sudah validasi dan akan melakukan perubahan data
								echo 'sukses-'.base_url().'main/index';
							} else {
								echo 'sukses-'.base_url().'main/index/konfirmasi_pendaftaran';
							}
							// insert akses penyedia jika tidak ada di tabel
							$this->insertAllowURL();
						}
						elseif($this->kauth->getInstance()->getIdentity()->USER_STATUS == "2") {
							// 0=Belum 1=Validasi 2=Hapus 3=Kirim ke Rekomendator, 4=Kirim ke Validator, 10=Tolak
							if ($this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI == "0" || $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI == "10") {
								echo 'sukses-'.base_url().'main/index/konfirmasi_pendaftaran';
								// insert akses penyedia jika tidak ada di tabel
								$this->insertAllowURL();
							} else {
								echo 'sukses-'.base_url().'main/index';
							}
						}
						else {
							$this->insertAllowURL();
							echo 'sukses-'.base_url().'main/index';
						}
					}
					else {
						// echo $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID; die();
						switch ($this->kauth->getInstance()->getIdentity()->USER_TYPE_ID) {
							case '1': // administrator
							case '10': // audit
							case '25': // direksi
								echo 'sukses-'.base_url().'main/index/dashboardall';
								// redirect(base_url().'main/index/dashboard');
								break;
							case '4': // APPROVAL SUPER ADMIN
									echo 'sukses-'.base_url().'main/index/master_daftar_user_non_rekanan_approve';
								break;
							case '26': // senior manager
								echo 'sukses-'.base_url().'main/index/dashboardheadmanager';
								break;
							case '7': // kepala pengadaan
								echo 'sukses-'.base_url().'main/index/dashboardhead';
								// redirect(base_url().'main/index/dashboard');
								break;
							case '3': // panitia
								$this->load->model("UserLogin");
								$user_login_jabatan = new UserLogin();
								$user_login_jabatan->selectByParams(array("USER_LOGIN_ID"=> $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID));
								$user_login_jabatan->firstRow();
								if ($user_login_jabatan->getField('PENUNJUK_PIC') == '1') {
									echo 'sukses-'.base_url().'main/index/dashboardall';
								} else {
									echo 'sukses-'.base_url().'main/index/dashboard';
								}
								// redirect(base_url().'main/index/dashboard');
								break;

							case '11': // pejabat pengadaan
									echo 'sukses-'.base_url().'main/index/dashboardpembeli';
								break;
							case '9': // Pengguna
									// Cek API
									
									// End Cek API
									echo 'sukses-'.base_url().'main/index/dashboardperencana';
								break;
							case '17': // verifikator unit
									echo 'sukses-'.base_url().'main/index/dashboardunitverifikator';
								break;
							case '21': // unit instalasi
									echo 'sukses-'.base_url().'main/index/dashboardunit';
								break;
							case '2': // admin vms
							case '18': // approval vms
							case '19': // rekomendasi vms
								echo 'sukses-'.base_url().'main/main/dashboardvms';
								break;
							case '22': // Validator
								echo 'sukses-'.base_url().'main/index/dashboardunitvalidator';
								break;
							case '23': // Approval
								echo 'sukses-'.base_url().'main/index/dashboardunitapproval';
								break;
							case '12': // pengelola kontrak
							case '20': // pemeriksa kontrak
								echo 'sukses-'.base_url().'kontrak/index/dashboardkontrak';
								break;
							case '26': // senior manager
								echo 'sukses-'.base_url().'kontrak/index/dashboardall';
								break;
							case '27': // Perencana
							case '28': // Perencana
								echo 'sukses-'.base_url().'main/index/dashboardperencanadiv';
								break;
							default:
								echo 'sukses-'.base_url().'main/index';
								break;
						}
					}
				}
				else
				{
					$cekUsernameCount = new UsersBase();
					$updateAttempt = new UsersBase();
					$cekUsernameCount->selectById($reqUser);

					if ($cekUsernameCount->countRow() > 0 ) { // jika username ada di database, update attempt
						$cekUsername = new UsersBase();
						$cekUsername->selectById($reqUser);
						$cekUsername->firstRow();
						$reqUserloginid = $cekUsername->getField('USER_LOGIN_ID');
						$reqAttempt = $cekUsername->getField('ATTEMPT') ?: 0;
						$maxAttempt = 4;
						$countAttempt = $reqAttempt + 1;
						$sisaAttempt = $maxAttempt - $countAttempt;

						if ($countAttempt >= 4) { // non-aktifkan akun

							$updateAttempt->setField("CREATED_BY", $reqUserloginid);
							$updateAttempt->setField("USER_LOGIN_ID", $reqUserloginid);
							$updateAttempt->setField("ATTEMPT", $countAttempt);
							$updateAttempt->update_attempt();

							$updateStatusAktif = new UsersBase();
							$updateStatusAktif->setField("CREATED_BY", $reqUserloginid);
							$updateStatusAktif->setField("USER_LOGIN_ID", $reqUserloginid);
							$updateStatusAktif->setField("USER_AKTIF", '0');
							$updateStatusAktif->update_status_aktif();
							echo 'gagal-Akun di blokir karena Kesalahan memasukan Username atau password lebih dari 4 kali. Silahkan hubungi Administrator'.$attempt;
						} else { // update attempt
							$updateAttempt->setField("CREATED_BY", $reqUserloginid);
							$updateAttempt->setField("USER_LOGIN_ID", $reqUserloginid);
							$updateAttempt->setField("ATTEMPT", $countAttempt);
							$updateAttempt->update_attempt();

							echo 'gagal-Username atau password salah, anda memiliki '.$sisaAttempt.' kesempatan lagi.';
						}
						// $updateAttempt->updateAttempt();
					} else {
						echo 'gagal-Username atau password tidak terdaftar';
					}

				}
			// }
			// else
			// {
			// 	echo 'gagal-Captcha Not Valid.';
			// }
		}
		else
		{
			echo 'gagal-Masukkan username dan password.';
		}
		// echo json_encode(array('message' => $message, 'url' => $url));
	}

	public function eprocsso()
	{
		error_reporting(0);
		// echo "hello, direct to SSO"; die;
		$this->load->library('cas');
	  	$this->cas->force_auth();
	  	$user = $this->cas->user();

		if ($user) {
			$token = md5(session_id().date('his').rand().$user->userlogin);

			// echo "Hello, $user->userlogin!";
			// $username = phpCAS::getUser();
			// $attributes = phpCAS::getAttributes();
			// print_r($attributes);
			if($this->kauth->localAuthenticateSSO($user->userlogin,$token))
			{
				$this->load->model("UsersBase");
				$users_base = new UsersBase();
				$users_base->setField("USER_LOGIN_ID", $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID);
				$users_base->updateLasLogin();

				// insert to user_login_logs
				$userslogin_logs = new UsersBase();
				$server = implode("||",$_SERVER);
				$users_base->setField("LOGS_IP", $this->getIp());
				$users_base->setField("LOGS_OS", $this->getOS());
				$users_base->setField("LOGS_BROWSER", $this->getBrowser());
				$users_base->setField("LOGS_INFOSERVER", $server);
				$users_base->setField("USER_LOGIN_ID", $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID);
				$users_base->setField("USER_LOGIN", $reqUser);
				$users_base->setField("TOKEN", $token);
				$users_base->setField("AKTIF", '1');
				$users_base->insertLoginLogs();

				// attempt 0 lagi
				$updateAttempt = new UsersBase();
				$updateAttempt->setField("CREATED_BY", $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID);
				$updateAttempt->setField("USER_LOGIN_ID", $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID);
				$updateAttempt->setField("ATTEMPT", 0);
				$updateAttempt->update_attempt();


				if($this->kauth->getInstance()->getIdentity()->USER_TYPE_ID == "6") // penyedia
				{
				}
				else {
					// echo $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID; die();
					switch ($this->kauth->getInstance()->getIdentity()->USER_TYPE_ID) {
						case '1': // administrator
						case '10': // audit
						case '25': // direksi
							redirect(base_url().'main/index/dashboardall');
							break;
						case '4': // APPROVAL SUPER ADMIN
						redirect(base_url().'main/index/master_daftar_user_non_rekanan_approve');
							break;
						case '26': // senior manager
						redirect(base_url().'main/index/dashboardheadmanager');
							break;
						case '7': // kepala pengadaan
						redirect(base_url().'main/index/dashboardhead');
							break;
						case '3': // panitia
							$this->load->model("UserLogin");
							$user_login_jabatan = new UserLogin();
							$user_login_jabatan->selectByParams(array("USER_LOGIN_ID"=> $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID));
							$user_login_jabatan->firstRow();
							if ($user_login_jabatan->getField('PENUNJUK_PIC') == '1') {
								// echo 'sukses-'.base_url().'main/index/dashboardall';
								redirect(base_url().'main/index/dashboardall');
							} else {
								// echo 'sukses-'.base_url().'main/index/dashboard';
								redirect(base_url().'main/index/dashboard');
							}
							// redirect(base_url().'main/index/dashboard');
							break;

						case '11': // pejabat pengadaan
								// echo 'sukses-'.base_url().'main/index/dashboardpembeli';
								redirect(base_url().'main/index/dashboardpembeli');
							break;
						case '9': // Perencana
								// echo 'sukses-'.base_url().'main/index/dashboardperencana';
								redirect(base_url().'main/index/dashboardperencana');
							break;
						case '17': // verifikator unit
								// echo 'sukses-'.base_url().'main/index/dashboardunitverifikator';
								redirect(base_url().'main/index/dashboardunitverifikator');
							break;
						case '21': // unit instalasi
								// echo 'sukses-'.base_url().'main/index/dashboardunitverifikator';
								redirect(base_url().'main/index/dashboardunit');
							break;
						case '2': // admin vms
						case '18': // approval vms
						case '19': // rekomendasi vms
							redirect(base_url().'main/index/dashboardvms');
							break;
						case '22': // Validator
							// echo 'sukses-'.base_url().'main/index/dashboardunitvalidator';
							redirect(base_url().'main/index/dashboardunitvalidator');
							break;
						case '23': // Approval
							// echo 'sukses-'.base_url().'main/index/dashboardunitapproval';
							redirect(base_url().'main/index/dashboardunitapproval');
							break;
						case '12': // pengelola kontrak
						case '20': // pemeriksa kontrak
							// echo 'sukses-'.base_url().'kontrak/index/dashboardkontrak';
							redirect(base_url().'kontrak/index/dashboardkontrak');
							break;
						case '26': // senior manager
							// echo 'sukses-'.base_url().'kontrak/index/dashboardall';
							redirect(base_url().'kontrak/index/dashboardall');
							break;
						case '27': // Perencana
							// echo 'sukses-'.base_url().'main/index/dashboardperencanadiv';
							redirect(base_url().'main/index/dashboardperencanadiv');
							break;
						default:
							// echo 'sukses-'.base_url().'main/index';
							redirect(base_url().'main/index');
							break;
					}
				}
			} else {
				redirect (base_url().'main');
			}
		} else {
			redirect (base_url().'main');
		}
		// echo "<pre>";
		// var_dump($user);
	}

	public function insertAllowURL()
	{
		$this->load->model("Rekananurlvalidasiallow");
		$cekrekananurlvalidasiallow = new Rekananurlvalidasiallow();
		$cekrekananurlvalidasiallow->selectByParams(array('REKANAN_ID' => $this->kauth->getInstance()->getIdentity()->ID),-1,-1);

		if ($cekrekananurlvalidasiallow->countRow() > 0) { }
		else {
			$cekrekananurlvalidasiallow->setField("REKANAN_ID", $this->kauth->getInstance()->getIdentity()->ID);
			$cekrekananurlvalidasiallow->setField("ALLOW_URL", '2,3,5,6,7,8,9,10,11,12,13,14,16,17,18,19,21,22,23,24,25,26,28,29,30,31,36,32,33,34,35');
			$cekrekananurlvalidasiallow->setField("CREATED_BY", $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID);
			$cekrekananurlvalidasiallow->insertAllowLogin();
		}
	}

	public function logout()
	{
		if ($this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID) {
			$this->load->model("UsersBase");
			$users_base = new UsersBase();
			$users_base->setField("LOGS_OS", $this->getOS());
			$users_base->setField("LOGS_BROWSER", $this->getBrowser());
			$users_base->setField("USER_LOGIN_ID", $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID);
			$users_base->setField("USER_LOGIN", $this->kauth->getInstance()->getIdentity()->USER_LOGIN);
			$users_base->setField("TOKEN", $this->session->userdata('token'));
			$users_base->updateLoginLogs();
		}

		if ($this->kauth->getInstance()->getIdentity()->USER_TYPE_ID != '6') { // selain penyedia
			$this->kauth->getInstance()->clearIdentity();
			$this->load->library('cas');
			phpCAS::logout(['url' => base_url().'main']);
		} else {
			$this->kauth->getInstance()->clearIdentity();
			redirect (base_url().'main');
		}
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
		$this->load->view($reqFolder.'/'.$reqFilename, $data);
	}

	public function autho()
	{
		$this->load->model("UsersBase");
		$users_base = new UsersBase();
		$users_base2 = new UsersBase();
		$explode = explode('-',$this->kauth->getInstance()->getIdentity()->TOKEN);
		$token = $explode[0];

		// $users_base->selectByParamsLogs(array("USER_LOGIN_ID" => $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID, "USER_LOGIN" => $this->kauth->getInstance()->getIdentity()->USER_LOGIN, "LOGS_OS" => $this->getOS(), "LOGS_BROWSER" => $this->getBrowser(), "AKTIF" => "1" ));
		$users_base->selectByParamsLogs(array("TOKEN" => $token, "AKTIF" => "0", "USER_LOGIN" => $this->kauth->getInstance()->getIdentity()->USER_LOGIN));

		if ($users_base->countRow() > 0 ) {
		 $arrRet = array('respon' => 'true', 'message' => 'found'); // logout
		} else {
		 $arrRet = array('respon' => 'false', 'message' => 'not found');
		}

		echo json_encode($arrRet);
	}

	public function getMenit() { echo date('i'); }
	public function getDetik() { echo date('s'); }

	// Ref : https://stackoverflow.com/questions/18070154/get-operating-system-info
	private function getOS() {
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$os_platform  = "Unknown OS Platform";
		$os_array     = array(
		                     '/windows nt 10/i'      =>  'Windows 10',
		                     '/windows nt 6.3/i'     =>  'Windows 8.1',
		                     '/windows nt 6.2/i'     =>  'Windows 8',
		                     '/windows nt 6.1/i'     =>  'Windows 7',
		                     '/windows nt 6.0/i'     =>  'Windows Vista',
		                     '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
		                     '/windows nt 5.1/i'     =>  'Windows XP',
		                     '/windows xp/i'         =>  'Windows XP',
		                     '/windows nt 5.0/i'     =>  'Windows 2000',
		                     '/windows me/i'         =>  'Windows ME',
		                     '/win98/i'              =>  'Windows 98',
		                     '/win95/i'              =>  'Windows 95',
		                     '/win16/i'              =>  'Windows 3.11',
		                     '/macintosh|mac os x/i' =>  'Mac OS X',
		                     '/mac_powerpc/i'        =>  'Mac OS 9',
		                     '/linux/i'              =>  'Linux',
		                     '/ubuntu/i'             =>  'Ubuntu',
		                     '/iphone/i'             =>  'iPhone',
		                     '/ipod/i'               =>  'iPod',
		                     '/ipad/i'               =>  'iPad',
		                     '/android/i'            =>  'Android',
		                     '/blackberry/i'         =>  'BlackBerry',
		                     '/webos/i'              =>  'Mobile'
		               );
		foreach ($os_array as $regex => $value)
		 if (preg_match($regex, $user_agent))
		   $os_platform = $value;

		return $os_platform;
	}

	private function getBrowser() {
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$browser        = "Unknown Browser";
		$browser_array = array(
		                       '/msie/i'      => 'Internet Explorer',
		                       '/firefox/i'   => 'Firefox',
		                       '/safari/i'    => 'Safari',
		                       '/chrome/i'    => 'Chrome',
		                       '/edge/i'      => 'Edge',
		                       '/opera/i'     => 'Opera',
		                       '/netscape/i'  => 'Netscape',
		                       '/maxthon/i'   => 'Maxthon',
		                       '/konqueror/i' => 'Konqueror',
		                       '/mobile/i'    => 'Handheld Browser'
		                );

		foreach ($browser_array as $regex => $value)
		 if (preg_match($regex, $user_agent))
		   $browser = $value;

		return $browser;
	}

	// Mendapatkan IP pengunjung menggunakan $_SERVER
	private function getIp() {
	 $ipaddress = '';
	 if (isset($_SERVER['HTTP_CLIENT_IP']))
	     $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	 else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	     $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	 else if(isset($_SERVER['HTTP_X_FORWARDED']))
	     $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	 else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
	     $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	 else if(isset($_SERVER['HTTP_FORWARDED']))
	     $ipaddress = $_SERVER['HTTP_FORWARDED'];
	 else if(isset($_SERVER['REMOTE_ADDR']))
	     $ipaddress = $_SERVER['REMOTE_ADDR'];
	 else
	     $ipaddress = 'IP tidak dikenali';
	 return $ipaddress;
	}

	public function manualbook()
	{
		// 1	ADMIN
		// 4	ADMIN APPROVAL

		// 2	ADMINISTRATOR VMS
		// 18	APPROVAL SUB DIV
		// 19	APPROVAL PENYELIA

		// 3	PANITIA
		// 7	KEPALA PENGADAAN
		// 9	PENGGUNA
		// 10	AUDIT
		// 11	PURCHASER
		// ----------- PERENCANA
		// 17 	ADMIN RUP
		// 21 	UNIT / INSTALASI
		// 22 	VALIDATOR UNIT
		// 23 	APPROVAL UNIT
		// 24 	ADMIN RUP


		// 12	PENGELOLA KONTRAK
		// 20	PEMERIKSA KONTRAK

		// 6	PENYEDIA

		// Belom ada manual book
		// 13 	PPHP
		// 14 	TENAGA TEKNIS
		// 15 	KONSULTAN PENGAWAS
		// 16 	ADM. KONTRAK
		switch ($this->USER_TYPE_ID) {
			case '1':
			case '4':
				redirect(base_url('/uploads/manual_book/admin-10d0b55e0ce96e1ad711adaac266c9200cbc27e4jts.pdf'));
				break;

			case '2':
			case '18':
			case '19':
				redirect(base_url('/uploads/manual_book/vms-7815fe8ae4a280234ff3203d70ebe234dafa6215jts.pdf'));
				break;

			case '9':
			case '17':
				redirect(base_url('/uploads/manual_book/pengguna-0269c5569e4631ac09b620856e1f50da.pdf'));
				break;

			case '10':
				redirect(base_url('/uploads/manual_book/audit-3d52903611a8f873b42fa32fb9312a89.pdf'));
				break;

			case '3':
			case '7':
			case '9':
			case '11':
			case '21':
			case '22':
			case '23':
			case '24':
			case '28':
				redirect(base_url('/uploads/manual_book/panitia-5880bb84dadfd2318acfd5a520255cd2466465acjts.pdf'));
				break;

			case '12':
			case '20':
				redirect(base_url('/uploads/manual_book/kontrak-d03c1fa9e14858d15d0953d6bbc0323a196b24c6jts.pdf'));
				break;

			case '6':
				redirect(base_url('/uploads/manual_book/penyedia-f12c8db94d1286639c3bc4a0a9d715d5e2add83ajts.pdf'));
				break;

			default:
				echo ". : : No Manual Book : : .";
				break;
		}
	}

}
