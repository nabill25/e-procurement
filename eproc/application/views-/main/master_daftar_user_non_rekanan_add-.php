<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
/* INCLUDE FILE */
$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->library("libapi"); 
$this->load->model(array("Users","UsersBase","Rekanan","UserType","UnitKerja","Direktorat","UserLogin"));
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

/* create objects */
$user_type = new UserType();
$rekanan = new Rekanan();
$user_login = new Users();
$unitKerja = new UnitKerja();
$direktorat = new Direktorat();
$userLogin = new UserLogin();
$libapi = new libapi(); 

$reqId	= $this->input->get("reqId");
$reqMode	= $this->input->post("reqMode");
$reqTipe  = $this->input->post("reqTipe");
$reqTipe2	= $this->input->post("reqTipe2");

$reqNamaUser	= $this->input->post("reqNamaUser");
$reqNama = $this->input->post("reqNama");
$reqPasswordRetype	= $this->input->post("reqPasswordRetype");
$reqPassword	= $this->input->post("reqPassword");
$reqAlamat	= $this->input->post("reqAlamat");
$reqTipePeserta	= $this->input->post("reqTipePeserta");
$reqJabatan	= $this->input->post("reqJabatan");
$reqTelepon	= $this->input->post("reqTelepon");
$reqSubmit	= $this->input->post("reqSubmit");
$reqNamaTemp    = $this->input->post('reqNamaTemp');
$reqUnitKerja   = $this->input->post('reqUnitKerja');
$reqUserJabatanPanitia   = $this->input->post('reqUserJabatanPanitia');

$tmpNamaUser = $reqNamaUser;
$tmpNama = $reqNama;
$tmpAlamat = $reqAlamat;
$tmpPasswordRetype = $reqPasswordRetype;
$tmpPassword = $reqPassword;
$tmpTipe = $reqTipe;
$tmpJabatan = $reqJabatan;
$tmpTelepon = $reqTelepon;
$tmpUnitKerja = $reqUnitKerja;

