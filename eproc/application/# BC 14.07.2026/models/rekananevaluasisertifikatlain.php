<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class RekananEvaluasiSertifikatLain extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function RekananEvaluasiSertifikatLain()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_EVAL_SERTIFIKAT_ID", $this->getNextId("REKANAN_EVAL_SERTIFIKAT_ID","REKANAN_EVAL_SERTIFIKAT_LAIN")); 

		$str = "INSERT INTO REKANAN_EVAL_SERTIFIKAT_LAIN (
				    REKANAN_EVAL_SERTIFIKAT_ID, PAKET_EVAL_SERTIFIKAT_LAIN_ID, PAKET_REKANAN_ID, 
   					REKANAN_SERTIFIKAT_ID) 
				VALUES (
				  ".$this->getField("REKANAN_EVAL_SERTIFIKAT_ID").",
				  ".$this->getField("PAKET_EVAL_SERTIFIKAT_LAIN_ID").",
				  ".$this->getField("PAKET_REKANAN_ID").",
				  '".$this->getField("REKANAN_SERTIFIKAT_ID")."'
				)"; 
				
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_EVAL_SERTIFIKAT_LAIN SET
				  REKANAN_EVAL_SERTIFIKAT_ID = '".$this->getField("REKANAN_EVAL_SERTIFIKAT_ID")."'
				WHERE REKANAN_EVAL_SERTIFIKAT_ID = '".$this->getField("REKANAN_EVAL_SERTIFIKAT_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updatePenilaian()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_EVAL_SERTIFIKAT_LAIN SET
				  KESESUAIAN = '".$this->getField("KESESUAIAN")."',
				  KESESUAIAN_NILAI = '".$this->getField("KESESUAIAN_NILAI")."'
				WHERE REKANAN_EVAL_SERTIFIKAT_ID = '".$this->getField("REKANAN_EVAL_SERTIFIKAT_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updatePenilaianTotal()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_EVAL_SERTIFIKAT_LAIN SET
				  KESESUAIAN_TOTAL = ".$this->getField("KESESUAIAN_TOTAL")."
				WHERE PAKET_EVAL_SERTIFIKAT_LAIN_ID = '".$this->getField("PAKET_EVAL_SERTIFIKAT_LAIN_ID")."' AND
					  PAKET_REKANAN_ID = '".$this->getField("PAKET_REKANAN_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
		
    function updateByField()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_EVAL_SERTIFIKAT_LAIN A SET
				  ".$this->getField("FIELD")." = ".$this->getField("FIELD_VALUE")."
				WHERE PAKET_REKANAN_ID = ".$this->getField("PAKET_REKANAN_ID")."
				"; 
				$this->query = $str;

		return $this->execQuery($str);
    }	
		
	function delete()
	{
        $str = "DELETE FROM REKANAN_EVAL_SERTIFIKAT_LAIN
                WHERE 
                  PAKET_REKANAN_ID = '".$this->getField("PAKET_REKANAN_ID")."'"; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","REKANAN_EVAL_SERTIFIKAT_ID"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT REKANAN_EVAL_SERTIFIKAT_ID, PAKET_EVAL_SERTIFIKAT_LAIN_ID, PAKET_REKANAN_ID, A.REKANAN_SERTIFIKAT_ID, 
							B.NAMA SERTIFIKAT, A.NILAI, KESESUAIAN, COALESCE(KESESUAIAN_NILAI, 0) KESESUAIAN_NILAI, KESESUAIAN_TOTAL 
						FROM REKANAN_EVAL_SERTIFIKAT_LAIN A INNER JOIN  
							REKANAN_SERTIFIKAT B ON A.REKANAN_SERTIFIKAT_ID=B.REKANAN_SERTIFIKAT_ID
						WHERE REKANAN_EVAL_SERTIFIKAT_ID IS NOT NULL "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_EVAL_SERTIFIKAT_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
	
	function selectByParamsKualifikasi($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT REKANAN_EVAL_SERTIFIKAT_ID, PAKET_EVAL_SERTIFIKAT_LAIN_ID, PAKET_REKANAN_ID, A.REKANAN_SERTIFIKAT_ID, 
							B.NAMA SERTIFIKAT, A.NILAI, KESESUAIAN, COALESCE(KESESUAIAN_NILAI, 0) KESESUAIAN_NILAI, KESESUAIAN_TOTAL 
						FROM REKANAN_EVAL_SERTIFIKAT_LAIN A LEFT JOIN  
							REKANAN_SERTIFIKAT B ON A.REKANAN_SERTIFIKAT_ID=B.REKANAN_SERTIFIKAT_ID
						WHERE REKANAN_EVAL_SERTIFIKAT_ID IS NOT NULL "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_EVAL_SERTIFIKAT_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT REKANAN_EVAL_SERTIFIKAT_ID, REKANAN_EVAL_SERTIFIKAT_ID
				FROM REKANAN_EVAL_SERTIFIKAT_LAIN WHERE REKANAN_EVAL_SERTIFIKAT_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_EVAL_SERTIFIKAT_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","REKANAN_EVAL_SERTIFIKAT_ID"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(REKANAN_EVAL_SERTIFIKAT_ID) AS ROWCOUNT FROM REKANAN_EVAL_SERTIFIKAT_LAIN WHERE REKANAN_EVAL_SERTIFIKAT_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(REKANAN_EVAL_SERTIFIKAT_ID) AS ROWCOUNT FROM REKANAN_EVAL_SERTIFIKAT_LAIN WHERE REKANAN_EVAL_SERTIFIKAT_ID IS NOT NULL "; 
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