<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

class libkontrak
{
  private $_CI;

  function __construct()
  {
    $this->_CI =& get_instance();
    $this->_CI->load->library('kauth');
    $this->_CI->ID              =  $this->_CI->kauth->getInstance()->getIdentity()->REKANAN_ID;
    $this->_CI->USER_LOGIN_ID   =  $this->_CI->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
    $this->_CI->USER_LOGIN      =  $this->_CI->kauth->getInstance()->getIdentity()->USER_LOGIN;
    $this->_CI->USER_NAMA       =  $this->_CI->kauth->getInstance()->getIdentity()->USER_NAMA;
    $this->_CI->USER_TYPE_ID    =  $this->_CI->kauth->getInstance()->getIdentity()->USER_TYPE_ID;
    $this->_CI->LEGAL           =  $this->_CI->kauth->getInstance()->getIdentity()->LEGAL;
    $this->_CI->PENUNJUK_PIC    =  $this->_CI->kauth->getInstance()->getIdentity()->PENUNJUK_PIC;
    $this->_CI->LEVEL_KONTRAK   =  $this->_CI->kauth->getInstance()->getIdentity()->LEVEL_KONTRAK;
    $this->_CI->HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
  }

  function baseProses($getTahun='all')
  {
    // INISIASI KONTRAK
    // PERENCANAAN KONTRAK
    // PELAKSANAAN KONTRAK
    // MONITOR DAN KONTROL KONTRAK
    // PENUTUPAN KONTRAK
    // SELESAI
    // SPPBJ
    $this->_CI->load->model("Contracting");
    $getMenu = new Contracting();
    $getMenu->selectMenuProses(array("A.cp_show" => "1"));
    $totalRow = $getMenu->countRow();
    $totalRowPersen = 100 / $totalRow;
    $html  = '';
    $html .= '<ul class="nav nav-tabs process-model more-icon-preocess" role="tablist">';
              while($getMenu->nextRow()){
                $this->_CI->load->model("Contractingrekanan");
                $contractingrekanan = new Contractingrekanan();
                if ($getTahun == 'all' || $getTahun == '') {
                  if ($getMenu->getField('contractingprosesid') == '3'  || $getMenu->getField('contractingprosesid') == '4'
                     ) // Pelaksanaan & Monitoring Berjalan Bersamaan
                  {
                    if ($this->_CI->LEGAL == '1') { // PENG. KONTRAK BAGIAN LEGAL
                      $totalProses = $contractingrekanan->selectByParamsCount2(array(), " AND A.CONTRACTINGPROSESID in ('3','4')");
                    } else {
                      if ($this->_CI->USER_TYPE_ID == '20') { // Pemeriksa
                        $totalProses = $contractingrekanan->selectByParamsCount2(array(), " AND A.CONTRACTINGPROSESID in ('3','4') AND A.JNS_KONTRAK IN ('0','1','3') ");
                      } else {
                        $totalProses = $contractingrekanan->selectByParamsCount2(array(), " AND C.PPK = '".$this->_CI->USER_LOGIN_ID."' AND A.CONTRACTINGPROSESID in ('3','4')");
                      }
                    }
                  } else {
                    if ($this->_CI->LEGAL == '1') { // PENG. KONTRAK BAGIAN LEGAL
                      $totalProses = $contractingrekanan->selectByParamsCount2(array(), " AND A.CONTRACTINGPROSESID = '".$getMenu->getField('CONTRACTINGPROSESID')."'");
                    } else {
                      if ($this->_CI->USER_TYPE_ID == '20') { // Pemeriksa
                        $totalProses = $contractingrekanan->selectByParamsCount2(array(), " AND A.CONTRACTINGPROSESID = '".$getMenu->getField('CONTRACTINGPROSESID')."' AND A.JNS_KONTRAK IN ('0','1','3')");
                      } else {
                        $totalProses = $contractingrekanan->selectByParamsCount2(array(), " AND C.PPK = '".$this->_CI->USER_LOGIN_ID."' AND A.CONTRACTINGPROSESID = '".$getMenu->getField('CONTRACTINGPROSESID')."'");
                        // echo $contractingrekanan->query;
                      }
                    }
                  }
                } else {
                  if ($getMenu->getField('contractingprosesid') == '3'  || $getMenu->getField('contractingprosesid') == '4'
                     ) // Pelaksanaan & Monitoring Berjalan Bersamaan
                  {
                    if ($this->_CI->LEGAL == '1') { // PENG. KONTRAK BAGIAN LEGAL
                      $totalProses = $contractingrekanan->selectByParamsCount2(array(), " AND A.CONTRACTINGPROSESID in ('3','4') AND C.TAHUN_SPPBJ::text[] && array['".$getTahun."'] ");
                    } else {
                      if ($this->_CI->USER_TYPE_ID == '20') { // Pemeriksa
                        $totalProses = $contractingrekanan->selectByParamsCount2(array(), " AND A.CONTRACTINGPROSESID in ('3','4') AND C.TAHUN_SPPBJ::text[] && array['".$getTahun."'] AND A.JNS_KONTRAK IN ('0','1','3')");
                      } else {
                        $totalProses = $contractingrekanan->selectByParamsCount2(array(), " AND C.PPK = '".$this->_CI->USER_LOGIN_ID."' AND A.CONTRACTINGPROSESID in ('3','4') AND C.TAHUN_SPPBJ::text[] && array['".$getTahun."'] ");
                      }
                    }
                  } else {
                    if ($this->_CI->LEGAL == '1') { // PENG. KONTRAK BAGIAN LEGAL
                      $totalProses = $contractingrekanan->selectByParamsCount2(array(), " AND A.CONTRACTINGPROSESID = '".$getMenu->getField('CONTRACTINGPROSESID')."' AND C.TAHUN_SPPBJ::text[] && array['".$getTahun."'] ");
                    } else {
                      if ($this->_CI->USER_TYPE_ID == '20') { // Pemeriksa
                        $totalProses = $contractingrekanan->selectByParamsCount2(array(), " AND A.CONTRACTINGPROSESID = '".$getMenu->getField('CONTRACTINGPROSESID')."' AND C.TAHUN_SPPBJ::text[] && array['".$getTahun."'] AND A.JNS_KONTRAK IN ('0','1','3')");
                      } else {
                        $totalProses = $contractingrekanan->selectByParamsCount2(array(), " AND C.PPK = '".$this->_CI->USER_LOGIN_ID."' AND A.CONTRACTINGPROSESID = '".$getMenu->getField('CONTRACTINGPROSESID')."' AND C.TAHUN_SPPBJ::text[] && array['".$getTahun."'] ");
                      }
                    }
                  }
                }

                $class = '';
                if ($getMenu->getField('cp_link') == 'kontrak/index/'.$this->_CI->uri->segment(3, "")) {
                  $class = 'class="active"';
                }
    $html .=   '<li role="presentation" style="width: '.$totalRowPersen.'% !important;" '.$class.'>
                <a href="'.$getMenu->getField('cp_link').'?tahun='.$getTahun.'">
                  <i class="'.$getMenu->getField('cp_icon').'" aria-hidden="true"></i>
                  <p> <span class="badge badge-primary" style="font-size: 1em">'.$totalProses.'</span><br>'.$getMenu->getField('cp_name').' </p>
                </a>
              </li>';
              }
    $html .= '</ul> ';

    return $html;
  }

