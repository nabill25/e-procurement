<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->library("kauth");  $userLogin = new kauth(); 
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("PaketNegoisasi");

$reqPaketPenawaranId = httpFilterGet("reqPaketPenawaranId");

$paket_negoisasi = new PaketNegoisasi();
$paket_negoisasi->selectByParamsMonitoring(array("A.PAKET_PENAWARAN_ID" => $reqPaketPenawaranId));
$paket_negoisasi->firstRow();
$penawaranNegosiasi =  $paket_negoisasi->getField("UNIT_PRICE");													
$jumlahNegosiasi =  $paket_negoisasi->getField("TOTAL");
$setujui =  $paket_negoisasi->getField("SETUJUI");
$paketPenawaranId = $reqPaketPenawaranId;
?>

<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title><?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?></title>

<!-- Bootstrap Core CSS -->
<link href="lib/startbootstrap-sb-admin-2-1.0.7/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- MetisMenu CSS -->
<link href="lib/startbootstrap-sb-admin-2-1.0.7/bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

<!-- Timeline CSS -->
<link href="lib/startbootstrap-sb-admin-2-1.0.7/dist/css/timeline.css" rel="stylesheet">

<!-- Custom CSS -->
<link href="lib/startbootstrap-sb-admin-2-1.0.7/dist/css/sb-admin-2.css" rel="stylesheet">

<!-- Morris Charts CSS -->
<link href="lib/startbootstrap-sb-admin-2-1.0.7/bower_components/morrisjs/morris.css" rel="stylesheet">

<!-- Custom Fonts -->
<link href="lib/startbootstrap-sb-admin-2-1.0.7/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

<!-- EMODAL -->
<script src="lib/startbootstrap-sb-admin-2-1.0.7/bower_components/jquery/dist/jquery.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="lib/startbootstrap-sb-admin-2-1.0.7/bower_components/bootstrap/dist/js/bootstrap.js"></script>

<!-- Metis Menu Plugin JavaScript -->
<script src="lib/startbootstrap-sb-admin-2-1.0.7/bower_components/metisMenu/dist/metisMenu.min.js"></script>

<!-- Custom Theme JavaScript -->
<script src="lib/startbootstrap-sb-admin-2-1.0.7/dist/js/sb-admin-2.js"></script>

<style>
.chat
{
    list-style: none;
    margin: 0;
    padding: 0;
}

.chat li
{
    margin-bottom: 3px;
    padding-bottom: 3px;
    border-bottom: 1px dotted #B3A9A9;
}

.chat li.left .chat-body
{
    *margin-left: 60px;
	margin-left:0px;
}

.chat li.right .chat-body
{
    margin-right: 60px;
	*border:1px solid cyan;
}


.chat li .chat-body p
{
    margin: 0;
    color: #777777;
}

.panel .slidedown .glyphicon, .chat .glyphicon
{
    margin-right: 5px;
}

.panel-body
{
    overflow-y: scroll;
    height: 250px;
}

::-webkit-scrollbar-track
{
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
    background-color: #F5F5F5;
}

::-webkit-scrollbar
{
    width: 12px;
    background-color: #F5F5F5;
}

::-webkit-scrollbar-thumb
{
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
    background-color: #555;
}

.uraian{
	border:1px solid #e5e5e5;
	padding:10px;
	text-transform:uppercase;
	
	-webkit-border-top-left-radius: 4px;
	-webkit-border-top-right-radius: 4px;
	-moz-border-radius-topleft: 4px;
	-moz-border-radius-topright: 4px;
	border-top-left-radius: 4px;
	border-top-right-radius: 4px;
	
}
.unit-price{
	background:#f6f6f6;
	padding:10px;
	border:1px solid #e5e5e5;
	border-width:0px 1px 1px 1px;
	
	-webkit-border-bottom-right-radius: 4px;
	-webkit-border-bottom-left-radius: 4px;
	-moz-border-radius-bottomright: 4px;
	-moz-border-radius-bottomleft: 4px;
	border-bottom-right-radius: 4px;
	border-bottom-left-radius: 4px;
}
</style>

