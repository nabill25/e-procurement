<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->model("PaketRekanan");
$this->load->model("RekananEvaluasiPersonil");
$this->load->model("PaketEvaluasiPersonil");

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("lib/phpqrcode/qrlib.php");

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=evaluasi_kualifikasi_personil.xls");

$paket_rekanan = new PaketRekanan();
$paket_evaluasi_personil = new PaketEvaluasiPersonil();;

set_time_limit(300);
ini_set("memory_limit","500M");
ini_set('max_execution_time', 520);

$reqId = $this->input->get("reqId");
$reqPaketRekanan = $_POST["reqPaketRekanan"];
$reqNilaiFinal = $_POST["reqNilaiFinal"];

$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 ");
while($paket_rekanan->nextRow())
{
	$arrRekanan[] = $paket_rekanan->getField("REKANAN");
	$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
	$arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");
}

$paket_evaluasi_personil->selectByParams(array("PAKET_ID" => $reqId));
while($paket_evaluasi_personil->nextRow())
{
	$arrEvalPersonilId[] = $paket_evaluasi_personil->getField("PAKET_EVAL_PERSONIL_ID");
	$arrJabatan[] = $paket_evaluasi_personil->getField("JABATAN");
	$arrPendidikan[] = $paket_evaluasi_personil->getField("PENDIDIKAN_NAMA");	
	$arrPengalaman[] = $paket_evaluasi_personil->getField("PENGALAMAN");	
	$arrJumlah[] = $paket_evaluasi_personil->getField("JUMLAH");	
	$arrNilaiPerEvaluasi[] = $paket_evaluasi_personil->getField("NILAI");	
	$arrNilai[] = $paket_evaluasi_personil->getField("NILAI_MINIMUM");		
	$arrSKA[] = $paket_evaluasi_personil->getField("SKA");
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
            <?
				for($i=0;$i<count($arrRekanan);$i++)
				{
			?>
            	 <tr>
                    <th width="27">No</th>
                    <th width="208">Uraian</th>
                    <th width="54">Nilai</th>
                    <th align="center" width="148"><?=$arrRekanan[$i]?></th>
                    <th width="72">&nbsp;</th>
                    <th width="61">&nbsp;</th>
                  </tr>
                  <tr>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Keterangan</th>
                    <th>Bobot</th>
                    <th>Nilai Akhir</th>
                  </tr>
             </thead>
             <tbody>
			 <?
               $z=2;
				$jumlah_bobot = 0;
				$jumlah_nilai_akhir = 0;
				$nilai_final_database = 0;
				for($j=0; $j<count($arrEvalPersonilId);$j++)
				{
					
					$status_tenaga_ahli = 0;
					$total_pegawai = 0;
					$rekanan_evaluasi_personil = new RekananEvaluasiPersonil();
					$rekanan_evaluasi_personil->selectByParamsCetak(array("PAKET_EVAL_PERSONIL_ID" => $arrEvalPersonilId[$j], "PAKET_REKANAN_ID" => $arrPaketRekananId[$i]));
					$k = 0;
					while($rekanan_evaluasi_personil->nextRow())
					{
						if($k == 0)
						{
             	?>
                          <tr>
                            <td><?=$j+1?></td>
                            <td><?=$arrJabatan[$j]?></td>
                            <td ><?=$arrNilaiPerEvaluasi[$j]?></td>
                            <td><?=$rekanan_evaluasi_personil->getField("TENAGA_AHLI")?></td>
                            <td><?=$rekanan_evaluasi_personil->getField("KESESUAIAN_NILAI")?></td>
                        	<td><?=$rekanan_evaluasi_personil->getField("KESESUAIAN_TOTAL")?></td>
                           </tr>
				<?
					$row_tenaga_ahli = $z;
					$z++;
            	?>
                   <tr>
                    <td></td>
                    <td><?="Pendidikan - ".$arrPendidikan[$j].", Jumlah : ".$arrJumlah[$j]." orang"?></td>
                    <td></td>
                    <td><?=coalesce($rekanan_evaluasi_personil->getField("JURUSAN"), "-")?></td>
                    <td></td>
                    <td></td>
                   </tr>
				<?
	           		 $z++;
				?>
            		<tr>
                        <td></td>
                        <td><?="Pengalaman - ".$arrPengalaman[$j]." tahun"?></td>
                        <td></td>
                        <td><?="Pengalaman, ".$rekanan_evaluasi_personil->getField("PENGALAMAN")." tahun"?></td>
                        <td></td>
                        <td></td>
                    </tr>
				<?
				if($arrSKA[$j] == 1)
						{
							$z++;
            	?>
            	 <tr>
                    <td></td>
                    <td><?="Sertifikat Keahilian"?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
            	<?
					$z++;			
					}
					$k++;	
					if($status_tenaga_ahli == 0)
						$z = $row_tenaga_ahli;
					 $arrTenagaAhli[] =  $z;
							$z++;
						$arrTenagaAhli[] =  $z;
							$z++;
						if($arrSKA[$j] == 1)
							{
								$z++;
				?>
                	<tr>
                    <td><?="SKA ".$rekanan_evaluasi_personil->getField("SERTIFIKAT")?></td>
                    <td></td>
                  </tr>
                  <?
                  }
					$z++;	
					$nilai_final_database = $rekanan_evaluasi_personil->getField("NILAI");
					$jumlah_bobot		+= $rekanan_evaluasi_personil->getField("KESESUAIAN_TOTAL");
					}
					else
					{
					?>
                     <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><?=$rekanan_evaluasi_personil->getField("TENAGA_AHLI")?></td>
                        <td><?= $rekanan_evaluasi_personil->getField("KESESUAIAN_NILAI")?></td>
                        <td></td>
                      </tr>
                    <?
					   $row_tenaga_ahli = $z;
						$z++;
						  $z++;
							if($arrSKA[$j] == 1)
							{
								$z++;
							  
							  $z++;			
							}				
							if($status_tenaga_ahli == 0)
								$z = $row_tenaga_ahli;
								$arrTenagaAhli[] =  $z;
								$z++;
					  ?>
                      <tr>
                      	<td></td>
                        <td></td>
                        <td></td>
                        <td><?=coalesce($rekanan_evaluasi_personil->getField("JURUSAN"), "-")?></td>
						<td></td>
                        <td></td>                      
                      </tr>
                      <?
                      	$arrTenagaAhli[] =  $z;
						$z++;
					  ?>
                      <tr>
                      	<td></td>
                        <td></td>
                        <td></td>
                        <td><?="Pengalaman, ".coalesce($rekanan_evaluasi_personil->getField("PENGALAMAN"), "-")." tahun"?></td>
                      	<td></td>
                        <td></td>
                      </tr>
                      <?
                      	if($arrSKA[$j] == 1)
						{
						$z++;
					  
					  ?>
                       <tr>
                        <td><?="SKA ".$rekanan_evaluasi_personil->getField("SERTIFIKAT")?></td>
                      </tr>
                      <?
						  }
						
						$z++;
								}
						}
					  ?>
                      <tr>
                        <td></td>
                         <td></td>
                         <td></td>
                         <td></td>
                         <td></td>
                         <td></td>
                      </tr>
                      <?
                      }
						if((int)$nilai_final_database == 0)
							$jumlah_nilai_akhir = $jumlah_nilai_akhir;
						else
							$jumlah_nilai_akhir = $nilai_final_database;
					  
					  ?>
                      <tr>
                        <td></td>
                         <td>"Total Bobot"</td>
                         <td><?= $jumlah_bobot?></td>
                         <td colspan="2">Total Nilai</td>
                         <td><?=$jumlah_nilai_akhir?></td>
                      </tr>
                      <?
			}?>
             </tbody>
        </table>
        </div>
	</body>
</html>