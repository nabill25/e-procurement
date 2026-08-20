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
$paket_rekanan = new PaketRekanan();
$aanwijzing = new Aanwijzing();
$php_shoutbox = new PhpShoutbox();
$paket_panitia = new PaketPanitia();
$paket_pihak_lain = new PaketPihakLain();
$php_shoutbox_rekanan = new PhpShoutbox();
$paket_aanwijzing_rekanan = new PaketAanwijzing();
$paket_aanwijzing = new PaketAanwijzing();
$paket_aanwijzing_first = new PaketAanwijzing();
$paket_pembukaan_validasi = new PaketPembukaanValidasi();

$paket_tahap_metode = new PaketTahap();
$paket_tahap = new PaketTahap();
$reqId = httpFilterGet("reqId");
$reqRekananId = httpFilterGet("reqRekananId"); 

$PNG_TEMP_DIR = 'uploads/';


$paketInfo->getPaket($reqId);

$paket_aanwijzing->selectByParams(array("PAKET_ID" => $reqId, "PARENT_ID" => 0));
$paket_aanwijzing_first->selectByParams(array("PAKET_ID" => $reqId, "PARENT_ID" => 0));
$totalAan = $paket_aanwijzing_first->firstRow();

$i=0; 

// $arrTahapan = array(0, 10, 5,  10, 5,  9,  5,  10, 10, 0, 0, 10, 5,  10, 5);
$arrTahapan                  = AANWIJZING;

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
$paket_tahap->selectByParams(array("URUT" => $arrTahapan[$jenis_tahap], "PAKET_ID" => $reqId));
$paket_tahap->firstRow();

$time = strtotime($paket_tahap->getField("TANGGAL_AWAL"));
$aanwijzing_hari = date('w', $time);
$aanwijzing_tanggal = (int)date('d', $time);
$aanwijzing_bulan = (int)date('m', $time);
$aanwijzing_tahun = (int)date('Y', $time);
$aanwijzing_dmy = date('d-m-Y', $time);
$aanwijzing_ymd = date('Y-m-d', $time);

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
$paket_aanwijzing_rekanan->selectByParamsPeserta3(array("A.PAKET_ID" => $reqId, 'A.PARENT_ID' => 0),'GROUP BY A.REKANAN_KODE, A.REKANAN_USER_ID');
$i=0; 

if ($paket_aanwijzing_rekanan->countRow() > 0) { 
  while($paket_aanwijzing_rekanan->nextRow())
  {
    if ($paket_aanwijzing_rekanan->getField("REKANAN_USER_ID") == $reqRekananId) {
      $arrRekanan[] = $paket_aanwijzing_rekanan->getField("NAMA_PENYEDIA").'<br><small>'.strtoupper($paket_aanwijzing_rekanan->getField("KODE_CUT")).'</small>'; 
    } else {
      $arrRekanan[] = strtoupper($paket_aanwijzing_rekanan->getField("KODE_CUT")); 
    }
    // $arrRekanan[$i]["NIP"] = $php_shoutbox_rekanan->getField("KODE");
    // $arrRekanan[$i]["QRCODE"] = $php_shoutbox_rekanan->getField("KODE_REKANAN");
    $i++;
  }
} else {
  $arrRekanan[] = ". : Tidak ada data : .";
}  
// echo "<pre>"; print_r($arrRekanan); die;

$paket_pembukaan_validasi->selectByParamsValidasi(array("A.PAKET_ID" => $reqId));

$nomor = $paketInfo->pr_group_number."/BA.AANWIJZING/".getYear($paketInfo->tanggal);
$reqMetodeLelangId    = $paketInfo->metode_lelang_id;
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
<div class="logo"><img src="images/<?= SYSTEM_LOGO_CETAK ?>" height="75" /></div>
<div class="judul">
	HASIL RAPAT PENJELASAN
	<?php
    if($paketInfo->bahasa == "EN")
		echo "<br>MINUTES OF PRE-BID MEETING";
	?>
</div><br>

<!-- <div class="nomor">Nomor :  <?=$nomor?></div><br> -->

<div class="pekerjaan">
    PEKERJAAN <?php if($paketInfo->bahasa == "EN") echo "/ <em>WORKS</em>"; ?><br />
    <?=strtoupper($paketInfo->nama)?>
</div><br>

