<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class UnitKerja extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
		parent::__construct();
	}
	
    function UnitKerja()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("UNIT_KERJA_ID", $this->getNextId("UNIT_KERJA_ID","UNIT_KERJA")); 

		$str = "
				INSERT INTO UNIT_KERJA (
					UNIT_KERJA_ID, 
					KODE, 
					NAMA, 
					ALAMAT, 
					LOKASI, 
					TELEPON, 
					FAX, 
					EMAIL)
				VALUES ( '".$this->getField("UNIT_KERJA_ID")."', 
					'".$this->getField("KODE")."',
					'".$this->getField("NAMA")."',
					'".$this->getField("ALAMAT")."',
					'".$this->getField("LOKASI")."',
					'".$this->getField("TELEPON")."',
					'".$this->getField("FAX")."', 
					'".$this->getField("EMAIL")."')
		"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE UNIT_KERJA 
				SET
					KODE = '".$this->getField("KODE")."',
					NAMA = '".$this->getField("NAMA")."',
					ALAMAT = '".$this->getField("ALAMAT")."',
					LOKASI = '".$this->getField("LOKASI")."',
					TELEPON = '".$this->getField("TELEPON")."',
					FAX = '".$this->getField("FAX")."',
					EMAIL = '".$this->getField("EMAIL")."'
				WHERE UNIT_KERJA_ID = '".$this->getField("UNIT_KERJA_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM UNIT_KERJA
                WHERE 
                  UNIT_KERJA_ID = '".$this->getField("UNIT_KERJA_ID")."'"; 
				  
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
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY UNIT_KERJA_ID ASC")
	{
			$str = "SELECT 
						UNIT_KERJA_ID, 
						KODE, 
						NAMA, 
						ALAMAT, 
						LOKASI, 
						TELEPON, 
						FAX, 
						EMAIL
					FROM UNIT_KERJA
					WHERE UNIT_KERJA_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ".$order;
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsDivre($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY UNIT_KERJA_ID ASC")
	{
			$str = "SELECT A.UNIT_KERJA_ID, A.NAMA, A.ALAMAT, COALESCE(B.JUMLAH, 0) JUMLAH
					FROM UNIT_KERJA A
					LEFT JOIN (SELECT UNIT_KERJA_ID, COUNT(PAKET_ID) JUMLAH 
					FROM PAKET WHERE PUBLISH_PAKET=1
					GROUP BY UNIT_KERJA_ID) B ON A.UNIT_KERJA_ID = B.UNIT_KERJA_ID	
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ".$order;
				
		return $this->selectLimit($str,$limit,$from); 
    }
		
	function selectByParamsMonitoringPic($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT   A.UNIT_KERJA_ID, A.NAMA UNIT_KERJA, COUNT(B.UNIT_KERJA_PIC_ID) JUMLAH
				FROM UNIT_KERJA A LEFT JOIN UNIT_KERJA_PIC B
					 ON A.UNIT_KERJA_ID = B.UNIT_KERJA_ID
			   WHERE A.UNIT_KERJA_ID IS NOT NULL
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement."GROUP BY A.UNIT_KERJA_ID, A.NAMA ORDER BY A.UNIT_KERJA_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT 
				UNIT_KERJA_ID, 
				KODE, 
				NAMA, 
				ALAMAT, 
				LOKASI, 
				TELEPON, 
				FAX, 
				EMAIL
			FROM UNIT_KERJA
			WHERE UNIT_KERJA_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY UNIT_KERJA_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{		
		$str = "SELECT COUNT(UNIT_KERJA_ID) AS ROWCOUNT FROM UNIT_KERJA WHERE UNIT_KERJA_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(UNIT_KERJA_ID) AS ROWCOUNT FROM UNIT_KERJA WHERE UNIT_KERJA_ID IS NOT NULL "; 
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