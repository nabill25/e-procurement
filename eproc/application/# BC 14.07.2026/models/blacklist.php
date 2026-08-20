<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Blacklist extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
 //    function Blacklist()
	// {
 //      $this->Entity();
 //    }

    function __construct(){
  		parent::__construct();
	}

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("BLACKLIST_ID", $this->getNextId("BLACKLIST_ID","BLACKLIST"));

		$str = "

		INSERT INTO BLACKLIST (
		   BLACKLIST_ID, REKANAN_ID,
		   NAMA, ALAMAT, KOTA,
		   NPWP, TANGGAL_MULAI, TANGGAL_SELESAI,
		   NO_SK, ALASAN, STATUS)
  			 	VALUES (
				  ".$this->getField("BLACKLIST_ID").",
  				  '".$this->getField("REKANAN_ID")."',
				  '".$this->getField("NAMA")."',
				  '".$this->getField("ALAMAT")."',
  				  '".$this->getField("KOTA")."',
   				  '".$this->getField("NPWP")."',
   				  ".$this->getField("TANGGAL_MULAI").",
				  ".$this->getField("TANGGAL_SELESAI").",
   				  '".$this->getField("NO_SK")."',
   				  '".$this->getField("ALASAN")."',
				  ".$this->getField("STATUS")."
				)";

		$this->query = $str;
		$this->id = $this->getField("BLACKLIST_ID");
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE BLACKLIST
				    SET
					   REKANAN_ID      = ".$this->getField("REKANAN_ID").",
					   REKANAN_TIPE_ID = ".$this->getField("REKANAN_TIPE_ID").",
					   NAMA            = '".$this->getField("NAMA")."',
					   ALAMAT          = '".$this->getField("ALAMAT")."',
					   KOTA            = '".$this->getField("KOTA")."',
					   NPWP            = '".$this->getField("NPWP")."',
					   TANGGAL_MULAI   = '".$this->getField("TANGGAL_MULAI")."',
					   TANGGAL_SELESAI = '".$this->getField("TANGGAL_SELESAI")."',
					   NO_SK           = '".$this->getField("NO_SK")."',
					   ALASAN          = '".$this->getField("ALASAN")."',
					   STATUS          =  ".$this->getField("STATUS")."
	         WHERE  BLACKLIST_ID    =  ".$this->getField("BLACKLIST_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

	function delete()
	{
    if ($this->execQuery("DELETE FROM BLACKLIST_FILE WHERE BLACKLIST_ID = ".$this->getField("BLACKLIST_ID")."")) {
      $str = "DELETE FROM BLACKLIST WHERE BLACKLIST_ID = ".$this->getField("BLACKLIST_ID")."";
      $this->query = $str;
      return $this->execQuery($str);
    } else {
      return false;
    }
  }

  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=" ORDER BY NAMA ASC")
	{
    $str = "
				SELECT
				   BLACKLIST_ID, REKANAN_ID, REKANAN_TIPE_ID,
				   ALAMAT, KOTA,
				   NPWP, TO_CHAR(TANGGAL_MULAI, 'YYYY-MM-DD') TANGGAL_MULAI, TO_CHAR(TANGGAL_SELESAI, 'YYYY-MM-DD') TANGGAL_SELESAI,
	               NO_SK, ALASAN, STATUS,
				   CASE WHEN REKANAN_ID= NULL THEN NAMA
					 ELSE (SELECT C.NAMA || ' ' || A.NAMA FROM REKANAN A, REKANAN_TIPE C WHERE A.REKANAN_ID = B.REKANAN_ID AND A.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID)
					 END NAMA
                 FROM BLACKLIST B WHERE BLACKLIST_ID IS NOT NULL
				 AND CURRENT_DATE BETWEEN TANGGAL_MULAI AND TANGGAL_SELESAI "; 

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ".$order;

		return $this->selectLimit($str,$limit,$from);
  }

  function selectByParamsAll($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=" ORDER BY NAMA ASC")
	{
		$str = "
				SELECT
				   BLACKLIST_ID, REKANAN_ID, REKANAN_TIPE_ID,
				   ALAMAT, KOTA,
				   NPWP, TO_CHAR(TANGGAL_MULAI, 'YYYY-MM-DD') TANGGAL_MULAI, TO_CHAR(TANGGAL_SELESAI, 'YYYY-MM-DD') TANGGAL_SELESAI,
	               NO_SK, ALASAN, STATUS,
				   CASE WHEN REKANAN_ID= NULL THEN NAMA
					 ELSE (SELECT C.NAMA || ' ' || A.NAMA FROM REKANAN A, REKANAN_TIPE C WHERE A.REKANAN_ID = B.REKANAN_ID AND A.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID)
					 END NAMA
                 FROM BLACKLIST B WHERE BLACKLIST_ID IS NOT NULL ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ".$order;

		return $this->selectLimit($str,$limit,$from);
  }

    function selectByParamsSimple($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				SELECT
				   A.BLACKLIST_ID, A.REKANAN_ID, B.SAP_KODE
                 FROM BLACKLIST A
				 INNER JOIN REKANAN B ON A.REKANAN_ID = B.REKANAN_ID WHERE 1 = 1 ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY BLACKLIST_ID ASC";

		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				SELECT
				   BLACKLIST_ID, REKANAN_ID, REKANAN_TIPE_ID,
				   NAMA, ALAMAT, KOTA,
				   NPWP, TANGGAL_MULAI, TANGGAL_SELESAI,
				   NO_SK, ALASAN, STATUS
 				FROM BLACKLIST WHERE BLACKLIST_ID IS NOT NULL";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY NAMA ASC";
		return $this->selectLimit($str,$limit,$from);
    }

    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(BLACKLIST_ID) AS ROWCOUNT FROM BLACKLIST B WHERE BLACKLIST_ID IS NOT NULL
				 AND CURRENT_DATE BETWEEN TANGGAL_MULAI AND TANGGAL_SELESAI ";
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
		$str = "SELECT COUNT(BLACKLIST_ID) AS ROWCOUNT FROM BLACKLIST WHERE BLACKLIST_ID IS NOT NULL ";
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
