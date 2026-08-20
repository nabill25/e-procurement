<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Berita extends Entity{ 

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
		$this->setField("BERITA_ID", $this->getNextId("BERITA_ID","BERITA")); 

		$str = "
		
			INSERT INTO BERITA (
			   BERITA_ID, USER_LOGIN_ID, NAMA, 
			   KETERANGAN, GAMBAR, LAMPIRAN, TANGGAL)
 
  			 	VALUES (
				  '".$this->getField("BERITA_ID")."',
  				  '".$this->getField("USER_LOGIN_ID")."',
   				  '".$this->getField("NAMA")."',
				  '".$this->getField("KETERANGAN")."',
				  '".$this->getField("GAMBAR")."',
  				  '".$this->getField("LAMPIRAN")."',
				  ".$this->getField("TANGGAL")."
				)"; 
				
		$this->query = $str;
		$this->id = $this->getField("BERITA_ID");
		//echo $str;exit;
		return $this->execQuery($str);
    }


    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "		
				 UPDATE  BERITA
				SET    
					   NAMA        = '".$this->getField("NAMA")."',
					   KETERANGAN  = '".$this->getField("KETERANGAN")."',
					   GAMBAR      = '".$this->getField("GAMBAR")."',
					   LAMPIRAN    = '".$this->getField("LAMPIRAN")."',
					   TANGGAL	   =  ".$this->getField("TANGGAL")."
				WHERE  BERITA_ID   =  ".$this->getField("BERITA_ID")."
			  
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM BERITA
                WHERE 
                  BERITA_ID = ".$this->getField("BERITA_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }
 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY TANGGAL DESC ")
	{
		$str = "
					SELECT 
					BERITA_ID, USER_LOGIN_ID, NAMA, 
			   		KETERANGAN, GAMBAR, LAMPIRAN, TO_CHAR(TANGGAL, 'YYYY-MM-DD') TANGGAL
					FROM BERITA
				    WHERE BERITA_ID IS NOT NULL "; 
		
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
		$str = "SELECT COUNT(A.BERITA_ID) AS ROWCOUNT 
					FROM    BERITA A
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