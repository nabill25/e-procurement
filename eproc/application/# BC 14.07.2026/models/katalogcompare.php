<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Katalogcompare extends Entity{ 

	var $query; 

    function __construct(){
  		parent::__construct();
	}
	
	function insert()
	{
	 	$this->setField("COMPAREID", $this->getNextId("COMPAREID","KATALOG_COMPARE"));
		$str = "
		INSERT INTO  KATALOG_COMPARE (
		   COMPAREID, KATALOGID, SESSIONID, CREATED_DATE, BROWSER) 
 			 	VALUES (
				  '".$this->getField("COMPAREID")."',
  				  '".$this->getField("KATALOGID")."',
				  '".$this->getField("SESSIONID")."',
  				  NOW(),
				  '".$this->getField("BROWSER")."'
				)"; 
				// echo $str; die();
		$this->query = $str;
		return $this->execQuery($str);
    } 

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE KATALOG_COMPARE SET
				  KATALOGID = '".$this->getField("KATALOGID")."',
				  CREATED_DATE = NOW(),
				  BROWSER = '".$this->getField("BROWSER")."'
				WHERE SESSIONID = '".$this->getField("SESSIONID")."'
				"; 
				$this->query = $str;
				
		return $this->execQuery($str);
    } 
	
	function delete()
	{ 
		
		$str = " DELETE FROM KATALOG_COMPARE WHERE
					SESSIONID = '".$this->getField("SESSIONID")."' AND 
				  	KATALOGID = '".$this->getField("KATALOGID")."'
					"; 
				  
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
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT A.SESSIONID, C.USER_NAMA, B.*
				FROM KATALOG_COMPARE A 
				INNER JOIN KATALOG B ON A.KATALOGID=B.KATALOGID
				INNER JOIN USER_LOGIN C ON B.REKANAN_ID=C.REKANAN_ID
				WHERE A.COMPAREID IS NOT NULL 
				"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ORDER BY COMPAREID ASC";
		// echo $str; die();
		$this->query = $str;
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function getCountByParams($paramsArray=array(), $varStatement=""){
      $str = "SELECT COUNT(COMPAREID) AS ROWCOUNT FROM KATALOG_COMPARE A WHERE 1=1 ".$varStatement;
      foreach ($paramsArray as $key => $value) {
        $str .= " AND $key = '$value' ";
      }
      // echo $str;
      $this->select($str);
      if($this->firstRow())
        return $this->getField("ROWCOUNT");
      else
         return 0;
    }
 
  } 
?>