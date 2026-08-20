<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Dashcontractingui extends Entity{

  var $query;

    function __construct(){
      $this->Entity();
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
      $str = "select SUM(CR_NILAI_KONTRAK) AS ROWCOUNT
                          from ( SELECT
                                  A.NILAI, A.CR_NILAI_KONTRAK, A.TAHUN, A.JENIS_KONTRAK, A.PPK, A.STATUS_KONTRAK, A.PAKET_METODE_LELANG_ID,A.SELESAI, A.JNS_KONTRAK,A.CR_LEGAL_TANGGAL
                                 FROM VIEW_CONTRACTING_PAKET A
                                 WHERE 1 = 1 ".$statement."
                                ";
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
      $str .= ' ) A where 1 = 1';

      $this->select($str);
      $this->query = $str;
      // echo $str; exit();
      if($this->firstRow())
        return $this->getField("ROWCOUNT");
      else
        return 0;
    }

    function getSumSPPBJByParams($paramsArray=array(), $statement='')
    {
      $str = "select SUM(NILAI) AS ROWCOUNT
                          from ( SELECT
                                  A.NILAI, A.CR_SPPBJ_NILAI, A.CR_NILAI_KONTRAK, A.TAHUN, A.JENIS_KONTRAK, A.PPK, A.STATUS_KONTRAK, A.PAKET_METODE_LELANG_ID,A.SELESAI, A.JNS_KONTRAK,A.CR_LEGAL_TANGGAL
                                 FROM VIEW_CONTRACTING_PAKET A
                                 WHERE 1 = 1 ".$statement."
                                ";
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
      $str .= ' ) A where 1 = 1';

      $this->select($str);
      $this->query = $str;
      // echo $str; exit();
      if($this->firstRow())
        return $this->getField("ROWCOUNT");
      else
        return 0;
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

  }
?>
