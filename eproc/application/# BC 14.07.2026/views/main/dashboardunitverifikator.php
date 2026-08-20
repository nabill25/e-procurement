<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();

include_once("functions/string.func.php");

$this->load->model(array("DashPermohonanPaket","Dashpaketperencana","Dashpaket")); 
// $this->load->model("Paket");
$this->load->model("Queryfree");

$unitkerja  = $this->UNIT_KERJA_ID; 

// Planning 2023
$dash1 = new Dashpaketperencana();
$dash2 = new Dashpaketperencana();
$permohonan_paket = new DashPermohonanPaket();
$permohonan_paket_usulan = new DashPermohonanPaket();
$permohonan_paket_usulan_sum = new DashPermohonanPaket();
$permohonan_paket_usulan2 = new DashPermohonanPaket();
$permohonan_paket_usulan2_sum = new DashPermohonanPaket();
$permohonan_paket_rup = new DashPermohonanPaket();
$permohonan_paket_rup_sum = new DashPermohonanPaket();
$permohonan_paket_realisasi = new DashPermohonanPaket();
$permohonan_paket_realisasi_sum = new DashPermohonanPaket();
$paket = new Dashpaket();
$paket2 = new Dashpaket();


$getTahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

  // $getTahun = $_GET['tahun'];
  if ($getTahun != 'all'){
    $tahun = 'Tahun '.$getTahun; 

    $dash1->getCountPlanningVerifikatorPMJ($unitkerja,$this->USER_LOGIN_ID, $getTahun);
    $dash1->firstRow();

    // Usulan Kebutuhan Draft
    $permohonan_paket_usulan->selectByParams(array("A.TAHUN_ANGGARAN" => $getTahun),-1,-1," AND (A.APPROVAL IN ('3','3241','3251')) AND (A.ADMIN_RUP = '".$this->USER_LOGIN_ID."') ");
    $countTotalPermohonanUsulanDraft = $permohonan_paket_usulan->countRow();
    $permohonan_paket_usulan_sum->sumHpsByParams(array("A.TAHUN_ANGGARAN" => $getTahun),-1,-1," AND (A.APPROVAL IN ('3','3241','3251')) AND (A.ADMIN_RUP = '".$this->USER_LOGIN_ID."') ");
    $permohonan_paket_usulan_sum->firstRow();
    $sumHpsTotalUsulanDraft = $permohonan_paket_usulan_sum->getField('TOTAL_HPS');


    // Usulan Kebutuhan To Be Approve
    $permohonan_paket_usulan2->selectByParams(array("A.TAHUN_ANGGARAN" => $getTahun),-1,-1," AND (A.APPROVAL IN ('6')) AND (A.ADMIN_RUP = '".$this->USER_LOGIN_ID."') ");
    $countTotalPermohonanUsulanToBe = $permohonan_paket_usulan2->countRow();
    $permohonan_paket_usulan2_sum->sumHpsByParams(array("A.TAHUN_ANGGARAN" => $getTahun),-1,-1," AND (A.APPROVAL IN ('6')) AND (A.ADMIN_RUP = '".$this->USER_LOGIN_ID."') ");
    $permohonan_paket_usulan2_sum->firstRow();
    $sumHpsTotalUsulanToBe = $permohonan_paket_usulan2_sum->getField('TOTAL_HPS');

    // Usulan Kebutuhan RUP
    $permohonan_paket_rup->selectByParams(array("A.TAHUN_ANGGARAN" => $getTahun),-1,-1," AND A.APPROVAL='1'");
    $countTotalPermohonanRUP = $permohonan_paket_rup->countRow();
    $permohonan_paket_rup_sum->sumHpsByParams(array("A.TAHUN_ANGGARAN" => $getTahun),-1,-1," AND A.APPROVAL='1'");
    $permohonan_paket_rup_sum->firstRow();
    $sumHpsTotalRUP = $permohonan_paket_rup_sum->getField('TOTAL_HPS'); 
 
  } else {
    $tahun = '';

    $dash1->getCountPlanningVerifikatorPMJ($unitkerja,$this->USER_LOGIN_ID);
    $dash1->firstRow();

    $permohonan_paket_usulan->selectByParams(array(),-1,-1," AND (A.APPROVAL IN ('3','3241','3251')) AND (A.ADMIN_RUP = '".$this->USER_LOGIN_ID."') ");
    $countTotalPermohonanUsulanDraft = $permohonan_paket_usulan->countRow();
    $permohonan_paket_usulan_sum->sumHpsByParams(array(),-1,-1," AND (A.APPROVAL IN ('3','3241','3251')) AND (A.ADMIN_RUP = '".$this->USER_LOGIN_ID."') ");
    $permohonan_paket_usulan_sum->firstRow();
    $sumHpsTotalUsulanDraft = $permohonan_paket_usulan_sum->getField('TOTAL_HPS');

    $permohonan_paket_usulan2->selectByParams(array(),-1,-1," AND (A.APPROVAL IN ('6')) AND (A.ADMIN_RUP = '".$this->USER_LOGIN_ID."') ");
    $countTotalPermohonanUsulanToBe = $permohonan_paket_usulan2->countRow();
    $permohonan_paket_usulan2_sum->sumHpsByParams(array(),-1,-1," AND (A.APPROVAL IN ('6')) AND (A.ADMIN_RUP = '".$this->USER_LOGIN_ID."') ");
    $permohonan_paket_usulan2_sum->firstRow();
    $sumHpsTotalUsulanToBe = $permohonan_paket_usulan2_sum->getField('TOTAL_HPS');

    $permohonan_paket_rup->selectByParams(array(),-1,-1," AND A.APPROVAL='1'");
    $countTotalPermohonanRUP = $permohonan_paket_rup->countRow();
    $permohonan_paket_rup_sum->sumHpsByParams(array(),-1,-1," AND A.APPROVAL='1'");
    $permohonan_paket_rup_sum->firstRow();
    $sumHpsTotalRUP = $permohonan_paket_rup_sum->getField('TOTAL_HPS'); 
  }
    // echo $dash1->query; die;

  // $getUnitByVerifikator->selectByParams("SELECT a.user_jabatan, a.user_nama, a.user_login_id, a.unit_kerja_id from USER_LOGIN a where admin_rup = ".$this->USER_LOGIN_ID." and user_type_id = 21");
  // $totalUnit = $getUnitByVerifikator->countRow();
 
  $total_jenis_barangjasa_jasa_lain = $dash1->getField('TOTAL_JENIS_BARANGJASA_JASA_LAIN') ?: 0;
  $total_jenis_barangjasa_barang = $dash1->getField('TOTAL_JENIS_BARANGJASA_BARANG') ?: 0;
  $total_jenis_barangjasa_konsultansi = $dash1->getField('TOTAL_JENIS_BARANGJASA_KONSULTANSI') ?: 0;
  $total_jenis_barangjasa_konstruksi = $dash1->getField('TOTAL_JENIS_BARANGJASA_KONSTRUKSI') ?: 0;
  $total_cp_swakelola = $dash1->getField('TOTAL_CP_SWAKELOLA') ?: 0;
  $total_cp_penyedia = $dash1->getField('TOTAL_CP_PENYEDIA') ?: 0;
  $total_kategori_ya = $dash1->getField('TOTAL_KATEGORI_YA') ?: 0;
  $total_kategori_tidak = $dash1->getField('TOTAL_KATEGORI_TIDAK') ?: 0;
  $sum_kategori_ya = $dash1->getField('SUM_KATEGORI_YA') ?: 0;
  $sum_kategori_tidak = $dash1->getField('SUM_KATEGORI_TIDAK') ?: 0;
 

