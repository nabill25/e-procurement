<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 // if($this->USER_TYPE_ID == "")
//     redirect("app");

$this->load->library("kauth");  $userLogin = new kauth();
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
$this->load->model("Rekanan");
$this->load->model("RekananIjinUsaha");
$this->load->model("RekananAkta");
$this->load->model("RekananPengurus");
$this->load->model("RekananBidangUsaha");
$this->load->model("Users");
$this->load->library("KMail");
$this->load->model("RekananSaham");
$this->load->model("RekananSertifikat");
$this->load->model("RekananRekeningKoran");

$rekanan = new Rekanan();
$rekanan_ijin_usaha = new RekananIjinUsaha();
$rekanan_akta = new RekananAkta();
$rekanan_pengurus_komisaris = new RekananPengurus();
$rekanan_pengurus_direksi = new RekananPengurus();
$rekanan_bidang_usaha = new RekananBidangUsaha();
$rekanan_bidang_usaha_sbu = new RekananBidangUsaha();
$FILE_DIR = "uploads/rekanan/";
$FILE_DIR_IJIN_USAHA = "uploads/ijin_usaha/";
$FILE_DIR_LANDASAN_HUKUM = "uploads/landasan_hukum/";
$FILE_DIR_KOMISARIS = "uploads/pemimpin_perusahaan/";
$FILE_DIR_DIREKSI = "uploads/pemimpin_perusahaan/";
$user_login = new Users();
$rekanan_saham 	= new RekananSaham();
$rekanan_sertifikat 	= new RekananSertifikat();
$rekanan_sertifikat_domisili 	= new RekananSertifikat();
$rekanan_sertifikat_tanda_daftar 	= new RekananSertifikat();

$reqKode = $this->input->post("reqKode");

$rekanan->selectByParams(array("A.KODE"=>$reqKode),-1,-1);
$rekanan->firstRow();
$reqId = $rekanan->getField("REKANAN_ID");
$reqRekananTipeId = $rekanan->getField("REKANAN_TIPE_ID");
$tempKualifikasi = $rekanan->getField("REKANAN_KUALIFIKASI");
$tempMail = $rekanan->getField("EMAIL");
$tempWebsite = $rekanan->getField("WEBSITE");
$tempKontakPerson = $rekanan->getField("KONTAK_PERSON");
$tempKontakPersonHp = $rekanan->getField("KONTAK_PERSON_HP");
$tempFax = $rekanan->getField("FAX_KODE").$rekanan->getField("FAX");
$tempTelepon = $rekanan->getField("TELEPON_KODE").$rekanan->getField("TELEPON");
$tempKota = $rekanan->getField("KOTA");
$tempAlamat = $rekanan->getField("ALAMAT");
$tempPKPTanggal = getFormattedDate($rekanan->getField("PKP_TANGGAL"));
$tempLinkFileTempPKP= $rekanan->getField("PKP_FILE");
$tempLinkFileTempKTP= $rekanan->getField("KTP_FILE");
$tempLinkFileTempNPWP= $rekanan->getField("NPWP_FILE");
$tempStatus = $rekanan->getField("STATUS_CP");
$tempNPWP = $rekanan->getField("NPWP");
$tempNama= $rekanan->getField("NAMA");
$tempCV= $rekanan->getField("CV_FILE");
$tempRekananNama = $rekanan->getField("REKANAN_NAMA");
?>
<script type="text/javascript">
$(document).ready(function() {
	$(function(){
			$('#ff').form({
				url:'rekanan_json/validasi_rekanan',
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
            hideLoad();
					$.messager.alert('Info', data, 'info');
          $("#btnValidasi").hide();
          $("#btnRevisi").hide();
          $("#btnSertifikat").show();
        }
      });

    });

});

function revisiPendaftaran()
{
  <?php //$this->input->get("reqId") ?>
  if(confirm("Apa benar data penyedia ini ingin dikembalikan?"))
  {
    $.getJSON('rekanan_json/revisi_rekanan?reqId='+<?=$reqId?>,
    function(data){
       $.messager.alert('Info', "Dikembalikan ke Rekanan.", 'info');
       $("#btnSertifikat").hide();
       $("#btnRevisi").show();
			 $("#btnValidasi").show();
		});
	}
} 
</script>

