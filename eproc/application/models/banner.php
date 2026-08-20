<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

include_once('entity.php');

class Banner extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
 //    function Banner()
	// {
 //      $this->Entity(); 
 //    }

    function __construct(){
	  parent::__construct();
	}
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("BANNER_ID", $this->getNextId("BANNER_ID","BANNER")); 

		$str = "
		
			INSERT INTO BANNER (
			   BANNER_ID, USER_LOGIN_ID, NAMA, 
			    GAMBAR, TANGGAL)
 
  			 	VALUES (
				  '".$this->getField("BANNER_ID")."',
  				  '".$this->getField("USER_LOGIN_ID")."',
   				  '".$this->getField("NAMA")."',
				  '".$this->getField("GAMBAR")."', 
				  ".$this->getField("TANGGAL")."
				)"; 
				
		$this->query = $str;
		$this->id = $this->getField("BANNER_ID");
		//echo $str;exit;
		return $this->execQuery($str);
    }


    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "		
				 UPDATE  BANNER
				SET    
					   NAMA        = '".$this->getField("NAMA")."',
					   GAMBAR      = '".$this->getField("GAMBAR")."', 
					   TANGGAL	   =  ".$this->getField("TANGGAL")."
				WHERE  BANNER_ID   =  ".$this->getField("BANNER_ID")."
			  
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM BANNER
                WHERE 
                  BANNER_ID = ".$this->getField("BANNER_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }
 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY TANGGAL DESC ")
	{
		$str = "
					SELECT 
					BANNER_ID, USER_LOGIN_ID, NAMA, 
			   		GAMBAR, LAMPIRAN, TO_CHAR(TANGGAL, 'YYYY-MM-DD') TANGGAL
					FROM BANNER
				    WHERE BANNER_ID IS NOT NULL "; 
		
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
		$str = "SELECT COUNT(A.BANNER_ID) AS ROWCOUNT 
					FROM    BANNER A
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