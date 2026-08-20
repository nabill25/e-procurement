<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("PaketRekanan");
$this->load->model("Paket");
$this->load->model("PaketNegoisasi");
$this->load->model("RekananPaketPenawaran");
$this->load->model("Rekanan");
$this->load->model("Metode");
$this->load->model("Negoshoutbox");
$this->load->model("PaketNegosiasiValidasi");
$this->load->library("AES");
$aes = new AES();
include_once("lib/phpqrcode/qrlib.php");
$PNG_TEMP_DIR = 'uploads/';
$PNG_TEMP_DIR_LOGO = '';
$filenamelogo = $PNG_TEMP_DIR_LOGO.'logo.png';
$reqId = $this->input->get("reqId");

// Unit Kerja
$this->load->library("libbreadcrumb"); 
$unitkerjaid = $this->input->get("unitkerjaid");
// End Unit Kerja

$paketInfo->getPaket($reqId);
$reqNamaPaket = $paketInfo->nama;
$reqRekananIdPemenang = $paketInfo->rekanan_id_pemenang;
$reqPublishBANegosiasi = $paketInfo->publish_ba_negosiasi;
$reqMetodeLelangId    = $paketInfo->metode_lelang_id;


$paket_rekanan = new PaketRekanan();
$paket_nilai = new Paket();
$paket_nilai_estimate = new Paket();
$rekanan_paket_penawaran = new RekananPaketPenawaran();
$rekanan_paket_penawaran_lampiran = new RekananPaketPenawaran();
$paket_negosiasi_validasi = new PaketNegosiasiValidasi();

$reqId = httpFilterRequest("reqId");
$reqNilaiEstimate = httpFilterPost("reqNilaiEstimate");
$reqDataPenawaranHarga = $_POST["reqDataPenawaranHarga"];
$reqRekananIdArray =unserialize(stripslashes($_POST['reqRekananIdArray']));
$submitSimpan = httpFilterPost("submitSimpan");

$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId, "A.REKANAN_ID" => $reqRekananIdPemenang), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 ");
$paket_rekanan->firstRow();
$i = $indexRekananPememenang =  0;
$arrRekananId[$i] = $paket_rekanan->getField("REKANAN_ID");
$arrRekanan[$i] = $paket_rekanan->getField("REKANAN");
$arrPaketRekananId[$i] = $paket_rekanan->getField("PAKET_REKANAN_ID");
$arrPaketRekananNilai[$i] = $paket_rekanan->getField("NILAI_PENAWARAN");
$arrDiemailNegosiasi[$i] = $paket_rekanan->getField("DI_EMAIL_NEGOSIASI");


$paket_nilai->selectByParams(array("PAKET_ID" => $reqId));
$paket_nilai->firstRow();
$reqNilaiEstimate = $paket_nilai->getField("NILAI_OWNER_ESTIMATE");
$reqJenisPengadaan = $paket_nilai->getField("JENIS_PENGADAAN");
if($reqJenisPengadaan == "LELANG")
    $display_pembelian = " style='display:none' ";
    
$submitNegosiasi = true;


$metode = new Metode();
$metode->selectByParams(array("UPPER(A.NAMA)|| LIKE " => "'%NEGOSIASI'"), -1, -1, $reqId);
$metode->firstRow();

$time = strtotime($metode->getField("TANGGAL_AWAL"));
$aanwijzing_hari = date('w', $time);
$aanwijzing_tanggal = (int)date('d', $time);
$aanwijzing_bulan = (int)date('m', $time);
$aanwijzing_tahun = (int)date('Y', $time);
$aanwijzing_dmy = date('d-m-Y', $time);
$aanwijzing_ymd = date('Y-m-d', $time);

$nomor = $paketInfo->pr_group_number."/BA.NEGOSIASI/".getYear($paketInfo->tanggal);

$rekanan_paket_penawaran->selectByParamsEvaluasiRekanan($arrPaketRekananId, array("A.PAKET_ID" => $reqId));
$rekanan_paket_penawaran_lampiran->selectByParamsEvaluasiRekanan($arrPaketRekananId, array("A.PAKET_ID" => $reqId));


