<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */ 
  include_once('entity.php');

  class PanelRekananBidangUsaha extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PanelRekananBidangUsaha()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		$str = "
		INSERT INTO  PANEL_REKANAN_BIDANG_USAHA (
		   PANEL_REKANAN_ID, PANEL_BIDANG_USAHA_ID, URUT) 
 			 	VALUES (
				  ".$this->getField("PANEL_REKANAN_ID").",
  				  '".$this->getField("PANEL_BIDANG_USAHA_ID")."',
				  '".$this->getField("URUT")."'
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PANEL_REKANAN_BIDANG_USAHA SET
				  PANEL_BIDANG_USAHA_ID = '".$this->getField("PANEL_BIDANG_USAHA_ID")."',
				  URUT = '".$this->getField("URUT")."'
				WHERE PANEL_REKANAN_ID = ".$this->getField("PANEL_REKANAN_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updateByField()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PANEL_REKANAN_BIDANG_USAHA A SET
				  ".$this->getField("FIELD")." = ".$this->getField("FIELD_VALUE")."
				WHERE PANEL_REKANAN_ID = ".$this->getField("PANEL_REKANAN_ID")." AND PANEL_BIDANG_USAHA_ID = '".$this->getField("PANEL_BIDANG_USAHA_ID")."'
				"; 
				$this->query = $str;
		//echo $str;
		return $this->execQuery($str);
    }
		
	function delete()
	{
        $str = "DELETE FROM PANEL_REKANAN_BIDANG_USAHA
                WHERE 
                  PANEL_REKANAN_ID = ".$this->getField("PANEL_REKANAN_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","URUT"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT A.PANEL_REKANAN_ID, TANGGAL, A.PANEL_BIDANG_USAHA_ID, URUT, KONVERSI_BIDANG_ID_KE_NAMA2(BIDANG_USAHA_ID) BIDANG_USAHA, B.NAMA
				FROM PANEL_REKANAN_BIDANG_USAHA A INNER JOIN PANEL_BIDANG_USAHA B ON A.PANEL_BIDANG_USAHA_ID = B.PANEL_BIDANG_USAHA_ID WHERE PANEL_REKANAN_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY TANGGAL DESC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
	
    function selectByParamsPersyaratan($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT DISTINCT B.PERSYARATAN_ID
                FROM PANEL_REKANAN_BIDANG_USAHA A INNER JOIN PANEL_BIDANG_USAHA_SYARAT B ON A.PANEL_BIDANG_USAHA_ID = B.PANEL_BIDANG_USAHA_ID WHERE PANEL_REKANAN_ID IS NOT NULL "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PANEL_REKANAN_ID DESC";
				
		return $this->selectLimit($str,$limit,$from); 
    }	
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PANEL_REKANAN_ID, TANGGAL, BIDANG_USAHA_ID, URUT
				FROM PANEL_REKANAN_BIDANG_USAHA WHERE PANEL_REKANAN_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY TANGGAL DESC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","URUT"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(PANEL_REKANAN_ID) AS ROWCOUNT FROM PANEL_REKANAN_BIDANG_USAHA A WHERE PANEL_REKANAN_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(PANEL_REKANAN_ID) AS ROWCOUNT FROM PANEL_REKANAN_BIDANG_USAHA WHERE PANEL_REKANAN_ID IS NOT NULL "; 
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