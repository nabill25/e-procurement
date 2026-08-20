<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=evaluasi_kualifikasi_sertifikat.xls");

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$this->load->model("PaketRekanan");
$this->load->model("RekananEvaluasiSertifikatLain");
$this->load->model("PaketEvaluasiSertifikatLain");
$this->load->model("PaketRekananKualifikasi");

$paket_rekanan = new PaketRekanan();
$paket_evaluasi_sertifikat = new PaketEvaluasiSertifikatLain();

$reqId = $this->input->get("reqId");

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
        <table border="1" cellspacing="1" cellpadding="2">
            <tr class="judul-kolom">
                <td>No</td>
                <td style="width:50%">Nama Perusahaan</td>
                <td>Perhitungan</td>
                <td>Nilai </td>
            </tr>
                <?php
                for($i=0;$i<count($arrRekanan);$i++)
                {
                    $nilai_final = 0;
                    $arrNilaiFinalPerEvaluasi = array();	
                ?>
                  <tr class="terang">
                        <td valign="top"><?=$i+1?></td>    	
                        <td valign="top">
                        <a onclick="openAdd('main/loadUrl/main/data_rekanan/?reqId=<?=$arrRekananId[$i]?>')" style="text-decoration:none"><?=$arrRekanan[$i]?></a> <br />
                        <table>
                        <?php
                        $prosentase_nilai = 0;
                        for($j=0; $j<count($arrEvalSertifikatId);$j++)
                        {
                        ?>
                            <tr>
                            <td colspan="3"><?=$j+1?>. <?=$arrNama[$j]?>, <?=$arrKeterangan[$j]?> - <strong>Prosentase : <?=$arrNilaiPerEvaluasi[$j]?>%</strong> 
                            <input type="hidden" id="reqProsentase<?=$j?>-<?=$i?>" value="<?=$arrNilaiPerEvaluasi[$j]?>" />                     
                            </td>
                            <td>
                                <?php
                                $rekanan_evaluasi_sertifikat = new RekananEvaluasiSertifikatLain();
                                $rekanan_evaluasi_sertifikat->selectByParams(array("PAKET_EVAL_SERTIFIKAT_LAIN_ID" => $arrEvalSertifikatId[$j], "PAKET_REKANAN_ID" => $arrPaketRekananId[$i]));
                                $rekanan_evaluasi_sertifikat->firstRow();
                                $prosentase_nilai += $rekanan_evaluasi_sertifikat->getField("KESESUAIAN_TOTAL");
                                ?>              
                                <input type="hidden" name="reqPaketEvaluasiSertifikatId[]" id="reqPaketEvaluasiSertifikatId<?=$j?>-<?=$i?>" value="<?=$arrEvalSertifikatId[$j]?>" style="width:50px;" />
                                <input type="hidden" name="reqPaketRekananSertifikatId[]" id="reqPaketRekananSertifikatId<?=$j?>-<?=$i?>" value="<?=$arrPaketRekananId[$i]?>" style="width:50px;" />
                                <?php
                                unset($rekanan_evaluasi_sertifikat);
                                ?>
                            </td>                
                            </tr>
                        <?php
                            $k=0;
                            $total_sertifikat = 0;
                            $rekanan_evaluasi_sertifikat = new RekananEvaluasiSertifikatLain();
                            $rekanan_evaluasi_sertifikat->selectByParams(array("PAKET_EVAL_SERTIFIKAT_LAIN_ID" => $arrEvalSertifikatId[$j], "PAKET_REKANAN_ID" => $arrPaketRekananId[$i]));
                            while($rekanan_evaluasi_sertifikat->nextRow())
                            {
                        ?>	
                            <tr>
                                <td>&raquo; <a onclick="openAdd('main/loadUrl/main/paket_evaluasi_lihat_sertifikat/?reqId=<?=$rekanan_evaluasi_sertifikat->getField("REKANAN_SERTIFIKAT_ID")?>')" style="text-decoration:none"><?=$rekanan_evaluasi_sertifikat->getField("SERTIFIKAT")?></a></td>
                                <td style="width:3%">&nbsp;</td>
                            </tr>
                        <?php	
                                $total_sertifikat += 1;
                                $k++;
                                $nilai_final = $rekanan_evaluasi_sertifikat->getField("NILAI");
                            }
                        ?>
                            <tr>
                                <td colspan="3"><input type="hidden" id="reqPemenuhan<?=$j?>-<?=$i?>" value="<?=$total_sertifikat?>" /></td>
                            </tr>
                        <?php
                            unset($rekanan_evaluasi_sertifikat);
                        }
                        
                        ?>
                        </table>
                        </td>
                        <td valign="top"><?=$arrNilai[0]?> * <label style="font-size:13px;" id="reqNilaiProsentase<?=$i?>"><?=$prosentase_nilai?></label>%</td>    	
                        <td valign="top" align="center">
                            <input type="hidden" name="reqPaketRekanan[]" value="<?=$arrPaketRekananId[$i]?>" />
                         	<label><?=str_replace('.', ',', $nilai_final)?></label>
                        </td>     
                  </tr>
                <?php
                    unset($arrNilaiFinalPerEvaluasi);
                }
                ?>    
            </table>
        </div>
	</body>
</html>