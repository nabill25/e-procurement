<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();

include_once("functions/string.func.php");

$this->load->model("Paketperencanadash");
$this->load->model("PermohonanPaket");
$this->load->model("Paket");
$this->load->model("Queryfree");

$unitkerja  = $this->UNIT_KERJA_ID; 

// Planning 2023
$dash1 = new Paketperencanadash();
$dash2 = new Paketperencanadash();
$dataUnit = new Paketperencanadash();
$getUnitByVerifikator = new Queryfree();


$getTahun = isset($_GET['tahun']) ? $_GET['tahun'] : 'all';

  // $getTahun = $_GET['tahun'];
  if ($getTahun != 'all'){
    $tahun = 'Tahun '.$getTahun;
    $dash1->getCountPlanningValidator($unitkerja,$this->USER_LOGIN_ID, $getTahun);
    $dash1->firstRow();
  } else {
    $tahun = '';
    $dash1->getCountPlanningValidator($unitkerja,$this->USER_LOGIN_ID);
    $dash1->firstRow();
  }

  $getUnitByVerifikator->selectByParams("SELECT a.user_jabatan, a.user_nama, a.user_login_id, a.unit_kerja_id from USER_LOGIN a where user_type_id = 17");
  $totalUnit = $getUnitByVerifikator->countRow();

  $total_inputan = $dash1->getField('TOTAL_INPUTAN') ?: 0;
  $perkiraan_biaya_harga_inputan = $dash1->getField('PERKIRAAN_BIAYA_HARGA_INPUTAN') ?: 0;
  $total_verifikator = $dash1->getField('TOTAL_VERIFIKATOR') ?: 0;
  $perkiraan_biaya_harga_verifikator = $dash1->getField('PERKIRAAN_BIAYA_HARGA_VERIFIKATOR') ?: 0;
  $total_validator = $dash1->getField('TOTAL_VALIDATOR') ?: 0;
  $perkiraan_biaya_harga_validator = $dash1->getField('PERKIRAAN_BIAYA_HARGA_VALIDATOR') ?: 0;
  $total_approval = $dash1->getField('TOTAL_APPROVAL') ?: 0;
  $perkiraan_biaya_harga_approval = $dash1->getField('PERKIRAAN_BIAYA_HARGA_APPROVAL') ?: 0;
  $total_rencana_pengadaan = $dash1->getField('TOTAL_RENCANA_PENGADAAN') ?: 0;
  $hps = $dash1->getField('HPS') ?: 0;
  $total_usulan = $dash1->getField('TOTAL_USULAN') ?: 0;
  $total_usulan_kebutuhan = $total_inputan + $total_verifikator + $total_validator + $total_approval;
  if ($total_usulan > 0) {
    $persentase_perencanaan = round($total_rencana_pengadaan/$total_usulan*100,1); 
  } 
  $total_jenis_barangjasa_jasa_lain = $dash1->getField('TOTAL_JENIS_BARANGJASA_JASA_LAIN') ?: 0;
  $total_jenis_barangjasa_barang = $dash1->getField('TOTAL_JENIS_BARANGJASA_BARANG') ?: 0;
  $total_jenis_barangjasa_konsultansi = $dash1->getField('TOTAL_JENIS_BARANGJASA_KONSULTANSI') ?: 0;
  $total_jenis_barangjasa_konstruksi = $dash1->getField('TOTAL_JENIS_BARANGJASA_KONSTRUKSI') ?: 0;
  $total_kategori_reguler = $dash1->getField('TOTAL_KATEGORI_REGULER') ?: 0;
  $total_kategori_insidental = $dash1->getField('TOTAL_KATEGORI_INSIDENTAL') ?: 0;
  $total_jenisbelanja_operasional = $dash1->getField('TOTAL_JENISBELANJA_OPERASIONAL') ?: 0;
  $total_jenisbelanja_modal = $dash1->getField('TOTAL_JENISBELANJA_MODAL') ?: 0;
 

?>

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/pages/project.css">

