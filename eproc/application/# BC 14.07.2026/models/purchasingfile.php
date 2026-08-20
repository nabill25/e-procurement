<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class Purchasingfile extends Entity{

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
    $this->setField("PURCHASINGFILEID", $this->getNextId("PURCHASINGFILEID","PURCHASING_FILE")); 

    $str = "
    INSERT INTO PURCHASING_FILE (
               PURCHASINGFILEID, PAKET_ID, FILE_NAMA, FILE_NAMA_ENCRYPT, FILE_PATH, FILE_EXTENTION, FILE_SIZE, FILE_TANGGAL, FILE_JENIS, FILE_KETERANGAN, CREATED_BY, CREATED_DATE) 
        VALUES (
            ".$this->getField("PURCHASINGFILEID").",
            ".$this->getField("PAKET_ID").",
            '".$this->getField("FILE_NAMA")."',
            '".$this->getField("FILE_NAMA_ENCRYPT")."',
            '".$this->getField("FILE_PATH")."',
            '".$this->getField("FILE_EXTENTION")."',
            '".$this->getField("FILE_SIZE")."',
            ".$this->getField("FILE_TANGGAL").",
            '".$this->getField("FILE_JENIS")."', 
            '".$this->getField("FILE_KETERANGAN")."',  
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
 
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.PURCHASINGFILEID DESC"){
    $str = "SELECT
				A.*, B.NAMA NAMA_PAKET, B.URAIAN, B.LOKASI, C.USER_NAMA CREATED_BY_STR
			FROM PURCHASING_FILE A 
      LEFT JOIN PAKET B ON A.PAKET_ID=B.PAKET_ID
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

  function delete()
  {
    $str = "DELETE FROM PURCHASING_FILE WHERE
            PURCHASINGFILEID = ".$this->getField("PURCHASINGFILEID")."";
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
