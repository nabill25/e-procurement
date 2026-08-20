<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("RekananSertifikat");
$this->load->model("Rekanan");
$this->load->model("RekananDaftarSertifikat");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$rekanan_sertifikat = new RekananSertifikat();
$rekanan_get_nama = new Rekanan();

$reqId = $this->input->get("reqId");
$reqPaketId =  $this->input->get("reqPaketId");
$reqCari = httpFilterPost("reqCari");
$reqInputCari = httpFilterPost("reqInputCari");
$reqSubmit = httpFilterPost("reqSubmit");
$reqCatatan = $_POST["reqCatatan"];
$reqDaftarId = $_POST["reqDaftarId"];

$FILE_DIR = "uploads/sertifikat/";

if($reqSubmit == "Submit")
{
	for($i=0;$i<count($reqDaftarId);$i++)
	{
		$rekanan_daftar_sertifikat = new RekananDaftarSertifikat();
		$rekanan_daftar_sertifikat->setField("CATATAN", $reqCatatan[$i]);
		$rekanan_daftar_sertifikat->setField("REKANAN_DAFTAR_SERTIFIKAT_ID", $reqDaftarId[$i]);
		$rekanan_daftar_sertifikat->updateCatatan();
		unset($rekanan_daftar_sertifikat);
	}
}


if($reqPaketId == "")
	$statement = "";
else
	$statement = " AND EXISTS(SELECT 1 FROM REKANAN_DAFTAR_SERTIFIKAT X WHERE X.REKANAN_SERTIFIKAT_ID = A.REKANAN_SERTIFIKAT_ID AND X.PAKET_ID = '".$reqPaketId."') ";
	
$allRecord = 1;
$rekanan_sertifikat->selectByParamsSyarat(array('A.REKANAN_ID'=>$reqId, 'B.PAKET_ID' => $reqPaketId), -1, -1, $statement);

//$allRecord = $rekanan_sertifikat->getCountByParams(array('REKANAN_ID'=>$reqId), $statement);
//$rekanan_sertifikat->selectByParams(array('REKANAN_ID'=>$reqId), -1, -1, $statement);
//echo $rekanan_sertifikat->query;exit;

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
    <script language="JavaScript" src="jslib/displayElement.js"></script>
  </head>

<body>

  <div class="card mb-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>SERTIFIKAT LAIN</strong>  
        </div> 
        <div class="table-responsive">
            <form action="" name="frmDaftarAlamat" method="post" enctype="multipart/form-data">
                <table class="table table-bordered table-responsive" border="0" cellpadding="2" cellspacing="1" id="tbl_bidang">
                  <tbody>
                    <tr class="judul-kolom">
                      <th>No</th>
                      <th>Nama Sertifikat </th>
                      <th>Nomor Sertifikat</th>
                      <th>Tanggal Terbit</th>
                      <th>Tanggal Berakhir</th>
                      <th>File</th>
                      <td align="center">Catatan</td>
                    </tr>
            <?php		
    				$i = 0;
    				if($allRecord > 0){
    					while($rekanan_sertifikat->nextRow()){
    					if($i % 2 == 0)	$css = "gelap";
    					else			$css = "terang";
    					
    				?>   
                   <tr class="<?=$css?>">
                        <td><?=$i+1?></td>
                        <td><?=$rekanan_sertifikat->getField("NAMA")?></td>
                        <td><?=$rekanan_sertifikat->getField("NOMOR")?></td>
                        <td><?=getFormattedDateJson($rekanan_sertifikat->getField("TANGGAL"))?></td>
                        <td><?=getFormattedDateJson($rekanan_sertifikat->getField("BERLAKU"))?></td>
                        <td><a href="<?=$FILE_DIR.str_replace("'", "''", $rekanan_sertifikat->getField("PATH_FILE"))?>" class="badge badge-primary" target="_blank">Download</a></td>
                    	<td valign="top"> 
                        	<textarea name="reqCatatan[]" style="width:98%"><?=$rekanan_sertifikat->getField("CATATAN_SERTIFIKAT")?></textarea>
                        	<input type="hidden" name="reqDaftarId[]" value="<?=$rekanan_sertifikat->getField("REKANAN_DAFTAR_SERTIFIKAT_ID")?>" />
                        </td>
                    </tr>
                    <?php $i++;}}else{
                        if($i % 2 == 0)	$css = "gelap";
                        else			$css = "terang";
                    ?>
                    <tr class="<?=$css?>">
                        <td colspan="5">.: data belum ada :.</td>
                    </tr>
                    <?php }?> 
                  </tbody>
                  
                </table>
                <br>
                <input type="submit" name="reqSubmit" class="btn btn-primary" value="Submit" />
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
