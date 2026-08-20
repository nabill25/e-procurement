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
$this->load->model(array("Users","UsersBase","Rekanan","UserType","UnitKerja","Direktorat","UserLogin","Userloginmulti"));
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
$user_login_multi = new Userloginmulti();

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

$displayPPK = "display:none";
$displayAdminRup = "display:none";
$displayKepalaPengadaan = "display:none";

if($reqId == "") {
  $reqMode = "insert";

  $displayPembeli = "display:none";
  $displayPengguna = "display:none";
  $displayPengguna2 = "display:none";
  $displayKontrak = "display:none";
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
  $tmpLevelKontrak = $user_login->getField("LEVEL_KONTRAK");  
  $tmpLevelPengguna = $user_login->getField("LEVEL_PENGGUNA");  
  $tmpKasiPengguna = $user_login->getField("KASI_PENGGUNA");  


  $displayPembeli = "display:none";
  $displayPengguna = "display:none";
  $displayPengguna2 = "display:none";
  $displayKontrak = "display:none";
  $displayPerenana = "display:none";
  $displayPenunjukPIC = "display:none";

  if ($tmpTipe == '9') { // PENGGUNA 
    $displayPembeli = "display:none";
    $displayPengguna = "display:''";
    if ($tmpLevelPengguna == '1') { // PIC
      $displayPengguna2 = "display:none";
    } else {
      $displayPengguna2 = "display:''";
    }
    $displayKontrak = "display:none";
    $displayPerenana = "display:none";
    $displayPenunjukPIC = "display:none";
  } else { 
  } 

  if ($tmpTipe == '11') { // PEJABAT PEMBELI
    $displayPembeli = "display:''";
    $displayPengguna = "display:none";
    $displayPengguna2 = "display:none";
    $displayKontrak = "display:none";
    $displayPerenana = "display:none";
    $displayPenunjukPIC = "display:none";
  } else {
  }

  if ($tmpTipe == '12') { // PENGELOLA KONTRAK
    $displayPembeli = "display:none";
    $displayPengguna = "display:none";
    $displayPengguna2 = "display:none";
    $displayKontrak = "display:''";
    $displayPerenana = "display:none";
    $displayPenunjukPIC = "display:''";
  } else {
  }

  if ($tmpTipe == '27') { // PERENCANA
    $displayPembeli = "display:none";
    $displayPengguna = "display:none";
    $displayPengguna2 = "display:none";
    $displayKontrak = "display:none";
    $displayPerenana = "display:''";
    $displayPenunjukPIC = "display:none";
  } else {
  }

	$reqMode = "update";

  $user_login_multi->selectByParams(array("USER_LOGIN_ID"=>$reqId),-1,-1);
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

    setTimeout(function(){
      $.ajax({
        url : '<?= base_url('users_base_json/getUserGroup/'.$reqId) ?>',
        type: "GET",
        dataType: "JSON",
        beforeSend: function() {
          $('#contentGroupUser').html('Load data...');
        },
        success: function(data)
        {
          $('#contentGroupUser').html(data.message);
        },
        error: function (jqXHR, textStatus, errorThrown) { },
      });
    }, 500);

  	$(function(){
  		$('#ff').form({
  			url:'users_base_json/master_daftar_user_non_rekanan_add_akses',
  			onSubmit:function(){
  				return $(this).form('validate');
  			},
  			success:function(data){
  				$.messager.alert('Info', data, 'info');	
  				setTimeout(function () {
            top.reloadMonitoring();
            // top.closePopup();
            reloadContent();
           }, 2000);
  			}
  		});
  		
  	});

    function reloadContent() {
      // setTimeout(function(){
        $.ajax({
          url : '<?= base_url('users_base_json/getUserGroup/'.$reqId) ?>',
          type: "GET",
          dataType: "JSON",
          beforeSend: function() {
            $('#contentGroupUser').html('Load data...');
          },
          success: function(data)
          {
            $('#contentGroupUser').html(data.message);
          },
          error: function (jqXHR, textStatus, errorThrown) { },
        });
      // }, 500);
    }

    function setGroup(item)
    {
    // alert(item.value);
    if (item.value === '9') { // PENGGUNA
      $('#setLevelPerencana').css("display", "none");
      $('#setPenunjukPIC').css("display", "none");
      $('#setLevelKontrak').css("display", "none");
      $('#setLevelPengguna').css("display", "");
      // $('#setLevelPengguna2').css("display", "");
      $('#setLevelPembeli').css("display", "none");
    } else if (item.value === '12') { // PENGELOLA KONTRAK
      $('#setLevelPerencana').css("display", "none");
      $('#setPenunjukPIC').css("display", "");
      $('#setLevelKontrak').css("display", "");
      $('#setLevelPengguna').css("display", "none");
      $('#setLevelPengguna2').css("display", "none");
      $('#setLevelPembeli').css("display", "none");
    } else if (item.value === '11') { // PEJABAT PEMBELI
      $('#setLevelPerencana').css("display", "none");
      $('#setPenunjukPIC').css("display", "none");
      $('#setLevelKontrak').css("display", "none");
      $('#setLevelPengguna').css("display", "none");
      $('#setLevelPengguna2').css("display", "none");
      $('#setLevelPembeli').css("display", "");
    } else if (item.value === '27') { // PERENCANA
      $('#setLevelPerencana').css("display", "");
      $('#setPenunjukPIC').css("display", "none");
      $('#setLevelKontrak').css("display", "none");
      $('#setLevelPengguna').css("display", "none");
      $('#setLevelPengguna2').css("display", "none");
      $('#setLevelPembeli').css("display", "none");
    } else { // SELAIN DIATAS
      $('#setLevelPerencana').css("display", "none");
      $('#setPenunjukPIC').css("display", "none");
      $('#setLevelKontrak').css("display", "none");
      $('#setLevelPengguna').css("display", "none");
      $('#setLevelPengguna2').css("display", "none");
      $('#setLevelPembeli').css("display", "none");
    }
  }
  
  function setPICPengguna(item)
  {
    if (item.value === '1') { // PIC 
      $('#setLevelPengguna2').css("display", "none"); 
    } else { 
      $('#setLevelPengguna2').css("display", ""); 
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

  function aaa(a) {
    $.ajax({
      url : '<?= base_url('users_base_json/excUserGroupDelete/'.$reqId.'/') ?>'+a,
      type: "GET",
      dataType: "JSON",
      beforeSend: function() {
        $('#btnDelete_'+a).html('<span class="fa fa-spinner fa-spin">');
      },
      success: function(data)
      {
        if (data.respon == 'true') {
          reloadContent();
        } else {
          $('#btnDelete_'+a).html(data.message);
          setTimeout(function(){
            reloadContent();
          }, 2000);

        }

      },
      error: function (jqXHR, textStatus, errorThrown) { },
    });
  }
	
  </script>
  </head>
 
<body class="body-popup" style="background: #fff;"> 

     <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Tambah Akses User</strong>
          </div> 
          <div class="p-1" >
            <div style="padding:10px 50px">
              <table class="table table-bordered table-hover">
                <tr>
                  <td width="15%" style="background: #f6db00; color:#000"><b>Nama</b></td><td><?=$tmpNama?></td>
                  <td width="15%" style="background: #f6db00; color:#000"><b>NUP/NIP</b></td><td><?=$tmpNip?></td>
                </tr>
                <tr>
                  <td width="15%" style="background: #f6db00; color:#000"><b>Jabatan</b></td><td><?=$tmpJabatan?></td>
                  <td width="15%" style="background: #f6db00; color:#000"><b>Telepon</b></td><td><?=$tmpTelepon?></td>
                </tr>
                <tr>
                  <td width="15%" style="background: #f6db00; color:#000"><b>Unit Kerja</b></td>
                  <td>
                    <?php 
                    $unitKerja->selectByParams(array("UNIT_KERJA_ID" => $tmpUnitKerja));
                    $unitKerja->firstRow();
                    echo $unitKerja->getField('NAMA');
                    ?>    
                  </td>
                  <td width="15%" style="background: #f6db00; color:#000"><b>Direktorat</b></td>
                  <td>
                    <?php 
                    if ($tmpdirektorat) {
                      $direktorat->selectByParams(array("DIREKTORAT_ID" => $tmpdirektorat));
                      $direktorat->firstRow();
                      echo $direktorat->getField('NAMA');
                    } else {
                      echo "-";
                    }
                    ?>
                  </td>
                </tr>
                <tr>
                  <td width="15%" style="background: #f6db00; color:#000"><b>Alamat</b></td><td colspan="3"><?=$tmpAlamat?></td>
                </tr>
              </table>
            </div>

            <form id="ff" class="form-horizontal" role="form" method="post" novalidate style="padding:10px 50px"> 
              <div class="border-primary" style="padding:15px 50px 0px 50px">
                <div class="row"> 

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

                  <div class="form-group col-md-3 mb-2" id="setLevelPerencana" style="<?= $displayPerenana ?>">
                    <label>Level Perencana:</label> 
                    <select id="idReqGroup9" name="reqTipe9" class="form-control span4" required>
                      <option value="1" <?php if ($tmpLevelPerencana == '1') { echo "selected"; } ?>>Staff</option>
                      <option value="2" <?php if ($tmpLevelPerencana == '2') { echo "selected"; } ?>>Kasi</option>
                      <option value="3" <?php if ($tmpLevelPerencana == '3') { echo "selected"; } ?>>Kasubdit</option>
                    </select>
                  </div> 

                  <div class="form-group col-md-3 mb-2" id="setPenunjukPIC" style="<?= $displayPenunjukPIC ?>">
                    <label>Penunjuk PIC:</label> 
                    <select id="idReqGroup8" name="reqTipe8" class="form-control span4" required>
                      <option value="0" <?php if ($tmpPenunjukPIC == '0') { echo "selected"; } ?>>Tidak</option>
                      <option value="1" <?php if ($tmpPenunjukPIC == '1') { echo "selected"; } ?>>Ya</option>
                    </select>
                  </div> 

                  <div class="form-group col-md-3 mb-2" id="setLevelKontrak" style="<?= $displayKontrak ?>">
                    <label>Level Kontrak:</label> 
                    <select id="idReqGroup10" name="reqTipe10" class="form-control span4" required>
                      <option value="1" <?php if ($tmpLevelKontrak == '1') { echo "selected"; } ?>>Persiapan</option>
                      <option value="2" <?php if ($tmpLevelKontrak == '2') { echo "selected"; } ?>>Pengedalian</option>
                      <option value="3" <?php if ($tmpLevelKontrak == '3') { echo "selected"; } ?>>Penyelesaian</option>
                    </select>
                  </div> 

                  <div class="form-group col-md-3 mb-2" id="setLevelPengguna" style="<?= $displayPengguna ?>">
                    <label>Level Pengguna:</label> 
                    <select id="idReqGroup11" name="reqTipe11" onchange="setPICPengguna(this);" class="form-control span4" required>
                      <option value="0" <?php if ($tmpLevelPengguna == '0') { echo "selected"; } ?>>Staff</option>
                      <option value="1" <?php if ($tmpLevelPengguna == '1') { echo "selected"; } ?>>PIC</option>
                    </select>
                  </div> 

                  <div class="form-group col-md-3 mb-2" id="setLevelPengguna2" style="<?= $displayPengguna2 ?>">
                    <label>PIC Pengguna:</label>  
                    <select id="idReqGroup6" name="reqTipe13" class="form-control span4" required>
                      <?php
                      $userLogin->selectByParams(array('USER_TYPE_ID' => '9', 'LEVEL_PENGGUNA' => '1'),-1,-1);
                        while($userLogin->nextRow()){?>
                        <option value="<?=$userLogin->getField("USER_LOGIN_ID")?>" 
                                 <?php if($userLogin->getField("USER_LOGIN_ID") == $tmpKasiPengguna) echo "selected";?>><?=$userLogin->getField("USER_NAMA").' - '.$userLogin->getField("USER_JABATAN")?></option>
                      <?php }?>
                    </select>
                  </div> 

                  <div class="form-group col-md-3 mb-2" id="setLevelPembeli" style="<?= $displayPembeli ?>">
                    <label>Level:</label> 
                    <select id="idReqGroup12" name="reqTipe12" class="form-control span4" required>
                      <option value="0" <?php if ($tmpLevelPembeli == '0') { echo "selected"; } ?>>Staff</option>
                      <option value="1" <?php if ($tmpLevelPembeli == '1') { echo "selected"; } ?>>Kasi</option>
                    </select>
                  </div>  

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

                </div> 
                      
                <div class="form-actions">
                  <input type="hidden" name="reqId" id="reqId" value="<?=$reqId?>"/>
                  <input type="hidden" name="reqMode" id="reqMode" value="<?=$reqMode?>"/>
                  <input type="hidden" name="reqSubmit" id="reqSubmit" value="Submit"/>
                  <button type="submit" class="btn round btn-min-width box-shadow-1 btn-primary"><i class="fa fa-check-square-o"></i> Simpan</button>
                </div> 
              </div>
            </form>

            <div class="row" style="padding: 10px 60px;"> 
              <h4>Group User</h4>
              <div id="contentGroupUser" style="width: 100%;">
              </div>
            </div>
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
