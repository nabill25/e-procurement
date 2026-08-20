<?php
 /**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 
  include_once('entity.php');

  class Contractingjaminanpemeliharaan extends Entity{

	var $query;
     
  function __construct(){
    $this->Entity();
  }

  function canInsert(){
    return true;
  }  

  function insertJaminan()
  { 
    $this->setField("CONTRACTING_JAMPEL_ID", $this->getNextId("CONTRACTING_JAMPEL_ID","CONTRACTING_JAMINAN_PEMELIHARAAN")); 

    $str = "
    INSERT INTO CONTRACTING_JAMINAN_PEMELIHARAAN 
    ( CONTRACTING_JAMPEL_ID, CONTRACTINGREKANANID, PAKET_ID, NOMOR, FILE_JAMINAN, TANGGAL_MULAI, TANGGAL_AKHIR, MASA, NILAI, CREATED_BY, CREATED_DATE) 
      VALUES (
          ".$this->getField("CONTRACTING_JAMPEL_ID").",
          ".$this->getField("CONTRACTINGREKANANID").",
          ".$this->getField("PAKET_ID").",
          '".$this->getField("NOMOR")."',
          '".$this->getField("FILE_JAMINAN")."',
          ".$this->getField("TANGGAL_MULAI").",
          ".$this->getField("TANGGAL_AKHIR").",
          '".$this->getField("MASA")."',
          ".$this->getField("NILAI").",
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
   UPDATE  CONTRACTING_JAMINAN_PEMELIHARAAN
    SET
         NOMOR        = '".$this->getField("NOMOR")."',
         FILE_JAMINAN        = '".$this->getField("FILE_JAMINAN")."',
         TANGGAL_MULAI        = ".$this->getField("TANGGAL_MULAI").",
         TANGGAL_AKHIR        = ".$this->getField("TANGGAL_AKHIR").",
         MASA        = '".$this->getField("MASA")."',
         NILAI        = ".$this->getField("NILAI").",
         UPDATED_BY    = '".$this->getField("CREATED_BY")."',
         UPDATED_DATE  = CURRENT_TIMESTAMP
    WHERE  CONTRACTING_JAMPEL_ID =  ".$this->getField("CONTRACTING_JAMPEL_ID")."
    "; 
    // echo $str; die();
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  }
  
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.CONTRACTING_JAMPEL_ID ASC"){
    $str = "SELECT
				A.*
			FROM CONTRACTING_JAMINAN_PEMELIHARAAN A 
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
    $str = "DELETE FROM CONTRACTING_JAMINAN_PEMELIHARAAN
                WHERE
                  CONTRACTING_JAMPEL_ID = ".$this->getField("CONTRACTING_JAMPEL_ID")."";
    $this->query = $str;
        return $this->execQuery($str);
  }
 
  }
?>
