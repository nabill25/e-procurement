<?php 
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

class AES
{
	private $iv = 'EPROC19'; #Same as in JAVA
	private $key = '321EPROC19'; #Same as in JAVA


	function __construct()
	{
	}

	function encrypt($str) {
	
	  $str_md5 = md5($str);
	  $str = $this->pkcs5_pad($str);  
	  $iv = $this->iv;

	  $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

	  mcrypt_generic_init($td, $this->key, $iv);
	  $encrypted = mcrypt_generic($td, $str);

	  mcrypt_generic_deinit($td);
	  mcrypt_module_close($td);
	  
	  $encrypted = bin2hex($encrypted);
	  $value = substr($str_md5, 0, 30).$encrypted.substr($str_md5, 30, 2);
	  return $value;
	}

	function decrypt($code) {
	  
	  $code = substr($code, 30, (strlen($code)-32));
	  $code = $this->hex2bin($code);
	  $iv = $this->iv;

	  $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

	  mcrypt_generic_init($td, $this->key, $iv);
	  $decrypted = mdecrypt_generic($td, $code);

	  mcrypt_generic_deinit($td);
	  mcrypt_module_close($td);
	  
	  return $this->pkcs5_unpad(utf8_encode(trim($decrypted)));
	  
	}

	protected function hex2bin($hexdata) {
	  $bindata = '';

	  for ($i = 0; $i < strlen($hexdata); $i += 2) {
			$bindata .= chr(hexdec(substr($hexdata, $i, 2)));
	  }

	  return $bindata;
	}
	
	function pkcs5_pad($text) {
    $blocksize = 16;
    $pad = $blocksize - (strlen($text) % $blocksize);
    return $text . str_repeat(chr($pad), $pad);
	}

	function pkcs5_unpad($text) {
    $pad = ord($text{strlen($text)-1});
    if ($pad > strlen($text))
        return false;

    if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)
        return false;

    return substr($text, 0, -1 * $pad);
	}

}

?>