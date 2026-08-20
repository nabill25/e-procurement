<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession('blockpenyedia');   
// cek allowed url 
if ($this->libsession->cekUrl($this->uri->segment(3, ""))) {} else { redirect(base_url()); }

/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth(); 
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$this->load->model("Rekanan");
$this->load->model("RekananPengalaman");
$this->load->model("RekananPengalamanBidang");

/* create objects */
$rekanan = new Rekanan();
$rekanan_pengalaman	= new RekananPengalaman(); // tipe 0

$reqPengalamanId= httpFilterRequest('reqPengalamanId');
$reqId= httpFilterPost('reqId');
$reqBidangUsahaId = $_POST["reqBidangUsahaId"];
$reqNama= httpFilterPost('reqNama');
$reqLokasi= httpFilterPost('reqLokasi');
$reqTgsNama= httpFilterPost('reqTgsNama');
$reqTgsAlamat= httpFilterPost('reqTgsAlamat');
$reqKontrakNo= httpFilterPost('reqKontrakNo');
$reqTanggal= httpFilterPost('reqTanggal');
$reqKontrakNilai= httpFilterPost('reqKontrakNilai');
$reqJOpersen= httpFilterPost('reqJOpersen');
$reqJOket= httpFilterPost('reqJOket');
$reqStatus= httpFilterPost('reqStatus');
$reqSelesaiBA= httpFilterPost('reqSelesaiBA');
$reqProgress= httpFilterPost('reqProgress');
$reqProgressTanggal= httpFilterPost('reqProgressTanggal');
$reqSubmit= httpFilterPost('reqSubmit');
$file_list = $_POST["file_list"];
$reqLinkFile= $_FILES['reqLinkFile'];
$reqLinkFileTemp = httpFilterPost("reqLinkFileTemp");
$reqLinkFileTempTipe = httpFilterPost("reqLinkFileTempTipe");
$reqLinkFileTempUkuran = httpFilterPost("reqLinkFileTempUkuran");

$reqId = $this->ID;
$rekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
$rekanan->firstRow();

if($reqPengalamanId == "")
{}
else
{
	$rekanan_pengalaman->selectByParams(array("REKANAN_PENGALAMAN_ID" => $reqPengalamanId, "REKANAN_ID" => $this->ID));
	$rekanan_pengalaman->firstRow();
				
	$reqNama= $rekanan_pengalaman->getField("NAMA");
	$reqLokasi= $rekanan_pengalaman->getField("LOKASI");
	$reqTgsNama= $rekanan_pengalaman->getField("PEMBERI_TUGAS");
	$reqTgsAlamat= $rekanan_pengalaman->getField("PEMBERI_TUGAS_ALAMAT");
	$reqKontrakNo= $rekanan_pengalaman->getField("KONTRAK_NOMOR");
	$reqTanggal= dateToPageCheck($rekanan_pengalaman->getField("KONTRAK_TANGGAL"));
	$reqKontrakNilai= $rekanan_pengalaman->getField("KONTRAK_NILAI");
	$reqJOpersen= $rekanan_pengalaman->getField("KONTRAK_JO");
	$reqJOket= $rekanan_pengalaman->getField("KONTRAK_KETERANGAN");
	$reqStatus= $rekanan_pengalaman->getField("KONTRAK_STATUS");
	$reqProgress= $rekanan_pengalaman->getField("PROGRESS");
	$reqProgressTanggal= dateToPageCheck($rekanan_pengalaman->getField("PROGRESS_TANGGAL"));
	$reqSelesaiBA= dateToPageCheck($rekanan_pengalaman->getField("BA_TANGGAL"));
	$reqLinkFileTemp= $rekanan_pengalaman->getField("PATH_FILE");
	$reqLinkFileTempTipe= $rekanan_pengalaman->getField("TIPE");
	$reqLinkFileTempUkuran= $rekanan_pengalaman->getField("UKURAN");
	$reqLinkFileTempNama= $rekanan_pengalaman->getField("NAMA_FILE");
	
	if($reqStatus == 1)	{$tmpNoneP = 'none';$tmpNoneS = '';}
	else					{$tmpNoneP = '';$tmpNoneS = 'none';}
}
if($reqPengalamanId=='')
	$reqMode='insert';
else
	$reqMode='update';
