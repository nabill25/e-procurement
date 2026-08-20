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
        	<div class="judul-halaman">Tambah Master SK Panitia</div>
            <div class="inner">
                <div class="area-konten">
                	
                    <div class="area-konten-inner">
                    	<form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
							
                            <div class="judul-grup">Tambah Master SK Panitia</div>
                             <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Username</label>
                                        <div class="col-md-6">
                                         <select name="reqUnitKerja">
                                            <option value="" ></option>
                                        </select>
                                        </div>
                                    </div>
                                </div>
							</div>
                             <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Nomor SK</label>
                                        <div class="col-md-6">
                                          <input type="text" name="reqNomor" value="<?=$tempNomor?>" title="No SK harus diisi" class="form-control easyui-validatebox">
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Tanggal Mulai SK</label>
                                        <div class="col-md-6">
                                         	<input type="text" class="form-control easyui-validatebox" style="width:80px" name="reqTanggalSK" id="reqTanggalSK" value="<?=$tempTanggalSK?>" />
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Pejabat Penandatangan SK</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control easyui-validatebox" name="reqPejabat" value="<?=$tempPejabat?>">
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">NIP Pejabat</label>
                                        <div class="col-md-8">
                                          <input type="text" style="width:250px" class="form-control easyui-validatebox" name="reqNIPPejabat" value="<?=$tempNIPPejabat?>">
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Tanggal SK</label>
                                        <div class="col-md-8">
                                           	<input type="text" class="form-control easyui-validatebox" style="width:80px" name="reqTanggalMulaiSK" id="reqTanggalMulaiSK" value="<?=$tempTanggalMulaiSK?>" />
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Tanggal Selesai SK</label>
                                        <div class="col-md-8">
                                           	 <input type="text" class="form-control easyui-validatebox" style="width:80px" name="reqTanggalSelesaiSK" id="reqTanggalSelesaiSK" value="<?=$tempTanggalSelesaiSK?>" />
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Status</label>
                                        <div class="col-md-8">
                                           	<select name="reqStatus">
                                                <option value="1">aktif</option>
                                            </select>
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
                                            <a href="main/index/sk_panitia" class="btn btn-primary">Batal</a>
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

