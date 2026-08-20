<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->libsession->cekSession('blockpenyedia');

// cek allowed url
if ($this->libsession->cekUrl($this->uri->segment(3, ""))) {} else { redirect(base_url()); }

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("Rekanan");
$this->load->model("RekananAkta");

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$rekanan_akta = new RekananAkta();
$rekanan = new Rekanan();

$reqId			= httpFilterPost("reqId");
$reqAktaType = httpFilterRequest("reqAktaType");
$reqNamaNotaris			= httpFilterPost("reqNamaNotaris");
$reqTanggal			= httpFilterPost("reqTanggal");
$reqNomorAkta     = httpFilterPost("reqNomorAkta");
$reqNomorKemenkumham			= httpFilterPost("reqNomorKemenkumham");
$reqRekananAktaId= httpFilterPost('reqRekananAktaId');
$reqSubmit= httpFilterPost('reqSubmit');
$reqLinkFile= $_FILES['reqLinkFile'];
$reqLinkFileTemp = httpFilterPost("reqLinkFileTemp");
$reqLinkFileTempTipe = httpFilterPost("reqLinkFileTempTipe");
$reqLinkFileTempUkuran = httpFilterPost("reqLinkFileTempUkuran");
$reqId = $this->ID;

$FILE_DIR = "uploads/landasan_hukum/";

$rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
$rekanan->firstRow();

if($reqAktaType == 3)
{
	$reqNomor = $rekanan->getField("SURAT_KUASA");
	$reqTanggal = dateToPageCheck($rekanan->getField("SURAT_KUASA_TANGGAL"));
	$reqNotaris = $rekanan->getField("SURAT_KUASA_NOTARIS");
}
else
{
	$rekanan_akta->selectByParams(array("REKANAN_ID"=>$this->ID, "AKTA_TYPE_ID"=>$reqAktaType),-1,-1,' ORDER BY REKANAN_AKTA_ID DESC LIMIT 1');
	$rekanan_akta->firstRow();
	if($rekanan_akta->getField("NOMOR") != '')
	{
    $reqNomor = $rekanan_akta->getField("NOMOR");
		$reqNomorKemenkumham = $rekanan_akta->getField("NOMOR_KEMENKUMHAM");
		$reqTanggal = dateToPageCheck($rekanan_akta->getField("TANGGAL"));
		$reqNotaris = $rekanan_akta->getField("NOTARIS");
		$reqRekananAktaId = $rekanan_akta->getField("REKANAN_AKTA_ID");
		$reqLinkFileTemp= $rekanan_akta->getField("PATH_FILE");
		$reqLinkFileTempTipe= $rekanan_akta->getField("TIPE");
		$reqLinkFileTempUkuran= $rekanan_akta->getField("UKURAN");
		$reqLinkFileTempNama= $rekanan_akta->getField("NAMA_FILE");
	}
	else
	{
    $reqNomor = $reqNomorAkta;
	  $reqNomorKemenkumham = $reqNomorKemenkumham;
    $reqTanggal = $reqTanggal;
		$reqNotaris = $reqNamaNotaris;
	}
}

$reqMode = "update";
?>

<script type="text/javascript">
$(document).ready(function() {

	$(function(){
		$('#ff').form({
			url:'rekanan_akta_json/data_administrasi_landasan_hukum_ubah',
			onSubmit:function(){
				var v=$(this).form('validate');
        if(v) {
            showLoad();
            return v;
        } else {
            hideLoad();
            return false;
        }
			},
			success:function(data){
        if (data == 'Data Gagal Tersimpan') {
          alertError3(data);
        } else {
          alertSuccess2('Data berhasil disimpan');
          setTimeout(function() {
				    document.location.href = 'main/index/data_administrasi_landasan_hukum';
          }, 2000);
        }
			}
		});

	});

  $('#reqTanggal').datebox({
    editable: false
  });

});
</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white"><?php if($reqAktaType == 1) { ?>Akta Pendirian<?php } else { ?>Akta Perubahan Terakhir<?php } ?>  </h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <div class="form-group col-md-12 mb-2">
            <label>Nomor Akta</label>
  			     <input type="text" name="reqNomorAkta" id="reqNomorAkta" title="Nomor Akta harus diisi" class="form-control easyui-validatebox span4" value="<?=$reqNomor?>" required />
          </div>
          <div class="form-group col-md-12 mb-2">
            <label>Nomor SK KEMENKUMHAM</label>
             <input type="text" name="reqNomorKemenkumham" id="reqNomorKemenkumham" title="Nomor Kemenkumham harus diisi" class="form-control easyui-validatebox span4" value="<?=$reqNomorKemenkumham?>" required />
          </div>
          <div class="form-group col-md-12 mb-2">
            <label>Nama Notaris</label>
            <input type="text" name="reqNamaNotaris" id="reqNamaNotaris" title="Nama Notaris harus diisi" class="form-control easyui-validatebox span4" value="<?=$reqNotaris?>" required />
          </div>
          <div class="form-group col-md-12 mb-2">
            <label style="width: 100%">Tanggal</label>
          	<input type="text" style="width: 200% !important" name="reqTanggal" id="reqTanggal" title="Tanggal harus diisi" class="form-control easyui-datebox span2" value="<?=$reqTanggal?>" required />
          </div>
          <div class="form-group col-md-12 mb-2">
            <label style="width: 100%">File  <?php if($reqAktaType == 1) { ?>Akta Pendirian<?php } else { ?>Akta Perubahan Terakhir<?php } ?> dan SK KEMENKUMHAM <br><?= UPLOAD_PDF_2MB ?></label>
             <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30" <?php if($reqLinkFileTemp == "") { ?> required <?php } ?> class="easyui-validatebox"  validType="fileType['pdf']" />
             <input type="hidden" name="reqLinkFileTemp" value="<?=$reqLinkFileTemp?>">
             <input type="hidden" name="reqLinkFileTempTipe" value="<?=$reqLinkFileTempTipe?>">
             <input type="hidden" name="reqLinkFileTempUkuran" value="<?=$reqLinkFileTempUkuran?>">
             <input type="hidden" name="reqLinkFileTempNama" value="<?=$reqLinkFileTempNama?>">
          </div>

          <div class="form-actions">
            <input type="hidden" name="reqAktaType" value="<?=$reqAktaType?>" />
            <input type="hidden" name="reqId" value="<?=$reqId?>" />
            <input type="hidden" name="reqRekananAktaId" value="<?=$reqRekananAktaId?>"/>
            <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
            <a href="main/index/data_administrasi_landasan_hukum" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a>
            <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
          </div>
        </div>
      </div>
      </form>
    </div>
  </div>
</div>
