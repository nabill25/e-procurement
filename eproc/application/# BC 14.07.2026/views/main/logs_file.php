<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession();   

  ?>
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
      "aaSorting": [[1, 'desc']],
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
    });
  });
  $('.confirmation').on('click', function () {
        return confirm('Are you sure?');
    });
</script>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Logs Aktifiktas </h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a> 
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
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
                        <th width="10px">No</th>
                        <th width="70px">Tanggal</th>
                        <th>File Logs Aktifitas</th>
                        <th style="text-align: center; width: 100px">Download</th>
                    </tr>       
                </thead>
                <tbody>
                    <?php 
                    $no=1;
                    $files = glob('logs/hooks/*.{txt,log}', GLOB_BRACE);
                    $dir   = 'logs/hooks/';
                    // echo "<pre>"; print_r($files); die();
                    arsort($files);
                    foreach($files as $file) {
                      $fileName = str_replace($dir, "", $file);
                      $explod = explode("_", $file);
                      echo '<tr>';
                      echo '<td>'.$no.'</td>';
                      echo '<td>'.str_replace(".txt", "", $explod[1]).'</td>';
                      echo '<td>'.$fileName.'</td>';
                      echo '<td align="center">
                              <a href="'.$file.'" class="btn btn-primary btn-sm" target="_blank"><i class="ft-download" aria-hidden="true"></i></a>
                              <a href="users_base_json/logs_file_delete/'.$fileName.'" onclick="return confirm(\'Apakah benar anda akan menghapus file logs aktifitas tanggal '.str_replace(".txt", "", $explod[1]).'?\')" class="btn btn-danger btn-sm"><i class="ft-trash" aria-hidden="true"></i></a>
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