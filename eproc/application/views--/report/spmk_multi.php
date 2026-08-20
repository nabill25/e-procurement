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
$spmk = new Contractingrekanan();

$contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
$contracting->firstRow();
$reqNamaPaket = $contracting->getField("NAMA");
$reqPanitiaStr = $contracting->getField("PANITIA_STR");
$reqPenggunaStr = $contracting->getField("PENGGUNA_STR");
$reqPpkStr = $contracting->getField("PPK_STR");
$reqPemenangStr = $contracting->getField("PEMENANG_NAMA");
$reqPaketId = $contracting->getField("PAKET_ID");

// get data contracting_rekanan_proses1
$spmk->selectSPMK(array("A.CONTRACTINGREKANANID" => $reqId, "A.REKANAN_ID" => $reqRekananId));
$spmk->firstRow();

$reqContractingRekananProses1SpmkId = $spmk->getField('CONTRACTINGREKANANPROSES1SPMKID') ?: '-';
$reqNomor = $spmk->getField('NOMOR') ?: '-';
$reqSPMKDari = dateToPageCheck($spmk->getField('SPMK_DARI')) ?: date('d-m-Y');
$reqSPMKSampai = dateToPageCheck($spmk->getField('SPMK_SAMPAI')) ?: date('d-m-Y');
$reqKeterangan = $spmk->getField('KETERANGAN') ?: '-';

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
          <td>: <?= $reqNomor ?></td>
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
          <td> :  Surat Perintah Mulai Kerja (SPMK) <br>&nbsp; <?= $reqNama ?></td>
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
    DATA SPMK
    <table class="table table-bordered table-hover">
      <tbody>
        <tr>
          <td width="30%"> Nomor </td><td>: <?= $reqNomor ?></td>
        </tr>
        <tr>
          <td> Masa Pelaksanaan Pekerjaan </td><td>: <?= getFormattedDateView($reqSPMKDari) ?> s/d <?= getFormattedDateView($reqSPMKSampai) ?></td>
        </tr>
        <tr>
          <td> Keterangan </td><td>: <?= $reqKeterangan ?></td>
        </tr>
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
