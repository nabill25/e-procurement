<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class RekananPenilaianRekanan extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function RekananPenilaianRekanan()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		//$this->setField("PENILAIAN_REKANAN_ID", $this->getNextId("PENILAIAN_REKANAN_ID","REKANAN_PENILAIAN_REKANAN")); 
		
		$str = "
				INSERT INTO REKANAN_PENILAIAN_REKANAN (
				   PENILAIAN_REKANAN_ID, PAKET_ID,
   					REKANAN_ID, NILAI, KETERANGAN) 
				VALUES (
				  '".$this->getField("PENILAIAN_REKANAN_ID")."', 
				  '".$this->getField("PAKET_ID")."', 
				  '".$this->getField("REKANAN_ID")."', 
				  '".$this->getField("NILAI")."', 
				  '".$this->getField("KETERANGAN")."'
				)"; 
				//echo $str;
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_PENILAIAN_REKANAN SET
				  NAMA = '".$this->getField("NAMA")."'
				WHERE PENILAIAN_REKANAN_ID = '".$this->getField("PENILAIAN_REKANAN_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM REKANAN_PENILAIAN_REKANAN
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
		$str = "SELECT PENILAIAN_REKANAN_ID, PAKET_ID,
   					REKANAN_ID, NILAI, KETERANGAN
				FROM REKANAN_PENILAIAN_REKANAN A WHERE 1=1 "; 
		//PENILAIAN_REKANAN_ID IS NOT NULL
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$order;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsPenilaian($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=" ORDER BY A.PENILAIAN_REKANAN_ID ASC ")
	{
		$str = "SELECT A.PENILAIAN_REKANAN_ID, A.PENILAIAN_REKANAN_PARENT_ID, 
					  A.NAMA, B.NILAI, B.KETERANGAN, USER_LOGIN_ID
				FROM PENILAIAN_REKANAN A 
				LEFT JOIN REKANAN_PENILAIAN_REKANAN B ON A.PAKET_ID = B.PAKET_ID AND A.PENILAIAN_REKANAN_ID = B.PENILAIAN_REKANAN_ID
                LEFT JOIN PAKET_PIHAK_LAIN C ON A.PAKET_ID = C.PAKET_ID 
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

    function selectByParamsHasilPenilaian($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=" ORDER BY A.PENILAIAN_REKANAN_ID ASC ")
	{
		$str = "SELECT A.PENILAIAN_REKANAN_ID, A.PENILAIAN_REKANAN_PARENT_ID, 
					  A.NAMA, B.NILAI, B.KETERANGAN, A.NILAI NILAI_STANDAR, A.PROSENTASE PROSENTASE_STANDAR, ROUND((A.NILAI * ROUND((B.NILAI * A.PROSENTASE) / 100, 2)) / 100, 2) HASIL
				FROM PENILAIAN_REKANAN A 
				LEFT JOIN REKANAN_PENILAIAN_REKANAN B ON A.PAKET_ID = B.PAKET_ID AND A.PENILAIAN_REKANAN_ID = B.PENILAIAN_REKANAN_ID 
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
	
	function selectByParamsHasilPenilaianRekanan($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=" ORDER BY A.PENILAIAN_REKANAN_ID ASC ")
	{
		$str = "SELECT A.PENILAIAN_REKANAN_ID, A.PENILAIAN_REKANAN_PARENT_ID, C.NAMA PAKET, 
					  A.NAMA, B.NILAI, B.KETERANGAN, A.NILAI NILAI_STANDAR, A.PROSENTASE PROSENTASE_STANDAR, ROUND((A.NILAI * ROUND((B.NILAI * A.PROSENTASE) / 100, 2)) / 100, 2) HASIL
				FROM PENILAIAN_REKANAN A 
				LEFT JOIN REKANAN_PENILAIAN_REKANAN B ON A.PAKET_ID = B.PAKET_ID AND A.PENILAIAN_REKANAN_ID = B.PENILAIAN_REKANAN_ID
                LEFT JOIN PAKET C ON A.PAKET_ID = C.PAKET_ID 
				WHERE 1 = 1
				 "; 		
				 
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= $statement." ".$order;
		$this->query = $str;
		
		return $this->selectLimit($str,$limit,$from); 
    }
		
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(PENILAIAN_REKANAN_ID) AS ROWCOUNT FROM REKANAN_PENILAIAN_REKANAN WHERE PENILAIAN_REKANAN_ID IS NOT NULL "; 
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

    function getItemPenilaian($paramsArray=array())
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
	
    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(PENILAIAN_REKANAN_ID) AS ROWCOUNT FROM REKANAN_PENILAIAN_REKANAN WHERE PENILAIAN_REKANAN_ID IS NOT NULL "; 
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