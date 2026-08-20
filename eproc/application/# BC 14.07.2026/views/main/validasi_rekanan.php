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

// $reqKode = $this->input->post("reqKode");
$reqKode = explode(' :: ',$this->input->post("reqKode"));
$rekanan->selectByParams(array("A.KODE"=>$reqKode[0]),-1,-1);
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
$tempStatusValidasi = $rekanan->getField("STATUS_VALIDASI");

?>
<script type="text/javascript">
$(document).ready(function() {
	$(function(){
			$('#ff').form({
        // url:'rekanan_json/validasi_rekanan',
				url:'rekanan_json/validasi_rekanan_teruskan',
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
          $("#note1").hide();
          $("#note11").hide();
          // $("#btnRevisi").hide();
          // $("#btnSertifikat").show();
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
<style type="text/css">.badge[class*='badge-'] span { bottom: 0px !important; }</style>
<?php 
if ($reqRekananTipeId == '7') { // Konsultan Perorangan ?>
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
                    <!-- <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                      <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Profil Perorangan</strong>
                      <span class="badge badge-pill badge-danger">wajib</span>
                    </div>  -->
                    <div class="table-responsive">
                      <a onClick="openAdd('main/loadUrl/main/data_rekanan/?reqId=<?=$reqId?>');" class="btn btn-danger" style="width: 100%; color: #fff;">  Lihat Data Perusahaan </a> 
                       <br>
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

              <div class="row"> 
                <div class="col-md-12 col-sm-12">
                  <div class="card">
                    <div class="card-content collapse show border-info border-darken-2">
                      <div class="card-body">
                        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                          <span class="alert-icon"><i class="fa fa-th"></i></span>  
                          <strong>Checklist Kelengkapan</strong>  
                        </div> 
                            <?php
                              $jumlahUncentang = 0;
                              $rekanan = new Rekanan();
                              $rekanan_keuangan = new Rekanan();
                              $rekanan_perpajakan = new Rekanan();
                              $rekanan_teknis = new Rekanan();
                              if ($reqRekananTipeId == '7') { // Perorangan
                                $rekanan->selectByParamsKonfirmasiPerorangan($reqId);
                                $rekanan_perpajakan->selectByParamsKonfirmasiPeroranganDataPerpajakan($reqId);
                                $rekanan_teknis->selectByParamsKonfirmasiPeroranganDataTeknis($reqId);
                              } else {
                                $rekanan->selectByParamsKonfirmasiDataAdmin($reqId);
                                $rekanan_keuangan->selectByParamsKonfirmasiDataKeuangan($reqId);
                                $rekanan_perpajakan->selectByParamsKonfirmasiDataPerpajakan($reqId);
                                $rekanan_teknis->selectByParamsKonfirmasiDataTeknis($reqId);
                              }
                              $no=1;
                              if ($reqRekananTipeId == '7') { // Perorangan ?>
                              <h4>Data Administrasi</h4>
                              <table class="table table-striped">
                              <?php
                                while($rekanan->nextRow())
                                {
                              ?>
                                  <tr>
                                    <td style="width: 2%"><?=$no;?></td>
                                    <td style="width: 83%">
                                      <?=$rekanan->getField("NAMA") ?>
                                      <?php 
                                      if ($rekanan->getField("WAJIB") == '*') {
                                         echo '<span class="color:red">'.$rekanan->getField("WAJIB").'</span>';
                                      } else {
                                        echo "";
                                      } ?> 
                                    </td>
                                    <td style="width: 15%">
                                      <img class="simbol" src="images/<?=$rekanan->getField("SIMBOL")?>.png">
                                    </td>
                                  </tr>
                                    <?php
                                 if($rekanan->getField("SIMBOL") == "uncentang" && $rekanan->getField("WAJIB") == '*')
                                  $jumlahUncentang++;
                                $no++;
                                } ?>
                            </table>
                              <?php
                              } else {  ?>
                              <h4>Data Administrasi</h4>
                              <table class="table table-striped">
                              <?php
                                while($rekanan->nextRow())
                                {
                              ?>
                                  <tr>
                                    <td style="width: 2%"><?=$no;?></td>
                                    <td style="width: 83%">
                                      <?=$rekanan->getField("NAMA") ?>
                                      <?php 
                                      if ($rekanan->getField("WAJIB") == '*') {
                                         echo '<span class="color:red">'.$rekanan->getField("WAJIB").'</span>';
                                      } else {
                                        echo "";
                                      } ?> 
                                    </td>
                                    <td style="width: 15%">
                                      <img class="simbol" src="images/<?=$rekanan->getField("SIMBOL")?>.png">
                                    </td>
                                  </tr>
                                    <?php
                                 if($rekanan->getField("SIMBOL") == "uncentang" && $rekanan->getField("WAJIB") == '*')
                                  $jumlahUncentang++;
                                $no++;
                                } ?>
                            </table>
                          <?php
                              }
                            ?>

                            <?php
                              $no=1;
                              if ($reqRekananTipeId == '7') { // Perorangan
                              } else {  ?>
                              <h4>Data Keuangan</h4>
                              <table class="table table-striped">
                              <?php
                                while($rekanan_keuangan->nextRow())
                                {
                              ?>
                                  <tr>
                                    <td style="width: 2%"><?=$no;?></td>
                                    <td style="width: 83%">
                                      <?=$rekanan_keuangan->getField("NAMA") ?>
                                     <?php 
                                      if ($rekanan_keuangan->getField("WAJIB") == '*') {
                                         echo '<span class="color:red">'.$rekanan_keuangan->getField("WAJIB").'</span>';
                                      } else {
                                        echo "";
                                      } ?> 
                                    </td>
                                    <td style="width: 15%">
                                      <img class="simbol" src="images/<?=$rekanan_keuangan->getField("SIMBOL")?>.png">
                                    </td>
                                  </tr>
                                    <?php
                                 if($rekanan_keuangan->getField("SIMBOL") == "uncentang" && $rekanan_keuangan->getField("WAJIB") == '*')
                                  $jumlahUncentang++;
                                $no++;
                                } ?>
                              </table>
                            <?php
                              }
                            ?>

                            <?php
                              $no=1;
                              if ($reqRekananTipeId == '7') { // Perorangan ?>
                              <h4>Data Perpajakan</h4>
                              <table class="table table-striped">
                              <?php
                                while($rekanan_perpajakan->nextRow())
                                {
                              ?>
                                  <tr>
                                    <td style="width: 2%"><?=$no;?></td>
                                    <td style="width: 83%">
                                      <?=$rekanan_perpajakan->getField("NAMA") ?>
                                       <?php 
                                      if ($rekanan_perpajakan->getField("WAJIB") == '*') {
                                         echo '<span class="color:red">'.$rekanan_perpajakan->getField("WAJIB").'</span>';
                                      } else {
                                        echo "";
                                      } ?> 
                                    </td>
                                    <td style="width: 15%">
                                      <img class="simbol" src="images/<?=$rekanan_perpajakan->getField("SIMBOL")?>.png">
                                    </td>
                                  </tr>
                                    <?php
                                 if($rekanan_perpajakan->getField("SIMBOL") == "uncentang" && $rekanan_perpajakan->getField("WAJIB") == '*')
                                  $jumlahUncentang++;
                                $no++;
                                } ?>
                              </table>
                              <?php
                              } else { ?>
                              <h4>Data Perpajakan</h4>
                              <table class="table table-striped">
                              <?php
                                while($rekanan_perpajakan->nextRow())
                                {
                              ?>
                                  <tr>
                                    <td style="width: 2%"><?=$no;?></td>
                                    <td style="width: 83%">
                                      <?=$rekanan_perpajakan->getField("NAMA") ?>
                                       <?php 
                                      if ($rekanan_perpajakan->getField("WAJIB") == '*') {
                                         echo '<span class="color:red">'.$rekanan_perpajakan->getField("WAJIB").'</span>';
                                      } else {
                                        echo "";
                                      } ?> 
                                    </td>
                                    <td style="width: 15%">
                                      <img class="simbol" src="images/<?=$rekanan_perpajakan->getField("SIMBOL")?>.png">
                                    </td>
                                  </tr>
                                    <?php
                                 if($rekanan_perpajakan->getField("SIMBOL") == "uncentang" && $rekanan_perpajakan->getField("WAJIB") == '*')
                                  $jumlahUncentang++;
                                $no++;
                                } ?>
                              </table>
                            <?php
                              }
                            ?>

                            <?php
                              $no=1;
                              if ($reqRekananTipeId == '7') { // Perorangan ?>
                              <h4>Data Teknis</h4>
                             <table class="table table-striped">
                              <?php 
                                while($rekanan_teknis->nextRow())
                                {
                              ?>
                                  <tr>
                                    <td style="width: 2%"><?=$no;?></td>
                                    <td style="width: 83%">
                                      <?=$rekanan_teknis->getField("NAMA") ?>
                                       <?php 
                                      if ($rekanan_teknis ->getField("WAJIB") == '*') {
                                         echo '<span class="color:red">'.$rekanan_teknis  ->getField("WAJIB").'</span>';
                                      } else {
                                        echo "";
                                      } ?> 
                                    </td>
                                    <td style="width: 15%">
                                      <img class="simbol" src="images/<?=$rekanan_teknis->getField("SIMBOL")?>.png">
                                    </td>
                                  </tr>
                                    <?php
                                 if($rekanan_teknis->getField("SIMBOL") == "uncentang" && $rekanan_teknis->getField("WAJIB") == '*')
                                  $jumlahUncentang++;
                                $no++;
                                } ?>
                              </table>
                              <?php
                              } else { ?> 
                             <h4>Data Teknis</h4>
                             <table class="table table-striped">
                              <?php 
                                while($rekanan_teknis->nextRow())
                                {
                              ?>
                                  <tr>
                                    <td style="width: 2%"><?=$no;?></td>
                                    <td style="width: 83%">
                                      <?=$rekanan_teknis->getField("NAMA") ?>
                                       <?php 
                                      if ($rekanan_teknis ->getField("WAJIB") == '*') {
                                         echo '<span class="color:red">'.$rekanan_teknis  ->getField("WAJIB").'</span>';
                                      } else {
                                        echo "";
                                      } ?> 
                                    </td>
                                    <td style="width: 15%">
                                      <img class="simbol" src="images/<?=$rekanan_teknis->getField("SIMBOL")?>.png">
                                    </td>
                                  </tr>
                                    <?php
                                 if($rekanan_teknis->getField("SIMBOL") == "uncentang" && $rekanan_teknis->getField("WAJIB") == '*')
                                  $jumlahUncentang++;
                                $no++;
                                } ?>
                              </table>
                            <?php  
                              }
                            ?>
                          
                        <?php 
                        if ($jumlahUncentang > 0) {
                           echo '<div class="alert alert-danger">
                                  <b> Kurang '.$jumlahUncentang.' data belum dilengkapi (tanda * data wajib diisi)  </b> 
                                </div>';
                        } ?>
               
                      </div>
                    </div>
                  </div>
                </div>
              </div>   
  
              <div class="form-actions">
                  <input type="hidden" name="reqNomorValidasi" value="<?=$reqKode[0]?>">
                  <input type="hidden" name="reqEmail" value="<?=$tempMail?>">
                  <input type="hidden" name="reqId" value="<?=$reqId?>">
                  <input type="hidden" name="reqRekananNama" value="<?=$tempRekananNama?>">
                  <input type="hidden"  name="submitSimpan" value="Simpan" />
                  <a href="main/index/validasi" class="btn btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a> 

                  <?php
                  $user_login->selectByParams(array("REKANAN_ID" => $reqId));
                  $user_login->firstRow();
                  $user_status = $user_login->getField("USER_STATUS"); 

                  if($tempStatusValidasi == 1)
                  { ?>
                    <a id="btnSertifikat" target="_blank" href="main/loadUrl/report/vms_pdf/?reqId=<?=$reqId?>&kode=<?=$reqKode[0]?>&rekanantipeid=7" onclick="if(confirm('Cetak Surat Keterangan Terdaftar (SKT) ?')) { return true; } else { return false; }" class="btn btn-info mr-1 text-white"><i class="fa fa-print"></i> Cetak Surat Keterangan Terdaftar (SKT)</a>
                  <?php 
                  } else if($tempStatusValidasi == 3) { // Posisi di user REKOMENDASI VMS
                    echo '<a class="btn btn-success mr-1 text-white" ><i class="fa fa-info-circle"></i> MENUNGGU REKOMENDASI</a>';
                  } else if($tempStatusValidasi == 4) { // Posisi di user APPROVAL VMS
                    echo '<a class="btn btn-info mr-1 text-white" ><i class="fa fa-g"></i> MENUNGGU APPROVAL</a>';
                  } else if ($tempStatusValidasi == 10 || $tempStatusValidasi == 0) { // Tolak, Melengkapi Data
                    echo '<label id="note1">Catatan</label><input type="text" class="form-control" id="note11" name="reqNote1" placeholder="Ketik disini" style="margin-bottom:2%">';
                    echo '<button type="submit" class="btn btn-primary mr-1 text-white" id="btnValidasi"><i class="fa fa-check-square-o"></i> VALIDASI & MINTA REKOMENDASI</button>';
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
                    <!-- <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                      <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Profil Perusahaan</strong> 
                    </div>  -->
                    <div class="table-responsive">
                      <a onClick="openAdd('main/loadUrl/main/data_rekanan/?reqId=<?=$reqId?>');" class="btn btn-danger" style="width: 100%; color: #fff;">  Lihat Data Perusahaan </a> 
                       <br>
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

              <div class="row"> 
                <div class="col-md-12 col-sm-12">
                  <div class="card">
                    <div class="card-content collapse show border-info border-darken-2">
                      <div class="card-body">
                        <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                          <span class="alert-icon"><i class="fa fa-th"></i></span>  
                          <strong>Checklist Kelengkapan</strong>  
                        </div> 
                            <?php
                              $jumlahUncentang = 0;
                              $rekanan = new Rekanan();
                              $rekanan_keuangan = new Rekanan();
                              $rekanan_perpajakan = new Rekanan();
                              $rekanan_teknis = new Rekanan();
                              if ($reqRekananTipeId == '7') { // Perorangan
                                $rekanan->selectByParamsKonfirmasiPerorangan($reqId);
                                $rekanan_perpajakan->selectByParamsKonfirmasiPeroranganDataPerpajakan($reqId);
                                $rekanan_teknis->selectByParamsKonfirmasiPeroranganDataTeknis($reqId);
                              } else {
                                $rekanan->selectByParamsKonfirmasiDataAdmin($reqId);
                                $rekanan_keuangan->selectByParamsKonfirmasiDataKeuangan($reqId);
                                $rekanan_perpajakan->selectByParamsKonfirmasiDataPerpajakan($reqId);
                                $rekanan_teknis->selectByParamsKonfirmasiDataTeknis($reqId);
                              }
                              $no=1;
                              if ($reqRekananTipeId == '7') { // Perorangan ?>
                              <h4>Data Administrasi</h4>
                              <table class="table table-striped">
                              <?php
                                while($rekanan->nextRow())
                                {
                              ?>
                                  <tr>
                                    <td style="width: 2%"><?=$no;?></td>
                                    <td style="width: 83%">
                                      <?=$rekanan->getField("NAMA") ?>
                                      <?php 
                                      if ($rekanan->getField("WAJIB") == '*') {
                                         echo '<span class="color:red">'.$rekanan->getField("WAJIB").'</span>';
                                      } else {
                                        echo "";
                                      } ?> 
                                    </td>
                                    <td style="width: 15%">
                                      <img class="simbol" src="images/<?=$rekanan->getField("SIMBOL")?>.png">
                                    </td>
                                  </tr>
                                    <?php
                                 if($rekanan->getField("SIMBOL") == "uncentang" && $rekanan->getField("WAJIB") == '*')
                                  $jumlahUncentang++;
                                $no++;
                                } ?>
                            </table>
                              <?php
                              } else {  ?>
                              <h4>Data Administrasi</h4>
                              <table class="table table-striped">
                              <?php
                                while($rekanan->nextRow())
                                {
                              ?>
                                  <tr>
                                    <td style="width: 2%"><?=$no;?></td>
                                    <td style="width: 83%">
                                      <?=$rekanan->getField("NAMA") ?>
                                      <?php 
                                      if ($rekanan->getField("WAJIB") == '*') {
                                         echo '<span class="color:red">'.$rekanan->getField("WAJIB").'</span>';
                                      } else {
                                        echo "";
                                      } ?> 
                                    </td>
                                    <td style="width: 15%">
                                      <img class="simbol" src="images/<?=$rekanan->getField("SIMBOL")?>.png">
                                    </td>
                                  </tr>
                                    <?php
                                 if($rekanan->getField("SIMBOL") == "uncentang" && $rekanan->getField("WAJIB") == '*')
                                  $jumlahUncentang++;
                                $no++;
                                } ?>
                            </table>
                          <?php
                              }
                            ?>

                            <?php
                              $no=1;
                              if ($reqRekananTipeId == '7') { // Perorangan
                              } else {  ?>
                              <h4>Data Keuangan</h4>
                              <table class="table table-striped">
                              <?php
                                while($rekanan_keuangan->nextRow())
                                {
                              ?>
                                  <tr>
                                    <td style="width: 2%"><?=$no;?></td>
                                    <td style="width: 83%">
                                      <?=$rekanan_keuangan->getField("NAMA") ?>
                                     <?php 
                                      if ($rekanan_keuangan->getField("WAJIB") == '*') {
                                         echo '<span class="color:red">'.$rekanan_keuangan->getField("WAJIB").'</span>';
                                      } else {
                                        echo "";
                                      } ?> 
                                    </td>
                                    <td style="width: 15%">
                                      <img class="simbol" src="images/<?=$rekanan_keuangan->getField("SIMBOL")?>.png">
                                    </td>
                                  </tr>
                                    <?php
                                 if($rekanan_keuangan->getField("SIMBOL") == "uncentang" && $rekanan_keuangan->getField("WAJIB") == '*')
                                  $jumlahUncentang++;
                                $no++;
                                } ?>
                              </table>
                            <?php
                              }
                            ?>

                            <?php
                              $no=1;
                              if ($reqRekananTipeId == '7') { // Perorangan ?>
                              <h4>Data Perpajakan</h4>
                              <table class="table table-striped">
                              <?php
                                while($rekanan_perpajakan->nextRow())
                                {
                              ?>
                                  <tr>
                                    <td style="width: 2%"><?=$no;?></td>
                                    <td style="width: 83%">
                                      <?=$rekanan_perpajakan->getField("NAMA") ?>
                                       <?php 
                                      if ($rekanan_perpajakan->getField("WAJIB") == '*') {
                                         echo '<span class="color:red">'.$rekanan_perpajakan->getField("WAJIB").'</span>';
                                      } else {
                                        echo "";
                                      } ?> 
                                    </td>
                                    <td style="width: 15%">
                                      <img class="simbol" src="images/<?=$rekanan_perpajakan->getField("SIMBOL")?>.png">
                                    </td>
                                  </tr>
                                    <?php
                                 if($rekanan_perpajakan->getField("SIMBOL") == "uncentang" && $rekanan_perpajakan->getField("WAJIB") == '*')
                                  $jumlahUncentang++;
                                $no++;
                                } ?>
                              </table>
                              <?php
                              } else { ?>
                              <h4>Data Perpajakan</h4>
                              <table class="table table-striped">
                              <?php
                                while($rekanan_perpajakan->nextRow())
                                {
                              ?>
                                  <tr>
                                    <td style="width: 2%"><?=$no;?></td>
                                    <td style="width: 83%">
                                      <?=$rekanan_perpajakan->getField("NAMA") ?>
                                       <?php 
                                      if ($rekanan_perpajakan->getField("WAJIB") == '*') {
                                         echo '<span class="color:red">'.$rekanan_perpajakan->getField("WAJIB").'</span>';
                                      } else {
                                        echo "";
                                      } ?> 
                                    </td>
                                    <td style="width: 15%">
                                      <img class="simbol" src="images/<?=$rekanan_perpajakan->getField("SIMBOL")?>.png">
                                    </td>
                                  </tr>
                                    <?php
                                 if($rekanan_perpajakan->getField("SIMBOL") == "uncentang" && $rekanan_perpajakan->getField("WAJIB") == '*')
                                  $jumlahUncentang++;
                                $no++;
                                } ?>
                              </table>
                            <?php
                              }
                            ?>

                            <?php
                              $no=1;
                              if ($reqRekananTipeId == '7') { // Perorangan ?>
                              <h4>Data Teknis</h4>
                             <table class="table table-striped">
                              <?php 
                                while($rekanan_teknis->nextRow())
                                {
                              ?>
                                  <tr>
                                    <td style="width: 2%"><?=$no;?></td>
                                    <td style="width: 83%">
                                      <?=$rekanan_teknis->getField("NAMA") ?>
                                       <?php 
                                      if ($rekanan_teknis ->getField("WAJIB") == '*') {
                                         echo '<span class="color:red">'.$rekanan_teknis  ->getField("WAJIB").'</span>';
                                      } else {
                                        echo "";
                                      } ?> 
                                    </td>
                                    <td style="width: 15%">
                                      <img class="simbol" src="images/<?=$rekanan_teknis->getField("SIMBOL")?>.png">
                                    </td>
                                  </tr>
                                    <?php
                                 if($rekanan_teknis->getField("SIMBOL") == "uncentang" && $rekanan_teknis->getField("WAJIB") == '*')
                                  $jumlahUncentang++;
                                $no++;
                                } ?>
                              </table>
                              <?php
                              } else { ?> 
                             <h4>Data Teknis</h4>
                             <table class="table table-striped">
                              <?php 
                                while($rekanan_teknis->nextRow())
                                {
                              ?>
                                  <tr>
                                    <td style="width: 2%"><?=$no;?></td>
                                    <td style="width: 83%">
                                      <?=$rekanan_teknis->getField("NAMA") ?>
                                       <?php 
                                      if ($rekanan_teknis ->getField("WAJIB") == '*') {
                                         echo '<span class="color:red">'.$rekanan_teknis  ->getField("WAJIB").'</span>';
                                      } else {
                                        echo "";
                                      } ?> 
                                    </td>
                                    <td style="width: 15%">
                                      <img class="simbol" src="images/<?=$rekanan_teknis->getField("SIMBOL")?>.png">
                                    </td>
                                  </tr>
                                    <?php
                                 if($rekanan_teknis->getField("SIMBOL") == "uncentang" && $rekanan_teknis->getField("WAJIB") == '*')
                                  $jumlahUncentang++;
                                $no++;
                                } ?>
                              </table>
                            <?php  
                              }
                            ?>
                          
                        <?php 
                        if ($jumlahUncentang > 0) {
                           echo '<div class="alert alert-danger">
                                  <b> Kurang '.$jumlahUncentang.' data belum dilengkapi (tanda * data wajib diisi)  </b> 
                                </div>';
                        } ?>
               
                      </div>
                    </div>
                  </div>
                </div>
              </div>   
  
              <div class="form-actions">
                  <input type="hidden" name="reqNomorValidasi" value="<?=$reqKode[0]?>">
                  <input type="hidden" name="reqEmail" value="<?=$tempMail?>">
                  <input type="hidden" name="reqId" value="<?=$reqId?>">
                  <input type="hidden" name="reqRekananNama" value="<?=$tempRekananNama?>">
                  <input type="hidden"  name="submitSimpan" value="Simpan" />
                  <?php
                  $user_login->selectByParams(array("REKANAN_ID" => $reqId));
                  $user_login->firstRow();
                  $user_status = $user_login->getField("USER_STATUS");
                  if($tempStatusValidasi == 1)
                  { ?>
                  <a id="btnSertifikat" target="_blank" href="main/loadUrl/report/vms_pdf/?reqId=<?=$reqId?>&kode=<?=$reqKode[0]?>" onclick="if(confirm('Cetak Surat Keterangan Terdaftar (SKT) ?')) { return true; } else { return false; }" class="btn btn-info mr-1 text-white"><i class="fa fa-print"></i> Cetak Surat Keterangan Terdaftar (SKT)</a>
                  <?php 
                  } else if($tempStatusValidasi == 3) { // Posisi di user REKOMENDASI VMS
                    echo '<a class="btn btn-success mr-1 text-white" ><i class="fa fa-info-circle"></i> MENUNGGU REKOMENDASI PENYELIA</a>';
                  } else if($tempStatusValidasi == 4) { // Posisi di user APPROVAL VMS
                    echo '<a class="btn btn-info mr-1 text-white" ><i class="fa fa-info-circle"></i> MENUNGGU APPROVAL </a>';
                  } else if ($tempStatusValidasi == 10 || $tempStatusValidasi == 0) { // Tolak, Melengkapi Data
                    echo '<label id="note1">Catatan</label><input type="text" class="form-control" id="note11" name="reqNote1" placeholder="Ketik disini" style="margin-bottom:2%">';
                    echo '<button type="submit" class="btn btn-primary mr-1 text-white" id="btnValidasi"><i class="fa fa-check-square-o"></i> VALIDASI & MINTA REKOMENDASI</button>';
                  } 
                  ?>
                  <!-- <a href="main/index/validasi" class="btn btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a>  -->

              </div> 


          </div>
        </div>
        </form>

      </div>
    </div> 
  </div>   
<?php 
} ?>
 
 