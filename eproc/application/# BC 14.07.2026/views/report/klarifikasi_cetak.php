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
$this->load->model("Rekanan");
$this->load->model("PaketKlarifikasi");
$this->load->model("PaketTahap");
$this->load->model("Aanwijzing");
$this->load->model("PhpShoutbox");
$this->load->model("PaketPembukaanValidasi");
$this->load->model(array("PaketEvaluasiAdminTawar","PaketEvaluasiTeknisTawar","PaketEvaluasiHargaTawar","PaketDokumen")); 
include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/string.func.php");
$this->load->model("PaketPanitia");
$this->load->model("PaketPihakLain");
$this->load->library("AES");
include_once("lib/phpqrcode/qrlib.php");


/* create objects */
$paket_rekanan = new PaketRekanan();
$rekanan = new Rekanan();
$aanwijzing = new Aanwijzing();
$php_shoutbox = new PhpShoutbox();
$paket_panitia = new PaketPanitia();
$paket_pihak_lain = new PaketPihakLain();
$php_shoutbox_rekanan = new PhpShoutbox(); 
$paket_pembukaan_validasi = new PaketPembukaanValidasi();

$paket_evaluasi_admin = new PaketEvaluasiAdminTawar();
$paket_evaluasi_teknis = new PaketEvaluasiTeknisTawar();
$paket_evaluasi_harga = new PaketEvaluasiHargaTawar();

$paket_tahap_metode = new PaketTahap();
$paket_tahap = new PaketTahap();
$reqId = httpFilterGet("reqId");
$thisId = httpFilterGet("thisId");
$reqToken = httpFilterGet("reqToken"); 
$reqUser = httpFilterGet("reqUser"); 

$PNG_TEMP_DIR = 'uploads/';

$paketInfo->getPaket($reqId);
$reqNama = $paketInfo->nama;
$reqJenisPengadaan = $paketInfo->jenis;
$reqRekananIdPemenang = $paketInfo->rekanan_id_pemenang;
$reqMetodeEvaluasi = $paketInfo->metode_evaluasi;
$reqBidding = $paketInfo->bidding;

$i=0; 

$arrTahapan  = NEGOSIASI;

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
$paket_tahap->selectByParams(array("URUT" => $arrTahapan[$jenis_tahap], "PAKET_ID" => $reqId));
$paket_tahap->firstRow();

$time = strtotime($paket_tahap->getField("TANGGAL_AWAL"));
$klarifikasi_hari = date('w', $time);
$klarifikasi_tanggal = (int)date('d', $time);
$klarifikasi_bulan = (int)date('m', $time);
$klarifikasi_tahun = (int)date('Y', $time);
$klarifikasi_dmy = date('d-m-Y', $time);
$klarifikasi_ymd = date('Y-m-d', $time);

$paket_panitia->selectByParamsBeritaAanwijzing(array("A.PAKET_ID" => $reqId));
$i=0;
while($paket_panitia->nextRow())
{
	$arrPanitia[$i]["NAMA"] = strtoupper($paket_panitia->getField("NAMA"));
	$arrPanitia[$i]["NIP"] = $paket_panitia->getField("NIP");
	$arrPanitia[$i]["QRCODE"] = $paket_panitia->getField("NIP"); //KODE_QR
	$i++;
}
$paket_pihak_lain->selectByParamsBeritaAanwijzing(array("A.PAKET_ID" => $reqId));
$i=0;
while($paket_pihak_lain->nextRow())
{
	$arrPihakLain[$i]["NAMA"] = strtoupper($paket_pihak_lain->getField("USER_NAMA"));
	$arrPihakLain[$i]["NIP"] = $paket_pihak_lain->getField("NIP_LOGIN_ID");
	$arrPihakLain[$i]["QRCODE"] = $paket_pihak_lain->getField("NIP_LOGIN_ID");
	$i++;
}

$paket_pembukaan_validasi->selectByParamsValidasi(array("A.PAKET_ID" => $reqId));

$nomor = $paketInfo->pr_group_number."/BA.AANWIJZING/".getYear($paketInfo->tanggal);
$reqMetodeLelangId    = $paketInfo->metode_lelang_id;

if ($reqUser == '6') { // kalau penyedia dicek
  if ($reqBidding == 1) { // e-Reverse Auction
    $paket_rekanan->selectUrutPenawaran(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => $thisId, "A.LULUS_PENDAFTARAN" => 1, "A.KIRIM_PENAWARAN" => 1, "A.LULUS_PENAWARAN" => 1), -1, -1, "", "", " ORDER BY NILAI_PENAWARAN ASC, KIRIM_PENAWARAN_TANGGAL ASC ");
    // echo $paket_rekanan->countRow().'---'; die();
    if ($paket_rekanan->countRow() == 0) // hanya untuk penyedia yang terundang
      exit;

  } else { // Negosiasi
    if($reqRekananIdPemenang != $thisId) // hanya untuk pemenang dan terundang untuk negosiasi
      exit;
  }
} else {
}
$rekanan->selectByParams(array("A.REKANAN_ID" => $thisId));
$rekanan->firstRow();
$nama_penyedia = $rekanan->getField('NAMA');
$npwp_penyedia = $rekanan->getField('NPWP');
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

