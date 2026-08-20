<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

  include_once('entity.php');

  class GroupAccessBase extends Entity{ 

	var $query;

    function GroupAccess(){
      $this->Entity(); 
    }

    function canInsert(){
      return true;
    }

    function insert(){
      if(!$this->canInsert())
        showMessageDlg("Data group_access tidak dapat di-insert",true);
      else{
	  	/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("GAID", $this->getNextId("GAID","group_access")); 			
		$str = "INSERT INTO group_access
                (GAID, UGID, id_menu) 
                VALUES(
				  '".$this->getField("GAID")."',
                  '".$this->getField("UGID")."',
				  '".$this->getField("id_menu")."'
                )"; 
		$this->query = $str;
        return $this->execQuery($str);
      }
    }

    function canUpdate(){
      return true;
    }

    function update(){
      if(!$this->canUpdate())
        showMessageDlg("Data group_access tidak dapat diupdate",true);
      else{
        //$this->setField("PASSWORD", md5($this->getField("PASSWORD")."BAKWAN"));
		$str = "UPDATE group_access
                SET 
				  UGID = '".$this->getField("UGID")."',
                  id_menu = '".$this->getField("id_menu")."'
                WHERE 
                  GAID = '".$this->getField("GAID")."'"; 
				  $this->query = $str;
        return $this->execQuery($str);
      }
    }

    function canDelete(){
      return true;
    }

    function deleteByUGID($UGID){
      if(!$this->canDelete())
        showMessageDlg("Data group_access tidak dapat di-hapus",true);
      else{
        $str = "DELETE FROM group_access
                WHERE 
                  UGID = '".$UGID."'"; 
	  $this->query = $str;
        return $this->execQuery($str);
      }
    }

    function selectById($GAID){
      $str = "SELECT * FROM group_access
              WHERE 
                GAID = '".$GAID."'"; 
				
		$this->query = $str;
		
      $this->select($str);
	  if($this->firstRow()) 
        return true; 
      else 
         return false; 
    }

    function selectByParams($paramsArray=array(),$limit=-1,$from=-1){
      $str = "SELECT * FROM group_access WHERE GAID IS NOT NULL "; 
      while(list($key,$val)=each($paramsArray)){
        $str .= " AND $key = '$val' ";
      }
      $str .= " ORDER BY GAID";
	  $this->query = $str;
      return $this->selectLimit($str,$limit,$from); 
    }

    function getCountByParams($paramsArray=array(), $varStatement=""){
      $str = "SELECT COUNT(GAID) AS ROWCOUNT FROM group_access WHERE GAID IS NOT NULL ".$varStatement; 
      while(list($key,$val)=each($paramsArray)){
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