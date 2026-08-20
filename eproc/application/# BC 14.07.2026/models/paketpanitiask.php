<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketPanitiaSK extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PaketPanitiaSK()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_PANITIA_SK_ID", $this->getNextId("PAKET_PANITIA_SK_ID","PAKET_PANITIA_SK")); 

		$str = "
				INSERT INTO PAKET_PANITIA_SK (
					PAKET_PANITIA_SK_ID, 
					PAKET_ID, 
					NO_SK, 
					TANGGAL_SK, 
					PEJABAT_PENETAP, 
					PEJABAT_PENETAP_NIP, 
					TANGGAL_MULAI, 
					TANGGAL_SELESAI)
				VALUES ( '".$this->getField("PAKET_PANITIA_SK_ID")."', 
					'".$this->getField("PAKET_ID")."',
					'".$this->getField("NO_SK")."', 
					'".$this->getField("TANGGAL_SK")."',
					'".$this->getField("PEJABAT_PENETAP")."',
					'".$this->getField("PEJABAT_PENETAP_NIP")."',
					'".$this->getField("TANGGAL_MULAI")."',
					'".$this->getField("TANGGAL_SELESAI")."')
		"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_PANITIA_SK 
				SET
					PAKET_ID = '".$this->getField("PAKET_ID")."',
					NO_SK = '".$this->getField("NO_SK")."',
					TANGGAL_SK = '".$this->getField("TANGGAL_SK")."',
					PEJABAT_PENETAP = '".$this->getField("PEJABAT_PENETAP")."',
					PEJABAT_PENETAP_NIP = '".$this->getField("PEJABAT_PENETAP_NIP")."',
					TANGGAL_MULAI = '".$this->getField("TANGGAL_MULAI")."',
					TANGGAL_SELESAI = '".$this->getField("TANGGAL_SELESAI")."'
				WHERE PAKET_PANITIA_SK_ID = '".$this->getField("PAKET_PANITIA_SK_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PAKET_PANITIA_SK
                WHERE 
                  PAKET_PANITIA_SK_ID = '".$this->getField("PAKET_PANITIA_SK_ID")."'"; 
				  
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
				PAKET_PANITIA_SK_ID, 
				PAKET_ID, 
				NO_SK, 
				TANGGAL_SK, 
				PEJABAT_PENETAP, 
				PEJABAT_PENETAP_NIP, 
				TANGGAL_MULAI, 
				TANGGAL_SELESAI
			FROM PAKET_PANITIA_SK
			WHERE PAKET_PANITIA_SK_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_PANITIA_SK_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT 
				PAKET_PANITIA_SK_ID, 
				PAKET_ID, 
				NO_SK, 
				TANGGAL_SK, 
				PEJABAT_PENETAP, 
				PEJABAT_PENETAP_NIP, 
				TANGGAL_MULAI, 
				TANGGAL_SELESAI
			FROM PAKET_PANITIA_SK
			WHERE PAKET_PANITIA_SK_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_PANITIA_SK_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{		
		$str = "SELECT COUNT(PAKET_PANITIA_SK_ID) AS ROWCOUNT FROM PAKET_PANITIA_SK WHERE PAKET_PANITIA_SK_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(PAKET_PANITIA_SK_ID) AS ROWCOUNT FROM PAKET_PANITIA_SK WHERE PAKET_PANITIA_SK_ID IS NOT NULL "; 
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