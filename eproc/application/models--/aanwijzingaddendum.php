<?php
 /**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
  include_once('entity.php');

  class Aanwijzingaddendum extends Entity{

	var $query;
     
  function __construct(){
    $this->Entity();
  }

  function canInsert(){
    return true;
  }  

  function insertAanwijzing()
  { 
    /*Auto-generate primary key(s) by next max value (integer) */
    $this->setField("PAKET_AANWIJZING_HASIL_ID", $this->getNextId("PAKET_AANWIJZING_HASIL_ID","PAKET_AANWIJZING_HASIL")); 

    $str = "
    INSERT INTO PAKET_AANWIJZING_HASIL 
    ( PAKET_AANWIJZING_HASIL_ID, PAKET_ID, TOPIC, TOPIC_SEMULA, TOPIC_MENJADI, CREATED_BY, CREATED_DATE) 
      VALUES (
          ".$this->getField("PAKET_AANWIJZING_HASIL_ID").",
          ".$this->getField("PAKET_ID").",
          '".$this->getField("TOPIC")."',
          '".$this->getField("TOPIC_SEMULA")."',
          '".$this->getField("TOPIC_MENJADI")."',
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
  
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.PAKET_AANWIJZING_HASIL_ID ASC"){
    $str = "SELECT
				A.*
			FROM PAKET_AANWIJZING_HASIL A 
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
    DELETE FROM PAKET_AANWIJZING_HASIL WHERE PAKET_ID=".$this->getField("PAKET_ID")." "; 
    $this->query = $str;
    return $this->execQuery($strDel);
  } 
 
  }
?>
