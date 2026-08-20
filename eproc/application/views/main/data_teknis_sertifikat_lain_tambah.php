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
$this->load->model("RekananSertifikat");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");


$rekanan = new Rekanan();
$rekanan_sertifikat = new RekananSertifikat();


$reqSertifikatId			= httpFilterRequest("reqSertifikatId") ?: '0';
$reqNama= httpFilterPost('reqNama');
$reqNomor= httpFilterPost('reqNomor');
$reqTanggalTerbit= httpFilterPost('reqTanggalTerbit');
$reqBerlakuHingga= httpFilterPost('reqBerlakuHingga');
$reqSubmit	= httpFilterRequest("reqSubmit");
$reqSimpan	= httpFilterPost("reqSimpan");
$reqBatal	= httpFilterPost("reqBatal");
$reqLinkFile= $_FILES['reqLinkFile'];
$reqLinkFileTemp = httpFilterPost("reqLinkFileTemp");
$reqLinkFileTempTipe = httpFilterPost("reqLinkFileTempTipe");
$reqLinkFileTempUkuran = httpFilterPost("reqLinkFileTempUkuran");

if ($reqSertifikatId != '0') {
	$rekanan_sertifikat->selectByParams(array("REKANAN_SERTIFIKAT_ID"=>$reqSertifikatId, "REKANAN_ID" => $this->ID),-1,-1);
	$rekanan_sertifikat->firstRow();
	$reqNama = $rekanan_sertifikat->getField("NAMA");
	$reqNomor = $rekanan_sertifikat->getField("NOMOR");
	$reqTanggalTerbit = dateToPageCheck($rekanan_sertifikat->getField("TANGGAL"));
	$reqBerlakuHingga = dateToPageCheck($rekanan_sertifikat->getField("BERLAKU"));
	$reqLinkFileTemp = $rekanan_sertifikat->getField("PATH_FILE");
	$reqLinkFileTempTipe = $rekanan_sertifikat->getField("TIPE");
	$reqLinkFileTempUkuran = $rekanan_sertifikat->getField("UKURAN");
	$reqLinkFileTempNama = $rekanan_sertifikat->getField("NAMA_FILE");
	$reqJenis = $rekanan_sertifikat->getField("JENIS");
	$reqJenisSertifikat = $rekanan_sertifikat->getField("REKANAN_JENIS_SERTIFIKAT_ID");
	$reqInstansiPemberi = $rekanan_sertifikat->getField("INSTANSI_PEMBERI");
}

if($reqSertifikatId=='0')
	$reqMode='insert';
else
	$reqMode='update';
?>
<script type="text/javascript">
$(document).ready(function() {

	$(function(){
		$('#ff').form({
			url:'rekanan_sertifikat_json/data_teknis_sertifikat_lain_ubah',
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
				    document.location.href = 'main/index/data_teknis_sertifikat_lain';
          }, 2000);
        }
			}
		});

	});

  $('#reqTanggalTerbit, #reqBerlakuHingga').datebox({
    editable: false
  });

});

<?php
if($reqJenis == '' || $reqJenis == 'Dokumen Teknis Lainnya') {
 ?>
$(document).ready(function() {
  $('#IdSertifikat').hide();
  $('#IdNamaDokumen').show();
});
<?php
} else {
?>
$(document).ready(function() {
  $('#IdSertifikat').show();
  $('#IdNamaDokumen').hide();
});
<?php
} ?>

$(document).ready(function() {
    $('input:radio[name=reqJenis]').change(function() {
      if (this.value == 'Dokumen Teknis Lainnya') {
        $('#IdSertifikat').hide();
        $('#IdNamaDokumen').show();
      }
      else if (this.value == 'Sertifikat') {
        $('#IdSertifikat').show();
        $('#IdNamaDokumen').hide();
      }
    });
  });

