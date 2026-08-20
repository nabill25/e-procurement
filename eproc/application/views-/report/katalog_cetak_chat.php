<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->library("kauth");  $userLogin = new kauth();

include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("Paket");
$this->load->model("Katalogrekanan");

$paket = new Paket();
$katalogrekanan = new Katalogrekanan();
$katalogrekananRow = new Katalogrekanan();
$katalogrekananGroupPenyedia = new Katalogrekanan();

$reqId = httpFilterGet("reqId");

$totalPenyedia = $katalogrekananGroupPenyedia->selectByParamsGroupByPenyedia(array()," AND A.PAKET_ID = '".$reqId."'");
// echo $totalPenyedia; die();

$paket->selectByParamsMonitoring(array("A.PAKET_ID" => coalesce($reqId, 0)));
$paket->firstRow();

  $reqMetodePengadaan = $paket->getField("PAKET_METODE_LELANG_ID");
  $reqMetodeKualifikasi = $paket->getField("PAKET_METODE_KUALIFIKASI_ID");
  $reqMetodeEvaluasi = $paket->getField("PAKET_METODE_EVALUASI_ID");
  $reqJenisPekerjaan = $paket->getField("PAKET_JENIS_ID");
  $reqJenisPekerjaanStr = $paket->getField("PAKET_JENIS");
  $reqKualifikasiRekanan = $paket->getField("REKANAN_KUALIFIKASI_ID");
  $reqNamaPaket = $paket->getField("NAMA");
  $reqUraianKegiatan = $paket->getField("URAIAN");
  $reqLokasiPekerjaan = $paket->getField("LOKASI");
  $reqAlamatPanitia =  $paket->getField("ALAMAT");
  $arrTelp = explode(" ", trim($paket->getField("TELEPON")));
  $reqTelpPanitiaKode = $arrTelp[0];
  $reqTelpPanitia = $arrTelp[1];
  $reqEmailPanitia = $paket->getField("EMAIL");
  $reqNilaiPekerjaan = $paket->getField("NILAI");
  $reqPermohonanId = $paket->getField("PERMOHONAN_PAKET_ID"); 
  $reqPermohonan = $paket->getField("PERMOHONAN");
  $reqPermohonanNotaDinas = $paket->getField("PERMOHONAN_NOTA_DINAS");
  $reqMetodePenyampulan = $paket->getField("SISTEM_SAMPUL");
  $reqBahasa = $paket->getField("BAHASA");
  $reqMataUang = $paket->getField("NILAI_MATA_UANG");
  $reqBidingMenit = $paket->getField("BIDDING_MENIT");
  $reqBidding = $paket->getField("BIDDING");
  $reqBobotTeknis = $paket->getField("BOBOT_TEKNIS");
  $reqBobotHarga = $paket->getField("BOBOT_HARGA");
  $reqPassingGrade = $paket->getField("PASSING_GRADE");

  if ($reqId == '' || $reqMetodePengadaan != '6')
    exit;

  $katalogrekanan->selectByParams(array('A.PAKET_ID' => $reqId));
  $katalogrekananRow->selectByParams(array('A.PAKET_ID' => $reqId));
  $katalogrekananRow->firstRow();
  if ($katalogrekananRow->getField('STATUS') == '')
    exit;
?>

<html moznomarginboxes mozdisallowselectionprint>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<base href="<?=base_url()?>" />
 
<!-- <link rel="stylesheet" href="css/print.css" type="text/css"> -->
 
</head>

<body>
 
<div class="logo"><img src="images/<?= SYSTEM_LOGO_CETAK ?>" height="75" /></div> 
<!-- <div class="logo"><img src="images/logo-cetak.png" height="75" /></div> -->
<div class="judul">
  PEMBELIAN LANGSUNG <br>
  <?php 
  if ($katalogrekananRow->getField('STATUS') != '0') {
    echo '<h3 style="text-align:center">#'.$katalogrekananRow->getField('NOINVOICE').'</h3>';
  } 
  ?>
</div><br>

<!-- <div class="nomor" style="font-size: 12px">Nomor :  <?=$nomor?></div><br> -->
 
    <table class="table2"> 
      <tr>
        <td align="left" valign="top" width="100%" class="tdnoborder2"><?=$reqNamaPaket?></td>
      </tr>  
      <tr>
        <td align="left" valign="top" width="100%" class="tdnoborder2"><?=$reqMataUang.' '.numberToIna($reqNilaiPekerjaan)?></td>
      </tr>
      <tr>
        <td align="left" valign="top" width="100%" class="tdnoborder2"><?= $reqLokasiPekerjaan ?></td>
      </tr>
      <tr>
        <td align="left" valign="top" width="100%" class="tdnoborder2"><?= $paket->getField("PAKET_JENIS").' '.$paket->getField("METODE_LELANG") ?></td>
      </tr>
    </table>

    <table class="table2"> 
      <tr>
        <td align="left" valign="top" width="100%" class="tdnoborder2"><?= $katalogrekananRow->getField('USER_NAMA') ?></td>
      </tr>  
      <tr>
        <td align="left" valign="top" width="100%" class="tdnoborder2">NPWP: <?= $katalogrekananRow->getField('NPWP') ?></td>
      </tr>
      <tr>
        <td align="left" valign="top" width="100%" class="tdnoborder2"><?= $katalogrekananRow->getField('ALAMAT').', '.$katalogrekananRow->getField('KOTA').' - '.$katalogrekananRow->getField('KODEPOS') ?></td>
      </tr>
      <tr>
        <td align="left" valign="top" width="100%" class="tdnoborder2"><?= $katalogrekananRow->getField('EMAIL') ?></td>
      </tr>
      <tr>
        <td align="left" valign="top" width="100%" class="tdnoborder2"><?= $katalogrekananRow->getField('TELEPON_KODE').' - '.$katalogrekananRow->getField('TELEPON') ?></td>
      </tr>
    </table>
 
 
  HISTORY CHAT NEGOSIASI:

  <?php 
  $this->load->model("NegoShoutbox");
  $nego_shoutbox = new NegoShoutbox();
  $nego_shoutbox->selectByParams(array(), -1, -1, " AND A.PAKET_ID = '".$reqId."'"); 
  ?>

    <table class="table">
      <tr class="tr-bc">
        <th class="td" style="width: 70%">Chat Negosiasi</th>  
        <th class="td" style="width: 20%">Tanggal</th>
      </tr>
      <?php 
      while($nego_shoutbox->nextRow())
      { 
        $nama = $nego_shoutbox->getField('NAMA');
        $pesan = $nego_shoutbox->getField('PESAN');
        $waktu = $nego_shoutbox->getField('WAKTU');?>
      <tr>
        <th class="td" style="width: 70%">
          <span class="font10"><?= $nama ?></span><br>
          <?= $pesan ?>
        </th>  
        <th class="td font10" style="width: 20%"><?= $waktu ?></th>
      </tr>
      <?php 
      } ?>
    </table>


  <div class="nomor-oe">
    <div class="data">
         <?= SYSTEM_SAH ?>
         <?php if($paketInfo->bahasa == "EN") echo "<br><br><em>".SYSTEM_SAH_EN."</em>"; ?>
    </div>

</body>
</html>