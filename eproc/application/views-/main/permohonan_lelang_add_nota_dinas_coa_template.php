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
            $('input[id^="reqNomorCOA<?=$no?>"]').validatebox({
                required:true
            });
        </script>
        <script>
            $('input[id^="reqBudgetAwal<?=$no?>"]').validatebox({
                required:true
            });
        </script>
        <script type="text/javascript">
           function calculate(checkbox)
            {
                awal = document.getElementById('reqBudgetAwal<?=$no?>').value;
                pakai = document.getElementById('reqBudgetTerpakai<?=$no?>').value;
                // alert('aaa');
                awalParsing = parseFloat(awal.split('.').join(""));
                pakaiParsing = parseFloat(pakai.split('.').join(""));
                total<?= $no ?> = awalParsing - pakaiParsing;
                $('#reqBudgetAkhir<?=$no?>').val(FormatNumberya(total<?= $no ?>));
            }
            function FormatNumberya(id)
            {
               var a = parseFloat(id);
               var nilai = FormatCurrency(a);
               return nilai;
            }
        </script>
     <td>
        <input type="text" name="reqNomorCOA[]" id="reqNomorCOA<?=$no?>" class="form-control span3 easyui-validatebox" />
     </td>
     <td>
        <input type="text" name="reqKeteranganCOA[]" id="reqKeteranganCOA<?=$no?>" class="form-control span3 easyui-validatebox"/>
     </td>
     <td>
        <input type="text" name="reqBudgetAwal[]" id="reqBudgetAwal<?=$no?>" class="form-control span3 easyui-validatebox" OnFocus="FormatAngka('reqBudgetAwal<?=$no?>')" OnKeyUp="FormatUang('reqBudgetAwal<?=$no?>')" OnBlur="FormatUang('reqBudgetAwal<?=$no?>')" onchange="calculate(this);"/>
     </td>
     <td>
        <input type="text" name="reqBudgetTerpakai[]" id="reqBudgetTerpakai<?=$no?>" class="form-control span3 easyui-validatebox" OnFocus="FormatAngka('reqBudgetTerpakai<?=$no?>')" OnKeyUp="FormatUang('reqBudgetTerpakai<?=$no?>')" OnBlur="FormatUang('reqBudgetTerpakai<?=$no?>')" onchange="calculate(this);" value="0"/>
     </td>
     <td>
        <input type="text" name="reqBudgetAkhir[]" id="reqBudgetAkhir<?=$no?>" class="form-control span3 easyui-validatebox" OnFocus="FormatAngka('reqBudgetAkhir<?=$no?>')" OnKeyUp="FormatUang('reqBudgetAkhir<?=$no?>')" OnBlur="FormatUang('reqBudgetAkhir<?=$no?>')" onchange="calculate(this);"/>
     </td>
     <td>
         <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
     </td>
</tr>
