<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class KatalogKategori extends Entity{ 

	var $query; 

    function __construct(){
  		parent::__construct();
	}
	
	function insert()
	{
		$str = "
		INSERT INTO  KATALOG_KATEGORI (
		   KATEGORI_ID, NAMA_KATEGORI_1, NAMA_KATEGORI_2, KATEGORI_PARENT_ID, KODE, NAMA, KATEGORI_STATUS) 
 			 	VALUES (
				  '".$this->getField("KATEGORI_ID")."',
  				  '".$this->getField("NAMA_KATEGORI_1")."',
  				  '".$this->getField("NAMA_KATEGORI_2")."',
  				  '".$this->getField("KATEGORI_PARENT_ID")."',
				  '".$this->getField("KODE")."',
				  '".$this->getField("NAMA")."',
				  '".$this->getField("KATEGORI_STATUS")."'
				)"; 
				// echo $str; die();
		$this->query = $str;
		return $this->execQuery($str);
    } 


    function insertParent()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("KATEGORI_ID", $this->getNextId("KATEGORI_ID","KATALOG_KATEGORI")); 

		$str = "
		INSERT INTO KATALOG_KATEGORI (
		   KATEGORI_ID, NAMA_KATEGORI_1, NAMA_KATEGORI_2, KATEGORI_PARENT_ID, KODE, NAMA, KATEGORI_STATUS, CREATED_DATE, CREATED_BY ) 
 			 	VALUES (
				  '".$this->getField("KATEGORI_ID")."',
  				  '".$this->getField("NAMA_KATEGORI_1")."',
  				  '".$this->getField("NAMA_KATEGORI_2")."',
  				  '".$this->getField("KATEGORI_PARENT_ID")."',
				  '".$this->getField("KODE")."',
				  '".$this->getField("NAMA")."',
				  '".$this->getField("KATEGORI_STATUS")."',
				  NOW(),
				  '".$this->getField("CREATED_BY")."'
				)"; 
		$this->query = $str;
		$this->id = $this->getField("KATEGORI_ID");
		//echo $str;exit;
		return $this->execQuery($str);
  }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE KATALOG_KATEGORI SET
				  NAMA_KATEGORI_1 = '".$this->getField("NAMA_KATEGORI_1")."',
				  NAMA_KATEGORI_2 = '".$this->getField("NAMA_KATEGORI_2")."',
				  KODE = '".$this->getField("KODE")."',
				  NAMA = '".$this->getField("NAMA")."',
				  KATEGORI_STATUS = '".$this->getField("KATEGORI_STATUS")."'
				WHERE KATEGORI_ID = '".$this->getField("KATEGORI_ID")."'
				"; 
				$this->query = $str;
				
		return $this->execQuery($str);
    } 
	
	function delete()
	{ 
		
		$str = " DELETE FROM KATALOG_KATEGORI WHERE
					KATEGORI_ID = '".$this->getField("KATEGORI_ID")."' "; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT A.* FROM (
				SELECT KATEGORI_ID, KATEGORI_PARENT_ID, KODE, NAMA, LOWER(REPLACE(nama_kategori_1, ' ', '-')) url,LOWER(REPLACE(nama_kategori_2, ' ', '-')) url2,LOWER(REPLACE(nama, ' ', '-')) url3,
				CONCAT(NAMA_KATEGORI_2,' - ', NAMA) NAMA_VIEW,KATEGORI_STATUS, NAMA_KATEGORI_1, NAMA_KATEGORI_2
				FROM KATALOG_KATEGORI A WHERE KATEGORI_ID IS NOT NULL AND KATEGORI_STATUS = '1' 
				) A 
				WHERE 1=1 
				"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ORDER BY KATEGORI_ID ASC";
		// echo $str; die();
		$this->query = $str;
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsAll($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT KATEGORI_ID, KATEGORI_PARENT_ID, KODE, NAMA,
				-- CONCAT(NAMA_KATEGORI_2,' - ', NAMA) NAMA_VIEW,KATEGORI_STATUS
				NAMA NAMA_VIEW, NAMA_KATEGORI_2 KATEGORI_1, NAMA_KATEGORI_2 KATEGORI_2, KATEGORI_STATUS
				FROM KATALOG_KATEGORI A WHERE KATEGORI_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ORDER BY KATEGORI_ID ASC";
		// echo $str; die();
		$this->query = $str;
				
		return $this->selectLimit($str,$limit,$from); 
    } 

    function selectByParamsPanelKatalogKategori($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				SELECT A.KATEGORI_ID, A.KATEGORI_PARENT_ID, A.NAMA FROM KATALOG_KATEGORI_PANEL A
                WHERE 1 = 1 AND KATEGORI_STATUS = 1 	
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY A.KATEGORI_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsPanelRekananKatalogKategori($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				SELECT A.KATEGORI_ID, A.KATEGORI_PARENT_ID, A.NAMA FROM KATALOG_KATEGORI_PANEL_REKANAN A
                WHERE 1 = 1	 AND KATEGORI_STATUS = 1	
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY A.KATEGORI_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
					    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT  KATEGORI_ID, KATEGORI_PARENT_ID, KODE, NAMA
				FROM KATALOG_KATEGORI WHERE KATEGORI_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY NAMA ASC";
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsLevel($level)
	{
		$str = "SELECT NAMA_KATEGORI_1 NAMA_KATEGORI from KATALOG_KATEGORI
				group by NAMA_KATEGORI_1
				order by NAMA_KATEGORI_1 asc
						 ";

		$this->query = $str;

		return $this->selectLimit($str,$limit,$from);
  }	

  function selectByParamsLevel2($level)
	{
		$str = "SELECT NAMA_KATEGORI_2 NAMA_KATEGORI from KATALOG_KATEGORI
				WHERE NAMA_KATEGORI_1 = '".$level."' AND KATEGORI_PARENT_ID = '1'
				group by NAMA_KATEGORI_2
				order by NAMA_KATEGORI_2 asc
						 ";

		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
  }	

    function getKatalogKategori($KatalogKategoriId)
	{
		$str = "SELECT AMBIL_KATALOG_KATEGORI_NAMA('".$KatalogKategoriId."') KATALOG_KATEGORI FROM DUAL "; 
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("KATALOG_KATEGORI"); 
		else 
			return ""; 
    }
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
	
	function getCountByParamsMaxBidangId($paramsArray=array())
	{
		$str = "SELECT MAX(KATEGORI_ID) MAXID FROM KATALOG_KATEGORI WHERE 1=1 "; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("MAXID"); 
		else 
			return 0; 
    }

    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(KATEGORI_ID) AS ROWCOUNT FROM KATALOG_KATEGORI WHERE KATEGORI_ID IS NOT NULL  AND KATEGORI_STATUS = 1 "; 
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
		$str = "SELECT COUNT(KATEGORI_ID) AS ROWCOUNT FROM KATALOG_KATEGORI WHERE KATEGORI_ID IS NOT NULL "; 
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