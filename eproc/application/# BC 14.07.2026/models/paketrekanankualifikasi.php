<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketRekananKualifikasi extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PaketRekananKualifikasi()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		$str = "
		INSERT INTO  PAKET_REKANAN_KUALIFIKASI (
		   PAKET_REKANAN_ID, KODE, CATATAN, CREATED_BY, CREATED_DATE) 
 			 	VALUES ( 
  				  '".$this->getField("PAKET_REKANAN_ID")."',
  				  '".$this->getField("KODE")."',
				  '".$this->getField("CATATAN")."',
				  '".$this->getField("CREATED_BY")."',
				  CURRENT_DATE 			 
				)"; 
				
		$this->query = $str;
		$this->id = $this->getField("PAKET_REKANAN_ID");
		return $this->execQuery($str);
    }

    // Ikn 20190929
    function insert2()
	{
		$str = "
		INSERT INTO  PAKET_REKANAN_KUALIFIKASI (
		   PAKET_REKANAN_ID, KODE, CATATAN, CREATED_BY, NILAI, STATUS, CREATED_DATE) 
 			 	VALUES ( 
  				  '".$this->getField("PAKET_REKANAN_ID")."',
  				  '".$this->getField("KODE")."',
				  '".$this->getField("CATATAN")."',
				  '".$this->getField("CREATED_BY")."',
				  '".$this->getField("NILAI")."',
				  '".$this->getField("STATUS")."',
				  CURRENT_DATE 			 
				)"; 
				// echo $str; die();
		$this->query = $str;
		$this->id = $this->getField("PAKET_REKANAN_ID");
		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM PAKET_REKANAN_KUALIFIKASI
                WHERE 
                  PAKET_REKANAN_ID = '".$this->getField("PAKET_REKANAN_ID")."' AND
				  KODE = '".$this->getField("KODE")."' "; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","CATATAN"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PAKET_REKANAN_ID, KODE, CATATAN, CREATED_BY, CREATED_DATE, STATUS, NILAI
				FROM PAKET_REKANAN_KUALIFIKASI A WHERE 1 = 1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_REKANAN_ID ASC";
				// echo $str;
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsCatatan($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT 
				PAKET_REKANAN_ID, KODE, CATATAN
				FROM PAKET_REKANAN_KUALIFIKASI_CATATAN A 
				WHERE 1 = 1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ";
				
		return $this->selectLimit($str,$limit,$from); 
    }	
	
    function getCatatan($paketId, $rekananId, $kodeCatatan)
	{
		$str = "SELECT CATATAN FROM PAKET_REKANAN_KUALIFIKASI A 
				INNER JOIN PAKET_REKANAN B ON A.PAKET_REKANAN_ID = B.PAKET_REKANAN_ID
				WHERE B.PAKET_ID = '".$paketId."' AND B.REKANAN_ID = '".$rekananId."' AND A.KODE = '".$kodeCatatan."' "; 
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("CATATAN"); 
		else 
			return ""; 
    }

  } 
?>