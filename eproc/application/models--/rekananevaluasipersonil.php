<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class RekananEvaluasiPersonil extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function RekananEvaluasiPersonil()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_EVAL_PERSONIL_ID", $this->getNextId("REKANAN_EVAL_PERSONIL_ID","REKANAN_EVAL_PERSONIL")); 

		$str = "INSERT INTO REKANAN_EVAL_PERSONIL (
				   REKANAN_EVAL_PERSONIL_ID, PAKET_EVAL_PERSONIL_ID, PAKET_REKANAN_ID, 
  				   REKANAN_TENAGA_AHLI_ID) 
				VALUES (
				  ".$this->getField("REKANAN_EVAL_PERSONIL_ID").",
				  ".$this->getField("PAKET_EVAL_PERSONIL_ID").",
				  ".$this->getField("PAKET_REKANAN_ID").",
				  '".$this->getField("REKANAN_TENAGA_AHLI_ID")."'
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_EVAL_PERSONIL SET
				  REKANAN_EVAL_PERSONIL_ID = '".$this->getField("REKANAN_EVAL_PERSONIL_ID")."'
				WHERE REKANAN_EVAL_PERSONIL_ID = '".$this->getField("REKANAN_EVAL_PERSONIL_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updatePenilaianPersonil()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_EVAL_PERSONIL SET
				  KESESUAIAN = '".$this->getField("KESESUAIAN")."',
				  KESESUAIAN_NILAI = '".$this->getField("KESESUAIAN_NILAI")."'
				WHERE REKANAN_EVAL_PERSONIL_ID = '".$this->getField("REKANAN_EVAL_PERSONIL_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updatePenilaianPersonilTotal()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_EVAL_PERSONIL SET
				  KESESUAIAN_TOTAL = '".$this->getField("KESESUAIAN_TOTAL")."'
				WHERE PAKET_EVAL_PERSONIL_ID = '".$this->getField("PAKET_EVAL_PERSONIL_ID")."' AND
					  PAKET_REKANAN_ID = '".$this->getField("PAKET_REKANAN_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
			
    function updateByField()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_EVAL_PERSONIL A SET
				  ".$this->getField("FIELD")." = ".$this->getField("FIELD_VALUE")."
				WHERE PAKET_REKANAN_ID = ".$this->getField("PAKET_REKANAN_ID")."
				"; 
				$this->query = $str;

		return $this->execQuery($str);
    }	
		
	function delete()
	{
        $str = "DELETE FROM REKANAN_EVAL_PERSONIL
                WHERE 
                  PAKET_REKANAN_ID = '".$this->getField("PAKET_REKANAN_ID")."'"; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","REKANAN_EVAL_PERSONIL_ID"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = " SELECT REKANAN_EVAL_PERSONIL_ID, PAKET_EVAL_PERSONIL_ID, PAKET_REKANAN_ID, 
                     A.REKANAN_TENAGA_AHLI_ID, B.NAMA TENAGA_AHLI, A.NILAI, KESESUAIAN, COALESCE(KESESUAIAN_NILAI, 0) KESESUAIAN_NILAI, KESESUAIAN_TOTAL
                FROM REKANAN_EVAL_PERSONIL A, REKANAN_TENAGA_AHLI B 
                WHERE A.REKANAN_TENAGA_AHLI_ID = B.REKANAN_TENAGA_AHLI_ID AND REKANAN_EVAL_PERSONIL_ID IS NOT NULL "; 
		
		
/*		$str = " 
		SELECT REKANAN_EVAL_PERSONIL_ID,
			   PAKET_EVAL_PERSONIL_ID,
			   PAKET_REKANAN_ID,
			   A.REKANAN_TENAGA_AHLI_ID,
			   B.NAMA TENAGA_AHLI,
			   A.NILAI,
			   C.PENDIDIKAN,
			   C.JURUSAN,
			   D.POSISI,
			   D.PENGALAMAN,
			   D.PEKERJAAN,
			   E.KEAHLIAN,
			   E.NOMOR
		  FROM REKANAN_EVAL_PERSONIL A, REKANAN_TENAGA_AHLI B, REKANAN_TENAGA_AHLI_PEND C, REKANAN_TENAGA_AHLI_PENG D, REKANAN_TENAGA_AHLI_SERT E
		 WHERE     A.REKANAN_TENAGA_AHLI_ID = B.REKANAN_TENAGA_AHLI_ID
			   AND A.REKANAN_TENAGA_AHLI_ID = C.REKANAN_TENAGA_AHLI_ID
			   AND A.REKANAN_TENAGA_AHLI_ID = D.REKANAN_TENAGA_AHLI_ID
			   AND A.REKANAN_TENAGA_AHLI_ID = E.REKANAN_TENAGA_AHLI_ID
			   AND REKANAN_EVAL_PERSONIL_ID IS NOT NULL";*/
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_EVAL_PERSONIL_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsCetak($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{

		$str = "SELECT REKANAN_EVAL_PERSONIL_ID, PAKET_EVAL_PERSONIL_ID, PAKET_REKANAN_ID, 
                     A.REKANAN_TENAGA_AHLI_ID, B.NAMA TENAGA_AHLI, A.NILAI, 
                     (SELECT JURUSAN FROM REKANAN_TENAGA_AHLI_PEND X WHERE A.REKANAN_TENAGA_AHLI_ID = X.REKANAN_TENAGA_AHLI_ID AND PENDIDIKAN = 1 AND ROWNUM = 1) JURUSAN,
                     (SELECT SUM(PERIODE) FROM REKANAN_TENAGA_AHLI_PENG X WHERE A.REKANAN_TENAGA_AHLI_ID = X.REKANAN_TENAGA_AHLI_ID AND ROWNUM = 1) PENGALAMAN,
                     (SELECT KEAHLIAN FROM REKANAN_TENAGA_AHLI_SERT X WHERE A.REKANAN_TENAGA_AHLI_ID = X.REKANAN_TENAGA_AHLI_ID AND ROWNUM = 1) SERTIFIKAT, KESESUAIAN_NILAI, KESESUAIAN_TOTAL 
                FROM REKANAN_EVAL_PERSONIL A, REKANAN_TENAGA_AHLI B WHERE A.REKANAN_TENAGA_AHLI_ID = B.REKANAN_TENAGA_AHLI_ID AND REKANAN_EVAL_PERSONIL_ID IS NOT NULL ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_EVAL_PERSONIL_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT REKANAN_EVAL_PERSONIL_ID, REKANAN_EVAL_PERSONIL_ID
				FROM REKANAN_EVAL_PERSONIL WHERE REKANAN_EVAL_PERSONIL_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_EVAL_PERSONIL_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","REKANAN_EVAL_PERSONIL_ID"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array(), $statement="")
	{
		$str = "SELECT COUNT(1) AS ROWCOUNT 
				FROM REKANAN_EVAL_PERSONIL A, REKANAN_TENAGA_AHLI B 
				WHERE A.REKANAN_TENAGA_AHLI_ID = B.REKANAN_TENAGA_AHLI_ID AND REKANAN_EVAL_PERSONIL_ID IS NOT NULL ".$statement; 
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

    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(REKANAN_EVAL_PERSONIL_ID) AS ROWCOUNT FROM REKANAN_EVAL_PERSONIL WHERE REKANAN_EVAL_PERSONIL_ID IS NOT NULL "; 
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