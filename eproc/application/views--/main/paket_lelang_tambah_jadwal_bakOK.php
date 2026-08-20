<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("Metode");
$this->load->model("Paket");

$metode = new Metode();

$reqId = $this->input->get("reqId");

$paketInfo->getPaket($reqId);
$reqMetodeLelangId = $paketInfo->metode_lelang_id;

$reqExistData = $metode->getCountByParams(array("PAKET_ID" => $reqId));
$metode->selectByParams(array(), -1, -1, $reqId);

if($reqMetodeLelangId == 1 || $reqMetodeLelangId == 3)
{
	$tempPublishTanggalJam = $paketInfo->publish_paket_tanggal;
	$arrPublishTanggalJam = explode(" ", $tempPublishTanggalJam);
	$arrPublishJamMenit = explode(":", $arrPublishTanggalJam[1]);
	$tempPublishTanggal = $arrPublishTanggalJam[0];
	$tempPublishJam = $arrPublishJamMenit[0];
	$tempPublishMenit = $arrPublishJamMenit[1];
}

?> 

<script type="text/javascript">
$(document).ready(function() {
	
	$(function(){
		$('#ff').form({
			url:'paket_tahap_json/add',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				//$.messager.alert('Info', data, 'info');	
				document.location.href = data;
			}
		});
		
	});
	
});
</script>

