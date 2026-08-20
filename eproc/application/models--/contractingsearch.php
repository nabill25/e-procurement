<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class Contractingsearch extends Entity{

	var $query;
     
  function __construct(){
    $this->Entity();
  }
 
  function canInsert(){
    return true;    
  }
 
  function insertProses1()
  { 
    return $this->execQuery($str);
  }
   
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.PAKET_ID ASC")
  {
    $str = "SELECT A.* FROM VIEW_CONTRACTING_PAKET A 
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

 
  }
?>
