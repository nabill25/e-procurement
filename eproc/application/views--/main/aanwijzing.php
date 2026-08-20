<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
 
if($this->USER_TYPE_ID == "")
    redirect("main");

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("Aanwijzing");
$this->load->model("PaketRekanan");
$this->load->model("PaketTahap");
$this->load->model("PaketDokumen");
$this->load->model("PaketAanwijzingValidasi");
$this->load->model("QrValidasi");
$this->load->library("kauth");  $userLogin = new kauth();
include_once("functions/default.func.php");
include_once("functions/string.func.php");

$aanwijzing = new Aanwijzing();
$aanwijzing_room = new Aanwijzing();
$aanwijzing_keterangan = new Aanwijzing();
$paket_aanwijzing_validasi = new PaketAanwijzingValidasi();
$qr_validasi = new QRValidasi();

$reqId = httpFilterRequest("reqId");

$jumlah_aanwijzing = $aanwijzing->getCountByParams(array("PAKET_ID" => $reqId, "AANWIJZING_PARENT_ID" => 0));
if($jumlah_aanwijzing == 0)
{
	echo '<script language="javascript">';
	echo "alert('".translate("Materi aanwijzing belum dibuat", "Aanwijzing not yet started")."');";
	echo "window.top.location.href = '".base_url()."main/index/paket_detil/?reqId=".$reqId."';";
	echo '</script>';
	exit();
}

if($this->USER_TYPE_ID == 3)
	$nickname = "Panitia Lelang";
elseif($this->USER_TYPE_ID == 6)
	$nickname = $this->REKANAN_KODE;//$this->REKANAN;
else
	$nickname = $this->REKANAN;

$paketInfo->getPaket($reqId);

if($this->USER_TYPE_ID == 6)
{
	/*$paket_dokumen = new PaketDokumen();
	$dokumen_berbayar = $paket_dokumen->getCountByParams(array("STATUS" => 1, "PAKET_ID" => $reqId));
	if($dokumen_berbayar > 0)
	{
		$paket_rekanan = new PaketRekanan();
		if($paket_rekanan->getPaketRekananBayar($reqId, $this->REKANAN_ID) == 0)
		{
			echo '<script language="javascript">';
			echo 'alert("Anda tidak punya hak mengakses halaman ini.\n Silakan bayar terlebih dahulu.");';
			echo 'top.location.href = "index";';
			echo '</script>';
			exit;
		}
	}*/

	if($paketInfo->metode_kualifikasi_id == 1)
	{
		$paket_rekanan_check = new PaketRekanan();
		$paket_rekanan_check->selectByParamsPaketLelang(array("PAKET_ID" =>$reqId, "REKANAN_ID" => $this->REKANAN_ID));
		$paket_rekanan_check->firstRow();
		if($paket_rekanan_check->getField("LULUS_KUALIFIKASI") == 0)
		{
			echo '<script language="javascript">';
			echo 'alert("Anda tidak punya hak mengakses halaman ini.\n Anda telah gagal pada tahap kualifikasi.");';
			echo 'top.location.href = "index";';
			echo '</script>';
			exit;
		}
	}
}

$paket_tahap_metode = new PaketTahap();
$paket_tahap = new PaketTahap();
//$arrAanwijzing = array(0, 10, 5, 10, 5, 9, 5, 10, 10);

// $arrAanwijzing = array(0, 10, 5,  10, 5,  9,  5,  10, 10, 0, 0, 10, 5,  10, 5);
$arrAanwijzing = AANWIJZING;

$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
// $check = $paket_tahap->getCountByParams(array("URUT" => $arrAanwijzing[$jenis_tahap], "PAKET_ID" => $reqId), " AND (SYSDATE BETWEEN TANGGAL_AWAL AND TANGGAL_AKHIR OR TO_CHAR(SYSDATE, 'DDMMYYYY') =  TO_CHAR(TANGGAL_AWAL, 'DDMMYYYY')) ");
$check = $paket_tahap->getCountByParams(array("URUT" => $arrAanwijzing[$jenis_tahap], "PAKET_ID" => $reqId), " AND (to_char(now(),'DDMMYYYY') BETWEEN to_char(TANGGAL_AWAL,'DDMMYYYY') AND to_char(TANGGAL_AKHIR,'DDMMYYYY')) ");
if((int)$check == 0)
{//echo "string"; die();
}
else
{

	$paket_rekanan = new PaketRekanan();
	$reqPaketRekananId = $paket_rekanan->getPaketRekananId($reqId, $this->REKANAN_ID);
	$paket_rekanan->setField("FIELD", "AANWIJZING");
	$paket_rekanan->setField("FIELD_VALUE", 1);
	$paket_rekanan->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
	$paket_rekanan->update();
}

