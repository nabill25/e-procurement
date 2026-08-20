<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

include_once('entity.php');

class Permohonanpaketchecklist extends Entity{ 

	var $query;

    function __construct(){
	  parent::__construct();
	}  

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PERMOHONAN_PAKET_CHECKLIST_ID", $this->getNextId("PERMOHONAN_PAKET_CHECKLIST_ID","PERMOHONAN_PAKET_CHECKLIST")); 

		$str = "
		INSERT INTO PERMOHONAN_PAKET_CHECKLIST (
		   PERMOHONAN_PAKET_CHECKLIST_ID, PERMOHONAN_PAKET_ID,MASTER_CHECKLIST_ID,APPROVED,CREATED_BY,CREATED_DATE) 
 			 	VALUES (
				  ".$this->getField("PERMOHONAN_PAKET_CHECKLIST_ID").",
  				  ".$this->getField("PERMOHONAN_PAKET_ID").",
  				  ".$this->getField("MASTER_CHECKLIST_ID").",
  				  '".$this->getField("APPROVED")."',
  				  ".$this->getField("CREATED_BY").",
  				  CURRENT_TIMESTAMP
				)"; 
		$this->query = $str;
		$this->id = $this->getField("PERMOHONAN_PAKET_CHECKLIST_ID");
		//echo $str;exit;
		return $this->execQuery($str);
  }
	
	 function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PERMOHONAN_PAKET_CHECKLIST SET
				  APPROVED = '".$this->getField("APPROVED")."',
				  UPDATED_BY = ".$this->getField("CREATED_BY").",
				  UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE PERMOHONAN_PAKET_ID = ".$this->getField("PERMOHONAN_PAKET_ID")." AND MASTER_CHECKLIST_ID = ".$this->getField("MASTER_CHECKLIST_ID")." 
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY PERMOHONAN_PAKET_CHECKLIST_ID ASC ")
	{
		$str = "SELECT 
				PERMOHONAN_PAKET_CHECKLIST_ID, PERMOHONAN_PAKET_ID, MASTER_CHECKLIST_ID, 
		   		APPROVED
				FROM PERMOHONAN_PAKET_CHECKLIST
			    WHERE PERMOHONAN_PAKET_CHECKLIST_ID IS NOT NULL "; 
		
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
		$str = "SELECT COUNT(A.PERMOHONAN_PAKET_CHECKLIST_ID) AS ROWCOUNT 
					FROM    PERMOHONAN_PAKET_CHECKLIST A
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