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
     	<input class="form-control easyui-validatebox span2" type="hidden"  name="reqRekananSahamId[]" id="reqRekananSahamId<?=$no?>" value="" />
        <input class="form-control easyui-validatebox span2" type="text" required name="reqPemegangSaham[]" id="reqPemegangSaham<?=$no?>" value=""/>
     </td>
     <td><input class="form-control easyui-validatebox span2" type="text" required name="reqNomorKTP[]" id="reqNomorKTP<?=$no?>" value=""/></td>
     <td><input class="form-control easyui-validatebox span2" type="text" required name="reqAlamat[]" id="reqAlamat<?=$no?>" value=""/></td>
     <td><input class="form-control easyui-validatebox span1" type="text" required name="reqPersentase[]" id="reqPersentase<?=$no?>" value="" onkeypress="return isNumberKey(event)"/></td>
     <td> 
      <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
</tr> 
                        