<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketProgres extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PaketProgres()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_PROGRES_ID", $this->getNextId("PAKET_PROGRES_ID","PAKET_PROGRES")); 

		$str = "
		
			INSERT INTO PAKET_PROGRES (
			   PAKET_PROGRES_ID, PAKET_ID, USER_LOGIN_ID, 
			   PAKET_PROGRES_TEMPLATE_ID, NAMA, KETERANGAN, TANGGAL_AWAL_REN, TANGGAL_AKHIR_REN, URUT, LAST_CREATE_USER, LAST_CREATE_DATE)
  			 	VALUES (
				  '".$this->getField("PAKET_PROGRES_ID")."',
  				  '".$this->getField("PAKET_ID")."',
   				  '".$this->getField("USER_LOGIN_ID")."',
				  '".$this->getField("PAKET_PROGRES_TEMPLATE_ID")."',
				  '".$this->getField("NAMA")."',
  				  '".$this->getField("KETERANGAN")."',
  				  ".$this->getField("TANGGAL_AWAL_REN").",
  				  ".$this->getField("TANGGAL_AKHIR_REN").",
				  '".$this->getField("URUT")."',
				  '".$this->getField("LAST_CREATE_USER")."',
				  ".$this->getField("LAST_CREATE_DATE")."
				)"; 
				
		$this->query = $str;
		$this->id = $this->getField("PAKET_PROGRES_ID");
		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM PAKET_PROGRES
                WHERE 
                  PAKET_ID = ".$this->getField("PAKET_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

	function deleteDetil()
	{
        $str = "DELETE FROM PAKET_PROGRES
                WHERE 
                  PAKET_PROGRES_ID= ".$this->getField("PAKET_PROGRES_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }
	
    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","PAKET_PROGRES_METODE_EVALUASI_ID"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $sOrder="ORDER BY A.PAKET_ID ASC, COALESCE(A.URUT, C.URUT) ASC")
	{
		$str = "
					SELECT 
                    A.PAKET_PROGRES_ID, PAKET_ID, A.USER_LOGIN_ID, COALESCE(A.URUT, C.URUT) URUT,
                       A.PAKET_PROGRES_TEMPLATE_ID, A.NAMA, A.KETERANGAN, A.TANGGAL_AWAL_REN, A.TANGGAL_AKHIR_REN, A.TANGGAL_AWAL_REAL, A.TANGGAL_AKHIR_REAL, B.NAMA USER_NAMA, D.USER_TYPE_ID, 
                       CASE WHEN D.USER_TYPE_ID = 3 THEN 'pbj' WHEN D.USER_TYPE_ID = 9 THEN 'fungsional' WHEN D.USER_TYPE_ID = 10 THEN  'hukum' END TIPE_ICON, C.KETENTUAN
                    FROM PAKET_PROGRES A 
                    LEFT JOIN V_OAUTH_USER B ON TO_CHAR(A.USER_LOGIN_ID) = B.NIPP
					LEFT JOIN PAKET_PROGRES_TEMPLATE C ON A.PAKET_PROGRES_TEMPLATE_ID = C.PAKET_PROGRES_TEMPLATE_ID
                    LEFT JOIN PAKET_PROGRES_TEMPLATE D ON A.PAKET_PROGRES_TEMPLATE_ID = D.PAKET_PROGRES_TEMPLATE_ID
                    WHERE PAKET_PROGRES_ID IS NOT NULL  "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
			
		$str .= $statement." ".$sOrder;
		$this->query = $str;
		
		return $this->selectLimit($str,$limit,$from); 
    }
	
	function selectByParamsJumlahDetil($paramsArray=array(),$limit=-1,$from=-1, $statement='', $sOrder="")
	{
		$str = "
				SELECT COUNT(1) JUMLAH_PROGRES_DETIL, A.PAKET_ID
				FROM PAKET_PROGRES A
				INNER JOIN PAKET_PROGRES_DETIL B ON A.PAKET_PROGRES_ID = B.PAKET_PROGRES_ID
				WHERE 1=1
				"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
			
		$str .= $statement." GROUP BY A.PAKET_ID".$sOrder;
		$this->query = $str;
		
		return $this->selectLimit($str,$limit,$from); 
    }
	
    function selectByParamsTimeline($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=' ORDER BY A.PAKET_PROGRES_ID ASC ')
	{
		$str = "
					SELECT 
                    PAKET_PROGRES_ID, PAKET_ID, A.USER_LOGIN_ID, 
                       A.PAKET_PROGRES_TEMPLATE_ID, A.NAMA, A.KETERANGAN, A.TANGGAL_AWAL_REN, A.TANGGAL_AKHIR_REN, A.TANGGAL_AWAL_REAL, A.TANGGAL_AKHIR_REAL, UPPER(B.NAMA) USER_NAMA, D.USER_TYPE_ID, 
                       CASE WHEN D.USER_TYPE_ID = 3 THEN 'pbj' WHEN D.USER_TYPE_ID = 9 THEN 'fungsional' WHEN D.USER_TYPE_ID = 10 THEN  'hukum' END TIPE_ICON, CASE WHEN (SELECT COUNT(1) FROM PAKET_PROGRES_DETIL X WHERE A.PAKET_PROGRES_ID = X.PAKET_PROGRES_ID) = 0  THEN 'belumlapor' ELSE 'sudahlapor' END TIPE_WARNA,
                       (SELECT COUNT(1) FROM PAKET_PROGRES_DETIL X WHERE X.PAKET_PROGRES_ID = A.PAKET_PROGRES_ID) JUMLAH_FEEDBACK,
                       (SELECT COUNT(1) FROM PAKET_PROGRES_KOMEN X WHERE X.PAKET_PROGRES_ID = A.PAKET_PROGRES_ID) JUMLAH_KOMEN,
                       CASE WHEN A.TANGGAL_AKHIR_REN IS NULL THEN TO_CHAR(A.TANGGAL_AWAL_REN, 'DD/MM/YYYY') ELSE TO_CHAR(A.TANGGAL_AWAL_REN, 'DD/MM/YYYY') || ' s/d ' || TO_CHAR(A.TANGGAL_AKHIR_REN, 'DD/MM/YYYY') END TANGGAL_TIMELINE
                    FROM PAKET_PROGRES A 
                    LEFT JOIN V_OAUTH_USER B ON TO_CHAR(A.USER_LOGIN_ID) = B.NIPP
                    LEFT JOIN PAKET_PROGRES_TEMPLATE D ON A.PAKET_PROGRES_TEMPLATE_ID = D.PAKET_PROGRES_TEMPLATE_ID
                    WHERE A.PAKET_PROGRES_ID IS NOT NULL  "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
			
		$str .= $statement." ".$order;
				
		return $this->selectLimit($str,$limit,$from); 
    }
	 
	function selectByParamsWorkOrder($paramsArray=array(),$limit=-1,$from=-1, $userId="", $statement='', $sOrder="ORDER BY A.PAKET_ID ASC, COALESCE(A.URUT, F.URUT) ASC")
	{
		$str = "
				SELECT 
                D.NAMA PAKET, A.PAKET_PROGRES_ID, A.PAKET_ID,
				COALESCE(A.URUT, F.URUT) URUT,
				CASE WHEN A.USER_LOGIN_ID = '".$userId."' AND E.USER_LOGIN_ID IS NOT NULL
			    THEN '1'
			    ELSE '0'
			    END STATUS_ENTRI,
                A.USER_LOGIN_ID, B.NAMA USER_LOGIN,
                A.NAMA, A.KETERANGAN, A.TANGGAL_AWAL_REN, A.TANGGAL_AKHIR_REN, 
                COALESCE(STATUS,0) STATUS, CASE WHEN COALESCE(STATUS,0) > 0 THEN 'Progres' ELSE 'Belum Progres' END STATUS_INFO,
                CASE WHEN A.TANGGAL_AWAL_REN = A.TANGGAL_AKHIR_REN OR A.TANGGAL_AKHIR_REN IS NULL THEN 
                    TO_CHAR(A.TANGGAL_AWAL_REN, 'DD-MM-YYYY') 
                ELSE TO_CHAR(A.TANGGAL_AWAL_REN, 'DD-MM-YYYY') || ' s/d ' || TO_CHAR(A.TANGGAL_AKHIR_REN, 'DD-MM-YYYY') END TANGGAL_TIMELINE
                FROM PAKET_PROGRES A
                LEFT JOIN V_OAUTH_USER B ON TO_CHAR(A.USER_LOGIN_ID) = B.NIPP
                LEFT JOIN
                (
                SELECT CASE WHEN PAKET_PROGRES_TEMPLATE_ID = 6 THEN CASE WHEN MAX(PROSENTASE) = 100 THEN 1 ELSE 0 END ELSE 1 END STATUS, X.PAKET_PROGRES_ID
                FROM PAKET_PROGRES_DETIL X INNER JOIN PAKET_PROGRES Y ON X.PAKET_PROGRES_ID = Y.PAKET_PROGRES_ID
                GROUP BY X.PAKET_PROGRES_ID, PAKET_PROGRES_TEMPLATE_ID
                ) C ON C.PAKET_PROGRES_ID = A.PAKET_PROGRES_ID
                LEFT JOIN PAKET D ON A.PAKET_ID = D.PAKET_ID
				LEFT JOIN PAKET_PROGRES_AWAL E ON E.PAKET_PROGRES_ID = A.PAKET_PROGRES_ID AND E.USER_LOGIN_ID = ".$userId."
				LEFT JOIN PAKET_PROGRES_TEMPLATE F ON A.PAKET_PROGRES_TEMPLATE_ID = F.PAKET_PROGRES_TEMPLATE_ID
                WHERE 1 = 1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
			
		$str .= $statement." ".$sOrder;
		$this->query = $str;
				
		return $this->selectLimit($str,$limit,$from); 
    }
	
	function getCountByParamsWorkOrder($paramsArray=array(), $pegawaiId="", $statement='')
	{
		$str = "SELECT COUNT(1) AS ROWCOUNT 
				FROM PAKET_PROGRES A
                LEFT JOIN V_OAUTH_USER B ON TO_CHAR(A.USER_LOGIN_ID) = B.NIPP
                LEFT JOIN
                (
                SELECT 1 STATUS, X.PAKET_PROGRES_ID
                FROM PAKET_PROGRES_DETIL X
                GROUP BY X.PAKET_PROGRES_ID
                ) C ON C.PAKET_PROGRES_ID = A.PAKET_PROGRES_ID
                LEFT JOIN PAKET D ON A.PAKET_ID = D.PAKET_ID
				LEFT JOIN PAKET_PROGRES_AWAL E ON E.PAKET_PROGRES_ID = A.PAKET_PROGRES_ID AND E.USER_LOGIN_ID = ".$pegawaiId."
                WHERE 1 = 1 ".$statement; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }
	  
    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(A.PAKET_PROGRES_ID) AS ROWCOUNT 
					FROM    PAKET_PROGRES A
					WHERE 1 = 1".$statement; 
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

  } 
?>