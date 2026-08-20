<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class EvaluasiAdmin extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function EvaluasiAdmin()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		//$this->setField("EVALUASI_NUMBER", $this->getNextId("EVALUASI_NUMBER","EVAL_ADMIN")); 

		$str = "INSERT INTO EVAL_ADMIN (
				   EVALUASI_NUMBER, NAMA, TIPE) 
				VALUES (
				  ".$this->getField("EVALUASI_NUMBER").",
				  '".$this->getField("NAMA")."',
				  '".$this->getField("TIPE")."'
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE EVAL_ADMIN SET
				  NAMA= '".$this->getField("NAMA")."',
				  TIPE= '".$this->getField("TIPE")."'
				WHERE EVALUASI_NUMBER = '".$this->getField("EVALUASI_NUMBER")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM EVAL_ADMIN
                WHERE 
                  EVALUASI_NUMBER = '".$this->getField("EVALUASI_NUMBER")."'"; 
				  
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
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $sOrder="ORDER BY NAMA ASC")
	{
		$str = "SELECT EVALUASI_NUMBER, NAMA, TIPE
				FROM EVAL_ADMIN WHERE EVALUASI_NUMBER IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$sOrder;
		$this->query = $str;
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT EVALUASI_NUMBER, NAMA, TIPE
				FROM EVAL_ADMIN WHERE EVALUASI_NUMBER IS NOT NULL"; 
		
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
		$str = "SELECT COUNT(1) AS ROWCOUNT FROM EVAL_ADMIN WHERE EVALUASI_NUMBER IS NOT NULL "; 
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
		$str = "SELECT COUNT(1) AS ROWCOUNT FROM EVAL_ADMIN WHERE EVALUASI_NUMBER IS NOT NULL "; 
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
	
	function getMaxData($paramsArray=array(), $varStatement="")
	{
		$str = "SELECT COALESCE(MAX(EVALUASI_NUMBER),0) + 1 AS ROWCOUNT FROM EVAL_ADMIN WHERE 1=1 "; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->select($str); 
		$this->query = $str;
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }
	
  } 
?>