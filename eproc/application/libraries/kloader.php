<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
 
require_once 'Zend/Loader.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Kloader
 *
 * @author user
 */
class kloader {
    static function load($class = null) {
        ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.BASEPATH.'../application/libraries/');
//        require_once 'Zend/'.$class.'.php';
        Zend_Loader::loadClass($class);
    }
}

?>
