<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

class libjadwal
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
    $this->_CI->HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
  } 

  function getJadwal($paket_id)
  {
    $this->_CI->load->model("PaketTahap");
    $paket_tahap_jadwal = new PaketTahap();

    $paket_tahap_jadwal->selectByParamsJadwal(array("TAMPILKAN" => "1"), -1, -1, " AND PAKET_ID = '".$paket_id."' ");

    $i=1;
    $style="gelap";
    
    $html = '';
    if ($paket_tahap_jadwal->countRow() <= 0) {
      $html .= '<td width="100%">. : : Tidak Ada Jadwal : : .</td>';
    } else
    {
      while($paket_tahap_jadwal->nextRow())
      { 
        $now = $paket_tahap_jadwal->getField("AKTIF");

        // $tgl_awal   = $paket_tahap_jadwal->getField("TANGGAL_AWAL"); 
        // $tgl_akhir  = strtotime($paket_tahap_jadwal->getField("TANGGAL_AKHIR"));
        $total_perubahan = 0;

        if ($paket_tahap_jadwal->getField("TANGGAL_AWAL") != '' || $paket_tahap_jadwal->getField("TANGGAL_AKHIR") != '') 
        { 
          $html .= ' <td width="60%"';
            if($now == 1) { 
              $html .= ' style="font-weight:bold; color: #cf252d !important";'; 
            } 
          $html .= ' >';

          // CEK PERUBAHAN
          $this->_CI->load->model("Paket");
          // CEK BERAPA KALI PERUBAHAN
          $cek_total_perubahan = new Paket();
          $cek_total_perubahan->selectByParamsTotalPerubahan(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.'');
          $cek_total_perubahan->firstRow();

          $total_perubahan = $this->__cekPerubahan($cek_total_perubahan->getField('RESCHEDULE_KE'), $paket_id, $paket_tahap_jadwal->getField("NAMA"));

          // END CEK PERUBAHAN

          if ($total_perubahan > 0) {
            $html .= '<div class="pull-right badge badge-primary"> '.$total_perubahan.' kali perubahan</div>';
          }

          $html .= ''.$paket_tahap_jadwal->getField("NAMA").''; 
          if($paket_tahap_jadwal->getField("HADIR") == 1) { 
            $html .= ' <b style="color: blue; font-size: 1.1em">*</b>';
            }
            $html .= '</td>';

            $html .= '<td width="35%"'; 
              if($now == 1) { 
                $html .= ' style="font-weight:bold"';
              }
              $html .= ' >';
              $html .= ' <div class="badge badge-square">';
              $html .= ' <span class="info"'; 
              if($now == 1) {
                $html .= 'style="font-weight:bold; color: #cf252d !important"'; 
              } else { 
                $html .= 'style="color:#000 !important"';
              }
              $html .= ' >'; 

              if ($paket_tahap_jadwal->getField("TANGGAL_AWAL") != '' || $paket_tahap_jadwal->getField("TANGGAL_AKHIR") != '') {
                $html .= '<i class="fa fa-clock-o font-medium-2"></i> ';
              }
              $html .= ''.getFormattedDate($paket_tahap_jadwal->getField("TANGGAL_AWAL")).'';
              $html .= ''.addWIB($paket_tahap_jadwal->getField("JAM_AWAL")).'';
              
              if($paket_tahap_jadwal->getField("TANGGAL_AKHIR") == "")
              {}
              else
              {
                if ($paket_tahap_jadwal->getField("TANGGAL_AWAL")) {
                  $html .= ' s.d '; 
                }
                $html .= ''.getFormattedDate($paket_tahap_jadwal->getField("TANGGAL_AKHIR")).'';
                $html .= ''.addWIB($paket_tahap_jadwal->getField("JAM_AKHIR")).'';

              }
              $html .= ' </span>
                       </div>';
            $html .= '</td>'; 
        }
        $html .= '</tr>';
      }
    }

    return $html;
  } 

  function __cekPerubahan($total_perubahan, $paket_id, $nama)
  {

    switch ($total_perubahan) {
        
      case '1': // 1x Perubahan
        $total_perubahan = 0; 
        $paket_reschedule_rekamjejak1 = new Paket();
        $paket_reschedule_rekamjejak1->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_1 is not null');
        if($paket_reschedule_rekamjejak1->countRow() > 0)
        {
          $paket_reschedule_rekamjejak1->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak1->getField('TANGGAL_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak1->getField('TANGGAL_AKHIR'));
          $reschedule_1_awal  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_1_AWAL'));
          $reschedule_1_akhir = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_1_AKHIR')); 
          if ($tanggal_awal == $reschedule_1_awal && $tanggal_akhir == $reschedule_1_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }
        break;

      case '2': // 2x Perubahan
        $total_perubahan = 0; 
        // PERUBAHAN KE=2
        $paket_reschedule_rekamjejak2 = new Paket();
        $paket_reschedule_rekamjejak2->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_2 is not null');
        if($paket_reschedule_rekamjejak2->countRow() > 0)
        {
          $paket_reschedule_rekamjejak2->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak2->getField('TANGGAL_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak2->getField('TANGGAL_AKHIR'));
          $reschedule_2_awal  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_2_AWAL'));
          $reschedule_2_akhir = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_2_AKHIR')); 
          if ($tanggal_awal == $reschedule_2_awal && $tanggal_akhir == $reschedule_2_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=1
        $paket_reschedule_rekamjejak1 = new Paket();
        $paket_reschedule_rekamjejak1->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_1 is not null');
        if($paket_reschedule_rekamjejak1->countRow() > 0)
        {
          $paket_reschedule_rekamjejak1->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_2_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_2_AKHIR'));
          $reschedule_1_awal  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_1_AWAL'));
          $reschedule_1_akhir = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_1_AKHIR')); 
          if ($tanggal_awal == $reschedule_1_awal && $tanggal_akhir == $reschedule_1_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }
        break;

      case '3': // 3x Perubahan
        $total_perubahan = 0; 
        // PERUBAHAN KE=3
        $paket_reschedule_rekamjejak3 = new Paket();
        $paket_reschedule_rekamjejak3->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_3 is not null');
        if($paket_reschedule_rekamjejak3->countRow() > 0)
        {
          $paket_reschedule_rekamjejak3->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak3->getField('TANGGAL_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak3->getField('TANGGAL_AKHIR'));
          $reschedule_3_awal  = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_3_AWAL'));
          $reschedule_3_akhir = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_3_AKHIR')); 
          if ($tanggal_awal == $reschedule_3_awal && $tanggal_akhir == $reschedule_3_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=2
        $paket_reschedule_rekamjejak2 = new Paket();
        $paket_reschedule_rekamjejak2->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_2 is not null');
        if($paket_reschedule_rekamjejak2->countRow() > 0)
        {
          $paket_reschedule_rekamjejak2->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_3_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_3_AKHIR'));
          $reschedule_2_awal  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_2_AWAL'));
          $reschedule_2_akhir = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_2_AKHIR')); 
          if ($tanggal_awal == $reschedule_2_awal && $tanggal_akhir == $reschedule_2_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=1
        $paket_reschedule_rekamjejak1 = new Paket();
        $paket_reschedule_rekamjejak1->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_1 is not null');
        if($paket_reschedule_rekamjejak1->countRow() > 0)
        {
          $paket_reschedule_rekamjejak1->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_2_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_2_AKHIR'));
          $reschedule_1_awal  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_1_AWAL'));
          $reschedule_1_akhir = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_1_AKHIR')); 
          if ($tanggal_awal == $reschedule_1_awal && $tanggal_akhir == $reschedule_1_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }
        break;

      case '4': // 4x Perubahan
        $total_perubahan = 0; 
        // PERUBAHAN KE=4
        $paket_reschedule_rekamjejak4 = new Paket();
        $paket_reschedule_rekamjejak4->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_4 is not null');
        if($paket_reschedule_rekamjejak4->countRow() > 0)
        {
          $paket_reschedule_rekamjejak4->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak4->getField('TANGGAL_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak4->getField('TANGGAL_AKHIR'));
          $reschedule_4_awal  = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_4_AWAL'));
          $reschedule_4_akhir = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_4_AKHIR')); 
          if ($tanggal_awal == $reschedule_4_awal && $tanggal_akhir == $reschedule_4_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=3
        $paket_reschedule_rekamjejak3 = new Paket();
        $paket_reschedule_rekamjejak3->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_3 is not null');
        if($paket_reschedule_rekamjejak3->countRow() > 0)
        {
          $paket_reschedule_rekamjejak3->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_4_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_4_AKHIR'));
          $reschedule_3_awal  = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_3_AWAL'));
          $reschedule_3_akhir = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_3_AKHIR')); 
          if ($tanggal_awal == $reschedule_3_awal && $tanggal_akhir == $reschedule_3_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=2
        $paket_reschedule_rekamjejak2 = new Paket();
        $paket_reschedule_rekamjejak2->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_2 is not null');
        if($paket_reschedule_rekamjejak2->countRow() > 0)
        {
          $paket_reschedule_rekamjejak2->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_3_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_3_AKHIR'));
          $reschedule_2_awal  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_2_AWAL'));
          $reschedule_2_akhir = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_2_AKHIR')); 
          if ($tanggal_awal == $reschedule_2_awal && $tanggal_akhir == $reschedule_2_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=1
        $paket_reschedule_rekamjejak1 = new Paket();
        $paket_reschedule_rekamjejak1->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_1 is not null');
        if($paket_reschedule_rekamjejak1->countRow() > 0)
        {
          $paket_reschedule_rekamjejak1->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_2_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_2_AKHIR'));
          $reschedule_1_awal  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_1_AWAL'));
          $reschedule_1_akhir = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_1_AKHIR')); 
          if ($tanggal_awal == $reschedule_1_awal && $tanggal_akhir == $reschedule_1_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }
        break;

      case '5': // 5x Perubahan
        $total_perubahan = 0; 
        // PERUBAHAN KE=5
        $paket_reschedule_rekamjejak5 = new Paket();
        $paket_reschedule_rekamjejak5->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_5 is not null');
        if($paket_reschedule_rekamjejak5->countRow() > 0)
        {
          $paket_reschedule_rekamjejak5->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak5->getField('TANGGAL_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak5->getField('TANGGAL_AKHIR'));
          $reschedule_5_awal  = strtotime($paket_reschedule_rekamjejak5->getField('RESCHEDULE_5_AWAL'));
          $reschedule_5_akhir = strtotime($paket_reschedule_rekamjejak5->getField('RESCHEDULE_5_AKHIR')); 
          if ($tanggal_awal == $reschedule_5_awal && $tanggal_akhir == $reschedule_5_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=4
        $paket_reschedule_rekamjejak4 = new Paket();
        $paket_reschedule_rekamjejak4->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_4 is not null');
        if($paket_reschedule_rekamjejak4->countRow() > 0)
        {
          $paket_reschedule_rekamjejak4->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_5_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_5_AKHIR'));
          $reschedule_4_awal  = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_4_AWAL'));
          $reschedule_4_akhir = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_4_AKHIR')); 
          if ($tanggal_awal == $reschedule_4_awal && $tanggal_akhir == $reschedule_4_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=3
        $paket_reschedule_rekamjejak3 = new Paket();
        $paket_reschedule_rekamjejak3->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_3 is not null');
        if($paket_reschedule_rekamjejak3->countRow() > 0)
        {
          $paket_reschedule_rekamjejak3->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_4_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_4_AKHIR'));
          $reschedule_3_awal  = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_3_AWAL'));
          $reschedule_3_akhir = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_3_AKHIR')); 
          if ($tanggal_awal == $reschedule_3_awal && $tanggal_akhir == $reschedule_3_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=2
        $paket_reschedule_rekamjejak2 = new Paket();
        $paket_reschedule_rekamjejak2->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_2 is not null');
        if($paket_reschedule_rekamjejak2->countRow() > 0)
        {
          $paket_reschedule_rekamjejak2->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_3_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_3_AKHIR'));
          $reschedule_2_awal  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_2_AWAL'));
          $reschedule_2_akhir = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_2_AKHIR')); 
          if ($tanggal_awal == $reschedule_2_awal && $tanggal_akhir == $reschedule_2_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=1
        $paket_reschedule_rekamjejak1 = new Paket();
        $paket_reschedule_rekamjejak1->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_1 is not null');
        if($paket_reschedule_rekamjejak1->countRow() > 0)
        {
          $paket_reschedule_rekamjejak1->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_2_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_2_AKHIR'));
          $reschedule_1_awal  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_1_AWAL'));
          $reschedule_1_akhir = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_1_AKHIR')); 
          if ($tanggal_awal == $reschedule_1_awal && $tanggal_akhir == $reschedule_1_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }
        break;
      
      case '6': // 6x Perubahan
        $total_perubahan = 0; 
        // PERUBAHAN KE=6
        $paket_reschedule_rekamjejak6 = new Paket();
        $paket_reschedule_rekamjejak6->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_6 is not null');
        if($paket_reschedule_rekamjejak6->countRow() > 0)
        {
          $paket_reschedule_rekamjejak6->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak6->getField('TANGGAL_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak6->getField('TANGGAL_AKHIR'));
          $reschedule_6_awal  = strtotime($paket_reschedule_rekamjejak6->getField('RESCHEDULE_6_AWAL'));
          $reschedule_6_akhir = strtotime($paket_reschedule_rekamjejak6->getField('RESCHEDULE_6_AKHIR')); 
          if ($tanggal_awal == $reschedule_6_awal && $tanggal_akhir == $reschedule_6_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=5
        $paket_reschedule_rekamjejak5 = new Paket();
        $paket_reschedule_rekamjejak5->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_5 is not null');
        if($paket_reschedule_rekamjejak5->countRow() > 0)
        {
          $paket_reschedule_rekamjejak5->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak5->getField('RESCHEDULE_6_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak5->getField('RESCHEDULE_6_AKHIR'));
          $reschedule_5_awal  = strtotime($paket_reschedule_rekamjejak5->getField('RESCHEDULE_5_AWAL'));
          $reschedule_5_akhir = strtotime($paket_reschedule_rekamjejak5->getField('RESCHEDULE_5_AKHIR')); 
          if ($tanggal_awal == $reschedule_5_awal && $tanggal_akhir == $reschedule_5_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=4
        $paket_reschedule_rekamjejak4 = new Paket();
        $paket_reschedule_rekamjejak4->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_4 is not null');
        if($paket_reschedule_rekamjejak4->countRow() > 0)
        {
          $paket_reschedule_rekamjejak4->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_5_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_5_AKHIR'));
          $reschedule_4_awal  = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_4_AWAL'));
          $reschedule_4_akhir = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_4_AKHIR')); 
          if ($tanggal_awal == $reschedule_4_awal && $tanggal_akhir == $reschedule_4_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=3
        $paket_reschedule_rekamjejak3 = new Paket();
        $paket_reschedule_rekamjejak3->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_3 is not null');
        if($paket_reschedule_rekamjejak3->countRow() > 0)
        {
          $paket_reschedule_rekamjejak3->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_4_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_4_AKHIR'));
          $reschedule_3_awal  = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_3_AWAL'));
          $reschedule_3_akhir = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_3_AKHIR')); 
          if ($tanggal_awal == $reschedule_3_awal && $tanggal_akhir == $reschedule_3_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=2
        $paket_reschedule_rekamjejak2 = new Paket();
        $paket_reschedule_rekamjejak2->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_2 is not null');
        if($paket_reschedule_rekamjejak2->countRow() > 0)
        {
          $paket_reschedule_rekamjejak2->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_3_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_3_AKHIR'));
          $reschedule_2_awal  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_2_AWAL'));
          $reschedule_2_akhir = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_2_AKHIR')); 
          if ($tanggal_awal == $reschedule_2_awal && $tanggal_akhir == $reschedule_2_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=1
        $paket_reschedule_rekamjejak1 = new Paket();
        $paket_reschedule_rekamjejak1->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_1 is not null');
        if($paket_reschedule_rekamjejak1->countRow() > 0)
        {
          $paket_reschedule_rekamjejak1->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_2_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_2_AKHIR'));
          $reschedule_1_awal  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_1_AWAL'));
          $reschedule_1_akhir = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_1_AKHIR')); 
          if ($tanggal_awal == $reschedule_1_awal && $tanggal_akhir == $reschedule_1_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }
        break;
      
      case '7': // 7x Perubahan
        $total_perubahan = 0; 
        // PERUBAHAN KE=7
        $paket_reschedule_rekamjejak7 = new Paket();
        $paket_reschedule_rekamjejak7->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_7 is not null');
        if($paket_reschedule_rekamjejak7->countRow() > 0)
        {
          $paket_reschedule_rekamjejak7->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak7->getField('TANGGAL_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak7->getField('TANGGAL_AKHIR'));
          $reschedule_7_awal  = strtotime($paket_reschedule_rekamjejak7->getField('RESCHEDULE_7_AWAL'));
          $reschedule_7_akhir = strtotime($paket_reschedule_rekamjejak7->getField('RESCHEDULE_7_AKHIR')); 
          if ($tanggal_awal == $reschedule_7_awal && $tanggal_akhir == $reschedule_7_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=6
        $paket_reschedule_rekamjejak6 = new Paket();
        $paket_reschedule_rekamjejak6->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_6 is not null');
        if($paket_reschedule_rekamjejak6->countRow() > 0)
        {
          $paket_reschedule_rekamjejak6->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak6->getField('RESCHEDULE_7_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak6->getField('RESCHEDULE_7_AKHIR'));
          $reschedule_6_awal  = strtotime($paket_reschedule_rekamjejak6->getField('RESCHEDULE_6_AWAL'));
          $reschedule_6_akhir = strtotime($paket_reschedule_rekamjejak6->getField('RESCHEDULE_6_AKHIR')); 
          if ($tanggal_awal == $reschedule_6_awal && $tanggal_akhir == $reschedule_6_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=5
        $paket_reschedule_rekamjejak5 = new Paket();
        $paket_reschedule_rekamjejak5->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_5 is not null');
        if($paket_reschedule_rekamjejak5->countRow() > 0)
        {
          $paket_reschedule_rekamjejak5->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak5->getField('RESCHEDULE_6_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak5->getField('RESCHEDULE_6_AKHIR'));
          $reschedule_5_awal  = strtotime($paket_reschedule_rekamjejak5->getField('RESCHEDULE_5_AWAL'));
          $reschedule_5_akhir = strtotime($paket_reschedule_rekamjejak5->getField('RESCHEDULE_5_AKHIR')); 
          if ($tanggal_awal == $reschedule_5_awal && $tanggal_akhir == $reschedule_5_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=4
        $paket_reschedule_rekamjejak4 = new Paket();
        $paket_reschedule_rekamjejak4->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_4 is not null');
        if($paket_reschedule_rekamjejak4->countRow() > 0)
        {
          $paket_reschedule_rekamjejak4->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_5_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_5_AKHIR'));
          $reschedule_4_awal  = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_4_AWAL'));
          $reschedule_4_akhir = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_4_AKHIR')); 
          if ($tanggal_awal == $reschedule_4_awal && $tanggal_akhir == $reschedule_4_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=3
        $paket_reschedule_rekamjejak3 = new Paket();
        $paket_reschedule_rekamjejak3->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_3 is not null');
        if($paket_reschedule_rekamjejak3->countRow() > 0)
        {
          $paket_reschedule_rekamjejak3->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_4_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_4_AKHIR'));
          $reschedule_3_awal  = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_3_AWAL'));
          $reschedule_3_akhir = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_3_AKHIR')); 
          if ($tanggal_awal == $reschedule_3_awal && $tanggal_akhir == $reschedule_3_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=2
        $paket_reschedule_rekamjejak2 = new Paket();
        $paket_reschedule_rekamjejak2->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_2 is not null');
        if($paket_reschedule_rekamjejak2->countRow() > 0)
        {
          $paket_reschedule_rekamjejak2->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_3_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_3_AKHIR'));
          $reschedule_2_awal  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_2_AWAL'));
          $reschedule_2_akhir = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_2_AKHIR')); 
          if ($tanggal_awal == $reschedule_2_awal && $tanggal_akhir == $reschedule_2_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=1
        $paket_reschedule_rekamjejak1 = new Paket();
        $paket_reschedule_rekamjejak1->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_1 is not null');
        if($paket_reschedule_rekamjejak1->countRow() > 0)
        {
          $paket_reschedule_rekamjejak1->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_2_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_2_AKHIR'));
          $reschedule_1_awal  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_1_AWAL'));
          $reschedule_1_akhir = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_1_AKHIR')); 
          if ($tanggal_awal == $reschedule_1_awal && $tanggal_akhir == $reschedule_1_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }
        break;
      
      case '8': // 8x Perubahan
        $total_perubahan = 0; 
        // PERUBAHAN KE=8
        $paket_reschedule_rekamjejak8 = new Paket();
        $paket_reschedule_rekamjejak8->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_8 is not null');
        if($paket_reschedule_rekamjejak8->countRow() > 0)
        {
          $paket_reschedule_rekamjejak8->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak8->getField('TANGGAL_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak8->getField('TANGGAL_AKHIR'));
          $reschedule_8_awal  = strtotime($paket_reschedule_rekamjejak8->getField('RESCHEDULE_8_AWAL'));
          $reschedule_8_akhir = strtotime($paket_reschedule_rekamjejak8->getField('RESCHEDULE_8_AKHIR')); 
          if ($tanggal_awal == $reschedule_8_awal && $tanggal_akhir == $reschedule_8_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=7
        $paket_reschedule_rekamjejak7 = new Paket();
        $paket_reschedule_rekamjejak7->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_7 is not null');
        if($paket_reschedule_rekamjejak7->countRow() > 0)
        {
          $paket_reschedule_rekamjejak7->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak7->getField('RESCHEDULE_8_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak7->getField('RESCHEDULE_8_AKHIR'));
          $reschedule_7_awal  = strtotime($paket_reschedule_rekamjejak7->getField('RESCHEDULE_7_AWAL'));
          $reschedule_7_akhir = strtotime($paket_reschedule_rekamjejak7->getField('RESCHEDULE_7_AKHIR')); 
          if ($tanggal_awal == $reschedule_7_awal && $tanggal_akhir == $reschedule_7_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=6
        $paket_reschedule_rekamjejak6 = new Paket();
        $paket_reschedule_rekamjejak6->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_6 is not null');
        if($paket_reschedule_rekamjejak6->countRow() > 0)
        {
          $paket_reschedule_rekamjejak6->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak6->getField('RESCHEDULE_7_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak6->getField('RESCHEDULE_7_AKHIR'));
          $reschedule_6_awal  = strtotime($paket_reschedule_rekamjejak6->getField('RESCHEDULE_6_AWAL'));
          $reschedule_6_akhir = strtotime($paket_reschedule_rekamjejak6->getField('RESCHEDULE_6_AKHIR')); 
          if ($tanggal_awal == $reschedule_6_awal && $tanggal_akhir == $reschedule_6_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=5
        $paket_reschedule_rekamjejak5 = new Paket();
        $paket_reschedule_rekamjejak5->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_5 is not null');
        if($paket_reschedule_rekamjejak5->countRow() > 0)
        {
          $paket_reschedule_rekamjejak5->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak5->getField('RESCHEDULE_6_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak5->getField('RESCHEDULE_6_AKHIR'));
          $reschedule_5_awal  = strtotime($paket_reschedule_rekamjejak5->getField('RESCHEDULE_5_AWAL'));
          $reschedule_5_akhir = strtotime($paket_reschedule_rekamjejak5->getField('RESCHEDULE_5_AKHIR')); 
          if ($tanggal_awal == $reschedule_5_awal && $tanggal_akhir == $reschedule_5_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=4
        $paket_reschedule_rekamjejak4 = new Paket();
        $paket_reschedule_rekamjejak4->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_4 is not null');
        if($paket_reschedule_rekamjejak4->countRow() > 0)
        {
          $paket_reschedule_rekamjejak4->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_5_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_5_AKHIR'));
          $reschedule_4_awal  = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_4_AWAL'));
          $reschedule_4_akhir = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_4_AKHIR')); 
          if ($tanggal_awal == $reschedule_4_awal && $tanggal_akhir == $reschedule_4_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=3
        $paket_reschedule_rekamjejak3 = new Paket();
        $paket_reschedule_rekamjejak3->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_3 is not null');
        if($paket_reschedule_rekamjejak3->countRow() > 0)
        {
          $paket_reschedule_rekamjejak3->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_4_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_4_AKHIR'));
          $reschedule_3_awal  = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_3_AWAL'));
          $reschedule_3_akhir = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_3_AKHIR')); 
          if ($tanggal_awal == $reschedule_3_awal && $tanggal_akhir == $reschedule_3_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=2
        $paket_reschedule_rekamjejak2 = new Paket();
        $paket_reschedule_rekamjejak2->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_2 is not null');
        if($paket_reschedule_rekamjejak2->countRow() > 0)
        {
          $paket_reschedule_rekamjejak2->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_3_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_3_AKHIR'));
          $reschedule_2_awal  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_2_AWAL'));
          $reschedule_2_akhir = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_2_AKHIR')); 
          if ($tanggal_awal == $reschedule_2_awal && $tanggal_akhir == $reschedule_2_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=1
        $paket_reschedule_rekamjejak1 = new Paket();
        $paket_reschedule_rekamjejak1->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_1 is not null');
        if($paket_reschedule_rekamjejak1->countRow() > 0)
        {
          $paket_reschedule_rekamjejak1->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_2_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_2_AKHIR'));
          $reschedule_1_awal  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_1_AWAL'));
          $reschedule_1_akhir = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_1_AKHIR')); 
          if ($tanggal_awal == $reschedule_1_awal && $tanggal_akhir == $reschedule_1_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }
        break;
      
      case '9': // 9x Perubahan
        $total_perubahan = 0; 
        // PERUBAHAN KE=9
        $paket_reschedule_rekamjejak9 = new Paket();
        $paket_reschedule_rekamjejak9->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_9 is not null');
        if($paket_reschedule_rekamjejak9->countRow() > 0)
        {
          $paket_reschedule_rekamjejak9->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak9->getField('TANGGAL_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak9->getField('TANGGAL_AKHIR'));
          $reschedule_9_awal  = strtotime($paket_reschedule_rekamjejak9->getField('RESCHEDULE_9_AWAL'));
          $reschedule_9_akhir = strtotime($paket_reschedule_rekamjejak9->getField('RESCHEDULE_9_AKHIR')); 
          if ($tanggal_awal == $reschedule_9_awal && $tanggal_akhir == $reschedule_9_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=8
        $paket_reschedule_rekamjejak8 = new Paket();
        $paket_reschedule_rekamjejak8->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_8 is not null');
        if($paket_reschedule_rekamjejak8->countRow() > 0)
        {
          $paket_reschedule_rekamjejak8->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak8->getField('RESCHEDULE_9_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak8->getField('RESCHEDULE_9_AKHIR'));
          $reschedule_8_awal  = strtotime($paket_reschedule_rekamjejak8->getField('RESCHEDULE_8_AWAL'));
          $reschedule_8_akhir = strtotime($paket_reschedule_rekamjejak8->getField('RESCHEDULE_8_AKHIR')); 
          if ($tanggal_awal == $reschedule_8_awal && $tanggal_akhir == $reschedule_8_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=7
        $paket_reschedule_rekamjejak7 = new Paket();
        $paket_reschedule_rekamjejak7->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_7 is not null');
        if($paket_reschedule_rekamjejak7->countRow() > 0)
        {
          $paket_reschedule_rekamjejak7->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak7->getField('RESCHEDULE_8_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak7->getField('RESCHEDULE_8_AKHIR'));
          $reschedule_7_awal  = strtotime($paket_reschedule_rekamjejak7->getField('RESCHEDULE_7_AWAL'));
          $reschedule_7_akhir = strtotime($paket_reschedule_rekamjejak7->getField('RESCHEDULE_7_AKHIR')); 
          if ($tanggal_awal == $reschedule_7_awal && $tanggal_akhir == $reschedule_7_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=6
        $paket_reschedule_rekamjejak6 = new Paket();
        $paket_reschedule_rekamjejak6->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_6 is not null');
        if($paket_reschedule_rekamjejak6->countRow() > 0)
        {
          $paket_reschedule_rekamjejak6->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak6->getField('RESCHEDULE_7_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak6->getField('RESCHEDULE_7_AKHIR'));
          $reschedule_6_awal  = strtotime($paket_reschedule_rekamjejak6->getField('RESCHEDULE_6_AWAL'));
          $reschedule_6_akhir = strtotime($paket_reschedule_rekamjejak6->getField('RESCHEDULE_6_AKHIR')); 
          if ($tanggal_awal == $reschedule_6_awal && $tanggal_akhir == $reschedule_6_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=5
        $paket_reschedule_rekamjejak5 = new Paket();
        $paket_reschedule_rekamjejak5->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_5 is not null');
        if($paket_reschedule_rekamjejak5->countRow() > 0)
        {
          $paket_reschedule_rekamjejak5->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak5->getField('RESCHEDULE_6_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak5->getField('RESCHEDULE_6_AKHIR'));
          $reschedule_5_awal  = strtotime($paket_reschedule_rekamjejak5->getField('RESCHEDULE_5_AWAL'));
          $reschedule_5_akhir = strtotime($paket_reschedule_rekamjejak5->getField('RESCHEDULE_5_AKHIR')); 
          if ($tanggal_awal == $reschedule_5_awal && $tanggal_akhir == $reschedule_5_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=4
        $paket_reschedule_rekamjejak4 = new Paket();
        $paket_reschedule_rekamjejak4->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_4 is not null');
        if($paket_reschedule_rekamjejak4->countRow() > 0)
        {
          $paket_reschedule_rekamjejak4->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_5_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_5_AKHIR'));
          $reschedule_4_awal  = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_4_AWAL'));
          $reschedule_4_akhir = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_4_AKHIR')); 
          if ($tanggal_awal == $reschedule_4_awal && $tanggal_akhir == $reschedule_4_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=3
        $paket_reschedule_rekamjejak3 = new Paket();
        $paket_reschedule_rekamjejak3->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_3 is not null');
        if($paket_reschedule_rekamjejak3->countRow() > 0)
        {
          $paket_reschedule_rekamjejak3->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_4_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_4_AKHIR'));
          $reschedule_3_awal  = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_3_AWAL'));
          $reschedule_3_akhir = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_3_AKHIR')); 
          if ($tanggal_awal == $reschedule_3_awal && $tanggal_akhir == $reschedule_3_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=2
        $paket_reschedule_rekamjejak2 = new Paket();
        $paket_reschedule_rekamjejak2->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_2 is not null');
        if($paket_reschedule_rekamjejak2->countRow() > 0)
        {
          $paket_reschedule_rekamjejak2->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_3_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_3_AKHIR'));
          $reschedule_2_awal  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_2_AWAL'));
          $reschedule_2_akhir = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_2_AKHIR')); 
          if ($tanggal_awal == $reschedule_2_awal && $tanggal_akhir == $reschedule_2_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=1
        $paket_reschedule_rekamjejak1 = new Paket();
        $paket_reschedule_rekamjejak1->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_1 is not null');
        if($paket_reschedule_rekamjejak1->countRow() > 0)
        {
          $paket_reschedule_rekamjejak1->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_2_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_2_AKHIR'));
          $reschedule_1_awal  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_1_AWAL'));
          $reschedule_1_akhir = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_1_AKHIR')); 
          if ($tanggal_awal == $reschedule_1_awal && $tanggal_akhir == $reschedule_1_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }
        break;
      
      case '10': // 10x Perubahan
        $total_perubahan = 0; 
        // PERUBAHAN KE=10
        $paket_reschedule_rekamjejak10 = new Paket();
        $paket_reschedule_rekamjejak10->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_10 is not null');
        if($paket_reschedule_rekamjejak10->countRow() > 0)
        {
          $paket_reschedule_rekamjejak10->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak10->getField('TANGGAL_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak10->getField('TANGGAL_AKHIR'));
          $reschedule_10_awal  = strtotime($paket_reschedule_rekamjejak10->getField('RESCHEDULE_10_AWAL'));
          $reschedule_10_akhir = strtotime($paket_reschedule_rekamjejak10->getField('RESCHEDULE_10_AKHIR')); 
          if ($tanggal_awal == $reschedule_10_awal && $tanggal_akhir == $reschedule_10_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=9
        $paket_reschedule_rekamjejak9 = new Paket();
        $paket_reschedule_rekamjejak9->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_9 is not null');
        if($paket_reschedule_rekamjejak9->countRow() > 0)
        {
          $paket_reschedule_rekamjejak9->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak9->getField('RESCHEDULE_10_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak9->getField('RESCHEDULE_10_AKHIR'));
          $reschedule_9_awal  = strtotime($paket_reschedule_rekamjejak9->getField('RESCHEDULE_9_AWAL'));
          $reschedule_9_akhir = strtotime($paket_reschedule_rekamjejak9->getField('RESCHEDULE_9_AKHIR')); 
          if ($tanggal_awal == $reschedule_9_awal && $tanggal_akhir == $reschedule_9_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=8
        $paket_reschedule_rekamjejak8 = new Paket();
        $paket_reschedule_rekamjejak8->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_8 is not null');
        if($paket_reschedule_rekamjejak8->countRow() > 0)
        {
          $paket_reschedule_rekamjejak8->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak8->getField('RESCHEDULE_9_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak8->getField('RESCHEDULE_9_AKHIR'));
          $reschedule_8_awal  = strtotime($paket_reschedule_rekamjejak8->getField('RESCHEDULE_8_AWAL'));
          $reschedule_8_akhir = strtotime($paket_reschedule_rekamjejak8->getField('RESCHEDULE_8_AKHIR')); 
          if ($tanggal_awal == $reschedule_8_awal && $tanggal_akhir == $reschedule_8_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=7
        $paket_reschedule_rekamjejak7 = new Paket();
        $paket_reschedule_rekamjejak7->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_7 is not null');
        if($paket_reschedule_rekamjejak7->countRow() > 0)
        {
          $paket_reschedule_rekamjejak7->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak7->getField('RESCHEDULE_8_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak7->getField('RESCHEDULE_8_AKHIR'));
          $reschedule_7_awal  = strtotime($paket_reschedule_rekamjejak7->getField('RESCHEDULE_7_AWAL'));
          $reschedule_7_akhir = strtotime($paket_reschedule_rekamjejak7->getField('RESCHEDULE_7_AKHIR')); 
          if ($tanggal_awal == $reschedule_7_awal && $tanggal_akhir == $reschedule_7_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=6
        $paket_reschedule_rekamjejak6 = new Paket();
        $paket_reschedule_rekamjejak6->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_6 is not null');
        if($paket_reschedule_rekamjejak6->countRow() > 0)
        {
          $paket_reschedule_rekamjejak6->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak6->getField('RESCHEDULE_7_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak6->getField('RESCHEDULE_7_AKHIR'));
          $reschedule_6_awal  = strtotime($paket_reschedule_rekamjejak6->getField('RESCHEDULE_6_AWAL'));
          $reschedule_6_akhir = strtotime($paket_reschedule_rekamjejak6->getField('RESCHEDULE_6_AKHIR')); 
          if ($tanggal_awal == $reschedule_6_awal && $tanggal_akhir == $reschedule_6_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=5
        $paket_reschedule_rekamjejak5 = new Paket();
        $paket_reschedule_rekamjejak5->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_5 is not null');
        if($paket_reschedule_rekamjejak5->countRow() > 0)
        {
          $paket_reschedule_rekamjejak5->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak5->getField('RESCHEDULE_6_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak5->getField('RESCHEDULE_6_AKHIR'));
          $reschedule_5_awal  = strtotime($paket_reschedule_rekamjejak5->getField('RESCHEDULE_5_AWAL'));
          $reschedule_5_akhir = strtotime($paket_reschedule_rekamjejak5->getField('RESCHEDULE_5_AKHIR')); 
          if ($tanggal_awal == $reschedule_5_awal && $tanggal_akhir == $reschedule_5_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=4
        $paket_reschedule_rekamjejak4 = new Paket();
        $paket_reschedule_rekamjejak4->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_4 is not null');
        if($paket_reschedule_rekamjejak4->countRow() > 0)
        {
          $paket_reschedule_rekamjejak4->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_5_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_5_AKHIR'));
          $reschedule_4_awal  = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_4_AWAL'));
          $reschedule_4_akhir = strtotime($paket_reschedule_rekamjejak4->getField('RESCHEDULE_4_AKHIR')); 
          if ($tanggal_awal == $reschedule_4_awal && $tanggal_akhir == $reschedule_4_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=3
        $paket_reschedule_rekamjejak3 = new Paket();
        $paket_reschedule_rekamjejak3->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_3 is not null');
        if($paket_reschedule_rekamjejak3->countRow() > 0)
        {
          $paket_reschedule_rekamjejak3->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_4_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_4_AKHIR'));
          $reschedule_3_awal  = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_3_AWAL'));
          $reschedule_3_akhir = strtotime($paket_reschedule_rekamjejak3->getField('RESCHEDULE_3_AKHIR')); 
          if ($tanggal_awal == $reschedule_3_awal && $tanggal_akhir == $reschedule_3_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=2
        $paket_reschedule_rekamjejak2 = new Paket();
        $paket_reschedule_rekamjejak2->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_2 is not null');
        if($paket_reschedule_rekamjejak2->countRow() > 0)
        {
          $paket_reschedule_rekamjejak2->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_3_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_3_AKHIR'));
          $reschedule_2_awal  = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_2_AWAL'));
          $reschedule_2_akhir = strtotime($paket_reschedule_rekamjejak2->getField('RESCHEDULE_2_AKHIR')); 
          if ($tanggal_awal == $reschedule_2_awal && $tanggal_akhir == $reschedule_2_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }

        // PERUBAHAN KE=1
        $paket_reschedule_rekamjejak1 = new Paket();
        $paket_reschedule_rekamjejak1->selectByParamsReschedule(array(), '-1', '-1', ' AND PAKET_ID = '.$paket_id.' AND NAMA = \''.$nama.'\' AND reschedule_1 is not null');
        if($paket_reschedule_rekamjejak1->countRow() > 0)
        {
          $paket_reschedule_rekamjejak1->firstRow();
          $tanggal_awal  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_2_AWAL'));
          $tanggal_akhir  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_2_AKHIR'));
          $reschedule_1_awal  = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_1_AWAL'));
          $reschedule_1_akhir = strtotime($paket_reschedule_rekamjejak1->getField('RESCHEDULE_1_AKHIR')); 
          if ($tanggal_awal == $reschedule_1_awal && $tanggal_akhir == $reschedule_1_akhir) {
            // $total_perubahan = 0;
          }
          else {
            $total_perubahan++;
          }
        }
        break;
      
      default:
        $total_perubahan = 0;
        break;
    }



    return $total_perubahan;
  }

}
