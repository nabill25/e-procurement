<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=evaluasi_kualifikasi_peralatan.xls");

$this->load->model("PaketRekanan");
$this->load->model("RekananEvaluasiPeralatan");
$this->load->model("PaketEvaluasiPeralatanDetil");
$this->load->model("PaketEvaluasiPeralatan");
$this->load->model("PaketRekananKualifikasi");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/recordcoloring.func.php");

$paket_rekanan = new PaketRekanan();
$paket_evaluasi_peralatan = new PaketEvaluasiPeralatan();
$paket_evaluasi_peralatan_detil = new PaketEvaluasiPeralatanDetil();

$reqId = $this->input->get("reqId");

$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 ");
while($paket_rekanan->nextRow())
{
	$arrRekanan[] = $paket_rekanan->getField("REKANAN");
	$arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
	$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
}

$paket_evaluasi_peralatan->selectByParams(array("PAKET_ID" => $reqId));
$paket_evaluasi_peralatan->firstRow();
$msb = $paket_evaluasi_peralatan->getField("MSB"); 
$spjb = $paket_evaluasi_peralatan->getField("SPJB"); 
$spdb = $paket_evaluasi_peralatan->getField("SPDB"); 
$nilai = $paket_evaluasi_peralatan->getField("NILAI_MINIMUM"); 

$paket_evaluasi_peralatan_detil->selectByParams(array("PAKET_ID" => $reqId));
while($paket_evaluasi_peralatan_detil->nextRow())
{
	$arrEvalPeralatanId[] = $paket_evaluasi_peralatan_detil->getField("PAKET_EVAL_PERALATAN_DETIL_ID");
	$arrNama[] = $paket_evaluasi_peralatan_detil->getField("NAMA");
	$arrKeterangan[] = $paket_evaluasi_peralatan_detil->getField("KETERANGAN");	
	$arrNilai[] = $paket_evaluasi_peralatan_detil->getField("NILAI");		
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
                    $arrNilaiFinalPerEvaluasi = array();
                    $nilai_final = 0;
                ?>
                  <tr class="terang">
                        <td valign="top"><?=$i+1?></td>    	
                        <td valign="top">
                        <a onclick="openAdd('main/loadUrl/main/data_rekanan/?reqId=<?=$arrRekananId[$i]?>')" style="text-decoration:none"><?=$arrRekanan[$i]?></a> <br />
                        <table>
                        <?php 
                        $prosentase_nilai = 0;
                        for($j=0; $j<count($arrEvalPeralatanId);$j++)
                        {
                        ?>
                            <tr>
                            <td style="width:90%">
                                <?=$j+1?>. <?=$arrNama[$j]?>, <?=$arrKeterangan[$j]?> - <strong>Prosentase : <?=$arrNilai[$j]?>%</strong>
                                <input type="hidden" id="reqProsentase<?=$j?>-<?=$i?>" value="<?=$arrNilai[$j]?>" />     
                            </td> 
                            <td>
                                <?php 
                                $rekanan_evaluasi_peralatan = new RekananEvaluasiPeralatan();
                                $rekanan_evaluasi_peralatan->selectByParams(array("PAKET_EVAL_PERALATAN_DETIL_ID" => $arrEvalPeralatanId[$j], "PAKET_REKANAN_ID" => $arrPaketRekananId[$i]));
                                $rekanan_evaluasi_peralatan->firstRow();
                                $prosentase_nilai += $rekanan_evaluasi_peralatan->getField("KESESUAIAN_TOTAL");
                                ?>
                                <?php 
                                unset($rekanan_evaluasi_peralatan);
                                ?>
                            </td>         
                            </tr>
                        <?php 
                            $k = 0;
                            $total_peralatan = 0;
                            $rekanan_evaluasi_peralatan = new RekananEvaluasiPeralatan();
                            $rekanan_evaluasi_peralatan->selectByParams(array("PAKET_EVAL_PERALATAN_DETIL_ID" => $arrEvalPeralatanId[$j], "PAKET_REKANAN_ID" => $arrPaketRekananId[$i]));
                            while($rekanan_evaluasi_peralatan->nextRow())
                            {
                                switch($rekanan_evaluasi_peralatan->getField("BUKTI_KEPEMILIKAN"))
                                {
                                    case "Milik Sendiri":
                                        $prosentase_kepemilikan = $msb;
                                        break;
                                    case "Sewa Jangka Panjang":
                                        $prosentase_kepemilikan = $spjb;
                                        break;
                                    case "Sewa Jangka Pendek":
                                        $prosentase_kepemilikan = $spdb;
                                        break;
                                }					
                        ?>	
                            <tr>
                                <td style="width:63%">
                                    &raquo; <a onclick="openAdd('app/loadUrl/main/paket_evaluasi_lihat_peralatan/?reqId=<?=$rekanan_evaluasi_peralatan->getField("REKANAN_PERALATAN_ID")?>')" style="text-decoration:none"><?=$rekanan_evaluasi_peralatan->getField("PERALATAN")?></a>
                                     - <?=$rekanan_evaluasi_peralatan->getField("BUKTI_KEPEMILIKAN")?> 
                                </td>
                            </tr>
                        <?php 		
                                $nilai_final = $rekanan_evaluasi_peralatan->getField("NILAI");
                                $total_peralatan += 1;
                                $k++;
                            }
                            unset($rekanan_evaluasi_peralatan);
                            ?>
                        <?php 
                        }
                        ?>            
                        </table>
                        </td>
                        <td valign="top"><?=$nilai?> * <label style="font-size:13px;" id="reqNilaiProsentase<?=$i?>"><?=$prosentase_nilai?></label>%</td>			
                        <td valign="top" align="center">
                            <input type="hidden" name="reqPaketRekanan[]" value="<?=$arrPaketRekananId[$i]?>" />
                            <label><?=str_replace('.', ',', $nilai_final)?></label>
                        </td>   
                  </tr>
                <?php 
                    unset($arrNilaiFinalPerEvaluasi);
                    unset($arrNilaiProsentase);
                }
                ?>    
            </table>
        
        </div>
	</body>
</html>