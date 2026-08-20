<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class EvaluasiSyaratDaftar extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
		parent::__construct();
	}
	
    function EvaluasiSyaratDaftar()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		//$this->setField("EVALUASI_NUMBER", $this->getNextId("EVALUASI_NUMBER","EVAL_SYARAT_DAFTAR")); 

		$str = "INSERT INTO EVAL_SYARAT_DAFTAR (
				   EVALUASI_NUMBER, NAMA, TIPE) 
				VALUES (
				  ".$this->getField("EVALUASI_NUMBER").",
				  '".$this->getField("NAMA")."',
				  '".$this->getField("TIPE")."'
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE EVAL_SYARAT_DAFTAR SET
				  NAMA= '".$this->getField("NAMA")."',
				  TIPE= '".$this->getField("TIPE")."'
				WHERE EVALUASI_NUMBER = '".$this->getField("EVALUASI_NUMBER")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM EVAL_SYARAT_DAFTAR
                WHERE 
                  EVALUASI_NUMBER = '".$this->getField("EVALUASI_NUMBER")."'"; 
				  
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
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $sOrder="ORDER BY NAMA ASC")
	{
		$str = "SELECT EVALUASI_NUMBER, NAMA, TIPE
				FROM EVAL_SYARAT_DAFTAR WHERE EVALUASI_NUMBER IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$sOrder;
		$this->query = $str;
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsEvaluasiPaket($paketid, $statement="",$limit=-1,$from=-1, $sOrder=" ORDER BY A.EVALUASI_NUMBER ASC ")
	{
		$str = "SELECT A.EVALUASI_NUMBER, A.NAMA, A.TIPE, B.EVALUASI_NUMBER EVALUASI_NUMBER_PAKET, B.KETERANGAN, PAKET_FIELD_NAME, PAKET_FIELD_INFO,
        CASE 
            WHEN A.EVALUASI_NUMBER = 6 THEN SYARAT_REKENING_KORAN_BULAN
            WHEN A.EVALUASI_NUMBER = 8 THEN SYARAT_KEUANGAN_PPN_BULAN           
            WHEN A.EVALUASI_NUMBER = 9 THEN SYARAT_KEUANGAN_PPH_BULAN          
            WHEN A.EVALUASI_NUMBER = 5 THEN SYARAT_TEKNIS_SERTIFIKAT_INFO
            WHEN A.EVALUASI_NUMBER = 11 THEN SYARAT_ADM_KUALIFIKASI_INFO
        END INFO_LAIN
				FROM EVAL_SYARAT_DAFTAR A LEFT JOIN PAKET_EVAL_SYARAT_DAFTAR B ON A.EVALUASI_NUMBER = B.EVALUASI_NUMBER AND B.PAKET_ID = '".$paketid."' 
                LEFT JOIN PAKET C ON B.PAKET_ID = C.PAKET_ID
			    WHERE A.EVALUASI_NUMBER IS NOT NULL AND AKTIF='1' "; 
		
		$str .= $statement." ".$sOrder;
		$this->query = $str;
				
		return $this->selectLimit($str,$limit,$from); 
    }
    // ikn 20190925
 //    function selectByParamsEvaluasiPaket($paketid, $statement="",$limit=-1,$from=-1, $sOrder=" ORDER BY A.EVALUASI_NUMBER ASC ")
	// {
	// 	$str = "SELECT A.EVALUASI_NUMBER, A.NAMA, A.TIPE, B.EVALUASI_NUMBER EVALUASI_NUMBER_PAKET, B.KETERANGAN, PAKET_FIELD_NAME, PAKET_FIELD_INFO,
 //        CASE 
 //            WHEN A.EVALUASI_NUMBER = 6 THEN SYARAT_REKENING_KORAN_BULAN
 //            WHEN A.EVALUASI_NUMBER = 8 THEN SYARAT_KEUANGAN_PPN_BULAN           
 //            WHEN A.EVALUASI_NUMBER = 9 THEN SYARAT_KEUANGAN_PPH_BULAN          
 //            WHEN A.EVALUASI_NUMBER = 5 THEN SYARAT_TEKNIS_SERTIFIKAT_INFO
 //            WHEN A.EVALUASI_NUMBER = 11 THEN SYARAT_ADM_KUALIFIKASI_INFO
 //        END INFO_LAIN
	// 			FROM EVAL_SYARAT_DAFTAR A LEFT JOIN PAKET_EVAL_SYARAT_DAFTAR B ON A.EVALUASI_NUMBER = B.EVALUASI_NUMBER 
 //                LEFT JOIN PAKET C ON B.PAKET_ID = C.PAKET_ID
	// 		    WHERE A.EVALUASI_NUMBER IS NOT NULL AND B.PAKET_ID = '".$paketid."' "; 
	// 	$str .= $statement." ".$sOrder;
	// 	$this->query = $str;
				
	// 	return $this->selectLimit($str,$limit,$from); 
 //    }
	    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT EVALUASI_NUMBER, NAMA, TIPE
				FROM EVAL_SYARAT_DAFTAR WHERE EVALUASI_NUMBER IS NOT NULL"; 
		
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
		$str = "SELECT COUNT(1) AS ROWCOUNT FROM EVAL_SYARAT_DAFTAR WHERE EVALUASI_NUMBER IS NOT NULL "; 
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
		$str = "SELECT COUNT(1) AS ROWCOUNT FROM EVAL_SYARAT_DAFTAR WHERE EVALUASI_NUMBER IS NOT NULL "; 
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
	
	function getMaxData($paramsArray=array(), $varStatement="")
	{
		$str = "SELECT COALESCE(MAX(EVALUASI_NUMBER),0) + 1 AS ROWCOUNT FROM EVAL_SYARAT_DAFTAR WHERE 1=1 "; 
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
	
  } 
?>