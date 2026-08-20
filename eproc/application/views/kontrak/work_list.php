<?php
$this->libsession->cekSession();

$this->load->library("libworklist");
$libworklist = new libworklist();

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

  var groupingTable = $('.row-grouping').DataTable({
        "columnDefs": [{
            "visible": false,
            "targets": 1
        }],
        "order": [
            [1, 'asc']
        ],
        "displayLength": 25,
        "drawCallback": function(settings) {
            var api = this.api();
            var rows = api.rows({
                page: 'current'
            }).nodes();
            var last = null;

            api.column(1, {
                page: 'current'
            }).data().each(function(group, i) {
                if (last !== group) {
                    $(rows).eq(i).before(
                        '<tr class="group"><td colspan="2">' + group + '</td></tr>'
                    );

                    last = group;
                }
            });
        }
    });

    $('.row-grouping tbody').on('click', 'tr.group', function() {
        var currentOrder = table.order()[0];
        if (currentOrder[0] === 2 && currentOrder[1] === 'asc') {
            table.order([0, 'desc']).draw();
        }
        else {
            table.order([0, 'asc']).draw();
        }
    });
});
</script>
<style type="text/css">
  .dataTables_length { display: none; }
</style>
<section id="backColor">
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;">
        <div class="card-header">
          <h4 class="card-title">WORK LIST</h4>
          <div class="heading-elements" id="tombol">
            <span></span>
          </div>
        </div>
        <div class="card-body area-datatable">
          <div class="form-body">
            <div class="col-lg-12 col-xl-12"> 
             <?php 
             switch ($this->USER_TYPE_ID) {
                case '12': // Pengelola Kontrak

                  // PERSIAPAN
                  if ($this->LEVEL_KONTRAK == '1') { // Staff Persiapan
                    if ($this->PENUNJUK_PIC == '1') { // Kasi
                      // echo 'Kasi Persiapan';
                      echo $libworklist->worklistPersiapanKasi();
                    } else {
                      // echo 'Staff Persiapan';
                      echo $libworklist->worklistPersiapanStaff();
                    }
                  }

                  // PENGENDALI
                  if ($this->LEVEL_KONTRAK == '2') { // Staff Pengendali
                    if ($this->PENUNJUK_PIC == '1') { // Kasi
                      // echo 'Kasi Pengendali';
                      echo $libworklist->worklistPengendaliKasi();
                    } else {
                      // echo 'Staff Pengendali';
                      echo $libworklist->worklistPengendaliStaff();
                    }
                  }

                  // PENYELESAI
                  if ($this->LEVEL_KONTRAK == '3') { // Staff Penyelesai
                    if ($this->PENUNJUK_PIC == '1') { // Kasi
                      // echo 'Kasi Penyelesai';
                      echo $libworklist->worklistPenyelesaiKasi();
                    } else {
                      // echo 'Staff Penyelesai';
                      echo $libworklist->worklistPenyelesaiStaff();
                    }
                  }

                  break;
                
                case '20': // Pemeriksa Kontrak
                  echo $libworklist->worklistKasubditKontrak();
                  break;
                
                case '28': // PPK
                  echo $libworklist->worklistPPK();
                  break;

                default:
                  // code...
                  break;
              } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
