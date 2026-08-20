<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class MetodeTahap extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function MetodeTahap()
	{
      $this->Entity(); 
    }
	
	
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("METODE_TAHAP_ID", $this->getNextId("METODE_TAHAP_ID","METODE_TAHAP")); 

		$str = "
				INSERT INTO METODE_TAHAP (
				   METODE_TAHAP_ID, 
				   JENIS_TAHAP, 
				   NAMA, 
				   URUT, 
				   HADIR) 
				VALUES ( '".$this->getField("METODE_TAHAP_ID")."', 
					'".$this->getField("JENIS_TAHAP")."',
					'".$this->getField("NAMA")."', 
					'".$this->getField("URUT")."',
					'".$this->getField("HADIR")."')
		"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE METODE_TAHAP 
				SET
					JENIS_TAHAP = '".$this->getField("JENIS_TAHAP")."',
					NAMA = '".$this->getField("NAMA")."',
					URUT = '".$this->getField("URUT")."',
					HADIR = '".$this->getField("HADIR")."'
				WHERE METODE_TAHAP_ID = '".$this->getField("METODE_TAHAP_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM METODE_TAHAP
                WHERE 
                  METODE_TAHAP_ID = '".$this->getField("METODE_TAHAP_ID")."'"; 
				  
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
				METODE_TAHAP_ID, 
				JENIS_TAHAP, 
				NAMA, 
				URUT, 
				HADIR,
				CEK_TANGGAL_MERAH
			FROM METODE_TAHAP
			WHERE METODE_TAHAP_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die;
		$this->query = $str;
		$str .= $statement." ORDER BY METODE_TAHAP_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT 
				METODE_TAHAP_ID, 
				JENIS_TAHAP, 
				NAMA, 
				URUT, 
				HADIR
			FROM METODE_TAHAP
			WHERE METODE_TAHAP_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY METODE_TAHAP_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{		
		$str = "SELECT COUNT(METODE_TAHAP_ID) AS ROWCOUNT FROM METODE_TAHAP WHERE METODE_TAHAP_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(METODE_TAHAP_ID) AS ROWCOUNT FROM METODE_TAHAP WHERE METODE_TAHAP_ID IS NOT NULL "; 
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