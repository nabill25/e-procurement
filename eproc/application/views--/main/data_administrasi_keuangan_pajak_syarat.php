<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

/* INCLUDE FILE */
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Rekanan");
$this->load->model("RekananPajak");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$rekanan = new Rekanan();
$rekanan_pph	= new RekananPajak(); // tipe 2
$rekanan_ppn	= new RekananPajak(); // tipe 3

$reqId = $this->ID;
$reqPaketId = $this->input->get("reqPaketId");
$reqTipe = $this->input->get("reqTipe");


$paketInfo->getPaket($reqPaketId);

// $rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
// $rekanan->firstRow();
$rekanan_pph->selectByParams(array("TIPE"=>2, "REKANAN_ID"=>$reqId), -1, -1, " AND BULAN || TAHUN IN (".$paketInfo->syarat_keuangan_bulan_pph.") ", " ORDER BY TO_DATE(LPAD(BULAN, 2, '0') || TAHUN, 'MMYYYY') ASC ");
$i=0;
while($rekanan_pph->nextRow()){
	$arrNomorPPH[$i] = $rekanan_pph->getField('NOMOR');
	$arrTanggalPPH[$i] = dateToPageCheck($rekanan_pph->getField('TANGGAL'));
	$arrLinkFilePPH[$i] = $rekanan_pph->getField('PATH_FILE');
	$arrLinkFilePPHNama[$i] = $rekanan_pph->getField('NAMA_FILE');
	$i++;
}
$rekanan_ppn->selectByParams(array("TIPE"=>3, "REKANAN_ID"=>$reqId), -1, -1, " AND BULAN || TAHUN IN (".$paketInfo->syarat_keuangan_bulan_ppn.") ", " ORDER BY TO_DATE(LPAD(BULAN, 2, '0') || TAHUN, 'MMYYYY') ASC ");
$i=0;
while($rekanan_ppn->nextRow()){
	$arrNomorPPN[$i] = $rekanan_ppn->getField('NOMOR');
	$arrTanggalPPN[$i] = dateToPageCheck($rekanan_ppn->getField('TANGGAL'));
	$arrLinkFilePPN[$i] = $rekanan_ppn->getField('PATH_FILE');
	$arrLinkFilePPNNama[$i] = $rekanan_ppn->getField('NAMA_FILE');
	$i++;
}

if($reqTipe == 2){
$arrSyaratBulan = explode(", ",$paketInfo->syarat_keuangan_bulan_pph);
}elseif($reqTipe == 3){
$arrSyaratBulan = explode(", ",$paketInfo->syarat_keuangan_bulan_ppn);
}

