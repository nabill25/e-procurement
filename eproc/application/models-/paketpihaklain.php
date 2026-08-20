<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketPihakLain extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
		parent::__construct();
	}
	
    function PaketPihakLain()
	{
      $this->Entity(); 
    }
    
    function insert()
    {
        $this->setField('PAKET_PIHAK_LAIN_ID', $this->getNextId("PAKET_PIHAK_LAIN_ID","PAKET_PIHAK_LAIN"));
        $q = "Insert into PAKET_PIHAK_LAIN
                (PAKET_PIHAK_LAIN_ID, USER_LOGIN_ID, PIHAK_LAIN_TYPE, STATUS, JABATAN, 
                 PAKET_ID)
              Values
                (".$this->getField("PAKET_PIHAK_LAIN_ID").",".$this->getField("USER_LOGIN_ID").",NULL,".$this->getField("STATUS").",NULL, 
                 ".$this->getField("PAKET_ID").")";
        $this->query = $q;
        return $this->execQuery($q);
    }
    
    function deleteNotIn($paketId,$notIn = '')
    {
        $q = "DELETE FROM PAKET_PIHAK_LAIN WHERE PAKET_ID = ".$paketId;
        
        if(!empty($notIn))
            $q .= " AND PAKET_PIHAK_LAIN_ID NOT IN (".$notIn.")";
        $this->query = $q;
        return $this->execQuery($q);
    }
    function selectByParamsEmail($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = " SELECT PAKET_REKANAN_ID, EMAIL, LULUS_KUALIFIKASI, LULUS_KUALIFIKASI_KETERANGAN, NAMA FROM PAKET_REKANAN A, REKANAN B WHERE PAKET_REKANAN_ID IS NOT NULL AND A.REKANAN_ID = B.REKANAN_ID "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_REKANAN_ID ASC";
			
		return $this->selectLimit($str,$limit,$from); 
    }

	
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $paketId='')
	{
		$str = "select  a.PAKET_ID,a.PIHAK_LAIN_TYPE,a.PAKET_PIHAK_LAIN_ID,a.USER_LOGIN_ID,
                        b.UNIT_KERJA_ID,b.USER_ALAMAT,b.USER_JABATAN,b.USER_LOGIN,b.USER_NAMA,b.USER_TELEPON,b.USER_TYPE_ID,
                        c.NAMA tipe_nama, B.NIP
                        from paket_pihak_lain a, user_login b,user_type c
                        where a.USER_LOGIN_ID = b.USER_LOGIN_ID
                        and b.USER_TYPE_ID = c.USER_TYPE_ID "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= $statement." ORDER BY B.USER_NAMA ASC";
		$this->query = $str;	
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsPaktaIntegritas($paramsArray=array(),$limit=-1,$from=-1, $statement='', $paketId='')
	{
		$str = "SELECT  A.PAKET_ID,A.PIHAK_LAIN_TYPE,A.PAKET_PIHAK_LAIN_ID,A.USER_LOGIN_ID,
                        B.UNIT_KERJA_ID,B.USER_ALAMAT,B.USER_JABATAN,B.USER_LOGIN,B.USER_NAMA,B.USER_TELEPON,B.USER_TYPE_ID,
                        COALESCE(B.NIP, SUBSTR(B.USER_NAMA, 0, 49)) NIP, C.KODE, C.KODE_QR, TRIM(LPAD(A.PAKET_PIHAK_LAIN_ID::VARCHAR, 9, '0')) NIP_LOGIN_ID
                        FROM PAKET_PIHAK_LAIN A 
                        INNER JOIN USER_LOGIN B ON A.USER_LOGIN_ID = B.USER_LOGIN_ID
                        LEFT JOIN PAKET_PAKTA_INTEGRITAS C ON A.PAKET_ID = C.PAKET_ID 
						-- AND COALESCE(B.NIP, SUBSTR(B.USER_NAMA, 0, 49)) = C.KODE --aim: INC0000634 join jgn pake nama karena ada yg namanya pake tanda --> &  --> sewaktu validasi json reqKode nya kepotong.
						-- ganti join by user_login_id
						AND B.USER_LOGIN_ID=C.USER_LOGIN_ID
            WHERE 1 = 1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= $statement." ORDER BY B.USER_NAMA ASC";
		$this->query = $str;	
		return $this->selectLimit($str,$limit,$from); 
    }
	
	function selectByParamsBeritaAanwijzing($paramsArray=array(),$limit=-1,$from=-1, $statement='', $paketId='')
	{
		$str = "SELECT  A.PAKET_ID,A.PIHAK_LAIN_TYPE,A.PAKET_PIHAK_LAIN_ID,A.USER_LOGIN_ID,
                        B.UNIT_KERJA_ID,B.USER_ALAMAT,B.USER_JABATAN,B.USER_LOGIN,B.USER_NAMA,B.USER_TELEPON,B.USER_TYPE_ID,
                        COALESCE(B.NIP, SUBSTR(B.USER_NAMA, 0, 49)) NIP, C.KODE, C.KODE_QR, TRIM(LPAD(A.PAKET_PIHAK_LAIN_ID::VARCHAR, 9, '0')) NIP_LOGIN_ID
                        FROM PAKET_PIHAK_LAIN A 
                        INNER JOIN USER_LOGIN B ON A.USER_LOGIN_ID = B.USER_LOGIN_ID
                        LEFT JOIN PAKET_AANWIJZING_VALIDASI C ON A.PAKET_ID = C.PAKET_ID AND B.USER_LOGIN_ID = C.USER_LOGIN_ID
            WHERE 1 = 1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= $statement." ORDER BY B.USER_NAMA ASC";
		$this->query = $str;	
		return $this->selectLimit($str,$limit,$from); 
    }
	
    function selectByParamsPaketLelang($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "select * 
                        from paket_pihak_lain where 1 = 1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_PIHAK_LAIN_ID ASC";
			
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsPembukaanAuction($paket_id, $paket_rekanan_id)
	{
		$str = "
				SELECT 
				(SELECT SUM (ROWCOUNT) FROM
				(SELECT SUM(STATUS) AS ROWCOUNT FROM REKANAN_EVAL_ADMIN_TAWAR WHERE REKANAN_EVAL_ADMIN_TAWAR_ID IS NOT NULL AND PAKET_REKANAN_ID = ".$paket_rekanan_id." AND STATUS = 1
				UNION ALL
				SELECT SUM(STATUS) AS ROWCOUNT FROM REKANAN_EVAL_TEKNIS_TAWAR WHERE REKANAN_EVAL_TEKNIS_TAWAR_ID IS NOT NULL AND PAKET_REKANAN_ID = ".$paket_rekanan_id."  AND STATUS = 1) A) REKANAN,
				(SELECT SUM (ROWCOUNT) FROM
				(SELECT COUNT(PAKET_ID) AS ROWCOUNT FROM PAKET_EVAL_ADMIN_TAWAR WHERE PAKET_EVAL_ADMIN_TAWAR_ID IS NOT NULL AND PAKET_ID = ".$paket_id."
				UNION ALL
				SELECT COUNT(PAKET_ID) AS ROWCOUNT FROM PAKET_EVAL_TEKNIS_TAWAR WHERE PAKET_EVAL_TEKNIS_TAWAR_ID IS NOT NULL AND PAKET_ID = ".$paket_id.") A) PAKET
				 FROM DUAL		
		 "; 
		
		$this->query = $str;
			
		return $this->selectLimit($str,-1,-1); 
    }

    function selectNilaiPenawaran($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT   PAKET_REKANAN_ID, NILAI_PENAWARAN
					FROM PAKET_REKANAN
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
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT  PAKET_REKANAN_ID, PAKET_ID, REKANAN_ID, 
				   TANGGAL_UNDANG, TANGGAL_DAFTAR, NILAI_PENAWARAN, 
				   KODE_REKANAN, PAKTA, SPM, 
				   NILAI_PENGALAMAN, NILAI_PERSONIL, NILAI_PERALATAN, 
				   NILAI_KEUANGAN, NILAI_SERTIFIKAT_LAIN, LULUS_KUALIFIKASI, 
				   LULUS_PENAWARAN, LULUS_PENAWARAN_URUT, LULUS_ADMINISTRASI, 
				   LULUS_KEMAMPUAN_DASAR, LULUS_KUALIFIKASI_REKANAN, LULUS_KEWAJARAN_HARGA, 
				   STATUS_BAYAR, DI_EMAIL
				FROM PAKET_REKANAN WHERE PAKET_REKANAN_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_REKANAN_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
	
    function getPaketRekananId($paket_id, $rekanan_id, $statement='')
	{
		$str = "SELECT PAKET_REKANAN_ID AS ROWCOUNT FROM PAKET_REKANAN 
				WHERE PAKET_REKANAN_ID IS NOT NULL AND PAKET_ID = '".$paket_id."' AND REKANAN_ID = '".$rekanan_id."' ".$statement; 
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }	

    function getPaketRekananBayar($paket_id, $rekanan_id, $statement='')
	{
		$str = "SELECT STATUS_BAYAR AS ROWCOUNT FROM PAKET_REKANAN 
				WHERE PAKET_REKANAN_ID IS NOT NULL AND PAKET_ID = '".$paket_id."' AND REKANAN_ID = '".$rekanan_id."' ".$statement; 
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }	

    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(PAKET_REKANAN_ID) AS ROWCOUNT FROM PAKET_REKANAN WHERE PAKET_REKANAN_ID IS NOT NULL ".$statement; 
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
		$str = "SELECT COUNT(PAKET_REKANAN_ID) AS ROWCOUNT FROM PAKET_REKANAN WHERE PAKET_REKANAN_ID IS NOT NULL "; 
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