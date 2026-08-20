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
        $('input[id^="reqTopic<?=$no?>"]').validatebox({  
            required:true
        });
        $('input[id^="reqTopicSemula<?=$no?>"]').validatebox({  
            required:true
        });
        $('input[id^="reqTopicMenjadi<?=$no?>"]').validatebox({  
            required:true
        });
    </script>
      <td>   
        <input type="text" class="form-control easyui-validatebox" required name="topic[]" id="reqTopic<?=$no?>" value="">
       </td> 
       <td>
        <input type="text" class="form-control easyui-validatebox" required name="topicsemula[]" id="reqTopicSemula<?=$no?>" value="">
       </td>  
       <td>
        <input type="text" class="form-control easyui-validatebox" required name="topicmenjadi[]" id="reqTopicMenjadi<?=$no?>" value="">
       </td>  
      <td>
        <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><?= ICON_DELETE ?></a>
       </td>
</tr>
