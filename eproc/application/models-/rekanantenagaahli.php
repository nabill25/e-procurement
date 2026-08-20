<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

  include_once('entity.php');

  class RekananTenagaAhli extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
    function RekananTenagaAhli()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_TENAGA_AHLI_ID", $this->getNextId("REKANAN_TENAGA_AHLI_ID","REKANAN_TENAGA_AHLI"));

		$str = "
				INSERT INTO REKANAN_TENAGA_AHLI (
					REKANAN_TENAGA_AHLI_ID,
					REKANAN_ID,
					NAMA,
					TEMPAT_LAHIR,
					TANGGAL_LAHIR,
					ALAMAT,
					KTP,
					NPWP,
					CREATED_BY, CREATED_DATE, JENIS_KELAMIN)
				VALUES ( '".$this->getField("REKANAN_TENAGA_AHLI_ID")."',
					'".$this->getField("REKANAN_ID")."',
					'".$this->getField("NAMA")."',
					'".$this->getField("TEMPAT_LAHIR")."',
					".$this->getField("TANGGAL_LAHIR").",
					'".$this->getField("ALAMAT")."',
					'".$this->getField("KTP")."',
					'".$this->getField("NPWP")."',
          ".$this->getField("CREATED_BY").",
					CURRENT_TIMESTAMP,
          '".$this->getField("JENIS_KELAMIN")."'
					)
		";

		$this->query = $str;
		$this->id = $this->getField("REKANAN_TENAGA_AHLI_ID");
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_TENAGA_AHLI
				SET
					NAMA = '".$this->getField("NAMA")."',
					TEMPAT_LAHIR = '".$this->getField("TEMPAT_LAHIR")."',
					TANGGAL_LAHIR = ".$this->getField("TANGGAL_LAHIR").",
					ALAMAT = '".$this->getField("ALAMAT")."',
					KTP = '".$this->getField("KTP")."',
          NPWP = '".$this->getField("NPWP")."',
					UPDATED_BY = ".$this->getField("CREATED_BY").",
					UPDATED_DATE = CURRENT_TIMESTAMP,
          JENIS_KELAMIN = '".$this->getField("JENIS_KELAMIN")."'
				WHERE REKANAN_TENAGA_AHLI_ID = '".$this->getField("REKANAN_TENAGA_AHLI_ID")."' AND REKANAN_ID = '".$this->getField("REKANAN_ID")."'
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM REKANAN_TENAGA_AHLI
                WHERE
                  REKANAN_TENAGA_AHLI_ID = '".$this->getField("REKANAN_TENAGA_AHLI_ID")."' AND REKANAN_ID = '".$this->getField("REKANAN_ID")."' ";

		$this->query = $str;
        return $this->execQuery($str);
    }

	function delete_spp()
	{
		$str0 = "DELETE FROM REKANAN_TENAGA_AHLI_PEND
                WHERE
                  REKANAN_TENAGA_AHLI_ID = '".$this->getField("REKANAN_TENAGA_AHLI_ID")."'";

		$this->query = $str0;
        $this->execQuery($str0);

		$str1 = "DELETE FROM REKANAN_TENAGA_AHLI_PENG
                WHERE
                  REKANAN_TENAGA_AHLI_ID = '".$this->getField("REKANAN_TENAGA_AHLI_ID")."'";

		$this->query = $str1;
        $this->execQuery($str1);

        $str = "DELETE FROM REKANAN_TENAGA_AHLI_SERT
                WHERE
                  REKANAN_TENAGA_AHLI_ID = '".$this->getField("REKANAN_TENAGA_AHLI_ID")."'";

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
				REKANAN_TENAGA_AHLI_ID,
				REKANAN_ID,
				NAMA,
				TEMPAT_LAHIR,
				TO_CHAR(TANGGAL_LAHIR, 'YYYY-MM-DD') TANGGAL_LAHIR,
				ALAMAT,
				KTP,
				NPWP,
        JENIS_KELAMIN,
				AMBIL_TENAGA_AHLI_PENDIDIKAN(REKANAN_TENAGA_AHLI_ID) PENDIDIKAN,
				AMBIL_TENAGA_AHLI_SERTIFIKAT(REKANAN_TENAGA_AHLI_ID) SERTIFIKAT
			FROM REKANAN_TENAGA_AHLI A
			WHERE REKANAN_TENAGA_AHLI_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_TENAGA_AHLI_ID ASC";
		//echo $str;
		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsSyarat($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT
				A.REKANAN_TENAGA_AHLI_ID,
				A.REKANAN_ID,
				NAMA,
				TEMPAT_LAHIR,
				TANGGAL_LAHIR,
				ALAMAT,
				KTP,
				NPWP,
				AMBIL_TENAGA_AHLI_PENDIDIKAN (A.REKANAN_TENAGA_AHLI_ID) PENDIDIKAN,
				AMBIL_TENAGA_AHLI_SERTIFIKAT (A.REKANAN_TENAGA_AHLI_ID) SERTIFIKAT,
				B.CATATAN,
				B.REKANAN_DAFTAR_TENAGA_AHLI_ID
			FROM REKANAN_TENAGA_AHLI A
            INNER JOIN REKANAN_DAFTAR_TENAGA_AHLI B ON A.REKANAN_TENAGA_AHLI_ID = B.REKANAN_TENAGA_AHLI_ID AND A.REKANAN_ID = B.REKANAN_ID
			WHERE A.REKANAN_TENAGA_AHLI_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY A.REKANAN_TENAGA_AHLI_ID ASC";
		//echo $str;
		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT
				REKANAN_TENAGA_AHLI_ID,
				REKANAN_ID,
				NAMA,
				TEMPAT_LAHIR,
				TANGGAL_LAHIR,
				ALAMAT,
				KTP,
				NPWP
			FROM REKANAN_TENAGA_AHLI
			WHERE REKANAN_TENAGA_AHLI_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_TENAGA_AHLI_ID ASC";
		return $this->selectLimit($str,$limit,$from);
    }
    /**
    * Hitung jumlah record berdasarkan parameter (array).
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy")
    * @return long Jumlah record yang sesuai kriteria
    **/
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(REKANAN_TENAGA_AHLI_ID) AS ROWCOUNT FROM REKANAN_TENAGA_AHLI WHERE REKANAN_TENAGA_AHLI_ID IS NOT NULL ";
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
		$str = "SELECT COUNT(REKANAN_TENAGA_AHLI_ID) AS ROWCOUNT FROM REKANAN_TENAGA_AHLI WHERE REKANAN_TENAGA_AHLI_ID IS NOT NULL ";
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
