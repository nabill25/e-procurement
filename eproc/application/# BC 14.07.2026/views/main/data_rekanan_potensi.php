<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->load->model("Rekanan");

$reqId = $this->input->get("reqId");
$reqType = $this->input->get("reqType");

$rekanan = new Rekanan();

$rekanan->selectByParams(array("A.REKANAN_ID"=> $reqId),-1,-1);
$rekanan->firstRow();
$reqRekananTipeId = $rekanan->getField("REKANAN_TIPE_ID");
$reqKode = $rekanan->getField("KODE");

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
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/ui/jqueryui.css">
    <script src="<?=base_url()?>assets/new/js/core/libraries/jquery_ui/jquery-ui.min.js"></script>
    <script src="<?=base_url()?>assets/new/js/scripts/ui/jquery-ui/navigations.js"></script>
    <style type="text/css">
      ul.menu-icons li {list-style-type:none;}
      ul { padding-left: 2px; }
    </style>

  </head>

<!-- <body class="body-popup"> -->
<body>

    <div class="card mb-1 border-darken-1">
      <div class="card-content">
        <div class="p-1">
          <div class="row">
            <div class="col-md-3 col-sm-3">
              <div class="jqueryui-ele-container">

                <div id="accordionWrap6" role="tablist" aria-multiselectable="true">
                  <div class="card collapse-icon accordion-icon-rotate">

                    <!--
                    0 : All
                    1 : Admin
                    2 : Teknis
                    3 : KBLI
                    4 : Kontrak (NPWP, NIB, Akta)
                    -->
                    <div id="heading61"  class="card-header border-success">
                      <a data-toggle="collapse" data-parent="#accordionWrap6" href="#accordion61" aria-controls="accordion61" class="card-title lead success">Detail Potensi</a>
                    </div>
                    <div id="accordion61" role="tabpanel" aria-labelledby="heading61" class="card-collapse collapse border-success">
                      <div class="card-content">
                        <div class="card-body">
                          <ul class="menu-icons">
                            <?php
                            if ($reqRekananTipeId != '7') { ?>
                            <li>
                              <div>
                                <a title="" href="main/loadUrl/main/daftar_rekanan_administrasi_umum/?reqId=<?=$reqId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                Umum (Profil Perusahaan)</a>
                              </div>
                            </li>
                            <?php
                            } else { ?>
                            <li>
                              <div>
                                <a title="" href="main/loadUrl/main/daftar_rekanan_administrasi_umum_perorangan/?reqId=<?=$reqId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                Umum (Profile)</a>
                              </div>
                            </li>
                            <?php
                            }

                            if ($reqType == 0 || $reqType == 1 && $reqRekananTipeId != '7') { ?>
                            <li>
                              <div>
                                <a title="" href="main/loadUrl/main/daftar_rekanan_administrasi_pengurus/?reqId=<?=$reqId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                Pengurus Perusahaan</a>
                              </div>
                            </li>
                            <?php
                            }
                            if ($reqType == 0 || $reqType == 2) { ?>
                            <li>
                              <div>
                                <a title="" href="main/loadUrl/main/daftar_rekanan_teknis_pengalaman_lihat/?reqId=<?=$reqId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                Pengalaman</a>
                              </div>
                            </li>
                            <li>
                              <div>
                                <a title="" href="main/loadUrl/main/daftar_rekanan_teknis_sertifikat_lihat/?reqId=<?=$reqId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                <?php
                                if ($reqRekananTipeId != '7') {
                                  echo 'Dokumen Teknis Perusahaan';
                                } else {
                                  echo 'Sertifikat Lain';
                                }
                                ?>
                                </a>
                              </div>
                            </li>
                            <?php
                            }
                            if ($reqType == 0 || $reqType == 4) { ?>
                            <li>
                              <div>
                                <a title="" href="main/loadUrl/main/daftar_rekanan_administrasi_ijin/?reqId=<?=$reqId?>&reqIjinUsahaId=all" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                Ijin Usaha</a>
                              </div>
                            </li>
                            <li>
                              <div>
                                <a title="" href="main/loadUrl/main/daftar_rekanan_administrasi_landasan/?reqId=<?=$reqId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                Akta Pendirian / Perubahan</a>
                              </div>
                            </li>
                            <?php
                            } ?>

                          </ul>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-9 col-sm-9">
              <div class="card">
                <div class="card-content collapse show border-info border-darken-2">
                  <div class="card-body" style="padding-right: 2px">
                  <?php
                    if ($reqRekananTipeId != '7') { ?>
                      <iframe style="width: 100%; height: 410px; border: 0;" name="popupFrame" src="main/loadUrl/main/daftar_rekanan_administrasi_umum/?reqId=<?=$reqId?>"></iframe>
                    <?php
                    } else {?>
                      <iframe style="width: 100%; height: 410px; border: 0;" name="popupFrame" src="main/loadUrl/main/daftar_rekanan_administrasi_umum_perorangan/?reqId=<?=$reqId?>"></iframe>
                    <?php
                    } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <script src="lib/bootstrap/libs/jquery/1.12.4/jquery.min.js"></script>

    <script src="<?=base_url()?>assets/new/vendors/js/vendors.min.js"></script>
    <script src="<?=base_url()?>assets/new/vendors/js/ui/jquery.sticky.js"></script>
    <script src="<?=base_url()?>assets/new/vendors/js/ui/prism.min.js"></script>
    <script src="<?=base_url()?>assets/new/js/core/app-menu.js"></script>
    <script src="<?=base_url()?>assets/new/js/core/app.js"></script>
    <script src="<?=base_url()?>assets/new/js/scripts/ui/breadcrumbs-with-stats.js"></script>
    <script src="<?=base_url()?>assets/new/js/scripts/tooltip/tooltip.js"></script>

  </body>
</html>
