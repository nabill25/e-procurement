<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$milliseconds = round(microtime(true) * 1000);
$no = $milliseconds;
?>
 <tr id="tr<?=$no?>">
    <td>
    	<input type="hidden" name="reqPaketPenawaranId[]" id="reqPaketPenawaranId<?=$no?>" value="" style="width:40px;" />
      	<input type="text" name="reqLot[]" id="reqLot<?=$no?>" value="" class="form-control span1" OnKeyUp="FormatUang('reqLot<?=$no?>');"  />
    </td>
    <td>
      <input type="text" name="reqItem[]" id="reqItem<?=$no?>" value="" class="form-control span3"/>
    </td>
    <td>
      <input type="text" name="reqSatuan[]" id="reqSatuan<?=$no?>" value="" class="form-control span1"/>
    </td>
    <td>
      <input type="text" name="reqQuantity[]" id="reqQuantity<?=$no?>" value=""  OnFocus="FormatAngka('reqQuantity<?=$no?>')" OnKeyUp="FormatUang('reqQuantity<?=$no?>'); summary()" OnBlur="FormatUang('reqQuantity<?=$no?>')" class="form-control span1"/>
    </td>
    <td>
      <input type="text" name="reqOE[]" id="reqOEParent<?=$no?>" value=""  OnFocus="FormatAngka('reqOEParent<?=$no?>')" OnKeyUp="FormatUang('reqOEParent<?=$no?>'); summary()" OnBlur="FormatUang('reqOEParent<?=$no?>')" class="form-control span1"/>
    </td>
    <td>
      <input type="text" name="reqJumlah[]" id="reqJumlahParent<?=$no?>" value="" readonly  class="form-control span2" style="background-color:#EDEDED;"  />
    </td>
    <td>
        
        <input type="file" name="reqLinkFile[]" id="reqLinkFile<?=$no?>"  style="width:190px" class="easyui-validatebox" validType="fileType['xls']" />
        <input type="hidden" name="reqLinkFileTemp[]" id="reqLinkFileTemp<?=$no?>" value="">
        <br>
        Kolom : <input type="text" name="reqBOQKolom[]" id="reqBOQKolom<?=$no?>" 
                value=""   style="width:50px;" />(ex: H-10)
    </td>
    <td align="center">
        <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
        <!-- <a onclick="addChild('<?=$no?>')" class="btn-aksi"><i class="fa fa-plus" aria-hidden="true"></i></a> -->
        <input type="hidden" name="reqParent[]" value="<?=$no?>"  />
    </td>
</tr>
<tr>
	<td colspan="8" class="ada-sub">
    	<table class="sub-rincian-pekerjaan">
        	<tbody id="child<?=$no?>"></tbody>
        </table>
    </td>
</tr>
                        