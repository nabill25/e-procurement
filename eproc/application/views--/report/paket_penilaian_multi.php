<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->library("kauth");  $userLogin = new kauth();
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->LEGAL =  $this->kauth->getInstance()->getIdentity()->LEGAL;

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/penilaian.func.php");

$reqId = httpFilterRequest("reqId"); // contractingrekananid
$pemenang = httpFilterRequest("pemenang"); // pemenang
$template = str_replace("|-|", " ", httpFilterRequest("template"));

$this->load->model("Contracting");
$this->load->model("Contractingrekanan");
$this->load->model("PaketRekanan");
$this->load->model("PaketPenilaian");
$this->load->model("Rekanan");

$contracting = new Contracting();

$contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
$contracting->firstRow();

$reqJenisKontrak = $contracting->getField('JENIS_KONTRAK') ?: '-';
$reqRekananId = $contracting->getField('PEMENANG') ?: '-';
$reqPaketId = $contracting->getField('PAKET_ID') ?: '-';
$reqPenggunaStr = $contracting->getField('PENGGUNA_STR') ?: '-';
$reqPenggunaJabatan = $contracting->getField('JABATAN') ?: '-';
$reqPenggunaNIP = $contracting->getField('NIP') ?: '-';
$reqPPKStr = $contracting->getField('PPK_STR') ?: '-';
$reqPPKJabatan = $contracting->getField('PPK_JABATAN') ?: '-';

$PNG_TEMP_DIR = 'uploads/';

/* create objects */
$rekanan = new Rekanan();
$paketpenilaian = new PaketPenilaian();
$paketpenilaianChild = new PaketPenilaian();
$paketpenilaianChildCount = new PaketPenilaian();
$cekPenilaian = new PaketPenilaian();
$cekPenilaianTotal = new PaketPenilaian();
$paketpenilaianrekap = new PaketPenilaian();

$paketInfo->getPaket($reqPaketId);
$reqNama = $paketInfo->nama;

$rekanan->selectByParams(array("A.REKANAN_ID" => $pemenang), -1, -1, '');
$rekanan->firstRow();

// $paketpenilaianrekap->hasilNilai($reqPaketId,$pemenang);
$paketpenilaianrekap->getHasil($reqId,$pemenang);

$cekPenilaianTotal->selectPenilaian(array("PAKET_ID" => $reqPaketId,"REKANAN_ID" => $pemenang, "CONTRACTINGREKANANID" => $reqId));

if ($cekPenilaianTotal->countRow() > 0) {
  $paketpenilaian->selectParent(array("TEMPLATE" => $template), -1, -1, '');
  $totalPenilaian = $paketpenilaian->countRow();
} 

$nomor = $paketInfo->pr_group_number."/PENILAIAN.REKANAN/".getYear($paketInfo->tanggal);

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


<link rel="stylesheet" href="css/dokumen-pembukaan-penawaran.css" type="text/css">
</head>

<body>

<div class="tombol-print">
<input type="button" value="Print" onClick="print();">
</div>

<br>
<div class="logo"><img src="images/<?= SYSTEM_LOGO_CETAK ?>" height="75" /></div>
<div class="judul">
  PENILAIAN REKANAN
  <?php
    if($paketInfo->bahasa == "EN")
    echo "<br>VENDOR RATINGS";
  ?>
</div><br>


