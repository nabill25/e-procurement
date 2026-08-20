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
    $CI->load->library('libapiui');

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

            // Untuk tipe_user = 9 (Pengguna), cek API SA dan DPSJ
            if ($identity->USER_TYPE_ID == '9') {
              $libapiui = new libapiui();
              echo ($username);
              $dataUser = $this->parsingUser($libapiui->getUserOrg(),$username); 
              $identity->KODE_SA = $dataUser['kode_sa'];
              $identity->KODE_DPSJ = $dataUser['kode_dpsj'];
            } else {
              $identity->KODE_SA = '';
              $identity->KODE_DPSJ = '';
            }
            // End Untuk tipe_user = 9 (Pengguna), cek API SA dan DPSJ

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
            $identity->LEVEL_KONTRAK = $users->getField("LEVEL_KONTRAK");
            $identity->PENUNJUK_PIC = $users->getField("PENUNJUK_PIC");
            $identity->LEVEL_PENGGUNA = $users->getField("LEVEL_PENGGUNA");
            $identity->KASI_PENGGUNA = $users->getField("KASI_PENGGUNA");

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

  public function reloadlocalAuthenticate($userloginid) {
    $auth = Zend_Auth::getInstance();
    $auth->clearIdentity();

    $CI =& get_instance();
    $CI->load->library('libapiui');

    $CI->load->model("Users");
    $CI->load->model("UserType");

    $users = new Users();
    $users->selectById($userloginid);
    $users->firstRow();
     
    $identity = new stdClass();
    $identity->USER_LOGIN_ID = $users->getField("USER_LOGIN_ID");
    $identity->USER_LOGIN = $users->getField("USER_LOGIN");
    $identity->USER_STATUS = $users->getField("USER_STATUS");
    $identity->USER_NAMA = $users->getField("USER_NAMA");
    $identity->USER_TYPE_ID = $users->getField("USER_TYPE_ID");

    // Untuk tipe_user = 9 (Pengguna), cek API SA dan DPSJ
    if ($identity->USER_TYPE_ID == '9') {
      $libapiui = new libapiui();
      $dataUser = $this->parsingUser($libapiui->getUserOrg(),$username); 
      $identity->KODE_SA = $dataUser['kode_sa'];
      $identity->KODE_DPSJ = $dataUser['kode_dpsj'];
    } else {
      $identity->KODE_SA = '';
      $identity->KODE_DPSJ = '';
    }
    // End Untuk tipe_user = 9 (Pengguna), cek API SA dan DPSJ

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
    $identity->LEVEL_KONTRAK = $users->getField("LEVEL_KONTRAK");
    $identity->PENUNJUK_PIC = $users->getField("PENUNJUK_PIC");
    $identity->LEVEL_PENGGUNA = $users->getField("LEVEL_PENGGUNA");
    $identity->KASI_PENGGUNA = $users->getField("KASI_PENGGUNA");

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
 
  }



  public function localAuthenticateSSO($username,$token) {
    $auth = Zend_Auth::getInstance();
    $auth->clearIdentity();

    $CI =& get_instance();
    $CI->load->library('libapiui');
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
            
            // Untuk tipe_user = 9 (Pengguna), cek API SA dan DPSJ
            if ($identity->USER_TYPE_ID == '9') {
              $libapiui = new libapiui();
              $dataUser = $this->parsingUser($libapiui->getUserOrg(),$username); 
              $identity->KODE_SA = $dataUser['kode_sa'];
              $identity->KODE_DPSJ = $dataUser['kode_dpsj'];
            } else {
              $identity->KODE_SA = '';
              $identity->KODE_DPSJ = '';
            }
            // End Untuk tipe_user = 9 (Pengguna), cek API SA dan DPSJ

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
            $identity->LEVEL_KONTRAK = $users->getField("LEVEL_KONTRAK");
            $identity->PENUNJUK_PIC = $users->getField("PENUNJUK_PIC");
            $identity->LEVEL_PENGGUNA = $users->getField("LEVEL_PENGGUNA");
            $identity->KASI_PENGGUNA = $users->getField("KASI_PENGGUNA");

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

  public function parsingUser($data='',$username)
  {
    // echo "<pre>"; print_r($data->result);
    $hasil = [];
    $depan = [];
    $belakang = [];
    foreach ($data->result as $row) {
      if ($row->username == $username && $row->rolename == 'operator_prodi') {
        // echo $row->unit_code.'<br>';
        //   $hasil['unit_code'] = $row->unit_code; 
        list($a, $b) = explode('.', $row->unit_code);
        $depan[] = '\''.$a.'\'';
        $belakang[] = '\''.$b.'\'';
      }
      if ($row->username == $username && $row->rolename == 'operator_fakultas') {
        $hasil['kode_sa'] = '\''.$row->unit_code.'\'';
        $hasil['kode_dpsj'] = '\''.'%%'.'\'';
        return $hasil;
      }
    }

    $hasil['kode_sa'] = implode(',', $depan);
    $hasil['kode_dpsj'] = implode(',', $belakang);

    return $hasil; // hasil berupa array (bisa kosong atau berisi data)
  }

}

?>
