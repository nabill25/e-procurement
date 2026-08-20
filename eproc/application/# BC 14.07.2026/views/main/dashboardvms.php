<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

include_once("functions/string.func.php");

$this->libsession->cekSession();

$this->load->model("Dashboardvms");
$this->load->model("UsersBase");
$this->load->model("Masterpengaturan");

$viewtotalrekanan = new Dashboardvms();
$viewtotalblacklist = new Dashboardvms();
$viewtotalblacklistHistory = new Dashboardvms();
$viewtotalkatalog = new Dashboardvms();
$viewtotalkatalogLaporan = new Dashboardvms();
$viewtotalPenilaian = new Dashboardvms();
$user_login = new Dashboardvms();
$master_pengaturan = new Masterpengaturan();
$countDataKirimBerkas = new Dashboardvms();

  $viewtotalrekanan->selectByParams('view_dash_total_rekanan',array());
  $viewtotalblacklist->selectByParams('view_dash_total_blacklist',array());
  $viewtotalblacklistHistory->selectBlacklistHistory(array());
  $master_pengaturan->selectByParamsDokExpired(array());
  $viewtotalkatalog->selectByParams('view_dash_total_katalog',array());
  $viewtotalkatalogLaporan->selectByParams('view_dash_total_katalog_laporan',array());
  $viewtotalPenilaian->selectByPenilaian(array());
  $user_login->selectByParams('view_dash_last_login_penyedia',array(),15,0);
  $countDataKirimBerkas->selectByParamsKirimBerkasRevisi();
  $countDataKirimBerkas->firstRow();


  // ---------------------------------- TOTAL PENYEDIA ------------------------------
  $status1_persen = 0;
  $status0_persen = 0;
  $status2_persen = 0;
  $status0_total_status = 0;
  $status1_total_status = 0;
  $status2_total_status = 0;
  while ($viewtotalrekanan->nextRow()) {
    switch ($viewtotalrekanan->getField("user_status")) {
      case '0': // Belom Kirim Berkas
        $status0_status = $viewtotalrekanan->getField('status');
        $status0_total_status = $viewtotalrekanan->getField('total_status');
        $status0_total = $viewtotalrekanan->getField('total');
        $status0_persen = round($viewtotalrekanan->getField('persen'),2);
        break;

      case '2': // Sudah Kirim Berkas
        $status2_status = $viewtotalrekanan->getField('status');
        $status2_total_status = $viewtotalrekanan->getField('total_status');
        $status2_total = $viewtotalrekanan->getField('total');
        $status2_persen = round($viewtotalrekanan->getField('persen'),2);
        break;

      default: // Terverifikasi
        $status1_status = $viewtotalrekanan->getField('status');
        $status1_total_status = $viewtotalrekanan->getField('total_status');
        $status1_total = $viewtotalrekanan->getField('total');
        $status1_persen = round($viewtotalrekanan->getField('persen'),2);
        break;
    }
  }
  // cari total penyedia
  if ($status1_persen) {
    $totalPenyedia = $status1_total;
  } else if ($status0_persen) {
    $totalPenyedia = $status0_total;
  } else {
    $totalPenyedia = $status2_total;
  }

  $viewtotalblacklist->firstRow();
  $totalBlacklist = $viewtotalblacklist->getField('total');
  $totalBlacklistHistory = $viewtotalblacklistHistory->countRow();

  $totalExpired = $master_pengaturan->countRow();

  // ---------------------------------- TOTAL KATALOG ------------------------------
  $katalog0_persen = 0;
  $katalog1_persen = 0;
  while ($viewtotalkatalog->nextRow()) {
    switch ($viewtotalkatalog->getField("status")) {
      case '0': // Belom Terverifikasi
        $katalog0_total_status = $viewtotalkatalog->getField('total_status');
        $katalog0_total_katalog = $viewtotalkatalog->getField('total_katalog');
        $katalog0_persen = round($viewtotalkatalog->getField('persen'),1);
        break;

      default: // Terverifikasi
        $katalog1_total_status = $viewtotalkatalog->getField('total_status');
        $katalog1_total_katalog = $viewtotalkatalog->getField('total_katalog');
        $katalog1_persen = round($viewtotalkatalog->getField('persen'),1);
        break;
    }
  }

  // ---------------------------------- TOTAL PENILAIAN ------------------------------
  while ($viewtotalPenilaian->nextRow()) {
    switch ($viewtotalPenilaian->getField('star')) {
      case '1':
        $rating1 = $viewtotalPenilaian->getField('star');
        $rating1_total = $viewtotalPenilaian->getField('total');
        $rating1_total_persen = round($viewtotalPenilaian->getField('total')/$totalPenyedia*(100),1);
        break;
      case '2':
        $rating2 = $viewtotalPenilaian->getField('star');
        $rating2_total = $viewtotalPenilaian->getField('total');
        $rating2_total_persen = round($viewtotalPenilaian->getField('total')/$totalPenyedia*(100),1);
        break;
      case '3':
        $rating3 = $viewtotalPenilaian->getField('star');
        $rating3_total = $viewtotalPenilaian->getField('total');
        $rating3_total_persen = round($viewtotalPenilaian->getField('total')/$totalPenyedia*(100),1);
        break;
      case '4':
        $rating4 = $viewtotalPenilaian->getField('star');
        $rating4_total = $viewtotalPenilaian->getField('total');
        $rating4_total_persen = round($viewtotalPenilaian->getField('total')/$totalPenyedia*(100),1);
        break;
      case '5':
        $rating5 = $viewtotalPenilaian->getField('star');
        $rating5_total = $viewtotalPenilaian->getField('total');
        $rating5_total_persen = round($viewtotalPenilaian->getField('total')/$totalPenyedia*(100),1);
        break;
    }
  }

