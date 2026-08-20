<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$no_direksi = rand();
?>
<tr>
     <td>
     	<input class="form-control easyui-validatebox" type="hidden"  name="reqRekananPengurusId[]" id="reqRekananPengurusId<?=$no_direksi?>" value="" style="width:100%" />
     	<input class="form-control easyui-validatebox" type="text" required name="reqDireksiNama[]" id="reqDireksiNama<?=$no_direksi?>" value="" style="width:100%" />
     </td>
     <td>
     	<input class="form-control easyui-validatebox" type="text" required name="reqDireksiKTP[]" id="reqDireksiKTP<?=$no_direksi?>" value="" style="width:100p%" />
     </td>
     <td>
     	<input class="form-control easyui-validatebox" type="text" required name="reqDireksiJabatan[]" id="reqDireksiJabatan<?=$no_direksi?>" value="" style="width:100%" />
     </td>
     <td>
        <input type="file" name="reqLinkFileDireksi[]" id="reqLinkFileDireksi[]<?=$no_direksi?>" size="30" <? if($reqLinkFileTemp == "") { ?>  required class="easyui-validatebox" <? } ?> value="" />
        <input type="hidden" name="reqLinkFileDireksiTemp[]" id="reqLinkFileDireksiTemp[]<?=$no_direksi?>" value="">
        <input type="hidden" name="reqLinkFileDireksiTempTipe[]" id="reqLinkFileDireksiTempTipe[]<?=$no_direksi?>" value="">
        <input type="hidden" name="reqLinkFileDireksiTempUkuran[]" id="reqLinkFileDireksiTempUkuran[]<?=$no_direksi?>" value="">
        <input type="hidden" name="reqLinkFileDireksiTempNama[]" id="reqLinkFileDireksiTempNama[]<?=$no_direksi?>" value="">
     </td>
     <td>
           <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
     </td>
</tr> 
                        