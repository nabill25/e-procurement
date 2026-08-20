<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("BidangUsaha");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$bidang_usaha		= new BidangUsaha();
$submitSimpan = $this->input->post("submitSimpan");
$reqParentId	= $this->input->get("reqParentId");
$reqId			= $this->input->get("reqId");

$reqBidangTahun 	= $this->input->post("reqBidangTahun");
$reqBidangUsaha 		= $this->input->post("reqBidangUsaha");
$reqBidangId 		= $this->input->post("reqBidangId");

$tempBidangTahun	 = $reqBidangTahun ;
$tempBidangUsaha	 	 = $reqBidangUsaha ;
 

$bidang_usaha->selectByParamsAll(array("BIDANG_USAHA_ID"=>$reqId),-1,-1, '');
$bidang_usaha->firstRow();

$tempBidangUsaha = $bidang_usaha->getField('NAMA');
$tempBidangTahun = $bidang_usaha->getField('KODE');
$tempStatusBidangTahun = $bidang_usaha->getField('STATUS_BIDANG_USAHA');

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
    <?php 
    if ($reqId == 'tambah') { ?>
      <script type="text/javascript">
    	$(function(){
    		$('#ff').form({
    			url:'bidang_usaha_json/addCustom',
    			onSubmit:function(){
    				return $(this).form('validate');
    			},
    			success:function(data){
    				$.messager.alert('Info', data, 'info');
            top.reloadMonitoring();
    				setTimeout(function() {
                top.closePopup(); }, 1800);
    			}
    		});

    	});

      $(document).ready(function() {
        $('input:radio[name=reqBidangUsahaJenis]').change(function() {
          var a = this.value;
          $(function () {
            $.get("main/loadUrl/main/master_bidang_usaha_template?reqJenis="+a+"", function (data) {
              $("#setKode").html(data);
            });
          });
        });
      });

      </script>
    <?php 
    } else {
      ?>
      <script type="text/javascript">
      $(function(){
        $('#ff').form({
          url:'bidang_usaha_json/add',
          onSubmit:function(){
            return $(this).form('validate');
          },
          success:function(data){
            $.messager.alert('Info', data, 'info');
            top.reloadMonitoring();
            setTimeout(function() {
                top.closePopup(); }, 1800);
          }
        });

      });
      </script>
    <?php 
    } ?>
  </head>
  <style type="text/css">
    .fa.fa-trash {background: red; padding: 5px;border-radius: 10px;color: #da4453;}
  </style>

<body class="body-popup">

  <div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Tambah Master Bidang Usaha</strong>
        </div>
          <div class="p-1">

          <?php 
          if ($reqId == 'tambah') { ?> 

              <form id="ff" class="form-horizontal" role="form" method="post" novalidate style="padding:0 50px">
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <?php
                        $pl = array(
                          '1' => 'KBLI',
                          '99' => 'SBUJK',
                        );

                      foreach ($pl as $key => $value) {
                        if ('1' == $key) {
                          $checked = 'checked';
                        } else {
                          $checked = '';
                        }
                          ?>
                        <input value="<?= $key ?>" name="reqBidangUsahaJenis" id="reqBidangUsahaJenis-0" type="radio" <?= $checked ?>/>
                        &nbsp; <?= $value ?> &nbsp;
                      <?php
                      }
                      ?>
                  </div>
                  <div class="form-group col-md-2 mb-2">
                    <label>Sub Kode</label>
                    <div id="setKode">
                        <input type="text" class="easyui-combobox" name="reqBidangUsahaParentId"  title="Harus diisi" data-options=" required: true, filter: function(q, row) { return row['text'].toLowerCase().indexOf(q.toLowerCase()) != -1; }, valueField: 'id', textField: 'text', url: 'metode_json/metode_combo/?jenis=1'" value=""  style="width:150px;">
                      </div>
                  </div>
                  <div class="form-group col-md-2 mb-2">
                    <label>Kode</label>
                    <input type='text' name='reqKode' size='30' title="Kode harus diisi" value="" class="form-control easyui-validatebox span9" required>
                  </div>
                  <div class="form-group col-md-12 mb-2">
                    <label>Bidang Usaha</label>
                    <input type='text' name='reqNama' size='30' title="Bidang usaha harus diisi" value="" class="form-control easyui-validatebox span9" required>
                  </div>
                  <div class="form-group col-md-10 mb-2">
                    <label>Status</label>
                    <select name="reqStatusBidangUsaha" class="form-control easyui-validatebox span1" style="width: 200px">
                      <?php
                      if ($tempStatusBidangTahun == '1') { ?>
                        <option value="1" selected="">Aktif</option>
                        <option value="0">Non Aktif</option>
                      <?php
                      } else { ?>
                        <option value="1">Aktif</option>
                        <option value="0" selected="">Non Aktif</option>
                      <?php
                      } ?>
                    </select>
                  </div>
                </div>
                <div class="form-actions">
                  <button type="submit" class="btn btn-primary round"><i class="fa fa-check-square-o"></i> Simpan</button>
                </div>
              </form>

          <?php 
          } else { // Update
            ?>

              <form id="ff" class="form-horizontal" role="form" method="post" novalidate style="padding:0 50px">
                <div class="row">
                  <div class="form-group col-md-2 mb-2">
                    <label>Kode</label>
                    <input type='text' name='reqBidangTahun' id='reqBidangTahun' size='30' title="Kode harus diisi" value="<?=$tempBidangTahun?>" class="form-control easyui-validatebox span9" required>
                  </div>
                  <div class="form-group col-md-10 mb-2">
                    <label>Bidang Usaha</label>
                    <input type='text' name='reqBidangUsaha' id='reqBidangUsaha' size='30' title="Bidang usaha harus diisi" value="<?=$tempBidangUsaha?>" class="form-control easyui-validatebox span9" required>
                  </div>
                  <div class="form-group col-md-10 mb-2">
                    <label>Status</label>
                    <select name="reqStatusBidangUsaha" class="form-control easyui-validatebox span1" style="width: 200px">
                      <?php
                      if ($tempStatusBidangTahun == '1') { ?>
                        <option value="1" selected="">Aktif</option>
                        <option value="0">Non Aktif</option>
                      <?php
                      } else { ?>
                        <option value="1">Aktif</option>
                        <option value="0" selected="">Non Aktif</option>
                      <?php
                      } ?>
                    </select>
                    <!-- <input type='text' name='reqStatusBidangUsaha' id='reqStatusBidangUsaha' size='30' title="Aktif harus diisi" value="<?=$tempStatusBidangTahun?>" class="form-control easyui-validatebox span9" required> -->
                  </div>
                </div>
                <div class="form-actions">
                  <input type="hidden" name="reqBidangId" value="<?=$reqBidangId?>"/>
                  <input type="hidden" name="reqParentId" value="<?=$reqParentId?>"/>
                  <input type="hidden" name="reqId" value="<?=$reqId?>"/>
                  <input type="hidden" name="reqSubmit" id="reqSubmit" value="<?=$reqSubmit?>" />
                  <button type="submit" class="btn btn-primary round"><i class="fa fa-check-square-o"></i> Update</button>
                </div>
              </form>

          <?php 
          } ?>

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

  </body>
</html>
