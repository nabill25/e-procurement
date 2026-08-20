<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketEvaluasiSyaratDaftar extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
		parent::__construct();
	}
	
    function PaketEvaluasiSyaratDaftar()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_EVAL_SYARAT_DAFTAR_ID", $this->getNextId("PAKET_EVAL_SYARAT_DAFTAR_ID","PAKET_EVAL_SYARAT_DAFTAR")); 

		$str = "INSERT INTO PAKET_EVAL_SYARAT_DAFTAR (
				   PAKET_EVAL_SYARAT_DAFTAR_ID, PAKET_ID, NAMA, EVALUASI_NUMBER, KETERANGAN) 
				VALUES (
				  ".$this->getField("PAKET_EVAL_SYARAT_DAFTAR_ID").",
				  ".$this->getField("PAKET_ID").",
				  '".$this->getField("NAMA")."',
				  '".$this->getField("EVALUASI_NUMBER")."',
				  '".$this->getField("KETERANGAN")."'
				)"; 
				// echo $str; die();
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE AGAMA SET
				  NAMA = '".$this->getField("NAMA")."'
				WHERE AGAMA_ID = '".$this->getField("AGAMA_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PAKET_EVAL_SYARAT_DAFTAR
                WHERE 
                  PAKET_ID = '".$this->getField("PAKET_ID")."'"; 

				  
		$this->query = $str;
		//echo $str;
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
		$str = "SELECT 
				PAKET_EVAL_SYARAT_DAFTAR_ID, PAKET_ID, NAMA, KETERANGAN, EVALUASI_NUMBER
				FROM PAKET_EVAL_SYARAT_DAFTAR WHERE PAKET_EVAL_SYARAT_DAFTAR_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY EVALUASI_NUMBER ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsPersyaratan($reqRekananId, $paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT 
				A.PAKET_EVAL_SYARAT_DAFTAR_ID, A.PAKET_ID, NAMA, KETERANGAN, EVALUASI_NUMBER, B.PATH_FILE, 
				CASE WHEN B.PATH_FILE IS NULL THEN '' ELSE 'Data Lengkap' END KELENGKAPAN
				FROM PAKET_EVAL_SYARAT_DAFTAR A 
				LEFT JOIN REKANAN_EVAL_SYARAT_DAFTAR B ON A.PAKET_EVAL_SYARAT_DAFTAR_ID = B.PAKET_EVAL_SYARAT_DAFTAR_ID AND B.REKANAN_ID = '".$reqRekananId."'
				WHERE A.PAKET_EVAL_SYARAT_DAFTAR_ID IS NOT NULL "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
		$str .= $statement." ORDER BY EVALUASI_NUMBER ASC";
		return $this->selectLimit($str,$limit,$from); 
    }
	    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT AGAMA_ID, NAMA
				FROM AGAMA WHERE AGAMA_ID IS NOT NULL"; 
		
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
    function getCountByParams($paramsArray=array(), $statement="")
	{
		$str = "SELECT COUNT(PAKET_EVAL_SYARAT_DAFTAR_ID) AS ROWCOUNT FROM PAKET_EVAL_SYARAT_DAFTAR WHERE PAKET_EVAL_SYARAT_DAFTAR_ID IS NOT NULL ".$statement; 

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
		$str = "SELECT COUNT(AGAMA_ID) AS ROWCOUNT FROM AGAMA WHERE AGAMA_ID IS NOT NULL "; 
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