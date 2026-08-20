<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
$paketid = $this->input->get("paketid");

$this->load->model("Paketpemenangpurchasing");
$pemenang = new Paketpemenangpurchasing();
$pemenang->selectByParams(array("PAKET_ID" => $paketid));
$pemenang->firstRow();
$id = $pemenang->getField("PAKET_PEMENANG_PURCHASING_ID");
$reqRekananId = $pemenang->getField("REKANAN_ID");
$reqPaketId = $pemenang->getField("PAKET_ID");
$reqNama = $pemenang->getField("NAMA");
$reqNPWP = $pemenang->getField("NPWP");
$reqTelepon = $pemenang->getField("TELEPON");
$reqAlamat = $pemenang->getField("ALAMAT");
$reqEmail = $pemenang->getField("EMAIL");
$reqJenis = $pemenang->getField("JENIS");
$reqNilaiPembelian = $pemenang->getField("NILAI_PEMBELIAN");

if ($id) {
  $aksi = 'update';
} else {
  $aksi = 'insert';
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
    <title><?= SYSTEM_NAME_PT ?></title>
    <!-- Bootstrap core CSS -->
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
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>

    <style type="text/css">
      #reqRekananId { width: 65% !important;}
    </style>
    <script type="text/javascript">
      $(function(){
        $('#ff').form({
          url:'paket_json/paket_lelang_tunjuk_penyedia',
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
      $('.ui-autocomplete-input').css('width','100%');


      $(document).ready(function() {

      <?php
      if ($aksi == 'update') {
        if ($reqJenis == '1') {
          echo "$('#IdRekananId').show();";
        } else {
          echo "$('#IdRekananId').hide();";
        }
      } else {
          echo "$('#IdRekananId').hide();";
      } ?>

        $('input:radio[name=reqJenis]').change(function() {
          if (this.value == '0') {
            $('#IdRekananId').hide();
            $('#reqRekananId').combobox('setValue', '');
            $('#reqRekananId').combobox({ required: false });
            $('#reqNama').val('');
            $('#reqAlamat').val('');
            $('#reqNPWP').val('');
            $('#reqTelepon').val('');
            $('#reqEmail').val('');
          }
          else if (this.value == '1') {
            $('#IdRekananId').show();
            $('#reqRekananId').combobox({ required: true });
          }
        });
      });
    </script>
  </head>

  <body class="body-popup">

    <div class="modal-body">
      <div class="p-1">

       <form id="ff" method="post" class="form-horizontal" role="form">
          <div class="row">
            <div class="col-md-12 mb-2">
              <label style="width: 100%">Jenis Penyedia</label>
              <input type="radio" <?php if($reqJenis == '' || $reqJenis == '0') echo 'checked';?>  name="reqJenis" value="0" required/> Vendor Retail &nbsp;&nbsp;&nbsp;
              <input type="radio" <?php if($reqJenis == '1') echo 'checked';?> name="reqJenis" value="1" required /> Dari VMS Terverifikasi
            </div>
          </div>
          <div class="row" id="IdRekananId">
            <div class="col-md-12 mb-2">
              <label>Pilih Penyedia dari VMS</label><br>
              <input type="text" class="easyui-combobox ui-autocomplete-input" id="reqRekananId" name="reqRekananId" id="" title="Nama harus diisi" data-options=" required: true, filter: function(q, row) { return row['text'].toLowerCase().indexOf(q.toLowerCase()) != -1; }, valueField: 'id', textField: 'text', url: 'panitia_json/vendor_combo_json', onSelect: function(rec){
                                            getComboA(this);
                                            }"  value="<?= $reqRekananId ?>">
            </div>
          </div>
          <div class="row" id="IdRekananId2">
            <div class="col-md-12 mb-2">
              <label>Pilih Penyedia dari Vendor Retail</label><br>
              <input type="text" class="easyui-combobox ui-autocomplete-input" id="reqRekananId2" name="reqRekananId2" id="" title="Nama harus diisi" data-options=" required: true, filter: function(q, row) { return row['text'].toLowerCase().indexOf(q.toLowerCase()) != -1; }, valueField: 'id', textField: 'text', url: 'panitia_json/vendor_retail_combo_json', onSelect: function(rec){
                                            getComboB(this);
                                            }"  value="<?= $reqRekananId ?>">
            </div>
          </div>
          <div class="row" id="IdNamaRekanan">
            <div class="col-md-12 mb-2">
              <label>Nama</label>
              <input name="reqNama" title="Nama Penyedia" class="form-control easyui-validatebox span4" type="text" id="reqNama" value="<?=$reqNama?>" required />
            </div>
          </div>
          <div class="row" id="IdNPWP">
            <div class="col-md-4 mb-2">
              <label>NPWP</label>
              <input name="reqNPWP" id="reqNPWP" title="NPWP" onkeydown="return format_npwp(event, 'reqNPWP');" maxlength="20"  class="form-control easyui-validatebox" type="text" id="txtNPWP" value="<?=$reqNPWP?>" />

            </div>
            <div class="col-md-4 mb-2">
              <label>Telepon</label>
              <input name="reqTelepon" title="Telepon" class="form-control easyui-validatebox" type="text" id="reqTelepon" value="<?=$reqTelepon?>" required />
            </div>
            <div class="col-md-4 mb-2">
              <label>Email</label>
              <input name="reqEmail" title="Email" class="form-control easyui-validatebox span4" type="email" id="reqEmail" value="<?=$reqEmail?>" />
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 mb-2">
              <label>Nilai</label>
              <input title="Nilai harus diisi" class="form-control easyui-validatebox span3"  name="reqNilaiPembelian" type="text" id="reqNilaiPembelian" value="<?=numberToIna($reqNilaiPembelian)?>"  OnFocus="FormatAngka('reqNilaiPembelian')" OnKeyUp="FormatUang('reqNilaiPembelian')" OnBlur="FormatUang('reqNilaiPembelian')" required/>
            </div>
          </div>
          <div class="row" id="IdAlamat">
            <div class="col-md-12 mb-2">
              <label>Alamat</label>
              <textarea name="reqAlamat" title="Alamat" class="form-control easyui-validatebox span4" type="text" id="reqAlamat"><?= $reqAlamat ?></textarea>
            </div>
          </div>

          <div class="form-actions" style="margin:0px !important">
            <input type="hidden" name="reqAksi" id="reqAksi" value="<?=$aksi?>"/>
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

  <script type="text/javascript">

    function getComboA(att) {
      var nv = $('#reqRekananId').combobox('getValue');
      $.getJSON("panitia_json/vendor_combo_detail_json/?reqRekananId="+nv,
      function(data){
        $('#reqNama').val(data.nama);
        $('#reqAlamat').val(data.alamat);
        $('#reqNPWP').val(data.npwp);
        $('#reqTelepon').val(data.telepon);
        $('#reqEmail').val(data.email);
      })
    }

    function getComboB(att) {
      var nv = $('#reqRekananId2').combobox('getValue');
      $.getJSON("panitia_json/vendor_retail_combo_detail_json/?reqRekananId2="+nv,
      function(data){
        $('#reqNama').val(data.nama);
        $('#reqAlamat').val(data.alamat);
        $('#reqNPWP').val(data.npwp);
        $('#reqTelepon').val(data.telepon);
        $('#reqEmail').val(data.email);
      })
    }
  </script>

  </body>
</html>
