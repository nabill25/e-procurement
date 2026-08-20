<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model(array("Contractingfile","Contractingrekanan"));
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqId  = $this->input->get("reqAidi"); // deliverableid
$reqConRekId  = $this->input->get("reqConRekId"); // contractingrekananid

$contractingfile = new Contractingfile();
$spkpks = new Contractingrekanan();

$spkpks->selectViewPKSSPK(array("A.CONTRACTINGREKANANID" => $reqConRekId));
$spkpks->firstRow();
$reqWaktuPelaksanaanDari = $spkpks->getField('CR_WAKTU_PELAKSANAAN_DARI') ?: '-';
$reqWaktuPelaksanaanSampai = $spkpks->getField('CR_WAKTU_PELAKSANAAN_SAMPAI') ?: '-';

// Format YMD
$exTglAwal = explode('-',datetimeToPage($reqWaktuPelaksanaanDari, "date"));
$exTglAwalYear = $exTglAwal[2];
$exTglAwalMonth = $exTglAwal[1]-1;
$exTglAwalDate = $exTglAwal[0];


// Format YMD
$exTglNow = explode('-',datetimeToPage(date('Y-m-d'), "date"));
$exTglNowYear = $exTglNow[2];
$exTglNowMonth = $exTglNow[1];
$exTglNowDate = $exTglNow[0];
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
        url:'contracting_json/addDeliverableEdit',
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
                <table class="table table-bordered table mb-0">
                  <tbody id="tbodyDeliverable">
                    <?php
                    $this->load->model(array("Contractingdeliverable","Contractingpayment"));
                    $datadelivery = new Contractingdeliverable();
                    $datadelivery->selectByParams(array("DELIVERABLEID"=>$reqId));
                    $no=21;
                    while($datadelivery->nextRow()) {
                      $status = str_replace(' ','',$datadelivery->getField('STATUS'));
                      $datapayment = new Contractingpayment();
                      $datapayment->selectByParams(array("DELIVERABLEID_FK" => $datadelivery->getField('DELIVERABLEID')));
                      $datapayment->firstRow(); 
                    ?>
                    <tr>
                      <td width="20%"> Realisasi </td>
                      <td><?= $datadelivery->getField('DELIVERY_NAMA') ?></td>
                    </tr>
                    <tr>
                      <td> Keterangan </td>
                      <td> <?= $datadelivery->getField('LINGKUP') ?></td>
                    </tr>
                    <script>
                    $(document).ready(function() {
                      $('#reqTanggal').datebox({
                        editable: false
                      });
                    });
                      $(function(){
                        $('#reqTanggal').datebox().datebox('calendar').calendar({
                          validator: function(date){
                            var now = new Date();
                            var d1 = new Date(<?= $exTglAwalYear.','.$exTglAwalMonth.','.$exTglAwalDate; ?>);
                            // var d2 = new Date(<?= $exTglAkhirYear.','.$exTglAkhirMonth.','.$exTglAkhirDate; ?>);
                            // return d1<=date && date<=d2;
                            return d1<=date;
                          }
                        });
                        $('#reqTanggalTerima').datebox().datebox('calendar').calendar({
                          validator: function(date){
                            var now = new Date();
                            var d1 = new Date(<?= $exTglNowYear.','.$exTglNowMonth.','.$exTglNowDate; ?>);
                            // return d1<=date && date<=d2;
                            return now>=date;
                          }
                        });
                      });
                  </script>
                    <tr>
                      <td> Tanggal Aktual Pekerjaan </td>
                      <td>
                        <input type="text" name="reqTanggal" id="reqTanggal" title="" class="form-control easyui-datebox span2" value="<?= dateToPageCheck($datadelivery->getField('TANGGAL'))?>" required style="width: 200% !important" />
                        <span class="ml-3">Tanggal Realisasi: <span class="badge badge-primary"><?= getFormattedDateShort($datadelivery->getField('TANGGAL_DELIVERY_DARI')).' sd '.getFormattedDateShort($datadelivery->getField('TANGGAL_DELIVERY_SAMPAI')) ?></span></span>
                      </td>
                    </tr>
                    <tr>
                      <td> Tanggal Laporan Selesai</td>
                      <td>
                        <input type="text" name="reqTanggalTerima" id="reqTanggalTerima" title="" class="form-control easyui-datebox span2" value="<?= dateToPageCheck($datadelivery->getField('TANGGAL_TERIMA'))?>" style="width: 200% !important" />
                      </td>
                    </tr>
                    <tr>
                      <td> Presentase (%) </td>
                      <td>
                        <input type="text" name="reqPersentase" class="form-control easyui-validatebox span2"  value="<?= $datadelivery->getField('PRESENTASE') ? $datadelivery->getField('PRESENTASE') : $datapayment->getField("PAY_PROGRES") ?>" style="width:100px" maxlength="3"/>
                      </td>
                    </tr>
                    <tr>
                      <td> Catatan </td>
                      <td>
                        <input type="text" name="reqKeterangan" class="form-control easyui-validatebox span2"  value="<?= $datadelivery->getField('KETERANGAN') ?>" maxlength="255"/>
                      </td>
                    </tr>
                    <tr>
                      <td> File </td>
                      <td> 
                        <input type="file" name="reqLinkFile" id="reqLinkFile" size="30" class="easyui-validatebox span9" validType="fileType['pdf']"/><br>
                        <?= UPLOAD_PDF_ZIP_2MB ?>
                        <input type="hidden" name="reqLinkFileTemp" id="reqLinkFileTemp" value="<?= $datadelivery->getField('FILE_NAMA') ?>" />
                        <?php 
                        if (file_exists('uploads/kontrak/'.$datadelivery->getField('FILE_NAMA')) && $datadelivery->getField('FILE_NAMA') != '' ) {
                          echo '<br><a href="uploads/kontrak/'.$datadelivery->getField('FILE_NAMA').'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> Download </span></a>';
                        } ?>
                      </td> 
                    </tr>
                    <tr>
                      <td> Status </td>
                      <td>
                        <select class="form-control" name="status" style="width:25%">
                         <option <?php if ($status == 'Proses') { echo "selected"; } ?> value="Proses">Proses</option>
                         <option <?php if ($status == 'Selesai') { echo "selected"; } ?> value="Selesai">Selesai</option>
                        </select>
                      </td>
                    </tr>
                    <?php
                    $no++;
                    } ?>
                  </tbody>
                </table>
              </div>

              <div class="form-actions">
                <input type="hidden" name="deliverableid" id="deliverableid" value="<?=$reqId?>"/>
                <input type="hidden" name="reqContractingRekananId" id="reqContractingRekananId" value="<?=$reqConRekId?>"/>
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