$paket_negosiasi_validasi->selectByParamsValidasi(array("A.PAKET_ID" => $reqId));

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
            HASIL NEGOSIASI 
         </div><br>
        <!-- <div class="nomor">Nomor :  <?=$nomor?></div><br> -->
        
        <div class="pekerjaan">
        PEKERJAAN <?php if($paketInfo->bahasa == "EN") echo "/ <em>WORKS</em>"; ?><br />
        <?=strtoupper($paketInfo->nama)?>
        </div><br>
        
        <div class="isi">
            <div>
                Pada hari ini, <?=strtoupper(getHari($aanwijzing_hari));?> tanggal <?=strtoupper(getTerbilang($aanwijzing_tanggal));?> bulan <?=strtoupper(getNameMonth($aanwijzing_bulan));?> tahun <?=strtoupper(getTerbilang($aanwijzing_tahun));?> (<?=$aanwijzing_dmy?>), mulai pukul <?=(($metode->getField("JAM_AWAL") == "") ? '00:00' : $metode->getField("JAM_AWAL"))?> WIB sampai dengan selesai telah diadakan negosiasi pekerjaan dimaksud di atas.
            </div>
            <div style="height:7px;"></div>
            <div>
                Negosiasi dilaksanakan secara online melalui website <?= SYSTEM_NAME_URL ?> dengan risalah penjelasan terlampir.
            </div>
            <div style="height:7px;"></div>
            <div>
                Hasil ini mengikat dan merupakan bagian yang tidak terpisahkan dari Dokumen Pengadaan.
            </div>
            <div style="height:7px;"></div>
            <div>
                Berikut hasil negosiasi : 
            </div>
        </div>

        <?php
        if($paketInfo->bahasa == "EN")
        {
        ?>
        <div class="isi" style="margin-top:20px; font-style:italic">
            <div>
                 On this date, <?=(getHariEn($aanwijzing_hari));?>, <?=getDay($aanwijzing_ymd);?> <?=getNameMonthEn((int)getMonth($aanwijzing_ymd));?> <?=getYear($aanwijzing_ymd);?>,  from <?=(($metode->getField("JAM_AWAL") == "") ? '00:00' : $metode->getField("JAM_AWAL"))?> <?php if($metode->getField("JAM_AWAL") <= "12:00") echo "am"; else echo "pm"; ?> to the completed meeting, it has been held the Negotiation for the work referred to above.
            </div>
            <div style="height:7px;"></div>
            <div>
                Negotiation has been conducted in online through <?= SYSTEM_NAME_URL ?> with the following explanations attached.
            </div>
            <div style="height:7px;"></div>
            <div>
                The Minutes of Negotiation is binding and as a part of the tender document.
            </div>
            <div style="height:7px;"></div>
            <div>
                The result of negotiation as follow : 
            </div>
        </div>        
        <?php
        }
        ?>        
        
        <div class="area-dokumen">

                  <table class="table">
                     <tr class="tr-bc">
                      <td rowspan="3" align="center" class="td">No. <?php if($paketInfo->bahasa == "EN") echo "/ <em>Number</em>"; ?></td>
                      <td rowspan="3" align="center" class="td">Uraian <?php if($paketInfo->bahasa == "EN") echo "/ <em>Description</em>"; ?></td>
                      <td rowspan="3" align="center" class="td">Quantity</td>
                      <td rowspan="3" align="center" class="td">Satuan <?php if($paketInfo->bahasa == "EN") echo "/ <em>Unit</em>"; ?></td>
                      <td colspan="2" align="center" class="td">Hasil Negosiasi <?php if($paketInfo->bahasa == "EN") echo "/ <em>Result of Negotiation</em>"; ?></td>
                    </tr>       
                     <tr class="sub-judul1">
                        <td align="center" class="td" colspan="2">Unit Price</td>
                        <!-- <td align="center" class="td" rowspan="2">Total Sesudah Negosiasi  <?php // if($paketInfo->bahasa == "EN") echo "/ <em>Total Amount of Post-negotiation</em>"; ?></td> -->
                    </tr>
                     <tr class="sub-judul1">
                        <td align="center" class="td">Sebelum <?php if($paketInfo->bahasa == "EN") echo "/ <em>Pre-negotiation</em>"; ?></td>
                        <td align="center" class="td">Sesudah <?php if($paketInfo->bahasa == "EN") echo "/ <em>Post-negotiation</em>"; ?></td>
                    </tr>
                    <?php
                    $style="gelap";
                    $totalNegosiasi = 0;
                    while($rekanan_paket_penawaran->nextRow())
                    {
                    
                        if($rekanan_paket_penawaran->getField("QUANTITY") == 0)
                        {   
                            $no = 0;
                        ?>
                        <tr class="<?=$style?>">
                                <td colspan="7" class="td"><?=$rekanan_paket_penawaran->getField("ITEM")?></td>
                        </tr>
                        <?php
                        }
                        else
                        {
                        ?>                     
                            <tr class="<?=$style?>">
                                <td class="tdno"><?=($no+1)?></td>
                                <td class="td"><?=$rekanan_paket_penawaran->getField("ITEM")?></td>
                                <td class="td">
                                    <?=$rekanan_paket_penawaran->getField("QUANTITY")?>
                                    <input type="hidden" name="reqQuantity[]" id="reqQuantity<?=$rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID")?>" value="<?=$rekanan_paket_penawaran->getField("QUANTITY")?>">
                                    <input type="hidden" name="reqPaketPenawaranId[]" id="reqPaketPenawaranId<?=$rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID")?>" value="<?=$rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID")?>">
                                    
                                 </td>
                                <td class="td" <?=$displayElement?>><?=$rekanan_paket_penawaran->getField("SATUAN")?></td>
                                <?php
                            
                                $paket_negosiasi = new PaketNegoisasi();                                                        
                                $paket_negosiasi->selectByParams(array("A.PAKET_PENAWARAN_ID" => $rekanan_paket_penawaran->getField("PAKET_PENAWARAN_ID")));    
                                $paket_negosiasi->firstRow();       
                                $penawaranAwal =  $paket_negosiasi->getField("UNIT_PRICE_AWAL");
                                $penawaranNegosiasi =  $paket_negosiasi->getField("UNIT_PRICE");                                                    
                                $jumlahNegosiasi =  $paket_negosiasi->getField("TOTAL");
                                    
                                ?>            
                                <!-- <td class="td" align="right" <?php //$displayElement?>><?php //numberToIna($rekanan_paket_penawaran->getField("UP_".$arrPaketRekananId[0]))?></td>    -->
                                <td class="td" align="right" <?=$displayElement?>><?=numberToIna($penawaranAwal)?></td>   
                                <td class="td" align="right" <?=$displayElement?>><?=numberToIna($jumlahNegosiasi)?></td>   
                                <!-- <td class="td" align="right" <?php // $displayElement?>><?php //numberToIna($jumlahNegosiasi)?></td>            -->
                            </tr>                    
                        <?php
                            $totalTerkecil += $jumlahTerkecil;
                            $totalNegosiasi += $jumlahNegosiasi;
                            unset($arrPenawaran);
                            unset($paket_negosiasi);
                            $no++;
                            if($style == "gelap")
                                $style = "terang";
                            else
                                $style = "gelap";                       
                        }
                    }
                    ?>    
                    <tr class="judul-kolom">
                        <td colspan="5"></td>                   
                        <td class="td" align="right">
                          <?php echo numberToIna($totalNegosiasi)?>
                        </td>          
                    </tr>  
                  </table>
            <p></p>

            <?php 
  if ($reqMetodeLelangId == '2') {
   } else {
    echo "PELAKSANA PENGADAAN BARANG DAN JASA :";
   } ?><?php if($paketInfo->bahasa == "EN") echo "/ <em>PROCUREMENT COMMITTEE</em>"; ?>
                <table class="table">
                <tr class="tr-bc">
                    <td class="td" style="width:20px">NO</td>
                    <td class="td" class="td">NAMA <?php if($paketInfo->bahasa == "EN") echo "/ <em>NAME</em>"; ?></td>
                    <!-- <td style="width:20%">APPROVAL QR CODE</td> -->
                </tr>
                <?php
              $i = 1;
                while($paket_negosiasi_validasi->nextRow())
              {
              ?>
                    <tr>
                        <td class="td"><?=$i?></td>
                        <td class="td">
                            <?=$paket_negosiasi_validasi->getField("NAMA")?>
                        </td>
                        <!-- <td align="center"> -->
                            <?php
                               /*$encrypt_text = "QR CODE VALID UNTUK DOKUMEN NOMOR ".$nomor." A.N ".$paket_negosiasi_validasi->getField("NAMA");//$paket_pembukaan_validasi->getField("NIP");
                                $filename = $PNG_TEMP_DIR.$reqId.$paket_negosiasi_validasi->getField("KODE").'.png';
                      
                                //$encrypt_text = $paket_negosiasi_validasi->getField("NIP");
                                //$filename = $PNG_TEMP_DIR.$encrypt_text.'.png';
                                $errorCorrectionLevel = 'L';   
                                $matrixPointSize = 2;
                                QRcode::png($encrypt_text, $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
                                //display generated file
                                echo '<img src="'.$PNG_TEMP_DIR.basename($filename).'" />'; */
                            ?>        
                        <!-- </td> -->
                    </tr>
              <?php
                $i++;
              }
              ?>       
            </table>
            <pagebreak>
            <p><b>LAMPIRAN <?php if($paketInfo->bahasa == "EN") echo "/ <em>ATTACHMENTS</em>"; ?> NEGOSIASI :</b></p>
            <table class="table">
                <tr class="tr-bc">
                  <td class="td" width="100%" style="text-align:center"><strong>RINCIAN <?php if($paketInfo->bahasa == "EN") echo "/ <em>DETAILS</em>"; ?></strong></td>
                </tr>
                <?php
                while($rekanan_paket_penawaran_lampiran->nextRow())
                {
                    ?>
                    <tr class="tr">
                        <td class="td" style="text-align: center" colspan="100"><strong><?=strtoupper($paketInfo->nama)?></strong></td>
                    </tr>
                    <tr>
                      <td valign="top">
                      <table class="table">
                      <tbody>
                      <?php
                      $php_shoutbox = new NegoShoutbox();
                      $php_shoutbox->selectByParams(array("PAKET_PENAWARAN_ID" => $rekanan_paket_penawaran_lampiran->getField("PAKET_PENAWARAN_ID"), "REKANAN_ID" => $reqRekananIdPemenang));
                      $pesan = "";
                      while($php_shoutbox->nextRow())
                      {
                      ?>
                        <tr>
                            <!-- <th width="20%" class="td"></th> -->
                            <th width="35%" align="left" class="td">
                                            <?php
                                
                                                if($php_shoutbox->getField("NAMA") == "Panitia"){
                                if ($reqMetodeLelangId == '2') {
                                  echo "Pengadaan Barang/Jasa";
                                 } else {
                                  echo "PELAKSANA PENGADAAN BARANG DAN JASA :";
                                 } 
                              }
                                else{
                                                    echo $php_shoutbox->getField("NAMA");
                              }
                              ?>
                                <span class="font10"><br>(<?=$php_shoutbox->getField("WAKTU")?>)</span>
                            </th>
                            <!-- <th width="1%" align="left">:</th> -->
                            <th align="left" class="td"><?=$php_shoutbox->getField("PESAN")?></th>
                        </tr>
                            <?php
                              $pesan = "ada";
                            }
                            if($pesan == "")
                            {
                            ?>
                        <tr>
                            <th colspan="4">--- TIDAK ADA PESAN <?php if($paketInfo->bahasa == "EN") echo "/ <em>NO MESSAGE</em>"; ?> ---</th>
                        </tr>          
                      <?php
                      }
                      ?>
                      </tbody>
                      </table>
                      </td>
                    </tr>                              
                    <?php
                }
                ?>  
            </table>
                    
        </div>

<p></p>
 
<div class="nomor-oe">
  <div class="data" style="font-size:10px; font-style:italic">
       <?= $this->libbreadcrumb->cetakcopyright($unitkerjaid) ?>
       <?php if($paketInfo->bahasa == "EN") echo "<br><br><em>".SYSTEM_SAH_EN."</em>"; ?>
  </div>
</div>
</body>
</html>
