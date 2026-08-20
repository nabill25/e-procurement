<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Analisapasar extends Entity{ 

	var $query;

    function __construct(){
  		parent::__construct();
	}
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("ANALISA_PASAR_ID", $this->getNextId("ANALISA_PASAR_ID","ANALISA_PASAR")); 

		$str = "
		INSERT INTO ANALISA_PASAR (
		   ANALISA_PASAR_ID, AP_NAMA, CREATED_BY, CREATED_DATE, AKTIF) 
 			 	VALUES (
				  ".$this->getField("ANALISA_PASAR_ID").",
  				  '".$this->getField("AP_NAMA")."',
  				  '".$this->getField("CREATED_BY")."',
  				  '".$this->getField("CREATED_DATE")."',
  				  '".$this->getField("AKTIF")."'
				)"; 
		$this->query = $str;
		$this->id = $this->getField("ANALISA_PASAR_ID");
		//echo $str;exit;
		return $this->execQuery($str);
    }
	
	 function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE ANALISA_PASAR SET
				  AP_NAMA = '".$this->getField("AP_NAMA")."', 
				  CREATED_DATE = '".$this->getField("CREATED_DATE")."' 
				WHERE ANALISA_PASAR_ID = ".$this->getField("ANALISA_PASAR_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "DELETE FROM ANALISA_PASAR  
				WHERE ANALISA_PASAR_ID = ".$this->getField("ANALISA_PASAR_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="ORDER BY A.ANALISA_PASAR_ID ASC")
	{
		$str = "SELECT A.* FROM ANALISA_PASAR A
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
		$str = "SELECT COUNT(ANALISA_PASAR_ID) AS ROWCOUNT FROM ANALISA_PASAR A
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