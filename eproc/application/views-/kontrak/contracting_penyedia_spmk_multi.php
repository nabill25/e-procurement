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

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->model("Contracting");
$this->load->model("Contractingrekanan");
$this->load->model("Contractingfile");

$getMenu = new Contracting();
$kontrak = new Contracting();
$contractingrekanan = new Contractingrekanan();
$sppbj = new Contractingrekanan();

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

$spmk = new Contractingrekanan();
$spmk->selectSPMK(array("A.CONTRACTINGREKANANID" => $reqId, "A.REKANAN_ID" => $this->ID));
$spmk->firstRow();
$reqNomor = $spmk->getField('NOMOR') ?: ''; 
$reqSPMKDari = dateToPageCheck($spmk->getField('SPMK_DARI')) ?: '';
$reqSPMKSampai = dateToPageCheck($spmk->getField('SPMK_SAMPAI')) ?: '';
$reqKeterangan = $spmk->getField('KETERANGAN') ?: ''; 

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
</script>

<style type="text/css">
  small { font-weight: bold; font-size: 11.5px }
  sup { font-style: italic; color: red;}
  tr.backcolornew {
    background: #b11016 !important;
    color: #fff;
  }
</style>

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
          <h4 class="mb-2">Surat Perintah Mulai Kerja (SPMK)</h4>
          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <h5>Dokumen SPMK</h5>
                <div class="table-responsive">
                  <table id="dokumenFileIdTable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                    <?= $this->libkontrak->getTableFilePenyedia($reqId," AND A.FILE_JENIS = 'Dokumen SPMK' AND FILE_PUBLISH_PENYEDIA = '1' AND A.REKANAN_ID = $this->ID ") ?>
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
                    <small>Nomor SPMK</small> <br> <?= $reqNomor ?>
                  </td>
                  <td width="25%" colspan="2">
                    <small>Masa Pelaksanaan Pekerjaan</small> <br>  <?= getFormattedDateShort2($reqSPMKDari).' s/d '.getFormattedDateShort2($reqSPMKSampai) ?>
                  </td>
                </tr>
                <tr>
                  <td width="50%" colspan="4">
                    <small>Keterangan</small> <br> <?= $reqKeterangan ?>
                  </td> 
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
