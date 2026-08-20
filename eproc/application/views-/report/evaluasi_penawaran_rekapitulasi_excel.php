<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
if($this->USER_TYPE_ID == "")
    redirect("main");

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("Paket");
$this->load->model("PaketRekanan");
$this->load->model("PaketDokumen");
$this->load->model("RekananEvaluasiTeknisTawar");
$this->load->model("RekananEvaluasiAdminTawar");
$this->load->model("RekananEvaluasiHargaTawar");
$this->load->model("MatrixEvaluasi");
$this->load->model("RekananPaketPenawaran");
$this->load->model("PaketPenawaran");

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=evaluasi_penawaran_rekapitulasi.xls");

$paket_rekanan = new PaketRekanan();
$paket_rekanan_nilai = new PaketRekanan();
$matrix_evaluasi = new MatrixEvaluasi();
$paket_penawaran = new PaketPenawaran();
$paket_dokumen = new PaketDokumen();

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
$reqMetodePengadaan = $paketInfo->metode_lelang_id;
$reqJenisPegadaaan = $paketInfo->jenis_pengadaan;
$reqBidding = $paketInfo->bidding;

$paket_rekanan->selectByParams2(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 ");
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
        <?php
        for($i=0;$i<count($arrRekanan);$i++)
        {
        ?>
        <th style="text-align: center;"><?=$arrRekanan[$i]?></th>
        <?php
        }
        ?>
      </tr>
      <?php
                // 1-e-Tender ,2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat,7-e-Tender Cepat
                if ($reqMetodePengadaan != 7) { ?>
                <tr>
                  <td valign="top"><strong>I</strong></td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"><strong> EVALUASI ADMINISTRASI </strong></td>
                  <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                      $rekanan_evaluasi_admin = new RekananEvaluasiAdminTawar();
                      $rekanan_evaluasi_admin->selectMemenuhiSyarat($reqId, $arrPaketRekananId[$i]);
                      $rekanan_evaluasi_admin->firstRow();
                      // echo $rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI");
                      // if($rekanan_evaluasi_admin->getField("JUMLAH_EVALUASI") <= $rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI"))
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
                  <?php
                      unset($rekanan_evaluasi_admin);
                  }
                  ?>
                </tr>
                <tr>
                  <td valign="top"><strong>II</strong></td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"><strong> EVALUASI TEKNIS </strong></td>
                  <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                      $rekanan_evaluasi_teknis = new RekananEvaluasiTeknisTawar();
                      $rekanan_evaluasi_teknis->selectMemenuhiSyarat($reqId, $arrPaketRekananId[$i]);
                      $rekanan_evaluasi_teknis->firstRow();
                      // if($rekanan_evaluasi_teknis->getField("JUMLAH_EVALUASI") <= $rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI"))
                      if($rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI") >= 1 && $arrEvaluasiAdmin[$i] == 1)
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
                  <?php
                      unset($rekanan_evaluasi_teknis);
                  }
                  ?>
                </tr>
                <?php
                } ?>
                <tr>
                  <td valign="top">
                  <?php
                  // 1-e-Tender ,2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat,7-e-Tender Cepat
                  if ($reqMetodePengadaan != 7) { ?>
                    <strong>III</strong>
                  <?php
                  } else { ?>
                    <strong>I</strong>
                  <?php
                  } ?>
                  </td>
                  <td valign="top">&nbsp;</td>
                  <td valign="top"><strong> EVALUASI HARGA </strong></td>
                  <?php
                  for($i=0;$i<count($arrRekanan);$i++)
                  {
                      $rekanan_evaluasi_harga = new RekananEvaluasiHargaTawar();
                      $rekanan_evaluasi_harga->selectMemenuhiSyarat2($reqId, $arrPaketRekananId[$i]);
                      $rekanan_evaluasi_harga->firstRow();
                      // 1-e-Tender ,2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat,7-e-Tender Cepat
                      if ($reqMetodePengadaan != 7) {
                        if($rekanan_evaluasi_harga->getField("JUMLAH_DILENGKAPI") >= 1 && $arrEvaluasiAdmin[$i] == 1 && $arrEvaluasiTeknis[$i] == 1)
                        {
                          $status_harga = "MEMENUHI SYARAT";
                          $arrEvaluasiHarga[$i] = 1;
                        }
                        else
                        {
                          $status_harga = "TIDAK MEMENUHI SYARAT";
                          $arrEvaluasiHarga[$i] = 0;
                        }
                      } else
                      {
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
                      }
                  ?>
                      <td align="center"><strong><?=$status_harga?></strong></td>
                  <?php
                      unset($rekanan_evaluasi_harga);
                  }
                  ?>
                </tr>

      <!-- <tr>
        <td valign="top">&nbsp;</td>
        <td valign="top"> -->
          <?php
          // 1-e-Tender ,2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat,7-e-Tender Cepat
          // if ($reqMetodePengadaan != 7) { ?>
            <!-- I -->
          <?php
          // } else {
          //   echo "1.";
          // } ?>
       <!--  </td>
        <td valign="top"> EVALUASI KEWAJARAN HARGA </td> -->
        <?php
        // for($i=0;$i<count($arrRekanan);$i++)
        // {
        ?>
            <!-- <td valign="top">&nbsp;</td> -->
        <?php
        // }
        ?>
      <!-- </tr> -->
      <tr>
        <td valign="top">&nbsp;</td>
        <td valign="top">&nbsp;</td>
        <td valign="top"> a. Penawaran Terkoreksi </td>
        <?php
        for($i=0;$i<count($arrRekanan);$i++)
        {
        ?>
            <td align="center"><strong><?=$paketInfo->mata_uang?> <?=numberToIna($arrPaketRekananNilai[$i])?></strong></td>
            <!-- <td align="center"><strong><?=$paketInfo->mata_uang?> <?php //numberToIna($paket_penawaran->getField("JUMLAH_KOREKSI"))?></strong></td> -->
        <?php
        }
        ?>
      </tr>
      <tr>
        <td valign="top">&nbsp;</td>
        <td valign="top">&nbsp;</td>
        <td valign="top"> b. HPS </td>
        <td valign="top" colspan="<?=count($arrRekanan)?>" align="center"> <strong><?=$paketInfo->mata_uang?> <?=numberToIna($reqOwnerEstimate)?></strong> </td>
      </tr>
      <!-- <tr>
        <td valign="top">&nbsp;</td>
        <td valign="top">&nbsp;</td>
        <td valign="top"> e. Persentase Kesalahan Penawaran </td>
        <?php
        for($i=0;$i<count($arrRekanan);$i++)
        {
        ?>
        <td valign="top" align="center"> 0,00% </td>
        <?php
        }
        ?>
      </tr> -->
      <tr>
        <td valign="top">&nbsp;</td>
        <td valign="top">&nbsp;</td>
        <td valign="top"> c. % penawaran terkoreksi terhadap HPS </td>
        <?php
        for($i=0;$i<count($arrRekanan);$i++)
        {
            if((int)$reqOwnerEstimate == 0)
              $presentase = 0;
            else
              $presentase = round(($arrPaketRekananNilai[$i] / $reqOwnerEstimate) * 100,2);

            $arrEvaluasiPresentase[$i] = $presentase;
        ?>
            <td align="center"><strong><?=$presentase?>%</strong></td>
        <?php
        }
        ?>
      </tr>
 
      <?php
      // 1-e-Tender ,2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat,7-e-Tender Cepat
      if ($reqMetodePengadaan != 7)
      { ?>
        <tr>
          <td valign="top"><strong>IV</strong></td>
          <td colspan="2" valign="top"> <strong>KESIMPULAN</strong></td>
          <?php
          for($i=0;$i<count($arrRekanan);$i++)
          {
              if((int)$reqOwnerEstimate == 0)
                $nilai = 0;
              else
                  $nilai = round(((int)$arrPaketRekananNilai[$i] / (int)$reqOwnerEstimate) * 100,2);
              if($arrEvaluasiAdmin[$i] == 0 || $arrEvaluasiTeknis[$i] == 0 || $arrEvaluasiHarga[$i] == 0)
              {
                $hasil = "GUGUR";
              } else
              {
                if ($arrEvaluasiPresentase[$i] == 0 || $arrEvaluasiPresentase[$i] > 100) {
                  $hasil = "LULUS <br> <small style='color:red'><i>Diatas HPS</i></small>";
                  $notifNegosiasi = 'Ya';
                } else {
                  $hasil = "LULUS";
                  $notifNegosiasi = 'Ya';
                }
              }
          ?>
              <td align="center"><strong><?=$hasil?></strong></td>
          <?php
          }
          ?>
        </tr>
      <?php
      } else { ?>
        <tr>
          <td valign="top"><strong>II</strong></td>
          <td colspan="2" valign="top"> <strong>KESIMPULAN</strong></td>
          <?php
          for($i=0;$i<count($arrRekanan);$i++)
          {
              if((int)$reqOwnerEstimate == 0)
                $nilai = 0;
              else
                $nilai = round(((int)$arrPaketRekananNilai[$i] / (int)$reqOwnerEstimate) * 100,2);

            // if($arrEvaluasiHarga[$i] == 0 || $arrEvaluasiPresentase[$i] == 0 || $arrEvaluasiPresentase[$i] > 100)
              if($arrEvaluasiHarga[$i] == 0)
              {
                $hasil = "GUGUR";
              } else
              {
                if ($arrEvaluasiPresentase[$i] == 0 || $arrEvaluasiPresentase[$i] > 100) {
                  $hasil = "LULUS <br> <small style='color:red'><i>Diatas HPS</i></small>";
                  $notifNegosiasi = 'Ya';
                } else {
                  $hasil = "LULUS";
                  $notifNegosiasi = 'Ya';
                }
              }
          ?>
              <td align="center"><strong><?=$hasil?></strong></td>
          <?php
          }
          ?>
        </tr>
      <?php
      } ?>
      <?php
      if ($reqBidding == 1) {
      ?>
        <tr>
          <td valign="top"><strong>III</strong></td>
          <td colspan="2" valign="top"> <strong>AUCTION</strong></td>
          <?php
          for($i=0;$i<count($arrRekanan);$i++)
          {
            if ($reqMetodePengadaan != 7)
            {
              // if($arrEvaluasiAdmin[$i] == 0 || $arrEvaluasiTeknis[$i] == 0 || $arrEvaluasiHarga[$i] == 0 || $arrEvaluasiPresentase[$i] == 0 || $arrEvaluasiPresentase[$i] > 100) {
              if($arrEvaluasiAdmin[$i] == 0 || $arrEvaluasiTeknis[$i] == 0 || $arrEvaluasiHarga[$i] == 0)
              {
                $notifNegosiasi = '-';
              } else
              {
                $notifNegosiasi = 'Ya';
              }
            } else {
              // if($arrEvaluasiHarga[$i] == 0 || $arrEvaluasiPresentase[$i] == 0 || $arrEvaluasiPresentase[$i] > 110){
              if($arrEvaluasiHarga[$i] == 0)
              {
                $notifNegosiasi = '-';
              } else
              {
                $notifNegosiasi = 'Ya';
              }
            }
          ?>
              <td align="center">
                <!-- <strong><?php //$paketInfo->mata_uang?> <?php // numberToIna($arrPaketRekananNilaiAuction[$i])?></strong> -->
                <?= $notifNegosiasi; ?>
              </td>
          <?php
          }
          ?>
        </tr>
      <?php
       } else {
        // 1-e-Tender ,2-Pengadaan Langsung ,5-Penunjukan Langsung Cepat,7-e-Tender Cepat
        if ($reqMetodePengadaan != 7)
        { // Bukan Tender Cepat
      ?>
      <tr>
        <td valign="top"><strong>V</strong></td>
        <td colspan="2" valign="top"> <strong>UNDANGAN KLARIFIKASI DAN NEGOSIASI</strong></td>
        <?php
        for($i=0;$i<count($arrRekanan);$i++)
        {
        ?>
            <td align="center">
              <?php
              // if($arrEvaluasiHarga[$i] == 0 || $arrEvaluasiPresentase[$i] == 0 || $arrEvaluasiPresentase[$i] > 110)
              // if($arrEvaluasiHarga[$i] == 0)
              if($arrEvaluasiAdmin[$i] == 0 || $arrEvaluasiTeknis[$i] == 0 || $arrEvaluasiHarga[$i] == 0)
	      {
                echo '-';
              }
              else
              {
                if($arrRekananId[$i] == $reqRekananIdPemenang) {
                 echo 'Ya';
                } else {
                 echo "-";
                }
              ?>
              <!-- <input type="radio" name="reqUndangan" value="<?=$arrRekananId[$i]?>" <?php // if($arrRekananId[$i] == $reqRekananIdPemenang) { ?> checked <?php //} ?>> Pilih -->
              <?php
              }
              ?>
            </td>
        <?php
        }
        ?>
      </tr>
      <?php
        }
      }
      ?>
  </table>
	</body>
</html>
