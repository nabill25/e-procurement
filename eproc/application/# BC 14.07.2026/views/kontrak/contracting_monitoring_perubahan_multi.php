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

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Contracting");
$this->load->model("Contractingrekanan");
$this->load->model("Paketpemenang");

$contracting = new Contracting();
$spkpks = new Contractingrekanan();
$legal = new Contractingrekanan();
$proses4 = new Contractingrekanan();
$textMonitoring = new Contractingrekanan();
$getpaket_pemenang = new Paketpemenang();
$getpaket_pemenang2 = new Paketpemenang();

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
$reqPerubahan = $proses4->getField('CR_PERUBAHAN') ?: '';
$reqPerubahanAlasan = $proses4->getField('CR_PERUBAHAN_ALASAN') ?: '';
$reqPerubahanUpdated = $proses4->getField('CR_PERUBAHAN_UPDATED_DATE') ? explode(' ',$proses4->getField('CR_PERUBAHAN_UPDATED_DATE')) : '';
$reqPerubahanUpdatedDate = $reqPerubahanUpdated[0];
$reqPerubahanUpdatedDate2 = $reqPerubahanUpdated[1];

$textMonitoring->selectText(array("A.TYPE" => 'Perubahan'));
$textMonitoring->firstRow();
$reqText = $textMonitoring->getField('KETERANGAN') ?: '';

$paketInfo->getPaket($reqPaketId);
$bidding = $paketInfo->bidding;
$reqMultiPemenang = $paketInfo->multi_pemenang;

// if ($reqMultiPemenang == '0') {
//   // echo "1 Pemanang";
//   exit;
// } else {
  // echo "Multi Pemanang";
  // $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1); 
  // $getpaket_pemenang2->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1); 
// }

if ($reqMultiPemenang == '0') {
  $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqPaketId, "PERINGKAT" => '1'), -1, -1); 
  $getpaket_pemenang2->selectByParams(array("PAKET_ID" => $reqPaketId, "PERINGKAT" => '1'), -1, -1);
} else {
  // echo "Multi Pemanang";
  $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1);
  $getpaket_pemenang2->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1);
}

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

$(document).ready(function() {
  $(function(){
    $('#ff').form({
      url:'contracting_json/addPerubahanKontrak',
      onSubmit:function(){
        var v=$(this).form('validate');
        if(v) showLoad();  // show the message box
        return v;
      },
      success:function(data){
        hideLoad();
        alertSuccess2(data);
      }
    });
  });
});

function delDelivery(aidi,m) {
  $.messager.confirm('Konfirmasi',m,function(r){
    if (r){
      $.getJSON("contracting_json/delDelivery/?reqAidi="+aidi,
      function(data){
        alertSuccess2(data.PESAN);
        setTimeout(function() {
          location.reload();
        }, 2000);
      });
    }
  });
}

