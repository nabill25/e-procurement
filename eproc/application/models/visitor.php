<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class Visitor extends Entity{ 

	var $query;

    function __construct()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		$str = "
				INSERT INTO VISITOR (
				   IP, TANGGAL, HITS, 
   					STATUS) 
				VALUES (
				  '".$this->getField("IP")."', 
				  '".$this->getField("TANGGAL")."', 
				  '".$this->getField("HITS")."', 
				  '".$this->getField("STATUS")."'
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function getOnline($time='', $ip='')
	{
		$str = " SELECT 1 TOTAL FROM VISITOR WHERE IP = '" . $ip . "' AND TANGGAL = '" . $time . "' "; 
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("TOTAL"); 
		else 
			return 0; 
    }
	
    function hitsToday($time='')
	{
		$str = " SELECT SUM(HITS) TOTAL FROM VISITOR
				 WHERE TANGGAL = '" . $time . "' GROUP BY TANGGAL "; 
		
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("TOTAL"); 
		else 
			return 0; 
    }
	
	function totalHits()
	{
		$str = " SELECT SUM(HITS) as TOTAL FROM VISITOR "; 
		
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("TOTAL"); 
		else 
			return 0; 
    }

	function countOnline($diff='')
	{
		$str = " SELECT COUNT(*) TOTAL FROM VISITOR WHERE STATUS > " . $diff . " "; 
		
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("TOTAL"); 
		else 
			return 0; 
    }

    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(AANWIJZING_ID) AS ROWCOUNT FROM AANWIJZING WHERE AANWIJZING_ID IS NOT NULL "; 
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

    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(AANWIJZING_ID) AS ROWCOUNT FROM AANWIJZING WHERE AANWIJZING_ID IS NOT NULL "; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }	
  } 
?>