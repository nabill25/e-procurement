<?php
/**
 * @package     eProcurement Application
 * @author      eproc2025
 * @since       25. Version 3.1
 * 
 */

$no = rand();
$this->load->model(array("Contractingdeliverable"));

$contractingdeliverable = new Contractingdeliverable();

$reqMetodePembayaran  = $this->input->get("reqMetodePembayaran"); // Metode Pembayaran 1:Sekaligus 2:Termin
$reqId  = $this->input->get("reqId"); // Contracting Rekanan ID

$contractingdeliverable->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));

?>
<tr>
   <link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
   <script>
        $('input[id^="reqNo<?=$no?>"]').validatebox({ required:true }); 
        $('input[id^="reqKet<?=$no?>"]').validatebox({ required:true });
        $('#reqPayDateDari<?=$no?>, #reqPayDateSampai<?=$no?>').datebox({
          // editable: false
        }); 
    </script>   
    <td>
      <select class="form-control" name="reqDeliverableId[]">
        <?php 
        while($contractingdeliverable->nextRow()) {  ?>
          <option value="<?= $contractingdeliverable->getField("DELIVERABLEID") ?>"><?= $contractingdeliverable->getField("DELIVERY_NAMA") ?></option>
        <?php 
        } ?>
      </select>
    </td>  
    <?php 
      if ($reqMetodePembayaran != '1') { // 1:Sekaligus  ?>
      <td>
        <input type="text" class="form-control easyui-validatebox" required name="payteminke[]" id="<?=$no?>" value="">
       </td> 
      <?php 
      } else { ?>
        <input type="hidden" class="form-control easyui-validatebox" required name="payteminke[]" id="<?=$no?>" value="Sekaligus">
      <?php 
      } ?>
    <td>
    <input type="text" class="form-control easyui-validatebox" required name="payketerangan[]" id="reqKet<?=$no?>" value="">
    </td>  
    <td>
    <input type="text" class="form-control easyui-validatebox paynilai" required name="paynilai[]" id="reqNilai<?=$no?>" value="" OnFocus="FormatAngka('reqNilai<?=$no?>')" OnKeyUp="hitungTotal(); FormatUang('reqNilai<?=$no?>')" onchage="hitungTotal();" OnBlur="FormatUang('reqNilai<?=$no?>')">
    </td>  
    <td>
    <input type="text" class="form-control easyui-validatebox payprogres" required name="payprogres[]" id="reqpayprogres<?=$no?>" OnFocus="FormatAngka('reqpayprogres<?=$no?>')" OnFocus="FormatAngka('reqpayprogres<?=$no?>')" OnKeyUp="hitungProgress();FormatUang('reqpayprogres<?=$no?>')" onchage="hitungProgress()" OnBlur="FormatUang('reqpayprogres<?=$no?>')" maxlength="3" value="">
    </td>  
     <td class="text-center">
      <input type="text" name="reqPayDateDari[]" id="reqPayDateDari<?=$no?>" class="form-control easyui-datebox" value="" style="width: 110%"/> <br><span style="margin:0 2%">s/d</span><br>
      <input type="text" name="reqPayDateSampai[]" id="reqPayDateSampai<?=$no?>" class="form-control easyui-datebox" value="" style="width: 110%"/>
     </td> 
    <td>
    <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
    </td>
</tr>
