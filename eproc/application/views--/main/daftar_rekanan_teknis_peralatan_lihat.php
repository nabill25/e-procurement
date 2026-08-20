<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("RekananPeralatan");
$this->load->model("Rekanan");
$this->load->model("RekananDaftarPeralatan");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$rekanan_peralatan = new RekananPeralatan();
$rekanan_get_nama = new Rekanan();

$reqId = $this->input->get("reqId");
$reqPaketId =  $this->input->get("reqPaketId");
$reqCari = httpFilterPost("reqCari");
$reqInputCari = httpFilterPost("reqInputCari");

$reqSubmit = $this->input->post("reqSubmit");
$reqCatatan = $_POST["reqCatatan"];
$reqDaftarId = $_POST["reqDaftarId"];

$FILE_DIR = "uploads/peralatan/";
if($reqSubmit == "Submit")
{
	for($i=0;$i<count($reqDaftarId);$i++)
	{
		$rekanan_daftar_peralatan = new RekananDaftarPeralatan();
		$rekanan_daftar_peralatan->setField("CATATAN", $reqCatatan[$i]);
		$rekanan_daftar_peralatan->setField("REKANAN_DAFTAR_PERALATAN_ID", $reqDaftarId[$i]);
		$rekanan_daftar_peralatan->updateCatatan();
		unset($rekanan_daftar_peralatan);
	}
}

if($reqPaketId == "")
	$statement = "";
else
	$statement = " AND EXISTS(SELECT 1 FROM REKANAN_DAFTAR_PERALATAN X WHERE X.REKANAN_PERALATAN_ID = A.REKANAN_PERALATAN_ID AND X.PAKET_ID = '".$reqPaketId."') ";


$allRecord = $rekanan_peralatan->getCountByParams(array('REKANAN_ID'=>$reqId), $statement);
$rekanan_peralatan->selectByParams(array('REKANAN_ID'=>$reqId), -1, -1, $statement);

$rekanan_get_nama->selectByParams(array("A.REKANAN_ID"=>$reqId),-1,-1);
$rekanan_get_nama->firstRow();
$tempNama_getNama= $rekanan_get_nama->getField("NAMA");
$reqStatusValidasi = $rekanan_get_nama->getField("STATUS_VALIDASI");
$reqUserStatus = $rekanan_get_nama->getField("USER_STATUS");
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
    <script language="JavaScript" src="jslib/displayElement.js"></script>
  </head>

<body>
    <div class="card mb-1">
        <div class="card-content">
          <div class="p-1">
            <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
              <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>PERALATAN</strong>
            </div>
            <p class="alert alert-primary" style="background-color: transparent !important;"><b><?= $tempNama_getNama ?></b></p>
            <div class="table-responsive">
              <form action="" name="frmDaftarAlamat" method="post" enctype="multipart/form-data">
                <table class="table table-bordered table-hover table-responsive" border="0" cellpadding="2" cellspacing="1" id="tbl_bidang">
                    <tbody>
                      	<tr class="judul-kolom">
                          <th align="center">No.</th>
                          <th align="center">Jenis</th>
                          <th align="center">Jumlah</th>
                          <th align="center">Kapasitas</th>
                          <th align="center">Merk</th>
                          <th align="center">Th. Pembuatan</th>
                          <th align="center">Kondisi</th>
                          <th align="center">Lokasi</th>
                          <th align="center">Kepemilikan</th>
                          <th align="center">File Peralatan</th>
                        </tr>
            <?php
						$i = 0;
						if($allRecord > 0){
							while($rekanan_peralatan->nextRow()){
						?>
                      <tr class="<?=$css?>">
                        <td><?=$i+1?></td>
                        <td><?=$rekanan_peralatan->getField("JENIS")?></td>
                        <td><?=$rekanan_peralatan->getField("JUMLAH")?></td>
                        <td><?=$rekanan_peralatan->getField("KAPASITAS")?></td>
                        <td><?=$rekanan_peralatan->getField("MERK")?></td>
                        <td><?=$rekanan_peralatan->getField("TAHUN")?></td>
                        <td><?=$rekanan_peralatan->getField("KONDISI")?></td>
                        <td><?=$rekanan_peralatan->getField("LOKASI")?></td>
                        <td><?=$rekanan_peralatan->getField("BUKTI_KEPEMILIKAN")?></td>
                        <td><a href="<?=$FILE_DIR.str_replace("'", "''", $rekanan_peralatan->getField("PATH_FILE"))?>" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download</a></td>

                        </tr>
                        <?php $i++;}}else{
                        ?>
                        <tr class="<?=$css?>">
                            <td colspan="10">.: data belum ada :.</td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
            <br>
          </form>
        </div>

        <?php 
        if ($this->USER_TYPE_ID == '2' && ($reqStatusValidasi != '1' || $reqUserStatus == '0')) { ?>
				<form id="ff2" class="easyui-form " method="post" novalidate enctype="multipart/form-data">
					<div class="form-actions card-content collapse show border-info border-darken-2 mt-2">
						<div class="card-body">
							<?php
							$checked = '';
							$cekData = new Rekanan();
							$cekData->selectByParamsRekananChecklist(array("REKANANID"=>$reqId),-1,-1);
							$cekData->firstRow();
							if ($cekData->getField("peralatan") == '1') {
								$checked = 'checked';
							}
							echo '<input class="mb-1" type="checkbox" name="checkperalatan" id="checkjenis" onclick="return updateChecklist(\''.$reqId.'\',\'peralatan\')" style="cursor:pointer" '.$checked.'> Ya, Lengkap ';
							?>
							<input type="" class="form-control easyui-validatebox span2" required maxlength="255" style="height: 30px !important;" name="" placeholder="catatan" id="catatanjenis" value="<?=$cekData->getField("peralatan_note")?>" onChange="return updateChecklist('<?= $reqId ?>','peralatan')">
							<small><sup>*</sup>&nbsp;Tekan enter setalah mengisi catatan</small>
						</div>
					</div>
				</form>
        <?php 
        } ?>
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
    <link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
		<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <script type="text/javascript" src="lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="lib/eproc/allfunc.js"></script>

		<script type="text/javascript">
    function updateChecklist(rekananid,jenis) {
      var n = $("#checkjenis:checked").length;
      if (n == 1) {
          $('#catatanjenis').validatebox({ required:false  });
          $('#catatanjenis').val('');
      } else {
          $('#catatanjenis').validatebox({ required:true  });
      }
      var c = $("#catatanjenis").val();
      // alert(n+'-'+c+'-'+rekananid+'-'+jenis); return false;
        $.getJSON("rekanan_json/updateChecklist2/?rekananid="+rekananid+"&jenis="+jenis+"&status="+n+"&catatan="+c,
          function(data){
            if (data.RESPONSE === 'Gagal') {
              $.messager.alert('Info', data.PESAN, 'info');
              if (n === 0) { // kalau gagal balik ke awal
                $("#checkjenis").prop("checked", true);
              } else {
                $("#checkjenis").prop("checked", false);
              }
            }
        });
    }
    </script>
		
  </body>
</html>
