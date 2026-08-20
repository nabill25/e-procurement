<?
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
if($this->USER_TYPE_ID == "")
    redirect("app");
    
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketRekanan");
$this->load->model("RekananEvaluasiTeknisTawar");
$this->load->model("RekananEvaluasiAdminTawar");
$this->load->model("RekananEvaluasiHargaTawar");
$this->load->model("MatrixEvaluasi");
$this->load->model("PaketPenawaran");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("lib/phpqrcode/qrlib.php");

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=evaluasi_penawaran_rekapitulasi.xls");

$paket_rekanan = new PaketRekanan();
$paket_rekanan_nilai = new PaketRekanan();
$matrix_evaluasi = new MatrixEvaluasi();
$paket_penawaran = new PaketPenawaran();

$reqId = $this->input->get("reqId");
$submitSimpan = $this->input->post("submitSimpan");
$reqEvaluasiPenilaian = $_POST["reqEvaluasiPenilaian"];
$reqPaketRekananId = $_POST["reqPaketRekananId"];
$reqPaketRekananUrutArray =unserialize(stripslashes($_POST['reqPaketRekananUrutArray']));

$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;
$reqJenisPekerjaan = $paketInfo->jenis;
$reqJenisPekerjaanId = $paketInfo->jenis_id;
$reqMetodeEvaluasi = $paketInfo->metode_evaluasi;
$reqMetodeEvaluasiId = $paketInfo->metode_evaluasi_id;
$reqOwnerEstimate  = $paketInfo->nilai_owner_estimate;
$reqRekananIdPemenang = $paketInfo->rekanan_id_pemenang;
$reqMetodeLelang = $paketInfo->metode_lelang_id;
$reqJenisPegadaaan = $paketInfo->jenis_pengadaan; 
$reqBidding = $paketInfo->bidding;

$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 ");
while($paket_rekanan->nextRow())
{

  // ambil nilai koreksi
  $paket_penawaran->selectByParamsRekananPaketPenawaran(array('B.PAKET_REKANAN_ID' => $paket_rekanan->getField("PAKET_REKANAN_ID")), -1, -1, " AND 1=1");
  $paket_penawaran->firstRow(); 

	$arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
	$arrRekanan[] = $paket_rekanan->getField("REKANAN");
	$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
	// $arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");
  $arrPaketRekananNilaiSebelumnya[] = $paket_rekanan->getField("UNIT_PRICE");
  $arrPaketRekananNilai[] = $paket_penawaran->getField("JUMLAH_KOREKSI");
	$arrPaketRekananLulus[] = $paket_rekanan->getField("LULUS_PENAWARAN");
}
$i = 0;

$paket_rekanan_nilai->selectNilaiPenawaran2(array("PAKET_ID" => $reqId));
while($paket_rekanan_nilai->nextRow())
{
	$arrUrutan[] = $paket_rekanan_nilai->getField("PAKET_REKANAN_ID");	
}

function getUrutan($reqPaketRekananId, $arrUrutan)
{
	$key = array_search($reqPaketRekananId, $arrUrutan);
	return $key + 1;	
}

