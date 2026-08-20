<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

include_once('entity.php');

class Masterdokumentemplate extends Entity{ 

	var $query;

    function __construct(){
	  parent::__construct();
	}
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("ID", $this->getNextId("ID","MASTER_DOKUMEN_TEMPLATE_UPLOAD")); 

		$str = "
		
			INSERT INTO MASTER_DOKUMEN_TEMPLATE_UPLOAD (
			   ID, NAMA, UKURAN, TIPE, PATH_FILE, TANGGAL_UPLOAD,CREATED_BY)
 
  			 	VALUES (
				  '".$this->getField("ID")."',
  				  '".$this->getField("NAMA")."',
   				  '".$this->getField("UKURAN")."',
				  '".$this->getField("TIPE")."', 
				  '".$this->getField("PATH_FILE")."', 
				  CURRENT_TIMESTAMP,
				 '".$this->getField("CREATED_BY")."'
				)"; 
				
		$this->query = $str;
		$this->id = $this->getField("ID");
		//echo $str;exit;
		return $this->execQuery($str);
    }
 
	
	function deteleDokumen()
	{
        $str = "DELETE FROM MASTER_DOKUMEN_TEMPLATE_UPLOAD
                WHERE 
                  ID = ".$this->getField("ID").""; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }
 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY ID ASC ")
	{
		$str = " SELECT A.*, B.ID ID_UPLOAD, B.PATH_FILE, B.UKURAN, B.TIPE, B.TANGGAL_UPLOAD  
				 FROM MASTER_DOKUMEN_TEMPLATE A
				 LEFT JOIN MASTER_DOKUMEN_TEMPLATE_UPLOAD B ON A.NAMA=B.NAMA
				 WHERE 1=1
				 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
			
		$str .= $statement." ".$order;

		return $this->selectLimit($str,$limit,$from); 
    }


  } 
?>