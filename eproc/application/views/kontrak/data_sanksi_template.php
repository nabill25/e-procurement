<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$no = rand();
$reqId  = $this->input->get("reqId"); // Contracting Rekanan ID
$this->load->model(array("Contractingpayment"));
$contractingPay = new Contractingpayment();
$contractingPay->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
?>
<tr> 
  <link rel="stylesheet" type="text/css" href="lib/eproc/themes/default/easyui.css">
   <script>
        $('input[id^="reqNo<?=$no?>"]').validatebox({  
            required:true
        }); 
       // function calculate(checkbox)
       //  {
       //      nilaisanksi = document.getElementById('nilaisanksi<?=$no?>').value;
       //      nilaipekerjaan = document.getElementById('nilaipekerjaan<?=$no?>').value;
       //      hariterlambat = document.getElementById('hariterlambat<?=$no?>').value;

       //      nilaisanksiParsing = parseFloat(nilaisanksi.split('.').join(""));
       //      nilaipekerjaanParsing = parseFloat(nilaipekerjaan.split('.').join(""));
       //      hariterlambatParsing = parseFloat(hariterlambat.split('.').join(""));

       //      total<?= $no ?> = (nilaisanksiParsing/1000) * nilaipekerjaanParsing * hariterlambatParsing;

       //      $('#nilaidenda<?=$no?>').val(FormatNumberya(total<?= $no ?>));
       //      alert(total<?= $no ?>);
       //  }
       //  function FormatNumberya(id)
       //  {
       //     var a = parseFloat(id);
       //     var nilai = FormatCurrency(a);
       //     return nilai;    
       //  }
   function calculate(no)
    {
        nilaisanksi = document.getElementById('nilaisanksi'+no).value;
        nilaipekerjaan = document.getElementById('nilaipekerjaan'+no).value;
        hariterlambat = document.getElementById('hariterlambat'+no).value;

        nilaisanksiParsing = parseFloat(nilaisanksi.split('.').join(""));
        nilaipekerjaanParsing = parseFloat(nilaipekerjaan.split('.').join(""));
        hariterlambatParsing = parseFloat(hariterlambat.split('.').join(""));

        total = (nilaisanksiParsing/1000) * nilaipekerjaanParsing * hariterlambatParsing;

        $('#nilaidenda'+no).val(FormatNumberya(Math.round(total)));
    }
    function FormatNumberya(id)
    {
       var a = parseFloat(id);
       var nilai = FormatCurrency(a);
       return nilai;    
    }

    function toggleUpload(el) {
        var row = el.closest('.row-bayar');
        var upload = row.querySelector('.showUpload');
        var file = row.querySelector('input[type="file"]');

        if (el.value === 'Disetor') {
            upload.style.display = 'block';
            file.required = true;
        } else {
            upload.style.display = 'none';
            file.required = false;
            file.value = '';
        }
    }

    function getPayment(paymentid,idnya) {
        if(paymentid === '') return;

        fetch("<?= base_url() ?>contracting_json/getPaymentNilai?reqId=<?= $reqId ?>&paymentid=" + paymentid)
            .then(response => response.text())
            .then(data => {
                $('#nilaipekerjaan'+idnya).val(data);
                calculate(idnya);
            });
    }

    $('input[id^="nilaisanksi<?=$no?>"], input[id^="hariterlambat<?=$no?>"]').validatebox({  
        required:true
    }); 
    $('#paymentid<?= $no ?>').validatebox({
        required:true,
        validType:'selectRequired'
    });
  </script>
    <td>
        <input type="hidden" name="rowkey[<?= $no ?>]" value="<?= $no ?>">
        <select class="form-control" name="paymentid[<?= $no ?>]" id="paymentid<?= $no ?>" style="width:150px" onchange="getPayment(this.value,<?= $no ?>)" required>
              <option value="">~ Pilih Tagihan ~</option>
            <?php 
            while($contractingPay->nextRow()) {  ?>
              <option value="<?= $contractingPay->getField("PAYMENTID") ?>"><?= $contractingPay->getField("PAY_TERMIN_KE") ?></option>
            <?php 
            } ?>
        </select>
    </td>  
   <td>
    <input type="text" class="form-control easyui-validatebox" required name="nilaisanksi[<?= $no ?>]" id="nilaisanksi<?=$no?>" OnFocus="FormatAngka('nilaisanksi<?=$no?>')" OnFocus="FormatAngka('nilaisanksi<?=$no?>')" OnKeyUp="FormatUang('nilaisanksi<?=$no?>')" OnBlur="FormatUang('nilaisanksi<?=$no?>')" maxlength="3" value="" onchange="calculate('<?= $no ?>');" required>
   </td> 
   <td>
    <input type="text" class="form-control easyui-validatebox" required name="nilaipekerjaan[<?= $no ?>]" id="nilaipekerjaan<?=$no?>" value="" id="nilaipekerjaan<?=$no?>" value="" OnFocus="FormatAngka('nilaipekerjaan<?=$no?>')" OnKeyUp="FormatUang('nilaipekerjaan<?=$no?>')" OnBlur="FormatUang('nilaipekerjaan<?=$no?>')" onchange="calculate('<?= $no ?>');">
   </td>  
   <td> 
    <input type="text" class="form-control easyui-validatebox" required name="hariterlambat[<?= $no ?>]" id="hariterlambat<?=$no?>" OnFocus="FormatAngka('hariterlambat<?=$no?>')" OnFocus="FormatAngka('hariterlambat<?=$no?>')" OnKeyUp="FormatUang('hariterlambat<?=$no?>')" OnBlur="FormatUang('hariterlambat<?=$no?>')" maxlength="3" value="" onchange="calculate('<?= $no ?>');" required>
   </td> 
   <td>
    <input type="text" class="form-control easyui-validatebox" required name="nilaidenda[<?= $no ?>]" id="nilaidenda<?=$no?>" value="" id="nilaidenda<?=$no?>" value="" FormatAngka="FormatAngka('nilaidenda<?=$no?>')" OnKeyUp="FormatUang('nilaidenda<?=$no?>')" OnBlur="FormatUang('nilaidenda<?=$no?>')" style="width: 110px;">
   </td>  
   <td> 
    <div class="row-bayar">
        <input type="radio" name="carabayar[<?=$no?>]" value="Dipotong" onchange="toggleUpload(this)">Dipotong
        <input type="radio" name="carabayar[<?=$no?>]" value="Disetor" onchange="toggleUpload(this)" checked>Disetor
    </div>
   </td> 
   <td>
    <div class="showUpload">
        <input type="file" name="reqLampiran[<?= $no ?>]" id="reqLampiran<?= $no ?>" size="30" class="easyui-validatebox span9" validType="fileType['pdf']"/>
    </div>
   </td> 
   <td>
    <div class="showUpload">
        <input type="file" name="reqLampiran2[<?= $no ?>]" id="reqLampiran2<?= $no ?>" size="30" class="easyui-validatebox span9" validType="fileType['pdf']"/>
    </div>
   </td> 
  <td>
    <a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a>
   </td>
</tr>
