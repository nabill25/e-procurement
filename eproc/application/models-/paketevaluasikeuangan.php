<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketEvaluasiKeuangan extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PaketEvaluasiKeuangan()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_EVAL_KEUANGAN_ID", $this->getNextId("PAKET_EVAL_KEUANGAN_ID","PAKET_EVAL_KEUANGAN")); 

		$str = "
				INSERT INTO PAKET_EVAL_KEUANGAN (
				   PAKET_EVAL_KEUANGAN_ID, PAKET_ID, FP, 
				   FL, SKK1RP, SKK1PERSEN, 
				   SKK1NILAI, SKK2RPMIN, SKK2RPMAX, 
				   SKK2PERSEN, SKK3RP, 
				   SKK3PERSEN, NILAI_LULUS, 
				   FPB, FLB, REKENING_BULAN, 
				   SALDO_REK_MIN) 
				VALUES (".$this->getField("PAKET_EVAL_KEUANGAN_ID").", ".$this->getField("PAKET_ID").", ".$this->getField("FP").", 
				   ".$this->getField("FL").", ".$this->getField("SKK1RP").", ".$this->getField("SKK1PERSEN").", 				   
				   ".$this->getField("SKK1NILAI").", ".$this->getField("SKK2RPMIN").", ".$this->getField("SKK2RPMAX").", 
				   ".$this->getField("SKK2PERSEN").", ".$this->getField("SKK3RP").", 
				   ".$this->getField("SKK3PERSEN").", ".$this->getField("NILAI_LULUS").", 
				   ".$this->getField("FPB").", ".$this->getField("FLB").", '".$this->getField("REKENING_BULAN")."', 
				   ".$this->getField("SALDO_REK_MIN").") "; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_EVAL_KEUANGAN SET
				FP = '".$this->getField("FP")."',
				FL = '".$this->getField("FL")."',
				SKK1RP = '".$this->getField("SKK1RP")."',
				SKK1PERSEN = '".$this->getField("SKK1PERSEN")."',
				SKK1NILAI = '".$this->getField("SKK1NILAI")."',
				SKK2RPMIN = '".$this->getField("SKK2RPMIN")."',
				SKK2RPMAX = '".$this->getField("SKK2RPMAX")."',
				SKK2PERSEN = '".$this->getField("SKK2PERSEN")."',
				SKK3RP = '".$this->getField("SKK3RP")."',
				SKK3PERSEN = '".$this->getField("SKK3PERSEN")."',
				NILAI_LULUS = '".$this->getField("NILAI_LULUS")."',
				FPB = '".$this->getField("FPB")."',
				FLB = '".$this->getField("FLB")."',
				REKENING_BULAN = '".$this->getField("REKENING_BULAN")."',
				SALDO_REK_MIN = ".$this->getField("SALDO_REK_MIN")."
				WHERE PAKET_ID = '".$this->getField("PAKET_ID")."'
				"; 
				$this->query = $str;
		
			
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PAKET_EVAL_KEUANGAN
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
						PAKET_EVAL_KEUANGAN_ID, PAKET_ID, FP, 
						FL, SKK1RP, SKK1PERSEN, 
						SKK1NILAI, SKK2RPMIN, SKK2RPMAX, 
						SKK2PERSEN, SKK2NILAI, SKK3RP, 
						SKK3PERSEN, SKK3NILAI, NILAI_LULUS, 
						FPB, FLB, REKENING_BULAN, 
						SALDO_REK_MIN
				FROM PAKET_EVAL_KEUANGAN WHERE PAKET_EVAL_KEUANGAN_ID IS NOT NULL "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_EVAL_KEUANGAN_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT AGAMA_ID, NAMA
				FROM AGAMA WHERE AGAMA_ID IS NOT NULL"; 
		
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
		$str = "SELECT COUNT(AGAMA_ID) AS ROWCOUNT FROM AGAMA WHERE AGAMA_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(AGAMA_ID) AS ROWCOUNT FROM AGAMA WHERE AGAMA_ID IS NOT NULL "; 
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