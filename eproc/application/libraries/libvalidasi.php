<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package     eProcurement Application
 * @author      eproc2025
 * @since       25. Version 3.1
 * 
 */


class libvalidasi
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
  }

  function cekValidasiPublishPaket($reqId)
  { 
    $this->_CI->load->model("Paket");
    $this->_CI->load->model("Metode");
    $this->_CI->load->model("Paket");
    $this->_CI->load->model("PaketRekanan");
    $this->_CI->load->model("PaketDokumen");
    $this->_CI->load->model("PaketPanitia");
    $this->_CI->load->model("PaketEvaluasiAdminTawar");
    $this->_CI->load->model("PaketEvaluasiTeknisTawar");
    $this->_CI->load->model("PaketEvaluasiHargaTawar");
    $this->_CI->load->model("PaketEvaluasiKualifikasi");
    $this->_CI->load->model("Paketpemenang");
    $this->_CI->load->model("PaketBidangUsaha");

    $count = 0;
    $html  = '';
    $totalWajib = 0;

    $paket = new Paket();
    $paket->selectByParamsMonitoring2(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
    $paket->firstRow();
    $pra_kualifikasi_cek = $paket->getField("PAKET_METODE_KUALIFIKASI_ID"); // 1 File atau 2 File
    $metode_evaluasi_cek = $paket->getField("PAKET_METODE_EVALUASI_ID"); // 2-Sistem Nilai, 7-Sistem Harga Terendah
    $paket_jenis_cek = $paket->getField("PAKET_JENIS_ID"); // 1-PK, 2-JASKON, 3-B, 4-JL

    $paket_user_id = $paket->getField("USER_LOGIN_ID");
    $alasan = $paket->getField("ALASAN");
    $alasan_ulang = $paket->getField("ALASAN_ULANG");
    // 1:Tender, 2:Pengadaan langsung, 3:Tender Terbatas, 4:Seleksi Langsung, 5:Penunjukan Langsung, 6:e-Purchasing, 7:Tender Cepat, 8:Kompetisi, 9:Pembelian Langsung Offline, 10:Tender Kualifikasi, 11:Penunjukan Langsung Khusus
    $paket_metode_lelang_id = $paket->getField("PAKET_METODE_LELANG_ID");
    $paket_metode_nama = $paket->getField("METODE_LELANG");
    $rekanan_id_pemenang_negosiasi = $paket->getField("REKANAN_ID_PEMENANG");
    $publish_ba_penawaran = $paket->getField("PUBLISH_BA_PENAWARAN");
    $publish_ba_kualifikasi = $paket->getField("PUBLISH_BA_KUALIFIKASI");
    $sistem_sampul = $paket->getField("SISTEM_SAMPUL");
    $publish_ba_evaluasi_sampul1 = $paket->getField("PUBLISH_BA_EVALSAMPUL1");
    $publish_ba_evaluasi_sampul2 = $paket->getField("PUBLISH_BA_EVALSAMPUL2");
    $publish_ba_penawaran_sampul2 = $paket->getField("PUBLISH_BA_PENAWARAN2");
    $bidding = $paket->getField("BIDDING");
    $reqPermohonanId = $paket->getField("PERMOHONAN_PAKET_ID");
    $reqMetodePengadaan = $paket->getField("PAKET_METODE_LELANG_ID");

    // untuk label wajib dilengkapi dan lengkap

    $metode_c = new Metode();
    $paket_c = new Paket();
    $paket_dokumen_c = new PaketDokumen();
    $paket_dokumen_k = new PaketDokumen();
    $paket_panitia_c = new PaketPanitia();
    $paket_evaluasi_admin_count_c = new PaketEvaluasiAdminTawar();
    $paket_evaluasi_teknis_count_c = new PaketEvaluasiTeknisTawar();
    $paket_evaluasi_harga_count_c = new PaketEvaluasiHargaTawar();
    $paket_evaluasi_kualifikasi_count = new PaketEvaluasiKualifikasi();
    $getpaket_pemenang_c = new Paketpemenang();
    $paket_bidang_usaha_c = new PaketBidangUsaha();

    $countJadwal = $metode_c->getCountByParams(array("PAKET_ID" => $reqId));
    $countDokumen = $paket_dokumen_c->getCountByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "LELANG"));
    $countDokumenKualifikasi = $paket_dokumen_k->getCountByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "KUALIFIKASI"));
    $countPanitia = $paket_panitia_c->getCountByParams(array("PAKET_ID" => $reqId));
    $countPaket = $paket_c->getCountByParams(array("A.PAKET_ID" => coalesce($reqId, 0)));
    $countPaketEvaluasiAdminCOunt = $paket_evaluasi_admin_count_c->getCountByParams(array("PAKET_ID" => $reqId));
    $countPaketEvaluasiTeknisCOunt = $paket_evaluasi_teknis_count_c->getCountByParams(array("PAKET_ID" => $reqId));
    $countPaketEvaluasiHargaCOunt = $paket_evaluasi_harga_count_c->getCountByParams(array("PAKET_ID" => $reqId));
    $countPaketEvaluasiKualifikasi = $paket_evaluasi_kualifikasi_count->getCountByParams(array("PAKET_ID" => $reqId));
    $countPemenang = $getpaket_pemenang_c->getCountByParams(array("A.PAKET_ID" => $reqId));
    $countBidangUsaha = $paket_bidang_usaha_c->getCountByParams(array("PAKET_ID" => coalesce($reqId, 0)));


    if($pra_kualifikasi_cek == 1) // Prakualifikasi
      $labelDokumenPengadaan = 'Dokumen Pengadaan/Kualifikasi';
    else
      $labelDokumenPengadaan = 'Dokumen Pengadaan';

    $syarat = 0;
    if ($paket_metode_lelang_id == '7')  {
      if ($countPaketEvaluasiHargaCOunt > 0) {
        $syarat = 1;
      }
    } else {
      if ($countPaketEvaluasiAdminCOunt > 0 && $countPaketEvaluasiTeknisCOunt > 0 && $countPaketEvaluasiHargaCOunt > 0) {
        $syarat = 1;
      }
    }

    // $this->_CI->load->model("Queryfree");
    // $kirimBerkas = new Queryfree();
    // $kirimBerkas->selectByParams("SELECT USER_LOGIN_ID FROM USER_LOGIN WHERE USER_STATUS = '2' ");
    
    // Edit Paket Pengadaan
    $editpaket = 0;
    // 1-e-Tender, 
    // 2-Pengadaan Langsung, 
    // 3-Tender Terbatas, 
    // 4-Seleksi Langsung,
    // 5-Penunjukan Langsung Cepat, 
    // 6-e-Purchasing ,
    // 7-e-Tender Cepat, 
    // 8-Kompetisi,
    // 9-Pembelian Langsung Offline,
    // 10-Tender Kualifikasi,
    // 11-Penunjukan Langsung Khusus,
    if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '8' || $paket_metode_lelang_id == '10')  {
      if ($countPaket > 0 && $countBidangUsaha > 0) {
        $editpaket = 1;
      }
    } else if ($paket_metode_lelang_id == '6' || $paket_metode_lelang_id == '9')  {
      if ($countPaket > 0) {
        $editpaket = 1;
      }
    } else {
      $paket_rekanan_count = new PaketRekanan();
      $prcount = $paket_rekanan_count->getCountByParams(array("PAKET_ID" => $reqId));
      if ($countPaket > 0 && $countBidangUsaha > 0 && $prcount > 0) {
        $editpaket = 1;
      }
    }
    if ($editpaket == 0) { $totalWajib++; $html .= 'Edit Paket Pengadaan belum lengkap <br>'; } else {}
    // END Edit Paket Pengadaan


    $editUndangan = 0;
    if ($paket_metode_lelang_id == '2' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '5' || $paket_metode_lelang_id == '8') {
      $paket_rekanan_count = new PaketRekanan();
      $prcount = $paket_rekanan_count->getCountByParams(array("PAKET_ID" => $reqId));
      if ($prcount > 0) {
        $editUndangan = 1;
      }

      if ($editUndangan == 0) { $totalWajib++; $html .= 'Undangan belum lengkap <br>'; } else {}
    }


    // Tim Pengadaan
    if ($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9') { // selain e-Purchasing & Pembelian offline
      if ($countPanitia == 0) { 
        if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '8' || $paket_metode_lelang_id == '10') {
          $totalWajib++; $html .= 'Tim Pengadaan belum lengkap <br>';
        } else { 
        }
      } else { }
    }
    // END Tim Pengadaan

    // Jadwal
    if ($countJadwal == 0) { $totalWajib++; $html .= 'Jadwal belum lengkap <br>';} else { }
    // END Jadwal

    // Dokumen Kualifikasi
    if ($paket_metode_lelang_id == '10')
    { // Tender Kualifikasi
      if ($countDokumenKualifikasi == 0) { $totalWajib++; $html .= 'Dokumen Kualifikasi belum lengkap <br>'; } else { }
    } 
    // END Dokumen Kualifikasi

    // Syarat Dokumen Kualifikasi
    if ($paket_metode_lelang_id == '10')
    { // Tender Kualifikasi
      if ($countPaketEvaluasiKualifikasi == 0) { $totalWajib++; $html .= 'Syarat Dokumen Kualifikasi belum lengkap <br>'; } else { } 
    }
    // END Syarat Dokumen Kualifikasi

    // Dokumen Pengadaan
    if ($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9')
    { // bukan Purchasing/Pembelian Langsung & bukan pembelian offline 
      if ($countDokumen == 0) { $totalWajib++; $html .= $labelDokumenPengadaan.' belum lengkap <br>'; } else { }
    } 
    // END Dokumen Pengadaan

    // Syarat Dokumen Pengadaan
    if ($paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9')
    { // bukan Purchasing/Pembelian Langsung  & bukan pembelian offline
      if ($syarat == 0) { $totalWajib++; $html .= 'Syarat Dokumen Penawaran belum lengkap <br>'; } else { } 
    }
    // END Syarat Dokumen Pengadaan

    return array('data' => $html, 'count' => $totalWajib);
  } 

  public function cekPanitiaSetuju($totalPanitiaSetuju,$totalPanitiaSudahValidasi)
  {
    $totalPanitiaSetuju = $totalPanitiaSetuju/2;
    if ($totalPanitiaSudahValidasi > $totalPanitiaSetuju) {
      return true;
    } else {
      return false;
    }
  }

}
