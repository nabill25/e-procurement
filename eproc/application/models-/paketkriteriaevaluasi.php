<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketKriteriaEvaluasi extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PaketKriteriaEvaluasi()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_KRITERIA_EVAL_ID", $this->getNextId("PAKET_KRITERIA_EVAL_ID","PAKET_KRITERIA_EVAL")); 

		$str = "
				INSERT INTO PAKET_KRITERIA_EVAL (
				   PAKET_KRITERIA_EVAL_ID, PAKET_ID, SKK, 
				   SALDO, KEMAMPUAN_DASAR, BIDANG_KERJA, 
				   NILAI_KONTRAK, STATUS_PENYEDIA, PERSONIL, 
				   PERALATAN, SERTIFIKAT_LAIN) 
				VALUES (".$this->getField("PAKET_KRITERIA_EVAL_ID").", ".$this->getField("PAKET_ID").", ".$this->getField("SKK").", 
				   ".$this->getField("SALDO").", ".$this->getField("KEMAMPUAN_DASAR").", ".$this->getField("BIDANG_KERJA").", 
				   ".$this->getField("NILAI_KONTRAK").", ".$this->getField("STATUS_PENYEDIA").", ".$this->getField("PERSONIL").", 
				   ".$this->getField("PERALATAN").", ".$this->getField("SERTIFIKAT_LAIN").")		
				"; 
				
		$this->query = $str;
		//echo $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_KRITERIA_EVAL
				SET    SKK                    = '".$this->getField("SKK")."',
					   SALDO                  = '".$this->getField("SALDO")."',
					   KEMAMPUAN_DASAR        = '".$this->getField("KEMAMPUAN_DASAR")."',
					   BIDANG_KERJA           = '".$this->getField("BIDANG_KERJA")."',
					   NILAI_KONTRAK          = '".$this->getField("NILAI_KONTRAK")."',
					   STATUS_PENYEDIA        = '".$this->getField("STATUS_PENYEDIA")."',
					   PERSONIL               = '".$this->getField("PERSONIL")."',
					   PERALATAN              = '".$this->getField("PERALATAN")."',
					   SERTIFIKAT_LAIN        = '".$this->getField("SERTIFIKAT_LAIN")."'
				WHERE  PAKET_ID = ".$this->getField("PAKET_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PAKET_KRITERIA_EVAL
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
				PAKET_KRITERIA_EVAL_ID, PAKET_ID, SKK, 
				   SALDO, KEMAMPUAN_DASAR, BIDANG_KERJA, 
				   NILAI_KONTRAK, STATUS_PENYEDIA, PERSONIL, 
				   PERALATAN, SERTIFIKAT_LAIN
				FROM PAKET_KRITERIA_EVAL WHERE PAKET_KRITERIA_EVAL_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_KRITERIA_EVAL_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PAKET_KRITERIA_EVAL_ID, NAMA
				FROM PAKET_KRITERIA_EVAL WHERE PAKET_KRITERIA_EVAL_ID IS NOT NULL"; 
		
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
		$str = "SELECT COUNT(PAKET_KRITERIA_EVAL_ID) AS ROWCOUNT FROM PAKET_KRITERIA_EVAL WHERE PAKET_KRITERIA_EVAL_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(PAKET_KRITERIA_EVAL_ID) AS ROWCOUNT FROM PAKET_KRITERIA_EVAL WHERE PAKET_KRITERIA_EVAL_ID IS NOT NULL "; 
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