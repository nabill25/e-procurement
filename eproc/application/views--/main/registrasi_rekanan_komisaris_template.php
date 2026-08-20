<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$no_komisaris = rand();
?>
<tr>
     <td>
     	<input class="form-control easyui-validatebox" type="hidden"  name="reqRekananPengurusId[]" id="reqRekananPengurusId<?=$no_komisaris?>" value="" style="width:100%" />
     	<input class="form-control easyui-validatebox" type="text" required name="reqKomisarisNama[]" id="reqKomisarisNama<?=$no_komisaris?>" value="" style="width:100%" />
     </td>
     <td>
     	<input class="form-control easyui-validatebox" type="text" required name="reqKomisarisKTP[]" id="reqKomisarisKTP<?=$no_komisaris?>" value="" style="width:100%" />
     </td>
     <td>
     	<input class="form-control easyui-validatebox" type="text" required name="reqKomisarisJabatan[]" id="reqKomisarisJabatan<?=$no_komisaris?>" value="" style="width:100%" />
     </td>
     <td>
        <input type="file" name="reqLinkFile[]" id="reqLinkFile[]<?=$no?>" size="30" <? if($reqLinkFileTemp == "") { ?>  required class="easyui-validatebox" <? } ?> value=""  validType="fileType['pdf']" />
        <input type="hidden" name="reqLinkFileTemp[]" id="reqLinkFileTemp[]<?=$no?>" value="">
        <input type="hidden" name="reqLinkFileTempTipe[]" id="reqLinkFileTempTipe[]<?=$no?>" value="">
        <input type="hidden" name="reqLinkFileTempUkuran[]" id="reqLinkFileTempUkuran[]<?=$no?>" value="">
        <input type="hidden" name="reqLinkFileTempNama[]" id="reqLinkFileTempNama[]<?=$no?>" value="">
     </td>
     <td>
          <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
     </td>
</tr> 
                        