<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PermohonanPaketFileRevisi extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PermohonanPaketFileRevisi()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PERMOHONAN_PAKET_FILE_REVISI_ID", $this->getNextId("PERMOHONAN_PAKET_FILE_REVISI_ID","PERMOHONAN_PAKET_FILE_REVISI")); 

		$str = "INSERT INTO PERMOHONAN_PAKET_FILE_REVISI (
					   PERMOHONAN_PAKET_FILE_REVISI_ID, PERMOHONAN_PAKET_FILE_ID, CATATAN, 
					   FILE_REVISI, REVISI_BY, REVISI_DATE)
						VALUES (
						  ".$this->getField("PERMOHONAN_PAKET_FILE_REVISI_ID").",
						  ".$this->getField("PERMOHONAN_PAKET_FILE_ID").",
						  '".$this->getField("CATATAN")."',
						  '".$this->getField("FILE_REVISI")."',
						  '".$this->getField("REVISI_BY")."',
						  ".$this->getField("REVISI_DATE")."
						)"; 
				
		$this->query = $str;
		$this->id = $this->getField("PERMOHONAN_PAKET_FILE_REVISI_ID");
		
		return $this->execQuery($str);
    }
	
	function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PERMOHONAN_PAKET_FILE_REVISI SET
				  PERMOHONAN_PAKET_FILE_ID	= ".$this->getField("PERMOHONAN_PAKET_FILE_ID").",
   				  CATATAN					= '".$this->getField("CATATAN")."',
				  FILE_REVISI				= '".$this->getField("FILE_REVISI")."',
				  REVISI_BY					= '".$this->getField("REVISI_BY")."',
				  REVISI_DATE				= ".$this->getField("REVISI_DATE")."
				WHERE PERMOHONAN_PAKET_FILE_REVISI_ID = ".$this->getField("PERMOHONAN_PAKET_FILE_REVISI_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }


	function delete()
	{
        $str = "DELETE FROM PERMOHONAN_PAKET_FILE_REVISI
                WHERE 
                  PERMOHONAN_PAKET_FILE_REVISI_ID = ".$this->getField("PERMOHONAN_PAKET_FILE_REVISI_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }
	
	function deletePermohonan()
	{
        $str = "DELETE FROM PERMOHONAN_PAKET_FILE_REVISI
                WHERE 
                  PERMOHONAN_PAKET_FILE_ID = ".$this->getField("PERMOHONAN_PAKET_FILE_ID").""; 
				  
		$this->query = $str;
		//echo $str;exit;
        return $this->execQuery($str);
    }

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","PERMOHONAN_PAKET_FILE_METODE_EVALUASI_ID"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PERMOHONAN_PAKET_FILE_REVISI_ID, PERMOHONAN_PAKET_FILE_ID, CATATAN, 
						   FILE_REVISI, REVISI_BY, TO_CHAR(REVISI_DATE, 'YYYY-MM-DD HH:MI:SS') REVISI_DATE
					  FROM PERMOHONAN_PAKET_FILE_REVISI A WHERE 1=1"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
			
		$str .= $statement." ORDER BY PERMOHONAN_PAKET_FILE_REVISI_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }

   
    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(A.PERMOHONAN_PAKET_FILE_REVISI_ID) AS ROWCOUNT 
					FROM    PERMOHONAN_PAKET_FILE_REVISI A
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