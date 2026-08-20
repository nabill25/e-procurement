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
$this->load->model("IjinUsaha");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$rekanan_ijin_usaha = new RekananIjinUsaha();
$rekanan_ijin_usaha_count = new RekananIjinUsaha();
$rekanan_bidang_usaha = new RekananBidangUsaha();
$rekanan_get_nama = new Rekanan();

$FILE_DIR = "uploads/ijin_usaha/";
/* VARIABLE */
$reqId = $this->input->get("reqId");
$reqIjinUsahaId = $this->input->get("reqIjinUsahaId") ?: '0';
$reqPaketId = $this->input->get("reqPaketId");
$reqMode = $this->input->get("reqMode");
$reqKoreksi = $this->input->post("reqKoreksi");
$reqSubmit = $this->input->post("reqSubmit");

/* VALIDATION */

// trigger the validation
$rcBright = "table_list_bright";
$rcDark = "table_list_dark";
$rcI = 0;

/* ACTION BY REQMODE */

if($reqIjinUsahaId == "0") {
} else if($reqIjinUsahaId == "all") {
	$statement = " AND IJIN_USAHA_ID != '99' ";
} else {
  $statement = " AND IJIN_USAHA_ID = '".$reqIjinUsahaId."' ";
}

$rekanan_ijin_usaha->selectByParams(array("REKANAN_ID" => $reqId), -1, -1, $statement." AND NOT IJIN_USAHA_ID = 99 ");
$countData = $rekanan_ijin_usaha_count->getCountByParams(array("REKANAN_ID" => $reqId), $statement." AND NOT IJIN_USAHA_ID = 99 ");
// echo $countData->query;exit;
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
  $tempPKKPR[$i]= $rekanan_ijin_usaha->getField("PKKPR");
  $tempFileTemp2[$i]= $rekanan_ijin_usaha->getField("PATH_FILE2");
  $tempTanggalPKKPR[$i]= $rekanan_ijin_usaha->getField("TANGGAL_PKKPR");
  $tempTanggalPKKPRBerakhir[$i]= $rekanan_ijin_usaha->getField("TANGGAL_PKKPR_BERAKHIR");
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
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">
  </head>

