<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();
?>
<script type="text/javascript">
function directBC(a,b) {
    showLoad();
    $.ajax({
        url : "<?php echo base_url('dbbackup/backup')?>/" + a + '/' + b,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
          $.messager.alert('Info', data.respone, 'info');
          setTimeout(function () {
            window.location.href = '<?= base_url('main/index/backup') ?>';
          }, 1000);
          hideLoad();
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          $.messager.alert('Info', data.respone, 'info');
          setTimeout(function () {
            window.location.href = '<?= base_url('main/index/backup') ?>';
          }, 1000);
          hideLoad();
        }
    });
}

function exportData() {
  window.location.href = '<?= base_url() ?>dbbackup/files';
}

</script>
<div class="row">
  <!-- <div class="col-md-4 offset-md-4"> -->
  <div class="col-md-4 offset-md-2">
    <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">
        <div class="card-content" style="padding: 5%">
            <div class="card-body" style="padding: .7em">
                <div class="media">
                    <div class="media-body text-center">
                        <i class="fa fa-database fa-4x" style="margin-bottom: 10px"></i>
                        <h1 class="orange">DATABASE</h1>
                        <?php
                        $db_name = 'backup-on-' . date("Y-m-d") . '.zip'; // file name
                        $save  = 'backup/db/' . $db_name; // dir name backup output destination
                        if (file_exists($save)) {
                          echo "Hari ini Database sudah dibackup";
                        } else {
                          echo '<a onclick="directBC(\'db\',\''.date("Y-m-d").'\')"><span class="fa fa-refresh"></span> Backup hari ini </a>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card box-shadow-0 animated zoomIn" data-appear="appear" data-animation="zoomIn">
        <div class="card-content" style="padding: 5%">
            <div class="card-body" style="padding: .7em">
                <div class="media">
                    <div class="media-body text-center">
                        <i class="fa fa-book fa-4x" style="margin-bottom: 10px"></i>
                        <h1 class="orange">FILES</h1>
                        <?php
                        
                        $db_name2 = 'backup-on-' . date("Y-m-d") . '.zip'; // file name
                        $save2  = 'backup/file/' . $db_name2; // dir name backup output destination
                        if (file_exists($save2)) {
                          echo "Hari ini File sudah dibackup";
                        } else {
                          // echo '<a onclick="directBC(\'file\',\''.date("Y-m-d").'\')"><span class="fa fa-download"></span> Download </a>';
                          echo '<a onclick="exportData()"><span class="fa fa-download"></span> Download </a>';
                        }
                        
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
