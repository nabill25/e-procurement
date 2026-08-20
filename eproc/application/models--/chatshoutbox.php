<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class ChatShoutbox extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
    function ChatShoutbox()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		//$this->setField("JAM", $this->getNextId("JAM","PHPSHOUTBOX"));

		$str = "
				INSERT INTO CHATSHOUTBOX (
				   JAM, NAMA, PESAN,
   					IP_ADDRESS, REKANAN_ID, PAKET_PENAWARAN_ID, KODE)
				VALUES (
				  '".$this->getField("JAM")."',
				  '".$this->getField("NAMA")."',
				  '".$this->getField("PESAN")."',
				  '".$this->getField("IP_ADDRESS")."',
				  ".$this->getField("REKANAN_ID").",
				  '".$this->getField("PAKET_PENAWARAN_ID")."',
				  '".$this->getField("KODE")."'
				)";
		$this->query = $str;

		return $this->execQuery($str);
    }

    function insert2()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		//$this->setField("JAM", $this->getNextId("JAM","PHPSHOUTBOX"));
		$this->setField("CHATID", $this->getNextId("CHATID","CHATSHOUTBOX"));

		$str = "
				INSERT INTO CHATSHOUTBOX (
				    CHATID, JAM, NAMA, PESAN,
   					IP_ADDRESS, REKANAN_ID, PAKET_ID, JENIS_CHAT, USER_LOGIN_ID,WAKTU)
				VALUES (
				  '".$this->getField("CHATID")."',
				  '".$this->getField("JAM")."',
				  '".$this->getField("NAMA")."',
				  '".$this->getField("PESAN")."',
				  '".$this->getField("IP_ADDRESS")."',
				  ".$this->getField("REKANAN_ID").",
				  '".$this->getField("PAKET_ID")."',
				  '".$this->getField("JENIS_CHAT")."',
				  ".$this->getField("USER_LOGIN_ID").",
				  CURRENT_TIMESTAMP
				)";
		// echo $str; die();
		$this->query = $str;

		return $this->execQuery($str);
  }

  function insert3()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		//$this->setField("JAM", $this->getNextId("JAM","PHPSHOUTBOX"));
		$this->setField("CHATID", $this->getNextId("CHATID","CHATSHOUTBOX"));

		$str = "
				INSERT INTO CHATSHOUTBOX (
				    CHATID, JAM, NAMA, PESAN,
   					IP_ADDRESS, REKANAN_ID, PAKET_ID, JENIS_CHAT, USER_LOGIN_ID,FILE,WAKTU)
				VALUES (
				  '".$this->getField("CHATID")."',
				  '".$this->getField("JAM")."',
				  '".$this->getField("NAMA")."',
				  '".$this->getField("PESAN")."',
				  '".$this->getField("IP_ADDRESS")."',
				  ".$this->getField("REKANAN_ID").",
				  '".$this->getField("PAKET_ID")."',
				  '".$this->getField("JENIS_CHAT")."',
				  ".$this->getField("USER_LOGIN_ID").",
				  '".$this->getField("FILE")."',
				  CURRENT_TIMESTAMP
				)";
		// echo $str; die();
		$this->query = $str;

		return $this->execQuery($str);
  }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE CHATSHOUTBOX SET
				  NAMA = '".$this->getField("NAMA")."'
				WHERE JAM = '".$this->getField("JAM")."'
				";
				$this->query = $str;

		return $this->execQuery($str);
    }

    function updateRead()
  {
    /*Auto-generate primary key(s) by next max value (integer) */
    $str = "UPDATE CHATSHOUTBOX SET
          IS_READ = '1'
        WHERE JENIS_CHAT = '".$this->getField("JENIS_CHAT")."' AND PAKET_ID = ".$this->getField("PAKET_ID")." AND REKANAN_ID = ".$this->getField("REKANAN_ID")."
        "; 
        $this->query = $str;

    return $this->execQuery($str);
    }

    function updateBaca($tiketId, $pegawaiId)
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE CHATSHOUTBOX SET
				  STATUS_BACA = 1
				WHERE TIKET_ID = '".$tiketId."' AND
					  NOT REKANAN_ID = '".$pegawaiId."'
				";
				$this->query = $str;

		return $this->execQuery($str);
    }


	function delete()
	{
        $str = "DELETE FROM CHATSHOUTBOX
                WHERE
                  JAM = '".$this->getField("JAM")."'";

		$this->query = $str;
        return $this->execQuery($str);
    }

	function deleteParentChild()
	{
        $str = "DELETE FROM CHATSHOUTBOX
                WHERE
                  NAMA = '".$this->getField("NAMA")."'";

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
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=' ORDER BY JAM ASC ')
	{
		$str = "SELECT CHATID, JAM, NAMA, PESAN,
   					IP_ADDRESS, PAKET_PENAWARAN_ID, KODE, TO_CHAR(WAKTU, 'DD/MM/YYYY HH24:MI:SS') WAKTU, PAKET_ID, JENIS_CHAT, USER_LOGIN_ID, FILE
				FROM CHATSHOUTBOX A WHERE 1=1 ";
		//JAM IS NOT NULL
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
	}

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT JAM, NAMA
				FROM CHATSHOUTBOX WHERE JAM IS NOT NULL";

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
		$str = "SELECT COUNT(JAM) AS ROWCOUNT FROM CHATSHOUTBOX WHERE JAM IS NOT NULL ";
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
		$str = "SELECT COUNT(JAM) AS ROWCOUNT FROM CHATSHOUTBOX WHERE JAM IS NOT NULL ";
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
