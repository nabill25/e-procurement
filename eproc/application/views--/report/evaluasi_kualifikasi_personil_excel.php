<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=evaluasi_kualifikasi_personil.xls");

$this->load->model("PaketRekanan");
$this->load->model("RekananEvaluasiPersonil");
$this->load->model("PaketEvaluasiPersonil");
$this->load->model("PaketRekananKualifikasi");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket_rekanan = new PaketRekanan();
$paket_evaluasi_personil = new PaketEvaluasiPersonil();

$reqId = $this->input->get("reqId");

$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 ");
while($paket_rekanan->nextRow())
{
	$arrRekanan[] = $paket_rekanan->getField("REKANAN");
	$arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
	$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
	$arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");
}
//echo $paket_rekanan->query;
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
}

//echo $paket_evaluasi_personil->query;
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
                        <td style="width:80%">Nama Perusahaan</td>
                        <td>Perhitungan</td>    
                        <td>Nilai </td>
                        </tr>
                        <?php
                        for($i=0;$i<count($arrRekanan);$i++)
                        {
                            $arrNilaiFinalPerEvaluasi = array();	
                        ?>
                          <tr class="terang">
                                <td valign="top"><?=$i+1?></td>    	
                                <td valign="top">
                                <a onclick="openAdd('main/loadUrl/main/data_rekanan/?reqId=<?=$arrRekananId[$i]?>')" style="text-decoration:none"><?=$arrRekanan[$i]?></a> <br />
                                <table>
                                <?php
                                $prosentase_nilai = 0;
                                for($j=0; $j<count($arrEvalPersonilId);$j++)
                                {							
                                    $nilai_final = 0;
                                ?>
                                    <tr>
                                    <td colspan="2">
                                        <?=$j+1?>. <?=$arrJabatan[$j]?>, <?=$arrPendidikan[$j]?> / <?=$arrPengalaman[$j]?> th (Jumlah : <?=$arrJumlah[$j]?> orang) - <strong>Prosentase : <?=$arrNilaiPerEvaluasi[$j]?> %</strong>
                                    </td>
                                    <td>
                                        <?php
                                        $rekanan_evaluasi_personil = new RekananEvaluasiPersonil();
                                        $rekanan_evaluasi_personil->selectByParams(array("PAKET_EVAL_PERSONIL_ID" => $arrEvalPersonilId[$j], "PAKET_REKANAN_ID" => $arrPaketRekananId[$i]));
                                        $rekanan_evaluasi_personil->firstRow();
                                        $prosentase_nilai += $rekanan_evaluasi_personil->getField("KESESUAIAN_TOTAL");
                                        ?><?php
                                        unset($rekanan_evaluasi_personil);
                                        ?>
                                     </td>                
                                    </tr>
                                <?php
                                    $k = 0;
                                    $total_pegawai = 0;
                                    $rekanan_evaluasi_personil = new RekananEvaluasiPersonil();
                                    $rekanan_evaluasi_personil->selectByParams(array("PAKET_EVAL_PERSONIL_ID" => $arrEvalPersonilId[$j], "PAKET_REKANAN_ID" => $arrPaketRekananId[$i]));
                                    while($rekanan_evaluasi_personil->nextRow())
                                    {
                                        //echo $rekanan_evaluasi_personil->query;
                                    ?>	
                                        <tr>
                                            <td>
                                                &raquo; 
                                                <a onclick="openAdd('main/loadUrl/main/paket_evaluasi_lihat_personil/?reqId=<?=$rekanan_evaluasi_personil->getField("REKANAN_TENAGA_AHLI_ID")?>')" style="text-decoration:none"><?=$rekanan_evaluasi_personil->getField("TENAGA_AHLI")?></a>
                                            </td>
                                            <td></td>
                                        </tr>
                                    <?php	
                                        $nilai_final = $rekanan_evaluasi_personil->getField("NILAI");
                                        $total_pegawai += 1;
                                        $k++;
                                    }
                                    unset($rekanan_evaluasi_personil);
                                    ?>
                                    <?php
                                }
                                ?>
                                </table>            	
                                </td>
                                <td valign="top"><?=$arrNilai[0]?> * <label style="font-size:13px;" id="reqNilaiProsentase<?=$i?>"><?=$prosentase_nilai?></label>%</td>    	
                                <td valign="top" align="center">
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