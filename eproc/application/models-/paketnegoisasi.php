<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

  include_once('entity.php');

  class PaketNegoisasi extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
    function PaketNegoisasi()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_NEGOSIASI_ID", $this->getNextId("PAKET_NEGOSIASI_ID","PAKET_NEGOSIASI"));

		$str = "
		INSERT INTO PAKET_NEGOSIASI (
   			           PAKET_NEGOSIASI_ID, PAKET_ID, KODE,
   					   SATUAN, JUMLAH, BULAN, PAKET_PENAWARAN_ID, UNIT_PRICE, UNIT_PRICE_AWAL, QUANTITY, BIAYA_KIRIM, BIAYA_KIRIM_AWAL)
			 	VALUES (
					   ".$this->getField("PAKET_NEGOSIASI_ID").", '".$this->getField("PAKET_ID")."', '".$this->getField("KODE")."',
					   '".$this->getField("SATUAN")."', ".$this->getField("JUMLAH").", '".$this->getField("BULAN")."',
					   '".$this->getField("PAKET_PENAWARAN_ID")."', ".$this->getField("UNIT_PRICE").", ".$this->getField("UNIT_PRICE").", ".$this->getField("QUANTITY").",
					   ".$this->getField("BIAYA_KIRIM").",
					   ".$this->getField("BIAYA_KIRIM")."
				)";
		$this->query = $str;
		// echo $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_NEGOSIASI SET
				  KODE = '".$this->getField("KODE")."'
				WHERE PAKET_NEGOSIASI_ID = ".$this->getField("PAKET_NEGOSIASI_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updateUnitPrice()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_NEGOSIASI SET
				  UNIT_PRICE = ".$this->getField("UNIT_PRICE")."
				WHERE PAKET_PENAWARAN_ID = ".$this->getField("PAKET_PENAWARAN_ID")."
				";

				$this->query = $str;
		return $this->execQuery($str);
    }

    function updateBiayaKirim()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_NEGOSIASI SET
				  BIAYA_KIRIM = ".$this->getField("BIAYA_KIRIM")."
				WHERE PAKET_PENAWARAN_ID = ".$this->getField("PAKET_PENAWARAN_ID")."
				";

				$this->query = $str;
		return $this->execQuery($str);
    }



    function updateSetujui()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_NEGOSIASI SET
				  SETUJUI = '1'
				WHERE PAKET_PENAWARAN_ID = ".$this->getField("PAKET_PENAWARAN_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM PAKET_NEGOSIASI
                WHERE
                  PAKET_ID = ".$this->getField("PAKET_ID")."";

		$this->query = $str;
		//echo $str;
        return $this->execQuery($str);
    }

    /**
    * Cari record berdasarkan array parameter dan limit tampilan
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","KODE"=>"yyy")
    * @param int limit Jumlah maksimal record yang akan diambil
    * @param int from Awal record yang diambil
    * @return boolean True jika sukses, false jika tidak
    **/
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT
					PAKET_NEGOSIASI_ID, PAKET_ID, KODE,
   					SATUAN, JUMLAH, BULAN, UNIT_PRICE, UNIT_PRICE_AWAL, QUANTITY, ((UNIT_PRICE * QUANTITY)) TOTAL, SETUJUI, BIAYA_KIRIM, PPN_JUMLAH_HARGA_SATUAN, PPN_JUMLAH_HARGA_PENAWARAN, PPN_JUMLAH_HARGA_NEGO, PPN
				FROM PAKET_NEGOSIASI A WHERE PAKET_NEGOSIASI_ID IS NOT NULL ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

    $str .= $statement." ORDER BY PAKET_NEGOSIASI_ID ASC";
		$this->query = $str;
		// echo $str; die;
		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsMonitoring($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT
					A.PAKET_NEGOSIASI_ID, A.PAKET_ID, KODE,
   					B.SATUAN, A.JUMLAH, BULAN, UNIT_PRICE, A.QUANTITY, ITEM, A.SETUJUI, A.BIAYA_KIRIM
				FROM PAKET_NEGOSIASI A INNER JOIN PAKET_PENAWARAN B ON A.PAKET_PENAWARAN_ID = B.PAKET_PENAWARAN_ID WHERE PAKET_NEGOSIASI_ID IS NOT NULL ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_NEGOSIASI_ID ASC";
		//echo $str;
		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PAKET_NEGOSIASI_ID, PAKET_ID, KODE,
			   SATUAN, JUMLAH
				FROM PAKET_NEGOSIASI WHERE PAKET_NEGOSIASI_ID IS NOT NULL";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY KODE ASC";
		return $this->selectLimit($str,$limit,$from);
    }
    /**
    * Hitung jumlah record berdasarkan parameter (array).
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","KODE"=>"yyy")
    * @return long Jumlah record yang sesuai kriteria
    **/
	function getUnitPrice($paramsArray=array())
	{
		$str = "SELECT UNIT_PRICE AS ROWCOUNT FROM PAKET_NEGOSIASI A WHERE PAKET_NEGOSIASI_ID IS NOT NULL ";
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->select($str);

		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return "";
    }

	function getBiayaKirim($paramsArray=array())
	{
		$str = "SELECT BIAYA_KIRIM AS ROWCOUNT FROM PAKET_NEGOSIASI A WHERE PAKET_NEGOSIASI_ID IS NOT NULL ";
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->select($str);

		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return "";
    }

    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(PAKET_NEGOSIASI_ID) AS ROWCOUNT FROM PAKET_NEGOSIASI WHERE PAKET_NEGOSIASI_ID IS NOT NULL ";
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
		$str = "SELECT COUNT(PAKET_NEGOSIASI_ID) AS ROWCOUNT FROM PAKET_NEGOSIASI WHERE PAKET_NEGOSIASI_ID IS NOT NULL ";
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
