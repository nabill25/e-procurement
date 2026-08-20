<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class RekananRekeningKoran extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function RekananRekeningKoran()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_REKENING_KORAN_ID", $this->getNextId("REKANAN_REKENING_KORAN_ID","REKANAN_REKENING_KORAN")); 

		// $str = "INSERT INTO REKANAN_REKENING_KORAN (
		// 			REKANAN_REKENING_KORAN_ID, 
		// 			REKANAN_ID, 
		// 			NOMOR, 
		// 			NAMA, 
		// 			BULAN, 
		// 			TAHUN, 
		// 			NILAI,
		// 			MATA_UANG_ID,
		// 			BANK_ID,
		// 			PATH_FILE, TIPE, UKURAN, NAMA_FILE,CREATED_BY, CREATED_DATE)
		// 		VALUES ( '".$this->getField("REKANAN_REKENING_KORAN_ID")."', 
		// 			'".$this->getField("REKANAN_ID")."',
		// 			'".$this->getField("NOMOR")."', 
		// 			'".$this->getField("NAMA")."', 
		// 			'".$this->getField("BULAN")."', 
		// 			'".$this->getField("TAHUN")."',  
		// 			'".$this->getField("NILAI")."',
		// 			'".$this->getField("MATA_UANG_ID")."',
		// 			'".$this->getField("BANK_ID")."',
		// 			'".$this->getField("PATH_FILE")."', 
		// 			'".$this->getField("TIPE")."', 
		// 			'".$this->getField("UKURAN")."',
		// 			'".$this->getField("NAMA_FILE")."',
		// 			".$this->getField("CREATED_BY").",
		// 			CURRENT_TIMESTAMP
		// 			)
		// 	"; 

		$str = "INSERT INTO REKANAN_REKENING_KORAN (
					REKANAN_REKENING_KORAN_ID, 
					REKANAN_ID, 
					NOMOR, 
					NAMA, 
					BULAN, 
					TAHUN, 
					MATA_UANG_ID,
					BANK_ID,
					PATH_FILE, TIPE, UKURAN, NAMA_FILE,CREATED_BY, CREATED_DATE)
				VALUES ( '".$this->getField("REKANAN_REKENING_KORAN_ID")."', 
					'".$this->getField("REKANAN_ID")."',
					'".$this->getField("NOMOR")."', 
					'".$this->getField("NAMA")."', 
					'".$this->getField("BULAN")."', 
					'".$this->getField("TAHUN")."',   
					'".$this->getField("MATA_UANG_ID")."',
					'".$this->getField("BANK_ID")."',
					'".$this->getField("PATH_FILE")."', 
					'".$this->getField("TIPE")."', 
					'".$this->getField("UKURAN")."',
					'".$this->getField("NAMA_FILE")."',
					".$this->getField("CREATED_BY").",
					CURRENT_TIMESTAMP
					)
			"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		// $str = "UPDATE REKANAN_REKENING_KORAN 
		// 		SET
		// 			NOMOR = '".$this->getField("NOMOR")."',
		// 			NAMA = '".$this->getField("NAMA")."',
		// 			BULAN = '".$this->getField("BULAN")."',
		// 			TAHUN = '".$this->getField("TAHUN")."',
		// 			NILAI = '".$this->getField("NILAI")."',
		// 			MATA_UANG_ID = '".$this->getField("MATA_UANG_ID")."',
		// 			BANK_ID = '".$this->getField("BANK_ID")."',
		// 			PATH_FILE = '".$this->getField("PATH_FILE")."', 
		// 			TIPE = '".$this->getField("TIPE")."', 
		// 			UKURAN = '".$this->getField("UKURAN")."',
		// 			NAMA_FILE= '".$this->getField("NAMA_FILE")."',
		// 			UPDATED_BY = ".$this->getField("CREATED_BY").",
		// 			UPDATED_DATE = CURRENT_TIMESTAMP
		// 		WHERE REKANAN_REKENING_KORAN_ID = '".$this->getField("REKANAN_REKENING_KORAN_ID")."' 
		// 			  AND REKANAN_ID = '".$this->getField("REKANAN_ID")."'
		// 		"; 
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_REKENING_KORAN 
				SET
					NOMOR = '".$this->getField("NOMOR")."',
					NAMA = '".$this->getField("NAMA")."',
					BULAN = '".$this->getField("BULAN")."',
					TAHUN = '".$this->getField("TAHUN")."',
					MATA_UANG_ID = '".$this->getField("MATA_UANG_ID")."',
					BANK_ID = '".$this->getField("BANK_ID")."',
					PATH_FILE = '".$this->getField("PATH_FILE")."', 
					TIPE = '".$this->getField("TIPE")."', 
					UKURAN = '".$this->getField("UKURAN")."',
					NAMA_FILE= '".$this->getField("NAMA_FILE")."',
					UPDATED_BY = ".$this->getField("CREATED_BY").",
					UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE REKANAN_REKENING_KORAN_ID = '".$this->getField("REKANAN_REKENING_KORAN_ID")."' 
					  AND REKANAN_ID = '".$this->getField("REKANAN_ID")."'
				"; 
				$this->query = $str;
				
		return $this->execQuery($str);
    }

    function updateNilai()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_REKENING_KORAN 
				SET
					NILAI = '".$this->getField("NILAI")."',
					NILAI_SEBELUMNYA = '".$this->getField("NILAI_SEBELUMNYA")."',
					UPDATED_BY = '".$this->getField("UPDATED_BY")."',
					UPDATED_DATE = CURRENT_DATE
					WHERE REKANAN_REKENING_KORAN_ID = '".$this->getField("REKANAN_REKENING_KORAN_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
		
	function delete()
	{
        $str = "DELETE FROM REKANAN_REKENING_KORAN
                WHERE 
                  REKANAN_REKENING_KORAN_ID = '".$this->getField("REKANAN_REKENING_KORAN_ID")."' AND REKANAN_ID = '".$this->getField("REKANAN_ID")."' "; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }
	
	function deleteByRekananId()
	{
        $str = "DELETE FROM REKANAN_REKENING_KORAN
                WHERE 
                  REKANAN_ID = '".$this->getField("REKANAN_ID")."'"; 
				  
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
	function selectByParamsTahunSelect($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT 
				TAHUN
			FROM REKANAN_REKENING_KORAN
			WHERE REKANAN_REKENING_KORAN_ID IS NOT NULL		
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
	
	function selectByParamsTahun($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT 
				TAHUN, BULAN
			FROM REKANAN_REKENING_KORAN
			WHERE REKANAN_REKENING_KORAN_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." GROUP BY TAHUN, BULAN ORDER BY BULAN";
		//echo $str;		
		return $this->selectLimit($str,$limit,$from); 
    }
	
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT 
				REKANAN_REKENING_KORAN_ID, 
				CONCAT(BULAN, TAHUN) BULAN_TAHUN,
				LPAD(BULAN::VARCHAR, 2, '0') || TAHUN PERIODE,
				REKANAN_ID,
				K.NAMA, 
				NOMOR, 
				K.NAMA NAMA_BANK, 
				BULAN, 
				TAHUN, 
				NILAI, NAMA_FILE,
				K.MATA_UANG_ID,
                (SELECT X.KODE FROM MATA_UANG X WHERE K.MATA_UANG_ID = X.MATA_UANG_ID) MATAUANG,
                COALESCE((SELECT KURS FROM KURS X WHERE TO_NUMBER(BULAN_TAHUN, '99999') = TO_NUMBER(K.BULAN::VARCHAR || K.TAHUN::VARCHAR, '99999') AND X.MATA_UANG_ID = K.MATA_UANG_ID), 1) KURS,
                COALESCE((SELECT KURS FROM KURS X WHERE TO_NUMBER(BULAN_TAHUN, '99999') = TO_NUMBER(K.BULAN::VARCHAR || K.TAHUN::VARCHAR, '99999') AND X.MATA_UANG_ID = K.MATA_UANG_ID), 1) * NILAI NOMINAL,
                PATH_FILE, TIPE, UKURAN,
                COALESCE(NILAI_SEBELUMNYA, NILAI) NILAI_SEBELUMNYA,
				K.BANK_ID
            FROM REKANAN_REKENING_KORAN K
            WHERE 1=1
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		
		$str .= $statement." ORDER BY TAHUN, BULAN ASC";
		$this->query = $str;
		//echo $str;				
		return $this->selectLimit($str,$limit,$from); 
    }
	
    function selectByParamsKualifikasi($rekanan_id, $bulan)
	{
		/*(SELECT X.KODE FROM MATA_UANG X WHERE K.MATA_UANG_ID = X.MATA_UANG_ID) MATAUANG,
		COALESCE((SELECT KURS FROM KURS X WHERE TO_NUMBER(BULAN_TAHUN) = K.BULAN || K.TAHUN AND X.MATA_UANG_ID = K.MATA_UANG_ID), 1) KURS,
		COALESCE((SELECT KURS FROM KURS X WHERE TO_NUMBER(BULAN_TAHUN) = K.BULAN || K.TAHUN AND X.MATA_UANG_ID = K.MATA_UANG_ID), 1) * NILAI NOMINAL,
		
		*/
		$str = "
			SELECT BULAN, TAHUN, NAMA, NOMOR, MATA_UANG, NILAI, KURS,
			NILAI * KURS NOMINAL FROM ( 
			SELECT BULAN, TAHUN, A.NAMA, NOMOR, B.KODE MATA_UANG, NILAI, 
			   COALESCE((SELECT KURS FROM KURS X WHERE TO_NUMBER(BULAN_TAHUN) = A.BULAN || A.TAHUN AND B.MATA_UANG_ID = A.MATA_UANG_ID AND ROWNUM = 1), 1
			) KURS
			FROM REKANAN_REKENING_KORAN A 
				 LEFT JOIN MATA_UANG B ON B.MATA_UANG_ID = A.MATA_UANG_ID
			WHERE REKANAN_ID = ".$rekanan_id." AND BULAN || TAHUN IN (".$bulan.") ORDER BY TAHUN ASC, BULAN
			) A 	
		"; 
				
		$this->query = $str;
		$str .= $statement." ORDER BY TAHUN ASC, BULAN ";
	
		return $this->selectLimit($str,-1,-1); 
    }	
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT 
				REKANAN_REKENING_KORAN_ID, 
				REKANAN_ID, 
				NOMOR, 
				NAMA, 
				BULAN, 
				TAHUN, 
				NILAI
			FROM REKANAN_REKENING_KORAN
			WHERE REKANAN_REKENING_KORAN_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_REKENING_KORAN_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
	function getCountByParamsTahun($paramsArray=array())
	{		
		$str = "SELECT COUNT(REKANAN_REKENING_KORAN_ID) AS ROWCOUNT FROM REKANAN_REKENING_KORAN WHERE REKANAN_REKENING_KORAN_ID IS NOT NULL "; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= 'GROUP BY TAHUN';
		$this->select($str); 
		$this->query = $str;
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }
    function getCountByParams($paramsArray=array())
	{		
		$str = "SELECT COUNT(REKANAN_REKENING_KORAN_ID) AS ROWCOUNT FROM REKANAN_REKENING_KORAN WHERE REKANAN_REKENING_KORAN_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(REKANAN_REKENING_KORAN_ID) AS ROWCOUNT FROM REKANAN_REKENING_KORAN WHERE REKANAN_REKENING_KORAN_ID IS NOT NULL "; 
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