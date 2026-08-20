<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

include_once("functions/default.func.php");
include_once("functions/date.func.php");
include_once("functions/string.func.php");

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->library("Pagination");
$this->load->model("Paket");
$this->load->model("PaketRekanan");


$paket = new Paket();

$showRecord = 10;
$pageView = "paket_json/paket_lelang/";


if($this->USER_TYPE_ID == 6)
{
	$arrStatement = array("JENIS_PENGADAAN" => "LELANG");	
	$rowCount = $paket->getCountByParamsPaketRekanan($arrStatement, $this->REKANAN_ID);
	$paket->selectByParamsPaketRekanan($arrStatement, $showRecord, 0, $this->REKANAN_ID);  
}
elseif($this->USER_TYPE_ID == 8 || $this->USER_TYPE_ID == 9 || $this->USER_TYPE_ID == 10)
{
	$arrStatement = array("JENIS_PENGADAAN" => "LELANG");	
	$rowCount = $paket->getCountByParamsPaketFungsional($arrStatement, $this->USER_LOGIN_ID);
	$paket->selectByParamsPaketFungsional($arrStatement, $showRecord, 0, $this->USER_LOGIN_ID);  
}
else
{
	if((int)$this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 7 || $this->USER_TYPE_ID == 10)
		$arrStatement = array("JENIS_PENGADAAN" => "LELANG");
	else
		$arrStatement = array("JENIS_PENGADAAN" => "LELANG", "PUBLISH_PAKET" => "1");
	
	$rowCount = $paket->getCountByParams($arrStatement, $this->REKANAN_ID);
	$paket->selectByParamsMonitoring($arrStatement, $showRecord, 0);  

}

$arrSerialized = serialize($arrStatement);	
$arrSerialized = str_replace('"', '@', $arrSerialized);		
$pagConfig = array('baseURL'=>$pageView, 'showRecord' => $showRecord, 'totalRows'=>$rowCount, 'perPage'=>$showRecord, 'contentDiv'=>'area-paket-lelang', 'arrSerialized' => $arrSerialized, 'searchVarible' => "reqPencarian");
$pagination =  new Pagination($pagConfig);					

