<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$reqId = httpFilterRequest("reqId");

if (!$reqId) {
	redirect(base_url());
}

$this->libsession->cekSession();

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Masterdokumentemplate");
$this->load->model("Rekananurlvalidasiallow");
$this->load->model("Rekanan");

$rekanan = new Rekanan();
$rekanan->selectByParams(array('A.REKANAN_ID' => $reqId),-1,-1);
$rekanan->firstRow();
$rek_nama = $rekanan->getField('NAMA');

$cekrekananurlvalidasiallow = new Rekananurlvalidasiallow();
$cekrekananurlvalidasiallow->selectByParams(array('REKANAN_ID' => $reqId),-1,-1);

$cekrekananurl = new Rekananurlvalidasiallow();
$cekrekananurl->selectByParamsURL(array("ISPARENT" => '1'),-1,-1, "", " ORDER BY ID ASC");

?>

<script type="text/javascript">
$(document).ready(function() {

  $(function(){
    $('#ff').form({
      url:'rekanan_json/set_akses',
      onSubmit:function(){
        return $(this).form('validate');
      },
      success:function(data){
        //alert(data);return false;
        alertSuccess2(data);
        // document.location.href = 'main/index/daftar_rekanan_akses/?reqId=<?=$reqId?>';
      }
    });

  });

});

function checkAll(ele) {
 var checkboxes = document.getElementsByTagName('input');
 if (ele.checked) {
  for (var i = 0; i < checkboxes.length; i++) {
   if (checkboxes[i].type == 'checkbox' ) {
    checkboxes[i].checked = true;
   }
  }
 } else {
  for (var i = 0; i < checkboxes.length; i++) {
   if (checkboxes[i].type == 'checkbox') {
    checkboxes[i].checked = false;
   }
  }
 }
}

function checkParent(ele,a) {
 if (ele.checked) {
 	console.log("ya");
	$('.'+a).attr("checked", true);
 } else {
 	console.log("tidak");
	$('.'+a).attr("checked", false);
 }
}


</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Hak Akses <?= $rek_nama ?></h4>
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
				 		<form id="ff" method="post" novalidate enctype="multipart/form-data">
          	<table class="table table-bordered">
          			<div class="form-check">
                	<input class="form-check-input" type="checkbox" onchange="checkAll(this)" name="reqUrl[]" id="check--parent0" >
								  <label class="form-check-label" for="check--parent0" style="cursor: pointer">
      							<h5>Pilih Semua</h5>
								  </label>
								</div>
			      			<?php
			      			echo '<input type"hidden" value="'.$reqId.'" name="reqId" style="display:none">';
			      			while ($cekrekananurl->nextRow()) {
			      				$cekrekananurl2 = new Rekananurlvalidasiallow();
										$cekrekananurl2->selectByParamsURL(array("ISPARENT" => '2', "PARENTID" => $cekrekananurl->getField('ID')),-1,-1, "", " ORDER BY ID ASC");
									?>
				      			<tr>
			          			<th colspan="3" style="background-color:#e3ebf3"><?= $cekrekananurl->getField('TITLE'); ?></th>
			          			<!-- <th>Akses</th> -->
				      			</tr>
				      				<?php
				      				while ($cekrekananurl2->nextRow())
				      				{
				      					$cekrekananurl0 = new Rekananurlvalidasiallow();
												$cekrekananurl0->selectByParamsURL(array("ISPARENT" => '0', "PARENTID" => $cekrekananurl2->getField('ID')),-1,-1, "", " ORDER BY ID ASC");

			      						$cekrekananurl2Check = new Rekananurlvalidasiallow();
												$cekrekananurl2Check->selectByParamsAllow(array("REKANAN_ID" => $reqId, "URL" => $cekrekananurl2->getField('ID')),-1,-1, "", " ORDER BY ID ASC");
				      					?>
				      					<tr>
				      						<td width="10px"><span class="fa fa-long-arrow-right"></span></td>
					          			<td width="30%">
					          				<div class="form-check">
				          					<input class="form-check-input" type="checkbox" name="reqUrl[]" id="check--parent<?= $cekrekananurl2->getField('ID'); ?>" value="<?= $cekrekananurl2->getField('ID') ?>" <?php if ($cekrekananurl2Check->countRow() > 0) { echo "checked"; } ?> onchange="checkParent(this,'subparent<?= $cekrekananurl2->getField('ID'); ?>')" data-num="">
														  <label class="form-check-label" for="check--parent<?= $cekrekananurl2->getField('ID'); ?>" style="cursor:pointer">
				          							<?= $cekrekananurl2->getField('TITLE'); ?>
														  </label>
														</div>
			          					</td>
					          			<td width="auto">
					          				<?php
					          				while ($cekrekananurl0->nextRow()) {
					          					$cekrekananurl0Check = new Rekananurlvalidasiallow();
															$cekrekananurl0Check->selectByParamsAllow(array("REKANAN_ID" => $reqId, "URL" => $cekrekananurl0->getField('ID')),-1,-1, "", " ORDER BY ID ASC");?>
					          					<div class="form-check">
					          						<input class="form-check-input subparent<?= $cekrekananurl2->getField('ID'); ?>" type="checkbox" name="reqUrl[]" value="<?= $cekrekananurl0->getField('ID') ?>" style="cursor: pointer" id="check--sub-parent<?= $cekrekananurl0->getField('ID') ?>" <?php if ($cekrekananurl0Check->countRow() > 0) { echo "checked"; } ?>>
															  <label class="form-check-label" for="check--sub-parent<?= $cekrekananurl0->getField('ID') ?>" style="cursor:pointer">
					          							<?= $cekrekananurl0->getField('TITLE'); ?>
															  </label>
															</div>
					          				<?php
					          				} ?>
					          			</td>
						      			</tr>
				      				<?php
			      				  }
			      			 } ?>

          	</table>
            <a href="<?= base_url('main/index/daftar_rekanan_belum_valid?status=01') ?>" class="<?= CLASS_BTN_DANGER ?> mr-1"><?= BTN_KEMBALI ?> </a>
	      		<button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
	      		</form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
