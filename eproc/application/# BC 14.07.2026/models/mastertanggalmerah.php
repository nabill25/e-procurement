<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

include_once('entity.php');

class Mastertanggalmerah extends Entity{

	var $query;

    function __construct(){
	  parent::__construct();
	}

  function insert()
  {
    /*Auto-generate primary key(s) by next max value (integer) */
    $this->setField("TM_ID", $this->getNextId("TM_ID","TANGGAL_MERAH"));

    $str = "INSERT INTO TANGGAL_MERAH (
            TM_ID, TM_NOTE, TM_DATE,CREATED_BY, CREATED_DATE)
            VALUES (
            '".$this->getField("TM_ID")."',
            '".$this->getField("TM_NOTE")."',
            ".$this->getField("TM_DATE").",
            '".$this->getField("CREATED_BY")."',
            CURRENT_TIMESTAMP
          )";
        // echo $str; die;
    $this->query = $str;
    $this->id = $this->getField("TIM_ID");
    return $this->execQuery($str);
  }

	function deteleTanggal()
	{
    $str = "DELETE FROM TANGGAL_MERAH ";
		$this->query = $str;
    return $this->execQuery($str);
  }

  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY TM_ID ASC ")
	{
		$str = " SELECT A.* FROM TANGGAL_MERAH A WHERE 1=1";

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
