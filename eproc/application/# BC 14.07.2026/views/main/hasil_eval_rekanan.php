<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$reqId = $this->input->get("reqId");
$this->libsession->cekSession($reqId);

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("Paket");
$this->load->model("PaketRekanan");
$this->load->model("RekananPaketPenawaran");
$this->load->model(array("MatrixEvaluasi","PaketNegoisasi"));
$this->load->model(array("RekananEvaluasiTeknisTawar","RekananEvaluasiAdminTawar","RekananEvaluasiHargaTawar"));
$this->load->model(array("PaketEvaluasiValidasi","Paketpemenang","PaketTahap"));


// cek pemenang
$getpaket_pemenang = new Paketpemenang();
$getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqId), -1, -1);
$getpaket_pemenang->firstRow();
$cekPublishPemenang = $getpaket_pemenang->getField("PUBLISH");


// if ($cekPublishPemenang != "1") {
//   echo "Pemenang belom di publish"; die;
// }

$paket_rekanan = new PaketRekanan();
$paket_rekanan_nilai = new PaketRekanan();
$matrix_evaluasi = new MatrixEvaluasi();
$paket_evaluasi_validasi = new PaketEvaluasiValidasi();
$paket_tahap = new PaketTahap();
$paket_negosiasi = new PaketNegoisasi();
$paket_tahap_metode = new PaketTahap();

$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;
$reqJenisPekerjaan = $paketInfo->jenis;
$reqJenisPekerjaanId = $paketInfo->jenis_id;
$reqMetodeEvaluasi = $paketInfo->metode_evaluasi;
$reqMetodeEvaluasiId = $paketInfo->metode_evaluasi_id;
$reqOwnerEstimate  = $paketInfo->nilai_owner_estimate;
$reqRekananIdPemenang = $paketInfo->rekanan_id_pemenang;
$reqMetodePengadaan = $paketInfo->metode_lelang_id;
$reqMetodeLelang = $paketInfo->metode_lelang_id;
$bidding = $paketInfo->bidding;
$reqUUID = $paketInfo->uuid;

if ($paketInfo->publish_ba_sampul1 != "1" && $paketInfo->publish_ba_sampul2 != "1") {
  echo "Hasil Evaluasi belom di publish"; die;
}

if ($bidding == '0' || $bidding == '') { // jika Sistem Negosiasi
  // $paket_rekanan->selectByParams3(array("A.PAKET_ID" => $reqId), -1, -1, " AND LULUS_PENDAFTARAN = 1 AND LULUS_KUALIFIKASI = 1 AND LULUS_PENAWARAN = 1 AND KIRIM_PENAWARAN = 1  ORDER BY A.NILAI_PENAWARAN ASC", $reqId);
  $paket_rekanan->selectByParams3(array("A.PAKET_ID" => $reqId), -1, -1, " AND LULUS_PENDAFTARAN = 1 AND LULUS_KUALIFIKASI = 1 AND KIRIM_PENAWARAN = 1  ORDER BY A.NILAI_PENAWARAN ASC", $reqId);
} else { // jika Sistem Negosiasi nya Bidding
  $paket_rekanan->selectByParams4(array("A.PAKET_ID" => $reqId), -1, -1, " AND LULUS_PENDAFTARAN = 1 ORDER BY A.NILAI_PENAWARAN ASC", $reqId);
}

// $jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
// $aktif_penentuan_pemenang = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrPenetapanPemenang[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
// $aktif_penentuan_pemenang2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrPenetapanPemenang[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
// if($aktif_penentuan_pemenang  > 0 || $aktif_penentuan_pemenang2  > 0) {
//   $info = "1";
// } else {
//   $info = "0";
// }

?>

<style type="text/css">
  table th {
    background-color: #967adc;
    color: #fff;
  }
