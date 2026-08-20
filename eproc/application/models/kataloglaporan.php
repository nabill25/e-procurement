<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Kataloglaporan extends Entity{

	var $query;
     
    function __construct(){
      $this->Entity();
    }
  
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.KATALOGID ASC"){
      $str = "SELECT A.*, B.NAMAPRODUK
				FROM KATALOG_LAPORAN A
				LEFT JOIN KATALOG B ON A.KATALOGID = B.KATALOGID
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

    function getCountByParams($paramsArray=array(), $varStatement=""){
      $str = "SELECT COUNT(KATALOGID) AS ROWCOUNT FROM KATALOG_LAPORAN A WHERE 1=1 ".$varStatement;
      foreach ($paramsArray as $key => $value) {
        $str .= " AND $key = '$val' ";
      }
      $this->select($str);
      if($this->firstRow())
        return $this->getField("ROWCOUNT");
      else
         return 0;
    }

    function delete()
    {
      $str = "DELETE FROM KATALOG_LAPORAN
                  WHERE
                    LAPORANID = ".$this->getField("LAPORANID")."";
                    // echo $str; die();
      $this->query = $str;
          return $this->execQuery($str);
    }
 
  }
?>
