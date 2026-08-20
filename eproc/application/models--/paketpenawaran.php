<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

  include_once('entity.php');

  class PaketPenawaran extends Entity{

	var $query;
    /**
    * Class constructor.
    **/

    function __construct(){
		parent::__construct();
	}

    function PaketPenawaran()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_PENAWARAN_ID", $this->getNextId("PAKET_PENAWARAN_ID","PAKET_PENAWARAN"));

		$str = "
				INSERT INTO PAKET_PENAWARAN (
					PAKET_PENAWARAN_ID,
					PAKET_ID,
					ITEM,
					TOTAL_COST,
					SATUAN,
					QUANTITY,
					OE,
					JUMLAH,
					LOKASI,
					BOQ,
					BOQ_KOLOM,
					PR_GROUP_NUMBER,
					PR_NUMBER,
					ITEM_NUMBER,
					DELIVERY_DATE,
					LAST_CREATE_USER,
					LAST_CREATE_DATE,
					ITEM_PARENT,
					ITEM_CHILD,
					BIAYA_KIRIM, BOQ_FILE)
				VALUES ( '".$this->getField("PAKET_PENAWARAN_ID")."',
						'".$this->getField("PAKET_ID")."',
						'".$this->getField("ITEM")."',
						'".$this->getField("TOTAL_COST")."',
						'".$this->getField("SATUAN")."',
						'".$this->getField("QUANTITY")."',
						'".$this->getField("OE")."',
						".$this->getField("JUMLAH").",
						'".$this->getField("LOKASI")."',
						'".$this->getField("BOQ")."',
						'".$this->getField("BOQ_KOLOM")."',
						'".$this->getField("PR_GROUP_NUMBER")."',
						'".$this->getField("PR_NUMBER")."',
						".$this->getField("ITEM_NUMBER").",
						".$this->getField("DELIVERY_DATE").",
						'".$this->getField("LAST_CREATE_USER")."',
						CURRENT_DATE,
						'".$this->getField("ITEM_PARENT")."',
						'".$this->getField("ITEM_CHILD")."',
						'".$this->getField("BIAYA_KIRIM")."',
						'".$this->getField("BOQ_FILE")."')
		";
		$this->query = $str;
		//echo $str;exit;
		return $this->execQuery($str);
    }

    function insert2()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_PENAWARAN_ID", $this->getNextId("PAKET_PENAWARAN_ID","PAKET_PENAWARAN"));

		$str = "
				INSERT INTO PAKET_PENAWARAN (
					PAKET_PENAWARAN_ID,
					PAKET_ID,
					ITEM,
					ITEM_GROUP,
					ITEM_CHILD,
					OE,
					JUMLAH,
					QUANTITY,
					LAST_CREATE_USER,
					LAST_CREATE_DATE,
					PERMOHONAN_PAKET_ID)
				VALUES ( '".$this->getField("PAKET_PENAWARAN_ID")."',
						'".$this->getField("PAKET_ID")."',
						'".$this->getField("NAMA")."',
						'".$this->getField("NAMA")."',
						'0',
						".$this->getField("JUMLAH").",
						".$this->getField("JUMLAH").",
						1,
						".$this->getField("LAST_CREATE_USER").",
						CURRENT_DATE,
						'".$this->getField("PERMOHONAN_PAKET_ID")."')
		";
		$this->query = $str;
		// echo $str;exit;
		return $this->execQuery($str);
    }

    function update2()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_PENAWARAN
				SET
					ITEM = '".$this->getField("NAMA")."',
					ITEM_GROUP = '".$this->getField("NAMA")."',
					OE = '".$this->getField("JUMLAH")."',
					JUMLAH = '".$this->getField("JUMLAH")."',
					LAST_CREATE_USER = '".$this->getField("LAST_CREATE_USER")."',
					LAST_CREATE_DATE = CURRENT_DATE
				WHERE PERMOHONAN_PAKET_ID = '".$this->getField("PERMOHONAN_PAKET_ID")."'
				";
				$this->query = $str;

		return $this->execQuery($str);
    }

	function insertPaket()
	{

		$str = "
				INSERT INTO PAKET_PENAWARAN (
					PAKET_PENAWARAN_ID,
					PAKET_ID,
					ITEM_GROUP,
					ITEM,
					SATUAN,
					QUANTITY,
					OE,
					JUMLAH,
					PR_GROUP_NUMBER,
					PR_NUMBER,
					ITEM_NUMBER,
					DELIVERY_DATE,
					ITEM_PARENT,
					ITEM_CHILD,
					LAST_CREATE_USER,
					LAST_CREATE_DATE)
				SELECT COALESCE((SELECT MAX(PAKET_PENAWARAN_ID) FROM PAKET_PENAWARAN), 0) + 1 ROWNUM,
					   '".$this->getField("PAKET_ID")."' PAKET_ID,
					   '".$this->getField("NAMA")."' ITEM_GROUP,
					   '".$this->getField("NAMA")."' ITEM, 'PKT' SATUAN,
					   1 QUANTITY,
					   '".$this->getField("JUMLAH")."' OE,
					   '".$this->getField("JUMLAH")."' JUMLAH,
					   '' PR_GROUP_NUMBER, '' PR_NUMBER, 1 ITEM_NUMBER, CURRENT_DATE DELIVERY_DATE,
					   '".round(microtime(true) * 1000)."', 0,
					   '".$this->getField("LAST_CREATE_USER")."' LAST_CREATE_USER, CURRENT_DATE
				FROM PAKET WHERE PAKET_ID = '".$this->getField("PAKET_ID")."'
		";
		$this->query = $str;

		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_PENAWARAN
				SET
					OE = '".$this->getField("OE")."',
					JUMLAH = '".$this->getField("JUMLAH")."',
					BOQ = '".$this->getField("BOQ")."',
					BOQ_KOLOM = '".$this->getField("BOQ_KOLOM")."',
					LOKASI = '".$this->getField("LOKASI")."',
					DELIVERY_DATE = ".$this->getField("DELIVERY_DATE")."
				WHERE PAKET_PENAWARAN_ID = '".$this->getField("PAKET_PENAWARAN_ID")."'
				";
				$this->query = $str;

		return $this->execQuery($str);
    }

    function updatePaketId()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_PENAWARAN
				SET
					PAKET_ID = '".$this->getField("PAKET_ID")."',
					OE = '".$this->getField("OE")."',
					JUMLAH = '".$this->getField("JUMLAH")."',
					QUANTITY = '".$this->getField("QUANTITY")."',
					ITEM_GROUP = '".$this->getField("ITEM_GROUP")."',
					ITEM_NUMBER = '".$this->getField("ITEM_NUMBER")."',
					SATUAN = 'PKT',
					DELIVERY_DATE = CURRENT_DATE,
					UPDATED_BY = ".$this->getField("CREATED_BY").",
					UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE PERMOHONAN_PAKET_ID = '".$this->getField("PERMOHONAN_PAKET_ID")."'
				";
				// echo $str;
				$this->query = $str;

		return $this->execQuery($str);
    }

	function updatePemenang()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_PENAWARAN
				SET
						REKANAN_ID_PEMENANG= '".$this->getField("REKANAN_ID_PEMENANG")."'
				WHERE PAKET_PENAWARAN_ID = ".$this->getField("PAKET_PENAWARAN_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

	function updatePenawaran()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_PENAWARAN
				SET
						PAKET_ID= '".$this->getField("PAKET_ID")."',
						ITEM='".$this->getField("ITEM")."',
						TOTAL_COST='".$this->getField("TOTAL_COST")."',
						SATUAN='".$this->getField("SATUAN")."',
						QUANTITY='".$this->getField("QUANTITY")."',
						OE='".$this->getField("OE")."',
						JUMLAH=".$this->getField("JUMLAH").",
						LOKASI='".$this->getField("LOKASI")."',
						BOQ='".$this->getField("BOQ")."',
						BOQ_KOLOM='".$this->getField("BOQ_KOLOM")."',
						PR_GROUP_NUMBER='".$this->getField("PR_GROUP_NUMBER")."',
						PR_NUMBER='".$this->getField("PR_NUMBER")."',
						DELIVERY_DATE=".$this->getField("DELIVERY_DATE").",
						BIAYA_KIRIM	= '".$this->getField("BIAYA_KIRIM")."',
						BOQ_FILE = '".$this->getField("BOQ_FILE")."'
				WHERE PAKET_PENAWARAN_ID = ".$this->getField("PAKET_PENAWARAN_ID")."
				";
				$this->query = $str;
				// echo $str;exit;
		return $this->execQuery($str);
  }

  function updatePenawaranBoq()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_PENAWARAN
				SET
						BOQ_FILE = '".$this->getField("BOQ_FILE")."'
				WHERE PAKET_PENAWARAN_ID = ".$this->getField("PAKET_PENAWARAN_ID")."
				";
				$this->query = $str;
				// echo $str;exit;
		return $this->execQuery($str);
  }

    function updatePenawaranPermohonan()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_PENAWARAN
				SET
						ITEM='".$this->getField("ITEM")."',
						TOTAL_COST='".$this->getField("TOTAL_COST")."',
						SATUAN='".$this->getField("SATUAN")."',
						QUANTITY='".$this->getField("QUANTITY")."',
						OE='".$this->getField("OE")."',
						JUMLAH=".$this->getField("JUMLAH").",
						LOKASI='".$this->getField("LOKASI")."',
						BOQ='".$this->getField("BOQ")."',
						BOQ_KOLOM='".$this->getField("BOQ_KOLOM")."',
						PR_GROUP_NUMBER='".$this->getField("PR_GROUP_NUMBER")."',
						PR_NUMBER='".$this->getField("PR_NUMBER")."',
						DELIVERY_DATE=".$this->getField("DELIVERY_DATE").",
						BIAYA_KIRIM	= '".$this->getField("BIAYA_KIRIM")."',
						BOQ_FILE = '".$this->getField("BOQ_FILE")."'
				WHERE PAKET_PENAWARAN_ID = ".$this->getField("PAKET_PENAWARAN_ID")."
				";
				$this->query = $str;
				// echo $str;exit;
		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM PAKET_PENAWARAN
                WHERE
                  PAKET_ID = '".$this->getField("PAKET_ID")."'";

		$this->query = $str;
        return $this->execQuery($str);
    }

    function deletePermohonan()
	{
        $str = "DELETE FROM PAKET_PENAWARAN
                WHERE
                  PERMOHONAN_PAKET_ID = '".$this->getField("PERMOHONAN_PAKET_ID")."'";

		$this->query = $str;
        return $this->execQuery($str);
    }

	function deletePenawaran()
	{
        $str = "
				DELETE FROM PAKET_PENAWARAN
                	WHERE
                	 PAKET_ID = ".$this->getField("PAKET_ID")." AND ITEM_CHILD = '".$this->getField("ITEM_CHILD")."'
				";

		$this->query = $str;

        return $this->execQuery($str);
    }

	function deleteHarga()
	{
        $str = "
				DELETE FROM PAKET_PENAWARAN
                	WHERE
                	 PAKET_ID = ".$this->getField("PAKET_ID")." AND ITEM_CHILD IS NULL AND ITEM_PARENT IS NULL
				";

		$this->query = $str;

        return $this->execQuery($str);
    }

	function deletePenawaranParent()
	{
        $str = "
				DELETE FROM PAKET_PENAWARAN
                	WHERE
                	 PAKET_PENAWARAN_ID = ".$this->getField("PAKET_PENAWARAN_ID")."
				";

		$this->query = $str;
		//echo $str;exit;
        return $this->execQuery($str);
    }

	function deleteChild()
	{
        $str = "
				DELETE FROM PAKET_PENAWARAN
                	WHERE
                	 PAKET_PENAWARAN_ID = ".$this->getField("PAKET_PENAWARAN_ID")."
				";

		$this->query = $str;
		//echo $str;exit;
        return $this->execQuery($str);
    }

    /**
    * Cari record berdasarkan array parameter dan limit tampilan
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","ITEM"=>"yyy")
    * @param int limit Jumlah maksimal record yang akan diambil
    * @param int from Awal record yang diambil
    * @return boolean True jika sukses, false jika tidak
    **/
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=' ORDER BY PAKET_PENAWARAN_ID ASC ')
	{
		$str = "
			SELECT
				PAKET_PENAWARAN_ID,
				PAKET_ID,
				ITEM,
				TOTAL_COST,
				SATUAN,
				QUANTITY,
				OE,
				JUMLAH,
				LOKASI,
				BOQ,
				BOQ_KOLOM,
				DELIVERY_DATE,
				ITEM_NUMBER,
				ITEM_PARENT,
				ITEM_CHILD,
				PERMOHONAN_PAKET_ID,
				REKANAN_ID_PEMENANG, BIAYA_KIRIM, BOQ_FILE
			FROM PAKET_PENAWARAN A
			WHERE PAKET_PENAWARAN_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die();
		$this->query = $str;
		$str .= $statement." ".$order;

		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsLot($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=' ORDER BY PAKET_PENAWARAN_ID ASC ')
	{
		$str = "
			SELECT
				A.PAKET_PENAWARAN_ID,
				A.PAKET_ID,
				A.ITEM,
				B.JUMLAH,
				B.BIAYA_KIRIM,
				ITEM_NUMBER,
				A.ITEM_PARENT,
				A.ITEM_CHILD,
				A.REKANAN_ID_PEMENANG
			FROM PAKET_PENAWARAN A
			INNER JOIN PAKET_PENAWARAN_LOT B ON A.PAKET_ID = B.PAKET_ID AND A.ITEM_PARENT = B.ITEM_PARENT
			WHERE PAKET_PENAWARAN_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ".$order;

		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsNegosiasiLot($reqPaketId='', $paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				SELECT A.PAKET_ID, B.REKANAN_ID, B.NAMA, COUNT(1) JUMLAH,
					   AMBIL_PAKET_PENAWARAN_LOKASI(A.PAKET_ID, B.REKANAN_ID) LOT,
					   C.NAMA, C.NAMA || ' ' || B.NAMA NAMA_PERUSAHAAN
				FROM PAKET_PENAWARAN A
				INNER JOIN REKANAN B ON A.REKANAN_ID_PEMENANG = B.REKANAN_ID
				LEFT JOIN REKANAN_TIPE C ON B.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID
				WHERE A.PAKET_ID = '".$reqPaketId."'  AND A.ITEM_CHILD = '0'

		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." GROUP BY A.PAKET_ID, B.REKANAN_ID, B.NAMA, C.NAMA ORDER BY PAKET_ID ASC";

		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsNegosiasiLotLokasi($reqPaketId='', $paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT A.PAKET_ID, B.REKANAN_ID, B.NAMA, COUNT(1) JUMLAH,
					   AMBIL_PAKET_PENAWARAN_LOKASI_LOT(A.PAKET_ID, B.REKANAN_ID) LOT,
					   C.NAMA, C.NAMA || ' ' || B.NAMA NAMA_PERUSAHAAN
				FROM PAKET_PENAWARAN A
				INNER JOIN REKANAN B ON A.REKANAN_ID_PEMENANG = B.REKANAN_ID
				LEFT JOIN REKANAN_TIPE C ON B.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID
				WHERE A.PAKET_ID = '".$reqPaketId."'  AND A.ITEM_CHILD = '0'
			";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." GROUP BY A.PAKET_ID, B.REKANAN_ID, B.NAMA, C.NAMA ORDER BY PAKET_ID ASC";

		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsRekananPemenang($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT DISTINCT B.REKANAN_ID, B.NAMA, B.EMAIL FROM PAKET_PENAWARAN A
			INNER JOIN REKANAN B ON A.REKANAN_ID_PEMENANG = B.REKANAN_ID
			WHERE NOT A.QUANTITY = 0
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement."";

		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsRekananPaketPenawaran($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT
				A.PAKET_PENAWARAN_ID,
				PAKET_ID,
				ITEM,
				TOTAL_COST,
				SATUAN,
				A.QUANTITY,
				OE,
				A.JUMLAH,
				LOKASI,
				A.BOQ,
				BOQ_KOLOM,
				A.DELIVERY_DATE,
				ITEM_NUMBER,
				ITEM_PARENT,
				ITEM_CHILD,
				B.UNIT_PRICE, TO_CHAR(B.DELIVERY_DATE, 'YYYY-MM-DD') DELIVERY_DATE, B.JUMLAH, B.JUMLAH_KOREKSI
			FROM PAKET_PENAWARAN A
			LEFT JOIN REKANAN_PAKET_PENAWARAN B ON A.PAKET_PENAWARAN_ID=B.PAKET_PENAWARAN_ID
			WHERE 1=1
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die();
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_PENAWARAN_ID ASC";

		return $this->selectLimit($str,$limit,$from);
    }


	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT
				PAKET_PENAWARAN_ID,
				PAKET_ID,
				ITEM,
				TOTAL_COST,
				SATUAN,
				QUANTITY,
				OE,
				DELIVERY_DATE
			FROM PAKET_PENAWARAN
			WHERE PAKET_PENAWARAN_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_PENAWARAN_ID ASC";
		return $this->selectLimit($str,$limit,$from);
    }
    /**
    * Hitung jumlah record berdasarkan parameter (array).
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","ITEM"=>"yyy")
    * @return long Jumlah record yang sesuai kriteria
    **/

    function getSummaryOE($paramsArray=array())
	{
		$str = "SELECT SUM(COALESCE(QUANTITY, 0) * COALESCE(OE, 0)) AS ROWCOUNT FROM PAKET_PENAWARAN WHERE PAKET_PENAWARAN_ID IS NOT NULL ";
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

    function getCountByParams($paramsArray=array(), $statement ='')
	{
		$str = "SELECT COUNT(PAKET_PENAWARAN_ID) AS ROWCOUNT FROM PAKET_PENAWARAN WHERE PAKET_PENAWARAN_ID IS NOT NULL ".$statement;
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

    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(PAKET_PENAWARAN_ID) AS ROWCOUNT FROM PAKET_PENAWARAN WHERE PAKET_PENAWARAN_ID IS NOT NULL ";
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
	function getCountByParamsJumlahPaketPenawaran($paramsArray=array())
	{
		$str = "SELECT COUNT(1) AS ROWCOUNT FROM PAKET_PENAWARAN A
								   WHERE EXISTS (SELECT 1 FROM PAKET_NEGOSIASI X
								   						  WHERE X.PAKET_PENAWARAN_ID = A.PAKET_PENAWARAN_ID) ";
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
