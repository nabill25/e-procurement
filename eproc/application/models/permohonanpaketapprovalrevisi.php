<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

include_once('entity.php');

class Permohonanpaketapprovalrevisi extends Entity{ 

	var $query;

    function __construct(){
	  parent::__construct();
	}  

	function insert()
	{
		$this->setField("PERMOHONAN_PAKET_APPROVAL_REVISI_ID", $this->getNextId("PERMOHONAN_PAKET_APPROVAL_REVISI_ID","PERMOHONAN_PAKET_APPROVAL_REVISI")); 

		$str = "
		   INSERT INTO PERMOHONAN_PAKET_APPROVAL_REVISI (
		   PERMOHONAN_PAKET_APPROVAL_REVISI_ID, PERMOHONAN_PAKET_ID,CATATAN,FILE,CREATED_BY,CREATED_DATE) 
 			 	VALUES (
				  ".$this->getField("PERMOHONAN_PAKET_APPROVAL_REVISI_ID").",
  				  ".$this->getField("PERMOHONAN_PAKET_ID").",
  				  '".$this->getField("CATATAN")."',
  				  '".$this->getField("FILE")."',
  				  ".$this->getField("CREATED_BY").",
  				  CURRENT_TIMESTAMP
				)"; 
		$this->query = $str;
		$this->id = $this->getField("PERMOHONAN_PAKET_APPROVAL_REVISI_ID");

		if ($this->execQuery($str)) {
			$str2 = "
			   UPDATE PERMOHONAN_PAKET_ANALISA SET APPROVAL = '2', 
			   UPDATED_BY = ".$this->getField("CREATED_BY").",
			   UPDATED_DATE = CURRENT_TIMESTAMP
			   WHERE PERMOHONAN_PAKET_ANALISA_ID = ".$this->getField("PERMOHONAN_PAKET_ANALISA_ID")."
			   "; 
			$this->query = $str2;
			return $this->execQuery($str2);
		}
  	}
	
 // 	function update()
	// {
	// 	/*Auto-generate primary key(s) by next max value (integer) */
	// 	$str = "UPDATE PERMOHONAN_PAKET_APPROVAL_REVISI SET
	// 			  CATATAN = '".$this->getField("CATATAN")."',
	// 			  FILE = '".$this->getField("FILE")."',
	// 			  UPDATED_BY = ".$this->getField("CREATED_BY").",
	// 			  UPDATED_DATE = CURRENT_TIMESTAMP
	// 			WHERE PERMOHONAN_PAKET_ID = ".$this->getField("PERMOHONAN_PAKET_ID")." AND APPROVED_BY = ".$this->getField("CREATED_BY")." 
	// 			"; 
	// 			$this->query = $str;
	// 	return $this->execQuery($str);
 //    }
 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY PERMOHONAN_PAKET_APPROVAL_REVISI_ID ASC ")
	{
		$str = "SELECT 
				A.PERMOHONAN_PAKET_APPROVAL_REVISI_ID, A.PERMOHONAN_PAKET_ID, A.CATATAN, A.FILE, A.CREATED_BY, A.CREATED_DATE, A.UPDATED_BY, A.UPDATED_DATE
				FROM PERMOHONAN_PAKET_APPROVAL_REVISI A
				JOIN USER_LOGIN B ON A.CREATED_BY = B.USER_LOGIN_ID
			    WHERE A.PERMOHONAN_PAKET_APPROVAL_REVISI_ID IS NOT NULL "; 
		
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
		$str = "SELECT COUNT(A.PERMOHONAN_PAKET_APPROVAL_REVISI_ID) AS ROWCOUNT 
					FROM    PERMOHONAN_PAKET_APPROVAL_REVISI A
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