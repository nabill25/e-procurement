<?php
 /**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 
  include_once('entity.php');

  class Contractingdeliverable extends Entity{

	var $query;
     
  function __construct(){
    $this->Entity();
  }

  function canInsert(){
    return true;
  }  

  function inserDelivery()
  { 
    /*Auto-generate primary key(s) by next max value (integer) */
    $this->setField("DELIVERABLEID", $this->getNextId("DELIVERABLEID","CONTRACTING_DELIVERABLE")); 

    $str = "
    INSERT INTO CONTRACTING_DELIVERABLE 
    ( DELIVERABLEID, LINGKUP, DELIVERY_NAMA, STATUS, CONTRACTINGREKANANID, TANGGAL_DELIVERY_DARI, TANGGAL_DELIVERY_SAMPAI, CREATED_BY, CREATED_DATE) 
      VALUES (
          ".$this->getField("DELIVERABLEID").",
          '".$this->getField("LINGKUP")."',
          '".$this->getField("DELIVERY_NAMA")."',
          '".$this->getField("STATUS")."',
          ".$this->getField("CONTRACTINGREKANANID").",
          ".$this->getField("TANGGAL_DELIVERY_DARI").",
          ".$this->getField("TANGGAL_DELIVERY_SAMPAI").",
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

  function updateDelivery()
  { 
    $str = "
   UPDATE  CONTRACTING_DELIVERABLE
    SET
         STATUS      = '".$this->getField("STATUS")."',
         TANGGAL        = ".$this->getField("TANGGAL").",
         TANGGAL_TERIMA        = ".$this->getField("TANGGAL_TERIMA").",
         PRESENTASE        = '".$this->getField("PRESENTASE")."',
         KETERANGAN        = '".$this->getField("KETERANGAN")."',
         FILE_NAMA        = '".$this->getField("FILE_NAMA")."',
         UPDATED_BY    = '".$this->getField("UPDATED_BY")."',
         UPDATED_DATE  = CURRENT_TIMESTAMP
    WHERE  DELIVERABLEID =  ".$this->getField("DELIVERABLEID")."
    "; 
    // echo $str; die;
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  }

  function updateDelivery2()
  { 
    $str = "
   UPDATE  CONTRACTING_DELIVERABLE
    SET
         STATUS        = '".$this->getField("STATUS")."',
         LINGKUP        = '".$this->getField("LINGKUP")."',
         DELIVERY_NAMA        = '".$this->getField("DELIVERY_NAMA")."',
         UPDATED_BY    = '".$this->getField("UPDATED_BY")."',
         UPDATED_DATE  = CURRENT_TIMESTAMP
    WHERE  DELIVERABLEID =  ".$this->getField("DELIVERABLEID")."
    "; 
    
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  }

  function updateDeliveryBAPP()
  { 
    $str = "
   UPDATE  CONTRACTING_DELIVERABLE
    SET
         FILE_BAPP        = '".$this->getField("FILE_BAPP")."',
         FILE_BAPP_CREATED_DATE = CURRENT_TIMESTAMP,
         UPDATED_BY    = '".$this->getField("UPDATED_BY")."',
         UPDATED_DATE  = CURRENT_TIMESTAMP
    WHERE  DELIVERABLEID =  ".$this->getField("DELIVERABLEID")."
    "; 
    // echo $str; die;
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  }
  
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.DELIVERABLEID ASC"){
    $str = "SELECT
				A.*
			FROM CONTRACTING_DELIVERABLE A 
			WHERE 1=1 ".$stat;
      foreach ($paramsArray as $key => $val) {
        $pecah = explode("||", $key);
        if (count($pecah) > 1) {
          $str .= "AND $pecah[0] $pecah[1] $val ";
        } else {
          $str .= " AND $key = '$val' ";
        }
      }
    $str .= " ".$order;
      // echo $str; die();
    $this->query = $str;
    return $this->selectLimit($str,$limit,$from);
  } 

  public function delAll()
  {
    $strDel = "
    DELETE FROM CONTRACTING_DELIVERABLE WHERE CONTRACTINGREKANANID=".$this->getField("CONTRACTINGREKANANID")." "; 
    $this->query = $str;
    return $this->execQuery($strDel);
  }

  function delete()
  {
    $str = "DELETE FROM CONTRACTING_DELIVERABLE
                WHERE
                  DELIVERABLEID = ".$this->getField("DELIVERABLEID")."";
                  // echo $str; die();
    $this->query = $str;
        return $this->execQuery($str);
  }
 
  }
?>
