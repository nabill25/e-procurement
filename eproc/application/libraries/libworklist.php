<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

class libworklist
{
  private $_CI;

  function __construct()
  {
    $this->_CI =& get_instance();
    $this->_CI->load->library('kauth');
    $this->_CI->USER_LOGIN_ID   =  $this->_CI->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
    $this->_CI->USER_LOGIN      =  $this->_CI->kauth->getInstance()->getIdentity()->USER_LOGIN;
    $this->_CI->USER_NAMA       =  $this->_CI->kauth->getInstance()->getIdentity()->USER_NAMA;
    $this->_CI->USER_TYPE_ID    =  $this->_CI->kauth->getInstance()->getIdentity()->USER_TYPE_ID;
    $this->_CI->LEGAL           =  $this->_CI->kauth->getInstance()->getIdentity()->LEGAL;
    $this->_CI->ID              =  $this->_CI->kauth->getInstance()->getIdentity()->REKANAN_ID;
    $this->_CI->LEVEL_KONTRAK   =  $this->_CI->kauth->getInstance()->getIdentity()->LEVEL_KONTRAK;
    $this->_CI->REKANAN_ID   =  $this->_CI->kauth->getInstance()->getIdentity()->REKANAN_ID;
    $this->_CI->HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
  }  


  // ======== PERSIAPAN KASI

