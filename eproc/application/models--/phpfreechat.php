<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class PhpFreeChat extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
    function PhpFreeChat()
	{
      $this->Entity(); 
    }
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		//$this->setField("PHPFREECHAT_ID", $this->getNextId("PHPFREECHAT_ID","PHPFREECHAT")); 

		$str = "
				INSERT INTO PHPFREECHAT (
				   PHPFREECHAT_ID, PAKET_ID, PHPFREECHAT_PARENT_ID, 
   					KODE, NAMA) 
				VALUES (
				  (SELECT PHPFREECHAT_GENERATE('".$this->getField("PHPFREECHAT_ID")."') FROM DUAL),
				  '".$this->getField("PAKET_ID")."', 
				  '".$this->getField("PHPFREECHAT_PARENT_ID")."', 
				  '".$this->getField("KODE")."', 
				  '".$this->getField("NAMA")."'
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PHPFREECHAT SET
				  NAMA = '".$this->getField("NAMA")."'
				WHERE PHPFREECHAT_ID = '".$this->getField("PHPFREECHAT_ID")."'
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }
	
	function delete()
	{
        $str = "DELETE FROM PHPFREECHAT
                WHERE 
                  PHPFREECHAT_ID = '".$this->getField("PHPFREECHAT_ID")."'"; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

	function deleteParentChild()
	{
        $str = "DELETE FROM PHPFREECHAT
                WHERE 
                  PAKET_ID = '".$this->getField("PAKET_ID")."'"; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

    /** 
    * Cari record berdasarkan array parameter dan limit tampilan 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @param int limit Jumlah maksimal record yang akan diambil 
    * @param int from Awal record yang diambil 
    * @return boolean True jika sukses, false jika tidak 
    **/ 
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $sOrder=" ORDER BY TO_NUMBER(MSGID) ASC ")
	{
		$str = "SELECT 
				SERVER, GROUPG, SUBGROUP, LEAF, LEAFVALUE, TIMESTAMPG, MSGID, NICK, CMD, PARAM, JAM, TO_CHAR(JAM, 'YYYY-MM-DD HH:MM:SS') JAM_TANGGAL
				FROM PHPFREECHAT
				WHERE 1=1 "; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement." ".$sOrder;
		$this->query = $str;
				//CAST(KODE AS INT)
				//." ORDER BY KODE ASC"
		return $this->selectLimit($str,$limit,$from); 
    }

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PHPFREECHAT_ID, NAMA
				FROM PHPFREECHAT WHERE PHPFREECHAT_ID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= $statement." ORDER BY NAMA ASC";
		return $this->selectLimit($str,$limit,$from); 
    }	
    /** 
    * Hitung jumlah record berdasarkan parameter (array). 
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","NAMA"=>"yyy") 
    * @return long Jumlah record yang sesuai kriteria 
    **/ 
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(PHPFREECHAT_ID) AS ROWCOUNT FROM PHPFREECHAT WHERE PHPFREECHAT_ID IS NOT NULL "; 
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
		$str = "SELECT COUNT(PHPFREECHAT_ID) AS ROWCOUNT FROM PHPFREECHAT WHERE PHPFREECHAT_ID IS NOT NULL "; 
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