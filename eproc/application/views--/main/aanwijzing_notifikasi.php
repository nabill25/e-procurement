<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketRekanan");
$this->load->model("Aanwijzing");
$this->load->model("PhpShoutbox");
include_once("functions/date.func.php");
include_once("functions/default.func.php");


/* create objects */
$paket_rekanan = new PaketRekanan();
$paket_rekanan_count = new PaketRekanan();
$aanwijzing = new Aanwijzing();
$php_shoutbox = new PhpShoutbox();

$reqId = httpFilterGet("reqId");
$reqKode = httpFilterGet("reqKode");


if($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 9)
{}
else
	exit();
	
$i = 0;	

$paketInfo->getPaket($reqId);
$statement='';
if($paketInfo->metode_kualifikasi_id == 1)
	$statement = " AND A.LULUS_KUALIFIKASI = 1 ";

$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 ".$statement);
$prc = $paket_rekanan_count->getCountByParams(array("PAKET_ID" => $reqId), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 ".$statement);

if ($prc > 0) {
  while($paket_rekanan->nextRow())
  {
    $arrPaketRekanan[$i]["REKANAN_ID"] = $paket_rekanan->getField("REKANAN_ID");
    $arrPaketRekanan[$i]["KODE_REKANAN"] = $paket_rekanan->getField("KODE_REKANAN");
    $arrPaketRekanan[$i]["REKANAN"] = $paket_rekanan->getField("REKANAN");
    $arrPaketRekanan[$i]["AANWIJZING"] = $paket_rekanan->getField("AANWIJZING");
    $i++;
  }
} else {
  $arrPaketRekanan[$i]["REKANAN_ID"] = '';
  $arrPaketRekanan[$i]["KODE_REKANAN"] = '';
  $arrPaketRekanan[$i]["REKANAN"] = '';
  $arrPaketRekanan[$i]["AANWIJZING"] = '';
}
$aanwijzing->selectByParams(array("PAKET_ID" => $reqId));

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<base href="<?=base_url()?>" />
<link rel="stylesheet" href="lib/aanwijzing/css/gaya.css" type="text/css">

<script type="text/javascript" src="jslib/jquery-1.9.1.min.js"></script>
<script type="text/javascript">

	function refresh() {
	  $.getJSON("phpshoutbox_json/aanwijzing_konfirmasi_json/?reqId=<?=$reqId?>", function(json) {
		if(json.length) {
		  for(i=0; i < json.length; i++) {
			if(json[i].KODE == 'PESAN')
			{
				//$("#"+json[i].HALAMAN).html("<img src='images/message.png'>");
				$("#"+json[i].KODE_HALAMAN+"-"+json[i].HALAMAN).html(json[i].INFORMASI);
			}
			else if(json[i].KODE == 'KEHADIRAN')
			{
				if(json[i].INFORMASI == 1)
					$("#KEHADIRAN-"+json[i].HALAMAN).html("<img src='<?= base_url() ?>images/centang.png'>");
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
	
 	function menujuHalaman(buku, halaman)
	{
		parent.menujuHalaman(buku, halaman);	
	}
</script>

</head>

<body bgcolor="#71cee8" onload="refresh();">
<div id="content-inner">
<table>
    <tr class="sub-judul1">
      <td width="5%" style="text-align:center"><strong>HAL</strong></td>
      <td width="8%" style="text-align:center"><strong>MSG (R/P)</strong></td>
      <?php
      for($i=0;$i<count($arrPaketRekanan);$i++)
      {
      ?>
          <td width="20%" style="text-align:center"><strong><?=$arrPaketRekanan[$i]["KODE_REKANAN"]?></strong></td>
      <?php
      }
      ?>
    </tr>
    <tr class="sub-judul">
        <td colspan="100"><strong>Status Aktif</strong></td>
    </tr>  
    <tr>
      <td style="text-align:center">-</td>
      <td style="text-align:center">-</td>
      <?php
      for($j=0;$j<count($arrPaketRekanan);$j++)
      {
      ?>
          <td style="text-align:center">
          	<span id="KEHADIRAN-<?=$arrPaketRekanan[$j]["KODE_REKANAN"]?>">
            <?php
            if($arrPaketRekanan[$j]["AANWIJZING"] == 1)
			{
			?>
            	<img src="<?= base_url() ?>images/centang.png">            
            <?php
			}
			else
			{
			?>
            	<img src="<?= base_url() ?>images/uncentang.png">
            <?php
			}
			?>
            </span>
          </td>
      <?php
      }
      ?>
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
            $pesan = $php_shoutbox->getPesanMasuk($reqId, $i, $aanwijzing->getField("KODE"));	
        ?>
        <tr>
          <td style="text-align:center"><a title="#" onclick="menujuHalaman('<?=$aanwijzing->getField("KODE")?>', '<?=$i?>');"><?=$i?></a></td>
          <td style="text-align:center"><span id="<?=$aanwijzing->getField("KODE")?>-<?=$i?>"><?=$pesan?></span></td>
          <?php
          for($j=0;$j<count($arrPaketRekanan);$j++)
          {
          ?>
              <td style="text-align:center"><span id="<?=$aanwijzing->getField("KODE")?>-<?=$i?>-<?=$arrPaketRekanan[$j]["KODE_REKANAN"]?>"><img src="<?= base_url() ?>images/uncentang.png"></span></td>
          <?php
          }
          ?>
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