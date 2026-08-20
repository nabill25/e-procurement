<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
if($this->USER_TYPE_ID == "")
    redirect("main");

$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("PaketRekanan");
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Paket");
$this->load->model("PaketDokumen");
$this->load->library("FileHandler");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$this->load->library("KMail");

$paket = new Paket();
$file = new FileHandler();
//
//$pemenang = new PaketRekanan();
//
//$reqMode = $this->input->post("reqMode");
//$reqId = $this->input->post("reqId");
//
//$pemenang->selectByParams(array("PAKET_ID" => $reqId));

$submitUpload	= $this->input->post("submitUpload");
$reqId = $this->input->get("reqId");
$tempReqId = $this->input->get("tempReqId");
$reqLinkFile =  $_FILES["reqLinkFile"];
$HapusData = $this->input->post("HapusData");
$reqNamaFile = $this->input->post("reqNamaFile");
$reqKirim = $this->input->post("reqKirim");

$paket->selectByParamsMonitoring(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
$paket->firstRow();
//echo $reqId;exit;

//$tempLink_File = $reqLink_File;

$FILE_DIR = "uploads/pemenang/";
//echo $tempReqId."ljljljljnl";
$renameFile = $tempReqId."~~".formatTextToDb($file->getFileName('reqLinkFile'));


?>
<script type="text/javascript">
$(document).ready(function() {
	$(function(){
		$('#ff').form({
			url:'paket_rekanan_json/pengumuman_prakualifikasi',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				//alert(data);
				document.location.href = 'main/index/paket_lelang_tambah_pengumuman_prakualifikasi/?reqId=<?=$reqId?>';
			}
		});
	});
});
</script>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Pengumuman Pemenang 
        </h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a> 
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable">
          <div class="card mb-1">
		    <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
		        <?
		        	$paket_dokumen = new PaketDokumen();
					$paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "PENGUMUMAN_PEMENANG"));
						
					$paket_dokumen->firstRow();
					$reqPaketDokumenId = $paket_dokumen->getField("PAKET_DOKUMEN_ID");
					
					if($reqPaketDokumenId == '')
					{
						$reqMode=  'insert';
					}
					else
						$reqMode = 'update';
				?>
		          
		          
		            <?  if ($paket_dokumen->getField("PATH_FILE")=='')
						{
						}
						else
						{
					?>
		           	 		<?php /*?><br><a href="<?=$FILE_DIR.str_replace("'", "''", $paket_dokumen->getField("PATH_FILE"))?>" class="taut" target="_blank">Download</a><?php */?>
		                    <iframe src="<?=$FILE_DIR.str_replace("'", "''", $paket_dokumen->getField("PATH_FILE"))?>" style="width:100%; height:870px; zoom: 1.4"></iframe>
		        	<?
						}
					?>
		        <div>
		        	 <input type="hidden" name="tempReqId" value="<?=$reqId?>" />
		             <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
		             <input type="hidden" name="reqPaketDokumenId" value="<?=$reqPaketDokumenId?>" />
					 <input type="hidden" name="reqId" value="<?=$reqId?>" />
		        </div>
			</form> 
          </div>
          <div class="form-actions">
			  <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a> 
			</div> 
        </div>
      </div>
    </div>
  </div> 
</div>   