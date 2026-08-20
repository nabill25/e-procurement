<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketProgresTemplate extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PaketProgresTemplate()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_PROGRES_TEMPLATE_ID", $this->getNextId("PAKET_PROGRES_TEMPLATE_ID","PAKET_PROGRES_TEMPLATE")); 

		$str = "
		
			INSERT INTO PAKET_PROGRES_TEMPLATE (
			   PAKET_PROGRES_TEMPLATE_ID, USER_TYPE_ID, NAMA, 
			   KETERANGAN)
 
  			 	VALUES (
				  '".$this->getField("PAKET_PROGRES_TEMPLATE_ID")."',
  				  '".$this->getField("USER_TYPE_ID")."',
   				  '".$this->getField("NAMA")."',
				  '".$this->getField("KETERANGAN")."'
				)"; 
				
		$this->query = $str;
		$this->id = $this->getField("PAKET_PROGRES_TEMPLATE_ID");
		return $this->execQuery($str);
    }


	function delete()
	{
        $str = "DELETE FROM PAKET_PROGRES_TEMPLATE
                WHERE 
                  PAKET_PROGRES_TEMPLATE_ID = ".$this->getField("PAKET_PROGRES_TEMPLATE_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","PAKET_PROGRES_TEMPLATE_METODE_EVALUASI_ID"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $sOrder="ORDER BY URUT ASC")
	{
		$str = "
					SELECT 
					 PAKET_PROGRES_TEMPLATE_ID, USER_TYPE_ID, NAMA, URUT,
			  		 KETERANGAN
					FROM PAKET_PROGRES_TEMPLATE A 
				    WHERE PAKET_PROGRES_TEMPLATE_ID IS NOT NULL "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
			
		$str .= $statement." ".$sOrder;
		$this->query = $str;		
		return $this->selectLimit($str,$limit,$from); 
    }
	

	function selectByParamsPicUserLogin($paramsArray=array(),$limit=-1,$from=-1, $statement='', $sOrder="ORDER BY USER_NAMA ASC")
	{
		$str = "
				SELECT USER_LOGIN_ID, USER_TYPE_ID, USER_NAMA
				FROM USER_LOGIN
				WHERE 1=1  "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
			
		$str .= $statement." ".$sOrder;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
    }
	
	function selectByParamsPic($paramsArray=array(),$limit=-1,$from=-1, $statement='', $sOrder="ORDER BY NAMA ASC")
	{
		$str = "
				SELECT NIPP USER_LOGIN_ID, 9 USER_TYPE_ID, NAMA USER_NAMA
				FROM V_OAUTH_USER
				WHERE 1=1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
			
		$str .= $statement." ".$sOrder;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
    }
	
	function selectByParamsMonitoring($paramsArray=array(),$limit=-1,$from=-1, $statement='', $sOrder="ORDER BY A.TANGGAL")
	{
		$str = "
			    SELECT 
				   A.REKANAN_ID_PEMENANG, I.NAMA PEMENANG,
				   a.PAKET_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI, 
				   A.PAKET_METODE_KUALIFIKASI_ID,
				   D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN, 
				   G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(a.PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN, 
				   A.LOKASI, A.ALAMAT, A.TELEPON, 
				   A.FAX, A.EMAIL, A.SYARAT, 
				   A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL, 
				   A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI, A.TANGGAL_PENGUMUMAN_PEMENANG,
				   A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE, H.UNIT_KERJA_ID ,H.NAMA UNIT_KERJA, A.USER_LOGIN_ID, A.ALASAN, A.PAKET_METODE_LELANG_ID,
				   COALESCE((SELECT tanggal_awal FROM PAKET_TAHAP WHERE PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang' and rownum =1 ),A.TANGGAL) tanggal_tahap
				FROM    PAKET A,
						PAKET_METODE_LELANG B, 
						PAKET_METODE_KUALIFIKASI C, 
						PAKET_METODE_EVALUASI D, 
						PAKET_JENIS E, 
						V_OAUTH_USER F, 
						REKANAN_KUALIFIKASI G,
						UNIT_KERJA H,
						REKANAN I
				WHERE A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID(+) AND
					  A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID(+) AND
					  A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID(+) AND
					  A.PAKET_JENIS_ID = E.PAKET_JENIS_ID(+) AND
					  TO_CHAR(A.USER_LOGIN_ID) = F.NIPP (+) AND
					  A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID(+) AND
					  A.UNIT_KERJA_ID = H.UNIT_KERJA_ID(+) AND
					  A.REKANAN_ID_PEMENANG = I.REKANAN_ID(+)
					  AND A.REKANAN_ID_PEMENANG IS NOT NULL
				"; 
				
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
			
		$str .= $statement." ".$sOrder;
		$this->query = $str;
				
		return $this->selectLimit($str,$limit,$from); 
    } 
   			
	function getCountByParamsMonitoring($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(1) AS ROWCOUNT 
					FROM    PAKET A,
						PAKET_METODE_LELANG B, 
						PAKET_METODE_KUALIFIKASI C, 
						PAKET_METODE_EVALUASI D, 
						PAKET_JENIS E, 
						V_OAUTH_USER F, 
						REKANAN_KUALIFIKASI G,
						UNIT_KERJA H,
						REKANAN I
				WHERE A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID(+) AND
					  A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID(+) AND
					  A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID(+) AND
					  A.PAKET_JENIS_ID = E.PAKET_JENIS_ID(+) AND
					  TO_CHAR(A.USER_LOGIN_ID) = F.NIPP (+) AND
					  A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID(+) AND
					  A.UNIT_KERJA_ID = H.UNIT_KERJA_ID(+) AND
					  A.REKANAN_ID_PEMENANG = I.REKANAN_ID(+)
					  AND A.REKANAN_ID_PEMENANG IS NOT NULL ".$statement; 
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
		$str = "SELECT COUNT(A.PAKET_PROGRES_TEMPLATE_ID) AS ROWCOUNT 
					FROM    PAKET_PROGRES_TEMPLATE A
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