<body>

  <div class="card mb-1">
    <div class="card-content">
      <div class="p-1">
        <div class="table-responsive">
          <form action="" name="ff" method="post" enctype="multipart/form-data">
            <?php
					  //echo count($tempRekananIjinUsahaId);
					  for($x=0; $x < $countData; $x++){
				    ?>
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Ijin <?=$tempBidang[$x]?></strong>
          </div>
              <p class="alert alert-primary" style="background-color: transparent !important;"><b><?= $tempNama_getNama ?></b></p>
          
              <table class="table table-bordered table-hover">
                <tbody>
                    <tr>
                        <td style="width: 30%">Nomor Ijin:</td>
                        <td>
                          <?=$tempNomor[$x]?>
                        </td>
                    </tr>
                    <tr>
                        <td>Tanggal Cetak:</td>
                        <td>
                          <?=$tempTanggalIjin[$x]?>
                        </td>
                    </tr>  
                    <tr>
                        <td>File NIB:</td>
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
          										<a href="<?=$FILE_DIR.$arrFile[$iFile]?>" class="badge badge-primary" target="_blank" class="badge badge-primary"><span class="fa fa-download"></span> Download</a>
          								<?php
                                } else {
                                  echo "-";
                                }
          										}
          									}
          								?>
                        </td>
                    </tr>
                    <?php 
                      if ($tempPKKPR[$x] == '1') { ?>
                      <tr>
                        <td colspan="2"><b>PKKPR</b></td>
                      </tr>
                      <tr>
                        <td style="width: 20%">Tanggal Terbit</td>
                        <td>: <?= getFormattedDateJson($tempTanggalPKKPR[$x]) ?></td>
                      </tr>
                      <tr>
                        <td style="width: 20%">Tanggal Berakhir</td>
                        <td>: <?= getFormattedDateJson($tempTanggalPKKPRBerakhir[$x]) ?></td>
                      </tr>
                      <tr>
                        <td style="width: 20%">File PKKPR</td>
                        <td>:
                          <?php
                            $arrFile2 = explode(";", $tempFileTemp2[$x]);
                            for($iFile=0;$iFile<count($arrFile2);$iFile++)
                            {
                              if ($tempFileTemp2[$x]) {
                                if(file_exists('uploads/ijin_usaha/'.$tempFileTemp2[$x])) {
                                  echo '<a href="'.base_url('uploads/ijin_usaha/').$tempFileTemp2[$x].'" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file</a>';
                                }
                              } else {
                                echo "-";
                              }
                            }
                          ?>
                        </td>
                      </tr>
                      <?php 
                      } else { ?>
                        <tr>
                          <td colspan="2"><b>SELFT DECLARE</b></td>
                        </tr>
                        <tr>
                          <td style="width: 20%">File</td>
                          <td>:
                            <?php
                              $arrFile2 = explode(";", $tempFileTemp2[$x]);
                              for($iFile=0;$iFile<count($arrFile2);$iFile++)
                              {
                                if ($tempFileTemp2[$x]) {
                                  if(file_exists('uploads/ijin_usaha/'.$tempFileTemp2[$x])) {
                                    echo '<a href="'.base_url('uploads/ijin_usaha/').$tempFileTemp2[$x].'" class="badge badge-primary" target="_blank"><span class="fa fa-download"></span> Download file</a>';
                                  }
                                } else {
                                  echo "-";
                                }
                              }
                            ?>
                          </td>
                        </tr>
                      <?php 
                      } ?>
                </tbody>
              </table>

              <!-- <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Bidang Usaha</strong>
              </div>  -->
              <table class="table table-bordered table-hover" id="tbl_bidang">
                <tbody>
                  <tr class="judul-kolom">
                    <th width="10px">No</th>
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
                          <td class="text-center">
                            <?php 
                            // echo $reqStatusValidasi.'--'.$reqUserStatus;
                            if ($reqStatusValidasi == '1' && $reqUserStatus != '0') { // Rekanan sudah tervalidasi
                              if ($rekanan_bidang_usaha->getField("VALIDASI") == 1 ) {
                                   echo '<i class="fa fa-check-square" aria-hidden="true" style="color:blue"></i>';
                                } else {
                                   echo '<i class="fa fa-minus-square" aria-hidden="true" style="color:red"></i>';
                                }
                            } else { 
                              $checked = '';
                              if ($rekanan_bidang_usaha->getField("VALIDASI") == 1) {
                                $checked = 'checked';
                              } ?>
                              
                              <input type="checkbox" name="check<?= $rekanan_bidang_usaha->getField("REKANAN_BIDANG_USAHA_ID") ?>" id="check<?= $rekanan_bidang_usaha->getField("REKANAN_BIDANG_USAHA_ID") ?>" onclick="return updateChecklistKBLI('<?= $reqId ?>','<?= $rekanan_bidang_usaha->getField("REKANAN_BIDANG_USAHA_ID") ?>')" style="cursor:pointer" <?= $checked ?>>
                            <?php 
                            } ?>
                          </td>
                      </tr>
                      <?php
                      $i++;

                      }
                      ?>

                </tbody>
              </table>
            <?php
          } ?>
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
							if ($cekData->getField("nib") == '1') {
								$checked = 'checked';
							}
							echo '<input class="mb-1" type="checkbox" name="checknib" id="checkjenis" onclick="return updateChecklist(\''.$reqId.'\',\'nib\')" style="cursor:pointer" '.$checked.'> Ya, Lengkap ';
							?>
							<input type="" class="form-control easyui-validatebox span2" required maxlength="255" style="height: 30px !important;" name="" placeholder="catatan" id="catatanjenis" value="<?=$cekData->getField("nib_note")?>" onChange="return updateChecklist('<?= $reqId ?>','nib')">
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

      function updateChecklistKBLI(rekananid,id) {
        // alert(rekananid+''+id);
        var n = $("#check"+id+":checked").length;
          $.getJSON("rekanan_json/updateChecklistKBLI/?rekananid="+rekananid+"&id="+id+"&status="+n,
            function(data){
              if (data.RESPONSE === 'Gagal') {
                $.messager.alert('Info', data.PESAN, 'info');
                if (n === 0) { // kalau gagal balik ke awal
                  $("#check"+id).prop("checked", true);
                } else {
                  $("#check"+id).prop("checked", false);
                }
              }
          });
      }
    </script>

  </body>
</html>
