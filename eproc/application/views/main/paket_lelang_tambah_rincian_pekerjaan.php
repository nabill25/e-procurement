<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->libsession->cekSession();

// if($this->USER_TYPE_ID == "")
//     redirect("app");

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketPenawaran");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$paket_penawaran = new PaketPenawaran();
$paket_penawaran_child = new PaketPenawaran();

/* VARIABLES */
$reqMode = $this->input->get("reqMode");
$reqId = $this->input->get("reqId");

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqPermohonanId = $paketInfo->permohonan_paket_id;
$reqNilai = $paketInfo->nilai; 

$paket_penawaran->selectByParams(array("A.PAKET_ID" => $reqId, "ITEM_CHILD" => "0"));

if ($paket_penawaran->countRow() == 0 ) {
	// ikn 20241105 By Pass tidak musti upload BoQ
	$paket_penawaran_insert = new PaketPenawaran();
	$paket_penawaran_insert->setField("PAKET_ID", $reqId);
	$paket_penawaran_insert->setField("NAMA", $reqNama);
	$paket_penawaran_insert->setField("LAST_CREATE_USER", $this->USER_LOGIN_ID);
	$paket_penawaran_insert->setField("JUMLAH", CommaToNo($reqNilai));
	$paket_penawaran_insert->setField("PERMOHONAN_PAKET_ID", $reqPermohonanId);
	$paket_penawaran_insert->insert2();
}
	$paket_penawaran->selectByParams(array("A.PERMOHONAN_PAKET_ID" => $reqPermohonanId, "ITEM_CHILD" => "0"));


?>
<script type="text/javascript">
$(document).ready(function() {
	$(function(){
		$('#ff').form({
			url:'paket_penawaran_json/updateBoqFile',
			onSubmit:function(){
				// return $(this).form('validate');
				var v=$(this).form('validate');
		        if(v) showLoad();  // show the message box
		        return v;
			},
			success:function(data){
				alertSuccess();
				setTimeout(function () {
					document.location.href = 'main/index/paket_lelang_tambah_rincian_pekerjaan/?reqId=<?=$reqId?>';
				}, 1000);
				hideLoad();
			}
		});

	});

});

function createRowPembagianLot()
{
	$(function () {
		$.get("main/loadUrl/app/paket_lelang_tambah_pembagian_lot_template_rincian_pekerjaan", function (data) {
			$("#tbodyPembagianLot").append(data);
		});
	});
}

function addChild(idtbody)
{
	$(function () {
		//alert("sdada");return false;
		$.get("main/loadUrl/app/paket_lelang_tambah_pembagian_rincian_pekerjaan_child_template/?reqChild="+idtbody, function (data) {
			$("#child"+idtbody).append(data);
		});
	});
}

function summary()
{
	var reqTotal = 0;

	$("table input[id^=reqOEParent]").each(function() {
		var txtQuantity = $(this).attr("id").replace("reqOEParent", "reqQuantity");
		var txtJumlah = $(this).attr("id").replace("reqOEParent", "reqJumlahParent");

		var jumlah = (Number(FormatAngkaNumber($(this).val())) * Number(FormatAngkaNumber($("#"+txtQuantity).val())));

		if($(this).val() == "" || $(this).val() == "0")
			jumlah = Number(FormatAngkaNumber($("#"+txtJumlah).val()));
		else
		{
			$("#"+txtJumlah).val(FormatCurrency(jumlah));
		}
		reqTotal = reqTotal + jumlah;

	});

	var reqPPN = Number(reqTotal) * Number(0.10);
	reqPPN = Math.round(reqPPN);
	var reqTotalPPN = reqTotal + reqPPN;

	$("#reqTotal").val(FormatCurrency(reqTotal));
	$("#reqPPN").val(FormatCurrency(reqPPN));
	$("#reqTotalPPN").val(FormatCurrency(reqTotalPPN));
}

function summary_child()
{
	var reqTotalChild = 0;

	$("table input[id^=reqOEChild]").each(function() {
	var txtQuantityChild = $(this).attr("id").replace("reqOEChild", "reqQuantityChild");
	var txtJumlahChild = $(this).attr("id").replace("reqOEChild", "reqJumlahChild");

	var jumlahChild = (Number(FormatAngkaNumber($(this).val())) * Number(FormatAngkaNumber($("#"+txtQuantityChild).val())));

	reqTotalChild = reqTotalChild + jumlahChild;

	$("#"+txtJumlahChild).val(FormatCurrency(jumlahChild));

	});

	$("#reqTotalChild").val(FormatCurrency(reqTotalChild));
}

