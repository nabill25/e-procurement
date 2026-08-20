<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
if($this->USER_TYPE_ID == "")
    redirect("main");

$this->load->model(array("Paket","Contractingrekanan"));

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqId  = $this->input->get("reqId"); // paketid
$reqContractId  = $this->input->get("reqContractId"); // Contract ID

$permohonan_paket = new Paket();
$spkpks = new Contractingrekanan();

$permohonan_paket->selectByParams(array("PAKET_ID" => $reqId));
$permohonan_paket->firstRow();
$reqSP = $permohonan_paket->getField("STRATEGI_PENGADAAN");

$spkpks->selectProses1(array("A.CONTRACTINGREKANANID" => $reqContractId));
$spkpks->firstRow();
$reqPICPenyelesai = $spkpks->getField('PIC_PENYELESAIAN') ?: '';

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
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />
    <script src="lib/emodal/eModal.js"></script>
    <script type="text/javascript">
    function openAdd(pageUrl) {
      eModal.iframe(pageUrl, '<?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?>')
    }
    function closePopup() {
      eModal.close();
    }
    $(function(){
      $('#ff').form({
        url:'contracting_json/tunjuk_pic_penyelesai',
        onSubmit:function(){
          return $(this).form('validate');
        },
        success:function(data){
           $.messager.alert('Info', data, 'info');
           setTimeout(function () {
            top.reloadMonitoring();
            top.closePopup();
           }, 2000);
        }
      });

    }); 
  </script>

  </head>
<body style="background-color: #fff;">
<div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>PIC Penyelesaian</strong>
        </div>
        <div class="p-1">
          <?php
          // $this->load->library("libpaket");
          // $libpaket = new libpaket();
          // echo $libpaket->detail($reqId);

          $this->load->library("libkontrak");
          $libkontrak = new libkontrak();
          echo $libkontrak->getInfoKontrak($reqContractId);
          ?>
         <form id="ff" method="post" class="form-horizontal" role="form">
            <table class="table table-bordered table-hover p-1">
                <tbody>
                  <tr>
                    <td width="25%">PIC</td>
                    <td width="75%"> 
                        <input type="text" class="easyui-combobox" name="reqPIC" title="PIC harus diisi" data-options=" required: true, filter: function(q, row) { return row['text'].toLowerCase().indexOf(q.toLowerCase()) != -1; }, valueField: 'id', textField: 'text', url: 'contracting_json/pic_penyelesai_combo_json/?unitkerja=<?=$this->UNIT_KERJA_ID?>'" style="width:300px;" value="<?= $reqPICPenyelesai ?>"> 
                    </td>
                  </tr>
                </tbody>
             </table> 

            <div class="form-actions">
              <input type="hidden" name="reqId" value="<?=$reqId?>"> <!-- paket_id --> 
              <input type="hidden" name="reqContractId" value="<?=$reqContractId?>"> <!-- paket_id --> 
              <input type="hidden" name="reqMode" value="<?=isset($reqMode)?$reqMode:''?>">
              <a href="#" onClick="top.closePopup()" class="<?= CLASS_BTN_DANGER ?>"> <i class="fa fa-close"></i> Tutup</a>
              <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-check-square-o"></i> Simpan</button>
            </div>
            <!--<button onClick="top.closePopup()">hai</button> -->
         </form>

        </div>
      </div>
    </div>
  </div>

  <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
  <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
  <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
  <script type="text/javascript" src="lib/eproc/jquery.easyui.min.js"></script>
  <script type="text/javascript" src="lib/eproc/kalender-easyui.js"></script>
  <script type="text/javascript" src="lib/eproc/allfunc.js"></script>

  </body>
</html>
