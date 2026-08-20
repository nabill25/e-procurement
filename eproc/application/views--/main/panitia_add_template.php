<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$no = rand();
$reqUnitKerja = $this->input->get("reqUnitKerja");
?>
<tr>
<td>
    <link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
	<script>
		$('input[id^="reqNama<?=$no?>"]').combobox({  
			required:true
		});
		$('input[id^="reqStatusPanitia<?=$no?>"]').combobox({  
			required:true
		});
		$('input[id^="reqFungsiPanitia<?=$no?>"]').combobox({  
			required:true
		});
	</script>
    <input type="hidden" name="reqPanitiaId[]" value="">
   <?php /*?> <input type="text" id="reqNamaPanitia<?=$no?>" name="reqNamaPanitia[]" class="easyui-validatebox" style="width:100%; background-color:#F3F3F3" value="" /><?php */?>
    <input type="text"  name="reqNama[]" id="reqNama<?=$no?>" class="easyui-combobox" title="Nama harus diisi" 
    data-options=" required: true,
                    filter: function(q, row) { return row['text'].toLowerCase().indexOf(q.toLowerCase()) != -1; },
                    valueField: 'id', textField: 'cabang',
                    url: 'panitia_json/panitia_combo_json_panitia/?reqUnitKerja=<?=$reqUnitKerja?>',
                    onSelect:function(rec){ 
                        $('#reqNamaPanitia<?=$no?>').val(rec.text);
                        $('#reqNip<?=$no?>').val(rec.id);
                        $('#reqJabatanPanitia<?=$no?>').val(rec.jabatan);
                    }
                    " value="<?=$reqNama?>" required="required" style="width:300px;">
</td>
<td>
	<input type="hidden"  name="reqNamaPanitia[]" id="reqNamaPanitia<?=$no?>">
    <input type="text" id="reqNip<?=$no?>" name="reqNip[]" class="form-control easyui-validatebox" style="width:150px; background-color:#F3F3F3" value="" />
</td>
<td>
    <input type="text" id="reqJabatanPanitia<?=$no?>" name="reqJabatanPanitia[]" class="form-control easyui-validatebox" style="width:150px; background-color:#F3F3F3" value="" />
</td>
<td>
    <select  id="reqStatusPanitia<?=$no?>" name="reqStatusPanitia[]" class="form-control easyui-combobox">
        	<option value="1">Aktif</option>
        	<option value="0">Tidak Aktif</option>
    </select>
</td>
<td>
    <select  id="reqFungsiPanitia<?=$no?>" name="reqFungsiPanitia[]" class="form-control easyui-combobox">
    	<option value="0">Anggota</option>
        <option value="1" >Ketua</option>
    </select>
</td>  
<td align="center">
    <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
</td>                                
</tr>
                        