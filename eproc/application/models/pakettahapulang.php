<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketTahapUlang extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PaketTahapUlang()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_TAHAP_ULANG_ID", $this->getNextId("PAKET_TAHAP_ULANG_ID","PAKET_TAHAP_ULANG")); 

		$str = "
				INSERT INTO PAKET_TAHAP_ULANG (
				   PAKET_TAHAP_ULANG_ID, PAKET_ID, NAMA, 
				   HADIR, TAMPILKAN, TANGGAL_AWAL, 
				   TANGGAL_AKHIR, URUT, JAM_AWAL, JAM_AKHIR) 
				VALUES ('".$this->getField("PAKET_TAHAP_ULANG_ID")."', '".$this->getField("PAKET_ID")."',
					'".$this->getField("NAMA")."', '".$this->getField("HADIR")."', '".$this->getField("TAMPILKAN")."',
					".$this->getField("TANGGAL_AWAL").", ".$this->getField("TANGGAL_AKHIR").", '".$this->getField("URUT")."', '".$this->getField("JAM_AWAL")."', '".$this->getField("JAM_AKHIR")."')
		"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_TAHAP_ULANG SET
				  NAMA = '".$this->getField("NAMA")."'
				WHERE PAKET_TAHAP_ULANG_ID = ".$this->getField("PAKET_TAHAP_ULANG_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PAKET_TAHAP_ULANG
                WHERE 
                  PAKET_TAHAP_ULANG_ID = ".$this->getField("PAKET_TAHAP_ULANG_ID").""; 
				  
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
				PAKET_TAHAP_ULANG_ID, PAKET_ID, NAMA, 
				   HADIR, TAMPILKAN, TANGGAL_AWAL, 
				   TANGGAL_AKHIR, URUT, JAM_AWAL, JAM_AKHIR, TO_CHAR(TANGGAL_AWAL, 'DD-MM-YYYY HH24:MI') JAM_BUKA
				FROM PAKET_TAHAP_ULANG A WHERE 1 = 1
			  "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY URUT ASC";
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsJadwal($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				SELECT 
                PAKET_TAHAP_ULANG_ID, PAKET_ID, NAMA, 
                   HADIR, TAMPILKAN, TANGGAL_AWAL, 
                   TANGGAL_AKHIR, URUT, JAM_AWAL, JAM_AKHIR, TO_CHAR(TANGGAL_AWAL, 'DD-MM-YYYY HH24:MI') JAM_BUKA,
                   CASE WHEN (CURRENT_DATE BETWEEN TANGGAL_AWAL AND COALESCE(TANGGAL_AKHIR, TO_DATE(TO_CHAR(TANGGAL_AWAL, 'DDMMYYYY') || '23:59', 'DDMMYYYY HH24:MI'))) THEN 1 ELSE 0 END AKTIF
                FROM PAKET_TAHAP_ULANG A WHERE 1 = 1
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
		$str = "SELECT PAKET_TAHAP_ULANG_ID, NAMA
				FROM PAKET_TAHAP_ULANG A WHERE PAKET_TAHAP_ULANG_ID IS NOT NULL"; 
		
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
			A.PAKET_METODE_KUALIFIKASI_ID = B.PAKET_METODE_KUALIFIKASI_ID AND
            A.SISTEM_SAMPUL = B.SISTEM_SAMPUL
			AND B.PAKET_ID = '".$reqId."' "; 
	
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("JENIS_TAHAP"); 
		else 
			return 0; 
    }

    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(PAKET_TAHAP_ULANG_ID) AS ROWCOUNT FROM PAKET_TAHAP_ULANG WHERE PAKET_TAHAP_ULANG_ID IS NOT NULL ".$statement; 
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

    function getCountByParamsAktif($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(PAKET_TAHAP_ULANG_ID) AS ROWCOUNT FROM PAKET_TAHAP_ULANG WHERE PAKET_TAHAP_ULANG_ID IS NOT NULL
				AND (CURRENT_DATE BETWEEN TANGGAL_AWAL AND COALESCE(TANGGAL_AKHIR, TO_DATE(TO_CHAR(TANGGAL_AWAL, 'DDMMYYYY') || '23:59', 'DDMMYYYY HH24:MI')) OR 
					 (tanggal_awal) = (CURRENT_DATE) or 
					 (COALESCE(tanggal_akhir,tanggal_awal)) = (CURRENT_DATE)) 
			   ".$statement; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}	
		$this->query = $str;
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }

    function getCountByParamsBerlalu($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(PAKET_TAHAP_ULANG_ID) AS ROWCOUNT FROM PAKET_TAHAP_ULANG WHERE PAKET_TAHAP_ULANG_ID IS NOT NULL AND (CURRENT_DATE > COALESCE(TANGGAL_AKHIR, TO_DATE(TO_CHAR(TANGGAL_AWAL, 'DDMMYYYY') || '23:59', 'DDMMYYYY HH24:MI'))) ".$statement; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		//echo $str;
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }
		
    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(PAKET_TAHAP_ULANG_ID) AS ROWCOUNT FROM PAKET_TAHAP_ULANG WHERE PAKET_TAHAP_ULANG_ID IS NOT NULL "; 
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