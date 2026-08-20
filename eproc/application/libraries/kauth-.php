<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package     eProcurement Application
 * @author      eproc2025
 * @since       25. Version 3.1
 *
 */

require_once 'kloader.php';

class kauth {

        function __construct() {
        // load the auth class
        kloader::load('Zend_Auth');
        kloader::load('Zend_Auth_Storage_Session');
        // set the unique storege
        Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session("eProc19$"));
    }

	public function localAuthenticate($username,$credential,$token) {
    $auth = Zend_Auth::getInstance();
    $auth->clearIdentity();

		$CI =& get_instance();
		$CI->load->model("Users");
		$CI->load->model("UserType");

		$users = new Users();
    $users->selectByIdUsername($username);
		// $users->selectByIdPassword($username, md5($credential));
    $users->firstRow();
		if(password_verify($credential,$users->getField("USER_PASSWORD")))
		{
            $identity = new stdClass();
            $identity->USER_LOGIN_ID = $users->getField("USER_LOGIN_ID");
            $identity->USER_LOGIN = $users->getField("USER_LOGIN");
            $identity->USER_STATUS = $users->getField("USER_STATUS");
            $identity->USER_NAMA = $users->getField("USER_NAMA");
            $identity->USER_TYPE_ID = $users->getField("USER_TYPE_ID");
            $identity->USER_TYPE = $users->getField("USER_TYPE");
            $identity->VP_PENGADAAN = $users->getField("VP_PENGADAAN");
            $identity->ADMIN_RUP = $users->getField("ADMIN_RUP");
            $identity->TENDER = $users->getField("TENDER");
            $identity->LEGAL = $users->getField("LEGAL");
            $identity->VALIDATOR_UNIT = $users->getField("VALIDATOR_UNIT");
            $identity->APPROVAL_UNIT = $users->getField("APPROVAL_UNIT");
            $identity->USER_JABATAN_PANITIA = $users->getField("USER_JABATAN_PANITIA");
            $identity->TOKEN = $token.'-token';
            $identity->DEPARTMENT = $users->getField("DEPARTMENT");
            $identity->LEVEL_PERENCANA = $users->getField("LEVEL_PERENCANA");
            $identity->LEVEL_PEMBELI = $users->getField("LEVEL_PEMBELI");

            $identity->UNIT_KERJA_ID = $users->getField("UNIT_KERJA_ID");
            $identity->NIP = $users->getField("NIP");
            $identity->LOGIN_TIME = time();
            $identity->LOGIN_DATE = date("l, j M Y, H:i",time());

            if ($users->getField("REKANAN_ID")) {
              $rekanan = new Users();
              $rekanan->selectByRekanan($users->getField("REKANAN_ID"));
              $rekanan->firstRow();
              $identity->ID = $users->getField("REKANAN_ID");
              $identity->REKANAN_ID = $users->getField("REKANAN_ID");

              $identity->REKANAN = $rekanan->getField("NAMA");
              $identity->REKANAN_TIPE_ID = $rekanan->getField("REKANAN_TIPE_ID");
              $identity->REKANAN_KODE = $rekanan->getField("KODE");
              $identity->REKANAN_PKP = $rekanan->getField("PKP");
              $identity->REKANAN_NPWP = $rekanan->getField("NPWP");
              $identity->REKANAN_EMAIL = $rekanan->getField("EMAIL");
              $identity->REKANAN_STATUS_PERUSAHAAN = $rekanan->getField("STATUS_PERUSAHAAN");
              $identity->REKANAN_STATUS_VALIDASI = $rekanan->getField("STATUS_VALIDASI");
              $identity->STATUS_VALIDASI = $rekanan->getField("STATUS_VALIDASI");
            } else {
              $identity->ID = 0;
              $identity->REKANAN_ID = 0;
              $identity->REKANAN = 0;
              $identity->REKANAN_TIPE_ID = 0;
              $identity->REKANAN_KODE = '-';
              $identity->REKANAN_PKP = '-';
              $identity->REKANAN_NPWP = '-';
              $identity->REKANAN_EMAIL = '-';
              $identity->REKANAN_STATUS_PERUSAHAAN = '-';
              $identity->REKANAN_STATUS_VALIDASI = '-';
              $identity->STATUS_VALIDASI = '-';
            }

            $auth->getStorage()->write($identity);

			if($users->getField("USER_LOGIN_ID") == "")
				return false;
			else
				return true;
		}
		else {
      return false;
    }
  }

  public function localAuthenticateSSO($username,$token) {
    $auth = Zend_Auth::getInstance();
    $auth->clearIdentity();

		$CI =& get_instance();
		$CI->load->model("Users");
		$CI->load->model("UserType");

		$users = new Users();
    $users->selectByIdUsername($username);
		// $users->selectByIdPassword($username, md5($credential));
    $users->firstRow();
		if($users->getField("USER_PASSWORD"))
		{
            $identity = new stdClass();
            $identity->USER_LOGIN_ID = $users->getField("USER_LOGIN_ID");
            $identity->USER_LOGIN = $users->getField("USER_LOGIN");
            $identity->USER_STATUS = $users->getField("USER_STATUS");
            $identity->USER_NAMA = $users->getField("USER_NAMA");
            $identity->USER_TYPE_ID = $users->getField("USER_TYPE_ID");
            $identity->USER_TYPE = $users->getField("USER_TYPE");
            $identity->VP_PENGADAAN = $users->getField("VP_PENGADAAN");
            $identity->ADMIN_RUP = $users->getField("ADMIN_RUP");
            $identity->TENDER = $users->getField("TENDER");
            $identity->LEGAL = $users->getField("LEGAL");
            $identity->VALIDATOR_UNIT = $users->getField("VALIDATOR_UNIT");
            $identity->APPROVAL_UNIT = $users->getField("APPROVAL_UNIT");
            $identity->USER_JABATAN_PANITIA = $users->getField("USER_JABATAN_PANITIA");
            $identity->TOKEN = $token.'-token';
            $identity->DEPARTMENT = $users->getField("DEPARTMENT");

            $identity->UNIT_KERJA_ID = $users->getField("UNIT_KERJA_ID");
            $identity->NIP = $users->getField("NIP");
            $identity->LOGIN_TIME = time();
            $identity->LOGIN_DATE = date("l, j M Y, H:i",time());

            if ($users->getField("REKANAN_ID")) {
              $rekanan = new Users();
              $rekanan->selectByRekanan($users->getField("REKANAN_ID"));
              $rekanan->firstRow();
              $identity->ID = $users->getField("REKANAN_ID");
              $identity->REKANAN_ID = $users->getField("REKANAN_ID");

              $identity->REKANAN = $rekanan->getField("NAMA");
              $identity->REKANAN_TIPE_ID = $rekanan->getField("REKANAN_TIPE_ID");
              $identity->REKANAN_KODE = $rekanan->getField("KODE");
              $identity->REKANAN_PKP = $rekanan->getField("PKP");
              $identity->REKANAN_NPWP = $rekanan->getField("NPWP");
              $identity->REKANAN_EMAIL = $rekanan->getField("EMAIL");
              $identity->REKANAN_STATUS_PERUSAHAAN = $rekanan->getField("STATUS_PERUSAHAAN");
              $identity->REKANAN_STATUS_VALIDASI = $rekanan->getField("STATUS_VALIDASI");
              $identity->STATUS_VALIDASI = $rekanan->getField("STATUS_VALIDASI");
            } else {
              $identity->ID = 0;
              $identity->REKANAN_ID = 0;
              $identity->REKANAN = 0;
              $identity->REKANAN_TIPE_ID = 0;
              $identity->REKANAN_KODE = '-';
              $identity->REKANAN_PKP = '-';
              $identity->REKANAN_NPWP = '-';
              $identity->REKANAN_EMAIL = '-';
              $identity->REKANAN_STATUS_PERUSAHAAN = '-';
              $identity->REKANAN_STATUS_VALIDASI = '-';
              $identity->STATUS_VALIDASI = '-';
            }

            $auth->getStorage()->write($identity);

			if($users->getField("USER_LOGIN_ID") == "")
				return false;
			else
				return true;
		}
		else {
      return false;
    }
  }

  public function getInstance(){
    return Zend_Auth::getInstance();
  }

}

?>
