<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

class libsession
{
    private $_CI;

    function __construct()
    {
        $this->_CI =& get_instance();
        $this->_CI->load->library('kauth');

        // USER_TYPE_ID
        // 1   ADMIN
        // 2   VALIDATOR
        // 3   PANITIA
        // 6   PENYEDIA
        // 7   KEPALA PENGADAAN
        // 9   PENGGUNA
        // 10  AUDIT

        $this->_CI->USER_TYPE_ID =  $this->_CI->kauth->getInstance()->getIdentity()->USER_TYPE_ID;
        $this->_CI->URL1 = str_replace("?", "", $this->_CI->uri->segment(1, ""));
        $this->_CI->URL2 = $this->_CI->uri->segment(2, "");
        $this->_CI->URL3 = $this->_CI->uri->segment(3, "");
        $this->_CI->URL4 = $this->_CI->uri->segment(4, "");
    }

    function cekSession($paketid=null)
    {

      // cek role
      if ($paketid != 'free' && $paketid != 'blockpenyedia') {
        if ($this->_CI->URL3 == 'report') {
          $url = $this->_CI->URL4;
        } else {
          $url = $this->_CI->URL3;
        }
        //print "masuk";die;
        $this->_CI->load->model("Users");
        $users = new Users();
        $cek = $users->selectAksesMenuByType($url,$this->_CI->USER_TYPE_ID);
        if ($cek == 0) {
          $ins = $this->insertLogs();
          if ($ins) {
             redirect(base_url().'main/index/403');
          }
        }
      }
      /*
        $this->libsession->cekSession(parameter);
        parameter:
        1. paket_id : hanya untuk halaman tertentu penyedia yang ikut dan atau di undang paket dan untuk panitia dan anggota yang berhak buka halaman
        2. blockpenyedia : hanya untuk halaman penyedia yang belum di verifikasi (status selain 1)
        3. free : hanya untuk halaman public
      */

      // jika paketid tersedia dan bukan free,blockpenyedia
      if ($paketid && $paketid != 'free' && $paketid != 'blockpenyedia') {
        if ($this->_CI->USER_TYPE_ID == 10) { // tidak untuk audit bebas akses
          return true;
        } else if ($this->_CI->USER_TYPE_ID == 3 || $this->_CI->USER_TYPE_ID == 11) { // Panitia: hanya untuk halaman panitia dan anggota
          $this->_CI->load->model("PaketPanitia");
          $paket_panitia = new PaketPanitia();
          $cekPanitia = $paket_panitia->getCountByParams2(array("A.PAKET_ID" => $paketid, "A.USER_LOGIN_ID" => $this->_CI->USER_LOGIN_ID));
          if ($cekPanitia > 0) { // user adalah panitia/anggota
            return true;
          } else { // user bukan panitia/anggota
            redirect(base_url().'main/index/403');
          }
        } else if ($this->_CI->USER_TYPE_ID == 6) { // Penyedia: hanya untuk halaman tertentu penyedia yang ikut dan atau di undang paket
          $this->_CI->load->model("PaketRekanan");
          $paket_rekanan_check = new PaketRekanan();
          $check = $paket_rekanan_check->getCountByParams(array("PAKET_ID" => $paketid, "REKANAN_ID" => $this->_CI->REKANAN_ID, "LULUS_PENDAFTARAN" => "1"));
          // echo $check.'----'; die();
          if($check == 0)
          {
            $ins = $this->insertLogs();
            if ($ins) {
              redirect(base_url().'main/index/403');
            }
          }
        }
      }
      // end jika paketid tersedia dan bukan free,blockpenyedia

      //  hanya untuk halaman penyedia yang belum di verifikasi
      if ($paketid == 'blockpenyedia') {
        $this->_CI->load->model("UserLogin");
        $user_login = new UserLogin();
        $adaKelengkapanData = $user_login->getCountByParams(array("USER_LOGIN_ID"=> $this->_CI->USER_LOGIN_ID,"USER_STATUS|| IN " => "(0)")); // 0 = Baru/ Revisi, 1 = valid , 2- Berkas disubmit

        $this->_CI->load->model("Rekanan");
        $rekanan = new Rekanan();
        $rekanan->selectByParams(array("A.REKANAN_ID"=> $this->_CI->REKANAN_ID));
        $rekanan->firstRow();
        $status_validasi = $rekanan->getField('STATUS_VALIDASI');

        if (!$this->_CI->kauth->getInstance()->hasIdentity())
        { // belum login
          $ins = $this->insertLogs();
          if ($ins) {
            redirect(base_url().'main/index/403');
          }
        }

        // 0=Belum 1=Validasi 2=Hapus 3=Kirim ke Rekomendator, 4=Kirim ke Validator, 10=Tolak

        // echo 'Rekanan_id:'.$this->_CI->REKANAN_ID.'- status_validasi:'.$status_validasi.'---'; die();
        // if ($adaKelengkapanData == 1) { // belum verifikasi
        // if ($adaKelengkapanData == 0) { // sudah verifikasi
        // $arrStatus = array('1','2','3','4'); // OLD
        $arrStatus = array('1','2','3');
        $arrStatusUser = array('1','2');
        if (in_array($status_validasi,$arrStatus)) { // status table rekanan
          $this->_CI->load->model("Userlogin");
          $userlogin = new Userlogin();
          $userlogin->selectByParams(array("REKANAN_ID"=> $this->_CI->REKANAN_ID));
          $userlogin->firstRow();
          $user_status = $userlogin->getField('USER_STATUS');

          if (in_array($user_status,$arrStatusUser)) { // status user_login
            $ins = $this->insertLogs();
            if ($ins) {
              redirect(base_url().'main/index/403');
            }
          }
        }
      //  end hanya untuk halaman penyedia yang belum di verifikasi

      //  hanya untuk halaman public
      } else if ($paketid == 'free') {
      //  end hanya untuk halaman public
        return true;
      } else {
        if ($this->_CI->URL3 == 'report') {
          $url = $this->_CI->URL4;
        } else {
          $url = $this->_CI->URL3;
        }
          $this->_CI->load->model("Users");
          $users = new Users();
          $cek = $users->selectAksesMenuByType($url,$this->_CI->USER_TYPE_ID);
          // die();
          // echo $cek.'-'.$this->_CI->URL3.'-'.$this->_CI->USER_TYPE_ID; die();
          if (!$this->_CI->kauth->getInstance()->hasIdentity())
          { // belum login
            $ins = $this->insertLogs();
            if ($ins) {
              redirect(base_url().'main/index/403');
            }
          } else {
            if ($cek == 0) {
              $ins = $this->insertLogs();
              if ($ins) {
                 redirect(base_url().'main/index/403');
              }
            } else {
              return false;
            }
          }
      }
    }

