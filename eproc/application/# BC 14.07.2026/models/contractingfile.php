<?php
 /**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 
  include_once('entity.php');

  class Contractingfile extends Entity{

	var $query;
     
  function __construct(){
    $this->Entity();
  }

  function canInsert(){
    return true;
  }
  
  function insertFile() // Done
  { 
    /*Auto-generate primary key(s) by next max value (integer) */
    $this->setField("CONTRACTINGFILEID", $this->getNextId("CONTRACTINGFILEID","CONTRACTING_FILE")); 

    $str = "
    INSERT INTO CONTRACTING_FILE (
               CONTRACTINGFILEID, CONTRACTINGREKANANID, FILE_NAMA, FILE_NAMA_ENCRYPT, FILE_PATH, FILE_EXTENTION, FILE_SIZE, FILE_TANGGAL, FILE_JENIS, FILE_KETERANGAN, FILE_PUBLISH_PENYEDIA, CONTRACTINGPROSESID, CREATED_BY, CREATED_DATE) 
        VALUES (
            ".$this->getField("CONTRACTINGFILEID").",
            ".$this->getField("CONTRACTINGREKANANID").",
            '".$this->getField("FILE_NAMA")."',
            '".$this->getField("FILE_NAMA_ENCRYPT")."',
            '".$this->getField("FILE_PATH")."',
            '".$this->getField("FILE_EXTENTION")."',
            '".$this->getField("FILE_SIZE")."',
            ".$this->getField("FILE_TANGGAL").",
            '".$this->getField("FILE_JENIS")."', 
            '".$this->getField("FILE_KETERANGAN")."', 
            '".$this->getField("FILE_PUBLISH_PENYEDIA")."', 
            ".$this->getField("CONTRACTINGPROSESID").",
            ".$this->getField("CREATED_BY").",
            CURRENT_TIMESTAMP
        )"; 
    // echo $str; die();
    $this->query = $str;
    if ($this->execQuery($str)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  function insertFileMulti() // Done
  { 
    /*Auto-generate primary key(s) by next max value (integer) */
    $this->setField("CONTRACTINGFILEID", $this->getNextId("CONTRACTINGFILEID","CONTRACTING_FILE")); 

    $str = "
    INSERT INTO CONTRACTING_FILE (
               CONTRACTINGFILEID, CONTRACTINGREKANANID, FILE_NAMA, FILE_NAMA_ENCRYPT, FILE_PATH, FILE_EXTENTION, FILE_SIZE, FILE_TANGGAL, FILE_JENIS, FILE_KETERANGAN, FILE_PUBLISH_PENYEDIA, CONTRACTINGPROSESID, CREATED_BY, CREATED_DATE, REKANAN_ID) 
        VALUES (
            ".$this->getField("CONTRACTINGFILEID").",
            ".$this->getField("CONTRACTINGREKANANID").",
            '".$this->getField("FILE_NAMA")."',
            '".$this->getField("FILE_NAMA_ENCRYPT")."',
            '".$this->getField("FILE_PATH")."',
            '".$this->getField("FILE_EXTENTION")."',
            '".$this->getField("FILE_SIZE")."',
            ".$this->getField("FILE_TANGGAL").",
            '".$this->getField("FILE_JENIS")."', 
            '".$this->getField("FILE_KETERANGAN")."', 
            '".$this->getField("FILE_PUBLISH_PENYEDIA")."', 
            ".$this->getField("CONTRACTINGPROSESID").",
            ".$this->getField("CREATED_BY").",
            CURRENT_TIMESTAMP,
            ".$this->getField("REKANAN_ID")."
        )"; 
    // echo $str; die();
    $this->query = $str;
    if ($this->execQuery($str)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  function publishFile() // Done
  {
    $str = "UPDATE CONTRACTING_FILE 
            SET FILE_PUBLISH_PENYEDIA = ".$this->getField("FILE_PUBLISH_PENYEDIA")."
            WHERE CONTRACTINGFILEID = ".$this->getField("CONTRACTINGFILEID")."
            ";
            // echo $str; die();
    $this->query = $str;
    if ($this->execQuery($str)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }
 
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.CONTRACTINGFILEID DESC"){
    $str = "SELECT
				A.*, B.CP_NAME PROSES_STR, C.USER_NAMA CREATED_BY_STR
			FROM CONTRACTING_FILE A 
      LEFT JOIN CONTRACTING_PROSES B ON A.CONTRACTINGPROSESID=B.CONTRACTINGPROSESID
      LEFT JOIN USER_LOGIN C ON A.CREATED_BY=C.USER_LOGIN_ID
			WHERE 1=1 ";
      foreach ($paramsArray as $key => $val) {
        $pecah = explode("||", $key);
        if (count($pecah) > 1) {
          $str .= "AND $pecah[0] $pecah[1] $val ";
        } else {
          $str .= " AND $key = '$val' ";
        }
      }
    $str .= $stat." ".$order;
      // echo $str; die();
    $this->query = $str;
    return $this->selectLimit($str,$limit,$from);
  } 

  function selectByParamsMulti($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.CONTRACTINGFILEID DESC"){
    $str = "SELECT
        A.*, B.CP_NAME PROSES_STR, C.USER_NAMA CREATED_BY_STR, D.NAMA
      FROM CONTRACTING_FILE A 
      LEFT JOIN CONTRACTING_PROSES B ON A.CONTRACTINGPROSESID=B.CONTRACTINGPROSESID
      LEFT JOIN USER_LOGIN C ON A.CREATED_BY=C.USER_LOGIN_ID
      LEFT JOIN REKANAN D ON A.REKANAN_ID = D.REKANAN_ID
      WHERE 1=1 ";
      foreach ($paramsArray as $key => $val) {
        $pecah = explode("||", $key);
        if (count($pecah) > 1) {
          $str .= "AND $pecah[0] $pecah[1] $val ";
        } else {
          $str .= " AND $key = '$val' ";
        }
      }
    $str .= $stat." ".$order;
      // echo $str; die();
    $this->query = $str;
    return $this->selectLimit($str,$limit,$from);
  } 

  function delete()
  {
    $str = "DELETE FROM CONTRACTING_FILE WHERE
            CONTRACTINGFILEID = ".$this->getField("CONTRACTINGFILEID")."";
            // echo $str; die();
    $this->query = $str;
    if ($this->execQuery($str)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }
 
  }
?>
