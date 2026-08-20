<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();

$this->load->model("Rekanan");
$rekanan = new Rekanan();

$rekanan->selectByParamsCari(array("STATUS_VALIDASI" => "1"), -1, -1, "");
$reqFrom = $this->USER_NAMA.' '.SYSTEM_NAME_PT;

?>

<script type="text/javascript">
function backP() {
	window.location.href = "main/index/inbox_survei";
}

$(document).ready(function() {
	$(function(){
		$('#ff').form({
			url:'inbox_rfi_json/add',
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
	        // $.messager.alert('Info', data, 'info');
	          // $("#ff").trigger("reset");
	          alertSuccess2(data);
	          setTimeout(function () {
	            window.location.href = '<?= base_url('main/index/inbox_survei') ?>';
	          }, 1000);
	        hideLoad();
	      }
	    });

	});
});

function tambahRekananBlacklist(rekanan_id, nama, alamat, kota, npwp)
{
	$("#reqRekananId").val(rekanan_id);
	$("#reqTo").val(nama);
	$("#reqAlamat").val(alamat);
	$("#reqKota").val(kota);
	$("#reqNPWP").val(npwp);
}
</script>

<section id="backColor">
  <div class="row">

    <div class="col-md-12 col-sm-12">
      <div class="card border-bottom-primary">
        <div class="card-header">
          <h4 class="card-title"> Survey</h4>
          <div class="heading-elements" id="tombol">
	  		<a class="<?= CLASS_BTN_DANGER ?>" id="backPage" onclick="backP()"> <i class="fa fa-close"></i> Batal</a>
          </div>
        </div>
        <div class="row" id="header-inbox" >
		    <div class="form-group col-md-12" style="padding: 0 2.6%">
		    	<div class="card mb-1 border-blue border-darken-1" style="border-color: #f1f1f1 !important">
	              <div class="card-content">
	                <div class="p-1">
						<div class="form-group" style="padding: 1%">
          					<form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
								<div class="row">
									<div class="col-md-12 mb-1">
								      <label for="projectinput6" class="" id="labelNamaPerusahaan">Penerima</label>
										<select name="reqTo[]" class="select2 form-control easyui-validatebox" multiple="multiple" required>
											<?php
											while($rekanan->nextRow()) { ?>
												<option value="<?= $rekanan->getField('REKANAN_ID') ?>"><?= $rekanan->getField('NAMA') ?></option>
											<?php
											} ?>
										</select>
								    </div>
								    <!-- <div class="col-md-2 mb-1">
								      <label for="projectinput6" id="labelNamaPerusahaan">&nbsp;</label>
                					  <a title="Tambah" class="btn btn-primary text-white" style="width: 100%" id="btnAdd" onClick="openAdd('main/loadUrl/main/rekanan');"><span class="fa fa-plus"></span> Tambah</a>
								    </div> -->
								</div>
								<div class="row">
								    <div class="form-group col-md-12 mb-1">
								      <label for="projectinput6" id="labelNamaPerusahaan">Subject</label>
								      <input type="text" name="reqSubject" maxlength="255" accesskey="n" title="Subject harus diisi" class="form-control easyui-validatebox span7" value="<?=isset($reqSubject)?$reqSubject:''?>" id="reqSubject" required />
									</div>
								</div>
								<div class="row">
								    <div class="form-group col-md-12 mb-1">
								      <label for="projectinput6" id="labelNamaPerusahaan">Pengirim</label>
								      <input type="text" name="reqFrom" maxlength="255" accesskey="n" title="Pengirim harus diisi" class="form-control easyui-validatebox span7" value="<?=isset($reqFrom)?$reqFrom:''?>" id="reqFrom" required />
								    </div>
								</div>
								<div class="row">
								  <div class="form-group col-md-12 mb-1">
								    <label>Informasi Tambahan</label>
								    <textarea id="idGuestBookIsi" name="reqUraianKegiatan" class="textarea-tinymce" style="width:100%; height:350px"><?=isset($reqUraianKegiatan)?$reqUraianKegiatan:''?></textarea>
								  </div>
								</div>
								<div class="row">
									<div class="form-group col-md-12">
						                <label style="width: 100%">Upload lampiran <?= UPLOAD_PDF_2MB ?></label>
						                 <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30" maxlength="1" class="easyui-validatebox"  validType="fileType['pdf','zip']" />
						            </div>
						        </div>
						        <input type="hidden" name="reqInboxcategory" value="2">
								<!-- <div class="form-actions"> -->
								    <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>" name="reqSubmit" id="reqSubmit">
								      <i class="fa fa-send"></i> <?=translate("Kirim Survey", "Sent")?>
								    </button>
								<!-- </div> -->
							</form>
						</div>
			    	</div>
			      </div>
			    </div>
			</div>
        </div>
      </div>
    </div>
  </div>
</section>
