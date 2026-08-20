<?php  
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

// if($this->USER_TYPE_ID == "")
//     redirect("main"); 
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
    $('#example').DataTable();
    $('#btnBackup').on('click', function () { 
      $.messager.confirm('Konfirmasi',"Apakah anda ingin mem-backup database hari ini?",function(r){
        if (r){
          $.getJSON("master_backup_json/backup",
            function(data){
              alertSuccess2(data.data);
          });       
        }
      });    
      
    }); 
  } );
  $('.confirmation').on('click', function () {
    return confirm('Are you sure?');
  });
</script>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Backup Database </h4>
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
             <div class="row">
              <div class="form-group col-md-6 mb-2"> 
              </div> 
              <div class="form-group col-md-6 mb-2 text-right">
                  <a id="btnBackup" class="btn btn-primary text-white"><i class="icon-reload"></i> Create Backup</a>
              </div> 
            </div>
            <div class="table-responsive">
              <table class="table table-striped mb-0" id="example"> 
                <thead>
                    <tr>
                        <th width="10px">No</th>
                        <th>Tanggal</th>
                        <th>Database</th>
                        <th style="text-align: center">Download</th>
                    </tr>       
                </thead>
                <tbody>
                    <?php 
                    $no=1;
                    $files = glob('uploads/backup/*.{zip,log}', GLOB_BRACE);
                    $dir   = 'uploads/backup/';
                    foreach($files as $file) {
                      $fileName = str_replace($dir, "", $file);
                      $explod = explode("_", $file);
                      echo '<tr>';
                      echo '<td>'.$no.'</td>';
                      echo '<td>'.str_replace(".zip", "", $explod[1]).'</td>';
                      echo '<td>'.$fileName.'</td>';
                      echo '<td align="center">
                              <a href="'.$file.'" class="btn btn-primary" target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a>
                              <a href="users_base_json/logs_file_delete/'.$fileName.'" onclick="return confirm(\'Apakah benar anda akan menghapus file logs aktifitas tanggal '.str_replace(".zip", "", $explod[1]).'?\')" class="btn btn-danger btn-xs" target="_blank"><i class="fa fa-trash" aria-hidden="true"></i></a>
                            </td>';
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