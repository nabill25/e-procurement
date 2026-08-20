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
$this->load->model("Users");

$rekanan = new Rekanan();
$user_login = new Users();
$reqId = $reqParse1;

$rekanan->selectByParamsSimple(array("A.KODE" => $reqId));
$rekanan->firstRow();

$user_login->selectByParams(array("A.REKANAN_ID" => $rekanan->getField("REKANAN_ID")));
$user_login->firstRow();
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

	              <h1 style="font-size: 20px; line-height: 18px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 600; text-decoration: none; color: #000000; margin-bottom: 10%;">Konfirmasi Verifikasi</h1> 

                  <!-- <p style="color:#000000;">Persyaratan pendaftaran memiliki pengalaman pekerjaan sejenis dalam 7 (tujuh) tahun terakhir dengan melengkapi data pengalaman pekerjaan pada aplikasi e-procurement.</p> --> 

	              <p style="text-align: left; font-size: 12px; line-height: 18px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 200; text-decoration: none; ">
	                Perusahaan dengan nama<strong>&nbsp;<?=$rekanan->getField("NAMA")?></strong>&nbsp;telah berhasil di verifikasi pada aplikasi&nbsp;<strong>e-Procurement - <?= SYSTEM_NAME_PT ?></strong><br />
     			Adapun informasi login Anda adalah sebagai berikut :
	              </p>   

	              <p>
	                <table style="text-align: left; font-size: 12px; line-height: 18px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 200; text-decoration: none; " width="100%">

	                	<tr>
		                      <td style="padding:4px 8px;  border:none; width:180px;">Username</td>
		                      <td style="padding:4px 8px; border:none; width:10px;">:</td>
		                      <td style="padding:4px 8px;"><?=$user_login->getField("USER_LOGIN")?></td>
	                  	</tr>                           
	                </table>
	              </p>           

	              <p style="text-align: left; font-size: 12px; line-height: 18px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 200; text-decoration: none; ">
	               Terimakasih</p>

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
	                <b>Vendor Management System</b><br>
			<?= SYSTEM_NAME_PT ?><br>
			<?= SYSTEM_ALAMAT_PT ?><br>
			<?= SYSTEM_TLP ?><br> 
			<?= SYSTEM_EMAIL_VMS ?><br> 
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