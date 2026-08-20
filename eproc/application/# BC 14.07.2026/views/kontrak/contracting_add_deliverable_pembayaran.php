<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model(array("Contractingfile","Contracting","Contractingdeliverable","Contractingpayment"));
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqAidi = explode('-',httpFilterRequest("reqAidi")); 
$reqDeliverableId = httpFilterRequest("reqDeliverableId"); 

$reqId = $reqAidi[0]; // contractingrekananid
$reqMetodePembayaran = $reqAidi[1] ?: '100'; // reqMetodePembayaran
/* create objects */
$contractingfile = new Contractingfile();
$contracting = new Contracting();
$contractingdeliverable = new Contractingdeliverable();
$contractingpayment = new Contractingpayment();

if ($reqDeliverableId) {
  $reqSubmit = 'Update';

  $contractingdeliverableEdit = new Contractingdeliverable();
  $contractingdeliverableEdit->selectByParams(array("DELIVERABLEID" => $reqDeliverableId));
  $contractingdeliverableEdit->firstRow();
  $deliveryname = $contractingdeliverableEdit->getField("DELIVERY_NAMA");
  $reqlingkup = $contractingdeliverableEdit->getField("LINGKUP");
  $reqTanggalDeliveryDari = dateToPageCheck($contractingdeliverableEdit->getField("TANGGAL_DELIVERY_DARI"));
  $reqTanggalDeliverySampai = dateToPageCheck($contractingdeliverableEdit->getField("TANGGAL_DELIVERY_SAMPAI"));

  $contractingpaymentEdit = new Contractingpayment();
  $contractingpaymentEdit->selectByParams(array("DELIVERABLEID_FK" => $reqDeliverableId));
  $contractingpaymentEdit->firstRow();
  $payterminke = $contractingpaymentEdit->getField("PAY_TERMIN_KE");
  $paynilai = $contractingpaymentEdit->getField("PAY_NILAI");
  $payprogres = $contractingpaymentEdit->getField("PAY_PROGRES");
  $paydatedari = dateToPageCheck($contractingpaymentEdit->getField("PAY_DATE_DARI"));
  $paydatesampai = dateToPageCheck($contractingpaymentEdit->getField("PAY_DATE_SAMPAI"));

  $contractingpayment->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
  while ($contractingpayment->nextRow()) {
    $totalNilai += $contractingpayment->getField("PAY_NILAI");
    $totalProgres += $contractingpayment->getField("PAY_PROGRES");
  }

  $contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
  $contracting->firstRow();

  $nilaiSisa = ($contracting->getField("CR_NILAI_KONTRAK") + $contractingpaymentEdit->getField("PAY_NILAI")) - $totalNilai ;

} else {
  $reqSubmit = 'Simpan';
  $contractingpayment->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
  while ($contractingpayment->nextRow()) {
    $totalNilai += $contractingpayment->getField("PAY_NILAI");
    $totalProgres += $contractingpayment->getField("PAY_PROGRES");
  }

  $contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
  $contracting->firstRow();

  $nilaiSisa = $contracting->getField("CR_NILAI_KONTRAK") - $totalNilai;
}


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
        url:'contracting_json/addPaymentMerger',
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
           }, 2000);
        }
      });
      
    }); 

    function createRowPayment()
    {
      var aa = <?= $reqMetodePembayaran ?>;
      $(function () {
        $.get("main/loadUrlKontrak/kontrak/data_payment_template?reqMetodePembayaran="+aa+"&reqId="+<?= $reqId ?>, function (data) {
          $("#tbodyDeliverable").append(data);
        });
      }); 
    }

     $(document).ready(function() {
      $('#reqPayDateDari, #reqPayDateSampai, #reqTanggalDeliveryDari, #reqTanggalDeliverySampai').datebox({
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
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Output Pekerjaan</strong>
          </div> 
          <div class="p-1" >
            <form id="ffAddDeliverable" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data" style="padding:0 10px">
                <input type="hidden" class="form-control easyui-validatebox" name="" id="reqNilaiSisa" value="<?= $nilaiSisa ?>">
                <input type="hidden" class="form-control easyui-validatebox" name="" id="reqProgres" value="<?= $totalProgres ?>">
                <div class="row"> 
                  <div class="col-md-12 text-right">
                    <h2>Nilai Tersisa: <?= currencyToPage($nilaiSisa) ?></h2>
                  </div>
                </div>
              <div class="table-responsive">
                
                <table class="table table-bordered table mb-0"> 
                  <tbody id="tbodyDeliverable">   
                    <tr>
                      <td width="35%"> Pekerjaan </td>
                      <td> 
                        <input type="text" class="form-control easyui-validatebox" required name="reqdeliveryname" id="reqNo" value="<?= $deliveryname ?>">
                      </td>
                    </tr>
                    <tr>
                      <td> Keterangan </td>
                      <td> 
                       <textarea class="form-control easyui-validatebox" name="reqlingkup"><?= $reqlingkup ?></textarea>
                      </td>
                    </tr>
                    <tr>
                      <td width="20%"> Tanggal <br>Output Pekerjaan </td>
                      <td>
                          <input type="text" name="reqTanggalDeliveryDari" id="reqTanggalDeliveryDari" class="form-control easyui-datebox" value="<?= $reqTanggalDeliveryDari ?>" style="width: 110%"/> <span style="margin:0 2%">s/d</span>
                          <input type="text" name="reqTanggalDeliverySampai" id="reqTanggalDeliverySampai" class="form-control easyui-datebox" value="<?= $reqTanggalDeliverySampai ?>" style="width: 110%"/>
                        </td> 
                    </tr>  
                    <tr style="background: #103A6C !important; color: #fff;"> 
                      <td colspan="2">PEMBAYARAN</td>
                    </tr>
                    <tr> 
                      <td width="20%"> Penagihan </td>
                      <td> 
                        <input type="text" class="form-control easyui-validatebox" required name="payterminke" id="reqNo" value="<?= $payterminke ?>">
                      </td>  
                    </tr> 
                    <tr>
                      <td> Nilai Pembayaran</td> 
                      <td> 
                        <input type="text" class="form-control easyui-validatebox" required name="paynilai" value="<?= $paynilai ?>" id="reqNilai" value="" OnFocus="FormatAngka('reqNilai')" OnKeyUp="hitungTotal(); FormatUang('reqNilai')" OnBlur="FormatUang('reqNilai')" onchange="hitungTotal()">
                      </td> 
                    </tr> 
                    <tr>
                      <td> Persentase %</td>
                      <td>
                      <input type="text" class="form-control easyui-validatebox" required name="payprogres" id="reqpayprogres" OnFocus="FormatAngka('reqpayprogres')" OnFocus="FormatAngka('reqpayprogres')" OnKeyUp="FormatUang('reqpayprogres')" OnBlur="FormatUang('reqpayprogres')" maxlength="3" value="<?= $payprogres ?>" style="width:100px">
                      </td> 
                    </tr>   
                    <tr>
                      <td> Tanggal <br>Penyelesaian Administrasi</td>
                      <td>
                        <input type="text" name="reqPayDateDari" id="reqPayDateDari" class="form-control easyui-datebox" value="<?= $paydatedari ?>" style="width: 110%"/> 
                        <span style="margin:0 2%">s/d</span>
                        <input type="text" name="reqPayDateSampai" id="reqPayDateSampai" class="form-control easyui-datebox" value="<?= $paydatesampai ?>" style="width: 110%"/>
                      </td> 
                    </tr>   

                    <tr>
                      <td colspan="2">
                        <span id="notifHPS">
                          <?php if ($totalNilai > $contracting->getField("CR_NILAI_KONTRAK")) {
                            echo "<div class=\"alert alert-danger\">Nilai Pembayaran tidak boleh melebihi Nilai HPS</div>";
                          } ?>
                        </span>
                      </td>
                    </tr>

                  </tbody>
                </table> 
              </div>

              <div class="form-actions">
                <input type="hidden" name="contractingrekananid" id="contractingrekananid" value="<?=$reqId?>"/>
                <input type="hidden" name="deliverableid" id="deliverableid" value="<?=$reqDeliverableId?>"/>
                <input type="hidden" name="reqSubmit" id="reqSubmit" value="<?= $reqSubmit ?>"/>
                <button id="btnSimpan" type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
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

    <script>
    function hitungTotal() {
        let total = $('#reqNilai').val().replace(/\./g, '')   // hapus titik
                .replace(/,/g, '');   // hapus koma;
        let sisa = <?= $nilaiSisa ?: 0?>; 
        if (total > sisa) {
          $('#notifHPS').html("<div class=\"alert alert-danger\">Nilai Pembayaran tidak boleh melebihi Nilai Tersisa</div>");
          $('#btnSimpan').hide();
        } else {
          $('#notifHPS').html("");
          $('#btnSimpan').show();
        }

    }

    function hitungProgress() {
        let total = 0; 
        document.querySelectorAll('.payprogres').forEach(function(el) {
            let val = el.value
                .replace(/\./g, '')   // hapus titik
                .replace(/,/g, '');   // hapus koma

            if (val !== '' && !isNaN(val)) {
                total += parseFloat(val);
            }
        });

          document.getElementById('totalProgress').value = total;

    }

    // formatter sederhana
    function formatRupiah(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    </script>

    
  </body>
</html>
