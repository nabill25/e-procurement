<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PanelRekananBidangUsahaNilai extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PanelRekananBidangUsahaNilai()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		$str = "
		INSERT INTO  PANEL_REKANAN_BID_USAHA_NILAI (
		   PANEL_REKANAN_ID, PANEL_BIDANG_USAHA_ID, REKANAN_PENGALAMAN_ID, NILAI, NILAI_JENIS) 
 			 	VALUES (
				  ".$this->getField("PANEL_REKANAN_ID").",
  				  '".$this->getField("PANEL_BIDANG_USAHA_ID")."',
				  '".$this->getField("REKANAN_PENGALAMAN_ID")."',
  				  '".$this->getField("NILAI")."',
  				  '".$this->getField("NILAI_JENIS")."'
				)"; 
		$this->query = $str;
		
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PANEL_REKANAN_BID_USAHA_NILAI SET
				  PANEL_BIDANG_USAHA_ID = '".$this->getField("PANEL_BIDANG_USAHA_ID")."',
 				  NILAI = '".$this->getField("NILAI")."',
				  REKANAN_PENGALAMAN_ID = '".$this->getField("REKANAN_PENGALAMAN_ID")."'
				WHERE PANEL_REKANAN_ID = ".$this->getField("PANEL_REKANAN_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PANEL_REKANAN_BID_USAHA_NILAI
                WHERE 
                  PANEL_REKANAN_ID = ".$this->getField("PANEL_REKANAN_ID")." AND 
				  PANEL_BIDANG_USAHA_ID = '".$this->getField("PANEL_BIDANG_USAHA_ID")."' "; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","REKANAN_PENGALAMAN_ID"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PANEL_REKANAN_ID, NILAI, PANEL_BIDANG_USAHA_ID, REKANAN_PENGALAMAN_ID, NILAI_JENIS
				FROM PANEL_REKANAN_BID_USAHA_NILAI WHERE PANEL_REKANAN_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY NILAI DESC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PANEL_REKANAN_ID, NILAI, PANEL_BIDANG_USAHA_ID, REKANAN_PENGALAMAN_ID
				FROM PANEL_REKANAN_BID_USAHA_NILAI WHERE PANEL_REKANAN_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY NILAI DESC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","REKANAN_PENGALAMAN_ID"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(PANEL_REKANAN_ID) AS ROWCOUNT FROM PANEL_REKANAN_BID_USAHA_NILAI WHERE PANEL_REKANAN_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(PANEL_REKANAN_ID) AS ROWCOUNT FROM PANEL_REKANAN_BID_USAHA_NILAI WHERE PANEL_REKANAN_ID IS NOT NULL "; 
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