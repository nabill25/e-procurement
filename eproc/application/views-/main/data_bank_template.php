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
    $('input[id^="reqBankId<?=$no?>"]').combobox({  
      required:true
    });
    $('#reqBankId<?=$no?>').combobox({
    filter: function(q, row){
      var opts = $(this).combobox('options');
      return row[opts.textField].toLowerCase().indexOf(q.toLowerCase()) >= 0;
    }
    });
  </script>
  <td>
    <input type="text" name="reqBankId[]" id="reqBankId<?=$no?>" class="easyui-combobox" data-options="valueField:'id',textField:'text',url:'bank_json/combo'"  value="" required="required" style="width: 200% !important" />
 </td> 
 <td>
  <input type="text" name="reqNoRekening[]" value="" title="No rekening harus diisi" class="form-control easyui-validatebox span4" required  />
 </td>  
 <td>
  <input type="text" name="reqAtasNama[]" value="" title="Pemilik rekening harus diisi" class="form-control easyui-validatebox span4" required  />
 </td> 
 <td>
  <input type="text" name="reqBankCabang[]" value="" title="Cabang harus diisi" class="form-control easyui-validatebox span4" required  />
 </td>   
 <td>
  <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
 </td>
</tr>
