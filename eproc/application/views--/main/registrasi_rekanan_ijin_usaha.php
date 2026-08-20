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
$this->load->model("IjinUsaha");
$this->load->model("Rekanan");
$this->load->model("RekananIjinUsaha");
$this->load->model("RekananBidangUsaha");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* VARIABLE */
$submitSimpan = httpFilterPost("submitSimpan");
$reqBatal = httpFilterPost("reqBatal");
$reqNomorIjin = httpFilterPost('reqNomorIjin');
$reqTanggalIjin = httpFilterPost('reqTanggalIjin');
$reqTanggalBerakhir = httpFilterPost('reqTanggalBerakhir');
$reqInstansiPemberiIjin = httpFilterPost('reqInstansiPemberiIjin');
$reqId = httpFilterRequest('reqId');
$reqBidangUsahaId = $_POST["reqBidangUsahaId"];
$reqLinkFile= $_FILES['reqLinkFile'];
$reqLinkFileTemp = httpFilterPost("reqLinkFileTemp");
$reqLinkFileTempTipe = httpFilterPost("reqLinkFileTempTipe");
$reqLinkFileTempUkuran = httpFilterPost("reqLinkFileTempUkuran");

$reqRekananId = $reqId ;
/* create objects */
$ijin_usaha = new IjinUsaha();
$rekanan_ijin_usaha = new RekananIjinUsaha();
$rekanan_bidang_usaha = new RekananBidangUsaha();
$rekanan = new Rekanan();

$rekanan->selectByParams(array("REKANAN_ID"=> $this->ID),-1,-1);
$rekanan->firstRow();  

$reqId = $this->ID;
$rekanan_ijin_usaha->selectByParams(array("REKANAN_ID" => $this->ID));
$rekanan_ijin_usaha->firstRow();
//echo $rekanan_ijin_usaha->query;exit;
$reqIjinUsahaId = $rekanan_ijin_usaha->getField("REKANAN_IJIN_USAHA_ID");
$reqNomor = $rekanan_ijin_usaha->getField("NO_IJIN");
$reqTanggalIjin = $rekanan_ijin_usaha->getField("TANGGAL");
$reqTanggalBerakhir = $rekanan_ijin_usaha->getField("TANGGAL_BERAKHIR");
$reqInstansi = $rekanan_ijin_usaha->getField("INSTANSI");
$reqBidang = $rekanan_ijin_usaha->getField("IJIN_USAHA");
$reqLinkFileTemp= $rekanan_ijin_usaha->getField("PATH_FILE");
$reqLinkFileTempTipe= $rekanan_ijin_usaha->getField("TIPE");
$reqLinkFileTempUkuran= $rekanan_ijin_usaha->getField("UKURAN");
$reqLinkFileTempNama= $rekanan_ijin_usaha->getField("NAMA_FILE");
$reqIjinUsaha= $rekanan_ijin_usaha->getField("IJIN_USAHA_ID");
$reqIjinUsahaTemp= $rekanan_ijin_usaha->getField("IJIN_USAHA_ID");

if($reqIjinUsahaId == "")
  $reqMode = "insert";
else
  $reqMode = "update";
