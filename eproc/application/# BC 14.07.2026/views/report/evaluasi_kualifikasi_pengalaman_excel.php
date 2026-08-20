<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->model("PaketRekanan");
$this->load->model("RekananEvaluasiPengalaman");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("lib/phpqrcode/qrlib.php");

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=evaluasi_kualifikasi_pengalaman.xls");

$paket_rekanan = new PaketRekanan();
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
                    <th align="center" width="235">Bidang    Usaha</th>
                    <th width="40">&nbsp;</th>
                    <th align="center" width="99">Nilai    Kontrak</th>
                    <th width="40">&nbsp;</td>
                    <th align="center" width="89">Status    Penyedia Jasa</th>
                    <th width="40">&nbsp;</th>
                    <th width="54">Nilai</th>
                    <th width="64">Nilai Final</th>
                </tr>
                <tr>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Keterangan</th>
                    <th width="40">Poin</th>
                    <th>Keterangan</th>
                    <th width="40">Poin</th>
                    <th>Keterangan</th>
                    <th width="40">Poin</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
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
				
					$i=2;
					$no = 1;
					for($row=0;$row<count($arrRekanan);$row++)
					{ 
						$rekanan_evaluasi_kd = new RekananEvaluasiPengalaman();
						$rekanan_evaluasi_kd->selectByParamsEvaluasi(array("PAKET_REKANAN_ID" => $arrPaketRekananId[$row]), -1, -1, $reqId);
						$j = 0;
						while($rekanan_evaluasi_kd->nextRow())
						{
							if($j == 0)
							{
								$bidang_usaha = str_replace(" | ", "\n", $rekanan_evaluasi_kd->getField("BIDANG_USAHA"));
								
								if ($rekanan_evaluasi_kd->getField("NILAI") == "") {
									$nilai= 0; 
									}else{ 
									$nilai=$rekanan_evaluasi_kd->getField("NILAI");
									}
             ?>
             	  <tr>
                    <td><?=$no?></td>
                    <td><?=$arrRekanan[$row]?></td>
                    <td width="235"><?=$rekanan_evaluasi_kd->getField("NAMA");?></td>
                    <td><?=$rekanan_evaluasi_kd->getField("BP");?></td>
                    <td><?=currencyToPage($rekanan_evaluasi_kd->getField("KONTRAK_NILAI"));?></td>
                    <td><?=$rekanan_evaluasi_kd->getField("NK");?></td>
                    <td><?=$rekanan_evaluasi_kd->getField("JO");?></td>
                    <td><?=$rekanan_evaluasi_kd->getField("STBU")?></td>
                    <td><?=(str_replace('.', ',', $rekanan_evaluasi_kd->getField("BP")) + str_replace('.', ',', $rekanan_evaluasi_kd->getField("NK")) + str_replace('.', ',', $rekanan_evaluasi_kd->getField("STBU")))?></td>
                    <td><?=str_replace('.', ',', $nilai)?></td>
                  </tr>
			<?php 
           		$i++;
				$no++;
			}
			else
			{
				$bidang_usaha = str_replace(" | ", "\n", $rekanan_evaluasi_kd->getField("BIDANG_USAHA"));
				
				if ($rekanan_evaluasi_kd->getField("NILAI") == "") {
					$nilai=$rekanan_evaluasi_kd->getField("BP") + $rekanan_evaluasi_kd->getField("NK") + $rekanan_evaluasi_kd->getField("STBU"); 
					}else{ 
					$nilai=$rekanan_evaluasi_kd->getField("NILAI");
					}
            ?>
            	 <tr>
                    <td></td>
                    <td></td>
                    <td width="235"><?=$rekanan_evaluasi_kd->getField("NAMA")?></td>
                    <td><?=$rekanan_evaluasi_kd->getField("BP")?></td>
                    <td><?=currencyToPage($rekanan_evaluasi_kd->getField("KONTRAK_NILAI"));?></td>
                    <td><?=$rekanan_evaluasi_kd->getField("NK");?></td>
                    <td><?=$rekanan_evaluasi_kd->getField("JO");?></td>
                    <td><?=$rekanan_evaluasi_kd->getField("STBU")?></td>
                    <td><?=($rekanan_evaluasi_kd->getField("BP") + $rekanan_evaluasi_kd->getField("NK") + $rekanan_evaluasi_kd->getField("STBU"))?></td>
                    <td></td>
                  </tr>
            <?php 
			}
				$j++;
	 		}
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
                    
				echo '<img src="'.$PNG_TEMP_DIR.basename($filename).'" />'; 				
				
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