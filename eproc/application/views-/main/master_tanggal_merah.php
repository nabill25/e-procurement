<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession();

$this->load->model("Mastertanggalmerah");
$master_tanggal = new Mastertanggalmerah();
$master_tanggal->selectByParams();
?>

<script type="text/javascript">
function createRowTanggal(a)
{
  $(a).html('<i class="fa fa-refresh"></i> Proses... ');
  $.get("main/loadUrl/main/tanggal_merah_template", function (data) {
    $("#tbodyTanggal").append(data);
    $(a).html('<i class="fa fa-plus"></i> Tambah Tanggal Merah');
  });
}

$(document).ready(function() {
  $(function(){
    $('#ff').form({
      url:'master_tanggal_json/add',
      onSubmit:function(){
        var v=$(this).form('validate');
        if(v) showLoad();  // show the message box
        return v;
      },
      success:function(data){
        if (data == 'Data berhasil disimpan') {
          alertSuccess2(data);
        } else {
          alertError2(data);
        }
        setTimeout(function() {
            location.reload(); }, 1800);
      },
      error: function (data) {
        setTimeout(function() {
            location.reload(); }, 1800);
      }
    });
  });
});
</script>

<style type="text/css">
  .fa.fa-trash {background: #da4453; padding: 5px 10px; border-radius: 10px;color: #fff;}
</style>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Setup Tanggal Merah <?= date('Y') ?></h4>
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
          <div class="table-responsive">
            <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Keterangan</th>
                    <th>Tanggal</th>
                    <th width="10px">#</th>
                  </tr>
                </thead>
                <tbody id="tbodyTanggal">
                <?php
                $i=1;
                while($master_tanggal->nextRow())
                {
                ?>
                <tr>
                  <td>
                    <input type="text" name="reqTmNote[]" class="form-control span9" value="<?=$master_tanggal->getField("TM_NOTE")?>">
                  </td>
                  <td align="center" class="kolom-aksi" width="10%">
                    <input type="text" name="reqTmDate[]" id="reqTmDate<?=$i?>" class="form-control span2 easyui-datebox" value="<?= dateToPageCheck($master_tanggal->getField("TM_DATE")); ?>" style="width: 150% !important"/>
                  </td>
                  <td>
                    <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
                  </td>
                </tr>
                <?php
                  $i++;
                }
                ?>
              </tbody>
            </table>
            <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"> <i class="fa fa-check-square-o"></i> Simpan</button>
            <a onclick="createRowTanggal(this)" class="<?= CLASS_BTN_DANGER ?>"> <i class="fa fa-plus"></i> Tambah Tanggal Merah </a>
          </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
