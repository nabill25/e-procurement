<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$reqId = httpFilterRequest("reqId"); // contractingrekananid

$this->libsession->cekSessionKontrak($reqId);

$this->load->library("kauth");  $userLogin = new kauth();

$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqProses = httpFilterRequest("reqProses");
$getTahun = $this->session->userdata('setTahunKontrak');

$this->load->model(array("Contracting","Contractingjaminan"));
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
$reqPO = $spkpks->getField('CR_PO') ?: '-';
$reqTglHasilTerimaPilihan = $spkpks->getField('CR_TGL_HASIL_TERIMA_PEMILIHAN') ?: '-';
$reqPenyelesaianAwal = $spkpks->getField('CR_PENYELESAIAN_KONTRAK_AWAL') ?: '-';
$reqPenyelesaianAkhir = $spkpks->getField('CR_PENYELESAIAN_KONTRAK_AKHIR') ?: '-';
$reqMasaGaransi = $spkpks->getField('CR_MASA_GARANSI') ?: '-';
$reqMasaGaransiPeriode = $spkpks->getField('CR_MASA_GARANSI_PERIODE') ?: '-';

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
$reqPemutusan = $proses4->getField('CR_PEMUTUSAN') ?: '';
$reqPemutusanAlasan = $proses4->getField('CR_PEMUTUSAN_ALASAN') ?: '';
$reqPemutusanFile = $proses4->getField('CR_PEMUTUSAN_FILE') ?: '';
$reqPemutusanUpdated = $proses4->getField('CR_PEMUTUSAN_UPDATED_DATE') ? explode(' ',$proses4->getField('CR_PEMUTUSAN_UPDATED_DATE')) : '';
$reqPemutusanUpdatedDate = $reqPemutusanUpdated[0];
$reqPemutusanUpdatedDate2 = $reqPemutusanUpdated[1];

$textMonitoring->selectText(array("A.TYPE" => 'Pemutusan'));
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
      <?= $this->libkontrak->getMenuPenyedia($reqId); ?>
    </div>
  </div>
  <div class="col-md-9 col-sm-9">
    <div class="card">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <h4 class="mb-2">Pemutusan Kontrak</h4>
      <?php
      if ($reqPemutusanAlasan == '')
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
                    <small>'.getFormattedDate($reqPemutusanUpdatedDate).' '.$reqPemutusanUpdatedDate2.'</small><br><b>Alasan Pemutusan</b>: <i>'.$reqPemutusanAlasan.'</i><br>
                    <a href="uploads/kontrak/'.$reqPemutusanFile.'" target="_blank" class="badge badge-primary"><span class="fa fa-download"></span> Download File</a>
                  </div>
                </div>
              </div>';
      ?>

          <div class="form-actions">

            <?php 
            $contractingjaminan = new Contractingjaminan();
            $contractingjaminan->selectByParams(array("CONTRACTINGREKANANID" => $reqId)); ?>
            <h4>Jaminan</h4>
            <table class="table table-bordered">
              <thead>
                <tr class="backcolornew">
                  <td>Nomor Jaminan</td>
                  <td>Tanggal Jaminan</td>
                  <td width="100">File <br>Jaminan</td>
                  <td width="100">Status</td>
                </tr>
              </thead>
              <tbody>
                <?php 
                if ($contractingjaminan->countRow() > 0) {
                  while($contractingjaminan->nextRow())
                  { ?>
                    <tr>
                      <td><?= $contractingjaminan->getField("NOMOR")?></td>
                      <td><?= getFormattedDateShort2(dateTimeToPageCheck($contractingjaminan->getField("TANGGAL_JAMINAN")))?></td>
                      <td><a href="uploads/kontrak/<?= $contractingjaminan->getField("FILE_JAMINAN")?>" target="_blank" class="badge badge-primary"><span class="fa fa-download"> Download</span></a></td> 
                      <?php
                        if ($contractingjaminan->getField("KONFIRMASI") == '1') { ?>
                        <td><span class="badge badge-primary">Sesuai</span></td>
                      <?php } else if ($contractingjaminan->getField("KONFIRMASI") == '2') { ?>
                        <td><span class="badge badge-info">Cair</span></td>
                      <?php } else { ?>
                        <td><span class="badge badge-danger">Tidak Sesuai</span></td>
                      <?php } ?> 
                    </tr>
                <?php } 
                    } ?>
              </tbody>
            </table>

             <?= $this->libkontrak->getInfoKontrak($reqId); ?> 

              <div class="card mb-1 border-blue border-darken-1">
                <div class="card-content">
                  <div class="p-1">
                    <h5>Dokumen Pemutusan Kontrak</h5>
                    <div class="table-responsive">
                      <table id="dokumenFileIdTable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                        <?= $this->libkontrak->getTableFilePenyedia($reqId," AND A.FILE_JENIS = 'Pemutusan Kontrak' AND FILE_PUBLISH_PENYEDIA = '1'") ?>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

      <?php
      } ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
