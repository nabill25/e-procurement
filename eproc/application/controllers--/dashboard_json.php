<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

class Dashboard_json extends CI_Controller {

  function __construct() {
    parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

    //kauth
    if (!$this->kauth->getInstance()->hasIdentity())
    {
      // trow to unauthenticated page!
      //redirect('Login');
    }

    /* GLOBAL VARIABLE */
    //$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;

    $this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
    $this->USER_LOGIN_ID =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
    $this->USER_LOGIN =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN;
    $this->USER_NAMA =  $this->kauth->getInstance()->getIdentity()->USER_NAMA;
    $this->USER_TYPE_ID =  $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID;
    $this->REKANAN_ID =  $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
    $this->UNIT_KERJA_ID =  $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID;
    $this->NIP =  $this->kauth->getInstance()->getIdentity()->NIP;
    $this->LOGIN_TIME = $this->kauth->getInstance()->getIdentity()->LOGIN_TIME;
    $this->LOGIN_DATE = $this->kauth->getInstance()->getIdentity()->LOGIN_DATE;
    $this->REKANAN = $this->kauth->getInstance()->getIdentity()->REKANAN;
    $this->REKANAN_KODE = $this->kauth->getInstance()->getIdentity()->REKANAN_KODE;
    $this->REKANAN_PKP = $this->kauth->getInstance()->getIdentity()->REKANAN_PKP;
    $this->REKANAN_NPWP = $this->kauth->getInstance()->getIdentity()->REKANAN_NPWP;
    $this->REKANAN_STATUS_PERUSAHAAN = $this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN;
    $this->REKANAN_STATUS_VALIDASI = $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI;
  }

  function setHPSVal()
  {
    $this->load->model('Queryfree');
    $getDataHPS = new Queryfree();

    $unitkerja = $this->input->get("unitkerja");
    $userloginid = $this->input->get("userloginid");
    $tahun = $this->input->get("tahun");
    $bulan = $this->input->get("bulan");


    if ($tahun != 'all')
    {
      if ($bulan == '101') { // all karana ke deteksi NaN
        $getDataHPS->selectByParams("SELECT * FROM view_dash_efesiensi WHERE unit_kerja_id='".$unitkerja."' AND tahun_anggaran = '".$tahun."' AND (user_login_id = ".$userloginid." OR user_tim_pengadaan && ARRAY[".$userloginid."] ) "
        );
      } else {
        $getDataHPS->selectByParams("SELECT * FROM view_dash_efesiensi WHERE unit_kerja_id='".$unitkerja."' AND tahun_anggaran = '".$tahun."' AND bulan_permohonan = ".$bulan." AND (user_login_id = ".$userloginid." OR user_tim_pengadaan && ARRAY[".$userloginid."] ) "
        );

      }
    } else { // Tahun ALL
      if ($bulan == '101') { // all karana ke deteksi NaN
        $getDataHPS->selectByParams("SELECT * FROM view_dash_efesiensi WHERE unit_kerja_id='".$unitkerja."' AND (user_login_id = ".$userloginid." OR user_tim_pengadaan && ARRAY[".$userloginid."] ) ");
      } else {
        $getDataHPS->selectByParams("SELECT * FROM view_dash_efesiensi WHERE unit_kerja_id='".$unitkerja."' AND bulan_permohonan = ".$bulan." AND (user_login_id = ".$userloginid." OR user_tim_pengadaan && ARRAY[".$userloginid."] ) ");
      }
    }

    // echo $getDataHPS->query; die;
    $html  = '';

    $nilaiHps = 0;
    $nilaiNegosiasi = 0;
    while($getDataHPS->nextRow())
    {
      $nilaiHps += $getDataHPS->getField('nilai');
      $nilaiNegosiasi += $getDataHPS->getField('harga_negosiasi');

    }
    // $getDataHPS->firstRow();
    // $nilaiHps = $getDataHPS->getField('nilai') ?: 0;
    // $nilaiKontrak = $getDataHPS->getField('harga_negosiasi') ?: 0;
    $nilaiEfesiensi = $nilaiHps - $nilaiNegosiasi;
    // $nilaiEfesiensi = $nilaiHps - 22660000000;
    if ($nilaiEfesiensi > 0) {
      $persentaseEfesiensi = round($nilaiEfesiensi/$nilaiHps * 100,2).' %';
      $backColor = ' background-color:#967adc;';
      $ketEfesiensi1 = 'Efesien';
      $ketEfesiensi2 = '<span class="white darken-1 block"><i class="ft-arrow-down white" ></i> '.$persentaseEfesiensi.' dari Harga Perkiraan</span>';
    } else {
      $persentaseEfesiensi = 0;
      $backColor = ' background-color:#da4453;';
      $ketEfesiensi1 = 'Tidak Efesien';
      $ketEfesiensi2 = '<span class="white darken-1 block"><i class="ft-arrow-up white" ></i>  '.$persentaseEfesiensi.' dari Harga Perkiraan</span>';
    }

        $html .= '
        <table class="table table-bordered mb-3">
          <tr>
            <td>
              <div class="float-center pl-2">
                  <span class="grey darken-1 block">Harga Perkiraan</span>
                  <span class="font-large-2 line-height-1 text-bold-300">'.number_format($nilaiHps,'0',',','.').'</span>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="float-center pl-2">
                  <span class="grey darken-1 block">Harga Final/AKhir</span>
                  <span class="font-large-2 line-height-1 text-bold-300">'.number_format($nilaiNegosiasi,'0',',','.').'</span>
              </div>
            </td>
          </tr>
          <tr style="'.$backColor.'">
            <td>
              <div class="float-center pl-2">
                  <span class="white darken-1 block" style="color:#fff">'.$ketEfesiensi1.'</span>
                  <span class="font-large-1 line-height-1 text-bold-300" style="color:#fff">'.number_format($nilaiEfesiensi,'0',',','.').'</span>
                  '.$ketEfesiensi2.'
              </div>
            </td>
          </tr>
          <!-- <small><b>Dalam hitungan rupiah</b></small> -->
        </table>';


    $arr_json = array("message" => $html);

    echo json_encode($arr_json);
  }

