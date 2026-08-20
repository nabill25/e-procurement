<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PanelTahap extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PanelTahap()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PANEL_TAHAP_ID", $this->getNextId("PANEL_TAHAP_ID","PANEL_TAHAP")); 

		$str = "
				INSERT INTO PANEL_TAHAP (
				   PANEL_TAHAP_ID, PANEL_ID, NAMA, 
				   HADIR, TAMPILKAN, TANGGAL_AWAL, 
				   TANGGAL_AKHIR, URUT, JAM_AWAL, JAM_AKHIR) 
				VALUES ('".$this->getField("PANEL_TAHAP_ID")."', '".$this->getField("PANEL_ID")."',
					'".$this->getField("NAMA")."', '".$this->getField("HADIR")."', '".$this->getField("TAMPILKAN")."',
					".$this->getField("TANGGAL_AWAL").", ".$this->getField("TANGGAL_AKHIR").", '".$this->getField("URUT")."', '".$this->getField("JAM_AWAL")."', '".$this->getField("JAM_AKHIR")."')
		"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PANEL_TAHAP SET
				  NAMA = '".$this->getField("NAMA")."'
				WHERE PANEL_TAHAP_ID = ".$this->getField("PANEL_TAHAP_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PANEL_TAHAP
                WHERE 
                  PANEL_ID = ".$this->getField("PANEL_ID").""; 
				  
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
				PANEL_TAHAP_ID, PANEL_ID, NAMA, 
				   HADIR, TAMPILKAN, TANGGAL_AWAL, 
				   TANGGAL_AKHIR, URUT, JAM_AWAL, JAM_AKHIR
				FROM PANEL_TAHAP WHERE 1 = 1
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
		$str = "SELECT PANEL_TAHAP_ID, NAMA
				FROM PANEL_TAHAP WHERE PANEL_TAHAP_ID IS NOT NULL"; 
		
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
		$str = "SELECT JENIS_TAHAP FROM METODE A, PANEL B WHERE 
			A.PANEL_JENIS_ID = B.PANEL_JENIS_ID AND
			A.PANEL_METODE_LELANG_ID = B.PANEL_METODE_LELANG_ID AND 
			A.PANEL_METODE_KUALIFIKASI_ID = B.PANEL_METODE_KUALIFIKASI_ID
			AND B.PANEL_ID = '".$reqId."' "; 
	
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("JENIS_TAHAP"); 
		else 
			return 0; 
    }

    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(PANEL_TAHAP_ID) AS ROWCOUNT FROM PANEL_TAHAP A WHERE PANEL_TAHAP_ID IS NOT NULL ".$statement; 
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
		$str = "SELECT COUNT(PANEL_TAHAP_ID) AS ROWCOUNT FROM PANEL_TAHAP WHERE PANEL_TAHAP_ID IS NOT NULL AND (CURRENT_DATE BETWEEN TANGGAL_AWAL AND TANGGAL_AKHIR OR trim(tanggal_awal) = trim(CURRENT_DATE) or trim(COALESCE(tanggal_akhir,CURRENT_DATE)) = trim(CURRENT_DATE)) ".$statement; 
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
		$str = "SELECT COUNT(PANEL_TAHAP_ID) AS ROWCOUNT FROM PANEL_TAHAP WHERE PANEL_TAHAP_ID IS NOT NULL "; 
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