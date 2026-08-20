<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
 
require_once 'kloader.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Kcache
 *
 * @author user
 */
class kzendcache {
    static function _init(){
        kloader::load('Zend_Cache');
        $frontendOptions = array('lifetime'=> 86400,//24jam
                                'automatic_serialization' => true 
                                ); 

        $backendOptions = array('cache_dir' => APPPATH.'cache'); 
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions,$backendOptions);
        return $cache;
    }
}

?>
