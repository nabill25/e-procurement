<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

$this->load->library("kauth");  //$userLogin = new kauth(); 
$this->load->model("PaketRekanan");
$this->load->model("Aanwijzing");
$this->load->model("PhpShoutbox");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
//include_once("classes/utils/Validate.php");


$aanwijzing = new Aanwijzing();
$paket_rekanan = new PaketRekanan();

$reqId = httpFilterGet("reqId");
$reqKode = httpFilterGet("reqKode");
$reqJumlahBuku = httpFilterGet("reqJumlahBuku");

if($this->USER_TYPE_ID == 3)
	$nickname = "Panitia Lelang";
elseif($this->USER_TYPE_ID == 6)
{
	$paket_rekanan->selectByParams(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => $this->REKANAN_ID));
	$paket_rekanan->firstRow();
	$nickname = $paket_rekanan->getField("KODE_REKANAN");//	
}
else
	$nickname = $this->REKANAN;


$aanwijzing->selectByParams(array("PAKET_ID" => $reqId, "KODE" => $reqKode));
$aanwijzing->firstRow();
//echo $aanwijzing->query;exit;
$jumlah_halaman = $aanwijzing->getField("FILE_COUNT");
$prefix_halaman = $aanwijzing->getField("FILE_UPLOAD");
$jumlah_buku = $reqJumlahBuku;
	
?>

<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>Fullscreen Pageflip Layout with BookBlock</title>
		<base href="<?=base_url()?>" />
		<meta name="description" content="Fullscreen Pageflip Layout with BookBlock" />
		<meta name="keywords" content="fullscreen pageflip, booklet, layout, bookblock, jquery plugin, flipboard layout, sidebar menu" />
		<meta name="author" content="Codrops" />
		<link rel="shortcut icon" href="lib/favicon.ico">
        
        <script src="lib/FullscreenBookBlock/js/jquery.min.js"></script>
		<!-- FULLSCREEN BOOKBLOCK -->
		<link rel="stylesheet" type="text/css" href="lib/FullscreenBookBlock/css/jquery.jscrollpane.custom.css" />
		<link rel="stylesheet" type="text/css" href="lib/FullscreenBookBlock/css/bookblock.css" />
		<link rel="stylesheet" type="text/css" href="lib/FullscreenBookBlock/css/custom.css" />
		<script src="lib/FullscreenBookBlock/js/modernizr.custom.79639.js"></script>
        
        <style>
		#separuh-kiri{
			width:50%; float:left;
			background:#0086a9;
			
			height:100%; min-height:100%; 
			/*height: -moz-calc(100% + 150px);
			height: -webkit-calc(100% + 150px);
			height: -o-calc(100% + 150px);
			height: calc(100% + 150px);
	
			min-height: -moz-calc(100% + 150px);
			min-height: -webkit-calc(100% + 150px);
			min-height: -o-calc(100% + 150px);
			min-height: calc(100% + 150px);*/
			
		}
		#separuh-kiri img{
			height:100%;
			width:100%;
		}

		#separuh-kiri-tombol{
			position:absolute; right:50%; padding:20px 20px 10px;
		}
		#separuh-kiri-tombol input[type = button]{
			background:#4bbad9;
			color:#FFF;
			border:none;
			padding:7px 10px 8px;
			font-size:12px;
			text-transform:uppercase;
			
			-webkit-border-radius: 16px;
			-moz-border-radius: 16px;
			border-radius: 16px;
		}
		#separuh-kiri-tombol input[type = button].disable{
			background:#b0b0b0;
			color:#dddddd;
			border:none;
			padding:7px 10px 8px;
			font-size:12px;
			text-transform:uppercase;
			
			-webkit-border-radius: 16px;
			-moz-border-radius: 16px;
			border-radius: 16px;
		}
		#separuh-kanan{
			width:50%; float:right;
			/*background:#0086a9;*/
			background:#71cee8;
			
			height:100%;
			min-height:100%;
			
			position:relative;
			
		}
		#separuh-kanan iframe{
			width:100%;
			height:100%;
			min-height:100%;
			/*
			height:50%;
			min-height:50%;*/
			border: none;
			overflow:hidden;
			
		}
		#separuh-kanan #kanan-bawah{
			/*padding:18px 20px;*/
			background:#e0f5fb;
			height:100%;
			min-height:100%;
			
			/*height: -moz-calc(50% - 36px);
			height: -webkit-calc(50% - 36px);
			height: -o-calc(50% - 36px);
			height: calc(50% - 36px);
	
			min-height: -moz-calc(50% - 36px);
			min-height: -webkit-calc(50% - 36px);
			min-height: -o-calc(50% - 36px);
			min-height: calc(50% - 36px);*/
			
			overflow-y:scroll;
		}
		#kanan-bawah-inner{
			padding:18px 20px;
		}
		#tombol2{
			/*background:#4bbad9; */
			float:left; 
			position:fixed; 
			left:160px; 
			top:20px; 
			height:32px; 
			padding:0 20px;
			z-index:999;
			
			/*-webkit-border-radius: 16px;
			-moz-border-radius: 16px;
			border-radius: 16px;*/
		}
		
		</style>
