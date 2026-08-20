<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$reqId = httpFilterRequest("reqId");

$this->libsession->cekSession();
$this->libsession->cekSessionKontrakPPK($reqId);

$this->load->library("kauth");  $userLogin = new kauth();

$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->model(array("Contracting","Contractingjaminan","Contractingrekanan","Contractingfile")); 

$getMenu = new Contracting();
$kontrak = new Contracting();
$sppbj = new Contractingrekanan();
$contractingrekanan = new Contractingrekanan();

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
$reqNilai = $sppbj->getField('CR_SPPBJ_NILAI') ?: 0;
$reqContractingStatusKontrakId = $sppbj->getField('CONTRACTINGSTATUSKONTRAKID') ?: '0'; 
$reqPICKontrak = $sppbj->getField('PIC_KONTRAK') ?: ''; 
$reqPICPengendali = $sppbj->getField('PIC_PENGENDALI') ?: ''; 
$reqPICPenyelesai = $sppbj->getField('PIC_PENYELESAI') ?: ''; 


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
          document.location.href = 'kontrak/index/contracting_persiapan';
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
    background: #103A6C !important;
    color: #fff;
  }
</style>

<?php 
if ($this->LEGAL != '1' && $reqLegalNomorPKS != '-' && $this->USER_TYPE_ID != '20') {
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
          <h4 class="mb-2">Penetapan Surat Penunjukan Penyedia Barang/Jasa (SPPBJ)</h4>

          <div class="row">
            <div class="col-md-12"> 
              <?php
              $contractingjaminan = new Contractingjaminan();
              $contractingjaminan->selectByParams(array("CONTRACTINGREKANANID" => $reqId));
              if ($reqJaminanPelaksanaan == '1' && $contractingjaminan->countRow() == 0 && $reqContractingStatusKontrakId=='2') {
                echo '<div class="alert alert-danger text-center" style="font-weight:bold; margin-top:1%">. : : Penyedia belum upload Jaminan Pelaksanaan: : .</div>';
              } else {  ?>
                <div class="row mb-1">
                  <div class="col-md-12">
                    <?php 
                      // if ($this->PENUNJUK_PIC == '1' || $this->USER_TYPE_ID == '20') { // Kasi yang bisa meneruskan ke kasubdit
                        echo $this->libkontrak->getStatusKontrakTeruskan($reqContractingStatusKontrakId,$reqId,$this->USER_TYPE_ID,$this->LEGAL,'SPPBJ'); 
                      // }

                        if ($reqContractingStatusKontrakId != '2' && $contractingjaminan->countRow() > 0) {  ?>

                          <table class="table table-bordered">
                            <thead>
                              <tr class="backcolornew">
                                <td>Nomor Jaminan</td>
                                <td>Tanggal Jaminan</td>
                                <td width="100">File <br>Jaminan</td>
                                <td width="100">Tanggal Konfirmasi ke Bank</td>
                                <td width="100">Tanggal Konfirmasi oleh Bank</td>
                                <td width="100">Bukti Konfirmasi <br> Keabsahan</td>
                                <td width="100">Status</td>
                              </tr>
                            </thead>
                            <tbody>
                              <?php 
                                while($contractingjaminan->nextRow())
                                { ?>
                                  <tr>
                                    <td><?= $contractingjaminan->getField("NOMOR")?></td>
                                    <td><?= getFormattedDateShort2(dateTimeToPageCheck($contractingjaminan->getField("TANGGAL_JAMINAN")))?></td>
                                    <td><a href="uploads/kontrak/<?= $contractingjaminan->getField("FILE_JAMINAN")?>" target="_blank" class="badge badge-primary"><span class="fa fa-download"> Download</span></a></td>
                                    <td><?= getFormattedDateShort2(dateTimeToPageCheck($contractingjaminan->getField("TANGGAL_KONFIRMASI_KEBANK")))?></td>
                                    <td><?= getFormattedDateShort2(dateTimeToPageCheck($contractingjaminan->getField("TANGGAL_KONFIRMASI_OLEH_BANK")))?></td>
                                    <?php 
                                    if ($contractingjaminan->getField("FILE_KONFIRMASI")) { ?>
                                      <td><a href="uploads/kontrak/<?= $contractingjaminan->getField("FILE_KONFIRMASI")?>" target="_blank" class="badge badge-primary"><span class="fa fa-download"> Download</span></a></td>
                                    <?php } else { ?>
                                      <td>-</td>
                                    <?php } 
                                      if ($contractingjaminan->getField("KONFIRMASI") == '1') { ?>
                                      <td><span class="badge badge-primary">Sesuai</span></td>
                                    <?php } else if ($contractingjaminan->getField("KONFIRMASI") == '2') { ?>
                                      <td><span class="badge badge-info">Cair</span></td>
                                    <?php } else { ?>
                                      <td><span class="badge badge-danger">Tidak Sesuai</span></td>
                                    <?php } ?>
                                  </tr>
                              <?php 
                                  } ?>
                            </tbody>
                          </table>
                    <?php 
                        }
                    ?>
                  </div>
                </div>
              <?php
              } ?>
            </div>
          </div>

          <div class="form-actions">
            <?= $this->libkontrak->getInfoSPPBJ($reqId); ?>

            <?php
            $arrA = array('}','{');
            if ($this->LEGAL != '1' && $this->USER_TYPE_ID != '20' && $this->USER_TYPE_ID != '28' && ($this->USER_TYPE_ID == '12' && $reqPICKontrak == $this->USER_LOGIN_ID)) { // Bukan Peng. Kontrak bagian Legal tapi User Pengguna, pemeriksa 
              if ($reqContractingStatusKontrakId == '0' || $reqContractingStatusKontrakId == '115' ) { ?>
              <a href="kontrak/index/contracting_persiapan_sppbj_edit?reqId=<?= $reqId ?>" class="<?= CLASS_BTN_PRIMARY ?>"> <i class="fa fa-pencil"></i> Edit Data SPPBJ </a>
              <?php 
              } ?>
            <!-- <a href="main/loadUrl/report/sppbj_multi_pdf?reqId=<?php // echo $reqId ?>&reqRekananId=<?php // echo str_replace($arrA,"",$paket_pemenang) ?>" class="btn <?php // echo CLASS_BTN_INFO ?>" target="_blank"> <?php // echo BTN_PRINT ?> </a> -->

            <?php
            } ?>
            <?php // echo $reqContractingStatusKontrakId.'****'; ?>

            <div class="card mt-2 border-blue border-darken-1">
              <div class="card-content">
                <div class="p-1">
                  <h5>Dokumen SPPBJ <small style="font-size:0.9em">
                  <?php
                  if ($this->LEGAL != '1') { // Bukan Peng. Kontrak bagian Legal tapi User Pengguna, pemeriksa ?>
                    <a onclick="openAdd('main/loadUrlKontrak/kontrak/contracting_file_add?reqAidi=<?= $reqId ?>&reqProses=1&reqJenis=Dokumen SPPBJ')" class="badge badge-primary pull-right mr-1 text-white"> <i class="fa fa-plus-circle"></i> Tambah Dokumen SPPBJ </a>
                  <?php
                  } ?>
                  </small> </h5>
                  <div class="table-responsive">
                    <table id="dokumenFileIdTable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
                      <?= $this->libkontrak->getTableFile($reqId," AND (A.FILE_JENIS = 'Dokumen SPPBJ' OR A.FILE_JENIS = 'Jaminan Pelaksanaan') ") ?>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <hr>

            <!-- <a href="kontrak/index/contracting_detail?reqId=<?= $reqId; ?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <i class="fa fa-arrow-left"></i> Kembali </a> -->


          </div>
        </div>
      </div>
    </div>
  </div>
</div>