  function getMenu($contractingrekananid,$reqProses=null)
  {
    $this->_CI->load->model("Contracting");
    $this->_CI->load->model(array("Contractingrekanan","Contractingmaterial"));

    $getMenu = new Contracting();
    $getMenuSub = new Contracting();
    $contractingrekanan = new Contractingrekanan();
    $contractingmaterial = new Contractingmaterial();

    if ($reqProses) {
      $reqProses = $reqProses;
    } else {
      $reqProses = $this->_CI->session->userdata('setProsesKontrak');
    }

    $contractingmaterial->selectByParams(array("A.CONTRACTINGREKANANID" => $contractingrekananid));
    $contractingmaterial->firstRow();
    $materialSifat = $contractingmaterial->getField('SIFAT'); // 1:Berubah, 2:Tetap

    $contractingrekanan->selectByParams(array("A.CONTRACTINGREKANANID" => $contractingrekananid));
    $contractingrekanan->firstRow();
    $contractingprosesid = $contractingrekanan->getField('CONTRACTINGPROSESID');
    $jnsKontrak = $contractingrekanan->getField('JNS_KONTRAK');
    $html  = '<style type="text/css">
      a.list-group-item { color: #000 !important; }
      .list-group-item { padding: 0.7rem 1.25rem !important; }
    </style>';

    switch ($this->_CI->USER_TYPE_ID) {
      case '12': // Pengelola Kontrak
        if ($this->_CI->LEVEL_KONTRAK == '1') 
        { // Staff
          // echo "Menu Untuk Staff: proses 1 & jenis_kontrak:".$jnsKontrak;

          if ($contractingprosesid == '6') { // Selesai
            $getMenu->selectMenuProses(array("A.CONTRACTINGPROSESID" => '6'));
            $getMenu->firstRow();
            $cp_name = $getMenu->getField('CP_NAME');
            $cp_link = $getMenu->getField('CP_LINK');
            $getMenuSub->selectMatrix(array("A.CONTRACTINGPROSESID" => "6", "JNS_KONTRAK" => $jnsKontrak), -1, -1, " OR ( A.CONTRACTINGPROSESID = '6' AND JNS_KONTRAK = '2' ) OR ( A.CONTRACTINGPROSESID = '6' AND JNS_KONTRAK = '2' ) AND A.CM_STATUS='1'");  // JNS_KONTRAK 0:SPK, 1:PKS, 2:ALL

            $html .= '<div class="list-group">
                        <a class="btn-primary text-white list-group-item disabled" style="color:#fff !important; background-color:#000 !important; border-color:none !important;"> '.$cp_name.'</a>';
                         while($getMenuSub->nextRow())
                         {
                          if ($getMenuSub->getField('cm_link') == 'kontrak/index/'.$this->_CI->uri->segment(3, "") ||
                              ($getMenuSub->getField('cm_name_menu') == "SPPBJ" && $this->_CI->uri->segment(3, "") == 'contracting_persiapan_sppbj_edit')
                              ) {
                            $colorBg = '  background-color: #F7CA18 !important; color:#000 !important;';
                            $colorFotn = ' color:#000';
                          } else { $colorBg=""; $colorFotn ="";}
            $html .=      '
                              <a class="list-group-item" href="'.$getMenuSub->getField('cm_link').'?reqId='.$contractingrekananid.'" style="border-bottom: 1px solid #f5ebeb; cursor: pointer; '.$colorBg.'">
                                <i class="fa fa-angle-double-right">
                                <i style="height: 20px; font-size: 17px; '.$colorFotn.'"> '.$getMenuSub->getField('cm_name_menu') .' </i>
                                 <small style="font-size: 10px; display: block; font-weight: normal; margin:5px 0 0 12px; '.$colorFotn.'" class="hidden-md">'.$getMenuSub->getField('cm_name') .'</small>
                                </i>
                              </a>
                              ';
                        }
          } else 
          {
            if ($jnsKontrak == '3') { // Surat Pesanan
              // $getMenu->selectMenuProses(array("A.CONTRACTINGPROSESID" => $contractingprosesid));
              $getMenu->selectMenuProses(array("A.CONTRACTINGPROSESID" => $contractingprosesid));
              $getMenu->firstRow();
              $cp_name = $getMenu->getField('CP_NAME');
              $cp_link = $getMenu->getField('CP_LINK');
              $getMenuSub->selectMatrix(array("A.CONTRACTINGPROSESID" => $contractingprosesid, "JNS_KONTRAK" => $jnsKontrak), -1, -1, " OR ( A.CONTRACTINGPROSESID = '6' AND JNS_KONTRAK = '3' ) OR ( A.CONTRACTINGPROSESID = '".$contractingprosesid."' AND JNS_KONTRAK = '3' ) AND A.CM_STATUS='1'");  // JNS_KONTRAK 0:SPK, 1:PKS, 2:ALL

              $html .= '<div class="list-group">
                          <a class="btn-primary text-white list-group-item disabled" style="color:#fff !important; background-color:#000 !important; border-color:none !important;"> '.$cp_name.'</a>';
                           while($getMenuSub->nextRow())
                           {
                            if ($getMenuSub->getField('cm_link') == 'kontrak/index/'.$this->_CI->uri->segment(3, "") ||
                                ($getMenuSub->getField('cm_name_menu') == "SPPBJ" && $this->_CI->uri->segment(3, "") == 'contracting_persiapan_sppbj_edit')
                                ) {
                              $colorBg = '  background-color: #F7CA18 !important; color:#000 !important;';
                              $colorFotn = ' color:#000';
                            } else { $colorBg=""; $colorFotn ="";}

                            if ($getMenuSub->getField('cm_name_menu') == 'SPMK') { // 1:Berubah, 2:Tetap
                              if ($materialSifat == '1') {
                              // SPMK hanya untuk Material Barang Jasa berSifat Berubah
              $html .=      '
                                <a class="list-group-item" href="'.$getMenuSub->getField('cm_link').'?reqId='.$contractingrekananid.'" style="border-bottom: 1px solid #f5ebeb; cursor: pointer; '.$colorBg.'">
                                  <i class="fa fa-angle-double-right">
                                  <i style="height: 20px; font-size: 17px; '.$colorFotn.'"> '.$getMenuSub->getField('cm_name_menu') .' </i>
                                   <small style="font-size: 10px; display: block; font-weight: normal; margin:5px 0 0 12px; '.$colorFotn.'" class="hidden-md">'.$getMenuSub->getField('cm_name') .'</small>
                                  </i>
                                </a>
                                ';
                              }
                            } else {
              $html .=      '
                                <a class="list-group-item" href="'.$getMenuSub->getField('cm_link').'?reqId='.$contractingrekananid.'" style="border-bottom: 1px solid #f5ebeb; cursor: pointer; '.$colorBg.'">
                                  <i class="fa fa-angle-double-right">
                                  <i style="height: 20px; font-size: 17px; '.$colorFotn.'"> '.$getMenuSub->getField('cm_name_menu') .' </i>
                                   <small style="font-size: 10px; display: block; font-weight: normal; margin:5px 0 0 12px; '.$colorFotn.'" class="hidden-md">'.$getMenuSub->getField('cm_name') .'</small>
                                  </i>
                                </a>
                                ';
                            }

                          }
            } else
            {

              if ($contractingprosesid == '1') { 
                // $getMenu->selectMenuProses(array("A.CONTRACTINGPROSESID" => $contractingprosesid));
                $getMenu->selectMenuProses(array("A.CONTRACTINGPROSESID" => '1'));
                $getMenu->firstRow();
                $cp_name = $getMenu->getField('CP_NAME');
                $cp_link = $getMenu->getField('CP_LINK');
                // $getMenuSub->selectMatrix(array("A.CONTRACTINGPROSESID" => "1", "JNS_KONTRAK" => $jnsKontrak), -1, -1, " OR ( A.CONTRACTINGPROSESID = '6' AND JNS_KONTRAK = '2' ) OR ( A.CONTRACTINGPROSESID = '"."1"."' AND JNS_KONTRAK = '2' ) AND A.CM_STATUS='1'");  // JNS_KONTRAK 0:SPK, 1:PKS, 2:ALL
                $getMenuSub->selectMatrix(array("A.CONTRACTINGPROSESID" => "1", "JNS_KONTRAK" => $jnsKontrak), -1, -1, " AND A.CM_STATUS='1' OR ( A.CONTRACTINGPROSESID = '6' AND JNS_KONTRAK = '2' )");  // JNS_KONTRAK 0:SPK, 1:PKS, 2:ALL
                $html .= '<div class="list-group">
                            <a class="btn-primary text-white list-group-item disabled" style="color:#fff !important; background-color:#000 !important; border-color:none !important;"> '.$cp_name.'</a>';
                              
                              if ($jnsKontrak == '0')  // SPK Tidak pakai SPPBJ
                              { 
                               while($getMenuSub->nextRow())
                               {
                                if ($getMenuSub->getField('cm_name_menu') != 'SPPBJ')  // SPK Tidak pakai SPPBJ
                                { 
                                  if ($getMenuSub->getField('cm_link') == 'kontrak/index/'.$this->_CI->uri->segment(3, "") ||
                                      ($getMenuSub->getField('cm_name_menu') == "SPPBJ" && $this->_CI->uri->segment(3, "") == 'contracting_persiapan_sppbj_edit')
                                      ) {
                                    $colorBg = '  background-color: #F7CA18 !important; color:#000 !important;';
                                    $colorFotn = ' color:#000';
                                  } else { $colorBg=""; $colorFotn ="";}
                    $html .=      '
                                      <a class="list-group-item" href="'.$getMenuSub->getField('cm_link').'?reqId='.$contractingrekananid.'" style="border-bottom: 1px solid #f5ebeb; cursor: pointer; '.$colorBg.'">
                                        <i class="fa fa-angle-double-right">
                                        <i style="height: 20px; font-size: 17px; '.$colorFotn.'"> '.$getMenuSub->getField('cm_name_menu') .' </i>
                                         <small style="font-size: 10px; display: block; font-weight: normal; margin:5px 0 0 12px; '.$colorFotn.'" class="hidden-md">'.$getMenuSub->getField('cm_name') .'</small>
                                        </i>
                                      </a>
                                      '; 
                                }
                              }
                            } else {
                              while($getMenuSub->nextRow())
                              { 
                                if ($getMenuSub->getField('cm_link') == 'kontrak/index/'.$this->_CI->uri->segment(3, "") ||
                                    ($getMenuSub->getField('cm_name_menu') == "SPPBJ" && $this->_CI->uri->segment(3, "") == 'contracting_persiapan_sppbj_edit')
                                    ) {
                                  $colorBg = '  background-color: #F7CA18 !important; color:#000 !important;';
                                  $colorFotn = ' color:#000';
                                } else { $colorBg=""; $colorFotn ="";}
                  $html .=      '
                                    <a class="list-group-item" href="'.$getMenuSub->getField('cm_link').'?reqId='.$contractingrekananid.'" style="border-bottom: 1px solid #f5ebeb; cursor: pointer; '.$colorBg.'">
                                      <i class="fa fa-angle-double-right">
                                      <i style="height: 20px; font-size: 17px; '.$colorFotn.'"> '.$getMenuSub->getField('cm_name_menu') .' </i>
                                       <small style="font-size: 10px; display: block; font-weight: normal; margin:5px 0 0 12px; '.$colorFotn.'" class="hidden-md">'.$getMenuSub->getField('cm_name') .'</small>
                                      </i>
                                    </a>
                                    '; 
                              }
                            }
              } else 
              {
                $getMenu->selectMenuProses(array("A.CONTRACTINGPROSESID" => $contractingprosesid));
                $getMenu->firstRow();
                $cp_name = $getMenu->getField('CP_NAME');
                $cp_link = $getMenu->getField('CP_LINK');
                $getMenuSub->selectMatrix(array("A.CONTRACTINGPROSESID" => "$contractingprosesid", "JNS_KONTRAK" => $jnsKontrak), -1, -1, " OR ( A.CONTRACTINGPROSESID = '6' AND JNS_KONTRAK = '2' ) OR ( A.CONTRACTINGPROSESID = '3' AND JNS_KONTRAK = '2' ) AND A.CM_STATUS='1'");  // JNS_KONTRAK 0:SPK, 1:PKS, 2:ALL

                $html .= '<div class="list-group">
                            <a class="btn-primary text-white list-group-item disabled" style="color:#fff !important; background-color:#000 !important; border-color:none !important;"> '.$cp_name.'</a>';
                              
                              if ($jnsKontrak == '0')  // SPK Tidak pakai SPPBJ
                              { 
                               while($getMenuSub->nextRow())
                               {
                                if ($getMenuSub->getField('cm_name_menu') != 'SPPBJ')  // SPK Tidak pakai SPPBJ
                                { 
                                  if ($getMenuSub->getField('cm_name_menu') == 'Perubahan' || $getMenuSub->getField('cm_name_menu') == 'Dok. Kontrak & Rekam Jejak' || $getMenuSub->getField('cm_name_menu') == 'Penilaian Kinerja') 
                                  { 
                                    if ($getMenuSub->getField('cm_link') == 'kontrak/index/'.$this->_CI->uri->segment(3, "") ||
                                        ($getMenuSub->getField('cm_name_menu') == "SPPBJ" && $this->_CI->uri->segment(3, "") == 'contracting_persiapan_sppbj_edit')
                                        ) {
                                      $colorBg = '  background-color: #F7CA18 !important; color:#000 !important;';
                                      $colorFotn = ' color:#000';
                                    } else { $colorBg=""; $colorFotn ="";}
                    $html .=      '
                                      <a class="list-group-item" href="'.$getMenuSub->getField('cm_link').'?reqId='.$contractingrekananid.'" style="border-bottom: 1px solid #f5ebeb; cursor: pointer; '.$colorBg.'">
                                        <i class="fa fa-angle-double-right">
                                        <i style="height: 20px; font-size: 17px; '.$colorFotn.'"> '.$getMenuSub->getField('cm_name_menu') .' </i>
                                         <small style="font-size: 10px; display: block; font-weight: normal; margin:5px 0 0 12px; '.$colorFotn.'" class="hidden-md">'.$getMenuSub->getField('cm_name') .'</small>
                                        </i>
                                      </a>
                                      '; 
                                  }
                                }
                              }
                            } else {
                              while($getMenuSub->nextRow())
                              { 
                                if ($getMenuSub->getField('cm_link') == 'kontrak/index/'.$this->_CI->uri->segment(3, "") ||
                                    ($getMenuSub->getField('cm_name_menu') == "SPPBJ" && $this->_CI->uri->segment(3, "") == 'contracting_persiapan_sppbj_edit')
                                    ) {
                                  $colorBg = '  background-color: #F7CA18 !important; color:#000 !important;';
                                  $colorFotn = ' color:#000';
                                } else { $colorBg=""; $colorFotn ="";}
                                  if ($getMenuSub->getField('cm_name_menu') == 'Perubahan' || $getMenuSub->getField('cm_name_menu') == 'Pemutusan' || $getMenuSub->getField('cm_name_menu') == 'Penilaian Kinerja') 
                                  { 
                  $html .=      '
                                    <a class="list-group-item" href="'.$getMenuSub->getField('cm_link').'?reqId='.$contractingrekananid.'" style="border-bottom: 1px solid #f5ebeb; cursor: pointer; '.$colorBg.'">
                                      <i class="fa fa-angle-double-right">
                                      <i style="height: 20px; font-size: 17px; '.$colorFotn.'"> '.$getMenuSub->getField('cm_name_menu') .' </i>
                                       <small style="font-size: 10px; display: block; font-weight: normal; margin:5px 0 0 12px; '.$colorFotn.'" class="hidden-md">'.$getMenuSub->getField('cm_name') .'</small>
                                      </i>
                                    </a>
                                    '; 
                                  }
                              }
                            }
              }

            }
          } // if ($contractingprosesid == '6') { // Selesai
        }

        if ($this->_CI->LEVEL_KONTRAK == '2') { // Pengendali
          // echo "Menu Untuk Pengendali: proses 3 & jenis_kontrak:".$jnsKontrak;
          if ($contractingprosesid == '6') { // Selesai
            $getMenu->selectMenuProses(array("A.CONTRACTINGPROSESID" => '6'));
            $getMenu->firstRow();
            $cp_name = $getMenu->getField('CP_NAME');
            $cp_link = $getMenu->getField('CP_LINK');
            $getMenuSub->selectMatrix(array("A.CONTRACTINGPROSESID" => "6", "JNS_KONTRAK" => $jnsKontrak), -1, -1, " OR ( A.CONTRACTINGPROSESID = '6' AND JNS_KONTRAK = '2' ) OR ( A.CONTRACTINGPROSESID = '6' AND JNS_KONTRAK = '2' ) AND A.CM_STATUS='1'");  // JNS_KONTRAK 0:SPK, 1:PKS, 2:ALL

            $html .= '<div class="list-group">
                        <a class="btn-primary text-white list-group-item disabled" style="color:#fff !important; background-color:#000 !important; border-color:none !important;"> '.$cp_name.'</a>';
                         while($getMenuSub->nextRow())
                         {
                          if ($getMenuSub->getField('cm_link') == 'kontrak/index/'.$this->_CI->uri->segment(3, "") ||
                              ($getMenuSub->getField('cm_name_menu') == "SPPBJ" && $this->_CI->uri->segment(3, "") == 'contracting_persiapan_sppbj_edit')
                              ) {
                            $colorBg = '  background-color: #F7CA18 !important; color:#000 !important;';
                            $colorFotn = ' color:#000';
                          } else { $colorBg=""; $colorFotn ="";}
            $html .=      '
                              <a class="list-group-item" href="'.$getMenuSub->getField('cm_link').'?reqId='.$contractingrekananid.'" style="border-bottom: 1px solid #f5ebeb; cursor: pointer; '.$colorBg.'">
                                <i class="fa fa-angle-double-right">
                                <i style="height: 20px; font-size: 17px; '.$colorFotn.'"> '.$getMenuSub->getField('cm_name_menu') .' </i>
                                 <small style="font-size: 10px; display: block; font-weight: normal; margin:5px 0 0 12px; '.$colorFotn.'" class="hidden-md">'.$getMenuSub->getField('cm_name') .'</small>
                                </i>
                              </a>
                              ';
                        }
          } else {

            $getMenu->selectMenuProses(array("A.CONTRACTINGPROSESID" => '3'));
            $getMenu->firstRow();
            $cp_name = $getMenu->getField('CP_NAME');
            $cp_link = $getMenu->getField('CP_LINK');
            $getMenuSub->selectMatrix(array("A.CONTRACTINGPROSESID" => "3", "JNS_KONTRAK" => $jnsKontrak), -1, -1, " OR ( A.CONTRACTINGPROSESID = '6' AND JNS_KONTRAK = '2' ) OR ( A.CONTRACTINGPROSESID = '3' AND JNS_KONTRAK = '2' ) AND A.CM_STATUS='1'");  // JNS_KONTRAK 0:SPK, 1:PKS, 2:ALL

            $html .= '<div class="list-group">
                        <a class="btn-primary text-white list-group-item disabled" style="color:#fff !important; background-color:#000 !important; border-color:none !important;"> '.$cp_name.'</a>';
                         while($getMenuSub->nextRow())
                         {
                          if ($getMenuSub->getField('cm_link') == 'kontrak/index/'.$this->_CI->uri->segment(3, "") ||
                              ($getMenuSub->getField('cm_name_menu') == "SPPBJ" && $this->_CI->uri->segment(3, "") == 'contracting_persiapan_sppbj_edit')
                              ) {
                            $colorBg = '  background-color: #F7CA18 !important; color:#000 !important;';
                            $colorFotn = ' color:#000';
                          } else { $colorBg=""; $colorFotn ="";}
            $html .=      '
                              <a class="list-group-item" href="'.$getMenuSub->getField('cm_link').'?reqId='.$contractingrekananid.'" style="border-bottom: 1px solid #f5ebeb; cursor: pointer; '.$colorBg.'">
                                <i class="fa fa-angle-double-right">
                                <i style="height: 20px; font-size: 17px; '.$colorFotn.'"> '.$getMenuSub->getField('cm_name_menu') .' </i>
                                 <small style="font-size: 10px; display: block; font-weight: normal; margin:5px 0 0 12px; '.$colorFotn.'" class="hidden-md">'.$getMenuSub->getField('cm_name') .'</small>
                                </i>
                              </a>
                              ';
                        }
          }
        }

        if ($this->_CI->LEVEL_KONTRAK == '3') { // Penyelesai
          // echo "Menu Untuk Pengendali: proses 3 & jenis_kontrak:".$jnsKontrak;

          if ($contractingprosesid == '6') { // Selesai
            $getMenu->selectMenuProses(array("A.CONTRACTINGPROSESID" => '6'));
            $getMenu->firstRow();
            $cp_name = $getMenu->getField('CP_NAME');
            $cp_link = $getMenu->getField('CP_LINK');
            $getMenuSub->selectMatrix(array("A.CONTRACTINGPROSESID" => "6", "JNS_KONTRAK" => $jnsKontrak), -1, -1, " OR ( A.CONTRACTINGPROSESID = '6' AND JNS_KONTRAK = '2' ) OR ( A.CONTRACTINGPROSESID = '6' AND JNS_KONTRAK = '2' ) AND A.CM_STATUS='1'");  // JNS_KONTRAK 0:SPK, 1:PKS, 2:ALL

            $html .= '<div class="list-group">
                        <a class="btn-primary text-white list-group-item disabled" style="color:#fff !important; background-color:#000 !important; border-color:none !important;"> '.$cp_name.'</a>';
                         while($getMenuSub->nextRow())
                         {
                          if ($getMenuSub->getField('cm_link') == 'kontrak/index/'.$this->_CI->uri->segment(3, "") ||
                              ($getMenuSub->getField('cm_name_menu') == "SPPBJ" && $this->_CI->uri->segment(3, "") == 'contracting_persiapan_sppbj_edit')
                              ) {
                            $colorBg = '  background-color: #F7CA18 !important; color:#000 !important;';
                            $colorFotn = ' color:#000';
                          } else { $colorBg=""; $colorFotn ="";}
            $html .=      '
                              <a class="list-group-item" href="'.$getMenuSub->getField('cm_link').'?reqId='.$contractingrekananid.'" style="border-bottom: 1px solid #f5ebeb; cursor: pointer; '.$colorBg.'">
                                <i class="fa fa-angle-double-right">
                                <i style="height: 20px; font-size: 17px; '.$colorFotn.'"> '.$getMenuSub->getField('cm_name_menu') .' </i>
                                 <small style="font-size: 10px; display: block; font-weight: normal; margin:5px 0 0 12px; '.$colorFotn.'" class="hidden-md">'.$getMenuSub->getField('cm_name') .'</small>
                                </i>
                              </a>
                              ';
                        }
          } else {

            $getMenu->selectMenuProses(array("A.CONTRACTINGPROSESID" => '4'));
            $getMenu->firstRow();
            $cp_name = $getMenu->getField('CP_NAME');
            $cp_link = $getMenu->getField('CP_LINK');
            $getMenuSub->selectMatrix(array("A.CONTRACTINGPROSESID" => "4", "JNS_KONTRAK" => $jnsKontrak), -1, -1, " OR ( A.CONTRACTINGPROSESID = '6' AND JNS_KONTRAK = '2' ) OR ( A.CONTRACTINGPROSESID = '4' AND JNS_KONTRAK = '2' ) AND A.CM_STATUS='1'");  // JNS_KONTRAK 0:SPK, 1:PKS, 2:ALL

            $html .= '<div class="list-group">
                        <a class="btn-primary text-white list-group-item disabled" style="color:#fff !important; background-color:#000 !important; border-color:none !important;"> '.$cp_name.'</a>';
                         while($getMenuSub->nextRow())
                         {
                          if ($getMenuSub->getField('cm_link') == 'kontrak/index/'.$this->_CI->uri->segment(3, "") ||
                              ($getMenuSub->getField('cm_name_menu') == "SPPBJ" && $this->_CI->uri->segment(3, "") == 'contracting_persiapan_sppbj_edit')
                              ) {
                            $colorBg = '  background-color: #F7CA18 !important; color:#000 !important;';
                            $colorFotn = ' color:#000';
                          } else { $colorBg=""; $colorFotn ="";}
            $html .=      '
                              <a class="list-group-item" href="'.$getMenuSub->getField('cm_link').'?reqId='.$contractingrekananid.'" style="border-bottom: 1px solid #f5ebeb; cursor: pointer; '.$colorBg.'">
                                <i class="fa fa-angle-double-right">
                                <i style="height: 20px; font-size: 17px; '.$colorFotn.'"> '.$getMenuSub->getField('cm_name_menu') .' </i>
                                 <small style="font-size: 10px; display: block; font-weight: normal; margin:5px 0 0 12px; '.$colorFotn.'" class="hidden-md">'.$getMenuSub->getField('cm_name') .'</small>
                                </i>
                              </a>
                              ';
                        }
          }
        }
        break;

      case '20': // Kasubdit Kontrak 
      case '28': // PPK 
          // echo "Menu Untuk Kasubdit dan PPK Kontrak";
          if ($contractingprosesid == '6') 
          { // Selesai
              $getMenu->selectMenuProses(array("A.CONTRACTINGPROSESID" => '6'));
              $getMenu->firstRow();
              $cp_name = $getMenu->getField('CP_NAME');
              $cp_link = $getMenu->getField('CP_LINK');
              $getMenuSub->selectMatrix(array("A.CONTRACTINGPROSESID" => "6", "JNS_KONTRAK" => $jnsKontrak), -1, -1, " OR ( A.CONTRACTINGPROSESID = '6' AND JNS_KONTRAK = '2' ) OR ( A.CONTRACTINGPROSESID = '6' AND JNS_KONTRAK = '2' ) AND A.CM_STATUS='1'");  // JNS_KONTRAK 0:SPK, 1:PKS, 2:ALL

              $html .= '<div class="list-group">
                          <a class="btn-primary text-white list-group-item disabled" style="color:#fff !important; background-color:#000 !important; border-color:none !important;"> '.$cp_name.'</a>';
                           while($getMenuSub->nextRow())
                           {
                            if ($getMenuSub->getField('cm_link') == 'kontrak/index/'.$this->_CI->uri->segment(3, "") ||
                                ($getMenuSub->getField('cm_name_menu') == "SPPBJ" && $this->_CI->uri->segment(3, "") == 'contracting_persiapan_sppbj_edit')
                                ) {
                              $colorBg = '  background-color: #F7CA18 !important; color:#000 !important;';
                              $colorFotn = ' color:#000';
                            } else { $colorBg=""; $colorFotn ="";}
              $html .=      '
                                <a class="list-group-item" href="'.$getMenuSub->getField('cm_link').'?reqId='.$contractingrekananid.'" style="border-bottom: 1px solid #f5ebeb; cursor: pointer; '.$colorBg.'">
                                  <i class="fa fa-angle-double-right">
                                  <i style="height: 20px; font-size: 17px; '.$colorFotn.'"> '.$getMenuSub->getField('cm_name_menu') .' </i>
                                   <small style="font-size: 10px; display: block; font-weight: normal; margin:5px 0 0 12px; '.$colorFotn.'" class="hidden-md">'.$getMenuSub->getField('cm_name') .'</small>
                                  </i>
                                </a>
                                ';
                          }
          } else 
          {
            if ($contractingprosesid == '1') 
            { 

              $getMenu->selectMenuProses(array("A.CONTRACTINGPROSESID" => '1'));
              $getMenu->firstRow();
              $cp_name = $getMenu->getField('CP_NAME');
              $cp_link = $getMenu->getField('CP_LINK');

              $getMenuSub->selectMatrix(array("A.CONTRACTINGPROSESID" => "1", "JNS_KONTRAK" => $jnsKontrak), -1, -1, " AND A.CM_STATUS='1' OR ( A.CONTRACTINGPROSESID = '6' AND JNS_KONTRAK = '2' )");  // JNS_KONTRAK 0:SPK, 1:PKS, 2:ALL

              $html .= '<div class="list-group">
                          <a class="btn-primary text-white list-group-item disabled" style="color:#fff !important; background-color:#000 !important; border-color:none !important;"> '.$cp_name.'</a>';

                          if ($jnsKontrak == '0')  // SPK Tidak pakai SPPBJ
                          { 
                           while($getMenuSub->nextRow())
                           {
                            if ($getMenuSub->getField('cm_name_menu') != 'SPPBJ')  // SPK Tidak pakai SPPBJ
                            { 
                              if ($getMenuSub->getField('cm_link') == 'kontrak/index/'.$this->_CI->uri->segment(3, "") ||
                                  ($getMenuSub->getField('cm_name_menu') == "SPPBJ" && $this->_CI->uri->segment(3, "") == 'contracting_persiapan_sppbj_edit')
                                  ) {
                                $colorBg = '  background-color: #F7CA18 !important; color:#000 !important;';
                                $colorFotn = ' color:#000';
                              } else { $colorBg=""; $colorFotn ="";}
                $html .=      '
                                  <a class="list-group-item" href="'.$getMenuSub->getField('cm_link').'?reqId='.$contractingrekananid.'" style="border-bottom: 1px solid #f5ebeb; cursor: pointer; '.$colorBg.'">
                                    <i class="fa fa-angle-double-right">
                                    <i style="height: 20px; font-size: 17px; '.$colorFotn.'"> '.$getMenuSub->getField('cm_name_menu') .' </i>
                                     <small style="font-size: 10px; display: block; font-weight: normal; margin:5px 0 0 12px; '.$colorFotn.'" class="hidden-md">'.$getMenuSub->getField('cm_name') .'</small>
                                    </i>
                                  </a>
                                  ';
                              }
                            }
                         } else 
                         {
                          while($getMenuSub->nextRow())
                           { 
                              if ($getMenuSub->getField('cm_link') == 'kontrak/index/'.$this->_CI->uri->segment(3, "") ||
                                  ($getMenuSub->getField('cm_name_menu') == "SPPBJ" && $this->_CI->uri->segment(3, "") == 'contracting_persiapan_sppbj_edit')
                                  ) {
                                $colorBg = '  background-color: #F7CA18 !important; color:#000 !important;';
                                $colorFotn = ' color:#000';
                              } else { $colorBg=""; $colorFotn ="";}
                $html .=      '
                                  <a class="list-group-item" href="'.$getMenuSub->getField('cm_link').'?reqId='.$contractingrekananid.'" style="border-bottom: 1px solid #f5ebeb; cursor: pointer; '.$colorBg.'">
                                    <i class="fa fa-angle-double-right">
                                    <i style="height: 20px; font-size: 17px; '.$colorFotn.'"> '.$getMenuSub->getField('cm_name_menu') .' </i>
                                     <small style="font-size: 10px; display: block; font-weight: normal; margin:5px 0 0 12px; '.$colorFotn.'" class="hidden-md">'.$getMenuSub->getField('cm_name') .'</small>
                                    </i>
                                  </a>
                                  ';
                            }
                         }
            } else {

              $getMenu->selectMenuProses(array("A.CONTRACTINGPROSESID" => '3'));
              $getMenu->firstRow();
              $cp_name = $getMenu->getField('CP_NAME');
              $cp_link = $getMenu->getField('CP_LINK');
              $getMenuSub->selectMatrix(array("A.CONTRACTINGPROSESID" => "3", "JNS_KONTRAK" => $jnsKontrak), -1, -1, " OR ( A.CONTRACTINGPROSESID = '6' AND JNS_KONTRAK = '2' ) OR ( A.CONTRACTINGPROSESID = '3' AND JNS_KONTRAK = '2' ) AND A.CM_STATUS='1'");  // JNS_KONTRAK 0:SPK, 1:PKS, 2:ALL

              $html .= '<div class="list-group">
                          <a class="btn-primary text-white list-group-item disabled" style="color:#fff !important; background-color:#000 !important; border-color:none !important;"> '.$cp_name.'</a>';
                           while($getMenuSub->nextRow())
                           {
                            if ($getMenuSub->getField('cm_link') == 'kontrak/index/'.$this->_CI->uri->segment(3, "") ||
                                ($getMenuSub->getField('cm_name_menu') == "SPPBJ" && $this->_CI->uri->segment(3, "") == 'contracting_persiapan_sppbj_edit')
                                ) {
                              $colorBg = '  background-color: #F7CA18 !important; color:#000 !important;';
                              $colorFotn = ' color:#000';
                            } else { $colorBg=""; $colorFotn ="";}
              $html .=      '
                                <a class="list-group-item" href="'.$getMenuSub->getField('cm_link').'?reqId='.$contractingrekananid.'" style="border-bottom: 1px solid #f5ebeb; cursor: pointer; '.$colorBg.'">
                                  <i class="fa fa-angle-double-right">
                                  <i style="height: 20px; font-size: 17px; '.$colorFotn.'"> '.$getMenuSub->getField('cm_name_menu') .' </i>
                                   <small style="font-size: 10px; display: block; font-weight: normal; margin:5px 0 0 12px; '.$colorFotn.'" class="hidden-md">'.$getMenuSub->getField('cm_name') .'</small>
                                  </i>
                                </a>
                                ';
                          }
            }
          }
        break;

      // case '28': // PPK 
      //     echo "Menu Untuk PPK";
      //   break;
      
      default:
        // code...
        break;
    }


    // echo $this->_CI->USER_TYPE_ID.'--'.$this->_CI->LEVEL_KONTRAK; die;

    // Ketika proses di flow 3 (palaksanaan kontrak) maka flow 4 bisa buka flow 3
    if ($reqProses == '4') { // di Hardcode  $reqProses untuk link di flow 4 ajah

      if ($this->_CI->LEVEL_KONTRAK != '' && $this->_CI->LEVEL_KONTRAK != '2') {
        redirect('/kontrak/index/contracting_persiapan');
      }

      if ($jnsKontrak == '3') { // Surat Pesanan
        $getMenu->selectMenuProses(array("A.CONTRACTINGPROSESID" => '4'));
        $getMenu->firstRow();
        $cp_name = $getMenu->getField('CP_NAME');
        $cp_link = $getMenu->getField('CP_LINK');
        $getMenuSub->selectMatrix(array("A.CONTRACTINGPROSESID" => '4', "JNS_KONTRAK" => $jnsKontrak), -1, -1, " OR ( A.CONTRACTINGPROSESID = '6' AND JNS_KONTRAK = '3') AND A.CM_STATUS='1'");  // JNS_KONTRAK 0:SPK, 1:PKS, 2:ALL

        $html .= '<div class="list-group">
                    <a class="btn-primary text-white list-group-item disabled" style="color:#fff !important; background-color:#000 !important; border-color:none !important;"> '.$cp_name.'</a>';
                     while($getMenuSub->nextRow())
                     {
                      if ($getMenuSub->getField('cm_link') == 'kontrak/index/'.$this->_CI->uri->segment(3, "") ||
                          ($getMenuSub->getField('cm_name_menu') == "SPPBJ" && $this->_CI->uri->segment(3, "") == 'contracting_persiapan_sppbj_edit')
                          ) {
                        $colorBg = '  background-color: #F7CA18 !important; color:#000 !important;';
                        $colorFotn = ' color:#000';
                      } else { $colorBg=""; $colorFotn ="";}
        $html .=      '
                          <a class="list-group-item" href="'.$getMenuSub->getField('cm_link').'?reqId='.$contractingrekananid.'" style="border-bottom: 1px solid #f5ebeb; cursor: pointer; '.$colorBg.'">
                            <i class="fa fa-angle-double-right">
                            <i style="height: 20px; font-size: 17px; '.$colorFotn.'"> '.$getMenuSub->getField('cm_name_menu') .' </i>
                             <small style="font-size: 10px; display: block; font-weight: normal; margin:5px 0 0 12px; '.$colorFotn.'" class="hidden-md">'.$getMenuSub->getField('cm_name') .'</small>
                            </i>
                          </a>
                          ';
                    }
      } else
      {
        $getMenu->selectMenuProses(array("A.CONTRACTINGPROSESID" => '4'));
        $getMenu->firstRow();
        $cp_name = $getMenu->getField('CP_NAME');
        $cp_link = $getMenu->getField('CP_LINK');
        // $getMenuSub->selectMatrix(array("A.CONTRACTINGPROSESID" => '4', "JNS_KONTRAK" => $jnsKontrak), -1, -1, " OR ( A.CONTRACTINGPROSESID = '6' AND JNS_KONTRAK = '2' ) ");  // JNS_KONTRAK 0:SPK, 1:PKS, 2:ALL
        $getMenuSub->selectMatrix(array("A.CONTRACTINGPROSESID" => '4', "JNS_KONTRAK" => $jnsKontrak), -1, -1, " OR ( A.CONTRACTINGPROSESID = '6' AND JNS_KONTRAK = '2' ) OR ( A.CONTRACTINGPROSESID = '4' AND JNS_KONTRAK = '2' ) AND A.CM_STATUS='1'");  // JNS_KONTRAK 0:SPK, 1:PKS, 2:ALL

        $html .= '<div class="list-group">
                    <a class="btn-primary text-white list-group-item disabled" style="color:#fff !important; background-color:#000 !important; border-color:none !important;"> '.$cp_name.'</a>';
                     while($getMenuSub->nextRow())
                     {
                      if ($getMenuSub->getField('cm_link') == 'kontrak/index/'.$this->_CI->uri->segment(3, "") ||
                          ($getMenuSub->getField('cm_name_menu') == "SPPBJ" && $this->_CI->uri->segment(3, "") == 'contracting_persiapan_sppbj_edit')
                          ) {
                        $colorBg = '  background-color: #F7CA18 !important; color:#000 !important;';
                        $colorFotn = ' color:#000';
                      } else { $colorBg=""; $colorFotn ="";}
        $html .=      '
                        <a class="list-group-item" href="'.$getMenuSub->getField('cm_link').'?reqId='.$contractingrekananid.'" style="border-bottom: 1px solid #f5ebeb; cursor: pointer; '.$colorBg.'">
                          <i class="fa fa-angle-double-right">
                          <i style="height: 20px; font-size: 17px; '.$colorFotn.'"> '.$getMenuSub->getField('cm_name_menu') .' </i>
                            <small style="font-size: 10px; display: block; font-weight: normal; margin:5px 0 0 12px; '.$colorFotn.'" class="hidden-md">'.$getMenuSub->getField('cm_name') .'</small>
                          </i>
                        </a> ';
                    }
      }
    } else
    {
      
    }
    // $html .=      '<li>
    //                 <div style="border-bottom: 1px solid #f5ebeb; cursor: pointer; '.$colorBg.'">
    //                   <a href="kontrak/index/contracting_persiapan_file?reqId='.$contractingrekananid.'">
    //                     <i class="fa fa-angle-double-right">
    //                     <i style="height: 20px; font-size: 17px; '.$colorFotn.'"> Upload File </i>
    //                      <small style="font-size: 10px; display: block; font-weight: normal; margin:5px 0 0 12px; '.$colorFotn.'" class="hidden-md">'.ucfirst(strtolower($cp_name)).' </small>
    //                     </i>
    //                   </a>
    //                 </div>
    //               </li>';
    $html .=  '</div>';

    return $html;
  }

  function getMenuPenyedia($contractingrekananid)
  {
    $this->_CI->load->model("Contractingrekanan");
    $contractingrekanan = new Contractingrekanan();
    $proses4 = new Contractingrekanan();

    $contractingrekanan->selectByParams(array("A.CONTRACTINGREKANANID" => $contractingrekananid));
    $contractingrekanan->firstRow();
    $reqjnsKontrak = $contractingrekanan->getField('JNS_KONTRAK');

    $proses4->selectProses4(array("A.CONTRACTINGREKANANID" => $contractingrekananid));
    $proses4->firstRow();
    $reqPerubahan = $proses4->getField('CR_PERUBAHAN');
    $reqPerubahanAlasan = $proses4->getField('CR_PERUBAHAN_ALASAN');
    $reqPenyesuaian = $proses4->getField('CR_PENYESUAIAN');
    $reqPenyesuaianAlasan = $proses4->getField('CR_PENYESUAIAN_ALASAN');
    $reqKahar = $proses4->getField('CR_KAHAR');
    $reqKaharAlasan = $proses4->getField('CR_KAHAR_ALASAN');
    $reqBerakhir = $proses4->getField('CR_BERAKHIR');
    $reqBerakhirAlasan = $proses4->getField('CR_BERAKHIR_ALASAN');
    $reqPemutusan = $proses4->getField('CR_PEMUTUSAN');
    $reqPemutusanAlasan = $proses4->getField('CR_PEMUTUSAN_ALASAN');
    $reqKesempatan = $proses4->getField('CR_KESEMPATAN');
    $reqKesempatanAlasan = $proses4->getField('CR_KESEMPATAN_ALASAN');
    $reqDenda = $proses4->getField('CR_DENDA');
    $reqDendaAlasan = $proses4->getField('CR_DENDA_ALASAN');

    if ($reqjnsKontrak == '3') { // Surat Pesanan
      // $jnsKontrak = 'Payung';
      $jnsKontrak = 'Surat Pesanan';
      $arrayLink1 = array(
                          'kontrak/index/contracting_penyedia_sppbj_multi',
                          'kontrak/index/contracting_penyedia_perjanjian_multi',
                          'kontrak/index/contracting_penyedia_spmk_multi',
                          'kontrak/index/contracting_penyedia_surat_pesanan_multi',
                          // 'kontrak/index/contracting_penyedia_realisasi_multi',
                          'kontrak/index/contracting_penyedia_termin_multi',
                          // 'kontrak/index/contracting_penyedia_termin_multi',
                         );

      $arrayMenu1 = array(
                          'SPPBJ' => 'Penetapan Surat Penunjukan Penyedia Barang/Jasa',
                          'Data Surat Pesanan' => 'Perancangan Kontrak',
                          'SPMK' => 'Surat Perintah Mulai Kerja',
                          'Surat Pesanan' => 'Kontrak',
                          // 'Realisasi Pekerjaan' => 'Kontrak',
                          'Tagihan' => 'Kontrak',
                          // 'Tagihan' => 'Kontrak',
                        );

    } else if ($reqjnsKontrak == '1') { // Surat Perjanjian
      $jnsKontrak = 'Surat Perjanjian';
      $arrayLink1 = array(
                          'kontrak/index/contracting_penyedia_sppbj',
                          'kontrak/index/contracting_penyedia_perjanjian',
                          'kontrak/index/contracting_penyedia_realisasi',
                          'kontrak/index/contracting_penyedia_termin',
                         );

      $arrayMenu1 = array(
                          'SPPBJ' => 'Penetapan Surat Penunjukan Penyedia Barang/Jasa',
                          'Data Perjanjian' => 'Perancangan Kontrak',
                          'Realisasi Pekerjaan' => 'Kontrak',
                          'Tagihan' => 'Kontrak',
                        );

    } else if ($reqjnsKontrak == '0') { // SPK
      $arrayLink1 = array(
                          // 'kontrak/index/contracting_penyedia_sppbj',
                          'kontrak/index/contracting_penyedia_perjanjian',
                          'kontrak/index/contracting_penyedia_realisasi',
                          'kontrak/index/contracting_penyedia_termin',
                         );

      $arrayMenu1 = array(
                          // 'SPPBJ' => 'Penetapan Surat Penunjukan Penyedia Barang/Jasa',
                          'Data SPK' => 'Perancangan Kontrak',
                          'Realisasi Pekerjaan' => 'Kontrak',
                          'Tagihan' => 'Kontrak',
                        );
    }

    if ($reqjnsKontrak == '3') { // Surat Pesanan
      if ($reqPerubahan == '1') {
      $arrayLink2 = array('kontrak/index/contracting_penyedia_perubahan_multi');
      $arrayMenu2 = array('Perubahan' => 'Kontrak');
      } else { $arrayLink2 = array(); $arrayMenu2 = array(); }
    } else {
      if ($reqPerubahan == '1') {
      $arrayLink2 = array('kontrak/index/contracting_penyedia_perubahan');
      $arrayMenu2 = array('Perubahan' => 'Kontrak');
      } else { $arrayLink2 = array(); $arrayMenu2 = array(); }
    }

    if ($reqPenyesuaian == '1') {
    $arrayLink3 = array('kontrak/index/contracting_penyedia_harga');
    $arrayMenu3 = array('Penyesuaian Harga' => 'Kontrak');
    } else { $arrayLink3 = array(); $arrayMenu3 = array(); }

    if ($reqKahar == '1') {
    $arrayLink4 = array('kontrak/index/contracting_penyedia_kahar');
    $arrayMenu4 = array('Keadaan Kahar' => 'Kontrak');
    } else { $arrayLink4 = array(); $arrayMenu4 = array(); }

    if ($reqBerakhir == '1') {
    $arrayLink5 = array('kontrak/index/contracting_penyedia_berakhir');
    $arrayMenu5 = array('Berakhir' => 'Kontrak');
    } else { $arrayLink5 = array(); $arrayMenu5 = array(); }

    if ($reqPemutusan == '1') {
    $arrayLink6 = array('kontrak/index/contracting_penyedia_pemutusan');
    $arrayMenu6 = array('Pemutusan' => 'Kontrak');
    } else { $arrayLink6 = array(); $arrayMenu6 = array(); }

    if ($reqKesempatan == '1') {
    $arrayLink7 = array('kontrak/index/contracting_penyedia_kesempatan');
    $arrayMenu7 = array('Pemberian Kesempatan' => 'Kontrak');
    } else { $arrayLink7 = array(); $arrayMenu7 = array(); }

    if ($reqDenda == '1') {
    $arrayLink8 = array('kontrak/index/contracting_penyedia_denda');
    $arrayMenu8 = array('Sanksi dan Denda' => 'Kontrak');
    } else {
      $arrayLink8 = array(); $arrayMenu8 = array();
    }

    if ($reqjnsKontrak == '3') { // Surat Pesanan
      $arrayLink10 = array('kontrak/index/contracting_multi_penyedia_dokumen',);
      $arrayMenu10 = array('Dokumen Pendukung' => 'file pendukung lainnya');
    } else {
      $arrayLink10 = array('kontrak/index/contracting_penyedia_dokumen',);
      $arrayMenu10 = array('Dokumen Pendukung' => 'file pendukung lainnya');
    }

    $arrayLink = array_merge($arrayLink1,$arrayLink2,$arrayLink3,$arrayLink4,$arrayLink5,$arrayLink6,$arrayLink7,$arrayLink8,$arrayLink10);
    $arrayMenu = array_merge($arrayMenu1,$arrayMenu2,$arrayMenu3,$arrayMenu4,$arrayMenu5,$arrayMenu6,$arrayMenu7,$arrayMenu8,$arrayMenu10);


    $html  = '<style type="text/css">
      a.list-group-item { color: #000 !important; }
      .list-group-item { padding: 0.7rem 1.25rem !important; }
    </style>';
    $html .= '<div class="list-group">
               <a class="text-white list-group-item disabled" style="color:#fff !important; background-color:#000 !important"> Kontrak Detail</a>';
                 $no=0;
                 foreach ($arrayMenu as $key => $value) {
                  if ($arrayLink[$no] == 'kontrak/index/'.$this->_CI->uri->segment(3, ""))
                  {
                    $colorBg = '  background-color: #F7CA18 !important; color:#000 !important;';
                    $colorFotn = ' color:#000';
                  } else { $colorBg=""; $colorFotn ="";}
    $html .=      '
                      <a class="list-group-item" href="'.$arrayLink[$no].'?reqId='.$contractingrekananid.'"  style="border-bottom: 1px solid #f5ebeb; cursor: pointer; '.$colorBg.'">
                        <i class="fa fa-angle-double-right">
                        <i style="height: 20px; font-size: 17px; '.$colorFotn.'"> '.$key.' </i>
                         <small style="font-size: 10px; display: block; font-weight: normal; margin:5px 0 0 12px; '.$colorFotn.'" class="hidden-md">'.$value.'</small>
                        </i>
                      </a>
                    ';
                 $no++;
                }

    $html .=  '</div>';

    return $html;
  }

  function getMenuLegal($contractingrekananid)
  {
    $this->_CI->load->model("Contractingrekanan");
    $contractingrekanan = new Contractingrekanan();
    $proses4 = new Contractingrekanan();

    $contractingrekanan->selectByParams(array("A.CONTRACTINGREKANANID" => $contractingrekananid));
    $contractingrekanan->firstRow();
    $reqjnsKontrak = $contractingrekanan->getField('JNS_KONTRAK');

    $proses4->selectProses4(array("A.CONTRACTINGREKANANID" => $contractingrekananid));
    $proses4->firstRow();
    $reqPerubahan = $proses4->getField('CR_PERUBAHAN');
    $reqPerubahanAlasan = $proses4->getField('CR_PERUBAHAN_ALASAN');
    $reqPenyesuaian = $proses4->getField('CR_PENYESUAIAN');
    $reqPenyesuaianAlasan = $proses4->getField('CR_PENYESUAIAN_ALASAN');
    $reqKahar = $proses4->getField('CR_KAHAR');
    $reqKaharAlasan = $proses4->getField('CR_KAHAR_ALASAN');
    $reqBerakhir = $proses4->getField('CR_BERAKHIR');
    $reqBerakhirAlasan = $proses4->getField('CR_BERAKHIR_ALASAN');
    $reqPemutusan = $proses4->getField('CR_PEMUTUSAN');
    $reqPemutusanAlasan = $proses4->getField('CR_PEMUTUSAN_ALASAN');
    $reqKesempatan = $proses4->getField('CR_KESEMPATAN');
    $reqKesempatanAlasan = $proses4->getField('CR_KESEMPATAN_ALASAN');
    $reqDenda = $proses4->getField('CR_DENDA');
    $reqDendaAlasan = $proses4->getField('CR_DENDA_ALASAN');


    if ($reqjnsKontrak == '3') { // Surat Pesanan
      // $jnsKontrak = 'Surat Perjanjian';
      $jnsKontrak = 'Surat Pesanan';
      $arrayLink1 = array(
                          'kontrak/index/contracting_legal_file',
                          'kontrak/index/contracting_legal_sppbj',
                          'kontrak/index/contracting_legal_perjanjian',
                          'kontrak/index/contracting_legal_penyedia_surat_pesanan_multi',
                          'kontrak/index/contracting_legal_realisasi',
                          'kontrak/index/contracting_legal_termin',
                         );

      $arrayMenu1 = array(
                          'Dokumen Pemilihan' => 'dokumen-dokumen hasil sourcing',
                          'SPPBJ' => 'Penetapan Surat Penunjukan Penyedia Barang/Jasa',
                          'Data Surat Pesanan' => 'Perancangan Kontrak',
                          'Surat Pesanan' => 'Kontrak',
                          'Realisasi Pekerjaan' => 'Kontrak',
                          'Tagihan' => 'Kontrak',
                        );

    } else if ($reqjnsKontrak == '1') {
      $jnsKontrak = 'Surat Perjanjian';
      $arrayLink1 = array(
                          'kontrak/index/contracting_legal_file',
                          'kontrak/index/contracting_legal_sppbj',
                          'kontrak/index/contracting_legal_perjanjian',
                          'kontrak/index/contracting_legal_realisasi',
                          'kontrak/index/contracting_legal_termin',
                         );

      $arrayMenu1 = array(
                          'Dokumen Pemilihan' => 'dokumen-dokumen hasil sourcing',
                          'SPPBJ' => 'Penetapan Surat Penunjukan Penyedia Barang/Jasa',
                          'Data Perjanjian' => 'Perancangan Kontrak',
                          'Realisasi Pekerjaan' => 'Kontrak',
                          'Tagihan' => 'Kontrak',
                        );

    } else {
      $arrayLink1 = array(
                          'kontrak/index/contracting_legal_file',
                          'kontrak/index/contracting_legal_perjanjian',
                          'kontrak/index/contracting_legal_realisasi',
                          'kontrak/index/contracting_legal_termin',
                         );

      $arrayMenu1 = array(
                          'Dokumen Pemilihan' => 'dokumen-dokumen hasil sourcing',
                          'Data SPK' => 'Perancangan Kontrak',
                          'Realisasi Pekerjaan' => 'Kontrak',
                          'Tagihan' => 'Kontrak',
                        );
    }

    if ($reqPerubahan == '1') {
    $arrayLink2 = array('kontrak/index/contracting_legal_perubahan');
    $arrayMenu2 = array('Perubahan' => 'Kontrak');
    } else { $arrayLink2 = array(); $arrayMenu2 = array(); }

    if ($reqPenyesuaian == '1') {
    $arrayLink3 = array('kontrak/index/contracting_legal_harga');
    $arrayMenu3 = array('Penyesuaian Harga' => 'Kontrak');
    } else { $arrayLink3 = array(); $arrayMenu3 = array(); }

    if ($reqKahar == '1') {
    $arrayLink4 = array('kontrak/index/contracting_legal_kahar');
    $arrayMenu4 = array('Keadaan Kahar' => 'Kontrak');
    } else { $arrayLink4 = array(); $arrayMenu4 = array(); }

    if ($reqBerakhir == '1') {
    $arrayLink5 = array('kontrak/index/contracting_legal_berakhir');
    $arrayMenu5 = array('Berakhir' => 'Kontrak');
    } else { $arrayLink5 = array(); $arrayMenu5 = array(); }

    if ($reqPemutusan == '1') {
    $arrayLink6 = array('kontrak/index/contracting_legal_pemutusan');
    $arrayMenu6 = array('Pemutusan' => 'Kontrak');
    } else { $arrayLink6 = array(); $arrayMenu6 = array(); }

    if ($reqKesempatan == '1') {
    $arrayLink7 = array('kontrak/index/contracting_legal_kesempatan');
    $arrayMenu7 = array('Pemberian Kesempatan' => 'Kontrak');
    } else { $arrayLink7 = array(); $arrayMenu7 = array(); }

    if ($reqDenda == '1') {
    $arrayLink8 = array('kontrak/index/contracting_legal_denda');
    $arrayMenu8 = array('Sanksi dan Denda' => 'Kontrak');
    } else { $arrayLink8 = array(); $arrayMenu8 = array(); }

    $arrayLink10 = array('kontrak/index/contracting_legal_dokumen',);
    $arrayMenu10 = array('Dokumen Pendukung' => 'file pendukung lainnya');

    $arrayLink = array_merge($arrayLink1,$arrayLink2,$arrayLink3,$arrayLink4,$arrayLink5,$arrayLink6,$arrayLink7,$arrayLink8,$arrayLink10);
    $arrayMenu = array_merge($arrayMenu1,$arrayMenu2,$arrayMenu3,$arrayMenu4,$arrayMenu5,$arrayMenu6,$arrayMenu7,$arrayMenu8,$arrayMenu10);


    $html  = '<style type="text/css">
      a.list-group-item { color: #000 !important; }
      .list-group-item { padding: 0.7rem 1.25rem !important; }
    </style>';
    $html .= '<div class="list-group">
                <a class="btn-primary text-white list-group-item disabled" style="color:#fff !important; background-color:#000 !important; border-color:none !important;"> Kontrak Detail</a>';

                 $no=0;
                 foreach ($arrayMenu as $key => $value) {
                  if ($arrayLink[$no] == 'kontrak/index/'.$this->_CI->uri->segment(3, ""))
                  {
                    $colorBg = '  background-color: #F7CA18 !important; color:#000 !important;';
                    $colorFotn = ' color:#000';
                  } else { $colorBg=""; $colorFotn ="";}
    $html .=      '
                      <a class="list-group-item" href="'.$arrayLink[$no].'?reqId='.$contractingrekananid.'"  style="border-bottom: 1px solid #f5ebeb; cursor: pointer; '.$colorBg.'">
                        <i class="fa fa-angle-double-right">
                        <i style="height: 20px; font-size: 17px; '.$colorFotn.'"> '.$key.' </i>
                         <small style="font-size: 10px; display: block; font-weight: normal; margin:5px 0 0 12px; '.$colorFotn.'" class="hidden-md">'.$value.'</small>
                        </i>
                      </a>
                      ';
                 $no++;
                }

    $html .=  '</div>';

    return $html;
  }

  function getInfoPaket($reqId)
  {
    $this->_CI->load->model("Paket");
    $paket = new Paket();

    $paket->selectByParamsMonitoring(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
    $paket->firstRow();

    $html  = '';
    $html .=  '<div class="alert alert-icon-left alert-arrow-left alert-info mb-2" role="alert">
                <span class="alert-icon"><i class="fa fa-info"></i></span>
                <h4 style="color: #000; font-weight: bold">'.$paket->getField("NAMA").'</h4>
                </div>
                <table class="table table-bordered table-hover">
                  <tbody>
                    <tr>
                      <td width="25%" colspan="2">
                        <small><i class="fa fa-calendar"></i> Tahun Anggaran</small> <br>
                        '.getYear($paket->getField("TAHUN_ANGGARAN")).'
                      </td>
                      <td width="25%" colspan="2">
                        <small><i class="fa fa-map-marker"></i> Lokasi Pekerjaan</small> <br>
                        '.$paket->getField("LOKASI").'
                      </td>
                    </tr>
                    <tr>
                      <td width="25%" colspan="2">
                        <small><i class="fa fa-inbox"></i> Jenis Pengadaan</small> <br>
                        '.$paket->getField("PAKET_JENIS").'
                      </td>
                      <td width="25%" colspan="2">
                        <small><i class="fa fa-tag"></i> Metode Pengadaan</small> <br>
                        '.$paket->getField("METODE_LELANG").'
                      </td>
                    </tr>';
                    if ($paket_metode_lelang_id != '6')
                    {
      $html .=      '<tr>
                      <td width="25%" colspan="2">
                        <small><i class="fa fa-folder-open"></i> Metode Penyampaian Penawaran</small> <br>
                        '.$paket->getField("SISTEM_SAMPUL").' File
                      </td>
                      <td width="25%" colspan="2">
                        <small><i class="fa fa-exchange"></i> Metode Evaluasi</small> <br>
                        '.$paket->getField("METODE_EVALUASI").'
                      </td>
                    </tr>';
                    }

                    if ($paket_metode_lelang_id != '6')
                    {
      $html .=      '<tr>
                      <td width="25%" colspan="2">
                        <small><i class="fa fa-file-text"></i> Kualifikasi Usaha</small> <br>
                        '.$paket->getField("REKANAN_KUALIFIKASI").'
                      </td>
                      <td width="25%" colspan="2">
                        <small><i class="fa fa-clock-o"></i> Sistem Negosiasi</small> <br>';

                        if ($paket->getField("BIDDING") == 1) {
                          $html .= 'e-Reverse Auction '.$paket->getField("BIDDING_MENIT").' menit';
                        } else {
                          $html .= "Chatting Nego";
                        }
      $html .=     '</tr>';
                    }
      $html .=     '<tr>
                      <td width="25%" colspan="4">
                        <small><i class="fa fa-money"></i> Perkiraan Nilai Pekerjaan</small> <br>
                        '.$paket->getField("NILAI_MATA_UANG").' '.currencyToPage($paket->getField("NILAI")).'
                      </td>
                      </td>
                    </tr>';

                    if ($paket_metode_lelang_id != '6')
                    { // bukan Purchasing/Pembelian Langsung .'
      $html .=      '<tr>
                      <td width="25%" colspan="4">
                        <small><i class="fa fa-suitcase"></i> Bidang / Sub Bidang</small><br>';
                        if(trim($paket->getField("BIDANG_USAHA2")) == "()")
                            $html .= "-";
                           else
                            $html .= str_replace("---"," <br/> ", $paket->getField("BIDANG_USAHA2"));
      $html .=     ' </td>
                    </tr>
                    <tr>
                      <td width="25%" colspan="4">
                        <small><i class="fa fa-th-list"></i> Persyaratan Peserta</small><br>
                        '.$paket->getField("URAIAN").'
                      </td>
                    </tr>';
                    }
      $html .=   '</tbody>
                </table>';

    return $html;
  }

  function getInfoSPPBJ($reqId)
  {
    include_once("functions/string.func.php");
    include_once("functions/date.func.php");
    include_once("functions/default.func.php");

    $this->_CI->load->model(array("Paket","Contracting","Contractingrekanan","Rekanan","Contractingjaminan"));
    $paket = new Paket();
    $kontrak = new Contracting();
    $sppbj = new Contractingrekanan();
    $rekanan = new Rekanan();

    $kontrak->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
    $kontrak->firstRow();
    $kontrak_nama = $kontrak->getField('NAMA');
    $kontrak_nilai = $kontrak->getField('NILAI');
    $kontrak_paket_metode_lelang = $kontrak->getField('PAKET_METODE_LELANG');
    $paket_pemenang = $kontrak->getField('PEMENANG');
    $paket_id = $kontrak->getField('PAKET_ID');

    $sppbj->selectProses1(array("A.CONTRACTINGREKANANID" => $reqId));
    $sppbj->firstRow();

    $reqPaketId = $sppbj->getField('PAKET_ID') ?: '-';
    $reqContractingRekananId = $sppbj->getField('CONTRACTINGREKANANID') ?: '-';
    $reqCode = $sppbj->getField('CR_SPPBJ_CODE') ?: '-';
    $reqTanggal = $sppbj->getField('CR_SPPBJ_TANGGAL') ?: '-';
    $reqDirut = $sppbj->getField('CR_SPPBJ_DIRUT') ?: '-';
    $reqDirutAlamat = $sppbj->getField('CR_SPPBJ_DIRUT_ALAMAT') ?: '-';
    $reqDirutKota = $sppbj->getField('CR_SPPBJ_DIRUT_KOTA') ?: '-';
    $reqDirutJabatan = $sppbj->getField('CR_SPPBJ_DIRUT_JABATAN') ?: '-';
    $reqJaminanPelaksanaan = $sppbj->getField('CR_SPPBJ_JAMINAN_PELAKSANA') ?: '-';
    $reqJaminanBesar = $sppbj->getField('CR_SPPBJ_JAMINAN_BESAR') ?: '-';
    $reqJaminanJangkaDari = $sppbj->getField('CR_SPPBJ_JAMINAN_JANGKA_DARI') ?: '-';
    $reqJaminanJangkaSampai = $sppbj->getField('CR_SPPBJ_JAMINAN_JANGKA_SAMPAI') ?: '-';
    $reqJangkaMaksimal = $sppbj->getField('CR_SPPBJ_JAMINAN_MAKSIMAL_PENYERAHAN') ?: '-';
    $reqJaminanNilai = $sppbj->getField('CR_SPPBJ_JAMINAN_NILAI') ?: '-';
    $reqPejabatBerwenang = $sppbj->getField('CR_SPPBJ_PEJABAT_BERWENANG') ?: '-';
    $reqNIP = $sppbj->getField('CR_SPPBJ_NIP') ?: '-';
    $reqJabatan = $sppbj->getField('CR_SPPBJ_JABATAN') ?: '-';
    $reqPPN = $sppbj->getField('CR_SPPBJ_PPN') ?: '-';
    $reqPelaksanaanDari = $sppbj->getField('CR_SPPBJ_PELAKSANAAN_DARI') ?: '-';
    $reqPelaksanaanSampai = $sppbj->getField('CR_SPPBJ_PELAKSANAAN_SAMPAI') ?: '-';
    $reqCreatedBy = $sppbj->getField('CR_SPPBJ_CREATED_BY') ?: '-';
    $reqCreatedDate = $sppbj->getField('CR_SPPBJ_CREATED_DATE') ?: '-';
    $reqNilai = $sppbj->getField('CR_SPPBJ_NILAI') ?: 0;
    $reqContractingStatusKontrakId = $sppbj->getField('CONTRACTINGSTATUSKONTRAKID') ?: '0'; 
    $reqSA = $sppbj->getField('SA') ?: ''; 
    $reqDPSJ = $sppbj->getField('DPSJ') ?: ''; 
    $reqListKegiatan = $sppbj->getField('LIST_KEGIATAN') ?: ''; 

    $paket->selectByParamsMonitoring(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
    $paket->firstRow();
    
    $arrA = array('}','{','"');

    // Parsing SA
    $expSA = explode("||", $reqSA);

    // Parsing DPSJ
    $expDPSJ = explode("||", $reqDPSJ);
    $expDPSJ1 = str_replace($arrA,"",$expDPSJ[1]);

    // Get Rekanan
    $rekanan->selectByParams(array("A.REKANAN_ID" => str_replace($arrA,"",$paket_pemenang)), -1, -1);
    $rekanan->firstRow();
    $rekanan_nama = $rekanan->getField("NAMA");
    $rekanan_npwp = $rekanan->getField("NPWP");
    $rekanan_alamat = $rekanan->getField("ALAMAT");
    $rekanan_telepon = $rekanan->getField("TELEPON_FULL");
    $rekanan_email = $rekanan->getField("EMAIL");
    $rekanan_kota = $rekanan->getField("KOTA");
    $rekanan_kodepos = $rekanan->getField("KODEPOS");

    $html  = '';
    $html .=  '<table class="table table-bordered table-hover">
                <tbody>
                  <tr style="background-color:#F7CA18 !important">
                    <td width="25%" colspan="4"><h4>'.$kontrak_nama.'</h4></td> 
                  </tr>
                  <tr>
                    <td colspan="4">
                        <h2>'.$rekanan_nama.'</h2>
                        <i class="fa fa-id-card"></i> '.$rekanan_npwp.' <span class="badge badge-info">NPWP</span> </br>
                        <i class="fa fa-phone"></i> Telepon: '.$rekanan_telepon.' </br>
                        <i class="fa fa-envelope"></i> Email: '.$rekanan_email.' </br>
                        <i class="fa fa-map-marker"></i> '.$rekanan_alamat.', '.$rekanan_kota.' '.$rekanan_kodepos.'</br>
                    </td>
                  </tr>';
    $html .=  '   <tr>
                    <td width="85%" colspan="3">
                      <small>DPSJ</small> <br> '.$expDPSJ1.'
                    </td>
                    <td>
                      '.$expSA[0].' <br>
                      <h2>'.$expSA[1].'</h2>
                    </td>
                  </tr>';
    $html .=  '   <tr>
                    <td width="25%" colspan="2">
                      <small>Nomor SPPBJ</small> <br> '.$reqCode.'
                    </td>
                    <td width="25%" colspan="2">
                      <small>Tanggal SPPBJ</small> <br>  '.getFormattedDateShort2(dateTimeToPageCheck($reqTanggal)).'
                    </td>
                  </tr>';
                  // <tr>
                  //   <td width="25%" colspan="2">
                  //     <small>Pejabat Berwenang</small> <br> '.$reqPejabatBerwenang.'
                  //   </td>
                  //   <td width="10%">
                  //     <small>NUP/NIP</small> <br>  '.$reqNIP.'
                  //   </td>
                  //   <td width="15%">
                  //     <small>Jabatan</small> <br> '.$reqJabatan.'
                  //   </td>
                  // </tr>
    $html .=      '<tr>
                    <td width="25%" colspan="2">
                      <small>Nama Direktur <sup>Penyedia</sup></small> <br> <i>'.$reqDirut.' <br> '.$reqDirutJabatan.'</i>
                    </td>
                    <td width="25%" colspan="2">
                      <small>Alamat <sup>Penyedia</sup></small> <br>  '.$reqDirutAlamat.' <br> '.$reqDirutKota.'
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <small>Nilai Kontrak</small> <br> '.currencyToPage($reqNilai).'
                    </td>
                    <td>
                      <small>Harga Perkiraan Sendiri</small> <br> '.currencyToPage($kontrak_nilai).'
                    </td>
                    <td width="10%">
                      <small>PPN</small> <br>';
                      if ($reqPPN == '1') { 
    $html .=            "Ya";
                      } else { 
    $html .=            "Tidak"; 
                      }
    $html .=      '</td>
                    <td width="15%">
                      <small>Jaminan Pelaksanaan</small> <br>'; 
                      if ($reqJaminanPelaksanaan == '1') { 
    $html .=            "Ya";
                      } else { 
    $html .=            "Tidak"; 
                      }
    $html .=  ' </td>
                  </tr>';
                  if ($reqJaminanPelaksanaan == '1') {
    $html .=  ' <tr>
                    <td width="10%">
                      <small>Persen Jaminan </small> <br>  '.$reqJaminanBesar.' %
                    </td>
                    <td width="15%">
                      <small>Nilai Jaminan</small> <br>  '.currencyToPage($reqJaminanNilai).'
                    </td>
                    <td width="10%">
                      <small>Mak. Penyerahan JamPel</small> <br>  '.getFormattedDateShort2(dateTimeToPageCheck($reqJangkaMaksimal)).'
                    </td>
                    <td width="15%">
                      <small>Jangka Jaminan Pelaksanaan</small> <br>  '.getFormattedDateShort2(dateTimeToPageCheck($reqJaminanJangkaDari)).' s/d '.getFormattedDateShort2(dateTimeToPageCheck($reqJaminanJangkaSampai)).'
                    </td>
                  </tr>'; 
                  }
    $html .=  ' </tbody>
              </table>';


    // $contractingjaminan = new Contractingjaminan();
    // $contractingjaminan->selectByParams(array("CONTRACTINGREKANANID" => $reqId, "KONFIRMASI" => "1"));
     
    // if ($contractingjaminan->countRow() > 0) {
    //   $html .= '<table class="table table-bordered">
    //               <thead>
    //                 <tr class="backcolornew">
    //                   <td>Nomor Jaminan</td>
    //                   <td>Tanggal Jaminan</td>
    //                   <td width="100">File</td>
    //                   <td width="100">Tanggal Konfirmasi ke Bank</td>
    //                   <td width="100">Tanggal Konfirmasi oleh Bank</td>
    //                   <td width="100">Status</td>
    //                 </tr>'; 
    //                   while($contractingjaminan->nextRow())
    //                   {
    //   $html .= '        <tr>
    //                       <td>'.$contractingjaminan->getField("NOMOR").'</td>
    //                       <td>'.getFormattedDateShort2(dateTimeToPageCheck($contractingjaminan->getField("TANGGAL_JAMINAN"))).'</td>
    //                       <td><a href="uploads/kontrak/'.$contractingjaminan->getField("FILE_JAMINAN").'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> Download</span></a></td>
    //                       <td>'.getFormattedDateShort2(dateTimeToPageCheck($contractingjaminan->getField("TANGGAL_KONFIRMASI_KEBANK"))).'</td>
    //                       <td>'.getFormattedDateShort2(dateTimeToPageCheck($contractingjaminan->getField("TANGGAL_KONFIRMASI_OLEH_BANK"))).'</td>';
    //                       if ($contractingjaminan->getField("KONFIRMASI")) {
    //   $html .=  '         <td><span class="badge badge-primary">Sesuai</span></td>';
    //                       } else {
    //   $html .=  '         <td><span class="badge badge-danger">Tidak Sesuai</span></td>';
    //                       }
    //   $html .=  '         </tr>';
    //                   } 
    //   $html .= '  </thead>
    //             </table>';  
    // }

    return $html;
  }

  function getInfoKontrak($reqId)
  {
    include_once("functions/string.func.php");
    include_once("functions/date.func.php");
    include_once("functions/default.func.php");

    $this->_CI->load->model(array("Paket","Contracting","Contractingrekanan","Rekanan"));
    $contracting = new Contracting();
    $spkpks = new Contractingrekanan();
    $legal = new Contractingrekanan();
    $rekanan = new Rekanan();

    $contracting->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId));
    $contracting->firstRow();

    $spkpks->selectViewPKSSPK(array("A.CONTRACTINGREKANANID" => $reqId));
    $spkpks->firstRow();

    $reqJenisKontrak = $contracting->getField('JENIS_KONTRAK') ?: '-';
    $reqCode = $spkpks->getField('CR_CODE') ?: '-';
    $reqNilai = $spkpks->getField('NILAI') ?: 0;
    $reqJnsKontrakStr = $spkpks->getField('JNS_KONTRAK_STR') ?: ''; 
    $reqRekananProses1Id = $spkpks->getField('CONTRACTINGREKANANPROSES1ID') ?: '-';
    $reqPaketId = $spkpks->getField('PAKET_ID') ?: '-';
    $reqContractingRekananId = $spkpks->getField('CONTRACTINGREKANANID') ?: '-';
    $reqJenisPengadaan = $spkpks->getField('CR_JENIS_PENGADAAN') ?: '-';
    $reqJenisPengadaanStr = $spkpks->getField('CR_JENIS_PENGADAAN_STR') ?: '-';
    $reqJenisPekerjaan = $spkpks->getField('CR_JENIS_PEKERJAAN') ?: '-';
    $reqJenisPekerjaanStr = $spkpks->getField('CR_JENIS_PEKERJAAN_STR') ?: '-';
    $reqContractingjeniskontrakid = $spkpks->getField('CONTRACTINGJENISKONTRAKID') ?: '-';
    $reqJenisKontrakStr = $spkpks->getField('CR_JENIS_KONTRAK_STR') ?: '-';
    $reqWaktuPelaksanaanDari = $spkpks->getField('CR_WAKTU_PELAKSANAAN_DARI') ?: '-';
    $reqWaktuPelaksanaanSampai = $spkpks->getField('CR_WAKTU_PELAKSANAAN_SAMPAI') ?: '-';
    $reqLingkupPekerjaan = $spkpks->getField('CR_LINGKUP_PEKERJAAN') ?: '-';
    $reqNilaiKontrak = $spkpks->getField('CR_NILAI_KONTRAK') ?: '0';
    $reqMetodePembayaran = $spkpks->getField('CR_METODE_PEMBAYARAN') ?: '-';
    $reqPihak1Nama = $spkpks->getField('CR_PIHAK1_NAMA') ?: '-';
    $reqPihak1Jabatan = $spkpks->getField('CR_PIHAK1_JABATAN') ?: '-';
    $reqPihak2Nama = $spkpks->getField('CR_PIHAK2_NAMA') ?: '-';
    $reqPihak2Jabatan = $spkpks->getField('CR_PIHAK2_JABATAN') ?: '-';
    $reqPihak2 = $spkpks->getField('CR_PIHAK2_PERUSAHAAN') ?: '-';
    $reqCreatedBy = $spkpks->getField('CR_CREATED_BY') ?: '-';
    $reqContractingStatusKontrakId = $spkpks->getField('CONTRACTINGSTATUSKONTRAKID') ?: '-';
    $reqRakananId = $spkpks->getField('REKANAN_ID') ?: '-';
    $reqPO = $spkpks->getField('CR_PO') ?: '-';
    $reqTglHasilTerimaPilihan = $spkpks->getField('CR_TGL_HASIL_TERIMA_PEMILIHAN') ?: '-';
    $reqPenyelesaianAwal = $spkpks->getField('CR_PENYELESAIAN_KONTRAK_AWAL') ?: '-';
    $reqPenyelesaianAkhir = $spkpks->getField('CR_PENYELESAIAN_KONTRAK_AKHIR') ?: '-';
    $reqMasaGaransi = $spkpks->getField('CR_MASA_GARANSI') ?: '-';
    $reqMasaGaransiPeriode = $spkpks->getField('CR_MASA_GARANSI_PERIODE') ?: '-';
    $reqNama = $spkpks->getField('NAMA') ?: '-';
    $reqSA = $spkpks->getField('SA') ?: ''; 
    $reqDPSJ = $spkpks->getField('DPSJ') ?: ''; 
    $reqListKegiatan = $spkpks->getField('LIST_KEGIATAN') ?: ''; 
    $reqSumberDana = $spkpks->getField('SUMBER_DANA_KETERANGAN') ?: '-'; 
    $reqNilaiMataUang = $spkpks->getField('NILAI_MATA_UANG') ?: '-'; 
    $reqNamaKegiatan = $spkpks->getField('CR_NAMA_KEGIATAN') ?: '-'; 
    $reqNamaKegiatanStr = $spkpks->getField('CR_NAMA_KEGIATAN_STR') ?: '-'; 
    $reqNamaPengawasUnitKerja = $spkpks->getField('CR_NAMA_PENGAWAS_UNIT_KERJA') ?: '-'; 

    $legal->selectViewLegal(array("A.CONTRACTINGREKANANID" => $reqId));
    $legal->firstRow();
    $reqLegalNomorPKS = $legal->getField('CR_LEGAL_NOMOR_PKS') ?: '-';
    $reqLegalTanggal = $legal->getField('CR_LEGAL_TANGGAL') ?: '-';
    $reqLegalNomorRekanan = $legal->getField('CR_LEGAL_NOMOR_REKANAN') ?: '-';
    $reqLegalTanggalRekanan = $legal->getField('CR_LEGAL_TANGGAL_REKANAN') ?: '-';
    $reqLegalCreatedBy = $legal->getField('CR_LEGAL_CREATED_BY') ?: '-';
    $reqLegalCreatedDate = $legal->getField('CR_LEGAL_CREATED_DATE') ?: '-';
    $reqLegalUpdatedBy = $legal->getField('CR_LEGAL_UPDATED_BY') ?: '-';
    $reqLegalUpdatedDate = $legal->getField('CR_LEGAL_UPDATED_DATE') ?: '-';
    
    $arrA = array('}','{','"');
    // Parsing SA
    $expSA = explode("||", $reqSA);
    // Parsing DPSJ
    $expDPSJ = explode("||", $reqDPSJ);
    $expDPSJ1 = str_replace($arrA,"",$expDPSJ[1]);

    // Get Rekanan
    // Get Rekanan
    $rekanan->selectByParams(array("A.REKANAN_ID" => $reqRakananId), -1, -1);
    $rekanan->firstRow();
    $rekanan_nama = $rekanan->getField("NAMA");
    $rekanan_npwp = $rekanan->getField("NPWP");
    $rekanan_alamat = $rekanan->getField("ALAMAT");
    $rekanan_telepon = $rekanan->getField("TELEPON_FULL");
    $rekanan_email = $rekanan->getField("EMAIL");
    $rekanan_kota = $rekanan->getField("KOTA");
    $rekanan_kodepos = $rekanan->getField("KODEPOS");

    $html  = '';
    $html .=  '<table class="table table-bordered table-hover">
                <tbody>
                  <tr style="background-color:#F7CA18 !important">
                    <td width="25%" colspan="4"><h4>'.$reqNama.'</h4></td> 
                  </tr>
                  <tr>
                    <td colspan="4">
                        <h2>'.$rekanan_nama.'</h2>
                        <i class="fa fa-id-card"></i> '.$rekanan_npwp.' <span class="badge badge-info">NPWP</span> </br>
                        <i class="fa fa-phone"></i> Telepon: '.$rekanan_telepon.' </br>
                        <i class="fa fa-envelope"></i> Email: '.$rekanan_email.' </br>
                        <i class="fa fa-map-marker"></i> '.$rekanan_alamat.', '.$rekanan_kota.' '.$rekanan_kodepos.'</br>
                    </td>
                  </tr>';
    $html .=  '   <tr>
                    <td width="85%" colspan="3">
                      <small>DPSJ</small> <br> '.$expDPSJ1.'
                    </td>
                    <td>
                      '.$expSA[0].' <br>
                      <h2>'.$expSA[1].'</h2>
                    </td>
                  </tr>';
    $html .=  '   <tr>
                    <td colspan="2">
                      <small>Sumber Dana </small> <br> '.$reqSumberDana.'
                    </td> 
                    <td width="12%">
                      <small>Currency </small> <br> '.$reqNilaiMataUang.'
                    </td>
                    <td>
                      <small>Harga Perkiraan Sementara </small> <br> '.currencyToPage($reqNilai).'
                    </td> 
                  </tr> 
                  <tr>
                    <td width="12%">
                      <small>Nomor '.$reqJnsKontrakStr.' '.SYSTEM_NAME_PT.' </small> <br> '.$reqLegalNomorPKS.'
                    </td>
                    <td width="12%">
                      <small>Nomor PO </small> <br> '.$reqPO.'
                    </td>
                    <td width="12%">
                      <small>Tanggal  '.$reqJnsKontrakStr.'</small> <br> '.getFormattedDateShort2(dateTimeToPageCheck($reqLegalTanggal)).'
                    </td>
                    <td width="12%">
                      <small>Terima Laporan Hasil Pemilihan </small> <br> '.getFormattedDateShort2(dateTimeToPageCheck($reqTglHasilTerimaPilihan)).'
                    </td>
                  </tr> 
                  <tr>
                    <td width="13%">
                      <small>Nilai Kontrak </small> <br>  '.currencyToPage($reqNilaiKontrak).'
                    </td> 
                    <td width="12%">
                      <small>Metode Pembayaran </small> <br>';
                      if ($reqMetodePembayaran == '1') {
        $html .=        "Sekaligus";
                      } else if ($reqMetodePembayaran == '2') { 
        $html .=        "Termin"; 
                      } else {
        $html .=        "Bulanan"; 
                      }
        $html .=  ' </td>
                    <td width="12%">
                      <small>Jenis Pengadaan</small> <br> '.$reqJenisPengadaanStr.'
                    </td>
                    <td width="13%">
                      <small>Jenis Kontrak</small> <br> '.$reqJenisKontrakStr.'
                    </td>
                  </tr> 
                  <tr>
                    <td width="12%">
                      <small>Tanggal Kontrak (awal dan akhir) </small> <br> '.getFormattedDateShort2(dateTimeToPageCheck($reqWaktuPelaksanaanDari)).' s/d '.getFormattedDateShort2(dateTimeToPageCheck($reqWaktuPelaksanaanSampai)).'
                    </td>
                    <td width="12%">
                      <small>Masa Garansi?</small> <br>';
                      if ($reqMasaGaransi == '1') {
        $html .=        "Ya, ".$reqMasaGaransiPeriode.' Bulan';
                      } 
        $html .=  '</td>
                    <td width="25%" colspan="2">
                      <small>Tanggal Penyelesaian Tagihan (awal dan akhir) </small> <br> '.getFormattedDateShort2(dateTimeToPageCheck($reqPenyelesaianAwal)).' s/d '.getFormattedDateShort2(dateTimeToPageCheck($reqPenyelesaianAkhir)).'
                    </td>
                  </tr>
                  <tr>
                    <td width="25%" colspan="2"> <small>Nama Kegiatan</small> <br>
                    '.$reqNamaKegiatanStr.'
                    </td> 
                    <td width="25%" colspan="2"> <small>Nama Pengawas Unit Kerja</small> <br>
                    '.$reqNamaPengawasUnitKerja.'
                    </td> 
                  </tr>
                  <tr>
                    <td width="25%" colspan="2">
                      <small>PIHAK I </small> <br>
                      '.$reqPihak1Nama.' <br>
                      <i>'.$reqPihak1Jabatan.'</i>
                    </td>
                    <td width="25%" colspan="2">
                      <small>PIHAK II </small> <br>
                      '.$reqPihak2Nama.' <br>
                      <i>'.$reqPihak2Jabatan.'</i>
                    </td>
                  </tr>
                  ';
    $html .=  ' </tbody>
              </table>';
                  // <tr>
                  //   <td colspan="4">
                  //     <small>Lingkup Pekerjaan</small> <br> '.$reqLingkupPekerjaan.'
                  //   </td>
                  // </tr>

    return $html;
  }

  // $status=ContractingStatusKontrakId, $id=CONTRACTINGREKANANID, $type=tipe user, $skketerangan=jenis kontrak (SPK/PKS)
  public function getStatusKontrakTeruskan($status,$id,$typeid,$legal,$skketerangan)
  {
    // get jns kontra (SPK/PKS)
    include_once("functions/string.func.php");
    include_once("functions/date.func.php");
    include_once("functions/default.func.php");
    $this->_CI->load->model(array("Contractingrekanan","Contractingjaminan"));
    $spkpks = new Contractingrekanan();
    $spkpks->selectByParams(array("A.CONTRACTINGREKANANID" => $id));
    $spkpks->firstRow();
    $contractingrekananproses1id = $spkpks->getField('CONTRACTINGREKANANPROSES1ID');
    $reqPaketId = $spkpks->getField('PAKET_ID') ?: '-';
    $reqPICKontrak = $spkpks->getField('PIC_KONTRAK') ?: '-';
    $reqPICPengendali = $spkpks->getField('PIC_PENGENDALI') ?: '-';
    $reqPICPenyelesaian = $spkpks->getField('PIC_PENYELESAIAN') ?: '-';

    switch ($spkpks->getField('JNS_KONTRAK')) {
      case '0':
        $jnsKontrak = 'SPK';
        break;
      case '1':
        $jnsKontrak = 'Surat Perjanjian';
        break;
      case '2':
        $jnsKontrak = 'ALl';
        break;
      case '3':
        $jnsKontrak = 'Surat Pesanan';
        break;

      default:
        // code...
        break;
    }

    $html  = '';

    if ($skketerangan == 'SPPBJ') {
      if ($status > 0) {
        switch ($status) {
          case '1': // 1:Konfirmasi Penyedia
            if ($typeid == '6') { // Penyedia
             $html .= '<a onClick="prosesKontrak(\'2\',\''.$contractingrekananproses1id.'\',\'Konfirmasi SPPBJ?\')" class="'.CLASS_BTN_SUCCESS.' mr-1"> <i class="fa fa-gavel"></i> Konfirmasi SPPBJ </a>';
            } else { // Notif Untuk Pengelola Kontrak
              $html .= '<div class="alert alert-info mr-1 text-center" style="font-weight:bold; margin-top:1%">. : : Menunggu Konfirmasi dari Penyedia : : .</div>';
            }
            break;

          case '2': // 2:Approve Konfirmasi SPPBJ
            if ($typeid != '6' && $typeid == '12') { // Pengelola Kontrak
              $contractingjaminan = new Contractingjaminan();
              $contractingjaminan->selectByParams(array("CONTRACTINGREKANANID" => $id));
              if ($contractingjaminan->countRow() > 0) {
                $contractingjaminan2 = new Contractingjaminan();
                $contractingjaminan2->selectByParams(array("CONTRACTINGREKANANID" => $id, "KONFIRMASI" => "1"));
                $totalStatus = $contractingjaminan2->countRow();
                if ($totalStatus > 0) {
                  $html .= '<div class="alert alert-info" style="font-weight:bold; margin-top:1%; text-align:center">. : : SPPBJ Sudah Diterima Oleh Penyedia. Silahkan ke tahap selanjutnya pembuatan '.$jnsKontrak.' : : .</div>';
                } else {
                  $html .= '<div class="alert alert-info" style="font-weight:bold; margin-top:1%; text-align:center">. : : SPPBJ Sudah Diterima Oleh Penyedia. Silahkan konfirmasi Jaminan Pelaksanaan : : .</div>';
                }
                $html .= '<table class="table table-bordered">
                            <thead>
                              <tr class="backcolornew">
                                <td>Nomor Jaminan</td>
                                <td>Tanggal Jaminan</td>
                                <td width="100">File <br>Jaminan</td>
                                <td width="100">Tanggal Konfirmasi ke Bank</td>
                                <td width="100">Tanggal Konfirmasi oleh Bank</td>
                                <td width="100">Bukti Konfirmasi <br> Keabsahan</td>
                                <td width="100">Status</td>
                                <td width="10">Aksi</td>
                              </tr>'; 
                              if ($contractingjaminan->countRow() > 0) {
                                while($contractingjaminan->nextRow())
                                {
                $html .= '        <tr>
                                    <td>'.$contractingjaminan->getField("NOMOR").'</td>
                                    <td>'.getFormattedDateShort2(dateTimeToPageCheck($contractingjaminan->getField("TANGGAL_JAMINAN"))).'</td>
                                    <td><a href="uploads/kontrak/'.$contractingjaminan->getField("FILE_JAMINAN").'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> Download</span></a></td>
                                    <td>'.getFormattedDateShort2(dateTimeToPageCheck($contractingjaminan->getField("TANGGAL_KONFIRMASI_KEBANK"))).'</td>
                                    <td>'.getFormattedDateShort2(dateTimeToPageCheck($contractingjaminan->getField("TANGGAL_KONFIRMASI_OLEH_BANK"))).'</td>';

                                    if ($contractingjaminan->getField("FILE_KONFIRMASI")) {
                $html .=  '         <td><a href="uploads/kontrak/'.$contractingjaminan->getField("FILE_KONFIRMASI").'" target="_blank" class="badge badge-primary"><span class="fa fa-download"> Download</span></a></td>';
                                    } else {
                $html .=  '         <td>-</td>';
                                    }

                                    if ($contractingjaminan->getField("KONFIRMASI")) {
                $html .=  '         <td><span class="badge badge-primary">Sesuai</span></td>';
                                    } else {
                $html .=  '         <td><span class="badge badge-danger">Tidak Sesuai</span></td>';
                                    }
                $html .=  '         <td class="text-center" style="padding: 10px !important">
                                      <a style="color: #fff;" onclick="openAddFrame(\'main/loadUrlKontrak/kontrak/contracting_jaminan_file_update?reqJaminanId='.$contractingjaminan->getField('CONTRACTING_JAMINAN_ID').'\')" class="badge badge-info"><i class="fa fa-edit"></i></a> 
                                    </td>
                                  </tr>';
                                } 
                              }
                $html .= '  </thead>
                          </table>';
              } else { // Tidak ada Jaminan
                $html .= '<div class="alert alert-info" style="font-weight:bold; margin-top:1%; text-align:center">. : : SPPBJ Sudah Diterima Oleh Penyedia. Silahkan ke tahap selanjutnya pembuatan '.$jnsKontrak.' : : .</div>';
              }
            } else { // Notif Untuk Penyedia
              $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : SPPBJ Sudah Diterima Oleh Penyedia : : .</div>';
            }
            break;

          case '110': // Approval Kasubdit
            if ($typeid == '20') { // Pemeriksa Kontrak
              $html .= '<a onClick="prosesKontrak(\'111\',\''.$id.'\',\'Setujui draft?\')" class="'.CLASS_BTN_SUCCESS.'"> <i class="fa fa-gavel"></i> Setujui draft </a>';
            $html .= '<a onClick="prosesKontrak(\'115\',\''.$id.'\',\'Kembalikan untuk di revisi ?\')" class="'.CLASS_BTN_DANGER.' ml-1"> <i class="fa fa-times"></i> Kembalikan </a>';
              $html .= '<a onClick="openAdd(\'main/loadUrlKontrak/kontrak/contracting_catatan?reqAidi='.$id.'&reqJenis=sppbj\')" class="'.CLASS_BTN_INFO.' ml-1"> <i class="fa fa-plus"></i> Tambah Catatan </a>';
            } else if ($typeid != '6') { // Pengelola Kontrak
              $html .= '<div class="alert alert-info mr-1 text-center" style="font-weight:bold; margin-top:1%">. : : Menunggu SPPBJ Approval Kasubdit : : .</div>';
            } else { // Notif Untuk Penyedia
              $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Tahap Approval SPPBJ : : .</div>';
            }
            break;

          case '111': // Approval PPK
            if ($typeid == '28') { // PPK
              $html .= '<a onClick="prosesKontrak(\'112\',\''.$id.'\',\'Yakin setuju dan Sudah di Tandatangan?\')" class="'.CLASS_BTN_SUCCESS.'"> <i class="fa fa-gavel"></i> Setujui dan Sudah di Tandatangan </a>';
              $html .= '<a onClick="prosesKontrak(\'116\',\''.$id.'\',\'Kembalikan ke Kasubdit untuk di revisi ?\')" class="'.CLASS_BTN_DANGER.' ml-1"> <i class="fa fa-times"></i> Kembalikan </a>';
              $html .= '<a onClick="openAdd(\'main/loadUrlKontrak/kontrak/contracting_catatan?reqAidi='.$id.'&reqJenis=sppbj\')" class="'.CLASS_BTN_INFO.' ml-1"> <i class="fa fa-plus"></i> Tambah Catatan </a>';
            } else if ($typeid != '6') { // Pengelola Kontrak
              $html .= '<div class="alert alert-info mr-1 text-center" style="font-weight:bold; margin-top:1%">. : : Menunggu SPPBJ Disetujui dan Tandatangan PPK : : .</div>';
            } else { // Notif Untuk Penyedia
              $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Tahap Menunggu SPPBJ Disetujui dan Tandatangan PPK : : .</div>';
            }
            break;

          case '112': // Approval PPK
            if ($typeid == '28') { // PPK
              $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : SPPBJ sudah disetujui dan ditandatangani oleh PPK : : .</div>';
            } else if ($typeid != '6') { // Pengelola Kontrak
              // $html .= '<a onClick="openAdd(\'main/loadUrlKontrak/kontrak/contracting_catatan?reqAidi='.$id.'&reqJenis=sppbj\')" class="'.CLASS_BTN_INFO.' ml-1"> <i class="fa fa-plus"></i> Catatan </a>';
              if ($this->_CI->LEVEL_KONTRAK == '1' && $reqPICKontrak == $this->_CI->USER_LOGIN_ID) {
                $html .= '<a onClick="prosesKontrak(\'1\',\''.$contractingrekananproses1id.'\',\'Kirim SPPBJ ke Penyedia ?\')" class="'.CLASS_BTN_SUCCESS.' mr-1"> '.BTN_KIRIM.' Ke Penyedia </a>';
              }
                $html .= '<div class="alert alert-info mr-1 text-center" style="font-weight:bold; margin-top:1%">. : : SPPBJ sudah disetujui dan ditandatangani oleh PPK : : .</div>';
            } else { // Notif Untuk Penyedia
              $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Cek SPPBJ oleh Pengelola Kontrak : : .</div>';
            }
            break;

          case '115': // Kasubdit menolak SPPBJ
            if ($typeid == '20') { // Pemeriksa Kontrak
            } else if ($typeid != '6') { // Pengelola Kontrak
              if ($this->_CI->LEVEL_KONTRAK == '1' && $reqPICKontrak == $this->_CI->USER_LOGIN_ID) { // Kasi
                $html .= '<a onClick="prosesKontrak(\'110\',\''.$contractingrekananproses1id.'\',\'Kirim ke Kasubdit untuk Proses Approval, pastikan data dan dokumen sudah lengkap?\')" class="'.CLASS_BTN_SUCCESS.' mr-1"> '.BTN_KIRIM.' Ke Kasubdit </a>';
                $html .= '<a onClick="openAdd(\'main/loadUrlKontrak/kontrak/contracting_catatan?reqAidi='.$id.'&reqJenis=sppbj\')" class="'.CLASS_BTN_INFO.' ml-1"> <i class="fa fa-plus"></i> Lihat Catatan </a>';
                $html .= '<div class="alert alert-danger mr-1 text-center" style="font-weight:bold; margin-top:1%">. : : Ditolak Kasubdit, untuk revisi : : .</div>';
              } else {
                $html .= '<a onClick="openAdd(\'main/loadUrlKontrak/kontrak/contracting_catatan?reqAidi='.$id.'&reqJenis=sppbj\')" class="'.CLASS_BTN_INFO.' ml-1"> <i class="fa fa-plus"></i> Lihat Catatan </a>';
                $html .= '<div class="alert alert-danger mr-1 text-center" style="font-weight:bold; margin-top:1%">. : : Ditolak Kasubdit, untuk revisi : : .</div>';

              }
            } else { // Notif Untuk Penyedia
              $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Tahap Approval SPPBJ : : .</div>';
            }
            break;

          case '116': // Revisi Kasubdit
            if ($typeid == '20') { // Pemeriksa Kontrak
              $html .= '<a onClick="prosesKontrak(\'111\',\''.$id.'\',\'Setujui draft?\')" class="'.CLASS_BTN_SUCCESS.'"> <i class="fa fa-gavel"></i> Setujui draft </a>';
            $html .= '<a onClick="prosesKontrak(\'115\',\''.$id.'\',\'Kembalikan untuk di revisi ?\')" class="'.CLASS_BTN_DANGER.' ml-1"> <i class="fa fa-times"></i> Kembalikan </a>';
              $html .= '<a onClick="openAdd(\'main/loadUrlKontrak/kontrak/contracting_catatan?reqAidi='.$id.'&reqJenis=sppbj\')" class="'.CLASS_BTN_INFO.' ml-1"> <i class="fa fa-plus"></i> Tambah Catatan </a>';
            } else if ($typeid != '6') { // Pengelola Kontrak
              $html .= '<div class="alert alert-info mr-1 text-center" style="font-weight:bold; margin-top:1%">. : : Menunggu SPPBJ Revisi Kasubdit : : .</div>';
            } else { // Notif Untuk Penyedia
              $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Tahap Revisi SPPBJ : : .</div>';
            }
            break;

          default:
            $html .= '';
            break;
        }
      } else {
        if ($typeid != '6') { // Pengelola Kontrak
          if ($legal=='' || $legal == '0') { // bukan legal
            // OLD
            // $html .= '<a onClick="prosesKontrak(\'1\',\''.$contractingrekananproses1id.'\',\'Kirim SPPBJ ke Penyedia ?\')" class="'.CLASS_BTN_SUCCESS.' mr-1"> '.BTN_KIRIM.' Ke Penyedia </a>';
            if ($this->_CI->LEVEL_KONTRAK == '1' && $reqPICKontrak == $this->_CI->USER_LOGIN_ID) { // Bukan Kasi tapi PIC
              $html .= '<a onClick="prosesKontrak(\'110\',\''.$contractingrekananproses1id.'\',\'Kirim ke Kasubdit untuk Proses Approval, pastikan data dan dokumen sudah lengkap?\')" class="'.CLASS_BTN_SUCCESS.' mr-1"> '.BTN_KIRIM.' Ke Kasubdit </a>';
            }
          }
        } else { // Notif Untuk Penyedia
          $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Tahap Pembuatan SPPBJ : : .</div>';

        }
      }
    }

    if ($skketerangan == 'SPK-PKS') {
      switch ($status) {
        case '2': // 2:Approve SPPBJ
        case '3': // 3:Buat SPK/PKS
          $html2 = '';
          $html3 = '';
          if ($typeid == '20') { // Pemeriksa Kontrak 
            $html2 .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Tahap pembuatan kontrak oleh PIC Kontrak : : .</div>';
          } if ($typeid == '28') { // PPK 
            $html2 .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Tahap pembuatan kontrak oleh PIC Kontrak : : .</div>';
          } else if ($typeid != '6') { // Pengelola Kontrak
            if ($this->_CI->LEVEL_KONTRAK == '1' && ($this->_CI->USER_TYPE_ID == '12' && $reqPICKontrak == $this->_CI->USER_LOGIN_ID)) {
              $html3 .= '<a onClick="prosesKontrak(\'113\',\''.$id.'\',\'Teruskan draft '.$jnsKontrak.' ?\')" class="'.CLASS_BTN_SUCCESS.'"> <i class="fa fa-send"></i> Teruskan ke Kasubdit </a>'; 
            }
          } else { // Notif Untuk Penyedia
            $html2 .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Tahap pembuatan '.$jnsKontrak.' : : .</div>';
          }
          $html .= $html3.$html2;
          break;

        case '113': // 113:Approval SPK/PKS Kasubdit
          if ($typeid == '20') { // Pemeriksa Kontrak
            $html .= '<a onClick="prosesKontrak(\'31\',\''.$id.'\',\'Approve draft '.$jnsKontrak.' ?\')" class="'.CLASS_BTN_SUCCESS.'"> <i class="fa fa-gavel"></i> Approve </a> ';
            $html .= '<a onClick="prosesKontrak(\'114\',\''.$id.'\',\'Tolak draft '.$jnsKontrak.' ?\')" class="'.CLASS_BTN_DANGER.'"> <i class="fa fa-times"></i> Tolak </a> ';
            $html .= '<a onClick="openAdd(\'main/loadUrlKontrak/kontrak/contracting_catatan?reqAidi='.$id.'&reqJenis=kontrak\')" class="'.CLASS_BTN_INFO.'"> <i class="fa fa-plus"></i> Tambah Catatan </a>';
          } else if ($typeid == '28') { // PPK
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Tahap approval '.$jnsKontrak.' : : .</div>';
          } else if ($typeid != '6') { // Pengelola Kontrak
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Tahap approval '.$jnsKontrak.' : : .</div>';
          } else { // Notif Untuk Penyedia
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Tahap approval '.$jnsKontrak.' : : .</div>';
          }
          break;

        case '114': // 114:Tolak SPK/PKS
          if ($typeid == '20') { // Pemeriksa Kontrak 
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Tahap revisi kontrak oleh PIC Kontrak : : .</div>';
          } else if ($typeid == '28') { // PPK
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Tahap revisi kontrak oleh PIC Kontrak : : .</div>';
          }else if ($typeid != '6') { // Pengelola Kontrak
            $html .= '<a onClick="prosesKontrak(\'113\',\''.$id.'\',\'Teruskan draft '.$jnsKontrak.' ?\')" class="'.CLASS_BTN_SUCCESS.'"> <i class="fa fa-send"></i> Teruskan ke Kasubdit </a>'; 
            $html .= '<a onClick="openAdd(\'main/loadUrlKontrak/kontrak/contracting_catatan?reqAidi='.$id.'&reqJenis=kontrak\')" class="'.CLASS_BTN_INFO.' ml-1"> <i class="fa fa-plus"></i> Lihat Catatan </a>';
          } else { // Notif Untuk Penyedia
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Tahap pembuatan '.$jnsKontrak.' : : .</div>';
          }
          break;

        case '31': // 31:Approve Pemeriksa
          if ($typeid == '20') { // Pemeriksa Kontrak
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Draft di Setujui : : .</div>';
          } else if ($typeid == '28') { // PPK
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Draft di Setujui : : .</div>';
          } else if ($typeid != '6') { // Pengelola Kontrak
            $html .= '<a onClick="prosesKontrak(\'4\',\''.$id.'\',\'Teruskan '.$jnsKontrak.' ke Penyedia, pastikan data dan dokumen sudah lengkap?\')" class="'.CLASS_BTN_SUCCESS.'"> <i class="fa fa-send"></i> Teruskan ke Penyedia </a>';
          } else { // Notif Untuk Penyedia
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Tahap pembuatan '.$jnsKontrak.' : : .</div>';
          }
          break;

        case '4': // 4:Persetujuan Penyedia SPK/PKS
          if ($typeid == '6') { // Penyedia
            $html .= '<a onClick="prosesKontrak(\'5\',\''.$contractingrekananproses1id.'\',\'Setujui '.$jnsKontrak.' ?\')" class="'.CLASS_BTN_SUCCESS.' mr-1"> <i class="fa fa-gavel"></i> Setujui '.$jnsKontrak.' </a> ';
            $html .= '<a onClick="prosesKontrak(\'51\',\''.$contractingrekananproses1id.'\',\'Kembalikan '.$jnsKontrak.' untuk di revisi ?\')" class="'.CLASS_BTN_DANGER.'"> <i class="fa fa-times"></i> Kembalikan '.$jnsKontrak.' </a> ';
            $html .= '<a onClick="openAdd(\'main/loadUrlKontrak/kontrak/contracting_catatan_penyedia?reqAidi='.$id.'&reqJenis=kontrak\')" class="'.CLASS_BTN_INFO.'"> <i class="fa fa-plus"></i> Tambah Catatan </a>';
          } else { // Notif Untuk Pengelola Kontrak
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Menunggu Persetujuan '.$jnsKontrak.' dari Penyedia : : .</div>';
          }
          break;

        case '5': // 4:Approve SPK/PKS
          if ($typeid == '20') { // Pemeriksa Kontrak
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : '.$jnsKontrak.' Sudah Disetujui : : .</div>';
          } else if ($typeid == '28') { // PPH
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : '.$jnsKontrak.' Sudah Disetujui : : .</div>';
          } else if ($typeid != '6') { // Pengelola Kontrak
            $html .= '<a onClick="prosesKontrak(\'6\',\''.$id.'\',\'Lanjut ke Pelaksanaan Kontrak ?\')" class="'.CLASS_BTN_SUCCESS.'"> <i class="fa fa-gavel"></i> Pelaksanaan Kontrak </a>';
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : '.$jnsKontrak.' Sudah Disetujui Oleh Penyedia Silahkan ke tahap selanjutnya Pelaksanaan Kontrak : : .</div>';
          } else { // Notif Untuk Penyedia
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : '.$jnsKontrak.' Sudah Disetujui : : .</div>';
          }
          break;

        case '51': // 4:Tolak SPK/PKS
          if ($typeid == '20') { // Pemeriksa Kontrak
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : '.$jnsKontrak.' Di Kembalikan Oleh Penyedia dan sedang di revisi : : .</div>';
          } else if ($typeid == '28') { // PPK
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : '.$jnsKontrak.' sedang di revisi : : .</div>';
          } else if ($typeid != '6') { // Pengelola Kontrak
            $html .= '<a onClick="prosesKontrak(\'4\',\''.$id.'\',\'Kirim '.$jnsKontrak.' ke Penyedia ?\')" class="'.CLASS_BTN_SUCCESS.'"> <i class="fa fa-send"></i> Kirim ulang Ke Penyedia </a>';
            $html .= '<a onClick="openAdd(\'main/loadUrlKontrak/kontrak/contracting_catatan?reqAidi='.$id.'&reqJenis=kontrak\')" class="'.CLASS_BTN_INFO.' ml-1"> <i class="fa fa-plus"></i> Tambah Catatan </a>';
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : '.$jnsKontrak.' Di Kembalikan Oleh Penyedia Silahkan revisi dan kirim kembali : : .</div>';
          } else { // Notif Untuk Penyedia
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : '.$jnsKontrak.' sedang di revisi : : .</div>';
          }
          break;

        default:
          $html .= '';
          break;
      }
    }

    if ($skketerangan == 'PELAKSANAAN') {
      switch ($status) {
        case '3': // 3: Kontrak  dibuat
        case '6': // 6: Pelaksanaan Kontrak
          if ($typeid != '6') { // Pengelola Kontrak
            $html .= '<a onClick="prosesKontrak(\'200\',\''.$contractingrekananproses1id.'\',\'Apakah anda yakin kontrak ini sudah selesai?\')" class="'.CLASS_BTN_WARNING.'"> <i class="fa fa-gavel"></i> Selesai Kontrak </a>';
          } else { // Notif Untuk Penyedia
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Kontrak sedang Berjalan : : .</div>';
          }
          break;
        case '7': // 7: Perubahan Kontrak
          if ($typeid != '6') { // Pengelola Kontrak
            $url3 = $this->_CI->uri->segment(3, "");
            if ($url3 == 'contracting_pengelolaan_realisasi' || $url3 == 'contracting_pengelolaan_termin') {
              $html .= '<a onClick="prosesKontrak(\'200\',\''.$contractingrekananproses1id.'\',\'Apakah anda yakin kontrak ini sudah selesai?\')" class="'.CLASS_BTN_WARNING.'"> <i class="fa fa-gavel"></i> Selesai Kontrak </a>';
            }
          } else { // Notif Untuk Penyedia
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Perubahan Kontrak : : .</div>';
          }
          break;
        case '8': // 8: Penyesuaian Harga
          if ($typeid != '6') { // Pengelola Kontrak
            $url3 = $this->_CI->uri->segment(3, "");
            if ($url3 == 'contracting_pengelolaan_realisasi' || $url3 == 'contracting_pengelolaan_termin') {
              $html .= '<a onClick="prosesKontrak(\'200\',\''.$contractingrekananproses1id.'\',\'Apakah anda yakin kontrak ini sudah selesai?\')" class="'.CLASS_BTN_WARNING.'"> <i class="fa fa-gavel"></i> Selesai Kontrak </a>';
            }
          } else { // Notif Untuk Penyedia
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Perubahan Kontrak : : .</div>';
          }
          break;
        case '9': // 9: Keadaan Kahar
          if ($typeid != '6') { // Pengelola Kontrak
            $url3 = $this->_CI->uri->segment(3, "");
            if ($url3 == 'contracting_pengelolaan_realisasi' || $url3 == 'contracting_pengelolaan_termin') {
              $html .= '<a onClick="prosesKontrak(\'200\',\''.$contractingrekananproses1id.'\',\'Apakah anda yakin kontrak ini sudah selesai?\')" class="'.CLASS_BTN_WARNING.'"> <i class="fa fa-gavel"></i> Selesai Kontrak </a>';
            }
          } else { // Notif Untuk Penyedia
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Perubahan Kontrak : : .</div>';
          }
          break;
        case '10': // 10: Berakhir Kontrak
          if ($typeid != '6') { // Pengelola Kontrak
            $url3 = $this->_CI->uri->segment(3, "");
            if ($url3 == 'contracting_pengelolaan_realisasi' || $url3 == 'contracting_pengelolaan_termin') {
              $html .= '<a onClick="prosesKontrak(\'200\',\''.$contractingrekananproses1id.'\',\'Apakah anda yakin kontrak ini sudah selesai?\')" class="'.CLASS_BTN_WARNING.'"> <i class="fa fa-gavel"></i> Selesai Kontrak </a>';
            }
          } else { // Notif Untuk Penyedia
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Perubahan Kontrak : : .</div>';
          }
          break;
        case '11': // 11: Pemutusan Kontrak
          if ($typeid != '6') { // Pengelola Kontrak
            $url3 = $this->_CI->uri->segment(3, "");
            if ($url3 == 'contracting_pengelolaan_realisasi' || $url3 == 'contracting_pengelolaan_termin') {
              $html .= '<a onClick="prosesKontrak(\'200\',\''.$contractingrekananproses1id.'\',\'Apakah anda yakin kontrak ini sudah selesai?\')" class="'.CLASS_BTN_WARNING.'"> <i class="fa fa-gavel"></i> Selesai Kontrak </a>';
            }
          } else { // Notif Untuk Penyedia
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Perubahan Kontrak : : .</div>';
          }
          break;
        case '12': // 12: Pemberian Kesempatan
          if ($typeid != '6') { // Pengelola Kontrak
            $url3 = $this->_CI->uri->segment(3, "");
            if ($url3 == 'contracting_pengelolaan_realisasi' || $url3 == 'contracting_pengelolaan_termin') {
              $html .= '<a onClick="prosesKontrak(\'200\',\''.$contractingrekananproses1id.'\',\'Apakah anda yakin kontrak ini sudah selesai?\')" class="'.CLASS_BTN_WARNING.'"> <i class="fa fa-gavel"></i> Selesai Kontrak </a>';
            }
          } else { // Notif Untuk Penyedia
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Perubahan Kontrak : : .</div>';
          }
          break;
        case '13': // 13: Sanksi dan Denda
          if ($typeid != '6') { // Pengelola Kontrak
            $url3 = $this->_CI->uri->segment(3, "");
            if ($url3 == 'contracting_pengelolaan_realisasi' || $url3 == 'contracting_pengelolaan_termin') {
              $html .= '<a onClick="prosesKontrak(\'200\',\''.$contractingrekananproses1id.'\',\'Apakah anda yakin kontrak ini sudah selesai?\')" class="'.CLASS_BTN_WARNING.'"> <i class="fa fa-gavel"></i> Selesai Kontrak </a>';
            }
          } else { // Notif Untuk Penyedia
            $html .= '<div class="aler alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Perubahan Kontrak : : .</div>';
          }
          break;

        default:
          $html .= '';
          break;
      }
    }

    if ($skketerangan == 'PENUTUPAN') {
      if ($spkpks->getField('JNS_KONTRAK') == '3') { // Surat Pesanan
        $html .= '<a onClick="prosesKontrak(\'200\',\''.$contractingrekananproses1id.'\',\'Apakah anda yakin kontrak sudah selesai ?\')" class="'.CLASS_BTN_WARNING.'"> <i class="fa fa-gavel"></i> Kontrak Selesai ?</a>';
      } else
      {
        switch ($status) {
          case '100': // 100: Penutupan Kontrak
            if ($typeid != '6') { // bukan penyedia tapi Pengelola Kontrak
              $spkpksKontrak = new Contractingrekanan();
              $spkpksKontrak->selectViewPKSSPK(array("A.CONTRACTINGREKANANID" => $id));
              $spkpksKontrak->firstRow();

              $reqNilaiKontrak = $spkpksKontrak->getField('CR_NILAI_KONTRAK') ?: '-';
              $reqJnsKontrak = $spkpksKontrak->getField('JNS_KONTRAK') ?: '0';

              // if ($reqJnsKontrak == '1') { // Jika Kontrak PKS maka harus melalui persetutujuan pemeriksa kontrak atau > 1M
                $proses5 = new Contractingrekanan();
                $proses5->selectProses5(array("A.CONTRACTINGREKANANID" => $id));
                $proses5->firstRow();
                $reqApprovalPemeriksa = $proses5->getField('CR_PEMERIKSA_APPROVAL');

                // if ($reqJnsKontrak == '1' && $typeid == '20') { // Jika Kontrak PKS maka harus melalui persetutujuan pemeriksa kontrak atau > 1M dan Pemeriksa Kontrak
                //   // code...
                //   if ($reqApprovalPemeriksa == '1') {
                //     $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Kontrak sudah di approve : : .</div>';
                //   } else {
                //     $proses5Total = new Contractingrekanan();
                //     $proses5Total->selectProses5(array("A.CONTRACTINGREKANANID" => $id));
                //     if ($proses5Total->countRow() > 0) {
                //       $html .= '<a onClick="approvalKontrak('.$id.')" class="'.CLASS_BTN_WARNING.'"> <i class="fa fa-gavel"></i> Approve Kontrak ?</a>';
                //     } else {
                //       $html .= '<div class="alert alert-danger text-center" style="font-weight:bold; margin-top:1%">. : : BAST Belum di isi oleh Pengelola Kontrak : : .</div>';
                //     }
                //   }
                // } else { // Jika SPK Tidak perlu approval pemeriksa
                  if ($typeid == '12') { // Pengelola Kontrak
                    $proses5Total = new Contractingrekanan();
                    $proses5Total->selectProses5(array("A.CONTRACTINGREKANANID" => $id));
                    // if ($proses5Total->countRow() > 0) {
                      // if ($reqJnsKontrak == '1' && $reqApprovalPemeriksa == '1') { // PKS & sudah approval pemeriksa kontrak
                        $html .= '<a onClick="prosesKontrak(\'200\',\''.$contractingrekananproses1id.'\',\'Apakah anda yakin kontrak sudah selesai ?\')" class="'.CLASS_BTN_WARNING.'"> <i class="fa fa-gavel"></i> Kontrak Selesai ?</a>';
                      } else if ($reqJnsKontrak == '0') {
                        $html .= '<a onClick="prosesKontrak(\'200\',\''.$contractingrekananproses1id.'\',\'Apakah anda yakin kontrak sudah selesai ?\')" class="'.CLASS_BTN_WARNING.'"> <i class="fa fa-gavel"></i> Kontrak Selesai ?</a>';
                      }
                    // } else {
                      // $html .= '<div class="alert alert-danger text-center" style="font-weight:bold; margin-top:1%">. : : BAST Belum di isi oleh Pengelola Kontrak : : .</div>';
                    // }
                  // }
                // }


              // }


            } else { // Notif Untuk Penyedia
              $html .= '<div class="alert alert-danger text-center" style="font-weight:bold; margin-top:1%">. : : Kontrak sedang proses Serah Terima Pekerjaan : : .</div>';
            }
            break;

          default:
            $html .= '';
            break;
        }
      }
    }

    $html .= '';

    return $html;
  }

  // $status=ContractingStatusKontrakId, $id=CONTRACTINGREKANANID, $type=tipe user, $skketerangan=jenis kontrak (SPK/PKS)
  public function getStatusKontrakTeruskanMulti($status,$id,$typeid,$legal,$skketerangan)
  {
    // get jns kontra (SPK/PKS)
    $this->_CI->load->model("Contractingrekanan");

    if ($typeid == '6') { // Penyedia
      $spkpks = new Contractingrekanan();
      $spkpks->selectByParams(array("D.CONTRACTINGREKANANPROSES1ID" => $id));
      $spkpks->firstRow();
      $contractingrekananproses1id = $spkpks->getField('CONTRACTINGREKANANPROSES1ID');
      $reqPaketId = $spkpks->getField('PAKET_ID') ?: '-';
    } else { // Pengelola Kontrak
      $spkpks = new Contractingrekanan();
      $spkpks->selectByParams(array("A.CONTRACTINGREKANANID" => $id));
      $spkpks->firstRow();
      $contractingrekananproses1id = $spkpks->getField('CONTRACTINGREKANANPROSES1ID');
      $reqPaketId = $spkpks->getField('PAKET_ID') ?: '-';
    }

    switch ($spkpks->getField('JNS_KONTRAK')) {
      case '0':
        $jnsKontrak = 'SPK';
        break;
      case '1':
        $jnsKontrak = 'Surat Perjanjian';
        break;
      case '2':
        $jnsKontrak = 'ALl';
        break;
      case '3':
        $jnsKontrak = 'Surat Pesanan';
        break;

      default:
        // code...
        break;
    }

    $html  = '';

    if ($skketerangan == 'SPPBJ') {
      if ($status > 0) {
        switch ($status) {
          case '1': // 1:Konfirmasi Penyedia
            if ($typeid == '6') { // Penyedia
             $html .= '<a onClick="prosesKontrak(\'2\',\''.$id.'\',\'Konfirmasi SPPBJ ?\')" class="'.CLASS_BTN_SUCCESS.' mr-1"> <i class="fa fa-gavel"></i> Konfirmasi SPPBJ </a>';
            } else { // Notif Untuk Pengelola Kontrak
              $html .= '<div class="alert alert-danger mr-1 text-center" style="font-weight:bold; margin-top:1%">. : : Menunggu Konfirmasi dari Penyedia : : .</div>';
            }
            break;

          case '2': // 2:Approve Konfirmasi SPPBJ
            if ($typeid != '6') { // Pengelola Kontrak
              $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%;">. : : SPPBJ Sudah Diterima Oleh Penyedia. Silahkan ke tahap selanjutnya pembuatan '.$jnsKontrak.' : : .</div>';
            } else { // Notif Untuk Penyedia
              $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : SPPBJ Sudah Diterima : : .</div>';
            }
            break;

          default:
            $html .= '';
            break;
        }
      } else {
        if ($typeid != '6') { // Pengelola Kontrak
          if ($legal=='' || $legal == '0') { // bukan legal
            $html .= '<a onClick="prosesKontrak(\'1\',\''.$id.'\',\'Kirim SPPBJ ke Penyedia ?\')" class="'.CLASS_BTN_SUCCESS.' mr-1"> '.BTN_KIRIM.' Ke Penyedia </a>';
          }
        } else { // Notif Untuk Penyedia
          $html .= '<div class="alert alert-danger text-center" style="font-weight:bold; margin-top:1%">. : : Tahap Pembuatan SPPBJ : : .</div>';

        }
      }
    }

    if ($skketerangan == 'SPK-PKS') {
      switch ($status) {
        case '3': // 3:Buat SPK/PKS
          if ($typeid != '6') { // Pengelola Kontrak
            $html .= '<a onClick="prosesKontrak(\'4\',\''.$id.'\',\'Kirim '.$jnsKontrak.' ke Penyedia ?\')" class="'.CLASS_BTN_SUCCESS.'"> <i class="fa fa-send"></i> Kirim Ke Penyedia </a>';
          } else { // Notif Untuk Penyedia
            $html .= '<div class="alert alert-danger text-center" style="font-weight:bold; margin-top:1%">. : : Tahap pembuatan '.$jnsKontrak.' : : .</div>';
          }
          break;

        case '4': // 4:Persetujuan Penyedia SPK/PKS
          if ($typeid == '6') { // Penyedia
            $html .= '<a onClick="prosesKontrak(\'5\',\''.$id.'\',\'Setujui '.$jnsKontrak.' ?\')" class="'.CLASS_BTN_SUCCESS.' mr-1"> <i class="fa fa-gavel"></i> Setujui '.$jnsKontrak.' </a>';
            // $html .= '<a onClick="prosesKontrak(\'51\',\''.$contractingrekananproses1id.'\',\'Kembalikan '.$jnsKontrak.' untuk di revisi ?\')" class="'.CLASS_BTN_DANGER.'"> <i class="fa fa-times"></i> Kembalikan '.$jnsKontrak.' </a>';
          } else { // Notif Untuk Pengelola Kontrak
            $html .= '<div class="alert alert-danger text-center" style="font-weight:bold; margin-top:1%">. : : Menunggu Persetujuan '.$jnsKontrak.' dari Penyedia : : .</div>';
          }
          break;

        case '5': // 4:Approve SPK/PKS
          if ($typeid != '6') { // Pengelola Kontrak
            $html .= '<a onClick="prosesKontrak(\'6\',\''.$id.'\',\'Lanjut ke Pelaksanaan Kontrak ?\')" class="'.CLASS_BTN_SUCCESS.'"> <i class="fa fa-gavel"></i> Pelaksanaan Kontrak </a>';
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : '.$jnsKontrak.' Sudah Disetujui Oleh Penyedia Silahkan ke tahap selanjutnya Pelaksanaan Kontrak : : .</div>';
          } else { // Notif Untuk Penyedia
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : '.$jnsKontrak.' Sudah Disetujui : : .</div>';
          }
          break;

        case '51': // 4:Tolak SPK/PKS
          if ($typeid != '6') { // Pengelola Kontrak
            $html .= '<a onClick="prosesKontrak(\'4\',\''.$contractingrekananproses1id.'\',\'Kirim '.$jnsKontrak.' ke Penyedia ?\')" class="'.CLASS_BTN_SUCCESS.'"> <i class="fa fa-send"></i> Kirim Ke Penyedia </a>';
            $html .= '<div class="'.CLASS_BTN_DANGER.'" style="font-weight:bold; margin-top:1%">. : : '.$jnsKontrak.' Di Kembalikan Oleh Penyedia Silahkan revisi dan kirim kembali : : .</div>';
          } else { // Notif Untuk Penyedia
            $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : '.$jnsKontrak.' sedang di revisi : : .</div>';
          }
          break;

        default:
          $html .= '';
          break;
      }
    }

    // if ($skketerangan == 'PELAKSANAAN') {
    //   switch ($status) {
    //     case '6': // 6: Pelaksanaan Kontrak
    //       if ($typeid != '6') { // Pengelola Kontrak
    //         $html .= '<a onClick="prosesKontrak(\'100\',\''.$contractingrekananproses1id.'\',\'Lanjut ke Penutupan Kontrak ?\')" class="'.CLASS_BTN_WARNING.'"> <i class="fa fa-gavel"></i> Selesai Kontrak </a>';
    //       } else { // Notif Untuk Penyedia
    //         $html .= '<div class="alert alert-danger text-center" style="font-weight:bold; margin-top:1%">. : : Kontrak sedang Berjalan : : .</div>';
    //       }
    //       break;
    //     case '7': // 7: Perubahan Kontrak
    //       if ($typeid != '6') { // Pengelola Kontrak
    //         $url3 = $this->_CI->uri->segment(3, "");
    //         if ($url3 == 'contracting_pengelolaan_realisasi' || $url3 == 'contracting_pengelolaan_termin') {
    //           $html .= '<a onClick="prosesKontrak(\'100\',\''.$contractingrekananproses1id.'\',\'Lanjut ke Penutupan Kontrak ?\')" class="'.CLASS_BTN_WARNING.'"> <i class="fa fa-gavel"></i> Selesai Kontrak </a>';
    //         }
    //       } else { // Notif Untuk Penyedia
    //         $html .= '<div class="alert alert-danger text-center" style="font-weight:bold; margin-top:1%">. : : Perubahan Kontrak : : .</div>';
    //       }
    //       break;
    //     case '8': // 8: Penyesuaian Harga
    //       if ($typeid != '6') { // Pengelola Kontrak
    //         $url3 = $this->_CI->uri->segment(3, "");
    //         if ($url3 == 'contracting_pengelolaan_realisasi' || $url3 == 'contracting_pengelolaan_termin') {
    //           $html .= '<a onClick="prosesKontrak(\'100\',\''.$contractingrekananproses1id.'\',\'Lanjut ke Penutupan Kontrak ?\')" class="'.CLASS_BTN_WARNING.'"> <i class="fa fa-gavel"></i> Selesai Kontrak </a>';
    //         }
    //       } else { // Notif Untuk Penyedia
    //         $html .= '<div class="alert alert-danger text-center" style="font-weight:bold; margin-top:1%">. : : Perubahan Kontrak : : .</div>';
    //       }
    //       break;
    //     case '9': // 9: Keadaan Kahar
    //       if ($typeid != '6') { // Pengelola Kontrak
    //         $url3 = $this->_CI->uri->segment(3, "");
    //         if ($url3 == 'contracting_pengelolaan_realisasi' || $url3 == 'contracting_pengelolaan_termin') {
    //           $html .= '<a onClick="prosesKontrak(\'100\',\''.$contractingrekananproses1id.'\',\'Lanjut ke Penutupan Kontrak ?\')" class="'.CLASS_BTN_WARNING.'"> <i class="fa fa-gavel"></i> Selesai Kontrak </a>';
    //         }
    //       } else { // Notif Untuk Penyedia
    //         $html .= '<div class="alert alert-danger text-center" style="font-weight:bold; margin-top:1%">. : : Perubahan Kontrak : : .</div>';
    //       }
    //       break;
    //     case '10': // 10: Berakhir Kontrak
    //       if ($typeid != '6') { // Pengelola Kontrak
    //         $url3 = $this->_CI->uri->segment(3, "");
    //         if ($url3 == 'contracting_pengelolaan_realisasi' || $url3 == 'contracting_pengelolaan_termin') {
    //           $html .= '<a onClick="prosesKontrak(\'100\',\''.$contractingrekananproses1id.'\',\'Lanjut ke Penutupan Kontrak ?\')" class="'.CLASS_BTN_WARNING.'"> <i class="fa fa-gavel"></i> Selesai Kontrak </a>';
    //         }
    //       } else { // Notif Untuk Penyedia
    //         $html .= '<div class="alert alert-danger text-center" style="font-weight:bold; margin-top:1%">. : : Perubahan Kontrak : : .</div>';
    //       }
    //       break;
    //     case '11': // 11: Pemutusan Kontrak
    //       if ($typeid != '6') { // Pengelola Kontrak
    //         $url3 = $this->_CI->uri->segment(3, "");
    //         if ($url3 == 'contracting_pengelolaan_realisasi' || $url3 == 'contracting_pengelolaan_termin') {
    //           $html .= '<a onClick="prosesKontrak(\'100\',\''.$contractingrekananproses1id.'\',\'Lanjut ke Penutupan Kontrak ?\')" class="'.CLASS_BTN_WARNING.'"> <i class="fa fa-gavel"></i> Selesai Kontrak </a>';
    //         }
    //       } else { // Notif Untuk Penyedia
    //         $html .= '<div class="alert alert-danger text-center" style="font-weight:bold; margin-top:1%">. : : Perubahan Kontrak : : .</div>';
    //       }
    //       break;
    //     case '12': // 12: Pemberian Kesempatan
    //       if ($typeid != '6') { // Pengelola Kontrak
    //         $url3 = $this->_CI->uri->segment(3, "");
    //         if ($url3 == 'contracting_pengelolaan_realisasi' || $url3 == 'contracting_pengelolaan_termin') {
    //           $html .= '<a onClick="prosesKontrak(\'100\',\''.$contractingrekananproses1id.'\',\'Lanjut ke Penutupan Kontrak ?\')" class="'.CLASS_BTN_WARNING.'"> <i class="fa fa-gavel"></i> Selesai Kontrak </a>';
    //         }
    //       } else { // Notif Untuk Penyedia
    //         $html .= '<div class="alert alert-danger text-center" style="font-weight:bold; margin-top:1%">. : : Perubahan Kontrak : : .</div>';
    //       }
    //       break;
    //     case '13': // 13: Sanksi dan Denda
    //       if ($typeid != '6') { // Pengelola Kontrak
    //         $url3 = $this->_CI->uri->segment(3, "");
    //         if ($url3 == 'contracting_pengelolaan_realisasi' || $url3 == 'contracting_pengelolaan_termin') {
    //           $html .= '<a onClick="prosesKontrak(\'100\',\''.$contractingrekananproses1id.'\',\'Lanjut ke Penutupan Kontrak ?\')" class="'.CLASS_BTN_WARNING.'"> <i class="fa fa-gavel"></i> Selesai Kontrak </a>';
    //         }
    //       } else { // Notif Untuk Penyedia
    //         $html .= '<div class="aler alert-danger text-center" style="font-weight:bold; margin-top:1%">. : : Perubahan Kontrak : : .</div>';
    //       }
    //       break;

    //     default:
    //       $html .= '';
    //       break;
    //   }
    // }

    // if ($skketerangan == 'PENUTUPAN') {
    //   switch ($status) {
    //     case '100': // 100: Penutupan Kontrak
    //       if ($typeid != '6') { // bukan penyedia tapi Pengelola Kontrak
    //         $spkpksKontrak = new Contractingrekanan();
    //         $spkpksKontrak->selectViewPKSSPK(array("A.CONTRACTINGREKANANID" => $id));
    //         $spkpksKontrak->firstRow();

    //         $reqNilaiKontrak = $spkpksKontrak->getField('CR_NILAI_KONTRAK') ?: '-';
    //         $reqJnsKontrak = $spkpksKontrak->getField('JNS_KONTRAK') ?: '0';

    //         // if ($reqJnsKontrak == '1') { // Jika Kontrak PKS maka harus melalui persetutujuan pemeriksa kontrak atau > 1M
    //           $proses5 = new Contractingrekanan();
    //           $proses5->selectProses5(array("A.CONTRACTINGREKANANID" => $id));
    //           $proses5->firstRow();
    //           $reqApprovalPemeriksa = $proses5->getField('CR_PEMERIKSA_APPROVAL');

    //           // if ($reqJnsKontrak == '1' && $typeid == '20') { // Jika Kontrak PKS maka harus melalui persetutujuan pemeriksa kontrak atau > 1M dan Pemeriksa Kontrak
    //           //   // code...
    //           //   if ($reqApprovalPemeriksa == '1') {
    //           //     $html .= '<div class="alert alert-info text-center" style="font-weight:bold; margin-top:1%">. : : Kontrak sudah di approve : : .</div>';
    //           //   } else {
    //           //     $proses5Total = new Contractingrekanan();
    //           //     $proses5Total->selectProses5(array("A.CONTRACTINGREKANANID" => $id));
    //           //     if ($proses5Total->countRow() > 0) {
    //           //       $html .= '<a onClick="approvalKontrak('.$id.')" class="'.CLASS_BTN_WARNING.'"> <i class="fa fa-gavel"></i> Approve Kontrak ?</a>';
    //           //     } else {
    //           //       $html .= '<div class="alert alert-danger text-center" style="font-weight:bold; margin-top:1%">. : : BAST Belum di isi oleh Pengelola Kontrak : : .</div>';
    //           //     }
    //           //   }
    //           // } else { // Jika SPK Tidak perlu approval pemeriksa
    //             if ($typeid == '12') { // Pengelola Kontrak
    //               $proses5Total = new Contractingrekanan();
    //               $proses5Total->selectProses5(array("A.CONTRACTINGREKANANID" => $id));
    //               if ($proses5Total->countRow() > 0) {
    //                 // if ($reqJnsKontrak == '1' && $reqApprovalPemeriksa == '1') { // PKS & sudah approval pemeriksa kontrak
    //                   $html .= '<a onClick="prosesKontrak(\'200\',\''.$contractingrekananproses1id.'\',\'Apakah anda yakin kontrak sudah selesai ?\')" class="'.CLASS_BTN_WARNING.'"> <i class="fa fa-gavel"></i> Kontrak Selesai ?</a>';
    //                 } else if ($reqJnsKontrak == '0') {
    //                   $html .= '<a onClick="prosesKontrak(\'200\',\''.$contractingrekananproses1id.'\',\'Apakah anda yakin kontrak sudah selesai ?\')" class="'.CLASS_BTN_WARNING.'"> <i class="fa fa-gavel"></i> Kontrak Selesai ?</a>';
    //                 }
    //               } else {
    //                 $html .= '<div class="alert alert-danger text-center" style="font-weight:bold; margin-top:1%">. : : BAST Belum di isi oleh Pengelola Kontrak : : .</div>';
    //               }
    //             // }
    //           // }


    //         // }


    //       } else { // Notif Untuk Penyedia
    //         $html .= '<div class="alert alert-danger text-center" style="font-weight:bold; margin-top:1%">. : : Kontrak sedang proses Serah Terima Pekerjaan : : .</div>';
    //       }
    //       break;

    //     default:
    //       $html .= '';
    //       break;
    //   }
    // }

    $html .= '';

    return $html;
  }

  public function getTableFile($reqId,$statement=null)
  {

    $this->_CI->load->model("Contractingfile");
    include_once("functions/date.func.php");

    $contractingfile = new Contractingfile();
    $contractingfile->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId),-1,-1,$statement);

    $html  =  '';
    $html .= '
    <style type="text/css">
      small { font-weight: bold; font-size: 11.5px }
      sup { font-style: italic; color: red;}
      tr.backcolornew {
        background: #103A6C !important;
        color: #fff;
      }
    </style>
      <thead>
        <tr class="backcolornew">
          <th class="text-center" width="10px">No<br>&nbsp;</th>
          <th class="text-center">Nama Dokumen<br>&nbsp;</th>
          <th class="text-center">Keterangan<br>&nbsp;</th>
          <th class="text-center" width="20px">Tahap<br>&nbsp;</th>';
    if ($this->_CI->USER_TYPE_ID != '20') { // Bukan Pemeriksa Kontrak
    $html .= '
          <th class="text-center" width="10px">Kirim ke<br> Penyedia</th>';
    }

    $html .= '
          <th class="text-center" width="10px">Aksi<br>&nbsp;</th>
        </tr>
      </thead>';
      $html .= '
      <tbody> ';
        $no=1;
        if ($contractingfile->countRow() > 0) {
          while($contractingfile->nextRow())
          {
            switch ($contractingfile->getField('contractingprosesid')) {
              case '1': // Inisisai
                $tahap = '<span class="badge badge-info">'.str_replace('KONTRAK','',$contractingfile->getField('proses_str')).'</span>'; break;
              case '2': // Perencanaan
                $tahap = '<span class="badge badge-primary">'.str_replace('KONTRAK','',$contractingfile->getField('proses_str')).'</span>'; break;
              case '3': // Pelaksanaan
                $tahap = '<span class="badge badge-warning">'.str_replace('KONTRAK','',$contractingfile->getField('proses_str')).'</span>'; break;
              case '4': // Monitoring
                $tahap = '<span class="badge badge-success">'.str_replace('KONTRAK','',$contractingfile->getField('proses_str')).'</span>'; break;
              case '5': // Penutupan
                $tahap = '<span class="badge badge-danger">'.str_replace('KONTRAK','',$contractingfile->getField('proses_str')).'</span>'; break;
              case '6': // Selesai
                $tahap = '<span class="badge badge-dark">'.str_replace('KONTRAK','',$contractingfile->getField('proses_str')).'</span>'; break;

              default:
                break;
            }

            if ($contractingfile->getField('file_nama_encrypt') != '' && file_exists($contractingfile->getField('file_path').'/'.$contractingfile->getField('file_nama_encrypt'))) {
              $linkDok = '<a href="'.$contractingfile->getField('file_path').$contractingfile->getField('file_nama_encrypt').'" target="_blank"><span class="badge badge-info"><i class="fa fa-download"></i> Download</span> '.$contractingfile->getField('file_nama').'</a>';
            } else {
              $linkDok = $contractingfile->getField('file_nama');
            }

            if ($contractingfile->getField('file_publish_penyedia') == '1') {
              $publish = '<a onClick="publishFile(\'contracting_json/publishFile/\', '.$contractingfile->getField("CONTRACTINGFILEID").',\'0\')" class="fa fa-check btn-xs btn-primary" style="padding:5px; border-radius:4px; color:#fff"> Ya</a>';

              $this->_CI->load->model("PaketDokumenKontrakDownload");
              $dok_kontrak_check = new PaketDokumenKontrakDownload();
              $dok_kontrak_check->selectByParamsGroup(array("A.CONTRACTINGREKANANID" => $reqId, "A.CONTRACTINGFILEID" => $contractingfile->getField("CONTRACTINGFILEID")));

              $badge = array('badge-success','badge-dark','badge-primary','badge-danger','badge-warning');
              $noUrut = 0;
              while($dok_kontrak_check->nextRow())
              {
                $publish .= '<span class="badge '.$badge[$noUrut].' mt-1"><small>Sudah didownload oleh: <br>'.$dok_kontrak_check->getField("NAMA").'</small></span>';
                $noUrut++;
              }

            } else {
              $publish = '<a onClick="publishFile(\'contracting_json/publishFile/\', '.$contractingfile->getField("CONTRACTINGFILEID").',\'1\')" class="fa fa-times btn-xs btn-danger" style="padding:5px; border-radius:4px; color:#fff"> <small>Tidak</small></a>';
            }

      $html .= '
            <tr>
              <td class="text-center">'.$no.'</td>
              <td>
                '.$linkDok.'<br>
                <small><i>'.getFormattedDateShort2(dateTimeToPageCheck($contractingfile->getField('created_date'))).'</i></small><br>
                <span class="badge badge-primary">Jenis Dok: '.$contractingfile->getField('file_jenis').'</span> <br>
                <small><i>Oleh: '.$contractingfile->getField('created_by_str').'</i></small>
              </td>
              <td>'.$contractingfile->getField('file_keterangan').'</td>
              <td class="text-center">'.$tahap.'</td>';
      if ($this->_CI->USER_TYPE_ID != '20') { // Bukan Pemeriksa Kontrak
      $html .= '
              <td class="text-center">'.$publish.'</td>';
      }
      if ($contractingfile->getField('created_by') == $this->_CI->USER_LOGIN_ID) {
      $html .= '<td class="text-center"><a onClick="deleteData(\'contracting_json/deleteFile/\', '.$contractingfile->getField("CONTRACTINGFILEID").')"><span class="fa fa-trash btn-xs btn-danger" style="padding:5px; border-radius:4px"></span></a></td>';
      } else {
      $html .= '<td class="text-center">-</td>';
      }
      $html .= '
            </tr>
           ';
            $no++;
          }
        } else {
          // echo '<tr><td colspan="6">. : : Tidak ada dokumen : : .</td></tr>';
          // $html .= '<tr><td>. : : Tidak ada dokumen : : .</td></tr>';
        }
    $html .= '
      </tbody>';

    return $html;
  }

  public function getTableFileMulti($reqId,$statement=null)
  {

    $this->_CI->load->model("Contractingfile");

    $contractingfile = new Contractingfile();
    $contractingfile->selectByParamsMulti(array("A.CONTRACTINGREKANANID" => $reqId),-1,-1,$statement);

    $html  =  '';
    $html .= '
      <style type="text/css">
        small { font-weight: bold; font-size: 11.5px }
        sup { font-style: italic; color: red;}
        tr.backcolornew {
          background: #103A6C !important;
          color: #fff;
        }
      </style>
      <thead>
        <tr class="backcolornew">
          <th class="text-center" width="10px">No<br>&nbsp;</th>
          <th class="text-center">Nama Dokumen<br>&nbsp;</th>
          <th class="text-center">Keterangan<br>&nbsp;</th>
          <th class="text-center" width="20px">Tahap<br>&nbsp;</th>';
    if ($this->_CI->USER_TYPE_ID != '20') { // Bukan Pemeriksa Kontrak
    $html .= '
          <th class="text-center" width="10px">Kirim ke<br> Penyedia</th>';
    }

    $html .= '
          <th class="text-center" width="10px">Aksi<br>&nbsp;</th>
        </tr>
      </thead>';
      $html .= '
      <tbody> ';
        $no=1;
        if ($contractingfile->countRow() > 0) {
          while($contractingfile->nextRow())
          {
            switch ($contractingfile->getField('contractingprosesid')) {
              case '1': // Inisisai
                $tahap = '<span class="badge badge-info">'.str_replace('KONTRAK','',$contractingfile->getField('proses_str')).'</span>'; break;
              case '2': // Perencanaan
                $tahap = '<span class="badge badge-primary">'.str_replace('KONTRAK','',$contractingfile->getField('proses_str')).'</span>'; break;
              case '3': // Pelaksanaan
                $tahap = '<span class="badge badge-warning">'.str_replace('KONTRAK','',$contractingfile->getField('proses_str')).'</span>'; break;
              case '4': // Monitoring
                $tahap = '<span class="badge badge-success">'.str_replace('KONTRAK','',$contractingfile->getField('proses_str')).'</span>'; break;
              case '5': // Penutupan
                $tahap = '<span class="badge badge-danger">'.str_replace('KONTRAK','',$contractingfile->getField('proses_str')).'</span>'; break;
              case '6': // Selesai
                $tahap = '<span class="badge badge-dark">'.str_replace('KONTRAK','',$contractingfile->getField('proses_str')).'</span>'; break;

              default:
                break;
            }

            if ($contractingfile->getField('file_nama_encrypt') != '' && file_exists($contractingfile->getField('file_path').'/'.$contractingfile->getField('file_nama_encrypt'))) {
              $linkDok = '<a href="'.$contractingfile->getField('file_path').'/'.$contractingfile->getField('file_nama_encrypt').'" target="_blank">'.$contractingfile->getField('file_nama').'</a>';
            } else {
              $linkDok = $contractingfile->getField('file_nama');
            }

            if ($contractingfile->getField('file_publish_penyedia') == '1') {
              $publish = '<a onClick="publishFile(\'contracting_json/publishFile/\', '.$contractingfile->getField("CONTRACTINGFILEID").',\'0\')" class="fa fa-check btn-xs btn-primary" style="padding:5px; border-radius:4px; color:#fff"></a><br>';
              if ($contractingfile->getField('created_by') == $this->_CI->USER_LOGIN_ID) {
                $publish .= '<small style="font-size:10px">'.$contractingfile->getField('nama').'</small>';
              }
            } else {
              $publish = '<a onClick="publishFile(\'contracting_json/publishFile/\', '.$contractingfile->getField("CONTRACTINGFILEID").',\'1\')" class="fa fa-times btn-xs btn-danger" style="padding:5px; border-radius:4px; color:#fff"></a>';
            }

      $html .= '
            <tr>
              <td class="text-center">'.$no.'</td>
              <td>
                '.$linkDok.'<br>
                <span class="badge badge-primary">Jenis Dok: '.$contractingfile->getField('file_jenis').'</span> <br>
                <small><i>Oleh: '.$contractingfile->getField('created_by_str').'</i></small>
              </td>
              <td>'.$contractingfile->getField('file_keterangan').'</td>
              <td class="text-center">'.$tahap.'</td>';
      if ($this->_CI->USER_TYPE_ID != '20') { // Bukan Pemeriksa Kontrak
      $html .= '
              <td class="text-center">'.$publish.'</td>';
      }
      if ($contractingfile->getField('created_by') == $this->_CI->USER_LOGIN_ID) {
      $html .= '<td class="text-center"><a onClick="deleteData(\'contracting_json/deleteFile/\', '.$contractingfile->getField("CONTRACTINGFILEID").')"><span class="fa fa-trash btn-xs btn-danger" style="padding:5px; border-radius:4px"></span></a></td>';
      } else {
      $html .= '<td class="text-center">-</td>';
      }
      $html .= '
            </tr>
           ';
            $no++;
          }
        } else {
          // echo '<tr><td colspan="6">. : : Tidak ada dokumen : : .</td></tr>';
          // $html .= '<tr><td>. : : Tidak ada dokumen : : .</td></tr>';
        }
    $html .= '
      </tbody>';

    return $html;
  }

  public function getTableFilePenyedia($reqId,$statement=null)
  {

    $this->_CI->load->model("Contractingfile");
    include_once("functions/date.func.php");

    $contractingfile = new Contractingfile();
    $contractingfile->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId),-1,-1,$statement);
    // echo $contractingfile->query; die;

