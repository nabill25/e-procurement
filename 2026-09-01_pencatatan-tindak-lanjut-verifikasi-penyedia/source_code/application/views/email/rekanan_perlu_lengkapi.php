<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 * Email ke PENYEDIA: verifikator minta kelengkapan berkas.
 * Dipanggil lewat: main/loadUrl/email/rekanan_perlu_lengkapi/<REKANAN_ID>
 * Gaya HTML disamakan dengan application/views/email/dokumen_expired.php.
 */

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->model('Rekanan');
$this->load->model('Rekanantindaklanjut');

$reqId = $reqParse1;

$rekanan = new Rekanan();
$rekanan->selectByParamsSimple(array("A.REKANAN_ID" => $reqId));
$rekanan->firstRow();

$tindakLanjut = new Rekanantindaklanjut();
$tindakLanjut->selectTerakhirByRekananId($reqId);
$catatan = $tindakLanjut->firstRow() ? $tindakLanjut->getField("CATATAN") : '';
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="x-apple-disable-message-reformatting">
  <style type="text/css">
    a,a[href],a:hover,a:link,a:visited {text-decoration:none!important;color:#0000EE;}
    p,p:visited {font-size:12px;line-height:24px;font-family:'Helvetica',Arial,sans-serif;font-weight:300;color:#000;}
  </style>
</head>

<body style="margin:0;padding:10px 0;-webkit-text-size-adjust:100%;background-color:#f2f4f6;color:#000" align="center">
  <div>

    <table align="center" style="text-align:center;vertical-align:top;width:600px;max-width:600px;background-color:#fff;" width="600">
      <tbody><tr><td style="width:596px;padding:22px 0;" width="596">
        <img src="<?= SYSTEM_LOGO_URL ?>" style="margin:0 auto;height:60px;" />
      </td></tr></tbody>
    </table>

    <table align="center" style="vertical-align:top;width:600px;max-width:600px;background-color:#fff;" width="600">
      <tbody><tr><td style="width:596px;padding:30px 30px 40px;" width="596">

        <p style="text-align:left;font-size:12px;line-height:18px;font-family:'Helvetica',Arial,sans-serif;font-weight:200;">
          <b>Rekanan yang terhormat,</b><br>
          Verifikator kami menemukan dokumen perusahaan Anda masih perlu dilengkapi pada sistem
          <a href="<?= SYSTEM_NAME_URL ?>">e-Procurement - <?= SYSTEM_NAME_PT ?></a>.
        </p>

        <table style="text-align:left;font-size:12px;line-height:18px;font-family:'Helvetica',Arial,sans-serif;font-weight:200;">
          <tr>
            <td bgcolor="#FFF" style="padding:4px 8px;border:none;width:180px;">Nama Perusahaan</td>
            <td bgcolor="#FFF" style="padding:4px 8px;border:none;width:10px;">:</td>
            <td bgcolor="#FFF" style="padding:4px 8px;border:none;"><?= htmlspecialchars($rekanan->getField("NAMA")) ?></td>
          </tr>
          <tr>
            <td bgcolor="#FFF" style="padding:4px 8px;border:none;">No. Registrasi</td>
            <td bgcolor="#FFF" style="padding:4px 8px;border:none;">:</td>
            <td bgcolor="#FFF" style="padding:4px 8px;border:none;"><?= htmlspecialchars($rekanan->getField("KODE")) ?></td>
          </tr>
          <tr>
            <td bgcolor="#FFF" style="padding:4px 8px;border:none;vertical-align:top;">Catatan Verifikator</td>
            <td bgcolor="#FFF" style="padding:4px 8px;border:none;vertical-align:top;">:</td>
            <td bgcolor="#FFF" style="padding:4px 8px;border:none;"><i><?= nl2br(htmlspecialchars($catatan)) ?></i></td>
          </tr>
        </table>

        <p style="text-align:left;font-size:12px;line-height:18px;font-family:'Helvetica',Arial,sans-serif;font-weight:200;">
          Silakan login ke <a href="<?= SYSTEM_NAME_URL ?>">e-Procurement - <?= SYSTEM_NAME_PT ?></a>,
          lengkapi/perbaiki dokumen sesuai catatan di atas, lalu tekan tombol
          <b>&ldquo;Sudah Saya Lengkapi&rdquo;</b> pada halaman Konfirmasi Pendaftaran.
        </p>

      </td></tr></tbody>
    </table>

    <table align="center" style="text-align:center;vertical-align:top;width:600px;max-width:600px;background-color:#000;" width="600">
      <tbody><tr><td style="width:596px;padding:30px;" width="596">
        <p style="font-size:11px;line-height:18px;font-family:'Helvetica',Arial,sans-serif;font-weight:200;color:#fff;">
          Administrator e-Procurement<br /><?= SYSTEM_NAME_PT ?>
        </p>
      </td></tr></tbody>
    </table>

    <table align="center" style="text-align:center;vertical-align:top;width:600px;max-width:600px;" width="600">
      <tbody><tr><td style="width:596px;padding:15px 30px;" width="596">
        <p style="font-size:11px;line-height:12px;font-family:'Helvetica',Arial,sans-serif;margin-top:30px;">
          <?= LABEL_COPY_RIGHT_YEAR ?> <a href="<?=base_url();?>" target="_blank" style="text-decoration:none;color:#828282;"><span style="color:#828282;">eProcurement <?= SYSTEM_NAME_PT ?></span></a>. All&nbsp;rights&nbsp;reserved.
        </p>
      </td></tr></tbody>
    </table>

  </div>
</body>
</html>
