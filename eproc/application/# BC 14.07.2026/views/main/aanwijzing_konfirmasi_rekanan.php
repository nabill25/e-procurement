<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("PaketRekanan");
$this->load->model("Aanwijzing");
$this->load->model("PhpShoutbox");
include_once("functions/date.func.php");
include_once("functions/default.func.php");


/* create objects */
$paket_rekanan = new PaketRekanan();
$aanwijzing = new Aanwijzing();
$php_shoutbox = new PhpShoutbox();

$reqId = httpFilterGet("reqId");
$reqKode = httpFilterGet("reqKode");

	
$i = 0;	
$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId, "A.REKANAN_ID" => $this->ID), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 ");
while($paket_rekanan->nextRow())
{
	$arrPaketRekanan[$i]["REKANAN_ID"] = $paket_rekanan->getField("REKANAN_ID");
	$arrPaketRekanan[$i]["KODE_REKANAN"] = $paket_rekanan->getField("KODE");
	$arrPaketRekanan[$i]["REKANAN"] = $paket_rekanan->getField("REKANAN");
	$i++;
}
$aanwijzing->selectByParams(array("PAKET_ID" => $reqId));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?></title>
<base href="<?=base_url()?>" />
<script type="text/javascript" src="lib/treeTable/doc/javascripts/jquery.js"></script>
<script type="text/javascript" src="jslib/jquery-1.9.1.min.js"></script>

<link href="lib/aanwijzing/css/gaya.css" rel="stylesheet" type="text/css">
<link href="lib/aanwijzing/files/style.css" rel="stylesheet" type="text/css">
<link href="lib/aanwijzing/files/menu.css" rel="stylesheet" type="text/css">

<script language="JavaScript" src="jslib/displayElement.js"></script>
<script type="text/javascript" src="lib/aanwijzing/globalfunction.js"></script>
<style type="text/css">
h4 { font-size: 18px; }
input, textarea { padding: 3px; border: 1px solid #999; }
input.error, select.error, textarea.error { border: 1px solid red; }
label.error { color:red; margin-left: 10px; }
html{ height:100%;}
</style>

<!-- POPUP WINDOW -->
<script type="text/javascript">

	function refresh() {
	  $.getJSON("json/aanwijzing_konfirmasi_rekanan_json/json/?reqId=<?=$reqId?>", function(json) {
		if(json.length) {
		  for(i=0; i < json.length; i++) {
			if(json[i].KODE == 'PESAN')
			{
				//$("#"+json[i].HALAMAN).html("<img src='images/message.png'>");
				$("#"+json[i].KODE_HALAMAN+"-"+json[i].HALAMAN).html(json[i].INFORMASI);
			}
			else
			{
				$("#"+json[i].KODE_HALAMAN+"-"+json[i].HALAMAN+"-"+json[i].KODE).css("background-image", "url(images/centang.png)");
			}
		  }
		}
		//alert(lastTime);
	  });
	  setTimeout(refresh, 3000);
	}

</script>

</head>

<body class="body-popup" onload="refresh();">
        
<table width="100%" border="0" cellpadding="2" cellspacing="2">                        
  <tr>
    <td colspan="5">
    <table width="100%" border="0" align="center" cellpadding="2" cellspacing="1">                          	
      <tbody>
        <tr class="judul-kolom">
          <th colspan="100">Notifikasi Aanwijzing - <?=$this->REKANAN?></th>
        </tr>
        <tr class="gelap">
          <td width="5%" style="text-align:center"><strong>HAL</strong></td>
          <td width="15%" style="text-align:center"><strong>PESAN ANDA / PESAN PANITIA</strong></td>
          <?php
          for($i=0;$i<count($arrPaketRekanan);$i++)
          {
          ?>
              <td width="20%" style="text-align:center"><strong>STATUS KONFIRMASI</strong></td>
          <?php
          }
          ?>
        </tr>
        <?php
		while($aanwijzing->nextRow())
		{
			$jumlah_halaman = $aanwijzing->getField("FILE_COUNT");
			?>
            <tr class="gelap">
            	<td colspan="100"><strong>Dokumen Lelang <?=$aanwijzing->getField("KODE")?></strong></td>
            </tr>
            <?php
			for($i=1;$i<=$jumlah_halaman;$i++)
			{
				$php_shoutbox = new PhpShoutbox();
				$pesan = $php_shoutbox->getPesanMasukRekanan($reqId, $i, $aanwijzing->getField("KODE"), $this->REKANAN_KODE);	
			?>
			<tr class="terang">
			  <td width="5%" style="text-align:center"><?=$i?></td>
			  <td width="5%" style="text-align:center"><label id="<?=$aanwijzing->getField("KODE")?>-<?=$i?>"><?=$pesan?></label></td>
			  <?php
			  for($j=0;$j<count($arrPaketRekanan);$j++)
			  {
			  ?>
				  <td width="20%" style="text-align:center"><label id="<?=$aanwijzing->getField("KODE")?>-<?=$i?>-<?=$arrPaketRekanan[$j]["KODE_REKANAN"]?>" style="background-image:url(images/uncentang.png); background-repeat:no-repeat">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label></td>
			  <?php
			  }
			  ?>
			</tr>                              
			<?php
				unset($php_shoutbox);
			}
		}
        ?>
      </tbody>                            
    </table>
    </td>
  </tr>
  </table>
                        
   
</body>
</html>