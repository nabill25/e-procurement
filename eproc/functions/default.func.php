<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

include_once("lib/kses/kses.php");
include_once("functions/encrypt.func.php");

$allowedTagAttributes = array('style' => array(),
						   	  'class' => array());

$allowed = array(
                 'a' => array('href' => array(), 
				 			  'title' => array()),
                 'br' => array(),
				 'p' => array(),
				 'b' => array(),
				 'strong' => array(),
				 'u' => array(),
				 'em' => array(),
				 'ul' => $allowedTagAttributes,
				 'ol' => $allowedTagAttributes,
				 'li' => $allowedTagAttributes,
				 'table' => array('border' => array(),
				 				  'width' => array(),
								  'height' => array(),
								  'style' => array(),
								  'class' => array()),
				 'tr' => array(),
				 'td' => $allowedTagAttributes,
				 'div' => $allowedTagAttributes,
				 'img' => array('src' => array(),
				 				'title' => array(),
								'alt' => array())
				);

$allowedForUser = array(
                 'a' => array('href' => array(), 'title' => array()),
                 'br' => array(),
				 'p' => array(),
				 'b' => array(),
				 'strong' => array(),
				 'u' => array(),
				 'em' => array()
				);

$allowedStrict = array('b' => array(), 'strong' => array());
				
	function formatTextToDb($varText)
	{
		$varText = ltrim($varText);
		
		$varText = str_replace("'", "&quote", $varText);
		$varText = str_replace("\\", "&backslash", $varText);
		
		$varText = str_replace("\\", "", $varText);
		$varText = str_replace("'", "''", $varText);
		
		return $varText;
	}
	
	/* fungsi untuk memformat tampilan dari database ke html */
	function formatTextToPage($varText)
	{
		$varText = str_replace("&amp;", "&", $varText);
		$varText = str_replace("&quote", "'", $varText);
		$varText = str_replace("&backslash", "\\", $varText);
		
		//$varText = str_replace("\n", "<br />", $varText);
		
		return $varText;
	}
	
	function dropAllHtml($varText)
	{
		$varResult = $varText;
		
		if (get_magic_quotes_gpc())
		  $varResult = stripslashes($varText);
		
		return kses($varResult, $allowedForUser);
	}

	function httpFilterGet($varname,$inputSource = 'admin')
	{
		global $allowed;
		global $allowedForUser;
		
		$varResult = isset($_GET[$varname]) ? formatTextToDb($_GET[$varname]) : '';
		
		if (get_magic_quotes_gpc())
		  $varResult = stripslashes($varResult);
		
		if($inputSource == 'admin')
			//return kses($varResult, $allowed, false);
			return $varResult;
		else if($inputSource == 'user')
			return kses($varResult, $allowedForUser);
	}
	
	function httpEncryptGet($varname,$inputSource = 'admin')
	{
		global $allowed;
		global $allowedForUser;
		
		$varResult = formatTextToDb($_GET[$varname]);
		
		if (get_magic_quotes_gpc())
		  $varResult = stripslashes($varResult);
		
		if($inputSource == 'admin')
			//return kses($varResult, $allowed, false);
			return mdecrypt($varResult);
		else if($inputSource == 'user')
			return kses($varResult, $allowedForUser);
	}	
	
	function httpFilterPost($varname,$inputSource = 'admin')
	{
		global $allowed;
		global $allowedForUser;
		
		$varResult = isset($_POST[$varname]) ? formatTextToDb($_POST[$varname]) : '';
		
		if (get_magic_quotes_gpc())
		  $varResult = stripslashes($varResult);
		
		if($inputSource == 'admin')
		{	//return kses($varResult, $allowed, false);
			$varResult = str_replace("\'", "''", $varResult);
			return $varResult;
		}
		else if($inputSource == 'user')
		{
			$varResult = str_replace("\'", "''", $varResult);			
			return kses($varResult, $allowed);
		}
	}
	
	function httpFilterRequest($varname,$inputSource = 'admin')
	{
		global $allowed;
		global $allowedForUser;
		
		$varResult = isset($_REQUEST[$varname]) ? formatTextToDb($_REQUEST[$varname]) : null;
		
		if (get_magic_quotes_gpc())
		  $varResult = stripslashes($varResult);
		
		if($inputSource == 'admin')
			//return kses($varResult, $allowed, false);
			return $varResult;
		else if($inputSource == 'user')
			return kses($varResult, $allowedForUser);
	}

	function httpEncryptRequest($varname,$inputSource = 'admin')
	{
		global $allowed;
		global $allowedForUser;
		
		$varResult = formatTextToDb($_REQUEST[$varname]);
		
		if (get_magic_quotes_gpc())
		  $varResult = stripslashes($varResult);
		
		if($inputSource == 'admin')
			//return kses($varResult, $allowed, false);
			return mdecrypt($varResult);
		else if($inputSource == 'user')
			return kses($varResult, $allowedForUser);
	}	
	
	function encrypt($var)
	{
		return mencrypt($var);
	}

	function decrypt($var)
	{
		return mdecrypt($var);
	}	
	
	function httpParamGet($varname){
		return $_GET[$varname];
	}
	
	function httpParamPost($varname){
		return $_POST[$varname];
	}
	
	function httpParamGetPost($varname){
		$retval = $_GET[$varname];
		if(!$retval)
			$retval = $_POST[$varname];		
		return $retval;
	}
	
	function httpParamPostGet($varname){
		$retval = $_POST[$varname];
		if(!$retval)
			$retval = $_GET[$varname];		
		return $retval;
	}
	
	function httpParamRequest($varname) {
		$retval = $_REQUEST[$varname];
		return $retval;
	}
	
	function setcookielive($name, $value, $expire, $path='', $domain, $secure=false, $httponly=false) {
		//set a cookie as usual, but ALSO add it to $_COOKIE so the current page load has access
		$_COOKIE[$name] = $value;
		return setcookie($name,$value,$expire,$path,$domain,$secure,$httponly);
	}
	
	function integerToRoman($integer)
{
 // Convert the integer into an integer (just to make sure)
 $integer = intval($integer);
 $result = '';
 
 // Create a lookup array that contains all of the Roman numerals.
 $lookup = array('M' => 1000,
 'CM' => 900,
 'D' => 500,
 'CD' => 400,
 'C' => 100,
 'XC' => 90,
 'L' => 50,
 'XL' => 40,
 'X' => 10,
 'IX' => 9,
 'V' => 5,
 'IV' => 4,
 'I' => 1);
 
 foreach($lookup as $roman => $value){
  // Determine the number of matches
  $matches = intval($integer/$value);
 
  // Add the same number of characters to the string
  $result .= str_repeat($roman,$matches);
 
  // Set the integer to be the remainder of the integer and the value
  $integer = $integer % $value;
 }
 
 // The Roman numeral should be built, return it
 return $result;
}

function getBrowser() 
{ 
    $u_agent = $_SERVER['HTTP_USER_AGENT']; 
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }
    
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Internet Explorer'; 
        $ub = "MSIE"; 
    } 
    elseif(preg_match('/Firefox/i',$u_agent)) 
    { 
        $bname = 'Mozilla Firefox'; 
        $ub = "Firefox"; 
    } 
    elseif(preg_match('/Chrome/i',$u_agent)) 
    { 
        $bname = 'Google Chrome'; 
        $ub = "Chrome"; 
    } 
    elseif(preg_match('/Safari/i',$u_agent)) 
    { 
        $bname = 'Apple Safari'; 
        $ub = "Safari"; 
    } 
    elseif(preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Opera'; 
        $ub = "Opera"; 
    } 
    elseif(preg_match('/Netscape/i',$u_agent)) 
    { 
        $bname = 'Netscape'; 
        $ub = "Netscape"; 
    } 
    
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
    
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }
    
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
    
    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
} 

?>