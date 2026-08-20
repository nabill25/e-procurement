<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class RekananPajak extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
  		parent::__construct();
  	}
  	
    function RekananPajak()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_PAJAK_ID", $this->getNextId("REKANAN_PAJAK_ID","REKANAN_PAJAK")); 

		$str = "
				INSERT INTO REKANAN_PAJAK (
					REKANAN_PAJAK_ID, 
					REKANAN_ID, 
					NOMOR, 
					TANGGAL, 
					BULAN, 
					TAHUN, 
					TIPE,
					PATH_FILE, NAMA_FILE, CREATED_BY, CREATED_DATE)
				VALUES ( '".$this->getField("REKANAN_PAJAK_ID")."', 
					'".$this->getField("REKANAN_ID")."',
					'".$this->getField("NOMOR")."', 
					".$this->getField("TANGGAL").", 
					'".$this->getField("BULAN")."', 
					'".$this->getField("TAHUN")."', 
					'".$this->getField("TIPE")."', 
					'".$this->getField("PATH_FILE")."',
					'".$this->getField("NAMA_FILE")."',
					".$this->getField("CREATED_BY").",
					CURRENT_TIMESTAMP
					)
		"; 	
		$this->query = $str; 
		//echo $str;exit;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_PAJAK 
				SET
					TAHUN = '".$this->getField("TAHUN")."',
					NOMOR = '".$this->getField("NOMOR")."',
					TANGGAL = ".$this->getField("TANGGAL").",
					PATH_FILE = '".$this->getField("PATH_FILE")."',
					NAMA_FILE = '".$this->getField("NAMA_FILE")."',		
					UPDATED_BY = ".$this->getField("CREATED_BY").",		
					UPDATED_DATE = CURRENT_TIMESTAMP			
				WHERE REKANAN_PAJAK_ID = '".$this->getField("REKANAN_PAJAK_ID")."' AND REKANAN_ID = '".$this->getField("REKANAN_ID")."'
				"; 
				$this->query = $str;
				
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM REKANAN_PAJAK
                WHERE 
                  REKANAN_PAJAK_ID = '".$this->getField("REKANAN_PAJAK_ID")."' AND REKANAN_ID = '".$this->getField("REKANAN_ID")."' "; 
				  
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
    function selectByParams($paramsArray=array(), $limit=-1,$from=-1, $statement='', $order=" ORDER BY TO_NUMBER(BULAN, '9999'), TAHUN ASC")
	{
		$str = "
			SELECT 
				REKANAN_PAJAK_ID, 
				REKANAN_ID, 
				NOMOR, 
				TO_CHAR(TANGGAL, 'YYYY-MM-DD')TANGGAL, 
				BULAN, 
				TAHUN, 
				LPAD(BULAN, 2, '0') || TAHUN PERIODE,
				TIPE, 
				PATH_FILE, NAMA_FILE
			FROM REKANAN_PAJAK
			WHERE REKANAN_PAJAK_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val'" ;
		}
		$str .= $statement." ".$order;
		$this->query = $str;
		
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsMonitoring($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT REKANAN_PAJAK_ID, 
					REKANAN_ID, 
					NOMOR, 
					TANGGAL, 
					BULAN, 
					TAHUN, 
					TIPE, 
					PATH_FILE,NAMA_FILE FROM
					( SELECT row_number() OVER ()::varchar AS pivot
					   FROM pivoting
					 LIMIT 12) A
					LEFT JOIN
					(  		
					SELECT 
						REKANAN_PAJAK_ID, 
						REKANAN_ID, 
						NOMOR, 
						TO_CHAR(TANGGAL, 'YYYY-MM-DD') TANGGAL, 
						BULAN, 
						TAHUN, 
						TIPE, 
						PATH_FILE, NAMA_FILE
					FROM REKANAN_PAJAK
					WHERE REKANAN_PAJAK_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ) B ON BULAN = PIVOT
					ORDER BY TO_NUMBER(BULAN, '9999'), TAHUN ASC    ";
		$this->query = $str;
					// echo $str; die();
		return $this->selectLimit($str,$limit,$from); 
    }
	
    function selectByParamsKualifikasi($rekanan_id)
	{
		$str = "
			SELECT CONCAT('SPT Th. ' , MAX(TAHUN)) KETERANGAN, 1 BULAN, TIPE  FROM REKANAN_PAJAK WHERE TIPE = '1' AND REKANAN_ID = ".$rekanan_id." GROUP BY REKANAN_ID, TIPE
			UNION ALL
			SELECT CONCAT('PPH ' , AMBIL_BULAN(CAST(A.BULAN AS INTEGER)) , ' Th. ' , A.TAHUN) KETERANGAN, CAST(A.BULAN AS INTEGER) BULAN, A.TIPE
			 FROM REKANAN_PAJAK A INNER JOIN
			   (SELECT REKANAN_ID, TIPE, MAX(TAHUN) TAHUN FROM REKANAN_PAJAK WHERE TIPE = '2' AND REKANAN_ID = ".$rekanan_id." GROUP BY REKANAN_ID, TIPE ) B
			   ON A.REKANAN_ID = B.REKANAN_ID AND A.TAHUN = B.TAHUN AND A.TIPE = B.TIPE AND A.REKANAN_ID = ".$rekanan_id."   AND A.NOMOR IS NOT NULL
			UNION ALL
			SELECT CONCAT('PPN ' , AMBIL_BULAN(CAST(A.BULAN AS INTEGER)) , ' Th. ' , A.TAHUN) KETERANGAN, CAST(A.BULAN AS INTEGER) BULAN, A.TIPE
			 FROM REKANAN_PAJAK A INNER JOIN
			   (SELECT REKANAN_ID, TIPE, MAX(TAHUN) TAHUN FROM REKANAN_PAJAK WHERE TIPE = '3' AND REKANAN_ID = ".$rekanan_id." GROUP BY REKANAN_ID, TIPE ) B
			   ON A.REKANAN_ID = B.REKANAN_ID AND A.TAHUN = B.TAHUN AND A.TIPE = B.TIPE AND A.REKANAN_ID = ".$rekanan_id."  AND A.NOMOR IS NOT NULL
			 ORDER BY TIPE ASC, BULAN ASC	
		"; 
		//TO_NUMBER(A.BULAN)
		$this->query = $str;
				
		return $this->selectLimit($str,-1,-1); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT 
				REKANAN_PAJAK_ID, 
				REKANAN_ID, 
				NOMOR, 
				TANGGAL, 
				BULAN, 
				TAHUN, 
				TIPE
			FROM REKANAN_PAJAK
			WHERE REKANAN_PAJAK_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_PAJAK_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array(), $statement="")
	{		
		$str = "SELECT COUNT(REKANAN_PAJAK_ID) AS ROWCOUNT FROM REKANAN_PAJAK WHERE REKANAN_PAJAK_ID IS NOT NULL ".$statement; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		//echo $str;
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }

    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(REKANAN_PAJAK_ID) AS ROWCOUNT FROM REKANAN_PAJAK WHERE REKANAN_PAJAK_ID IS NOT NULL "; 
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
	function getCountByParamsTahun($paramsArray=array())
	{		
		$str = "SELECT COUNT(REKANAN_PAJAK_ID) AS ROWCOUNT FROM REKANAN_PAJAK WHERE REKANAN_PAJAK_ID IS NOT NULL "; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= 'GROUP BY TAHUN';
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
	}
	
	function selectByParamsTahunSelect($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT 
				TAHUN
			FROM REKANAN_PAJAK
			WHERE REKANAN_PAJAK_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." GROUP BY TAHUN ORDER BY TAHUN DESC";
		//echo $str;		
		return $this->selectLimit($str,$limit,$from); 
    }
  }   	
?>