<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$reqMode = httpFilterRequest("reqMode");
$reqId = httpFilterRequest("reqId");
$reqCetak= httpFilterPost('reqCetak');
$reqNamaDokumen= httpFilterPost('reqNamaDokumen');
$reqKeterangan= httpFilterPost('reqKeterangan');
$reqLinkFile= $_FILES['reqLinkFile'];
$reqBayar= httpFilterPost('reqBayar');
$reqDokumenId = httpFilterGet('reqDokumenId');
$submitSimpan= httpFilterPost('submitSimpan');

$this->libsession->cekSession($reqId);   

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketAanwijzing");
$this->load->library("FileHandler");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket_aanwijzing = new PaketAanwijzing();
$file = new FileHandler();


$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqParent = $this->input->get('reqParent') ? $this->input->get('reqParent') : '';

$paket_aanwijzing->selectByParams(array("PAKET_ID" => $reqId, "paket_aanwijzing_id" => $reqParent, "PARENT_ID" => 0 ));
?>
<script type="text/javascript">
$(document).ready(function() {

	$(function(){
		$('#ff').form({
			url:'aanwijzing_chat_json/dokumen_aanwijzing_ba',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				// alert(data);return false;
				document.location.href = 'main/index/aanwijzing_chat_ba/?reqId=<?=$reqId?>';
			}
		});

	});

});
</script>


<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Upload BA Aanwijzing 
        </h4>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable"> 

            <form id="ff" class="easyui-form " method="post" novalidate enctype="multipart/form-data"> 

                <div class="row">
                  <div class="form-group col-md-12">
                    <label>Keterangan</label>
                    <textarea name="reqKeterangan" id="reqKeterangan" cols="45" rows="5" class="easyui-validatebox form-control" required></textarea>
                  </div> 
                </div>

                <div class="row">
                  <div class="form-group col-md-12">
                    <label>Lampiran <small> <br>Format file .pdf & Maksimal ukuran file 2MB </small></label>
                    <input type="file" class="form-control" name="reqLinkFile" id="reqLinkFilePDF" required validType="fileType['pdf', 'jpg']" />
                  </div> 
                </div>

                <div class="form-actions">
                  <input type="hidden" name="reqId" value="<?=$reqId?>" />
                  <input type="hidden" name="reqPaketDokumenId" value="0" />
                  <input type="hidden" name="reqRekananUserId" value="0" />
                  <a href="main/index/aanwijzing_chat/?reqId=<?=$reqId?>" class="btn btn-danger"><i class="fa fa-arrow-left"></i> Kembali</a>
                  <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Upload</button>
                </div> 
            </form>

        </div>
      </div>
    </div>
  </div> 
</div>  

<div class="span12">
 <div class="card">
  <h3 class="card-heading simple"></h3>
  <div class="card-body">

  </div>
 </div>
</div>
