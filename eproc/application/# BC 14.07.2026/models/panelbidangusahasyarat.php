<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PanelBidangUsahaSyarat extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PanelBidangUsahaSyarat()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */

		$str = "
		INSERT INTO  PANEL_BIDANG_USAHA_SYARAT (
		   PANEL_ID, PANEL_BIDANG_USAHA_ID, PERSYARATAN_ID, KETERANGAN) 
 			 	VALUES (
				  ".$this->getField("PANEL_ID").",
  				  ".$this->getField("PANEL_BIDANG_USAHA_ID").",
  				  '".$this->getField("PERSYARATAN_ID")."',
				  '".$this->getField("KETERANGAN")."'
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PANEL_BIDANG_USAHA_SYARAT SET
				  PERSYARATAN_ID = '".$this->getField("PERSYARATAN_ID")."',
				  KETERANGAN = '".$this->getField("KETERANGAN")."'
				WHERE PANEL_BIDANG_USAHA_ID = ".$this->getField("PANEL_BIDANG_USAHA_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PANEL_BIDANG_USAHA_SYARAT
                WHERE 
                  PANEL_ID = ".$this->getField("PANEL_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","KETERANGAN"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PANEL_ID, PANEL_BIDANG_USAHA_ID, PERSYARATAN_ID, KETERANGAN
				FROM PANEL_BIDANG_USAHA_SYARAT A WHERE PANEL_BIDANG_USAHA_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PANEL_BIDANG_USAHA_ID DESC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PANEL_BIDANG_USAHA_ID, PERSYARATAN_ID, KETERANGAN
				FROM PANEL_BIDANG_USAHA_SYARAT WHERE PANEL_BIDANG_USAHA_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PANEL_BIDANG_USAHA_ID DESC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","KETERANGAN"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(PANEL_BIDANG_USAHA_ID) AS ROWCOUNT FROM PANEL_BIDANG_USAHA_SYARAT A WHERE PANEL_BIDANG_USAHA_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(PANEL_BIDANG_USAHA_ID) AS ROWCOUNT FROM PANEL_BIDANG_USAHA_SYARAT WHERE PANEL_BIDANG_USAHA_ID IS NOT NULL "; 
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