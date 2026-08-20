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
  <input type="text" class="form-control easyui-validatebox" required name="slaavaibility[]" id="reqAvaibility<?=$no?>" value="" id="reqAvaibility<?=$no?>" value="" OnFocus="CekDouble('reqAvaibility<?=$no?>')" OnKeyUp="CekDouble('reqAvaibility<?=$no?>')" OnBlur="CekDouble('reqAvaibility<?=$no?>')" maxlength="5">
  <small>Desimal gunakan titik (99.35)</small>
 </td>  
 <td>
  <input type="text" class="form-control easyui-validatebox" required name="slawaktu[]" id="<?=$no?>" value="">
 </td> 
 <td>
  <input type="text" class="form-control easyui-validatebox" required name="sladenda[]" id="reqDenda<?=$no?>" value="" id="reqDenda<?=$no?>" value="" OnFocus="FormatAngka('reqDenda<?=$no?>')" OnKeyUp="FormatUang('reqDenda<?=$no?>')" OnBlur="FormatUang('reqDenda<?=$no?>')" maxlength="2">
 </td> 
 <td>
  <input type="text" class="form-control easyui-validatebox" required name="slabiayamaintanance[]" id="reqBiaya<?=$no?>" value="" id="reqBiaya<?=$no?>" value="" OnFocus="FormatAngka('reqBiaya<?=$no?>')" OnKeyUp="FormatUang('reqBiaya<?=$no?>')" OnBlur="FormatUang('reqBiaya<?=$no?>')">
 </td> 
 <td>
  <input type="text" class="form-control easyui-validatebox" required name="slanilaidenda[]" id="reqNilai<?=$no?>" value="" id="reqNilai<?=$no?>" value="" OnFocus="FormatAngka('reqNilai<?=$no?>')" OnKeyUp="FormatUang('reqNilai<?=$no?>')" OnBlur="FormatUang('reqNilai<?=$no?>')">
 </td>   
 <!-- <td>
  <select class="form-control" name="slastatus[]">
    
  </select>
  <input type="text" class="form-control easyui-validatebox" required name="slastatus[]" id="<?php //$no?>" value="<?php // $status ?>">
 </td>   -->
<td>
  <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
 </td>
</tr>
