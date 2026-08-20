<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header('X-Content-Type-Options: nosniff');

include_once("functions/default.func.php");
include_once("functions/string.func.php");
include_once("functions/date.func.php");
$this->load->library("crfs_protect"); $csrfLogin = new crfs_protect('_crfs_rr_login');
$this->load->library("libnotification");

// Visitor
$this->load->model(array("Visitor","Rekanan","Users","Queryfree"));

$user_login = new Users();
$visitor    = new Visitor();

$menu = $this->input->get("menu");
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="title" content="<?= $metaTitle ?>">
    <meta name="description" content="<?= $metaDesc ?>">
    <meta name="author" content="<?= $metaAuthor ?>">

    <meta property="og:locale" content="en_US">
    <meta property="og:site_name" content="<?= $metaOGSitename ?>">
    <meta property="og:url" content="<?= $metaOGUrl?>">
    <meta property="og:type" content="<?= $metaOGType ?>">
    <meta property="og:title" content="<?= $metaOGTitle ?>">
    <meta property="og:description" content="<?= $metaOGDesc ?>" />
    <meta property="og:image" content="<?= $metaOGImage ?>">
    <meta property="og:image:width" content="600">
    <meta property="og:image:height" content="315">
    <link rel="shortcut icon" href="<?=base_url()?>assets/ico/favicon.png">

    <title><?= SYSTEM_NAME.' - '.SYSTEM_NAME_PT ?></title>
    <base href="<?=base_url()?>">
    <script src="<?=base_url()?>assets/new/vendors/js/jquery.min.3.6.0.js"></script>
    <link rel="stylesheet" href="<?=base_url()?>lib/font-awesome-4.7.0/css/font-awesome.css" type="text/css">
    <script language="javascript" src="<?=base_url()?>js/jquery.redirect.js"></script>
    <script src="<?=base_url()?>lib/multifile-master/jquery.MultiFile.js"></script>
    <!-- Anyar -->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/extensions/unslider.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/prism.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/extensions/toastr.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/animate/animate.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/style.css">
    <?php // echo $script_captcha; // javascript recaptcha ?>
  </head>

  <style type="text/css">
  .mr-1 { margin-right: 0.3rem !important;  }
  /* Absolute Center Spinner */
  /* Mackbok  */
  a, a:link, a:visited, a:hover, a:active  { text-decoration: none !important; }
  body, h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 { font-family: "Helvetica Neue",Helvetica,Arial,sans-serif !important; }
  .bg-primary { background-color: #40576a !important; } .border-info.border-darken-2 {border: 1px solid #228eb6 !important;} .ui-menu .ui-widget-header { background-color: #000 !important; border: 1px solid #000 !important} .ul-state-active { background-color: #000 } .ui-menu .ui-state-active { background: #ffdd00 !important;  opacity: 0.5; } .border-info.border-darken-2 { border: 1px solid #b7b7b7 !important; -webkit-box-shadow: 0px 17px 11px -4px rgba(0,0,0,0.16); -moz-box-shadow: 0px 17px 11px -4px rgba(0,0,0,0.16); box-shadow: 0px 17px 11px -4px rgba(0,0,0,0.16);} .menu-icons { border: 1px solid #b7b7b7 !important; -webkit-box-shadow: 0px 17px 11px -4px rgba(0,0,0,0.16); -moz-box-shadow: 0px 17px 11px -4px rgba(0,0,0,0.16); box-shadow: 0px 17px 11px -4px rgba(0,0,0,0.16);} .combobox-item-selected { background-color: #3bafda !important;  opacity: .6; color: #fff !important; } .navbar-dark.navbar-horizontal { background: #000; } .navbar-dark .navbar-nav .active.nav-link { background-color: #ffdd00; color: #000;} .horizontal-menu .navbar-dark .nav-item:hover, .horizontal-menu .navbar-dark .nav-item .hover { background-color: transparent !important; color: #000 !important; } table.dataTable.hover tbody tr:hover, table.dataTable.display tbody tr:hover { background-color:#e7d5f5 !important; } .navbar-dark .navbar-nav .nav-link:hover, .navbar-dark .navbar-nav .nav-link:focus { color: #000 !important; } .horizontal-menu .navbar-dark .active > a {background-color: #ffdd00; color: #000 !important;} .dropdown-item.active, .dropdown-item:active {background-color: #f1f1f1; color: #000; font-weight: bold;}
  .btn-min-width { min-width: 0rem !important; }
  /* end Mackbok  */
  .stick { position: fixed; top: 55px; padding: 20px 10px 0px 0px; z-index: 999; }
  .stick2 { position: fixed; top: 70px; padding: 20px 10px 0px 0px; z-index: 999; }
  .header-navbar .navbar-header .navbar-brand { padding: 5px !important; }
  /*.stickfilter { position: fixed; top: 50px; }*/
  footer.footer-light { background-color: #000 !important; color: #fff; } #Date, #hours,#point,#min,#point,#sec, .fontfooter { color: #fff !important; font-size: .9em !important; } .unslider-nav { display: none; }
  .text-white {color: #fff}ul.unstyled {list-style-type: none;padding: 0px;}.loading2 {position: fixed;z-index: 999999999;height: 2em;width: 2em;overflow: show;margin: auto;top: 0;left: -30px;bottom: 0;right: 0;}.loading2 img {position: fixed;z-index: 9999999999;margin-bottom: 100px;overflow: show;margin: auto;top: -20%;left: 0;bottom: 0;right: 0;}.loading2:before {content: '';display: block;position: fixed;top: 0;left: 0;width: 100%;height: 100%;opacity: .7;background: radial-gradient(rgba(20, 20, 20,.8), rgba(0, 0, 0, .8));background: -webkit-radial-gradient(rgba(20, 20, 20,.8), rgba(0, 0, 0,.8));}.loading {position: fixed;z-index: 999999999;height: 2em;width: 2em;overflow: show;margin: auto;top: 0;left: 0;bottom: 0;right: 0;}.loading:before {content: '';display: block;position: fixed;top: 0;left: 0;width: 100%;height: 100%;background: radial-gradient(rgba(20, 20, 20,.8), rgba(0, 0, 0, .8));background: -webkit-radial-gradient(rgba(20, 20, 20,.8), rgba(0, 0, 0,.8));}.loading:not(:required) {font: 0/0 a;color: transparent;text-shadow: none;background-color: transparent;border: 0;}.loading:not(:required):after {content: '';display: block;font-size: 10px;width: 1em;height: 1em;margin-top: -0.5em;-webkit-animation: spinner 150ms infinite linear;-moz-animation: spinner 150ms infinite linear;-ms-animation: spinner 150ms infinite linear;-o-animation: spinner 150ms infinite linear;animation: spinner 150ms infinite linear;border-radius: 0.5em;-webkit-box-shadow: rgba(255,255,255, 0.75) 1.5em 0 0 0, rgba(255,255,255, 0.75) 1.1em 1.1em 0 0, rgba(255,255,255, 0.75) 0 1.5em 0 0, rgba(255,255,255, 0.75) -1.1em 1.1em 0 0, rgba(255,255,255, 0.75) -1.5em 0 0 0, rgba(255,255,255, 0.75) -1.1em -1.1em 0 0, rgba(255,255,255, 0.75) 0 -1.5em 0 0, rgba(255,255,255, 0.75) 1.1em -1.1em 0 0;box-shadow: rgba(255,255,255, 0.75) 1.5em 0 0 0, rgba(255,255,255, 0.75) 1.1em 1.1em 0 0, rgba(255,255,255, 0.75) 0 1.5em 0 0, rgba(255,255,255, 0.75) -1.1em 1.1em 0 0, rgba(255,255,255, 0.75) -1.5em 0 0 0, rgba(255,255,255, 0.75) -1.1em -1.1em 0 0, rgba(255,255,255, 0.75) 0 -1.5em 0 0, rgba(255,255,255, 0.75) 1.1em -1.1em 0 0;}@-webkit-keyframes spinner {0% {-webkit-transform: rotate(0deg);-moz-transform: rotate(0deg);-ms-transform: rotate(0deg);-o-transform: rotate(0deg);transform: rotate(0deg);}100% {-webkit-transform: rotate(360deg);-moz-transform: rotate(360deg);-ms-transform: rotate(360deg);-o-transform: rotate(360deg);transform: rotate(360deg);}}@-moz-keyframes spinner {0% {-webkit-transform: rotate(0deg);-moz-transform: rotate(0deg);-ms-transform: rotate(0deg);-o-transform: rotate(0deg);transform: rotate(0deg);}100% {-webkit-transform: rotate(360deg);-moz-transform: rotate(360deg);-ms-transform: rotate(360deg);-o-transform: rotate(360deg);transform: rotate(360deg);}}@-o-keyframes spinner {0% {-webkit-transform: rotate(0deg);-moz-transform: rotate(0deg);-ms-transform: rotate(0deg);-o-transform: rotate(0deg);transform: rotate(0deg);}100% {-webkit-transform: rotate(360deg);-moz-transform: rotate(360deg);-ms-transform: rotate(360deg);-o-transform: rotate(360deg);transform: rotate(360deg);}}@keyframes spinner {0% {-webkit-transform: rotate(0deg);-moz-transform: rotate(0deg);-ms-transform: rotate(0deg);-o-transform: rotate(0deg);transform: rotate(0deg);}100% {-webkit-transform: rotate(360deg);-moz-transform: rotate(360deg);-ms-transform: rotate(360deg);-o-transform: rotate(360deg);transform: rotate(360deg);}} .badge[class*='badge-'] span { bottom: 0px !important; }
    #loading {width: 100%;height: 100%;top: 0;left: 0;position: fixed;display: block;opacity: 0.7;background-color: #fff;z-index: 999999;text-align: center; }#loading-image {top: 50%;/*width: 200px;*/position: absolute;left: 44%;margin: 0 auto;z-index: 999999;} .navbar-dark .navbar-nav .nav-link:hover, .navbar-dark .navbar-nav .nav-link:focus { color: yellow !important; }
  }
  .modal{z-index:9999!important}.p-detail{margin-top:20px!important;gap:.2rem}.datagrid-cell-c1-aktif{text-align:center}.datagrid-cell .tree-icon.tree-file,.datagrid-cell .tree-icon.tree-folder{display:none}#fa-circle{font-size:1.2em;margin:0 4px}@font-face{font-family:Roboto;src:url('assets/font/Roboto-Light.woff2') format('woff2');font-weight:300;font-style:normal}@font-face{font-family:Roboto;src:url('assets/font/Roboto-Regular.woff2') format('woff2');font-weight:400;font-style:normal}@font-face{font-family:Roboto;src:url('assets/font/Roboto-Bold.woff2') format('woff2');font-weight:700;font-style:normal}@font-face{font-family:'Open Sans';src:url('assets/font/OpenSans-Light.woff2') format('woff2');font-weight:300;font-style:normal}@font-face{font-family:'Open Sans';src:url('assets/font/OpenSans-LightItalic.woff2') format('woff2');font-weight:300;font-style:italic}@font-face{font-family:'Open Sans';src:url('assets/font/OpenSans-Regular.woff2') format('woff2');font-weight:400;font-style:normal}@font-face{font-family:'Open Sans';src:url('assets/font/OpenSans-Italic.woff2') format('woff2');font-weight:400;font-style:italic}@font-face{font-family:'Open Sans';src:url('assets/font/OpenSans-SemiBold.woff2') format('woff2');font-weight:600;font-style:normal}@font-face{font-family:'Open Sans';src:url('assets/font/OpenSans-SemiBoldItalic.woff2') format('woff2');font-weight:600;font-style:italic}@font-face{font-family:'Open Sans';src:url('assets/font/OpenSans-Bold.woff2') format('woff2');font-weight:700;font-style:normal}@font-face{font-family:'Open Sans';src:url('assets/font/OpenSans-BoldItalic.woff2') format('woff2');font-weight:700;font-style:italic}@font-face{font-family:Muli;src:url('assets/font/Muli-Light.woff2') format('woff2');font-weight:300;font-style:normal}@font-face{font-family:Muli;src:url('assets/font/Muli-Regular.woff2') format('woff2');font-weight:400;font-style:normal}@font-face{font-family:Muli;src:url('assets/font/Muli-Medium.woff2') format('woff2');font-weight:500;font-style:normal}@font-face{font-family:Muli;src:url('assets/font/Muli-Bold.woff2') format('woff2');font-weight:700;font-style:normal}body{font-family:'Open Sans',Muli,sans-serif}
  tbody tr.odd, tr.even { cursor: pointer; } @font-face { font-family: 'Sacramento'; src: url('/lib/MPDF60/ttfonts/Sacramento-Regular.ttf') format('truetype'); font-weight: normal; font-style: normal; } .font-sacramento { font-family: 'Sacramento', cursive;}
  </style>
<script type="text/javascript">
    function onloadBody() {
      $('#loading').hide();
    }
    function getNotif() {
      $.getJSON("main/getNotif", function(json) {
        $('#notif_count').html(json.count);
        $('#notif_message').html(json.data);
      });
    }
  </script>
  <div id="loading">
    <img id="loading-image" src="<?php echo base_url('images') ?>/loader-page2.gif" alt="Loading..." />
    <!-- <span id="loading-image" class="fa fa-spinner fa-pulse fa-5x fa-fw"></span> -->
  </div>
  <body class="backtotop horizontal-layout horizontal-menu horizontal-menu-padding 2-columns menu-expanded" data-open="hover" data-menu="horizontal-menu" data-col="2-columns" onload="onloadBody(); getNotif()">
  <!-- Back to top button -->
  <div class="loading" id="loadingGif" style="display: none"></div>

  <a id="buttontotop"></a>

  <!-- Navigation -->
    <!-- fixed-top-->
    <nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-static-top navbar-dark navbar-border navbar-brand-center">
      <div class="navbar-wrapper">
        <div class="navbar-header">
          <ul class="nav navbar-nav flex-row">
            <li class="nav-item mobile-menu d-md-none mr-auto">
              <a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu font-large-1"></i></a>
            </li>
            <li class="nav-item d-block d-sm-none">
              <a class="navbar-brand" href="<?= base_url() ?>">
                <img class="brand-logo" src="<?= SYSTEM_LOGO_URL_WHITE ?>" style="width: 310px !important">
              </a>
            </li>
            <li class="nav-item d-md-none">
              <a class="nav-link open-navbar-container" data-toggle="collapse" data-target="#navbar-mobile">
                <i class="fa fa-ellipsis-v"></i>
              </a>
            </li>
          </ul>
        </div>
        <div class="navbar-container container center-layout">
          <div class="collapse navbar-collapse" id="navbar-mobile">
            <ul class="nav navbar-nav mr-auto float-left">
              <li class="nav-item" style="margin-top: 10px;">
                  <ul class="date-analog" style="text-align: left !important;">
                    <span id="Date"><?= date('Y-m-d') ?></span>
                    <li id="hours">- -</li> <li id="point">:</li> <li id="min">- -</li> <li id="point">:</li> <li id="sec">- -</li> <li id="sec" style="padding-left: 1px" class="badge primary badge-border"> WIB </li> <!-- labelClock -->
                  </ul>
              </li>
              <li class="nav-item d-none d-md-block">
                <a class="nav-link nav-link-expand" href="#"><i class="ficon ft-a"></i></a>
              </li>
            </ul> 
            <?php
            if($this->USER_LOGIN_ID != "")
            {
              if($this->USER_TYPE_ID == 6) // REKANAN
              {
                $user_login->selectByParams(array("REKANAN_ID" => $this->ID));
                $user_login->firstRow();
                $user_status = $user_login->getField("USER_STATUS");
              } else {
                $user_status = '';
              }

              $this->load->model("UserLogin");
              $user_login = new UserLogin();
              $adaKelengkapanData = $user_login->getCountByParams(array("USER_LOGIN_ID"=> $this->USER_LOGIN_ID, "USER_STATUS|| IN " => "(0,2)"));

              $user_login_jabatan = new UserLogin();
              $user_login_jabatan->selectByParams2(array("USER_LOGIN_ID"=> $this->USER_LOGIN_ID));
              $user_login_jabatan->firstRow();

            ?>
            <ul class="nav navbar-nav">
              <?php 
              $this->load->model("Userloginmulti");
              $user_login_multi = new Userloginmulti();
              $user_login_multi->selectByParams(array("USER_LOGIN_ID"=>$this->USER_LOGIN_ID),-1,-1); 
              if ($user_login_multi->countRow() > 0) { 
              ?>
              <li class="dropdown dropdown-notification nav-item">
                <a class="nav-link nav-link-label" onclick="openMulti('<?= $this->USER_LOGIN_ID ?>')" target="_blank">
                  <span class="badge badge-pill badge-default badge-warning badge-default" style="margin-left:-10px">
                  <i class="fa fa-users"></i>
                  Multi Role</span>
                </a> 
              </li>
              <?php 
              } ?>
              <li class="dropdown dropdown-notification nav-item">
                <a class="nav-link nav-link-label" href="login/manualbook" target="_blank">
                  <span class="badge badge-pill badge-default badge-success badge-default" style="margin-left:-10px">
                  <i class="fa fa-book"></i>
                  Manual Book</span>
                </a> 
              </li>
              <?php 
              if (in_array($this->USER_TYPE_ID,array('12','20','28'))) 
              { ?>
              <li class="dropdown dropdown-notification nav-item">
                <a class="nav-link nav-link-label" href="kontrak/index/work_list">
                  <span class="badge badge-pill badge-default badge-primary badge-default" style="margin-left:-10px">
                  <i class="fa fa-pencil"></i>
                  Work List</span>
                </a> 
              </li>
              <?php 
              } ?>
              <li class="dropdown dropdown-notification nav-item">
                <a class="nav-link nav-link-label" href="#" data-toggle="dropdown">
                  <i class="ficon ft-bell"></i>
                  <span class="badge badge-pill badge-default badge-danger badge-default" style="margin-left:-10px" id="notif_count"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" id="notif_message">
                </div>
              </li>
            </ul>
              <span class="user-name" style="color: #fff;">
                <?php
                if($this->USER_TYPE_ID == 6) // REKANAN
                {
                  if ($adaKelengkapanData == '0') {
                    $this->load->model("Users");
                    $cekBlacklist = new Users();
                    $cekBlacklist->selectBlacklistByRekananId($this->ID);
                    $cekBlacklist->firstRow();
                    $akunBlackList = $cekBlacklist->getField("BLACKLIST_ID");
                    if ($akunBlackList == "") {
                      echo '<i class="fa fa-check-square-o btn btn-primary" style="padding:2px 6px !important"></i> ';
                    } else {
                      echo '<i class="fa fa-close btn btn-black" style="padding:2px 6px !important"></i> ';
                    }
                  } else {
                    echo '<i class="fa fa-close btn btn-danger" style="padding:2px 6px !important"></i> ';
                  }
                echo $this->REKANAN;
                } else {
                echo $this->USER_NAMA;
                }
               ?><br>
                <small class="pull-right" style="font-size: 10px">
                  <?php
                  if ($this->USER_TYPE_ID == '6') { // REKANAN
                    if ($this->REKANAN_TIPE_ID == '7') {
                      echo $this->USER_TYPE.' PERORANGAN';
                    } else {
                      echo $this->USER_TYPE;
                    }
                  } else {
                    echo '<span class="badge badge-dark" style="background-color: #283c4d !important">'.$user_login_jabatan->getField('TYPE_NAMA').' - <i>'.$this->libbreadcrumb->unitkerja($this->UNIT_KERJA_ID).'</span></i><br>'.$this->DEPARTMENT;
                  }
                  ?>
                </small>
              </span>
            <?php
            }?>
          </div>
        </div>
      </div>
    </nav>

    <!-- Modal -->
    <div class="modal fade text-left" id="iconForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel34" aria-hidden="true">
      <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h3 class="modal-title" id="myModalLabel34">Login</h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        </div>

        <form class="navbar-form pull-right" id="fflogin" method="post" enctype="multipart/form-data">
          <div class="modal-body">
            <label for="username">Username: </label>
            <div class="form-group position-relative has-icon-left">
              <input type="text" name="reqUser" placeholder="Username" class="form-control easyui-validatebox" required="">
              <div class="form-control-position">
                <i class="ft-user font-medium-5 line-height-1 text-muted icon-align"></i>
              </div>
            </div>

            <label for="pass">Password: <i id="showPass" class="fa fa-eye-slash" style="cursor:pointer" title="lihat password"></i><i id="hidePass" class="fa fa-eye" style="cursor:pointer;display:none" title="Sembunyikan password"></i></label>
            <div class="form-group position-relative has-icon-left">
              <input type="password" name="reqPasswd" id="reqPassword" placeholder="Password" class="form-control easyui-validatebox" required="" autocomplete="off">
              <div class="form-control-position">
                <i class="ft-lock font-large-1 line-height-1 text-muted icon-align"></i>
              </div>
            </div>
            <div class="form-group position-relative has-icon-left">
              <a href="main/index/lupa_password" class="mr-1">Lupa Password ?</a>
            </div>


            <div class="form-group position-relative has-icon-left">
                <?php // echo $captcha // tampilkan recaptcha ?>
            </div>

            <div class="form-group position-relative has-icon-left">
                <img src="<?php // base_url() ?>main/loadUrl/main/CaptchaSecurityImages/?&width=100&height=40&characters=5" id="captchaImage" style="margin-bottom: 10px"/>&nbsp;&nbsp;&nbsp;<i class="icon-refresh" onclick="reloadCaptcha()" style="cursor:pointer;" title="refresh captcha"></i>

                <input id="labellogin" required name="security_code" type="text" title="Kode harus diisi" class="form-control easyui-validatebox" validType="remote['fungsi_json/captcha_validation', 'security_code']" invalidMessage="Kode validasi salah." placeholder="ketik kode" style="width: 68%; float: right; padding-left: calc(1rem + 2px);" />
            </div>

            <div style="color: red;text-align: center; width: 100%">
              <span id="messagelogin"></span>
            </div>
            <div class="col-md-12 mt-1" style="padding:10px 0px !important; border-top: 1px solid #eceeef;">
              <?=$csrfLogin->echoInputField();?>
              <!-- <input type="reset" class="btn round btn-min-width box-shadow-1 btn-outline-secondary btn-lg" data-dismiss="modal" value="close"> -->
              <button type="submit"class="btn round btn-min-width box-shadow-1 btn-outline-primary btn-lg mr-1" value="Login" id="btnLogin"><i class="fa fa-sign-in"></i> Login</button>
              <a href="main/index/registrasi" class="btn round btn-min-width box-shadow-1 btn-outline-danger btn-lg ml-1 pull-right"><i class="icon-user-follow"></i> Registrasi Penyedia</a>
              <?=$csrfLogin->echoInputField();?>
            </div>
          </div>
          <!-- <div class="modal-footer text-center" style="border: transparent !important;">
          </div> -->
        </form>
      </div>
      </div>
    </div>
    <!-- End Modal -->

    <!-- ////////////////////////////////////////////////////////////////////////////-->
    <div class="header-navbar navbar-expand-sm navbar navbar-horizontal navbar-fixed navbar-light navbar-without-dd-arrow navbar-shadow" role="navigation" data-menu="menu-wrapper">
      <div class="navbar-container main-menu-content container center-layout" data-menu="menu-container">
        <ul class="nav navbar-nav float-left" id="main-menu-navigation" data-menu="menu-navigation">
          <a href="<?= base_url() ?>">
            <img class="brand-logo" src="<?= SYSTEM_LOGO_URL ?>" style="max-width: 100%; width: 210px !important">
          </a>
        </ul>

        <ul class="nav navbar-nav float-right" id="main-menu-navigation" data-menu="menu-navigation">
          <?php

          if($this->USER_TYPE_ID != 6 && $this->USER_TYPE_ID != "") // REKANAN
          {
            if ($this->USER_TYPE_ID == '2' || $this->USER_TYPE_ID == '18' || $this->USER_TYPE_ID == '19')
            { // Admin VMS  & Approval VMS & Rekomendasi VMS
          ?>
            <li class="nav-item">
              <a href="main/index/dashboardvms" class="nav-link <?php if ($pg == 'dashboardvms') { echo ' active'; } ?>">
              Dashboard
              </a>
            </li>
          <?php
            } else if ($this->USER_TYPE_ID == '1' || $this->USER_TYPE_ID == '10' || $this->USER_TYPE_ID == '25' || $this->USER_TYPE_ID == '26')
            { // 1:admin, 10:audit, 25:Direktur, 26:Senior Manager
            ?>

              <?php if ($this->USER_TYPE_ID == '26') { ?>
                <li class="nav-item">
                  <a href="main/index/dashboardheadmanager" class="nav-link <?php if ($pg == 'dashboardheadmanager') { echo ' active'; } ?>">
                  Dashboard
                  </a>
                </li>
                <li class="nav-item">
                  <a href="main/index/dashboardvms" class="nav-link <?php if ($pg == 'dashboardvms') { echo ' active'; } ?>">
                  Dashboard VMS
                  </a>
                </li>
                <li class="dropdown nav-item" data-menu="dropdown">
                  <a href="#" class="dropdown-toggle nav-link <?php if ($pg == 'daftar_rekanan_belum_valid' || $pg == 'daftar_rekanan_valid' || $pg == 'daftar_rekanan_akses' || $pg == 'daftar_rekanan_delete' || $pg == 'daftar_rekanan_potensi') { echo ' active'; } ?>" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Manajemen <?= LABEL_PENYEDIA ?> </a>
                  <ul class="dropdown-menu">
                    <li <?php if ($pg == 'daftar_rekanan_belum_valid') { echo 'class="active"'; } ?>>
                      <a class="dropdown-item" href="main/index/daftar_rekanan_belum_valid">Daftar <?= LABEL_PENYEDIA ?></a>
                    </li>
                    <li <?php if ($pg == 'daftar_rekanan_valid' || $pg == 'daftar_rekanan_akses') { echo 'class="active"'; } ?>>
                      <a class="dropdown-item" href="main/index/daftar_rekanan_valid">Terverifikasi</a>
                    </li>
                    <li <?php if ($pg == 'daftar_rekanan_potensi') { echo 'class="active"'; } ?>>
                      <a class="dropdown-item" href="main/index/daftar_rekanan_potensi">Pencarian Potensi</a>
                    </li>
                  </ul>
                </li>
              <?php } else { ?>
                <li class="nav-item">
                  <a href="main/index/dashboardall" class="nav-link <?php if ($pg == 'dashboardall') { echo ' active'; } ?>">
                  Dashboard
                  </a>
                </li>
              <?php } ?>
          <?php
            } else if ($this->USER_TYPE_ID == '7')
            { // 7:kepala pengadaan
            ?>
              <li class="nav-item">
                <a href="main/index/dashboardhead" class="nav-link <?php if ($pg == 'dashboardhead') { echo ' active'; } ?>">
                Dashboard
                </a>
              </li>
          <?php
            } else if ($this->USER_TYPE_ID == '11')
            { // 11:pejabat pengadaan
            ?>
              <li class="nav-item">
                <a href="main/index/dashboardpembeli" class="nav-link <?php if ($pg == 'dashboardpembeli') { echo ' active'; } ?>">
                Dashboard
                </a>
              </li>
          <?php
            } else if ($this->USER_TYPE_ID == '9')
            { // 9:perencana, 27: Perencana
              $getTahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
            ?>
              <li class="nav-item">
                <a href="main/index/dashboardperencana?tahun=<?= $getTahun ?>" class="nav-link <?php if ($pg == 'dashboardperencana') { echo ' active'; } ?>">
                Dashboard
                </a>
              </li>
          <?php
          } else if ($this->USER_TYPE_ID == '27' || $this->USER_TYPE_ID == '28')
            { // 27:Perencana, 28: PPK
              $getTahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
            ?>
              <li class="nav-item">
                <a href="main/index/dashboardperencanadiv?tahun=<?= $getTahun ?>" class="nav-link <?php if ($pg == 'dashboardperencanadiv') { echo ' active'; } ?>">
                Dashboard
                </a>
              </li>
          <?php
            } else if ($this->USER_TYPE_ID == '21')
            { //21: Unit Instalasi
            ?>
              <li class="nav-item">
                <a href="main/index/dashboardunit" class="nav-link <?php if ($pg == 'dashboardunit') { echo ' active'; } ?>">
                Dashboard
                </a>
              </li>
        <?php
            } else if ($this->USER_TYPE_ID == '17')
            { //17: Verifikator Unit
            ?>
              <li class="nav-item">
                <a href="main/index/dashboardunitverifikator" class="nav-link <?php if ($pg == 'dashboardunitverifikator') { echo ' active'; } ?>">
                Dashboard
                </a>
              </li>
        <?php
            } else if ($this->USER_TYPE_ID == '22')
            { //22: Validator Unit
            ?>
              <li class="nav-item">
                <a href="main/index/dashboardunitvalidator" class="nav-link <?php if ($pg == 'dashboardunitvalidator') { echo ' active'; } ?>">
                Dashboard
                </a>
              </li>
        <?php
            } else if ($this->USER_TYPE_ID == '23')
            { //23: Approval Unit
            ?>
              <li class="nav-item">
                <a href="main/index/dashboardunitapproval" class="nav-link <?php if ($pg == 'dashboardunitapproval') { echo ' active'; } ?>">
                Dashboard
                </a>
              </li>
        <?php
            } else
            {
                if ($this->USER_TYPE_ID == '3')
                { // 3:panitia,
                  // khusus panitia yang menjabat sebagai kepala pengadaan juga
                  $this->load->model("UserLogin");
                  $user_login_jabatan = new UserLogin();
                  $user_login_jabatan->selectByParams(array("USER_LOGIN_ID"=> $this->USER_LOGIN_ID));
                  $user_login_jabatan->firstRow();
                  if ($user_login_jabatan->getField('PENUNJUK_PIC') == '1') {
                ?>
                  <li class="nav-item">
                    <a href="main/index/dashboardall" class="nav-link <?php if ($pg == 'dashboardall') { echo ' active'; } ?>">
                    Dashboard
                    </a>
                  </li>
                  <?php
                  } else
                  {  ?>
                  <li class="nav-item">
                    <a href="main/index/dashboard" class="nav-link <?php if ($pg == 'dashboard') { echo ' active'; } ?>">
                    Dashboard
                    </a>
                  </li>
          <?php
                  }
                } else if ($this->USER_TYPE_ID == '12' || $this->USER_TYPE_ID == '20')
                { // 12: Pengelola Kontrak, 20: Pemeriksa Kontrak
                  $getTahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
                  if ($this->LEGAL != '1') { // pengelola kontrak, pemeriksa kontrak
          ?>
                  <li class="nav-item">
                    <a href="kontrak/index/dashboardkontrak?tahun=<?= $getTahun ?>" class="nav-link <?php if ($pg == 'dashboardkontrak') { echo ' active'; } ?>">
                      Dashboard
                    </a>
                  </li>
          <?php
                  }
              }
            }
          } else {
            if ($this->USER_TYPE_ID == "") {
          ?>
          <li class="nav-item">
            <a href="<?= base_url() ?>" class="nav-link <?php if ($pg == 'home' || $pg == '') { echo ' active'; } ?>">
              Home</a>
          </li>
          <?php
            }
          }
          if ($this->USER_TYPE_ID == "" || $this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 10 || $this->USER_TYPE_ID == 6 || $this->USER_TYPE_ID == 9 || $this->USER_TYPE_ID == 7 || $this->USER_TYPE_ID == 28 || $this->USER_TYPE_ID == 20) { // Panitia, Auditor, Penyedia, PPKom/pengguna
          ?>
          <li class="nav-item">
            <a href="main/index/tender" class="nav-link <?php if ($pg == 'tender') { echo ' active'; } ?>">
              Tender
              <?php
              if ($this->USER_TYPE_ID == 6)
              {
                 $countTender = new Queryfree();
                 $countTender->selectByParams("SELECT PESERTA FROM view_dashboard_paket_proses WHERE PUBLISH_PAKET = '1' AND PROSES = '1' AND PESERTA && ARRAY[".$this->REKANAN_ID."] AND PAKET_METODE_LELANG_ID IN (1,3,4,7,10)");
                 // $countTender->firstRow();
                 // $totalFolTender = $countTender->getField('total');
                 if ($countTender->countRow() > 0) {
                  echo '<sup class="badge badge-info" style="font-size:.7em; padding: .0em .4em !important;">'.$countTender->countRow().'</sup>';
                }
              }
              ?>
            </a>
          </li>
          <?php
          }

          if ($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 6 || $this->USER_TYPE_ID == 9 || $this->USER_TYPE_ID == 10 || $this->USER_TYPE_ID == 11 || $this->USER_TYPE_ID == 28 || $this->USER_TYPE_ID == 20) { ?>
          <li class="nav-item">
            <a href="main/index/tendernon" class="nav-link <?php if ($pg == 'tendernon') { echo ' active'; } ?>">
              Non Tender
              <?php
              if ($this->USER_TYPE_ID == 6) {
                 $countNonTender = new Queryfree();
                 $countNonTender->selectByParams("SELECT PESERTA FROM view_dashboard_paket_proses WHERE PUBLISH_PAKET = '1' AND PROSES = '1' AND PESERTA && ARRAY[".$this->REKANAN_ID."] AND PAKET_METODE_LELANG_ID IN (2,5,6,8,9)");
                 if ($countNonTender->countRow() > 0) {
                  echo '<sup class="badge badge-info" style="font-size:.7em; padding: .0em .4em !important;">'.$countNonTender->countRow().'</sup>';
                  }
                }
              ?>
            </a>
          </li>
          <?php
          }
              if ($this->USER_TYPE_ID == '18')  // Approval VMS
              {
           ?>
            <li class="nav-item">
              <a href="main/index/daftar_rekanan_approval" class="nav-link <?php if ($pg == 'daftar_rekanan_approval') { echo ' active'; } ?>">
                Approval Penyedia
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?php if ($pg == 'daftar_rekanan_valid') { echo ' active'; } ?>" href="main/index/daftar_rekanan_valid">Penyedia Terverifikasi</a>
            </li>
            <?php
            } ?>
          <?php
              if ($this->USER_TYPE_ID == '19')  // Rekomendasi VMS
              {
           ?>
            <li class="nav-item">
              <a href="main/index/daftar_rekanan_approval_rekomendasi" class="nav-link <?php if ($pg == 'daftar_rekanan_approval_rekomendasi') { echo ' active'; } ?>">
                Approval Penyedia </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?php if ($pg == 'daftar_rekanan_valid') { echo ' active'; } ?>" href="main/index/daftar_rekanan_valid">Daftar Penyedia</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?php if ($pg == 'daftar_rekanan_potensi') { echo ' active'; } ?>" href="main/index/daftar_rekanan_potensi">Potensi Penyedia</a>
            </li>
            <?php
            }
          if($this->USER_TYPE_ID == "" || $this->USER_TYPE_ID == "24")
          {
          ?>
         <!--  <li class="nav-item">
            <a href="main/index/rup" class="nav-link <?php // if ($pg == 'rup') { echo ' active'; } ?>">
              Pengumuman RUP</a>
          </li> -->
          <?php
          }

          ?>
          <?php
          if($this->USER_TYPE_ID == "")
          { ?>
          <li class="nav-item">
            <a href="main/index/blacklist" class="nav-link <?php if ($pg == 'blacklist') { echo ' active'; } ?>">
              Daftar Hitam</a>
          </li>
          <li class="nav-item">
            <a href="main/index/kontak" class="nav-link <?php if ($pg == 'kontak') { echo ' active'; } ?>">
              Kontak Kami</a>
          </li>
          <?php
          } ?>
          <!-- <li class="nav-item">
            <a href="main/index/berita" class="nav-link"> Berita</a>
          </li>
          <li class="nav-item">
            <a href="main/index/kontak" class="nav-link"> Kontak</a>
          </li> -->
          <?php 
          if ($this->USER_TYPE_ID == '10' || $this->USER_TYPE_ID == '25') { // Auditor; Direktur
          ?>
            <li class="nav-item">
              <a href="main/index/rencana_umum_pengadaan_persiapan" class="nav-link <?php if ($pg == "rencana_umum_pengadaan_persiapan") { echo 'active'; } ?>">
                Persiapan
              </a>
            </li>
          <?php
          }
          if($this->USER_TYPE_ID == 9) // PENGGUNA
          {
            $arrayePlanning = array('rencana_umum_pengadaan','rencana_umum_pengadaan_persiapan','permohonan_paket_usulan_add','permohonan_paket_usulan_pengguna');
            $arrayePlanningAnalisaPasar = array('daftar_potensi','inbox_rfi','inbox_rfi_add');
            $arrayeInbox = array('inbox_rfi','inbox_rfi_add');

          if ($this->LEVEL_PENGGUNA == '1') { 
          ?>
            <li class="nav-item">
              <a href="kontrak/index/contracting_pengguna" class="nav-link <?php if ($pg == "contracting_pengguna") { echo 'active'; } ?>">
                Penilaian
              </a>
            </li>
          <?php
          } else {
          ?>
            <li class="dropdown nav-item" data-menu="dropdown">
                <a class="dropdown-toggle nav-link <?php if (in_array($pg, $arrayePlanning)) { echo ' active'; } ?>" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                  Planning
                </a>
                <ul class="dropdown-menu"> 
                    <li <?php if ($pg == 'rencana_umum_pengadaan' || $pg == 'permohonan_paket_usulan_pengguna') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/rencana_umum_pengadaan">RUP</a></li>
                    <li <?php if ($pg == 'rencana_umum_pengadaan_persiapan' || $pg == 'rencana_umum_pengadaan_persiapan_add') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/rencana_umum_pengadaan_persiapan">Persiapan</a></li>
                </ul>
            </li>

            <li class="dropdown nav-item" data-menu="dropdown">
                <a href="#" class="dropdown-toggle nav-link <?php if (in_array($pg, $arrayePlanningAnalisaPasar)) { echo ' active'; } ?>" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Survei Pasar
                </a>
                <ul class="dropdown-menu">
                  <li <?php if ($pg == 'daftar_potensi') { echo ' class="active"'; } ?>>
                    <a class="dropdown-item" href="main/index/daftar_potensi">Potensi Penyedia</a>
                  </li>
                  <li <?php if (in_array($pg, $arrayeInbox)) { echo ' class="active"'; } ?>>
                    <a href="main/index/inbox_rfi" class="dropdown-item">RFI/Market Sounding</a>
                  </li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="kontrak/index/contracting_pengguna" class="nav-link <?php if ($pg == "contracting_pengguna") { echo 'active'; } ?>">
                  Penilaian
                </a>
              </li>

            <?php
            }
          }

          if($this->USER_TYPE_ID == 27 || $this->USER_TYPE_ID == 28) // 27:PERENCANA, 28:PPK
          {
            $arrayePlanning = array('rencana_umum_pengadaan','permohonan_paket_usulan_add','rencana_umum_pengadaan_persiapan','permohonan_paket_pic_add');
          ?>
            <li class="dropdown nav-item" data-menu="dropdown">
                <a class="dropdown-toggle nav-link <?php if (in_array($pg, $arrayePlanning)) { echo ' active'; } ?>" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                  Planning
                </a>
                <ul class="dropdown-menu">
                    <li <?php if ($pg == 'rencana_umum_pengadaan') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/rencana_umum_pengadaan">RUP</a></li>
                    <li <?php if ($pg == 'rencana_umum_pengadaan_persiapan' || $pg == 'permohonan_paket_usulan_add' || $pg == 'permohonan_paket_pic_add') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/rencana_umum_pengadaan_persiapan">Persiapan</a></li>
                </ul>
            </li>

              <?php
          } 
          if($this->USER_TYPE_ID == 27) // 27:PERENCANA
          {
          ?>

            <li class="nav-item">
              <a href="main/index/permohonan_paket_kaji_ulang" class="nav-link <?php if ($pg == 'permohonan_paket_kaji_ulang' || $pg == 'permohonan_paket_kaji_ulang_add') { echo ' active'; } ?>">
                Kaji Ulang
              </a>
            </li>

          <?php 
          }

          if($this->USER_TYPE_ID == 28) // 28:PPK
          {
            $arrayePlanning = array('contracting_paket','contracting_persiapan','contracting_pengelolaan','contracting_serah_terima','contracting_selesai');
            $getTahunKontrak = $this->session->userdata('setTahunKontrak') ?: 'all';
          ?>
            <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu">
                <a class="dropdown-toggle nav-link <?php if (in_array($pg, $arrayePlanning)) { echo ' active'; } ?>" href="#" data-toggle="dropdown">Proses Kontrak</a>
                  <ul class="dropdown-menu">
                      <li <?php if ($pg == 'contracting_paket') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="kontrak/index/contracting_paket">Selesai Pemilihan</a></li>
                      <li <?php if ($pg == 'contracting_persiapan') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="kontrak/index/contracting_persiapan">Persiapan</a></li>
                      <li <?php if ($pg == 'contracting_pengelolaan') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="kontrak/index/contracting_pengelolaan">Pengendalian</a></li>
                      <li <?php if ($pg == 'contracting_selesai') { echo 'class="active"'; } ?>> 
                        <a class="dropdown-item" href="kontrak/index/contracting_selesai">Selesai Kontrak</a></li>
                      <!-- <li <?php if ($pg == 'contracting_persiapan') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="kontrak/index/contracting_persiapan">Kontrak</a></li> -->
                  </ul>
              </li>

              <?php
          }

          if($this->USER_TYPE_ID == 21) // UNIT INSTALASI
          { $arrayePlanning = array('permohonan_paket_fungsional','permohonan_paket_usulan_add','paket_lelang_tambah_rincian_pekerjaan_permohonan','permohonan_paket_usulan');
            $arrayeInbox = array('inbox_rfi','inbox_rfi_add'); ?>

            <li class="dropdown nav-item <?php if (in_array($pg, $arrayePlanning)) { echo ' active'; } ?>" data-menu="dropdown">
                <a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown">
                  Planning
                </a>
                <ul class="dropdown-menu">
                    <li <?php if ($pg == 'permohonan_paket_usulan' || $pg == 'permohonan_paket_usulan_add') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/permohonan_paket_usulan">Usulan Kebutuhan</a></li>
                    <li <?php if ($pg == 'permohonan_paket_unit' || $pg == 'paket_lelang_tambah_rincian_pekerjaan_permohonan') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/permohonan_paket_unit">Rencana Pengadaan</a></li>
                </ul>
            </li>


          <?php
          }

          if($this->USER_TYPE_ID == 17) // VERIFIKATOR UNIT
          {
            $arrayePlanning = array('permohonan_paket_usulan_admin','permohonan_paket_fungsional_admin','permohonan_paket_usulan_admin_to_be_approved','permohonan_paket_fungsional_rup');
          ?>
            <li class="dropdown nav-item" data-menu="dropdown">
                <a class="dropdown-toggle nav-link <?php if (in_array($pg, $arrayePlanning)) { echo ' active'; } ?>" href="#" data-toggle="dropdown">
                  Planning
                </a>
                <ul class="dropdown-menu">
                  <li <?php if ($pg == 'permohonan_paket_usulan_admin') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/permohonan_paket_usulan_admin">Usulan Kebutuhan</a></li>
                  <li <?php if ($pg == 'permohonan_paket_fungsional_rup') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/permohonan_paket_fungsional_rup">Rencana Pengadaan</a></li>
                </ul>
            </li>
          <?php
          }

          if($this->USER_TYPE_ID == 22) // VALIDATOR UNIT
          {

            $arrayePlanning = array('permohonan_paket_usulan_validator','permohonan_paket_fungsional_admin');
          ?>
            <li class="dropdown nav-item <?php if (in_array($pg, $arrayePlanning)) { echo ' active'; } ?>" data-menu="dropdown">
                <a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown">
                  Planning
                </a>
                <?php
                if ($this->VALIDATOR_UNIT == 1) { // Perencana ?>
                <ul class="dropdown-menu">
                  <li <?php if ($pg == 'permohonan_paket_usulan_validator') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/permohonan_paket_usulan_validator">Usulan Kebutuhan</a></li>
                  <!-- <li <?php if ($pg == 'permohonan_paket_fungsional_admin') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/permohonan_paket_fungsional_admin">Rencana Pengadaan</a></li> -->
                </ul>

                <?php
                } else { // Kauangan?>
                <ul class="dropdown-menu">
                  <li <?php if ($pg == 'permohonan_paket_usulan_validator2') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/permohonan_paket_usulan_validator2">Usulan Kebutuhan</a></li>
                  <!-- <li <?php if ($pg == 'permohonan_paket_fungsional_admin') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/permohonan_paket_fungsional_admin">Rencana Pengadaan</a></li> -->
                </ul>
                <?php
                } ?>
            </li>
          <?php
          }

          if($this->USER_TYPE_ID == 23) // APPROVAL UNIT
          {
            $arrayePlanning = array('permohonan_paket_usulan_approval','permohonan_paket_fungsional_admin');
          ?>
            <li class="dropdown nav-item <?php if (in_array($pg, $arrayePlanning)) { echo ' active'; } ?>" data-menu="dropdown">
                <a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown">
                  Planning
                </a>
                <?php
                if ($this->APPROVAL_UNIT == 1) { // PKAP ?>
                <ul class="dropdown-menu">
                  <li <?php if ($pg == 'permohonan_paket_usulan_approval') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/permohonan_paket_usulan_approval">Usulan Kebutuhan</a></li>
                  <!-- <li <?php if ($pg == 'permohonan_paket_fungsional_admin') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/permohonan_paket_fungsional_admin">Rencana Pengadaan</a></li> -->
                </ul>
                <?php
                } else { // KPA
                ?>
                 <ul class="dropdown-menu">
                  <li <?php if ($pg == 'permohonan_paket_usulan_approval_kpa') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/permohonan_paket_usulan_approval_kpa">Usulan Kebutuhan</a></li>
                  <!-- <li <?php if ($pg == 'permohonan_paket_fungsional_admin') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/permohonan_paket_fungsional_admin">Rencana Pengadaan</a></li> -->
                </ul>
                <?php
                } ?>
            </li>
          <?php
          }

          if($this->USER_TYPE_ID == 24) // ADMIN RUP
          {
          ?>
            <li class="nav-item">
              <a href="main/index/permohonan_paket_fungsional_rup" class="nav-link <?php if ($pg == 'permohonan_paket_fungsional_rup') { echo ' active'; } ?>">
                Rencana Pengadaan
              </a>
            </li>
          <?php
          }

          if($this->USER_TYPE_ID == 7) // KA MANAJER PENGADAAN (OLD:KEPALA PENGADAAN)
          {
            $arrayePlanning = array('permohonan_penunjukan_pic');
          ?>
            <!-- <li class="dropdown nav-item <?php //if (in_array($pg, $arrayePlanning)) { echo ' active'; } ?>" data-menu="dropdown">
                <a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown">
                  <span data-i18n="nav.templates.main">Planning</span>
                </a>
                <ul class="dropdown-menu"> -->
                    <!-- <li <?php // if ($pg == 'permohonan_penunjukan_pic') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/permohonan_penunjukan_pic">Penunjukan PIC Paket</a></li> -->
                    <!-- <li <?php // if ($pg == 'permohonan_paket_fungsional') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/permohonan_paket_fungsional">Rencana Pengadaan</a></li> -->
                <!-- </ul>
            </li> -->
            <li class="nav-item">
              <a href="main/index/permohonan_penunjukan_pic" class="nav-link <?php if ($pg == 'permohonan_penunjukan_pic') { echo ' active'; } ?>">
                Penunjukan PIC Paket
              </a>
            </li> 
            <li class="nav-item">
              <a href="main/index/permohonan_paket_kaji_ulang" class="nav-link <?php if ($pg == 'permohonan_paket_kaji_ulang' || $pg == 'permohonan_paket_kaji_ulang_add') { echo ' active'; } ?>">
                Kaji Ulang
              </a>
            </li>
            <li class="nav-item">
              <a href="main/index/paket_selesai_pemilihan" class="nav-link <?php if ($pg == 'paket_selesai_pemilihan') { echo ' active'; } ?>">
                Selesai Pemilihan
              </a>
            </li> 
          <?php
          }

          if($this->USER_TYPE_ID == 11) // PEJABAT PENGADAAN
          {
            $arrayPurchsAll = array('pembelian_langsung','katalog_tracking_pesanan','katalog_cart','katalog_negosiasi','katalog_surat_pesanan','katalog_tracking_pesanan','pembelian_offline','pembelian_pemerintah');
            ?>

            <li class="dropdown nav-item" data-menu="dropdown">
                <a class="dropdown-toggle nav-link <?php if (in_array($pg, $arrayPurchsAll)) { echo ' active'; } ?>" href="#" data-toggle="dropdown">
                  Pembelian
                </a>
                <ul class="dropdown-menu">
                  <li <?php if ($pg == 'pembelian_langsung') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/pembelian_langsung">Pembelian Katalog</a></li>
                  <li <?php if ($pg == 'pembelian_pemerintah') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/pembelian_pemerintah">Pembelian Katalog Pemerintah</a></li>
                  <li <?php if ($pg == 'pembelian_offline') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/pembelian_offline">Pembelian Langsung</a></li>
                </ul>
            </li>
          <?php
          }

          if($this->USER_TYPE_ID == 3 || $this->USER_TYPE_ID == 11) // PANITIA & PEJABAT PENGADAAN
          {
            $arrayePlanning = array('permohonan_paket_panitia');
          ?>
            <!-- <li class="dropdown nav-item <?php //if (in_array($pg, $arrayePlanning)) { echo ' active'; } ?>" data-menu="dropdown">
                <a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown">
                  <i class="icon-notebook"></i><span data-i18n="nav.templates.main">Planning</span>
                </a> -->
                <?php
                if ($user_login_jabatan->getField('PENUNJUK_PIC') == '1' ) { // Ketua Panitia & Penunjuk PIC
                 ?>
                  <!-- <li class="nav-item">
                    <a href="main/index/permohonan_penunjukan_pic" class="nav-link <?php // if ($pg == 'permohonan_penunjukan_pic') { echo ' active'; } ?>">
                     <i class="icon-user-follow"></i>Penunjukan PIC Paket
                    </a>
                  </li> -->
                  <li class="nav-item">
                    <a href="main/index/permohonan_paket_panitia" class="nav-link <?php if ($pg == 'permohonan_paket_panitia') { echo ' active'; } ?>">
                     Permohonan Paket
                    </a>
                  </li>
                  <!-- <ul class="dropdown-menu"> -->
                      <!-- <li <?php //if ($pg == 'permohonan_penunjukan_pic') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/permohonan_penunjukan_pic">Penunjukan PIC Paket</a></li> -->
                      <!-- <li <?php // if ($pg == 'permohonan_paket_fungsional') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/permohonan_paket_fungsional">Rencana Pengadaan</a></li> -->
                  <!-- </ul> -->
                <?php
                } else { // Panitia bukan ketua
                  if ($user_login_jabatan->getField('USER_JABATAN_PANITIA') == '1' || $user_login_jabatan->getField('USER_JABATAN_PANITIA') == '2' || $this->USER_TYPE_ID == 11 )  // Permohonan Khusus Penyelia
                  { ?>
                  <li class="nav-item">
                    <a href="main/index/permohonan_paket_panitia" class="nav-link <?php if ($pg == 'permohonan_paket_panitia') { echo ' active'; } ?>">
                      Permohonan Paket
                    </a>
                  </li>
                  <!-- <ul class="dropdown-menu">
                      <li <?php //if ($pg == 'permohonan_paket_panitia') { echo 'class="active"'; } ?>> <a class="dropdown-item" href="main/index/permohonan_paket_panitia">Permohonan Paket</a></li>
                  </ul> -->
                <?php
                  }
                }

                if ($this->USER_TYPE_ID == 3) { ?>
                  <li class="nav-item">
                    <a href="main/index/permohonan_paket_kaji_ulang" class="nav-link <?php if ($pg == 'permohonan_paket_kaji_ulang' || $pg == 'permohonan_paket_kaji_ulang_add') { echo ' active'; } ?>">
                      Kaji Ulang
                    </a>
                  </li>
                <?php 
                } ?>
            <!-- </li> -->

            <!-- <li class="nav-item">
              <a href="main/index/permohonan_paket_panitia" class="nav-link <?php // if ($pg == 'permohonan_paket_panitia') { echo ' active'; } ?>">
                Permohonan
              </a>
            </li> -->
          <?php
          } 

  
            $arrayePlanning = array('permohonan_penunjukan_pic');
            if($this->USER_TYPE_ID == 11 && $this->LEVEL_PEMBELI == '1') // PEJABAT PEMBELI AS PIC
            {
          ?> 
            <li class="nav-item">
              <a href="main/index/permohonan_penunjukan_pic" class="nav-link <?php if ($pg == 'permohonan_penunjukan_pic') { echo ' active'; } ?>">
                Penunjukan PIC Paket
              </a>
            </li> 
            <li class="nav-item">
              <a href="main/index/paket_selesai_pemilihan" class="nav-link <?php if ($pg == 'paket_selesai_pemilihan') { echo ' active'; } ?>">
                Selesai Pemilihan
              </a>
            </li> 
          <?php
            } 

         $arrayContractingPemilihan = array('contracting_paket','contracting_surat_perintah','contracting_surat_perjanjian');
         $arrayContractingPembelian = array('contracting_pembelian');
         $arrayContractingPersiapan = array('contracting_persiapan_sppbj','contracting_paket','contracting_persiapan','contracting_paket_sppbj','contracting_persiapan_kontrak','contracting_notifikasi','contracting_po');

          $arrayContractingProses = array('contracting_persiapan','contracting_pengelolaan','contracting_permasalahan','contracting_serah_terima','contracting_pengelolaan_realisasi','contracting_pengelolaan_termin','contracting_file','contracting_monitoring_realisasi','contracting_monitoring_termin','contracting_persiapan_sppbj','contracting_persiapan_kontrak','contracting_monitoring_perubahan','contracting_monitoring_harga','contracting_monitoring_kahar','contracting_monitoring_berakhir','contracting_monitoring_pemutusan','contracting_monitoring_kesempatan','contracting_monitoring_denda','contracting_serah_terima_hasil','contracting_serah_terima_pemeliharaan','contracting_persiapan_kontrak_edit_legal','contracting_penilaian');

          if($this->USER_TYPE_ID == 12 || $this->USER_TYPE_ID == 13 || $this->USER_TYPE_ID == 20) // 12:Pengelola Kontrak, 13:PPHP, 20:Pemeriksa Kontrak
          {
            $getTahunKontrak = $this->session->userdata('setTahunKontrak') ?: 'all';
          ?>
              <?php 
              if ($this->PENUNJUK_PIC == '1' && $this->LEVEL_KONTRAK == '1') { // ini khusus untuk level staff (peng. kontrak) ?>
              <li class="nav-item">
                <a href="kontrak/index/contracting_penunjukan_pic" class="nav-link <?php if ($pg == "contracting_penunjukan_pic") { echo ' active'; } ?>">
                  Penunjukan PIC
                </a>
              </li>
            <?php 
            } ?>

            <?php
            // if ($this->LEGAL == '0') { ?>

              <?php 
              if ($this->LEVEL_KONTRAK == '1') { // Staff ?>
              <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu">
                <a class="dropdown-toggle nav-link <?php if (in_array($pg, $arrayContractingPersiapan)) { echo ' active'; } ?>" href="#" data-toggle="dropdown">Persiapan</a>
                  <ul class="dropdown-menu">
                      <li <?php if ($pg == 'contracting_paket_sppbj') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="kontrak/index/contracting_paket_sppbj">SPPBJ</a></li>
                      <li <?php if ($pg == 'contracting_persiapan') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="kontrak/index/contracting_persiapan">Kontrak</a></li>
                      <?php 
                      if ($this->PENUNJUK_PIC == '1') { ?>
                      <li <?php if ($pg == 'contracting_po') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="kontrak/index/contracting_po">Update No. PO</a></li>
                      <?php 
                      } ?>
                  </ul>
              </li>
              <li class="nav-item">
                <a href="kontrak/index/contracting_pengelolaan" class="nav-link <?php if ($pg == 'contracting_pengelolaan') { echo 'active'; } ?>">
                  Pengendalian
                </a>
              </li> 
              <?php 
              } 

              if ($this->LEVEL_KONTRAK == '2') { // Pengendali 
                $arrayContractingPembelian = array("contracting_pengelolaan","contracting_pengelolaan_realisasi","contracting_pengelolaan_termin","contracting_monitoring_perubahan","contracting_monitoring_kahar","contracting_monitoring_pemutusan","contracting_monitoring_denda","contracting_penilaian","contracting_serah_terima_hasil","contracting_serah_terima_pemeliharaan","contracting_monitoring_termin");
                ?>
              <li class="nav-item">
                <a href="kontrak/index/contracting_pengelolaan" class="nav-link <?php if (in_array($pg, $arrayContractingPembelian)) { echo 'active'; } ?>">
                  Pengendalian
                </a>
              </li>
              <?php 
              } 
              if ($this->LEVEL_KONTRAK == '3') { // Penyelesaian 
                $arrayContractingPembelian = array("contracting_serah_terima");
                ?>
              <li class="nav-item">
                <a href="kontrak/index/contracting_serah_terima" class="nav-link <?php if (in_array($pg, $arrayContractingPembelian)) { echo 'active'; } ?>">
                  Penyelesaian
                </a>
              </li>
              <?php 
              } ?>
              <li class="nav-item">
                <a href="kontrak/index/contracting_selesai" class="nav-link <?php if ($pg == 'contracting_selesai') { echo 'active'; } ?>">
                    Selesai Kontrak
                </a>
              </li>
            <!-- <li class="nav-item">
              <a href="kontrak/index/contracting_pembelian" class="nav-link <?php if (in_array($pg, $arrayContractingPembelian)) { echo 'active'; } ?>">
                Purchasing
              </a>
            </li>
            <li class="nav-item">
              <a href="kontrak/index/contracting_paket" class="nav-link <?php if (in_array($pg, $arrayContractingPemilihan)) { echo 'active'; } ?>">
                SPPBJ
              </a>
            </li>
            <li class="nav-item">
              <a href="kontrak/index/contracting_persiapan" class="nav-link <?php if (in_array($pg, $arrayContractingProses)) { echo 'active'; } ?>">
                  Kontrak
              </a>
            </li>
            <li class="nav-item">
              <a href="kontrak/index/contracting_selesai" class="nav-link <?php if ($pg == 'contracting_selesai') { echo 'active'; } ?>">
                  Selesai Kontrak
              </a>
            </li> -->
            <?php
          // }
          if ($this->LEGAL == '1') {
            ?>
            <li class="nav-item">
              <a href="kontrak/index/contracting_persiapan_legal" class="nav-link <?php if ($pg == 'contracting_persiapan_legal' ) { echo 'active'; } ?>">
                  Kontrak
              </a>
            </li>
            <?php
          }
            if ($this->USER_TYPE_ID == 20) { // Pemeriksa Kontrak
              ?>
              <li class="nav-item">
                <a href="kontrak/index/contracting_persiapan" class="nav-link <?php if (in_array($pg, array("contracting_persiapan"))) { echo 'active'; } ?>">
                    Persiapan
                </a>
              </li>
              <li class="nav-item">
                <a href="kontrak/index/contracting_pengelolaan" class="nav-link <?php if (in_array($pg, array("contracting_pengelolaan"))) { echo 'active'; } ?>">
                    Pengendalian
                </a>
              </li>
              <li class="nav-item">
                <a href="kontrak/index/contracting_serah_terima" class="nav-link <?php if (in_array($pg, array("contracting_serah_terima"))) { echo 'active'; } ?>">
                    Penyelesaian
                </a>
              </li>
              <li class="nav-item">
                <a href="kontrak/index/contracting_selesai" class="nav-link <?php if ($pg == 'contracting_selesai') { echo 'active'; } ?>">
                    Selesai Kontrak
                </a>
              </li>
          <?php
              }
              ?>

              <li class="nav-item">
                <a href="kontrak/index/blacklist_kontrak" class="nav-link <?php if ($pg == 'blacklist_kontrak') { echo 'active'; } ?>">
                    Daftar Hitam Kontrak
                </a>
              </li>
          <?php
          }
          ?>

          <?php
          if($this->USER_TYPE_ID == 6) // PENYEDIA
          { ?>
            <!-- <li class="nav-item">
              <a href="main/index/contracting_rekanan" class="nav-link <?php if ($pg == 'contracting') { echo ' active'; } ?>">
                Pelaksanaan Kontrak
              </a>
            </li> -->
          <?php
          }
          ?>

          <?php

          if($this->USER_TYPE_ID == "6") // REKANAN
          {
            // count penyedia yang sudah kirim berkas
            // $this->load->model("UserLogin");
            // $user_login = new UserLogin();
            // $adaKelengkapanData = $user_login->getCountByParams(array("USER_LOGIN_ID"=> $this->USER_LOGIN_ID, "USER_STATUS" => 1));

            $arrayDataAdministrasi = array('data_administrasi_umum','data_administrasi_umum_ubah_profile','data_administrasi_ijin_usaha','data_administrasi_ijin_usaha_ubah','data_administrasi_landasan_hukum','data_administrasi_landasan_hukum_ubah','data_administrasi_pengurus_perusahaan','data_administrasi_pengurus_perusahaan_tambah','data_administrasi_kepemilikan_saham','data_administrasi_kepemilikan_saham_tambah','data_administrasi_sbu','data_administrasi_sbu_ubah','data_administrasi_umum_perorangan','data_administrasi_umum_cv','registrasi_rekanan_cv','data_administrasi_umum_ubah_profile_perorangan');
            $arrayDataKeuangan = array('data_keuangan_rekening_koran','data_keuangan_rekening_koran_tambah','data_perpajakan_neraca','data_perpajakan_neraca_tambah');
            $arrayDataPerpajakan = array('data_perpajakan_pkp','data_perpajakan_pkp_ubah','data_perpajakan_spt_tahunan','data_perpajakan_spt_tahunan_ubah','data_perpajakan_pajak_bulanan','data_perpajakan_pajak_bulanan_tambah');
            $arrayDataTeknis = array('data_teknis_tenaga_ahli','data_teknis_tenaga_ahli_tambah','data_teknis_pengalaman','data_teknis_pengalaman_selesai_tambah','data_teknis_peralatan','data_teknis_peralatan_tambah','data_teknis_sertifikat_lain','data_teknis_sertifikat_lain_tambah');
            $arrayMerger = array_merge($arrayDataAdministrasi, $arrayDataKeuangan, $arrayDataPerpajakan, $arrayDataTeknis);
          if($user_status == 1) { // sudah diverifikasi
          ?>
            <li class="dropdown nav-item" data-menu="dropdown">
              <a class="dropdown-toggle nav-link <?php if (in_array($pg, $arrayMerger)) { echo ' active'; } ?>" href="#" data-toggle="dropdown">
                  Data Vendor
              </a>
              <ul class="dropdown-menu">
                <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown">Data Administrasi</a>
                  <ul class="dropdown-menu">
                    <?php
                    if ($this->REKANAN_TIPE_ID == '7') { // Perorangan?>
                      <li <?php if ($pg == 'data_administrasi_umum_perorangan' || $pg == 'data_administrasi_umum_perorangan_ubah') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="main/index/data_administrasi_umum_perorangan">Umum (Profile)</a></li>
                      <li <?php if ($pg == 'data_administrasi_ijin_usaha' || $pg == 'data_administrasi_ijin_usaha_ubah') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="main/index/data_administrasi_ijin_usaha">NIB</a></li>
                      <li <?php if ($pg == 'data_administrasi_umum_cv') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="main/index/data_administrasi_umum_cv">CV</a></li>
                    <?php
                    } else
                    { ?>
                      <li <?php if ($pg == 'data_administrasi_umum' || $pg == 'data_administrasi_umum_ubah_profile') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="main/index/data_administrasi_umum">Profil Perusahaan</a></li>
                      <li <?php if ($pg == 'data_administrasi_ijin_usaha' || $pg == 'data_administrasi_ijin_usaha_ubah') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="main/index/data_administrasi_ijin_usaha">N I B</a></li>
                      <li <?php if ($pg == 'data_administrasi_landasan_hukum' || $pg == 'data_administrasi_landasan_hukum_ubah') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="main/index/data_administrasi_landasan_hukum">Akta Pendirian</a></li>
                      <li <?php if ($pg == 'data_administrasi_pengurus_perusahaan' || $pg == 'data_administrasi_pengurus_perusahaan_tambah') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="main/index/data_administrasi_pengurus_perusahaan">Pengurus Perusahaan</a></li>
                      <li <?php if ($pg == 'data_administrasi_kepemilikan_saham' || $pg == 'data_administrasi_kepemilikan_saham_tambah') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="main/index/data_administrasi_kepemilikan_saham">Kepemilikan Saham</a></li>
                      <li <?php if ($pg == 'data_administrasi_sbu' ||$pg == 'data_administrasi_sbu_ubah') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="main/index/data_administrasi_sbu">Sertifikat Badan Usaha Konstruksi</a></li>
                    <?php
                    } ?>

                  </ul>
                </li>

                <?php
                if ($this->REKANAN_TIPE_ID == '7') { // Perorangan
                } else {?>
                <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown">Data Keuangan</a>
                  <ul class="dropdown-menu">
                    <li <?php if ($pg == 'data_keuangan_rekening_koran' || $pg == 'data_keuangan_rekening_koran_tambah') { echo 'class="active"'; } ?>>
                      <a class="dropdown-item" href="main/index/data_keuangan_rekening_koran">Rekening Koran</a></li>
                    <li <?php if ($pg == 'data_perpajakan_neraca' || $pg == 'data_perpajakan_neraca_tambah') { echo 'class="active"'; } ?>>
                      <a class="dropdown-item" href="main/index/data_perpajakan_neraca">Neraca</a></li>
                  </ul>
                </li>
                <?php
                } ?>

                <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown">Data Perpajakan</a>
                  <ul class="dropdown-menu">
                    <?php
                    if ($this->REKANAN_TIPE_ID == '7') { // Perorangan?>
                      <li <?php if ($pg == 'data_perpajakan_spt_tahunan') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="main/index/data_perpajakan_spt_tahunan">SPT Tahunan</a></li>
                    <?php
                    } else
                    { ?>
                      <li <?php if ($pg == 'data_perpajakan_pkp' || $pg == 'data_perpajakan_pkp_ubah') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="main/index/data_perpajakan_pkp">PKP / Non PKP</a></li>
                      <li <?php if ($pg == 'data_perpajakan_spt_tahunan' || $pg == 'data_perpajakan_spt_tahunan_ubah') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="main/index/data_perpajakan_spt_tahunan">SPT Tahunan</a></li>
                      <li <?php if ($pg == 'data_perpajakan_pajak_bulanan' || $pg == 'data_perpajakan_pajak_bulanan_tambah') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="main/index/data_perpajakan_pajak_bulanan">Laporan Pajak Bulanan (PPN)</a></li>
                    <?php
                    } ?>
                  </ul>
                </li>

                <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown">Data Teknis</a>
                  <ul class="dropdown-menu">
                    <?php
                    if ($this->REKANAN_TIPE_ID == '7') { // Perorangan?>
                      <li <?php if ($pg == 'data_teknis_pengalaman' || $pg == 'data_teknis_pengalaman_selesai_tambah' || $pg == 'data_teknis_pengalaman_progress_tambah') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="main/index/data_teknis_pengalaman">Pengalaman Keahlian</a></li>
                      <li <?php if ($pg == 'data_teknis_sertifikat_lain' || $pg == 'data_teknis_sertifikat_lain_tambah') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="main/index/data_teknis_sertifikat_lain">Sertifikat Keahlian</a></li>
                    <?php
                    } else
                    { ?>
                      <li <?php if ($pg == 'data_teknis_tenaga_ahli' || $pg == 'data_teknis_tenaga_ahli_tambah') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="main/index/data_teknis_tenaga_ahli">Tenaga Ahli Tetap</a></li>
                      <li <?php if ($pg == 'data_teknis_pengalaman' || $pg == 'data_teknis_pengalaman_selesai_tambah' || $pg == 'data_teknis_pengalaman_progress_tambah') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="main/index/data_teknis_pengalaman">Pengalaman</a></li>
                      <li <?php if ($pg == 'data_teknis_peralatan' || $pg == 'data_teknis_peralatan_tambah') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="main/index/data_teknis_peralatan">Peralatan</a></li>
                      <li <?php if ($pg == 'data_teknis_sertifikat_lain' || $pg == 'data_teknis_sertifikat_lain_tambah') { echo 'class="active"'; } ?>>
                        <a class="dropdown-item" href="main/index/data_teknis_sertifikat_lain">Dokumen Teknis Perusahaan</a></li>
                    <?php
                    } ?>
                  </ul>
                </li>
              </ul>
            </li>
          <?php
          } else
          { // belum diverifikasi
          ?>
            <li class="dropdown nav-item" data-menu="dropdown">
              <a class="dropdown-toggle nav-link <?php if (in_array($pg, $arrayDataAdministrasi)) { echo ' active'; } ?>" href="#" data-toggle="dropdown">
                Data Administrasi
              </a>
              <ul class="dropdown-menu">
                <?php
                if ($this->REKANAN_TIPE_ID == '7') { // Perorangan?>
                  <li <?php if ($pg == 'data_administrasi_umum_perorangan' || $pg == 'data_administrasi_umum_perorangan_ubah') { echo 'class="active"'; } ?>>
                    <a class="dropdown-item" href="main/index/data_administrasi_umum_perorangan">Umum (Profile)</a></li>
                  <li <?php if ($pg == 'data_administrasi_ijin_usaha' || $pg == 'data_administrasi_ijin_usaha_ubah') { echo 'class="active"'; } ?>>
                    <a class="dropdown-item" href="main/index/data_administrasi_ijin_usaha">NIB</a></li>
                  <li <?php if ($pg == 'data_administrasi_umum_cv') { echo 'class="active"'; } ?>>
                    <a class="dropdown-item" href="main/index/data_administrasi_umum_cv">CV</a></li>
                <?php
                } else
                { ?>
                  <li <?php if ($pg == 'data_administrasi_umum' || $pg == 'data_administrasi_umum_ubah_profile') { echo 'class="active"'; } ?>>
                    <a class="dropdown-item" href="main/index/data_administrasi_umum">Profil Perusahaan</a></li>
                  <li <?php if ($pg == 'data_administrasi_ijin_usaha' || $pg == 'data_administrasi_ijin_usaha_ubah') { echo 'class="active"'; } ?>>
                    <a class="dropdown-item" href="main/index/data_administrasi_ijin_usaha">N I B</a></li>
                  <li <?php if ($pg == 'data_administrasi_landasan_hukum' || $pg == 'data_administrasi_landasan_hukum_ubah') { echo 'class="active"'; } ?>>
                    <a class="dropdown-item" href="main/index/data_administrasi_landasan_hukum">Akta Pendirian</a></li>
                  <li <?php if ($pg == 'data_administrasi_pengurus_perusahaan' || $pg == 'data_administrasi_pengurus_perusahaan_tambah') { echo 'class="active"'; } ?>>
                    <a class="dropdown-item" href="main/index/data_administrasi_pengurus_perusahaan">Pengurus Perusahaan</a></li>
                  <li <?php if ($pg == 'data_administrasi_kepemilikan_saham' || $pg == 'data_administrasi_kepemilikan_saham_tambah') { echo 'class="active"'; } ?>>
                    <a class="dropdown-item" href="main/index/data_administrasi_kepemilikan_saham">Kepemilikan Saham</a></li>
                  <li <?php if ($pg == 'data_administrasi_sbu' ||$pg == 'data_administrasi_sbu_ubah') { echo 'class="active"'; } ?>>
                    <a class="dropdown-item" href="main/index/data_administrasi_sbu">Sertifikat Badan Usaha Konstruksi</a></li>
                <?php
                } ?>
              </ul>
            </li>

            <?php
            if ($this->REKANAN_TIPE_ID == '7') { // Perorangan
            } else {?>
            <li class="dropdown nav-item" data-menu="dropdown">
              <a class="dropdown-toggle nav-link <?php if (in_array($pg, $arrayDataKeuangan)) { echo ' active'; } ?>" href="#" data-toggle="dropdown">
                Data Keuangan
              </a>
              <ul class="dropdown-menu">
                <li <?php if ($pg == 'data_keuangan_rekening_koran' || $pg == 'data_keuangan_rekening_koran_tambah') { echo 'class="active"'; } ?>>
                  <a class="dropdown-item" href="main/index/data_keuangan_rekening_koran">Rekening Koran</a></li>
                <li <?php if ($pg == 'data_perpajakan_neraca' || $pg == 'data_perpajakan_neraca_tambah') { echo 'class="active"'; } ?>>
                  <a class="dropdown-item" href="main/index/data_perpajakan_neraca">Neraca</a></li>
              </ul>
            </li>
            <?php
            } ?>

            <li class="dropdown nav-item" data-menu="dropdown">
              <a class="dropdown-toggle nav-link <?php if (in_array($pg, $arrayDataPerpajakan)) { echo ' active'; } ?>" href="#" data-toggle="dropdown">
                Data Perpajakan
              </a>
              <ul class="dropdown-menu">
              <?php
              if ($this->REKANAN_TIPE_ID == '7') { // Perorangan?>
                <li <?php if ($pg == 'data_perpajakan_spt_tahunan') { echo 'class="active"'; } ?>>
                  <a class="dropdown-item" href="main/index/data_perpajakan_spt_tahunan">SPT Tahunan</a></li>
              <?php
              } else
              { ?>
                <li <?php if ($pg == 'data_perpajakan_pkp' || $pg == 'data_perpajakan_pkp_ubah') { echo 'class="active"'; } ?>>
                  <a class="dropdown-item" href="main/index/data_perpajakan_pkp">PKP / Non PKP</a></li>
                <li <?php if ($pg == 'data_perpajakan_spt_tahunan' || $pg == 'data_perpajakan_spt_tahunan_ubah') { echo 'class="active"'; } ?>>
                  <a class="dropdown-item" href="main/index/data_perpajakan_spt_tahunan">SPT Tahunan</a></li>
                <li <?php if ($pg == 'data_perpajakan_pajak_bulanan' || $pg == 'data_perpajakan_pajak_bulanan_tambah') { echo 'class="active"'; } ?>>
                  <a class="dropdown-item" href="main/index/data_perpajakan_pajak_bulanan">Laporan Pajak Bulanan (PPN)</a></li>
              <?php
              } ?>
              </ul>
            </li>

            <li class="dropdown nav-item" data-menu="dropdown">
              <a class="dropdown-toggle nav-link <?php if (in_array($pg, $arrayDataTeknis)) { echo ' active'; } ?>" href="#" data-toggle="dropdown">
                Data Teknis
              </a>
              <ul class="dropdown-menu">
              <?php
              if ($this->REKANAN_TIPE_ID == '7') { // Perorangan?>
                <li <?php if ($pg == 'data_teknis_pengalaman' || $pg == 'data_teknis_pengalaman_selesai_tambah' || $pg == 'data_teknis_pengalaman_progress_tambah') { echo 'class="active"'; } ?>>
                  <a class="dropdown-item" href="main/index/data_teknis_pengalaman">Pengalaman Keahlian</a></li>
                <li <?php if ($pg == 'data_teknis_sertifikat_lain' || $pg == 'data_teknis_sertifikat_lain_tambah') { echo 'class="active"'; } ?>>
                  <a class="dropdown-item" href="main/index/data_teknis_sertifikat_lain">Sertifikat Keahlian</a></li>
              <?php
              } else
              { ?>
                <li <?php if ($pg == 'data_teknis_tenaga_ahli' || $pg == 'data_teknis_tenaga_ahli_tambah') { echo 'class="active"'; } ?>>
                  <a class="dropdown-item" href="main/index/data_teknis_tenaga_ahli">Tenaga Ahli Tetap</a></li>
                <li <?php if ($pg == 'data_teknis_pengalaman' || $pg == 'data_teknis_pengalaman_selesai_tambah' || $pg == 'data_teknis_pengalaman_progress_tambah') { echo 'class="active"'; } ?>>
                  <a class="dropdown-item" href="main/index/data_teknis_pengalaman">Pengalaman</a></li>
                <li <?php if ($pg == 'data_teknis_peralatan' || $pg == 'data_teknis_peralatan_tambah') { echo 'class="active"'; } ?>>
                  <a class="dropdown-item" href="main/index/data_teknis_peralatan">Peralatan</a></li>
                <li <?php if ($pg == 'data_teknis_sertifikat_lain' || $pg == 'data_teknis_sertifikat_lain_tambah') { echo 'class="active"'; } ?>>
                  <a class="dropdown-item" href="main/index/data_teknis_sertifikat_lain">Dokumen Teknis Perusahaan</a></li>
              <?php
              } ?>
              </ul>
            </li>

          <?php
          } // End belum diverifikasi?>

            <?php
            // echo $adaKelengkapanData;
            if($user_status == 1 && $this->REKANAN_TIPE_ID != '7') // 7: Konsultan Perorangan
            { // Bukan Perorangan
              $arrayEtalase = array('katalog_rekanan','katalog_foto','katalog_lampiran','katalog_rekanan_add','katalog_penawaran','katalog_pernyataan');
            ?>
            <li class="nav-item">
              <a href="main/index/katalog_rekanan" class="nav-link <?php if (in_array($pg, $arrayEtalase)) { echo ' active'; } ?>">
                Etalase</a>
            </li>
            <?php
            $arrayeInboxPenyedia = array('inbox_rfi_penyedia','inbox_rfi_penyedia_add','inbox_survei_penyedia','inbox_survei_penyedia_add');
            ?>
            <li class="dropdown nav-item" data-menu="dropdown">
              <a href="#" class="dropdown-toggle nav-link <?php if (in_array($pg, $arrayeInboxPenyedia)) { echo ' active'; } ?>" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> Inbox </a>
              <ul class="dropdown-menu">
                <li <?php if ($pg == 'inbox_rfi_penyedia' || $pg == 'inbox_rfi_penyedia_add') { echo 'class="active"'; } ?>>
                  <a class="dropdown-item" href="main/index/inbox_rfi_penyedia">RFI/Market Sounding</a></li>
                <li <?php if ($pg == 'inbox_survei_penyedia' || $pg == 'inbox_survei_penyedia_add') { echo 'class="active"'; } ?>>
                  <a class="dropdown-item" href="main/index/inbox_survei_penyedia">Survey</a></li> 
              </ul>
            </li>
            <li class="nav-item">
              <a href="main/index/inbox_complain_penyedia" class="nav-link <?php if ($pg == 'inbox_complain_penyedia' || $pg == 'inbox_complain_penyedia_add') { echo ' active'; } ?>">
                Bantuan</a>
            </li>

            <?php
            }
            if($user_status == 1) // 7: Konsultan Perorangan
            {
              $arrContractPenyedia = array('contracting_penyedia_detail','contracting_penyedia','contracting_penyedia_sppbj','contracting_penyedia_perjanjian','contracting_penyedia_realisasi','contracting_penyedia_termin','contracting_penyedia_dokumen','contracting_penyedia_perubahan','contracting_penyedia_harga','contracting_penyedia_kahar','contracting_penyedia_berakhir','contracting_penyedia_pemutusan','contracting_penyedia_kesempatan','contracting_penyedia_denda');
            ?>
            <li class="nav-item">
              <a href="kontrak/index/contracting_penyedia" class="nav-link <?php if (in_array($pg, $arrContractPenyedia)) { echo ' active'; } ?>">
                Kontrak</a>
            </li>
            <?php
            }


            if($user_status == 1)
            { ?>

            <li class="nav-item">
              <a href="main/index/dokumen_template_rekanan" class="nav-link <?php if (in_array($pg, array('dokumen_template_rekanan'))) { echo ' active'; } ?>">
                Template Dokumen</a>
            </li>
            <?php
            }

            $rekanan = new Rekanan();
            $rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
            $rekanan->firstRow();
            $reqStatusValidasi= $rekanan->getField("STATUS_VALIDASI");
                  $this->load->model("UserLogin");

            $userRekanan = new Userlogin();
            $userRekanan->selectByParams(array("REKANAN_ID"=>$this->ID),-1,-1);
            $userRekanan->firstRow();
            $reqStatusUser= $userRekanan->getField("USER_STATUS");

            // if($user_status == 0) {
            // echo $reqStatusUser.'--'.$reqStatusValidasi;
            if($reqStatusValidasi == 0 || $reqStatusValidasi == 10 || $reqStatusValidasi == 4) {
            ?>
            <li class="nav-item">
              <a href="main/index/konfirmasi_pendaftaran" class="nav-link <?php if ($pg == 'konfirmasi_pendaftaran') { echo ' active'; } ?>">
                Konfirmasi</a>
            </li> 
            <?php
            }

            if($reqStatusValidasi == 0 || $reqStatusValidasi == 10 || $reqStatusValidasi == 4 || ($reqStatusUser == '0' && $reqStatusValidasi == '1')) {
            ?> 
            <li class="nav-item">
              <a href="main/index/inbox_complain_penyedia" class="nav-link <?php if ($pg == 'inbox_complain_penyedia') { echo ' active'; } ?>">
                Bantuan</a>
            </li>
            <?php
            }
            // } ?>

          <?php
            // }
          }

          if($this->USER_TYPE_ID == "1") // ADMINISTRATOR
          {
          ?>
          <!-- <li class="nav-item"><a class="nav-link <?php //if ($pg == 'daftar_rekanan_belum_valid' || $pg == 'daftar_rekanan_valid') { echo ' active'; } ?>" href="main/index/daftar_rekanan_belum_valid">Daftar <?php // LABEL_PENYEDIA ?></a> </li>-->
          <li class="dropdown nav-item" data-menu="dropdown">
            <a href="#" class="dropdown-toggle nav-link <?php
            $arrayMasterMenuAdmin1 = array('master_daftar_user_non_rekanan','master_user_rekanan','master_group','master_menu');
            if(in_array($pg, $arrayMasterMenuAdmin1)) { echo ' active'; } ?>"
            data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Management User</a>
            <ul class="dropdown-menu">
              <li <?php if ($pg == 'master_daftar_user_non_rekanan') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/master_daftar_user_non_rekanan">User eProc</a>
              </li>
              <li <?php if ($pg == 'master_user_rekanan') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/master_user_rekanan">User Penyedia</a>
              </li>
              <li <?php if ($pg == 'master_group') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/master_group">Group</a>
              </li>
              <li <?php if ($pg == 'master_menu') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/master_menu">Group & Role</a>
              </li>
            </ul>
          </li>
          <li class="dropdown nav-item" data-menu="dropdown">
            <a href="#" class="dropdown-toggle nav-link <?php
            $arrayMasterMenuAdmin = array('master_bidang_usaha','master_rekanan_tipe','master_sk_panitia','master_kontak','master_unit_kerja','master_payment_method','master_bank','master_banner','backup','master_tanggal_merah','master_dokumen_template','master_dokumen_template2','master_dokumen_template_rekanan','master_sertifikat_jenis','master_metode','master_vendor_retail');
            if(in_array($pg, $arrayMasterMenuAdmin)) { echo ' active'; } ?>"
            data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Master Data</a>
            <ul class="dropdown-menu">
              <li <?php if ($pg == 'master_vendor_retail') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/master_vendor_retail">Vendor Retail</a>
              </li>
              <li <?php if ($pg == 'master_bidang_usaha') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/master_bidang_usaha">Master Bidang Usaha</a>
              </li>
              <li <?php if ($pg == 'master_sk_panitia' || $pg == 'master_sk_panitia_lampiran') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/master_sk_panitia">SK Panitia</a>
              </li>
              <li <?php if ($pg == 'master_kontak') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/master_kontak">Kritik dan Saran</a>
              </li>
              <li <?php if ($pg == 'master_unit_kerja') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/master_unit_kerja">Perusahaan</a>
              </li>
              <!-- <li <?php // if ($pg == 'master_payment_method') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/master_payment_method">Payment Method</a>
              </li> -->
              <li <?php if ($pg == 'master_bank') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/master_bank">Bank</a>
              </li>
              <li <?php if ($pg == 'master_banner') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/master_banner">Banner</a>
              </li>
              <li <?php if ($pg == 'master_bentuk_usaha') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/master_rekanan_tipe">Bentuk Usaha</a>
              </li>
              <li <?php if ($pg == 'master_tanggal_merah') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/master_tanggal_merah">Tanggal Merah</a>
              </li>
              <li <?php if ($pg == 'master_dokumen_template') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/master_dokumen_template">Template Dokumen</a>
              </li>
              <li <?php if ($pg == 'master_dokumen_template2') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/master_dokumen_template2">Bank Template Dokumen</a>
              </li>
              <li <?php if ($pg == 'master_dokumen_template_rekanan') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/master_dokumen_template_rekanan">Bank Template Penyedia</a>
              </li>
              <li <?php if ($pg == 'master_sertifikat_jenis') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/master_sertifikat_jenis">Jenis Sertifikat</a>
              </li>
              <li <?php if ($pg == 'master_metode') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/master_metode">Matrix Metode Pengadaan</a>
              </li>
              <!-- <li <?php if ($pg == 'dbbackup') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/backup">Backup Database</a>
              </li> -->
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php if ($pg == 'vendor_oracle') { echo ' active'; } ?>" href="main/index/vendor_oracle">Sinkron Vendor </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php if ($pg == 'master_berita') { echo ' active'; } ?>" href="main/index/master_berita">Berita & Pengumuman</a>
          </li>
          <!-- <li class="nav-item">
            <a class="nav-link <?php if ($pg == 'master_backup') { echo ' active'; } ?>" href="main/index/master_backup">Backup</a>
          </li> -->

          <!-- <li class="nav-item">
            <a class="nav-link <?php // if ($pg == 'master_blacklist') { echo ' active'; } ?>" href="main/index/master_blacklist">Blacklist</a>
          </li> -->

          <li class="dropdown nav-item" data-menu="dropdown">
            <a href="#" class="dropdown-toggle nav-link <?php if ($pg == 'logs_file' || $pg == 'logs_login' || $pg == 'logs_multirole') { echo ' active'; } ?>" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Logs </a>
            <ul class="dropdown-menu">
              <li <?php if ($pg == 'logs_file') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/logs_file">Aktifitas</a></li>
              <li <?php if ($pg == 'logs_login') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/logs_login">Login</a></li>
              <li <?php if ($pg == 'logs_multirole') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/logs_multirole">Multi Role</a></li>
            </ul>
          </li>

          <li class="nav-item">
            <a class="nav-link <?php if ($pg == 'master_pengaturan' || $pg == 'master_pengaturan_dok_expired') { echo ' active'; } ?>" href="main/index/master_pengaturan">Pengaturan</a>
          </li>

          <li class="nav-item">
            <a href="kontrak/index/blacklist_kontrak" class="nav-link <?php if ($pg == 'blacklist_kontrak') { echo 'active'; } ?>">
                Daftar Hitam Kontrak
            </a>
          </li>

          <!-- <li class="dropdown nav-item">
            <a href="#" class="dropdown-toggle nav-link <?php // if ($pg == 'katalog_kategori' || $pg == 'katalog_rekanan' || $pg == 'katalog_validasi') { echo ' active'; } ?>" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Data Katalog </a>
            <ul class="dropdown-menu">
              <li <?php // if ($pg == 'katalog_kategori') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/katalog_kategori">Kategori</a></li>
              <li <?php// if ($pg == 'katalog_rekanan' || $pg == 'katalog_validasi' || $pg == 'katalog_validasi_rekanan' || $pg == 'katalog_laporan') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/katalog_rekanan">Katalog</a></li>
            </ul>
          </li>  -->

          <?php
          }
          if($this->USER_TYPE_ID == "4") // ADMINISTRATOR APRROVAL
          {
          ?>
          <li class="nav-item">
            <a class="nav-link <?php if ($pg == 'master_daftar_user_non_rekanan_approve') { echo ' active'; } ?>" href="main/index/master_daftar_user_non_rekanan_approve">User eProc</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php if ($pg == 'master_menu_approve') { echo ' active'; } ?>" href="main/index/master_menu_approve">Group & Role</a>
          </li>
          <?php
          }

          if($this->USER_TYPE_ID == "2") // VALIDATOR
          {
          ?>
          <!-- <li class="nav-item">
            <a class="nav-link <?php // if ($pg == 'validasi' || $pg == 'validasi_rekanan') { echo ' active'; } ?>" href="main/index/validasi">Validasi Penyedia</a>
          </li> -->

          <li class="dropdown nav-item" data-menu="dropdown">
            <a href="#" class="dropdown-toggle nav-link <?php if ($pg == 'daftar_rekanan_belum_valid' || $pg == 'daftar_rekanan_valid' || $pg == 'daftar_rekanan_akses' || $pg == 'daftar_rekanan_delete' || $pg == 'daftar_rekanan_potensi') { echo ' active'; } ?>" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Manajemen <?= LABEL_PENYEDIA ?> </a>
            <ul class="dropdown-menu">
              <li <?php if ($pg == 'daftar_rekanan_belum_valid') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/daftar_rekanan_belum_valid">Daftar <?= LABEL_PENYEDIA ?></a>
              </li>
              <!-- <li <?php // if ($pg == 'daftar_rekanan_valid' || $pg == 'daftar_rekanan_akses') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/daftar_rekanan_valid">Terverifikasi</a>
              </li> -->
              <li <?php if ($pg == 'daftar_rekanan_potensi') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/daftar_rekanan_potensi">Pencarian Potensi</a>
              </li>
              <li <?php if ($pg == 'daftar_rekanan_delete') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/daftar_rekanan_delete">Hapus</a>
              </li>
            </ul>
          </li>

          <li class="dropdown nav-item" data-menu="dropdown">
            <a href="#" class="dropdown-toggle nav-link <?php if ($pg == 'katalog_kategori' || $pg == 'katalog_rekanan' || $pg == 'katalog_validasi' || $pg == 'katalog_laporan') { echo ' active'; } ?>" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Manajemen Katalog </a>
            <ul class="dropdown-menu">
              <li <?php if ($pg == 'katalog_kategori') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/katalog_kategori">Kategori</a></li>
              <li <?php if ($pg == 'katalog_rekanan' || $pg == 'katalog_validasi' || $pg == 'katalog_laporan') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/katalog_rekanan">Katalog</a></li>
            </ul>
          </li>
          <?php
          $arrayeInbox = array('inbox_survei','inbox_survei_add','inbox_complain','inbox_complain_add'); ?>
          <li class="dropdown nav-item" data-menu="dropdown">
            <a href="#" class="dropdown-toggle nav-link <?php if (in_array($pg, $arrayeInbox)) { echo ' active'; } ?>" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              Inbox </a>
            <ul class="dropdown-menu">
              <li <?php if ($pg == 'inbox_survei' || $pg == 'inbox_survei_add') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/inbox_survei">Survey</a></li>
              <li <?php if ($pg == 'inbox_complain' || $pg == 'inbox_complain_add') { echo 'class="active"'; } ?>>
                <a class="dropdown-item" href="main/index/inbox_complain">Pertanyaan/Pengajuan</a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php if ($pg == 'master_blacklist') { echo ' active'; } ?>" href="main/index/master_blacklist">Blacklist</a>
          </li>
          <!-- <li class="nav-item">
            <a class="nav-link <?php if ($pg == 'master_user_rekanan') { echo ' active'; } ?>" href="main/index/master_user_rekanan">Daftar Rekanan Ubah Status</a>
          </li>  -->
          <?php
          }

          if($this->USER_TYPE_ID == "3" || $this->USER_TYPE_ID == "9" || $this->USER_TYPE_ID == "12" )
          { ?>
          <li class="nav-item">
            <a class="nav-link <?php if ($pg == 'dokumen_template') { echo ' active'; } ?>" href="main/index/dokumen_template">
              Template Dokumen
            </a>
          </li> 
          <?php
          }

          if($this->USER_TYPE_ID == 11) // PEJABAT PEMBELI 
          {
            $arrayePlanning = array('permohonan_penunjukan_pic');
            ?>

            <li class="nav-item">
              <a href="main/index/daftar_rekanan_potensi" class="nav-link <?php if ($pg == 'daftar_rekanan_potensi') { echo ' active'; } ?>">
                Pencarian Potensi
              </a>
            </li> 
            <?php 
          }
          
          // if($this->USER_TYPE_ID != "" && $this->USER_TYPE_ID != "6" && $this->USER_TYPE_ID != "10" && $this->USER_TYPE_ID != "25") // REKANAN
          if($this->USER_TYPE_ID != "1" && $this->USER_TYPE_ID != "4" && $this->USER_TYPE_ID != "6" && $this->USER_TYPE_ID != "10" && $this->USER_TYPE_ID != "12" && $this->USER_TYPE_ID != "20")
          { ?>

            <li class="dropdown nav-item" data-menu="dropdown">
              <a href="#" class="dropdown-toggle nav-link <?php if (in_array($pg, array('dokumen_template_rekanan'))) { echo ' active'; } ?>" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="icon-options-vertical"></i></a>
              <ul class="dropdown-menu">
                <?php 
                if($this->USER_TYPE_ID == "11")
                { ?>
                <li>
                  <a class="dropdown-item <?php if ($pg == 'dokumen_template') { echo ' active'; } ?>" href="main/index/dokumen_template">
                    Template Dokumen
                  </a>
                </li> 
                <?php
                }
                if($this->USER_TYPE_ID == "3" || $this->USER_TYPE_ID == "11" || $this->USER_TYPE_ID == "2" )
                { ?> 
                <li>
                  <a href="kontrak/index/blacklist_kontrak" class="dropdown-item <?php if ($pg == 'blacklist_kontrak') { echo 'active'; } ?>">
                      Blacklist Kontrak
                  </a>
                </li>
                <?php
                } ?>
                <li class="<?php if ($pg == 'dokumen_template_rekanan') { echo 'active'; } ?>">
                  <a href="main/index/katalog" class="dropdown-item <?php if ($pg == 'katalog' || $pg == 'katalog_detail') { echo ' active'; } ?>">
                   Katalog</a>
                </li>  
              </ul>
            </li> 

          <?php
          }
          ?>

          <?php
          if($this->USER_LOGIN_ID == "")
          {
          ?>
           <li class="nav-item">
              <a href="main/index/registrasi" class="nav-link <?php if ($pg == 'registrasi') { echo'active'; } ?>">
                Registrasi</a>
           </li>
           <li class="nav-item">
            <a href="#" class="nav-link" data-toggle="modal" data-target="#iconForm">
              Login </a>
           </li>
          <?php
          } else { ?>
          <li class="dropdown nav-item" data-menu="dropdown">
            <a href="#" class="dropdown-toggle nav-link <?php if ($pg == 'ubah_password' || $pg == 'pakta_integritas') { echo ' active'; } ?>" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user-circle-o"></i> <span class="icon-arrow-down"></span></a>
            <ul class="dropdown-menu dropdown-menu-right">
              <li>
                <a href="main/index/ubah_password" class="logout dropdown-item">Ubah Password</a>
              </li>
              <li>
              <?php
              if($this->USER_TYPE_ID == 6) // REKANAN
              {
                if ($adaKelengkapanData == '0') {
                  echo '<a id="btnSertifikat" target="_blank" href="main/loadUrl/report/vms_pdf_penyedia/" onclick="if(confirm(\'Cetak Surat Keterangan Terdaftar (SKT) ?\')) { return true; } else { return false; }" class="logout dropdown-item">Cetak Surat Keterangan Terdaftar</a>';
                }
              }
              ?>

              <!-- <li> -->
              <?php
              // $this->load->model("Dokumenrekanan");
              // $dokumen_rekanan = new Dokumenrekanan();
              // $dokumen_rekanan->selectByParams(array('REKANAN_ID' => $this->ID));
              //   if ($dokumen_rekanan->countRow() > 0) {
              //     $dokumen_rekanan->firstRow();
              //     echo '<a href="uploads/pakta_integritas/'.$dokumen_rekanan->getField('PATH_FILE').'" target="_blank" class="dropdown-item">Pakta Integritas</a>';
              //   } else { }
              ?>
              <!-- </li> -->
              <!-- <li>
                <a href="login/manualbook" target="_blank" class="logout dropdown-item">Manual Book</a>
              </li> -->
              <li>
                <a href="login/logout" class="logout dropdown-item">Logout</a>
              </li>
            </ul>
          </li>
          <?php
          } ?>

        </ul>
      </div>
    </div>
    <!-- End Navigation -->


    <div class="app-content content">
      <?php
      if ($pg == 'home') {
      $this->load->model("Banner");
      $banner = new Banner();
      $bannerCount = new Banner();
      $arrStatement = array();
      $banner->selectByParams($arrStatement);
      $bannerCount->getCountByParams($arrStatement);
      ?>

      <section id="basic-carousel">
        <div class="row">
          <div class="col-md-12 col-sm-12">
            <div class="card">
              <div class="card-content">
                <div class="card-body" style="padding: 0 !important">


                  <div class="center-slider mx-auto form-group hidden-xs mt-1">
                    <div id="automatic-slider" style="height:420px;">
                      <ul>
                        <?php
                        while ($banner->nextRow()) { ?>
                          <li><img src="<?= base_url('uploads/banner/'.$banner->getField("GAMBAR")) ?>" class="img-fluid"></li>
                        <?php
                        } ?>
                        <!-- <li><img src="<?= base_url('images/katalog/01.jpg') ?>" class="img-fluid"></li>
                        <li><img src="<?= base_url('images/katalog/02.jpg') ?>" class="img-fluid"></li> -->
                      </ul>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <?php
      } ?>
      <div class="content-wrapper">

        <?php
        if ($pg != 'home' && $pg != 'dashboardperencana' && $pg != 'dashboard') {
        ?>
        <div class="content-header row">
          <div class="content-header-left col-md-12 col-12 mb-2">
            <div class="row breadcrumbs-top">
              <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                  <?= $breadcrumb?>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <?php } ?>

        <!-- BEGIN VENDOR JS-->
        <script src="<?=base_url()?>assets/new/vendors/js/vendors.min.js"></script>
        <script src="<?=base_url()?>assets/new/vendors/js/extensions/unslider-min.js"></script>
        <script src="<?=base_url()?>assets/new/vendors/js/ui/jquery.sticky.js"></script>
        <script src="<?=base_url()?>assets/new/js/core/app-menu.js"></script>
        <script src="<?=base_url()?>assets/new/js/core/app.js"></script>
        <script src="<?=base_url()?>assets/new/vendors/js/extensions/toastr.min.js"></script>
        <script src="<?=base_url()?>assets/new/js/scripts/tooltip/tooltip.js"></script>
        <script src="<?=base_url()?>assets/new/vendors/scripts/extensions/unslider.js"></script>

        <!-- END PAGE LEVEL JS-->

        <div class="content-body"><!-- Basic Carousel start -->
          <?=($content ? $content:'')?>
          <?php
          if($this->USER_TYPE_ID == 6) // REKANAN
          {
            $rekanan = new Rekanan();
            $rekanan->selectByParams(array("A.REKANAN_ID"=>$this->ID),-1,-1);
            $rekanan->firstRow();
            $reqStatusValidasi= $rekanan->getField("STATUS_VALIDASI");
            switch ($user_status) {
              case '0': // table user_login, field user_status
                if ($reqStatusValidasi == '1') { // Revisi
                  echo '<div class="alert alert-danger" style="position:fixed; z-index:9999; bottom:1%; width: 98%; margin: 0 auto; left: 1%; text-align:center; font-weight:bold">.:: Akses sudah dibuka, segera lengkapi perubahan data ! ::.</div>';
                } else {
                  echo '<div class="alert alert-danger" style="position:fixed; z-index:9999; bottom:1%; width: 98%; margin: 0 auto; left: 1%; text-align:center; font-weight:bold">.:: Segera Lengkapi data! ::.</div>';
                }
                break;
             case '2': // table user_login, field user_status
                switch ($reqStatusValidasi) {  // table rekanan, field status_validasi
                  case '10': // Tolak
                    $notifStatus = 'alert-danger';
                    $statusValidasiVendor = 'Data dikembalikan, silahkan verifikasi ulang ke admin vendor management '.SYSTEM_NAME_PT.' <br>untuk klarifikasi dokumen '.SYSTEM_EMAIL_VMS.' '; break;
                  case '1': // Terveririkasi
                    $notifStatus = 'alert-danger';
                    $statusValidasiVendor = 'Akun sudah terverifikasi, jika tanda di pojok kanan atas masih menunjukan icon <i class="fa fa-close btn btn-danger" style="padding:3px 8px !important"></i> <br>Silahkan hubungi admin vendor management '.SYSTEM_NAME_PT.' melalui email '.SYSTEM_EMAIL_VMS.' '; break;
                  case '3': // Posisi di user REKOMENDASI VMS
                    $notifStatus = 'alert-primary';
                    $statusValidasiVendor = '<span class="fa fa-cogs"></span> Proses Rekomendasi'; break;
                  case '4': // Posisi di user APPROVAL VMS
                    $notifStatus = 'alert-info';
                    $statusValidasiVendor = '<span class="fa fa-gavel"></span> Data Sedang Proses Approval'; break;
                  default: //
                    $notifStatus = 'alert-success';
                    $statusValidasiVendor = 'Data sudah dikirim, segera lakukan konfirmasi ke admin vendor '.SYSTEM_NAME_PT.' <br> untuk Verifikasi pastikan tidak ada revisi, dimohon untuk melakukan konfirmasi kedatangan terlebih dahulu'; break;
                }

                echo '<div class="alert '.$notifStatus.'" style="position:fixed; z-index:9999; bottom:1%; width: 98%; margin: 0 auto; left: 1%; text-align:center; font-weight:bold">. : : '.$statusValidasiVendor.' : : .</div>';
                break;
             case '1':
                // echo '<div class="alert alert-success" style="position:fixed; z-index:9999; bottom:1%; width: 98%; margin: 0 auto; left: 1%; text-align:center; font-weight:bold">.:: Data sudah dikirim, segera konfirmasi ke verifikator/validator untuk verifikasi data! ::.</div>';
                break;
              default:
                break;
            }
            $cekBlacklist = new Users();
            $cekBlacklist->selectBlacklistByRekananId($this->ID);
            $cekBlacklist->firstRow();
            if ($cekBlacklist->getField("BLACKLIST_ID")) {
              echo '<div class="alert alert-black" style="position:fixed; z-index:9999; bottom:1%; width: 98%; margin: 0 auto; left: 1%; text-align:center;   font-weight:bold; color: #fff !important">.:: Akun anda masuk dalam daftar blacklist ::.</div>';
            }
          }
          ?>
        </div>
      </div>
    </div>

<?php
$ip         = _ip(); // Get the users IP using the function above
$time       = date( 'd-m-Y' ); // Get the current date, in the format of: 12-12-2006
$timestamp  = time();
$getStats   = $visitor->getOnline($time, $ip);
if($getStats == 0)
{
  $visitor->setField("IP", $ip);
  $visitor->setField("TANGGAL", $time);
  $visitor->setField("HITS", 1);
  $visitor->setField("STATUS", $timestamp);
  $visitor->insert();
}
// $hitsToday = $visitor->hitsToday($time);
// $totalHits = $visitor->totalHits();
// $diff = time() - 300;
// $countOnline = $visitor->countOnline($diff);
// End Visitor
?>
      <!-- Footer
      ================================================== -->

      <footer class="footer footer-static footer-light navbar-shadow">
        <div class="container">
          <div class="row">
            <div class="col-md-12 text-right fontfooter">
              &copy; <?= LABEL_COPY_RIGHT_YEAR.' | '.SYSTEM_NAME.' '.SYSTEM_NAME_PT.' | '.LABEL_COPY_RIGHT ?>
            </div>
          </div>
        </div>
      </footer>


    <script src="<?=base_url()?>assets/js/bootstrap-transition.js"></script>
    <script src="<?=base_url()?>assets/js/bootstrap-alert.js"></script>
    <script src="<?=base_url()?>assets/js/bootstrap-dropdown.js"></script>
    <script src="<?=base_url()?>assets/js/bootstrap-scrollspy.js"></script>
    <script src="<?=base_url()?>assets/js/bootstrap-tab.js"></script>
    <script src="<?=base_url()?>assets/js/bootstrap-popover.js"></script>
    <script src="<?=base_url()?>assets/js/bootstrap-button.js"></script>
    <script src="<?=base_url()?>assets/js/bootstrap-carousel.js"></script>
    <script src="<?=base_url()?>assets/js/bootstrap-typeahead.js"></script>
    <script src="<?=base_url()?>assets/js/bootstrap-affix.js"></script>
    <script src="<?=base_url()?>assets/js/holder/holder.js"></script>
    <script src="<?=base_url()?>assets/js/google-code-prettify/prettify.js"></script>
    <script src="<?=base_url()?>assets/js/application.js"></script>

    <!-- <script>window.jQuery || document.write('<script src="lib/bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script> -->
    <script src="<?=base_url()?>lib/bootstrap/assets/js/ie10-viewport-bug-workaround.js"></script>
    <script>
    function openMulti(a) {
      openAddFrame("main/loadUrl/main/user_login_multi/?reqId="+a);
    } 
    $(document).on('click','#showPass',function(){
      $('#reqPassword').attr("type","text");
      $(this).hide();
      $('#hidePass').show();
    });
    $(document).on('click','#hidePass',function(){
      $('#reqPassword').attr("type","password");
      $(this).hide();
      $('#showPass').show();
    });
    $(document).on('click','#showPassx',function(){
      $('#reqPasswordx').attr("type","text");
      $(this).hide();
      $('#hidePassx').show();
    });
    $(document).on('click','#hidePassx',function(){
      $('#reqPasswordx').attr("type","password");
      $(this).hide();
      $('#showPassx').show();
    });
    </script>

    <!-- SERVER TIME -->
    <?php
    $intervalJS = '+7';
   ?>
    <script type="text/javascript">

      function alertSuccess() {
        setTimeout(function() {
          toastr.success("Data Berhasil disimpan", "Sukses", {
              progressBar: !0
          })
        }, 6000);
      }

      function alertSuccess2(a) {
        toastr.success(a, "Sukses", {
          progressBar: !0
        })
      }

      function alertError() {
        toastr.error("Data Gagal disimpan, Ulangi kembali!", "Gagal", {
          progressBar: !0
        })
      }

      function alertError2(a) {
        toastr.error(a, "Gagal", {
          progressBar: !0
        })
      }

      function alertError3(a) {
        toastr.error(a, "Perhatian!", {
          progressBar: !0
        })
      }

      // New Time
      $(document).ready(function() {
        var intervalJS = '+7';
        var monthNames = [ "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember" ];
        var dayNames= ["Minggu, ","Senin, ","Selasa, ","Rabu, ","Kamis, ","jum'at, ","Sabtu, "]
        var newDate = new Date();
        newDate.setDate(newDate.getDate());
        $('#Date').html(dayNames[newDate.getDay()] + " " + newDate.getDate() + ' ' + monthNames[newDate.getMonth()] + ' ' + newDate.getFullYear());

        setInterval( function() {
          var offset= intervalJS;
          var seconds = new Date().getUTCSeconds();
          $("#sec").html(seconds);
        },1000);

        setInterval( function() {
          var offset= intervalJS;
          var minutes = new Date().getUTCMinutes();
          $("#min").html(minutes);
        },1000);

        setInterval( function() {
          var offset = intervalJS;
              d      = new Date();
              utc    = d.getTime() + (d.getTimezoneOffset() * 60000);
            hours  = new Date(utc + (3600000*offset)).getHours();

          $("#hours").html(hours);
        }, 1000);

        function calcTime(city, offset) {
          d = new Date();
          utc = d.getTime() + (d.getTimezoneOffset() * 60000);
          nd = new Date(utc + (3600000*offset));
          return "The local time in " + city + " is " + nd.toLocaleString();
        }
      });

    // <!-- UBAH BGCOLOR SUB HEADER SAAT SCROLL -->
    $(window).on("scroll", function() {
        if($(window).scrollTop() > 20) {
            $(".area-sub-header").addClass("active");
        } else {
           $(".area-sub-header").removeClass("active");
        }
    });

    function autocomplete(inp, arr) {
  var currentFocus;
  inp.addEventListener("input", function (e) {
    var a,
      b,
      i,
      val = this.value;
    closeAllLists();
    if (!val) {
      return false;
    }
    currentFocus = -1;
    a = document.createElement("DIV");
    a.setAttribute("id", this.id + "autocomplete-list");
    a.setAttribute("class", "autocomplete-items");
    this.parentNode.appendChild(a);
    for (i = 0; i < arr.length; i++) {
      var pos = arr[i].toUpperCase().indexOf(val.toUpperCase());
      if (pos > -1) {
        b = document.createElement("DIV");
        b.innerHTML = arr[i].substr(0, pos);
        b.innerHTML +=
          "<strong>" + arr[i].substr(pos, val.length) + "</strong>";
        b.innerHTML += arr[i].substr(pos + val.length);
        b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
        b.addEventListener("click", function (e) {
          inp.value = this.getElementsByTagName("input")[0].value;
          closeAllLists();
        });
        a.appendChild(b);
      }
    }
  });

  inp.addEventListener("keydown", function (e) {
    var x = document.getElementById(this.id + "autocomplete-list");
    if (x) x = x.getElementsByTagName("div");
    if (e.keyCode == 40) {
      currentFocus++;
      addActive(x);
    } else if (e.keyCode == 38) {
      currentFocus--;
      addActive(x);
    } else if (e.keyCode == 13) {
      e.preventDefault();
      if (currentFocus > -1) {
        if (x) x[currentFocus].click();
      }
    }
  });
  function addActive(x) {
    if (!x) return false;
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = x.length - 1;
    x[currentFocus].classList.add("autocomplete-active");
  }
  function removeActive(x) {
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
    }
  }
  function closeAllLists(elmnt) {
    var x = document.getElementsByClassName("autocomplete-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  document.addEventListener("click", function (e) {
    closeAllLists(e.target);
  });
}

<?php if ($pg == 'validasi') { ?>
autocomplete(document.getElementById("reqKodeSeachPenyedia"), countries);
<?php } ?>

(function($){
    var oldLoad = $.fn.load;
    $.fn.load = function(url, params, callback){
        if (typeof url !== 'string') {
            return this.on('load', url);
        }
        return oldLoad.apply(this, arguments);
    };
})(jQuery);

</script>

  <link rel="stylesheet" type="text/css" href="<?=base_url()?>lib/eproc/themes/default/easyui.css">
  <script type="text/javascript" src="<?=base_url()?>lib/eproc/jquery.easyui.min.js"></script>
  <script type="text/javascript" src="<?=base_url()?>lib/eproc/kalender-easyui.js"></script>
  <script type="text/javascript" src="<?=base_url()?>lib/eproc/allfunc.js"></script>
  <script type="text/javascript" src="<?=base_url()?>lib/eproc/eModal.min.js"></script>
  <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/forms/selects/select2.min.css">
  <script src="<?=base_url()?>assets/new/vendors/js/forms/select/select2.full.min.js"></script>
  <script src="<?=base_url()?>assets/new/vendors/scripts/forms/select/form-select2.js"></script>

  <script type="text/javascript">
    function openAdd(pageUrl) {
          eModal.iframe(pageUrl, '<?= SYSTEM_NAME.' '.SYSTEM_NAME_PT ?>')
      }
    function openAddFrame(url) {
      var options = {
            url: url,
            title:'<?= SYSTEM_NAME ?>',
            size: eModal.size.lg,
            subtitle: '<?= SYSTEM_NAME_PT ?>',
        };
        eModal.iframe(options)
      // eModal.ajax(url, 'Rekam Jejak')
    }
      function openAddLg(url) {
        var options = {
              url: url,
              title:'<?= SYSTEM_NAME ?>',
              size: eModal.size.lg,
              subtitle: '<?= SYSTEM_NAME_PT ?>',
          };
          eModal.ajax(options)
        // eModal.ajax(url, 'Rekam Jejak')
      }
    function closePopup() {
      eModal.close();
    }
    function closePopupReload() {
      eModal.close();
      location.reload();
    }
    </script>

    <script>
        function openPopupChat(url) {
            eModal.ajax(url, 'Negosiasi')
        }
        function openPopupChatSmall(url,title) {
            var options = {
              url: url,
              title:'<?= SYSTEM_NAME ?>',
              size: eModal.size.sm,
              subtitle: '<?= SYSTEM_NAME_PT ?>',
          };
          eModal.iframe(options)
        }
    </script>

    <script src="<?=base_url()?>lib/tinyMCE/tinymce.min.js"></script>
    <script type="text/javascript">
    tinymce.init({
        selector: "textarea.textarea-tinymce",
        plugins: "image",
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
        menubar: false,

    });
    </script>

    <script src="<?=base_url()?>jslib/elementDis.js"></script>
    <script type="text/javascript">

    function setEvaluasiPenawaran(ctrl, ctrl_change)
    {
      if (ctrl.checked == true) {
        document.getElementById(ctrl_change).value = 1;
      } else {
        document.getElementById(ctrl_change).value = 0;
      }
    }

    function insertHTML() {
      var html = prompt("Enter some HTML code here");
      if (html) {
      editor.insertHTML(html);
      }
    }
    function highlight() {
      editor.surroundHTML('<span style="background-color: yellow">', '</span>');
    }

    function disposeall() {
      try {
      hideElement('filerekanan1');hideElement('filerekanan2');hideElement('filerekanan3');hideElement('filerekanan4');hideElement('filerekanan5');hideElement('filerekanan6');hideElement('filerekanan7');hideElement('filerekanan8');hideElement('filerekanan9');hideElement('filerekanan10');hideElement('filerekanan11');hideElement('filerekanan12');hideElement('filerekanan13');hideElement('filerekanan14');
      }
      catch(e) {}
    }

    function selectAllOption()
    {
      try
      {
        for (var i = 0; i < document.getElementById("file_list").options.length; i++)
        {
          document.getElementById("file_list").options[i].selected = true;
        }
      }
      catch(e)
      {
        alert(e);
      }

      return true;
    }

    function add_file(s) {
      try
      {
        var selvalue = s.value;
        var box = document.getElementById('file_list');
        var num = box.length;
        var file_exists = 0;

        var explode = selvalue.split('*');
        var varKlasifikasiId = explode[0];
        var varKlasifikasiKode = explode[1];

        for (x = 0; x < num; x++) {
          //alert(box.options[x].text);
          if (box.options[x].text == varKlasifikasiKode) {
            alert('Data sudah dipilih sebelumnya.');
            document.getElementById('file_' + x).value = "";
            file_exists = 1;
            break;
          }
        }

        box.options[num] = new Option(varKlasifikasiKode, varKlasifikasiId);
      }
      catch(e)
      {
        //alert(e);
      }

    }

    function remove_file() {
      var box = document.getElementById('file_list');

      if (box.selectedIndex != -1) {
        var value = box.options[box.selectedIndex].value;
        var child = document.getElementById(value);

        box.options[box.selectedIndex] = null;
        document.getElementById('files_div').removeChild(child);

        if (box.length == 0) {
          document.getElementById('list_div').style.display = 'none';
        }
      }
      else {
        alert('Silahkan pilih data terlebih dahulu.');
      }
    }

    function isNumberKey(evt)
    {
      var charCode = (evt.which) ? evt.which : event.keyCode;
     // console.log(charCode);
        if (charCode != 46 && charCode != 45 && charCode > 31
        && (charCode < 48 || charCode > 57))
         return false;

      return true;
    }

    var btn = $('#buttontotop');

    $(window).scroll(function() {
      if ($(window).scrollTop() > 50) {
        btn.addClass('show');
      } else {
        btn.removeClass('show');
      }
    });

    btn.on('click', function(e) {
      e.preventDefault();
      $('html, body').animate({scrollTop:0}, '300');
    });


      $(function(){
        $('#fflogin').form({
          url:'<?= base_url('login/action') ?>',
          onSubmit:function(){
            var v=$(this).form('validate');
            if(v) {
              hideBtnLogin();
              showLoad();
              // reloadCaptcha();
              $('#labellogin').val('');
              return v;
            } else {
              hideBtnLogin();
              hideLoad();
              // reloadCaptcha();
              $('#labellogin').val('');
              showBtnLogin();
              return false;
            }
          },
          success:function(data){
            arrData = data.split("-");
            if(arrData[0] === "sukses") {
              window.location.replace(arrData[1]);
            } else {
              $('#messagelogin').text(arrData[1]);
            }
            showBtnLogin();
            hideLoad();
            // reloadCaptcha();
            // grecaptcha.reset();
          }
        });

      });

      function showLoad() {
        document.getElementById('loading').style.display = "block";
      }

      function hideLoad() {
        document.getElementById('loading').style.display = "none";
      }

      function hideBtnLogin() {
        $('#btnLogin').html('Proses . . .');
      }

      function showBtnLogin() {
        $('#btnLogin').html('<i class="fa fa-sign-in"></i> Login');
      }

    $(function() {
      $('img').bind('contextmenu', function(e){
       return false;
      });
    });

    $(document).ready(function() {
      var f = $("#example_filter");
      var pos = s.position();
      var posfilter = f.position();

      $(window).scroll(function() {
        var windowpos = $(window).scrollTop();
        // alert(windowpos);
        //if (windowpos >= pos.top) {
        if (windowpos >= 207) {
          f.addClass("stickfilter");
        } else {
          f.removeClass("stickfilter");
        }
      });
    });
    <?php
    if($this->USER_LOGIN_ID == "")
    { } else {
    ?>
    setInterval(function(){
      $.ajax({
        url : '<?= base_url('login/autho') ?>',
        type: "GET",
        dataType: "JSON",
        success: function(data)
        { if (data.respon === 'true') {
          window.location.href = '<?= base_url('login/logout') ?>';
          } else {
          }
        },
        error: function (jqXHR, textStatus, errorThrown) { }
      });
    }, 7500);
    <?php } ?>

    </script>

    <?php
    $arrayKatalogSearc = array('inbox_rfi','inbox_survei','inbox_complain');
    if(in_array($pg, $arrayKatalogSearc))
    {
    ?>
      <script src="<?=base_url()?>assets/new/vendors/js/extensions/listjs/list.min.js"></script>
      <script src="<?=base_url()?>assets/new/js/scripts/extensions/list.js"></script>
    <?php
    }
    $browser = getBrowser()['name'];
    if ($pg == 'home' || $pg == '') {
      if ($browser != 'Google Chrome') {
    ?>
        <script type="text/javascript">
          $(window).on('load', function() {
            setTimeout(function () {
              $('#notifBrowserU').modal('show');
            });
          });
        </script>
        <div class="modal animated bounceInUp text-left" id="notifBrowserU" tabindex="-1" role="dialog" aria-labelledby="notifBrowserULabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                  <div style="padding: .5rem 1rem 2rem;">
                    <h1>Informasi</h1>
                      <p class="font-secondary"> Untuk mendapatkan pengalaman terbaik saat menggunakan eProcurement <?= SYSTEM_NAME_PT ?>, kami rekomendasikan untuk menggunakan <b class="font-weight-bold">Google Chrome</b>. Jika belum memiliki, Anda dapat mengunduhnya dengan mengklik tautan dibawah ini:
                      </p>
                    <div class="mt-1">
                      <span class="fa fa-chrome"></span>
                      <a href="https://www.google.com/chrome/" target="_blank" rel="noreferrer noopener" class="bg-hover text-info ml-1">Google Chrome
                      </a>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    <?php
      }
    }
    ?>
  </body>
</html>