  function worklistPersiapanKasi()
  {
    include_once("functions/default.func.php");
    include_once("functions/string.func.php");
    include_once("functions/date.func.php");

    $this->_CI->load->model("Queryfree");

    $html = '';
    $html  .=  '<table class="table table-striped table-bordered row-grouping">
                  <thead>
                    <tr>
                      <th>Paket Pengadaan</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                <tbody>'; 

    $countKontrakPIC = new Queryfree();
    $countKontrakPIC->selectByParams("SELECT NAMA FROM VIEW_CONTRACTING_PAKET 
                                    WHERE SELESAI = '1' AND PAKET_METODE_LELANG_ID IN (1,2,3,4,5,7,8,10,11) AND STATUS_KONTRAK = 'Belum dibuat' AND APPROVE_PPK = '1' AND PIC_KONTRAK IS NULL");
    if ($countKontrakPIC->countRow() > 0) { 
        while ($countKontrakPIC->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_penunjukan_pic">'.$countKontrakPIC->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrakPIC->countRow().'</span> 
                        PIC belum ditunjuk
                      </td>
                    </tr>';
        }
        $count += 1;
    }
    unset($countKontrakPIC);

    $countKontrakSPPBJBelumDibuat = new Queryfree();
    $countKontrakSPPBJBelumDibuat->selectByParams("SELECT CONTRACTINGPROSESID, NAMA FROM VIEW_CONTRACTING_PAKET 
                                                WHERE SELESAI = '1' AND PAKET_METODE_LELANG_ID IN (1,2,3,4,5,7,8,10,11) AND STATUS_KONTRAK = 'Belum dibuat' AND APPROVE_PPK = '1' AND PIC_KONTRAK IS NOT NULL");
    if ($countKontrakSPPBJBelumDibuat->countRow() > 0) { 
        while ($countKontrakSPPBJBelumDibuat->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_paket_sppbj">'.$countKontrakSPPBJBelumDibuat->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrakSPPBJBelumDibuat->countRow().'</span> 
                        SPPBJ belum dibuat
                      </td>
                    </tr>';
        }
        $count += 1;
    }
    unset($countKontrakSPPBJBelumDibuat);

    $countKontrakKasubditMenolakSPPBJ = new Queryfree();
    $countKontrakKasubditMenolakSPPBJ->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID, NAMA FROM VIEW_CONTRACTING_REKANAN 
                                                WHERE CONTRACTINGSTATUSKONTRAKID IN ('115')");
    if ($countKontrakKasubditMenolakSPPBJ->countRow() > 0) { 
        while ($countKontrakKasubditMenolakSPPBJ->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_persiapan_sppbj?reqId='.$countKontrakKasubditMenolakSPPBJ->getField("CONTRACTINGREKANANID").'">'.$countKontrakKasubditMenolakSPPBJ->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrakKasubditMenolakSPPBJ->countRow().'</span> 
                        Kasubdit menolak SPPBJ
                      </td>
                    </tr>';
        }
        $count += 1;
    }
    unset($countKontrakKasubditMenolakSPPBJ);

    $countKontrakSPPBJDisetujuiPPK = new Queryfree();
    $countKontrakSPPBJDisetujuiPPK->selectByParams("SELECT CONTRACTINGREKANANID, CONTRACTINGSTATUSKONTRAKID, NAMA 
                                                FROM VIEW_CONTRACTING_REKANAN 
                                                WHERE CONTRACTINGSTATUSKONTRAKID IN ('112')");
    if ($countKontrakSPPBJDisetujuiPPK->countRow() > 0) { 
        while ($countKontrakSPPBJDisetujuiPPK->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_persiapan_sppbj?reqId='.$countKontrakSPPBJDisetujuiPPK->getField("CONTRACTINGREKANANID").'">'.$countKontrakSPPBJDisetujuiPPK->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrakSPPBJDisetujuiPPK->countRow().'</span> 
                        SBPPJ Disetujui PPK
                      </td>
                    </tr>';
        }
        $count += 1;
    }
    unset($countKontrakSPPBJDisetujuiPPK);

    $countKontrakPembuatanSPPBJ = new Queryfree();
    $countKontrakPembuatanSPPBJ->selectByParams("SELECT CONTRACTINGREKANANID, CONTRACTINGSTATUSKONTRAKID, NAMA 
                                                FROM VIEW_CONTRACTING_REKANAN 
                                                WHERE CONTRACTINGSTATUSKONTRAKID IN ('0')");
    if ($countKontrakPembuatanSPPBJ->countRow() > 0) { 
        while ($countKontrakPembuatanSPPBJ->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_persiapan_sppbj?reqId='.$countKontrakPembuatanSPPBJ->getField("CONTRACTINGREKANANID").'">'.$countKontrakPembuatanSPPBJ->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrakPembuatanSPPBJ->countRow().'</span> 
                        Pembuatan SPPBJ
                      </td>
                    </tr>';
        }
        $count += 1;
    }
    unset($countKontrakPembuatanSPPBJ);

    $countKontrakSPPBJDisetujuiPenyedia = new Queryfree();
    $countKontrakSPPBJDisetujuiPenyedia->selectByParams("SELECT CONTRACTINGREKANANID, CONTRACTINGSTATUSKONTRAKID, NAMA FROM VIEW_CONTRACTING_REKANAN 
                                                WHERE CONTRACTINGSTATUSKONTRAKID IN ('2')");
    if ($countKontrakSPPBJDisetujuiPenyedia->countRow() > 0) { 
        while ($countKontrakSPPBJDisetujuiPenyedia->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_persiapan_sppbj?reqId='.$countKontrakSPPBJDisetujuiPenyedia->getField("CONTRACTINGREKANANID").'">'.$countKontrakSPPBJDisetujuiPenyedia->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrakSPPBJDisetujuiPenyedia->countRow().'</span> 
                        SPPBJ Disetujui oleh Penyedia
                      </td>
                    </tr>';
        }
        $count += 1;
    }
    unset($countKontrakSPPBJDisetujuiPenyedia);

    $countKontrakPembuatanKontrak = new Queryfree();
    $countKontrakPembuatanKontrak->selectByParams("SELECT CONTRACTINGREKANANID, CONTRACTINGSTATUSKONTRAKID, NAMA 
                                                FROM VIEW_CONTRACTING_REKANAN 
                                                WHERE CONTRACTINGSTATUSKONTRAKID IN ('3')");
    if ($countKontrakPembuatanKontrak->countRow() > 0) { 
        while ($countKontrakPembuatanKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_persiapan_kontrak?reqId='.$countKontrakPembuatanKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrakPembuatanKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrakPembuatanKontrak->countRow().'</span> 
                        Pembuatan kontrak
                      </td>
                    </tr>';
        }
        $count += 1;
    }
    unset($countKontrakPembuatanKontrak);

    $countKontrakKasubditMenolakKontrak = new Queryfree();
    $countKontrakKasubditMenolakKontrak->selectByParams("SELECT CONTRACTINGREKANANID, CONTRACTINGSTATUSKONTRAKID, NAMA 
                                                FROM VIEW_CONTRACTING_REKANAN 
                                                WHERE CONTRACTINGSTATUSKONTRAKID IN ('114')");
    if ($countKontrakKasubditMenolakKontrak->countRow() > 0) { 
        while ($countKontrakKasubditMenolakKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_persiapan_kontrak?reqId='.$countKontrakKasubditMenolakKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrakKasubditMenolakKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrakKasubditMenolakKontrak->countRow().'</span> 
                        Kasubdit menolak Kontrak
                      </td>
                    </tr>';
        }
        $count += 1;
    }
    unset($countKontrakKasubditMenolakKontrak);

    $countKontrakPenyediaMenolakKontrak = new Queryfree();
    $countKontrakPenyediaMenolakKontrak->selectByParams("SELECT CONTRACTINGREKANANID, CONTRACTINGSTATUSKONTRAKID, NAMA 
                                                FROM VIEW_CONTRACTING_REKANAN 
                                                WHERE CONTRACTINGSTATUSKONTRAKID IN ('51')");
    if ($countKontrakPenyediaMenolakKontrak->countRow() > 0) { 
        while ($countKontrakPenyediaMenolakKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_persiapan_kontrak?reqId='.$countKontrakPenyediaMenolakKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrakPenyediaMenolakKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrakPenyediaMenolakKontrak->countRow().'</span> 
                        Penyedia menolak kontrak
                      </td>
                    </tr>';
        }
        $count += 1;
    }
    unset($countKontrakPenyediaMenolakKontrak);

    $countKontrakKontrakDisetujuiKasubdit = new Queryfree();
    $countKontrakKontrakDisetujuiKasubdit->selectByParams("SELECT CONTRACTINGREKANANID, CONTRACTINGSTATUSKONTRAKID, NAMA 
                                                FROM VIEW_CONTRACTING_REKANAN 
                                                WHERE CONTRACTINGSTATUSKONTRAKID IN ('31')");
    if ($countKontrakKontrakDisetujuiKasubdit->countRow() > 0) { 
        while ($countKontrakKontrakDisetujuiKasubdit->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_persiapan_kontrak?reqId='.$countKontrakKontrakDisetujuiKasubdit->getField("CONTRACTINGREKANANID").'">'.$countKontrakKontrakDisetujuiKasubdit->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrakKontrakDisetujuiKasubdit->countRow().'</span> 
                        Kontrak Disetujui Kasubdit
                      </td>
                    </tr>';
        }
        $count += 1;
    }
    unset($countKontrakKontrakDisetujuiKasubdit);

    $countKontrakKontrakDisetujuiPenyedia = new Queryfree();
    $countKontrakKontrakDisetujuiPenyedia->selectByParams("SELECT CONTRACTINGREKANANID, CONTRACTINGSTATUSKONTRAKID, NAMA 
                                                FROM VIEW_CONTRACTING_REKANAN 
                                                WHERE CONTRACTINGSTATUSKONTRAKID IN ('5')");
    if ($countKontrakKontrakDisetujuiPenyedia->countRow() > 0) { 
        while ($countKontrakKontrakDisetujuiPenyedia->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_persiapan_kontrak?reqId='.$countKontrakKontrakDisetujuiPenyedia->getField("CONTRACTINGREKANANID").'">'.$countKontrakKontrakDisetujuiPenyedia->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrakKontrakDisetujuiPenyedia->countRow().'</span> 
                        Kontrak Disetujui Penyedia
                      </td>
                    </tr>';
        }
        $count += 1;
    }
    unset($countKontrakKontrakDisetujuiPenyedia);

    $countKontrakKontrakAddendum = new Queryfree();
    $countKontrakKontrakAddendum->selectByParams("SELECT CR_PERUBAHAN, NAMA, CONTRACTINGREKANANID FROM VIEW_CONTRACTING_PAKET 
                                                WHERE CR_PERUBAHAN = '1' AND CONTRACTINGPROSESID IN ('3')");
    if ($countKontrakKontrakAddendum->countRow() > 0) { 
        $countKontrak2 = new Queryfree();
        $countKontrak2->selectByParams("SELECT A.ADDENDUM_KE, A.NOMOR, A.CONTRACTINGREKANANID, A.STATUS, B.NAMA 
                                        FROM CONTRACTING_ADDENDUM A
                                        JOIN CONTRACTING_REKANAN B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID 
                                        WHERE A.APPROVED_KASUBDIT = '1' AND A.STATUS = 'Proses' OR (A.APPROVED_PENYEDIA IS NULL OR A.APPROVED_PENYEDIA = '' ) "); 
        if ($countKontrak2->countRow() > 0) { 
          while ($countKontrak2->nextRow()) {
            $html .= '<tr>
                        <td>
                          <a href="kontrak/index/contracting_monitoring_perubahan?reqId='.$countKontrak2->getField("CONTRACTINGREKANANID").'">'.$countKontrak2->getField("NAMA").' ( Addendum Ke '.$countKontrak2->getField("ADDENDUM_KE").' )</a>
                        </td>
                        <td>
                          <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak2->countRow().'</span> 
                          Perubahan Kontrak
                        </td>
                      </tr>';
          }
        }
        $count += 1;
    }
    unset($countKontrakKontrakAddendum);

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT CONTRACTINGREKANANID, CR_PERUBAHAN, NAMA FROM VIEW_CONTRACTING_PAKET 
                                                WHERE CR_PEMUTUSAN = '1' AND CONTRACTINGPROSESID IN ('3')");
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_monitoring_pemutusan?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        Pemutusan Kontrak
                      </td>
                    </tr>';
        }
        $count += 1;
    }


    $html  .=  ' </tbody>
              </table>';

    return $html;
  }

  function worklistPersiapanStaff()
  {
    include_once("functions/default.func.php");
    include_once("functions/string.func.php");
    include_once("functions/date.func.php");

    $this->_CI->load->model("Queryfree");

    $html = '';
    $html  .=  '<table class="table table-striped table-bordered row-grouping">
                  <thead>
                    <tr>
                      <th>Paket Pengadaan</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                <tbody>'; 

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT CONTRACTINGPROSESID, NAMA FROM VIEW_CONTRACTING_PAKET 
                                                WHERE SELESAI = '1' AND PAKET_METODE_LELANG_ID IN (1,2,3,4,5,7,8,10,11) AND STATUS_KONTRAK = 'Belum dibuat' AND APPROVE_PPK = '1' AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID." ");
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_paket_sppbj">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        SPPBJ belum dibuat
                      </td>
                    </tr>';
        }
        $count += 1;
    }

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK, B.NAMA, B.CONTRACTINGREKANANID FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('115') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_persiapan_sppbj?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        Kasubdit menolak SPPBJ
                      </td>
                    </tr>';
        }
        $count += 1;
    }

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK, B.NAMA, B.CONTRACTINGREKANANID FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('112') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_persiapan_sppbj?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        SBPPJ Disetujui PPK
                      </td>
                    </tr>';
        }
        $count += 1;
    }

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK, B.NAMA FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('2') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_persiapan_sppbj?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        SPPBJ Disetujui oleh Penyedia
                      </td>
                    </tr>';
        }
        $count += 1;
    }

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK, B.NAMA FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('3') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_persiapan_kontrak?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        Pembuatan kontrak
                      </td>
                    </tr>';
        }
        $count += 1;
    }

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK, B.NAMA FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('114') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_persiapan_kontrak?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        Kasubdit menolak Kontrak
                      </td>
                    </tr>';
        }
        $count += 1;
    }

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK, B.NAMA FROM CONTRACTING_REKANAN_PROSES1 A
                                   JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                   WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('51') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_persiapan_kontrak?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        Penyedia menolak kontrak
                      </td>
                    </tr>';
        }
        $count += 1;
    }

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK, B.NAMA FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('31') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_persiapan_kontrak?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        Kontrak Disetujui Kasubdit
                      </td>
                    </tr>';
        }
        $count += 1;
    }

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK, B.NAMA FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('5') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_persiapan_kontrak?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        Kontrak Disetujui Penyedia
                      </td>
                    </tr>';
        }
        $count += 1;
    }

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT CONTRACTINGREKANANID, CR_PERUBAHAN, NAMA FROM VIEW_CONTRACTING_PAKET 
                                                WHERE CR_PERUBAHAN = '1' AND CONTRACTINGPROSESID IN ('3') AND PIC_KONTRAK = '".$this->_CI->USER_LOGIN_ID."'");
    if ($countKontrak->countRow() > 0) {  

        $countKontrak2 = new Queryfree();
        $countKontrak2->selectByParams("SELECT A.ADDENDUM_KE, A.NOMOR, A.CONTRACTINGREKANANID, A.STATUS, B.NAMA 
                                        FROM CONTRACTING_ADDENDUM A
                                        JOIN CONTRACTING_REKANAN B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                        JOIN VIEW_CONTRACTING_PAKET C ON A.CONTRACTINGREKANANID=C.CONTRACTINGREKANANID
                                        WHERE A.APPROVED_KASUBDIT = '1' AND A.STATUS = 'Proses' AND PIC_KONTRAK = '".$this->_CI->USER_LOGIN_ID."' OR (A.APPROVED_PENYEDIA IS NULL OR A.APPROVED_PENYEDIA = '' ) "); 
        if ($countKontrak2->countRow() > 0) { 
          while ($countKontrak2->nextRow()) {
            $html .= '<tr>
                        <td>
                          <a href="kontrak/index/contracting_monitoring_perubahan?reqId='.$countKontrak2->getField("CONTRACTINGREKANANID").'">'.$countKontrak2->getField("NAMA").' ( Addendum Ke '.$countKontrak2->getField("ADDENDUM_KE").' )</a>
                        </td>
                        <td>
                          <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak2->countRow().'</span> 
                          Perubahan Kontrak
                        </td>
                      </tr>';
          }
        }
        $count += 1;
    }

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT CONTRACTINGREKANANID, CR_PERUBAHAN, NAMA FROM VIEW_CONTRACTING_PAKET 
                                                WHERE CR_PEMUTUSAN = '1' AND CONTRACTINGPROSESID IN ('3') AND PIC_KONTRAK = '".$this->_CI->USER_LOGIN_ID."'");
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_monitoring_pemutusan?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        Pemutusan Kontrak
                      </td>
                    </tr>';
        }
        $count += 1;
    }


    $html  .=  ' </tbody>
              </table>';

    return $html;
  }

  // ======== PENGENDALI KASI

  function worklistPengendaliKasi()
  {
    include_once("functions/default.func.php");
    include_once("functions/string.func.php");
    include_once("functions/date.func.php");

    $this->_CI->load->model("Queryfree");

    $html = '';
    $html  .=  '<table class="table table-striped table-bordered row-grouping">
                  <thead>
                    <tr>
                      <th>Paket Pengadaan</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                <tbody>'; 

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK, B.NAMA, B.CONTRACTINGREKANANID FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('6') AND PIC_PENGENDALI IS NOT NULL"); 
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_pengelolaan_realisasi?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        Pelaksanaan Kontrak
                      </td>
                    </tr>';
        }
        $count += 1;
    }  

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK, B.NAMA, B.CONTRACTINGREKANANID FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE PIC_PENGENDALI IS NULL AND A.CONTRACTINGSTATUSKONTRAKID IN ('6')"); 
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_pengelolaan">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        PIC belum ditunjuk
                      </td>
                    </tr>';
        }
        $count += 1;
    }  

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK, B.NAMA, B.CONTRACTINGREKANANID FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE CR_PERUBAHAN = '1' AND A.CONTRACTINGSTATUSKONTRAKID IN ('6')"); 
    if ($countKontrak->countRow() > 0) { 
      $countKontrak2 = new Queryfree();
      $countKontrak2->selectByParams("SELECT A.ADDENDUM_KE, A.NOMOR, A.CONTRACTINGREKANANID, A.STATUS, B.NAMA 
                                      FROM CONTRACTING_ADDENDUM A
                                      JOIN CONTRACTING_REKANAN B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                      WHERE A.STATUS = 'Proses'"); 
      if ($countKontrak2->countRow() > 0) { 
        while ($countKontrak2->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_monitoring_perubahan?reqId='.$countKontrak2->getField("CONTRACTINGREKANANID").'">'.$countKontrak2->getField("NAMA").' ( Addendum Ke '.$countKontrak2->getField("ADDENDUM_KE").' )</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak2->countRow().'</span> 
                        Perubahan Kontrak
                      </td>
                    </tr>';
        }
      }
        $count += 1;
    } 

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.STATUS, A.DELIVERY_NAMA, A.TANGGAL_DELIVERY_DARI, A.TANGGAL_DELIVERY_SAMPAI, B.PAKET_ID, B.NAMA, B.PIC_PENGENDALI, B.PIC_PENYELESAIAN
                                  FROM CONTRACTING_DELIVERABLE A 
                                  JOIN CONTRACTING_REKANAN B ON A.CONTRACTINGREKANANID = B.CONTRACTINGREKANANID
                                  WHERE A.STATUS = 'Proses' 
                                    AND A.TANGGAL_DELIVERY_DARI <= CURRENT_DATE
                                    AND A.TANGGAL_DELIVERY_SAMPAI >= CURRENT_DATE"); 
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_pengelolaan_realisasi?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").' ('.$countKontrak->getField('DELIVERY_NAMA').')</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        Relisasi Pekerjaan
                      </td>
                    </tr>';
        }
        $count += 1;
    } 


    $html  .=  ' </tbody>
              </table>';

    return $html;
  }

  function worklistPengendaliStaff()
  {
    include_once("functions/default.func.php");
    include_once("functions/string.func.php");
    include_once("functions/date.func.php");

    $this->_CI->load->model("Queryfree");

    $html = '';
    $html  .=  '<table class="table table-striped table-bordered row-grouping">
                  <thead>
                    <tr>
                      <th>Paket Pengadaan</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                <tbody>'; 

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.CONTRACTINGSTATUSKONTRAKID, B.NAMA, PIC_PENGENDALI FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('6') AND PIC_PENGENDALI = ".$this->_CI->USER_LOGIN_ID."");
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_pengelolaan_realisasi?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        Pelaksanaan Kontrak
                      </td>
                    </tr>';
        }
        $count += 1;
    }   

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.CONTRACTINGSTATUSKONTRAKID, B.NAMA, PIC_PENGENDALI FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE CR_PERUBAHAN = '1' AND A.CONTRACTINGSTATUSKONTRAKID IN ('6') AND PIC_PENGENDALI = ".$this->_CI->USER_LOGIN_ID."");
    if ($countKontrak->countRow() > 0) { 
      $countKontrak2 = new Queryfree();
      $countKontrak2->selectByParams("SELECT A.ADDENDUM_KE, A.NOMOR, A.CONTRACTINGREKANANID, A.STATUS, B.NAMA 
                                      FROM CONTRACTING_ADDENDUM A
                                      JOIN CONTRACTING_REKANAN B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                      WHERE A.STATUS = 'Proses' AND PIC_PENGENDALI = ".$this->_CI->USER_LOGIN_ID.""); 
      if ($countKontrak2->countRow() > 0) { 
        while ($countKontrak2->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_monitoring_perubahan?reqId='.$countKontrak2->getField("CONTRACTINGREKANANID").'">'.$countKontrak2->getField("NAMA").' ( Addendum Ke '.$countKontrak2->getField("ADDENDUM_KE").' )</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak2->countRow().'</span> 
                        Perubahan Kontrak
                      </td>
                    </tr>';
        }
      }
        $count += 1;
    }   

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.STATUS, A.DELIVERY_NAMA, A.TANGGAL_DELIVERY_DARI, A.TANGGAL_DELIVERY_SAMPAI, B.PAKET_ID, B.NAMA, B.PIC_PENGENDALI, B.PIC_PENYELESAIAN
                                  FROM CONTRACTING_DELIVERABLE A 
                                  JOIN CONTRACTING_REKANAN B ON A.CONTRACTINGREKANANID = B.CONTRACTINGREKANANID
                                  WHERE A.STATUS = 'Proses' AND B.PIC_PENGENDALI = ".$this->_CI->USER_LOGIN_ID."
                                    AND A.TANGGAL_DELIVERY_DARI <= CURRENT_DATE
                                    AND A.TANGGAL_DELIVERY_SAMPAI >= CURRENT_DATE"); 
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_pengelolaan_realisasi?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").' ('.$countKontrak->getField('DELIVERY_NAMA').')</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        Relisasi Pekerjaan
                      </td>
                    </tr>';
        }
        $count += 1;
    } 

    $html  .=  ' </tbody>
              </table>';

    return $html;
  }
  
  // ======== PENYELESAI KASI
  function worklistPenyelesaiKasi()
  {
    include_once("functions/default.func.php");
    include_once("functions/string.func.php");
    include_once("functions/date.func.php");

    $this->_CI->load->model("Queryfree");

    $html = '';
    $html  .=  '<table class="table table-striped table-bordered row-grouping">
                  <thead>
                    <tr>
                      <th>Paket Pengadaan</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                <tbody>'; 


    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK, B.NAMA, B.CONTRACTINGREKANANID FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE PIC_PENYELESAIAN IS NULL AND A.CONTRACTINGSTATUSKONTRAKID IN ('6')"); 
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_serah_terima">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        PIC belum ditunjuk
                      </td>
                    </tr>';
        }
        $count += 1;
    }  

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK, B.NAMA, B.CONTRACTINGREKANANID FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE CR_PERUBAHAN = '1' AND A.CONTRACTINGSTATUSKONTRAKID IN ('6')"); 
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_pengelolaan_realisasi?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        Perubahan Kontrak
                      </td>
                    </tr>';
        }
        $count += 1;
    }  

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK, B.NAMA, B.CONTRACTINGREKANANID FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE CR_DENDA = '1' AND A.CONTRACTINGSTATUSKONTRAKID IN ('6')"); 
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_monitoring_denda?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        Kontrak Sanksi dan Denda
                      </td>
                    </tr>';
        }
        $count += 1;
    }  

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.PAY_TERMIN_KE, A.PAY_STATUS, A.PAY_DATE_DARI, A.PAY_DATE_SAMPAI, B.PAKET_ID, B.NAMA, B.PIC_PENGENDALI, B.PIC_PENYELESAIAN
                                  FROM CONTRACTING_PAYMENT A 
                                  JOIN CONTRACTING_REKANAN B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                  WHERE A.PAY_STATUS IS NULL
                                    AND A.PAY_DATE_DARI <= CURRENT_DATE
                                    AND A.PAY_DATE_SAMPAI >= CURRENT_DATE"); 
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_pengelolaan_termin?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").' ('.$countKontrak->getField('PAY_TERMIN_KE').')</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        Tagihan
                      </td>
                    </tr>';
        }
        $count += 1;
    } 

    $html  .=  ' </tbody>
              </table>';

    return $html;
  }

  function worklistPenyelesaiStaff()
  {
    include_once("functions/default.func.php");
    include_once("functions/string.func.php");
    include_once("functions/date.func.php");

    $this->_CI->load->model("Queryfree");

    $html = '';
    $html  .=  '<table class="table table-striped table-bordered row-grouping">
                  <thead>
                    <tr>
                      <th>Paket Pengadaan</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                <tbody>'; 

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.CONTRACTINGSTATUSKONTRAKID, B.NAMA, PIC_PENYELESAIAN 
                                   FROM CONTRACTING_REKANAN_PROSES1 A
                                   JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                   WHERE CR_PERUBAHAN = '1' AND CONTRACTINGPROSESID IN ('3') AND PIC_PENYELESAIAN = ".$this->_CI->USER_LOGIN_ID."");
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_pengelolaan_termin?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        Pelaksanaan Kontrak
                      </td>
                    </tr>';
        }
        $count += 1;
    }  

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.CONTRACTINGSTATUSKONTRAKID, B.NAMA, PIC_PENYELESAIAN 
                                   FROM CONTRACTING_REKANAN_PROSES1 A
                                   JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                   WHERE CR_DENDA = '1' AND CONTRACTINGPROSESID IN ('3') AND PIC_PENYELESAIAN = ".$this->_CI->USER_LOGIN_ID."");
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_monitoring_denda?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        Kontrak Sanksi dan Denda
                      </td>
                    </tr>';
        }
        $count += 1;
    }  

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.PAY_TERMIN_KE, A.PAY_STATUS, A.PAY_DATE_DARI, A.PAY_DATE_SAMPAI, B.PAKET_ID, B.NAMA, B.PIC_PENGENDALI, B.PIC_PENYELESAIAN
                                  FROM CONTRACTING_PAYMENT A 
                                  JOIN CONTRACTING_REKANAN B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                  WHERE A.PAY_STATUS IS NULL AND B.PIC_PENYELESAIAN = ".$this->_CI->USER_LOGIN_ID."
                                    AND A.PAY_DATE_DARI <= CURRENT_DATE
                                    AND A.PAY_DATE_SAMPAI >= CURRENT_DATE"); 
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_pengelolaan_termin?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").' ('.$countKontrak->getField('PAY_TERMIN_KE').')</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        Tagihan
                      </td>
                    </tr>';
        }
        $count += 1;
    } 

    $html  .=  ' </tbody>
              </table>';

    return $html;
  }

  // ========= KASUBDIT

  function worklistKasubditKontrak()
  {
    include_once("functions/default.func.php");
    include_once("functions/string.func.php");
    include_once("functions/date.func.php");

    $this->_CI->load->model("Queryfree");

    $html = '';
    $html  .=  '<table class="table table-striped table-bordered row-grouping">
                  <thead>
                    <tr>
                      <th>Paket Pengadaan</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                <tbody>';

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK, B.NAMA, B.CONTRACTINGREKANANID FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('110')");
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_persiapan_sppbj?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        SPPBJ Proses Approval Kasubdit
                      </td>
                    </tr>';
        }
        $count += 1;
    }

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK, B.NAMA, B.CONTRACTINGREKANANID FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('113')");
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_persiapan_kontrak?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        Proses approval Kasubdit
                      </td>
                    </tr>';
        }
        $count += 1;
    }

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.CONTRACTINGREKANANID, A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK, B.NAMA, B.CONTRACTINGREKANANID FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('116')");
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_persiapan_kontrak?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        PPK menolak SPPBJ
                      </td>
                    </tr>';
        }
        $count += 1;
    }

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT B.CONTRACTINGREKANANID, B.NAMA  FROM CONTRACTING_ADDENDUM A 
                                    JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                        WHERE APPROVED_KASUBDIT IS NULL");
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_monitoring_perubahan?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        Addendum belum disetujui
                      </td>
                    </tr>';
        }
        $count += 1;
    }

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT CONTRACTINGREKANANID, CR_PERUBAHAN, NAMA FROM VIEW_CONTRACTING_PAKET 
                                                WHERE CR_PEMUTUSAN = '1' AND CONTRACTINGPROSESID IN ('3')");
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_monitoring_pemutusan?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        Pemutusan Kontrak
                      </td>
                    </tr>';
        }
        $count += 1;
    }

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.APPROVAL_KASUBDIT, A.CONTRACTINGREKANANID, B.NAMA, B.CONTRACTINGPROSESID 
                                    FROM PAKET_PENILAIAN_REKANAN A
                                    JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                    WHERE A.APPROVAL_KASUBDIT IS NULL AND B.CONTRACTINGPROSESID != '6'
                                    GROUP BY A.APPROVAL_KASUBDIT, A.CONTRACTINGREKANANID, B.NAMA, B.CONTRACTINGPROSESID");
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_penilaian?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        Approval Penilaian
                      </td>
                    </tr>';
        }
        $count += 1;
    }


    $html  .=  ' </tbody>
              </table>';

    return $html;
  }

  // ======== PPK

  function worklistPPK()
  {
    include_once("functions/default.func.php");
    include_once("functions/string.func.php");
    include_once("functions/date.func.php");

    $this->_CI->load->model("Queryfree");

    $html = '';
    $html  .=  '<table class="table table-striped table-bordered row-grouping">
                  <thead>
                    <tr>
                      <th>Paket Pengadaan</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                <tbody>'; 

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT NAMA, CONTRACTINGREKANANID FROM VIEW_CONTRACTING_REKANAN 
                                    WHERE CONTRACTINGSTATUSKONTRAKID IN ('111')");
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_persiapan_sppbj?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        SPPBJ Proses Approval PPK
                      </td>
                    </tr>';
        }
        $count += 1;
    }

    $countKontrak = new Queryfree();
    $countKontrak->selectByParams("SELECT A.APPROVAL_PPK, A.CONTRACTINGREKANANID, B.NAMA, B.CONTRACTINGPROSESID 
                                    FROM PAKET_PENILAIAN_REKANAN A
                                    JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                    WHERE A.APPROVAL_PPK IS NULL AND B.CONTRACTINGPROSESID != '6'
                                    GROUP BY A.APPROVAL_PPK, A.CONTRACTINGREKANANID, B.NAMA, B.CONTRACTINGPROSESID");
    if ($countKontrak->countRow() > 0) { 
        while ($countKontrak->nextRow()) {
          $html .= '<tr>
                      <td>
                        <a href="kontrak/index/contracting_penilaian?reqId='.$countKontrak->getField("CONTRACTINGREKANANID").'">'.$countKontrak->getField("NAMA").'</a>
                      </td>
                      <td>
                        <span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> 
                        Approval Penilaian
                      </td>
                    </tr>';
        }
        $count += 1;
    }


    $html  .=  ' </tbody>
              </table>';

    return $html;
  }

  function notifKontrak()
  {
    /**
      1. Table contracting_notifikasi (ok)
    **/
    $this->_CI->load->model("Queryfree");
    $countVerifikasi = new Queryfree();
    $countVerifikasi->selectByParams("SELECT * FROM CONTRACTING_NOTIFIKASI 
                                      WHERE (CURRENT_TIMESTAMP BETWEEN TANGGAL_NOTIFIKASI_DARI 
                                      AND COALESCE(TANGGAL_NOTIFIKASI_SAMPAI, TO_TIMESTAMP(TO_CHAR(TANGGAL_NOTIFIKASI_DARI, 'DDMMYYYY') || ' 23:59', 'DDMMYYYY HH24:MI'))) AND CREATED_BY = ".$this->_CI->USER_LOGIN_ID."");
    $count = 0;
    $html  = '';

    if ($countVerifikasi->countRow() > 0) {
      while($countVerifikasi->nextRow())
      {
        $html .= '<span class="dropdown-item" style="font-size:12px">'.$countVerifikasi->getField("JUDUL").'</span>';
        $count += 1;
      }
    }


    switch ($this->_CI->USER_TYPE_ID) {

      case '6': // PENYEDIA 
        $this->_CI->load->model("Queryfree");
        $countKontrak = new Queryfree();
        $countKontrak->selectByParams("SELECT A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK 
                                        FROM CONTRACTING_REKANAN_PROSES1 A
                                        JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                          WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('1') and pemenang && ARRAY[".$this->_CI->REKANAN_ID."]");

        if ($countKontrak->countRow() > 0) {
          while($countKontrak->nextRow())
          {
            $html .= '<a href="kontrak/index/contracting_penyedia"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Menunggu Konfirmasi dari Penyedia</span></a>';
            $count += 1;
          }
        }

        $countKontrak = new Queryfree();
        $countKontrak->selectByParams("SELECT A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK 
                                      FROM CONTRACTING_REKANAN_PROSES1 A
                                      JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                          WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('4') and pemenang && ARRAY[".$this->_CI->REKANAN_ID."]");
        if ($countKontrak->countRow() > 0) {
          while($countKontrak->nextRow())
          {
            $html .= '<a href="kontrak/index/contracting_penyedia"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Persetujuan Kontrak</span></a>';
            $count += 1;
          }
        }

        $countKontrak = new Queryfree();
            $countKontrak->selectByParams("SELECT CR_PERUBAHAN FROM VIEW_CONTRACTING_PAKET 
                                              WHERE CR_PERUBAHAN = '1' AND CONTRACTINGPROSESID IN ('3') and pemenang && ARRAY[".$this->_CI->REKANAN_ID."]");

            if ($countKontrak->countRow() > 0) {
              $html .= '<a href="kontrak/index/contracting_penyedia"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Perubahan Kontrak</span></a>';
              $count += 1;
            }

        break;

      case '12': // PENGGELOLA KONTRAK
        $html = '';
        switch ($this->_CI->LEVEL_KONTRAK) {
          case '1': // Pengelola

            $this->_CI->load->model("Queryfree");

            /// ============== UNTUK KASI
            if ($this->_CI->PENUNJUK_PIC == '1') // KASI PIC Kontrak 
            {
              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CONTRACTINGPROSESID FROM VIEW_CONTRACTING_PAKET 
                                                WHERE SELESAI = '1' AND PAKET_METODE_LELANG_ID IN (1,2,3,4,5,7,8,10,11) AND STATUS_KONTRAK = 'Belum dibuat' AND APPROVE_PPK = '1' AND PIC_KONTRAK IS NULL");
              if ($countKontrak->countRow() > 0) { 
                $html .= '<a href="kontrak/index/contracting_penunjukan_pic"><span class="dropdown-item" style="font-size:12px" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> PCI belum ditunjuk</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CONTRACTINGPROSESID FROM VIEW_CONTRACTING_PAKET 
                                                WHERE SELESAI = '1' AND PAKET_METODE_LELANG_ID IN (1,2,3,4,5,7,8,10,11) AND STATUS_KONTRAK = 'Belum dibuat' AND APPROVE_PPK = '1' AND PIC_KONTRAK IS NOT NULL");
              if ($countKontrak->countRow() > 0) { 
                $html .= '<a href="kontrak/index/contracting_paket_sppbj"><span class="dropdown-item" style="font-size:12px" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> SPPBJ belum dibuat</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID FROM CONTRACTING_REKANAN_PROSES1 
                                                WHERE CONTRACTINGSTATUSKONTRAKID IN ('115')");
              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Kasubdit menolak SPPBJ</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID FROM CONTRACTING_REKANAN_PROSES1 
                                                WHERE CONTRACTINGSTATUSKONTRAKID IN ('112')");
              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> SBPPJ Disetujui PPK</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID FROM CONTRACTING_REKANAN_PROSES1 
                                                WHERE CONTRACTINGSTATUSKONTRAKID IN ('0')");
              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Pembuatan SPPBJ</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID FROM CONTRACTING_REKANAN_PROSES1 
                                                WHERE CONTRACTINGSTATUSKONTRAKID IN ('2')");
              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> SPPBJ Disetujui oleh Penyedia</span></a>';
                $count += 1;
              }


              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID FROM CONTRACTING_REKANAN_PROSES1 
                                                WHERE CONTRACTINGSTATUSKONTRAKID IN ('3')");
              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Pembuatan kontrak</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID FROM CONTRACTING_REKANAN_PROSES1 
                                                WHERE CONTRACTINGSTATUSKONTRAKID IN ('114')");
              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Kasubdit menolak Kontrak</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID FROM CONTRACTING_REKANAN_PROSES1 
                                                WHERE CONTRACTINGSTATUSKONTRAKID IN ('51')");

              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Penyedia menolak kontrak</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID FROM CONTRACTING_REKANAN_PROSES1 
                                                WHERE CONTRACTINGSTATUSKONTRAKID IN ('31')");
              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Kontrak Disetujui Kasubdit</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID FROM CONTRACTING_REKANAN_PROSES1 
                                                WHERE CONTRACTINGSTATUSKONTRAKID IN ('5')");

              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Kontrak Disetujui Penyedia</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CR_PERUBAHAN FROM VIEW_CONTRACTING_PAKET 
                                                WHERE CR_PERUBAHAN = '1' AND CONTRACTINGPROSESID IN ('3')");
              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_pengelolaan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Perubahan Kontrak</span></a>';
                $count += 1;
              }

            }
            // ================ UNTUK STAFF =============
            else 
            {

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CONTRACTINGPROSESID FROM VIEW_CONTRACTING_PAKET 
                                                WHERE SELESAI = '1' AND PAKET_METODE_LELANG_ID IN (1,2,3,4,5,7,8,10,11) AND STATUS_KONTRAK = 'Belum dibuat' AND APPROVE_PPK = '1' AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID." ");
              if ($countKontrak->countRow() > 0) { 
                $html .= '<a href="kontrak/index/contracting_paket_sppbj"><span class="dropdown-item" style="font-size:12px" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> SPPBJ belum dibuat</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('115') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");
              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Kasubdit menolak SPPBJ</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('112') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");
              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> SBPPJ Disetujui PPK</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('2') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");
              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> SPPBJ Disetujui oleh Penyedia</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('3') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");
              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Pembuatan kontrak</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('114') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");
              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Kasubdit menolak Kontrak</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('51') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");

              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Penyedia menolak kontrak</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('31') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");
              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Kontrak Disetujui Kasubdit</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT A.CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('5') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");

              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Kontrak Disetujui Penyedia</span></a>';
                $count += 1;
              }


              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CR_PERUBAHAN FROM VIEW_CONTRACTING_PAKET 
                                                WHERE CR_PERUBAHAN = '1' AND CONTRACTINGPROSESID IN ('3') AND PIC_KONTRAK = '".$this->_CI->USER_LOGIN_ID."'");
              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_pengelolaan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Perubahan Kontrak</span></a>';
                $count += 1;
              }

            }

            break;

          case '2': // Pengendali

            /// ============== UNTUK KASI
            if ($this->_CI->PENUNJUK_PIC == '1') // KASI PENGENDALI 
            {
              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID FROM CONTRACTING_REKANAN_PROSES1 
                                                WHERE CONTRACTINGSTATUSKONTRAKID IN ('6')");

              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_pengelolaan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Pelaksanaan Kontrak</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CR_PERUBAHAN FROM VIEW_CONTRACTING_PAKET 
                                                WHERE CR_PERUBAHAN = '1' AND CONTRACTINGPROSESID IN ('3')");

              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_pengelolaan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Perubahan Kontrak</span></a>';
                $count += 1;
              }

            }
            // ================ UNTUK STAFF =============
            else 
            {
              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT A.CONTRACTINGSTATUSKONTRAKID, PIC_PENGENDALI FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('6') AND PIC_PENGENDALI = ".$this->_CI->USER_LOGIN_ID."");

              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_pengelolaan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Pelaksanaan Kontrak</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CR_PERUBAHAN FROM VIEW_CONTRACTING_PAKET 
                                                WHERE CR_PERUBAHAN = '1' AND CONTRACTINGPROSESID IN ('3')");

              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_pengelolaan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Perubahan Kontrak</span></a>';
                $count += 1;
              }
            }

            break;

          case '3': // Penelesai
            $countKontrak = new Queryfree();
            $countKontrak->selectByParams("SELECT CR_PERUBAHAN FROM VIEW_CONTRACTING_PAKET 
                                              WHERE CR_PERUBAHAN = '1' AND CONTRACTINGPROSESID IN ('3')");

            if ($countKontrak->countRow() > 0) {
              $html .= '<a href="kontrak/index/contracting_serah_terima"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Perubahan Kontrak</span></a>';
              $count += 1;
            }
            break;
          
          default:
            // code...
            break;
        } 

        break;

      case '20': // KASUBDIT KONTRAK 
        $html = '';
        $this->_CI->load->model("Queryfree");
        $countKontrak = new Queryfree();
        $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID FROM CONTRACTING_REKANAN_PROSES1 
                                          WHERE CONTRACTINGSTATUSKONTRAKID IN ('110')");
        if ($countKontrak->countRow() > 0) {
            $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> SPPBJ Proses Approval Kasubdit</span></a>';
            $count += 1;
        }

        $countKontrak = new Queryfree();
        $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID FROM CONTRACTING_REKANAN_PROSES1 
                                          WHERE CONTRACTINGSTATUSKONTRAKID IN ('113')");
        if ($countKontrak->countRow() > 0) {
            $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Proses approval Kasubdit</span></a>';
            $count += 1;
        }

        $countKontrak = new Queryfree();
        $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID FROM CONTRACTING_REKANAN_PROSES1 
                                          WHERE CONTRACTINGSTATUSKONTRAKID IN ('116')");
        if ($countKontrak->countRow() > 0) {
            $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> PPK menolak SPPBJ</span></a>';
            $count += 1;
        }

        $countAddendum = new Queryfree();
        $countAddendum->selectByParams("SELECT * FROM CONTRACTING_ADDENDUM 
                                        WHERE APPROVED_KASUBDIT IS NULL");
        if ($countAddendum->countRow() > 0) {
            $html .= '<a href="kontrak/index/contracting_pengelolaan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countAddendum->countRow().'</span> Addendum belum disetujui</span></a>';
            $count += 1;
        }


        break;

      case '28': // PPK 
        $html = '';
        $this->_CI->load->model("Queryfree");
        $countKontrak = new Queryfree();
        $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID FROM CONTRACTING_REKANAN_PROSES1 
                                          WHERE CONTRACTINGSTATUSKONTRAKID IN ('111')");
        if ($countKontrak->countRow() > 0) {
            $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> SPPBJ Proses Approval PPK</span></a>';
            $count += 1;
        } 
        break;
      
      default:
        // code...
        break;
    }


    if ($count == 0) {
      $html .= '<span class="dropdown-item" style="font-size:12px">. : : Tidak ada pesan : : . </span>';
      $count = '';
    }

    return array('data' => $html, 'count' => $count);
  }

}
