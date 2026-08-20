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
$reqPO = $spkpks->getField('CR_PO') ?: '-';
$reqTglHasilTerimaPilihan = $spkpks->getField('CR_TGL_HASIL_TERIMA_PEMILIHAN') ?: '-';
$reqPenyelesaianAwal = $spkpks->getField('CR_PENYELESAIAN_KONTRAK_AWAL') ?: '-';
$reqPenyelesaianAkhir = $spkpks->getField('CR_PENYELESAIAN_KONTRAK_AKHIR') ?: '-';
$reqMasaGaransi = $spkpks->getField('CR_MASA_GARANSI') ?: '-';
$reqMasaGaransiPeriode = $spkpks->getField('CR_MASA_GARANSI_PERIODE') ?: '-';
$reqPICKontrak = $spkpks->getField('PIC_KONTRAK') ?: '-';
$reqPICPengendali = $spkpks->getField('PIC_PENGENDALI') ?: '-';
$reqPICPenyelesaian = $spkpks->getField('PIC_PENYELESAIAN') ?: '-';

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
$reqDenda = $proses4->getField('CR_DENDA') ?: '';
$reqDendaAlasan = $proses4->getField('CR_DENDA_ALASAN') ?: '';
$reqDendaFile = $proses4->getField('CR_DENDA_FILE') ?: '';
$reqDendaUpdated = $proses4->getField('CR_DENDA_UPDATED_DATE') ? explode(' ',$proses4->getField('CR_DENDA_UPDATED_DATE')) : '';
$reqDendaUpdatedDate = $reqDendaUpdated[0];
$reqDendaUpdatedDate2 = $reqDendaUpdated[1];

