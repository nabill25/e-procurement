<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */


class libchat
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
    $this->_CI->HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
  }

  function kontrak($contractingrekananid)
  {
    include_once("functions/encrypt.func.php");

    $html = ''; 
    $this->_CI->load->model(array("Contracting","Paketpemenang"));
    $this->_CI->load->library("paketinfo"); 

    $paketInfo = new paketinfo();
    $kontrak = new Contracting();
    $getpaket_pemenang = new Paketpemenang();

    $kontrak->selectByParams(array("A.CONTRACTINGREKANANID" => $contractingrekananid));
    $kontrak->firstRow();
    $kontrak_nama = $kontrak->getField('NAMA');
    $kontrak_nilai = $kontrak->getField('NILAI');
    $kontrak_paket_metode_lelang = $kontrak->getField('PAKET_METODE_LELANG');
    $paket_pemenang = $kontrak->getField('PEMENANG');
    $paket_id = $kontrak->getField('PAKET_ID');

    $paketInfo->getPaket($paket_id);
    $bidding = $paketInfo->bidding;
    $reqMultiPemenang = $paketInfo->multi_pemenang;

    if ($reqMultiPemenang == '0') {
      $getpaket_pemenang->selectByParams(array("PAKET_ID" => $paket_id, "PERINGKAT" => '1'), -1, -1); 
    } else {
      $getpaket_pemenang->selectByParams(array("PAKET_ID" => $paket_id), -1, -1);
      $totalPemenang = $getpaket_pemenang->countRow();
    }

    while($getpaket_pemenang->nextRow())
    { 
      // $enn = encryptIkn($getpaket_pemenang->getField("REKANAN_ID"));
      $enn = $getpaket_pemenang->getField("REKANAN_ID");
      $reqRekananArr[] = $enn;
    } 
      $reqRekanan = implode('||||||',$reqRekananArr);

      $html .= '<style type="text/css"> .wafixed { position: fixed; left: 30px; bottom: 30px; z-index: 999; }</style>';
      $html .= '  <a onclick="openAdd(\'main/loadUrl/main/chatting?reqRekananId='.$reqRekanan.'&reqPaketId='.$paket_id.'\')" class="wafixed btn round btn-min-width box-shadow-1 btn-success btn-sm" style="color:#fff"> 
                    <i class="fa fa-comments-o fa-2x"></i> Kirim Pesan
                  </a>'; 

    return $html;

  }

  function kontrakPenyedia($contractingrekananid)
  {
    include_once("functions/encrypt.func.php");

    $html = ''; 
    $this->_CI->load->model(array("Contracting","Paketpemenang"));
    $this->_CI->load->library("paketinfo"); 

    $paketInfo = new paketinfo();
    $kontrak = new Contracting();
    $getpaket_pemenang = new Paketpemenang();

    $kontrak->selectByParams(array("A.CONTRACTINGREKANANID" => $contractingrekananid));
    $kontrak->firstRow();
    $kontrak_nama = $kontrak->getField('NAMA');
    $kontrak_nilai = $kontrak->getField('NILAI');
    $kontrak_paket_metode_lelang = $kontrak->getField('PAKET_METODE_LELANG');
    $paket_pemenang = $kontrak->getField('PEMENANG');
    $paket_id = $kontrak->getField('PAKET_ID');

    $html = ''; 

      $html .= '<style type="text/css"> .wafixed { position: fixed; left: 30px; bottom: 30px; z-index: 999; }</style>';

    $html .= '
    <script type="text/javascript"> 
    </script>
    ';

    $html .= '<a onClick="openPopupChatSmall(\'main/loadUrl/main/kontrak_chat_rekanan?reqPaketId='.$paket_id.'\',\'Chat Room\')" class="wafixed btn round btn-min-width box-shadow-1 btn-success btn-sm" style="color:#fff !important" ><i class="fa fa-comments-o fa-2x"></i></a>';

    return $html;

  }

  function auction($paket_id)
  {
    include_once("functions/encrypt.func.php");
    $this->_CI->load->model("PaketRekanan");

    $html = ''; 
    $paket_rekanan = new PaketRekanan();
    $paket_rekanan->selectUrutPenawaran(array("A.PAKET_ID" => $paket_id, "A.LULUS_PENDAFTARAN" => 1, "A.KIRIM_PENAWARAN" => 1, "A.LULUS_PENAWARAN" => 1), -1, -1, "", "", " ORDER BY NILAI_PENAWARAN ASC, KIRIM_PENAWARAN_TANGGAL ASC ");
     $urut=1; 

    while($paket_rekanan->nextRow())
    { 
      // $enn = encryptIkn($paket_rekanan->getField("REKANAN_ID"));
      $enn = $paket_rekanan->getField("REKANAN_ID");
      $reqRekananArr[] = $enn;
    } 
      $reqRekanan = implode('||||||',$reqRekananArr);

      $html .= '<style type="text/css"> .wafixed { position: fixed; left: 30px; bottom: 30px; z-index: 999; }</style>';
      $html .= '  
      <a href="#code" data-toggle="modal" onclick="openAdd22(\'main/loadUrl/main/chattingAuction?reqRekananId='.$reqRekanan.'&reqPaketId='.$paket_id.'\')" class="wafixed btn round btn-min-width box-shadow-1 btn-success btn-sm" style="color:#fff"> 
                    <i class="fa fa-comments-o fa-2x"></i> Kirim Pesan
                  </a>'; 

    return $html;

  }

}
