<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

  include_once('entity.php');

  class PaymentMethod extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
    function PaymentMethod()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAYMENT_METHOD_ID", $this->getNextId("PAYMENT_METHOD_ID","PAYMENT_METHOD"));

		$str = "
		INSERT INTO PAYMENT_METHOD (
		   PAYMENT_METHOD_ID, NAMA)
 			 	VALUES (
				  '".$this->getField("PAYMENT_METHOD_ID")."',
  				  '".$this->getField("NAMA")."'
				)";
		$this->query = $str;
		$this->id = $this->getField("PAYMENT_METHOD_ID");
		//echo $str;exit;
		return $this->execQuery($str);
    }

	 function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAYMENT_METHOD SET
				  		NAMA = '".$this->getField("NAMA")."'
				WHERE PAYMENT_METHOD_ID = '".$this->getField("PAYMENT_METHOD_ID")."'
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

	 function delete()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "DELETE FROM PAYMENT_METHOD
				WHERE PAYMENT_METHOD_ID = '".$this->getField("PAYMENT_METHOD_ID")."'
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY PAYMENT_METHOD_ID ASC")
	{
		$str = "SELECT
				PAYMENT_METHOD_ID, NAMA
				FROM PAYMENT_METHOD WHERE 1=1 ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}


		$str .= $statement." ".$order;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

	function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(PAYMENT_METHOD_ID) AS ROWCOUNT FROM PAYMENT_METHOD A
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
