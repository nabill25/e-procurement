<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->model("Paket");
$this->load->model("PermohonanPaket");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("lib/phpqrcode/qrlib.php");

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=rencana-pengadaan.xls");

$reqStatus = $this->input->get("reqStatus");
$reqMode = $this->input->get("reqMode");

	$arrStatement = array("A.APPROVAL|| IN" => "('1','2','3')", "A.ADMIN_RUP" => $this->USER_LOGIN_ID);	 
	$statement    = "";

	$permohonan_paket = new PermohonanPaket();
	$permohonan_paket->selectByParamsUsulan($arrStatement, $dsplyRange, $dsplyStart, $statement);
	
?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<base href="<?=base_url()?>" />
	</head>
	<body>
    <div class="kop-laporan">
            <div class="info">
                
            </div>
        </div>
        <div class="isi" align="center">
        	PERMOHONAN PAKET USULAN DAN ANALISA KEBUTUHAN <?= SYSTEM_NAME_PT ?> <br>
        </div>
        <br>
        <div class="data-laporan">
       		<table id="example" class="display" cellspacing="0" width="100%" border="1">
              <thead>
                   <tr>
                   		<th>No.</th>
                        <th>Tahun Anggaran</th>    
                        <th>Nama Kebutuhan</th>
                        <th>Perkiraan Biaya</th>
                        <th>Waktu Penggunaan</th>
                        <th>Rencana Pengadaan</th>    
                        <th>Cara Pengadaan</th>    
                        <th>User</th>    
                    </tr>         
                </thead>
                <tbody>
			 <?php
			 	$number = 1;
				while($permohonan_paket->nextRow())
				{
             ?>
             	<tr>
                	<td align="center"><?=$number?></td>
                    <td align="center"><?=$permohonan_paket->getField("TAHUN_ANGGARAN")?></td>
                    <td align="center"><?=$permohonan_paket->getField("NAMA_KEBUTUHAN")?></td>
                    <td align="center"><?=numberToIna($permohonan_paket->getField("PERKIRAAN_BIAYA_HARGA"))?></td>
                    <td align="center"><?=getFormattedDateJson($permohonan_paket->getField("WAKTU_PENGGUNA_BARANGJASA"))?></td>
                    <td align="center"><?=getFormattedDateJson($permohonan_paket->getField("RENCANA_PENGADAAN"))?></td>
                    <td align="center"><?=$permohonan_paket->getField("CARA_PENGADAAN_STR")?></td>
                    <td align="center"><?=$permohonan_paket->getField("PEMBUAT")?></td>
                </tr>
			<?php
	           $number++;
			}
            ?>
             </tbody>
            </table>
        </div>
	</body>
</html>