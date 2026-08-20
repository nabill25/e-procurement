<?php
// $this->libsession->cekSession('free');
$reqId = $this->input->get("reqId");
// $this->libsession->cekSession($reqId);

// if($this->USER_TYPE_ID != "6")
//     redirect("app");

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("Paket");
$this->load->model("PaketTahap");
$this->load->model("PaketRekanan");
$this->load->model("PaketDokumen");
$this->load->model("Paketpemenang");
$this->load->model(array("RekananEvaluasiTeknisTawar","RekananEvaluasiAdminTawar","RekananEvaluasiHargaTawar"));
$this->load->model("MatrixEvaluasi");
$this->load->model("RekananPaketPenawaran");
$this->load->model("PaketPenawaran");
$this->load->model("PermohonanPaket");
$this->load->model("PaketNegoisasi");

$paket = new Paket();
$paket_rekanan = new PaketRekanan();
$paket_rekanan_nilai = new PaketRekanan();
$matrix_evaluasi = new MatrixEvaluasi();
$paket_penawaran = new PaketPenawaran();
$paket_dokumen = new PaketDokumen();
$getpaket_pemenang = new Paketpemenang();
$getpaket_pemenang_array = new Paketpemenang();
$getpaket_pemenang_c = new Paketpemenang();
$paket_negosiasi = new PaketNegoisasi();


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
$reqPermohonanId = $paketInfo->permohonan_paket_id;
$reqMultiPemenang = $paketInfo->multi_pemenang; // Kontrak Payung
$reqMetodeLelang = $paketInfo->metode_lelang_id;
$bidding = $paketInfo->bidding;
$alasan_batal = $paketInfo->alasan_batal;
$alasan_gagal = $paketInfo->alasan_gagal;
$uuid = $paketInfo->uuid;
// echo $alasan_batal.'--'.$alasan_gagal; die;
// echo $reqPermohonanId.'---';
if ($reqPermohonanId) {
  $permohonan_paket = new PermohonanPaket();
  // $permohonan_paket->selectByParams(array("A.PERMOHONAN_PAKET_ID" => $reqPermohonanId, "A.UNIT_KERJA_ID" => $this->UNIT_KERJA_ID));
  $permohonan_paket->selectByParams(array("A.PERMOHONAN_PAKET_ID" => $reqPermohonanId));
  $permohonan_paket->firstRow();
  $reqPL = $permohonan_paket->getField("PENGADAANLANGSUNG");
}

