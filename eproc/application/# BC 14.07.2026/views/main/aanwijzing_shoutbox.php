<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("PhpShoutbox");
$this->load->model("PaketRekanan");
$this->load->model("Aanwijzing");
include_once("functions/date.func.php");
include_once("functions/default.func.php");


$paket_rekanan = new PaketRekanan();
$aanwijzing = new Aanwijzing();
$php_shoutbox = new PhpShoutbox();

$reqId = httpFilterGet("reqId");
$reqHalaman = httpFilterGet("reqHalaman");
$reqKode = httpFilterGet("reqKode");

if($this->USER_TYPE_ID == 3)
	$nickname = "Panitia Lelang";
elseif($this->USER_TYPE_ID == 6)
{
	$paket_rekanan->selectByParams(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => $this->REKANAN_ID));
	$paket_rekanan->firstRow();
	$nickname = $paket_rekanan->getField("KODE_REKANAN");//
}
else
	$nickname = $this->USER_NAMA;

?>

<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title><?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?></title>
		<base href="<?=base_url()?>" />
        
        <!--<script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>-->
		<script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>     
     
        <!-- Bootstrap core CSS -->
        <link href="lib/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
        
        <link rel="stylesheet" href="css/core.css" type="text/css">
        <link rel="stylesheet" href="css/core-bootstrap.css" type="text/css">
        
        <!-- FONT AWESOME -->
        <link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">
    
    <!-- COPAS -->
    
    <link rel="stylesheet" href="lib/tab2/tabs.css" type="text/css">

    
