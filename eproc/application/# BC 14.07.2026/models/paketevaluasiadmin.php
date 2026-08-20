<?php 
 /**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
   include_once('entity.php');

  class PaketEvaluasiAdmin extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PaketEvaluasiAdmin()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_EVAL_ADMIN_ID", $this->getNextId("PAKET_EVAL_ADMIN_ID","PAKET_EVAL_ADMIN")); 

		$str = "INSERT INTO PAKET_EVAL_ADMIN (
				   PAKET_EVAL_ADMIN_ID, PAKET_ID, NAMA, EVALUASI_NUMBER) 
				VALUES (
				  ".$this->getField("PAKET_EVAL_ADMIN_ID").",
				  ".$this->getField("PAKET_ID").",
				  '".$this->getField("NAMA")."',
				  '".$this->getField("EVALUASI_NUMBER")."'
				)"; 
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_EVAL_ADMIN SET
				  NAMA = '".$this->getField("NAMA")."'
				WHERE PAKET_EVAL_ADMIN = '".$this->getField("AGAMA_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PAKET_EVAL_ADMIN
                WHERE 
                  PAKET_ID = '".$this->getField("PAKET_ID")."'"; 
			  
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
		$str = "SELECT A.EVALUASI_NUMBER, 
					   A.NAMA, 
						CASE WHEN B.EVALUASI_NUMBER = NULL THEN 0
						 ELSE 1 END STATUS, A.TIPE 
				FROM 
					EVAL_ADMIN A LEFT JOIN PAKET_EVAL_ADMIN B ON A.EVALUASI_NUMBER = B.EVALUASI_NUMBER "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY A.EVALUASI_NUMBER ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
	
    function selectByParamsProses($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PAKET_EVAL_ADMIN_ID, NAMA, EVALUASI_NUMBER FROM PAKET_EVAL_ADMIN A WHERE 1 = 1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY EVALUASI_NUMBER ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }	

    function selectByParamsProsesV2($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PAKET_EVAL_ADMIN_ID, B.NAMA, COALESCE(B.EVALUASI_NUMBER, 0) EVALUASI_NUMBER FROM EVAL_ADMIN A 
				LEFT JOIN PAKET_EVAL_ADMIN B ON A.EVALUASI_NUMBER = B.EVALUASI_NUMBER  "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement."  WHERE 1 = 1 ORDER BY EVALUASI_NUMBER ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }	
	    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT AGAMA_ID, NAMA
				FROM AGAMA WHERE AGAMA_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY NAMA ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(PAKET_EVAL_ADMIN_ID) AS ROWCOUNT FROM PAKET_EVAL_ADMIN WHERE PAKET_EVAL_ADMIN_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(AGAMA_ID) AS ROWCOUNT FROM AGAMA WHERE AGAMA_ID IS NOT NULL "; 
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