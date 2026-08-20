<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

  include_once('entity.php');

  class UsersBase extends Entity{

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
    $this->setField("USER_LOGIN_ID", $this->getNextId("USER_LOGIN_ID","USER_LOGIN"));
        $this->setField("USER_PASSWORD", $this->getField("USER_PASSWORD"));

    $str = "
        INSERT INTO USER_LOGIN (
           USER_LOGIN_ID, USER_TYPE_ID, REKANAN_ID,
           USER_NAMA, USER_JABATAN, USER_ALAMAT,
           USER_TELEPON, USER_LOGIN, USER_PASSWORD, USER_STATUS,UNIT_KERJA_ID, NIP, CHILD_PL, USER_JABATAN_PANITIA, USER_JABATAN_PANITIA_STR,TENDER,PENUNJUK_PIC,LEVEL_KONTRAK,LEVEL_PEMBELI,LEVEL_PERENCANA,LEVEL_PENGGUNA,KASI_PENGGUNA,DEPARTMENT,DIREKTORAT_ID,CREATED_BY,CREATED_DATE)
          VALUES (
          ".$this->getField("USER_LOGIN_ID").",
          ".$this->getField("USER_TYPE_ID").",
          ".$this->getField("REKANAN_ID").",
          '".$this->getField("USER_NAMA")."',
          '".$this->getField("USER_JABATAN")."',
          '".$this->getField("USER_ALAMAT")."',
          '".$this->getField("USER_TELEPON")."',
          '".$this->getField("USER_LOGIN")."',
          '".$this->getField("USER_PASSWORD")."',
          ".$this->getField("USER_STATUS").",
           ".$this->getField("UNIT_KERJA_ID").",
           '".$this->getField("NIP")."',
           '".$this->getField("CHILD_PL")."',
           '".$this->getField("USER_JABATAN_PANITIA")."',
           '".$this->getField("USER_JABATAN_PANITIA_STR")."',
           ".$this->getField("TENDER").",
           '".$this->getField("PENUNJUK_PIC")."',
           '".$this->getField("LEVEL_KONTRAK")."',
           '".$this->getField("LEVEL_PEMBELI")."',
           '".$this->getField("LEVEL_PERENCANA")."', 
           '".$this->getField("LEVEL_PENGGUNA")."', 
           ".$this->getField("KASI_PENGGUNA").", 
           '".$this->getField("DEPARTMENT")."',
           ".$this->getField("DIREKTORAT_ID").",
           ".$this->getField("CREATED_BY").",
           CURRENT_TIMESTAMP
        )";
 
    $this->query = $str;
    // echo $str;exit;
        return $this->execQuery($str);
      }
    }

    function insertLoginLogs(){
      // tutup login di browser lain
      $strupdate = "
        UPDATE USER_LOGIN_LOGS
        SET
          AKTIF = '0',
          UPDATED_BY = ".$this->getField("USER_LOGIN_ID").",
          UPDATED_DATE = CURRENT_TIMESTAMP
        WHERE USER_LOGIN_ID = ".$this->getField("USER_LOGIN_ID")." AND USER_LOGIN = '".$this->getField("USER_LOGIN")."' AND AKTIF = '1'
        "; 
      $this->query = $strupdate;
      $this->execQuery($strupdate);

      $this->setField("LOGSID", $this->getNextId("LOGSID","USER_LOGIN_LOGS"));

      $str = "
      INSERT INTO USER_LOGIN_LOGS (
         LOGSID, LOGS_IP, LOGS_OS, LOGS_BROWSER, LOGS_INFOSERVER, USER_LOGIN_ID, USER_LOGIN, AKTIF,CREATED_BY,CREATED_DATE,TOKEN)
        VALUES (
        ".$this->getField("LOGSID").",
        '".$this->getField("LOGS_IP")."',
        '".$this->getField("LOGS_OS")."',
        '".$this->getField("LOGS_BROWSER")."',
        '".$this->getField("LOGS_INFOSERVER")."',
        ".$this->getField("USER_LOGIN_ID").",
        '".$this->getField("USER_LOGIN")."',
        '".$this->getField("AKTIF")."',
        ".$this->getField("USER_LOGIN_ID").", 
         CURRENT_TIMESTAMP,
        '".$this->getField("TOKEN")."'
      )"; 
      // echo $str; die();
      $this->query = $str;
      return $this->execQuery($str);
    }

    function updateLoginLogs(){ 
      $str = "
        UPDATE USER_LOGIN_LOGS
        SET
          AKTIF = '0',
          UPDATED_BY = ".$this->getField("USER_LOGIN_ID").",
          UPDATED_DATE = CURRENT_TIMESTAMP
        WHERE TOKEN = '".$this->getField("TOKEN")."' AND USER_LOGIN = '".$this->getField("USER_LOGIN")."' AND AKTIF = '1'
        "; 
        // WHERE LOG_OS = ".$this->getField("LOG_OS")." AND LOGS_BROWSER = ".$this->getField("LOGS_BROWSER")." AND USER_LOGIN_ID = ".$this->getField("USER_LOGIN_ID")." AND USER_LOGIN = '".$this->getField("USER_LOGIN")."' AND AKTIF = '1'
        // echo $str;
      $this->query = $str;
      return $this->execQuery($str);
    }

    function insertRegis(){
      if(!$this->canInsert())
        showMessageDlg("Data Users tidak dapat di-insert",true);
      else{
      /*Auto-generate primary key(s) by next max value (integer) */
        $this->setField("USER_LOGIN_ID", $this->getNextId("USER_LOGIN_ID","USER_LOGIN"));
            $this->setField("USER_PASSWORD", $this->getField("USER_PASSWORD"));

        $str = "
            INSERT INTO USER_LOGIN (
               USER_LOGIN_ID, USER_TYPE_ID, REKANAN_ID,
               USER_NAMA, USER_JABATAN, USER_ALAMAT,
               USER_TELEPON, USER_LOGIN, USER_AUTH, USER_PASSWORD, USER_STATUS,UNIT_KERJA_ID, NIP, CREATED_DATE)
              VALUES (
              ".$this->getField("USER_LOGIN_ID").",
                ".$this->getField("USER_TYPE_ID").",
              ".$this->getField("REKANAN_ID").",
                '".$this->getField("USER_NAMA")."',
                  '".$this->getField("USER_JABATAN")."',
                '".$this->getField("USER_ALAMAT")."',
              '".$this->getField("USER_TELEPON")."',
              '".$this->getField("USER_LOGIN")."',
              '".$this->getField("USER_AUTH")."',
              '".$this->getField("USER_PASSWORD")."',
                ".$this->getField("USER_STATUS").",
                       ".$this->getField("UNIT_KERJA_ID").",
                       '".$this->getField("NIP")."',
                       CURRENT_TIMESTAMP
            )";
        $this->query = $str;
        // echo $str;exit;
            return $this->execQuery($str);
      }
    }

    function canUpdate(){
      return true;
    }

    function update(){
      if(!$this->canUpdate())
        showMessageDlg("Data Users tidak dapat diupdate",true);
      else{
        $this->setField("USER_PASSWORD", $this->getField("USER_PASSWORD"));

    $str = "
        UPDATE USER_LOGIN
        SET
             USER_TYPE_ID     = ".$this->getField("USER_TYPE_ID").",
             USER_NAMA        = '".$this->getField("USER_NAMA")."',
             USER_AKTIF      = '0',
             USER_JABATAN     = '".$this->getField("USER_JABATAN")."',
             USER_ALAMAT      = '".$this->getField("USER_ALAMAT")."',
             USER_TELEPON     = '".$this->getField("USER_TELEPON")."',
             UNIT_KERJA_ID    = '".$this->getField("UNIT_KERJA_ID")."',
             NIP        = '".$this->getField("NIP")."',
             CHILD_PL         = '".$this->getField("CHILD_PL")."',   
             USER_JABATAN_PANITIA        = '".$this->getField("USER_JABATAN_PANITIA")."',
             USER_JABATAN_PANITIA_STR         = '".$this->getField("USER_JABATAN_PANITIA_STR")."',
             TENDER         = ".$this->getField("TENDER").",
             PENUNJUK_PIC         = '".$this->getField("PENUNJUK_PIC")."', 
             LEVEL_KONTRAK         = '".$this->getField("LEVEL_KONTRAK")."', 
             DEPARTMENT         = '".$this->getField("DEPARTMENT")."',
             LEVEL_PERENCANA         = '".$this->getField("LEVEL_PERENCANA")."',
             LEVEL_PEMBELI         = '".$this->getField("LEVEL_PEMBELI")."',
             LEVEL_PENGGUNA         = '".$this->getField("LEVEL_PENGGUNA")."',
             KASI_PENGGUNA         = ".$this->getField("KASI_PENGGUNA").",
             DIREKTORAT_ID         = ".$this->getField("DIREKTORAT_ID").",
             UPDATED_BY         = ".$this->getField("CREATED_BY").",
             UPDATED_DATE         = CURRENT_TIMESTAMP
        WHERE  USER_LOGIN_ID    = ".$this->getField("USER_LOGIN_ID")."
        ";
    /*$str = "
    UPDATE USER_LOGIN
                SET
                  USER_TYPE_ID = '".$this->getField("USER_TYPE_ID")."',
                  REKANAN_ID = '".$this->getField("REKANAN_ID")."',
                  USER_NAMA = '".$this->getField("USER_NAMA")."',
          USER_JABATAN = '".$this->getField("USER_JABATAN")."',
          USER_ALAMAT = '".$this->getField("USER_ALAMAT")."',
                  USER_TELEPON = '".$this->getField("USER_TELEPON")."',
          USER_LOGIN = '".$this->getField("USER_LOGIN")."',
          USER_PASSWORD = '".$this->getField("USER_PASSWORD")."'
                WHERE
                  USER_LOGIN_ID = '".$this->getField("USER_LOGIN_ID")."'"; */
          $this->query = $str;
          // echo $str;
        return $this->execQuery($str);
      }
    }

  function update_status(){
      if(!$this->canUpdate())
        showMessageDlg("Data Users tidak dapat diupdate",true);
      else{
        $this->setField("USER_PASSWORD", $this->getField("USER_PASSWORD"));

    $str = "
        UPDATE USER_LOGIN
        SET
             USER_STATUS    = ".$this->getField("USER_STATUS")."
        WHERE  USER_LOGIN_ID   = ".$this->getField("USER_LOGIN_ID")."
        ";
          $this->query = $str;

        return $this->execQuery($str);
      }
  }

  function update_status_aktif(){
      if(!$this->canUpdate())
        showMessageDlg("Data Users tidak dapat diupdate",true);
      else{
        $this->setField("USER_PASSWORD", $this->getField("USER_PASSWORD"));

    $str = "
        UPDATE USER_LOGIN
        SET
             USER_AKTIF    = ".$this->getField("USER_AKTIF").",
             UPDATED_BY    = ".$this->getField("CREATED_BY").",
             UPDATED_DATE  = CURRENT_TIMESTAMP
        WHERE  USER_LOGIN_ID   = ".$this->getField("USER_LOGIN_ID")."
        ";
          $this->query = $str;

        return $this->execQuery($str);
      }
  }

  function update_status_aktif2(){
      if(!$this->canUpdate())
        showMessageDlg("Data Users tidak dapat diupdate",true);
      else{
        $this->setField("USER_PASSWORD", $this->getField("USER_PASSWORD"));

    $str = "
        UPDATE USER_LOGIN
        SET
             USER_AKTIF    = ".$this->getField("USER_AKTIF").",
             UPDATED_BY    = ".$this->getField("CREATED_BY").",
             UPDATED_DATE  = CURRENT_TIMESTAMP,
	    ATTEMPT = 0
        WHERE  USER_LOGIN_ID   = ".$this->getField("USER_LOGIN_ID")."
        ";
          $this->query = $str;

        return $this->execQuery($str);
      }
  }

  function update_attempt(){

    $str = "
        UPDATE USER_LOGIN
        SET
             ATTEMPT    = ".$this->getField("ATTEMPT").",
             UPDATED_BY    = ".$this->getField("CREATED_BY").",
             UPDATED_DATE  = CURRENT_TIMESTAMP
        WHERE  USER_LOGIN_ID   = ".$this->getField("USER_LOGIN_ID")."
        ";
        // echo $str; die();
          $this->query = $str;

        return $this->execQuery($str);
  }

    function update_status2(){
      if(!$this->canUpdate())
        showMessageDlg("Data Users tidak dapat diupdate",true);
      else{
        $this->setField("USER_PASSWORD", $this->getField("USER_PASSWORD"));

    $str = "
        UPDATE USER_LOGIN
        SET
             USER_STATUS    = ".$this->getField("USER_STATUS")."
        WHERE  REKANAN_ID   = ".$this->getField("REKANAN_ID")."
        ";
          $this->query = $str;

        return $this->execQuery($str);
      }
    }

  function updateNoPass(){
      if(!$this->canUpdate())
        showMessageDlg("Data Users tidak dapat diupdate",true);
      else{
    $str = "
    UPDATE USER_LOGIN
                SET
                  USER_TYPE_ID = '".$this->getField("USER_TYPE_ID")."',
                  REKANAN_ID = '".$this->getField("REKANAN_ID")."',
                  USER_NAMA = '".$this->getField("USER_NAMA")."',
          USER_JABATAN = '".$this->getField("USER_JABATAN")."',
          USER_ALAMAT = '".$this->getField("USER_ALAMAT")."',
                  USER_TELEPON = '".$this->getField("USER_TELEPON")."',
          USER_LOGIN = '".$this->getField("USER_LOGIN")."'
                WHERE
                  USER_LOGIN_ID = '".$this->getField("USER_LOGIN_ID")."'";
          //echo $str;
          $this->query = $str;
        return $this->execQuery($str);
      }
    }

  function getCountByParams_onedha($paramsArray=array(),$stat="")
  {
    $str = "SELECT COUNT(USER_LOGIN_ID) AS ROWCOUNT FROM USER_LOGIN WHERE USER_LOGIN_ID IS NOT NULL ".$stat;
    while(list($key,$val)=each($paramsArray))
    {
      $str .= " AND $key = '$val' ";
    }
    //echo $str;
    $this->select($str);
    if($this->firstRow())
      return $this->getField("ROWCOUNT");
    else
      return 0;
    }

    function canDelete(){
      return true;
    }

    function delete(){
      if(!$this->canDelete())
        showMessageDlg("Data Users tidak dapat di-hapus",true);
      else{
        $str = "DELETE FROM USER_LOGIN
                WHERE
                  USER_LOGIN_ID = '".$this->getField("USER_LOGIN_ID")."'";
        return $this->execQuery($str);
      }
    }

    function updatePassword()
  {
    /*Auto-generate primary key(s) by next max value (integer) */
    $str = "UPDATE USER_LOGIN SET
          USER_PASSWORD = '".$this->getField("USER_PASSWORD")."'
        WHERE USER_LOGIN_ID = '".$this->getField("USER_LOGIN_ID")."'
        ";
    $this->query = $str;
    return $this->execQuery($str);
    }

    function updateLasLogin()
  {
    /*Auto-generate primary key(s) by next max value (integer) */
    $str = "UPDATE USER_LOGIN SET
          USER_LAST_LOGIN = NOW()
        WHERE USER_LOGIN_ID = '".$this->getField("USER_LOGIN_ID")."'
        ";
    $this->query = $str;
    return $this->execQuery($str);
    }

  function resetPassword()
  {
    /*Auto-generate primary key(s) by next max value (integer) */
    $str = "UPDATE USER_LOGIN SET
          USER_PASSWORD = '".$this->getField("USER_PASSWORD")."'
        WHERE USER_LOGIN_ID = '".$this->getField("USER_LOGIN_ID")."'
        ";
    $this->query = $str;

    return $this->execQuery($str);
    }

  function resetPasswordBaru()
  {
    $str = "
        UPDATE USER_LOGIN
        SET    USER_PASSWORD    = '".$this->getField("USER_PASSWORD")."'
        WHERE  USER_LOGIN_ID    = '".$this->getField("USER_LOGIN_ID")."'

       ";
    $this->query = $str;
    //echo $str;exit;
    return $this->execQuery($str);
    }

  function resetWhatsapp()
  {
    /*Auto-generate primary key(s) by next max value (integer) */
    $str = "UPDATE REKANAN SET
          WHATSAPP = ''
        WHERE REKANAN_ID = '".$this->getField("REKANAN_ID")."'
        ";
    $this->query = $str;
    //echo $str;
    return $this->execQuery($str);
    }

    function selectById($username){
      $str = "SELECT * FROM USER_LOGIN
              WHERE
                USER_LOGIN = '".$username."'";

    $this->query = $str;

      return $this->select($str);
    }

    function selectByEmail($email){
      $str = "SELECT USER_LOGIN_ID, EMAIL, USER_STATUS FROM USER_LOGIN A INNER JOIN REKANAN B ON A.REKANAN_ID = B.REKANAN_ID
              WHERE
                EMAIL = '".$email."' OR USER_LOGIN = '".$email."' ";

    $this->query = $str;

      return $this->select($str);
    }

  function selectByRekanan($username){
      $str = "SELECT * FROM REKANAN
              WHERE
                REKANAN_ID = '".$username."'";

    $this->query = $str;

      return $this->select($str);
    }

  function selectByUserLogin($paramsArray=array(),$limit=-1,$from=-1)
  {
    $str = "  SELECT USER_LOGIN, USER_LOGIN_ID
          FROM USER_LOGIN
                    WHERE 1 = 1 ";

    while(list($key,$val) = each($paramsArray))
    {
      $str .= " AND $key = '$val' ";
    }
    $this->query = $str;

    return $this->selectLimit($str,$limit,$from);

    }

  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order="ORDER BY A.USER_TYPE_ID ASC")
  {
    $str = "SELECT
        USER_LOGIN_ID, B.NAMA USER_TYPE, REKANAN_ID,
         USER_NAMA, USER_JABATAN, USER_ALAMAT,
         USER_TELEPON, USER_LOGIN,USER_AUTH, USER_PASSWORD,CHILD_PL, USER_JABATAN_PANITIA, USER_JABATAN_PANITIA_STR, VP_PENGADAAN, ADMIN_RUP, CATATAN_TOLAK, A.PPK, A.LEGAL,
         USER_IS_LOGIN, USER_LAST_LOGIN, cast(NOW() as timestamp)-(cast(USER_LAST_LOGIN as timestamp)) SELISIH_LOGIN,USER_STATUS, A.UNIT_KERJA_ID, C.NAMA UNIT_KERJA,
         C.ALAMAT UNIT_ALAMAT, C.TELEPON UNIT_TELEPON, A.USER_TYPE_ID, A.NIP, A.USER_AKTIF, A.TENDER, A.PENUNJUK_PIC, A.DEPARTMENT, A.DIREKTORAT_ID, D.NAMA DIREKTORAT_STR, D.KODE, A.LEVEL_PERENCANA, A.LEVEL_PEMBELI, LEVEL_KONTRAK, LEVEL_PENGGUNA, KASI_PENGGUNA
      FROM USER_LOGIN A
      INNER JOIN USER_TYPE B ON A.USER_TYPE_ID = B.USER_TYPE_ID
      LEFT JOIN UNIT_KERJA C ON A.UNIT_KERJA_ID=C.UNIT_KERJA_ID
      LEFT JOIN DIREKTORAT D ON A.DIREKTORAT_ID=D.DIREKTORAT_ID
      WHERE 1=1 ".$stat;
    while(list($key,$val)=each($paramsArray)){
      $str .= " AND $key = '$val' ";
    }
    $str .= " ".$order;
  // echo $str;
    $this->query = $str;
    return $this->selectLimit($str,$limit,$from);
  }
 
  function update_catatan(){ 
    $str = "
        UPDATE USER_LOGIN
        SET
             CATATAN_TOLAK = '".$this->getField("CATATAN_TOLAK")."',
             USER_AKTIF    = '".$this->getField("USER_AKTIF")."',
             UPDATED_BY    = ".$this->getField("UPDATED_BY").",
             UPDATED_DATE  = CURRENT_TIMESTAMP
        WHERE  USER_LOGIN_ID   = ".$this->getField("USER_LOGIN_ID")."
        ";
          $this->query = $str;

        return $this->execQuery($str);
  }

  function updateRoleUser(){ 
    $str = "
        UPDATE USER_LOGIN
        SET
             USER_TYPE_ID = ".$this->getField("USER_TYPE_ID").",
             PENUNJUK_PIC = '".$this->getField("PENUNJUK_PIC")."',
             LEVEL_KONTRAK    = '".$this->getField("LEVEL_KONTRAK")."',
             LEVEL_PERENCANA    = '".$this->getField("LEVEL_PERENCANA")."',
             LEVEL_PEMBELI    = '".$this->getField("LEVEL_PEMBELI")."',
             LEVEL_PENGGUNA    = '".$this->getField("LEVEL_PENGGUNA")."',
             KASI_PENGGUNA    = ".$this->getField("KASI_PENGGUNA").",
             UPDATED_BY    = ".$this->getField("UPDATED_BY").",
             UPDATED_DATE  = CURRENT_TIMESTAMP
        WHERE  USER_LOGIN_ID   = ".$this->getField("USER_LOGIN_ID")."
        ";
          $this->query = $str;
          // echo $str; die;
        return $this->execQuery($str); 
  }

  function selectByParamsLogs($paramsArray=array(),$limit=-1,$from=-1, $stat="", $order=""){
    $str = "SELECT * FROM USER_LOGIN_LOGS
      WHERE 1=1 ".$stat;
    while(list($key,$val)=each($paramsArray)){
      $str .= " AND $key = '$val' ";
    }
    $str .= " ".$order;
  // echo $str; die();
    $this->query = $str;
    return $this->selectLimit($str,$limit,$from);
  }

  function selectUnitKerjaPanitia($param)
  {
      $str = "SELECT NAMA AS NAMA_PEL FROM UNIT_KERJA A WHERE UNIT_KERJA_ID = '".(int)$param."' ";

      $this->select($str);
      if($this->firstRow())
      return $this->getField("NAMA_PEL");
      else
       return "";
  }

  function selectUnitKerjaKodePanitia($param)
  {
      $str = "SELECT KODE AS KD_PEL FROM UNIT_KERJA A WHERE UNIT_KERJA_ID = '".(int)$param."' ";

      $this->select($str);
      if($this->firstRow())
      return $this->getField("KD_PEL");
      else
       return "";
  }


  function selectByParamsSayembara($paramsArray=array(),$limit=-1,$from=-1, $stat=""){
      $str = "SELECT USER_LOGIN_ID, U.USER_TYPE_ID, REKANAN_ID, (SELECT X.NAMA FROM USER_TYPE X WHERE X.USER_TYPE_ID = U.USER_TYPE_ID) USER_TYPE,
            X.KODE NO_REG,
                   USER_NAMA, USER_JABATAN, USER_ALAMAT,
                   USER_TELEPON, USER_LOGIN, USER_PASSWORD,
                   USER_IS_LOGIN, USER_LAST_LOGIN, USER_STATUS
                FROM USER_LOGIN U, PESERTA_LOMBA X WHERE USER_LOGIN_ID IS NOT NULL AND  X.EMAIL = U.USER_LOGIN ".$stat;
      while(list($key,$val)=each($paramsArray)){
        $str .= " AND $key = '$val' ";
      }
      $str .= " ORDER BY USER_STATUS DESC";
    //echo $str;
      return $this->selectLimit($str,$limit,$from);
    }

    function getCountByParams($paramsArray=array(), $varStatement=""){
      $str = "SELECT COUNT(USER_NAMA) AS ROWCOUNT 
              FROM USER_LOGIN A 
              INNER JOIN USER_TYPE B ON A.USER_TYPE_ID = B.USER_TYPE_ID
              WHERE USER_NAMA IS NOT NULL ".$varStatement;
      while(list($key,$val)=each($paramsArray)){
        $str .= " AND $key = '$val' ";
      }
      // echo $str; die();
      $this->select($str);
      if($this->firstRow())
        return $this->getField("ROWCOUNT");
      else
         return 0;
    }

  function getJenis($varCID)
  {
    $this->selectByParams(array('user_id' => $varCID));
    $this->firstRow();

    return $this->getField('jenis');
  }

  function getHp($varCID)
  {
    $this->selectByParams(array('satuan_kerja_id' => $varCID));
    $this->firstRow();

    return $this->getField('hp');
  }

  function getEmail($varCID)
  {
    $this->selectByParams(array('satuan_kerja_id' => $varCID));
    $this->firstRow();

    return $this->getField('email');
  }
  }
?>
