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
// $template = str_replace("|-|", " ", httpFilterRequest("template"));

$this->load->model(array("Contracting","Userlogin","Contractingrekanan"));
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

$spkpks = new Contractingrekanan();
$spkpks->selectViewPKSSPK(array("A.CONTRACTINGREKANANID" => $reqId));
$spkpks->firstRow();
$reqUnitKerjaId = $spkpks->getField('UNIT_KERJA_ID');
$reqNama = $spkpks->getField('NAMA') ?: '-';
$reqPICKontrak = $spkpks->getField('PIC_KONTRAK') ?: '-';
$reqPICPengendali = $spkpks->getField('PIC_PENGENDALI') ?: '-';
$reqPICPenyelesaian = $spkpks->getField('PIC_PENYELESAIAN') ?: '-';
$reqPengguna = $spkpks->getField('PENGGUNA') ?: '-';
$reqPO = $spkpks->getField('CR_PO') ?: '-';
$reqJnsKontrakStr = $spkpks->getField('JNS_KONTRAK_STR') ?: ''; 

$userPPK = new Userlogin();
$userPPK->selectByParams(array("UNIT_KERJA_ID" => $reqUnitKerjaId, "USER_TYPE_ID" => "28", "USER_AKTIF" => '1'));
$userPPK->firstRow();
$reqPPK = $userPPK->getField("USER_NAMA");
$reqPPKJabatan = $userPPK->getField("USER_JABATAN");
$reqPihak1Nama = $spkpks->getField('CR_PIHAK1_NAMA') ?: $reqPPK;
$reqPihak1Jabatan = $spkpks->getField('CR_PIHAK1_JABATAN') ?: $reqPPKJabatan;

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

$cekPenilaianTotal->selectPenilaian(array("PAKET_ID" => $reqPaketId,"A.REKANAN_ID" => $pemenang, "CONTRACTINGREKANANID" => $reqId));

if ($cekPenilaianTotal->countRow() > 0) {
  $paketpenilaian->selectParent(array(), -1, -1, '');
  $totalPenilaian = $paketpenilaian->countRow();
} 

$nomor = $paketInfo->pr_group_number."/PENILAIAN.REKANAN/".getYear($paketInfo->tanggal);

$legal = new Contractingrekanan();
$legal->selectViewLegal(array("A.CONTRACTINGREKANANID" => $reqId));
$legal->firstRow();
$reqLegalNomorPKS = $legal->getField('CR_LEGAL_NOMOR_PKS') ?: '-';

?>

<html moznomarginboxes mozdisallowselectionprint>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<base href="<?=base_url()?>" />

</head>

<body>
 
<div class="logo"><img src="images/<?= SYSTEM_LOGO_CETAK ?>" height="75" /></div>
<div class="judul">
  FORMULIR PENILAIAN KINERJA PENYEDIA BARANG/JASA   
