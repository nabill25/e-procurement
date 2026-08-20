<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketEvaluasiSertifikatLain extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PaketEvaluasiSertifikatLain()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_EVAL_SERTIFIKAT_LAIN_ID", $this->getNextId("PAKET_EVAL_SERTIFIKAT_LAIN_ID","PAKET_EVAL_SERTIFIKAT_LAIN")); 

		$str = "
		INSERT INTO PAKET_EVAL_SERTIFIKAT_LAIN (
		   PAKET_EVAL_SERTIFIKAT_LAIN_ID, PAKET_ID, NAMA, KETERANGAN, 
		   NILAI_MINIMUM, NILAI)
		   VALUES (".$this->getField("PAKET_EVAL_SERTIFIKAT_LAIN_ID").", ".$this->getField("PAKET_ID").", '".$this->getField("NAMA")."', '".$this->getField("KETERANGAN")."', 
				   '".$this->getField("NILAI_MINIMUM")."', '".$this->getField("NILAI")."')
				"; 
		/* $str = "
		INSERT INTO PAKET_EVAL_SERTIFIKAT_LAIN (
		   PAKET_EVAL_SERTIFIKAT_LAIN_ID, PAKET_ID, NILAI_MINIMUM)
		   VALUES (".$this->getField("PAKET_EVAL_SERTIFIKAT_LAIN_ID").", ".$this->getField("PAKET_ID").", ".$this->getField("NILAI_MINIMUM").")
				";	 */			
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_EVAL_SERTIFIKAT_LAIN SET
				  NILAI_MINIMUM = '".$this->getField("NILAI_MINIMUM")."'
				WHERE PAKET_ID = '".$this->getField("PAKET_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PAKET_EVAL_SERTIFIKAT_LAIN
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
		$str = "SELECT PAKET_EVAL_SERTIFIKAT_LAIN_ID, PAKET_ID, NAMA, 
		  				 NILAI_MINIMUM, NILAI, KETERANGAN,
                		(SELECT COUNT(NAMA) FROM PAKET_EVAL_SERTIFIKAT_LAIN X WHERE X.PAKET_ID = A.PAKET_ID) JUMLAH_SERTIFIKAT
				FROM PAKET_EVAL_SERTIFIKAT_LAIN A WHERE PAKET_EVAL_SERTIFIKAT_LAIN_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_EVAL_SERTIFIKAT_LAIN_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PAKET_EVAL_SERTIFIKAT_LAIN_ID, NAMA
				FROM PAKET_EVAL_SERTIFIKAT_LAIN WHERE PAKET_EVAL_SERTIFIKAT_LAIN_ID IS NOT NULL"; 
		
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
		$str = "SELECT COUNT(PAKET_EVAL_SERTIFIKAT_LAIN_ID) AS ROWCOUNT FROM PAKET_EVAL_SERTIFIKAT_LAIN WHERE PAKET_EVAL_SERTIFIKAT_LAIN_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(PAKET_EVAL_SERTIFIKAT_LAIN_ID) AS ROWCOUNT FROM PAKET_EVAL_SERTIFIKAT_LAIN WHERE PAKET_EVAL_SERTIFIKAT_LAIN_ID IS NOT NULL "; 
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