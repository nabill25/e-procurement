<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Direktorat extends Entity{ 

	var $query;

  function __construct(){
  		parent::__construct();
	}
	 
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("DIREKTORAT_ID", $this->getNextId("DIREKTORAT_ID","DIREKTORAT")); 

		$str = "
		INSERT INTO DIREKTORAT (
		   DIREKTORAT_ID, KODE, NAMA, KETERANGAN, CREATED_BY, CREATED_DATE) 
 			 	VALUES (
				  ".$this->getField("DIREKTORAT_ID").",
  				  '".$this->getField("KODE")."',
  				  '".$this->getField("NAMA")."',
  				  '".$this->getField("KETERANGAN")."',
  				  '".$this->getField("CREATED_BY")."'
  				  CURRENT_TIMESTAMP
				)"; 
		$this->query = $str;
		$this->id = $this->getField("DIREKTORAT_ID");
		//echo $str;exit;
		return $this->execQuery($str);
    }
	
	 function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE DIREKTORAT SET
				  KODE = '".$this->getField("KODE")."',
				  NAMA = '".$this->getField("NAMA")."',
				  KETERANGAN = '".$this->getField("KETERANGAN")."'
				WHERE DIREKTORAT_ID = ".$this->getField("DIREKTORAT_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "DELETE FROM DIREKTORAT  
				WHERE DIREKTORAT_ID = ".$this->getField("DIREKTORAT_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="ORDER BY KODE ASC")
	{
		$str = "SELECT DIREKTORAT_ID, KODE, NAMA, KETERANGAN 
						FROM DIREKTORAT A 
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
	
	function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(DIREKTORAT_ID) AS ROWCOUNT FROM DIREKTORAT A
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