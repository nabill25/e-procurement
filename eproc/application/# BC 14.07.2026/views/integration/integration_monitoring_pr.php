<?php
$this->libsession->cekSession();

include_once("functions/string.func.php");
include_once("functions/date.func.php");

$this->load->model(array("Integrate","Queryfree","Userlogin"));
$this->load->library("Libintegrationoracle");
?>

<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/media/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/extensions/Responsive/css/dataTables.responsive.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/syntax/shCore.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/examples/resources/demo.css"> 
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/extensions/Responsive/js/dataTables.responsive.js"></script>

<script type="text/javascript" language="javascript" class="init"> 
  $(document).ready(function() {
    $('.myTable').DataTable({
      // "aaSorting": [[1, 'desc']],
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
    });
 
  });
  $('.confirmation').on('click', function () {
        return confirm('Are you sure?');
    });
</script>


<script type="text/javascript" language="javascript" class="init">
var thisURLRKAFileRemote = "<?= base_url('integrasi_json/prFileRemote') ?>";
var thisURLRKAFileLokal = "<?= base_url('integrasi_json/prFileLokal') ?>";
var thisURLDownloadPR = "<?= base_url('integrasi_json/prDownloadToFTP') ?>";
var thisURLImportPR = "<?= base_url('integrasi_json/prImport') ?>";
var thisURLMovePR = "<?= base_url('integrasi_json/prMove') ?>";

$(document).ready(function() {
    setTimeout(function() {
        reloadFileRemote();
    }, 1000);

    // setTimeout(function() {
    //     reloadFileLokal();
    // }, 1000);
});

function reloadFileRemote() {
    $('#loadingSide').show();
    $("#dataRemote").html('');
    $.get(thisURLRKAFileRemote, function(response) {
        $("#dataRemote").html(response);
    })
    .always(function() {
        $('#loadingSide').hide();
    });
}

function reloadFileLokal() {
    $('#loadingSide2').show();
    $("#dataLokal").html('');
    $.get(thisURLRKAFileLokal, function(response) {
        $("#dataLokal").html(response);
    })
    .always(function() {
        $('#loadingSide2').hide();
    });
}

function prDownload(file,id) {
    if (confirm('Apakah Anda yakin ingin melakukan import file '+file+' ini ?')) {
        $("#"+id).html('<i class="fa fa-spinner"></i>');
        $("#content-import-notif").html('');
        $("#content-import-notif").show();
        $.get(thisURLDownloadPR+'?file='+file, function(response) {
            $("#content-import-notif").html(response);
            // Import File to local
            prImport(file,id);
        })
        .always(function() { // Finish
        });
    } else {}
}

function prImport(file,id) {
    $.get(thisURLImportPR+'?file='+file, function(response) {
        $("#content-import-notif").prepend(response);
    })
    .always(function() { // Finish 
        setTimeout(function() {
            $("#"+id).html('<i class="fa fa-cloud-download"></i>'); 
            $("#content-import-notif").hide();
            // reloadFileRemote();
        }, 4500);
        setTimeout(function() {
            $(".tab-content").show();
        }, 5500);
        setTimeout(function() {
            location.reload();
        }, 6000);

    });
}

// function prMove(file,id) {
//     $("#"+id).html('<i class="fa fa-spinner"></i>');
//     $.get(thisURLMovePR+'?file='+file, function(response) {
//         $("#"+id).html('<i class="fa fa-cloud-download"></i>'); 
//         $("#content-import-notif").prepend(response);
//         reloadFileRemote();
//     })
//     .always(function() { // Finish
//     });
// }

