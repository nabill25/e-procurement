<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketReschedule extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
		parent::__construct();
	}
	
    function PaketReschedule()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_RESCHEDULE_ID", $this->getNextId("PAKET_RESCHEDULE_ID","PAKET_RESCHEDULE")); 

		$str = "
		INSERT INTO PAKET_RESCHEDULE (
   			        PAKET_RESCHEDULE_ID, NAMA) 
			 	VALUES (
				  ".$this->getField("PAKET_RESCHEDULE_ID").",
				  '".$this->getField("NAMA")."'
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_RESCHEDULE SET
				  NAMA = '".$this->getField("NAMA")."'
				WHERE PAKET_RESCHEDULE_ID = ".$this->getField("PAKET_RESCHEDULE_ID")."
				"; 
				$this->query = $str;
				
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PAKET_RESCHEDULE
                WHERE 
                  PAKET_RESCHEDULE_ID = ".$this->getField("PAKET_RESCHEDULE_ID").""; 
				  
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
				SELECT PAKET_RESCHEDULE_ID, PAKET_TAHAP_ID, PAKET_ID, NAMA, HADIR, TAMPILKAN, 
					   TO_CHAR(TANGGAL_AWAL, 'YYYY-MM-DD HH24:MI') TANGGAL_AWAL, TO_CHAR(TANGGAL_AKHIR, 'YYYY-MM-DD HH24:MI') TANGGAL_AKHIR, 
					   URUT, JAM_AWAL, JAM_AKHIR, TO_CHAR(TANGGAL_AWAL_BARU, 'YYYY-MM-DD HH24:MI') TANGGAL_AWAL_BARU, 
					   TO_CHAR(TANGGAL_AKHIR_BARU, 'YYYY-MM-DD HH24:MI') TANGGAL_AKHIR_BARU, JAM_AWAL_BARU, JAM_AKHIR_BARU, RESCHEDULE_KE
				  FROM PAKET_RESCHEDULE A
				  WHERE 1 = 1
			  "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY URUT ASC";
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsTahapan($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				SELECT 
				A.PAKET_RESCHEDULE_ID, A.PAKET_ID, A.NAMA,  B.NAMA PAKET, 
				   HADIR, TAMPILKAN, TANGGAL_AWAL, 
				   TO_CHAR(TANGGAL_AKHIR, 'DD-MM-YYYY') TANGGAL_AKHIR, URUT, JAM_AWAL, JAM_AKHIR, TO_CHAR(TANGGAL_AWAL, 'DD-MM-YYYY') TANGGAL_AWAL
				FROM PAKET_RESCHEDULE A INNER JOIN PAKET B ON A.PAKET_ID = B.PAKET_ID WHERE 1 = 1
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
				   PAKET_RESCHEDULE_ID, PAKET_ID, NAMA, 
						   HADIR, TAMPILKAN, TO_CHAR(TANGGAL_AWAL, 'YYYY-MM-DD') TANGGAL_AWAL, 
						   TO_CHAR(TANGGAL_AKHIR, 'YYYY-MM-DD') TANGGAL_AKHIR, URUT, JAM_AWAL, JAM_AKHIR, TO_CHAR(TANGGAL_AWAL, 'DD-MM-YYYY HH24:MI') JAM_BUKA,
						   CASE WHEN COALESCE(NULLIF(JAM_AWAL,''), 'X') = 'X' THEN 
					CASE WHEN (CURRENT_DATE BETWEEN TANGGAL_AWAL 
							AND 
							COALESCE(TANGGAL_AKHIR, 
									TO_DATE(TO_CHAR(TANGGAL_AWAL, 'DDMMYYYY') || ' 23:59', 'DDMMYYYY HH24:MI'))) THEN 1 ELSE 0 END 
						   ELSE
					   CASE WHEN (CURRENT_TIMESTAMP BETWEEN TANGGAL_AWAL 
							AND 
							COALESCE(TANGGAL_AKHIR, 
									TO_DATE(TO_CHAR(TANGGAL_AWAL, 'DDMMYYYY') || ' 23:59', 'DDMMYYYY HH24:MI'))) THEN 1 ELSE 0 END 
					END AKTIF
						FROM PAKET_RESCHEDULE WHERE 1 = 1
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
		$str = "SELECT PAKET_RESCHEDULE_ID, NAMA
				FROM PAKET_RESCHEDULE WHERE PAKET_RESCHEDULE_ID IS NOT NULL"; 
		
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
	

    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(PAKET_RESCHEDULE_ID) AS ROWCOUNT FROM PAKET_RESCHEDULE A WHERE PAKET_RESCHEDULE_ID IS NOT NULL ".$statement; 
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

    function getRescheduleKe($paramsArray=array(), $statement='')
	{
		$str = "SELECT RESCHEDULE_KE FROM PAKET_RESCHEDULE_TERAKHIR A WHERE 1 = 1 ".$statement; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->select($str); 
	
		$this->query = $str;
		if($this->firstRow()) 
			return $this->getField("RESCHEDULE_KE"); 
		else 
			return 0; 
    }
		
  } 
?>