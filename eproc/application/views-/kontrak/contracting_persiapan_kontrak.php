<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$reqId = httpFilterRequest("reqId");
$getTahun = $this->session->userdata('setTahunKontrak');

$this->libsession->cekSession();
$this->libsession->cekSessionKontrakPPK($reqId);

$this->load->library("kauth");  $userLogin = new kauth();

$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;

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
// echo $reqContractingStatusKontrakId.'--';
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

?>

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/ui/jqueryui.css">
<script src="<?=base_url()?>assets/new/js/scripts/ui/jquery-ui/navigations.js"></script>
<style type="text/css">
 small { font-weight: bold; font-size: 11.5px }
 tr.backcolornew { background: #103A6C !important; color: #fff; }
 .blinking-element { animation: blink-smooth 1s infinite; }
 @keyframes blink-smooth { 0% {opacity: 1;} 50% {opacity: 0;} 100% {opacity: 1;} }
</style>

<script type="text/javascript">
function prosesKontrak(flow,aidi,m) {
  $.messager.confirm('Konfirmasi',m,function(r){
    if (r){
      $.getJSON("contracting_json/proseskontrak/?reqAidi="+aidi+"&flow="+flow,
      function(data){
        alertSuccess2(data.PESAN);
        setTimeout(function() {
          // if (data.FLOW == '6') {
            location.href = "kontrak/index/contracting_persiapan";
            // location.reload();
          // } else {
            // location.reload();
          // }
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

  function delDeliveryPayment(aidi,m) {
    $.messager.confirm('Konfirmasi',m,function(r){
      if (r){
        $.getJSON("contracting_json/delDeliveryPayment/?reqAidi="+aidi,
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

<?php 
// if ($this->LEGAL != '1' && $reqLegalNomorPKS != '-' && $this->USER_TYPE_ID != '20') {
 echo $this->libchat->kontrak($reqId);
//} ?>

<div class="row">
  <div class="col-md-3 col-sm-3">
    <div class="jqueryui-ele-container">
      <?= $this->libkontrak->getMenu($reqId); ?>
    </div>
  </div>
  <div class="col-md-9 col-sm-9">
    <div class="card">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <h4 class="mb-2">Data Kontrak <?= $reqJnsKontrakStr ?></h4>

          <div class="form-actions">
            <div class="row">
              <div class="col-md-12">
                <?php
                // Yang kirim ke penyedia bagian pengelola kontrak & Data PKS Sudah di isi, pemeriksa
                // if ($this->LEGAL != '1' && $reqLegalNomorPKS != '-' && $this->USER_TYPE_ID != '20') { 
                if ($this->LEGAL != '1'  && $this->USER_TYPE_ID != '20' && ($reqContractingStatusKontrakId == '2' || $reqContractingStatusKontrakId == '3' || $reqContractingStatusKontrakId == '113' || $reqContractingStatusKontrakId == '114')) {
                  echo $this->libkontrak->getStatusKontrakTeruskan($reqContractingStatusKontrakId,$reqRekananProses1Id,$this->USER_TYPE_ID,$this->LEGAL,'SPK-PKS');
                } else {
                  // echo $reqContractingStatusKontrakId.'---';
                  // if ($reqContractingStatusKontrakId == '4') { // informasi proses persetujuan penyedia untuk peng. kontrak
                  echo $this->libkontrak->getStatusKontrakTeruskan($reqContractingStatusKontrakId,$reqRekananProses1Id,$this->USER_TYPE_ID,$this->LEGAL,'SPK-PKS');
                  // }
                }
                ?>
              </div>
              <div class="col-md-12 text-right">
                <?php
                if (($reqContractingStatusKontrakId == '2' || $reqContractingStatusKontrakId == '3' || $reqContractingStatusKontrakId == '114' || $reqContractingStatusKontrakId == '51') && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && ($this->USER_TYPE_ID == '12' && $reqPICKontrak == $this->USER_LOGIN_ID)) { // Penyedia sudah approve & Peng. Kontrak bagian Legal tapi User Pengguna, pemeriksa 
                  ?>
                <a href="kontrak/index/contracting_persiapan_kontrak_edit?reqId=<?= $reqId ?>" class="<?= CLASS_BTN_PRIMARY ?> mb-1"> <i class="fa fa-pencil"></i> Edit Data Kontrak </a>
                <?php
                } else { ?>
                <!-- <a href="kontrak/index/contracting_persiapan_kontrak_edit_legal?reqId=<?= $reqId ?>" class="btn btn-primary mr-1 mb-1 text-white"> <i class="fa fa-pencil"></i> Edit Data <?= $reqJenisKontrak ?> </a>  -->
                <?php
                } ?>

                <button class="<?= CLASS_BTN_SUCCESS ?> mb-1" data-toggle="modal" data-target=".bs-example-modal-lg"><span class="fa fa-eye"></span> Lihat Dok. Pendukung Pemilihan </button>
                <button onClick="openAdd('main/loadUrl/main/data_rekanan_potensi?reqId=<?= $reqRakananId ?>&reqType=4');" class="<?= CLASS_BTN_DARK ?> mb-1" ><span class="fa fa-eye"></span> Lihat Data Penyedia </button>
              </div>
            </div>

            <?= $this->libkontrak->getInfoKontrak($reqId); ?> 

            <hr>
            <h4>Output Pekerjaan
              <?php
              if ($this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && $this->USER_TYPE_ID == '12' && $this->LEVEL_KONTRAK == '1' && ($reqContractingStatusKontrakId == '2' || $reqContractingStatusKontrakId == '3' || $reqContractingStatusKontrakId == '114') && ($this->USER_TYPE_ID == '12' && $reqPICKontrak == $this->USER_LOGIN_ID)) { // Penyedia sudah approve, pemeriksa ?>
              <small style="font-size:0.9em">
                <a onclick="openAddFrame('main/loadUrlKontrak/kontrak/contracting_add_deliverable_pembayaran?reqAidi=<?= $reqId ?>-<?= $reqMetodePembayaran ?>')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Output Pekerjaan </a>
                <!-- <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_deliverable?reqAidi=<?php // echo $reqId ?>-<?php // echo $reqMetodePembayaran ?>')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Deliverable Pekerjaan </a> -->
              </small>
              <?php
              } ?>
            </h4>
            <table id="deliveriable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
              <tr class="backcolornew">
                <th class="text-center">Pekerjaan</th>
                <th class="text-center">Keterangan</th>
                <th class="text-center" width="150px">Tanggal</th>
                <?php 
                if ($this->LEVEL_KONTRAK == '1' && ($reqContractingStatusKontrakId == '3' || $reqContractingStatusKontrakId == '114') && ($this->USER_TYPE_ID == '12' && $reqPICKontrak == $this->USER_LOGIN_ID)) { ?>
                <th class="text-center" width="50px">Aksi</th>
                <?php 
                } ?>
              </tr>
              <?php
              $this->load->model("Contractingdeliverable");
              $datadelivery = new Contractingdeliverable();
              $datadelivery->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
              if ($datadelivery->countRow() > 0) {
                while($datadelivery->nextRow()) {
                ?>
                <tr>
                  <td><?= $datadelivery->getField('DELIVERY_NAMA') ?></td>
                  <td><?= $datadelivery->getField('LINGKUP') ?></td>
                  <td class="text-center" width="100px">
                    <?= getFormattedDateShort2(dateTimeToPageCheck($datadelivery->getField('TANGGAL_DELIVERY_DARI'))) ?> <br>s/d<br>
                    <?= getFormattedDateShort2(dateTimeToPageCheck($datadelivery->getField('TANGGAL_DELIVERY_SAMPAI'))) ?>
                      
                  </td>
                  <!-- <td width="100px"><?php // echo $datadelivery->getField('STATUS') ?></td> -->
                  <?php 
                  if ($this->LEVEL_KONTRAK == '1' && ($reqContractingStatusKontrakId == '3' || $reqContractingStatusKontrakId == '114') && ($this->USER_TYPE_ID == '12' && $reqPICKontrak == $this->USER_LOGIN_ID)) { ?>
                    <td style="padding:5px !important" class="text-center">
                      <a style="color: #fff;" onclick="openAddFrame('main/loadUrlKontrak/kontrak/contracting_add_deliverable_pembayaran?reqAidi=<?= $reqId ?>-<?= $reqMetodePembayaran ?>&reqDeliverableId=<?= $datadelivery->getField('DELIVERABLEID') ?>')" class="badge badge-info"><i class="fa fa-edit"></i></a>
                      <a style="color: #fff;" onClick="delDeliveryPayment('<?= $datadelivery->getField('DELIVERABLEID') ?>','Hapus data <?= $datadelivery->getField('DELIVERY_NAMA') ?>?')" class="badge badge-danger"><i class="fa fa-trash"></i></a>
                    </td>
                  <?php 
                  } ?>
                </tr>
                <?php
                }
              } else { echo '<tr><td colspan="3">. : : Tidak ada data : : .</td></tr>';} ?>
            </table>

            <hr>
            <h4>Penagihan
              <?php
              if ($this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && $this->USER_TYPE_ID == '12') { // Penyedia sudah approve, pemeriksa ?>
              <!-- <small style="font-size:0.9em">
                <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_payment?reqAidi=<?php // echo $reqId ?>-<?php //echo $reqMetodePembayaran ?>')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Penagihan </a>
              </small> -->
              <?php
              } ?>
            </h4>
            <table id="tabletermin" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
              <tr class="backcolornew">
                <?php // if ($reqMetodePembayaran != 1) { ?>
                <th class="text-center">Penagihan</th>
                <!-- <th>Keterangan</th> -->
                <?php
                // } else { ?>
                <!-- <th>Keterangan</th> -->
                <?php
                // } ?>
                <th class="text-center" width="80px">Nilai Pembayaran</th>
                <th class="text-center" width="100px">Persentase</th>
                <th class="text-center" width="250px">Tanggal Selesai Administrasi  </th>
                <?php 
                if ($this->LEVEL_KONTRAK == '1' && ($reqContractingStatusKontrakId == '3' || $reqContractingStatusKontrakId == '114') && ($this->USER_TYPE_ID == '12' && $reqPICKontrak == $this->USER_LOGIN_ID)) { ?>
                <th class="text-center" width="50px">Aksi</th>
                <?php 
                } ?>
                <!-- <th>Berita Acara</th> -->
              </tr>
              <?php
              $this->load->model("Contractingpayment");
              $datapayment = new Contractingpayment();
              $datapayment->selectByParams(array("A.CONTRACTINGREKANANID"=>$reqId));
              if ($datapayment->countRow() > 0) {
                while($datapayment->nextRow()) {
                  $totalPay += $datapayment->getField('PAY_NILAI');
                  $totalProgress += $datapayment->getField('PAY_PROGRES');
                ?>
                <tr>
                  <?php // if ($reqMetodePembayaran != 1) { ?>
                  <td><?= $datapayment->getField('PAY_TERMIN_KE') ?></td>
                  <?php
                  // } ?>
                  <!-- <td><?php // echo $datapayment->getField('PAY_KETERANGAN') ?></td> -->
                  <td><?= currencyToPage($datapayment->getField('PAY_NILAI')) ?></td>
                  <!-- <td><?php // $datapayment->getField('PAY_LAMPIRAN') ?></td>  -->
                  <td class="text-center"><?= $datapayment->getField('PAY_PROGRES') ?> %</td>
                  <td class="text-center" width="100px">
                    <?= getFormattedDateShort2(dateTimeToPageCheck($datapayment->getField('PAY_DATE_DARI'))) ?> s/d
                    <?= getFormattedDateShort2(dateTimeToPageCheck($datapayment->getField('PAY_DATE_SAMPAI'))) ?>
                      
                  </td>
                  <?php 
                  if ($this->LEVEL_KONTRAK == '1'  && ($reqContractingStatusKontrakId == '3' || $reqContractingStatusKontrakId == '114') && ($this->USER_TYPE_ID == '12' && $reqPICKontrak == $this->USER_LOGIN_ID)) { ?>
                    <td style="padding:5px !important" class="text-center">
                      <a style="color: #fff;" onclick="openAddFrame('main/loadUrlKontrak/kontrak/contracting_add_deliverable_pembayaran?reqAidi=<?= $reqId ?>-<?= $reqMetodePembayaran ?>&reqDeliverableId=<?= $datapayment->getField('DELIVERABLEID_FK') ?>')" class="badge badge-info"><i class="fa fa-edit"></i></a>
                      <a style="color: #fff;" onClick="delDeliveryPayment('<?= $datapayment->getField('DELIVERABLEID_FK') ?>','Hapus data <?= $datapayment->getField('PAY_TERMIN_KE') ?>?')" class="badge badge-danger"><i class="fa fa-trash"></i></a>
                    </td>
                  <?php 
                  } ?>
                </tr>
                <?php
                }
              } else { echo '<tr><td colspan="6">. : : Tidak ada data : : .</td></tr>';} ?>
              <tfoot>
                <tr style="background-color:#b7b7b7; font-weight: bold;">
                  <?php // if ($reqMetodePembayaran != 1) { ?>
                  <td class="text-center">TOTAL</td>
                  <?php  //} else { ?>
                  <!-- <td>TOTAL</td> -->
                  <?php // } ?>
                  <td><?= currencyToPage($totalPay) ?></td>
                  <?php 
                  if ($totalProgress > 100) {
                    echo '<td class="text-center blinking-element" style="background:red; color:#fff">'.$totalProgress.' %</td>';  
                  } else {
                    echo '<td class="text-center">'.$totalProgress.' %</td>';  
                  }
                  ?>    
                  <td colspan="2"></td>
                </tr>
              </tfoot>
            </table>

            <?php
            if ($reqJenisPekerjaan == '1') { // Hanya untuk pekerjaan TI ?>
            <hr>
            <h4>Service Level Agreement (SLA)
              <?php
              if ($reqContractingStatusKontrakId == '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && $this->USER_TYPE_ID == '12') { // Penyedia sudah approve, pemeriksa ?>
              <small style="font-size:0.9em">
                <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_sla?reqAidi=<?= $reqId ?>')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah SLA </a>
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

            <div class="card mb-1 border-blue border-darken-1">
              <div class="card-content">
                <div class="p-1">
                  <h5>Dokumen <?= $reqJnsKontrakStr ?>
                  <?php
                  // if ($reqContractingStatusKontrakId == '2' || $reqContractingStatusKontrakId == '3') { // Penyedia sudah approve ?>
                  <small style="font-size:0.9em">
                    <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_file_add?reqAidi=<?= $reqId ?>&reqProses=1&reqJenis=Dokumen Kontrak')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Dokumen Kontrak </a>
                  </small>
                  <?php
                  // } ?>
                  </h5>
                  <div class="table-responsive">
                    <table id="dokumenFileIdTable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                      <?= $this->libkontrak->getTableFile($reqId," AND A.FILE_JENIS = 'Dokumen Kontrak' ") ?>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <!-- <hr> -->
            <!-- <h4>Sanksi dan Denda Keterlambatan -->
              <?php
              // if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20') { // Penyedia sudah approve, pemeriksa ?>
              <!-- <small style="font-size:0.7em">
                <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_sanksi?reqAidi=<?= $reqId ?>')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Sanksi/Denda Keterlambatan </a>
              </small> -->
              <?php
             // } ?>
              <!-- </h4> -->
            <!-- <table id="tablesanksi" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
              <tr class="backcolornew">
                <th>Nilai Sanksi</th>
                <th>Nilai / Bagian Pekerjaan </th>
                <th width="100px">Hari Keterlambatan</th>
                <th>Nilai Denda</th>
              </tr> -->
              <?php
              // $this->load->model("Contractingsanksi");
              // $datasanksi = new Contractingsanksi();
              // $datasanksi->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
              // if ($datasanksi->countRow() > 0) {
              //   while($datasanksi->nextRow()) {
                ?>
                <!-- <tr>
                  <td><?php // echo $datasanksi->getField('NILAI_SANKSI') ?>/1000</td>
                  <td><?php // echo currencyToPage($datasanksi->getField('NILAI_PEKERJAAN')) ?></td>
                  <td><?php // echo $datasanksi->getField('HARI_TERLAMBAT') ?></td>
                  <td><?php // echo currencyToPage($datasanksi->getField('NILAI_DENDA')) ?></td>
                </tr> -->
                <?php
              //   }
              // } else { echo '<tr><td colspan="6">. : : Tidak ada data : : .</td></tr>';} ?>
            <!-- </table> -->
            <!-- <div class="card mb-1 border-blue border-darken-1">
              <div class="card-content">
                <div class="p-1">
                <?php
                // $this->load->model("Contractingsanksi");
                // $datasanksi = new Contractingsanksi();
                // $datasanksi->selectByParamsKetentuan(array("CONTRACTINGREKANANID"=>$reqId, "JENIS" => "1"));
                // if ($datasanksi->countRow() > 0) {
                //   while($datasanksi->nextRow()) {
                //   ?> <?php // echo  $datasanksi->getField('KETERANGAN') ?>
                //   <?php
                //   }
                // } else { echo '. : : Tidak ada keterangan : : .';} ?>
                </div>
                <?php
                // if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20') { // Penyedia sudah approve, pemeriksa ?>
                <small style="margin: 10px;">
                  <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_sanksi_ketentuan?reqAidi=<?php // echo $reqId ?>&reqJenis=1')" class="badge badge-info pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Sanksi/Denda Keterlambatan </a>
                </small>
                <?php
                // } ?>
              </div>
            </div> -->

            <!-- <hr> -->
           <!--  <h4>Sanksi dan Denda Kelalaian
            </h4>
            <div class="card mb-1 border-blue border-darken-1">
              <div class="card-content">
                <div class="p-1">
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
                </div>
                <?php
              // if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20') { // Penyedia sudah approve, pemeriksa ?>
              <small style="margin: 10px;">
                <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_sanksi_ketentuan?reqAidi=<?php // echo $reqId ?>&reqJenis=2')" class="badge badge-info pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Sanksi/Denda Kelalaian </a>
              </small>
              <?php
              // } ?>
              </div>
            </div> -->

            <!-- <a href="kontrak/index/contracting_detail?reqId=<?= $reqId; ?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?></a> -->
            <?php
            if ($reqContractingStatusKontrakId == '2') { // Penyedia sudah approve 
              if ($this->LEGAL != '1' && $reqLegalNomorPKS != '-' && $this->USER_TYPE_ID != '20') {
            ?>
            <a href="main/loadUrl/report/kontrak_pdf/?reqId=<?=$reqId?>&reqPaketId=<?= $reqPaketId ?>" target="_blank" class="<?= CLASS_BTN_INFO ?> mr-1"> <?= BTN_PRINT ?> Data Kontrak <?php // $reqJnsKontrakStr ?></a>
            <?php 
              }
            } ?>

            <!-- <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_file_add?reqAidi=<?php // $reqId ?>&reqProses=1&reqJenis=Dokumen Kontrak')" class="btn btn-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Dokumen Kontrak </a>  -->

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
                  <div class="modal-footer">
                      <a href="main/loadUrl/report/paket_cetak_pdf/?reqId=<?=$reqPaketId?>" target="_blank" class="list-group-item"> <span class="ft-arrow-right"></span>Cetak Laporan Paket </a>

                    <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
