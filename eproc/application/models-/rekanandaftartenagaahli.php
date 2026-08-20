<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class RekananDaftarTenagaAhli extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function RekananDaftarTenagaAhli()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_DAFTAR_TENAGA_AHLI_ID", $this->getNextId("REKANAN_DAFTAR_TENAGA_AHLI_ID","REKANAN_DAFTAR_TENAGA_AHLI")); 

		$str = "
		INSERT INTO  REKANAN_DAFTAR_TENAGA_AHLI (
		   REKANAN_DAFTAR_TENAGA_AHLI_ID, PAKET_ID, REKANAN_ID, 
		   REKANAN_TENAGA_AHLI_ID) 
 			 	VALUES (
				  '".$this->getField("REKANAN_DAFTAR_TENAGA_AHLI_ID")."',
  				  '".$this->getField("PAKET_ID")."',
				  '".$this->getField("REKANAN_ID")."',
				  '".$this->getField("REKANAN_TENAGA_AHLI_ID")."'
				)"; 
				
		$this->query = $str;
		//echo $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_DAFTAR_TENAGA_AHLI SET
				  REKANAN_ID = '".$this->getField("REKANAN_ID")."',
				  REKANAN_TENAGA_AHLI_ID = '".$this->getField("REKANAN_TENAGA_AHLI_ID")."'
				WHERE REKANAN_DAFTAR_TENAGA_AHLI_ID = '".$this->getField("REKANAN_DAFTAR_TENAGA_AHLI_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updateCatatan()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_DAFTAR_TENAGA_AHLI SET
				  CATATAN = '".$this->getField("CATATAN")."'
				WHERE REKANAN_DAFTAR_TENAGA_AHLI_ID = '".$this->getField("REKANAN_DAFTAR_TENAGA_AHLI_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
		
	function delete()
	{
		
        $str = "DELETE FROM REKANAN_DAFTAR_TENAGA_AHLI
                WHERE 
                  PAKET_ID = '".$this->getField("PAKET_ID")."' AND
				  REKANAN_ID = '".$this->getField("REKANAN_ID")."' "; 
		
		$this->query = $str;
        return $this->execQuery($str);
    }

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","REKANAN_TENAGA_AHLI_ID"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT 
                REKANAN_DAFTAR_TENAGA_AHLI_ID, A.PAKET_ID, A.REKANAN_ID, 
                   A.REKANAN_TENAGA_AHLI_ID, B.NAMA REKANAN_TENAGA_AHLI, A.CATATAN
                FROM REKANAN_DAFTAR_TENAGA_AHLI A
                LEFT JOIN REKANAN_TENAGA_AHLI B ON A.REKANAN_TENAGA_AHLI_ID = B.REKANAN_TENAGA_AHLI_ID
                WHERE 1 = 1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_DAFTAR_TENAGA_AHLI_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
	
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(REKANAN_DAFTAR_TENAGA_AHLI_ID) AS ROWCOUNT FROM REKANAN_DAFTAR_TENAGA_AHLI WHERE 1 = 1 "; 
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

    function getPaketTenagaAhli($paketId, $rekananId)
	{
		$str = "SELECT COUNT(1) AS ROWCOUNT FROM REKANAN_DAFTAR_TENAGA_AHLI A 
				WHERE A.PAKET_ID = '".$paketId."' AND A.REKANAN_ID = '".$rekananId."' "; 
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }

	
  } 
?>