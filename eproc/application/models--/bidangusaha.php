<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class BidangUsaha extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
 //    function BidangUsaha()
	// {
 //      $this->Entity();
 //    }

    function __construct(){
  		parent::__construct();
	}

	function insert()
	{
		$str = "
		INSERT INTO  BIDANG_USAHA (
		   BIDANG_USAHA_ID, BIDANG_USAHA_PARENT_ID, KODE,
		   NAMA)
 			 	VALUES (
				  '".$this->getField("BIDANG_USAHA_ID")."',
  				  '".$this->getField("BIDANG_USAHA_PARENT_ID")."',
				  '".$this->getField("KODE")."',
				  '".$this->getField("NAMA")."'
				)";
		$this->query = $str;
		return $this->execQuery($str);
    }

  function insert2()
	{
		$str = "
		INSERT INTO  BIDANG_USAHA (
		   BIDANG_USAHA_ID, BIDANG_USAHA_PARENT_ID, KODE, NAMA, STATUS_BIDANG_USAHA, BIDANG_USAHA_JENIS)
 			 	VALUES (
				  '".$this->getField("BIDANG_USAHA_ID")."',
  				  '".$this->getField("BIDANG_USAHA_PARENT_ID")."',
				  '".$this->getField("KODE")."',
				  '".$this->getField("NAMA")."',
          '".$this->getField("STATUS_BIDANG_USAHA")."',
				  '".$this->getField("BIDANG_USAHA_JENIS")."'
				)";
				// echo $str; die;

		$this->query = $str;
		return $this->execQuery($str);
  }

  function insert3()
	{
		$str = "
		INSERT INTO  BIDANG_USAHA (
		   BIDANG_USAHA_ID, BIDANG_USAHA_PARENT_ID, KODE, NAMA, STATUS_BIDANG_USAHA, BIDANG_USAHA_JENIS, CREATED_BY, CREATED_DATE)
 			 	VALUES (
				  '".$this->getField("BIDANG_USAHA_ID")."',
  				  '".$this->getField("BIDANG_USAHA_PARENT_ID")."',
				  '".$this->getField("KODE")."',
				  '".$this->getField("NAMA")."',
          '".$this->getField("STATUS_BIDANG_USAHA")."',
				  '".$this->getField("BIDANG_USAHA_JENIS")."',
				  '".$this->getField("CREATED_BY")."',
				  NOW()
				)";
				// echo $str; die;

		$this->query = $str;
		return $this->execQuery($str);
  }

    function update()
	{
		$str = "UPDATE BIDANG_USAHA SET
				  KODE = '".$this->getField("KODE")."',
				  NAMA = '".$this->getField("NAMA")."'
				WHERE BIDANG_USAHA_ID = '".$this->getField("BIDANG_USAHA_ID")."'
				";
				$this->query = $str;

		return $this->execQuery($str);
    }

    function update2()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE BIDANG_USAHA SET
            BIDANG_USAHA_ID = '".$this->getField("BIDANG_USAHA_ID_UPDATE")."',
				    KODE = '".$this->getField("KODE")."',
            NAMA = '".$this->getField("NAMA")."',
		        STATUS_BIDANG_USAHA = '".$this->getField("STATUS_BIDANG_USAHA")."'
		        WHERE BIDANG_USAHA_ID = '".$this->getField("BIDANG_USAHA_ID")."'
		        ";
				$this->query = $str;

		return $this->execQuery($str);
    }

	function delete()
	{
		/*
		$str1 = "DELETE FROM BIDANG_USAHA
                WHERE
                  BIDANG_USAHA_PARENT_ID = '".$this->getField("BIDANG_USAHA_ID")."'";

		$this->query = $str1;
        $this->execQuery($str1);

        $str = "DELETE FROM BIDANG_USAHA
                WHERE
                  BIDANG_USAHA_ID = '".$this->getField("BIDANG_USAHA_ID")."'";
		*/

    $str = "DELETE FROM BIDANG_USAHA
            WHERE
              BIDANG_USAHA_ID = '".$this->getField("BIDANG_USAHA_ID")."'";
		// $str = " UPDATE BIDANG_USAHA SET STATUS_BIDANG_USAHA = 0 WHERE
		// 			BIDANG_USAHA_ID = '".$this->getField("BIDANG_USAHA_ID")."' ";

		$this->query = $str;
        return $this->execQuery($str);
    }

  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT BIDANG_USAHA_ID, BIDANG_USAHA_PARENT_ID, KODE, NAMA, REPLACE(NAMA, ',', ';') NAMA_TANPA_KOMA,
				-- CONCAT(BIDANG_USAHA_ID,' - ', AMBIL_BIDANG_USAHA_NAMA(BIDANG_USAHA_ID)) NAMA_VIEW
				CONCAT(BIDANG_USAHA_ID,' - ', NAMA) NAMA_VIEW
				FROM BIDANG_USAHA A WHERE BIDANG_USAHA_ID IS NOT NULL AND STATUS_BIDANG_USAHA = '1' ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ORDER BY BIDANG_USAHA_ID ASC";
		// echo $str; die();
		$this->query = $str;

		return $this->selectLimit($str,$limit,$from);
  }

  function selectByParamsOwn($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT A.BIDANG_USAHA_ID, A.BIDANG_USAHA_PARENT_ID, A.KODE, A.NAMA, REPLACE(A.NAMA, ',', ';') NAMA_TANPA_KOMA, CONCAT(A.BIDANG_USAHA_ID,' - ', A.NAMA) NAMA_VIEW
				FROM BIDANG_USAHA A 
				JOIN REKANAN_BIDANG_USAHA B ON A.BIDANG_USAHA_ID = B.BIDANG_USAHA_ID
				WHERE A.BIDANG_USAHA_ID IS NOT NULL AND A.STATUS_BIDANG_USAHA = '1' ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ORDER BY BIDANG_USAHA_ID ASC";
		// echo $str; die();
		$this->query = $str;

		return $this->selectLimit($str,$limit,$from);
  }

    function selectByParamsAll($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT BIDANG_USAHA_ID, BIDANG_USAHA_PARENT_ID, KODE, NAMA, REPLACE(NAMA, ',', ';') NAMA_TANPA_KOMA,
				-- CONCAT(BIDANG_USAHA_ID,' - ', AMBIL_BIDANG_USAHA_NAMA(BIDANG_USAHA_ID)) NAMA_VIEW
				CONCAT(BIDANG_USAHA_ID,' - ', NAMA) NAMA_VIEW, STATUS_BIDANG_USAHA
				FROM BIDANG_USAHA A WHERE BIDANG_USAHA_ID IS NOT NULL ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ORDER BY BIDANG_USAHA_ID ASC";
		$this->query = $str;

		return $this->selectLimit($str,$limit,$from);
    }


  function selectByParamsGroup($jenis)
	{
		$str = "SELECT BIDANG_USAHA_PARENT_ID from BIDANG_USAHA 
						where BIDANG_USAHA_PARENT_ID != '0' and BIDANG_USAHA_JENIS = '".$jenis."'
						group by BIDANG_USAHA_PARENT_ID
						order by BIDANG_USAHA_PARENT_ID asc
						 ";

		$this->query = $str;

		return $this->selectLimit($str,$limit,$from);
  }

    // ikn 20190306
 //    function selectByParamsParent($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	// {
	// 	$str = "SELECT BIDANG_USAHA_ID, BIDANG_USAHA_PARENT_ID, KODE, NAMA, REPLACE(NAMA, ',', ';') NAMA_TANPA_KOMA,
	// 			CONCAT(BIDANG_USAHA_ID, '- ', REPLACE(NAMA, ',', ';')) NAMA_VIEW
	// 			FROM BIDANG_USAHA A WHERE BIDANG_USAHA_ID IS NOT NULL AND STATUS_BIDANG_USAHA = '1' ";

	// 	while(list($key,$val) = each($paramsArray))
	// 	{
	// 		$str .= " AND $key = '$val' ";
	// 	}

	// 	$this->query = $str;
	// 	$str .= $statement." ORDER BY BIDANG_USAHA_ID ASC";

	// 	return $this->selectLimit($str,$limit,$from);
 //    }

    function selectByParamsPanelBidangUsaha($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				SELECT A.BIDANG_USAHA_ID, A.BIDANG_USAHA_PARENT_ID, A.NAMA FROM BIDANG_USAHA_PANEL A
                WHERE 1 = 1 AND STATUS_BIDANG_USAHA = 1
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY A.BIDANG_USAHA_ID ASC";

		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsPanelRekananBidangUsaha($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				SELECT A.BIDANG_USAHA_ID, A.BIDANG_USAHA_PARENT_ID, A.NAMA FROM BIDANG_USAHA_PANEL_REKANAN A
                WHERE 1 = 1	 AND STATUS_BIDANG_USAHA = 1
		";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY A.BIDANG_USAHA_ID ASC";

		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT  BIDANG_USAHA_ID, BIDANG_USAHA_PARENT_ID, KODE, NAMA
				FROM BIDANG_USAHA WHERE BIDANG_USAHA_ID IS NOT NULL";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY NAMA ASC";
		return $this->selectLimit($str,$limit,$from);
    }

    function getBidangUsaha($bidangUsahaId)
	{
		$str = "SELECT AMBIL_BIDANG_USAHA_NAMA('".$bidangUsahaId."') BIDANG_USAHA FROM DUAL ";
		$this->select($str);
		if($this->firstRow())
			return $this->getField("BIDANG_USAHA");
		else
			return "";
    }

	function getCountByParamsMaxBidangId($paramsArray=array())
	{
		$str = "SELECT MAX(BIDANG_USAHA_ID) MAXID FROM BIDANG_USAHA WHERE 1=1 ";
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->select($str);
		if($this->firstRow())
			return $this->getField("MAXID");
		else
			return 0;
    }

    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(BIDANG_USAHA_ID) AS ROWCOUNT FROM BIDANG_USAHA WHERE BIDANG_USAHA_ID IS NOT NULL  AND STATUS_BIDANG_USAHA = 1 ";
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
		$str = "SELECT COUNT(BIDANG_USAHA_ID) AS ROWCOUNT FROM BIDANG_USAHA WHERE BIDANG_USAHA_ID IS NOT NULL ";
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