if($reqId == "") {
  $reqMode = "insert";
  $displayPL = "display:none";
  $displayJP = "display:none";
  $displayPPK = "display:none";
  $displayKepalaPengadaan = "display:none";
  $displayAdminRup = "display:none";
  $displayLegal = "display:none";
  $displayPerenana = "display:none";
  $displayPenunjukPIC = "display:none";
}
else
{
	$user_login->selectByParams(array("USER_LOGIN_ID"=>$reqId),-1,-1);
	$user_login->firstRow();
	// echo $user_login->query;exit;
	$tmpNamaUser = $user_login->getField("USER_LOGIN");
	$tmpNama = $user_login->getField("USER_NAMA");
	$tmpAlamat = $user_login->getField("USER_ALAMAT");
	$tmpTipe = $user_login->getField("USER_TYPE_ID");
	$tmpJabatan = $user_login->getField("USER_JABATAN");
	$tmpTelepon = $user_login->getField("USER_TELEPON");
  $tmpUnitKerja = $user_login->getField("UNIT_KERJA_ID");
  $tmpNip = $user_login->getField("NIP");
  $tmpPanitiaPL = $user_login->getField("CHILD_PL");
  $tmpPPK = $user_login->getField("PPK");
  $tmpVPPengadaan = $user_login->getField("VP_PENGADAAN");
  $tmpAdminRup = $user_login->getField("ADMIN_RUP");
  $tmpUserJabatanPanitia = $user_login->getField("USER_JABATAN_PANITIA"); 
  $tmpPenunjukPIC = $user_login->getField("PENUNJUK_PIC"); 
  $tmpTender = $user_login->getField("TENDER"); 
  $tmpLegal = $user_login->getField("LEGAL"); 
  $tmpdirektorat = $user_login->getField("DIREKTORAT_ID"); 
  $tmpdepartment = $user_login->getField("DEPARTMENT"); 
  $tmpLevelPerencana = $user_login->getField("LEVEL_PERENCANA"); 
  $tmpLevelPembeli = $user_login->getField("LEVEL_PEMBELI"); 


  // if ($tmpTipe == '9' && $tmpPanitiaPL) {
  if ($tmpTipe == '9' || $tmpTipe == '21') { // PENGGUNA OR UNIT INSTALASI
    $displayPL = ' display:\'\'';
    // $displayPPK = ' display:\'\'';
    $displayPPK = ' display:none';
    $displayKepalaPengadaan = ' display:\'\'';
    $displayAdminRup = ' display:\'\'';
  } else {
    $displayPL = ' display:none';
    $displayPPK = ' display:none';
    $displayKepalaPengadaan = ' display:none';
    $displayAdminRup = ' display:none';
    $displayLegal = ' display:none';
  }

  if ($tmpTipe == '3') { // PANITIA
    $displayJP = ' display:\'\'';
    // $displayJPKetua = ' display:\'\'';
  } else {
    $displayJP = ' display:none';
  }

  if ($tmpTipe == '27') { // Perencana
    $displayPerenana = ' display:\'\'';
  } else {
    $displayPerenana = ' display:none';
  }

  if ($tmpTipe == '11') { // Pejabat Pembeli
    $displayPenunjukPIC = ' display:\'\'';
  } else {
    $displayPenunjukPIC = ' display:none';
  }

  if ($tmpTipe == '12') { // LEGAL
    $displayLegal = ' display:\'\'';
  } else {
    $displayLegal = ' display:none';
  }

	$reqMode = "update";
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?=base_url();?>" />
    <link rel="icon" href="../../favicon.ico">
    <title><?= SYSTEM_NAME.' | '.SYSTEM_NAME_PT ?></title>
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">
    <link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">
    <script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="lib/drupal/index.css" /> 
    <script src="lib/emodal/eModal.js"></script>
    <script>
    function openAdd(pageUrl) {
      eModal.iframe(pageUrl, 'Eprocurement | <?= SYSTEM_NAME_PT ?>')
    }
	  function closePopup() {
		  eModal.close();
	  }
  	$(function(){
  		$('#ff').form({
  			url:'users_base_json/master_daftar_user_non_rekanan_add',
  			onSubmit:function(){
  				return $(this).form('validate');
  			},
  			success:function(data){
  				$.messager.alert('Info', data, 'info');	
  				setTimeout(function () {
            top.reloadMonitoring();
            top.closePopup();
           }, 2000);
  			}
  		});
  		
  	});

    function setGroup(item)
    {
    // alert(item.value);
    if (item.value === '9' || item.value === '21') { // PENGGUNA OR UNIT INSTALASI
      $('#setLegal').css("display", "none");
      $('#setPenunjukPIC').css("display", "none"); 
      $('#setLevelPerencana').css("display", "none"); 
      // $('#setPanitiaPL').css("display", "");
      // $('#setPanitiaTender').css("display", "none"); 
      // $('#setJabatanPanitia').css("display", "none"); 
      // $('#setPPK').css("display", "");
      // $('#setKepalaPengadaan').css("display", "");
      // $('#setAdminRup').css("display", "");

    } else if (item.value === '3') { // PANITIA
      $('#setLegal').css("display", "none");
      $('#setPenunjukPIC').css("display", "none"); 
      $('#setLevelPerencana').css("display", "none"); 
      // $('#setJabatanPanitia').css("display", "");
      // $('#setPanitiaTender').css("display", "");
      // $('#setPPK').css("display", "none");
      // $('#setKepalaPengadaan').css("display", "none");
      // $('#setAdminRup').css("display", "none");
      // $('#setPenunjukPIC').css("display", ""); 
      // $('#setPanitiaPL').css("display", "none");
      
    } else if (item.value === '12') { // PENGELOLA KONTRAK
      $('#setLegal').css("display", "");
      $('#setPenunjukPIC').css("display", "none"); 
      $('#setLevelPerencana').css("display", "none"); 
      // $('#setPanitiaTender').css("display", "none"); 
      // $('#setPanitiaPL').css("display", "none");
      // $('#setJabatanPanitia').css("display", "none");
      // $('#setPPK').css("display", "none");
      // $('#setKepalaPengadaan').css("display", "none");
      // $('#setAdminRup').css("display", "none");
      
    } else if (item.value === '11') { // PEJABAT PEMBELI
      $('#setLegal').css("display", "none");
      $('#setPenunjukPIC').css("display", ""); 
      $('#setLevelPerencana').css("display", "none"); 
      // $('#setPanitiaTender').css("display", "none"); 
      // $('#setPanitiaPL').css("display", "none");
      // $('#setJabatanPanitia').css("display", "none");
      // $('#setPPK').css("display", "none");
      // $('#setKepalaPengadaan').css("display", "none");
      // $('#setAdminRup').css("display", "none");
      
    } else if (item.value === '27') { // PERENANA
      $('#setLegal').css("display", "none");
      $('#setPenunjukPIC').css("display", "none"); 
      $('#setLevelPerencana').css("display", ""); 
      // $('#setPanitiaTender').css("display", "none"); 
      // $('#setPanitiaPL').css("display", "none");
      // $('#setJabatanPanitia').css("display", "none");
      // $('#setPPK').css("display", "none");
      // $('#setKepalaPengadaan').css("display", "none");
      // $('#setAdminRup').css("display", "none");
      
    } else { // SELAIN DIATAS
      $('#setLegal').css("display", "none");
      $('#setPenunjukPIC').css("display", "none"); 
      $('#setLevelPerencana').css("display", "none"); 
      // $('#setPanitiaTender').css("display", "none"); 
      // $('#setPanitiaPL').css("display", "none");
      // $('#setJabatanPanitia').css("display", "none");
      // $('#setPanitiaTender').css("display", "none");
      // $('#setPPK').css("display", "none");
      // $('#setKepalaPengadaan').css("display", "none");
      // $('#setAdminRup').css("display", "none");
      // alert('Bukan Pengguna');
    }
  }

  function setRole(item)
  {
    // alert(item.value);
    if (item.value === '1:Ketua') { // KETUA 
      $('#setPenunjukPIC').css("display", ""); 
      $('#setPanitiaTender').css("display", "none"); 
      
    } else { // SELAIN KETUA
      $('#setPenunjukPIC').css("display", "none"); 
      $('#setPanitiaTender').css("display", ""); 
    }
  }
	
  </script>
  </head>
 
<body class="body-popup"> 

     <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Master User Non Rekanan</strong>
          </div> 
          <div class="p-1" >
            <form id="ff" class="form-horizontal" role="form" method="post" novalidate style="padding:0 50px">
              <?php 
              if($reqMode == 'insert')
              {
              ?>
                <div class="row">
                  <div class="form-group col-md-12 mb-2">
                    <label>Username:</label>
                    <input type="text" name="reqNamaUser" value="<?=$tmpNamaUser?>" class="form-control easyui-validatebox span9" required>
                  </div> 
                </div>  

                <div class="row">
                  <div class="form-group col-md-6 mb-2">
                    <label>Password baru:</label>
                    <input type="password" size="20" name="reqPassword" id="reqPassword" value="<?=$tmpUSER_PASSWORD?>" <?=$status?> class="form-control easyui-validatebox span9" required>
                  </div>  
                  <div class="form-group col-md-6 mb-2">
                    <label>Ulangi password baru:</label>
                    <input type="password" size="20" name="reqPasswordRetype" id="reqPasswordRetype" value="<?=$tmpUSER_PASSWORD?>" <?=$status?> class="form-control easyui-validatebox span9" required>
                  </div> 
                </div>  
  
             <?php 
             }    
             ?>

              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label>Nama:</label>
                  <input type="text" name="reqNama" value="<?=$tmpNama?>" title="Nama harus diisi" class="form-control easyui-validatebox span9" required/>
                </div>  
              </div>   
              

              <div class="row">
                <div class="form-group col-md-4 mb-2">
                  <label>NUP/NIP:</label> 
                  <input type="text" name="reqNip" value="<?=$tmpNip?>" title="NUP/NIP harus diisi" class="form-control easyui-validatebox span9" required/>
                </div>  
                <div class="form-group col-md-4 mb-2">
                  <label>Jabatan:</label>
                  <input type="text" name="reqJabatan" value="<?=$tmpJabatan?>" class="form-control easyui-validatebox span9" required/>
                </div>  
                <div class="form-group col-md-4 mb-2">
                  <label>Telepon:</label>
                  <input type="text" name="reqTelepon" value="<?=$tmpTelepon?>" class="form-control easyui-validatebox span9" required/>
                </div> 
              </div>  

              <div class="row">
                <div class="form-group col-md-3 mb-2">
                  <label>Unit Kerja:</label>
                  <select name="reqUnitKerja" class="form-control easyui-validatebox" id="reqUnitKerja" required>
                   <option value="" class="form-control easyui-validatebox span9">-- Pilih data --</option>
                    <?php 
                    $unitKerja->selectByParams();
                    while($unitKerja->nextRow())
                    {
                    ?>
                      <option value="<?= $unitKerja->getField("UNIT_KERJA_ID")?>" <?= ($unitKerja->getField('UNIT_KERJA_ID') == $tmpUnitKerja ? 'selected="selected"':'')?> ><?=$unitKerja->getField("NAMA")?></option>
                    <?php 
                    }
                    ?>
                  </select>
                </div>  
                <div class="form-group col-md-3 mb-2">
                  <label>Direktorat:</label>
                  <select name="reqDirektorat" class="form-control easyui-validatebox" id="reqDirektorat">
                   <option value="" class="form-control easyui-validatebox span9">-- Pilih Direktorat --</option>
                    <?php 
                    $direktorat->selectByParams();
                    while($direktorat->nextRow())
                    {
                    ?>
                      <option value="<?= $direktorat->getField("DIREKTORAT_ID")?>" <?= ($direktorat->getField('DIREKTORAT_ID') == $tmpdirektorat ? 'selected="selected"':'')?> ><?=$direktorat->getField("NAMA")?></option>
                    <?php 
                    }
                    ?>
                  </select>
                </div> 
                <!-- <div class="form-group col-md-6 mb-2">
                  <label>Divisi:</label>
                    <?php 
                    // $libapi = new libapi(); 
                    // $url = URL_API_AGNES.'/department';
                    // // untuk ambil Department
                    // $libapii = new libapi(); 
                    // $getDept = $libapii->getDepartment($url); 
                    // $dataDept = $getDept->results->data;
                    ?>
                  <select name="reqDepartment" class="form-control" id="reqDepartment" required>
                   <option value="" class="form-control easyui-validatebox span9">-- Pilih Divisi --</option>
                    <?php 
                    // foreach ($dataDept as $key => $value) {
                    ?>
                      <option value="<?php // echo $value->department ?>" <?php // echo ($value->department == $tmpdepartment ? 'selected="selected"':'')?> ><?php // echo $value->department ?></option>
                    <?php 
                    // }
                    ?>
                  </select>
                </div>   -->

                <div class="form-group col-md-3 mb-2">
                  <label>Tipe User:</label>  
                    <?php  
                    $user_type->selectByParams(array('AKTIF' => 1));
                  ?>
                  <select id="idReqGroup" name="reqTipe" onchange="setGroup(this);" class="form-control span4" required>
                    <?php 
                      while($user_type->nextRow()){?>
                      <option value="<?=$user_type->getField("USER_TYPE_ID")?>" 
                               <?php  if($user_type->getField("USER_TYPE_ID") == $tmpTipe) echo "selected";?>><?=$user_type->getField("NAMA")?></option>
                    <?php }?>
                  </select>
                </div>  
                <div class="form-group col-md-3 mb-2" id="setLegal" style="<?= $displayLegal ?>">
                  <label>Legal :</label> 
                  <select id="idReqGroup12" name="reqTipe12" class="form-control span4" required>
                    <option value="">-- Pilih --</option> 
                    <option value="1" <?php if ($tmpLegal == '1') { echo "selected"; } ?>>Ya</option>
                    <option value="0" <?php if ($tmpLegal == '0') { echo "selected"; } ?>>Tidak</option>
                  </select>
                </div>  
                <!-- <div class="form-group col-md-3 mb-2" id="setJabatanPanitia" style="<?= $displayJP ?>">
                  <label>Fungsi :</label> 
                  <select id="idReqGroup3" name="reqUserJabatanPanitia" onchange="setRole(this);" class="form-control span4" required>
                    <option value="">-- Pilih --</option>  -->
                    <!-- <option value="1:Ketua" <?php // if ($tmpUserJabatanPanitia == '1') { echo "selected"; } ?>>Ketua</option> -->
                    <!-- <option value="2:Penyelia" <?php // if ($tmpUserJabatanPanitia == '2') { echo "selected"; } ?>>Penyelia</option> -->
                    <!-- <option value="3:Anggota" <?php // if ($tmpUserJabatanPanitia == '3') { echo "selected"; } ?>>Anggota</option> -->
                  <!-- </select>
                </div>  -->
                <?php 
                //if ($tmpUserJabatanPanitia == '1') { ?>
                <div class="form-group col-md-3 mb-2" id="setPenunjukPIC" style="<?= $displayPenunjukPIC ?>">
                  <label>Penunjuk PIC:</label> 
                  <select id="idReqGroup8" name="reqTipe8" class="form-control span4" required>
                    <option value="0" <?php if ($tmpLevelPembeli == '0') { echo "selected"; } ?>>Tidak</option>
                    <option value="1" <?php if ($tmpLevelPembeli == '1') { echo "selected"; } ?>>Ya</option>
                  </select>
                </div> 
                <div class="form-group col-md-3 mb-2" id="setLevelPerencana" style="<?= $displayPerenana ?>">
                  <label>Level Perencana:</label> 
                  <select id="idReqGroup9" name="reqTipe9" class="form-control span4" required>
                    <option value="1" <?php if ($tmpLevelPerencana == '1') { echo "selected"; } ?>>Staff</option>
                    <option value="2" <?php if ($tmpLevelPerencana == '2') { echo "selected"; } ?>>Kasi</option>
                    <option value="3" <?php if ($tmpLevelPerencana == '3') { echo "selected"; } ?>>Kasubdit</option>
                  </select>
                </div> 
                <?php //} else { ?>
                <!-- <div class="form-group col-md-3 mb-2" id="setPanitiaTender" 
                    style="<?php // if($tmpTipe == '3' && $tmpUserJabatanPanitia != '' && $tmpUserJabatanPanitia != '1') { echo "display:"; } else { echo "display: none"; } ?>">
                  <label>Role:</label> 
                  <select id="idReqGroup6" name="reqTipe7" class="form-control span4" required>
                    <option value="2" <?php // if ($tmpTender == '2') { echo "selected"; } ?>>Tender & Non-Tender</option>
                    <option value="1" <?php // if ($tmpTender == '1') { echo "selected"; } ?>>Tender</option>
                    <option value="0" <?php // if ($tmpTender == '0') { echo "selected"; } ?>>Non-Tender</option>
                  </select>
                </div>  -->
                <?php 
                //} ?>
                 <div class="form-group col-md-3 mb-2" id="setAdminRup" style="<?= $displayAdminRup ?>">
                  <label>Admin RUP :</label> 
                  <select id="idReqGroup6" name="reqTipe6" class="form-control span4" required>
                    <?php
                    $userLogin->selectByParams(array('USER_TYPE_ID' => '17'),-1,-1);
                      while($userLogin->nextRow()){?>
                      <option value="<?=$userLogin->getField("USER_LOGIN_ID")?>" 
                               <?php if($userLogin->getField("USER_LOGIN_ID") == $tmpAdminRup) echo "selected";?>><?=$userLogin->getField("USER_NAMA").' - '.$userLogin->getField("USER_JABATAN")?></option>
                    <?php }?>
                  </select>
                </div>  
                <div class="form-group col-md-3 mb-2" id="setPPK" style="<?= $displayPPK ?>">
                  <label>Pengelola Kontrak :</label> 
                  <select id="idReqGroup4" name="reqTipe3" class="form-control span4" required>
                    <?php
                    $userLogin->selectByParams(array('USER_TYPE_ID' => '12', 'LEGAL' => '0'),-1,-1);
                      while($userLogin->nextRow()){?>
                      <option value="<?=$userLogin->getField("USER_LOGIN_ID")?>" 
                               <?php if($userLogin->getField("USER_LOGIN_ID") == $tmpPPK) echo "selected";?>><?=$userLogin->getField("USER_NAMA")?></option>
                    <?php }?>
                  </select>
                </div> 
                <div class="form-group col-md-3 mb-2" id="setKepalaPengadaan" style="<?= $displayKepalaPengadaan ?>">
                  <label>Manager Pengadaan :</label> 
                  <select id="idReqGroup5" name="reqTipe5" class="form-control span4" required>
                    <?php
                    $userLogin->selectKepalaPengadaan(array(),-1,-1);
                      while($userLogin->nextRow()){?>
                      <option value="<?=$userLogin->getField("USER_LOGIN_ID")?>" 
                               <?php if($userLogin->getField("USER_LOGIN_ID") == $tmpVPPengadaan) echo "selected";?>><?=$userLogin->getField("USER_NAMA")?></option>
                    <?php }?>
                  </select>
                </div>  

              </div>

              <div class="row">
                <div class="form-group col-md-12 mb-2">
                  <label>Alamat:</label>
                  <textarea name="reqAlamat" cols="50" rows="5" class="form-control"><?=$tmpAlamat?></textarea>
                </div> 
              </div>  
                    
              <div class="form-actions">
                <input type="hidden" name="reqId" id="reqId" value="<?=$reqId?>"/>
                <input type="hidden" name="reqMode" id="reqMode" value="<?=$reqMode?>"/>
                <input type="hidden" name="reqSubmit" id="reqSubmit" value="Submit"/>
                <button type="submit" class="btn round btn-min-width box-shadow-1 btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
              </div> 
            </form>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
    
    <!-- EASYUI -->
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
    <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>
    
  </body>
</html>