    $html  =  '';
    $html .= '
      <style type="text/css">
        small { font-weight: bold; font-size: 11.5px }
        sup { font-style: italic; color: red;}
        tr.backcolornew {
          background: #103A6C !important;
          color: #fff;
        }
      </style>
      <thead>
        <tr class="backcolornew">
          <th class="text-center" width="10px">No<br>&nbsp;</th>
          <th class="text-center">Nama Dokumen<br>&nbsp;</th>
          <th class="text-center">Keterangan<br>&nbsp;</th>
          <th class="text-center" width="10px">Aksi<br>&nbsp;</th>
        </tr>
      </thead>';
      $html .= '
      <tbody> ';
        $no=1;
        if ($contractingfile->countRow() > 0) {
          while($contractingfile->nextRow())
          {
            if ($contractingfile->getField('file_nama_encrypt') != '' && file_exists($contractingfile->getField('file_path').'/'.$contractingfile->getField('file_nama_encrypt'))) {
              // $linkDok = '<a href="'.$contractingfile->getField('file_path').'/'.$contractingfile->getField('file_nama_encrypt').'" target="_blank">'.$contractingfile->getField('file_nama').'</a>';
              $linkDok = '<a target="_blank" href="download_dokumen_json/getDokContract/'.$contractingfile->getField('contractingfileid').'/'.$reqId.'/'.$contractingfile->getField('file_nama_encrypt').'"><span class="badge badge-info"><i class="fa fa-download"></i> Download</span> '.$contractingfile->getField('file_nama').'</a>';
            } else {
              $linkDok = $contractingfile->getField('file_nama');
            }

      $html .= '
            <tr>
              <td class="text-center">'.$no.'</td>
              <td>
                '.$linkDok.'<br>
                <small><i>'.getFormattedDateShort2(dateTimeToPageCheck($contractingfile->getField('created_date'))).'</i></small><br>
                <span class="badge badge-primary">Jenis Dok: '.$contractingfile->getField('file_jenis').'</span>';
      if ($contractingfile->getField('created_by') == $this->_CI->USER_LOGIN_ID) {
      $html .= '<br> <small><i>Oleh: '.$contractingfile->getField('created_by_str').'</i></small>';
      }
      $html .= '</td>
              <td>'.$contractingfile->getField('file_keterangan').'</td>';
              if ($contractingfile->getField('created_by') == $this->_CI->USER_LOGIN_ID) {
      $html .= '<td class="text-center"><a onClick="deleteData(\'contracting_json/deleteFile/\', '.$contractingfile->getField("CONTRACTINGFILEID").')"><span class="fa fa-trash btn-xs btn-danger" style="padding:5px; border-radius:4px"></span></a></td>';
      } else {
      $html .= '<td class="text-center">-</td>';
      }
      $html .= '
            </tr>
           ';
            $no++;
          }
        } else {
          // $html .= '<tr><td></td><td>. : : Tidak ada dokumen : : .</td><td></td><td></td></tr>';
        }
    $html .= '
      </tbody>';

