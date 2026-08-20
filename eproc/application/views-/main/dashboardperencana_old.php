<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();

$this->load->model("Paketperencanadash");
$this->load->model("PermohonanPaket");
$this->load->model("Paket");
$unitkerja  = $this->UNIT_KERJA_ID;

$permohonan_paket = new PermohonanPaket();
$permohonan_paket_sum = new PermohonanPaket();
$permohonan_paket_usulan = new PermohonanPaket();
$permohonan_paket_usulan_sum = new PermohonanPaket();
$permohonan_paket_input = new PermohonanPaket();
$permohonan_paket_input_sum = new PermohonanPaket();
$permohonan_paket_diproses = new PermohonanPaket();
$permohonan_paket_diproses_sum = new PermohonanPaket();
$permohonan_paket_rup = new PermohonanPaket();
$permohonan_paket_rup_sum = new PermohonanPaket();
$permohonan_paket_belum_diproses = new PermohonanPaket();
$permohonan_paket_belum_diproses_sum = new PermohonanPaket();
$paket = new Paket();

/*
1:Tender, 
3:Tender Terbatas, 
7:Tender Cepat, 
10:Tender Kualifikasi, 
2:Pengadaan langsung, 
5:Penunjukan Langsung, 
6:e-Purchasing, 
9:Pembelian Langsung Offline, 
11:Penunjukan Langsung Khusus
*/

$getTahun = isset($_GET['tahun']) ? $_GET['tahun'] : 'all';


