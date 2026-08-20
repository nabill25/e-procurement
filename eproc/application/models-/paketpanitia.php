<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

  include_once('entity.php');

  class PaketPanitia extends Entity{

	var $query;
    /**
    * Class constructor.
    **/

    function __construct(){
	  parent::__construct();
	}

    function PaketPanitia()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_PANITIA_ID", $this->getNextId("PAKET_PANITIA_ID","PAKET_PANITIA"));

		$str = "
				INSERT INTO PAKET_PANITIA (
					PAKET_PANITIA_ID,
					PAKET_PANITIA_SK_ID,
					NAMA,
					NIP,
					STATUS,
					JABATAN,
					PAKET_ID,
					KETUA, CREATED_BY, CREATED_DATE)
				VALUES ( '".$this->getField("PAKET_PANITIA_ID")."',
					".$this->getField("PAKET_PANITIA_SK_ID").",
					'".$this->getField("NAMA")."',
					'".$this->getField("NIP")."',
					'".$this->getField("STATUS")."',
					'".$this->getField("JABATAN")."',
					'".$this->getField("PAKET_ID")."',
					'".$this->getField("KETUA")."',
					".$this->getField("CREATED_BY").",
					CURRENT_TIMESTAMP)
		";

		$this->query = $str;
		// echo $str; die;
		return $this->execQuery($str);
    }

  function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_PANITIA
				SET
					PAKET_PANITIA_SK_ID = ".$this->getField("PAKET_PANITIA_SK_ID").",
					NAMA = '".$this->getField("NAMA")."',
					NIP = '".$this->getField("NIP")."',
					STATUS = '".$this->getField("STATUS")."',
					JABATAN = '".$this->getField("JABATAN")."',
					PAKET_ID = '".$this->getField("PAKET_ID")."',
					KETUA = '".$this->getField("KETUA")."'
				WHERE PAKET_PANITIA_ID = '".$this->getField("PAKET_PANITIA_ID")."'
				";
				$this->query = $str;
		return $this->execQuery($str);
   }

	function updateValidasi()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_PANITIA
				SET
					VALIDASI_PUBLISH = '".$this->getField("VALIDASI_PUBLISH")."', VALIDASI_PUBLISH_DATE = CURRENT_TIMESTAMP
				WHERE PAKET_ID = '".$this->getField("PAKET_ID")."' AND NIP = '".$this->getField("NIP")."'
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

  function updateValidasiPemenang()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_PANITIA
				SET
					VALIDASI_PEMENANG = '".$this->getField("VALIDASI_PEMENANG")."',
					VALIDASI_PEMENANG_DATE = CURRENT_TIMESTAMP
				WHERE PAKET_ID = '".$this->getField("PAKET_ID")."' AND NIP = '".$this->getField("NIP")."' AND PAKET_PANITIA_ID = '".$this->getField("PAKET_PANITIA_ID")."'
				";
				// echo $str; die();
				$this->query = $str;
		return $this->execQuery($str);
  }

  function updateValidasiHasilKualifikasi()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_PANITIA
				SET
					VALIDASI_HASIL_KUALIFIKASI = '".$this->getField("VALIDASI_HASIL_KUALIFIKASI")."',
					VALIDASI_HASIL_KUALIFIKASI_DATE = CURRENT_TIMESTAMP
				WHERE PAKET_ID = '".$this->getField("PAKET_ID")."' AND NIP = '".$this->getField("NIP")."'
				";
				$this->query = $str;
		return $this->execQuery($str);
  }

  function updateKetua()
	{
		/*Auto-generate primary key(s) by next max value (integer) */

		$str = "UPDATE PAKET_PANITIA SET KETUA = '0' WHERE PAKET_ID = '".$this->getField("PAKET_ID")."'";
		$this->query = $str;
		if($this->execQuery($str)) {
			$str2 = "UPDATE PAKET_PANITIA
					SET
						KETUA = '".$this->getField("KETUA")."',
						UPDATED_BY = ".$this->getField("UPDATED_BY").",
						UPDATED_DATE = CURRENT_TIMESTAMP
					WHERE PAKET_ID = '".$this->getField("PAKET_ID")."' AND NIP = '".$this->getField("NIP")."'
					";
					$this->query = $str2;
			return $this->execQuery($str2);
		} else {
			return false;
		}

  }

  function updateKunciTimPengadaan()
	{
		$str2 = "UPDATE PAKET_PANITIA
				SET
					KUNCI_PANITIA = '1',
					KUNCI_PANITIA_DATE = CURRENT_TIMESTAMP,
					UPDATED_BY = ".$this->getField("UPDATED_BY").",
					UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE PAKET_ID = '".$this->getField("PAKET_ID")."'
				";
				$this->query = $str2;
		return $this->execQuery($str2);
  }

  function updateValidasiPemenangTolak()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_PANITIA
				SET
					VALIDASI_PEMENANG = '".$this->getField("VALIDASI_PEMENANG")."',
					VALIDASI_PEMENANG_CATATAN = '".$this->getField("VALIDASI_PEMENANG_CATATAN")."',
					VALIDASI_PEMENANG_DATE = CURRENT_TIMESTAMP
				WHERE PAKET_ID = '".$this->getField("PAKET_ID")."' AND NIP = '".$this->getField("NIP")."'
				";
				// echo $str; die();
				$this->query = $str;
		return $this->execQuery($str);
  }

  function beforeDelete()
	{
    /*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_PANITIA_ID", $this->getNextId("PAKET_PANITIA_ID","PAKET_PANITIA_BACKUP"));

		$str = "
				INSERT INTO PAKET_PANITIA_BACKUP SELECT * FROM PAKET_PANITIA WHERE PAKET_ID = ".$this->getField("PAKET_ID")."
		";
				  // echo $str; die;
		$this->query = $str;
    return $this->execQuery($str);
  }

	function delete()
	{
        $str = "DELETE FROM PAKET_PANITIA
                WHERE
                  PAKET_ID = '".$this->getField("PAKET_ID")."'";

		$this->query = $str;
        return $this->execQuery($str);
  }

    // ikn 20190306
    function deleteByPaktaIntegritas()
	{
        $str = "DELETE
				FROM
					PAKET_PANITIA A
				WHERE
					A.NIP NOT IN (
					SELECT B.KODE FROM PAKET_PAKTA_INTEGRITAS B
					WHERE A.PAKET_ID = B.PAKET_ID AND A.NIP = B.KODE
					)
                  AND A.PAKET_ID = '".$this->getField("PAKET_ID")."'";
				  // echo $str; die();
		$this->query = $str;
        return $this->execQuery($str);
    }

	function deletePanitia()
	{
        $str = "DELETE FROM PAKET_PANITIA
                WHERE
                  PAKET_PANITIA_ID = '".$this->getField("PAKET_PANITIA_ID")."'";

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

    // ikn edit 20190306
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		// $str = "
		// 	SELECT
		// 		PAKET_PANITIA_ID,
		// 		PAKET_PANITIA_SK_ID,
		// 		NAMA,
		// 		NIP,
		// 		STATUS,
		// 		JABATAN,
		// 		PAKET_ID,
		// 		KETUA,
		// 		CASE WHEN KETUA = '1' THEN 'Ketua' ELSE 'Anggota'END FUNGSI
		// 	FROM PAKET_PANITIA
		// 	WHERE PAKET_PANITIA_ID IS NOT NULL
		// ";

		$str = "
			SELECT
				A.PAKET_PANITIA_ID,
				A.PAKET_PANITIA_SK_ID,
				A.NAMA,
				A.NIP,
				A.STATUS,
				A.JABATAN,
				A.PAKET_ID,
				A.KETUA,
				A.VALIDASI_PUBLISH,
				A.VALIDASI_PUBLISH_DATE,
				CASE WHEN KETUA = '1' THEN 'Ketua' ELSE 'Anggota'END FUNGSI,
				A.VALIDASI_PEMENANG,
				B.KODE,
				B.KODE_QR,
				A.VALIDASI_PEMENANG_CATATAN,
				A.VALIDASI_HASIL_KUALIFIKASI,
				A.VALIDASI_HASIL_KUALIFIKASI_DATE
			FROM PAKET_PANITIA A LEFT JOIN PAKET_PAKTA_INTEGRITAS B ON A.PAKET_ID = B.PAKET_ID AND A.NIP = B.KODE
			WHERE PAKET_PANITIA_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
		$str .= $statement." ORDER BY COALESCE(KETUA, '0') DESC ";
				// echo $str;
		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParams2($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{

		$str = "
			SELECT A.* FROM (
			SELECT
				A.NAMA,
				A.NIP,
				A.JABATAN,
				A.PAKET_ID,
				C.USER_JABATAN_PANITIA,
				C.USER_JABATAN_PANITIA_STR,
				'ANGGOTA' FUNGSI,
				B.KODE,
				A.KETUA
			FROM PAKET_PANITIA A LEFT JOIN PAKET_PAKTA_INTEGRITAS B ON A.PAKET_ID = B.PAKET_ID AND A.NIP = B.KODE
			LEFT JOIN USER_LOGIN C ON A.NIP=C.NIP
			WHERE PAKET_PANITIA_ID IS NOT NULL	 and c.user_jabatan_panitia is not null
			UNION ALL
					SELECT C.USER_NAMA NAMA, CAST(C.NIP AS TEXT) NIP, C.USER_JABATAN JABATAN, PAKET_ID, C.USER_JABATAN_PANITIA,
					C.USER_JABATAN_PANITIA_STR, 'PEMBUAT' FUNGSI, '' KODE, '' KETUA
				FROM PAKET A
				LEFT JOIN USER_LOGIN C ON C.USER_LOGIN_ID = A.USER_LOGIN_ID
			) A WHERE 1=1
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
		$str .= $statement." ORDER BY A.KETUA DESC ";

		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParams2Group($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{

		$str = "
			SELECT A.* FROM (
			SELECT
				A.NAMA,
				A.NIP,
				A.JABATAN,
				A.PAKET_ID,
				'ANGGOTA' FUNGSI,
				B.KODE,
				A.KETUA,
				A.KUNCI_PANITIA
			FROM PAKET_PANITIA A LEFT JOIN PAKET_PAKTA_INTEGRITAS B ON A.PAKET_ID = B.PAKET_ID AND A.NIP = B.KODE
			LEFT JOIN USER_LOGIN C ON A.NIP=C.NIP AND C.USER_TYPE_ID='3'
			WHERE PAKET_PANITIA_ID IS NOT NULL	 and c.user_jabatan_panitia is not null
			UNION ALL
					SELECT C.USER_NAMA NAMA, CAST(C.NIP AS TEXT) NIP, C.USER_JABATAN JABATAN, PAKET_ID, 'PEMBUAT' FUNGSI, '' KODE, '' KETUA, '' KUNCI_PANITIA
				FROM PAKET A
				LEFT JOIN USER_LOGIN C ON C.USER_LOGIN_ID = A.USER_LOGIN_ID
			) A WHERE 1=1
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
		$str .= $statement." GROUP BY A.NAMA, A.NIP, A.JABATAN, A.PAKET_ID, A.FUNGSI, A.KODE, A.KETUA, A.KUNCI_PANITIA ORDER BY A.KETUA DESC";
		// echo $str; die;
		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsPaktaIntegritas($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT
				PAKET_PANITIA_ID,
				PAKET_PANITIA_SK_ID,
				NAMA,
				NIP,
				STATUS,
				JABATAN,
				A.PAKET_ID,
				B.KODE,
				B.KODE_QR
			FROM PAKET_PANITIA A LEFT JOIN PAKET_PAKTA_INTEGRITAS B ON A.PAKET_ID = B.PAKET_ID AND A.NIP = B.KODE
			WHERE PAKET_PANITIA_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}


		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_PANITIA_ID ASC";
		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsBeritaAanwijzing($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT
				PAKET_PANITIA_ID,
				PAKET_PANITIA_SK_ID,
				NAMA,
				NIP,
				STATUS,
				JABATAN,
				A.PAKET_ID,
				B.KODE,
				B.KODE_QR
			FROM PAKET_PANITIA A LEFT JOIN PAKET_AANWIJZING_VALIDASI B ON A.PAKET_ID = B.PAKET_ID AND A.NIP = B.KODE
			WHERE PAKET_PANITIA_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}


		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_PANITIA_ID ASC";

		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT
				PAKET_PANITIA_ID,
				PAKET_PANITIA_SK_ID,
				NAMA,
				NIP,
				STATUS,
				JABATAN,
				PAKET_ID
			FROM PAKET_PANITIA
			WHERE PAKET_PANITIA_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY PAKET_PANITIA_ID ASC";
		return $this->selectLimit($str,$limit,$from);
    }
    /**
    * Hitung jumlah record berdasarkan parameter (array).
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy")
    * @return long Jumlah record yang sesuai kriteria
    **/
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(PAKET_PANITIA_ID) AS ROWCOUNT FROM PAKET_PANITIA WHERE PAKET_PANITIA_ID IS NOT NULL ";
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
		$str = "SELECT COUNT(PAKET_PANITIA_ID) AS ROWCOUNT FROM PAKET_PANITIA WHERE PAKET_PANITIA_ID IS NOT NULL ";
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

    function getCountByParams2($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{

		$str = "
			SELECT COUNT(A.PAKET_ID) AS ROWCOUNT FROM (
			SELECT
				A.NAMA,
				A.NIP,
				A.JABATAN,
				A.PAKET_ID,
				C.USER_JABATAN_PANITIA,
				C.USER_JABATAN_PANITIA_STR,
				'ANGGOTA' FUNGSI,
				B.KODE,
				C.USER_LOGIN_ID
			FROM PAKET_PANITIA A LEFT JOIN PAKET_PAKTA_INTEGRITAS B ON A.PAKET_ID = B.PAKET_ID AND A.NIP = B.KODE
			LEFT JOIN USER_LOGIN C ON A.NIP=C.NIP
			WHERE PAKET_PANITIA_ID IS NOT NULL	 and c.user_jabatan_panitia is not null
			UNION ALL
					SELECT C.USER_NAMA NAMA, CAST(C.NIP AS TEXT) NIP, C.USER_JABATAN JABATAN, PAKET_ID, C.USER_JABATAN_PANITIA,
					C.USER_JABATAN_PANITIA_STR, 'PEMBUAT' FUNGSI, '' KODE, C.USER_LOGIN_ID
				FROM PAKET A
				LEFT JOIN USER_LOGIN C ON C.USER_LOGIN_ID = A.USER_LOGIN_ID
			) A WHERE 1=1
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die();
		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

   function getCountByParams3($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT COUNT(PAKET_PANITIA_ID) AS ROWCOUNT FROM PAKET_PANITIA WHERE PAKET_PANITIA_ID IS NOT NULL ";
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement;
		$this->select($str);
		$this->query = $str;

		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }
  }
?>
