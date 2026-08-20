<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("SKPanitia");
$this->load->model("UnitKerja");
$this->load->model("Panitia");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$sk_panitia = new SKPanitia();
$unit_kerja = new UnitKerja();
$panitia = new Panitia();
//$peserta = new PesertaLomba();

/* VARIABLE */
$reqId	= $this->input->get("reqId");
$reqUnit	= $this->input->post("reqUnit");
$reqNomor	= $this->input->post("reqNomor");
$reqTanggalSK	= $this->input->post("reqTanggalSK");
$reqPejabat	= $this->input->post("reqPejabat");
$reqNIPPejabat	= $this->input->post("reqNIPPejabat");
$reqUnitKerja	= $this->input->post("reqUnitKerja");
$reqStatus	= $this->input->post("reqStatus");
$reqTanggalMulaiSK	= $this->input->post("reqTanggalMulaiSK");
$reqTanggalSelesaiSK	= $this->input->post("reqTanggalSelesaiSK");
$reqSubmit	= $this->input->post("reqSubmit");


$reqUnit = $reqUnit;
$reqNomor = $reqNomor;
$reqTanggalSK = $reqTanggalSK;
$reqPejabat = $reqPejabat;
$reqNIPPejabat = $reqNIPPejabat;
$reqTanggalMulaiSK = $reqTanggalMulaiSK;
$reqTanggalSelesaiSK = $reqTanggalSelesaiSK;
$reqStatus = $reqStatus;
$reqUnitKerja = $reqUnitKerja;

if($reqId){
	//echo $reqId;
	$reqId = $reqId;
	$arrayReqId = explode('*',$reqId);
	$reqId = $arrayReqId[1];
	$sk_panitia->selectByParams(array("A.SK_PANITIA_ID"=>$reqId),-1,-1);
	$sk_panitia->firstRow();
	//echo $sk_panitia->query;
	
	$reqUnit = $sk_panitia->getField("UNIT_KERJA");
	$reqUnitKerja = $sk_panitia->getField("UNIT_KERJA_ID");
	$reqNomor = $sk_panitia->getField("NO_SK");
	$reqTanggalSK = dateToPageCheck($sk_panitia->getField("TANGGAL"));
	$reqPejabat = $sk_panitia->getField("PEJABAT_PENETAP");
	$reqNIPPejabat = $sk_panitia->getField("PEJABAT_PENETAP_NIP");
	$reqTanggalMulaiSK = dateToPageCheck($sk_panitia->getField("TANGGAL_MULAI"));
	$reqTanggalSelesaiSK = dateToPageCheck($sk_panitia->getField("TANGGAL_AKHIR"));
	$reqStatus = $sk_panitia->getField("STATUS");
	$reqStatusBerlaku = $sk_panitia->getField("AKTIF");
	
	
}
if($reqId=='')
	$reqMode= 'insert';
else
	$reqMode ='update';