    function cekSessionKualifikasi($paketid=null)
    {
      if ($paketid && $paketid != 'free' && $paketid != 'blockpenyedia') {
        if ($this->_CI->USER_TYPE_ID == 10) { // tidak untuk audit bebas akses
          return true;
        } else if ($this->_CI->USER_TYPE_ID == 3) { // Panitia: hanya untuk halaman panitia dan anggota
          return false;
        } else if ($this->_CI->USER_TYPE_ID == 6) { // Penyedia: hanya untuk halaman tertentu penyedia yang ikut dan atau di undang paket

          // cek untuk tender kualifikasi peserta yang sedang tahap pendaftaran dan lulus saja yang bisa lihat halaman tersebut
          $this->_CI->load->model("PaketRekanan");
          $paket_rekanan_check_kualifikasi = new PaketRekanan();
          $paket_rekanan_check_kualifikasi->selectByParams(array("PAKET_ID" => $paketid, "A.REKANAN_ID" => $this->_CI->REKANAN_ID,),-1,-1," AND LULUS_PENDAFTARAN IN ('0','1','2')");
          $check = $paket_rekanan_check_kualifikasi->countRow();
          // $check = $paket_rekanan_check_kualifikasi->getField('LULUS_PENDAFTARAN');

          // echo $check.'----'; die();
          if($check == 0)
          {
            $ins = $this->insertLogs();
            if ($ins) {
              redirect(base_url().'main/index/403');
            }
          }
        }
      }
    }

    function cekStatusValidasiRekanan()
    {
      $this->_CI->load->model("Rekanan");
      $this->_CI->load->model("Userlogin");
      $rekanan = new Rekanan();
      $userlogin = new Userlogin();
      $rekanan->selectByParams(array("A.REKANAN_ID"=> $this->_CI->REKANAN_ID));
      $rekanan->firstRow();
      $status_validasi = $rekanan->getField('STATUS_VALIDASI');
      $userlogin->selectByParams(array("REKANAN_ID"=> $this->_CI->REKANAN_ID));
      $userlogin->firstRow();
      $user_status = $userlogin->getField('USER_STATUS');

      $paramRet[] = $status_validasi;
      $paramRet[] = $user_status;

      return $paramRet;
    }

