<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

if($this->USER_TYPE_ID == "")
    redirect("main");

include_once("functions/string.func.php");
include_once("functions/default.func.php");

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->model("PaketEvaluasiSyaratDaftar");

$paket_evaluasi_syarat_daftar = new PaketEvaluasiSyaratDaftar();

$reqId = $this->input->get("reqId");
$reqPaketId = $this->input->get("reqPaketId");

$paketInfo->getPaket($reqPaketId);

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
    <!-- END VENDOR CSS-->
    <!-- BEGIN ROBUST CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/app.css">
    <!-- <link rel="stylesheet" type="text/css" href="<?=base_url() ?>css/core.css"> -->
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/core/colors/palette-callout.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
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
                    <div id="heading61"  class="card-header border-success">
                      <a data-toggle="collapse" data-parent="#accordionWrap6" href="#accordion61" aria-expanded="true" aria-controls="accordion61" class="card-title lead success">Data Administrasi Umum</a>
                    </div>
                    <div id="accordion61" role="tabpanel" aria-labelledby="heading61" class="card-collapse collapse show border-success" aria-expanded="true">
                      <div class="card-content">
                        <div class="card-body">
                          <ul class="menu-icons">
                            <li> 
                              <div>
                                <a title="" href="main/loadUrl/main/daftar_rekanan_administrasi_umum/?reqId=<?=$reqId?>&reqPaketId=<?=$reqPaketId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                Umum</a>
                              </div>
                            </li>  
                            <li> 
                              <div>
                                <a title="" href="main/loadUrl/main/daftar_rekanan_administrasi_landasan/?reqId=<?=$reqId?>&reqPaketId=<?=$reqPaketId?>&reqMode=koreksi" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                Landasan Hukum</a>
                              </div>
                            </li>  
                            <li> 
                              <div>
                                <a title="" href="main/loadUrl/main/daftar_rekanan_administrasi_pengurus/?reqId=<?=$reqId?>&reqPaketId=<?=$reqPaketId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                Pengurus Perusahaan</a>
                              </div>
                            </li>  
                            <li> 
                              <div>
                                <a title="" href="main/loadUrl/main/daftar_rekanan_administrasi_keuangan/?reqId=<?=$reqId?>&reqPaketId=<?=$reqPaketId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                Kepemilikan Saham</a>
                              </div>
                            </li>  
                            <?php if($paketInfo->syarat_ijin_siup > 0){ ?>
                              <li> 
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_administrasi_ijin/?reqId=<?=$reqId?>&reqIjinUsahaId=1&reqPaketId=<?=$reqPaketId?>&reqMode=koreksi" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  SIUP</a>
                                </div>
                              </li>  
                            <?php } ?>
                            <?php if($paketInfo->syarat_ijin_siujk > 0){ ?>
                              <li> 
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_administrasi_ijin/?reqId=<?=$reqId?>&reqIjinUsahaId=3&reqPaketId=<?=$reqPaketId?>&reqMode=koreksi" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  SIUJK</a>
                                </div>
                              </li>  
                            <?php } ?>
                            <?php if($paketInfo->syarat_ijin_siui > 0){ ?>
                              <li> 
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_administrasi_ijin/?reqId=<?=$reqId?>&reqIjinUsahaId=4&reqPaketId=<?=$reqPaketId?>&reqMode=koreksi" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  SIUI</a>
                                </div>
                              </li>  
                            <?php } ?>
                            <?php if($paketInfo->syarat_ijin_lain > 0){ ?>
                              <li> 
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_administrasi_ijin/?reqId=<?=$reqId?>&reqIjinUsahaId=5&reqPaketId=<?=$reqPaketId?>&reqMode=koreksi" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  Ijin Lain</a>
                                </div>
                              </li>  
                            <?php } ?>
                            <?php if($paketInfo->syarat_sbu > 0){ ?>
                              <li> 
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_administrasi_sbu/?reqId=<?=$reqId?>&reqPaketId=<?=$reqPaketId?>&reqPaketId=<?=$reqPaketId?>&reqMode=koreksi" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  Sertifikat Badan Usaha</a>
                                </div>
                              </li>  
                            <?php } ?>  
                          </ul>
                        </div>
                      </div>
                    </div>
                    <?php if($paketInfo->syarat_rekening_koran > 0){ ?>
                    <div id="heading62"  class="card-header mt-1 border-danger">
                      <a data-toggle="collapse" data-parent="#accordionWrap6" href="#accordion62" aria-expanded="false" aria-controls="accordion62" class="card-title lead danger collapsed">Rekening Koran</a>
                    </div>
                    <div id="accordion62" role="tabpanel" aria-labelledby="heading62" class="border-danger no-border-top card-collapse collapse" aria-expanded="false">
                      <div class="card-content">
                        <div class="card-body">
                          <ul class="menu-icons"> 
                            <?php if($paketInfo->syarat_rekening_koran > 0){ ?>
                              <li> 
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_keuangan_rekening/?reqId=<?=$reqId?>&reqPaketId=<?=$reqPaketId?>&reqMode=koreksi" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  Rekening Koran</a>
                                </div>
                              </li>  
                            <?php } ?> 
                        </ul>
                        </div>
                      </div>
                    </div>
                    <?php } ?>

                    <?php if($paketInfo->syarat_keuangan_pkp > 0 || $paketInfo->syarat_keuangan_spt > 0 || 
                         $paketInfo->syarat_keuangan_pph > 0 || $paketInfo->syarat_keuangan_ppn > 0 || 
                         $paketInfo->syarat_neraca > 0){ ?>
                    <div id="heading63"  class="card-header mt-1 border-info">
                      <a data-toggle="collapse" data-parent="#accordionWrap6" href="#accordion63" aria-expanded="false" aria-controls="accordion63" class="card-title lead info collapsed">Data Perpajakan</a>
                    </div>
                    <div id="accordion63" role="tabpanel" aria-labelledby="heading63" class="border-info no-border-top card-collapse collapse" aria-expanded="false">
                      <div class="card-content">
                        <div class="card-body">
                          <ul class="menu-icons">
                            <?php if($paketInfo->syarat_keuangan_pkp > 0){ ?>
                              <li> 
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_pajak_pkp/?reqId=<?=$reqId?>&reqPaketId=<?=$reqPaketId?>&reqMode=koreksi&reqKeuangan=PKP" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  PKP</a>
                                </div>
                              </li>  
                            <?php } ?>
                            <?php if($paketInfo->syarat_keuangan_spt > 0){ ?>
                              <li> 
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_pajak_spt_tahunan/?reqId=<?=$reqId?>&reqPaketId=<?=$reqPaketId?>&reqMode=koreksi&reqKeuangan=SPT" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  SPT Tahunan</a>
                                </div>
                              </li>  
                            <?php } ?>
                            <?php if($paketInfo->syarat_keuangan_pph > 0){ ?>
                              <li> 
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_pajak_bulanan/?reqId=<?=$reqId?>&reqPaketId=<?=$reqPaketId?>&reqTipe=2&reqMode=koreksi&reqKeuangan=PPH" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  PPH</a>
                                </div>
                              </li>  
                            <?php } ?>
                            <?php if($paketInfo->syarat_keuangan_ppn > 0){ ?>
                              <li> 
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_pajak_bulanan/?reqId=<?=$reqId?>&reqPaketId=<?=$reqPaketId?>&reqTipe=3&reqMode=koreksi&reqKeuangan=PPN" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  PPN</a>
                                </div>
                              </li>  
                            <?php } ?>
                            <?php if($paketInfo->syarat_neraca > 0){ ?>
                              <li> 
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_pajak_neraca/?reqId=<?=$reqId?>&reqPaketId=<?=$reqPaketId?>&reqMode=koreksi&reqKeuangan=NERACA" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  Neraca</a>
                                </div>
                              </li>   
                            <?php } ?>
                          </ul>
                        </div>
                      </div>
                    </div>
                    <?php } ?>
                    <div id="heading64"  class="card-header mt-1 border-warning">
                      <a data-toggle="collapse" data-parent="#accordionWrap6" href="#accordion64" aria-expanded="false" aria-controls="accordion64" class="card-title lead warning collapsed">Data Teknis</a>
                    </div>
                    <div id="accordion64" role="tabpanel" aria-labelledby="heading64" class="border-warning no-border-top card-collapse collapse" aria-expanded="false" style="height: 0px;">
                      <div class="card-content">
                        <div class="card-body"> 
                          <ul class="menu-icons"> 
                              <li> 
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_teknis_pengalaman/?reqId=<?=$reqId?>&reqPaketId=<?=$reqPaketId?>&reqMode=koreksi&reqKeuangan=NERACA" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  Pengalaman</a>
                                </div>
                              </li>  
                            <?php if($paketInfo->syarat_teknis_tenaga_ahli > 0){ ?>
                              <li> 
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_teknis_tenaga/?reqId=<?=$reqId?>&reqPaketId=<?=$reqPaketId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  Tenaga Ahli</a>
                                </div>
                              </li>  
                            <?php } ?> 
                            <?php if($paketInfo->syarat_teknis_peralatan > 0){ ?>
                              <li> 
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_teknis_peralatan/?reqId=<?=$reqId?>&reqPaketId=<?=$reqPaketId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  Peralatan</a>
                                </div>
                              </li>  
                            <?php } ?>
                            <?php if($paketInfo->syarat_teknis_sertifikat > 0){ ?>
                              <li> 
                                <div>
                                  <a title="" href="main/loadUrl/main/daftar_rekanan_teknis_sertifikat/?reqId=<?=$reqId?>&reqPaketId=<?=$reqPaketId?>" target="popupFrame"><span class="fa fa-angle-double-right"></span>
                                  Sertifikat Lain</a>
                                </div>
                              </li>  
                            <?php } ?>
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
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-9 col-sm-9">
              <div class="card">
                <div class="card-content collapse show border-info border-darken-2">
                  <div class="card-body" style="padding-right: 2px">
                    <iframe style="width: 100%; height: 410px; border: 0;" name="popupFrame" src="main/loadUrl/main/daftar_rekanan_administrasi_umum/?reqId=<?=$reqId?>"></iframe>
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
