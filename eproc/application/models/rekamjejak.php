<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Rekamjejak extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
    function __construct()
	{
      // $this->Entity();
	 parent::__construct();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKAMID", $this->getNextId("REKAMID","REKAM_JEJAK"));

		$str = "

			INSERT INTO REKAM_JEJAK (
			   REKAMID, POSISI, KETERANGAN,
			   CREATED_BY, CREATED_DATE, USER_TYPE, PAKET_ID, PERMOHONAN_PAKET_ID, BROWSER, FLOW, CONTRACTINGREKANANID)
  			 	VALUES (
				  '".$this->getField("REKAMID")."',
  				  '".$this->getField("POSISI")."',
   				  '".$this->getField("KETERANGAN")."',
				  '".$this->getField("CREATED_BY")."',
				  '".$this->getField("CREATED_DATE")."',
				  '".$this->getField("USER_TYPE")."',
				  ".$this->getField("PAKET_ID").",
				  ".$this->getField("PERMOHONAN_PAKET_ID").",
				  '".$this->getField("BROWSER")."',
				  '".$this->getField("FLOW")."',
				  ".$this->getField("CONTRACTINGREKANANID")."
				)";

		//echo $str;exit;
		$this->query = $str;
		$this->id = $this->getField("REKAMID");
		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM REKAM_JEJAK
                WHERE
                  REKAMID = ".$this->getField("REKAMID")."";

		$this->query = $str;
        return $this->execQuery($str);
  }

  function deletePermohonan()
	{
        $str = "DELETE FROM REKAM_JEJAK
                WHERE
                  PERMOHONAN_PAKET_ID = ".$this->getField("ID")." OR PAKET_ID = ".$this->getField("ID")."" ;

		$this->query = $str;
        return $this->execQuery($str);
  }

    /**
    * Cari record berdasarkan array parameter dan limit tampilan
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","BERITA_METODE_EVALUASI_ID"=>"yyy")
    * @param int limit Jumlah maksimal record yang akan diambil
    * @param int from Awal record yang diambil
    * @return boolean True jika sukses, false jika tidak
    **/
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY REKAMID ASC ")
	{
		$str = " SELECT A.*
				 FROM REKAM_JEJAK A
				 WHERE 1=1 ";

		while(list($key,$val) = each($paramsArray))
		{
		    $pecah = explode("||", $key);
		    if (count($pecah) > 1) {
		        $str .= "AND $pecah[0] $pecah[1] $val ";
		    } else {
		        $str .= " AND $key = '$val' ";
		    }
		}
		$this->query = $str;

		$str .= $statement." ".$order;

		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsOR($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY REKAMID ASC ")
	{
		$str = " SELECT A.*
				 FROM V_REKAM_JEJAK A
				 WHERE 1=1 ";

		while(list($key,$val) = each($paramsArray))
		{
		    $pecah = explode("||", $key);
		    if (count($pecah) > 1) {
		        $str .= "AND $pecah[0] $pecah[1] $val ";
		    } else {
		        $str .= " AND $key = '$val' ";
		    }
		}
		$this->query = $str;

		$str .= $statement." ".$order;
		// echo $str;

		/*
		 SELECT a.rekamid,
		    a.posisi,
		    a.keterangan,
		    b.user_nama,
		    c.nama AS user_type_str,
		    a.created_date,
		    a.paket_id,
		    a.permohonan_paket_id,
		    concat(a.paket_id, ',', a.permohonan_paket_id) AS id,
				(
					select count(z.rekamid) as total from rekam_jejak z
					where a.posisi=z.posisi and (a.permohonan_paket_id=z.permohonan_paket_id or a.paket_id=z.paket_id)
					group by z.posisi, z.permohonan_paket_id, z.paket_id
					)
		   FROM ((rekam_jejak a
		     LEFT JOIN user_login b ON ((a.created_by = b.user_login_id)))
		     LEFT JOIN user_type c ON ((a.user_type = c.user_type_id)))
				 where a.permohonan_paket_id = 19
		  ORDER BY a.rekamid
		  */

		return $this->selectLimit($str,$limit,$from);
    }

  function selectByParamsOR2($permohonanid,$paketid=null)
	{

    if ($paketid) {
      $str = " SELECT A.* FROM V_REKAM_JEJAK A WHERE 1=1 AND A.PERMOHONAN_PAKET_ID = '".$permohonanid."' OR A.PAKET_ID='".$paketid."' ";
    } else {
      $str = " SELECT A.* FROM V_REKAM_JEJAK A WHERE 1=1 AND A.PERMOHONAN_PAKET_ID = '".$permohonanid."' ";
    }
    
		$this->query = $str;
		$str .= $statement." ".$order;
		return $this->selectLimit($str,$limit,$from);
  }


    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(A.REKAMID) AS ROWCOUNT
					FROM    V_REKAM_JEJAK A
					WHERE 1 = 1".$statement;
		while(list($key,$val) = each($paramsArray))
		{
		    $pecah = explode("||", $key);
		    if (count($pecah) > 1) {
		        $str .= "OR $pecah[0] $pecah[1] $val ";
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

  }
?>
