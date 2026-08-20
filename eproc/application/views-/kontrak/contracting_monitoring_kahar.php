<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$reqId = httpFilterRequest("reqId");
$reqProses = httpFilterRequest("reqProses");
$getTahun = $this->session->userdata('setTahunKontrak');
$this->session->set_userdata('setProsesKontrak',$reqProses);

$this->libsession->cekSessionKontrakPPK($reqId);

$this->load->library("kauth");  $userLogin = new kauth();

$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

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
$reqKahar = $proses4->getField('CR_KAHAR') ?: '';
$reqKaharAlasan = $proses4->getField('CR_KAHAR_ALASAN') ?: '';
$reqKaharUpdated = $proses4->getField('CR_KAHAR_UPDATED_DATE') ? explode(' ',$proses4->getField('CR_KAHAR_UPDATED_DATE')) : '';
$reqKaharUpdatedDate = $reqKaharUpdated[0];
$reqKaharUpdatedDate2 = $reqKaharUpdated[1];

$textMonitoring->selectText(array("A.TYPE" => 'Kahar'));
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

<script type="text/javascript">
function prosesKontrak(flow,aidi,m) {
  $.messager.confirm('Konfirmasi',m,function(r){
    if (r){
      $.getJSON("contracting_json/proseskontrak/?reqAidi="+aidi+"&flow="+flow,
      function(data){
        alertSuccess2(data.PESAN);
        setTimeout(function() {
          if (data.FLOW == '6') {
            location.href = "kontrak/index/contracting_pengelolaan?tahun=<?= $getTahun ?>";
          } else {
            location.reload();
          }
        }, 2000);
      });
    }
  });
}

function publishFile(delele_link, id, stat)
  {
    if (stat == '1') {
      var messa = 'Kirim Dokumen ke Penyedia ?';
    } else {
      var messa = 'Batal kirim Dokumen ke Penyedia ?';
    }

    $.messager.confirm('Konfirmasi',messa,function(r){
      if (r){
        var jqxhr = $.get( delele_link+'?reqId='+id+'&status='+stat, function(data) {
        })
        .done(function(data) {
          alertSuccess2(data);
          setTimeout(function() {
            document.location.reload();
          }, 2000);
        })
        .fail(function() {
          alertError2('Data gagal diproses, silahkan coba kembali'); // gagal
        });
      }
    });
  }

</script>

