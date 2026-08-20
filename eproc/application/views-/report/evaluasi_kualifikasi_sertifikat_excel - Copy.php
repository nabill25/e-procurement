<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->model("PaketRekanan");
$this->load->model("RekananEvaluasiSertifikatLain");
$this->load->model("PaketEvaluasiSertifikatLain");

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("lib/phpqrcode/qrlib.php");

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=evaluasi_kualifikasi_sertifikat.xls");

$paket_rekanan = new PaketRekanan();
$paket_evaluasi_sertifikat = new PaketEvaluasiSertifikatLain();

set_time_limit(300);
ini_set("memory_limit","500M");
ini_set('max_execution_time', 520);

$reqId = $this->input->get("reqId");
$reqPaketRekanan = $_POST["reqPaketRekanan"];
$reqNilaiFinal = $_POST["reqNilaiFinal"];

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
                    <th width="27">No</th>
                    <th width="115">Nama Perusahaan</th>
                    <th align="center" width="235">Maks * (%) Sertifikat</th>
                    <th width="40">Nilai</th>
                </tr>
             </thead>
             <tbody>
			 <?
               $paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 ");
				while($paket_rekanan->nextRow())
				{
					$arrRekanan[] = $paket_rekanan->getField("REKANAN");
					$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
				}
				
				$paket_evaluasi_sertifikat->selectByParams(array("PAKET_ID" => $reqId));
				while($paket_evaluasi_sertifikat->nextRow())
				{
					$arrEvalSertifikatId[] = $paket_evaluasi_sertifikat->getField("PAKET_EVAL_SERTIFIKAT_LAIN_ID");
					$arrNama[] = $paket_evaluasi_sertifikat->getField("NAMA");
					$arrKeterangan[] = $paket_evaluasi_sertifikat->getField("KETERANGAN");	
					$arrNilaiPerEvaluasi[] = $paket_evaluasi_sertifikat->getField("NILAI");	
					$arrNilai[] = $paket_evaluasi_sertifikat->getField("NILAI_MINIMUM");	
				}
				
					$z=0;
					$minus = 0;
					for($i=0;$i<count($arrRekanan);$i++)
					{ 
						//if($z<>1){
						//$z +=1;
						//}
									
						$z +=1;
						$arrNilaiFinalPerEvaluasi = array();
						$nilai_final = 0;
             ?>
             	  <tr>
                    <td><?=$i+1?></td>
                    <td><?=$arrRekanan[$i]?></td>
                    <td width="235"></td>
                    <td></td>
                  </tr>
			<?
	           		$prosentase_nilai = 0;
					for($j=0; $j<count($arrEvalSertifikatId);$j++)
					{
						$minus +=1;
						$z +=1;
            ?>
            	 <tr>
                    <td></td>
                    <td><?= ($j+1)." ".$arrNama[$j].", ".$arrKeterangan[$j]." (Prosentase : ".$arrNilaiPerEvaluasi[$j]."%)"?></td>
                    <td width="235"></td>
                    <td></td>
                  </tr>
            <?
				$total_sertifikat = 0;
				$rekanan_evaluasi_sertifikat = new RekananEvaluasiSertifikatLain();
				$rekanan_evaluasi_sertifikat->selectByParams(array("PAKET_EVAL_SERTIFIKAT_LAIN_ID" => $arrEvalSertifikatId[$j], "PAKET_REKANAN_ID" => $arrPaketRekananId[$i]));
				$ada_data = false;
				while($rekanan_evaluasi_sertifikat->nextRow())
				{			
			
					if($total_sertifikat == 0)
						$prosentase_nilai += $rekanan_evaluasi_sertifikat->getField("KESESUAIAN_TOTAL");
						
					$ada_data = true;

					$minus +=1;
					$z +=1;
			?>
                      <tr>
                        <td></td>
                        <td width="300"><?= ">> ".$rekanan_evaluasi_sertifikat->getField("SERTIFIKAT")?></td>
                        <td width="235"></td>
                        <td></td>
                      </tr>
              <?
				  $nilai_final = $rekanan_evaluasi_sertifikat->getField("NILAI");
				  $total_sertifikat += 1;
					}
					
					if($ada_data == false)
					{
						$minus +=1;
						$z +=1;
			  
			  ?>
              		<tr>
                        <td></td>
                        <td></td>
                        <td width="235"></td>
                        <td></td>
                      </tr>
                <?
					}
					
							unset($rekanan_evaluasi_sertifikat);
							
							if($total_sertifikat > 0)
							{
								$arrNilaiFinalPerEvaluasi[] = $arrNilaiPerEvaluasi[$j];
							}
							else
							{
								$arrNilaiFinalPerEvaluasi[] = 0;				
							}		
						
					}
					if((int)$nilai_final == 0)
							$nilai_final = ($arrNilai[0] * array_sum($arrNilaiFinalPerEvaluasi)) / 100;
					?>
                    		<?php /*?><tr>
                                <td><?=$arrNilai[0]." * ".$prosentase_nilai."%"?></td>
                                <td><?=$nilai_final?></td>
                              </tr><?php */?>
                    <?
					unset($arrNilaiFinalPerEvaluasi);
			
					$minus = 0;
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