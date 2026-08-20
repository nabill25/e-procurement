<?php
 /**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

  include_once('entity.php');

  class RekananPeralatan extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
    function RekananPeralatan()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_PERALATAN_ID", $this->getNextId("REKANAN_PERALATAN_ID","REKANAN_PERALATAN"));

		$str = "
				INSERT INTO REKANAN_PERALATAN (
					REKANAN_PERALATAN_ID,
					REKANAN_ID,
					JENIS,
					JUMLAH,
					KAPASITAS,
					KAPASITAS_SATUAN,
					MERK,
					TAHUN,
					KONDISI,
					LOKASI,
					BUKTI_KEPEMILIKAN,
   					PATH_FILE, TIPE, UKURAN, NAMA_FILE, CREATED_BY, CREATED_DATE)
				VALUES ( '".$this->getField("REKANAN_PERALATAN_ID")."',
					'".$this->getField("REKANAN_ID")."',
					'".$this->getField("JENIS")."',
					'".$this->getField("JUMLAH")."',
					'".$this->getField("KAPASITAS")."',
					'".$this->getField("KAPASITAS_SATUAN")."',
					'".$this->getField("MERK")."',
					'".$this->getField("TAHUN")."',
					'".$this->getField("KONDISI")."',
					'".$this->getField("LOKASI")."',
					'".$this->getField("BUKTI_KEPEMILIKAN")."',
					'".$this->getField("PATH_FILE")."',
					'".$this->getField("TIPE")."',
					'".$this->getField("UKURAN")."',
					'".$this->getField("NAMA_FILE")."',
					".$this->getField("CREATED_BY").",
					CURRENT_TIMESTAMP
					)
		";
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_PERALATAN
				SET
					JENIS = '".$this->getField("JENIS")."',
					JUMLAH = '".$this->getField("JUMLAH")."',
					KAPASITAS = '".$this->getField("KAPASITAS")."',
					KAPASITAS_SATUAN = '".$this->getField("KAPASITAS_SATUAN")."',
					MERK = '".$this->getField("MERK")."',
					TAHUN = '".$this->getField("TAHUN")."',
					KONDISI = '".$this->getField("KONDISI")."',
					LOKASI = '".$this->getField("LOKASI")."',
					BUKTI_KEPEMILIKAN = '".$this->getField("BUKTI_KEPEMILIKAN")."',
					PATH_FILE = '".$this->getField("PATH_FILE")."',
					TIPE = '".$this->getField("TIPE")."',
					UKURAN = '".$this->getField("UKURAN")."',
					NAMA_FILE= '".$this->getField("NAMA_FILE")."',
					UPDATED_BY = ".$this->getField("CREATED_BY").",
					UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE REKANAN_PERALATAN_ID = '".$this->getField("REKANAN_PERALATAN_ID")."' AND REKANAN_ID = '".$this->getField("REKANAN_ID")."'
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM REKANAN_PERALATAN
                WHERE
                  REKANAN_PERALATAN_ID = '".$this->getField("REKANAN_PERALATAN_ID")."' AND REKANAN_ID = '".$this->getField("REKANAN_ID")."' ";

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
		$str = "
			SELECT
				REKANAN_PERALATAN_ID,
				REKANAN_ID,
				JENIS,
				JUMLAH,
				KAPASITAS,
				KAPASITAS_SATUAN,
				MERK,
				TAHUN,
				KONDISI,
				LOKASI,
				BUKTI_KEPEMILIKAN,
   				PATH_FILE, TIPE, UKURAN,NAMA_FILE
			FROM REKANAN_PERALATAN A
			WHERE REKANAN_PERALATAN_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_PERALATAN_ID ASC";

		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsSyarat($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT
				A.REKANAN_PERALATAN_ID,
				A.REKANAN_ID,
				JENIS,
				JUMLAH,
				KAPASITAS,
				KAPASITAS_SATUAN,
				MERK,
				TAHUN,
				KONDISI,
				LOKASI,
				BUKTI_KEPEMILIKAN,
   				PATH_FILE, TIPE, UKURAN,
                B.CATATAN,
                B.REKANAN_DAFTAR_PERALATAN_ID, A.NAMA_FILE
			FROM REKANAN_PERALATAN A
            INNER JOIN REKANAN_DAFTAR_PERALATAN B ON A.REKANAN_PERALATAN_ID = B.REKANAN_PERALATAN_ID AND A.REKANAN_ID = B.REKANAN_ID
			WHERE A.REKANAN_PERALATAN_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY A.REKANAN_PERALATAN_ID ASC";

		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT
				REKANAN_PERALATAN_ID,
				REKANAN_ID,
				JENIS,
				JUMLAH,
				KAPASITAS,
				KAPASITAS_SATUAN,
				MERK,
				TAHUN,
				KONDISI,
				LOKASI,
				BUKTI_KEPEMILIKAN
			FROM REKANAN_PERALATAN
			WHERE REKANAN_PERALATAN_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_PERALATAN_ID ASC";
		return $this->selectLimit($str,$limit,$from);
    }
    /**
    * Hitung jumlah record berdasarkan parameter (array).
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy")
    * @return long Jumlah record yang sesuai kriteria
    **/
    function getCountByParams($paramsArray=array(), $stat = '')
	{
		$str = "SELECT COUNT(REKANAN_PERALATAN_ID) AS ROWCOUNT FROM REKANAN_PERALATAN A WHERE REKANAN_PERALATAN_ID IS NOT NULL ";
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= $stat;
		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(REKANAN_PERALATAN_ID) AS ROWCOUNT FROM REKANAN_PERALATAN WHERE REKANAN_PERALATAN_ID IS NOT NULL ";
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
