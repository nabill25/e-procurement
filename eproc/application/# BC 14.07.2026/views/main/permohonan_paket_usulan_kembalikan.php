<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
if($this->USER_TYPE_ID == "")
    redirect("main");
    
$this->load->model("PermohonanPaket");

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqId  = $this->input->get("reqId"); // PermohonanPaketAnalisaId 
$reqPerId  = $this->input->get("reqPerId"); // PermohonanPaketId 
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
            url:'permohonan_paket_usulan_json/kembali_permohonan_ke_staff',
            onSubmit:function(){
                return $(this).form('validate');
            },
            success:function(data){
                $.messager.alert('Info', data, 'info');    
                top.reloadMonitoring5();
                top.closePopup();
            }
        });
        
    }); 
    </script>   
   
  </head>

<body>
<div class="card mb-1 border-darken-1">
  <div class="card-content">
    <div class="p-1">
      <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
        <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Persiapan</strong>
      </div> 
      <div class="p-1">
         <form id="ff" method="post" class="form-horizontal" role="form"> 
            <table class="table table-bordered table-hover">
              <tbody> 
                <tr>
                  <td width="25%">Alasan DiKembalikan <br>
                    <b><small style="color: red"><u><i>Diisi jika paket akan dikembalikan</i></u></small></b>
                  </td>
                  <td width="75%">
                      <textarea name="reqAlasan" rows="3" accesskey="l" id="reqAlasan" title="Alamat harus diisi" class="form-control easyui-validatebox span8" required ></textarea>
                  </td>
                </tr> 
              </tbody>
             </table>     
            
            <div class="form-actions">
              <input type="hidden" name="reqId" value="<?=$reqId?>">
              <input type="hidden" name="reqPerId" value="<?=$reqPerId?>">
              <a href="#" onClick="top.closePopup()" class="<?= CLASS_BTN_DANGER ?>"> <i class="fa fa-close"></i> Tutup</a> 
              <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-check-square-o"></i> Simpan</button>
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
  </body>
</html>
