<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

  include_once('entity.php');

  class RekananNeraca extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
  		parent::__construct();
  	}

    function RekananNeraca()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_NERACA_ID", $this->getNextId("REKANAN_NERACA_ID","REKANAN_NERACA"));

		$str = "
				INSERT INTO REKANAN_NERACA (
					REKANAN_NERACA_ID,
					REKANAN_ID,
					TAHUN,
					AKTIVA,
					PASIVA,
					MODAL,
					AUDIT_NAMA,
					AUDIT_NOMOR,
					AUDIT_TANGGAL,
					AUDIT_KESIMPULAN,
					PATH_FILE, TIPE, UKURAN, NAMA_FILE, PATH_FILE2, TIPE2, UKURAN2, NAMA_FILE2, CREATED_BY, CREATED_DATE)
				VALUES ( '".$this->getField("REKANAN_NERACA_ID")."',
					'".$this->getField("REKANAN_ID")."',
					'".$this->getField("TAHUN")."',
					".$this->getField("AKTIVA").",
					".$this->getField("PASIVA").",
					'".$this->getField("MODAL")."',
					'".$this->getField("AUDIT_NAMA")."',
					'".$this->getField("AUDIT_NOMOR")."',
					".$this->getField("AUDIT_TANGGAL").",
					'".$this->getField("AUDIT_KESIMPULAN")."',
					'".$this->getField("PATH_FILE")."',
					'".$this->getField("TIPE")."',
					'".$this->getField("UKURAN")."',
					'".$this->getField("NAMA_FILE")."',
					'".$this->getField("PATH_FILE2")."',
					'".$this->getField("TIPE2")."',
					'".$this->getField("UKURAN2")."',
					'".$this->getField("NAMA_FILE2")."',
					".$this->getField("CREATED_BY").",
					CURRENT_TIMESTAMP
					)
		";
				// echo $str; die();
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_NERACA
				SET
					AKTIVA = ".$this->getField("AKTIVA").",
					PASIVA = ".$this->getField("PASIVA").",
					MODAL = '".$this->getField("MODAL")."',
					AUDIT_NAMA = '".$this->getField("AUDIT_NAMA")."',
					AUDIT_NOMOR = '".$this->getField("AUDIT_NOMOR")."',
					AUDIT_TANGGAL = ".$this->getField("AUDIT_TANGGAL").",
					AUDIT_KESIMPULAN = '".$this->getField("AUDIT_KESIMPULAN")."',
					PATH_FILE = '".$this->getField("PATH_FILE")."',
					TIPE = '".$this->getField("TIPE")."',
					UKURAN = '".$this->getField("UKURAN")."',
					NAMA_FILE	= '".$this->getField("NAMA_FILE")."',
					PATH_FILE2 = '".$this->getField("PATH_FILE2")."',
					TIPE2 = '".$this->getField("TIPE2")."',
					UKURAN2 = '".$this->getField("UKURAN2")."',
					NAMA_FILE2	= '".$this->getField("NAMA_FILE2")."',
					UPDATED_BY	= ".$this->getField("CREATED_BY").",
					UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE REKANAN_NERACA_ID = '".$this->getField("REKANAN_NERACA_ID")."' AND REKANAN_ID = '".$this->getField("REKANAN_ID")."'
				";
				// echo $str; die;
				$this->query = $str;
		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM REKANAN_NERACA
                WHERE
                  REKANAN_NERACA_ID = '".$this->getField("REKANAN_NERACA_ID")."'";

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
					REKANAN_NERACA_ID,
					REKANAN_ID,
					TAHUN,
					AKTIVA,
					PASIVA,
					MODAL,
					AUDIT_NAMA,
					AUDIT_NOMOR,
					TO_CHAR(AUDIT_TANGGAL, 'YYYY-MM-DD') AUDIT_TANGGAL,
					AUDIT_KESIMPULAN,
          PATH_FILE, TIPE, UKURAN, NAMA_FILE,
					PATH_FILE2, TIPE2, UKURAN2, NAMA_FILE2
				FROM REKANAN_NERACA
				WHERE REKANAN_NERACA_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY TAHUN DESC";

		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsNeracaTerakhir($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				SELECT MODAL, AUDIT_NAMA, AUDIT_NOMOR, AUDIT_TANGGAL
				 FROM REKANAN_NERACA A INNER JOIN
				   (SELECT REKANAN_ID, MAX(TAHUN) TAHUN FROM REKANAN_NERACA GROUP BY REKANAN_ID) B
				   ON A.REKANAN_ID = B.REKANAN_ID AND A.TAHUN = B.TAHUN
   		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement;

		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsTahun($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT
				REKANAN_ID,
				TAHUN
			FROM REKANAN_NERACA
			WHERE 1=1
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." GROUP BY TAHUN, REKANAN_ID ORDER BY TAHUN DESC";
		//echo $str;
		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT
				REKANAN_NERACA_ID,
				REKANAN_ID,
				TAHUN,
				AKTIVA,
				PASIVA,
				MODAL,
				AUDIT_NAMA,
				AUDIT_NOMOR,
				AUDIT_TANGGAL,
				AUDIT_KESIMPULAN,
				PATH_FILE, TIPE, UKURAN, NAMA_FILE
			FROM REKANAN_NERACA
			WHERE REKANAN_NERACA_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_NERACA_ID ASC";
		return $this->selectLimit($str,$limit,$from);
    }
    /**
    * Hitung jumlah record berdasarkan parameter (array).
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy")
    * @return long Jumlah record yang sesuai kriteria
    **/
	function getCountByParamsTahun($paramsArray=array())
	{
		$str = "SELECT COUNT(REKANAN_NERACA_ID) AS ROWCOUNT FROM REKANAN_NERACA WHERE REKANAN_NERACA_ID IS NOT NULL ";
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= " GROUP BY TAHUN";
		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getCountByParams($paramsArray=array(), $statement="")
	{
		$str = "SELECT COUNT(REKANAN_NERACA_ID) AS ROWCOUNT FROM REKANAN_NERACA WHERE REKANAN_NERACA_ID IS NOT NULL ".$statement;
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
		$str = "SELECT COUNT(REKANAN_NERACA_ID) AS ROWCOUNT FROM REKANAN_NERACA WHERE REKANAN_NERACA_ID IS NOT NULL ";
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