<!-- <div class="nomor">Nomor :  <?=$nomor?></div><br> -->

  <div class="pekerjaan">
      PEKERJAAN <?php if($paketInfo->bahasa == "EN") echo "/ <em>WORKS</em>"; ?><br />
      <?=strtoupper($paketInfo->nama)?>
  </div><br>
  <div class="isi">
    Nama Rekanan: <?= $rekanan->getField("NAMA") ?> <br>
    NPWP: <?= $rekanan->getField("NPWP") ?> <br>
    Status: <?= $rekanan->getField("STATUS_CP") ?> <br>
    Alamat: <?= $rekanan->getField("ALAMAT") ?> <br>
    Kota: <?= $rekanan->getField("KOTA") ?> <br>
    No. Telepon: <?= $rekanan->getField("TELEPON_FULL") ?> <br>
    No. Fax: <?= $rekanan->getField("FAX_FULL") ?> <br>
    Email: <?= $rekanan->getField("EMAIL") ?> <br>
  </div>
  <div class="area-dokumen">

  </div>

      <div class="area-dokumen">
      <?php
        while($paketpenilaian->nextRow())
        {
          $paketpenilaianChild->selectChild(array("PPT_PARENT_ID" => $paketpenilaian->getField("PPT_ID")), -1, -1, '');
          $total = $paketpenilaianChildCount->getCountByParams(array("PPT_PARENT_ID" => $paketpenilaian->getField("PPT_ID")));
         ?>
        <table class="table">
          <tr class="tr">
            <td colspan="7" class="td">
            <?= '<b>'.$paketpenilaian->getField("KODE").'. '.$paketpenilaian->getField("NAMA").'</b>'?>
            </td>
          </tr>
          <tr class="tr-bc">
            <td  class="td" align="center" valign="middle" width="7%">No.</td>
            <td  class="td" align="left" valign="middle" width="50%">Deskripsi Penilaian</td>
            <td  class="td" align="center" valign="middle">Kurang</td>
            <td  class="td" align="center" valign="middle">Cukup</td>
            <td  class="td" align="center" valign="middle">Baik</td>
            <td  class="td" align="center" valign="middle">Sangat Baik</td>
          </tr>
          <?php
          $no     = 1;
          $noChild  = 0;
          while($paketpenilaianChild->nextRow())
          {
            $cekPenilaian->selectPenilaian(array("PAKET_ID" => $reqPaketId,"REKANAN_ID" => $pemenang, "PPT_ID" => $paketpenilaianChild->getField("PPT_ID"), "PPT_PARENT_ID" => $paketpenilaianChild->getField("PPT_PARENT_ID")), -1, -1, '');
            $cekPenilaian->firstRow();
            $nilai = $cekPenilaian->getField("NILAI");
            $note  = $cekPenilaian->getField("NOTE");
          ?>
          <tr class="gelap">
            <td class="td" valign="top"><strong><?=$no?></strong></td>
            <td class="td" valign="top"><b><?=$paketpenilaianChild->getField("NAMA")?></b><br><?=$paketpenilaianChild->getField("NOTE")?></td>
            <?php
            switch ($nilai) {

              case '2':
                echo
                '
                  <td class="td" align="center" valign="top">&#10004;</td>
                  <td class="td" align="center" valign="top"></td>
                  <td class="td" align="center" valign="top"></td>
                  <td class="td" align="center" valign="top"></td>
                ';
                break;

              case '3':
                echo
                '
                  <td class="td" align="center" valign="top"></td>
                  <td class="td" align="center" valign="top">&#10004;</td>
                  <td class="td" align="center" valign="top"></td>
                  <td class="td" align="center" valign="top"></td>
                ';
                break;

              case '4':
                echo
                '
                  <td class="td" align="center" valign="top"></td>
                  <td class="td" align="center" valign="top"></td>
                  <td class="td" align="center" valign="top">&#10004;</td>
                  <td class="td" align="center" valign="top"></td>
                ';
                break;

              case '5':
                echo
                '
                  <td class="td" align="center" valign="top"></td>
                  <td class="td" align="center" valign="top"></td>
                  <td class="td" align="center" valign="top"></td>
                  <td class="td" align="center" valign="top">&#10004;</td>
                ';
                break;

              default:
                echo
                ' <td class="td" align="center" valign="top"></td>
                  <td class="td" align="center" valign="top"></td>
                  <td class="td" align="center" valign="top"></td>
                  <td class="td" align="center" valign="top"></td>
                ';
                break;
            }
            ?>

            </tr>
          <?php $no++; $noChild++;
          } ?>
          </table>
          <div class="isi" style="margin:5px 0 20px 0; border:1px solid #000; padding: 20px 10px">
            Komentar <i>(Comments)</i> : <?=$note?>
          </div>
          <?php
        } ?>
      </div>

    <div class="isi">
      <h4>Hasil Penilaian :</h4>
    </div>
    <div class="area-dokumen">
      <table class="table">
        <tr class="tr">
          <td class="td" align="center" valign="middle" width="7%">No.</td>
          <td class="td" align="left" valign="middle" width="73%">Deskripsi Penilaian</td>
          <td class="td" align="center" valign="middle" width="10%">Nilai</td>
        </tr>
        <?php
        $noHasil=1;
        // echo "<pre>"; print_r($paketpenilaianrekap); die();
        while ($paketpenilaianrekap->nextRow()) {
          $totalNilai += $paketpenilaianrekap->getField("TOTAL_SKOR");
         ?>
        <tr>
          <td class="td" align="center" valign="middle"><?=$noHasil?></td>
          <td class="td" align="left" valign="middle"><?=$paketpenilaianrekap->getField("NAMA")?></td>
          <td class="td" align="center" valign="middle"><?= round($paketpenilaianrekap->getField("TOTAL_SKOR"),2) ?></td>
        </tr>
        <?php $noHasil++;
          } ?>
        <tr class="tr-bc">
          <td class="td" colspan="2" align="center" valign="middle">TOTAL</td>
          <td class="td" align="center" valign="middle"><?= $totalNilai; ?></td>
        </tr>
        <tr class="tr-bc">
          <td class="td" colspan="2" align="center" valign="middle">Kesimpulan Penilaian Akhir</td>
          <td class="td" align="center" valign="middle">
            <?= setGrade($totalNilai); ?>
          </td>
        </tr>
      </table>
    </div>

    <div class="area-dokumen" style="margin:30px 0">
    <table class="table" > 
      <tr>
        <td align="center" width="50%">
          <?= $reqPenggunaJabatan ?>
          <br><br><br><br><br><br><br>
          <?= $reqPenggunaStr ?> <br> 
        </td>
        <td align="center" width="50%">
          <?= $reqPPKJabatan ?>
          <br><br><br><br><br><br><br>
          <?= $reqPPKStr ?> 
        </td>
      </tr> 
    </table> 
  </div> 

</body>
</html>
