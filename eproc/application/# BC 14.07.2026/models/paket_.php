<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class Paket extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function Paket()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_ID", $this->getNextId("PAKET_ID","PAKET")); 

		$str = "
		
			INSERT INTO PAKET (
			   PAKET_ID, PAKET_METODE_LELANG_ID, PAKET_METODE_KUALIFIKASI_ID, 
			   PAKET_METODE_EVALUASI_ID, PAKET_JENIS_ID, USER_LOGIN_ID, 
			   REKANAN_KUALIFIKASI_ID, NAMA, URAIAN, 
			   LOKASI, ALAMAT, TELEPON, 
			   EMAIL, 
			   TANGGAL, PUBLISH_PAKET,
			   PUBLISH_PEMENANG, NILAI, UNIT_KERJA_ID, PERMOHONAN_PAKET_ID) 
 
  			 	VALUES (
				  ".$this->getField("PAKET_ID").",
  				  ".$this->getField("PAKET_METODE_LELANG_ID").",
   				  ".$this->getField("PAKET_METODE_KUALIFIKASI_ID").",
				  ".$this->getField("PAKET_METODE_EVALUASI_ID").",
				  ".$this->getField("PAKET_JENIS_ID").",
  				  ".$this->getField("USER_LOGIN_ID").",
   				  ".$this->getField("REKANAN_KUALIFIKASI_ID").",
   				  '".$this->getField("NAMA")."',
				  '".$this->getField("URAIAN")."',
   				  '".$this->getField("LOKASI")."',
                                  '".$this->getField("ALAMAT")."',
   				  '".$this->getField("TELEPON")."',
				  '".$this->getField("EMAIL")."',
   				  CURRENT_DATE,
				  ".$this->getField("PUBLISH_PAKET").",
   				  ".$this->getField("PUBLISH_PEMENANG").",
				  ".$this->getField("NILAI").",
                  ".$this->getField('UNIT_KERJA_ID').",
                  '".$this->getField('PERMOHONAN_PAKET_ID')."'
				)"; 
				
		$this->query = $str;
		$this->id = $this->getField("PAKET_ID");
		return $this->execQuery($str);
    }

	function update_dyna()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET A SET
				  ".$this->getField("FIELD")." = ".$this->getField("FIELD_VALUE")."
				WHERE PAKET_ID = ".$this->getField("PAKET_ID")."
				"; 
				$this->query = $str;
		//echo $str;
		return $this->execQuery($str);
    }
	
	function update_set_null_syarat()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET SET
				  SYARAT_TEKNIS_TENAGA_AHLI = NULL,
				  SYARAT_TEKNIS_PERALATAN = NULL,
				  SYARAT_TEKNIS_SERTIFIKAT = NULL,
				  SYARAT_REKENING_KORAN = NULL,
				  SYARAT_KEUANGAN_SPT = NULL,
				  SYARAT_KEUANGAN_PPN = NULL,
				  SYARAT_KEUANGAN_PPH = NULL,		
				  SYARAT_TEKNIS_SERTIFIKAT_INFO = NULL,
				  SYARAT_KEUANGAN_PKP = NULL,
				  SYARAT_ADM_KUALIFIKASI = NULL,
				  SYARAT_IJIN_SIUJK = NULL,
				  SYARAT_IJIN_SIUI = NULL,
				  SYARAT_IJIN_LAIN = NULL,
				  SYARAT_ADM_KUALIFIKASI_INFO = NULL,
				  SYARAT_NERACA = NULL,
				  SYARAT_SBU = NULL,
				  SYARAT_IJIN_SIUP = NULL
				WHERE PAKET_ID = ".$this->getField("PAKET_ID")."
				"; 
				$this->query = $str;
		//echo $str;
		return $this->execQuery($str);
    }
	
	function updateNilaiOwner()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "		
				 UPDATE  PAKET
				SET    
					   NILAI_OWNER_ESTIMATE        = '".$this->getField("NILAI_OWNER_ESTIMATE")."'
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."
			  
				"; 
				$this->query = $str;
				
		return $this->execQuery($str);
    }

	function updatePemenang()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "		
				 UPDATE  PAKET
				SET    
					   NILAI_NEGOSIASI        = '".$this->getField("NILAI_NEGOSIASI")."',
					   TANGGAL_PENGUMUMAN_PEMENANG = ".$this->getField("TANGGAL_PENGUMUMAN_PEMENANG").",
					   REKANAN_ID_PEMENANG        = '".$this->getField("REKANAN_ID_PEMENANG")."'
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."
			  
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
    function updateByField()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET A SET
				  ".$this->getField("FIELD")." = ".$this->getField("FIELD_VALUE")."
				WHERE PAKET_ID = ".$this->getField("PAKET_ID")."
				"; 
				$this->query = $str;
	
		return $this->execQuery($str);
    }
	
	function updateAlasan()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET A SET
				  ALASAN = '".$this->getField("ALASAN")."'
				WHERE PAKET_ID = ".$this->getField("PAKET_ID")."
				"; 
				$this->query = $str;
	
		return $this->execQuery($str);
    }
	
    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "		
				 UPDATE  PAKET
				SET    
					   PAKET_METODE_LELANG_ID      =  ".$this->getField("PAKET_METODE_LELANG_ID").",
					   PAKET_METODE_KUALIFIKASI_ID = ".$this->getField("PAKET_METODE_KUALIFIKASI_ID").",
					   PAKET_METODE_EVALUASI_ID    = ".$this->getField("PAKET_METODE_EVALUASI_ID").",
					   PAKET_JENIS_ID              = ".$this->getField("PAKET_JENIS_ID").",
					   USER_LOGIN_ID               = ".$this->getField("USER_LOGIN_ID").",
					   REKANAN_KUALIFIKASI_ID      =  ".$this->getField("REKANAN_KUALIFIKASI_ID").",
					   NAMA                        = '".$this->getField("NAMA")."',
					   URAIAN                      = '".$this->getField("URAIAN")."',
					   LOKASI                      = '".$this->getField("LOKASI")."',
					   ALAMAT                      = '".$this->getField("ALAMAT")."',
					   TELEPON                     = '".$this->getField("TELEPON")."',
					   EMAIL                       = '".$this->getField("EMAIL")."',
					   NILAI                       =   ".$this->getField("NILAI").",
					   PERMOHONAN_PAKET_ID		   = '".$this->getField("PERMOHONAN_PAKET_ID")."'
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."
			  
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PAKET
                WHERE 
                  PAKET_ID = ".$this->getField("PAKET_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","PAKET_METODE_EVALUASI_ID"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
					SELECT 
					A.PAKET_ID, A.PAKET_METODE_LELANG_ID, A.PAKET_METODE_KUALIFIKASI_ID,
					   A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.USER_LOGIN_ID,
					   A.REKANAN_KUALIFIKASI_ID, A.NAMA, A.URAIAN,
					   A.LOKASI, A.ALAMAT, A.TELEPON, 
					   A.FAX, A.EMAIL, A.SYARAT, 
					   A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL, 
					   A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI,
					   A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE, A.PERMOHONAN_PAKET_ID, B.NOTA_DINAS PERMOHONAN_NOTA_DINAS, B.NAMA PERMOHONAN
					FROM PAKET A
					LEFT JOIN PERMOHONAN_PAKET B ON A.PERMOHONAN_PAKET_ID = B.PERMOHONAN_PAKET_ID
				    WHERE A.PAKET_ID IS NOT NULL "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
			
		$str .= $statement." ORDER BY A.NAMA ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByPaketRekananKeterangan($paket_id, $paket_rekanan_id, $rekanan_id, $urut_kualifikasi1, $urut_penawaran1)
	{
		$str = "
				SELECT KETERANGAN FROM
				(
				SELECT COALESCE(TANGGAL_AKHIR, TANGGAL_AWAL) TANGGAL_BATAS, CASE WHEN COALESCE((SELECT COUNT(*) FROM REKANAN_EVAL_ADMIN WHERE PAKET_REKANAN_ID = ".$paket_rekanan_id."), 0) = 0 THEN 'Anda gagal pada tahap kualifikasi karena tidak memasukkan data kualifikasi' END KETERANGAN 
                                    FROM PAKET_TAHAP WHERE PAKET_ID = ".$paket_id." AND TAMPILKAN = 1 AND URUT = ".$urut_kualifikasi1."
				UNION ALL
				SELECT COALESCE(TANGGAL_AKHIR, TANGGAL_AWAL) TANGGAL_BATAS, CASE WHEN COALESCE((SELECT COUNT(*) 
                                FROM PAKET_DOKUMEN WHERE REKANAN_USER_ID = ".$rekanan_id." AND JENIS_DOKUMEN = 'PENAWARAN'), 0) = 0 THEN 'Anda gagal pada tahap penawaran karena tidak memasukkan dokumen penawaran' END KETERANGAN FROM PAKET_TAHAP WHERE PAKET_ID = ".$paket_id." AND TAMPILKAN = 1 AND URUT = ".$urut_penawaran1."
				) A WHERE TO_DATE(TANGGAL_BATAS, 'yyyy/mm/dd hh:mi:ss') < TO_DATE(CURRENT_DATE, 'yyyy/mm/dd hh:mi:ss') AND KETERANGAN IS NOT NULL
	  "; 
					
		$this->query = $str;
	return $this->selectLimit($str, -1, -1); 
    }
	
    function selectById($paket_Id)
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
                           A.PAKET_ID, A.NAMA, A.PAKET_METODE_LELANG_ID, A.PAKET_METODE_KUALIFIKASI_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID,  
                           B.NAMA PAKET_METODE_LELANG, C.NAMA PAKET_METODE_KUALIFIKASI, 
                           D.NAMA PAKET_METODE_EVALUASI, E.NAMA PAKET_JENIS, G.NAMA REKANAN_KUALIFIKASI, A.NILAI, A.NILAI_OWNER_ESTIMATE, A.TANGGAL, A.PASS_GRADE, A.LOKASI,
                           COALESCE((SELECT tanggal_awal FROM PAKET_TAHAP WHERE PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang' and rownum =1 ),A.TANGGAL) tanggal_tahap,
						   COALESCE((SELECT tanggal_awal FROM PAKET_TAHAP WHERE PAKET_ID = A.PAKET_ID and NAMA = 'Pemasukan data kualifikasi' and rownum =1 ),A.TANGGAL) tanggal_pemasukan, --aim: INC0002723
                           REKANAN_ID_PEMENANG, A.NILAI_NEGOSIASI, A.TANGGAL_PENGUMUMAN_PEMENANG, REKANAN_ID_PENILAIAN, A.USER_LOGIN_ID,
                           H.NAMA UNIT_KERJA, A.UNIT_KERJA_ID, TO_CHAR(A.PUBLISH_PAKET_TANGGAL, 'DD-MM-YYYY HH24:MI') PUBLISH_PAKET_TANGGAL, SYARAT_IJIN_SIUP, A.SYARAT_KEUANGAN_SPT_TAHUN,
						   A.SYARAT_NERACA_TAHUN
                            FROM    PAKET A,
                                        PAKET_METODE_LELANG B, 
                                        PAKET_METODE_KUALIFIKASI C, 
                                        PAKET_METODE_EVALUASI D, 
                                        PAKET_JENIS E, 
                                        USER_LOGIN F, 
                                        REKANAN_KUALIFIKASI G,
                                        UNIT_KERJA H
                        WHERE A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID(+) AND
                                  A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID(+) AND
                                  A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID(+) AND
                                  A.PAKET_JENIS_ID = E.PAKET_JENIS_ID(+) AND
                                  A.USER_LOGIN_ID = F.USER_LOGIN_ID(+) AND
                                  A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID(+) AND
                                  A.UNIT_KERJA_ID = H.UNIT_KERJA_ID(+) AND
                                  A.PAKET_ID = ".$paket_Id."
	  "; 
					
		$this->query = $str;
		return $this->select($str);  
    }
	
    function selectByParamsMonitoring($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
	//THE NEW ONE, TANGGAL DI TAHAPAN LELANG IKUT DIQUERY SEBAGAI ACUAN PEMBUATAN LELANG	
        $str = "select * from (
					SELECT 
                       A.PAKET_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI, 
                       A.PAKET_METODE_KUALIFIKASI_ID,
                       D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN, 
                       G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(A.PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN, 
                       A.LOKASI, A.ALAMAT, A.TELEPON, 
                       A.FAX, A.EMAIL, A.SYARAT, 
                       A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL, 
                       A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI, 
                       A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE, H.UNIT_KERJA_ID ,H.NAMA UNIT_KERJA, A.USER_LOGIN_ID, A.ALASAN, A.PAKET_METODE_LELANG_ID,
                       COALESCE((SELECT TANGGAL_AWAL FROM PAKET_TAHAP WHERE PAKET_ID = A.PAKET_ID AND NAMA = 'PEMBUATAN PAKET LELANG' AND ROWNUM =1 ),A.TANGGAL) TANGGAL_TAHAP,
                       COALESCE(I.USER_LOGIN_ID, 0) USER_LOGIN_ID_FUNGSIONAL, AMBIL_PAKET_BIDANG_USAHA_ID(A.PAKET_ID) BIDANG_USAHA_ID
                    FROM    PAKET A,
                            PAKET_METODE_LELANG B, 
                            PAKET_METODE_KUALIFIKASI C, 
                            PAKET_METODE_EVALUASI D, 
                            PAKET_JENIS E, 
                            V_OAUTH_USER F, 
                            REKANAN_KUALIFIKASI G,
                            UNIT_KERJA H,
                            PERMOHONAN_PAKET I
                    WHERE A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID(+) AND
                          A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID(+) AND
                          A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID(+) AND
                          A.PAKET_JENIS_ID = E.PAKET_JENIS_ID(+) AND
                          TO_CHAR(A.USER_LOGIN_ID) = F.NIPP (+) AND
                          A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID(+) AND
                          A.UNIT_KERJA_ID = H.UNIT_KERJA_ID(+) AND
                          A.PERMOHONAN_PAKET_ID = I.PERMOHONAN_PAKET_ID(+)
                    ) where 1 = 1
	  "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ORDER BY TANGGAL_TAHAP DESC,PAKET_ID DESC ";

		$this->query = $str;
                $rs = $this->selectLimit($str,$limit,$from);
//		print_r($rs);
                return  $rs;
    }

    function selectByParamsPaketRekanan($paramsArray=array(),$limit=-1,$from=-1, $rekanan_id='',$statement='')
	{
            $str = "
                    SELECT * FROM
                    (
                        SELECT 
                           PAKET_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI, 
                           D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN, 
                           G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN, 
                           A.LOKASI, A.ALAMAT, A.TELEPON, 
                           A.FAX, A.EMAIL, A.SYARAT, 
                           A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL, 
                           A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI, 
                           A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE,H.NAMA UNIT_KERJA, A.ALASAN, A.UNIT_KERJA_ID,
                           COALESCE((SELECT tanggal_awal FROM PAKET_TAHAP WHERE PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang' and rownum =1 ),A.TANGGAL) tanggal_tahap
                        FROM    PAKET A,
                                PAKET_METODE_LELANG B, 
                                PAKET_METODE_KUALIFIKASI C, 
                                PAKET_METODE_EVALUASI D, 
                                PAKET_JENIS E, 
                                V_OAUTH_USER F, 
                                REKANAN_KUALIFIKASI G,
                                UNIT_KERJA H
                        WHERE A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID(+) AND
                              A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID(+) AND
                              A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID(+) AND
                              A.PAKET_JENIS_ID = E.PAKET_JENIS_ID(+) AND
                              TO_CHAR(A.USER_LOGIN_ID) = F.NIPP (+) AND
                              A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID(+) AND
                              A.UNIT_KERJA_ID = H.UNIT_KERJA_ID(+) AND
                              A.PUBLISH_PAKET = 1                               
                    UNION ALL
                        SELECT 
                           PAKET_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI, 
                           D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN, 
                           G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN, 
                           A.LOKASI, A.ALAMAT, A.TELEPON, 
                           A.FAX, A.EMAIL, A.SYARAT, 
                           A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL, 
                           A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI, 
                           A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE, H.NAMA UNIT_KERJA, A.ALASAN, A.UNIT_KERJA_ID,
                           COALESCE((SELECT tanggal_awal FROM PAKET_TAHAP WHERE PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang' and rownum =1 ),A.TANGGAL) tanggal_tahap
                        FROM    PAKET A,
                                PAKET_METODE_LELANG B, 
                                PAKET_METODE_KUALIFIKASI C, 
                                PAKET_METODE_EVALUASI D, 
                                PAKET_JENIS E, 
                                V_OAUTH_USER F, 
                                REKANAN_KUALIFIKASI G,
                                UNIT_KERJA H
                        WHERE A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID(+) AND
                              A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID(+) AND
                              A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID(+) AND
                              A.PAKET_JENIS_ID = E.PAKET_JENIS_ID(+) AND
                              TO_CHAR(A.USER_LOGIN_ID) = F.NIPP (+) AND
                              A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID(+) AND
                              A.UNIT_KERJA_ID = H.UNIT_KERJA_ID(+) AND 
                              EXISTS(SELECT 1 FROM PAKET_REKANAN X WHERE X.PAKET_ID = A.PAKET_ID AND X.TANGGAL_UNDANG IS NOT NULL AND X.REKANAN_ID = '".$rekanan_id."')                      
                        ) WHERE 1 = 1
            "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ORDER BY TANGGAL_TAHAP DESC,PAKET_ID DESC";

		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
    }	
    function selectByParamsPaketFungsional($paramsArray=array(),$limit=-1,$from=-1, $user_id='',$statement='')
	{
            $str = "
                    SELECT * FROM
                    (
                        SELECT 
                           PAKET_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI, 
                           D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN, 
                           G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN, 
                           A.LOKASI, A.ALAMAT, A.TELEPON, 
                           A.FAX, A.EMAIL, A.SYARAT, 
                           A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL, 
                           A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI, 
                           A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE,H.NAMA UNIT_KERJA, A.ALASAN, A.UNIT_KERJA_ID,
                           COALESCE((SELECT tanggal_awal FROM PAKET_TAHAP WHERE PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang' and rownum =1 ),A.TANGGAL) tanggal_tahap
                        FROM    PAKET A,
                                PAKET_METODE_LELANG B, 
                                PAKET_METODE_KUALIFIKASI C, 
                                PAKET_METODE_EVALUASI D, 
                                PAKET_JENIS E, 
                                V_OAUTH_USER F, 
                                REKANAN_KUALIFIKASI G,
                                UNIT_KERJA H
                        WHERE A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID(+) AND
                              A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID(+) AND
                              A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID(+) AND
                              A.PAKET_JENIS_ID = E.PAKET_JENIS_ID(+) AND
                              TO_CHAR(A.USER_LOGIN_ID) = F.NIPP (+) AND
                              A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID(+) AND
                              A.UNIT_KERJA_ID = H.UNIT_KERJA_ID(+) AND
                              A.PUBLISH_PAKET = 1                               
                    UNION ALL
                        SELECT 
                           PAKET_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI, 
                           D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN, 
                           G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN, 
                           A.LOKASI, A.ALAMAT, A.TELEPON, 
                           A.FAX, A.EMAIL, A.SYARAT, 
                           A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL, 
                           A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI, 
                           A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE, H.NAMA UNIT_KERJA, A.ALASAN, A.UNIT_KERJA_ID,
                           COALESCE((SELECT tanggal_awal FROM PAKET_TAHAP WHERE PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang' and rownum =1 ),A.TANGGAL) tanggal_tahap
                        FROM    PAKET A,
                                PAKET_METODE_LELANG B, 
                                PAKET_METODE_KUALIFIKASI C, 
                                PAKET_METODE_EVALUASI D, 
                                PAKET_JENIS E, 
                                V_OAUTH_USER F, 
                                REKANAN_KUALIFIKASI G,
                                UNIT_KERJA H
                        WHERE A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID(+) AND
                              A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID(+) AND
                              A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID(+) AND
                              A.PAKET_JENIS_ID = E.PAKET_JENIS_ID(+) AND
                              TO_CHAR(A.USER_LOGIN_ID) = F.NIPP (+) AND
                              A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID(+) AND
                              A.UNIT_KERJA_ID = H.UNIT_KERJA_ID(+) AND 
                              EXISTS(SELECT 1 FROM PAKET_PIHAK_LAIN X WHERE X.PAKET_ID = A.PAKET_ID AND X.STATUS = 1 AND X.USER_LOGIN_ID = '".$user_id."')                      
                        ) WHERE 1 = 1
            "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ORDER BY TANGGAL_TAHAP DESC,PAKET_ID DESC";

		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
    }	
    function getCountByParamsPaketFungsional($paramsArray=array(),$limit=-1,$from=-1, $user_id='',$statement='')
	{
            $str = "select count(1) rowcount from (
                        SELECT 
                           PAKET_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI, 
                           D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN, 
                           G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN, 
                           A.LOKASI, A.ALAMAT, A.TELEPON, 
                           A.FAX, A.EMAIL, A.SYARAT, 
                           A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL, 
                           A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI, 
                           A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE,H.NAMA UNIT_KERJA, A.ALASAN, A.UNIT_KERJA_ID,
                           COALESCE((SELECT tanggal_awal FROM PAKET_TAHAP WHERE PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang' and rownum =1 ),A.TANGGAL) tanggal_tahap
                        FROM    PAKET A,
                                PAKET_METODE_LELANG B, 
                                PAKET_METODE_KUALIFIKASI C, 
                                PAKET_METODE_EVALUASI D, 
                                PAKET_JENIS E, 
                                V_OAUTH_USER F, 
                                REKANAN_KUALIFIKASI G,
                                UNIT_KERJA H
                        WHERE A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID(+) AND
                              A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID(+) AND
                              A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID(+) AND
                              A.PAKET_JENIS_ID = E.PAKET_JENIS_ID(+) AND
                              TO_CHAR(A.USER_LOGIN_ID) = F.NIPP(+) AND
                              A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID(+) AND
                              A.UNIT_KERJA_ID = H.UNIT_KERJA_ID(+) AND
                              A.PUBLISH_PAKET = 1                               
                    UNION ALL
                        SELECT 
                           PAKET_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI, 
                           D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN, 
                           G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN, 
                           A.LOKASI, A.ALAMAT, A.TELEPON, 
                           A.FAX, A.EMAIL, A.SYARAT, 
                           A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL, 
                           A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI, 
                           A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE, H.NAMA UNIT_KERJA, A.ALASAN, A.UNIT_KERJA_ID,
                           COALESCE((SELECT tanggal_awal FROM PAKET_TAHAP WHERE PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang' and rownum =1 ),A.TANGGAL) tanggal_tahap
                        FROM    PAKET A,
                                PAKET_METODE_LELANG B, 
                                PAKET_METODE_KUALIFIKASI C, 
                                PAKET_METODE_EVALUASI D, 
                                PAKET_JENIS E, 
                                V_OAUTH_USER F, 
                                REKANAN_KUALIFIKASI G,
                                UNIT_KERJA H
                        WHERE A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID(+) AND
                              A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID(+) AND
                              A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID(+) AND
                              A.PAKET_JENIS_ID = E.PAKET_JENIS_ID(+) AND
                              TO_CHAR(A.USER_LOGIN_ID) = F.NIPP (+) AND
                              A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID(+) AND
                              A.UNIT_KERJA_ID = H.UNIT_KERJA_ID(+) AND 
                              EXISTS(SELECT 1 FROM PAKET_PIHAK_LAIN X WHERE X.PAKET_ID = A.PAKET_ID AND X.STATUS = 1 AND X.USER_LOGIN_ID = '".$user_id."')                      
                        ) WHERE 1 = 1
            "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ORDER BY TANGGAL_TAHAP DESC,PAKET_ID DESC";

		$this->query = $str;
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }	
    
	function selectByParamsMonitoringCetak($paramsArray=array(),$limit=-1,$from=-1, $statement='', $tahun='')
	{
		$str = "SELECT * FROM (
                            SELECT 
                            PAKET_ID, A.PAKET_ID ID_PAKET,
                            B.NAMA METODE_LELANG, A.NAMA, E.NAMA PAKET_JENIS,  
                            A.LOKASI, A.TANGGAL, A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI, 
                            A.NILAI_OWNER_ESTIMATE, A.USER_LOGIN_ID,
                            COALESCE((SELECT tanggal_awal FROM PAKET_TAHAP WHERE PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang' and rownum =1 ),A.TANGGAL) tanggal_tahap
                            FROM    
                                PAKET A,
                                PAKET_METODE_LELANG B, 
                                PAKET_METODE_KUALIFIKASI C, 
                                PAKET_METODE_EVALUASI D, 
                                PAKET_JENIS E,
                                REKANAN_KUALIFIKASI G,
                                UNIT_KERJA H
                            WHERE 
                                A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID(+) AND
                                A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID(+) AND
                                A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID(+) AND
                                A.PAKET_JENIS_ID = E.PAKET_JENIS_ID(+) AND
                                A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID(+) AND
                                A.UNIT_KERJA_ID = H.UNIT_KERJA_ID(+)
                          )
                          WHERE to_char(TANGGAL_TAHAP, 'YYYY') = '".$tahun."' 
	  "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ORDER BY TANGGAL_TAHAP DESC,PAKET_ID DESC";

		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
    }
						  
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				SELECT 
					PAKET_ID, PAKET_METODE_LELANG_ID, PAKET_METODE_KUALIFIKASI_ID, 
					   PAKET_METODE_EVALUASI_ID, PAKET_JENIS_ID, USER_LOGIN_ID, 
					   REKANAN_KUALIFIKASI_ID, NAMA, URAIAN, 
					   LOKASI, ALAMAT, TELEPON, 
					   FAX, EMAIL, SYARAT, 
					   TANGGAL, PUBLISH_PAKET, PUBLISH_PAKET_TANGGAL, 
					   PUBLISH_PEMENANG, PUBLISH_PEMENANG_TANGGAL, NILAI, 
					   NILAI_OWNER_ESTIMATE, PASS_GRADE
					FROM PAKET;
				    WHERE PAKET_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY NAMA ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","PAKET_METODE_EVALUASI_ID"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getPaketAktif($rekanan_id, $paket_id, $state='')
	{
		$str = "SELECT 1 ROWCOUNT
			  	FROM REKANAN_BIDANG_USAHA A
			 	WHERE REKANAN_ID = '".$rekanan_id."'
			   	AND EXISTS (SELECT 1
                FROM PAKET_BIDANG_USAHA X
                WHERE X.BIDANG_USAHA_ID = A.BIDANG_USAHA_ID AND PAKET_ID = '".$paket_id."') ".$state; 
		//echo $str;
		$this->query = $str;
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }

    function getPaketPendaftaran($paket_id)
	{
		// kadang bisa ini DD/MM/YYYY HH24:MI:SS
		// kadang bisa ini yyyy/mm/dd hh:mi:ss		
		//$str = "SELECT 1 ROWCOUNT FROM PAKET_TAHAP WHERE (TO_DATE(CURRENT_DATE, 'yyyy/mm/dd hh:mi:ss') BETWEEN TO_DATE(TANGGAL_AWAL, 'yyyy/mm/dd hh:mi:ss') AND TO_DATE(TANGGAL_AKHIR, 'yyyy/mm/dd hh:mi:ss') OR TO_DATE(TANGGAL_AWAL, 'yyyy/mm/dd hh:mi:ss') = TO_DATE(CURRENT_DATE, 'yyyy/mm/dd hh:mi:ss'))AND URUT = 3 AND PAKET_ID = '".$paket_id."' "; 
		$str = "SELECT 1 ROWCOUNT FROM PAKET_TAHAP WHERE (CURRENT_DATE BETWEEN TANGGAL_AWAL AND TANGGAL_AKHIR OR 
				TO_DATE(TANGGAL_AWAL, 'yyyy/mm/dd hh:mi:ss') = TO_DATE(CURRENT_DATE, 'yyyy/mm/dd hh:mi:ss')) AND URUT = 3 AND PAKET_ID = '".$paket_id."' "; 
	
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
		{
			echo $this->errorMsg;
			return 0; 
		}
    }

    function getPaketRekeningKoran($rekanan_id, $bulan)
	{ 
		$str = "select count(*) ROWCOUNT from (
                        select distinct bulan,tahun from
                        REKANAN_REKENING_KORAN A 
                                WHERE REKANAN_ID = {$rekanan_id} AND BULAN || TAHUN IN ({$bulan})
                        ) "; 
	
		$this->select($str); 
		//echo $str;
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }
	
	function getPaketPajakRekanan($rekanan_id, $bulan, $tipe)
	{
		$str = "select count(*) ROWCOUNT from (
                            select distinct bulan,tahun from
                            REKANAN_PAJAK A 
                            WHERE REKANAN_ID = {$rekanan_id} 
                            AND BULAN || TAHUN IN ({$bulan})
                            AND TIPE = {$tipe}
                            AND NOMOR IS NOT NULL
                        )"; 
	
		$this->select($str); 
//		echo $str;
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }
	
    function getPaketPajak($rekanan_id, $bulan, $tahun)
	{
		$str = "SELECT SUM(ROWCOUNT) ROWCOUNT FROM 
				(
				SELECT 1 ROWCOUNT FROM REKANAN_PAJAK A 
				WHERE REKANAN_ID = ".$rekanan_id." AND TIPE = 1 AND (TAHUN = ".$tahun." OR TAHUN = ".($tahun-1).")
				UNION ALL
				SELECT COUNT(REKANAN_PAJAK_ID) ROWCOUNT FROM REKANAN_PAJAK A 
				WHERE REKANAN_ID = ".$rekanan_id." AND TIPE = 2 AND BULAN || TAHUN IN(".$bulan.") AND NOMOR IS NOT NULL
				UNION ALL
				SELECT COUNT(REKANAN_PAJAK_ID) ROWCOUNT FROM REKANAN_PAJAK A 
				WHERE REKANAN_ID = ".$rekanan_id." AND TIPE = 3 AND BULAN || TAHUN IN(".$bulan.") AND NOMOR IS NOT NULL) A
              "; 
	
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }	
	
    function getPaketPengalaman($paket_id, $rekanan_id)
	{
		$str = "SELECT COUNT(REKANAN_PENGALAMAN_BIDANG_ID) AS ROWCOUNT FROM REKANAN_PENGALAMAN_BIDANG A, REKANAN_PENGALAMAN B
				WHERE A.REKANAN_PENGALAMAN_ID = B.REKANAN_PENGALAMAN_ID AND REKANAN_ID = ".$rekanan_id."
					  AND EXISTS (SELECT 1
					  FROM PAKET_BIDANG_USAHA X
					  WHERE X.BIDANG_USAHA_ID = A.BIDANG_USAHA_ID AND PAKET_ID = ".$paket_id.")  "; 
	
		$this->select($str); 
		
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }

    function getPaketMengikuti($rekanan_id, $paket_id)
	{
		$str = "SELECT 1 ROWCOUNT
				  FROM PAKET_REKANAN A
				WHERE REKANAN_ID = '".$rekanan_id."' AND PAKET_ID = '".$paket_id."' AND A.TANGGAL_DAFTAR IS NOT NULL"; 
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }

    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "select COUNT(*) AS ROWCOUNT 
                        from ( SELECT a.PAKET_ID,A.NAMA,A.PUBLISH_PAKET,H.UNIT_KERJA_ID,
                                        COALESCE((SELECT tanggal_awal FROM PAKET_TAHAP WHERE PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang' and rownum =1 ),A.TANGGAL) tanggal_tahap 
                                FROM PAKET A, 
                                PAKET_METODE_LELANG B, 
                                PAKET_METODE_KUALIFIKASI C, 
                                PAKET_METODE_EVALUASI D, 
                                PAKET_JENIS E,
                                REKANAN_KUALIFIKASI G, 
                                UNIT_KERJA H 
                            WHERE A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID(+) 
                                AND A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID(+) 
                                AND A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID(+) 
                                AND A.PAKET_JENIS_ID = E.PAKET_JENIS_ID(+)
                                AND A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID(+) 
                                AND A.UNIT_KERJA_ID = H.UNIT_KERJA_ID(+) ) where 1 = 1 "; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= $statement;
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }

    function getCountByParamsPaketRekanan($paramsArray=array(), $rekanan_id='', $statement='')
	{
		$str = "SELECT COUNT(PAKET_ID) AS ROWCOUNT FROM
                (
                            SELECT 
                           PAKET_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI, 
                           D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS,
                           G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN, 
                           A.LOKASI, A.ALAMAT, A.TELEPON, 
                           A.FAX, A.EMAIL, A.SYARAT, 
                           A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL, 
                           A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI, 
                           A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE,H.NAMA UNIT_KERJA, A.ALASAN, A.UNIT_KERJA_ID,
                           COALESCE((SELECT tanggal_awal FROM PAKET_TAHAP WHERE PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang' and rownum =1 ),A.TANGGAL) tanggal_tahap
                        FROM    PAKET A,
                                PAKET_METODE_LELANG B, 
                                PAKET_METODE_KUALIFIKASI C, 
                                PAKET_METODE_EVALUASI D, 
                                PAKET_JENIS E,
                                REKANAN_KUALIFIKASI G,
                                UNIT_KERJA H
                        WHERE A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID(+) AND
                              A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID(+) AND
                              A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID(+) AND
                              A.PAKET_JENIS_ID = E.PAKET_JENIS_ID(+) AND
                              A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID(+) AND
                              A.UNIT_KERJA_ID = H.UNIT_KERJA_ID(+) AND
                              A.PUBLISH_PAKET = 1                               
                    UNION ALL
                        SELECT 
                           PAKET_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI, 
                           D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS,
                           G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN, 
                           A.LOKASI, A.ALAMAT, A.TELEPON, 
                           A.FAX, A.EMAIL, A.SYARAT, 
                           A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL, 
                           A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI, 
                           A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE, H.NAMA UNIT_KERJA, A.ALASAN, A.UNIT_KERJA_ID,
                           COALESCE((SELECT tanggal_awal FROM PAKET_TAHAP WHERE PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang' and rownum =1 ),A.TANGGAL) tanggal_tahap
                        FROM    PAKET A,
                                PAKET_METODE_LELANG B, 
                                PAKET_METODE_KUALIFIKASI C, 
                                PAKET_METODE_EVALUASI D, 
                                PAKET_JENIS E, 
                                REKANAN_KUALIFIKASI G,
                                UNIT_KERJA H
                        WHERE A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID(+) AND
                              A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID(+) AND
                              A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID(+) AND
                              A.PAKET_JENIS_ID = E.PAKET_JENIS_ID(+) AND
                              A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID(+) AND
                              A.UNIT_KERJA_ID = H.UNIT_KERJA_ID(+) AND 
                              EXISTS(SELECT 1 FROM PAKET_REKANAN X WHERE X.PAKET_ID = A.PAKET_ID AND X.TANGGAL_UNDANG IS NOT NULL AND X.REKANAN_ID = '".$rekanan_id."')                      
                    ) WHERE 1 = 1 
                "; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
                $str .= $statement;
//                $this->query = $str;
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }

    function getCountByParamsMonitoring($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(PAKET_ID) AS ROWCOUNT FROM PAKET A WHERE PAKET_ID IS NOT NULL ".$statement; 
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

    function getPaketId($paramsArray=array(), $statement='')
	{
		$str = "SELECT PAKET_ID FROM PAKET A WHERE PAKET_ID IS NOT NULL ".$statement; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("PAKET_ID"); 
		else 
			return 0; 
    }	
	
    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(PAKET_ID) AS ROWCOUNT FROM PAKET WHERE PAKET_ID IS NOT NULL "; 
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