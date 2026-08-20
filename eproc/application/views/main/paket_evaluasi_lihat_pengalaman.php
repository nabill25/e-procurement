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
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->library("panelinfo"); $panelInfo = new panelinfo();
$this->load->model("RekananPengalaman");
$this->load->model("Rekanan");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$rekanan_pengalaman					= new RekananPengalaman(); // tipe 0
$rekanan_pengalaman_progress		= new RekananPengalaman(); // tipe 0
$rekanan_get_nama = new Rekanan();

/* VARIABLE */
$reqId = $this->input->get("reqId");
$reqPengalamanId  = $_POST["reqPengalamanId"];
$reqKontrakNilai = $_POST["reqKontrakNilai"];
$reqKontrakNilaiTemp = $_POST["reqKontrakNilaiTemp"];
$submitSimpan = $this->input->post("submitSimpan");

/* VALIDATION */
$FILE_DIR = "uploads/pengalaman/";
// trigger the validation

$statement = " AND EXISTS(SELECT 1 FROM REKANAN_EVAL_PENGALAMAN X WHERE X.REKANAN_PENGALAMAN_ID = A.REKANAN_PENGALAMAN_ID AND PAKET_REKANAN_ID = '".$reqId."') ";

$rekanan_pengalaman->selectByParams(array(), -1, -1, $statement);

$rekanan_get_nama->selectByParams(array("REKANAN_ID"=>$reqId),-1,-1);
$rekanan_get_nama->firstRow();
$tempNama_getNama= $rekanan_get_nama->getField("NAMA");
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

    <script type="text/javascript">
	$(function(){
		$('#ff').form({
			url:'rekanan_pengalaman_json/paket_evaluasi_lihat_pengalaman',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				$.messager.alert('Info', data, 'info');	
				top.reloadMonitoring();
			}
		});
		
	});
	function getNilai(id)
	{
		if($("#reqNilaiKategori" + id).val() == "S")
		{
			$("#reqNilai" + id).val(100);	
			$("#reqNilai" + id).attr("readonly", true);	
		}
		else if($("#reqNilaiKategori" + id).val() == "R")
		{
			$("#reqNilai" + id).val('');	
			$("#reqNilai" + id).attr("readonly", false);			
		}
		else
		{
			$("#reqNilai" + id).val(0);	
			$("#reqNilai" + id).attr("readonly", true);				
		}
	
	}
  </script>
  </head>

