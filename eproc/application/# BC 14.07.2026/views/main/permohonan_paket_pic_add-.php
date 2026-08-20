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
$reqNamaPaket   = $permohonan_paket->getField("NAMA");
$reqNilai   = $permohonan_paket->getField("NILAI");
$reqKeterangan    = $permohonan_paket->getField("KETERANGAN");
$reqNomorPPA = $permohonan_paket->getField("NO_PPA");
$reqPL = $permohonan_paket->getField("PENGADAANLANGSUNG");
$reqUserLoginId = $permohonan_paket->getField("USER_LOGIN_ID");
$reqUnitKerjaId = $permohonan_paket->getField("UNIT_KERJA_ID");

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
      url:'permohonan_paket_json/tunjuk_pic',
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

<div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Permohonan Paket Lelang</strong>
        </div> 
        <div class="p-1">
         <form id="ff" method="post" class="form-horizontal" role="form">
            <div class="alert alert-info">Data Permohonan Paket Lelang</div>

            <table class="table table-bordered table-hover">
                <tbody>
                  <!-- <tr>
                    <td width="25%">No. SiRUP</td>
                    <td width="75%"><?=$reqNotaDinas?></td>
                  </tr> -->
                  <tr>
                    <td width="25%">No Disposisi</td>
                    <td width="75%"><?=$reqNomorPPA?></td>
                  </tr>
                  <tr>
                    <td width="25%">Tanggal Disposisi</td>
                    <td width="75%"><?=getFormattedDate($reqTanggal)?></td>
                  </tr>
                  <tr>
                    <td width="25%">Nama Paket</td>
                    <td width="75%"><?=$reqNamaPaket?></td>
                  </tr>
                  <tr>
                    <td width="25%">Nilai HPS</td>
                    <td width="75%"><?=numberToIna($reqNilai)?></td>
                  </tr>
                  <tr>
                    <td width="25%">Rincian BoQ</td>
                    <td width="75%"><?=$pesanHPS?></td>
                  </tr>
                  <tr>
                    <td width="25%">Dilaksanakan oleh</td>
                    <td width="75%">
                      <?php 
                    if ($reqPengadaanlangsung == '1') {
                      echo "Non Tender";
                    } else if ($reqPengadaanlangsung == '2') {
                      echo "Purchasing (Katalog)  ";
                    } else {
                      echo "Tender";
                    }
                    ?>
                        
                      </td>
                  </tr>
                  <!-- <tr>
                    <td width="25%">Keterangan</td>
                    <td width="75%"><?=$reqKeterangan?></td>
                  </tr> -->
                  <tr>
                    <td width="25%">PIC</td>
                    <td width="75%">
                      <?php 
                      $no=1;
                      if ($reqPL == '1') { // Non Tender ?>
                        <input type="text" class="easyui-combobox" name="reqPIC" id="reqNama<?=$no?>" title="Nama harus diisi" data-options=" required: true, filter: function(q, row) { return row['text'].toLowerCase().indexOf(q.toLowerCase()) != -1; }, valueField: 'id', textField: 'text', url: 'panitia_json/panitia_combo_json_pl/?userloginid=<?=$reqUserLoginId?>&unitkerja=<?=$this->UNIT_KERJA_ID?>'" value=""  style="width:300px;">
                      <?php 
                      } else if ($reqPL == '2') { // Purchasing ?>
                        <input type="text" class="easyui-combobox" name="reqPIC" id="reqNama<?=$no?>" title="Nama harus diisi" data-options=" required: true, filter: function(q, row) { return row['text'].toLowerCase().indexOf(q.toLowerCase()) != -1; }, valueField: 'id', textField: 'text', url: 'panitia_json/panitia_combo_json_purchase/?userloginid=<?=$reqUserLoginId?>&unitkerja=<?=$this->UNIT_KERJA_ID?>'" value=""  style="width:300px;">
                      <?php 
                      } else { // Tender ?> 
                        <input type="text" class="easyui-combobox" name="reqPIC" id="reqNama<?=$no?>" title="Nama harus diisi" data-options=" required: true, filter: function(q, row) { return row['text'].toLowerCase().indexOf(q.toLowerCase()) != -1; }, valueField: 'id', textField: 'text', url: 'panitia_json/panitia_combo_json_nopl/?unitkerja=<?=$this->UNIT_KERJA_ID?>'" value=""  style="width:300px;">
                      <?php
                      } ?>
                    </td>
                  </tr>
                </tbody>
             </table>     
            
            <div class="alert alert-info">File Upload</div>

            <table class="table table-striped table-hover" id="tbl_bidang">
              <tbody>
                <tr class="judul-kolom">
                  <th>Nama</th>
                  <!-- <th>Ukuran</th> -->
                  <!-- <th>Tipe</th> -->
                  <th style="text-align: center">Download</th>
                 </tr>
                    <?php
                    while($permohonan_paket_file->nextRow())
                    {
                    ?>
                     <tr>
                         <td><?=$permohonan_paket_file->getField("JUDUL")?></td>
                         <!-- <td><?php //round($permohonan_paket_file->getField("UKURAN")/1024, 2)?> kb</td> -->
                         <!-- <td><?php // $permohonan_paket_file->getField("TIPE")?></td> -->
                         <td style="text-align: center">
                          <?php 
                          if ($permohonan_paket_file->getField("TIPE")) { ?>
                          <a href="uploads/permohonan_paket/<?=$permohonan_paket_file->getField("PATH_FILE")?>" target="new"> <i class="fa fa-download"></i></a>
                          <?php 
                          } else { echo ""; } ?>
                        </td>
                    </tr> 
                     <?php
                     }
                     ?>
              </tbody>
            </table> 
            
            <div class="form-actions">
              <input type="hidden" name="reqId" value="<?=$reqId?>">
              <input type="hidden" name="reqMode" value="<?=isset($reqMode)?$reqMode:''?>">
              <a href="#" onClick="top.closePopup()" class="btn btn-danger mr-1 text-white"> <i class="fa fa-close"></i> Tutup</a> 
              <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
            </div> 
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
