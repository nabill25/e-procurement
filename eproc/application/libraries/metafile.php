<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
 
require_once('getID3-1.9.7/getid3/getid3.php');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Kloader
 *
 * @author user
 */
class metafile  extends getID3 {
    public function __construct() {
        parent::__construct();
    }
}

?>
