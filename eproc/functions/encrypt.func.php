<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

function encryptIkn($string='')
{
	// Reference : https://www.w3docs.com/snippets/php/how-to-encrypt-and-decrypt-a-string-in-php.html
	$simple_string = $string;
	$ciphering = "AES-128-CTR";
	$iv_length = openssl_cipher_iv_length($ciphering);
	$options = 0;
	$encryption_iv = '1234567891011121';
	$encryption_key = "eProcBismiLL4h4II1";
	$encryption = openssl_encrypt($simple_string, $ciphering, $encryption_key, $options, $encryption_iv);

	return $encryption;
}

function decryptIkn($encryption='')
{
	$ciphering = "AES-128-CTR";
	$iv_length = openssl_cipher_iv_length($ciphering);
	$options = 0;
	$decryption_iv = '1234567891011121';
	$decryption_key = "eProcBismiLL4h4II1";
	$decryption = openssl_decrypt($encryption, $ciphering, $decryption_key, $options, $decryption_iv);

	return $decryption;
}

function mencrypt($input,$key="bismillah"){
$key = substr(md5($key),0,24);
$td = mcrypt_module_open('tripledes', '', 'ecb', '');
$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size ($td), MCRYPT_RAND);
mcrypt_generic_init($td, $key, $iv);
$encrypted_data = mcrypt_generic($td, $input);
mcrypt_generic_deinit($td);
mcrypt_module_close($td);
return trim(chop(url_base64_encode($encrypted_data)));
}
 
function mdecrypt($input,$key="bismillah"){

if($input == "") return "";

$input = trim(chop(url_base64_decode($input)));
$td = mcrypt_module_open('tripledes', '', 'ecb', '');
$key = substr(md5($key),0,24);
$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size ($td), MCRYPT_RAND);
mcrypt_generic_init ($td, $key, $iv);
$decrypted_data = mdecrypt_generic ($td, $input);
mcrypt_generic_deinit ($td);
mcrypt_module_close ($td);
return trim(chop($decrypted_data));
}
 
function url_base64_encode($str){
return strtr(base64_encode($str),
array(
'+' => '999999999x99',
'=' => '888888888x88',
'/' => '777777777x77'
)
);
}
 
function url_base64_decode($str){
return base64_decode(strtr($str,
array(
'999999999x99' => '+',
'888888888x88' => '=',
'777777777x77' => '/'
)
));
}
?>