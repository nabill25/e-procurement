<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class RekananTenagaAhliPendidikan extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function RekananTenagaAhliPendidikan()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_TENAGA_AHLI_PEND_ID", $this->getNextId("REKANAN_TENAGA_AHLI_PEND_ID","REKANAN_TENAGA_AHLI_PEND")); 

		$str = "
				INSERT INTO REKANAN_TENAGA_AHLI_PEND (
					REKANAN_TENAGA_AHLI_PEND_ID, 
					REKANAN_TENAGA_AHLI_ID, 
					PENDIDIKAN, 
					JURUSAN, CREATED_BY, CREATED_DATE)
				VALUES ( '".$this->getField("REKANAN_TENAGA_AHLI_PEND_ID")."', 
					'".$this->getField("REKANAN_TENAGA_AHLI_ID")."',
					'".$this->getField("PENDIDIKAN")."', 
					'".$this->getField("JURUSAN")."',
					".$this->getField("CREATED_BY").",
					CURRENT_TIMESTAMP
					)
		"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_TENAGA_AHLI_PEND 
				SET
					REKANAN_TENAGA_AHLI_ID = '".$this->getField("REKANAN_TENAGA_AHLI_ID")."',
					PENDIDIKAN = '".$this->getField("PENDIDIKAN")."',
					JURUSAN = '".$this->getField("JURUSAN")."',
					UPDATED_BY = ".$this->getField("CREATED_BY").",
					UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE REKANAN_TENAGA_AHLI_PEND_ID = '".$this->getField("REKANAN_TENAGA_AHLI_PEND_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM REKANAN_TENAGA_AHLI_PEND A
                WHERE 
                  REKANAN_TENAGA_AHLI_PEND_ID = '".$this->getField("REKANAN_TENAGA_AHLI_PEND_ID")."' 
				  AND EXISTS(SELECT 1 FROM REKANAN_TENAGA_AHLI X WHERE X.REKANAN_TENAGA_AHLI_ID = A.REKANAN_TENAGA_AHLI_ID 
				  	AND X.REKANAN_ID = '".$this->getField("REKANAN_ID")."')
				  "; 
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
		$str = "
			SELECT 
				REKANAN_TENAGA_AHLI_PEND_ID, 
				REKANAN_TENAGA_AHLI_ID, 
				PENDIDIKAN, 
				JURUSAN
			FROM REKANAN_TENAGA_AHLI_PEND
			WHERE REKANAN_TENAGA_AHLI_PEND_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_TENAGA_AHLI_PEND_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT 
				REKANAN_TENAGA_AHLI_PEND_ID, 
				REKANAN_TENAGA_AHLI_ID, 
				PENDIDIKAN, 
				JURUSAN
			FROM REKANAN_TENAGA_AHLI_PEND
			WHERE REKANAN_TENAGA_AHLI_PEND_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_TENAGA_AHLI_PEND_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{		
		$str = "SELECT COUNT(REKANAN_TENAGA_AHLI_PEND_ID) AS ROWCOUNT FROM REKANAN_TENAGA_AHLI_PEND WHERE REKANAN_TENAGA_AHLI_PEND_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(REKANAN_TENAGA_AHLI_PEND_ID) AS ROWCOUNT FROM REKANAN_TENAGA_AHLI_PEND WHERE REKANAN_TENAGA_AHLI_PEND_ID IS NOT NULL "; 
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