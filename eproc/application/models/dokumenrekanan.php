<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class DokumenRekanan extends Entity{

	var $query;
  function __construct(){
		parent::__construct();
	}

  function PaketDokumen()
	{
    $this->Entity();
  }
 
	function insertPI()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_PAKTA_INTEGRITAS_ID", $this->getNextId("REKANAN_PAKTA_INTEGRITAS_ID","REKANAN_PAKTA_INTEGRITAS"));

		$str = "
			INSERT INTO REKANAN_PAKTA_INTEGRITAS (
   			           REKANAN_PAKTA_INTEGRITAS_ID, REKANAN_ID, PATH_FILE,
					   TIPE, UKURAN, NAMA_FILE, CREATED_BY, CREATED_DATE)
			 	VALUES (
					   ".$this->getField("REKANAN_PAKTA_INTEGRITAS_ID").", ".$this->getField("REKANAN_ID").", '".$this->getField("PATH_FILE")."',
					   '".$this->getField("TIPE")."', ".$this->getField("UKURAN").", '".$this->getField("NAMA_FILE")."', ".$this->USER_LOGIN_ID.", CURRENT_TIMESTAMP
				)";

		$this->query = $str;
		// echo $str;die();
		return $this->execQuery($str);
  }
 
  // NOT USE
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

  // NOT USE
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
 	
	function deletePI()
	{
        $str = "DELETE FROM REKANAN_PAKTA_INTEGRITAS
                WHERE
                  REKANAN_ID = ".$this->getField("REKANAN_ID")."";

		$this->query = $str;
        return $this->execQuery($str);
  }
 
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				SELECT *
				FROM REKANAN_PAKTA_INTEGRITAS 
				WHERE 1=1
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ORDER BY REKANAN_PAKTA_INTEGRITAS_ID ASC";
		// echo $str;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
  }
}
?>