<!-- #################################################################################################################### -->

  <style type="text/css">
	#frameNotifikasi {
		width:100%;
		height:100%;
		min-height:100%;
		border: none;
	}
	
    #daddy-shoutbox {
		padding: 10px 20px;

		width: -moz-calc(100% - 40px);
		width: -webkit-calc(100% - 40px);
		width: -o-calc(100% - 40px);
		width: calc(100% - 40px);
		
		font-size: 14px;
		
		float:left;
		width:100%;
		
    }
    .shoutbox-list {
		*border-bottom: 1px solid #0289ab;
		border-bottom: 1px solid rgba(0,0,0,0.2);
		
		padding: 5px;
	  
    }
	
    #daddy-shoutbox-form {
      text-align: left;
      
    }
    .shoutbox-list-time {
      *color: #8DA2B4;
	  color:rgba(0,0,0,0.6);
    }
    .shoutbox-list-nick {
      margin-left: 5px;
      font-weight: bold;
	  text-transform:uppercase;
    }
    .shoutbox-list-message {
      margin-left: 5px;
	  text-transform:capitalize;
    }
	#daddy-shoutbox input[type = text]{
		border:1px solid #0289ab;
		padding:4px 10px;
	}
	/*#daddy-shoutbox input[type = submit]{
		background:#0289ab;
		color:#FFF;
		border:none;
		padding:3px 10px 4px;
		font-size:12px;
		text-transform:uppercase;
	}*/
	#daddy-shoutbox input[type = button]{
		background:#0289ab;
		color:#FFF;
		border:none;
		padding:3px 10px 4px;
		font-size:12px;
		text-transform:uppercase;
	}
    
  </style>
  <script type="text/javascript" src="lib/shoutbox2/javascript/jquery.js"></script>
  <script type="text/javascript" src="lib/shoutbox2/javascript/jquery.form.js"></script>
  <script type="text/javascript">
        
        var files = '';
		
        function success(response, status)  { 
          if(status == 'success') {
			if(response.response == 'failed')
			{
				alert('Waktu aanwijzing telah usai.');
				$('#daddy-shoutbox-response-<?=$reqHalaman?>').html('');
				$('#message-<?=$reqHalaman?>').attr('value', '');
				$('#message-<?=$reqHalaman?>').focus();		
			}
			else if(response.response == 'closed')
			{
				alert('Berita acara aanwijzing telah di-publish, penambahan informasi harus menggunakan addendum dokumen lelang.');
				$('#daddy-shoutbox-response-<?=$reqHalaman?>').html('');
				$('#message-<?=$reqHalaman?>').attr('value', '');
				$('#message-<?=$reqHalaman?>').focus();			
			}
			else
			{
            lastTime = response.time;
            $('#daddy-shoutbox-response-<?=$reqHalaman?>').html('<img src="<?= base_url() ?>'+files+'images/accept.png" />');
            $('#message-<?=$reqHalaman?>').attr('value', '');
            $('#message-<?=$reqHalaman?>').focus();		
			}
          }
        }
        
        function validate(formData, jqForm, options) {
          for (var i=0; i < formData.length; i++) { 
              if (!formData[i].value) {
                  alert('Please fill in all the fields'); 
                  $('textarea[@name='+formData[i].name+']').css('background', 'red');
                  return false; 
              } 
          } 
          $('#daddy-shoutbox-response-<?=$reqHalaman?>').html('<img src="<?= base_url() ?>'+files+'images/loader.gif" />');
		  clearTimeout(parent.timeoutID);
        }
		
		
        function successGlobal(response, status)  { 
          if(status == 'success') {
			if(response.response == 'failed')
			{
				alert('Waktu aanwijzing telah usai.');
				$('#daddy-shoutbox-response').html('');
				$('#message').attr('value', '');
				$('#message').focus();		
			}
			else if(response.response == 'closed')
			{
				alert('Aanwijzing telah dipublish.');
				$('#daddy-shoutbox-response').html('');
				$('#message').attr('value', '');
				$('#message').focus();		
				
			}
			else
			{  
				lastTime = response.time;
				$('#daddy-shoutbox-response').html('<img src="<?= base_url() ?>'+files+'images/accept.png" />');
				$('#message').attr('value', '');
				$('#message').focus();		
			}
          }
        }
        
        function validateGlobal(formData, jqForm, options) {
          for (var i=0; i < formData.length; i++) { 
              if (!formData[i].value) {
                  alert('Please fill in all the fields'); 
                  $('input[@name='+formData[i].name+']').css('background', 'red');
                  return false; 
              } 
          } 
          $('#daddy-shoutbox-response').html('<img src="<?= base_url() ?>'+files+'images/loader.gif" />');
		  clearTimeout(parent.parent.timeoutMsgID);
        }
		
		function konfirmasiPesan()
		{
			if(confirm("Konfirmasi bahwa anda telah membaca dan memahami isi pada halaman <?=$reqHalaman?> ?"))
			{
				$.getJSON(files+"shoutbox_json/json/?reqId=<?=$reqId?>&reqKode=<?=$reqKode?>&reqHalaman=<?=$reqHalaman?>&action=confirm&time=0", function(json) {			 
					if(json.length) {
					  for(i=0; i < json.length; i++) {
						if(json[i].response == 'success')
						{
							alert('Konfirmasi berhasil.');
							return;
						}
					  }
					}
				});	
				
				$("#button-<?=$reqHalaman?>").css("visibility", "hidden");
			}
		}
		
		function openNotifikasi()
		{
			<?php
			if($this->USER_TYPE_ID == 6)
				$link = "aanwijzing_notifikasi_rekanan";
			else
				$link = "aanwijzing_notifikasi";			
			?>
			$("#content-inner").html('<iframe src="main/loadUrl/main/<?=$link?>/?reqId=<?=$reqId?>" id="frameNotifikasi"></iframe>');								
		}
	  $(document).ready(function() { 
		 var options = { 
			dataType:       'json',
			beforeSubmit:   validate,
			success:        success
		  }; 
		  $('#daddy-shoutbox-form-<?=$reqHalaman?>').ajaxForm(options);
		  
		 var optionsGlobal = { 
			dataType:       'json',
			beforeSubmit:   validateGlobal,
			success:        successGlobal
		  }; 
		  $('#daddy-shoutbox-form').ajaxForm(optionsGlobal);
	  });  
	  
		
	  function menujuHalaman(buku, halaman)
	  {
		  parent.menujuHalaman(buku, halaman);	
	  }	  
  </script>
 


