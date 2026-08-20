<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->model("Rekanan");
$this->load->model("RekananIjinUsaha");
$this->load->model("RekananBidangUsaha");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$rekanan_ijin_usaha = new RekananIjinUsaha();
$rekanan_ijin_usaha_count = new RekananIjinUsaha();
$rekanan_bidang_usaha = new RekananBidangUsaha();
$rekanan_get_nama = new Rekanan();

$reqId = $this->input->get("reqId");
$reqIjinUsahaId = $this->input->post("reqIjinUsahaId");


$reqPaketId = $this->input->post("reqPaketId");
$reqMode = $this->input->post("reqMode");
$reqKoreksi = $this->input->post("reqKoreksi");
$reqSubmit = $this->input->post("reqSubmit");

$FILE_DIR = "uploads/ijin_usaha/";
/* VARIABLE */

/* VALIDATION */

// trigger the validation
$rcBright = "table_list_bright";
$rcDark = "table_list_dark";
$rcI = 0;

/* ACTION BY REQMODE */
$rekanan_ijin_usaha->selectByParams(array("REKANAN_ID" => $reqId), -1, -1, " AND IJIN_USAHA_ID = 99 ");
$countData = $rekanan_ijin_usaha_count->getCountByParams(array("REKANAN_ID" => $reqId), " AND IJIN_USAHA_ID = 99 ");
//echo $rekanan_ijin_usaha->query;exit;

//$rekanan_ijin_usaha->firstRow();
$i=0;
while($rekanan_ijin_usaha->nextRow()){
	//echo $rekanan_ijin_usaha->getField("REKANAN_IJIN_USAHA_ID").'--';
	$tempRekananIjinUsahaId[$i] = $rekanan_ijin_usaha->getField("REKANAN_IJIN_USAHA_ID");
	$tempIjinUsahaId[$i] = $rekanan_ijin_usaha->getField("IJIN_USAHA_ID");
	$tempNomor[$i] = $rekanan_ijin_usaha->getField("NO_IJIN");
	$tempTanggalIjin[$i] = getFormattedDate($rekanan_ijin_usaha->getField("TANGGAL"));
  $tempTanggalBerakhir[$i] = getFormattedDate($rekanan_ijin_usaha->getField("TANGGAL_BERAKHIR"));
	$tempTanggalBerakhirAsli[$i] = $rekanan_ijin_usaha->getField("TANGGAL_BERAKHIR");
	$tempInstansi[$i] = $rekanan_ijin_usaha->getField("INSTANSI");
	$tempBidang[$i] = $rekanan_ijin_usaha->getField("IJIN_USAHA");
	$tempLinkFileTemp[$i]= $rekanan_ijin_usaha->getField("PATH_FILE");
	$tempNamaPemegang[$i]= $rekanan_ijin_usaha->getField("NAMA_PEMEGANG");
	$i++;
}

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

  </head>

<body>
 <div class="card mb-1">
    <div class="card-content">
      <div class="p-1">
        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Sertifikat Badan Usaha Konstruksi</strong>
        </div>
        <p class="alert alert-primary" style="background-color: transparent !important;"><b><?= $tempNama_getNama ?></b></p>
        <div class="table-responsive">
          <?php
		      //echo count($tempRekananIjinUsahaId);
		      for($x=0; $x < $countData; $x++){
  		   ?>

          <table class="table table-bordered table-hover">
            <tbody>
                <tr>
                    <td style="width: 30%">Nomor sertifikat:</td>
                    <td>
                      <?=$tempNomor[$x]?>
                    </td>
                </tr>
                <tr>
                    <td style="width: 30%">Tanggal sertifikat:</td>
                    <td>
                      <?=$tempTanggalIjin[$x]?>
                    </td>
                </tr>
                <tr>
                    <td style="width: 30%">Tanggal berakhir:</td>
                    <td>
                      <?php //$tempTanggalBerakhir[$x]?>
                      <?php
                        if (strtotime($tempTanggalBerakhirAsli[$x]) < 1) {
                          echo '<span class="badge badge-pill badge-success"> Seumur hidup</span>';
                        } else {
                          if (strtotime($tempTanggalBerakhirAsli[$x]) < strtotime(date('Y-m-d'))) {
                            echo $tempTanggalBerakhir[$x]. ' <span class="badge badge-pill badge-danger">Berakhir</span>';
                          } else {
                            echo $tempTanggalBerakhir[$x].'';
                          }
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="width: 30%">Lembaga Sertifikasi:</td>
                    <td>
                      <?=$tempInstansi[$x]?>
                    </td>
                </tr> 
                <tr>
                    <td style="width: 30%">file SBU:</td>
                    <td>
                      <?php
                      if($tempLinkFileTemp == '')
                      {}
                      else
                      {
                          $arrFile = explode(";", $tempLinkFileTemp[$x]);
                          for($iFile=0;$iFile<count($arrFile);$iFile++)
                          {
                              if (file_exists($FILE_DIR.$arrFile[$iFile]) && $arrFile[$iFile] != '') {
                      ?>
                              <a href="<?=$FILE_DIR.$arrFile[$iFile]?>" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download</a>
                      <?php
                            } else {
                                  echo "-";
                                }
                          }
                      }
                      ?>
                    </td>
                </tr>
            </tbody>
          </table>

            <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
              <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Bidang usaha</strong>
            </div>
            <table class="table table-bordered table-hover" id="tbl_bidang">
              <tbody>
                <tr class="judul-kolom">
                  <th width="5px">No</th>
                  <th>Bidang usaha</th>
                </tr>
                  <?php
    							$i = 1;
    							$rekanan_bidang_usaha->selectByParamsMonitoring(array("REKANAN_ID" => $reqId, "IJIN_USAHA_ID"=>$tempIjinUsahaId[$x]));
    							while($rekanan_bidang_usaha->nextRow())
    							{
    							?>
                <tr >
                    <td ><?=$i?>
                      .</td>
                    <td><?=$rekanan_bidang_usaha->getField("NAMA")?></td>
                </tr>
  	            <?php
                $i++;
                }
                ?>
              </tbody>
            </table>
          <?php
          }?>

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
							if ($cekData->getField("sbu") == '1') {
								$checked = 'checked';
							}
							echo '<input class="mb-1" type="checkbox" name="checksbu" id="checkjenis" onclick="return updateChecklist(\''.$reqId.'\',\'sbu\')" style="cursor:pointer" '.$checked.'> Ya, Lengkap ';
							?>
							<input type="" class="form-control easyui-validatebox span2" required maxlength="255" style="height: 30px !important;" name="" placeholder="catatan" id="catatanjenis" value="<?=$cekData->getField("sbu_note")?>" onChange="return updateChecklist('<?= $reqId ?>','sbu')">
							<small><sup>*</sup>&nbsp;Tekan enter setelah mengisi catatan</small>
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