$matrix_evaluasi->selectByParams(array("A.PAKET_JENIS_ID" => $reqJenisPekerjaanId, "A.PAKET_METODE_EVALUASI_ID" => $reqMetodeEvaluasiId));
$matrix_evaluasi->firstRow();
?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<base href="<?=base_url()?>" />
	</head>
	<body>
    <table class="table table-bordered table-hover">
                <tr>
                  <!-- <td>Pekerjaan :</td> -->
                  <td colspan="<?=3+count($arrRekanan)?>">Pekerjaan:  <?=$reqNamaPaket?> </td>
                </tr> 
                <tr>
                  <!-- <td>Jenis Pekerjaan :</td> -->
                  <td colspan="<?=3+count($arrRekanan)?>">Jenis Pekerjaan:  <?=$reqJenisPekerjaan?> </td>
                </tr> 
                <tr>
                  <!-- <td>Metode Evaluasi :</td> -->
                  <td colspan="<?=3+count($arrRekanan)?>">Metode Evaluasi:  <?=$reqMetodeEvaluasi?> </td>
                </tr> 
              </table>   

              <table class="table table-bordered table-hover"> 
                <tr class="judul-kolom">
                  <th align="center" valign="middle" width="2%">No.</th>
                  <th colspan="2" align="center" valign="middle">Uraian</th>
                  <?
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                  ?>
                  <th style="text-align: center;"><?=$arrRekanan[$i]?></th>
                  <?
                  }
                  ?>
                </tr>
                <tr>
                  <td valign="top"><strong>I</strong></td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"><strong> EVALUASI DATA ADMINISTRASI </strong></td>
                  <?
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                      $rekanan_evaluasi_admin = new RekananEvaluasiAdminTawar();
                      $rekanan_evaluasi_admin->selectMemenuhiSyarat($reqId, $arrPaketRekananId[$i]);
                      $rekanan_evaluasi_admin->firstRow();
                      // if($rekanan_evaluasi_admin->getField("JUMLAH_EVALUASI") <= $rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI")) // menghitung antara jumlah yang dievaluasi sama jumlah yang sudah dilengkapi
                      if($rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI") >= 1)
                      {
                        $status_admin = "MEMENUHI SYARAT";
                        $arrEvaluasiAdmin[$i] = 1;
                      }
                      else
                      {
                        $status_admin = "TIDAK MEMENUHI SYARAT";                
                        $arrEvaluasiAdmin[$i] = 0;
                      }
                  ?>
                      <td align="center"><strong><?=$status_admin?></strong></td>
                  <?
                      unset($rekanan_evaluasi_admin);
                  }
                  ?>
                </tr>
                <tr>
                  <td valign="top"><strong>II</strong></td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"><strong> EVALUASI DATA TEKNIS </strong></td>
                  <?
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                      $rekanan_evaluasi_teknis = new RekananEvaluasiTeknisTawar();
                      $rekanan_evaluasi_teknis->selectMemenuhiSyarat($reqId, $arrPaketRekananId[$i]);
                      $rekanan_evaluasi_teknis->firstRow();
                      // if($rekanan_evaluasi_teknis->getField("JUMLAH_EVALUASI") <= $rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI"))
                      if($rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI") >= 1)
                      {
                        $status_teknis = "MEMENUHI SYARAT";
                        $arrEvaluasiTeknis[$i] = 1;
                      }
                      else
                      {
                        $status_teknis = "TIDAK MEMENUHI SYARAT";               
                        $arrEvaluasiTeknis[$i] = 0;
                      } 
                  ?>
                      <td align="center"><strong><?=$status_teknis?></strong></td>
                  <?
                      unset($rekanan_evaluasi_teknis);
                  }
                  ?>
                </tr>
                <tr>
                  <td valign="top"><strong>III</strong></td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"><strong> EVALUASI DATA HARGA PENAWARAN </strong></td>
                  <?
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                      $rekanan_evaluasi_harga = new RekananEvaluasiHargaTawar();
                      $rekanan_evaluasi_harga->selectMemenuhiSyarat2($reqId, $arrPaketRekananId[$i]);
                      $rekanan_evaluasi_harga->firstRow();
                      // if($rekanan_evaluasi_harga->getField("JUMLAH_EVALUASI") <= $rekanan_evaluasi_harga->getField("JUMLAH_DILENGKAPI"))
                      if($rekanan_evaluasi_harga->getField("JUMLAH_DILENGKAPI") >= 1)
                      {
                        $status_harga = "MEMENUHI SYARAT";
                        $arrEvaluasiHarga[$i] = 1;
                      }
                      else
                      {
                        $status_harga = "TIDAK MEMENUHI SYARAT";               
                        $arrEvaluasiHarga[$i] = 0;
                      } 
                  ?>
                      <td align="center"><strong><?=$status_harga?></strong></td>
                  <?
                      unset($rekanan_evaluasi_harga);
                  }
                  ?>
                  </tr>
                <tr>
                  <td valign="top">&nbsp;</td>
                  <td valign="top">'1. </td>
                  <td valign="top"> EVALUASI PENDAHULUAN </td>
                  <?
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                  ?>
                      <td valign="top">&nbsp;</td>
                  <?
                  }
                  ?>
                </tr>
                <tr>
                  <td valign="top">&nbsp;</td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"> a. Koreksi Item, Satuan dan Volume </td>
                  <?
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                      $rekanan_evaluasi_teknis = new RekananEvaluasiTeknisTawar();
                      $rekanan_evaluasi_teknis->selectMemenuhiSyarat($reqId, $arrPaketRekananId[$i]);
                      $rekanan_evaluasi_teknis->firstRow();
                      // if($rekanan_evaluasi_teknis->getField("JUMLAH_EVALUASI") <= $rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI"))
                      if($rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI") >= 1)
                        $status_teknis = "MEMENUHI SYARAT";
                      else
                        $status_teknis = "TIDAK MEMENUHI SYARAT";               
                  ?>
                      <td align="center"><strong><?=$status_teknis?></strong></td>
                  <?
                      unset($rekanan_evaluasi_admin);
                  }
                  ?>
                </tr>
                <tr>
                  <td valign="top">&nbsp;</td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"> b. Koreksi Aritmatik </td>
                  <?
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                      $rekanan_evaluasi_teknis = new RekananEvaluasiTeknisTawar();
                      $rekanan_evaluasi_teknis->selectMemenuhiSyarat($reqId, $arrPaketRekananId[$i]);
                      $rekanan_evaluasi_teknis->firstRow();
                      // if($rekanan_evaluasi_teknis->getField("JUMLAH_EVALUASI") <= $rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI"))
                      if($rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI") >= 1)
                        $status_teknis = "MEMENUHI SYARAT";
                      else
                        $status_teknis = "TIDAK MEMENUHI SYARAT";               
                  ?>
                      <td align="center"><strong><?=$status_teknis?></strong></td>
                  <?
                      unset($rekanan_evaluasi_admin);
                  }
                  ?>
                </tr>
                <tr>
                  <td valign="top">&nbsp;</td>
                  <td valign="top">'2.</td>
                  <td valign="top"> EVALUASI KEWAJARAN HARGA </td>
                  <?
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                  ?>
                      <td valign="top">&nbsp;</td>
                  <?
                  }
                  ?>
                </tr>
                <tr>
                  <td valign="top">&nbsp;</td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"> a. Penawaran </td>
                  <?
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                  ?>
                      <td align="center"><strong><?=$paketInfo->mata_uang?> <?=numberToIna($arrPaketRekananNilaiSebelumnya[$i])?></strong></td>
                  <?
                  }
                  ?>
                </tr>
                <tr>
                  <td valign="top">&nbsp;</td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"> b. Penawaran Terkoreksi </td>
                  <?
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                  ?>
                      <td align="center"><strong><?=$paketInfo->mata_uang?> <?=numberToIna($arrPaketRekananNilai[$i])?></strong></td>
                      <!-- <td align="center"><strong><?=$paketInfo->mata_uang?> <?php //numberToIna($paket_penawaran->getField("JUMLAH_KOREKSI"))?></strong></td> -->
                  <?
                  }
                  ?>
                </tr>
                <tr>
                  <td valign="top">&nbsp;</td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"> c. Owner Estimate </td>
                  <td valign="top" colspan="<?=count($arrRekanan)?>" align="center"> <strong><?=$paketInfo->mata_uang?> <?=numberToIna($reqOwnerEstimate)?></strong> </td>
                </tr>
                <tr>
                  <td valign="top">&nbsp;</td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"> d. Persentase Penawaran Terhadap OE </td>
                  <?
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                      if((int)$reqOwnerEstimate == 0)
                        $prosentase_terhadap_oe = 0;
                      else
                        $prosentase_terhadap_oe = round(((int)$arrPaketRekananNilaiSebelumnya[$i] / (int)$reqOwnerEstimate) * 100,2);
                      
                  ?>
                      <td align="center"><strong><?=$prosentase_terhadap_oe?>%</strong></td>
                  <?
                  }
                  ?>
                </tr>
                <!-- <tr>
                  <td valign="top">&nbsp;</td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"> e. Persentase Kesalahan Penawaran </td> -->
                  <?
                  // for($i=0;$i<count($arrRekanan);$i++)
                  // {
                  ?>
                  <!-- <td valign="top" align="center"> 0,00% </td> -->
                  <?
                  // }
                  ?>
                <!-- </tr> -->
                <tr>
                  <td valign="top">&nbsp;</td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"> e. Persentase penawaran terkoreksi thd OE </td>
                  <?
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                      if((int)$reqOwnerEstimate == 0)
                        $presentase = 0;
                      else 
                        $presentase = round(($arrPaketRekananNilai[$i] / $reqOwnerEstimate) * 100,2);
                      
                      $arrEvaluasiPresentase[$i] = $presentase;
                  ?>
                      <td align="center"><strong><?=$presentase?>%</strong></td>
                  <?
                  }
                  ?>
                </tr>
                <tr>
                  <td valign="top">&nbsp;</td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"> f. Penilaian </td>
                  <?
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                  ?>
                      <td valign="top" align="center">
                      <input type="hidden" name="reqPaketRekananId[]" value="<?=$arrPaketRekananId[$i]?>" />
                      <input type="hidden" name="reqEvaluasiPenilaian[]" id="reqEvaluasiPenilaian<?=$i?>" value="<?=(int)$arrPaketRekananLulus[$i]?>" />
                      <input name="checkbox[]" type="checkbox" style="cursor: pointer;" value="1" id="reqEvaluasiPenilaianCheckbox<?=$i?>" onchange="setEvaluasiPenawaran(this, 'reqEvaluasiPenilaian<?=$i?>')" <? if($arrPaketRekananLulus[$i] == 1) { ?> checked="checked" <? } ?> />                          
                      <label for="reqPenilaian">Memenuhi Syarat</label></td>
                  <?
                  }
                  ?>
                </tr>
                <tr>
                  <td valign="top">&nbsp;</td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top">&nbsp;</td>
                  <?
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                  ?>
                      <td valign="top">&nbsp;</td>
                  <?
                  }
                  ?>
                </tr>
                <tr>
                  <td valign="top"><strong>IV</strong></td>
                  <td colspan="2" valign="top"> <strong>KESIMPULAN</strong></td>
                  <?
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                      if((int)$reqOwnerEstimate == 0)
                        $nilai = 0;
                      else
                          $nilai = round(((int)$arrPaketRekananNilai[$i] / (int)$reqOwnerEstimate) * 100,2);
                      if($arrEvaluasiAdmin[$i] == 0 || $arrEvaluasiTeknis[$i] == 0 || $arrEvaluasiHarga[$i] == 0 || $arrEvaluasiPresentase[$i] == 0 || $arrEvaluasiPresentase[$i] > 100)
                        $hasil = "GUGUR";
                      else
                        $hasil = "LULUS";                   
                  ?>
                      <td align="center"><strong><?=$hasil?></strong></td>
                  <?
                  }
                  ?>
                </tr>
                <tr>
                  <td valign="top">&nbsp;</td>
                  <td colspan="2" valign="top"> Keterangan </td>
                  <?
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                      if((int)$reqOwnerEstimate == 0)
                          $nilai = 0;
                      else
                          $nilai = round(((int)$arrPaketRekananNilai[$i] / (int)$reqOwnerEstimate) * 100,2);
                    
                      if($arrEvaluasiAdmin[$i] == 0)
                        $hasil = "Gagal evaluasi administrasi.";                
                      elseif($arrEvaluasiTeknis[$i] == 0)
                        $hasil = "Gagal evaluasi teknis.";
                      elseif($arrEvaluasiHarga[$i] == 0)
                        $hasil = "Gagal evaluasi harga penawaran.";                 
                      elseif($arrEvaluasiPresentase[$i] == 0)
                        $hasil = "Gagal evaluasi kewajaran harga.";
                      elseif($arrEvaluasiPresentase[$i] > 100)
                        $hasil = "Harga penawaran > 100% OE";
                      else
                        $hasil = "Terendah ke-".getUrutan($arrPaketRekananId[$i], $arrUrutan);                    
                    
                  ?>
                      <td align="center">
                        <strong><?=$hasil?></strong>
                        <input type="hidden" name="reqPaketRekananUrutId[]" value="<?=$arrPaketRekananId[$i]?>">
                        <input type="hidden" name="reqUrutan[]" value="<?=getUrutan($arrPaketRekananId[$i], $arrUrutan)?>">
                        <input type="hidden" name="reqEvaluasiPenilaianKeterangan[]" value="<?=$hasil?>">
                      </td>                              
                  <?
                  }
                  ?>
                </tr>
                <?php 
                if ($reqBidding == 1) {
                   # code...
                 } else { ?>
                <tr>
                  <td valign="top"><strong>V</strong></td>
                  <td colspan="2" valign="top"> <strong>UNDANGAN NEGOSIASI</strong></td>
                  <?
                  for($i=0;$i<count($arrRekanan);$i++)
                  {           
                  ?>
                      <td align="center">
                        <?
                        if($arrEvaluasiAdmin[$i] == 0 || $arrEvaluasiTeknis[$i] == 0 || $arrEvaluasiPresentase[$i] == 0 || $arrEvaluasiPresentase[$i] > 100)
                        {
                            if($reqJenisPegadaaan == "PEMBELIAN")
                            {
                                ?>
                                <input type="radio" name="reqUndangan" value="<?=$arrRekananId[$i]?>" <? if($arrRekananId[$i] == $reqRekananIdPemenang) { ?> checked <? } ?>> Pilih
                                <?
                            } 
                        }
                        else
                        {
                        ?>
                        <input type="radio" name="reqUndangan" value="<?=$arrRekananId[$i]?>" <? if($arrRekananId[$i] == $reqRekananIdPemenang) { ?> checked <? } ?>> Pilih
                        <?
                        }
                        ?>
                      </td>
                  <?
                  }
                  ?>
                </tr>
                <?php 
                } ?>
                <tr>
                  <td valign="top">&nbsp;</td>
                  <td colspan="<?=3+count($arrRekanan)?>" valign="top"> <?=$matrix_evaluasi->getField("KETERANGAN_HARGA")?> </td>
                </tr>
                <tr>
                  <td valign="top">&nbsp;</td>
                  <td colspan="<?=3+count($arrRekanan)?>" valign="top"> <?=$matrix_evaluasi->getField("KETERANGAN_REKAP")?> </td>
                </tr> 
                <tr colspan="5" style="display:none">
                    <td >
                        <textarea name="reqPaketRekananUrutArray"><?php print_r(serialize($arrUrutan)); ?></textarea>           
                    </td>
                </tr>        
            </table>
	</body>
</html>