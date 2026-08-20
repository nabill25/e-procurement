<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Tanggalmerah extends Entity{ 

		var $query;

  	function __construct(){
  		parent::__construct();
		}
	 
	  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="ORDER BY TM_ID ASC")
		{
			$str = "SELECT TM_ID, TM_NOTE, TM_DATE
							FROM TANGGAL_MERAH A
							WHERE 1 = 1
					"; 
			
			while(list($key,$val) = each($paramsArray))
			{
				$str .= " AND $key = '$val' ";
			}
			
			$this->query = $str;
			$str .= $statement." ".$order;
					
			return $this->selectLimit($str,$limit,$from); 
	  } 
    
  } 
?>