<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();   

$this->load->library("kauth");  $userLogin = new kauth(); 

$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqId = httpFilterRequest("reqId");
$getTahun = $this->session->userdata('setTahunKontrak');

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Contracting");
$this->load->model("Contractingrekanan");
$this->load->model("PaketPenilaian");
$this->load->model("Paketpemenang");

$contracting = new Contracting();
$spkpks = new Contractingrekanan();
$proses5 = new Contractingrekanan();
$getpaket_pemenang = new Paketpemenang();
$getpaket_pemenang2 = new Paketpemenang();


$contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId)); 
$contracting->firstRow();
$reqRekananId = str_replace(array("{","}"),"",$contracting->getField('PEMENANG')) ?: '-';
$reqPaketId = $contracting->getField('PAKET_ID') ?: '-';

$paketInfo->getPaket($reqPaketId);
$reqNama = $paketInfo->nama;
$bidding = $paketInfo->bidding;
$reqMultiPemenang = $paketInfo->multi_pemenang;

$spkpks->selectViewPKSSPK(array("A.CONTRACTINGREKANANID" => $reqId)); 
$spkpks->firstRow();

$reqJenisKontrak = $contracting->getField('JENIS_KONTRAK') ?: '-'; 
$reqJnsKontrak = $contracting->getField('JNS_KONTRAK') ?: '-'; 
$reqCode = $spkpks->getField('CR_CODE') ?: '-';  
$reqJnsKontrakStr = $spkpks->getField('JNS_KONTRAK_STR') ?: ''; 
$reqContractingStatusKontrakId = $spkpks->getField('CONTRACTINGSTATUSKONTRAKID') ?: '-';   
$reqRakananId = $spkpks->getField('REKANAN_ID') ?: '-';   
 
$cekPenilaianTotal = new PaketPenilaian();
$cekPenilaianTotal->selectPenilaian(array("PAKET_ID" => $reqPaketId, "CONTRACTINGREKANANID" => $reqId), "-1", "-1", " AND REKANAN_ID IN (".$reqRekananId.")");
if ($reqMultiPemenang == '0') {
  $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqPaketId, "PERINGKAT" => '1'), -1, -1); 
  $getpaket_pemenang2->selectByParams(array("PAKET_ID" => $reqPaketId, "PERINGKAT" => '1'), -1, -1);
} else {
  $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1);
  $getpaket_pemenang2->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1);
}

// if ($reqMultiPemenang == '0') {
//   exit;
// } else {
  // $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1); 
  // $getpaket_pemenang2->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1); 

  $totalPemenang = $getpaket_pemenang->countRow(); 
