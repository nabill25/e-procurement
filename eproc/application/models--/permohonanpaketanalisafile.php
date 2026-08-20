<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PermohonanPaketAnalisaFile extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
	function __construct(){
		parent::__construct();
	}
	
    function PermohonanPaketAnalisaFile()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PERMOHONAN_PAKET_ANALISA_FILE_ID", $this->getNextId("PERMOHONAN_PAKET_ANALISA_FILE_ID","PERMOHONAN_PAKET_ANALISA_FILE")); 

		$NOSRT = $this->getField("ESIGN_NOMOR_SURAT").'_'.$this->getField("PERMOHONAN_PAKET_ANALISA_FILE_ID");

		$str = "
			INSERT INTO PERMOHONAN_PAKET_ANALISA_FILE (
			   PERMOHONAN_PAKET_ANALISA_FILE_ID, PERMOHONAN_PAKET_ANALISA_ID, PATH_FILE, 
			   TIPE, UKURAN, JUDUL, CREATED_BY, CREATED_DATE, ESIGN_NOMOR_SURAT, FILE_TTE, FILE_SHARE)
 			VALUES (
				  ".$this->getField("PERMOHONAN_PAKET_ANALISA_FILE_ID").",
  				  ".$this->getField("PERMOHONAN_PAKET_ANALISA_ID").",
   				  '".$this->getField("PATH_FILE")."',
				  '".$this->getField("TIPE")."',
				  ".$this->getField("UKURAN").",
				  '".$this->getField("JUDUL")."',
				  ".$this->getField("CREATED_BY").",
				  '".$this->getField("CREATED_DATE")."',
				  '".$NOSRT."',
				  '".$this->getField("FILE_TTE")."',
				  '".$this->getField("FILE_SHARE")."'
				)"; 
				// echo $str; die();
		$this->query = $str;
		$this->id = $this->getField("PERMOHONAN_PAKET_ANALISA_FILE_ID");

		return $this->execQuery($str);
    } 
	
	function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PERMOHONAN_PAKET_ANALISA_FILE SET
   				  PATH_FILE = '".$this->getField("PATH_FILE")."',
					  TIPE = '".$this->getField("TIPE")."',
					  UKURAN = ".$this->getField("UKURAN").",
					  JUDUL = '".$this->getField("JUDUL")."',
					  FILE_TTE = '".$this->getField("FILE_TTE")."',
					  FILE_SHARE = '".$this->getField("FILE_SHARE")."',
					  UPDATED_BY = ".$this->getField("CREATED_BY").",
					  UPDATED_DATE = CURRENT_TIMESTAMP
					WHERE PERMOHONAN_PAKET_ANALISA_FILE_ID = ".$this->getField("PERMOHONAN_PAKET_ANALISA_FILE_ID")."
				"; 
				$this->query = $str;
				//echo $str;exit;
		return $this->execQuery($str);
    }
	
	function updateEsign200()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PERMOHONAN_PAKET_ANALISA_FILE SET
   				  ESIGN_ID = '".$this->getField("ESIGN_ID")."',
					  ESIGN_PATH_FILE = '".$this->getField("ESIGN_PATH_FILE")."',
					  ESIGN_STATUS = '".$this->getField("ESIGN_STATUS")."',
					  UPDATED_BY = ".$this->getField("UPDATED_BY").",
					  UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE PERMOHONAN_PAKET_ANALISA_FILE_ID 	= ".$this->getField("PERMOHONAN_PAKET_ANALISA_FILE_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
  }

  function updateEsign400()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PERMOHONAN_PAKET_ANALISA_FILE SET
					  ESIGN_STATUS = '".$this->getField("ESIGN_STATUS")."',
					  UPDATED_BY = ".$this->getField("UPDATED_BY").",
					  UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE PERMOHONAN_PAKET_ANALISA_FILE_ID 	= ".$this->getField("PERMOHONAN_PAKET_ANALISA_FILE_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
  }

  function updateEsign400Close()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PERMOHONAN_PAKET_ANALISA_FILE SET
					  ESIGN_STATUS = '".$this->getField("ESIGN_STATUS")."',
					  ESIGN_PATH_FILE = '".$this->getField("ESIGN_PATH_FILE")."',
					  UPDATED_BY = ".$this->getField("UPDATED_BY").",
					  UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE PERMOHONAN_PAKET_ANALISA_FILE_ID 	= ".$this->getField("PERMOHONAN_PAKET_ANALISA_FILE_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
  }

  function updateFileCheck()
	{

		if ($this->getField("JENIS") == 'fileTTE') {
			$str = "UPDATE PERMOHONAN_PAKET_ANALISA_FILE SET
	   				  FILE_TTE = '".$this->getField("FILE_TTE")."',
						  UPDATED_BY = ".$this->getField("UPDATED_BY").",
						  UPDATED_DATE = CURRENT_TIMESTAMP
					WHERE PERMOHONAN_PAKET_ANALISA_FILE_ID 	= ".$this->getField("PERMOHONAN_PAKET_ANALISA_FILE_ID")."
					"; 
		} else {
			$str = "UPDATE PERMOHONAN_PAKET_ANALISA_FILE SET
	   				  FILE_SHARE = '".$this->getField("FILE_SHARE")."',
						  UPDATED_BY = ".$this->getField("UPDATED_BY").",
						  UPDATED_DATE = CURRENT_TIMESTAMP
					WHERE PERMOHONAN_PAKET_ANALISA_FILE_ID 	= ".$this->getField("PERMOHONAN_PAKET_ANALISA_FILE_ID")."
					"; 
		}

		$this->query = $str;
		return $this->execQuery($str);
  }

	function delete()
	{
        $str = "DELETE FROM PERMOHONAN_PAKET_ANALISA_FILE
                WHERE 
                  PERMOHONAN_PAKET_ANALISA_FILE_ID = ".$this->getField("PERMOHONAN_PAKET_ANALISA_FILE_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

  function deleteByID()
	{
        $str = "DELETE FROM PERMOHONAN_PAKET_ANALISA_FILE
                WHERE 
                  PERMOHONAN_PAKET_ANALISA_FILE_ID = ".$this->getField("PERMOHONAN_PAKET_ANALISA_FILE_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }
	
	function deletePermohonan()
	{
        $str = "DELETE FROM PERMOHONAN_PAKET_ANALISA_FILE
                WHERE 
                  PERMOHONAN_PAKET_ANALISA_ID = ".$this->getField("PERMOHONAN_PAKET_ANALISA_ID").""; 
				  
		$this->query = $str;
		//echo $str;exit;
        return $this->execQuery($str);
    } 

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","PERMOHONAN_PAKET_ANALISA_FILE_METODE_EVALUASI_ID"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
					SELECT 
					 A.PERMOHONAN_PAKET_ANALISA_FILE_ID, PERMOHONAN_PAKET_ANALISA_ID, PATH_FILE, 
			  		 TIPE, UKURAN,JUDUL,ESIGN_ID, ESIGN_NOMOR_SURAT, ESIGN_STATUS, ESIGN_PATH_FILE, FILE_TTE, FILE_SHARE
					FROM PERMOHONAN_PAKET_ANALISA_FILE A 
				    WHERE A.PERMOHONAN_PAKET_ANALISA_FILE_ID IS NOT NULL "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
			
		$str .= $statement." ORDER BY PERMOHONAN_PAKET_ANALISA_FILE_ID ASC";
		$this->query = $str;
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectFileTTE($paramsArray=array(),$limit=-1,$from=-1, $statement='') // For CronJob
	{
		$str = "
					SELECT *
					FROM permohonan_paket_analisa_file
					WHERE file_tte='1' and esign_status != 'Selesai'";  
		$this->query = $str;
				
		return $this->selectLimit($str,$limit,$from); 
    }

   
    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(A.PERMOHONAN_PAKET_ANALISA_FILE_ID) AS ROWCOUNT 
					FROM    PERMOHONAN_PAKET_ANALISA_FILE A
					WHERE 1 = 1".$statement; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str;
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }

  } 
?>