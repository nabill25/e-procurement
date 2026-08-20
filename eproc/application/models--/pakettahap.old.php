<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketTahap extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PaketTahap()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_TAHAP_ID", $this->getNextId("PAKET_TAHAP_ID","PAKET_TAHAP")); 

		$str = "
		INSERT INTO PAKET_TAHAP (
   			        PAKET_TAHAP_ID, NAMA) 
			 	VALUES (
				  ".$this->getField("PAKET_TAHAP_ID").",
				  '".$this->getField("NAMA")."'
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_TAHAP SET
				  NAMA = '".$this->getField("NAMA")."'
				WHERE PAKET_TAHAP_ID = ".$this->getField("PAKET_TAHAP_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PAKET_TAHAP
                WHERE 
                  PAKET_TAHAP_ID = ".$this->getField("PAKET_TAHAP_ID").""; 
				  
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
				PAKET_TAHAP_ID, PAKET_ID, NAMA, 
				   HADIR, TAMPILKAN, TANGGAL_AWAL, 
				   TANGGAL_AKHIR, URUT, JAM_AWAL, JAM_AKHIR
				FROM PAKET_TAHAP WHERE 1 = 1
			  "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY URUT ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PAKET_TAHAP_ID, NAMA
				FROM PAKET_TAHAP WHERE PAKET_TAHAP_ID IS NOT NULL"; 
		
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
	

    function getJenisTahapById($reqId)
	{
		$str = "SELECT JENIS_TAHAP FROM METODE A, PAKET B WHERE 
				A.PAKET_JENIS_ID = B.PAKET_JENIS_ID AND
				A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID AND 
				A.PAKET_METODE_KUALIFIKASI_ID = B.PAKET_METODE_KUALIFIKASI_ID
				AND B.PAKET_ID = '".$reqId."' "; 
	
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("JENIS_TAHAP"); 
		else 
			return 0; 
    }

    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(PAKET_TAHAP_ID) AS ROWCOUNT FROM PAKET_TAHAP WHERE PAKET_TAHAP_ID IS NOT NULL ".$statement; 
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

    function getCountByParamsAktif($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(PAKET_TAHAP_ID) AS ROWCOUNT FROM PAKET_TAHAP WHERE PAKET_TAHAP_ID IS NOT NULL AND (CURRENT_DATE BETWEEN TANGGAL_AWAL AND TANGGAL_AKHIR OR TO_DATE(TANGGAL_AWAL, 'yyyy/mm/dd hh:mi:ss') = TO_DATE(CURRENT_DATE, 'yyyy/mm/dd hh:mi:ss')) ".$statement; 
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
		$str = "SELECT COUNT(PAKET_TAHAP_ID) AS ROWCOUNT FROM PAKET_TAHAP WHERE PAKET_TAHAP_ID IS NOT NULL "; 
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