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

$this->load->model(array("Contracting","Contractingrekanan","Contractingfile","Contractingjaminan"));

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

$sppbj->selectProses1(array("A.CONTRACTINGREKANANID" => $reqId));
$sppbj->firstRow();

$reqPaketId = $sppbj->getField('PAKET_ID') ?: '-';
$reqContractingRekananId = $sppbj->getField('CONTRACTINGREKANANID') ?: '-';
$reqCode = $sppbj->getField('CR_SPPBJ_CODE') ?: '-';
$reqTanggal = $sppbj->getField('CR_SPPBJ_TANGGAL') ?: '-';
$reqDirut = $sppbj->getField('CR_SPPBJ_DIRUT') ?: '-';
$reqDirutAlamat = $sppbj->getField('CR_SPPBJ_DIRUT_ALAMAT') ?: '-';
$reqDirutKota = $sppbj->getField('CR_SPPBJ_DIRUT_KOTA') ?: '-';
$reqDirutJabatan = $sppbj->getField('CR_SPPBJ_DIRUT_JABATAN') ?: '-';
$reqJaminanPelaksanaan = $sppbj->getField('CR_SPPBJ_JAMINAN_PELAKSANA') ?: '-';
$reqJaminanBesar = $sppbj->getField('CR_SPPBJ_JAMINAN_BESAR') ?: '-';
$reqJaminanJangkaDari = $sppbj->getField('CR_SPPBJ_JAMINAN_JANGKA_DARI') ?: '-';
$reqJaminanJangkaSampai = $sppbj->getField('CR_SPPBJ_JAMINAN_JANGKA_SAMPAI') ?: '-';
$reqJangkaMaksimal = $sppbj->getField('CR_SPPBJ_JAMINAN_MAKSIMAL_PENYERAHAN') ?: '-';
$reqJaminanNilai = $sppbj->getField('CR_SPPBJ_JAMINAN_NILAI') ?: '-';
$reqPejabatBerwenang = $sppbj->getField('CR_SPPBJ_PEJABAT_BERWENANG') ?: '-';
$reqNIP = $sppbj->getField('CR_SPPBJ_NIP') ?: '-';
$reqJabatan = $sppbj->getField('CR_SPPBJ_JABATAN') ?: '-';
$reqPPN = $sppbj->getField('CR_SPPBJ_PPN') ?: '-';
$reqPelaksanaanDari = $sppbj->getField('CR_SPPBJ_PELAKSANAAN_DARI') ?: '-';
$reqPelaksanaanSampai = $sppbj->getField('CR_SPPBJ_PELAKSANAAN_SAMPAI') ?: '-';
$reqCreatedBy = $sppbj->getField('CR_SPPBJ_CREATED_BY') ?: '-';
$reqCreatedDate = $sppbj->getField('CR_SPPBJ_CREATED_DATE') ?: '-';
$reqNilai = $sppbj->getField('CR_SPPBJ_NILAI') ?: '-';
$reqContractingStatusKontrakId = $sppbj->getField('CONTRACTINGSTATUSKONTRAKID') ?: '-';

?>

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/ui/jqueryui.css">
<script src="<?=base_url()?>assets/new/js/scripts/ui/jquery-ui/navigations.js"></script>

