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
$reqPerubahan = $proses4->getField('CR_PERUBAHAN') ?: '';
$reqPerubahanAlasan = $proses4->getField('CR_PERUBAHAN_ALASAN') ?: '';
$reqPerubahanFile = $proses4->getField('CR_PERUBAHAN_FILE') ?: '';
$reqPerubahanUpdated = $proses4->getField('CR_PERUBAHAN_UPDATED_DATE') ? explode(' ',$proses4->getField('CR_PERUBAHAN_UPDATED_DATE')) : '';
$reqPerubahanUpdatedDate = $reqPerubahanUpdated[0];
$reqPerubahanUpdatedDate2 = $reqPerubahanUpdated[1];

$textMonitoring->selectText(array("A.TYPE" => 'Perubahan'));
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
  small { font-weight: bold; font-size: 11.5px }
  sup { font-style: italic; color: red;}
  tr.backcolornew {
    background: #103A6C !important;
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

  function approvalAddendum(delele_link, id, stat)
  {
    if (stat == '1') {
      var messa = 'Setujui Addendum ini?';
    } else {
      var messa = 'Batal setujui Addendum ini ?';
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

  function approvalAddendumClose(delele_link, id, stat)
  {
    if (stat == 'Proses') {
      var messa = 'Batal Addendum ini ?';
    } else {
      var messa = 'Selesai Addendum ini?';
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

function delAddendum(aidi,m) {
  $.messager.confirm('Konfirmasi',m,function(r){
    if (r){
      $.getJSON("contracting_json/delAddendum/?reqAidi="+aidi,
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

<?= $this->libchat->kontrak($reqId); ?>

<style type="text/css">
.blinking-element {
  animation: blink-smooth 1s infinite;
}
@keyframes blink-smooth {
  0% {opacity: 1;}
  50% {opacity: 0;}
  100% {opacity: 1;}
}
</style>

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
                    <?php
                    if ($this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && $this->LEVEL_KONTRAK == '2') { ?>
                    <a class="<?= CLASS_BTN_DANGER ?> mb-1" onClick="openAddFrame('kontrak/loadUrl/kontrak/contracting_monitoring_perubahan_edit/?reqId=<?=$reqId?>');" style="color:#fff"> <span class="fa fa-angle-double-right" style="color:#fff"></span>  Lakukan Perubahan Kontrak ?</a>
                    <?php
                    } else {
                      echo '<div class="alert alert-info text-center">. : : Tidak ada perubahan : : .</div>';
                    } ?>
                  </div>
                </div>
            <?= $this->libkontrak->getInfoKontrak($reqId); ?> 
            <?php
            } else
            {
              // echo '<div class="card mb-1 border-blue border-darken-1" style="padding: 5px 10px 0 10px; background-color: #fff3f3">
              //         <div class="row">
              //           <div class="form-group col-md-12 mb-2">
              //             <small>'.getFormattedDate($reqPerubahanUpdatedDate).' '.$reqPerubahanUpdatedDate2.'</small><br><b>Alasan Perubahan</b>: <i>'.$reqPerubahanAlasan.'</i><br>
              //             <a href="uploads/kontrak/'.$reqPerubahanFile.'" target="_blank" class="badge badge-primary"><span class="fa fa-download"></span> Download File</a>
              //           </div>
              //         </div>
              //       </div>';
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

                <div class="form-actions">

                  <h4>Addendum
                    <?php
                    if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && ($this->LEVEL_KONTRAK == '2') && ($this->USER_TYPE_ID == '12' && $reqPICPengendali == $this->USER_LOGIN_ID)) { // Penyedia sudah approve, pemeriksa ?>
                      <small style="font-size:0.9em">
                        <a onclick="openAddFrame('main/loadUrlKontrak/kontrak/contracting_add_addendum?reqAidi=<?= $reqPaketId ?>&reqConRekId=<?= $reqId ?>')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Addendum </a>
                      </small>
                    <?php
                    } ?>
                  </h4>
                  <div class="table-responsive">
                    <table id="addendum" class="border-double table mb-0 table-bordered mb-2">
                      <tr class="backcolornew">
                        <?php
                        if ($this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && ($this->LEVEL_KONTRAK == '1' || $this->LEVEL_KONTRAK == '2') && (($this->USER_TYPE_ID == '12' && $reqPICPengendali == $this->USER_LOGIN_ID) OR ($this->USER_TYPE_ID == '12' && $reqPICKontrak == $this->USER_LOGIN_ID))) { // update status oleh peng. kontrak bukan legal, pemeriksa ?>
                        <th class="text-center" width="40px">Aksi</th>
                        <?php
                        } else { echo '<th class="text-center" width="40px">Aksi</th>'; } ?>
                        <th class="text-center">Status</th>
                        <th class="text-center">Approval <br>Kasubdit</th>
                        <th class="text-center">Approval <br>Penyedia</th>
                        <th class="text-center">No. Addendum</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-center">Dok. <br>Addendum</th>
                        <th class="text-center" width="10px">Addendum <br>Ke</th>
                        <th class="text-center">Dok. <br>Persetujuan</th>
                        <th class="text-center">Jenis</th>
                        <th class="text-center">Masa Berlaku <br> Kontrak Addendum</th>
                        <th class="text-center">Tanggal Penyelesaian <br> Administrasi Penagihan</th>
                        <th class="text-center">Nilai</th>
                        <th class="text-center">Keterangan</th>
                      </tr>
                      <?php
                      $this->load->model("Contractingaddendum");
                      $addendum = new Contractingaddendum();

                      if ($this->LEVEL_KONTRAK == '1' || $this->USER_TYPE_ID == '6') { // Staff
                        $addendum->selectByParams(array("CONTRACTINGREKANANID"=>$reqId, "APPROVED_KASUBDIT" => "1"));
                      } else {
                        $addendum->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
                      }
                      if ($addendum->countRow() > 0) {
                        while($addendum->nextRow()) {
                        ?>
                        <tr>
                          <?php
                          if ($this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && ($this->LEVEL_KONTRAK == '1' || $this->LEVEL_KONTRAK == '2') && $addendum->getField('STATUS') == 'Proses' && (($this->USER_TYPE_ID == '12' && $reqPICPengendali == $this->USER_LOGIN_ID) OR ($this->USER_TYPE_ID == '12' && $reqPICKontrak == $this->USER_LOGIN_ID))) { // update status oleh peng. kontrak bukan legal, pemeriksa ?>
                            <td class="text-center" style="padding:10px !important">
                              <a style="color: #fff;" onclick="openAddFrame('main/loadUrlKontrak/kontrak/contracting_add_addendum?reqAddendumId=<?= $addendum->getField('CONTRACTING_ADDENDUM_ID') ?>&reqConRekId=<?= $reqId ?>')" class="badge badge-info"><i class="fa fa-edit"></i></a>
                              <a style="color: #fff;" onClick="delAddendum('<?= $addendum->getField('CONTRACTING_ADDENDUM_ID') ?>','Hapus data ini?')" class="badge badge-danger"><i class="fa fa-trash"></i></a>
                            </td>
                          <?php
                          } else { echo '<td>-</td>'; } ?>
                          <td class="text-center"> 
                            <?php 
                            if ($this->LEVEL_KONTRAK == '1' && $reqPICKontrak == $this->USER_LOGIN_ID) 
                            {
                              if (trim($addendum->getField('STATUS')) == 'Proses') {
                                    $blink = '';
                                  if ($addendum->getField('APPROVED_KASUBDIT') == '1' && $addendum->getField('APPROVED_PENYEDIA') == '1') {
                                    echo '<a onClick="approvalAddendumClose(\'contracting_json/approvalAddendumClose/\', '.$addendum->getField("CONTRACTING_ADDENDUM_ID").',\'Selesai\')" class="btn-xs btn-danger" style="padding:5px; border-radius:4px; color:#fff"><small class="blinking-element">Selesai?</small></a>';
                                  } else {
                                    echo "-";
                                  }
                                } else {
                                  echo '<a onClick="approvalAddendumClose(\'contracting_json/approvalAddendumClose/\', '.$addendum->getField("CONTRACTING_ADDENDUM_ID").',\'Proses\')" class="btn-xs btn-primary" style="padding:5px; border-radius:4px; color:#fff"><small>'.$addendum->getField('STATUS').'</small></a>';
                                } 
                            } else {
                              if ($addendum->getField('STATUS') == 'Selesai') {
                                echo '<span class="fa fa-check btn-xs btn-primary" style="padding:5px; border-radius:4px; color:#fff"><small>Selesai</small></span>';
                              } else {
                                echo '<span class="btn-xs btn-danger" style="padding:5px; border-radius:4px; color:#fff"> <small>Proses</small></span>';
                              }
                            }
                            ?>
                          </td>
                           <!-- Approval Kasubdit -->
                          <td class="text-center" <?php if ($this->USER_TYPE_ID == '20' && $addendum->getField('APPROVED_KASUBDIT') != '1') { echo 'style="background-color:#F7CA18"'; } ?>>
                            <?php 
                            if ($this->USER_TYPE_ID == '20') { // KA SUBDIT
                              if ($addendum->getField('APPROVED_KASUBDIT') == '1') {
                                echo '<a onClick="approvalAddendum(\'contracting_json/approvalAddendum/\', '.$addendum->getField("CONTRACTING_ADDENDUM_ID").',\'0\')" class="fa fa-check btn-xs btn-primary" style="padding:5px; border-radius:4px; color:#fff"><small>Disetujui</small></a>';
                              } else {
                                echo '<a onClick="approvalAddendum(\'contracting_json/approvalAddendum/\', '.$addendum->getField("CONTRACTING_ADDENDUM_ID").',\'1\')" class="btn-xs btn-danger" style="padding:5px; border-radius:4px; color:#fff"> <small class="blinking-element">Setujui?</small></a>';
                              }
                            } else {
                              if ($addendum->getField('APPROVED_KASUBDIT') == '1') {
                                echo '<span class="fa fa-check btn-xs btn-primary" style="padding:2px; border-radius:4px; color:#fff"> <small>Disetujui</small></span>';
                              } else {
                                echo '<span class="fa fa-times btn-xs btn-danger" style="padding:2px; border-radius:4px; color:#fff"> <small>Belum</small></span>';
                              }
                            }
                            ?>
                          </td>  
                          <td class="text-center"> 
                          <!-- Approval Penyedia --> 
                            <?php  
                              if ($addendum->getField('APPROVED_PENYEDIA') == '1') {
                                echo '<span class="fa fa-check btn-xs btn-primary" style="padding:2px; border-radius:4px; color:#fff"> <small>Disetujui</small></span>';
                              } else {
                                echo '<span class="fa fa-times btn-xs btn-danger" style="padding:2px; border-radius:4px; color:#fff"> <small>Belum</small></span>';
                              }
                            ?>
                          </td> 
                          <td><?= $addendum->getField('NOMOR') ?></td>
                          <td><?= getFormattedDateShort2(dateTimeToPageCheck($addendum->getField('TANGGAL'))) ?></td> 
                          <td class="text-center">
                            <?php 
                            if ($addendum->getField('ADDENDUM_FILE')) {
                              echo '<a href="uploads/kontrak/'.$addendum->getField('ADDENDUM_FILE').'" class="badge badge-primary" target="_blank" style="margin-bottom:5px"><i class="fa fa-download"></i> Download</a><br>';
                              if ($addendum->getField('ADDENDUM_FILE_PENYEDIA')) {
                              echo '<a href="uploads/kontrak/'.$addendum->getField('ADDENDUM_FILE_PENYEDIA').'" class="badge badge-success" target="_blank"><i class="fa fa-download"></i> TTD Penyedia</a>';
                              }
                            } else {
                              echo '-';
                            }
                             ?>
                          </td> 
                          <td class="text-center"><?= $addendum->getField('ADDENDUM_KE') ?></td> 
                          <td>
                            <?php 
                            if ($addendum->getField('ADDENDUM_FILE_PERSETUJUAN')) {
                              echo '<a href="uploads/kontrak/'.$addendum->getField('ADDENDUM_FILE_PERSETUJUAN').'" class="badge badge-primary" target="_blank"><i class="fa fa-download"></i> Download</a>';
                            } else {
                              echo '-';
                            }
                             ?>
                          </td> 
                          <td><?= $addendum->getField('JENIS') ?></td> 
                          <td class="text-center">
                            <?php  
                            if ($addendum->getField('TANGGAL_KONTRAK_DARI')) {
                              echo getFormattedDateShort2(dateTimeToPageCheck($addendum->getField('TANGGAL_KONTRAK_DARI'))).'<br> sd <br>'.getFormattedDateShort2(dateTimeToPageCheck($addendum->getField('TANGGAL_KONTRAK_SAMPAI')));
                            } else { echo "-";} ?>
                          </td> 
                          <td class="text-center">
                            <?php  
                            if ($addendum->getField('TANGGAL_PENYELESAIAN_KONTRAK_AWAL')) {
                              echo getFormattedDateShort2(dateTimeToPageCheck($addendum->getField('TANGGAL_PENYELESAIAN_KONTRAK_AWAL'))).'<br> sd <br>'.getFormattedDateShort2(dateTimeToPageCheck($addendum->getField('TANGGAL_PENYELESAIAN_KONTRAK_AKHIR')));
                            } else { echo "-";} ?>
                          </td> 
                          <td><?= currencyToPage($addendum->getField('ADDENDUM_NILAI')) ?></td> 
                          <td><?= $addendum->getField('KETERANGAN') ?></td> 
                        </tr>
                        <?php
                        }
                      } else { echo '<tr><td colspan="7" class="text-center">. : : Tidak ada data : : .</td></tr>';} ?>
                    </table>
                  </div>
                  <hr>

                  <?php
                  if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && $this->LEVEL_KONTRAK == '1') { // Penyedia sudah approve & Peng. Kontrak bagian Legal tapi User Pengguna, pemeriksa kontrak ?>
                  <a href="kontrak/index/contracting_persiapan_kontrak_edit?reqId=<?= $reqId ?>&back=contracting_monitoring_perubahan" class="<?= CLASS_BTN_PRIMARY ?> mr-1 mb-1 text-white"> <i class="fa fa-pencil"></i> Edit Data Kontrak </a>
                  <?php
                  } else { ?>
                  <!-- <a href="kontrak/index/contracting_persiapan_kontrak_edit_legal?reqId=<?= $reqId ?>&back=contracting_monitoring_perubahan" class="btn btn-primary mr-1 mb-1 text-white"> <i class="fa fa-pencil"></i> Edit Data <?= $reqJenisKontrak ?> </a>  -->
                  <?php
                  } ?>

                  <button class="<?= CLASS_BTN_SUCCESS ?> mb-1" data-toggle="modal" data-target=".bs-example-modal-lg"><span class="fa fa-eye"></span> Lihat Dokumen Pendukung Pemilihan </button>

                    <?= $this->libkontrak->getInfoKontrak($reqId); ?> 

                  <hr>

                  <h4>Realisasi Pekerjaan
                    <?php
                    if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && $this->LEVEL_KONTRAK == '1') { // Penyedia sudah approve, pemeriksa ?>
                    <small style="font-size:0.9em">
                      <a onclick="openAddFrame('main/loadUrlKontrak/kontrak/contracting_add_deliverable_pembayaran?reqAidi=<?= $reqId ?>')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Output Pekerjaan </a>
                    </small>
                    <?php
                    } ?>
                  </h4>
                  <div class="table-responsive">
                    <table id="deliveriable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                      <tr class="backcolornew">
                        <th>Realisasi</th>
                        <th>Keterangan</th>
                        <th width="20%" class="text-center">Tanggal Realisasi</th>
                        <th width="20%">Tanggal</th> 
                        <th width="50px">Catatan</th>
                        <th width="50px">Status</th>
                        <?php
                        if ($this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && $this->LEVEL_KONTRAK == '1') { // update status oleh peng. kontrak bukan legal, pemeriksa ?>
                        <th width="50px">Aksi</th>
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
                          <td class="text-center">
                            <?= getFormattedDateShort($datadelivery->getField('TANGGAL_DELIVERY_DARI')); ?> <br> s/d <br>
                            <?= getFormattedDateShort($datadelivery->getField('TANGGAL_DELIVERY_SAMPAI')); ?>
                          </td>
                          <td>
                            <small>Aktual Pekerjaan: <?= $datadelivery->getField('TANGGAL') ? getFormattedDateShort($datadelivery->getField('TANGGAL')) : '-'; ?> <br>
                            Laporan Selesai: <?= $datadelivery->getField('TANGGAL_TERIMA') ? getFormattedDateShort($datadelivery->getField('TANGGAL_TERIMA')) : '-'; ?></td>
                          <td>
                            <?php
                              if ($datadelivery->getField('FILE_BAPP')) {
                                 echo '<a href="uploads/kontrak/'.$datadelivery->getField('FILE_BAPP').'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> BAPP</span></a>';
                                
                                if ($datadelivery->getField('FILE_NAMA')) {
                                   echo '<a href="uploads/kontrak/'.$datadelivery->getField('FILE_NAMA').'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> File</span></a><br>';
                                   echo $datadelivery->getField('KETERANGAN');
                                 } else {
                                   echo $datadelivery->getField('KETERANGAN');
                                 }
                               }
                            ?>
                          </td>
                          <td width="100px">
                            <?php
                            if(trim($datadelivery->getField('STATUS')) == 'Proses') {
                              echo '<span class="badge badge-danger">'.$datadelivery->getField('STATUS').'</span>';
                            } else {
                              echo '<span class="badge badge-primary">'.$datadelivery->getField('STATUS').'</span>';
                            }
                            ?>
                          </td>
                          <?php
                          if ($this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && $this->LEVEL_KONTRAK == '1' && trim($datadelivery->getField('STATUS')) == 'Proses') { // update status oleh peng. kontrak bukan legal, pemeriksa ?>
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
                  </div>
                  <hr>
                  <h4>Tagihan
                    <?php
                    if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && $this->LEVEL_KONTRAK == '1') { // Penyedia sudah approve, pemeriksa  ?>
                    <small style="font-size:0.9em">
                      <!-- <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_payment2?reqAidi=<?php // echo $reqId ?>-<?php // echo $reqMetodePembayaran ?>')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Tagihan </a> -->
                    </small>
                    <?php
                    } ?>
                  </h4>

                  <div class="table-responsive">
                    <table id="tabletermin" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                      <tr class="backcolornew">
                        <th>Nomor Receipt</th>
                        <th width="150px">Tanggal</th>
                        <th width="150px">Tanggal Selesai Administrasi</th>
                        <?php // if ($reqMetodePembayaran == 2) { ?>
                        <th class="text-center">Tagihan</th>
                        <?php
                        // } ?>
                        <th>Nilai Pembayaran</th>
                        <th>Nilai Potongan</th>
                        <th width="80px">Persentase</th>
                        <th width="50px" class="text-center">Dokumen</th>
                        <!-- <th width="50px">BA</th> -->
                        <th width="100px">Status</th> 
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
                          <td><?= $datapayment->getField('PAY_NOMOR') ?></td>
                          <td><?= getFormattedDateShort($datapayment->getField('PAY_DATE')) ?></td>
                          <td><?= getFormattedDateShort($datapayment->getField('PAY_DATE_DARI')).' sd '.getFormattedDateShort($datapayment->getField('PAY_DATE_SAMPAI')) ?></td>
                          <?php // if ($reqMetodePembayaran == 2) { ?>
                          <td class="text-center"><?= $datapayment->getField('PAY_TERMIN_KE') ?></td>
                          <?php
                          // } ?>
                          <td><?= currencyToPage($datapayment->getField('PAY_NILAI')) ?></td>
                          <td><?= currencyToPage($datapayment->getField('PAY_POTONGAN')) ?></td>
                          <td class="text-center"> <?= $datapayment->getField('PAY_PROGRES') ?> %</td>
                          <td class="text-center">
                          <?php
                            if (file_exists('uploads/payment/'.$datapayment->getField('PAY_LAMPIRAN')) && $datapayment->getField('PAY_LAMPIRAN') != '' ) {
                              echo '<a href="uploads/payment/'.$datapayment->getField('PAY_LAMPIRAN').'" target="_blank" class="badge badge-primary" style="margin-top:3%"><span class="fa fa-download"> BAST</span></a>';
                            } else {
                              // echo '-';
                            }

                            if (file_exists('uploads/payment/'.$datapayment->getField('PAY_LAMPIRAN_BAP')) && $datapayment->getField('PAY_LAMPIRAN_BAP') != '' ) {
                              echo '<a href="uploads/payment/'.$datapayment->getField('PAY_LAMPIRAN_BAP').'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> BAP</span></a>';
                            } else {
                              echo '-';
                            }
                          ?>
                          </td> 
                          <td>
                            <?php 
                            if(trim($datapayment->getField('PAY_STATUS')) == 'Selesai') {
                              echo '<span class="badge badge-primary">'.$datapayment->getField('PAY_STATUS').'</span>';
                            } else {
                              echo '<span class="badge badge-danger">'.$datapayment->getField('PAY_STATUS').'</span>';
                            }
                            ?>
                          </td> 
                        </tr>
                        <?php
                        }
                      } else { echo '<tr><td colspan="6">. : : Tidak ada data : : .</td></tr>';} ?>
                      <tfoot>
                        <tr style="background-color:#b7b7b7; font-weight: bold;">
                          <td class="text-center" colspan="4">TOTAL</td>
                          <td><?= currencyToPage($totalPay) ?></td>
                          <td></td>
                          <td class="text-center"><?= $totalProgress.' %' ?></td>
                          <td colspan="3"></td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>

                  <!-- <div class="card mb-1 border-blue border-darken-1">
                    <div class="card-content">
                      <div class="p-1">
                        <h5>Dokumen Perubahan
                        <?php
                        // if ($reqContractingStatusKontrakId >= '2') { // Penyedia sudah approve ?>
                        <small style="font-size:0.9em">
                          <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_file_add?reqAidi=<?= $reqId ?>&reqProses=3&reqJenis=Perubahan Kontrak')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Dokumen </a>
                        </small>
                        <?php
                        // } ?>
                        </h5>
                        <div class="table-responsive">
                          <table id="dokumenFileIdTable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                            <?php // echo $this->libkontrak->getTableFile($reqId," AND A.FILE_JENIS = 'Perubahan Kontrak' ") ?>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div> -->

                  <?php
                  if ($reqJenisPekerjaan == '1') { // Hanya untuk pekerjaan TI ?>
                  <hr>
                  <h4>Service Level Agreement (SLA)
                    <?php
                    if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && $this->LEVEL_KONTRAK == '1') { // Penyedia sudah approve, pemeriksa ?>
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

                  <!-- <hr>
                  <h4>Sanksi dan Denda Keterlambatan
                    <?php
                    // if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && $this->LEVEL_KONTRAK == '1') { // Penyedia sudah approve, pemeriksa ?>
                    <small style="font-size:0.9em">
                      <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_sanksi2?reqAidi=<?php // echo $reqId ?>')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Sanksi/Denda Keterlambatan </a>
                    </small>
                    <?php
                    //} ?>
                    </h4>
                  <table id="tablesanksi" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                    <tr class="backcolornew">
                      <th>Nilai Sanksi</th>
                      <th>Nilai / Bagian Pekerjaan </th>
                      <th width="100px">Hari Keterlambatan</th>
                      <th>Nilai Denda</th>
                      <?php
                      // if ($this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && $this->LEVEL_KONTRAK == '1') { // update status oleh peng. kontrak bukan legal, pemeriksa ?>
                      <th width="105px">Aksi</th>
                      <?php
                      // } ?>
                    </tr>
                    <?php
                    // $this->load->model("Contractingsanksi");
                    // $datasanksi = new Contractingsanksi();
                    // $datasanksi->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
                    // if ($datasanksi->countRow() > 0) {
                    //   while($datasanksi->nextRow()) {
                      ?>
                      <tr>
                        <td><?php // echo $datasanksi->getField('NILAI_SANKSI') ?>/1000</td>
                        <td><?php // echo currencyToPage($datasanksi->getField('NILAI_PEKERJAAN')) ?></td>
                        <td><?php // echo $datasanksi->getField('HARI_TERLAMBAT') ?></td>
                        <td><?php // echo currencyToPage($datasanksi->getField('NILAI_DENDA')) ?></td>
                        <?php
                        // if ($this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && $this->LEVEL_KONTRAK == '1') { // update status oleh peng. kontrak bukan legal, pemeriksa ?>
                        <td>
                          <a style="color: #fff;" onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_sanksi_editform?reqAidi=<?php // echo $datasanksi->getField('SANKSIID') ?>')" class="badge badge-info"><i class="fa fa-edit"></i></a>
                          <a style="color: #fff;" onClick="delSanksi('<?php // echo $datasanksi->getField('SANKSIID') ?>','Hapus data ini?')" class="badge badge-danger"><i class="fa fa-trash"></i></a>
                        </td>
                        <?php
                        // }?>
                      </tr>
                      <?php
                    //   }
                    // } else { echo '<tr><td colspan="6">. : : Tidak ada data : : .</td></tr>';} ?>
                  </table> -->
                  <!-- <div class="card mb-1 border-blue border-darken-1">
                    <div class="card-content">
                      <div class="p-1">
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
                      </div>
                      <?php
                      // if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && $this->LEVEL_KONTRAK == '1') { // Penyedia sudah approve, pemeriksa ?>
                      <small style="margin: 10px;">
                        <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_sanksi_ketentuan?reqAidi=<?php // echo $reqId ?>&reqJenis=1')" class="badge badge-info pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Sanksi/Denda Keterlambatan </a>
                      </small>
                      <?php
                      // } ?>
                    </div>
                  </div> -->

                 <!--  <hr>
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
                        ?> <?php // echo $datasanksi->getField('KETERANGAN') ?>
                        <?php
                      //   }
                      // } else { echo '. : : Tidak ada keterangan : : .';} ?>
                      </div>
                      <?php
                    // if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && $this->LEVEL_KONTRAK == '1') { // Penyedia sudah approve , pemerisa?>
                    <small style="margin: 10px;">
                      <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_sanksi_ketentuan?reqAidi=<?php // echo $reqId ?>&reqJenis=2')" class="badge badge-info pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Sanksi/Denda Kelalaian </a>
                    </small>
                    <?php
                    // } ?>
                    </div>
                  </div> -->
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
