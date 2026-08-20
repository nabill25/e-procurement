<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketEvaluasiPeralatan extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PaketEvaluasiPeralatan()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_EVAL_PERALATAN_ID", $this->getNextId("PAKET_EVAL_PERALATAN_ID","PAKET_EVAL_PERALATAN")); 

		$str = "
				INSERT INTO PAKET_EVAL_PERALATAN (
				   PAKET_EVAL_PERALATAN_ID, PAKET_ID, MSB, 
				   SPJB, SPDB, NILAI_MINIMUM)
				VALUES (".$this->getField("PAKET_EVAL_PERALATAN_ID").", ".$this->getField("PAKET_ID").", ".$this->getField("MSB").", 
				   ".$this->getField("SPJB").", ".$this->getField("SPDB").", ".$this->getField("NILAI_MINIMUM").")

				"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_EVAL_PERALATAN SET
				  MSB = '".$this->getField("MSB")."',
				  SPJB = '".$this->getField("SPJB")."',
				  SPDB = '".$this->getField("SPDB")."',
				  NILAI_MINIMUM = '".$this->getField("NILAI_MINIMUM")."',
				  NILAI_MINIMAL= '".$this->getField("NILAI_MINIMAL")."'
				WHERE PAKET_ID = '".$this->getField("PAKET_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PAKET_EVAL_PERALATAN
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
		$str = "SELECT PAKET_EVAL_PERALATAN_ID, PAKET_ID, MSB, 
				   SPJB, SPDB, NILAI_MINIMUM, NILAI_MINIMAL
				FROM PAKET_EVAL_PERALATAN WHERE PAKET_EVAL_PERALATAN_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_EVAL_PERALATAN_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PAKET_EVAL_PERALATAN_ID, NAMA
				FROM PAKET_EVAL_PERALATAN WHERE PAKET_EVAL_PERALATAN_ID IS NOT NULL"; 
		
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
		$str = "SELECT COUNT(PAKET_EVAL_PERALATAN_ID) AS ROWCOUNT FROM PAKET_EVAL_PERALATAN WHERE PAKET_EVAL_PERALATAN_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(PAKET_EVAL_PERALATAN_ID) AS ROWCOUNT FROM PAKET_EVAL_PERALATAN WHERE PAKET_EVAL_PERALATAN_ID IS NOT NULL "; 
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