?>
<script type="text/javascript">
$(document).ready(function() {
	
	$(function(){
		$('#ff').form({
			url:'rekanan_pengalaman_json/data_teknis_pengalaman_progress_ubah',
			onSubmit:function(){
				var v=$(this).form('validate');
        if(v) { 
          showLoad();
          return v;
        } else {
          hideLoad();
          return false;
        }
			},
			success:function(data){
				// alert(data);return false;
        hideLoad();
        alertSuccess2('Data berhasil disimpan'); 
        setTimeout(function() {
            document.location.href = 'main/index/data_teknis_pengalaman'; 
        }, 2000);
			}
		});
		
	});

  $('#reqTanggal, #reqProgressTanggal').datebox({
    editable: false
  });

});
</script>

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Pengalaman Pekerjaan Dalam Progress </h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a> 
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>

      <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          
          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Data Pengalaman 
                  <?php
                  if ($this->REKANAN_TIPE_ID == '7') { // Perorangan
                    echo "";
                  } else {
                    echo "perusahaan";
                  }
                  ?>
                  </strong>  
                </div> 
                
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label>Nama Paket Pekerjaan</label>
                    <input name="reqNama" id="txtNama" size="50" value="<?=$reqNama?>" title="Nama paket pekerjaan harus diisi" class="form-control easyui-validatebox span4"  maxlength="100" type="text" required/>
                  </div> 
                </div>
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label>Lokasi</label>
                    <input name="reqLokasi" required id="txtLokasi" size="50" title="Lokasi harus diisi" class="form-control easyui-validatebox span4" maxlength="100" type="text" value="<?=$reqLokasi?>" />
                  </div>  
                </div>
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label>File Kontrak <?= UPLOAD_PDF_2MB ?></label><br>
                    <input type="hidden" name="reqLinkFileTemp" value="<?=$reqLinkFileTemp?>" />
                    <input type="hidden" name="reqLinkFileTempTipe" value="<?=$reqLinkFileTempTipe?>" />
                    <input type="hidden" name="reqLinkFileTempUkuran" value="<?=$reqLinkFileTempUkuran?>" />
                    <input type="file" name="reqLinkFile" id="reqLinkFilePDF" size="30" <?php if($reqLinkFileTemp == "") { ?> required <?php } ?> class="easyui-validatebox"  validType="fileType['pdf']" />
                    <input type="hidden" name="reqLinkFileTempNama" value="<?=$reqLinkFileTempNama?>">
                    <?php // $reqLinkFileTempNama?>
                    <?php 
                    if ($reqLinkFileTempNama) {
                       echo '<br><a href="'.base_url('uploads/pengalaman/').$reqLinkFileTemp.'" class="badge badge-primary">Download file</a>';
                     } ?>
                  </div> 
                </div>
                <div class="card mb-1 border-blue border-darken-1">
                  <div class="card-content">
                    <div class="p-1">
                      <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                        <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Bidang 
                          <?php
                            if ($this->REKANAN_TIPE_ID == '7') { // Perorangan
                              echo "Pekerjaan";
                            } else {
                              echo "Usaha";
                            }
                            ?> </strong>  
                        <div class="badge badge-pill badge-warning"> 
                          <a id="btnAdd" onClick="openAdd('main/loadUrl/main/bidang_usaha_own');"> 
                            <span class="fa fa-plus text-white icon-line-height" data-toggle="tooltip" data-placement="top" title="Tambah Bidang Usaha"> Tambah</span> 
                          </a> 
                        </div>
                      </div> 
                      <div class="table-responsive">
                        <table class="border-double table mb-0">
                          <thead>
                            <tr class="judul-kolom">
                              <th>
                                Bidang
                                <?php
                                if ($this->REKANAN_TIPE_ID == '7') { // Perorangan
                                  echo "Pekerjaan";
                                } else {
                                  echo "Usaha";
                                }
                                ?>  
                              </th>   
                              <th>Aksi</th>
                            </tr>
                          </thead>
                          <tbody id="tbodyBidangUsaha">
                            <?php
                                $paketBidangUsaha = new RekananPengalamanBidang();
                                $paketBidangUsaha->selectByParams(array("REKANAN_PENGALAMAN_ID" => $reqPengalamanId));
                              if ($paketBidangUsaha->countRow() > 0) { 
                      
                                while($paketBidangUsaha->nextRow())
                                {
                                ?>
                                <tr>
                                  <!-- <td><?=$paketBidangUsaha->getField("BIDANG_USAHA_ID")?></td> -->
                                  <td><?=$paketBidangUsaha->getField("NAMA")?></td>
                                  <td><input type="hidden" name="reqBidangUsahaId[]" value="<?=$paketBidangUsaha->getField("BIDANG_USAHA_ID")?>" /><a title="#" onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a></td>                                    
                                </tr>
                                <?php
                                }
                              }
                            ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div> 

              </div>
            </div>
          </div>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Pemberi tugas / Pengguna Jasa</strong>   
                </div> 
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label>Nama</label>
        		        <input name="reqTgsNama" required id="txtTgsNama" value="<?=$reqTgsNama?>" title="Nama harus diisi" class="form-control easyui-validatebox span4" size="50" maxlength="100" type="text" />
                  </div> 
                </div>
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label>Alamat</label>
            	       <textarea name="reqTgsAlamat" required id="reqTgsAlamat" cols="46" class="form-control easyui-validatebox span4" rows="2"><?=$reqTgsAlamat?></textarea>
                  </div> 
                </div> 
              </div>
            </div>
          </div>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Kontrak</strong> 
                </div> 
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label>No</label>
        		        <input name="reqKontrakNo" required id="txtKontrakNo" title="No kontrak harus diisi" value="<?=$reqKontrakNo?>" class="form-control easyui-validatebox span4" size="50" maxlength="100" type="text" />
                  </div> 
                </div>
                <div class="row">
                  <div class="form-group col-md-2 mb-2">
                    <label style="width: 100%">Tanggal</label>
                    <input type="text" required name="reqTanggal" id="reqTanggal" title="Tanggal kontrak harus diisi" class="form-control easyui-datebox span2" value="<?=$reqTanggal?>" style="width: 200% !important" />
                  </div> 
                  <div class="form-group col-md-10 mb-2">
                    <label>Nilai</label>
                    <input name="reqKontrakNilai" required id="txtKontrakNilai" title="Nilai kontrak harus diisi" class="form-control easyui-validatebox span3" value="<?=$reqKontrakNilai?>" size="50" maxlength="100" type="text" OnFocus="FormatAngka('txtKontrakNilai')" OnKeyUp="FormatUang('txtKontrakNilai')" OnBlur="FormatUang('txtKontrakNilai')"/>
                  </div> 
                </div> 
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label>Keterangan</label>
                    <textarea name="reqJOket" required id="txtJOket" cols="46" class="form-control easyui-validatebox span4" rows="2"><?=$reqJOket?></textarea>
                  </div> 
                </div> 
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label style="width: 100%">Status</label>
                    <input required value="1" <?php if($reqStatus == 1) echo 'checked'?> name="reqStatus" id="rdstatus1" onclick="changePengalaman(this.value)" type="radio" /> Selesai &nbsp;
                     <input value="2" checked name="reqStatus" id="rdstatus2" onclick="changePengalaman(this.value)" type="radio" style="display: none;" /> <!-- Dalam Progres -->
                  </div> 
                </div>  
              </div>
            </div>
          </div>

          <div class="card mb-1 border-blue border-darken-1">
            <div class="card-content">
              <div class="p-1">
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Laporan Progress</strong>   
                </div> 
                <div class="row">
                  <div class="form-group col-md-2 mb-2">
                    <label style="width: 100%">Tanggal Progress</label>
        		        <input required type="text" name="reqProgressTanggal" id="reqProgressTanggal" title="Tanggal progress harus diisi" class="form-control easyui-datebox span2" value="<?=$reqProgressTanggal?>" style="width: 200% !important"/>
                  </div> 
                  <div class="form-group col-md-10 mb-2">
                    <label style="width: 100%">Progress %</label>
                    <input required name="reqProgress" type="text" onkeypress="return isNumberKey(event)" id="reqProgress" title="Progress harus diisi" class="form-control easyui-validatebox span1" value="<?=$reqProgress?>" size="3" maxlength="3" />
                  </div> 
                </div>  
              </div>
            </div>
          </div>

          <div class="form-actions">
            <input type="hidden" name="reqMode" value="<?=$reqMode?>" />
            <input type="hidden" name="reqPengalamanId" value="<?=$reqPengalamanId?>" />
            <a href="main/index/data_teknis_pengalaman" class="<?= CLASS_BTN_DANGER ?> mr-1"> <?= BTN_KEMBALI ?> </a> 
            <button type="submit" class="<?= CLASS_BTN_PRIMARY ?>"><?= BTN_SIMPAN ?></button>
          </div> 

        </div>
      </div>
      </form>

    </div>
  </div> 
</div>      