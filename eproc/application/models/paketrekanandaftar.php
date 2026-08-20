<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketRekananDaftar extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
		parent::__construct();
	}
	
    function PaketRekananDaftar()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		$str = "
		INSERT INTO  PAKET_REKANAN_DAFTAR (
		   PAKET_REKANAN_ID, KODE, CATATAN, CREATED_BY, CREATED_TIMESTAMP) 
 			 	VALUES ( 
				  (SELECT PAKET_REKANAN_ID FROM PAKET_REKANAN WHERE PAKET_ID = '".$this->getField("PAKET_ID")."' AND  REKANAN_ID = '".$this->getField("REKANAN_ID")."'),
  				  '".$this->getField("KODE")."',
				  '".$this->getField("CATATAN")."',
				  '".$this->getField("CREATED_BY")."',
				  CURRENT_DATE 			 
				)"; 
		$this->query = $str;
		$this->id = $this->getField("PAKET_REKANAN_ID");
		
		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM PAKET_REKANAN_DAFTAR
                WHERE 
                  PAKET_REKANAN_ID = (SELECT PAKET_REKANAN_ID FROM PAKET_REKANAN WHERE PAKET_ID = '".$this->getField("PAKET_ID")."' AND  REKANAN_ID = '".$this->getField("REKANAN_ID")."') AND
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
		$str = "SELECT PAKET_REKANAN_ID, KODE, CATATAN, CREATED_BY, CREATED_TIMESTAMP
				FROM PAKET_REKANAN_DAFTAR A WHERE 1 = 1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_REKANAN_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsCatatan($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT 
				PAKET_REKANAN_ID, KODE, CATATAN
				FROM PAKET_REKANAN_DAFTAR_CATATAN A 
				WHERE 1 = 1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ";
				
		return $this->selectLimit($str,$limit,$from); 
    }	

    // ikn 20190313
    function selectByParamsCatatan2($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT 
				LULUS_PENDAFTARAN_KETERANGAN
				FROM PAKET_REKANAN A 
				WHERE LULUS_PENDAFTARAN = '0' AND 1 = 1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die();
		$this->query = $str;
		$str .= $statement." ";
				
		return $this->selectLimit($str,$limit,$from); 
    }	
	
    function getCatatan($paketId, $rekananId, $kodeCatatan)
	{
		$str = "SELECT CATATAN FROM PAKET_REKANAN_DAFTAR A 
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