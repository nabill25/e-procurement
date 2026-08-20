<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketPernyataanMinat extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PaketPernyataanMinat()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_PERNYATAAN_MINAT_ID", $this->getNextId("PAKET_PERNYATAAN_MINAT_ID","PAKET_PERNYATAAN_MINAT")); 

		$str = "
		
			INSERT INTO PAKET_PERNYATAAN_MINAT (
			   PAKET_PERNYATAAN_MINAT_ID, PAKET_REKANAN_ID, NAMA, 
			   JABATAN, ALAMAT, TELEPON, EMAIL, 
			   PENERIMA_KUASA, PENERIMA_KUASA_JABATAN, PENERIMA_KUASA_KTP, PENERIMA_KUASA_FILE)
 
  			 	VALUES (
				  '".$this->getField("PAKET_PERNYATAAN_MINAT_ID")."',
  				  '".$this->getField("PAKET_REKANAN_ID")."',
   				  '".$this->getField("NAMA")."',
				  '".$this->getField("JABATAN")."',
				  '".$this->getField("ALAMAT")."',
  				  '".$this->getField("TELEPON")."',
  				  '".$this->getField("EMAIL")."',
  				  '".$this->getField("PENERIMA_KUASA")."',
  				  '".$this->getField("PENERIMA_KUASA_JABATAN")."',
  				  '".$this->getField("PENERIMA_KUASA_KTP")."',
  				  '".$this->getField("PENERIMA_KUASA_FILE")."'
				)"; 
				
		$this->query = $str;
		//echo $str;exit;
		$this->id = $this->getField("PAKET_PERNYATAAN_MINAT_ID");
		return $this->execQuery($str);
    }


    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "		
				 UPDATE  PAKET_PERNYATAAN_MINAT
				SET    
					   NAMA        = '".$this->getField("NAMA")."',
					   JABATAN  = '".$this->getField("JABATAN")."',
					   ALAMAT      = '".$this->getField("ALAMAT")."',
					   TELEPON    = '".$this->getField("TELEPON")."'
				WHERE  PAKET_PERNYATAAN_MINAT_ID   =  ".$this->getField("PAKET_PERNYATAAN_MINAT_ID")."
			  
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PAKET_PERNYATAAN_MINAT
                WHERE 
                  PAKET_PERNYATAAN_MINAT_ID = ".$this->getField("PAKET_PERNYATAAN_MINAT_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","PAKET_PERNYATAAN_MINAT_METODE_EVALUASI_ID"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
					SELECT 
						PAKET_PERNYATAAN_MINAT_ID, PAKET_REKANAN_ID, NAMA, 
						JABATAN, ALAMAT, TELEPON, EMAIL, TO_CHAR(TANGGAL, 'YYYY-MM-DD')TANGGAL, 
						PENERIMA_KUASA, PENERIMA_KUASA_JABATAN, PENERIMA_KUASA_KTP, PENERIMA_KUASA_FILE, KODE_QR
					FROM PAKET_PERNYATAAN_MINAT A
				    WHERE PAKET_PERNYATAAN_MINAT_ID IS NOT NULL "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die();
		$this->query = $str;
		$str .= $statement." ORDER BY EMAIL DESC";
				
		return $this->selectLimit($str,$limit,$from); 
    }

   
    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(A.PAKET_PERNYATAAN_MINAT_ID) AS ROWCOUNT 
					FROM    PAKET_PERNYATAAN_MINAT A
					WHERE 1 = 1".$statement; 
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

  } 
?>