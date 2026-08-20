<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class RekananDaftarPengalaman extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
  		parent::__construct();
  	}
  	
    function RekananDaftarPengalaman()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_DAFTAR_PENGALAMAN_ID", $this->getNextId("REKANAN_DAFTAR_PENGALAMAN_ID","REKANAN_DAFTAR_PENGALAMAN")); 

		$str = "
		INSERT INTO  REKANAN_DAFTAR_PENGALAMAN (
		   REKANAN_DAFTAR_PENGALAMAN_ID, PAKET_ID, REKANAN_ID, 
		   REKANAN_PENGALAMAN_ID) 
 			 	VALUES (
				  '".$this->getField("REKANAN_DAFTAR_PENGALAMAN_ID")."',
  				  '".$this->getField("PAKET_ID")."',
				  '".$this->getField("REKANAN_ID")."',
				  '".$this->getField("REKANAN_PENGALAMAN_ID")."'
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_DAFTAR_PENGALAMAN SET
				  REKANAN_ID = '".$this->getField("REKANAN_ID")."',
				  REKANAN_PENGALAMAN_ID = '".$this->getField("REKANAN_PENGALAMAN_ID")."'
				WHERE REKANAN_DAFTAR_PENGALAMAN_ID = '".$this->getField("REKANAN_DAFTAR_PENGALAMAN_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updateCatatan()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_DAFTAR_PENGALAMAN SET
				  CATATAN = '".$this->getField("CATATAN")."'
				WHERE REKANAN_DAFTAR_PENGALAMAN_ID = '".$this->getField("REKANAN_DAFTAR_PENGALAMAN_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
		
	function delete()
	{
		
        $str = "DELETE FROM REKANAN_DAFTAR_PENGALAMAN
                WHERE 
                  PAKET_ID = '".$this->getField("PAKET_ID")."' AND
				  REKANAN_ID = '".$this->getField("REKANAN_ID")."' "; 
		
		$this->query = $str;
        return $this->execQuery($str);
    }

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","REKANAN_PENGALAMAN_ID"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT 
                REKANAN_DAFTAR_PENGALAMAN_ID, A.PAKET_ID, A.REKANAN_ID, 
                   A.REKANAN_PENGALAMAN_ID, B.NAMA REKANAN_PENGALAMAN, A.CATATAN
                FROM REKANAN_DAFTAR_PENGALAMAN A
                LEFT JOIN REKANAN_PENGALAMAN B ON A.REKANAN_PENGALAMAN_ID = B.REKANAN_PENGALAMAN_ID
                WHERE 1 = 1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_DAFTAR_PENGALAMAN_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
	
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(REKANAN_DAFTAR_PENGALAMAN_ID) AS ROWCOUNT FROM REKANAN_DAFTAR_PENGALAMAN WHERE 1 = 1 "; 
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

    function getPaketPengalaman($paketId, $rekananId)
	{
		$str = "SELECT COUNT(1) AS ROWCOUNT FROM REKANAN_DAFTAR_PENGALAMAN A 
				WHERE A.PAKET_ID = '".$paketId."' AND A.REKANAN_ID = '".$rekananId."' "; 
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }

	
  } 
?>