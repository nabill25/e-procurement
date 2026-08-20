<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Metode");
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");

$id = $this->input->get("id");
$paketid = $this->input->get("paketid");
$unitkerjaid = $this->input->get("unitkerjaid");

$this->load->library("librekamjejak"); $librekamjejak = new librekamjejak();

$paketInfo->getPaket($paketid);
$reqMetodeLelangId = $paketInfo->metode_lelang_id;

?>

<html moznomarginboxes mozdisallowselectionprint>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<base href="<?=base_url()?>" />

<!-- QRCODE -->
<script type="text/javascript" src="lib/jquery-qrcode-0.11.0/demo/jquery-1.10.2.js"></script>
<script type="text/javascript" src="lib/jquery-qrcode-0.11.0/jquery.qrcode-0.11.0.js"></script>
<script type="text/javascript" src="lib/jquery-qrcode-0.11.0/demo/ff-range.js"></script>
<script type="text/javascript" src="lib/jquery-qrcode-0.11.0/demo/scripts.js"></script>
<!--<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700' rel='stylesheet'>-->
<!--<link href="http://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet" type="text/css">-->


</head>

<body>
  <br>
  <div class="logo"><img src="images/<?= $this->libbreadcrumb->cetakcopyrightlogo($unitkerjaid) ?>" height="75" /></div>
  <div class="judul">
  	REKAM JEJAK<br>

  <div class="pekerjaan">
   <?php  if($paketInfo->bahasa == "EN") echo "/ <em>WORKS</em>"; ?><br />
  <?=strtoupper($paketInfo->nama)?>
  </div><br>

  <!-- <div class="area-dokumen">
    <table class="table font13">
    </table>
  </div> -->

  <?php echo $librekamjejak->viewRJCetak($id,$paketid); ?>

<p></p>

<div class="nomor-oe">
  <div class="data" style="font-size:10px; font-style:italic">
       <?= $this->libbreadcrumb->cetakcopyright($unitkerjaid) ?>
       <?php if($paketInfo->bahasa == "EN") echo "<br><br><em>".SYSTEM_SAH_EN."</em>"; ?>
  </div>
</div>
</body>
</html>
