<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->load->model("Rekanan");

$reqId = $this->input->get("reqId");
$reqValidasi = $this->input->get("reqValidasi");

$rekanan = new Rekanan();

$rekanan->selectByParams(array("A.REKANAN_ID"=> $reqId),-1,-1);
$rekanan->firstRow();
$reqRekananTipeId = $rekanan->getField("REKANAN_TIPE_ID");
$reqKode = $rekanan->getField("KODE");
$reqNama = $rekanan->getField("NAMA");

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
      .card { box-shadow: none !important;background: #fff !important;}
      .pace-done { background: #fff !important; } 
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

                    <div id="heading61"  class="card-header border-success">
                      <a data-toggle="collapse" data-parent="#accordionWrap6" href="#accordion61" aria-controls="accordion61" class="card-title lead success">Data Administrasi Umum</a>
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
                            <li>
                              <div>
                                <a title="" href="main/loadUrl/main/daftar_rekanan_administrasi_ijin/?reqId=<?=$reqId?>&reqIjinUsahaId=all" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                N I B</a>
                              </div>
                            </li>
                            <li>
                              <div>
                                <a title="" href="main/loadUrl/main/daftar_rekanan_administrasi_landasan/?reqId=<?=$reqId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                Akta Pendirian / Perubahan</a>
                              </div>
                            </li>
                            <li>
                              <div>
                                <a title="" href="main/loadUrl/main/daftar_rekanan_administrasi_pengurus/?reqId=<?=$reqId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                Pengurus Perusahaan</a>
                              </div>
                            </li>
                            <li>
                              <div>
                                <a title="" href="main/loadUrl/main/daftar_rekanan_administrasi_keuangan/?reqId=<?=$reqId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                Kepemilikan Saham</a>
                              </div>
                            </li>
                            <li>
                              <div>
                                <a title="" href="main/loadUrl/main/daftar_rekanan_administrasi_sbu/?reqId=<?=$reqId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                Sertifikat Badan Usaha Konstruksi</a>
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
                            <li>
                              <div>
                                <a title="" href="main/loadUrl/main/daftar_rekanan_administrasi_ijin/?reqId=<?=$reqId?>&reqIjinUsahaId=all" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                NIB</a>
                              </div>
                            </li>
                            <li>
                              <div>
                                <a title="" href="main/loadUrl/main/daftar_rekanan_administrasi_cv/?reqId=<?=$reqId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                CV (Daftar Riwayat Hidup)</a>
                              </div>
                            </li>
                            <?php
                            } ?>
                          </ul>
                        </div>
                      </div>
                    </div>

                    <?php
                    if ($reqRekananTipeId != '7') { ?>
                    <div id="heading62"  class="card-header mt-1 border-danger">
                      <a data-toggle="collapse" data-parent="#accordionWrap6" href="#accordion62" aria-expanded="false" aria-controls="accordion62" class="card-title lead danger collapsed">Data Keuangan</a>
                    </div>
                    <div id="accordion62" role="tabpanel" aria-labelledby="heading62" class="border-danger no-border-top card-collapse collapse" aria-expanded="false">
                      <div class="card-content">
                        <div class="card-body">
                          <ul class="menu-icons">
                              <li>
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_keuangan_rekening/?reqId=<?=$reqId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  Rekening Koran</a>
                                </div>
                              </li>
                              <li>
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_pajak_neraca/?reqId=<?=$reqId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  Neraca</a>
                                </div>
                              </li>
                        </ul>
                        </div>
                      </div>
                    </div>
                    <?php
                    } ?>

                    <div id="heading63"  class="card-header mt-1 border-info">
                      <a data-toggle="collapse" data-parent="#accordionWrap6" href="#accordion63" aria-expanded="false" aria-controls="accordion63" class="card-title lead info collapsed">Data Perpajakan</a>
                    </div>
                    <div id="accordion63" role="tabpanel" aria-labelledby="heading63" class="border-info no-border-top card-collapse collapse" aria-expanded="false">
                      <div class="card-content">
                        <div class="card-body">
                          <ul class="menu-icons">
                            <?php
                            if ($reqRekananTipeId != '7') { ?>
                              <li>
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_pajak_pkp/?reqId=<?=$reqId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  PKP / Non PKP</a>
                                </div>
                              </li>
                              <li>
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_pajak_spt_tahunan/?reqId=<?=$reqId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  SPT Tahunan</a>
                                </div>
                              </li>
                              <li>
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_pajak_bulanan/?reqId=<?=$reqId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  Laporan Pajak Bulanan (PPN)</a>
                                </div>
                              </li>
                            <?php
                            } else { ?>
                              <li>
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_pajak_spt_tahunan/?reqId=<?=$reqId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  SPT Tahunan</a>
                                </div>
                              </li>
                            <?php
                            } ?>
                          </ul>
                        </div>
                      </div>
                    </div>

                    <div id="heading64"  class="card-header mt-1 border-warning">
                      <a data-toggle="collapse" data-parent="#accordionWrap6" href="#accordion64" aria-expanded="false" aria-controls="accordion64" class="card-title lead warning collapsed">Data Teknis</a>
                    </div>
                    <div id="accordion64" role="tabpanel" aria-labelledby="heading64" class="border-warning no-border-top card-collapse collapse" aria-expanded="false" style="height: 0px;">
                      <div class="card-content">
                        <div class="card-body">
                          <ul class="menu-icons">
                            <?php
                            if ($reqRekananTipeId != '7') { // Konsultan Perorangan ?>
                              <li>
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_teknis_tenaga_lihat/?reqId=<?=$reqId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  Tenaga Ahli</a>
                                </div>
                              </li>
                              <li>
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_teknis_pengalaman_lihat/?reqId=<?=$reqId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  Pengalaman</a>
                                </div>
                              </li>
                              <li>
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_teknis_peralatan_lihat/?reqId=<?=$reqId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  Peralatan</a>
                                </div>
                              </li>
                              <li>
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_teknis_sertifikat_lihat/?reqId=<?=$reqId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  Dokumen Teknis Perusahaan</a>
                                </div>
                              </li>
                            <?php
                            } else { ?>
                              <li>
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_teknis_pengalaman_lihat/?reqId=<?=$reqId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  Pengalaman</a>
                                </div>
                              </li>
                              <li>
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_teknis_sertifikat_lihat/?reqId=<?=$reqId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  Sertifikat Lain</a>
                                </div>
                              </li>
                            <?php
                            } ?>
                          </ul>
                        </div>
                      </div>
                    </div>

                    <div id="heading199"  class="card-header mt-1 border-danger">
                      <a data-toggle="collapse" data-parent="#accordionWrap6" href="#accordion199" aria-expanded="false" aria-controls="accordion199" class="card-title lead danger collapsed">Ranking</a>
                    </div>
                    <div id="accordion199" role="tabpanel" aria-labelledby="heading199" class="border-danger no-border-top card-collapse collapse" aria-expanded="false" style="height: 0px;">
                      <div class="card-content">
                        <div class="card-body">
                          <ul class="menu-icons">
                              <li>
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_penilaian/?reqId=<?=$reqId?>&reqPaketId=<?=$reqPaketId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  Penilaian</a>
                                </div>
                              </li>
                          </ul>
                        </div>
                      </div>
                    </div>

                    <?php
                    if ($reqValidasi == 1) { ?>
                    <div id="heading999"  class="card-header mt-1 border-danger">
                      <a data-toggle="collapse" data-parent="#accordionWrap6" href="#accordion999" aria-expanded="false" aria-controls="accordion999" class="card-title lead danger collapsed">Rekomendasi</a>
                    </div>
                    <div id="accordion999" role="tabpanel" aria-labelledby="heading999" class="border-danger no-border-top card-collapse collapse" aria-expanded="false" style="height: 0px;">
                      <div class="card-content">
                        <div class="card-body">
                          <ul class="menu-icons">
                              <li>
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_rekomendasi/?reqId=<?=$reqId?>&reqPaketId=<?=$reqPaketId?>" target="popupFrame" id="urlRefreshChecklist"><span class="fa fa-angle-double-right"></span>
                                  Checklist Kelengkapan </a>
                                </div>
                              </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                    <?php
                    } ?>

                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-9 col-sm-9">
              <!-- <div class="card" style="height: 100vh; overflow: auto;"> -->
                <div class="card-content collapse show border-info border-darken-2">
                  <div class="card-body" style="padding-right: 2px">
                  <?php
                  if ($reqValidasi == 1) { ?>
                    <iframe style="width: 100%; height: 100%; min-height: 100vh; border: 0;" name="popupFrame" src="main/loadUrl/main/daftar_rekanan_rekomendasi/?reqId=<?=$reqId?>"></iframe>
                  <?php
                    ?>
                  <?php
                  } else {
                    if ($reqRekananTipeId != '7') { ?>
                      <iframe style="width: 100%; height: 100%; min-height: 100vh; border: 0;" name="popupFrame" src="main/loadUrl/main/daftar_rekanan_administrasi_umum/?reqId=<?=$reqId?>"></iframe>
                    <?php
                    } else {?>
                      <iframe style="width: 100%; height: 100%; min-height: 100vh; border: 0;" name="popupFrame" src="main/loadUrl/main/daftar_rekanan_administrasi_umum_perorangan/?reqId=<?=$reqId?>"></iframe>
                    <?php
                    }
                    if(($this->USER_TYPE_ID == 2 || $this->USER_TYPE_ID == 18 || $this->USER_TYPE_ID == 19) && $reqValidasi == 0) // Admin VMS, Approval Penyelia, Approval Sub Div & sudah diapprov
                    { ?>
                      <div class="col-md-12 text-center">
                        <a id="btnSertifikat" target="_blank" href="main/loadUrl/report/vms_pdf/?reqId=<?=$reqId?>&kode=<?=$reqKode?>" onclick="if(confirm('Cetak Surat Keterangan Terdaftar (SKT) ?')) { return true; } else { return false; }" class="<?= CLASS_BTN_INFO ?>"><i class="fa fa-print"></i> Cetak Surat Keterangan Terdaftar (SKT)</a>

                        <?php
                        $this->load->model("Dokumenrekanan");
                        $dokumen_rekanan = new Dokumenrekanan();
                        $dokumen_rekanan->selectByParams(array('REKANAN_ID' => $reqId));
                         ?>
                        <?php
                          if ($dokumen_rekanan->countRow() > 0) {
                            $dokumen_rekanan->firstRow();
                            echo '<a style="color:#fff" href="uploads/pakta_integritas/'.$dokumen_rekanan->getField('PATH_FILE').'" target="_blank" class="'.CLASS_BTN_SUCCESS.'"><span class="fa fa-book"></span> Pakta Integritas</a>';
                          } else { }
                        ?>

                      </div>
                    <?php
                    }
                  } ?>
                  </div>
                </div>
              <!-- </div> -->
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
