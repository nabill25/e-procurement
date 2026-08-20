<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->library("AES");
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Paket");
$this->load->model("Contracting");
$this->load->model("Contractingrekanan");
$this->load->model("Paketpemenang");
$this->load->model("Region");
$this->load->model("Rekanan");
$this->load->model("PaketRekanan");
$this->load->model("PaketNegoisasi");
// Unit Kerja
$this->load->library("libbreadcrumb"); 
$unitkerjaid = $this->input->get("unitkerjaid");
// End Unit Kerja

$aes = new AES();
include_once("lib/phpqrcode/qrlib.php");
$PNG_TEMP_DIR = 'uploads/';
$PNG_TEMP_DIR_LOGO = '';
$filenamelogo = $PNG_TEMP_DIR_LOGO.'logo.png';

$reqId = $this->input->get("reqId");
$reqRekananId = $this->input->get("reqRekananId");

$paket = new Paket();
$contracting = new Contractingrekanan();
$region = new Region();
$rekanan = new Rekanan();
$sppbj = new Contractingrekanan();

$contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
$contracting->firstRow();
$reqNamaPaket = $contracting->getField("NAMA");
$reqPanitiaStr = $contracting->getField("PANITIA_STR");
$reqPenggunaStr = $contracting->getField("PENGGUNA_STR");
$reqPpkStr = $contracting->getField("PPK_STR");
$reqPemenangStr = $contracting->getField("PEMENANG_NAMA");

// get data contracting_rekanan_proses1
$sppbj->selectViewSPPBJ(array("A.CONTRACTINGREKANANID" => $reqId, "REKANAN_ID" => $reqRekananId));
$sppbj->firstRow();

$reqPaketId = $sppbj->getField('PAKET_ID') ?: '-';
$reqContractingRekananProses1Id = $sppbj->getField('CONTRACTINGREKANANPROSES1ID') ?: '-';
$reqContractingRekananId = $sppbj->getField('CONTRACTINGREKANANID') ?: '-';
$reqCode = $sppbj->getField('CR_SPPBJ_CODE') ?: '-';
$reqTanggal = dateToPageCheck($sppbj->getField('CR_SPPBJ_TANGGAL')) ?: '-';
$reqDirut = $sppbj->getField('CR_SPPBJ_DIRUT') ?: '-';
$reqDirutAlamat = $sppbj->getField('CR_SPPBJ_DIRUT_ALAMAT') ?: '-';
$reqDirutKota = $sppbj->getField('CR_SPPBJ_DIRUT_KOTA') ?: '-';
$reqDirutKotaStr = $sppbj->getField('CR_SPPBJ_DIRUT_KOTA_STR') ?: '-';
$reqDirutJabatan = $sppbj->getField('CR_SPPBJ_DIRUT_JABATAN') ?: '-';
$reqJaminanPelaksanaan = $sppbj->getField('CR_SPPBJ_JAMINAN_PELAKSANA') ?: '-';
$reqJaminanBesar = $sppbj->getField('CR_SPPBJ_JAMINAN_BESAR') ?: 0;
$reqJaminanJangkaDari = dateToPageCheck($sppbj->getField('CR_SPPBJ_JAMINAN_JANGKA_DARI')) ?: date('d-m-Y');
$reqJaminanJangkaSampai = dateToPageCheck($sppbj->getField('CR_SPPBJ_JAMINAN_JANGKA_SAMPAI')) ?: date('d-m-Y');
$reqJaminanNilai = $sppbj->getField('CR_SPPBJ_JAMINAN_NILAI') ?: 0;
$reqPejabatBerwenang = $sppbj->getField('CR_SPPBJ_PEJABAT_BERWENANG') ?: '-';
$reqNIP = $sppbj->getField('CR_SPPBJ_NIP') ?: '-';
$reqJabatan = $sppbj->getField('CR_SPPBJ_JABATAN') ?: '-';
$reqPPN = $sppbj->getField('CR_SPPBJ_PPN') ?: '-';
$reqPelaksanaanDari = dateToPageCheck($sppbj->getField('CR_SPPBJ_PELAKSANAAN_DARI')) ?: '-';
$reqPelaksanaanSampai = dateToPageCheck($sppbj->getField('CR_SPPBJ_PELAKSANAAN_SAMPAI')) ?: '-';
$reqCreatedBy = $sppbj->getField('CR_SPPBJ_CREATED_BY') ?: '-';
$reqCreatedDate = $sppbj->getField('CR_SPPBJ_CREATED_DATE') ?: '-';
$reqNilai = $sppbj->getField('CR_SPPBJ_NILAI') ?: '-';
$reqContractingStatusKontrakId = $sppbj->getField('CONTRACTINGSTATUSKONTRAKID') ?: '-';
$reqRekananId = $sppbj->getField('REKANAN_ID') ?: '-';

// Get Rekanan
$rekanan->selectByParams(array("A.REKANAN_ID" => $reqRekananId), -1, -1);
$rekanan->firstRow();
$rekanan_nama = $rekanan->getField("NAMA");
$rekanan_npwp = $rekanan->getField("NPWP");
$rekanan_alamat = $rekanan->getField("ALAMAT");
$rekanan_telepon = $rekanan->getField("TELEPON_FULL");
$rekanan_email = $rekanan->getField("EMAIL");
$rekanan_kota = $rekanan->getField("KOTA");
$rekanan_kodepos = $rekanan->getField("KODEPOS");

