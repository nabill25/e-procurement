<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

  include_once('entity.php');

  class RekananAkta extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
  		parent::__construct();
  	}

    function RekananAkta()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_AKTA_ID", $this->getNextId("REKANAN_AKTA_ID","REKANAN_AKTA"));

		$str = "
		INSERT INTO REKANAN_AKTA (
		   REKANAN_AKTA_ID, AKTA_TYPE_ID, REKANAN_ID, NOMOR, TANGGAL, NOTARIS, STATUS, PATH_FILE, TIPE, UKURAN, NAMA_FILE, CREATED_BY, CREATED_DATE, NOMOR_KEMENKUMHAM)
 			 	VALUES (
				  ".$this->getField("REKANAN_AKTA_ID").",
  				  ".$this->getField("AKTA_TYPE_ID").",
				  ".$this->getField("REKANAN_ID").",
    			  '".$this->getField("NOMOR")."',
      			  ".$this->getField("TANGGAL").",
  				  '".$this->getField("NOTARIS")."',
				  ".$this->getField("STATUS").",
				  '".$this->getField("PATH_FILE")."',
				  '".$this->getField("TIPE")."',
				  ".$this->getField("UKURAN").",
				  '".$this->getField("NAMA_FILE")."',
				  ".$this->getField("CREATED_BY").",
				  CURRENT_TIMESTAMP,
				  '".$this->getField("NOMOR_KEMENKUMHAM")."'
				)";
				// echo $str; die();
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_AKTA SET
				  AKTA_TYPE_ID = ".$this->getField("AKTA_TYPE_ID").",
				  REKANAN_ID = ".$this->getField("REKANAN_ID").",
				  NOMOR = '".$this->getField("NOMOR")."',
				  TANGGAL = '".$this->getField("TANGGAL")."',
				  NOTARIS = '".$this->getField("NOTARIS")."',
				  STATUS = ".$this->getField("STATUS").",
				  NAMA_FILE = '".$this->getField("NAMA_FILE")."'
				WHERE REKANAN_AKTA_ID = ".$this->getField("REKANAN_AKTA_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

	function update_landasan()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_AKTA SET
				  NOMOR = '".$this->getField("NOMOR")."',
				  NOMOR_KEMENKUMHAM = '".$this->getField("NOMOR_KEMENKUMHAM")."',
				  TANGGAL = ".$this->getField("TANGGAL").",
				  NOTARIS = '".$this->getField("NOTARIS")."',
					PATH_FILE = '".$this->getField("PATH_FILE")."',
					TIPE = '".$this->getField("TIPE")."',
					UKURAN = '".$this->getField("UKURAN")."',
					NAMA_FILE = '".$this->getField("NAMA_FILE")."',
					UPDATED_BY = ".$this->getField("CREATED_BY").",
					UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE REKANAN_AKTA_ID = ".$this->getField("REKANAN_AKTA_ID")." AND REKANAN_ID = ".$this->getField("REKANAN_ID")."
				";
				$this->query = $str;
				//echo $str;
		return $this->execQuery($str);
    }

	function update_rekanan()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN SET
				  SURAT_KUASA = '".$this->getField("SURAT_KUASA")."',
				  SURAT_KUASA_TANGGAL = ".$this->getField("SURAT_KUASA_TANGGAL").",
				  SURAT_KUASA_NOTARIS = '".$this->getField("SURAT_KUASA_NOTARIS")."'
				WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."
				";
				$this->query = $str;
				//echo $str;
				/*,
					PATH_FILE = '".$this->getField("PATH_FILE")."',
					TIPE = '".$this->getField("TIPE")."',
					UKURAN = ".$this->getField("UKURAN")."*/
		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM REKANAN_AKTA
                WHERE
                  REKANAN_AKTA_ID = ".$this->getField("REKANAN_AKTA_ID")."";

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
		$str = "SELECT REKANAN_AKTA_ID, AKTA_TYPE_ID, REKANAN_ID, NOMOR, NOMOR_KEMENKUMHAM, TO_CHAR(TANGGAL, 'YYYY-MM-DD') TANGGAL, NOTARIS, STATUS,
   				PATH_FILE, TIPE, UKURAN, NAMA_FILE
				FROM REKANAN_AKTA WHERE REKANAN_AKTA_ID IS NOT NULL";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// $str .= $statement." ORDER BY TANGGAL DESC";
		$str .= $statement;
		// echo $str; die();
		$this->query = $str;

		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT  REKANAN_AKTA_ID, AKTA_TYPE_ID, REKANAN_ID, NOMOR, TANGGAL, NOTARIS, STATUS, NAMA_FILE
				FROM REKANAN_AKTA WHERE REKANAN_AKTA_ID IS NOT NULL";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY TANGGAL DESC";
		return $this->selectLimit($str,$limit,$from);
    }

    /**
    * Hitung jumlah record berdasarkan parameter (array).
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy")
    * @return long Jumlah record yang sesuai kriteria
    **/
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(REKANAN_AKTA_ID) AS ROWCOUNT FROM REKANAN_AKTA WHERE REKANAN_AKTA_ID IS NOT NULL ";
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
		$str = "SELECT COUNT(REKANAN_AKTA_ID) AS ROWCOUNT FROM REKANAN_AKTA WHERE REKANAN_AKTA_ID IS NOT NULL ";
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