$unit_kerja->selectByParams();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?=base_url();?>" />
    
    <link rel="icon" href="../../favicon.ico">

    <title><?= SYSTEM_NAME_PT ?></title>
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
        eModal.iframe(pageUrl, 'Eprocurement | <?= SYSTEM_NAME_PT ?>')
    }
	function closePopup() {
		eModal.close();
	}
    </script> 
    <script type="text/javascript">	
	$(function(){
		$('#ff').form({
			url:'sk_panitia_json/sk_panitia_add',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				$.messager.alert('Info', data, 'info');	
			  	 top.reloadMonitoring();
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

  function createRowDokumenPembeli()
  {
    $(function () {
      $.get("main/loadUrl/main/panitia_add_template_pembeli/?reqUnitKerja="+$("#reqUnitKerja").val(), function (data) {
        $("#tbDataDokumenPanitia").append(data);
      });
    }); 
  }
    </script>	
   
  </head>
  <style type="text/css">
    .fa.fa-trash {background: #da4453; padding: 5px 10px; border-radius: 10px;color: #fff;}
  </style>

<body class="body-popup">

  <div class="card mb-1 border-darken-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>SK Panitia</strong>
        </div> 
        <div class="p-1">
          <form id="ff" class="form-horizontal" role="form" method="post" novalidate style="padding:0 50px">
            <div class="row">
              <div class="form-group col-md-3 mb-2">
                <label>Nama Kelompok Kerja</label>
                <input type="text" name="reqUnit" value="<?=$reqUnit?>" class="form-control easyui-validatebox" required>
              </div> 
              <div class="form-group col-md-4 mb-2">
                <label>Nomor SK</label>
                <input type="text" name="reqNomor" value="<?=$reqNomor?>" title="No SK harus diisi" class="form-control easyui-validatebox" required>
              </div>  
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%">Tanggal Mulai SK</label>
               	<input type="text" style="width:120px" name="reqTanggalSK" id="reqTanggalSK" value="<?=$reqTanggalSK?>" class="form-control easyui-datebox"/>
              </div>  
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%">Status Berlaku</label>
                <select class="easyui-combobox" style="width: 200% !important" name="reqStatusBerlaku" id="reqStatusBerlaku">
                  <option value="1" <?php if($reqStatusBerlaku == "1") {?> selected <?php }else {}?>>Berlaku</option>
                  <option value="0"  <?php if($reqStatusBerlaku == "0") {?> selected <?php }else {}?>>Tidak Berlaku</option>
                </select>
              </div> 
            </div> 
            <div class="row">
              <div class="form-group col-md-6 mb-2">
                <label>Nama Pegawai</label>
               	<input type="text" name="reqPejabat" value="<?=$reqPejabat?>" class="form-control easyui-validatebox" required>
              </div>  
              <div class="form-group col-md-6 mb-2">
                <label>NPP Pegawai</label>
                <input type="text" name="reqNIPPejabat" value="<?=$reqNIPPejabat?>" class="form-control easyui-validatebox">
              </div> 
            </div> 
            <div class="row">
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%">Tanggal SK</label>
                <input type="text" style="width:120px" name="reqTanggalMulaiSK" id="reqTanggalMulaiSK" value="<?=$reqTanggalMulaiSK?>" class="form-control easyui-datebox" />
              </div>  
              <div class="form-group col-md-2 mb-2">
                <label style="width: 100%">Tanggal Selesai SK</label>
                <input type="text" style="width:120px" name="reqTanggalSelesaiSK" id="reqTanggalSelesaiSK" value="<?=$reqTanggalSelesaiSK?>" class="form-control easyui-datebox" />
              </div> 
              <div class="form-group col-md-8 mb-2">
                <label>Unit Kerja</label>
                <select name="reqUnitKerja" id="reqUnitKerja" class="form-control span4">
                  <?php while($unit_kerja->nextRow()){?>
                    <option value="<?=$unit_kerja->getField("UNIT_KERJA_ID")?>" <?php if($reqUnitKerja==$unit_kerja->getField("UNIT_KERJA_ID")) echo 'selected'?>><?=$unit_kerja->getField("NAMA")?></option>
                  <?php }?>
                </select>
              </div> 
            </div> 
            <div class="row" style="display:none">
              <div class="form-group col-md-12 mb-2">
                <label>Tanggal Selesai SK</label>
                <select name="reqStatus">
                  <option value="1">aktif</option>
                </select>
              </div> 
            </div> 
            <hr>
            <div class="row">
              <div class="form-group col-md-12 mb-2">
                <a style="cursor:pointer; margin-bottom: 15px" class="btn round btn-min-width box-shadow-1 btn-primary text-white" id="btnAdd" title="Tambah" onClick="createRowDokumenPanitia()"><span class="fa fa-plus"></span> Tambah Panitia</a>
                <a style="cursor:pointer; margin-bottom: 15px" class="btn round btn-min-width box-shadow-1 btn-info text-white" id="btnAdd" title="Tambah" onClick="createRowDokumenPembeli()"><span class="fa fa-plus"></span> Tambah Pembeli</a>

                <table class="table table-bordered table-hover">
                    <thead>
                    <tr class="judul-kolom">
                      <th>Nama</th>   
                      <th>NPP</th>
                      <th>Jabatan</th>
                      <th>Status</th>
                      <th>Fungsi</th>
                      <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody id="tbDataDokumenPanitia">
                    <?php
            					$ada = 0;
            					if($reqId == "")
            				   {}
            				   else
            				   {
            					   $panitia = new Panitia();
            					   $panitia->selectByParams(array("SK_PANITIA_ID" => $reqId));
            					   $no = 1;
            					   while($panitia->nextRow())
            					   {
            					?>
                    <tr>
                    	<td>
                            <input type="hidden" name="reqPanitiaId[]" value="<?=$panitia->getField("PANITIA_ID")?>">
                            <?php /*?><input type="text" id="reqNamaPanitia<?=$no?>" name="reqNamaPanitia[]" class="easyui-validatebox" style="width:100%; background-color:#F3F3F3" value="<?=$panitia->getField("NAMA")?>" /><?php */?>
                            <input type="text" class="easyui-combobox" name="reqNama[]<?=$no?>" id="reqNama<?=$no?>" title="Nama harus diisi" data-options="
                                        required: true,
                                        filter: function(q, row) { return row['text'].toLowerCase().indexOf(q.toLowerCase()) != -1; },
                                        valueField: 'id', textField: 'cabang',
                                        url: 'panitia_json/panitia_combo_json/?reqUnitKerja=<?=$reqUnitKerja?>',
                                        onSelect:function(rec){ 
                                            $('#reqNamaPanitia<?=$no?>').val(rec.text);
                                            $('#reqNip<?=$no?>').val(rec.id);
                                            $('#reqJabatanPanitia<?=$no?>').val(rec.jabatan);
                                        }
                                        " value="<?=$panitia->getField("NIP")?>" required style="width:300px;">
                        </td>
                        <td>
                        	<input type="hidden"  name="reqNamaPanitia[]" id="reqNamaPanitia<?=$no?>" value="<?=$panitia->getField("NAMA")?>">
                            <input type="text" id="reqNip<?=$no?>" name="reqNip[]" class="form-control easyui-validatebox" style="width:150px; background-color:#F3F3F3" value="<?=$panitia->getField("NIP")?>" />
                        </td>
                        <td>
                            <input type="text" id="reqJabatanPanitia<?=$no?>" name="reqJabatanPanitia[]" class="form-control easyui-validatebox" style="width:150px; background-color:#F3F3F3" value="<?=$panitia->getField("JABATAN")?>" />
                        </td>
                        <td>
                        	<select name="reqStatusPanitia[]" class="easyui-combobox">
                                <option value="1" <?php if($panitia->getField("STATUS") == "1") { ?> selected <?php } ?>>Aktif</option>
                                <option value="0" <?php if($panitia->getField("STATUS") == "0") { ?> selected <?php } ?>>Tidak Aktif</option>
                            </select>
                        </td>
                        <td>
                        	<select name="reqFungsiPanitia[]" class="easyui-combobox">
                                <option value="1" <?php if($panitia->getField("KETUA") == "1") { ?> selected <?php } ?>>Ketua</option>
                            	<option value="2" <?php if($panitia->getField("KETUA") == "2") { ?> selected <?php } ?>>Penyelia</option>
                                <option value="0" <?php if($panitia->getField("KETUA") == "0") { ?> selected <?php } ?>>Anggota</option>
                            </select>
                        </td> 
                        <td>
                            <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </td>                                
                    </tr>
                    <?php
          					$no++;
          					$ada++;
          							}
          						}
          					?>
                    </tbody>
                </table> 
              </div> 
            </div> 
            <div class="form-actions">
              <input type="hidden" name="reqId" value="<?=$reqId?>">
              <input type="hidden" name="reqMode" value="<?=$reqMode?>">
              <a href="#" onClick="top.closePopup()" class="btn round btn-min-width box-shadow-1 btn-danger mr-1 text-white"> <i class="fa fa-close"></i> Tutup</a> 
              <button type="submit" class="btn round btn-min-width box-shadow-1 btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
            </div> 
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
