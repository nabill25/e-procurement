<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

include_once('entity.php');

class Masterpengaturan extends Entity{

	var $query;

    function __construct(){
	  parent::__construct();
	}

  function insert()
  {
    /*Auto-generate primary key(s) by next max value (integer) */
    $this->setField("ID", $this->getNextId("ID","MASTER_PENGATURAN"));

    $str = "INSERT INTO MASTER_PENGATURAN (
            ID, URL, AKTIF, KETERANGAN, CREATED_BY, CREATED_DATE)
            VALUES (
            '".$this->getField("ID")."',
            '".$this->getField("URL")."',
            ".$this->getField("AKTIF").",
            '".$this->getField("KETERANGAN")."',
            '".$this->getField("CREATED_BY")."',
            CURRENT_TIMESTAMP
          )";
        // echo $str; die;
    $this->query = $str;
    $this->id = $this->getField("ID");
    return $this->execQuery($str);
  }

  function insertLogs()
  {
    /*Auto-generate primary key(s) by next max value (integer) */
    $this->setField("ID", $this->getNextId("ID","LOGS_KIRIM_EMAIL_DOK_EXPIRED"));

    $str = "INSERT INTO LOGS_KIRIM_EMAIL_DOK_EXPIRED (
            ID, REKANAN_ID, STAT, KIRIM_KE, TANGGAL_KIRIM, CREATED_BY, CREATED_DATE, UPDATED_BY, UPDATED_DATE)
            VALUES (
            '".$this->getField("ID")."',
            '".$this->getField("REKANAN_ID")."',
            ".$this->getField("STAT").",
            '".$this->getField("KIRIM_KE")."',
            CURRENT_TIMESTAMP,
            '".$this->getField("CREATED_BY")."',
            CURRENT_TIMESTAMP,
            '".$this->getField("CREATED_BY")."',
            CURRENT_TIMESTAMP
          )";
        // echo $str; die;
    $this->query = $str;
    $this->id = $this->getField("ID");
    return $this->execQuery($str);
  }

  function update()
  {
    $str = "UPDATE MASTER_PENGATURAN SET
            AKTIF = '".$this->getField("AKTIF")."'
            WHERE ID = ".$this->getField("ID")."
            "; 
        $this->query = $str;
    return $this->execQuery($str);
  }

  function updateLogs()
  {
    $str = "UPDATE LOGS_KIRIM_EMAIL_DOK_EXPIRED SET
            KIRIM_KE = '".$this->getField("KIRIM_KE")."',
            TANGGAL_KIRIM = CURRENT_TIMESTAMP,
            UPDATED_BY = '".$this->getField("CREATED_BY")."',
            UPDATED_DATE = CURRENT_TIMESTAMP
            WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."
            "; 
        $this->query = $str;
    return $this->execQuery($str);
  }

	function detelePengaturan()
	{
    $str = "DELETE FROM MASTER_PENGATURAN ";
		$this->query = $str;
    return $this->execQuery($str);
  }

  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY ID ASC ")
	{
		$str = " SELECT A.* FROM MASTER_PENGATURAN A WHERE 1=1";

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
    $str = "SELECT COUNT(A.BANNER_ID) AS ROWCOUNT FROM MASTER_PENGATURAN A
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

  function selectByParamsDokExpired($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY ID ASC ")
  {
    $str = " SELECT A.* FROM VIEW_REKANAN_DOKUMEN_EXPIRED A WHERE 1=1";

    while(list($key,$val) = each($paramsArray))
    {
      $str .= " AND $key = '$val' ";
    }
    $this->query = $str;

    $str .= $statement." ".$order;

    return $this->selectLimit($str,$limit,$from);
  }

  function getCountByParamsDokExpired($paramsArray=array(), $statement='')
  {
    $str = "SELECT COUNT(A.ID) AS ROWCOUNT FROM VIEW_REKANAN_DOKUMEN_EXPIRED A
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

  function selectByParamsDokExpiredEmail($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY REKANAN_ID ASC ")
  {
    $str = " SELECT A.* FROM VIEW_REKANAN_DOKUMEN_EXPIRED_EMAIL A WHERE 1=1";

    while(list($key,$val) = each($paramsArray))
    {
      $str .= " AND $key = '$val' ";
    }
    $this->query = $str;

    $str .= $statement." ".$order;

    return $this->selectLimit($str,$limit,$from);
  }

  function selectByParamsCekLog($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY REKANAN_ID ASC ")
  {
    $str = " SELECT A.* FROM LOGS_KIRIM_EMAIL_DOK_EXPIRED A WHERE 1=1";

    while(list($key,$val) = each($paramsArray))
    {
      $str .= " AND $key = '$val' ";
    }
    $this->query = $str;

    $str .= $statement." ".$order;

    return $this->selectLimit($str,$limit,$from);
  }

}
?>