?>
<div class="row">
    <div class="col-md-12">
    	<div class="area-main">
        	<div class="judul-halaman">Paket Lelang</div>
            <div class="inner">
            	<div class="area-sidelook"></div>
                <div class="area-konten">
                	
                    <div class="area-konten-inner">
                		
                        <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
                            <div class="judul-grup">Paket Lelang</div>
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
                                            <thead>
                                                <tr class="judul-kolom">
                                                <th>Tanggal</th>
                                                <th>Lokasi</th>
                                                <th>Nama</th> 
                                                <th>Bidang/Sub Bidang</th>
												<?
                                                if((int)$this->USER_TYPE_ID == 3)
                                                {
                                                ?>
                                                <th>Publish</th>
                                                <?
                                                }
                                                ?>
                                                <?
                                                if($this->USER_TYPE_ID == 6){
                                                ?>
                                                <th><?=translate("Aksi", "Action")?></th>
                                                <?
                                                }
                                                ?>
                                                </tr>
                                            </thead>
                                            <tbody> 
                                            <?
                                            while($paket->nextRow())
											{
												if(trim($paket->getField("ALASAN")) == "")
												  $batal = 0;
												else
												  $batal = 1;													
											  ?>          
												<tr>
                                                    <td><?=getFormattedDate($paket->getField("TANGGAL_TAHAP"))?></td>
                                                    <td><?=$paket->getField("LOKASI")?></td>
                                                    <td>
                                                        <a href="main/index/paket_detil/?reqId=<?=$paket->getField("PAKET_ID")?>"><?=strtoupper($paket->getField("NAMA"))?></a>
                                                        <? if($batal == 1) { ?><font color="#FF0000" style="font-size:13px">(PAKET DIBATALKAN / DIULANG)</font><? } ?>                                                
			
														
														<?
														/* STATUS PENDAFTARAN REKANAN */
                                                        if($this->USER_TYPE_ID == "6")
                                                        {
                                                            $paket_mengikuti1 = new Paket();
                                                            $mengikuti = $paket_mengikuti1->getPaketMengikuti($this->REKANAN_ID, $paket->getField("PAKET_ID"));
                                                            $pendaftaran = 0;
                                                            if($mengikuti == 0)
                                                            {
                                                                    $paket_pendaftaran1 = new Paket();
                                                                    $pendaftaran = $paket_pendaftaran1->getPaketPendaftaran($paket->getField("PAKET_ID"));
                                                            }
                                                            $validasi = 0;
                                                            if($mengikuti == 1)
                                                            {
                                                                echo "<div id=\"ket-daftar\">Anda telah mendaftar paket ini</div>";
                                                                $validasi = 1;
                                                            }
                                                            elseif($pendaftaran == 0)
                                                                echo "<div id=\"ket-daftar\" class=\"tdk-dapat\">Anda tidak dapat mendaftar paket ini. Waktu pendaftaran belum dimulai atau sudah berakhir</div>";
                                                            
                                                        
                                                        }
                                                        ?>
                                                        
                                                        <?
															/* STATUS PEMBUAT PAKET PANITIA */
                                                            if($this->USER_TYPE_ID == 3)
                                                            {
                                                            ?>
                                                            <div id="pembuat-paket">Pembuat Paket : <strong><?=$paket->getField("USER_LOGIN")?></strong></div>
                                                            <?
                                                            }
                                                            else
                                                            {
                                                                $pendaftaran = 0;
                                                                $paket_mengikuti = new Paket();
                                                                $mengikuti = $paket_mengikuti->getPaketMengikuti($this->REKANAN_ID, $paket->getField("PAKET_ID"));
                                                                if($mengikuti == 0)
                                                                {
                                                                    $paket_pendaftaran = new Paket();
                                                                    $pendaftaran = $paket_pendaftaran->getPaketPendaftaran($paket->getField("PAKET_ID"));
                                                                    if($pendaftaran == 1 && ($paket->getField("PAKET_METODE_LELANG_ID") == 1 || $paket->getField("PAKET_METODE_LELANG_ID") == 3 || $paket->getField("PAKET_METODE_LELANG_ID") == 4)) 
                                                                    {
                                                                        if($this->USER_LOGIN_ID == "")
                                                                        {
                                                                    ?>
                                                                        <div style="clear:both;"></div>
                                                                        <div class="daftar-paket-ini2" style="position:absolute; bottom:7px; right:10px;">
                                                                        <a href="#login-box" class="login-window" title="<?=$paket->getField("PAKET_ID")?>"><?=translate("daftar paket ini", "sign up")?></a>
                                                                        </div>                                    
                                                                <?
                                                                        }
                                                                    }
                                                                }
                                                                else
                                                                {
                                                                    /* jika sudah mengikuti cek apakah gagal */
                                                                    $paket_rekanan_lulus = new PaketRekanan();
                                                                    $lulus_pendaftaran = $paket_rekanan_lulus->getLulusPendaftaran($this->REKANAN_ID, $paket->getField("PAKET_ID"));	
                                                                    if($lulus_pendaftaran == "0")
                                                                    {
                                                                    	$paket_pendaftaran = new Paket();
                                                                        $pendaftaran = $paket_pendaftaran->getPaketPendaftaran($paket->getField("PAKET_ID"));	
                                                                    }
                                                                }
                                                            }
                                                            ?>                                                        
                                                    </td>
                                                    <td><? if(trim($paket->getField("BIDANG_USAHA")) == "()") echo "-"; else echo str_replace(", (",", <br/>(", $paket->getField("BIDANG_USAHA"));?></td>


													<?
													/* CENTANG PUBLISH PAKET */
                                                    if((int)$this->USER_TYPE_ID == 3)
                                                    {
                                                    ?>
                                                    <td>
														<?
                                                        if((int)$this->USER_TYPE_ID == 3 && $paket->getField("USER_LOGIN_ID") == $this->USER_LOGIN_ID)
                                                        {
                                                        ?>
                                                        <input type="checkbox" name="reqPublish" id="reqPublish<?=$i?>" onclick="updatePublishPaket('reqPublish<?=$i?>', '<?=$paket->getField("PAKET_ID")?>')" <? if($paket->getField("PUBLISH_PAKET") == 1) { ?>  checked="checked" <? } ?> />
                                                        <label for="reqPublish"> Publish</label>
                                                        <?
                                                        }
                                                        ?>
                                                    </td>
                                                    <?
                                                    }
                                                    ?>

													<?
													/* TOMBOL PENDAFTARAN PAKET OLEH REKANAN */
                                                    if($this->USER_TYPE_ID == 6){
                                                    ?>
                                                    <td>
                                                        
                                                        <?
                                                        if($this->USER_TYPE_ID == 6 && $pendaftaran == 1)
                                                        {
                                                          $mengikuti = $paket_mengikuti->getPaketMengikuti($this->REKANAN_ID, $paket->getField("PAKET_ID"));
                                                          if($mengikuti == 1)
                                                          {
                                                                if($lulus_pendaftaran == "0")
                                                                {
                                                                ?>
                                                                  <!--<div style="margin-bottom:10px; text-align:center">-->
																  <div class="keterangan warning"><?=translate("Anda tidak memenuhi syarat pendaftaran, untuk melengkapi klik tombol daftar ulang.", "You are not eligible for registration, please click the button below to re-registration")?></div>
                                                                  <div align="center">   
                                                                  <a href="main/?pg=registrasi_rekanan_syarat&reqPaketId=<?=md5($this->REKANAN_ID.$paket->getField("PAKET_ID"))?>" class="btn-daftar-ulang btn-kecil"><?=translate("Daftar Ulang", "Re-registration")?></a>
                                                                  </div>
                                                                <?	
                                                                }
                                                          }
                                                          else{
                                                              ?>
                                                              <div align="center">   
                                                              <a href="main/?pg=registrasi_rekanan_syarat&reqPaketId=<?=md5($this->REKANAN_ID.$paket->getField("PAKET_ID"))?>" class="btn-daftar"><?=translate("Daftar", "Register")?></a>
                                                              </div>
                                                              <?
                                                          }
                                                        }
														?>
                                                    </td>
                                                    <?
                                                    }
                                                    ?>
                                                    
												</tr>
                                            <?
											}
											?>
                                            </tbody>
                                        </table>
										<?=$pagination->createLinks()?>                                        
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="inputEmail" class="col-md-2 control-label">&nbsp;</label>
                                    <div class="col-md-4">
                                        <a href="main/index/paket_lelang_tambah" class="btn btn-primary">Edit</a>
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
