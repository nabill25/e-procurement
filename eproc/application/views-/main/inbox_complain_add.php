<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->libsession->cekSession();

$this->load->model("Inbox");
$id  = $this->input->get("id");

$dataInbox    		= new Inbox();
$dataInboxContent   = new Inbox();
$arrStatement = array("A.INBOXID" => $id, "A.PARENT" => 0, "A.INBOXCATEGORYID" => '3');
$dataInbox->selectByPenerima($arrStatement, -1, -1);
$dataInbox->firstRow();

$inbox_content_text = '';
$inboxid = $dataInbox->getField("INBOXID");
$inbox_subject = 'Reply - '.$dataInbox->getField("INBOX_SUBJECT").' '.$dataInbox->getField("INBOX_FROM");
$ic_name = $dataInbox->getField("IC_NAME");
$inbox_content = $dataInbox->getField("INBOX_CONTENT");
$inbox_from = $dataInbox->getField("INBOX_FROM");
$inbox_to = $dataInbox->getField("INBOX_TO");
$penerima_str = $dataInbox->getField("PENERIMA_STR");
$inbox_status = $dataInbox->getField("STATUS");
$inbox_parent = $dataInbox->getField("PARENT");
$inbox_file = $dataInbox->getField("INBOX_FILE");
$inbox_file_nama = $dataInbox->getField("INBOX_FILE_NAMA");
$inbox_file_size = $dataInbox->getField("INBOX_FILE_SIZE");
$inbox_file_type = $dataInbox->getField("INBOX_FILE_TYPE");
$created_by = $dataInbox->getField("CREATED_BY");
$created_by_str = $dataInbox->getField("CREATED_BY_STR");
$created_date = $dataInbox->getField("CREATED_DATE");
$reqToo = $this->USER_NAMA.' '.SYSTEM_NAME_PT;

$dataInboxContent->selectByPenerima(array("A.PARENT" => $id, "A.INBOXCATEGORYID" => '3'), -1, -1);
while ($dataInboxContent->nextRow()) {
	$inbox_content2 = $dataInboxContent->getField("INBOX_CONTENT");
	$inbox_content_text .= $dataInboxContent->getField("INBOX_FROM").'<br><small>'.getFormattedDate($dataInboxContent->getField("CREATED_DATE")).'</small><br>'.$dataInboxContent->getField("INBOX_CONTENT").'<hr>';
}
$inbox_content_text .= $inbox_from.'<br><small>'.getFormattedDate($created_date).'</small><br>'.$inbox_content;

?>

<script type="text/javascript">
function backP() {
	window.location.href = "main/index/inbox_complain";
}

$(document).ready(function() {
	$(function(){
		$('#ff').form({
			url:'inbox_rfi_json/replaycomplain',
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
				window.location.href = '<?= base_url('main/index/inbox_complain') ?>';
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
          <h4 class="card-title"> Jawab </h4>
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
								      <label for="projectinput6" class="" id="labelNamaPerusahaan">Penerima</label>  <br>
								      <?= '<b>'.$inbox_from.'</b>' ?>
								      <input type="hidden" name="reqTo" maxlength="255" accesskey="n" title="Subject harus diisi" class="form-control easyui-validatebox span7" value="<?=isset($created_by)?$created_by:''?>" id="reqTo" required />
								    </div>
								</div>
								<div class="row">
								    <div class="form-group col-md-12 mb-1">
								      <label for="projectinput6" id="labelNamaPerusahaan">Subject</label>
								      <input type="text" name="reqSubject" maxlength="255" accesskey="n" title="Subject harus diisi" class="form-control easyui-validatebox span7" value="<?=isset($inbox_subject)?$inbox_subject:''?>" id="reqSubject" required />
									</div>
								</div>
								<div class="row">
								    <div class="form-group col-md-12 mb-1">
								      <label for="projectinput6" id="labelNamaPerusahaan">Pengirim</label>
								      <input type="text" name="reqFrom" maxlength="255" accesskey="n" title="Pengirim harus diisi" class="form-control easyui-validatebox span7" value="<?=isset($reqToo)?$reqToo:''?>" id="reqFrom" required />
								    </div>
								</div>
								<div class="row">
								  <div class="form-group col-md-12 mb-1">
								    <label>Request For Information</label>
								    <textarea id="idGuestBookIsi" name="reqUraianKegiatan" class="textarea-tinymce" style="width:100%; height:350px"><?=isset($reqUraianKegiatan)?$reqUraianKegiatan:''?>
								    	<br><br><hr>
								    	<?= $inbox_content_text ?>
								    </textarea>
								  </div>
								</div>
								<div class="row">
									<div class="form-group col-md-12">
						                <label style="width: 100%">Upload lampiran <small>(Format file .pdf .zip & Maksimal ukuran file 2MB) </small></label>
						                 <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30" maxlength="1" class="easyui-validatebox"  validType="fileType['pdf','zip']" />
						            </div>
						        </div>
						        <input type="hidden" name="reqInboxcategory" value="3">
								<!-- <div class="form-actions"> -->
									<input type="hidden" name="reqId" value="<?= $inboxid ?>">
								    <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>" name="reqSubmit" id="reqSubmit">
								      <i class="fa fa-send"></i> <?=translate("Kirim Jabawan", "Sent")?>
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
