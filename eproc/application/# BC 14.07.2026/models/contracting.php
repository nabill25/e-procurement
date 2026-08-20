<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Contracting extends Entity{

  var $query;

    function __construct(){
      $this->Entity();
    }

    function canInsert(){
      return true;
    }

  function insertProses1()
  {

    return $this->execQuery($str);
  }

  function insertContracting() // Done
  {
    $this->setField("CONTRACTINGREKANANID", $this->getNextId("CONTRACTINGREKANANID","CONTRACTING_REKANAN"));

    $str = "
    INSERT INTO CONTRACTING_REKANAN (
               CONTRACTINGREKANANID, PAKET_ID, NAMA, URAIAN, NILAI, CONTRACTINGPROSESID, REKANAN_ID, JNS_KONTRAK, CREATED_BY, CREATED_DATE)
        VALUES (
            ".$this->getField("CONTRACTINGREKANANID").",
            ".$this->getField("PAKET_ID").",
            '".$this->getField("NAMA")."',
            '".$this->getField("URAIAN")."',
            ".$this->getField("NILAI").",
            '".$this->getField("CONTRACTINGPROSESID")."',
            ".$this->getField("REKANAN_ID").",
            '".$this->getField("JNS_KONTRAK")."',
            ".$this->getField("CREATED_BY").",
            CURRENT_TIMESTAMP
        )";
        // echo $str; die();
    $this->query = $str;
    if ($this->execQuery($str)) {

      $this->setField("CONTRACTINGREKANANPROSES1ID", $this->getNextId("CONTRACTINGREKANANPROSES1ID","CONTRACTING_REKANAN_PROSES1"));

      $str2 = "
      INSERT INTO CONTRACTING_REKANAN_PROSES1 (
                 CONTRACTINGREKANANPROSES1ID, PAKET_ID,CONTRACTINGREKANANID,CR_SPPBJ_CODE,CR_SPPBJ_TANGGAL,CR_SPPBJ_DIRUT,CR_SPPBJ_DIRUT_ALAMAT,CR_SPPBJ_DIRUT_KOTA,CR_SPPBJ_DIRUT_JABATAN,CR_SPPBJ_JAMINAN_PELAKSANA,CR_SPPBJ_JAMINAN_BESAR,CR_SPPBJ_JAMINAN_JANGKA_DARI,CR_SPPBJ_JAMINAN_JANGKA_SAMPAI,CR_SPPBJ_JAMINAN_NILAI,CR_SPPBJ_PEJABAT_BERWENANG,CR_SPPBJ_NIP,CR_SPPBJ_JABATAN,CR_SPPBJ_PPN,CR_SPPBJ_PELAKSANAAN_DARI,CR_SPPBJ_PELAKSANAAN_SAMPAI,CR_SPPBJ_CREATED_BY,CR_SPPBJ_CREATED_DATE,CR_SPPBJ_NILAI,CONTRACTINGSTATUSKONTRAKID,CR_JENIS_PENGADAAN,REKANAN_ID,CR_SPPBJ_JAMINAN_MAKSIMAL_PENYERAHAN)
          VALUES (
              ".$this->getField("CONTRACTINGREKANANPROSES1ID").",
              ".$this->getField("PAKET_ID").",
              ".$this->getField("CONTRACTINGREKANANID").",
              '".$this->getField("CR_SPPBJ_CODE")."',
              ".$this->getField("CR_SPPBJ_TANGGAL").",
              '".$this->getField("CR_SPPBJ_DIRUT")."',
              '".$this->getField("CR_SPPBJ_DIRUT_ALAMAT")."',
              '".$this->getField("CR_SPPBJ_DIRUT_KOTA")."',
              '".$this->getField("CR_SPPBJ_DIRUT_JABATAN")."',
              '".$this->getField("CR_SPPBJ_JAMINAN_PELAKSANA")."',
              ".$this->getField("CR_SPPBJ_JAMINAN_BESAR").",
              ".$this->getField("CR_SPPBJ_JAMINAN_JANGKA_DARI").",
              ".$this->getField("CR_SPPBJ_JAMINAN_JANGKA_SAMPAI").",
              ".$this->getField("CR_SPPBJ_JAMINAN_NILAI").",
              '".$this->getField("CR_SPPBJ_PEJABAT_BERWENANG")."',
              '".$this->getField("CR_SPPBJ_NIP")."',
              '".$this->getField("CR_SPPBJ_JABATAN")."',
              '".$this->getField("CR_SPPBJ_PPN")."',
              ".$this->getField("CR_SPPBJ_PELAKSANAAN_DARI").",
              ".$this->getField("CR_SPPBJ_PELAKSANAAN_SAMPAI").",
              ".$this->getField("CR_SPPBJ_CREATED_BY").",
              CURRENT_TIMESTAMP,
              ".$this->getField("CR_SPPBJ_NILAI").",
              ".$this->getField("STATUS").",
              ".$this->getField("CR_JENIS_PENGADAAN").",
              ".$this->getField("REKANAN_ID").",
              ".$this->getField("CR_SPPBJ_JAMINAN_MAKSIMAL_PENYERAHAN")."
          )";
          // echo $str2; die();
      $this->query = $str2;
      if ($this->execQuery($str2)) {
        $this->CONTRACTINGREKANANID = $this->getField("CONTRACTINGREKANANID");
        return TRUE;
      } else {
        $strDel = "DELETE FROM CONTRACTING_REKANAN WHERE CONTRACTINGREKANANID = ".$this->getField("CONTRACTINGREKANANID")."";
        $this->execQuery($strDel);
        return FALSE;
      }
    } else {
      return FALSE;
    }
  }

  function insertContractingNonSPPBJ() // Done
  {
    $this->setField("CONTRACTINGREKANANID", $this->getNextId("CONTRACTINGREKANANID","CONTRACTING_REKANAN"));
 
    $str = "
    INSERT INTO CONTRACTING_REKANAN (
               CONTRACTINGREKANANID, PAKET_ID, NAMA, URAIAN, NILAI, CONTRACTINGPROSESID, REKANAN_ID, JNS_KONTRAK, CREATED_BY, CREATED_DATE)
        VALUES (
            ".$this->getField("CONTRACTINGREKANANID").",
            ".$this->getField("PAKET_ID").",
            '".$this->getField("NAMA")."',
            '".$this->getField("URAIAN")."',
            ".$this->getField("NILAI").",
            '".$this->getField("CONTRACTINGPROSESID")."',
            ".$this->getField("REKANAN_ID").",
            '".$this->getField("JNS_KONTRAK")."',
            ".$this->getField("CREATED_BY").",
            CURRENT_TIMESTAMP
        )";
        // echo $str; die();
    $this->query = $str;
    if ($this->execQuery($str)) {

      $this->setField("CONTRACTINGREKANANPROSES1ID", $this->getNextId("CONTRACTINGREKANANPROSES1ID","CONTRACTING_REKANAN_PROSES1"));

      $str2 = "
      INSERT INTO CONTRACTING_REKANAN_PROSES1 (
                 CONTRACTINGREKANANPROSES1ID, PAKET_ID,CONTRACTINGREKANANID,CONTRACTINGSTATUSKONTRAKID,CR_JENIS_PENGADAAN,REKANAN_ID)
          VALUES (
              ".$this->getField("CONTRACTINGREKANANPROSES1ID").",
              ".$this->getField("PAKET_ID").",
              ".$this->getField("CONTRACTINGREKANANID").",
              ".$this->getField("STATUS").",
              ".$this->getField("CR_JENIS_PENGADAAN").",
              ".$this->getField("REKANAN_ID")."
          )";
          // echo $str2; die();
      $this->query = $str2;
      if ($this->execQuery($str2)) {
        $this->CONTRACTINGREKANANID = $this->getField("CONTRACTINGREKANANID");
        return TRUE;
      } else {
        $strDel = "DELETE FROM CONTRACTING_REKANAN WHERE CONTRACTINGREKANANID = ".$this->getField("CONTRACTINGREKANANID")."";
        $this->execQuery($strDel);
        return FALSE;
      }
    } else {
      return FALSE;
    }
  }

  function insertContractingSPPBJMulti() // Done
  {

    $this->setField("CONTRACTINGREKANANPROSES1ID", $this->getNextId("CONTRACTINGREKANANPROSES1ID","CONTRACTING_REKANAN_PROSES1"));

    $str2 = "
    INSERT INTO CONTRACTING_REKANAN_PROSES1 (
               CONTRACTINGREKANANPROSES1ID, PAKET_ID,CONTRACTINGREKANANID,CR_SPPBJ_CODE,CR_SPPBJ_TANGGAL,CR_SPPBJ_DIRUT,CR_SPPBJ_DIRUT_ALAMAT,CR_SPPBJ_DIRUT_KOTA,CR_SPPBJ_DIRUT_JABATAN,CR_SPPBJ_JAMINAN_PELAKSANA,CR_SPPBJ_JAMINAN_BESAR,CR_SPPBJ_JAMINAN_JANGKA_DARI,CR_SPPBJ_JAMINAN_JANGKA_SAMPAI,CR_SPPBJ_JAMINAN_NILAI,CR_SPPBJ_PEJABAT_BERWENANG,CR_SPPBJ_NIP,CR_SPPBJ_JABATAN,CR_SPPBJ_PPN,CR_SPPBJ_PELAKSANAAN_DARI,CR_SPPBJ_PELAKSANAAN_SAMPAI,CR_SPPBJ_CREATED_BY,CR_SPPBJ_CREATED_DATE,CR_SPPBJ_NILAI,CONTRACTINGSTATUSKONTRAKID,CR_JENIS_PENGADAAN,REKANAN_ID)
        VALUES (
            ".$this->getField("CONTRACTINGREKANANPROSES1ID").",
            ".$this->getField("PAKET_ID").",
            ".$this->getField("CONTRACTINGREKANANID").",
            '".$this->getField("CR_SPPBJ_CODE")."',
            ".$this->getField("CR_SPPBJ_TANGGAL").",
            '".$this->getField("CR_SPPBJ_DIRUT")."',
            '".$this->getField("CR_SPPBJ_DIRUT_ALAMAT")."',
            '".$this->getField("CR_SPPBJ_DIRUT_KOTA")."',
            '".$this->getField("CR_SPPBJ_DIRUT_JABATAN")."',
            '".$this->getField("CR_SPPBJ_JAMINAN_PELAKSANA")."',
            ".$this->getField("CR_SPPBJ_JAMINAN_BESAR").",
            ".$this->getField("CR_SPPBJ_JAMINAN_JANGKA_DARI").",
            ".$this->getField("CR_SPPBJ_JAMINAN_JANGKA_SAMPAI").",
            ".$this->getField("CR_SPPBJ_JAMINAN_NILAI").",
            '".$this->getField("CR_SPPBJ_PEJABAT_BERWENANG")."',
            '".$this->getField("CR_SPPBJ_NIP")."',
            '".$this->getField("CR_SPPBJ_JABATAN")."',
            '".$this->getField("CR_SPPBJ_PPN")."',
            ".$this->getField("CR_SPPBJ_PELAKSANAAN_DARI").",
            ".$this->getField("CR_SPPBJ_PELAKSANAAN_SAMPAI").",
            ".$this->getField("CR_SPPBJ_CREATED_BY").",
            CURRENT_TIMESTAMP,
            ".$this->getField("CR_SPPBJ_NILAI").",
            ".$this->getField("STATUS").",
            ".$this->getField("CR_JENIS_PENGADAAN").",
            ".$this->getField("REKANAN_ID")."
        )";
        // echo $str2; die();
    $this->query = $str2;
    return $this->execQuery($str2);
  }

  function insertSPMK() // Done
  {

    $this->setField("CONTRACTINGREKANANPROSES1SPMKID", $this->getNextId("CONTRACTINGREKANANPROSES1SPMKID","CONTRACTING_REKANAN_PROSES1_SPMK"));

    $str2 = "
    INSERT INTO CONTRACTING_REKANAN_PROSES1_SPMK (
               CONTRACTINGREKANANPROSES1SPMKID, CONTRACTINGREKANANID, NOMOR, SPMK_DARI, SPMK_SAMPAI, KETERANGAN, CREATED_BY, CREATED_DATE, REKANAN_ID)
        VALUES (
            ".$this->getField("CONTRACTINGREKANANPROSES1SPMKID").",
            ".$this->getField("CONTRACTINGREKANANID").",
            '".$this->getField("NOMOR")."',
            ".$this->getField("SPMK_DARI").",
            ".$this->getField("SPMK_SAMPAI").",
            '".$this->getField("KETERANGAN")."',
            ".$this->getField("CREATED_BY").",
            CURRENT_TIMESTAMP,
            ".$this->getField("REKANAN_ID")."
        )";
        // echo $str2; die();
    $this->query = $str2;
    return $this->execQuery($str2);
  }

  function updateSPMK() // Done
    {
      $str = "UPDATE CONTRACTING_REKANAN_PROSES1_SPMK SET
             NOMOR   = '".$this->getField("NOMOR")."', 
             SPMK_DARI   = ".$this->getField("SPMK_DARI").", 
             SPMK_SAMPAI   = ".$this->getField("SPMK_SAMPAI").", 
             KETERANGAN   = '".$this->getField("KETERANGAN")."', 
             UPDATED_BY = ".$this->getField("UPDATED_BY").",
             UPDATED_DATE = CURRENT_TIMESTAMP
          WHERE CONTRACTINGREKANANPROSES1SPMKID = ".$this->getField("CONTRACTINGREKANANPROSES1SPMKID")."
          ";
          // echo $str; die();
          $this->query = $str;
      return $this->execQuery($str);
    }

  function insertContractingSPK() // Done
  {
    /*Auto-generate primary key(s) by next max value (integer) */
    $this->setField("CONTRACTINGREKANANID", $this->getNextId("CONTRACTINGREKANANID","CONTRACTING_REKANAN"));

    $str = "
    INSERT INTO CONTRACTING_REKANAN (
               CONTRACTINGREKANANID, PAKET_ID, NAMA, URAIAN, NILAI, CONTRACTINGPROSESID, REKANAN_ID, JNS_KONTRAK, CREATED_BY, CREATED_DATE)
        VALUES (
            ".$this->getField("CONTRACTINGREKANANID").",
            ".$this->getField("PAKET_ID").",
            '".$this->getField("NAMA")."',
            '".$this->getField("URAIAN")."',
            ".$this->getField("NILAI").",
            '".$this->getField("CONTRACTINGPROSESID")."',
            ".$this->getField("REKANAN_ID").",
            '".$this->getField("JNS_KONTRAK")."',
            ".$this->getField("CREATED_BY").",
            CURRENT_TIMESTAMP
        )";
        // echo $str; die();
    $this->query = $str;
    if ($this->execQuery($str)) {

      $this->setField("CONTRACTINGREKANANPROSES1ID", $this->getNextId("CONTRACTINGREKANANPROSES1ID","CONTRACTING_REKANAN_PROSES1"));

      $str2 = "
      INSERT INTO CONTRACTING_REKANAN_PROSES1 (
                 CONTRACTINGREKANANPROSES1ID, PAKET_ID,CONTRACTINGREKANANID,CR_CODE,CR_NILAI_KONTRAK,CR_METODE_PEMBAYARAN,CR_JENIS_PENGADAAN,CR_JENIS_PEKERJAAN,CONTRACTINGJENISKONTRAKID,CR_WAKTU_PELAKSANAAN_DARI,CR_WAKTU_PELAKSANAAN_SAMPAI,CR_PIHAK1_NAMA,CR_PIHAK1_JABATAN,CR_PIHAK2_NAMA,CR_PIHAK2_JABATAN,CR_LINGKUP_PEKERJAAN,CONTRACTINGSTATUSKONTRAKID,CR_UPDATED_BY,CR_UPDATED_DATE)
          VALUES (
              ".$this->getField("CONTRACTINGREKANANPROSES1ID").",
              ".$this->getField("PAKET_ID").",
              ".$this->getField("CONTRACTINGREKANANID").",
              '".$this->getField("CR_CODE")."',
              ".$this->getField("CR_NILAI_KONTRAK").",
              '".$this->getField("CR_METODE_PEMBAYARAN")."',
              ".$this->getField("CR_JENIS_PENGADAAN").",
              ".$this->getField("CR_JENIS_PEKERJAAN").",
              ".$this->getField("CONTRACTINGJENISKONTRAKID").",
              ".$this->getField("CR_WAKTU_PELAKSANAAN_DARI").",
              ".$this->getField("CR_WAKTU_PELAKSANAAN_SAMPAI").",
              '".$this->getField("CR_PIHAK1_NAMA")."',
              '".$this->getField("CR_PIHAK1_JABATAN")."',
              '".$this->getField("CR_PIHAK2_NAMA")."',
              '".$this->getField("CR_PIHAK2_JABATAN")."',
              '".$this->getField("CR_LINGKUP_PEKERJAAN")."',
              ".$this->getField("CONTRACTINGSTATUSKONTRAKID").",
              ".$this->getField("CR_UPDATED_BY").",
              CURRENT_TIMESTAMP
          )";
          // echo $str2; die();
      $this->query = $str2;
      if ($this->execQuery($str2)) {
        return TRUE;
      } else {
        $strDel = "DELETE FROM CONTRACTING_REKANAN WHERE CONTRACTINGREKANANID = ".$this->getField("CONTRACTINGREKANANID")."";
        $this->execQuery($strDel);
        return FALSE;
      }
    } else {
      return FALSE;
    }
  }

    function canUpdate(){
      return true;
    }

    function updateProses1() // Done
    {
      $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
             CR_SPPBJ_CODE   = '".$this->getField("CR_SPPBJ_CODE")."',
             CR_SPPBJ_TANGGAL  = ".$this->getField("CR_SPPBJ_TANGGAL").",
             CR_SPPBJ_DIRUT = '".$this->getField("CR_SPPBJ_DIRUT")."',
             CR_SPPBJ_DIRUT_ALAMAT = '".$this->getField("CR_SPPBJ_DIRUT_ALAMAT")."',
             CR_SPPBJ_DIRUT_KOTA = '".$this->getField("CR_SPPBJ_DIRUT_KOTA")."',
             CR_SPPBJ_DIRUT_JABATAN    = '".$this->getField("CR_SPPBJ_DIRUT_JABATAN")."',
             CR_SPPBJ_JAMINAN_PELAKSANA = '".$this->getField("CR_SPPBJ_JAMINAN_PELAKSANA")."',
             CR_SPPBJ_JAMINAN_BESAR = ".$this->getField("CR_SPPBJ_JAMINAN_BESAR").",
             CR_SPPBJ_JAMINAN_JANGKA_DARI = ".$this->getField("CR_SPPBJ_JAMINAN_JANGKA_DARI").",
             CR_SPPBJ_JAMINAN_JANGKA_SAMPAI = ".$this->getField("CR_SPPBJ_JAMINAN_JANGKA_SAMPAI").",
             CR_SPPBJ_JAMINAN_NILAI = ".$this->getField("CR_SPPBJ_JAMINAN_NILAI").",
             CR_SPPBJ_PEJABAT_BERWENANG = '".$this->getField("CR_SPPBJ_PEJABAT_BERWENANG")."',
             CR_SPPBJ_NIP = '".$this->getField("CR_SPPBJ_NIP")."',
             CR_SPPBJ_JABATAN = '".$this->getField("CR_SPPBJ_JABATAN")."',
             CR_SPPBJ_PPN = '".$this->getField("CR_SPPBJ_PPN")."',
             CR_SPPBJ_PELAKSANAAN_DARI = ".$this->getField("CR_SPPBJ_PELAKSANAAN_DARI").",
             CR_SPPBJ_PELAKSANAAN_SAMPAI = ".$this->getField("CR_SPPBJ_PELAKSANAAN_SAMPAI").",
             CR_SPPBJ_JAMINAN_MAKSIMAL_PENYERAHAN = ".$this->getField("CR_SPPBJ_JAMINAN_MAKSIMAL_PENYERAHAN").",
             CR_SPPBJ_NILAI = ".$this->getField("CR_SPPBJ_NILAI").",
             CR_SPPBJ_UPDATED_BY = ".$this->getField("CR_SPPBJ_UPDATED_BY").",
             CR_SPPBJ_UPDATED_DATE = CURRENT_TIMESTAMP
          WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
          ";
          // echo $str; die();
          $this->query = $str;
      return $this->execQuery($str);
    }

    function updateStatus() //
    {

      switch ($this->getField("CONTRACTINGSTATUSKONTRAKID")) {
        case '1':
          $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
                 CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
                 CR_SPPBJ_APPROVED_DATE = CURRENT_TIMESTAMP,
                 UPDATED_BY   = ".$this->getField("CREATED_BY").",
                 UPDATED_DATE = CURRENT_TIMESTAMP
              WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
              ";
          break;
        case '2':
          $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
                 CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
                 CR_SPPBJ_APPROVED_DATE = CURRENT_TIMESTAMP
              WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
              ";
          break;
        case '31':
          $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
                 CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
                 CR_SPPBJ_APPROVED_DATE = CURRENT_TIMESTAMP
              WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
              ";
          break;
        case '4':
          $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
                 CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
                 UPDATED_BY   = ".$this->getField("CREATED_BY").",
                 UPDATED_DATE = CURRENT_TIMESTAMP
              WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
              ";
          break;
        case '5':
          $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
                 CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
                 CR_APPROVED_DATE = CURRENT_TIMESTAMP,
                 UPDATED_BY   = ".$this->getField("CREATED_BY").",
                 UPDATED_DATE = CURRENT_TIMESTAMP
              WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
              ";
          break;
        case '51': // kembalikan spk/pks
          $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
                 CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
                 CR_APPROVED_DATE = CURRENT_TIMESTAMP,
                 UPDATED_BY   = ".$this->getField("CREATED_BY").",
                 UPDATED_DATE = CURRENT_TIMESTAMP
              WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
              ";
          break;
        case '6':
          $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
                 CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
                 UPDATED_BY   = ".$this->getField("CREATED_BY").",
                 UPDATED_DATE = CURRENT_TIMESTAMP
              WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
              ";

          $str3 = "UPDATE CONTRACTING_REKANAN SET
                 CONTRACTINGPROSESID   = 3,
                 UPDATED_BY   = '".$this->getField("CREATED_BY")."',
                 UPDATED_DATE = CURRENT_TIMESTAMP
              WHERE CONTRACTINGREKANANID = ".$this->getField("CONTRACTINGREKANANID")."
              ";

          $this->query = $str3;
          $this->execQuery($str3);
          break;
        case '7':
        case '8':
        case '9':
        case '10':
        case '11':
        case '12':
        case '13':
          $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
                 CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
                 UPDATED_BY   = ".$this->getField("CREATED_BY").",
                 UPDATED_DATE = CURRENT_TIMESTAMP
              WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
              ";
          break;

        case '100':
          $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
                 CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
                 UPDATED_BY   = ".$this->getField("CREATED_BY").",
                 UPDATED_DATE = CURRENT_TIMESTAMP
              WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
              ";

          $str3 = "UPDATE CONTRACTING_REKANAN SET
                 CONTRACTINGPROSESID   = 6,
                 UPDATED_BY   = '".$this->getField("CREATED_BY")."',
                 UPDATED_DATE = CURRENT_TIMESTAMP
              WHERE CONTRACTINGREKANANID = ".$this->getField("CONTRACTINGREKANANID")."
              ";

          $this->query = $str3;
          $this->execQuery($str3);
          break;

        case '200':
          $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
                 CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
                 UPDATED_BY   = ".$this->getField("CREATED_BY").",
                 UPDATED_DATE = CURRENT_TIMESTAMP
              WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
              ";

          $str3 = "UPDATE CONTRACTING_REKANAN SET
                 CONTRACTINGPROSESID   = 6,
                 UPDATED_BY   = '".$this->getField("CREATED_BY")."',
                 UPDATED_DATE = CURRENT_TIMESTAMP
              WHERE CONTRACTINGREKANANID = ".$this->getField("CONTRACTINGREKANANID")."
              ";

          $this->query = $str3;
          $this->execQuery($str3);
          break;

        // Custom UI
        case '110':
        case '111':
        case '112':
        case '113':
        case '114':
        case '115':
        case '116':
          $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
                 CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
                 UPDATED_BY   = ".$this->getField("CREATED_BY").",
                 UPDATED_DATE = CURRENT_TIMESTAMP
              WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
              "; 
          break;

        default:
          break;
      }

          // echo $str; die();
          $this->query = $str;
      return $this->execQuery($str);
    }

    function updateStatusMulti() //
    {

      switch ($this->getField("CONTRACTINGSTATUSKONTRAKID")) {
        case '1': // Done
          $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
                  CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
                  CR_SPPBJ_APPROVED_DATE = CURRENT_TIMESTAMP,
                  UPDATED_BY   = ".$this->getField("CREATED_BY").",
                  UPDATED_DATE = CURRENT_TIMESTAMP
                  WHERE CONTRACTINGREKANANID = ".$this->getField("CONTRACTINGREKANANID")."
                  ";
          break;
        case '2': // Done
          $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
                 CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
                 CR_SPPBJ_APPROVED_DATE = CURRENT_TIMESTAMP
              WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
              ";
          break;

        case '4': // Done
        case '31':
          $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
                 CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
                 UPDATED_BY   = ".$this->getField("CREATED_BY").",
                 UPDATED_DATE = CURRENT_TIMESTAMP
              WHERE CONTRACTINGREKANANID = ".$this->getField("CONTRACTINGREKANANID")."
              ";
          break;
        case '5': // Done
          $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
                 CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
                 CR_APPROVED_DATE = CURRENT_TIMESTAMP,
                 UPDATED_BY   = ".$this->getField("CREATED_BY").",
                 UPDATED_DATE = CURRENT_TIMESTAMP
              WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
              ";
          break;
        case '51': // kembalikan spk/pks
          // $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
          //        CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
          //        CR_APPROVED_DATE = CURRENT_TIMESTAMP,
          //        UPDATED_BY   = ".$this->getField("CREATED_BY").",
          //        UPDATED_DATE = CURRENT_TIMESTAMP
          //     WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
          //     ";
          break;
        case '6': // Done
          $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
                 CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
                 UPDATED_BY   = ".$this->getField("CREATED_BY").",
                 UPDATED_DATE = CURRENT_TIMESTAMP
              WHERE CONTRACTINGREKANANID = ".$this->getField("CONTRACTINGREKANANID")."
              ";

          $str3 = "UPDATE CONTRACTING_REKANAN SET
                 CONTRACTINGPROSESID   = 3,
                 UPDATED_BY   = '".$this->getField("CREATED_BY")."',
                 UPDATED_DATE = CURRENT_TIMESTAMP
              WHERE CONTRACTINGREKANANID = ".$this->getField("CONTRACTINGREKANANID")."
              ";

          $this->query = $str3;
          $this->execQuery($str3);
          break;
        case '7':
        case '8':
        case '9':
        case '10':
        case '11':
        case '12':
        case '13':
          // $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
          //        CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
          //        UPDATED_BY   = ".$this->getField("CREATED_BY").",
          //        UPDATED_DATE = CURRENT_TIMESTAMP
          //     WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
          //     ";
          break;

        case '100':
          // $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
          //        CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
          //        UPDATED_BY   = ".$this->getField("CREATED_BY").",
          //        UPDATED_DATE = CURRENT_TIMESTAMP
          //     WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
          //     ";

          // $str3 = "UPDATE CONTRACTING_REKANAN SET
          //        CONTRACTINGPROSESID   = 5,
          //        UPDATED_BY   = '".$this->getField("CREATED_BY")."',
          //        UPDATED_DATE = CURRENT_TIMESTAMP
          //     WHERE CONTRACTINGREKANANID = ".$this->getField("CONTRACTINGREKANANID")."
          //     ";

          // $this->query = $str3;
          // $this->execQuery($str3);
          break;

        case '200':
          // $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
          //        CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
          //        UPDATED_BY   = ".$this->getField("CREATED_BY").",
          //        UPDATED_DATE = CURRENT_TIMESTAMP
          //     WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
          //     ";

          // $str3 = "UPDATE CONTRACTING_REKANAN SET
          //        CONTRACTINGPROSESID   = 6,
          //        UPDATED_BY   = '".$this->getField("CREATED_BY")."',
          //        UPDATED_DATE = CURRENT_TIMESTAMP
          //     WHERE CONTRACTINGREKANANID = ".$this->getField("CONTRACTINGREKANANID")."
          //     ";

          // $this->query = $str3;
          // $this->execQuery($str3);
          break;

        default:
          break;
      }



      // echo $str; die();
      $this->query = $str;
      return $this->execQuery($str);
    }

    function updateProses1Kontrak()
    {
             // CR_JENIS_PEKERJAAN = ".$this->getField("CR_JENIS_PEKERJAAN").",
      $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
             CR_CODE   = '".$this->getField("CR_CODE")."',
             CR_NILAI_KONTRAK  = ".$this->getField("CR_NILAI_KONTRAK").",
             CR_METODE_PEMBAYARAN = '".$this->getField("CR_METODE_PEMBAYARAN")."',
             CR_JENIS_PENGADAAN = ".$this->getField("CR_JENIS_PENGADAAN").",
             CONTRACTINGJENISKONTRAKID = ".$this->getField("CONTRACTINGJENISKONTRAKID").",
             CR_WAKTU_PELAKSANAAN_DARI = ".$this->getField("CR_WAKTU_PELAKSANAAN_DARI").",
             CR_WAKTU_PELAKSANAAN_SAMPAI = ".$this->getField("CR_WAKTU_PELAKSANAAN_SAMPAI").",
             CR_PIHAK1_NAMA = '".$this->getField("CR_PIHAK1_NAMA")."',
             CR_PIHAK1_JABATAN = '".$this->getField("CR_PIHAK1_JABATAN")."',
             CR_PIHAK2_NAMA = '".$this->getField("CR_PIHAK2_NAMA")."',
             CR_PIHAK2_JABATAN = '".$this->getField("CR_PIHAK2_JABATAN")."',
             CR_LINGKUP_PEKERJAAN = '".$this->getField("CR_LINGKUP_PEKERJAAN")."',
             CONTRACTINGSTATUSKONTRAKID = ".$this->getField("CONTRACTINGSTATUSKONTRAKID").",
             CR_UPDATED_BY = ".$this->getField("CR_UPDATED_BY").",
             CR_UPDATED_DATE = CURRENT_TIMESTAMP,
             CR_LEGAL_NOMOR_PKS   = '".$this->getField("CR_LEGAL_NOMOR_PKS")."',
             CR_LEGAL_TANGGAL = ".$this->getField("CR_LEGAL_TANGGAL").",
             CR_LEGAL_NOMOR_REKANAN   = '".$this->getField("CR_LEGAL_NOMOR_REKANAN")."',
             CR_LEGAL_UPDATED_BY = ".$this->getField("CR_UPDATED_BY").",
             CR_LEGAL_UPDATED_DATE = CURRENT_TIMESTAMP,
             CR_PO = '".$this->getField("CR_PO")."',
             CR_TGL_HASIL_TERIMA_PEMILIHAN = ".$this->getField("CR_TGL_HASIL_TERIMA_PEMILIHAN").",
             CR_PENYELESAIAN_KONTRAK_AWAL = ".$this->getField("CR_PENYELESAIAN_KONTRAK_AWAL").",
             CR_PENYELESAIAN_KONTRAK_AKHIR = ".$this->getField("CR_PENYELESAIAN_KONTRAK_AKHIR").",
             CR_MASA_GARANSI = '".$this->getField("CR_MASA_GARANSI")."',
             CR_MASA_GARANSI_PERIODE = ".$this->getField("CR_MASA_GARANSI_PERIODE").",
             CR_NAMA_KEGIATAN = ".$this->getField("CR_NAMA_KEGIATAN")."
            
          WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
          ";
          // echo $str; die();
          $this->query = $str;
      return $this->execQuery($str);
    }

    function updateProses1Legal()
    {
      $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
             CR_LEGAL_NOMOR_PKS   = '".$this->getField("CR_LEGAL_NOMOR_PKS")."',
             CR_LEGAL_TANGGAL = ".$this->getField("CR_LEGAL_TANGGAL").",
             CR_LEGAL_NOMOR_REKANAN   = '".$this->getField("CR_LEGAL_NOMOR_REKANAN")."',
             CR_LEGAL_TANGGAL_REKANAN = ".$this->getField("CR_LEGAL_TANGGAL_REKANAN").",
             CR_LEGAL_UPDATED_BY = ".$this->getField("CR_LEGAL_UPDATED_BY").",
             CR_LEGAL_UPDATED_DATE = CURRENT_TIMESTAMP
          WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
          ";
          // echo $str; die();
          $this->query = $str;
      return $this->execQuery($str);
    }

  // Update Status Proses
  public function FunctionName($value='')
  {
    $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
            CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."'
            WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
            ";
    $this->query = $str;
    return $this->execQuery($str);
  }

  // Perubahan Kontrak
  function insertProses4Perubahan()
  {
    $this->setField("CONTRACTINGREKANANPROSES4ID", $this->getNextId("CONTRACTINGREKANANPROSES4ID","CONTRACTING_REKANAN_PROSES4"));

    $str = "
    INSERT INTO CONTRACTING_REKANAN_PROSES4 (
           CONTRACTINGREKANANPROSES4ID, PAKET_ID, CONTRACTINGREKANANID, CR_PERUBAHAN, CR_PERUBAHAN_ALASAN, CR_PERUBAHAN_UPDATED_BY, CR_PERUBAHAN_UPDATED_DATE,CR_CREATED_BY, CR_CREATED_DATE)
    VALUES (
        ".$this->getField("CONTRACTINGREKANANPROSES4ID").",
        ".$this->getField("PAKET_ID").",
        ".$this->getField("CONTRACTINGREKANANID").",
        '".$this->getField("CR_PERUBAHAN")."',
        '".$this->getField("CR_PERUBAHAN_ALASAN")."',
        ".$this->getField("CR_PERUBAHAN_UPDATED_BY").",
        CURRENT_TIMESTAMP,
        ".$this->getField("CR_PERUBAHAN_UPDATED_BY").",
        CURRENT_TIMESTAMP
    )";
        // echo $str; die();
        $this->query = $str;
    return $this->execQuery($str);
  }

  function updateProses4Perubahan()
  {
    $str = "UPDATE CONTRACTING_REKANAN_PROSES4 SET
           CR_PERUBAHAN   = '".$this->getField("CR_PERUBAHAN")."',
           CR_PERUBAHAN_ALASAN  = '".$this->getField("CR_PERUBAHAN_ALASAN")."',
           CR_PERUBAHAN_UPDATED_BY = ".$this->getField("CR_PERUBAHAN_UPDATED_BY").",
           CR_PERUBAHAN_UPDATED_DATE = CURRENT_TIMESTAMP
        WHERE CONTRACTINGREKANANPROSES4ID = ".$this->getField("CONTRACTINGREKANANPROSES4ID")."
        ";
        // echo $str; die();
        $this->query = $str;
    return $this->execQuery($str);
  }

  // Penyesuaian Harga

  function insertProses4Penyesuaian()
  {
    $this->setField("CONTRACTINGREKANANPROSES4ID", $this->getNextId("CONTRACTINGREKANANPROSES4ID","CONTRACTING_REKANAN_PROSES4"));

    $str = "
    INSERT INTO CONTRACTING_REKANAN_PROSES4 (
           CONTRACTINGREKANANPROSES4ID, PAKET_ID, CONTRACTINGREKANANID, CR_PENYESUAIAN, CR_PENYESUAIAN_ALASAN, CR_PENYESUAIAN_UPDATED_BY, CR_PENYESUAIAN_UPDATED_DATE,CR_CREATED_BY, CR_CREATED_DATE)
    VALUES (
        ".$this->getField("CONTRACTINGREKANANPROSES4ID").",
        ".$this->getField("PAKET_ID").",
        ".$this->getField("CONTRACTINGREKANANID").",
        '".$this->getField("CR_PENYESUAIAN")."',
        '".$this->getField("CR_PENYESUAIAN_ALASAN")."',
        ".$this->getField("CR_PENYESUAIAN_UPDATED_BY").",
        CURRENT_TIMESTAMP,
        ".$this->getField("CR_PENYESUAIAN_UPDATED_BY").",
        CURRENT_TIMESTAMP
    )";
        // echo $str; die();
        $this->query = $str;
    return $this->execQuery($str);
  }

  function updateProses4Penyesuaian()
  {
    $str = "UPDATE CONTRACTING_REKANAN_PROSES4 SET
           CR_PENYESUAIAN   = '".$this->getField("CR_PENYESUAIAN")."',
           CR_PENYESUAIAN_ALASAN  = '".$this->getField("CR_PENYESUAIAN_ALASAN")."',
           CR_PENYESUAIAN_UPDATED_BY = ".$this->getField("CR_PENYESUAIAN_UPDATED_BY").",
           CR_PENYESUAIAN_UPDATED_DATE = CURRENT_TIMESTAMP
        WHERE CONTRACTINGREKANANPROSES4ID = ".$this->getField("CONTRACTINGREKANANPROSES4ID")."
        ";
        // echo $str; die();
        $this->query = $str;
    return $this->execQuery($str);
  }

  // Keadaan Kahar

  function insertProses4Kahar()
  {
    $this->setField("CONTRACTINGREKANANPROSES4ID", $this->getNextId("CONTRACTINGREKANANPROSES4ID","CONTRACTING_REKANAN_PROSES4"));

    $str = "
    INSERT INTO CONTRACTING_REKANAN_PROSES4 (
           CONTRACTINGREKANANPROSES4ID, PAKET_ID, CONTRACTINGREKANANID, CR_KAHAR, CR_KAHAR_ALASAN, CR_KAHAR_UPDATED_BY, CR_KAHAR_UPDATED_DATE,CR_CREATED_BY, CR_CREATED_DATE)
    VALUES (
        ".$this->getField("CONTRACTINGREKANANPROSES4ID").",
        ".$this->getField("PAKET_ID").",
        ".$this->getField("CONTRACTINGREKANANID").",
        '".$this->getField("CR_KAHAR")."',
        '".$this->getField("CR_KAHAR_ALASAN")."',
        ".$this->getField("CR_KAHAR_UPDATED_BY").",
        CURRENT_TIMESTAMP,
        ".$this->getField("CR_KAHAR_UPDATED_BY").",
        CURRENT_TIMESTAMP
    )";
        // echo $str; die();
        $this->query = $str;
    return $this->execQuery($str);
  }

  function updateProses4Kahar()
  {
    $str = "UPDATE CONTRACTING_REKANAN_PROSES4 SET
           CR_KAHAR   = '".$this->getField("CR_KAHAR")."',
           CR_KAHAR_ALASAN  = '".$this->getField("CR_KAHAR_ALASAN")."',
           CR_KAHAR_UPDATED_BY = ".$this->getField("CR_KAHAR_UPDATED_BY").",
           CR_KAHAR_UPDATED_DATE = CURRENT_TIMESTAMP
        WHERE CONTRACTINGREKANANPROSES4ID = ".$this->getField("CONTRACTINGREKANANPROSES4ID")."
        ";
        // echo $str; die();
        $this->query = $str;
    return $this->execQuery($str);
  }

  // Berakhir Kontrak

  function insertProses4Berakhir()
  {
    $this->setField("CONTRACTINGREKANANPROSES4ID", $this->getNextId("CONTRACTINGREKANANPROSES4ID","CONTRACTING_REKANAN_PROSES4"));

    $str = "
    INSERT INTO CONTRACTING_REKANAN_PROSES4 (
           CONTRACTINGREKANANPROSES4ID, PAKET_ID, CONTRACTINGREKANANID, CR_BERAKHIR, CR_BERAKHIR_ALASAN, CR_BERAKHIR_UPDATED_BY, CR_BERAKHIR_UPDATED_DATE,CR_CREATED_BY, CR_CREATED_DATE)
    VALUES (
        ".$this->getField("CONTRACTINGREKANANPROSES4ID").",
        ".$this->getField("PAKET_ID").",
        ".$this->getField("CONTRACTINGREKANANID").",
        '".$this->getField("CR_BERAKHIR")."',
        '".$this->getField("CR_BERAKHIR_ALASAN")."',
        ".$this->getField("CR_BERAKHIR_UPDATED_BY").",
        CURRENT_TIMESTAMP,
        ".$this->getField("CR_BERAKHIR_UPDATED_BY").",
        CURRENT_TIMESTAMP
    )";
        // echo $str; die();
        $this->query = $str;
    return $this->execQuery($str);
  }

  function updateProses4Berakhir()
  {
    $str = "UPDATE CONTRACTING_REKANAN_PROSES4 SET
           CR_BERAKHIR   = '".$this->getField("CR_BERAKHIR")."',
           CR_BERAKHIR_ALASAN  = '".$this->getField("CR_BERAKHIR_ALASAN")."',
           CR_BERAKHIR_UPDATED_BY = ".$this->getField("CR_BERAKHIR_UPDATED_BY").",
           CR_BERAKHIR_UPDATED_DATE = CURRENT_TIMESTAMP
        WHERE CONTRACTINGREKANANPROSES4ID = ".$this->getField("CONTRACTINGREKANANPROSES4ID")."
        ";
        // echo $str; die();
        $this->query = $str;
    return $this->execQuery($str);
  }

  // Pemutusan Kontrak

  function insertProses4Pemutusan()
  {
    $this->setField("CONTRACTINGREKANANPROSES4ID", $this->getNextId("CONTRACTINGREKANANPROSES4ID","CONTRACTING_REKANAN_PROSES4"));

    $str = "
    INSERT INTO CONTRACTING_REKANAN_PROSES4 (
           CONTRACTINGREKANANPROSES4ID, PAKET_ID, CONTRACTINGREKANANID, CR_PEMUTUSAN, CR_PEMUTUSAN_ALASAN, CR_PEMUTUSAN_UPDATED_BY, CR_PEMUTUSAN_UPDATED_DATE,CR_PEMUTUSAN_FILE, CR_CREATED_BY, CR_CREATED_DATE)
    VALUES (
        ".$this->getField("CONTRACTINGREKANANPROSES4ID").",
        ".$this->getField("PAKET_ID").",
        ".$this->getField("CONTRACTINGREKANANID").",
        '".$this->getField("CR_PEMUTUSAN")."',
        '".$this->getField("CR_PEMUTUSAN_ALASAN")."',
        ".$this->getField("CR_PEMUTUSAN_UPDATED_BY").",
        CURRENT_TIMESTAMP,
        '".$this->getField("CR_PEMUTUSAN_FILE")."',
        ".$this->getField("CR_PEMUTUSAN_UPDATED_BY").",
        CURRENT_TIMESTAMP
    )";
        // echo $str; die();
        $this->query = $str;
    return $this->execQuery($str);
  }

  function updateProses4Pemutusan()
  {
    $str = "UPDATE CONTRACTING_REKANAN_PROSES4 SET
           CR_PEMUTUSAN   = '".$this->getField("CR_PEMUTUSAN")."',
           CR_PEMUTUSAN_ALASAN  = '".$this->getField("CR_PEMUTUSAN_ALASAN")."',
           CR_PEMUTUSAN_FILE  = '".$this->getField("CR_PEMUTUSAN_FILE")."',
           CR_PEMUTUSAN_UPDATED_BY = ".$this->getField("CR_PEMUTUSAN_UPDATED_BY").",
           CR_PEMUTUSAN_UPDATED_DATE = CURRENT_TIMESTAMP
        WHERE CONTRACTINGREKANANPROSES4ID = ".$this->getField("CONTRACTINGREKANANPROSES4ID")."
        ";
        // echo $str; die();
        $this->query = $str;
    return $this->execQuery($str);
  }

  // Pemberian Kesempatan

  function insertProses4Kesempatan()
  {
    $this->setField("CONTRACTINGREKANANPROSES4ID", $this->getNextId("CONTRACTINGREKANANPROSES4ID","CONTRACTING_REKANAN_PROSES4"));

    $str = "
    INSERT INTO CONTRACTING_REKANAN_PROSES4 (
           CONTRACTINGREKANANPROSES4ID, PAKET_ID, CONTRACTINGREKANANID, CR_KESEMPATAN, CR_KESEMPATAN_ALASAN, CR_KESEMPATAN_UPDATED_BY, CR_KESEMPATAN_UPDATED_DATE,CR_CREATED_BY, CR_CREATED_DATE)
    VALUES (
        ".$this->getField("CONTRACTINGREKANANPROSES4ID").",
        ".$this->getField("PAKET_ID").",
        ".$this->getField("CONTRACTINGREKANANID").",
        '".$this->getField("CR_KESEMPATAN")."',
        '".$this->getField("CR_KESEMPATAN_ALASAN")."',
        ".$this->getField("CR_KESEMPATAN_UPDATED_BY").",
        CURRENT_TIMESTAMP,
        ".$this->getField("CR_KESEMPATAN_UPDATED_BY").",
        CURRENT_TIMESTAMP
    )";
        // echo $str; die();
        $this->query = $str;
    return $this->execQuery($str);
  }

  function updateProses4Kesempatan()
  {
    $str = "UPDATE CONTRACTING_REKANAN_PROSES4 SET
           CR_KESEMPATAN   = '".$this->getField("CR_KESEMPATAN")."',
           CR_KESEMPATAN_ALASAN  = '".$this->getField("CR_KESEMPATAN_ALASAN")."',
           CR_KESEMPATAN_UPDATED_BY = ".$this->getField("CR_KESEMPATAN_UPDATED_BY").",
           CR_KESEMPATAN_UPDATED_DATE = CURRENT_TIMESTAMP
        WHERE CONTRACTINGREKANANPROSES4ID = ".$this->getField("CONTRACTINGREKANANPROSES4ID")."
        ";
        // echo $str; die();
        $this->query = $str;
    return $this->execQuery($str);
  }

  // Denda dan Ganti rugi

  function insertProses4Denda()
  {
    $this->setField("CONTRACTINGREKANANPROSES4ID", $this->getNextId("CONTRACTINGREKANANPROSES4ID","CONTRACTING_REKANAN_PROSES4"));

    $str = "
    INSERT INTO CONTRACTING_REKANAN_PROSES4 (
           CONTRACTINGREKANANPROSES4ID, PAKET_ID, CONTRACTINGREKANANID, CR_DENDA, CR_DENDA_ALASAN, CR_DENDA_FILE, CR_DENDA_UPDATED_BY, CR_DENDA_UPDATED_DATE,CR_CREATED_BY, CR_CREATED_DATE)
    VALUES (
        ".$this->getField("CONTRACTINGREKANANPROSES4ID").",
        ".$this->getField("PAKET_ID").",
        ".$this->getField("CONTRACTINGREKANANID").",
        '".$this->getField("CR_DENDA")."',
        '".$this->getField("CR_DENDA_ALASAN")."',
        '".$this->getField("CR_DENDA_FILE")."',
        ".$this->getField("CR_DENDA_UPDATED_BY").",
        CURRENT_TIMESTAMP,
        ".$this->getField("CR_DENDA_UPDATED_BY").",
        CURRENT_TIMESTAMP
    )";
        // echo $str; die();
        $this->query = $str;
    return $this->execQuery($str);
  }

  function updateProses4Denda()
  {
    $str = "UPDATE CONTRACTING_REKANAN_PROSES4 SET
           CR_DENDA   = '".$this->getField("CR_DENDA")."',
           CR_DENDA_ALASAN  = '".$this->getField("CR_DENDA_ALASAN")."',
           CR_DENDA_FILE  = '".$this->getField("CR_DENDA_FILE")."',
           CR_DENDA_UPDATED_BY = ".$this->getField("CR_DENDA_UPDATED_BY").",
           CR_DENDA_UPDATED_DATE = CURRENT_TIMESTAMP
        WHERE CONTRACTINGREKANANPROSES4ID = ".$this->getField("CONTRACTINGREKANANPROSES4ID")."
        ";
        // echo $str; die();
        $this->query = $str;
    return $this->execQuery($str);
  }

  // BAST Hasil Pekerjaan
  function insertProses5Hasil()
  {
    $this->setField("CONTRACTINGREKANANPROSES5ID", $this->getNextId("CONTRACTINGREKANANPROSES5ID","CONTRACTING_REKANAN_PROSES5"));

    $str = "
    INSERT INTO CONTRACTING_REKANAN_PROSES5 (
           CONTRACTINGREKANANPROSES5ID, PAKET_ID, CONTRACTINGREKANANID, CR_BAST_PEKERJAAN_NOMOR, CR_BAST_PEKERJAAN_TANGGAL, CR_BAST_PEKERJAAN_NAMA_PENYEDIA, CR_BAST_PEKERJAAN_JABATAN_PENYEDIA, CR_BAST_PEKERJAAN_NAMA_PENERIMA, CR_BAST_PEKERJAAN_JABATAN_PENERIMA, CR_BAST_PEKERJAAN_STATUS, CR_BAST_PEKERJAAN_UPDATED_BY, CR_BAST_PEKERJAAN_UPDATED_DATE, CREATED_BY, CREATED_DATE, REKANAN_ID)
    VALUES (
        ".$this->getField("CONTRACTINGREKANANPROSES5ID").",
        ".$this->getField("PAKET_ID").",
        ".$this->getField("CONTRACTINGREKANANID").",
        '".$this->getField("CR_BAST_PEKERJAAN_NOMOR")."',
        ".$this->getField("CR_BAST_PEKERJAAN_TANGGAL").",
        '".$this->getField("CR_BAST_PEKERJAAN_NAMA_PENYEDIA")."',
        '".$this->getField("CR_BAST_PEKERJAAN_JABATAN_PENYEDIA")."',
        '".$this->getField("CR_BAST_PEKERJAAN_NAMA_PENERIMA")."',
        '".$this->getField("CR_BAST_PEKERJAAN_JABATAN_PENERIMA")."',
        '".$this->getField("CR_BAST_PEKERJAAN_STATUS")."',
        ".$this->getField("CREATED_BY").",
        CURRENT_TIMESTAMP,
        ".$this->getField("CREATED_BY").",
        CURRENT_TIMESTAMP,
        ".$this->getField("REKANAN_ID")."
    )";
        // echo $str; die();
        $this->query = $str;
    return $this->execQuery($str);
  }

  function updateProses5Hasil()
  {
    $str = "UPDATE CONTRACTING_REKANAN_PROSES5 SET
           CR_BAST_PEKERJAAN_NOMOR   = '".$this->getField("CR_BAST_PEKERJAAN_NOMOR")."',
           CR_BAST_PEKERJAAN_TANGGAL  = ".$this->getField("CR_BAST_PEKERJAAN_TANGGAL").",
           CR_BAST_PEKERJAAN_NAMA_PENYEDIA   = '".$this->getField("CR_BAST_PEKERJAAN_NAMA_PENYEDIA")."',
           CR_BAST_PEKERJAAN_JABATAN_PENYEDIA   = '".$this->getField("CR_BAST_PEKERJAAN_JABATAN_PENYEDIA")."',
           CR_BAST_PEKERJAAN_NAMA_PENERIMA   = '".$this->getField("CR_BAST_PEKERJAAN_NAMA_PENERIMA")."',
           CR_BAST_PEKERJAAN_JABATAN_PENERIMA   = '".$this->getField("CR_BAST_PEKERJAAN_JABATAN_PENERIMA")."',
           CR_BAST_PEKERJAAN_STATUS   = '".$this->getField("CR_BAST_PEKERJAAN_STATUS")."',
           CR_BAST_PEKERJAAN_UPDATED_BY = ".$this->getField("CREATED_BY").",
           CR_BAST_PEKERJAAN_UPDATED_DATE = CURRENT_TIMESTAMP
        WHERE CONTRACTINGREKANANPROSES5ID = ".$this->getField("CONTRACTINGREKANANPROSES5ID")."
        ";
        // echo $str; die();
        $this->query = $str;
    return $this->execQuery($str);
  }

  function insertProses5HasilMulti()
  {
    $this->setField("CONTRACTINGREKANANPROSES5ID", $this->getNextId("CONTRACTINGREKANANPROSES5ID","CONTRACTING_REKANAN_PROSES5"));

    $str = "
    INSERT INTO CONTRACTING_REKANAN_PROSES5 (
           CONTRACTINGREKANANPROSES5ID, PAKET_ID, CONTRACTINGREKANANID, CR_BAST_PEKERJAAN_NOMOR, CR_BAST_PEKERJAAN_TANGGAL, CR_BAST_PEKERJAAN_NAMA_PENYEDIA, CR_BAST_PEKERJAAN_JABATAN_PENYEDIA, CR_BAST_PEKERJAAN_NAMA_PENERIMA, CR_BAST_PEKERJAAN_JABATAN_PENERIMA, CR_BAST_PEKERJAAN_STATUS, CR_BAST_PEKERJAAN_UPDATED_BY, CR_BAST_PEKERJAAN_UPDATED_DATE, CREATED_BY, CREATED_DATE, REKANAN_ID)
    VALUES (
        ".$this->getField("CONTRACTINGREKANANPROSES5ID").",
        ".$this->getField("PAKET_ID").",
        ".$this->getField("CONTRACTINGREKANANID").",
        '".$this->getField("CR_BAST_PEKERJAAN_NOMOR")."',
        ".$this->getField("CR_BAST_PEKERJAAN_TANGGAL").",
        '".$this->getField("CR_BAST_PEKERJAAN_NAMA_PENYEDIA")."',
        '".$this->getField("CR_BAST_PEKERJAAN_JABATAN_PENYEDIA")."',
        '".$this->getField("CR_BAST_PEKERJAAN_NAMA_PENERIMA")."',
        '".$this->getField("CR_BAST_PEKERJAAN_JABATAN_PENERIMA")."',
        '".$this->getField("CR_BAST_PEKERJAAN_STATUS")."',
        ".$this->getField("CREATED_BY").",
        CURRENT_TIMESTAMP,
        ".$this->getField("CREATED_BY").",
        CURRENT_TIMESTAMP,
        ".$this->getField("REKANAN_ID")."
    )";
        // echo $str; die();
        $this->query = $str;
    return $this->execQuery($str);
  }

  // BAST Masa Pemeliharaan
  function insertProses5Pemeliharaan()
  {
    $this->setField("CONTRACTINGREKANANPROSES5ID", $this->getNextId("CONTRACTINGREKANANPROSES5ID","CONTRACTING_REKANAN_PROSES5"));

    $str = "
    INSERT INTO CONTRACTING_REKANAN_PROSES5 (
           CONTRACTINGREKANANPROSES5ID, PAKET_ID, CONTRACTINGREKANANID, CR_BAST_MASA_NOMOR, CR_BAST_MASA_TANGGAL, CR_BAST_MASA_NAMA_PENYEDIA, CR_BAST_MASA_JABATAN_PENYEDIA, CR_BAST_MASA_NAMA_PENERIMA, CR_BAST_MASA_JABATAN_PENERIMA, CR_BAST_MASA_STATUS, CR_BAST_MASA_UPDATED_BY, CR_BAST_MASA_UPDATED_DATE, CREATED_BY, CREATED_DATE)
    VALUES (
        ".$this->getField("CONTRACTINGREKANANPROSES5ID").",
        ".$this->getField("PAKET_ID").",
        ".$this->getField("CONTRACTINGREKANANID").",
        '".$this->getField("CR_BAST_MASA_NOMOR")."',
        ".$this->getField("CR_BAST_MASA_TANGGAL").",
        '".$this->getField("CR_BAST_MASA_NAMA_PENYEDIA")."',
        '".$this->getField("CR_BAST_MASA_JABATAN_PENYEDIA")."',
        '".$this->getField("CR_BAST_MASA_NAMA_PENERIMA")."',
        '".$this->getField("CR_BAST_MASA_JABATAN_PENERIMA")."',
        '".$this->getField("CR_BAST_MASA_STATUS")."',
        ".$this->getField("CREATED_BY").",
        CURRENT_TIMESTAMP,
        ".$this->getField("CREATED_BY").",
        CURRENT_TIMESTAMP
    )";
        // echo $str; die();
        $this->query = $str;
    return $this->execQuery($str);
  }

  function updateProses5Pemeliharaan()
  {
    $str = "UPDATE CONTRACTING_REKANAN_PROSES5 SET
           CR_BAST_MASA_NOMOR   = '".$this->getField("CR_BAST_MASA_NOMOR")."',
           CR_BAST_MASA_TANGGAL  = ".$this->getField("CR_BAST_MASA_TANGGAL").",
           CR_BAST_MASA_NAMA_PENYEDIA   = '".$this->getField("CR_BAST_MASA_NAMA_PENYEDIA")."',
           CR_BAST_MASA_JABATAN_PENYEDIA   = '".$this->getField("CR_BAST_MASA_JABATAN_PENYEDIA")."',
           CR_BAST_MASA_NAMA_PENERIMA   = '".$this->getField("CR_BAST_MASA_NAMA_PENERIMA")."',
           CR_BAST_MASA_JABATAN_PENERIMA   = '".$this->getField("CR_BAST_MASA_JABATAN_PENERIMA")."',
           CR_BAST_MASA_STATUS   = '".$this->getField("CR_BAST_MASA_STATUS")."',
           CR_BAST_MASA_UPDATED_BY = ".$this->getField("CREATED_BY").",
           CR_BAST_MASA_UPDATED_DATE = CURRENT_TIMESTAMP
        WHERE CONTRACTINGREKANANPROSES5ID = ".$this->getField("CONTRACTINGREKANANPROSES5ID")."
        ";
        // echo $str; die();
        $this->query = $str;
    return $this->execQuery($str);
  }

  function updatePemeriksa()
  {
    $str = "UPDATE CONTRACTING_REKANAN_PROSES5 SET
           CR_PEMERIKSA_NAMA   = '".$this->getField("CR_PEMERIKSA_NAMA")."',
           CR_PEMERIKSA_JABATAN  = '".$this->getField("CR_PEMERIKSA_JABATAN")."',
           CR_PEMERIKSA_APPROVAL  = '".$this->getField("CR_PEMERIKSA_APPROVAL")."',
           CR_PEMERIKSA_CREATED_BY = ".$this->USER_LOGIN_ID.",
           CR_PEMERIKSA_CREATED_DATE = CURRENT_TIMESTAMP
        WHERE CONTRACTINGREKANANID = ".$this->getField("CONTRACTINGREKANANID")."
        ";
        // echo $str; die();
        $this->query = $str;
    return $this->execQuery($str);
  }

  // function inserDelivery()
  // {
  //   /*Auto-generate primary key(s) by next max value (integer) */
  //   $this->setField("DELIVERABLEID", $this->getNextId("DELIVERABLEID","CONTRACTING_DELIVERABLE"));

  //   $str = "
  //   INSERT INTO CONTRACTING_DELIVERABLE
  //   ( DELIVERABLEID, LINGKUP, DELIVERY_NAMA, STATUS, CONTRACTINGREKANANID, CREATED_BY, CREATED_DATE)
  //     VALUES (
  //         ".$this->getField("DELIVERABLEID").",
  //         '".$this->getField("LINGKUP")."',
  //         '".$this->getField("DELIVERY_NAMA")."',
  //         '".$this->getField("STATUS")."',
  //         '".$this->getField("CONTRACTINGREKANANID")."',
  //         ".$this->getField("CREATED_BY").",
  //         CURRENT_TIMESTAMP
  //     )";
  //   // echo $str; die();
  //   $this->query = $str;
  //   if ($this->execQuery($str)) {
  //     return TRUE;
  //   } else {
  //     return FALSE;
  //   }
  // }

  function updatePublish(){
    $str = "
        UPDATE KATALOG
        SET
             PUBLISH        = '".$this->getField("PUBLISH")."',
             PUBLISH_BY     = '".$this->getField("PUBLISH_BY")."',
             PUBLISH_DATE   = NOW()
        WHERE  KATALOGID    = ".$this->getField("KATALOGID")."
        ";
          // echo $str; die();
          $this->query = $str;
        return $this->execQuery($str);
  }

  function selectById($paket_id){
    $str = "SELECT * FROM VIEW_CONTRACTING_PAKET
            WHERE
              PAKET_ID = '".$paket_id."'";

  $this->query = $str;

    return $this->select($str);
  }

    function selectMenuProses($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.contractingprosesid ASC"){
      $str = "SELECT
          A.*
        FROM contracting_proses A
        WHERE 1=1 ".$stat;
        foreach ($paramsArray as $key => $val) {
          // $str .= " AND $key = '$val' ";
          // ikn 20190218
          $pecah = explode("||", $key);
          if (count($pecah) > 1) {
            $str .= "AND $pecah[0] $pecah[1] $val ";
          } else {
            $str .= " AND $key = '$val' ";
          }
        }
      $str .= " ".$order;
        // echo $str; die();
      $this->query = $str;
      return $this->selectLimit($str,$limit,$from);
    }

    function selectMatrix($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.cm_order_by ASC"){
      $str = "SELECT
          A.*
        FROM contracting_matrix A
        WHERE 1=1 ";
        foreach ($paramsArray as $key => $val) {
          // $str .= " AND $key = '$val' ";
          // ikn 20190218
          $pecah = explode("||", $key);
          if (count($pecah) > 1) {
            $str .= "AND $pecah[0] $pecah[1] $val ";
          } else {
            $str .= " AND $key = '$val' ";
          }
        }
      $str .= $stat." ".$order;
        // echo $str; die();
      $this->query = $str;
      return $this->selectLimit($str,$limit,$from);
    }

    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.PAKET_ID ASC"){
      $str = "SELECT
          A.*
        FROM VIEW_CONTRACTING_PAKET A
        WHERE 1=1 ".$stat;
        foreach ($paramsArray as $key => $val) {
          // $str .= " AND $key = '$val' ";
          // ikn 20190218
          $pecah = explode("||", $key);
          if (count($pecah) > 1) {
            $str .= "AND $pecah[0] $pecah[1] $val ";
          } else {
            $str .= " AND $key = '$val' ";
          }
        }
      $str .= " ".$order;
        // echo $str; die();
      $this->query = $str;
      return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsViewContracting($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.PAKET_ID ASC"){
      $str = "SELECT
          A.*
        FROM VIEW_CONTRACTING_PAKET A
        WHERE 1=1 ".$stat;
        foreach ($paramsArray as $key => $val) {
          // $str .= " AND $key = '$val' ";
          // ikn 20190218
          $pecah = explode("||", $key);
          if (count($pecah) > 1) {
            $str .= "AND $pecah[0] $pecah[1] $val ";
          } else {
            $str .= " AND $key = '$val' ";
          }
        }
      $str .= " ".$order;
        // echo $str; die();
      $this->query = $str;
      return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsViewContractingPenilaian($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.PAKET_ID ASC"){
      $str = "
        SELECT A.* FROM (
          SELECT A.*, B.REKANAN_ID, B.REKANAN_ID_STR 
          FROM VIEW_CONTRACTING_PAKET A 
          LEFT JOIN VIEW_CONTRACTING_REKANAN B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
        ) A 
        WHERE 1=1 ".$stat;
        foreach ($paramsArray as $key => $val) {
          // $str .= " AND $key = '$val' ";
          // ikn 20190218
          $pecah = explode("||", $key);
          if (count($pecah) > 1) {
            $str .= "AND $pecah[0] $pecah[1] $val ";
          } else {
            $str .= " AND $key = '$val' ";
          }
        }
      $str .= " ".$order;
        // echo $str; die();
      $this->query = $str;
      return $this->selectLimit($str,$limit,$from);
    }

    function getCountByParamsViewContractingPenilaian($paramsArray=array(), $statement='')
    {
      $str = "SELECT COUNT(*) AS ROWCOUNT
                from ( 
                SELECT A.* FROM (
                  SELECT A.*, B.REKANAN_ID, B.REKANAN_ID_STR 
                  FROM VIEW_CONTRACTING_PAKET A 
                  LEFT JOIN VIEW_CONTRACTING_REKANAN B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID 
                ) A WHERE 1=1 "; 

      while(list($key,$val)=each($paramsArray))
      {
        // $str .= " AND $key = '$val' ";
        // ikn 20190218
        $pecah = explode("||", $key);
        if (count($pecah) > 1) {
          $str .= "AND $pecah[0] $pecah[1] $val ";
        } else {
          $str .= " AND $key = '$val' ";
        }
      }
      $str .= $statement;
      $str .= ') A where 1 = 1';

      $this->select($str);
      $this->query = $str;
      // echo $str; exit();
      if($this->firstRow())
        return $this->getField("ROWCOUNT");
      else
        return 0;
    }


    function getCountByParamsViewContracting($paramsArray=array(), $statement='')
    {
      $str = "SELECT COUNT(*) AS ROWCOUNT
              FROM ( SELECT A.* FROM VIEW_CONTRACTING_PAKET A
              WHERE 1=1 ";
      while(list($key,$val)=each($paramsArray))
      {
        // $str .= " AND $key = '$val' ";
        // ikn 20190218
        $pecah = explode("||", $key);
        if (count($pecah) > 1) {
          $str .= "AND $pecah[0] $pecah[1] $val ";
        } else {
          $str .= " AND $key = '$val' ";
        }
      }
      $str .= $statement;
      $str .= ') A where 1 = 1';

      $this->select($str);
      $this->query = $str;
      // echo $str; exit();
      if($this->firstRow())
        return $this->getField("ROWCOUNT");
      else
        return 0;
    }

    function getCountByParams($paramsArray=array(), $statement=""){
      $str = "SELECT COUNT(*) AS ROWCOUNT
                from ( SELECT PAKET_ID FROM VIEW_CONTRACTING_PAKET A WHERE 1=1 ";
      while(list($key,$val)=each($paramsArray))
      {
        // $str .= " AND $key = '$val' ";
        // ikn 20190218
        $pecah = explode("||", $key);
        if (count($pecah) > 1) {
          $str .= "AND $pecah[0] $pecah[1] $val ";
        } else {
          $str .= " AND $key = '$val' ";
        }
      }
      $str .= $statement;
      $str .= ') A where 1 = 1';

      $this->select($str);
      $this->query = $str;
      // echo $str;
      if($this->firstRow())
        return $this->getField("ROWCOUNT");
      else
        return 0;
    }

    function getSumByParams($paramsArray=array(), $statement='')
    {
      $str = "select SUM(NILAI) AS ROWCOUNT
                          from ( SELECT
                                  A.NILAI, A.TAHUN, A.JENIS_KONTRAK, A.PPK, A.STATUS_KONTRAK, A.PAKET_METODE_LELANG_ID,A.SELESAI, A.JNS_KONTRAK,A.CR_LEGAL_TANGGAL
                                 FROM VIEW_CONTRACTING_PAKET A
                                ) A where 1 = 1";
      while(list($key,$val)=each($paramsArray))
      {
        // $str .= " AND $key = '$val' ";
        // ikn 20190218
        $pecah = explode("||", $key);
        if (count($pecah) > 1) {
          $str .= "AND $pecah[0] $pecah[1] $val ";
        } else {
          $str .= " AND $key = '$val' ";
        }
      }
      $str .= $statement;

      $this->select($str);
      $this->query = $str;
      // echo $str; exit();
      if($this->firstRow())
        return $this->getField("ROWCOUNT");
      else
        return 0;
    }

    function delete()
    {
      $str = "DELETE FROM KONTRAK
                  WHERE
                    KONTRAK = ".$this->getField("KONTRAKID")."";
                    // echo $str; die();
      $this->query = $str;
          return $this->execQuery($str);
    }

    function selectJenisPekerjaan($paramsArray=array(),$limit=-1,$from=-1, $statement='')
    {
      $str = "SELECT CONTRACTINGJENISPEKERJAANID, JP_NAME
              FROM CONTRACTING_JENIS_PEKERJAAN
              WHERE 1 = 1 ";

      while(list($key,$val) = each($paramsArray))
      {
        $str .= " AND $key = '$val' ";
      }

      $this->query = $str;
      $str .= $statement." ORDER BY CONTRACTINGJENISPEKERJAANID ASC";

      return $this->selectLimit($str,$limit,$from);
    }

    function selectJenisPengadaan($paramsArray=array(),$limit=-1,$from=-1, $statement='')
    {
      $str = "SELECT PAKET_JENIS_ID, NAMA
              FROM PAKET_JENIS
              WHERE 1 = 1 ";

      while(list($key,$val) = each($paramsArray))
      {
        $str .= " AND $key = '$val' ";
      }

      $this->query = $str;
      $str .= $statement." ORDER BY PAKET_JENIS_ID ASC";

      return $this->selectLimit($str,$limit,$from);
    }

    function selectJenisKontrak($paramsArray=array(),$limit=-1,$from=-1, $statement='')
    {
      $str = "SELECT CONTRACTINGJENISKONTRAKID, JK_NAME
              FROM CONTRACTING_JENIS_KONTRAK
              WHERE 1 = 1 ";

      while(list($key,$val) = each($paramsArray))
      {
        $str .= " AND $key = '$val' ";
      }

      $this->query = $str;
      $str .= $statement." ORDER BY CONTRACTINGJENISKONTRAKID ASC";

      return $this->selectLimit($str,$limit,$from);
    }

    function selectStatusKontrak($paramsArray=array(),$limit=-1,$from=-1, $statement='')
    {
      $str = "SELECT CONTRACTINGSTATUSKONTRAKID, SK_NAME
              FROM CONTRACTING_STATUS_KONTRAK
              WHERE 1 = 1 ";

      while(list($key,$val) = each($paramsArray))
      {
        $str .= " AND $key = '$val' ";
      }

      $this->query = $str;
      $str .= $statement." ORDER BY CONTRACTINGSTATUSKONTRAKID ASC";

      return $this->selectLimit($str,$limit,$from);
    }

    function nilaiSuratPesanan($statement,$limit=-1,$from=-1)
    {
      $str = "SELECT b.total, b.cr_nilai_kontrak, b.cr_nilai_kontrak - b.total sisa, b.penyedia from (  
                select sum(a.total) total, a.cr_nilai_kontrak, a.penyedia from (
                  select a.rekanan_id, a.suratpesananid, a.contractingrekananid, a.contractingrekananproses1id, a.penyedia, a.total, a.cr_nilai_kontrak
                  from view_contracting_surat_pesanan_material a
                ) a
                  where 1=1 ".$statement."
                group by a.penyedia, a.cr_nilai_kontrak
              ) b";

      $this->query = $str;

      return $this->selectLimit($str,$limit,$from);
    }

    function getNilaiKontrakPenyediaByNilai($statement,$limit=-1,$from=-1)
    {
      $str = "SELECT a.*, (a.nilai_kontrak - a.total) sisa  from 
              (
                select a.cr_nilai_kontrak nilai_kontrak,
                ( select case when sum(total) > 0 then sum(total) else 0 end total from view_contracting_surat_pesanan_material x where x.rekanan_id=a.rekanan_id and x.contractingrekananid=a.contractingrekananid )
                from contracting_rekanan_proses1 a 
                where 1=1 ".$statement."
              ) a";

      $this->query = $str;

      return $this->selectLimit($str,$limit,$from);
    }

    function cekSisaQty($contractingrekananid,$materialid,$limit=-1,$from=-1)
    {
      $str = "SELECT a.*, a.qty_maksimal-a.qty_total sisa from (
              select a.contractingrekananid, a.materialid, a.nama, a.qty_maksimal, case when a.qty_total is null then 0 else a.qty_total end as qty_total 
              from (
                select a.contractingrekananid, a.materialid, a.nama, a.qty qty_maksimal, sum(b.qty) qty_total
                from contracting_material a 
                left join contracting_surat_pesanan_material b on a.materialid=b.materialid
                group by a.materialid, a.nama, a.qty
                ) a
              ) a 
              where a.contractingrekananid = ".$contractingrekananid." and a.materialid = ".$materialid."
              order by a.materialid asc";

      $this->query = $str;

      return $this->selectLimit($str,$limit,$from);
    }

    function updatePICPengendaliKontrak()
    {
      $str = "
          UPDATE  CONTRACTING_REKANAN
          SET
          PIC_PENGENDALI = '".$this->getField("PIC")."',
          UPDATED_BY = ".$this->USER_LOGIN_ID.",
          PIC_PENGENDALI_DATE = CURRENT_TIMESTAMP
          WHERE  CONTRACTINGREKANANID =  ".$this->getField("CONTRACTINGREKANANID")."
          ";
      $this->query = $str;
      if ($this->execQuery($str)) {
        $str2 = "
          UPDATE  CONTRACTING_REKANAN_PROSES1
          SET
          CR_NAMA_PENGAWAS_UNIT_KERJA = '".$this->getField("CR_NAMA_PENGAWAS_UNIT_KERJA")."',
          UPDATED_BY = ".$this->USER_LOGIN_ID.",
          UPDATED_DATE = CURRENT_TIMESTAMP
          WHERE  CONTRACTINGREKANANID =  ".$this->getField("CONTRACTINGREKANANID")."
          ";
      $this->query = $str2;
      }
      // echo $str; die;
      return $this->execQuery($str2);
    }

    function updatePICPenyelesaiKontrak()
    {
      $str = "
          UPDATE  CONTRACTING_REKANAN
          SET
          PIC_PENYELESAIAN = '".$this->getField("PIC")."',
          UPDATED_BY = ".$this->USER_LOGIN_ID.",
          PIC_PENYELESAIAN_DATE = CURRENT_TIMESTAMP
          WHERE  CONTRACTINGREKANANID =  ".$this->getField("CONTRACTINGREKANANID")."
          ";
      // echo $str; die;
          $this->query = $str;
      return $this->execQuery($str);
    }

    function updatePO()
    {
      $str = "
          UPDATE  CONTRACTING_REKANAN_PROSES1
          SET
          CR_PO = '".$this->getField("CR_PO")."',
          UPDATED_BY = ".$this->USER_LOGIN_ID.",
          UPDATED_DATE = CURRENT_TIMESTAMP
          WHERE  CONTRACTINGREKANANID =  ".$this->getField("CONTRACTINGREKANANID")."
          ";
      // echo $str; die;
          $this->query = $str;
      return $this->execQuery($str);
    }

  }
?>
