<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
if($this->USER_TYPE_ID == "")
    redirect("main");

/* INCLUDE FILE */
$this->load->model("PermohonanPaket");
$this->load->model("PermohonanPaketFile");
$this->load->model("SkPanitia");
$this->load->model("Panitia");
$this->load->model("PaketPenawaran");

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* VARIABLE */
$reqId  = $this->input->get("reqId");
$permohonan_paket_file = new PermohonanPaketFile();
$permohonan_paket = new PermohonanPaket();

$paket_penawaran = new PaketPenawaran();

$permohonan_paket->selectByParamsPermohonanLelang(array("PERMOHONAN_PAKET_ID" => $reqId));
$permohonan_paket->firstRow();

$paket_penawaran->selectByParams(array("A.PERMOHONAN_PAKET_ID" => $reqId, "ITEM_CHILD" => "0"));
$paket_penawaran->firstRow();
$ppi = $paket_penawaran->getField("PAKET_PENAWARAN_ID");
$boqFile = $paket_penawaran->getField("BOQ_FILE");
$reqPengadaanlangsung = $permohonan_paket->getField("PENGADAANLANGSUNG");
$reqBudgetAwal = $permohonan_paket->getField("BUDGET_AWAL");
$reqBudgetTerpakai = $permohonan_paket->getField("BUDGET_TERPAKAI");
$reqBudgetAkhir = $permohonan_paket->getField("BUDGET_AKHIR");
$reqTahunAnggaran = $permohonan_paket->getField("TAHUN_ANGGARAN");
$reqNotaDinas = $permohonan_paket->getField("NOTA_DINAS");
$reqTanggal   = $permohonan_paket->getField("TANGGAL");
$reqNamaPaket   = $permohonan_paket->getField("NAMA");
$reqNilai   = $permohonan_paket->getField("NILAI");
$reqKeterangan    = $permohonan_paket->getField("KETERANGAN");
$reqNomorPPA = $permohonan_paket->getField("NO_PPA");
$reqPL = $permohonan_paket->getField("PENGADAANLANGSUNG");
$reqUserLoginId = $permohonan_paket->getField("USER_LOGIN_ID");
$reqUnitKerjaId = $permohonan_paket->getField("UNIT_KERJA_ID");
$reqKodePR = $permohonan_paket->getField("KODE_PR");

$permohonan_paket_file->selectByParams(array("PERMOHONAN_PAKET_ID" => $reqId));
 

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

    <!-- PAGINATION -->
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
      url:'permohonan_paket_json/updatePROne',
      onSubmit:function(){
        return $(this).form('validate');
      },
      success:function(data){
        $.messager.alert('Info', data, 'info');
        top.reloadMonitoring();
        // top.closePopup();
      }
    });

  }); 
  </script>

  </head>

<div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Rencana Pengadaan</strong>
        </div>
        <div class="p-1">
         <form id="ff" method="post" class="form-horizontal" role="form">
            <?php 
            $this->load->library("libplanning"); 
            $libplanning = new libplanning(); 
            echo $libplanning->headerPermohonan($reqId); 
            ?>   
            <?php 
            if ($reqKodePR) 
            { ?>
            <div class="form-group col-md-6 mb-2">
              <label>No. PR</label>
              <input type="text" name="reqKodePR" value="<?=$reqKodePR?>" title="" class="form-control easyui-validatebox span9" required >
            </div>

            <div class="form-actions">
              <input type="hidden" name="reqId" value="<?=$reqId?>">
              <input type="hidden" name="reqMode" value="<?=isset($reqMode)?$reqMode:''?>">
              <a href="#" onClick="top.closePopup()" class="<?= CLASS_BTN_DANGER ?>"> <i class="fa fa-close"></i> Tutup</a>
              <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-check-square-o"></i> Simpan</button>
            </div>
            <?php 
            } else {
              echo '<span class="alert alert-danger">No. PR belum ada, silahkan gunakan tombol Cek. PR untuk input No. PR</span>';
            } ?>
            <!--<button onClick="top.closePopup()">hai</button> -->
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
    <link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <script type="text/javascript" src="lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="lib/eproc/allfunc.js"></script>

  </body>
</html>
