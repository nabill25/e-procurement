<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("UnitKerja");
$this->load->model("UnitKerjaPic");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$unit_kerja = new UnitKerja();

/* VARIABLE */
$reqId = $this->input->get("reqId");

$unit_kerja->selectByParams(array("UNIT_KERJA_ID" => $reqId));
$unit_kerja->firstRow();
$reqNama = $unit_kerja->getField("NAMA");

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

    <!-- Bootstrap core CSS -->
    <link href="lib/bootstrap/dist/css/bootstrap.css" rel="stylesheet">

    
    <link rel="stylesheet" href="css/gaya.css" type="text/css">
    <link rel="stylesheet" href="css/gaya-bootstrap.css" type="text/css">
    
    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">
    
	<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    
    <!-- PAGINATION -->
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />  
    <script type="text/javascript">	
	$(function(){
		$('#ff').form({
			url:'unit_kerja_pic_json/add',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				//alert(data);return false;
			   $.messager.alert('Info', data, 'info');	
			  	top.reloadMonitoring();
			   // top.frames['mainFrame'].location.reload();
			}
		});
		
	});
	
	function createRowDokumenPegawai()
	{
		$(function () {
			$.get("main/loadUrl/main/pegawai_add_template", function (data) {
				$("#tbDataDokumenPegawai").append(data);
			});
		});	
	}
    </script>	
  </head>

<body class="body-popup">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="area-main popup">
                        <div class="judul-halaman">PIC</div>
                        <div class="inner">
                            <div class="area-konten">
                                <div class="area-konten-inner">
                                    <form id="ff" method="post" class="form-horizontal" role="form">
                                        <div class="judul-grup">PIC</div>
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="inputEmail" class="col-md-3 control-label ">Unit Kerja</label>
                                                    <div class="col-md-4">
                                                        <label><?=$reqNama?></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="inputEmail" class="col-md-3 control-label ">Pegawai</label>
                                                    <div class="col-md-4">
                                                    <table width="100%" border="0" cellpadding="2" cellspacing="1">
                                                        <thead>
                                                        <tr class="judul-kolom">
                                                          <th>NRP <a style="cursor:pointer" id="btnAdd" title="Tambah" onClick="createRowDokumenPegawai()">Tambah</a></th>   
                                                          <th>Nama Pegawai</th>
                                                          <th>Aksi</th>                                                        
                                                        </tr>
                                                        </thead>
                                                        <tbody id="tbDataDokumenPegawai">
                                                        <?php
														$ada = 0;
														if($reqId == "")
														{}
														else
														{
														$unit_kerja_pic = new UnitKerjaPic();
														$unit_kerja_pic->selectByParams(array("A.UNIT_KERJA_ID"=>$reqId));
														while($unit_kerja_pic->nextRow())
														{
															$id = $unit_kerja_pic->getField("UNIT_KERJA_PIC_ID");
														?>
                                                        <tr>
                                                        	<td>
                                                                <input type="hidden" name="reqUnitKerjaPicId[]" value="<?=$unit_kerja_pic->getField("UNIT_KERJA_PIC_ID")?>">
                                                                <input type="text" id="reqNip<?=$no?>" name="reqNip[]" class="easyui-validatebox" style="width:100%; background-color:#F3F3F3" value="<?=$unit_kerja_pic->getField("NIP")?>" />
                                                            </td>
                                                            <td>
                                                                <input type="text" id="reqNama<?=$no?>" name="reqNama[]" class="easyui-validatebox" style="width:100%; background-color:#F3F3F3" value="<?=$unit_kerja_pic->getField("NAMA")?>" />
                                                            </td>
                                                            <td>
                                                            		xx
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
                                            </div>
                                        </div>
                                        <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-2 control-label">&nbsp;</label>
                                                <div class="col-md-4">
                                                	<input type="hidden" name="reqDelete" id="reqDelete" value="0">
                                                    <input type="hidden" name="reqId" value="<?=$reqId?>">
                                                    <input type="hidden" name="reqMode" value="<?=$reqMode?>">
                                                    <button type="submit" class="btn-simpan">Simpan</button>
                                                    <button type="button" class="btn-batal">Batal</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                        <!--<button onClick="top.closePopup()">hai</button>
                                        -->
                                    </form>
                                
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div> <!-- /container -->
        
        
    

    
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
