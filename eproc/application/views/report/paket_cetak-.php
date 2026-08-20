<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketRekanan");
$this->load->model("Rekanan");
$this->load->model("Paket");
$this->load->model("PermohonanPaket");
$this->load->model("PaketPenilaian");
$this->load->model("PaketPanitia");
$this->load->model("Paketpemenang");
$this->load->model("PaketAanwijzing");
$this->load->model("PaketTahap");
$this->load->model("PaketNegosiasiValidasi");
$this->load->model("Metode");
$this->load->model("PaketDokumen");
$this->load->model("PaketEvaluasiAdminTawar");
$this->load->model("PaketEvaluasiTeknisTawar");
$this->load->model("PaketEvaluasiHargaTawar");
$this->load->model("MatrixEvaluasi");
$this->load->model("RekananPaketPenawaran");
$this->load->model("PaketNegoisasi");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/string.func.php");
include_once("lib/phpqrcode/qrlib.php");

// Unit Kerja
$this->load->library("libbreadcrumb");
$unitkerjaid = $this->input->get("unitkerjaid");
// End Unit Kerja

$PNG_TEMP_DIR = 'uploads/';

$reqId = httpFilterGet("reqId");
/* create objects */
$paket                      = new Paket();
$paket_rekanan              = new PaketRekanan();
$paket_rekanan2             = new PaketRekanan();
$paket_rekanan_aanwijzing   = new PaketRekanan();
$paket_aanwijzing_rekanan   = new PaketAanwijzing();
$paket_rekanan_1sampul      = new PaketRekanan();
$paket_rekanan_2sampul      = new PaketRekanan();
$rekanan                    = new Rekanan();
$rekanan_ceknama            = new Rekanan();
$paket_panitia              = new PaketPanitia();
$paket_panitia2             = new PaketPanitia();
$paket_tahap                = new PaketTahap();
$paket_tahap_metode         = new PaketTahap();
$paket_negosiasi_validasi   = new PaketNegosiasiValidasi();
$metode                     = new Metode();
$paket_dokumen              = new PaketDokumen();
$paket_dokumen_sanggahan    = new PaketDokumen();
$paket_dokumen_sanggahan_count    = new PaketDokumen();
$paket_dokumen_tanggapan    = new PaketDokumen();
$paket_evaluasi_admin       = new PaketEvaluasiAdminTawar();
$paket_evaluasi_teknis      = new PaketEvaluasiTeknisTawar();
$paket_evaluasi_harga       = new PaketEvaluasiHargaTawar();
$matrix_evaluasi            = new MatrixEvaluasi();
$rekanan_paket_penawaran    = new RekananPaketPenawaran();
$getpaket_pemenang          = new Paketpemenang();

$paketInfo->getPaket($reqId);
$reqNama              = $paketInfo->nama;
$reqKualifikasi       = $paketInfo->kualifikasi;
$reqKualifikasiId     = $paketInfo->kualifikasi_id;
$reqMetodeLelangId    = $paketInfo->metode_lelang_id;
$reqNilai             = $paketInfo->nilai;
$reqJenisPekerjaanId  = $paketInfo->jenis_id;
$reqMetodeEvaluasiId  = $paketInfo->metode_evaluasi_id;
$reqJenisPekerjaan    = $paketInfo->jenis;
$reqMetodeEvaluasi    = $paketInfo->metode_evaluasi;
$reqSistemSampul      = $paketInfo->sistem_sampul;
$reqPublishBAPenawaranTanggal = $paketInfo->publish_ba_penawaran_tanggal; 
$reqMultiPemenang = $paketInfo->multi_pemenang; // Kontrak Payung

// Pemenang
$getpaket_pemenang->selectByParams(array("A.PAKET_ID" => $reqId), -1, -1);

// Rekanan
$rekanan->selectByParams(array("A.REKANAN_ID" => $paketInfo->rekanan_id_pemenang), -1, -1, '');
$rekanan->firstRow();
// Paket
$paket->selectByParamsMonitoring(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
$paket->firstRow();

$field = array('REKANAN', 'TANGGAL_UNDANG','TANGGAL_DAFTAR','LULUS_PENDAFTARAN', 'LULUS_KUALIFIKASI', 'LULUS_PENAWARAN', 'LULUS_PENAWARAN_URUT', 'STATUS_BAYAR');
$allrecord = $paket_rekanan->getCountByParams(array("PAKET_ID" => $reqId));
$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId));
$paket_rekanan2->selectByParams(array("PAKET_ID" => $reqId));

// Panitia
$paket_panitia->selectByParams2Group(array("PAKET_ID" => $reqId));
$paket_panitia2->selectByParams2Group(array("PAKET_ID" => $reqId));
// $paket_negosiasi_validasi->selectByParamsValidasi(array("A.PAKET_ID" => $reqId));
// Rekanan aanwijzing
// $paket_rekanan_aanwijzing->selectByParamsPaket(array("PAKET_ID" => $reqId), -1, -1, '');
$paket_aanwijzing_rekanan->selectByParamsPeserta2(array("A.PAKET_ID" => $reqId, 'A.PARENT_ID' => 0),'GROUP BY A.REKANAN_KODE, B.NAMA');
$i=0;
while($paket_aanwijzing_rekanan->nextRow())
{
  $arrRekanan[$i]["NAMA"] = strtoupper($paket_aanwijzing_rekanan->getField("REKANAN_KODE"));
  $arrRekanan[$i]["NAMAPT"] = $paket_aanwijzing_rekanan->getField("NAMA");
  // $arrRekanan[$i]["QRCODE"] = $php_shoutbox_rekanan->getField("KODE_REKANAN");
  $i++;
}
// Tanggal Aanwijzing
// $arrTahapan = array(0, 10, 5,  10, 5,  9,  5,  10, 10, 0, 0, 10, 5,  10, 5);
$arrTahapan = AANWIJZING;
$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
$paket_tahap->selectByParams(array("URUT" => $arrTahapan[$jenis_tahap], "PAKET_ID" => $reqId));
$paket_tahap->firstRow();
$time = strtotime($paket_tahap->getField("TANGGAL_AWAL"));
$aanwijzing_hari = date('w', $time);
$aanwijzing_tanggal = (int)date('d', $time);
$aanwijzing_bulan = (int)date('m', $time);
$aanwijzing_tahun = (int)date('Y', $time);
$aanwijzing_dmy = date('d-m-Y', $time);
$aanwijzing_ymd = date('Y-m-d', $time);

