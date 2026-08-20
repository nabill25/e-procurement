<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Komoditas extends Entity{ 

	var $query;

    function __construct(){
  		parent::__construct();
	}
	
    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("KOMODITAS_ID", $this->getNextId("KOMODITAS_ID","KOMODITAS")); 

		$str = "
		INSERT INTO KOMODITAS (
		   KOMODITAS_ID, KOMODITAS_NAMA, KOMODITAS_KODE, KOMODITAS_KETERANGAN, CREATED_BY, CREATED_DATE, AKTIF) 
 			 	VALUES (
				  ".$this->getField("KOMODITAS_ID").",
  				  '".$this->getField("KOMODITAS_NAMA")."',
  				  '".$this->getField("KOMODITAS_KODE")."',
  				  '".$this->getField("KOMODITAS_KETERANGAN")."',
  				  '".$this->getField("CREATED_BY")."',
  				  '".$this->getField("CREATED_DATE")."',
  				  '".$this->getField("AKTIF")."'
				)"; 
		$this->query = $str;
		$this->id = $this->getField("KOMODITAS_ID");
		//echo $str;exit;
		return $this->execQuery($str);
    }
	
	 function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE KOMODITAS SET
				  KOMODITAS_NAMA = '".$this->getField("KOMODITAS_NAMA")."', 
				  KOMODITAS_KODE = '".$this->getField("KOMODITAS_KODE")."',
				  KOMODITAS_KETERANGAN = '".$this->getField("KOMODITAS_KETERANGAN")."' 
				WHERE KOMODITAS_ID = ".$this->getField("KOMODITAS_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "DELETE FROM KOMODITAS  
				WHERE KOMODITAS_ID = ".$this->getField("KOMODITAS_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="ORDER BY A.KOMODITAS_ID ASC")
	{
		$str = "SELECT A.* FROM KOMODITAS A
				WHERE 1 = 1
				"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ".$order;
				
		return $this->selectLimit($str,$limit,$from); 
    }
	
	function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(KOMODITAS_ID) AS ROWCOUNT FROM KOMODITAS A
				WHERE 1 = 1 "; 
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
    
  } 
?>