<div class="isi">
   Pada hari ini, <?=strtoupper(getHari($aanwijzing_hari));?> tanggal <?=strtoupper(getTerbilang($aanwijzing_tanggal));?> bulan <?=strtoupper(getNameMonth($aanwijzing_bulan));?> tahun <?=strtoupper(getTerbilang($aanwijzing_tahun));?> (<?=$aanwijzing_dmy?>), mulai pukul <?=(($paket_tahap->getField("JAM_AWAL") == "") ? '00:00' : $paket_tahap->getField("JAM_AWAL"))?> WIB sampai dengan selesai telah diadakan rapat pemberian penjelasan / aanwijzing untuk pekerjaan dimaksud di atas.
  <div style="height:7px;"></div>
  Rapat aanwijzing dilaksanakan secara online melalui website <?= SYSTEM_NAME_URL ?> dengan risalah penjelasan terlampir.
  <div style="height:7px;"></div>
  Hasil ini mengikat dan merupakan bagian yang tidak terpisahkan dari Dokumen Lelang.
  <div style="height:7px;"></div>
  Bagi perserta yang tidak mengikuti pelaksanaan rapat aanwijzing online harus mengikuti apa yang sudah disepakati dalam rapat aanwijzing online ini.
  <div style="height:7px;"></div>
  Rapat aanwijzing online diikuti oleh:

