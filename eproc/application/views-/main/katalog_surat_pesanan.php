<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();   

include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model(array("Katalogrekanan","Paket"));

$this->libsession->cekSession();

$reqId = $this->input->get("reqId");

$paket = new Paket();

$paket->selectByParamsMonitoring(array("A.PAKET_ID" => coalesce($reqId, 0)));
$paket->firstRow();
$multi_pemenang = $paket->getField("MULTI_PEMENANG");
$ppk = $paket->getField("PPK");
$reqUUID = $paket->getField("PAKET_UUID");

if ($reqId == '')
  redirect(base_url('main'));

  if ($paket->getField("USER_LOGIN_ID") != $this->USER_LOGIN_ID && $ppk != $this->USER_LOGIN_ID) {
    redirect(base_url('main'));
  }

$katalogrekananRow = new Katalogrekanan();
$katalogrekananRow->selectByParams(array('A.PAKET_ID' => $reqId));
$katalogrekananRow->firstRow();

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
    url:'katalog_json/uploadsp',
    onSubmit:function(){
      if($(this).form('validate'))
      {
      var win = $.messager.progress({
                    title:'Upload Surat Pesanan',
                    msg:'Proses Mengupload Surat Pesanan...'
                  });
      }
      else
        $('input:file').MultiFile('reset');
      return $(this).form('validate');
    },
    success:function(data){
      $.messager.progress('close');
      if (data === 'Surat Pesanan berhasil diupload.') {
        alertSuccess2(data);
      } else {
        alertError2(data);
      }
      setTimeout(function() {
        document.location.reload();
      }, 2000);
    }
  });


});

  function confirmation(a,b,c)
  {
    $.messager.confirm('Konfirmasi','Apakah anda akan menghapus Lampiran '+b+' ?',function(r){
      if (r){
        $.get( "<?= base_url('katalog_json/delete_sp?reqId=')?>"+a+"&file="+c+"", function( data ) {
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

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Surat Pesanan</h4>
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
          if ($ppk == $this->USER_LOGIN_ID) { 
            if($katalogrekananRow->getField('STATUS') == '2' || $katalogrekananRow->getField('STATUS') == '3')
            { ?>
              <a href="<?= base_url('main/loadUrl/report/katalog_surat_pesanan_pdf/?reqId='.$reqId) ?>" class="<?= CLASS_BTN_DANGER ?> mr-1"><i class="fa fa-print"></i> Download Template Surat Pesanan</a>
              <hr>
              <form method="post" class="dropzone dropzone-area" id="ffupload" novalidate enctype="multipart/form-data" style="width: 100%">
                <span class="btn btn-success fileinput-button mr-1" style="width: 100%">
                    <i class="fa fa-plus-circle fa-2x mb-1"> <small style="font-size: .6em;">Upload Surat Pesanan</small></i>
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
                <input type="hidden" name="reqId" value="<?=$reqId?>" />
                <input type="submit" name="reqSubmit" id="reqSubmit" value="" style="display:none">
              </form>
            <?php
            } 
          }?>

          <div class="col-md-12 mt-2 mb-4">
            <h4>Surat Pesanan</h4> <hr>

            <table class="table table-bordered">
              <tbody>

              <!-- width of .grid-sizer used for columnWidth -->
              <?php
              $this->load->model("Kataloglogistik");

              $Kataloglogistik = new Kataloglogistik();
              $Kataloglogistik->selectByParams(array('A.PAKET_ID' => $reqId ));
              while($Kataloglogistik->nextRow())
              {
                if (file_exists('images/katalog/'.$Kataloglogistik->getField("path_file_surat_pesanan"))) {
                  $filenya = $Kataloglogistik->getField("path_file_surat_pesanan");
                } else {
                  $filenya = '';
                }
                ?>
                <tr>
                  <td>
                    <a style="text-align: left" href="images/katalog/<?= $filenya ?>" /> <?= $Kataloglogistik->getField("file_surat_pesanan") ?></a>
                  </td>
                </tr>
              <?php
              } ?>
              </tbody>
            </table>

          </div>

          <div class="form-actions mt-2">
            <?php 
            if ($ppk != $this->USER_LOGIN_ID) { 
            ?>
              <a href="<?php if($reqId == "") { ?>main/index/paket_lelang<?php } else { ?>main/index/paket_detil/?eid=<?=$reqId?>&key=<?= $reqUUID ?><?php } ?>" class="<?= CLASS_BTN_DANGER ?> mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a>
           <?php 
            } else 
            {
            ?>
            <a href="<?php if($reqId == "") { ?>main/index/paket_lelang<?php } else { ?>main/index/paket_detil_kontrak/?reqId=<?=$reqId?><?php } ?>" class="<?= CLASS_BTN_DANGER ?> mr-1"> <i class="fa fa-arrow-left"></i> Kembali </a> 
            <?php 
            } ?>
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
