<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class VOauthUser extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function VOauthUser()
	{
      $this->Entity(); 
    }
	
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT 
			NIPP, NAMA, EMAIL, 
			   HP, STATUS, KD_PEL, 
			   KD_SUB, KD_DIT, KD_SEK, 
			   NAMA_DIT, NAMA_SUB, NAMA_SEK, 
			   NAMA_PEL, KELAS, NAJAB
			FROM V_OAUTH_USER 
			WHERE 1=1	
		"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY NAMA ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
    function getCountByParams($paramsArray=array())
	{		
		$str = "SELECT COUNT(1) AS ROWCOUNT FROM V_OAUTH_USER WHERE 1=1 "; 
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