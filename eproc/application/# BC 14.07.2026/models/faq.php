<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Faq extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function Faq()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("FAQ_ID", $this->getNextId("FAQ_ID","FAQ")); 

		$str = "
		INSERT INTO FAQ (
		   FAQ_ID, PERTANYAAN, JAWABAN) 
 			 	VALUES (
				  ".$this->getField("FAQ_ID").",
  				  '".$this->getField("PERTANYAAN")."',
				  '".$this->getField("JAWABAN")."'
				)"; 
		$this->query = $str;
		$this->id = $this->getField("FAQ_ID");
		//echo $str;exit;
		return $this->execQuery($str);
    }
	
	 function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE FAQ SET
				  PERTANYAAN 	= '".$this->getField("PERTANYAAN")."',
				  JAWABAN 		=  '".$this->getField("JAWABAN")."'
				WHERE FAQ_ID = ".$this->getField("FAQ_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "DELETE FROM FAQ  
				WHERE FAQ_ID = ".$this->getField("FAQ_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="ORDER BY PERTANYAAN ASC")
	{
		$str = "SELECT 
					FAQ_ID, PERTANYAAN, JAWABAN
					FROM FAQ A
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
		$str = "SELECT COUNT(FAQ_ID) AS ROWCOUNT FROM FAQ A
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