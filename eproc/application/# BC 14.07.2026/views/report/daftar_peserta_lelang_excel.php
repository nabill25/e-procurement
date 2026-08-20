<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->model("PaketRekanan");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("lib/phpqrcode/qrlib.php");

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=daftar_peserta.xls");

/* create objects */
$paket_rekanan = new PaketRekanan();
$paket_rekanan_detil = new PaketRekanan();

$reqId = $this->input->get("reqId");

$field = array('REKANAN', 'TANGGAL_UNDANG','TANGGAL_DAFTAR','LULUS_PENDAFTARAN', 'LULUS_KUALIFIKASI', 'LULUS_PENAWARAN', 'LULUS_PENAWARAN_URUT', 'STATUS_BAYAR');

$start = 6; $number = 1;
//$reqId = 120;

$allrecord = $paket_rekanan->getCountByParams(array("PAKET_ID" => $reqId));
$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId));

$paket_rekanan_detil->selectByParams(array("PAKET_ID" => $reqId),-1,-1,'',$reqId);
$paket_rekanan_detil->firstRow();
//echo $paket_rekanan_detil->query;exit;
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
                <div class="judul-laporan" align="left"><font size="+1"><b>Daftar Peserta <?=$paket_rekanan_detil->getField("PROSES_PEMILIHAN")?></b></font></div>
                <div class="judul-laporan" align="left"><font size="+1"><b><?=$paket_rekanan_detil->getField("NAMA_PEKERJAAN")?></b></font></div>
                <br>
            </div>
        </div>
        <div class="isi">
        </div>
        <br>
        <div class="data-laporan">
        <table>
    	    <thead>
	    	    <tr>
                	<th align="center">No</th>
                    <th align="center">Nama Perusahaan</th>
                    <th align="center">Tanggal Daftar</th>
                </tr>
             </thead>
             <tbody>
             <?php
             	$no=1;
				while($paket_rekanan->nextRow())
				{
					$temp1=$temp2=$temp3=$temp4=$temp5='';
			?>
             	<tr>
                	<td align="center"><?=$no?></td>
                    <td align="center">
                        <?= '<b>'.$paket_rekanan->getField("FULL_NAMA_REKANAN").''?>
                    </td>
                    <!-- <td align="center"><?php //getFormattedDateJson($paket_rekanan->getField($field[1]));?></td> -->
                    <td align="center"><?=getFormattedDateJson($paket_rekanan->getField($field[2]));?></td> 
                </tr>
			<?php
				$no++;
	            }
            ?>
             </tbody>
        </table>
        </div>
	</body>
</html>