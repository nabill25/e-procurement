<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("KatalogKategori");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$katalog_kategori		= new KatalogKategori();

$submitSimpan = $this->input->post("submitSimpan");
$reqParentId	= $this->input->get("reqParentId");
$reqId			= $this->input->get("reqId") ?: '0';

$reqBidangTahun 	= $this->input->post("reqBidangTahun");
$reqKatalogKategori 		= $this->input->post("reqKatalogKategori");
$reqKategoriId 		= $this->input->post("reqKategoriId");

$tempBidangTahun	 = $reqBidangTahun ;
$tempKatalogKategori	 	 = $reqKatalogKategori ;

$max_bidang_id = new KatalogKategori();
$reqKategoriId = 1 + $max_bidang_id->getCountByParamsMaxBidangId();

if ($reqId == 'tambahParent') {
} elseif ($reqId == 'tambahChild') {
} else {
  $katalog_kategori->selectByParams(array("KATEGORI_ID"=>$reqId),-1,-1, '');
  $katalog_kategori->firstRow();

  $tempKategoriId = $katalog_kategori->getField('KATEGORI_ID');
  $tempNamaKategori1= $katalog_kategori->getField('NAMA_KATEGORI_1');
  $tempNamaKategori2 = $katalog_kategori->getField('NAMA_KATEGORI_2');
  $tempKategoriParentId = $katalog_kategori->getField('KATEGORI_PARENT_ID');
  $tempKode = $katalog_kategori->getField('KODE');
  $tempNama = $katalog_kategori->getField('NAMA');
  $tempKategoriStatus = $katalog_kategori->getField('KATEGORI_STATUS');
}

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
    if ($reqId == 'tambahParent') { ?>
      <script type="text/javascript">
      	$(function(){
      		$('#ff').form({
      			url:'katalog_kategori_json/addParent',
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
    } else { ?>
      <script type="text/javascript">
        $(function(){
          $('#ff').form({
            url:'katalog_kategori_json/add',
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

<body class="body-popup">

  <div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Tambah Katalog Kategori</strong>
        </div>
        <div class="p-1">
          <form id="ff" class="form-horizontal" role="form" method="post" novalidate style="padding:0 50px">
          <?php
          if ($reqId == 'tambahParent') { ?>
            <div class="row">

              <div class="form-group col-md-12 mb-2">
                  <label>Nama Kategori</label>
                  <input type='text' name='reqNamaKategori1' id='reqNamaKategori1' title="Bidang usaha harus diisi" value="" class="form-control easyui-validatebox span9">
                </div>
                <div class="form-group col-md-3 mb-2">
                  <label>Kode</label>
                  <input type='text' name='reqKode' id='reqKode' title="Kode harus diisi" value="<?=$tempKode?>" class="form-control easyui-validatebox span9">
                </div>
                <div class="form-group col-md-4 mb-2">
                  <label>Status</label>
                  <select name="reqKategoriStatus" class="form-control easyui-validatebox span1" style="width: 200px">
                    <?php
                    if ($tempKategoriStatus == '1') { ?>
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
                <input type="hidden" name="reqKategoriParentId" id="reqKategoriParentId" value="0" />
                <input type="hidden" name="reqSubmit" id="reqSubmit" value="tambah" />
                <button type="submit" class="btn round btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
              </div>

          <?php
          } elseif ($reqId == 'tambahChild') { ?>
              <div class="row">

                <div class="form-group col-md-12 mb-2">
                  <label>Nama Kategori 1</label>
                  <div id="setKode1">
                    <input type="text" class="easyui-combobox" name="reqNamaKategori1"  title="Harus diisi" data-options=" required: true, filter: function(q, row) { return row['text'].toLowerCase().indexOf(q.toLowerCase()) != -1; }, valueField: 'id', textField: 'text', url: 'katalog_kategori_json/combo/?level=1',
                                          onSelect: function(rec){
                                              $('#reqNamaKategori2').combobox('reload', 'katalog_kategori_json/combo/?level=2&id='+rec.id);
                                          }" value=""  style="width:450px;">
                  </div>
                </div>

                <div class="form-group col-md-12 mb-2">
                  <label>Nama Kategori 2</label>
                  <div id="setKode1">
                    <input type="text" class="easyui-combobox" name="reqNamaKategori2" id="reqNamaKategori2"  title="Harus diisi" value=""  style="width:450px;">
                  </div>
                </div>

                <div class="form-group col-md-12 mb-2">
                  <label>Nama</label>
                  <input type='text' name='reqNama' id='reqNama' title="Kode harus diisi" value="" class="form-control easyui-validatebox span9">
                </div>

                <div class="form-group col-md-3 mb-2">
                  <label>Kode</label>
                  <input type='text' name='reqKode' id='reqKode' title="Kode harus diisi" value="<?=$tempKode?>" class="form-control easyui-validatebox span9">
                </div>
                <div class="form-group col-md-4 mb-2">
                  <label>Status</label>
                  <select name="reqKategoriStatus" class="form-control easyui-validatebox span1" style="width: 200px">
                    <?php
                    if ($tempKategoriStatus == '1') { ?>
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
                <input type="hidden" name="reqKategoriParentId" id="reqKategoriParentId" value="0" />
                <input type="hidden" name="reqSubmit" id="reqSubmit" value="tambah" />
                <button type="submit" class="btn round btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
              </div>

          <?php
          } else { ?>

            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <label>Nama Kategori 1</label>
                <input type='text' name='reqNamaKategori1' id='reqNamaKategori1' title="Bidang usaha harus diisi" value="<?=$tempNamaKategori1?>" class="form-control easyui-validatebox span9" readonly>
              </div>
              <div class="form-group col-md-12 mb-2">
                <label>Nama Kategori 2</label>
                <input type='text' name='reqNamaKategori2' id='reqNamaKategori2' title="Bidang usaha harus diisi" value="<?=$tempNamaKategori2?>" class="form-control easyui-validatebox span9" readonly>
              </div>
              <div class="form-group col-md-12 mb-2">
                <label>Nama Kategori 3</label>
                <input type='text' name='reqNama' id='reqNama' title="Bidang usaha harus diisi" value="<?=$tempNama?>" class="form-control easyui-validatebox span9" required>
              </div>
              <div class="form-group col-md-3 mb-2">
                <label>Kode</label>
                <input type='text' name='reqKode' id='reqKode' title="Kode harus diisi" value="<?=$tempKode?>" class="form-control easyui-validatebox span9" required>
              </div>
              <div class="form-group col-md-4 mb-2">
                <label>Status</label>
                <select name="reqKategoriStatus" class="form-control easyui-validatebox span1" style="width: 200px">
                  <?php
                  if ($tempKategoriStatus == '1') { ?>
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
              <input type="hidden" name="reqKategoriId" value="<?=$reqKategoriId?>"/>
              <input type="hidden" name="reqParentId" value="<?=$reqParentId?>"/>
              <input type="hidden" name="reqId" value="<?=$reqId?>"/>
              <input type="hidden" name="reqSubmit" id="reqSubmit" value="<?=$reqSubmit?>" />
              <button type="submit" class="btn round btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
            </div>

          <?php
          } ?>
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


  </body>
</html>
