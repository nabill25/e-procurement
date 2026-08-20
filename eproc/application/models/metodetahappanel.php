<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class MetodeTahapPanel extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function MetodeTahapPanel()
	{
      $this->Entity(); 
    }
	
	
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("URUT", $this->getNextId("URUT","METODE_TAHAP_PANEL")); 

		$str = "
				INSERT INTO METODE_TAHAP_PANEL (
				   NAMA, 
				   URUT, 
				   HADIR) 
				VALUES ( 
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
		$str = "UPDATE METODE_TAHAP_PANEL 
				SET
					NAMA = '".$this->getField("NAMA")."',
					HADIR = '".$this->getField("HADIR")."'
				WHERE URUT = '".$this->getField("URUT")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM METODE_TAHAP_PANEL
                WHERE 
                  URUT = '".$this->getField("URUT")."'"; 
				  
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
				NAMA, 
				URUT, 
				HADIR
			FROM METODE_TAHAP_PANEL A
			WHERE URUT IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY URUT ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsJadwal($paramsArray=array(),$limit=-1,$from=-1, $statement="", $panel_id="")
	{
		$str = "
				SELECT A.URUT, A.NAMA, A.HADIR, B.HADIR HADIR_CENTANG, B.TAMPILKAN TAMPILKAN_CENTANG, 
				TO_CHAR(B.TANGGAL_AWAL, 'YYYY-MM-DD HH24:MI') TANGGAL_AWAL, TO_CHAR(B.TANGGAL_AKHIR, 'YYYY-MM-DD HH24:MI') TANGGAL_AKHIR, JAM_AWAL, JAM_AKHIR
	 	 		FROM METODE_TAHAP_PANEL A 
				LEFT JOIN PANEL_TAHAP B ON A.NAMA = B.NAMA AND B.PANEL_ID = ".$panel_id."
				WHERE 1 = 1
			  "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY A.URUT ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT 
				NAMA, 
				URUT, 
				HADIR
			FROM METODE_TAHAP_PANEL
			WHERE URUT IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY URUT ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{		
		$str = "SELECT COUNT(URUT) AS ROWCOUNT FROM METODE_TAHAP_PANEL WHERE URUT IS NOT NULL "; 
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
		$str = "SELECT COUNT(URUT) AS ROWCOUNT FROM METODE_TAHAP_PANEL WHERE URUT IS NOT NULL "; 
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