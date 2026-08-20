<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->model("Rekanan"); $rekanan = new Rekanan(); 
$rekanan->selectByParams(array(),'','', 'AND COALESCE(STATUS_VALIDASI, 0) != 1 AND TANGGAL_HAPUS IS NULL'); // 0=Belum 1=Validasi 2=Hapus 3=Kirim ke Verifikator
if ($rekanan->countRow() > 0) {
  while($rekanan->nextRow())
  {
    $rekananArr[] = '"'.$rekanan->getField('KODE').' :: '.$rekanan->getField('NAMA').'"'; 
  }
  $impRekananArr = implode(',',$rekananArr);
} else {
  $impRekananArr = '" .:: Tidak ada data ::. "';
}

 ?>
<script type="text/javascript">
$(document).ready(function() {

	$(function(){
		$('#ff').form({
			onSubmit:function(){
				if($(this).form('validate'))
					$.redirect('main/index/validasi_rekanan', {'reqKode': $("#reqKodeSeachPenyedia").val()});
			},
		});

	});

});

var countries = [<?= $impRekananArr ?>];
</script>

<style type="text/css">
  #reqKodeSeachPenyediaautocomplete-list {
    position: relative;
    margin-top: 10px;
    background: #fff;
    width: 100%;
  }
  #reqKodeSeachPenyediaautocomplete-list div {
    margin: 5px;
  }
</style>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Validasi Rekanan </h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a> 
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <div class="alert alert-danger mb-2">Masukkan nomor nama atau registrasi penyedia untuk proses validasi:</div>
          <div class="card-block">
            <fieldset>
              <div class="input-group">
                <input type="text" id="reqKodeSeachPenyedia" name="reqKode" class="form-control" placeholder="Cari Penyedia . . .">
                <div class="input-group-append">
                  <button class="btn btn-danger" type="submit">Cek Rekanan</button>
                </div>
              </div>
            </fieldset>
          </div>  
        </div>
      </div>
      </form>
    
    </div>
  </div> 
</div>   