<?= $this->libchat->kontrak($reqId); ?>
<div class="row">
  <div class="col-md-3 col-sm-3">
    <div class="jqueryui-ele-container">
      <?= $this->libkontrak->getMenu($reqId,$reqProses); ?>
    </div>
  </div>
  <div class="col-md-9 col-sm-9">
    <div class="card">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <h4 class="mb-2">Keadaan Kahar</h4>
      <?php
      if ($reqKaharAlasan == '')
      { // Jika Ada Perubahan Kontrak
      ?>
          <div class="row mb-1">
            <div class="col-md-12">
              <p>
                <?= $reqText ?>
              </p>
              <?php
              if ( $this->LEGAL != '1' && $this->USER_TYPE_ID != '20') { ?>
              <a class="<?= CLASS_BTN_DANGER ?> mb-1" onClick="openAdd('kontrak/loadUrl/kontrak/contracting_monitoring_kahar_edit/?reqId=<?=$reqId?>');" style="color:#fff"> <span class="fa fa-angle-double-right" style="color:#fff"></span> Keadaan Kahar ?</a>
              <?php
              } ?>
            </div>
          </div>
      <?php
      } else
      {
        echo '<div class="card mb-1 border-blue border-darken-1" style="padding: 5px 10px 0 10px; background-color: #fff3f3">
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <small>'.getFormattedDate($reqKaharUpdatedDate).' '.$reqKaharUpdatedDate2.'</small><br><b>Alasan Keadaan Kahar</b>: <i>'.$reqKaharAlasan.'</i>
                  </div>
                </div>
              </div>';
      ?>
          <div class="row mb-1">
            <div class="col-md-12">
              <?php
              // Yang kirim ke penyedia bagian Legal & Data PKS Sudah di isi
              if ($this->LEGAL == '1' && $reqLegalNomorPKS != '-' && $this->USER_TYPE_ID != '20') {
                echo $this->libkontrak->getStatusKontrakTeruskan($reqContractingStatusKontrakId,$reqId,$this->USER_TYPE_ID,$this->LEGAL,'SPK-PKS');
              } else {
                if ($reqContractingStatusKontrakId == '4') { // informasi proses persetujuan penyedia untuk peng. kontrak
                  echo $this->libkontrak->getStatusKontrakTeruskan($reqContractingStatusKontrakId,$reqId,$this->USER_TYPE_ID,$this->LEGAL,'SPK-PKS');
                }
              }
              ?>
            </div>
          </div>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <h5>Dokumen Keadaan Kahar
                <?php
                if ($reqContractingStatusKontrakId >= '2') { // Penyedia sudah approve ?>
                <small style="font-size:0.7em">
                  <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_file_add?reqAidi=<?= $reqId ?>&reqProses=4&reqJenis=Keadaan Kahar')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Dokumen </a>
                </small>
                <?php
                } ?>
                </h5>
                <div class="table-responsive">
                  <table id="dokumenFileIdTable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                    <?= $this->libkontrak->getTableFile($reqId," AND A.FILE_JENIS = 'Keadaan Kahar' ") ?>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="form-actions">

            <?php
            if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20') { // Penyedia sudah approve & Peng. Kontrak bagian Legal tapi User Pengguna, pemeriksa ?>
            <a href="kontrak/index/contracting_persiapan_kontrak_edit?reqId=<?= $reqId ?>&back=contracting_monitoring_kahar" class="<?= CLASS_BTN_PRIMARY ?> mr-1 mb-1 text-white"> <i class="fa fa-pencil"></i> Edit Data Kontrak </a>
            <?php
            } else { ?>
            <!-- <a href="kontrak/index/contracting_persiapan_kontrak_edit_legal?reqId=<?= $reqId ?>&back=contracting_monitoring_kahar" class="btn btn-primary mr-1 mb-1 text-white"> <i class="fa fa-pencil"></i> Edit Data <?= $reqJenisKontrak ?> </a>  -->
            <?php
            } ?>

            <button class="<?= CLASS_BTN_SUCCESS ?> mb-1" data-toggle="modal" data-target=".bs-example-modal-lg"><span class="fa fa-eye"></span> Lihat Dokumen Pendukung Pemilihan </button>

              <table class="table table-bordered table-hover">
                <tbody>
                  <tr>
                    <td width="25%" colspan="2">
                      <small>Nomor <?= $reqJnsKontrakStr ?> <?= SYSTEM_NAME_PT ?> </small> <br> <?= $reqLegalNomorPKS ?>
                    </td>
                    <td width="25%" colspan="2">
                      <small>Tanggal  <?= $reqJnsKontrakStr ?></small> <br> <?= $reqLegalTanggal ?>
                    </td>
                  </tr>
                  <tr>
                    <!-- <td width="50%" colspan="4">
                      <small>Nomor PKS Penyedia</small> <br> <?= $reqLegalNomorRekanan ?>
                    </td> -->
                    <!-- <td width="25%" colspan="2">
                      <small>Tanggal </small> <br> <?= $reqLegalTanggalRekanan ?>
                    </td>  -->
                  </tr>
                  <tr>
                    <td width="13%">
                      <small>Nilai Pekerjaan </small> <br>  <?= currencyToPage($reqNilaiKontrak) ?>
                    </td>
                    <!-- <td width="25%" colspan="2">
                      <small>Nomor Kontrak</small> <br> <?= $reqCode ?>
                    </td> -->
                    <td width="12%">
                      <small>Metode Pembayaran </small> <br>
                      <?php
                      if ($reqMetodePembayaran == '1') {
                         echo "Sekaligus";
                      } else { echo "Termin"; } ?>
                    </td>
                    <td width="12%">
                      <small>Jenis Pengadaan</small> <br> <?= $reqJenisPengadaanStr ?>
                    </td>
                    <td width="13%">
                      <small>Jenis Kontrak</small> <br> <?= $reqJenisKontrakStr ?>
                    </td>
                  </tr>
                  <tr>
                    <!-- <td width="25%" colspan="2">
                      <small>Jenis Pekerjaan</small> <br> <?= $reqJenisPekerjaanStr ?>
                    </td> -->
                  </tr>
                  <tr>
                    <td width="25%" colspan="4">
                      <small>Jangka Waktu Pelaksanaan </small> <br> <?= getFormattedDateShort2(dateTimeToPageCheck($reqWaktuPelaksanaanDari)).' s/d '.getFormattedDateShort2(dateTimeToPageCheck($reqWaktuPelaksanaanSampai)) ?>
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

            <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
              <div class="modal-dialog modal-lg" role="document">
                <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title">Dokumen Pendukung Pemilihan</h4>
                  </div>
                  <div class="modal-body">
                   <?= $this->libkontrak->getDokumenPendukung($reqPaketId,$reqRakananId) ?>
                   <br><br>
                  </div>
                  <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div> -->
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
