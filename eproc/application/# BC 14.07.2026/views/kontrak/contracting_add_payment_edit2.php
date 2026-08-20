<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model(array("Contractingfile","Contractingdeliverable"));
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqAidi = explode('-',httpFilterRequest("reqAidi")); 
$reqId = $reqAidi[0]; // contractingrekananid
$reqMetodePembayaran = $reqAidi[1]; // reqMetodePembayaran
$reqConRekId  = $this->input->get("reqConRekId"); // contractingrekananid

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

    <title><?= SYSTEM_NAME_PT ?></title>

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
        url:'contracting_json/editPayment',
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
  $(document).ready(function() {

    $('#reqPayDate').datebox({
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
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Pembayaran</strong>
          </div> 
          <div class="p-1" >
            <form id="ffAddDeliverable" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data"style="padding:0 50px">
              <div class="table-responsive"> 
                <table class="table table-bordered table mb-0"> 
                  <tbody id="tbodyDeliverable">  
                    <?php 

                    $this->load->model("Contractingpayment");
                    $datapayment = new Contractingpayment();
                    $contractingdeliverable = new Contractingdeliverable();
                    $datapayment->selectByParams(array("PAYMENTID"=>$reqId));
                    $no=21;
                    if($datapayment->countRow() > 0) { 
                      while($datapayment->nextRow()) { 
                        $status = str_replace(' ','',$datapayment->getField('STATUS'));
                      ?>
                      <tr>
                        <td> Nomor Surat Pembayaran</td>
                        <td> <input type="text" name="reqPayNomor" id="reqPayNomor" value="<?= $datapayment->getField('PAY_NOMOR') ?>" class="form-control easyui-validatebox" required/></td> 
                      </tr> 
                      <tr>
                        <td> Tanggal </td>
                        <td> <input type="text" style="width:120px" class="form-control easyui-datebox span9" name="reqPayDate" id="reqPayDate" value="<?= dateToPageCheck($datapayment->getField('PAY_DATE')) ?>" /></td> 
                      </tr> 
                      <tr>
                        <?php 
                        if ($reqMetodePembayaran == '2') { // Termin  ?>
                        <td width="20%"> Pembayaran </td>
                        <td> <?= $datapayment->getField('PAY_TERMIN_KE') ?></td> 
                        <?php 
                        } else { ?>
                        <td width="20%"> Pembayaran </td>
                        <td> Sekaligus</td>  
                        <?php 
                        } ?>
                      </tr>
                      <tr>
                        <td> Nilai Pembayaran </td>
                        <td> <?= currencyToPage($datapayment->getField('PAY_NILAI')) ?></td> 
                      </tr> 
                      <tr>
                        <td> Keterangan </td>
                        <td> <?= $datapayment->getField('PAY_KETERANGAN') ?></td> 
                      </tr>
                      <tr>
                        <td> Progres (%)</td>
                        <td> 
                          <input style="width: 60px;" type="text" name="reqPayProgres" id="reqPayProgres" value="<?= $datapayment->getField('PAY_PROGRES') ?>" class="form-control easyui-validatebox mb-1" maxlength="3" required/> 
                        </td> 
                      </tr>   
                      <tr>
                        <td> Status </td>
                        <td> 
                          <?php $status = str_replace(' ','', $datapayment->getField('PAY_STATUS')); ?>
                          <select class="form-control" name="status" style="width:25%">
                           <option <?php if ($status == 'Proses') { echo "selected"; } ?> value="Proses">Proses</option>
                           <option <?php if ($status == 'Selesai') { echo "selected"; } ?> value="Selesai">Selesai</option>
                          </select>
                        </td> 
                      </tr>
                      <tr>
                        <td> BAPP </td>
                        <td> 
                          <?php $bapp = $datapayment->getField('PAY_LAMPIRAN_BAPP') ?> 
                          <input type="file" name="reqLampiran2" id="reqLampiran2" size="30" <?php if($bapp == "") { ?> class="easyui-validatebox span9 custom" <?php } ?> validType="fileType['pdf']"/><br>
                          <?= UPLOAD_PDF_ZIP_2MB ?>
                          <input type="hidden" name="reqLampiranTemp2" id="reqLampiranTemp2" value="<?= $bapp ?>" />
                          <?php 
                          if (file_exists('uploads/payment/'.$datapayment->getField('PAY_LAMPIRAN_BAPP')) && $datapayment->getField('PAY_LAMPIRAN_BAPP') != '' ) {
                            echo '<br><a href="uploads/payment/'.$datapayment->getField('PAY_LAMPIRAN_BAPP').'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> BAPP</span></a>';
                          } ?>
                        </td> 
                      </tr>
                      <tr>
                        <td> Berita Acara </td>
                        <td> 
                          <?php $ba = $datapayment->getField('PAY_LAMPIRAN') ?> 
                          <input type="file" name="reqLampiran" id="reqLampiran" size="30" <?php if($ba == "") { ?> class="easyui-validatebox span9" <?php } ?> validType="fileType['pdf']"/><br>
                          <?= UPLOAD_PDF_ZIP_2MB ?>
                          <input type="hidden" name="reqLampiranTemp" id="reqLampiranTemp" value="<?= $ba ?>" />
                          <?php 
                          if (file_exists('uploads/payment/'.$datapayment->getField('PAY_LAMPIRAN')) && $datapayment->getField('PAY_LAMPIRAN') != '' ) {
                            echo '<br><a href="uploads/payment/'.$datapayment->getField('PAY_LAMPIRAN').'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> Berita Acara</span></a>';
                          } ?>
                        </td> 
                      </tr>
                      <?php 
                      $no++;
                      }
                    } else { 
                      $no=1;
                    ?>
                     
                    <?php 
                    } ?>
                  </tbody>
                </table> 
              </div>

              <div class="form-actions">
                <input type="hidden" name="paymentid" id="paymentid" value="<?=$reqId?>"/>
                <input type="hidden" name="reqContractingRekananId" id="reqContractingRekananId" value="<?=$reqConRekId?>"/>
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
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>
    
  </body>
</html>
