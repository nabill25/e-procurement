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
  <!-- <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script> -->
   <link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
   <script>
    $('input[id^="reqTmNote<?=$no?>"]').validatebox({ required:true });
    </script>
    <script>
    $('input[id^="reqTmDate<?=$no?>"]').datebox({ required:true });
    </script>
    <td> <input type="text" name="reqTmNote[]" id="reqTmNote<?=$no?>" class="form-control span9"></td>
    <td align="center" class="kolom-aksi" width="10%">
      <input type="text" name="reqTmDate[]" id="reqTmDate<?=$no?>" class="form-control span2 easyui-datebox" style="width: 150% !important"/>
    </td>
    <td>
      <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
    </td>
</tr>
