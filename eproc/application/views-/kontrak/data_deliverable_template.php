<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

$no = rand();
?>

<tr class="gelap">
  <link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
  <script>
       $('input[id^="reqLingkup<?=$no?>"]').validatebox({ required:true });
       $('input[id^="reqDeliveryname<?=$no?>"]').validatebox({ required:true });
       // $('input[id^="reqTanggalDeliveryDari<?=$no?>"]').validatebox({  required:true });
       // $('input[id^="reqTanggalDeliverySampai<?=$no?>"]').validatebox({  required:true });
        $('#reqTanggalDeliveryDari<?=$no?>, #reqTanggalDeliverySampai<?=$no?>').datebox({
          // editable: false
        }); 
   </script>
 <td>
  <input type="text" class="form-control easyui-validatebox" required name="deliveryname[]" id="reqDeliveryname<?=$no?>" value="">
 </td> 
 <td>
  <input type="text" class="form-control easyui-validatebox" required name="lingkup[]" id="reqLingkup<?=$no?>" value="">
 </td>
 <td class="text-center">
  <input type="text" name="reqTanggalDeliveryDari[]" id="reqTanggalDeliveryDari<?=$no?>" class="form-control easyui-datebox" value="" style="width: 110%"/> <span style="margin:0 2%">s/d</span>
  <input type="text" name="reqTanggalDeliverySampai[]" id="reqTanggalDeliverySampai<?=$no?>" class="form-control easyui-datebox" value="" style="width: 110%"/>
 </td> 
 <td>
  <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
 </td>
</tr>

