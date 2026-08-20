<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */
$this->libsession->cekSession();
/* VARIABLES */
$reqMode = httpFilterRequest("reqMode");
$reqId = httpFilterRequest("reqId");

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("PaketRekanan");
$this->load->model("Paket");
$this->load->library("KMail");
$this->load->model("PaketPanitia");
$this->load->model("EvaluasiSyaratDaftar");
$this->load->model("PaketEvaluasiSyaratDaftar");

$paket_rekanan = new PaketRekanan();
$paket_rekanan_count = new PaketRekanan();

$paket_rekanan->selectByParamsPenunjukanLang(array("PAKET_ID" => $reqId));
$prcount = $paket_rekanan_count->getCountByParams(array("PAKET_ID" => $reqId));
$paketInfo->getPaket($reqId);
$reqUUID = $paketInfo->uuid;

?>
<script type="text/javascript">
$(document).ready(function() {

	$(function(){
		$('#ff').form({
			url:'paket_rekanan_json/undang_pemilihan',
			onSubmit:function(){
				var v=$(this).form('validate');
          if(v) {
            showLoad();
            return v;
          } else {
            hideLoad();
            return false;
          }
			},
			success:function(data){
        alertSuccess2(data);
				setTimeout(function() {
          location.reload();
					// window.location = '<?php echo "main/index/paket_lelang_tambah_rekanan/?reqId=$reqId"; ?>';
          // hideLoad();
        }, 2000);
			}
		});

	});

	$('#btnKirim').on('click', function () {
		$.messager.defaults.ok = 'Ya';
		$.messager.defaults.cancel = 'Tidak';
		$.messager.confirm('Konfirmasi',"Kirim Undangan ke Penyedia?",function(r){
		  if (r){
			  var win = $.messager.progress({
									  title:'<?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?>',
									  msg:'Proses kirim undangan via email...'
								  });

			  $.get("paket_rekanan_json/undang_pemilihan_email/?reqId=<?=$reqId?>", function( data ) {
				  $.messager.progress('close');
				  $.messager.alert('Informasi',data, 'info');
			  });
		  }
	  });
	});

});

</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Penyedia
            <?php
            if ($paketInfo->publish_paket != '1') { // sebelum publish ?>
            <div class="badge badge-pill badge-warning">
              <a id="btnAdd" onClick="openAdd('main/loadUrl/main/rekanan_lookup/?reqId=<?=$reqId?>');" data-toogle=""><span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Data"></span> Tambah </a>
            </div>
            <?php
            } ?>
        </h4>
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
          <div class="card mb-1">
            <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
                <table class="table table-bordered table-hover" id="tbl_bidang">
                	<thead>
                    <tr>
                      <td>Nama Penyedia</td>
                      <td>Alamat</td>
                      <td>Email</td>
                      <td width="5%">Aksi</td>
                    </tr>
                    </thead>
                 	<tbody id="tbodyRekanan">
    				        <?php
                    $i=1;
                    while($paket_rekanan->nextRow())
                    {
                    ?>
                      <tr>
                        <td><?=$paket_rekanan->getField("REKANAN")?></td>
                        <td><?=$paket_rekanan->getField("ALAMAT")?></td>
                        <td><?=$paket_rekanan->getField("EMAIL")?></td>
                        <td>
                          <input type="hidden" name="reqRekananId[]" id="reqRekananId<?=$i?>" value="<?=$paket_rekanan->getField("REKANAN_ID")?>">
                          <?php
                          if ($paketInfo->publish_paket != '1') { // sebelum publish ?>
                            <a title="#" onclick="$(this).parent().parent().remove();">
                              <?= ICON_DELETE ?>
                            </a>
                          <?php
                          } else { echo "-"; } ?>
                        </td>
                      </tr>
                    <?php
                    }
                    ?>
                  	</tbody>
                </table>

                <div>
                	<input type="hidden" id="reqMetodeLelangId" value="<?=$paketInfo->metode_lelang_id?>" />
                	<input type="hidden" name="reqId" value="<?=$reqId?>">
                	<input type="hidden" name="submitSimpan" value="Simpan" />
                </div>

                <div class="form-actions">
                    <a href="main/index/paket_detil/?eid=<?=$reqId?>&key=<?= $reqUUID ?>" class="<?= CLASS_BTN_DANGER ?> mr-1 text-white"> <?= BTN_KEMBALI ?> </a>
                  <?php
                  if ($paketInfo->publish_paket != '1') { // sebelum publish ?>
                  <button type="submit" class="mr-1 <?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
                  <?php
                  }

                  if ($prcount >= 1 && $paketInfo->publish_paket != '1') {
                    ?>
                    <?php
                  } ?>
                </div>
    		    </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
