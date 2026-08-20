<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketEvaluasiPeralatanDetil extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PaketEvaluasiPeralatanDetil()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_EVAL_PERALATAN_DETIL_ID", $this->getNextId("PAKET_EVAL_PERALATAN_DETIL_ID","PAKET_EVAL_PERALATAN_DETIL")); 

		$str = "
		INSERT INTO PAKET_EVAL_PERALATAN_DETIL (
				PAKET_EVAL_PERALATAN_DETIL_ID, PAKET_ID, NAMA, 
		 		KETERANGAN, NILAI) 
				VALUES (
				  ".$this->getField("PAKET_EVAL_PERALATAN_DETIL_ID").",
				  ".$this->getField("PAKET_ID").",
				  '".$this->getField("NAMA")."',
				  '".$this->getField("KETERANGAN")."',
				  '".$this->getField("NILAI")."'
				)"; 
		$this->id = $this->getField("PAKET_EVAL_PERALATAN_DETIL_ID");	
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_EVAL_PERALATAN_DETIL SET
				  NAMA = '".$this->getField("NAMA")."'
				WHERE PAKET_EVAL_PERALATAN_DETIL_ID = '".$this->getField("PAKET_EVAL_PERALATAN_DETIL_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function updatePeralatan()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_EVAL_PERALATAN_DETIL SET
				  NAMA = '".$this->getField("NAMA")."',
				  KETERANGAN = '".$this->getField("KETERANGAN")."', 
				  NILAI = '".$this->getField("NILAI")."'
				WHERE PAKET_EVAL_PERALATAN_DETIL_ID = '".$this->getField("PAKET_EVAL_PERALATAN_DETIL_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PAKET_EVAL_PERALATAN_DETIL
                WHERE 
                  PAKET_ID = '".$this->getField("PAKET_ID")."'"; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

	function deleteNotIn()
	{
        $str = "DELETE FROM PAKET_EVAL_PERALATAN_DETIL
                WHERE 
                  PAKET_ID = '".$this->getField("PAKET_ID")."' AND PAKET_EVAL_PERALATAN_DETIL_ID NOT IN (".$this->getField("PAKET_EVAL_PERALATAN_DETIL_ID").") "; 
				  
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
		$str = "SELECT PAKET_EVAL_PERALATAN_DETIL_ID, PAKET_ID, NAMA, 
		 		KETERANGAN, NILAI,
                (SELECT COUNT(NAMA) FROM PAKET_EVAL_PERALATAN_DETIL X WHERE X.PAKET_ID = A.PAKET_ID) JUMLAH_PERALATAN
				FROM PAKET_EVAL_PERALATAN_DETIL A WHERE PAKET_EVAL_PERALATAN_DETIL_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_EVAL_PERALATAN_DETIL_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
	
	function selectByParamsCetak($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT A.PAKET_EVAL_PERALATAN_DETIL_ID, PAKET_ID, NAMA, 
					KETERANGAN, A.NILAI,
					(SELECT COUNT(NAMA) 
					FROM PAKET_EVAL_PERALATAN_DETIL X 
					WHERE X.PAKET_ID = A.PAKET_ID) JUMLAH_PERALATAN, B.KESESUAIAN_TOTAL, B.NILAI NILAI_FINAL
				FROM PAKET_EVAL_PERALATAN_DETIL A 
				LEFT JOIN REKANAN_EVAL_PERALATAN B ON A.PAKET_EVAL_PERALATAN_DETIL_ID = B.PAKET_EVAL_PERALATAN_DETIL_ID
				WHERE 1=1"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_EVAL_PERALATAN_DETIL_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PAKET_EVAL_PERALATAN_DETIL_ID, PAKET_ID, NAMA, 
		 		KETERANGAN, NILAI
				FROM PAKET_EVAL_PERALATAN_DETIL WHERE PAKET_EVAL_PERALATAN_DETIL_ID IS NOT NULL"; 
		
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
		$str = "SELECT COUNT(PAKET_EVAL_PERALATAN_DETIL_ID) AS ROWCOUNT FROM PAKET_EVAL_PERALATAN_DETIL WHERE PAKET_EVAL_PERALATAN_DETIL_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(PAKET_EVAL_PERALATAN_DETIL_ID) AS ROWCOUNT FROM PAKET_EVAL_PERALATAN_DETIL WHERE PAKET_EVAL_PERALATAN_DETIL_ID IS NOT NULL "; 
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