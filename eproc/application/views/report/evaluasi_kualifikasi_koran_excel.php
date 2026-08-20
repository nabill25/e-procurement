<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->model("PaketRekanan");
$this->load->model("RekananEvaluasiKeuangan");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("lib/phpqrcode/qrlib.php");

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=evaluasi_kualifikasi_koran.xls");

$paket_rekanan = new PaketRekanan();
set_time_limit(300);
ini_set("memory_limit","500M");
ini_set('max_execution_time', 520);

$reqId = $this->input->get("reqId");

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
        <div class="isi">
        </div>
        <br>
        <div class="data-laporan">
        <table border="1">
    	    <thead>
	    	    <tr>
                        <th align="center">No</th>
                        <th align="center">Nama Perusahaan</th>
                        <th align="center">Rekening 3 Bulan Terakhir</th>
                        <th align="center">Nilai</th>
                </tr>
             </thead>
             <tbody>
			 <?php 
       $paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 ");
				while($paket_rekanan->nextRow())
				{
					$arrRekanan[] = $paket_rekanan->getField("REKANAN");
					$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
					$arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");
				}
				
					$i=1;
					for($row=0;$row<count($arrRekanan);$row++)
					{ 
						$rekanan_evaluasi_skk = new RekananEvaluasiKeuangan();
						$rekanan_evaluasi_skk->selectByParams(array("PAKET_REKANAN_ID" => $arrPaketRekananId[$row]));
						$rekanan_evaluasi_skk->firstRow();
						
						if ($rekanan_evaluasi_skk->getField("LULUS_REKENING_KORAN")==1){
							$nilai='Memenuhi';
							}else{
								$nilai='Tidak Memenuhi';
								}
						$rekening_koran = str_replace('&gt;', '>', str_replace('&lt;', '<', $rekanan_evaluasi_skk->getField("REKENING_KORAN")));
						$rekening_koran = str_replace("<br>", "\n", $rekening_koran);
             ?>
             	<tr>
                	<td align="center"><?=$i?></td>
                    <td align="center"><?=$arrRekanan[$row]?></td>
                    <td align="center"><?=$rekening_koran?></td>
                    <td align="center"><?=$nilai?></td>
                </tr>
			<?php 
     		 $i++;
					unset($rekanan_evaluasi_skk);
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