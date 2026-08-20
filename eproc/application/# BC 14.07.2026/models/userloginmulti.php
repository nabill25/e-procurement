<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

include_once('entity.php');

class Userloginmulti extends Entity{ 

	var $query; 

    function __construct(){
	  parent::__construct();
	}
	
	function insert()
	{
		$this->setField("USER_LOGIN_MULTI_ID", $this->getNextId("USER_LOGIN_MULTI_ID","USER_LOGIN_MULTI")); 

		$str = "
		
			INSERT INTO USER_LOGIN_MULTI (
			   USER_LOGIN_MULTI_ID, USER_LOGIN_ID, USER_TYPE_ID, 
			    PENUNJUK_PIC, LEVEL_KONTRAK, LEVEL_PERENCANA, LEVEL_PEMBELI, LEVEL_PENGGUNA, KASI_PENGGUNA,
			    CREATED_BY, CREATED_DATE)
 
  			 	VALUES (
				  '".$this->getField("USER_LOGIN_MULTI_ID")."',
  				  ".$this->getField("USER_LOGIN_ID").",
   				  ".$this->getField("USER_TYPE_ID").",
				  '".$this->getField("PENUNJUK_PIC")."', 
				  '".$this->getField("LEVEL_KONTRAK")."',
				  '".$this->getField("LEVEL_PERENCANA")."',
				  '".$this->getField("LEVEL_PEMBELI")."',
				  '".$this->getField("LEVEL_PENGGUNA")."',
				  ".$this->getField("KASI_PENGGUNA").",
				  ".$this->getField("CREATED_BY").",
				  CURRENT_TIMESTAMP
				)"; 
				
		$this->query = $str;
		$this->id = $this->getField("USER_LOGIN_MULTI_ID");
		//echo $str;exit;
		return $this->execQuery($str);
    }


    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "		
				 UPDATE  USER_LOGIN_MULTI
				SET    
					   USER_TYPE_ID = ".$this->getField("USER_TYPE_ID").",
					   PENUNJUK_PIC = '".$this->getField("PENUNJUK_PIC")."', 
					   LEVEL_KONTRAK = '".$this->getField("LEVEL_KONTRAK")."', 
					   LEVEL_PERENCANA = '".$this->getField("LEVEL_PERENCANA")."', 
					   LEVEL_PEMBELI = '".$this->getField("LEVEL_PEMBELI")."', 
					   LEVEL_PENGGUNA = '".$this->getField("LEVEL_PENGGUNA")."', 
					   KASI_PENGGUNA = '".$this->getField("KASI_PENGGUNA")."',
					   UPDATED_BY = '".$this->getField("UPDATED_BY")."',
					   UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE  USER_LOGIN_MULTI_ID   =  ".$this->getField("USER_LOGIN_MULTI_ID")."
			  
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }

  function insertHistory()
	{
		$this->setField("USER_LOGIN_MULTI_REKAM_ID", $this->getNextId("USER_LOGIN_MULTI_REKAM_ID","USER_LOGIN_MULTI_REKAM")); 

		$str = "
		
			INSERT INTO USER_LOGIN_MULTI_REKAM (
			   USER_LOGIN_MULTI_REKAM_ID, USER_LOGIN_ID, USER_TYPE_ID_OLD, USER_TYPE_ID_NEW, CREATED_BY, CREATED_DATE)
 
  			 	VALUES (
				  	".$this->getField("USER_LOGIN_MULTI_REKAM_ID").",
  				  ".$this->getField("USER_LOGIN_ID").",
   				  ".$this->getField("USER_TYPE_ID_OLD").",
				  	".$this->getField("USER_TYPE_ID_NEW").", 
				  	".$this->getField("CREATED_BY").",
				  CURRENT_TIMESTAMP
				)"; 
				
		$this->query = $str;
		$this->id = $this->getField("USER_LOGIN_MULTI_REKAM_ID");
		//echo $str;exit;
		return $this->execQuery($str);
    }
	
	function deleteData()
	{
        $str = "DELETE FROM USER_LOGIN_MULTI
                WHERE 
                  USER_LOGIN_MULTI_ID = ".$this->getField("USER_LOGIN_MULTI_ID")." 
                  AND USER_LOGIN_ID = ".$this->getField("USER_LOGIN_ID")."" ; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }
 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY USER_LOGIN_MULTI_ID DESC ")
	{
		$str = "SELECT A.*, B.NAMA FROM USER_LOGIN_MULTI A
				INNER JOIN USER_TYPE B ON A.USER_TYPE_ID = B.USER_TYPE_ID
			    WHERE USER_LOGIN_MULTI_ID IS NOT NULL "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
			
		$str .= $statement." ".$order;
				
		return $this->selectLimit($str,$limit,$from); 
    }

  function selectByParamsRekam($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY USER_LOGIN_MULTI_REKAM_ID DESC ")
	{
		$str = "SELECT a.*, b.user_nama, c.nama type_old, d.nama type_new, b.user_nama || ' merubah role dari ' || c.nama || ' menjadi ' || d.nama kegiatan
						FROM user_login_multi_rekam a
						JOIN user_login b on a.user_login_id=b.user_login_id
						JOIN user_type c on a.user_type_id_old=c.user_type_id
						JOIN user_type d on a.user_type_id_new=d.user_type_id "; 
		
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
		$str = "SELECT COUNT(A.USER_LOGIN_MULTI_ID) AS ROWCOUNT 
					FROM    USER_LOGIN_MULTI A
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

    function getCountByParamsRekam($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(A.USER_LOGIN_MULTI_REKAM_ID) AS ROWCOUNT  
						FROM user_login_multi_rekam a
						JOIN user_login b on a.user_login_id=b.user_login_id
						JOIN user_type c on a.user_type_id_old=c.user_type_id
						JOIN user_type d on a.user_type_id_new=d.user_type_id 
						where 1=1 ".$statement; 
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