<script type="text/javascript">
function prosesKontrak(flow,aidi,m) {
  $.messager.confirm('Konfirmasi',m,function(r){
    if (r){
      $.getJSON("contracting_json/proseskontrak/?reqAidi="+aidi+"&flow="+flow,
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
          <h4 class="mb-2">Penetapan Surat Penunjukan Penyedia Barang/Jasa (SPPBJ)</h4>
        <?php
        $contractingjaminan = new Contractingjaminan();
        $contractingjaminan->selectByParams(array("CONTRACTINGREKANANID" => $reqId, "CREATED_BY" => $this->USER_LOGIN_ID));

        if ($reqJaminanPelaksanaan == '1' && $contractingjaminan->countRow() == 0 && $reqContractingStatusKontrakId=='1') {

          echo '<div class="alert alert-danger text-center" style="font-weight:bold; margin-top:1%">. : : Silahkan upload Jaminan Pelaksanaan: : .</div>'; ?>
          <a onclick="openAddFrame('main/loadUrlKontrak/kontrak/contracting_jaminan_file?reqId=<?= $reqId ?>&reqPaketId=<?= $paket_id ?>')" class="badge badge-primary pull-right mr-1 text-white mb-1"> <i class="fa fa-plus-circle"></i> Tambah Jaminan </a>
          <table class="table table-bordered">
            <thead>
              <tr class="backcolornew">
                <td>Nomor Jaminan</td>
                <td>Tanggal</td>
                <td width="100">File</td>
                <td width="120">Aksi</td>
              </tr>
              <?php 
              if ($contractingjaminan->countRow() > 0) {
                while($contractingjaminan->nextRow())
                { ?>
                  <tr>
                    <td><?= $contractingjaminan->getField("NOMOR") ?></td>
                    <td><?= getFormattedDateShort2(dateTimeToPageCheck($contractingjaminan->getField("TANGGAL_JAMINAN"))) ?></td>
                    <td><a href="uploads/kontrak/<?= $contractingjaminan->getField("FILE_JAMINAN") ?>" target="_blank" class="badge badge-primary"><span class="fa fa-download"> Download</span></a></td>
                    <td>
                      <a style="color: #fff;" onclick="openAddFrame('main/loadUrlKontrak/kontrak/contracting_jaminan_file?reqJaminanId=<?= $contractingjaminan->getField('CONTRACTING_JAMINAN_ID') ?>')" class="badge badge-info"><i class="fa fa-edit"></i></a>
                      <a style="color: #fff;" onClick="delJaminan('<?= $contractingjaminan->getField('CONTRACTING_JAMINAN_ID') ?>','Hapus data addendum dengan nomor <?= $contractingjaminan->getField('NOMOR') ?>?')" class="badge badge-danger"><i class="fa fa-trash"></i></a>
                    </td>
                  </tr>
              <?php 
                }
              } else {
                echo '<tr><td colspan="4" class="text-center">. : : Tidak ada data : : .</td></tr>';
              } ?>
            </thead>
          </table>

        <?php
        } else {  ?>

          <div class="row">

            <div class="col-md-12 mb-1">
              <?php echo $this->libkontrak->getStatusKontrakTeruskan($reqContractingStatusKontrakId,$reqId,$this->USER_TYPE_ID,$this->LEGAL,'SPPBJ'); ?>
              <?php 
              // if ($reqJaminanPelaksanaan == '1' && $contractingjaminan->countRow() >= 1 && $reqContractingStatusKontrakId=='1') 
              if ($reqJaminanPelaksanaan == '1' && $contractingjaminan->countRow() >= 1) 
              { ?>
              <table class="table table-bordered mt-1">
                <thead>
                  <tr class="backcolornew">
                    <td>Nomor Jaminan</td>
                    <td>Tanggal</td>
                    <td width="100">File</td>
                  </tr>
                  <?php 
                  if ($contractingjaminan->countRow() > 0) {
                    while($contractingjaminan->nextRow())
                    { ?>
                      <tr>
                        <td><?= $contractingjaminan->getField("NOMOR") ?></td>
                        <td><?= getFormattedDateShort2(dateTimeToPageCheck($contractingjaminan->getField("TANGGAL_JAMINAN"))) ?></td>
                        <td><a href="uploads/kontrak/<?= $contractingjaminan->getField("FILE_JAMINAN") ?>" target="_blank" class="badge badge-primary"><span class="fa fa-download"> Download</span></a></td> 
                      </tr>
                  <?php 
                    }
                  } else {
                    echo '<tr><td colspan="4" class="text-center">. : : Tidak ada data : : .</td></tr>';
                  } ?>
                </thead>
              </table>
              <?php 
              } ?>
            </div>

          </div> 


        <?php 
        }
        ?>

          <div class="form-actions">
            <?= $this->libkontrak->getInfoSPPBJ($reqId); ?> 
          </div>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <h5>Dokumen SPPBJ
                  <small style="font-size:0.9em">
                  <?php
                  if ($reqContractingStatusKontrakId == '1' && $reqJaminanPelaksanaan == '1') { // Jika ada Jaminan Pelaksanaan wajib di sertakan ?>
                    <a onclick="openAddFrame('main/loadUrlKontrak/kontrak/contracting_file_add?reqAidi=<?= $reqId ?>&reqProses=1&reqJenis=Jaminan Pelaksanaan&reqType=penyedia')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Dokumen </a>
                  <?php
                  } ?>
                  </small>
                </h5>
                <div class="table-responsive">
                  <table id="dokumenFileIdTable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                    <?= $this->libkontrak->getTableFilePenyedia($reqId," AND (A.FILE_JENIS = 'Dokumen SPPBJ' OR A.FILE_JENIS = 'Jaminan Pelaksanaan') AND FILE_PUBLISH_PENYEDIA = '1' ") ?>
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

<script type="text/javascript">
  function delJaminan(aidi,m) {
  $.messager.confirm('Konfirmasi',m,function(r){
    if (r){
      $.getJSON("contracting_json/delJaminan/?reqAidi="+aidi,
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
