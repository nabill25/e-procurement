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
<td>
	<div class="row">
		<div class="form-group col-md-12 mb-2">
		  	<label for="reqEvaluasiAdministrasi"></label>
			<input name="reqEvaluasiAdministrasi[<?=$i?>]" type="text" id="reqEvaluasiAdministrasi" value="" size="100" class="form-control span10" />
			<input type="hidden" name="reqCheck[<?=$i?>]" id="reqPilih<?=$i?>" value="1">
		</div> 
	</div>  
</td>
<td align="center"><a title="#" onclick="createRowDataAdministrasi()" class="btn-aksi"><i class="btn btn-primary fa fa-plus" aria-hidden="true"></i></a></td>
</tr>