  function setHPSValPembelian()
  {
    $this->load->model('Queryfree');
    $getDataHPS = new Queryfree();

    $unitkerja = $this->input->get("unitkerja");
    $userloginid = $this->input->get("userloginid");
    $tahun = $this->input->get("tahun");
    $bulan = $this->input->get("bulan");
    // echo $tahun.'--'.$bulan;
    if ($tahun != 'all'){ // Tahun bukan ALL
      if ($bulan == 'NaN') { // Bulan ALL
        $getDataHPS->selectByParams("SELECT (SELECT sum(nilai) nilai_hps  from view_paket_dashboard
                                 WHERE unit_kerja_id='".$unitkerja."' AND user_login_id = ".$userloginid." AND tahun_anggaran = ".$tahun.") nilai_hps,
                                 (
                                  SELECT (a.ongkos_kirim + a.harga_nego) nilai_kontrak from (
                                      SELECT
                                      (SELECT sum(ongkos_kirim) as ongkos_kirim from katalog_logistik where paket_id in (
                                      SELECT paket_id from view_paket_dashboard where unit_kerja_id='".$unitkerja."' AND user_login_id = ".$userloginid." AND tahun_anggaran = ".$tahun." and paket_metode_lelang_id in (6,9)
                                      )),
                                      (SELECT sum(harga_nego * qty) harga_nego from katalog_rekanan where paket_id in (
                                      SELECT paket_id from view_paket_dashboard where unit_kerja_id='".$unitkerja."' AND user_login_id = ".$userloginid." AND tahun_anggaran = ".$tahun." and paket_metode_lelang_id in (6,9)
                                      ))
                                  ) a
                                 ) nilai_kontrak
                                 "
                                 );
      } else {
        $getDataHPS->selectByParams("SELECT (SELECT sum(nilai) nilai_hps  from view_paket_dashboard
                                 WHERE unit_kerja_id='".$unitkerja."' AND user_login_id = ".$userloginid." AND tahun_anggaran = ".$tahun." AND bulan_permohonan = ".$bulan.") nilai_hps,
                                 (
                                  SELECT (a.ongkos_kirim + a.harga_nego) nilai_kontrak from (
                                      SELECT
                                      (SELECT sum(ongkos_kirim) as ongkos_kirim from katalog_logistik where paket_id in (
                                      SELECT paket_id from view_paket_dashboard where unit_kerja_id='".$unitkerja."' AND user_login_id = ".$userloginid." AND tahun_anggaran = ".$tahun." and paket_metode_lelang_id in (6,9) AND bulan_permohonan = ".$bulan."
                                      )),
                                      (SELECT sum(harga_nego * qty) harga_nego from katalog_rekanan where paket_id in (
                                      SELECT paket_id from view_paket_dashboard where unit_kerja_id='".$unitkerja."' AND user_login_id = ".$userloginid." AND tahun_anggaran = ".$tahun." and paket_metode_lelang_id in (6,9) AND bulan_permohonan = ".$bulan."
                                      ))
                                  ) a
                                 ) nilai_kontrak
                                 "
                                 );
      }
    } else { // Tahun ALL
      if ($bulan == 'NaN') { // Bulan ALL
        $getDataHPS->selectByParams("SELECT (SELECT sum(nilai) nilai_hps  from view_paket_dashboard
                                 WHERE unit_kerja_id='".$unitkerja."' AND user_login_id = ".$userloginid.") nilai_hps,
                                 (
                                  SELECT (a.ongkos_kirim + a.harga_nego) nilai_kontrak from (
                                      SELECT
                                      (SELECT sum(ongkos_kirim) as ongkos_kirim from katalog_logistik where paket_id in (
                                      SELECT paket_id from view_paket_dashboard where unit_kerja_id='".$unitkerja."' AND user_login_id = ".$userloginid." and paket_metode_lelang_id in (6,9)
                                      )),
                                      (SELECT sum(harga_nego * qty) harga_nego from katalog_rekanan where paket_id in (
                                      SELECT paket_id from view_paket_dashboard where unit_kerja_id='".$unitkerja."' AND user_login_id = ".$userloginid." and paket_metode_lelang_id in (6,9)
                                      ))
                                  ) a
                                 ) nilai_kontrak
                                 "
                                 );
      } else {
        $getDataHPS->selectByParams("SELECT (SELECT sum(nilai) nilai_hps  from view_paket_dashboard
                               WHERE unit_kerja_id='".$unitkerja."' AND user_login_id = ".$userloginid." AND bulan_permohonan = ".$bulan.") nilai_hps,
                               (
                                SELECT (a.ongkos_kirim + a.harga_nego) nilai_kontrak from (
                                    SELECT
                                    (SELECT sum(ongkos_kirim) as ongkos_kirim from katalog_logistik where paket_id in (
                                    SELECT paket_id from view_paket_dashboard where unit_kerja_id='".$unitkerja."' AND user_login_id = ".$userloginid." and paket_metode_lelang_id in (6,9) AND bulan_permohonan = ".$bulan."
                                    )),
                                    (SELECT sum(harga_nego * qty) harga_nego from katalog_rekanan where paket_id in (
                                    SELECT paket_id from view_paket_dashboard where unit_kerja_id='".$unitkerja."' AND user_login_id = ".$userloginid." and paket_metode_lelang_id in (6,9) AND bulan_permohonan = ".$bulan."
                                    ))
                                ) a
                               ) nilai_kontrak
                               "
                               );
      }
    }
    // echo $getDataHPS->query; die;
    $html  = '';

    $getDataHPS->firstRow();
        $nilaiHps = $getDataHPS->getField('nilai_hps') ?: 0;
        $nilaiKontrak = $getDataHPS->getField('nilai_kontrak') ?: 0;
        $nilaiEfesiensi = $nilaiHps - $nilaiKontrak;
        // $nilaiEfesiensi = $nilaiHps - 22660000000;
        if ($nilaiEfesiensi > 0) {
          $persentaseEfesiensi = round($nilaiEfesiensi/$nilaiHps * 100,2).' %';
          $backColor = ' background-color:#967adc;';
          $ketEfesiensi1 = 'Efesien';
          $ketEfesiensi2 = '<span class="white darken-1 block"><i class="ft-arrow-down white" ></i> '.$persentaseEfesiensi.' dari Harga Perkiraan</span>';
        } else {
          $persentaseEfesiensi = 0;
          $backColor = ' background-color:#da4453;';
          $ketEfesiensi1 = 'Tidak Efesien';
          $ketEfesiensi2 = '<span class="white darken-1 block"><i class="ft-arrow-up white" ></i>  '.$persentaseEfesiensi.' dari Harga Perkiraan</span>';
        }

        $html .= '
        <table class="table table-bordered mb-3">
          <tr>
            <td>
              <div class="float-center pl-2">
                  <span class="grey darken-1 block">Harga Perkiraan</span>
                  <span class="font-large-2 line-height-1 text-bold-300">'.number_format($nilaiHps,'0',',','.').'</span>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="float-center pl-2">
                  <span class="grey darken-1 block">Harga Final/AKhir</span>
                  <span class="font-large-2 line-height-1 text-bold-300">'.number_format($nilaiKontrak,'0',',','.').'</span>
              </div>
            </td>
          </tr>
          <tr style="'.$backColor.'">
            <td>
              <div class="float-center pl-2">
                  <span class="white darken-1 block" style="color:#fff">'.$ketEfesiensi1.'</span>
                  <span class="font-large-1 line-height-1 text-bold-300" style="color:#fff">'.number_format($nilaiEfesiensi,'0',',','.').'</span>
                  '.$ketEfesiensi2.'
              </div>
            </td>
          </tr>
          <!-- <small><b>Dalam hitungan rupiah</b></small> -->
        </table>';


    $arr_json = array("message" => $html);

    echo json_encode($arr_json);
  }

}
?>
