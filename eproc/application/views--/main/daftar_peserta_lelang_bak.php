<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("PaketRekanan");
$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$this->load->library("KMail");

$paket_rekanan = new PaketRekanan();

$reqMode = $this->input->post("reqMode");
$reqId = $this->input->get("reqId");
$submitSimpan = $this->input->post("submitSimpan");

$reqLulusPendaftaran = $_POST["reqLulusPendaftaran"];
$reqLulusKeterangan = $_POST["reqLulusKeterangan"];
$reqPaketRekananId = $_POST["reqPaketRekananId"];
$reqPaketRekananIdUser = $this->input->post("reqPaketRekananIdUser");
$reqLulusPendaftaranUser = $this->input->post("reqLulusPendaftaranUser");


$paket_rekanan->selectByParams(array("PAKET_ID" => $reqId));
//echo $paket_rekanan->query;exit;
?> 
<script type="text/javascript">
$(document).ready(function() {
	$(function(){
		$('#ff').form({
			url:'paket_rekanan_json/daftar_peserta_lelang',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				
				//document.location.href = 'main/index/data_perpajakan_neraca/?reqTahunNeraca=<?=$reqTahunNeraca?>';		
			}
		});
		
	});
	
});
</script>
<div class="row">
    <div class="col-md-12">
    	<div class="area-main">
        	<div class="judul-halaman">Dokumen Paket Lelang</div>
            <div class="inner">
            	<div class="area-sidelook"></div>
                <div class="area-konten">
                    <div class="area-konten-inner">
                        <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
							
                            <div class="judul-grup">Daftar Peserta Paket Lelang</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                    
                                    <?php /*?>Data Administrasi Umum
                                    <a title="#" onClick="openAdd('main/loadUrl/main/daftar_rekanan_administrasi_umum/?reqId=354');">Umum</a>
                                    <a title="#" onClick="openAdd('main/loadUrl/main/daftar_rekanan_administrasi_ijin/?reqId=354');">Ijin Usaha</a>
                                    <a title="#" onClick="openAdd('main/loadUrl/main/daftar_rekanan_administrasi_sbu/?reqId=354');">Sertifikat Badan Usaha</a>
                                    <a title="#" onClick="openAdd('main/loadUrl/main/daftar_rekanan_administrasi_landasan/?reqId=354');">Landasan Hukum</a>
                                    <a title="#" onClick="openAdd('main/loadUrl/main/daftar_rekanan_administrasi_pengurus/?reqId=354');">Pengurus Perusahaan</a>
                                    <a title="#" onClick="openAdd('main/loadUrl/main/daftar_rekanan_administrasi_keuangan/?reqId=354');">Kepemilikan Saham</a>
                                    Data Keuangan
                                    <a title="#" onClick="openAdd('main/loadUrl/main/daftar_rekanan_keuangan_rekening/?reqId=354');">Rekening Koran</a>
                                    Data Perpajakan 
                                    <a title="#" onClick="openAdd('main/loadUrl/main/daftar_rekanan_pajak_pkp/?reqId=354');">PKP</a>
                                    <a title="#" onClick="openAdd('main/loadUrl/main/daftar_rekanan_pajak_spt_tahunan/?reqId=354');">SPT Tahunan</a>
                                    <a title="#" onClick="openAdd('main/loadUrl/main/daftar_rekanan_pajak_bulanan/?reqId=354');">Laporan Pajak Bulanan(PPH/PPN)</a>
                                    <a title="#" onClick="openAdd('main/loadUrl/main/daftar_rekanan_pajak_neraca/?reqId=354');">Neraca</a>
                                    Data Teknis
                                    <a title="#" onClick="openAdd('main/loadUrl/main/daftar_rekanan_teknis_tenaga/?reqId=354');">Tenaga Ahli</a>
                                    <a title="#" onClick="openAdd('main/loadUrl/main/daftar_rekanan_teknis_pengalaman/?reqId=354');">Pengalaman</a>
                                    <a title="#" onClick="openAdd('main/loadUrl/main/daftar_rekanan_teknis_sertifikat/?reqId=354');">Sertifikat Lain</a>
                                    <a title="#" onClick="openAdd('main/loadUrl/main/daftar_rekanan_teknis_peralatan/?reqId=354');">Peralatan</a>
                                    Persyaratan Pendaftaran
                                    <a title="#" onClick="openAdd('main/loadUrl/main/daftar_rekanan_persyaratan_pendaftaran/?reqId=354');">Peralatan</a><?php */?>
                                    
                                    	<table width="100%" border="0" cellpadding="2" cellspacing="1" id="tbl_bidang">
                                          <tbody>
                                            <tr class="judul-kolom">
                                              <th>No.</th>
                                              <th >Nama</th>
                                              <th>Syarat</th>
                                              <th>Diundang</th>
                                              <th>Tgl Daftar</th>
                                              <th>Lulus Pendaftaran</th>
                                              <th>Lulus Kualifikasi</th>
                                              <th>Dok Penawaran</th>
                                              <th>Lulus Penawaran</th>
                                              <th>Sudah Email</th>
                                              <th>Email</th>
                                            </tr>
                                             <?php
												  $i=1;
												  $style="gelap";
												  while($paket_rekanan->nextRow())
												  {
													  $disable = "";
											  ?>
                                              	<tr class="<?=$style?>">
                                                    <td><?=$i?>.</td>
                                                    <td> <a title="#" onClick="openAdd('main/loadUrl/main/data_rekanan');"><?=$paket_rekanan->getField("REKANAN")?></a></td>
                                                    <td align="center"> 
                                                    <input type="button" onclick="windowOpener(600,900, '', 'main/loadUrl/main/daftar_rekanan_persyaratan/?reqId=<?=$paket_rekanan->getField("REKANAN_ID")?>&reqPaketId=<?=$reqId?>')" value="Lihat">
                                                    </td>
                                                    <td> <?=getFormattedDate($paket_rekanan->getField("TANGGAL_UNDANG"))?> </td>
                                                    <td> <?=getFormattedDate($paket_rekanan->getField("TANGGAL_DAFTAR"))?> </td>
                                                    <?php
                                                    if($paket_rekanan->getField("TANGGAL_DAFTAR") == "")
                                                        $disable = 'disabled="disabled"';
                                                    ?>
                                                    <td>
                                                        <input type="hidden" name="reqPaketRekananId[]" value="<?=$paket_rekanan->getField("PAKET_REKANAN_ID")?>" />
                                                        <input type="hidden" name="reqLulusPendaftaran[]" id="reqLulusPendaftaran<?=$i?>" value="<?php if($paket_rekanan->getField("LULUS_PENDAFTARAN") == 2) echo "NULL"; else { echo $paket_rekanan->getField("LULUS_PENDAFTARAN"); } ?>" />
                                                        <input type="radio" name="reqSetujuiPendaftaran<?=$i?>" onclick="document.getElementById('reqLulusPendaftaran<?=$i?>').value='1'"  <?php if($paket_rekanan->getField("LULUS_PENDAFTARAN") == 1) { ?> checked="checked" <?php } ?> <?=$disable?> /> Setujui 
                                                        <input type="radio" name="reqSetujuiPendaftaran<?=$i?>" onclick="document.getElementById('reqLulusPendaftaran<?=$i?>').value='0'" <?php if($paket_rekanan->getField("LULUS_PENDAFTARAN") == 0) { ?> checked="checked" <?php } ?> <?=$disable?> /> Tolak
                                                        
                                                    </td>
                                                    <td align="center"> <?php if($paket_rekanan->getField("LULUS_KUALIFIKASI") == 1) {?><img src="images/centang.png" /> <?php } ?> </td>
                                                    <td align="center"> <?php if($paket_rekanan->getField("LULUS_PENAWARAN") == 1) {?><img src="images/centang.png" /> <?php } ?> </td>
                                                    <td> <?php if($paket_rekanan->getField("LULUS_PENAWARAN_URUT") == "") {} else { 
                                                             if($paket_rekanan->getField("LULUS_PENAWARAN_KETERANGAN") == "")
                                                             {	
                                                             ?>
                                                                Terendah <?=$paket_rekanan->getField("LULUS_PENAWARAN_URUT")?> 
                                                             <?php
                                                             }
                                                             else
                                                                echo $paket_rekanan->getField("LULUS_PENAWARAN_KETERANGAN");
                                                         } 
                                                         ?>
                                                       </td>
                                                    <?php /*?><td><input type="checkbox" name="reqSudahBayar" id="reqSudahBayar<?=$i?>" value="1" onclick="updateStatusBayar('reqSudahBayar<?=$i?>', '<?=$paket_rekanan->getField("PAKET_REKANAN_ID")?>')" <?php if($paket_rekanan->getField("STATUS_BAYAR") == 1) { ?> checked="checked" <?php } ?>  <?=$disable?>  />
                                                    <label for="reqSudahBayar"></label></td><?php */?>
                                                    <td align="center"><?php if($paket_rekanan->getField("DI_EMAIL") == 2) { ?> <img src="images/centang.png"> <? } else { ?> <img src="images/uncentang.png"> <?php } ?> 
                                                    <label for="reqSudahBayar"></label></td>
                                                    <td>
                                                    <?php
                                                    if($paket_rekanan->getField("LULUS_PENDAFTARAN") == "2")
                                                    {}
                                                    else
                                                    {
                                                    ?>
                                                    <a onclick="if(confirm('Apakah anda yakin untuk mengirim email kepada rekanan perihal lulus pendaftaran?')) { document.location = 'main/?pg=daftar_peserta_lelang&reqId=<?=$reqId?>&reqMode=kirim_user&reqPaketRekananIdUser=<?=$paket_rekanan->getField("PAKET_REKANAN_ID")?>&reqLulusPendaftaranUser=' + document.getElementById('reqLulusPendaftaran<?=$i?>').value; }" style="cursor:pointer" class="btn-kirim">Kirim</a>
                                                    <?php
                                                    }
                                                    ?>
                                                    </td>
                                                  </tr>
                                                  <?php
                                                    $i++;
                                                    if($style == "gelap")
                                                        $style = "terang";
                                                    else
                                                        $style = "gelap";
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
                                    <div class="form-group">
                                    	<label for="inputEmail" class="col-md-2 control-label">&nbsp;</label>
                                        <div class="col-md-4">
                                        	<input type="hidden" name="submitSimpan" id="submitSimpan" />
                                            <input type="hidden" id="reqMode" name="reqMode" />
                                            <input type="hidden" name="reqId" value="<?=$reqId?>" />
                                            <?php /*?><a onclick="if(confirm('Apakah anda yakin untuk mengirim email kepada rekanan perihal lulus pendaftaran?')) { setIdWithValue('reqMode','kirim');$('#alumniForm').submit();}" 
                                            	style="cursor:pointer" class="btn-kirim">Kirim</a><?php */?>
                                            <?php /*?><a onclick="windowOpenerPopup(350,450,'Cetak Close','main/loadUrl/main/cetak_proses_pengadaan/?reqId=<?=$reqId?>');" style="cursor:pointer" class="btn-cetak">Cetak</a>
                                            <a onclick="setIdWithValue('submitSimpan','Simpan');$('#alumniForm').submit()" class="btn-simpan">Simpan</a></td><?php */?>
                                        	<a href="main/index/paket_detil/?reqId=<?=$reqId?>" class="btn btn-primary">Kembali</a>
                                            <a href="" class="btn btn-primary">Kirim</a>
                                            <a href="" class="btn btn-primary">Cetak</a>
                                            <a href="" class="btn btn-primary">Simpan</a>
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
