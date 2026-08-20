<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$no = rand();
$reqJenis = $this->input->get("reqJenis");
?>
<link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
<script>
    $('input[id^="reqNama"]').combobox({  
        required:true
    });
</script> 

<input type="text" class="easyui-combobox" name="reqBidangUsahaParentId" id="reqNama" title="Nama harus diisi" data-options=" required: true, filter: function(q, row) { return row['text'].toLowerCase().indexOf(q.toLowerCase()) != -1; }, valueField: 'id', textField: 'text', url: 'metode_json/metode_combo?jenis=<?= $reqJenis ?>'" value=""  style="width:150px;">    
 