function summaryParent(idParent)
{
	var reqTotal = 0;

	$("table input[id^=reqOEChild"+idParent+"]").each(function() {

		var txtQuantity = $(this).attr("id").replace("reqOEChild", "reqQuantityChild");
		var txtJumlah = $(this).attr("id").replace("reqOEChild", "reqJumlahChild");

		var jumlah = (Number(FormatAngkaNumber($(this).val())) * Number(FormatAngkaNumber($("#"+txtQuantity).val())));

		reqTotal = reqTotal + jumlah;

		$("#"+txtJumlah).val(FormatCurrency(jumlah));

	});
	$("#reqJumlahParent"+idParent).val(FormatCurrency(reqTotal));
	$("#reqOEParent"+idParent).val("");
	$("#reqSatuan"+idParent).val("");
	$("#reqQuantity"+idParent).val("");

	var reqTotal = 0;


	$("table input[id^=reqJumlahParent]").each(function() {

		var jumlah = (Number(FormatAngkaNumber($(this).val())));

		reqTotal = reqTotal + jumlah;
	});

	$("#reqTotal").val(FormatCurrency(reqTotal));

	var reqPPN = Number(reqTotal) * Number(0.10);
	reqPPN = Math.round(reqPPN);
	var reqTotalPPN = reqTotal + reqPPN;

	$("#reqPPN").val(FormatCurrency(reqPPN));
	$("#reqTotal").val(FormatCurrency(reqTotal));
	$("#reqTotalPPN").val(FormatCurrency(reqTotalPPN));


}
</script>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Bill of Quantity (Daftar Kuantitas)
        	<!-- <div class="badge badge-glow badge-pill badge-warning">
            	<a onclick="createRowPembagianLot()" data-toogle=""><span class="fa fa-plus text-white font-medium-2 icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Data"></span> </a>
        	</div> -->
    	</h4>
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
        	<div class="alert alert-warning mb-2" role="alert">
				<strong>Perhatian!</strong> Periksa kembali File Rincian BoQ, Pastikan File terupload dan benar .
			</div>
	        <div class="table-responsive">
	          <table class="table table-striped mb-0">
	                <?php
					$no=1;
					$no_child = 1000;
					$totalHarga = 0;
					while($paket_penawaran->nextRow())
					{
						$no = $paket_penawaran->getField("ITEM_PARENT");
						$totalHarga += $paket_penawaran->getField("JUMLAH");
					?>

					<tr>
						<td style="width: 25%">Nama Paket:</td><td><?=$paket_penawaran->getField("ITEM")?></td>
					</tr>
					<tr>
						<td>Harga Perkiraan:</td>
						<td>
						<input type="text" name="reqOE[]" id="reqOEParent<?=$no?>" value="<?=numberToIna($paket_penawaran->getField("OE"))?>"
	                      			OnFocus="FormatAngka('reqOEParent<?=$no?>')"
	                                OnKeyUp="FormatUang('reqOEParent<?=$no?>'); summary()"
	                                OnBlur="FormatUang('reqOEParent<?=$no?>')" class="form-control span1" readonly="readonly" />
						</td>
					</tr>
					<tr>
						<td>Rincian Bill of Quantity (Daftar Kuantitas):</td>
						<td> 
                <input type="hidden" name="reqLinkFileTemp[]" id="reqLinkFileTemp[]<?=$no_child?>" value="<?=$paket_penawaran->getField("BOQ_FILE")?>"> 
              <?php
							$boqFile = $paket_penawaran->getField("BOQ_FILE");
	                        if($boqFile == "")
							{}
							else
							{
							?>
                <a href="uploads/boq/<?=$boqFile?>" class="badge badge-pill badge-primary" target="_blank"><span class="fa fa-download"></span> download file</a>
                <?php
							}
							?>
						</td>
					</tr>
					<tr>
						<td>Upload Rincian BoQ:</td>
						<td>
							<input type="file" name="reqLinkFile[]" id="reqLinkFile[]<?=$no_child?>"  style="width:auto" class="easyui-validatebox" validType="fileType['xls','xlsx']" required/>
                <small style="font-weight: bold"> <br>
              	<i class="fa fa-hand-o-right"></i> Format file .xls, .xlsx <br>
              	<i class="fa fa-hand-o-right"></i> Maksimal ukuran file 2MB </small>
              <input type="hidden" name="reqLinkFileTemp[]" id="reqLinkFileTemp[]<?=$no_child?>" value="<?=$paket_penawaran->getField("BOQ_FILE")?>">
              <?php
							$boqFile = $paket_penawaran->getField("BOQ_FILE");
	                        if($boqFile == "")
							{}
							else
							{
							?>
                <!-- <br>
                <div class="alert alert-primary mb-2" role="alert">
									Bill of Quantity (Daftar Kuantitas) berhasil di Upload <a href="uploads/boq/<?=$boqFile?>" class="badge badge-pill badge-primary" target="_blank"><span class="fa fa-download"></span> download file</a>
								</div> -->
                <?php
							}
							?>
						</td>
					</tr>
					<!-- <tr> -->
						<!-- <td>Kolom Total Rincian BoQ: <br> <span style="color: red;"><small>menggunakan huruf kapital</small></span></td> -->
						<!-- <td> -->
							<input type="hidden" name="reqBOQKolom[]" id="reqBOQKolom<?=$no?>" value="<?=($paket_penawaran->getField("BOQ_KOLOM"))?>" onkeyup="this.value=this.value.toUpperCase()" style="width:60px; height: 30px" />
							<!-- (ex: H-10) -->
						<!-- </td> -->
					<!-- </tr> -->
	                    <!-- <td> -->
	                      <input type="hidden" name="reqPaketPenawaranId[]" id="reqPaketPenawaranId<?=$no?>" value="<?=$paket_penawaran->getField("PAKET_PENAWARAN_ID")?>" />
	                      <input type="hidden" name="reqLot[]" id="reqLot<?=$no?>" value="<?=$paket_penawaran->getField("ITEM_NUMBER")?>" OnKeyUp="FormatUang('reqLot<?=$no?>');" class="form-control span1"  />
	                    <!-- </td> -->
	                      <input type="hidden" name="reqItem[]" id="reqItem<?=$no?>" value="<?=$paket_penawaran->getField("ITEM")?>" class="form-control span3"/>
	                    <!-- <td> -->
	                      <input type="hidden" name="reqSatuan[]" id="reqSatuan<?=$no?>" value="<?=$paket_penawaran->getField("SATUAN")?>" class="form-control span1" />
	                    <!-- </td> -->
	                    <!-- <td> -->
	                      <input type="hidden" name="reqQuantity[]" id="reqQuantity<?=$no?>"
	                      			value="<?=numberToIna($paket_penawaran->getField("QUANTITY"))?>"
	                                OnFocus="FormatAngka('reqQuantity<?=$no?>')"
	                                OnKeyUp="FormatUang('reqQuantity<?=$no?>'); summary()"
	                                OnBlur="FormatUang('reqQuantity<?=$no?>')" class="form-control span1" />
	                    <!-- </td> -->
	                      <input type="hidden" name="reqOE[]" id="reqOEParent<?=$no?>" value="<?=numberToIna($paket_penawaran->getField("OE"))?>"
	                      			OnFocus="FormatAngka('reqOEParent<?=$no?>')"
	                                OnKeyUp="FormatUang('reqOEParent<?=$no?>'); summary()"
	                                OnBlur="FormatUang('reqOEParent<?=$no?>')" class="form-control span1" />
	                      <input type="hidden" name="reqJumlah[]" id="reqJumlahParent<?=$paket_penawaran->getField("ITEM_PARENT")?>" value="<?=numberToIna($paket_penawaran->getField("JUMLAH"))?>"  OnKeyUp="summary()" readonly style="background-color:#EDEDED;" class="form-control span2"/>
	                        <!-- <a onclick="addChild('<?=$no?>')" class="btn-aksi"><i class="fa fa-plus" aria-hidden="true"></i></a> -->
	                      	<input type="hidden" name="reqParent[]" id="reqParent<?=$no?>" value="<?=$paket_penawaran->getField("ITEM_PARENT")?>" style="width:40px;" />
							  <?php
							  	$no_parent = $paket_penawaran->getField("ITEM_PARENT");
	                            $paket_penawaran_child = new PaketPenawaran();
	                            $paket_penawaran_child->selectByParams(array("A.PAKET_ID" => $reqId, "ITEM_CHILD" => $paket_penawaran->getField("ITEM_PARENT")));

	                            while($paket_penawaran_child->nextRow())
	                            {
	                            ?>
	                                        <input type="hidden" name="reqPaketPenawaranIdChild[]" id="reqPaketPenawaranId<?=$no_child?>" value="<?=$paket_penawaran_child->getField("PAKET_PENAWARAN_ID")?>" style="width:40px;" />
	                                        <input type="hidden" name="reqLotChild[]" id="reqLotChild<?=$no_child?>" value="" style="width:40px;" />
	                                      <input type="hidden" name="reqItemChild[]" id="reqItemChild<?=$no_child?>" value="<?=$paket_penawaran_child->getField("ITEM")?>"  style="width:100%;" />
	                                      <input type="hidden" name="reqSatuanChild[]" id="reqSatuanChild<?=$no_child?>" value="<?=$paket_penawaran_child->getField("SATUAN")?>" style="width:100%;" />
	                                      <input type="hidden" name="reqQuantityChild[]" id="reqQuantityChild<?=$no_parent?>-<?=$no_child?>" value="<?=$paket_penawaran_child->getField("QUANTITY")?>"  OnFocus="FormatAngka('reqQuantityChild<?=$no_parent?>-<?=$no_child?>')" OnKeyUp="FormatUang('reqQuantityChild<?=$no_parent?>-<?=$no_child?>'); summaryParent('<?=$no_parent?>');" OnBlur="FormatUang('reqQuantityChild<?=$no_parent?>-<?=$no_child?>')" style="width:100%;" />
	                                      <input type="hidden" name="reqOEChild[]" id="reqOEChild<?=$no_parent?>-<?=$no_child?>" value="<?=numberToIna($paket_penawaran_child->getField("OE"))?>"  OnFocus="FormatAngka('reqOEChild<?=$no_parent?>-<?=$no_child?>')" OnKeyUp="FormatUang('reqOEChild<?=$no_parent?>-<?=$no_child?>'); summaryParent('<?=$no_parent?>');" OnBlur="FormatUang('reqOEChild<?=$no_parent?>-<?=$no_child?>')" style="width:100%;" />
	                                    	<input type="hidden" name="reqJumlahChild[]" id="reqJumlahChild<?=$no_parent?>-<?=$no_child?>"
	                      						value="<?=numberToIna($paket_penawaran_child->getField("JUMLAH"))?>"  OnKeyUp="summary_child()" readonly style="width:100%;background-color:#EDEDED;" />
	                                        <input type="hidden" style="width:100px" />
	                                    	<!-- <a onClick="deleteData('paket_penawaran_json/delete_child/', '<?=$paket_penawaran_child->getField("PAKET_PENAWARAN_ID")?>');  summaryParent('<?=$no_parent?>');" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a> -->
	                                    	<input type="hidden" name="reqChild[]" id="reqChild" value="<?=$paket_penawaran_child->getField("ITEM_CHILD")?>" style="width:40px;" />

	                             <?php
								 	  $no_child++;
	                            }
	                            ?>
	                <?php
					  $no++;
					}
					?>
	                      <input type="hidden" name="reqTotal" id="reqTotal"  class="form-control"
	                          readonly style="width:100%;background-color:#EDEDED;" value="<?=numberToIna($totalHarga)?>" /></td>
	                <?php
	                $ppn = round($totalHarga * 0.11);
	                $totalHargaPPN = $totalHarga + $ppn;
	                $sppn = isset($paketInfo->sistem_ppn) ? $paketInfo->sistem_ppn : '';
	                if($sppn == "PISAH")
						$displayPPN = "";
					else
						$displayPPN = " style='display:none' ";
	                ?>
					  <input type="hidden" name="reqPPN" id="reqPPN" readonly style="width:100%; background-color:#EDEDED;" value="<?=numberToIna($ppn)?>" />
	                  <input type="hidden" name="reqTotalPPN" id="reqTotalPPN" readonly style="width:100%;background-color:#EDEDED;" value="<?=numberToIna($totalHargaPPN)?>" />
	          </table>
	        </div>
	        <div class="form-actions">
	        	<input type="hidden" name="reqId" value="<?=$reqId?>">
                <input type="hidden" name="submitSimpan" value="Simpan" />
			    <a href="main/index/paket_lelang_tambah/?reqId=<?=$reqId?>" class="<?= CLASS_BTN_DANGER ?> mr-1 text-white"> <?= BTN_KEMBALI ?> </a>
			    <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><i class="fa fa-check-square-o"></i>
            <?php
            if($boqFile == "") { echo 'Simpan'; } else { echo 'Update'; } ?>
          </button>
          <a class="<?= CLASS_BTN_PRIMARY ?> pull-right" href="<?= base_url('main/index/paket_lelang_tambah_jadwal/?reqId=').$reqId ?>"><?= BTN_LANJUT ?></a>
			    <!-- <button type="submit" ></button> -->
			  </div>
        </div>
      </div>
  	  </form>

    </div>
  </div>
</div>
