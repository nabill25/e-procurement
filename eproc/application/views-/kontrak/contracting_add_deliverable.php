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
include_once("functions/default.func.php");
include_once("functions/date.func.php");

$reqAidi = explode('-',httpFilterRequest("reqAidi")); 
$reqId = $reqAidi[0]; // contractingrekananid
$reqMetodePembayaran = $reqAidi[1] ?: '100'; // reqMetodePembayaran

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
      $('#ffAddDeliverable').form({
        url:'contracting_json/addDeliverable',
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
          window.top.location.reload();
        }
      });

    });

    function createRowDeliverable()
    {
      $(function () {
        $.get("main/loadUrlKontrak/kontrak/data_deliverable_template", function (data) {
          $("#tbodyDeliverable").append(data);
        });
      });
    }

    $(document).ready(function() {
      $('#reqTanggalDeliveryDari, #reqTanggalDeliverySampai').datebox({
        editable: false
      }); 
    }); 

  </script>
  </head>

<body class="body-popup">

     <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Realisasi Pekerjaan</strong>
          </div>
          <div class="p-1" >
            <form id="ffAddDeliverable" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data" style="padding:0 50px">
              <div class="table-responsive">
                <?php if ($reqMetodePembayaran != '1') { // 1:Sekaligus ?>
                 <a onclick="createRowDeliverable()" class="<?= CLASS_BTN_PRIMARY ?> mb-2" style="color:#fff"> <span class="fa fa-plus text-white font-medium-2 icon-line-height" data-toggle="tooltip" data-placement="top" title=""></span> Tambah</a>
                <?php 
                } ?>
                <table class="table table-bordered table mb-0">
                  <thead>
                    <tr>
                      <th style="width: 40%">Realisasi</th>
                      <th style="width: 40%">Keterangan</th>
                      <th style="text-align: center">Tanggal</th>
                      <th style="width:10px">Aksi</th>
                    </tr>
                  </thead>
                  <tbody id="tbodyDeliverable">
                    <?php
                    $this->load->model("Contractingdeliverable");
                    $datadelivery = new Contractingdeliverable();
                    $datadelivery->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
                    $no=21;
                    if($datadelivery->countRow() > 0) { 

                      while($datadelivery->nextRow()) {
                        $status = str_replace(' ','',$datadelivery->getField('STATUS'));
                      ?>
                      <tr>
                         <td>
                          <input type="text" class="form-control easyui-validatebox" required name="deliveryname[]" id="<?=$no?>" value="<?= $datadelivery->getField('DELIVERY_NAMA') ?>">
                         </td>
                        <td>
                          <input type="text" class="form-control easyui-validatebox" required name="lingkup[]" id="<?=$no?>" value="<?= $datadelivery->getField('LINGKUP') ?>">
                        </td>
                        <td style="text-align: center">
                          <input type="text" name="reqTanggalDeliveryDari[]" id="reqTanggalDeliveryDari" class="form-control easyui-datebox" value="<?=dateToPageCheck($datadelivery->getField('TANGGAL_DELIVERY_DARI'))?>" style="width: 110%"/> <span style="margin:0 2%">s/d</span>
                          <input type="text" name="reqTanggalDeliverySampai[]" id="reqTanggalDeliverySampai" class="form-control easyui-datebox" value="<?=dateToPageCheck($datadelivery->getField('TANGGAL_DELIVERY_SAMPAI'))?>" style="width: 110%"/>
                        </td> 
                        <td>
                          <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
                         </td>
                      </tr>
                      <?php
                      $no++;
                      }

                    } else { 
                      $no=1;
                    ?>
                      <tr>
                         <td>
                          <input type="text" class="form-control easyui-validatebox" required name="deliveryname[]" id="<?=$no?>" value="<?= $datadelivery->getField('DELIVERY_NAMA') ?>">
                         </td>
                        <td>
                          <input type="text" class="form-control easyui-validatebox" required name="lingkup[]" id="<?=$no?>" value="<?= $datadelivery->getField('LINGKUP') ?>">
                         </td> 
                        <td style="text-align: center">
                          <input type="text" name="reqTanggalDeliveryDari[]" id="reqTanggalDeliveryDari" class="form-control easyui-datebox" value="<?=$reqTanggalDeliveryDari?>" style="width: 110%"/> <span style="margin:0 2%">s/d</span>
                          <input type="text" name="reqTanggalDeliverySampai[]" id="reqTanggalDeliverySampai" class="form-control easyui-datebox" value="<?=$reqTanggalDeliverySampai?>" style="width: 110%"/>
                        </td> 
                        <td>
                          <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
                         </td>
                      </tr>
                    <?php
                    }?>

                  </tbody>
                </table>
              </div>

              <div class="form-actions">
                <input type="hidden" name="contractingrekananid" id="contractingrekananid" value="<?=$reqId?>"/>
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
