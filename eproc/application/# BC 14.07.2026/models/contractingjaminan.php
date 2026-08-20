<?php
 /**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 
  include_once('entity.php');

  class Contractingjaminan extends Entity{

	var $query;
     
  function __construct(){
    $this->Entity();
  }

  function canInsert(){
    return true;
  }  

  function insertJaminan()
  { 
    /*Auto-generate primary key(s) by next max value (integer) */
    $this->setField("CONTRACTING_JAMINAN_ID", $this->getNextId("CONTRACTING_JAMINAN_ID","CONTRACTING_JAMINAN")); 

    $str = "
    INSERT INTO CONTRACTING_JAMINAN 
    ( CONTRACTING_JAMINAN_ID, CONTRACTINGREKANANID, PAKET_ID, NOMOR, TANGGAL_JAMINAN, FILE_JAMINAN, CREATED_BY, CREATED_DATE) 
      VALUES (
          ".$this->getField("CONTRACTING_JAMINAN_ID").",
          ".$this->getField("CONTRACTINGREKANANID").",
          ".$this->getField("PAKET_ID").",
          '".$this->getField("NOMOR")."',
          ".$this->getField("TANGGAL_JAMINAN").",
          '".$this->getField("FILE_JAMINAN")."',
          ".$this->getField("CREATED_BY").",
          CURRENT_TIMESTAMP
      )"; 
    // echo $str; die();
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  }

  function updateJaminan()
  { 
    $str = "
   UPDATE  CONTRACTING_JAMINAN
    SET
         NOMOR        = '".$this->getField("NOMOR")."',
         TANGGAL_JAMINAN        = ".$this->getField("TANGGAL_JAMINAN").",
         FILE_JAMINAN        = '".$this->getField("FILE_JAMINAN")."',
         UPDATED_BY    = '".$this->getField("CREATED_BY")."',
         UPDATED_DATE  = CURRENT_TIMESTAMP
    WHERE  CONTRACTING_JAMINAN_ID =  ".$this->getField("CONTRACTING_JAMINAN_ID")."
    "; 
    // echo $str; die();
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  }

  function updateJaminanAll()
  { 
    $str = "
   UPDATE  CONTRACTING_JAMINAN
    SET
         NOMOR        = '".$this->getField("NOMOR")."',
         TANGGAL_JAMINAN        = ".$this->getField("TANGGAL_JAMINAN").",
         TANGGAL_KONFIRMASI_KEBANK        = ".$this->getField("TANGGAL_KONFIRMASI_KEBANK").",
         TANGGAL_KONFIRMASI_OLEH_BANK        = ".$this->getField("TANGGAL_KONFIRMASI_OLEH_BANK").",
         KONFIRMASI        = '".$this->getField("KONFIRMASI")."',
         FILE_KONFIRMASI        = '".$this->getField("FILE_KONFIRMASI")."',
         UPDATED_BY    = '".$this->getField("CREATED_BY")."',
         UPDATED_DATE  = CURRENT_TIMESTAMP
    WHERE  CONTRACTING_JAMINAN_ID =  ".$this->getField("CONTRACTING_JAMINAN_ID")."
    "; 
    // echo $str; die();
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  }  

  function updateJaminanAllUpdate()
  { 
    $str = "
   UPDATE  CONTRACTING_JAMINAN
    SET
         KONFIRMASI        = '".$this->getField("KONFIRMASI")."',
         UPDATED_BY    = '".$this->getField("CREATED_BY")."',
         UPDATED_DATE  = CURRENT_TIMESTAMP
    WHERE  CONTRACTING_JAMINAN_ID =  ".$this->getField("CONTRACTING_JAMINAN_ID")."
    "; 

    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  }  
  
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.CONTRACTING_JAMINAN_ID ASC"){
    $str = "SELECT
				A.*
			FROM CONTRACTING_JAMINAN A 
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
    $str = "DELETE FROM CONTRACTING_JAMINAN
                WHERE
                  CONTRACTING_JAMINAN_ID = ".$this->getField("CONTRACTING_JAMINAN_ID")."";
    $this->query = $str;
        return $this->execQuery($str);
  }
 
  }
?>