// Jadwal Pelelangan
$reqExistData = $metode->getCountByParams(array("PAKET_ID" => $reqId));
$metode->selectByParams(array(), -1, -1, $reqId);

// Dokumen Lelang
$paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "LELANG"));

//Kriteria Evaluasi Penawaran Paket Lelang
$paket_evaluasi_admin->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_teknis->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_harga->selectByParams(array("PAKET_ID" => $reqId));
$matrix_evaluasi->selectByParams(array("A.PAKET_JENIS_ID" => $reqJenisPekerjaanId, "A.PAKET_METODE_EVALUASI_ID" => $reqMetodeEvaluasiId));
$matrix_evaluasi->firstRow();

// Pembukaan Penawaran
$time2 = strtotime($reqPublishBAPenawaranTanggal);
$pembukaan_penawaran_hari = date('w', $time2);
$pembukaan_penawaran_tanggal = (int)date('d', $time2);
$pembukaan_penawaran_bulan = (int)date('m', $time2);
$pembukaan_penawaran_tahun = (int)date('Y', $time2);
$pembukaan_penawaran_dmy = date('d-m-Y', $time2);
$pembukaan_penawaran_ymd = date('Y-m-d', $time2);

 // 1 Sampul
$paket_rekanan_1sampul->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 AND A.KIRIM_PENAWARAN = 1 ");

 // 2 Sampul
$paket_rekanan_2sampul->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 AND A.KIRIM_PENAWARAN = 1 AND A.LULUS_PENAWARAN_SAMPUL1 = 1 ");

// Sanggahan
$paket_dokumen_sanggahan->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "SANGGAH", "PARENT_ID" => 0));
$pdsc = $paket_dokumen_sanggahan_count->getCountByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "SANGGAH", "PARENT_ID" => 0));

// if($paketInfo->publish_ba_penawaran == "")
//   exit;

$nomor = $paketInfo->pr_group_number."/LAPORAN.SUMREPORT/".getYear($paketInfo->tanggal);
?>

<html moznomarginboxes mozdisallowselectionprint>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<base href="<?=base_url()?>" />

<!-- <link rel="stylesheet" href="css/print.css" type="text/css"> -->

</head>

<body>

<div class="logo"><img src="images/<?= $this->libbreadcrumb->cetakcopyrightlogo($unitkerjaid) ?>" height="75" /></div>
<!-- <div class="logo"><img src="images/logo-cetak.png" height="75" /></div> -->
<div class="judul">
  BERITA ACARA HASIL PEMILIHAN
</div><br>

