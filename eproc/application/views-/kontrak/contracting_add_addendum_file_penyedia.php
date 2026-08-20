<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->load->library("kauth");  $userLogin = new kauth();
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqId  = $this->input->get("reqAidi"); // addendumid
$reqConRekId  = $this->input->get("reqConRekId"); // contractingrekananid

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
      $('#ffUpdateFileAddendumPenyedia').form({
        url:'contracting_json/editFileAddendumPenyedia',
        onSubmit:function(){
          var v=$(this).form('validate');
          if(v) {
            return v;
          } else {
            return false;
           // showLoad();  // show the message box
          }
        },
        success:function(data){
          $.messager.alert('Info', data, 'info');
          setTimeout(function () {
            window.top.location.reload();
          }, 1500);
        }
      });

    });

    $(document).ready(function() {
      $('#reqTanggal, #reqTanggalTerima').datebox({
        editable: false
      });

    });

  </script>
  </head>

<body class="body-popup" style="background: #fff;">

     <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Upload Addendum yang sudah di Tandatangani</strong>
          </div>
          <div class="p-1" >
            <form id="ffUpdateFileAddendumPenyedia" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data" style="padding:0 50px">
              <div class="table-responsive">
                <table class="table table-bordered table mb-0">
                  <tbody id="tbodyDeliverable">
                    <?php
                    $this->load->model("Contractingaddendum");
                    $dataAddendum = new Contractingaddendum();
                    $dataAddendum->selectByParams(array("CONTRACTING_ADDENDUM_ID"=>$reqId));
                    $no=21;
                    while($dataAddendum->nextRow()) {
                    ?> 
                    <tr>
                      <td> File Addendum </td>
                      <td> 
                        <input type="file" name="reqLinkFile" id="reqLinkFile" size="30" class="easyui-validatebox span9" validType="fileType['pdf','zip']"/><br>
                        <?= UPLOAD_PDF_ZIP_2MB ?>
                        <input type="hidden" name="reqLinkFileTemp" id="reqLinkFileTemp" value="<?= $dataAddendum->getField('ADDENDUM_FILE_PENYEDIA') ?>" />
                        <?php 
                        if (file_exists('uploads/kontrak/'.$dataAddendum->getField('ADDENDUM_FILE_PENYEDIA')) && $dataAddendum->getField('ADDENDUM_FILE_PENYEDIA') != '' ) {
                          echo '<br><a href="uploads/kontrak/'.$dataAddendum->getField('ADDENDUM_FILE_PENYEDIA').'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> Download </span></a>';
                        } ?>
                      </td> 
                    </tr> 
                    <?php
                    $no++;
                    } ?>
                  </tbody>
                </table>
              </div>

              <div class="form-actions">
                <input type="hidden" name="contractingaddendumid" id="contractingaddendumid" value="<?=$reqId?>"/>
                <input type="hidden" name="reqContractingRekananId" id="reqContractingRekananId" value="<?=$reqConRekId?>"/>
                <input type="hidden" name="reqSubmit" id="reqSubmit" value="Submit"/>
                <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>">Upload & Setujui Addendum</button>
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
