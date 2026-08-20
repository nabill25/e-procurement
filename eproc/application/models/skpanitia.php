<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class SKPanitia extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
		parent::__construct();
	}
	
    function SKPanitia()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("SK_PANITIA_ID", $this->getNextId("SK_PANITIA_ID","SK_PANITIA")); 

		$str = "
		INSERT INTO SK_PANITIA (
		   SK_PANITIA_ID, NO_SK, TANGGAL, PEJABAT_PENETAP, PEJABAT_PENETAP_NIP, TANGGAL_MULAI, TANGGAL_AKHIR, STATUS, UNIT_KERJA, UNIT_KERJA_ID,
		   	AKTIF) 
 			 	VALUES (
				  ".$this->getField("SK_PANITIA_ID").",
  				  '".$this->getField("NO_SK")."',
				  ".$this->getField("TANGGAL").", 	
    			  '".$this->getField("PEJABAT_PENETAP")."',
      			  '".$this->getField("PEJABAT_PENETAP_NIP")."',
  				  ".$this->getField("TANGGAL_MULAI").",
				  ".$this->getField("TANGGAL_AKHIR").",	
				  ".$this->getField("STATUS").",
				  '".$this->getField("UNIT_KERJA")."',
				  '".$this->getField("UNIT_KERJA_ID")."',
				  '".$this->getField("AKTIF")."'
				)"; 
		$this->query = $str;
		$this->id = $this->getField("SK_PANITIA_ID");
		//echo $str;exit;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE SK_PANITIA SET
				  NO_SK = '".$this->getField("NO_SK")."',
				  TANGGAL = ".$this->getField("TANGGAL").", 
				  UNIT_KERJA = '".$this->getField("UNIT_KERJA")."',
				  UNIT_KERJA_ID = '".$this->getField("UNIT_KERJA_ID")."',
				  PEJABAT_PENETAP = '".$this->getField("PEJABAT_PENETAP")."',
				  PEJABAT_PENETAP_NIP = '".$this->getField("PEJABAT_PENETAP_NIP")."',
				  TANGGAL_MULAI = ".$this->getField("TANGGAL_MULAI").",	 
				  TANGGAL_AKHIR = ".$this->getField("TANGGAL_AKHIR").",	 
				  STATUS = ".$this->getField("STATUS").",
				  AKTIF 	= '".$this->getField("AKTIF")."'
				WHERE SK_PANITIA_ID = ".$this->getField("SK_PANITIA_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }

  function updateFile()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE SK_PANITIA SET
				  FILE_SK = '".$this->getField("FILE_SK")."',
				  PATH_FILE = '".$this->getField("PATH_FILE")."', 
				  UPDATED_BY = ".$this->getField("UPDATED_BY").",
				  UPDATED_DATE 	= CURRENT_TIMESTAMP
				WHERE SK_PANITIA_ID = ".$this->getField("SK_PANITIA_ID")."
				"; 
				// echo $str; die;
				$this->query = $str;
		return $this->execQuery($str);
  }
	
	function delete()
	{
		$str1 = "DELETE FROM PANITIA
                WHERE 
                  SK_PANITIA_ID = ".$this->getField("SK_PANITIA_ID").""; 
				  
		$this->query = $str1;
        $this->execQuery($str1);
		
        $str = "DELETE FROM SK_PANITIA
                WHERE 
                  SK_PANITIA_ID = ".$this->getField("SK_PANITIA_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

	
	/*function delete()
	{
        $str = "DELETE FROM SK_PANITIA
                WHERE 
                  SK_PANITIA_ID = ".$this->getField("SK_PANITIA_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }*/

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","TANGGAL"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="ORDER BY UNIT_KERJA ASC, TANGGAL DESC")
	{
		$str = "SELECT A.SK_PANITIA_ID, A.NO_SK, TO_CHAR(A.TANGGAL, 'YYYY-MM-DD') TANGGAL, A.PEJABAT_PENETAP, A.PEJABAT_PENETAP_NIP, TO_CHAR(A.TANGGAL_MULAI, 'YYYY-MM-DD') TANGGAL_MULAI, TO_CHAR(A.TANGGAL_AKHIR, 'YYYY-MM-DD') TANGGAL_AKHIR, 
						A.AKTIF STATUS, A.UNIT_KERJA, A.UNIT_KERJA_ID, B.NAMA NAMA_UNIT_KERJA, A.AKTIF, A.FILE_SK, A.PATH_FILE
                FROM SK_PANITIA A
                INNER JOIN UNIT_KERJA B ON A.UNIT_KERJA_ID=B.UNIT_KERJA_ID
                WHERE A.SK_PANITIA_ID IS NOT NULL "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
		$str .= $statement." ".$order;
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsTerakhir($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT SK_PANITIA_ID, NO_SK, TANGGAL, PEJABAT_PENETAP, PEJABAT_PENETAP_NIP, TANGGAL_MULAI, TANGGAL_AKHIR, STATUS, UNIT_KERJA, UNIT_KERJA_ID, NIP, USER_LOGIN_ID, USER_NAMA, KETUA, STATUS_PANITIA
				FROM SK_PANITIA_TERAKHIR WHERE SK_PANITIA_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY UNIT_KERJA ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }


  function selectByParamsTerakhirAggota()
	{
		$str = "SELECT A.SK_PANITIA_ID, A.NO_SK, A.TANGGAL, A.PEJABAT_PENETAP, A.PEJABAT_PENETAP_NIP, A.TANGGAL_MULAI, 
						A.TANGGAL_AKHIR, A.STATUS, A.UNIT_KERJA, A.UNIT_KERJA_ID, A.NIP, A.USER_LOGIN_ID, A.USER_NAMA, A.KETUA, A.STATUS_PANITIA
						FROM SK_PANITIA_TERAKHIR A
						JOIN (SELECT SK_PANITIA_ID FROM SK_PANITIA_TERAKHIR WHERE SK_PANITIA_ID IS NOT NULL
						AND USER_LOGIN_ID=".$this->USER_LOGIN_ID.") B ON A.SK_PANITIA_ID=B.SK_PANITIA_ID
						WHERE A.SK_PANITIA_ID IS NOT NULL AND KETUA = '0'
		";  
		
		$this->query = $str; 
				
		return $this->selectLimit($str,$limit,$from); 
    }

    
	    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT  SK_PANITIA_ID, NO_SK, TANGGAL, PEJABAT_PENETAP, PEJABAT_PENETAP_NIP, TANGGAL_MULAI, TANGGAL_AKHIR, STATUS,AKTIF 
				FROM SK_PANITIA WHERE SK_PANITIA_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY TANGGAL ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","TANGGAL"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(SK_PANITIA_ID) AS ROWCOUNT FROM SK_PANITIA WHERE SK_PANITIA_ID IS NOT NULL "; 
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

    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(SK_PANITIA_ID) AS ROWCOUNT FROM SK_PANITIA WHERE SK_PANITIA_ID IS NOT NULL "; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }	
  } 
?>