<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

  include_once('entity.php');

  class Vendorretail extends Entity
  {

	var $query;

  function __construct(){
  		parent::__construct();
	}


	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_RETAIL_ID", $this->getNextId("REKANAN_RETAIL_ID","REKANAN_RETAIL"));

    $KODE = str_replace(" ","",'RR-'.$this->generateNomorUrut($this->getField("REKANAN_RETAIL_ID")));

		$str = "
		INSERT INTO REKANAN_RETAIL (
		   REKANAN_RETAIL_ID, REKANAN_TIPE_ID, KODE, NAMA, NPWP, ALAMAT, TELEPON_KODE, TELEPON, TANGGAL_DAFTAR,
       KOTA, WHATSAPP, REGION_ID, KONTAK_PERSON, KONTAK_PERSON_HP,CREATED_BY,CREATED_DATE)
 			 	VALUES (
          ".$this->getField("REKANAN_RETAIL_ID").",
          ".$this->getField("REKANAN_TIPE_ID").",
				  '".$KODE."',
          '".$this->getField("NAMA")."',
          '".$this->getField("NPWP")."',
          '".$this->getField("ALAMAT")."',
          '".$this->getField("TELEPON_KODE")."',
          '".$this->getField("TELEPON")."',
          ".$this->getField("TANGGAL_DAFTAR").",
          '".$this->getField("KOTA")."',
          '".$this->getField("WHATSAPP")."',
          '".$this->getField("REGION_ID")."',
          '".$this->getField("KONTAK_PERSON")."',
          '".$this->getField("KONTAK_PERSON_HP")."',
				  ".$this->getField("CREATED_BY").",
          CURRENT_TIMESTAMP
				)";
		$this->query = $str;
		// echo $str;exit;
		return $this->execQuery($str);
  }

  function checkNpwp($npwp)
  {
      $q = "select NPWP from REKANAN_RETAIL
      where REPLACE(REPLACE ( NPWP, '.', '-' ), ' ', '') = REPLACE(REPLACE ('".$npwp."','.','-'), ' ', '') ";
      $this->query = $q;
      return $this->selectLimit($q);
  }

  function generateNomorUrut($number, $length = 6) {
    return str_pad($number, $length, '0', STR_PAD_LEFT);
  }

	 function update()
	{
		$str = "UPDATE REKANAN_RETAIL SET
        REKANAN_TIPE_ID = ".$this->getField("REKANAN_TIPE_ID").",
         NAMA = '".$this->getField("NAMA")."',
         NPWP = '".$this->getField("NPWP")."',
         ALAMAT = '".$this->getField("ALAMAT")."',
         TELEPON_KODE = '".$this->getField("TELEPON_KODE")."',
         TELEPON = '".$this->getField("TELEPON")."',
         EMAIL = '".$this->getField("EMAIL")."',
         TANGGAL_DAFTAR = ".$this->getField("TANGGAL_DAFTAR").",
         KOTA = '".$this->getField("KOTA")."',
         KODEPOS = '".$this->getField("KODEPOS")."',
         WHATSAPP = '".$this->getField("WHATSAPP")."',
         REGION_ID = '".$this->getField("REGION_ID")."',
         KONTAK_PERSON = '".$this->getField("KONTAK_PERSON")."',
         KONTAK_PERSON_HP = '".$this->getField("KONTAK_PERSON_HP")."',
         UPDATED_BY = '".$this->getField("CREATED_BY")."',
         UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE REKANAN_RETAIL_ID = ".$this->getField("REKANAN_RETAIL_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
  }

	function delete()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "DELETE FROM REKANAN_RETAIL
				WHERE REKANAN_RETAIL_ID = ".$this->getField("REKANAN_RETAIL_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
  }

  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="ORDER BY KODE ASC")
	{
		$str = "SELECT
            REKANAN_RETAIL_ID, REKANAN_TIPE_ID, KODE, NAMA, NPWP, ALAMAT, TELEPON_KODE, TELEPON, EMAIL, TANGGAL_DAFTAR, PKP,
            PKP_TANGGAL, KOTA, KODEPOS, WHATSAPP, REGION_ID, KONTAK_PERSON, KONTAK_PERSON_HP, CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE
					FROM REKANAN_RETAIL
				WHERE 1 = 1
				";

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
		$str = "SELECT COUNT(REKANAN_RETAIL_ID) AS ROWCOUNT FROM REKANAN_RETAIL A
				WHERE 1 = 1 ";
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