<style>
.col-md-12.area-negosiasi-chat{
	*border:1px solid red;
}
.triangle-isosceles.right .header{
	*background:pink;
}
.triangle-isosceles.right p{
	text-align:right;
	*background:yellow;
	*padding-right:0px;
	display:inline-block;
	width:100%;
	*margin-top:10px;
	
	float:right;
}
.col-md-12.area-negosiasi-chat .panel-footer{
	
}
.col-md-12.area-negosiasi-chat .panel-footer input.form-control.input-sm{
	width:calc(100% - 50px);
}
.col-md-12.area-negosiasi-chat .panel-footer span.input-group-btn{
	width:50px;
}
</style>

</head>

<body>
<div class="container-fluid">

	<div class="row" style="margin-bottom:18px; box-sizing:border-box;">
    	<div class="col-md-12 area-negosiasi-chat">
        	
            <div class="uraian"><?=$paket_negoisasi->getField("ITEM")?></div>
        	<?
            	$total = round($paket_negoisasi->getField("QUANTITY") * $paket_negoisasi->getField("UNIT_PRICE"), 2);
			?>
            <div class="unit-price">Rincian : <?=$paket_negoisasi->getField("QUANTITY")?> x <input type="text" name="reqUnitPrice" id="reqUnitPrice<?=$paketPenawaranId?>" value="<?=numberToIna($penawaranNegosiasi)?>"  OnFocus="FormatAngka('reqUnitPrice<?=$paketPenawaranId?>')" OnKeyUp="FormatUang('reqUnitPrice<?=$paketPenawaranId?>'); summaryDetil(); saveDetil('<?=$paketPenawaranId?>', 'reqUnitPrice<?=$paketPenawaranId?>', event); " OnBlur="FormatUang('reqUnitPrice<?=$paketPenawaranId?>')" style=" width:100px; text-align:right" <?  if($setujui == "") { ?>  <? } else { ?> disabled <? } ?>> = <label id="reqTotalNegosiasi" style="font-weight:normal; font-size:14px"><?=numberToIna($total)?></label>
            <br>
            <div style="font-size:10px">Tekan enter setelah mengubah harga.</div>
            </div>
        	
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 area-negosiasi-chat">
                <div class="panel panel-primary">
                    <div class="panel-heading" id="accordion">
                        <span class="glyphicon glyphicon-comment"></span> Chat
                    </div>
                <div>
                <div class="panel-body area-negosiasi-chat">
					<script type="text/javascript">
                        var count = 0;
                        var files = 'lib/shoutbox2/';
                        var lastTime = 0;
                        
                        function prepare(response) {
                          count++;
                          var d = new Date();
                          var mytime = d.getDate() +'/'+(Number(d.getMonth())+1)+'/'+d.getFullYear()+ ' ' + d.getHours()+':'+d.getMinutes()+':'+d.getSeconds();								 
                          var string = '<div class="shoutbox-list" id="list-'+count+'">'
                              + '<span class="shoutbox-list-time">'+mytime+'</span>'
                              + '<span class="shoutbox-list-nick">'+response.nickname+':</span>'
                              + '<span class="shoutbox-list-message">'+response.message+'</span>'
                              +'</div>';
                              
                          if(response.nickname == 'Panitia')
                          {
                              var string = '<li class="left clearfix">'
                                  + '<div class="chat-body clearfix">'
                                  + '<div class="triangle-isosceles left">'
                                  + '<div class="header"><strong class="primary-font">'+response.nickname+'</strong> <small class="pull-right text-muted">'
                                  + '<i class="fa fa-clock-o" aria-hidden="true"></i>'+mytime+'</small>'
                                  + '</div>'
                                  + '<p>'
                                  + ''+response.message+'</p>'
                                  + '</p>'
                                  + '</div>'
                                  + '</div>'
                                  + '</li>';										  
							  
                          }
                          else
                          {
                              var string = '<li class="left clearfix">'
                                  + '<div class="chat-body clearfix">'
                                  + '<div class="triangle-isosceles left">'
                                  + '<div class="header"><strong class="primary-font">'+response.nickname+'</strong> <small class="pull-right text-muted">'
                                  + '<i class="fa fa-clock-o" aria-hidden="true"></i>'+mytime+'</small>'
                                  + '</div>'
                                  + '<p>'
                                  + ''+response.message+'</p>'
                                  + '</p>'
                                  + '</div>'
                                  + '</div>'
                                  + '</li>';										  
                          }						  
                          return string;
                        }
                    
                    
                        function success(response, status)  { 
                          if(status == 'success') {
                            lastTime = response.time;
                            $('#daddy-shoutbox-response').html('<img src="'+files+'images/accept.png" />');
                            $('.chat').prepend(prepare(response));
                            $('#btn-input').val('');
                            $('#btn-input').focus();										
                            $('#list-'+count).fadeIn('slow');
                            timeoutID = setTimeout(refresh, 3000);
                          }
                        }
                        
                        function validate(formData, jqForm, options) {
                          if(($("#btn-input").val()) == '')
                          {
                            alert('Isi pesan terlebih dahulu.'); 
                            return false;
                          }
                          
                          $('#daddy-shoutbox-response').html('<img src="'+files+'images/loader.gif" />');
                          clearTimeout(timeoutID);
                        }
                    
                        function refresh() {
                          $.getJSON("nego_shoutbox_json/nego_shoutbox/?reqPaketPenawaranId=<?=$reqPaketPenawaranId?>&action=view&time="+lastTime, function(json) {
                            if(json.length) {
                              for(i=0; i < json.length; i++) {
                                $('.kolomchatting<?=$reqPaketPenawaranId?>').prepend(prepare(json[i]));
                              }
                              var j = i-1;
                              lastTime = json[j].time;
                            }
                            //alert(lastTime);
                          });
						  
									
						  $.getJSON("json/paket_negosiasi/ambil_negosiasi/?reqId=<?=$reqPaketPenawaranId?>", function(json) {
							  	if(json.SETUJUI == "1")
								{
									$("#setujui<?=$reqPaketPenawaranId?>").show();
									$("#reqUnitPrice<?=$reqPaketPenawaranId?>").attr('disabled', true);
									$("#reqUnitPriceNegosiasi<?=$reqPaketPenawaranId?>").attr('disabled', true);
								}
						  });
								  						  
                          timeoutID = setTimeout(refresh, 3000);
                        }
                        
                        // wait for the DOM to be loaded 
                        $(document).ready(function() { 
                            var options = { 
                              dataType:       'json',
                              beforeSubmit:   validate,
                              success:        success
                            }; 
                            $('#daddy-shoutbox-form').ajaxForm(options);
                            timeoutID = setTimeout(refresh, 100);
                        });

					function summaryDetil()
					{
							var txtQuantity = Number('<?=$paket_negoisasi->getField("QUANTITY")?>');
							var txtHarga = Number(FormatAngkaNumber($("#reqUnitPrice<?=$paketPenawaranId?>").val()));
							var jumlah = (txtHarga * txtQuantity).toFixed(2);
							$("#reqTotalNegosiasi").text(FormatCurrencyBaru(jumlah));
					}

					function saveDetil(paketPenawaranId, inputId, event)
					{
						if(event.keyCode == "13")
						{
							if(confirm("Ubah data negosiasi rincian terpilih ?"))
							{
								var nilai = $("#"+inputId).val();
								
								$("#reqUnitPriceNegosiasi<?=$paketPenawaranId?>").val(nilai);
								summary();
								
								$.getJSON("json/paket_negosiasi/negosiasi/?reqId="+paketPenawaranId+"&reqNilai="+nilai, function(json) {
									alert(json);
								});		
								
							}	
						}
						
					}						
                    </script>                    
                    <ul class="chat kolomchatting<?=$reqPaketPenawaranId?>">                       
                    </ul>
                </div>
                <div class="panel-footer">
                    <div class="input-group" style="width:100%;">
                        <form id="daddy-shoutbox-form" action="nego_shoutbox_json/nego_shoutbox/?action=add" method="post" style="width:100%;">
                        	<input type="hidden" name="nickname" value="Panitia" />
                            <input type="hidden" name="reqPaketPenawaranId" value="<?=$reqPaketPenawaranId?>" readonly /> 
                            <input type="hidden" name="reqHalaman" value="0" readonly />
                            <input type="hidden" name="reqKode" value="0" readonly />
                                                
                        	<input id="btn-input" type="text" name="message" class="form-control input-sm" placeholder="Type your message here..." />
                            <span class="input-group-btn">
                                <input type="submit" value="Submit"  style="display:none" />
                                <button class="btn btn-warning btn-sm" id="btn-chat">Send</button>
                            </span>
                        </form>  
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

  	<script type="text/javascript" src="lib/shoutbox2/javascript/jquery.js"></script>
  	<script type="text/javascript" src="lib/shoutbox2/javascript/jquery.form.js"></script>
    
</body>
</html>