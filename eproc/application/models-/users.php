<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

  include_once("usersbase.php");

  class Users extends UsersBase{
    var $query;

    function __construct(){
      parent::__construct();
    }

	function validasi()
	{
			/*Auto-generate primary key(s) by next max value (integer) */
			$str = "
					UPDATE USER_LOGIN
					SET
						   USER_STATUS    = 1
					WHERE  REKANAN_ID   = (SELECT REKANAN_ID FROM REKANAN WHERE KODE =  '".$this->getField("KODE")."')
					";
			$this->execQuery($str);
			$str1 = "UPDATE REKANAN
					SET
						   STATUS_VALIDASI  = 1,
						   TANGGAL_VALIDASI = NOW(),
               USER_VALIDASI = '".$this->getField("USER_VALIDASI")."',
               NOTE_3 = '".$this->getField("NOTE3")."',
              DATE_VALIDASI_3 = '".$this->getField("DATE_VALIDASI_3")."'
					WHERE  KODE = '".$this->getField("KODE")."'";
			return $this->execQuery($str1);
	}

  function validasiTeruskan() // Done
  {
    $str1 = "UPDATE REKANAN
        SET
             STATUS_VALIDASI  = 4,
             TANGGAL_VALIDASI = NOW(),
             USER_VALIDASI = '".$this->getField("USER_VALIDASI")."',
             NOTE_1 = '".$this->getField("NOTE1")."',
             DATE_VALIDASI_1 = '".$this->getField("DATE_VALIDASI_1")."'
        WHERE  KODE = '".$this->getField("KODE")."'";

    return $this->execQuery($str1);
  }

  function validasiRekomendasi() // Done
  {
    $str1 = "UPDATE REKANAN
        SET
             STATUS_VALIDASI  = ".$this->getField("STATUS_VALIDASI").",
             TANGGAL_VALIDASI = NOW(),
             USER_VALIDASI = '".$this->getField("USER_VALIDASI")."',
             NOTE_2 = '".$this->getField("NOTE2")."',
             DATE_VALIDASI_2 = '".$this->getField("DATE_VALIDASI_2")."'
        WHERE  REKANAN_ID = '".$this->getField("REKANAN_ID")."'";
        // echo $str1; die();
    return $this->execQuery($str1);
  }

  function validasiValidator()
  {
    $str1 = "UPDATE REKANAN
        SET
             STATUS_VALIDASI  = ".$this->getField("STATUS_VALIDASI").",
             TANGGAL_VALIDASI = NOW(),
             USER_VALIDASI = '".$this->getField("USER_VALIDASI")."',
             NOTE_3 = '".$this->getField("NOTE3")."',
             DATE_VALIDASI_3 = '".$this->getField("DATE_VALIDASI_3")."'
        WHERE  REKANAN_ID = '".$this->getField("REKANAN_ID")."'";
        // echo $str1; die();
    return $this->execQuery($str1);
  }

    /************************** </STANDARD METHODS> **********************************/

    /************************** <ADDITIONAL METHODS> *********************************/
	function selectByIdPassword($id_usr,$passwd){
  $str = "SELECT
			USER_LOGIN_ID, A.USER_TYPE_ID, REKANAN_ID,
			   USER_NAMA, USER_JABATAN, USER_ALAMAT,
			   USER_TELEPON, USER_LOGIN, USER_PASSWORD, CHILD_PL,
			   USER_IS_LOGIN, USER_LAST_LOGIN, USER_STATUS,
			   A.UNIT_KERJA_ID, COALESCE(NIP, SUBSTR(USER_NAMA, 0, 49)) NIP, B.NAMA UNIT_KERJA,
			   C.NAMA USER_TYPE
			FROM USER_LOGIN A
			LEFT JOIN UNIT_KERJA B ON A.UNIT_KERJA_ID = B.UNIT_KERJA_ID
			LEFT JOIN USER_TYPE C ON A.USER_TYPE_ID = C.USER_TYPE_ID
			WHERE USER_LOGIN ='".$id_usr."' AND USER_PASSWORD ='".$passwd."'
			AND NOT EXISTS
				(SELECT 1
						FROM BLACKLIST X
						WHERE X.REKANAN_ID = A.REKANAN_ID
				 AND CURRENT_DATE BETWEEN TANGGAL_MULAI AND TANGGAL_SELESAI ) ";
    return $this->select($str);
  }

  // ikn 20201105
  function selectByIdUsername($id_usr){

  $str = "SELECT
      USER_LOGIN_ID, A.USER_TYPE_ID, A.REKANAN_ID,
         USER_NAMA, USER_JABATAN, USER_ALAMAT,
         USER_TELEPON, USER_LOGIN, USER_PASSWORD, CHILD_PL,
         USER_IS_LOGIN, USER_LAST_LOGIN, USER_STATUS,
         A.UNIT_KERJA_ID, COALESCE(NIP, SUBSTR(USER_NAMA, 0, 49)) NIP, B.NAMA UNIT_KERJA, A.USER_JABATAN_PANITIA, A.USER_JABATAN_PANITiA_STR,
         C.NAMA USER_TYPE, VP_PENGADAAN, ADMIN_RUP, TENDER, PENUNJUK_PIC, LEGAL, VALIDATOR_UNIT, APPROVAL_UNIT, A.DEPARTMENT, A.LEVEL_PERENCANA, A.LEVEL_PEMBELI, A.LEVEL_KONTRAK, D.EMAIL, A.LEVEL_PENGGUNA, A.KASI_PENGGUNA
      FROM USER_LOGIN A
      LEFT JOIN UNIT_KERJA B ON A.UNIT_KERJA_ID = B.UNIT_KERJA_ID
      LEFT JOIN USER_TYPE C ON A.USER_TYPE_ID = C.USER_TYPE_ID
      LEFT JOIN REKANAN D ON A.REKANAN_ID = D.REKANAN_ID
      WHERE A.USER_AKTIF = '1' AND (USER_LOGIN ='".$id_usr."' OR D.EMAIL = '".$id_usr."' )";
    return $this->select($str);
  }

  function selectById($userloginid){

  $str = "SELECT
      USER_LOGIN_ID, A.USER_TYPE_ID, A.REKANAN_ID,
         USER_NAMA, USER_JABATAN, USER_ALAMAT,
         USER_TELEPON, USER_LOGIN, USER_PASSWORD, CHILD_PL,
         USER_IS_LOGIN, USER_LAST_LOGIN, USER_STATUS,
         A.UNIT_KERJA_ID, COALESCE(NIP, SUBSTR(USER_NAMA, 0, 49)) NIP, B.NAMA UNIT_KERJA, A.USER_JABATAN_PANITIA, A.USER_JABATAN_PANITiA_STR,
         C.NAMA USER_TYPE, VP_PENGADAAN, ADMIN_RUP, TENDER, PENUNJUK_PIC, LEGAL, VALIDATOR_UNIT, APPROVAL_UNIT, A.DEPARTMENT, A.LEVEL_PERENCANA, A.LEVEL_PEMBELI, A.LEVEL_KONTRAK, D.EMAIL, A.LEVEL_PENGGUNA, A.KASI_PENGGUNA
      FROM USER_LOGIN A
      LEFT JOIN UNIT_KERJA B ON A.UNIT_KERJA_ID = B.UNIT_KERJA_ID
      LEFT JOIN USER_TYPE C ON A.USER_TYPE_ID = C.USER_TYPE_ID
      LEFT JOIN REKANAN D ON A.REKANAN_ID = D.REKANAN_ID
      WHERE A.USER_AKTIF = '1' AND (USER_LOGIN_ID ='".$userloginid."' )";
    return $this->select($str);
  }

  // ikn 20201105
  function selectBlacklistByRekananId($rekananid,$limit=-1,$from=-1){

    $str = "SELECT X.BLACKLIST_ID
              FROM BLACKLIST X
              WHERE CURRENT_DATE BETWEEN TANGGAL_MULAI AND TANGGAL_SELESAI
      AND REKANAN_ID ='".$rekananid."' ";
      // echo $str;
    $this->query = $str;
    return $this->selectLimit($str,$limit,$from);
  }

	function updateUserPass(){
      if(!$this->canUpdate())
        showMessageDlg("Data Users tidak dapat diupdate",true);
      else{
		$this->setField("USER_PASSWORD", md5($this->getField("USER_PASSWORD")));
		$str = "UPDATE USER_LOGIN
                SET
                  USER_PASSWORD = '".$this->getField("USER_PASSWORD")."'
                WHERE
                  USER_LOGIN_ID = '".$this->getField("USER_LOGIN_ID")."'";
			   $this->query = $str;
        return $this->execQuery($str);
      }
    }

	function selectUserGroup($paramsArray=array(),$limit=-1,$from=-1,$varStatement=""){
      $str = "SELECT u.username AS username,
	  				 u.NAMA AS NAMA,
					 u.EMAIL AS EMAIL,
					 ug.NAMA as USERGROUP
	  		  FROM USER_LOGIN u, usergroups ug
			  WHERE username IS NOT NULL
					AND ug.UGID = u.LEVEL ";
      while(list($key,$val)=each($paramsArray)){
        $str .= " AND $key = '$val' ";
      }
      $str .= $varStatement." ORDER BY u.username";
	  $this->query = $str;
      return $this->selectLimit($str,$limit,$from);
    }

	function searchUserGroup($paramsArray=array(),$limit=-1,$from=-1,$varStatement=""){
      $str = "SELECT u.username AS username,
	  				 u.NAMA AS NAMA,
					 u.EMAIL AS EMAIL,
					 ug.NAMA as USERGROUP
	  		  FROM USER_LOGIN u, usergroups ug
			  WHERE username IS NOT NULL
					AND ug.UGID = u.LEVEL ";
      while(list($key,$val)=each($paramsArray)){
        $str .= " AND $key LIKE '%$val%' ";
      }
      $str .= $varStatement." ORDER BY u.username";
	  $this->query = $str;
      return $this->selectLimit($str,$limit,$from);
    }

	function getSearchCountByParams($paramsArray=array(),$varStatement=""){
      $str = "SELECT COUNT(username) AS ROWCOUNT FROM USER_LOGIN WHERE username IS NOT NULL ".$varStatement;
      while(list($key,$val)=each($paramsArray)){
        $str .= " AND $key LIKE '%$val%' ";
      }
      $this->select($str);
      if($this->firstRow())
        return $this->getField("ROWCOUNT");
      else
         return 0;
    }

	function getUserLoginByKode($kode){
      $str = "SELECT USER_LOGIN AS ROWCOUNT FROM USER_LOGIN WHERE
	  		  REKANAN_ID   = (SELECT REKANAN_ID FROM REKANAN WHERE KODE =  '".$kode."') ";
      $this->select($str);
      if($this->firstRow())
        return $this->getField("ROWCOUNT");
      else
         return "";
    }

  function selectAksesMenuByType($url,$typeid){
    // $str = "SELECT COUNT(MENUID) AS ROWCOUNT from TBL_M_MENU where LINKMENU = '".$url."' AND HAKAKSES LIKE '%".$typeid."%' AND STATUSAKTIF='Y'";
    $str = "SELECT COUNT(A.hakakses) AS ROWCOUNT FROM
            (
              SELECT unnest(string_to_array(hakakses, ',')) AS hakakses
              FROM
                TBL_M_MENU
              WHERE
                LINKMENU = '".$url."'
                AND STATUSAKTIF = 'Y'
            ) A
            where A.hakakses = '".$typeid."'";
    // echo $str;
    $this->select($str);
    if($this->firstRow())
      return $this->getField("ROWCOUNT");
    else
       return "";
  }

    /************************** </ADDITIONAL METHODS> *******************************/
  } //end of class Users
?>
