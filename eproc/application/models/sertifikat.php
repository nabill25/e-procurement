<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Sertifikat extends Entity{ 

	var $query; 

  function __construct(){
  		parent::__construct();
	}
	 
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_SERTIFIKAT_JENIS_ID", $this->getNextId("REKANAN_SERTIFIKAT_JENIS_ID","REKANAN_SERTIFIKAT_JENIS")); 

		$str = "
		INSERT INTO REKANAN_SERTIFIKAT_JENIS (
		   REKANAN_SERTIFIKAT_JENIS_ID, NAMA, ALIAS, CREATED_BY, CREATED_DATE) 
 			 	VALUES (
				  ".$this->getField("REKANAN_SERTIFIKAT_JENIS_ID").",
  				'".$this->getField("NAMA")."',
  				'".$this->getField("ALIAS")."',
          ".$this->getField("CREATED_BY").",
          CURRENT_TIMESTAMP
				)"; 
		$this->query = $str;
		$this->id = $this->getField("REKANAN_SERTIFIKAT_JENIS_ID");
		//echo $str;exit;
		return $this->execQuery($str);
    }
	
	 function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_SERTIFIKAT_JENIS SET
				  NAMA = '".$this->getField("NAMA")."',
				  ALIAS = '".$this->getField("ALIAS")."',
				  UPDATED_BY = ".$this->getField("CREATED_BY").",
				  UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE REKANAN_SERTIFIKAT_JENIS_ID = ".$this->getField("REKANAN_SERTIFIKAT_JENIS_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "DELETE FROM REKANAN_SERTIFIKAT_JENIS  
				WHERE REKANAN_SERTIFIKAT_JENIS_ID = ".$this->getField("REKANAN_SERTIFIKAT_JENIS_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="ORDER BY REKANAN_SERTIFIKAT_JENIS_ID ASC")
	{
		$str = "SELECT 
					REKANAN_SERTIFIKAT_JENIS_ID, NAMA, ALIAS, 
					   KETERANGAN, PATH_FILE, CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
					FROM REKANAN_SERTIFIKAT_JENIS A
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
		$str = "SELECT COUNT(REKANAN_SERTIFIKAT_JENIS_ID) AS ROWCOUNT FROM REKANAN_SERTIFIKAT_JENIS A
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