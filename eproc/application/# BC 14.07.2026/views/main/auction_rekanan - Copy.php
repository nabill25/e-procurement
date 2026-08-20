<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
?>
<script type="text/javascript">
$(document).ready(function() {
	
	$(function(){
		$('#ff').form({
			url:'paket_json/add',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				//$.messager.alert('Info', data, 'info');	
				document.location.href = 'main/index/paket_lelang_tambah_jadwal/?reqId='+data;
			}
		});
		
	});
	
});
</script>

<div class="row">
    <div class="col-md-12">
    	<div class="area-main">
        	<div class="judul-halaman">Auction Rekanan</div>
            <div class="inner">
                <div class="area-konten">
                	
                    <div class="area-konten-inner" style="clear:both; float:left; width:100%;">
                    
                    	<div class="area-auction">
                        
                        	<div class="jam-jumlah">
                            	<div class="jam"><i class="fa fa-clock-o" aria-hidden="true"></i> 14 : 06 : 27</div>
                                <div class="area-jumlah">
                                	<div class="nilai">987</div>
                                    <div class="keterangan">jumlah peserta</div>
                                </div>
                            </div>
                            
                            <div class="penawaran">
                            	<div class="judul">Penawaran Anda</div>
                                <input type="text" placeholder="ketikkan penawaran...">
                                <button>Submit</button>
                            </div>
                            
                            <div class="harga">
                            	<div class="judul">Harga Terendah</div>
                                <div class="nilai">
                                	<div class="mata-uang">Rp</div>
                                	<div class="nominal">1.000.000</div>
                                    <div class="keterangan">
                                    	<div class="keterangan">Ikon kunci menunjukkan bahwa harga penawaran Anda adalah yang terendah *</div>
                                        <div class="ikon"><i class="fa fa-key" aria-hidden="true"></i></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="area-chatting">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="panel panel-primary">
                                                <div class="panel-heading" id="accordion">
                                                    <i class="fa fa-comments" aria-hidden="true"></i> Chat
                                                </div>
                            
                                                <div class="panel-body">
                                                    <ul class="chat">
                                                        <li class="left clearfix">
                                                            <div class="chat-body clearfix">
                                                                <div class="waktu"><i class="fa fa-clock-o" aria-hidden="true"></i> 6 Juli 2017, 11:58</div>
                                                                <div class="data">
                                                                    <span class="nama">Jack Sparrow aaaa</span>
                                                                    <span class="isi">Lorem ipsum dolor sit amet, consectetur adipiscing elit</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="left clearfix">
                                                            <div class="chat-body clearfix">
                                                                <div class="waktu"><i class="fa fa-clock-o" aria-hidden="true"></i> 6 Juli 2017, 11:58</div>
                                                                <div class="data">
                                                                    <span class="nama">Jack Sparrow aaaa</span>
                                                                    <span class="isi">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales quis ullamcorper ligula sodales.</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="left clearfix">
                                                            <div class="chat-body clearfix">
                                                                <div class="waktu"><i class="fa fa-clock-o" aria-hidden="true"></i> 6 Juli 2017, 11:58</div>
                                                                <div class="data">
                                                                    <span class="nama">Jack Sparrow aaaa</span>
                                                                    <span class="isi">Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales.</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="left clearfix">
                                                            <div class="chat-body clearfix">
                                                                <div class="waktu"><i class="fa fa-clock-o" aria-hidden="true"></i> 6 Juli 2017, 11:58</div>
                                                                <div class="data">
                                                                    <span class="nama">Jack Sparrow aaaa</span>
                                                                    <span class="isi">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales consectetur adipiscing elit consectetur adipiscing elit.</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="left clearfix">
                                                            <div class="chat-body clearfix">
                                                                <div class="waktu"><i class="fa fa-clock-o" aria-hidden="true"></i> 6 Juli 2017, 11:58</div>
                                                                <div class="data">
                                                                    <span class="nama">Jack Sparrow aaaa</span>
                                                                    <span class="isi">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales.</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="left clearfix">
                                                            <div class="chat-body clearfix">
                                                                <div class="waktu"><i class="fa fa-clock-o" aria-hidden="true"></i> 6 Juli 2017, 11:58</div>
                                                                <div class="data">
                                                                    <span class="nama">Jack Sparrow aaaa</span>
                                                                    <span class="isi">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales.</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="left clearfix">
                                                            <div class="chat-body clearfix">
                                                                <div class="waktu"><i class="fa fa-clock-o" aria-hidden="true"></i> 6 Juli 2017, 11:58</div>
                                                                <div class="data">
                                                                    <span class="nama">Jack Sparrow aaaa</span>
                                                                    <span class="isi">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales.</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="left clearfix">
                                                            <div class="chat-body clearfix">
                                                                <div class="waktu"><i class="fa fa-clock-o" aria-hidden="true"></i> 6 Juli 2017, 11:58</div>
                                                                <div class="data">
                                                                    <span class="nama">Jack Sparrow aaaa</span>
                                                                    <span class="isi">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales.</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="left clearfix">
                                                            <div class="chat-body clearfix">
                                                                <div class="waktu"><i class="fa fa-clock-o" aria-hidden="true"></i> 6 Juli 2017, 11:58</div>
                                                                <div class="data">
                                                                    <span class="nama">Jack Sparrow aaaa</span>
                                                                    <span class="isi">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales.</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="left clearfix">
                                                            <div class="chat-body clearfix">
                                                                <div class="waktu"><i class="fa fa-clock-o" aria-hidden="true"></i> 6 Juli 2017, 11:58</div>
                                                                <div class="data">
                                                                    <span class="nama">Jack Sparrow aaaa</span>
                                                                    <span class="isi">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales.</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="panel-footer">
                                                    <div class="input-group">
                                                        <input id="btn-input" type="text" class="form-control input-sm" placeholder="Type your message here..." />
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-warning btn-sm" id="btn-chat">Send</button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                
                                </div>
                            </div>

                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

