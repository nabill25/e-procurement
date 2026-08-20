<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Blacklistkontrak extends Entity{

	var $query; 

  function __construct(){
		parent::__construct();
	}

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("BLACKLISTKONTRAK_ID", $this->getNextId("BLACKLISTKONTRAK_ID","BLACKLIST_KONTRAK"));

		$str = "

		INSERT INTO BLACKLIST_KONTRAK (
		   BLACKLISTKONTRAK_ID, REKANAN_ID,
		   CONTRACTING_REKANAN_ID, JUDUL, KETERANGAN,
		   FILE, NO_SK, CREATED_BY,
		   CREATED_DATE, TANGGAL_BERLAKU)
  			 	VALUES (
				  ".$this->getField("BLACKLISTKONTRAK_ID").",
  				".$this->getField("REKANAN_ID").",
				  ".$this->getField("CONTRACTING_REKANAN_ID").",
				  '".$this->getField("JUDUL")."',
  				'".$this->getField("KETERANGAN")."',
   				'".$this->getField("FILE")."',
   				'".$this->getField("NO_SK")."',
				  ".$this->getField("CREATED_BY").",
   				CURRENT_TIMESTAMP,
				  ".$this->getField("TANGGAL_BERLAKU")."
				)";

		$this->query = $str;
		$this->id = $this->getField("BLACKLISTKONTRAK_ID");
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE BLACKLIST_KONTRAK
				    SET
					   JUDUL				= '".$this->getField("JUDUL")."',
					   KETERANGAN   = '".$this->getField("KETERANGAN")."',
					   FILE         = '".$this->getField("FILE")."',
					   NO_SK        = '".$this->getField("NO_SK")."',
					   UPDATED_BY   =  ".$this->getField("CREATED_BY").",
					   UPDATED_DATE = CURRENT_TIMESTAMP,
					   TANGGAL_BERLAKU   =  ".$this->getField("TANGGAL_BERLAKU")."
	         WHERE  REKANAN_ID = ".$this->getField("REKANAN_ID")." AND CONTRACTING_REKANAN_ID = ".$this->getField("CONTRACTING_REKANAN_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

	function delete()
	{ 
      $str = "DELETE FROM BLACKLIST_KONTRAK WHERE BLACKLISTKONTRAK_ID = ".$this->getField("BLACKLISTKONTRAK_ID")."";
      $this->query = $str;
      return $this->execQuery($str); 
  }

  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=" ORDER BY REKANAN_ID ASC")
	{
    $str = "
    		SELECT A.* FROM (
					SELECT A.*, CONCAT(C.NAMA,' ',B.NAMA) PENYEDIA, B.ALAMAT  FROM BLACKLIST_KONTRAK A 
					JOIN REKANAN B ON A.REKANAN_ID=B.REKANAN_ID
					LEFT JOIN REKANAN_TIPE C ON B.REKANAN_TIPE_ID=C.REKANAN_TIPE_ID
					) A 
				WHERE 1=1  "; 

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ".$order;

		return $this->selectLimit($str,$limit,$from);
  }
 
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(BLACKLISTKONTRAK_ID) AS ROWCOUNT FROM BLACKLIST_KONTRAK B WHERE BLACKLISTKONTRAK_ID IS NOT NULL";
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
