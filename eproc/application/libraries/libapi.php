<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */


class libapi
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
    // $this->_CI->TOKEN2 = 'YWRtaW58YWRtaW4=';
  }

  // function getAnggaran()
  function getAnggaran($url,$tahun_anggaran,$department)
  {
    // echo $url.'___'.$tahun_anggaran.'___'.$department;
    $curl = curl_init();
    $department = str_replace(' ','%20', $department);
    $department = str_replace('&','%26', $department);
    // echo $url.'?token='.$this->_CI->TOKEN.'&tahun_anggaran='.$tahun_anggaran.'&department='.$department;
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url.'?Token='.$this->_CI->TOKEN.'&tahun_anggaran='.$tahun_anggaran.'&department='.$department,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        // 'Token: '.$this->_CI->TOKEN,
        // 'tahun_anggaran: '.$tahun_anggaran,
        // 'department: '.$department,
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
    // echo "<pre>"; print_r($response);
    $decode_json = json_decode($response, false);
    return $decode_json;
    // echo $decode_json;
    // echo $response->results;

  }

  function groupData($val)
  {
    $result = array();
    if ($val) {
      foreach ($val as $element) {
        $result[$element->mata_anggaran][] = $element;
      }
    }
    return $result;
  }

  function getA($url,$tahun_anggaran,$department,$mata_anggaran,$kegiatan)
  {
    $reqUrl = $url;
    $reqDate = $tahun_anggaran;
    $reqDepartment = $department;
    $reqMT = $mata_anggaran;

    $this->_CI->load->library("libapi");
    $libapi = new libapi();
    $a = $libapi->getAnggaran($reqUrl,$reqDate,$reqDepartment);
    $dataMataAnggaran = $a->results->data;
    $arrMataAnggaran = $dataMataAnggaran;

    // Group data dulu
    $libapi2 = new libapi();
    $b = $libapi2->groupData($arrMataAnggaran);
    // End Group data dulu

    // echo "<pre>"; print_r($result);
    $data20 = array();
    foreach ($b as $key => $value) {
      if ($reqMT == $key) {
        $data20[$key] = $value;
      }
    }
    // echo "<pre>"; print_r($data20);
    // $html = '<option>-- Pilih kegiatan --</option>';
    $html = '<option value="">-- Pilih kegiatan --</option>';
    if (count($data20) > 0) {
      foreach ($data20 as $key => $value) {
        foreach ($value as $key2 => $value2) {
          if ($kegiatan == $value2->kegiatan) {
            $selected = " selected";
          } else {
            $selected = "";
          }

          $html .= '<option value="'.$value2->kegiatan.'" data-department-code="'.$value2->department_code.'" data-kode-mata-anggaran="'.$value2->kode_mata_anggaran.'" data-tipe-transaksi="'.$value2->tipe_transaksi.'" data-kode-kegiatan="'.$value2->kode_kegiatan.'" data-sumber-dana="'.$value2->sumber_dana.'" data-total-budget="'.$value2->total_budget.'" data-budget-remaining="'.$value2->budget_remaining.'" '.$selected.'>'.$value2->kegiatan.'</option>';
        }
      }
    }
  // $arrJson["PESAN"] = $reqUrl.'-'.$reqDate.'-'.$reqDepartment.'-'.$reqMT;
  return $html;
  }

  function getDepartment($url)
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url.'?token='.$this->_CI->TOKEN,
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

  // function getPRByRUP()
  function getPRByRUP($url,$kode_rup)
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url.'?token='.$this->_CI->TOKEN.'&kode_rup='.$kode_rup,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        // 'Token: '.$this->_CI->TOKEN,
        // 'kode_rup: '.$kode_rup,
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

  // function getPRByPR()
  function getPRByPR($url,$pr_number)
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url.'?token='.$this->_CI->TOKEN.'&pr_number='.$pr_number,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        // 'Token: '.$this->_CI->TOKEN,
        // 'pr_number: '.$pr_number,
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

  // function getPRAttachmentByPR()
  function getPRAttachmentByPR($url,$pr_number)
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url.'?token='.$this->_CI->TOKEN.'&pr_number='.$pr_number,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        // 'Token: '.$this->_CI->TOKEN,
        // 'pr_number: '.$pr_number,
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


}
