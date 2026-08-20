<?php
/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->library("libgeneratecode"); $libgeneratecode = new libgeneratecode();


$this->load->model(array("Paket","PaketRekanan","Rekanan","PermohonanPaket","PaketPenilaian","PaketPanitia","Paketpemenang","PaketAanwijzing","PaketTahap","PaketNegosiasiValidasi","Metode","PaketDokumen","PaketEvaluasiAdminTawar","PaketEvaluasiTeknisTawar","PaketEvaluasiHargaTawar","MatrixEvaluasi","RekananPaketPenawaran","PaketNegoisasi","RekananEvaluasiAdminTawar","RekananEvaluasiTeknisTawar","RekananEvaluasiHargaTawar","Queryfree"));

include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/string.func.php");
include_once("lib/phpqrcode/qrlib.php");

// Unit Kerja
$this->load->library("libbreadcrumb");
$unitkerjaid = $this->input->get("unitkerjaid");
$userloginid = $this->input->get("userloginid");
$nip = $this->input->get("nip");

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
$reqNomorBAHP = $paketInfo->nomor_bahp;
$reqNomorPaket = $paketInfo->nomor_paket;

// Pemenang
$getpaket_pemenang->selectByParams(array("A.PAKET_ID" => $reqId), -1, -1);


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
if ($reqNomorBAHP == '') {
  $reqNomorBAHP = $libgeneratecode->bahp($reqId,$reqMetodeLelangId,$nip);
  $reqNomorPaket = $libgeneratecode->nomorPaket($reqId,$reqMetodeLelangId);

  $paketUpdateNoBAHP    = new Paket();
  $paketUpdateNoBAHP->setField("NOMOR_BAHP", $reqNomorBAHP);
  $paketUpdateNoBAHP->setField("NOMOR_PAKET", $reqNomorPaket);
  $paketUpdateNoBAHP->setField("USER_LOGIN_ID", $userloginid);
  $paketUpdateNoBAHP->setField("PAKET_ID", $reqId);
  $paketUpdateNoBAHP->updateNoBAHP();
}

?>

<html moznomarginboxes mozdisallowselectionprint>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<base href="<?=base_url()?>" />
</head>

<body>

<div class="logo"><img src="images/<?= $this->libbreadcrumb->cetakcopyrightlogo($unitkerjaid) ?>" height="75" /></div>
<div class="judul">
  <?= ucwords('summary report '.$paket->getField('metode_lelang')) ?>
</div>
<div class="nomor" style="font-size: 10px">Nomor :  <?=$reqNomorBAHP?></div><br>

<p>
  <?php
  $dateSanggah = new Queryfree();
  $dateSanggah->selectByParams("SELECT * FROM PAKET_TAHAP WHERE PAKET_ID = ".$reqId." order by urut desc limit 1 ");
  $dateSanggah->firstRow();
  $tanggalAkhir = explode(" ","",$dateSanggah->getField('TANGGAL_AKHIR'));
  $time = strtotime($tanggalAkhir[0]);
  $harinya = date('w', $time);

  $date = new DateTime($tanggalAkhir[0]);
  $tglFormat = $date->format('d-m-Y');
 ?>
