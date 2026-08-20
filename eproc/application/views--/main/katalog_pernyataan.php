<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("Katalogrekanan");

$this->libsession->cekSession();


?>

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/file-uploaders/blueimp-gallery.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/file-uploaders/jquery.fileupload.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/file-uploaders/jquery.fileupload-ui.css">

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/js/gallery/photo-swipe/photoswipe.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/js/gallery/photo-swipe/default-skin/default-skin.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/pages/gallery.css">


<script type="text/javascript">
$(function(){
  $('#ffupload').form({
    url:'katalog_json/uploadpernyataan',
    onSubmit:function(){
      if($(this).form('validate'))
      {
      var win = $.messager.progress({
                    title:'Upload Dokumen',
                    msg:'Proses Mengupload Dokumen...'
                  });
      }
      else
        $('input:file').MultiFile('reset');
      return $(this).form('validate');
    },
    success:function(data){
      $.messager.progress('close');
      alertSuccess2(data);
      setTimeout(function() {
        document.location.reload();
      }, 2000);
    }
  });


});
</script>

<style type="text/css">
  .card-text a {
    font-size: 11px;
  }
</style>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Kontrak Katalog & Dokumen Lain</h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
            <?php
            $this->load->model("Masterdokumentemplate");
            $master_dokumen = new Masterdokumentemplate();
            $master_dokumen->selectByParams(array('B.NAMA' => 'Dokumen Template Kontrak Katalog'));
            if ($master_dokumen->countRow() > 0) {
              $master_dokumen->firstRow();
             ?>
                <a href="uploads/template/<?=$master_dokumen->getField('PATH_FILE')?>" class="<?= CLASS_BTN_PRIMARY ?> mr-1" target="_blank"><i class="fa fa-print"></i> Download Template Kontrak Katalog</a>
            <?php
            } ?>
            <!-- <a href="<?php // base_url('uploads/surat-pernyataan.pdf') ?>" target="_blank" class="btn btn-primary mr-1"><i class="fa fa-print"></i> </a> -->
            <hr>
            <form method="post" class="dropzone dropzone-area" id="ffupload" novalidate enctype="multipart/form-data" style="width: 100%">
              <span class="btn btn-success fileinput-button mr-1" style="width: 100%">
                  <i class="fa fa-plus-circle fa-2x mb-1"> <small style="font-size: .6em;">Wajib Upload <br> 1. Kontrak Katalog <br>2. Surat Pernyataan Kewajaran Harga</small></i>
                  <input name="reqLinkFile" type="file" multiple class="maxsize-20240" accept="pdf" id="reqLinkFile" value=""/>
                  <small>Format file .pdf & Maksimal ukuran file 1MB </small>
              </span>
              <script>
              // wait for document to load
              $(function(){
                  // invoke plugin
                  $('#reqLinkFile').MultiFile({
                      onFileChange: function(){
                          document.querySelector('#reqSubmit').click();
                      }
                  });
              });
              </script>
              <input type="submit" name="reqSubmit" id="reqSubmit" value="" style="display:none">
            </form>

          <div class="col-md-12 mt-2 mb-4">
            <?php
              $this->load->model("Katalogsuratpernyataan");

              $Katalogsuratpernyataan = new Katalogsuratpernyataan();
              $Katalogsuratpernyataan->selectByParams(array('A.CREATED_BY' => $this->USER_LOGIN_ID ),-1,-1," ORDER BY SPID DESC LIMIT 3");
              while($Katalogsuratpernyataan->nextRow())
              {
                if (file_exists('uploads/vms/surat_pernyataan/'.$Katalogsuratpernyataan->getField("PATH_SP"))) {
                  $filenya = $Katalogsuratpernyataan->getField("PATH_SP");
                  $filename = $Katalogsuratpernyataan->getField("FILE_SP");
                } else {
                  $filenya = '';
                  $filename = '';
                }
                ?>
                  <a style="text-align: left" href="uploads/vms/surat_pernyataan/<?= $filenya ?>" target="_blank" />
                    <div class="alert alert-info">
                      <span class="fa fa-download"></span> <?= $filename ?>
                    </div>
                  </a>
              <?php
              } ?>
                  <small class="alert alert-warning">Catatan: Kuota upload 3 file, jika melebihi kuota yang dipakai file terakhir upload </small>
          </div>


          <div class="form-actions mt-2">
            <a href="main/index/katalog_rekanan" class="<?= CLASS_BTN_DANGER ?> mr-1"> <i class="fa fa-arrow-left"></i> Kembali </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<script language="javascript" src="<?=base_url()?>assets/new/vendors/js/vendors.min.js"></script>
<script language="javascript" src="<?=base_url()?>assets/new/vendors/js/gallery/masonry/masonry.pkgd.min.js"></script>
<script language="javascript" src="<?=base_url()?>assets/new/vendors/js/gallery/photo-swipe/photoswipe.min.js"></script>
<script language="javascript" src="<?=base_url()?>assets/new/vendors/js/gallery/photo-swipe/photoswipe-ui-default.min.js"></script>
<script language="javascript" src="<?=base_url()?>assets/new/js/scripts/gallery/photo-swipe/photoswipe-script.js"></script>
