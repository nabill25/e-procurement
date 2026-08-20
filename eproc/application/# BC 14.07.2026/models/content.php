<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Content extends Entity{ 

	var $query;
    /**
    * Class constructor.
    **/
 //    function Content()
	// {
 //      $this->Entity(); 
 //    }

    function __construct(){
  		parent::__construct();
	}
	
	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("CID", $this->getNextId("CID","content")); 
		
		$str = "INSERT INTO content(CID, parent_CID, urut, nama, keterangan, isi, link_url, status_content_menu, status_locked) 
				VALUES(
				  ".$this->getField("CID").",
				  ".$this->getField("parent_CID").",
				  '".$this->getField("urut")."',
				  '".$this->getField("nama")."',
				  '".$this->getField("keterangan")."',
				  '".$this->getField("isi")."',
				  '".$this->getField("link_url")."',
				  '".$this->getField("status_content_menu")."',
				  '".$this->getField("status_locked")."'
				)"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE content SET
				  keterangan = '".$this->getField("keterangan")."'
				WHERE CID = '".$this->getField("CID")."'
				"; 
				
		$this->query = $str;
		return $this->execQuery($str);
    }
	
	function updateContent()
	{
		$str = "UPDATE content SET
				  parent_CID = '".$this->getField("parent_CID")."',
				  urut = '".$this->getField("urut")."',
				  nama = '".$this->getField("nama")."',
				  keterangan = '".$this->getField("keterangan")."',
				  isi = '".$this->getField("isi")."',
				  link_url = '".$this->getField("link_url")."',
				  status_content_menu = '".$this->getField("status_content_menu")."',
				  status_locked = '".$this->getField("status_locked")."'
				WHERE CID = '".$this->getField("CID")."'
				"; 
				
		$this->query = $str;
		return $this->execQuery($str);
	}
	
	function updateStatusContentMenu($CID, $value)
	{
		$str = "UPDATE content SET
				  status_content_menu = '".$value."'
				WHERE CID = '".$CID."'
				"; 
				
		$this->query = $str;
		return $this->execQuery($str);
	}
	
	function updateUrut($CID, $value)
	{
		$str = "UPDATE content SET
				  urut = '".$value."'
				WHERE CID = '".$CID."'
				"; 
				
		$this->query = $str;
		return $this->execQuery($str);
	}
	
	function delete()
	{
        $str = "DELETE FROM content
                WHERE 
                  CID = '".$this->getField("CID")."'"; 
				  
		$this->query = $str;
        return $this->execQuery($str);
    }

    function selectByParams($paramsArray=array(),$limit=-1,$from=-1)
	{
		$str = "SELECT CID, parent_CID, urut, nama, keterangan, isi, link_url, status_content_menu, status_locked
				FROM content WHERE CID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = $val ";
		}
		
		$this->query = $str;
		$str .= " ORDER BY urut ASC, nama ASC";
				
		return $this->selectLimit($str,$limit,$from); 
    }
    
	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1)
	{
		$str = "SELECT CID, parent_CID, urut, nama, keterangan, isi, link_url, status_content_menu, status_locked
				FROM content WHERE CID IS NOT NULL"; 
		
		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}
		
		$this->query = $str;
		$str .= " ORDER BY urut ASC, nama ASC";
				
		return $this->selectLimit($str,$limit,$from);
    }	
   
    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(CID) AS ROWCOUNT FROM content WHERE CID IS NOT NULL "; 
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
		$str = "SELECT COUNT(CID) AS ROWCOUNT FROM content WHERE CID IS NOT NULL "; 
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
	
	function getContentTitle($varCID)
	{
		$this->selectByParams(array('CID' => $varCID));
		$this->firstRow();
		
		return $this->getField('nama');
	}
	
	function getContentText($varCID)
	{
		$this->selectByParams(array('CID' => $varCID));
		$this->firstRow();
		
		return $this->getField('keterangan');
	}
	
	function getContent($varCID)
	{
		$this->selectByParams(array('CID' => $varCID));
		$this->firstRow();
		
		return $this->getField('isi');
	}
	
  } 
?>