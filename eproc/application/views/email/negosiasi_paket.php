<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model('Rekanan');
$this->load->model("PaketRekanan");
$this->load->model("PaketRekananDaftar");
$this->load->model("Metode");

$rekanan = new Rekanan();
$paket_rekanan = new PaketRekanan();
$reqId = $reqParse1;
$reqPaketRekananId = $reqParse2;

$paket_rekanan->selectByParamsEmail(array("PAKET_ID" => $reqId, 'PAKET_REKANAN_ID' => $reqPaketRekananId));
$paket_rekanan->firstRow();

$paketInfo->getPaket($reqId);


$metode = new Metode();
// $metode->selectByParams(array("UPPER(A.NAMA)" => "KLARIFIKASI TEKNIS & NEGOSIASI"), -1, -1, $reqId);
$metode->selectByParams(array("UPPER(A.NAMA)" => "PEMBUKTIAN & NEGOSIASI"), -1, -1, $reqId);
$metode->firstRow();

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head> 
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="x-apple-disable-message-reformatting">
  <!--[if !mso]><!--><meta http-equiv="X-UA-Compatible" content="IE=edge"><!--<![endif]-->
    <!-- Template on https://codepen.io/fullsphere-adam/pen/eYKVOyz -->
    <!-- Start stylesheet -->
    <style type="text/css">
      a,a[href],a:hover, a:link, a:visited {text-decoration: none!important;color: #0000EE;}.link {text-decoration: underline!important;}p, p:visited {font-size:12px;line-height:24px;font-family:'Helvetica', Arial, sans-serif;font-weight:300;text-decoration:none;color: #000000;}h1 {font-size:22px;line-height:24px;font-family:'Helvetica', Arial, sans-serif;font-weight:normal;text-decoration:none;color: #000000;}.ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td {line-height: 100%;}.ExternalClass {width: 100%;}
    </style>
    <!-- End stylesheet -->
</head>

  <body style="margin: 0; padding-top: 10px; padding-bottom: 10px; padding-left: 0; padding-right: 0; -webkit-text-size-adjust: 100%;background-color: #f2f4f6; color: #000000" align="center">

  	<div> 
	    
	    <!-- Start container for logo -->
	    <table align="center" style="text-align: center; vertical-align: top; width: 600px; max-width: 600px; background-color: #ffffff;" width="600">
	      <tbody>
	        <tr>
	          <td style="width: 596px; vertical-align: top; padding-left: 0; padding-right: 0; padding-top: 22px; padding-bottom: 22px;" width="596">
	            <img src="<?= SYSTEM_LOGO_URL ?>" style="margin:0 auto; height:60px;" />
	          </td>
	        </tr>
	      </tbody>
	    </table>
	    <!-- End container for logo -->


	    <!-- Start single column section -->
	    <table align="center" style="vertical-align: top; width: 600px; max-width: 600px; background-color: #ffffff;" width="600">
	        <tbody>
	          <tr>
	            <td style="width: 596px; vertical-align: top; padding-left: 30px; padding-right: 30px; padding-top: 30px; padding-bottom: 40px;" width="596">

	              <h1 style="font-size: 20px; line-height: 18px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 600; text-decoration: none; color: #000000; margin-bottom: 10%;">Undangan Pembuktian & Negosiasi</h1> 

                  <!-- <p style="color:#000000;">Persyaratan pendaftaran memiliki pengalaman pekerjaan sejenis dalam 7 (tujuh) tahun terakhir dengan melengkapi data pengalaman pekerjaan pada aplikasi e-procurement.</p> -->  

	              <p style="text-align: left; font-size: 12px; line-height: 18px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 200; text-decoration: none; ">
	                <!-- <b>Rekanan yang terhormat,</b> <br> -->
	                Yang Terhormat <?=$paket_rekanan->getField("NAMA")?> saudara telah di undang untuk mengikuti Pembuktian & Negosiasi "<?=$paketInfo->nama?> ", Jadwal pelaksanaan sebagai berikut :
	              </p>   

	              <p>
	              	<?php 
              	$time = strtotime($metode->getField("TANGGAL_AWAL"));
								$ini_hari = date('w', $time);
								$ini_tanggal = (int)date('d', $time);
								$ini_bulan = (int)date('m', $time);
								$ini_tahun = (int)date('Y', $time);
								$ini_dmy = date('d-m-Y', $time);
								$ini_ymd = date('Y-m-d', $time); 

								$ex = explode(' ',$metode->getField("TANGGAL_AWAL"));

								$time2 = strtotime($metode->getField("TANGGAL_AKHIR"));
								$ini_hari2 = date('w', $time2);
								$ini_tanggal2 = (int)date('d', $time2);
								$ini_bulan2 = (int)date('m', $time2);
								$ini_tahun2 = (int)date('Y', $time2);
								$ini_dmy2 = date('d-m-Y', $time2);
								$ini_ymd2 = date('Y-m-d', $time2); 

								$ex2 = explode(' ',$metode->getField("TANGGAL_AKHIR"));
								?>

	              <table style="text-align: left; font-size: 12px; line-height: 18px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 200; text-decoration: none; " width="100%">
                	  <tr>
									      <td style="padding:4px 8px; width:20%">Tanggal</td>
									      <td style="padding:4px 8px; width:1%">:</td>
									      <td style="padding:4px 8px;"><?= ucwords(getHari($ini_hari)).', '.getFormattedDateJson($ex[0]).' s/d '.ucwords(getHari($ini_hari2)).', '.getFormattedDateJson($ex2[0]) ?></td>
									  </tr>
									  <?php
									  if($metode->getField("JAM_AWAL") == "")
									  {}
									  else
									  {
									  ?>
									  <tr>
									      <td style="padding:4px 8px;">Jam</td>
									      <td style="padding:4px 8px; width:1%">:</td>
									      <td style="padding:4px 8px;"><?=$metode->getField("JAM_AWAL")?> sampai dengan selesai</td>
									  </tr>
									  <?php
									  }
									  ?>
									  <tr>
									      <td style="padding:4px 8px; width:20%">Lokasi</td>
									      <td style="padding:4px 8px; width:1%">:</td>
									      <td style="padding:4px 8px;"><?= LOKASI_KLARIFIKASI ?></td>
									  </tr>
                </table>
	              </p>           

	              <p style="text-align: left; font-size: 12px; line-height: 18px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 200; text-decoration: none; ">
	              	Berdasarkan Jadwal di atas penyedia barang jasa harap login melalui aplikasi <a href="<?=base_url()?>" target="_blank">e-Procurement - <?= SYSTEM_NAME_PT ?></a> pilih detil paket pekerjaan "<?=$paketInfo->nama?>" kemudian klik menu Pembuktian & Negosiasi. 
	               </p>

	               <p style="text-align: left; font-size: 12px; line-height: 18px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 200; text-decoration: none; ">
	               Jika ada kesulitan, silahkan hubungi Administrator kami melalui email <?= SYSTEM_EMAIL ?>. 
	               </p>

	              <!-- Start button (You can change the background colour by the hex code below) -->
	              <!-- <a href="#" target="_blank" style="background-color: #000000; font-size: 12px; line-height: 22px; font-family: 'Helvetica', Arial, sans-serif; font-weight: normal; text-decoration: none; padding: 12px 12px; color: #ffffff; border-radius: 5px; display: inline-block; mso-padding-alt: 0;"> 

	                  <span style="mso-text-raise: 15pt; color: #ffffff;">Learn more</span> 
	              </a> -->
	              <!-- End button here -->

	            </td>
	          </tr>
	        </tbody>
	      </table>
	      <!-- End single column section -->   

	      <!-- Start footer -->
	      <table align="center" style="text-align: center; vertical-align: top; width: 600px; max-width: 600px; background-color: #000000;" width="600">
	        <tbody>
	          <tr>
	            <td style="width: 596px; vertical-align: top; padding-left: 30px; padding-right: 30px; padding-top: 30px; padding-bottom: 30px;" width="596"> 
	              <p style="font-size: 11px; line-height: 18px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 200; text-decoration: none; color: #ffffff;">
	                System e-Procurement<br />
	                <?= SYSTEM_NAME_PT ?>
	              </p> 
	            </td>
	          </tr>
	        </tbody>
	      </table>
	      <!-- End footer -->
	    
	      <!-- Start unsubscribe section -->
	      <table align="center" style="text-align: center; vertical-align: top; width: 600px; max-width: 600px;" width="600">
	        <tbody>
	          <tr>
	            <td style="width: 596px; vertical-align: top; padding-left: 30px; padding-right: 30px; padding-top: 15px; padding-bottom: 15px;" width="596"> 
	              <p style="font-size: 11px; line-height: 12px; font-family: 'Helvetica', Arial, sans-serif; font-weight: normal; text-decoration: none;  margin-top: 30px;">
	                <?= LABEL_COPY_RIGHT_YEAR ?> <a href="<?=base_url();?>" target="_blank" style="text-decoration:none;color:#828282;"><span style="color:#828282;">eProcurement <?= SYSTEM_NAME_PT ?></span></a>. All&nbsp;rights&nbsp;reserved.
	              </p>
	            </td>
	          </tr>
	        </tbody>
	      </table>
	      <!-- End unsubscribe section -->
	  
  	</div>
  </body>

</html> 
                                                                                       