</head> 

<body class="body">
<div class="logo"><img src="images/<?= SYSTEM_LOGO_CETAK ?>" height="75" /></div>
<div class="judul">
	HASIL PEMBUKTIAN 
</div><br>

<!-- <div class="nomor">Nomor :  <?=$nomor?></div><br> -->

<div class="pekerjaan">
    PEKERJAAN <?php if($paketInfo->bahasa == "EN") echo "/ <em>WORKS</em>"; ?><br />
    <?=strtoupper($paketInfo->nama)?>
</div><br>

<div class="isi">
   Pada hari ini, <?=strtoupper(getHari($klarifikasi_hari));?> tanggal <?=strtoupper(getTerbilang($klarifikasi_tanggal));?> bulan <?=strtoupper(getNameMonth($klarifikasi_bulan));?> tahun <?=strtoupper(getTerbilang($klarifikasi_tahun));?> (<?=$klarifikasi_dmy?>), mulai pukul <?=(($paket_tahap->getField("JAM_AWAL") == "") ? '00:00' : $paket_tahap->getField("JAM_AWAL"))?> WIB sampai dengan selesai telah diadakan pembuktian dengan <?= $nama_penyedia ?>. 
  <div style="height:7px;"></div>
  <!-- Klarifikasi Tambahan online diikuti oleh: -->
