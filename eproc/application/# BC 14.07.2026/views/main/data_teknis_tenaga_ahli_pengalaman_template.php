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
      <td><input type="text" class="form-control easyui-validatebox" required name="reqPekerjaan[]" id="reqPekerjaan0<?=$no?>" value=""></td>
      <td><input type="text" class="form-control easyui-validatebox" required name="reqPosisi[]" id="reqPosisi0<?=$no?>" value=""></td>
      <td><input type="text" class="form-control easyui-numberbox" required name="reqLama[]" id="reqLama0<?=$no?>" value="" style="width:80px" maxlength="2"></td>
      <td><input type="text" class="form-control easyui-numberbox" required name="reqJumlahTahun[]" id="reqJumlahTahun0<?=$no?>" value="" style="width:80px" maxlength="4"></td>
      <td><input type="text" class="form-control easyui-validatebox" required name="reqInstansi[]" id="reqInstansi0<?=$no?>"></td>
      <td><input type="text" class="form-control easyui-validatebox" required name="reqNamaPerusahaan[]" id="reqNamaPerusahaan<?=$no?>" ></td>
      <td>
       <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
    </td>
    <?php /*?><td><a style="cursor:pointer" onclick="addRowTAPengalaman('dataTableTAPengalaman')"><img src="images/icn_add.gif" width="16" height="16" border="0" /></a></td><?php */?>
</tr>
