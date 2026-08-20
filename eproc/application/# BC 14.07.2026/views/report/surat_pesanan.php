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
$this->load->model(array("Contracting","Contractingsuratpesanan"));
$this->load->model("Paketpemenang");
$this->load->model("Region");
$this->load->model("Rekanan");
$this->load->model("Contractingrekanan");

$getProses = $this->session->userdata('setProsesKontrak');

$contracting = new Contracting();
$getpaket_pemenang = new Paketpemenang();
$region = new Region();
$rekanan = new Rekanan();

$unitkerjaid = $this->input->get("unitkerjaid");
$reqId = $this->input->get("reqId"); // CONTRACTINGREKANANPROSES1ID
$reqConRekId = $this->input->get("reqConRekId"); // CONTRACTINGREKANANID
$reqSuratPesananId = $this->input->get("reqSuratPesananId"); // SURATPESANANID


$getMenu = new Contracting();
// $kontrak = new Contracting();
$contractingrekanan = new Contractingrekanan();
$spkpks = new Contractingrekanan();
$sppbj = new Contractingrekanan();
$proses4 = new Contractingrekanan();
$legal = new Contractingrekanan();
$suratpensanan = new Contractingsuratpesanan();

$sppbj->selectViewSPPBJ(array("A.CONTRACTINGREKANANPROSES1ID" => $reqId));
$sppbj->firstRow();
$reqNilaiSPPBJ = $sppbj->getField('CR_SPPBJ_NILAI') ?: '';
$reqDirutSPPBJ = $sppbj->getField('CR_SPPBJ_DIRUT') ?: '';
$reqDirutJabatanSPPBJ = $sppbj->getField('CR_SPPBJ_DIRUT_JABATAN') ?: '';
$reqPejabatBerwenangSPPBJ = $sppbj->getField('CR_SPPBJ_PEJABAT_BERWENANG') ?: '';
$reqJabatanSPPBJ = $sppbj->getField('CR_SPPBJ_JABATAN') ?: '';
$reqContractingRekananId = $sppbj->getField('CONTRACTINGREKANANID') ?: '';

$contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqContractingRekananId));
$contracting->firstRow();
$reqNamaPaket = $contracting->getField("NAMA");
$reqPanitiaStr = $contracting->getField("PANITIA_STR");
$reqPenggunaStr = $contracting->getField("PENGGUNA_STR");
$reqPpkStr = $contracting->getField("PPK_STR");
$reqPemenangStr = $contracting->getField("PEMENANG_NAMA");

$spkpks->selectViewPKSSPK(array("A.CONTRACTINGREKANANPROSES1ID" => $reqId));
$spkpks->firstRow();
$reqContractingRekananProses1Id = $spkpks->getField('CONTRACTINGREKANANPROSES1ID') ?: '-';
$reqJenisKontrak = $contracting->getField('JENIS_KONTRAK') ?: '-';
$reqCode = $spkpks->getField('CR_CODE') ?: '';
$reqJnsKontrakStr = $spkpks->getField('JNS_KONTRAK_STR') ?: '';
$reqRekananProses1Id = $spkpks->getField('CONTRACTINGREKANANPROSES1ID') ?: '-';
$reqPaketId = $spkpks->getField('PAKET_ID') ?: '-';
$reqContractingRekananId = $spkpks->getField('CONTRACTINGREKANANID') ?: '-';
$reqJenisPengadaan = $spkpks->getField('CR_JENIS_PENGADAAN') ?: '-';
$reqJenisPengadaanStr = $spkpks->getField('CR_JENIS_PENGADAAN_STR') ?: '-';
$reqJenisPekerjaan = $spkpks->getField('CR_JENIS_PEKERJAAN') ?: '';
$reqJenisPekerjaanStr = $spkpks->getField('CR_JENIS_PEKERJAAN_STR') ?: '';
$reqContractingjeniskontrakid = $spkpks->getField('CONTRACTINGJENISKONTRAKID') ?: '-';
$reqJenisKontrakStr = $spkpks->getField('CR_JENIS_KONTRAK_STR') ?: '-';
$reqWaktuPelaksanaanDari = dateToPageCheck($spkpks->getField('CR_WAKTU_PELAKSANAAN_DARI')) ?: '';
$reqWaktuPelaksanaanSampai = dateToPageCheck($spkpks->getField('CR_WAKTU_PELAKSANAAN_SAMPAI')) ?: '';
$reqLingkupPekerjaan = $spkpks->getField('CR_LINGKUP_PEKERJAAN') ?: '-';
$reqNilaiKontrak = $spkpks->getField('CR_NILAI_KONTRAK') ?: $reqNilaiSPPBJ;
$reqMetodePembayaran = $spkpks->getField('CR_METODE_PEMBAYARAN') ?: '-';
$reqPihak1Nama = $spkpks->getField('CR_PIHAK1_NAMA') ?: $reqPejabatBerwenangSPPBJ;
$reqPihak1Jabatan = $spkpks->getField('CR_PIHAK1_JABATAN') ?: $reqJabatanSPPBJ;
$reqPihak2Nama = $spkpks->getField('CR_PIHAK2_NAMA') ?: $reqDirutSPPBJ;
$reqPihak2Jabatan = $spkpks->getField('CR_PIHAK2_JABATAN') ?: $reqDirutJabatanSPPBJ;
$reqPihak2 = $spkpks->getField('CR_PIHAK2_PERUSAHAAN') ?: '';
$reqCreatedBy = $spkpks->getField('CR_CREATED_BY') ?: '-';
$reqContractingStatusKontrakId = $spkpks->getField('CONTRACTINGSTATUSKONTRAKID') ?: '-';
$reqRakananId = $spkpks->getField('REKANAN_ID') ?: '-';

