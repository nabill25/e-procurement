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

$reqPaketPenawaranId = $this->input->get("reqPaketPenawaranId");

$paket_negoisasi = new PaketNegoisasi();
$paket_negoisasi->selectByParamsMonitoring(array("A.PAKET_PENAWARAN_ID" => $reqPaketPenawaranId));
$paket_negoisasi->firstRow();
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
	margin-left: 0px;
}

.chat li.right .chat-body
{
    margin-right: 60px;
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

/** button setuju **/
.setuju{
	width:100%;
	height:84px; 
	
	background:#337ab7; 
	border:1px solid #296da7;
	text-transform:uppercase;
	text-align:center;
	color:#FFF;
	
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
	
	display:table;
}
.setuju span{
	display:table-cell;
	vertical-align:middle;
}

/** rincian **/
.unit-price, .unit-price label{
	font-size:13px !important;
}
</style>

<!-- JQUERY CONFIRM -->
<link href="lib/confirm/index_files/bootstrap-responsive.css" rel="stylesheet">

</head>

<body>
<div class="container-fluid">

	<div class="row" style="margin-bottom:18px;">
    	<div class="col-md-9" style="padding-right:0px;">
        	
            <div class="uraian"><?=$paket_negoisasi->getField("ITEM")?></div>
        	<?
            	$total = round($paket_negoisasi->getField("QUANTITY") * $paket_negoisasi->getField("UNIT_PRICE"), 2);
			?>
            <div class="unit-price">Rincian : <span><?=$paket_negoisasi->getField("QUANTITY")?></span> x <span id="price"><?=numberToIna($paket_negoisasi->getField("UNIT_PRICE"))?></span> = <span id="total"><?=numberToIna($total)?></span></div>
        	
        </div>
        <div class="col-md-3" style="text-align:right;">

        <?
        if($paket_negoisasi->getField("SETUJUI") == "1")
			echo "<div class='setuju'><span>Anda<br>telah<br>menyetujui</span></div>";
		else
		{
		?>
        	<button class="btn btn-success" data-toggle="confirmation" type="button" style="height:80px !important; width:100% !important;">Setuju</button>	
        <?
		}
		?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 area-negosiasi-chat">
            <div class="panel panel-primary">
                <div class="panel-heading" id="accordion">
                    <span class="glyphicon glyphicon-comment"></span> Chat
                </div>
            <div>
                <div class="panel-body">
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
                                      
                                  if(response.nickname == '<?=$userLogin->userNamaRekanan?>')
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
								  
								  $.getJSON("paket_negoisasi_json/ambil_negosiasi/?reqId=<?=$reqPaketPenawaranId?>", function(json) {
                                    	$("#price").text(json.UNIT_PRICE);
                                    	$("#total").text(json.TOTAL);
										$("#reqUnitPriceNegosiasi<?=$reqPaketPenawaranId?>").val(json.UNIT_PRICE);
										$("#reqJumlahNegosiasi<?=$reqPaketPenawaranId?>").val(json.TOTAL);
										top.summary();
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
								
								function setujui() 
								{
								  $.getJSON("paket_negoisasi_json/setujui/?reqId=<?=$reqPaketPenawaranId?>", function(json) {
                                     
                                  });				
								  $("#setujui<?=$reqPaketPenawaranId?>").show();						
								}
								
                          </script>                    
                    <ul class="chat kolomchatting<?=$reqPaketPenawaranId?>">                       
                    </ul>
                </div>
                <div class="panel-footer">
                    <div class="input-group" style="width:100%;">
                        <form id="daddy-shoutbox-form" action="nego_shoutbox_json/nego_shoutbox/?action=add" method="post" style="width:100%;">
                        	<input type="hidden" name="nickname" value="<?=$userLogin->userNamaRekanan?>" />
                            <input type="hidden" name="reqPaketPenawaranId" value="<?=$reqPaketPenawaranId?>" readonly /> 
                            <input type="hidden" name="reqHalaman" value="0" readonly />
                            <input type="hidden" name="reqKode" value="0" readonly />
                                                
                            <input id="btn-input" type="text" name="message" class="form-control input-sm" placeholder="Type your message here..." />
                            <span class="input-group-btn">
                                <input type="submit" value="Submit"  style="display:none" />
                                <button class="btn btn-warning btn-sm" id="btn-chat">
                                    Send</button>
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
    
	<!-- JQUERY CONFIRM -->
    <script src="lib/confirm/index_files/bootstrap-transition.js"></script>
    <script src="lib/confirm/index_files/bootstrap-tooltip.js"></script>


    <script src="lib/confirm/index_files/application.js"></script>

    <script src="lib/confirm/index_files/bootstrap-confirmation.js"></script>
    <script>
	$.fn.confirmation.defaults = $.extend({} , $.fn.tooltip.defaults, {
		placement: 'top'
		, trigger: 'click'
		, target : '_self'
		, title: 'Setujui?'
		, template: '<div class="popover">' +
				'<div class="arrow"></div>' +
				'<h3 class="popover-title"></h3>' +
				'<div class="popover-content text-center">' +
				'<div class="btn-group">' +
				'<a class="btn btn-small" data-dismiss="modal"></a>' +
				'<a class="btn btn-small" data-dismiss="confirmation"></a>' +
				'</div>' +
				'</div>' +
				'</div>'
		, btnOkClass:  'x btn-primary'
		, btnCancelClass:  ''
		, btnOkLabel: '<i class="icon-ok-sign icon-white"></i> Ya'
		, btnCancelLabel: '<i class="icon-remove-sign"></i> Batal'
		, singleton: false
		, popout: false
		, onConfirm: function(){
			setujui();
			}
		, onCancel: function(){}
	})

	</script>

    
</body>
</html>