<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("Rekanan");
$this->load->model("RekananTenagaAhli");
$this->load->model("RekananTenagaAhliPengalaman");
$this->load->model("RekananDaftarTenagaAhli");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$rekanan = new Rekanan();
$rekanan_tenaga_ahli = new RekananTenagaAhli();
$rtap = new RekananTenagaAhliPengalaman();

$reqId = $this->input->get("reqId");
$reqPaketId =  $this->input->get("reqPaketId");

$reqSubmit = $this->input->post("reqSubmit");
$reqCatatan = $_POST["reqCatatan"];
$reqDaftarId = $_POST["reqDaftarId"];

$FILE_DIR = "uploads/tenaga_ahli_sertifikat/";

if($reqSubmit == "Submit")
{
	for($i=0;$i<count($reqDaftarId);$i++)
	{
		$rekanan_daftar_tenaga_ahli = new RekananDaftarTenagaAhli();
		$rekanan_daftar_tenaga_ahli->setField("CATATAN", $reqCatatan[$i]);
		$rekanan_daftar_tenaga_ahli->setField("REKANAN_DAFTAR_TENAGA_AHLI_ID", $reqDaftarId[$i]);
		$rekanan_daftar_tenaga_ahli->updateCatatan();
		unset($rekanan_daftar_tenaga_ahli);
	}
}


if($reqPaketId == "")
	$statement = "";
else
	$statement = " AND EXISTS(SELECT 1 FROM REKANAN_DAFTAR_TENAGA_AHLI X WHERE X.REKANAN_TENAGA_AHLI_ID = REKANAN_TENAGA_AHLI_ID AND X.PAKET_ID = '".$reqPaketId."') ";

$allRecord = 1;
// $rs = $rekanan_tenaga_ahli->selectByParamsSyarat(array('B.REKANAN_ID'=>$reqId, "B.PAKET_ID" => $reqPaketId), -1, -1, $statement);
//$allRecord = $rekanan_tenaga_ahli->getCountByParams(array('REKANAN_ID'=>$reqId), $statement);
//$rs = $rekanan_tenaga_ahli->selectByParams(array('REKANAN_ID'=>$reqId), -1, -1, $statement);
// $whereIn = NULL;
// foreach ($rs as $v)
// {
//     // prepare IN query
//     $whereIn .= "'".$v['REKANAN_TENAGA_AHLI_ID']."',";
// }
// $whereIn = "(".trim($whereIn,',').")";
// $rsp = $rtap->selectByParamsExtended(array('REKANAN_TENAGA_AHLI_ID'=>array($whereIn,FALSE,'IN')));

// foreach ($rsp as $v) {
//     $dpengalaman[$v['REKANAN_TENAGA_AHLI_ID']][] = $v;
// }

