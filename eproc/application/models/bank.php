<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Bank extends Entity 
  { 

	var $query; 

  function __construct(){
  		parent::__construct();
	}
	 
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("BANK_ID", $this->getNextId("BANK_ID","BANK")); 

		$str = "
		INSERT INTO BANK (
		   BANK_ID, NAMA) 
 			 	VALUES (
				  ".$this->getField("BANK_ID").",
  				  '".$this->getField("NAMA")."'
				)"; 
		$this->query = $str;
		$this->id = $this->getField("BANK_ID");
		//echo $str;exit;
		return $this->execQuery($str);
  }
	
	 function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE BANK SET
				  NAMA = '".$this->getField("NAMA")."'
				WHERE BANK_ID = ".$this->getField("BANK_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "DELETE FROM BANK  
				WHERE BANK_ID = ".$this->getField("BANK_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
  }
	
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="ORDER BY KODE ASC")
	{
		$str = "SELECT 
					BANK_ID, NAMA, KODE, 
					   RTGS, SAP_KODE
					FROM BANK A
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
		$str = "SELECT COUNT(BANK_ID) AS ROWCOUNT FROM BANK A
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

  function selectByParamsRekanan($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="ORDER BY REKANAN_BANK_ID ASC")
	{
		$str = "SELECT A.REKANAN_BANK_ID, A.BANK_ID, B.NAMA BANK_NAMA, A.BANK_REKENING, A.BANK_PEMILIK, A.BANK_CABANG, A.CREATED_BY, A.CREATED_DATE, A.UPDATED_BY, A.UPDATED_DATE
					FROM REKANAN_BANK A
					LEFT JOIN BANK B ON A.BANK_ID = B.BANK_ID
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

  function insertBankRekanan()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_BANK_ID", $this->getNextId("REKANAN_BANK_ID","REKANAN_BANK")); 

		$str = "
		INSERT INTO REKANAN_BANK (
		   REKANAN_BANK_ID, BANK_ID, BANK_REKENING, BANK_PEMILIK, REKANAN_ID, BANK_CABANG, CREATED_BY, CREATED_DATE) 
 			 	VALUES (
				  ".$this->getField("REKANAN_BANK_ID").",
  				".$this->getField("BANK_ID").",
  				'".$this->getField("BANK_REKENING")."',
  				'".$this->getField("BANK_PEMILIK")."',
  				".$this->getField("REKANAN_ID").",
  				'".$this->getField("BANK_CABANG")."',
  				".$this->getField("CREATED_BY").",
  				CURRENT_TIMESTAMP
				)"; 
		// echo $str;exit;
		$this->query = $str;
		$this->id = $this->getField("REKANAN_BANK_ID");
		return $this->execQuery($str);
  }

  function delAll()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "DELETE FROM REKANAN_BANK  
						WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."
					 "; 
				// echo $str; die;
				$this->query = $str;
		return $this->execQuery($str);
  }
    
  } 
?>