<link rel="stylesheet" href="WEB-INF/base-main/css/form.css" type="text/css">
              
		<!-- SCROLL -->

      <script type="text/javascript">
        var count = 0;
        var files = '';
        var lastTime = 0;
		var activate = 1;
        
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

        function refresh() {

		  clearTimeout(timeoutID);
		  if($("#activate").val() == "1")
		  {
			  $("#activate").val("0");
			  $.getJSON(files+"shoutbox_json/json/?reqKode=<?=$reqKode?>&reqId=<?=$reqId?>&action=view&time="+lastTime, function(json) {
				 
				if(json.length) {
				  for(i=0; i < json.length; i++) {
					$('#childFrame'+json[i].halaman).contents().find('#daddy-shoutbox-list-'+json[i].halaman).prepend(prepare(json[i]));
				  }
				  var j = i-1;
				  lastTime = json[j].time;
				}
				else
					i=0;
					
				if(i == json.length)
					$("#activate").val("1");	
				//alert(lastTime);
			  });
		  }
	      timeoutID = setTimeout(refresh, 5000);
        }
        
        // wait for the DOM to be loaded 
        $(document).ready(function() { 
            //$('#daddy-shoutbox-form').ajaxForm(options);
            timeoutID = setTimeout(refresh, 2000);
        });

		function konfirmasiPesan(halaman, buttonName)
		{
			if(confirm("<?=translate("Konfirmasi bahwa anda telah membaca dan memahami isi pada halaman", "Confirm that you have read and understood the content of this page")?> "+halaman+" ?"))
			{
				$.getJSON(files+"shoutbox_json/json/?reqId=<?=$reqId?>&reqKode=<?=$reqKode?>&reqToken=<?=$nickname?>&reqHalaman="+halaman+"&action=confirm&time=0", function(json) {			 
					if(json.length) {
					  for(i=0; i < json.length; i++) {
						  alert(json[i].response);
						if(json[i].response == 'success')
						{
							alert('Konfirmasi berhasil.');
							return;
						}
					  }
					}
				});	
				
				$("#"+buttonName).attr("disabled", true);
				$("#"+buttonName).attr("class", "disable");
			}
		}		

		function notifikasiPesan()
		{
			var left = (screen.width/2)-(900/2);
  			var top = (screen.height/2)-(550/2);
			<?
			if($this->USER_TYPE_ID == 6)
				$link = "main/loadUrl/main/aanwijzing_konfirmasi_rekanan/";
			else
				$link = "main/loadUrl/main/aanwijzing_konfirmasi/";			
			?>
			divwin=dhtmlwindow.open('divbox', 'iframe', '<?=$link?>?reqId=<?=$reqId?>&reqKode=<?=$reqKode?>', 'Notifikasi', 'width=900px,height=550px,left='+left+'px,top=40px,resize=1,scrolling=1,midle=1'); return false			
		}				
		
		$(document).ready( function () {
			
			$("#pindahHalaman<?=$reqKode?>").change(function() { 
				var id = $("#pindahHalaman<?=$reqKode?>").val();		
				$("#pindahHalaman<?=$reqKode?>").val(<?=$reqKode?>);
				$('.bb-nav-close', window.parent.document)[<?=($reqKode-1)?>].click();
				$('#bukaBuku'+id, window.parent.document)[0].click();
				
			});
			
		});
		
 	 </script>
  
	</head>
	<body>
		<div id="container" class="container">	

			<div class="menu-panel" style="overflow:scroll;">
				<h3 style="position:fixed; background:#71cee8; width:225px; display:block;">
                	<?=translate("Daftar Isi", "table of contents")?>  
                    <input type="hidden" id="activate" value="1"></h3>
				<ul id="menu-toc" class="menu-toc" style="margin-top:57px;">
                <?
                for($i=1;$i<=$jumlah_halaman;$i++)
				{
					if($i == 1)
						$class = 'class="menu-toc-current"';
					else
						$class = "";
				?>
					<li <?=$class?>><a href="#item<?=$i?>" id="hal-<?=$reqKode?>-<?=$i?>"><?=translate("Hal", "Page")?> <?=$i?></a></li>
                <?
				}
				?>
				</ul>
				
			</div>
			
			
            
			<div class="bb-custom-wrapper">
				<div id="bb-bookblock" class="bb-bookblock">
					<?
                    if($jumlah_buku > 1)
                    {
                    ?>
                
                	<div id="tombol2" class="styled-select green rounded">
                      <select id="pindahHalaman<?=$reqKode?>">
                          <?
                          for($x=1;$x<=$jumlah_buku;$x++)
                          {
                          ?>
                        <option value="<?=$x?>" <? if($reqKode == $x) { ?> selected <? } ?>><?=translate("Dokumen Lelang", "Tender Documents")?> <?=$x?></option>
                          <?
                          }
                          ?>
                      </select>
					</div>
                    
                    <?
					}
					?>
                  <?
                for($i=1;$i<=$jumlah_halaman;$i++)
				{
				?>
					<div class="bb-item" id="item<?=$i?>">
                   
                        <div id="separuh-kiri-tombol">
                        <?
                        if($this->USER_TYPE_ID == 6)
						{
							$php_shoutbox = new PhpShoutbox();
							$php_shoutbox->selectByParams(array("PAKET_ID" => $reqId, "KODE" => $reqKode,"HALAMAN" => $i, "NAMA" => $nickname, "PESAN" => "CONFIRMED"));
							$php_shoutbox->firstRow();
							if($php_shoutbox->getField("JAM") == "")
							{							
						?>
	                        	<input type="button" id="btnKonfirmasi<?=$reqKode?>-<?=$i?>" value="<?=translate("Konfirmasi", "Confirm")?>" onClick="konfirmasiPesan('<?=$i?>', 'btnKonfirmasi<?=$reqKode?>-<?=$i?>');">
                        <?
							}
							else
							{
						?>
	                            <input type="button" class="disable" disabled value="Konfirmasi">
                        <?
							}
						}
						?>    
                        </div>
						<div id="separuh-kiri" style="background-size:100% 100%;">
                        	<img src="uploads/lelang/<?=substr($prefix_halaman,0, strlen($prefix_halaman)-4)?>_<?=$i?>.Jpeg">
                        </div>
                        <div id="separuh-kanan">
                        	<iframe src="main/loadUrl/main/aanwijzing_shoutbox/?reqKode=<?=$reqKode?>&reqId=<?=$reqId?>&reqHalaman=<?=$i?>" class="frameShoutbox" id="childFrame<?=$i?>"></iframe>
                            <?php /*?><div id="kanan-bawah">
                            	<div id="kanan-bawah-inner">
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
                                    
                                    <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur</p>
                                    
                                    <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat</p>
                                </div>
                            </div><?php */?>
						</div>
					</div>
				<?
				}
				?>
                </div>
				
				<nav>
					<span id="bb-nav-prev">&larr;</span>
					<span id="bb-nav-next">&rarr;</span>
				</nav>

				<a id="tblcontents" class="menu-button" onClick=""><?=translate("Daftar Isi", "table of contents")?></a>

			</div>
				
		</div><!-- /container -->
		<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>-->
		<!--<script src="lib/FullscreenBookBlock/js/jquery.min.js"></script>-->
        <script src="lib/FullscreenBookBlock/js/jquery.mousewheel.js"></script>
		<script src="lib/FullscreenBookBlock/js/jquery.jscrollpane.min.js"></script>
		<script src="lib/FullscreenBookBlock/js/jquerypp.custom.js"></script>
		<script src="lib/FullscreenBookBlock/js/jquery.bookblock.js"></script>
		<script src="lib/FullscreenBookBlock/js/page.js"></script>
		<script>
			$(function() {

				Page.init();

			});
		</script>
        
        
        



	</body>
</html>