$paket_aanwijzing_validasi->selectByParamsValidasi(array("NIP" => $this->NIP, "A.PAKET_ID" => $reqId));
$paket_aanwijzing_validasi->firstRow();

if($this->USER_TYPE_ID == 6)
{
	if($paket_aanwijzing_validasi->getField("KODE") == "")
	{
		$paket_aanwijzing_validasi->setField("PAKET_ID", $reqId);
		$paket_aanwijzing_validasi->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
		$paket_aanwijzing_validasi->setField("KODE", $this->USER_LOGIN_ID);
		$paket_aanwijzing_validasi->setField("JENIS", "REKANAN");
		$paket_aanwijzing_validasi->insert();
	}
}

$aanwijzing_publish = new Aanwijzing();
$aanwijzing_publish->selectByParams(array("PAKET_ID" => $reqId));
$aanwijzing_publish->firstRow();
//echo $aanwijzing_publish->query;exit;

if($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 7)
{
	$kode_qr = generateZero($paketInfo->unit_kerja_id, 3).generateZero($reqId, 6);
	$qr_validasi->selectByParams(array("KODE_QR" => $kode_qr, "SUMBER" => "DOKUMEN_AANWIJZING"));
	$qr_validasi->firstRow();

	if($qr_validasi->getField("KODE_QR") == "")
	{
		$qr_validasi->setField("SUMBER", "DOKUMEN_AANWIJZING");
		$qr_validasi->setField("KODE_QR", $kode_qr);
		$qr_validasi->setField("PAKET_ID", $reqId);
		$qr_validasi->setField("INFORMASI", "DOKUMEN BERITA ACARA AANWIJZING\n\n".$aanwijzing_publish->getField("NAMA")."\n\n".strtoupper($paketInfo->nama));
		$qr_validasi->insert();
	}
}

$aanwijzing_keterangan = new Aanwijzing();
$aanwijzing_keterangan->selectByParams(array("PAKET_ID" => $reqId), -1, -1, "", " ORDER BY KODE ASC ");
$i = 1;
while($aanwijzing_keterangan->nextRow())
{
	$arrKeteranganBuku[$i]["KETERANGAN"] = $aanwijzing_keterangan->getField("KETERANGAN");
	$i++;
}


?>
<link rel="stylesheet" href="<?=base_url()?>css/core.css" type="text/css">

<script type="text/javascript" src="lib/chatboxfb/js/jquery-1.9.0.min.js"></script>

<script type="text/javascript">

function hiddenBodyScroll()
{
	document.body.style.overflow = "hidden";
}
</script>

<style type="text/css">

.arrowsidemenu{
	width: 180px; /*width of menu*/
	border-style: solid solid none solid;
	border-color: #c1c1c1;
	border-size: 1px;
	border-width: 1px;
}

.arrowsidemenu div a{ /*header bar links*/
	font: bold 12px Verdana, Arial, Helvetica, sans-serif;
	display: block;
	background: transparent url("base-main/DDAccordionMenu/bg-row.png") 100% 0;
  height: 24px; /*Set to height of bg image-padding within link (ie: 32px - 4px - 4px)*/
	padding: 4px 0 4px 10px;
	line-height: 24px; /*Set line-height of bg image-padding within link (ie: 32px - 4px - 4px)*/
	text-decoration: none;
}

.arrowsidemenu div a:link, .arrowsidemenu div a:visited{
	color: #26370A;
}

.arrowsidemenu div a:hover{
	background-position: 100% -32px;
}

.arrowsidemenu div.unselected a{ /*header that's currently not selected*/
	color: #6F3700;
}


.arrowsidemenu div.selected a{ /*header that's currently selected*/
	color: blue;
	background-position: 100% -64px !important;
}

.arrowsidemenu ul{
	list-style-type: none;
	margin: 0;
	padding: 0;
}

.arrowsidemenu ul li{
	border-bottom: 1px solid #e1dfdf;
}


.arrowsidemenu ul li a{ /*sub menu links*/
	display: block;
	font: normal 12px Verdana, Arial, Helvetica, sans-serif;
	text-decoration: none;
	color: black;
	padding: 5px 0;
	padding-left: 10px;
	border-left: 10px double #e1dfdf;
}

.arrowsidemenu ul li a:hover{
	background: #d5e5c1;
}