?>

<!-- <script src="https://unpkg.com/gauge-chart@latest/dist/bundle.js"></script> -->
<!-- <script src="<?=base_url()?>assets/new/vendors/js/vendors.min.js"></script> -->
<!-- <script src="<?=base_url()?>assets/new/vendors/js/charts/chart.min.js"></script> -->
<script src="<?=base_url()?>assets/new/vendors/js/extensions/jquery.raty.js"></script>
<script src="<?=base_url()?>assets/new/js/scripts/extensions/rating.js"></script>
<script src="<?=base_url()?>assets/new/vendors/js/extensions/jquery.knob.min.js"></script>
<script src="<?=base_url()?>assets/new/js/scripts/extensions/knob.js"></script>

<script type="text/javascript">

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
  .table th, .table td {
    padding: .2rem 2rem !important;
  }
</style>

<script>
function laporanDirect() {
  location.href = '<?= base_url('main/index/katalog_laporan') ?>';
}
</script>

<div class="row">
  <input type="hidden" id="tahun" value="<?=$getTahun?>">
    <input type="hidden" id="metode" value="">
    <input type="hidden" id="bulan" value="">

    <div class="col-xl-12 col-lg-12">
      <div class="row">
        <div class="col-xl-4 col-lg-6 col-md-12 animated zoomIn">
            <div class="card">
                <div class="card-content">
                    <div class="card-body text-center">
                        <div class="card-header mb-2" onclick="openAdd('main/loadUrl/main/dashboardvms_detail_penyedia_terdaftar?reqJenis=total')" style="cursor:pointer">
                            <span class="success darken-1">Total Penyedia Terdaftar</span>
                            <h3 class="font-large-2 grey darken-1 text-bold-200"><?= number_format($totalPenyedia,0, ",",".")?></h3>
                        </div>
                        <div class="card-content">
                          <input type="text" value="<?= $status1_persen ?>" class="knob hide-value responsive angle-offset" data-angleOffset="0" data-thickness=".15" data-linecap="round" data-width="150" data-height="150" data-inputColor="#e1e1e1" data-readOnly="true" data-fgColor="#37BC9B" data-knob-icon="ft-trending-up">
                          <ul class="list-inline clearfix mt-2 mb-0">
                              <li class="border-right-grey border-right-lighten-2 pr-2" onclick="openAdd('main/loadUrl/main/dashboardvms_detail_penyedia_terdaftar?reqJenis=verifikasi')" style="cursor:pointer">
                                  <h2 class="grey darken-1 text-bold-400"><?= $status1_persen.'%' ?></h2>
                                  <span class="success">Terverifikasi</span>
                              </li>
                              <li class="pl-2" onclick="openAdd('main/loadUrl/main/dashboardvms_detail_penyedia_terdaftar?reqJenis=nonverifikasi')" style="cursor:pointer">
                                  <h2 class="grey darken-1 text-bold-400"><?= $status0_persen+$status2_persen.'%' ?></h2>
                                  <span class="danger">Belum Terverifikasi</span>
                              </li>
                          </ul>
                      </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-12 col-md-12 animated zoomIn">
            <div class="card">
                <div class="card-content" onclick="openAdd('main/loadUrl/main/dashboardvms_detail_penyedia_terdaftar?reqJenis=sudahkirimberkas')" style="cursor: pointer;">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body text-left">
                                <ul class="list-inline text-left clearfix mb-0">
                                  <li class="border-right-grey border-right-lighten-2 pr-1">
                                     <h3 class="deep-orange"><?= number_format($status2_total_status - $countDataKirimBerkas->getfield('total'),0, ",",".")?></h3>
                                    <span class="">Penyedia Kirim Berkas</span>
                                  </li>
                                  <li class="pl-1">
                                    <h3 class="deep-orange"><?= number_format($countDataKirimBerkas->getfield('total'),0, ",",".")?></h3>
                                    <span class="">Dikembalikan</span>
                                  </li>
                                </ul>
                            </div>
                            <div class="media-right media-middle">
                                <i class="ft-inbox success font-large-2 float-right"></i>
                            </div>
                        </div>
                        <div class="progress mt-1 mb-0" style="height: 7px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $status2_persen ?>%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card animated zoomIn">
              <div class="card-content" onclick="openAdd('main/loadUrl/main/dashboardvms_detail_penyedia_terdaftar?reqJenis=belumkirimberkas')" style="cursor: pointer;">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body text-left">
                                <h3 class="deep-orange"><?= $status0_total_status ?></h3>
                                <span>Penyedia Belum Kirim Berkas</span>
                            </div>
                            <div class="media-right media-middle">
                                <i class="ft-minus-circle deep-orange font-large-2 float-right"></i>
                            </div>
                        </div>
                        <div class="progress mt-1 mb-0" style="height: 7px;">
                            <div class="progress-bar bg-deep-orange" role="progressbar" style="width: <?= $status0_persen ?>%" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card animated zoomIn">
              <div class="card-content" onclick="openAdd('main/loadUrl/main/dashboardvms_detail_penyedia_terdaftar?reqJenis=expired')" style="cursor: pointer;">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body text-left">
                                <h3 class="black"><?= $totalExpired ?></h3>
                                <span>Dokumen Expired Penyedia</span>
                            </div>
                            <div class="media-right media-middle">
                                <i class="ft-file black font-large-2 float-right"></i>
                            </div>
                        </div>
                        <div class="progress mt-1 mb-0" style="height: 7px;">
                            <div class="progress-bar bg-black" role="progressbar" style="width: <?php
                            if ($totalExpired > 0) {
                              if ($status1_total_status > 0){
                                echo round(($totalExpired/$status1_total_status*100),1);
                              } else {
                                echo $totalExpired;
                              }
                            } else {
                              echo '-';
                            }
                               ?>%" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-12 animated zoomIn">
          <div class="card animated zoomIn">
            <div class="card-content" onclick="openAdd('main/loadUrl/main/dashboardvms_detail_penyedia_terdaftar?reqJenis=blacklist')" style="cursor: pointer;">
                  <div class="card-body">
                      <div class="media">
                          <div class="media-body text-left">
                              <h3 class="black"><?= $totalBlacklist ?></h3>
                              <span>Penyedia Masuk Daftar Hitam</span>
                          </div>
                          <div class="media-right media-middle">
                              <i class="ft-user-x black font-large-2 float-right"></i>
                          </div>
                      </div>
                      <div class="progress mt-1 mb-0" style="height: 7px;">
                          <div class="progress-bar bg-black" role="progressbar" style="width: <?php
                          if ($totalBlacklist > 0) {
                            echo round(($totalBlacklist/$status1_total_status*100),1);
                          } else {
                            echo '-';
                          }
                             ?>%" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                  </div>
              </div>
          </div>

          <div class="card">
              <div class="card-header no-border">
                  <h4 class="card-title">Rating <?= LABEL_PENYEDIA ?></h4>
                  <a class="heading-elements-toggle"><i class="ft-more-horizontal font-medium-3"></i></a>
              </div>
              <div class="card-content">
                  <div id="audience-list-scroll" class="table-responsive height-200 position-relative">
                      <table class="table mb-0">
                          <thead>
                              <tr>
                                  <th>Rating</th>
                                  <th style="text-align: center">Total</th>
                              </tr>
                          </thead>
                          <tbody>
                              <tr onclick="openAdd('main/loadUrl/main/dashboardvms_detail_rating?reqStar=5')" style="cursor: pointer;">
                                  <td><div id="read-only-stars5"></div></td>
                                  <td class="text-center font-small-2" width="40%">
                                      <?= $rating5_total_persen ? $rating5_total_persen : 0 ?> %
                                      <div class="progress mb-0" style="height: 7px;">
                                          <div class="progress-bar bg-success" role="progressbar" style="width: <?= $rating5_total_persen ?>%" aria-valuenow="<?= $rating5_total_persen ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                  </td>
                              </tr>
                              <tr onclick="openAdd('main/loadUrl/main/dashboardvms_detail_rating?reqStar=4')" style="cursor: pointer;">
                                  <td><div id="read-only-stars4"></div></td>
                                  <td class="text-center font-small-2" width="40%">
                                      <?= $rating4_total_persen? $rating4_total_persen: 0 ?> %
                                      <div class="progress mb-0" style="height: 7px;">
                                          <div class="progress-bar bg-success" role="progressbar" style="width: <?= $rating4_total_persen ?>%" aria-valuenow="<?= $rating4_total_persen ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                  </td>
                              </tr>
                              <tr onclick="openAdd('main/loadUrl/main/dashboardvms_detail_rating?reqStar=3')" style="cursor: pointer;">
                                  <td><div id="read-only-stars3"></div></td>
                                  <td class="text-center font-small-2" width="40%">
                                      <?= $rating3_total_persen ? $rating3_total_persen : 0 ?> %
                                      <div class="progress mb-0" style="height: 7px;">
                                          <div class="progress-bar bg-success" role="progressbar" style="width: <?= $rating3_total_persen ?>%" aria-valuenow="<?= $rating3_total_persen ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                  </td>
                              </tr>
                              <tr onclick="openAdd('main/loadUrl/main/dashboardvms_detail_rating?reqStar=2')" style="cursor: pointer;">
                                  <td><div id="read-only-stars2"></div></td>
                                  <td class="text-center font-small-2" width="40%">
                                      <?= $rating2_total_persen? $rating2_total_persen : 0 ?> %
                                      <div class="progress mb-0" style="height: 7px;">
                                          <div class="progress-bar bg-success" role="progressbar" style="width: <?= $rating2_total_persen ?>%" aria-valuenow="<?= $rating2_total_persen ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                  </td>
                              </tr>
                              <tr onclick="openAdd('main/loadUrl/main/dashboardvms_detail_rating?reqStar=1')" style="cursor: pointer;">
                                  <td><div id="read-only-stars1"></div></td>
                                  <td class="text-center font-small-2" width="40%">
                                      <?= $rating1_total_persen ? $rating1_total_persen : 0 ?> %
                                      <div class="progress mb-0" style="height: 7px;">
                                          <div class="progress-bar bg-success" role="progressbar" style="width: <?= $rating1_total_persen ?>%" aria-valuenow="<?= $rating1_total_persen ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                  </td>
                              </tr>
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
        </div>

      </div>
    </div>

    <div class="col-xl-4 col-lg-6 col-md-12 animated zoomIn">
        <div class="card">
            <div class="card-content">
                <div class="card-body text-center">
                    <div class="card-header mb-2" <?php if ($katalog0_total_katalog > 0) { ?>onclick="openAdd('main/loadUrl/main/dashboardvms_detail_katalog?reqJenis=all')" <?php } ?> style="cursor:pointer">
                        <span class="success darken-1">Total Katalog</span>
                        <h3 class="font-large-2 orange darken-1 text-bold-200"><?= number_format($katalog0_total_katalog,0, ",",".")?></h3>
                    </div>
                    <div class="card-content">
                      <input type="text" value="<?= $katalog1_persen ?>" class="knob hide-value responsive angle-offset" data-angleOffset="0" data-thickness=".15" data-linecap="round" data-width="150" data-height="150" data-inputColor="#e1e1e1" data-readOnly="true" data-fgColor="#FF5722" data-knob-icon="ft-trending-up">
                      <ul class="list-inline clearfix mt-2 mb-0">
                          <li class="border-right-orange border-right-lighten-2 pr-2" <?php if ($katalog0_total_katalog > 0) { ?> onclick="openAdd('main/loadUrl/main/dashboardvms_detail_katalog?reqJenis=1')" <?php } ?> style="cursor:pointer">
                              <h2 class="orange darken-1 text-bold-400"><?= $katalog1_persen ?>%</h2>
                              <span class="success">Terverifikasi</span>
                          </li>
                          <li class="pl-2" <?php if ($katalog0_total_katalog > 0) { ?> onclick="openAdd('main/loadUrl/main/dashboardvms_detail_katalog?reqJenis=0')" <?php } ?> style="cursor:pointer">
                              <h2 class="orange darken-1 text-bold-400"><?= $katalog0_persen ?>%</h2>
                              <span class="danger">Belum Terverifikasi</span>
                          </li>
                      </ul>
                  </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-6 col-md-12 animated zoomIn">
        <div class="card">
          <div class="card-header no-border">
              <h4 class="card-title">Last Login <?= LABEL_PENYEDIA ?></h4>
          </div>
          <div class="card-content height-350" style="padding: 10px; overflow: scroll;">
            <table class="table table-bordered">
              <?php
              while ($user_login->nextRow()) {
                $ex = explode('.', $user_login->getField('selisih_login'));
                $ex2 = explode(':', $ex[0]);
                $jam = $ex2[0]; $menit = $ex2[1]; $detik = $ex2[2];
                $selisihLogin = $jam.' hour '.$menit.' minute '.$detik.' second';
                 echo '<tr>
                        <td width="100%">
                          <b>'.$user_login->getField('user_nama').' <span class="badge badge-primary" style="font-size:9px">'.$selisihLogin.'</span></b> <br>
                          <small><i class="fa fa-clock-o"></i> '.$user_login->getField('user_last_login').'</small>
                        </td>
                      </tr>';
               } ?>
            </table>
          </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-6 col-md-12 animated zoomIn">
      <div class="card animated zoomIn">
        <div class="card-content" onclick="openAdd('main/loadUrl/main/dashboardvms_detail_penyedia_terdaftar?reqJenis=blacklistHistory')" style="cursor: pointer;">
              <div class="card-body">
                  <div class="media">
                      <div class="media-body text-left">
                          <h3 class="black"><?= $totalBlacklistHistory ?></h3>
                          <span>History Daftar Hitam</span>
                      </div>
                      <div class="media-right media-middle">
                          <i class="ft-user-x black font-large-2 float-right"></i>
                      </div>
                  </div>
                  <div class="progress mt-1 mb-0" style="height: 7px;">
                      <div class="progress-bar bg-black" role="progressbar" style="width: <?php
                      if ($totalBlacklistHistory > 0) {
                        echo round(($totalBlacklistHistory/$status1_total_status*100),1);
                      } else {
                        echo '-';
                      }
                         ?>%" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
              </div>
          </div>
      </div>
      <div class="card">
        <div class="card-header no-border">
            <h4 class="card-title">Laporan Katalog</h4>
        </div>
        <div id="audience-list-scroll" class="table-responsive height-200 position-relative">
        <!-- <div class="card-content height-350" style="padding: 10px; overflow: scroll;"> -->
          <table class="table table-bordered">
            <tr>
              <th width="80%">Jenis Laporan</th>
              <th width="20%">Total</th>
            </tr>
            <?php
            $number = 0;
            while ($viewtotalkatalogLaporan->nextRow()) {
              $colorBadge = array('primary','danger','success','warning','secondary','black');
              echo '
                    <tr onClick="laporanDirect()" style="cursor:pointer">
                      <td width="80%">'.$viewtotalkatalogLaporan->getField('jenislaporan').'</td>
                      <td width="20%" style="text-align:center"> <span class="badge badge-pill badge-'.$colorBadge[$number].'">'.$viewtotalkatalogLaporan->getField('total').'</span></td>
                    </tr>
                   ';
            $number++;
            }
             ?>
          </table>
        </div>
      </div>
    </div>

</div>
