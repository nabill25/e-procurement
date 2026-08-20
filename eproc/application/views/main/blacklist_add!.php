<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 ?>
 
<div class="row">
    <div class="col-md-12">
    	<div class="area-main">
        	<div class="judul-halaman">Tambah Master Blacklist</div>
            <div class="inner">
                <div class="area-konten">
                	
                    <div class="area-konten-inner">
                    	<form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
							
                            <div class="judul-grup">Tambah Master Blacklist</div>
                             <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Nama perusahaan</label>
                                        <div class="col-md-6">
                                         <select name="reqPerusahaan" id="reqPerusahaan">
                                            <option value="1" <? if($reqJenisPerusahaan == 1) echo 'selected'?>>PT</option>
                                            <option value="2" <? if($reqJenisPerusahaan == 2) echo 'selected'?>>CV</option>
                                            <option value="3" <? if($reqJenisPerusahaan == 3) echo 'selected'?>>Firma</option>
                                            <option value="4" <? if($reqJenisPerusahaan == 4) echo 'selected'?>>Koperasi</option>
                                            <option value="5" <? if($reqJenisPerusahaan == 5) echo 'selected'?>>UD</option>
                                            <option value="6" <? if($reqJenisPerusahaan == 6) echo 'selected'?>>Lain-lain</option>
                                            <!--<option value="PT">PT</option>
                                            <option value="CV">CV</option>
                                            <option value="Firma">Firma</option>
                                            <option value="Koperasi">Koperasi</option>
                                            <option value="UD">UD</option>
                                            <option value="Lain-lain">Lain-lain</option>-->
                                        </select>
                                        <input type="hidden" name="reqRekananId" id="reqRekananId" value="<?=$tempRekananId?>"/>
                                        <input type="text" style="width:250px" name="reqNama" id="reqNama" value="<?=$tempNama?>" title="Nama Perusahaan harus diisi" class="form-control easyui-validatebox"/>
                                        <a onClick="windowOpenerPopup(600,900,'blaklist rekanan','main/index/blaclist_rekanan_search');" ><img src="images/icn_search.gif" title="cari" /></a>
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Alamat</label>
                                        <div class="col-md-6">
                                         	<textarea name="reqAlamat" id="reqAlamat" title="Alamat harus diisi" class="form-control easyui-validatebox"><?=$tempAlamat;?></textarea>
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Kota</label>
                                        <div class="col-md-8">
                                           <input type="text" style="width:150px" name="reqKota" id="reqKota" value="<?=$tempKota?>" title="Kota harus diisi" class="form-control easyui-validatebox"/>
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">NPWP</label>
                                        <div class="col-md-8">
                                          <input type="text" style="width:150px" name="reqNPWP" id="reqNPWP" onkeydown="return format_npwp(event);" maxlength="20" value="<?=$tempNPWP?>" />
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Tanggal Mulai</label>
                                        <div class="col-md-8">
                                           	<input type="text" style="width:80px" name="reqTanggalMulai" id="reqTanggalMulai" value="<?=$tempTanggalMulai?>" title="Tanggal mulai harus diisi" class="form-control easyui-validatebox" />
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Tanggal Selesai</label>
                                        <div class="col-md-8">
                                           	 <input type="text" class="form-control easyui-datebox" style="width:80px" name="reqTanggalSelesai" id="reqTanggalSelesai" value="<?=$tempTanggalSelesai?>" title="Tanggal selesai harus diisi" />
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">No SK Blacklist</label>
                                        <div class="col-md-8">
                                           	<input type="text" style="width:150px" name="reqNoSk" value="<?=$tempNoSk?>" title="No sk harus diisi" class="form-control easyui-validatebox"/>
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Alasan</label>
                                        <div class="col-md-8">
                                           <textarea name="reqAlasan" class="form-control easyui-validatebox" title="Alasan harus diisi" ><?=$tempAlasan;?></textarea>
                                            <br>
                                            <input type="checkbox" id="reqTanggung" name="reqTanggung"/> Saya yakin bahwa data yang telah saya isi di atas benar.
                                        </div>
                                    </div>
                                </div>
							</div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-2 control-label">&nbsp;</label>
                                        <div class="col-md-4">
                                        	<button type="submit" class="btn btn-primary">Simpan</button>
                                            <a href="main/index/blacklist" class="btn btn-primary">Batal</a>
                                        </div>
                                    </div>
                                </div>
							</div>
                            
						</form>
                    
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

