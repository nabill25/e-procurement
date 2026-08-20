<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class AuthUser extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
 //    function AuthUser()
	// {
 //      $this->Entity(); 
 //    }
	
	function __construct(){
  		parent::__construct();
	}
		
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT 
				NIPP, NAMA, EMAIL, 
				   HP, STATUS, KD_PEL, 
				   KD_SUB, KD_DIT, KD_SEK, 
				   NAMA_DIT, NAMA_SUB, NAMA_SEK, 
				   NAMA_PEL, KELAS, NAJAB, TENDER
				FROM V_OAUTH_USER A
			    WHERE 1 = 1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ORDER BY NAMA ASC  ";
		// echo $str; die();
		$this->query = $str;
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsPanitiaTender($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT A.* FROM (
					SELECT  A.SK_PANITIA_ID, A.NO_SK, C.USER_LOGIN_ID, B.NAMA, B.NIP, A.UNIT_KERJA_ID
					FROM SK_PANITIA A 
					JOIN PANITIA B ON A.SK_PANITIA_ID = B.SK_PANITIA_ID 
					JOIN USER_LOGIN C ON B.NIP = C.NIP 
					WHERE A.AKTIF='1' AND B.KETUA='1'
				) A WHERE 1=1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ORDER BY A.SK_PANITIA_ID ASC  ";
		// echo $str; die();
		$this->query = $str;
				
		return $this->selectLimit($str,$limit,$from); 
    }
	
  } 
?>