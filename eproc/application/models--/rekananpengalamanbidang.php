<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class RekananPengalamanBidang extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function RekananPengalamanBidang()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_PENGALAMAN_BIDANG_ID", $this->getNextId("REKANAN_PENGALAMAN_BIDANG_ID","REKANAN_PENGALAMAN_BIDANG")); 

		$str = "
				INSERT INTO REKANAN_PENGALAMAN_BIDANG (
					REKANAN_PENGALAMAN_BIDANG_ID, 
					REKANAN_PENGALAMAN_ID, 
					BIDANG_USAHA_ID,
					CREATED_BY, CREATED_DATE)
				VALUES ( '".$this->getField("REKANAN_PENGALAMAN_BIDANG_ID")."', 
					'".$this->getField("REKANAN_PENGALAMAN_ID")."',
					'".$this->getField("BIDANG_USAHA_ID")."',
					".$this->getField("CREATED_BY").",
					CURRENT_TIMESTAMP
					)
		"; 
				echo $str;
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_PENGALAMAN_BIDANG 
				SET
					REKANAN_PENGALAMAN_ID = '".$this->getField("REKANAN_PENGALAMAN_ID")."',
					BIDANG_USAHA_ID = '".$this->getField("BIDANG_USAHA_ID")."'
				WHERE REKANAN_PENGALAMAN_BIDANG_ID = '".$this->getField("REKANAN_PENGALAMAN_BIDANG_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM REKANAN_PENGALAMAN_BIDANG
                WHERE 
                  REKANAN_PENGALAMAN_BIDANG_ID = '".$this->getField("REKANAN_PENGALAMAN_BIDANG_ID")."'"; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }
	
	function delete_parent()
	{
        $str = "DELETE FROM REKANAN_PENGALAMAN_BIDANG
                WHERE 
                  REKANAN_PENGALAMAN_ID = '".$this->getField("REKANAN_PENGALAMAN_ID")."'"; 
				  
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
				REKANAN_PENGALAMAN_BIDANG_ID, 
				REKANAN_PENGALAMAN_ID, 
				BIDANG_USAHA_ID,
				AMBIL_BIDANG_USAHA_NAMA(BIDANG_USAHA_ID) NAMA
			FROM REKANAN_PENGALAMAN_BIDANG
			WHERE REKANAN_PENGALAMAN_BIDANG_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_PENGALAMAN_BIDANG_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT 
				REKANAN_PENGALAMAN_BIDANG_ID, 
				REKANAN_PENGALAMAN_ID, 
				BIDANG_USAHA_ID
			FROM REKANAN_PENGALAMAN_BIDANG
			WHERE REKANAN_PENGALAMAN_BIDANG_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_PENGALAMAN_BIDANG_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{		
		$str = "SELECT COUNT(REKANAN_PENGALAMAN_BIDANG_ID) AS ROWCOUNT FROM REKANAN_PENGALAMAN_BIDANG WHERE REKANAN_PENGALAMAN_BIDANG_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(REKANAN_PENGALAMAN_BIDANG_ID) AS ROWCOUNT FROM REKANAN_PENGALAMAN_BIDANG WHERE REKANAN_PENGALAMAN_BIDANG_ID IS NOT NULL "; 
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