</style>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Hasil Evaluasi Penawaran</h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable">

  		    <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
            <div>
              <table class="table table-bordered table-hover">
                <tr>
                  <td width="15%"> Pekerjaan</td>
                  <td> <?=$reqNamaPaket?> </td>
                </tr>
                <tr>
                  <td width="15%"> Jenis Pekerjaan </td>
                  <td colspan="2"> <?=$reqJenisPekerjaan?> </td>
                </tr>
                <tr>
                  <td width="15%"> Metode Evaluasi</td>
                  <td> <?=$reqMetodeEvaluasi?> </td>
                </tr>
              <tr>
              </table>

              <table class="table table-bordered table-responsive">
                  <thead>
                    <tr>
                      <th rowspan="2" width="5%">No.</th>
                      <th rowspan="2">
                        <?php
                        if ($reqMetodeLelang == 1 || $reqMetodeLelang == 7 || $reqMetodeLelang == 10) { // tender & tender cepat
                           echo "Nama Peserta";
                        } else {
                          echo "Nama Penyedia";
                        }?>
                      </th>
                      <?php
                      if ($reqMetodeLelang != '7') { // Selain Tender Cepat ?>
                      <th colspan="3" width="21%" style="text-align: center">Evaluasi</th>
                      <?php
                      } else { ?>
                      <th colspan="1" width="21%" style="text-align: center">Evaluasi</th>
                      <?php
                      }  ?>
                      <?php
                      if ($reqMetodeEvaluasiId == '2') {
                        echo '<th rowspan="2" width="5%" style="text-align: center">Total <br> Kombinasi</th>';
                      } ?>
                      <th rowspan="2" width="15%" style="text-align: center">Penawaran</th>
                      <th rowspan="2" width="15%" style="text-align: center">Penawaran Terkoreksi</th>
                      <?php
                      if ($bidding == '0' || $bidding == '') { // jika Sistem Negosiasi  ?>
                      <th rowspan="2">Negosiasi</th>
                      <?php
                      } else { ?>
                      <th rowspan="2">Harga <br> e-Reverse Auction</th>
                      <?php
                      } ?>
                      <th rowspan="2" width="15%" style="text-align: center">Hasil Evaluasi</th>
                    </tr>
                    <tr>
                      <?php
                      if ($reqMetodeLelang != '7') { // Selain Tender Cepat ?>
                      <th width="7%" style="text-align: center">Adm.</th>
                      <th width="7%" style="text-align: center">Teknis</th>
                      <?php } ?>
                      <th width="7%" style="text-align: center">Harga</th>
                    </tr>

                    <tr style="background: #967adc; color: #fff">
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $no=1;
                      $noLulus=1;
                    if ($paket_rekanan->countRow() == 0) {
                      echo '<td colspan="8">. : : Data tidak ada : : .</td>';
                    } else
                    {
                    while($paket_rekanan->nextRow())
                    {
                      $rekanan_evaluasi_admin = new RekananEvaluasiAdminTawar();
                      $rekanan_evaluasi_admin->selectMemenuhiSyarat($reqId, $paket_rekanan->getField("PAKET_REKANAN_ID"));
                      $rekanan_evaluasi_admin->firstRow();

                      $rekanan_evaluasi_teknis = new RekananEvaluasiTeknisTawar();
                      $rekanan_evaluasi_teknis->selectMemenuhiSyarat($reqId, $paket_rekanan->getField("PAKET_REKANAN_ID"));
                      $rekanan_evaluasi_teknis->firstRow();

                      $rekanan_evaluasi_harga = new RekananEvaluasiHargaTawar();
                      $rekanan_evaluasi_harga->selectMemenuhiSyarat2($reqId, $paket_rekanan->getField("PAKET_REKANAN_ID"));
                      $rekanan_evaluasi_harga->firstRow();

                      $paket_negosiasi->selectByParams(array("A.PAKET_PENAWARAN_ID" => $paket_rekanan->getField("PAKET_PENAWARAN_ID")));
                      $paket_negosiasi->firstRow();
                      $penawaranNegosiasi =  $paket_negosiasi->getField("UNIT_PRICE");
                      $jumlahNegosiasi =  $paket_negosiasi->getField("TOTAL");
                      $setujui =  $paket_negosiasi->getField("SETUJUI");
                    ?>
                    <tr>
                      <td><?=$no?></td>
                      <td><?=$paket_rekanan->getField("REKANAN")?></td>
                      <?php
                      if ($reqMetodeLelang != '7') { // Selain Tender Cepat ?>
                      <td class="text">
                        <?php
                        if($rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI") >= 1)
                        {
                          $status_admin = '<img src="images/centang-cetak.png">';
                          $keterangan_admin = $rekanan_evaluasi_admin->getField("KETERANGAN_LULUS");
                          $arrEvaluasiAdmin[$i] = 1;
                        }
                        else
                        {
                          $status_admin = '<img src="images/uncentang-cetak.png">';
                          $keterangan_admin = $rekanan_evaluasi_admin->getField("KETERANGAN_GAGAL");
                          $arrEvaluasiAdmin[$i] = 0;
                        }
                        echo $status_admin.'<br><small>'.$keterangan_admin.'</small>';
                        ?>
                      </td>
                      <td class="text">
                      <?php
                        if($rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI") >= 1 && $rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI") >= 1)
                        {
                          $status_teknis = '<img src="images/centang.png">';
                          $arrEvaluasiTeknis[$i] = 1;
                          if ($reqMetodeEvaluasiId == '2') {
                            $skor_teknis = 'Nilai Teknis <b>'.$rekanan_evaluasi_teknis->getField("NILAI_TEKNIS").'</b><br>'.$rekanan_evaluasi_teknis->getField("KETERANGAN_LULUS");
                            $skor_teknis_angka[$arrPaketRekananId[$i]] = $rekanan_evaluasi_teknis->getField("NILAI_TEKNIS");
                          } else {
                            $keterangan_teknis = $rekanan_evaluasi_teknis->getField("KETERANGAN_LULUS");
                          }
                          // $arrEvaluasiTeknis[$i] = 1;
                        }
                        else
                        {
                          $status_teknis = '<img src="images/uncentang.png">';
                          $skor_teknis = 'Nilai Teknis <b>'.$rekanan_evaluasi_teknis->getField("NILAI_TEKNIS").'</b>';
                          $keterangan_teknis = $rekanan_evaluasi_teknis->getField("KETERANGAN_GAGAL");
                          $arrEvaluasiTeknis[$i] = 0;
                        }
                        // echo $status_teknis.'<br><small>'.$keterangan_teknis.'</small>';
                          if ($reqMetodeEvaluasiId == '2') { ?>
                            <?= $status_teknis.'<br><small>'.$skor_teknis.'</small><br><small>'.$keterangan_teknis.'</small>'; ?>
                          <?php
                          } else { ?>
                            <?= $status_teknis.'<br><small>'.$keterangan_teknis.'</small>'; ?>
                          <?php
                          } ?>
                      </td>
                      <?php
                      } ?>
                      <td class="text">
                      <?php
                        if ($reqMetodePengadaan != 7) {
                          if($rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI") >= 1 && $rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI") >= 1 && $rekanan_evaluasi_harga->getField("JUMLAH_DILENGKAPI") >= 1)
                          {
                            $status_harga = '<img src="images/centang.png">';
                            $arrEvaluasiHarga[$i] = 1;
                            if ($reqMetodeEvaluasiId == '2') {
                              $skor_harga = 'Nilai Harga <b>'.$rekanan_evaluasi_harga->getField("NILAI_HARGA").'</b><br>'.$rekanan_evaluasi_harga->getField("KETERANGAN_LULUS");
                              $skor_harga_angka[$arrPaketRekananId[$i]] = $rekanan_evaluasi_harga->getField("NILAI_HARGA");
                            } else {
                              $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_LULUS");
                            }

                          }
                          else
                          {
                            $status_harga = '<img src="images/uncentang.png">';
                            $skor_harga = 'Nilai Harga <b>'.$rekanan_evaluasi_harga->getField("NILAI_HARGA").'</b>';
                            $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_GAGAL");
                            $arrEvaluasiHarga[$i] = 0;
                          }
                        } else
                        {
                          if($rekanan_evaluasi_harga->getField("JUMLAH_DILENGKAPI") >= 1)
                          {
                            $status_harga = '<img src="images/centang.png">';
                            $arrEvaluasiHarga[$i] = 1;
                            if ($reqMetodeEvaluasiId == '2') {
                              $skor_harga = 'Nilai Harga <b>'.$rekanan_evaluasi_harga->getField("NILAI_HARGA").'</b>';
                            } else {
                              $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_LULUS");
                            }
                          }
                          else
                          {
                            $status_harga = '<img src="images/uncentang.png">';
                            $skor_harga = 'Nilai Harga <b>'.$rekanan_evaluasi_harga->getField("NILAI_HARGA").'</b>';
                            $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_GAGAL");
                            $arrEvaluasiHarga[$i] = 0;
                          }
                        }

                        // echo $status_harga.'<br><small>'.$keterangan_harga.'</small>';
                        if ($reqMetodeEvaluasiId == '2') { ?>
                          <?= $status_harga.'<br><small>'.$skor_harga.'<br>'.$keterangan_harga.'</small>'; ?>
                        <?php
                        } else { ?>
                          <?= $status_harga.'<br><small>'.$keterangan_harga.'</small>'; ?>
                        <?php
                        } ?>
                      </td>

                      <?php
                      if ($reqMetodeEvaluasiId == '2') {
                        $totalKombinasi = $rekanan_evaluasi_harga->getField("NILAI_HARGA") + $rekanan_evaluasi_teknis->getField("NILAI_TEKNIS");
                        echo '<td style="text-align:center">'.$totalKombinasi.'</td>';
                      } ?>

                      <td><?=numberToIna($paket_rekanan->getField("UNIT_PRICE"))?></td>
                      <td><?=numberToIna($paket_rekanan->getField("JUMLAH_KOREKSI"))?></td>

                      <?php
                      if ($bidding == '0' || $bidding == '') { // jika Sistem Negosiasi  ?>
                      <td>
                        <?php
                        if ($reqRekananIdPemenang == $paket_rekanan->getField("REKANAN_ID")) {
                          echo numberToIna($jumlahNegosiasi).'';
                        } else {
                          echo "";
                        }

                        ?>
                      </td>
                      <?php
                      } else {
                      if($rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI") >= 1 && $rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI") >= 1 && $rekanan_evaluasi_harga->getField("JUMLAH_DILENGKAPI") >= 1)
                      {
                          ?>
                        <td><?=numberToIna($paket_rekanan->getField("NILAI_PENAWARAN"))?></td>
                      <?php
                        } else {
                          echo '<td></td>';
                        }
                      } ?>

                      <?php
                      if ($reqMetodeLelang != '7') { // Selain Tender Cepat
                        if($arrEvaluasiAdmin[$i] == 0 || $arrEvaluasiTeknis[$i] == 0 || $arrEvaluasiHarga[$i] == 0)
                        {
                          $evaluasi = 0;
                          $hasil2 = "Tidak Memenuhi Syarat";
                        }
                        else
                        {
                          $evaluasi = 1;
                          $hasil2 = "Memenuhi Syarat";
                        }
                      } else {
                        if($arrEvaluasiHarga[$i] == 0)
                        {
                          $evaluasi = 0;
                          $hasil2 = "Tidak Memenuhi Syarat";
                        }
                        else
                        {
                          $evaluasi = 1;
                          $hasil2 = "Memenuhi Syarat";
                        }
                      } ?>
                      <td <?= $bold ?> class="text-center"> <?=$hasil2?></td>
                    </tr>
                    <?php
                      $no++;
                      if ($getpaket_pemenang->countRow() > 0) {
                      $noLulus++;
                      } else {
                      }
                      unset($rekanan_evaluasi_admin);
                      unset($rekanan_evaluasi_teknis);
                      unset($rekanan_evaluasi_harga);
                    }
                    } ?>
                  </tbody>
                </table>

              <div class="form-actions">
                <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?>"> <?= BTN_KEMBALI ?> </a>
              </div>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>



   </div>
  </div>
</div>

<script type="text/javascript">
  const maxChars = 25;
  const textCells = document.querySelectorAll(".text");

  textCells.forEach(td => {
    const small = td.querySelector("small");
    if (!small) return;

    const fullText = small.textContent.trim();

    if (fullText.length > maxChars) {
      const visibleText = fullText.slice(0, maxChars);
      const hiddenText = fullText.slice(maxChars);

      small.innerHTML = `
        ${visibleText}<span class="dots">...</span><span class="more-text" style="display:none;">${hiddenText}</span>
        <button type="button" class="more-btn" onclick="toggleText(this)"
          style="color:blue; background:none; border:none; cursor:pointer; font-size:12px;">More..
        </button>
      `;
    }
  });

  function toggleText(btn) {
    const small = btn.parentNode;
    const dots = small.querySelector(".dots");
    const moreText = small.querySelector(".more-text");

    if (moreText.style.display === "none" || moreText.style.display === "") {
      moreText.style.display = "inline";
      dots.style.display = "none";
      btn.textContent = "Less";
    } else {
      moreText.style.display = "none";
      dots.style.display = "inline";
      btn.textContent = "More";
    }
  }
</script>
