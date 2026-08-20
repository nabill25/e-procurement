<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class NegoShoutbox extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function NegoShoutbox()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		$str = "
				INSERT INTO NEGOSHOUTBOX (
				   JAM, NAMA, PESAN, 
   					IP_ADDRESS, REKANAN_ID, PAKET_PENAWARAN_ID, KODE, FILE) 
				VALUES (
				  '".$this->getField("JAM")."',
				  '".$this->getField("NAMA")."', 
				  '".$this->getField("PESAN")."', 
				  '".$this->getField("IP_ADDRESS")."', 
				  ".$this->getField("REKANAN_ID").", 
				  '".$this->getField("PAKET_PENAWARAN_ID")."', 
				  '".$this->getField("KODE")."',
				  '".$this->getField("FILE")."'
				)"; 
		$this->query = $str;
		
		return $this->execQuery($str);
    }

    function insert2()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		//$this->setField("JAM", $this->getNextId("JAM","PHPSHOUTBOX")); 

		$str = "
				INSERT INTO NEGOSHOUTBOX (
				   JAM, NAMA, PESAN, 
   					IP_ADDRESS, REKANAN_ID, PAKET_ID) 
				VALUES (
				  '".$this->getField("JAM")."',
				  '".$this->getField("NAMA")."', 
				  '".$this->getField("PESAN")."', 
				  '".$this->getField("IP_ADDRESS")."', 
				  ".$this->getField("REKANAN_ID").", 
				  '".$this->getField("PAKET_ID")."'
				)"; 
		// echo $str; die();
		$this->query = $str;
		
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE NEGOSHOUTBOX SET
				  NAMA = '".$this->getField("NAMA")."'
				WHERE JAM = '".$this->getField("JAM")."'
				"; 
				$this->query = $str;
				
		return $this->execQuery($str);
    }

    function updateBaca($tiketId, $pegawaiId)
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE NEGOSHOUTBOX SET
				  STATUS_BACA = 1
				WHERE TIKET_ID = '".$tiketId."' AND 
					  NOT REKANAN_ID = '".$pegawaiId."'
				"; 
				$this->query = $str;
				
		return $this->execQuery($str);
    }
	
		
	function delete()
	{
        $str = "DELETE FROM NEGOSHOUTBOX
                WHERE 
                  JAM = '".$this->getField("JAM")."'"; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

	function deleteParentChild()
	{
        $str = "DELETE FROM NEGOSHOUTBOX
                WHERE 
                  NAMA = '".$this->getField("NAMA")."'"; 
				  
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
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=' ORDER BY JAM ASC ')
	{
		$str = "SELECT JAM, NAMA, PESAN, 
   					IP_ADDRESS, PAKET_PENAWARAN_ID, KODE, TO_CHAR(WAKTU, 'DD/MM/YYYY HH24:MI:SS') WAKTU, FILE
				FROM NEGOSHOUTBOX A WHERE 1=1 "; 
		//JAM IS NOT NULL
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
	}

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT JAM, NAMA
				FROM NEGOSHOUTBOX WHERE JAM IS NOT NULL"; 
		
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
		$str = "SELECT COUNT(JAM) AS ROWCOUNT FROM NEGOSHOUTBOX WHERE JAM IS NOT NULL "; 
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
		$str = "SELECT COUNT(JAM) AS ROWCOUNT FROM NEGOSHOUTBOX WHERE JAM IS NOT NULL "; 
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
