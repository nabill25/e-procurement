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
            $('input[id^="reqKeahlian<?=$no?>"]').validatebox({  
                required:true
            });
            $('#reqTglBerlaku<?=$no?>').datebox({
    editable: false
  });
        </script> 
		 <script>
            $('input[id^="reqNoSertifikat<?=$no?>"]').validatebox({  
                required:true
            });
        </script> 
		 <script>
            $('input[id^="reqLinkFile<?=$no?>"]').validatebox({  
                required:true
            });
        </script>     
    <td>
    <input class="form-control easyui-validatebox span3" required type="text" name="reqKeahlian[]" id="reqKeahlian<?=$no?>" value="" />
    </td>
    <td>
        <input class="form-control easyui-validatebox span3" required name="reqNoSertifikat[]" type="text" id="reqNoSertifikat<?=$no?>" value=""/>
    </td>
    <td>
       <input type="file" name="reqLinkFile[]" id="reqLinkFile<?=$no?>" size="30" <?php if($reqLinkFileTemp == "") { ?> required <?php } ?> class="easyui-validatebox"  validType="fileType['pdf']"  value="" />
       <input type="hidden" name="reqLinkFileTemp[]" id="reqLinkFileTemp[]<?=$no?>" value="">
       <input type="hidden" name="reqLinkFileTempTipe[]" id="reqLinkFileTempTipe[]<?=$no?>" value="">
       <input type="hidden" name="reqLinkFileTempUkuran[]" id="reqLinkFileTempUkuran[]<?=$no?>" value="">
       <input type="hidden" name="reqLinkFileTempNama[]" id="reqLinkFileTempNama[]<?=$no?>" value="">
       <!-- <br>File : <?php //$reqLinkFileTempNama?> -->
   <?php /*?> <input required class="form-control easyui-validatebox" type="file" name="reqLinkFile[]" id="reqLinkFile<?=$no?>" size="0" /><?php */?>
    </td>
    <td>
        <input class="form-control easyui-validatebox span3" type="text" required name="reqInstansi2[]" id="reqInstansi2<?=$no?>" value="" />
    </td>
    <td> 
        <input style="width: 200% !important" type="text" name="reqTglBerlaku[]" id="reqTglBerlaku<?=$no?>" title="Tanggal berlaku harus diisi" class="form-control easyui-datebox span2" value="" />
    </td>
    <td>
    	 <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
    </td>
</tr> 
                        