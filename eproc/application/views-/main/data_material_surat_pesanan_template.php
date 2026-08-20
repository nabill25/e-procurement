<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$no = rand();
$reqConRekId = $this->input->get("reqConRekId"); // CONTRACTINGREKANANID
?>
<tr>
    <td>
        <link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
         <script>
            $('input[id^="reqMaterial<?=$no?>"]').combobox({
                required:true
            });
        </script> 

        <input type="text" name="reqMaterial[]" required class="easyui-combobox span2"  id="reqMaterial<?=$no?>"
        data-options="valueField:'id',textField:'text',url:'contracting_json/comboMaterial/<?= $reqConRekId ?>',onSelect: function(rec){
            $.get('contracting_json/comboMaterialData/'+rec.id, function (data) {
                const obj = JSON.parse(data);
                $('#reqDeskripsi<?=$no?>').val(obj.keterangan);
                $('#reqhargaSatuan<?=$no?>').val(FormatNumberya(obj.harga_satuan));
                $('#reqNama<?=$no?>').val(obj.nama);
                $('#reqSatuan<?=$no?>').val(obj.satuan);
                $('#reqSifat<?=$no?>').val(obj.sifat);
                $('#reqQtyOld<?=$no?>').val(obj.qty);
                calculate(<?=$no?>);
            });
        }"  value="" style="width: 300px;" />
        <input type="hidden" class="form-control easyui-validatebox span6"  required name="reqNama[]" id="reqNama<?=$no?>"/>
        <input type="hidden" class="form-control easyui-validatebox span6"  required name="reqSifat[]" id="reqSifat<?=$no?>"/>
        <input type="hidden" class="form-control easyui-validatebox span6"  required name="reqQtyOld[]" id="reqQtyOld<?=$no?>"/>
        <!-- Sifat= 
            1:Berubah (Qty boleh diatas Vol/Qty yang sudah di tentukan, 
            2: Tetap (Vol/Qty tidak boleh lebih dari Vol/Qty yang sudah di tentukan) 
        -->
    </td>
    <td>
        <input type="text" class="form-control easyui-validatebox span6"  required name="reqQty[]" id="reqQty<?=$no?>" OnFocus="FormatAngka('reqQty<?=$no?>');" OnKeyUp="calculate(<?=$no?>); sum();" OnBlur="FormatUang('reqQty<?=$no?>');" onchange="calculate(<?=$no?>); sum();"  value="0"/>
    </td>
    <td>
        <input type="text" class="form-control easyui-validatebox span6"  required name="reqSatuan[]" id="reqSatuan<?=$no?>" value="" readonly/>
    </td>
    <td>
        <input type="text" class="form-control easyui-validatebox span6"  required name="reqhargaSatuan[]" id="reqhargaSatuan<?=$no?>" value="" readonly/>
        <!-- <input type="text" class="form-control easyui-validatebox span6"  required name="reqDeskripsi[]" id="reqDeskripsi<?php // echo $no?>" value="" readonly/> -->
    </td>
    <td>
        <input type="text" class="form-control easyui-validatebox span6 sumTotal"  required name="reqTotal[]" id="reqTotal<?=$no?>" value="0" readonly/>
    </td>
    <td>
         <a title="#" onclick="$(this).parent().parent().remove(); sum();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
    </td>
</tr>
