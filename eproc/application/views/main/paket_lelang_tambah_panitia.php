<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("PaketPanitia");
$this->load->model("SKPanitia");

$paket_panitia = new PaketPanitia();
$sk_panitia = new SKPanitia();

/* VARIABLES */
$reqMode = httpFilterRequest("reqMode");
$reqId = $this->input->get("reqId");

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;

$paket_panitia->selectByParams(array("PAKET_ID" => $reqId));

?>

<script type="text/javascript">
$(document).ready(function() {
	
	$(function(){
		$('#ff').form({
			url:'paket_panitia_json/add', 
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				//$.messager.alert('Info', data, 'info');	
				//alert(data);return false;
				document.location.href = "main/index/paket_detil/?reqId=<?=$reqId?>";
			}
		});
		
	});
	
});

</script>

<div class="span12">
 <div class="card">
  <h3 class="card-heading simple">Daftar Pelaksana</h3>
   <div class="card-body">
    <div class="control-group">
     <div class="controls">
        <a title="Tambah" style="margin-bottom: 10px" class="btn btn-primary" id="btnAdd" onClick="openAdd('main/loadUrl/main/panitia/?reqId=<?=$reqId?>');">
          <span class="fa fa-plus"></span> Tambah</a>
        <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
        	<table class="table table-bordered" id="tbl_bidang">
            <thead>
              <tr class="judul-kolom">
                <th>Nama </th>
                <th>NIP</th>
                <th>Jabatan</th>
                <th>Aksi</th>
              </tr>                                          
            </thead>
            <tbody id="tbodyPanitia">
              <?
              while($paket_panitia->nextRow())
              {
              ?>
                <tr>
                	<td><?=$paket_panitia->getField("NAMA")?></td>
                	<td><?=$paket_panitia->getField("NIP")?></td>
                  <td><?=$paket_panitia->getField("JABATAN")?></td>
                  <td>
                  	<input type="hidden" id= name="reqNIP[]" name="reqNIP[]" value="<?=$paket_panitia->getField("NIP")?>">
                      <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
                  </td>
                </tr>
              <?
              }
              ?>
            </tbody>
          </table> 
        
      	  <div>
          	<input type="hidden" name="reqId" value="<?=$reqId?>">
          	<input type="hidden" name="submitSimpan" value="Simpan" />
          	<a href="main/index/paket_lelang_tambah_syarat/?reqId=<?=$reqId?>" class="btn btn-danger">Kembali</a>
              <input type="submit" value="Simpan" class="btn btn-primary">
          </div>
        </form>
     </div>
    </div>
   </div>
   <div class="card-actions">
   </div>
 </div> 
</div>