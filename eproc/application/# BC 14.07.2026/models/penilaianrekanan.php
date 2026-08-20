<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PenilaianRekanan extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PenilaianRekanan()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		//$this->setField("PENILAIAN_REKANAN_ID", $this->getNextId("PENILAIAN_REKANAN_ID","PENILAIAN_REKANAN")); 
		
		$str = "
				INSERT INTO PENILAIAN_REKANAN (
				   PENILAIAN_REKANAN_ID, PENILAIAN_REKANAN_PARENT_ID, PAKET_ID,
   					KODE, NAMA, NILAI, PROSENTASE) 
				VALUES (
				  (SELECT PENILAIAN_REKANAN_GENERATE('".$this->getField("PENILAIAN_REKANAN_PARENT_ID")."', '".$this->getField("PAKET_ID")."') HASILID FROM DUAL),
				  '".$this->getField("PENILAIAN_REKANAN_PARENT_ID")."', 
				  '".$this->getField("PAKET_ID")."', 
				  '".$this->getField("KODE")."', 
				  '".$this->getField("NAMA")."', 
				  '".$this->getField("NILAI")."', 
				  '".$this->getField("PROSENTASE")."'
				)"; 
				//echo $str;
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PENILAIAN_REKANAN SET
				  NAMA = '".$this->getField("NAMA")."'
				WHERE PENILAIAN_REKANAN_ID = '".$this->getField("PENILAIAN_REKANAN_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PENILAIAN_REKANAN
                WHERE 
                  PENILAIAN_REKANAN_ID = '".$this->getField("PENILAIAN_REKANAN_ID")."'"; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

	function deleteParentChild()
	{
        $str = "DELETE FROM PENILAIAN_REKANAN
                WHERE 
                  PAKET_ID = '".$this->getField("PAKET_ID")."'"; 
				  
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
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=" ORDER BY PENILAIAN_REKANAN_ID ASC ")
	{
		$str = "SELECT PENILAIAN_REKANAN_ID, PENILAIAN_REKANAN_PARENT_ID, 
   					KODE, NAMA, NILAI, PROSENTASE
				FROM PENILAIAN_REKANAN WHERE 1=1 "; 
		//PENILAIAN_REKANAN_ID IS NOT NULL
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$order;
		
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsMonitoringPenilaian($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=" ORDER BY B.TANGGAL DESC ")
	{
		$str = "
				SELECT A.PAKET_ID, B.NAMA, E.NAMA REKANAN,  CASE WHEN COALESCE(D.JUMLAH, 0) = 0 THEN 0 ELSE 1 END STATUS,
				CASE WHEN COALESCE(D.JUMLAH, 0) = 0 THEN 'Belum Dinilai' ELSE 'Sudah Dinilai' END STATUS_PENILAIAN, B.TANGGAL 
				FROM PAKET_PIHAK_LAIN A 
				INNER JOIN PAKET B ON A.PAKET_ID = B.PAKET_ID
				INNER JOIN (SELECT PAKET_ID, COUNT(1) JUMLAH FROM PENILAIAN_REKANAN X GROUP BY PAKET_ID) C ON A.PAKET_ID = C.PAKET_ID
				LEFT JOIN (SELECT PAKET_ID, COUNT(1) JUMLAH FROM REKANAN_PENILAIAN_REKANAN X GROUP BY PAKET_ID) D ON A.PAKET_ID = D.PAKET_ID
				LEFT JOIN REKANAN E ON B.REKANAN_ID_PEMENANG = E.REKANAN_ID
				WHERE 1 = 1 
				"; 
		//PENILAIAN_REKANAN_ID IS NOT NULL
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$order;
		
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
    }
	
    function selectByParamsRoom($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT   (SELECT 'BAB ' || KODE
							FROM PENILAIAN_REKANAN X
						   WHERE X.PENILAIAN_REKANAN_ID = A.PENILAIAN_REKANAN_PARENT_ID) || ' - Pasal ' || KODE NAMA
					FROM PENILAIAN_REKANAN A
				   WHERE NOT PENILAIAN_REKANAN_PARENT_ID = 0
				"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PENILAIAN_REKANAN_ID ASC ";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PENILAIAN_REKANAN_ID, NAMA
				FROM PENILAIAN_REKANAN WHERE PENILAIAN_REKANAN_ID IS NOT NULL"; 
		
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
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(PENILAIAN_REKANAN_ID) AS ROWCOUNT FROM PENILAIAN_REKANAN WHERE PENILAIAN_REKANAN_ID IS NOT NULL "; 
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

    function getCountByParamsMonitoringPenilaian($paramsArray=array(), $statement="")
	{
		$str = "SELECT COUNT(1) AS ROWCOUNT FROM PAKET_PIHAK_LAIN A 
				INNER JOIN PAKET B ON A.PAKET_ID = B.PAKET_ID
				INNER JOIN (SELECT PAKET_ID, COUNT(1) JUMLAH FROM PENILAIAN_REKANAN X GROUP BY PAKET_ID) C ON A.PAKET_ID = C.PAKET_ID
				LEFT JOIN (SELECT PAKET_ID, COUNT(1) JUMLAH FROM REKANAN_PENILAIAN_REKANAN X GROUP BY PAKET_ID) D ON A.PAKET_ID = D.PAKET_ID
				LEFT JOIN REKANAN E ON B.REKANAN_ID_PEMENANG = E.REKANAN_ID
				WHERE 1 = 1  ".$statement; 
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
		$str = "SELECT COUNT(PENILAIAN_REKANAN_ID) AS ROWCOUNT FROM PENILAIAN_REKANAN WHERE PENILAIAN_REKANAN_ID IS NOT NULL "; 
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