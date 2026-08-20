<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("UsersBase");
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$this->load->model("PaketPihakLain");

$paket_pihak_lain = new PaketPihakLain();

/* VARIABLES */
$reqMode = $this->input->post("reqMode");
$reqId = $this->input->get("reqId");
$submitSimpan = $this->input->post("submitSimpan");
$reqPihakLain = $_POST["reqPihakLain"];
$reqLoginId = $_POST["reqLoginId"];


$paket_pihak_lain->selectByParams(array("PAKET_ID" => $reqId));

?> 

<script type="text/javascript">
$(document).ready(function() {
	
	$(function(){
		$('#ff').form({
			url:'paket_pihak_lain_json/add', 
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				//alert(data);return false;
				$.messager.alert('Info', data, 'info');
			}
		});
		
	});
	
});

</script>

<div class="span12">
 <div class="card">
  <h3 class="card-heading simple">Unit Fungsional & Konsultan</h3>
   <div class="card-body">
    <div class="control-group">
     <div class="controls">
      <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
        <a title="Tambah" class="btn btn-primary" id="btnAdd" onClick="openAdd('main/loadUrl/main/daftar_pihak_lain_cari/?reqId=<?=$reqId?>');" style="margin-bottom: 10px"><span class="fa fa-plus"></span> Tambah</a>
        <table class="table table-bordered table-hover" id="tbl_bidang">
            <thead>
            <tr class="judul-kolom">
              <th>Nama</th>
              <th>Username</th>
              <th>Tipe</th>
              <th>Aksi</th>
            </tr>
            </thead >
             <tbody id="tbodyPihakLain">
            <?
                $i=1;
                while($paket_pihak_lain->nextRow())
                {
                //$input = $paket_pihak_lain->getField("USER_NAMA").";".$paket_pihak_lain->getField("USER_LOGIN").";".$paket_pihak_lain->getField("TIPE_NAMA");
            ?>
            <tr>
                <td><?= $paket_pihak_lain->getField("USER_NAMA")?></td>
                <td><?= $paket_pihak_lain->getField("USER_LOGIN")?></td>
                <td><?= $paket_pihak_lain->getField("TIPE_NAMA")?></td>
                <td style="width: 30px">
                    <input type="hidden" name="reqPihakLain[]" value="<?= $paket_pihak_lain->getField("PAKET_PIHAK_LAIN_ID")?>">
                    <input type="hidden" name="reqLoginId[]" value="<?= $paket_pihak_lain->getField("USER_LOGIN_ID")?>">
                    <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
                </td>
            </tr>
             <?
                  $i++;
            }
            ?>
          </tbody>
        </table> 
        
        <div>
            <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger">Kembali</a>
            <input type="hidden" name="submitSimpan" value="Simpan" />
            <input type="hidden" name="reqId" value="<?=$reqId?>">
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>   
     </div>
    </div>
   </div>
   <div class="card-actions">
   </div>
 </div> 
</div>
