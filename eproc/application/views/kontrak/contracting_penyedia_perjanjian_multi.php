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
$this->load->model("Paketpemenang");

$contracting = new Contracting();
$spkpks = new Contractingrekanan();
$legal = new Contractingrekanan();

$contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
$contracting->firstRow();
$contractingprosesid = $contracting->getField('CONTRACTINGPROSESID');

$spkpks->selectViewPKSSPK(array("A.CONTRACTINGREKANANID" => $reqId, "A.REKANAN_ID" => $this->ID));
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
// echo $reqContractingStatusKontrakId; die;
$legal->selectViewLegal(array("A.CONTRACTINGREKANANID" => $reqId,  "A.REKANAN_ID" => $this->ID));
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
      $.getJSON("contracting_json/proseskontrakmulti/?reqAidi="+aidi+"&flow="+flow,
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
                if ($reqContractingStatusKontrakId >= '4') { // informasi proses persetujuan penyedia untuk peng. kontrak
                  echo $this->libkontrak->getStatusKontrakTeruskanMulti($reqContractingStatusKontrakId,$reqRekananProses1Id,$this->USER_TYPE_ID,$this->LEGAL,'SPK-PKS');
                }
              ?>
            </div>
          </div>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <h5>Dokumen <?= $reqJnsKontrakStr ?>
                <?php
                if ($reqContractingStatusKontrakId >= '2') { // Penyedia sudah approve ?>
                <small style="font-size:0.7em">
                  <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_file_add?reqAidi=<?= $reqId ?>&reqProses=1&reqJenis=Dokumen Kontrak&reqType=penyedia')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Dokumen Kontrak </a>
                </small>
                <?php
                } ?>
                </h5>
                <div class="table-responsive">
                  <table id="dokumenFileIdTable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                    <?= $this->libkontrak->getTableFilePenyedia($reqId," AND (A.FILE_JENIS = 'Dokumen Kontrak') AND FILE_PUBLISH_PENYEDIA = '1' AND (A.REKANAN_ID = $this->ID OR A.CREATED_BY =$this->USER_LOGIN_ID )") ?>

                    <?php // $this->libkontrak->getTableFilePenyedia($reqId," AND A.FILE_JENIS = 'Dokumen Kontrak' AND FILE_PUBLISH_PENYEDIA = '1' ") ?>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="form-actions">

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
                    <small>Nilai Kontrak Maksimal </small> <br>  <?= currencyToPage($reqNilaiKontrak) ?>
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
            <hr>

            <h4>Daftar Barang Jasa </h4>
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
            if ($contractingprosesid >= 3)
            { // SELESAI KONTRAK SELAIN KONTRAK PAYUNG ?>
            <h4>Termin Pembayaran</h4>
            <table id="tabletermin" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
              <tr class="backcolornew">
                <?php if ($reqMetodePembayaran == 2) { ?>
                <th class="text-center">Termin</th>
                <?php
                } ?>
                <th>Nilai Pembayaran</th>
                <!-- <th>Berita Acara</th> -->
                <th width="100px">Progres</th>
                <th>Keterangan</th>
              </tr>
              <?php
              $this->load->model("Contractingpayment");
              $datapayment = new Contractingpayment();
              $datapayment->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
              if ($datapayment->countRow() > 0) {
                while($datapayment->nextRow()) {
                ?>
                <tr>
                  <?php if ($reqMetodePembayaran == 2) { ?>
                  <td class="text-center"><?= $datapayment->getField('PAY_TERMIN_KE') ?></td>
                  <?php
                  } ?>
                  <td><?= currencyToPage($datapayment->getField('PAY_NILAI')) ?></td>
                  <!-- <td><?php // $datapayment->getField('PAY_LAMPIRAN') ?></td>  -->
                  <td><?= $datapayment->getField('PAY_PROGRES') ?> %</td>
                  <td><?= $datapayment->getField('PAY_KETERANGAN') ?></td>
                </tr>
                <?php
                }
              } else { echo '<tr><td colspan="6">. : : Tidak ada data : : .</td></tr>';} ?>
            </table>
            <?php 
            } ?>

            <!-- <h4>Sanksi dan Denda Keterlambatan</h4>
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
                  ?> <?php // echo $datasanksi->getField('KETERANGAN') ?>
                  <?php
                //   }
                // } else { echo '. : : Tidak ada keterangan : : .';} ?>
                </div>
              </div>
            </div> -->

            <!-- <a href="kontrak/index/contracting_penyedia_detail?reqId=<?= $reqId; ?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?></a>  -->

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
