<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$reqId = httpFilterRequest("reqId"); 

$this->libsession->cekSessionKontrak($reqId);   

$this->load->library("kauth");  $userLogin = new kauth(); 
$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$getTahun = $this->session->userdata('setTahunKontrak');

$this->load->model("Contracting");
$this->load->model("Contractingrekanan");

$contracting = new Contracting();
$spkpks = new Contractingrekanan();
$legal = new Contractingrekanan();

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
          <h4 class="mb-2">Data Kontrak <?= $reqJnsKontrakStr ?></h4> 

          <div class="row mb-1">
            <div class="col-md-12">
              <?php  
                if ($reqContractingStatusKontrakId >= '3') { // informasi proses persetujuan penyedia untuk peng. kontrak
                  echo $this->libkontrak->getStatusKontrakTeruskan($reqContractingStatusKontrakId,$reqId,$this->USER_TYPE_ID,$this->LEGAL,'SPK-PKS');
                }
              ?>
            </div>
          </div>

          <div class="form-actions">  
             <?= $this->libkontrak->getInfoKontrak($reqId); ?> 
            <hr>
            <h4>Output Pekerjaan</h4>
            <table id="deliveriable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
              <tr class="backcolornew">
                <th class="text-center">Pekerjaan</th>
                <th class="text-center">Keterangan</th>
                <th class="text-center" width="150px">Tanggal</th>
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
                </tr>
                <?php 
                }
              } else { echo '<tr><td colspan="3">. : : Tidak ada data : : .</td></tr>';} ?>
            </table>

            <hr>
            <h4>Penagihan</h4>
            <table id="tabletermin" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
              <tr class="backcolornew">
                <?php // if ($reqMetodePembayaran == 2) { ?>
                <th class="text-center">Penagihan</th>
                <?php
                // } else { ?>
                <!-- <th>Keterangan</th> -->
                <?php
                // } ?>
                <th class="text-center" width="80px">Nilai Pembayaran</th>
                <th class="text-center" width="100px">Persentase</th>
                <th class="text-center" width="250px">Tanggal</th>
                <!-- <th>Berita Acara</th> -->
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
                  <td><?= $datapayment->getField('PAY_TERMIN_KE') ?></td>
                  <?php
                  // } ?>
                  <td><?= currencyToPage($datapayment->getField('PAY_NILAI')) ?></td>
                  <!-- <td><?php // $datapayment->getField('PAY_LAMPIRAN') ?></td>  -->
                  <td class="text-center"><?= $datapayment->getField('PAY_PROGRES') ?> %</td>
                  <td class="text-center" width="100px">
                    <?= getFormattedDateShort2(dateTimeToPageCheck($datapayment->getField('PAY_DATE_DARI'))) ?> s/d
                    <?= getFormattedDateShort2(dateTimeToPageCheck($datapayment->getField('PAY_DATE_SAMPAI'))) ?>
                      
                  </td>
                </tr>
                <?php 
                }
              } else { echo '<tr><td colspan="6">. : : Tidak ada data : : .</td></tr>';} ?>
            </table>

            <?php 
            if ($reqJenisPekerjaan == '1') { // Hanya untuk pekerjaan TI ?>
            <hr>
            <h4>Service Level Agreement (SLA)</h4>
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
                if ($reqContractingStatusKontrakId >= '2') { // Penyedia sudah approve ?>
                <small style="font-size:0.9em">
                  <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_file_add?reqAidi=<?= $reqId ?>&reqProses=1&reqJenis=Dokumen Kontrak&reqType=penyedia')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Dokumen Kontrak </a>
                </small>
                <?php 
                } ?>
                </h5>
                <div class="table-responsive">
                  <table id="dokumenFileIdTable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                    <?= $this->libkontrak->getTableFilePenyedia($reqId," AND A.FILE_JENIS = 'Dokumen Kontrak' AND FILE_PUBLISH_PENYEDIA = '1' ") ?>
                  </table>
                </div>
              </div>
            </div>
          </div>

            <!-- <hr>
            <h4>Sanksi dan Denda Keterlambatan</h4>
            <table id="tablesanksi" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
              <tr class="backcolornew">
                <th>Nilai Sanksi</th>
                <th>Nilai / Bagian Pekerjaan </th>
                <th width="100px">Hari Keterlambatan</th>
                <th>Nilai Denda</th>
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
                </tr>
                <?php 
              //   }
              // } else { echo '<tr><td colspan="6">. : : Tidak ada data : : .</td></tr>';} ?>
            </table>
            <div class="card mb-1 border-blue border-darken-1">
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
              </div>
            </div>  -->

            <!-- <hr>
            <h4>Sanksi dan Denda Kelalaian </h4>
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
              </div>
            </div>  -->

          </div> 
        </div>
      </div>
    </div>
  </div> 
</div>   