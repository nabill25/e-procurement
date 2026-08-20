<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  include_once('entity.php');

  class Aanwijzing extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
 //    function Aanwijzing()
	// {
 //      $this->Entity(); 
 //    }
    function __construct(){
  		parent::__construct();
	}
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		//$this->setField("AANWIJZING_ID", $this->getNextId("AANWIJZING_ID","AANWIJZING")); 

		$str = "
				INSERT INTO AANWIJZING (
				   AANWIJZING_ID, PAKET_ID, AANWIJZING_PARENT_ID, 
   					KODE, NAMA, FILE_UPLOAD, FILE_COUNT, KETERANGAN) 
				VALUES (
				  (SELECT AANWIJZING_GENERATE('".$this->getField("AANWIJZING_ID")."')),
				  '".$this->getField("PAKET_ID")."', 
				  '".$this->getField("AANWIJZING_PARENT_ID")."', 
				  '".$this->getField("KODE")."', 
				  '".$this->getField("NAMA")."', 
				  '".$this->getField("FILE_UPLOAD")."', 
				  '".$this->getField("FILE_COUNT")."', 
				  '".$this->getField("KETERANGAN")."'
				)"; 
		$this->query = $str;
		// echo $str; die();
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE AANWIJZING SET
				  NAMA = '".$this->getField("NAMA")."'
				WHERE AANWIJZING_ID = '".$this->getField("AANWIJZING_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updateByField()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE AANWIJZING A SET
				  ".$this->getField("FIELD")." = ".$this->getField("FIELD_VALUE")."
				WHERE PAKET_ID = ".$this->getField("PAKET_ID")."
				"; 
				$this->query = $str;
	
		return $this->execQuery($str);
    }
		
	function delete()
	{
        $str = "DELETE FROM AANWIJZING
                WHERE 
                  AANWIJZING_ID = '".$this->getField("AANWIJZING_ID")."'"; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

	function deleteParentChild()
	{
        $str = "DELETE FROM AANWIJZING
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
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=" ORDER BY AANWIJZING_ID ASC ")
	{
		$str = "SELECT AANWIJZING_ID, PAKET_ID, AANWIJZING_PARENT_ID, 
   					KODE, NAMA, FILE_UPLOAD, FILE_COUNT, PUBLISH, KETERANGAN
				FROM AANWIJZING A WHERE 1=1 "; 
		//AANWIJZING_ID IS NOT NULL
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$order;
		$this->query = $str;
				//CAST(KODE AS INT)
				//." ORDER BY KODE ASC"
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsRoom($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT   (SELECT 'BAB ' || KODE
							FROM AANWIJZING X
						   WHERE X.AANWIJZING_ID = A.AANWIJZING_PARENT_ID) || ' - Pasal ' || KODE NAMA
					FROM AANWIJZING A
				   WHERE NOT AANWIJZING_PARENT_ID = 0
				"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY AANWIJZING_ID ASC ";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT AANWIJZING_ID, NAMA
				FROM AANWIJZING WHERE AANWIJZING_ID IS NOT NULL"; 
		
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
		$str = "SELECT COUNT(AANWIJZING_ID) AS ROWCOUNT FROM AANWIJZING A WHERE AANWIJZING_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(AANWIJZING_ID) AS ROWCOUNT FROM AANWIJZING WHERE AANWIJZING_ID IS NOT NULL "; 
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