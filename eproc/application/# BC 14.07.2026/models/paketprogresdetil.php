<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketProgresDetil extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PaketProgresDetil()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_PROGRES_DETIL_ID", $this->getNextId("PAKET_PROGRES_DETIL_ID","PAKET_PROGRES_DETIL")); 

		$str = "
			INSERT INTO PAKET_PROGRES_DETIL (
			   PAKET_PROGRES_DETIL_ID, PAKET_PROGRES_ID, USER_LOGIN_ID, 
			   TANGGAL, NAMA, PROSENTASE, KETERANGAN, KENDALA, LAST_CREATE_USER, LAST_CREATE_DATE)
  			 	VALUES (
				  '".$this->getField("PAKET_PROGRES_DETIL_ID")."',
  				  '".$this->getField("PAKET_PROGRES_ID")."',
   				  ".$this->getField("USER_LOGIN_ID").",
				  ".$this->getField("TANGGAL").",
				  '".$this->getField("NAMA")."',
				  '".$this->getField("PROSENTASE")."',
  				  '".$this->getField("KETERANGAN")."',
  				  '".$this->getField("KENDALA")."',
				  '".$this->getField("LAST_CREATE_USER")."',
				  ".$this->getField("LAST_CREATE_DATE")."
				)"; 
				
		$this->query = $str;
		$this->id = $this->getField("PAKET_PROGRES_DETIL_ID");
		return $this->execQuery($str);
    }
	
	function update()
	{
		$str = "
				UPDATE PAKET_PROGRES_DETIL
				SET    
					  PAKET_PROGRES_ID= '".$this->getField("PAKET_PROGRES_ID")."',
					  USER_LOGIN_ID= ".$this->getField("USER_LOGIN_ID").",
					  TANGGAL= ".$this->getField("TANGGAL").",
					  NAMA= '".$this->getField("NAMA")."',
					  PROSENTASE= '".$this->getField("PROSENTASE")."',
					  KETERANGAN= '".$this->getField("KETERANGAN")."',
					  KENDALA= '".$this->getField("KENDALA")."',
					  LAST_CREATE_USER= '".$this->getField("LAST_CREATE_USER")."',
				  	  LAST_CREATE_DATE= ".$this->getField("LAST_CREATE_DATE")."
				WHERE  PAKET_PROGRES_DETIL_ID = '".$this->getField("PAKET_PROGRES_DETIL_ID")."'
			 "; 
		$this->query = $str;
		return $this->execQuery($str);
    }
	
	function updateFormat()
	{
		$str = "
				UPDATE PAKET_PROGRES_DETIL
				SET    
					   PATH_FILE= '".$this->getField("PATH_FILE")."',
					   UKURAN= ".$this->getField("UKURAN").",
					   TIPE= '".$this->getField("TIPE")."'
				WHERE  PAKET_PROGRES_DETIL_ID = '".$this->getField("PAKET_PROGRES_DETIL_ID")."'
			 "; 
		$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PAKET_PROGRES_DETIL
                WHERE 
                  PAKET_PROGRES_DETIL_ID = ".$this->getField("PAKET_PROGRES_DETIL_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","PAKET_PROGRES_DETIL_METODE_EVALUASI_ID"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
	function selectByParamsBlob($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PATH_FILE, TIPE
				FROM PAKET_PROGRES_DETIL A WHERE 1=1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement."";
		$this->query = $str;
				
		return $this->selectLimit($str,$limit,$from); 
    }
	
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
					SELECT 
                    PAKET_PROGRES_DETIL_ID, PAKET_PROGRES_ID, A.USER_LOGIN_ID, A.PROSENTASE,
                       A.TANGGAL, A.NAMA, A.KETERANGAN, A.KENDALA, A.PATH_FILE, B.NAMA USER_NAMA, DECODE(A.PATH_FILE, '', 0, 1) FILE_ADA
                    FROM PAKET_PROGRES_DETIL A 
                    INNER JOIN V_OAUTH_USER B ON TO_CHAR(A.USER_LOGIN_ID) = B.NIPP
                    WHERE PAKET_PROGRES_DETIL_ID IS NOT NULL           
				 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
			
		$str .= $statement." ORDER BY TANGGAL ASC";
		
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsTimeline($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
					SELECT 
                    PAKET_PROGRES_DETIL_ID, A.PAKET_PROGRES_ID, A.USER_LOGIN_ID, A.PROSENTASE,
                       A.TANGGAL, A.NAMA, A.KETERANGAN, A.KENDALA, A.PATH_FILE, DECODE(A.PATH_FILE, '', 0, 1) FILE_ADA
                    FROM PAKET_PROGRES_DETIL A INNER JOIN PAKET_PROGRES B ON A.PAKET_PROGRES_ID = B.PAKET_PROGRES_ID
                    WHERE PAKET_PROGRES_DETIL_ID IS NOT NULL AND PROSENTASE IS NOT NULL       
				 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
			
		$str .= $statement." ORDER BY PROSENTASE ASC";
		
				
		return $this->selectLimit($str,$limit,$from); 
    }
   
    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(A.PAKET_PROGRES_DETIL_ID) AS ROWCOUNT 
					FROM    PAKET_PROGRES_DETIL A
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