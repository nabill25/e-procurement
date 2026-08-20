<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class KatalogKategoriRekanan extends Entity{

	var $query;

    function __construct(){
  		parent::__construct();
	}

	function insert()
	{
	 	$this->setField("KATEGORI_REKANAN_ID", $this->getNextId("KATEGORI_REKANAN_ID","KATALOG_KATEGORI_REKANAN"));
		$str = "
		INSERT INTO  KATALOG_KATEGORI_REKANAN (
		   KATEGORI_REKANAN_ID, KATEGORI_ID, KATALOGID, DATE, CREATED_BY)
 			 	VALUES (
				  '".$this->getField("KATEGORI_REKANAN_ID")."',
  				  '".$this->getField("KATEGORI_ID")."',
  				  '".$this->getField("KATALOGID")."',
  				  CURRENT_TIMESTAMP,
				  ".$this->getField("CREATED_BY")."
				)";
				// echo $str; die();
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE KATALOG_KATEGORI_REKANAN SET
				  KATEGORI_ID = '".$this->getField("KATEGORI_ID")."',
				  KATALOGID = '".$this->getField("KATALOGID")."'
				WHERE KATEGORI_REKANAN_ID = '".$this->getField("KATEGORI_REKANAN_ID")."'
				";
				$this->query = $str;

		return $this->execQuery($str);
    }

	function delete()
	{

		$str = " DELETE FROM KATALOG_KATEGORI_REKANAN WHERE
					KATALOGID = '".$this->getField("KATALOGID")."' ";

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
		$str = "SELECT A.*, B.NAMA
				FROM KATALOG_KATEGORI_REKANAN A
				INNER JOIN KATALOG_KATEGORI B ON A.KATEGORI_ID=B.KATEGORI_ID
				WHERE A.KATEGORI_REKANAN_ID IS NOT NULL
				";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ORDER BY KATEGORI_REKANAN_ID ASC";
		// echo $str; die();
		$this->query = $str;

		return $this->selectLimit($str,$limit,$from);
    }

  }
?>
