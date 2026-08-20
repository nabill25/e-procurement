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
        	<div class="judul-halaman">Master Maintenance PO Number</div>
            <div class="inner">
            	<div class="area-sidelook"></div>
                <div class="area-konten">
                	
                    <div class="area-konten-inner">
                		
                        <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
                            <div class="judul-grup">Maintenance PO Number</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<div class="col-md-3">
                                     		<input type="text" name="reqInputCari" id="reqInputCari" class="form-control"/>
                                        </div>
										<div class="col-md-3">
                                      		<input type="submit" name="reqCari" id="reqCari" value="Cari" class="btn-cari" />  
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <table width="100%" border="0" cellpadding="2" cellspacing="1" id="tbl_bidang">
                                            <tbody>
                                                <tr class="judul-kolom">
                                                <th>PR Group Number</th>
                                                <th>PR Number</th>
                                                <th>Kontrak Ke</th> 
                                                <th>PO Number</th>                                                </tr>           
                                            <tr >
                                            <td>1.</td>
                                            <td>ADMIN </td>
                                            <td>1</td>
                                            <td>1</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="inputEmail" class="col-md-2 control-label">&nbsp;</label>
                                    <div class="col-md-4">
                                        <a href="main/index/" class="btn btn-primary">Reset PO Number</a>
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
