<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

  include_once('entity.php');

  class RekananTipe extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
  		parent::__construct();
  	}

    function RekananTipe()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_TIPE_ID", $this->getNextId("REKANAN_TIPE_ID","REKANAN_TIPE"));

		$str = "
		INSERT INTO REKANAN_TIPE (
   			        REKANAN_TIPE_ID, NAMA, STATUS, CREATED_BY, CREATED_DATE)
			 	VALUES (
				  ".$this->getField("REKANAN_TIPE_ID").",
          '".$this->getField("NAMA")."',
				  '".$this->getField("STATUS")."',
            ".$this->getField("CREATED_BY").",
            CURRENT_TIMESTAMP
				)";

		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_TIPE SET
          NAMA = '".$this->getField("NAMA")."',
				  STATUS = '".$this->getField("STATUS")."',
          UPDATED_BY = ".$this->getField("CREATED_BY").",
          UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE REKANAN_TIPE_ID = ".$this->getField("REKANAN_TIPE_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM REKANAN_TIPE
                WHERE
                  REKANAN_TIPE_ID = ".$this->getField("REKANAN_TIPE_ID")."";

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
		$str = "SELECT REKANAN_TIPE_ID, NAMA, STATUS
				FROM REKANAN_TIPE WHERE REKANAN_TIPE_ID IS NOT NULL";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY REKANAN_TIPE_ID ASC";

		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT REKANAN_TIPE_ID, NAMA
				FROM REKANAN_TIPE WHERE REKANAN_TIPE_ID IS NOT NULL";

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
		$str = "SELECT COUNT(REKANAN_TIPE_ID) AS ROWCOUNT FROM REKANAN_TIPE WHERE REKANAN_TIPE_ID IS NOT NULL ";
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
		$str = "SELECT COUNT(REKANAN_TIPE_ID) AS ROWCOUNT FROM REKANAN_TIPE WHERE REKANAN_TIPE_ID IS NOT NULL ";
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
