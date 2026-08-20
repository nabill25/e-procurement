<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 ?>

 <tr>
    <td><input type="text" name="reqPersonilKualifikasi[]" style="width:200px" class="form-rounded" /></td>
    <td><select name='reqPendidikan[]' id='reqPendidikan'><option value=''></option><option value='1'>S1</option><option value='2'>S2</option><option value='3'>S3</option><option value='4'>D3</option><option value='5'>D4</option><option value='6'>SLTA</option></select></td>
    <td><input name="reqPersonilPengalaman[]" type="text" style="width:40px" class="form-rounded" /> th</td>
    <td><input name="reqPersonilJumlah[]" type="text" style="width:40px" class="form-rounded" /></td>
    
    <td><input type="hidden" name="reqPersonilSKA[]" id="reqPersonilSKA1" /><input name="reqPersonilSKACheckbox[]" value="1" type="checkbox" onchange="setEvaluasiPenawaran(this, 'reqPersonilSKA1')" /> SKA</td>
    <td><input type="hidden" name="reqPersonilCV[]" id="reqPersonilCV1" /><input name="reqPersonilCV[]" value="1" type="checkbox" onchange="setEvaluasiPenawaran(this, 'reqPersonilCV1')" /> CV</td>
    <td><input name="reqPersonilNilai[]" type="text" style="width:40px" class="form-rounded" /></td>
    <td align="center"><a onclick="createRowPersonilKualifikasi()" class="btn-aksi"><i class="fa fa-plus" aria-hidden="true"></i></a></td>
</tr>