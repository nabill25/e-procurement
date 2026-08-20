<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
if($this->USER_TYPE_ID == "")
    redirect("main");

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

$paket_penawaran->selectByParams(array("A.PAKET_ID" => $reqId, "ITEM_CHILD" => "0"));

?> 
<script type="text/javascript">
$(document).ready(function() {
	$(function(){
		$('#ff').form({
			url:'paket_penawaran_json/add', 
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				//alert(data);return false;
				$.messager.alert('Info', data, 'info');
				document.location.href = 'main/index/paket_lelang_tambah_jadwal/?reqId=<?=$reqId?>';
			}
		});
		
	});
	
});

function createRowPembagianLot()
{
	$(function () {
		$.get("main/loadUrl/main/paket_lelang_tambah_pembagian_lot_template_rincian_pekerjaan", function (data) {
			$("#tbodyPembagianLot").append(data);
		});
	});	
}

function addChild(idtbody)
{
	$(function () {
		//alert("sdada");return false;
		$.get("main/loadUrl/main/paket_lelang_tambah_pembagian_rincian_pekerjaan_child_template/?reqChild="+idtbody, function (data) {
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
        <h4 class="card-title text-white">HPS
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
	        <div class="table-responsive">
	          <table class="table table-striped mb-0"> 
	          	<thead>
					<tr>
					  <!-- <th align="center" width="8%">No.</th> -->
					  <th align="center">Uraian</th>
					  <!-- <th align="center" width="10%">Satuan</th> -->
					  <!-- <th align="center" width="9%">Jumlah</th> -->
					  <th align="center">Total HPS <small>sudah PPN</small></th>
					  <th align="center">Total</th>
					  <th align="center">Rincian HPS</th>
					  <th width="2%">Aksi</th>
					</tr>                                          
				</thead>
	        	<tbody id="tbodyPembagianLot">
	                <?
					$no=1;
					$no_child = 1000;
					$totalHarga = 0;
					while($paket_penawaran->nextRow())
					{
						$no = $paket_penawaran->getField("ITEM_PARENT");
						$totalHarga += $paket_penawaran->getField("JUMLAH");
					?> 
	                  <tr id="tr<?=$no?>">
	                    <!-- <td> -->
	                      <input type="hidden" name="reqPaketPenawaranId[]" id="reqPaketPenawaranId<?=$no?>" value="<?=$paket_penawaran->getField("PAKET_PENAWARAN_ID")?>" />
	                      <input type="hidden" name="reqLot[]" id="reqLot<?=$no?>" value="<?=$paket_penawaran->getField("ITEM_NUMBER")?>" OnKeyUp="FormatUang('reqLot<?=$no?>');" class="form-control span1"  />
	                    <!-- </td> -->
	                    <td style="width: 30%">
	                      <input type="text" name="reqItem[]" id="reqItem<?=$no?>" value="<?=$paket_penawaran->getField("ITEM")?>" class="form-control span3"/>
	                    </td>
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
	                    <td>
	                      <input type="text" name="reqOE[]" id="reqOEParent<?=$no?>" value="<?=numberToIna($paket_penawaran->getField("OE"))?>"  
	                      			OnFocus="FormatAngka('reqOEParent<?=$no?>')" 
	                                OnKeyUp="FormatUang('reqOEParent<?=$no?>'); summary()" 
	                                OnBlur="FormatUang('reqOEParent<?=$no?>')" class="form-control span1" />
	                    </td>
	                    <td>
	                      <input type="text" name="reqJumlah[]" id="reqJumlahParent<?=$paket_penawaran->getField("ITEM_PARENT")?>" 
	                      		value="<?=numberToIna($paket_penawaran->getField("JUMLAH"))?>"  OnKeyUp="summary()" readonly style="background-color:#EDEDED;" class="form-control span2"/>
	                    </td>
	                    <td>
	                        <input type="file" name="reqLinkFile[]" id="reqLinkFile[]<?=$no_child?>"  style="width:190px" class="easyui-validatebox" validType="fileType['xls']" />
	                        <small> <br>Format file .xls & Maksimal ukuran file 2MB </small>
	                        <input type="hidden" name="reqLinkFileTemp[]" id="reqLinkFileTemp[]<?=$no_child?>" value="<?=$paket_penawaran->getField("BOQ_FILE")?>">
	                        <br>
	                        Kolom : <input type="text" name="reqBOQKolom[]" id="reqBOQKolom<?=$no?>" 
	                      			value="<?=($paket_penawaran->getField("BOQ_KOLOM"))?>"   style="width:50px;" /> (ex: H-10)
	                        <?
							$boqFile = $paket_penawaran->getField("BOQ_FILE");
	                        if($boqFile == "")
							{}
							else
							{
							?>
	                        <br>
	                        file : <a href="uploads/boq/<?=$boqFile?>" target="_blank">download</a>
	                        <?
							}
							?>
	                    </td>
	                    <td align="center">
	                        <a onClick="deleteData('paket_penawaran_json/delete/', '<?=$paket_penawaran->getField("PAKET_PENAWARAN_ID")?>')" 
	                            class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
	                        <!-- <a onclick="addChild('<?=$no?>')" class="btn-aksi"><i class="fa fa-plus" aria-hidden="true"></i></a> -->
	                      	<input type="hidden" name="reqParent[]" id="reqParent<?=$no?>" value="<?=$paket_penawaran->getField("ITEM_PARENT")?>" style="width:40px;" />
	                    </td>
	                  </tr>
	                  <tr>
	                      <td colspan="8" class="ada-sub">
	                        <table class="sub-rincian-pekerjaan">
	                             <tbody id="child<?=$paket_penawaran->getField("ITEM_PARENT")?>">
							  <?
							  	$no_parent = $paket_penawaran->getField("ITEM_PARENT");
	                            $paket_penawaran_child = new PaketPenawaran();
	                            $paket_penawaran_child->selectByParams(array("A.PAKET_ID" => $reqId, "ITEM_CHILD" => $paket_penawaran->getField("ITEM_PARENT")));
	                            
	                            while($paket_penawaran_child->nextRow())
	                            {
	                            ?>
	                               <tr>
	                                   <td>
	                                        <input type="hidden" name="reqPaketPenawaranIdChild[]" id="reqPaketPenawaranId<?=$no_child?>" value="<?=$paket_penawaran_child->getField("PAKET_PENAWARAN_ID")?>" style="width:40px;" />
	                                        <input type="hidden" name="reqLotChild[]" id="reqLotChild<?=$no_child?>" value="" style="width:40px;" />
	                                    </td>
	                                    <td>
	                                      <input type="text" name="reqItemChild[]" id="reqItemChild<?=$no_child?>" value="<?=$paket_penawaran_child->getField("ITEM")?>"  style="width:100%;" />
	                                    </td>
	                                    <td>
	                                      <input type="text" name="reqSatuanChild[]" id="reqSatuanChild<?=$no_child?>" value="<?=$paket_penawaran_child->getField("SATUAN")?>" style="width:100%;" />
	                                    </td>
	                                    <td>
	                                      <input type="text" name="reqQuantityChild[]" id="reqQuantityChild<?=$no_parent?>-<?=$no_child?>" value="<?=$paket_penawaran_child->getField("QUANTITY")?>"  OnFocus="FormatAngka('reqQuantityChild<?=$no_parent?>-<?=$no_child?>')" OnKeyUp="FormatUang('reqQuantityChild<?=$no_parent?>-<?=$no_child?>'); summaryParent('<?=$no_parent?>');" OnBlur="FormatUang('reqQuantityChild<?=$no_parent?>-<?=$no_child?>')" style="width:100%;" />
	                                    </td>
	                                    <td>
	                                      <input type="text" name="reqOEChild[]" id="reqOEChild<?=$no_parent?>-<?=$no_child?>" value="<?=numberToIna($paket_penawaran_child->getField("OE"))?>"  OnFocus="FormatAngka('reqOEChild<?=$no_parent?>-<?=$no_child?>')" OnKeyUp="FormatUang('reqOEChild<?=$no_parent?>-<?=$no_child?>'); summaryParent('<?=$no_parent?>');" OnBlur="FormatUang('reqOEChild<?=$no_parent?>-<?=$no_child?>')" style="width:100%;" />
	                                    </td>
	                                    <td>
	                                    	<input type="text" name="reqJumlahChild[]" id="reqJumlahChild<?=$no_parent?>-<?=$no_child?>" 
	                      						value="<?=numberToIna($paket_penawaran_child->getField("JUMLAH"))?>"  OnKeyUp="summary_child()" readonly style="width:100%;background-color:#EDEDED;" />
	                                    </td>
	                                    <td>
	                                        <input type="hidden" style="width:100px" />                                                                    
	                                    </td>
	                                    <td align="center">
	                                    
	                                    	<a onClick="deleteData('paket_penawaran_json/delete_child/', '<?=$paket_penawaran_child->getField("PAKET_PENAWARAN_ID")?>');  summaryParent('<?=$no_parent?>');" 
	                                        	class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
	                                    	<input type="hidden" name="reqChild[]" id="reqChild" value="<?=$paket_penawaran_child->getField("ITEM_CHILD")?>" style="width:40px;" />
	                                   
	                                    </td>
	                                  </tr>
	                             <?
								 	  $no_child++;
	                            }
	                            ?> 
	                              </tbody>
	                        </table>
	                      </td>
	                  </tr>                                              
	                <?
					  $no++;
					}
					?>
	              </tbody>
	              <tfoot>
	                <tr> 
	                  <td align="right" colspan="2" style="text-align: center"><div style="margin-top:4px; font-weight: bold">GRAND TOTAL&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
	                  <td >
	                  <div style="margin-top:4px;">
	                      <input type="text" name="reqTotal" id="reqTotal"  class="form-control"
	                          readonly style="width:100%;background-color:#EDEDED;" value="<?=numberToIna($totalHarga)?>" /></td>
	                  </div> 
	                  </td>   
	                  <td align="center"></td>
	                  <td></td>
	                </tr>     
	                <?
	                $ppn = round($totalHarga * 0.10); 
	                $totalHargaPPN = $totalHarga + $ppn;
	                if($paketInfo->sistem_ppn == "PISAH")
						$displayPPN = "";
					else
						$displayPPN = " style='display:none' ";											
	                ?>
					<tr <?=$displayPPN?>>
					  <td align="center"></td>
					  <td align="center"></td>
					  <td align="center"></td>
					  <td align="right" colspan="2"><div style="margin-top:4px;">PPN&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
					  <td><div style="margin-top:4px; margin-bottom:4px;"><input type="text" name="reqPPN" id="reqPPN" 
						  readonly style="width:100%; background-color:#EDEDED;" value="<?=numberToIna($ppn)?>" />
	                  	 </div>
	                  </td>
					  <td align="center"></td>
					  <td></td>
					</tr>                                        
					<tr <?=$displayPPN?>>
					  <td align="center"></td>
					  <td align="center"></td>
					  <td align="center"></td>
					  <td align="right" colspan="2"> <div style="margin-bottom:4px;">GRAND TOTAL + PPN&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
					  <td >
	                  <div style="margin-bottom:4px;">
	                  <input type="text" name="reqTotalPPN" id="reqTotalPPN" 
						  readonly style="width:100%;background-color:#EDEDED;" value="<?=numberToIna($totalHargaPPN)?>" />
	                  </div>
	                  </td>
					  <td align="center"></td>
					  <td></td>
					</tr>                                
	              </tfoot>                                         
	          </table>   
	        </div>
	        <div class="form-actions">
	        	<input type="hidden" name="reqId" value="<?=$reqId?>">
                <input type="hidden" name="submitSimpan" value="Simpan" />
			    <a href="main/index/paket_lelang_tambah/?reqId=<?=$reqId?>" class="btn btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a> 
			    <button type="submit" class="btn btn-primary pull-right">Lanjut <i class="fa fa-arrow-right"></i></button>
			  </div> 
        </div>
      </div>
  	  </form>

    </div>
  </div> 
</div>  