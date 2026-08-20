<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 
class Db_log {
 
    function __construct() {
        $this->CI = & get_instance();
    }

    // ikn 20191130
    function logQueries() {

      if (php_sapi_name() === 'cli') {
        return;
      }
      $filepath = 'logs/hooks/Query-log_' . date('Y-m-d') . '.txt'; 
      $handle = fopen($filepath, "a+");

      $times = $this->CI->db->query_times;
      $query2 = '';
      foreach ($this->CI->db->queries as $key => $hasilquery) { 
        $query = explode(" ", $hasilquery);
        // $query2 .= $query[0]. ', ';
        $kamus = array("select","SELECT");
        if (in_array($query[0], $kamus)) {
          $query2 .= $query[0]. ', ';
        } else {
          $query2 .= $hasilquery;
        }
      }

      $query2 = $query2;
      $text   = "TIME:".date('h:i:s')." ### DATE:".date('Y-m-d')." ### USERID:". $this->GetUserId()." ### IP:". $this->getIP()." ### URL:". $this->GetURL()." ### Controller\Method:". $this->GetClass()." ### EXECUTION-TIME:". $times[$key]." ### STATEMENT:". $query2."

        -----------------------------------------------------------------------------------------";
        // $logtext = sprintf("%s\r\n%s\r\n%s\r\n%s\r\n%s\r\n%s\r\n%s", "'".$text."'");
        // $logtext = sprintf("%s", "'".$text."'");
      $arr = array(' ', '<br>');
        $logtext = str_replace($arr, "", $text);
        fwrite($handle, $logtext . "\r\n");              
      fclose($handle);
      if (!is_dir($filepath))
      {
        // mkdir($dir, 0777, true);
        // chmod($filepath, 0777, true);  //changed to add the zero
      }
    }

    // function logQueries() {
    //     $filepath = 'logs/hooks/Query-log-' . date('Y-m-d') . '.log'; 
    //     $handle = fopen($filepath, "a+");
 
    //     $times = $this->CI->db->query_times;
    //     $query2 = '';
    //     foreach ($this->CI->db->queries as $key => $hasilquery) { 
    //       $query = explode(" ", $hasilquery);
    //       // $query2 .= $query[0]. ', ';
    //       $kamus = array("select","SELECT");
    //       if (in_array($query[0], $kamus)) {
    //         $query2 .= $query[0]. ', ';
    //       } else {
    //         $query2 .= $hasilquery;
    //       }
    //     }
    //       $query2 = $query2;
    //         $logtext = sprintf("%s\r\n%s\r\n%s\r\n%s\r\n%s\r\n%s\r\n%s",
    //                    "User ID           : ". $this->GetUserId(), 
    //                    "IP Client         : ". $this->getIP(),
    //                    "URL               : ". $this->GetURL(),
    //                    "Controller\Method : ". $this->GetClass(),
    //                    "Tanggal           : ". date('d-m-Y h:i:s'),
    //                    "Statement         : ". $query2,
    //                    // "Info Server         : ". $this->GetDetailServer(),
    //                    "Execution Time    : ". $times[$key]."\r\n------------------------------------------------------------------------------------------"
    //                    );
    //         fwrite($handle, $logtext . "\r\n\r\n");              
 
    //     fclose($handle);
    // }
 

    function GetUserId(){
      $session = '';
      if (isset($this->CI->kauth->getInstance()->getIdentity()->USER_LOGIN_ID)) {
        $session  =   $this->CI->kauth->getInstance()->getIdentity()->USER_LOGIN_ID .' ('.$this->CI->kauth->getInstance()->getIdentity()->USER_NAMA.')';
      }
        return $session;
    }

    function GetDetailServer()
    {
      $indicesServer = array('PHP_SELF', 
                              'argv', 
                              'argc', 
                              'GATEWAY_INTERFACE', 
                              'SERVER_ADDR', 
                              'SERVER_NAME', 
                              'SERVER_SOFTWARE', 
                              'SERVER_PROTOCOL', 
                              'REQUEST_METHOD', 
                              'REQUEST_TIME', 
                              'REQUEST_TIME_FLOAT', 
                              'QUERY_STRING', 
                              'DOCUMENT_ROOT', 
                              'HTTP_ACCEPT', 
                              'HTTP_ACCEPT_CHARSET', 
                              'HTTP_ACCEPT_ENCODING', 
                              'HTTP_ACCEPT_LANGUAGE', 
                              'HTTP_CONNECTION', 
                              'HTTP_HOST', 
                              'HTTP_REFERER', 
                              'HTTP_USER_AGENT', 
                              'HTTPS', 
                              'REMOTE_ADDR', 
                              'REMOTE_HOST', 
                              'REMOTE_PORT', 
                              'REMOTE_USER', 
                              'REDIRECT_REMOTE_USER', 
                              'SCRIPT_FILENAME', 
                              'SERVER_ADMIN', 
                              'SERVER_PORT', 
                              'SERVER_SIGNATURE', 
                              'PATH_TRANSLATED', 
                              'SCRIPT_NAME', 
                              'REQUEST_URI', 
                              'PHP_AUTH_DIGEST', 
                              'PHP_AUTH_USER', 
                              'PHP_AUTH_PW', 
                              'AUTH_TYPE', 
                              'PATH_INFO', 
                              'ORIG_PATH_INFO') ; 

      $html = '' ; 
      foreach ($indicesServer as $arg) { 
          if (isset($_SERVER[$arg])) { 
              $html .= ''.$arg.'=' . $_SERVER[$arg] . '||' ; 
          } 
          else { 
              $html .= ''.$arg.'=-||' ; 
          } 
      } 
      $html .= '' ; 

      return $html;
    } 

    function GetClass(){
        return $this->CI->router->fetch_class()."\\".$this->CI->router->fetch_method();
    }

    function GetURL(){
        $request = strstr( $_SERVER['REQUEST_URI'], '?', true );
        if ( !$request ) {
            $request = $_SERVER['REQUEST_URI'] .'||'. $_SERVER['HTTP_USER_AGENT'] .'||REMOTE_PORT:'. $_SERVER['REMOTE_PORT'];
        }        return $request;
    }

    public function getIP()
    {
        // $ip = $_SERVER['REMOTE_ADDR']?:($_SERVER['HTTP_X_FORWARDED_FOR']?:$_SERVER['HTTP_CLIENT_IP']);
      $ipaddress = '';
      if (isset($_SERVER['HTTP_CLIENT_IP']))
          $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
      else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
          $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
      else if(isset($_SERVER['HTTP_X_FORWARDED']))
          $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
      else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
          $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
      else if(isset($_SERVER['HTTP_FORWARDED']))
          $ipaddress = $_SERVER['HTTP_FORWARDED'];
      else if(isset($_SERVER['REMOTE_ADDR']))
          $ipaddress = $_SERVER['REMOTE_ADDR'];
      else
          $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

 
}
?>