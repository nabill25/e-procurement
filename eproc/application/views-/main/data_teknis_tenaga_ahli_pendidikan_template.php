<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$no = rand();
?>
<tr>
    <td>
    	<link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
		 <script>
            $('input[id^="reqPendidikan<?=$no?>"]').combobox({  
                required:true
            });
        </script> 
    	<input type="text" name="reqPendidikan[]" required class="easyui-combobox span2"  id="reqPendidikan<?=$no?>"
        data-options="valueField:'id',textField:'text',url:'pendidikan_json/combo'"  value="" />
    </td>
    <td>
    	<input type="text" class="form-control easyui-validatebox span6"  required name="reqJurusan[]" id="reqJurusan<?=$no?>" value=""/>
    </td>
    <td>
    	 <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
    </td>
</tr>   
                        