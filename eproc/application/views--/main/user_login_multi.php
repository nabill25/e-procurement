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
$user_login2 = new UserLogin();

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
 
  function aaa(multiid,userid) {
    $.ajax({
      url : '<?= base_url('users_base_json/excSplitRole/') ?>'+multiid+'/'+userid,
      type: "GET",
      dataType: "JSON",
      beforeSend: function() {
        $('#btnPilih_'+multiid).html('<span class="fa fa-spinner fa-spin">');
      },
      success: function(data)
      {
        if (data.respon == 'true') {
        } else {
          $('#btnPilih_'+multiid).html(data.message);
          setTimeout(function(){ 
          }, 2000);

        }
        // close modal parent
        parent.eModal.close();
        // optional reload parent
        parent.location.reload();
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
            <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Multi Role</strong>
          </div> 
          <div class="p-1" style="margin-top:-20px">

            <div class="row" style="padding: 10px"> 
              <?php 
              $no=1;
              $html  = '<table class="table table-bordered table-hover" id="contentGroupUser">
                        <tr style="background-color: #000; color:#fff">
                          <th width="10px">No.</th>
                          <th>Tipe User</th>
                          <th width="10px">Aksi</th>
                        </tr> ';
              while($user_login_multi->nextRow()) {
                  $html .= '<tr>
                        <td>'.$no.'</td>
                        <td>';
                $html .= '    '.$user_login_multi->getField('NAMA').'';
                          switch ($user_login_multi->getField('NAMA')) 
                          {
                            case 'PENGGUNA':
                              if ($user_login_multi->getField('LEVEL_PENGGUNA') == '1') {
                                $html .= '<br><small><b><span class="badge badge-warning">PIC</span></b></small>';
                              } else {
                                if ($user_login_multi->getField('KASI_PENGGUNA')) {
                                  $user_login2->selectByParams(array("USER_LOGIN_ID"=>$user_login_multi->getField('KASI_PENGGUNA')),-1,-1);
                                  $user_login2->firstRow();
                                  $html .= '<br><small><b><span class="badge badge-primary">Staff</span></b></small>
                                        <small><b><span class="badge badge-primary">PIC: '.$user_login2->getField('USER_NAMA').'</span></b></small>';
                                } else {
                                  $html .= '<br><small><b><span class="badge badge-primary">Staff</span></b></small>
                                        <small><b><span class="badge badge-danger">PIC belum ditetapkan</span></b></small>';
                                }
                              }
                              break;
                            case 'PEJABAT PENGADAAN':
                              if ($user_login_multi->getField('LEVEL_PEMBELI') == '1') {
                                $html .= '<br><small><b><span class="badge badge-warning">Kasi</span></b></small>';
                              } else {
                                $html .= '<br><small><b><span class="badge badge-primary">Staff</span></b></small>';
                              }
                              break;
                            case 'PERENCANAAN':
                              if ($user_login_multi->getField('LEVEL_PERENCANA') == '1') {
                                $html .= '<br><small><b><span class="badge badge-primary">Staff</span></b></small>';
                              } elseif ($user_login_multi->getField('LEVEL_PERENCANA') == '2') {
                                $html .= '<br><small><b><span class="badge badge-warning">Kasi</span></b></small>';
                              } elseif ($user_login_multi->getField('LEVEL_PERENCANA') == '3') {
                                $html .= '<br><small><b><span class="badge badge-dark">Kasubdit</span></b></small>';
                              }
                              break;
                            case 'PENGELOLA KONTRAK':
                              if ($user_login_multi->getField('LEVEL_KONTRAK') == '1') {
                                if ($user_login_multi->getField('PENUNJUK_PIC') == '1') {
                                  $html .= '<br><small><b>Persiapan <span class="badge badge-warning">(Kasi)</span></b></small>';
                                } else {
                                  $html .= '<br><small><b>Persiapan <span class="badge badge-primary">(Staff)</span></b></small>';
                                }
                              } elseif ($user_login_multi->getField('LEVEL_KONTRAK') == '2') {
                                if ($user_login_multi->getField('PENUNJUK_PIC') == '1') {
                                  $html .= '<br><small><b>Pengendalian <span class="badge badge-warning">(Kasi)</span></b></small>';
                                } else {
                                  $html .= '<br><small><b>Pengendalian <span class="badge badge-primary">(Staff)</span></b></small>';
                                }
                              } elseif ($user_login_multi->getField('LEVEL_KONTRAK') == '3') {
                                if ($user_login_multi->getField('PENUNJUK_PIC') == '1') {
                                  $html .= '<br><small><b>Penyelesaian <span class="badge badge-warning">(Kasi)</span></b></small>';
                                } else {
                                  $html .= '<br><small><b>Penyelesaian <span class="badge badge-primary">(Staff)</span></b></small>';
                                }
                              }
                            break;
                              default:
                              $html .= '';
                              break;
                            }
                $html .=  '</td>
                        <td><a id="btnPilih_'.$user_login_multi->getField('USER_LOGIN_MULTI_ID').'" style="color:#fff" class="badge badge-primary" onclick="return aaa(\''.$user_login_multi->getField('USER_LOGIN_MULTI_ID').'\',\''.$this->USER_LOGIN_ID.'\')"><span class="fa fa-hand-pointer-o"></span></span> Pilih</a>
                                      <span id="msgDelete_'.$user_login_multi->getField('USER_LOGIN_MULTI_ID').'"></span>
                                  </td>
                      </tr>';
                  $no++;
                } 
              $html .= '</table>';

              echo $html;
              ?>
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
