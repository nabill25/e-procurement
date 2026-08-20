<?php
$this->libsession->cekSession();

include_once("functions/string.func.php");
include_once("functions/date.func.php");

$this->load->model(array("Integrate","Queryfree","Userlogin"));

?>
<script src="<?=base_url()?>assets/new/vendors/js/charts/chart.min.js"></script>
<script src="https://unpkg.com/emodal@1.2.69/dist/eModal.min.js" /></script>

<script type="text/javascript" language="javascript" class="init">
var thisURLGenerate = "<?= base_url('integrasi_json/supplier') ?>";
var thisURLSentFTP = "<?= base_url('integrasi_json/supplierSendToFTP') ?>";
function executeFTP() {
    if (confirm('Apakah Anda yakin ingin menjalankan proses Supplier Generate Excel dan kirim FTP secara manual?')) {
        $.get(thisURLGenerate, function(response) {
            alertSuccess2(response);
            $.get(thisURLSentFTP, function(response2) {
                alertSuccess2(response2);
            });
        });
      } else {
      }
}

var thisURLGeneratePO = "<?= base_url('integrasi_json/po') ?>";
var thisURLSentFTPPO = "<?= base_url('integrasi_json/poSendToFTP') ?>";
function executeFTPPO() {
    if (confirm('Apakah Anda yakin ingin menjalankan proses Purchase Order (PO) Generate Excel dan kirim FTP secara manual?')) {
        $.get(thisURLGeneratePO, function(response) {
            alertSuccess2(response);
            $.get(thisURLSentFTPPO, function(response2) {
                alertSuccess2(response2);
            });
        });
      } else {
      }
}
</script>

<style type="text/css"> 
    .wfont, .ft-info { color: #fff !important; }
    .border-right { border-right: 1px solid #dee2e6!important; }
    .description-block { display: block; margin: 0px 0; text-align: center; }
</style>

<div class="row">
  <div class="col-md-12">
    <div class="form-group alert" style="border: 1px solid #dee2e6!important">
      <div class="float-left">
        <h5><b>Monitoring Integrasi Oracle</b></h5>
      </div>
      &nbsp;
    </div>
  </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #b30000;">
            <div class="card-content">
                <div class="card-body">
                    <div class="media">
                        <div class="media-body text-center">
                            <h2 class="wfont mt-2"><b>SUPPLIER <br> &nbsp; </b></h2>
                        </div> 
                    </div>
                    <div class="text-center" onclick="openAdd('integration/loadUrl/integration/integration_monitoring_detail/?jenis=vendor')" style="cursor: pointer">
                      Lihat detail <i class="fa fa-arrow-circle-right"></i>
                    </div>
                    <div class="mt-1 text-center btn btn-info btn-sm" onclick="executeFTP()" style="cursor: pointer; width: 100%;">
                     <i class="fa fa-cogs"></i>  Execute Manual 
                    </div>
                </div>
            </div>
        </div>
    </div> 
    <div class="col-md-3">
        <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #b30000;">
            <div class="card-content">
                <div class="card-body">
                    <div class="media">
                        <div class="media-body text-center">
                            <h2 class="wfont mt-2"><b>P R  <br> &nbsp; </b></h2>
                        </div> 
                    </div>
                    <div class="text-center" onclick="window.location.href='<?= base_url('integration/index/integration_monitoring_pr') ?>'" style="cursor: pointer">
                      Lihat detail <i class="fa fa-arrow-circle-right"></i>
                    </div>
                    <div class="mt-2 text-center">
                     &nbsp;<br>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #b30000;">
            <div class="card-content">
                <div class="card-body">
                    <div class="media">
                        <div class="media-body text-center">
                            <h2 class="wfont mt-2"><b>R K A  <br> &nbsp; </b></h2>
                        </div> 
                    </div>
                    <div class="text-center" onclick="window.location.href='<?= base_url('integration/index/integration_monitoring_rka') ?>'" style="cursor: pointer">
                      Lihat detail <i class="fa fa-arrow-circle-right"></i>
                    </div>
                    <div class="mt-2 text-center">
                     &nbsp;<br>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card box-shadow-0 animated zoomIn wfont" data-appear="appear" data-animation="zoomIn" style="background-color: #b30000;">
            <div class="card-content">
                <div class="card-body">
                    <div class="media">
                        <div class="media-body text-center">
                            <h2 class="wfont mt-2"><b>Purchase Order  <br> (PO) </b></h2>
                        </div> 
                    </div>
                    <div class="text-center" onclick="openAdd('integration/loadUrl/integration/integration_monitoring_detail/?jenis=po')" style="cursor: pointer">
                      Lihat detail <i class="fa fa-arrow-circle-right"></i>
                    </div>
                    <div class="mt-1 text-center btn btn-info btn-sm" onclick="executeFTPPO()" style="cursor: pointer; width: 100%;">
                     <i class="fa fa-cogs"></i>  Execute Manual 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


    <!-- Logs -->
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
        $('#example').DataTable({
          "aaSorting": [[0, 'desc']],
          "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
        });
      });
      $('.confirmation').on('click', function () {
            return confirm('Are you sure?');
        });
    </script>
    <div class="col-md-12 col-sm-12">
        <div class="card">
          <div class="card-header card-head-inverse bg-primary">
            <h4 class="card-title text-white">Integration Logs </h4>
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

                <!-- <div class="table-responsive"> -->
                  <!-- <table class="table table-striped mb-0" id="example">  -->
                  <table id="example" class="border-double table mb-0 table-bordered" style="width: 100%">
                    <thead>
                        <tr>
                            <!-- <th width="10px">No</th> -->
                            <th width="70px">Tanggal</th>
                            <th>File Logs</th>
                            <th style="text-align: center; width: 100px">Download</th>
                        </tr>       
                    </thead>
                    <tbody>
                        <?php 
                        $no=1;
                        $files = glob('integration/logs/*.{txt,log}', GLOB_BRACE);
                        $dir   = 'integration/logs/';
                        // echo "<pre>"; print_r($files); die();
                        arsort($files);
                        foreach($files as $file) {
                          $fileName = str_replace($dir, "", $file);
                          $jenis = explode("_", $fileName);
                          if ($jenis[0] == 'SUPPLIER') {
                              $explod = explode("SUPPLIER_LOGS_", $file);
                          } else if ($jenis[0] == 'PO') {
                              $explod = explode("PO_LOGS_", $file);
                          } else if ($jenis[0] == 'PR') {
                              $explod = explode("PR_LOGS_", $file);
                          } else if ($jenis[0] == 'RKA') {
                              $explod = explode("RKA_LOGS_", $file);
                          }
                          echo '<tr>';
                          // echo '<td>'.$no.'</td>';
                          echo '<td>'.str_replace(".txt", "", $explod[1]).'</td>';
                          echo '<td>'.$fileName.'</td>';
                          echo '<td align="center">
                                  <a href="'.$file.'" class="btn btn-primary" target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a>
                                  <a href="integrasi_json/logs_file_delete/'.$fileName.'" onclick="return confirm(\'Apakah benar anda akan menghapus file logs aktifitas tanggal '.str_replace(".txt", "", $explod[1]).'?\')" class="btn btn-danger btn-xs"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                </td>';
                          echo '</tr>';
                        $no++;
                        } 
                        ?>
                    </tbody>
                  </table>   
                <!-- </div> -->

            </div>
          </div>
        </div>
      </div> 

</div>

<script src="<?=base_url()?>assets/new/vendors/js/charts/echarts/echarts.js"></script>

