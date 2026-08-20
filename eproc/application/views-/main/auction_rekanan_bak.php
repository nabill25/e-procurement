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
                	
                    <div class="area-konten-inner">
                    	<?php /*?><form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
							
                            <!--<div class="judul-grup">Tambah Paket Lelang</div>-->
                             <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label harus-diisi">Nama Paket</label>
                                        <div class="col-md-5">
                                         <input type="text" name="reqNamaPaket" title="Nama paket harus diisi" class="form-control easyui-validatebox"  value="" required />
                                        </div>
                                    </div>
                                </div>
							</div>
                            
						</form><?php */?>
						
                        <div class="area-auction">
                        	<div class="area-data">
                            	<div class="waktu-peserta">
                                	
                                    <div class="list">
                                    	<span>Waktu</span>
                                        <span><input type="text"></span>
                                    </div>
                                    <div class="list">
                                    	<span>Jumlah Peserta</span>
                                        <span><input type="text"></span>
                                    </div>
                                    
                                </div>
                                <div class="harga-terendah">
                                    <div class="judul">Harga Terendah</div>
                                    <div class="data">
                                    	<div class="nilai">1.000.000</div>
                                        <div class="ikon"><i class="fa fa-key" aria-hidden="true"></i></div>
                                    </div>
                                </div>
                                
                            </div>
                            
                            <div class="area-penawaran-anda">
                            	<span>Penawaran Anda</span>
                                <span><input type="text"></span>
                                <span class="keterangan">Ikon kunci menunjukkan bahwa harga penawaran Anda adalah yang terendah <font style="color:#da1a1a;">*</font></span>
                            </div>
                            
                        	<div class="area-chatting" style="display:none;">
                            	<div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="panel panel-primary">
                                                <div class="panel-heading" id="accordion">
                                                    <i class="fa fa-comments" aria-hidden="true"></i> Chat
                                                </div>
                                            <!--<div class="panel-collapse collapse" id="collapseOne">
                                            <div class="panel-collapse" id="collapseOne">-->
                                                <div class="panel-body">
                                                    <ul class="chat">
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
                                                            <button class="btn btn-warning btn-sm" id="btn-chat">
                                                                Send</button>
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

