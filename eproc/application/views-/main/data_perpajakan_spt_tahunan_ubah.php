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
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$this->load->model("Rekanan");
$this->load->model("RekananPajak");

/* create objects */
$rekanan = new Rekanan();
$rekanan_spt	= new RekananPajak(); // tipe 1

$reqId = $this->ID;

$reqRekananPajakId= $this->input->get("reqRekananPajakId") ?: '0';

if ($reqRekananPajakId != '0') {
	$rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
	$rekanan->firstRow();

	$rekanan_spt->selectByParams(array("REKANAN_PAJAK_ID"=>$reqRekananPajakId, "REKANAN_ID"=>$this->ID), -1, -1);
	$rekanan_spt->firstRow();
	$reqTahun = $rekanan_spt->getField('TAHUN');
	$reqNomor = $rekanan_spt->getField('NOMOR');
	$reqLinkFileTemp= $rekanan_spt->getField("PATH_FILE");
	$reqLinkFileTempNama= $rekanan_spt->getField("NAMA_FILE");
}

$reqTanggal = dateToPageCheck($rekanan_spt->getField('TANGGAL'));
$reqRekananPajakId = $reqRekananPajakId;

if($reqRekananPajakId == '0')
	$reqMode='insert';
else
	$reqMode='update';
?>
<script type="text/javascript">
$(document).ready(function() {

	$(function(){
		$('#ff').form({
			url:'rekanan_pajak_json/data_administrasi_keuangan_spt_ubah',
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
				      document.location.href = 'main/index/data_perpajakan_spt_tahunan/?reqId=<?=$reqId?>';
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
        <h4 class="card-title text-white">SPT Tahunan </h4>
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
            <div class="form-group col-md-2 mb-2">
              <label>Tahun</label>
          		<input type="hidden" name="reqTahunPajak_last" value="<?=$reqTahun?>" />
          		<input title="Tahun harus diisi" class="form-control easyui-validatebox span2"  required type="text" name="reqTahun" id="reqTahun" value="<?=$reqTahun?>"size="1" maxlength="4" onkeypress="return isNumberKey(event)" />
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>Nomor Tanda Terima Elektronik</label>
              <input title="Nomor SPT harus diisi" class="form-control easyui-validatebox span4"  required type="text" name="reqNomor" id="reqNomor" value="<?=$reqNomor?>"/>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label style="width: 100%">Tanggal Penyampaian</label>
    		      <input title="Tanggal harus diisi" class="form-control easyui-datebox span2"  required type="text" name="reqTanggal" id="reqTanggal" value="<?=$reqTanggal?>" style="width: 200% !important" />
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label>File SPT dan Bukti Lapor </label><br>
              <input type="hidden" name="reqLinkFileTemp" value="<?=$reqLinkFileTemp?>" />
              <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30" <?php if($reqLinkFileTemp == "") { ?> required <?php } ?> class="easyui-validatebox"  validType="fileType['pdf']" />
              <input type="hidden" name="reqLinkFileTempNama" value="<?=$reqLinkFileTempNama?>">
              <?php
              if ($reqLinkFileTempNama) {
                 echo "File :".$reqLinkFileTempNama;
               } ?>
               <br><?= UPLOAD_PDF_2MB ?>
            </div>
          </div>
          <div class="form-actions">
            <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
            <input type="hidden" name="reqRekananPajakId" value="<?=$reqRekananPajakId?>" />
            <a href="main/index/data_perpajakan_spt_tahunan" class="<?= CLASS_BTN_DANGER ?> mr-1"><?= BTN_KEMBALI ?></a>
            <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
          </div>
        </div>
      </div>
      </form>
    </div>
  </div>
</div>
