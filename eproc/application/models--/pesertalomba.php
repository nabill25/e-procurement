<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PesertaLomba extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PesertaLomba()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PESERTA_LOMBA_ID", $this->getNextId("PESERTA_LOMBA_ID","PESERTA_LOMBA")); 

		$str = "
						INSERT INTO  PESERTA_LOMBA (
				   PESERTA_LOMBA_ID, PENDIDIKAN_ID, KODE, 
				   NAMA, ALAMAT, KOTA, 
				   TELEPON_KODE, TELEPON, FAX_KODE, 
				   FAX, EMAIL, TANDA_PENGENAL, 
				   TANDA_NOMOR, TEMPAT_LAHIR, TANGGAL_LAHIR, 
				   TANGGAL_DAFTAR, STATUS, NO_AIA) 
			  	VALUES (
				  ".$this->getField("PESERTA_LOMBA_ID").",
				  ".$this->getField("PENDIDIKAN_ID").",
  				  '".$this->getField("KODE")."',
				  '".$this->getField("NAMA")."',
   				  '".$this->getField("ALAMAT")."',
				  '".$this->getField("KOTA")."',
				  '".$this->getField("TELEPON_KODE")."',
				  '".$this->getField("TELEPON")."',
   				  '".$this->getField("FAX_KODE")."',
				  '".$this->getField("FAX")."',				  
				  '".$this->getField("EMAIL")."',
				  '".$this->getField("TANDA_PENGENAL")."',
   				  '".$this->getField("TANDA_NOMOR")."',
				  '".$this->getField("TEMPAT_LAHIR")."',				  
				  '".$this->getField("TANGGAL_LAHIR")."',
				  '".$this->getField("TANGGAL_DAFTAR")."',
   				  '".$this->getField("STATUS")."',
				  '".$this->getField("NO_AIA")."'
				)"; 
		//echo $str;		
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				UPDATE  PESERTA_LOMBA
				SET   
					   PENDIDIKAN_ID    = ".$this->getField("PENDIDIKAN_ID").",
					   KODE             = '".$this->getField("KODE")."',
					   NAMA             = '".$this->getField("NAMA")."',
					   ALAMAT           = '".$this->getField("ALAMAT")."',
					   KOTA             = '".$this->getField("KOTA")."',
					   TELEPON_KODE     = '".$this->getField("TELEPON_KODE")."',
					   TELEPON          = '".$this->getField("TELEPON")."',
					   FAX_KODE         = '".$this->getField("FAX_KODE")."',
					   FAX              = '".$this->getField("FAX")."',
					   EMAIL            = '".$this->getField("EMAIL")."',
					   TANDA_PENGENAL   = '".$this->getField("TANDA_PENGENAL")."',
					   TANDA_NOMOR      = '".$this->getField("TANDA_NOMOR")."',
					   TEMPAT_LAHIR     = '".$this->getField("TEMPAT_LAHIR")."',
					   TANGGAL_LAHIR    = '".$this->getField("TANGGAL_LAHIR")."',
					   TANGGAL_DAFTAR   = '".$this->getField("TANGGAL_DAFTAR")."',
					   STATUS           = '".$this->getField("STATUS")."',
					   NO_AIA           = '".$this->getField("NO_AIA")."'
				WHERE  PESERTA_LOMBA_ID = ".$this->getField("PESERTA_LOMBA_ID")."
			 "; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function update_sayembara()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str1 = "
				UPDATE  USER_LOGIN
				SET   
					   USER_LOGIN             = '".$this->getField("EMAIL")."'
				WHERE  USER_LOGIN = '".$this->getField("EMAIL_TMP")."'
			 "; 
				$this->query = $str1;
				//echo $str;
		$this->execQuery($str1);
		
		$str = "
				UPDATE  PESERTA_LOMBA
				SET   
					   NAMA             = '".$this->getField("NAMA")."',
					   ALAMAT           = '".$this->getField("ALAMAT")."',
					   EMAIL            = '".$this->getField("EMAIL")."'
				WHERE  EMAIL = '".$this->getField("EMAIL_TMP")."'
			 "; 
				$this->query = $str;
				//echo $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PESERTA_LOMBA
                WHERE 
                  PESERTA_LOMBA_ID = ".$this->getField("PESERTA_LOMBA_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","PENDIDIKAN_ID"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				 SELECT 
				   PESERTA_LOMBA_ID, PENDIDIKAN_ID, KODE, 
				   NAMA, ALAMAT, KOTA, 
				   TELEPON_KODE, TELEPON, FAX_KODE, 
				   FAX, EMAIL, TANDA_PENGENAL, 
				   TANDA_NOMOR, TEMPAT_LAHIR, TANGGAL_LAHIR, 
				   TANGGAL_DAFTAR, STATUS, NO_AIA
				FROM  PESERTA_LOMBA 
		        WHERE PESERTA_LOMBA_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY NAMA ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = " SELECT 
				   PESERTA_LOMBA_ID, PENDIDIKAN_ID, KODE, 
				   NAMA, ALAMAT, KOTA, 
				   TELEPON_KODE, TELEPON, FAX_KODE, 
				   FAX, EMAIL, TANDA_PENGENAL, 
				   TANDA_NOMOR, TEMPAT_LAHIR, TANGGAL_LAHIR, 
				   TANGGAL_DAFTAR, STATUS, NO_AIA
				FROM  PESERTA_LOMBA 
		        WHERE PESERTA_LOMBA_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY NAMA ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","PENDIDIKAN_ID"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(PESERTA_LOMBA_ID) AS ROWCOUNT FROM PESERTA_LOMBA WHERE PESERTA_LOMBA_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(PESERTA_LOMBA_ID) AS ROWCOUNT FROM PESERTA_LOMBA WHERE PESERTA_LOMBA_ID IS NOT NULL "; 
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