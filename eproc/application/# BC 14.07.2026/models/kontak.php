<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once("entity.php");

  class Kontak extends Entity{ 

	var $query;
     
    function Kontak()
	{
      $this->Entity(); 
    }
			
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("KONTAK_ID", $this->getNextId("KONTAK_ID","KONTAK")); 

		$str = "INSERT INTO KONTAK(KONTAK_ID, NAMA, EMAIL, TELEPON, SUBYEK, PESAN, IPADDRESS, TANGGAL, STATUS) 
				VALUES(
				  ".$this->getField("KONTAK_ID").",
				  '".$this->getField("NAMA")."',
				  '".$this->getField("EMAIL")."',
				  '".$this->getField("TELEPON")."',
				  '".$this->getField("SUBYEK")."',				  				  
				  '".$this->getField("PESAN")."',
				  '".$this->getField("IPADDRESS")."',
				  CURRENT_DATE,
				  ".$this->getField("STATUS")."
				)"; 
				//'".$this->getField("TANGGAL")."',	
		$this->query = $str;
		// echo $str;
		return $this->execQuery($str);
    }

    function updateByField()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE KONTAK A SET
				  ".$this->getField("FIELD")." = '".$this->getField("FIELD_VALUE")."'
				WHERE KONTAK_ID = ".$this->getField("KONTAK_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }		
    
	function updateStatusAktifOnly()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE KONTAK SET
				STATUS = '".$this->getField("STATUS")."'				  
				WHERE KONTAK_ID = '".$this->getField("KONTAK_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }	
	
	function delete()
	{
        $str = "DELETE FROM KONTAK
                WHERE 
                  KONTAK_ID = '".$this->getField("KONTAK_ID")."'"; 
				  
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
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement="", $order="ORDER BY TANGGAL DESC")
	{
		$str = "SELECT KONTAK_ID, NAMA, 
						EMAIL, PERUSAHAAN, SUBYEK, PESAN, 
					   IPADDRESS, TO_CHAR(TANGGAL, 'YYYY-MM-DD')TANGGAL, 
					   CASE WHEN STATUS = 1 THEN 'Aktif' ELSE 'Nonaktif' END AKTIF, STATUS, TELEPON
				FROM KONTAK WHERE KONTAK_ID IS NOT NULL ".$statement; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= " ".$order;
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1)
	{
		$str = "SELECT KONTAK_ID, NAMA, EMAIL, PERUSAHAAN, SUBYEK, PESAN, IPADDRESS, TANGGAL, CASE WHEN STATUS = 1 THEN 'Aktif' ELSE 'Nonaktif' END STATUS, TELEPON
				FROM KONTAK WHERE KONTAK_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= " ORDER BY KONTAK_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array(), $statement="")
	{
		$str = "SELECT COUNT(KONTAK_ID) AS ROWCOUNT FROM KONTAK WHERE KONTAK_ID IS NOT NULL ".$statement; 
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
		$str = "SELECT COUNT(KONTAK_ID) AS ROWCOUNT FROM KONTAK WHERE KONTAK_ID IS NOT NULL "; 
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