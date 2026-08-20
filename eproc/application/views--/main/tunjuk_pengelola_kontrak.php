<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$paketid = $this->input->get("paketid");

$this->load->model("Contracting");
$getPPK = new Contracting();
$getPPK->selectByParams(array("PAKET_ID" => $paketid));
$getPPK->firstRow();
$ppk = $getPPK->getField("PPK");

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
    <link href="lib/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="css/core.css" type="text/css">
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,300,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">
    <link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>

    <script type="text/javascript">
      $(function(){
        $('#ff').form({
          url:'paket_json/paket_lelang_tunjuk_ppk',
          onSubmit:function(){
            return $(this).form('validate');
          },
          success:function(data){
            $.messager.alert('Info', data, 'info');
            setTimeout(function() {
              window.top.location.reload();
            }, 1800);
          }
        });

      });
      $('.ui-autocomplete-input').css('width','100%')
    </script>
  </head>

  <body class="body-popup">

    <div class="modal-header" style="border-bottom:transparent !important;">
      <span class="alert alert-danger" style="width:100%">Pilih PJK</span>
    </div>  
    <div class="modal-body">
      <div class="p-1">

       <form id="ff" method="post" class="form-horizontal" role="form">
        <div class="row">
          <div class="form-group col-md-12 mb-2">
            <label>Pilih PJK</label><br>
            <input type="text" class="easyui-combobox ui-autocomplete-input" name="reqPPK" id="" title="Nama harus diisi" data-options=" required: true, filter: function(q, row) { return row['text'].toLowerCase().indexOf(q.toLowerCase()) != -1; }, valueField: 'id', textField: 'text', url: 'panitia_json/ppk_combo_json/?unitkerja=<?=$this->UNIT_KERJA_ID?>'" value="<?= $ppk ?>">
          </div>
        </div>
        <div class="form-actions">
          <input type="hidden" name="reqPaketId" id="reqPaketId" value="<?=$paketid?>"/>
          <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
        </div> 
       </form>
      </div>
    </div>

  <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
  <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
  <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
  <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
  <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>
  <script src="<?=base_url()?>lib/tinyMCE/tinymce.min.js"></script>

  </body>
</html>
