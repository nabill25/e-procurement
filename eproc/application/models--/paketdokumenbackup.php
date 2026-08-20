<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketDokumenBackup extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PaketDokumenBackup()
	{
      $this->Entity(); 
    }
	
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				SELECT  
					PAKET_DOKUMEN_ID, PAKET_ID, NAMA, 
					UKURAN, TIPE, PATH_FILE, 
					TANGGAL_UPLOAD, JENIS_DOKUMEN, KETERANGAN, TO_CHAR(TANGGAL_UPLOAD, 'DD-MM-YYYY HH24:MI') TGL_JAM_UPLOAD,
					STATUS, REKANAN_USER_ID, FILE_PASSWORD, (SELECT NAMA FROM REKANAN X WHERE X.REKANAN_ID = P.REKANAN_USER_ID) NMREKANAN
				FROM PAKET_DOKUMEN_BACKUP P WHERE PAKET_DOKUMEN_ID IS NOT NULL 
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ORDER BY PAKET_DOKUMEN_ID ASC";
		$this->query = $str;
		//echo $str;
		return $this->selectLimit($str,$limit,$from); 
    }
    
    function getCountByParams($paramsArray=array(), $statement="")
	{
		$str = "SELECT COUNT(PAKET_DOKUMEN_ID) AS ROWCOUNT FROM PAKET_DOKUMEN_BACKUP WHERE PAKET_DOKUMEN_ID IS NOT NULL ".$statement; 
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