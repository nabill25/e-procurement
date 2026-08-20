<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
  
  include_once('entity.php');

  class MenuBase extends Entity{

	var $query;
    function MenuBase(){
      $this->Entity();
    }

    function canInsert(){
      return true;
    }

    function insert(){
      if(!$this->canInsert())
        showMessageDlg("Data menu tidak dapat di-insert",true);
      else{
        $str = "INSERT INTO menu
                (menuid,id_induk,nama_menu,caption,orderid,ket_menu,link,target,status_aktif)
                VALUES(
                  '".$this->getField("menuid")."',
                  '".$this->getField("id_induk")."',
                  '".$this->getField("nama_menu")."',
                  '".$this->getField("caption")."',
                  ".$this->getField("orderid").",
                  '".$this->getField("ket_menu")."',
                  '".$this->getField("link")."',
                  '".$this->getField("target")."',
				  '".$this->getField("status_aktif")."'
                )";
		$this->query = $str;
        return $this->execQuery($str);
      }
    }

    function canUpdate(){
      return true;
    }

    function update(){
      if(!$this->canUpdate())
        showMessageDlg("Data menu tidak dapat diupdate",true);
      else{
        $str = "UPDATE menu
                SET
                  id_induk = '".$this->getField("id_induk")."',
                  nama_menu = '".$this->getField("nama_menu")."',
                  caption = '".$this->getField("caption")."',
                  orderid = ".$this->getField("orderid").",
                  ket_menu = '".$this->getField("ket_menu")."',
                  link = '".$this->getField("link")."',
                  target = '".$this->getField("target")."'
                WHERE
                  menuid = '".$this->getField("menuid")."'";
		$this->query = $str;
        return $this->execQuery($str);
      }
    }

    function canDelete(){
      return true;
    }

    function delete(){
      if(!$this->canDelete())
        showMessageDlg("Data menu tidak dapat di-hapus",true);
      else{
        $str = "DELETE FROM tbl_m_menu 
                WHERE
                  menuid = '".$this->getField("menuid")."'";

		$this->query = $str;
        return $this->execQuery($str);
      }
    }

    function selectById($menuid){
      $str = "SELECT * FROM tbl_m_menu
              WHERE
                menuid = '".$menuid."'";
      return $this->select($str);
    }

    function selectByParams($paramsArray=array(),$limit=-1,$from=-1,$statement='',  $order='ORDER BY MENUID desc'){
      $str = "SELECT * FROM tbl_m_menu WHERE menuid IS NOT NULL "; 
      while(list($key,$val)=each($paramsArray)){
        $str .= " AND $key = '$val' ";
      }
      // $str .= " ORDER BY MENUID desc";
    $this->query = $str;
    $str .= $statement." ".$order;
      // echo $str; die();
      return $this->selectLimit($str,$limit,$from);
    }

    function getCountByParams($paramsArray=array()){
      $str = "SELECT COUNT(menuid) AS ROWCOUNT FROM tbl_m_menu WHERE menuid IS NOT NULL "; 
      while(list($key,$val)=each($paramsArray)){
        $str .= " AND $key = '$val' ";
      }
      $this->select($str);
      if($this->firstRow())
        return $this->getField("ROWCOUNT");
      else
         return 0;
    }
  }
?>