<?php 
if ($reqRekananTipeId == '7') { ?>
  <div class="row"> 
    <div class="col-md-12 col-sm-12">
      <div class="card">
        <div class="card-header card-head-inverse bg-primary">
          <h4 class="card-title text-white">Konfirmasi Validasi <?= LABEL_PENYEDIA ?> Perorangan</h4>
          <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a> 
          <div class="heading-elements">
              <ul class="list-inline mb-0">
                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
              </ul>
          </div>
        </div>

        <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
        <div class="card-content collapse show border-info border-darken-2">
          <div class="card-body">

              <div class="card mb-1 border-blue border-darken-1">
                <div class="card-content">
                  <div class="p-1">
                    <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                      <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Profil Perorangan</strong>
                      <span class="badge badge-pill badge-danger">wajib</span>
                    </div> 
                    <div class="table-responsive">
                      <table class="border-double table mb-0">
                        <tbody>
                          <tr>
                              <td style="width: 20%">Nama :</td>
                              <td>
                                 <?=$tempNama?>
                              </td>
                          </tr>
                          <tr>
                              <td>Alamat :</td>
                              <td>
                                 <?=$tempAlamat?>
                              </td>
                          </tr>
                          <tr>
                              <td>Kota :</td>
                              <td>
                                 <?=$tempKota?>
                              </td>
                          </tr>
                              <td>Provinsi :</td>
                              <td>
                                 <?=$rekanan->getField("REGION")?>
                              </td>
                          <tr>
                          </tr>
                              <td>Kodepos :</td>
                              <td>
                                 <?=$rekanan->getField("KODEPOS")?>
                              </td>
                          <tr>
                              <td>No. telepon :</td>
                              <td>
                                 <?=$tempTelepon?>
                              </td>
                          </tr>
                          <tr>
                              <td>No. Fax :</td>
                              <td>
                                  <?=$tempFax?>
                              </td>
                          </tr>
                          <tr>
                              <td>Kontak Person :</td>
                              <td>
                                  <?=$tempKontakPerson?>
                              </td>
                          </tr>
                          <tr>
                              <td>HP :</td>
                              <td>
                                  <?=$tempKontakPersonHp?>
                              </td>
                          </tr>
                          <tr>
                              <td>E-mail :</td>
                              <td>
                                  <?=$tempMail?>
                              </td>
                          </tr>
                          <tr>
                              <td>Website :</td>
                              <td>
                                  <?=$tempWebsite?>
                              </td>
                          </tr>
                          <tr>
                              <td>Kualifikasi :</td>
                              <td>
                                  <?=$tempKualifikasi?>
                              </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card mb-1 border-blue border-darken-1">
                <div class="card-content">
                  <div class="p-1">
                    <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                      <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>NPWP</strong>
                      <span class="badge badge-pill badge-danger">Wajib</span>
                    </div> 
                    <div class="table-responsive">
                      <table class="border-double table mb-0"> 
                          <tbody> 
                              <tr>  
                                <td style="width: 20%">NPWP :</td>
                                  <td>
                                      <?=$tempNPWP?>
                                  </td>
                              </tr>
                              <tr>  
                                <td>File NPWP :</td>
                                  <td>
                                     <?php
                         if($tempLinkFileTempNPWP == '')
                         {
                         }
                         else{
                        $arrFile = explode(";", $tempLinkFileTempNPWP);
                        for($iFile=0;$iFile<count($arrFile);$iFile++)
                        {
                        ?>
                            <a href="<?=$FILE_DIR.$arrFile[$iFile]?>" class="taut" target="_blank">Download</a>
                        <?php
                          }
                         }
                        ?>
                                  </td>
                              </tr>
                              <tr>  
                              </tr>
                          </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card mb-1 border-blue border-darken-1">
                <div class="card-content">
                  <div class="p-1">
                    <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                      <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>KTP</strong>
                      <span class="badge badge-pill badge-danger">wajib</span>
                    </div> 
                    <div class="table-responsive">
                      <table class="border-double table mb-0"> 
                          <tbody>
                              <tr> 
                                  <td style="width: 20%">Nomor :
                                  </td>
                                  <td>
                                     <?=$rekanan->getField("KTP");?>
                                  </td>
                              </tr> 
                              <tr>  
                                  <td>File KTP :</td>
                                  <td>
                                     <?php
                                     if($tempLinkFileTempKTP == '')
                                     {
                                     }
                                     else{
                                      $arrFile = explode(";", $tempLinkFileTempKTP);
                                      for($iFile=0;$iFile<count($arrFile);$iFile++)
                                      {
                                      ?>
                                              <a href="<?=$FILE_DIR.$arrFile[$iFile]?>" class="taut" target="_blank">Download</a>
                                      <?php
                                          }
                                     }
                                      ?>
                                  </td>
                              </tr> 
                              <tr>  
                              </tr>
                          </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card mb-1 border-blue border-darken-1">
                <div class="card-content">
                  <div class="p-1">
                    <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                      <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>CV (Daftar Riwayat Hidup)</strong>
                      <span class="badge badge-pill badge-danger">wajib</span>
                    </div> 
                    <div class="table-responsive">
                      <table class="border-double table mb-0"> 
                          <tbody> 
                              <tr>  
                                  <td style="width: 20%">File CV (Daftar Riwayat Hidup) :</td>
                                  <td>
                                     <?php
                                     if($tempCV == '')
                                     {
                                     }
                                     else{ 
                                      ?>
                                              <a href="<?=$FILE_DIR.$tempCV?>" class="taut" target="_blank">Download</a>
                                      <?php
                                     }
                                      ?>
                                  </td>
                              </tr> 
                              <tr>  
                              </tr>
                          </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
  
              <div class="form-actions">
                  <input type="hidden" name="reqNomorValidasi" value="<?=$reqKode?>">
                  <input type="hidden" name="reqEmail" value="<?=$tempMail?>">
                  <input type="hidden" name="reqId" value="<?=$reqId?>">
                  <input type="hidden" name="reqRekananNama" value="<?=$tempRekananNama?>">
                  <input type="hidden"  name="submitSimpan" value="Simpan" />
                  <a href="main/index/validasi" class="btn btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a> 
                  <?php
                  $user_login->selectByParams(array("REKANAN_ID" => $reqId));
                  $user_login->firstRow();

                  $user_status = $user_login->getField("USER_STATUS");

                  // if($rekanan->getField("STATUS_VALIDASI") == 1)
                  if($user_status == 1)
                  { ?>
                  <button type="submit" style="display: none" class="btn btn-primary mr-1 text-white" id="btnValidasi"><i class="fa fa-check-square-o"></i> VALIDASI</button>
                  <!-- <a title="#" id="btnRevisi"  onclick="revisiPendaftaran()" class="btn btn-info mr-1 text-white"><i class="fa fa-repeat"></i> Dikembalikan</a> -->
                  <a id="btnSertifikat" href="main/loadUrl/report/vms_pdf/?reqId=<?=$reqId?>&kode=<?=$reqKode?>&rekanantipeid=7" onclick="confirm('Cetak Sertifikat ?')" class="btn btn-info mr-1 text-white"><i class="fa fa-print"></i> Cetak Sertifikat</a>
                  <?php 
                  }
                  else
                  {
                  ?>
                  <button type="submit" class="btn btn-primary mr-1 text-white" id="btnValidasi"><i class="fa fa-check-square-o"></i> VALIDASI</button>
                  <a title="#" id="btnRevisi"  onclick="revisiPendaftaran()" class="btn btn-info mr-1 text-white"><i class="fa fa-repeat"></i> Dikembalikan</a>
                  <a id="btnSertifikat" href="main/loadUrl/report/vms_pdf/?reqId=<?=$reqId?>&kode=<?=$reqKode?>" style="display: none" onclick="confirm('Cetak Sertifikat ?')" class="btn btn-info mr-1 text-white"><i class="fa fa-print"></i> Cetak Sertifikat</a>
                  <?php
                  }
                  ?>
              </div> 


          </div>
        </div>
        </form>

      </div>
    </div> 
  </div>  
<?php 
} else 
{ ?>
  <div class="row"> 
    <div class="col-md-12 col-sm-12">
      <div class="card">
        <div class="card-header card-head-inverse bg-primary">
          <h4 class="card-title text-white">Konfirmasi Validasi <?= LABEL_PENYEDIA ?></h4>
          <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a> 
          <div class="heading-elements">
              <ul class="list-inline mb-0">
                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
              </ul>
          </div>
        </div>

        <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data">
        <div class="card-content collapse show border-info border-darken-2">
          <div class="card-body">

              <div class="card mb-1 border-blue border-darken-1">
                <div class="card-content">
                  <div class="p-1">
                    <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                      <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Profil Perusahaan</strong>
                      <span class="badge badge-pill badge-danger">wajib</span>
                    </div> 
                    <div class="table-responsive">
                      <table class="border-double table mb-0">
                        <tbody>
                          <tr>
                              <td style="width: 20%">Nama Perusahaan :</td>
                              <td>
                                 <?=$tempNama?>
                              </td>
                          </tr>
                              <td>Status Kantor Perusahaan :</td>
                              <td>
                                  <?=$tempStatus?>
                              </td>
                          <tr>
                          </tr>
                              <td>Alamat :</td>
                              <td>
                                 <?=$tempAlamat?>
                              </td>
                          <tr>
                              <td>Kota :</td>
                              <td>
                                 <?=$tempKota?>
                              </td>
                          </tr>
                              <td>Provinsi :</td>
                              <td>
                                 <?=$rekanan->getField("REGION")?>
                              </td>
                          <tr>
                          </tr>
                              <td>Kodepos :</td>
                              <td>
                                 <?=$rekanan->getField("KODEPOS")?>
                              </td>
                          <tr>
                              <td>No. telepon :</td>
                              <td>
                                 <?=$tempTelepon?>
                              </td>
                          </tr>
                          <tr>
                              <td>No. Fax :</td>
                              <td>
                                  <?=$tempFax?>
                              </td>
                          </tr>
                          <tr>
                              <td>Kontak Person :</td>
                              <td>
                                  <?=$tempKontakPerson?> &nbsp;&nbsp;Hp. :  <?=$tempKontakPersonHp?>
                              </td>
                          </tr>
                          <tr>
                              <td>E-mail :</td>
                              <td>
                                  <?=$tempMail?>
                              </td>
                          </tr>
                          <tr>
                              <td>Website :</td>
                              <td>
                                  <?=$tempWebsite?>
                              </td>
                          </tr>
                          <tr>
                              <td>Kualifikasi :</td>
                              <td>
                                  <?=$tempKualifikasi?>
                              </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card mb-1 border-blue border-darken-1">
                <div class="card-content">
                  <div class="p-1">
                    <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                      <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>NPWP</strong>
                      <span class="badge badge-pill badge-danger">Wajib</span>
                    </div> 
                    <div class="table-responsive">
                      <table class="border-double table mb-0"> 
                          <tbody> 
                              <tr>  
                              	<td style="width: 20%">NPWP :</td>
                                  <td>
                                      <?=$tempNPWP?>
                                  </td>
                              </tr>
                              <tr>  
                              	<td>File NPWP :</td>
                                  <td>
                                     <?php
          						   if($tempLinkFileTempNPWP == '')
          						   {
          						   }
          						   else{
          							$arrFile = explode(";", $tempLinkFileTempNPWP);
          							for($iFile=0;$iFile<count($arrFile);$iFile++)
          							{
          							?>
          									<a href="<?=$FILE_DIR.$arrFile[$iFile]?>" class="taut" target="_blank">Download</a>
          							<?php
          								}
          						   }
          							?>
                                  </td>
                              </tr>
                              <tr>  
                              </tr>
                          </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card mb-1 border-blue border-darken-1">
                <div class="card-content">
                  <div class="p-1">
                    <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                      <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>PKP</strong>
                      <span class="badge badge-pill badge-danger">jika ada</span>
                    </div> 
                    <div class="table-responsive">
                      <table class="border-double table mb-0"> 
                          <tbody>
                              <tr> 
                                  <td style="width: 20%">Nomor Pengukuhan :
                                  </td>
                                  <td>
                                     <?=$rekanan->getField("PKP");?>
                                     <?=$tempNama?>
                                  </td>
                              </tr>
                              <tr>  
                                  <td>Tanggal :</td>
                                  <td>
                                    <?=$tempPKPTanggal?>
                                  </td>
                              </tr>
                              <tr>  
                                  <td>File PKP :</td>
                                  <td>
                                     <?php
                                     if($tempLinkFileTempPKP == '')
                                     {
                                     }
                                     else{
                                      $arrFile = explode(";", $tempLinkFileTempPKP);
                                      for($iFile=0;$iFile<count($arrFile);$iFile++)
                                      {
                                      ?>
                                              <a href="<?=$FILE_DIR.$arrFile[$iFile]?>" class="taut" target="_blank">Download</a>
                                      <?php
                                          }
                                     }
                                      ?>
                                  </td>
                              </tr> 
                              <tr>  
                              </tr>
                          </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <?php
                  $rekanan_akta->selectByParams(array("REKANAN_ID"=>$reqId, "AKTA_TYPE_ID" => 1),-1,-1);
                  $rekanan_akta->firstRow();
                  $tempAktaTypeId = $rekanan_akta->getField("AKTA_TYPE_ID");
                  $tempNomorLandasan = $rekanan_akta->getField("NOMOR");
                  $tempTanggalLandasan = getFormattedDateJson($rekanan_akta->getField("TANGGAL"));
                  $tempNotarisLandasan = $rekanan_akta->getField("NOTARIS");
                  $tempLinkFileTempAktaPendirian = $rekanan_akta->getField("PATH_FILE");
              ?>
              <div class="card mb-1 border-blue border-darken-1">
                <div class="card-content">
                  <div class="p-1">

                    <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                      <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Landasan Hukum</strong>
                    </div> 
                    <div class="alert alert-danger">Akta Pendirian <span class="badge badge-pill badge-danger">wajib</span></div>
                    <div class="table-responsive">
                      <table class="border-double table mb-0">   
                          <tbody>
                              <tr> 
                                  <td style="width: 20%">Nomor Akta :
                                  </td>
                                  <td>
                                      <?=$tempNomorLandasan?>
                                  </td>
                              </tr>
                              <tr> 
                                  <td>Tanggal :</td>
                                  <td>
                                      <?=$tempTanggalLandasan?>
                                  </td>
                              </tr>
                              <tr> 
                                  <td>Nama Notaris :</td>
                                  <td>
                                     <?=$tempNotarisLandasan?>
                                  </td>
                              </tr>
                              <tr> 
                                  <td>File :</td>
                                  <td>
                                     <?php
                                     if($tempLinkFileTempAktaPendirian == '')
                                     {
                                     }
                                     else{
                                      $arrFile = explode(";", $tempLinkFileTempAktaPendirian);
                                      for($iFile=0;$iFile<count($arrFile);$iFile++)
                                      {
                                      ?>
                                              <a href="<?=$FILE_DIR_LANDASAN_HUKUM.$arrFile[$iFile]?>" class="taut" target="_blank">Download</a>
                                      <?php
                                          }
                                     }
                                      ?>
                                  </td>
                              </tr>
                          </tbody>
                      </table>
                    </div>
                      <?php
                      $rekanan_akta_perubahan = new RekananAkta();
                      $reqHitung = $rekanan_akta_perubahan->getCountByParams(array("REKANAN_ID"=>$reqId, "AKTA_TYPE_ID" => "2"));

                      $rekanan_akta_perubahan_terakhir = new RekananAkta();
                      $rekanan_akta_perubahan_terakhir->selectByParams(array("REKANAN_ID"=>$reqId, "AKTA_TYPE_ID" => "2"),-1,-1);
                      $rekanan_akta_perubahan_terakhir->firstRow();
                      if($reqHitung >0)
                      {
                      ?>
                    <div class="alert alert-danger">Akta Perubahan Terakhir <span class="badge badge-pill badge-danger">jika ada</span></div>
                    <div class="table-responsive">
                      <table class="border-double table mb-0">  
                          <tbody>
                              <tr> 
                                  <td style="width: 20%">Nomor Akta :
                                  </td>
                                  <td>
                                      <?=$rekanan_akta_perubahan_terakhir->getField("NOMOR");?>
                                  </td>
                              </tr>
                              <tr>  
                                  <td>Tanggal :</td>
                                  <td>
                                     <?=getFormattedDate($rekanan_akta_perubahan_terakhir->getField("TANGGAL"));?>
                                  </td>
                              </tr>
                              <tr>  
                                  <td>Nama Notaris :</td>
                                  <td>
                                      <?=$rekanan_akta_perubahan_terakhir->getField("NOTARIS");?>
                                  </td>
                              </tr>
                              <tr>  
                                  <td>File :</td>
                                  <td>
                                     <?
                                     if($rekanan_akta_perubahan_terakhir->getField("PATH_FILE") == '')
                                     {
                                     }
                                     else{
                                      $arrFile = explode(";", $rekanan_akta_perubahan_terakhir->getField("PATH_FILE"));
                                      for($iFile=0;$iFile<count($arrFile);$iFile++)
                                      {
                                      ?>
                                              <a href="<?=$FILE_DIR_LANDASAN_HUKUM.$arrFile[$iFile]?>" class="taut" target="_blank">Download</a>
                                      <?php
                                          }
                                     }
                                      ?>
                                  </td>
                              </tr> 
                          </tbody>
                      </table>
                    </div>
                      <?php
                      }
                      else
                      {} 
                    
                          $rekanan_sertifikat->selectByParams(array("REKANAN_ID"=>$reqId, "SERTIFIKAT_TIPE"=>"PENGESAHAN_BADAN_USAHA"),-1,-1);
                          $rekanan_sertifikat->firstRow();
                          $reqPengesahanSertifikatId = $rekanan_sertifikat->getField("REKANAN_SERTIFIKAT_ID");
                          $reqNomorPengesahan = $rekanan_sertifikat->getField("NOMOR");
                          $reqTanggalPengesahan = ($rekanan_sertifikat->getField("TANGGAL"));
                          $reqTanggalBerlakuPengesahan = ($rekanan_sertifikat->getField("BERLAKU"));
                          $reqLinkFilePengesahanTempNama = $rekanan_sertifikat->getField("NAMA_FILE");
                          $reqLinkFilePengesahanTemp= $rekanan_sertifikat->getField("PATH_FILE");
                          $reqLinkFilePengesahanTempTipe= $rekanan_sertifikat->getField("TIPE");
                          $reqLinkFilePengesahanTempUkuran= $rekanan_sertifikat->getField("UKURAN");
                      ?>
                    <div class="alert alert-danger">Pengesahan Badan Hukum <span class="badge badge-pill badge-danger">jika ada</span></div>
                    <div class="table-responsive">
                      <table class="border-double table mb-0">   
                          <tbody>
                              <tr> 
                                  <td style="width: 20%">Nomor Sertifikat :
                                  </td>
                                  <td>
                                      <?=$reqNomorPengesahan;?>
                                  </td>
                              </tr>
                              <tr> 
                                  <td>Tanggal :</td>
                                  <td>
                                     <?=getFormattedDate($reqTanggalPengesahan);?>
                                  </td>
                              </tr>
                              <tr> 
                                  <td>Berlaku :</td>
                                  <td>
                                      <?=getFormattedDate($reqTanggalBerlakuPengesahan);?>
                                  </td>
                              </tr>
                              <tr> 
                                  <td> File :</td>
                                  <td>
                                       <?php
                                       if($reqLinkFilePengesahanTemp=='')
                                       {}
                                       else
                                       {
                                          $arrFile = explode(";", $reqLinkFilePengesahanTemp);
                                          for($iFile=0;$iFile<count($arrFile);$iFile++)
                                          {
                                      ?>
                                              <a href="<?=$FILE_DIR_LANDASAN_HUKUM.$arrFile[$iFile]?>" class="taut" target="_blank">Download</a>
                                      <?php
                                          }
                                      }
                                      ?>
                                  </td>
                              </tr>
                          </tbody>
                      </table>
                    </div>
                      <?php
                          $rekanan_sertifikat_domisili->selectByParams(array("REKANAN_ID"=>$reqId, "SERTIFIKAT_TIPE"=>"SURAT_DOMISILI"),-1,-1);
                          $rekanan_sertifikat_domisili->firstRow();
                          $reqDomisiliId = $rekanan_sertifikat_domisili->getField("REKANAN_SERTIFIKAT_ID");
                          $reqNomorDomisili = $rekanan_sertifikat_domisili->getField("NOMOR");
                          $reqTanggalDomisili = ($rekanan_sertifikat_domisili->getField("TANGGAL"));
                          $reqTanggalBerlakuDomisili = ($rekanan_sertifikat_domisili->getField("BERLAKU"));
                          $reqLinkFileDomisiliTempNama = $rekanan_sertifikat_domisili->getField("NAMA_FILE");
                          $reqLinkFileDomisiliTemp= $rekanan_sertifikat_domisili->getField("PATH_FILE");
                          $reqLinkFileDomisiliTempTipe= $rekanan_sertifikat_domisili->getField("TIPE");
                          $reqLinkFileDomisiliTempUkuran= $rekanan_sertifikat_domisili->getField("UKURAN");
                      ?>
                    <div class="alert alert-danger">Surat Domisili <span class="badge badge-pill badge-danger">jika ada</span></div>
                    <div class="table-responsive">
                      <table class="border-double table mb-0"> 
                          <tbody>
                              <tr> 
                                  <td style="width: 20%">Nomor Sertifikat :
                                  </td>
                                  <td>
                                      <?=$reqNomorDomisili;?>
                                  </td>
                              </tr>
                              <tr> 
                                  <td>Tanggal :</td>
                                  <td>
                                     <?=getFormattedDate($reqTanggalDomisili);?>
                                  </td>
                              </tr>
                              <tr> 
                                  <td>Berlaku :</td>
                                  <td>
                                      <?=getFormattedDate($reqTanggalBerlakuDomisili);?>
                                  </td>
                              </tr>
                              <tr> 
                                  <td>File :</td>
                                  <td>
                                       <?php
                                       if($reqLinkFileDomisiliTemp=='')
                                       {}
                                       else
                                       {
                                          $arrFile = explode(";", $reqLinkFileDomisiliTemp);
                                          for($iFile=0;$iFile<count($arrFile);$iFile++)
                                          {
                                      ?>
                                              <a href="<?=$FILE_DIR_LANDASAN_HUKUM.$arrFile[$iFile]?>" class="taut" target="_blank">Download</a>
                                      <?php
                                          }
                                       }
                                      ?>
                                  </td>
                              </tr>
                          </tbody>
                      </table>
                    </div>
                       <?php
                          $rekanan_sertifikat_tanda_daftar->selectByParams(array("REKANAN_ID"=>$reqId, "SERTIFIKAT_TIPE"=>"TANDA_DAFTAR_PERUSAHAAN"),-1,-1);
                          $rekanan_sertifikat_tanda_daftar->firstRow();
                          $reqTandaDaftarId = $rekanan_sertifikat_tanda_daftar->getField("REKANAN_SERTIFIKAT_ID");
                          $reqNomorTandaDaftar = $rekanan_sertifikat_tanda_daftar->getField("NOMOR");
                          $reqTanggalTandaDaftar = ($rekanan_sertifikat_tanda_daftar->getField("TANGGAL"));
                          $reqTanggalBerlakuTandaDaftar = ($rekanan_sertifikat_tanda_daftar->getField("BERLAKU"));
                          $reqLinkFileTandaDaftarTempNama = $rekanan_sertifikat_tanda_daftar->getField("NAMA_FILE");
                          $reqLinkFileTandaDaftarTemp= $rekanan_sertifikat_tanda_daftar->getField("PATH_FILE");
                          $reqLinkFileTandaDaftarTempTipe= $rekanan_sertifikat_tanda_daftar->getField("TIPE");
                          $reqLinkFileTandaDaftarTempUkuran= $rekanan_sertifikat_tanda_daftar->getField("UKURAN");
                      ?>
                    <div class="alert alert-danger">Tanda Daftar Perusahaan <span class="badge badge-pill badge-danger">jika ada</span></div>
                    <div class="table-responsive">
                      <table class="border-double table mb-0">  
                          <tbody>
                              <tr> 
                                  <td style="width: 20%">Nomor Sertifikat :
                                  </td>
                                  <td>
                                      <?=$reqNomorTandaDaftar;?>
                                  </td>
                              </tr>
                              <tr> 
                                  <td>Tanggal :</td>
                                  <td>
                                     <?=getFormattedDate($reqTanggalTandaDaftar);?>
                                  </td>
                              </tr>
                              <tr> 
                                  <td>Berlaku :</td>
                                  <td>
                                      <?=getFormattedDate($reqTanggalBerlakuTandaDaftar);?>
                                  </td>
                              </tr>
                              <tr> 
                                  <td> File : </td>
                                  <td>
                                      <?php
                                       if($reqLinkFileTandaDaftarTemp=='')
                                       {}
                                       else
                                       {
                                          $arrFile = explode(";", $reqLinkFileTandaDaftarTemp);
                                          for($iFile=0;$iFile<count($arrFile);$iFile++)
                                          {
                                      ?>
                                              <a href="<?=$FILE_DIR_LANDASAN_HUKUM.$arrFile[$iFile]?>" class="taut" target="_blank">Download</a>
                                      <?php
                                          }
                                       }
                                      ?>
                                  </td>
                              </tr>
                          </tbody>
                      </table>
                    </div>

                  </div>
                </div>
              </div>

              <?php
              	$rekanan_ijin_usaha->selectByParams(array("REKANAN_ID" => $reqId));
  				$rekanan_ijin_usaha->firstRow();
  				$tempNomor = $rekanan_ijin_usaha->getField("NO_IJIN");
  				$tempTanggalIjin = getFormattedDate($rekanan_ijin_usaha->getField("TANGGAL"));
  				$tempTanggalBerakhir = getFormattedDate($rekanan_ijin_usaha->getField("TANGGAL_BERAKHIR"));
  				$tempLinkFileTempIjinSiup = $rekanan_ijin_usaha->getField("PATH_FILE");
  				$tempBidang = $rekanan_ijin_usaha->getField("IJIN_USAHA");
  				$tempInstansi = $rekanan_ijin_usaha->getField("INSTANSI");
  			?>
              <div class="card mb-1 border-blue border-darken-1">
                <div class="card-content">
                  <div class="p-1">
                    <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                      <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Ijin Usaha / OSS (SIUP)</strong>
                      <span class="badge badge-pill badge-danger">wajib</span>
                    </div> 
                    <div class="table-responsive">
                      <table class="border-double table mb-0">  
                          <tbody>
                              <tr> 
                              	<td style="width: 20%">Nomor ijin :</td>
                                  <td>
                                     <?=$tempNomor?>
                                  </td> 
                              </tr>
                              <tr>  
                              	<td>Tanggal ijin :</td>
                                  <td>
                                      <?=$tempTanggalIjin?>
                                  </td>
                              </tr>
                              <tr>  
                              	<td>Tanggal berakhir :</td>
                                  <td>
                                      <?=$tempTanggalBerakhir?>
                                  </td>
                              </tr>
                              <tr>  
                              	<td>Instansi pemberi ijin :</td>
                                  <td>
                                      <?=$tempInstansi?>
                                  </td>
                              </tr>
                              <tr>  
                              	<td>File :</td>
                                  <td>
                                     <?php
          						   if($tempLinkFileTempIjinSiup == '')
          						   {
          						   }
          						   else{
          							$arrFile = explode(";", $tempLinkFileTempIjinSiup);
          							for($iFile=0;$iFile<count($arrFile);$iFile++)
          							{
          							?>
          									<a href="<?=$FILE_DIR_IJIN_USAHA.$arrFile[$iFile]?>" class="taut" target="_blank">Download</a>
          							<?php
          								}
          						   }
          							?>
                                  </td>
                              </tr>
                              <tr>  
                              	<td>Bidang usaha :</td>
                                  <td>
                                     <table class="table table-striped" id="tbl_bidang">
                                      <tbody>
                                        <tr>
                                          <td width="50">No</td>
                                          <td>Bidang Usaha</td>
                                        </tr>
                                        <?php
          								$rekanan_bidang_usaha->selectByParamsMonitoring(array("REKANAN_ID" =>$reqId),-1,-1, " AND IJIN_USAHA_ID NOT IN(99)");
          								//echo $rekanan_bidang_usaha->query;exit;
          								$no = 1;
                                          while($rekanan_bidang_usaha->nextRow())
                                          {
                                          ?>
                                            <tr>
                                            	<td><?=$no?></td>
                                              <td><?=$rekanan_bidang_usaha->getField("NAMA")?></td>
                                            </tr>
                                         <?php
          							   		$no++;
                                          }
                                          ?>
                                      </tbody>
                                    </table>
                                  </td>
                              </tr> 
                          </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <?php
  				$allRecord_komisaris = $rekanan_pengurus_komisaris->getCountByParams(array("REKANAN_ID"=>$reqId,"TIPE"=>1));
  				$rekanan_pengurus_komisaris ->selectByParams(array("REKANAN_ID"=>$reqId,"TIPE"=>1),-1,-1);
                  $rekanan_cek = new Rekanan();
                  $rekanan_cek->selectByParams(array("REKANAN_ID"=>$reqId),-1,-1);
                  $rekanan_cek->firstRow();

                  $tempRekananTipeID = $rekanan_cek->getField("REKANAN_TIPE_ID"); // 1 PT, 2 CV, 3 Firma, 4 Koperasi, 5 UD, 6 Lainnya
  			?>
              <div class="card mb-1 border-blue border-darken-1">
                <div class="card-content">
                  <div class="p-1">
                    <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                      <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Pimpinan Perusahaan</strong>  
                      <span class="badge badge-pill badge-danger">wajib</span>
                    </div> 
                    <div class="alert alert-danger"> Komisaris 
                    <?php 
                      if ($tempRekananTipeID == '1') {
                           echo '<span class="badge badge-pill badge-danger">wajib</span>';
                       } else {
                           echo '<span class="badge badge-pill badge-danger">jika ada</span>';
                       } 
                       ?>
                    </div>
                    <div class="table-responsive">
                      <table class="border-double table mb-0">
                          <tbody>
                            <tr>
                              <td width="30">No.</td>
                              <td width="144">Nama</td>
                              <td width="110">No. KTP</td>
                              <td width="200">Jabatan dalam Perusahaan</td>
                              <td width="200">File</td>
                            </tr>
                            <tr class="judul-kolom2">
                            </tr>
                            <?php
                              if($allRecord_komisaris > 0){
                                  $no_komisaris = 1;
                                  while($rekanan_pengurus_komisaris->nextRow()){
                            ?>
                            <tr>
                                <td><?=$no_komisaris?></td>
                                <td><?=$rekanan_pengurus_komisaris->getField("NAMA")?></td>
                                <td><?=$rekanan_pengurus_komisaris->getField("KTP")?></td>
                                <td><?=$rekanan_pengurus_komisaris->getField("JABATAN")?></td>
                                <td>
                               		<?php
          							if($rekanan_pengurus_komisaris->getField("PATH_FILE") =='')
          						 	{}
          						 	else
          						 	{
          							$arrFile = explode(";", $rekanan_pengurus_komisaris->getField("PATH_FILE"));
          							for($iFile=0;$iFile<count($arrFile);$iFile++)
          							{
          							?>
          									<a href="<?=$FILE_DIR_KOMISARIS.$arrFile[$iFile]?>" class="taut" target="_blank">Download</a>
          							<?php
          								}
          							}
          							?>
                                </td>
                            </tr>
                            <? $no_komisaris++;}}else{
                            ?>
                            <tr>
                              <td colspan="5" align="center"><span class="merah">.: data belum ada :.</span></td>
                            </tr>
                            <?php }?>
                          </tbody>
                      </table>
                    </div>
                    <?php
                  	$allRecord_direksi = $rekanan_pengurus_direksi->getCountByParams(array("REKANAN_ID"=>$reqId,"TIPE"=>2));
      				$rekanan_pengurus_direksi ->selectByParams(array("REKANAN_ID"=>$reqId,"TIPE"=>2),-1,-1);
      			  ?>
                    <div class="alert alert-danger"> Direksi <span class="badge badge-pill badge-danger">wajib</span></div>
                    <div class="table-responsive">
                      <table class="border-double table mb-0"> 
                          <tbody>
                            <tr>
                              <td width="30">No.</td>
                              <td width="144">Nama</td>
                              <td width="110">No. KTP</td>
                              <td width="200">Jabatan dalam Perusahaan</td>
                              <td width="200">File</td>
                            </tr>
                            <?php

      							if($allRecord_direksi > 0){
      								$no_direksi = 1;
      								while($rekanan_pengurus_direksi->nextRow()){
      					  ?>
      					  <tr>
      						  <td><?=$no_direksi?></td>
      						  <td><?=$rekanan_pengurus_direksi->getField("NAMA")?></td>
      						  <td><?=$rekanan_pengurus_direksi->getField("KTP")?></td>
      						  <td><?=$rekanan_pengurus_direksi->getField("JABATAN")?></td>
                                <td>
                               		<?php
      								if($rekanan_pengurus_direksi->getField("PATH_FILE") =='')
      								{}
      								else
      								{
      									$arrFile = explode(";", $rekanan_pengurus_direksi->getField("PATH_FILE"));
      									for($iFile=0;$iFile<count($arrFile);$iFile++)
      									{
      								?>
      										<a href="<?=$FILE_DIR_DIREKSI.$arrFile[$iFile]?>" class="taut" target="_blank">Download</a>
      								<?php
      									}
      								}
      								?>
                                </td>
      					  </tr>
      					  <? $no_direksi++;}}else{
      					  ?>
      					  <tr>
      						<td colspan="5" align="center"><span class="merah">.: data belum ada :.</span></td>
      					  </tr>
      					  <?php }?>
                          </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card mb-1 border-blue border-darken-1">
                <div class="card-content">
                  <div class="p-1">
                    <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                      <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Rekening Koran</strong>
                      <span class="badge badge-pill badge-danger">wajib</span>   
                    </div> 
                    <div class="table-responsive">
                      <table class="border-double table mb-0">
                          <tbody>
                            <tr>
                              <td>Nomor Rekening</td>
                              <td>Bank</td>
                              <td>Mata Uang</td>
                              <td>Nilai</td>
                              <td>Tahun</td>
                            </tr>
                            <?php
          				  		$rekanan_rekening_koran = new RekananRekeningKoran();
          				  		$rekanan_rekening_koran->selectByParams(array("REKANAN_ID" => $reqId),-1,-1);
          						while($rekanan_rekening_koran->nextRow()){
          				  ?>
          				  <tr>
          					  	<td><?=$rekanan_rekening_koran->getField("NOMOR")?></td>
                                  <td><?=$rekanan_rekening_koran->getField("NAMA")?></td>
                                  <td><?=$rekanan_rekening_koran->getField("MATAUANG")?></td>
                                  <td><?=numberToIna($rekanan_rekening_koran->getField("NILAI"))?></td>
                                  <td><?=$rekanan_rekening_koran->getField("TAHUN")?></td>
          				  </tr>
          				  <?php }
          				  ?>
                          </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card mb-1 border-blue border-darken-1">
                <div class="card-content">
                  <div class="p-1">
                    <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                      <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Kepemilikan Saham</strong>   
                      <span class="badge badge-pill badge-danger">wajib</span>
                    </div> 
                    <div class="table-responsive">
                      <table class="border-double table mb-0"> 
                          <tbody>
                            <tr>
                              <td>Pemegang Saham</td>
                              <td>No. KTP/NPWP</td>
                              <td>Alamat</td>
                              <td>Prosentase(%)</td>
                            </tr>
                            <?php
          				  		$rekanan_saham->selectByParams(array("REKANAN_ID" => $reqId),-1,-1);
          						while($rekanan_saham->nextRow()){
          				  ?>
          				  <tr>
          					  	<td><?=$rekanan_saham->getField("NAMA")?></td>
                                  <td><?=$rekanan_saham->getField("KTP")?></td>
                                  <td><?=$rekanan_saham->getField("ALAMAT")?></td>
                                  <td><?=$rekanan_saham->getField("JUMLAH_SAHAM")?></td>
          				  </tr>
          				  <?php }
          				  ?>
                          </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <?
              	$rekanan_ijin_usaha->selectByParams(array("REKANAN_ID" => $reqId, "IJIN_USAHA_ID" => 99 ));
  				$rekanan_ijin_usaha->firstRow();
  				//echo $rekanan_ijin_usaha->query;exit;

  				$reqIjinUsahaId = $rekanan_ijin_usaha->getField("REKANAN_IJIN_USAHA_ID");
  				$reqNomor = $rekanan_ijin_usaha->getField("NO_IJIN");
  				$reqTanggalIjin = $rekanan_ijin_usaha->getField("TANGGAL");
  				$reqTanggalBerakhir = $rekanan_ijin_usaha->getField("TANGGAL_BERAKHIR");
  				$reqInstansi = $rekanan_ijin_usaha->getField("INSTANSI");
  				$reqBidang = $rekanan_ijin_usaha->getField("IJIN_USAHA");
  				$reqLinkFileTemp= $rekanan_ijin_usaha->getField("PATH_FILE");
  				$reqLinkFileTempTipe= $rekanan_ijin_usaha->getField("TIPE");
  				$reqLinkFileTempUkuran= $rekanan_ijin_usaha->getField("UKURAN");
  				$reqLinkFileTempNama= $rekanan_ijin_usaha->getField("NAMA_FILE");
  				//echo $_SESSION['KODE_VALIDASI_SET_TO'];
  			?>
              <div class="card mb-1 border-blue border-darken-1">
                <div class="card-content">
                  <div class="p-1">
                    <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                      <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Sertifikat Badan Usaha</strong>  
                      <span class="badge badge-pill badge-danger">jika perusahaan jasa konstruksi</span>
                    </div> 
                    <div class="table-responsive">
                      <table class="border-double table mb-0">
                          <tbody>
                            <tr>
                              <td style="width: 20%"> Nomor Sertifikat :</td>
                              <td>
                                 <?=$reqNomor?>
                              </td>
                            </tr>
                            <tr> 
                              <td>Tanggal Sertifikat :</td>
                              <td>
                                 <?=getFormattedDate($reqTanggalIjin)?>
                              </td>
                            </tr>
                            <tr> 
                              <td>Tanggal Berakhir :</td>
                              <td>
                                  <?=getFormattedDate($reqTanggalBerakhir)?>
                              </td>
                            </tr>
                            <tr> 
                              <td> Nama Penanda Tangan : </td>
                              <td>
                                  <?=$reqInstansi?>
                              </td>
                            </tr>
                            <tr> 
                          	<td>File :</td>
                              <td>
                                   <?php
          						 if($reqLinkFileTemp=='')
          						 {}
          						 else
          						 {
          							$arrFile = explode(";", $reqLinkFileTemp);
          							for($iFile=0;$iFile<count($arrFile);$iFile++)
          							{
          						?>
          								<a href="<?=$FILE_DIR_IJIN_USAHA.$arrFile[$iFile]?>" class="taut" target="_blank">Download</a>
          						<?php
          							}
          						 }
          						?>
                              </td>
                            </tr>
                          </tbody>
                      </table>
                    </div>
                    <h4>Bidang Usaha</h4>
                    <div class="table-responsive">
                      <table class="border-double table mb-0">
                       <tbody>
                        <tr>
                          <td width="50">No</td>
                          <td>Bidang Usaha</td>
                        </tr>
                        <?php
                          $rekanan_bidang_usaha_sbu->selectByParamsMonitoring(array("REKANAN_ID" =>$reqId, "IJIN_USAHA_ID "=> "99"));
          				//echo $rekanan_bidang_usaha_sbu->query;exit;
                          $no = 1;
                          while($rekanan_bidang_usaha_sbu->nextRow())
                          {
                          ?>
                            <tr>
                              <td><?=$no?></td>
                              <td><?=$rekanan_bidang_usaha_sbu->getField("NAMA")?></td>
                            </tr>
                         <?php
                              $no++;
                          }
                          ?>
                       </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-actions">
                  <input type="hidden" name="reqNomorValidasi" value="<?=$reqKode?>">
                  <input type="hidden" name="reqEmail" value="<?=$tempMail?>">
                  <input type="hidden" name="reqId" value="<?=$reqId?>">
                  <input type="hidden" name="reqRekananNama" value="<?=$tempRekananNama?>">
                  <input type="hidden"  name="submitSimpan" value="Simpan" />
                  <a href="main/index/validasi" class="btn btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a> 
                  <?
                  $user_login->selectByParams(array("REKANAN_ID" => $reqId));
                  $user_login->firstRow();

                  $user_status = $user_login->getField("USER_STATUS");

                  // if($rekanan->getField("STATUS_VALIDASI") == 1)
                  if($user_status == 1)
                  { ?>
                  <button type="submit" style="display: none" class="btn btn-primary mr-1 text-white" id="btnValidasi"><i class="fa fa-check-square-o"></i> VALIDASI</button>
                  <!-- <a title="#" id="btnRevisi"  onclick="revisiPendaftaran()" class="btn btn-info mr-1 text-white"><i class="fa fa-repeat"></i> Dikembalikan</a> -->
                  <a id="btnSertifikat" href="main/loadUrl/report/vms_pdf/?reqId=<?=$reqId?>&kode=<?=$reqKode?>" onclick="confirm('Cetak Sertifikat ?')" class="btn btn-info mr-1 text-white"><i class="fa fa-print"></i> Cetak Sertifikat</a>
                  <?php 
                  }
                  else
                  {
                  ?>
                  <button type="submit" class="btn btn-primary mr-1 text-white" id="btnValidasi"><i class="fa fa-check-square-o"></i> VALIDASI</button>
                  <a title="#" id="btnRevisi"  onclick="revisiPendaftaran()" class="btn btn-info mr-1 text-white"><i class="fa fa-repeat"></i> Dikembalikan</a>
                  <a id="btnSertifikat" href="main/loadUrl/report/vms_pdf/?reqId=<?=$reqId?>&kode=<?=$reqKode?>" style="display: none" onclick="confirm('Cetak Sertifikat ?')" class="btn btn-info mr-1 text-white"><i class="fa fa-print"></i> Cetak Sertifikat</a>
                  <?php
                  }
                  ?>
              </div> 


          </div>
        </div>
        </form>

      </div>
    </div> 
  </div>   
<?php 
} ?>
 
 