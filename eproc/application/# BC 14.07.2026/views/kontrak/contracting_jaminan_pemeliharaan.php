<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model(array("Contractingjaminanpemeliharaan"));
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqId  = $this->input->get("reqId"); // contractingrekananid
$reqPaketId  = $this->input->get("reqPaketId"); // paketid

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
      $('#ffJaminanPemeliharaan').form({
        url:'contracting_json/addJampel',
        onSubmit:function(){
          var v=$(this).form('validate');
          if(v) // showLoad();  // show the message box
          return v;
        },
        success:function(data){
          $.messager.alert('Info', data, 'info');
          setTimeout(function () { 
            window.top.location.reload();  
          }, 2000);
        }
      });
      
    });  
  $(document).ready(function() {

    $('#reqTanggalMulai, #reqTanggalAkhir').datebox({
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
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Jaminan Pemeliharaan</strong>
          </div> 
          <div class="p-1" >
            <form id="ffJaminanPemeliharaan" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
              <div class="table-responsive"> 
                <table class="table table-bordered table mb-0"> 
                  <tbody id="tbodyDeliverable">  
                    <?php 
                    $dataJamPel = new Contractingjaminanpemeliharaan();
                    $dataJamPel->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
                    $dataJamPel->firstRow();
                    
                    $id = $dataJamPel->getField('CONTRACTING_JAMPEL_ID');
                    if ($id != '') {
                      $action = 'update';
                    } else {
                      $action = 'simpan';
                    }
                      ?>
                      <tr>
                        <td> Nomor</td>
                        <td> <input type="text" name="reqNomor" id="reqNomor" value="<?= $dataJamPel->getField('NOMOR') ?>" class="form-control easyui-validatebox" required/></td> 
                      </tr> 
                      <tr>
                        <td> Nilai</td>
                        <td> 
                          <input type="text" class="form-control easyui-validatebox nilai" name="nilai" id="reqNilai<?=$no?>" value="<?= $dataJamPel->getField('NILAI') ?>" id="reqNilai<?=$no?>" OnFocus="FormatAngka('reqNilai<?=$no?>')" OnKeyUp="FormatUang('reqNilai<?=$no?>')" OnBlur="FormatUang('reqNilai<?=$no?>')" style="width:200px" required>
                        </td> 
                      </tr>  
                      <tr>
                        <td> Masa </td>
                        <td>
                          <?php $masa = str_replace(' ','', $dataJamPel->getField('MASA')); ?>
                          <select class="form-control" name="reqMasa" style="width:25%">
                           <option <?php if ($masa == '3') { echo "selected"; } ?> value="3">3 Bulan</option>
                           <option <?php if ($masa == '6') { echo "selected"; } ?> value="6">6 Bulan</option>
                           <option <?php if ($masa == '12') { echo "selected"; } ?> value="12">12 Bulan</option>
                           <option <?php if ($masa == '18') { echo "selected"; } ?> value="18">18 Bulan</option>
                           <option <?php if ($masa == '24') { echo "selected"; } ?> value="24">24 Bulan</option>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <td> Tanggal Mulai </td>
                        <td> <input type="text" style="width:120px" class="form-control easyui-datebox span9" name="reqTanggalMulai" id="reqTanggalMulai" value="<?= dateToPageCheck($dataJamPel->getField('TANGGAL_MULAI')) ?>" /></td> 
                      </tr>   
                      <tr>
                        <td> Tanggal Akhir </td>
                        <td> <input type="text" style="width:120px" class="form-control easyui-datebox span9" name="reqTanggalAkhir" id="reqTanggalAkhir" value="<?= dateToPageCheck($dataJamPel->getField('TANGGAL_AKHIR')) ?>" /></td> 
                      </tr>   
                      <tr>
                        <td> File Jaminan </td>
                        <td> 
                          <?php $bapp = $dataJamPel->getField('FILE_JAMINAN') ?> 
                          <input type="file" name="reqLampiran" id="reqLampiran" size="30" <?php if($bapp == "") { ?> class="easyui-validatebox span9 custom" <?php } ?> validType="fileType['pdf']"/><br>
                          <?= UPLOAD_PDF_ZIP_2MB ?>
                          <input type="hidden" name="reqLampiranTemp" id="reqLampiranTemp" value="<?= $bapp ?>" />
                          <?php 
                          if (file_exists('uploads/payment/'.$dataJamPel->getField('FILE_JAMINAN')) && $dataJamPel->getField('FILE_JAMINAN') != '' ) {
                            echo '<br><a href="uploads/payment/'.$dataJamPel->getField('FILE_JAMINAN').'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> File Jaminan</span></a>';
                          } ?>
                        </td> 
                      </tr> 
                     
                  </tbody>
                </table> 
              </div>

              <div class="form-actions"> 
                <input type="hidden" name="reqContractingJempelId" id="reqContractingJempelId" value="<?=$id?>"/>
                <input type="hidden" name="reqContractingRekananId" id="reqContractingRekananId" value="<?=$reqId?>"/>
                <input type="hidden" name="reqPaketId" id="reqPaketId" value="<?=$reqPaketId?>"/>
                <input type="hidden" name="reqSubmit" id="reqSubmit" value="<?= $action ?>"/>
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
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>
    
  </body>
</html>
