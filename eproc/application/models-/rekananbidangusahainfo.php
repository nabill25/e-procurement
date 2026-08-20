<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class RekananBidangUsahaInfo extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function RekananBidangUsahaInfo()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_BIDANG_USAHA_INFO_ID", $this->getNextId("REKANAN_BIDANG_USAHA_INFO_ID","REKANAN_BIDANG_USAHA_INFO")); 

		$str = "
				INSERT INTO REKANAN_BIDANG_USAHA_INFO (
					REKANAN_BIDANG_USAHA_INFO_ID, 
					REKANAN_BIDANG_USAHA_ID, 
					NO_SBU, 
					TANGGAL, 
					TANGGAL_BERLAKU, 
					PENERBIT)
				VALUES ( '".$this->getField("REKANAN_BIDANG_USAHA_INFO_ID")."', 
					'".$this->getField("REKANAN_BIDANG_USAHA_ID")."',
					'".$this->getField("NO_SBU")."', 
					'".$this->getField("TANGGAL")."', 
					'".$this->getField("TANGGAL_BERLAKU")."', 
					'".$this->getField("PENERBIT")."')
		"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_BIDANG_USAHA_INFO 
				SET
					REKANAN_BIDANG_USAHA_ID = '".$this->getField("REKANAN_BIDANG_USAHA_ID")."',
					NO_SBU = '".$this->getField("NO_SBU")."',
					TANGGAL = '".$this->getField("TANGGAL")."',
					TANGGAL_BERLAKU = '".$this->getField("TANGGAL_BERLAKU")."',
					PENERBIT = '".$this->getField("PENERBIT")."'
				WHERE REKANAN_BIDANG_USAHA_INFO_ID = '".$this->getField("REKANAN_BIDANG_USAHA_INFO_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM REKANAN_BIDANG_USAHA_INFO
                WHERE 
                  REKANAN_BIDANG_USAHA_INFO_ID = '".$this->getField("REKANAN_BIDANG_USAHA_INFO_ID")."'"; 
				  
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
				REKANAN_BIDANG_USAHA_INFO_ID, 
				REKANAN_BIDANG_USAHA_ID, 
				NO_SBU, 
				TANGGAL, 
				TANGGAL_BERLAKU, 
				PENERBIT
			FROM REKANAN_BIDANG_USAHA_INFO
			WHERE REKANAN_BIDANG_USAHA_INFO_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_BIDANG_USAHA_INFO_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT 
				REKANAN_BIDANG_USAHA_INFO_ID, 
				REKANAN_BIDANG_USAHA_ID, 
				NO_SBU, 
				TANGGAL, 
				TANGGAL_BERLAKU, 
				PENERBIT
			FROM REKANAN_BIDANG_USAHA_INFO
			WHERE REKANAN_BIDANG_USAHA_INFO_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_BIDANG_USAHA_INFO_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{		
		$str = "SELECT COUNT(REKANAN_BIDANG_USAHA_INFO_ID) AS ROWCOUNT FROM REKANAN_BIDANG_USAHA_INFO WHERE REKANAN_BIDANG_USAHA_INFO_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(REKANAN_BIDANG_USAHA_INFO_ID) AS ROWCOUNT FROM REKANAN_BIDANG_USAHA_INFO WHERE REKANAN_BIDANG_USAHA_INFO_ID IS NOT NULL "; 
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