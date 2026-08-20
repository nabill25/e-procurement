<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class UserGroupsBase extends Entity{ 

	var $query;

    function UserGroupsBase(){
      $this->Entity(); 
    }

    function canInsert(){
      return true;
    }

    function insert(){
      if(!$this->canInsert())
        showMessageDlg("Data UserGroups tidak dapat di-insert",true);
      else{			
		$str = "INSERT INTO usergroups 
                (UGID, NAMA) 
                VALUES(
				  '".$this->getField("UGID")."',
                  '".$this->getField("NAMA")."'
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
        showMessageDlg("Data usergroups tidak dapat diupdate",true);
      else{
        //$this->setField("PASSWORD", md5($this->getField("PASSWORD")."BAKWAN"));
		$str = "UPDATE usergroups 
                SET 
				  UGID = '".$this->getField("UGID")."',
                  NAMA = '".$this->getField("NAMA")."'
                WHERE 
                  UGID = '".$this->getField("tempUGID")."'"; 
				  $this->query = $str;
        return $this->execQuery($str);
      }
    }

    function canDelete(){
      return true;
    }

    function delete(){
      if(!$this->canDelete())
        showMessageDlg("Data usergroups tidak dapat di-hapus",true);
      else{
        $str = "DELETE FROM usergroups 
                WHERE 
                  UGID = '".$this->getField("UGID")."'"; 
        return $this->execQuery($str);
      }
    }

    function selectById($UGID){
      $str = "SELECT * FROM usergroups
              WHERE 
                UGID = '".$UGID."'"; 
				
		$this->query = $str;
		
      $this->select($str);
	  if($this->firstRow()) 
        return true; 
      else 
         return false; 
    }

    function selectByParams($paramsArray=array(),$limit=-1,$from=-1){
      $str = "SELECT * FROM usergroups WHERE UGID IS NOT NULL "; 
      while(list($key,$val)=each($paramsArray)){
        $str .= " AND $key LIKE '%$val%' ";
      }
      $str .= " ORDER BY NAMA";
	  $this->query = $str;
      return $this->selectLimit($str,$limit,$from); 
    }

    function getCountByParams($paramsArray=array(), $varStatement=""){
      $str = "SELECT COUNT(UGID) AS ROWCOUNT FROM usergroups WHERE UGID IS NOT NULL ".$varStatement; 
      while(list($key,$val)=each($paramsArray)){
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