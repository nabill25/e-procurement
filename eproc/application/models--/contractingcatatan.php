<?php
 /**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 
  include_once('entity.php');

  class Contractingcatatan extends Entity{

	var $query;
     
  function __construct(){
    $this->Entity();
  }

  function canInsert(){
    return true;
  }  

  function inserCatatan()
  { 
    /*Auto-generate primary key(s) by next max value (integer) */
    $this->setField("CONTRACTING_CATATAN_ID", $this->getNextId("CONTRACTING_CATATAN_ID","CONTRACTING_CATATAN")); 

    $str = "
    INSERT INTO CONTRACTING_CATATAN 
    ( CONTRACTING_CATATAN_ID, PAKET_ID, PESAN, CREATED_BY, CREATED_DATE, JENIS) 
      VALUES (
          ".$this->getField("CONTRACTING_CATATAN_ID").",
          ".$this->getField("PAKET_ID").",
          '".$this->getField("PESAN")."',
          ".$this->getField("CREATED_BY").",
          CURRENT_TIMESTAMP,
          '".$this->getField("JENIS")."'
      )"; 
    // echo $str; die();
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  }

  function inserCatatanKontrak()
  { 
    /*Auto-generate primary key(s) by next max value (integer) */
    $this->setField("CONTRACTING_CATATAN_ID", $this->getNextId("CONTRACTING_CATATAN_ID","CONTRACTING_CATATAN")); 

    $str = "
    INSERT INTO CONTRACTING_CATATAN 
    ( CONTRACTING_CATATAN_ID, PAKET_ID, PESAN_KONTRAK, CREATED_BY, CREATED_DATE) 
      VALUES (
          ".$this->getField("CONTRACTING_CATATAN_ID").",
          ".$this->getField("PAKET_ID").",
          '".$this->getField("PESAN_KONTRAK")."',
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

  function updateCatatan()
  { 
    $str = "
   UPDATE  CONTRACTING_CATATAN
    SET
         PESAN        = '".$this->getField("PESAN")."',
         CREATED_BY    = '".$this->getField("CREATED_BY")."',
         CREATED_DATE  = CURRENT_TIMESTAMP
    WHERE  CONTRACTING_CATATAN_ID =  ".$this->getField("CONTRACTING_CATATAN_ID")."
    "; 
    
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  } 
  
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.CONTRACTING_CATATAN_ID ASC"){
    $str = "SELECT
				A.*
			FROM CONTRACTING_CATATAN A 
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

  public function delAll()
  {
    $strDel = "
    DELETE FROM CONTRACTING_CATATAN WHERE PAKET_ID=".$this->getField("PAKET_ID")." AND JENIS='".$this->getField("JENIS")."' AND CREATED_BY = ".$this->getField("CREATED_BY").""; 
    $this->query = $str;
    return $this->execQuery($strDel);
  }

  public function delAllPenyedia()
  {
    $strDel = "
    DELETE FROM CONTRACTING_CATATAN WHERE PAKET_ID=".$this->getField("PAKET_ID")." AND JENIS='".$this->getField("JENIS")."' AND CREATED_BY = ".$this->getField("CREATED_BY")." "; 
    $this->query = $str;
    return $this->execQuery($strDel);
  }

  function delete()
  {
    $str = "DELETE FROM CONTRACTING_CATATAN
                WHERE
                  CONTRACTING_CATATAN_ID = ".$this->getField("CONTRACTING_CATATAN_ID")."";
                  // echo $str; die();
    $this->query = $str;
        return $this->execQuery($str);
  }
 
  }
?>