Pada hari ini <?= ucwords(getHari($harinya));?>, <?= getFormattedDateView($tglFormat) ?>, telah dibuat Berita Acara Hasil <?= ucwords($paket->getField('metode_lelang')) ?> untuk paket pekerjaan berikut:
</p><br>

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

      <!-- <tr>
        <td align="left" valign="top" width="30%"> Bidang Usaha </td>
        <td align="center" valign="top" width="2%">:</td>
        <td align="left" valign="top" width="68%">
          <?php
          // $exbd = explode(", ", $paket->getField('bidang_usaha'));
          // foreach ($exbd as $value) {
          //   echo $value.'<br>';
          // }
          ?>
        </td>
      </tr> -->
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
        <b>Dokumen Administrasi :</b>
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
        <b>Dokumen Teknis :</b>
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
        <b>Dokumen Harga :</b>
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

    <b>Jadwal :</b>
    <table class="table font13"> 
      <tbody>
          <tr class="tr">
            <th rowspan="3" valign="middle" class="tdno">No</th>
            <th rowspan="3" valign="middle" class="td">Tahapan</th>
            <th colspan="4" valign="top" style="text-align: center" class="td"> Waktu Pelaksanaan </th>
          </tr>
          <tr class="tr">
            <th colspan="2" style=" text-align: center" class="td"> Mulai </th>
            <th colspan="2" style=" text-align: center" class="td">Selesai</th>
            </tr>
          <tr class="tr"> 
            <th style=" text-align: center" class="td"> Tanggal </th>
            <th style=" text-align: center" class="td"> Jam </th>
            <th style=" text-align: center" class="td"> Tanggal </th>
            <th style=" text-align: center" class="td"> Jam </th>
          </tr>

          <?php 
          $i=1; $no=1; $stat = ''; $stat_m = '';
          while($metode->nextRow())
          {
              if($stat == '') $comma = '';    else    $comma = ', ';                              
              $stat .= $comma."#reqJamSelesai$i, #reqJamMulai$i";
              
              if($stat_m == '') $comma = '';  else    $comma = ', ';                              
              $stat_m .= $comma."#reqMenitSelesai$i, #reqMenitMulai$i";
              
              $disabledTanggalAwal = $metode->getField("TANGGAL_AWAL_DISABLED");
              $triggerTanggalAkhir = $metode->getField("TANGGAL_AKHIR_TRIGGER");
          ?>
              <tr valign="top">
                <td class="tdno" style="width: 6% !important"><?=$no?>.</td>
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
                      <span class="font11">(<?=$notif?>)</span>
                      <?php 
                      }
                      ?>
                  </td>  
                  <td class="td">                                    
                      <?=datetimeToPage($metode->getField("TANGGAL_AWAL"), "date")?> 
                  </td>
                  <td class="td ">
                      <?php 
                      $arrJamAwal = explode(":", $metode->getField("JAM_AWAL"));
                      ?>
                      <?=$arrJamAwal[0]?> 
                      : 
                      <?=$arrJamAwal[1]?> 
                  </td>
                  <td class="td">
                      <?=datetimeToPage($metode->getField("TANGGAL_AKHIR"), "date")?> 
                  </td>
                      <?php 
                      $arrJamAkhir = explode(":", $metode->getField("JAM_AKHIR"));
                      ?>                                      
                  <td class="td">
                      <?=$arrJamAkhir[0]?> 
                      
                      <?= $arrJamAkhir[1] ? ' : '.$arrJamAkhir[1] : '' ?> 
                      <?php 
                      if($paketInfo->bidding == "1")
                          $namaJadwal = str_replace("Negosiasi", "e-Auction", $metode->getField("NAMA"));
                      else
                          $namaJadwal = $metode->getField("NAMA");
                      ?>
                  </td>
              </tr> 
          <?php 
              $i++;
              $no++;
          }
          ?>
      </tbody>
    </table>   

    <b>Daftar Peserta :</b>

    <table class="table">

      <tr class="tr-bc">
        <td class="td" align="center">No</td>
        <td class="td">Nama Peserta <?= $paketInfo->metode_lelang_nama ?></td>
          <?php
        if ($paketInfo->metode_lelang_id == '2' || $paketInfo->metode_lelang_id == '3' || $paketInfo->metode_lelang_id == '5' || $paketInfo->metode_lelang_id == '6' || $paketInfo->metode_lelang_id == '8' ) { ?>
        <td class="td" align="center">Diundang</td>
        <?php
        } ?>
         <?php
        if ($paketInfo->metode_lelang_id == '1'|| $paketInfo->metode_lelang_id == '7' || $paketInfo->metode_lelang_id == '10' ) { ?>
        <td class="td" align="center">Tanggal Daftar</td>
        <?php
        } ?>
      </tr>
      <?php
      $no=1;
        while($paket_rekanan->nextRow())
        {
       ?>
      <tr>
        <td class="tdno" align="center"><?=$no?></td>
          <td class="td"><?=$paket_rekanan->getField('FULL_NAMA_REKANAN');?></td>
          <?php
          if ($paketInfo->metode_lelang_id != '1' && $paketInfo->metode_lelang_id != '7' && $paketInfo->metode_lelang_id != '10' ) { ?>
          <td class="td" align="center"> <?=getFormattedDate($paket_rekanan->getField("TANGGAL_UNDANG"))?> </td>
          <?php
          } else { ?>
          <td class="td" align="center"> <?=getFormattedDate($paket_rekanan->getField("TANGGAL_DAFTAR")).' <sup> '.$paket_rekanan->getField("JAM_DAFTAR").'</sup>'?>
          </td>
          <?php
          } ?>
      </tr>
      <?php $no++; } ?>
    </table>

  <!-- CR 06-01-2025 -->
  <?php // while($paket_rekanan->nextRow()) {
  if ($paket->getField('paket_metode_lelang_id') != 7) // Selain Tender Cepat
  { ?>
    <b>Daftar Peserta Aanwijzing :</b>
    <table class="table">
      <thead>
        <tr class="tr-bc">
          <th class="td" width="50px">No</th>
          <th class="td">NAMA</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $i = 0;
          foreach ($arrRekanan as $key => $value) {
          ?>
          <tr>
              <td class="td"><?= ($i+1)?>.</td>
              <td class="td">
                <?= $value; ?> <?php //$arrRekanan[$i]["NAMA"]?></td>
          </tr>
          <?php
          $i++;
          }
        ?>
      </tbody>
    </table>
    <span style="font-size: 15px;"><b>Aanwijzing:</b></span>
    <table class="table">
      <thead>
        <tr class="tr-bc">
          <th class="td"># ID</th>
          <th class="td">Tanya/Jawab</th>
          <th class="td">Tanggal</th>
        </tr>
      </thead>
      <tbody class="table">
        <?php
          if ($totalAan=='') {
              echo '<tr><td class="td" colspan="6">. : Tidak ada data : .</td></tr>';
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
              <td class="td">
                <?php
                if ($paket_aanwijzing->getField("REKANAN_USER_ID") == $this->REKANAN_ID) {
                  echo '<i class="fa fa-user" style="color:#000; opacity:1"></i> <span style="color:#000; opacity:1">'. $paket_aanwijzing->getField("KODE_CUT") .'</span>' ;
                } else {
                  echo '<i class="fa fa-user" style="color:#000; opacity:1"></i> <span style="color:#000; opacity:1">'. $paket_aanwijzing->getField("KODE_CUT") .'</span>' ;
                }
                echo '<br><small>'.$paket_aanwijzing->getField("NAMA_PENYEDIA").'</small>';
                ?>
              </td>
              <td class="td">
                  <?=$paket_aanwijzing->getField("KETERANGAN")?>
              </td>
              <td class="td">
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
              <td class="td"><i class="fa fa-arrow-right" aria-hidden="true"></i>
                <?php
              if ($reqMetodeLelangId == '2') {
               } else {
                echo "PANITIA PENGADAAN BARANG DAN JASA :";
               } ?>
                <small class="font10">Jawab<i><b> <?=$paket_aanwijzing->getField("KODE_CUT")?> </b></i></small>
              </td>
              <td class="td">
                  <?=$paket_aanwijzing_parent->getField("KETERANGAN")?>
              </td>
              <td class="td">
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


  <b>Pembukaan Penawaran :</b>
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
          <td colspan="3" class="padding5"><?=$no_urut?>. <?=$paket_rekanan_1sampul->getField("FULL_NAMA_REKANAN")?></td>
      </tr>
      <tr class="tr-bc">
        <td class="td" align="center">No. <?php if($paketInfo->bahasa == "EN") echo "/ <em>Number</em>"; ?></td>
        <td class="td" align="center" style="width:40%">Nama Dokumen <?php if($paketInfo->bahasa == "EN") echo "/ <em>Documents Name</em>"; ?></td>
        <td class="td" width="10px" align="center">Upload <?php if($paketInfo->bahasa == "EN") echo "/ <em>Upload</em>"; ?></td>
      </tr>
      <tr class="tr">
        <!-- <td>I</td> -->
        <td colspan="3" class="td padding5">Dokumen Administrasi <?php if($paketInfo->bahasa == "EN") echo "/ <em>Administrative Documents</em>"; ?></td>
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
        <td class="td font10" style="text-align:center">
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
      <tr class="tr">
        <!-- <td class="padding5">II</td> -->
        <td class="padding5" colspan="3">Dokumen Teknis <?php if($paketInfo->bahasa == "EN") echo "/ <em>Technical Documents</em>"; ?></td>
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
        <td class="td font10" style="text-align:center">
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
        <td colspan="3" class="padding5 td"><?=$no_urut2?>. <?=$paket_rekanan_2sampul->getField("FULL_NAMA_REKANAN")?></td>
      </tr>
      <tr class="tr">
        <td colspan="3" class="td">
          <?php if($paket_rekanan_2sampul->getField("NILAI_PENAWARAN") == "") {
            if($paketInfo->bahasa == "EN")
              echo "<font color=\"red\">DOKUMEN TIDAK DAPAT DIBUKA / TIDAK LENGKAP (DOCUMENTS CAN NOT OPEN / INCOMPLETE)</font>";
            else
              echo "<font color=\"red\">DOKUMEN TIDAK DAPAT DIBUKA / TIDAK LENGKAP</font>";
          } else { ?>
              Nilai Penawaran <?php if($paketInfo->bahasa == "EN") echo "/ <em>Bid Price</em>"; ?>: <?=$paketInfo->mata_uang?> <?=numberToIna($paket_rekanan_2sampul->getField("UNIT_PRICE"))?> (<?=terbilang($paket_rekanan_2sampul->getField("UNIT_PRICE"))?>) <br>
              <!-- Nilai Penawaran Koreksi<?php // if($paketInfo->bahasa == "EN") echo "/ <em>Bid Price</em>"; ?>: <?php // echo $paketInfo->mata_uang?> <?php // echo numberToIna($paket_rekanan_2sampul->getField("JUMLAH_KOREKSI"))?> ( <?php // echo terbilang($paket_rekanan_2sampul->getField("JUMLAH_KOREKSI"))?>) -->
          <?php } ?>
       </td>
      </tr>
      <tr class="tr-bc">
        <td class="td" align="center">No. <?php if($paketInfo->bahasa == "EN") echo "/ <em>Number</em>"; ?></td>
        <td class="td" align="center" style="width:40%">Nama Dokumen <?php if($paketInfo->bahasa == "EN") echo "/ <em>Documents Name</em>"; ?></td>
        <td class="td" width="10px" align="center">Upload <?php if($paketInfo->bahasa == "EN") echo "/ <em>Upload</em>"; ?></td>
      </tr>
      <tr class="tr">
        <!-- <td>I</td> -->
        <td colspan="3" class="padding5">Dokumen Harga <?php if($paketInfo->bahasa == "EN") echo "/ <em>Financial Documents</em>"; ?></td>
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
        <td class="td font10" style="text-align:center">
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

    <table class="table">
      <tr class="tr">
        <td colspan="3" class="padding5"><?=$no_urut?>. <?=$paket_rekanan_1sampul->getField("FULL_NAMA_REKANAN")?></td>
      </tr>
      <tr class="tr-bc">
        <td colspan="3" class="td">
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
      <tr class="tr-bc">
        <td class="tdno" align="center">No. <?php if($paketInfo->bahasa == "EN") echo "/ <em>Number</em>"; ?></td>
        <td class="td" align="center" style="width:40%">Nama Dokumen <?php if($paketInfo->bahasa == "EN") echo "/ <em>Documents Name</em>"; ?></td>
        <td class="td" width="10px" align="center">Upload <?php if($paketInfo->bahasa == "EN") echo "/ <em>Document</em>"; ?></td>
      </tr>
    <?php
    if ($paket->getField('paket_metode_lelang_id') != 7)
    { ?>
      <tr class="tr">
        <!-- <td class="padding5">I</td> -->
        <td class="padding5" colspan="3">Dokumen Administrasi <?php if($paketInfo->bahasa == "EN") echo "/ <em>Administrative Documents</em>"; ?></td>
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
        <td class="td font10" style="text-align:center">
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
      <tr class="tr">
        <!-- <td class="padding5">II</td> -->
        <td class="padding5" colspan="3">Dokumen Teknis <?php if($paketInfo->bahasa == "EN") echo "/ <em>Technical Documents</em>"; ?></td>
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
        <td class="td font10" style="text-align:center">
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
      <tr class="tr">
        <!-- <td class="padding5">III</td> -->
        <td class="padding5" colspan="3">Dokumen Harga <?php if($paketInfo->bahasa == "EN") echo "/ <em>Financial Documents</em>"; ?></td>
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
        <td class="td font10" style="text-align:center">
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

  <b>Hasil Evaluasi :</b>
  <br>

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
  <table class="table">
    <tr class="tr-bc">
      <th class="td" rowspan="2" width="5%">No</th>
      <th class="td" rowspan="2">Nama Peserta</th>
      <?php
      if ($reqMetodeLelangId != '7') { // Selain Tender Cepat ?>
      <th class="td" colspan="3" width="21%" style="text-align: center">Evaluasi</th>
      <?php
      } else { ?>
      <th class="td" colspan="1" width="21%" style="text-align: center">Evaluasi</th>
      <?php
      }  ?>
      <th class="td" rowspan="2" width="15%" style="text-align: center">Hasil Evaluasi</th>
    </tr>
    <tr class="tr-bc">
      <?php
      if ($reqMetodeLelangId != '7') { // Selain Tender Cepat ?>
      <th class="td" width="15%" style="text-align: center">Adm.</th>
      <th class="td" width="15%" style="text-align: center">Teknis</th>
      <?php } ?>
      <th class="td" width="15%" style="text-align: center">Harga</th>
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
        if ($reqMetodeEvaluasiId == '2' || $reqMetodeEvaluasiId == '10') {
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
        $bold = 'style="font-weight:bold; vertical-align: top;"';
      } else {
        $bold = 'style="vertical-align: top;"';
      }
    ?>
      <tr>
        <td class="td" style="vertical-align: top;"><?= $no ?>.</td>
        <td class="td" <?= $bold ?>><?=$arrRekananEval[$i]?></td>
        <?php
        if ($reqMetodeLelangId != '7') { // Selain Tender Cepat ?>
        <td class="td" style="vertical-align:top;">
          <strong><?=$status_admin.'<br><small>'.$keterangan_admin.'</small>'?></strong>
        </td>
        <td class="td" style="vertical-align:top;">
          <strong><?=$status_teknis.'<br><small>'.$keterangan_teknis.'</small>'?></strong>
        </td>
        <?php
        } ?>
        <td class="td" style="vertical-align:top;">
          <strong><?=$status_harga.'<br><small>'.$keterangan_harga.'</small>';?></strong>
        </td>
        <td class="td" <?= $bold ?>> <?=$hasil2?></td>
      </tr>
    <?php
    $no++;
    }
    unset($rekanan_evaluasi_admin);
    unset($rekanan_evaluasi_teknis);
    unset($rekanan_evaluasi_harga);
    ?>
  </table>

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
      if ($paketInfo->rekanan_id_pemenang) {
        // code...
      $paket_negosiasi = new PaketNegoisasi();
      $paket_negosiasi->selectByParams(array("A.PAKET_ID" => $reqId));
      $paket_negosiasi->firstRow();
      $penawaranNegosiasi =  $paket_negosiasi->getField("UNIT_PRICE");
      $jumlahNegosiasi =  $paket_negosiasi->getField("TOTAL");
      $setujui =  $paket_negosiasi->getField("SETUJUI");

      // Rekanan
      $rekanan->selectByParams(array("A.REKANAN_ID" => $paketInfo->rekanan_id_pemenang), -1, -1, '');
      $rekanan->firstRow();
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
      }
      ?>
    <?php
    }?>


  <b>Urutan/Ranking Pemenang <?=$paket->getField('metode_lelang')?>:</b>
  <div class="td">
    <!-- <div style="margin:5px 0 20px 0; border:1px solid #000; padding: 5px">
      <b>Nama Pemenang : <?php //$rekanan->getField("NAMA") ?></b> <br>
    </div>   -->
    <?php
    while($getpaket_pemenang->nextRow())
    { ?>
    <table class="table">
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

      <?php
      $paket_rekanan = new PaketRekanan();
      $paket_rekanan->selectUrutPenawaran2(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => $getpaket_pemenang->getField("REKANAN_ID"), "A.LULUS_PENDAFTARAN" => 1, "A.KIRIM_PENAWARAN" => 1, "A.LULUS_PENAWARAN" => 1), -1, -1, "", "", " ORDER BY NILAI_PENAWARAN ASC, KIRIM_PENAWARAN_TANGGAL ASC ");
      $paket_rekanan->firstRow();
      
      if($paketInfo->bidding == "1"){
        // Rumus perhitungan
        $persentase = ($paket_rekanan->getField("NILAI_PENAWARAN") / $paket->getField('nilai')) * 100;

        $rekanan_paket_penawaran = new RekananPaketPenawaran();
        $rekanan_paket_penawaran->selectByParams(array("PAKET_REKANAN_ID" => $paket_rekanan->getField("PAKET_REKANAN_ID")));
        $rekanan_paket_penawaran->firstRow();
      ?>
      <tr>
        <td>Nilai Penawaran</td><td align="left">:&nbsp;&nbsp; <?= numberToIna($rekanan_paket_penawaran->getField('unit_price')) ?></td>
      </tr>
      <tr>
        <td>Nilai Negosiasi</td><td align="left">:&nbsp;&nbsp; <?=numberToIna($paket_rekanan->getField("NILAI_PENAWARAN"))?></td>
      </tr>
      <tr>
        <td>Persentase Terhadap HPS</td><td align="left">:&nbsp;&nbsp; <?= number_format($persentase, 2, ',', '.') ?>% </td>
      </tr>

      <?php 
        } else {
          if ($paketInfo->rekanan_id_pemenang) { 
            $paket_negosiasi = new PaketNegoisasi();
            $paket_negosiasi->selectByParams(array("A.PAKET_ID" => $reqId));
            $paket_negosiasi->firstRow();
            $penawaranNegosiasi =  $paket_negosiasi->getField("UNIT_PRICE");
            $jumlahNegosiasi =  $paket_negosiasi->getField("TOTAL");
            $setujui =  $paket_negosiasi->getField("SETUJUI");

            // Rumus perhitungan
            $persentase = ($penawaranNegosiasi / $paket->getField('nilai')) * 100;

            // Rekanan
            $rekanan->selectByParams(array("A.REKANAN_ID" => $paketInfo->rekanan_id_pemenang), -1, -1, '');
            $rekanan->firstRow();
      ?> 

        <tr>
          <td>Nilai Penawaran</td><td align="left">:&nbsp;&nbsp; <?=numberToIna($paket_rekanan->getField("NILAI_PENAWARAN"))?></td>
        </tr>
        <tr>
          <td>Nilai Negosiasi</td><td align="left">:&nbsp;&nbsp; <?=numberToIna($penawaranNegosiasi)?></td>
        </tr>
        <tr>
          <td>Persentase Terhadap HPS</td><td align="left">:&nbsp;&nbsp; <?= number_format($persentase, 2, ',', '.') ?>% </td>
        </tr>
      <?php
          } 
 
       } ?>
    </table>
    <?php
    } ?>
  </div>
  <br>

  <b>Sanggah:</b>

  <table class="table">
    <tbody>
      <tr class="tr-bc">
        <th class="td">No.</th>
        <th class="td">Penyedia</th>
        <th class="td">Sanggah</th>
        <th class="td">Jawab</th>
      </tr>
      <?php
        $i=1;
        if ($paket_dokumen_sanggahan->countRow() <= 0) {
          echo '<tr class="tr-bc"><td class="td" colspan="6">. : : Tidak ada data : : .</td></tr>';
        } else 
        { 
          while($paket_dokumen_sanggahan->nextRow())
          {
            $parentDate = explode(" ", $paket_dokumen_sanggahan->getField("TGL_JAM_UPLOAD"));
            // Get Parent
            $paket_dokumen_parent = new PaketDokumen();
            $paket_dokumen_parent->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "SANGGAH", "PARENT_ID" => $paket_dokumen_sanggahan->getField("PAKET_DOKUMEN_ID") ));
        ?>
          <tr>
              <td class="td"><?=$i?>.</td>
              <td class="td"><?= $paket_dokumen_sanggahan->getField("NMREKANAN")?></td>
              <td class="td">
                <?=$paket_dokumen_sanggahan->getField("KETERANGAN")?> <br>
                <small style="font-size:9px"><i class="fa fa-clock-o"></i> <?= getFormattedDate($paket_dokumen_sanggahan->getField("TANGGAL_UPLOAD")).' '.$parentDate[1] ?></small>
              </td>
              <td class="td">
                <?php 
                while($paket_dokumen_parent->nextRow())
                {
                  $childDate = explode(" ", $paket_dokumen_parent->getField("TGL_JAM_UPLOAD"));

                ?> 
                  <?=$paket_dokumen_parent->getField("KETERANGAN")?> <br>
                  <small style="font-size:9px"><i class="fa fa-clock-o"></i> <?= getFormattedDate($paket_dokumen_parent->getField("TANGGAL_UPLOAD")).' '.$childDate[1] ?></small>
                <?php 
                } ?>
              </td> 
          </tr>
           <?php
          $i++;
        }
      }
      ?>
    </tbody>
  </table>
  <br>

  <table class="table" style="margin-top:10%">
      <tr class="tr-bc">
        <td class="td" align="left">Nama</td>
        <!-- <td class="td" align="center" width="250px">TTD</td> -->
      </tr>
      <?php
      $no=1;
        while($paket_panitia2->nextRow())
        {
       ?>
      <tr>
          <td class="td" align="left" height="30px">
            <?=$paket_panitia2->getField("NAMA");?><br><span style="font-size:10px">NUP/NIP: <?=$paket_panitia2->getField("NIP");?></span>
          </td>
          <!-- <td class="td" ></td> -->
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
