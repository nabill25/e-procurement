<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Rekananurlvalidasiallow extends Entity{

	var $query;
  function __construct()
	{
      $this->Entity();
  }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_ID", $this->getNextId("REKANAN_ID","REKANAN"));

		$str = "
					INSERT INTO  REKANAN (
					   REKANAN_ID, IJIN_USAHA_ID, REKANAN_TIPE_ID,
					   REKANAN_KUALIFIKASI_ID, KODE, NAMA,
					   NPWP, ALAMAT, TELEPON_KODE,
					   TELEPON, FAX_KODE, FAX,
					   EMAIL, STATUS_PERUSAHAAN, STATUS_VALIDASI,
					   STATUS_CP, TANGGAL_DAFTAR, TANGGAL_VALIDASI)
 			  	VALUES (
						  ".$this->getField("REKANAN_ID").",
						  ".$this->getField("IJIN_USAHA_ID").",
						  ".$this->getField("REKANAN_TIPE_ID").",
						  ".$this->getField("REKANAN_KUALIFIKASI_ID").",
						  '".$this->getField("KODE")."',
						  '".$this->getField("NAMA")."',
						  '".$this->getField("NPWP")."',
						  '".$this->getField("ALAMAT")."',
						  '".$this->getField("TELEPON_KODE")."',
						  '".$this->getField("TELEPON")."',
						  '".$this->getField("FAX_KODE")."',
						  '".$this->getField("FAX")."',
						  '".$this->getField("EMAIL")."',
						  ".$this->getField("STATUS_PERUSAHAAN").",
						  ".$this->getField("STATUS_VALIDASI").",
						  ".$this->getField("STATUS_CP").",
						  CURRENT_DATE
						)";
				//'".$this->getField("TANGGAL_DAFTAR")."'
						// echo $str; die();
		$this->query = $str;
		$this->id = $this->getField("REKANAN_ID");
		return $this->execQuery($str);
  }

  function insertAllowLogin()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("ID", $this->getNextId("ID","REKANAN_URL_VALIDASI_ALLOW"));

		$str = "
					INSERT INTO REKANAN_URL_VALIDASI_ALLOW (
					   ID, REKANAN_ID, ALLOW_URL,
					   CREATED_BY, CREATED_DATE)
 			  	VALUES (
						  ".$this->getField("ID").",
						  ".$this->getField("REKANAN_ID").",
						  '".$this->getField("ALLOW_URL")."',
						  ".$this->getField("CREATED_BY").", 
						  CURRENT_DATE
						)";
				//'".$this->getField("TANGGAL_DAFTAR")."'
						// echo $str; die();
		$this->query = $str; 
		return $this->execQuery($str);
  }
 
  function update()
	{ 
		$str = "
				UPDATE  REKANAN
				SET
					   REKANAN_TIPE_ID        = '".$this->getField("REKANAN_TIPE_ID")."',
					   REKANAN_KUALIFIKASI_ID = '".$this->getField("REKANAN_KUALIFIKASI_ID")."',
					   NAMA                   = '".$this->getField("NAMA")."',
					   NPWP                   = '".$this->getField("NPWP")."',
					   ALAMAT                 = '".$this->getField("ALAMAT")."',
					   TELEPON_KODE           = '".$this->getField("TELEPON_KODE")."',
					   TELEPON                = '".$this->getField("TELEPON")."',
					   MATA_UANG_KODE		  = '".$this->getField("MATA_UANG_KODE")."'
				WHERE  REKANAN_ID             = '".$this->getField("REKANAN_ID")."'

			 ";
				$this->query = $str;
				// echo $str;exit;
		return $this->execQuery($str);
  }
  
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY A.ID DESC")
	{
		$str = "
			SELECT A.* FROM REKANAN_URL_VALIDASI_ALLOW A 
             WHERE 1=1
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

  function selectByParamsAllow($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY A.ID DESC")
	{
		$str = "
			SELECT a.* from (
					SELECT unnest(string_to_array(allow_url,',')) url , rekanan_id, id
					FROM rekanan_url_validasi_allow  
				) a
				WHERE 1=1
				";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ".$order;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
  }

  function selectByParamsURL($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY A.ID DESC")
	{
		$str = "
			SELECT A.* FROM REKANAN_URL_VALIDASI A 
             WHERE 1=1
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
