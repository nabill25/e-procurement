<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class Panel extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function Panel()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PANEL_ID", $this->getNextId("PANEL_ID","PANEL")); 

		$str = "
		INSERT INTO PANEL (
		   PANEL_ID, USER_LOGIN_ID, REKANAN_KUALIFIKASI_ID, 
		   NAMA, URAIAN, 
		   UNIT_KERJA_ID) 
		VALUES ('".$this->getField("PANEL_ID")."', '".$this->getField("USER_LOGIN_ID")."', '".$this->getField("REKANAN_KUALIFIKASI_ID")."', 
		   '".$this->getField("NAMA")."', '".$this->getField("URAIAN")."',
		   '".$this->getField("UNIT_KERJA_ID")."')
   "; 
				
		$this->query = $str;
		$this->id = $this->getField("PANEL_ID");
		return $this->execQuery($str);
    }


    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "		
				 UPDATE  PANEL
				SET    
					   NAMA        = '".$this->getField("NAMA")."',
					   URAIAN  = '".$this->getField("URAIAN")."',
					   REKANAN_KUALIFIKASI_ID      = '".$this->getField("REKANAN_KUALIFIKASI_ID")."'
				WHERE  PANEL_ID   =  ".$this->getField("PANEL_ID")."
			  
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }

	function update_set_null_syarat()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PANEL SET
				  SYARAT_TEKNIS_TENAGA_AHLI = NULL,
				  SYARAT_TEKNIS_PERALATAN = NULL,
				  SYARAT_TEKNIS_SERTIFIKAT = NULL,
				  SYARAT_REKENING_KORAN = NULL,
				  SYARAT_REKENING_KORAN_BULAN = NULL,
				  SYARAT_KEUANGAN_SPT = NULL,
				  SYARAT_KEUANGAN_PPN = NULL,
				  SYARAT_KEUANGAN_PPH = NULL,				  
				  SYARAT_KEUANGAN_PPH_BULAN = NULL,
				  SYARAT_KEUANGAN_PPN_BULAN = NULL,
				  SYARAT_TEKNIS_SERTIFIKAT_INFO = NULL,
				  SYARAT_KEUANGAN_PKP = NULL,
				  SYARAT_ADM_KUALIFIKASI = NULL,
				  SYARAT_IJIN_SIUJK = NULL,
				  SYARAT_IJIN_SIUI = NULL,
				  SYARAT_IJIN_LAIN = NULL,
				  SYARAT_ADM_KUALIFIKASI_INFO = NULL,
				  SYARAT_NERACA = NULL,
				  SYARAT_SBU = NULL,
				  SYARAT_PENGALAMAN_TAHUN = NULL
				WHERE PANEL_ID = ".$this->getField("PANEL_ID")."
				"; 
				$this->query = $str;
		//echo $str;
		return $this->execQuery($str);
    }
	
	function update_dyna()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PANEL A SET
				  ".$this->getField("FIELD")." = ".$this->getField("FIELD_VALUE")."
				WHERE PANEL_ID = ".$this->getField("PANEL_ID")."
				"; 
				$this->query = $str;
		//echo $str;
		return $this->execQuery($str);
    }

    function updateByField()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PANEL A SET
				  ".$this->getField("FIELD")." = ".$this->getField("FIELD_VALUE")."
				WHERE PANEL_ID = ".$this->getField("PANEL_ID")."
				"; 
				$this->query = $str;
	
		return $this->execQuery($str);
    }
			
	function delete()
	{
        $str = "DELETE FROM PANEL
                WHERE 
                  PANEL_ID = ".$this->getField("PANEL_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","PANEL_METODE_EVALUASI_ID"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
					SELECT 
					PANEL_ID, USER_LOGIN_ID, REKANAN_KUALIFIKASI_ID, 
					   NAMA, URAIAN, SYARAT, 
					   TANGGAL, PUBLISH_PANEL, PUBLISH_PANEL_TANGGAL, 
					   PUBLISH_PEMENANG, PUBLISH_PEMENANG_TANGGAL, PASS_GRADE, 
					   UNIT_KERJA_ID, ALASAN, SYARAT_TEKNIS_TENAGA_AHLI, 
					   SYARAT_TEKNIS_PERALATAN, SYARAT_TEKNIS_SERTIFIKAT, SYARAT_REKENING_KORAN, 
					   SYARAT_KEUANGAN_SPT, SYARAT_KEUANGAN_PPN, SYARAT_KEUANGAN_PPH, 
					   SYARAT_REKENING_KORAN_BULAN, SYARAT_KEUANGAN_PPN_BULAN, SYARAT_KEUANGAN_PPH_BULAN, 
					   SYARAT_TEKNIS_SERTIFIKAT_INFO, SYARAT_KEUANGAN_PKP, SYARAT_ADM_KUALIFIKASI, 
					   SYARAT_IJIN_SIUJK, SYARAT_IJIN_SIUI, SYARAT_IJIN_LAIN, 
					   SYARAT_ADM_KUALIFIKASI_INFO, SYARAT_NERACA, SYARAT_SBU
					FROM PANEL A
					WHERE 1 = 1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
			
		$str .= $statement." ORDER BY TANGGAL DESC";
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectById($panel_id)
	{
		$str = "
                        SELECT 
                           A.SYARAT_TEKNIS_TENAGA_AHLI, A.SYARAT_TEKNIS_PERALATAN, 
                           A.SYARAT_TEKNIS_SERTIFIKAT, A.SYARAT_REKENING_KORAN_BULAN, A.SYARAT_REKENING_KORAN, A.SYARAT_KEUANGAN_SPT, 
                           A.SYARAT_KEUANGAN_PPN, A.SYARAT_KEUANGAN_PPH,
                           A.SYARAT_KEUANGAN_PPN_BULAN, A.SYARAT_KEUANGAN_PPH_BULAN,
                           A.SYARAT_TEKNIS_SERTIFIKAT_INFO,
                           A.SYARAT_KEUANGAN_PKP, A.SYARAT_ADM_KUALIFIKASI,
                           A.SYARAT_NERACA, A.SYARAT_SBU,
                           A.SYARAT_IJIN_SIUJK, A.SYARAT_IJIN_SIUI, A.SYARAT_IJIN_LAIN, A.SYARAT_ADM_KUALIFIKASI_INFO,
                           A.PANEL_ID, A.NAMA, A.REKANAN_KUALIFIKASI_ID,  
                           G.NAMA REKANAN_KUALIFIKASI, A.TANGGAL, A.PASS_GRADE, 
                           COALESCE((SELECT TANGGAL_AWAL FROM PANEL_TAHAP WHERE PANEL_ID = A.PANEL_ID AND UPPER(NAMA) = 'PEMBUATAN PANEL' AND ROWNUM =1 ), A.TANGGAL) TANGGAL_TAHAP,
                           A.USER_LOGIN_ID,
                           H.NAMA UNIT_KERJA,
						   A.SYARAT_PENGALAMAN_TAHUN
                            FROM    PANEL A
                            LEFT JOIN USER_LOGIN F ON A.USER_LOGIN_ID = F.USER_LOGIN_ID
                            LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
                            LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
                        WHERE A.PANEL_ID = ".$panel_id."
	  "; 
					
		$this->query = $str;
		return $this->select($str);  
    }

    function selectByIdEncrypt($panel_id, $rekanan_id)
	{
		$str = "
                        SELECT 
                           A.SYARAT_TEKNIS_TENAGA_AHLI, A.SYARAT_TEKNIS_PERALATAN, 
                           A.SYARAT_TEKNIS_SERTIFIKAT, A.SYARAT_REKENING_KORAN_BULAN, A.SYARAT_REKENING_KORAN, A.SYARAT_KEUANGAN_SPT, 
                           A.SYARAT_KEUANGAN_PPN, A.SYARAT_KEUANGAN_PPH,
                           A.SYARAT_KEUANGAN_PPN_BULAN, A.SYARAT_KEUANGAN_PPH_BULAN,
                           A.SYARAT_TEKNIS_SERTIFIKAT_INFO,
                           A.SYARAT_KEUANGAN_PKP, A.SYARAT_ADM_KUALIFIKASI,
                           A.SYARAT_NERACA, A.SYARAT_SBU,
                           A.SYARAT_IJIN_SIUJK, A.SYARAT_IJIN_SIUI, A.SYARAT_IJIN_LAIN, A.SYARAT_ADM_KUALIFIKASI_INFO,
                           A.PANEL_ID, A.NAMA, A.REKANAN_KUALIFIKASI_ID,  
                           G.NAMA REKANAN_KUALIFIKASI, A.TANGGAL, A.PASS_GRADE, 
                           COALESCE((SELECT TANGGAL_AWAL FROM PANEL_TAHAP WHERE PANEL_ID = A.PANEL_ID AND UPPER(NAMA) = 'PEMBUATAN PANEL' AND ROWNUM =1 ), A.TANGGAL) TANGGAL_TAHAP,
                           A.USER_LOGIN_ID,
                           H.NAMA UNIT_KERJA
                            FROM    PANEL A
                            LEFT JOIN USER_LOGIN F ON A.USER_LOGIN_ID = F.USER_LOGIN_ID
                            LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
                            LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
                        WHERE MD5(A.PANEL_ID || '".$rekanan_id."') = '".$panel_id."'
	  "; 
		$this->query = $str;
		return $this->select($str);  
    }
	
    function selectByParamsMonitoring($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
        $str = "SELECT * FROM (
					SELECT 
                       A.PANEL_ID, A.REKANAN_KUALIFIKASI_ID,                  
                       F.NAMA USER_LOGIN, 
                       G.NAMA REKANAN_KUALIFIKASI, A.NAMA, A.URAIAN, 
                       A.SYARAT, 
                       A.TANGGAL, A.PUBLISH_PANEL, A.PUBLISH_PANEL_TANGGAL, 
                       A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL,  
                       A.PASS_GRADE, H.UNIT_KERJA_ID ,H.NAMA UNIT_KERJA, A.USER_LOGIN_ID, A.ALASAN, 
                       COALESCE((SELECT TANGGAL_AWAL FROM PANEL_TAHAP WHERE PANEL_ID = A.PANEL_ID AND UPPER(NAMA) = 'PEMBUATAN PANEL' AND ROWNUM =1 ), A.TANGGAL) TANGGAL_TAHAP
                    FROM    PANEL A
                            LEFT JOIN V_OAUTH_USER F ON TO_CHAR(A.USER_LOGIN_ID) = F.NIPP 
                            LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
                            LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID                                                                                  
                    ) A WHERE 1 = 1
	  "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ORDER BY TANGGAL_TAHAP DESC, PANEL_ID DESC ";

		$this->query = $str;
		$rs = $this->selectLimit($str,$limit,$from);
		return  $rs;
    }
	   
    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(A.PANEL_ID) AS ROWCOUNT 
					FROM    PANEL A
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

    function getPanelMengikuti($rekanan_id, $panel_id)
	{
		$str = "SELECT 1 ROWCOUNT
				  FROM PANEL_REKANAN A
				WHERE REKANAN_ID = '".$rekanan_id."' AND PANEL_ID = '".$panel_id."' AND A.TANGGAL_DAFTAR IS NOT NULL"; 
	
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }
	
    function getPanelPendaftaran($panel_id)
	{
		// kadang bisa ini DD/MM/YYYY HH24:MI:SS
		// kadang bisa ini yyyy/mm/dd hh:mi:ss		
		//$str = "SELECT 1 ROWCOUNT FROM PANEL_TAHAP WHERE (TO_DATE(CURRENT_DATE, 'yyyy/mm/dd hh:mi:ss') BETWEEN TO_DATE(TANGGAL_AWAL, 'yyyy/mm/dd hh:mi:ss') AND TO_DATE(TANGGAL_AKHIR, 'yyyy/mm/dd hh:mi:ss') OR TO_DATE(TANGGAL_AWAL, 'yyyy/mm/dd hh:mi:ss') = TO_DATE(CURRENT_DATE, 'yyyy/mm/dd hh:mi:ss'))AND URUT = 3 AND PANEL_ID = '".$panel_id."' "; 
		$str = "SELECT 1 ROWCOUNT FROM PANEL_TAHAP WHERE (CURRENT_DATE BETWEEN TANGGAL_AWAL AND TANGGAL_AKHIR OR 
				TO_DATE(TANGGAL_AWAL, 'yyyy/mm/dd hh:mi:ss') = TO_DATE(CURRENT_DATE, 'yyyy/mm/dd hh:mi:ss')) AND URUT = 3 AND PANEL_ID = '".$panel_id."' "; 
	
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
		{
			echo $this->errorMsg;
			return 0; 
		}
    }		
  } 
?>