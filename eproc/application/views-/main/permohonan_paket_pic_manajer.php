<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
if($this->USER_TYPE_ID == "")
    redirect("main");

$this->load->model(array("PermohonanPaket"));

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqId  = $this->input->get("reqId"); // permohonan_paket_analisa_id
$sirupId  = $this->input->get("sirupId"); // kode_sirup
$permohonanId  = $this->input->get("permohonanId"); // permohonan_paket_id

$permohonan_paket = new PermohonanPaket();

$permohonan_paket->selectByParamsPermohonanLelang(array("PERMOHONAN_PAKET_ID" => $permohonanId));
$permohonan_paket->firstRow();
$reqSP = $permohonan_paket->getField("STRATEGI_PENGADAAN");
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
        url:'permohonan_paket_json/tunjuk_pic',
        onSubmit:function(){
          return $(this).form('validate');
        },
        success:function(data){
           $.messager.alert('Info', data, 'info');
           top.reloadMonitoring();
           top.closePopup();
           top.getNotif();
        }
      });

    });

    function createRowDokumenPanitia()
    {
      $(function () {
        $.get("main/loadUrl/main/panitia_add_template/?reqUnitKerja="+$("#reqUnitKerja").val(), function (data) {
          $("#tbDataDokumenPanitia").append(data);
        });
      });
    }

  $(document).ready(function() {
    $('input:radio[name=reqMetodePengadaan]').change(function() {
      if (this.value == '0') {
        setPanitia(0);
        $('#ketStrategi').html("<span class=\"badge badge-secondary\">Penunjukan Langsung</span> <span class=\"badge badge-secondary\">Tender Cepat</span> <span class=\"badge badge-secondary\">Tender</span> <span class=\"badge badge-secondary\">Tender Kualifikasi</span> <span class=\"badge badge-secondary\">Tender Terbatas</span> <span class=\"badge badge-secondary\">Seleksi</span> <span class=\"badge badge-secondary\">Kompetisi</span>");
      }
      else if (this.value == '2') {
        setPanitia(2);
        $('#ketStrategi').html("<span class=\"badge badge-secondary\">Pengadaan Langsung</span> <span class=\"badge badge-secondary\">Pembelian Langsung</span> <span class=\"badge badge-secondary\">Pembelian Katalog</span> <span class=\"badge badge-secondary\">Pembelian Katalog Pemerintah</span> ");
        // $('#panitiaSourching').hide();
        // $('#panitiaPurchasing').show();
      } else {
        $('#ketStrategi').html("<span class=\"badge badge-secondary\">Transaksi < 25 juta</span>");
      }
    });
  });

  function setPanitia(a)
  {
    $(function () {
      $.get("main/loadUrl/main/panitia_pic_template?reqUnitKerja=<?= $reqUnitKerjaId ?>&reqSourching="+a+"", function (data) {
        $("#panitiaSourching").html(data);
      });
    });
  }
  </script>

  </head>

<div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Persiapan</strong>
        </div>
        <div class="p-1">
          <?php
          $this->load->library("libplanning");
          $libplanning = new libplanning();
          echo $libplanning->permohonanHeader($reqId,$permohonanId,$sirupId);
          ?>
         <form id="ff" method="post" class="form-horizontal" role="form">
            <table class="table table-bordered table-hover p-1">
                <tbody>
                  <tr>
                    <?php 
                    $no=1;
                    if ($reqSP == '1') { // Non Tender ?>
                    <td width="25%"><b>PIC</b></td>
                    <td width="75%">
                        <input type="text" class="easyui-combobox" name="reqPIC" id="reqNama<?=$no?>" title="Nama harus diisi" data-options=" required: true, filter: function(q, row) { return row['text'].toLowerCase().indexOf(q.toLowerCase()) != -1; }, valueField: 'id', textField: 'text', url: 'panitia_json/panitia_combo_json_pl/?userloginid=<?=$reqUserLoginId?>&unitkerja=<?=$this->UNIT_KERJA_ID?>'" value=""  style="width:300px;">
                      <?php 
                      } else if ($reqSP == 'Purchasing') { // Purchasing ?>
                    <td width="25%"><b>PIC</b></td>
                    <td width="75%">
                        <input type="text" class="easyui-combobox" name="reqPIC" id="reqNama<?=$no?>" title="Nama harus diisi" data-options=" required: true, filter: function(q, row) { return row['text'].toLowerCase().indexOf(q.toLowerCase()) != -1; }, valueField: 'id', textField: 'text', url: 'panitia_json/panitia_combo_json_purchase/?userloginid=<?=$reqUserLoginId?>&unitkerja=<?=$this->UNIT_KERJA_ID?>'" value=""  style="width:300px;">
                      <?php 
                      } else if ($reqSP == 'Sourcing') { // Tender ?> 
                    <td width="25%"><b>Ketua</b></td>
                    <td width="75%">
                        <input type="text" class="easyui-combobox" name="reqPIC" id="reqNama<?=$no?>" title="Nama harus diisi" data-options=" required: true, filter: function(q, row) { return row['text'].toLowerCase().indexOf(q.toLowerCase()) != -1; }, valueField: 'id', textField: 'text', url: 'panitia_json/panitia_combo_json_tender/?unitkerja=<?=$this->UNIT_KERJA_ID?>'" value=""  style="width:300px;">
                      <?php
                      } ?>
                    </td>
                  </tr>
                </tbody>
             </table>

            <?php
            $this->load->library("libplanning");
            $libplanning = new libplanning();
            echo $libplanning->headerPermohonanDokumenForPJPKPP($reqId);
            ?>

            <div class="form-actions">
              <input type="hidden" name="reqId" value="<?=$reqId?>"> <!-- permohonan_paket_analisa_id -->
              <input type="hidden" name="reqPermohonanId" value="<?=$permohonanId?>"> <!-- permohonan_paket_id -->
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
