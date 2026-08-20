<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("Contractingfile");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqId  = $this->input->get("reqAidi"); // contractingrekananid
$contractingprosesid  = $this->input->get("reqProses"); // contractingprosesid
$reqJenis  = $this->input->get("reqJenis"); // contractingprosesid
$reqType  = $this->input->get("reqType"); // Yang upload adalah penyedia

/* create objects */
$contractingfile = new Contractingfile();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?=base_url();?>" />
    <link rel="icon" href="../../favicon.ico">
    <title><?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?></title>

    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">
    <link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">

    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <!-- PAGINATION -->
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />
     <script src="lib/emodal/eModal.js"></script>
     <style type="text/css">
       #reqKodeSeachPenyediaautocomplete-list {
          position: relative;
          margin-top: 10px;
          background: #fff;
          width: 100%;
        }
        #reqKodeSeachPenyediaautocomplete-list div {
          margin: 5px;
        }
     </style>
    <script>
    function openAdd(pageUrl) {
        eModal.iframe(pageUrl, 'Eprocurement | <?= SYSTEM_NAME_PT ?>')
    }

    function closePopup() {
      eModal.close();
    }

    function closePopupReload() {
      eModal.close();
      location.reload();
    }
    </script>
    <script type="text/javascript">
    $(function(){
      $('#ffAddFileKontrak').form({
        url:'contracting_json/addfile',
        onSubmit:function(){ 
          return $(this).form('validate');
        },
        success:function(data){
          window.top.location.reload();
        }
      });

    });

  </script>
  </head>

<body class="body-popup">

     <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Tambah Dokumen Pendukung</strong>
          </div>
          <div class="p-1" >
            <form id="ffAddFileKontrak" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data" style="padding:0 50px">
              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label>Nama Dokumen</label>
                  <input type="text" name="reqNama" id="reqNama" class="form-control easyui-validatebox span9" required/>
                </div>
              </div>
              <?php
              if ($reqJenis == 'all') { ?>
              <div class="row">
                <div class="form-group col-md-10 mb-2">
                  <label>Jenis Dokumen</label>
                  <input type="text" name="reqJenis" id="reqKodeSeachPenyedia" class="form-control easyui-validatebox span9" required/>
                </div>
                <div class="form-group col-md-2 mb-2"><br><br>
                  <input type="checkbox" name="reqPublishPenyedia" id="reqPublishPenyedia" value="1" style="cursor: pointer;"> Kirim ke Penyedia ?
                </div>
              </div>
              <?php
              } else {
                if ($reqType == 'penyedia') { // Jika yang upload dokumen penyedia
              ?>
                  <input type="hidden" name="reqJenis" id="reqKodeSeachPenyedia" class="form-control easyui-validatebox span9" value="<?php echo $reqJenis ?>"/>
                  <div class="form-group col-md-12 mb-2">
                    <input type="hidden" name="reqPublishPenyedia" id="reqPublishPenyedia" value="1" style="cursor: pointer;" checked="">
                  </div>
              <?php
                } else
                { ?>
                  <input type="hidden" name="reqJenis" id="reqKodeSeachPenyedia" class="form-control easyui-validatebox span9" value="<?php echo $reqJenis ?>"/>
                  <div class="form-group col-md-12 mb-2">
                    <input type="checkbox" name="reqPublishPenyedia" id="reqPublishPenyedia" value="1" style="cursor: pointer;"> Kirim ke Penyedia ?
                  </div>
              <?php
                }
              }?>
              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label style="width: 100%">Keterangan</label>
                  <textarea name="reqKeterangan" id="reqKeterangan" cols="45" rows="5" class="easyui-validatebox span9" style="width: 100%"></textarea>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label>File</label>
                  <input type="file" name="reqLinkFile" id="reqLinkFilePDF" class="easyui-validatebox" required validType="fileType['docx','zip', 'pdf']" />
                  <?= UPLOAD_PDF_ZIP_DOC_10MB ?>
                </div>
              </div>

              <div class="form-actions">
                <input type="hidden" name="contractingrekananid" id="contractingrekananid" value="<?=$reqId?>"/>
                <input type="hidden" name="contractingprosesid" id="contractingprosesid" value="<?=$contractingprosesid?>"/>
                <input type="hidden" name="reqSubmit" id="reqSubmit" value="Submit"/>
                <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>

  </body>
</html>
