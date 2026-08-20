<?php
 /**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
  include_once('entity.php');

  class Contractingmaterial extends Entity{

	var $query;
     
  function __construct(){
    $this->Entity();
  }

  function canInsert(){
    return true;
  }  

  function inserMaterial()
  { 
    /*Auto-generate primary key(s) by next max value (integer) */
    $this->setField("MATERIALID", $this->getNextId("MATERIALID","CONTRACTING_MATERIAL")); 

    $str = "
    INSERT INTO CONTRACTING_MATERIAL 
    ( MATERIALID, NAMA, HARGA_SATUAN, SATUANID, QTY, CONTRACTINGREKANANID, CREATED_BY, CREATED_DATE, SIFAT) 
      VALUES (
          ".$this->getField("MATERIALID").",
          '".$this->getField("NAMA")."',
          ".$this->getField("HARGA_SATUAN").",
          ".$this->getField("SATUANID").",
          ".$this->getField("QTY").",
          ".$this->getField("CONTRACTINGREKANANID").",
          ".$this->getField("CREATED_BY").",
          CURRENT_TIMESTAMP,
          '".$this->getField("SIFAT")."'
      )"; 
    // echo $str; die();
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  }

  function updateMaterial()
  { 
    $str = "
   UPDATE  CONTRACTING_MATERIAL
    SET
         NAMA        = '".$this->getField("NAMA")."',
         HARGA_SATUAN        = ".$this->getField("HARGA_SATUAN").",
         SATUANID        = ".$this->getField("SATUANID").",
         QTY        = ".$this->getField("QTY").",
         UPDATED_BY    = '".$this->getField("UPDATED_BY")."',
         UPDATED_DATE  = CURRENT_TIMESTAMP
    WHERE  MATERIALID =  ".$this->getField("MATERIALID")."
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
   UPDATE  CONTRACTING_MATERIAL
    SET
         STATUS        = '".$this->getField("STATUS")."',
         LINGKUP        = '".$this->getField("LINGKUP")."',
         DELIVERY_NAMA        = '".$this->getField("DELIVERY_NAMA")."',
         UPDATED_BY    = '".$this->getField("UPDATED_BY")."',
         UPDATED_DATE  = CURRENT_TIMESTAMP
    WHERE  MATERIALID =  ".$this->getField("MATERIALID")."
    "; 
    
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  }
  
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.MATERIALID ASC"){
    $str = "SELECT
				A.*, B.KODE SATUAN_STR
			FROM CONTRACTING_MATERIAL A 
      LEFT JOIN SATUAN B ON A.SATUANID=B.SATUANID
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
    DELETE FROM CONTRACTING_MATERIAL WHERE CONTRACTINGREKANANID=".$this->getField("CONTRACTINGREKANANID")." "; 
    $this->query = $str;
    return $this->execQuery($strDel);
  }

  function delete()
  {
    $str = "DELETE FROM CONTRACTING_MATERIAL
                WHERE
                  MATERIALID = ".$this->getField("MATERIALID")."";
                  // echo $str; die();
    $this->query = $str;
        return $this->execQuery($str);
  }
 
  }
?>
