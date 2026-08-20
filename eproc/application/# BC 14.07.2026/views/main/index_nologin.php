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
// $this->load->library("libnotification");

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
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,700' rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i%7CMuli:300,400,500,700" rel="stylesheet">
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

  .stick { position: fixed; top: 50px; padding: 20px 10px 0px 0px; z-index: 999999; }
  .stick2 { position: fixed; top: 70px; padding: 20px 10px 0px 0px; z-index: 999999; }
  .header-navbar .navbar-header .navbar-brand { padding: 5px !important; }
  a.sec-home .card-body img { -webkit-filter: grayscale(0); filter: grayscale(0); }
  a.sec-home:hover .card-body h4 { color: red; }
  a.sec-home:hover .card-body img {  -webkit-filter: grayscale(100%); filter: grayscale(100%); -webkit-transition: .3s ease-in-out; transition: .3s ease-in-out; }
  /*.stickfilter { position: fixed; top: 50px; }*/

  footer.footer-light { background-color: #000 !important; color: #fff; } #Date, #hours,#point,#min,#point,#sec, .fontfooter { color: #fff !important; font-size: .9em !important; } .unslider-nav { display: none; }

  .text-white {color: #fff}ul.unstyled {list-style-type: none;padding: 0px;}.loading2 {position: fixed;z-index: 999999999;height: 2em;width: 2em;overflow: show;margin: auto;top: 0;left: -30px;bottom: 0;right: 0;}.loading2 img {position: fixed;z-index: 9999999999;margin-bottom: 100px;overflow: show;margin: auto;top: -20%;left: 0;bottom: 0;right: 0;}.loading2:before {content: '';display: block;position: fixed;top: 0;left: 0;width: 100%;height: 100%;opacity: .7;background: radial-gradient(rgba(20, 20, 20,.8), rgba(0, 0, 0, .8));background: -webkit-radial-gradient(rgba(20, 20, 20,.8), rgba(0, 0, 0,.8));}.loading {position: fixed;z-index: 999999999;height: 2em;width: 2em;overflow: show;margin: auto;top: 0;left: 0;bottom: 0;right: 0;}.loading:before {content: '';display: block;position: fixed;top: 0;left: 0;width: 100%;height: 100%;background: radial-gradient(rgba(20, 20, 20,.8), rgba(0, 0, 0, .8));background: -webkit-radial-gradient(rgba(20, 20, 20,.8), rgba(0, 0, 0,.8));}.loading:not(:required) {font: 0/0 a;color: transparent;text-shadow: none;background-color: transparent;border: 0;}.loading:not(:required):after {content: '';display: block;font-size: 10px;width: 1em;height: 1em;margin-top: -0.5em;-webkit-animation: spinner 150ms infinite linear;-moz-animation: spinner 150ms infinite linear;-ms-animation: spinner 150ms infinite linear;-o-animation: spinner 150ms infinite linear;animation: spinner 150ms infinite linear;border-radius: 0.5em;-webkit-box-shadow: rgba(255,255,255, 0.75) 1.5em 0 0 0, rgba(255,255,255, 0.75) 1.1em 1.1em 0 0, rgba(255,255,255, 0.75) 0 1.5em 0 0, rgba(255,255,255, 0.75) -1.1em 1.1em 0 0, rgba(255,255,255, 0.75) -1.5em 0 0 0, rgba(255,255,255, 0.75) -1.1em -1.1em 0 0, rgba(255,255,255, 0.75) 0 -1.5em 0 0, rgba(255,255,255, 0.75) 1.1em -1.1em 0 0;box-shadow: rgba(255,255,255, 0.75) 1.5em 0 0 0, rgba(255,255,255, 0.75) 1.1em 1.1em 0 0, rgba(255,255,255, 0.75) 0 1.5em 0 0, rgba(255,255,255, 0.75) -1.1em 1.1em 0 0, rgba(255,255,255, 0.75) -1.5em 0 0 0, rgba(255,255,255, 0.75) -1.1em -1.1em 0 0, rgba(255,255,255, 0.75) 0 -1.5em 0 0, rgba(255,255,255, 0.75) 1.1em -1.1em 0 0;}@-webkit-keyframes spinner {0% {-webkit-transform: rotate(0deg);-moz-transform: rotate(0deg);-ms-transform: rotate(0deg);-o-transform: rotate(0deg);transform: rotate(0deg);}100% {-webkit-transform: rotate(360deg);-moz-transform: rotate(360deg);-ms-transform: rotate(360deg);-o-transform: rotate(360deg);transform: rotate(360deg);}}@-moz-keyframes spinner {0% {-webkit-transform: rotate(0deg);-moz-transform: rotate(0deg);-ms-transform: rotate(0deg);-o-transform: rotate(0deg);transform: rotate(0deg);}100% {-webkit-transform: rotate(360deg);-moz-transform: rotate(360deg);-ms-transform: rotate(360deg);-o-transform: rotate(360deg);transform: rotate(360deg);}}@-o-keyframes spinner {0% {-webkit-transform: rotate(0deg);-moz-transform: rotate(0deg);-ms-transform: rotate(0deg);-o-transform: rotate(0deg);transform: rotate(0deg);}100% {-webkit-transform: rotate(360deg);-moz-transform: rotate(360deg);-ms-transform: rotate(360deg);-o-transform: rotate(360deg);transform: rotate(360deg);}}@keyframes spinner {0% {-webkit-transform: rotate(0deg);-moz-transform: rotate(0deg);-ms-transform: rotate(0deg);-o-transform: rotate(0deg);transform: rotate(0deg);}100% {-webkit-transform: rotate(360deg);-moz-transform: rotate(360deg);-ms-transform: rotate(360deg);-o-transform: rotate(360deg);transform: rotate(360deg);}} .badge[class*='badge-'] span { bottom: 0px !important; }
    #loading {width: 100%;height: 100%;top: 0;left: 0;position: fixed;display: block;opacity: 0.7;background-color: #fff;z-index: 999999;text-align: center; }#loading-image {top: 50%;/*width: 200px;*/position: absolute;left: 44%;margin: 0 auto;z-index: 999999;}
  }
  .modal { z-index: 9999 !important; }
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
                <img class="brand-logo" src="<?= SYSTEM_LOGO_URL_WHITE ?>" style="max-width: 100%; width: 280px !important">
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
            <label for="Username">Username / Email: </label>
            <div class="form-group position-relative has-icon-left">
              <input type="text" name="reqUser" placeholder="Username" class="form-control easyui-validatebox" required="">
              <div class="form-control-position">
                <i class="ft-user line-height-1 text-muted icon-align"></i>
              </div>
            </div>

            <label for="Password">Password: <i id="showPass" class="fa fa-eye-slash" style="cursor:pointer" title="lihat password"></i><i id="hidePass" class="fa fa-eye" style="cursor:pointer;display:none" title="Sembunyikan password"></i></label>
            <div class="form-group position-relative has-icon-left">
              <input type="password" name="reqPasswd" id="reqPassword" placeholder="Password" class="form-control easyui-validatebox" required="" autocomplete="off">
              <div class="form-control-position">
                <i class="ft-lock line-height-1 text-muted icon-align"></i>
              </div>
            </div>
            <div class="form-group position-relative has-icon-left">
              <a href="main/index/lupa_password" class="mr-1">Lupa Password ?</a>
            </div>

            <div class="form-group position-relative has-icon-left">
                <?php // echo $captcha // tampilkan recaptcha ?>
            </div>

            <div class="form-group position-relative has-icon-left">
                <img src="<?php // base_url() ?>main/loadUrl/main/CaptchaSecurityImages/?&width=100&height=40&characters=5" id="captchaImage" style="margin-bottom: 10px"/>&nbsp;&nbsp;&nbsp;<i class="icon-refresh fa-2x" onclick="reloadCaptcha()" style="cursor:pointer;" title="refresh captcha"></i>

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
              <a href="<?= base_url('login/eprocsso') ?>" class="btn btn-success round box-shadow-1 btn-outline-danger btn-lg mt-1 pull-ceter" style="width:100%; color:#fff"><i class="icon-lock"></i> Login with SSO</a>
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
            <img class="brand-logo" src="<?= SYSTEM_LOGO_URL ?>" style="max-width: 100%; width: 280px !important">
          </a>
        </ul>

        <ul class="nav navbar-nav float-right" id="main-menu-navigation" data-menu="menu-navigation">

          <li class="nav-item">
            <a href="<?= base_url() ?>" class="nav-link <?php if ($pg == 'home' || $pg == '') { echo ' active'; } ?>">
              Home</a>
          </li>
          <li class="nav-item">
            <a href="main/index/tender" class="nav-link <?php if ($pg == 'tender') { echo ' active'; } ?>">
              Tender
            </a>
          </li>
          <li class="nav-item">
            <a href="main/index/blacklist" class="nav-link <?php if ($pg == 'blacklist') { echo ' active'; } ?>">
              Daftar Hitam</a>
          </li>
          <li class="nav-item">
            <a href="main/index/kontak" class="nav-link <?php if ($pg == 'kontak') { echo ' active'; } ?>">
              Kontak Kami</a>
          </li>
           <li class="nav-item">
              <a href="main/index/registrasi" class="nav-link <?php if ($pg == 'registrasi') { echo'active'; } ?>">
              Registrasi</a>
           </li>
           <li class="nav-item">
            <a href="#" class="nav-link" data-toggle="modal" data-target="#iconForm">
              Login </a>
           </li>

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

      <section id="basic-carousel" class="d-none d-lg-block">
        <div class="row">
          <div class="col-md-12 col-sm-12">
            <div class="card">
              <div class="card-content">
                <div class="card-body" style="padding: 0 !important">
                  <div class="center-slider mx-auto form-group hidden-xs mt-1">
                    <div id="automatic-slider" style="height:420px;">
                      <ul>
                        <?php
                        while($banner->nextRow())
                        { ?>
                          <li><img src="<?= base_url('uploads/banner/'.$banner->getField("GAMBAR")) ?>" class="img-fluid"></li>

                          <!-- <li><img src="<?= base_url('images/katalog/01.jpg') ?>" class="img-fluid"></li>
                          <li><img src="<?= base_url('images/katalog/02.jpg') ?>" class="img-fluid"></li> -->
                        <?php
                          $no++;
                        } ?>
                      </ul>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section id="backColor user-cards-with-square-thumbnail" class="row mt-2" style="margin:0 8%">
        <div class="col-xl-2 col-md-2 col-12"></div>
        <div class="col-xl-4 col-md-4 col-12">
          <a href="<?= base_url('main/index/tender') ?>" class="sec-home">
            <div class="card box-shadow-2">
              <div class="text-center">
                <div class="card-body">
                  <img src="<?= base_url('images/icon-eproc-home-tender.png') ?>" class="rounded-circle  height-150" alt="Card image">
                </div>
                <div class="card-body">
                  <h4 class="card-title">TENDER</h4>
                </div>
              </div>
            </div>
          </a>
        </div>
        <!-- <div class="col-xl-4 col-md-4 col-12">
          <a href="<?= base_url('main/index/katalog') ?>" class="sec-home">
            <div class="card box-shadow-2">
              <div class="text-center">
                <div class="card-body">
                  <img src="<?= base_url('images/icon-eproc-home-katalog.png') ?>" class="rounded-circle  height-150" alt="Card image">
                </div>
                <div class="card-body">
                  <h4 class="card-title">KATALOG</h4>
                </div>
              </div>
            </div>
          </a>
        </div> -->
        <div class="col-xl-4 col-md-4 col-12">
          <a href="<?= base_url('main/index/registrasi') ?>" class="sec-home">
            <div class="card box-shadow-2">
              <div class="text-center">
                <div class="card-body">
                  <img src="<?= base_url('images/icon-eproc-home-registrasi.png') ?>" class="rounded-circle  height-150" alt="Card image">
                </div>
                <div class="card-body">
                  <h4 class="card-title">REGISTRASI</h4>
                </div>
              </div>
            </div>
          </a>
        </div>
      </section>

      <?php
      $this->load->model("Berita");
      $berita = new Berita();
      $arrStatement = array();
      $berita->selectByParams($arrStatement, 3, 0);
      ?>
      <section class="card mt-5" style="padding: 8% 5%;">

        <div class="row">
          <div class="col-md-8 offset-md-2">
            <h2 class="list-group-item-heading text-center mb-3">PENGUMUMAN & BERITA</h2>
              <?php
                while($berita->nextRow())
                {
                  $beritaId = $berita->getField("BERITA_ID"); ?>

                  <p>
                    <h4>
                      <a href="main/index/beritad/?id=<?=$beritaId?>"><?=$berita->getField("NAMA")?></a> <small></small>
                    </h4>
                  </p>
                    <?php
                    echo substr($berita->getField("KETERANGAN"), 0, 250);
                    ?>...
                    <footer class="blockquote-footer">
                      <cite title="Source Title"><?=getFormattedDate($berita->getField("TANGGAL"))?></cite>
                    </footer>
                    <hr>
              <?php
              } ?>

              <p class="text-center">

              <?php
              if ($berita->countRow() > 2) {
                echo '<a href="'.base_url().'main/index/berita" class="btn round btn-primary mt-4 text-white text-center"> Lihat selengkapnya...</a>';
              }
              ?>
              </p>
              <?php //$pagination->createLinks()?>
          </div>
        </div>

      </section>

      <section style="padding: 8% 5%;">
        <div class="row">
            <div class="offset-md-2 col-sm-2 text-center mb-1">
              <img src="<?= base_url('images/icon-warning-2.png') ?>" class="height-150" alt="Warning image">
            </div>
            <div class="col-sm-6">
              <h3 class="card-title">HATI-HATI DENGAN PENIPUAN!</h3>
              <p class="card-text align-self-center"><?= SYSTEM_NAME_PT ?> tidak pernah memungut biaya atau meminta uang dari calon penyedia barang/jasa <br>
              dalam proses pendaftaran Vendor Management System (VMS) <br> maupun dalam proses pengadaan barang/jasa. <br><br> Terima Kasih</p>
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
        <!-- <script src="<?=base_url()?>assets/new/vendors/js/ui/prism.min.js"></script> disable ikn -->
        <script src="<?=base_url()?>assets/new/js/core/app-menu.js"></script>
        <script src="<?=base_url()?>assets/new/js/core/app.js"></script>
        <script src="<?=base_url()?>assets/new/vendors/js/extensions/toastr.min.js"></script>
        <script src="<?=base_url()?>assets/new/js/scripts/tooltip/tooltip.js"></script>
        <script src="<?=base_url()?>assets/new/vendors/scripts/extensions/unslider.js"></script>

        <div class="content-body"><!-- Basic Carousel start -->
          <?=($content ? $content:'')?>
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
    <script src="<?=base_url()?>assets/js/bootstrap-affix.js"></script>
    <script src="<?=base_url()?>assets/js/holder/holder.js"></script>
    <script src="<?=base_url()?>assets/js/google-code-prettify/prettify.js"></script>
    <script src="<?=base_url()?>assets/js/application.js"></script>
    <script src="<?=base_url()?>lib/bootstrap/assets/js/ie10-viewport-bug-workaround.js"></script>
    <script>
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
      // End New Time

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
    /*append the DIV element as a child of the autocomplete container:*/
    this.parentNode.appendChild(a);
    /*for each item in the array...*/
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
  /*execute a function presses a key on the keyboard:*/
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
// for validation vendor
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

    <!-- <script src="<?=base_url()?>lib/emodal/eModal-popup-chat.js"></script> disable ikn -->
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

    <!-- tiny MCE -->
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
            // alert(arrData[1]);
            window.location.replace(arrData[1]);
            // location.reload(arrData[1]);
          } else {
            $('#messagelogin').text(arrData[1]);
          }
          showBtnLogin();
          hideLoad();
          reloadCaptcha();
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

  </script>

    <?php
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