</div>
<?php
if($paketInfo->bahasa == "EN")
{
?>
<div class="isi" style="margin-top:20px; font-style:italic">
  <div>
   On this date, <?=(getHariEn($aanwijzing_hari));?>, <?=getDay($aanwijzing_ymd);?> <?=getNameMonthEn((int)getMonth($aanwijzing_ymd));?> <?=getYear($aanwijzing_ymd);?>, from <?=(($paket_tahap->getField("JAM_AWAL") == "") ? '00:00' : $paket_tahap->getField("JAM_AWAL"))?> <?php if($paket_tahap->getField("JAM_AWAL") <= "12:00") echo "am"; else echo "pm"; ?> to the completed meeting, it has been held the Pre-Bid Meeting / aanwijzing for the work referred to above.
  </div>
  <div style="height:7px;"></div>
  <div>
      Pre-Bid Meeting has been conducted in online through <?= SYSTEM_EMAIL ?> with the following explanations attached.
  </div>
  <div style="height:7px;"></div>
  <div>
      This Minutes of Pre-Bid Meeting is binding and as a part of the tender document.
  </div>
  <div style="height:7px;"></div>
  <div>
      For the participants who are not taking part or following in the online Pre-Bid Meeting, they shall comply with this agreed online Pre-Bid Meeting.
  </div>
  <div style="height:7px;"></div>
  <div>
      Online Pre-Bid Meeting has been followed/participated by:
  </div>

</div>
<?php
}
?>
         <div class="area-dokumen"> 
            <p><b>
              <?php 
              if ($reqMetodeLelangId == '2') {
                echo "PELAKSANA";
               } else {
                echo "PELAKSANA PENGADAAN BARANG DAN JASA :";
               } ?> </b>
             </p>

            <table class="table">
              <tr class="tr-bc">
                <td class="tdno">No</td>
                <td class="td">Nama <?php if($paketInfo->bahasa == "EN") echo "/ <em>Name</em>"; ?></td>
              </tr>
              <?php
                $i = 1;
                  while($paket_pembukaan_validasi->nextRow())
                {
                ?>
                  <tr>
                    <td class="tdno"><?=$i?></td>
                    <td class="td">
                        <?php // echo $paket_pembukaan_validasi->getField("NAMA")?>
                        <?php if ($reqMetodeLelangId == '2') { echo "PEJABAT PEMBELI"; } else { echo "POKJA ".$i; } ?>
                    </td> 
                  </tr>
                <?php
                  $i++;
                }
                ?>       
              </table>


            <p><b>PENYEDIA <?php if($paketInfo->bahasa == "EN") echo "/ <em>VENDORS</em>"; ?> :</b></p>
            <table class="table">
                <tr class="tr-bc">
                  <td class="tdno">NO</td>
                  <td class="td">NAMA <?php if($paketInfo->bahasa == "EN") echo "/ <em>NAME</em>"; ?></td>
                  <!-- <td width="20%" style="text-align:center"><strong>APPROVAL QR CODE</strong></td> -->
                </tr>
                <?php
                $paket_rekanan->selectByParams(array("A.PAKET_ID" => $reqId));

                $i = 0;
                // while($paket_rekanan->nextRow()) 
                foreach ($arrRekanan as $key => $value) {
                ?>
                <tr>
                    <td style="border: 1px solid #b7b7b7;"><?= ($i+1)?>.</td>
                    <td style="border: 1px solid #b7b7b7;">
                      <?= $value; ?> <?php //$arrRekanan[$i]["NAMA"]?></td> 
                </tr>
                <?php
                $i++;
                } 
              ?>
            </table>
            <pagebreak>
            LAMPIRAN <?php if($paketInfo->bahasa == "EN") echo "/ <em>ATTACHMENTS</em>"; ?> :
            <table class="table">
                <tbody>
                  <tr class="tr-bc">
                    <th class="tdno" style="width: 30%"># ID</th>
                    <th class="td" style="width: 50%">Tanya/Jawab</th>  
                    <th class="td" style="width: 20%">Tanggal</th>
                  </tr>
                  <?php
                  if ($totalAan=='') {
                      echo '<tr><td colspan="6">. : Tidak ada data : .</td></tr>';
                    } else {
                    $i=1;
                    while($paket_aanwijzing->nextRow())
                    {
                      $tglupload = explode('.', $paket_aanwijzing->getField("TANGGAL_UPLOAD"));
                      if ($i=1) {
                          $reqRekananUserId = $paket_aanwijzing->getField("REKANAN_USER_ID");
                          // echo '<input type="hidden" name="reqPaketDokumenId" value="'.$paket_aanwijzing->getField("REKANAN_USER_ID").'" />';
                      }
                      // Get Parent
                      $paket_aanwijzing_parent_first = new PaketAanwijzing();
                      $paket_aanwijzing_parent_first->selectByParams(array("PAKET_ID" => $reqId, "PARENT_ID" => $paket_aanwijzing->getField("PAKET_AANWIJZING_ID") ));
                      $paket_aanwijzing_parent_first->firstRow();
                  ?>
                  <tr style="
                  <?php 
                  if ($paket_aanwijzing->getField("REKANAN_USER_ID") == $this->REKANAN_ID) {
                    echo 'background-color: #ff9969; font-weight: bold;';
                  } else {
                    echo 'background-color: #ffd6c3; font-weight: bold;';
                    } ?>
                  ">
                      <td class="tdno">
                        <?php 
                        if ($paket_aanwijzing->getField("REKANAN_USER_ID") == $this->REKANAN_ID) {
                          echo '<i class="fa fa-user" style="color:#000; opacity:1"></i> <span style="color:#000; opacity:1">'. $paket_aanwijzing->getField("KODE_CUT") .'</span>' ; 
                        } else {
                          echo '<i class="fa fa-user" style="color:#000; opacity:1"></i> <span style="color:#000; opacity:1">'. $paket_aanwijzing->getField("KODE_CUT") .'</span>' ; 
                        }
                        ?> 
                      </td> 
                      <td class="td" style="color: #000; opacity: 1 !important">
                          <?=$paket_aanwijzing->getField("KETERANGAN")?> 
                      </td> 
                      <td class="td font10"> 
                          <small><i class="fa fa-clock-o"></i> <?=$tglupload[0] ?></small>
                      </td>
                  </tr>
                  <?php
                  // if (count($paket_aanwijzing_parent->nextRow())>=1) {
                    $paket_aanwijzing_parent = new PaketAanwijzing();
                      $paket_aanwijzing_parent->selectByParams(array("PAKET_ID" => $reqId, "PARENT_ID" => $paket_aanwijzing->getField("PAKET_AANWIJZING_ID") ));
                    while($paket_aanwijzing_parent->nextRow())
                      {
                      $tglupload_parent = explode('.', $paket_aanwijzing_parent->getField("TANGGAL_UPLOAD"));
                  ?>
                    <tr >
                      <td class="tdno"><i class="fa fa-arrow-right" aria-hidden="true"></i> 
                        <?php 
                      if ($reqMetodeLelangId == '2') {
                       } else {
                        echo "PANITIA PENGADAAN BARANG DAN JASA :";
                       } ?> 
                        <small class="font10">Jawab<i><b> <?=$paket_aanwijzing->getField("KODE_CUT")?> </b></i></small>
                      </td>
                      <td class="td">
                          <?=$paket_aanwijzing_parent->getField("KETERANGAN")?>
                      </td> 
                      <td class="td font10"> 
                        <?=$tglupload_parent[0] ?>
                      </td>
                  </tr>
                  <?php }  
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
