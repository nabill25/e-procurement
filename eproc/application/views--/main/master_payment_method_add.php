<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

/* INCLUDE FILE */
$this->load->model("PaymentMethod");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$bank = new PaymentMethod();

$reqId  = $this->input->get("reqId") ?: '0';

if($reqId=='0')
  $reqMode= 'insert';
else
  $reqMode ='update';

$bank->selectByParams(array("PAYMENT_METHOD_ID" => $reqId));
$bank->firstRow();

$reqNama = $bank->getField("NAMA");

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?=base_url();?>" />
    <link rel="icon" href="../../favicon.ico">
    <title><?= SYSTEM_NAME_PT ?></title>
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
        eModal.iframe(pageUrl, '<? SYSTEM_NAME.' '.SYSTEM_NAME_PT ?>')
      }
    function closePopup() {
      eModal.close();
    }
    </script>
    <script type="text/javascript">
    $(function(){
      $('#ff').form({
        url:'payment_method_json/add',
        onSubmit:function(){
          return $(this).form('validate');
        },
        success:function(data){
          //alert(data);return false;
           $.messager.alert('Info', data, 'info');
              top.reloadMonitoring();
        }
      });

    });

    function createRowDokumenPanitia()
    {
      $(function () {
        $.get("main/loadUrl/main/panitia_add_template/?reqUnitKerja="+$("#reqUnitKerja").val(), function (data) {
          $("#tbDataDokumenPanitia").append(data);
        });
      });
    }
    </script>

  </head>

<body class="body-popup">

  <div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Payment Method</strong>
        </div>
        <div class="p-1">
          <form id="ff" class="form-horizontal" role="form" method="post" novalidate style="padding:0 50px">
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label>Nama</label>
                <textarea name="reqNama" title="" rows="5" class="form-control easyui-validatebox span9" required ><?=$reqNama?></textarea>
              </div>
            </div>
            <div class="form-actions">
              <input type="hidden" name="reqId" value="<?=$reqId?>">
              <input type="hidden" name="reqMode" value="<?=$reqMode?>">
              <a href="#" onClick="top.closePopup()" class="<?= CLASS_BTN_DANGER ?>"> <i class="fa fa-close"></i> Tutup</a>
              <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

    <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script><link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>
  </body>
</html>
