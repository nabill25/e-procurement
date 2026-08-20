<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
include_once("functions/date.func.php");
$no = rand();
?>
 <tr>
     <td>
     	<link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
		 <script>
            $('input[id^="reqBankId<?=$no?>"]').combobox({  
                required:true
            });
			 $('input[id^="reqBulan<?=$no?>"]').combobox({  
                required:true
            });
        </script> 
        	<input class="form-control easyui-validatebox span1" type="hidden"  name="reqRekananRekeningKoranId[]" id="reqRekananRekeningKoranId<?=$no?>" value="" />
           <input name="reqNoRekening[]" type="text" title="Nomor rekening harus diisi" class="form-control easyui-validatebox span1" id="reqNoRekening<?=$no?>" value="" required />
     </td>
     <td>
        <input type="text" name="reqBankId[]" id="reqBankId<?=$no?>" class="easyui-combobox span2"
                    data-options="valueField:'id',textField:'text',url:'bank_json/combo'" value="" required />
     </td>
     <td>
        <select name="reqPeriode[]" id="reqPeriode<?=$no?>" class="easyui-combobox span2"  required>
		<?php
        for($i=0;$i<=3;$i++)
        {
            $reqLastMonth = strtotime("-".$i." months", strtotime(date("d-m-Y")));
            $bulan = strftime ( '%m' , $reqLastMonth );
            $tahun = strftime ( '%Y' , $reqLastMonth );
            $periode =$bulan.$tahun;
        ?>
            <option value="<?=$periode?>"><?=getNamePeriode($periode)?></option>
        <?php
        }
        ?>
        </select>
     </td>
      <td>
          <input name="reqNilai[]" class="form-control easyui-validatebox span1" required type="text" id="reqNilai<?=$no?>" value="" 
                 OnFocus="FormatAngka('reqNilai<?=$no?>')" OnKeyUp="FormatUang('reqNilai<?=$no?>')" OnBlur="FormatUang('reqNilai<?=$no?>')"/>
	</td>
     <td>
          <input type="hidden" name="reqLinkFileTemp[]" id="reqLinkFileTemp<?=$no?>" value="" />
          <input type="hidden" name="reqLinkFileTempTipe[]" id="reqLinkFileTempTipe<?=$no?>" value="" />
          <input type="hidden" name="reqLinkFileTempUkuran[]" id="reqLinkFileTempUkuran<?=$no?>" value="" />
          <input type="file" class="span1" name="reqLinkFile[]" id="reqLinkFile<?=$no?>" id="reqLinkFilePDF" size="30" <?php if($reqLinkFileTemp == "") { ?> required <?php } ?> class="easyui-validatebox"  validType="fileType['pdf']" /> <br>
          <span style="color: red"><small>Lampiran wajib</small></span>
          <input type="hidden" name="reqLinkFileTempNama[]" id="reqLinkFileTempNama<?=$no?>" value="">
     </td>
     <td> 
      <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
</tr> 
                        