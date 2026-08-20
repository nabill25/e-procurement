<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$reqId = httpFilterRequest("reqId"); // contractingrekananid

$this->libsession->cekSession($reqId);

$this->load->library("kauth");  $userLogin = new kauth();

$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqProses = httpFilterRequest("reqProses");
$getTahun = $this->session->userdata('setTahunKontrak');


$this->load->model("Contracting");
$this->load->model("Contractingrekanan");

$contracting = new Contracting();
$spkpks = new Contractingrekanan();
$legal = new Contractingrekanan();
$proses4 = new Contractingrekanan();
$textMonitoring = new Contractingrekanan();

$contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
$contracting->firstRow();

$spkpks->selectViewPKSSPK(array("A.CONTRACTINGREKANANID" => $reqId));
$spkpks->firstRow();

$reqContractingRekananProses1Id = $spkpks->getField('CONTRACTINGREKANANPROSES1ID') ?: '-';
$reqJenisKontrak = $contracting->getField('JENIS_KONTRAK') ?: '-';
$reqCode = $spkpks->getField('CR_CODE') ?: '';
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
$reqNilaiKontrak = $spkpks->getField('CR_NILAI_KONTRAK') ?: '-';
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

$proses4->selectProses4(array("A.CONTRACTINGREKANANID" => $reqId));
$proses4->firstRow();

if ($proses4->countRow() > 0) {
  $reqSubmit = 'update';
} else {
  $reqSubmit = 'simpan';
}

$reqContractingRekananProses4Id = $proses4->getField('CONTRACTINGREKANANPROSES4ID') ?: '';
$reqBerakhir = $proses4->getField('CR_BERAKHIR') ?: '';
$reqBerakhirAlasan = $proses4->getField('CR_BERAKHIR_ALASAN') ?: '';
$reqBerakhirUpdated = $proses4->getField('CR_BERAKHIR_UPDATED_DATE') ? explode(' ',$proses4->getField('CR_BERAKHIR_UPDATED_DATE')) : '';
$reqBerakhirUpdatedDate = $reqBerakhirUpdated[0];
$reqBerakhirUpdatedDate2 = $reqBerakhirUpdated[1];

$textMonitoring->selectText(array("A.TYPE" => 'Berakhir'));
$textMonitoring->firstRow();
$reqText = $textMonitoring->getField('KETERANGAN') ?: '';

?>

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/ui/jqueryui.css">
<script src="<?=base_url()?>assets/new/js/scripts/ui/jquery-ui/navigations.js"></script>
<style type="text/css">
  small { font-weight: bold; font-size: 11.5px }
  tr.backcolornew {
    background: #b11016 !important;
    color: #fff;
  }
</style>

<div class="row">
  <div class="col-md-3 col-sm-3">
    <div class="jqueryui-ele-container">
      <?= $this->libkontrak->getMenuLegal($reqId); ?>
    </div>
  </div>
  <div class="col-md-9 col-sm-9">
    <div class="card">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <h4 class="mb-2">Berakhir Kontrak</h4>
      <?php
      if ($reqBerakhirAlasan == '')
      { // Jika Ada Perubahan Kontrak
      ?>
          <div class="row mb-1">
            <div class="col-md-12">
              <p>
                <?= $reqText ?>
              </p>
            </div>
          </div>
      <?php
      } else
      {
        echo '<div class="card mb-1 border-blue border-darken-1" style="padding: 5px 10px 0 10px; background-color: #fff3f3">
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <small>'.getFormattedDate($reqBerakhirUpdatedDate).' '.$reqBerakhirUpdatedDate2.'</small><br><b>Alasan Berakhir Kontrak</b>: <i>'.$reqBerakhirAlasan.'</i>
                  </div>
                </div>
              </div>';
      ?>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <h5>Dokumen Berakhir Kontrak</h5>
                <div class="table-responsive">
                  <table id="dokumenFileIdTable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                    <?= $this->libkontrak->getTableFile($reqId," AND A.FILE_JENIS = 'Berakhir Kontrak'") ?>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="form-actions">

            <table class="table table-bordered table-hover">
              <tbody>
                <tr>
                  <td width="25%" colspan="2">
                    <small>Nomor <?= $reqJenisKontrak ?> <?= SYSTEM_NAME_PT ?></small>  <br> <?= $reqLegalNomorPKS ?>
                  </td>
                  <td width="25%" colspan="2">
                    <small>Tanggal  <?= $reqJenisKontrak ?></small> <br> <?= $reqLegalTanggal ?>
                  </td>
                </tr>
                <tr>
                  <td width="50%" colspan="4">
                    <small>Nomor PKS Penyedia</small> <br> <?= $reqLegalNomorRekanan ?>
                  </td>
                  <!-- <td width="25%" colspan="2">
                    <small>Tanggal </small> <br> <?= $reqLegalTanggalRekanan ?>
                  </td>  -->
                </tr>
                <tr>
                  <td width="25%" colspan="2">
                    <small>Nomor Kontrak</small> <br> <?= $reqCode ?>
                  </td>
                  <td width="25%" colspan="2">
                    <small>Jenis Kontrak</small> <br> <?= $reqJenisKontrakStr ?>
                  </td>
                </tr>
                <tr>
                  <td width="25%" colspan="2">
                    <small>Jenis Pengadaan</small> <br> <?= $reqJenisPengadaanStr ?>
                  </td>
                  <td width="25%" colspan="2">
                    <small>Jenis Pekerjaan</small> <br> <?= $reqJenisPekerjaanStr ?>
                  </td>
                </tr>
                <tr>
                  <td width="25%" colspan="2">
                    <small>Jangka Waktu Pelaksanaan </small> <br> <?= getFormattedDateShort2(dateTimeToPageCheck($reqWaktuPelaksanaanDari)).' s/d '.getFormattedDateShort2(dateTimeToPageCheck($reqWaktuPelaksanaanSampai)) ?>
                  </td>
                  <td width="15%">
                    <small>Nilai Pekerjaan </small> <br>  <?= currencyToPage($reqNilaiKontrak) ?>
                  </td>
                  <td width="10%">
                    <small>Metode Pembayaran </small> <br>
                    <?php
                    if ($reqMetodePembayaran == '1') {
                       echo "Sekaligus";
                    } else { echo "Tempo"; } ?>
                  </td>
                </tr>
                <tr>
                  <td colspan="4">
                    <small>Lingkup Pekerjaan</small> <br> <?= $reqLingkupPekerjaan ?>
                  </td>
                </tr>
                <tr>
                  <td width="25%" colspan="2">
                    <small>PIHAK I </small> <br>
                    <?= $reqPihak1Nama ?> <br>
                    <i><?= $reqPihak1Jabatan ?></i>
                  </td>
                  <td width="25%" colspan="2">
                    <small>PIHAK II </small> <br>
                    <?= $reqPihak2Nama ?> <br>
                    <i><?= $reqPihak2Jabatan ?></i>
                  </td>
                </tr>
              </tbody>
            </table>

      <?php
      } ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
