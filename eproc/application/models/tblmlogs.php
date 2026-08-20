<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class Tblmlogs extends Entity{

	var $query;
    function Tblmlogs(){
      $this->Entity();
    }

    function insert(){
        $this->setField("LOGSID", $this->getNextId("LOGSID","TBL_M_LOGS"));

        $str = "INSERT INTO tbl_m_logs
                (LOGSID,LOGSNAME,LOGSKET, LOGSDATE)
                VALUES(
                  '".$this->getField("LOGSID")."',
                  '".$this->getField("LOGSNAME")."',
                  '".$this->getField("LOGSKET")."',
                  '".$this->getField("DATETIME")."'
                )";
				// echo $str; die();
		    // $this->query = $str;
        return $this->execQuery($str);
    }

  }
?>