    function cekStatusValidasiRekananStr()
    {
      $this->_CI->load->model("Rekanan");
      $this->_CI->load->model("Userlogin");
      $rekanan = new Rekanan();
      $userlogin = new Userlogin();
      $rekanan->selectByParams(array("A.REKANAN_ID"=> $this->_CI->REKANAN_ID));
      $rekanan->firstRow();
      $status_validasi = $rekanan->getField('STATUS_VALIDASI');
      $html = '';
      // 0=Belum 1=Validasi 2=Hapus 3=Kirim ke Rekomendator, 4=Kirim ke Validator, 10=Tolak
      switch ($status_validasi) {
        case '1':
          $html .= '<div class="alert alert-danger">
                      <button type="button" class="close" data-dismiss="alert">&times;</button>
                      <b><u>Data sudah terverifikasi jika ada perubahan, silahkan hubungi verifikator/validator. </u></b>
                    </div>';
          break;

        default:
          break;
      }
          return $html;
    }

    function cekUrl($url)
    {
      // sebelum dibuka akses, cek dulu URL allowed untuk perubahan data
      // karena setiap halaman diberi akses untuk cegatan jika ingin di ubah
      $this->_CI->load->model("Rekananurlvalidasiallow");
      $rekananurlvalidasi = new Rekananurlvalidasiallow();
      $rekananurlvalidasi->selectByParamsURL(array("URL"=> $url));
      $rekananurlvalidasi->firstRow();
      $idUrl = $rekananurlvalidasi->getField('ID');

      $rekananurlvalidasiallow = new Rekananurlvalidasiallow();
      $rekananurlvalidasiallow->selectByParamsAllow(array("REKANAN_ID"=> $this->_CI->REKANAN_ID, "URL" => $idUrl));

      if ($rekananurlvalidasiallow->countRow() > 0 ) {
        // cek apakah masih proses verifikasi atau tidak
        // 0=Belum 1=Validasi 2=Hapus 3=Kirim ke Rekomendator, 4=Kirim ke Validator, 10=Tolak
        $this->_CI->load->model("Rekanan");
        $rekanan = new Rekanan();
        $rekanan->selectByParams(array("A.REKANAN_ID"=> $this->_CI->REKANAN_ID));
        $rekanan->firstRow();
        $status_validasi = $rekanan->getField('STATUS_VALIDASI');
        // if ($status_validasi == '1' || $status_validasi == '3' || $status_validasi == '4') {
        if ($status_validasi == '3' || $status_validasi == '4') {
          return false;
        } else {
          return true;
        }
      } else {
        return false;
      }

    }

    function cekChecklist($field)
    {
      // cek checklist verifikator
      $this->_CI->load->model("Rekanan");

      $cekData = new Rekanan();
      $cekData->selectByParamsRekananChecklist(array("REKANANID"=>$this->_CI->REKANAN_ID),-1,-1);
      $cekData->firstRow();

      if ($cekData->getField("$field") == '1') {
        return false;
      } else {
        return true;
      }

    }

    function cekSessionKontrak($contractingrekananid=null)
    {

      // cek role
        if ($this->_CI->URL3 == 'report') {
          $url = $this->_CI->URL4;
        } else {
          $url = $this->_CI->URL3;
        }
        // echo $url;
        $this->_CI->load->model("Users");
        $users = new Users();
        $cek = $users->selectAksesMenuByType($url,$this->_CI->USER_TYPE_ID);
        if ($cek == 0) {
          $ins = $this->insertLogs();
          if ($ins) {
             redirect(base_url().'main/index/403');
          }
        }

      // jika paketid tersedia dan bukan free,blockpenyedia
      if ($contractingrekananid) {
        $sesRekananId = $this->_CI->REKANAN_ID;
        $this->_CI->load->model("Contractingrekanan");
        $contractingrekanan = new Contractingrekanan();

        $contractingrekanan->selectByParams(array("A.CONTRACTINGREKANANID" => $contractingrekananid));
        // $contractingrekanan->firstRow();
        while($contractingrekanan->nextRow())
        {
        $rekananid[] = $contractingrekanan->getField('REKANAN_ID');

        }
        // echo "<pre>"; print_r($rekananid); die;
        // if ($rekananid == $sesRekananId) {
        if (in_array($sesRekananId,$rekananid)) { // status table rekanan
        } else {
          redirect(base_url().'main/index/403');
        }
      } else {
        $ins = $this->insertLogs();
        if ($ins) {
          redirect(base_url().'main/index/404');
        }
      }
    }