</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Dokumen Teknis Perusahaan</h4>
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

        <div class="row">
          <div class="form-group col-md-3 mb-2">
            <label style="width: 100%">Jenis Dokumen</label>
            <input type="radio" <?php if($reqJenis == '' || $reqJenis == 'Dokumen Teknis Lainnya') echo 'checked';?>  name="reqJenis" value="Dokumen Teknis Lainnya" required/> Dokumen Teknis Lainnya &nbsp;&nbsp;&nbsp;
            <input type="radio" <?php if($reqJenis == 'Sertifikat') echo 'checked';?> name="reqJenis" value="Sertifikat" required /> Sertifikat
          </div>
        </div>

        <div class="row" id="IdSertifikat">
          <div class="form-group col-md-9 mb-2">
            <label style="width: 100%">Jenis Sertifikat</label>
            <input type="text" name="reqJenisSertifikat" class="form-control easyui-combobox span1" data-options="valueField:'id',textField:'text',url:'rekanan_tipe_json/comboJenis',
                            onSelect : function(rec){
                              $('#txtNama').val(rec.text);
                            }" style="width: 600% !important" value="<?=$reqJenisSertifikat?>"/>
          </div>
        </div>

        <div class="row" id="IdNamaDokumen">
          <div class="form-group col-md-12 mb-2">
            <label>Nama</label>
      		  <input name="reqNama" onkeypress="return cancelSubmit(event)" title="Nama sertifikat harus diisi" class="form-control easyui-validatebox span4" type="text" id="txtNama" value="<?=$reqNama?>" size="50" maxlength="100" required />
          </div>
        </div>
        <div class="row">
          <div class="form-group col-md-4 mb-2">
            <label>Nomor</label>
            <input name="reqNomor" onkeypress="return cancelSubmit(event)" title="Nomor harus diisi" class="form-control easyui-validatebox span4" type="text" id="txtNomor" value="<?=$reqNomor?>" size="50" maxlength="100" required />
          </div>
					<div class="form-group col-md-8 mb-2">
            <label>Instansi Pemberi</label>
            <input name="reqInstansiPemberi" title="Instansi Pemberi harus diisi" class="form-control easyui-validatebox span4" type="text" id="txtNomor" value="<?=$reqInstansiPemberi?>" size="100" maxlength="100" required />
          </div>
        </div>
        <div class="row">
          <div class="form-group col-md-2 mb-2">
            <label style="width: 100%">Tanggal Terbit</label>
            <input type="text" name="reqTanggalTerbit" id="reqTanggalTerbit" title="Tanggal terbit saham harus diisi" class="form-control easyui-datebox span2" value="<?=$reqTanggalTerbit?>" required style="width: 200% !important" />
          </div>
          <div class="form-group col-md-2 mb-2">
            <label style="width: 100%">Tanggal Berakhir</label>
            <input type="text" name="reqBerlakuHingga" id="reqBerlakuHingga" title="Tanggal terbit saham harus diisi" class="form-control easyui-datebox span2" value="<?=$reqBerlakuHingga?>" style="width: 200% !important" />
          </div>
          <div class="form-group col-md-8 mb-2">
            <label style="width: 100%">File Dokumen Teknis <?= UPLOAD_PDF_2MB ?></label>
            <input type="hidden" name="reqLinkFileTemp" value="<?=$reqLinkFileTemp?>" />
            <input type="hidden" name="reqLinkFileTempTipe" value="<?=$reqLinkFileTempTipe?>" />
            <input type="hidden" name="reqLinkFileTempUkuran" value="<?=$reqLinkFileTempUkuran?>" />
            <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30" <?php if($reqLinkFileTemp == "") { ?> required <?php } ?> class="easyui-validatebox span4"  validType="fileType['pdf']" />
            <input type="hidden" name="reqLinkFileTempNama" value="<?=$reqLinkFileTempNama?>" />
            <?php
            // if ($reqLinkFileTempNama) {
              if ($reqLinkFileTempNama && file_exists('uploads/sertifikat/'.$reqLinkFileTemp)) {
               echo '<br><a target="_blank" href="'.base_url('uploads/sertifikat/').$reqLinkFileTemp.'" class="badge badge-primary"><span class="fa fa-download" style="margin-top:1%"></span> Download file</a>';
             } ?>
          </div>
        </div>
        <div class="form-actions">
          <input type="hidden" name="reqSertifikatId" value="<?=$reqSertifikatId?>" />
          <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
          <a href="main/index/data_teknis_sertifikat_lain" class="<?= CLASS_BTN_DANGER ?> mr-1"><?= BTN_KEMBALI ?></a>
          <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
        </div>

        </div>
      </div>
      </form>
    </div>
  </div>
</div>