</div><br>

  <div class="pekerjaan">
    <?=strtoupper($paketInfo->nama)?>
  </div><br>
  <div class="isi">
    Nomor PO: <?= $reqPO ?> <br>
    Nomor <?= $reqJnsKontrakStr ?> </small> : <?= $reqLegalNomorPKS ?> <br>
    Kode Penyedia: <?= $rekanan->getField("KODE") ?> <br>
    Nama Pemenang: <?= $rekanan->getField("NAMA") ?> <br>
  </div>

      <div class="area-dokumen">
      <?php
        $nourut = 1;
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
            <td  class="td" align="center" valign="middle">Sangat Buruk</td>
            <td  class="td" align="center" valign="middle">Buruk</td>
            <td  class="td" align="center" valign="middle">Cukup</td>
            <td  class="td" align="center" valign="middle">Baik</td>
            <td  class="td" align="center" valign="middle">Sangat Baik</td>
          </tr>
          <?php
          $no     = 1;
          $noChild  = 0;
          $nilaiTotal.$nourut = 0;
          while($paketpenilaianChild->nextRow())
          {
            $cekPenilaian->selectPenilaian(array("PAKET_ID" => $reqPaketId,"A.REKANAN_ID" => $pemenang, "PPT_ID" => $paketpenilaianChild->getField("PPT_ID"), "PPT_PARENT_ID" => $paketpenilaianChild->getField("PPT_PARENT_ID")), -1, -1, '');
            $cekPenilaian->firstRow();
            $nilai = $cekPenilaian->getField("NILAI");
            $note  = $cekPenilaian->getField("NOTE");
            $namaPICUnit  = $cekPenilaian->getField("NAMA_PIC_UNIT");
            $jabatanPICUnit  = $cekPenilaian->getField("JABATAN_UNIT");
            $namaKasubdit  = $cekPenilaian->getField("NAMA_KASUBDIT");
            $jabatanKasubdit  = $cekPenilaian->getField("JABATAN_KASUBDIT");
            $namaPPK  = $cekPenilaian->getField("NAMA_PPK");
            $jabatanPPK  = $cekPenilaian->getField("JABATAN_PPK");

            $nilaiTotal.$nourut += $cekPenilaian->getField("NILAI");
          ?>
          <tr class="gelap">
            <td class="td" valign="top"><strong><?=$no?></strong></td>
            <td class="td" valign="top"><b><?=$paketpenilaianChild->getField("NAMA")?></b><br><?=$paketpenilaianChild->getField("NOTE")?></td>
            <?php
            switch ($nilai) {

              case '1':
                echo
                '
                  <td class="td" align="center" valign="top" style="width:60px;font-family:dejavusans;font-size:18px">✓</td>
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                ';
                break;

              case '2':
                echo
                '
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                  <td class="td" align="center" valign="top" style="width:60px;font-family:dejavusans;font-size:18px">✓</td>
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                ';
                break;

              case '3':
                echo
                '
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                  <td class="td" align="center" valign="top" style="width:60px;font-family:dejavusans;font-size:18px">✓</td>
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                ';
                break;

              case '4':
                echo
                '
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                  <td class="td" align="center" valign="top" style="width:60px;font-family:dejavusans;font-size:18px">✓</td>
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                ';
                break;

              case '5':
                echo
                '
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                  <td class="td" align="center" valign="top" style="width:60px;font-family:dejavusans;font-size:18px">✓</td>
                ';
                break;

              default:
                echo
                ' <td class="td" align="center" valign="top" style="width:60px"></td>
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                  <td class="td" align="center" valign="top" style="width:60px"></td>
                ';
                break;
            }
            ?>

            </tr>
          <?php $no++; $noChild++;
          } ?>
            <tr class="gelap">
              <td class="td" colspan="2" style="text-align: center; font-weight: bold;"><b>TOTAL</b></td>
              <td class="td" colspan="5" style="text-align: center; font-weight: bold;"><b><?= $nilaiTotal.$nourut ?></b></td>
            </tr>
          </table>
          <!-- <div class="isi" style="margin:5px 0 20px 0; border:1px solid #000; padding: 20px 10px">
            Komentar <i>(Comments)</i> : <?=$note?>
          </div> -->
          <?php
        } ?>
      </div>

    <div class="isi">
      <h4>Hasil Penilaian :</h4>
    </div>
    <div class="area-dokumen">
      <table class="table table-bordered">
        <tr class="tr">
          <td class="td" align="center" valign="middle" width="7%">No.</td>
          <td class="td" align="center" valign="middle" width="48%">Aspek Penilaian</td>
          <td class="td" align="center" valign="middle" width="18%">Total Skor <br>(Dari Maks. 20)</td>
          <td class="td" align="center" valign="middle" width="10%">Bobot (%)</td>
          <td class="td" align="center" valign="middle" width="30%">Skor Tertimbang</td>
        </tr>
        <?php
        $noHasil=1;
        // echo "<pre>"; print_r($paketpenilaianrekap); die();
        while ($paketpenilaianrekap->nextRow()) {
          $totalNilai += $paketpenilaianrekap->getField("RATA2_SKOR");
          $totalSkorUI += $paketpenilaianrekap->getField("SKOR_UI");
          $totalPresentasi += $paketpenilaianrekap->getField("PRESENTASI");
          $totalSkorTertimbangUI += $paketpenilaianrekap->getField("SKOR_TERTIMBANG_UI");
         ?>
        <tr>
          <td class="td" align="center" valign="middle"><?=$noHasil?></td>
          <td class="td" align="left" valign="middle"><?=$paketpenilaianrekap->getField("NAMA")?></td>
          <td class="td" align="center" valign="middle"><?= round($paketpenilaianrekap->getField("SKOR_UI"),2) ?></td>
          <td class="td" align="center" valign="middle"><?=$paketpenilaianrekap->getField("PRESENTASI")?>%</td>
          <td class="td" align="center" valign="middle"><?= round($paketpenilaianrekap->getField("SKOR_TERTIMBANG_UI"),2) ?></td>
        </tr>
        <?php $noHasil++;
          } ?>
        <tr class="tr-bc">
          <td class="td" colspan="3" align="center" valign="middle">TOTAL SKOR AKHIR</td>
          <td class="td" align="center" valign="middle"><?= $totalPresentasi; ?>%</td>
          <td class="td" align="center" valign="middle"><?= $totalSkorTertimbangUI; ?></td>
        </tr>
        <tr class="tr-bc">
          <td class="td" colspan="4" align="center" valign="middle">NILAI AKHIR</td>
          <td class="td" align="center" valign="middle">
            <?= $totalSkorTertimbangUI * 5; ?> <br>
            <?= setGrade($totalSkorTertimbangUI * 5); ?>
          </td>
        </tr>
      </table>
    </div>

    <div class="area-dokumen" style="margin:30px 0">
    <table class="table" > 
      <tr>
        <td align="center" width="33%">
          Pimpinan Unit Kerja <br><br>
          <div style="font-family:sacramento; font-size:22px;">
              <?= ucfirst($namaPICUnit) ?> <br>
          </div> 
              (<?= $jabatanPICUnit ?>)
        </td>
        <td align="center" width="33%">
          Kasubdit Manajemen Kontrak<br><br>
          <div style="font-family:sacramento; font-size:22px;">
              <?= ucfirst($namaKasubdit) ?> <br>
          </div> 
              (<?= $jabatanKasubdit ?>)
        </td>
        <td align="center" width="33%">
          Pejabat Pembuat Komitmen<br><br>
          <div style="font-family:sacramento; font-size:22px;">
              <?= ucfirst($namaPPK) ?> <br>
          </div> 
              (<?= $jabatanPPK ?>)
        </td>
      </tr> 
    </table> 
  </div> 

</body>
</html>
