<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Katalogsuratpernyataan extends Entity{ 

	var $query; 

    function __construct(){
  		parent::__construct();
	}
	
	function insert()
	{
	 	$this->setField("SPID", $this->getNextId("SPID","KATALOG_SURAT_PERNYATAAN"));
		$str = "
		INSERT INTO  KATALOG_SURAT_PERNYATAAN (
		   SPID, FILE_SP, PATH_SP, CREATED_BY, CREATED_DATE) 
 			 	VALUES (
				  '".$this->getField("SPID")."',
				  '".$this->getField("FILE_SP")."',
				  '".$this->getField("PATH_SP")."',
				  ".$this->getField("CREATED_BY").",
  				  NOW()
				)"; 
				// echo $str; die();
		$this->query = $str;
		return $this->execQuery($str);
    }   
  
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT A.*
				FROM KATALOG_SURAT_PERNYATAAN A  
				WHERE 1=1 
				"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement;
		// echo $str; die();
		$this->query = $str;
				
		return $this->selectLimit($str,$limit,$from); 
  } 
 
 
  } 
?>