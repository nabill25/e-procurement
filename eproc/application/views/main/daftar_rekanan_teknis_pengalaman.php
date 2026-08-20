<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("RekananPengalaman");
$this->load->model("Rekanan");
$this->load->model("RekananDaftarPengalaman");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$rekanan_pengalaman				= new RekananPengalaman(); // tipe 0
$rekanan_pengalaman_progress	= new RekananPengalaman(); // tipe 0
$rekanan_get_nama = new Rekanan();

/* VARIABLE */
$reqId = $this->input->get("reqId");
$reqPaketId =  $this->input->get("reqPaketId");
$reqCari = $this->input->post("reqCari");

$reqSubmit = httpFilterPost("reqSubmit");
$reqCatatan = isset($_POST["reqCatatan"])?$_POST["reqCatatan"]:'';
$reqDaftarId = isset($_POST["reqDaftarId"])?$_POST["reqDaftarId"]:'';
/* VALIDATION */
$FILE_DIR = "uploads/pengalaman/";
// trigger the validation

if($reqSubmit == "Submit")
{
	for($i=0;$i<count($reqDaftarId);$i++)
	{
		$rekanan_daftar_pengalaman = new RekananDaftarPengalaman();
		$rekanan_daftar_pengalaman->setField("CATATAN", $reqCatatan[$i]);
		$rekanan_daftar_pengalaman->setField("REKANAN_DAFTAR_PENGALAMAN_ID", $reqDaftarId[$i]);
		$rekanan_daftar_pengalaman->updateCatatan();
		unset($rekanan_daftar_pengalaman);
	}
}

/* ACTION BY REQMODE */
if($reqPaketId == "")
	$statement = "";
else
	$statement = " AND EXISTS(SELECT 1 FROM REKANAN_DAFTAR_PENGALAMAN X WHERE X.REKANAN_PENGALAMAN_ID = A.REKANAN_PENGALAMAN_ID AND X.PAKET_ID = '".$reqPaketId."') ";

$allRecord_K = 1;
$rekanan_pengalaman->selectByParamsSyarat(array("A.REKANAN_ID"=>$reqId,"B.PAKET_ID"=>$reqPaketId), -1, -1, $statement);
//$allRecord_K = $rekanan_pengalaman->getCountByParams(array("REKANAN_ID"=>$reqId,"KONTRAK_STATUS"=>1),  $statement);
//$rekanan_pengalaman->selectByParams(array("REKANAN_ID"=>$reqId,"KONTRAK_STATUS"=>1), -1, -1, $statement);