<!-- <div class="nomor" style="font-size: 12px">Nomor :  <?=$nomor?></div><br> -->

  <div style="margin-bottom:20px">
    <table border="0">
      <tr>
        <td align="left" valign="top" width="30%">Nama Pengadaan</td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"><?=$paket->getField('nama')?> </td>
      </tr>
      <tr>
        <td align="left" valign="top" width="30%">Kode RUP</td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"><?=$paket->getField('KODE_RUP')?> </td>
      </tr>
      <tr>
        <td align="left" valign="top" width="30%">Kode PR</td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"><?=$paket->getField('KODE_PR')?> </td>
      </tr>
      <tr>
        <td align="left" valign="top" width="30%">Tahun Anggaran</td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> <?=getYear($paket->getField('tanggal_tahap'))?> </td>
      </tr>
      <!-- <tr>
        <td align="left" valign="top" width="30%"> Unit Kerja </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> <?=$paket->getField('unit_kerja')?> </td>
      </tr>  -->
      <tr>
        <td align="left" valign="top" width="30%"> Lokasi Pekerjaan </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> <?=$paket->getField('lokasi')?> </td>
      </tr>
      <tr>
        <td align="left" valign="top" width="30%"> Jenis Pekerjaan  </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> <?=$paket->getField('paket_jenis')?> </td>
      </tr>
      <tr>
        <td align="left" valign="top" width="30%"> Metode Pengadaan </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> <?=$paket->getField('metode_lelang')?> </td>
      </tr>
      <!-- <tr>
        <td align="left" valign="top" width="30%"> Metode Kualifikasi </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> <?php //$paket->getField('metode_kualifikasi')?> </td>
      </tr>  -->
      <tr>
        <td align="left" valign="top" width="30%"> Metode Evaluasi </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> <?=$paket->getField('metode_evaluasi')?> </td>
      </tr>
      <tr>
        <td align="left" valign="top" width="30%"> Metode Penyampaian Penawaran </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> <?=$paket->getField('sistem_sampul')?> File</td>
      </tr>
      <tr>
        <td align="left" valign="top" width="30%"> Sistem Negosiasi </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%">
          <?php
          if ($paket->getField("BIDDING") == 1) {
            echo 'e-Reverse Auction '.$paket->getField("BIDDING_MENIT").' menit';
          } else {
            echo "Negosiasi";
          }
          ?>
        </td>
      </tr>
      <tr>
        <td align="left" valign="top" width="30%"> Kualifikasi Usaha</td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> <?=$paket->getField('rekanan_kualifikasi')?> </td>
      </tr>
      <tr>
        <!-- <td align="left" valign="top" width="30%"> Perkiraan Nilai Pekerjaan </td> -->
        <td align="left" valign="top" width="30%"> Harga Perkiraan </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> <?= $paket->getField('nilai_mata_uang').' '.currencyToPage($paket->getField('nilai'))?> </td>
      </tr>
      <tr>
        <td align="left" valign="top" width="30%"> Uraian Pekerjaan </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> <?=$paket->getField('uraian')?> </td>
      </tr>
      <tr>
        <td align="left" valign="top" width="30%"> Bidang Usaha </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%">
          <?php
          $exbd = explode(", ", $paket->getField('bidang_usaha'));
          foreach ($exbd as $value) {
            echo $value.'<br>';
          }
          ?>
        </td>
      </tr>
      <<!-- tr>
        <td align="left" valign="top" width="30%"> OE (Owner Estimate) </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> <?=currencyToPage($paket->getField('nilai_owner_estimate'))?> </td>
      </tr>   -->
    </table>
  </div>

    <!-- <b>COA :</b>
    <table class="table">
        <tr class="tr-bc">
          <td class="td">Nomor COA</td>
          <td class="td">Keterangan</td>
          <td class="td">Anggaran Awal</td>
          <td class="td">Anggaran Terpakai</td>
          <td class="td">Sisa Anggaran</td>
        </tr>
      <?php
      // $permohonan_paket_coa = new PermohonanPaket();
      // $permohonan_paket_coa->selectByParamsCoa(array("A.PERMOHONAN_PAKET_ID" => coalesce($paket->getField('permohonan_paket_id'), 0)));
      // if ($permohonan_paket_coa->countRow() > 0) {
      //   while($permohonan_paket_coa->nextRow())
      //   { 
        ?>
          <tr class="judul-kolom">
            <td class="td"><?php // $permohonan_paket_coa->getField("NOMOR") ?></td>
            <td class="td"><?php // $permohonan_paket_coa->getField("KETERANGAN") ?></td>
            <td class="td"><?php // numberToIna($permohonan_paket_coa->getField("BUDGET_AWAL")) ?></td>
            <td class="td"><?php // numberToIna($permohonan_paket_coa->getField("BUDGET_TERPAKAI")) ?></td>
            <td class="td"><?php // numberToIna($permohonan_paket_coa->getField("BUDGET_AKHIR")) ?></td>
          </tr>
        <?php
      //   }
      // } else {
      //   echo '<tr><td class="td" colspan="5">. : : Tidak ada data : : .</td></tr>';
      // }
      ?>
      </tbody>
    </table> -->

    <b>Syarat Dokumen Penawaran :</b>

      <?php
        if($reqSistemSampul == "2")
        {
        ?>
        <div class="font12">
          <b>FILE I</b>
        </div>
        <?php
        }
      ?>

      <?php
      if ($paket->getField('paket_metode_lelang_id') != '7') { ?>
      <div class="tr td">
        <b>Evaluasi Administrasi :</b>
      </div>
      <table class="table">
        <tr class="tr-bc">
          <td class="td" align="center" width="7%">No</td>
          <td class="td" align="center" width="83%">Uraian</td>
          <td class="td" align="center" width="10%">Wajib</td>
        </tr>
        <?php
          $iAdmin = 1;
          $style="gelap";
          while($paket_evaluasi_admin->nextRow())
          {
          ?>
          <tr class="<?=$style?>">
            <td class="td" align="center"><?=$iAdmin?></td>
            <td class="td"><?=$paket_evaluasi_admin->getField("NAMA")?></td>
            <td class="td" align="center"><?php if($paket_evaluasi_admin->getField("WAJIB") == '1') { echo 'Ya'; } else { echo 'Tidak'; } ?></td>
          </tr>
          <?php
            $iAdmin++;
          }
        ?>
      </table>

      <div class="tr td">
        <b>Evaluasi Teknis :</b>
      </div>
        <table class="table">
          <tr class="tr-bc">
            <td class="td" align="center" width="7%">No</td>
            <td class="td" align="center" width="83%">Uraian</td>
            <td class="td" align="center" width="10%">Wajib</td>
          </tr>
          <?php
            $iTeknis = 1;
            $style="gelap";
            while($paket_evaluasi_teknis->nextRow())
            {
          ?>
          <tr class="<?=$style?>">
            <td class="td" align="center"><?=$iTeknis?></td>
            <td class="td"><?=$paket_evaluasi_teknis->getField("NAMA")?></td>
            <td class="td" align="center"><?php if($paket_evaluasi_teknis->getField("WAJIB") == '1') { echo 'Ya'; } else { echo 'Tidak'; } ?></td>
          </tr>
          <?php
            $iTeknis++;
            }
          ?>
        </table>
      <?php
      } ?>

      <?php
      if($reqMetodeTenderId == "6")
      {}
      else
      {
        if($reqSistemSampul == "2") { ?>
          <div class="font12">
            <b>FILE II</b>
          </div>
      <?php } ?>
      <div class="tr td">
        <b>Evaluasi Harga :</b>
      </div>
        <table class="table">
          <tr class="tr-bc">
            <td class="td" align="center" width="7%">No</td>
            <td class="td" align="center" width="83%">Uraian</td>
            <td class="td" align="center" width="10%">Wajib</td>
          </tr>
          <?php
          $iHarga = 1;
          $style="gelap";
          while($paket_evaluasi_harga->nextRow())
          {
          ?>
          <tr class="<?=$style?>">
            <td class="td" align="center"><?=$iHarga?></td>
            <td class="td"><?=$paket_evaluasi_harga->getField("NAMA")?></td>
            <td class="td" align="center"> <?php if($paket_evaluasi_harga->getField("WAJIB") == '1') { echo 'Ya'; } else { echo 'Tidak'; } ?></td>
          </tr>
          <?php 
          $iHarga++;
          } ?>
        </table>
      <?php } ?>

      <!-- <div class="td">
        <b>Evaluasi Harga</b><br>
        <?=$matrix_evaluasi->getField("KETERANGAN_HARGA")?>
        <?php
        if ($paket->getField('paket_metode_lelang_id') != '7') { ?>
            <b>REKAPITULASI</b><br>
            <?=$matrix_evaluasi->getField("KETERANGAN_REKAP")?>
        <?php
        } ?>
      </div> -->

    <br>
    <b>Dokumen Pengadaan :</b>
    <table class="table">
      <tr class="tr-bc">
        <td class="tdno" align="center">No</td>
        <td class="td" align="center">Nama Dokumen</td>
        <td class="td" align="center">Tanggal Upload</td>
        <!-- <td class="td" align="center">File</td> -->
      </tr>
      <?php
        $i=1;
        while($paket_dokumen->nextRow())
        {
      ?>
      <tr >
          <td class="td" align="center"><?=$i?>.</td>
          <td class="td"><?=$paket_dokumen->getField("NAMA")?></td>
          <td class="td"><?=getFormattedDate($paket_dokumen->getField("TANGGAL_UPLOAD"))?></td>
          <!-- <td class="td"><?=$paket_dokumen->getField("PATH_FILE")?></td> -->
      </tr>
       <?php
        $i++;
      }
      ?>
    </table>

    <b>Jadwal :</b>
    <table class="table">
      <tr class="tr-bc">
        <td class="td" align="center">No</td>
        <td class="td" align="center">Tahap</td>
        <td class="td" align="center">Tanggal</td>
      </tr>
      <?php
        $i=1; $no=1; $stat = ''; $stat_m = '';
        while($metode->nextRow())
        {
            if($stat == '') $comma = '';  else  $comma = ', ';
            $stat .= $comma."#reqJamSelesai$i, #reqJamMulai$i";

            if($stat_m == '') $comma = '';  else  $comma = ', ';
            $stat_m .= $comma."#reqMenitSelesai$i, #reqMenitMulai$i";

            $disabledTanggalAwal = $metode->getField("TANGGAL_AWAL_DISABLED");
            $triggerTanggalAkhir = $metode->getField("TANGGAL_AKHIR_TRIGGER");
            ?>
            <tr valign="top" class="gelap">
              <td class="tdno" align="center"><?=$no?>.</td>
                <td class="td">
                  <?php
                    if($paketInfo->bidding == "1")
                        $namaJadwal = str_replace("Negosiasi", "e-Reverse Auction", $metode->getField("NAMA"));
                      else
                        $namaJadwal = $metode->getField("NAMA");
                    ?>

                    <?=$namaJadwal?>
                    <?php
                    $notif = "";
                    $notifikasi = $metode->getField("NOTIFIKASI");
                    if($notifikasi == "PENAWARAN")
                    {
                        if($metode->getField("HADIR_CENTANG") == 1)
                            $notif = "Pemasukan dokumen penawaran melalui offline";
                        else
                            $notif = "Pemasukan dokumen penawaran melalui online";
                    }
                    if($notifikasi == "")
                    {}
                    else
                    {
                    ?>
                    <br>
                    <label id="lblNotifikasi<?=$notifikasi?>" style="font-size:5px; font-weight:bold"><?=$notif?></label>
                    <?php
                    }
                    ?>
                </td>
                <td class="td" align="center">
                  <?php
                  if($i == 1 && $metode->getField("TANGGAL_AWAL") == '')
                      $tmpTanggalMulai = date("d-m-Y");
                  else
                      $tmpTanggalMulai = datetimeToPage($metode->getField("TANGGAL_AWAL"), "date");

                  $arrJamAwal = explode(":", $metode->getField("JAM_AWAL")); ?>
                  <?=$tmpTanggalMulai?>     <?=$arrJamAwal[0].':'?><?=$arrJamAwal[1]?>

                  <?php $arrJamAkhir = explode(":", $metode->getField("JAM_AKHIR")); ?>
                  <?=datetimeToPage($metode->getField("TANGGAL_AKHIR"), "date")?> <?=$arrJamAkhir[0]?><?= $arrJamAkhir[1] ? ':'.$arrJamAkhir[1] : ''?>
                </td>

            </tr>
        <?php
            $i++;
            $no++;
        }
        ?>
    </table>

    <b>
      <?php
  if ($reqMetodeLelangId == '2') {
    echo "Pengadaan Barang dan Jasa :";
   } else {
    echo "Pelaksana Pengadaan Barang dan Jasa :";
   } ?>
    </b>
    <table class="table">
      <tr class="tr-bc">
        <td class="td" align="center" width="5%">No</td>
        <td class="td" align="center">NPP</td>
        <td class="td" align="center">Nama</td>
        <td class="td" align="center">Jabatan</td>
      </tr>
      <?php
      $no=1;
        while($paket_panitia->nextRow())
        {
       ?>
      <tr>
        <td class="tdno" align="center"><?=$no?></td>
          <td class="td" align="left"><?=$paket_panitia->getField("NIP");?></td>
          <td class="td" align="left"><?=$paket_panitia->getField("NAMA");?></td>
          <td class="td" >
            <?php 
            if ($paket_panitia->getField("KETUA") == '1') {
              echo "Ketua";
            } else {
              echo "Anggota";
            } ?>
            <?php // echo $paket_panitia->getField("JABATAN").' (<b>'.$paket_panitia->getField("USER_JABATAN_PANITIA_STR").'</b>)'?>
              
          </td>
      </tr>
      <?php $no++; } ?>
    </table>


    <?php
    if ($paket->getField('paket_metode_lelang_id') == 7 || $paket->getField('paket_metode_lelang_id') == 1) { ?>

    <b>Daftar Peserta :</b>

    <table class="table">
      <tr class="tr-bc">
        <td class="td" align="center">No</td>
        <td class="td" align="center">Nama Perusahaan</td>
        <?php
        if ($paket->getField('paket_metode_lelang_id') != '7' && $paket->getField('paket_metode_lelang_id') != '1') { ?>
        <td class="td" align="center">Diundang</td>
        <?php
        } ?>
        <td class="td" align="center">Tanggal Daftar</td>
      </tr>
      <?php
      $no=1;
        while($paket_rekanan->nextRow())
        {
          $temp1=$temp2=$temp3=$temp4=$temp5='';
       ?>
      <tr>
        <td class="tdno" align="center"><?=$no?></td>
          <!-- <td class="td" align="center"><?php // $paket_rekanan->getField($field[0]);?></td> -->
          <td class="td" align="center"><?=$paket_rekanan->getField('FULL_NAMA_REKANAN');?></td>
          <?php
          if ($paket->getField('paket_metode_lelang_id') != '7' && $paket->getField('paket_metode_lelang_id') != '1') { ?>
          <td class="td" align="center"><?=getFormattedDateJson($paket_rekanan->getField($field[1]));?></td>
          <?php
          } ?>
          <td class="td" align="center"><?=getFormattedDateJson($paket_rekanan->getField($field[2]));?></td>
          <!-- <td align="center"><?php //$temp5;?></td> -->
      </tr>
      <?php $no++; } ?>
    </table>
  <?php
  } ?>

  <?php // while($paket_rekanan->nextRow()) {
  if ($paket->getField('paket_metode_lelang_id') != 7 && $paket->getField('paket_metode_lelang_id') != '5') { ?>
    <b>Daftar Peserta Aanwijzing :</b>
    <table class="table">
        <tr class="tr-bc">
          <td class="tdno">NO</td>
          <td class="td">Nama <?php if($paketInfo->bahasa == "EN") echo "/ <em>NAME</em>"; ?> Peserta</td>
          <!-- <td width="20%" style="text-align:center"><strong>APPROVAL QR CODE</strong></td> -->
        </tr>
        <?php
        // for($i=0;$i<count($arrRekanan);$i++)
        $ii=0;
        while($paket_rekanan2->nextRow())
        {
        ?>
        <tr>
            <td class="tdno"><?=($ii+1)?>.</td>
            <td class="td"><?= $paket_rekanan2->getField("KODE_CUT").' - '.$paket_rekanan2->getField("FULL_NAMA_REKANAN"); ?></td>
            <!-- <td class="td"><?php //$arrRekanan[$i]["NAMA"].' - '.$arrRekanan[$i]["NAMAPT"]?></td>  -->
        </tr>
        <?php
        $ii++;
        }
        ?>
    </table>
    <div class="font12" style="margin:5px 0 20px 0; border:1px solid #000; padding: 5px">
      <b>Tanggal Aanwijzing</b><br>
      <!-- <?php //strtoupper(getHari($aanwijzing_hari));?>, <?php //strtoupper(getTerbilang($aanwijzing_tanggal));?> <?php //strtoupper(getNameMonth($aanwijzing_bulan));?> <?php //strtoupper(getTerbilang($aanwijzing_tahun));?> <?php //$aanwijzing_dmy?>, mulai pukul <?php //(($paket_tahap->getField("JAM_AWAL") == "") ? '00:00' : $paket_tahap->getField("JAM_AWAL"))?> -->
      <?=strtoupper(getHari($aanwijzing_hari));?>,  <?=getFormattedDate($aanwijzing_dmy)?>, mulai pukul <?=(($paket_tahap->getField("JAM_AWAL") == "") ? '00:00' : $paket_tahap->getField("JAM_AWAL").'-'.$paket_tahap->getField("JAM_AKHIR"))?>
    </div>
  </div>
  <?php
  } ?>

    <b>Pembukaan Penawaran Pengadaan :</b>
  <?php if ($reqSistemSampul == '2') { // Dua Sampul ?>
      <br>
      <b>FILE 1 :</b>
    <?php
      $no_urut = 1;
      while($paket_rekanan_1sampul->nextRow())
      {
          $userRekanan = $paket_rekanan_1sampul->getField("REKANAN_ID");

          $paket_evaluasi_admin_tawar = new PaketEvaluasiAdminTawar();
          $paket_evaluasi_teknis_tawar = new PaketEvaluasiTeknisTawar();
          $paket_evaluasi_harga_tawar = new PaketEvaluasiHargaTawar();

          $paket_evaluasi_admin_tawar->selectByParamsRekananDokumen($userRekanan, array("A.PAKET_ID" => $reqId));
          $paket_evaluasi_teknis_tawar->selectByParamsRekananDokumen($userRekanan, array("A.PAKET_ID" => $reqId));
          $paket_evaluasi_harga_tawar->selectByParamsRekananDokumen($userRekanan, array("A.PAKET_ID" => $reqId));

      ?>

      <table class="table">
      <tr class="tr">
          <td colspan="5" class="padding5"><?=$no_urut?>. <?=$paket_rekanan_1sampul->getField("FULL_NAMA_REKANAN")?></td>
      </tr>
      <!-- <tr>
        <td colspan="5" class="td">
          <?php //if($paket_rekanan_1sampul->getField("KIRIM_PENAWARAN_LENGKAP") == "0") { ?>
            <font color="red">DOKUMEN PENAWARAN TIDAK LENGKAP -->
              <?php //if($paket_rekanan_1sampul->getField("KIRIM_PENAWARAN_ALASAN") == "") {}
                 //else
                  // echo "DENGAN ALASAN : ".strtoupper($paket_rekanan_1sampul->getField("KIRIM_PENAWARAN_ALASAN"));
              ?>
            <!-- </font> -->
          <?php //} else { ?>
            <!-- <font color="#367B35">DOKUMEN PENAWARAN LENGKAP <?php //if($paketInfo->bahasa == "EN") echo "/ <em>BID DOCUMENT COMPLETE</em>"; ?></font> -->
          <?php //} ?>
       <!--  </td>
      </tr> -->
      <tr class="tr-bc">
        <td class="td" align="center">No. <?php if($paketInfo->bahasa == "EN") echo "/ <em>Number</em>"; ?></td>
        <td class="td" align="center" style="width:40%">Nama Dokumen <?php if($paketInfo->bahasa == "EN") echo "/ <em>Documents Name</em>"; ?></td>
        <td class="td" align="center">Nama File <?php if($paketInfo->bahasa == "EN") echo "/ <em>File Name</em>"; ?></td>
        <td class="td" align="center">Ukuran <?php if($paketInfo->bahasa == "EN") echo "/ <em>File Size</em>"; ?></td>
        <td class="td" align="center">Tgl Upload <?php if($paketInfo->bahasa == "EN") echo "/ <em>Upload Date</em>"; ?></td>
      </tr>
      <tr class="tr">
        <!-- <td>I</td> -->
        <td colspan="5" class="td padding5">Dokumen Administrasi <?php if($paketInfo->bahasa == "EN") echo "/ <em>Administrative Documents</em>"; ?></td>
      </tr>
      <?php
      $id = 1;
      $i=1;
      $jumlahDokumenAdmin = 0;
      $jumlahUploadAdmin = 0;
      while($paket_evaluasi_admin_tawar->nextRow())
      {
      ?>
      <tr class="terang">
        <td class="tdno"><?=$i?>.</td>
        <td class="td"> <?=$paket_evaluasi_admin_tawar->getField("NAMA")?> </td>
        <td class="td font10"> <?=$paket_evaluasi_admin_tawar->getField("KETERANGAN")?> </td>
        <td class="td font10" align="right"> <?=round($paket_evaluasi_admin_tawar->getField("UKURAN") / 1024, 2)?> Kb </td>
        <td class="td font10"> <?=($paket_evaluasi_admin_tawar->getField("TANGGAL_UPLOAD"))?> </td>
      </tr>
      <?php
        $i++;
        $id++;
        $jumlahDokumenAdmin++;
      }
      ?>
      <tr class="tr">
        <!-- <td class="padding5">II</td> -->
        <td class="padding5" colspan="5">Dokumen Teknis <?php if($paketInfo->bahasa == "EN") echo "/ <em>Technical Documents</em>"; ?></td>
      </tr>
      <?php
      $i=1;
      $jumlahDokumenTeknis = 0;
      $jumlahUploadTeknis = 0;
      while($paket_evaluasi_teknis_tawar->nextRow())
      {
      ?>
      <tr class="terang">
        <td class="tdno"><?=$i?>.</td>
        <td class="td"> <?=$paket_evaluasi_teknis_tawar->getField("NAMA")?> </td>
        <td class="td font10"> <?=$paket_evaluasi_teknis_tawar->getField("KETERANGAN")?> </td>
        <td class="td font10" align="right"> <?=round($paket_evaluasi_teknis_tawar->getField("UKURAN") / 1024, 2)?> Kb </td>
        <td class="td font10"> <?=($paket_evaluasi_teknis_tawar->getField("TANGGAL_UPLOAD"))?> </td>
      </tr>
      <?php
        $i++;
        $id++;
        $jumlahDokumenTeknis++;
      }
      ?>
    </table><br>
    <?php
      unset($paket_evaluasi_admin_tawar);
      unset($paket_evaluasi_teknis_tawar);
      unset($paket_evaluasi_harga_tawar);
      $no_urut++;
    }
    ?>
    <!-- Ke 2 -->
    <div>
      <b>FILE 2 :</b>
    </div>
    <?php
    $no_urut2 = 1;
    while($paket_rekanan_2sampul->nextRow())
    {
      $userRekanan = $paket_rekanan_2sampul->getField("REKANAN_ID");

      $paket_evaluasi_admin_tawar = new PaketEvaluasiAdminTawar();
      $paket_evaluasi_teknis_tawar = new PaketEvaluasiTeknisTawar();
      $paket_evaluasi_harga_tawar = new PaketEvaluasiHargaTawar();

      $paket_evaluasi_admin_tawar->selectByParamsRekananDokumen($userRekanan, array("A.PAKET_ID" => $reqId));
      $paket_evaluasi_teknis_tawar->selectByParamsRekananDokumen($userRekanan, array("A.PAKET_ID" => $reqId));
      $paket_evaluasi_harga_tawar->selectByParamsRekananDokumen($userRekanan, array("A.PAKET_ID" => $reqId));
    ?>

    <table class="table">
      <tr class="tr-bc">
        <td colspan="5" class="padding5 td"><?=$no_urut2?>. <?=$paket_rekanan_2sampul->getField("FULL_NAMA_REKANAN")?></td>
      </tr>
      <tr class="tr">
        <td colspan="5" class="td">
          <?php if($paket_rekanan_2sampul->getField("NILAI_PENAWARAN") == "") {
            if($paketInfo->bahasa == "EN")
              echo "<font color=\"red\">DOKUMEN TIDAK DAPAT DIBUKA / TIDAK LENGKAP (DOCUMENTS CAN NOT OPEN / INCOMPLETE)</font>";
            else
              echo "<font color=\"red\">DOKUMEN TIDAK DAPAT DIBUKA / TIDAK LENGKAP</font>";
          } else { ?>
              Nilai Penawaran <?php if($paketInfo->bahasa == "EN") echo "/ <em>Bid Price</em>"; ?>: <?=$paketInfo->mata_uang?> <?=numberToIna($paket_rekanan_2sampul->getField("UNIT_PRICE"))?> (<?=terbilang($paket_rekanan_2sampul->getField("UNIT_PRICE"))?>) <br>
              Nilai Penawaran Koreksi<?php if($paketInfo->bahasa == "EN") echo "/ <em>Bid Price</em>"; ?>: <?=$paketInfo->mata_uang?> <?=numberToIna($paket_rekanan_2sampul->getField("JUMLAH_KOREKSI"))?> (<?=terbilang($paket_rekanan_2sampul->getField("JUMLAH_KOREKSI"))?>)
          <?php } ?>
       </td>
      </tr>
      <tr class="tr-bc">
        <td class="td" align="center">No. <?php if($paketInfo->bahasa == "EN") echo "/ <em>Number</em>"; ?></td>
        <td class="td" align="center" style="width:40%">Nama Dokumen <?php if($paketInfo->bahasa == "EN") echo "/ <em>Documents Name</em>"; ?></td>
        <td class="td" align="center">Nama File <?php if($paketInfo->bahasa == "EN") echo "/ <em>File Name</em>"; ?></td>
        <td class="td" align="center">Ukuran <?php if($paketInfo->bahasa == "EN") echo "/ <em>File Size</em>"; ?></td>
        <td class="td" align="center">Tgl Upload <?php if($paketInfo->bahasa == "EN") echo "/ <em>Upload Date</em>"; ?></td>
      </tr>
      <tr class="tr">
        <!-- <td>I</td> -->
        <td colspan="5" class="padding5">Dokumen Harga <?php if($paketInfo->bahasa == "EN") echo "/ <em>Financial Documents</em>"; ?></td>
      </tr>
      <?php
      $i=1;
      $jumlahDokumenHarga = 0;
      $jumlahUploadHarga = 0;
      while($paket_evaluasi_harga_tawar->nextRow())
      {
      ?>
      <tr class="terang">
        <td class="tdno"><?=$i?>.</td>
        <td class="td"> <?=$paket_evaluasi_harga_tawar->getField("NAMA")?> </td>
        <td class="td font10"> <?=$paket_evaluasi_harga_tawar->getField("KETERANGAN")?> </td>
        <td class="td font10" align="right"> <?=round($paket_evaluasi_harga_tawar->getField("UKURAN") / 1024, 2)?> Kb </td>
        <td class="td font10"> <?=($paket_evaluasi_harga_tawar->getField("TANGGAL_UPLOAD"))?> </td>
      </tr>
      <?php
        $i++;
        $id++;
        $jumlahDokumenHarga++;
      }
      ?>

    </table><br>
    <?php
      unset($paket_evaluasi_admin_tawar);
      unset($paket_evaluasi_teknis_tawar);
      unset($paket_evaluasi_harga_tawar);
      $no_urut2++;
    }
    ?>
  <?php } ?>

  <?php if ($reqSistemSampul == '1') { // Satu Sampul ?>
  <?php
    $no_urut = 1;
    while($paket_rekanan_1sampul->nextRow())
    {
      $userRekanan = $paket_rekanan_1sampul->getField("REKANAN_ID");

      $paket_evaluasi_admin_tawar = new PaketEvaluasiAdminTawar();
      $paket_evaluasi_teknis_tawar = new PaketEvaluasiTeknisTawar();
      $paket_evaluasi_harga_tawar = new PaketEvaluasiHargaTawar();

      $paket_evaluasi_admin_tawar->selectByParamsRekananDokumen($userRekanan, array("A.PAKET_ID" => $reqId));
      $paket_evaluasi_teknis_tawar->selectByParamsRekananDokumen($userRekanan, array("A.PAKET_ID" => $reqId));
      $paket_evaluasi_harga_tawar->selectByParamsRekananDokumen($userRekanan, array("A.PAKET_ID" => $reqId));
    ?>

    <table class="table">
      <tr class="tr">
        <td colspan="5" class="padding5"><?=$no_urut?>. <?=$paket_rekanan_1sampul->getField("FULL_NAMA_REKANAN")?></td>
      </tr>
      <tr class="tr-bc">
        <td colspan="5" class="td">
          <?php $nilaiPenawaran = $paket_rekanan_1sampul->getField("UNIT_PRICE");
          if($nilaiPenawaran == "") {
            if($paketInfo->bahasa == "EN")
              echo "<font color=\"red\">DOKUMEN TIDAK DAPAT DIBUKA / TIDAK LENGKAP (DOCUMENTS CAN NOT OPEN / INCOMPLETE)</font>";
            else
              echo "<font color=\"red\">DOKUMEN TIDAK DAPAT DIBUKA / TIDAK LENGKAP</font>";
          } else {
          ?>
          Nilai Penawaran <?php if($paketInfo->bahasa == "EN") echo "/ <em>Bid Price</em>"; ?>: <?=$paketInfo->mata_uang?> <?=numberToIna($nilaiPenawaran)?> (<?=terbilang($nilaiPenawaran)?>)<br>
          Nilai Penawaran Koreksi<?php if($paketInfo->bahasa == "EN") echo "/ <em>Bid Price</em>"; ?>: <?=$paketInfo->mata_uang?> <?=numberToIna($paket_rekanan_1sampul->getField("JUMLAH_KOREKSI"))?> (<?=terbilang($paket_rekanan_1sampul->getField("JUMLAH_KOREKSI"))?>)
          <?php } ?>
       </td>
      </tr>
      <tr class="tr-bc">
        <td class="tdno" align="center">No. <?php if($paketInfo->bahasa == "EN") echo "/ <em>Number</em>"; ?></td>
        <td class="td" align="center" style="width:40%">Nama Dokumen <?php if($paketInfo->bahasa == "EN") echo "/ <em>Documents Name</em>"; ?></td>
        <td class="td" align="center">Nama File <?php if($paketInfo->bahasa == "EN") echo "/ <em>File Name</em>"; ?></td>
        <td class="td" align="center">Ukuran <?php if($paketInfo->bahasa == "EN") echo "/ <em>File Size</em>"; ?></td>
        <td class="td" align="center">Tgl Upload <?php if($paketInfo->bahasa == "EN") echo "/ <em>Upload Date</em>"; ?></td>
      </tr>
    <?php
    if ($paket->getField('paket_metode_lelang_id') != 7)
    { ?>
      <tr class="tr">
        <!-- <td class="padding5">I</td> -->
        <td class="padding5" colspan="5">Dokumen Administrasi <?php if($paketInfo->bahasa == "EN") echo "/ <em>Administrative Documents</em>"; ?></td>
      </tr>
      <?php
      $id = 1;
      $i=1;
      $jumlahDokumenAdmin = 0;
      $jumlahUploadAdmin = 0;
      while($paket_evaluasi_admin_tawar->nextRow())
      {
      ?>
      <tr>
        <td class="tdno"><?=$i?>.</td>
        <td class="td"> <?=$paket_evaluasi_admin_tawar->getField("NAMA")?> </td>
        <td class="td font10"> <?=$paket_evaluasi_admin_tawar->getField("KETERANGAN")?> </td>
        <td align="right" class="td font10"> <?=round($paket_evaluasi_admin_tawar->getField("UKURAN") / 1024, 2)?> Kb </td>
        <td class="td font10"> <?=($paket_evaluasi_admin_tawar->getField("TANGGAL_UPLOAD"))?> </td>
      </tr>
      <?php
        $i++;
        $id++;
        $jumlahDokumenAdmin++;
      }
      ?>
      <tr class="tr">
        <!-- <td class="padding5">II</td> -->
        <td class="padding5" colspan="5">Dokumen Teknis <?php if($paketInfo->bahasa == "EN") echo "/ <em>Technical Documents</em>"; ?></td>
      </tr>
      <?php
      $i=1;
      $jumlahDokumenTeknis = 0;
      $jumlahUploadTeknis = 0;
      while($paket_evaluasi_teknis_tawar->nextRow())
      {
      ?>
      <tr class="terang">
        <td class="tdno"><?=$i?>.</td>
        <td class="td"> <?=$paket_evaluasi_teknis_tawar->getField("NAMA")?> </td>
        <td class="td font10"> <?=$paket_evaluasi_teknis_tawar->getField("KETERANGAN")?> </td>
        <td class="td font10" align="right"> <?=round($paket_evaluasi_teknis_tawar->getField("UKURAN") / 1024, 2)?> Kb </td>
        <td class="td font10"> <?=($paket_evaluasi_teknis_tawar->getField("TANGGAL_UPLOAD"))?> </td>
      </tr>
      <?php
        $i++;
        $id++;
        $jumlahDokumenTeknis++;
      }
      ?>
    <?php
    } ?>
      <tr class="tr">
        <!-- <td class="padding5">III</td> -->
        <td class="padding5" colspan="5">Dokumen Harga <?php if($paketInfo->bahasa == "EN") echo "/ <em>Financial Documents</em>"; ?></td>
      </tr>
      <?php
      $i=1;
      $jumlahDokumenHarga = 0;
      $jumlahUploadHarga = 0;
      while($paket_evaluasi_harga_tawar->nextRow())
      {
      ?>
      <tr class="terang">
        <td class="tdno"><?=$i?>.</td>
        <td class="td"> <?=$paket_evaluasi_harga_tawar->getField("NAMA")?> </td>
        <td class="td font10"> <?=$paket_evaluasi_harga_tawar->getField("KETERANGAN")?> </td>
        <td class="td font10" align="right"> <?=round($paket_evaluasi_harga_tawar->getField("UKURAN") / 1024, 2)?> Kb </td>
        <td class="td font10"> <?=($paket_evaluasi_harga_tawar->getField("TANGGAL_UPLOAD"))?> </td>
      </tr>
      <?php
        $i++;
        $id++;
        $jumlahDokumenHarga++;
      }
      ?>
    </table>
  <?php
  unset($paket_evaluasi_admin_tawar);
  unset($paket_evaluasi_teknis_tawar);
  unset($paket_evaluasi_harga_tawar);
  $no_urut++;
  }
  ?>

  <?php } ?>
  <!-- <div class="td">
      <b>Publish BA Penawaran</b><br> -->
      <!-- <?php //strtoupper(getHari($pembukaan_penawaran_hari));?>, <?php // strtoupper(getTerbilang($pembukaan_penawaran_tanggal));?> <?php // strtoupper(getNameMonth($pembukaan_penawaran_bulan));?> <?php // strtoupper(getTerbilang($pembukaan_penawaran_tahun));?> (<?php //  getFormattedDate($pembukaan_penawaran_ymd)?>)  -->
      <?php // strtoupper(getHari($pembukaan_penawaran_hari));?>  <?php // getFormattedDate($pembukaan_penawaran_ymd)?>
  <!-- </div>
  <br> -->

  <?php
  if ($pdsc >= 2) {
  ?>
  <b>Sanggahan :</b>
  <table class="table">
    <tbody>
      <?php
        $i=1;
        while($paket_dokumen_sanggahan->nextRow())
        {
          $rekanan_ceknama->selectByParams(array("REKANAN_ID" => $paket_dokumen_sanggahan->getField("REKANAN_USER_ID") ));
          $rekanan_ceknama->firstRow();
      ?>
      <tr class="tr-bc">
        <!-- <td class="td"><?=$i?>.</td> -->
        <!-- <td class="td" colspan="2"><?php // $paket_dokumen_sanggahan->getField("NMREKANAN")?></td> -->
        <td class="td" colspan="2"><?= $rekanan_ceknama->getField("NAMA")?></td>
      </tr>
      <tr>
        <td class="td" style="width: 200px">
          Tanya <br>
          <small class="font10"><?=getFormattedDate($paket_dokumen_sanggahan->getField("TANGGAL_UPLOAD"))?></small>
        </td>
        <td class="td"> <b><i><?=$paket_dokumen_sanggahan->getField("KETERANGAN")?></i></b></td>
        <!-- <td  class="td"><small><?=getFormattedDate($paket_dokumen_sanggahan->getField("TANGGAL_UPLOAD"))?></small></td>  -->
      </tr>
       <?php
          $paket_dokumen_tanggapan->selectByParams(array("PAKET_ID" => $paket_dokumen_sanggahan->getField("PAKET_ID"), "JENIS_DOKUMEN" => "SANGGAH", "PARENT_ID" => $paket_dokumen_sanggahan->getField("PAKET_DOKUMEN_ID") ));
          while($paket_dokumen_tanggapan->nextRow())
          { ?>
          <tr >
            <td class="td">
            Jawab <br>
            <small class="font10"><?=getFormattedDate($paket_dokumen_tanggapan->getField("TANGGAL_UPLOAD"))?></small>
           </td>
            <td class="td">  &#8594;  <?=$paket_dokumen_tanggapan->getField("KETERANGAN")?></td>
            <!-- <td class="td"><small><?=getFormattedDate($paket_dokumen_tanggapan->getField("TANGGAL_UPLOAD"))?></small></td>  -->
          </tr>
        <?php }
      $i++;
    }
    ?>
    </tbody>
  </table>
  <?php } else { ?>
  <!-- <div>
    <b>Tidak ada Sanggahan</b>
  </div>  -->
  <?php } ?>

  <?php
  if($paketInfo->bidding == "1")
    echo 'e-Reverse Auction :';
  else
    echo 'Negosiasi :';
  ?>
    <?php
    if($paketInfo->bidding == "1"){
    ?>
    <table class="table">
      <tr class="tr-bc">
        <td class="td">Nama Peserta</td>
        <td class="td">Harga e-Reverse Auction</td>
      </tr>
      <?php
      $paket_rekanan = new PaketRekanan();
      $paket_rekanan->selectUrutPenawaran2(array("A.PAKET_ID" => $reqId, "A.LULUS_PENDAFTARAN" => 1, "A.KIRIM_PENAWARAN" => 1, "A.LULUS_PENAWARAN" => 1), -1, -1, "", "", " ORDER BY NILAI_PENAWARAN ASC, KIRIM_PENAWARAN_TANGGAL ASC ");
       $urut=1;
      while($paket_rekanan->nextRow())
      {
      ?>
        <tr>
          <td class="td"><?=$paket_rekanan->getField("NAMA")?></td>
          <td class="td"><?=numberToIna($paket_rekanan->getField("NILAI_PENAWARAN"))?></td>
        </tr>
      <?php
       $urut++;
      }
      ?>
    </table>
    <?php
    } else {
      $paket_negosiasi = new PaketNegoisasi();
      $paket_negosiasi->selectByParams(array("A.PAKET_ID" => $reqId));
      $paket_negosiasi->firstRow();
      $penawaranNegosiasi =  $paket_negosiasi->getField("UNIT_PRICE");
      $jumlahNegosiasi =  $paket_negosiasi->getField("TOTAL");
      $setujui =  $paket_negosiasi->getField("SETUJUI");
      ?>
      <table class="table">
      <tr class="tr-bc">
        <td class="td">Nama Penyedia</td>
        <td class="td">Nilai Negosiasi</td>
      </tr>
        <tr>
          <td class="td"><?=$rekanan->getField("NAMA")?></td>
          <td class="td"><?=numberToIna($penawaranNegosiasi)?></td>
        </tr>
    </table>
    <?php
    }?>


  <b>Data Pemenang <?=$paket->getField('metode_lelang')?>:</b>
  <div class="td">
    <!-- <div style="margin:5px 0 20px 0; border:1px solid #000; padding: 5px">
      <b>Nama Pemenang : <?php //$rekanan->getField("NAMA") ?></b> <br>
    </div>   -->
    <?php
    while($getpaket_pemenang->nextRow())
    { ?>
    <table class="table">
      <tr>
        <td colspan="2" style="font-weight: bold"> <?php if ($reqMultiPemenang == '0') { echo "Urutan"; } else { echo "Pemenang"; } ?> <?= $getpaket_pemenang->getField("PERINGKAT") ?></td>
      </tr>
      <tr>
        <td style="width: 20%">Nama Perusahaan</td><td align="left">:&nbsp;&nbsp; <?= $getpaket_pemenang->getField("NAMA") ?></td>
      </tr>
      <tr>
        <td>NPWP</td><td align="left">:&nbsp;&nbsp; <?= $getpaket_pemenang->getField("NPWP") ?></td>
      </tr>
      <tr>
        <td>Alamat</td><td align="left">:&nbsp;&nbsp; <?= $getpaket_pemenang->getField("ALAMAT") ?></td>
      </tr>
    </table>
    <?php
    } ?>
  </div>
  <br>

  <table class="table" style="margin-top:10%">
      <tr class="tr-bc">
        <td class="td" align="center">Nama</td>
        <td class="td" align="center" width="250px">TTD</td>
      </tr>
      <?php
      $no=1;
        while($paket_panitia2->nextRow())
        {
       ?>
      <tr> 
          <td class="td" align="left" height="100px">
            <?=$paket_panitia2->getField("NAMA");?><br><span style="font-size:10px">NPP: <?=$paket_panitia2->getField("NIP");?></span>
          </td>
          <td class="td" ></td>
      </tr>
      <?php $no++; } ?>
    </table>

  <div class="nomor-oe">
    <div class="data" style="font-size:10px; font-style:italic">
     <?= $this->libbreadcrumb->cetakcopyright($unitkerjaid) ?>
     <?php if($paketInfo->bahasa == "EN") echo "<br><br><em>".SYSTEM_SAH_EN."</em>"; ?>
    </div>

</body>
</html>
