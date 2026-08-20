<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

include_once('entity.php');

class Contractingkegiatan extends Entity{ 

	var $query;

    function __construct(){
	  parent::__construct();
	}
	
	function insert()
	{
		$this->setField("CONTRACTING_KEGIATAN_ID", $this->getNextId("CONTRACTING_KEGIATAN_ID","CONTRACTING_KEGIATAN")); 

		$str = "
		
			INSERT INTO CONTRACTING_KEGIATAN (
			   CONTRACTING_KEGIATAN_ID, KEGIATAN, CREATED_BY, CREATED_DATE)
  			 	VALUES (
				  '".$this->getField("CONTRACTING_KEGIATAN_ID")."',
  				  '".$this->getField("KEGIATAN")."',
   				  ".$this->getField("CREATED_BY").",
   				  CURRENT_TIMESTAMP
				)"; 
				
		$this->query = $str;
		$this->id = $this->getField("CONTRACTING_KEGIATAN_ID");
		//echo $str;exit;
		return $this->execQuery($str);
    }


    function update()
	{
		$str = "		
				 UPDATE  CONTRACTING_KEGIATAN
				SET    
					   KEGIATAN        = '".$this->getField("KEGIATAN")."',
					   UPDATED_BY      = ".$this->getField("UPDATED_BY").", 
					   UPDATED_DATE	   =  CURRENT_TIMESTAMP
				WHERE  CONTRACTING_KEGIATAN_ID   =  ".$this->getField("CONTRACTING_KEGIATAN_ID")."
			  
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM CONTRACTING_KEGIATAN
                WHERE 
                  CONTRACTING_KEGIATAN_ID = ".$this->getField("CONTRACTING_KEGIATAN_ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }
 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY CONTRACTING_KEGIATAN_ID DESC ")
	{
		$str = "
					SELECT * FROM CONTRACTING_KEGIATAN
				    WHERE CONTRACTING_KEGIATAN_ID IS NOT NULL "; 
		
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
		$str = "SELECT COUNT(A.CONTRACTING_KEGIATAN_ID) AS ROWCOUNT 
					FROM    CONTRACTING_KEGIATAN A
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