$rekanan->selectByParams(array("REKANAN_ID"=>$reqId),-1,-1);
$rekanan->firstRow();
$tempNama= $rekanan->getField("NAMA");
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
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>TENAGA AHLI</strong>  
        </div> 
        <div class="table-responsive">
            <form action="" name="frmDaftarAlamat" method="post" enctype="multipart/form-data">
                <table class="table table-bordered" border="0" cellpadding="2" cellspacing="1" id="tbl_bidang">
                  <tbody>
                    <tr>
                        <th style="width:10px">No</th>
                        <th>Nama</th>
                        <th>Tanggal Lahir</th>
                        <th >Catatan</th>
                    </tr>
                    <?php	
					$i = 0;
					if($allRecord > 0)
					{
						while($rekanan_tenaga_ahli->nextRow())
						{
					?> 
							<tr class="<?=$style?>">
								 <td align="center" valign="top"><?=$i+1?></td>
								 <td valign="top"> <a onclick="displayElement('reqDetil<?=$i?>')" style="cursor:pointer" id="rekanan<?=$i?>">
										<?=$rekanan_tenaga_ahli->getField("NAMA")?>
										</a>
								</td>
								 <td valign="top"><?=getFormattedDate($rekanan_tenaga_ahli->getField("TANGGAL_LAHIR"))?></td>
                                 <td valign="top" colspan="3"> 
                                    <textarea class="form-control" name="reqCatatan[]" style="width:100%"><?=$rekanan_tenaga_ahli->getField("CATATAN")?></textarea>
                                    <input type="hidden" name="reqDaftarId[]" value="<?=$rekanan_tenaga_ahli->getField("REKANAN_DAFTAR_TENAGA_AHLI_ID")?>" />
                                </td>
							</tr>
							<tr id="reqDetil<?=$i?>" style="display:none;">
                                <td colspan="5">
                                
                                <div class="area-show-hide-konten">
                                 <table>
                                    <tr class="judul-kolom2">
                                        <td width="10px" style="text-align:center">No</td>
                                        <td style="text-align:center">Pendidikan</td>
                                        <td style="text-align:center" colspan="5">Jurusan</td>
                                      </tr>
                                    <?php
                                    $array_pendidikan = explode("* ",$rekanan_tenaga_ahli->getField("PENDIDIKAN"));
                                    //print_r($array_pendidikan);
                                    $x=0;
                                    while($x < count($array_pendidikan)){
                                    $array_pendidikan_isi = explode("-",$array_pendidikan[$x]);
                                    
                                    $nmJurusan = str_replace("(","",$array_pendidikan_isi[0]);
                                    $nmPendidikan = str_replace(")","",$array_pendidikan_isi[1]);
                                    ?>
                                    <tr class="judul-kolom4">
                                        <td width="10px" style="text-align:center"><?=$x+1?></td>
                                        <td style="text-align:center"><?=$nmJurusan?></td>
                                        <td style="text-align:center" colspan="5"><?=$nmPendidikan?></td>
                                      </tr>
                                    <?php $x++;}?>
                                    <tr class="judul-kolom2">
                                        <td width="10px" style="text-align:center">No</td>
                                        <td width="100px" style="text-align:center">Nama Proyek</td>
                                        <td width="100px" style="text-align:center">Posisi/Jabatan</td>
                                        <td width="105px" style="text-align:center">Periode/Lama</td>
                                        <td width="50px" style="text-align:center">Tahun</td>
                                        <td width="100px" style="text-align:center">Instansi</td>
                                        <td width="50px" style="text-align:center">Nama Perusahaan</td>
                                      </tr>
                                  <?php
									 $rekanan_tenaga_ahli_pengalaman = new RekananTenagaAhliPengalaman();
									 $rekanan_tenaga_ahli_pengalaman->selectByParams(array('REKANAN_TENAGA_AHLI_ID'=>$rekanan_tenaga_ahli->getField("REKANAN_TENAGA_AHLI_ID")), -1, -1, $statement);
									 $y=1;
									 while($rekanan_tenaga_ahli_pengalaman->nextRow())
								  {
								  ?>
									<tr class="judul-kolom4">
										<td width="10px" style="text-align:center"><?=$y?></td>
										<td style="text-align:center"><?=$rekanan_tenaga_ahli_pengalaman->getField("PEKERJAAN") ?></td>
										<td style="text-align:center"><?=$rekanan_tenaga_ahli_pengalaman->getField("POSISI") ?></td>
										<td style="text-align:center"><?=$rekanan_tenaga_ahli_pengalaman->getField("PERIODE") ?></td>
										<td style="text-align:center"><?=$rekanan_tenaga_ahli_pengalaman->getField("PENGALAMAN") ?></td>
										<td style="text-align:center"><?=$rekanan_tenaga_ahli_pengalaman->getField("INSTANSI") ?></td>
										<td style="text-align:center"><?=$rekanan_tenaga_ahli_pengalaman->getField("NAMA_PERUSAHAAN") ?></td>
									</tr>
									<?php
									   $y++; 
									 }
									 unset($rekanan_tenaga_ahli_pengalaman);
									?>
                                    <tr class="judul-kolom2">
                                        <td width="10px" style="text-align:center">No</td>
                                        <td width="100px" colspan="2" style="text-align:center">Keahlian</td>
                                        <td width="100px" colspan="2" style="text-align:center">No. Serifikat</td>
                                        <td width="100px" colspan="2" style="text-align:center">File</td>
                                      </tr>
                                    <?php
                                    $array_pendidikan = explode(" # ",$rekanan_tenaga_ahli->getField("SERTIFIKAT"));
                                    //echo print_r($array_pendidikan);
                                    //$array_pendidikan = explode(", ",$rekanan_tenaga_ahli->getField("SERTIFIKAT"));
                                    $x=0;
                                    while($x < count($array_pendidikan)){
                                    $array_pendidikan_isi = explode("* ",$array_pendidikan[$x]);
                                    
                                    $nmKeahlian = str_replace(")","",str_replace("(","",$array_pendidikan_isi[0]));//str_replace("(","",$array_pendidikan_isi[0]);
                                    $nmNomor = $array_pendidikan_isi[1];
                                    $nmFile = str_replace(")","",str_replace("(","",$array_pendidikan_isi[2]));//str_replace(")","",$array_pendidikan_isi[2]);
                                    
                                    /*$array_pendidikan_isi = explode("*",$array_pendidikan[$x]);
                                    
                                    $nmKeahlian = str_replace("(","",$array_pendidikan_isi[0]);
                                    $nmNomor = $array_pendidikan_isi[1];
                                    $nmFile = str_replace(")","",$array_pendidikan_isi[2]);*/
                                    ?>
                                    <tr class="judul-kolom4">
                                        <td width="10px" style="text-align:center"><?=$x+1?></td>
                                        <td style="text-align:center" colspan="2"><?=$nmKeahlian?></td>
                                        <td style="text-align:center" colspan="3"><?=$nmNomor?></td>
                                        <td><a href="<?=$FILE_DIR.$nmFile?>" class="badge badge-primary" target="_blank"><?=$nmFile?></a></td>
                                    </tr>
                                    <?php $x++; }
                                    ?>
                                </table>
                                </div>
                                 </td>
                                </tr>                                                                
						<?php 
						$i++;
						}
					}
					else
					{
                    ?>
                        <tr>
                            <td colspan="3">.: data belum ada :.</td>
                        </tr>
					<?php 
					}
					?>  
                  </tbody>
                  
                </table> 
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
