<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class PaketDokumenDownload extends Entity{

	var $query; 

  function __construct(){
		parent::__construct();
	}

  function PaketDokumen()
	{
      $this->Entity();
  }
 
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_DOKUMEN_DOWNLOAD_ID", $this->getNextId("PAKET_DOKUMEN_DOWNLOAD_ID","PAKET_DOKUMEN_DOWNLOAD"));

		$str = "
			INSERT INTO PAKET_DOKUMEN_DOWNLOAD (
   			           PAKET_DOKUMEN_DOWNLOAD_ID, PAKET_ID, REKANAN_ID,
					   PAKET_DOKUMEN_ID, CREATED_BY, CREATED_DATE)
			 	VALUES (
					   ".$this->getField("PAKET_DOKUMEN_DOWNLOAD_ID").", '".$this->getField("PAKET_ID")."', '".$this->getField("REKANAN_ID")."',
					   '".$this->getField("PAKET_DOKUMEN_ID")."', ".$this->ID.", CURRENT_TIMESTAMP
				)";
		$this->query = $str;
		$this->id = $this->getField("PAKET_DOKUMEN_DOWNLOAD_ID");
		return $this->execQuery($str);
  }
  
	function delete()
	{
        $str = "DELETE FROM PAKET_DOKUMEN_DOWNLOAD
                WHERE
                  PAKET_DOKUMEN_DOWNLOAD_ID = ".$this->getField("PAKET_DOKUMEN_DOWNLOAD_ID")."";

		$this->query = $str;
        return $this->execQuery($str);
  }
  
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				SELECT A.PAKET_DOKUMEN_DOWNLOAD_ID, A.PAKET_ID, A.REKANAN_ID, A.PAKET_DOKUMEN_ID, A.CREATED_BY, A.CREATED_DATE,
				B.NAMA
				FROM PAKET_DOKUMEN_DOWNLOAD A
				JOIN REKANAN B ON A.REKANAN_ID = B.REKANAN_ID
				WHERE PAKET_DOKUMEN_DOWNLOAD_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ORDER BY PAKET_DOKUMEN_DOWNLOAD_ID DESC";
		// echo $str;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
  }

  function selectByParamsGroup($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				SELECT A.PAKET_ID, A.REKANAN_ID, A.PAKET_DOKUMEN_ID,
				B.NAMA
				FROM PAKET_DOKUMEN_DOWNLOAD A
				JOIN REKANAN B ON A.REKANAN_ID = B.REKANAN_ID
				WHERE PAKET_DOKUMEN_DOWNLOAD_ID IS NOT NULL
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." GROUP BY A.PAKET_ID, A.REKANAN_ID, A.PAKET_DOKUMEN_ID,
				B.NAMA";
		// echo $str;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
  }
 
  }
?>