$allRecord_progress = $rekanan_pengalaman_progress->getCountByParams(array("REKANAN_ID"=>$reqId,"KONTRAK_STATUS"=>2),  $statement);
$rekanan_pengalaman_progress->selectByParams(array("REKANAN_ID"=>$reqId,"KONTRAK_STATUS"=>2), -1, -1, $statement);

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
          <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>PENGALAMAN</strong>  
        </div> 
        <p class="alert alert-primary" style="background-color: transparent !important;"><b><?= $tempNama_getNama ?></b></p>
        <div class="table-responsive">
          <form action="" name="frmDaftarAlamat" method="post" enctype="multipart/form-data">
            <div class="alert alert-info">Pekerjaan Selesai </div>
            <table width="100%" class="table table-bordered" border="0" cellpadding="2" cellspacing="1" id="tbl_bidang">
              <tbody>
                <tr class="judul-kolom">
                  <th>No</th>
                  <th>Nama Pekerjaan </th>
                  <th>Bidang Pekerjaan</th>
                  <th>Lokasi</th>
                  <th>File SPK</th>
                  <th>File BA</th>
                  <th>Catatan</th>
                </tr>
                <?php if($allRecord_K > 0)
                  {
                    $i = 0;
                    $rcI=0;
                    while($rekanan_pengalaman->nextRow())
                    {
                  ?>   
                      <tr>
                          <td><?=$rcI+1?></td>
                          <td><a class="taut" onclick="displayElement('reqDetil<?=$i?>')" style="cursor:pointer" id="rekanan<?=$i?>"><?=$rekanan_pengalaman->getField("NAMA")?></a></td>
                          <input type="hidden" id="valTem<?=$i?>">
                          <td><?=$rekanan_pengalaman->getField("PENGALAMAN_BIDANG")?></td>
                          <td><?=$rekanan_pengalaman->getField("LOKASI")?></td>
                          <td><a href="<?=$FILE_DIR.$rekanan_pengalaman->getField("PATH_FILE")?>" class="badge badge-primary" target="_blank">Download</a></td>
                          <td><a  href="<?=$FILE_DIR.$rekanan_pengalaman->getField("PATH_FILE_BA")?>" class="badge badge-primary" target="_blank">Download</a></td>
                        <td valign="top"> 
                          <textarea class="form-control" name="reqCatatan[]" style="width:98%"><?=$rekanan_pengalaman->getField("CATATAN")?></textarea>
                          <input type="hidden" name="reqDaftarId[]" value="<?=$rekanan_pengalaman->getField("REKANAN_DAFTAR_PENGALAMAN_ID")?>" />
                        </td>
                      </tr>
                      <tr id="reqDetil<?=$i?>" style="display:none">
                        <td colspan="6">
                          <table id="keahlian" width="100%" border="0" cellpadding="2" cellspacing="1">
                            <tr class="baris-spek">
                              <td colspan="4">Pemberi Tugas</td>
                            </tr>
                            <tr class="baris-ket">
                              <td colspan="2">Nama</td>
                              <td width="3%">:</td>
                              <td width="76%"><?=$rekanan_pengalaman->getField("PEMBERI_TUGAS")?></td>
                            </tr>
                            <tr class="baris-ket">
                              <td colspan="2">Alamat</td>
                              <td>:</td>
                              <td><?=$rekanan_pengalaman->getField("PEMBERI_TUGAS_ALAMAT")?></td>
                            </tr>
                            <tr>
                              <td colspan="4">&nbsp;</td>
                            </tr>
                            <tr class="baris-spek">
                              <td colspan="4">Kontrak</td>
                            </tr>
                            <tr class="baris-ket">
                              <td colspan="2">No</td>
                              <td>:</td>
                              <td><?=$rekanan_pengalaman->getField("KONTRAK_NOMOR")?></td>
                            </tr>
                            <tr class="baris-ket">
                              <td colspan="2">Tanggal</td>
                              <td>:</td>
                              <td><?=getFormattedDate($rekanan_pengalaman->getField("KONTRAK_TANGGAL"))?></td>
                            </tr>
                            <tr class="baris-ket">
                              <td colspan="2">Nilai</td>
                              <td>:</td>
                              <td><?=currencyToPage($rekanan_pengalaman->getField("KONTRAK_NILAI"))?></td>
                            </tr>
                            <tr>
                              <td colspan="4">&nbsp;</td>
                            </tr>
                            <tr class="baris-spek">
                              <td colspan="4">Tanggal Selesai</td>
                            </tr>
                            <tr class="baris-ket">
                              <td colspan="2">Penyerahan</td>
                              <td>:</td>
                              <td><?=getFormattedDate($rekanan_pengalaman->getField("BA_TANGGAL"))?></td>
                            </tr>
                          </table>
                        </td>
                      </tr>                                                                
                        <?php
                      $i++;$rcI++;
                    }
                  } else
                  {
                  ?>
                    <tr>
                        <td colspan="4">.: data belum ada :.</td>
                    </tr>
                  <?php
                    }
                  ?>
              </tbody>
            </table>
             
            <div class="alert alert-info">Pekerjaan Dalam Proses</div>
            <table width="100%" class="table table-bordered" border="0" cellpadding="2" cellspacing="1" id="tbl_bidang">
              <tbody>
                <tr class="judul-kolom">
                  <th>No</th>
                  <th>Nama Pekerjaan </th>
                  <th>Bidang Pekerjaan</th>
                  <th>Proses</th>
                  <th>Lokasi</th>
                  <th>File</th>
                  <th>Catatan</th>
                </tr>
                <?php
                 if($allRecord_progress > 0)
                {
                  $i = 0;
                  while($rekanan_pengalaman_progress->nextRow())
                  {
                ?>   
                    <tr class="baris-nama">
                      <td><?=$rcI+1?></td>
                      <td><a onclick="displayElement('reqDetilProgress<?=$i?>')" style="cursor:pointer" id="rekanan<?=$i?>"><?=$rekanan_pengalaman_progress->getField("NAMA")?></a></td>
                      <input type="hidden" id="valTem<?=$i?>">
                      <td><?=$rekanan_pengalaman_progress->getField("PENGALAMAN_BIDANG")?></td>
                      <td><?=$rekanan_pengalaman_progress->getField("PROGRESS")?></td>
                      <td><?=$rekanan_pengalaman_progress->getField("LOKASI")?></td>
                      <td><a href="<?=$FILE_DIR.$rekanan_pengalaman_progress->getField("PATH_FILE")?>" class="taut" target="_blank">Download</a></td>
                      <td valign="top"> 
                        <textarea name="reqCatatan[]" class="table table-bordered" style="width:98%"><?=$rekanan_pengalaman->getField("CATATAN")?></textarea>
                        <input type="hidden" name="reqDaftarId[]" value="<?=$rekanan_pengalaman->getField("REKANAN_DAFTAR_PENGALAMAN_ID")?>" />
                        </td>
                    </tr>
                    <tr id="reqDetilProgress<?=$i?>" style="display:none">
                    <td colspan="6">
                      <table id="keahlian" width="100%" border="0" cellpadding="2" cellspacing="1">
                        <tr class="baris-spek">
                          <td colspan="4">Pemberi Tugas</td>
                        </tr>
                        <tr class="baris-ket">
                          <td colspan="2">Nama</td>
                          <td width="3%">:</td>
                          <td width="76%"><?=$rekanan_pengalaman_progress->getField("PEMBERI_TUGAS")?></td>
                        </tr>
                        <tr class="baris-ket">
                          <td colspan="2">Alamat</td>
                          <td>:</td>
                          <td><?=$rekanan_pengalaman_progress->getField("PEMBERI_TUGAS_ALAMAT")?></td>
                        </tr>
                        <tr>
                          <td colspan="4">&nbsp;</td>
                        </tr>
                        <tr class="baris-spek">
                          <td colspan="4">Kontrak</td>
                        </tr>
                        <tr class="baris-ket">
                          <td colspan="2">No</td>
                          <td>:</td>
                          <td><?=$rekanan_pengalaman_progress->getField("KONTRAK_NOMOR")?></td>
                        </tr>
                        <tr class="baris-ket">
                          <td colspan="2">Tanggal</td>
                          <td>:</td>
                          <td><?=getFormattedDate($rekanan_pengalaman_progress->getField("KONTRAK_TANGGAL"))?></td>
                        </tr>
                        <tr class="baris-ket">
                          <td colspan="2">Nilai</td>
                          <td>:</td>
                          <td><?=currencyToPage($rekanan_pengalaman_progress->getField("KONTRAK_NILAI"))?></td>
                        </tr>
                        <tr>
                          <td colspan="4">&nbsp;</td>
                        </tr>
                        <tr class="baris-spek">
                          <td colspan="4">Tanggal Selesai</td>
                        </tr>
                        <tr class="baris-ket">
                          <td colspan="2">Penyerahan</td>
                          <td>:</td>
                          <td><?=getFormattedDate($rekanan_pengalaman_progress->getField("BA_TANGGAL"))?></td>
                        </tr>
                      </table>
                    </td>
                    </tr>
                  <?php
                   $i++;$rcI++;
                   }
                } else
                {
               ?>
                <tr>
                   <td colspan="5">.: data belum ada :.</td>
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
