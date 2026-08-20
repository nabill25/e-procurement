<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
include_once('entity.php');

class Group extends Entity{

  function __construct(){
    parent::__construct();
  }

  function insert()
  {
    /*Auto-generate primary key(s) by next max value (integer) */
    $this->setField("USER_TYPE_ID", $this->getNextId("USER_TYPE_ID","USER_TYPE")); 

    $str = "
    INSERT INTO USER_TYPE (
       USER_TYPE_ID, NAMA, AKTIF, CREATED_BY, CREATED_DATE)
        VALUES (
          '".$this->getField("USER_TYPE_ID")."',
            '".$this->getField("NAMA")."',
            '".$this->getField("AKTIF")."',
            ".$this->getField("CREATED_BY").",
            CURRENT_TIMESTAMP
        )";
    $this->query = $str;
    $this->id = $this->getField("USER_TYPE_ID");
    // echo $str;exit;
    return $this->execQuery($str);
    }

  function update()
  {
    /*Auto-generate primary key(s) by next max value (integer) */
    $str = "UPDATE USER_TYPE SET
              NAMA = '".$this->getField("NAMA")."',
              AKTIF = '".$this->getField("AKTIF")."',
              UPDATED_BY = ".$this->getField("CREATED_BY").",
              UPDATED_DATE = CURRENT_TIMESTAMP
        WHERE USER_TYPE_ID = '".$this->getField("USER_TYPE_ID")."'
        ";
        // echo $str; die;
        $this->query = $str;
    return $this->execQuery($str);
  }

  function delete()
  {
    /*Auto-generate primary key(s) by next max value (integer) */
    $str = "DELETE FROM USER_TYPE 
        WHERE USER_TYPE_ID = '".$this->getField("USER_TYPE_ID")."'
        ";
        $this->query = $str;
    return $this->execQuery($str);
  }

  function selectByParams($paramsArray=array(),$limit=-1,$from=-1,$statement='',  $order='ORDER BY AKTIF ASC')
  {
    $str = "SELECT * FROM USER_TYPE WHERE USER_TYPE_ID IS NOT NULL "; 
    while(list($key,$val)=each($paramsArray)){
      $str .= " AND $key = '$val' ";
    }
    $this->query = $str;
    $str .= $statement." ".$order;
    // echo $str; die();
    return $this->selectLimit($str,$limit,$from);
  }

  function getCountByParams($paramsArray=array(), $statement=""){
      $str = "SELECT COUNT(*) AS ROWCOUNT
                from ( SELECT A.USER_TYPE_ID FROM USER_TYPE A WHERE 1=1 ";
      while(list($key,$val)=each($paramsArray))
      {
        $pecah = explode("||", $key);
        if (count($pecah) > 1) {
          $str .= "AND $pecah[0] $pecah[1] $val ";
        } else {
          $str .= " AND $key = '$val' ";
        }
      }
      $str .= $statement;
      $str .= ') A where 1 = 1';

      $this->select($str);
      $this->query = $str;
      if($this->firstRow())
        return $this->getField("ROWCOUNT");
      else
        return 0;
    }
	 
} //end of class Menu
?>
