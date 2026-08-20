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
   <link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
   <script>
        $('input[id^="reqPesan<?=$no?>"]').validatebox({  
            required:true
        }); 
    </script>
      <td>   
        <input type="text" class="form-control easyui-validatebox" required name="reqPesan[]" id="reqPesan<?=$no?>" value="">
       </td>  
      <td>   
        <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
      </td>
</tr>
