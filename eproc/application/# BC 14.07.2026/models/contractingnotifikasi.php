<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Contractingnotifikasi extends Entity 
  { 

	var $query; 

  	function __construct(){
  		parent::__construct();
	}
	 
	
	function insert()
	{
		$this->setField("CONTRACTING_NOTIFIKASI_ID", $this->getNextId("CONTRACTING_NOTIFIKASI_ID","CONTRACTING_NOTIFIKASI")); 

		$str = "
		INSERT INTO CONTRACTING_NOTIFIKASI (
		   CONTRACTING_NOTIFIKASI_ID, JUDUL, PAKET_ID, TANGGAL_NOTIFIKASI_DARI, CREATED_BY, CREATED_DATE) 
 			 	VALUES (
				  ".$this->getField("CONTRACTING_NOTIFIKASI_ID").",
  				  '".$this->getField("JUDUL")."',
  				  ".$this->getField("PAKET_ID").",
  				  ".$this->getField("TANGGAL_NOTIFIKASI_DARI").",
  				  ".$this->getField("CREATED_BY").",
  				  CURRENT_TIMESTAMP
				)"; 
		$this->query = $str;
		$this->id = $this->getField("CONTRACTING_NOTIFIKASI_ID");
		//echo $str;exit;
		return $this->execQuery($str);
  }
	
	 function update()
	{
		$str = "UPDATE CONTRACTING_NOTIFIKASI SET
				  JUDUL = '".$this->getField("JUDUL")."',
				  TANGGAL_NOTIFIKASI_DARI = ".$this->getField("TANGGAL_NOTIFIKASI_DARI").",
				  UPDATED_BY = ".$this->getField("CREATED_BY").",
				  UPDATE_DATE = CURRENT_TIMESTAMP
				WHERE CONTRACTING_NOTIFIKASI_ID = ".$this->getField("CONTRACTING_NOTIFIKASI_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
		$str = "DELETE FROM CONTRACTING_NOTIFIKASI  
				WHERE CONTRACTING_NOTIFIKASI_ID = ".$this->getField("CONTRACTING_NOTIFIKASI_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
  }
	
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order="ORDER BY PAKET_ID ASC")
	{
		$str = "SELECT A.* FROM (
					SELECT 
					CONTRACTING_NOTIFIKASI_ID, JUDUL, TANGGAL_NOTIFIKASI_DARI, PAKET_ID,
					TANGGAL_NOTIFIKASI_SAMPAI, A.CREATED_BY, A.CREATED_DATE, B.USER_NAMA PEMBUAT
					FROM CONTRACTING_NOTIFIKASI A
					JOIN USER_LOGIN B ON A.CREATED_BY=B.USER_LOGIN_ID
				) A  	WHERE 1 = 1
				"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement." ".$order;
				
		return $this->selectLimit($str,$limit,$from); 
  }
	
	function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(CONTRACTING_NOTIFIKASI_ID) AS ROWCOUNT FROM CONTRACTING_NOTIFIKASI A
				WHERE 1 = 1 "; 
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