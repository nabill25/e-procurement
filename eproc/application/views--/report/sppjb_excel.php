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
include_once("functions/date.func.php");
include_once("lib/phpqrcode/qrlib.php");
$this->load->model("Sppjb");
$this->load->model("Paketpemenang");

$sppjb = new Sppjb();
$getpaket_pemenang = new Paketpemenang();

$setId = $this->input->get("reqId");
$reqId = explode('-', $setId);
$pemenang = $this->input->get("pemenang");
$getpaket_pemenang->selectByParams(array("A.PAKET_PEMENANG_ID" => $reqId[1]), -1, -1);
$getpaket_pemenang->firstRow();
$reqRekananName = $getpaket_pemenang->getField("NAMA");
$sppjb->selectByParamsMonitoring(array("A.PAKET_ID" => $reqId[0], "PAKET_PEMENANG_ID" => $reqId[1]));
$sppjb->firstRow();

?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<base href="<?=base_url()?>" />
	</head>
	<body style="font-family:'Arial Narrow';">
    	<div class="kop-laporan">
            <div class="info">
                <br>
            </div>
        </div><!--class="tabel-pernyataan"-->
        <div class="data-laporan">
        <table border="1" class="tabel-pernyataan" >
            <tbody>
               <tr>
               	 	<td width="64">Nomor</td>
                    <td width="12">:</td>
                    <td colspan="3" width="192"><?=$sppjb->getField("KODE")?></td>
                    <td width="19"></td>
                    <td colspan="2" width="128" align="right"><?=$sppjb->getField("KOTA_DIRUT")?>, <?=getFormattedDate(date("Y-m-d"))?></td>
                  </tr>
                  <tr>
               	 	<td width="64">Lampiran</td>
                    <td width="12">:</td>
                    <td colspan="3" width="192">-</td>
                    <td width="19"></td>
                    <td colspan="2" width="128"></td>
                  </tr>
              <tr>
   	 	<td width="64" valign="top">Perihal</td>
                    <td width="12" valign="top">:</td>
                    <td colspan="3" width="192" valign="top">Penetapan Penyedia <?=ucwords(strtolower($sppjb->getField("NAMA_PAKET")))?></td>
                    <td width="19"></td>
                    <td colspan="2" width="128"></td>
                  </tr>
        </tbody>        
        </table>
       </div>
        <div class="data-laporan"><!--class="tabel-pernyataan"-->
        <table width="318" border="1" class="tabel-pernyataan" >
            <tbody>
               <tr>
               <td width="101">Yth.<br><b>Direktur <?=$getpaket_pemenang->getField("NAMA")?><br>
                <?=$sppjb->getField("NAMA_DIRUT")?></b><br><?=$sppjb->getField("ALAMAT_DIRUT")?><br><?=$sppjb->getField("KOTA_DIRUT")?></td>
                <td width="201"></td>
              </tr>
        </tbody>        
        </table>
       </div>
       <br>
        <div class="data-laporan"><!--class="tabel-pernyataan"-->
        <table border="1" class="tabel-pernyataan">
            <tbody>
               <tr>
                    <td align="right" width="12" valign="top">1.</td>
                    <td colspan="5" align="justify">Berdasarkan hasil    proses Pengadaan oleh Divisi Pengadaan Barang dan Jasa, bahwa perusahaan    Saudara telah ditunjuk sebagai Pemenang Penyedia <?=ucwords(strtolower($sppjb->getField("NAMA_PAKET")))?>.</td>
              </tr>
                  <tr>
                    <td align="right" valign="top">2.</td>
                    <td colspan="5" align="justify">Selanjutnya, kami    minta kepada Saudara untuk menyerahkan Jaminan Pelaksanaan yang Saudara    tujukan kepada <?= SYSTEM_NAME_PT ?>, paling lambat 14 (empat belas) hari kalender setelah    penandatanganan kontrak, dengan ketentuan sebagai berikut: </td>
                  </tr>
                  <tr>
                    <td></td>
                    <td valign="top">a.  Nilai Pekerjaan</td>
                    <td width="12" valign="top">:</td>
                    <td width="262" colspan="3" valign="top"><b>Rp. <?=numberToIna($sppjb->getField("NILAI_PEKERJAAN"))?></b>
                    										<br><?php if($sppjb->getField("PPN")=='1'){
                    											?>
                                                                (sudah termasuk PPN 10%)
                                                                <?php
																}
																else
																{
                                                                ?>
                                                                (belum termasuk PPN 10%)<?php } ?></td>
                  </tr>
                  <tr>
                    <td></td>
                    <td width="305" valign="top">b. Nilai Jaminan Pelaksanaan</td>
                    <td valign="top">:</td>
                    <td colspan="3" valign="top"><?=$sppjb->getField("PERSEN_JAMINAN")?>% &nbsp;&nbsp;dari nilai pekerjaan</td>
                  </tr>
                  <tr>
                    <td></td>
                    <td width="305" valign="top">c. Jangka Waktu Jaminan Pelaksanaan</td>
                    <td valign="top">:</td>
                    <td align="justify" valign="top" colspan="3"><b><?=$sppjb->getField("JANGKA_WAKTU_JAMINAN")?>&nbsp;(<?=kekata($sppjb->getField("JANGKA_WAKTU_JAMINAN"))?>&nbsp;)</b> hari kalender terhitung mulai <?=getFormattedDate($sppjb->getField("TMT_JAMINAN"))?></td>
                  </tr>
                  <tr>
                    <td></td>
                    <td width="305" valign="top">d. Jangka Waktu Pekerjaan</td>
                    <td valign="top">:</td>
                    <td align="justify" colspan="3" valign="top"><b><?=$sppjb->getField("JANGKA_WAKTU")?>&nbsp;(<?=kekata($sppjb->getField("JANGKA_WAKTU"))?>&nbsp;)</b> hari kalender</td>
                  </tr>
                  <tr>
                    <td align="right" valign="top">3.</td>
                    <td colspan="5" align="justify"> Lebih jauh, dalam hal    terjadi kegagalan Saudara untuk menerima penunjukan ini yang disusun    berdasarkan evaluasi terhadap penawaran Anda, maka akan dikenakan sanksi sesuai ketentuan dalam Peraturan  Direksi Perusahaan Umum (Perum) <?= SYSTEM_NAME_PT ?> Nomor: 02/DS000/01/2017    Tahun 2017 tentang Pedoman   Barang dan Jasa.</td>
                  </tr>
                  <tr>
                    <td align="right" valign="top">4.</td>
                    <td colspan="5" align="justify">Demikian kami sampaikan, atas    perhatian dan kerjasama Saudara, kami menyampaikan terima kasih.</td>
                  </tr>
                  <tr>
                  	<td></td>
                    <td></td>
                    <td width="12"></td>
                    <td width="262" colspan="3" align="center">Hormat Kami,</td>
                  </tr>
                  <tr>
                  	<td></td>
                    <td></td>
                    <td width="12"></td>
                    <td width="262" colspan="3" align="center"></td>
                  </tr>
                  <tr>
                  	<td></td>
                    <td></td>
                    <td width="12"></td>
                    <td width="262" colspan="3" align="center"></td>
                  </tr>
                  <tr>
                  	<td></td>
                    <td></td>
                    <td width="12"></td>
                    <td width="262" colspan="3" align="center"></td>
                  </tr>
                  <tr>
                  	<td></td>
                    <td></td>
                    <td width="12"></td>
                    <td width="262" colspan="3" align="center"></td>
                  </tr>
                  <tr>
                  	<td></td>
                    <td></td>
                    <td width="12"></td>
                    <td width="262" colspan="3" align="center"><b><u>
                    <?=ucwords(strtolower($sppjb->getField("PENANDA_TANGAN")))?></u></b></td>
                  </tr>
                   <tr>
                  	<td></td>
                    <td></td>
                    <td width="12"></td>
                    <td width="262" colspan="3" align="center"><?=ucwords(strtolower($sppjb->getField("PENANDA_TANGAN_JABATAN")))?></td>
                  </tr>
                  
        </tbody>        
        </table>
       </div>
       
        <div class="area-ttd-bawah">
            <div class="tanggal-tempat">
                 <div class="tanggal"></div>
                 <div class="tempat"></div>
            </div>
            <div class="ttd">
          <?php /*?> <?
                $encrypt_text = "ddd";//$paket_pernyataan_minat->getField("KODE_QR");//"adada";//$arrKodeQr[$i];
                $filename = $PNG_TEMP_DIR.$encrypt_text.'.png';
                $errorCorrectionLevel = 'L';   
                $matrixPointSize = 3;
                QRcode::png($encrypt_text, $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
                //display generated file
            ?>
            <?php
                 echo '<img src="'.$PNG_TEMP_DIR.basename($filename).'" />'; 
            ?><?php */?>
            </div>
            <div class="nama">
               
            </div>
            <div class="jabatan">
              
            </div>
        </div>
        
        </div>
	</body>
</html>