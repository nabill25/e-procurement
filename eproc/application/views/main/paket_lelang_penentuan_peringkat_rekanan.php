<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$reqId = $this->input->get("reqId");
$this->libsession->cekSession($reqId);

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketRekanan");
$this->load->model("Paket");
$this->load->model("PaketPanitia");
$this->load->model("Paketpemenangperingkat");
$this->load->model("RekananEvaluasiTeknisTawar");
$this->load->model("RekananEvaluasiAdminTawar");
$this->load->model("RekananEvaluasiHargaTawar");
$this->load->model("PaketNegoisasi");
$this->load->model("PaketTahap");
$this->load->model("PaketDokumen");
$this->load->library("FileHandler");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$arrPeringkat = PERINGKAT;

$paket_rekanan = new PaketRekanan();
$paket_pemenang = new Paketpemenangperingkat();
$getpaket_pemenang = new Paketpemenangperingkat();
$countpaket_pemenang = new Paketpemenangperingkat();
$paket_negosiasi = new PaketNegoisasi();
$paket_tahap = new PaketTahap();
$paket_tahap_metode = new PaketTahap();

$reqMode = $this->input->get("reqMode");
$reqPemenang= $this->input->post('reqPemenang');
$reqNegosiasi= $this->input->post('reqNegosiasi');
$reqTanggal= $this->input->post('reqTanggal');
// $reqKeterangan= $this->input->post('reqKeterangan');
$submitSimpan= $this->input->post('submitSimpan');

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqMetodeLelang = $paketInfo->metode_lelang_id;
$reqMetodeEvaluasiId = $paketInfo->metode_evaluasi_id;
$bidding = $paketInfo->bidding;
$reqPaketMetodeLelangId = $paketInfo->metode_lelang_id;
$reqRekananIdPemenang = $paketInfo->rekanan_id_pemenang;
$reqUserLoginId = $paketInfo->user_login_id;

$paket_panitia = new PaketPanitia();
$paket_panitia->selectByParams(array("A.PAKET_ID" => $reqId));

$getpaket_pemenang->selectByParams(array("PAKET_ID" => $reqId), -1, -1);
$countpaket_pemenang = $countpaket_pemenang->getCountByParams(array("A.PAKET_ID" => $reqId));

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
$aktif_peringkat = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrPeringkat[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_peringkat2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrPeringkat[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));


if($aktif_peringkat > 0 || $aktif_peringkat2 > 0) {
  $info = "1";
} else {
  $info = "0";
}

?>

<style type="text/css">
  tr > th {
    vertical-align: middle;
    text-align: center;
  }
