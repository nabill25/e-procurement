<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

class libnotification
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
    $this->_CI->REKANAN_ID      =  $this->_CI->kauth->getInstance()->getIdentity()->REKANAN_ID;
    $this->_CI->HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
    $this->_CI->UNIT_KERJA_ID   =  $this->_CI->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID;
  }

  function notifAdminVMS()
  {
    /**
      1. Vendor Proses Approval (ok)
      2. Katalog belum diverifikasi (ok)
      3. Komplain Masuk yang belum di respone (ok)
    **/
    $this->_CI->load->model("Queryfree");
    $kirimBerkas = new Queryfree();
    $kirimBerkas->selectByParams("SELECT USER_LOGIN_ID FROM USER_LOGIN WHERE USER_STATUS = '2' ");
    $count = 0;
    $html  = '';
    if ($kirimBerkas->countRow() > 0) {
      $html .= '<a href="'.base_url('/main/index/daftar_rekanan_belum_valid').'"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$kirimBerkas->countRow().'</span> Vendor Proses Approval </span></a>';
      $count += 1;
    }

    // Katalog belum verifikasi
    $katalog = new Queryfree();
    $katalog->selectByParams("SELECT KATALOGID FROM KATALOG WHERE PUBLISH = '0' ");
    if ($katalog->countRow() > 0) {
      $html .= '<a href="'.base_url('/main/index/katalog_validasi').'"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$katalog->countRow().'</span> Katalog belum verifikasi </span></a>';
      $count += 1;
    }

    // Komplain Masuk yang belum di respone
    $countComplain = new Queryfree();
    $countComplain->selectByParams("SELECT a2.* from (
                                    SELECT a.*,
                                        (SELECT z.inboxid from inbox z where cast(z.parent AS INTEGER) = a.inboxid ) jawab
                                        from (
                                      SELECT
                                        A.inboxid,
                                        A.inboxcategoryid,
                                        A.parent,
                                        b.ic_name
                                      FROM
                                        inbox
                                        A INNER JOIN inbox_category b ON A.inboxcategoryid = b.inboxcategoryid
                                      where a.inboxcategoryid = 3 and a.parent = '0'
                                      ) a
                                    ) a2 where a2.jawab is null");
    if ($countComplain->countRow() > 0) {
      $countComplain->firstRow();

      if ($countComplain->getField("JAWAB") == '') {
        $html .= '<a href="'.base_url('/main/index/inbox_complain').'"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countComplain->countRow().'</span> Pertanyaan belum di respon </span></a>';
        $count += 1;
      } else {
      }
    }

    if ($count == 0) {
      $html .= '<span class="dropdown-item" style="font-size:12px">. : : Tidak ada pesan : : . </span>';
      $count = '';
    }

    return array('data' => $html, 'count' => $count);
  }

  function notifRekanan()
  {
    /**
      1. Dokumen Expired (ok)
      2. RFI belum di jawab (ok)
      3. Survei belum di jawab (ok)
      4. Pertanyaan belum di jawab (ok)
      5. Notifikasi Negosiasi Item (ok)
    **/
    $this->_CI->load->model("Queryfree");
    $countDokExpired = new Queryfree();
    $countDokExpired->selectByParams("SELECT REKANAN_ID FROM view_rekanan_dokumen_expired WHERE REKANAN_ID = ".$this->_CI->ID." ");
    $count = 0;
    $html  = '';
    if ($countDokExpired->countRow() > 0) {
      $html .= '<span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countDokExpired->countRow().'</span> Dokumen Expired </span>';
      $count += 1;
    }

    // RFI
    $countRFI = new Queryfree();
    $countRFI->selectByParams("SELECT a2.* from (
                              SELECT a.*,
                                  (SELECT z.inboxid from inbox z where cast(z.parent AS INTEGER) = a.inboxid AND cast(a.rekanan_id AS INTEGER) = z.created_by ) jawab
                                  from (
                                SELECT UNNEST
                                  (
                                  string_to_array( A.inbox_to, ',' )) rekanan_id,
                                  A.inboxid,
                                  A.inboxcategoryid,
                                  A.parent,
                                  b.ic_name
                                FROM
                                  inbox
                                  A INNER JOIN inbox_category b ON A.inboxcategoryid = b.inboxcategoryid
                                where a.inboxcategoryid = 1 and a.parent = '0'
                                ) a where a.rekanan_id = '".$this->_CI->ID."'
                              ) a2 where a2.jawab is null   ");
    if ($countRFI->countRow() > 0) {
      $countRFI->firstRow();

      if ($countRFI->getField("JAWAB") == '') {
        $html .= '<a href="'.base_url('/main/index/inbox_rfi_penyedia').'"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countRFI->countRow().'</span> RFI belum di respon </span></a>';
        $count += 1;
      } else {
      }
    }

    // Survei
    $countSurvei = new Queryfree();
    $countSurvei->selectByParams("SELECT a2.* from (
                              SELECT a.*,
                                  (SELECT z.inboxid from inbox z where cast(z.parent AS INTEGER) = a.inboxid AND cast(a.rekanan_id AS INTEGER) = z.created_by ) jawab
                                  from (
                                SELECT UNNEST
                                  (
                                  string_to_array( A.inbox_to, ',' )) rekanan_id,
                                  A.inboxid,
                                  A.inboxcategoryid,
                                  A.parent,
                                  b.ic_name
                                FROM
                                  inbox
                                  A INNER JOIN inbox_category b ON A.inboxcategoryid = b.inboxcategoryid
                                where a.inboxcategoryid = 2 and a.parent = '0'
                                ) a where a.rekanan_id = '".$this->_CI->ID."'
                              ) a2 where a2.jawab is null   ");
    if ($countSurvei->countRow() > 0) {
      $countSurvei->firstRow();

      if ($countSurvei->getField("JAWAB") == '') {
        $html .= '<a href="'.base_url('/main/index/inbox_survei_penyedia').'"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countSurvei->countRow().'</span> Survei belum di respon </span></a>';
        $count += 1;
      }
    }

    // Pertanyaan / Complain
    $countPertanyaan = new Queryfree();
    $countPertanyaan->selectByParams("SELECT a2.* from (
                              SELECT a.*,
                                  (SELECT z.inboxid from inbox z where cast(z.parent AS INTEGER) = a.inboxid AND cast(z.inbox_to AS INTEGER) = A.created_by  ) jawab
                                  from (
                                SELECT UNNEST
                                  (
                                  string_to_array( A.inbox_to, ',' )) rekanan_id,
                                  A.inboxid,
                                  A.inboxcategoryid,
                                  A.parent,
                                  A.created_by,
                                  b.ic_name
                                FROM
                                  inbox
                                  A INNER JOIN inbox_category b ON A.inboxcategoryid = b.inboxcategoryid
                                where a.inboxcategoryid = 3 and a.parent = '0'
                                ) a where a.created_by = '".$this->_CI->ID."'
                              ) a2 where a2.jawab is null   ");
    if ($countPertanyaan->countRow() > 0) {
      $countPertanyaan->firstRow();

      if ($countPertanyaan->getField("JAWAB") == '') {
        $html .= '<a href="'.base_url('/main/index/inbox_complain_penyedia').'"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countPertanyaan->countRow().'</span> Pertanyaan belum di respon </span></a>';
        $count += 1;
      }
    } 

    // Negosiasi Item
    $this->_CI->load->model("PaketTahap");
    $paket_tahap = new PaketTahap();
    $paket_tahap_metode = new PaketTahap();
    $arrNegosiasi                    = NEGOSIASI;
    $countNogoItem = new Queryfree();
    $countNogoItem->selectByParams("SELECT B.PAKET_ID, COUNT(PAKET_NEGOSIASI_ITEM_ID) TOTAL 
                                    FROM PAKET A 
                                    LEFT JOIN PAKET_NEGOSIASI_ITEM B ON A.PAKET_ID = B.PAKET_ID
                                    WHERE A.REKANAN_ID_PEMENANG = '".$this->_CI->ID."' AND B.STATUS_NEGO != '1'
                                    GROUP BY B.PAKET_ID");
    if ($countNogoItem->countRow() > 0) { 
      while($countNogoItem->nextRow())
      {
        $jenis_tahap = $paket_tahap_metode->getJenisTahapById($countNogoItem->getField('PAKET_ID'));
        $aktif_negosiasi = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrNegosiasi[$jenis_tahap], "PAKET_ID" => $countNogoItem->getField('PAKET_ID'), "TAMPILKAN" => 1));
        $aktif_negosiasi2 = $paket_tahap->getCountByParamsBerlalu(array("URUT" => $arrNegosiasi[$jenis_tahap], "PAKET_ID" => $countNogoItem->getField('PAKET_ID'), "TAMPILKAN" => 1));

        if($aktif_negosiasi > 0 || $aktif_negosiasi2 < 1)
        {
          $html .= '<a href="'.base_url('/main/index/negosiasi_rekanan/?reqId='.$countNogoItem->getField('PAKET_ID').'').'"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">1</span> Negosiasi dalam proses </span></a>';
          $count += 1;
        } else {
          $html .= '';
        }
      }
    }

    if ($count == 0) {
      $html .= '<span class="dropdown-item" style="font-size:12px">. : : Tidak ada pesan : : . </span>';
      $count = '';
    }


    return array('data' => $html, 'count' => $count);
  }

  function notifPenyeliaVMS()
  {
    /**
      1. Approval Penyedia (ok)
    **/
    $this->_CI->load->model("Queryfree");
    $countVerifikasi = new Queryfree();
    $countVerifikasi->selectByParams("SELECT REKANAN_ID FROM REKANAN WHERE STATUS_VALIDASI = '3'");
    $count = 0;
    $html  = '';
    if ($countVerifikasi->countRow() > 0) {
      $html .= '<span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countVerifikasi->countRow().'</span> Vendor Proses Approval </span>';
      $count += 1;
    }

    if ($count == 0) {
      $html .= '<span class="dropdown-item" style="font-size:12px">. : : Tidak ada pesan : : . </span>';
      $count = '';
    }

    return array('data' => $html, 'count' => $count);
  }

  function notifApprovalVMS()
  {
    /**
      1. Approval Penyedia (ok)
    **/
    $this->_CI->load->model("Queryfree");
    $countVerifikasi = new Queryfree();
    $countVerifikasi->selectByParams("SELECT REKANAN_ID FROM REKANAN WHERE STATUS_VALIDASI = '4'");
    $count = 0;
    $html  = '';
    if ($countVerifikasi->countRow() > 0) {
      $html .= '<a href="'.base_url('/main/index/daftar_rekanan_approval').'"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countVerifikasi->countRow().'</span> Vendor Proses Approval </span></a>';
      $count += 1;
    }

    if ($count == 0) {
      $html .= '<span class="dropdown-item" style="font-size:12px">. : : Tidak ada pesan : : . </span>';
      $count = '';
    }

    return array('data' => $html, 'count' => $count);
  }

  function notifPengguna()
  {
    /**
    **/
    $this->_CI->load->model("Queryfree"); 
    $html = '';
    $count = 0;

    if ($count == 0) {
      $html .= '<span class="dropdown-item" style="font-size:12px">. : : Tidak ada pesan : : . </span>';
      $count = '';
    }
    
    return array('data' => $html, 'count' => $count);
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
        $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK 
                                        FROM CONTRACTING_REKANAN_PROSES1 A
                                        JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                          WHERE CONTRACTINGSTATUSKONTRAKID IN ('1') and pemenang && ARRAY[".$this->_CI->REKANAN_ID."]");

        if ($countKontrak->countRow() > 0) {
          while($countKontrak->nextRow())
          {
            $html .= '<a href="kontrak/index/contracting_penyedia"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Menunggu Konfirmasi dari Penyedia</span></a>';
            $count += 1;
          }
        }

        $countKontrak = new Queryfree();
        $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK 
                                      FROM CONTRACTING_REKANAN_PROSES1 A
                                      JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                          WHERE CONTRACTINGSTATUSKONTRAKID IN ('4') and pemenang && ARRAY[".$this->_CI->REKANAN_ID."]");
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

        $countKontrak = new Queryfree();
        $countKontrak->selectByParams("SELECT CR_PERUBAHAN FROM VIEW_CONTRACTING_PAKET 
                                          WHERE CR_DENDA = '1' AND CONTRACTINGPROSESID IN ('3') and pemenang && ARRAY[".$this->_CI->REKANAN_ID."]");

        if ($countKontrak->countRow() > 0) {
          $html .= '<a href="kontrak/index/contracting_penyedia"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Sanksi atau Denda</span></a>';
          $count += 1;
        }

        $countKontrak = new Queryfree();
        $countKontrak->selectByParams("SELECT CR_PERUBAHAN FROM VIEW_CONTRACTING_PAKET 
                                          WHERE CR_PEMUTUSAN = '1' AND CONTRACTINGPROSESID IN ('3') and pemenang && ARRAY[".$this->_CI->REKANAN_ID."]");

        if ($countKontrak->countRow() > 0) {
          $html .= '<a href="kontrak/index/contracting_penyedia"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Pemutusan</span></a>';
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
                $html .= '<a href="kontrak/index/contracting_pengelolaan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Kontrak Addendum</span></a>';
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
              $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE CONTRACTINGSTATUSKONTRAKID IN ('115') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");
              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Kasubdit menolak SPPBJ</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE CONTRACTINGSTATUSKONTRAKID IN ('112') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");
              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> SBPPJ Disetujui PPK</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE CONTRACTINGSTATUSKONTRAKID IN ('2') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");
              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> SPPBJ Disetujui oleh Penyedia</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE CONTRACTINGSTATUSKONTRAKID IN ('3') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");
              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Pembuatan kontrak</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE CONTRACTINGSTATUSKONTRAKID IN ('114') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");
              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Kasubdit menolak Kontrak</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE CONTRACTINGSTATUSKONTRAKID IN ('51') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");

              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Penyedia menolak kontrak</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE CONTRACTINGSTATUSKONTRAKID IN ('31') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");
              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Kontrak Disetujui Kasubdit</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID, PIC_KONTRAK FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE CONTRACTINGSTATUSKONTRAKID IN ('5') AND PIC_KONTRAK = ".$this->_CI->USER_LOGIN_ID."");

              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_persiapan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Kontrak Disetujui Penyedia</span></a>';
                $count += 1;
              }


              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CR_PERUBAHAN FROM VIEW_CONTRACTING_PAKET 
                                                WHERE CR_PERUBAHAN = '1' AND CONTRACTINGPROSESID IN ('3') AND PIC_KONTRAK = '".$this->_CI->USER_LOGIN_ID."'");
              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_pengelolaan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Kontrak Addendum</span></a>';
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
                                                WHERE CONTRACTINGSTATUSKONTRAKID IN ('6') AND PIC_PENGENDALI IS NOT NULL");

              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_pengelolaan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Pelaksanaan Kontrak</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CR_PERUBAHAN FROM VIEW_CONTRACTING_PAKET 
                                                WHERE CR_PERUBAHAN = '1' AND CONTRACTINGPROSESID IN ('3')");

              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_pengelolaan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Kontrak Addendum</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CR_DENDA FROM VIEW_CONTRACTING_PAKET 
                                                WHERE CR_DENDA = '1' AND CONTRACTINGPROSESID IN ('3')");

              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_pengelolaan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Kontrak Sanksi dan Denda</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CR_PEMUTUSAN FROM VIEW_CONTRACTING_PAKET 
                                                WHERE CR_PEMUTUSAN = '1' AND CONTRACTINGPROSESID IN ('3')");

              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_pengelolaan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Pemutusan Kontrak</span></a>';
                $count += 1;
              }

            }
            // ================ UNTUK STAFF =============
            else 
            {
              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CONTRACTINGSTATUSKONTRAKID, PIC_PENGENDALI FROM CONTRACTING_REKANAN_PROSES1 A
                                             JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                             WHERE CONTRACTINGSTATUSKONTRAKID IN ('6') AND PIC_PENGENDALI = ".$this->_CI->USER_LOGIN_ID."");

              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_pengelolaan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Pelaksanaan Kontrak</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CR_PERUBAHAN FROM VIEW_CONTRACTING_PAKET 
                                                WHERE CR_PERUBAHAN = '1' AND CONTRACTINGPROSESID IN ('3') AND PIC_PENGENDALI = ".$this->_CI->USER_LOGIN_ID."");

              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_pengelolaan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Kontrak Addendum</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CR_DENDA FROM VIEW_CONTRACTING_PAKET 
                                                WHERE CR_DENDA = '1' AND CONTRACTINGPROSESID IN ('3') AND PIC_PENGENDALI = ".$this->_CI->USER_LOGIN_ID."");

              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_pengelolaan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Kontrak Sanksi dan Denda</span></a>';
                $count += 1;
              }

              $countKontrak = new Queryfree();
              $countKontrak->selectByParams("SELECT CR_PEMUTUSAN FROM VIEW_CONTRACTING_PAKET 
                                                WHERE CR_PEMUTUSAN = '1' AND CONTRACTINGPROSESID IN ('3') AND PIC_PENGENDALI = ".$this->_CI->USER_LOGIN_ID."");

              if ($countKontrak->countRow() > 0) {
                $html .= '<a href="kontrak/index/contracting_pengelolaan"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Pemutusan Kontrak</span></a>';
                $count += 1;
              }

            }

            break;

          case '3': // Penelesai
            $countKontrak = new Queryfree();
            $countKontrak->selectByParams("SELECT CR_PERUBAHAN FROM VIEW_CONTRACTING_PAKET 
                                              WHERE CR_PERUBAHAN = '1' AND CONTRACTINGPROSESID IN ('3')");

            if ($countKontrak->countRow() > 0) {
              $html .= '<a href="kontrak/index/contracting_serah_terima"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$countKontrak->countRow().'</span> Kontrak Addendum</span></a>';
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
        $countKontrak->selectByParams("SELECT A.CONTRACTINGSTATUSKONTRAKID FROM CONTRACTING_REKANAN_PROSES1 A
                                          JOIN PAKET B ON A.PAKET_ID=B.PAKET_ID
                                          WHERE A.CONTRACTINGSTATUSKONTRAKID IN ('111') AND B.UNIT_KERJA_ID = '".$this->_CI->UNIT_KERJA_ID."'");
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

  function notifManagerPengadaan()
  {
    /**
      1. Kaji Ulang (ok)
      1. Penunjukan PIC (ok)
    **/
    $this->_CI->load->model("PermohonanPaket");
    $statement .= " AND A.POSTING IS NOT NULL AND A.KAJI_ULANG = '0' AND STRATEGI_PENGADAAN = 'Sourcing' AND PIC IS NOT NULL"; 
    $statement .= " AND D.PAKET_ID IS NULL AND A.POSTING IS NOT NULL ";

    $kajiUlang = new PermohonanPaket();
    $kajiUlang->selectByParams(array(), -1, -1, $statement);

    $count = 0;
    $html  = '';
    if ($kajiUlang->countRow() > 0) {
      $html .= '<a href="'.base_url('/main/index/permohonan_paket_kaji_ulang').'"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$kajiUlang->countRow().'</span> Kaji Ulang belum selesai </span></a>';
      $count += 1;
    } 

    $statement2 .= " AND STRATEGI_PENGADAAN = 'Sourcing' AND D.PAKET_ID IS NULL AND A.POSTING IS NOT NULL AND (A.PIC IS NULL) ";

    $PenunjukanPIC = new PermohonanPaket();
    $PenunjukanPIC->selectByParams(array(), -1, -1, $statement2);

    if ($PenunjukanPIC->countRow() > 0) {
      $html .= '<a href="'.base_url('/main/index/permohonan_penunjukan_pic').'"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$PenunjukanPIC->countRow().'</span> Belum tunjuk ketua </span></a>';
      $count += 1;
    } 

    if ($count == 0) {
      $html .= '<span class="dropdown-item" style="font-size:12px">. : : Tidak ada pesan : : . </span>';
      $count = '';
    }

    return array('data' => $html, 'count' => $count);
  }

  function notifPokja()
  {
    /**
      1. Kaji Ulang (ok)
    **/
    // Cek Kelompok Kerja
    $this->_CI->load->model(array("Queryfree","PermohonanPaket"));

    $getPokjaID = new Queryfree();
    $getPokjaID->selectByParams("SELECT sk_panitia_id, user_login_id, a.nama, a.nip, b.nip
                FROM panitia a 
                JOIN user_login b on a.nip=b.nip
                WHERE USER_LOGIN_ID = ".$this->_CI->USER_LOGIN_ID."
                ");
    $getPokjaID->firstRow();
    $SK = $getPokjaID->getField("SK_PANITIA_ID");

    $statement .= " AND A.POSTING IS NOT NULL AND SK_PANITIA_ID = ".$SK." AND A.KAJI_ULANG = '0' AND STRATEGI_PENGADAAN = 'Sourcing'"; 
    $statement .= " AND D.PAKET_ID IS NULL AND A.POSTING IS NOT NULL ";

    $permohonan_paket = new PermohonanPaket();
    $permohonan_paket->selectByParams(array(), -1, -1, $statement);

    $count = 0;
    $html  = '';
    if ($permohonan_paket->countRow() > 0) {
      $html .= '<a href="'.base_url('/main/index/permohonan_paket_kaji_ulang').'"><span class="dropdown-item" style="font-size:12px"><span class="badge badge-info round" style="padding:4px 7px">'.$permohonan_paket->countRow().'</span> Kaji Ulang belum selesai </span></a>';
      $count += 1;
    } 

    if ($count == 0) {
      $html .= '<span class="dropdown-item" style="font-size:12px">. : : Tidak ada pesan : : . </span>';
      $count = '';
    }

    return array('data' => $html, 'count' => $count);
  }

}
