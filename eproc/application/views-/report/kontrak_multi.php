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

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Contracting");
$this->load->model("Contractingrekanan");
$this->load->model("Paketpemenang");

$getpaket_pemenang = new Paketpemenang();
$getpaket_pemenang2 = new Paketpemenang();

$paketInfo->getPaket($reqPaketId);
$reqMetodeLelangId = $paketInfo->metode_lelang_id;
$bidding = $paketInfo->bidding;
$reqMultiPemenang = $paketInfo->multi_pemenang;

if ($reqMultiPemenang == '0') {
  $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqPaketId, "PERINGKAT" => '1'), -1, -1); 
  $getpaket_pemenang2->selectByParams(array("PAKET_ID" => $reqPaketId, "PERINGKAT" => '1'), -1, -1);
} else {
  $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1);
  $getpaket_pemenang2->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1);
}


// $this->load->model("Contracting");
// $this->load->model("Contractingrekanan");

$contracting = new Contracting();
$spkpks = new Contractingrekanan();
// $legal = new Contractingrekanan();

$contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
$contracting->firstRow();

$spkpks->selectViewPKSSPK(array("A.CONTRACTINGREKANANID" => $reqId));
$spkpks->firstRow();

$reqJenisKontrak = $contracting->getField('JENIS_KONTRAK') ?: '-';
$reqCode = $spkpks->getField('CR_CODE') ?: '-';
$reqJnsKontrakStr = $spkpks->getField('JNS_KONTRAK_STR') ?: ''; 
// $reqRekananProses1Id = $spkpks->getField('CONTRACTINGREKANANPROSES1ID') ?: '-';
// $reqPaketId = $spkpks->getField('PAKET_ID') ?: '-';
// $reqContractingRekananId = $spkpks->getField('CONTRACTINGREKANANID') ?: '-';
// $reqJenisPengadaan = $spkpks->getField('CR_JENIS_PENGADAAN') ?: '-';
// $reqJenisPengadaanStr = $spkpks->getField('CR_JENIS_PENGADAAN_STR') ?: '-';
// $reqJenisPekerjaan = $spkpks->getField('CR_JENIS_PEKERJAAN') ?: '-';
// $reqJenisPekerjaanStr = $spkpks->getField('CR_JENIS_PEKERJAAN_STR') ?: '-';
// $reqContractingjeniskontrakid = $spkpks->getField('CONTRACTINGJENISKONTRAKID') ?: '-';
// $reqJenisKontrakStr = $spkpks->getField('CR_JENIS_KONTRAK_STR') ?: '-';
// $reqWaktuPelaksanaanDari = $spkpks->getField('CR_WAKTU_PELAKSANAAN_DARI') ?: '-';
// $reqWaktuPelaksanaanSampai = $spkpks->getField('CR_WAKTU_PELAKSANAAN_SAMPAI') ?: '-';
// $reqLingkupPekerjaan = $spkpks->getField('CR_LINGKUP_PEKERJAAN') ?: '-';
// $reqNilaiKontrak = $spkpks->getField('CR_NILAI_KONTRAK') ?: '0';
// $reqMetodePembayaran = $spkpks->getField('CR_METODE_PEMBAYARAN') ?: '-';
// $reqPihak1Nama = $spkpks->getField('CR_PIHAK1_NAMA') ?: '-';
// $reqPihak1Jabatan = $spkpks->getField('CR_PIHAK1_JABATAN') ?: '-';
// $reqPihak2Nama = $spkpks->getField('CR_PIHAK2_NAMA') ?: '-';
// $reqPihak2Jabatan = $spkpks->getField('CR_PIHAK2_JABATAN') ?: '-';
// $reqPihak2 = $spkpks->getField('CR_PIHAK2_PERUSAHAAN') ?: '-';
// $reqCreatedBy = $spkpks->getField('CR_CREATED_BY') ?: '-';
// $reqContractingStatusKontrakId = $spkpks->getField('CONTRACTINGSTATUSKONTRAKID') ?: '-';
// $reqRakananId = $spkpks->getField('REKANAN_ID') ?: '-';

