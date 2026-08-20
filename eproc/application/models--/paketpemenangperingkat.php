<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class Paketpemenangperingkat extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function Paket()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_PEMENANG_PERINGKAT_ID", $this->getNextId("PAKET_PEMENANG_PERINGKAT_ID","PAKET_PEMENANG_PERINGKAT")); 

		$str = "
			INSERT INTO PAKET_PEMENANG_PERINGKAT (
			   PAKET_PEMENANG_PERINGKAT_ID, REKANAN_ID, KETERANGAN, 
			   TANGGAL_PENETAPAN, CREATED_DATE, CREATED_BY, PAKET_ID, PERINGKAT
			   )
  			 	VALUES (
				  ".$this->getField("PAKET_PEMENANG_PERINGKAT_ID").",
  				  ".$this->getField("REKANAN_ID").",
   				  '".$this->getField("KETERANGAN")."',
				  ".$this->getField("TANGGAL_PENETAPAN").",
   				  CURRENT_TIMESTAMP,
				  ".$this->getField("USER_LOGIN_ID").",
				  ".$this->getField("PAKET_ID").",
				  ".$this->getField("PERINGKAT")."
				)"; 
				
				// echo $str; die();
		$this->query = $str;
		return $this->execQuery($str);
    }
 
	function delete()
	{
        $str = "DELETE FROM PAKET_PEMENANG_PERINGKAT
                WHERE 
                  PAKET_PEMENANG_PERINGKAT_ID = ".$this->getField("PAKET_PEMENANG_PERINGKAT_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","PAKET_METODE_EVALUASI_ID"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
					SELECT 
					A.PAKET_PEMENANG_PERINGKAT_ID,A.KETERANGAN, A.TANGGAL_PENETAPAN, A.PAKET_ID, A.FILE, A.PUBLISH, B.REKANAN_ID, B.NAMA, B.NPWP, B.ALAMAT, B.TELEPON,
					B.FAX, B.EMAIL, B.KONTAK_PERSON, B.KONTAK_PERSON_HP, B.WEBSITE, A.PERINGKAT
					FROM PAKET_PEMENANG_PERINGKAT A
					LEFT JOIN REKANAN B ON A.REKANAN_ID = B.REKANAN_ID
				    WHERE A.PAKET_ID IS NOT NULL "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
			
		$str .= $statement." ORDER BY A.PERINGKAT ASC";
				// echo $str; die();
		return $this->selectLimit($str,$limit,$from); 
    }

    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT 
					COUNT(A.PAKET_PEMENANG_PERINGKAT_ID) AS ROWCOUNT
					FROM PAKET_PEMENANG_PERINGKAT A
					LEFT JOIN REKANAN B ON A.REKANAN_ID = B.REKANAN_ID
				    WHERE A.PAKET_ID IS NOT NULL  
			   ".$statement;
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die();
		$this->query = $str;
		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function updatePublish()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_PEMENANG_PERINGKAT 
				SET
					PUBLISH = '".$this->getField("PUBLISH")."'
				WHERE PAKET_PEMENANG_PERINGKAT_ID = '".$this->getField("PAKET_PEMENANG_PERINGKAT_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updatePublish2()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_PEMENANG_PERINGKAT 
				SET
					PUBLISH = '".$this->getField("PUBLISH")."'
				WHERE PAKET_ID = '".$this->getField("PAKET_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updateDokumen()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_PEMENANG_PERINGKAT 
				SET
					FILE = '".$this->getField("FILE")."'
				WHERE PAKET_PEMENANG_PERINGKAT_ID = '".$this->getField("PAKET_PEMENANG_PERINGKAT_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }

  } 
?>