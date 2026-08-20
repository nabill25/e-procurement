<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("RekananIjinUsaha");
$this->load->model("RekananBidangUsaha");

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$rekanan_ijin_usaha = new RekananIjinUsaha();
$rekanan_bidang_usaha = new RekananBidangUsaha();

/* VARIABLE */
$reqIjinUsahaId = $this->input->get('reqIjinUsahaId');
$reqTipe	= "99";
$reqId = $this->ID;

$rekanan_ijin_usaha->selectByParams(array("REKANAN_ID" => $reqId, "IJIN_USAHA_ID"=> 99));   
$rekanan_ijin_usaha->firstRow();

$reqIjinUsahaId = $rekanan_ijin_usaha->getField("REKANAN_IJIN_USAHA_ID");
$reqNomor = $rekanan_ijin_usaha->getField("NO_IJIN");
$reqTanggalIjin = $rekanan_ijin_usaha->getField("TANGGAL");
$reqTanggalBerakhir = $rekanan_ijin_usaha->getField("TANGGAL_BERAKHIR");
$reqInstansi = $rekanan_ijin_usaha->getField("INSTANSI");
$reqBidang = $rekanan_ijin_usaha->getField("IJIN_USAHA");
$reqLinkFileTemp= $rekanan_ijin_usaha->getField("PATH_FILE");
$reqLinkFileTempTipe= $rekanan_ijin_usaha->getField("TIPE");
$reqLinkFileTempUkuran= $rekanan_ijin_usaha->getField("UKURAN");
$reqLinkFileTempNama= $rekanan_ijin_usaha->getField("NAMA_FILE");

if($reqIjinUsahaId == '')	
	$reqMode = 'insert';
else
	$reqMode='update';
	
	
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

    <script type="text/javascript" src="js/jquery-1.9.1.js"></script>
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
    
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    
    <link href="lib/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <!-- <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/bootstrap.css"> -->

    
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

    <!-- EASYUI -->
    <link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
  <!--<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>-->
    <script type="text/javascript" src="lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="lib/eproc/allfunc.js"></script>
  
    <!-- EMODAL -->
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
    $(document).ready(function() {
        
        $(function(){
            $('#ff').form({
                url:'rekanan_ijin_usaha_json/data_administrasi_sbu_ubah',
                onSubmit:function(){
                    return $(this).form('validate');
                },
                success:function(data){
                  $.messager.alert('Info', data, 'info'); 
             //      top.setElementValue('reqDataSBU','Data Lengkap');
        					// top.reloadIjinUsaha(99);
        					// top.closePopup();
                }
            });
            
        });
        
    });
    </script>
 </head>


<body class="body-popup">
  <div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Data Administrasi - Sertifikat Badan Usaha</strong>
        </div> 
        <div class="p-1">
          <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
            <div class="col-md-12">  

              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label>Nomor Sertifikat</label>
                  <input name="reqNomorIjin" type="text" title="Nomor sertifikat harus diisi" class="form-control easyui-validatebox" id="reqNomorIjin" value="<?=$reqNomor?>" size="50" required />
                </div> 
              </div>    
              
              <div class="row">
                <div class="form-group col-md-2 mb-2">
                  <label style="width: 100%">Tanggal Sertifikat</label>
                  <input type="text" style="width: 150px" title="Tanggal sertifikat harus diisi" class="form-control easyui-datebox" name="reqTanggalIjin" id="reqTanggalIjin" value="<?=dateToPageCheck($reqTanggalIjin)?>" required />
                </div> 
                <div class="form-group col-md-2 mb-2">
                  <label style="width: 100%">Tanggal berakhir</label>
                  <input type="text" style="width: 150px" title="Tanggal berakhir harus diisi" class="form-control easyui-datebox" name="reqTanggalBerakhir" id="reqTanggalBerakhir" value="<?=dateToPageCheck($reqTanggalBerakhir)?>" required />
                </div> 
              </div>    

              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label>Nama penanda tangan</label>
                  <input name="reqInstansiPemberiIjin" title="Nama penanda tangan harus diisi" class="form-control easyui-validatebox" type="text" id="reqInstansiPemberiIjin" value="<?=$reqInstansi?>" size="80" required />
                </div> 
              </div>    

              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label>File</label>
                   <input type="hidden" name="reqLinkFileTemp" value="<?=$reqLinkFileTemp?>" />
                   <input type="hidden" name="reqLinkFileTempTipe" value="<?=$reqLinkFileTempTipe?>" />
                   <input type="hidden" name="reqLinkFileTempUkuran" value="<?=$reqLinkFileTempUkuran?>" />
                   <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30" class="easyui-validatebox" <?php if($reqLinkFileTemp == "") { ?> required  <?php } ?> validType="fileType['pdf']" />
                   <input type="hidden" name="reqLinkFileTempNama" value="<?=$reqLinkFileTempNama?>">
                    temp : <?=$reqLinkFileTempNama?>
                </div> 
              </div>    
            </div>
            <div class="card mb-1 border-blue border-darken-1">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Bidang usaha</strong>  
                  <a title="Tambah Bidang Usaha" id="btnAdd" onClick="openAdd('main/loadUrl/main/bidang_usaha');" style="margin-bottom: 15px"><span class="fa fa-plus"></span> </a>
                </div> 
                <div class="table-responsive">
                  <table class="table table-bordered">
                      <thead>
                      <tr class="judul-kolom">
                        <th>
                          Bidang usaha 
                        </th>   
                        <th>
                          Aksi
                        </th>
                      </tr>
                      </thead>
                      <tbody id="tbodyBidangUsaha">
                      <?php
                      $rekanan_bidang_usaha->selectByParamsMonitoring(array("REKANAN_ID" => $reqId, "IJIN_USAHA_ID"=> 99));
                      while($rekanan_bidang_usaha->nextRow())
                      {
                      ?>
                      <tr>
                        <td><?=$rekanan_bidang_usaha->getField("NAMA")?></td>
                        <td style="text-align: center"><input type="hidden" name="reqBidangUsahaId[]" value="<?=$rekanan_bidang_usaha->getField("BIDANG_USAHA_ID")?>" /><a title="#" onclick="$(this).parent().parent().remove();"><span class="fa fa-trash"></span></a></td>                                    
                      </tr>
                      <?php
                      }
                      ?>
                      </tbody>
                  </table>
                </div>
              </div> 
            </div>
            <div class="form-actions">
              <input type="hidden" name="reqId" value="<?=$reqId?>" />
              <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
              <input type="hidden" name="reqIjinUsahaId" value="<?=$reqIjinUsahaId?>" />
              <input type="hidden" name="reqTipe" value="<?=$reqTipe?>" />
              <a href="#" onClick="top.closePopup()" class="btn btn-danger mr-1 text-white"> <i class="fa fa-close"></i> Tutup</a> 
              <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
            </div> 
          </form>
        </div>
      </div>
    </div>
  </div> 

</body>    
