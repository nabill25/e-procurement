<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("PaketDokumen");
$this->load->model("PaketRekanan");
$this->load->model("RekananPaketPenawaran");
$this->load->library("FileHandler");
//include_once("WEB-INF/classes/utils/FileHandler.php");

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=evaluasi_penawaran_aritmatika.xls");

$FILE_DIR = "uploads/aritmatika/";

$paket_rekanan = new PaketRekanan();
$paket_dokumen = new PaketDokumen();
$rekanan_paket_penawaran = new RekananPaketPenawaran();
$rekanan_paket_penawaran2 = new RekananPaketPenawaran();
$file = new FileHandler();

$reqId = httpFilterRequest("reqId");


$submitSimpan = httpFilterPost("submitSimpan");
$reqLinkFile= $_FILES['reqLinkFile'];
$reqRekananId = $_POST['reqRekananId'];
$reqPaketRekananId = $_POST['reqPaketRekananId'];
$reqPenawaranKoreksi = $_POST['reqPenawaranKoreksi'];
$reqUraian = $_POST["reqUraian"];

$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;

$paket_rekanan = new PaketRekanan();
$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 ");
//echo $paket_rekanan->query;exit;
while($paket_rekanan->nextRow())
{
  $arrRekanan[] = $paket_rekanan->getField("REKANAN");
  $arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
  $arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
  $arrNilaiPenawaran[] = $paket_rekanan->getField("NILAI_PENAWARAN");
}

$rekanan_paket_penawaran->selectByParamsEvaluasiRekanan($arrPaketRekananId, array("A.PAKET_ID" => $reqId));
$rekanan_paket_penawaran2->selectByParamsEvaluasiRekanan($arrPaketRekananId, array("A.PAKET_ID" => $reqId));
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
        <table class="table table-bordered table-hover">
                <tr>
                  <th width="2%">
                    <?php
                      if($paketInfo->sistem_harga == "LOT")
                        echo "Lot";
                      else
                        echo "No.";
                  ?>
                  </th>
                  <th width="38%">Mitra Usaha</th>
                  <!-- <th width="15%">Unit Price</th> -->
                  <th width="15%">Penawaran</th>
                  <!-- <th width="15%">Unit Price Koreksi</th> -->
                  <th width="15%">Penawaran Terkoreksi</th>
                </tr>
                <?php
                $no=1;
                while($rekanan_paket_penawaran->nextRow())
                {
                  $style = "";
                  if($rekanan_paket_penawaran->getField("QUANTITY") == 0)
                    $style = ' style="display:none" ';
                for($i=0;$i<count($arrRekanan);$i++)
                {
                ?>
                <tr>
                  <td width="2%"><?= $no ?></td>
                  <td><?=$arrRekanan[$i]?></td>
                  <?php
                  $arrSummary["SUMMARY"][$no] = $rekanan_paket_penawaran->getField("SUMMARY");
                  $idElement = $arrPaketRekananId[$i]."-".$rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID");
                  ?>
                    <!-- <td align="right" <?=$style?>>
                      <?php //numberToIna($rekanan_paket_penawaran->getField("UP_".$arrPaketRekananId[$i]))?>
                    </td> -->
                    <td align="right" <?=$style?>><?=numberToIna($rekanan_paket_penawaran->getField("SUM_".$arrPaketRekananId[$i]))?></td>
                    <!-- <td align="center" <?=$style?>>
                       <?php //numberToIna($rekanan_paket_penawaran->getField("UPK_".$arrPaketRekananId[$i]))?>
                    </td>  -->
                    <td align="center" <?=$style?>>
                       <?=numberToIna($rekanan_paket_penawaran->getField("SUMKOREKSI_".$arrPaketRekananId[$i]))?>
                    </td>

                  <?php
                      $arrSummary["SUM_".$arrPaketRekananId[$i]][$no] = $rekanan_paket_penawaran->getField("SUM_".$arrPaketRekananId[$i]);
                  ?>
                </tr>
                <?php
                  $no++;
                  }
                }
                while($rekanan_paket_penawaran2->nextRow())
                {
                ?>
                <input type="hidden" name="reqPaketPenawaranId[]" value="<?php echo $rekanan_paket_penawaran2->getField("PAKET_PENAWARAN_ID")?>">
                <input type="hidden" name="reqQuantity[]" id="reqQuantity<?php echo $rekanan_paket_penawaran2->getField("PAKET_PENAWARAN_ID")?>" value="<?php echo $rekanan_paket_penawaran2->getField("QUANTITY")?>">
                <tfoot>
                  <tr>
                    <td>#</td>
                    <td>HPS</td>
                    <td align="right"><?php echo numberToIna($rekanan_paket_penawaran2->getField("OE"))?></td>
                    <td align="right"><?php echo numberToIna($rekanan_paket_penawaran2->getField("SUMMARY"))?></td>
                    <td colspan="2"></td>
                  </tr>
                </tfoot>
                <?php } ?>

                <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                    $reqNilaiPenawaran = $arrNilaiPenawaran[$i];
                  ?>
                      <input type="hidden" name="reqPenawaranSebelumnya[]" value="<?=array_sum($arrSummary["SUM_".$arrPaketRekananId[$i]])?>" class="span2">
                      <input type="hidden" name="reqPaketRekananId[]" value="<?=$arrPaketRekananId[$i]?>">
                  <?php
                  }
                  ?>
              </table>
        </div>
  </body>
</html>
