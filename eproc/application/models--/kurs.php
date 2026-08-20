<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Kurs extends Entity{ 

	var $query;
     
    function Kurs()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("KURS_ID", $this->getNextId("KURS_ID","KURS")); 

		$str = "
		INSERT INTO  KURS (
		   KURS_ID, MATA_UANG_ID, BULAN_TAHUN, KURS ) 
 			 	VALUES (
				  ".$this->getField("KURS_ID").",
  				  ".$this->getField("MATA_UANG_ID").",
			  	  '".$this->getField("BULAN_TAHUN")."',
				  ".$this->getField("KURS")." 			 
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE KURS SET
				  KURS = ".$this->getField("KURS")." 
				WHERE MATA_UANG_ID = ".$this->getField("MATA_UANG_ID")." AND BULAN_TAHUN = '".$this->getField("BULAN_TAHUN")."'
				"; 
				$this->query = $str;
			//echo $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM KURS
                WHERE 
                  MATA_UANG_ID = ".$this->getField("MATA_UANG_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }
	
	function delete_sblm_simpan($stat = '')
	{
        $str = "DELETE FROM KURS
                WHERE 
                  KURS_ID = ".$this->getField("KURS_ID").""; 
				  
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
		$str = "SELECT KURS_ID, MATA_UANG_ID, BULAN_TAHUN, KURS 
				FROM KURS WHERE KURS_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY BULAN_TAHUN DESC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsKursUang($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT 
				A.MATA_UANG_ID, KURS_ID, KODE, NAMA, KURS, BULAN_TAHUN, TO_NUMBER(SUBSTR(BULAN_TAHUN,0 , 2)) BULAN, SUBSTR(BULAN_TAHUN,3 , 6) TAHUN
				FROM MATA_UANG A
				LEFT JOIN KURS B ON A.MATA_UANG_ID = B.MATA_UANG_ID
				 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= " WHERE 1 = 1 ".$statement." ORDER BY BULAN_TAHUN DESC";


				
		return $this->selectLimit($str,$limit,$from); 
    }
	
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT KURS_ID, MATA_UANG_ID, BULAN_TAHUN, KURS  
				FROM KURS WHERE KURS_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY BULAN_TAHUN DESC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(KURS_ID) AS ROWCOUNT FROM KURS WHERE KURS_ID IS NOT NULL "; 
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

    function getMataUangId($paramsArray=array())
	{
		$str = "SELECT MATA_UANG_ID AS ROWCOUNT FROM MATA_UANG WHERE MATA_UANG_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(KURS_ID) AS ROWCOUNT FROM KURS WHERE KURS_ID IS NOT NULL "; 
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