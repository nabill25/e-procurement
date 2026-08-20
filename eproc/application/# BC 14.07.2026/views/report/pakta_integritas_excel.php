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

$PNG_TEMP_DIR = 'uploads/';
/*header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Neraca_Saldo_Eksport.xls");*/

$this->load->model("PaketPanitia");
$this->load->model("Rekanan");
$this->load->model("PaketPihakLain");
$this->load->model("PaketPaktaIntegritas");

$paket_panitia = new PaketPanitia();
$paket_pihak_lain = new PaketPihakLain();
$rekanan = new Rekanan();
$paket_pakta_integritas = new PaketPaktaIntegritas();

$reqId = $this->input->get("reqId");
$reqRekananId = $this->input->get("reqRekananId");

$paketInfo->getPaket($reqId);

$i = 0;
$paket_panitia->selectByParamsPaktaIntegritas(array("A.PAKET_ID" => $reqId));
while($paket_panitia->nextRow())
{
	$arrNama[$i] = strtoupper($paket_panitia->getField("NAMA"));
	$arrJenis[$i] = "PANITIA";
	$arrKode[$i] = $paket_panitia->getField("NIP");
	$arrKodeQr[$i] = $paket_panitia->getField("KODE_QR");
	$i++;
}
$paket_pihak_lain->selectByParamsPaktaIntegritas(array("A.PAKET_ID" => $reqId));
while($paket_pihak_lain->nextRow())
{
	$arrNama[$i] = strtoupper($paket_pihak_lain->getField("USER_NAMA"));
	$arrJenis[$i] = "FUNGSIONAL";
	$arrKode[$i] = $paket_pihak_lain->getField("NIP");
	$arrKodeQr[$i] = $paket_pihak_lain->getField("KODE_QR");
	$i++;
}
?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<base href="<?=base_url()?>" />

	</head>
	<body class="body">
  <div class="logo"><img src="images/<?= SYSTEM_LOGO_CETAK ?>" height="75" /></div>
  <div class="judul">
    PAKTA INTEGRITAS
  </div><br>
        <div class="isi">
            <p>Saya yang bertandatangan dibawah ini, dalam rangka pengadaan/pekerjaan <?=$paketInfo->nama?>, dengan ini menyatakan bahwa saya :</p>
        </div>
				<ol>
				  <li>Tidak akan melakukan praktek KKN;</li>
				  <li>Akan melaporkan kepada pihak yang berwajib/berwenang apabila mengetahui ada indikasi KKN dalam proses pengadaan/pekerjaan ini;</li>
					<li>Dalam proses pengadaan/pekerjaan ini, berjanji akan melaksanakan tugas secara bersih, transparan, dan pekerjaan/kegiatan ini. <br>Profesional dalam arti akan mengerahkan segala kemampuan dan sumber daya secara optimal untuk memberikan hasil kerja terbaik mulai dari penyiapan penawaran, pelaksanaan, pelaksanaan, dan penyelesaian; </li>
					<li>Apabila saya melanggar hal-hal yang telah saya nyatakan dalam PAKTA INTEGRITAS ini, saya bersedia dikenakan sanksi moral, sanksi administrasi serta dituntut ganti rugi dan pidana sesuai dengan ketentuan peraturan perundang-undangan yang berlaku. </li>
				</ol>
        <div class="isi">
            <p></p>
        </div>
        <div class="data-laporan" style="text-align: center">
        <table class="teble" style="width: 100%">
          <tbody>
            <tr class="tr-bc">
              <td width="30%" align="center" class="td"><b style="font-size: 11px">Biro Pengadaan Barang dan Jasa</b></td>
              <td width="30%" align="center" class="td"><b style="font-size: 11px">Nama Jelas</b></td>
              <td width="30%" align="center" class="td"><b style="font-size: 11px">Tanda Tangan</b></td>
            </tr>
            <tr>
              <td width="30%" height="150px" align="center" class="td"> </td>
              <td width="30%" align="center" class="td"> </td>
              <td width="30%" align="center" class="td"> </td>
            </tr>
          </tbody>
        </table>

        </div>
	</body>
</html>
