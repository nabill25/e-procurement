<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class Permohonanpaketanalisakategori extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
		parent::__construct();
	}
	
    function Permohonanpaketanalisakategori()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PERMOHONAN_PAKET_ANALISA_KATEGORI_ID", $this->getNextId("PERMOHONAN_PAKET_ANALISA_KATEGORI_ID","PERMOHONAN_PAKET_ANALISA_KATEGORI")); 

		$str = "
		INSERT INTO PERMOHONAN_PAKET_ANALISA_KATEGORI (
   			        PERMOHONAN_PAKET_ANALISA_KATEGORI_ID, NAMA, INISIAL) 
			 	VALUES (
				  ".$this->getField("PERMOHONAN_PAKET_ANALISA_KATEGORI_ID").",
				  '".$this->getField("NAMA")."',
				  '".$this->getField("INISIAL")."'
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PERMOHONAN_PAKET_ANALISA_KATEGORI SET
				  NAMA = '".$this->getField("NAMA")."', INISIAL = '".$this->getField("INISIAL")."'
				WHERE PERMOHONAN_PAKET_ANALISA_KATEGORI_ID = ".$this->getField("PERMOHONAN_PAKET_ANALISA_KATEGORI_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PERMOHONAN_PAKET_ANALISA_KATEGORI
                WHERE 
                  PERMOHONAN_PAKET_ANALISA_KATEGORI_ID = ".$this->getField("PERMOHONAN_PAKET_ANALISA_KATEGORI_ID").""; 
				  
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
		$str = "SELECT PERMOHONAN_PAKET_ANALISA_KATEGORI_ID, NAMA, INISIAL
				FROM PERMOHONAN_PAKET_ANALISA_KATEGORI WHERE PERMOHONAN_PAKET_ANALISA_KATEGORI_ID IS NOT NULL"; 
		
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