</style>
<style>
h2#pfc_title{ color:#0086a9; padding-bottom:7px;}
.klik-buku a span:nth-child(1){
	font-family: 'Open SansRegular';
}
.klik-buku a span:nth-child(2){

}
.klik-buku a span:nth-child(3){

}
.loader {
	position: fixed;
	left: 0px;
	top: 0px;
	width: 100%;
	height: 100%;
	z-index: 9999;
	/*background: url('images/page-loader.gif') 50% 50% no-repeat rgb(249,249,249);*/
	background: url('images/page-loader.gif') 50% 50% no-repeat rgb(245,245,245);
}

</style>

<script type="text/javascript">

	$(window).load(function() {
		$(".loader").fadeOut("slow");
		timeoutMsgID = setTimeout(refresh, 100);
	})

	var count = 0;
	var files = '';
	var lastTime = 0;

	function prepare(response) {
	  var d = new Date();
	  count++;
	  d.setTime(response.time*1000);
	  var mytime = (d.getHours() < 10 ? '0' + d.getHours() : d.getHours()) +':'+ (d.getMinutes() < 10 ? '0' + d.getMinutes() : d.getMinutes()) + ':' + (d.getSeconds() < 10 ? '0' + d.getSeconds() : d.getSeconds());
	  var string = '<div class="shoutbox-list" id="list-'+count+'">'
		  + '<span class="shoutbox-list-time">'+response.waktu+'</span>'
		  + '<span class="shoutbox-list-nick">'+response.nickname+':</span>'
		  + '<span class="shoutbox-list-message">'+response.message+'</span>'
		  +'</div>';
	  return string;
	}

	function refresh()
	{
		clearTimeout(timeoutMsgID);
        // $.getJSON(files+"shoutbox_json/json/?reqKode=0&reqId=<?=$reqId?>&action=view&time="+lastTime, function(json) {
        $.getJSON(files+"shoutbox_json/json/?reqKode=0&reqId=<?=$reqId?>&action=view&time="+lastTime, function(json) {
            if(json.length) {
              for(i=0; i < json.length; i++) {
				$('.frameBuku').contents().find('.frameShoutbox').contents().find('#daddy-shoutbox-list-global').prepend(prepare(json[i]));
              }
              var j = i-1;
              lastTime = json[j].time;
            }
        });

		<?php
		if($this->USER_TYPE_ID == 3)
		{}
		else
		{
		?>
		$.getJSON('json/aanwijzing_publish_json/json/?reqId=<?=$reqId?>', function (json)
		{
		   if(json.length) {
			if(json[0].PUBLISH == '1')
			{
				$("#btnCetak").css("display", "");
			}
		  }
		});
		<?php
		}
		?>
        timeoutMsgID = setTimeout(refresh, 5000);


	}

	<?php
	if($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 7)
	{
	?>
	function publishAanwijzing()
	{
		$.getJSON('paket_aanwijzing_validasi_json/aanwijzing_publish_validasi_json/?reqId=<?=$reqId?>',
		function(dataJson){
			if(dataJson.PESAN == "1")
			{
				if(confirm("Publish berita acara aanwijzing dan email pemberitahuan ke peserta?"))
				{
					$(".loader").fadeIn("slow");
					var jqxhr = $.get( "aanwijzing_json/set_publish_aanwijzing/?reqId=<?=$reqId?>", function(data) {
					  $(".loader").fadeOut("slow");
					  alert(data);
					  $("#btnPublish").css("display", "none");
					})
					  .fail(function() {
						alert( "error" );
					  });
				}
			}
			else
				alert(dataJson.PESAN);

		});

	}
	<?php
	}
	?>

	<?php
	if($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 9 || $this->USER_TYPE_ID == 7)
	{
	?>
	function submitValidasi(kode, jenis)
	{
		if(confirm("Validasi berita acara aanwijzing ?"))
		{
			$.getJSON('paket_aanwijzing_validasi_json/aanwijzing_validasi_json/?reqId=<?=$reqId?>&reqKode='+kode+'&reqJenis='+jenis,
			function(data){
			  alert(data.PESAN);
			  $("#tombolValidasi").css("display", "none");
			});
		}
	}
	<?php
	}
	?>

	/*function disableF5(e) { if ((e.which || e.keyCode) == 116) e.preventDefault(); };
		$(document).ready(function(){
		$(document).on("keydown", disableF5);
	});*/

</script>

<div class="loader"></div>