$paketInfo->getPaket($reqPaketId);
$reqNama = $paketInfo->nama;
?>


<html moznomarginboxes mozdisallowselectionprint>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<base href="<?=base_url()?>" />

<!-- QRCODE -->
<script type="text/javascript" src="lib/jquery-qrcode-0.11.0/demo/jquery-1.10.2.js"></script>
<script type="text/javascript" src="lib/jquery-qrcode-0.11.0/jquery.qrcode-0.11.0.js"></script>
<script type="text/javascript" src="lib/jquery-qrcode-0.11.0/demo/ff-range.js"></script>
<script type="text/javascript" src="lib/jquery-qrcode-0.11.0/demo/scripts.js"></script>
<!--<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700' rel='stylesheet'>-->
<!--<link href="http://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet" type="text/css">-->

	
</head>

<body>
  <br>
  <div class="logo"><img src="images/<?= $this->libbreadcrumb->cetakcopyrightlogo($unitkerjaid) ?>" height="75" /></div>
  <div class="judul"> <br> <br> 

  <div class="area-dokumen" style="margin-bottom:1%">
    <table class="table table-bordered table-hover">
      <tbody>
        <tr>
          <td width="20%"> Nomor </td>
          <td width="20%">: <?= $reqCode ?></td>
        </tr>
        <tr> 
          <td> Sifat</td>
          <td> :</td>
        </tr>
        <tr>
          <td> Lampiran </small></td> 
          <td> : </td> 
        </tr>
        <tr>
          <td>Hal</td>
          <td> :  Surat Penunjukan Penyedia Barang / Jasa <br>&nbsp; <?= $reqNama ?></td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="area-dokumen" style="margin-bottom:3%">
    Kepada <br> 
    Direktur <?= ucwords(str_replace("pt","PT",strtolower($rekanan_nama))) ?>
    <table style="width: 100%">
      <tr> <td><i class="fa fa-map-marker"></i> <?= $rekanan_alamat.', '.$rekanan_kota.' '.$rekanan_kodepos ?></td> </tr>
      <tr> <td><i class="fa fa-envelope"></i> Email: <?= $rekanan_email ?></td> </tr>
    </table>
  </div>
  
  <div class="area-dokumen">
    DATA SPPBJ
    <table class="table table-bordered table-hover">
      <tbody>
        <tr>
          <td width="25%"> Nomor </td><td>: <?= $reqCode ?></td>
        </tr> 
        <tr>
          <td> Tanggal SPPBJ </td><td>: <?= $reqTanggal ?></td>
        </tr> 
        <tr>
          <td> Pejabat Berwenang </td><td>: <?= $reqPejabatBerwenang ?></td>
        </tr> 
        <tr>
          <td> NPP </td><td>: <?= $reqNIP ?></td>
        </tr> 
        <tr>
          <td> Jabatan </td><td>: <?= $reqJabatan ?></td>
        </tr> 
        <tr>
          <td> Nama Direktur <sup style="font-size:9px">Penyedia</sup> </td><td>: <?= $reqDirut ?></td>
        </tr> 
        <tr>
          <td> Kota <sup style="font-size:9px">Penyedia</sup> </td><td>: <?= $reqDirutKotaStr ?></td>
        </tr> 
        <tr>
          <td> Jabatan <sup style="font-size:9px">Penyedia</sup> </td><td>: <?= $reqDirutJabatan ?></td>
        </tr> 
        <tr>
          <td> Alamat <sup style="font-size:9px">Penyedia</sup> </td><td>: <?= $reqDirutAlamat ?></td>
        </tr> 
        <tr>
          <td> Nilai Kontrak </td><td>: <?= numberToIna($reqNilai) ?></td>
        </tr> 
        <tr>
          <td> Masa Pelaksanaan Pekerjaan </td><td>: <?= getFormattedDateView($reqPelaksanaanSampai) ?></td>
        </tr> 
        <tr>
          <td> PPN </td><td>: <?php if($reqPPN == "1") { echo "Ya"; } else { echo "-"; } ?></td>
        </tr> 
        <tr>
          <td> Jaminan Pelaksanaan </td><td>: <?php if($reqJaminanPelaksanaan == "1") { echo "Ya"; } else { echo "-"; } ?></td>
        </tr> 
        <?php 
        if($reqJaminanPelaksanaan == "1") { ?>
        <tr>
          <td> Persen Jaminan </td><td>: <?= $reqJaminanBesar ?> %</td>
        </tr> 
        <tr>
          <td> Nilai Jaminan </td><td>: <?= numberToIna($reqJaminanNilai) ?></td>
        </tr> 
        <tr>
          <td> Jangka Jaminan Pelaksanaan </td><td>: <?= getFormattedDateView($reqJaminanJangkaDari) ?> s/d <?= getFormattedDateView($reqJaminanJangkaSampai) ?></td>
        </tr>  
        <?php 
        } ?>
      </tbody>
    </table>
  </div>


<div class="nomor-oe">
  <div class="data" style="font-size:10px; font-style:italic">
       <?= $this->libbreadcrumb->cetakcopyright($unitkerjaid) ?>
       <?php if($paketInfo->bahasa == "EN") echo "<br><br><em>".SYSTEM_SAH_EN."</em>"; ?>
  </div>
</div>
</body>
</html>
