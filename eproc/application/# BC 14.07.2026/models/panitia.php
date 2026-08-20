<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Panitia extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
		parent::__construct();
	}

    function Panitia()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PANITIA_ID", $this->getNextId("PANITIA_ID","PANITIA"));

		$str = "
				INSERT INTO PANITIA (
					PANITIA_ID,
					SK_PANITIA_ID,
					NIP,
					NAMA,
					JABATAN,
					STATUS,
					KETUA)
				VALUES ( '".$this->getField("PANITIA_ID")."',
					'".$this->getField("SK_PANITIA_ID")."',
					'".$this->getField("NIP")."',
					'".$this->getField("NAMA")."',
					'".$this->getField("JABATAN")."',
					'".$this->getField("STATUS")."',
					'".$this->getField("KETUA")."')
		";
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PANITIA
				SET
					SK_PANITIA_ID = '".$this->getField("SK_PANITIA_ID")."',
					NIP = '".$this->getField("NIP")."',
					NAMA = '".$this->getField("NAMA")."',
					JABATAN = '".$this->getField("JABATAN")."',
					STATUS = '".$this->getField("STATUS")."',
					KETUA = '".$this->getField("KETUA")."'
				WHERE PANITIA_ID = '".$this->getField("PANITIA_ID")."'
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM PANITIA
                WHERE
                  PANITIA_ID = '".$this->getField("PANITIA_ID")."'";

		$this->query = $str;
        return $this->execQuery($str);
    }

	function deleteParent()
	{
        $str = "DELETE FROM PANITIA
                WHERE
                  SK_PANITIA_ID = ".$this->getField("SK_PANITIA_ID")."";

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
				PANITIA_ID,
				SK_PANITIA_ID,
				NIP,
				NAMA,
				JABATAN,
				STATUS,
				KETUA,
				CASE WHEN KETUA = '1' THEN 'Ketua' WHEN KETUA = '0' THEN 'Anggota' ELSE 'Penyelia' END FUNGSI
			FROM PANITIA
			WHERE PANITIA_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY PANITIA_ID ASC";

		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsCari($paramsArray=array(),$limit=-1,$from=-1, $id='', $state='')
	{
		$str = "
            SELECT
				PANITIA_ID,
				A.SK_PANITIA_ID,
				NIP,
				NAMA,
				JABATAN,
				A.STATUS,
				CASE WHEN KETUA = '1' THEN 'Ketua' ELSE 'Anggota'END FUNGSI,
				COALESCE(KETUA, '0') KETUA
			FROM PANITIA A INNER JOIN SK_PANITIA_TERAKHIR B ON A.SK_PANITIA_ID = B.SK_PANITIA_ID
			WHERE PANITIA_ID IS NOT NULL AND EXISTS (SELECT 1 FROM SK_PANITIA X WHERE STATUS = 1 AND X.SK_PANITIA_ID = A.SK_PANITIA_ID)
			AND NOT EXISTS (SELECT 1 FROM PAKET_PANITIA X WHERE A.NIP = X.NIP AND PAKET_ID = ".$id.")
		".$state;

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= " ORDER BY PANITIA_ID ASC";

		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsCari2($paramsArray=array(),$limit=-1,$from=-1, $id='', $state='')
	{
		$str = "
            SELECT
				PANITIA_ID,
				A.SK_PANITIA_ID,
				A.NIP,
				NAMA,
				JABATAN,
				A.STATUS,
				CASE WHEN KETUA = '1' THEN 'Ketua' ELSE 'Anggota'END FUNGSI,
				COALESCE(KETUA, '0') KETUA,
				C.USER_JABATAN_PANITIA_STR JABATAN_STR,
			 (SELECT COUNT(1) FROM PAKET_PANITIA Z WHERE A.NIP=Z.NIP) BEBAN_PAKET_PANITIA,
			 (SELECT COUNT(1) FROM PAKET_PANITIA Z JOIN VIEW_DASHBOARD_PAKET_PROSES V ON Z.PAKET_ID=V.PAKET_ID WHERE A.NIP=Z.NIP AND V.PROSES='1') BEBAN_PAKET_PANITIA_PROSES
			FROM PANITIA A INNER JOIN SK_PANITIA B ON A.SK_PANITIA_ID = B.SK_PANITIA_ID
			LEFT JOIN USER_LOGIN C ON A.NIP=C.NIP
			WHERE PANITIA_ID IS NOT NULL AND EXISTS (SELECT 1 FROM SK_PANITIA X WHERE STATUS = 1 AND X.SK_PANITIA_ID = A.SK_PANITIA_ID)
			AND NOT EXISTS (SELECT 1 FROM PAKET_PANITIA X WHERE A.NIP = X.NIP AND PAKET_ID = ".$id.") AND C.USER_JABATAN_PANITIA_STR != 'Penyelia'
		".$state;

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= " ORDER BY PANITIA_ID ASC";
				// echo $str; die();
		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT
				PANITIA_ID,
				SK_PANITIA_ID,
				NIP,
				NAMA,
				JABATAN,
				STATUS
			FROM PANITIA
			WHERE PANITIA_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY PANITIA_ID ASC";
		return $this->selectLimit($str,$limit,$from);
    }
    /**
    * Hitung jumlah record berdasarkan parameter (array).
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy")
    * @return long Jumlah record yang sesuai kriteria
    **/
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(PANITIA_ID) AS ROWCOUNT FROM PANITIA WHERE PANITIA_ID IS NOT NULL ";
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
		$str = "SELECT COUNT(PANITIA_ID) AS ROWCOUNT FROM PANITIA WHERE PANITIA_ID IS NOT NULL ";
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
