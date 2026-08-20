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
        	<div class="judul-halaman">Data Master user non rekanan</div>
            <div class="inner">
                <div class="area-konten">
                	
                    <div class="area-konten-inner">
                	
                    	<form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
							
                            <div class="judul-grup">Data Master user non rekanan</div>
                             <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Username</label>
                                        <div class="col-md-6">
                                          <input type="text" name="reqNamaUser2" style="width:200px" value="<?=$tmpNamaUser?>" disabled >
        								  <input type="hidden"   name="reqNamaUser" style="width:200px" value="<?=$tmpNamaUser?>" >
                                        </div>
                                    </div>
                                </div>
							</div>
                             <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Password baru</label>
                                        <div class="col-md-6">
                                          <input type="password" class="form-control easyui-validatebox" size="20" name="reqPassword" id="reqPassword" value="<?=$tmpUSER_PASSWORD?>" <?=$status?>>
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Ulangi password baru</label>
                                        <div class="col-md-6">
                                          <input type="password" class="form-control easyui-validatebox" size="20" name="reqPasswordRetype" id="reqPasswordRetype" value="<?=$tmpUSER_PASSWORD?>" <?=$status?>>
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Nama</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control easyui-validatebox" name="reqNama" style="width:200px"  value="<?=$tmpNama?>" title="Nama harus diisi" />
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Tipe User</label>
                                        <div class="col-md-8">
                                           <input type="hidden" name="reqTipePeserta" value="peserta">
            								<input type="hidden" name="reqTipe" value="22">
                                            <select id="idReqGroup" name="reqTipe">
                                                <option value="" > </option>
                                        	</select>
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Jabatan</label>
                                        <div class="col-md-8">
                                           	 <input type="text" name="reqJabatan" style="width:200px"  class="form-control easyui-validatebox" value="<?=$tmpJabatan?>" />
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Alamat</label>
                                        <div class="col-md-8">
                                           	 <textarea name="reqAlamat" cols="50" class="form-control easyui-validatebox" rows="5"><?=$tmpAlamat?></textarea>
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Telepon</label>
                                        <div class="col-md-8">
                                           	 <input type="text" name="reqTelepon" style="width:200px"  class="form-control easyui-validatebox" value="<?=$tmpTelepon?>" />
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Unit Kerja</label>
                                        <div class="col-md-8">
                                           	  <select name="reqUnitKerja" id="reqUnitKerja">
                                               <option value="">-- Pilih Unit Kerja --</option>
                                               <option value="" ></option>
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
                                            <a href="main/index/master_daftar_user_non_rekanan" class="btn btn-primary">Batal</a>
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

