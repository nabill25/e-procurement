<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$reqIdSet = httpFilterRequest("reqId"); // contractingrekananid

$this->libsession->cekSession($reqIdSet);
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$reqProses = httpFilterRequest("reqProses");

$this->load->model("Contractingrekanan");
$contractingrekanan = new Contractingrekanan();

$contractingrekanan->selectByParams(array("A.PAKET_ID" => $reqIdSet));
$contractingrekanan->firstRow();
$contractingprosesid = $contractingrekanan->getField('CONTRACTINGPROSESID');
$reqId = $contractingrekanan->getField('CONTRACTINGREKANANID');

$paketInfo->getPaket($reqIdSet);
$reqUUID = $paketInfo->uuid;
// echo $reqId; die;

?>
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/media/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="lib/DataTables-1.10.7/extensions/Responsive/css/dataTables.responsive.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/ui/jqueryui.css">
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="lib/DataTables-1.10.7/extensions/Responsive/js/dataTables.responsive.js"></script>
<script src="<?=base_url()?>assets/new/js/scripts/ui/jquery-ui/navigations.js"></script>

<script type="text/javascript">
  $(document).ready(function() {
    $('#dokumenFileIdTable').DataTable({
      // "aaSorting": [[1, 'desc']],
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    });
  });
</script>

<style type="text/css">
  small { font-weight: bold; font-size: 11.5px }
  .dataTables_length { display: none; }
</style>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <table id="dokumenFileIdTable" class="border-double table mb-0 table-bordered mb-2" style="width: 100%">
            <?= $this->libkontrak->getTableFile($reqId," ") ?>
          </table>
          <div class="form-actions">
            <a href="main/index/paket_detil/?eid=<?=$reqIdSet?>&key=<?=$reqUUID?>" class="<?= CLASS_BTN_DANGER ?>"> <?= BTN_KEMBALI ?> </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
