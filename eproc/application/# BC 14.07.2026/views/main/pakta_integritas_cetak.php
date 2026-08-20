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
include_once("functions/default.func.php");
$this->load->model("PaketPanitia");
$this->load->model("Rekanan");
$this->load->model("PaketPihakLain");
$this->load->model("PaketPaktaIntegritas");


$paket_panitia = new PaketPanitia();
$paket_pihak_lain = new PaketPihakLain();
$rekanan = new Rekanan();
$paket_pakta_integritas = new PaketPaktaIntegritas();

$reqId = httpFilterGet("reqId");
$reqRekananId = httpFilterGet("reqRekananId");

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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html moznomarginboxes mozdisallowselectionprint>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?></title>
<base href="<?=base_url();?>" />
<style>
body{
	font-family:"Arial Narrow";
	font-size:15px;
}
.judul{
	font-size:18px;
	text-transform:uppercase;
	text-align:center;
	text-decoration:underline;
}
.isi{
	text-align:justify;
}
.ket{
	text-decoration:underline;
}
.tempat{
	text-align:right;
}
ul{
	list-style:decimal;
}
h2{
	text-transform:uppercase;
	text-align:center;
	text-decoration:underline;
}
.tengah{
	text-align:center;
}
.top{
	vertical-align:top;
}

/****/

.tabel-table{
	 display: table;
	 border:1px solid #000;
	 width:100%;
	 border-width:1px 0px 0px 1px;
}
.tabel-cell{
	 display: table-cell;
	 border:1px solid #000;
	 border-width:0px 1px 1px 0px;
	 padding:5px 10px;
	 vertical-align:top;
}

/****/

@media print {
	.tombol-print{
		display:none;
	}
}


</style>

<!-- QRCODE -->
<script type="text/javascript" src="lib/jquery-qrcode-0.11.0/demo/jquery-1.10.2.js"></script>
<script type="text/javascript" src="lib/jquery-qrcode-0.11.0/jquery.qrcode-0.11.0.js"></script>
<script type="text/javascript" src="lib/jquery-qrcode-0.11.0/demo/ff-range.js"></script>
<script type="text/javascript" src="lib/jquery-qrcode-0.11.0/demo/scripts.js"></script>
<!--<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700' rel='stylesheet'>-->
<link href='http://fonts.googleapis.com/css?family=Noto+Sans' rel='stylesheet' type='text/css'>

</head>

<body>


<div class="tombol-print">
<input type="button" value="Print" onClick="print();">
</div>

<br />
<!--<h2>Pakta Integritas</h2>-->
<div class="judul">Pakta Integritas</div>
<br />
<div class="area-dokumen"> 
	
    
    <table class="table">
          <tr class="tr-bc">
            <td align="right" width="44">1</td>
            <td width="871">Tidak akan melakukan praktek KKN;</td>
          </tr>
          <tr>
            <td align="right">2</td>
            <td>Akan melaporkan kepada pihak yang berwajib/berwenang apabila</td>
          </tr>
          <tr>
            <td></td>
            <td> mengetahui ada indikasi KKN dalam    proses pengadaan/pekerjaan ini;</td>
          </tr>
          <tr>
            <td align="right">3</td>
            <td>Dalam proses pengadaan/pekerjaan ini, berjanji akan melaksanakan tugas    secara bersih, transparan, dan pekerjaan/kegiatan ini;</td>
          </tr>
          <tr>
            <td></td>
            <td>profesional dalam arti akan mengerahkan segala kemampuan dan sumber daya    secara optimal untuk </td>
          </tr>
          <tr>
            <td></td>
            <td>memberikan hasil kerja terbaik mulai dari penyiapan penawaran,    pelaksanaan, pelaksanaan, dan penyelesaian </td>
          </tr>
          <tr>
            <td align="right">4</td>
            <td>Apabila saya melanggar hal-hal yang telah saya nyatakan dalam PAKTA    INTEGRITAS ini, saya bersedia dikenakan </td>
          </tr>
          <tr>
            <td></td>
            <td>sanksi moral, sanksi administrasi serta dituntut ganti rugi dan pidana    sesuai dengan ketentuan peraturan </td>
          </tr>
          <tr>
            <td></td>
            <td>perundang-undangan yang berlaku.</td>
          </tr>
        </table>
    <p>Saya yang bertandatangan dibawah ini, dalam rangka pengadaan/pekerjaan <?=$paketInfo->nama?>,<br />
    dengan ini menyatakan bahwa saya :</p>
   
