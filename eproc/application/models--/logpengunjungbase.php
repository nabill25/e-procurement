<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class LogPengunjungBase extends Entity{ 

	var $query;
    function LogPengunjungBase(){
      $this->Entity(); 
    }

    function canInsert(){
      return true;
    }

    function insert(){
      if(!$this->canInsert())
        showMessageDlg("Data Log_Pengunjung tidak dapat di-insert",true);
      else{  		
				
        $str = "INSERT INTO log_pengunjung 
                (TANGGAL,IP_ADDRESS, LOGIN_UID, ACTION) 
                VALUES(
                  '".$this->getField("TANGGAL")."',
                  '".$this->getField("IP_ADDRESS")."',
				  '".$this->getField("LOGIN_UID")."',
				  '".$this->getField("ACTION")."'
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
        showMessageDlg("Data Log_Pengunjung tidak dapat diupdate",true);
      else{
        $str = "UPDATE log_pengunjung 
                SET 
                  TANGGAL = '".$this->getField("TANGGAL")."',
                  IP_ADDRESS = '".$this->getField("IP_ADDRESS")."',
				  LOGIN_UID = '".$this->getField("LOGIN_UID")."',
				  ACTION = '".$this->getField("ACTION")."'
                WHERE 
                  ID_LOG_PENGUNJUNG = '".$this->getField("ID_LOG_PENGUNJUNG")."'"; 
        return $this->execQuery($str);
      }
    }

    function canDelete(){
      return true;
    }

    function delete(){
      if(!$this->canDelete())
        showMessageDlg("Data Log_Pengunjung tidak dapat di-hapus",true);
      else{
        $str = "DELETE FROM log_pengunjung 
                WHERE 
                  ID_LOG_PENGUNJUNG = '".$this->getField("ID_LOG_PENGUNJUNG")."'"; 
        return $this->execQuery($str);
      }
    }

    function selectById($ID_LOG_PENGUNJUNG){
      $str = "SELECT * FROM log_Pengunjung
              WHERE 
                ID_LOG_PENGUNJUNG = '".$ID_LOG_PENGUNJUNG."'"; 
      return $this->select($str);
    }

    function selectByParams($paramsArray=array(),$limit=-1,$from=-1,$varStatement=""){
      $str = "SELECT * FROM log_pengunjung WHERE ID_LOG_PENGUNJUNG IS NOT NULL "; 
      while(list($key,$val)=each($paramsArray)){
        $str .= " AND $key = '$val' ";
      }
      $str .= $varStatement." ORDER BY ID_LOG_PENGUNJUNG DESC";
	  $this->query = $str;
      return $this->selectLimit($str,$limit,$from); 
    }

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1,$varStatement=""){
      $str = "SELECT * FROM log_pengunjung WHERE ID_LOG_PENGUNJUNG IS NOT NULL "; 
      while(list($key,$val)=each($paramsArray)){
        $str .= " AND $key LIKE '%$val%' ";
      }
      $str .= $varStatement." ORDER BY ID_LOG_PENGUNJUNG DESC";
	  $this->query = $str;
      return $this->selectLimit($str,$limit,$from); 
    }

    function getCountByParams($paramsArray=array(),$varStatement=""){
      $str = "SELECT COUNT(ID_LOG_PENGUNJUNG) AS ROWCOUNT FROM log_pengunjung WHERE ID_LOG_PENGUNJUNG IS NOT NULL "; 
      while(list($key,$val)=each($paramsArray)){
        $str .= " AND $key = '$val' ";
      }
	  $str .= " ".$varStatement." ";
      $this->select($str); 
	  $this->query = $str;
      if($this->firstRow()) 
        return $this->getField("ROWCOUNT"); 
      else 
         return 0; 
    }
	function getCountByParamsLike($paramsArray=array(),$varStatement=""){
      $str = "SELECT COUNT(ID_LOG_PENGUNJUNG) AS ROWCOUNT FROM log_pengunjung WHERE ID_LOG_PENGUNJUNG IS NOT NULL "; 
      while(list($key,$val)=each($paramsArray)){
        $str .= " AND $key LIKE '%$val%' ";
      }
	  $str .= " ".$varStatement." ";
      $this->select($str); 
	  $this->query = $str;
      if($this->firstRow()) 
        return $this->getField("ROWCOUNT"); 
      else 
         return 0; 
    }
  } 
?>