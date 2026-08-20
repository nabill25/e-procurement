<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->load->model("Paketnegosiasiitem");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* VARIABLE */
$reqId  = $this->input->get("reqId") ?: '0'; // Paket Negosiasi Item ID

/* create objects */
$negosiasiitem = new Paketnegosiasiitem();
$negosiasiitem->selectByParams(array("PAKET_NEGOSIASI_ITEM_ID" => $reqId));
$negosiasiitem->firstRow();
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
    <script>
    function openAdd(pageUrl) {
        eModal.iframe(pageUrl, '<?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?>')
      }
  	function closePopup() {
  		eModal.close();
  	}
      </script>
      <script type="text/javascript">
  	$(function(){
  		$('#ff').form({
  			url:'paket_lelang_tambah_negosiasi_item_json/edit',
  			onSubmit:function(){
  				return $(this).form('validate');
  			},
  			success:function(data){
			    $.messager.alert('Info', data, 'info');
          setTimeout(function () {
            top.reloadMonitoringReload();
            top.closePopup();
           }, 2000);
  			}
  		});
  	});
    </script>

  </head>

<body class="body-popup" style="background-color: #fff;">

  <div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Item Negosiasi</strong>
        </div>
        <div class="p-1">
          <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data" style="padding:0 50px"> 
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label>Uraian</label>
                <input title="Uraian harus diisi" class="form-control easyui-validatebox span3"  name="reqUraian" type="text" id="reqUraian" value="<?= $negosiasiitem->getField("URAIAN"); ?>" required />
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-4 mb-2">
                <label>Volume</label>
                <input title="Volume harus diisi" class="form-control easyui-validatebox span3"  name="reqVolume" type="text" id="reqVolume" value="<?= $negosiasiitem->getField("VOLUME"); ?>" onkeypress="return isNumberKey(event)" OnFocus="calculateAll(this)" OnKeyUp="calculateAll(this)" OnBlur="calculateAll(this)" required />
              </div>
              <div class="form-group col-md-8 mb-2">
                <label>Satuan</label>
                <input title="Satuan harus diisi" class="form-control easyui-validatebox span3"  name="reqSatuanVolume" type="text" id="reqSatuanVolume" value="<?= $negosiasiitem->getField("SATUAN_VOLUME"); ?>" required />
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-4 mb-2">
                <label>Durasi</label>
                <input title="Durasi harus diisi" class="form-control easyui-validatebox span3"  name="reqDurasi" type="text" id="reqDurasi" value="<?= $negosiasiitem->getField("DURASI"); ?>" onkeypress="return isNumberKey(event)" OnFocus="calculateAll(this)" OnKeyUp="calculateAll(this)" OnBlur="calculateAll(this)" required />
              </div>
              <div class="form-group col-md-8 mb-2">
                <label>Satuan</label>
                <input title="Satuan harus diisi" class="form-control easyui-validatebox span3"  name="reqSatuanDurasi" type="text" id="reqSatuanDurasi" value="<?= $negosiasiitem->getField("SATUAN_DURASI"); ?>" required />
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-6 mb-2">
                <label>Harga Satuan</label>
                <input title="Harga Satuan harus diisi" OnFocus="FormatAngka('reqHargaSatuan'); calculateHargaSatuan(this)" OnKeyUp="FormatUang('reqHargaSatuan'); calculateHargaSatuan(this)" OnBlur="FormatUang('reqHargaSatuan'); calculateHargaSatuan(this)" class="form-control easyui-validatebox span3"  name="reqHargaSatuan" type="text" id="reqHargaSatuan" value="<?= $negosiasiitem->getField("HARGA_SATUAN"); ?>" required  onchange="calculateHargaSatuan(this)"/>
              </div>
              <div class="form-group col-md-6 mb-2">
                <label>Jumlah Harga Satuan</label>
                <input title="Jumlah Harga Satuan harus diisi" OnFocus="FormatAngka('reqJumlahHarga')" OnKeyUp="FormatUang('reqJumlahHarga')" OnBlur="FormatUang('reqJumlahHarga')" class="form-control easyui-validatebox span3"  name="reqJumlahHarga" type="text" id="reqJumlahHarga" value="<?= $negosiasiitem->getField("JUMLAH_HARGA"); ?>" required />
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-6 mb-2">
                <label>Harga Penawaran</label>
                <input title="Harga Penawaran harus diisi" OnFocus="FormatAngka('reqNilaiPenawaran'); ; calculateHargaPenawaran(this)" OnKeyUp="FormatUang('reqNilaiPenawaran'); calculateHargaPenawaran(this)" OnBlur="FormatUang('reqNilaiPenawaran'); calculateHargaPenawaran(this)" class="form-control easyui-validatebox span3"  name="reqNilaiPenawaran" type="text" id="reqNilaiPenawaran" value="<?= $negosiasiitem->getField("NILAI_PENAWARAN"); ?>" required />
              </div>
              <div class="form-group col-md-6 mb-2">
                <label>Jumlah Harga Penawaran</label>
                <input title="Jumlah Harga Penawaran harus diisi" OnFocus="FormatAngka('reqJumlahPenawaran')" OnKeyUp="FormatUang('reqJumlahPenawaran')" OnBlur="FormatUang('reqJumlahPenawaran')" class="form-control easyui-validatebox span3"  name="reqJumlahPenawaran" type="text" id="reqJumlahPenawaran" value="<?= $negosiasiitem->getField("JUMLAH_PENAWARAN"); ?>" required />
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-6 mb-2">
                <label>Nilai Negosiasi</label>
                <input title="Nilai Negosiasi harus diisi" OnFocus="FormatAngka('reqNilaiNegosiasi'); calculateNilaiNegosiasi(this)" OnKeyUp="FormatUang('reqNilaiNegosiasi'); calculateNilaiNegosiasi(this)" OnBlur="FormatUang('reqNilaiNegosiasi'); calculateNilaiNegosiasi(this)" class="form-control easyui-validatebox span3"  name="reqNilaiNegosiasi" type="text" id="reqNilaiNegosiasi" value="<?= $negosiasiitem->getField("NILAI_NEGOSIASI"); ?>" required />
              </div>
              <div class="form-group col-md-6 mb-2">
                <label>Jumlah Negosiasi</label>
                <input title="Jumlah Negosiasi harus diisi" OnFocus="FormatAngka('reqJumlahNegosiasi')" OnKeyUp="FormatUang('reqJumlahNegosiasi')" OnBlur="FormatUang('reqJumlahNegosiasi')" class="form-control easyui-validatebox span3"  name="reqJumlahNegosiasi" type="text" id="reqJumlahNegosiasi" value="<?= $negosiasiitem->getField("JUMLAH_NEGOSIASI"); ?>" required />
              </div>
            </div>
            <div class="form-actions">
              <input type="hidden" name="reqId" value="<?=$reqId?>"> 
              <a href="#" onClick="top.closePopup()" class="btn round btn-min-width box-shadow-1 btn-danger mr-1 text-white"> <i class="fa fa-close"></i> Tutup</a>
              <button type="submit" class="btn round btn-min-width box-shadow-1 btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

	<link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
	<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
	<script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>
  <script type="text/javascript">

    function calculateAll(a) {
      var reqVolume = document.getElementById('reqVolume').value;
      var reqDurasi = document.getElementById('reqDurasi').value;
      nilaireqHargaSatuan = parseFloat(document.getElementById('reqHargaSatuan').value.split('.').join(""));
      nilaireqHargaSatuan = reqVolume * reqDurasi * nilaireqHargaSatuan; 
      $('#reqJumlahHarga').val(FormatNumberya(nilaireqHargaSatuan));

      nilaireqNilaiPenawaran = parseFloat(document.getElementById('reqNilaiPenawaran').value.split('.').join(""));
      nilaireqNilaiPenawaran = reqVolume * reqDurasi * nilaireqNilaiPenawaran; 
      $('#reqJumlahPenawaran').val(FormatNumberya(nilaireqNilaiPenawaran));

      nilaireqNilaiNegosiasi = parseFloat(document.getElementById('reqNilaiNegosiasi').value.split('.').join(""));
      nilaireqNilaiNegosiasi = reqVolume * reqDurasi * nilaireqNilaiNegosiasi; 
      $('#reqJumlahNegosiasi').val(FormatNumberya(nilaireqNilaiNegosiasi));
    }
    function calculateHargaSatuan(a)
    {  
      var reqVolume = document.getElementById('reqVolume').value;
      var reqDurasi = document.getElementById('reqDurasi').value;

      nilaiParsing = parseFloat(a.value.split('.').join(""));
      nilai = reqVolume * reqDurasi * nilaiParsing; 
      $('#reqJumlahHarga').val(FormatNumberya(nilai));
    }
    function calculateHargaPenawaran(a)
    { 
      var reqVolume = document.getElementById('reqVolume').value;
      var reqDurasi = document.getElementById('reqDurasi').value;

      nilaiParsing = parseFloat(a.value.split('.').join(""));
      nilai = reqVolume * reqDurasi * nilaiParsing; 
      $('#reqJumlahPenawaran').val(FormatNumberya(nilai));
    }
    function calculateNilaiNegosiasi(a)
    { 
      var reqVolume = document.getElementById('reqVolume').value;
      var reqDurasi = document.getElementById('reqDurasi').value;

      nilaiParsing = parseFloat(a.value.split('.').join(""));
      nilai = reqVolume * reqDurasi * nilaiParsing; 
      $('#reqJumlahNegosiasi').val(FormatNumberya(nilai));
    }
    function FormatNumberya(id)
    {
       var a = parseFloat(id);
       var nilai = FormatCurrency(a);
       return nilai;
    }

    function isNumberKey(evt)
    {
      var charCode = (evt.which) ? evt.which : event.keyCode;
     // console.log(charCode);
        if (charCode != 46 && charCode != 45 && charCode > 31
        && (charCode < 48 || charCode > 57))
         return false;

      return true;
    }

  </script>
  </body>
</html>
