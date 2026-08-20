<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketPaktaIntegritas extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PaketPaktaIntegritas()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */

		$str = "INSERT INTO PAKET_PAKTA_INTEGRITAS (
				   PAKET_ID, USER_LOGIN_ID, KODE, JENIS, CREATED_BY, CREATED_DATE) 
				VALUES (
				  ".$this->getField("PAKET_ID").",
				  '".$this->getField("USER_LOGIN_ID")."',
				  '".$this->getField("KODE")."',
				  '".$this->getField("JENIS")."',
				  ".$this->getField("CREATED_BY").",
				  CURRENT_TIMESTAMP
				)"; 
		$this->query = $str;
		//echo $str;exit;
		return $this->execQuery($str);
    }

	function deletePaktaRekanan()
	{
		/*Auto-generate primary key(s) by next max value (integer) */

		$str = "DELETE FROM PAKET_PAKTA_INTEGRITAS 
				WHERE 
					PAKET_ID = '".$this->getField("PAKET_ID")."' AND
					USER_LOGIN_ID = '".$this->getField("USER_LOGIN_ID")."' AND
					JENIS = 'REKANAN' "; 
		$this->query = $str;
		return $this->execQuery($str);
    }

    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PAKET_ID, USER_LOGIN_ID, KODE, JENIS, KODE_QR
				FROM PAKET_PAKTA_INTEGRITAS WHERE PAKET_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY USER_LOGIN_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","USER_LOGIN_ID"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(PAKET_ID) AS ROWCOUNT FROM PAKET_PAKTA_INTEGRITAS WHERE PAKET_ID IS NOT NULL "; 
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