<style type="text/css">
.chart-content { padding: 5px; /*background-color: #f9f9f9;*/ /*width: 700px;*/ margin: 10px; /*box-shadow: 0px 0px 2px #ccc;*/ }

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
      $url = base_url('main/index/dashboardunitvalidator?tahun=');
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

  <?php 
  
  switch ($totalUnit) {
    case '2':
      $classUnit = "col-xl-6 col-lg-6 col-md-6";
      break;
    case '3':
      $classUnit = "col-xl-4 col-lg-4 col-md-4";
      break;
    case '4':
      $classUnit = "col-xl-3 col-lg-3 col-md-3";
      break;
    case '5':
      $classUnit = "col-xl-2 col-lg-2 col-md-2";
      break;
    case '6':
      $classUnit = "col-xl-2 col-lg-2 col-md-2";
      break;
    
    default:
      $classUnit = "col-xl-12 col-lg-12 col-md-12";
      break;
  }

  // for ($i=0; $i < 1; $i++)
  while($getUnitByVerifikator->nextRow())
  { 
    if ($getTahun != 'all')
    {
      $tahun = 'Tahun '.$getTahun;
      $dataUnit->getCountPlanningVerifikator($getUnitByVerifikator->getField('UNIT_KERJA_ID'),$getUnitByVerifikator->getField('USER_LOGIN_ID'), $getTahun);
      $dataUnit->firstRow();
    } else {
      $tahun = '';
      $dataUnit->getCountPlanningVerifikator($getUnitByVerifikator->getField('UNIT_KERJA_ID'),$getUnitByVerifikator->getField('USER_LOGIN_ID'));
      $dataUnit->firstRow();
    }

    $total_inputan = $dataUnit->getField('TOTAL_INPUTAN') ?: 0;
    $perkiraan_biaya_harga_inputan = $dataUnit->getField('PERKIRAAN_BIAYA_HARGA_INPUTAN') ?: 0;
    $total_verifikator = $dataUnit->getField('TOTAL_VERIFIKATOR') ?: 0;
    $perkiraan_biaya_harga_verifikator = $dataUnit->getField('PERKIRAAN_BIAYA_HARGA_VERIFIKATOR') ?: 0;
    $total_validator = $dataUnit->getField('TOTAL_VALIDATOR') ?: 0;
    $perkiraan_biaya_harga_validator = $dataUnit->getField('PERKIRAAN_BIAYA_HARGA_VALIDATOR') ?: 0;
    $total_approval = $dataUnit->getField('TOTAL_APPROVAL') ?: 0;
    $perkiraan_biaya_harga_approval = $dataUnit->getField('PERKIRAAN_BIAYA_HARGA_APPROVAL') ?: 0;
    $total_rencana_pengadaan = $dataUnit->getField('TOTAL_RENCANA_PENGADAAN') ?: 0;
    $hps = $dataUnit->getField('HPS') ?: 0;
    $total_usulan = $dataUnit->getField('TOTAL_USULAN') ?: 0;
    $total_usulan_kebutuhan = $total_inputan + $total_verifikator + $total_validator + $total_approval;
    if ($total_usulan > 0) {
      $persentase_perencanaan = round($total_rencana_pengadaan/$total_usulan*100,1); 
    } else {
      $persentase_perencanaan = 0; 
    } 
  ?> 

  <div class="<?= $classUnit ?> animated zoomIn">
      <div class="card">
          <div class="card-content">
              <div class="card-body text-center">
                  <div class="card-header mb-2" style="padding: 0px !important;" onclick="openAdd('main/loadUrl/main/dashboardvms_detail_penyedia_terdaftar?reqJenis=total')" style="cursor:pointer">
                      <h2><?= $getUnitByVerifikator->getField("USER_NAMA") ?></h2>
                      <h6 class="card-subtitle text-muted mt-1"><?= $getUnitByVerifikator->getField("USER_JABATAN") ?></h6>
                      <hr>
                      <h3 class="success darken-1">Total Perencanaan <br> <small><?= $tahun ?></small></h3>
                      <h3 class="font-large-2 grey darken-1 text-bold-200"><?= number_format($total_usulan,0, ",",".")?></h3>
                  </div>
                  <div class="card-content" style="margin-bottom:2%">
                    <input type="text" value="<?= $persentase_perencanaan ?>" class="knob hide-value responsive angle-offset" data-angleOffset="0" data-thickness=".15" data-linecap="round" data-width="150" data-height="150" data-inputColor="#e1e1e1" data-readOnly="true" data-fgColor="#37BC9B" data-knob-icon="ft-trending-up">
                    <ul class="list-inline clearfix mt-2 mb-0">
                        <li class="border-right-grey border-right-lighten-2 pr-2" onclick="openAdd('main/loadUrl/main/dashboardvms_detail_penyedia_terdaftar?reqJenis=verifikasi')" style="cursor:pointer">
                            <h2 class="grey darken-1 text-bold-400"><?= $total_usulan_kebutuhan ?></h2>
                            <span class="success">Usulan Kebutuhan</span> 
                        </li>
                        <li class="pl-2" onclick="openAdd('main/loadUrl/main/dashboardvms_detail_penyedia_terdaftar?reqJenis=nonverifikasi')" style="cursor:pointer">
                            <h2 class="grey darken-1 text-bold-400"><?= $total_rencana_pengadaan ?></h2>
                            <span class="success">Rencana Pengadaan </span>
                        </li>
                    </ul>
                </div>
              </div>
          </div>
      </div>
  </div>
  <?php 
  } ?>

</div>
  

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

<div class="row">
   <div class="col-xl-8 col-lg-12 col-md-12">
      <div class="card">
            <div class="card-body">
            <h4 class="text-center">Jenis Barang/Jasa <br><small><?= $tahun ?></small></h4>
               <div id="bug-pie-chart" class="height-400 echart-container"></div>
            </div>
      </div>
   </div>

  <input type="hidden" id="tahun" value="<?=$getTahun?>">
  <input type="hidden" id="metode" value="">
  <input type="hidden" id="bulan" value="">
  

  <div class="col-md-4">
    <div class="col-md-12">
      <?php 
        $pieLabel = array('0' => 'Insidental', '1' => 'Reguler');
        $pieValue = array('0' => $total_kategori_insidental, '1' => $total_kategori_reguler);
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
                  text: 'Kategori <?= $tahun ?>',
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
      $pieLabel2 = array('0' => 'Modal', '1' => 'Operasional');
      $pieValue2 = array('0' => $total_jenisbelanja_modal, '1' => $total_jenisbelanja_operasional);
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
                text: 'Jenis Belanja <?= $tahun ?>',
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
        </div>
      </div>
    </div>
  </div>  
