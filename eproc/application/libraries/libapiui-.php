<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */


class libapiui
{
  private $_CI;

  function __construct()
  {
    $this->_CI =& get_instance();
    $this->_CI->load->library('kauth');
    $this->_CI->USER_LOGIN_ID   =  $this->_CI->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
    $this->_CI->USER_LOGIN      =  $this->_CI->kauth->getInstance()->getIdentity()->USER_LOGIN;
    $this->_CI->USER_NAMA       =  $this->_CI->kauth->getInstance()->getIdentity()->USER_NAMA;
    $this->_CI->USER_TYPE_ID    =  $this->_CI->kauth->getInstance()->getIdentity()->USER_TYPE_ID;
    $this->_CI->LEGAL           =  $this->_CI->kauth->getInstance()->getIdentity()->LEGAL;
    $this->_CI->ID              =  $this->_CI->kauth->getInstance()->getIdentity()->REKANAN_ID;
    $this->_CI->HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
    $this->_CI->TOKEN = 'ZXByb2d8TnF1N2xydGJv';
  }

  function getRUP($url='https://sirup.ui.ac.id/apiEproc/getAll')
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        // 'Token: '.$this->_CI->TOKEN,
        'Cookie: session_id=5bce53515bda14315f1dc0487fc38ff0004d45f6'
      ),
    ));

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        $error_msg = curl_error($curl);
    }
    curl_close($curl);

    if (isset($error_msg)) {
      echo $error_msg;
        // TODO - Handle cURL error accordingly
    }

    $decode_json = json_decode($response, false);
    return $decode_json;
    // echo $response->results;

  }

  function getPR($tahun,$kode_sa)
  {
    if ($tahun && $kode_sa) {
      $url = 'https://planning.ui.ac.id/rest/purchasing.php?method=list_pr';
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => $url.'&tahun='.$tahun.'&kode_sa='.$kode_sa,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
          // 'Token: '.$this->_CI->TOKEN,
          'Cookie: session_id=5bce53515bda14315f1dc0487fc38ff0004d45f6'
        ),
      ));

      $response = curl_exec($curl);

      if (curl_errno($curl)) {
          $error_msg = curl_error($curl);
      }
      curl_close($curl);

      if (isset($error_msg)) {
        echo $error_msg;
          // TODO - Handle cURL error accordingly
      }

      $decode_json = json_decode($response, false);
      return $decode_json;
    } else {
      return false;
    }

  }

  function getAttachment($requisition_header_id)
  {
    if ($requisition_header_id) {
      $url = 'https://planning.ui.ac.id/rest/purchasing.php?method=get_attachment';
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => $url.'&requisition_header_id='.$requisition_header_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
          // 'Token: '.$this->_CI->TOKEN,
          'Cookie: session_id=5bce53515bda14315f1dc0487fc38ff0004d45f6'
        ),
      ));

      $response = curl_exec($curl);

      if (curl_errno($curl)) {
          $error_msg = curl_error($curl);
      }
      curl_close($curl);

      if (isset($error_msg)) {
        echo $error_msg;
          // TODO - Handle cURL error accordingly
      }

      $decode_json = json_decode($response, false);
      return $decode_json;
    } else {
      return false;
    }

  }

}
