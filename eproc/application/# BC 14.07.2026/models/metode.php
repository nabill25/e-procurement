<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class Metode extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
		parent::__construct();
	}
	
    function Metode()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_TAHAP_ID", $this->getNextId("PAKET_TAHAP_ID","PAKET_TAHAP"));

		$str = "
				INSERT INTO PAKET_TAHAP (
				   PAKET_TAHAP_ID, PAKET_ID, NAMA, AKTIVITAS,
				   HADIR, TAMPILKAN, TANGGAL_AWAL,
				   TANGGAL_AKHIR, URUT, JAM_AWAL, JAM_AKHIR, CREATED_BY, CREATED_DATE, CEK_TANGGAL_CLASH)
				VALUES ('".$this->getField("PAKET_TAHAP_ID")."', '".$this->getField("PAKET_ID")."',
					'".$this->getField("NAMA")."', '".$this->getField("AKTIVITAS")."', '".$this->getField("HADIR")."', '".$this->getField("TAMPILKAN")."',
					".$this->getField("TANGGAL_AWAL").", ".$this->getField("TANGGAL_AKHIR").", '".$this->getField("URUT")."', '".$this->getField("JAM_AWAL")."', '".$this->getField("JAM_AKHIR")."', ".$this->getField("CREATED_BY").", CURRENT_TIMESTAMP, '".$this->getField("CEK_TANGGAL_CLASH")."')
		";

		$this->query = $str;
		return $this->execQuery($str);
    }

	function reschedule()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_RESCHEDULE_ID", $this->getNextId("PAKET_RESCHEDULE_ID","PAKET_RESCHEDULE"));

		$str = "
				INSERT INTO PAKET_RESCHEDULE(
							PAKET_RESCHEDULE_ID, PAKET_TAHAP_ID, PAKET_ID,
							NAMA, URUT,
							TANGGAL_AWAL,
							TANGGAL_AKHIR,
							JAM_AWAL,
							JAM_AKHIR,
							TANGGAL_AWAL_BARU, TANGGAL_AKHIR_BARU, JAM_AWAL_BARU, JAM_AKHIR_BARU, RESCHEDULE_KE)
				VALUES ('".$this->getField("PAKET_RESCHEDULE_ID")."', '".$this->getField("PAKET_TAHAP_ID")."', '".$this->getField("PAKET_ID")."',
					'".$this->getField("NAMA")."', '".$this->getField("URUT")."',
					(SELECT TANGGAL_AWAL FROM PAKET_TAHAP X WHERE X.PAKET_TAHAP_ID = '".$this->getField("PAKET_TAHAP_ID")."'),
					(SELECT TANGGAL_AKHIR FROM PAKET_TAHAP X WHERE X.PAKET_TAHAP_ID = '".$this->getField("PAKET_TAHAP_ID")."'),
					(SELECT JAM_AWAL FROM PAKET_TAHAP X WHERE X.PAKET_TAHAP_ID = '".$this->getField("PAKET_TAHAP_ID")."'),
					(SELECT JAM_AKHIR FROM PAKET_TAHAP X WHERE X.PAKET_TAHAP_ID = '".$this->getField("PAKET_TAHAP_ID")."'),
					".$this->getField("TANGGAL_AWAL").", ".$this->getField("TANGGAL_AKHIR").", '".$this->getField("JAM_AWAL")."', '".$this->getField("JAM_AKHIR")."', '".$this->getField("RESCHEDULE_KE")."')
		";
		$this->query = $str;
		return $this->execQuery($str);
    }


	function insertJadwalUlang()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_TAHAP_ULANG_ID", $this->getNextId("PAKET_TAHAP_ULANG_ID","PAKET_TAHAP_ULANG"));

		$str = "
				INSERT INTO PAKET_TAHAP_ULANG (
				   PAKET_TAHAP_ULANG_ID, PAKET_ID, NAMA,
				   HADIR, TAMPILKAN, TANGGAL_AWAL,
				   TANGGAL_AKHIR, URUT, JAM_AWAL, JAM_AKHIR)
				VALUES ('".$this->getField("PAKET_TAHAP_ULANG_ID")."', '".$this->getField("PAKET_ID")."',
					'".$this->getField("NAMA")."', '".$this->getField("HADIR")."', '".$this->getField("TAMPILKAN")."',
					".$this->getField("TANGGAL_AWAL").", ".$this->getField("TANGGAL_AKHIR").", '".$this->getField("URUT")."', '".$this->getField("JAM_AWAL")."', '".$this->getField("JAM_AKHIR")."')
		";

		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_TAHAP SET
				  TAMPILKAN = 1,
				  TANGGAL_AWAL = ".$this->getField("TANGGAL_AWAL").",
				  JAM_AWAL = '".$this->getField("JAM_AWAL")."'
				WHERE PAKET_TAHAP_ID = '".$this->getField("PAKET_TAHAP_ID")."'
				";
				$this->query = $str;

		return $this->execQuery($str);
    }

    function updateRescheduleJadwal()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_TAHAP SET
				  TANGGAL_AWAL = ".$this->getField("TANGGAL_AWAL").",
				  JAM_AWAL = '".$this->getField("JAM_AWAL")."',
				  TANGGAL_AKHIR = ".$this->getField("TANGGAL_AKHIR").",
				  JAM_AKHIR = '".$this->getField("JAM_AKHIR")."'
				WHERE PAKET_TAHAP_ID = '".$this->getField("PAKET_TAHAP_ID")."'
				";
				$this->query = $str;

		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM PAKET_TAHAP
                WHERE
                  PAKET_ID = '".$this->getField("PAKET_ID")."'";

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
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $paket_id="")
	{
		$statement = '';
		$str = "
			SELECT B.PAKET_TAHAP_ID, A.URUT, A.TAMPILKAN, A.JENIS_TAHAP, A.NAMA, A.HADIR, B.HADIR HADIR_CENTANG,
				   B.TAMPILKAN TAMPILKAN_CENTANG, TO_CHAR(B.TANGGAL_AWAL, 'YYYY-MM-DD HH24:MI') TANGGAL_AWAL,
				   TO_CHAR(B.TANGGAL_AKHIR, 'YYYY-MM-DD HH24:MI') TANGGAL_AKHIR, JAM_AWAL, JAM_AKHIR, A.NOTIFIKASI, A.TANGGAL_AWAL_DISABLED, A.TANGGAL_AKHIR_TRIGGER,
				   B.TANGGAL_AWAL TANGGAL_AWAL_TAHAP, A.AKTIVITAS, A.CEK_TANGGAL_MERAH, A.TANGGAL_AKHIR_MANDATORY, A.CEK_TANGGAL_CLASH
	 	 	FROM METODE_TAHAP A LEFT JOIN PAKET_TAHAP B ON A.URUT = B.URUT AND B.PAKET_ID = '".$paket_id."' WHERE A.JENIS_TAHAP =
			(SELECT JENIS_TAHAP FROM METODE A, PAKET B WHERE
			A.PAKET_JENIS_ID = B.PAKET_JENIS_ID AND
			A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID AND
			A.PAKET_METODE_KUALIFIKASI_ID = B.PAKET_METODE_KUALIFIKASI_ID AND
			A.SISTEM_SAMPUL = B.SISTEM_SAMPUL
			AND B.PAKET_ID = '".$paket_id."') AND STATUS='1'
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

		$this->query = $str;
		$str .= $statement." ORDER BY URUT ASC";
		// echo $str; die();
		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsReschedule($paramsArray=array(),$limit=-1,$from=-1, $paket_id="", $statement="")
	{
		$str = "
			SELECT B.PAKET_TAHAP_ID, A.URUT, A.TAMPILKAN, A.JENIS_TAHAP, A.NAMA, A.HADIR, B.HADIR HADIR_CENTANG,
				   B.TAMPILKAN TAMPILKAN_CENTANG, TO_CHAR(B.TANGGAL_AWAL, 'YYYY-MM-DD HH24:MI') TANGGAL_AWAL,
				   TO_CHAR(B.TANGGAL_AKHIR, 'YYYY-MM-DD HH24:MI') TANGGAL_AKHIR, B.JAM_AWAL JAM_AWAL,
				   B.JAM_AKHIR JAM_AKHIR, A.NOTIFIKASI, A.TANGGAL_AWAL_DISABLED, A.TANGGAL_AKHIR_TRIGGER,
				   B.TANGGAL_AWAL TANGGAL_AWAL_TAHAP, A.AKTIVITAS
	 	 	FROM METODE_TAHAP A
	 	 	LEFT JOIN PAKET_TAHAP B ON A.NAMA = B.NAMA AND B.PAKET_ID = '".$paket_id."'
	 	 	WHERE A.JENIS_TAHAP =
			(SELECT JENIS_TAHAP FROM METODE A, PAKET B WHERE
			A.PAKET_JENIS_ID = B.PAKET_JENIS_ID AND
			A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID AND
			A.PAKET_METODE_KUALIFIKASI_ID = B.PAKET_METODE_KUALIFIKASI_ID AND
			A.SISTEM_SAMPUL = B.SISTEM_SAMPUL
			AND B.PAKET_ID = '".$paket_id."') AND STATUS='1'
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY URUT ASC";

		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsMetodeLelang($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = " SELECT DISTINCT A.PAKET_METODE_LELANG_ID, B.NAMA PAKET_METODE_LELANG FROM METODE A, PAKET_METODE_LELANG B WHERE A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
		// $str .= $statement." ORDER BY PAKET_METODE_LELANG_ID ASC";
		$str .= $statement." ORDER BY B.NAMA DESC";

		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsMetodeKualifikasi($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		// $str = " SELECT DISTINCT A.PAKET_METODE_KUALIFIKASI_ID, B.NAMA PAKET_METODE_KUALIFIKASI , PAKET_METODE_LELANG_ID
		// 		FROM METODE A, PAKET_METODE_KUALIFIKASI B WHERE A.PAKET_METODE_KUALIFIKASI_ID = B.PAKET_METODE_KUALIFIKASI_ID ";
		$str = " SELECT DISTINCT A.PAKET_METODE_KUALIFIKASI_ID, B.NAMA PAKET_METODE_KUALIFIKASI , PAKET_METODE_LELANG_ID
				FROM METODE A, PAKET_METODE_KUALIFIKASI B WHERE A.PAKET_METODE_KUALIFIKASI_ID = B.PAKET_METODE_KUALIFIKASI_ID AND A.AKTIF='1' ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_METODE_LELANG_ID ASC";
		// echo $str; exit;
		return $this->selectLimit($str,$limit,$from);
    }

    // Modif ikun 2018-08-05
    function selectByParamsMetodeKualifikasi2($paramsArray=array(),$limit=-1,$from=-1, $statement='')
  	{
  		$str = " SELECT DISTINCT A.PAKET_METODE_KUALIFIKASI_ID, B.NAMA PAKET_METODE_KUALIFIKASI , PAKET_METODE_LELANG_ID
  				FROM METODE A, PAKET_METODE_KUALIFIKASI B WHERE A.PAKET_METODE_KUALIFIKASI_ID = B.PAKET_METODE_KUALIFIKASI_ID AND A.PAKET_METODE_KUALIFIKASI_ID ='2' ";

  		while(list($key,$val) = each($paramsArray))
  		{
  			$str .= " AND $key = '$val' ";
  		}
  		$this->query = $str;
  		$str .= $statement." ORDER BY PAKET_METODE_LELANG_ID ASC";
  		return $this->selectLimit($str,$limit,$from);
    }
    // End Modif ikun 2018-08-05

    // Modif ikun 2019-08-02
    function selectByParamsMetodeEvaluasi2($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		// $str = " SELECT DISTINCT A.PAKET_METODE_EVALUASI_ID, B.NAMA PAKET_METODE_EVALUASI FROM MATRIX_EVALUASI A, PAKET_METODE_EVALUASI B WHERE A.PAKET_METODE_EVALUASI_ID = B.PAKET_METODE_EVALUASI_ID ";
		$str = " SELECT DISTINCT A.PAKET_METODE_EVALUASI_ID, B.NAMA PAKET_METODE_EVALUASI FROM MATRIX_EVALUASI A, PAKET_METODE_EVALUASI B WHERE A.PAKET_METODE_EVALUASI_ID = B.PAKET_METODE_EVALUASI_ID AND B.AKTIF='1' ";
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_METODE_EVALUASI_ID ASC";
		// echo $str; exit;
		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsMetodePenyampaian($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = " SELECT DISTINCT A.SISTEM_SAMPUL,
								CASE WHEN A.SISTEM_SAMPUL=1 THEN '1 File'
								ELSE '2 File'
								END NAMA
  				FROM METODE A, PAKET_METODE_KUALIFIKASI B WHERE A.PAKET_METODE_KUALIFIKASI_ID = B.PAKET_METODE_KUALIFIKASI_ID   ";

  		while(list($key,$val) = each($paramsArray))
  		{
  			$str .= " AND $key = '$val' ";
  		}
  		$this->query = $str;
  		$str .= $statement." ";
  		// echo $str; exit();
  		return $this->selectLimit($str,$limit,$from);
    }
    // End Modif ikun 2019-08-02

	function selectByParamsMetodeEvaluasi($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		// $str = " SELECT DISTINCT A.PAKET_METODE_EVALUASI_ID, B.NAMA PAKET_METODE_EVALUASI FROM MATRIX_EVALUASI A, PAKET_METODE_EVALUASI B WHERE A.PAKET_METODE_EVALUASI_ID = B.PAKET_METODE_EVALUASI_ID ";
		$str = " SELECT DISTINCT A.PAKET_METODE_EVALUASI_ID, B.NAMA PAKET_METODE_EVALUASI FROM MATRIX_EVALUASI A, PAKET_METODE_EVALUASI B WHERE A.PAKET_METODE_EVALUASI_ID = B.PAKET_METODE_EVALUASI_ID AND B.AKTIF='1'";
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_METODE_EVALUASI_ID ASC";
		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT AGAMA_ID, NAMA
				FROM AGAMA WHERE AGAMA_ID IS NOT NULL";

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
  function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(PAKET_TAHAP_ID) ROWCOUNT FROM PAKET_TAHAP WHERE 1 = 1 ";
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

  function selectByParamsJadwalClash($tgl, $paketid)
	{
		$str = " SELECT A.* FROM (
						SELECT A.PAKET_TAHAP_ID, A.PAKET_ID, A.NAMA, A.URUT, 
						TO_CHAR(A.TANGGAL_AWAL, 'DD-MM-YYYY') TANGGAL_AWAL2, A.JAM_AWAL,
						TO_CHAR(A.TANGGAL_AKHIR, 'DD-MM-YYYY') TANGGAL_AKHIR2, A.JAM_AKHIR,
						TO_CHAR(A.TANGGAL_AWAL, 'DD-MM-YYYY HH24:MI') TANGGAL_AWAL, TO_CHAR(A.TANGGAL_AKHIR, 'DD-MM-YYYY HH24:MI') TANGGAL_AKHIR, B.NAMA NAMA_PAKET
						FROM PAKET_TAHAP A 
						JOIN PAKET B ON A.PAKET_ID=B.PAKET_ID
						WHERE CEK_TANGGAL_CLASH = '1' ) A 
						WHERE '".$tgl."' BETWEEN A.TANGGAL_AWAL AND A.TANGGAL_AKHIR AND A.PAKET_ID != ".$paketid." "; 
		$this->query = $str;

		return $this->selectLimit($str,$limit,$from);
  }

  function selectByParamsJadwalClash2($tgl, $paketid, $user_login_id)
  {
  	$str = "SELECT A.* FROM 
						(
						SELECT a.PAKET_TAHAP_ID, A.PAKET_ID, A.NAMA,
						TO_CHAR(A.TANGGAL_AWAL, 'DD-MM-YYYY') TANGGAL_AWAL2,
						TO_CHAR(A.TANGGAL_AKHIR, 'DD-MM-YYYY') TANGGAL_AKHIR2,
						TO_CHAR(A.TANGGAL_AWAL, 'DD-MM-YYYY HH24:MI') TANGGAL_AWAL, TO_CHAR(A.TANGGAL_AKHIR, 'DD-MM-YYYY HH24:MI') TANGGAL_AKHIR,
						A.URUT, A.JAM_AWAL, A.JAM_AKHIR, A.CREATED_BY, A.CREATED_DATE,
						(SELECT ARRAY_AGG ( DISTINCT X.USER_LOGIN_ID ) PAKET_PANITIA_ID FROM PAKET_PANITIA v JOIN USER_LOGIN X ON V.NIP=X.NIP WHERE A.PAKET_ID=V.PAKET_ID) PAKET_PANITIA_ID,
						(SELECT X.USER_LOGIN_ID FROM PAKET X WHERE A.PAKET_ID=X.PAKET_ID) PAKET_PENYELIA_ID,
					         B.NAMA NAMA_PAKET
						FROM PAKET_TAHAP a
						JOIN PAKET B ON a.PAKET_ID=B.PAKET_ID
						WHERE a.CEK_TANGGAL_CLASH = '1'
						) A 
						WHERE (A.PAKET_PANITIA_ID && ARRAY[".$user_login_id."] OR A.PAKET_PENYELIA_ID = ".$user_login_id." ) AND '".$tgl."' BETWEEN A.TANGGAL_AWAL AND A.TANGGAL_AKHIR AND A.PAKET_ID != ".$paketid."
  				 ";  	

$this->query = $str;

		return $this->selectLimit($str,$limit,$from);
  }

  function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(AGAMA_ID) AS ROWCOUNT FROM AGAMA WHERE AGAMA_ID IS NOT NULL ";
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

    function selectByParamsMatrix($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="ORDER BY METODE_ID ASC")
	{
		$statement = '';
		$str = "
		  SELECT a.* FROM (
				SELECT a.metode_id, 
				a.paket_jenis_id, b.nama paket_jenis_nama,
				a.paket_metode_lelang_id, c.nama paket_metode_lelang_nama,
				a.jenis_tahap, (select count(metode_tahap_id) total from metode_tahap aa where aa.jenis_tahap=a.jenis_tahap) total_tahap,
				a.paket_metode_kualifikasi_id, d.nama paket_metode_kualifikasi_nama,a.sistem_sampul,
				a.aktif
				from metode a 
				join paket_jenis b on a.paket_jenis_id=b.paket_jenis_id
				join paket_metode_lelang c on a.paket_metode_lelang_id=c.paket_metode_lelang_id
				join paket_metode_kualifikasi d on a.paket_metode_kualifikasi_id=d.paket_metode_kualifikasi_id 
			) a
			WHERE 1=1
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

		$this->query = $str;
		$str .= $statement." ORDER BY METODE_ID ASC";
		// echo $str; die();
		return $this->selectLimit($str,$limit,$from);
    }

    function getCountByParamsMatrix($paramsArray=array())
	{
		$str = "SELECT COUNT(METODE_ID) ROWCOUNT FROM METODE WHERE 1 = 1 ";
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
