<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

class libgeneratecode
{
  private $_CI;

  function __construct()
  {
    $this->_CI =& get_instance();
    $this->_CI->load->library('kauth');
    $this->_CI->ID              =  $this->_CI->kauth->getInstance()->getIdentity()->REKANAN_ID;
    $this->_CI->USER_LOGIN_ID   =  $this->_CI->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
    $this->_CI->USER_LOGIN      =  $this->_CI->kauth->getInstance()->getIdentity()->USER_LOGIN;
    $this->_CI->USER_NAMA       =  $this->_CI->kauth->getInstance()->getIdentity()->USER_NAMA;
    $this->_CI->USER_TYPE_ID    =  $this->_CI->kauth->getInstance()->getIdentity()->USER_TYPE_ID;
    $this->_CI->LEGAL           =  $this->_CI->kauth->getInstance()->getIdentity()->LEGAL;
    $this->_CI->NIP             =  $this->_CI->kauth->getInstance()->getIdentity()->NIP;
    $this->_CI->HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
  }

  function bahp($reqId,$metode,$nip)
  {
    include_once("functions/encrypt.func.php");

    $html = ''; 
    $this->_CI->load->model(array("Contracting","Paketpemenang","Queryfree"));
    $this->_CI->load->library("paketinfo"); 

    $paketInfo = new paketinfo();
    $paketInfo->getPaket($reqId);
    $bidding = $paketInfo->bidding;
    $reqMultiPemenang = $paketInfo->multi_pemenang;
    $reqMetodeLelangId = $paketInfo->metode_lelang_id;
    $reqMetodeLelangNama = $paketInfo->metode_lelang_nama;
    $reqJenisId = $paketInfo->jenis_id;
    // return $reqId.'/BAHP/'.$this->textElektronik($reqMetodeLelangId).'/EPROC/'.$this->textJenis($reqJenisId).'/'.$this->textBulanRomawi(date('m')).'/'.date('Y');

    switch ($metode) {
      case '1': // Tender
      case '3': // Tender Terbatas
      case '4': // Seleksi
      case '5': // Penunjukan Langsung
      case '7': // Tender Cepat
      case '8': // Kontes
      case '10': // Tender Kualifikasi
        $getPokja = new Queryfree();
        $getPokja->selectByParams("SELECT * FROM SK_PANITIA A JOIN PANITIA B ON A.SK_PANITIA_ID=B.SK_PANITIA_ID 
                                  WHERE NIP = '".$nip."' LIMIT 1");
        $getPokja->firstRow();
        return "BA-".$reqId."/UN2.PMP.".strtoupper($getPokja->getField('UNIT_KERJA'))."/LOG.01/".date('Y');
        break; 

      case '2': // Pengadaan Langsung
      case '11': // Penunjukan Langsung Khusus
        return "BA-".$reqId."/PJP/LOG.01/".date('Y');
        break; 

      default:
        return "-";
        break;
    }


  }

  function nomorPaket($reqId,$metode)
  {
    include_once("functions/encrypt.func.php");
    include_once("functions/string.func.php");

    $html = ''; 
    $this->_CI->load->library("paketinfo"); 

    $paketInfo = new paketinfo();
    $paketInfo->getPaket($reqId);
    $bidding = $paketInfo->bidding;
    $reqMultiPemenang = $paketInfo->multi_pemenang;
    $reqMetodeLelangId = $paketInfo->metode_lelang_id;
    $reqMetodeLelangNama = $paketInfo->metode_lelang_nama;
    $reqJenisId = $paketInfo->jenis_id;
    $reqTanggal = $paketInfo->tanggal;
    $exTanggal = explode(' ',$reqTanggal);
    $exTanggal2 = explode('-',$exTanggal[0]);

    return generateZero($reqId, 3, 0).'/'.$this->textElektronik($reqMetodeLelangId).'/EUI/'.$this->textJenis($reqJenisId).'/'.$this->textBulanRomawi($exTanggal2[1]).'/'.$exTanggal2[0];

  }

  private function textElektronik($metode)
  {
    switch ($metode) {
      case '1': // Tender
      case '3': // Tender Terbatas
      case '4': // Seleksi
      case '7': // Tender Cepat
      case '8': // Kontes
      case '10': // Tender Kualifikasi
        $text = 'ETD';
        break; 

      case '2': // Pengadaan Langsung
      case '5': // Penunjukan Langsung
      case '11': // Penunjukan Langsung Khusus
      $text = 'EP';
        break; 

      default:
        $text = 'XX';
        break;
    }

    return $text;
  }

  private function textElektronik2($metode)
  {
    switch ($metode) {
      case '1': // Tender
      case '3': // Tender Terbatas
      case '7': // Tender Cepat
      case '10': // Tender Kualifikasi
        $text = 'ETD';
        break; 

      case '2': // Pengadaan Langsung
      $text = 'EPL';
        break; 

      case '5': // Penunjukan Langsung
      case '11': // Penunjukan Langsung Khusus
      $text = 'EPLBJK';
        break; 

      case '9': // Pembelian Langsung Offline
      $text = 'PLO';
        break; 

      case '12': // e-Purchasing Pemerintah
      $text = 'KP';
        break; 

      $text = 'ETD';
        break; 


      default:
        $text = 'XX';
        break;
    }

    return $text;
  }

  private function textJenis($jenis)
  {
    switch ($jenis) {
      case '1': // Pekerjaan Konstruksi
        $text = 'F';
        break;

      case '2': // Jasa Konsultansi
        $text = 'JK';
        break; 

      case '3': // Barang
        $text = 'B';
        break; 

      case '4': // Jasa Lainnya
        $text = 'JL';
        break; 
      
      default:
        $text = '';
        // code...
        break;
    }

    return $text;
  }

  private function textBulanRomawi($bulan)
  {
    $arrMonth = array("01"=>"I", "02"=>"II", "03"=>"III", "04"=>"IV", "05"=>"V",
                "06"=>"VI", "07"=>"VII", "08"=>"VIII", "09"=>"IX", "10"=>"X",
                "11"=>"XI", "12"=>"XII");
    return $arrMonth[$bulan];
  }

}
