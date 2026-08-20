<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PanelRekanan extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PanelRekanan()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PANEL_REKANAN_ID", $this->getNextId("PANEL_REKANAN_ID","PANEL_REKANAN")); 

		$str = "
				INSERT INTO PANEL_REKANAN (
				   PANEL_REKANAN_ID, PANEL_ID, REKANAN_ID, 
				   TANGGAL_DAFTAR,
				   KODE_REKANAN,
				   NILAI_PENGALAMAN, NILAI_PERSONIL, NILAI_PERALATAN, 
				   NILAI_KEUANGAN, NILAI_SERTIFIKAT_LAIN, LULUS_KUALIFIKASI, 
				   DI_EMAIL) 
				VALUES (".$this->getField("PANEL_REKANAN_ID").", ".$this->getField("PANEL_ID").", ".$this->getField("REKANAN_ID").", 
				   '".$this->getField("TANGGAL_DAFTAR")."', 
				   '".$this->getField("KODE_REKANAN")."',
				   ".$this->getField("NILAI_PENGALAMAN").", ".$this->getField("NILAI_PERSONIL").", ".$this->getField("NILAI_PERALATAN").", 
				   ".$this->getField("NILAI_KEUANGAN").", ".$this->getField("NILAI_SERTIFIKAT_LAIN").", ".$this->getField("LULUS_KUALIFIKASI").", 
				   ".$this->getField("DI_EMAIL").")		
			"; 
				
		$this->query = $str;
	
		return $this->execQuery($str);
    }

	function insertUndang()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PANEL_REKANAN_ID", $this->getNextId("PANEL_REKANAN_ID","PANEL_REKANAN")); 

		$str = "
				INSERT INTO PANEL_REKANAN (
				   PANEL_REKANAN_ID, PANEL_ID, REKANAN_ID, 
				   TANGGAL_UNDANG, DI_EMAIL) 
				VALUES (".$this->getField("PANEL_REKANAN_ID").", ".$this->getField("PANEL_ID").", ".$this->getField("REKANAN_ID").", 
				   CURRENT_DATE, ".$this->getField("DI_EMAIL").")		
			"; 
				
		$this->query = $str;
	
		return $this->execQuery($str);
    }	

	function insertDaftar()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PANEL_REKANAN_ID", $this->getNextId("PANEL_REKANAN_ID","PANEL_REKANAN")); 

		$str = "
				INSERT INTO PANEL_REKANAN (
				   PANEL_REKANAN_ID, PANEL_ID, REKANAN_ID, 
				   TANGGAL_DAFTAR, DI_EMAIL, KODE_REKANAN) 
				VALUES (".$this->getField("PANEL_REKANAN_ID").", ".$this->getField("PANEL_ID").", ".$this->getField("REKANAN_ID").", 
				   CURRENT_DATE, 1, '".$this->getField("KODE_REKANAN")."')		
			"; 
				
		$this->query = $str;
		$this->id = $this->getField("PANEL_REKANAN_ID");
		return $this->execQuery($str);
    }	

    function callProsesPeringkatPanel()
	{
		$str = " CALL PROSES_PERINGKAT_PANEL('".$this->getField("PANEL_ID")."', '".$this->getField("PANEL_BIDANG_USAHA_ID")."')
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }


    function updateDaftar()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PANEL_REKANAN  SET
					TANGGAL_DAFTAR = CURRENT_DATE, KODE_REKANAN = '".$this->getField("KODE_REKANAN")."'
				WHERE PANEL_ID = ".$this->getField("PANEL_ID")." AND REKANAN_ID = ".$this->getField("REKANAN_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }


    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PANEL_REKANAN A SET
				  ".$this->getField("FIELD")." = ".$this->getField("FIELD_VALUE")."
				WHERE PANEL_REKANAN_ID = ".$this->getField("PANEL_REKANAN_ID")."
				"; 
				$this->query = $str;
		//echo $str;
		return $this->execQuery($str);
    }
	
	function updateTwoField()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PANEL_REKANAN A SET
				  ".$this->getField("FIELD1")." = ".$this->getField("FIELD1_VALUE").",
				  ".$this->getField("FIELD2")." = '".$this->getField("FIELD2_VALUE")."'
				WHERE PANEL_REKANAN_ID = ".$this->getField("PANEL_REKANAN_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PANEL_REKANAN
                WHERE 
                  PANEL_REKANAN_ID = ".$this->getField("PANEL_REKANAN_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }
	
	function deleteByPaket()
	{
        $str = "DELETE FROM PANEL_REKANAN
                WHERE 
                  PANEL_ID = ".$this->getField("PANEL_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }
	
	function deleteByPaketNew()
	{
        $str = "DELETE FROM PANEL_REKANAN
                WHERE 
                  PANEL_ID = ".$this->getField("PANEL_ID")." AND REKANAN_ID NOT IN (".$this->getField("REKANAN_ID").")
				"; 
				  
		$this->query = $str;
		//echo $str;
        return $this->execQuery($str);
    }	

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
	
	

    function selectByParamsEmail($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = " SELECT PANEL_REKANAN_ID, EMAIL, LULUS_KUALIFIKASI, LULUS_KUALIFIKASI_KETERANGAN, NAMA, WHATSAPP FROM PANEL_REKANAN A, REKANAN B WHERE PANEL_REKANAN_ID IS NOT NULL AND A.REKANAN_ID = B.REKANAN_ID "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PANEL_REKANAN_ID ASC";
			
		return $this->selectLimit($str,$limit,$from); 
    }

	
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PANEL_REKANAN_ID, PANEL_ID, A.REKANAN_ID, B.NAMA REKANAN, 
               (SELECT X.NAMA FROM REKANAN_TIPE X WHERE X.REKANAN_TIPE_ID = B.REKANAN_TIPE_ID)|| '. ' || B.NAMA FULL_NAMA_REKANAN,
               NAMA NAMA_PEKERJAAN,
                   A.TANGGAL_DAFTAR, 
                   KODE_REKANAN,   
                   NILAI_PENGALAMAN, NILAI_PERSONIL, NILAI_PERALATAN, 
                   NILAI_KEUANGAN, NILAI_SERTIFIKAT_LAIN, LULUS_KUALIFIKASI,                                         
                   DI_EMAIL, B.ALAMAT, B.EMAIL, B.NPWP, LULUS_KUALIFIKASI_KETERANGAN, COALESCE(LULUS_PENDAFTARAN, 2) LULUS_PENDAFTARAN, LULUS_PENDAFTARAN_KETERANGAN,
                   B.WHATSAPP,
                   COALESCE((SELECT COUNT(1) FROM REKANAN_PENGALAMAN X WHERE X.REKANAN_ID = A.REKANAN_ID), 0) JUMLAH_PENGALAMAN,
                   COALESCE((SELECT COUNT(1) FROM REKANAN_REKENING_KORAN X WHERE X.REKANAN_ID = A.REKANAN_ID AND BULAN || TAHUN IN (SELECT REGEXP_SUBSTR(REPLACE(C.SYARAT_REKENING_KORAN_BULAN, ' ',  ''),'[^,]+', 1, LEVEL) FROM DUAL CONNECT BY REGEXP_SUBSTR(REPLACE(C.SYARAT_REKENING_KORAN_BULAN, ' ',  ''), '[^,]+', 1, LEVEL) IS NOT NULL)), 0) JUMLAH_REKENING_KORAN
                FROM PANEL_REKANAN A INNER JOIN REKANAN B ON A.REKANAN_ID = B.REKANAN_ID
                INNER JOIN PANEL C ON A.PANEL_ID = C.PANEL_ID 
				WHERE 1 = 1	 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ORDER BY A.PANEL_REKANAN_ID ASC";
	
		$this->query = $str;	
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsMonitoring($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT P.PERINGKAT, P.NILAI_PANEL, A.PANEL_REKANAN_ID, A.PANEL_ID, A.REKANAN_ID, B.NAMA REKANAN, 
               (SELECT X.NAMA FROM REKANAN_TIPE X WHERE X.REKANAN_TIPE_ID = B.REKANAN_TIPE_ID)|| '. ' || B.NAMA FULL_NAMA_REKANAN,
               NAMA NAMA_PEKERJAAN,
                   A.TANGGAL_DAFTAR, 
                   KODE_REKANAN,   
                   NILAI_PERSONIL, NILAI_PERALATAN, 
                   COALESCE(NILAI_KEUANGAN, 0) NILAI_KEUANGAN, NILAI_KEUANGAN NILAI_KEUANGAN_REAL, NILAI_SERTIFIKAT_LAIN, LULUS_KUALIFIKASI,                                         
                   DI_EMAIL, B.ALAMAT, B.EMAIL, B.NPWP, LULUS_KUALIFIKASI_KETERANGAN, COALESCE(LULUS_PENDAFTARAN, 2) LULUS_PENDAFTARAN, LULUS_PENDAFTARAN_KETERANGAN,
                   B.WHATSAPP, COALESCE(D.JUMLAH, 0) || '/' || (SELECT COUNT(1) FROM REKANAN_PENGALAMAN X WHERE TO_CHAR(X.KONTRAK_TANGGAL, 'YYYY') >= C.SYARAT_PENGALAMAN_TAHUN AND X.REKANAN_ID = B.REKANAN_ID) JUMLAH_PENGALAMAN, 
                   COALESCE(D.TOTAL, 0) NILAI_PENGALAMAN, CASE WHEN COALESCE(E.TOTAL, 0) = 0 THEN 0 ELSE ROUND((COALESCE(D.TOTAL, 0) / E.TOTAL) * 100, 2) END RATA_PENGALAMAN,
                   ROUND((F.TOTAL / 6), 2) JUMLAH_REK_KORAN,
                   CASE WHEN COALESCE(G.TOTAL, 0) = 0 THEN 0 ELSE ROUND(( ROUND((F.TOTAL / 6), 2) / ROUND((G.TOTAL / 6), 2)) * 100, 2) END RATA_REK_KORAN                    
                FROM PANEL_REKANAN A 
                INNER JOIN REKANAN B ON A.REKANAN_ID = B.REKANAN_ID
                INNER JOIN PANEL C ON A.PANEL_ID = C.PANEL_ID
                INNER JOIN PANEL_REKANAN_BIDANG_USAHA P ON A.PANEL_REKANAN_ID = P.PANEL_REKANAN_ID
                LEFT JOIN BIDANG_USAHA_PANEL_NILAI D ON A.PANEL_ID = D.PANEL_ID  AND A.REKANAN_ID = D.REKANAN_ID AND D.PANEL_BIDANG_USAHA_ID = P.PANEL_BIDANG_USAHA_ID 
                LEFT JOIN BIDANG_USAHA_PANEL_NILAI_MAX E ON A.PANEL_ID = E.PANEL_ID AND E.PANEL_BIDANG_USAHA_ID = P.PANEL_BIDANG_USAHA_ID
                LEFT JOIN REK_KORAN_PANEL_NILAI F ON A.PANEL_ID = F.PANEL_ID  AND A.REKANAN_ID = F.REKANAN_ID
                LEFT JOIN REK_KORAN_PANEL_NILAI_MAX G ON A.PANEL_ID = G.PANEL_ID AND G.PANEL_BIDANG_USAHA_ID  = P.PANEL_BIDANG_USAHA_ID
                WHERE 1 = 1  "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ORDER BY P.PERINGKAT ASC, CASE WHEN COALESCE(E.TOTAL, 0) = 0 THEN 0 ELSE ROUND((COALESCE(D.TOTAL, 0) / E.TOTAL) * 100, 2) END DESC";

		$this->query = $str;	
		return $this->selectLimit($str,$limit,$from); 
    }
	
    function selectByParamsPanelLelang($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PANEL_REKANAN_ID, TANGGAL_DAFTAR, KODE_REKANAN, AANWIJZING, COALESCE(LULUS_KUALIFIKASI, 1) LULUS_KUALIFIKASI, LULUS_KUALIFIKASI_KETERANGAN, COALESCE(LULUS_PENDAFTARAN, 2) LULUS_PENDAFTARAN, LULUS_PENDAFTARAN_KETERANGAN
				FROM PANEL_REKANAN WHERE PANEL_REKANAN_ID IS NOT NULL "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PANEL_REKANAN_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsPembukaanAuction($panel_id, $paket_rekanan_id)
	{
		$str = "
				SELECT 
				(SELECT SUM (ROWCOUNT) FROM
				(SELECT SUM(STATUS) AS ROWCOUNT FROM REKANAN_EVAL_ADMIN_TAWAR WHERE REKANAN_EVAL_ADMIN_TAWAR_ID IS NOT NULL AND PANEL_REKANAN_ID = ".$paket_rekanan_id." AND STATUS = 1
				UNION ALL
				SELECT SUM(STATUS) AS ROWCOUNT FROM REKANAN_EVAL_TEKNIS_TAWAR WHERE REKANAN_EVAL_TEKNIS_TAWAR_ID IS NOT NULL AND PANEL_REKANAN_ID = ".$paket_rekanan_id."  AND STATUS = 1) A) REKANAN,
				(SELECT SUM (ROWCOUNT) FROM
				(SELECT COUNT(PANEL_ID) AS ROWCOUNT FROM PAKET_EVAL_ADMIN_TAWAR WHERE PAKET_EVAL_ADMIN_TAWAR_ID IS NOT NULL AND PANEL_ID = ".$panel_id."
				UNION ALL
				SELECT COUNT(PANEL_ID) AS ROWCOUNT FROM PAKET_EVAL_TEKNIS_TAWAR WHERE PAKET_EVAL_TEKNIS_TAWAR_ID IS NOT NULL AND PANEL_ID = ".$panel_id.") A) PAKET
				 FROM DUAL		
		 "; 
		
		$this->query = $str;
			
		return $this->selectLimit($str,-1,-1); 
    }

    function selectNilaiPenawaran($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT   PANEL_REKANAN_ID, NILAI_PENAWARAN
					FROM PANEL_REKANAN
				   WHERE TANGGAL_DAFTAR IS NOT NULL
					 AND NOT NVL (NILAI_PENAWARAN, 0) = 0
				 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY NILAI_PENAWARAN ASC ";
		
			
		return $this->selectLimit($str,$limit,$from); 
    }	

    function selectPengalamanBidangUsaha($paramsArray=array(),$limit=-1,$from=-1, $statement='', $batas_tahun='')
	{
		$str = "SELECT BIDANG_USAHA_ID, AMBIL_BIDANG_USAHA_NAMA(BIDANG_USAHA_ID) NAMA, 
				(SELECT COUNT(1) FROM REKANAN_PENGALAMAN_BIDANG X INNER JOIN REKANAN_PENGALAMAN Y ON X.REKANAN_PENGALAMAN_ID = Y.REKANAN_PENGALAMAN_ID 
				 WHERE TO_CHAR(KONTRAK_TANGGAL, 'YYYY') >= '".$batas_tahun."' AND X.BIDANG_USAHA_ID = A.BIDANG_USAHA_ID AND Y.REKANAN_ID = B.REKANAN_ID) JUMLAH 
				FROM PANEL_REKANAN_BIDANG_USAHA A INNER JOIN PANEL_REKANAN B ON A.PANEL_REKANAN_ID = B.PANEL_REKANAN_ID 
				WHERE 1= 1 
				 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY BIDANG_USAHA_ID ASC ";
			
		return $this->selectLimit($str,$limit,$from); 
    }    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT  PANEL_REKANAN_ID, PANEL_ID, REKANAN_ID, 
				   TANGGAL_UNDANG, TANGGAL_DAFTAR, NILAI_PENAWARAN, 
				   KODE_REKANAN, PAKTA, SPM, 
				   NILAI_PENGALAMAN, NILAI_PERSONIL, NILAI_PERALATAN, 
				   NILAI_KEUANGAN, NILAI_SERTIFIKAT_LAIN, LULUS_KUALIFIKASI, 
				   LULUS_PENAWARAN, LULUS_PENAWARAN_URUT, LULUS_ADMINISTRASI, 
				   LULUS_KEMAMPUAN_DASAR, LULUS_KUALIFIKASI_REKANAN, LULUS_KEWAJARAN_HARGA, 
				   STATUS_BAYAR, DI_EMAIL
				FROM PANEL_REKANAN WHERE PANEL_REKANAN_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PANEL_REKANAN_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
	
    function getPanelRekananId($panel_id, $rekanan_id, $statement='')
	{
		$str = "SELECT PANEL_REKANAN_ID AS ROWCOUNT FROM PANEL_REKANAN 
				WHERE PANEL_REKANAN_ID IS NOT NULL AND PANEL_ID = '".$panel_id."' AND REKANAN_ID = '".$rekanan_id."' ".$statement; 
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }	

    function getPanelId($paket_rekanan_id)
	{
		$str = "SELECT PANEL_ID AS ROWCOUNT FROM PANEL_REKANAN 
				WHERE PANEL_REKANAN_ID IS NOT NULL AND PANEL_REKANAN_ID = '".$paket_rekanan_id."' ".$statement; 
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }	
	
    function getPanelRekananIdEncrypt($panel_id, $rekanan_id, $statement='')
	{
		$str = "SELECT PANEL_REKANAN_ID AS ROWCOUNT FROM PANEL_REKANAN 
				WHERE PANEL_REKANAN_ID IS NOT NULL AND MD5(PANEL_ID || '".$rekanan_id."') = '".$panel_id."' AND REKANAN_ID = '".$rekanan_id."' ".$statement; 
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }	
	
    function getPanelRekananBayar($panel_id, $rekanan_id, $statement='')
	{
		$str = "SELECT STATUS_BAYAR AS ROWCOUNT FROM PANEL_REKANAN 
				WHERE PANEL_REKANAN_ID IS NOT NULL AND PANEL_ID = '".$panel_id."' AND REKANAN_ID = '".$rekanan_id."' ".$statement; 
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }	

    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(PANEL_REKANAN_ID) AS ROWCOUNT FROM PANEL_REKANAN A INNER JOIN REKANAN B ON A.REKANAN_ID = B.REKANAN_ID
                INNER JOIN PANEL C ON A.PANEL_ID = C.PANEL_ID
				WHERE A.PANEL_REKANAN_ID IS NOT NULL ".$statement; 
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

    function getCountByParamsMonitoring($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(A.PANEL_REKANAN_ID) AS ROWCOUNT FROM PANEL_REKANAN A 
                INNER JOIN REKANAN B ON A.REKANAN_ID = B.REKANAN_ID
                INNER JOIN PANEL C ON A.PANEL_ID = C.PANEL_ID
                INNER JOIN PANEL_REKANAN_BIDANG_USAHA P ON A.PANEL_REKANAN_ID = P.PANEL_REKANAN_ID
                LEFT JOIN BIDANG_USAHA_PANEL_NILAI D ON A.PANEL_ID = D.PANEL_ID  AND A.REKANAN_ID = D.REKANAN_ID AND D.PANEL_BIDANG_USAHA_ID = P.PANEL_BIDANG_USAHA_ID 
                LEFT JOIN BIDANG_USAHA_PANEL_NILAI_MAX E ON A.PANEL_ID = E.PANEL_ID AND E.PANEL_BIDANG_USAHA_ID = P.PANEL_BIDANG_USAHA_ID
                LEFT JOIN REK_KORAN_PANEL_NILAI F ON A.PANEL_ID = F.PANEL_ID  AND A.REKANAN_ID = F.REKANAN_ID
                LEFT JOIN REK_KORAN_PANEL_NILAI_MAX G ON A.PANEL_ID = G.PANEL_ID AND G.PANEL_BIDANG_USAHA_ID  = P.PANEL_BIDANG_USAHA_ID
                WHERE 1 = 1 ".$statement; 
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
		$str = "SELECT COUNT(PANEL_REKANAN_ID) AS ROWCOUNT FROM PANEL_REKANAN WHERE PANEL_REKANAN_ID IS NOT NULL "; 
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