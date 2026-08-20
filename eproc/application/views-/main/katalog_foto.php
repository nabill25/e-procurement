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
    url:'katalog_foto_json/json',
    onSubmit:function(){
      if($(this).form('validate'))
      {
      var win = $.messager.progress({
                    title:'Upload Gambar/Foto',
                    msg:'Proses Mengupload Gambar/Foto...'
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
      window.location.href = "<?= base_url('katalog_foto_json/delete_foto?reqId=')?>"+a+"&file="+c+"";
      document.location.reload();
      return false;
    } else {
      return false;
      // alert('false');
    }
  }

  function confirmation(a,b,c)
  {
    $.messager.confirm('Konfirmasi','Apakah anda akan menghapus Gambar/Foto '+b+' ?',function(r){
      if (r){
        $.get( "<?= base_url('katalog_foto_json/delete_foto?reqId=')?>"+a+"&file="+c+"", function( data ) {
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
              $statement = ' AND A.REKANAN_ID = '.$this->ID.' AND A.STATUS=\'1\' OR A.STATUS=\'3\' OR A.STATUS=\'4\' OR A.STATUS=\'5\' ';
              $totalPenawaran = $katalog->getCountByParamsPenawaran(array(), $statement);
            ?>
              <a href="<?= base_url() ?>main/index/katalog_rekanan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%; opacity: .3"><i class="fa fa-cogs fa-lg pull-right"></i> Katalog</a>
              <a href="<?= base_url() ?>main/index/katalog_rekanan_add" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-plus fa-lg pull-right"></i> Tambah Katalog</a>
              <a href="<?= base_url() ?>main/index/katalog_penawaran" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%"><i class="fa fa-shopping-basket fa-lg pull-right"></i> Penawaran <?= '<span class="badge badge-danger" style="opacity: 1">'.$totalPenawaran.'</span>'; ?></a>
              <a href="<?= base_url() ?>main/index/katalog_pernyataan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%"><i class="fa fa-upload fa-lg pull-right"></i> Upload <br>Kontrak Katalog & <br>Surat Pernyataan<br> Kewajaran Harga</a>
            <?php
            } ?>

            <?php
            if($this->USER_TYPE_ID == "1" || $this->USER_TYPE_ID == "2"){  // 1:admin, 2:validator?>
              <a href="<?= base_url() ?>main/index/katalog_rekanan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%; opacity: .3"><i class="fa fa-cogs fa-lg pull-right"></i> Katalog</a>
              <a href="<?= base_url() ?>main/index/katalog_validasi" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-edit fa-lg pull-right"></i> Validasi</a>
              <a href="<?= base_url() ?>main/index/katalog_laporan" class="btn btn-success btn-lg mb-1 text-left" style="width: 100%;"><i class="fa fa-flag fa-lg pull-right"></i> Laporan</a>
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
                Silahkan upload gambar/foto produk dengan lengkap dan jelas, agar produk yang ditampilkan dapat dilihat secara detail.
              </p>
              <form method="post" class="dropzone dropzone-area" id="ffupload" novalidate enctype="multipart/form-data" style="width: 100%">
                <span class="btn btn-success fileinput-button mr-1" style="width: 100%">
                    <i class="fa fa-plus-circle fa-2x mb-1"> <small style="font-size: .6em;">Tambah Gambar/Foto</small></i>
                    <input name="reqLinkFile" type="file" multiple class="maxsize-20240" accept="jpg|jpeg|png" id="reqLinkFile" value=""/>
                    <small>Format file .jpg .jpeg .png & Maksimal ukuran file 1MB <br> Ukuran 300x300px</small>
                </span>
                <script>
                // wait for document to load
                $( "#reqLinkFile" ).bind( "change", function() {
                  document.querySelector('#reqSubmit').click();
                });
                </script>
                <input type="hidden" name="reqId" value="<?=$reqId?>" />
                <!-- <input type="submit" name="reqSubmit" id="reqSubmit" value="" style="display:none"> -->
                <input type="submit" name="reqSubmit" id="reqSubmit" value="submit" style="display:none">
              </form>
          </div>
            <div class="col-md-12 mt-2 mb-4">
              <h4>List Gambar/Foto</h4> <hr>

              <div class="card-body  my-gallery" itemscope itemtype="http://schema.org/ImageGallery">
                <div class="row">

                <!-- width of .grid-sizer used for columnWidth -->
                <?php
                $this->load->model("Katalogfoto");

                $Katalogfoto = new Katalogfoto();
                $Katalogfoto->selectById($reqId,$this->ID);
                while($Katalogfoto->nextRow())
                {
                  if (file_exists('images/katalog/'.$Katalogfoto->getField("path_file"))) {
                    $filenya = $Katalogfoto->getField("path_file");
                  } else {
                    $filenya = '';
                  }
                  ?>
                  <figure class="col-lg-3 col-md-6 col-12" itemprop="associatedMedia" itemscope>
                      <a href="images/katalog/<?= $filenya ?>" itemprop="contentUrl" data-size="400x400">
                        <img class="img-thumbnail img-fluid" src="images/katalog/<?= $filenya ?>" itemprop="thumbnail" alt="Image description" />
                      </a>
                      <!-- <small><?= $Katalogfoto->getField("file") ?></small> -->
                      <a onclick="return confirmation('<?= $Katalogfoto->getField("fotoid") ?>','<?= $Katalogfoto->getField("file") ?>','<?= $Katalogfoto->getField("path_file") ?>')" class="btn btn-danger mr-1 text-white" style="cursor: pointer; width: 100%; border-radius: 0"><i class="fa fa-trash"></i> </a>
                  </figure>
                <?php
                } ?>
              </div>

              <!-- Root element of PhotoSwipe. Must have class pswp. -->
              <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="pswp__bg"></div>
                <div class="pswp__scroll-wrap">
                  <div class="pswp__container">
                    <div class="pswp__item"></div>
                    <div class="pswp__item"></div>
                    <div class="pswp__item"></div>
                  </div>

                  <div class="pswp__ui pswp__ui--hidden">
                    <div class="pswp__top-bar">
                        <div class="pswp__counter"></div>
                        <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                        <button class="pswp__button pswp__button--share" title="Share"></button>
                        <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                        <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                        <div class="pswp__preloader">
                            <div class="pswp__preloader__icn">
                              <div class="pswp__preloader__cut">
                                <div class="pswp__preloader__donut"></div>
                              </div>
                            </div>
                        </div>
                    </div>

                    <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                        <div class="pswp__share-tooltip"></div>
                    </div>

                    <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
                    </button>

                    <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
                    </button>

                    <div class="pswp__caption">
                        <div class="pswp__caption__center"></div>
                    </div>
                  </div>

                </div>
              </div>
              <!-- End Root element of PhotoSwipe. Must have class pswp. -->

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- <script language="javascript" src="<?=base_url()?>assets/new/vendors/js/vendors.min.js"></script> -->
<script language="javascript" src="<?=base_url()?>assets/new/vendors/js/gallery/masonry/masonry.pkgd.min.js"></script>
<script language="javascript" src="<?=base_url()?>assets/new/vendors/js/gallery/photo-swipe/photoswipe.min.js"></script>
<script language="javascript" src="<?=base_url()?>assets/new/vendors/js/gallery/photo-swipe/photoswipe-ui-default.min.js"></script>
<script language="javascript" src="<?=base_url()?>assets/new/js/scripts/gallery/photo-swipe/photoswipe-script.js"></script>
