<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Key extends Entity{ 

	var $query;

    function __construct(){
  		parent::__construct();
	} 

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("ID", $this->getNextId("ID","KEY_REQUEST")); 

		$str = "
		INSERT INTO KEY_REQUEST (
		   ID, KEY_ID, KEY, URL_API, REQ_DATE, IP, BROWSER, DATA, MODULE_API) 
 			 	VALUES (
				  '".$this->getField("ID")."',
				  '".$this->getField("KEY_ID")."',
				  '".$this->getField("KEY")."',
				  '".$this->getField("URL_API")."',
				  '".$this->getField("REQ_DATE")."',
				  '".$this->getField("IP")."',
				  '".$this->getField("BROWSER")."',
				  '".$this->getField("DATA")."',
  				  '".$this->getField("MODULE_API")."'
				)"; 
		$this->query = $str;
		$this->id = $this->getField("ID");
		// echo $str;exit;
		return $this->execQuery($str);
    }
	
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="")
	{
		$str = "SELECT A.* FROM KEY A WHERE 1 = 1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ".$order;
				
		return $this->selectLimit($str,$limit,$from); 
    }
	
	function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(A.ID) AS ROWCOUNT FROM KEY A
				WHERE 1 = 1 "; 
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