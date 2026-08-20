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

  function getPRByRUP($tahun,$kode_rup)
  {
    if ($tahun && $kode_rup) {
      $url = 'https://planning.ui.ac.id/rest/purchasing.php?method=list_pr_kode_rup';
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => $url.'&tahun='.$tahun.'&kode_rup='.$kode_rup,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
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

  function getUserOrg($url='https://sirup.ui.ac.id/apiEproc/getUserOrg')
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
    }

    $decode_json = json_decode($response, false);
    return $decode_json;
    // echo $response->results;

  }


  // function postEsignPengajuan($url='https://esign.ui.ac.id/api/esign/pengajuan')
  function postEsignPengajuan($fileName,$noSuratUniq,$url='https://esign.ui.ac.id/api/esign/pengajuan')
  {
    $pathFile = FCPATH.'uploads/permohonan_paket/'.$fileName;

    if (!file_exists($pathFile)) {
        die("FILE TIDAK ADA : $pathFile");
    }

    $curl = curl_init();

    $verbose = fopen('php://temp', 'w+'); // untuk capture debug

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'file'=> new CURLFILE($pathFile),
            'IdJenisDokumenOrganisasi' => '190',
            'NomorSurat' => '\''.$noSuratUniq.'\'',
            'SourceApk' => 'eproc',
            'PenggunaEntry' => 'akmal.gafar81'
        ],
        CURLOPT_HTTPHEADER => [
            'X-ApkSource: eproc',
            'X-SecretKey: 1asd3314@ersf'
        ],
        CURLOPT_VERBOSE => true,
        CURLOPT_STDERR => $verbose,
    ]);

    $response = curl_exec($curl);

    $err = curl_error($curl);
    $info = curl_getinfo($curl);

    curl_close($curl);

    rewind($verbose);
    $debug = stream_get_contents($verbose);

    // echo "<pre>";
    // echo "HTTP CODE:\n";
    // print_r($info['http_code']);

    // echo "\nCURL ERROR:\n";
    // print_r($err);

    // echo "\nDEBUG LOG:\n";
    // print_r($debug);

    // echo "\nRESPONSE:\n";
    // print_r($response);
    // echo "</pre>";

    $decode_json = json_decode($response, false);
    // echo "<pre>"; print_r($decode_json); die;
    return $decode_json;
    // echo $response->results;

  }

  function postEsignCekStatus($id,&$fileName)
  {
    $url = 'https://esign.ui.ac.id/api/esign/get_dokumen_status/id/'.$id;

    $curl = curl_init();
    $verbose = fopen('php://temp', 'w+'); // untuk capture debug

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
        'X-ApkSource: eproc',
        'X-SecretKey: 1asd3314@ersf'
      ),
        CURLOPT_VERBOSE => true,
        CURLOPT_STDERR => $verbose,
    ));

    $response = curl_exec($curl);

    $err = curl_error($curl);
    $info = curl_getinfo($curl);

    curl_close($curl);

    rewind($verbose);
    $debug = stream_get_contents($verbose);

    // echo "\nRESPONSE:\n";
    // print_r($response);
    // echo "</pre>";

    $decode_json = json_decode($response, false);
    $fileName = $decode_json->data->file_dokumen;

    if ($decode_json->data->status == 'Selesai') {
      $this->postEsignDownloadById($id,$decode_json->data->file_dokumen);
    }
    // echo $decode_json->data->file_dokumen;
    // echo "<pre>"; print_r($decode_json); die;

    return $decode_json;
    // echo $response->results;

  }

  function postEsignDownloadById($id,$fileName)
  {
    $url = 'https://esign.ui.ac.id/api/esign/get_dokumen?tipe=dokumen&field=id&value='.$id;

    $savePath = FCPATH . 'uploads/permohonan_paket/'.$fileName; // lokasi penyimpanan

    if (file_exists(FCPATH . 'uploads/permohonan_paket/'.$fileName)) {
      return true;
    }
    else { 
      $curl = curl_init();
      $verbose = fopen('php://temp', 'w+'); // untuk capture debug 

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
          'X-ApkSource: eproc',
          'X-SecretKey: 1asd3314@ersf'
        ),
          CURLOPT_VERBOSE => true,
          CURLOPT_STDERR => $verbose,
      ));

      $response = curl_exec($curl);

      $err = curl_error($curl);
      $info = curl_getinfo($curl);

      curl_close($curl);

      rewind($verbose);
      $debug = stream_get_contents($verbose);

      file_put_contents($savePath, $response); 
      $decode_json = json_decode($response, false);
      return $decode_json;
      // echo $response->results;
    }
  }

  function getPRLines($kode_sa,$pr_line)
  {
    if ($kode_sa && $pr_line) {
      $url = 'https://planning.ui.ac.id/rest/purchasing.php?method=list_pr_line';
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => $url.'&kode_sa='.$kode_sa.'&pr_number='.$pr_line,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
          // 'Token: '.$this->_CI->TOKEN,
          // 'Cookie: session_id=5bce53515bda14315f1dc0487fc38ff0004d45f6'
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
