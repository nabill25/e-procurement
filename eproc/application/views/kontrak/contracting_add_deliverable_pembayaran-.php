<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model(array("Contractingfile","Contracting","Contractingdeliverable"));
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqAidi = explode('-',httpFilterRequest("reqAidi")); 
$reqId = $reqAidi[0]; // contractingrekananid
$reqMetodePembayaran = $reqAidi[1] ?: '100'; // reqMetodePembayaran
/* create objects */
$contractingfile = new Contractingfile();
$contracting = new Contracting();
$contractingdeliverable = new Contractingdeliverable();


$contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
$contracting->firstRow();
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
        url:'contracting_json/addPayment',
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
          // window.top.location.reload();
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
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Penagihan</strong>
          </div> 
          <div class="p-1" >
            <form id="ffAddDeliverable" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data" style="padding:0 10px">
                <div class="row">
                  <div class="col-md-8">
                  <?php if ($reqMetodePembayaran != '1') { // 1:Sekaligus ?>
                   <a onclick="createRowPayment()" class="<?= CLASS_BTN_PRIMARY ?> mb-2" style="color:#fff"> <span class="fa fa-plus text-white font-medium-2 icon-line-height" data-toggle="tooltip" data-placement="top" title=""></span> Tambah</a> 
                  <?php } ?>
                    
                  </div>
                  <div class="col-md-4 text-right">
                    <h2>Nilai Kontrak: <?= currencyToPage($contracting->getField("CR_NILAI_KONTRAK")) ?></h2>
                  </div>
                </div>
              <div class="table-responsive">


                <table class="table table-bordered table mb-0">
                  <thead>
                    <tr style="background-color:#103A6C !important; color:#fff">
                      <th style="width:350px">Output Pekerjaan</th> 
                      <th style="width:300px">Tagihan</th>
                      <th>Nilai Pembayaran</th>
                      <th style="width:80px">Presentase %</th>
                      <th style="text-align: center; width:100px">Tanggal</th>
                      <th width="10px">Aksi</th>
                    </tr>
                  </thead> 
                  <tbody id="tbodyDeliverable">  
                    <?php 
                    $this->load->model("Contractingpayment");
                    $datapayment = new Contractingpayment();
                    $datapayment->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
                    $no=21;
                    if($datapayment->countRow() > 0) { 
                      while($datapayment->nextRow()) { 
                        $totalPay += $datapayment->getField('PAY_NILAI');
                        $totalProgress += $datapayment->getField('PAY_PROGRES');
                        $status = str_replace(' ','',$datapayment->getField('STATUS'));
                        $contractingdeliverable->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));

                      ?> 
                      <tr> 
                        <td>
                          <select class="form-control" name="reqDeliverableId[]">
                            <?php 
                            while($contractingdeliverable->nextRow()) { 
                              $selected = ''; 
                              if ($contractingdeliverable->getField("DELIVERABLEID") == $datapayment->getField('DELIVERABLEID_FK')) {
                                $selected = 'selected'; 
                              }
                              ?>
                              <option value="<?= $contractingdeliverable->getField("DELIVERABLEID") ?>" <?= $selected ?>><?= $contractingdeliverable->getField("DELIVERY_NAMA") ?></option>
                            <?php 
                            } ?>
                          </select>
                        </td> 
                         <td>
                          <input type="text" class="form-control easyui-validatebox" required name="payketerangan[]" id="<?=$no?>" value="<?= $datapayment->getField('KETERANGAN') ?>">
                         </td>  
                        <td>
                          <input type="text" class="form-control easyui-validatebox" required name="payteminke[]" id="<?=$no?>" value="<?= $datapayment->getField('PAY_TERMIN_KE') ?>">
                         </td>  
                         <td>
                          <input type="text" class="form-control easyui-validatebox paynilai" name="paynilai[]" id="reqNilai<?=$no?>" value="<?= $datapayment->getField('PAY_NILAI') ?>" id="reqNilai<?=$no?>" value="" OnFocus="FormatAngka('reqNilai<?=$no?>')" OnKeyUp="hitungTotal(); FormatUang('reqNilai<?=$no?>')" onchange="hitungTotal()" OnBlur="FormatUang('reqNilai<?=$no?>')">
                         </td>  
                         <td>
                          <input type="text" class="form-control easyui-validatebox payprogres" name="payprogres[]" id="payprogres<?=$no?>" OnFocus="FormatAngka('payprogres<?=$no?>')" OnFocus="FormatAngka('payprogres<?=$no?>')" OnKeyUp="hitungProgress(); FormatUang('payprogres<?=$no?>')" onchange="hitungProgress()" OnBlur="FormatUang('payprogres<?=$no?>')" maxlength="3" value="<?= $datapayment->getField('PAY_PROGRES') ?>">
                         </td>  
                         <td style="text-align: center">
                            <input type="text" name="reqPayDateDari[]" id="reqPayDateDari" class="form-control easyui-datebox" value="<?=dateToPageCheck($datapayment->getField('PAY_DATE_DARI'))?>" style="width: 110%"/> 
                            <br><span style="margin:0 2%">s/d</span><br>
                            <input type="text" name="reqPayDateSampai[]" id="reqPayDateSampai" class="form-control easyui-datebox" value="<?=dateToPageCheck($datapayment->getField('PAY_DATE_SAMPAI'))?>" style="width: 110%"/>
                          </td> 
                        <td>
                          <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
                         </td>
                      </tr>
                      <?php 
                      $no++;
                      }
                    } else {
                      $contractingdeliverable->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
                      $no=1;
                    ?>
                     <tr>
                        <td>
                          <input type="text" class="form-control easyui-validatebox" required name="deliveryname[]" id="<?=$no?>" value="<?= $datapayment->getField('DELIVERY_NAMA') ?>"> <br>
                          <small>Keterangan:</small>
                          <textarea class="form-control easyui-validatebox" name="lingkup[]"><?= $datapayment->getField('LINGKUP') ?></textarea> <br>
                          <small>Tanggal:</small><br>
                          <input type="text" name="reqTanggalDeliveryDari[]" id="reqTanggalDeliveryDari" class="form-control easyui-datebox" value="<?=$reqTanggalDeliveryDari?>" style="width: 120%"/> 
                          <span style="margin:0 2%">s/d</span>
                          <input type="text" name="reqTanggalDeliverySampai[]" id="reqTanggalDeliverySampai" class="form-control easyui-datebox" value="<?=$reqTanggalDeliverySampai?>" style="width: 120%"/>
                        </td>   
                         <td>
                          <input type="text" class="form-control easyui-validatebox" required name="payteminke[]" id="<?=$no?>" value="<?= $datapayment->getField('PAY_TERMIN_KE') ?>">
                         </td>  
                         <td>
                          <input type="text" class="form-control easyui-validatebox paynilai" name="paynilai[]" id="reqNilai<?=$no?>" value="<?= $datapayment->getField('PAY_NILAI') ?>" id="reqNilai<?=$no?>" value="" OnFocus="FormatAngka('reqNilai<?=$no?>')" OnKeyUp="hitungTotal(); FormatUang('reqNilai<?=$no?>')" onchange="hitungTotal()" OnBlur="FormatUang('reqNilai<?=$no?>')">
                         </td>   
                         <td>
                          <input type="text" class="form-control easyui-validatebox payprogres" name="payprogres[]" id="payprogres<?=$no?>" OnFocus="FormatAngka('payprogres<?=$no?>')" OnFocus="FormatAngka('payprogres<?=$no?>')" OnKeyUp="hitungProgress(); FormatUang('payprogres<?=$no?>')" onchange="hitungProgress()" OnBlur="FormatUang('payprogres<?=$no?>')" maxlength="3" value="<?= $datapayment->getField('PAY_PROGRES') ?>">
                         </td>  
                         <td style="text-align: center">
                            <input type="text" name="reqPayDateDari[]" id="reqPayDateDari" class="form-control easyui-datebox" value="<?=dateToPageCheck($datapayment->getField('PAY_DATE_DARI'))?>" style="width: 110%"/> 
                            <br><span style="margin:0 2%">s/d</span><br>
                            <input type="text" name="reqPayDateSampai[]" id="reqPayDateSampai" class="form-control easyui-datebox" value="<?=dateToPageCheck($datapayment->getField('PAY_DATE_SAMPAI'))?>" style="width: 110%"/>
                          </td> 
                        <td>
                          <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
                         </td>
                      </tr> 
                    <?php 
                    } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td style="background-color: #000; color:#fff"> 
                        Nilai Tersisa
                      </td>
                      <td class="text-right">
                        <span id="sisaPembayaran" class="text-right" style="font-size:14px"> 
                        <input type="text" id="totalSisa" value="<?= currencyToPage($totalPay - $contracting->getField("CR_NILAI_KONTRAK")) ?>" class="form-control" readonly>
                        </span>
                      </td>
                      <td>
                        <input type="text" id="totalNilai" value="<?= currencyToPage($totalPay) ?>" class="form-control" readonly>
                      </td>
                      <td>
                        <input type="text" id="totalProgress" value="<?= $totalProgress ?>" class="form-control" readonly>
                      </td>
                      <td colspan="2">
                      </td>
                    </tr>
                    <tr>
                      <td colspan="6">
                        <span id="notifHPS">
                          <?php if ($totalPay > $contracting->getField("CR_NILAI_KONTRAK")) {
                            echo "<div class=\"alert alert-danger\">Nilai Pembayaran tidak boleh melebihi Nilai HPS</div>";
                          } ?>
                        </span>
                      </td>
                    </tr>
                  </tfoot>
                </table> 
              </div>

              <div class="form-actions">
                <input type="hidden" name="contractingrekananid" id="contractingrekananid" value="<?=$reqId?>"/>
                <input type="hidden" name="reqSubmit" id="reqSubmit" value="Submit"/>
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
        let total = 0;
        let HPS = <?= $contracting->getField("CR_NILAI_KONTRAK") ?: 0?>;
        document.querySelectorAll('.paynilai').forEach(function(el) {
            let val = el.value
                .replace(/\./g, '')   // hapus titik
                .replace(/,/g, '');   // hapus koma

            if (val !== '' && !isNaN(val)) {
                total += parseFloat(val);
            }
        });

        if (total > HPS) {
          $('#notifHPS').html("<div class=\"alert alert-danger\">Nilai Pembayaran tidak boleh melebihi Nilai HPS</div>");
          $('#btnSimpan').hide();
        } else {
          $('#notifHPS').html("");
          $('#btnSimpan').show();
        }
          document.getElementById('totalSisa').value = formatRupiah(total-HPS);

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
