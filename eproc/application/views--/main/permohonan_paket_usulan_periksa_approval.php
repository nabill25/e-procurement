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
$this->load->model("PermohonanPaketAnalisaFile");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* VARIABLE */
$reqId  = $this->input->get("usulanId");

$this->load->model("PermohonanPaket");
$this->load->model("PermohonanPaketAnalisaFile");

$permohonan_paket_analisa_file = new PermohonanPaketAnalisaFile();
$permohonan_paket = new PermohonanPaket();

$permohonan_paket->selectByParamsUsulan(array("PERMOHONAN_PAKET_ANALISA_ID" => $reqId));
$permohonan_paket->firstRow();

$reqPermohonanPaketAnalisaId = $permohonan_paket->getField("PERMOHONAN_PAKET_ANALISA_ID");
$reqPermohonanPaketId = $permohonan_paket->getField("PERMOHONAN_PAKET_ID");
$reqPerkiraanBiayaHarga = $permohonan_paket->getField("PERKIRAAN_BIAYA_HARGA");
$reqKategoriId = $permohonan_paket->getField("KATEGORI");

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
    window.onload=setPPKom;
    function setPPKom() { $('#notifPPKom').html('PPKom wajib di isi'); }

    function openAdd(pageUrl) {
        eModal.iframe(pageUrl, '<?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?>')
    }
    function closePopup() {
        eModal.close();
    }

    <?php 
    if ($reqPerkiraanBiayaHarga < 2000000000) { 
    ?>
      $(document).ready(function() {

        $(function(){
          $('#ff').form({
            url:'permohonan_paket_usulan_json/kembali_permohonan',
            onSubmit:function(){
              var g = $('input[name="reqApprove"]:checked').val();
              var v=$(this).form('validate');
              if(v) { 
                var ppkom = $('#reqNama').combobox('getValue');
                if (g == '1') {
                  if (ppkom) {
                    $('#notifPPKom').html('');
                    return $(this).form('validate');
                  } else {
                    $('#notifPPKom').html('PPKom wajib di isi');
                    return false;
                  }
                } else {
                  return $(this).form('validate');
                }
              } 
            },
            success:function(data){
              $.messager.alert('Info', data, 'info');    
              top.reloadMonitoring();
              setTimeout(function () {
                top.closePopup();
              }, 2000);
            }
          });
        });

      });

      function handleChange(src) {
        if (src.value == '1') {
        // $('#reqNama').combobox('enable'); 
        $('#trPPKom').show(); 
        $('#notifPPKom').html('PPKom wajib di isi'); 
        } else {
        // $('#reqNama').combobox('disable'); 
        $('#trPPKom').hide(); 
        $('#notifPPKom').html('');
        }
      }
    <?php 
    } else { ?>
      $(document).ready(function() {

        $(function(){
          $('#ff').form({
            url:'permohonan_paket_usulan_json/kembali_permohonan',
            onSubmit:function(){ 
              return $(this).form('validate');
            },
            success:function(data){
              $.messager.alert('Info', data, 'info');    
              top.reloadMonitoring();
              setTimeout(function () {
                top.closePopup();
              }, 2000);
            }
          });
        });

      });

      function handleChange(src) { 
      }
    <?php 
    } ?>
    </script>    
   
  </head>

<body class="body-popup">

<div class="card mb-1 border-darken-1">
  <div class="card-content">
    <div class="p-1">
      
        <?php 
        $this->load->library("libplanning"); 
        $libplanning = new libplanning(); 
        echo $libplanning->headerUsulan($reqId); 
        ?>
      
         <form id="ff" method="post" class="form-horizontal" role="form">
          <div class="alert bg-success alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Cek Kebutuhan</strong>
          </div> 
          <table class="table table-striped table-hover" id="tbl_bidang">
            <tr>
              <td> 
                <?php 
                if ($reqPerkiraanBiayaHarga < 2000000000) { 
                ?>
                  <input value="1" name="reqApprove" id="reqApprove" onchange="handleChange(this);" type="radio" style="cursor:pointer" checked="" /> &nbsp; Setujui menjadi Rencana Pengadaan &nbsp;&nbsp;
                <?php 
                } else {  // >2M ?>
                  <input value="52" name="reqApprove" id="reqApprove" onchange="handleChange(this);" type="radio" style="cursor:pointer" checked="" /> &nbsp; Setujui dan teruskan ke KPA &nbsp;&nbsp;
                <?php 
                } ?>

                <?php 
                if ($reqKategoriId == '1') { // '1' Reguler
                ?>
                  <input value="3251" name="reqApprove" id="reqApprove" onchange="handleChange(this);" type="radio" style="cursor:pointer"/> &nbsp; Kembalikan ke Verifikator
                <?php 
                } else { // Insidental?>
                  <input value="42251" name="reqApprove" id="reqApprove" onchange="handleChange(this);" type="radio" style="cursor:pointer"/> &nbsp; Kembalikan ke Validator Keuangan
                <?php 
                } ?>

              </td>
            </tr>

            <?php 
                if ($reqPerkiraanBiayaHarga < 2000000000) { 
                ?>
                  <tr id="trPPKom">
                    <td>
                      <label>Pilih PPKom</label> <br>
                        <input type="text" class="easyui-combobox" name="reqPIC" id="reqNama" title="PPKom harus diisi" data-options="filter: function(q, row) { return row['text'].toLowerCase().indexOf(q.toLowerCase()) != -1; }, valueField: 'id', textField: 'text', url: 'panitia_json/panitia_combo_json_ppkom/?reqUnitKerja=<?=$this->UNIT_KERJA_ID?>'" value=""  style="width:400px;"> <br>
                      <span id="notifPPKom" style="color:red"></span>
                    </td>
                  </tr>
                <?php 
                } ?>
            <tr> 
              <td>Catatan <br>
                <!-- <b><small style="color: red"><u><i>Diisi jika usulan akan dikembalikan</i></u></small></b> -->
                  <textarea name="reqAlasan" rows="3" accesskey="l" id="reqAlasan" title="" class="textarea-tinymce easyui-validatebox" style="width:100%; height:150px"></textarea>
              </td>
            </tr> 
            <tr>
              <td>
                  <input type="hidden" name="reqPermohonanPaketAnalisaId" value="<?=$reqPermohonanPaketAnalisaId?>" />
                  <input type="hidden" name="reqNilai" value="<?=$reqPerkiraanBiayaHarga?>" />
                  <input type="hidden" name="reqPermohonanPaketId" value="<?=$reqPermohonanPaketId?>" />
                  <button type="submit" class="btn btn-primary "> <i class="fa fa-check-square-o"></i> Simpan</button> 
              </td>
            </tr>
           </table>
         </form>
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
    <script src="<?=base_url()?>lib/tinyMCE/tinymce.min.js"></script>
    <script type="text/javascript">
      tinymce.init({
        selector: "textarea.textarea-tinymce",
        plugins: "image",
        //plugins: [
        //  "advlist autolink lists link image charmap print preview anchor",
        //  "searchreplace visualblocks code fullscreen",
        //  "insertdatetime media table contextmenu paste"
        //],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
        menubar: false,

    });
    </script>
  </body>
</html>
