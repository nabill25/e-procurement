<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

class libbreadcrumb
{
    private $_CI;

    function __construct()
    {
      $this->_CI =& get_instance();
      $this->_CI->load->library('session');
    }

    function breadikn($pg,$reqId)
    {
      $this->_CI->load->model("Paket");

      include_once("functions/string.func.php");
      include_once("functions/date.func.php");
      include_once("functions/default.func.php");

      $paket = new Paket();

      $arrJudul = explode("_", $pg);
      $max = count($arrJudul) - 1;
      $breadcrumb = '';
      $reqId = (!empty($reqId)) ? $reqId : '0';

      $cek = is_numeric($reqId);
      if ($cek == true) {
        // echo '---'.$reqId; die;
        $paket->selectByParamsMonitoring(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
        $paket->firstRow();
        $pra_kualifikasi_cek = $paket->getField("PAKET_METODE_KUALIFIKASI_ID"); // 1 File atau 2 File
        $metode_evaluasi_cek = $paket->getField("PAKET_METODE_EVALUASI_ID"); // 2-Sistem Nilai, 7-Sistem Harga Terendah
        $uuid = $paket->getField("PAKET_UUID"); // 2-Sistem Nilai, 7-Sistem Harga Terendah
        $paket_jenis_cek = $paket->getField("PAKET_JENIS_ID"); // 1-PK, 2-JASKON, 3-B, 4-JL
        $paket_metode_lelang_id = $paket->getField("PAKET_METODE_LELANG_ID");
      }
      // 1-e-Tender ,7-e-Tender Cepat, 2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat

      switch ($pg) {
        case 'data_teknis_pengalaman_progress_tambah':
          $link_monitoring = str_replace("_ubah", "", $pg);
          $link_monitoring = str_replace("_tambah", "", $link_monitoring);
          $monitoring = str_replace("_", " ", $link_monitoring);

          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/data_teknis_pengalaman\">Data Teknis Pengalaman</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Manajemen ".ucwords($monitoring)."</li>";
          break;

        case 'data_teknis_pengalaman_selesai_tambah':
          $link_monitoring = str_replace("_ubah", "", $pg);
          $link_monitoring = str_replace("_selesai_tambah", "", $link_monitoring);
          $monitoring = str_replace("_", " ", $link_monitoring);

          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/data_teknis_pengalaman\">Data Teknis Pengalaman</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> ".ucwords($monitoring)."</li>";
          break;

        case 'blacklist':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Daftar Hitam</li>";
          break;

        case 'data_administrasi_landasan_hukum':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Data Administrasi</li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Akta Pendirian</li>";
        break;

        case 'data_administrasi_landasan_hukum_ubah':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Data Administrasi</li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Akta Pendirian Ubah</li>";
        break;

        case 'registrasi_paket':
          $pg_monitoring = str_replace("paket_lelang_tambah", "", $pg);
          $pg_monitoring = str_replace("sampul1", "File 1", $pg_monitoring);
          $pg_monitoring = str_replace("sampul2", "File 2", $pg_monitoring);
          $monitoring = str_replace("_", " ", $pg_monitoring);

          if($pg == "paket_lelang_tambah")
            $monitoring = "Informasi Paket";
          else
            $monitoring = "Kelola ".$monitoring;

          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Paket Tender</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\">".ucwords(str_replace("_", " ", $pg))."</li>";
          break;

        case 'registrasi_rekanan_cv':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> CV ( Daftar Riwayat Hidup )</li>";
          break;

        case 'paket_lelang_masa_sanggah':
        case 'paket_lelang_masa_sanggah_rekanan':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Sanggahan</li>";
          break;

        case 'paket_lelang_masa_sanggah_kualifikasi_rekanan':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Sanggahan</li>";
          break;

        case 'paket_lelang_masa_sanggah_kualifikasi_tanggapan':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Sanggahan Kualifikasi</li>";
          break;

        case 'paket_lelang_masa_sanggah_tanggapan';
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_lelang_masa_sanggah/?reqId=".$reqId."\">Sanggahan</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Jawab</li>";
          break;

        case 'rekanan_chat_eval_teknis':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Evaluasi Dokumen</li>";
          break;

        case 'paket_laporan':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Laporan Paket</li>";
          break;

        case 'paket_penilaian':
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Paket Tender</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Penilaian</li>";
          break;

        case 'paket_lelang':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Tender</li>";
          break;

        case 'tendernon':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Non Tender</li>";
          break;

        case 'master_pengaturan_dok_expired':
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/master_pengaturan\">Master Pengaturan</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Dokumen Expired Penyedia</li>";
        break;

        case 'master_dokumen_template2':
        case 'master_dokumen_template_rekanan':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Master Data</li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Bank Template Dokumen</li>";
        break;

        case 'dokumen_template_rekanan':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Template Dokumen</li>";
        break;

        case 'dokumen_template':
          $breadcrumb .= "<li class=\"breadcrumb-item active\">Template Dokumen</li>";
        break;

        case 'permohonan_penunjukan_pic':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Penunjukan PIC Paket</li>";
          break;

        case 'permohonan_paket_panitia':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Permohonan Paket</li>";
          break;

        case 'permohonan_paket_usulan':
        case 'permohonan_paket_usulan_admin':
        case 'permohonan_paket_usulan_validator':
        case 'permohonan_paket_usulan_validator2':
        case 'permohonan_paket_usulan_approval':
        case 'permohonan_paket_usulan_approval_kpa':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Usulan Kebutuhan</li>";
          break;

        case 'permohonan_paket_usulan_divisi':
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/permohonan_paket_usulan\">Usulan Kebutuhan</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Divisi</li>";
          break;

        case 'permohonan_paket_usulan_admin_to_be_approved':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Usulan Kebutuhan (To Be Approved)</li>";
          break;

        case 'permohonan_paket_usulan_pengguna':
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/permohonan_paket_usulan_pengguna\">RUP</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Usulan PBJ</li>";
          break;

        case 'permohonan_paket_usulan_add':
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/permohonan_paket_usulan_add\">Persiapan</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Reviu</li>";
          break;

        case 'permohonan_paket_fungsional_add':
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/permohonan_paket_fungsional\">Rencana Pengadaan</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Ubah</li>";
          break;

        case 'rencana_umum_pengadaan':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> RUP</li>";
          break;

        case 'rencana_umum_pengadaan_persiapan':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Persiapan</li>";
          break;

        case 'permohonan_paket_fungsional':
        case 'permohonan_paket_fungsional_rup':
        case 'permohonan_paket_unit':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Rencana Pengadaan</li>";
          break;

        case 'kontak':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Kontak Kami</li>";
          break;

        case 'katalog':
          $name = httpFilterRequest("name") ? httpFilterRequest("name") : null;
          $subKaetgoriLabel = httpFilterRequest("kategori") ? httpFilterRequest("kategori") : null;
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> <a href=\"main/index/katalog\">Katalog</a></li>";
          if ($name) {
            $name = ucwords(str_replace("-", " ", $name));
            $breadcrumb .= "<li class=\"breadcrumb-item active\">".$name."</li>";
          }

          if ($subKaetgoriLabel) {
            $this->_CI->load->model("Katalogkategori");
            $katalog_kategori = new Katalogkategori();
            $arrStatement = array('A.URL3' => $subKaetgoriLabel);
            $katalog_kategori->selectByParams($arrStatement, -1, -1);
            $katalog_kategori->firstRow();
            $subKaetgoriLabel = ucwords(str_replace("-", " ", $subKaetgoriLabel));
            $breadcrumb .= "<li class=\"breadcrumb-item active\"> <a href=\"main/index/katalog?name=".$katalog_kategori->getField("URL")."\">".$katalog_kategori->getField("NAMA_KATEGORI_1")."</a></li>";
            $breadcrumb .= "<li class=\"breadcrumb-item active\">".$subKaetgoriLabel."</li>";
          }
          break;

        case 'katalog_rekanan':
          $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"main/index/katalog_rekanan\">Etalase</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\">Katalog</li>";
          break;

        case 'katalog_cart':
          $breadcrumb .= "<li class=\"breadcrumb-item active\">Daftar Produk</li>";
          break;

        case 'katalog_penawaran':
          $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"main/index/katalog_rekanan\">Etalase</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\">Penawaran</li>";
          break;

        case 'katalog_pernyataan':
          $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"main/index/katalog_rekanan\">Etalase</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\">Upload Kontrak Katalog</li>";
          break;

        case 'katalog_rekanan_add':
        $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"main/index/katalog_rekanan\">Etalase</a></li>";
            if($this->_CI->USER_TYPE_ID == "6") {
              if ($reqId) {
                $labelAdd = 'Ubah';
              } else {
                $labelAdd = 'Tambah';
              }
            }
            if($this->_CI->USER_TYPE_ID == "1" || $this->_CI->USER_TYPE_ID == "2"){
              $labelAdd = 'Lihat';
            }
            $breadcrumb .= "<li class=\"breadcrumb-item active\">Katalog ".$labelAdd."</li>";
          break;

        case 'katalog_detail':
          $this->_CI->load->model("Katalog");
          $katalog = new Katalog();
          $id = httpFilterRequest("id") ? httpFilterRequest("id") : null;
          $arrStatement = array('A.KATALOGID' => $id);
          $katalog->selectByParamsViewKatalogByKategori($arrStatement, -1, -1);
          $katalog->firstRow();
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> <a href=\"main/index/katalog\">Katalog</a></li>";
          if ($id) {
            $breadcrumb .= "<li class=\"breadcrumb-item active\"> <a href=\"main/index/katalog?name=".$katalog->getField("URL")."\">".$katalog->getField("NAMA_KATEGORI_1")."</a></li>";
            $breadcrumb .= "<li class=\"breadcrumb-item active\"> <a href=\"main/index/katalog?kategori=".$katalog->getField("URL3")."\">".$katalog->getField("NAMA")."</a></li>";
            $breadcrumb .= "<li class=\"breadcrumb-item active\">".$katalog->getField("NAMAPRODUK")."</li>";
          }
          break;

        case 'katalog_validasi':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Data Katalog</li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Katalog verifikasi</li>";
          break;

        case 'katalog_validasi_rekanan':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Data Katalog</li>";
          $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"main/index/katalog_validasi\">Katalog verifikasi</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Detail</li>";
          break;

        case 'contracting_selesai':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Selesai Kontrak</li>";
        break;

        case 'contracting_dashboard':
          $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"main/index/contracting_dashboard\">Kontrak</a></li>";
          break;

        case 'contracting_detaillegal':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Kontrak Detail</li>";
        break;

        case 'contracting_legal_file':
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"kontrak/index/contracting_detaillegal/?reqId=".$reqId."\">Kontrak Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Dokumen Pemilihan</li>";
        break;

        case 'contracting_legal_sppbj':
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"kontrak/index/contracting_detaillegal/?reqId=".$reqId."\">Kontrak Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> SPPBJ</li>";
        break;

        case 'contracting_legal_perjanjian':
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"kontrak/index/contracting_detaillegal/?reqId=".$reqId."\">Kontrak Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Data Kontrak</li>";
        break;

        case 'contracting_legal_realisasi':
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"kontrak/index/contracting_detaillegal/?reqId=".$reqId."\">Kontrak Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Realisasi Pekerjaan</li>";
        break;

        case 'contracting_legal_termin':
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"kontrak/index/contracting_detaillegal/?reqId=".$reqId."\">Kontrak Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Termin Pembayaran</li>";
        break;

        case 'contracting_legal_perubahan':
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"kontrak/index/contracting_detaillegal/?reqId=".$reqId."\">Kontrak Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Perubahan</li>";
        break;

        case 'contracting_legal_harga':
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"kontrak/index/contracting_detaillegal/?reqId=".$reqId."\">Kontrak Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Penyesuaian Harga</li>";
        break;

        case 'contracting_legal_kahar':
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"kontrak/index/contracting_detaillegal/?reqId=".$reqId."\">Kontrak Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Keadaan Kahar</li>";
        break;

        case 'contracting_legal_berakhir':
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"kontrak/index/contracting_detaillegal/?reqId=".$reqId."\">Kontrak Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Berakhir</li>";
        break;

        case 'contracting_legal_pemutusan':
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"kontrak/index/contracting_detaillegal/?reqId=".$reqId."\">Kontrak Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Pemutusan</li>";
        break;

        case 'contracting_legal_kesempatan':
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"kontrak/index/contracting_detaillegal/?reqId=".$reqId."\">Kontrak Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Pemberian Kesempatan</li>";
        break;

        case 'contracting_legal_denda':
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"kontrak/index/contracting_detaillegal/?reqId=".$reqId."\">Kontrak Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Sanksi dan Denda</li>";
        break;

        case 'contracting_legal_file':
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"kontrak/index/contracting_detaillegal/?reqId=".$reqId."\">Kontrak Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Dokumen Pendukung</li>";
        break;

        case 'contracting_legal_dokumen':
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"kontrak/index/contracting_detaillegal/?reqId=".$reqId."\">Kontrak Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Dokumen Pendukung</li>";
        break;

        case 'contracting_audit_dokumen':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Dokumen Kontrak</li>";
          break;

        case 'dashboard':
        case 'dashboardvms':
        case 'dashboardkontrak':
        case 'dashboardperencana':
        case 'dashboardpembeli':
        case 'dashboardunitverifikator';
        case 'dashboardunitvalidator';
        case 'dashboardunitapproval';
          $breadcrumb .= "<li class=\"breadcrumb-item\"> Dashboard</li>";
          break;

        case 'rup':
          $breadcrumb .= "<li class=\"breadcrumb-item\"> Pengumuman RUP</li>";
          break;

        case 'paket_lelang_tambah_dokumen_lelang':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Dokumen Pengadaan</li>";
          break;

        case 'paket_lelang_tambah_dokumen_kualifikasi':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Dokumen Kualifikasi</li>";
          break;

        case 'paket_lelang_dokumen_kualifikasi_rekanan':
        case 'dokumen_penawaran_kualifikasi_rekanan':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";

          $pg = str_replace("paket_lelang_dokumen_kualifikasi_rekanan", "Dokumen Kualifikasi", $pg).'';
          $pg = str_replace("dokumen_penawaran_kualifikasi_rekanan", "Upload Dokumen Kualifikasi", $pg).'';

          $breadcrumb .= "<li class=\"breadcrumb-item active\"> ".$pg."</li>";
          break;

          case 'rekanan_chat_eval_kualifikasi':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Pembuktian Kualifikasi</li>";
          break;


         case 'hasil_eval_rekanan':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Hasil Evaluasi</li>";
          break;

          case 'hasil_eval_rekanan_kualifikasi':
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Hasil Evaluasi Kualifikasi</li>";
          break;

        case 'hasil_eval_rekanan_file_1':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Hasil Evaluasi File 1</li>";
          break;

        case 'hasil_eval_rekanan_file_2':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Hasil Evaluasi File 2</li>";
          break;

        case 'paket_lelang_penentuan_peringkat_rekanan':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Pemberitahuan Peringkat</li>";
          break;

        case 'contracting_paket':
          $getTahun = $this->_CI->session->userdata('setTahunKontrak') ?: 'all';

          // $breadcrumb .= "<li class=\"breadcrumb-item active\">Manajemen Kontrak</li>";
          // $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"kontrak/index/contracting_dashboard?tahun=".$getTahun."\">Kontrak</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\">SPPBJ</li>";

          break;



        case 'contracting_pembelian':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Contracting</li>";
        break;

        case 'contracting_surat_perjanjian':
          $getTahun = $this->_CI->session->userdata('setTahunKontrak') ?: 'all';

          // $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"kontrak/index/contracting_dashboard?tahun=".$getTahun."\">Kontrak</a></li>";
          // $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"kontrak/index/contracting_paket?tahun=".$getTahun."\">Paket</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"kontrak/index/contracting_paket?tahun=".$getTahun."\">Paket Selesai Pemilihan</a></li>";

          $pg = str_replace("contracting_surat_perjanjian", "SPPBJ", $pg).'';
          $pg = str_replace('contracting', '', $pg);
          $breadcrumb .= "<li class=\"breadcrumb-item active\">".ucwords(str_replace("_", " ", $pg))."</li>";

          break;

        case 'contracting_detail':
          $getTahun = $this->_CI->session->userdata('setTahunKontrak') ?: 'all';
          $reqProses = $_GET['reqProses'];

          // $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"kontrak/index/contracting_dashboard?tahun=".$getTahun."\">Kontrak</a></li>";
          // $breadcrumb .= "<li class=\"breadcrumb-item active\">Manajemen Kontrak</li>";
          if ($reqProses == '6') { }
          else {
            $breadcrumb .= "<li class=\"breadcrumb-item active\"><a href=\"kontrak/index/contracting_persiapan?tahun=".$getTahun."\">Proses Kontrak</a></li>";
          }


          // Proses Kontrak
          $this->_CI->load->model("Contractingrekanan");
          $contractingrekanan = new Contractingrekanan();
          $contractingrekanan->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
          $contractingrekanan->firstRow();
          $namaLabel = ucfirst(strtolower($contractingrekanan->getField('CP_NAME')));
          // Khusus Proses ke 4 diharcode karena ketika proses 3 bisa langsung di proses 4
          if ($reqProses == '4') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"kontrak/index/contracting_permasalahan?tahun=".$getTahun."\">".str_replace(" kontrak", "", "Monitor dan Kontrol" )."</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"".$contractingrekanan->getField('CP_LINK')."?tahun=".$getTahun."\">".str_replace(" kontrak", "", $namaLabel )."</a></li>";
          }
          // End Proses Kontrak

          $pg = str_replace("contracting_persiapan", "Inisiasi", $pg).'';
          $pg = str_replace("sppbj", "SPPBJ", $pg).'';
          $pg = str_replace('contracting', '', $pg);
          $breadcrumb .= "<li class=\"breadcrumb-item active\">".ucwords(str_replace("_", " ", $pg))."</li>";

          break;

        case 'contracting_penyedia';
          $pg = str_replace("contracting_penyedia_detail", "Detail", $pg).'';
          $breadcrumb .= "<li class=\"breadcrumb-item active\">Manajemen Kontrak</li>";
          break;

        case 'contracting_penyedia_detail';
          $pg = str_replace("contracting_penyedia_detail", "Detail", $pg).'';
          $breadcrumb .= "<li class=\"breadcrumb-item active\"><a href=\"kontrak/index/contracting_penyedia\">Manajemen Kontrak</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\">".ucwords(str_replace("_", " ", $pg))."</li>";
          break;

        case 'contracting_penyedia_sppbj';
        case 'contracting_penyedia_sppbj_multi';
        case 'contracting_penyedia_perjanjian';
        case 'contracting_penyedia_perubahan';
        case 'contracting_penyedia_perubahan_multi';
        case 'contracting_penyedia_perjanjian_multi';
        case 'contracting_penyedia_realisasi';
        case 'contracting_penyedia_termin';
        case 'contracting_penyedia_termin_multi';
        case 'contracting_penyedia_harga';
        case 'contracting_penyedia_kahar';
        case 'contracting_penyedia_berakhir';
        case 'contracting_penyedia_pemutusan';
        case 'contracting_penyedia_kesempatan';
        case 'contracting_penyedia_denda';
        case 'contracting_penyedia_dokumen';
        case 'contracting_multi_penyedia_dokumen';
        case 'contracting_penyedia_surat_pesanan_multi';
          $pg = str_replace("contracting_penyedia_sppbj", "SPPBJ", $pg).'';
          $pg = str_replace("SPPBJ_multi", "SPPBJ", $pg).'';
          $pg = str_replace("contracting_penyedia_perjanjian_multi", "Kontrak", $pg).'';
          $pg = str_replace("contracting_penyedia_perjanjian", "Kontrak", $pg).'';
          $pg = str_replace("contracting_penyedia_perubahan", "Kontrak", $pg).'';
          $pg = str_replace(array("contracting_penyedia_perubahan_multi","multi"), "Perubahan", $pg).'';
          $pg = str_replace("contracting_penyedia_realisasi", "Realisasi Pekerjaan", $pg).'';
          $pg = str_replace("contracting_penyedia_termin", "Termin Pembayaran", $pg).'';
          $pg = str_replace("contracting_penyedia_termin_multi", "Termin Pembayaran", $pg).'';
          $pg = str_replace("contracting_penyedia_harga", "Penyesuaian Harga", $pg).'';
          $pg = str_replace("contracting_penyedia_kahar", "Keadaan Kahar", $pg).'';
          $pg = str_replace("contracting_penyedia_berakhir", "Berakhir", $pg).'';
          $pg = str_replace("contracting_penyedia_pemutusan", "Pemutusan", $pg).'';
          $pg = str_replace("contracting_penyedia_kesempatan", "Pemberian Kesempatan", $pg).'';
          $pg = str_replace("contracting_penyedia_denda", "Denda dan Ganti Rugi", $pg).'';
          $pg = str_replace("contracting_Perubahan_penyedia_dokumen", "Dokumen Pendukung", $pg).'';
          $pg = str_replace("contracting_multi_penyedia_dokumen", "Dokumen Pendukung", $pg).'';
          $pg = str_replace(array("contracting_penyedia_surat_pesanan_multi","contracting_penyedia_surat_pesanan_Perubahan"), "Surat Pesanan", $pg).'';
          $breadcrumb .= "<li class=\"breadcrumb-item active\"><a href=\"kontrak/index/contracting_penyedia\">Manajemen Kontrak</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"><a href=\"kontrak/index/contracting_penyedia_detail?reqId=".$reqId."\">Detail</a></li>";

          $breadcrumb .= "<li class=\"breadcrumb-item active\">".ucwords(str_replace("_", " ", $pg))."</li>";
          break;

        case 'contracting_persiapan':
        case 'contracting_pengelolaan':
        case 'contracting_permasalahan':
        case 'contracting_serah_terima':
          $getTahun = $this->_CI->session->userdata('setTahunKontrak') ?: 'all';
          // $breadcrumb .= "<li class=\"breadcrumb-item active\">Manajemen Kontrak</li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"><a href=\"kontrak/index/contracting_persiapan?tahun=".$getTahun."\">Proses Kontrak</a></li>";

          // $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"kontrak/index/contracting_dashboard?tahun=".$getTahun."\">Kontrak</a></li>";

          $pg = str_replace("contracting_persiapan", "Inisiasi", $pg).'';
          $pg = str_replace("contracting_pengelolaan", "Pelaksanaan", $pg).'';
          $pg = str_replace("contracting_permasalahan", "Pengendalian ", $pg).'';
          $pg = str_replace("contracting_serah_terima", "Penutupan", $pg).'';
          $pg = str_replace("sppbj", "SPPBJ", $pg).'';
          $pg = str_replace('contracting', '', $pg);
          $breadcrumb .= "<li class=\"breadcrumb-item active\">".ucwords(str_replace("_", " ", $pg))."</li>";

          break;

        case 'contracting_persiapan_sppbj';
        case 'contracting_persiapan_sppbj_multi';
        case 'contracting_persiapan_sppbj_multi_edit';
        case 'contracting_persiapan_spmk_multi';
        case 'contracting_persiapan_spmk_multi_edit';
        case 'contracting_file';
        case 'contracting_multi_file';
        case 'contracting_persiapan_kontrak';
        case 'contracting_persiapan_kontrak_payung';
        case 'contracting_pengelolaan_realisasi':
        case 'contracting_persiapan_lokasi';
        case 'contracting_persiapan_spmk';
        case 'contracting_pengelolaan_termin';
        case 'contracting_monitoring_termin';
        case 'contracting_monitoring_termin_multi';
        case 'contracting_monitoring_realisasi';
        case 'contracting_monitoring_realisasi_multi';
        case 'contracting_monitoring_perubahan';
        case 'contracting_monitoring_perubahan_multi';
        case 'contracting_monitoring_harga';
        case 'contracting_monitoring_kahar';
        case 'contracting_monitoring_berakhir';
        case 'contracting_monitoring_pemutusan';
        case 'contracting_monitoring_kesempatan';
        case 'contracting_monitoring_denda';
        case 'contracting_serah_terima_hasil';
        case 'contracting_serah_terima_hasil_multi';
        case 'contracting_serah_terima_pemeliharaan';
        case 'contracting_penilaian';
        case 'contracting_penilaian_tambah';
        case 'contracting_penilaian_multi_tambah';
        case 'contracting_penilaian_multi';
        case 'contracting_pelaksanaan_kontrak_payung';
        case 'contracting_pengelolaan_termin_multi';
        case 'contracting_pengelolaan_realisasi_multi';
          $reqProses = $_GET['reqProses'];

          if ($pg == 'contracting_persiapan_sppbj'): $active = 'SPPBJ';
          elseif ($pg == 'contracting_persiapan_sppbj_multi'): $active = 'SPPBJ';
          elseif ($pg == 'contracting_persiapan_sppbj_multi_edit'): $active = 'SPPBJ';
          elseif ($pg == 'contracting_persiapan_spmk_multi'): $active = 'SPMK';
          elseif ($pg == 'contracting_persiapan_spmk_multi_edit'): $active = 'SPMK';
          elseif ($pg == 'contracting_file'): $active = 'Dokumen Pendukung';
          elseif ($pg == 'contracting_multi_file'): $active = 'Dokumen Pendukung';
          elseif ($pg == 'contracting_persiapan_kontrak'): $active = 'Kontrak';
          elseif ($pg == 'contracting_persiapan_lokasi'): $active = 'Lokasi';
          elseif ($pg == 'contracting_persiapan_kontrak'): $active = 'Kontrak';
          elseif ($pg == 'contracting_persiapan_kontrak_payung'): $active = 'Kontrak';
          elseif ($pg == 'contracting_persiapan_spmk'): $active = 'SPMK';
          elseif ($pg == 'contracting_pengelolaan_realisasi'): $active = 'Realisasi Pekerjaan';
          elseif ($pg == 'contracting_pengelolaan_termin'): $active = 'Termin Pembayaran';
          elseif ($pg == 'contracting_monitoring_realisasi'): $active = 'Realisasi Pekerjaan';
          elseif ($pg == 'contracting_monitoring_realisasi_multi'): $active = 'Realisasi Pekerjaan';
          elseif ($pg == 'contracting_monitoring_termin'): $active = 'Termin Pembayaran';
          elseif ($pg == 'contracting_monitoring_termin_multi'): $active = 'Termin Pembayaran';
          elseif ($pg == 'contracting_monitoring_perubahan'): $active = 'Perubahan Kontrak';
          elseif ($pg == 'contracting_monitoring_perubahan_multi'): $active = 'Perubahan Kontrak';
          elseif ($pg == 'contracting_monitoring_harga'): $active = 'Penyesuaian Harga';
          elseif ($pg == 'contracting_monitoring_kahar'): $active = 'Keadaan Kahar';
          elseif ($pg == 'contracting_monitoring_berakhir'): $active = 'Berakhir Kontrak';
          elseif ($pg == 'contracting_monitoring_pemutusan'): $active = 'Pemutusan Kontrak';
          elseif ($pg == 'contracting_monitoring_kesempatan'): $active = 'Pemberian Kesempatan';
          elseif ($pg == 'contracting_monitoring_denda'): $active = 'Sanksi dan Denda';
          elseif ($pg == 'contracting_serah_terima_hasil'): $active = 'Hasil Pekerjaan';
          elseif ($pg == 'contracting_serah_terima_hasil_multi'): $active = 'Hasil Pekerjaan';
          elseif ($pg == 'contracting_serah_terima_pemeliharaan'): $active = 'Hasil Pekerjaan';
          elseif ($pg == 'contracting_penilaian'): $active = 'Penilaian Kinerja';
          elseif ($pg == 'contracting_penilaian_tambah'): $active = 'Penilaian Kinerja';
          elseif ($pg == 'contracting_penilaian_multi_tambah'): $active = 'Penilaian Kinerja';
          elseif ($pg == 'contracting_penilaian_multi'): $active = 'Penilaian Kinerja';
          elseif ($pg == 'contracting_pelaksanaan_kontrak_payung'): $active = 'Surat Pesanan';
          elseif ($pg == 'contracting_pengelolaan_termin_multi'): $active = 'Pembayaran';
          elseif ($pg == 'contracting_pengelolaan_realisasi_multi'): $active = 'Realisasi Pekerjaan';
          else: $active = '';
          endif;

          $arrDetail = array("contracting_persiapan_sppbj","contracting_persiapan_sppbj_multi","contracting_pengelolaan_termin_multi","contracting_persiapan_sppbj_multi_edit","contracting_persiapan_kontrak","contracting_file","contracting_persiapan_lokasi","contracting_persiapan_spmk","contracting_pengelolaan_realisasi","contracting_pengelolaan_termin","contracting_monitoring_realisasi","contracting_monitoring_termin","contracting_monitoring_perubahan","contracting_monitoring_harga","contracting_monitoring_kahar","contracting_monitoring_berakhir","contracting_monitoring_pemutusan","contracting_monitoring_kesempatan","contracting_monitoring_denda","contracting_serah_terima_hasil","contracting_penilaian","contracting_penilaian_tambah");
          $pg = str_replace($arrDetail, "Detail", $pg).'';
          $pg = str_replace("Multi Edit", "Detail", "Multi Edit").'';

          $getTahun = $this->_CI->session->userdata('setTahunKontrak') ?: 'all';
          // $breadcrumb .= "<li class=\"breadcrumb-item active\">".ucwords(str_replace("_", " ", $pg))."</li>";
          // $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"kontrak/index/contracting_dashboard?tahun=".$getTahun."\">Kontrak</a></li>";
          // $breadcrumb .= "<li class=\"breadcrumb-item active\">Manajemen Kontrak</li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"><a href=\"kontrak/index/contracting_persiapan?tahun=".$getTahun."\">Proses Kontrak</a></li>";

          // Proses Kontrak
          $this->_CI->load->model("Contractingrekanan");
          $contractingrekanan = new Contractingrekanan();
          $contractingrekanan->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
          $contractingrekanan->firstRow();
          $namaLabel = ucfirst(strtolower($contractingrekanan->getField('CP_NAME')));
          // Khusus Proses ke 4 diharcode karena ketika proses 3 bisa langsung di proses 4
          if ($reqProses == '4') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"kontrak/index/contracting_permasalahan?tahun=".$getTahun."\">".str_replace(" kontrak", "", "Monitor dan Kontrol" )."</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"".$contractingrekanan->getField('CP_LINK')."?tahun=".$getTahun."\">".str_replace(" kontrak", "", $namaLabel )."</a></li>";
          }
          // END Proses Kontrak

          $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"kontrak/index/contracting_detail?reqId=".$reqId."&reqProses=".$reqProses."\">".ucwords(str_replace("_", " ", $pg))."</a></li>";

          $breadcrumb .= "<li class=\"breadcrumb-item active\">".$active."</li>";

        break;

        case 'contracting_persiapan_kontrak_multi_edit';
        case 'contracting_pelaksanaan_kontrak_surat_pesanan_multi_edit';
          $reqProses = isset($_GET['reqProses']) ?: $this->_CI->session->userdata('setProsesKontrak');

           // Proses Kontrak
          $this->_CI->load->model("Contractingrekanan");
          $contractingrekanan = new Contractingrekanan();
          $contractingrekanan->selectByParams(array("D.CONTRACTINGREKANANPROSES1ID" => $reqId));
          $contractingrekanan->firstRow();
          $namaLabel = ucfirst(strtolower($contractingrekanan->getField('CP_NAME')));

          $reqId = $contractingrekanan->getField('CONTRACTINGREKANANID');

           if ($pg == 'contracting_persiapan_kontrak_multi_edit'):
            $getProses = $this->_CI->session->userdata('setProsesKontrak');

            $active   = 'Kontrak';
            $pg       = str_replace("contracting_persiapan_kontrak_multi_edit", "Detail", $pg).'';
            $linkBack = "<a href=\"kontrak/index/contracting_persiapan_kontrak_payung?reqId=".$reqId."\">".$active."</a>";

           elseif ($pg == 'contracting_pelaksanaan_kontrak_surat_pesanan_multi_edit'):
            $active   = 'Surat Pesanan';
            $pg       = str_replace("contracting_pelaksanaan_kontrak_surat_pesanan_multi_edit", "Detail", $pg).'';
            $linkBack = "<a href=\"kontrak/index/contracting_pelaksanaan_kontrak_payung?reqId=".$reqId."\">".$active."</a>";

          else: $active = ''; $pg = ''; $linkBack = '';
          endif;

          $getTahun = $this->_CI->session->userdata('setTahunKontrak') ?: 'all';
          $back = $this->_CI->input->get("back");

          $breadcrumb .= "<li class=\"breadcrumb-item active\"><a href=\"kontrak/index/contracting_persiapan?tahun=".$getTahun."\">Proses Kontrak</a></li>";

          if ($back) {
            $linkBack = "<a href=\"kontrak/index/contracting_persiapan_kontrak?reqId=".$reqId."\">".$active."</a>";
            $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"kontrak/index/contracting_permasalahan?tahun=".$getTahun."\">Monitorig dan Kontrol</a></li>";
            $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"kontrak/index/contracting_detail?reqId=".$reqId."&reqProses=4\">".ucwords(str_replace("_", " ", $pg))."</a></li>";
          } else
          {

            $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"".$contractingrekanan->getField('CP_LINK')."?tahun=".$getTahun."\">".str_replace(" kontrak", "", $namaLabel )."</a></li>";
            // END Proses Kontrak

            $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"kontrak/index/contracting_detail?reqId=".$reqId."&reqProses=".$reqProses."\">".ucwords(str_replace("_", " ", $pg))."</a></li>";
            $breadcrumb .= "<li class=\"breadcrumb-item\">".$linkBack."</li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item active\">Edit</li>";

        break;

        case 'contracting_persiapan_kontrak_edit';
        case 'contracting_persiapan_sppbj_edit';
        case 'contracting_persiapan_kontrak_edit_legal';

          if ($pg == 'contracting_persiapan_sppbj_edit'):
            $active   = 'SPPBJ';
            $pg       = str_replace("contracting_persiapan_sppbj_edit", "Detail", $pg).'';
            $linkBack = "<a href=\"kontrak/index/contracting_persiapan_sppbj?reqId=".$reqId."\">".$active."</a>";

          elseif ($pg == 'contracting_persiapan_kontrak_edit'):
            $active   = 'Kontrak';
            $getProses = $this->_CI->session->userdata('setProsesKontrak');

            $pg       = str_replace("contracting_persiapan_kontrak_edit", "Detail", $pg).'';
            $linkBack = "<a href=\"kontrak/index/contracting_persiapan_kontrak?reqId=".$reqId."\">".$active."</a>";

          elseif ($pg == 'contracting_persiapan_kontrak_edit_legal'):
            $active   = 'Kontrak';
            $pg       = str_replace("contracting_persiapan_kontrak_edit_legal", "Detail", $pg).'';
            $linkBack = "<a href=\"kontrak/index/contracting_persiapan_kontrak?reqId=".$reqId."\">".$active."</a>";

          elseif ($pg == 'contracting_persiapan_lokasi_edit'):
            $active   = 'Lokasi';
            $pg       = str_replace("contracting_persiapan_lokasi_edit", "Detail", $pg).'';
            $linkBack = "<a href=\"kontrak/index/contracting_persiapan_lokasi?reqId=".$reqId."\">".$active."</a>";

          elseif ($pg == 'contracting_persiapan_spmk_edit'):
            $active   = 'SPMK';
            $pg       = str_replace("contracting_persiapan_spmk_edit", "Detail", $pg).'';
            $linkBack = "<a href=\"kontrak/index/contracting_persiapan_spmk?reqId=".$reqId."\">".$active."</a>";

          else: $active = ''; $pg = ''; $linkBack = '';
          endif;

          $getTahun = $this->_CI->session->userdata('setTahunKontrak') ?: 'all';

          // $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"kontrak/index/contracting_dashboard?tahun=".$getTahun."\">Kontrak</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"><a href=\"kontrak/index/contracting_persiapan?tahun=".$getTahun."\">Proses Kontrak</a></li>";

          $back = $this->_CI->input->get("back");

          if ($back) {
            $linkBack = "<a href=\"kontrak/index/contracting_persiapan_kontrak?reqId=".$reqId."\">".$active."</a>";
            $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"kontrak/index/contracting_permasalahan?tahun=".$getTahun."\">Monitorig dan Kontrol</a></li>";
            $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"kontrak/index/contracting_detail?reqId=".$reqId."&reqProses=4\">".ucwords(str_replace("_", " ", $pg))."</a></li>";
          } else
          {
            // Proses Kontrak
            $this->_CI->load->model("Contractingrekanan");
            $contractingrekanan = new Contractingrekanan();
            $contractingrekanan->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
            $contractingrekanan->firstRow();
            $namaLabel = ucfirst(strtolower($contractingrekanan->getField('CP_NAME')));
            $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"".$contractingrekanan->getField('CP_LINK')."?tahun=".$getTahun."\">".str_replace(" kontrak", "", $namaLabel )."</a></li>";
            // END Proses Kontrak

            $breadcrumb .= "<li class=\"breadcrumb-item\"> <a href=\"kontrak/index/contracting_detail?reqId=".$reqId."\">".ucwords(str_replace("_", " ", $pg))."</a></li>";
            $breadcrumb .= "<li class=\"breadcrumb-item\">".$linkBack."</li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item active\">Edit</li>";

        break;

        case 'master_daftar_user_non_rekanan_approve':
          $breadcrumb .= "<li class=\"breadcrumb-item active\">User Eproc</li>";
          break;

         case 'master_menu_approve':
          $breadcrumb .= "<li class=\"breadcrumb-item active\">Group & Role</li>";
          break;

        case 'master_daftar_user_non_rekanan':
        case 'master_user_rekanan':
        case 'master_group':
        case 'master_menu':
          $pg = str_replace("master_daftar_user_non_rekanan", "User eProc", $pg).'';
          $pg = str_replace("master_user_rekanan", "User Penyedia", $pg).'';
          $pg = str_replace("master_menu", "Group & Role", $pg).'';
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Master User</li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\">".str_replace("Master","",ucwords(str_replace("_", " ", $pg)))."</li>";

          $breadcrumb = str_replace("Kontak", "Kritik dan Saran", $breadcrumb);
          $breadcrumb = str_replace("Berita", "Berita dan Pengumuman", $breadcrumb);
          break;

        case 'master_menu':
        case 'master_berita':
          $pg = str_replace("master_menu", "Group & Role", $pg).'';
          $pg = str_replace("master_berita", "Berita dan Pengumuman", $pg).'';
          $pg = str_replace("master_sertifikat_jenis", "Jenis Sertifikat", $pg).'';
          $breadcrumb .= "<li class=\"breadcrumb-item active\">".str_replace("Master","",ucwords(str_replace("_", " ", $pg)))."</li>";
          break;

        case 'contracting_persiapan_legal':
          // $breadcrumb .= "<li class=\"breadcrumb-item\"> Inbox</li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Kontrak</li>";
          break;

        case 'master_bidang_usaha':
        case 'master_sk_panitia':
        case 'master_kontak':
        case 'master_unit_kerja':
        case 'master_payment_method':
        case 'master_bank':
        case 'master_banner':
        case 'master_rekanan_tipe':
        case 'master_tanggal_merah':
        case 'master_dokumen_template':
        case 'master_sertifikat_jenis':
        case 'master_vendor_retail':
          $pg = str_replace("master_user_rekanan", "User Penyedia", $pg).'';
          $pg = str_replace("master_menu", "Group & Role", $pg).'';
          $pg = str_replace("master_unit_kerja", "Perusahaan", $pg).'';
          $pg = str_replace("master_rekanan_tipe", "Bentuk Usaha", $pg).'';
          $pg = str_replace("master_vendor_retail", "Vendor Retail", $pg).'';
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Master Data</li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\">".str_replace("Master","",ucwords(str_replace("_", " ", $pg)))."</li>";

          $breadcrumb = str_replace("Kontak", "Kritik dan Saran", $breadcrumb);
          $breadcrumb = str_replace("Berita", "Berita dan Pengumuman", $breadcrumb);
          break;

        case 'inbox_rfi_add':
          // $breadcrumb .= "<li class=\"breadcrumb-item\"> Inbox</li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> RFI (Market Sounding)</li>";
          break;

        case 'inbox_rfi_penyedia_add':
          $breadcrumb .= "<li class=\"breadcrumb-item\"> Inbox</li>";
          $breadcrumb .= "<li class=\"breadcrumb-item\"> RFI (Market Sounding)</li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Jawab</li>";
          break;

        case 'inbox_rfi_penyedia':
          $breadcrumb .= "<li class=\"breadcrumb-item\"> Inbox RFI</li>";
          break;

        case 'inbox_survei':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Inbox Survey</li>";
          break;

        case 'inbox_survei_add':
          $breadcrumb .= "<li class=\"breadcrumb-item\"> Inbox Survey</li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Buat</li>";
          break;

        case 'inbox_survei_penyedia':
          $breadcrumb .= "<li class=\"breadcrumb-item\"> Inbox Survey</li>";
          break;

        case 'inbox_complain':
        case 'inbox_complain_penyedia';
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Bantuan</li>";
          break;

        case 'inbox_complain_penyedia_add';
          $breadcrumb .= "<li class=\"breadcrumb-item\"> Bantuan</li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Ajukan</li>";
          break;

        case 'inbox_complain_add':
          $breadcrumb .= "<li class=\"breadcrumb-item\"> Inbox Pertanyaan atau Pengajuan</li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Jawab</li>";
          break;

        case 'master_backup':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Backup</li>";
          break;

        case 'master_blacklist':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Blacklist</li>";
          break;

        case 'logs_file':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Logs </li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Aktifitas</li>";
          break;

        case 'logs_login':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Logs </li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Login</li>";
          break;

        case 'kontak':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Kontak Kami</li>";
          break;

        case 'kontak':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Kontak Kami</li>";
          break;

        case 'kontak':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Kontak Kami</li>";
          break;

        case 'kontak':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Kontak Kami</li>";
          break;

        case 'kontak':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Kontak Kami</li>";
          break;

        case 'kontak':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Kontak Kami</li>";
          break;

        case 'kontak':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Kontak Kami</li>";
          break;

        case 'data_keuangan_rekening_koran':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Data Keuangan </li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Rekening Koran</li>";
          break;

        case 'data_keuangan_rekening_koran_tambah':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Data Keuangan </li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Rekening Koran Form</li>";
          break;

        case 'data_perpajakan_neraca':
        case 'data_perpajakan_neraca_tambah':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Data Keuangan </li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Neraca</li>";
          break;

        case 'validasi':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> VaLidator ".LABEL_PENYEDIA."</li>";
          break;

          case 'beritad':
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/berita\">Berita</a></li>";
            $breadcrumb .= "<li class=\"breadcrumb-item active\"> Detail</li>";
            break;

        case 'validasi_rekanan':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> ValiDasi ".LABEL_PENYEDIA."</li>";
          break;

        case 'daftar_rekanan_belum_valid':
          // $breadcrumb .= "<li class=\"breadcrumb-item active\"> DaFtar ".LABEL_PENYEDIA."</li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Daftar ".LABEL_PENYEDIA."</li>";
          break;

        case 'daftar_rekanan_valid':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Daftar ".LABEL_PENYEDIA." Terverifikasi</li>";
          // $breadcrumb .= "<li class=\"breadcrumb-item active\"> Verifikasi</li>";
          break;

        case 'daftar_rekanan_potensi':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Pencarian Potensi ".LABEL_PENYEDIA."</li>";
          break;

        case 'daftar_rekanan_akses':
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/daftar_rekanan_valid\">Daftar ".LABEL_PENYEDIA." Terverifikasi</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Hak Akses</li>";
          break;

        case 'daftar_rekanan_potensi':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Potensi ".LABEL_PENYEDIA."</li>";
          break;

        case 'daftar_rekanan_delete':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Hapus ".LABEL_PENYEDIA."</li>";
          break;

        case 'daftar_rekanan_approval_rekomendasi':
        case 'daftar_rekanan_approval':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Approval ".LABEL_PENYEDIA."</li>";
          break;

        case 'pembelian_offline':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Pembelian Langsung</li>";
          break;

        case 'pembelian_langsung':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Pembelian Katalog</li>";
          break;

        case 'integration_monitoring':
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Monitoring Integrasi </li>";
          break;

        case 'integration_monitoring_rka':
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"integration/index/integration_monitoring\">Monitoring Integrasi</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> R K A</li>";
          break;

        case 'integration_monitoring_pr':
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"integration/index/integration_monitoring\">Monitoring Integrasi</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> P R</li>";
          break;

        case 'paket_detil':
          // 1-e-Tender ,3-Tender Terbatas, 7-e-Tender Cepat, 2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat, 6-Pembelian langsung
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else if ($paket_metode_lelang_id == '2' || $paket_metode_lelang_id == '5' || $paket_metode_lelang_id == '8') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          } else if ($paket_metode_lelang_id == '9') {
            if($this->_CI->USER_TYPE_ID == "11") {
              $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/pembelian_offline\">Pembelian Langsung</a></li>";
            } else {
              $breadcrumb .= "<li class=\"breadcrumb-item\">Pembelian Langsung</li>";
            }
          } else if ($paket_metode_lelang_id == '12') {
              $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/pembelian_pemerintah\">Pembelian Katalog Pemerintah</a></li>";
          }
          else {
            if($this->_CI->USER_TYPE_ID == "11") {
              $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/pembelian_langsung\">Pembelian Katalog</a></li>";
            } else {
              $breadcrumb .= "<li class=\"breadcrumb-item\">Pembelian Katalog</li>";
            }
          }
          $breadcrumb .= "<li class=\"breadcrumb-item active\"> Paket Detail</li>";
          break;

        case 'paket_lelang_tambah_negosiasi_undangan':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\">Setting Notifikasi Klarifikasi</li>";
        break;

        case 'paket_lelang_tambah_negosiasi_undangan_reverse':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\">Setting Notifikasi Klarifikasi</li>";
        break;

        case 'klarifikasi_chat':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\">Klarifikasi Dokumen Penawaran</li>";
        break;

        case 'klarifikasi_chat_tanggapan':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/klarifikasi_chat/?reqId=".$reqId."\">Klarifikasi Dokumen Penawaran</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\">Chat</li>";
        break;

        case 'klarifikasi_chat_rekanan':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\">Klarifikasi Dokumen Penawaran</li>";
        break;

        case 'purchasing_file':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else if ($paket_metode_lelang_id == '2' || $paket_metode_lelang_id == '5' || $paket_metode_lelang_id == '8') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          } else if ($paket_metode_lelang_id == '9') {
            if($this->_CI->USER_TYPE_ID == "11") {
              $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/pembelian_offline\">Pembelian Langsung</a></li>";
            } else {
              $breadcrumb .= "<li class=\"breadcrumb-item\">Pembelian Langsung</li>";
            }
          } else if ($paket_metode_lelang_id == '12') {
              $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/pembelian_pemerintah\">Pembelian Katalog Pemerintah</a></li>";
          }
          // $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\">Upload Dokumen</li>";
        break;

        case 'negosiasi_rekanan':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\">Negosiasi</li>";
        break;

        case 'pembelian_pemerintah':
          $breadcrumb .= "<li class=\"breadcrumb-item active\">Pembelian Katalog Pemerintah</li>";
        break;

        case 'aanwijzing_chat':
        case 'aanwijzing_kualifikasi_chat':
        case 'aanwijzing_kualifikasi_chat_rekanan':
        case 'aanwijzing_kualifikasi_chat_tanggapan':
        case 'evaluasi_penawaran_administrasi':
        case 'evaluasi_penawaran_teknis':
        case 'evaluasi_penawaran_harga':
        case 'evaluasi_penawaran_rekapitulasi':
        case 'evaluasi_penawaran_administrasi_sampul1':
        case 'evaluasi_penawaran_teknis_sampul1':
        case 'evaluasi_penawaran_rekapitulasi_sampul1':
        case 'evaluasi_penawaran_harga_sampul2':
        case 'evaluasi_penawaran_rekapitulasi_sampul2':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";

          // echo $pg;
          $pg = str_replace('penawaran', '', $pg);
          $pg = str_replace('evaluasi__administrasi_sampul1', 'Evaluasi Administrasi File 1', $pg);
          $pg = str_replace('aanwijzing_kualifikasi_chat_rekanan', 'Aanwijzing Kualifikasi', $pg);
          $pg = str_replace('evaluasi__teknis_sampul1', 'Evaluasi Teknis File1', $pg);
          $pg = str_replace('evaluasi__rekapitulasi_sampul1', 'Evaluasi Rekapitulasi File 1', $pg);
          $pg = str_replace('evaluasi__harga_sampul2', 'Evaluasi Harga File2', $pg);
          $pg = str_replace('evaluasi__rekapitulasi_sampul2', 'Evaluasi Rekapitulasi File 2', $pg);
          // echo $pg;

          $breadcrumb .= "<li class=\"breadcrumb-item active\">".ucwords(str_replace("_", " ", $pg))."</li>";

          break;

        case 'paket_lelang_tambah_daftar_peserta':
        case 'paket_lelang_tambah_kriteria_kualifikasi':
        case 'evaluasi_dokumen_kualifikasi':
        case 'paket_lelang_tambah_daftar_peserta_hasil';
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";

          // echo $pg;
          $pg = str_replace('paket_lelang_tambah_daftar_peserta', 'Daftar Peserta Tender', $pg);
          $pg = str_replace('paket_lelang_tambah_kriteria_kualifikasi', 'Syarat Dokumen Kualifikasi', $pg);
          $pg = str_replace('paket_lelang_tambah_daftar_peserta_hasil', 'Pengumuman Hasil Kualifikasi', $pg);
          // echo $pg;

          $breadcrumb .= "<li class=\"breadcrumb-item active\">".ucwords(str_replace("_", " ", $pg))."</li>";

          break;

        case 'aanwijzing_chat_tanggapan':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/aanwijzing_chat/?reqId=".$reqId."\">Aanwijzing Chat</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\">Jawab</li>";

          break;

        case 'paket_lelang_masa_sanggah_kualifikasi':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
          } else {
            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
          }
          $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
          $breadcrumb .= "<li class=\"breadcrumb-item active\">Sanggah Kualifikasi</li>";