$legal->selectViewLegal(array("A.CONTRACTINGREKANANPROSES1ID" => $reqId));
$legal->firstRow();
$reqLegalNomorPKS = $legal->getField('CR_LEGAL_NOMOR_PKS') ?: '';
$reqLegalTanggal = dateToPageCheck($legal->getField('CR_LEGAL_TANGGAL')) ?: '';
$reqLegalNomorRekanan = $legal->getField('CR_LEGAL_NOMOR_REKANAN') ?: '';
$reqLegalTanggalRekanan = dateToPageCheck($legal->getField('CR_LEGAL_TANGGAL_REKANAN')) ?: '';
$reqLegalCreatedBy = $legal->getField('CR_LEGAL_CREATED_BY') ?: '';
$reqLegalCreatedDate = $legal->getField('CR_LEGAL_CREATED_DATE') ?: '';
$reqLegalUpdatedBy = $legal->getField('CR_LEGAL_UPDATED_BY') ?: '';
$reqLegalUpdatedDate = $legal->getField('CR_LEGAL_UPDATED_DATE') ?: '';

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

if ($reqSuratPesananId) {
  $reqSubmit = 'update';

  $v = $this->libkontrak->getNilaiKontrakPenyediaByNilai(" AND REKANAN_ID = ".$reqRakananId." AND CONTRACTINGREKANANID = ".$reqContractingRekananId); 
  $sisaNilaiKontrak = $v['sisa'];
} else {
  $reqSubmit = 'simpan';
  $sisaNilaiKontrak = '';
}

$suratpensanan->selectByParams(array("A.SURATPESANANID" => $reqSuratPesananId));
$suratpensanan->firstRow();
$reqNoSuratPesanan = $suratpensanan->getField('NOMOR_SURAT') ?: '';
$reqTglSuratPesanan = dateToPageCheck($suratpensanan->getField('TANGGAL')) ?: '';
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
          <td style="width:100px !important"> Nomor </td>
          <td>: <?= $reqNoSuratPesanan ?></td>
        </tr>
        <tr> 
          <td> Tanggal</td>
          <td> : <?= getFormattedDateView($reqTglSuratPesanan) ?></td>
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
          <td> :  Surat Pesanan</td>
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
    DATA BARANG JASA
    <table style="width: 100%">
      <tbody>
        <tr class="tr-bc">
          <td class="td" style="width:10px">No</td>
          <td class="td">Deskripsi</td>
          <td class="td" style="width:10px">Vol/Qty</td>
          <td class="td" style="width:10px">Satuan</td>
          <td class="td" style="width:100px">Harga Satuan</td>
          <td class="td" style="width:100px">Total</td>
        </tr>
        <?php
        $no=1;
        $suratpensananmaterial = new Contractingsuratpesanan();
        $suratpensananmaterial->selectByParamsSuratPesananMaterial(array("A.SURATPESANANID" => $reqSuratPesananId));
        if ($suratpensananmaterial->countRow() > 0) {
          while($suratpensananmaterial->nextRow())
          { 
              $sumTotal += $suratpensananmaterial->getField('TOTAL');

          ?>
          <tr>
            <td class="td"><?= $no; ?></td>
            <td class="td"><?= $suratpensananmaterial->getField('NAMA') ?></td>
            <td class="td"><?= $suratpensananmaterial->getField('QTY'); ?></td>
            <td class="td"><?= $suratpensananmaterial->getField('SATUAN'); ?></td>
            <td class="td"><?= currencyToPage($suratpensananmaterial->getField('HARGA_SATUAN')) ?></td>
            <td class="td"><?= currencyToPage($suratpensananmaterial->getField('TOTAL')) ?></td>
          </tr>
          <?php
          $no++;
          }
        } else { echo '<tr><td colspan="3">. : : Tidak ada data : : .</td></tr>';} ?>
      </tbody>
      <tfoot>
        <tr class="tr-bc">
          <td class="td" colspan="5"> <b>TOTAL</b> </td>
          <td class="td"><span id="sumTotal"><?= numberToIna($sumTotal); ?></span></td>
        </tr>
      </tfoot>
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
