<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class EmailRekanan extends Entity{ 

	var $query; 
	
    function __construct(){
  		parent::__construct();
	}
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("EMAIL_REKANAN_ID", $this->getNextId("EMAIL_REKANAN_ID","EMAIL_REKANAN")); 

		$str = "
		INSERT INTO  EMAIL_REKANAN (
		   EMAIL_REKANAN_ID, EMAIL_ID, REKANAN_ID ) 
 			 	VALUES (
				  ".$this->getField("EMAIL_REKANAN_ID").",
  				  ".$this->getField("EMAIL_ID").",
				  ".$this->getField("REKANAN_ID")." 			 
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE EMAIL_REKANAN SET
				  EMAIL_ID = ".$this->getField("EMAIL_ID").",
				  REKANAN_ID = ".$this->getField("REKANAN_ID")." 
				WHERE EMAIL_REKANAN_ID = ".$this->getField("EMAIL_REKANAN_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM EMAIL_REKANAN
                WHERE 
                  EMAIL_REKANAN_ID = ".$this->getField("EMAIL_REKANAN_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT EMAIL_REKANAN_ID, EMAIL_ID, REKANAN_ID 
				FROM EMAIL_REKANAN WHERE EMAIL_REKANAN_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT  EMAIL_REKANAN_ID, EMAIL_ID, REKANAN_ID 
				FROM EMAIL_REKANAN WHERE EMAIL_REKANAN_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(EMAIL_REKANAN_ID) AS ROWCOUNT FROM EMAIL_REKANAN WHERE EMAIL_REKANAN_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(EMAIL_REKANAN_ID) AS ROWCOUNT FROM EMAIL_REKANAN WHERE EMAIL_REKANAN_ID IS NOT NULL "; 
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