// $getTahun = $_GET['tahun'];
if ($getTahun != 'all'){
    $tahun = 'Tahun '.$getTahun;

    $permohonan_paket->selectByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun));
    $countTotalPermohonan = $permohonan_paket->countRow();
    $permohonan_paket_sum->sumHpsByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun));
    $permohonan_paket_sum->firstRow();
    $sumHpsTotal = $permohonan_paket_sum->getField('TOTAL_HPS');

    $permohonan_paket_usulan->selectByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun),-1,-1," AND ( G.APPROVAL != '1' OR G.APPROVAL IS NULL )  ");
    $countTotalPermohonanUsulan = $permohonan_paket_usulan->countRow();
    $permohonan_paket_usulan_sum->sumHpsByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun),-1,-1," AND ( G.APPROVAL != '1' OR G.APPROVAL IS NULL )  ");
    $permohonan_paket_usulan_sum->firstRow();
    $sumHpsTotalUsulan = $permohonan_paket_usulan_sum->getField('TOTAL_HPS');

     $permohonan_paket_rup->selectByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun),-1,-1," AND G.APPROVAL='1'");
    $countTotalPermohonanRUP = $permohonan_paket_rup->countRow();
    $permohonan_paket_rup_sum->sumHpsByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun),-1,-1," AND G.APPROVAL='1'");
    $permohonan_paket_rup_sum->firstRow();
    $sumHpsTotalRUP = $permohonan_paket_rup_sum->getField('TOTAL_HPS');


    // $permohonan_paket_input->selectByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun),-1,-1," AND A.POSTING IS NULL ");
    // $countTotalPermohonanInput = $permohonan_paket_input->countRow();
    // $permohonan_paket_input_sum->sumHpsByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun),-1,-1," AND A.POSTING IS NULL ");
    // $permohonan_paket_input_sum->firstRow();
    // $sumHpsInput = $permohonan_paket_input_sum->getField('TOTAL_HPS');

    // $permohonan_paket_diproses->selectByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun),-1,-1," AND D.PAKET_ID IS NOT NULL AND A.POSTING IS NOT NULL ");
    // $countTotalPermohonanDiproses = $permohonan_paket_diproses->countRow();
    // $permohonan_paket_diproses_sum->sumHpsByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun),-1,-1," AND D.PAKET_ID IS NOT NULL AND A.POSTING IS NOT NULL ");
    // $permohonan_paket_diproses_sum->firstRow();
    // $sumHpsProses = $permohonan_paket_diproses_sum->getField('TOTAL_HPS');

    // $permohonan_paket_belum_diproses->selectByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun),-1,-1," AND D.PAKET_ID IS NULL AND A.POSTING IS NOT NULL ");
    // $countTotalPermohonanBelumDiproses = $permohonan_paket_belum_diproses->countRow();
    // $permohonan_paket_belum_diproses_sum->sumHpsByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja, "A.TAHUN_ANGGARAN" => $getTahun),-1,-1," AND D.PAKET_ID IS NULL AND A.POSTING IS NOT NULL ");
    // $permohonan_paket_belum_diproses_sum->firstRow();
    // $sumHpsBelumProses = $permohonan_paket_belum_diproses_sum->getField('TOTAL_HPS');

    $paket->getDashboardBar2Detail($getTahun,$this->USER_LOGIN_ID,$unitkerja);

} else {
  $tahun = '';

    $permohonan_paket->selectByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja));
    $countTotalPermohonan = $permohonan_paket->countRow();
    $permohonan_paket_sum->sumHpsByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja));
    $permohonan_paket_sum->firstRow();
    $sumHpsTotal = $permohonan_paket_sum->getField('TOTAL_HPS');

    $permohonan_paket_usulan->selectByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja),-1,-1," AND ( G.APPROVAL != '1' OR G.APPROVAL IS NULL )  ");
    $countTotalPermohonanUsulan = $permohonan_paket_usulan->countRow();
    $permohonan_paket_usulan_sum->sumHpsByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja),-1,-1," AND ( G.APPROVAL != '1' OR G.APPROVAL IS NULL )  ");
    $permohonan_paket_usulan_sum->firstRow();
    $sumHpsTotalUsulan = $permohonan_paket_usulan_sum->getField('TOTAL_HPS');

    $permohonan_paket_rup->selectByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja),-1,-1," AND G.APPROVAL='1'");
    $countTotalPermohonanRUP = $permohonan_paket_rup->countRow();
    $permohonan_paket_rup_sum->sumHpsByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja),-1,-1," AND G.APPROVAL='1'");
    $permohonan_paket_rup_sum->firstRow();
    $sumHpsTotalRUP = $permohonan_paket_rup_sum->getField('TOTAL_HPS');

    // $permohonan_paket_input->selectByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja),-1,-1," AND A.POSTING IS NULL ");
    // $countTotalPermohonanInput = $permohonan_paket_input->countRow();
    // $permohonan_paket_input_sum->sumHpsByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja),-1,-1," AND A.POSTING IS NULL ");
    // $permohonan_paket_input_sum->firstRow();
    // $sumHpsInput = $permohonan_paket_input_sum->getField('TOTAL_HPS');

    // $permohonan_paket_diproses->selectByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja),-1,-1," AND D.PAKET_ID IS NOT NULL AND A.POSTING IS NOT NULL ");
    // $countTotalPermohonanDiproses = $permohonan_paket_diproses->countRow();
    // $permohonan_paket_diproses_sum->sumHpsByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja),-1,-1," AND D.PAKET_ID IS NOT NULL AND A.POSTING IS NOT NULL ");
    // $permohonan_paket_diproses_sum->firstRow();
    // $sumHpsProses = $permohonan_paket_diproses_sum->getField('TOTAL_HPS');

    // $permohonan_paket_belum_diproses->selectByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja),-1,-1," AND D.PAKET_ID IS NULL AND A.POSTING IS NOT NULL ");
    // $countTotalPermohonanBelumDiproses = $permohonan_paket_belum_diproses->countRow();
    // $permohonan_paket_belum_diproses_sum->sumHpsByParams(array("A.USER_LOGIN_ID" => $this->USER_LOGIN_ID, "A.UNIT_KERJA_ID" => $unitkerja),-1,-1," AND D.PAKET_ID IS NULL AND A.POSTING IS NOT NULL ");
    // $permohonan_paket_belum_diproses_sum->firstRow();
    // $sumHpsBelumProses = $permohonan_paket_belum_diproses_sum->getField('TOTAL_HPS');

    $paket->getDashboardBar2Detail($getTahun,$this->USER_LOGIN_ID,$unitkerja);

}
// End Laporan Berdasarkan Metode Pengadaan
// echo "<pre>"; print_r($pieValue); die();
?>
 <script src="https://unpkg.com/gauge-chart@latest/dist/bundle.js"></script>
