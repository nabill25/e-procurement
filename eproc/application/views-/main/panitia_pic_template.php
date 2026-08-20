<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$no = rand();
$reqUnitKerja = $this->input->get("reqUnitKerja");
$reqSourching = $this->input->get("reqSourching");
?>
<link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
<script>
    $('input[id^="reqNama<?=$no?>"]').combobox({  
        required:true
    });
</script> 
<?php 
if ($reqSourching == '0') { // Sourcing ?>
<input type="text" class="easyui-combobox" name="reqPIC" id="reqNama<?=$no?>" title="Nama harus diisi" data-options=" required: true, filter: function(q, row) { return row['text'].toLowerCase().indexOf(q.toLowerCase()) != -1; }, valueField: 'id', textField: 'text', url: 'panitia_json/panitia_combo_json_penyelia/?reqUnitKerja=<?= $reqUnitKerja ?>'" value=""  style="width:300px;">    
<?php 
} else { ?>
<input type="text" class="easyui-combobox" name="reqPIC" id="reqNama<?=$no?>" title="Nama harus diisi" data-options=" required: true, filter: function(q, row) { return row['text'].toLowerCase().indexOf(q.toLowerCase()) != -1; }, valueField: 'id', textField: 'text', url: 'panitia_json/panitia_combo_json_purchase/?unitkerja=<?= $reqUnitKerja ?>'" value=""  style="width:300px;">          
<?php 
} ?>