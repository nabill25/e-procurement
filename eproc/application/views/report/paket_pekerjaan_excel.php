<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->model("Paket");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("lib/phpqrcode/qrlib.php");

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=paket_pekerjaan.xls");

$paket = new Paket();
/* VARIABLES */
$reqTahunPajak= $this->input->get("reqTahunPajak");

$field = array('NAMA', 'PAKET_JENIS','LOKASI','METODE_LELANG');

if((int)$this->USER_TYPE_ID == 3)
	$statement_status = array();	
else
	$statement_status = array("PUBLISH_PAKET" => 1);
		
$allrecord = $paket->getCountByParams($statement_status," AND to_char(TANGGAL_TAHAP, 'YYYY') = '".$reqTahunPajak."'");
$paket->selectByParamsMonitoringCetak($statement_status, -1, -1,'',$reqTahunPajak);
//echo $paket->query;exit;
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
        	DAFTAR PAKET PEKERJAAN <?= SYSTEM_NAME_PT ?>
        </div>
        <div class="isi" align="center">
        	TAHUN <?=$reqTahunPajak?>
        
        </div>
        <br>
        <div class="data-laporan">
        <table border="1">
    	    <thead>
	    	    <tr>
                        <th align="center">No</th>
                        <th align="center">Nama Paket</th>
                        <th align="center">Bidang Pekerjaan</th>
                        <th align="center">Lokasi Pekerjaan</th>
                        <th align="center">Metode Pengadaan</th>
                        <th align="center">Tanggal Selesai Proses </th>
                        <th align="center">Perusahaan Pemenang</th>
                        <th align="center">Nilai Pekerjaan (OE)</th>
                        <th align="center">Nilai Sesudah Negosiasi</th>
                    	<th align="center">Persentase Thd OE</th>
                </tr>
             </thead>
             <tbody>
			 <?php
			 	$start = 6; $number = 1;
                $temp = $start-1;
				while($paket->nextRow())
				{
             ?>
             	<tr>
                	<td align="center"><?=$number?></td>
                    <td align="center"><?=$paket->getField($field[0])?></td>
                    <td align="center"><?=$paket->getField($field[1])?></td>
                    <td align="center"><?=$paket->getField($field[2])?></td>
                    <td align="center"><?=$paket->getField($field[3])?></td>
                    <td align="center"><?=$paket->getField($field[4])?></td>
                    <td align="center"><?=$paket->getField($field[5])?></td>
                    <td align="center"><?=$paket->getField($field[6])?></td>
                    <td align="center"><?=$paket->getField($field[7])?></td>
                    <td align="center"><?=$paket->getField($field[8])?></td>
                </tr>
			<?php
	           $number++;
				$temp++;
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
               <?php
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
                <?php
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
				<?php
				echo '<img src="'.$PNG_TEMP_DIR.basename($filename).'" />'; 				
				?>
                <?php
				}
				?>
                </td>
              </tr>
              <?php
			$no++;
		  }
	  }
	  ?>
        </tbody>        
        </table><?php */?>
        </div>
	</body>
</html>