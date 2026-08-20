<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

  include_once('entity.php');

  class RekananPengurus extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
  		parent::__construct();
  	}

    function RekananPengurus()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_PENGURUS_ID", $this->getNextId("REKANAN_PENGURUS_ID","REKANAN_PENGURUS"));

		$str = "
		INSERT INTO REKANAN_PENGURUS (
		   REKANAN_PENGURUS_ID, REKANAN_ID, NAMA, KTP, JABATAN, TIPE, STATUS, PATH_FILE, UKURAN, TIPE_FILE, NAMA_FILE, KEWARGANEGARAAN, JENIS_KELAMIN, ALAMAT_KTP, DOMISILI, NPWP, NEGARA, NOMOR_HP_DIREKTUR, PATH_FILE2)
		   -- REKANAN_PENGURUS_ID, REKANAN_ID, NAMA, KTP, JABATAN, TIPE, STATUS, )
 			 	VALUES (
				  ".$this->getField("REKANAN_PENGURUS_ID").",
  				  ".$this->getField("REKANAN_ID").",
				  '".$this->getField("NAMA")."',
    			  '".$this->getField("KTP")."',
   			  '".$this->getField("JABATAN")."',
  				  '".$this->getField("TIPE")."',
				  ".$this->getField("STATUS").",
				  '".$this->getField("PATH_FILE")."',
				  ".$this->getField("UKURAN").",
				  '".$this->getField("TIPE_FILE")."',
	          '".$this->getField("NAMA_FILE")."',
	          '".$this->getField("KEWARGANEGARAAN")."',
	          '".$this->getField("JENIS_KELAMIN")."',
	          '".$this->getField("ALAMAT_KTP")."',
	          '".$this->getField("DOMISILI")."',
	          '".$this->getField("NPWP")."',
	          '".$this->getField("NEGARA")."',
				 '".$this->getField("NOMOR_HP_DIREKTUR")."',
				 '".$this->getField("PATH_FILE2")."'
				)";

		// echo $str; die();
		$this->query = $str;
		return $this->execQuery($str);
    }

    function insertnofile()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_PENGURUS_ID", $this->getNextId("REKANAN_PENGURUS_ID","REKANAN_PENGURUS"));

		$str = "
		INSERT INTO REKANAN_PENGURUS (
		   REKANAN_PENGURUS_ID, REKANAN_ID, NAMA, KTP, JABATAN, TIPE, STATUS, CREATED_BY, CREATED_DATE)
		   -- REKANAN_PENGURUS_ID, REKANAN_ID, NAMA, KTP, JABATAN, TIPE, STATUS)
 			 	VALUES (
				  ".$this->getField("REKANAN_PENGURUS_ID").",
  				  ".$this->getField("REKANAN_ID").",
				  '".$this->getField("NAMA")."',
    			  '".$this->getField("KTP")."',
      			  '".$this->getField("JABATAN")."',
  				  '".$this->getField("TIPE")."',
				  ".$this->getField("STATUS").",
				  ".$this->getField("CREATED_BY").",
				  CURRENT_TIMESTAMP
				)";

		// echo $str; die();
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_PENGURUS SET
				  NAMA = '".$this->getField("NAMA")."',
				  KTP = '".$this->getField("KTP")."',
				  JABATAN = '".$this->getField("JABATAN")."',
				  TIPE = '".$this->getField("TIPE")."',
				  STATUS = ".$this->getField("STATUS").",
				  PATH_FILE= '".$this->getField("PATH_FILE")."',
				  UKURAN= ".$this->getField("UKURAN").",
				  TIPE_FILE= '".$this->getField("TIPE_FILE")."',
				  NAMA_FILE= '".$this->getField("NAMA_FILE")."',
          UPDATED_BY = ".$this->getField("CREATED_BY").",
				  UPDATED_DATE = CURRENT_TIMESTAMP,
          KEWARGANEGARAAN = '".$this->getField("KEWARGANEGARAAN")."',
          JENIS_KELAMIN = '".$this->getField("JENIS_KELAMIN")."',
          ALAMAT_KTP = '".$this->getField("ALAMAT_KTP")."',
          DOMISILI = '".$this->getField("DOMISILI")."',
          NPWP = '".$this->getField("NPWP")."',
          NEGARA = '".$this->getField("NEGARA")."',
          NOMOR_HP_DIREKTUR = '".$this->getField("NOMOR_HP_DIREKTUR")."',
          PATH_FILE2 = '".$this->getField("PATH_FILE2")."'
				WHERE REKANAN_PENGURUS_ID = ".$this->getField("REKANAN_PENGURUS_ID")." AND REKANAN_ID = '".$this->getField("REKANAN_ID")."'
				";
				$this->query = $str;
				//echo $str;
		return $this->execQuery($str);
    }

    function updatenofile()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_PENGURUS SET
				  NAMA = '".$this->getField("NAMA")."',
				  KTP = '".$this->getField("KTP")."',
				  JABATAN = '".$this->getField("JABATAN")."',
				  UPDATED_BY = ".$this->getField("CREATED_BY").",
				  UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE REKANAN_PENGURUS_ID = ".$this->getField("REKANAN_PENGURUS_ID")." AND REKANAN_ID = '".$this->getField("REKANAN_ID")."'
				";
				$this->query = $str;
				//echo $str;
		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM REKANAN_PENGURUS
                WHERE
                   REKANAN_PENGURUS_ID = '".$this->getField("REKANAN_PENGURUS_ID")."' ";

		$this->query = $str;
		//echo $str;
        return $this->execQuery($str);
    }

	function delete_komisaris()
	{
        $str = "DELETE FROM REKANAN_PENGURUS
                WHERE
                   REKANAN_ID = '".$this->getField("REKANAN_ID")."' ";

		$this->query = $str;
		//echo $str;
        return $this->execQuery($str);
    }

	function delete_direksi()
	{
        $str = "DELETE FROM REKANAN_PENGURUS
                WHERE
                   REKANAN_ID = '".$this->getField("REKANAN_ID")."' ";

		$this->query = $str;
		//echo $str;
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
		$str = "SELECT REKANAN_PENGURUS_ID, REKANAN_ID, NAMA, KTP, JABATAN, TIPE, STATUS , PATH_FILE, UKURAN, TIPE_FILE, NAMA_FILE, KEWARGANEGARAAN, JENIS_KELAMIN, ALAMAT_KTP, DOMISILI, NPWP, NEGARA, NOMOR_HP_DIREKTUR, PATH_FILE2
				FROM REKANAN_PENGURUS WHERE REKANAN_PENGURUS_ID IS NOT NULL";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY NAMA ASC";
		//echo $str;
		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsDirektur($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT NOMOR, A.REKANAN_ID, A.NAMA, JABATAN, B.ALAMAT, B.KOTA
				  FROM REKANAN_PENGURUS_DIREKTUR A
				  INNER JOIN REKANAN B ON A.REKANAN_ID = B.REKANAN_ID
				 WHERE 1 = 1 ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY NOMOR ASC";
		//echo $str;
		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT  REKANAN_PENGURUS_ID, REKANAN_ID, NAMA, KTP, JABATAN, TIPE, STATUS
				FROM REKANAN_PENGURUS WHERE REKANAN_PENGURUS_ID IS NOT NULL";

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
		$str = "SELECT COUNT(REKANAN_PENGURUS_ID) AS ROWCOUNT FROM REKANAN_PENGURUS WHERE REKANAN_PENGURUS_ID IS NOT NULL ";
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->select($str);
		//echo $str;
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(REKANAN_PENGURUS_ID) AS ROWCOUNT FROM REKANAN_PENGURUS WHERE REKANAN_PENGURUS_ID IS NOT NULL ";
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