// }

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
          } else if (data.FLOW == '100') {
            location.href = "kontrak/index/contracting_serah_terima?tahun=<?= $getTahun ?>";
          } else if (data.FLOW == '200') {
            location.href = "kontrak/index/contracting_serah_terima?tahun=<?= $getTahun ?>";
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

function approvalKontrak(aidi) { 
  openAdd('main/loadUrlKontrak/kontrak/contracting_approval_pemeriksa?reqAidi='+aidi);
} 
</script>

<?= $this->libchat->kontrak($reqId); ?>
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
          <h4 class="mb-2">Berita Acara Serah Terima (BAST) Pekerjaan</h4> 

          <div class="row mb-1">
            <div class="col-md-12">
              <?php
              if ($this->LEGAL != '1' && $cekPenilaianTotal->countRow() > 0) { // Pengelola Kontrak bukan legal dan sudah isi penilaian
                echo $this->libkontrak->getStatusKontrakTeruskan($reqContractingStatusKontrakId,$reqId,$this->USER_TYPE_ID,$this->LEGAL,'PENUTUPAN'); 
              } else {
                echo '<div class="alert alert-danger text-center" style="font-weight:bold; margin-top:1%">. : : Kontrak tidak bisa di selesaikan karena penilaian belum di isi : : .</div>';
              }
              ?>
            </div>
          </div> 

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <h5>Dokumen BAST
                <?php 
                // if ($reqContractingStatusKontrakId >= '99') { // Status Penutupan?>
                <small style="font-size:0.7em">
                  <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_file_add_multi?reqAidi=<?= $reqId ?>&reqProses=5&reqJenis=BAST Pekerjaan')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Dokumen BAST </a> 
                </small>
                <?php 
                // } ?>
                </h5>
                <div class="table-responsive">
                  <table id="dokumenFileIdTable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                    <?= $this->libkontrak->getTableFileMulti($reqId," AND A.FILE_JENIS = 'BAST Pekerjaan' ") ?>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="form-actions">

            <div class="form-actions table-responsive">
                <table class="table table-bordered table-hover">
                  <tbody>
                    <tr>
                      <th width="2%">No</th>
                      <th>No. BAST</th>
                      <th>Tanggal</th>
                      <th>Penyedia</th>
                      <th>Status</th>
                      <th width="13%">Aksi</th>
                    </tr>
                    <?php 
                    $no = 1; 
                    while($getpaket_pemenang->nextRow())
                    { 
                      $proses5 = new Contractingrekanan();
                      $proses5->selectProses5(array("A.CONTRACTINGREKANANID" => $reqId, "A.REKANAN_ID" => $getpaket_pemenang->getField("REKANAN_ID"))); 
                      $proses5->firstRow();
                      ?>
                      <tr>
                        <td width="2%"><?= $no ?></td>
                        <td><?= $proses5->getField("CR_BAST_PEKERJAAN_NOMOR") ?: '-'; ?></td>
                        <td><?= getFormattedDate($proses5->getField("CR_BAST_PEKERJAAN_TANGGAL")) ?: '-'; ?></td>
                        <td><?= $getpaket_pemenang->getField("NAMA"); ?>
                        <td><?php if($proses5->getField("CR_BAST_PEKERJAAN_STATUS") == '1') { echo '<span class="badge badge-primary">Selesai</span>'; } else { echo '<span class="badge badge-danger">Proses</span>'; } ?></td>
                        </td>
                        <td>
                          <?php 
                          $cekPenilaianTotal = new PaketPenilaian();
                          $cekPenilaianTotal->selectPenilaian(array("PAKET_ID" => $reqPaketId, "CONTRACTINGREKANANID" => $reqId), "-1", "-1", " AND REKANAN_ID IN (".$getpaket_pemenang->getField("REKANAN_ID").")");
                          
                          $proses5 = new Contractingrekanan();
                          $proses5->selectProses5(array("A.CONTRACTINGREKANANID" => $reqId, "A.REKANAN_ID" => $getpaket_pemenang->getField("REKANAN_ID"))); 
                          if ($cekPenilaianTotal->countRow() > 0) { 
                            if ($proses5->countRow() > 0) { ?>
                            <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_serah_terima_hasil_multi_edit?reqId=<?= $reqId ?>&reqRekananId=<?= $getpaket_pemenang->getField("REKANAN_ID") ?>')" class="badge badge-primary mb-1 text-white"><i class="fa fa-pencil"></i> BAST Pekerjaan </a> 
                          <?php 
                            } else { ?>
                            <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_serah_terima_hasil_multi_edit?reqId=<?= $reqId ?>&reqRekananId=<?= $getpaket_pemenang->getField("REKANAN_ID") ?>')" class="badge badge-primary mb-1 text-white"><i class="fa fa-plus"></i> BAST Pekerjaan </a> 
                          <?php 
                            }
                          } else { echo '<span class="badge badge-danger">Belum di Nilai</span>';}  ?> 

                        </td>
                      </tr>
                      <!--   -->
                    <?php 
                    $no++;
                    } ?>
                  </tbody>
                </table>
              </div> 
             
          </div> 
        </div>
      </div>
    </div>
  </div> 
</div>   