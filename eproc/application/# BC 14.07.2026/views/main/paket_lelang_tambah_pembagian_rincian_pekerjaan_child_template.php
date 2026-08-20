<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$reqChild = $this->input->get("reqChild");
$no = rand();
?>
<tr>
    <td>
    	<input type="hidden" name="reqPaketPenawaranIdChild[]" id="reqPaketPenawaranId<?=$no?>" value="" style="width:40px;" />
        <input type="hidden" name="reqLotChild[]" id="reqLotChild<?=$no?>" value="" class="form-control span1"/>
        &nbsp;a
    </td>
    <td>
      <input type="text" name="reqItemChild[]" id="reqItemChild<?=$no?>" value=""  class="form-control span1"/>
    </td>
    <td>
      <input type="text" name="reqSatuanChild[]" id="reqSatuanChild<?=$no?>" value="" class="form-control span1"/>
    </td>
    <td>
      <input type="text" name="reqQuantityChild[]" id="reqQuantityChild<?=$reqChild?>-<?=$no?>" value=""  OnFocus="FormatAngka('reqQuantityChild<?=$reqChild?>-<?=$no?>')" OnKeyUp="FormatUang('reqQuantityChild<?=$reqChild?>-<?=$no?>'); summaryParent('<?=$reqChild?>');" OnBlur="FormatUang('reqQuantityChild<?=$reqChild?>-<?=$no?>')" class="form-control span2" />
    </td>    
    <td>
      <input type="text" name="reqOEChild[]" id="reqOEChild<?=$reqChild?>-<?=$no?>" value=""  OnFocus="FormatAngka('reqOEChild<?=$reqChild?>-<?=$no?>')" OnKeyUp="FormatUang('reqOEChild<?=$reqChild?>-<?=$no?>'); summaryParent('<?=$reqChild?>');" OnBlur="FormatUang('reqOEChild<?=$reqChild?>-<?=$no?>')" class="form-control span2"/>
    </td>
    <td>
      <input type="text" name="reqJumlahChild[]" id="reqJumlahChild<?=$reqChild?>-<?=$no?>" value="" readonly  class="form-control span2" style="background-color:#EDEDED;" />
    </td>
    <td>
        <input type="hidden" style="width:100px" />                                                                    
    </td>
    <td align="center">    
        <a title="#" onclick="$(this).parent().parent().remove();  summaryParent('<?=$reqChild?>');" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
        <input type="hidden" name="reqChild[]" value="<?=$reqChild?>"  />
    </td>
</tr>
                        