<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Kebijakan extends Entity{ 

	var $query;

  function __construct(){
  		parent::__construct();
	}
	 
	 
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="ORDER BY KEBIJAKAN_ID ASC")
	{
		$str = "SELECT 
					KEBIJAKAN_ID, TITLE, TEXT, 
					   CREATED_BY, CREATED_DATE, JENIS
					FROM KEBIJAKAN A
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