    function cekSessionKontrakPPK($contractingrekananid=null)
    {
      // jika paketid tersedia dan bukan free,blockpenyedia
      if ($contractingrekananid) {
        $sesUserID = $this->_CI->USER_LOGIN_ID;
        $sesTypeUser = $this->_CI->USER_TYPE_ID;
        $sesLevelKontrak = $this->_CI->LEVEL_KONTRAK;
        $sesKasi = $this->_CI->PENUNJUK_PIC;
        $this->_CI->load->model("Contractingrekanan");
        $contractingrekanan = new Contractingrekanan();

        $contractingrekanan->selectByParams(array("A.CONTRACTINGREKANANID" => $contractingrekananid));
        $contractingrekanan->firstRow();
        // $ppk = $contractingrekanan->getField('PPK');
        $ppk = $contractingrekanan->getField('PIC_KONTRAK');
        // echo $ppk.'-'.$sesUserID; die;
        if ($ppk == $sesUserID || $sesTypeUser == '20' || $sesTypeUser == '28' || ($sesTypeUser=='12' && $sesLevelKontrak =='2') || ($sesTypeUser=='12' && $sesLevelKontrak =='3') || ($sesTypeUser=='12' && $sesKasi =='1') ) { // Jika User adalah PPK nya atau 20:PEMERIKSA KONTRAK, 28:PPK dan Kasi
        } else {
          redirect(base_url().'main/index/403');
        }
      } else {
        $ins = $this->insertLogs();
        if ($ins) {
          redirect(base_url().'main/index/404');
        }
      }
    }

    function insertLogs()
    {
      $this->_CI->load->model("Tblmlogs");
      $tblmlogs = new Tblmlogs();
      // macam2 error halaman https://ruanglaptop.com/kode-error-internet/
      $tblmlogs->setField("LOGSNAME", '403||ACCESS DENIED/FORBIDDEN||'.$this->_CI->URL3);
      $tblmlogs->setField("LOGSKET", $this->infoServer());
      $tblmlogs->setField("DATETIME", date('Y-m-d H:i:s'));
      $ins = $tblmlogs->insert();
      if ($ins) {
        return true;
      } else {
        return false;
      }
    }

    function infoServer()
    {
        $indicesServer = array('PHP_SELF',
                              'argv',
                              'argc',
                              'GATEWAY_INTERFACE',
                              'SERVER_ADDR',
                              'SERVER_NAME',
                              'SERVER_SOFTWARE',
                              'SERVER_PROTOCOL',
                              'REQUEST_METHOD',
                              'REQUEST_TIME',
                              'REQUEST_TIME_FLOAT',
                              'QUERY_STRING',
                              'DOCUMENT_ROOT',
                              'HTTP_ACCEPT',
                              'HTTP_ACCEPT_CHARSET',
                              'HTTP_ACCEPT_ENCODING',
                              'HTTP_ACCEPT_LANGUAGE',
                              'HTTP_CONNECTION',
                              'HTTP_HOST',
                              'HTTP_REFERER',
                              'HTTP_USER_AGENT',
                              'HTTPS',
                              'REMOTE_ADDR',
                              'REMOTE_HOST',
                              'REMOTE_PORT',
                              'REMOTE_USER',
                              'REDIRECT_REMOTE_USER',
                              'SCRIPT_FILENAME',
                              'SERVER_ADMIN',
                              'SERVER_PORT',
                              'SERVER_SIGNATURE',
                              'PATH_TRANSLATED',
                              'SCRIPT_NAME',
                              'REQUEST_URI',
                              'PHP_AUTH_DIGEST',
                              'PHP_AUTH_USER',
                              'PHP_AUTH_PW',
                              'AUTH_TYPE',
                              'PATH_INFO',
                              'ORIG_PATH_INFO') ;

        $html = '<table class="table">' ;
        foreach ($indicesServer as $arg) {
          if (isset($_SERVER[$arg])) {
              $html .= '<tr><td>'.$arg.'</td><td>' . $_SERVER[$arg] . '</td></tr>' ;
          }
          else {
              $html .= '<tr><td>'.$arg.'</td><td>-</td></tr>' ;
          }
        }
        $html .= '</table>' ;

        return $html;
    }
}