</style>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Pemberitahuan Peringkat <?= $paketInfo->metode_lelang_nama ?></h4>
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

          <?php
          if ($info == 0) { ?>
          <div class="alert alert-danger" style="color:#fff">
            <span style="color: #fff">
              Pemberitahuan Peringkat belum mulai.
            </span>
          </div>
          <?php
          } ?>
      <?php
      if ($info == 1)
      {  ?>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Daftar Peringkat</strong>
                </div>
                <div class="table-responsive">
                  <table class="border-double table mb-0">
                    <thead>
                      <th width="5%">Urutan</th>
                      <th style="text-align: left;">Nama </th>
                      <?php
                      if ($reqMetodeLelang != '7') { // Selain Tender Cepat ?>
                      <th width="20%">Evaluasi <br> Administrasi</th>
                      <th width="20%">Evaluasi <br> Teknis</th>
                      <?php
                      } ?>
                      <th width="20%">Evaluasi <br> Harga</th>
                    </thead>
                    <tbody>
                      <?php
                      if ($countpaket_pemenang > 0) {
                        $no=1;
                        while($getpaket_pemenang->nextRow())
                        {
                          // $paket_rekanan->selectByParams(array("PAKET_ID" => $reqId, "A.REKANAN_ID" => $getpaket_pemenang->getField("REKANAN_ID")), -1, -1, " AND LULUS_PENDAFTARAN = 1 AND LULUS_KUALIFIKASI = 1 AND LULUS_PENAWARAN = 1 AND KIRIM_PENAWARAN = 1", $reqId);
                          $paket_rekanan->selectByParams(array("PAKET_ID" => $reqId, "A.REKANAN_ID" => $getpaket_pemenang->getField("REKANAN_ID")), -1, -1, " AND LULUS_PENDAFTARAN = 1 AND LULUS_KUALIFIKASI = 1", $reqId);
                          $paket_rekanan->firstRow();

                          $rekanan_evaluasi_admin = new RekananEvaluasiAdminTawar();
                          $rekanan_evaluasi_admin->selectMemenuhiSyarat($reqId, $paket_rekanan->getField("PAKET_REKANAN_ID"));
                          $rekanan_evaluasi_admin->firstRow();

                          $rekanan_evaluasi_teknis = new RekananEvaluasiTeknisTawar();
                          $rekanan_evaluasi_teknis->selectMemenuhiSyarat($reqId, $paket_rekanan->getField("PAKET_REKANAN_ID"));
                          $rekanan_evaluasi_teknis->firstRow();

                          $rekanan_evaluasi_harga = new RekananEvaluasiHargaTawar();
                          $rekanan_evaluasi_harga->selectMemenuhiSyarat2($reqId, $paket_rekanan->getField("PAKET_REKANAN_ID"));
                          $rekanan_evaluasi_harga->firstRow();
                          ?>
                          <tr>
                           <td style="width:5%; text-align:center"><?= $getpaket_pemenang->getField("PERINGKAT")?></td>
                           <td><?= $getpaket_pemenang->getField("NAMA")?></td>
                           <?php
                          if ($reqMetodeLelang != '7') { // Selain Tender Cepat ?>
                          <td style="text-align:center">
                            <?php
                            if($rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI") >= 1)
                            {
                              $status_admin = '<img class="text-center" src="images/centang-cetak.png">';
                              $keterangan_admin = $rekanan_evaluasi_admin->getField("KETERANGAN_LULUS");
                              $arrEvaluasiAdmin[$i] = 1;
                            }
                            else
                            {
                              $status_admin = '<img class="text-center" src="images/uncentang-cetak.png">';
                              $keterangan_admin = $rekanan_evaluasi_admin->getField("KETERANGAN_GAGAL");
                              $arrEvaluasiAdmin[$i] = 0;
                            }
                            echo $status_admin.'<br><small>'.$keterangan_admin.'</small>';
                            ?>
                          </td>
                          <td style="text-align:center">
                          <?php
                            if($rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI") >= 1 && $rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI") >= 1)
                            {
                              $status_teknis = '<img class="text-center" src="images/centang-cetak.png">';
                              if ($reqMetodeEvaluasiId == '2') {
                                $keterangan_teknis = 'Nilai Teknis <b>'.$rekanan_evaluasi_teknis->getField("NILAI_TEKNIS").'</b>';
                              } else {
                                $keterangan_teknis = $rekanan_evaluasi_teknis->getField("KETERANGAN_LULUS");
                              }
                              $arrEvaluasiTeknis[$i] = 1;
                            }
                            else
                            {
                              $status_teknis = '<img class="text-center" src="images/uncentang-cetak.png">';
                              $keterangan_teknis = $rekanan_evaluasi_teknis->getField("KETERANGAN_GAGAL");
                              $arrEvaluasiTeknis[$i] = 0;
                            }
                            echo $status_teknis.'<br><small>'.$keterangan_teknis.'</small>';
                          ?>
                          </td>
                          <?php
                          } ?>
                          <td style="text-align:center">
                          <?php
                            if ($reqMetodeLelang != '7') { // Selain Tender Cepat

                              if($rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI") >= 1 && $rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI") >= 1 && $rekanan_evaluasi_harga->getField("JUMLAH_DILENGKAPI") >= 1)
                              {
                                $status_harga = '<img class="text-center" src="images/centang-cetak.png">';
                                if ($reqMetodeEvaluasiId == '2') {
                                  $keterangan_harga = 'Nilai Harga <b>'.$rekanan_evaluasi_harga->getField("NILAI_HARGA").'</b>';
                                } else {
                                  $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_LULUS");
                                }
                                $arrEvaluasiHarga[$i] = 1;
                              }
                              else
                              {
                                $status_harga = '<img class="text-center" src="images/uncentang-cetak.png">';
                                $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_GAGAL");
                                $arrEvaluasiHarga[$i] = 0;
                              }
                            } else {
                              if($rekanan_evaluasi_harga->getField("JUMLAH_DILENGKAPI") >= 1)
                              {
                                $status_harga = '<img class="text-center" src="images/centang-cetak.png">';
                                if ($reqMetodeEvaluasiId == '2') {
                                  $keterangan_harga = 'Nilai Harga <b>'.$rekanan_evaluasi_harga->getField("NILAI_HARGA").'</b>';
                                } else {
                                  $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_LULUS");
                                }
                                $arrEvaluasiHarga[$i] = 1;
                              }
                              else
                              {
                                $status_harga = '<img class="text-center" src="images/uncentang-cetak.png">';
                                $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_GAGAL");
                                $arrEvaluasiHarga[$i] = 0;
                              }
                            }

                            echo $status_harga.'<br><small>'.$keterangan_harga.'</small>';
                          ?>
                          </td>
                          </tr>
                        <?php
                          $no++;
                        } // end of while
                      } else {
                        echo  '<tr><td colspan="4">Peringkat belum di tetapkan</td>';
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <?php
          if ($this->USER_LOGIN_ID != $reqUserLoginId) {
           ?>
          <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_DANGER ?>"> <?= BTN_KEMBALI ?></a>
        <?php } ?>
      <?php
      } ?>
        </div>
      </div>

    </div>
  </div>
</div>
