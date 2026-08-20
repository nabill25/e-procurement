<?php
$this->libsession->cekSession();

include_once("functions/string.func.php");
include_once("functions/date.func.php");
$this->load->model(array("Dashpaket","Queryfree","Userlogin"));
?>

<div class="row">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>lib/DataTables-1.10.7/media/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>lib/DataTables-1.10.7/extensions/Responsive/css/dataTables.responsive.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>lib/DataTables-1.10.7/examples/resources/syntax/shCore.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>lib/DataTables-1.10.7/examples/resources/demo.css">
    <script type="text/javascript" language="javascript" src="<?=base_url() ?>lib/DataTables-1.10.7/media/js/jquery.dataTables.js"></script>
    <script type="text/javascript" language="javascript" src="<?=base_url() ?>lib/DataTables-1.10.7/extensions/Responsive/js/dataTables.responsive.js"></script>
    <script type="text/javascript" language="javascript" src="<?=base_url() ?>lib/DataTables-1.10.7/examples/resources/syntax/shCore.js"></script>
    <script type="text/javascript" language="javascript" src="<?=base_url() ?>lib/DataTables-1.10.7/examples/resources/demo.js"></script>
    <script type="text/javascript" language="javascript" class="init">
    $(document).ready(function() {
      $('#penyediaDash').DataTable({
        "iDisplayLength": 10,
        "aaSorting": [[1, 'desc']],
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      });
    });
    </script>
    <style>
    #penyediaDash_length { display: none;}
    </style>
    <div class="col-md-12">
        <div class="card">
          <div class="card-header card-head-inverse bg-primary">
             <h4 class="card-title text-white" style="font-size:.9em !important">&nbsp;</h4>
             <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
              <div class="heading-elements">
                <ul class="list-inline mb-0">
                  <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                </ul>
              </div>
          </div>
          <div class="card-content collapse show border-info border-darken-2">
            <div class="card-body area-datatable">
              <div class="row" id="sticker">
                <div class="form-group col-md-12 mb-2">
                  <a class="<?= CLASS_BTN_PRIMARY ?>" title="Reload"><span class="fa fa-refresh"></span> Refresh</a>
                </div> 
              </div>
                <table id="penyediaDash" class="border-double table mb-0 table-bordered" style="width: 100%;">
                  <thead>
                    <tr>
                      <th class="text-left">Penyedia</th>
                      <th width="10%">Status Integrasi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $getData = new Queryfree(); 
                        $getData->selectByParams("SELECT a.* FROM view_integrasi_oracle a
                                                ");
                    while($getData->nextRow())
                    { ?>
                    <tr>
                      <td class="text-left">
                        <a onclick="openAdd('main/loadUrl/main/dashboard_detail_new/?jenis=terkontrak&tahun=<?= $getTahun ?>&rekananid=<?=$getData->getField('rekanan_id')?>')"><?= $getData->getField('nama'); ?>
                        </a>
                     </td> 
                      <td>
                        <?php  if($getData->getField('total') > 0 ) { echo $getData->getField('total').' kali'; } else { echo '0'; } ?>
                      </td>
                    </tr>
                    <?php
                    } ?>
                  </tbody>
                </table>
            </div>
          </div>
        </div>
    </div>
</div>

<script src="<?=base_url()?>assets/new/vendors/js/charts/echarts/echarts.js"></script>