$info_bulan='';
for($i=0; $i < 3; $i++){
	if($info_bulan == '')
		$info_bulan .= getNameMonth(substr($arrSyaratBulan[$i],0,-4))." ".substr($arrSyaratBulan[$i],strlen($arrSyaratBulan[$i])-4,strlen($arrSyaratBulan[$i]));
	else
		$info_bulan .= ', '.getNameMonth(substr($arrSyaratBulan[$i],0,-4))." ".substr($arrSyaratBulan[$i],strlen($arrSyaratBulan[$i])-4,strlen($arrSyaratBulan[$i]));
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

    <!-- EASYUI -->
    <link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
	<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <script type="text/javascript" src="lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="lib/eproc/allfunc.js"></script>

	<script type="text/javascript">
    $(document).ready(function() {

        $(function(){
            $('#ff').form({
                url:'rekanan_pajak_json/data_administrasi_keuangan_pajak_syarat',
                onSubmit:function(){
                    return $(this).form('validate');
                },
                success:function(data){
					<?php
					if($reqTipe == 2)
						$reqFieldKeterangan = "reqDataKeuanganPPH";
					else
						$reqFieldKeterangan = "reqDataKeuanganPPN";
					?>
					if(data == "1")
					{
						top.setElementValue('<?=$reqFieldKeterangan?>','Data Lengkap');
					}
					top.reloadPajak('<?=$reqTipe?>');
					top.closePopup();
                }
            });

        });

    });
    </script>

 </head>


<body class="body-popup">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="area-main popup">
                    <div class="judul-halaman">Data Administrasi - Keuangan </div>
                    <div class="inner">
                        <div class="area-konten">

                            <div class="area-konten-inner">

                                <div class="informasi">
                                    <ul>
                                        <li>Silahkan melengkapi terlebih dahulu Pajak bulan <?=$info_bulan?>.</li>
                                    </ul>
                                </div>

                                <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="col-md-8">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    if($reqTipe == 2)
									{
									?>
                                    <div class="judul-grup">Laporan Pajak Bulanan PPH
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <table width="100%" border="0" cellpadding="2" cellspacing="1" id="tbl_bidang">
                                                  <tbody>
                                                    <tr class="judul-kolom">
                                                      <th>Bulan</th>
                                                      <th>Nomor</th>
                                                      <th>Tanggal</th>
                                                      <th>File</th>
                                                    </tr>
                                                    <?php
                                                      for($i=0;$i<count($arrSyaratBulan);$i++)
                                                      {
														  $periode = generateZero($arrSyaratBulan[$i], 6);
                                                      ?>
                                                         <tr>
                                                             <td><?=getNamePeriode($periode)?><input type="hidden" name="reqBulanPPH[]" value="<?=$periode?>"></td>
                                                             <td><input id="reqNomorPPH<?=$i?>" class="form-control easyui-validatebox" name="reqNomorPPH[]" size="40" maxlength="50" value="<?=$arrNomorPPH[$i]?>" type="text" /></td>
                                                             <td><input type="text"  class="form-control easyui-datebox" style="width:120px" name="reqTanggalPPH[]" id="reqTanggalPPH<?=$i?>" value="<?=$arrTanggalPPH[$i]?>" /></td>
                                                             <td><input type="file"  name="reqLinkFilePPH[]" class="easyui-validatebox" validType="fileType['pdf']"  />
                                                                <input type="hidden" name="reqLinkFilePPHTemp[]" value="<?=$arrLinkFilePPH[$i]?>">
                                                   				<input type="hidden" name="reqLinkFilePPHTempNama[]" value="<?=$arrLinkFilePPHNama[$i]?>">
                                                                <span style="font-size:9px;">temp : <?=$arrLinkFilePPHNama[$i]?></span></td>
                                                        </tr>
                                                    <?php
                                                      }
                                                      ?>
                                                  </tbody>
                                                </table>
                                                <div class="col-md-8">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
									}
									?>
                                    <?php
                                    if($reqTipe == "3")
									{
									?>
                                    <div class="judul-grup">Laporan Pajak Bulanan PPN
                                    </div>
                                     <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <table width="100%" border="0" cellpadding="2" cellspacing="1" id="tbl_bidang">
                                                  <tbody>
                                                    <tr class="judul-kolom">
                                                      <th>Bulan</th>
                                                      <th>Nomor</th>
                                                      <th>Tanggal</th>
                                                      <th>File</th>
                                                    </tr>
                                                    <?php

                                                      for($i=0;$i<count($arrSyaratBulan);$i++)
                                                      {
														   $periode = generateZero($arrSyaratBulan[$i], 6);
                                                      ?>
                                                         <tr >
                                                             <td><?=getNamePeriode($periode)?><input type="hidden" name="reqBulanPPN[]" value="<?=$periode?>"></td>
                                                             <td><input id="reqNomorPPN<?=$i?>" class="form-control easyui-validatebox" name="reqNomorPPN[]" size="40" maxlength="50" value="<?=$arrNomorPPN[$i]?>" type="text" /></td>
                                                             <td><input type="text" style="width:120px" class="form-control easyui-datebox" name="reqTanggalPPN[]" id="reqTanggalPPN<?=$i?>" value="<?=$arrTanggalPPN[$i]?>" /></td>
                                                             <td><input type="file" name="reqLinkFilePPN[]" class="easyui-validatebox" validType="fileType['pdf']"  />
                                                                <input type="hidden" name="reqLinkFilePPNTemp[]" value="<?=$arrLinkFilePPN[$i]?>">
                                                    			<input type="hidden" name="reqLinkFilePPNTempNama[]" value="<?=$arrLinkFilePPNNama[$i]?>">
                                                                <span style="font-size:9px;">temp : <?=$arrLinkFilePPNNama[$i]?></span></td>
                                                        </tr>
                                                    <?php
                                                      }
                                                      ?>
                                                  </tbody>
                                                </table>
                                                <div class="col-md-8">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
									}
									?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputEmail" class="col-md-2 control-label">&nbsp;</label>
                                                <div class="col-md-4">
                                                   <input type="hidden" name="reqSubmit" id="reqSubmit"/>
                                                   <input type="hidden" name="reqTipe" value="<?=$reqTipe?>"/>
                                                   <input type="hidden" name="reqSyaratPPH" value="<?=$paketInfo->syarat_keuangan_bulan_pph?>"/>
                                                   <input type="hidden" name="reqSyaratPPN" value="<?=$paketInfo->syarat_keuangan_bulan_ppn?>"/>
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                    <button type="button" class="btn btn-primary" onClick="top.closePopup()">Batal</button>
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
	</div>
</body>
