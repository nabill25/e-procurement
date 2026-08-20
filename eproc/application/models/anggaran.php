<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Anggaran extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
 //    function Anggaran()
	// {
 //      $this->Entity(); 
 //    }

    function __construct(){
      parent::__construct();
  }
	
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT 
                    NO_PPA, DASAR_PPA, TOTAL_PPA, 
                       NO_RELEASE, SUBSTR(KET_TAMBAH, 0, 100) KET_TAMBAH
                    FROM V_ANGGARAN 
                WHERE 1 = 1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY SUBSTR(NO_PPA, -4) DESC, SUBSTR(NO_PPA, 0, 6) ASC  ";
				
		return $this->selectLimit($str,$limit,$from); 
    }
	
  } 
?>