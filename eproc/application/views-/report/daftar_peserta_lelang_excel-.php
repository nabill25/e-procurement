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
header("Content-Disposition: attachment; filename=daftar_peserta_lelang.xls");

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
                <div class="judul-laporan" align="left"><font size="+1"><b>Proses <?=$paket_rekanan_detil->getField("PROSES_PEMILIHAN")?></b></font></div>
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
                    <!-- <th align="center">Diundang</th> -->
                    <th align="center">Tanggal Daftar</th>
                    <th align="center">Lulus Pendaftaran</th>
                    <!-- <th align="center">Lulus Kualifikasi</th> -->
                    <th align="center">Dok Penawaran</th>
                    <th align="center">Lulus Penawaran</th>
                    <!-- <th align="center">Sudah Bayar</th> -->
                </tr>
             </thead>
             <tbody>
             <?
             	$no=1;
				while($paket_rekanan->nextRow())
				{
					$temp1=$temp2=$temp3=$temp4=$temp5='';
			?>
             	<tr>
                	<td align="center"><?=$no?></td>
                    <td align="center"><?=$paket_rekanan->getField($field[0]);?></td>
                    <!-- <td align="center"><?php //getFormattedDateJson($paket_rekanan->getField($field[1]));?></td> -->
                    <td align="center"><?=getFormattedDateJson($paket_rekanan->getField($field[2]));?></td>
                    <?
                    if($paket_rekanan->getField($field[3])== 1)
						$temp1 = 'v';
					if($paket_rekanan->getField($field[4])== 1)
						$temp2 = 'v';
					if($paket_rekanan->getField($field[5])== 1)
						$temp3 = 'v';
					if($paket_rekanan->getField($field[6])== 1)
						$temp4 = 'v';
					if($paket_rekanan->getField($field[7])== 1)
						$temp5 = 'v';
					?>
                    <td align="center"><?=$temp1;?></td>
                    <!-- <td align="center"><?=$temp2;?></td> -->
                    <td align="center"><?=$temp3;?></td>
                    <td align="center"><?=$temp4;?></td>
                    <!-- <td align="center"><?=$temp5;?></td> -->
                </tr>
			<?
				$no++;
	            }
            ?>
             </tbody>
        </table>
		<?php /*?><table border="1">
            <tbody>
              <tr>
                <td width="106" align="center"><b>Biro Pengadaan Barangdan Jasa</b></td>
                <td width="800" align="center"><b>Nama Jelas</b></td>
                <td width="164" align="center"><b>Tanda Tangan</b></td>
              </tr>
               <?
			  $no = 1;
			  for($i=0;$i<count($arrNama);$i++)
			  {
				  if($arrJenis[$i] == "PANITIA")
				  {
					  ?>
              <tr>
                <td><?=$no;?>.</td>
                <td><?=$arrNama[$i]?></td>
                <td>
                <?
                if($arrKodeQr[$i] == "") {}
				else
				{
                    $encrypt_text = $arrKodeQr[$i];
                    $filename = $PNG_TEMP_DIR.$encrypt_text.'.png';
                    $errorCorrectionLevel = 'L';   
                    $matrixPointSize = 3;
                    QRcode::png($encrypt_text, $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
                    //display generated file
                    
				?>
				<?  
				echo '<img src="'.$PNG_TEMP_DIR.basename($filename).'" />'; 				
				?>
                <?
				}
				?>
                </td>
              </tr>
              <?
			$no++;
		  }
	  }
	  ?>
        </tbody>        
        </table><?php */?>
        </div>
	</body>
</html>