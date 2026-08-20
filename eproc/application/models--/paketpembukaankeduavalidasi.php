<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class PaketPembukaanKeduaValidasi extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PaketPembukaanKeduaValidasi()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */

		$str = "INSERT INTO PAKET_PEMBUKAANKEDUA_VALIDASI (
				   PAKET_ID, USER_LOGIN_ID, KODE, JENIS) 
				VALUES (
				  ".$this->getField("PAKET_ID").",
				  '".$this->getField("USER_LOGIN_ID")."',
				  '".$this->getField("KODE")."',
				  '".$this->getField("JENIS")."'
				)"; 
		$this->query = $str;
		
		return $this->execQuery($str);
    }

	function deletePaktaRekanan()
	{
		/*Auto-generate primary key(s) by next max value (integer) */

		$str = "DELETE FROM PAKET_PEMBUKAANKEDUA_VALIDASI 
				WHERE 
					PAKET_ID = '".$this->getField("PAKET_ID")."' AND
					USER_LOGIN_ID = '".$this->getField("USER_LOGIN_ID")."' AND
					JENIS = 'REKANAN' "; 
		$this->query = $str;
		return $this->execQuery($str);
    }

    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PAKET_ID, USER_LOGIN_ID, KODE, JENIS, KODE_QR
				FROM PAKET_PEMBUKAANKEDUA_VALIDASI WHERE PAKET_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY USER_LOGIN_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
	
	function selectByParamsValidasi($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT A.PAKET_ID, NIP, NAMA, A.JENIS, KODE FROM 
				( 
					SELECT PAKET_ID, NIP, NAMA, 'PANITIA' JENIS 
					FROM PAKET_PANITIA 
				UNION ALL 
					SELECT PAKET_ID, CAST(C.NIP AS TEXT) NIP, 
					C.USER_NAMA NAMA, 'PEMBUAT' JENIS 
				FROM PAKET A 
				LEFT JOIN USER_LOGIN C ON C.USER_LOGIN_ID = A.USER_LOGIN_ID) A 
                LEFT JOIN PAKET_PEMBUKAANKEDUA_VALIDASI B ON A.PAKET_ID = B.PAKET_ID AND A.NIP = B.KODE
                 WHERE 1 = 1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement."  ORDER BY A.JENIS DESC ";
		
		return $this->selectLimit($str,$limit,$from); 
    }

	function selectByParamsValidasiPublish($paket_id)
	{
		$str = "SELECT (SELECT COUNT(1) FROM PAKET_ANGGOTA WHERE PAKET_ID = ".$paket_id." AND JENIS IN ('PANITIA', 'FUNGSIONAL')) TOTAL_ANGGOTA,
				COALESCE((SELECT COUNT(1) FROM PAKET_ANGGOTA A INNER JOIN PAKET_PEMBUKAANKEDUA_VALIDASI B ON A.PAKET_ID = B.PAKET_ID AND A.NIP = B.KODE WHERE A.PAKET_ID = ".$paket_id." AND A.JENIS IN ('PANITIA', 'FUNGSIONAL')), 0) TOTAL_TTD
				 FROM DUAL
			  "; 				
		return $this->selectLimit($str,-1,-1); 
    }
	    
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","USER_LOGIN_ID"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(PAKET_ID) AS ROWCOUNT FROM PAKET_PEMBUKAANKEDUA_VALIDASI A WHERE PAKET_ID IS NOT NULL "; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= " ".$statement;
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }

  } 
?>