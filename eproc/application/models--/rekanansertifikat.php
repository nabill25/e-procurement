<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

  include_once('entity.php');

  class RekananSertifikat extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
    function RekananSertifikat()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_SERTIFIKAT_ID", $this->getNextId("REKANAN_SERTIFIKAT_ID","REKANAN_SERTIFIKAT"));

		$str = "
				INSERT INTO REKANAN_SERTIFIKAT (
					REKANAN_SERTIFIKAT_ID,
					REKANAN_ID,
					NOMOR,
					NAMA,
					TANGGAL,
					BERLAKU,
   					PATH_FILE, TIPE, UKURAN,NAMA_FILE, SERTIFIKAT_TIPE,CREATED_BY,CREATED_DATE,JENIS,REKANAN_JENIS_SERTIFIKAT_ID,INSTANSI_PEMBERI)
				VALUES ( '".$this->getField("REKANAN_SERTIFIKAT_ID")."',
					'".$this->getField("REKANAN_ID")."',
					'".$this->getField("NOMOR")."',
					'".$this->getField("NAMA")."',
					".$this->getField("TANGGAL").",
					".$this->getField("BERLAKU").",
					'".$this->getField("PATH_FILE")."',
					'".$this->getField("TIPE")."',
					'".$this->getField("UKURAN")."',
					'".$this->getField("NAMA_FILE")."',
					'".$this->getField("SERTIFIKAT_TIPE")."',
					".$this->getField("CREATED_BY").",
					CURRENT_TIMESTAMP,
					'".$this->getField("JENIS")."',
          ".$this->getField("REKANAN_JENIS_SERTIFIKAT_ID").",
					'".$this->getField("INSTANSI_PEMBERI")."'
					)
		";

		$this->query = $str;
		echo $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_SERTIFIKAT
				SET
					NOMOR = '".$this->getField("NOMOR")."',
					NAMA = '".$this->getField("NAMA")."',
					TANGGAL = ".$this->getField("TANGGAL").",
					BERLAKU = ".$this->getField("BERLAKU").",
					PATH_FILE = '".$this->getField("PATH_FILE")."',
					TIPE = '".$this->getField("TIPE")."',
					UKURAN = '".$this->getField("UKURAN")."',
					NAMA_FILE= '".$this->getField("NAMA_FILE")."',
					SERTIFIKAT_TIPE	= '".$this->getField("SERTIFIKAT_TIPE")."',
					UPDATED_BY	= ".$this->getField("CREATED_BY").",
					UPDATED_DATE = CURRENT_TIMESTAMP,
					JENIS	= '".$this->getField("JENIS")."',
          REKANAN_JENIS_SERTIFIKAT_ID	= ".$this->getField("REKANAN_JENIS_SERTIFIKAT_ID").",
					INSTANSI_PEMBERI	= '".$this->getField("INSTANSI_PEMBERI")."'
				WHERE REKANAN_SERTIFIKAT_ID = '".$this->getField("REKANAN_SERTIFIKAT_ID")."' AND REKANAN_ID = '".$this->getField("REKANAN_ID")."'
				";
				// echo $str; die;
				$this->query = $str;
		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM REKANAN_SERTIFIKAT
                WHERE
                  REKANAN_SERTIFIKAT_ID = '".$this->getField("REKANAN_SERTIFIKAT_ID")."'";

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
				A.REKANAN_SERTIFIKAT_ID,
				A.REKANAN_ID,
				A.NOMOR,
				A.NAMA,
				TO_CHAR(TANGGAL, 'YYYY-MM-DD') TANGGAL,
				TO_CHAR(BERLAKU, 'YYYY-MM-DD') BERLAKU,
   				A.PATH_FILE, A.TIPE, UKURAN, NAMA_FILE, JENIS, REKANAN_JENIS_SERTIFIKAT_ID, CONCAT(B.NAMA, ' - ', B.ALIAS) AS NAMA_SERTIFIKAT,
          INSTANSI_PEMBERI
			FROM REKANAN_SERTIFIKAT A
			LEFT JOIN REKANAN_SERTIFIKAT_JENIS B ON A.REKANAN_JENIS_SERTIFIKAT_ID=B.REKANAN_SERTIFIKAT_JENIS_ID
			WHERE REKANAN_SERTIFIKAT_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_SERTIFIKAT_ID DESC";
		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsSyarat($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT
				A.REKANAN_SERTIFIKAT_ID,
				A.REKANAN_ID,
				NOMOR,
				NAMA,
				TANGGAL,
				BERLAKU,
   				PATH_FILE, TIPE, UKURAN,
                B.CATATAN,
                B.REKANAN_DAFTAR_SERTIFIKAT_ID, A.NAMA_FILE
			FROM REKANAN_SERTIFIKAT A
            INNER JOIN REKANAN_DAFTAR_SERTIFIKAT B ON A.REKANAN_SERTIFIKAT_ID = B.REKANAN_SERTIFIKAT_ID AND A.REKANAN_ID = B.REKANAN_ID
			WHERE A.REKANAN_SERTIFIKAT_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY A.REKANAN_SERTIFIKAT_ID ASC";

		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT
				REKANAN_SERTIFIKAT_ID,
				REKANAN_ID,
				NOMOR,
				NAMA,
				TANGGAL,
				BERLAKU
			FROM REKANAN_SERTIFIKAT
			WHERE REKANAN_SERTIFIKAT_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_SERTIFIKAT_ID ASC";
		return $this->selectLimit($str,$limit,$from);
    }
    /**
    * Hitung jumlah record berdasarkan parameter (array).
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy")
    * @return long Jumlah record yang sesuai kriteria
    **/
    function getCountByParams($paramsArray=array(), $stat = '')
	{
		$str = "SELECT COUNT(REKANAN_SERTIFIKAT_ID) AS ROWCOUNT FROM REKANAN_SERTIFIKAT A WHERE REKANAN_SERTIFIKAT_ID IS NOT NULL ";
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= $stat;
		$this->select($str);
		//echo $str;
		if($this->firstRow()) {
			return $this->getField("ROWCOUNT");
		}
		else {
			return 0;
		}
    }

    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(REKANAN_SERTIFIKAT_ID) AS ROWCOUNT FROM REKANAN_SERTIFIKAT WHERE REKANAN_SERTIFIKAT_ID IS NOT NULL ";
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
