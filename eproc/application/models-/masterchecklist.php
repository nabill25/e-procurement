<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

include_once('entity.php');

class Masterchecklist extends Entity{ 

	var $query;

    function __construct(){
	  parent::__construct();
	}  
 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY WAJIB ASC ", $reqPerId)
	{
		$str = "SELECT A.MASTER_CHECKLIST_ID, A.NAMA, A.PAKET_JENIS, A.METODE_PEMILIHAN, A.WAJIB,
				(SELECT AA.APPROVED FROM PERMOHONAN_PAKET_CHECKLIST AA WHERE AA.MASTER_CHECKLIST_ID = A.MASTER_CHECKLIST_ID AND AA.PERMOHONAN_PAKET_ID = '".$reqPerId."')
				FROM
					MASTER_CHECKLIST
					A LEFT JOIN PERMOHONAN_PAKET_CHECKLIST B ON A.MASTER_CHECKLIST_ID = B.MASTER_CHECKLIST_ID  
				WHERE
					A.MASTER_CHECKLIST_ID IS NOT NULL  "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= $statement." GROUP BY A.MASTER_CHECKLIST_ID, A.NAMA, A.PAKET_JENIS, A.METODE_PEMILIHAN, A.WAJIB ORDER BY A.WAJIB DESC".$order;
		$this->query = $str;
			
				
		return $this->selectLimit($str,$limit,$from); 
    }

   
    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(A.MASTER_CHECKLIST_ID) AS ROWCOUNT 
					FROM    MASTER_CHECKLIST A
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