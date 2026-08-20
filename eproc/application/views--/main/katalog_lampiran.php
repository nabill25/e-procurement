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

$this->libsession->cekSession();

$this->load->model("Katalog");

$reqId = httpFilterRequest("reqId");
if ($reqId == '') {
  redirect(base_url('main/index/katalog_rekanan'));
} else {
  $katalog = new Katalog();
  $katalog->selectByParams(array(), -1, -1, " AND A.KATALOGID = '".$reqId."' AND A.REKANAN_ID = '".$this->ID."' ");
  $katalog->firstRow();
  $reqNoproduk = $katalog->getField("NOPRODUK");
  $reqNamaproduk = $katalog->getField("NAMAPRODUK");
  $reqRekananId = $katalog->getField("REKANAN_ID");

  if ($reqRekananId != $this->ID) {
    redirect(base_url('main/index/katalog_rekanan'));
  }
}
  ?>
<!-- <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/file-uploaders/dropzone.css"> -->
<!-- <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/file-uploaders/dropzone.min.css">
<script language="javascript" src="<?=base_url()?>assets/new/vendors/js/extensions/dropzone.min.js"></script>
<script language="javascript" src="<?=base_url()?>assets/new/js/scripts/extensions/dropzone.js"></script> -->

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/file-uploaders/blueimp-gallery.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/file-uploaders/jquery.fileupload.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/file-uploaders/jquery.fileupload-ui.css">

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/js/gallery/photo-swipe/photoswipe.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/js/gallery/photo-swipe/default-skin/default-skin.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/pages/gallery.css">


<script type="text/javascript">
$(function(){
  $('#ffupload').form({
    url:'katalog_lampiran_json/json',
    onSubmit:function(){
      if($(this).form('validate'))
      {
      var win = $.messager.progress({
                    title:'Upload Lampiran',
                    msg:'Proses Mengupload Lampiran...'
                  });
      }
      else
        $('input:file').MultiFile('reset');
      return $(this).form('validate');
    },
    success:function(data){
      // alert(data);
      $.messager.progress('close');
      document.location.reload();
    }
  });


});

  function confirmation1(a,b,c) {
    var result = confirm("Are you sure to delete "+b+"?");
    if(result){
      // alert('true');
      window.location.href = "<?= base_url('katalog_lampiran_json/delete_foto?reqId=')?>"+a+"&file="+c+"";
      document.location.reload();
      return false;
    } else {
      return false;
      // alert('false');
    }
  }

  function confirmation(a,b,c)
  {
    $.messager.confirm('Konfirmasi','Apakah anda akan menghapus Lampiran '+b+' ?',function(r){
      if (r){
        $.get( "<?= base_url('katalog_lampiran_json/delete_foto?reqId=')?>"+a+"&file="+c+"", function( data ) {
          $.messager.alert('Informasi',data, 'info');
          document.location.reload();
        });
      }
      else
      {
       return false;
      }
    });

  }
</script>

<style type="text/css">
  .card-text a {
    font-size: 11px;
  }
</style>

