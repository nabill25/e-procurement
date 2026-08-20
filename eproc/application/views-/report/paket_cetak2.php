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
$this->load->library("libgeneratecode"); $libgeneratecode = new libgeneratecode();

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
$this->load->model("RekananEvaluasiAdminTawar");
$this->load->model("RekananEvaluasiTeknisTawar");
$this->load->model("RekananEvaluasiHargaTawar");

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
$paket_aanwijzing           = new PaketAanwijzing();
$paket_aanwijzing_first     = new PaketAanwijzing();
$paket_aanwijzing_hadir     = new PaketAanwijzing();

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
$reqAlasanBatal = $paketInfo->alasan_batal;
$reqAlasanGagal = $paketInfo->alasan_gagal;

// Pemenang
$getpaket_pemenang->selectByParams(array("A.PAKET_ID" => $reqId), -1, -1);

// if($paketInfo->bidding == "1"){
// } else {
//   // Rekanan
//   $rekanan->selectByParams(array("A.REKANAN_ID" => $paketInfo->rekanan_id_pemenang), -1, -1, '');
//   $rekanan->firstRow();
// }
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
// $paket_aanwijzing_rekanan->selectByParamsPeserta2(array("A.PAKET_ID" => $reqId, 'A.PARENT_ID' => 0),'GROUP BY A.REKANAN_KODE, B.NAMA');
// $i=0;
// while($paket_aanwijzing_rekanan->nextRow())
// {
//   $arrRekanan[$i]["NAMA"] = strtoupper($paket_aanwijzing_rekanan->getField("REKANAN_KODE"));
//   $arrRekanan[$i]["NAMAPT"] = $paket_aanwijzing_rekanan->getField("NAMA");
//   // $arrRekanan[$i]["QRCODE"] = $php_shoutbox_rekanan->getField("KODE_REKANAN");
//   $i++;
// }