<!-- BOOK PREVIEW -->
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>lib/BookPreview/css/normalize.css" />
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>lib/BookPreview/css/demo.css" />
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>lib/BookPreview/css/bookblock.css" />
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>lib/BookPreview/css/component.css" />
<script src="<?= base_url() ?>lib/BookPreview/js/modernizr.custom.js"></script>
<style>
.bookshelf figure .ket{
	position:absolute;
	/*bottom:130px;*/
	top:70px;
	width:100%;
	text-align:center;
	/*font-weight:bold;*/
	font-size:14px;

	font-family: 'ApercuRegular';
}
</style>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Aanwijzing</h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a> 
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable">
	        <table>
	            <?php /*?><tr>
	              <td width="20%">&nbsp;</td>
	              <td width="29%">&nbsp;</td>
	              <td width="17%" align="right">&nbsp;</td>
	              <td width="17%" align="right">&nbsp;</td>
	              <td width="17%" align="right">&nbsp;</td>
	            </tr><?php */?>
	            <?php /*?><tr>
	              <td colspan="5" class="judul-halaman">Aanwijzing [ <?=$this->REKANAN?> ]</td>
	            </tr>
	            <tr>
				<?php */?>
	            <?php
	            //$link_paket_lelang = $link_home.'main/?pg=paket_lelang&reqMode=view';
	            //$link_paket_detil = $link_home.'main/?pg='.$_SESSION['sesJenisLelangDetilPage'].'&reqId='.$reqId;
	            ?>
	              <?php /*?><td colspan="5">
	                <div id="nav" style="display:none;">
	                    <ul>
	                        <!--<li>Current onedha</li>-->
	                        <li>Aanwijzing</li>
	                        <li style="width:75px;"><a href="<?=$link_paket_detil?>"><span>Detil</span></a></li>
	                        <li style="width:<?=$_SESSION['sesJenisLelangWidth']?>;"><a href="<?=$_SESSION['sesBackToPaketLelangParams']?>"><span><?=$_SESSION['sesJenisLelangJudul']?></span></a></li>
	                        <li style="width:95px;"><a href="<?=$link_home?>"><span>Home</span></a></li>
	                    </ul>
	                </div>
	              </td>
	            </tr><?php */?>
	            <?php /*?><tr>
	              <td colspan="5">&nbsp;</td>
	            </tr><?php */?>
	            <tr>
	              <td valign="top" colspan="5">
	            <!-- ########################################################################### -->

	            <div id="scroll-wrap" class="container">
	                <div class="main">
	                    <div id="bookshelf" class="bookshelf">

	                        <?php
	                        if($jumlah_aanwijzing == 1)
	                        {
	                        ?>
	                        <figure>
	                            <div class="book" data-book="book-1" style="margin-bottom:-260px;"></div>
	                            <div class="buttons klik-buku" onClick="hiddenBodyScroll();">
	                                <a title="#" id="bukaBuku1">
	                                    &nbsp;
	                                </a>
	                                <a title="#">Details</a>
	                            </div>
	                            <div class="details">&nbsp;</div>
	                            <div class="ket"><?=$arrKeteranganBuku[1]["KETERANGAN"]?></div>
	                        </figure>
	                        <?php
	                        }
	                        else
	                        {
	                            $buku_ke = 2;
	                            for($i=1;$i<=$jumlah_aanwijzing;$i++)
	                            {
	                        ?>
	                                <figure>
	                                    <div class="book" data-book="book-<?=$buku_ke?>" style="margin-bottom:-260px;"></div>
	                                    <div class="buttons klik-buku" onClick="hiddenBodyScroll();">
	                                        <a title="#" id="bukaBuku<?=$i?>">
	                                            &nbsp;
	                                        </a>
	                                        <a title="#">Details</a>
	                                    </div>
	                                    <div class="details">&nbsp;</div>
	                                    <div class="ket"><?=$arrKeteranganBuku[$i]["KETERANGAN"]?></div>
	                                </figure>
	                        <?php
	                                $buku_ke++;

	                            }
	                        }
	                        ?>
	                        <figure>
	                            <div class="book" data-book="book-16" style="margin-bottom:-260px;"></div>
	                            <div class="buttons klik-buku" onClick="hiddenBodyScroll();">
	                                <a title="#" id="bukaPanduan">
	                                    &nbsp;
	                                </a>
	                                <a title="#">Details</a>
	                            </div>
	                            <div class="details">&nbsp;</div>
	                        </figure>
	                    </div>
	                </div><!-- /main -->
	            </div><!-- /container -->

	            <!-- Fullscreen BookBlock -->
	            <?php
	            if($jumlah_aanwijzing == 1)
	            {
	            ?>
	                <div class="bb-custom-wrapper" id="book-1">
	                    <div class="bb-bookblock">
	                        <div class="bb-item" style="height:100%; min-height:100%">
	                            <iframe id="buku-frame" class="frameBuku" src="main/loadUrl/main/aanwijzing_buku/?reqJumlahBuku=1&reqKode=1&reqId=<?=$reqId?>"></iframe>
	                        </div>
	                    </div><!-- /bb-bookblock -->
	                    <nav>
	                        <a title="#" class="bb-nav-prev">Previous</a>
	                        <a title="#" class="bb-nav-next">Next</a>
	                        <a title="#" class="bb-nav-close">Close</a>
	                    </nav>
	                </div><!-- /bb-custom-wrapper -->
	            <?php
	            }
	            else
	            {
	                $buku_ke = 2;
	                for($i=1;$i<=$jumlah_aanwijzing;$i++)
	                {
	            ?>
	                    <div class="bb-custom-wrapper" id="book-<?=$buku_ke?>">
	                        <div class="bb-bookblock">
	                            <div class="bb-item" style="height:100%; min-height:100%">
	                                <iframe id="buku-frame" class="frameBuku" src="main/loadUrl/main/aanwijzing_buku/?reqJumlahBuku=<?=$jumlah_aanwijzing?>&reqKode=<?=$i?>&reqId=<?=$reqId?>"></iframe>
	                            </div>
	                        </div>
	                        <nav>
	                            <a title="#" class="bb-nav-prev">Previous</a>
	                            <a title="#" class="bb-nav-next">Next</a>
	                            <a title="#" class="bb-nav-close">Close</a>
	                        </nav>
	                    </div>
	            <?php
	                    $buku_ke++;
	                }
	            }
	            ?>
	                    <div class="bb-custom-wrapper" id="book-16">
	                    <div class="bb-bookblock">
	                        <div class="bb-item" style="height:100%; min-height:100%">
	                            <iframe id="buku-frame" src="main/loadUrl/main/aanwijzing_buku_panduan"></iframe>
	                        </div>
	                    </div><!-- /bb-bookblock -->
	                    <nav>
	                        <a title="#" class="bb-nav-prev">Previous</a>
	                        <a title="#" class="bb-nav-next">Next</a>
	                        <a title="#" class="bb-nav-close">Close</a>
	                    </nav>
	                </div>

					<script src="<?= base_url() ?>lib/BookPreview/js/bookblock.min.js"></script>
	                <script src="<?= base_url() ?>lib/BookPreview/js/classie.js"></script>
	                <script src="<?= base_url() ?>lib/BookPreview/js/bookshelf.js"></script>

	              </td>
	            </tr>
	            <tr>
	              <td colspan="5">&nbsp;</td>
	            </tr>
	            <tr>
	              <td>
	                  <a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-danger text-white"><i class="fa fa-arrow-left"></i> Kembali</a>
	              <?php
	              // if($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 9 || $this->USER_TYPE_ID == 7)
	              if($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 9)
	              {
	                  if($paket_aanwijzing_validasi->getField("KODE") == "")
	                  {
	                  ?>
	                  	<a title="#" id="tombolValidasi" onclick="submitValidasi('<?=$paket_aanwijzing_validasi->getField("NIP")?>', '<?=$paket_aanwijzing_validasi->getField("JENIS")?>')" class="btn btn-success text-white"><i class="fa fa-check-square-o"></i> Validasi</a>
	                  <?php
	                  }
	              }
	              ?>
	              <?php
	              if($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 7)
	              {
	                  if($aanwijzing_publish->getField("PUBLISH") == 0)
	                  {
	                      if($paketInfo->user_login_id == $this->USER_LOGIN_ID)
	                      {
	              ?>
	                          <a title="#" onClick="publishAanwijzing();" id="btnPublish" class="btn btn-info text-white"><i class="fa fa-send"></i> Publish</a>
	              <?php
	                      }
	                  }
	              ?>
	                  <a href="main/loadUrl/report/aanwijzing_cetak_pdf/?reqId=<?=$reqId?>" target="_blank" class="btn btn-primary"><i class="fa fa-print"></i> Cetak</a>
	              <?php
	              }
	              else
	              {
	                  if($aanwijzing_publish->getField("PUBLISH") == 0)
	                    $style = "style='display:none'";
	              ?>
	                  <a href="main/loadUrl/report/aanwijzing_cetak_pdf/?reqId=<?=$reqId?>" target="_blank" <?=$style?> id="btnCetak"  class="btn btn-primary text-white"><i class="fa fa-print"></i> Cetak</a>
	              <?php
	              }
	              ?>
	              </td>
	            </tr>
	            </table>
        </div>
      </div>
    </div>
  </div> 
</div>  
 