<section id="backColor">
  <div class="row">

    <div class="col-md-2 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;">
        <div class="card-body">
          <div class="card-text">
           <?php
            if($this->USER_TYPE_ID == "6") {
            // get Notification Penawaran
              $this->load->model("Katalog");
              $katalog = new Katalog();
              $statement = " AND A.REKANAN_ID = ".$this->ID." AND (A.STATUS='1' OR A.STATUS='3' OR A.STATUS='4' OR A.STATUS='5' )";
              $totalPenawaran = $katalog->getCountByParamsPenawaran(array(), $statement);
              ?>
              <a href="<?= base_url() ?>main/index/katalog_rekanan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%; opacity: .3"><i class="fa fa-cogs fa-lg pull-right"></i> Katalog</a>
              <a href="<?= base_url() ?>main/index/katalog_rekanan_add" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-plus fa-lg pull-right"></i> Tambah Katalog</a>
              <a href="<?= base_url() ?>main/index/katalog_penawaran" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%"><i class="fa fa-shopping-basket fa-lg pull-right"></i> Penawaran <?= '<span class="badge badge-danger" style="opacity: 1">'.$totalPenawaran.'</span>'; ?></a>
              <a href="<?= base_url() ?>main/index/katalog_pernyataan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%"><i class="fa fa-upload fa-lg pull-right"></i> Upload <br>Kontrak Katalog & <br>Surat Pernyataan<br> Kewajaran Harga</a>
            <?php
            } ?>

            <?php
            if($this->USER_TYPE_ID == "1"){  ?>
              <a href="<?= base_url() ?>main/index/katalog_rekanan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-cogs fa-lg pull-right"></i> Katalog</a>
              <a href="" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%"><i class="fa fa-shopping-basket fa-lg pull-right"></i> Penawaran</a>
            <?php
            } ?>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-10 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;">
        <div class="card-header">
          <h4 class="card-title"><?= $reqNamaproduk ?></h4>
          <div class="heading-elements" id="tombol">
              <a href="<?= base_url('main/index/katalog_rekanan') ?>" class="<?= CLASS_BTN_DANGER ?>" title="Hapus"><span class="fa fa-arrow-left"></span> Kembali</a>
          </div>
        </div>
        <div class="card-content">
          <div class="card-body" style="padding: 0px 10px !important"><br>
              <p class="card-text alert alert-danger">
                Silahkan upload Lampiran produk dengan lengkap dan jelas, agar produk yang ditampilkan dapat dilihat secara detail.
              </p>
              <form method="post" class="dropzone dropzone-area" id="ffupload" novalidate enctype="multipart/form-data" style="width: 100%">
                <span class="btn btn-success fileinput-button mr-1" style="width: 100%">
                    <i class="fa fa-plus-circle fa-2x mb-1"> <small style="font-size: .6em;">Tambah Lampiran</small></i>
                    <input name="reqLinkFile" type="file" multiple class="maxsize-20240" accept="pdf" id="reqLinkFile" value=""/>
                    <small>Format file .pdf & Maksimal ukuran file 2MB </small>
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
                <input type="hidden" name="reqId" value="<?=$reqId?>" />
                <input type="submit" name="reqSubmit" id="reqSubmit" value="" style="display:none">
              </form>
          </div>
          <div class="col-md-12 mt-2 mb-4">
            <h4>List Lampiran</h4> <hr>

            <table class="table table-bordered">
              <tbody>

              <!-- width of .grid-sizer used for columnWidth -->
              <?php
              $this->load->model("Kataloglampiran");

              $Kataloglampiran = new Kataloglampiran();
              $Kataloglampiran->selectById($reqId,$this->ID);
              while($Kataloglampiran->nextRow())
              {
                if (file_exists('images/katalog/'.$Kataloglampiran->getField("path_file"))) {
                  $filenya = $Kataloglampiran->getField("path_file");
                } else {
                  $filenya = '';
                }
                ?>
                <tr>
                  <td>
                    <a style="text-align: left" href="images/katalog/<?= $filenya ?>" /> <?= $Kataloglampiran->getField("file") ?></a>
                  </td>
                  <td style="width: 10px">
                    <a onclick="return confirmation('<?= $Kataloglampiran->getField("lampiranid") ?>','<?= $Kataloglampiran->getField("file") ?>','<?= $Kataloglampiran->getField("path_file") ?>')" class="" style="cursor: pointer;"><i class="fa fa-trash"></i> </a>
                  </td>
                </tr>
              <?php
              } ?>
              </tbody>
            </table>

          </div>
        </div>
    </div>

  </div>
</section>

<script language="javascript" src="<?=base_url()?>assets/new/vendors/js/vendors.min.js"></script>
<script language="javascript" src="<?=base_url()?>assets/new/vendors/js/gallery/masonry/masonry.pkgd.min.js"></script>
<script language="javascript" src="<?=base_url()?>assets/new/vendors/js/gallery/photo-swipe/photoswipe.min.js"></script>
<script language="javascript" src="<?=base_url()?>assets/new/vendors/js/gallery/photo-swipe/photoswipe-ui-default.min.js"></script>
<script language="javascript" src="<?=base_url()?>assets/new/js/scripts/gallery/photo-swipe/photoswipe-script.js"></script>
