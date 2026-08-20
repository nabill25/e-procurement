<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class RekananEvaluasiTeknisTawar extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function RekananEvaluasiTeknisTawar()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_EVAL_TEKNIS_TAWAR_ID", $this->getNextId("REKANAN_EVAL_TEKNIS_TAWAR_ID","REKANAN_EVAL_TEKNIS_TAWAR")); 

		$str = "INSERT INTO REKANAN_EVAL_TEKNIS_TAWAR (
				   REKANAN_EVAL_TEKNIS_TAWAR_ID, PAKET_ID, NAMA) 
				VALUES (
				  ".$this->getField("REKANAN_EVAL_TEKNIS_TAWAR_ID").",
				  ".$this->getField("PAKET_ID").",
				  '".$this->getField("NAMA")."'
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

	function insertStatus()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_EVAL_TEKNIS_TAWAR_ID", $this->getNextId("REKANAN_EVAL_TEKNIS_TAWAR_ID","REKANAN_EVAL_TEKNIS_TAWAR")); 

		$str = "INSERT INTO REKANAN_EVAL_TEKNIS_TAWAR (
				   REKANAN_EVAL_TEKNIS_TAWAR_ID, PAKET_REKANAN_ID, PAKET_EVAL_TEKNIS_TAWAR_ID, STATUS) 
				VALUES (
				  ".$this->getField("REKANAN_EVAL_TEKNIS_TAWAR_ID").",
				  ".$this->getField("PAKET_REKANAN_ID").",
				  ".$this->getField("PAKET_EVAL_TEKNIS_TAWAR_ID").",
				  1
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

	function insertSyarat()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_EVAL_TEKNIS_TAWAR_ID", $this->getNextId("REKANAN_EVAL_TEKNIS_TAWAR_ID","REKANAN_EVAL_TEKNIS_TAWAR")); 

		$str = "INSERT INTO REKANAN_EVAL_TEKNIS_TAWAR (
				   REKANAN_EVAL_TEKNIS_TAWAR_ID, PAKET_REKANAN_ID, PAKET_EVAL_TEKNIS_TAWAR_ID, MEMENUHI_SYARAT, URAIAN, KETERANGAN, SKOR_TEKNIS, NILAI_TEKNIS, CREATED_BY, CREATED_DATE) 
				VALUES (
				  ".$this->getField("REKANAN_EVAL_TEKNIS_TAWAR_ID").",
				  ".$this->getField("PAKET_REKANAN_ID").",
				  ".$this->getField("PAKET_EVAL_TEKNIS_TAWAR_ID").",
				  '".$this->getField("MEMENUHI_SYARAT")."',
				  '".$this->getField("URAIAN")."',
				  '".$this->getField("KETERANGAN")."',
				  ".$this->getField("SKOR_TEKNIS").",
				  ".$this->getField("NILAI_TEKNIS").",
				  ".$this->getField("CREATED_BY").",
				  CURRENT_TIMESTAMP
				)"; 		
		$this->query = $str;
		return $this->execQuery($str);
    }

    function updateStatus()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_EVAL_TEKNIS_TAWAR A SET
				  STATUS = ".$this->getField("STATUS")."
				WHERE PAKET_REKANAN_ID = '".$this->getField("PAKET_REKANAN_ID")."' AND PAKET_EVAL_TEKNIS_TAWAR_ID = '".$this->getField("PAKET_EVAL_TEKNIS_TAWAR_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
    function updateSyarat()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_EVAL_TEKNIS_TAWAR A SET
				  MEMENUHI_SYARAT = '".$this->getField("MEMENUHI_SYARAT")."',
				  URAIAN = '".$this->getField("URAIAN")."',
				  KETERANGAN = '".$this->getField("KETERANGAN")."',
				  SKOR_TEKNIS = ".$this->getField("SKOR_TEKNIS").",
				  NILAI_TEKNIS = ".$this->getField("NILAI_TEKNIS").",
				  UPDATED_BY = ".$this->getField("CREATED_BY").",
				  UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE PAKET_REKANAN_ID = '".$this->getField("PAKET_REKANAN_ID")."' AND PAKET_EVAL_TEKNIS_TAWAR_ID = '".$this->getField("PAKET_EVAL_TEKNIS_TAWAR_ID")."'
				"; 

				$this->query = $str;
		return $this->execQuery($str);
    }	
	
	function delete()
	{
        $str = "DELETE FROM REKANAN_EVAL_TEKNIS_TAWAR
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
				REKANAN_EVAL_TEKNIS_TAWAR_ID, PAKET_EVAL_TEKNIS_TAWAR_ID, PAKET_REKANAN_ID, 
				STATUS, MEMENUHI_SYARAT, URAIAN, KETERANGAN, SKOR_TEKNIS, NILAI_TEKNIS
				FROM REKANAN_EVAL_TEKNIS_TAWAR WHERE REKANAN_EVAL_TEKNIS_TAWAR_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_EVAL_TEKNIS_TAWAR_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT REKANAN_EVAL_TEKNIS_TAWAR_ID, NAMA
				FROM REKANAN_EVAL_TEKNIS_TAWAR WHERE REKANAN_EVAL_TEKNIS_TAWAR_ID IS NOT NULL"; 
		
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

    function selectMemenuhiSyarat($paket_id,$paket_rekanan_id)
	{
		$str = "
				SELECT (SELECT COUNT(*) FROM PAKET_EVAL_TEKNIS_TAWAR WHERE PAKET_ID = ".$paket_id.") JUMLAH_EVALUASI,
        		(SELECT COUNT(*) FROM REKANAN_EVAL_TEKNIS_TAWAR WHERE PAKET_REKANAN_ID = ".$paket_rekanan_id." AND MEMENUHI_SYARAT = '1') JUMLAH_DILENGKAPI,
				( SELECT URAIAN FROM REKANAN_EVAL_TEKNIS_TAWAR WHERE PAKET_REKANAN_ID = ".$paket_rekanan_id." AND MEMENUHI_SYARAT = '0' ) KETERANGAN_GAGAL,
				( SELECT KETERANGAN FROM REKANAN_EVAL_TEKNIS_TAWAR WHERE PAKET_REKANAN_ID = ".$paket_rekanan_id." AND MEMENUHI_SYARAT = '1' ) KETERANGAN_LULUS,
				( SELECT NILAI_TEKNIS FROM REKANAN_EVAL_TEKNIS_TAWAR WHERE PAKET_REKANAN_ID = ".$paket_rekanan_id." ) NILAI_TEKNIS
	    ";
      // ( SELECT NILAI_TEKNIS FROM REKANAN_EVAL_TEKNIS_TAWAR WHERE PAKET_REKANAN_ID = ".$paket_rekanan_id." AND MEMENUHI_SYARAT = '1' ) NILAI_TEKNIS

		$this->query = $str;
		return $this->selectLimit($str,-1,-1);
    }
	
    function getStatus($paramsArray=array())
	{
		$str = "SELECT STATUS AS ROWCOUNT FROM REKANAN_EVAL_TEKNIS_TAWAR WHERE REKANAN_EVAL_TEKNIS_TAWAR_ID IS NOT NULL "; 
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
	
	function getStatusTotal($paramsArray=array(),$stat='')
	{
		$str = "SELECT COUNT(STATUS) AS ROWCOUNT FROM REKANAN_EVAL_TEKNIS_TAWAR WHERE REKANAN_EVAL_TEKNIS_TAWAR_ID IS NOT NULL "; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $stat;
		$this->query = $str;
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }

    function getMemenuhiSyarat($paramsArray=array())
	{
		$str = "SELECT MEMENUHI_SYARAT AS ROWCOUNT FROM REKANAN_EVAL_TEKNIS_TAWAR WHERE REKANAN_EVAL_TEKNIS_TAWAR_ID IS NOT NULL "; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return ""; 
    }

	
	
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(REKANAN_EVAL_TEKNIS_TAWAR_ID) AS ROWCOUNT FROM REKANAN_EVAL_TEKNIS_TAWAR WHERE REKANAN_EVAL_TEKNIS_TAWAR_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(REKANAN_EVAL_TEKNIS_TAWAR_ID) AS ROWCOUNT FROM REKANAN_EVAL_TEKNIS_TAWAR WHERE REKANAN_EVAL_TEKNIS_TAWAR_ID IS NOT NULL "; 
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