<script src="<?=base_url()?>assets/new/vendors/js/charts/chart.min.js"></script>

<script type="text/javascript">

function getDashboardDetail(a,b) {
  var bulan   = a;
  var metode  = b;
  var tahun   = $('#tahun').val();
  var url = 'main/loadUrl/main/dashboard_detail2/?metode='+metode+'&bulan='+bulan+'&tahun='+tahun+'&uid='+<?= $this->USER_LOGIN_ID ?>+'&jenis=panitia';
  openAdd(url);
  // alert(a+'-'+tahun+'-'+b);
}

</script>

<style type="text/css">
.chart-content {
  padding: 5px;
  /*background-color: #f9f9f9;*/
  /*width: 700px;*/
  margin: 10px;
  /*box-shadow: 0px 0px 2px #ccc;*/
}

.chart-legend{
  font-size: 0.8em;
  li {
    list-style: none;
    span {
      display: inline-block;
      width: 8px;
      height: 8px;
      margin-right: 5px;
    }
  }
}
</style>

<div class="row">
  <div class="form-group col-md-12">
    <!-- <label>Pilih Tahun</label> -->
    <select class="form-control" id="setyear" onChange="return window.location = $(this).val()">
      <?php
      $selected = '';
      $url = base_url('main/index/dashboardperencana?tahun=');
      $kurangdari = date('Y') - 5;
            echo '<option value="'.$url.'all">-- Pilih Tahun --</option>';
      for ($i= date('Y'); $i > $kurangdari   ; $i--) {
           // if ($i == date('Y') || $i == $getTahun) {
           if ($i == $getTahun) {
            $selected = 'selected';
           } else {
            $selected = '';
           }
            echo '<option value="'.$url.$i.'" '.$selected.'>'.$i.'</option>';
      }
      ?>
    </select>
  </div>
</div>

