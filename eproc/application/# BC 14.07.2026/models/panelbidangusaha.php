<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PanelBidangUsaha extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PanelBidangUsaha()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PANEL_BIDANG_USAHA_ID", $this->getNextId("PANEL_BIDANG_USAHA_ID","PANEL_BIDANG_USAHA")); 

		$str = "
		INSERT INTO  PANEL_BIDANG_USAHA (
		   PANEL_BIDANG_USAHA_ID, PANEL_ID, BIDANG_USAHA_ID, KETERANGAN, NAMA) 
 			 	VALUES (
				  ".$this->getField("PANEL_BIDANG_USAHA_ID").",
  				  ".$this->getField("PANEL_ID").",
				  KONVERSI_BIDANG_NAMA_KE_ID('".$this->getField("BIDANG_USAHA_ID")."'),
				  '".$this->getField("KETERANGAN")."',
				  '".$this->getField("NAMA")."'  			 
				)"; 
				
		$this->query = $str;
	
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PANEL_BIDANG_USAHA SET
				  PANEL_ID = ".$this->getField("PANEL_ID").",
				  BIDANG_USAHA_ID = '".$this->getField("BIDANG_USAHA_ID")."' ,
				  KETERANGAN = '".$this->getField("KETERANGAN")."' 
				WHERE PANEL_BIDANG_USAHA_ID = ".$this->getField("PANEL_BIDANG_USAHA_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PANEL_BIDANG_USAHA
                WHERE 
                  PANEL_ID = ".$this->getField("PANEL_ID").""; 
				  
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
		$str = "SELECT PANEL_BIDANG_USAHA_ID, PANEL_ID, BIDANG_USAHA_ID, KONVERSI_BIDANG_ID_KE_NAMA(BIDANG_USAHA_ID) NAMA_BIDANG, KONVERSI_BIDANG_ID_KE_NAMA2(BIDANG_USAHA_ID) NAMA_BIDANG_FULL,KETERANGAN, PERUBAHAN_DATA, NAMA 
				FROM PANEL_BIDANG_USAHA A WHERE PANEL_BIDANG_USAHA_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PANEL_BIDANG_USAHA_ID ASC";
		
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsPeringkat($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PANEL_BIDANG_USAHA_ID, PANEL_ID, BIDANG_USAHA_ID, KONVERSI_BIDANG_ID_KE_NAMA(BIDANG_USAHA_ID) NAMA_BIDANG, KONVERSI_BIDANG_ID_KE_NAMA2(BIDANG_USAHA_ID) NAMA_BIDANG_FULL,KETERANGAN, PERUBAHAN_DATA, NAMA 
				FROM PANEL_BIDANG_USAHA A WHERE PANEL_BIDANG_USAHA_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PANEL_BIDANG_USAHA_ID ASC";
		
		return $this->selectLimit($str,$limit,$from); 
    }
	
    function selectByParamsPilihBidangUsaha($paramsArray=array(),$limit=-1,$from=-1, $statement='',$rekanan_id='', $panel_rekanan_id='')
	{
		$str = "SELECT A.PANEL_BIDANG_USAHA_ID, PANEL_ID, A.BIDANG_USAHA_ID, 
				KONVERSI_BIDANG_ID_KE_NAMA2(A.BIDANG_USAHA_ID) BIDANG_USAHA, KETERANGAN, 
                (SELECT COUNT(1) FROM REKANAN_BIDANG_USAHA X 
                 WHERE X.REKANAN_ID = '".$rekanan_id."' AND X.IJIN_USAHA_ID = 1 AND BIDANG_USAHA_ID IN (SELECT REGEXP_SUBSTR(UPPER(A.BIDANG_USAHA_ID),'[^,]+', 1, LEVEL) FROM DUAL 
                 CONNECT BY REGEXP_SUBSTR(UPPER(A.BIDANG_USAHA_ID), '[^,]+', 1, LEVEL) IS NOT NULL)) BIDANG_USAHA_ID_REKANAN, 
							C.PANEL_BIDANG_USAHA_ID PANEL_BIDANG_USAHA_ID_PILIHAN, A.NAMA
							FROM PANEL_BIDANG_USAHA A
					 LEFT JOIN PANEL_REKANAN_BIDANG_USAHA C
					 ON A.PANEL_BIDANG_USAHA_ID = C.PANEL_BIDANG_USAHA_ID AND C.PANEL_REKANAN_ID = '".$panel_rekanan_id."'
			   WHERE A.PANEL_BIDANG_USAHA_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY A.PANEL_BIDANG_USAHA_ID ASC";
		//echo $str;
		return $this->selectLimit($str,$limit,$from); 
    }


    function selectByParamsRekananTanpaBidangUsaha($paramsArray=array(),$limit=-1,$from=-1, $id='', $statement='')
	{
		$str = "SELECT DISTINCT A.REKANAN_ID, NAMA, TRIM(ALAMAT) ALAMAT, EMAIL
				   FROM REKANAN A
				  WHERE NOT EXISTS(SELECT 1 FROM PANEL_REKANAN X WHERE A.REKANAN_ID = X.REKANAN_ID AND PANEL_ID = ".$id.") ".$statement; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= " ORDER BY NAMA ASC";
		
		return $this->selectLimit($str,$limit,$from); 
    }



    function selectByParamsRekanan($paramsArray=array(),$limit=-1,$from=-1, $id='', $statement='')
	{
		$str = "SELECT DISTINCT A.REKANAN_ID, NAMA, TRIM(ALAMAT) ALAMAT, EMAIL
				   FROM REKANAN_BIDANG_USAHA A, REKANAN B
				  WHERE A.REKANAN_ID = B.REKANAN_ID
					AND EXISTS (
						   SELECT 1
							 FROM PANEL_BIDANG_USAHA X
							WHERE X.BIDANG_USAHA_ID = A.BIDANG_USAHA_ID
							  AND PANEL_ID = ".$id.")  AND NOT EXISTS(SELECT 1 FROM PANEL_REKANAN X WHERE A.REKANAN_ID = X.REKANAN_ID AND PANEL_ID = ".$id.") ".$statement; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
		$str .= " ORDER BY NAMA ASC";
		
		return $this->selectLimit($str,$limit,$from); 
    }

    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT  PANEL_BIDANG_USAHA_ID, PANEL_ID, BIDANG_USAHA_ID 
				FROM PANEL_BIDANG_USAHA WHERE PANEL_BIDANG_USAHA_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PANEL_BIDANG_USAHA_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(PANEL_BIDANG_USAHA_ID) AS ROWCOUNT FROM PANEL_BIDANG_USAHA WHERE PANEL_BIDANG_USAHA_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(PANEL_BIDANG_USAHA_ID) AS ROWCOUNT FROM PANEL_BIDANG_USAHA WHERE PANEL_BIDANG_USAHA_ID IS NOT NULL "; 
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