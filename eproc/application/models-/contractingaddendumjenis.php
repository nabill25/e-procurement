<?php
 /**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 
  include_once('entity.php');

  class Contractingaddendumjenis extends Entity{

	var $query;
     
  function __construct(){
    $this->Entity();
  }

  function canInsert(){
    return true;
  }   
  
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.CONTRACTING_ADDENDUM_JENIS_ID ASC"){
    $str = "SELECT
				A.*
			FROM CONTRACTING_ADDENDUM_JENIS A 
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
    $str = "DELETE FROM CONTRACTING_ADDENDUM_JENIS
                WHERE
                  CONTRACTING_ADDENDUM_JENIS_ID = ".$this->getField("CONTRACTING_ADDENDUM_JENIS_ID")."";
    $this->query = $str;
        return $this->execQuery($str);
  }
 
  }
?>
