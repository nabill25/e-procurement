<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class ParametersBase extends Entity{ 
	
	var $baseQuery;
    /**
    * Class constructor.
    **/
    function ParametersBase(){
      $this->Entity(); 
    }

    function canInsert(){
      return true;
    }

    function insert(){
      if(!$this->canInsert())
        showMessageDlg("Data parameters tidak dapat di-insert",true);
      else{ 
        /*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("ID_PARAMETERS",$this->getNextId("ID_PARAMETERS","parameters")); 
        $str = "INSERT INTO parameters 
                (ID_PARAMETERS,PARAM_KEY,VALUE,MEMBER_OF,KETERANGAN) 
                VALUES(
				  ".$this->getField("ID_PARAMETERS").",
                  '".$this->getField("PARAM_KEY")."',
				  '".$this->getField("VALUE")."',
				  '".$this->getField("MEMBER_OF")."',
                  '".$this->getField("KETERANGAN")."'
                )"; 
		$this->baseQuery = $str;
		
        return $this->execQuery($str);
      }
    }

    function canUpdate(){
      return true;
    }

    function update(){
      if(!$this->canUpdate())
        showMessageDlg("Data parameters tidak dapat diupdate",true);
      else{
        $str = "UPDATE parameters 
                SET 
                  PARAM_KEY = '".$this->getField("PARAM_KEY")."',
                  VALUE = '".$this->getField("VALUE")."',
				  MEMBER_OF = '".$this->getField("MEMBER_OF")."',
				  KETERANGAN = '".$this->getField("KETERANGAN")."'
                WHERE 
                  ID_PARAMETERS = '".$this->getField("ID_PARAMETERS")."'"; 
		$this->baseQuery = $str;
        return $this->execQuery($str);
      }
    }

    function canDelete(){
      return true;
    }

    function delete(){
      if(!$this->canDelete())
        showMessageDlg("Data parameters tidak dapat di-hapus",true);
      else{
        $str = "DELETE FROM parameters 
                WHERE 
                  ID_PARAMETERS = '".$this->getField("ID_PARAMETERS")."'"; 
        return $this->execQuery($str);
      }
    }

    function selectById($ID_PARAMETERS){
      $str = "SELECT * FROM parameters
              WHERE 
                ID_PARAMETERS = '".$ID_PARAMETERS."'"; 
      return $this->select($str);
    }

    function selectByParams($paramsArray=array(),$limit=-1,$from=-1,$orderKey="ID_PARAMETERS",$orderMethod="ASC"){
      $str = "SELECT * FROM parameters WHERE ID_PARAMETERS IS NOT NULL "; 
      while(list($key,$val)=each($paramsArray)){
        $str .= " AND $key = '$val' ";
      }
      $str .= " ORDER BY";
	  $str .= " ".$orderKey;
	  $str .= " ".$orderMethod;
	  
	  $this->baseQuery = $str;
      return $this->selectLimit($str,$limit,$from); 
    }

    function getCountByParams($paramsArray=array()){
      $str = "SELECT COUNT(ID_PARAMETERS) AS ROWCOUNT FROM parameters WHERE ID_PARAMETERS IS NOT NULL "; 
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