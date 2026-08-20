<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class RekananEvaluasiKeuangan extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function RekananEvaluasiKeuangan()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_EVAL_KEUANGAN_ID", $this->getNextId("REKANAN_EVAL_KEUANGAN_ID","REKANAN_EVAL_KEUANGAN")); 

		$str = "
				INSERT INTO REKANAN_EVAL_KEUANGAN (
						   REKANAN_EVAL_KEUANGAN_ID, PAKET_EVAL_KEUANGAN_ID, PAKET_REKANAN_ID, 
						   KUALIFIKASI, KB, FL, 
						   MK, FP, KK, 
						   NK, PROGRESS, PRESTASI, 
						   SKK, REKENING_KORAN		  				  
				  ) 
				VALUES (
				  ".$this->getField("REKANAN_EVAL_KEUANGAN_ID").",
				  ".$this->getField("PAKET_EVAL_KEUANGAN_ID").",
				  ".$this->getField("PAKET_REKANAN_ID").",
				  '".$this->getField("KUALIFIKASI")."',
				  '".$this->getField("KB")."',
				  '".$this->getField("FL")."',
				  '".$this->getField("MK")."',
				  '".$this->getField("FP")."',
				  '".$this->getField("KK")."',
				  '".$this->getField("NK")."',
				  '".$this->getField("PROGRESS")."',
				  '".$this->getField("PRESTASI")."',
				  '".$this->getField("SKK")."',
				  '".$this->getField("REKENING_KORAN")."'
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_EVAL_KEUANGAN SET
				  KUALIFIKASI = '".$this->getField("KUALIFIKASI")."',
				  KB = '".$this->getField("KB")."',
				  FL = '".$this->getField("FL")."',
				  MK = '".$this->getField("MK")."',
				  FP = '".$this->getField("FP")."',
				  KK = '".$this->getField("KK")."',
				  NK = '".$this->getField("NK")."',
				  PROGRESS = '".$this->getField("PROGRESS")."',
				  PRESTASI = '".$this->getField("PRESTASI")."',
				  SKK = '".$this->getField("SKK")."',
				  REKENING_KORAN = '".$this->getField("REKENING_KORAN")."'				 
				WHERE PAKET_REKANAN_ID = '".$this->getField("PAKET_REKANAN_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updateByField()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_EVAL_KEUANGAN A SET
				  ".$this->getField("FIELD")." = ".$this->getField("FIELD_VALUE")."
				WHERE PAKET_REKANAN_ID = ".$this->getField("PAKET_REKANAN_ID")."
				"; 
				$this->query = $str;
	
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM REKANAN_EVAL_KEUANGAN
                WHERE 
                  REKANAN_EVAL_KEUANGAN_ID = '".$this->getField("REKANAN_EVAL_KEUANGAN_ID")."'"; 
				  
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
		$str = "SELECT A.REKANAN_EVAL_KEUANGAN_ID, A.PAKET_EVAL_KEUANGAN_ID, A.PAKET_REKANAN_ID, 
						   A.KUALIFIKASI, KB, A.FL, 
						   A.MK, A.FP, A.KK, 
						   A.NK, A.PROGRESS, A.PRESTASI, 
						   A.SKK, A.LULUS_SKK_NILAI, A.REKENING_KORAN, A.LULUS_REKENING_KORAN, NILAI_LULUS, B.SKK1NILAI
				 FROM REKANAN_EVAL_KEUANGAN A, PAKET_EVAL_KEUANGAN B 
                WHERE REKANAN_EVAL_KEUANGAN_ID  IS NOT NULL AND A.PAKET_EVAL_KEUANGAN_ID = B.PAKET_EVAL_KEUANGAN_ID "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die();
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_EVAL_KEUANGAN_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsSKK($paramsArray=array(),$limit=-1,$from=-1, $paket_id='')
	{

		$str = "SELECT A.*, SKK1NILAI * (COALESCE(NILAI_1, 0) + COALESCE(NILAI_2, 0) + COALESCE(NILAI_3, 0)) / 100 NILAI_FINAL,
						CASE WHEN LULUS_SKK_NILAI IS NULL THEN 
							CASE WHEN SKK1NILAI * (COALESCE(NILAI_1, 0) + COALESCE(NILAI_2, 0) + COALESCE(NILAI_3, 0)) / 100 < NILAI_LULUS THEN 'Tidak Lulus' ELSE 'Lulus'
							END 
						ELSE 
							CASE WHEN LULUS_SKK_NILAI >= NILAI_LULUS THEN 'Lulus' ELSE 'Tidak Lulus'
							END
						END KETERANGAN
				FROM 
				(
				SELECT REKANAN_EVAL_KEUANGAN_ID, A.PAKET_EVAL_KEUANGAN_ID, PAKET_REKANAN_ID, 
						   KUALIFIKASI, KB, A.FL, 
						   MK, A.FP, KK, 
						   NK, PROGRESS, PRESTASI, 
						   SKK, LULUS_SKK_NILAI, REKENING_KORAN, LULUS_REKENING_KORAN,
                 (SELECT SKK1PERSEN FROM PAKET_EVAL_KEUANGAN WHERE PAKET_ID = ".$paket_id." AND A.SKK >= SKK1RP) NILAI_1,
				 (SELECT SKK2PERSEN FROM PAKET_EVAL_KEUANGAN WHERE PAKET_ID = ".$paket_id." AND A.SKK BETWEEN SKK2RPMIN AND SKK2RPMAX) NILAI_2,
				 (SELECT SKK3PERSEN FROM PAKET_EVAL_KEUANGAN WHERE PAKET_ID = ".$paket_id." AND A.SKK <= SKK3RP) NILAI_3,
                 SKK1NILAI, NILAI_LULUS
                FROM REKANAN_EVAL_KEUANGAN A, PAKET_EVAL_KEUANGAN B 
                WHERE REKANAN_EVAL_KEUANGAN_ID  IS NOT NULL AND A.PAKET_EVAL_KEUANGAN_ID = B.PAKET_EVAL_KEUANGAN_ID) A  WHERE 1 = 1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= " ORDER BY REKANAN_EVAL_KEUANGAN_ID ASC";
		
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT REKANAN_EVAL_KEUANGAN_ID, NAMA
				FROM REKANAN_EVAL_KEUANGAN WHERE REKANAN_EVAL_KEUANGAN_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_EVAL_KEUANGAN_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(REKANAN_EVAL_KEUANGAN_ID) AS ROWCOUNT FROM REKANAN_EVAL_KEUANGAN WHERE REKANAN_EVAL_KEUANGAN_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(REKANAN_EVAL_KEUANGAN_ID) AS ROWCOUNT FROM REKANAN_EVAL_KEUANGAN WHERE REKANAN_EVAL_KEUANGAN_ID IS NOT NULL "; 
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