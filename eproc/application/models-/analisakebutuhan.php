<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Analisakebutuhan extends Entity{ 

	var $query;

    function __construct(){
  		parent::__construct();
	}
	 
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("ANALISA_KEBUTUHAN_ID", $this->getNextId("ANALISA_KEBUTUHAN_ID","ANALISA_KEBUTUHAN")); 

		$str = "
		INSERT INTO ANALISA_KEBUTUHAN (
		   ANALISA_KEBUTUHAN_ID, AK_NAMA, CREATED_BY, CREATED_DATE, AKTIF) 
 			 	VALUES (
				  ".$this->getField("ANALISA_KEBUTUHAN_ID").",
  				  '".$this->getField("AK_NAMA")."',
  				  '".$this->getField("CREATED_BY")."',
  				  '".$this->getField("CREATED_DATE")."',
  				  '".$this->getField("AKTIF")."'
				)"; 
		$this->query = $str;
		$this->id = $this->getField("ANALISA_KEBUTUHAN_ID");
		//echo $str;exit;
		return $this->execQuery($str);
    }
	
	 function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE ANALISA_KEBUTUHAN SET
				  AK_NAMA = '".$this->getField("AK_NAMA")."', 
				  CREATED_DATE = '".$this->getField("CREATED_DATE")."' 
				WHERE ANALISA_KEBUTUHAN_ID = ".$this->getField("ANALISA_KEBUTUHAN_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "DELETE FROM ANALISA_KEBUTUHAN  
				WHERE ANALISA_KEBUTUHAN_ID = ".$this->getField("ANALISA_KEBUTUHAN_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="ORDER BY A.ANALISA_KEBUTUHAN_ID ASC")
	{
		$str = "SELECT A.* FROM ANALISA_KEBUTUHAN A
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
		$str = "SELECT COUNT(ANALISA_KEBUTUHAN_ID) AS ROWCOUNT FROM ANALISA_KEBUTUHAN A
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