          break;

        case 'paket_laporan_pengguna':
          if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
              $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
            } else {
              $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
            }
              $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
              $breadcrumb .= "<li class=\"breadcrumb-item active\"> Laporan Paket</li>";
          break;

        default:
          if (stristr($pg, "paket_lelang_tambah")) {
            $pg_monitoring = str_replace("paket_lelang_tambah", "", $pg);
            $pg_monitoring = str_replace("sampul1", "File 1", $pg_monitoring);
            $pg_monitoring = str_replace("sampul2", "File 2", $pg_monitoring);
            $pg_monitoring = str_replace("rekanan", "Penyedia", $pg_monitoring);
            // $monitoring = str_replace("_", "", $pg_monitoring);
            $pecahmonitoring = explode("_", $pg_monitoring);
            if (isset($pecahmonitoring[2])) {
              $monitoring = $pecahmonitoring[0].' '.$pecahmonitoring[1].' '.$pecahmonitoring[2];
            } else if (isset($pecahmonitoring[1])) {
              $monitoring = $pecahmonitoring[0].' '.$pecahmonitoring[1];
            } else {
              $monitoring = $pecahmonitoring[0];
            }

            // if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '7') {
            //   $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
            // } else {
            //   $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
            // }

            if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
              $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
            } else if ($paket_metode_lelang_id == '2' || $paket_metode_lelang_id == '5' || $paket_metode_lelang_id == '8') {
              $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
            } else if ($paket_metode_lelang_id == '9') {
              if($this->_CI->USER_TYPE_ID == "11") {
                $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/pembelian_offline\">Pembelian Langsung</a></li>";
              } else {
                $breadcrumb .= "<li class=\"breadcrumb-item\">Pembelian Langsung</li>";
              }
            }

            if ($pg_monitoring == '_rincian_pekerjaan_permohonan') {
              $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/permohonan_paket_fungsional\">Rencana Pengadaan</a></li>";
              // $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/permohonan_paket_fungsional_add?reqId=26\">Rencana Pengadaan Add</a></li>";
              $breadcrumb .= "<li class=\"breadcrumb-item active\">Upload BoQ</li>";
            } else {

              if($pg == "paket_lelang_tambah")
                $monitoring = "Informasi Paket";
              else
                $monitoring = "". str_replace("lelang", " Tender", $monitoring);

              // echo $monitoring;
              $monitoring = str_replace('rincian pekerjaan', 'BoQ', $monitoring);
              $monitoring = str_replace('Tender', 'Paket', $monitoring);
              $monitoring = str_replace('kriteria penawaran', 'Syarat Dokumen Penawaran', $monitoring);
              $monitoring = str_replace('panitia', 'Tim Pengadaan', $monitoring);
              $monitoring = str_replace('pembukaan auction', 'Pembukaan Penawaran', $monitoring);
              $monitoring = str_replace('penentuan pemenang', 'Penetapan Pemenang', $monitoring);

              if($pg != "paket_lelang_tambah") {
              $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
              }
              $breadcrumb .= "<li class=\"breadcrumb-item active\">".ucwords($monitoring)."</li>";
            }
          } elseif (stristr($pg, "data_administrasi")) {
            $pg_monitoring = str_replace("data_administrasi", "", $pg);
            $pg_monitoring = str_replace("_umum", "Profil Perusahaan", $pg);
            $breadcrumb .= "<li class=\"breadcrumb-item active\">Data Administrasi</li>";
            // $breadcrumb .= "<li class=\"breadcrumb-item active\">Profil Perusahaan</li>";
            $breadcrumb .= "<li class=\"breadcrumb-item active\">".str_replace('Data Administrasi','',ucwords(str_replace("_", " ", $pg)))."</li>";
          } elseif (stristr($pg, "data_perpajakan")) {
            $pg_monitoring = str_replace("data_perpajakan", "", $pg);
            $breadcrumb .= "<li class=\"breadcrumb-item active\">Data Perpajakan</li>";
            $breadcrumb .= "<li class=\"breadcrumb-item active\">".str_replace('Data Perpajakan','',ucwords(str_replace("_", " ", $pg)))."</li>";
          } elseif (stristr($pg, "data_teknis")) {
            $pg_monitoring = str_replace("data_teknis", "", $pg);
            $breadcrumb .= "<li class=\"breadcrumb-item active\">Data Teknis</li>";
            if ($pg == 'data_teknis_sertifikat_lain' || $pg == 'data_teknis_sertifikat_lain_tambah') {
              $breadcrumb .= "<li class=\"breadcrumb-item active\">Dokumen Teknis Perusahaan</li>";
            } else {
              $breadcrumb .= "<li class=\"breadcrumb-item active\">".str_replace('Data Teknis','',ucwords(str_replace("_", " ", $pg)))."</li>";
            }
          } elseif(stristr($pg, "evaluasi") || stristr($pg, "pembukaan_auction_rekanan") || stristr($pg, "dokumen_penawaran") || $pg == "dokumen_lelang_rekanan" || $pg == "data_kualifikasi" || $pg == "aanwijzing_chat_rekanan" || $pg == "pengumuman_pemenang_rekanan" || $pg == "auction_rekanan")
          {
            $pg_monitoring = str_replace("sampul1", "File 1", $pg);
            $pg_monitoring = str_replace("sampul2", "File 2", $pg_monitoring);
            $monitoring = str_replace("_", " ", $pg_monitoring);

            if ($paket_metode_lelang_id == '1' || $paket_metode_lelang_id == '3' || $paket_metode_lelang_id == '4' || $paket_metode_lelang_id == '7' || $paket_metode_lelang_id == '10') {
              $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Tender</a></li>";
            } else {
              $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tendernon\">Non Tender</a></li>";
            }
            $monitoring = str_replace('auction rekanan', 'Penawaran', $monitoring);
            $monitoring = str_replace('pengumuman pemenang rekanan', 'Pengumuman Pemenang', $monitoring);
            $monitoring = str_replace('auction rekanan', 'Auction', $monitoring);
            $monitoring = str_replace('password', '', $monitoring);
            $monitoring = str_replace('dokumen lelang rekanan', 'Dokumen Pengadaan', $monitoring);
            $monitoring = str_replace('dokumen penawaran boq', 'Nilai Penawaran', $monitoring);
            $monitoring = str_replace('dokumen penawaran rekanan', 'Dokumen Penawaran Penyedia', $monitoring);

            $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
            $breadcrumb .= "<li class=\"breadcrumb-item active\">".ucwords($monitoring)."</li>";
          }else {
            $breadcrumb .= "<li class=\"breadcrumb-item active\">".ucwords(str_replace("_", " ", $pg))."</li>";
          }
          break;
      }

      if ($pg == 'blacklist' || $pg == 'registrasi_rekanan_cv' || $pg == 'katalog_cart') {
        $replaceString = array('Valid','Belum','Lelang');
      } else {
        $replaceString = array('Valid','Belum','Daftar','Lelang');
      }

      $breadcrumb = str_replace($replaceString, "", $breadcrumb);

      // $arrayReplace = array('Fungsional');
      // $breadcrumb .= str_replace($arrayReplace, '', $breadcrumb);

      return $breadcrumb;

      if(stristr($pg, "evaluasi") || stristr($pg, "pembukaan_auction_rekanan") || stristr($pg, "dokumen_penawaran") || $pg == "dokumen_lelang_rekanan" || $pg == "data_kualifikasi" || $pg == "aanwijzing" || $pg == "negosiasi_rekanan" || $pg == "auction_rekanan")
      {
        $pg_monitoring = str_replace("sampul1", "File 1", $pg);
        $pg_monitoring = str_replace("sampul2", "File 2", $pg_monitoring);
        $monitoring = str_replace("_", " ", $pg_monitoring);

        $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/tender\">Paket Tender</a></li>";
        $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/paket_detil/?eid=".$reqId."&key=".$uuid."\">Paket Detail</a></li>";
        $breadcrumb .= "<li class=\"breadcrumb-item active\">".ucwords($monitoring)."</li>";
      }
      elseif($arrJudul[$max] == "detil")
      {
        $link_monitoring = str_replace("_detil", "", $pg);
        $monitoring = str_replace("_", " ", $link_monitoring);

        $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/".$link_monitoring."\">".ucwords(str_replace("lelang", " Tender", $monitoring))."</a></li>";
        $breadcrumb .= "<li class=\"breadcrumb-item active\"> Detil ".ucwords(str_replace("lelang", " Tender", $monitoring))."</li>";
      }
      elseif($arrJudul[$max] == "ubah" || $arrJudul[$max] == "tambah")
      {
        $link_monitoring = str_replace("_ubah", "", $pg);
        $link_monitoring = str_replace("_tambah", "", $link_monitoring);
        $monitoring = str_replace("_", " ", $link_monitoring);

        $breadcrumb .= "<li class=\"breadcrumb-item\"><a href=\"main/index/".$link_monitoring."\">".ucwords($monitoring)."</a></li>";
        $breadcrumb .= "<li class=\"breadcrumb-item active\"> Kelola ".ucwords($monitoring)."</li>";
      }
      else
      {  }
    }

    function unitkerja($id)
    {
      $this->_CI->load->model("Paket");
      $unitkerja = new Paket();
      $unitkerja->getUnitKerja($id);
      $unitkerja->firstRow();
      return $unitkerja->getField("NAMA");
    }

    function cetakcopyright($id)
    {
      $unitkerjanama = $this->unitkerja($id);
      $html  = '';
      $html .= $unitkerjanama.' menyatakan dokumen ini SAH dan dikeluarkan oleh sistem e-Procurement.';
      return $html;
    }

    function cetakcopyrightlogo($id)
    {
      $this->_CI->load->model("Paket");
      $unitkerja = new Paket();
      $unitkerja->getUnitKerja($id);
      $unitkerja->firstRow();
      return $unitkerja->getField("LOGO");
    }

}
