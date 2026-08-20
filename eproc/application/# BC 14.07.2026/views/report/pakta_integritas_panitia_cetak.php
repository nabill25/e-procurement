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
$this->load->model("PaketAanwijzing");
$this->load->model("PaketTahap");
$this->load->model("Aanwijzing");
$this->load->model("PhpShoutbox");
$this->load->model("PaketPembukaanValidasi");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/string.func.php");
$this->load->model("PaketPanitia");
$this->load->model("PaketPihakLain");
$this->load->library("AES");
include_once("lib/phpqrcode/qrlib.php");


/* create objects */
$paket_tahap_metode = new PaketTahap();
$paket_tahap = new PaketTahap();
$paket_panitia = new PaketPanitia();
$reqId = httpFilterGet("reqId");
$reqToken = httpFilterGet("reqToken");

$PNG_TEMP_DIR = 'uploads/';

$paketInfo->getPaket($reqId);

$this->load->model("PaketPanitia");
$this->load->model("SKPanitia");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");


$paket_panitia->selectByParams2Group(array("A.PAKET_ID" => $reqId));


?>


<html moznomarginboxes mozdisallowselectionprint>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<base href="<?=base_url()?>" />

<!-- QRCODE -->
<script type="text/javascript" src="lib/jquery-qrcode-0.11.0/demo/jquery-1.10.2.js"></script>
<script type="text/javascript" src="lib/jquery-qrcode-0.11.0/jquery.qrcode-0.11.0.js"></script>
<script type="text/javascript" src="lib/jquery-qrcode-0.11.0/demo/ff-range.js"></script>
<script type="text/javascript" src="lib/jquery-qrcode-0.11.0/demo/scripts.js"></script>
<!--<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700' rel='stylesheet'>-->
<!--<link href="http://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet" type="text/css">-->


</head>

<body class="body">
<div style="text-align: center; margin-bottom:20px;"><img src="images/<?= SYSTEM_LOGO ?>" height="75" /></div>
<div style="text-align: center; font-weight: bold;">
	PAKTA INTEGRITAS
</div><br>

  <div class="pekerjaan">
    <?php //strtoupper($paketInfo->nama)?>
  </div><br>

  <div class="isi">
    <p style="text-align:justify;">Kami  yang bertanda tangan di bawah ini, Pelaksana Pengadaan <?= SYSTEM_NAME_PT ?>, dalam rangka <?= $paketInfo->metode_lelang_nama.' <span style="font-weight:bold;font-style: italic;">'.$paketInfo->nama.'</span>' ?>, dengan ini menyatakan bahwa saya:</p>
    <div class="isi">
      <ol>
        <li style="text-align: justify;">Tidak akan melakukan praktek KKN;</li>
        <li style="text-align: justify;">Akan melaporkan kepada SPI <?= SYSTEM_NAME_PT ?> apabila mengetahui ada indikasi praktek KKN di dalam proses pengadaan ini;</li>
        <li style="text-align: justify;">Dalam proses pengadaan ini, berjanji akan melaksanakan tugas secara bersih, transparan, dan profesional dalam arti akan mengerahkan segala kemampuan dan sumber daya secara optimal untuk memberikan hasil kerja terbaik mulai dari pengumuman pengadaan, evaluasi penawaran, dan penetapan hasil kegiatan ini;</li>
        <li style="text-align: justify;">Akan mengikuti peraturan perundang-undangan dan praktik tata kelola perusahaan yang baik; dan</li>
        <li style="text-align: justify;">Apabila saya melanggar hal – hal yang telah saya nyatakan dalam PAKTA INTEGRITAS ini, saya bersedia dikenakan sanksi moral, sanksi administrasi serta dituntut ganti rugi dan pidana sesuai dengan ketentuan peraturan perundang – undangan yang berlaku.</li>
      </ol>
    </div>
  </div>

  <div class="isi">
    <p style="text-align:right; margin-right:20px"><?= getFormattedDate(date('Y-m-d')) ?></p>
    <p style="font-weight:bold">PELAKSANA PENGADAAN <?= SYSTEM_NAME_PT ?>,</p><BR>
    <table class="teble" style="width: 100%;">
      <tbody>

        <?php
          $i=1;
          $style="gelap";
          $kunciPanitia = 0;
          $ketuaKah = 0;
          while($paket_panitia->nextRow())
          {
          ?>
          <tr>
            <td style="vertical-align: top; width:200px; height:30px"></td>
            <td style="vertical-align: top;"><?=$paket_panitia->getField("NAMA")?></td>
            <?php
            if ($i%2 == 0) { ?>
              <td style="vertical-align: top; text-align: center;">...................................</td>
              <td> </td>
            <?php
            } else { ?>
              <td> </td>
              <td style="vertical-align: top; text-align: center;">...................................</td>
            <?php
            }?>
          </tr>
        <?php
         $i++;
        } ?>
      </tbody>
    </table>
  </div>

  <div class="nomor-oe">
    <div class="data">
     <?= SYSTEM_SAH ?>
     <?php if($paketInfo->bahasa == "EN") echo "<br><br><em>".SYSTEM_SAH_EN."</em>"; ?>
    </div>
  </div>

</body>
</html>