<div class="row">
  <input type="hidden" id="tahun" value="<?=$getTahun?>">
    <input type="hidden" id="metode" value="">
    <input type="hidden" id="bulan" value="">

  <div class="col-md-4">
    <!-- <div class="card"> -->
    <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">
        <div class="card-content">
            <div class="card-body" style="padding: .7em">
                <div class="media">
                    <div class="media-body text-left">
                        <h3 class="orange"><?= $countTotalPermohonan ?></h3>
                        <span>Total Planning</span>
                    </div>
                    <div class="media-right media-middle">
                        <i class="ft-package success font-large-1 float-right"></i>
                    </div>
                </div>
                <div class="mb-0">
                  <small><?= LABEL_HPS ?></small> <?= '<small style="font-weight:bold">Rp. '.number_format((float)$sumHpsTotal, 2, ',', '.').'</small>'; ?>
                </div>
            </div>
        </div>
    </div>
  </div>

  <div class="col-md-4">
    <!-- <div class="card"> -->
    <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">
        <div class="card-content">
            <div class="card-body" style="padding: .7em">
                <div class="media">
                    <div class="media-body text-left">
                        <h3 class="orange"><?= $countTotalPermohonanUsulan ?></h3>
                        <span>Usulan Kebutuhan</span>
                    </div>
                    <div class="media-right media-middle">
                        <i class="fa fa-pencil success font-large-1 float-right"></i>
                    </div>
                </div>
                <div class="mb-0">
                  <small><?= LABEL_HPS ?></small> <?= '<small style="font-weight:bold">Rp. '.number_format((float)$sumHpsTotalUsulan, 2, ',', '.').'</small>'; ?>
                </div>
            </div>
        </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">
        <div class="card-content">
            <div class="card-body" style="padding: .7em">
                <div class="media">
                    <div class="media-body text-left">
                        <h3 class="orange"><?= $countTotalPermohonanRUP ?></h3>
                        <span>Rencana Pengadaan</span>
                    </div>
                    <div class="media-right media-middle">
                        <i class="ft-check-circle orange font-large-1 float-right"></i>
                    </div>
                </div>
                <div class="mb-0">
                  <small><?= LABEL_HPS ?></small> <?= '<small style="font-weight:bold">Rp. '.number_format((float)$sumHpsTotalRUP, 2, ',', '.').'</small>'; ?>
                </div>
            </div>
        </div>
    </div>
  </div> 

  <link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/media/css/jquery.dataTables.css">
  <link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/extensions/Responsive/css/dataTables.responsive.css">
  <link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/syntax/shCore.css">
  <link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/demo.css">
  <script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/media/js/jquery.dataTables.js"></script>
  <script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/extensions/Responsive/js/dataTables.responsive.js"></script>
  <script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/examples/resources/syntax/shCore.js"></script>
  <script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/examples/resources/demo.js"></script>
  <script type="text/javascript" language="javascript" class="init">
    $(document).ready(function() {
      $('#prosesDash').DataTable({
        "iDisplayLength": 5,
        // "aaSorting": [[0, 'desc']],
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      });
    });
  </script>
  <style>
  #prosesDash_length { display: none;}
  </style>

  <div class="col-md-12">
    <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">
      <div class="card-content">
        <div class="card-body" style="padding: .7em">
          <span class="text-center">
            <h5 class="mt-2"><b>Laporan Berdasarkan Realisasi Paket</b></h5>
          </span>
          <?php
          $no=1;
          $total = 0; ?>
          <table id="prosesDash" class="border-double table mb-0 table-bordered" style="width: 100%">
            <thead>
              <tr>
                <th style="text-align: center;width: 5%">No</th>
                <th>Nama Paket</th>
                <th><?= LABEL_HPS ?> <small style="font-weight: bold">Rp.</small></th>
                <th width="10px" align="center">Realisasi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $total = 0;
              $totalPaketRencana = 0;
              $totalPaketRealisasi = 0;
              $totalSisa = 0;
              while($paket->nextRow()) {
                $total += $paket->getField('NILAI');
                $totalPaketRencana++;
              ?>
                <tr>
                  <td align="center"><?= $no ?></td>
                  <td>
                    <?php 
                    if ($paket->getField('PAKET_ID')) { ?>
                    <a href="<?=  base_url('main/index/paket_detil/?reqId='.$paket->getField('PAKET_ID').'') ?>" target="_blank">
                    <?php 
                    } else { echo '<a>'; } ?>
                    <?=  $paket->getField('NAMA') ?></a>
                  </td> 
                  <td><?= number_format((float)$paket->getField('NILAI'), 2, ',', '.') ?></td>
                  <?php
                  if ($paket->getField('TOTAL_REALISASI') == '1') {
                    $totalPaketRealisasi++;
                    echo             '<td align="center"><img src="images/centang.png"></td>';
                  } else {
                    echo             '<td align="center"><img src="images/uncentang.png"></td>';
                  }
                  ?>
                </tr>
              <?php
              $no++;
              }
                $totalSisa= $totalPaketRencana-$totalPaketRealisasi;
              ?>
              <tfoot>
                <tr>
                  <td colspan="2">
                    <b>TOTAL <?= LABEL_HPS ?> <small style="font-weight: bold">Rp.</small></b></td> <td><?= number_format((float)$total, 2, ',', '.') ?>
                  </td>
                  <td></td>
                </tr>
                <tr>
                  <td colspan="2"><b>TOTAL Paket Rencana</b></td> <td> <?= $totalPaketRencana ?></td><td></td>
                </tr>
                <tr>
                  <td colspan="2"><b>TOTAL Paket Realisasi</b></td> <td><?= $totalPaketRealisasi ?></td><td></td>
                </tr>
                <tr>
                  <td colspan="2"><b>TOTAL Sisa Paket Rencana</b></td> <td><?= $totalSisa ?></td><td></td>
                </tr>
              </tfoot>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
