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

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqProses = httpFilterRequest("reqProses");
$getTahun = $this->session->userdata('setTahunKontrak');


$this->load->model("Contracting");
$this->load->model("Contractingrekanan");

$contracting = new Contracting();
$spkpks = new Contractingrekanan();

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
</script>

<?= $this->libchat->kontrakPenyedia($reqId); ?>
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
          <h4 class="mb-2">Tagihan</h4> 
          <?php echo $this->libkontrak->getStatusKontrakTeruskan($reqContractingStatusKontrakId,$reqId,$this->USER_TYPE_ID,$this->LEGAL,'PELAKSANAAN'); ?>
          <div class="form-actions table-responsive">
            <table id="tabletermin" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
              <tr class="backcolornew">
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
                ?>
                <tr>
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
                  </td> 
                </tr>
                <?php 
                }
              } else { 
                if ($reqMetodePembayaran == 2) {
                  echo '<tr><td colspan="6">. : : Tidak ada data : : .</td></tr>';
                } else {
                  echo '<tr><td colspan="5">. : : Tidak ada data : : .</td></tr>';
                }
              } ?>
            </table>
          </div> 

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <h5>Dokumen <?= $reqJnsKontrakStr ?>
                <?php 
                if ($reqContractingStatusKontrakId >= '2') { // Penyedia sudah approve ?>
                <small style="font-size:0.9em">
                  <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_file_add?reqAidi=<?= $reqId ?>&reqProses=1&reqJenis=Dokumen Tagihan&reqType=penyedia')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Dokumen </a>
                </small>
                <?php 
                } ?>
                </h5>
                <div class="table-responsive">
                  <table id="dokumenFileIdTable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                    <?= $this->libkontrak->getTableFilePenyedia($reqId," AND A.FILE_JENIS = 'Dokumen Tagihan' AND FILE_PUBLISH_PENYEDIA = '1' ") ?>
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