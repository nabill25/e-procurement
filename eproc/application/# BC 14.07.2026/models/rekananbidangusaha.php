<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class RekananBidangUsaha extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
  		parent::__construct();
  	}
  	
    function RekananBidangUsaha()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_BIDANG_USAHA_ID", $this->getNextId("REKANAN_BIDANG_USAHA_ID","REKANAN_BIDANG_USAHA")); 

		$str = "
				INSERT INTO REKANAN_BIDANG_USAHA (
					REKANAN_BIDANG_USAHA_ID, 
					REKANAN_ID, 
					BIDANG_USAHA_ID)
				VALUES ( '".$this->getField("REKANAN_BIDANG_USAHA_ID")."', 
					'".$this->getField("REKANAN_ID")."',
					'".$this->getField("BIDANG_USAHA_ID")."')
		"; 
		//echo $str;
		$this->query = $str;
		
		return $this->execQuery($str);
    }
	
	function insert_ijin_usaha()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_BIDANG_USAHA_ID", $this->getNextId("REKANAN_BIDANG_USAHA_ID","REKANAN_BIDANG_USAHA")); 

		$str = "
				INSERT INTO REKANAN_BIDANG_USAHA (
					REKANAN_BIDANG_USAHA_ID, 
					REKANAN_ID, 
					BIDANG_USAHA_ID,
					IJIN_USAHA_ID,
					CREATED_BY,
					CREATED_DATE)
				VALUES ( '".$this->getField("REKANAN_BIDANG_USAHA_ID")."', 
					'".$this->getField("REKANAN_ID")."',
					'".$this->getField("BIDANG_USAHA_ID")."',
					'".$this->getField("IJIN_USAHA_ID")."',
					".$this->getField("CREATED_BY").",
					CURRENT_TIMESTAMP
				)
		"; 
		//echo $str;
		$this->query = $str;
		
		return $this->execQuery($str);
    }

	function insert_sbu()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_BIDANG_USAHA_ID", $this->getNextId("REKANAN_BIDANG_USAHA_ID","REKANAN_BIDANG_USAHA")); 

		$str = "
				INSERT INTO REKANAN_BIDANG_USAHA (
					REKANAN_BIDANG_USAHA_ID, 
					REKANAN_ID, 
					BIDANG_USAHA_ID,
					IJIN_USAHA_ID,
					REKANAN_BIDANG_USAHA_INFO_ID,
					CREATED_BY,
					CREATED_DATE)
				VALUES ( '".$this->getField("REKANAN_BIDANG_USAHA_ID")."', 
					'".$this->getField("REKANAN_ID")."',
					'".$this->getField("BIDANG_USAHA_ID")."',
					'".$this->getField("IJIN_USAHA_ID")."',
					".$this->getField("REKANAN_BIDANG_USAHA_INFO_ID").",
					".$this->getField("CREATED_BY").",
					CURRENT_TIMESTAMP
				)
		"; 
		//echo $str;
		$this->query = $str;
		//echo $str;exit;
		return $this->execQuery($str);
    }
		
    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_BIDANG_USAHA 
				SET
					REKANAN_ID = '".$this->getField("REKANAN_ID")."',
					BIDANG_USAHA_ID = '".$this->getField("BIDANG_USAHA_ID")."',
					REKANAN_BIDANG_USAHA_INFO_ID = '".$this->getField("REKANAN_BIDANG_USAHA_INFO_ID")."'
				WHERE REKANAN_BIDANG_USAHA_ID = '".$this->getField("REKANAN_BIDANG_USAHA_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function update_onedha()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_BIDANG_USAHA 
				SET
					IJIN_USAHA_ID = '".$this->getField("IJIN_USAHA_ID")."'
				WHERE REKANAN_ID = '".$this->getField("REKANAN_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete($str='')
	{
        $str = "DELETE FROM REKANAN_BIDANG_USAHA
                WHERE 
                  REKANAN_ID = '".$this->getField("REKANAN_ID")."' ".$str; 
				  
		$this->query = $str;
		//echo $str;exit;
        return $this->execQuery($str);
    }
	
	function delete_bidang_usaha_registrasi()
	{
        $str = "DELETE FROM REKANAN_BIDANG_USAHA
                WHERE 
                  REKANAN_ID = '".$this->getField("REKANAN_ID")."' AND  IJIN_USAHA_ID = ".$this->getField("IJIN_USAHA_ID").""; 
				  
		$this->query = $str;
		//echo $str;exit;
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
		$str = "
			SELECT 
				REKANAN_BIDANG_USAHA_ID, 
				REKANAN_ID, 
				BIDANG_USAHA_ID, 
				REKANAN_BIDANG_USAHA_INFO_ID
			FROM REKANAN_BIDANG_USAHA
			WHERE REKANAN_BIDANG_USAHA_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_BIDANG_USAHA_ID ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsMonitoring($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT BIDANG_USAHA_ID, AMBIL_BIDANG_USAHA_NAMA(BIDANG_USAHA_ID) NAMA, VALIDASI, REKANAN_BIDANG_USAHA_ID FROM REKANAN_BIDANG_USAHA
			WHERE REKANAN_BIDANG_USAHA_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ORDER BY REKANAN_BIDANG_USAHA_ID ASC";
				
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT 
				REKANAN_BIDANG_USAHA_ID, 
				REKANAN_ID, 
				BIDANG_USAHA_ID, 
				REKANAN_BIDANG_USAHA_INFO_ID
			FROM REKANAN_BIDANG_USAHA
			WHERE REKANAN_BIDANG_USAHA_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_BIDANG_USAHA_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{		
		$str = "SELECT COUNT(REKANAN_BIDANG_USAHA_ID) AS ROWCOUNT FROM REKANAN_BIDANG_USAHA WHERE REKANAN_BIDANG_USAHA_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(REKANAN_BIDANG_USAHA_ID) AS ROWCOUNT FROM REKANAN_BIDANG_USAHA WHERE REKANAN_BIDANG_USAHA_ID IS NOT NULL "; 
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

    function update_validasi()
		{
			$str = "UPDATE REKANAN_BIDANG_USAHA 
					SET
						VALIDASI = '".$this->getField("VALIDASI")."',
						VALIDASI_DATE = CURRENT_TIMESTAMP,
						VALIDASI_BY = ".$this->getField("CREATED_BY")."
					WHERE REKANAN_ID = '".$this->getField("REKANAN_ID")."' AND REKANAN_BIDANG_USAHA_ID = '".$this->getField("REKANAN_BIDANG_USAHA_ID")."'
					"; 
					$this->query = $str;
			return $this->execQuery($str);
	    }
  } 
?>