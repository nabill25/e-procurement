<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketEvaluasiKemampuanDasar extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PaketEvaluasiKemampuanDasar()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_EVAL_KD_ID", $this->getNextId("PAKET_EVAL_KD_ID","PAKET_EVAL_KD")); 

		$str = "
				INSERT INTO PAKET_EVAL_KD (
				   PAKET_EVAL_KD_ID, PAKET_ID, NILAI_KONTRAK_MIN, 
				   PEKERJAAN, PENGALAMAN_TAHUN) 
				VALUES (".$this->getField("PAKET_EVAL_KD_ID").", ".$this->getField("PAKET_ID").", ".$this->getField("NILAI_KONTRAK_MIN").", 
				   '".$this->getField("PEKERJAAN")."', ".$this->getField("PENGALAMAN_TAHUN").")
	
				"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
					UPDATE PAKET_EVAL_KD
					SET    NILAI_KONTRAK_MIN = ".$this->getField("NILAI_KONTRAK_MIN").",
						   PEKERJAAN         = ".$this->getField("PEKERJAAN").",
						   PENGALAMAN_TAHUN  = ".$this->getField("PENGALAMAN_TAHUN")."
					WHERE  PAKET_ID  = ".$this->getField("PAKET_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
		
	function delete()
	{
        $str = "DELETE FROM PAKET_EVAL_KD
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
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT 
				PAKET_EVAL_KD_ID, PAKET_ID, NILAI_KONTRAK_MIN, 
				   PEKERJAAN, PENGALAMAN_TAHUN
				FROM PAKET_EVAL_KD WHERE PAKET_EVAL_KD_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_EVAL_KD_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PAKET_EVAL_KD_ID, NAMA
				FROM PAKET_EVAL_KD WHERE PAKET_EVAL_KD_ID IS NOT NULL"; 
		
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
		$str = "SELECT COUNT(PAKET_EVAL_KD_ID) AS ROWCOUNT FROM PAKET_EVAL_KD WHERE PAKET_EVAL_KD_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(PAKET_EVAL_KD_ID) AS ROWCOUNT FROM PAKET_EVAL_KD WHERE PAKET_EVAL_KD_ID IS NOT NULL "; 
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