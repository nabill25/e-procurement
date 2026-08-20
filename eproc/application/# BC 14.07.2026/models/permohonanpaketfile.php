<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class PermohonanPaketFile extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
	function __construct(){
		parent::__construct();
	}

    function PermohonanPaketFile()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PERMOHONAN_PAKET_FILE_ID", $this->getNextId("PERMOHONAN_PAKET_FILE_ID","PERMOHONAN_PAKET_FILE"));

		$str = "

			INSERT INTO PERMOHONAN_PAKET_FILE (
			   PERMOHONAN_PAKET_FILE_ID, PERMOHONAN_PAKET_ID, PAKET_ID, PATH_FILE,
			   TIPE, UKURAN, JUDUL, URUT, CREATED_BY, CREATED_DATE)
 			VALUES (
				  ".$this->getField("PERMOHONAN_PAKET_FILE_ID").",
  				  ".$this->getField("PERMOHONAN_PAKET_ID").",
  				  ".$this->getField("PAKET_ID").",
   				  '".$this->getField("PATH_FILE")."',
				  '".$this->getField("TIPE")."',
				  ".$this->getField("UKURAN").",
				  '".$this->getField("JUDUL")."',
				  ".$this->getField("URUT").",
				  ".$this->getField("CREATED_BY").",
				  CURRENT_TIMESTAMP
				)";
				// echo $str; die();
		$this->query = $str;
		$this->id = $this->getField("PERMOHONAN_PAKET_FILE_ID");

		return $this->execQuery($str);
    }

    // ikn 20190309
    function insertByPaketId()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PERMOHONAN_PAKET_FILE_ID", $this->getNextId("PERMOHONAN_PAKET_FILE_ID","PERMOHONAN_PAKET_FILE"));

		$str = "

			INSERT INTO PERMOHONAN_PAKET_FILE (
			   PERMOHONAN_PAKET_FILE_ID, PAKET_ID, PATH_FILE,
			   TIPE, UKURAN, JUDUL, URUT)
 			VALUES (
				  '".$this->getField("PERMOHONAN_PAKET_FILE_ID")."',
  				  '".$this->getField("PAKET_ID")."',
   				  '".$this->getField("PATH_FILE")."',
				  '".$this->getField("TIPE")."',
				  ".$this->getField("UKURAN").",
				  '".$this->getField("JUDUL")."',
				  ".$this->getField("URUT")."
				)";

		$this->query = $str;
		$this->id = $this->getField("PERMOHONAN_PAKET_FILE_ID");

		return $this->execQuery($str);
    }

	function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PERMOHONAN_PAKET_FILE SET
   				  PATH_FILE					= '".$this->getField("PATH_FILE")."',
				  TIPE						= '".$this->getField("TIPE")."',
				  UKURAN					= ".$this->getField("UKURAN")."
				WHERE PERMOHONAN_PAKET_FILE_ID = ".$this->getField("PERMOHONAN_PAKET_FILE_ID")."
				";
				$this->query = $str;
				//echo $str;exit;
		return $this->execQuery($str);
    }

	function updateRevisi()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PERMOHONAN_PAKET_FILE SET
   				  PATH_FILE						= '".$this->getField("PATH_FILE")."',
				  TIPE							= '".$this->getField("TIPE")."',
				  UKURAN						= ".$this->getField("UKURAN")."
				WHERE PERMOHONAN_PAKET_FILE_ID 	= ".$this->getField("PERMOHONAN_PAKET_FILE_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM PERMOHONAN_PAKET_FILE
                WHERE
                  PERMOHONAN_PAKET_FILE_ID = ".$this->getField("PERMOHONAN_PAKET_FILE_ID")."";

		$this->query = $str;
        return $this->execQuery($str);
    }

	function deletePermohonan()
	{
    $this->execQuery("DELETE FROM PERMOHONAN_PAKET_FILE_BC WHERE PERMOHONAN_PAKET_ID = ".$this->getField("PERMOHONAN_PAKET_ID")."");
    $str2 = "INSERT INTO PERMOHONAN_PAKET_FILE_BC (SELECT * FROM PERMOHONAN_PAKET_FILE WHERE PERMOHONAN_PAKET_ID = ".$this->getField("PERMOHONAN_PAKET_ID").")";
		$this->query = $str2;
		if ($this->execQuery($str2)) {

	    $str = "DELETE FROM PERMOHONAN_PAKET_FILE WHERE PERMOHONAN_PAKET_ID = ".$this->getField("PERMOHONAN_PAKET_ID")."";
			$this->query = $str;
      return $this->execQuery($str);
      // echo "Hapus Berhasil";
		} else {
      // echo "Hapus ".$str2;
			return false;
		}

  }

    // ikn 20190309
  function deletePermohonanByPaketID()
	{
		$str2 = "INSERT INTO PERMOHONAN_PAKET_FILE_BC (SELECT * FROM PERMOHONAN_PAKET_FILE WHERE PAKET_ID = ".$this->getField("PAKET_ID").")";
		$this->query = $str2;
		if ($this->execQuery($str2)) {

	    $str = "DELETE FROM PERMOHONAN_PAKET_FILE WHERE PAKET_ID = ".$this->getField("PAKET_ID")."";
			$this->query = $str;
	    return $this->execQuery($str);

		} else {
			return false;
		}

  }

    /**
    * Cari record berdasarkan array parameter dan limit tampilan
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","PERMOHONAN_PAKET_FILE_METODE_EVALUASI_ID"=>"yyy")
    * @param int limit Jumlah maksimal record yang akan diambil
    * @param int from Awal record yang diambil
    * @return boolean True jika sukses, false jika tidak
    **/
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
					SELECT
					 A.PERMOHONAN_PAKET_FILE_ID, PERMOHONAN_PAKET_ID, PATH_FILE,
			  		 TIPE, UKURAN,JUDUL, URUT, B.CATATAN
					FROM PERMOHONAN_PAKET_FILE A
					LEFT JOIN PERMOHONAN_PAKET_FILE_REVISI_TERAKHIR B ON A.PERMOHONAN_PAKET_FILE_ID = B.PERMOHONAN_PAKET_FILE_ID
				    WHERE A.PERMOHONAN_PAKET_FILE_ID IS NOT NULL ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;

		$str .= $statement." ORDER BY URUT ASC";

		return $this->selectLimit($str,$limit,$from);
    }


    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(A.PERMOHONAN_PAKET_FILE_ID) AS ROWCOUNT
					FROM    PERMOHONAN_PAKET_FILE A
					WHERE 1 = 1".$statement;
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
