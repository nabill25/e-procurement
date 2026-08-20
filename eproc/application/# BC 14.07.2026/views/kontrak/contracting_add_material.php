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
      $('#ffAddMaterial').form({
        url:'contracting_json/addMaterial',
        onSubmit:function(){
          var v=$(this).form('validate');
          if(v) { 
              return v;
          } else {
              return false;
          }
        },
        success:function(str){
          var isNotif = str.split("--");
          $.messager.alert('Info', isNotif[1], 'info'); 
          setTimeout(function () {
            window.top.location.reload();
          }, 1000);
        }
      });
      
    }); 

    function createRowMaterial()
    {
      $(function () {
        $.get("main/loadUrlKontrak/kontrak/data_material_template", function (data) {
          $("#tbodyDeliverable").append(data);
        });
      }); 
    }

    $(document).ready(function() {
      $('input:radio[name=reqSifat]').change(function() {
        if (this.value == '1') { // Tetap
          $('.check-qty').val('1');
          $('.check-qty').prop('readonly', true);
        }
        else if (this.value == '2') { // Berubah
          // $('.check-qty').val('');
          $('.check-qty').prop('readonly', false);
        }
      });
    });

    function test() {
      var sf = $('input[name="reqSifat"]:checked').val();
      return sf;
    }
 
  </script>
  </head>

<body class="body-popup"> 

     <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Daftar Barang Jasa</strong>
          </div> 
          <div class="p-1" >
            <form id="ffAddMaterial" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data" style="padding:0 50px">
              <div class="table-responsive">
                 <a onclick="createRowMaterial()" class="<?= CLASS_BTN_PRIMARY ?> mb-2" style="color:#fff"> <span class="fa fa-plus text-white font-medium-2 icon-line-height" data-toggle="tooltip" data-placement="top" title=""></span> Tambah</a> 
                <div class="col-md-12 mb-1">
                  <?php 
                  $this->load->model("Contractingmaterial");
                  $datamaterialRow = new Contractingmaterial();
                  $datamaterialRow->selectByParams(array("CONTRACTINGREKANANID"=>$reqId)); 
                  $datamaterialRow->firstRow(); 
                  $reqS = $datamaterialRow->getField('SIFAT') ?: '2';
                  ?>
                  <input type="radio" <?php if($reqS == '1') echo 'checked';?>  name="reqSifat" id="reqSifat" value="1" /> Volume bersifat Berubah<br>
                  <input type="radio" <?php if($reqS == '2') echo 'checked';?> name="reqSifat" id="reqSifat" value="2" /> Volume bersifat Tetap
                </div>
                <table class="table table-bordered table mb-0">
                  <thead>
                    <tr>
                      <!-- <th>Nama Barang Jasa<br>&nbsp;</th> -->
                      <th>Deskripsi<br>&nbsp;</th>
                      <th width="10%">Vol/Qty<br>&nbsp;</th>
                      <th width="10%">Satuan<br>&nbsp;</th>
                      <th width="20%">Harga Satuan <br><small>Sudah termasuk pajak yang berlaku</small></th>
                      <th style="width:10px">Aksi<br>&nbsp;</th>
                    </tr>
                  </thead> 
                  <tbody id="tbodyDeliverable">  
                    <?php 
                    $this->load->model("Contractingmaterial");
                    $datamaterial = new Contractingmaterial();
                    $datamaterial->selectByParams(array("CONTRACTINGREKANANID"=>$reqId));
                    $no=21;
                    while($datamaterial->nextRow()) { 
                    ?>
                    <tr>
                      <td>
                        <input type="text" class="form-control easyui-validatebox" required name="material[]" id="<?=$no?>" value="<?= $datamaterial->getField('NAMA') ?>">
                      </td> 
                      <td>
                        <input type="text" class="form-control easyui-validatebox check-qty" required name="qty[]" id="<?=$no?>" value="<?= $datamaterial->getField('QTY') ?>" <?php if ($reqS == '1') { echo "readonly"; } ?>>
                      </td> 
                      <td>
                        <input type="text"  name="satuanid[]" id="satuanid<?=$no?>" class="easyui-combobox" 
                              data-options=" required: true,
                                              filter: function(q, row) { return row['text'].toLowerCase().indexOf(q.toLowerCase()) != -1; },
                                              valueField: 'id', textField: 'text',
                                              url: 'contracting_json/comboSatuanData'
                                              " value="<?= $datamaterial->getField('SATUANID') ?>" required="required" style="width:100px;">
                      </td> 
                       <!-- <td>
                        <input type="text" class="form-control easyui-validatebox" required name="keterangan[]" id="<?php //$no?>" value="<?php // $datamaterial->getField('KETERANGAN') ?>">
                       </td>  -->
                      <td>
                        <input type="text" class="form-control easyui-validatebox" required name="hargasatuan[]" id="hargasatuan<?=$no?>" value="<?= $datamaterial->getField('HARGA_SATUAN') ?>" OnFocus="FormatAngka('hargasatuan<?=$no?>')" OnFocus="FormatAngka('hargasatuan<?=$no?>')" OnKeyUp="FormatUang('hargasatuan<?=$no?>')" OnBlur="FormatUang('hargasatuan<?=$no?>')">
                       </td> 
                      <td>
                        <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
                       </td>
                    </tr>
                    <?php 
                    $no++;
                    } ?>
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
