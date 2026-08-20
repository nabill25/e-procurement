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
    <!-- END VENDOR CSS-->
    <!-- BEGIN ROBUST CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <!-- <link rel="stylesheet" type="text/css" href="<?=base_url() ?>css/core.css"> -->
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">
    
    <!-- FONT AWESOME -->
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
      $('#ffAddDeliverable').form({
        url:'contracting_json/addSLA',
        onSubmit:function(){
          var v=$(this).form('validate');
          if(v) // showLoad();  // show the message box
          return v;
        },
        success:function(data){
          window.top.location.reload();
        }
      });
      
    }); 

    function createRowSLA()
    {
      $(function () {
        $.get("main/loadUrlKontrak/kontrak/data_sla_template", function (data) {
          $("#tbodySLA").append(data);
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
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Service Level Agreement (SLA)</strong>
          </div> 
          <div class="p-1" >
            <form id="ffAddDeliverable" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data" style="padding:0 50px">
              <div class="table-responsive">
                 <a onclick="createRowSLA()" class="<?= CLASS_BTN_PRIMARY ?> mb-2" style="color:#fff"> <span class="fa fa-plus text-white font-medium-2 icon-line-height" data-toggle="tooltip" data-placement="top" title=""></span> Tambah</a> 
                <table class="table table-bordered table mb-0">
                  <thead>
                    <tr>
                      <th width="220px">Availability (%)</th>
                      <th width="50px">Waktu (jam)</th>
                      <th width="50px">Denda (%) </th>
                      <th>Biaya Maintanance</th>
                      <th>Nilai Denda</th>
                      <!-- <th width="100px">Status</th> -->
                      <th width="10px">Aksi</th>
                    </tr>
                  </thead> 
                  <tbody id="tbodySLA">  
                    <?php 
                    $this->load->model("Contractingsla");
                    $datasla = new Contractingsla();
                    $datasla->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
                    $no=21;
                    // $slastatusArr = array('');
                      while($datasla->nextRow()) { 
                        $status = str_replace(' ','',$datasla->getField('SLA_STATUS'));
                      ?>
                      <tr> 
                         <td>
                          <input type="text" class="form-control easyui-validatebox" required name="slaavaibility[]" id="reqAvaibility<?=$no?>" value="<?= $datasla->getField('SLA_AVAILABILITY') ?>" id="reqAvaibility<?=$no?>" value="" OnFocus="CekDouble('reqAvaibility<?=$no?>')" OnKeyUp="CekDouble('reqAvaibility<?=$no?>')" OnBlur="CekDouble('reqAvaibility<?=$no?>')" maxlength="5">
                          <small>Desimal gunakan titik (99.35)</small>
                         </td>  
                         <td>
                          <input type="text" class="form-control easyui-validatebox" required name="slawaktu[]" id="<?=$no?>" value="<?= $datasla->getField('SLA_WAKTU') ?>">
                         </td> 
                         <td>
                          <input type="text" class="form-control easyui-validatebox" required name="sladenda[]" id="reqDenda<?=$no?>" value="<?= $datasla->getField('SLA_DENDA') ?>" id="reqDenda<?=$no?>" value="" OnFocus="FormatAngka('reqDenda<?=$no?>')" OnKeyUp="FormatUang('reqDenda<?=$no?>')" OnBlur="FormatUang('reqDenda<?=$no?>')" maxlength="2">
                         </td> 
                         <td>
                          <input type="text" class="form-control easyui-validatebox" required name="slabiayamaintanance[]" id="reqBiaya<?=$no?>" value="<?= $datasla->getField('SLA_BIAYA_MAINTANANCE') ?>" id="reqBiaya<?=$no?>" value="" OnFocus="FormatAngka('reqBiaya<?=$no?>')" OnKeyUp="FormatUang('reqBiaya<?=$no?>')" OnBlur="FormatUang('reqBiaya<?=$no?>')">
                         </td> 
                         <td>
                          <input type="text" class="form-control easyui-validatebox" required name="slanilaidenda[]" id="reqNilai<?=$no?>" value="<?= $datasla->getField('SLA_NILAI_DENDA') ?>" id="reqNilai<?=$no?>" value="" OnFocus="FormatAngka('reqNilai<?=$no?>')" OnKeyUp="FormatUang('reqNilai<?=$no?>')" OnBlur="FormatUang('reqNilai<?=$no?>')">
                         </td>   
                         <!-- <td>
                          <select class="form-control" name="slastatus[]">
                            
                          </select>
                          <input type="text" class="form-control easyui-validatebox" required name="slastatus[]" id="<?php //$no?>" value="<?php // $status ?>">
                         </td>   -->
                        <td>
                          <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
                         </td>
                      </tr>
                      <?php 
                      $no++;
                      }
                    ?>
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
    
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
    
    <!-- EASYUI -->
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>
    
  </body>
</html>
