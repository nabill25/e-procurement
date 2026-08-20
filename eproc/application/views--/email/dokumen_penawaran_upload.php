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
$this->load->model("PaketEvaluasiAdminTawar");
$this->load->model("PaketEvaluasiTeknisTawar");
$this->load->model("PaketEvaluasiHargaTawar");
$this->load->model("PaketRekanan");
$this->load->model("Metode");

$rekanan = new Rekanan();
$paket_rekanan = new PaketRekanan();
$paket_evaluasi_admin_tawar = new PaketEvaluasiAdminTawar();
$paket_evaluasi_teknis_tawar = new PaketEvaluasiTeknisTawar();
$paket_evaluasi_harga_tawar = new PaketEvaluasiHargaTawar();

$reqId = $reqParse1;
$reqRekananId = $reqParse2;
$reqToken = rand(10000, 99999);

$rekanan->selectByParamsSimple(array("A.REKANAN_ID" => $reqRekananId));
$rekanan->firstRow();

$paketInfo->getPaket($reqId);

$paket_evaluasi_admin_tawar->selectByParamsRekananDokumen($reqRekananId, array("A.PAKET_ID" => $reqId));
$paket_evaluasi_teknis_tawar->selectByParamsRekananDokumen($reqRekananId, array("A.PAKET_ID" => $reqId));
$paket_evaluasi_harga_tawar->selectByParamsRekananDokumen($reqRekananId, array("A.PAKET_ID" => $reqId));

/* UPDATE KE PAKET_REKANAN */
$paket_rekanan->setField("FIELD", "KIRIM_PENAWARAN_KODE");
$paket_rekanan->setField("FIELD_VALUE", $reqToken);
$paket_rekanan->setField("REKANAN_ID", $reqRekananId);
$paket_rekanan->setField("PAKET_ID", $reqId);
$paket_rekanan->updateByRekananPaket();