</div>
<?php /*?><div class="tempat">....................,....................</div><?php */?>
<div class="table">
  <div style="display: table-row;">
    <div class="tabel-cell">
		1. Biro Pengadaan Barang dan Jasa :
    </div>
    <div class="tabel-cell tengah">
		Nama jelas
    </div>
    <div class="tabel-cell tengah">
		Tanda tangan
    </div>
  </div>
  <?php
  $no = 1;
  for($i=0;$i<count($arrNama);$i++)
  {
	  if($arrJenis[$i] == "PANITIA")
	  {
		  ?>
		  <div style="display: table-row;">
			<div class="tabel-cell">
				<?=$no;?>.
			</div>
			<div class="tabel-cell" style="vertical-align:top;">
				<?=$arrNama[$i]?>
			</div>
			<div class="tabel-cell tengah">
            	<?php
                if($arrKodeQr[$i] == "") {}
				else
				{
					$mcrypt = new AES();
					$encrypt_text = $arrKodeQr[$i];
				?>
                    <div id="qrcode<?=$i?>"></div>
                    <script language="javascript">
						$('#qrcode<?=$i?>').qrcode({
							render: 'canvas',
							minVersion: 6,
							ecLevel: 'H',
							left: 0,
							top: 0,
							size: 80,
							fill: '#000',
							background: '#ffffff',
							text: "<?=$mcrypt->encrypt($encrypt_text);?>",
							radius: 50,
							quiet: 1,
							mode: 2,
							mSize: 0.1,
							mPosX: 0.5,
							mPosY: 0.5,
							label: '<?=$encrypt_text?>',
							fontname: 'Noto Sans',
							fontcolor: '#898989'
						});	
                    </script>
                <?php
					unset($mcrypt);
				}
				?>
			</div>
		  </div>
		  <?php
  		$no++;
	  }
  }
  ?>

  <?php
  $no = 1;
  for($i=0;$i<count($arrNama);$i++)
  {
	  if($arrJenis[$i] == "FUNGSIONAL")
	  {
		  if($no == 1)
		  {
  ?>
              <div style="display: table-row;">
                <div class="tabel-cell">
                    2. Unit Kerja Pemakai / Fungsional<br />
                    <?=getAbjad($no);?>.
                </div>
                <div class="tabel-cell" style="vertical-align:top;">
                    <br />
                    <?=$arrNama[$i]?>
                </div>
                <div class="tabel-cell tengah">
                    <?
					if($arrKodeQr[$i] == "") {}
					else
					{
						$mcrypt = new AES();
						$encrypt_text = $arrKodeQr[$i];
					?>
						<div id="qrcode<?=$i?>"></div>
						<script>
							$('#qrcode<?=$i?>').qrcode({
								render: 'canvas',
								minVersion: 6,
								ecLevel: 'H',
								left: 0,
								top: 0,
								size: 80,
								fill: '#000',
								background: '#ffffff',
								text: "<?=$mcrypt->encrypt($encrypt_text);?>",
								radius: 50,
								quiet: 1,
								mode: 2,
								mSize: 0.1,
								mPosX: 0.5,
								mPosY: 0.5,
								label: '<?=$encrypt_text?>',
								fontname: 'Noto Sans',
								fontcolor: '#898989'
							});	
						</script>
					<?php
						unset($mcrypt);
					}
					?>
                </div>
              </div>  
  <?php
		  }
  		$no++;
	  }
  }
  ?>
    
  <?php
  if($reqRekananId == "")
  {}
  else
  {
	  $rekanan->selectByParamsSimple(array("MD5(A.REKANAN_ID)" => $reqRekananId));
	  $rekanan->firstRow();
	  $paket_pakta_integritas->selectByParams(array("KODE" => $rekanan->getField("KODE"), "PAKET_ID" => $reqId));
	  $paket_pakta_integritas->firstRow();
  ?>
  <div style="display: table-row;">
    <div class="tabel-cell">
		3. Penyedia Barang / Jasa (Rekanan)
    </div>
    <div class="tabel-cell" style="vertical-align:top;">
    	<?=strtoupper($rekanan->getField("NAMA"))?>
    </div>
    <div class="tabel-cell tengah">
    	<div id="qrcodeRekanan"></div>
        <?php            
		$mcrypt = new AES();
		$encrypt_text = $paket_pakta_integritas->getField("KODE_QR");
		?>
		<script>
        $('#qrcodeRekanan').qrcode({
		  render: 'canvas',
		  minVersion: 6,
		  ecLevel: 'H',
		  left: 0,
		  top: 0,
		  size: 80,
		  fill: '#000',
		  background: '#ffffff',
		  text: "<?=$mcrypt->encrypt($encrypt_text);?>",
		  radius: 50,
		  quiet: 1,
		  mode: 2,
		  mSize: 0.1,
		  mPosX: 0.5,
		  mPosY: 0.5,
		  label: '<?=$encrypt_text?>',
		  fontname: 'Noto Sans',
		  fontcolor: '#898989'
	  });		
        </script>
    </div>
  </div>
  <?php
  }
  ?>
</div>


</body>
</html>