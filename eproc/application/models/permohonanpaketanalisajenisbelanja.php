<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class Permohonanpaketanalisajenisbelanja extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
		parent::__construct();
	}
	
    function Permohonanpaketanalisajenisbelanja()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PERMOHONAN_PAKET_ANALISA_JENIS_BELANJA_ID", $this->getNextId("PERMOHONAN_PAKET_ANALISA_JENIS_BELANJA_ID","PERMOHONAN_PAKET_ANALISA_JENIS_BELANJA")); 

		$str = "
		INSERT INTO PERMOHONAN_PAKET_ANALISA_JENIS_BELANJA (
   			        PERMOHONAN_PAKET_ANALISA_JENIS_BELANJA_ID, NAMA, INISIAL) 
			 	VALUES (
				  ".$this->getField("PERMOHONAN_PAKET_ANALISA_JENIS_BELANJA_ID").",
				  '".$this->getField("NAMA")."'
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PERMOHONAN_PAKET_ANALISA_JENIS_BELANJA SET
				  NAMA = '".$this->getField("NAMA")."'
				WHERE PERMOHONAN_PAKET_ANALISA_JENIS_BELANJA_ID = ".$this->getField("PERMOHONAN_PAKET_ANALISA_JENIS_BELANJA_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PERMOHONAN_PAKET_ANALISA_JENIS_BELANJA
                WHERE 
                  PERMOHONAN_PAKET_ANALISA_JENIS_BELANJA_ID = ".$this->getField("PERMOHONAN_PAKET_ANALISA_JENIS_BELANJA_ID").""; 
				  
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
		$str = "SELECT PERMOHONAN_PAKET_ANALISA_JENIS_BELANJA_ID, NAMA
				FROM PERMOHONAN_PAKET_ANALISA_JENIS_BELANJA WHERE PERMOHONAN_PAKET_ANALISA_JENIS_BELANJA_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY NAMA ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    } 

  } 
?>