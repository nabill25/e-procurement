<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->library("paketinfo"); $paketInfo = new paketinfo(); 
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->library("AES"); 

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
$reqPaketId = $this->input->get("reqPaketId");

$paketInfo->getPaket($reqPaketId);
$reqMetodeLelangId = $paketInfo->metode_lelang_id;

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");


$this->load->model("Contracting");
$this->load->model("Contractingrekanan");
$this->load->model("Rekanan");

$contracting = new Contracting();
$spkpks = new Contractingrekanan();
$legal = new Contractingrekanan();
$rekanan = new Rekanan();

$contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
$contracting->firstRow();

$spkpks->selectViewPKSSPK(array("A.CONTRACTINGREKANANID" => $reqId));
$spkpks->firstRow();

$reqJenisKontrak = $contracting->getField('JENIS_KONTRAK') ?: '-';
$reqCode = $spkpks->getField('CR_CODE') ?: '-';
$reqJnsKontrakStr = $spkpks->getField('JNS_KONTRAK_STR') ?: ''; 
$reqRekananProses1Id = $spkpks->getField('CONTRACTINGREKANANPROSES1ID') ?: '-';
$reqPaketId = $spkpks->getField('PAKET_ID') ?: '-';
$reqContractingRekananId = $spkpks->getField('CONTRACTINGREKANANID') ?: '-';
$reqJenisPengadaan = $spkpks->getField('CR_JENIS_PENGADAAN') ?: '-';
$reqJenisPengadaanStr = $spkpks->getField('CR_JENIS_PENGADAAN_STR') ?: '-';
$reqJenisPekerjaan = $spkpks->getField('CR_JENIS_PEKERJAAN') ?: '-';
$reqJenisPekerjaanStr = $spkpks->getField('CR_JENIS_PEKERJAAN_STR') ?: '-';
$reqContractingjeniskontrakid = $spkpks->getField('CONTRACTINGJENISKONTRAKID') ?: '-';
$reqJenisKontrakStr = $spkpks->getField('CR_JENIS_KONTRAK_STR') ?: '-';
$reqWaktuPelaksanaanDari = $spkpks->getField('CR_WAKTU_PELAKSANAAN_DARI') ?: '-';
$reqWaktuPelaksanaanSampai = $spkpks->getField('CR_WAKTU_PELAKSANAAN_SAMPAI') ?: '-';
$reqLingkupPekerjaan = $spkpks->getField('CR_LINGKUP_PEKERJAAN') ?: '-';
$reqNilaiKontrak = $spkpks->getField('CR_NILAI_KONTRAK') ?: '0';
$reqMetodePembayaran = $spkpks->getField('CR_METODE_PEMBAYARAN') ?: '-';
$reqPihak1Nama = $spkpks->getField('CR_PIHAK1_NAMA') ?: '-';
$reqPihak1Jabatan = $spkpks->getField('CR_PIHAK1_JABATAN') ?: '-';
$reqPihak2Nama = $spkpks->getField('CR_PIHAK2_NAMA') ?: '-';
$reqPihak2Jabatan = $spkpks->getField('CR_PIHAK2_JABATAN') ?: '-';
$reqPihak2 = $spkpks->getField('CR_PIHAK2_PERUSAHAAN') ?: '-';
$reqCreatedBy = $spkpks->getField('CR_CREATED_BY') ?: '-';
$reqContractingStatusKontrakId = $spkpks->getField('CONTRACTINGSTATUSKONTRAKID') ?: '-';
$reqRakananId = $spkpks->getField('REKANAN_ID') ?: '-';