?>

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/pages/project.css">
<!-- <script src="https://unpkg.com/gauge-chart@latest/dist/bundle.js"></script> -->
<!-- <script src="https://unpkg.com/gauge-chart@latest/dist/bundle.js"></script> -->
<script src="<?=base_url()?>assets/new/vendors/js/vendors.min.js"></script>
<script src="<?=base_url()?>assets/new/vendors/js/charts/chart.min.js"></script>
<script src="<?=base_url()?>assets/new/vendors/js/extensions/jquery.raty.js"></script>
<script src="<?=base_url()?>assets/new/js/scripts/extensions/rating.js"></script>
<script src="<?=base_url()?>assets/new/vendors/js/extensions/jquery.knob.min.js"></script>
<script src="<?=base_url()?>assets/new/js/scripts/extensions/knob.js"></script>
<script src="<?=base_url()?>assets/new/vendors/js/charts/chart.min.js"></script>
<script src="<?=base_url()?>assets/new/vendors/js/charts/echarts/echarts.js"></script>
<!-- https://codepen.io/b1tn3r/pen/erLqbQ --> 

<script type="text/javascript"> 

  $(window).on("load", function(){
      require.config({
          paths: {
              echarts: '<?=base_url()?>assets/new/vendors/js/charts/echarts'
          }
      });

      require(
          [ 'echarts', 'echarts/chart/pie', 'echarts/chart/funnel', 'echarts/chart/bar' ],

          // Bug charts setup
          function (ec) {
              var bugChart = ec.init(document.getElementById('bug-pie-chart'));

              chartOptions = {
                  tooltip: {
                      trigger: 'item',
                      // formatter: "{a} <br/>{b}: {c} ({d}%)"
                      formatter: "{b}: {c} ({d}%)"
                  },

                  // Add legend 
                  legend: {
                      orient: 'horizontal',
                      x: 'left',
                      data: ['Barang', 'Jasa Konsultansi', 'Pekerjaan Konstruksi', 'Jasa Lainnya'],
                      show: false
                  },

                  // Add custom colors
                  // color: ['#FECEA8', '#FF847C', '#E84A5F','#759773', '#99B898','#afc8ae'],
                  color: ['#FECEA8', '#FF847C', '#E84A5F','#759773'],

                  // Display toolbox
                  toolbox: {
                      show: true,
                      orient: 'horizontal',
                      x: 'left',
                      //Enable if you need
                      feature: {
                          magicType: {
                              show: true,
                              title: {
                                  pie: 'Switch to pies',
                                  funnel: 'Switch to funnel',
                              },
                              type: ['pie', 'funnel'],
                              option: {
                                  funnel: {
                                      x: '25%',
                                      y: '20%',
                                      width: '50%',
                                      height: '70%',
                                      funnelAlign: 'left',
                                      max: 1548
                                  } 
                              }
                          },
                          restore: {
                              show: true,
                              title: 'Restore'
                          },
                          saveAsImage: {
                              show: true,
                              title: 'Same as image',
                              lang: ['Save']
                          }
                      }
                  },

                  // Enable drag recalculate
                  calculable: true,

                  // Add series
                  series: [{
                      name: '',
                      type: 'pie',
                      radius: '90%',
                      center: ['50%', '50.5%'],
                      data: [
                          {value: <?= $total_jenis_barangjasa_barang ?>, name: 'Barang'},
                          {value: <?= $total_jenis_barangjasa_konsultansi ?>, name: 'Jasa Konsultansi'},
                          {value: <?= $total_jenis_barangjasa_konstruksi ?>, name: 'Pekerjaan Konstruksi'},
                          {value: <?= $total_jenis_barangjasa_jasa_lain ?>, name: 'Jasa Lainnya'},
                      ]
                  }]
              };

              bugChart.setOption(chartOptions);

              $(function () {
                  // Resize chart on menu width change and window resize
                  $(window).on('resize', resize);
                  $(".menu-toggle").on('click', resize);
                    // Resize function
                  function resize() {
                      setTimeout(function() {
                          bugChart.resize();
                      }, 200);
                  }
              });
          }
      );
  });

