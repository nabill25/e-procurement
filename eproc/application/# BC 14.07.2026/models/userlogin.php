<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class UserLogin extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
    function UserLogin()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("USER_LOGIN_ID", $this->getNextId("USER_LOGIN_ID","USER_LOGIN"));

		$str = "
				INSERT INTO USER_LOGIN (
				   USER_LOGIN_ID, USER_TYPE_ID, REKANAN_ID,
				   USER_NAMA, USER_JABATAN, USER_ALAMAT,
				   USER_TELEPON, USER_LOGIN, USER_PASSWORD, USER_STATUS)
  			 	VALUES (
				  ".$this->getField("USER_LOGIN_ID").",
  				  ".$this->getField("USER_TYPE_ID").",
				  ".$this->getField("REKANAN_ID").",
    			  '".$this->getField("USER_NAMA")."',
      			  '".$this->getField("USER_JABATAN")."',
  				  '".$this->getField("USER_ALAMAT")."',
				  '".$this->getField("USER_TELEPON")."',
				  '".$this->getField("USER_LOGIN")."',
				  '".$this->getField("USER_PASSWORD")."',
  				  ".$this->getField("USER_STATUS")."
				)";
		//echo $str;
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				UPDATE USER_LOGIN
				SET
					   USER_TYPE_ID    = ".$this->getField("USER_TYPE_ID").",
					   USER_NAMA       = '".$this->getField("USER_NAMA")."',
					   USER_JABATAN    = '".$this->getField("USER_JABATAN")."',
					   USER_ALAMAT     = '".$this->getField("USER_ALAMAT")."',
					   USER_TELEPON    = '".$this->getField("USER_TELEPON")."'
				WHERE  USER_LOGIN_ID   = ".$this->getField("USER_LOGIN_ID")."
 				";
				/*
				REKANAN_ID      = ".$this->getField("REKANAN_ID").",
				USER_LOGIN      = ".$this->getField("USER_LOGIN").",
					   USER_PASSWORD   = '".$this->getField("USER_PASSWORD")."',
					   USER_IS_LOGIN   = ".$this->getField("USER_LOGIN").",
					   USER_LAST_LOGIN = '".$this->getField("USER_LAST_LOGIN")."',
					   USER_STATUS     = ".$this->getField("USER_STATUS")."*/
				$this->query = $str;
		return $this->execQuery($str);
    }

	function validasi()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				UPDATE USER_LOGIN
				SET
					   USER_STATUS    = 1
				WHERE  REKANAN_ID   = (SELECT REKANAN_ID FROM REKANAN WHERE KODE =  '".$this->getField("KODE")."')
 				";
		$this->execQuery($str);

		$str1 = "UPDATE REKANAN
				SET
					   STATUS_VALIDASI  = 1
				WHERE  KODE = '".$this->getField("KODE")."'";
		return $this->execQuery($str1);
    }

  function aktivasiAkun()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				UPDATE USER_LOGIN
				SET    USER_AKTIF = '1',
        USER_AUTH = '".$this->getField("USER_AUTH")."'
				WHERE  USER_LOGIN_ID   = ".$this->getField("USER_LOGIN_ID")."
 				";
				$this->query = $str;
				//echo $str;exit;
		return $this->execQuery($str);
    }

	function updateStatus()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				UPDATE USER_LOGIN
				SET    USER_STATUS = 2
				WHERE  USER_LOGIN_ID   = ".$this->getField("USER_LOGIN_ID")."
 				";
				$this->query = $str;
				//echo $str;exit;
		return $this->execQuery($str);
    }

	function updateStatusRevisiRekanan()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				UPDATE USER_LOGIN
				SET    USER_STATUS = 0
				WHERE  REKANAN_ID   = ".$this->getField("REKANAN_ID")."
 				";
				$this->query = $str;
				//echo $str;exit;
		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM USER_LOGIN
                WHERE
                  USER_LOGIN_ID = ".$this->getField("USER_LOGIN_ID")."";

		$this->query = $str;
        return $this->execQuery($str);
    }

    /**
    * Cari record berdasarkan array parameter dan limit tampilan
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","TANGGAL"=>"yyy")
    * @param int limit Jumlah maksimal record yang akan diambil
    * @param int from Awal record yang diambil
    * @return boolean True jika sukses, false jika tidak
    **/
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT USER_LOGIN_ID, USER_TYPE_ID, REKANAN_ID, NIP,
				   USER_NAMA, USER_JABATAN, USER_ALAMAT, UNIT_KERJA_ID,
				   USER_TELEPON, USER_LOGIN, USER_PASSWORD, CHILD_PL,
				   USER_IS_LOGIN, USER_LAST_LOGIN, USER_STATUS, USER_AKTIF, USER_AUTH, TENDER, USER_JABATAN_PANITIA, PENUNJUK_PIC, LEGAL, KASI_PENGGUNA
				FROM USER_LOGIN
				WHERE USER_LOGIN_ID IS NOT NULL
				";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY USER_LOGIN_ID ASC";
		//echo $str;
		return $this->selectLimit($str,$limit,$from);
  }

  function selectByParams2($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT USER_LOGIN_ID, A.USER_TYPE_ID, REKANAN_ID, NIP,
				   USER_NAMA, USER_JABATAN, USER_ALAMAT, UNIT_KERJA_ID,
				   USER_TELEPON, USER_LOGIN, USER_PASSWORD, CHILD_PL,
				   USER_IS_LOGIN, USER_LAST_LOGIN, USER_STATUS, USER_AKTIF, USER_AUTH, TENDER, USER_JABATAN_PANITIA, PENUNJUK_PIC, LEGAL,
				   B.NAMA TYPE_NAMA
				FROM USER_LOGIN A 
				LEFT JOIN USER_TYPE B ON A.USER_TYPE_ID=B.USER_TYPE_ID
				WHERE A.USER_LOGIN_ID IS NOT NULL
				";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY USER_LOGIN_ID ASC";
		//echo $str;
		return $this->selectLimit($str,$limit,$from);
  }

  function selectKepalaPengadaan($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		// $str = "SELECT USER_LOGIN_ID, USER_TYPE_ID, REKANAN_ID, NIP,
		// 		   USER_NAMA, USER_JABATAN, USER_ALAMAT,
		// 		   USER_TELEPON, USER_LOGIN, USER_PASSWORD, CHILD_PL,
		// 		   USER_IS_LOGIN, USER_LAST_LOGIN, USER_STATUS, USER_AKTIF, USER_AUTH, TENDER, USER_JABATAN_PANITIA, PENUNJUK_PIC
		// 		FROM USER_LOGIN WHERE USER_LOGIN_ID IS NOT NULL AND USER_AKTIF = '1' AND PENUNJUK_PIC = '1'
		// 		UNION
		// 		SELECT USER_LOGIN_ID, USER_TYPE_ID, REKANAN_ID, NIP,
		// 		   USER_NAMA, USER_JABATAN, USER_ALAMAT,
		// 		   USER_TELEPON, USER_LOGIN, USER_PASSWORD, CHILD_PL,
		// 		   USER_IS_LOGIN, USER_LAST_LOGIN, USER_STATUS, USER_AKTIF, USER_AUTH, TENDER, USER_JABATAN_PANITIA, PENUNJUK_PIC
		// 		FROM USER_LOGIN WHERE USER_LOGIN_ID IS NOT NULL AND USER_AKTIF = '1' AND USER_TYPE_ID = 7
		// 		";
		$str = " 
				SELECT USER_LOGIN_ID, USER_TYPE_ID, REKANAN_ID, NIP,
				   USER_NAMA, USER_JABATAN, USER_ALAMAT,
				   USER_TELEPON, USER_LOGIN, USER_PASSWORD, CHILD_PL,
				   USER_IS_LOGIN, USER_LAST_LOGIN, USER_STATUS, USER_AKTIF, USER_AUTH, TENDER, USER_JABATAN_PANITIA, PENUNJUK_PIC
				FROM USER_LOGIN WHERE USER_LOGIN_ID IS NOT NULL AND USER_AKTIF = '1' AND USER_TYPE_ID = 7
				";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY USER_TYPE_ID ASC";
		//echo $str;
		return $this->selectLimit($str,$limit,$from);
  }

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT USER_LOGIN_ID, USER_TYPE_ID, REKANAN_ID,
				   USER_NAMA, USER_JABATAN, USER_ALAMAT,
				   USER_TELEPON, USER_LOGIN, USER_PASSWORD, CHILD_PL,
				   USER_IS_LOGIN, USER_LAST_LOGIN, USER_STATUS
				FROM USER_LOGIN WHERE USER_LOGIN_ID IS NOT NULL";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY USER_NAMA	 ASC";
		return $this->selectLimit($str,$limit,$from);
    }
    /**
    * Hitung jumlah record berdasarkan parameter (array).
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","TANGGAL"=>"yyy")
    * @return long Jumlah record yang sesuai kriteria
    **/
    function getCountByParams($paramsArray=array(),$stat="")
	{
		$str = "SELECT COUNT(USER_LOGIN_ID) AS ROWCOUNT FROM USER_LOGIN WHERE USER_LOGIN_ID IS NOT NULL ".$stat;
		while(list($key,$val)=each($paramsArray))
		{
			// ikn 20190218
		    $pecah = explode("||", $key);
		    if (count($pecah) > 1) {
		        $str .= "AND $pecah[0] $pecah[1] $val ";
		    } else {
		        $str .= " AND $key = '$val' ";
		    }
		}
		// echo $str; die();
		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(USER_LOGIN_ID) AS ROWCOUNT FROM USER_LOGIN WHERE USER_LOGIN_ID IS NOT NULL ";
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