$legal->selectViewLegal(array("A.CONTRACTINGREKANANID" => $reqId));
$legal->firstRow();
$reqLegalNomorPKS = $legal->getField('CR_LEGAL_NOMOR_PKS') ?: '-';
$reqLegalTanggal = $legal->getField('CR_LEGAL_TANGGAL') ?: '-';
$reqLegalNomorRekanan = $legal->getField('CR_LEGAL_NOMOR_REKANAN') ?: '-';
$reqLegalTanggalRekanan = $legal->getField('CR_LEGAL_TANGGAL_REKANAN') ?: '-';
$reqLegalCreatedBy = $legal->getField('CR_LEGAL_CREATED_BY') ?: '-';
$reqLegalCreatedDate = $legal->getField('CR_LEGAL_CREATED_DATE') ?: '-';
$reqLegalUpdatedBy = $legal->getField('CR_LEGAL_UPDATED_BY') ?: '-';
$reqLegalUpdatedDate = $legal->getField('CR_LEGAL_UPDATED_DATE') ?: '-';

// Get Rekanan
$rekanan->selectByParams(array("A.REKANAN_ID" => $reqRakananId), -1, -1);
$rekanan->firstRow();
$rekanan_nama = $rekanan->getField("NAMA");
$rekanan_npwp = $rekanan->getField("NPWP");
$rekanan_alamat = $rekanan->getField("ALAMAT");
$rekanan_telepon = $rekanan->getField("TELEPON_FULL");
$rekanan_email = $rekanan->getField("EMAIL");
$rekanan_kota = $rekanan->getField("KOTA");
$rekanan_kodepos = $rekanan->getField("KODEPOS");