function delPayment(aidi,m) {
  $.messager.confirm('Konfirmasi',m,function(r){
    if (r){
      $.getJSON("contracting_json/delPayment/?reqAidi="+aidi,
      function(data){
        alertSuccess2(data.PESAN);
        setTimeout(function() {
          location.reload();
        }, 2000);
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
          <h4 class="mb-2">Perubahan Kontrak</h4>
      <?php
      if ($reqPerubahanAlasan == '')
      { // Jika Ada Perubahan Kontrak
      ?>
          <div class="row mb-1">
            <div class="col-md-12">
              <p>
                <?= $reqText ?>
              </p>
              <?php
              if ($this->LEGAL != '1' && $this->USER_TYPE_ID != '20') { ?>
              <a class="<?= CLASS_BTN_DANGER ?>" onClick="openAdd('kontrak/loadUrl/kontrak/contracting_monitoring_perubahan_edit/?reqId=<?=$reqId?>');" style="color:#fff"> <span class="fa fa-angle-double-right" style="color:#fff"></span>  Lakukan Perubahan Kontrak ?</a>
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
                    <small>'.getFormattedDate($reqPerubahanUpdatedDate).' '.$reqPerubahanUpdatedDate2.'</small><br><b>Alasan Perubahan</b>: <i>'.$reqPerubahanAlasan.'</i>
                  </div>
                </div>
              </div>';
      ?>
          <div class="row mb-1">
            <div class="col-md-12">
              <?php
              // Peng. Kontrak bukan legal
              // if ($this->LEGAL != '1') {
              //   echo $this->libkontrak->getStatusKontrakTeruskan('7',$reqId,$this->USER_TYPE_ID,$this->LEGAL,'PELAKSANAAN');
              // }
              ?>
            </div>
          </div>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <h5>Dokumen Perubahan
                <?php
                if ($reqContractingStatusKontrakId >= '2') { // Penyedia sudah approve ?>
                <small style="font-size:0.7em">
                  <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_file_add_multi?reqAidi=<?= $reqId ?>&reqProses=4&reqJenis=Perubahan Kontrak')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Dokumen </a>
                </small>
                <?php
                } ?>
                </h5>
                <div class="table-responsive">
                  <table id="dokumenFileIdTable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                    <?= $this->libkontrak->getTableFileMulti($reqId," AND A.FILE_JENIS = 'Perubahan Kontrak' ") ?>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="form-actions">
            <div class="form-actions">
              <table class="table table-bordered table-hover">
                <tbody>
                  <tr>
                    <th width="2%">No</th>
                    <th>No. <?= $reqJnsKontrakStr ?></th>
                    <th>Penyedia</th>
                    <th width="22%">Aksi</th>
                  </tr>
                  <?php 
                  $no = 1;
                  while($getpaket_pemenang->nextRow())
                  { 
                    $contractingrekananSPPBJ = new Contractingrekanan();
                    $contractingrekananSPPBJ->selectViewPKSSPK(array("A.PAKET_ID" => $reqPaketId, "A.REKANAN_ID" => $getpaket_pemenang->getField("REKANAN_ID")));
                    $contractingrekananSPPBJ->firstRow(); 
                    ?>
                    <tr>
                      <td width="2%"><?= $no ?></td>
                      <td>
                        <?= $contractingrekananSPPBJ->getField("CR_LEGAL_NOMOR_PKS") ?: '-'; ?>
                      </td>
                      <td><?= $getpaket_pemenang->getField("NAMA").'-'.$contractingrekananSPPBJ->getField("CONTRACTING_STATUS_KONTRAK"); ?>
                      <?php if ($contractingrekananSPPBJ->getField("CONTRACTINGSTATUSKONTRAKID") > 2) { ?>
                      <br><small><i><?= $contractingrekananSPPBJ->getField("CR_STATUS_KONTRAK_STR"); ?></i></small>
                      <?php } ?>
                      </td>
                      <td>
                        <?php
                        if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20') 
                        { // Penyedia sudah approve & Peng. Kontrak bagian Legal tapi User Pengguna, pemeriksa ?>
                        <a href="kontrak/index/contracting_persiapan_kontrak_multi_edit?reqId=<?= $contractingrekananSPPBJ->getField("CONTRACTINGREKANANPROSES1ID") ?>&reqConRekId=<?= $contractingrekananSPPBJ->getField("CONTRACTINGREKANANID") ?>&back=contracting_monitoring_perubahan_multi&getProses=4" class="<?= CLASS_BTN_PRIMARY ?> mb-1 btn-sm"> <i class="fa fa-pencil"></i> </a>
                        <?php
                        }?> 
                          <button class="<?= CLASS_BTN_SUCCESS ?> mb-1 btn-sm" data-toggle="modal" data-target=".bs-example-modal-lg-<?= $getpaket_pemenang->getField("REKANAN_ID") ?>"><span class="fa fa-eye"></span> Dok. Pemilihan </button>

                      </td>
                    </tr>
                    <!--   -->
                  <?php 
                  $no++;
                  } ?>
                </tbody>
              </table>
              <?php 
              while($getpaket_pemenang2->nextRow())
              {  ?>
              <div class="modal fade bs-example-modal-lg-<?= $getpaket_pemenang2->getField("REKANAN_ID") ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                  <!-- Modal content-->
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title">Dokumen Pendukung Pemilihan</h4>
                    </div>
                    <div class="modal-body">
                     <?= $this->libkontrak->getDokumenPendukungMulti($reqPaketId,$getpaket_pemenang2->getField("REKANAN_ID")) ?>
                     <br><br>
                    </div>
                    <!-- <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div> -->
                  </div>
                </div>
              </div>
              <?php 
              } ?>
            </div>
          </div>

          <div class="form-actions mt-3">
            <h4>Daftar Barang Jasa
              <?php
              if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20') { // Penyedia sudah approve, pemeriksa ?>
              <small style="font-size:0.7em">
                <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_material?reqAidi=<?= $reqId ?>')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Barang Jasa </a>
              </small>
              <?php
              } ?>
            </h4>
            <div style="height:450px; overflow:scroll"> 
              <table id="deliveriable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                <tr class="backcolornew">
                  <th width="5px">No</th>
                  <th>Deskripsi</th>
                  <th width="10%">Vol/Qty</th>
                  <th width="10%">Satuan</th>
                  <th width="20%">Harga Satuan</th>
                </tr>
                <?php
                $this->load->model("Contractingmaterial");
                $datamaterial = new Contractingmaterial();
                $datamaterial->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
                if ($datamaterial->countRow() > 0) {
                  $no=1;
                  while($datamaterial->nextRow()) {
                    if ($no == '1') {
                      if ($datamaterial->getField('SIFAT') == '1') {
                        $sifat = ' Volume bersifat Berubah';
                      } else {
                        $sifat = ' Volume bersifat Tetap';
                      }
                    }
                  ?>
                  <tr>
                    <td width="10px"><?= $no; ?></td>
                    <td><?= $datamaterial->getField('NAMA') ?></td>
                    <td><?= $datamaterial->getField('QTY'); ?></td>
                    <td><?= $datamaterial->getField('SATUAN_STR'); ?></td>
                    <td><?= currencyToPage($datamaterial->getField('HARGA_SATUAN')) ?></td>
                  </tr>
                  <?php
                  $no++;
                  }
                } else { echo '<tr><td colspan="3">. : : Tidak ada data : : .</td></tr>';} ?>
              </table>
            <?php 
            if ($sifat) { ?>
            <span class="badge badge-dark" style="padding:0.6% 3%"><i><?= $sifat ?></i></span>
            <?php 
            } ?>
            </div>
          </div>

          <!-- <hr>
          <h4>Sanksi dan Denda Keterlambatan -->
            <?php
            // if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20') { // Penyedia sudah approve, pemeriksa ?>
            <!-- <small style="font-size:0.7em">
              <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_sanksi?reqAidi=<?php // $reqId ?>')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Sanksi/Denda Keterlambatan </a>
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
                <td><?php // $datasanksi->getField('NILAI_SANKSI') ?>/1000</td>
                <td><?php // currencyToPage($datasanksi->getField('NILAI_PEKERJAAN')) ?></td>
                <td><?php // $datasanksi->getField('HARI_TERLAMBAT') ?></td>
                <td><?php // currencyToPage($datasanksi->getField('NILAI_DENDA')) ?></td>
              </tr> -->
              <?php
            //   }
            // } else { echo '<tr><td colspan="6">. : : Tidak ada data : : .</td></tr>';} ?>
          <!-- </table>
          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1"> -->
              <?php
              // $this->load->model("Contractingsanksi");
              // $datasanksi = new Contractingsanksi();
              // $datasanksi->selectByParamsKetentuan(array("CONTRACTINGREKANANID"=>$reqId, "JENIS" => "1"));
              // if ($datasanksi->countRow() > 0) {
              //   while($datasanksi->nextRow()) {
                ?> <?php // $datasanksi->getField('KETERANGAN') ?>
                <?php
              //   }
              // } else { echo '. : : Tidak ada keterangan : : .';} ?>
              <!-- </div> -->
              <?php
              // if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20') { // Penyedia sudah approve, pemeriksa ?>
              <!-- <small style="margin: 10px;">
                <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_sanksi_ketentuan?reqAidi=<?php // $reqId ?>&reqJenis=1')" class="badge badge-info pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Sanksi/Denda Keterlambatan </a>
              </small> -->
              <?php
              // } ?>
            <!-- </div>
          </div> -->

          <!-- <hr>
          <h4>Sanksi dan Denda Kelalaian
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
                // ?> <?php // $datasanksi->getField('KETERANGAN') ?>
                <?php
              //   }
              // } else { echo '. : : Tidak ada keterangan : : .';} ?>
              </div>
              <?php
            // if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20') { // Penyedia sudah approve, pemeriksa ?>
            <small style="margin: 10px;">
              <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_sanksi_ketentuan?reqAidi=<?php // $reqId ?>&reqJenis=2')" class="badge badge-info pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Sanksi/Denda Kelalaian </a>
            </small>
            <?php
            // } ?>
            </div>
          </div> -->
      <?php
      } ?>
 

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
