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

$this->load->model(array("Contracting","Contractingrekanan","Contractingdeliverable","PaketPenilaian","Contractingpayment","Contractingjaminanpemeliharaan"));

$contracting = new Contracting();
$spkpks = new Contractingrekanan();
$contractingdeliverable = new Contractingdeliverable();
$countPenilaian = new PaketPenilaian();

$contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
$contracting->firstRow();

$spkpks->selectViewPKSSPK(array("A.CONTRACTINGREKANANID" => $reqId));
$spkpks->firstRow();

$reqJenisKontrak = $contracting->getField('JENIS_KONTRAK') ?: '-';
$reqPemutusan = $contracting->getField('CR_PEMUTUSAN') ?: '-';
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
$reqPICKontrak = $spkpks->getField('PIC_KONTRAK') ?: '-';
$reqPICPengendali = $spkpks->getField('PIC_PENGENDALI') ?: '-';
$reqPICPenyelesaian = $spkpks->getField('PIC_PENYELESAIAN') ?: '-';

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
.blinking-element {
  animation: blink-smooth 1s infinite;
}
@keyframes blink-smooth {
  0% {opacity: 1;}
  50% {opacity: 0;}
  100% {opacity: 1;}
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
          } else if (data.FLOW == '100' || data.FLOW == '200') {
            // location.href = "kontrak/index/contracting_serah_terima?tahun=<?= $getTahun ?>";
            location.href = "kontrak/index/contracting_selesai";
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
      <?= $this->libkontrak->getMenu($reqId); ?>
    </div>
  </div>
  <div class="col-md-9 col-sm-9">
    <div class="card">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <!-- <h4 class="mb-2">Termin Pembayaran</h4> -->
          <h4 class="mb-2">Tagihan</h4>

          <?php 
            if ($this->LEGAL != '1' && $this->USER_TYPE_ID != '20') 
            { // Pengelola Kontrak bukan Legal, pemeriksa kontrak 
              $totalPointPenilaian = 12;
              $countPenilaian->selectPenilaian(array("CONTRACTINGREKANANID"=>$reqId)); 

              $datapaymentCount = new Contractingpayment();
              $datapaymentCount->selectByParams(array("A.CONTRACTINGREKANANID"=>$reqId));
              $datapaymentCountSelesai = new Contractingpayment();
              $datapaymentCountSelesai->selectByParams(array("A.CONTRACTINGREKANANID"=>$reqId, "PAY_STATUS" => "Selesai"));

             if ($datapaymentCountSelesai->countRow() == $datapaymentCount->countRow() || $reqPemutusan > 0) 
             {
              if ($countPenilaian->countRow() == $totalPointPenilaian) {

                // Cek Penilaian Approval 
                $this->load->model(array("Queryfree"));
                $countApproval = new Queryfree();
                $countApproval->selectByParams("SELECT APPROVAL_UNIT,APPROVAL_KASUBDIT,APPROVAL_PPK FROM PAKET_PENILAIAN_REKANAN 
                                                WHERE CONTRACTINGREKANANID = ".$reqId." GROUP BY APPROVAL_UNIT,APPROVAL_KASUBDIT,APPROVAL_PPK");
                $countApproval->firstRow();
                $apprUnit = $countApproval->getField("APPROVAL_UNIT"); 
                $apprKasubdit = $countApproval->getField("APPROVAL_KASUBDIT"); 
                $apprPPK = $countApproval->getField("APPROVAL_PPK"); 

                if ($apprUnit == '1' && $apprKasubdit == '1' && $apprPPK == '1') {
                 echo $this->libkontrak->getStatusKontrakTeruskan($reqContractingStatusKontrakId,$reqId,$this->USER_TYPE_ID,$this->LEGAL,'PELAKSANAAN');
                 echo '<a onClick="openAddFrame(\'main/loadUrlKontrak/kontrak/contracting_jaminan_pemeliharaan?reqId='.$reqId.'&reqPaketId='.$reqPaketId.'\')" class="'.CLASS_BTN_INFO.' ml-1"> <i class="fa fa-plus"></i> Jaminan Pemeliharaan </a>';
                 echo "<br><br>";
                } else {
                  echo '<div class="alert alert-danger mb-1 text-center" style="font-weight:bold; margin-top:1%">. : : Penilaian belum di setujui, lihat persetujuan <a href="'.base_url('kontrak/index/contracting_penilaian?reqId='.$reqId).'" style="color:#fff"> <i>klik disini</i> </a> : : .</div>';
                }


              } else {
                echo '<div class="alert alert-danger mb-1 text-center" style="font-weight:bold; margin-top:1%">. : : Penilaian belum di isi lengkap atau belum disetujui : : .</div>';
              }
             }
            } 

            $dataJamPel = new Contractingjaminanpemeliharaan();
            $dataJamPel->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
            $dataJamPel->firstRow();
            $id = $dataJamPel->getField('CONTRACTING_JAMPEL_ID');
            if ($id != '') {
            ?>
            <div class="form-actions table-responsive">
              <table id="tabletermin" class="border-double table mb-0 table-bordered mb-2" style="width: 100%"> 
                <thead>
                  <tr class="backcolornew">
                    <td>Nomor</td>
                    <td>Nilai</td>
                    <td>Masa</td>
                    <td>Tanggal Mulai</td>
                    <td>Tanggal Akhir</td>
                    <td>File Jaminan</td>
                  </tr>
                </thead>
                <tbody id="tbodyDeliverable">   
                    <tr>
                      <td> <?= $dataJamPel->getField('NOMOR') ?></td> 
                      <td> <?= $dataJamPel->getField('NILAI') ?></td> 
                      <td> <?= $dataJamPel->getField('MASA'); ?> Bulan</td>
                      <td> <?= getFormattedDateShort2(dateTimeToPageCheck($dataJamPel->getField('TANGGAL_MULAI'))) ?></td> 
                      <td> <?= getFormattedDateShort2(dateTimeToPageCheck($dataJamPel->getField('TANGGAL_AKHIR'))) ?></td> 
                      <td>  
                        <?php 
                        if (file_exists('uploads/payment/'.$dataJamPel->getField('FILE_JAMINAN')) && $dataJamPel->getField('FILE_JAMINAN') != '' ) {
                          echo '<a href="uploads/payment/'.$dataJamPel->getField('FILE_JAMINAN').'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> File Jaminan</span></a>';
                        } ?>
                      </td> 
                    </tr> 
                   
                </tbody>
              </table> 
            </div>
            <?php 
            } ?>

          <?= $this->libkontrak->getInfoKontrak($reqId); ?> 
          
          <div class="form-actions table-responsive">

            <table id="tabletermin" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
              <tr class="backcolornew">
                <?php
                if ($this->LEGAL != '1' && $this->USER_TYPE_ID == '12' && $reqPICPenyelesaian == $this->USER_LOGIN_ID) { // update status oleh peng. kontrak bukan legal ?>
                <th class="text-center" width="50px">Aksi</th>
                <?php
                } ?>
                <th width="100px">Status</th>
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
                <th width="50px" class="text-center">Tanggal diterimanya hardcopy tagihan</th>
                <th width="50px" class="text-center">Tanggal penyerahan dokumen tagihan ke DKA setelah BAST dan BAP diterbitkan</th>
                <!-- <th width="50px">BA</th> -->
              </tr>
              <?php
              $datapayment = new Contractingpayment();
              $datapayment->selectByParams(array("A.CONTRACTINGREKANANID"=>$reqId));
              if ($datapayment->countRow() > 0) {
                $tombolTutup = 0;
                $tombolTutupDeliverable = 0;
                while($datapayment->nextRow()) {
                  $statusPay = str_replace(' ','',$datapayment->getField('PAY_STATUS'));
                  $statusDeliverable = str_replace(' ','',$datapayment->getField('STATUS')); // Status Deliverable

                  // if ($datapayment->getField('PAY_LAMPIRAN') && $statusPay == 'Selesai') {
                  if ($statusPay == 'Selesai') {
                    $tombolTutup++;
                  }

                  if ($statusDeliverable == 'Selesai') {
                    $tombolTutupDeliverable++;
                  }
                ?>
                <tr>
                  <?php
                  if ($this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && $this->LEVEL_KONTRAK == '3' && $reqPICPenyelesaian == $this->USER_LOGIN_ID) {
                    if ($statusDeliverable == 'Selesai') 
                    {  ?>
                  <td>
                      <a style="color: #fff;" onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_payment_edit?reqAidi=<?= $datapayment->getField('PAYMENTID') ?>-<?= $reqMetodePembayaran ?>&reqConRekId=<?= $reqId ?>')" class="badge badge-info"><span class="fa fa-edit"></span> Update tagihan</a>
                  </td>
                  <?php
                    } else { echo '<td><span class="badge badge-danger">Belum terealisasi</span></td>'; }
                  } ?>
                  <td>
                    <?php 
                    if($statusPay == 'Selesai') {
                      echo '<span class="badge badge-primary">'.$datapayment->getField('PAY_STATUS').'</span>';
                    } else {
                      echo '<span class="badge badge-danger">'.$datapayment->getField('PAY_STATUS').'</span>';
                    }
                    ?>
                  </td>
                  <td><?= $datapayment->getField('PAY_NOMOR') ?></td>
                  <td><?= getFormattedDateShort($datapayment->getField('PAY_DATE')) ?></td>


                  <!-- cek tanggal -->
                  <?php 
                  $today = new DateTime();
                  $tanggalPayDari = new DateTime($datapayment->getField('PAY_DATE_DARI'));
                  $tanggalPaySampai = new DateTime($datapayment->getField('PAY_DATE_SAMPAI'));

                  // Mengecek apakah tanggal pengiriman berada dalam rentang hari ini
                  $blink = '';
                  $blinkStyle = '';
                  if ($tanggalPayDari <= $today && $tanggalPaySampai >= $today && $statusPay != 'Selesai') {
                    $blink = "blinking-element";
                    $blinkStyle = "background:#F7CA18;";
                  }
                  ?> 
                  <td class="text-center <?= $blink ?>" style="<?= $blinkStyle ?>">
                    <?= getFormattedDateShort($datapayment->getField('PAY_DATE_DARI')).' sd '.getFormattedDateShort($datapayment->getField('PAY_DATE_SAMPAI')) ?>
                  </td>


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
                    // if ($datapayment->getField('PAY_PROGRES') == $presen) {
                      // echo $presen;
                    // } else {
                      echo $datapayment->getField('PAY_PROGRES').' %';
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
                  <td><?= getFormattedDateShort($datapayment->getField('PAY_DATE_TERIMA_HARDCOPY')) ?></td>
                  <td><?= getFormattedDateShort($datapayment->getField('PAY_DATE_PENYERAHAN')) ?></td>
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
            </table>
          </div>
            <!-- <a href="kontrak/index/contracting_detail?reqId=<?= $reqId; ?>" class="<?= CLASS_BTN_DANGER ?> mr-1 text-white"> <?= BTN_KEMBALI ?></a> -->

            <div class="card mb-1 border-blue border-darken-1 mt-1">
              <div class="card-content">
                <div class="p-1">
                  <h5>Dokumen <?= $reqJnsKontrakStr ?>
                  <?php
                  // if ($reqContractingStatusKontrakId == '2' || $reqContractingStatusKontrakId == '3') { // Penyedia sudah approve ?>
                  <small style="font-size:0.9em">
                    <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_file_add?reqAidi=<?= $reqId ?>&reqProses=4&reqJenis=Dokumen Tagihan')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Dokumen Tagihan </a>
                  </small>
                  <?php
                  // } ?>
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
</div>
