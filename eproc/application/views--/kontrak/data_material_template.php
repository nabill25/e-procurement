<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$no = rand();
?>
<tr class="gelap">
  <link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
  <script>
    $('input[id^="satuanid<?=$no?>"]').combobox({  
      required:true
    });
    $('input[id^="reqMaterial<?=$no?>"]').validatebox({ required:true }); 
    $('input[id^="reqhargasatuan<?=$no?>"]').validatebox({ required:true });
    $(document).ready(function() {
      var sf = test();
      if (sf == '1') {
        $('.check-qty').val('1');
        $('.check-qty').prop('readonly', true);
      } else if (sf == '2') {
        $('.check-qty').prop('readonly', false);
      }
    });
  </script> 
 <td>
  <input type="text" class="form-control easyui-validatebox" required name="material[]" id="reqMaterial<?=$no?>" value="">
 </td> 
 <!-- <td width="30%">
  <input type="text" class="form-control easyui-validatebox" required name="keterangan[]" id="reqMaterial<?=$no?>" value="">
 </td>  -->
<td>
  <input type="text" class="form-control easyui-validatebox check-qty" required name="qty[]" id="<?=$no?>" value="">
</td> 
<td>
  <input type="text"  name="satuanid[]" id="satuanid<?=$no?>" class="easyui-combobox" title="Nama harus diisi" 
    data-options=" required: true,
                    filter: function(q, row) { return row['text'].toLowerCase().indexOf(q.toLowerCase()) != -1; },
                    valueField: 'id', textField: 'text',
                    url: 'contracting_json/comboSatuanData'
                    " value="" required="required" style="width:100px;">
</td> 
 <td>
  <input type="text" class="form-control easyui-validatebox" required name="hargasatuan[]" id="reqhargasatuan<?=$no?>" OnFocus="FormatAngka('reqhargasatuan<?=$no?>')" OnFocus="FormatAngka('reqhargasatuan<?=$no?>')" OnKeyUp="FormatUang('reqhargasatuan<?=$no?>')" OnBlur="FormatUang('reqhargasatuan<?=$no?>')" value="">
 </td>   
 <td>
  <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
 </td>
</tr>
