<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->libsession->cekSession();

$reqId  = $this->input->get("id") ? $this->input->get("id") : 0;

$this->load->model("Inbox");
$this->load->model("Rekanan");
$rekanan = new Rekanan();
$inboxTo = new Inbox();
$inboxComplainType = new Inbox();

$rekanan->selectByParams(array("A.REKANAN_ID"=>$this->REKANAN_ID),-1,-1);
$rekanan->firstRow();
$tempNama= $rekanan->getField("NAMA");

$inboxTo->selectInboxComplainSet(array(),-1,-1);
$inboxTo->firstRow();
$reqTo= $inboxTo->getField("ICS_TO");

$inboxComplainType->selectInboxComplainType(array(),-1,-1);

if ($reqId != 0) {
	$inbox = new Inbox();
	$inbox->selectByParams(array("A.INBOXID" => $reqId),-1,-1);
	$inbox->firstRow();
	$reqSubject = $inbox->getField("INBOX_SUBJECT");
} else {
	$reqSubject = '';
}

?>

<script type="text/javascript">
function backP() {
	window.location.href = "main/index/inbox_complain_penyedia";
}

$(document).ready(function() {
	$(function(){
		$('#ff').form({
			url:'inbox_rfi_json/addcomplain',
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
	            window.location.href = '<?= base_url('main/index/inbox_complain_penyedia') ?>';
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
          <h4 class="card-title"> Pertanyaan atau Permohonan</h4>
          <div class="heading-elements" id="tombol">
	  		<a class="<?= CLASS_BTN_DANGER ?>" id="backPage" onclick="backP()"><i class="fa fa-close"></i> Batal</a>
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
								    <div class="form-group col-md-12 mb-1">
								      <label for="projectinput6" id="labelNamaPerusahaan">Pengirim</label> <br>
								      <b><?= $tempNama ?></b>
								      <input type="hidden" name="reqTo" maxlength="255" accesskey="n" title="Pengirim harus diisi" class="form-control easyui-validatebox span7" value="<?=$reqTo?>" id="reqTo" required />
								      <input type="hidden" name="reqFrom" maxlength="255" accesskey="n" title="Pengirim harus diisi" class="form-control easyui-validatebox span7" value="<?=$tempNama?>" id="reqFrom" required />
								    </div>
								</div>
								<div class="row">
								    <div class="form-group col-md-3 mb-1">
								      <label for="projectinput6" id="labelNamaPerusahaan">Subject</label>
								      <select class="form-control easyui-validatebox span7" name="reqSubject" required>
								      	<option value="">-- Pilih ---</option>
								      	<?php
								      	while ($inboxComplainType->nextRow()) {
								      		if ($reqSubject == $inboxComplainType->getField("ICT_NAME")) {
								      			$selected = 'selected';
								      		} else {
								      			$selected = '';
								      		}
								      	 	echo '<option value="'.$inboxComplainType->getField("ICT_NAME").'" '.$selected.'>'.$inboxComplainType->getField("ICT_NAME").'</option>';
								      	 } ?>
								      </select>
									</div>
								</div>
								<div class="row">
								  <div class="form-group col-md-12 mb-1">
								    <label>Keterangan</label>
								    <textarea id="idGuestBookIsi" name="reqUraianKegiatan" class="textarea-tinymce" style="width:100%; height:350px"><?=isset($reqUraianKegiatan)?$reqUraianKegiatan:''?></textarea>
								  </div>
								</div>
								<div class="row">
									<div class="form-group col-md-12">
						                <label style="width: 100%">Upload lampiran <?= UPLOAD_PDF_ZIP_2MB ?></label>
						                 <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30" maxlength="1" class="form-control easyui-validatebox" required  validType="fileType['pdf']" />
						            </div>
						        </div>
						        <input type="hidden" name="reqInboxcategory" value="3">
								<!-- <div class="form-actions"> -->
									<input type="hidden" name="reqId" value="<?= $reqId ?>">
								    <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>" name="reqSubmit" id="reqSubmit">
								      <i class="fa fa-send"></i> <?=translate("Kirim", "Sent")?>
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
