<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->model("PaketEvaluasiAdmin");
$this->load->model("PaketRekanan");
$this->load->model("RekananEvaluasiAdmin");
$this->load->model("Paket");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("lib/phpqrcode/qrlib.php");

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=evaluasi_kualifikasi_administrasi.xls");

$paket_evaluasi_admin = new PaketEvaluasiAdmin();
$paket_rekanan = new PaketRekanan();
$paket_nilai = new Paket();
$paket_nilai_estimate = new Paket();


set_time_limit(300);
ini_set("memory_limit","500M");
ini_set('max_execution_time', 520);

$reqId = $this->input->get("reqId");

//$paketInfo->getPaket($reqId);
//$reqNamaPaket = $paketInfo->nama;

$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 ");

$paket_evaluasi_admin->selectByParamsProses(array("PAKET_ID" => $reqId));

$i = 0;
while($paket_evaluasi_admin->nextRow())
{
	$arrNamaEvaluasiAdmin[$i] = $paket_evaluasi_admin->getField("NAMA");
	$arrIdEvaluasiAdmin[$i] = $paket_evaluasi_admin->getField("EVALUASI_NUMBER"); 
	$i++;
}
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
                    <?php 
					for($z=0; $z<count($arrNamaEvaluasiAdmin);$z++)
					{
					?>
                    	<th align="center"><?=$arrNamaEvaluasiAdmin[$z];?></th>
                    <?php 	
					}
					?>
                </tr>
             </thead>
             <tbody>
			 <?php 
                $x=1;
                $no=1;
                $style="gelap";						
                while($paket_rekanan->nextRow())
                {
             ?>
             	<tr>
                	<td align="center">a</td>
                    <td align="center"><?=$paket_rekanan->getField("REKANAN")?></td>
                    <?php 
                    for($i=0; $i<count($arrIdEvaluasiAdmin);$i++)
					{
						$rekanan_evaluasi_admin = new RekananEvaluasiAdmin();
						$rekanan_evaluasi_admin->selectByParams(array("EVALUASI_NUMBER" => $arrIdEvaluasiAdmin[$i], "PAKET_REKANAN_ID" => $paket_rekanan->getField("PAKET_REKANAN_ID")));
						$rekanan_evaluasi_admin->firstRow();
						
						$uraian = str_replace("<br>", "--", $rekanan_evaluasi_admin->getField("URAIAN"));
						$uraian = str_replace("--", "\n", $uraian);
						$uraian = str_replace("<br />", "\n", $uraian);		
						
					?>
                    	<td align="center"><?=$uraian;?></td>
					<?php 
					   unset($rekanan_evaluasi_admin);
					}
					$x++;	
					$no++;
					?>
                </tr>
			<?php 
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