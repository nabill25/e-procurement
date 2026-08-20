<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class RekananDaftarPeralatan extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function RekananDaftarPeralatan()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_DAFTAR_PERALATAN_ID", $this->getNextId("REKANAN_DAFTAR_PERALATAN_ID","REKANAN_DAFTAR_PERALATAN")); 

		$str = "
		INSERT INTO  REKANAN_DAFTAR_PERALATAN (
		   REKANAN_DAFTAR_PERALATAN_ID, PAKET_ID, REKANAN_ID, 
		   REKANAN_PERALATAN_ID) 
 			 	VALUES (
				  '".$this->getField("REKANAN_DAFTAR_PERALATAN_ID")."',
  				  '".$this->getField("PAKET_ID")."',
				  '".$this->getField("REKANAN_ID")."',
				  '".$this->getField("REKANAN_PERALATAN_ID")."'
				)"; 
				
		$this->query = $str;
		//echo $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_DAFTAR_PERALATAN SET
				  REKANAN_ID = '".$this->getField("REKANAN_ID")."',
				  REKANAN_PERALATAN_ID = '".$this->getField("REKANAN_PERALATAN_ID")."'
				WHERE REKANAN_DAFTAR_PERALATAN_ID = '".$this->getField("REKANAN_DAFTAR_PERALATAN_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updateCatatan()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_DAFTAR_PERALATAN SET
				  CATATAN = '".$this->getField("CATATAN")."'
				WHERE REKANAN_DAFTAR_PERALATAN_ID = '".$this->getField("REKANAN_DAFTAR_PERALATAN_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
		
	function delete()
	{
		
        $str = "DELETE FROM REKANAN_DAFTAR_PERALATAN
                WHERE 
                  PAKET_ID = '".$this->getField("PAKET_ID")."' AND
				  REKANAN_ID = '".$this->getField("REKANAN_ID")."' "; 
		
		$this->query = $str;
        return $this->execQuery($str);
    }

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","REKANAN_PERALATAN_ID"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT 
                REKANAN_DAFTAR_PERALATAN_ID, A.PAKET_ID, A.REKANAN_ID, 
                   A.REKANAN_PERALATAN_ID, B.JENIS REKANAN_PERALATAN, A.CATATAN
                FROM REKANAN_DAFTAR_PERALATAN A
                LEFT JOIN REKANAN_PERALATAN B ON A.REKANAN_PERALATAN_ID = B.REKANAN_PERALATAN_ID
                WHERE 1 = 1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_DAFTAR_PERALATAN_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
	
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(REKANAN_DAFTAR_PERALATAN_ID) AS ROWCOUNT FROM REKANAN_DAFTAR_PERALATAN WHERE 1 = 1 "; 
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

    function getPaketPeralatan($paketId, $rekananId)
	{
		$str = "SELECT COUNT(1) AS ROWCOUNT FROM REKANAN_DAFTAR_PERALATAN A 
				WHERE A.PAKET_ID = '".$paketId."' AND A.REKANAN_ID = '".$rekananId."' "; 
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }

	
  } 
?>