<?php
 /**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 
  include_once('entity.php');

  class Contractingpayment extends Entity{

	var $query;
     
  function __construct(){
    $this->Entity();
  }

  function canInsert(){
    return true;
  }  

  function insertPayment()
  { 
    /*Auto-generate primary key(s) by next max value (integer) */
    $this->setField("PAYMENTID", $this->getNextId("PAYMENTID","CONTRACTING_PAYMENT")); 

    $str = "
    INSERT INTO CONTRACTING_PAYMENT 
    ( PAYMENTID, PAY_TERMIN_KE, PAY_NILAI, PAY_PROGRES, PAY_KETERANGAN, CONTRACTINGREKANANID, PAY_DATE_DARI, PAY_DATE_SAMPAI, DELIVERABLEID_FK, CREATED_BY, CREATED_DATE) 
      VALUES (
          ".$this->getField("PAYMENTID").",
          '".$this->getField("PAY_TERMIN_KE")."',
          ".$this->getField("PAY_NILAI").",
          '".$this->getField("PAY_PROGRES")."',
          '".$this->getField("PAY_KETERANGAN")."',
          ".$this->getField("CONTRACTINGREKANANID").",
          ".$this->getField("PAY_DATE_DARI").",
          ".$this->getField("PAY_DATE_SAMPAI").",
          ".$this->getField("DELIVERABLEID_FK").",
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

  function insertPaymentMerger()
  { 
    $this->setField("DELIVERABLEID", $this->getNextId("DELIVERABLEID","CONTRACTING_DELIVERABLE")); 

    $str = "
    INSERT INTO CONTRACTING_DELIVERABLE 
    ( DELIVERABLEID, LINGKUP, DELIVERY_NAMA, STATUS, CONTRACTINGREKANANID, TANGGAL_DELIVERY_DARI, TANGGAL_DELIVERY_SAMPAI, CREATED_BY, CREATED_DATE) 
      VALUES (
          ".$this->getField("DELIVERABLEID").",
          '".$this->getField("LINGKUP")."',
          '".$this->getField("DELIVERY_NAMA")."',
          'Proses',
          ".$this->getField("CONTRACTINGREKANANID").",
          ".$this->getField("TANGGAL_DELIVERY_DARI").",
          ".$this->getField("TANGGAL_DELIVERY_SAMPAI").",
          ".$this->getField("CREATED_BY").",
          CURRENT_TIMESTAMP
      )"; 
    $this->query = $str;
    if ($this->execQuery($str)) { 

      $this->setField("PAYMENTID", $this->getNextId("PAYMENTID","CONTRACTING_PAYMENT")); 
      $str2 = "
      INSERT INTO CONTRACTING_PAYMENT 
      ( PAYMENTID, PAY_TERMIN_KE, PAY_NILAI, PAY_PROGRES, CONTRACTINGREKANANID, PAY_DATE_DARI, PAY_DATE_SAMPAI, DELIVERABLEID_FK, CREATED_BY, CREATED_DATE) 
        VALUES (
            ".$this->getField("PAYMENTID").",
            '".$this->getField("PAY_TERMIN_KE")."',
            ".$this->getField("PAY_NILAI").",
            '".$this->getField("PAY_PROGRES")."',
            ".$this->getField("CONTRACTINGREKANANID").",
            ".$this->getField("PAY_DATE_DARI").",
            ".$this->getField("PAY_DATE_SAMPAI").",
            ".$this->getField("DELIVERABLEID").",
            ".$this->getField("CREATED_BY").",
            CURRENT_TIMESTAMP
        )"; 
      // echo $str; die();
      $this->query = $str2;
      if ($this->execQuery($str2)) { 
        return TRUE;
      } else { 
        $strDel = "
        DELETE FROM CONTRACTING_DELIVERABLE WHERE DELIVERABLEID=".$this->getField("DELIVERABLEID")." "; 
        return $this->execQuery($strDel); 
      }

    } else {

      return FALSE;

    }

  }

  function updatePaymentMerger()
  { 
    $str = "
     UPDATE  CONTRACTING_DELIVERABLE
      SET
           LINGKUP        = '".$this->getField("LINGKUP")."', 
           DELIVERY_NAMA        = '".$this->getField("DELIVERY_NAMA")."',
           TANGGAL_DELIVERY_DARI        = ".$this->getField("TANGGAL_DELIVERY_DARI").",
           TANGGAL_DELIVERY_SAMPAI        = ".$this->getField("TANGGAL_DELIVERY_SAMPAI").",
           UPDATED_BY    = '".$this->getField("CREATED_BY")."',
           UPDATED_DATE  = CURRENT_TIMESTAMP
      WHERE  DELIVERABLEID =  ".$this->getField("DELIVERABLEID")."
      ";  

      if ($this->execQuery($str)) { 
      $str2 = "
       UPDATE  CONTRACTING_PAYMENT
        SET
             PAY_TERMIN_KE        = '".$this->getField("PAY_TERMIN_KE")."',
             PAY_NILAI        = ".$this->getField("PAY_NILAI").",
             PAY_PROGRES        = '".$this->getField("PAY_PROGRES")."',
             PAY_DATE_DARI        = ".$this->getField("PAY_DATE_DARI").",
             PAY_DATE_SAMPAI        = ".$this->getField("PAY_DATE_SAMPAI").",
             UPDATED_BY    = '".$this->getField("CREATED_BY")."',
             UPDATED_DATE  = CURRENT_TIMESTAMP
        WHERE  DELIVERABLEID_FK =  '".$this->getField("DELIVERABLEID")."'
        "; 
        if ($this->execQuery($str2)) { 
          return TRUE;
        } else { 
          return FALSE;
        }
      } else { 
        return FALSE;
      }

  }

  function updatePayment()
  { 
    $str = "
   UPDATE  CONTRACTING_PAYMENT
    SET
         PAY_NOMOR        = '".$this->getField("PAY_NOMOR")."',
         PAY_DATE        = ".$this->getField("PAY_DATE").",
         PAY_NILAI        = ".$this->getField("PAY_NILAI").",
         PAY_POTONGAN        = ".$this->getField("PAY_POTONGAN").",
         PAY_STATUS        = '".$this->getField("STATUS")."',
         PAY_LAMPIRAN        = '".$this->getField("PAY_LAMPIRAN")."',
         PAY_LAMPIRAN_BAP        = '".$this->getField("PAY_LAMPIRAN_BAP")."',
         PAY_PROGRES    = '".$this->getField("PAY_PROGRES")."',
         DELIVERABLEID_FK    = '".$this->getField("DELIVERABLEID_FK")."',
         PAY_DATE_TERIMA_HARDCOPY        = ".$this->getField("PAY_DATE_TERIMA_HARDCOPY").",
         PAY_DATE_PENYERAHAN        = ".$this->getField("PAY_DATE_PENYERAHAN").",
         UPDATED_BY    = '".$this->getField("UPDATED_BY")."',
         UPDATED_DATE  = CURRENT_TIMESTAMP
    WHERE  PAYMENTID =  ".$this->getField("PAYMENTID")."
    "; 
    // echo $str; die();
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  }

  function updatePayment2()
  {  
    $str = "
   UPDATE  CONTRACTING_PAYMENT
    SET
         PAY_TERMIN_KE        = '".$this->getField("PAY_TERMIN_KE")."',
         PAY_NILAI        = ".$this->getField("PAY_NILAI").",
         PAY_PROGRES    = '".$this->getField("PAY_PROGRES")."',
         PAY_KETERANGAN    = '".$this->getField("PAY_KETERANGAN")."',
         UPDATED_BY    = '".$this->getField("UPDATED_BY")."',
         UPDATED_DATE  = CURRENT_TIMESTAMP
    WHERE  PAYMENTID =  ".$this->getField("PAYMENTID")."
    "; 
    // echo $str; die();
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  }

  function updatePaymentProgres()
  { 
    $str = "
    UPDATE  CONTRACTING_PAYMENT
    SET PAY_PROGRES    = '".$this->getField("PAY_PROGRES")."'
    WHERE  PAYMENTID =  ".$this->getField("PAYMENTID")."
    "; 
    // echo $str; die();
    $this->query = $str;
    if ($this->execQuery($str)) { 
      return TRUE;
    } else { 
      return FALSE;
    }
  }
  
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.PAYMENTID ASC"){
    $str = "SELECT
        A.*, B.STATUS
      FROM CONTRACTING_PAYMENT A 
      JOIN CONTRACTING_DELIVERABLE B ON A.DELIVERABLEID_FK=B.DELIVERABLEID
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
    DELETE FROM CONTRACTING_PAYMENT WHERE CONTRACTINGREKANANID=".$this->getField("CONTRACTINGREKANANID")." "; 
    $this->query = $str;
    return $this->execQuery($strDel);
  }

  function delete()
  {
    $str = "DELETE FROM CONTRACTING_PAYMENT
                WHERE
                  PAYMENTID = ".$this->getField("PAYMENTID")."";
                  // echo $str; die();
    $this->query = $str;
        return $this->execQuery($str);
  }



  function deleteDeliveryPayment()
  {
    $str = "DELETE FROM CONTRACTING_DELIVERABLE
                WHERE
                  DELIVERABLEID = ".$this->getField("DELIVERABLEID")."";
    $this->execQuery($str);

    $str2 = "DELETE FROM CONTRACTING_PAYMENT
                WHERE
                  DELIVERABLEID_FK = '".$this->getField("DELIVERABLEID")."'";
    return $this->execQuery($str2);
  }
 
  }
?>
