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
        	<div class="judul-halaman">Tambah PIC</div>
            <div class="inner">
                <div class="area-konten">
                	
                    <div class="area-konten-inner">
                    	<form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
							
                            <div class="judul-grup">Tambah PIC</div>
                             <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Unit Kerja</label>
                                        <div class="col-md-6">
                                         <?=$reqNama?>
                                        </div>
                                    </div>
                                </div>
							</div>
                             <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                     <table id="tbl_bidang">
                                       <tbody>
                                            <tr class="judul-kolom">
                                              <th>NRP</th>
                                              <th>Nama Pegawai</th>
                                              <th>Aksi</th>
                                            </tr>           
                                            <tr >
                                              <td>1.</td>
                                              <td>2</td>
                                              <td>3.</td>
                                            </tr>
                                          </tbody>
                                        </table>
                                      <div class="col-md-8">
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
                                            <a href="main/index/pic" class="btn btn-primary">Batal</a>
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

