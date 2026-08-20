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
var thisURLRKAFileRemote = "<?= base_url('integrasi_json/rkaFileRemote') ?>";
var thisURLDownloadRKA = "<?= base_url('integrasi_json/rkaDownloadToFTP') ?>";
var thisURLImportRKA = "<?= base_url('integrasi_json/rkaImport') ?>";

$(document).ready(function() {
    setTimeout(function() {
        reloadFileRemote();
    }, 1000);
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

function prDownload(file,id) {
    if (confirm('Apakah Anda yakin ingin melakukan import file '+file+' ini ?')) {
        $("#"+id).html('<i class="fa fa-spinner"></i>');
        $("#content-import-notif").html('');
        $("#content-import-notif").show();
        $.get(thisURLDownloadRKA+'?file='+file, function(response) {
            $("#content-import-notif").html(response);
            // Import File to local
            prImport(file,id);
        })
        .always(function() { // Finish
        });
    } else {}
}

function prImport(file,id) {
    $.get(thisURLImportRKA+'?file='+file, function(response) {
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
</script>
 
    <div class="row">
      <div class="col-md-12">
        <div class="form-group alert" style="border: 1px solid #dee2e6!important">
          <div class="float-left">
            <h5><b>Monitoring Integrasi R K A</b></h5>
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

            </div>
        </div>
    </div>

    <div class="content-detached content-right">
        <div class="content-body">
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <!-- <div class="card-header">
                            <h4 class="card-title">Database R K A</h4>
                            <a class="heading-elements-toggle"><i class="ft-ellipsis-h font-medium-3"></i></a>
                        </div> -->
                        <div class="card-content">
                            <div class="card-body">
                                <div id="content-import-notif"></div>
                                <table id="example" class="myTable border-double table mb-0 table-bordered table-responsive" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th width="10px">No<br>&nbsp;</th>
                                            <th>RKA KEY <br> &nbsp;</th>
                                            <th>START <br> DATE YEAR</th>
                                            <th>SEGMENT1<br> &nbsp;</th>
                                            <th>SEGMENT1 DESC<br> &nbsp;</th>
                                            <th>SEGMENT2<br> &nbsp;</th>
                                            <th>SEGMENT2 DESC<br> &nbsp;</th>
                                            <th>BUDGET AMT<br> &nbsp;</th>
                                            <th>REMAIN AMT<br> &nbsp;</th>
                                        </tr>       
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $dataRKA  = new Integrate();
                                        $dataRKA->selectByParamsRKA(array(), -1, -1, "");

                                        $no=1;
                                        while($dataRKA->nextRow())     
                                        {   
                                          echo '<tr>';
                                          echo '<td class="text-center">'.$no.'</td>';
                                          echo '<td>
                                                    '.$dataRKA->getField("rka_key").' <br>
                                                    <small class="badge badge-primary" style="font-size:9px !important">File: '.$dataRKA->getField("import_file").'</small>
                                                </td>'; 
                                          echo '<td>'.$dataRKA->getField("start_date_year").'</td>'; 
                                          echo '<td>'.$dataRKA->getField("segment1").'</td>'; 
                                          echo '<td>'.$dataRKA->getField("segment1_desc").'</td>'; 
                                          echo '<td>'.$dataRKA->getField("segment2").'</td>'; 
                                          echo '<td>'.$dataRKA->getField("segment2_desc").'</td>'; 
                                          echo '<td>'.$dataRKA->getField("budget_amt").'</td>'; 
                                          echo '<td>'.$dataRKA->getField("remain_amt").'</td>'; 
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
            </section>
        </div>
    </div>



</div>