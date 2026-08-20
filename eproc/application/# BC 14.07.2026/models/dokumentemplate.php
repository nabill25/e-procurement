<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Dokumentemplate extends Entity{

	var $query;
  function __construct()
	{
	   parent::__construct();
  }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("TEMPLATE_ID", $this->getNextId("TEMPLATE_ID","DOKUMEN_TEMPLATE")); 

		$str = "

			INSERT INTO DOKUMEN_TEMPLATE (
			   TEMPLATE_ID, USER_LOGIN_ID, NAMA,
			   KETERANGAN, GAMBAR, LAMPIRAN, TANGGAL)

  			 	VALUES (
				  '".$this->getField("TEMPLATE_ID")."',
  				  '".$this->getField("USER_LOGIN_ID")."',
   				  '".$this->getField("NAMA")."',
				  '".$this->getField("KETERANGAN")."',
				  '".$this->getField("GAMBAR")."',
  				  '".$this->getField("LAMPIRAN")."',
				  ".$this->getField("TANGGAL")."
				)";

		$this->query = $str;
		$this->id = $this->getField("TEMPLATE_ID");
		//echo $str;exit;
		return $this->execQuery($str);
    }


    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				 UPDATE  DOKUMEN_TEMPLATE
				SET
					   NAMA        = '".$this->getField("NAMA")."',
					   KETERANGAN  = '".$this->getField("KETERANGAN")."',
					   GAMBAR      = '".$this->getField("GAMBAR")."',
					   LAMPIRAN    = '".$this->getField("LAMPIRAN")."',
					   TANGGAL	   =  ".$this->getField("TANGGAL")."
				WHERE  TEMPLATE_ID   =  ".$this->getField("TEMPLATE_ID")."

				";
				$this->query = $str;
		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM DOKUMEN_TEMPLATE
                WHERE
                  TEMPLATE_ID = ".$this->getField("TEMPLATE_ID")."";

		$this->query = $str;
        return $this->execQuery($str);
    }

    /**
    * Cari record berdasarkan array parameter dan limit tampilan
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","DOKUMEN_TEMPLATE_METODE_EVALUASI_ID"=>"yyy")
    * @param int limit Jumlah maksimal record yang akan diambil
    * @param int from Awal record yang diambil
    * @return boolean True jika sukses, false jika tidak
    **/
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY TANGGAL DESC ")
	{
		$str = "
					SELECT
					TEMPLATE_ID, USER_LOGIN_ID, NAMA,
			   		KETERANGAN, GAMBAR, LAMPIRAN, TO_CHAR(TANGGAL, 'YYYY-MM-DD') TANGGAL
					FROM DOKUMEN_TEMPLATE
				    WHERE TEMPLATE_ID IS NOT NULL ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;

		$str .= $statement." ".$order;

		return $this->selectLimit($str,$limit,$from);
    }


    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(A.TEMPLATE_ID) AS ROWCOUNT
					FROM    DOKUMEN_TEMPLATE A
					WHERE 1 = 1".$statement;
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