$textMonitoring->selectText(array("A.TYPE" => 'Denda'));
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

  function delSanksi(aidi,m) {
  $.messager.confirm('Konfirmasi',m,function(r){
    if (r){
      $.getJSON("contracting_json/delSanksi/?reqAidi="+aidi,
      function(data){
        alertSuccess2(data.PESAN);
        setTimeout(function() {
          location.reload();
        }, 2000);
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
          <h4 class="mb-2">Sanksi dan Denda</h4>
      <?php
      if ($reqDendaAlasan == '')
      { // Jika Ada Sanksi dan Denda
      ?>
          <div class="row mb-1">
            <div class="col-md-12"> 
              <?php
              if ($this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && $this->LEVEL_KONTRAK == '2') { ?>
              <a class="<?= CLASS_BTN_DANGER ?>" onClick="openAddFrame('kontrak/loadUrl/kontrak/contracting_monitoring_denda_edit/?reqId=<?=$reqId?>');" style="color:#fff"> <span class="fa fa-angle-double-right" style="color:#fff"></span>  Sanksi dan Denda ?</a>
              <?php
              } ?>
            </div>
          </div>
          
          <?= $this->libkontrak->getInfoKontrak($reqId); ?> 

      <?php
      } else
      {
        echo '<div class="card mb-1 border-blue border-darken-1" style="padding: 5px 10px 0 10px; background-color: #fff3f3">
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <small>'.getFormattedDate($reqDendaUpdatedDate).' '.$reqDendaUpdatedDate2.'</small><br><b>Alasan</b>: <i>'.$reqDendaAlasan.'</i><br>
                    <a href="uploads/kontrak/'.$reqDendaFile.'" target="_blank" class="badge badge-primary"><span class="fa fa-download"></span> Download File</a>
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

          <div class="form-actions">

            <?php
            if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20') { // Penyedia sudah approve & Peng. Kontrak bagian Legal tapi User Pengguna, pemeriksa ?>
            <!-- <a href="kontrak/index/contracting_persiapan_kontrak_edit?reqId=<?php // echo $reqId ?>&back=contracting_monitoring_denda" class="<?php // echo CLASS_BTN_PRIMARY ?> mr-1 mb-1 text-white"> <i class="fa fa-pencil"></i> Edit Data Kontrak </a> -->
            <?php
            } else { ?>
            <!-- <a href="kontrak/index/contracting_persiapan_kontrak_edit_legal?reqId=<?= $reqId ?>&back=contracting_monitoring_perubahan" class="btn btn-primary mr-1 mb-1 text-white"> <i class="fa fa-pencil"></i> Edit Data <?= $reqJenisKontrak ?> </a>  -->
            <?php
            } ?>

            <button class="<?= CLASS_BTN_SUCCESS ?> mb-1" data-toggle="modal" data-target=".bs-example-modal-lg"><span class="fa fa-eye"></span> Lihat Dokumen Pendukung Pemilihan </button>

             <?= $this->libkontrak->getInfoKontrak($reqId); ?> 
               
            <?php
            if ($reqJenisPekerjaan == '1') { // Hanya untuk pekerjaan TI ?>
            <hr>
            <h4>Service Level Agreement (SLA)
              <?php
              if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20') { // Penyedia sudah approve, pemeriksa ?>
              <small style="font-size:0.9em">
                <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_sla?reqAidi=<?= $reqId ?>')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah / Ubah SLA </a>
              </small>
              <?php
              } ?>
              </h4>
            <table id="tablesla" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
              <tr class="backcolornew">
                <th width="100px">Availability</th>
                <th>Waktu (jam)</th>
                <th>Denda Keterlambatan </th>
                <th>Biaya Maintanance</th>
                <th>Nilai Denda</th>
                <!-- <th width="100px">Status</th> -->
              </tr>
              <?php
              $this->load->model("Contractingsla");
              $datsla = new Contractingsla();
              $datsla->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
              if ($datsla->countRow() > 0) {
                while($datsla->nextRow()) {
                ?>
                <tr>
                  <td><?= $datsla->getField('SLA_AVAILABILITY').' %' ?></td>
                  <td><?= $datsla->getField('SLA_WAKTU') ?></td>
                  <td><?= $datsla->getField('SLA_DENDA').' % dari nilai biaya bulanan maintanance' ?></td>
                  <td><?= currencyToPage($datsla->getField('SLA_BIAYA_MAINTANANCE')) ?></td>
                  <td><?= currencyToPage($datsla->getField('SLA_NILAI_DENDA')) ?></td>
                  <!-- <td><?php //$datsla->getField('SLA_STATUS') ?></td>  -->
                </tr>
                <?php
                }
              } else { echo '<tr><td colspan="6">. : : Tidak ada data : : .</td></tr>';} ?>
            </table>
            <?php
            } ?>

            <hr>
            <h4>Denda Keterlambatan
              <?php
              if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && ($this->USER_TYPE_ID == '12' && $reqPICPenyelesaian == $this->USER_LOGIN_ID)) { // Penyedia sudah approve, pemeriksa ?>
              <small style="font-size:0.9em">
                <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_sanksi2?reqAidi=<?= $reqId ?>')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Denda Keterlambatan </a>
              </small>
              <?php
              } ?>
              </h4>
              <div class="table-responsive">
                <table id="tablesanksi" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                  <tr class="backcolornew">
                    <?php
                    if ($this->LEGAL != '1' && $this->USER_TYPE_ID != '20') { // update status oleh peng. kontrak bukan legal, pemeriksa ?>
                    <th width="60px">Aksi</th>
                    <?php
                    } ?>
                    <th class="text-center" width="280px">Tagihan</th>
                    <th class="text-center">Nilai <br>Sanksi /1000</th>
                    <th class="text-center">Nilai / <br>Bagian Pekerjaan </th>
                    <th class="text-center" width="10px">Hari <br>Keterlambatan</th>
                    <th class="text-center">Nilai Denda</th>
                    <th class="text-center">Cara Bayar</th>
                    <th class="text-center">Bukti Bayar</th>
                    <th class="text-center">Invoice</th>
                    <th class="text-center">Invoice TTD</th>
                  </tr>
                  <?php
                  $this->load->model("Contractingsanksi");
                  $datasanksi = new Contractingsanksi();
                  $datasanksi->selectByParams(array("A.CONTRACTINGREKANANID"=>$reqId));
                  if ($datasanksi->countRow() > 0) {
                    while($datasanksi->nextRow()) {
                    ?>
                    <tr>
                      <?php
                      if ($this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && ($this->USER_TYPE_ID == '12' && $reqPICPenyelesaian == $this->USER_LOGIN_ID)) { // update status oleh peng. kontrak bukan legal, pemeriksa ?>
                      <td class="text-center" style="padding:10px !important">
                        <a style="color: #fff;" onclick="openAddFrame('main/loadUrlKontrak/kontrak/contracting_add_sanksi_editform?reqAidi=<?= $datasanksi->getField('SANKSIID') ?>&reqConId=<?= $reqId ?>')" class="badge badge-info"><i class="fa fa-edit"></i></a>
                        <a style="color: #fff;" onClick="delSanksi('<?= $datasanksi->getField('SANKSIID') ?>','Hapus data ini?')" class="badge badge-danger"><i class="fa fa-trash"></i></a>
                      </td>
                      <?php
                      }?>
                      <td><?= $datasanksi->getField('PAY_TERMIN_KE') ?></td>
                      <td class="text-center"><?= $datasanksi->getField('NILAI_SANKSI') ?>/1000</td>
                      <td><?= currencyToPage($datasanksi->getField('NILAI_PEKERJAAN')) ?></td>
                      <td class="text-center"><?= $datasanksi->getField('HARI_TERLAMBAT') ?></td>
                      <td><?= currencyToPage($datasanksi->getField('NILAI_DENDA')) ?></td>
                      <td><?= $datasanksi->getField('CARA_BAYAR'); ?>
                      </td>
                      <td>
                        <?php  
                        if ($datasanksi->getField('BUKTI_BAYAR')) {
                          echo '<a href="uploads/payment/'.$datasanksi->getField('BUKTI_BAYAR').'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> Download</span></a>';
                        }
                        ?>
                      </td>
                      <td>
                        <?php  
                        if ($datasanksi->getField('INVOICE_FILE')) {
                          echo '<a href="uploads/payment/'.$datasanksi->getField('INVOICE_FILE').'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> Download</span></a>';
                        }
                        ?>
                      </td>
                      <td>
                        <?php  
                        if ($datasanksi->getField('INVOICE_FILE_TTD')) {
                          echo '<a href="uploads/payment/'.$datasanksi->getField('INVOICE_FILE_TTD').'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> Download</span></a>';
                        }
                        ?>
                      </td>
                    </tr>
                    <?php
                    }
                  } else { echo '<tr><td class="text-center" colspan="7">. : : Tidak ada data : : .</td></tr>';} ?>
                </table> 
              </div>

            <hr>

            <div class="card mb-1 border-blue border-darken-1">
              <div class="card-content">
                <div class="p-1">
                  <h5>Dokumen Sanksi
                  <?php
                  if ($reqContractingStatusKontrakId >= '2') { // Penyedia sudah approve 

                    if ($this->LEVEL_KONTRAK == '2') { ?>
                      <small style="font-size:0.9em">
                        <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_file_add?reqAidi=<?= $reqId ?>&reqProses=3&reqJenis=Sanksi')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Dokumen </a>
                      </small>
                    <?php 
                    } else { ?>
                      <small style="font-size:0.9em">
                        <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_file_add?reqAidi=<?= $reqId ?>&reqProses=4&reqJenis=Sanksi')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Dokumen </a>
                      </small>
                  <?php
                    }
                  } ?>
                  </h5>
                  <div class="table-responsive">
                    <table id="dokumenFileIdTable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                      <?= $this->libkontrak->getTableFile($reqId," AND A.FILE_JENIS = 'Sanksi' ") ?>
                    </table>
                  </div>
                </div>
              </div>
            </div>
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
