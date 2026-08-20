<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession('blockpenyedia');   

$this->load->library("kauth");  $userLogin = new kauth();   
$this->load->model("Rekanan");
$this->load->model("PaketDokumen");
$this->load->library("FileHandler");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$this->load->library("KMail");

$paket = new Paket();
$file = new FileHandler(); 

$submitUpload = $this->input->post("submitUpload");
$reqId = $this->input->get("reqId");
$tempReqId = $this->input->get("tempReqId");
$reqLinkFile =  $_FILES["reqLinkFile"];
$HapusData = $this->input->post("HapusData");
$reqNamaFile = $this->input->post("reqNamaFile");
$reqKirim = $this->input->post("reqKirim");

$rekanan = new Rekanan();
$rekanan->selectByParams(array("A.REKANAN_ID"=> $this->ID),-1,-1);
$rekanan->firstRow(); 
$reqCvFile = $rekanan->getField("CV_FILE");
$reqNamaFileCv = $rekanan->getField("NAMA_FILE_CV");

$FILE_DIR = "uploads/rekanan/";
$renameFile = $tempReqId."~~".formatTextToDb($file->getFileName('reqLinkFile'));

?>
<script type="text/javascript">
$(document).ready(function() {
  $(function(){
    $('#ff').form({
      url:'rekanan_json/upload_cv',
      onSubmit:function(){
        return $(this).form('validate');
      },
      success:function(data){
        // alert(data);
        alertSuccess2('Data berhasil disimpan');
        setTimeout(function () { 
          document.location.href = 'main/index/registrasi_rekanan_cv';
        }, 2000);
      }
    });
  });
});
</script>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Upload Daftar Riwayat Hidup</h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a> 
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable">
          <!-- <div class="alert alert-danger">
            <b><u>Semua yang di tuliskan pada Daftar Riwayat Hidup wajib membawa  </u></b>
          </div><br> -->
          <div class="row">
            <div class="form-group col-md-12 mb-2">
              <label >CV ( Daftar Riwayat Hidup )</label>
                <?php
                  
                  if($reqCvFile == '')
                  {
                    $reqMode=  'insert';
                  }
                  else
                    $reqMode = 'update';
                ?>
                  <input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
                  <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30" <?php if($reqLinkFileTemp == "") { ?> required <?php } ?> maxlength="1" class="easyui-validatebox"  validType="fileType['pdf']" />
                  <small> <br>Format file .pdf & Maksimal ukuran file 2MB </small>
                  <input type="hidden" name="reqLinkFileTemp" value="<?=$reqLinkFileTemp?>">
                  <input type="hidden" name="reqLinkFileTempTipe" value="<?=$reqLinkFileTempTipe?>">
                  <input type="hidden" name="reqLinkFileTempUkuran" value="<?=$reqLinkFileTempUkuran?>">
                  <?php if ($reqCvFile)
                     {
                  ?>
                      <br><hr>File CV ( Daftar Riwayat Hidup ): <?=$reqNamaFileCv?> <br>
                      <iframe src="<?=$FILE_DIR.str_replace("'", "''", $reqCvFile)?>" style="width:100%; height:600px;"></iframe>
                  <?php
                     }
                   ?>

            </div> 
          </div>
          <div class="form-actions">
            <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
            <input type="hidden" name="reqRekananId" value="<?=$this->ID?>" />
            <a href="main/index/data_administrasi_umum_cv" class="<?= CLASS_BTN_DANGER ?> mr-1"> <i class="fa fa-arrow-left"></i> Kembali </a> 
            <button type="submit" id="submitUpload" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?> Upload CV</button>
            <!-- <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a>  -->
            <!-- <a href="main/index/konfirmasi_pendaftaran"  class="btn btn-primary pull-right"><?=translate("Lanjut", "Next")?> <i class="fa fa-arrow-right"></i></a> -->
          </div> 
        </div>
      </div>
      </form>

    </div>
  </div> 
</div>  
 