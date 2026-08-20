<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
/* INCLUDE FILE */
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

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
	  <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script> 
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />
    <script src="lib/emodal/eModal.js"></script>
    <script>
    function openAdd(pageUrl) {
        eModal.iframe(pageUrl, '<?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?>')
      }
  	function closePopup() {
  		eModal.close();
  	}
      </script>
      <script type="text/javascript">
        $(function(){
          $('#ffUpload').form({
            url:'rekanan_json/upload_pakta_integritas',
            onSubmit:function(){
              if($(this).form('validate'))
              {
              var win = $.messager.progress({
                            title:'Proses Upload',
                            msg:'Mengupload dokumen...'
                          });
              }
              else
              $('input:file').MultiFile('reset');
              return $(this).form('validate');
            },
            success:function(data){
              $.messager.progress('close');
              $.messager.alert('Info', data, 'info');
              setTimeout(function () {
                top.closePopup();
                top.konfirmasiReload();
              }, 2000);
            }
          }); 
        });
    </script>
  </head>

<body class="body-popup" style="background: #fff !important;">

  <div class="mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Upload Pakta Integritas</strong>
        </div>
        <div class="p-1">
          <form id="ffUpload" method="post" novalidate enctype="multipart/form-data">
            <input name="reqLinkFile" type="file" multiple class="easyui-validatebox maxsize-20240" validType="fileType['pdf']" accept="pdf" id="reqLinkFile" value="" required="" />
            <br><small><?= UPLOAD_PDF_2MB ?> </small>
            <script>
            // wait for document to load
              $( "#reqLinkFile").bind( "change", function() {
                document.querySelector('#reqSubmitPaktaIntegritas').click();
              });
            </script>
            <input type="hidden" name="reqNamaDokumen" id="reqNamaDokumen" value="Pakta Integritas" />
            <input type="hidden" name="reqJenisDokumen" id="reqJenisDokumen" value="PAKTA_INTEGRITAS" />
            <input type="submit" name="reqSubmitPaktaIntegritas" id="reqSubmitPaktaIntegritas" value="" style="display:none">
          </form>
        </div>
      </div>
    </div>
  </div>


    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>

  </body>
</html>
