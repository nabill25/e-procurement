<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

  include_once('entity.php');

  class Contractingrekanan extends Entity{

	var $query;

    function __construct(){
      $this->Entity();
    }

    function canInsert(){
      return true;
    }

    function insert(){
    }

    function canUpdate(){
      return true;
    }

    function update(){
    }

    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.CONTRACTINGREKANANID ASC"){
      $str = "SELECT
              A.contractingrekananid, A.paket_id, A.nama, A.uraian, A.nilai, A.contractingprosesid,
              CASE
                  WHEN (B.multi_pemenang = '1'::bpchar) THEN D.rekanan_id
                  ELSE a.rekanan_id
              END AS rekanan_id,
              A.jns_kontrak,
              B.PENGGUNA_STR, C.CP_NAME, C.CP_ICON, CP_LINK, D.CONTRACTINGREKANANPROSES1ID, C.CP_NAME CONTRACTINGPROSESID_STR, B.PPK, B.PIC_KONTRAK, B.PIC_PENGENDALI, B.PIC_PENYELESAIAN, D.CR_PO
              FROM CONTRACTING_REKANAN A
              JOIN VIEW_CONTRACTING_PAKET B ON A.PAKET_ID=B.PAKET_ID
              JOIN CONTRACTING_PROSES C ON C.CONTRACTINGPROSESID=A.CONTRACTINGPROSESID
              LEFT JOIN CONTRACTING_REKANAN_PROSES1 D ON A.CONTRACTINGREKANANID=D.CONTRACTINGREKANANID
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

    function selectProses1($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.CONTRACTINGREKANANPROSES1ID ASC"){
      $str = "SELECT
              A.*, B.NAMA NAMA_KOTA, D.JK_NAME CR_JENIS_KONTRAK_STR, E.SK_NAME CR_STATUS_KONTRAK_STR,F.SA, F.DPSJ, F.LIST_KEGIATAN, F.PIC_KONTRAK, F.PIC_PENGENDALI, F.PIC_PENYELESAIAN
              FROM CONTRACTING_REKANAN_PROSES1 A
              LEFT JOIN REGION B ON A.cr_sppbj_dirut_kota=B.REGION_ID
              LEFT JOIN CONTRACTING_JENIS_KONTRAK D ON A.contractingjeniskontrakid = D.contractingjeniskontrakid
              LEFT JOIN CONTRACTING_STATUS_KONTRAK E ON A.contractingstatuskontrakid = E.contractingstatuskontrakid
              LEFT JOIN VIEW_CONTRACTING_PAKET F ON A.CONTRACTINGREKANANID=F.CONTRACTINGREKANANID
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

    function selectProses4($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.CONTRACTINGREKANANPROSES4ID ASC"){
      $str = "SELECT
                A.*
                FROM CONTRACTING_REKANAN_PROSES4 A
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

    function selectProses5($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.CONTRACTINGREKANANPROSES5ID ASC")
    {
      $str = "SELECT
                A.*
                FROM CONTRACTING_REKANAN_PROSES5 A
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
    function selectText($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="LIMIT 1"){
      $str = "SELECT A.*
              FROM CONTRACTING_TEXT_MONITORING A
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

    function selectViewSPPBJ($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="LIMIT 1"){
      $str = "SELECT A.*
                FROM VIEW_CONTRACTING_REKANAN_PROSES1_SPPBJ A
              WHERE 1=1 ".$stat;
        foreach ($paramsArray as $key => $val) {
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

    function selectSPMK($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="LIMIT 1"){
      $str = "SELECT A.*, B.CONTRACTINGREKANANID
                FROM CONTRACTING_REKANAN_PROSES1_SPMK A
                JOIN CONTRACTING_REKANAN_PROSES1 B ON A.CONTRACTINGREKANANID = B.CONTRACTINGREKANANID
              WHERE 1=1 ".$stat;
        foreach ($paramsArray as $key => $val) {
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

    function selectViewPKSSPK($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="LIMIT 1"){
      $str = "SELECT A.*,B.SA, B.DPSJ, B.LIST_KEGIATAN, B.UNIT_KERJA_ID,B.PIC_KONTRAK, B.PIC_PENGENDALI, B.PIC_PENYELESAIAN, B.PENGGUNA, B.SUMBER_DANA_KETERANGAN, B.NILAI_MATA_UANG
                FROM VIEW_CONTRACTING_REKANAN_PROSES1_PKS_SPK A
                JOIN VIEW_CONTRACTING_PAKET B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
              WHERE 1=1 ".$stat;
        foreach ($paramsArray as $key => $val) {
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

    function selectViewLegal($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="LIMIT 1"){
      $str = "SELECT A.*
                FROM VIEW_CONTRACTING_REKANAN_PROSES1_LEGAL A
              WHERE 1=1 ".$stat;
        foreach ($paramsArray as $key => $val) {
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

    function selectDeliverable($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY DELIVERABLEID"){
      $str = "SELECT A.*
                FROM CONTRACTING_DELIVERABLE A
              WHERE 1=1 ".$stat;
        foreach ($paramsArray as $key => $val) {
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

    function selectByParamsCount($paramsArray=array(), $statement='')
    {
      $str = "SELECT COUNT(*) AS ROWCOUNT
              FROM ( SELECT A.*, C.PPK FROM CONTRACTING_REKANAN A
                                     LEFT JOIN CONTRACTING_REKANAN_PROSES1 B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                                     LEFT JOIN VIEW_CONTRACTING_PAKET C ON A.CONTRACTINGREKANANID=C.CONTRACTINGREKANANID
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

    function selectByParamsCount2($paramsArray=array(), $statement='')
    {
      $str = "SELECT COUNT(*) AS ROWCOUNT
              FROM ( SELECT A.*, C.PPK FROM CONTRACTING_REKANAN A
                                     LEFT JOIN VIEW_CONTRACTING_PAKET C ON A.CONTRACTINGREKANANID=C.CONTRACTINGREKANANID
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

    function getCountByParams($paramsArray=array(), $varStatement=""){
      $str = "SELECT COUNT(*) AS ROWCOUNT
               FROM (
               SELECT A.* FROM
               ( SELECT A.PAKET_ID, A.CONTRACTINGPROSESID, A.NAMA, A.URAIAN, A.CREATED_BY, A.NILAI, B.cr_sppbj_tanggal AS TAHUN_SPPBJ,C.TAHUN, A.JNS_KONTRAK, C.PPK, C.CR_LEGAL_TANGGAL
                      FROM CONTRACTING_REKANAN A
                      JOIN CONTRACTING_REKANAN_PROSES1 B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                      LEFT JOIN VIEW_CONTRACTING_PAKET C ON A.CONTRACTINGREKANANID=C.CONTRACTINGREKANANID
                ) A WHERE 1=1  ";
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

    function getSumByParams($paramsArray=array(), $statement='')
    {
      $str = "SELECT SUM(A.CR_NILAI_KONTRAK) AS ROWCOUNT
              FROM (
               SELECT A.* FROM
               ( SELECT A.PAKET_ID, A.CONTRACTINGPROSESID, A.NAMA, A.URAIAN, A.CREATED_BY, A.NILAI, B.cr_sppbj_tanggal AS TAHUN_SPPBJ,C.TAHUN, A.JNS_KONTRAK, C.PPK, B.CR_NILAI_KONTRAK,C.CR_LEGAL_TANGGAL
                      FROM CONTRACTING_REKANAN A
                      JOIN CONTRACTING_REKANAN_PROSES1 B ON A.CONTRACTINGREKANANID=B.CONTRACTINGREKANANID
                      LEFT JOIN VIEW_CONTRACTING_PAKET C ON A.CONTRACTINGREKANANID=C.CONTRACTINGREKANANID
                ) A
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
      $str = "DELETE FROM CONTRACTING_REKANAN
              WHERE CONTRACKTINGREKANANID = ".$this->getField("CONTRACKTINGREKANANID")."";
      $this->query = $str;
          return $this->execQuery($str);
    }

  }
?>