if($reqMetodeLelangId == 1 || $reqMetodeLelangId == 3)
{
    $tempPublishTanggalJam = $paketInfo->publish_paket_tanggal;
    $arrPublishTanggalJam = explode(" ", $tempPublishTanggalJam);
    $arrPublishJamMenit = explode(":", $arrPublishTanggalJam[1]);
    $tempPublishTanggal = $arrPublishTanggalJam[0];
    $tempPublishJam = $arrPublishJamMenit[0];
    $tempPublishMenit = $arrPublishJamMenit[1];
}

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
  <div class="judul">
  	DATA KONTRAK<br>
  
  <div class="pekerjaan">
   <?php  if($paketInfo->bahasa == "EN") echo "/ <em>WORKS</em>"; ?><br />
  <?=strtoupper($paketInfo->nama)?>
  </div><br> <br> 

  <div class="area-dokumen">
    <table class="table table-bordered table-hover">
      <tbody>
        <tr>
          <td width="35%"> Penyedia </td>
          <td>: <?= $rekanan_nama ?> </td>
        </tr>
        <tr>
          <td> NPWP </td>
          <td>: <?= $rekanan_npwp ?> </td>
        </tr>
        <tr>
          <td> Telpon </td>
          <td>: <?= $rekanan_telepon ?> </td>
        </tr>
        <tr>
          <td> Email </td>
          <td>: <?= $rekanan_email ?> </td>
        </tr>
        <tr>
          <td> Alamat </td>
          <td>: <?= $rekanan_alamat.', '.$rekanan_kota.' '.$rekanan_kodepos ?> </td>
        </tr>
        <tr>
          <td> Nomor <?= $reqJnsKontrakStr ?> <?= SYSTEM_NAME_PT ?> </td>
          <td>: <?= $reqLegalNomorPKS ?> </td>
        </tr>
        <tr> 
          <td> Tanggal  <?= $reqJnsKontrakStr ?></td>
          <td> : <?= $reqLegalTanggal ?></td>
        </tr>
        <tr>
          <td> Nilai Pekerjaan </small></td> 
          <td> : <?= currencyToPage($reqNilaiKontrak) ?></td> 
        </tr>
        <tr>
          <td>Metode Pembayaran</td>
          <td> : 
            <?php
            if ($reqMetodePembayaran == '1') {
               echo "Sekaligus";
            } else { echo "Termin"; } ?>
          </td>
        </tr>
        <tr>
          <td>Jenis Pengadaan</td>
          <td>: <?= $reqJenisPengadaanStr ?></td>
          </td>
        </tr>
        <tr>
          <td>Jenis Kontrak</td> 
          <td> : <?= $reqJenisKontrakStr ?></td>
        </tr> 
        <tr>
          <td> Jangka Waktu Pelaksanaan </td> 
          <td>: <?= getFormattedDateShort2(dateTimeToPageCheck($reqWaktuPelaksanaanDari)).' s/d '.getFormattedDateShort2(dateTimeToPageCheck($reqWaktuPelaksanaanSampai)) ?> </td>
        </tr>
        <tr>
          <td>Lingkup Pekerjaan</td> 
          <td> : <?= $reqLingkupPekerjaan ?>
          </td>
        </tr>
        <tr>
          <td>PIHAK I </td> 
          <td> : <?= $reqPihak1Nama ?> <small>(<?= $reqPihak1Jabatan ?>)</small></td>
        </tr>
        <tr>
          <td>PIHAK II </td> 
          <td> : <?= $reqPihak2Nama ?> <small>(<?= $reqPihak2Jabatan ?>)</small></td>
        </tr>
      </tbody>
    </table>
  </div>
  
  <div class="area-dokumen">
    DELIVERABLE PEKERJAAN
    <table class="table">
      <tr class="tr">
        <td class="td">Lingkup</td>
        <td class="td">Hasil Pekerjaan</td>
        <td class="td" widtd="100px">Status</td>
      </tr>
      <?php
      $this->load->model("Contractingdeliverable");
      $datadelivery = new Contractingdeliverable();
      $datadelivery->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
      if ($datadelivery->countRow() > 0) {
        while($datadelivery->nextRow()) {
        ?>
        <tr class="terang">
          <td class="td"><?= $datadelivery->getField('LINGKUP') ?></td>
          <td class="td"><?= $datadelivery->getField('DELIVERY_NAMA') ?></td>
          <td class="td" width="100px"><?= $datadelivery->getField('STATUS') ?></td>
        </tr>
        <?php
        }
      } else { echo '<tr><td colspan="3">. : : Tidak ada data : : .</td></tr>';} ?>
    </table>
  </div>

  <div class="area-dokumen">
    TERMIN PEMBAYARAN
    <table class="table">
      <tr class="tr">
        <?php if ($reqMetodePembayaran == 2) { ?>
        <td class="td" class="td">Termin</td>
        <td class="td">Keterangan</td>
        <?php
        } else { ?>
        <td class="td">Keterangan</td>
        <?php
        } ?>
        <td class="td">Nilai Pembayaran</td>
        <!-- <td class="td">Berita Acara</td> -->
        <td class="td" widtd="100px">Progres</td>
      </tr>
      <?php
      $this->load->model("Contractingpayment");
      $datapayment = new Contractingpayment();
      $datapayment->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
      if ($datapayment->countRow() > 0) {
        while($datapayment->nextRow()) {
        ?>
          <tr class="terang">
            <?php if ($reqMetodePembayaran == 2) { ?>
            <td class="td"><?= $datapayment->getField('PAY_TERMIN_KE') ?></td>
            <?php
            } ?>
            <td class="td"><?= $datapayment->getField('PAY_KETERANGAN') ?></td>
            <td class="td"><?= currencyToPage($datapayment->getField('PAY_NILAI')) ?></td>
            <!-- <td class="td"><?php // $datapayment->getField('PAY_LAMPIRAN') ?></td>  -->
            <td class="td"><?= $datapayment->getField('PAY_PROGRES') ?> %</td>
          </tr>
      <?php
      }
    } else { echo '<tr><td colspan="6">. : : Tidak ada data : : .</td></tr>';} ?>
    </table>
  </div>

  <?php
  if ($reqJenisPekerjaan == '1') { // Hanya untuk pekerjaan TI ?>  
  <div class="area-dokumen">
    SERVICE LEVEL AGREEMENT (SLA)
    <table class="table">
      <tr class="tr">
        <td class="td" width="100px">Availability</td>
        <td class="td">Waktu (jam)</td>
        <td class="td">Denda Keterlambatan </td>
        <td class="td">Biaya Maintanance</td>
        <td class="td">Nilai Denda</td>
      </tr>
      <?php
      $this->load->model("Contractingsla");
      $datsla = new Contractingsla();
      $datsla->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
      if ($datsla->countRow() > 0) {
        while($datsla->nextRow()) {
        ?>
          <tr class="terang">
            <td class="td"><?= $datsla->getField('SLA_AVAILABILITY').' %' ?></td>
            <td class="td"><?= $datsla->getField('SLA_WAKTU') ?></td>
            <td class="td"><?= $datsla->getField('SLA_DENDA').' % dari nilai biaya bulanan maintanance' ?></td>
            <td class="td"><?= currencyToPage($datsla->getField('SLA_BIAYA_MAINTANANCE')) ?></td>
            <td class="td"><?= currencyToPage($datsla->getField('SLA_NILAI_DENDA')) ?></td>
            <!-- <td class="td"><?php //$datsla->getField('SLA_STATUS') ?></td>  -->
          </tr>
        <?php
        }
      } else { echo '<tr><td colspan="6">. : : Tidak ada data : : .</td></tr>';} ?>
    </table>
  </div>
  <?php 
  } ?>

  <!-- <div class="area-dokumen">
    SANKSI DAN DENDA KETERLAMBATAN
    <table class="table">
      <tr class="tr">
        <td class="td">Nilai Sanksi</td>
        <td class="td">Nilai / Bagian Pekerjaan </td>
        <td class="td" width="100px">Hari Keterlambatan</td>
        <td class="td">Nilai Denda</td>
      </tr>
      <?php
      // $this->load->model("Contractingsanksi");
      // $datasanksi = new Contractingsanksi();
      // $datasanksi->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
      // if ($datasanksi->countRow() > 0) {
      //   while($datasanksi->nextRow()) {
        ?>
        <tr class="terang">
          <td class="td"><?php // echo $datasanksi->getField('NILAI_SANKSI') ?>/1000</td>
          <td class="td"><?php // echo currencyToPage($datasanksi->getField('NILAI_PEKERJAAN')) ?></td>
          <td class="td"><?php // echo $datasanksi->getField('HARI_TERLAMBAT') ?></td>
          <td class="td"><?php // echo currencyToPage($datasanksi->getField('NILAI_DENDA')) ?></td>
        </tr>
        <?php
      //   }
      // } else { echo '<tr><td colspan="6">. : : Tidak ada data : : .</td></tr>';} ?>
    </table>
    <p>
      <?php
      // $this->load->model("Contractingsanksi");
      // $datasanksi = new Contractingsanksi();
      // $datasanksi->selectByParamsKetentuan(array("CONTRACTINGREKANANID"=>$reqId, "JENIS" => "1"));
      // if ($datasanksi->countRow() > 0) {
      //   while($datasanksi->nextRow()) {
        ?> <?php // echo $datasanksi->getField('KETERANGAN') ?>
        <?php
      //   }
      // } else { echo '. : : Tidak ada keterangan : : .';} ?>
    </p>
  </div> -->

  <!-- <div class="area-dokumen" style="margin-top:5%">
    SANKSI DAN DENDA KELALAIAN 
    <p>
      <?php
      // $this->load->model("Contractingsanksi");
      // $datasanksi = new Contractingsanksi();
      // $datasanksi->selectByParamsKetentuan(array("CONTRACTINGREKANANID"=>$reqId, "JENIS" => "2"));
      // if ($datasanksi->countRow() > 0) {
      //   while($datasanksi->nextRow()) {
        ?> <?php // echo $datasanksi->getField('KETERANGAN') ?>
        <?php
      //   }
      // } else { echo '. : : Tidak ada keterangan : : .';} ?>
    </p>
  </div> -->

<div class="nomor-oe">
  <div class="data" style="font-size:10px; font-style:italic">
       <?= $this->libbreadcrumb->cetakcopyright($unitkerjaid) ?>
       <?php if($paketInfo->bahasa == "EN") echo "<br><br><em>".SYSTEM_SAH_EN."</em>"; ?>
  </div>
</div>
</body>
</html>
