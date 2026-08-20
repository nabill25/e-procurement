<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class RekananEvaluasiSyaratDaftar extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function RekananEvaluasiSyaratDaftar()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_EVAL_SYARAT_DAFTAR_ID", $this->getNextId("REKANAN_EVAL_SYARAT_DAFTAR_ID","REKANAN_EVAL_SYARAT_DAFTAR")); 

		$str = "INSERT INTO REKANAN_EVAL_SYARAT_DAFTAR (
				   REKANAN_EVAL_SYARAT_DAFTAR_ID, PAKET_EVAL_SYARAT_DAFTAR_ID, REKANAN_ID, PATH_FILE) 
				VALUES (
				  ".$this->getField("REKANAN_EVAL_SYARAT_DAFTAR_ID").",
				  ".$this->getField("PAKET_EVAL_SYARAT_DAFTAR_ID").",
				  '".$this->getField("REKANAN_ID")."',
				  '".$this->getField("PATH_FILE")."'
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM REKANAN_EVAL_SYARAT_DAFTAR
                WHERE 
                  PAKET_EVAL_SYARAT_DAFTAR_ID = '".$this->getField("PAKET_EVAL_SYARAT_DAFTAR_ID")."' AND
				  REKANAN_ID = '".$this->getField("REKANAN_ID")."' "; 

				  
		$this->query = $str;
		//echo $str;
        return $this->execQuery($str);
    }

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","REKANAN_ID"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT 
				REKANAN_EVAL_SYARAT_DAFTAR_ID, PAKET_EVAL_SYARAT_DAFTAR_ID, REKANAN_ID, PATH_FILE
				FROM REKANAN_EVAL_SYARAT_DAFTAR WHERE REKANAN_EVAL_SYARAT_DAFTAR_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PATH_FILE ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","REKANAN_ID"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(REKANAN_EVAL_SYARAT_DAFTAR_ID) AS ROWCOUNT FROM REKANAN_EVAL_SYARAT_DAFTAR WHERE REKANAN_EVAL_SYARAT_DAFTAR_ID IS NOT NULL "; 

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