// function prImport(file,id) {
//     if (confirm('Import file '+file+' ke database ?')) {
//         $("#"+id).html('<i class="fa fa-spinner"></i>');
//         $(".tab-content").hide();
//         $("#content-import-notif").html('');
//         $("#content-import-notif").show();
//         $.get(thisURLImportPR+'?file='+file, function(response) {
//             $("#"+id).html('<i class="fa fa-download"></i>'); 
//             $("#content-import-notif").html(response);
//         })
//         .always(function() { // Finish
//             setTimeout(function() {
//                 $("#content-import-notif").hide();
//             }, 1500);
//             setTimeout(function() {
//                 $(".tab-content").show();
//             }, 2000);
//         });
//     } else {}
// }
</script>
 
    <div class="row">
      <div class="col-md-12">
        <div class="form-group alert" style="border: 1px solid #dee2e6!important">
          <div class="float-left">
            <h5><b>Monitoring Integrasi P R</b></h5>
          </div>
          &nbsp;
        </div>
      </div>
    </div>

    <div class="sidebar-detached sidebar-left">
        <div class="sidebar">
            <div class="bug-list-sidebar-content">
                <!-- File Remote -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">File Remote</h4>
                        <a class="heading-elements-toggle"><i class="ft-ellipsis-h font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a onclick="reloadFileRemote()"><i class="fa fa-refresh"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- bug-list search -->
                    <div class="card-content collapse show" style="">
                        <div class="border-top-blue-grey border-top-lighten-5">
                        </div>
                        <!-- /bug-list search -->
 
                        <div class="card-body ">
                            <img id="loadingSide" src="<?php echo base_url('images') ?>/loader-page.gif" alt="Loading..." style="left:25% !important;" />
                            <ul class="list-group card">
                                <div id="dataRemote">
                                </div>
                            </ul>
                            <!-- <a class="btn btn-primary btn-sm" style="color:#fff">
                                <i class="ft-plus white"></i> Lihat lebih banyak
                            </a> -->
                        </div>
                    </div>
                </div>
                <!--/ File Remote -->

                <!-- File Remote -->
                <!-- <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">File Lokal</h4>
                        <a class="heading-elements-toggle"><i class="ft-ellipsis-h font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a onclick="reloadFileLokal()"><i class="fa fa-refresh"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content collapse show" style="">
                        <div class="border-top-blue-grey border-top-lighten-5">
                        </div>
 
                        <div class="card-body ">
                            <img id="loadingSide2" src="<?php // echo base_url('images') ?>/loader-page.gif" alt="Loading..." style="left:25% !important;" />
                            <ul class="list-group card">
                                <div id="dataLokal">
                                </div>
                            </ul>
                        </div>
                    </div>
                </div> -->
                <!--/ File Remote -->
                 
            </div>
        </div>
    </div>

    <div class="content-detached content-right">
        <div class="content-body">
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <!-- <div class="card-header">
                            <h4 class="card-title">Database P R</h4> 
                        </div> -->
                        <div class="card-content">
                            <div class="card-body" id="body-content-pr">
                                <ul class="nav nav-tabs nav-linetriangle">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="pr1-tab1" data-toggle="tab" href="#pr1" aria-controls="pr1" aria-expanded="true"><i class="fa fa-align-justify"></i> PR HEADER</a>
                                    </li> 
                                    <li class="nav-item">
                                        <a class="nav-link" id="pr2-tab2" data-toggle="tab" href="#pr2" aria-controls="pr2" aria-expanded="true"><i class="fa fa-align-justify"></i> PR HEADER ATTACHMENT</a>
                                    </li> 
                                    <li class="nav-item">
                                        <a class="nav-link" id="pr3-tab3" data-toggle="tab" href="#pr3" aria-controls="pr3" aria-expanded="true"><i class="fa fa-align-justify"></i> PR LINE</a>
                                    </li> 
                                    <li class="nav-item">
                                        <a class="nav-link" id="pr4-tab4" data-toggle="tab" href="#pr4" aria-controls="pr3" aria-expanded="true"><i class="fa fa-align-justify"></i> PR DISTRIBUTION</a>
                                    </li> 
                                </ul>
                                <div id="content-import-notif"></div>
                                <div class="tab-content px-1 pt-1">
                                    <div role="tabpanel" style="margin-top:2%" class="tab-pane active" id="pr1" aria-labelledby="pr1-tab1" aria-expanded="true"> 
                                        <table id="example" class="myTable border-double table mb-0 table-bordered table-responsive" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th width="10px">No<br>&nbsp;</th>
                                                    <th>REQUISITION HEADER ID</th>
                                                    <th>REQUISITION NUMBER</th>
                                                    <th>SUBDIVISI</th>
                                                    <th>NO. RUP</th>
                                                    <th>DOCUMENT <br>STATUS</th>
                                                    <th>PR TYPE<br>&nbsp;</th>
                                                    <th>METODE <br>PENGADAAN</th>
                                                    <th>JENIS <br>ANGGARAN</th>
                                                </tr>       
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $prheader  = new Integrate();
                                                $prheader->selectByParamsPRHeader(array(), -1, -1, "");


                                                $no=1;
                                                while($prheader->nextRow())     
                                                {   
                                                  echo '<tr>';
                                                  echo '<td class="text-center">'.$no.'</td>';
                                                  echo '<td>
                                                            '.$prheader->getField("requisition_header_id").' <br>
                                                            <small class="badge badge-primary" style="font-size:9px !important">File: '.$prheader->getField("import_file").'</small>
                                                        </td>'; 
                                                  echo '<td>'.$prheader->getField("requisition_number").'</td>'; 
                                                  echo '<td>'.$prheader->getField("subdivisi").'</td>'; 
                                                  echo '<td>'.$prheader->getField("nomor_rup").'</td>'; 
                                                  echo '<td>'.$prheader->getField("document_status").'</td>'; 
                                                  echo '<td>'.$prheader->getField("pr_type").'</td>'; 
                                                  echo '<td>'.$prheader->getField("metode_pengadaan").'</td>'; 
                                                  echo '<td>'.$prheader->getField("jenis_anggaran").'</td>'; 
                                                  echo '</tr>';
                                                $no++;
                                                } 
                                                ?>
                                            </tbody>
                                          </table>   
                                    </div> 
                                    <div role="tabpanel" style="margin-top:2%" class="tab-pane" id="pr2" aria-labelledby="pr2-tab2" aria-expanded="true">
                                        <table id="example" class="myTable border-double table mb-0 table-bordered table-responsive" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th width="10px">No</th>
                                                    <th>REQUISITION HEADER ID</th>
                                                    <th>ATTACHED DOCUMENT ID</th>
                                                    <th width="80%">FILE NAME</th>
                                                </tr>       
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $prheaderA  = new Integrate();
                                                $prheaderA->selectByParamsPRHeaderA(array(), -1, -1, "");


                                                $no=1;
                                                while($prheaderA->nextRow())     
                                                {   
                                                  echo '<tr>';
                                                  echo '<td class="text-center">'.$no.'</td>';
                                                  echo '<td>
                                                            '.$prheaderA->getField("requisition_header_id").' <br>
                                                            <small class="badge badge-primary" style="font-size:9px !important">File: '.$prheaderA->getField("import_file").'</small>
                                                        </td>'; 
                                                  echo '<td>'.$prheaderA->getField("attached_document_id").'</td>'; 
                                                  echo '<td>'.$prheaderA->getField("file_name").'</td>'; 
                                                  echo '</tr>';
                                                $no++;
                                                } 
                                                ?>
                                            </tbody>
                                        </table> 
                                    </div> 
                                    <div role="tabpanel" style="margin-top:2%" class="tab-pane" id="pr3" aria-labelledby="pr3-tab3" aria-expanded="true">
                                        <table id="example" class="myTable border-double table mb-0 table-bordered table-responsive" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th width="10px">No<br>&nbsp;</th>
                                                    <th>HEADER ID</th>
                                                    <th>LINE ID</th>
                                                    <th>TYPE</th>
                                                    <th>NUMBER</th>
                                                    <th>QUANTITY</th>
                                                    <th>UNIT PRICE</th>
                                                    <th>AMOUNT</th>
                                                    <th>STATUS</th>
                                                </tr>       
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $prheaderLine  = new Integrate();
                                                $prheaderLine->selectByParamsPRHeaderLine(array(), -1, -1, "");


                                                $no=1;
                                                while($prheaderLine->nextRow())     
                                                {   
                                                  echo '<tr>';
                                                  echo '<td class="text-center">'.$no.'</td>';
                                                  echo '<td>
                                                            '.$prheaderLine->getField("requisition_header_id").' <br>
                                                            <small class="badge badge-primary" style="font-size:9px !important">File: '.$prheaderLine->getField("import_file").'</small>
                                                        </td>'; 
                                                  echo '<td>'.$prheaderLine->getField("requisition_line_id").'</td>'; 
                                                  echo '<td>'.$prheaderLine->getField("line_type").'</td>'; 
                                                  echo '<td>'.$prheaderLine->getField("item_number").'</td>'; 
                                                  echo '<td>'.$prheaderLine->getField("quantity").'</td>'; 
                                                  echo '<td>'.$prheaderLine->getField("unit_price").'</td>'; 
                                                  echo '<td>'.$prheaderLine->getField("amount").'</td>'; 
                                                  echo '<td>'.$prheaderLine->getField("line_status").'</td>'; 
                                                  echo '</tr>';
                                                $no++;
                                                } 
                                                ?>
                                            </tbody>
                                          </table> 
                                    </div> 
                                    <div role="tabpanel" style="margin-top:2%" class="tab-pane" id="pr4" aria-labelledby="pr4-tab4" aria-expanded="true">
                                        <table id="example" class="myTable border-double table mb-0 table-bordered table-responsive" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th width="10px">No</th>
                                                    <th>DISTRIBUTION ID</th>
                                                    <th>LINE ID</th>
                                                    <th>CODE COMBINATION ID</th>
                                                    <th>QTY</th>
                                                    <th>AMOUNT</th>
                                                </tr>       
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $prDistribution  = new Integrate();
                                                $prDistribution->selectByParamsPRDistribution(array(), -1, -1, "");


                                                $no=1;
                                                while($prDistribution->nextRow())     
                                                {   
                                                  echo '<tr>';
                                                  echo '<td class="text-center">'.$no.'</td>';
                                                  echo '<td>
                                                            '.$prDistribution->getField("requisition_distribution_id").' <br>
                                                            <small class="badge badge-primary" style="font-size:9px !important">File: '.$prDistribution->getField("import_file").'</small>
                                                        </td>'; 
                                                  echo '<td>'.$prDistribution->getField("requisition_line_id").'</td>'; 
                                                  echo '<td>'.$prDistribution->getField("code_combination_id").'</td>'; 
                                                  echo '<td>'.$prDistribution->getField("distribution_quantity").'</td>'; 
                                                  echo '<td>'.$prDistribution->getField("distribution_amount").'</td>'; 
                                                  echo '</tr>';
                                                $no++;
                                                } 
                                                ?>
                                            </tbody>
                                        </table> 
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>



</div>