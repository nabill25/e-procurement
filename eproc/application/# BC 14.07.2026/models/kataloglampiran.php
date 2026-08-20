<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class Kataloglampiran extends Entity{

	var $query;
     
    function __construct(){
      $this->Entity();
    }
 
    function canInsert(){
      return true;
    }
 
    function insert(){
      if(!$this->canInsert())
        showMessageDlg("Data Users tidak dapat di-insert",true);
      else{
	  	/*Auto-generate primary key(s) by next max value (integer) */
		    $this->setField("LAMPIRANID", $this->getNextId("LAMPIRANID","KATALOG_LAMPIRAN"));

		$str = "
				INSERT INTO KATALOG_LAMPIRAN (
				   LAMPIRANID, FILE, CREATED_DATE, CREATED_BY, KATALOGID, UKURAN, TIPE, PATH_FILE)
  			 	VALUES (
				  ".$this->getField("LAMPIRANID").",
				  '".$this->getField("FILE")."',
          NOW(),
          '".$this->getField("CREATED_BY")."',
          '".$this->getField("KATALOGID")."',
          '".$this->getField("UKURAN")."',
          '".$this->getField("TIPE")."',
          '".$this->getField("PATH_FILE")."'

				)"; 
    		// echo $str;exit;
        $this->query = $str;
        return $this->execQuery($str);
      }
    } 

    function insert2(){
      if(!$this->canInsert())
        showMessageDlg("Data Users tidak dapat di-insert",true);
      else{
      /*Auto-generate primary key(s) by next max value (integer) */
        $this->setField("LAMPIRANID", $this->getNextId("LAMPIRANID","KATALOG_LAMPIRAN"));

    $str = "
        INSERT INTO KATALOG_LAMPIRAN (
           LAMPIRANID, NAMAFOTO, FILE, KETERANGAN, CREATED_DATE, CREATED_BY, KATALOGID, UKURAN, TIPE, PATH_FILE)
          VALUES (
          ".$this->getField("LAMPIRANID").",
          '".$this->getField("NAMAFOTO")."',
          '".$this->getField("FILE")."',
          '".$this->getField("KETERANGAN")."',
          NOW(),
          '".$this->getField("CREATED_BY")."',
          '".$this->getField("KATALOGID")."',
          '".$this->getField("UKURAN")."',
          '".$this->getField("TIPE")."',
          '".$this->getField("PATH_FILE")."'

        )"; 
        // echo $str;exit;
        $this->query = $str;
        return $this->execQuery($str);
      }
    } 
 
    function canUpdate(){
      return true;
    }
 
    function update(){ 

		$str = "
				UPDATE KATALOG_LAMPIRAN
				SET
					   NAMAFOTO    	= '".$this->getField("NAMAFOTO")."',
					   FILE       	= '".$this->getField("FILE")."',
					   KETERANGAN    	= ".$this->getField("KETERANGAN").",
             UPDATED_BY         = '".$this->getField("CREATED_BY")."',
             UPDATED_DATE         = NOW(),
             UKURAN         = '".$this->getField("UKURAN")."',
             TIPE         = '".$this->getField("TIPE")."',
             PATH_FILE         = '".$this->getField("PATH_FILE")."'
				WHERE  LAMPIRANID   	= ".$this->getField("LAMPIRANID")."
 				"; 
				  // echo $str; die();
          $this->query = $str;
        return $this->execQuery($str);
    } 

    function selectById($katalogid,$userid){
      $str = "SELECT * FROM KATALOG_LAMPIRAN
              WHERE
                KATALOGID = '".$katalogid."'
                AND CREATED_BY = '".$userid."'
                ";

		$this->query = $str;

      return $this->select($str);
    }
   
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY LAMPIRANID ASC"){
      $str = "SELECT *   
				FROM KATALOG_LAMPIRAN
				WHERE 1=1 ".$stat;
        foreach ($paramsArray as $key => $value) {
          $str .= " AND $key = '$value' ";
        }
      $str .= " ".$order;
        // echo $str; die();
      $this->query = $str;
      return $this->selectLimit($str,$limit,$from);
    }

    function getCountByParams($paramsArray=array(), $varStatement=""){
      $str = "SELECT COUNT(LAMPIRANID) AS ROWCOUNT FROM KATALOG_LAMPIRAN A WHERE 1=1 ".$varStatement;
      foreach ($paramsArray as $key => $value) {
        $str .= " AND $key = '$value' ";
      }
        // echo $str; die();
      $this->select($str);
      if($this->firstRow())
        return $this->getField("ROWCOUNT");
      else
         return 0;
    }

    function delete()
    {
      if (file_exists('images/katalog/'.$this->getField("PATH_FILE"))) {
        $str = "DELETE FROM KATALOG_LAMPIRAN
                    WHERE
                      LAMPIRANID = ".$this->getField("LAMPIRANID")."";
                      // echo $str; die();
        $this->query = $str;
        unlink('images/katalog/'.$this->getField("PATH_FILE"));
          return $this->execQuery($str);
      }
    }
 
  }
?>
