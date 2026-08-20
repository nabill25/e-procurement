<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class Contractingsanksi extends Entity{

	var $query;
     
  function __construct(){
    $this->Entity();
  }

  function canInsert(){
    return true;
  }  

  function insertSanksi()
  { 
    $this->setField("SANKSIID", $this->getNextId("SANKSIID","CONTRACTING_SANKSI")); 

    $str = "
    INSERT INTO CONTRACTING_SANKSI 
    ( SANKSIID, HARI_TERLAMBAT, NILAI_SANKSI, NILAI_PEKERJAAN, NILAI_DENDA, CONTRACTINGREKANANID, CREATED_BY, CREATED_DATE, BUKTI_BAYAR, CARA_BAYAR, PAYMENTID_FK, INVOICE_FILE, INVOICE_FILE_TTD) 
      VALUES (
          ".$this->getField("SANKSIID").",
          '".$this->getField("HARI_TERLAMBAT")."',
          '".$this->getField("NILAI_SANKSI")."',
          ".$this->getField("NILAI_PEKERJAAN").",
          ".$this->getField("NILAI_DENDA").",
          ".$this->getField("CONTRACTINGREKANANID").",
          ".$this->getField("CREATED_BY").",
          CURRENT_TIMESTAMP,
          '".$this->getField("BUKTI_BAYAR")."',
          '".$this->getField("CARA_BAYAR")."',
          ".$this->getField("PAYMENTID_FK").",
          '".$this->getField("INVOICE_FILE")."',
          '".$this->getField("INVOICE_FILE_TTD")."'
      )"; 
    // echo $str; die();
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  }

  function editSanksi2()
  { 
    $str = "
   UPDATE  CONTRACTING_SANKSI
    SET
         HARI_TERLAMBAT        = '".$this->getField("HARI_TERLAMBAT")."',
         NILAI_SANKSI        = '".$this->getField("NILAI_SANKSI")."',
         NILAI_PEKERJAAN        = ".$this->getField("NILAI_PEKERJAAN").",
         NILAI_DENDA        = ".$this->getField("NILAI_DENDA").",
         UPDATED_BY    = ".$this->getField("CREATED_BY").",
         UPDATED_DATE  = CURRENT_TIMESTAMP,
         INVOICE_FILE        = '".$this->getField("INVOICE_FILE")."',
         INVOICE_FILE_TTD        = '".$this->getField("INVOICE_FILE_TTD")."',
         CARA_BAYAR        = '".$this->getField("CARA_BAYAR")."',
         PAYMENTID_FK        = ".$this->getField("PAYMENTID_FK")."
    WHERE  SANKSIID =  ".$this->getField("SANKSIID")."
    "; 
    // echo $str;
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  }

  function editSanksi22()
  { 
    $str = "
   UPDATE  CONTRACTING_SANKSI
    SET
         UPDATED_BY    = ".$this->getField("CREATED_BY").",
         UPDATED_DATE  = CURRENT_TIMESTAMP,
         BUKTI_BAYAR        = '".$this->getField("BUKTI_BAYAR")."'
    WHERE  SANKSIID =  ".$this->getField("SANKSIID")."
    "; 
    // echo $str;
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  }

  function insertSanksiKetentuan()
  { 
    /*Auto-generate primary key(s) by next max value (integer) */
    $this->setField("SANKSITEXTID", $this->getNextId("SANKSITEXTID","CONTRACTING_SANKSI_KETENTUAN")); 

    $str = "
    INSERT INTO CONTRACTING_SANKSI_KETENTUAN 
    ( SANKSITEXTID, CONTRACTINGREKANANID, KETERANGAN, JENIS, CREATED_BY, CREATED_DATE) 
      VALUES (
          ".$this->getField("SANKSITEXTID").",
          ".$this->getField("CONTRACTINGREKANANID").",
          '".$this->getField("DESC")."',
          '".$this->getField("JENIS")."',
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

  function updateSanksiKetentuan() // Done
    { 
      $str = "UPDATE CONTRACTING_SANKSI_KETENTUAN SET
             KETERANGAN   = '".$this->getField("DESC")."',
             UPDATED_BY = ".$this->getField("CREATED_BY").",
             UPDATED_DATE = CURRENT_TIMESTAMP
          WHERE CONTRACTINGREKANANID = ".$this->getField("CONTRACTINGREKANANID")." AND JENIS = '".$this->getField("JENIS")."'
          ";  
          // echo $str; die();
          $this->query = $str;
      return $this->execQuery($str);
    }
  
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.SANKSIID ASC"){
    $str = "SELECT
				A.*, B.PAY_TERMIN_KE
			FROM CONTRACTING_SANKSI A 
      LEFT JOIN CONTRACTING_PAYMENT B ON A.PAYMENTID_FK=B.PAYMENTID
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
      // echo $str;
    $this->query = $str;
    return $this->selectLimit($str,$limit,$from);
  } 

  function selectByParamsKetentuan($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.SANKSITEXTID ASC"){
    $str = "SELECT
        A.*
      FROM CONTRACTING_SANKSI_KETENTUAN A 
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
    DELETE FROM CONTRACTING_SANKSI WHERE CONTRACTINGREKANANID=".$this->getField("CONTRACTINGREKANANID")." "; 
    $this->query = $str;
    return $this->execQuery($strDel);
  }

  function delete()
  {
    $str = "DELETE FROM CONTRACTING_SANKSI
                WHERE
                  SANKSIID = ".$this->getField("SANKSIID")."";
                  // echo $str; die();
    $this->query = $str;
        return $this->execQuery($str);
  }
 
  }
?>
