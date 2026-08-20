<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class Contractingsla extends Entity{

	var $query;
     
  function __construct(){
    $this->Entity();
  }

  function canInsert(){
    return true;
  }  

  function insertSla()
  {  
    /*Auto-generate primary key(s) by next max value (integer) */
    $this->setField("SLAID", $this->getNextId("SLAID","CONTRACTING_SLA")); 

    $str = "
    INSERT INTO CONTRACTING_SLA 
    ( SLAID, SLA_AVAILABILITY, SLA_WAKTU, SLA_DENDA, SLA_BIAYA_MAINTANANCE, SLA_NILAI_DENDA, CONTRACTINGREKANANID, CREATED_BY, CREATED_DATE) 
      VALUES (
          ".$this->getField("SLAID").",
          ".$this->getField("SLA_AVAILABILITY").",
          '".$this->getField("SLA_WAKTU")."',
          ".$this->getField("SLA_DENDA").",
          ".$this->getField("SLA_BIAYA_MAINTANANCE").",
          ".$this->getField("SLA_NILAI_DENDA").",
          ".$this->getField("CONTRACTINGREKANANID").",
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
  
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.SLAID ASC"){
    $str = "SELECT
				A.*
			FROM CONTRACTING_SLA A 
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
    DELETE FROM CONTRACTING_SLA WHERE CONTRACTINGREKANANID=".$this->getField("CONTRACTINGREKANANID")." "; 
    $this->query = $str;
    return $this->execQuery($strDel);
  }

  function delete()
  {
    $str = "DELETE FROM CONTRACTING_SLA
                WHERE
                  SLAID = ".$this->getField("SLAID")."";
                  // echo $str; die();
    $this->query = $str;
        return $this->execQuery($str);
  }
 
  }
?>