    return $html;
  }

  public function getDokumenPendukung($paketId,$pemenangid=null)
  {
    $html  = '';
    $this->_CI->load->model(array("PermohonanPaketFile","PermohonanPaketAnalisaFile"));
    $this->_CI->load->model("PaketDokumen");
    $this->_CI->load->model("Paket");
    $this->_CI->load->model("PaketRekanan");

    // Dokumen Pendukung dari Proses Pengadaan yang sudah dilaksanakan

    $paket = new Paket();
    $permohonan_paket_file = new PermohonanPaketFile();
    $permohonan_paket_analisa_file = new PermohonanPaketAnalisaFile();
    $paket_dokumen = new PaketDokumen();
    $paket_dokumen_ba_evaluasi = new PaketDokumen();
    $paket_rekanan = new PaketRekanan();
    $paket_dokumen_penawaran = new PaketDokumen();

    // Password Penawaran
    $paket_rekanan->selectByParams2(array("A.PAKET_ID" => $paketId, "A.REKANAN_ID" => $pemenangid), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 AND A.KIRIM_PENAWARAN = 1 ");
    $paket_rekanan->firstRow();

    // Dok. Permohonan
    $paket->selectByParamsMonitoring(array(), -1, -1, " AND PAKET_ID = '".$paketId."' ");
    $paket->firstRow();
    $reqPermohonanPaket = $paket->getField("PERMOHONAN_PAKET_ID");
    $reqPermohonanPaketAnalisa = $paket->getField("PERMOHONAN_PAKET_ANALISA_ID");
    $permohonan_paket_analisa_file->selectByParams(array("PERMOHONAN_PAKET_ANALISA_ID" => coalesce($reqPermohonanPaketAnalisa, 0)));
    // Dok. BA Aanwijzing
    $paket_dokumen->selectByParams(array("PAKET_ID" => $paketId),-1,-1," AND (JENIS_DOKUMEN = 'LELANG' OR JENIS_DOKUMEN = 'BERITA_ACARA_AANWIJZING') ");
    // Dok. BA Evaluasi Penawaran
    $paket_dokumen_ba_evaluasi->selectByParams(array("PAKET_ID" => $paketId),-1,-1," AND (JENIS_DOKUMEN = 'EVALUASI_PENAWARAN_ADMINISTRASI' OR JENIS_DOKUMEN = 'EVALUASI_PENAWARAN_TEKNIS' OR JENIS_DOKUMEN = 'EVALUASI_PENAWARAN_HARGA') ");
    // Dok. Penawaran
    $paket_dokumen_penawaran->selectByParams(array("PAKET_ID" => $paketId, "REKANAN_USER_ID" => $pemenangid), -1, -1, " AND JENIS_DOKUMEN LIKE 'PENAWARAN%' ");


    $html .= '
              <script type="text/javascript">
                function myFunction(a) {
                  var id = "myPass"+a;
                  var copyText = document.getElementById(id);
                  copyText.select();
                  copyText.setSelectionRange(0, 99999)
                  document.execCommand("copy");
                  // alert("Copied the text: " + copyText.value);
                  alert("Password disalin "+copyText.value);
                }
              </script>
              <table class="table-bordered table mb-0">
                <thead>
                  <tr class="judul-kolom">
                    <th class="text-center" style="width: 90%">Nama Dokumen</th>
                    <th class="text-center">Download</th>
                  </tr>
                </thead>
                <tbody id="tbodyPermohonanPaketFile">';
                    // Dok. Permohonan Paket
                    while($permohonan_paket_analisa_file->nextRow())
                    {

    $html .= '      <tr>
                       <td>'.$permohonan_paket_analisa_file->getField("JUDUL").'</td>
                       <td style="text-align: center;">';
                          if ($permohonan_paket_analisa_file->getField("PATH_FILE")) {
    $html .= '            <a href="uploads/permohonan_paket/'.$permohonan_paket_analisa_file->getField("PATH_FILE").'" target="new">
                            <i class="fa fa-download"></i>
                          </a>';
                          }
    $html .= '          </td>
                     </tr>';
                     }
                     // Dok. dokumen pengadaan
                     while($paket_dokumen->nextRow())
                     {
    $html .= '      <tr>
                       <td>'.$paket_dokumen->getField("NAMA").'</td>
                       <td style="text-align: center;">';
                          if ($paket_dokumen->getField("PATH_FILE")) {
    $html .= '            <a href="uploads/lelang/'.$paket_dokumen->getField("PATH_FILE").'" target="new">
                            <i class="fa fa-download"></i>
                          </a>';
                          }
    $html .= '          </td>
                     </tr>';
                     }
                     if ($paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD")) {
    $html .= '      <tr>
                       <td>
                        Password Dok. Penawaran <br>
                        <a onClick="return myFunction(\'1\')">
                          <div class="input-group" style="margin-top:1%">
                            <div class="input-group-prepend">
                              <i class="fa fa-copy"></i> &nbsp;&nbsp;
                            </div>
                            <input class="form-control" type="text" value="'.$paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD").'" id="myPass1" style="border:none; height:10px; cursor:copy;" readonly>
                          </div>
                        </a>
                        ';
                          if ($paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD2")) {
    $html .= '            <br>
                            <a onClick="return myFunction(\'2\')">
                            <div class="input-group" style="margin-top:1%">
                              <div class="input-group-prepend">
                                <i class="fa fa-copy"></i> &nbsp;&nbsp;
                              </div>
                              <input class="form-control" type="text" value="'.$paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD2").'" id="myPass2" style="border:none; height:10px; cursor:copy;" readonly>
                            </div>
                          </a>';
                          }
    $html .= '          </td>';
    $html .= '          <td style="text-align: center;"> <span class="fa fa-lock"></span></td>';
    $html .= '         </tr>';
                     }
                     // Dok. Penawaran Pemenang
                     while($paket_dokumen_penawaran->nextRow())
                     {
    $html .= '      <tr>
                       <td>'.$paket_dokumen_penawaran->getField("NAMA").'</td>
                       <td style="text-align: center;">';
                          if ($paket_dokumen_penawaran->getField("PATH_FILE")) {
    $html .= '            <a href="uploads/penawaran/'.$paket_dokumen_penawaran->getField("PATH_FILE").'" target="new">
                            <i class="fa fa-download"></i>
                          </a>';
                          }
    $html .= '          </td>
                     </tr>';
                     }

                     // Dok. BA Evaluasi
                     while($paket_dokumen_ba_evaluasi->nextRow())
                     {
    $html .= '      <tr>
                       <td>Berita Acara '.$paket_dokumen_ba_evaluasi->getField("NAMA").'</td>
                       <td style="text-align: center;">';
                          if ($paket_dokumen_ba_evaluasi->getField("PATH_FILE")) {
    $html .= '            <a href="uploads/penawaran/'.$paket_dokumen_ba_evaluasi->getField("PATH_FILE").'" target="new">
                            <i class="fa fa-download"></i>
                          </a>';
                          }
    $html .= '          </td>
                     </tr>';
                     }

                    $paket_dokumen_pemenang = new PaketDokumen();
                    $paket_dokumen_pemenang->selectByParams(array("PAKET_ID" => $paketId, "JENIS_DOKUMEN" => "PENETAPAN_PEMENANG"));
                    $paket_dokumen_pemenang->firstRow();
                    if($paket_dokumen_pemenang->getField("PATH_FILE"))
                    { 
    $html .= '      <tr>
                       <td>File Penetapan Pemenang</td>
                       <td style="text-align: center;">';
    $html .= '            <a href="uploads/penawaran/'.$paket_dokumen_pemenang->getField("PATH_FILE").'" target="new">
                            <i class="fa fa-download"></i>
                          </a>';
    $html .= '          </td>
                     </tr>';
                    } 

    $html .= '    </tbody>
              </table>
              ';

    return $html;
  }

  public function getDokumenPendukungMulti($paketId,$pemenangid=null)
  {
    $html  = '';
    $this->_CI->load->model("PermohonanPaketFile");
    $this->_CI->load->model("PaketDokumen");
    $this->_CI->load->model("Paket");
    $this->_CI->load->model("PaketRekanan");

    // Dokumen Pendukung dari Proses Pengadaan yang sudah dilaksanakan

    $paket = new Paket();
    $permohonan_paket_file = new PermohonanPaketFile();
    $paket_dokumen = new PaketDokumen();
    $paket_dokumen_ba_evaluasi = new PaketDokumen();
    $paket_rekanan = new PaketRekanan();
    $paket_dokumen_penawaran = new PaketDokumen();

    // Password Penawaran
    $paket_rekanan->selectByParams2(array("A.PAKET_ID" => $paketId, "A.REKANAN_ID" => $pemenangid), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 AND A.KIRIM_PENAWARAN = 1 ");
    $paket_rekanan->firstRow();

    // Dok. Permohonan
    $paket->selectByParamsMonitoring(array(), -1, -1, " AND PAKET_ID = '".$paketId."' ");
    $paket->firstRow();
    $reqPermohonanPaket = $paket->getField("PERMOHONAN_PAKET_ID");
    $permohonan_paket_file->selectByParams(array("PERMOHONAN_PAKET_ID" => coalesce($reqPermohonanPaket, 0)));

    // Dok. BA Aanwijzing
    $paket_dokumen->selectByParams(array("PAKET_ID" => $paketId),-1,-1," AND (JENIS_DOKUMEN = 'LELANG' OR JENIS_DOKUMEN = 'BERITA_ACARA_AANWIJZING') ");
    // Dok. BA Evaluasi Penawaran
    $paket_dokumen_ba_evaluasi->selectByParams(array("PAKET_ID" => $paketId),-1,-1," AND (JENIS_DOKUMEN = 'EVALUASI_PENAWARAN_ADMINISTRASI' OR JENIS_DOKUMEN = 'EVALUASI_PENAWARAN_TEKNIS' OR JENIS_DOKUMEN = 'EVALUASI_PENAWARAN_HARGA') ");
    // Dok. Penawaran
    $paket_dokumen_penawaran->selectByParams(array("PAKET_ID" => $paketId, "REKANAN_USER_ID" => $pemenangid), -1, -1, " AND JENIS_DOKUMEN LIKE 'PENAWARAN%' ");



    $html .= '
              <script type="text/javascript">
                function myFunction(a) {
                  var id = "myPass"+a;
                  var copyText = document.getElementById(id);
                  copyText.select();
                  copyText.setSelectionRange(0, 99999)
                  document.execCommand("copy");
                  // alert("Copied the text: " + copyText.value);
                  alert("Password disalin "+copyText.value);
                }
              </script>
              <table class="table-bordered table mb-0">
                <thead>
                  <tr class="judul-kolom">
                    <th class="text-center" style="width: 90%">Nama Dokumen</th>
                    <th class="text-center">Download</th>
                  </tr>
                </thead>
                <tbody id="tbodyPermohonanPaketFile">';
                    // Dok. Permohonan Paket
                    while($permohonan_paket_file->nextRow())
                    {

    $html .= '      <tr>
                       <td>'.$permohonan_paket_file->getField("JUDUL").'</td>
                       <td style="text-align: center;">';
                          if ($permohonan_paket_file->getField("PATH_FILE")) {
    $html .= '            <a href="uploads/permohonan_paket/'.$permohonan_paket_file->getField("PATH_FILE").'" target="new">
                            <i class="fa fa-download"></i>
                          </a>';
                          }
    $html .= '          </td>
                     </tr>';
                     }
                     // Dok. dokumen pengadaan
                     while($paket_dokumen->nextRow())
                     {
    $html .= '      <tr>
                       <td>'.$paket_dokumen->getField("NAMA").'</td>
                       <td style="text-align: center;">';
                          if ($paket_dokumen->getField("PATH_FILE")) {
    $html .= '            <a href="uploads/lelang/'.$paket_dokumen->getField("PATH_FILE").'" target="new">
                            <i class="fa fa-download"></i>
                          </a>';
                          }
    $html .= '          </td>
                     </tr>';
                     }
                     if ($paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD")) {
    $html .= '      <tr>
                       <td>
                        Password Dok. Penawaran <br>
                        <a onClick="return myFunction(\''.$pemenangid.'\')">
                          <div class="input-group" style="margin-top:1%">
                            <div class="input-group-prepend">
                              <i class="fa fa-copy"></i> &nbsp;&nbsp;
                            </div>
                            <input class="form-control" type="text" value="'.$paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD").'" id="myPass'.$pemenangid.'" style="border:none; height:10px; cursor:copy;" readonly>
                          </div>
                        </a>
                        ';
                          if ($paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD2")) {
    $html .= '            <br>
                            <a onClick="return myFunction(\'2\')">
                            <div class="input-group" style="margin-top:1%">
                              <div class="input-group-prepend">
                                <i class="fa fa-copy"></i> &nbsp;&nbsp;
                              </div>
                              <input class="form-control" type="text" value="'.$paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD2").'" id="myPass2" style="border:none; height:10px; cursor:copy;" readonly>
                            </div>
                          </a>';
                          }
    $html .= '          </td>';
    $html .= '          <td style="text-align: center;"> <span class="fa fa-lock"></span></td>';
    $html .= '         </tr>';
                     }
                     // Dok. Penawaran Pemenang
                     while($paket_dokumen_penawaran->nextRow())
                     {
    $html .= '      <tr>
                       <td>'.$paket_dokumen_penawaran->getField("NAMA").'</td>
                       <td style="text-align: center;">';
                          if ($paket_dokumen_penawaran->getField("PATH_FILE")) {
    $html .= '            <a href="uploads/penawaran/'.$paket_dokumen_penawaran->getField("PATH_FILE").'" target="new">
                            <i class="fa fa-download"></i>
                          </a>';
                          }
    $html .= '          </td>
                     </tr>';
                     }

                     // Dok. BA Evaluasi
                     while($paket_dokumen_ba_evaluasi->nextRow())
                     {
    $html .= '      <tr>
                       <td>Berita Acara '.$paket_dokumen_ba_evaluasi->getField("NAMA").'</td>
                       <td style="text-align: center;">';
                          if ($paket_dokumen_ba_evaluasi->getField("PATH_FILE")) {
    $html .= '            <a href="uploads/penawaran/'.$paket_dokumen_ba_evaluasi->getField("PATH_FILE").'" target="new">
                            <i class="fa fa-download"></i>
                          </a>';
                          }
    $html .= '          </td>
                     </tr>';
                     }

    $html .= '    </tbody>
              </table>
              ';

    return $html;
  }

  function jenisKontrak($value='')
  {
    switch ($value) {
      case '0': return "SPK";
        break;

      case '1': return "Surat Perjanjian";
        break;

      case '3': return "Surat Pesanan";
        break;

      default:
        return '';
        break;
    }
  }

  function getNilaiKontrakPenyedia($statement)
  {
    $this->_CI->load->model("Contracting");
    $aa = new Contracting();

    // Password Penawaran
    $aa->nilaiSuratPesanan($statement, -1, -1);
    $aa->firstRow();
    $total = $aa->getField("TOTAL");
    $nilai_kontrak = $aa->getField("CR_NILAI_KONTRAK");
    $sisa = $aa->getField("SISA");
    $penyedia = $aa->getField("PENYEDIA");

    return array('total' => $total, 'nilai_kontrak' => $nilai_kontrak, 'sisa' => $sisa);
  }

  function getNilaiKontrakPenyediaByNilai($statement)
  {
    $this->_CI->load->model("Contracting");
    $aa = new Contracting();

    // Password Penawaran
    $aa->getNilaiKontrakPenyediaByNilai($statement, -1, -1);
    $aa->firstRow();
    $total = $aa->getField("TOTAL");
    $nilai_kontrak = $aa->getField("NILAI_KONTRAK");
    $sisa = $aa->getField("SISA");

    return array('total' => $total, 'nilai_kontrak' => $nilai_kontrak, 'sisa' => $sisa);
  }

  function cekTagihanSelesai($reqId)
  {

    $this->_CI->load->model(array("Contractingpayment","Contractingdeliverable"));
    $levelKontrak = $this->_CI->LEVEL_KONTRAK;

    if ($levelKontrak == '2') // Pengendali
    { 
      // harus selesai realisasi
      $cekTotal = new Contractingdeliverable();
      $cekSelesai = new Contractingdeliverable();
      $cekTotal->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId)); 
      $cekSelesai->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId, "STATUS" => "Selesai"));
      if ($cekSelesai->countRow() == $cekTotal->countRow()) {
        return true;
      } else {
        return false;
      }
    } else if ($levelKontrak == '3') { // Penyelesaian
      // harus selesai pembayaran
      $cekTotal = new Contractingpayment();
      $cekSelesai = new Contractingpayment();
      $cekTotal->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId)); 
      $cekSelesai->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId, "PAY_STATUS" => "Selesai"));
      if ($cekSelesai->countRow() == $cekTotal->countRow()) {
        return true;
      } else {
        return false;
      }
    } else {
      // harus selesai pembayaran
      $cekTotal = new Contractingpayment();
      $cekSelesai = new Contractingpayment();
      $cekTotal->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId)); 
      $cekSelesai->selectByParams(array("A.CONTRACTINGREKANANID" => $reqId, "PAY_STATUS" => "Selesai"));
      if ($cekSelesai->countRow() == $cekTotal->countRow()) {
        return true;
      } else {
        return false;
      }
    }

  }

}
