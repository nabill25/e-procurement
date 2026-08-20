<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PaketRekananPassword extends Entity{

	var $query;
    /**
    * Class constructor.
    **/

    function __construct(){
	  parent::__construct();
	}
    function PaketRekananPassword()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_REKANAN_PASSWORD_ID", $this->getNextId("PAKET_REKANAN_PASSWORD_ID","PAKET_REKANAN_PASSWORD"));

		$str = "
				INSERT INTO PAKET_REKANAN_PASSWORD (
				   PAKET_REKANAN_PASSWORD_ID, PAKET_REKANAN_ID, PENAWARAN_PASSWORD,
				   PENAWARAN_PASSWORD2, CREATED_DATE, CREATED_BY)
				VALUES (".$this->getField("PAKET_REKANAN_PASSWORD_ID").", ".$this->getField("PAKET_REKANAN_ID").", '".$this->getField("PENAWARAN_PASSWORD")."',
				   '".$this->getField("PENAWARAN_PASSWORD2")."', CURRENT_TIMESTAMP, ".$this->getField("CREATED_BY").")
			";

		$this->query = $str;

		return $this->execQuery($str);
  }

	function delete()
	{
    $str = "DELETE FROM PAKET_REKANAN_PASSWORD
            WHERE
            PAKET_REKANAN_PASSWORD_ID = ".$this->getField("PAKET_REKANAN_PASSWORD_ID")."";

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
 
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $paketId='', $order='ORDER BY PAKET_REKANAN_ID ASC')
	{
		$str = "
				SELECT PAKET_REKANAN_PASSWORD_ID, PAKET_REKANAN_ID, PENAWARAN_PASSWORD, PENAWARAN_PASSWORD2, CREATED_DATE, CREATED_BY
				FROM PAKET_REKANAN_PASSWORD
				WHERE  1=1
				";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ".$order;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
  }

  function selectByParamsLimit1($paramsArray=array(),$limit=-1,$from=-1, $statement='', $paketId='', $order='LIMIT 1')
	{
		$str = "
				SELECT PAKET_REKANAN_PASSWORD_ID, PAKET_REKANAN_ID, PENAWARAN_PASSWORD, PENAWARAN_PASSWORD2, CREATED_DATE, CREATED_BY
				FROM PAKET_REKANAN_PASSWORD
				WHERE  1=1
				";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
  }
}
 
  
?>