$paket_aanwijzing->selectByParams(array("PAKET_ID" => $reqId, "PARENT_ID" => 0));
$paket_aanwijzing_first->selectByParams(array("PAKET_ID" => $reqId, "PARENT_ID" => 0));
$totalAan = $paket_aanwijzing_first->firstRow();
$paket_aanwijzing_hadir->selectByParams(array("PAKET_ID" => $reqId, "PARENT_ID" => 0));
$paket_aanwijzing_rekanan->selectByParamsPeserta3(array("A.PAKET_ID" => $reqId, 'A.PARENT_ID' => 0),'GROUP BY A.REKANAN_KODE, A.REKANAN_USER_ID');
$i=0;
if ($paket_aanwijzing_rekanan->countRow() > 0) {
  while($paket_aanwijzing_rekanan->nextRow())
  {
    $arrRekanan[] = $paket_aanwijzing_rekanan->getField("NAMA_PENYEDIA").'<br><small>'.strtoupper($paket_aanwijzing_rekanan->getField("KODE_CUT")).'</small>';
    // $arrRekanan[$i]["NIP"] = $php_shoutbox_rekanan->getField("KODE");
    // $arrRekanan[$i]["QRCODE"] = $php_shoutbox_rekanan->getField("KODE_REKANAN");
    $i++;
  }
} else {
  $arrRekanan[] = ". : Tidak ada data : .";
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

// $nomor = $paketInfo->pr_group_number."/LAPORAN.SUMREPORT/".getYear($paketInfo->tanggal);
$nomor = $libgeneratecode->bahp($reqId,$reqMetodeLelangId);

?>

<html moznomarginboxes mozdisallowselectionprint>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<base href="<?=base_url()?>" />
</head>

<body>

<div style="width: 100%; padding:20px 5px">

  <p style="text-align: center;"><img src="images/<?= $this->libbreadcrumb->cetakcopyrightlogo($unitkerjaid) ?>" height="75" width="100" /></p>
  <p style="text-align: center;"><?= ucwords('Berita Acara Hasil '.$paket->getField('metode_lelang')) ?><br>
    <span style="text-align: center; font-size: 10px"> Nomor :  <?=$nomor?></span>
  </p>


  <p>
    <?php
    $time = strtotime(date('Y-m-d'));
    $harinya = date('w', $time);
   ?>
  Pada hari ini <?= ucwords(getHari($harinya));?>, <?= getFormattedDateView(date('d-m-Y')) ?>, telah dibuat Berita Acara Hasil <?= ucwords($paket->getField('metode_lelang')) ?> untuk paket pekerjaan berikut:
  </p>

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
        <td align="left" valign="top" width="30%"> Persyaratan </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> <?=$paket->getField('uraian')?> </td>
      </tr>
      <?php
      if ($reqAlasanBatal != "")
      {?>
      <tr>
        <td align="left" valign="top" width="30%"> Paket Dibatalkan </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> <?=$reqAlasanBatal?> </td>
      </tr>
      <?php
      } ?>

      <?php
      if ($reqAlasanGagal != "")
      {?>
      <tr>
        <td align="left" valign="top" width="30%"> Paket Gagal </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%"> <?=$reqAlasanGagal?> </td>
      </tr>
      <?php
      } ?>
    </table>
  </div>



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
        <b>Dokumen Administrasi :</b>
      </div>
      <table>
        <tr>
          <td style="border: 1px solid #b7b7b7; width: 50px; text-align: center;">No</td>
          <td style="border: 1px solid #b7b7b7; width: 500px;">Uraian</td>
          <td style="border: 1px solid #b7b7b7; width: 100px; text-align: center;">Wajib</td>
        </tr>
        <?php
          $iAdmin = 1;
          $style="gelap";
          while($paket_evaluasi_admin->nextRow())
          {
          ?>
          <tr class="<?=$style?>">
            <td style="border: 1px solid #b7b7b7; text-align: center;"><?=$iAdmin?></td>
            <td style="border: 1px solid #b7b7b7;"><?=$paket_evaluasi_admin->getField("NAMA")?></td>
            <td style="border: 1px solid #b7b7b7; text-align: center;"><?php if($paket_evaluasi_admin->getField("WAJIB") == '1') { echo 'Ya'; } else { echo 'Tidak'; } ?></td>
          </tr>
          <?php
            $iAdmin++;
          }
        ?>
      </table>

      <br>
      <div class="tr td">
        <b>Dokumen Teknis :</b>
      </div>
        <table>
          <tr>
            <td style="border: 1px solid #b7b7b7; width: 50px; text-align: center;">No</td>
            <td style="border: 1px solid #b7b7b7; width: 500px;">Uraian</td>
            <td style="border: 1px solid #b7b7b7; width: 100px; text-align: center;">Wajib</td>
          </tr>
          <?php
            $iTeknis = 1;
            $style="gelap";
            while($paket_evaluasi_teknis->nextRow())
            {
          ?>
          <tr class="<?=$style?>">
            <td style="border: 1px solid #b7b7b7; text-align: center;"><?=$iTeknis?></td>
            <td style="border: 1px solid #b7b7b7;"><?=$paket_evaluasi_teknis->getField("NAMA")?></td>
            <td style="border: 1px solid #b7b7b7; text-align: center;"><?php if($paket_evaluasi_teknis->getField("WAJIB") == '1') { echo 'Ya'; } else { echo 'Tidak'; } ?></td>
          </tr>
          <?php
            $iTeknis++;
            }
          ?>
        </table>
      <?php
      } ?>

      <br>
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
        <b>Dokumen Harga :</b>
      </div>
        <table>
          <tr>
            <td style="border: 1px solid #b7b7b7; width: 50px; text-align: center;">No</td>
            <td style="border: 1px solid #b7b7b7; width: 500px;">Uraian</td>
            <td style="border: 1px solid #b7b7b7; width: 100px; text-align: center;">Wajib</td>
          </tr>
          <?php
          $iHarga = 1;
          $style="gelap";
          while($paket_evaluasi_harga->nextRow())
          {
          ?>
          <tr class="<?=$style?>">
            <td style="border: 1px solid #b7b7b7; text-align: center;"><?=$iHarga?></td>
            <td style="border: 1px solid #b7b7b7;"><?=$paket_evaluasi_harga->getField("NAMA")?></td>
            <td style="border: 1px solid #b7b7b7; text-align: center;"> <?php if($paket_evaluasi_harga->getField("WAJIB") == '1') { echo 'Ya'; } else { echo 'Tidak'; } ?></td>
          </tr>
          <?php
          $iHarga++;
          } ?>
        </table>
      <?php } ?>

    <br>
    <b>Daftar Peserta :</b>
    <table>
      <tr>
        <td style="border: 1px solid #b7b7b7; width: 50px; text-align: center;">No</td>
        <td style="border: 1px solid #b7b7b7; width: 500px;">Nama Peserta <?= $paketInfo->metode_lelang_nama ?></td>
          <?php
        if ($paketInfo->metode_lelang_id == '2' || $paketInfo->metode_lelang_id == '3' || $paketInfo->metode_lelang_id == '5' || $paketInfo->metode_lelang_id == '6' || $paketInfo->metode_lelang_id == '8' ) { ?>
        <td style="border: 1px solid #b7b7b7; width: 500px;">Diundang</td>
        <?php
        } ?>
         <?php
        if ($paketInfo->metode_lelang_id == '1'|| $paketInfo->metode_lelang_id == '7' || $paketInfo->metode_lelang_id == '10' ) { ?>
        <td style="border: 1px solid #b7b7b7; width: 150px; text-align: center;">Tanggal Daftar</td>
        <?php
        } ?>
      </tr>
      <?php
      $no=1;
        while($paket_rekanan->nextRow())
        {
       ?>
      <tr>
        <td style="border: 1px solid #b7b7b7; text-align: center;"><?=$no?></td>
        <td style="border: 1px solid #b7b7b7;"><?=$paket_rekanan->getField('FULL_NAMA_REKANAN');?></td>
        <?php
        if ($paketInfo->metode_lelang_id != '1' && $paketInfo->metode_lelang_id != '7' && $paketInfo->metode_lelang_id != '10' ) { ?>
        <td style="border: 1px solid #b7b7b7;"> <?=getFormattedDate($paket_rekanan->getField("TANGGAL_UNDANG"))?> </td>
        <?php
        } else { ?>
        <td style="border: 1px solid #b7b7b7; text-align: center;"> <?=getFormattedDate($paket_rekanan->getField("TANGGAL_DAFTAR")).' <br><sup> '.$paket_rekanan->getField("JAM_DAFTAR").'</sup>'?>
        </td>
        <?php
        } ?>
      </tr>
      <?php $no++; } ?>
    </table>

  <br>
    <!-- CR 06-01-2025 -->
  <?php // while($paket_rekanan->nextRow()) {
  if ($paket->getField('paket_metode_lelang_id') != 7) // Selain Tender Cepat
  { ?>
    <b>Daftar Peserta Aanwijzing :</b>
    <table>
      <thead>
        <tr>
          <th style="border: 1px solid #b7b7b7; width: 50px; text-align: center;">No</th>
          <th style="border: 1px solid #b7b7b7; width: 600px">NAMA</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $i = 0;
          foreach ($arrRekanan as $key => $value) {
          ?>
          <tr>
              <td style="border: 1px solid #b7b7b7;"><?= ($i+1)?>.</td>
              <td style="border: 1px solid #b7b7b7;">
                <?= $value; ?> <?php //$arrRekanan[$i]["NAMA"]?></td>
          </tr>
          <?php
          $i++;
          }
        ?>
      </tbody>
    </table>

    <br>
    <b>Aanwijzing:</b>
    <table>
      <thead>
        <tr>
          <th style="border: 1px solid #b7b7b7; width: 150px; text-align: center;"># ID</th>
          <th style="border: 1px solid #b7b7b7; width: 400px">Tanya/Jawab</th>
          <th style="border: 1px solid #b7b7b7; width: 100px; text-align: center;">Tanggal</th>
        </tr>
      </thead>
      <tbody>
        <?php
          if ($totalAan=='') {
              echo '<tr><td colspan="6">. : Tidak ada data : .</td></tr>';
            } else {
            $i=1;
            while($paket_aanwijzing->nextRow())
            {
              $tglupload = explode('.', $paket_aanwijzing->getField("TANGGAL_UPLOAD"));
              if ($i=1) {
                  $reqRekananUserId = $paket_aanwijzing->getField("REKANAN_USER_ID");
              }
              // Get Parent
              $paket_aanwijzing_parent_first = new PaketAanwijzing();
              $paket_aanwijzing_parent_first->selectByParams(array("PAKET_ID" => $reqId, "PARENT_ID" => $paket_aanwijzing->getField("PAKET_AANWIJZING_ID") ));
              $paket_aanwijzing_parent_first->firstRow();
          ?>
          <tr>
              <td style="border: 1px solid #b7b7b7;">
                <?php
                if ($paket_aanwijzing->getField("REKANAN_USER_ID") == $this->REKANAN_ID) {
                  echo '<i class="fa fa-user" style="color:#000; opacity:1"></i> <sup>'. $paket_aanwijzing->getField("KODE_CUT") .'</sup>' ;
                } else {
                  echo '<i class="fa fa-user" style="color:#000; opacity:1"></i> <sup>'. $paket_aanwijzing->getField("KODE_CUT") .'</sup>' ;
                }
                echo '<br><small>'.$paket_aanwijzing->getField("NAMA_PENYEDIA").'</small>';
                ?>
              </td>
              <td style="border: 1px solid #b7b7b7;">
                  <?=$paket_aanwijzing->getField("KETERANGAN")?>
              </td>
              <td style="border: 1px solid #b7b7b7;">
                  <small><i class="fa fa-clock-o"></i> <?=$tglupload[0] ?></small>
              </td>
          </tr>
          <?php
          // if (count($paket_aanwijzing_parent->nextRow())>=1) {
            $paket_aanwijzing_parent = new PaketAanwijzing();
              $paket_aanwijzing_parent->selectByParams(array("PAKET_ID" => $reqId, "PARENT_ID" => $paket_aanwijzing->getField("PAKET_AANWIJZING_ID") ));
            while($paket_aanwijzing_parent->nextRow())
              {
              $tglupload_parent = explode('.', $paket_aanwijzing_parent->getField("TANGGAL_UPLOAD"));
          ?>
            <tr >
              <td  style="border: 1px solid #b7b7b7;"><i class="fa fa-arrow-right" aria-hidden="true"></i>
                <?php
              if ($reqMetodeLelangId == '2') {
               } else {
                echo "PANITIA PENGADAAN BARANG DAN JASA :";
               } ?>
                <small class="font10">Jawab<i><b> <?=$paket_aanwijzing->getField("KODE_CUT")?> </b></i></small>
              </td>
              <td style="border: 1px solid #b7b7b7;">
                  <?=$paket_aanwijzing_parent->getField("KETERANGAN")?>
              </td>
              <td style="border: 1px solid #b7b7b7;">
                <?=$tglupload_parent[0] ?>
              </td>
          </tr>
          <?php }
          $i++;
          }
        }
        ?>
      </tbody>
    </table>
  </div>
  <?php
  } ?>

    <br>
    <b>Pembukaan Penawaran :</b>
    <?php if ($reqSistemSampul == '2') { // Dua Sampul ?>
      <b>FILE 1</b>
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

      <table>
      <tr>
          <td colspan="3" class="padding5"><?=$no_urut?>. <?=$paket_rekanan_1sampul->getField("FULL_NAMA_REKANAN")?></td>
      </tr>
      <tr>
        <td style="border: 1px solid #b7b7b7; width: 50px">No. <?php if($paketInfo->bahasa == "EN") echo "/ <em>Number</em>"; ?></td>
        <td style="border: 1px solid #b7b7b7; width: 500px">Nama Dokumen <?php if($paketInfo->bahasa == "EN") echo "/ <em>Documents Name</em>"; ?></td>
        <td style="border: 1px solid #b7b7b7; width: 100px; text-align: center;">Upload <?php if($paketInfo->bahasa == "EN") echo "/ <em>Upload</em>"; ?></td>
      </tr>
      <tr>
        <!-- <td>I</td> -->
        <td style="border: 1px solid #b7b7b7;" colspan="3">Dokumen Administrasi <?php if($paketInfo->bahasa == "EN") echo "/ <em>Administrative Documents</em>"; ?></td>
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
        <td style="border: 1px solid #b7b7b7;"><?=$i?>.</td>
        <td style="border: 1px solid #b7b7b7;"> <?=$paket_evaluasi_admin_tawar->getField("NAMA")?> </td>
        <td style="border: 1px solid #b7b7b7; text-align: center;">
          <?php
          if($paket_evaluasi_admin_tawar->getField("PATH_FILE") == "") {
            echo "Tidak";
          } else {
            echo "Ya";
          }?>
        </td>
      </tr>
      <?php
        $i++;
        $id++;
        $jumlahDokumenAdmin++;
      }
      ?>
      <tr>
        <!-- <td class="padding5">II</td> -->
        <td style="border: 1px solid #b7b7b7;" class="padding5" colspan="3">Dokumen Teknis <?php if($paketInfo->bahasa == "EN") echo "/ <em>Technical Documents</em>"; ?></td>
      </tr>
      <?php
      $i=1;
      $jumlahDokumenTeknis = 0;
      $jumlahUploadTeknis = 0;
      while($paket_evaluasi_teknis_tawar->nextRow())
      {
      ?>
      <tr>
        <td style="border: 1px solid #b7b7b7;"><?=$i?>.</td>
        <td style="border: 1px solid #b7b7b7;"> <?=$paket_evaluasi_teknis_tawar->getField("NAMA")?> </td>
        <td style="border: 1px solid #b7b7b7; text-align: center;">
          <?php
          if($paket_evaluasi_teknis_tawar->getField("PATH_FILE") == "") {
            echo "Tidak";
          } else {
            echo "Ya";
          }?>
        </td>
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
      <b>FILE 2</b>
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

    <table>
      <tr>
        <td style="border: 1px solid #b7b7b7;" colspan="3" class="padding5 td"><?=$no_urut2?>. <?=$paket_rekanan_2sampul->getField("FULL_NAMA_REKANAN")?></td>
      </tr>
      <tr>
        <td colspan="3" style="border: 1px solid #b7b7b7;">
          <?php if($paket_rekanan_2sampul->getField("NILAI_PENAWARAN") == "") {
            if($paketInfo->bahasa == "EN")
              echo "<font color=\"red\">DOKUMEN TIDAK DAPAT DIBUKA / TIDAK LENGKAP (DOCUMENTS CAN NOT OPEN / INCOMPLETE)</font>";
            else
              echo "<font color=\"red\">DOKUMEN TIDAK DAPAT DIBUKA / TIDAK LENGKAP</font>";
          } else { ?>
              Nilai Penawaran <?php if($paketInfo->bahasa == "EN") echo "/ <em>Bid Price</em>"; ?>: <?=$paketInfo->mata_uang?> <?=numberToIna($paket_rekanan_2sampul->getField("UNIT_PRICE"))?> (<?=terbilang($paket_rekanan_2sampul->getField("UNIT_PRICE"))?>)
          <?php } ?>
       </td>
      </tr>
      <tr>
        <td style="border: 1px solid #b7b7b7; width: 50px; text-align: center;">No. <?php if($paketInfo->bahasa == "EN") echo "/ <em>Number</em>"; ?></td>
        <td style="border: 1px solid #b7b7b7; width: 500px;">Nama Dokumen <?php if($paketInfo->bahasa == "EN") echo "/ <em>Documents Name</em>"; ?></td>
        <td style="border: 1px solid #b7b7b7; width: 100px; text-align: center;">Upload<?php if($paketInfo->bahasa == "EN") echo "/ <em>Upload</em>"; ?></td>
      </tr>
      <tr>
        <!-- <td>I</td> -->
        <td style="border: 1px solid #b7b7b7;" colspan="3" class="padding5">Dokumen Harga <?php if($paketInfo->bahasa == "EN") echo "/ <em>Financial Documents</em>"; ?></td>
      </tr>
      <?php
      $i=1;
      $jumlahDokumenHarga = 0;
      $jumlahUploadHarga = 0;
      while($paket_evaluasi_harga_tawar->nextRow())
      {
      ?>
      <tr>
        <td style="border: 1px solid #b7b7b7;"><?=$i?>.</td>
        <td style="border: 1px solid #b7b7b7;"> <?=$paket_evaluasi_harga_tawar->getField("NAMA")?> </td>
        <td style="border: 1px solid #b7b7b7; text-align: center;">
          <?php
          if($paket_evaluasi_harga_tawar->getField("PATH_FILE") == "") {
            echo "Tidak";
          } else {
            echo "Ya";
          }?>
        </td>
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
    <br>
    <table>
      <tr>
        <td colspan="3" class="padding5"><?=$no_urut?>. <?=$paket_rekanan_1sampul->getField("FULL_NAMA_REKANAN")?></td>
      </tr>
      <tr>
        <td colspan="3" style="border: 1px solid #b7b7b7;">
          <?php $nilaiPenawaran = $paket_rekanan_1sampul->getField("UNIT_PRICE");
          if($nilaiPenawaran == "") {
            if($paketInfo->bahasa == "EN")
              echo "<font color=\"red\">DOKUMEN TIDAK DAPAT DIBUKA / TIDAK LENGKAP (DOCUMENTS CAN NOT OPEN / INCOMPLETE)</font>";
            else
              echo "<font color=\"red\">DOKUMEN TIDAK DAPAT DIBUKA / TIDAK LENGKAP</font>";
          } else {
          ?>
          Nilai Penawaran <?php if($paketInfo->bahasa == "EN") echo "/ <em>Bid Price</em>"; ?>: <?=$paketInfo->mata_uang?> <?=numberToIna($nilaiPenawaran)?> (<?=terbilang($nilaiPenawaran)?>)<br>
          <!-- Nilai Penawaran Koreksi<?php // if($paketInfo->bahasa == "EN") echo "/ <em>Bid Price</em>"; ?>: <?php // echo $paketInfo->mata_uang?> <?php // echo numberToIna($paket_rekanan_1sampul->getField("JUMLAH_KOREKSI"))?> (<?php // echo terbilang($paket_rekanan_1sampul->getField("JUMLAH_KOREKSI"))?>) -->
          <?php } ?>
       </td>
      </tr>
      <tr>
        <td style="border: 1px solid #b7b7b7; width: 50px; text-align: center;">No. <?php if($paketInfo->bahasa == "EN") echo "/ <em>Number</em>"; ?></td>
        <td style="border: 1px solid #b7b7b7; width: 500px">Nama Dokumen <?php if($paketInfo->bahasa == "EN") echo "/ <em>Documents Name</em>"; ?></td>
        <td style="border: 1px solid #b7b7b7; width: 100px; text-align: center;">Upload <?php if($paketInfo->bahasa == "EN") echo "/ <em>Document</em>"; ?></td>
      </tr>
    <?php
    if ($paket->getField('paket_metode_lelang_id') != 7)
    { ?>
      <tr>
        <td colspan="3" style="border: 1px solid #b7b7b7;">Dokumen Administrasi <?php if($paketInfo->bahasa == "EN") echo "/ <em>Administrative Documents</em>"; ?></td>
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
        <td style="border: 1px solid #b7b7b7; text-align: center;"><?=$i?>.</td>
        <td style="border: 1px solid #b7b7b7;"> <?=$paket_evaluasi_admin_tawar->getField("NAMA")?> </td>
        <td style="border: 1px solid #b7b7b7; text-align: center;">
          <?php
          if($paket_evaluasi_admin_tawar->getField("PATH_FILE") == "") {
            echo "Tidak";
          } else {
            echo "Ya";
          }?>
        </td>
      </tr>
      <?php
        $i++;
        $id++;
        $jumlahDokumenAdmin++;
      }
      ?>
      <tr>
        <!-- <td class="padding5">II</td> -->
        <td colspan="3" style="border: 1px solid #b7b7b7;">Dokumen Teknis <?php if($paketInfo->bahasa == "EN") echo "/ <em>Technical Documents</em>"; ?></td>
      </tr>
      <?php
      $i=1;
      $jumlahDokumenTeknis = 0;
      $jumlahUploadTeknis = 0;
      while($paket_evaluasi_teknis_tawar->nextRow())
      {
      ?>
      <tr>
        <td style="border: 1px solid #b7b7b7; text-align: center;"><?=$i?>.</td>
        <td style="border: 1px solid #b7b7b7;"> <?=$paket_evaluasi_teknis_tawar->getField("NAMA")?> </td>
        <td style="border: 1px solid #b7b7b7; text-align: center;">
          <?php
          if($paket_evaluasi_teknis_tawar->getField("PATH_FILE") == "") {
            echo "Tidak";
          } else {
            echo "Ya";
          }?>
        </td>
      </tr>
      <?php
        $i++;
        $id++;
        $jumlahDokumenTeknis++;
      }
      ?>
    <?php
    } ?>
      <tr>
        <!-- <td class="padding5">III</td> -->
        <td colspan="3" style="border: 1px solid #b7b7b7;">Dokumen Harga <?php if($paketInfo->bahasa == "EN") echo "/ <em>Financial Documents</em>"; ?></td>
      </tr>
      <?php
      $i=1;
      $jumlahDokumenHarga = 0;
      $jumlahUploadHarga = 0;
      while($paket_evaluasi_harga_tawar->nextRow())
      {
      ?>
      <tr>
        <td style="border: 1px solid #b7b7b7; text-align: center;"><?=$i?>.</td>
        <td style="border: 1px solid #b7b7b7;"> <?=$paket_evaluasi_harga_tawar->getField("NAMA")?> </td>
        <td style="border: 1px solid #b7b7b7; text-align: center;">
          <?php
          if($paket_evaluasi_harga_tawar->getField("PATH_FILE") == "") {
            echo "Tidak";
          } else {
            echo "Ya";
          }?>
        </td>
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

  <br>
  <b>Hasil Evaluasi :</b>
  <?php
  $paket_rekanan_evaluasi = new PaketRekanan();
  $paket_rekanan_evaluasi->selectByParams2(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 AND A.KIRIM_PENAWARAN = 1  ",'', '  ORDER BY A.LULUS_PENAWARAN_URUT ASC');
  while($paket_rekanan_evaluasi->nextRow())
  {
    $arrRekananIdEval[] = $paket_rekanan_evaluasi->getField("REKANAN_ID");
    $arrRekananEval[] = $paket_rekanan_evaluasi->getField("REKANAN");
    $arrPaketRekananIdEval[] = $paket_rekanan_evaluasi->getField("PAKET_REKANAN_ID");
    $arrPaketRekananNilaiEval[] = $paket_rekanan_evaluasi->getField("NILAI_PENAWARAN");
    $arrPaketRekananLulusEval[] = $paket_rekanan_evaluasi->getField("LULUS_PENAWARAN");
  }
  if (is_array($arrRekananEval)) {
    $arrRekananIdEval = $arrRekananIdEval;
    $arrRekananEval = $arrRekananEval;
    $arrPaketRekananIdEval = $arrPaketRekananIdEval;
    $arrPaketRekananNilaiEval = $arrPaketRekananNilaiEval;
    $arrPaketRekananLulusEval = $arrPaketRekananLulusEval;
  } else {
    $arrRekananIdEval = array();
    $arrRekananEval = array();
    $arrPaketRekananIdEval = array();
    $arrPaketRekananNilaiEval = array();
    $arrPaketRekananLulusEval = array();
  }
  ?>
  <table>
    <tr>
      <th style="border: 1px solid #b7b7b7; width:50px" rowspan="2">No</th>
      <th style="border: 1px solid #b7b7b7;" rowspan="2">Nama Peserta</th>
      <?php
      if ($reqMetodeLelangId != '7') { // Selain Tender Cepat ?>
      <th style="border: 1px solid #b7b7b7; text-align: center" colspan="3" width="21%" >Evaluasi</th>
      <?php
      } else { ?>
      <th style="border: 1px solid #b7b7b7; text-align: center" colspan="1" width="21%">Evaluasi</th>
      <?php
      }  ?>
      <th style="border: 1px solid #b7b7b7; text-align: center" rowspan="2" width="15%">Hasil Evaluasi</th>
    </tr>
    <tr>
      <?php
      if ($reqMetodeLelangId != '7') { // Selain Tender Cepat ?>
      <th style="border: 1px solid #b7b7b7; text-align: center">Adm.</th>
      <th style="border: 1px solid #b7b7b7; text-align: center">Teknis</th>
      <?php } ?>
      <th style="border: 1px solid #b7b7b7; text-align: center">Harga</th>
    </tr>
    <?php
    $no=1;
    $hasil = 0;
    for($i=0;$i<count($arrRekananEval);$i++)
    {
      $rekanan_paket_penawaran = new RekananPaketPenawaran();
      $rekanan_paket_penawaran->selectByParams(array("PAKET_REKANAN_ID" => $arrPaketRekananIdEval[$i]));
      $rekanan_paket_penawaran->firstRow();

      $rekanan_evaluasi_admin = new RekananEvaluasiAdminTawar();
      $rekanan_evaluasi_admin->selectMemenuhiSyarat($reqId, $arrPaketRekananIdEval[$i]);
      $rekanan_evaluasi_admin->firstRow();

      //  ikn 20220310
      if($rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI") >= 1)
      {
        $status_admin = '<img class="text-center" src="images/centang-cetak.png">';
        $keterangan_admin = $rekanan_evaluasi_admin->getField("KETERANGAN_LULUS");
        $arrEvaluasiAdmin[$i] = 1;
      }
      else
      {
        $status_admin = '<img class="text-center" src="images/uncentang-cetak.png">';
        $keterangan_admin = $rekanan_evaluasi_admin->getField("KETERANGAN_GAGAL");
        $arrEvaluasiAdmin[$i] = 0;
      }

      $rekanan_evaluasi_teknis = new RekananEvaluasiTeknisTawar();
      $rekanan_evaluasi_teknis->selectMemenuhiSyarat($reqId, $arrPaketRekananIdEval[$i]);
      $rekanan_evaluasi_teknis->firstRow();

      //  ikn 20220310
      if($rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI") >= 1 && $rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI") >= 1)
      {
        $status_teknis = '<img class="text-center" src="images/centang-cetak.png">';
        if ($reqMetodeEvaluasiId == '2') {
          $keterangan_teknis = 'Nilai Teknis <b>'.$rekanan_evaluasi_teknis->getField("NILAI_TEKNIS").'</b>';
        } else {
          $keterangan_teknis = $rekanan_evaluasi_teknis->getField("KETERANGAN_LULUS");
        }
        $arrEvaluasiTeknis[$i] = 1;
      }
      else
      {
        $status_teknis = '<img class="text-center" src="images/uncentang-cetak.png">';
        $keterangan_teknis = $rekanan_evaluasi_teknis->getField("KETERANGAN_GAGAL");
        $arrEvaluasiTeknis[$i] = 0;
      }

      $rekanan_evaluasi_harga = new RekananEvaluasiHargaTawar();
      $rekanan_evaluasi_harga->selectMemenuhiSyarat2($reqId, $arrPaketRekananIdEval[$i]);
      $rekanan_evaluasi_harga->firstRow();
      //  ikn 20220310
      if ($reqMetodeLelangId != '7') { // Selain Tender Cepat
        if($rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI") >= 1 && $rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI") >= 1 && $rekanan_evaluasi_harga->getField("JUMLAH_DILENGKAPI") >= 1)
        {
          $status_harga = '<img class="text-center" src="images/centang-cetak.png">';
          if ($reqMetodeEvaluasiId == '2') {
            $keterangan_harga = 'Nilai Harga <b>'.$rekanan_evaluasi_harga->getField("NILAI_HARGA").'</b>';
          } else {
            $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_LULUS");
          }

          $nilaiHarga = '<br> <small> Nilai '.$rekanan_evaluasi_harga->getField("NILAI_HARGA").'</small>';
          $penawaran = $paketInfo->mata_uang.' '.numberToIna($rekanan_paket_penawaran->getField('unit_price'));
          $penawaran_terkoreksi = $paketInfo->mata_uang.' '.numberToIna($rekanan_paket_penawaran->getField('jumlah_koreksi'));

          $arrEvaluasiHarga[$i] = 1;
        }
        else
        {
          $status_harga = '<img class="text-center" src="images/uncentang-cetak.png">';
          $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_GAGAL");
          $nilaiHarga = '';
          $penawaran = '-';
          $penawaran_terkoreksi = '-';
          $arrEvaluasiHarga[$i] = 0;
        }
      } else {
        if($rekanan_evaluasi_harga->getField("JUMLAH_DILENGKAPI") >= 1)
        {
          $status_harga = '<img class="text-center" src="images/centang-cetak.png">';
          if ($reqMetodeEvaluasiId == '2') {
            $keterangan_harga = 'Nilai Harga <b>'.$rekanan_evaluasi_harga->getField("NILAI_HARGA").'</b>';
          } else {
            $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_LULUS");
          }

          $nilaiHarga = '<br> <small> Nilai '.$rekanan_evaluasi_harga->getField("NILAI_HARGA").'</small>';
          $penawaran = $paketInfo->mata_uang.' '.numberToIna($rekanan_paket_penawaran->getField('unit_price'));
          $penawaran_terkoreksi = $paketInfo->mata_uang.' '.numberToIna($rekanan_paket_penawaran->getField('jumlah_koreksi'));

          $arrEvaluasiHarga[$i] = 1;
        }
        else
        {
          $status_harga = '<img class="text-center" src="images/uncentang-cetak.png">';
          $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_GAGAL");
          $nilaiHarga = '';
          $penawaran = '-';
          $penawaran_terkoreksi = '-';
          $arrEvaluasiHarga[$i] = 0;
        }
      }


      if ($reqMetodeLelangId != '7') { // Selain Tender Cepat
        if((int)$reqOwnerEstimate == 0)
          $nilai = 0;
        else
          $nilai = round(((int)$arrPaketRekananNilaiEval[$i] / (int)$reqOwnerEstimate) * 100,2);
        if($arrEvaluasiAdmin[$i] == 0 || $arrEvaluasiTeknis[$i] == 0 || $arrEvaluasiHarga[$i] == 0)
        {
          $evaluasi = 0;
          $hasil2 = "Tidak Memenuhi Syarat";
        }
        else
        {
          $evaluasi = 1;
          $hasil2 = "Memenuhi Syarat";
        }
      } else {
        if((int)$reqOwnerEstimate == 0)
          $nilai = 0;
        else
          $nilai = round(((int)$arrPaketRekananNilaiEval[$i] / (int)$reqOwnerEstimate) * 100,2);
        if($arrEvaluasiHarga[$i] == 0)
        {
          $evaluasi = 0;
          $hasil2 = "Tidak Memenuhi Syarat";
        }
        else
        {
          $evaluasi = 1;
          $hasil2 = "Memenuhi Syarat";
        }
      }

      // bold akun login
      if ($arrRekananIdEval[$i] == $this->ID) {
        $bold = 'font-weight:bold; vertical-align: top;';
      } else {
        $bold = 'vertical-align: top;';
      }
    ?>
      <tr>
        <td style="border: 1px solid #b7b7b7; vertical-align: top; text-align: center;"><?= $no ?>.</td>
        <td style="border: 1px solid #b7b7b7; <?= $bold ?>"><?=$arrRekananEval[$i]?></td>
        <?php
        if ($reqMetodeLelangId != '7') { // Selain Tender Cepat ?>
        <td style="border: 1px solid #b7b7b7; vertical-align:top; text-align: center;">
          <?=$status_admin.'<br><small>'.$keterangan_admin.'</small>'?>
        </td>
        <td style="border: 1px solid #b7b7b7; vertical-align:top; text-align: center;">
          <?=$status_teknis.'<br><small>'.$keterangan_teknis.'</small>'?>
        </td>
        <?php
        } ?>
        <td style="border: 1px solid #b7b7b7; vertical-align:top; text-align: center;">
          <?=$status_harga.'<br><small>'.$keterangan_harga.'</small>';?>
        </td>
        <td style="border: 1px solid #b7b7b7; text-align: center; <?= $bold ?>"> <?=$hasil2?></td>
      </tr>
    <?php
    $no++;
    }
    unset($rekanan_evaluasi_admin);
    unset($rekanan_evaluasi_teknis);
    unset($rekanan_evaluasi_harga);
    ?>
  </table>

  <br>
  <?php
  if($paketInfo->bidding == "1")
    echo '<b>e-Reverse Auction :</b>';
  else
    echo '<b>Negosiasi :</b>';
  ?>
    <?php
    if($paketInfo->bidding == "1"){
    ?>
    <table>
      <tr>
        <td style="border: 1px solid #b7b7b7; width:500px">Nama Peserta</td>
        <td style="border: 1px solid #b7b7b7; width:150px">Harga e-Reverse Auction</td>
      </tr>
      <?php
      $paket_rekanan = new PaketRekanan();
      $paket_rekanan->selectUrutPenawaran2(array("A.PAKET_ID" => $reqId, "A.LULUS_PENDAFTARAN" => 1, "A.KIRIM_PENAWARAN" => 1, "A.LULUS_PENAWARAN" => 1), -1, -1, "", "", " ORDER BY NILAI_PENAWARAN ASC, KIRIM_PENAWARAN_TANGGAL ASC ");
       $urut=1;
      while($paket_rekanan->nextRow())
      {
      ?>
        <tr>
          <td style="border: 1px solid #b7b7b7;"><?=$paket_rekanan->getField("NAMA")?></td>
          <td style="border: 1px solid #b7b7b7;"><?=numberToIna($paket_rekanan->getField("NILAI_PENAWARAN"))?></td>
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

      $rekanan->selectByParams(array("A.REKANAN_ID" => $paketInfo->rekanan_id_pemenang), -1, -1, '');
      $rekanan->firstRow();

      ?>
      <table>
      <tr>
        <td style="border: 1px solid #b7b7b7; width:500px;">Nama Penyedia</td>
        <td style="border: 1px solid #b7b7b7; width:150px">Nilai Negosiasi</td>
      </tr>
        <tr>
          <td style="border: 1px solid #b7b7b7;"><?=$rekanan->getField("NAMA")?></td>
          <td style="border: 1px solid #b7b7b7;"><?=numberToIna($penawaranNegosiasi)?></td>
        </tr>
    </table>
    <?php
    }?>


  <br>
  <b>Urutan/Ranking Pemenang <?=$paket->getField('metode_lelang')?>:</b>
  <div style="border: 1px solid #b7b7b7;">
    <!-- <div style="margin:5px 0 20px 0; border:1px solid #000; padding: 5px">
      <b>Nama Pemenang : <?php //$rekanan->getField("NAMA") ?></b> <br>
    </div>   -->
    <?php
    while($getpaket_pemenang->nextRow())
    { ?>
    <table>
      <?php
      if ($reqMetodeLelangId != '2' && $reqMetodeLelangId != '5')
      { ?>
      <tr>
        <td colspan="2" style="font-weight: bold"> <?php if ($reqMultiPemenang == '0') { echo "Urutan"; } else { echo "Pemenang"; } ?> <?= $getpaket_pemenang->getField("PERINGKAT") ?></td>
      </tr>
      <?php
      } ?>
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
  <table style="margin-top:10%">
      <tr>
        <td style="border: 1px solid #b7b7b7; width: 400px;">Nama</td>
        <td style="border: 1px solid #b7b7b7; width: 250px; text-align: center">TTD</td>
      </tr>
      <?php
      $no=1;
        while($paket_panitia2->nextRow())
        {
       ?>
      <tr>
          <td style="border: 1px solid #b7b7b7;" align="left" height="100px">
            <br><br>
            <?=$paket_panitia2->getField("NAMA");?><br><span style="font-size:10px">NPP: <?=$paket_panitia2->getField("NIP");?></span>
            <br><br>
          </td>
          <td style="border: 1px solid #b7b7b7;" ></td>
      </tr>
      <?php $no++; } ?>
    </table>

  <br><br>
    <p style="margin-top:20px; text-align:center;">
      <div style="border:1px solid #000; background:#ddd; font-size:10px; font-style:italic; padding:8px 10px !important; text-align: center;">
        <?= SYSTEM_SAH ?>
        <?php if($paketInfo->bahasa == "EN") echo "<br><br><em>".SYSTEM_SAH_EN."</em>"; ?>
      </div>
    </p>

</body>
</html>