// $legal->selectViewLegal(array("A.CONTRACTINGREKANANID" => $reqId));
// $legal->firstRow();
// $reqLegalNomorPKS = $legal->getField('CR_LEGAL_NOMOR_PKS') ?: '-';
// $reqLegalTanggal = $legal->getField('CR_LEGAL_TANGGAL') ?: '-';
// $reqLegalNomorRekanan = $legal->getField('CR_LEGAL_NOMOR_REKANAN') ?: '-';
// $reqLegalTanggalRekanan = $legal->getField('CR_LEGAL_TANGGAL_REKANAN') ?: '-';
// $reqLegalCreatedBy = $legal->getField('CR_LEGAL_CREATED_BY') ?: '-';
// $reqLegalCreatedDate = $legal->getField('CR_LEGAL_CREATED_DATE') ?: '-';
// $reqLegalUpdatedBy = $legal->getField('CR_LEGAL_UPDATED_BY') ?: '-';
// $reqLegalUpdatedDate = $legal->getField('CR_LEGAL_UPDATED_DATE') ?: '-';


// if($reqMetodeLelangId == 1 || $reqMetodeLelangId == 3)
// {
//     $tempPublishTanggalJam = $paketInfo->publish_paket_tanggal;
//     $arrPublishTanggalJam = explode(" ", $tempPublishTanggalJam);
//     $arrPublishJamMenit = explode(":", $arrPublishTanggalJam[1]);
//     $tempPublishTanggal = $arrPublishTanggalJam[0];
//     $tempPublishJam = $arrPublishJamMenit[0];
//     $tempPublishMenit = $arrPublishJamMenit[1];
// }

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
            
          <?php 
          $no = 1;
          while($getpaket_pemenang->nextRow())
          { 
            $contractingrekananSPPBJ = new Contractingrekanan();
            $contractingrekananSPPBJ->selectViewPKSSPK(array("A.PAKET_ID" => $reqPaketId, "A.REKANAN_ID" => $getpaket_pemenang->getField("REKANAN_ID")));
            $contractingrekananSPPBJ->firstRow(); 
            ?>
            <table style="margin-bottom:20px">
              <tbody>
                <tr>
                  <td width="200px"> Nama Penyedia</td>
                  <td> : <?= $getpaket_pemenang->getField("NAMA"); ?></td>
                </tr> 
                <tr>
                  <td> No. <?= $reqJnsKontrakStr ?> </td> 
                  <td> : <?= $contractingrekananSPPBJ->getField("CR_LEGAL_NOMOR_PKS") ?: '-'; ?></td>
                </tr>
              </tbody>
            </table>
          <?php 
          $no++;
          } ?> 
          </td>
  </div>


  <div class="area-dokumen" style="margin-top:20px">
    DAFTAR BARANG JASA
    <table class="table">
      <tr class="tr">
        <td class="td">No</td>
        <td class="td">Deskripsi</td>
        <td class="td">Vol/Qty</td>
        <td class="td">Satuan</td>
        <td class="td" width="15%">Harga Satuan</td>
      </tr>
      <?php
      $this->load->model("Contractingmaterial");
      $datamaterial = new Contractingmaterial();
      $datamaterial->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
      if ($datamaterial->countRow() > 0) {
        $no=1;
        while($datamaterial->nextRow()) {
        ?>
        <tr>
          <td class="td" width="10px"><?= $no; ?></td>
          <td class="td"><?= $datamaterial->getField('NAMA') ?></td>
          <td class="td" width="10%"><?= $datamaterial->getField('QTY'); ?></td>
          <td class="td" width="10%"><?= $datamaterial->getField('SATUAN_STR'); ?></td>
          <td class="td"><?= currencyToPage($datamaterial->getField('HARGA_SATUAN')) ?></td>
        </tr>
        <?php
        $no++;
        }
      } else { echo '<tr><td colspan="3">. : : Tidak ada data : : .</td></tr>';} ?>
    </table>
  </div>

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
        // while($datasanksi->nextRow()) {
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
  
  
</body>
</html>