</div>  
        <div class="area-dokumen"> 
          <table class="table">
            <tr class="tr-bc">
              <td class="tdno">No.</td>
              <td class="td">Dokumen</td>
              <td class="td" width="70px">Sesuai?</td>
            </tr>
            <?php 
            $paket_evaluasi_admin->selectByParams(array("PAKET_ID" => $reqId));
            if ($paket_evaluasi_admin->countRow() > 0) {
              echo '<tr class="tdno"><td colspan="3" class="td">Dokumen Administrasi</td></tr>';
            }
            $no = 1;
            while($paket_evaluasi_admin->nextRow())
            { ?>
              <tr>
                <td class="tdno"><?=$no?></td>
                <td class="td"> 
                <?php 
                $paket_dokumen = new PaketDokumen();
                $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $thisId, "TRIM(NAMA)" => trim($paket_evaluasi_admin->getField("NAMA"))));
                $paket_dokumen->firstRow();  
                $checkedAdmin = ''; 
                ?>  
                  <?=$paket_evaluasi_admin->getField("NAMA")?><br>
                  <?php 
                    if ($paket_dokumen->getField("PAKET_DOKUMEN_ID")) 
                    { ?> 
                      <small style="font-size:10px">Catatan: <?=$paket_dokumen->getField("CATATAN") ?: '-'?></small>
                    <?php 
                    } else {
                      echo '<span style="font-size:10px;font-style: italic;">Tidak upload</span>';
                    } ?>
                </td>
                <td class="td" style="text-align:center;">
                  <?php if($paket_dokumen->getField("VERIFIKASI") == "1") { echo "Ya"; } else { echo "Tidak"; }  ?>
                </td>
              </tr> 
            <?php 
                  unset($paket_dokumen);
              $no++;
            } ?>

            <?php 
            $no=1;
            $paket_evaluasi_teknis->selectByParams(array("PAKET_ID" => $reqId));
            if ($paket_evaluasi_teknis->countRow() > 0) {
              echo '<tr class="tdno"><td colspan="3" class="td">Dokumen Teknis</td></tr>';
            }
            while($paket_evaluasi_teknis->nextRow())
            { ?>
              <tr>
                <td class="tdno"><?=$no?></td>
                <td class="td">
                <?php 
                $paket_dokumen = new PaketDokumen();
                $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $thisId, "TRIM(NAMA)" => trim($paket_evaluasi_teknis->getField("NAMA"))));
                $paket_dokumen->firstRow(); 
                $checkedTeknis = ''; 
                 ?>
                    <?=$paket_evaluasi_teknis->getField("NAMA")?><br>
                    <?php 
                    if ($paket_dokumen->getField("PAKET_DOKUMEN_ID")) 
                    { ?> 
                      <small style="font-size:10px">Catatan: <?=$paket_dokumen->getField("CATATAN") ?: '-'?></small>
                    <?php 
                    } else {
                      echo '<span style="font-size:10px;font-style: italic;">Tidak upload</span>';
                    } ?>
                </td>
                <td class="td" style="text-align:center;"> 
                  <?php if($paket_dokumen->getField("VERIFIKASI") == "1") { echo "Ya"; } else { echo "Tidak"; }  ?>
                </td>
              </tr> 
            <?php 
                  unset($paket_dokumen);
              $no++;
            } ?>

            <?php 
            $no=1;
            $paket_evaluasi_harga->selectByParams(array("PAKET_ID" => $reqId));
            if ($paket_evaluasi_harga->countRow() > 0) {
              echo '<tr class="tdno"><td colspan="3" class="td">Dokumen Harga</td></tr>';
            }
            while($paket_evaluasi_harga->nextRow())
            { ?>
              <tr>
                <td class="tdno"><?=$no?></td>
                <td class="td">
                <?php 
                $paket_dokumen = new PaketDokumen();
                $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $thisId, "TRIM(NAMA)" => trim($paket_evaluasi_harga->getField("NAMA"))));
                $paket_dokumen->firstRow(); 
                $checkedHarga = ''; 
                ?>
                  <?=$paket_evaluasi_harga->getField("NAMA")?><br>
                  <?php 
                    if ($paket_dokumen->getField("PAKET_DOKUMEN_ID")) 
                    { ?> 
                      <small style="font-size:10px">Catatan: <?=$paket_dokumen->getField("CATATAN") ?: '-'?></small>
                    <?php 
                    } else {
                      echo '<span style="font-size:10px;font-style: italic;">Tidak upload</span>';
                    } ?>
                </td>
                <td class="td" style="text-align:center;">
                  <?php if($paket_dokumen->getField("VERIFIKASI") == "1") { echo "Ya"; } else { echo "Tidak"; }  ?>
                </td>
              </tr> 
            <?php 
                  unset($paket_dokumen);
              $no++;
            } ?>
          </table>
        </div>

         <div class="area-dokumen"> 
            <p><b>
              <?php 
              if ($reqMetodeLelangId == '2') {
               } else {
                echo "PENYEDIA :";
               } ?> </b>
             </p>

             <table class="table">
              <tr class="tr-bc">
                <td class="td" align="center">Nama</td>
                <td class="td" align="center" width="250px">TTD</td>
              </tr> 
              <tr> 
                  <td class="td" align="left" height="100px">
                    <?=$nama_penyedia;?><br><span style="font-size:10px">NPWP: <?=$npwp_penyedia;?></span>
                  </td>
                  <td class="td" style="text-align:center"><br><br><br><br><br>
                    ( <span style="color: #b7b7b7;">....................................................</span>)
                  </td>
              </tr>
            </table> 

            <p><b>
              <?php 
              if ($reqMetodeLelangId == '2') {
               } else {
                echo "PELAKSANA PENGADAAN BARANG DAN JASA :";
               } ?> </b>
             </p>

             <table class="table">
              <tr class="tr-bc">
                <td class="td" align="center">Nama</td>
                <td class="td" align="center" width="250px">TTD</td>
              </tr>
              <?php
              $no=1;
                while($paket_pembukaan_validasi->nextRow())
                {
               ?>
              <tr> 
                  <td class="td" align="left" height="100px">
                    <?=$paket_pembukaan_validasi->getField("NAMA");?><br><span style="font-size:10px">NUP/NIP: <?=$paket_pembukaan_validasi->getField("NIP");?></span>
                  </td>
                  <td class="td" ></td>
              </tr>
              <?php $no++; } ?>
            </table> 
 
            LAMPIRAN <?php if($paketInfo->bahasa == "EN") echo "/ <em>ATTACHMENTS</em>"; ?> : 

            <table  class="table">
              <tbody>
                <?php 
                $paket_klarifikasi = new PaketKlarifikasi();
                $paket_klarifikasi->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $thisId));

                if ($paket_klarifikasi->countRow() < 0) {
                   echo '<tr><td colspan="2">Belum ada chat</td></tr>';
                } else {  
                  $i=1;
                  while($paket_klarifikasi->nextRow())
                  { 
                    $tglupload = explode('.', $paket_klarifikasi->getField("TANGGAL_UPLOAD"));
                ?>
                  <tr>
                    <td class="td" width="100%">
                      <i style="font-size: 10px;"><b> <?=$paket_klarifikasi->getField("USER_NAMA")?> </b></i> <br>
                        <?=$paket_klarifikasi->getField("KETERANGAN")?> <br> 
                        <small style="font-size: 9px;"><?=$tglupload[0] ?></small>
                    </td>  
                  </tr>
                <?php 
                  $i++;
                  }
                }
              ?>
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
