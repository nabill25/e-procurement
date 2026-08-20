<?php
 /**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 
include_once('entity.php');

class Contractingpenyedia extends Entity{

	var $query;
     
  function __construct(){
    $this->Entity();
  }

  function canInsert(){
    return true;
  } 

  function updateStatus() // 
  { 

    switch ($this->getField("CONTRACTINGSTATUSKONTRAKID")) {
      case '1':
        $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
               CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
               CR_SPPBJ_APPROVED_DATE = CURRENT_TIMESTAMP,
               UPDATED_BY   = ".$this->getField("CREATED_BY").",
               UPDATED_DATE = CURRENT_TIMESTAMP
            WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
            ";  
        break;
      case '4':
        $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
               CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
               UPDATED_BY   = ".$this->getField("CREATED_BY").",
               UPDATED_DATE = CURRENT_TIMESTAMP
            WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
            ";  
        break;
      case '5':
        $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
               CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
               CR_APPROVED_DATE = CURRENT_TIMESTAMP,
               UPDATED_BY   = ".$this->getField("CREATED_BY").",
               UPDATED_DATE = CURRENT_TIMESTAMP
            WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
            ";  
        break;
      case '6':
        $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
               CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
               CR_APPROVED_DATE = CURRENT_TIMESTAMP,
               UPDATED_BY   = ".$this->getField("CREATED_BY").",
               UPDATED_DATE = CURRENT_TIMESTAMP
            WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
            ";  

        $str3 = "UPDATE CONTRACTING_REKANAN SET
               CONTRACTINGPROSESID   = 3,
               UPDATED_BY   = '".$this->getField("CREATED_BY")."',
               UPDATED_DATE = CURRENT_TIMESTAMP
            WHERE CONTRACTINGREKANANID = ".$this->getField("CONTRACTINGREKANANID")."
            ";  

        $this->query = $str3;
        $this->execQuery($str3);
        break;
      case '7':
      case '8':
      case '9':
      case '10':
      case '11':
      case '12':
      case '13':
        $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
               CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
               UPDATED_BY   = ".$this->getField("CREATED_BY").",
               UPDATED_DATE = CURRENT_TIMESTAMP
            WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
            ";  
        break;

      case '100':
        $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
               CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
               UPDATED_BY   = ".$this->getField("CREATED_BY").",
               UPDATED_DATE = CURRENT_TIMESTAMP
            WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
            ";  

        $str3 = "UPDATE CONTRACTING_REKANAN SET
               CONTRACTINGPROSESID   = 5,
               UPDATED_BY   = '".$this->getField("CREATED_BY")."',
               UPDATED_DATE = CURRENT_TIMESTAMP
            WHERE CONTRACTINGREKANANID = ".$this->getField("CONTRACTINGREKANANID")."
            ";  

        $this->query = $str3;
        $this->execQuery($str3);
        break;

      case '200':
        $str = "UPDATE CONTRACTING_REKANAN_PROSES1 SET
               CONTRACTINGSTATUSKONTRAKID   = '".$this->getField("CONTRACTINGSTATUSKONTRAKID")."',
               UPDATED_BY   = ".$this->getField("CREATED_BY").",
               UPDATED_DATE = CURRENT_TIMESTAMP
            WHERE CONTRACTINGREKANANPROSES1ID = ".$this->getField("CONTRACTINGREKANANPROSES1ID")."
            ";  

        $str3 = "UPDATE CONTRACTING_REKANAN SET
               CONTRACTINGPROSESID   = 6,
               UPDATED_BY   = '".$this->getField("CREATED_BY")."',
               UPDATED_DATE = CURRENT_TIMESTAMP
            WHERE CONTRACTINGREKANANID = ".$this->getField("CONTRACTINGREKANANID")."
            ";  

        $this->query = $str3;
        $this->execQuery($str3);
        break;
      
      default:
        break;
    } 
    
        // echo $str; die();
        $this->query = $str;
    return $this->execQuery($str);
  }
 
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.PAKET_ID ASC"){
    $str = "SELECT
				A.*
			FROM VIEW_CONTRACTING_PAKET A 
			WHERE 1=1 ".$stat;
      foreach ($paramsArray as $key => $val) {
        // $str .= " AND $key = '$val' ";
        // ikn 20190218
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

  function selectByParamsViewContracting($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.PAKET_ID ASC"){
    $str = "SELECT
        A.*
      FROM VIEW_CONTRACTING_REKANAN A 
      WHERE 1=1 ".$stat;
      foreach ($paramsArray as $key => $val) {
        // $str .= " AND $key = '$val' ";
        // ikn 20190218
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

  function getCountByParams($paramsArray=array(), $statement=""){
      $str = "SELECT COUNT(*) AS ROWCOUNT
                from ( SELECT PAKET_ID FROM VIEW_CONTRACTING_REKANAN A WHERE 1=1 ";
      while(list($key,$val)=each($paramsArray))
      {
        // $str .= " AND $key = '$val' ";
        // ikn 20190218
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
      // echo $str; exit();
      if($this->firstRow())
        return $this->getField("ROWCOUNT");
      else
        return 0;
    }
 
}
?>
