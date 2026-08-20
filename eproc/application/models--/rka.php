<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Rka extends Entity{ 

	var $query;
  function __construct()
	{
		parent::__construct();
  }
	
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY INTEGRATION_IMPORT_RKA_BUDGET_ID DESC ")
	{
		$str = "
					SELECT *
					FROM INTEGRATION_IMPORT_RKA_BUDGET
				    WHERE 1=1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
			
		$str .= $statement." ".$order;
				
		return $this->selectLimit($str,$limit,$from); 
    }

   
    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(A.INTEGRATION_IMPORT_RKA_BUDGET_ID) AS ROWCOUNT 
					FROM INTEGRATION_IMPORT_RKA_BUDGET A
					WHERE 1 = 1".$statement; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }

  } 
?>