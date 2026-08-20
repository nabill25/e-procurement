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
$sk_panitia = new SKPanitia();
$panitia = new SKPanitia();

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

if ($reqPengadaanlangsung != '2') { // Selain Purchasing Wajib isi BoQ
  if ($boqFile == '') { 
    $pesanHPS = '<span class="badge badge-danger"><i class="fa fa-close"></i> Belum Lengkap</span>';   
  } else {
    $pesanHPS = '<span class="badge badge-info"><i class="fa fa-check"></i> Lengkap</span>
                 <a href="uploads/boq/'.$boqFile.'" class="badge badge-pill badge-primary" target="_blank">download</a>';   
  }
} else {
  $pesanHPS = '-';
}
// if ($ppi == '') { 
//   $pesanHPS = '<span class="badge badge-danger"><i class="fa fa-close"></i> Belum Lengkap</span>';   
// } else {
//   $pesanHPS = '<span class="badge badge-info"><i class="fa fa-check"></i> Lengkap</span>';   
// }

// $permohonan_paket->selectByParamsPermohonanLelang(array("PERMOHONAN_PAKET_ID" => $reqId));
// $permohonan_paket->firstRow();

$reqNotaDinas = $permohonan_paket->getField("NOTA_DINAS");
$reqTanggal   = $permohonan_paket->getField("TANGGAL");
$reqNamaPaket     = $permohonan_paket->getField("NAMA");
$reqNilai     = $permohonan_paket->getField("NILAI");
$reqKeterangan    = $permohonan_paket->getField("KETERANGAN");
$reqNomorPPA = $permohonan_paket->getField("NO_PPA");
$reqPL = $permohonan_paket->getField("PENGADAANLANGSUNG");

$permohonan_paket_file->selectByParams(array("PERMOHONAN_PAKET_ID" => $reqId));


$panitia = new Panitia();
$panitia->selectByParams(array("SK_PANITIA_ID" => $reqId));

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
    <!-- END VENDOR CSS-->
    <!-- BEGIN ROBUST CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <!-- <link rel="stylesheet" type="text/css" href="<?=base_url() ?>css/core.css"> -->
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">
    
    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">
    
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
            url:'permohonan_paket_json/kembali_permohonan',
            onSubmit:function(){
                return $(this).form('validate');
            },
            success:function(data){
                //alert(data);return false;
                 $.messager.alert('Info', data, 'info');    
                  top.reloadMonitoring();
                  top.closePopup();
               
               // top.frames['mainFrame'].location.reload();
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
    </script>   
   
  </head>

<body>
<div class="card mb-1 border-darken-1">
  <div class="card-content">
    <div class="p-1">
      <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
        <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Rencanan Pengadaan</strong>
      </div> 
      <div class="p-1">
         <form id="ff" method="post" class="form-horizontal" role="form"> 
            <?php 
            $this->load->library("libplanning"); 
            $libplanning = new libplanning(); 
            echo $libplanning->headerPermohonan($reqId); 
            ?> 
            <table class="table table-bordered table-hover">
              <tbody> 
                <tr>
                  <td width="25%">Alasan DiKembalikan <br>
                    <!-- <b><small>Diisi jika dikembalikan dan kosongkan jika lanjut</small></b> -->
                    <b><small style="color: red"><u><i>Diisi jika paket akan dikembalikan</i></u></small></b>
                  </td>
                  <td width="75%">
                      <textarea name="reqAlasan" rows="3" accesskey="l" id="reqAlasan" title="Alamat harus diisi" class="form-control easyui-validatebox span8" required ></textarea>
                  </td>
                </tr> 
              </tbody>
             </table>     
            
            <?php 
            $this->load->library("libplanning"); 
            $libplanning = new libplanning(); 
            echo $libplanning->headerPermohonanDokumen($reqId); 
            ?>
            
            <div class="form-actions">
              <input type="hidden" name="reqId" value="<?=$reqId?>">
              <input type="hidden" name="reqMode" value="<?= isset($reqMode) ? $reqMode : ''?>">
              <a href="#" onClick="top.closePopup()" class="<?= CLASS_BTN_DANGER ?>"> <i class="fa fa-close"></i> Tutup</a> 
              <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-check-square-o"></i> Simpan</button>
            </div> 
            <!-- div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="#" onClick="top.closePopup()" class="btn btn-danger">Batal</a>
            </div> -->
            <!--<button onClick="top.closePopup()">hai</button> -->
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
    
    
  </body>
</html>
