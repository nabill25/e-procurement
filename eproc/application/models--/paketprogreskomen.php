<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketProgresKomen extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PaketProgresKomen()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_PROGRES_KOMEN_ID", $this->getNextId("PAKET_PROGRES_KOMEN_ID","PAKET_PROGRES_KOMEN")); 		
		//'".$this->getField("FOTO")."',  FOTO,
		$str = "
				INSERT INTO PAKET_PROGRES_KOMEN (
				   PAKET_PROGRES_KOMEN_ID, PAKET_PROGRES_ID, USER_LOGIN_ID, KETERANGAN, TANGGAL, LAST_CREATE_USER, LAST_CREATE_DATE
				   ) 
 			  	VALUES (
				  ".$this->getField("PAKET_PROGRES_KOMEN_ID").",
				  ".$this->getField("PAKET_PROGRES_ID").",
				  ".$this->getField("USER_LOGIN_ID").",
				  '".$this->getField("KETERANGAN")."',
				  CURRENT_DATE,
				  '".$this->getField("LAST_CREATE_USER")."',
				  ".$this->getField("LAST_CREATE_DATE")."
				)"; 
		$this->id = $this->getField("PAKET_PROGRES_KOMEN_ID");
		$this->query = $str;
		return $this->execQuery($str);
    }

	function execSQL($str)
	{
		return $this->execQuery($str);
    }
	
	function upload($table, $column, $blob, $id)
	{
		return $this->uploadBlob($table, $column, $blob, $id);
    }
	
	function updateFormat()
	{
		$str = "
				UPDATE PAKET_PROGRES_KOMEN
				SET    
					   FILE_NAMA             = '".$this->getField("FILE_NAMA")."',
					   UKURAN                = ".$this->getField("UKURAN").",
					   FORMAT                = '".$this->getField("FORMAT")."'
				WHERE  PAKET_PROGRES_KOMEN_ID = '".$this->getField("PAKET_PROGRES_KOMEN_ID")."'
			 "; 
		$this->query = $str;
		return $this->execQuery($str);
    }
	
    function update()
	{
		$str = "
				UPDATE PAKET_PROGRES_KOMEN
				SET    
					   PAKET_PROGRES_ID= ".$this->getField("PAKET_PROGRES_ID").",
					   USER_LOGIN_ID= ".$this->getField("USER_LOGIN_ID").",
					   KETERANGAN= '".$this->getField("KETERANGAN")."',
					   TANGGAL= ".$this->getField("TANGGAL")."
				WHERE  PAKET_PROGRES_KOMEN_ID= '".$this->getField("PAKET_PROGRES_KOMEN_ID")."'
			 "; //FOTO= '".$this->getField("FOTO")."',
		$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PAKET_PROGRES_KOMEN
                WHERE 
                  PAKET_PROGRES_KOMEN_ID = ".$this->getField("PAKET_PROGRES_KOMEN_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","IJIN_USAHA_ID"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
	
    function selectByParamsBlob($paramsArray=array(),$limit=-1,$from=-1, $statement="", $order="")
	{
		$str = "
				SELECT 
				encode(FILE_UPLOAD, 'base64') FILE_UPLOAD, FORMAT, FILE_NAMA
				FROM PAKET_PROGRES_KOMEN
				WHERE 1 = 1
				"; 
		//, FOTO
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$order;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
    }
	
	function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement="", $order="")
	{
		$str = "
				SELECT 
				A.PAKET_PROGRES_KOMEN_ID, A.PAKET_PROGRES_ID,
				A.USER_LOGIN_ID, COALESCE(B.NAMA, C.USER_NAMA) USER_LOGIN, A.KETERANGAN, A.TANGGAL
				FROM PAKET_PROGRES_KOMEN A
                LEFT JOIN V_OAUTH_USER B ON TO_CHAR(A.USER_LOGIN_ID) = B.NIPP
				LEFT JOIN USER_LOGIN C ON C.USER_LOGIN_ID = A.USER_LOGIN_ID
				WHERE 1 = 1 
				"; 
		//, FOTO
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$order;
	
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement="")
	{
		$str = "
				SELECT PAKET_PROGRES_KOMEN_ID, PAKET_PROGRES_ID, USER_LOGIN_ID
				FROM PAKET_PROGRES_KOMEN
				WHERE 1 = 1
			    "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_PROGRES_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","IJIN_USAHA_ID"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
	function getCountByParams($paramsArray=array(), $statement="")
	{
		$str = "SELECT COUNT(1) AS ROWCOUNT 
				FROM PAKET_PROGRES_KOMEN A
                LEFT JOIN V_OAUTH_USER B ON TO_CHAR(A.USER_LOGIN_ID) = B.NIPP
				WHERE 1 = 1  ".$statement; 
		
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

    function getCountByParamsLike($paramsArray=array(), $statement="")
	{
		$str = "SELECT COUNT(PAKET_PROGRES_KOMEN_ID) AS ROWCOUNT FROM PAKET_PROGRES_KOMEN
		        WHERE PAKET_PROGRES_KOMEN_ID IS NOT NULL ".$statement; 
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