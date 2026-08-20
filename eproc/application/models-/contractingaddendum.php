<?php
 /**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 
  include_once('entity.php');

  class Contractingaddendum extends Entity{

	var $query;
     
  function __construct(){
    $this->Entity();
  }

  function canInsert(){
    return true;
  }  

  function insertAddendum()
  { 
    /*Auto-generate primary key(s) by next max value (integer) */
    $this->setField("CONTRACTING_ADDENDUM_ID", $this->getNextId("CONTRACTING_ADDENDUM_ID","CONTRACTING_ADDENDUM")); 

    $str = "
    INSERT INTO CONTRACTING_ADDENDUM 
    ( CONTRACTING_ADDENDUM_ID, NOMOR, ADDENDUM_KE, TANGGAL, TANGGAL_KONTRAK_DARI, TANGGAL_KONTRAK_SAMPAI, TANGGAL_PENYELESAIAN_KONTRAK_AWAL, TANGGAL_PENYELESAIAN_KONTRAK_AKHIR, JENIS, PAKET_ID, CONTRACTINGREKANANID, CREATED_BY, CREATED_DATE, ADDENDUM_FILE_PERSETUJUAN, KETERANGAN, STATUS, ADDENDUM_NILAI) 
      VALUES (
          ".$this->getField("CONTRACTING_ADDENDUM_ID").",
          '".$this->getField("NOMOR")."',
          ".$this->getField("ADDENDUM_KE").",
          ".$this->getField("TANGGAL").",
          ".$this->getField("TANGGAL_KONTRAK_DARI").",
          ".$this->getField("TANGGAL_KONTRAK_SAMPAI").",
          ".$this->getField("TANGGAL_PENYELESAIAN_KONTRAK_AWAL").",
          ".$this->getField("TANGGAL_PENYELESAIAN_KONTRAK_AKHIR").",
          '".$this->getField("JENIS")."',
          ".$this->getField("PAKET_ID").",
          ".$this->getField("CONTRACTINGREKANANID").",
          ".$this->getField("CREATED_BY").",
          CURRENT_TIMESTAMP,
          '".$this->getField("ADDENDUM_FILE_PERSETUJUAN")."',
          '".$this->getField("KETERANGAN")."',
          'Proses',
          ".$this->getField("ADDENDUM_NILAI")."
      )"; 
    // echo $str; die();
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  }

  function updateAddendum()
  { 
    $str = "
   UPDATE  CONTRACTING_ADDENDUM
    SET
         NOMOR        = '".$this->getField("NOMOR")."',
         ADDENDUM_KE  = ".$this->getField("ADDENDUM_KE").",
         JENIS  = '".$this->getField("JENIS")."',
         TANGGAL        = ".$this->getField("TANGGAL").",
         TANGGAL_KONTRAK_DARI        = ".$this->getField("TANGGAL_KONTRAK_DARI").",
         TANGGAL_KONTRAK_SAMPAI        = ".$this->getField("TANGGAL_KONTRAK_SAMPAI").",
         TANGGAL_PENYELESAIAN_KONTRAK_AWAL        = ".$this->getField("TANGGAL_PENYELESAIAN_KONTRAK_AWAL").",
         TANGGAL_PENYELESAIAN_KONTRAK_AKHIR        = ".$this->getField("TANGGAL_PENYELESAIAN_KONTRAK_AKHIR").",
         UPDATED_BY    = '".$this->getField("CREATED_BY")."',
         UPDATED_DATE  = CURRENT_TIMESTAMP,
         ADDENDUM_FILE    = '".$this->getField("ADDENDUM_FILE")."',
         ADDENDUM_FILE_PERSETUJUAN    = '".$this->getField("ADDENDUM_FILE_PERSETUJUAN")."',
         KETERANGAN    = '".$this->getField("KETERANGAN")."',
         ADDENDUM_NILAI    = ".$this->getField("ADDENDUM_NILAI")."
    WHERE  CONTRACTING_ADDENDUM_ID =  ".$this->getField("CONTRACTING_ADDENDUM_ID")."
    "; 
    // echo $str; die();
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  }  
  
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.CONTRACTING_ADDENDUM_ID ASC"){
    $str = "SELECT
				A.*
			FROM CONTRACTING_ADDENDUM A 
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

  function delete()
  {
    $str = "DELETE FROM CONTRACTING_ADDENDUM
                WHERE
                  CONTRACTING_ADDENDUM_ID = ".$this->getField("CONTRACTING_ADDENDUM_ID")."";
    $this->query = $str;
        return $this->execQuery($str);
  }

  function approvalKasuhdit() // Done
  {
    $str = "UPDATE CONTRACTING_ADDENDUM 
            SET APPROVED_KASUBDIT = '".$this->getField("APPROVED_KASUBDIT")."', 
                APPROVED_KASUBDIT_DATE = CURRENT_TIMESTAMP 
            WHERE CONTRACTING_ADDENDUM_ID = ".$this->getField("CONTRACTING_ADDENDUM_ID")."
            ";
            // echo $str; die();
    $this->query = $str;
    if ($this->execQuery($str)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

    function approvalPenyedia() // Done
    {
      $str = "UPDATE CONTRACTING_ADDENDUM 
              SET APPROVED_PENYEDIA = '".$this->getField("APPROVED_PENYEDIA")."', 
                  APPROVED_PENYEDIA_DATE = CURRENT_TIMESTAMP 
              WHERE CONTRACTING_ADDENDUM_ID = ".$this->getField("CONTRACTING_ADDENDUM_ID")."
              ";
              // echo $str; die();
      $this->query = $str;
      if ($this->execQuery($str)) {
        return TRUE;
      } else {
        return FALSE;
      }
    }

    function addendumClose() // Done
    {
      $str = "UPDATE CONTRACTING_ADDENDUM 
              SET STATUS = '".$this->getField("STATUS")."', 
                  STATUS_DATE = CURRENT_TIMESTAMP 
              WHERE CONTRACTING_ADDENDUM_ID = ".$this->getField("CONTRACTING_ADDENDUM_ID")."
              ";
              // echo $str; die();
      $this->query = $str;
      if ($this->execQuery($str)) {
        return TRUE;
      } else {
        return FALSE;
      }
    }

    function updateFileAddendumPenyedia()
    { 
      $str = "
     UPDATE  CONTRACTING_ADDENDUM
      SET ADDENDUM_FILE_PENYEDIA  = '".$this->getField("ADDENDUM_FILE_PENYEDIA")."',
          APPROVED_PENYEDIA = '1', 
          APPROVED_PENYEDIA_DATE = CURRENT_TIMESTAMP,
          UPDATED_BY    = '".$this->getField("UPDATED_BY")."',
          UPDATED_DATE  = CURRENT_TIMESTAMP
      WHERE  CONTRACTING_ADDENDUM_ID =  ".$this->getField("CONTRACTING_ADDENDUM_ID")."
      "; 
      // echo $str; die;
      $this->query = $str;
      if ($this->execQuery($str)) { 
        return TRUE;
      } else { 
        return FALSE;
      }
    } 


 
  }
?>
