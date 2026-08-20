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
        	<div class="judul-halaman">Tambah Paket Lelang</div>
            <div class="inner">
                <div class="area-konten">
                	
                    <div class="area-konten-inner">
                    	<form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
							
                            <div class="judul-grup">Tambah Paket Lelang</div>
                             <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Nama Paket</label>
                                        <div class="col-md-6">
                                         <input type="text" name="reqNamaPaket" title="Nama paket harus diisi" class="form-control easyui-validatebox"  value="<?=$tempNamaPaket?>" style="width:700px" />
                                        </div>
                                    </div>
                                </div>
							</div>
                             <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Bidang Usaha</label>
                                        <div class="col-md-6">
                                          <table width="100%" border="0" cellpadding="2" cellspacing="1" id="tbl_bidang">
                                              <tbody>
                                                <tr class="judul-kolom">
                                                  <th>No</th>
                                                  <th>Bidang usaha</th>
                                                </tr>
                                                                  
                                                <tr class="gelap">
                                                  <td>1.</td>
                                                  <td>PENGADAAN BARANG dan JASA | Alat/peralatan/suku cadang komputer.</td>
                                                </tr>   
                                              </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Uraian Kegiatan</label>
                                        <div class="col-md-6">
                                         	<textarea id="idGuestBookIsi" name="reqUraianKegiatan" style="width:100%; height:100%"><?=$tempUraianKegiatan?></textarea>
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Nilai Pekerjaan</label>
                                        <div class="col-md-8">
                                           <input title="Nilai pekerjaan harus diisi" class="form-control easyui-validatebox"  name="reqNilaiPekerjaan" type="text" id="reqNilaiPekerjaan" style="width:400px" value="<?=$tempNilaiPekerjaan?>"  OnFocus="FormatAngka('reqNilaiPekerjaan')" OnKeyUp="FormatUang('reqNilaiPekerjaan')" OnBlur="FormatUang('reqNilaiPekerjaan')" />
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Lokasi Pekerjaan</label>
                                        <div class="col-md-8">
                                          <input title="Lokasi pekerjaan harus diisi" class="form-control easyui-validatebox"  name="reqLokasiPekerjaan" type="text" id="reqLokasiPekerjaan" style="width:400px" value="<?=$tempLokasiPekerjaan?>" />
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Jenis Pekerjaan</label>
                                        <div class="col-md-8">
                                           	<select name="reqJenisPekerjaan" id="reqJenisPekerjaan" title="Jenis pekerjaan harus diisi" style="width:180px" class="form-control easyui-validatebox"  >
                                                   <option value="">-- pilih jenis pekerjaan --</option>
													<option value=""></option>
												 
												</select>
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Metode Pengadaan</label>
                                        <div class="col-md-8">
                                           <select name="reqMetodePengadaan" id="reqMetodePengadaan" style="width:180px" title="Metode pengadaan harus diisi" class="form-control easyui-validatebox"  >
                                                    <option value=""></option>
                                            </select> 
                                            
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Metode Kualifikasi</label>
                                        <div class="col-md-8">
                                           <select name="reqMetodeKualifikasi" id="reqMetodeKualifikasi" style="width:180px" title="Metode kualifikasi harus diisi" class="form-control easyui-validatebox"  >
										  
                                                <option value=""</option>
                                             
                                          </select>
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Metode Evaluasi</label>
                                        <div class="col-md-8">
                                           <select name="reqMetodeEvaluasi" id="reqMetodeEvaluasi" style="width:180px" title="Metode evaluasi harus diisi" class="form-control easyui-validatebox"  >
                                             
                                                    <option value="">  'xxxx'</option>
                                                 
                                              
                                            </select>
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Kualifikasi Rekanan</label>
                                        <div class="col-md-8">
                                        	 <label>
                                                <input type="radio" name="reqKualifikasiRekanan" title="Kualifikasi rekanan salah satu harus diisi" class="form-control easyui-validatebox"  
                                                       value="" id=""   checked="checked" />
                                                
                                            </label>
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Unit Kerja</label>
                                        <div class="col-md-8">
                                        	 <select name="reqUnitKerja" style="width:180px" id="reqUnitKerja" title="" class="form-control easyui-validatebox"  >
                                                    <option value="">  'xxxx'</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Bahasa</label>
                                        <div class="col-md-8">
                                        	<select name="reqBahasa" style="width:180px" id="reqBahasa" title="Bahasa harus diisi" class="form-control easyui-validatebox"  >
                                                <option value="ID" >Indonesia</option>
                                                <option value="EN">Bilingual</option>	
                                              </select>
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Alamat Panitia</label>
                                        <div class="col-md-8">
                                        	<textarea name="reqAlamatPanitia" style="width:100%; height:100%"><?=$tempAlamatPanitia?></textarea>
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label harus-diisi">Telp. Panitia</label>
                                        <div class="col-md-1">
                                          <input name="reqTelpPanitiaKode" class="form-control easyui-validatebox" type="text" id="reqTelpPanitiaKode" style="width:50px" value="<?=$tempTelpPanitiaKode?>" />
										</div>
                                        <div class="col-md-4">
                                         <input name="reqTelpPanitia" type="text" id="reqTelpPanitia" style="width:100px" value="<?=$tempTelpPanitia?>" />
                                        </div>
                                        <div class="col-md-9 col-md-push-3">
                                        	
                                        </div>
                                    </div>
                                </div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-3 control-label">Email Panitia</label>
                                        <div class="col-md-8">
                                        	<input name="reqEmailPanitia" type="text" id="reqEmailPanitia" class="form-control easyui-validatebox" style="width:400px" value="<?=$tempEmailPanitia?>" />
                                        </div>
                                    </div>
                                </div>
							</div>
                            <?php /*?><div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-2 control-label">&nbsp;</label>
                                        <div class="col-md-4">
                                        	<button type="submit" class="btn btn-primary">Simpan</button>
                                            <a href="main/index/sk_panitia" class="btn btn-primary">Batal</a>
                                        </div>
                                    </div>
                                </div>
							</div><?php */?>
                            
						</form>
                    
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