<div class="row">
    <div class="col-md-12">
    	<div class="area-main">
        	<div class="judul-halaman">Tambah Paket Lelang</div>
            <div class="inner">
            	<div class="area-sidelook"></div>
                <div class="area-konten">
                    <div class="area-konten-inner">
                		
                        <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
							
                            <div class="judul-grup">Jadwal Pelelangan</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    	<table width="100%" border="0" cellpadding="2" cellspacing="1" id="tbl_bidang">
                                          <tbody>
                                            <tr valign="top" class="judul-kolom">
                                              <td rowspan="3" valign="middle"><div align="center">No</div></td>
                                              <td rowspan="3" valign="middle"><div align="center">Tahapan Lelang</div></td>
                                              <td rowspan="2" valign="middle"><div align="center">Hadir</div>                                <div align="center"></div></td>
                                              <td rowspan="2" valign="middle"><div align="center">Tampil</div>                                <div align="center"></div></td>
                                              <td colspan="4" valign="top"> <div align="center">Waktu Pelaksanaan </div></td>
                                              </tr>
                                            <tr valign="top" class="judul-kolom">
                                              <td colspan="2"> <div align="center">Mulai </div></td>
                                              <td colspan="2"><div align="center">Selesai</div></td>
                                              </tr>
                                            <tr valign="top" class="judul-kolom">
                                              <td><div align="center">
                                                <input type="checkbox" name="reqHadirAll" id="reqHadirAll" onChange="cek_semua_hadir(document.frmInformasiAdd.reqHadir)" />
                                              </div>
                                              <label for="reqHadirAll"></label></td>
                                              <td><div align="center">
                                                <input type="checkbox" name="reqTampillAll" id="reqTampillAll" onChange="cek_semua_tampil(document.frmInformasiAdd.reqTampil)" />
                                              </div>
                                              <label for="reqTampillAll"></label></td>
                                              <td> <div align="center">Tanggal </div></td>
                                              <td><div align="center">Jam</div></td>
                                              <td> <div align="center">Tanggal </div></td>
                                              <td><div align="center">Jam</div></td>
                                            </tr>

											<?
                                            $i=1; $no=1; $stat = ''; $stat_m = '';
                                            while($metode->nextRow())
                                            {
                                                if($stat == '') $comma = '';	else	$comma = ', ';								
                                                $stat .= $comma."#reqJamSelesai$i, #reqJamMulai$i";
                                                
                                                if($stat_m == '') $comma = '';	else	$comma = ', ';								
                                                $stat_m .= $comma."#reqMenitSelesai$i, #reqMenitMulai$i";
                                                
                                                $disabledTanggalAwal = $metode->getField("TANGGAL_AWAL_DISABLED");
                                                $triggerTanggalAkhir = $metode->getField("TANGGAL_AKHIR_TRIGGER");
                                            ?>
                                                <tr valign="top" class="gelap">
                                                  <td><?=$no?>.</td>
                                                    <td style="width:calc(50% - 50px)">
                                                        <?=$metode->getField("NAMA")?>
                                                        <?
                                                        $notif = "";
                                                        $notifikasi = $metode->getField("NOTIFIKASI");
                                                        if($notifikasi == "PENAWARAN")
                                                        {
                                                            if($metode->getField("HADIR_CENTANG") == 1)
                                                                $notif = "Pemasukan dokumen penawaran melalui offline";
                                                            else	
                                                                $notif = "Pemasukan dokumen penawaran melalui online";
                                                        }
                                                        if($notifikasi == "")
                                                        {}
                                                        else
                                                        {
                                                        ?>        
                                                        <br>                                 
                                                        <label id="lblNotifikasi<?=$notifikasi?>" style="font-size:10px; font-weight:bold"><?=$notif?></label>
                                                        <?
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><div align="center">
                                                    <?
                                                    if($metode->getField("HADIR") == 1)
                                                    {
                                                    ?>
                                                      <input type="checkbox" name="reqHadir[<?=$i?>]" id="reqHadir<?=$i?>" value="1" id="reqHadir"  <? if($metode->getField("HADIR_CENTANG") == 1) { ?> checked="checked" <? } ?> onClick="checkNotifikasi('<?=$i?>', '<?=$notifikasi?>')" />                                  
                                                    <?
                                                    }
                                                    ?>
                                                    </div>
                                                    <?
                                                    if($i == 1 && $metode->getField("TANGGAL_AWAL") == '')
                                                        $tmpTanggalMulai = date("d-m-Y");
                                                    else
                                                        $tmpTanggalMulai = datetimeToPage($metode->getField("TANGGAL_AWAL"), "date");
                                                    
                                                    if($i == 1 && $metode->getField("TAMPILKAN_CENTANG") == '')
                                                        $tmpTampilCentang = 1;
                                                    else
                                                        $tmpTampilCentang = $metode->getField("TAMPILKAN_CENTANG");
                                                    ?>
                                                    
                                                    <label for="reqPembuatanPaketLelang"></label></td>
                                                    <td><div align="center">
                                                      <input type="checkbox" name="reqTampil[<?=$i?>]" id="reqTampil<?=$i?>" value="1"  id="reqTampil" <? if($tmpTampilCentang == 1) { ?> checked="checked" <? } ?> />
                                                    </div>
                                                    <label for="reqTampil"></label></td>
                                                    <td>                                    
                                                    <input type="text" class="easyui-datebox" name="reqTanggalMulai[<?=$i?>]" id="reqTanggalMulai<?=$i?>" value="<?=$tmpTanggalMulai?>" <? if($disabledTanggalAwal == "1") { ?> readonly style="width:112px; background:#F1F1F1;" <? } else { ?> style="width:112px"  <? } ?> />                                                    
                                                    </td>
                                                    <td><label for="reqJamMulai"></label>
                                                    <?
                                                    $arrJamAwal = explode(":", $metode->getField("JAM_AWAL"));
                                                    ?>
                                                    <input name="reqJamMulai[<?=$i?>]" type="text" id="reqJamMulai<?=$i?>" value="<?=$arrJamAwal[0]?>" size="2" maxlength="2" <? if($disabledTanggalAwal == "1") { ?> readonly style="background:#F1F1F1" <? } ?> /> 
                                                    : 
                                                    <label for="reqMenitMulai"></label>
                                                    <input name="reqMenitMulai[<?=$i?>]" type="text" id="reqMenitMulai<?=$i?>" value="<?=$arrJamAwal[1]?>" size="2" maxlength="2" <? if($disabledTanggalAwal == "1") { ?> readonly style="background:#F1F1F1" <? } ?> /></td>
                                                    <td><input type="text" class="easyui-datebox" style="width:112px" name="reqTanggalSelesai[<?=$i?>]" id="reqTanggalSelesai<?=$i?>" onchange="checkTanggal('reqTanggalMulai<?=$i?>', 'reqTanggalSelesai<?=$i?>')" value="<?=datetimeToPage($metode->getField("TANGGAL_AKHIR"), "date")?>" />
                                                    </td>
                                                    <?
                                                    $arrJamAkhir = explode(":", $metode->getField("JAM_AKHIR"));
                                                    ?>                                      
                                                    <td><label for="reqJamMulai"></label>
                                                      <input name="reqJamSelesai[<?=$i?>]" type="text" value="<?=$arrJamAkhir[0]?>" id="reqJamSelesai<?=$i?>" size="2" maxlength="2" <? if($triggerTanggalAkhir == "1") { ?> onKeyUp="$('#reqJamMulai<?=$i+1?>').val(this.value);" <? } ?> />
                                                    :
                                                    <label for="reqMenitMulai"></label>
                                                    <input name="reqMenitSelesai[<?=$i?>]" type="text" value="<?=$arrJamAkhir[1]?>" id="reqMenitSelesai<?=$i?>" size="2" maxlength="2" <? if($triggerTanggalAkhir == "1") { ?> onKeyUp="$('#reqMenitMulai<?=$i+1?>').val(this.value);" <? } ?> />
                                                    <input type="hidden" name="reqTahapanLelang[<?=$i?>]" value="<?=$metode->getField("NAMA")?>" />
                                                    
                                                    </td>
                                                </tr>
                                                <?
                                                /* TAMBAHAN MODUL UNTUK PUBLISH PANEL */
                                                if($metode->getField("JENIS_TAHAP") == 1 || $metode->getField("JENIS_TAHAP") == 2 || $metode->getField("JENIS_TAHAP") == 7 || $metode->getField("JENIS_TAHAP") == 11 || $metode->getField("JENIS_TAHAP") == 12)
                                                {
                                                    if($metode->getField("URUT") == 1)
                                                    {
                                                        $no++;
                                                    ?>
                                                        <tr valign="top" class="gelap">
                                                            <td><?=$no?></td>
                                                            <td>Publish Paket Lelang</td>
                                                            <td></td>
                                                            <td></td>
                                                            <td>
                                                                <input type="text" class="easyui-datebox" style="width:112px" name="reqTanggalPublish" id="reqTanggalPublish" value="<?=$tempPublishTanggal?>" />                                                                
                                                            </td>
                                                            <td>
                                                                <label for="reqJamPublish"></label>
                                                                <?
                                                                $arrJamAwal = explode(":", $metode->getField("JAM_AWAL"));
                                                                ?>
                                                                <input name="reqJamPublish" type="text" id="reqJamPublish" value="<?=$tempPublishJam?>" size="2" maxlength="2" /> 
                                                                : 
                                                                <label for="reqMenitPublish"></label>
                                                                <input name="reqMenitPublish" type="text" id="reqMenitPublish" value="<?=$tempPublishMenit?>" size="2" maxlength="2" />
                                                            </td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                    <?	
                                                    }
                                                }
                                                ?>
                                            <?
                                                $i++;
                                                $no++;
                                            }
                                            ?>


                                          </tbody>
                                        </table>
                                        <div class="col-md-8">
                                        </div>
                                    </div>
                                </div>
							</div>

                            <div class="row">
                                <div class="col-md-12">
                                	<div class="area-tombol-bawah">
                                    	<input type="hidden" name="reqId" value="<?=$reqId?>">
                                    	<input type="hidden" name="submitSimpan" value="<? if($reqExistData == "0") { ?>Simpan<? } else { ?>Update<? } ?>" />
                                    	<a href="main/index/paket_lelang_tambah/?reqId=<?=$reqId?>" class="btn-kembali">Kembali</a>
                                        <input type="submit" value="Lanjut" class="btn-lanjut pull-right">
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
