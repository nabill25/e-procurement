<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$reqId = httpFilterRequest("reqId");

$this->libsession->cekSessionKontrakPPK($reqId);

$this->load->library("kauth");  $userLogin = new kauth();

$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Contracting");
$this->load->model("Contractingrekanan");
$this->load->model("Contractingfile");
$this->load->model(array("Paketpemenang","Rekanan"));

$getMenu = new Contracting();
$kontrak = new Contracting();
$contractingrekanan = new Contractingrekanan();
$sppbj = new Contractingrekanan();
$getpaket_pemenang = new Paketpemenang();

$contractingrekanan->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
$contractingrekanan->firstRow();
$contractingprosesid = $contractingrekanan->getField('CONTRACTINGPROSESID');

$getMenu->selectMenuProses(array("A.CONTRACTINGPROSESID" => $contractingprosesid));
$getMenu->firstRow();
$cp_name = $getMenu->getField('CP_NAME');
$cp_link = $getMenu->getField('CP_LINK');

$kontrak->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
$kontrak->firstRow();
$kontrak_nama = $kontrak->getField('NAMA');
$kontrak_nilai = $kontrak->getField('NILAI');
$kontrak_paket_metode_lelang = $kontrak->getField('PAKET_METODE_LELANG');
$paket_pemenang = $kontrak->getField('PEMENANG');
$paket_id = $kontrak->getField('PAKET_ID');

$paketInfo->getPaket($paket_id);
$bidding = $paketInfo->bidding;
$reqMultiPemenang = $paketInfo->multi_pemenang;

if ($reqMultiPemenang == '0') {
  $getpaket_pemenang->selectByParams(array("PAKET_ID" => $paket_id, "PERINGKAT" => '1'), -1, -1); 
} else {
  // echo "Multi Pemanang";
  $getpaket_pemenang->selectByParams(array("PAKET_ID" => $paket_id), -1, -1);
  $totalPemenang = $getpaket_pemenang->countRow();
}

  $sppbj->selectProses1(array("A.CONTRACTINGREKANANID" => $reqId));
  $totalStatus2 = 0;
  $totalStatus1 = 0;
  $totalStatus0 = 0;
  while($sppbj->nextRow())
  {
    if ($sppbj->getField('CONTRACTINGSTATUSKONTRAKID') > '2') {
      $totalStatus3++;
    }

    if ($sppbj->getField('CONTRACTINGSTATUSKONTRAKID') == '2') {
      $totalStatus2++;
    }

    if ($sppbj->getField('CONTRACTINGSTATUSKONTRAKID') == '1') {
      $totalStatus1++;
    }

    if ($sppbj->getField('CONTRACTINGSTATUSKONTRAKID') == '0') {
      $totalStatus0++;
    }


  }
  // echo $totalPemenang.'-'.$totalStatus2.'-'.$totalStatus1.'-'.$totalStatus0;

  if ($totalStatus0 == 0) {
    if ($totalStatus1 == 0) {
      if ($totalStatus2 == 0) {
      } else {
        $reqContractingStatusKontrakId = 2; // Status SPPBJ Penyedia Setuju
      }
    } else {
      $reqContractingStatusKontrakId = 1; // Status SPPBJ Persetujuan Penyedia
    }
  } else {
    $reqContractingStatusKontrakId = 0; // Status SPPBJ dibuat
  }

  if ($totalStatus3 > 0) {
    $reqContractingStatusKontrakId = $totalStatus3;
  }

// $sppbj->firstRow();

