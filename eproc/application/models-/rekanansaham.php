<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

  include_once('entity.php');

  class RekananSaham extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
  		parent::__construct();
  	}

    function RekananSaham()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_SAHAM_ID", $this->getNextId("REKANAN_SAHAM_ID","REKANAN_SAHAM"));

		$str = "
				INSERT INTO REKANAN_SAHAM (
					REKANAN_SAHAM_ID,
					REKANAN_ID,
					NAMA,
					KTP,
					ALAMAT,
					JUMLAH_SAHAM,
					STATUS,
					CREATED_BY, CREATED_DATE)
				VALUES ( '".$this->getField("REKANAN_SAHAM_ID")."',
					'".$this->getField("REKANAN_ID")."',
					'".$this->getField("NAMA")."',
					'".$this->getField("KTP")."',
					'".$this->getField("ALAMAT")."',
					'".$this->getField("JUMLAH_SAHAM")."',
					'".$this->getField("STATUS")."',
					".$this->getField("CREATED_BY").",
					CURRENT_TIMESTAMP
					)
		";

		$this->query = $str;
		return $this->execQuery($str);
  }

  function insert2()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_SAHAM_ID", $this->getNextId("REKANAN_SAHAM_ID","REKANAN_SAHAM"));

		$str = "
				INSERT INTO REKANAN_SAHAM (
					REKANAN_SAHAM_ID,
					REKANAN_ID,
					NAMA,
					KTP,
					ALAMAT,
					JUMLAH_SAHAM,
					STATUS,
					PATH_FILE, UKURAN, TIPE_FILE, NAMA_FILE,
					CREATED_BY, CREATED_DATE, KEPEMILIKAN, NPWP, KEWARGANEGARAAN, JENIS_KELAMIN, NEGARA, NOMINAL_SAHAM)
				VALUES ( '".$this->getField("REKANAN_SAHAM_ID")."',
					'".$this->getField("REKANAN_ID")."',
					'".$this->getField("NAMA")."',
					'".$this->getField("KTP")."',
					'".$this->getField("ALAMAT")."',
					'".$this->getField("JUMLAH_SAHAM")."',
					'".$this->getField("STATUS")."',
				  '".$this->getField("PATH_FILE")."',
				  ".$this->getField("UKURAN").",
				  '".$this->getField("TIPE_FILE")."',
				  '".$this->getField("NAMA_FILE")."',
					".$this->getField("CREATED_BY").",
					CURRENT_TIMESTAMP,
					'".$this->getField("KEPEMILIKAN")."',
          '".$this->getField("NPWP")."',
          '".$this->getField("KEWARGANEGARAAN")."',
          '".$this->getField("JENIS_KELAMIN")."',
          '".$this->getField("NEGARA")."',
					".$this->getField("NOMINAL_SAHAM")."
					)
		";
				// echo $str; die;
		$this->query = $str;
		return $this->execQuery($str);
  }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_SAHAM
				SET
					NAMA = '".$this->getField("NAMA")."',
					KTP = '".$this->getField("KTP")."',
					ALAMAT = '".$this->getField("ALAMAT")."',
					JUMLAH_SAHAM = '".$this->getField("JUMLAH_SAHAM")."',
					UPDATED_BY = ".$this->getField("CREATED_BY").",
					UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE REKANAN_SAHAM_ID = '".$this->getField("REKANAN_SAHAM_ID")."' AND REKANAN_ID = '".$this->getField("REKANAN_ID")."'
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

   function update2()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_SAHAM
				SET
					KEPEMILIKAN = '".$this->getField("KEPEMILIKAN")."',
					NAMA = '".$this->getField("NAMA")."',
					KTP = '".$this->getField("KTP")."',
					NPWP = '".$this->getField("NPWP")."',
					ALAMAT = '".$this->getField("ALAMAT")."',
					JUMLAH_SAHAM = '".$this->getField("JUMLAH_SAHAM")."',
					PATH_FILE= '".$this->getField("PATH_FILE")."',
				  UKURAN= ".$this->getField("UKURAN").",
				  TIPE_FILE= '".$this->getField("TIPE_FILE")."',
				  NAMA_FILE= '".$this->getField("NAMA_FILE")."',
          UPDATED_BY = ".$this->getField("CREATED_BY").",
					UPDATED_DATE = CURRENT_TIMESTAMP,
          KEWARGANEGARAAN = '".$this->getField("KEWARGANEGARAAN")."',
          JENIS_KELAMIN = '".$this->getField("JENIS_KELAMIN")."',
          NEGARA = '".$this->getField("NEGARA")."',
					NOMINAL_SAHAM = ".$this->getField("NOMINAL_SAHAM")."
				WHERE REKANAN_SAHAM_ID = '".$this->getField("REKANAN_SAHAM_ID")."' AND REKANAN_ID = '".$this->getField("REKANAN_ID")."'
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM REKANAN_SAHAM
                WHERE
                  REKANAN_SAHAM_ID = '".$this->getField("REKANAN_SAHAM_ID")."' ";

		$this->query = $str;
        return $this->execQuery($str);
    }

	function delete_kepemilikan_saham()
	{
        $str = "DELETE FROM REKANAN_SAHAM
                WHERE
                 REKANAN_ID = '".$this->getField("REKANAN_ID")."' ";

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
				REKANAN_SAHAM_ID,
				REKANAN_ID,
				NAMA,
				KTP,
				ALAMAT,
				JUMLAH_SAHAM,
				STATUS,
				PATH_FILE,
				UKURAN,
				TIPE_FILE,
				NAMA_FILE,
				KEPEMILIKAN,
				NPWP,
        KEWARGANEGARAAN,
        JENIS_KELAMIN,
        NEGARA,
        NOMINAL_SAHAM
			FROM REKANAN_SAHAM
			WHERE REKANAN_SAHAM_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_SAHAM_ID ASC";

		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT
				REKANAN_SAHAM_ID,
				REKANAN_ID,
				NAMA,
				KTP,
				ALAMAT,
				JUMLAH_SAHAM,
				STATUS
			FROM REKANAN_SAHAM
			WHERE REKANAN_SAHAM_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_SAHAM_ID ASC";
		return $this->selectLimit($str,$limit,$from);
    }
    /**
    * Hitung jumlah record berdasarkan parameter (array).
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy")
    * @return long Jumlah record yang sesuai kriteria
    **/
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(REKANAN_SAHAM_ID) AS ROWCOUNT FROM REKANAN_SAHAM WHERE REKANAN_SAHAM_ID IS NOT NULL ";
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
		$str = "SELECT COUNT(REKANAN_SAHAM_ID) AS ROWCOUNT FROM REKANAN_SAHAM WHERE REKANAN_SAHAM_ID IS NOT NULL ";
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
