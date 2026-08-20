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

/*$allRecord = 1;
$rekanan_sertifikat->selectByParamsSyarat(array('A.REKANAN_ID'=>$reqId, 'B.PAKET_ID' => $reqPaketId), -1, -1, $statement);
*/
$allRecord = $rekanan_sertifikat->getCountByParams(array('REKANAN_ID'=>$reqId), $statement);
$rekanan_sertifikat->selectByParams(array('REKANAN_ID'=>$reqId), -1, -1, $statement);
//echo $rekanan_sertifikat->query;exit;

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
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>DOKUMEN TEKNIS</strong>
        </div>
        <p class="alert alert-primary" style="background-color: transparent !important;"><b><?= $tempNama_getNama ?></b></p>
        <div class="table-responsive">
            <form action="" name="frmDaftarAlamat" method="post" enctype="multipart/form-data">
                <table class="table table-bordered table-responsive" border="0" cellpadding="2" cellspacing="1" id="tbl_bidang">
                  <tbody>
                    <tr class="judul-kolom">
                      <th>No</th>
                      <th>Nama Sertifikat </th>
											<th>Nomor Sertifikat</th>
                      <th>Instansi Pemberi</th>
                      <th>Tanggal Terbit</th>
                      <th>Tanggal Berakhir</th>
                      <th>File Dokumen Teknis</th>
                    </tr>
                    <?php
                    $i = 0;
                    if($allRecord > 0){
                        while($rekanan_sertifikat->nextRow()){
                    ?>
                   <tr>
                        <td><?=$i+1?></td>
                        <td>
                          <?php
                          if($rekanan_sertifikat->getField("JENIS") == '' || $rekanan_sertifikat->getField("JENIS") == 'Dokumen Teknis Lainnya') { ?>
                            <span class="badge badge-primary"><?= $rekanan_sertifikat->getField("JENIS") ?></span><br>
                            <?=$rekanan_sertifikat->getField("NAMA")?>
                          <?php
                          } else { ?>
                            <span class="badge badge-warning"><?= $rekanan_sertifikat->getField("JENIS") ?></span><br>
                            <?=$rekanan_sertifikat->getField("NAMA_SERTIFIKAT")?>
                          <?php
                          } ?>
                        </td>
												<td><?=$rekanan_sertifikat->getField("NOMOR")?></td>
                        <td><?=$rekanan_sertifikat->getField("INSTANSI_PEMBERI")?></td>
                        <td><?=getFormattedDateJson($rekanan_sertifikat->getField("TANGGAL"))?></td>
                        <td>
                          <?php // getFormattedDateJson($rekanan_sertifikat->getField("BERLAKU"))?>
                        <?php
                        if ($rekanan_sertifikat->getField("BERLAKU")) {
                          if (strtotime($rekanan_sertifikat->getField("BERLAKU")) < strtotime(date('Y-m-d'))) {
                            echo getFormattedDateJson($rekanan_sertifikat->getField("BERLAKU")). ' <br><span class="badge badge-pill badge-danger">Berakhir</span>';
                          } else {
                            echo getFormattedDateJson($rekanan_sertifikat->getField("BERLAKU")).'';
                          }
                        }
                       ?>
                        </td>
                        <td>
                          <?php
                          if ($rekanan_sertifikat->getField("PATH_FILE") != '') {
                           ?>
                          <a href="<?=$FILE_DIR.str_replace("'", "''", $rekanan_sertifikat->getField("PATH_FILE"))?>" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download</a>
                          <?php
                          } else {
                            echo "-";
                          } ?>
                        </td>
                    </tr>
                    <?php $i++;}}else{
                        if($i % 2 == 0) $css = "gelap";
                        else            $css = "terang";
                    ?>
                    <tr>
                        <td colspan="5">.: data belum ada :.</td>
                    </tr>
                    <?php }?>
                  </tbody>

                </table>
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
							if ($cekData->getField("teknis_lain") == '1') {
								$checked = 'checked';
							}
							echo '<input class="mb-1" type="checkbox" name="checkteknis_lain" id="checkjenis" onclick="return updateChecklist(\''.$reqId.'\',\'teknis_lain\')" style="cursor:pointer" '.$checked.'> Ya, Lengkap ';
							?>
							<input type="" class="form-control easyui-validatebox span2" required maxlength="255" style="height: 30px !important;" name="" placeholder="catatan" id="catatanjenis" value="<?=$cekData->getField("teknis_lain_note")?>" onChange="return updateChecklist('<?= $reqId ?>','teknis_lain')">
							<small><sup>*</sup>&nbsp;Tekan enter setalah mengisi catatan</small>
						</div>
					</div>
				</form>
        <?php 
        } ?>
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