// $reqPaketId = $sppbj->getField('PAKET_ID') ?: '-';
// $reqContractingRekananId = $sppbj->getField('CONTRACTINGREKANANID') ?: '-';
// $reqCode = $sppbj->getField('CR_SPPBJ_CODE') ?: '-';
// $reqTanggal = $sppbj->getField('CR_SPPBJ_TANGGAL') ?: '-';
// $reqDirut = $sppbj->getField('CR_SPPBJ_DIRUT') ?: '-';
// $reqDirutAlamat = $sppbj->getField('CR_SPPBJ_DIRUT_ALAMAT') ?: '-';
// $reqDirutKota = $sppbj->getField('NAMA_KOTA') ?: '-';
// $reqDirutJabatan = $sppbj->getField('CR_SPPBJ_DIRUT_JABATAN') ?: '-';
// $reqJaminanPelaksanaan = $sppbj->getField('CR_SPPBJ_JAMINAN_PELAKSANA') ?: '-';
// $reqJaminanBesar = $sppbj->getField('CR_SPPBJ_JAMINAN_BESAR') ?: '-';
// $reqJaminanJangkaDari = $sppbj->getField('CR_SPPBJ_JAMINAN_JANGKA_DARI') ?: '-';
// $reqJaminanJangkaSampai = $sppbj->getField('CR_SPPBJ_JAMINAN_JANGKA_SAMPAI') ?: '-';
// $reqJaminanNilai = $sppbj->getField('CR_SPPBJ_JAMINAN_NILAI') ?: '-';
// $reqPejabatBerwenang = $sppbj->getField('CR_SPPBJ_PEJABAT_BERWENANG') ?: '-';
// $reqNIP = $sppbj->getField('CR_SPPBJ_NIP') ?: '-';
// $reqJabatan = $sppbj->getField('CR_SPPBJ_JABATAN') ?: '-';
// $reqPPN = $sppbj->getField('CR_SPPBJ_PPN') ?: '-';
// $reqPelaksanaanDari = $sppbj->getField('CR_SPPBJ_PELAKSANAAN_DARI') ?: '-';
// $reqPelaksanaanSampai = $sppbj->getField('CR_SPPBJ_PELAKSANAAN_SAMPAI') ?: '-';
// $reqCreatedBy = $sppbj->getField('CR_SPPBJ_CREATED_BY') ?: '-';
// $reqCreatedDate = $sppbj->getField('CR_SPPBJ_CREATED_DATE') ?: '-';
// $reqNilai = $sppbj->getField('CR_SPPBJ_NILAI') ?: 0;
// $reqContractingStatusKontrakId = $sppbj->getField('CONTRACTINGSTATUSKONTRAKID') ?: '-';

?>

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/ui/jqueryui.css">
<script src="<?=base_url()?>assets/new/js/scripts/ui/jquery-ui/navigations.js"></script>

