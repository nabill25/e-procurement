<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("Aanwijzing");
$this->load->model("PhpShoutbox");
$this->load->model("PaketRekanan");
include_once("functions/date.func.php");
include_once("functions/default.func.php");


/* create objects */
$aanwijzing = new Aanwijzing();
$php_shoutbox = new PhpShoutbox();
$paket_rekanan = new PaketRekanan();

$reqId = httpFilterGet("reqId");
$reqKode = httpFilterGet("reqKode");


$paket_rekanan->selectByParams(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => $this->ID));
$paket_rekanan->firstRow();
$nickname = $paket_rekanan->getField("KODE_REKANAN");	

	
$i = 0;	
$aanwijzing->selectByParams(array("PAKET_ID" => $reqId));
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?></title>
<base href="<?=base_url()?>" />
<link rel="stylesheet" href="lib/aanwijzing/css/gaya.css" type="text/css">

<script type="text/javascript" src="jslib/jquery-1.9.1.min.js"></script>
<script type="text/javascript">

	function refresh() {
	  $.getJSON("aanwijzing_konfirmasi_rekanan_json/json/?reqId=<?=$reqId?>", function(json) {
		if(json.length) {
		  for(i=0; i < json.length; i++) {
			if(json[i].KODE == 'PESAN')
			{
				//$("#"+json[i].HALAMAN).html("<img src='images/message.png'>");
				$("#"+json[i].KODE_HALAMAN+"-"+json[i].HALAMAN).html(json[i].INFORMASI);
			}
			else
			{
				$("#"+json[i].KODE_HALAMAN+"-"+json[i].HALAMAN+"-"+json[i].KODE).html("<img src='<?= base_url() ?>images/centang.png'>");
			}
		  }
		}
		//alert(lastTime);
	  });
	  setTimeout(refresh, 10000);
	}

</script>

</head>

<body onload="refresh();">
<div id="content-inner">
<table>
    <tr class="sub-judul1">
        <td width="5%" style="text-align:center"><strong>HAL</strong></td>
        <td width="15%" style="text-align:center"><strong>PESAN ANDA / PESAN PANITIA</strong></td>
        <td width="20%" style="text-align:center"><strong>STATUS KONFIRMASI</strong></td>
    </tr>
    <?php
    while($aanwijzing->nextRow())
    {
        $jumlah_halaman = $aanwijzing->getField("FILE_COUNT");
        ?>
        <tr class="sub-judul">
            <td colspan="100"><strong>Dokumen Lelang <?=$aanwijzing->getField("KODE")?></strong></td>
        </tr>
        <?php
        for($i=1;$i<=$jumlah_halaman;$i++)
        {
            $php_shoutbox = new PhpShoutbox();
			$pesan = $php_shoutbox->getPesanMasukRekanan($reqId, $i, $aanwijzing->getField("KODE"), $nickname);	
        ?>
        <tr>
          <td style="text-align:center"><?=$i?></td>
          <td style="text-align:center"><span id="<?=$aanwijzing->getField("KODE")?>-<?=$i?>"><?=$pesan?></span></td>
          <td style="text-align:center"><span id="<?=$aanwijzing->getField("KODE")?>-<?=$i?>-<?=$nickname?>"><img src="<?= base_url() ?>images/uncentang.png"></span></td>          
        </tr>                              
        <?php
            unset($php_shoutbox);
        }
    }
    ?>  
    </table>
</div>
</body>
</html>