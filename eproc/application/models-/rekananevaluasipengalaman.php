<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class RekananEvaluasiPengalaman extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function RekananEvaluasiPengalaman()
	{
      $this->Entity(); 
    }
	
	function insert()
	{ 
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_EVAL_PENGALAMAN_ID", $this->getNextId("REKANAN_EVAL_PENGALAMAN_ID","REKANAN_EVAL_PENGALAMAN")); 

		$str = "INSERT INTO REKANAN_EVAL_PENGALAMAN (
				   REKANAN_EVAL_PENGALAMAN_ID, PAKET_REKANAN_ID, REKANAN_PENGALAMAN_ID) 
				VALUES (
				  ".$this->getField("REKANAN_EVAL_PENGALAMAN_ID").",
				  ".$this->getField("PAKET_REKANAN_ID").",
				  '".$this->getField("REKANAN_PENGALAMAN_ID")."'
				)"; 
			// echo $str; die();
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_EVAL_PENGALAMAN SET
				  REKANAN_PENGALAMAN_ID = '".$this->getField("REKANAN_PENGALAMAN_ID")."'
				WHERE PAKET_REKANAN_ID = '".$this->getField("PAKET_REKANAN_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updatePenilaianPengalaman()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_EVAL_PENGALAMAN SET
				  BP_KESESUAIAN = '".$this->getField("BP_KESESUAIAN")."',
				  BP_KESESUAIAN_NILAI = '".$this->getField("BP_KESESUAIAN_NILAI")."',
				  BP_KESESUAIAN_TOTAL = '".$this->getField("BP_KESESUAIAN_TOTAL")."'
				WHERE REKANAN_EVAL_PENGALAMAN_ID = '".$this->getField("REKANAN_EVAL_PENGALAMAN_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
		
    function updateByField()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_EVAL_PENGALAMAN A SET
				  ".$this->getField("FIELD")." = ".$this->getField("FIELD_VALUE")."
				WHERE PAKET_REKANAN_ID = ".$this->getField("PAKET_REKANAN_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }	
	
	function delete()
	{
        $str = "DELETE FROM REKANAN_EVAL_PENGALAMAN
                WHERE 
                  REKANAN_EVAL_PENGALAMAN_ID = '".$this->getField("REKANAN_EVAL_PENGALAMAN_ID")."'"; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

	function deletePaketRekanan()
	{
        $str = "DELETE FROM REKANAN_EVAL_PENGALAMAN
                WHERE 
                  PAKET_REKANAN_ID = '".$this->getField("PAKET_REKANAN_ID")."'"; 
				  
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
		$str = "SELECT REKANAN_EVAL_PENGALAMAN_ID, PAKET_REKANAN_ID, A.REKANAN_PENGALAMAN_ID, B.NAMA, B.KONTRAK_NILAI, MEMENUHI_SYARAT, NILAI
				FROM REKANAN_EVAL_PENGALAMAN A, REKANAN_PENGALAMAN B
				WHERE A.REKANAN_PENGALAMAN_ID = B.REKANAN_PENGALAMAN_ID AND
				REKANAN_EVAL_PENGALAMAN_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY B.NAMA ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParams2($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT *
				FROM REKANAN_EVAL_PENGALAMAN  
				WHERE
				REKANAN_EVAL_PENGALAMAN_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY B.NAMA ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
	

 	function selectByParamsEvaluasi($paramsArray=array(),$limit=-1,$from=-1, $paket_id='', $order="")
	{
//		$str = "
//				SELECT A.*, BP_NILAI * (BP_PROSENTASE / 100) BP, NK_NILAI * (NK_PROSENTASE / 100) NK, STBU_NILAI * (STBU_PROSENTASE / 100) STBU FROM
//				(
//				SELECT REKANAN_EVAL_PENGALAMAN_ID, PAKET_REKANAN_ID, A.REKANAN_PENGALAMAN_ID, B.NAMA, B.KONTRAK_NILAI,
//					  CASE WHEN COALESCE((SELECT 1 FROM PAKET_BIDANG_USAHA X WHERE X.PAKET_ID = ".$paket_id." AND X.BIDANG_USAHA_ID = C.BIDANG_USAHA_ID), 0) = 1 THEN BP_SUB_SAMA_PERSEN WHEN COALESCE((SELECT 1 FROM PAKET_BIDANG_USAHA X WHERE X.PAKET_ID = ".$paket_id." AND SUBSTR(X.BIDANG_USAHA_ID, 0, LENGTH(X.BIDANG_USAHA_ID) - 2) = SUBSTR(C.BIDANG_USAHA_ID, 0, LENGTH(C.BIDANG_USAHA_ID) - 2)), 0) = 1 THEN BP_SUB_BEDA_PERSEN ELSE 0 END BP_PROSENTASE,    
//					   COALESCE(BP_NILAI, 0) BP_NILAI,            
//					   COALESCE((SELECT NK1_PERSEN FROM PAKET_EVAL_PENGALAMAN WHERE PAKET_ID = ".$paket_id." AND B.KONTRAK_NILAI >= NK1_RP), 0)  +
//					   COALESCE((SELECT NK2_PERSEN FROM PAKET_EVAL_PENGALAMAN WHERE PAKET_ID = ".$paket_id." AND B.KONTRAK_NILAI BETWEEN NK2_RPMIN AND NK2_RPMAX), 0)  +
//					   COALESCE((SELECT NK3_PERSEN FROM PAKET_EVAL_PENGALAMAN WHERE PAKET_ID = ".$paket_id." AND B.KONTRAK_NILAI <= NK3_RP), 0) NK_PROSENTASE,
//					   COALESCE(NK_NILAI, 0) NK_NILAI,
//					   CASE WHEN DECODE(B.KONTRAK_JO, 100, 1, 0) = 1 THEN STBU_UTAMA_PERSEN ELSE STBU_SUB_PERSEN END STBU_PROSENTASE,
//					   STBU_NILAI, A.NILAI,
//					   AMBIL_BIDANG_USAHA_NAMA(C.BIDANG_USAHA_ID) BIDANG_USAHA, DECODE(B.KONTRAK_JO, 100, 'Kontraktor Utama', 'Sub Kontraktor') JO, MEMENUHI_SYARAT, NILAI_MAKSIMUM
//					   FROM REKANAN_EVAL_PENGALAMAN A, REKANAN_PENGALAMAN B, REKANAN_PENGALAMAN_BIDANG C, PAKET_EVAL_PENGALAMAN D  
//					   WHERE A.REKANAN_PENGALAMAN_ID = B.REKANAN_PENGALAMAN_ID AND                
//					   B.REKANAN_PENGALAMAN_ID = C.REKANAN_PENGALAMAN_ID AND 
//					   D.PAKET_ID = ".$paket_id." AND
//					   REKANAN_EVAL_PENGALAMAN_ID IS NOT NULL) A WHERE 1 = 1			
//		";
/*

*/
            $str = "
                    SELECT A.*, ROUND(COALESCE(BP_KESESUAIAN_TOTAL, BP_NILAI * (BP_PROSENTASE / 100)), 1) BP,
       ROUND(NK_NILAI * (NK_PROSENTASE / 100), 2) NK,
       ROUND(STBU_NILAI * (STBU_PROSENTASE / 100), 1) STBU,
       COALESCE(BP_KESESUAIAN, 
       CASE WHEN BP_PROSENTASE = BP_SUB_SAMA_PERSEN THEN 'S' 
            WHEN BP_PROSENTASE = BP_SUB_BEDA_PERSEN THEN 'R' 
            ELSE 'TS'
       END) BP_KESESUAIAN_COMPARE,
       COALESCE(BP_KESESUAIAN_NILAI, BP_PROSENTASE) BP_KESESUAIAN_NILAI_COMPARE
  FROM (
		  SELECT REKANAN_EVAL_PENGALAMAN_ID, C.PAKET_REKANAN_ID, BP_SUB_SAMA_PERSEN, BP_SUB_BEDA_PERSEN, BP_KESESUAIAN, BP_KESESUAIAN_NILAI, BP_KESESUAIAN_TOTAL, NILAI_RATA, NILAI_PROSENTASE, 
          A.REKANAN_PENGALAMAN_ID, B.NAMA, B.KONTRAK_NILAI, 
                        COALESCE(BP_NILAI, 0) BP_NILAI, 
                        CASE 
                        WHEN B.KONTRAK_NILAI >= NK1_RP THEN NK1_PERSEN 
                        WHEN B.KONTRAK_NILAI BETWEEN NK2_RPMIN AND NK2_RPMAX THEN NK2_PERSEN
                        WHEN B.KONTRAK_NILAI <= NK3_RP THEN NK3_PERSEN END NK_PROSENTASE, 
                        COALESCE(NK_NILAI, 0) NK_NILAI, 
                        	CASE WHEN (CASE WHEN B.KONTRAK_JO = 100 THEN 1
										ELSE 0 END) = 1 THEN
                            STBU_UTAMA_PERSEN 
                        ELSE 
                            STBU_SUB_PERSEN 
                        END STBU_PROSENTASE, STBU_NILAI, A.NILAI,
						 CASE WHEN B.KONTRAK_JO = 100 THEN 'Kontraktor Utama'
								ELSE 'Sub Kontraktor' END JO, MEMENUHI_SYARAT, 
                        NILAI_MAKSIMUM,
                        CASE
                          WHEN COALESCE ((SELECT COUNT (1)
                                       FROM REKANAN_PENGALAMAN_BIDANG X
                                      WHERE EXISTS(SELECT 1 FROM PAKET_BIDANG_USAHA Y WHERE X.BIDANG_USAHA_ID = Y.BIDANG_USAHA_ID AND Y.PAKET_ID = C.PAKET_ID)
                                        AND X.REKANAN_PENGALAMAN_ID =
                                                               B.REKANAN_PENGALAMAN_ID),
                                    0
                                   ) >= 1
                             THEN BP_SUB_SAMA_PERSEN
                          WHEN COALESCE ((SELECT COUNT (1)
                                       FROM REKANAN_PENGALAMAN_BIDANG X
                                       WHERE EXISTS(SELECT 1 FROM PAKET_BIDANG_USAHA Y WHERE SUBSTR (X.BIDANG_USAHA_ID,
                                                    0,
                                                    LENGTH (X.BIDANG_USAHA_ID) - 2
                                                   ) =
                                               SUBSTR (Y.BIDANG_USAHA_ID,
                                                       0,
                                                       LENGTH (Y.BIDANG_USAHA_ID) - 2
                                                      ) AND Y.PAKET_ID = C.PAKET_ID)
                                        AND X.REKANAN_PENGALAMAN_ID =
                                                               B.REKANAN_PENGALAMAN_ID),                              
                                    0
                                   ) = 1
                             THEN BP_SUB_BEDA_PERSEN
                          ELSE 0
                       END BP_PROSENTASE
                        FROM REKANAN_EVAL_PENGALAMAN A, 
                            REKANAN_PENGALAMAN B,
                            PAKET_REKANAN C,  
                            PAKET_EVAL_PENGALAMAN D
                        WHERE A.REKANAN_PENGALAMAN_ID = B.REKANAN_PENGALAMAN_ID AND
                                A.PAKET_REKANAN_ID = C.PAKET_REKANAN_ID AND
                                C.PAKET_ID = D.PAKET_ID AND
                                REKANAN_EVAL_PENGALAMAN_ID IS NOT NULL
                    ) A 
                    WHERE 1 = 1 
                    ";
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
		$str .= $statement." ".$order;
		return $this->selectLimit($str,$limit,$from); 
    }   
	
 	function selectByParamsEvaluasiPerhitungan($paramsArray=array(),$limit=-1,$from=-1, $order="")
	{
            $str = "
					SELECT PAKET_REKANAN_ID, NILAI_RATA, PROSENTASE NILAI_PROSENTASE, ROUND((NILAI_RATA * PROSENTASE) / 100, 2) NILAI
					FROM
					(
					SELECT A.PAKET_REKANAN_ID, NILAI_RATA,
						   (SELECT CASE 
									WHEN JUMLAH >= MAX(JUMLAH_PENGALAMAN_A) THEN MAX(PROSENTASE_PENGALAMAN_A)
									WHEN JUMLAH >= MAX(JUMLAH_PENGALAMAN_B) THEN MAX(PROSENTASE_PENGALAMAN_B)
									WHEN JUMLAH >= MAX(JUMLAH_PENGALAMAN_C) THEN MAX(PROSENTASE_PENGALAMAN_C)
									WHEN JUMLAH >= MAX(JUMLAH_PENGALAMAN_D) THEN MAX(PROSENTASE_PENGALAMAN_D)  
									ELSE 0                
									END FROM PAKET_EVAL_PENGALAMAN X WHERE X.PAKET_ID = A.PAKET_ID GROUP BY X.PAKET_ID) PROSENTASE  
					FROM
					(
					SELECT A.PAKET_REKANAN_ID, 
						   ROUND((SUM(COALESCE(BP_KESESUAIAN_TOTAL, BP_NILAI * (BP_PROSENTASE / 100))) + SUM(ROUND(NK_NILAI * (NK_PROSENTASE / 100), 2)) + SUM(STBU_NILAI * (STBU_PROSENTASE / 100))) / COUNT(1), 2) NILAI_RATA,
						   COUNT(1) JUMLAH, MAX(PAKET_ID) PAKET_ID
					  FROM (
					SELECT C.PAKET_ID, REKANAN_EVAL_PENGALAMAN_ID, C.PAKET_REKANAN_ID, BP_SUB_SAMA_PERSEN, BP_SUB_BEDA_PERSEN, BP_KESESUAIAN, BP_KESESUAIAN_NILAI, BP_KESESUAIAN_TOTAL, NILAI_RATA, NILAI_PROSENTASE, 
							  A.REKANAN_PENGALAMAN_ID, B.NAMA, B.KONTRAK_NILAI, 
											COALESCE(BP_NILAI, 0) BP_NILAI, 
											CASE 
											WHEN B.KONTRAK_NILAI >= NK1_RP THEN NK1_PERSEN 
											WHEN B.KONTRAK_NILAI BETWEEN NK2_RPMIN AND NK2_RPMAX THEN NK2_PERSEN
											WHEN B.KONTRAK_NILAI <= NK3_RP THEN NK3_PERSEN END NK_PROSENTASE, 
											COALESCE(NK_NILAI, 0) NK_NILAI, 
											CASE WHEN (CASE WHEN B.KONTRAK_JO = 100 THEN 1
													ELSE 0 END) = 1THEN 
												STBU_UTAMA_PERSEN 
											ELSE 
												STBU_SUB_PERSEN 
											END STBU_PROSENTASE, STBU_NILAI, A.NILAI,
											CASE WHEN B.KONTRAK_JO = 100 THEN 'Kontraktor Utama'
												ELSE 'Sub Kontraktor' END JO , MEMENUHI_SYARAT, 
											NILAI_MAKSIMUM,
											CASE
											  WHEN COALESCE ((SELECT COUNT (1)
														   FROM REKANAN_PENGALAMAN_BIDANG X
														  WHERE EXISTS(SELECT 1 FROM PAKET_BIDANG_USAHA Y WHERE X.BIDANG_USAHA_ID = Y.BIDANG_USAHA_ID AND Y.PAKET_ID = C.PAKET_ID)
															AND X.REKANAN_PENGALAMAN_ID =
																				   B.REKANAN_PENGALAMAN_ID),
														0
													   ) >= 1
												 THEN BP_SUB_SAMA_PERSEN
											  WHEN COALESCE ((SELECT COUNT (1)
														   FROM REKANAN_PENGALAMAN_BIDANG X
														   WHERE EXISTS(SELECT 1 FROM PAKET_BIDANG_USAHA Y WHERE SUBSTR (X.BIDANG_USAHA_ID,
																		0,
																		LENGTH (X.BIDANG_USAHA_ID) - 2
																	   ) =
																   SUBSTR (Y.BIDANG_USAHA_ID,
																		   0,
																		   LENGTH (Y.BIDANG_USAHA_ID) - 2
																		  ) AND Y.PAKET_ID = C.PAKET_ID)
															AND X.REKANAN_PENGALAMAN_ID =
																				   B.REKANAN_PENGALAMAN_ID),                              
														0
													   ) = 1
												 THEN BP_SUB_BEDA_PERSEN
											  ELSE 0
										   END BP_PROSENTASE
											FROM REKANAN_EVAL_PENGALAMAN A, 
												REKANAN_PENGALAMAN B,
												PAKET_REKANAN C,  
												PAKET_EVAL_PENGALAMAN D
											WHERE A.REKANAN_PENGALAMAN_ID = B.REKANAN_PENGALAMAN_ID AND
													A.PAKET_REKANAN_ID = C.PAKET_REKANAN_ID AND
													C.PAKET_ID = D.PAKET_ID AND
													REKANAN_EVAL_PENGALAMAN_ID IS NOT NULL
													)  A GROUP BY A.PAKET_REKANAN_ID
					) A
					) A	
					WHERE 1 = 1			
                    ";
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
		$str .= $statement." ".$order;
		return $this->selectLimit($str,$limit,$from); 
    }   
		 
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT REKANAN_EVAL_PENGALAMAN_ID, NAMA
				FROM REKANAN_EVAL_PENGALAMAN WHERE REKANAN_EVAL_PENGALAMAN_ID IS NOT NULL"; 
		
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
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
	
    function getRekananPengalamanId($paramsArray=array())
	{
		$str = "SELECT REKANAN_PENGALAMAN_ID AS ROWCOUNT FROM REKANAN_EVAL_PENGALAMAN WHERE REKANAN_EVAL_PENGALAMAN_ID IS NOT NULL "; 
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

    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(REKANAN_EVAL_PENGALAMAN_ID) AS ROWCOUNT FROM REKANAN_EVAL_PENGALAMAN WHERE REKANAN_EVAL_PENGALAMAN_ID IS NOT NULL "; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str;
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }

    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(REKANAN_EVAL_PENGALAMAN_ID) AS ROWCOUNT FROM REKANAN_EVAL_PENGALAMAN WHERE REKANAN_EVAL_PENGALAMAN_ID IS NOT NULL "; 
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