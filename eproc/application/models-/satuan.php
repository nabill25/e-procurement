<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class Satuan extends Entity{

	var $query;
     
  function __construct(){
    $this->Entity();
  }

  function canInsert(){
    return true;
  }  

  function inserSatuan()
  { 
    /*Auto-generate primary key(s) by next max value (integer) */
    $this->setField("SATUANID", $this->getNextId("SATUANID","SATUAN")); 

    $str = "
    INSERT INTO SATUAN 
    ( SATUANID) 
      VALUES (
          ".$this->getField("SATUANID")."
      )"; 
    // echo $str; die();
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  }

  function updateSatuan()
  { 
    $str = "
    UPDATE  SATUAN
    SET
         NAMA        = '".$this->getField("NAMA")."',
    WHERE  SATUANID =  ".$this->getField("SATUANID")."
    "; 
    // echo $str; die;
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  } 
  
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.SATUANID ASC"){
    $str = "SELECT A.* FROM SATUAN A 
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
    $str = "DELETE FROM SATUAN
            WHERE
            SATUANID = ".$this->getField("SATUANID")."";
    $this->query = $str;
        return $this->execQuery($str);
  }
 
  }
?>