?>
<script type="text/javascript">
$(document).ready(function() {
  
  $(function(){
    $('#ff').form({
      url:'rekanan_ijin_usaha_json/registrasi',
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
        // $.messager.alert('Info', data, 'info'); 
        alertSuccess2(data);     
        hideLoad();
        document.location.href = 'main/index/registrasi_rekanan_pengurus';
      }
    });
    
  });
  
});
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
            <strong>Ijin Usaha / OSS (<?=$reqBidang?>) <span class="badge badge-pill badge-danger">wajib</span></strong>  
          </div> 
          <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
            
            <div class="row">
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%"><?=translate("Jenis ijin", "Type Permit")?></label>
                <input type="hidden" name="reqIjinUsahaTemp" value="<?=$reqIjinUsahaTemp?>" />
                <input type="text" name="reqIjinUsaha" class="easyui-combobox span4"  id="reqIjinUsaha"
                        data-options="valueField:'id',textField:'text',url:'ijin_usaha_json/combosiup',
                            onSelect : function(rec){
                                  if(rec.id == 1)
                                        {
                                          $('#reqTanggalBerakhir').datebox({ required:false  });
                                            $('#labelTanggalBerakhir').removeClass();
                                            $('#labelTanggalBerakhir').addClass('col-md-3 control-label');
                                        }
                                        else
                                        {
                                          $('#reqTanggalBerakhir').datebox({ required:true  }); 
                                            $('#labelTanggalBerakhir').removeClass();
                                            $('#labelTanggalBerakhir').addClass('col-md-3 control-label harus-diisi');
                                        }
                            }
                            "  value="<?=$reqIjinUsaha?>" required />
              </div> 
              <div class="form-group col-md-10 mb-2">
                <label><?=translate("Nomor ijin", "Reference Number")?></label>
                <input name="reqNomorIjin" type="text" title="Nomor ijin harus diisi" class="form-control easyui-validatebox span4" id="reqNomorIjin" value="<?=$reqNomor?>" size="50" required />
              </div> 
            </div> 
            
            <div class="row">
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%"><?=translate("Tanggal ijin", "Issue Date")?></label>
                <input type="text" style="width: 150px" title="Tanggal ijin harus diisi" class="form-control easyui-datebox span2" name="reqTanggalIjin" id="reqTanggalIjin" value="<?=dateToPageCheck($reqTanggalIjin)?>" required />
              </div> 
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%" <?php if($reqIjinUsaha == 1){  ?> class="control-label" <?php }else { ?> class="control-label harus-diisi" <?php } ?>><?=translate("Tanggal berakhir", "Expired Date")?></label>
                <input type="text" style="width: 150px" title="Tanggal berakhir harus diisi" class="form-control easyui-datebox span2" name="reqTanggalBerakhir" id="reqTanggalBerakhir" value="<?=dateToPageCheck($reqTanggalBerakhir)?>" <?php if($reqIjinUsaha == 1){} else { ?> required <?php } ?> />
              </div> 
              <div class="form-group col-md-8 mb-2">
                <label><?=translate("Instansi pemberi ijin", "Issued By")?></label>
                <input name="reqInstansiPemberiIjin" title="Instansi harus diisi" class="form-control easyui-validatebox span4" type="text" id="reqInstansiPemberiIjin" value="<?=$reqInstansi?>" size="80" required />
              </div> 
            </div> 
            
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label style="width: 100%">File (<small> Format file .pdf & Maksimal ukuran file 2MB </small>)</label>
                <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30" <?php if($reqLinkFileTemp == "") { ?> required <?php } ?> maxlength="1" class="easyui-validatebox span4"  validType="fileType['pdf']" />
                <input type="hidden" name="reqLinkFileTemp" value="<?=$reqLinkFileTemp?>">
                <input type="hidden" name="reqLinkFileTempTipe" value="<?=$reqLinkFileTempTipe?>">
                <input type="hidden" name="reqLinkFileTempUkuran" value="<?=$reqLinkFileTempUkuran?>">
                <input type="hidden" name="reqLinkFileTempNama" value="<?=$reqLinkFileTempNama?>">
                <?php 
                if ($reqLinkFileTempNama) {
                   echo "File :".$reqLinkFileTempNama;
                 } ?> 
              </div> 
            </div> 

          
          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong><?=translate("Bidang usaha", "Tender Category")?></strong>   
                  <a onClick="openAdd('main/loadUrl/main/bidang_usaha');">  <span class="fa fa-plus text-white font-medium-2 icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Bidang Usaha"></span> </a> 
                </div> 
                <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead>
                      <tr class="judul-kolom">
                        <th>
                        <?=translate("Bidang usaha", "Tender Category")?> 
                        </th>   
                        <th style="text-align: center;">
                           <?=translate("Aksi", "Action")?>
                        </th>
                      </tr>
                      </thead>
                      <tbody id="tbodyBidangUsaha">
                      <?php
                       $rekanan_bidang_usaha->selectByParamsMonitoring(array("REKANAN_ID" =>$this->ID),-1,-1, " AND IJIN_USAHA_ID NOT IN(99)");
                      //echo $rekanan_bidang_usaha->query;exit;
                       while($rekanan_bidang_usaha->nextRow())
                      {
                      ?>
                      <tr>
                        <td><?=$rekanan_bidang_usaha->getField("NAMA")?></td>
                        <td style="text-align: center"><input type="hidden" name="reqBidangUsahaId[]" value="<?=$rekanan_bidang_usaha->getField("BIDANG_USAHA_ID")?>" /><a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a></td>                                    
                      </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div> 
            
          <div class="control-group">
            <label class="control-label"></label>
            <div class="controls">
              <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
              <input type="hidden" name="reqId" value="<?=$reqId?>" />
              <input type="hidden" name="reqRekananId" value="<?=$reqRekananId?>" />
             <?php /*?> <button type="submit" class="btn btn-primary"><?=translate("Simpan", "Save")?></button><?php */?>
            </div>
          </div>   
            
            <div class="form-actions" style="margin-bottom: 2%">
              <input type="hidden" name="reqId" value="<?=$reqId?>">
              <a href="main/index/registrasi_rekanan_landasan_hukum"  class="btn btn-danger"><i class="fa fa-arrow-left"></i> <?=translate("Kembali", "Back")?> </a>
              <button type="submit" class="btn btn-primary pull-right"><?=translate("Lanjut", "Next")?> <i class="fa fa-arrow-right"></i></button>
            </div>   
 
          </form>                 
        </div>
      </div>
    </div>
  </div>
</div> 
 