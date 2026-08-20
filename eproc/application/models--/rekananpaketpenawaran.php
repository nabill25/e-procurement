<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class RekananPaketPenawaran extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function RekananPaketPenawaran()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_PAKET_PENAWARAN_ID", $this->getNextId("REKANAN_PAKET_PENAWARAN_ID","REKANAN_PAKET_PENAWARAN")); 

		$str = " 
				INSERT INTO REKANAN_PAKET_PENAWARAN (
					REKANAN_PAKET_PENAWARAN_ID, 
					PAKET_PENAWARAN_ID, 
					PAKET_REKANAN_ID, 
					DELIVERY_DATE, 
					QUANTITY, 
					UNIT_PRICE,
					JUMLAH,
					BOQ,
					LAST_CREATE_DATE, BIAYA_KIRIM, LAST_CREATE_USER)
				VALUES ( '".$this->getField("REKANAN_PAKET_PENAWARAN_ID")."', 
					'".$this->getField("PAKET_PENAWARAN_ID")."',
					'".$this->getField("PAKET_REKANAN_ID")."', 
					".$this->getField("DELIVERY_DATE").", 
					'".$this->getField("QUANTITY")."', 
					".$this->getField("UNIT_PRICE").", 
					'".$this->getField("JUMLAH")."', 
					'".$this->getField("BOQ")."',
					NOW(),
					".$this->getField("BIAYA_KIRIM").",
					'".$this->getField("CREATED_BY")."' )
		"; 	
		$this->query = $str;
		
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM REKANAN_PAKET_PENAWARAN
                WHERE 
                  PAKET_REKANAN_ID = '".$this->getField("PAKET_REKANAN_ID")."' "; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

	function updateKoreksi()
	{
        $str = " UPDATE REKANAN_PAKET_PENAWARAN
					SET 
					UNIT_PRICE_KOREKSI = '".$this->getField("UNIT_PRICE_KOREKSI")."',
					JUMLAH_KOREKSI = '".$this->getField("JUMLAH_KOREKSI")."',
					LAST_CREATE_USER = ".$this->USER_LOGIN_ID.",
					LAST_CREATE_DATE = CURRENT_TIMESTAMP
				  WHERE 
          PAKET_REKANAN_ID = '".$this->getField("PAKET_REKANAN_ID")."' AND
				  PAKET_PENAWARAN_ID = '".$this->getField("PAKET_PENAWARAN_ID")."' "; 
				  
		$this->query = $str;
		// echo $str; die();
    return $this->execQuery($str);
  }
	
	function updateKoreksiBidding()
	{
        $str = " UPDATE REKANAN_PAKET_PENAWARAN
					SET 
					UNIT_PRICE_KOREKSI = '".$this->getField("UNIT_PRICE_KOREKSI")."',
					JUMLAH_KOREKSI = (QUANTITY * '".$this->getField("UNIT_PRICE_KOREKSI")."'),
					LAST_CREATE_USER = ".$this->USER_LOGIN_ID.",
					LAST_CREATE_DATE = CURRENT_TIMESTAMP
				  WHERE 
                  PAKET_REKANAN_ID = '".$this->getField("PAKET_REKANAN_ID")."' AND
				  REKANAN_PAKET_PENAWARAN_ID = '".$this->getField("REKANAN_PAKET_PENAWARAN_ID")."' "; 
				  
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
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=" ORDER BY UNIT_PRICE ASC ")
	{
		$str = "
				SELECT 
					REKANAN_PAKET_PENAWARAN_ID, 
					PAKET_PENAWARAN_ID, 
					PAKET_REKANAN_ID, 
					DELIVERY_DATE, 
					--BULAN, 
					QUANTITY, 
					UNIT_PRICE, 
					JUMLAH,
					BOQ,
					UNIT_PRICE_KOREKSI, JUMLAH_KOREKSI,
					LAST_CREATE_DATE, BIAYA_KIRIM
				FROM REKANAN_PAKET_PENAWARAN
				WHERE REKANAN_PAKET_PENAWARAN_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ".$order;
		
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsRekanan($paketRekananId, $paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=' ORDER BY A.PAKET_PENAWARAN_ID ASC ')
	{
		$str = "
			SELECT 
			A.PAKET_PENAWARAN_ID, PAKET_ID, ITEM, 
			   TOTAL_COST, SATUAN, A.QUANTITY, 
			   B.UNIT_PRICE, 
			   TO_CHAR(B.DELIVERY_DATE, 'YYYY-MM-DD') DELIVERY_DATE, B.JUMLAH, 
			   A.LAST_CREATE_USER, A.LAST_CREATE_DATE,
			   A.BOQ, A.BOQ_KOLOM, B.BOQ BOQ_REKANAN, A.ITEM_NUMBER, A.LOKASI, A.OE, A.ITEM_PARENT, A.ITEM_CHILD, A.BIAYA_KIRIM,
			   B.BIAYA_KIRIM BIAYA_KIRIM_REKANAN, A.BOQ_FILE,
			   B.REKANAN_PAKET_PENAWARAN_ID
			FROM PAKET_PENAWARAN A
			LEFT JOIN REKANAN_PAKET_PENAWARAN B ON A.PAKET_PENAWARAN_ID = B.PAKET_PENAWARAN_ID AND B.PAKET_REKANAN_ID = '".$paketRekananId."'	
			WHERE 1 = 1	
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		
		
		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsRekananNegosiasi($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=' ORDER BY A.PAKET_PENAWARAN_ID ASC ')
	{
		$str = "
			SELECT 
			A.PAKET_PENAWARAN_ID, A.PAKET_ID, ITEM, 
			   TOTAL_COST, A.SATUAN, A.QUANTITY, A.LOKASI,
			   B.UNIT_PRICE, 
			   TO_CHAR(B.DELIVERY_DATE, 'YYYY-MM-DD') DELIVERY_DATE, B.JUMLAH,
			   C.UNIT_PRICE UNIT_PRICE_NEGOSIASI
			FROM PAKET_PENAWARAN A
			INNER JOIN REKANAN_PAKET_PENAWARAN B ON A.PAKET_PENAWARAN_ID = B.PAKET_PENAWARAN_ID AND B.PAKET_REKANAN_ID = (SELECT PAKET_REKANAN_ID FROM PAKET_REKANAN X WHERE X.PAKET_ID = A.PAKET_ID AND X.REKANAN_ID = A.REKANAN_ID_PEMENANG)	
			INNER JOIN PAKET_NEGOSIASI C ON A.PAKET_PENAWARAN_ID = C.PAKET_PENAWARAN_ID
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
	
	 function selectByParamsRekananBidding( $paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=' ORDER BY A.PAKET_PENAWARAN_ID ASC ')
	{
		$str = "
			SELECT 
			A.PAKET_PENAWARAN_ID, PAKET_ID, ITEM, 
			   TOTAL_COST, SATUAN, A.QUANTITY, 
			   B.UNIT_PRICE, 
			   TO_CHAR(B.DELIVERY_DATE, 'YYYY-MM-DD') DELIVERY_DATE, B.JUMLAH, 
			   A.LAST_CREATE_USER, A.LAST_CREATE_DATE,
			   A.BOQ, A.BOQ_KOLOM, B.BOQ BOQ_REKANAN, A.ITEM_NUMBER, A.LOKASI, A.OE, A.ITEM_PARENT, A.ITEM_CHILD, A.BIAYA_KIRIM,
			   B.BIAYA_KIRIM BIAYA_KIRIM_REKANAN, A.BOQ_FILE,
			   B.REKANAN_PAKET_PENAWARAN_ID, B.UNIT_PRICE_KOREKSI, B.JUMLAH_KOREKSI
			FROM PAKET_PENAWARAN A
			LEFT JOIN REKANAN_PAKET_PENAWARAN B ON A.PAKET_PENAWARAN_ID = B.PAKET_PENAWARAN_ID
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
	
    function selectByParamsRekananNegosiasiV2($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=' ORDER BY A.ITEM_PARENT::NUMERIC ASC ')
	{
		$str = "
			SELECT A.PAKET_ID, A.ITEM_PARENT, B.ITEM_NUMBER, A.JUMLAH_AWAL, A.JUMLAH_AKHIR, A.BIAYA_KIRIM_AWAL, A.BIAYA_KIRIM,
			A.JUMLAH_AWAL + A.BIAYA_KIRIM_AWAL TOTAL_AWAL, A.JUMLAH_AKHIR + A.BIAYA_KIRIM TOTAL_AKHIR FROM PAKET_NEGOSIASI_LOT A
			INNER JOIN PAKET_PENAWARAN B ON A.PAKET_ID = B.PAKET_ID AND A.ITEM_PARENT = B.ITEM_PARENT
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
		
	function selectByParamsCalonRekanan($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=" ORDER BY A.JUMLAH ASC ")
	{
		$str = "
				SELECT C.NAMA, A.JUMLAH, A.BIAYA_KIRIM, D.NAMA ||' ' || C.NAMA NAMA_PERUSAHAAN
				 FROM REKANAN_PAKET_PENAWARAN_LOT A 
				LEFT JOIN PAKET_REKANAN B ON A.PAKET_REKANAN_ID=B.PAKET_REKANAN_ID 
				LEFT JOIN REKANAN C ON B.REKANAN_ID = C.REKANAN_ID
				LEFT JOIN REKANAN_TIPE D ON C.REKANAN_TIPE_ID=D.REKANAN_TIPE_ID
				WHERE 1=1
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ".$order;
		
		return $this->selectLimit($str,$limit,$from); 
    }
	
     function selectByParamsEvaluasiRekanan($arrPaketRekananId, $paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=" ORDER BY CASE WHEN ITEM_PARENT = '0'  THEN TO_NUMBER(ITEM_CHILD, '999999') + A.PAKET_PENAWARAN_ID ELSE TO_NUMBER(ITEM_PARENT, '999999') END ASC ")
	{
		$str = "
			SELECT 
			A.PAKET_PENAWARAN_ID, A.REKANAN_ID_PEMENANG, A.LOKASI, A.ITEM_NUMBER, PAKET_ID, ITEM, ITEM_GROUP, SERVICE_LINE,
			   TOTAL_COST, SATUAN, A.QUANTITY, A.OE, TO_CHAR(A.DELIVERY_DATE, 'YYYY-MM-DD') DELIVERY_DATE,
			   ((COALESCE(A.QUANTITY, 0) * COALESCE(A.OE, 0))) SUMMARY, A.ITEM_PARENT, A.ITEM_CHILD, A.BIAYA_KIRIM,
			   ";

		for($i=0;$i<count($arrPaketRekananId);$i++)
		{			   
		$str .= "	   
			   COALESCE(B_".$i.".UNIT_PRICE_KOREKSI, B_".$i.".UNIT_PRICE) UPK_".$arrPaketRekananId[$i].",
			   ((COALESCE(B_".$i.".QUANTITY, 0) * COALESCE(B_".$i.".UNIT_PRICE_KOREKSI, B_".$i.".UNIT_PRICE))) SUMKOREKSI_".$arrPaketRekananId[$i].", 
			   B_".$i.".BOQ BOQ_".$arrPaketRekananId[$i].",
			   B_".$i.".QUANTITY Q_".$arrPaketRekananId[$i].",
			   B_".$i.".UNIT_PRICE UP_".$arrPaketRekananId[$i].", 
			   ((COALESCE(B_".$i.".QUANTITY, 0) * COALESCE(B_".$i.".UNIT_PRICE, 0))) SUM_".$arrPaketRekananId[$i].", 
			   B_".$i.".DELIVERY_DATE DD_".$arrPaketRekananId[$i].", 
			   B_".$i.".BIAYA_KIRIM BK_".$arrPaketRekananId[$i].", 
			   ";
		}
		
		$str .= "
			   A.LAST_CREATE_USER, A.LAST_CREATE_DATE
			FROM PAKET_PENAWARAN A 
				";
		for($i=0;$i<count($arrPaketRekananId);$i++)
		{
		$str .= "
			LEFT JOIN REKANAN_PAKET_PENAWARAN B_".$i." ON A.PAKET_PENAWARAN_ID = B_".$i.".PAKET_PENAWARAN_ID AND B_".$i.".PAKET_REKANAN_ID = '".$arrPaketRekananId[$i]."'	
				";
		}
		
		$str .= "
			WHERE 1 = 1	
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		
		$str .= $statement." ".$order;
		$this->query = $str;

		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsEvaluasiRekananLot($arrPaketRekananId, $paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=" ORDER BY A.ITEM_PARENT::NUMERIC ASC  ")
	{
		$str = "
			SELECT 
			A.PAKET_PENAWARAN_ID, A.REKANAN_ID_PEMENANG, A.LOKASI, A.ITEM_NUMBER, A.PAKET_ID, A.ITEM, A.ITEM_GROUP, 
			   AA.JUMLAH SUMMARY, AA.BIAYA_KIRIM,
			   ";

		for($i=0;$i<count($arrPaketRekananId);$i++)
		{			   
		$str .= "	   
			   B_".$i.".JUMLAH SUM_".$arrPaketRekananId[$i].",
			   B_".$i.".BIAYA_KIRIM BK_".$arrPaketRekananId[$i].",
			   ";
		}
		
		$str .= "
			   A.LAST_CREATE_USER, A.LAST_CREATE_DATE
			FROM PAKET_PENAWARAN A 
			LEFT JOIN PAKET_PENAWARAN_LOT AA ON A.PAKET_ID = AA.PAKET_ID AND A.ITEM_PARENT = AA.ITEM_PARENT
				";
		for($i=0;$i<count($arrPaketRekananId);$i++)
		{
		$str .= "
			LEFT JOIN REKANAN_PAKET_PENAWARAN_LOT B_".$i." ON A.PAKET_ID = B_".$i.".PAKET_ID AND A.ITEM_PARENT = B_".$i.".ITEM_PARENT AND B_".$i.".PAKET_REKANAN_ID = '".$arrPaketRekananId[$i]."'	
				";
		}
		
		$str .= "
			WHERE 1 = 1	 AND ITEM_CHILD = '0'  
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		
		$str .= $statement." ".$order;
		$this->query = $str;
		
		return $this->selectLimit($str,$limit,$from); 
    }
	
    function selectByParamsEvaluasiRekananNegosiasi($arrPaketRekananId, $paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=' ORDER BY A.PAKET_PENAWARAN_ID ASC ')
	{
		$str = "
			SELECT 
			A.PAKET_PENAWARAN_ID, PAKET_ID, ITEM, 
			   TOTAL_COST, SATUAN, A.QUANTITY, A.OE,  TO_CHAR(A.DELIVERY_DATE, 'YYYY-MM-DD') DELIVERY_DATE,
			   (COALESCE(A.QUANTITY, 0) * COALESCE(A.OE, 0)) SUMMARY,
			   ";

		for($i=0;$i<count($arrPaketRekananId);$i++)
		{			   
		$str .= "	   
			   B_".$i.".QUANTITY Q_".$arrPaketRekananId[$i].",
			   B_".$i.".UNIT_PRICE UP_".$arrPaketRekananId[$i].", 
			   (COALESCE(B_".$i.".QUANTITY, 0) * COALESCE(B_".$i.".UNIT_PRICE, 0)) SUM_".$arrPaketRekananId[$i].", 
			   B_".$i.".DELIVERY_DATE DD_".$arrPaketRekananId[$i].", 
			   ";
		}
		
		$str .= "
			   A.LAST_CREATE_USER, A.LAST_CREATE_DATE
			FROM PAKET_PENAWARAN A 
				";
		for($i=0;$i<count($arrPaketRekananId);$i++)
		{
		$str .= "
			LEFT JOIN REKANAN_PAKET_PENAWARAN B_".$i." ON A.PAKET_PENAWARAN_ID = B_".$i.".PAKET_PENAWARAN_ID AND B_".$i.".PAKET_REKANAN_ID = '".$arrPaketRekananId[$i]."'	
				";
		}
		
		$str .= "
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
		$str = "
			SELECT 
				REKANAN_PAKET_PENAWARAN_ID, 
				PAKET_PENAWARAN_ID, 
				PAKET_REKANAN_ID, 
				DELIVERY_DATE, 
				BULAN, 
				QUANTITY, 
				UNIT_PRICE
			FROM REKANAN_PAKET_PENAWARAN
			WHERE REKANAN_PAKET_PENAWARAN_ID IS NOT NULL		
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_PAKET_PENAWARAN_ID ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
	
    function getCountByParams($paramsArray=array())
	{		
		$str = "SELECT COUNT(REKANAN_PAKET_PENAWARAN_ID) AS ROWCOUNT FROM REKANAN_PAKET_PENAWARAN WHERE REKANAN_PAKET_PENAWARAN_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(REKANAN_PAKET_PENAWARAN_ID) AS ROWCOUNT FROM REKANAN_PAKET_PENAWARAN WHERE REKANAN_PAKET_PENAWARAN_ID IS NOT NULL "; 
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
	
	function getSumJumlahPenawaran($paramsArray=array())
	{
		$str = "SELECT SUM(JUMLAH) AS ROWCOUNT FROM REKANAN_PAKET_PENAWARAN WHERE 1=1"; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->select($str); 
		$this->query = $str;
		
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }	
	
  }   	
?>