<style>

	</style>

<!--<link rel="stylesheet" href="lib/aanwijzing/css/core.css" type="text/css">-->
<!-- <link rel="stylesheet" href="css/core-aanwijzing.css" type="text/css"> -->

    
  </head>

<body style="height:100%; min-height:100%;" class="area-aanwijzing-shoutbox">
    <!--<div class="container container-aps">
        <div class="row">
            <div class="col-md-12">-->
				
                <div class="tabs">
                
                    <div class="tab">
                       <input type="radio" id="tab-1" name="tab-group-1" checked>
                       <label for="tab-1">Tanya / Jawab per Halaman</label>
                       
                       <div class="content" style="overflow-x:hidden;">
                            <div id="bg-tab-header"></div>
                           <div id="daddy-shoutbox">
                                <!--<div style="margin-top:40px;"></div>-->
                                <div id="daddy-shoutbox-list-<?=$reqHalaman?>" style="display:inline-block; width:100%;"></div>
                                <div class="shoutbox-form">
                    
                                    <form id="daddy-shoutbox-form-<?=$reqHalaman?>" action="shoutbox_json/json/?action=add" method="post"> 
                                    <input type="hidden" name="nickname" value="<?=$nickname?>" readonly /> 
                                    <input type="hidden" name="reqId" value="<?=$reqId?>" readonly /> 
                                    <input type="hidden" name="reqHalaman" value="<?=$reqHalaman?>" readonly />
                                    <input type="hidden" name="reqKode" value="<?=$reqKode?>" readonly />
                                    
                                    <div class="message" style="margin-top: 5%">
                                    	<div class="nickname"><?=$nickname?> : </div>
                                    	<textarea style="width: 100%;" rows="5" name="message" id="message-<?=$reqHalaman?>"></textarea>
                                    </div>
                                    <div class="submit"><input class="btn btn-primary" type="submit" value="Kirim" /></div>
                                    
                                    <span id="daddy-shoutbox-response-<?=$reqHalaman?>"></span>
                                    </form>
                                </div>            
                              </div>
                       </div> 
                    </div>
                    
                    <div class="tab">
                       <input type="radio" id="tab-2" name="tab-group-1">
                       <label for="tab-2" style="left:195px;">Diskusi Umum</label>
                       <div class="content" style="overflow-x:hidden;">
                            <div id="bg-tab-header"></div>
                                <div id="daddy-shoutbox">
                    
                                    <div id="daddy-shoutbox-list-global" style="display:inline-block; width:100%;"></div>
                                    <div class="shoutbox-form">
                                        <form id="daddy-shoutbox-form" action="shoutbox_json/json/?action=add_global" method="post"> 
                                        <input type="hidden" name="nickname" value="<?=$nickname?>" readonly /> 
                                        <input type="hidden" name="reqId" value="<?=$reqId?>" readonly /> 
                                        <input type="hidden" name="reqHalaman" value="0" readonly />
                                        <input type="hidden" name="reqKode" value="0" readonly />
                                        
                                        <div class="message" style="margin-top: 5%">
                                        <div class="nickname"><?=$nickname?> : </div>
                                        	<textarea style="width: 100%;" rows="5" name="message" id="message"></textarea>
                                        </div>
                                        <div class="submit"><input class="btn" type="submit" value="Kirim" /></div>
                                        
                                        <span id="daddy-shoutbox-response"></span>
                                        </form>
                                    </div>            
                                </div>
                            </div>
                    </div>
                    
                    <div class="tab">
                       <input type="radio" id="tab-3" name="tab-group-1" onClick="openNotifikasi()">
                       <label for="tab-3" style="left:387px;">Notifikasi</label>
                        
                       <div class="content" style="top:35px !important;">
                            <div id="bg-tab-header"></div>
                            <div id="content-inner">
                            
                           </div>
                       </div> 
                    </div>
                    
                    </div>
                    
            <?php /*?></div>
        </div>

    </div><?php */?> <!-- /container -->
    
  </body>
</html>