$paket->selectByParamsMonitoring(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
$paket->firstRow();

// $paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 ");
$paket_rekanan->selectByParams3(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 ");
while($paket_rekanan->nextRow())
{

  if($paket_rekanan->getField("PAKET_PENAWARAN_ID")==''){
    continue;
  }

  // ambil nilai koreksi
  $paket_penawaran->selectByParamsRekananPaketPenawaran(array('B.PAKET_REKANAN_ID' => $paket_rekanan->getField("PAKET_REKANAN_ID")), -1, -1, " AND 1=1");
  $paket_penawaran->firstRow();

  // ambil negosiasi
  $paket_negosiasi->selectByParams(array("A.PAKET_PENAWARAN_ID" => $paket_rekanan->getField("PAKET_PENAWARAN_ID")));
  $paket_negosiasi->firstRow();
  $jumlahNegosiasi[] =  $paket_negosiasi->getField("TOTAL");
  $arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
  $arrRekanan[] = $paket_rekanan->getField("REKANAN");
  $arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
  $arrPaketRekananNilaiAuction[] = $paket_rekanan->getField("NILAI_PENAWARAN");
  $arrPaketRekananNilaiSebelumnya[] = $paket_rekanan->getField("UNIT_PRICE");
  $arrPaketRekananNilai[] = $paket_penawaran->getField("JUMLAH_KOREKSI");
  $arrPaketRekananLulus[] = $paket_rekanan->getField("LULUS_PENAWARAN");
}

if (is_array($arrRekanan)) {
  $arrRekananId = $arrRekananId;
  $arrRekanan = $arrRekanan;
  $arrPaketRekananId = $arrPaketRekananId;
  $arrPaketRekananNilaiSebelumnya = $arrPaketRekananNilaiSebelumnya;
  $arrPaketRekananNilai = $arrPaketRekananNilai;
  $arrPaketRekananLulus = $arrPaketRekananLulus;
} else {
  $arrRekananId = array();
  $arrRekanan = array();
  $arrPaketRekananId = array();
  $arrPaketRekananNilaiSebelumnya = array();
  $arrPaketRekananNilai = array();
  $arrPaketRekananLulus = array();
}


// Pemenang
$getpaket_pemenang->selectByParams(array("A.PAKET_ID" => $reqId, 'A.PUBLISH' => '1'), -1, -1);
$getpaket_pemenang_array->selectByParams(array("A.PAKET_ID" => $reqId, 'A.PUBLISH' => '1'), -1, -1);
$getpaket_pemenang_count = $getpaket_pemenang_c->getCountByParams(array("A.PAKET_ID" => $reqId, 'A.PUBLISH' => '1'));

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


$paket_tahap = new PaketTahap();
$paket_tahap_metode = new PaketTahap();

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);

$arrPengumumanPemenang          = PENGUMUMAN_PEMENANG;

$aktif_pengumuman = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrPengumumanPemenang[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
$aktif_pengumuman2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrPengumumanPemenang[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));

?>
<script>
function setEvaluasiPenawaran(ctrl, ctrl_change)
{
  if (ctrl.checked == true) {
    document.getElementById(ctrl_change).value = 1;
  } else {
    document.getElementById(ctrl_change).value = 0;
  }
}
</script>

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
        <h4 class="card-title text-white">Pengumuman Pemenang</h4>
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

            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <tbody>
                  <tr>
                    <td colspan="4">
                      <B><?=$paket->getField("NAMA")?></B>
                    </td>
                  </tr>
                  <tr>
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-calendar"></i> <b>Tahun Anggaran</b></small> <br>
                      <?=getYear($paket->getField("TANGGAL_TAHAP"))?>
                    </td>
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-map-marker"></i> <b>Lokasi Pekerjaan</b></small> <br>
                      <?=$paket->getField("LOKASI")?>
                    </td>
                  </tr>
                  <tr>
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-inbox"></i> <b>Jenis Pengadaan</b></small> <br>
                      <?=$paket->getField("PAKET_JENIS")?>
                    </td>
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-tag"></i> <b>Metode Pengadaan</b></small> <br>
                      <?=$paket->getField("METODE_LELANG")?>
                      <?php 
                      if($paket->getField("PAKET_METODE_LELANG_ID") == '1') { 
                        if ($paket->getField("MULTI_PEMENANG") == '1') {
                          echo '&nbsp;<span style="font-size:11px">( Pemenang lebih dari satu )</span>';
                        }
                      }  ?>
                    </td>
                  </tr>
                  <tr>
                    <!-- <td width="25%" colspan="2">
                      <small><i class="fa fa-clipboard"></i> Metode Kualifikasi</small> <br>
                      <?=$paket->getField("METODE_KUALIFIKASI")?>
                    </td> -->
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-folder-open"></i> <b>Metode Penyampaian Penawaran</b></small> <br>
                      <?=$paket->getField("SISTEM_SAMPUL")?> File
                    </td>
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-exchange"></i> <b>Metode Evaluasi</b></small> <br>
                      <?=$paket->getField("METODE_EVALUASI")?>
                    </td>
                  </tr>
                  <tr>
                    <td width="25%" colspan="2">
                      <small><i class="fa fa-file-text"></i> <b>Kualifikasi Usaha</b></small> <br>
                      <?=$paket->getField("REKANAN_KUALIFIKASI")?>
                    </td>
                    <!-- <td width="25%" colspan="2"> -->
                      <!-- <small><i class="fa fa-clock-o"></i> <b>Sistem Negosiasi</b></small> <br> -->
                      <?php
                      // if ($paket->getField("BIDDING") == 1) {
                      //   echo 'e-Reverse Auction '.$paket->getField("BIDDING_MENIT").' menit';
                      // } else {
                      //   echo "Negosiasi";
                      // }
                      ?>
                    <!-- </td> -->
                  </tr>
                  <?php
                  // if($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 7 ) // PANITIA & EKSEKUTIF
                  // {
                  if ($reqMetodePengadaan == '1') // ditampilkan hanya untuk Tender
                  {
                  ?>
                  <tr>
                    <td width="25%" colspan="4">
                      <small><i class="fa fa-money"></i> <b>Perkiraan Nilai Pekerjaan</b></small> <br>
                      <?=$paket->getField("NILAI_MATA_UANG")?> <?=currencyToPage($paket->getField("NILAI"))?>
                    </td>
                    </td>
                  </tr>
                  <?php
                  } else {
                    if ($this->USER_TYPE_ID != '6') { // bukan untuk penyedia
                  ?>
                  <tr>
                    <td width="25%" colspan="4">
                      <small><i class="fa fa-money"></i> Perkiraan Nilai Pekerjaan</small> <br>
                      <?=$paket->getField("NILAI_MATA_UANG")?> <?=currencyToPage($paket->getField("NILAI"))?>
                    </td>
                    </td>
                  </tr>
                  <?php
                    }
                  }
                  // }
                  ?>
                  <tr>
                    <td width="25%" colspan="4">
                      <small><i class="fa fa-suitcase"></i> <b>Bidang / Sub Bidang</b></small><br>
                      <?php if(trim($paket->getField("BIDANG_USAHA")) == "()")
                          echo "-";
                         else
                          echo str_replace(", (",", <br/> (", $paket->getField("BIDANG_USAHA")); ?>
                    </td>
                  </tr>
                  <tr>
                    <td width="25%" colspan="4">
                      <small><i class="fa fa-th-list"></i> <b>Persyaratan Peserta</b></small><br>
                      <?=$paket->getField("URAIAN")?>
                    </td>
                  </tr>
                  <?php
                    // echo $reqPermohonanId.'-'.$reqPL.'-'.$reqMetodePengadaan;
                    if (($reqPL == '0' && $reqMetodePengadaan == '2') || $reqMetodePengadaan != '2') { // Pengadaan langsung <= 300jt
                   ?>
                  <!-- <tr>
                    <td width="25%" colspan="4">
                      <div class="alert alert-info">PANITIA</div>
                      <table class="table table-hover">
                        <tr>
                          <td width="15%"><small><i class="fa fa-building-o"></i> Unit Kerja </small></td>
                          <td width="85%">: <?=$paket->getField("UNIT_KERJA")?></td>
                        </tr>
                        <tr>
                          <td><small><i class="fa fa-envelope-o"></i> Email </small></td>
                          <td>: <?=$paket->getField("EMAIL")?></td>
                        </tr>
                        <tr>
                          <td><small><i class="fa fa-phone"></i> Telepon </small></td>
                          <td>: <?=$paket->getField("TELEPON")?></td>
                        </tr>
                        <tr>
                          <td><small><i class="fa fa-map-marker"></i> Alamat </small></td>
                          <td>: <?=$paket->getField("ALAMAT")?></td>
                        </tr>
                      </table>
                    </td>
                  </tr>  -->
                  <?php
                  } ?>
                </tbody>
              </table>

          <?php
          if($aktif_pengumuman > 0  || $aktif_pengumuman2 > 0)
          {
            $paket_rekanan_pemenang = new PaketRekanan();
            if ($bidding == '0' || $bidding == '') { // jika Sistem Negosiasi
              $paket_rekanan_pemenang->selectByParams3(array("A.PAKET_ID" => $reqId), -1, -1, " AND LULUS_PENDAFTARAN = 1 AND LULUS_KUALIFIKASI = 1 AND KIRIM_PENAWARAN = 1  ORDER BY A.NILAI_PENAWARAN ASC", $reqId);
            } else { // jika Sistem Negosiasi nya Bidding
              $paket_rekanan_pemenang->selectByParams4(array("A.PAKET_ID" => $reqId), -1, -1, " AND LULUS_PENDAFTARAN = 1 ORDER BY A.NILAI_PENAWARAN ASC", $reqId);
            }
            ?>
              <h2>Hasil Evaluasi</h2>

                <table class="table table-bordered">
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
                    // if ($paket_rekanan_pemenang->countRow() == 0) {
                    // if ($getpaket_pemenang_count == 0) {
                    if ($getpaket_pemenang_count == 0 && $alasan_batal == '' && $alasan_gagal == '') { 
                      echo '<td colspan="8">Pemenang Belum Ditetapkan</td>';
                    } else
                    {
                      $arrayPemenang = array();
                      $arrayEvaluasiRekananId = array();
                      while($getpaket_pemenang_array->nextRow())
                      {
                        $arrayPemenang[] = $getpaket_pemenang_array->getField("REKANAN_ID");
                      }

                    while($paket_rekanan_pemenang->nextRow())
                    {
                      if($paket_rekanan_pemenang->getField("PAKET_PENAWARAN_ID")==''){
                        continue;
                      }
                      $rekanan_evaluasi_admin = new RekananEvaluasiAdminTawar();
                      $rekanan_evaluasi_admin->selectMemenuhiSyarat($reqId, $paket_rekanan_pemenang->getField("PAKET_REKANAN_ID"));
                      $rekanan_evaluasi_admin->firstRow();

                      $rekanan_evaluasi_teknis = new RekananEvaluasiTeknisTawar();
                      $rekanan_evaluasi_teknis->selectMemenuhiSyarat($reqId, $paket_rekanan_pemenang->getField("PAKET_REKANAN_ID"));
                      $rekanan_evaluasi_teknis->firstRow();

                      $rekanan_evaluasi_harga = new RekananEvaluasiHargaTawar();
                      $rekanan_evaluasi_harga->selectMemenuhiSyarat2($reqId, $paket_rekanan_pemenang->getField("PAKET_REKANAN_ID"));
                      $rekanan_evaluasi_harga->firstRow();

                      $paket_negosiasi->selectByParams(array("A.PAKET_PENAWARAN_ID" => $paket_rekanan_pemenang->getField("PAKET_PENAWARAN_ID")));
                      $paket_negosiasi->firstRow();
                      $penawaranNegosiasi =  $paket_negosiasi->getField("UNIT_PRICE");
                      $jumlahNegosiasi =  $paket_negosiasi->getField("TOTAL");
                      $setujui =  $paket_negosiasi->getField("SETUJUI");

                      $arrayEvaluasiRekananId[] = $paket_rekanan_pemenang->getField("REKANAN_ID");

                    ?>
                    <tr>
                      <td><?=$no?></td>
                      <td><?=$paket_rekanan_pemenang->getField("REKANAN")?></td>
                      <?php
                      if ($reqMetodeLelang != '7') { // Selain Tender Cepat ?>
                      <td style="font-size:11px">
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
                      <td style="font-size:11px">
                      <?php
                        if($rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI") >= 1 && $rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI") >= 1)
                        {
                          $status_teknis = '<img src="images/centang.png">';
                          $arrEvaluasiTeknis[$i] = 1;
                          if ($reqMetodeEvaluasiId == '2' || $reqMetodeEvaluasiId == '10') {
                            $skor_teknis = 'Nilai Teknis <b>'.$rekanan_evaluasi_teknis->getField("NILAI_TEKNIS").'</b><br>'.$rekanan_evaluasi_teknis->getField("KETERANGAN_LULUS");
                            $skor_teknis_angka[$arrPaketRekananId[$i]] = $rekanan_evaluasi_teknis->getField("NILAI_TEKNIS");
                            $keterangan_teknis = $rekanan_evaluasi_teknis->getField("KETERANGAN_LULUS");
                            echo $status_teknis.'<br><small>'.$skor_teknis;
                          } else {
                            $keterangan_teknis = $rekanan_evaluasi_teknis->getField("KETERANGAN_LULUS");
                            echo $status_teknis.'<br><small>'.$keterangan_teknis.'</small>';
                          }
                          // $arrEvaluasiTeknis[$i] = 1;
                        }
                        else
                        {
                          $status_teknis = '<img src="images/uncentang.png">';
                          $skor_teknis = 'Nilai Teknis <b>'.$rekanan_evaluasi_teknis->getField("NILAI_TEKNIS").'</b>';
                          $keterangan_teknis = $rekanan_evaluasi_teknis->getField("KETERANGAN_GAGAL");
                          $arrEvaluasiTeknis[$i] = 0;
                            echo $status_teknis.'<br><small>'.$keterangan_teknis.'</small>';
                        } ?>
                      </td>
                      <?php
                      } ?>
                      <td style="font-size:11px">
                      <?php
                        if ($reqMetodePengadaan != 7) {
                          if($rekanan_evaluasi_admin->getField("JUMLAH_DILENGKAPI") >= 1 && $rekanan_evaluasi_teknis->getField("JUMLAH_DILENGKAPI") >= 1 && $rekanan_evaluasi_harga->getField("JUMLAH_DILENGKAPI") >= 1)
                          {
                            $status_harga = '<img src="images/centang.png">';
                            $arrEvaluasiHarga[$i] = 1;
                            if ($reqMetodeEvaluasiId == '2') {
                              $skor_harga = 'Nilai Harga <b>'.$rekanan_evaluasi_harga->getField("NILAI_HARGA").'</b><br>'.$rekanan_evaluasi_harga->getField("KETERANGAN_LULUS");
                              $skor_harga_angka[$arrPaketRekananId[$i]] = $rekanan_evaluasi_harga->getField("NILAI_HARGA");
                              $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_LULUS");
                              echo $status_harga.'<br><small>'.$skor_harga.'</small>';

                            } else {
                              $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_LULUS");
                              echo $status_harga.'<br><small>'.$keterangan_harga.'</small>';
                            }

                          }
                          else
                          {
                            $status_harga = '<img src="images/uncentang.png">';
                            $skor_harga = 'Nilai Harga <b>'.$rekanan_evaluasi_harga->getField("NILAI_HARGA").'</b>';
                            $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_GAGAL");
                            $arrEvaluasiHarga[$i] = 0;
                            echo $status_harga.'<br><small>'.$keterangan_harga.'</small>';
                          }
                        } else
                        {
                          if($rekanan_evaluasi_harga->getField("JUMLAH_DILENGKAPI") >= 1)
                          {
                            $status_harga = '<img src="images/centang.png">';
                            $arrEvaluasiHarga[$i] = 1;
                            if ($reqMetodeEvaluasiId == '2') {
                              $skor_harga = 'Nilai Harga <b>'.$rekanan_evaluasi_harga->getField("NILAI_HARGA").'</b>';
                              $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_LULUS");
                              echo $status_harga.'<br><small>'.$skor_harga.'<br>'.$keterangan_harga.'</small>';

                            } else {
                              $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_LULUS");
                              echo $status_harga.'<br><small>'.$keterangan_harga.'</small>';
                            }
                          }
                          else
                          {
                            $status_harga = '<img src="images/uncentang.png">';
                            $skor_harga = 'Nilai Harga <b>'.$rekanan_evaluasi_harga->getField("NILAI_HARGA").'</b>';
                            $keterangan_harga = $rekanan_evaluasi_harga->getField("KETERANGAN_GAGAL");
                            $arrEvaluasiHarga[$i] = 0;
                            echo $status_harga.'<br><small>'.$keterangan_harga.'</small>';
                          }
                        }
                        ?>
                      </td>

                      <?php 
                      if ($reqMetodeEvaluasiId == '2') {
                        $totalKombinasi = $rekanan_evaluasi_harga->getField("NILAI_HARGA") + $rekanan_evaluasi_teknis->getField("NILAI_TEKNIS");
                        echo '<td style="text-align:center">'.$totalKombinasi.'</td>';
                      } ?>

                      <td>
                        <?php 
                        // only show to winner
                        if (in_array($paket_rekanan_pemenang->getField("REKANAN_ID"),$arrayPemenang)) {
                          echo str_replace(",00","",numberToIna($paket_rekanan_pemenang->getField("UNIT_PRICE")));
                        }
                        ?>
                          
                      </td>
                      <td>
                        <?php 
                        // only show to winner
                        if (in_array($paket_rekanan_pemenang->getField("REKANAN_ID"),$arrayPemenang)) {
                          echo str_replace(",00","",numberToIna($paket_rekanan_pemenang->getField("JUMLAH_KOREKSI")));
                        }
                        ?>
                          
                      </td>

                      <?php
                      if ($bidding == '0' || $bidding == '') { // jika Sistem Negosiasi  ?>
                      <td>
                        <?php
                        if ($reqRekananIdPemenang == $paket_rekanan_pemenang->getField("REKANAN_ID")) {
                          // only show to winner
                          if (in_array($paket_rekanan_pemenang->getField("REKANAN_ID"),$arrayPemenang)) {
                            echo str_replace(",00","",numberToIna($jumlahNegosiasi));
                          }
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
                        <td>
                          <?php 
                          // only show to winner
                          if (in_array($paket_rekanan_pemenang->getField("REKANAN_ID"),$arrayPemenang)) {
                            echo str_replace(",00","",numberToIna($paket_rekanan_pemenang->getField("NILAI_PENAWARAN")));
                          }
                          ?>
                        </td>
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

              <h2>Pemenang <?php if ($reqMultiPemenang == '0') { } else { echo "(Multi Winner)"; } ?></h2>
              <table class="table table-bordered table-hover">
                <tr class="judul-kolom">
                  <th width="<?php if ($reqMultiPemenang == '0') { echo "20%"; } else { echo "10%"; } ?>" class="text-left"><?php if ($reqMultiPemenang == '0') { echo "Urutan"; } else { echo "Pemenang"; } ?></th>
                  <th style="text-align: left;"><?php if ($reqMultiPemenang == '0') { echo "Nama Peserta"; } else { echo "Nama Pemenang"; } ?></th>
                  <!-- <th style="width: 15%">Tanggal Penetapan</th> -->
                  <!-- <th>Keterangan</th> -->
                </tr>
              <?php
              if ($getpaket_pemenang_count == 0) {
                echo '<tr><td colspan="2">Pemenang Belum Ditetapkan</td></tr>';
              } else
              {
                $paket_dokumen = new PaketDokumen();
                $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "JENIS_DOKUMEN" => "PENETAPAN_PEMENANG"));
                $paket_dokumen->firstRow();
                $dokumen = $paket_dokumen->getField("PATH_FILE");
                $no=1;
                while($getpaket_pemenang->nextRow())
                { ?>
                  <tr>
                    <td style="width:5%; <?php if ($reqMultiPemenang == '0') {} else { echo "text-align: center"; } ?>">
                      <?= $getpaket_pemenang->getField("PERINGKAT")?>
                        <?php 
                        if ($reqMultiPemenang == '0') {  
                          if ($no > 1) { 
                            echo " <small>( Pemenang Cadangan ".$cadangan." )</small>";
                          } else { 
                            echo " <small>( Pemenang )</small>";
                          }
                        } 
                        ?>
                    </td>
                    <td>
                      <?php
                      // if ($getpaket_pemenang->getField("REKANAN_ID") == $this->ID && $getpaket_pemenang->getField("PERINGKAT") == 1) { // hanya ID pemenang yang bisa download penetapan pemenang
                      if (in_array($this->ID,$arrayEvaluasiRekananId)) { // Peserta yang di evaluasi
                        if ($getpaket_pemenang->getField("PERINGKAT") == 1) { // hanya ID pemenang yang bisa download penetapan pemenang
                          echo $getpaket_pemenang->getField("NAMA");
                          // .'<br>';
                          if ($dokumen && file_exists('uploads/penawaran/'.$dokumen)) {
                            echo '<br><a href="uploads/penawaran/'.$dokumen.'" target="_blank" class="badge badge-danger" style="margin-top: 5px">Download File Penetapan Pemenang</a>';
                          }
                        } else
                        {
                          echo $getpaket_pemenang->getField("NAMA");
                        }
                      } else {
                        echo $getpaket_pemenang->getField("NAMA");
                      }
                      ?>
                    </td>
                    <!-- <td> <?= getFormattedDate($getpaket_pemenang->getField("TANGGAL_PENETAPAN")) ?></td>  -->
                    <!-- <td> <?= $getpaket_pemenang->getField("KETERANGAN") ?></td>  -->
                  </tr>
                <?php
                  $no++;
                  if ($no > 1) { $cadangan++; }
                }
              }?>
              </table>
          <?php
            // }
          } ?>

              <div class="form-actions">
                <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?= $uuid ?>" class="<?= CLASS_BTN_DANGER ?>"> <i class="fa fa-arrow-left"></i> Kembali </a>
              </div>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>
