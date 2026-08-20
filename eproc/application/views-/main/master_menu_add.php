<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

/* INCLUDE FILE */
$this->load->model("Menu");
$this->load->model("UserType");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$menu = new Menu();
$user_type = new UserType();

$reqId  = $this->input->get("reqId") ?: '0';

if($reqId=='0')
  $reqMode= 'insert';
else
  $reqMode ='update';

$menu->selectByParams(array("MENUID" => $reqId));
$menu->firstRow();
$user_type->selectByParams(array("AKTIF" => '1'));

$reqMenuid = $menu->getField("MENUID");
$reqNamamenu = $menu->getField("NAMAMENU");
$reqIsparent = $menu->getField("ISPARENT");
$reqHakakses = $menu->getField("HAKAKSES");
$a = explode(',', $reqHakakses);
if (is_array($a)) {
  $aArray = $a;
}
$reqLinkmenu = $menu->getField("LINKMENU");
$reqStatusaktif = $menu->getField("STATUSAKTIF");
$reqOrderid = $menu->getField("ORDERID");

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
        url:'menu_json/add',
        onSubmit:function(){
          return $(this).form('validate');
        },
        success:function(data){
          //alert(data);return false;
           $.messager.alert('Info', data, 'info');
          setTimeout(function () {
            top.reloadMonitoring();
            top.closePopup();
           }, 2000);
           // top.frames['mainFrame'].location.reload();
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

<body class="body-popup" style="background: #fff;">

  <div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Master Menu</strong>
        </div>
        <div class="p-1">
          <form id="ff" class="form-horizontal" role="form" method="post" novalidate style="padding:0 50px">
            <div class="row">
            <?php
            if ($this->USER_TYPE_ID == '1')
            { // Administrator ?>
              <div class="form-group col-md-6 mb-2">
                <label>Menu</label>
                <input type="text" name="reqNamamenu" value="<?=$reqNamamenu?>" title="" class="form-control easyui-validatebox span9" required >
              </div>
              <div class="form-group col-md-11 mb-2">
                <label>URL</label>
                <input type="text" name="reqLinkmenu" value="<?=$reqLinkmenu?>" title="" class="form-control easyui-validatebox span9" required >
              </div>
              <div class="form-group col-md-12 mb-2">
                <label>Group</label>
                <fieldset class="ccheckboxsas">
                <?php
                while ($user_type->nextRow()) { ?>
                  <div class="d-inline-block">
                    <label>
                      <input type="checkbox" name="reqHakakses[]" value="<?=$user_type->getField("USER_TYPE_ID"); ?>"
                      <?php
                      if ($reqMode == 'update') {
                         if (in_array($user_type->getField("USER_TYPE_ID"), $aArray)) {
                           echo 'checked=""';
                         }
                       } ?>
                      >
                      <span style="margin-right:10px">
                      <?=$user_type->getField("NAMA"); ?>
                      </span>
                    </label>
                  </div>
                <?php
                } ?>
                </fieldset>
              </div>
                <input type="hidden" name="reqStatusaktif" value="N" checked="">
              <?php
              } else { // Administrator Approval
              ?>
              <input type="hidden" name="reqNamamenu" value="<?=$reqNamamenu?>" title="" class="form-control easyui-validatebox span9" required >
              <input type="hidden" name="reqLinkmenu" value="<?=$reqLinkmenu?>" title="" class="form-control easyui-validatebox span9" required >
              <?php
              while ($user_type->nextRow()) { ?>
                  <label style="display:none !important">
                    <input type="checkbox" name="reqHakakses[]" value="<?=$user_type->getField("USER_TYPE_ID"); ?>"
                    <?php
                    if ($reqMode == 'update') {
                       if (in_array($user_type->getField("USER_TYPE_ID"), $aArray)) {
                         echo 'checked=""';
                       }
                     } ?>
                    >
                    <span style="margin-right:10px">
                    <?=$user_type->getField("NAMA"); ?>
                    </span>
                  </label>
              <?php
              } ?>

              <div class="form-group col-md-12 mb-2">
                <label>Status</label>
                <fieldset class="ccheckboxsas">
                  <div class="d-inline-block">
                    <?php
                    if ($reqStatusaktif == 'Y') { ?>
                      <label>
                        <input type="radio" name="reqStatusaktif" value="Y" checked="">
                        <span style="margin-right:10px"> Aktif </span>
                      </label>
                      <label>
                        <input type="radio" name="reqStatusaktif" value="N">
                        <span style="margin-right:10px"> Non Aktif </span>
                      </label>
                    <?php
                    } elseif ($reqStatusaktif == 'N') { ?>
                      <label>
                        <input type="radio" name="reqStatusaktif" value="Y">
                        <span style="margin-right:10px"> Aktif </span>
                      </label>
                      <label>
                        <input type="radio" name="reqStatusaktif" value="N" checked="">
                        <span style="margin-right:10px"> Non Aktif </span>
                      </label>
                    <?php
                    } else { ?>
                      <label>
                        <input type="radio" name="reqStatusaktif" value="Y">
                        <span style="margin-right:10px"> Aktif </span>
                      </label>
                      <label>
                        <input type="radio" name="reqStatusaktif" value="N" checked="">
                        <span style="margin-right:10px"> Non Aktif </span>
                      </label>
                    <?php
                    } ?>
                  </div>
                </fieldset>
              </div>
              <?php
              } ?>
            </div>
            <div class="form-actions">
              <input type="hidden" name="reqId" value="<?=$reqId?>">
              <input type="hidden" name="reqMode" value="<?=$reqMode?>">
              <a href="#" onClick="top.closePopup()" class="btn round btn-min-width box-shadow-1 btn-danger mr-1 text-white"> <i class="fa fa-close"></i> Tutup</a>
              <button type="submit" class="btn round btn-min-width box-shadow-1 btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
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
