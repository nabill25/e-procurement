<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
  
  include_once("menubase.php");

  class Menu extends MenuBase{

    function Menu(){
      $this->MenuBase(); //execute Entity constructor
    }

    function insert()
    {
      $this->setField("MENUID", $this->getNextId("MENUID","tbl_m_menu")); 

      $str = "
      INSERT INTO tbl_m_menu (
         MENUID, NAMAMENU, ISPARENT, PARENTID, LINKMENU, HAKAKSES, STATUSAKTIF, CREATED_BY, CREATED_DATE)
          VALUES (
            '".$this->getField("MENUID")."',
              '".$this->getField("NAMAMENU")."',
              '1',
              '0',
              '".$this->getField("LINKMENU")."',
              '".$this->getField("HAKAKSES")."',
              '".$this->getField("STATUSAKTIF")."',
              ".$this->getField("CREATED_BY").",
              CURRENT_TIMESTAMP
          )";
      $this->query = $str;
      $this->id = $this->getField("MENUID");
      // echo $str;exit;
      return $this->execQuery($str);
      }

     function update()
    {
      /*Auto-generate primary key(s) by next max value (integer) */
      $str = "UPDATE tbl_m_menu SET
                NAMAMENU = '".$this->getField("NAMAMENU")."',
                LINKMENU = '".$this->getField("LINKMENU")."',
                HAKAKSES = '".$this->getField("HAKAKSES")."',
                STATUSAKTIF = '".$this->getField("STATUSAKTIF")."',
                UPDATED_BY = ".$this->getField("CREATED_BY").",
                UPDATED_DATE = CURRENT_TIMESTAMP
          WHERE MENUID = '".$this->getField("MENUID")."'
          ";
          $this->query = $str;
      return $this->execQuery($str);
      }

     function delete()
    {
      /*Auto-generate primary key(s) by next max value (integer) */
      $str = "DELETE FROM tbl_m_menu 
          WHERE MENUID = '".$this->getField("MENUID")."'
          ";
          $this->query = $str;
      return $this->execQuery($str);
      }

    /************************** </STANDARD METHODS> **********************************/

    /************************** <ADDITIONAL METHODS> *********************************/
	function findByParent($id_induk){
		if(trim($id_induk)=="")
			$id_induk = "0";
		$str = "SELECT * FROM menu WHERE id_induk='$id_induk' ORDER BY urut_menu";
		return $this->select($str);
	}

	function selectUserGroup($paramsArray=array(),$limit=-1,$from=-1){
      $str = "SELECT menu.id_menu AS id_menu,
	  				 menu.id_induk AS id_induk,
					 menu.nama_menu AS nama_menu,
					 menu.caption AS caption,
					 menu.level AS level,
					 menu.urut_menu AS urut_menu,
					 menu.ket_menu AS ket_menu,
					 menu.link AS link,
					 menu.target AS target
	  		  FROM menu
			  WHERE id_menu IS NOT NULL ";
      while(list($key,$val)=each($paramsArray)){
        $str .= " AND $key = '$val' ";
      }
      $str .= " ORDER BY urut_menu";
	  $this->query = $str;
      return $this->selectLimit($str,$limit,$from);
    }

    function getCountByParams($paramsArray=array(), $statement=""){
      $str = "SELECT COUNT(*) AS ROWCOUNT
                from ( SELECT A.MENUID FROM tbl_m_menu A WHERE 1=1 ";
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

    /************************** </ADDITIONAL METHODS> *******************************/
  } //end of class Menu
?>
