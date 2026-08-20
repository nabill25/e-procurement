<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$reqId = httpFilterRequest("reqId");
$getTahun = $this->session->userdata('setTahunKontrak');

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
$kontrakPayung = new Contractingrekanan();
$getpaket_pemenang = new Paketpemenang();
$getpaket_pemenang2 = new Paketpemenang();

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

$paketInfo->getPaket($reqPaketId);
$bidding = $paketInfo->bidding;
$reqMultiPemenang = $paketInfo->multi_pemenang;

if ($reqMultiPemenang == '0') {
  $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqPaketId, "PERINGKAT" => '1'), -1, -1);
  $getpaket_pemenang2->selectByParams(array("PAKET_ID" => $reqPaketId, "PERINGKAT" => '1'), -1, -1);
} else {
  // echo "Multi Pemanang";
  $getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1);
  $getpaket_pemenang2->selectByParams(array("PAKET_ID" => $reqPaketId), -1, -1);
}

  $totalPemenang = $getpaket_pemenang->countRow();
  $kontrakPayung->selectProses1(array("A.CONTRACTINGREKANANID" => $reqId));
  $totalStatus2 = 0;
  $totalStatus1 = 0;
  $totalStatus0 = 0;
  while($kontrakPayung->nextRow())
  {
    if ($kontrakPayung->getField('CONTRACTINGSTATUSKONTRAKID') == '5') {
      $totalStatus2++;
    }

    if ($kontrakPayung->getField('CONTRACTINGSTATUSKONTRAKID') == '4') {
      $totalStatus1++;
    }

    if ($kontrakPayung->getField('CONTRACTINGSTATUSKONTRAKID') == '3') {
      $totalStatus0++;
    }
  }
  // echo $totalPemenang.'-'.$totalStatus2.'-'.$totalStatus1.'-'.$totalStatus0;

  if ($totalStatus0 == 0) {
    if ($totalStatus1 == 0) {
      if ($totalStatus2 == 0) {
      } else {
        $reqContractingStatusKontrakId = 5; // Status SPPBJ Penyedia Setuju
      }
    } else {
      $reqContractingStatusKontrakId = 4; // Status SPPBJ Persetujuan Penyedia
    }
  } else {
      $reqContractingStatusKontrakId = 3; // Status SPPBJ dibuat
  }
// }

// $reqContractingStatusKontrakId = 5;
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

