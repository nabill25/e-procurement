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

$reqId  = $this->input->get("reqAidi"); // SANKSIID 
$reqConId  = $this->input->get("reqConId"); // CONTRACTINGREKANANID 

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
        url:'contracting_json/editanksi22',
        onSubmit:function(){
          return $(this).form('validate');
        },
        success:function(data){
          $.messager.alert('Info', data, 'info');
          setTimeout(function () {
            window.top.location.reload();
           }, 2000);
        }
      });
      
    });  

    function toggleUpload(el) {
        var row = el.closest('.row-bayar');
        var upload = row.querySelector('.showUpload');
        var file = row.querySelector('input[type="file"]');

        if (el.value === 'Disetor') {
            upload.style.display = 'block';
            file.required = true;
        } else {
            upload.style.display = 'none';
            file.required = false;
            file.value = '';
        }
    }
 
  </script>
  <script type="text/javascript">
   function calculate()
    {
        nilaisanksi = document.getElementById('nilaisanksi').value;
        nilaipekerjaan = document.getElementById('nilaipekerjaan').value;
        hariterlambat = document.getElementById('hariterlambat').value;

        nilaisanksiParsing = parseFloat(nilaisanksi.split('.').join(""));
        nilaipekerjaanParsing = parseFloat(nilaipekerjaan.split('.').join(""));
        hariterlambatParsing = parseFloat(hariterlambat.split('.').join(""));

        total = (nilaisanksiParsing/1000) * nilaipekerjaanParsing * hariterlambatParsing;

        $('#nilaidenda').val(FormatNumberya(total));
    }
    function FormatNumberya(id)
    {
       var a = parseFloat(id);
       var nilai = FormatCurrency(a);
       return nilai;    
    }
  </script>
  </head>

<body class="body-popup" style="background: #fff;"> 

     <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Denda Keterlambatan</strong>
          </div> 
          <div class="p-1" >
            <form id="ffAddDeliverable" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data"style="padding:0 10px">
              <div class="table-responsive"> 
                <table class="table table-bordered table mb-0"> 
                  <tbody id="tbodyDeliverable">  
                    <?php 
                    $this->load->model("Contractingsanksi");
                    $datasanksi = new Contractingsanksi();
                    $datasanksi->selectByParams(array("SANKSIID"=>$reqId));
                    $no=21;
                    if($datasanksi->countRow() > 0) { 
                      while($datasanksi->nextRow()) { 
                      ?>
                      <tr> 
                        <td width="35%"> Tagihan </td>
                        <td> 
                              <?php 
                              $this->load->model(array("Contractingpayment"));
                              $contractingPay = new Contractingpayment();
                              $contractingPay->selectByParams(array("A.CONTRACTINGREKANANID" => $reqConId, "PAYMENTID" => $datasanksi->getField('PAYMENTID_FK')));
                              $contractingPay->firstRow();
                              echo $contractingPay->getField("PAY_TERMIN_KE");
                               ?>
                        </td>  
                      </tr> 
                      <tr> 
                        <td width="20%"> Nilai Sanksi /1000 </td>
                        <td>  <?= $datasanksi->getField('NILAI_SANKSI') ?> </td>  
                      </tr> 
                      <tr>
                        <td> Nilai / Bagian Pekerjaan</td>
                        <td>  <?= number_format($datasanksi->getField('NILAI_PEKERJAAN'),0,",",".") ?> </td>  
                      </tr> 
                      <tr>
                        <td> Hari Keterlambatan</td>
                        <td>  <?= $datasanksi->getField('HARI_TERLAMBAT') ?> </td>  
                      </tr>  
                      <tr>
                        <td> Nilai Denda</td> 
                        <td>  <?= number_format($datasanksi->getField('NILAI_DENDA'),0,",",".") ?> </td>  
                      </tr>  
                      <tr> 
                        <td width="20%"> Upload Bukti Bayar </td>
                        <td> 
                          <div class="row-bayar"> 
                                <input type="file" name="reqLampiran" id="reqLampiran<?= $no ?>" size="30" class="easyui-validatebox span9" validType="fileType['pdf']"/><br>
                              <?= UPLOAD_PDF_ZIP_2MB ?> 
                              <input type="hidden" name="reqLampiranTemp" id="reqLampiranTemp" value="<?= $datasanksi->getField('BUKTI_BAYAR') ?>" />

                          </div>
                        </td>  
                      </tr> 
                      <?php 
                      $no++;
                      }
                    } else { 
                      $no=1;
                    ?>
                     . : : Tidak ada data : : .
                    <?php 
                    } ?>
                  </tbody>
                </table> 
              </div>

              <div class="form-actions">
                <input type="hidden" name="sanksiid" id="sanksiid" value="<?=$reqId?>"/>
                <input type="hidden" name="reqSubmit" id="reqSubmit" value="Submit"/>
                <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-check-square-o"></i> Simpan</button>
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