$metode = new Metode();
$metode->selectByParams(array("UPPER(A.NAMA)" => "UPLOAD PASSWORD DOKUMEN PENAWARAN"), -1, -1, $reqId);
$metode->firstRow();
?>  

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head> 
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="x-apple-disable-message-reformatting">
    <style type="text/css">
      a,a[href],a:hover, a:link, a:visited {text-decoration: none!important;color: #0000EE;}.link {text-decoration: underline!important;}p, p:visited {font-size:12px;line-height:24px;font-family:'Helvetica', Arial, sans-serif;font-weight:300;text-decoration:none;color: #000000;}h1 {font-size:22px;line-height:24px;font-family:'Helvetica', Arial, sans-serif;font-weight:normal;text-decoration:none;color: #000000;}.ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td {line-height: 100%;}.ExternalClass {width: 100%;}
    </style>
</head>

  <body style="margin: 0; padding-top: 10px; padding-bottom: 10px; padding-left: 0; padding-right: 0; -webkit-text-size-adjust: 100%;background-color: #f2f4f6; color: #000000;">

  	<div> 
	    
	    <!-- Start container for logo -->
	    <table align="center" style="text-align: center; vertical-align: top; width: 80%; max-width: 80%; background-color: #ffffff; border-radius: 20px 20px 0 0 ;" width="600">
	      <tbody>
	        <tr>
	          <td>
	            <img src="<?= SYSTEM_LOGO_URL ?>" style="margin:0 auto; height:60px;" />
	          </td>
	        </tr>
	        <tr>
	          <td>
	              <p style="text-align: left; font-size: 12px; line-height: 18px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 200; text-decoration: none; ">
	              	<h3>Konfirmasi Upload Dokumen Penawaran</h3>
	              </p>
	          </td>
	        </tr>
	      </tbody>
	    </table>


	    <!-- Start single column section -->
	    <table align="center" style="vertical-align: top; width: 80%; max-width: 80%; background-color: #ffffff; border-bottom: 5px solid #b7b7b7;" width="600">
	        <tbody>
	          <tr>
	            <td style="width: 596px; vertical-align: top; padding-left: 30px; padding-right: 30px; padding-top: 30px; padding-bottom: 40px;" width="596">

	              <p>
	                <b><?=$rekanan->getField("NAMA")?> yang terhormat,</b> <br>
	                Anda telah mengirim dokumen penawaran untuk paket pekerjaan "<?=$paketInfo->nama?> (<?=$paketInfo->pr_group_number?>)" dengan rincian sebagai berikut :
	              </p>   

	              <p>
	                <table style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:12px;margin-bottom:0;color:#5F5F5F; border-collapse:collapse; width:100%;"> 
                      <tr>
                        <td bgcolor="#219ebc" width="5%" align="center" style="color:#FFF; border:1px solid #000; padding:4px 8px; text-transform:uppercase;">No.</td>
                        <td bgcolor="#219ebc" width="60%" align="center" style="color:#FFF; border:1px solid #000; padding:4px 8px; text-transform:uppercase;">Nama Dokumen</td>
                        <td bgcolor="#219ebc" align="center" style="color:#FFF; border:1px solid #000; padding:4px 8px; text-transform:uppercase;">Nama File</td>
                        <td bgcolor="#219ebc" width="10%" align="center" style="color:#FFF; border:1px solid #000; padding:4px 8px; text-transform:uppercase;">Ukuran File</td>
                      </tr>
                      <tr>
                          <td bgcolor="#8ecae6" style="padding:4px 8px; border:1px solid #000; color: #000; text-transform:uppercase" colspan="4">Dokumen Administrasi</td>
                      </tr>
                        <?php
                        $i=1;
                        while($paket_evaluasi_admin_tawar->nextRow())
                        {
                        ?>                
                            <tr>
                              <td bgcolor="#FFF" style="padding:4px 8px; border:1px solid #000; text-align: center;"><?=$i?>.</td>
                              <td bgcolor="#FFF" style="padding:4px 8px; border:1px solid #000;"> <?=$paket_evaluasi_admin_tawar->getField("NAMA")?> </td>
                              <td bgcolor="#FFF" style="padding:4px 8px; border:1px solid #000;"> 
                              	<small><?=$paket_evaluasi_admin_tawar->getField("KETERANGAN")?> </small>
                              </td>
                              <td bgcolor="#FFF" style="padding:4px 8px; border:1px solid #000;" align="right"> 
                              	<small><?=round($paket_evaluasi_admin_tawar->getField("UKURAN") / 1024, 2)?> Kb </small>
                              </td>
                              
                            </tr>
                        <?php
							$i++;
                        }
                        ?>
                      <tr>
                          <td bgcolor="#8ecae6" style="padding:4px 8px; border:1px solid #000; color:#000; text-transform:uppercase" colspan="4">Dokumen Teknis</td>
                      </tr>
                        <?php
                        $i=1;
                        while($paket_evaluasi_teknis_tawar->nextRow())
                        {
                        ?>                
                            <tr>
                              <td bgcolor="#FFF" style="padding:4px 8px; border:1px solid #000; border:1px solid #000; text-align: center;"><?=$i?>.</td>
                              <td bgcolor="#FFF" style="padding:4px 8px; border:1px solid #000; border:1px solid #000;"> <?=$paket_evaluasi_teknis_tawar->getField("NAMA")?> </td>
                              <td bgcolor="#FFF" style="padding:4px 8px; border:1px solid #000; border:1px solid #000;"> 
                              	<small><?=$paket_evaluasi_teknis_tawar->getField("KETERANGAN")?></small> 
                              </td>
                              <td bgcolor="#FFF" style="padding:4px 8px; border:1px solid #000; border:1px solid #000;" align="right"> 
                              	<small><?=round($paket_evaluasi_teknis_tawar->getField("UKURAN") / 1024, 2)?> Kb </small>
                              </td>
                              
                            </tr>
                        <?php
                          $i++;
                        }
                        ?>  
                      <tr>
                          <td bgcolor="#8ecae6" style="padding:4px 8px; border:1px solid #000; color: #000; text-transform:uppercase" colspan="4">Dokumen Harga</td>
                      </tr>
                        <?php
                        $i=1;
                        while($paket_evaluasi_harga_tawar->nextRow())
                        {
                        ?>                
                            <tr>
                              <td bgcolor="#FFF" style="padding:4px 8px; border:1px solid #000; text-align: center;" ><?=$i?>.</td>
                              <td bgcolor="#FFF" style="padding:4px 8px; border:1px solid #000;" > <?=$paket_evaluasi_harga_tawar->getField("NAMA")?> </td>
                              <td bgcolor="#FFF" style="padding:4px 8px; border:1px solid #000;" > 
                              	<small><?=$paket_evaluasi_harga_tawar->getField("KETERANGAN")?> </small>
                              </td>
                              <td bgcolor="#FFF" style="padding:4px 8px; border:1px solid #000;"  align="right"> 
                              	<small><?=round($paket_evaluasi_harga_tawar->getField("UKURAN") / 1024, 2)?> Kb </small>
                              </td>
                              
                            </tr>
                        <?php
							$i++;
                        }
                        ?>                          
                        
                  </table>
	              </p>           

	              	<p style="text-align: center; font-size: 12px; line-height: 18px; font-family: 'Helvetica', Arial, sans-serif; font-weight: 200; text-decoration: none; ">
	               		<b>Silahkan masukkan kode verifikasi berikut untuk melanjutkan kirim dokumen penawaran.</b>
	           		</p> 

			      <table border="0" cellpadding="0" cellspacing="0" width="50" class="emailButton" style="background-color: #000000; border-collapse:collapse; width:20%; text-align:center; margin:0 auto; border-radius: 20px;">
		              <tr>
		                  <td align="center" valign="middle" class="buttonContent" style="padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;">
		                      <a style="color:#FFFFFF;text-decoration:none;font-family:Helvetica,Arial,sans-serif;font-size:16px;line-height:110%;"><?=$reqToken?></a>
		                  </td>
		              </tr>
		          </table>
	            </td>
	          </tr>
	        </tbody>
	      </table>
	      <!-- End single column section -->   

 
	    
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