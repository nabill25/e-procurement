<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class PaketDokumen extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
		parent::__construct();
	}

    function PaketDokumen()
	{
      $this->Entity();
    }

 //    function insert()
	// {
	// 	/*Auto-generate primary key(s) by next max value (integer) */
	// 	$this->setField("PAKET_DOKUMEN_ID", $this->getNextId("PAKET_DOKUMEN_ID","PAKET_DOKUMEN"));

	// 	$str = "
	// 		INSERT INTO PAKET_DOKUMEN (
 //   			           PAKET_DOKUMEN_ID, PAKET_ID, NAMA,
	// 				   UKURAN, TIPE, PATH_FILE,
	// 				   TANGGAL_UPLOAD, JENIS_DOKUMEN, KETERANGAN,
	// 				   STATUS, REKANAN_USER_ID, FILE_PASSWORD, PARENT_ID)
	// 		 	VALUES (
	// 				   ".$this->getField("PAKET_DOKUMEN_ID").", '".$this->getField("PAKET_ID")."', '".$this->getField("NAMA")."',
	// 				   '".$this->getField("UKURAN")."', '".$this->getField("TIPE")."', '".$this->getField("PATH_FILE")."',
	// 				   CURRENT_DATE, '".$this->getField("JENIS_DOKUMEN")."', '".$this->getField("KETERANGAN")."',
	// 				   1, ".$this->getField("REKANAN_USER_ID").", '".$this->getField("FILE_PASSWORD")."', ".$this->getField("PARENT_ID")."
	// 			)";

	// 	$this->query = $str;
	// 	// echo $str;die();
	// 	return $this->execQuery($str);
 //    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_DOKUMEN_ID", $this->getNextId("PAKET_DOKUMEN_ID","PAKET_DOKUMEN"));

		$str = "
			INSERT INTO PAKET_DOKUMEN (
   			           PAKET_DOKUMEN_ID, PAKET_ID, NAMA,
					   UKURAN, TIPE, PATH_FILE,
					   TANGGAL_UPLOAD, JENIS_DOKUMEN, KETERANGAN,
					   STATUS, REKANAN_USER_ID, FILE_PASSWORD, PARENT_ID, CREATED_BY)
			 	VALUES (
					   ".$this->getField("PAKET_DOKUMEN_ID").", '".$this->getField("PAKET_ID")."', '".$this->getField("NAMA")."',
					   ".$this->getField("UKURAN").", '".$this->getField("TIPE")."', '".$this->getField("PATH_FILE")."',
					   NOW(), '".$this->getField("JENIS_DOKUMEN")."', '".$this->getField("KETERANGAN")."',
					   1, ".$this->getField("REKANAN_USER_ID").", '".$this->getField("FILE_PASSWORD")."', ".$this->getField("PARENT_ID").", ".$this->USER_LOGIN_ID."
				)";

		$this->query = $str;
		// echo $str;die();
		return $this->execQuery($str);
    }

    // ikn 20190311
    function insert2()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_DOKUMEN_ID", $this->getNextId("PAKET_DOKUMEN_ID","PAKET_DOKUMEN"));

		$str = "
			INSERT INTO PAKET_DOKUMEN (
   			           PAKET_DOKUMEN_ID, PAKET_ID, NAMA,
					   UKURAN, TIPE, PATH_FILE,
					   TANGGAL_UPLOAD, JENIS_DOKUMEN, KETERANGAN,
					   STATUS, REKANAN_USER_ID)
			 	VALUES (
					   ".$this->getField("PAKET_DOKUMEN_ID").", '".$this->getField("PAKET_ID")."', '".$this->getField("NAMA")."',
					   '".$this->getField("UKURAN")."', '".$this->getField("TIPE")."', '".$this->getField("PATH_FILE")."',
					   CURRENT_DATE, '".$this->getField("JENIS_DOKUMEN")."', '".$this->getField("KETERANGAN")."',
					   1, ".$this->getField("REKANAN_USER_ID")."
				)";

		$this->query = $str;
		// echo $str;exit;
		return $this->execQuery($str);
    }

    function insertNoFile()
  	{
  		/*Auto-generate primary key(s) by next max value (integer) */
  		$this->setField("PAKET_DOKUMEN_ID", $this->getNextId("PAKET_DOKUMEN_ID","PAKET_DOKUMEN"));

  		$str = "
  			INSERT INTO PAKET_DOKUMEN (
     			           PAKET_DOKUMEN_ID, PAKET_ID, NAMA,
                     TANGGAL_UPLOAD,JENIS_DOKUMEN, KETERANGAN,
  					   STATUS, REKANAN_USER_ID, FILE_PASSWORD, PARENT_ID)
  			 	VALUES (
  					   ".$this->getField("PAKET_DOKUMEN_ID").", '".$this->getField("PAKET_ID")."', '".$this->getField("NAMA")."',
               CURRENT_DATE, '".$this->getField("JENIS_DOKUMEN")."', '".$this->getField("KETERANGAN")."',
  					   1, ".$this->getField("REKANAN_USER_ID").", '".$this->getField("FILE_PASSWORD")."', ".$this->getField("PARENT_ID")."
  				)";

  		$this->query = $str;
  		// echo $str;exit;
  		return $this->execQuery($str);
      }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_DOKUMEN SET
				  NAMA = '".$this->getField("NAMA")."'
				WHERE PAKET_DOKUMEN_ID = ".$this->getField("PAKET_DOKUMEN_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

   function updateVerifikasi()
	{
		$str = "UPDATE PAKET_DOKUMEN SET
				  VERIFIKASI = '".$this->getField("VERIFIKASI")."',
				  CATATAN = '".$this->getField("CATATAN")."',
				  UPDATED_BY = ".$this->getField("CREATED_BY").",
				  UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE PAKET_DOKUMEN_ID = ".$this->getField("PAKET_DOKUMEN_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

	function updateDokumenPemenang()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET_DOKUMEN SET
				  UKURAN = '".$this->getField("UKURAN")."',
				  TIPE = '".$this->getField("TIPE")."',
				  PATH_FILE = '".$this->getField("PATH_FILE")."'
				WHERE PAKET_DOKUMEN_ID = ".$this->getField("PAKET_DOKUMEN_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

	function updateKirimPenawaranPengadaan()
	{
        $str = "UPDATE PAKET_DOKUMEN SET
				  STATUS = 1
                WHERE
                  PAKET_ID = '".$this->getField("PAKET_ID")."' AND
				  REKANAN_USER_ID = '".$this->getField("REKANAN_USER_ID")."' AND
				  JENIS_DOKUMEN = 'PENAWARAN'
				  ";

		$this->query = $str;
        return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM PAKET_DOKUMEN
                WHERE
                  PAKET_DOKUMEN_ID = ".$this->getField("PAKET_DOKUMEN_ID")."";

		$this->query = $str;
        return $this->execQuery($str);
    }

	function deleteRekanan()
	{
        $str = "DELETE FROM PAKET_DOKUMEN
                WHERE
                  PAKET_DOKUMEN_ID = '".$this->getField("PAKET_DOKUMEN_ID")."' AND
				  REKANAN_USER_ID = '".$this->getField("REKANAN_USER_ID")."'
				  ";

		$this->query = $str;
        return $this->execQuery($str);
    }

	function deleteByJenisPaket()
	{
        $str = "DELETE FROM PAKET_DOKUMEN
                WHERE
                  PAKET_ID = '".$this->getField("PAKET_ID")."' AND
				  REKANAN_USER_ID = '".$this->getField("REKANAN_USER_ID")."' AND
				  JENIS_DOKUMEN = '".$this->getField("JENIS_DOKUMEN")."'
				  ";

		$this->query = $str;
        return $this->execQuery($str);
    }

	function deleteByJenisPaketAdmin()
	{
        $str = "DELETE FROM PAKET_DOKUMEN
                WHERE
                  PAKET_ID = '".$this->getField("PAKET_ID")."' AND
				  JENIS_DOKUMEN = '".$this->getField("JENIS_DOKUMEN")."'
				  ";

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
					PAKET_DOKUMEN_ID, PAKET_ID, NAMA,
					UKURAN, TIPE, PATH_FILE,
					TO_CHAR(TANGGAL_UPLOAD, 'YYYY-MM-DD') TANGGAL_UPLOAD, JENIS_DOKUMEN, KETERANGAN, TO_CHAR(TANGGAL_UPLOAD, 'DD-MM-YYYY HH24:MI') TGL_JAM_UPLOAD,
					STATUS, REKANAN_USER_ID, FILE_PASSWORD, (SELECT NAMA FROM REKANAN X WHERE X.REKANAN_ID = P.REKANAN_USER_ID) NMREKANAN, VERIFIKASI, CATATAN
				FROM PAKET_DOKUMEN P WHERE PAKET_DOKUMEN_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ORDER BY PAKET_DOKUMEN_ID ASC";
		// echo $str;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

  function selectByParamsGroupRekId($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				SELECT REKANAN_USER_ID  FROM PAKET_DOKUMEN P WHERE PAKET_DOKUMEN_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." GROUP BY REKANAN_USER_ID";
		// echo $str;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsBackup($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				SELECT
					PAKET_DOKUMEN_ID, PAKET_ID, NAMA,
					UKURAN, TIPE, PATH_FILE,
					TANGGAL_UPLOAD, JENIS_DOKUMEN, KETERANGAN, TO_CHAR(TANGGAL_UPLOAD, 'DD-MM-YYYY HH24:MI') TGL_JAM_UPLOAD,
					STATUS, REKANAN_USER_ID, FILE_PASSWORD, (SELECT NAMA FROM REKANAN X WHERE X.REKANAN_ID = P.REKANAN_USER_ID) NMREKANAN
				FROM PAKET_DOKUMEN_BACKUP P WHERE PAKET_DOKUMEN_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ORDER BY PAKET_DOKUMEN_ID ASC";
		$this->query = $str;
		//echo $str;
		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PAKET_DOKUMEN_ID, NAMA
				FROM PAKET_DOKUMEN WHERE PAKET_DOKUMEN_ID IS NOT NULL";

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
    function getCountByParams($paramsArray=array(), $statement="")
	{
		$str = "SELECT COUNT(PAKET_DOKUMEN_ID) AS ROWCOUNT FROM PAKET_DOKUMEN WHERE PAKET_DOKUMEN_ID IS NOT NULL ".$statement;
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
		$str = "SELECT COUNT(PAKET_DOKUMEN_ID) AS ROWCOUNT FROM PAKET_DOKUMEN WHERE PAKET_DOKUMEN_ID IS NOT NULL ";
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
