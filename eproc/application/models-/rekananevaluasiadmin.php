<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class RekananEvaluasiAdmin extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
		parent::__construct();
	}
	
    function RekananEvaluasiAdmin()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_EVAL_ADMIN_ID", $this->getNextId("REKANAN_EVAL_ADMIN_ID","REKANAN_EVAL_ADMIN")); 

		$str = "
		INSERT INTO REKANAN_EVAL_ADMIN (
				REKANAN_EVAL_ADMIN_ID, PAKET_REKANAN_ID, PAKET_EVAL_ADMIN_ID, 
   				URAIAN, EVALUASI_NUMBER, PATH_FILE, TIPE, UKURAN) 
				VALUES (
				  ".$this->getField("REKANAN_EVAL_ADMIN_ID").",
				  ".$this->getField("PAKET_REKANAN_ID").",
				  NULL,
				  '".$this->getField("URAIAN")."',
				  ".$this->getField("EVALUASI_NUMBER").",
				  '".$this->getField("PATH_FILE")."',
				  '".$this->getField("TIPE")."',
				  ".$this->getField("UKURAN")."
				)"; 
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_EVAL_ADMIN SET
				  URAIAN = '".$this->getField("URAIAN")."'
				WHERE REKANAN_EVAL_ADMIN_ID = '".$this->getField("REKANAN_EVAL_ADMIN_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updateFile()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_EVAL_ADMIN SET
				  URAIAN = '".$this->getField("URAIAN")."',
				  PATH_FILE = '".$this->getField("PATH_FILE")."',
				  TIPE = '".$this->getField("TIPE")."',
				  UKURAN = ".$this->getField("UKURAN")."
				WHERE REKANAN_EVAL_ADMIN_ID = '".$this->getField("REKANAN_EVAL_ADMIN_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
		
	function delete()
	{
        $str = "DELETE FROM REKANAN_EVAL_ADMIN
                WHERE 
                  REKANAN_EVAL_ADMIN_ID = '".$this->getField("REKANAN_EVAL_ADMIN_ID")."'"; 
				  
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
		$str = "SELECT 
				PAKET_REKANAN_ID, PAKET_EVAL_ADMIN_ID, EVALUASI_NUMBER,
				   URAIAN, PATH_FILE, TIPE, UKURAN
				FROM REKANAN_EVAL_ADMIN WHERE REKANAN_EVAL_ADMIN_ID IS NOT NULL  "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY EVALUASI_NUMBER ASC";
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsV2($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT 
				PAKET_REKANAN_ID, PAKET_EVAL_ADMIN_ID, A.EVALUASI_NUMBER,
				   A.URAIAN, A.PATH_FILE, A.TIPE, A.UKURAN, B.TIPE TIPE_ENTRI
				FROM REKANAN_EVAL_ADMIN A LEFT JOIN EVAL_ADMIN B ON A.EVALUASI_NUMBER = B.EVALUASI_NUMBER WHERE REKANAN_EVAL_ADMIN_ID IS NOT NULL  "; 
		
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
		$str = "SELECT REKANAN_EVAL_ADMIN_ID, NAMA
				FROM REKANAN_EVAL_ADMIN WHERE REKANAN_EVAL_ADMIN_ID IS NOT NULL"; 
		
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
	
	function getIdEvaluasiAdmin($paramsArray=array())
	{
		$str = "SELECT REKANAN_EVAL_ADMIN_ID AS ROWCOUNT FROM REKANAN_EVAL_ADMIN WHERE REKANAN_EVAL_ADMIN_ID IS NOT NULL "; 
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
	
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(REKANAN_EVAL_ADMIN_ID) AS ROWCOUNT FROM REKANAN_EVAL_ADMIN WHERE REKANAN_EVAL_ADMIN_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(REKANAN_EVAL_ADMIN_ID) AS ROWCOUNT FROM REKANAN_EVAL_ADMIN WHERE REKANAN_EVAL_ADMIN_ID IS NOT NULL "; 
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