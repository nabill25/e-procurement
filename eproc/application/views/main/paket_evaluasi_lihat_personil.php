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
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$rekanan = new Rekanan();
$rekanan_tenaga_ahli = new RekananTenagaAhli();
$rtap = new RekananTenagaAhliPengalaman();

$reqId = httpFilterGet("reqId");

$FILE_DIR = "uploads/tenaga_ahli_sertifikat/";

$rs = $rekanan_tenaga_ahli->selectByParams(array('REKANAN_TENAGA_AHLI_ID'=>$reqId), -1, -1, $statement);

$whereIn = NULL;
foreach ($rs as $v)
{
//    prepare IN query
    $whereIn .= "'".$v['REKANAN_TENAGA_AHLI_ID']."',";
}
$whereIn = "(".trim($whereIn,',').")";
$rsp = $rtap->selectByParamsExtended(array('REKANAN_TENAGA_AHLI_ID'=>array($whereIn,FALSE,'IN')));

foreach ($rsp as $v) {
    $dpengalaman[$v['REKANAN_TENAGA_AHLI_ID']][] = $v;
}

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
    
    <!-- PAGINATION -->
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" />  	
    <script language="JavaScript" src="jslib/displayElement.js"></script>
    
  </head>

<body class="body-popup">
        <div class="container-fluid">
        	
            <div class="row">
                <div class="col-md-12">
                    <div class="area-main popup">
                        <div class="judul-halaman">TENAGA AHLI</div>
                        <div class="inner">
                            <div class="area-konten">
                                <div class="area-konten-inner">
                                    <form id="ff" class="form-horizontal" role="form" method="post" novalidate enctype="multipart/form-data">
                                        <div class="judul-grup">TENAGA AHLI</div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <table width="100%" border="0" cellpadding="2" cellspacing="1">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="padding-left:10px; padding-right:10px;">
                                                                        
                                                                            <table id="tenaga-ahli" width="100%" border="0" cellpadding="2" cellspacing="1">
                                                                                <? 			
                                                                                $i = 0;
                                                                                while($rekanan_tenaga_ahli->nextRow()){
                                                                                //if($i % 2 == 0)	$css = "gelap";
                                                                                //else			$css = "terang";
                                                                                
                                                                                ?>
                                                                                <tr>
                                                                                    <td colspan="2" style="width:15%;">
                                                                                    Nama
                                                                                    </td>
                                                                                    <td valign="top" colspan="3"> <?=$rekanan_tenaga_ahli->getField("NAMA")?></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td colspan="2">
                                                                                    Tanggal Lahir
                                                                                    </td>
                                                                                    <td valign="top" colspan="3"><?=getFormattedDate($rekanan_tenaga_ahli->getField("TANGGAL_LAHIR"))?></td>
                                                                                </tr>
                                                                                <tr id="reqDetil<?=$i?>">
                                                                                    <td colspan="5">
                                                                                    <div class="area-show-hide-konten">
                                                                                        <table >
                                                                                            <tr class="judul-kolom2">
                                                                                                <td width="10px" style="text-align:center">No</td>
                                                                                                <td style="text-align:center">Pendidikan</td>
                                                                                                <td style="text-align:center" colspan="5">Jurusan</td>
                                                                                              </tr>
                                                                                            <?
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
                                                                                            <? $x++;}?>
                                                                                            <tr class="judul-kolom2">
                                                                                                <td width="10px" style="text-align:center">No</td>
                                                                                                <td width="100px" style="text-align:center">Pekerjaan <br />(Nama Proyek)</td>
                                                                                                <td width="100px" style="text-align:center">Posisi/Jabatan<br />(dalam proyek)</td>
                                                                                                <td width="105px" style="text-align:center">Periode/Lama <br />(bulan)</td>
                                                                                                <td width="50px" style="text-align:center">Tahun</td>
                                                                                                <td width="100px" style="text-align:center">Instansi <br />(Pengguna Jasa)</td>
                                                                                                <td style="text-align:center">Nama Perusahaan <br />Tempat Bekerja</td>
                                                                                            </tr>
                                                                                            <?
                                                                                            if(count($dpengalaman[$rekanan_tenaga_ahli->getField("REKANAN_TENAGA_AHLI_ID")]) > 0)
                                                                                            {
                                                                                                foreach ($dpengalaman[$rekanan_tenaga_ahli->getField("REKANAN_TENAGA_AHLI_ID")] as $v)
                                                                                                {
                                                                                                ?>
                                                                                                <tr class="judul-kolom4">
                                                                                                    <td width="10px" style="text-align:center"><?=$i+1?></td>
                                                                                                    <td style="text-align:center"><?= $v['PEKERJAAN']?></td>
                                                                                                    <td style="text-align:center"><?= $v['POSISI']?></td>
                                                                                                    <td style="text-align:center"><?= $v['PERIODE']?></td>
                                                                                                    <td style="text-align:center"><?= $v['PENGALAMAN']?></td>
                                                                                                    <td style="text-align:center"><?= $v['INSTANSI']?></td>
                                                                                                    <td style="text-align:center"><?= $v['NAMA_PERUSAHAAN']?></td>
                                                                                                </tr>
                                                                                                <?  
                                                                                                }
                                                                                            }
                                                                                            ?>
                                                                                            <tr class="judul-kolom2">
                                                                                                <td width="10px" style="text-align:center">No</td>
                                                                                                <td width="100px" colspan="2" style="text-align:center">Keahlian</td>
                                                                                                <td width="100px" colspan="3" style="text-align:center">No. Serifikat</td>
                                                                                                <td width="50px" style="text-align:center">File</td>
                                                                                            </tr>
                                                                                            <?
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
                                                                                                <td><a href="<?=$FILE_DIR.$nmFile?>" class="taut" target="_blank"><?=$nmFile?></a></td>
                                                                                            </tr>
                                                                                            <? $x++; }?>
                                                                                        </table>
                                                                        
                                                                                    </td>
                                                                                </tr>
                                                                                <? $i++;} ?>      
                                                                            
                                                                            </table>
                                                                            
                                                                        </div>
                                                            
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                            </table>
                                                        <div class="col-md-8">
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
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
