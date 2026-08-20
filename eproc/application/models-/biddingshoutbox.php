<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class BiddingShoutbox extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
 //    function BiddingShoutbox()
	// {
 //      $this->Entity(); 
 //    }

    function __construct(){
  		parent::__construct();
	}
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		//$this->setField("JAM", $this->getNextId("JAM","PHPSHOUTBOX")); 

		$str = "
				INSERT INTO BIDDINGSHOUTBOX (
				   JAM, NAMA, PESAN, 
   					IP_ADDRESS, REKANAN_ID, PAKET_ID, KODE, WAKTU) 
				VALUES (
				  '".$this->getField("JAM")."',
				  '".$this->getField("NAMA")."', 
				  '".$this->getField("PESAN")."', 
				  '".$this->getField("IP_ADDRESS")."', 
				  ".$this->getField("REKANAN_ID").", 
				  '".$this->getField("PAKET_ID")."', 
				  '".$this->getField("KODE")."',
				  NOW()
				)"; 
		$this->query = $str;
		
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE BIDDINGSHOUTBOX SET
				  NAMA = '".$this->getField("NAMA")."'
				WHERE JAM = '".$this->getField("JAM")."'
				"; 
				$this->query = $str;
				
		return $this->execQuery($str);
    }

    function updateBaca($tiketId, $pegawaiId)
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE BIDDINGSHOUTBOX SET
				  STATUS_BACA = 1
				WHERE TIKET_ID = '".$tiketId."' AND 
					  NOT REKANAN_ID = '".$pegawaiId."'
				"; 
				$this->query = $str;
				
		return $this->execQuery($str);
    }
	
		
	function delete()
	{
        $str = "DELETE FROM BIDDINGSHOUTBOX
                WHERE 
                  JAM = '".$this->getField("JAM")."'"; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

	function deleteParentChild()
	{
        $str = "DELETE FROM BIDDINGSHOUTBOX
                WHERE 
                  NAMA = '".$this->getField("NAMA")."'"; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }
 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=' ORDER BY JAM ASC ')
	{
		$str = "SELECT JAM, NAMA, PESAN, 
   					IP_ADDRESS, PAKET_ID, KODE, TO_CHAR(WAKTU, 'DD/MM/YYYY HH24:MI:SS') WAKTU
				FROM BIDDINGSHOUTBOX A WHERE 1=1 "; 
		//JAM IS NOT NULL
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$order;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from); 
	}

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT JAM, NAMA
				FROM BIDDINGSHOUTBOX WHERE JAM IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY NAMA ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
	 
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(JAM) AS ROWCOUNT FROM BIDDINGSHOUTBOX WHERE JAM IS NOT NULL "; 
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

    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(JAM) AS ROWCOUNT FROM BIDDINGSHOUTBOX WHERE JAM IS NOT NULL "; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->select($str); 
		if($this->firstRow()) 
			return $this->getField("ROWCOUNT"); 
		else 
			return 0; 
    }	
  } 
?>