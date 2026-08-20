<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession();   

// if($this->USER_TYPE_ID == "")
//     redirect("app");
    
/* INCLUDE FILE */
$this->load->library("crfs_protect"); $csrf = new crfs_protect('_crfs_rr');
$this->load->library("kauth");  $userLogin = new kauth(); 
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$this->load->model("Rekanan");
$this->load->model("RekananSaham");

/* create objects */
$rekanan = new Rekanan();
$rekanan_saham 	= new RekananSaham();

$reqSahamId= httpFilterRequest('reqSahamId');
$reqPemegangSaham= httpFilterPost('reqPemegangSaham');
$reqNomorKTP= httpFilterPost('reqNomorKTP');
$reqAlamat= httpFilterPost('reqAlamat');
$reqPersentase= httpFilterPost('reqPersentase');
$reqId= httpFilterPost('reqId');
$reqSubmit= httpFilterPost('reqSubmit');

$reqRekananId= $this->ID;
//$reqId = $this->ID;
//echo $reqRekananId;exit;
$rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
$rekanan->firstRow();

$allRecord_S = $rekanan_saham->getCountByParams(array("REKANAN_ID"=>$this->ID));
$rekanan_saham->selectByParams(array("REKANAN_ID" => $this->ID),-1,-1);

?>
<script type="text/javascript">
$(document).ready(function() {
	$(function(){
		$('#ff').form({
			url:'rekanan_saham_json/registrasi',
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
        //alert(data);return false;
          hideLoad();
        document.location.href = 'main/index/registrasi_rekanan_sertifikat_badan_usaha/?reqRekananSahamId=<?=$reqRekananSahamId?>';     
			}
		});
	});
	
});

function createRowKepemilikanSaham()
{
	$(function () {
		$.get("main/loadUrl/main/registrasi_rekanan_kepemilikan_saham_template", function (data) {
			$("#tbodyKepemilikanSaham").append(data);
		});
	});	
}

</script>

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/ui/jqueryui.css">
<!-- <script src="<?=base_url()?>assets/new/js/core/libraries/jquery_ui/jquery-ui.min.js"></script> -->
<!-- <script src="<?=base_url()?>assets/new/js/scripts/ui/jquery-ui/navigations.js"></script> -->
<style type="text/css">
  small { font-weight: bold; font-size: 11.5px }
</style>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  
            <strong><?=translate("Kepemilikan Saham", "Shareholding")?></strong>  
            <span class="badge badge-pill badge-danger">wajib</span>
            <small style="font-weight: normal;"><?=translate("(Data susunan kepemilikan saham diperlukan jika jenis perusahaan Anda PT atau CV.)", "Shareholding structure is required if your company type is PT or CV.")?> 
            </small> &nbsp;
            <a onclick="createRowKepemilikanSaham()"> <span class="fa fa-plus text-white font-medium-2 icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Kepemilikan Saham"></span> </a> 
          </div> 
          <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data"> 
            <table class="table table-bordered table-hover" id="tbl_bidang">
              <thead>
                <tr class="judul-kolom">
                  <th><?=translate("Nama Pemegang Saham", "Shareholders")?> </th>
                  <th><?=translate("No. KTP/NPWP", "Identity Card (ID) / NPWP")?></th>
                  <th><?=translate("Alamat", "Address")?></th>
                  <th style="width:70px"><?=translate("Persentase(%)", "Percentage(%)")?></th>
                  <th><?=translate("Aksi", "Action")?></th>
                </tr>
              </thead>
              <tbody  id="tbodyKepemilikanSaham">
                <?php
                if($allRecord_S > 0){
                $no = 1;
                while($rekanan_saham->nextRow())
                {
                ?>  
                 <tr id="tr1<?=$no?>">
                     <td>
                      <input class="form-control easyui-validatebox span2" type="hidden"  name="reqRekananSahamId[]" id="reqRekananSahamId<?=$no?>" value="<?=$rekanan_saham->getField("REKANAN_SAHAM_ID")?>" />
                        <input class="form-control easyui-validatebox span2" type="text"  name="reqPemegangSaham[]" id="reqPemegangSaham<?=$no?>" value="<?=$rekanan_saham->getField("NAMA")?>" />
                     </td>
                     <td>
                      <input class="form-control easyui-validatebox span2" type="text"  name="reqNomorKTP[]" id="reqNomorKTP<?=$no?>" value="<?=$rekanan_saham->getField("KTP")?>" />
                     </td>
                     <td>
                      <input class="form-control easyui-validatebox span2" type="text"  name="reqAlamat[]" id="reqAlamat<?=$no?>" value="<?=$rekanan_saham->getField("ALAMAT")?>" />
                     </td>
                     <td>
                      <input class="form-control easyui-validatebox span1" type="text"  name="reqPersentase[]" id="reqPersentase<?=$no?>" value="<?=$rekanan_saham->getField("JUMLAH_SAHAM")?>" onkeypress="return isNumberKey(event)" />
                     </td>
                     <td> 
                      <a onClick="deleteDataTable('rekanan_saham_json/delete/', '<?=$rekanan_saham->getField("REKANAN_SAHAM_ID")?>', 'tr1<?=$no?>')" class="btn-aksi">
                      <i class="fa fa-trash" aria-hidden="true"></i>
                      </a>
                </tr> 
                <?php
                  $no++;
                   }
                  }
                ?>
                <tr id="tr1<?=$no?>">
                     <td>
                      <input class="form-control easyui-validatebox span2" type="hidden"  name="reqRekananSahamId[]" id="reqRekananSahamId<?=$no?>" value="" />
                        <input class="form-control easyui-validatebox span2" type="text"  name="reqPemegangSaham[]" id="reqPemegangSaham<?=$no?>" value="" />
                     </td>
                     <td>
                      <input class="form-control easyui-validatebox span2" type="text"  name="reqNomorKTP[]" id="reqNomorKTP<?=$no?>" value="" />
                     </td>
                     <td>
                      <input class="form-control easyui-validatebox span2" type="text"  name="reqAlamat[]" id="reqAlamat<?=$no?>" value="" />
                     </td>
                     <td>
                      <input class="form-control easyui-validatebox span1" type="text"  name="reqPersentase[]" id="reqPersentase<?=$no?>" value="" onkeypress="return isNumberKey(event)"/>
                     </td>
                     <td> 
                      <a onClick="deleteDataTable('rekanan_saham_json/delete/', '<?=$rekanan_saham->getField("REKANAN_SAHAM_ID")?>', 'tr1<?=$no?>')" class="btn-aksi">
                      <i class="fa fa-trash" aria-hidden="true"></i>
                      </a>
                </tr> 
              </tbody>
            </table> 
            <div class="form-actions">
              <input type="hidden" name="reqRekananId" value="<?=$reqRekananId?>" />
              <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
              <input type="hidden" name="reqId" value="<?=$reqId?>">
              <a href="main/index/registrasi_rekanan_rekening_koran"  class="btn btn-danger"><i class="fa fa-arrow-left"></i> <?=translate("Kembali", "Back")?></a>
              <button type="submit" class="btn btn-primary pull-right"><?=translate("Lanjut", "Next")?> <i class="fa fa-arrow-right"></i></button>
            </div>   
 
          </form>                 
        </div>
      </div>
    </div>
  </div>
</div>  