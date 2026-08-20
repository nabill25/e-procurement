<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

  include_once('entity.php');

  class Negara extends Entity
  {

	var $query;

  function __construct(){
  		parent::__construct();
	}


  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="ORDER BY KODE ASC")
	{
		$str = "SELECT
					ID, KODE, BENUA, NAMA
					FROM NEGARA A
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
		$str = "SELECT COUNT(ID) AS ROWCOUNT FROM NEGARA A
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
