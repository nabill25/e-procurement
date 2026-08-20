<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class QRValidasi extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
  		parent::__construct();
  	}
  	
    function QRValidasi()
	{
      $this->Entity(); 
    }
	

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		//$this->setField("AANWIJZING_ID", $this->getNextId("AANWIJZING_ID","AANWIJZING")); 

		$str = "
				INSERT INTO QR_VALIDASI (
				   SUMBER, KODE_QR, PAKET_ID, INFORMASI) 
				VALUES (
				  'DOKUMEN_AANWIJZING', 
				  '".$this->getField("KODE_QR")."', 
				  '".$this->getField("PAKET_ID")."', 
				  '".$this->getField("INFORMASI")."'
				)"; 
		$this->query = $str;
		return $this->execQuery($str);
    }
	
    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","BERITA_METODE_EVALUASI_ID"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
					SELECT 
					SUMBER, KODE_QR, PAKET_ID, 
					   USER_LOGIN_ID, REKANAN_ID, INFORMASI
					FROM QR_VALIDASI A WHERE 1 = 1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
			
		$str .= $statement." ORDER BY PAKET_ID DESC";
				
		return $this->selectLimit($str,$limit,$from); 
    }

  } 
?>