</script>

<style type="text/css">
.chart-content {
  padding: 5px;
  margin: 10px;
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
.wfont, .ft-info, a .wfont, a .ft-info, .media-body, .fa-question-circle, .card-body small { color: #fff !important; }
.border-right {
    border-right: 1px solid #dee2e6!important;
}
.description-block {
    display: block;
    margin: 10px 0;
    text-align: center;
}
</style>


<div class="row">
<input type="hidden" id="tahun" value="<?=$getTahun?>">
<input type="hidden" id="metode" value="">
<input type="hidden" id="bulan" value="">
  <div class="form-group col-md-3">
    <!-- <label>Pilih Tahun</label> -->
    <select class="form-control" id="setyear" onChange="return window.location = $(this).val()">
      <?php
      $selected = '';
      $url = base_url('main/index/dashboardunitverifikator?tahun=');
      $kurangdari = date('Y') - 5;
      echo '<option value="'.$url.'all">-- Pilih Tahun --</option>';
      for ($i= date('Y')+1; $i > $kurangdari   ; $i--) {
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
  <div class="col-md-12">
    <div class="form-group alert" style="border: 1px solid #dee2e6!important">
      <div class="float-left">
        <h5><b>Dashboard Administrator RUP 
        <?php 
          if ($getTahun != 'all'){
            echo 'Tahun Anggaran '.$getTahun; 
          } else {
            echo 'Tahun Anggaran All'; 
          }
        ?> 
        </b></h5>
      </div>
      &nbsp;
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-8"> </div>
  <div class="col-md-4">
      <div class="card">
        <div class="row">
            <div class="col-md-4 border-right">
                <i class="fa fa-question-circle" data-toggle="tooltip" title="Total Paket <?php if ($getTahun != 'all'){ echo 'Tahun Anggaran '.$getTahun; } ?>" data-placement="top" aria-hidden="true" style="position:absolute;right:10px;padding-top:5px;color:black"></i>
                <div class="description-block">
                    <div class="description-header" style="font-size:2rem;"><?= numberToSimbol($countTotalPermohonanUsulanDraft + $countTotalPermohonanUsulanToBe + $countTotalPermohonanRUP) ?></div>
                    <span class="description-text">TOTAL PAKET</span>
                </div>
            </div> 
            <div class="col-md-7">
                <i class="fa fa-question-circle" data-toggle="tooltip" title="Total Harga Perkiraan <?php if ($getTahun != 'all'){ echo 'Tahun Anggaran '.$getTahun; } ?>" data-placement="top" aria-hidden="true" style="position:absolute;right:15px;padding-top:10px;color:black"></i>
                <div class="description-block">
                    <div class="description-header" style="font-size:2rem;" data-toggle="tooltip" data-placement="top" data-original-title="">Rp <?php echo numberToSimbol(round($sumHpsTotalUsulanDraft + $sumHpsTotalUsulanToBe + $sumHpsTotalRUP)) ?></div>
                    <span class="description-text">TOTAL HARGA PERKIRAAN</span>
                </div>
            </div> 
        </div>
    </div>
  </div> 
</div>

<div class="row">
  <input type="hidden" id="tahun" value="<?=$getTahun?>">
    <input type="hidden" id="metode" value="">
    <input type="hidden" id="bulan" value="">

  <div class="col-md-8">
    <div class="row">
      <div class="col-md-4">
        <!-- <div class="card"> -->
        <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #677D6A;">
            <div class="card-content">
              <a href="<?= base_url('main/index/permohonan_paket_usulan_admin') ?>"> 
                <div class="card-body">
                    <div class="media">
                        <div class="media-body text-center">
                            <span style="margin-top: 15%;">Usulan Kebutuhan <br>(Draft)</span>
                            <h2 class="wfont mt-2"><b>Rp. <?php echo numberToSimbol(round($sumHpsTotalUsulanDraft)) ?> </b></h2>
                            <!-- <small style="font-size: .8em; top: -12px; position: relative;">Harga Perkiraan</small> -->
                        </div>
                        <div class="media-right media-middle">
                            <i class="fa fa-question-circle dark" data-toggle="tooltip" data-placement="top" title="Total Usulan Kebutuhan" style="font-size:1.3em !important; cursor: pointer;"></i>
                        </div>
                    </div>
                    <div class="mt-1 text-center">
                      <?= '<small style="font-weight:bold">'.$countTotalPermohonanUsulanDraft.' Paket</small>'; ?>
                    </div>
                </div>
              </a>
            </div>
        </div>
      </div>  

      <div class="col-md-4">
        <!-- <div class="card"> -->
        <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #6C946F;">
            <div class="card-content">
              <a href="<?= base_url('main/index/permohonan_paket_usulan_admin_to_be_approved') ?>"> 
                <div class="card-body">
                    <div class="media">
                        <div class="media-body text-center">
                            <span style="margin-top: 15%;">Usulan Kebutuhan <br>(To Be Approved)</span>
                            <h2 class="wfont mt-2"><b>Rp. <?php echo numberToSimbol(round($sumHpsTotalUsulanToBe)) ?> </b></h2>
                            <!-- <small style="font-size: .8em; top: -12px; position: relative;">Harga Perkiraan</small> -->
                        </div>
                        <div class="media-right media-middle">
                            <i class="fa fa-question-circle dark" data-toggle="tooltip" data-placement="top" title="Total Usulan Kebutuhan" style="font-size:1.3em !important; cursor: pointer;"></i>
                        </div>
                    </div>
                    <div class="mt-1 text-center">
                      <?= '<small style="font-weight:bold">'.$countTotalPermohonanUsulanToBe.' Paket</small>'; ?>
                    </div>
                </div>
              </a>
            </div>
        </div>
      </div>  

      <div class="col-md-4">
        <!-- <div class="card"> -->
        <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #7A3E29;">
            <div class="card-content">
              <a href="<?= base_url('main/index/permohonan_paket_fungsional_rup') ?>"> 
                <div class="card-body">
                    <div class="media">
                        <div class="media-body text-center">
                            <span style="margin-top: 15%;">Rencana Pengadaan <br>&nbsp;</span>
                            <h2 class="wfont mt-2"><b>Rp. <?php echo numberToSimbol(round($sumHpsTotalRUP)) ?> </b></h2>
                            <!-- <small style="font-size: .8em; top: -12px; position: relative;">Harga Perkiraan</small> -->
                        </div>
                        <div class="media-right media-middle">
                            <i class="fa fa-question-circle dark" data-toggle="tooltip" data-placement="top" title="Total Rencana Pengadaan" style="font-size:1.3em !important; cursor: pointer;"></i>
                        </div>
                    </div>
                    <div class="mt-1 text-center">
                      <?= '<small style="font-weight:bold">'.$countTotalPermohonanRUP.' Paket</small>'; ?>
                    </div>
                </div>
              </a>
            </div>
        </div>
      </div>  

      <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card">
            <div class="card-body">
            <h4 class="text-center">Jenis Barang/Jasa <br><small><?= $tahun ?></small></h4>
               <div id="bug-pie-chart" class="height-400 echart-container"></div>
            </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="col-md-12">
      <?php 
        $pieLabel = array('1' => 'Swakelola', '2' => 'Penyedia');
        $pieValue = array('1' => $total_cp_swakelola, '2' => $total_cp_penyedia);
        ?>

        <script type="text/javascript">
        $(function() {
          var canvas = document.getElementById("barChart");
          var ctx = canvas.getContext('2d');

          // Global Options:
          Chart.defaults.global.defaultFontColor = 'red';
          Chart.defaults.global.defaultFontSize = 10;

          var data = {
              labels: [
               <?php
               foreach ($pieLabel as $valpieLabel):
                echo '"'.$valpieLabel.'",';
               endforeach ?>
              ],
              datasets:
              [{
                  fill: true,
                  backgroundColor: ['#89224f','#ee6292'],
                  data: [
                  <?php
                   foreach ($pieValue as $valpieValue):
                    echo '"'.$valpieValue.'",';
                   endforeach ?>
                   ],
                  // Notice the borderColor
                  borderColor:  ['#89224f','#ee6292'],
                  borderWidth: [2,2]
                }
              ]
          };
          // Notice the rotation from the documentation.
          var options =
              {
                layout: {
                  padding: -10
                },
                title:
                {
                  display: true,
                  text: 'Cara Pengadaan <?= $tahun ?>',
                  position: 'top',  
                },
                animation: {
                  duration: 2500,
                },
                rotation: -0.7 * Math.PI
              };

          // Chart declaration:
          var myBarChart = new Chart(ctx, {
              type: 'doughnut',
              data: data,
              options: options
          }); 
          }); 
        </script>
          <div class="card"> 
            <div class="chart-content">
              <canvas id="barChart"></canvas>
            </div>
          </div>
    </div>

    <div class="col-md-12">
    <?php 
      $pieLabel2 = array('1' => 'Ya', '2' => 'Tidak');
      $pieValue2 = array('1' => $total_kategori_ya, '2' => $total_kategori_tidak);
      ?>

      <script type="text/javascript">
      $(function() {
        var canvas = document.getElementById("barChart2");
        var ctx = canvas.getContext('2d');

        // Global Options:
        Chart.defaults.global.defaultFontColor = 'black';
        Chart.defaults.global.defaultFontSize = 14;

        var data2 = {
            labels: [
             <?php
             foreach ($pieLabel2 as $valpieLabel2):
              echo '"'.$valpieLabel2.'",';
             endforeach ?>
            ],
            datasets:
            [{
                fill: true,
                backgroundColor: ['#827723','#cddc44'],
                data: [
                <?php
                 foreach ($pieValue2 as $valpieValue2):
                  echo '"'.$valpieValue2.'",';
                 endforeach ?>
                 ],
                // Notice the borderColor
                borderColor:  ['#827723','#cddc44'],
                borderWidth: [2,2]
              }
            ]
        };
        // Notice the rotation from the documentation.
        var options2 =
            {
              layout: {
                  padding: -10
                },
              title:
              {
                display: true,
                text: 'Produk Dalam Negeri <?= $tahun ?>',
                position: 'top',
              },
              animation: {
                duration: 2500,
              },
              plugins: {
      customCanvasBackgroundColor: {
        color: 'lightGreen',
      }
    },
          rotation: -0.1 * Math.PI
        };

        // Chart declaration:
        var myBarChart2 = new Chart(ctx, {
            type: 'doughnut',
            data: data2,
            options: options2
        }); 
        }); 
      </script>
      <div class="card"> 
        <div class="chart-content">
          <canvas id="barChart2"></canvas>
          <div class="btn-group col-md-12 mt-1" role="group" aria-label="Basic example">
            <span class="btn btn-outline-grey col-md-12">
              <h1><?= numberToSimbol(round($sum_kategori_ya)) ?></h1>
              Dalam Negeri
            </span>
            <span class="btn btn-outline-grey col-md-12">
              <h1><?= numberToSimbol(round($sum_kategori_tidak)) ?></h1>
              Luar Negeri
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>  