<body>
    <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Data Pengalaman Perusahaan</strong>
          </div> 
          <div class="p-1">
            <form id="ff" class="form-horizontal" role="form" method="post" novalidate> 
              <table class="table table-bordered">
                <tbody>   
                    <tr>
                        <th align="center" class="judul-kolom" rowspan="2">No.</th>
                        <th align="center" class="judul-kolom" rowspan="2">Nama Pekerjaan</th>
                        <th align="center" class="judul-kolom" rowspan="2">Bidang Pekerjaan</th>
                        <th align="center" class="judul-kolom" rowspan="2">Nilai Kontrak</th>
                        <th align="center" class="judul-kolom" style="text-align: center" colspan="2">File</th> 
                    </tr>  
                    <tr>
                        <th align="center" class="judul-kolom" style="text-align: center">SPK</th>
                        <th align="center" class="judul-kolom" style="text-align: center">BA</th>
                    </tr>  
                    <? 
                        $i = 0;
                        while($rekanan_pengalaman->nextRow()){
                        $readonly = 1;        
                    ?>   
                         <tr >
                            <td><?=$rcI+1?></td>
                            <td><a class="taut" onclick="displayElement('reqDetil<?=$i?>')" style="cursor:pointer" id="rekanan<?=$i?>"><?=$rekanan_pengalaman->getField("NAMA")?></a></td>
                            
                            <td><?=$rekanan_pengalaman->getField("PENGALAMAN_BIDANG")?></td>
                            <td>
                                <input type="text" class="form-control easyui-validatebox" readonly="" name="reqKontrakNilai[]" id="reqKontrakNilai<?=$x?>" value="<?=numberToIna($rekanan_pengalaman->getField("KONTRAK_NILAI"))?>" OnFocus="FormatAngka('reqKontrakNilai<?=$x?>')" OnKeyUp="FormatAngka('reqKontrakNilai<?=$x?>')" OnBlur="FormatUang('reqKontrakNilai<?=$x?>')" />
                                <input type="hidden" name="reqKontrakNilaiTemp[]" value="<?=numberToIna($rekanan_pengalaman->getField("KONTRAK_NILAI_SEBELUMNYA"))?>" />
                                <input type="hidden" name="reqPengalamanId[]" value="<?=($rekanan_pengalaman->getField("REKANAN_PENGALAMAN_ID"))?>" />
                            </td>
                            <td style="text-align: center"><a href="<?=$FILE_DIR.$rekanan_pengalaman->getField("PATH_FILE")?>" class="taut" target="_blank"><span class="fa fa-download"></span></a></td>
                            <td style="text-align: center"><a href="<?=$FILE_DIR.$rekanan_pengalaman->getField("PATH_FILE_BA")?>" class="taut" target="_blank"><span class="fa fa-download"></span></a></td>
                        </tr>
                        <tr id="reqDetil<?=$i?>" style="display:none">
                            <td colspan="8">
                                <table width="100%" border="0" cellpadding="2" cellspacing="1">
                                  <tr class="judul-kolom2">
                                    <td colspan="4">Pemberi Tugas</td>
                                  </tr>
                                  <tr class="gelap">
                                    <td colspan="2">Nama</td>
                                    <td width="3%">:</td>
                                    <td width="76%"><?=$rekanan_pengalaman->getField("PEMBERI_TUGAS")?></td>
                                  </tr>
                                  <tr class="terang">
                                    <td colspan="2">Alamat</td>
                                    <td>:</td>
                                    <td><?=$rekanan_pengalaman->getField("PEMBERI_TUGAS_ALAMAT")?></td>
                                  </tr>
                                  <tr>
                                    <td colspan="4">&nbsp;</td>
                                  </tr>
                                  <tr class="judul-kolom2">
                                    <td colspan="4">Kontrak</td>
                                  </tr>
                                  <tr class="gelap">
                                    <td colspan="2">No</td>
                                    <td>:</td>
                                    <td><?=$rekanan_pengalaman->getField("KONTRAK_NOMOR")?></td>
                                  </tr>
                                  <tr class="terang">
                                    <td colspan="2">Tanggal</td>
                                    <td>:</td>
                                    <td><?=getFormattedDate($rekanan_pengalaman->getField("KONTRAK_TANGGAL"))?></td>
                                  </tr>
                                  <tr class="gelap">
                                    <td colspan="2">Lokasi</td>
                                    <td>:</td>
                                    <td><?=$rekanan_pengalaman->getField("LOKASI")?></td>
                                  </tr>
                                  <tr>
                                    <td colspan="4">&nbsp;</td>
                                  </tr>
                                  <tr class="judul-kolom2">
                                    <td colspan="4">Tanggal Selesai</td>
                                  </tr>
                                  <tr class="gelap">
                                    <td colspan="2">Penyerahan</td>
                                    <td>:</td>
                                    <td><?=getFormattedDate($rekanan_pengalaman->getField("BA_TANGGAL"))?></td>
                                  </tr>
                                </table>
                            </td>
                        </tr>
                         <? $i++;$rcI++;$no++; $x++; 
                        }
                        ?>
                </tbody>
              </table>   
              <div class="form-actions">
                <input type="hidden" name="submitSimpan" value="Simpan" />
                <input type="hidden" name="reqId" value="<?=$reqId?>" /> 
                <a href="#" onClick="top.closePopup()" class="btn btn-danger mr-1 text-white"> <i class="fa fa-close"></i> Tutup</a> 
                <!-- <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button> -->
              </div> 
            </form>
          </div>
        </div>
      </div>
    </div> 
    
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!-- EASYUI -->
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>
    
  </body>
</html>
