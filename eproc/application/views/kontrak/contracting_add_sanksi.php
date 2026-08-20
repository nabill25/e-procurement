<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("Contractingfile");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqId  = $this->input->get("reqAidi"); // contractingrekananid

/* create objects */
$contractingfile = new Contractingfile();

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
     <style type="text/css">
       #reqKodeSeachPenyediaautocomplete-list {
          position: relative;
          margin-top: 10px;
          background: #fff;
          width: 100%;
        }
        #reqKodeSeachPenyediaautocomplete-list div {
          margin: 5px;
        }
     </style>
    <script>
    function openAdd(pageUrl) {
        eModal.iframe(pageUrl, 'Eprocurement | <?= SYSTEM_NAME_PT ?>')
    }

    function closePopup() {
      eModal.close();
    }

    function closePopupReload() {
      eModal.close();
      location.reload();
    }
    </script>   
    <script type="text/javascript">
    $(function(){
      $('#ffAddDeliverable').form({
        url:'contracting_json/addSanksi',
        onSubmit:function(){
          var v=$(this).form('validate');
          if(v) // showLoad();  // show the message box
          return v;
        },
        success:function(data){
          window.top.location.reload();
        }
      });
      
    }); 

    function createRowSanksi()
    {
      $(function () {
        $.get("main/loadUrlKontrak/kontrak/data_sanksi_template", function (data) {
          $("#tbodySLA").append(data);
        });
      }); 
    }
 
  </script>

  <script type="text/javascript">
   function calculate(no)
    {
        nilaisanksi = document.getElementById('nilaisanksi'+no).value;
        nilaipekerjaan = document.getElementById('nilaipekerjaan'+no).value;
        hariterlambat = document.getElementById('hariterlambat'+no).value;

        nilaisanksiParsing = parseFloat(nilaisanksi.split('.').join(""));
        nilaipekerjaanParsing = parseFloat(nilaipekerjaan.split('.').join(""));
        hariterlambatParsing = parseFloat(hariterlambat.split('.').join(""));

        total = (nilaisanksiParsing/1000) * nilaipekerjaanParsing * hariterlambatParsing;

        $('#nilaidenda'+no).val(FormatNumberya(total));
    }
    function FormatNumberya(id)
    {
       var a = parseFloat(id);
       var nilai = FormatCurrency(a);
       return nilai;    
    }
  </script>

  </head>

<body class="body-popup"> 

     <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Sanksi / Denda</strong>
          </div> 
          <div class="p-1" >
            <form id="ffAddDeliverable" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data" style="padding:0 50px">
              <div class="table-responsive">
                 <a onclick="createRowSanksi()" class="<?= CLASS_BTN_PRIMARY ?> mb-2" style="color:#fff"> <span class="fa fa-plus text-white font-medium-2 icon-line-height" data-toggle="tooltip" data-placement="top" title=""></span> Tambah</a> 
                <table class="table table-bordered table mb-0">
                  <thead>
                    <tr>
                      <th width="100px">Nilai Sanksi /1000</th>
                      <th>Nilai / Bagian Pekerjaan </th>
                      <th width="220px">Hari Keterlambatan</th>
                      <th>Nilai Denda</th>
                      <th width="10px">Aksi</th>
                    </tr>
                  </thead> 
                  <tbody id="tbodySLA">  
                    <?php 
                    $this->load->model("Contractingsanksi");
                    $datasanksi = new Contractingsanksi();
                    $datasanksi->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
                    $no=21;
                    // $slastatusArr = array('');
                      while($datasanksi->nextRow()) { 
                        $status = str_replace(' ','',$datasanksi->getField('SLA_STATUS'));
                      ?>
                      <tr> 
                         <td>
                          <input type="text" class="form-control easyui-validatebox" required name="nilaisanksi[]" id="nilaisanksi<?=$no?>" OnFocus="FormatAngka('nilaisanksi<?=$no?>')" OnFocus="FormatAngka('nilaisanksi<?=$no?>')" OnKeyUp="FormatUang('nilaisanksi<?=$no?>')" OnBlur="FormatUang('nilaisanksi<?=$no?>')" maxlength="3" value="<?= $datasanksi->getField('NILAI_SANKSI') ?>" onchange="calculate('<?=$no?>');">
                         </td> 
                         <td>
                          <input type="text" class="form-control easyui-validatebox" required name="nilaipekerjaan[]" id="nilaipekerjaan<?=$no?>" value="<?= $datasanksi->getField('NILAI_PEKERJAAN') ?>" id="nilaipekerjaan<?=$no?>" value="" OnFocus="FormatAngka('nilaipekerjaan<?=$no?>')" OnKeyUp="FormatUang('nilaipekerjaan<?=$no?>')" OnBlur="FormatUang('nilaipekerjaan<?=$no?>')" onchange="calculate('<?=$no?>');">
                         </td>  
                         <td>
                          <input type="text" class="form-control easyui-validatebox" required name="hariterlambat[]" id="hariterlambat<?=$no?>" OnFocus="FormatAngka('hariterlambat<?=$no?>')" OnFocus="FormatAngka('hariterlambat<?=$no?>')" OnKeyUp="FormatUang('hariterlambat<?=$no?>')" OnBlur="FormatUang('hariterlambat<?=$no?>')" maxlength="3" value="<?= $datasanksi->getField('HARI_TERLAMBAT') ?>" onchange="calculate('<?=$no?>');">
                         </td> 
                         <td>
                          <input type="text" class="form-control easyui-validatebox" required name="nilaidenda[]" id="nilaidenda<?=$no?>" value="<?= $datasanksi->getField('NILAI_DENDA') ?>" id="nilaidenda<?=$no?>" value="" FormatAngka="FormatAngka('nilaidenda<?=$no?>')" OnKeyUp="FormatUang('nilaidenda<?=$no?>')" OnBlur="FormatUang('nilaidenda<?=$no?>')">
                         </td>  
                        <td>
                          <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
                         </td>
                      </tr>
                      <?php 
                      $no++;
                      }
                    ?>
                  </tbody>
                </table> 
              </div>

              <div class="form-actions">
                <input type="hidden" name="contractingrekananid" id="contractingrekananid" value="<?=$reqId?>"/>
                <input type="hidden" name="reqSubmit" id="reqSubmit" value="Submit"/>
                <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
              </div> 
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
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>
    
  </body>
</html>
