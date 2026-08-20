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

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->model("Contracting");
$this->load->model(array("Contractingrekanan","Contractingdeliverable"));

$contracting = new Contracting();
$spkpks = new Contractingrekanan();
$contractingdeliverable = new Contractingdeliverable();

$contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
$contracting->firstRow();

$spkpks->selectViewPKSSPK(array("A.CONTRACTINGREKANANID" => $reqId));
$spkpks->firstRow();

$reqJenisKontrak = $contracting->getField('JENIS_KONTRAK') ?: '-';
$reqCode = $spkpks->getField('CR_CODE') ?: '-';
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

?>

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/ui/jqueryui.css">
<script src="<?=base_url()?>assets/new/js/scripts/ui/jquery-ui/navigations.js"></script>
<style type="text/css">
  small { font-weight: bold; font-size: 11.5px }
  tr.backcolornew {
    background: #cf252d !important;
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
          <h4 class="mb-2">Tagihan</h4>

          <?= $this->libkontrak->getInfoKontrak($reqId); ?> 

          <div class="form-actions table-responsive">

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
                <?php
                if ($this->LEGAL != '1') { // update status oleh peng. kontrak bukan legal ?>
                <th width="50px">Aksi</th>
                <?php
                } ?>
              </tr>
              <?php
              $this->load->model("Contractingpayment");
              $datapayment = new Contractingpayment();
              $datapayment->selectByParams(array("A.CONTRACTINGREKANANID"=>$reqId));
              if ($datapayment->countRow() > 0) {
                $tombolTutup = 0;
                while($datapayment->nextRow()) {
                  $totalPay += $datapayment->getField('PAY_NILAI');
                  $totalProgress += $datapayment->getField('PAY_PROGRES');
                  $statusPay = str_replace(' ','',$datapayment->getField('PAY_STATUS'));

                  // if ($datapayment->getField('PAY_LAMPIRAN') && $statusPay == 'Selesai') {
                  if ($statusPay == 'Selesai') {
                    $tombolTutup++;
                  }
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
                  <td class="text-center">
                    <?php 
                    $presen = 0;
                    $ff = explode(',',$datapayment->getField('DELIVERABLEID_FK'));
                    if (is_array($ff)) {
                      $aArray = $ff;
                    }
                    $contractingdeliverable->selectByParams(array("CONTRACTINGREKANANID"=>$datapayment->getField('CONTRACTINGREKANANID')));
                    $no = 1;
                    $countProses = 0;
                    while ($contractingdeliverable->nextRow()) 
                    {  
                      if (in_array($contractingdeliverable->getField("DELIVERABLEID"), $aArray)) {
                         $presen += $contractingdeliverable->getField("PRESENTASE");
                       }  

                       // Cek status harus selesai semua, agar bisa update tagihan
                       if (trim($contractingdeliverable->getField("STATUS")) == 'Proses') {
                         $countProses++;
                       }
                    $no++;
                    } 
                    // echo $presen;
                    ?> 
                    <?php 
                    echo $datapayment->getField('PAY_PROGRES').' %';
                    // if ($datapayment->getField('PAY_PROGRES') == $presen) {
                      // echo $presen;
                    // } else {
                      // echo '<span style="cursor: pointer" onclick="return updateProg'.$datapayment->getField('PAYMENTID').'()" class="badge badge-warning btn-xs">Update</span>';
                      // echo '<span class="badge badge-warning btn-xs">Update</span>';
                    // }
                    ?>
                  </td>
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
                    if($statusPay == 'Selesai') {
                      echo '<span class="badge badge-primary">'.$datapayment->getField('PAY_STATUS').'</span>';
                    } else {
                      echo '<span class="badge badge-danger">'.$datapayment->getField('PAY_STATUS').'</span>';
                    }
                    ?>
                  </td>
                  <?php
                  if ($this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && $this->LEVEL_KONTRAK == '3') { // update status oleh peng. kontrak bukan legal, pemeriksa kontrak ?>
                  <td>
                      <a style="color: #fff;" onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_payment_edit?reqAidi=<?= $datapayment->getField('PAYMENTID') ?>-<?= $reqMetodePembayaran ?>&reqConRekId=<?= $reqId ?>')" class="badge badge-info"><span class="fa fa-edit"></span></a>
                  </td>
                  <?php
                  } else { echo '<td>-</td>'; }?>
                </tr>
                <script type="text/javascript">
                  function updateProg<?=$datapayment->getField('PAYMENTID')?>() {
                    $.getJSON("contracting_json/updateprogres/?paymentid="+<?= $datapayment->getField('PAYMENTID') ?>+"&presen="+<?= $presen ?>,function(data){
                      // alert(data.PESAN);
                      location.reload();
                    }); 
                  }
                </script>
                <?php
                }
              } else {
                if ($reqMetodePembayaran == 2) {
                  echo '<tr><td colspan="6">. : : Tidak ada data : : .</td></tr>';
                } else {
                  echo '<tr><td colspan="5">. : : Tidak ada data : : .</td></tr>';
                }
              } ?>
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


            <!-- <a href="kontrak/index/contracting_detail?reqId=<?= $reqId; ?>" class="<?= CLASS_BTN_DANGER ?> mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a> -->

          </div>
          <div class="card mb-1 border-blue border-darken-1 mt-1">
            <div class="card-content">
              <div class="p-1">
                <h5>Dokumen <?= $reqJnsKontrakStr ?> 
                </h5>
                <div class="table-responsive">
                  <table id="dokumenFileIdTable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                    <?= $this->libkontrak->getTableFile($reqId," AND A.FILE_JENIS = 'Dokumen Tagihan' ") ?>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