<script type="text/javascript">
// flow=reqContractingStatusKontrakId, aidi=reqContractingRekananProses1Id
function prosesKontrak(flow,aidi,m) {
  $.messager.confirm('Konfirmasi',m,function(r){
    if (r){
      $.getJSON("contracting_json/proseskontrakmulti/?reqAidi="+aidi+"&flow="+flow,
      function(data){
        alertSuccess2(data.PESAN);
        setTimeout(function() {
            location.reload();
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

<style type="text/css">
  small { font-weight: bold; font-size: 11.5px }
  sup { font-style: italic; color: red;}
  tr.backcolornew {
    background: #b11016 !important;
    color: #fff;
  }
  
</style>

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
          <h4 class="mb-2">Penetapan Surat Penunjukan Penyedia Barang/Jasa (SPPBJ)</h4>
 
          <div class="row mb-1">
            <div class="col-md-12">
              <?php echo $this->libkontrak->getStatusKontrakTeruskanMulti($reqContractingStatusKontrakId,$reqId,$this->USER_TYPE_ID,$this->LEGAL,'SPPBJ'); ?>
            </div>
          </div>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <h5>Dokumen SPPBJ <small style="font-size:0.7em">
                <?php
                if ($this->LEGAL != '1' && $this->USER_TYPE_ID != '20') { // Bukan Peng. Kontrak bagian Legal tapi User Pengguna, pemeriksa ?>
                  <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_file_add_multi?reqAidi=<?= $reqId ?>&reqProses=1&reqJenis=Dokumen SPPBJ')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Dokumen SPPBJ </a>
                <?php
                } ?>
                </small> </h5>
                <div class="table-responsive">
                  <table id="dokumenFileIdTable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                    <?= $this->libkontrak->getTableFileMulti($reqId," AND (A.FILE_JENIS = 'Dokumen SPPBJ' OR A.FILE_JENIS = 'Jaminan Pelaksanaan') ") ?>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="form-actions">
            <table class="table table-bordered table-hover">
              <tbody>
                <tr>
                  <th width="2%">No</th>
                  <th>No. SPPBJ</th>
                  <th>Penyedia</th>
                  <th width="5%">Aksi</th>
                </tr>
                <?php
                $no = 1;
                while($getpaket_pemenang->nextRow())
                {
                  $contractingrekananSPPBJ = new Contractingrekanan();
                  $contractingrekananSPPBJ->selectViewSPPBJ(array("A.PAKET_ID" => $paket_id, "A.REKANAN_ID" => $getpaket_pemenang->getField("REKANAN_ID")));
                  $contractingrekananSPPBJ->firstRow();
                  ?>
                  <tr>
                    <td width="2%"><?= $no ?></td>
                    <td>
                      <?= $contractingrekananSPPBJ->getField("CR_SPPBJ_CODE") ?: '<span class="badge badge-danger">Belum di buat</span>'; ?>
                    </td>
                    <td><?= $getpaket_pemenang->getField("NAMA"); ?></td>
                    <td width="5%">
                      <?php
                      if ($this->LEGAL != '1' && $this->USER_TYPE_ID != '20') { // Bukan Peng. Kontrak bagian Legal tapi User Pengguna, pemeriksa
                        if ($contractingrekananSPPBJ->getField("CR_SPPBJ_CODE")) { ?>
                          <a href="kontrak/index/contracting_persiapan_sppbj_multi_edit?reqId=<?= $contractingrekananSPPBJ->getField("CONTRACTINGREKANANID") ?>&reqRekananId=<?= $getpaket_pemenang->getField("REKANAN_ID") ?>" class="btn-sm <?= CLASS_BTN_PRIMARY ?> mr-1"> <span class="fa fa-pencil"></span> </a>

                          <!-- <a href="main/loadUrl/report/sppbj_multi_pdf?reqId=<?php // echo $contractingrekananSPPBJ->getField("CONTRACTINGREKANANID") ?>&reqRekananId=<?php // echo $getpaket_pemenang->getField("REKANAN_ID") ?>" class="btn-sm <?php // echo CLASS_BTN_INFO ?>" target="_blank"> <span class="fa fa-print"></span> </a> -->
                        <?php
                        } else { ?>
                          <?= $contractingrekananSPPBJ->getField("CONTRACTINGREKANANID") ?>
                          <a href="kontrak/index/contracting_surat_perjanjian_multi?reqId=<?= $paket_id ?>&jnskontrak=3&tahun=<?= $getTahun ?>&id=<?= $reqId ?>" class="btn-sm <?= CLASS_BTN_DANGER ?>"> <?= BTN_TAMBAH ?> </a>
                      <?php
                        }
                      } else { ?>
                        <!-- <a href="main/loadUrl/report/sppbj_multi_pdf?reqId=<?php // echo $contractingrekananSPPBJ->getField("CONTRACTINGREKANANID") ?>&reqRekananId=<?php // echo $getpaket_pemenang->getField("REKANAN_ID") ?>" class="btn-sm <?php // echo CLASS_BTN_INFO ?>" target="_blank"> <span class="fa fa-print"></span> </a> -->
                      <?php 
                      } ?>
                    </td>
                  </tr>
                  <!--   -->
                <?php
                $no++;
                } ?>
              </tbody>
            </table>
          </div>

          <div class="form-actions">
            <!-- <a href="kontrak/index/contracting_detail?reqId=<?= $reqId; ?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <i class="fa fa-arrow-left"></i> Kembali </a> -->
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
