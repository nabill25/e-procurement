<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketTahap extends Entity{

	var $query;
    /**
    * Class constructor.
    **/

    function __construct(){
		parent::__construct();
	}
	
    function PaketTahap()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_TAHAP_ID", $this->getNextId("PAKET_TAHAP_ID","PAKET_TAHAP"));

		$str = "
		INSERT INTO PAKET_TAHAP (
   			        PAKET_TAHAP_ID, NAMA)
			 	VALUES (
				  ".$this->getField("PAKET_TAHAP_ID").",
				  '".$this->getField("NAMA")."'
				)";

		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_TAHAP SET
				  NAMA = '".$this->getField("NAMA")."'
				WHERE PAKET_TAHAP_ID = ".$this->getField("PAKET_TAHAP_ID")."
				";
				$this->query = $str;

		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM PAKET_TAHAP
                WHERE
                  PAKET_TAHAP_ID = ".$this->getField("PAKET_TAHAP_ID")."";

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
				PAKET_TAHAP_ID, PAKET_ID, NAMA,
				   HADIR, TAMPILKAN, TANGGAL_AWAL,
				   TO_CHAR(TANGGAL_AKHIR, 'DD-MM-YYYY') TANGGAL_AKHIR, TANGGAL_AKHIR TANGGAL_AKHIR2, URUT, JAM_AWAL, JAM_AKHIR, TO_CHAR(TANGGAL_AWAL, 'DD-MM-YYYY HH24:MI') JAM_BUKA
				FROM PAKET_TAHAP WHERE 1 = 1
			  ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die();
		$this->query = $str;
		$str .= $statement." ORDER BY URUT ASC";
		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsTahapan($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				SELECT
				A.PAKET_TAHAP_ID, A.PAKET_ID, A.NAMA,  B.NAMA PAKET,
				   HADIR, TAMPILKAN, TANGGAL_AWAL,
				   TO_CHAR(TANGGAL_AKHIR, 'DD-MM-YYYY') TANGGAL_AKHIR, URUT, JAM_AWAL, JAM_AKHIR, TO_CHAR(TANGGAL_AWAL, 'DD-MM-YYYY') TANGGAL_AWAL
				FROM PAKET_TAHAP A INNER JOIN PAKET B ON A.PAKET_ID = B.PAKET_ID WHERE 1 = 1
			  ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die();
		$this->query = $str;
		$str .= $statement." ORDER BY URUT ASC";
		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsJadwal($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				SELECT
				   PAKET_TAHAP_ID, PAKET_ID, NAMA,
						   HADIR, TAMPILKAN, TO_CHAR(TANGGAL_AWAL, 'YYYY-MM-DD') TANGGAL_AWAL,
						   TO_CHAR(TANGGAL_AKHIR, 'YYYY-MM-DD') TANGGAL_AKHIR, URUT, JAM_AWAL, JAM_AKHIR, TO_CHAR(TANGGAL_AWAL, 'DD-MM-YYYY HH24:MI') JAM_BUKA,
						   CASE WHEN COALESCE(NULLIF(JAM_AWAL,''), 'X') = 'X' THEN
						   CASE WHEN (CURRENT_TIMESTAMP BETWEEN TO_TIMESTAMP(TO_CHAR(TANGGAL_AWAL, 'DDMMYYYY') || ' 00:00', 'DDMMYYYY HH24:MI')
								AND
								COALESCE(TANGGAL_AKHIR,
										TO_TIMESTAMP(TO_CHAR(TANGGAL_AWAL, 'DDMMYYYY') || ' 23:59', 'DDMMYYYY HH24:MI'))) THEN 1 ELSE 0 END
							   ELSE
						   CASE WHEN (CURRENT_TIMESTAMP BETWEEN TANGGAL_AWAL
								AND
								COALESCE(TANGGAL_AKHIR,
										TO_TIMESTAMP(TO_CHAR(TANGGAL_AWAL, 'DDMMYYYY') || ' 23:59', 'DDMMYYYY HH24:MI'))) THEN 1 ELSE 0 END
						END AKTIF
						FROM PAKET_TAHAP WHERE 1 = 1
			  ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= $statement." ORDER BY URUT ASC";
		// echo $str;

		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PAKET_TAHAP_ID, NAMA
				FROM PAKET_TAHAP WHERE PAKET_TAHAP_ID IS NOT NULL";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY NAMA ASC";
		return $this->selectLimit($str,$limit,$from);
    }

    function selectByJawdalAktif($paramsArray=array(),$limit=-1,$from=-1, $statement='')
    {
    	$str = "
				SELECT A.*, B.NAMA PAKET, B.NILAI HPS, B.PAKET_METODE_LELANG_ID,
					C.NAMA METODE_LELANG FROM (
					SELECT 
					PAKET_TAHAP_ID, PAKET_ID, NAMA, HADIR, TAMPILKAN, 
					TANGGAL_AWAL, TANGGAL_AKHIR, URUT, JAM_AWAL, JAM_AKHIR, AKTIVITAS,
					CASE
							WHEN COALESCE ( NULLIF ( JAM_AWAL, '' ), 'X' ) = 'X' THEN
						CASE
								WHEN (
									CURRENT_TIMESTAMP BETWEEN TO_TIMESTAMP( TO_CHAR( TANGGAL_AWAL, 'DDMMYYYY' ) || ' 00:00', 'DDMMYYYY HH24:MI' ) 
									AND COALESCE (
										TANGGAL_AKHIR,
									TO_TIMESTAMP( TO_CHAR( TANGGAL_AWAL, 'DDMMYYYY' ) || ' 23:59', 'DDMMYYYY HH24:MI' ))) THEN
									1 ELSE 0 
							END ELSE
							CASE
									WHEN (
										CURRENT_TIMESTAMP BETWEEN TANGGAL_AWAL 
										AND COALESCE (
											TANGGAL_AKHIR,
										TO_TIMESTAMP( TO_CHAR( TANGGAL_AWAL, 'DDMMYYYY' ) || ' 23:59', 'DDMMYYYY HH24:MI' ))) THEN
										1 ELSE 0 
									END 
									END AKTIF 
					FROM PAKET_TAHAP
					WHERE TAMPILKAN=1 
					ORDER BY URUT ASC
					) A
					JOIN PAKET B ON A.PAKET_ID=B.PAKET_ID
					JOIN PAKET_METODE_LELANG C ON B.PAKET_METODE_LELANG_ID=C.PAKET_METODE_LELANG_ID
					WHERE A.AKTIF='1'
			  ";

		while(list($key,$val) = each($paramsArray))
		{
			// ikn 20190218
		    $pecah = explode("||", $key);
		    if (count($pecah) > 1) {
		        $str .= "AND $pecah[0] $pecah[1] $val ";
		    } else {
		        $str .= " AND $key = '$val' ";
		    }
		}
		$str .= $statement." ORDER BY A.PAKET_ID ASC";
		// echo $str; die();
		$this->query = $str;
        $rs = $this->selectLimit($str,$limit,$from);

		//	print_r($rs);
        return  $rs;
    }

    function selectByJawdalAktifFront($paramsArray=array(),$limit=-1,$from=-1, $statement='')
    {
    	$str = "
				SELECT B.PAKET_ID, B.NAMA PAKET, B.NILAI HPS, B.PAKET_METODE_LELANG_ID, B.PUBLISH_PAKET,
					C.NAMA METODE_LELANG FROM (
					SELECT 
					PAKET_TAHAP_ID, PAKET_ID, NAMA, HADIR, TAMPILKAN, 
					TANGGAL_AWAL, TANGGAL_AKHIR, URUT, JAM_AWAL, JAM_AKHIR, AKTIVITAS,
					CASE
							WHEN COALESCE ( NULLIF ( JAM_AWAL, '' ), 'X' ) = 'X' THEN
						CASE
								WHEN (
									CURRENT_TIMESTAMP BETWEEN TO_TIMESTAMP( TO_CHAR( TANGGAL_AWAL, 'DDMMYYYY' ) || ' 00:00', 'DDMMYYYY HH24:MI' ) 
									AND COALESCE (
										TANGGAL_AKHIR,
									TO_TIMESTAMP( TO_CHAR( TANGGAL_AWAL, 'DDMMYYYY' ) || ' 23:59', 'DDMMYYYY HH24:MI' ))) THEN
									1 ELSE 0 
							END ELSE
							CASE
									WHEN (
										CURRENT_TIMESTAMP BETWEEN TANGGAL_AWAL 
										AND COALESCE (
											TANGGAL_AKHIR,
										TO_TIMESTAMP( TO_CHAR( TANGGAL_AWAL, 'DDMMYYYY' ) || ' 23:59', 'DDMMYYYY HH24:MI' ))) THEN
										1 ELSE 0 
									END 
									END AKTIF 
					FROM PAKET_TAHAP
					WHERE TAMPILKAN=1 
					ORDER BY URUT ASC
					) A
					JOIN PAKET B ON A.PAKET_ID=B.PAKET_ID
					JOIN PAKET_METODE_LELANG C ON B.PAKET_METODE_LELANG_ID=C.PAKET_METODE_LELANG_ID
					WHERE A.AKTIF='1'
			  ";

		while(list($key,$val) = each($paramsArray))
		{
			// ikn 20190218
		    $pecah = explode("||", $key);
		    if (count($pecah) > 1) {
		        $str .= "AND $pecah[0] $pecah[1] $val ";
		    } else {
		        $str .= " AND $key = '$val' ";
		    }
		}
		$str .= $statement." GROUP BY B.PAKET_ID, B.NAMA, B.NILAI, B.PAKET_METODE_LELANG_ID, C.NAMA ";
		// echo $str; die();
		$this->query = $str;
        $rs = $this->selectLimit($str,$limit,$from);

		//	print_r($rs);
        return  $rs;
    }
    /**
    * Hitung jumlah record berdasarkan parameter (array).
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy")
    * @return long Jumlah record yang sesuai kriteria
    **/


    function getJenisTahapById($reqId)
	{
		$str = "SELECT JENIS_TAHAP FROM METODE A, PAKET B WHERE
			A.PAKET_JENIS_ID = B.PAKET_JENIS_ID AND
			A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID AND
			A.PAKET_METODE_KUALIFIKASI_ID = B.PAKET_METODE_KUALIFIKASI_ID AND
            A.SISTEM_SAMPUL = B.SISTEM_SAMPUL
			AND B.PAKET_ID = '".$reqId."' ";


		$this->select($str);
		if($this->firstRow())
			return $this->getField("JENIS_TAHAP");
		else
			return 0;
    }

    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(PAKET_TAHAP_ID) AS ROWCOUNT FROM PAKET_TAHAP WHERE PAKET_TAHAP_ID IS NOT NULL ".$statement;
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die();
		$this->select($str);

		$this->query = $str;
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getCountByParamsAktif($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(PAKET_TAHAP_ID) AS ROWCOUNT FROM PAKET_TAHAP WHERE PAKET_TAHAP_ID IS NOT NULL
				AND (CURRENT_TIMESTAMP BETWEEN TANGGAL_AWAL
									AND COALESCE(TANGGAL_AKHIR, TO_TIMESTAMP(TO_CHAR(TANGGAL_AWAL, 'DDMMYYYY') || ' 23:59', 'DDMMYYYY HH24:MI')))
			   ".$statement;
		// $str = "SELECT COUNT(PAKET_TAHAP_ID) AS ROWCOUNT FROM PAKET_TAHAP WHERE PAKET_TAHAP_ID IS NOT NULL
		// 		AND (CURRENT_TIMESTAMP BETWEEN TO_TIMESTAMP(TO_CHAR(TANGGAL_AWAL, 'DDMMYYYY') || ' 00:00', 'DDMMYYYY HH24:MI') AND COALESCE(TANGGAL_AKHIR, TO_TIMESTAMP(TO_CHAR(TANGGAL_AWAL, 'DDMMYYYY') || ' 23:59', 'DDMMYYYY HH24:MI')))
		// 	   ".$statement;
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die();
		$this->query = $str;
		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getCountByParamsTanggal($reqTanggal, $paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(PAKET_TAHAP_ID) AS ROWCOUNT FROM PAKET_TAHAP A INNER JOIN PAKET B ON A.PAKET_ID = B.PAKET_ID
				WHERE PAKET_TAHAP_ID IS NOT NULL
				AND (TO_DATE('".$reqTanggal."', 'DDMMYYYY') BETWEEN TANGGAL_AWAL AND COALESCE(TANGGAL_AKHIR, TO_TIMESTAMP(TO_CHAR(TANGGAL_AWAL, 'DDMMYYYY') || ' 23:59', 'DDMMYYYY HH24:MI')) OR
					 (tanggal_awal) = (TO_DATE('".$reqTanggal."', 'DDMMYYYY')) or
					 TO_CHAR(tanggal_awal, 'DDMMYYYY') = '".$reqTanggal."' or
					 (COALESCE(tanggal_akhir,tanggal_awal)) = (TO_DATE('".$reqTanggal."', 'DDMMYYYY')))
				AND TO_DATE('".$reqTanggal."', 'DDMMYYYY') >= CURRENT_DATE
			   ".$statement;
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		//echo $str."<br><br><br>";
		$this->query = $str;
		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getCountByParamsBerlalu($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(PAKET_TAHAP_ID) AS ROWCOUNT FROM PAKET_TAHAP
				WHERE PAKET_TAHAP_ID IS NOT NULL AND (CURRENT_TIMESTAMP > COALESCE(TANGGAL_AKHIR, TO_TIMESTAMP(TO_CHAR(TANGGAL_AWAL, 'DDMMYYYY') || ' 23:59', 'DDMMYYYY HH24:MI'))) ".$statement; 		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		//echo $str;


		$this->query = $str;
		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(PAKET_TAHAP_ID) AS ROWCOUNT FROM PAKET_TAHAP WHERE PAKET_TAHAP_ID IS NOT NULL ";
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
