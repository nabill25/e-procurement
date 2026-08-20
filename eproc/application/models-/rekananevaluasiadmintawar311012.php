<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class RekananEvaluasiAdminTawar extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function RekananEvaluasiAdminTawar()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_EVAL_ADMIN_TAWAR_ID", $this->getNextId("REKANAN_EVAL_ADMIN_TAWAR_ID","REKANAN_EVAL_ADMIN_TAWAR")); 

		$str = "INSERT INTO REKANAN_EVAL_ADMIN_TAWAR (
				   REKANAN_EVAL_ADMIN_TAWAR_ID, NAMA) 
				VALUES (
				  ".$this->getField("REKANAN_EVAL_ADMIN_TAWAR_ID").",
				  '".$this->getField("NAMA")."'
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

	function insertStatus()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_EVAL_ADMIN_TAWAR_ID", $this->getNextId("REKANAN_EVAL_ADMIN_TAWAR_ID","REKANAN_EVAL_ADMIN_TAWAR")); 

		$str = "
				INSERT INTO REKANAN_EVAL_ADMIN_TAWAR (
				   REKANAN_EVAL_ADMIN_TAWAR_ID, PAKET_REKANAN_ID, PAKET_EVAL_ADMIN_TAWAR_ID, 
				   URAIAN, STATUS) 
				VALUES (".$this->getField("REKANAN_EVAL_ADMIN_TAWAR_ID").", 
						".$this->getField("PAKET_REKANAN_ID").", 
						".$this->getField("PAKET_EVAL_ADMIN_TAWAR_ID").", 
				   		'', 1)						
				"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

	function insertUraian()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_EVAL_ADMIN_TAWAR_ID", $this->getNextId("REKANAN_EVAL_ADMIN_TAWAR_ID","REKANAN_EVAL_ADMIN_TAWAR")); 

		$str = "
				INSERT INTO REKANAN_EVAL_ADMIN_TAWAR (
				   REKANAN_EVAL_ADMIN_TAWAR_ID, PAKET_REKANAN_ID, PAKET_EVAL_ADMIN_TAWAR_ID, 
				   URAIAN, STATUS) 
				VALUES (".$this->getField("REKANAN_EVAL_ADMIN_TAWAR_ID").", 
						".$this->getField("PAKET_REKANAN_ID").", 
						".$this->getField("PAKET_EVAL_ADMIN_TAWAR_ID").", 
				   		'".$this->getField("URAIAN")."', NULL)						
				"; 

				
		$this->query = $str;
		return $this->execQuery($str);
    }


    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_EVAL_ADMIN_TAWAR A SET
				  STATUS = ".$this->getField("STATUS")."
				WHERE PAKET_REKANAN_ID = '".$this->getField("PAKET_REKANAN_ID")."' AND PAKET_EVAL_ADMIN_TAWAR_ID = '".$this->getField("PAKET_EVAL_ADMIN_TAWAR_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updateUraian()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_EVAL_ADMIN_TAWAR SET
				  URAIAN = '".$this->getField("URAIAN")."'
				WHERE PAKET_REKANAN_ID = '".$this->getField("PAKET_REKANAN_ID")."' AND PAKET_EVAL_ADMIN_TAWAR_ID = '".$this->getField("PAKET_EVAL_ADMIN_TAWAR_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM REKANAN_EVAL_ADMIN_TAWAR
                WHERE 
                  REKANAN_EVAL_ADMIN_TAWAR_ID = '".$this->getField("REKANAN_EVAL_ADMIN_TAWAR_ID")."'"; 
				  
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
				PAKET_REKANAN_ID, PAKET_EVAL_ADMIN_TAWAR_ID, 
				   URAIAN
				FROM REKANAN_EVAL_ADMIN_TAWAR WHERE REKANAN_EVAL_ADMIN_TAWAR_ID IS NOT NULL  "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_EVAL_ADMIN_TAWAR_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectMemenuhiSyarat($paket_id,$paket_rekanan_id)
	{
		$str = "
				SELECT (SELECT COUNT(*) FROM PAKET_EVAL_ADMIN_TAWAR WHERE PAKET_ID = ".$paket_id.") JUMLAH_EVALUASI,
        		(SELECT COUNT(*) FROM REKANAN_EVAL_ADMIN_TAWAR WHERE PAKET_REKANAN_ID = ".$paket_rekanan_id." AND STATUS = 1) JUMLAH_DILENGKAPI
				FROM DUAL
	    "; 
		
		$this->query = $str;
		return $this->selectLimit($str,-1,-1); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT REKANAN_EVAL_ADMIN_TAWAR_ID, NAMA
				FROM REKANAN_EVAL_ADMIN_TAWAR WHERE REKANAN_EVAL_ADMIN_TAWAR_ID IS NOT NULL"; 
		
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


    function getUraian($paramsArray=array())
	{
		$str = "SELECT URAIAN AS ROWCOUNT FROM REKANAN_EVAL_ADMIN_TAWAR WHERE REKANAN_EVAL_ADMIN_TAWAR_ID IS NOT NULL "; 
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
	
    function getStatus($paramsArray=array())
	{
		$str = "SELECT STATUS AS ROWCOUNT FROM REKANAN_EVAL_ADMIN_TAWAR WHERE REKANAN_EVAL_ADMIN_TAWAR_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(REKANAN_EVAL_ADMIN_TAWAR_ID) AS ROWCOUNT FROM REKANAN_EVAL_ADMIN_TAWAR WHERE REKANAN_EVAL_ADMIN_TAWAR_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(REKANAN_EVAL_ADMIN_TAWAR_ID) AS ROWCOUNT FROM REKANAN_EVAL_ADMIN_TAWAR WHERE REKANAN_EVAL_ADMIN_TAWAR_ID IS NOT NULL "; 
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