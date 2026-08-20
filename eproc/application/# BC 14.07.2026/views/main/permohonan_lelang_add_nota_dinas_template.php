<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$no_komisaris = rand();
$no = rand();
?>


<tr>
  <link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
  <script>
  $('input[id^="reqLinkFile<?=$no?>"]').validatebox({
    required:true
  });
</script>
     <td>
        <input class="form-control easyui-validatebox" type="hidden"  name="reqPermohonanPaketFileId[]" id="reqPermohonanPaketFileId<?=$no?>" value="" />
        <input class="form-control easyui-validatebox span4" type="text" required name="reqJudul[]" id="reqJudul<?=$no?>" value=""/>
     </td>
     <td>
        <input type="file" name="reqLinkFile[]" id="reqLinkFile<?=$no?>" size="30" class="easyui-validatebox" validType="fileType['pdf','zip']" required/>
        <input type="hidden" name="reqLinkFileTemp[]" id="reqLinkFileTemp[]<?=$no?>" value="">
        <input type="hidden" name="reqLinkFileTempTipe[]" id="reqLinkFileTempTipe[]<?=$no?>" value="">
        <input type="hidden" name="reqLinkFileTempUkuran[]" id="reqLinkFileTempUkuran[]<?=$no?>" value="">
        <input type="hidden" name="reqLinkFileTempNama[]" id="reqLinkFileTempNama[]<?=$no?>" value="">
        <!-- temp :  -->
     </td>
     <td></td>
     <td></td>
     <td>&nbsp;</td>
     <td>
      <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
     </td>
</tr>