<?php
if ($this->LEGAL != '1' && $reqLegalNomorPKS != '-') {
 echo $this->libchat->kontrak($reqId);
} ?>

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
          <h4 class="mb-2">Data <?= $reqJnsKontrakStr ?></h4>

          <div class="row mb-1">
            <div class="col-md-12">
              <?php
              // Yang kirim ke penyedia bagian pengelola kontrak & Data PKS Sudah di isi, pemeriksa
              // if ($this->LEGAL != '1' && $reqLegalNomorPKS != '-' && $this->USER_TYPE_ID != '20') {
              if ($this->LEGAL != '1'  && $this->USER_TYPE_ID != '20') {
                echo $this->libkontrak->getStatusKontrakTeruskan($reqContractingStatusKontrakId,$reqId,$this->USER_TYPE_ID,$this->LEGAL,'SPK-PKS');
              } else if ($this->USER_TYPE_ID == '20') {
                echo $this->libkontrak->getStatusKontrakTeruskan($reqContractingStatusKontrakId,$reqId,$this->USER_TYPE_ID,$this->LEGAL,'SPK-PKS');
              } else {
                // echo $reqContractingStatusKontrakId.'---';
                // if ($reqContractingStatusKontrakId == '4') { // informasi proses persetujuan penyedia untuk peng. kontrak
                echo $this->libkontrak->getStatusKontrakTeruskan($reqContractingStatusKontrakId,$reqId,$this->USER_TYPE_ID,$this->LEGAL,'SPK-PKS');
                // }
              }
              ?>
              <!-- <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_catatan?reqAidi=<?= $reqId ?>')" class="<?= CLASS_BTN_INFO ?> ml-1"><span class="fa fa-plus"></span> Catatan</a> -->
            </div>
          </div>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <h5>Dokumen <?= $reqJnsKontrakStr ?>
                <?php
                if ($reqContractingStatusKontrakId >= '2') { // Penyedia sudah approve ?>
                <small style="font-size:0.7em">
                  <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_file_add_multi?reqAidi=<?= $reqId ?>&reqProses=1&reqJenis=Dokumen Kontrak')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Dokumen Kontrak </a>
                </small>
                <?php
                } ?>
                </h5>
                <div class="table-responsive">
                  <table id="dokumenFileIdTable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                    <?= $this->libkontrak->getTableFileMulti($reqId," AND A.FILE_JENIS = 'Dokumen Kontrak' ") ?>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="form-actions table-responsive">
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
                    <td><?= $getpaket_pemenang->getField("NAMA").' '.$contractingrekananSPPBJ->getField("CONTRACTING_STATUS_KONTRAK"); ?>
                    <?php if ($contractingrekananSPPBJ->getField("CONTRACTINGSTATUSKONTRAKID") > 2) { ?>
                    <br><small><i><?= $contractingrekananSPPBJ->getField("CR_STATUS_KONTRAK_STR"); ?></i></small>
                    <?php } ?>
                    </td>
                    <td>
                      <?php
                      if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1')
                      { // Penyedia sudah approve & Peng. Kontrak bagian Legal tapi User Pengguna, pemeriksa ?>
                      <a href="kontrak/index/contracting_persiapan_kontrak_multi_edit?reqId=<?= $contractingrekananSPPBJ->getField("CONTRACTINGREKANANPROSES1ID") ?>&reqConRekId=<?= $contractingrekananSPPBJ->getField("CONTRACTINGREKANANID") ?>" class="<?= CLASS_BTN_PRIMARY ?> mb-1 btn-sm"> <i class="fa fa-pencil"></i> </a>
                      <?php
                      }?>
                        <button class="<?= CLASS_BTN_SUCCESS ?> mb-1 btn-sm" data-toggle="modal" data-target=".bs-example-modal-lg-<?= $getpaket_pemenang->getField("REKANAN_ID") ?>"><span class="fa fa-eye"></span> Dok. Pemilihan </button>
                        <button onClick="openAdd('main/loadUrl/main/data_rekanan_potensi?reqId=<?= $getpaket_pemenang->getField("REKANAN_ID") ?>&reqType=4');" class="<?= CLASS_BTN_DARK ?> mb-1 btn-sm" ><span class="fa fa-eye"></span> Data Penyedia </button>


                    </td>
                  </tr>
                  <!--   -->
                <?php
                $no++;
                } ?>
              </tbody>
            </table>
          </div>

          <div class="form-actions mt-3">
            <h4>Daftar Barang Jasa

              <?php
              $this->load->model("Contractingfile");

              $contractingfile = new Contractingfile();
              $contractingfile->selectByParamsMulti(array("A.CONTRACTINGREKANANID" => $reqId),-1,-1,"AND A.FILE_JENIS = 'Dokumen Kontrak Barang Jasa' ");
              $contractingfile->firstRow();

              if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20') { // Penyedia sudah approve, pemeriksa ?>
              <small style="font-size:0.7em">
                <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_material?reqAidi=<?= $reqId ?>')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Barang Jasa </a>
              </small>

              <?php
              // if ($contractingfile->countRow() > 0) {
              //   if ($contractingfile->getField('file_nama_encrypt') != '' && file_exists($contractingfile->getField('file_path').'/'.$contractingfile->getField('file_nama_encrypt'))) {
              //     echo '<small style="font-size:0.7em">
              //             <a href="'.$contractingfile->getField('file_path').'/'.$contractingfile->getField('file_nama_encrypt').'" target="_blank"><span class="badge badge-danger mr-1 text-white"> <i class="fa fa-download"></i> Download Dokumen Barang Jasa</span>
              //             </a>
              //           </small>';
              //   }
              // }
               ?>
              <!-- <small style="font-size:0.7em">
                <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_file_add_importexcel?reqAidi=<?= $reqId ?>&reqProses=1&reqJenis=Dokumen Kontrak Barang Jasa')" class="badge badge-success pull-right mr-1 text-white"> <i class="fa fa-upload"></i> Import Barang Jasa </a>
              </small> -->
              <?php
              } else if ($this->USER_TYPE_ID == '20') {
                // if ($contractingfile->countRow() > 0) {
                //   if ($contractingfile->getField('file_nama_encrypt') != '' && file_exists($contractingfile->getField('file_path').'/'.$contractingfile->getField('file_nama_encrypt'))) {
                //     echo '<small style="font-size:0.7em">
                //             <a href="'.$contractingfile->getField('file_path').'/'.$contractingfile->getField('file_nama_encrypt').'" target="_blank"><span class="badge badge-danger mr-1 text-white"> <i class="fa fa-download"></i> Download Dokumen Barang Jasa</span>
                //             </a>
                //           </small>';
                //   }
                // }
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
          <h4>Sanksi dan Denda Keterlambatan
            <?php
            // if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20') { // Penyedia sudah approve, pemeriksa ?>
            <small style="font-size:0.7em">
              <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_sanksi?reqAidi=<?php // echo $reqId ?>')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Sanksi/Denda Keterlambatan </a>
            </small>
            <?php
            // } ?>
            </h4>
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
              <?php
              // if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20') { // Penyedia sudah approve, pemeriksa ?>
              <small style="margin: 10px;">
                <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_sanksi_ketentuan?reqAidi=<?php // echo $reqId ?>&reqJenis=1')" class="badge badge-info pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Sanksi/Denda Keterlambatan </a>
              </small>
              <?php
              // } ?>
            </div>
          </div> -->

          <hr>
          <!-- <h4>Sanksi dan Denda Kelalaian
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
              //   $datasanksi->getField('KETERANGAN')
              //   }
              // } else { echo '. : : Tidak ada keterangan : : .';} ?>
              </div>
              <?php
            // if ($reqContractingStatusKontrakId >= '2' && $this->LEGAL != '1' && $this->USER_TYPE_ID != '20') { // Penyedia sudah approve, pemeriksa ?>
            <small style="margin: 10px;">
              <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_add_sanksi_ketentuan?reqAidi=<?= $reqId ?>&reqJenis=2')" class="badge badge-info pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Sanksi/Denda Kelalaian </a>
            </small>
            <?php
            // } ?>
            </div>
          </div> -->

          <div class="form-actions mt-3">
            <!-- <a href="kontrak/index/contracting_detail?reqId=<?= $reqId; ?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?></a> -->
            <?php
            if ($reqContractingStatusKontrakId >= '2' && $this->USER_TYPE_ID == '12') { // Penyedia sudah approve
              ?>
            <a href="main/loadUrl/report/kontrak_multi_pdf/?reqId=<?=$reqId?>&reqPaketId=<?= $reqPaketId ?>" target="_blank" class="<?= CLASS_BTN_INFO ?> mr-1"> <?= BTN_PRINT ?> Data Kontrak <?php // $reqJnsKontrakStr ?></a>
            <?php
            } ?>

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
      </div>
    </div>
  </div>
</div>
